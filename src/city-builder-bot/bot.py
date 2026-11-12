# Telegram: @Abolfazl_rwm
import logging
import sqlite3
import re
import os
import random
from datetime import datetime, timedelta
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    CallbackQueryHandler,
    MessageHandler,
    ContextTypes,
    filters,
)

# ---------------------------------------------------------
# Configurations & Logging
# ---------------------------------------------------------
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)
logger = logging.getLogger(__name__)

# Load and validate token from environment variable
BOT_TOKEN = os.environ.get("BOT_TOKEN", "").strip()
if not BOT_TOKEN or BOT_TOKEN == "YOUR_TELEGRAM_BOT_TOKEN_HERE":
    raise ValueError(
        "CRITICAL ERROR: BOT_TOKEN is not configured or contains placeholder value! "
        "Please specify a valid Telegram Bot Token via the 'BOT_TOKEN' environment variable."
    )

def escape_markdown(text: str) -> str:
    """Escapes Markdown special characters (*, _, `, [) to prevent Telegram parsing errors."""
    if not text:
        return ""
    text_str = str(text)
    for char in ["*", "_", "`", "["]:
        text_str = text_str.replace(char, f"\\{char}")
    return text_str

# ایدی عددی ادمین 
OWNER_ID = int(os.environ.get("OWNER_ID", "0") or 0)  # آیدی عددی مالک (اولین اجرا خودش admin می‌شود)

# Dictionary/Object class to hold dynamic state with built-in stale validation (5 minutes)
class SaferStateDict(dict):
    def __setitem__(self, key, value):
        if isinstance(value, dict) and "timestamp" not in value:
            value["timestamp"] = datetime.now()
        super().__setitem__(key, value)

    def _check_stale(self, key):
        if super().__contains__(key):
            val = super().__getitem__(key)
            if isinstance(val, dict):
                ts = val.get("timestamp")
                if ts and datetime.now() - ts > timedelta(minutes=5):
                    super().pop(key, None)
                    return True
        return False

    def __contains__(self, key):
        if self._check_stale(key):
            return False
        return super().__contains__(key)

    def __getitem__(self, key):
        self._check_stale(key)
        return super().__getitem__(key)

    def get(self, key, default=None):
        if self._check_stale(key):
            return default
        return super().get(key, default)

USER_STATES = SaferStateDict()

# ---------------------------------------------------------
# Database Initialization & Helpers
# ---------------------------------------------------------
DB_PATH = os.environ.get("DATABASE_PATH", "city_builder.db")
DB_FILE = os.path.abspath(DB_PATH)

# Save original sqlite3.connect to avoid recursive calls
_real_connect = sqlite3.connect

def safer_sqlite_connect(database, *args, **kwargs):
    """Override connecting function with enhanced safety settings."""
    if "timeout" not in kwargs:
        kwargs["timeout"] = 30.0
    conn = _real_connect(database, *args, **kwargs)
    if database == DB_FILE:
        try:
            conn.execute("PRAGMA journal_mode=WAL;")
            conn.execute("PRAGMA foreign_keys=ON;")
        except Exception as e:
            logger.warning(f"Failed to enable WAL/foreign keys on connection: {e}")
    return conn

# Patch sqlite3.connect globally for everything in this module
sqlite3.connect = safer_sqlite_connect

def init_db():
    """Initializes SQLite database tables for players, buildings, logs, admins, settings and trades."""
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        
        # Players table with status column
        # status can be: 'pending_name', 'pending_approval', 'approved', 'rejected'
        c.execute("""
            CREATE TABLE IF NOT EXISTS players (
                user_id INTEGER PRIMARY KEY,
                username TEXT,
                city_name TEXT,
                gold INTEGER DEFAULT 1000,
                wood INTEGER DEFAULT 500,
                stone INTEGER DEFAULT 300,
                food INTEGER DEFAULT 200,
                population REAL DEFAULT 10.0,
                happiness REAL DEFAULT 100.0,
                storage_capacity INTEGER DEFAULT 10000,
                last_collect TEXT,
                last_daily TEXT,
                status TEXT DEFAULT 'pending_name',
                created_at TEXT
            )
        """)
        
        # Gracefully add potentially missing columns for older DB instances with correct types
        for col, datatype, default_val in [
            ("status", "TEXT", "'pending_name'"),
            ("population", "REAL", "10.0"),
            ("happiness", "REAL", "100.0"),
            ("storage_capacity", "INTEGER", "10000"),
            ("last_collect", "TEXT", "NULL"),
            ("last_daily", "TEXT", "NULL"),
            ("created_at", "TEXT", "NULL"),
            ("active_event", "TEXT", "NULL"),
            ("active_event_expires", "TEXT", "NULL"),
            ("reputation", "REAL", "50.0")
        ]:
            try:
                c.execute(f"ALTER TABLE players ADD COLUMN {col} {datatype} DEFAULT {default_val}")
            except sqlite3.OperationalError as e:
                if "duplicate column name" in str(e).lower():
                    pass # Column already exists, safe to ignore
                else:
                    logger.error(f"Migration error: Failed to add column '{col}' to players: {e}")
                    raise
            
        # Buildings table
        c.execute("""
            CREATE TABLE IF NOT EXISTS buildings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                building_type TEXT,
                level INTEGER DEFAULT 1,
                created_at TEXT,
                FOREIGN KEY(user_id) REFERENCES players(user_id)
            )
        """)
        
        # Admins table
        c.execute("""
            CREATE TABLE IF NOT EXISTS admins (
                user_id INTEGER PRIMARY KEY,
                role TEXT
            )
        """)
        c.execute("INSERT OR IGNORE INTO admins (user_id, role) VALUES (?, 'owner')", (OWNER_ID,))
        
        # Settings table for dynamic configuration (e.g. forced join channel)
        c.execute("""
            CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT
            )
        """)
        c.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('required_channel', '@Abolfazl_rwm')")
        
        # Market Trades table
        c.execute("""
            CREATE TABLE IF NOT EXISTS market_trades (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                seller_id INTEGER,
                sell_resource TEXT,
                sell_amount INTEGER,
                buy_resource TEXT,
                buy_amount INTEGER,
                status TEXT DEFAULT 'open', -- 'open', 'completed', 'cancelled'
                created_at TEXT,
                FOREIGN KEY(seller_id) REFERENCES players(user_id)
            )
        """)
        
        # Game Logs table
        c.execute("""
            CREATE TABLE IF NOT EXISTS game_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_id INTEGER,
                log_type TEXT,
                message TEXT,
                created_at TEXT,
                FOREIGN KEY(player_id) REFERENCES players(user_id)
            )
        """)
        
        # Daily Missions table
        c.execute("""
            CREATE TABLE IF NOT EXISTS daily_missions (
                player_id INTEGER,
                mission_date TEXT,
                mission_type TEXT,
                progress INTEGER DEFAULT 0,
                target INTEGER,
                reward_res TEXT,
                reward_amt INTEGER,
                claimed INTEGER DEFAULT 0,
                PRIMARY KEY(player_id, mission_date, mission_type)
            )
        """)

        # Technologies table
        c.execute("""
            CREATE TABLE IF NOT EXISTS technologies (
                player_id INTEGER,
                tech_id TEXT,
                level INTEGER DEFAULT 0,
                PRIMARY KEY(player_id, tech_id),
                FOREIGN KEY(player_id) REFERENCES players(user_id)
            )
        """)

        # Diplomacy table
        c.execute("""
            CREATE TABLE IF NOT EXISTS diplomacy (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sender_id INTEGER,
                receiver_id INTEGER,
                type TEXT, -- 'friendship', 'trade_agreement'
                status TEXT DEFAULT 'pending', -- 'pending', 'accepted', 'rejected'
                created_at TEXT,
                FOREIGN KEY(sender_id) REFERENCES players(user_id),
                FOREIGN KEY(receiver_id) REFERENCES players(user_id)
            )
        """)
        
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error during init_db: {e}")
        raise
    finally:
        conn.close()

# ---------------------------------------------------------
# Advanced Strategic Systems Config & Helpers
# ---------------------------------------------------------
TECH_INFO = {
    "adv_farming": {
        "name": "🌾 کشاورزی پیشرفته (Advanced Farming)",
        "desc": "افزایش ۱۰٪ تولید غذا به ازای هر سطح.",
        "bonus_per_level": 0.10
    },
    "ind_efficiency": {
        "name": "⚙️ راندمان صنعتی (Industrial Efficiency)",
        "desc": "افزایش ۸٪ تولید چوب و سنگ به ازای هر سطح.",
        "bonus_per_level": 0.08
    },
    "market_opt": {
        "name": "📈 بهینه‌سازی بازار (Market Optimization)",
        "desc": "کاهش ۵٪ هزینه نگهداری ابنیه به ازای هر سطح.",
        "bonus_per_level": 0.05
    },
    "storage_eng": {
        "name": "📐 مهندسی مخازن (Storage Engineering)",
        "desc": "افزایش ۱۵٪ ظرفیت کل انبارها به ازای هر سطح.",
        "bonus_per_level": 0.15
    },
    "construction_methods": {
        "name": "🏗️ شیوه‌های مدرن ساخت (Construction Methods)",
        "desc": "کاهش ۵٪ هزینه طلا/چوب/سنگ ساخت و ارتقا ابنیه به ازای هر سطح.",
        "bonus_per_level": 0.05
    },
    "education": {
        "name": "📚 آموزش عمومی (Education)",
        "desc": "افزایش ۱۰٪ سرعت رشد جمعیت و ۵٪ اثربخشی افزایش رضایت به ازای هر سطح.",
        "bonus_per_level": 0.10
    },
    "defense_planning": {
        "name": "🛡️ طرح پدافند غیرعامل (Defense Planning)",
        "desc": "طراحی پناهگاه‌ها و تمهیدات نظامی جهت پدافند آینده شهر.",
        "bonus_per_level": 0.10
    }
}

EVENTS_INFO = {
    "economic_boom": {
        "name": "🚀 رونق اقتصادی (Economic Boom)",
        "desc": "رونق تجاری فوق‌العاده! تولید طلا به مدت ۲۴ ساعت ۲۰٪ افزایش می‌یابد.",
        "modifier": {"gold": 1.20},
        "is_positive": True
    },
    "good_harvest": {
        "name": "🌾 برداشت محصول بی‌سابقه (Good Harvest)",
        "desc": "بارندگی‌های مناسب و آب و هوای عالی! تولید غذا به مدت ۲۴ ساعت ۲۵٪ افزایش می‌یابد.",
        "modifier": {"food": 1.25},
        "is_positive": True
    },
    "pop_growth": {
        "name": "📈 استقبال مهاجران جدید (Migration Wave)",
        "desc": "امنیت و اعتبار شهر مهاجران جدید را جذب کرده! سرعت رشد جمعیت ۱۵٪ افزایش یافته و ۱۰٪ رضایت عمومی فوری افزوده شد.",
        "modifier": {"pop_growth": 1.15},
        "is_positive": True
    },
    "resource_discovery": {
        "name": "🪵 کشف رگه غنی (Rich Resource Vein)",
        "desc": "کاشفان شهرداری رگه‌های غنی چوب و سنگ یافتند! تولید چوب و سنگ به مدت ۲۴ ساعت ۲۰٪ افزایش می‌یابد.",
        "modifier": {"wood": 1.20, "stone": 1.20},
        "is_positive": True
    },
    "food_shortage": {
        "name": "🐛 حمله ملخ‌ها و خشکسالی (Crop Pest & Drought)",
        "desc": "خشکسالی و آفت! تولید غذا به مدت ۲۴ ساعت ۱۵٪ کاهش و رضایت عمومی ۱۰٪ کاهش می‌یابد.",
        "modifier": {"food": 0.85},
        "is_positive": False
    },
    "economic_crisis": {
        "name": "📉 بحران بازارهای مالی (Financial Crisis)",
        "desc": "تورم شدید در کشورهای همسایه! نرخ خالص تولید طلا به مدت ۲۴ ساعت ۲۰٪ کاهش می‌یابد.",
        "modifier": {"gold": 0.80},
        "is_positive": False
    },
    "resource_accident": {
        "name": "⚠️ ریزش کارگاه‌ها (Workshop Collapse)",
        "desc": "ریزش دیواره معدن و خطای کارگران در چوب‌بری! تولید چوب و سنگ به مدت ۲۴ ساعت ۲۰٪ کاهش می‌یابد.",
        "modifier": {"wood": 0.80, "stone": 0.80},
        "is_positive": False
    },
    "public_unrest": {
        "name": "🔥 اعتصابات و ناآرامی‌های مدنی (Public Unrest)",
        "desc": "نارضایتی بابت دستمزدها! ۱۵٪ از رضایت عمومی شهروندان به طور ناگهانی کاسته شد.",
        "modifier": {},
        "is_positive": False
    }
}

def get_player_technologies(player_id: int, cache=None):
    if cache is not None:
        return cache
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("SELECT tech_id, level FROM technologies WHERE player_id = ?", (player_id,))
        rows = c.fetchall()
    except Exception as e:
        logger.error(f"Error in get_player_technologies for player {player_id}: {e}")
        rows = []
    finally:
        conn.close()
    tech_dict = {
        "adv_farming": 0,
        "ind_efficiency": 0,
        "market_opt": 0,
        "storage_eng": 0,
        "construction_methods": 0,
        "education": 0,
        "defense_planning": 0
    }
    for row in rows:
        tech_dict[row[0]] = row[1]
    return tech_dict

def upgrade_technology(player_id: int, tech_id: str):
    techs = get_player_technologies(player_id)
    lvl = techs.get(tech_id, 0)
    if lvl >= 10:
        return False, "این فناوری قبلاً به حداکثر سطح (۱۰) رسیده است."
        
    cost = get_tech_upgrade_cost(tech_id, lvl)
    if not cost:
        return False, "این فناوری قبلاً به حداکثر سطح رسیده است."
        
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    try:
        c.execute("BEGIN IMMEDIATE")
        c.execute("SELECT gold, wood, stone FROM players WHERE user_id = ?", (player_id,))
        row = c.fetchone()
        if not row:
            conn.rollback()
            return False, "کاربر یافت نشد."
        g_bal, w_bal, s_bal = row
        if g_bal < cost["gold"] or w_bal < cost["wood"] or s_bal < cost["stone"]:
            conn.rollback()
            return False, f"منابع کافی برای ارتقا ندارید! نیاز به: {cost['gold']} طلا، {cost['wood']} چوب، {cost['stone']} سنگ"
            
        # Deduct resources
        c.execute(
            "UPDATE players SET gold = gold - ?, wood = wood - ?, stone = stone - ? WHERE user_id = ?",
            (cost["gold"], cost["wood"], cost["stone"], player_id)
        )
        # Update technology
        c.execute(
            "INSERT OR REPLACE INTO technologies (player_id, tech_id, level) VALUES (?, ?, ?)",
            (player_id, tech_id, lvl + 1)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error upgrading technology: {e}")
        return False, "خطای دیتابیس در ارتقای فناوری."
    finally:
        conn.close()
        
    # Clamp player resources after upgrade
    clamp_player_resources_to_capacity(player_id)
    
    tech_name = TECH_INFO.get(tech_id, {}).get("name", tech_id)
    add_game_log(player_id, "research", f"🔬 ارتقای فناوری «{tech_name}» به سطح {lvl+1}")
    
    return True, f"فناوری «{tech_name}» با موفقیت به سطح {lvl+1} ارتقا یافت."

def get_tech_upgrade_cost(tech_id: str, current_level: int):
    if current_level >= 10:
        return None
    mult = 2.0 ** current_level
    return {
        "gold": int(1000 * mult),
        "wood": int(500 * mult),
        "stone": int(500 * mult)
    }

def update_reputation(user_id: int, delta: float):
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("UPDATE players SET reputation = MIN(100.0, MAX(0.0, reputation + ?)) WHERE user_id = ?", (delta, user_id))
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error in update_reputation for user {user_id}: {e}")
    finally:
        conn.close()

def check_and_trigger_event(user_id: int):
    player = get_player(user_id)
    if not player or player["status"] != 'approved':
        return
        
    now = datetime.now()
    active_evt = player.get("active_event")
    expires_str = player.get("active_event_expires")
    
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        # Check if event has expired
        if active_evt and expires_str:
            try:
                expires = datetime.fromisoformat(expires_str)
            except Exception:
                expires = now
            if now >= expires:
                # Event expired! Clear it
                c.execute("UPDATE players SET active_event = NULL, active_event_expires = NULL WHERE user_id = ?", (user_id,))
                conn.commit()
                add_game_log(user_id, "event_expired", f"⌛ رویداد «{EVENTS_INFO.get(active_evt, {}).get('name', active_evt)}» به پایان رسید و شرایط شهر به حالت عادی بازگشت.")
                active_evt = None
                
        # If no event, 15% chance to trigger a new one
        if not active_evt:
            if random.random() < 0.15:
                evt_keys = list(EVENTS_INFO.keys())
                new_evt = random.choice(evt_keys)
                duration = timedelta(hours=24)
                expires = now + duration
                
                c.execute("UPDATE players SET active_event = ?, active_event_expires = ? WHERE user_id = ?", (new_evt, expires.isoformat(), user_id))
                conn.commit()
                
                evt_details = EVENTS_INFO[new_evt]
                add_game_log(user_id, "event_start", f"🌍 رویداد جدید: {evt_details['name']} - {evt_details['desc']}")
                
                # Apply instant effects if any
                if new_evt == "pop_growth":
                    c.execute("UPDATE players SET happiness = MIN(100.0, happiness + 10.0) WHERE user_id = ?", (user_id,))
                    conn.commit()
                    update_reputation(user_id, 2.0)
                elif new_evt == "public_unrest":
                    c.execute("UPDATE players SET happiness = MAX(0.0, happiness - 15.0) WHERE user_id = ?", (user_id,))
                    conn.commit()
                    update_reputation(user_id, -2.0)
                elif evt_details["is_positive"]:
                    update_reputation(user_id, 1.0)
                else:
                    update_reputation(user_id, -1.0)
    except Exception as e:
        conn.rollback()
        logger.error(f"Error in check_and_trigger_event for user {user_id}: {e}")
    finally:
        conn.close()

def get_diplomacy_relations(user_id: int):
    conn = sqlite3.connect(DB_FILE)
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("""
        SELECT d.*, p1.city_name as sender_city, p2.city_name as receiver_city, p1.username as sender_name, p2.username as receiver_name
        FROM diplomacy d
        JOIN players p1 ON d.sender_id = p1.user_id
        JOIN players p2 ON d.receiver_id = p2.user_id
        WHERE (d.sender_id = ? OR d.receiver_id = ?)
    """, (user_id, user_id))
    rows = c.fetchall()
    conn.close()
    return [dict(r) for r in rows]

def send_diplomacy_proposal(sender_id: int, receiver_id: int, p_type: str):
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("""
        SELECT 1 FROM diplomacy 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND type = ? AND status IN ('pending', 'accepted')
    """, (sender_id, receiver_id, receiver_id, sender_id, p_type))
    row = c.fetchone()
    if row:
        conn.close()
        return False, "توافق یا پیمان فعالی از این نوع قبلاً بین شما برقرار شده یا معلق است."
        
    now_str = datetime.now().isoformat()
    c.execute("""
        INSERT INTO diplomacy (sender_id, receiver_id, type, status, created_at)
        VALUES (?, ?, ?, 'pending', ?)
    """, (sender_id, receiver_id, p_type, now_str))
    conn.commit()
    conn.close()
    return True, "درخواست دیپلماتیک با موفقیت ارسال شد."

def accept_diplomacy_proposal(relation_id: int, receiver_id: int):
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("SELECT sender_id, type FROM diplomacy WHERE id = ? AND receiver_id = ? AND status = 'pending'", (relation_id, receiver_id))
    row = c.fetchone()
    if not row:
        conn.close()
        return False, None
    sender_id, p_type = row
    c.execute("UPDATE diplomacy SET status = 'accepted' WHERE id = ?", (relation_id,))
    conn.commit()
    conn.close()
    
    update_reputation(sender_id, 5.0)
    update_reputation(receiver_id, 5.0)
    return True, sender_id

def reject_diplomacy_proposal(relation_id: int, receiver_id: int):
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("UPDATE diplomacy SET status = 'rejected' WHERE id = ? AND receiver_id = ? AND status = 'pending'", (relation_id, receiver_id))
    rowcount = c.rowcount
    conn.commit()
    conn.close()
    return rowcount > 0

def has_trade_agreement(user_a: int, user_b: int) -> bool:
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("""
        SELECT 1 FROM diplomacy 
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND type = 'trade_agreement' AND status = 'accepted'
    """, (user_a, user_b, user_b, user_a))
    row = c.fetchone()
    conn.close()
    return row is not None

# ---------------------------------------------------------
# Helper Functions for City Builder Game Systems
# ---------------------------------------------------------
def get_or_create_daily_missions(player_id: int):
    conn = sqlite3.connect(DB_FILE)
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    today_str = datetime.now().date().isoformat()
    
    c.execute("SELECT * FROM daily_missions WHERE player_id = ? AND mission_date = ?", (player_id, today_str))
    rows = c.fetchall()
    
    if not rows:
        # Dynamic daily mission generation from a pool of 4 examples
        pool = [
            ("collect", 1, "wood", 300),
            ("build_upgrade", 1, "gold", 400),
            ("trade", 1, "food", 350),
            ("transfer", 1, "stone", 250)
        ]
        # Seed by player_id and date for consistent daily generation per player
        state = random.getstate()
        try:
            random.seed(f"{player_id}_{today_str}")
            missions = random.sample(pool, 3)
        except Exception:
            missions = [
                ("collect", 1, "wood", 300),
                ("build_upgrade", 1, "gold", 400),
                ("trade", 1, "food", 350)
            ]
        finally:
            random.setstate(state)
            
        for m_type, target, r_res, r_amt in missions:
            c.execute("""
                INSERT OR IGNORE INTO daily_missions (player_id, mission_date, mission_type, progress, target, reward_res, reward_amt, claimed)
                VALUES (?, ?, ?, 0, ?, ?, ?, 0)
            """, (player_id, today_str, m_type, target, r_res, r_amt))
        conn.commit()
        c.execute("SELECT * FROM daily_missions WHERE player_id = ? AND mission_date = ?", (player_id, today_str))
        rows = c.fetchall()
        
    conn.close()
    return [dict(r) for r in rows]

def update_mission_progress(player_id: int, mission_type: str, increment: int = 1):
    # Ensure daily missions are initialized for today
    get_or_create_daily_missions(player_id)
    
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    today_str = datetime.now().date().isoformat()
    c.execute("""
        UPDATE daily_missions 
        SET progress = MIN(target, progress + ?) 
        WHERE player_id = ? AND mission_date = ? AND mission_type = ? AND claimed = 0
    """, (increment, player_id, today_str, mission_type))
    conn.commit()
    conn.close()

def get_happiness_modifier(happiness: float) -> float:
    """Returns production rate modifier based on city happiness levels."""
    hap = float(happiness)
    if hap >= 90.0:
        return 1.20
    elif hap >= 85.0:
        return 1.15
    elif hap <= 25.0:
        return 0.60
    elif hap <= 35.0:
        return 0.70
    return 1.0

def deduct_player_resources_atomic(user_id: int, gold: int, wood: int, stone: int, food: int) -> bool:
    """Atomically verifies and deducts resources from a player inside an IMMEDIATE transaction.
    Returns True if deduction succeeded, False if resources were insufficient.
    """
    if gold < 0 or wood < 0 or stone < 0 or food < 0:
        return False
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("BEGIN IMMEDIATE")
        c.execute("SELECT gold, wood, stone, food FROM players WHERE user_id = ?", (user_id,))
        row = c.fetchone()
        if not row:
            conn.rollback()
            return False
        g_bal, w_bal, s_bal, f_bal = row
        if g_bal < gold or w_bal < wood or s_bal < stone or f_bal < food:
            conn.rollback()
            return False
        
        c.execute(
            """UPDATE players 
               SET gold = gold - ?, wood = wood - ?, stone = stone - ?, food = food - ? 
               WHERE user_id = ?""",
            (gold, wood, stone, food, user_id)
        )
        conn.commit()
        return True
    except Exception as e:
        conn.rollback()
        logger.error(f"Error in deduct_player_resources_atomic for user {user_id}: {e}")
        return False
    finally:
        conn.close()

def is_valid_city_name(city_name: str) -> bool:
    """Checks if the city name has 3-20 characters, containing only letters, numbers, and spaces."""
    if len(city_name) < 3 or len(city_name) > 20:
        return False
    # English and Persian alphanumeric characters + spaces
    pattern = re.compile(r'^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\u0FB5-\u0FD3\uFE70-\uFEFC]+$')
    return bool(pattern.match(city_name))

def has_sufficient_resource(player_id: int, resource_type: str, amount: int) -> bool:
    """Checks if player has at least amount of resource_type."""
    player = get_player(player_id)
    if not player:
        return False
    if resource_type not in ["gold", "wood", "stone", "food"]:
        return False
    try:
        return int(player[resource_type]) >= amount
    except (ValueError, TypeError):
        return False

def get_player_by_username_or_id(identifier: str):
    """Resolves player using ID or username (without @)."""
    identifier = identifier.strip().replace("@", "")
    conn = sqlite3.connect(DB_FILE)
    try:
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        if identifier.isdigit():
            c.execute("SELECT * FROM players WHERE user_id = ?", (int(identifier),))
        else:
            c.execute("SELECT * FROM players WHERE LOWER(username) = LOWER(?)", (identifier,))
        row = c.fetchone()
        return dict(row) if row else None
    except Exception as e:
        logger.error(f"Error resolving player: {e}")
        return None
    finally:
        conn.close()

def add_game_log(player_id: int, log_type: str, message: str):
    """Logs a player action or event in the game_logs table."""
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        now_str = datetime.now().isoformat()
        c.execute(
            "INSERT INTO game_logs (player_id, log_type, message, created_at) VALUES (?, ?, ?, ?)",
            (player_id, log_type, message, now_str)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error writing game log for user {player_id}: {e}")
    finally:
        conn.close()

def get_storage_capacity_for_player(player_id: int, buildings_cache=None, tech_cache=None) -> int:
    """Returns maximum storage capacity based on town_hall, warehouse levels, and storage engineering technology."""
    buildings = get_buildings(player_id, cache=buildings_cache)
    town_halls = [b for b in buildings if b["building_type"] == "town_hall"]
    warehouses = [b for b in buildings if b["building_type"] == "warehouse"]
    
    th_level = sum(b["level"] for b in town_halls) if town_halls else 1
    wh_level = sum(b["level"] for b in warehouses) if warehouses else 0
    
    base_cap = 10000 + (th_level - 1) * 5000 + wh_level * 10000
    
    # Apply storage_eng tech bonus (+15% per level)
    techs = get_player_technologies(player_id, cache=tech_cache)
    tech_bonus = techs.get("storage_eng", 0) * 0.15
    return int(base_cap * (1.0 + tech_bonus))

def get_max_population_for_player(player_id: int, buildings_cache=None) -> int:
    """Returns max population capacity based on town_hall levels."""
    buildings = get_buildings(player_id, cache=buildings_cache)
    town_halls = [b for b in buildings if b["building_type"] == "town_hall"]
    th_level = sum(b["level"] for b in town_halls) if town_halls else 1
    return 20 * th_level

def clamp_player_resources_to_capacity(player_id: int, player_cache=None, buildings_cache=None, tech_cache=None):
    """Caps wood, stone, and food resources to the player's storage capacity."""
    player = get_player(player_id, cache=player_cache)
    if not player:
        return
    capacity = get_storage_capacity_for_player(player_id, buildings_cache=buildings_cache, tech_cache=tech_cache)
    
    wood_overflow = max(0, player["wood"] - capacity)
    stone_overflow = max(0, player["stone"] - capacity)
    food_overflow = max(0, player["food"] - capacity)
    
    wood = max(0, min(int(player["wood"]), capacity))
    stone = max(0, min(int(player["stone"]), capacity))
    food = max(0, min(int(player["food"]), capacity))
    gold = max(0, int(player["gold"]))
    
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute(
            "UPDATE players SET wood = ?, stone = ?, food = ?, gold = ?, storage_capacity = ? WHERE user_id = ?",
            (wood, stone, food, gold, capacity, player_id)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error in clamp_player_resources_to_capacity for user {player_id}: {e}")
    finally:
        conn.close()
    
    # Log any wasted resources due to capacity limits
    wasted = []
    if wood_overflow > 0:
        wasted.append(f"🪵 چوب ({wood_overflow} واحد)")
    if stone_overflow > 0:
        wasted.append(f"🪨 سنگ ({stone_overflow} unit)")
    if food_overflow > 0:
        wasted.append(f"🌾 غذا ({food_overflow} واحد)")
        
    if wasted:
        wasted_str = " و ".join(wasted)
        add_game_log(player_id, "storage_waste", f"⚠️ اتلاف منابع! انبار شما گنجایش کافی نداشت و {wasted_str} از دست رفت. برای جلوگیری از هدررفت، انبار خود را ارتقا دهید!")

def calculate_construction_cost(building_type: str, count: int, user_id: int = None):
    """Calculates cost of building a new instance of a building type based on the number of already owned instances and construction methods technology."""
    config = BUILDING_INFO.get(building_type)
    if not config:
        return {"gold": 99999, "wood": 99999, "stone": 99999}
    base = config["base_cost"]
    # Scale cost by 1.5 for each existing building of this type
    mult = 1.5 ** count
    
    cost_reduction = 0.0
    if user_id:
        techs = get_player_technologies(user_id)
        cost_reduction = techs.get("construction_methods", 0) * 0.05 # 5% per level
        
    return {
        "gold": int(base["gold"] * mult * (1.0 - cost_reduction)),
        "wood": int(base["wood"] * mult * (1.0 - cost_reduction)),
        "stone": int(base["stone"] * mult * (1.0 - cost_reduction))
    }

# ---------------------------------------------------------
# Admin & Settings Helpers
# ---------------------------------------------------------
def is_admin(user_id: int) -> bool:
    if user_id == OWNER_ID:
        return True
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("SELECT 1 FROM admins WHERE user_id = ?", (user_id,))
        row = c.fetchone()
        return row is not None
    except Exception as e:
        logger.error(f"Error checking is_admin for user {user_id}: {e}")
        return False
    finally:
        conn.close()

def is_owner(user_id: int) -> bool:
    return user_id == OWNER_ID

def get_setting(key: str, default_val: str = "") -> str:
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("SELECT value FROM settings WHERE key = ?", (key,))
        row = c.fetchone()
        return row[0] if row else default_val
    except Exception as e:
        logger.error(f"Error getting setting {key}: {e}")
        return default_val
    finally:
        conn.close()

def set_setting(key: str, value: str):
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)", (key, value))
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error setting {key} to {value}: {e}")
    finally:
        conn.close()

# ---------------------------------------------------------
# Player Helpers
# ---------------------------------------------------------
def get_player(user_id: int, cache=None):
    if cache is not None:
        return cache
    conn = sqlite3.connect(DB_FILE)
    try:
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM players WHERE user_id = ?", (user_id,))
        row = c.fetchone()
        return dict(row) if row else None
    except Exception as e:
        logger.error(f"Error retrieving player details for {user_id}: {e}")
        return None
    finally:
        conn.close()

def register_player(user_id: int, username: str, city_name: str, status: str = 'pending_name'):
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        now_str = datetime.now().isoformat()
        c.execute(
            """INSERT OR IGNORE INTO players (user_id, username, city_name, last_collect, status, created_at)
               VALUES (?, ?, ?, ?, ?, ?)""",
            (user_id, username, city_name, now_str, status, now_str)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error registering player {user_id}: {e}")
    finally:
        conn.close()

def approve_city(user_id: int) -> tuple[bool, str]:
    player = get_player(user_id)
    if not player:
        return False, "کاربر یافت نشد."
    if player["status"] == "approved":
        return False, f"این شهر قبلاً تایید شده است (نام: {player['city_name']})."
    if player["status"] != "pending_approval":
        return False, f"کاربر در وضعیت انتظار تایید قرار ندارد. وضعیت فعلی: {player['status']}"
        
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("BEGIN IMMEDIATE")
        
        # Double-check inside connection
        c.execute("SELECT status, city_name FROM players WHERE user_id = ?", (user_id,))
        row = c.fetchone()
        if not row:
            conn.rollback()
            return False, "کاربر یافت نشد."
        db_status, db_city_name = row
        if db_status == "approved":
            conn.rollback()
            return False, f"این شهر قبلاً تایید شده است (نام: {db_city_name})."
        if db_status != "pending_approval":
            conn.rollback()
            return False, f"کاربر در وضعیت انتظار تایید قرار ندارد. وضعیت فعلی: {db_status}"

        c.execute("UPDATE players SET status = 'approved' WHERE user_id = ?", (user_id,))
        
        # Give starting building (Town Hall Level 1) when approved
        c.execute("SELECT 1 FROM buildings WHERE user_id = ? AND building_type = 'town_hall'", (user_id,))
        if not c.fetchone():
            now_str = datetime.now().isoformat()
            c.execute(
                "INSERT INTO buildings (user_id, building_type, level, created_at) VALUES (?, 'town_hall', 1, ?)",
                (user_id, now_str)
            )
        conn.commit()
        # Clear player state just in case
        USER_STATES.pop(user_id, None)
        return True, f"شهر «{player['city_name']}» با موفقیت تایید شد."
    except Exception as e:
        conn.rollback()
        logger.error(f"Error approving city for {user_id}: {e}")
        return False, "خطای دیتابیس در تایید شهر."
    finally:
        conn.close()

def reject_city(user_id: int) -> tuple[bool, str]:
    player = get_player(user_id)
    if not player:
        return False, "کاربر یافت نشد."
    if player["status"] == "approved":
        return False, "این شهر قبلاً تایید شده و فعال است و نمی‌توان آن را رد کرد."
    if player["status"] != "pending_approval":
        return False, f"کاربر در وضعیت انتظار تایید قرار ندارد. وضعیت فعلی: {player['status']}"
        
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute("BEGIN IMMEDIATE")
        
        # Double-check inside connection
        c.execute("SELECT status FROM players WHERE user_id = ?", (user_id,))
        row = c.fetchone()
        if not row:
            conn.rollback()
            return False, "کاربر یافت نشد."
        db_status = row[0]
        if db_status == "approved":
            conn.rollback()
            return False, "این شهر قبلاً تایید شده و فعال است."
        if db_status != "pending_approval":
            conn.rollback()
            return False, f"کاربر در وضعیت انتظار تایید قرار ندارد. وضعیت فعلی: {db_status}"

        c.execute("UPDATE players SET status = 'rejected' WHERE user_id = ?", (user_id,))
        conn.commit()
        # Clear player state
        USER_STATES.pop(user_id, None)
        return True, "نام پیشنهادی شهر رد شد."
    except Exception as e:
        conn.rollback()
        logger.error(f"Error rejecting city for {user_id}: {e}")
        return False, "خطای دیتابیس در رد شهر."
    finally:
        conn.close()

def get_buildings(user_id: int, cache=None):
    if cache is not None:
        return cache
    conn = sqlite3.connect(DB_FILE)
    try:
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM buildings WHERE user_id = ?", (user_id,))
        rows = c.fetchall()
        return [dict(r) for r in rows]
    except Exception as e:
        logger.error(f"Error retrieving buildings for user {user_id}: {e}")
        return []
    finally:
        conn.close()

def update_player_resources(user_id: int, gold: int, wood: int, stone: int, food: int):
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute(
            """UPDATE players 
               SET gold = MAX(0, gold + ?), wood = MAX(0, wood + ?), stone = MAX(0, stone + ?), food = MAX(0, food + ?) 
               WHERE user_id = ?""",
            (gold, wood, stone, food, user_id)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error updating player resources for {user_id}: {e}")
    finally:
        conn.close()

def add_building(user_id: int, building_type: str):
    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        now_str = datetime.now().isoformat()
        c.execute(
            "INSERT INTO buildings (user_id, building_type, level, created_at) VALUES (?, ?, 1, ?)",
            (user_id, building_type, now_str)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error adding building for user {user_id}: {e}")
    finally:
        conn.close()

def upgrade_building_db(building_id: int):
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("UPDATE buildings SET level = level + 1 WHERE id = ?", (building_id,))
    conn.commit()
    conn.close()

# ---------------------------------------------------------
# Game Balance / Cost Configs
# ---------------------------------------------------------
BUILDING_INFO = {
    "town_hall": {
        "name": "🏛️ تالار شهر (Town Hall)",
        "description": "مرکز فرماندهی شهر شما. افزایش ظرفیت کلی شهر.",
        "base_cost": {"gold": 500, "wood": 300, "stone": 200},
        "multiplier": 1.5
    },
    "farm": {
        "name": "🌾 مزرعه گندم (Wheat Farm)",
        "description": "تولید کننده غذا برای شهروندان شما.",
        "base_cost": {"gold": 150, "wood": 100, "stone": 50},
        "multiplier": 1.3,
        "production": {"food": 15}  # food per hour per level
    },
    "sawmill": {
        "name": "🪵 کارگاه چوب‌بری (Sawmill)",
        "description": "تولید کننده چوب برای ساخت و ساز ساختمان‌ها.",
        "base_cost": {"gold": 200, "wood": 50, "stone": 100},
        "multiplier": 1.3,
        "production": {"wood": 12}  # wood per hour per level
    },
    "mine": {
        "name": "🪨 معدن سنگ (Quarry Mine)",
        "description": "استخراج سنگ‌های باارزش برای استحکام بناها.",
        "base_cost": {"gold": 250, "wood": 150, "stone": 50},
        "multiplier": 1.4,
        "production": {"stone": 10}  # stone per hour per level
    },
    "market": {
        "name": "🪙 بازار تجاری (Market)",
        "description": "تولید کننده سکه طلا از طریق داد و ستد.",
        "base_cost": {"gold": 300, "wood": 200, "stone": 150},
        "multiplier": 1.4,
        "production": {"gold": 20}  # gold per hour per level
    },
    "warehouse": {
        "name": "📦 انبار کالا (Warehouse)",
        "description": "افزایش ظرفیت ذخیره‌سازی منابع شهر.",
        "base_cost": {"gold": 250, "wood": 200, "stone": 200},
        "multiplier": 1.4,
        "production": {}
    }
}

def calculate_cost(building_type: str, current_level: int, user_id: int = None):
    """Calculates the upgrade/construction cost based on building level and construction methods technology."""
    config = BUILDING_INFO.get(building_type)
    if not config:
        return {"gold": 99999, "wood": 99999, "stone": 99999}
    
    mult = config["multiplier"] ** (current_level - 1)
    base = config["base_cost"]
    
    cost_reduction = 0.0
    if user_id:
        techs = get_player_technologies(user_id)
        cost_reduction = techs.get("construction_methods", 0) * 0.05 # 5% per level
        
    return {
        "gold": int(base["gold"] * mult * (1.0 - cost_reduction)),
        "wood": int(base["wood"] * mult * (1.0 - cost_reduction)),
        "stone": int(base["stone"] * mult * (1.0 - cost_reduction))
    }

def calculate_production_rates(buildings, user_id: int = None, tech_cache=None, player_cache=None):
    """Calculates total production rates per hour for all resources, applying technologies and world events."""
    rates = {"gold": 5, "wood": 5, "stone": 3, "food": 2}  # base rates
    
    for b in buildings:
        b_type = b["building_type"]
        level = b["level"]
        config = BUILDING_INFO.get(b_type)
        if config and "production" in config:
            for res, base_prod in config["production"].items():
                rates[res] += base_prod * level
                
    if user_id:
        techs = get_player_technologies(user_id, cache=tech_cache)
        
        # Apply tech bonuses
        # adv_farming: +10% food per level
        food_bonus = techs.get("adv_farming", 0) * 0.10
        rates["food"] = rates["food"] * (1.0 + food_bonus)
        
        # ind_efficiency: +8% wood and stone per level
        ind_bonus = techs.get("ind_efficiency", 0) * 0.08
        rates["wood"] = rates["wood"] * (1.0 + ind_bonus)
        rates["stone"] = rates["stone"] * (1.0 + ind_bonus)
        
        # Apply active event modifiers
        player = get_player(user_id, cache=player_cache)
        if player and player.get("active_event"):
            evt_id = player["active_event"]
            if evt_id in EVENTS_INFO:
                mod = EVENTS_INFO[evt_id].get("modifier", {})
                for res, multiplier in mod.items():
                    if res in rates:
                        rates[res] = rates[res] * multiplier
                        
    return rates

def calculate_building_maintenance_rate(buildings, user_id: int = None, tech_cache=None) -> float:
    """Calculates total maintenance gold cost per hour based on building types, levels, and market optimization technology."""
    # Small hourly gold maintenance costs per level
    maintenance_rates = {
        "town_hall": 1.5,
        "farm": 0.5,
        "sawmill": 0.8,
        "mine": 1.0,
        "market": 0.5,
        "warehouse": 1.2
    }
    total_cost = 0.0
    for b in buildings:
        b_type = b["building_type"]
        level = b["level"]
        rate = maintenance_rates.get(b_type, 0.5)
        total_cost += rate * level
        
    if user_id:
        techs = get_player_technologies(user_id, cache=tech_cache)
        m_opt = techs.get("market_opt", 0)
        # market_opt: -5% maintenance per level (capped at 50% discount / level 10)
        discount = min(0.50, m_opt * 0.05)
        total_cost = total_cost * (1.0 - discount)
        
    return total_cost

def calculate_happiness_pressure_rate(buildings) -> float:
    """Calculates total recurring happiness pressure per hour based on industrial/crowded buildings, offset by clean/civic buildings."""
    pressure_rates = {
        "mine": 0.15,
        "sawmill": 0.1,
        "warehouse": 0.05
    }
    total_pressure = 0.0
    civic_relief = 0.0
    for b in buildings:
        b_type = b["building_type"]
        level = b["level"]
        rate = pressure_rates.get(b_type, 0.0)
        total_pressure += rate * level
        
        # Civic/clean buildings provide relief offset
        if b_type == "town_hall":
            civic_relief += 0.1 * level
        elif b_type == "farm":
            civic_relief += 0.05 * level
            
    return max(0.0, total_pressure - civic_relief)

def process_offline_production(user_id: int):
    """Calculates and adds resources produced since the last collection/claim, taking into account food consumption, population growth, and happiness."""
    # System 2: Check and trigger World Events + City Incidents
    check_and_trigger_event(user_id)
    
    player = get_player(user_id)
    if not player or player["status"] != 'approved':
        return 0, 0, 0, 0, 0
        
    buildings = get_buildings(user_id)
    techs = get_player_technologies(user_id)
    
    rates = calculate_production_rates(buildings, user_id, tech_cache=techs, player_cache=player)
    
    last_collect_str = player["last_collect"]
    if not last_collect_str:
        last_collect_str = datetime.now().isoformat()
        
    try:
        last_collect = datetime.fromisoformat(last_collect_str)
    except Exception:
        last_collect = datetime.now()
        
    now = datetime.now()
    duration = now - last_collect
    hours = duration.total_seconds() / 3600.0
    
    # Cap offline production at 12 hours max to encourage active playing
    if hours > 12:
        hours = 12.0
        
    current_pop = float(player.get("population") if player.get("population") is not None else 10)
    current_hap = float(player.get("happiness") if player.get("happiness") is not None else 100)

    # Happiness events logic with lightweight bonuses & penalties
    happiness_modifier = get_happiness_modifier(current_hap)
    if hours >= 1.0:
        if current_hap >= 90.0:
            add_game_log(user_id, "happiness_event", "🎉 به دلیل رضایت فوق‌العاده شهروندان (بالای ۹۰٪)، تولید منابع شما ۲۰٪ افزایش یافت!")
        elif current_hap >= 85.0:
            add_game_log(user_id, "happiness_event", "🎉 به دلیل رضایت بسیار بالای شهروندان (بالای ۸۵٪)، تولید منابع شما ۱۵٪ افزایش یافت!")
        elif current_hap <= 25.0:
            add_game_log(user_id, "happiness_event", "⚠️ به دلیل نارضایتی شدید شهروندان (زیر ۲۵٪)، تولید منابع شما ۴۰٪ کاهش یافت و شاهد کاهش تدریجی جمعیت هستیم!")
        elif current_hap <= 35.0:
            add_game_log(user_id, "happiness_event", "⚠️ به دلیل رضایت بسیار پایین شهروندان (زیر ۳۵٪)، تولید منابع شما ۳۰٪ کاهش یافت!")

    g_prod = int(rates["gold"] * hours * happiness_modifier)
    w_prod = int(rates["wood"] * hours * happiness_modifier)
    s_prod = int(rates["stone"] * hours * happiness_modifier)
    f_prod = int(rates["food"] * hours * happiness_modifier)
    
    if hours < 0.01:
        return 0, 0, 0, 0, hours

    # Calculate food consumption: 0.5 units of food per citizen per hour
    f_consumed = current_pop * 0.5 * hours
    
    final_food = int(player["food"] + f_prod)
    new_happiness = current_hap
    new_population = current_pop
    
    edu_lvl = techs.get("education", 0)
    
    if final_food >= f_consumed:
        final_food = int(final_food - f_consumed)
        # Apply education tech bonus (+5% happiness gain effectiveness per level)
        happiness_gain = hours * 2.0 * (1.0 + edu_lvl * 0.05)
        new_happiness = min(100.0, current_hap + happiness_gain)
        
        # Population grows if happiness is high
        if new_happiness >= 75.0:
            max_pop = float(get_max_population_for_player(user_id, buildings_cache=buildings))
            if current_pop < max_pop:
                pop_growth = hours * 0.5  # 0.5 people per hour
                
                # Apply education tech bonus (+10% pop growth speed per level)
                pop_growth *= (1.0 + edu_lvl * 0.10)
                
                # Apply event modifiers if any (e.g., Migration Wave event increases growth speed)
                active_evt = player.get("active_event")
                if active_evt and active_evt in EVENTS_INFO:
                    pop_growth *= EVENTS_INFO[active_evt].get("modifier", {}).get("pop_growth", 1.0)
                    
                # Extra growth speed if happiness is exceptional (>= 90%)
                if current_hap >= 90.0:
                    pop_growth *= 1.5
                new_population = min(max_pop, current_pop + pop_growth)
                if int(new_population) > int(current_pop):
                    add_game_log(user_id, "population", f"📈 رشد جمعیت! جمعیت شهر شما به {int(new_population)} نفر رسید.")
    else:
        final_food = 0
        happiness_loss = hours * 5.0
        new_happiness = max(0.0, current_hap - happiness_loss)
        
        # Warning log for food shortage
        if hours >= 0.5:
            add_game_log(user_id, "food_shortage", "⚠️ هشدار شدید! انبار غذای شهر خالی شده است. شهروندان گرسنه هستند و رضایت عمومی در حال فروپاشی است!")
        
        # Population declines if happiness is low
        if new_happiness <= 40.0:
            pop_loss = hours * 0.5  # 0.5 people per hour
            if new_happiness <= 25.0:
                pop_loss *= 1.5 # Faster decline if happiness <= 25%
            new_population = max(5.0, current_pop - pop_loss)
            if int(new_population) < int(current_pop):
                add_game_log(user_id, "population", f"📉 کاهش جمعیت! به دلیل گرسنگی شدید، جمعیت به {int(new_population)} نفر رسید.")

    # Apply recurring happiness pressure from industrial and crowded buildings
    hap_pressure_rate = calculate_happiness_pressure_rate(buildings)
    hap_pressure = hap_pressure_rate * hours
    new_happiness = max(0.0, new_happiness - hap_pressure)

    # Calculate building maintenance cost and apply pressure
    maint_rate = calculate_building_maintenance_rate(buildings, user_id, tech_cache=techs)
    maint_gold_cost = int(maint_rate * hours)
    
    current_gold = player["gold"] + g_prod
    maint_failed = False
    if current_gold >= maint_gold_cost:
        final_gold = current_gold - maint_gold_cost
    else:
        final_gold = 0
        maint_failed = True
        # Happiness drops slightly because of neglected infrastructure
        hap_penalty = hours * 3.0
        new_happiness = max(0.0, new_happiness - hap_penalty)
        if hours >= 0.5:
            add_game_log(user_id, "maintenance_failure", f"⚠️ بحران مالی! طلای شما برای نگهداری ابنیه کافی نبود ({maint_gold_cost} سکه). رضایت عمومی کاهش یافت!")

    g_change = final_gold - player["gold"]

    conn = sqlite3.connect(DB_FILE)
    try:
        c = conn.cursor()
        c.execute(
            """UPDATE players 
               SET gold = ?, wood = wood + ?, stone = stone + ?, food = ?, population = ?, happiness = ?, last_collect = ? 
               WHERE user_id = ?""",
            (final_gold, w_prod, s_prod, final_food, new_population, new_happiness, now.isoformat(), user_id)
        )
        conn.commit()
    except Exception as e:
        conn.rollback()
        logger.error(f"Error persisting offline production for {user_id}: {e}")
    finally:
        conn.close()
    
    # Clamp resources to player's storage capacity
    # Build updated player record for clamping cache
    updated_player = dict(player)
    updated_player["wood"] += w_prod
    updated_player["stone"] += s_prod
    updated_player["food"] = final_food
    updated_player["gold"] = final_gold
    
    clamp_player_resources_to_capacity(user_id, player_cache=updated_player, buildings_cache=buildings, tech_cache=techs)
    
    # System 4: Daily Mission progress update (only on meaningful collections)
    if hours >= 0.05:
        update_mission_progress(user_id, "collect", 1)
    
    return g_change, w_prod, s_prod, f_prod, hours

# ---------------------------------------------------------
# Forced Membership Verification
# ---------------------------------------------------------
async def check_force_join(user_id: int, context: ContextTypes.DEFAULT_TYPE) -> bool:
    """Checks if the user has joined the mandatory channel."""
    channel = get_setting('required_channel', '@Abolfazl_rwm')
    if not channel or channel == "@Abolfazl_rwm" or not channel.startswith("@"):
        return True
    try:
        member = await context.bot.get_chat_member(chat_id=channel, user_id=user_id)
        if member.status in ["creator", "administrator", "member"]:
            return True
        return False
    except Exception as e:
        logger.warning(f"Forced join check error for {channel} with user {user_id}: {e}")
        # Default to True to avoid locking out players when the bot isn't an admin in the channel yet.
        return True

async def show_force_join_message(update: Update, context: ContextTypes.DEFAULT_TYPE, is_callback=False):
    """Displays message instructing users to join the required channel."""
    channel = get_setting('required_channel')
    keyboard = [
        [InlineKeyboardButton("📢 عضویت در کانال", url=f"https://t.me/{channel.replace('@', '')}")],
        [InlineKeyboardButton("🔄 تایید عضویت (Verify)", callback_data="check_join")]
    ]
    text = (
        f"📢 *عضویت اجباری*\n\n"
        f"هم‌رزم گرامی، برای استفاده از ربات بازی شهرسازی ابتدا باید عضو کانال رسمی ما شوید:\n"
        f"👉 {channel}\n\n"
        f"پس از عضویت، دکمه تایید زیر را فشار دهید تا قفل ربات باز شود!"
    )
    if is_callback and update.callback_query:
        await update.callback_query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")
    else:
        message = update.message or update.effective_message
        if message:
            await message.reply_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

# ---------------------------------------------------------
# Dynamic Menu Keyboards & Views
# ---------------------------------------------------------
def get_main_menu_keyboard(user_id: int):
    keyboard = [
        [
            InlineKeyboardButton("🏛️ شهر من / آمار", callback_data="btn_city_stats"),
            InlineKeyboardButton("🏗️ ساخت و ساز", callback_data="btn_build_menu")
        ],
        [
            InlineKeyboardButton("🌾 جمع‌آوری درآمد", callback_data="btn_collect_resources"),
            InlineKeyboardButton("🎁 پاداش روزانه", callback_data="btn_daily_reward")
        ],
        [
            InlineKeyboardButton("🔬 تحقیق و توسعه", callback_data="btn_research_menu"),
            InlineKeyboardButton("🤝 امور دیپلماسی", callback_data="btn_diplomacy_menu")
        ],
        [
            InlineKeyboardButton("🤝 تجارت با بقیه", callback_data="btn_trade_menu"),
            InlineKeyboardButton("🎯 ماموریت‌های روزانه", callback_data="btn_daily_missions")
        ],
        [
            InlineKeyboardButton("🏆 برترین‌ها", callback_data="btn_leaderboard")
        ]
    ]
    # Add Admin panel button if applicable
    if is_admin(user_id):
        keyboard.append([InlineKeyboardButton("⚙️ پنل ادمین", callback_data="btn_admin_panel")])
    # Add Owner panel button if applicable
    if is_owner(user_id):
        keyboard.append([InlineKeyboardButton("👑 پنل مالک", callback_data="btn_owner_panel")])
        
    return InlineKeyboardMarkup(keyboard)

def get_back_button(target="menu_main"):
    labels = {
        "menu_main": "🔙 بازگشت به منوی اصلی",
        "btn_city_stats": "🔙 بازگشت به آمار شهر",
        "btn_build_menu": "🔙 بازگشت به منوی ساخت",
        "btn_trade_menu": "🔙 بازگشت به منوی تجارت",
        "btn_admin_panel": "🔙 بازگشت به پنل ادمین",
        "btn_owner_panel": "🔙 بازگشت به پنل مالک",
        "btn_research_menu": "🔬 بازگشت به منوی تحقیقات",
        "btn_diplomacy_menu": "🤝 بازگشت به منوی دیپلماسی"
    }
    label = labels.get(target, "🔙 بازگشت")
    return InlineKeyboardMarkup([[InlineKeyboardButton(label, callback_data=target)]])

# ---------------------------------------------------------
# City Name Request Notification Helper
# ---------------------------------------------------------
async def send_to_admins_approval_request(user_id: int, city_name: str, context: ContextTypes.DEFAULT_TYPE):
    """Sends a notification to all admins for approving/rejecting proposed city names."""
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute("SELECT user_id FROM admins")
    admins = [row[0] for row in c.fetchall()]
    conn.close()
    
    if OWNER_ID not in admins:
        admins.append(OWNER_ID)
        
    keyboard = [
        [
            InlineKeyboardButton("✅ تایید (Approve)", callback_data=f"adm_app_{user_id}"),
            InlineKeyboardButton("❌ رد (Reject)", callback_data=f"adm_rej_{user_id}")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    
    for admin_id in admins:
        try:
            await context.bot.send_message(
                chat_id=admin_id,
                text=(
                    f"📥 *درخواست ثبت شهر جدید*\n\n"
                    f"👤 آیدی کاربر: `{user_id}`\n"
                    f"🏰 نام پیشنهادی شهر: *{city_name}*\n\n"
                    f"آیا با ساخت این شهر موافقت می‌کنید؟"
                ),
                reply_markup=reply_markup,
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.warning(f"Could not send approval alert to admin {admin_id}: {e}")

# ---------------------------------------------------------
# Telegram Bot Command Handlers
# ---------------------------------------------------------
async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handles the /start command. Ensures forced join and registration states."""
    user = update.effective_user
    if not user:
        return
        
    user_id = user.id
    USER_STATES.pop(user_id, None)  # Clear any ongoing wizards or stale state
    joined = await check_force_join(user_id, context)
    if not joined:
        await show_force_join_message(update, context)
        return
        
    player = get_player(user_id)
    
    if not player:
        # Prompt player to select their custom city name first
        register_player(user_id, user.username or user.first_name, "", 'pending_name')
        escaped_first_name = escape_markdown(user.first_name)
        
        keyboard = []
        if is_admin(user_id):
            keyboard.append([InlineKeyboardButton("⚙️ پنل ادمین", callback_data="btn_admin_panel")])
        if is_owner(user_id):
            keyboard.append([InlineKeyboardButton("👑 پنل مالک", callback_data="btn_owner_panel")])
        reply_markup = InlineKeyboardMarkup(keyboard) if keyboard else None
        
        await update.message.reply_text(
            f"👑 *فرمانده گرامی {escaped_first_name}، به بازی شبیه‌ساز شهرسازی خوش آمدید!*\n\n"
            f"قبل از بنا نهادن قلمرو خود، باید یک نام برای شهرتان انتخاب کنید.\n\n"
            f"✍️ لطفاً نام مورد نظر خود را به صورت پیام متنی ارسال کنید (بین ۳ تا ۲۰ کاراکتر):",
            reply_markup=reply_markup,
            parse_mode="Markdown"
        )
        return
        
    status = player["status"]
    
    if status == 'pending_name':
        keyboard = []
        if is_admin(user_id):
            keyboard.append([InlineKeyboardButton("⚙️ پنل ادمین", callback_data="btn_admin_panel")])
        if is_owner(user_id):
            keyboard.append([InlineKeyboardButton("👑 پنل مالک", callback_data="btn_owner_panel")])
        reply_markup = InlineKeyboardMarkup(keyboard) if keyboard else None
        
        await update.message.reply_text(
            "✍️ لطفاً نام پیشنهادی شهر خود را ارسال فرمایید تا جهت بررسی به بخش فرمانداری (ادمین) ارسال گردد:",
            reply_markup=reply_markup
        )
    elif status == 'pending_approval':
        keyboard = []
        if is_admin(user_id):
            keyboard.append([InlineKeyboardButton("⚙️ پنل ادمین", callback_data="btn_admin_panel")])
        if is_owner(user_id):
            keyboard.append([InlineKeyboardButton("👑 پنل مالک", callback_data="btn_owner_panel")])
        reply_markup = InlineKeyboardMarkup(keyboard) if keyboard else None
        
        escaped_city_name = escape_markdown(player['city_name'])
        await update.message.reply_text(
            f"⏳ *نام انتخابی شما: «{escaped_city_name}» در صف بررسی ادمین قرار دارد.*\n\n"
            f"به محض تایید یا رد درخواست توسط ادمین، نتیجه به شما اعلام خواهد شد. لطفا صبور باشید.",
            reply_markup=reply_markup,
            parse_mode="Markdown"
        )
    elif status == 'rejected':
        keyboard = []
        if is_admin(user_id):
            keyboard.append([InlineKeyboardButton("⚙️ پنل ادمین", callback_data="btn_admin_panel")])
        if is_owner(user_id):
            keyboard.append([InlineKeyboardButton("👑 پنل مالک", callback_data="btn_owner_panel")])
        reply_markup = InlineKeyboardMarkup(keyboard) if keyboard else None
        
        await update.message.reply_text(
            f"❌ *نام شهر قبلی شما مورد تایید فرمانداری قرار نگرفت.*\n\n"
            f"لطفاً نام مناسب و زیباتر دیگری را انتخاب کرده و ارسال نمایید:",
            reply_markup=reply_markup
        )
    elif status == 'approved':
        # Collect idle resources automatically on login
        g, w, s, f, hrs = process_offline_production(user_id)
        player = get_player(user_id) # reload
        
        escaped_first_name = escape_markdown(user.first_name)
        escaped_city_name = escape_markdown(player['city_name'])
        welcome_text = (
            f"🎖️ *خوش آمدید شهردار گرامی {escaped_first_name}!*\n"
            f"قلمرو فرمانروایی شما: *«{escaped_city_name}»*\n\n"
        )
        if hrs > 0.05:
            f_consumed = int(player.get("population", 10) * 0.5 * hrs)
            welcome_text += (
                f"📈 در مدت غیبت شما (حدود `{hrs:.1f}` ساعت)، کارگران تلاش کردند:\n"
                f"🪙 طلا: `+{g}` | 🪵 چوب: `+{w}` | 🪨 سنگ: `+{s}` | 🌾 غذا: `+{f}`\n"
            )
            if f_consumed > 0:
                welcome_text += f"🍽️ مصرف غذای شهروندان: `-{f_consumed}` واحد\n"
            welcome_text += "\n"
        welcome_text += "جهت مدیریت منابع و ارتقای بناها از گزینه‌های زیر استفاده کنید:"
        
        await update.message.reply_text(
            welcome_text,
            reply_markup=get_main_menu_keyboard(user_id),
            parse_mode="Markdown"
        )


# ---------------------------------------------------------
# Message Handler (Handling text entries for wizards)
# ---------------------------------------------------------
async def message_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if not user:
        return
    user_id = user.id
    text = update.message.text.strip()
    
    # 1. Forced Channel Join Check
    joined = await check_force_join(user_id, context)
    if not joined:
        await show_force_join_message(update, context)
        return
        
    player = get_player(user_id)
    if not player:
        # Prompt player to select their custom city name first
        register_player(user_id, user.username or user.first_name, "", 'pending_name')
        escaped_first_name = escape_markdown(user.first_name)
        await update.message.reply_text(
            f"👑 *فرمانده گرامی {escaped_first_name}، به بازی شبیه‌ساز شهرسازی خوش آمدید!*\n\n"
            f"قبل از بنا نهادن قلمرو خود، باید یک نام برای شهرتان انتخاب کنید.\n\n"
            f"✍️ لطفاً نام مورد نظر خود را به صورت پیام متنی ارسال کنید (بین ۳ تا ۲۰ کاراکتر):",
            parse_mode="Markdown"
        )
        return

    # Check state first
    state_info = USER_STATES.get(user_id)
    
    if state_info:
        state = state_info.get("state")
        data = state_info.get("data", {})
        
        # Hardened permission check for admin and owner states
        if state in ["admin_giving_resources_id", "admin_giving_resources_amount"]:
            if not is_admin(user_id):
                USER_STATES.pop(user_id, None)
                await update.message.reply_text("❌ خطای امنیتی: شما دسترسی مدیریت ندارید.")
                return
        elif state in ["owner_adding_admin", "owner_removing_admin", "owner_changing_channel"]:
            if not is_owner(user_id):
                USER_STATES.pop(user_id, None)
                await update.message.reply_text("❌ خطای امنیتی: شما دسترسی مالک اصلی را ندارید.")
                return
        
        if state == "entering_trade_sell_amount":
            try:
                amt = int(text)
            except ValueError:
                amt = -1
            if amt <= 0:
                await update.message.reply_text("❌ لطفاً یک عدد معتبر بزرگتر از صفر وارد کنید:")
                return
            sell_res = data.get("sell_resource")
            if sell_res not in ["gold", "wood", "stone", "food"]:
                await update.message.reply_text("❌ منبع فروش نامعتبر است.", reply_markup=get_back_button("btn_trade_menu"))
                USER_STATES.pop(user_id, None)
                return
            if not has_sufficient_resource(user_id, sell_res, amt):
                await update.message.reply_text(f"❌ موجودی شما کافی نیست! موجودی انبار شما: {player[sell_res]}")
                return
            
            # Transition to selecting trade buy resource, excluding the sell resource
            res_labels = {
                "gold": "🪙 طلا",
                "wood": "🪵 چوب",
                "stone": "🪨 سنگ",
                "food": "🌾 غذا"
            }
            keyboard_row = []
            keyboard = []
            for r_type, r_label in res_labels.items():
                if r_type != sell_res:
                    keyboard_row.append(InlineKeyboardButton(r_label, callback_data=f"buyres_{r_type}"))
                    if len(keyboard_row) == 2:
                        keyboard.append(keyboard_row)
                        keyboard_row = []
            if keyboard_row:
                keyboard.append(keyboard_row)
            keyboard.append([InlineKeyboardButton("❌ انصراف", callback_data="btn_trade_menu")])
            
            USER_STATES[user_id] = {
                "state": "selecting_trade_buy_resource",
                "data": {**data, "sell_amount": amt}
            }
            await update.message.reply_text(
                f"✅ مقدار فروش: `{amt}` {sell_res} تایید شد.\n\n"
                f"حالا انتخاب کنید در قبال آن چه منبعی می‌خواهید دریافت کنید:",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode="Markdown"
            )
            return

        elif state == "entering_trade_buy_amount":
            try:
                amt = int(text)
            except ValueError:
                amt = -1
            if amt <= 0:
                await update.message.reply_text("❌ لطفاً یک عدد معتبر بزرگتر از صفر وارد کنید:")
                return
                
            sell_res = data.get("sell_resource")
            sell_amt = data.get("sell_amount")
            buy_res = data.get("buy_resource")
            
            if sell_res not in ["gold", "wood", "stone", "food"] or buy_res not in ["gold", "wood", "stone", "food"]:
                await update.message.reply_text("❌ منابع معامله معتبر نیستند.", reply_markup=get_back_button("btn_trade_menu"))
                USER_STATES.pop(user_id, None)
                return
                
            if not has_sufficient_resource(user_id, sell_res, sell_amt):
                await update.message.reply_text(f"❌ موجودی انبار شما کافی نیست! موجودی: {player[sell_res]}", reply_markup=get_back_button("btn_trade_menu"))
                USER_STATES.pop(user_id, None)
                return
                
            # Create the trade open record
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            try:
                c.execute("BEGIN IMMEDIATE")
                c.execute(f"SELECT {sell_res} FROM players WHERE user_id = ?", (user_id,))
                row = c.fetchone()
                if not row or row[0] < sell_amt:
                    conn.rollback()
                    await update.message.reply_text("❌ موجودی انبار شما کافی نیست یا تراکنش همزمان رخ داده است.", reply_markup=get_back_button("btn_trade_menu"))
                    USER_STATES.pop(user_id, None)
                    return
                # Deduct resources from seller
                c.execute(f"UPDATE players SET {sell_res} = {sell_res} - ? WHERE user_id = ?", (sell_amt, user_id))
                # Insert trade
                now_str = datetime.now().isoformat()
                c.execute(
                    """INSERT INTO market_trades (seller_id, sell_resource, sell_amount, buy_resource, buy_amount, status, created_at)
                       VALUES (?, ?, ?, ?, ?, 'open', ?)""",
                    (user_id, sell_res, sell_amt, buy_res, amt, now_str)
                )
                conn.commit()
                add_game_log(user_id, "trade", f"➕ ثبت پیشنهاد عرضه {sell_amt} {sell_res} در بازار در قبال دریافت {amt} {buy_res}")
                
                # System 4: Daily Mission progress update
                update_mission_progress(user_id, "trade", 1)
                
                # Clamp resources (after deducting)
                clamp_player_resources_to_capacity(user_id)
                
                await update.message.reply_text(
                    f"🎉 *پیشنهاد تجاری شما با موفقیت در بازار ثبت شد!*\n\n"
                    f"📦 اقلام سپرده شده: `{sell_amt}` {sell_res}\n"
                    f"🪙 ارزش درخواستی: `{amt}` {buy_res}\n\n"
                    f"دیگر شهردارها می‌توانند این پیشنهاد را مشاهده و پذیرش کنند.",
                    reply_markup=get_back_button("btn_trade_menu"),
                    parse_mode="Markdown"
                )
            except Exception as e:
                conn.rollback()
                logger.error(f"Error creating trade: {e}")
                await update.message.reply_text("❌ خطا در ثبت پیشنهاد در بازار.", reply_markup=get_back_button("btn_trade_menu"))
            finally:
                conn.close()
                USER_STATES.pop(user_id, None)
            return

        elif state == "entering_transfer_target":
            target = get_player_by_username_or_id(text)
            if not target or target.get("status") != 'approved':
                await update.message.reply_text("❌ شهر مقصد یافت نشد یا هنوز تایید نشده است. مجدداً آیدی عددی یا نام کاربری دقیق مقصد را بفرستید:")
                return
                
            if target["user_id"] == user_id:
                await update.message.reply_text("❌ شما نمی‌توانید برای خودتان هدیه بفرستید! مجدداً آیدی عددی یا نام کاربری دقیق دیگری بفرستید:")
                return
                
            USER_STATES[user_id] = {
                "state": "selecting_transfer_resource",
                "data": {"target_id": target["user_id"], "target_city": target["city_name"]}
            }
            keyboard = [
                [
                    InlineKeyboardButton("🪙 طلا", callback_data=f"txres_gold"),
                    InlineKeyboardButton("🪵 چوب", callback_data=f"txres_wood")
                ],
                [
                    InlineKeyboardButton("🪨 سنگ", callback_data=f"txres_stone"),
                    InlineKeyboardButton("🌾 غذا", callback_data=f"txres_food")
                ],
                [InlineKeyboardButton("❌ انصراف", callback_data="btn_trade_menu")]
            ]
            await update.message.reply_text(
                f"🐫 *دروازه‌های شهر مقصد باز شد: «شهر {escape_markdown(target['city_name'])}»*\n\n"
                f"حالا محموله‌ای که مایلید با کاروان حمایتی ارسال کنید را انتخاب فرمایید:",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode="Markdown"
            )
            return

        elif state == "entering_transfer_amount":
            try:
                amt = int(text)
            except ValueError:
                amt = -1
            if amt <= 0:
                await update.message.reply_text("❌ لطفاً یک عدد معتبر بزرگتر از صفر وارد کنید:")
                return
                
            tx_res = data.get("transfer_resource")
            target_id = data.get("target_id")
            target_city = data.get("target_city")
            
            if tx_res not in ["gold", "wood", "stone", "food"]:
                await update.message.reply_text("❌ منبع ارسالی نامعتبر است.", reply_markup=get_back_button("btn_trade_menu"))
                USER_STATES.pop(user_id, None)
                return
                
            if not has_sufficient_resource(user_id, tx_res, amt):
                await update.message.reply_text(f"❌ موجودی کافی نیست! موجودی: {player[tx_res]}")
                return
                
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            try:
                c.execute("BEGIN IMMEDIATE")
                c.execute(f"SELECT {tx_res} FROM players WHERE user_id = ?", (user_id,))
                row = c.fetchone()
                if not row or row[0] < amt:
                    conn.rollback()
                    await update.message.reply_text("❌ موجودی انبار شما کافی نیست یا تراکنش همزمان رخ داده است.", reply_markup=get_back_button("btn_trade_menu"))
                    USER_STATES.pop(user_id, None)
                    return
                # Check for trade or friendship agreement
                has_ag = has_trade_agreement(user_id, target_id)
                tax_rate = 0.0 if has_ag else 0.10
                tax_amt = int(amt * tax_rate)
                net_amt = amt - tax_amt
                
                c.execute(f"UPDATE players SET {tx_res} = {tx_res} - ? WHERE user_id = ?", (amt, user_id))
                c.execute(f"UPDATE players SET {tx_res} = {tx_res} + ? WHERE user_id = ?", (net_amt, target_id))
                conn.commit()
                
                res_name_fa = {"gold": "طلا", "wood": "چوب", "stone": "سنگ", "food": "غذا"}.get(tx_res, tx_res)
                
                # Log transfer events
                tax_msg = f" (شامل {tax_amt} مالیات جاده به دلیل نبود پیمان تجاری)" if tax_amt > 0 else " (بدون مالیات به دلیل پیمان تجاری فعال)"
                add_game_log(user_id, "transfer", f"🐫 اعزام کاروان امدادی حامل {amt} {res_name_fa} به شهر {target_city}{tax_msg}")
                add_game_log(target_id, "transfer", f"🐫 ورود کاروان امدادی حامل {net_amt} {res_name_fa} از طرف شهرداری {player['city_name']}{tax_msg}")
                
                # Clamp resources for both players
                clamp_player_resources_to_capacity(user_id)
                clamp_player_resources_to_capacity(target_id)
                
                # Daily Mission progress update
                update_mission_progress(user_id, "transfer", 1)
                
                escaped_target_city = escape_markdown(target_city)
                await update.message.reply_text(
                    f"🐫 *اعزام کاروان تجاری با موفقیت انجام شد!*\n\n"
                    f"📦 کاروان حامل `{amt}` {res_name_fa} با موفقیت به شهر *«{escaped_target_city}»* اعزام شد.\n"
                    f"💸 مالیات جاده کسر شده: `{tax_amt}` {res_name_fa}\n"
                    f"📥 مقدار نهایی تحویل شده: `{net_amt}` {res_name_fa}",
                    reply_markup=get_back_button("btn_trade_menu"),
                    parse_mode="Markdown"
                )
                
                try:
                    escaped_sender_city = escape_markdown(player['city_name'])
                    await context.bot.send_message(
                        chat_id=target_id,
                        text=(
                            f"🐫 *یک کاروان کمک‌های مردمی وارد دروازه شهر شد!*\n\n"
                            f"شهردار شهر *«{escaped_sender_city}»* یک کاروان تجاری حامل کمک‌های زیر برای شما فرستاده است:\n"
                            f"📦 محموله کاروان: `{net_amt}` {res_name_fa} (پس از کسر `{tax_amt}` مالیات جاده)\n\n"
                            f"اموال با موفقیت در انبارهای شما تخلیه و ذخیره شدند!"
                        ),
                        parse_mode="Markdown"
                    )
                except Exception:
                    pass
            except Exception as e:
                conn.rollback()
                logger.error(f"Error transferring resources: {e}")
                await update.message.reply_text("❌ متاسفانه خطایی در ارسال هدیه به مقصد رخ داد.", reply_markup=get_back_button("btn_trade_menu"))
            finally:
                conn.close()
                USER_STATES.pop(user_id, None)
            return

        elif state == "admin_giving_resources_id":
            target = get_player_by_username_or_id(text)
            if not target:
                await update.message.reply_text("❌ کاربر مورد نظر در سیستم بازی ثبت‌نام نکرده است. مجدداً آیدی یا یوزرنیم دقیق بفرستید:")
                return
            target_uid = target["user_id"]
                
            USER_STATES[user_id] = {
                "state": "admin_giving_resources_amount",
                "data": {"target_uid": target_uid}
            }
            await update.message.reply_text(
                f"👤 کاربر مقصد: `{target_uid}` ({target['city_name'] or 'بدون نام شهر'})\n\n"
                f"لطفاً نوع و مقدار را بنویسید (مثال: `gold 1000` یا `wood 500`):"
            )
            return

        elif state == "admin_giving_resources_amount":
            parts = text.lower().split(" ")
            if len(parts) != 2:
                await update.message.reply_text("❌ فرمت نامعتبر است! مثال: `gold 1000` یا `wood 500`:")
                return
            res_type, amt_str = parts
            try:
                amt = int(amt_str)
            except ValueError:
                amt = -1
                
            if res_type not in ["gold", "wood", "stone", "food"] or amt <= 0:
                await update.message.reply_text("❌ منبع باید یکی از این موارد باشد: gold, wood, stone, food و مقدار نیز بزرگتر از صفر باشد:")
                return
                
            target_uid = data.get("target_uid")
            
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            try:
                c.execute(f"UPDATE players SET {res_type} = {res_type} + ? WHERE user_id = ?", (amt, target_uid))
                conn.commit()
                
                # Clamp resources for the target player
                clamp_player_resources_to_capacity(target_uid)
                
                await update.message.reply_text(
                    f"✅ هدیه ادمین با موفقیت واریز شد:\n"
                    f"📦 مقدار: `{amt}` {res_type}\n"
                    f"👤 کاربر مقصد: `{target_uid}`",
                    reply_markup=get_back_button("btn_admin_panel"),
                    parse_mode="Markdown"
                )
                
                try:
                    await context.bot.send_message(
                        chat_id=target_uid,
                        text=(
                            f"🎁 *فرمانداری کل (ادمین) هدایایی به انبار شما اضافه کرد!*\n\n"
                            f"📦 مقدار: `{amt}` {res_type}"
                        ),
                        parse_mode="Markdown"
                    )
                except Exception:
                    pass
            except Exception as e:
                conn.rollback()
                logger.error(f"Error giving admin resources: {e}")
                await update.message.reply_text("❌ خطا در واریز هدیه.", reply_markup=get_back_button("btn_admin_panel"))
            finally:
                conn.close()
                USER_STATES.pop(user_id, None)
            return

        elif state == "owner_adding_admin":
            try:
                uid = int(text)
            except ValueError:
                await update.message.reply_text("❌ لطفاً یک آیدی عددی معتبر بفرستید:")
                return
                
            player_check = get_player(uid)
            if not player_check:
                await update.message.reply_text("❌ کاربر مورد نظر در بازی ثبت‌نام نکرده است! ابتدا باید عضو بازی باشد.")
                return
                
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            try:
                c.execute("INSERT OR REPLACE INTO admins (user_id, role) VALUES (?, 'admin')", (uid,))
                conn.commit()
                await update.message.reply_text(
                    f"✅ کاربر `{uid}` با موفقیت به عنوان ادمین ثبت شد.",
                    reply_markup=get_back_button("btn_owner_panel"),
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Error adding admin: {e}")
                await update.message.reply_text("❌ خطا در اضافه کردن ادمین.", reply_markup=get_back_button("btn_owner_panel"))
            finally:
                conn.close()
                USER_STATES.pop(user_id, None)
            return

        elif state == "owner_removing_admin":
            try:
                uid = int(text)
            except ValueError:
                await update.message.reply_text("❌ لطفاً یک آیدی عددی معتبر بفرستید:")
                return
                
            if uid == OWNER_ID:
                await update.message.reply_text("❌ شما نمی‌توانید مالک اصلی را حذف کنید!")
                return
                
            player_check = get_player(uid)
            if not player_check:
                await update.message.reply_text("❌ کاربر مورد نظر در بازی ثبت‌نام نکرده است!")
                return
                
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            try:
                c.execute("DELETE FROM admins WHERE user_id = ? AND role = 'admin'", (uid,))
                conn.commit()
                await update.message.reply_text(
                    f"✅ کاربر `{uid}` از لیست ادمین‌ها حذف شد.",
                    reply_markup=get_back_button("btn_owner_panel"),
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Error removing admin: {e}")
                await update.message.reply_text("❌ خطا در حذف ادمین.", reply_markup=get_back_button("btn_owner_panel"))
            finally:
                conn.close()
                USER_STATES.pop(user_id, None)
            return

        elif state == "owner_changing_channel":
            new_chan = text.strip()
            if not new_chan or new_chan.lower() in ["خالی", "none", "remove", "حذف", "لغو"]:
                new_chan = ""
            elif not new_chan.startswith("@"):
                await update.message.reply_text("❌ آیدی کانال حتماً باید با @ شروع شود (یا کلمه‌ای نظیر «خالی» یا «حذف» جهت لغو ارسال کنید):")
                return
                
            set_setting("required_channel", new_chan)
            await update.message.reply_text(
                f"✅ کانال عضویت اجباری به *«{new_chan or 'غیرفعال'}»* تغییر یافت.",
                reply_markup=get_back_button("btn_owner_panel"),
                parse_mode="Markdown"
            )
            USER_STATES.pop(user_id, None)
            return

        elif state == "entering_diplomacy_target":
            target = get_player_by_username_or_id(text)
            if not target or target.get("status") != 'approved':
                await update.message.reply_text("❌ شهر مقصد یافت نشد یا هنوز تایید نشده است. مجدداً آیدی عددی یا نام کاربری دقیق مقصد را بفرستید:")
                return
                
            if target["user_id"] == user_id:
                await update.message.reply_text("❌ شما نمی‌توانید با شهر خودتان پیمان دیپلماتیک برقرار کنید! مجدداً آیدی یا نام کاربری دیگری بفرستید:")
                return
                
            keyboard = [
                [
                    InlineKeyboardButton("🤝 پیشنهاد دوستی (Friendship)", callback_data=f"dipl_pro_{target['user_id']}_friendship"),
                    InlineKeyboardButton("📈 توافق تجاری (Trade)", callback_data=f"dipl_pro_{target['user_id']}_trade_agreement")
                ],
                [InlineKeyboardButton("❌ انصراف", callback_data="btn_diplomacy_menu")]
            ]
            await update.message.reply_text(
                f"🤝 *برقراری ارتباط با شهر «{escape_markdown(target['city_name'])}»*\n\n"
                f"لطفاً نوع پیمان یا درخواستی که مایلید ارسال کنید را انتخاب فرمایید:",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode="Markdown"
            )
            USER_STATES.pop(user_id, None)
            return

    # 2. Status Specific Name Selection Flows
    status = player["status"]
    if status in ['pending_name', 'rejected']:
        if not is_valid_city_name(text):
            await update.message.reply_text(
                "❌ نام پیشنهادی نامعتبر است!\n"
                "نام شهر باید بین ۳ تا ۲۰ کاراکتر بوده و فقط شامل حروف (فارسی/انگلیسی)، اعداد و فاصله باشد (بدون علائم خاص). لطفاً نام مناسب دیگری ارسال کنید:"
            )
            return
            
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute("UPDATE players SET city_name = ?, status = 'pending_approval' WHERE user_id = ?", (text, user_id))
        conn.commit()
        conn.close()
        
        await update.message.reply_text(
            f"⏳ *نام انتخابی شما: «{text}» با موفقیت ثبت شد.*\n\n"
            f"درخواست احداث شهر برای مدیران بازی ارسال گردید. به محض بررسی و تایید، بازی شما آغاز خواهد شد. صمیمانه از صبوری شما سپاسگزاریم.",
            parse_mode="Markdown"
        )
        
        # Send notification to admins
        await send_to_admins_approval_request(user_id, text, context)
        return
        
    elif status == 'pending_approval':
        await update.message.reply_text(
            f"⏳ *نام انتخابی شما: «{player['city_name']}» هنوز در صف بررسی قرار دارد.*\n\n"
            f"به محض تایید یا رد درخواست توسط ادمین، نتیجه به شما اعلام خواهد شد."
        )
        return
        
    # If the user is approved, guide them to use start command or button menus
    await update.message.reply_text(
        "👋 برای دسترسی به دکمه‌ها و گزینه‌های مدیریت شهر، لطفاً دستور /start را مجدداً ارسال کنید یا از منوی باز شبیه‌ساز استفاده کنید.",
        reply_markup=get_main_menu_keyboard(user_id)
    )

# ---------------------------------------------------------
# Callback Query Handler (Handling All Inline Buttons)
# ---------------------------------------------------------
async def inline_button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    query_answered = False
    
    async def answer_query(text: str = None, show_alert: bool = False):
        nonlocal query_answered
        if not query_answered:
            try:
                await query.answer(text=text, show_alert=show_alert)
                query_answered = True
            except Exception as e:
                logger.warning(f"Failed to answer callback query: {e}")
    
    user_id = query.from_user.id
    player = get_player(user_id)
    data = query.data

    # Handle force join validation button
    if data == "check_join":
        joined = await check_force_join(user_id, context)
        if joined:
            if not player:
                register_player(user_id, query.from_user.username or query.from_user.first_name, "", 'pending_name')
                await query.edit_message_text(
                    "✅ عضویت شما تایید شد!\n\n✍️ حالا لطفاً نامی زیبا برای شهر خود به صورت پیام متنی ارسال کنید (بین ۳ تا ۲۰ کاراکتر):"
                )
            else:
                status = player["status"]
                if status == 'approved':
                    await query.edit_message_text(
                        "✅ عضویت شما تایید شد! برای ورود به منوی بازی دکمه زیر را لمس کنید:",
                        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🎮 شروع بازی", callback_data="menu_main")]])
                    )
                elif status == 'pending_approval':
                    escaped_city_name = escape_markdown(player['city_name'])
                    await query.edit_message_text(
                        f"✅ عضویت شما تایید شد!\n\n"
                        f"⏳ نام پیشنهادی شما: *«{escaped_city_name}»* هم‌اکنون در صف بررسی قرار دارد و به محض تایید بازی شما آغاز خواهد شد.",
                        parse_mode="Markdown"
                    )
                elif status == 'rejected':
                    await query.edit_message_text(
                        "✅ عضویت شما تایید شد!\n\n"
                        "❌ نام شهر قبلی شما مورد تایید فرمانداری قرار نگرفت.\n"
                        "✍️ لطفاً نام مناسب و زیباتر دیگری را به صورت پیام متنی ارسال کنید (بین ۳ تا ۲۰ کاراکتر):"
                    )
                elif status == 'pending_name':
                    await query.edit_message_text(
                        "✅ عضویت شما تایید شد!\n\n"
                        "✍️ حالا لطفاً نامی زیبا برای شهر خود به صورت پیام متنی ارسال کنید (بین ۳ تا ۲۰ کاراکتر):"
                    )
                else:
                    await query.edit_message_text(
                        "✅ عضویت شما تایید شد!\n\n"
                        "لطفاً دستور /start را ارسال کنید تا فرآیند بازی مجدداً پیگیری شود."
                    )
        else:
            # Still not a member
            await query.edit_message_text(
                f"❌ عضویت شما در کانال هنوز احراز نشده است. لطفاً ابتدا عضو شوید و سپس مجدداً تلاش کنید.",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("📢 عضویت در کانال", url=f"https://t.me/{get_setting('required_channel').replace('@', '')}")],
                    [InlineKeyboardButton("🔄 تایید مجدد عضویت", callback_data="check_join")]
                ])
            )
        return

    # Check force join for all other buttons
    joined = await check_force_join(user_id, context)
    if not joined:
        await show_force_join_message(update, context, is_callback=True)
        return

    # Restrict unapproved users from normal player actions
    # But allow admins/owners to access administrative and owner callbacks
    is_admin_action = data in [
        "btn_admin_panel", "admin_game_stats", "admin_recent_logs", "admin_city_requests", "admin_give_resources"
    ] or data.startswith("adm_app_") or data.startswith("adm_rej_")
    
    is_owner_action = data in [
        "btn_owner_panel", "owner_list_admins", "owner_add_admin", "owner_remove_admin", "owner_set_channel"
    ]

    # Clean and strict role checks
    if is_admin_action and not is_admin(user_id):
        await answer_query("❌ شما دسترسی ادمین به این بخش را ندارید!", show_alert=True)
        return
    if is_owner_action and not is_owner(user_id):
        await answer_query("❌ شما دسترسی مالک به این بخش را ندارید!", show_alert=True)
        return

    # Regular player actions restriction
    if not is_admin_action and not is_owner_action:
        if not player:
            await query.edit_message_text(
                "⚠️ اطلاعات شما یافت نشد. لطفاً ابتدا ربات را مجدداً راه‌اندازی کنید.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔄 راه اندازی", callback_data="check_join")]])
            )
            return

        if player["status"] != 'approved':
            status = player["status"]
            if status == "pending_name":
                await query.edit_message_text(
                    "✍️ لطفاً نام پیشنهادی شهر خود را به صورت یک پیام متنی معمولی ارسال فرمایید (بین ۳ تا ۲۰ کاراکتر):"
                )
            elif status == "pending_approval":
                escaped_city_name = escape_markdown(player['city_name'])
                await query.edit_message_text(
                    f"⏳ *نام انتخابی شما: «{escaped_city_name}» هم‌اکنون در صف بررسی قرار دارد.*\n\n"
                    f"به محض تایید یا رد درخواست توسط ادمین، نتیجه به شما اعلام خواهد شد.",
                    parse_mode="Markdown"
                )
            elif status == "rejected":
                await query.edit_message_text(
                    "❌ *نام شهر قبلی شما مورد تایید فرمانداری قرار نگرفت.*\n\n"
                    "✍️ لطفاً نام مناسب و زیباتر دیگری را به صورت یک پیام متنی معمولی ارسال فرمایید (بین ۳ تا ۲۰ کاراکتر):",
                    parse_mode="Markdown"
                )
            else:
                await query.edit_message_text(
                    f"⏳ شهر شما هنوز تایید نشده است. وضعیت فعلی شما: *«{player['status']}»*\n"
                    f"لطفاً منتظر بررسی ادمین باشید.",
                    parse_mode="Markdown"
                )
            return

    # Clear any ongoing wizards if the user is navigating to a main menu option
    if data in [
        "menu_main", "btn_city_stats", "btn_build_menu", "btn_collect_resources",
        "btn_daily_reward", "btn_trade_menu", "btn_daily_missions", "btn_leaderboard",
        "btn_admin_panel", "btn_owner_panel", "btn_diplomacy_menu", "btn_research_menu"
    ]:
        USER_STATES.pop(user_id, None)

    # Return to Main Menu
    if data == "menu_main":
        await query.edit_message_text(
            f"🎖️ *مرکز فرماندهی شهر «{escape_markdown(player['city_name'])}»*\n\n"
            f"جهت مدیریت منابع، ارتقا تالار شهر و نظارت بر کارگاه‌ها از دکمه‌های زیر استفاده کنید:",
            reply_markup=get_main_menu_keyboard(user_id),
            parse_mode="Markdown"
        )
        return

    # 1. My City Stats & Buildings
    elif data == "btn_city_stats":
        # Fresh calculate of resources
        process_offline_production(user_id)
        
        buildings = get_buildings(user_id)
        rates = calculate_production_rates(buildings, user_id)
        player = get_player(user_id) # re-fetch to get accurate balances
        
        # Build buildings list
        b_list_text = ""
        for b in buildings:
            name = BUILDING_INFO.get(b['building_type'], {}).get('name', b['building_type'])
            b_list_text += f"🔹 {name} (شناسه #{b['id']} | سطح {b['level']})\n"

        capacity = get_storage_capacity_for_player(user_id)
        max_pop = get_max_population_for_player(user_id)
        
        # Determine happiness status
        hap = int(player.get("happiness", 100))
        if hap >= 80:
            hap_status = "😀 بسیار راضی"
        elif hap >= 50:
            hap_status = "😐 معمولی"
        else:
            hap_status = "😡 ناراضی و گرسنه"

        # Apply happiness modifier exactly like offline production
        hap_mod = get_happiness_modifier(hap)

        maint_rate = calculate_building_maintenance_rate(buildings, user_id)
        net_gold_rate = (rates['gold'] * hap_mod) - maint_rate

        status_text = (
            f"🏛️ *آمار و وضعیت شهر «{escape_markdown(player['city_name'])}»*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"👥 *مشخصات شهروندان:*\n"
            f"👨‍👩‍👧‍👦 جمعیت: `{int(player.get('population', 10))}` از `{max_pop}` نفر\n"
            f"😊 رضایت عمومی: `{hap}%` ({hap_status})\n\n"
            f"🏦 *موجودی انبارها:*\n"
            f"🪙 طلا: `{player['gold']:,}` سکه (نامحدود)\n"
            f"🪵 چوب: `{player['wood']:,}` / `{capacity:,}` واحد\n"
            f"🪨 سنگ: `{player['stone']:,}` / `{capacity:,}` واحد\n"
            f"🌾 غذا: `{player['food']:,}` / `{capacity:,}` واحد\n\n"
            f"📈 *نرخ تولید خودکار (در ساعت):*\n"
            f"🪙 طلا: `+{net_gold_rate:.1f}/h` (تولید: `+{rates['gold'] * hap_mod:.1f}/h` | نگهداری: `-{maint_rate:.1f}/h`)\n"
            f"🪵 چوب: `+{rates['wood'] * hap_mod:.1f}/h` | 🪨 سنگ: `+{rates['stone'] * hap_mod:.1f}/h` | 🌾 غذا: `+{rates['food'] * hap_mod:.1f}/h`\n\n"
            f"🧱 *بناهای ساخته شده در شهر:*\n"
            f"{b_list_text}\n"
            f"━━━━━━━━━━━━━━━━━━━━"
        )
        keyboard = [
            [
                InlineKeyboardButton("📜 تاریخچه رویدادها", callback_data="btn_game_logs"),
                InlineKeyboardButton("📊 گزارش جامع شهر", callback_data="btn_city_report")
            ],
            [
                InlineKeyboardButton("📦 مدیریت انبارها", callback_data="btn_storage_mgmt"),
                InlineKeyboardButton("🎁 ارسال هدیه مستقیم", callback_data="trade_transfer_direct")
            ],
            [InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")]
        ]
        await query.edit_message_text(status_text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    elif data == "btn_game_logs":
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM game_logs WHERE player_id = ? ORDER BY id DESC LIMIT 15", (user_id,))
        logs = c.fetchall()
        conn.close()
        
        log_text = "📜 *تاریخچه رویدادهای شهر شما*\n━━━━━━━━━━━━━━━━━━━━\n"
        if not logs:
            log_text += "هیچ رویدادی هنوز ثبت نشده است."
        else:
            for l in logs:
                time_str = l["created_at"].split("T")[1][:5] if "T" in l["created_at"] else l["created_at"]
                log_text += f"🕒 `{time_str}` | {l['message']}\n"
        log_text += "\n━━━━━━━━━━━━━━━━━━━━"
        await query.edit_message_text(log_text, reply_markup=get_back_button("btn_city_stats"), parse_mode="Markdown")

    elif data == "btn_storage_mgmt":
        player = get_player(user_id)
        capacity = get_storage_capacity_for_player(user_id)
        
        buildings = get_buildings(user_id)
        town_halls = [b for b in buildings if b["building_type"] == "town_hall"]
        warehouses = [b for b in buildings if b["building_type"] == "warehouse"]
        
        th_level = sum(b["level"] for b in town_halls) if town_halls else 1
        wh_level = sum(b["level"] for b in warehouses) if warehouses else 0
        
        wood_pct = min(100.0, (player["wood"] / capacity) * 100.0) if capacity > 0 else 0
        stone_pct = min(100.0, (player["stone"] / capacity) * 100.0) if capacity > 0 else 0
        food_pct = min(100.0, (player["food"] / capacity) * 100.0) if capacity > 0 else 0
        
        storage_text = (
            f"📦 *مدیریت انبارها و مخازن ذخیره‌سازی*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"📊 *ظرفیت کل انبارها:* `{capacity:,}` واحد\n"
            f"ℹ️ *نحوه محاسبه:* ۱۰,۰۰۰ پایه + `{th_level}` سطح تالار شهر (هر سطح ۵,۰۰۰+) + `{wh_level}` سطح انبار کالا (هر سطح ۱۰,۰۰۰+)\n\n"
            f"📈 *وضعیت پر بودن مخازن:*\n"
            f"🪵 چوب: `{player['wood']:,}` از `{capacity:,}` واحد (`{wood_pct:.1f}%`)\n"
            f"🪨 سنگ: `{player['stone']:,}` از `{capacity:,}` واحد (`{stone_pct:.1f}%`)\n"
            f"🌾 غذا: `{player['food']:,}` از `{capacity:,}` واحد (`{food_pct:.1f}%`)\n\n"
            f"🪙 طلا: `{player['gold']:,}` سکه (بدون محدودیت ذخیره‌سازی)\n\n"
            f"💡 *راهنمایی:* برای افزایش ظرفیت ذخیره‌سازی مخازن و جلوگیری از هدر رفت تولیدات، ساختمان *انبار کالا (Warehouse)* را احداث یا ارتقا دهید."
        )
        
        keyboard = [
            [InlineKeyboardButton("🏗️ احداث و ارتقای انبار کالا", callback_data="btn_build_menu")],
            [InlineKeyboardButton("🔙 بازگشت به آمار شهر", callback_data="btn_city_stats")]
        ]
        await query.edit_message_text(storage_text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    # 2. Construction / Upgrade Menu
    elif data == "btn_build_menu":
        buildings = get_buildings(user_id)
        
        # Build Keyboard for building construction/upgrades
        keyboard = []
        
        # 1. List existing buildings for upgrading
        for b in buildings:
            b_id = b["id"]
            b_type = b["building_type"]
            level = b["level"]
            b_info = BUILDING_INFO.get(b_type, {"name": b_type})
            cost = calculate_cost(b_type, level + 1, user_id)
            
            btn_text = f"🔺 ارتقا {b_info['name']} #{b_id} (سطح {level} ➔ {level+1})"
            callback_data = f"upgrade_{b_id}"
            keyboard.append([InlineKeyboardButton(btn_text, callback_data=callback_data)])
            
        # 2. List options to build new buildings (except town_hall which is unique)
        for b_type, b_info in BUILDING_INFO.items():
            count = len([b for b in buildings if b["building_type"] == b_type])
            if b_type == "town_hall" and count >= 1:
                continue
                
            cost = calculate_construction_cost(b_type, count, user_id)
            btn_text = f"🚧 احداث {b_info['name']} جدید (سطح ۱)"
            callback_data = f"construct_{b_type}"
            keyboard.append([InlineKeyboardButton(btn_text, callback_data=callback_data)])
            
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
        
        capacity = get_storage_capacity_for_player(user_id)
        build_menu_text = (
            f"🏗️ *بخش ساخت و ساز و ارتقا ابنیه*\n\n"
            f"🪙 طلای شما: `{player['gold']:,}` | 🪵 چوب: `{player['wood']:,}` | 🪨 سنگ: `{player['stone']:,}`\n"
            f"📦 ظرفیت انبارها: `{capacity:,}` واحد\n\n"
            f"روی یکی از ساختمان‌ها بزنید تا ارتقا داده شده یا احداث گردد:"
        )
        await query.edit_message_text(build_menu_text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    # Handling Upgrade Action
    elif data.startswith("upgrade_"):
        b_id = int(data.split("_")[1])
        
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM buildings WHERE id = ?", (b_id,))
        building = c.fetchone()
        conn.close()
        
        if not building:
            await query.edit_message_text("❌ ساختمان مورد نظر یافت نشد.", reply_markup=get_back_button("btn_build_menu"))
            return
            
        building = dict(building)
        b_type = building["building_type"]
        level = building["level"]
        b_info = BUILDING_INFO.get(b_type, {"name": b_type})
        cost = calculate_cost(b_type, level + 1, user_id)
        
        if deduct_player_resources_atomic(user_id, cost["gold"], cost["wood"], cost["stone"], 0):
            # Upgrade level
            upgrade_building_db(b_id)
            
            # System 4: Daily Mission progress update
            update_mission_progress(user_id, "build_upgrade", 1)
            
            # Clamp resources
            clamp_player_resources_to_capacity(user_id)
            
            # Log
            add_game_log(user_id, "upgrade", f"🔺 ارتقای {b_info['name']} #{b_id} به سطح {level+1}")
            
            # Fetch updated resources
            player = get_player(user_id)
            await query.edit_message_text(
                f"🎉 *تبریک! ساختمان {b_info['name']} با موفقیت به سطح {level+1} ارتقا یافت!*\n\n"
                f"💸 هزینه‌های کسر شده:\n"
                f"🪙 طلا: `{cost['gold']}` | 🪵 چوب: `{cost['wood']}` | 🪨 سنگ: `{cost['stone']}`\n\n"
                f"🏦 بودجه باقیمانده:\n"
                f"🪙 طلا: `{player['gold']:,}` | 🪵 چوب: `{player['wood']:,}` | 🪨 سنگ: `{player['stone']:,}`",
                reply_markup=get_back_button("btn_build_menu"),
                parse_mode="Markdown"
            )
        else:
            await query.edit_message_text(
                f"❌ *خطا! منابع کافی برای ارتقای {b_info['name']} به سطح {level+1} ندارید.*\n\n"
                f"🧱 منابع مورد نیاز:\n"
                f"🪙 طلا: `{cost['gold']}` (موجودی: `{player['gold']}`)\n"
                f"🪵 چوب: `{cost['wood']}` (موجودی: `{player['wood']}`)\n"
                f"🪨 سنگ: `{cost['stone']}` (موجودی: `{player['stone']}`)\n\n"
                f"لطفا ابتدا منابع لازم را جمع‌آوری کنید.",
                reply_markup=get_back_button("btn_build_menu"),
                parse_mode="Markdown"
            )

    # Handling Construction Action
    elif data.startswith("construct_"):
        b_type = data[len("construct_"):]
        b_info = BUILDING_INFO[b_type]
        
        buildings = get_buildings(user_id)
        count = len([b for b in buildings if b["building_type"] == b_type])
        
        if b_type == "town_hall" and count >= 1:
            await query.edit_message_text("❌ شما قبلاً تالار شهر را بنا کرده‌اید و ساخت بیش از یک مورد مجاز نیست.", reply_markup=get_back_button("btn_build_menu"))
            return
            
        cost = calculate_construction_cost(b_type, count, user_id)
        
        if deduct_player_resources_atomic(user_id, cost["gold"], cost["wood"], cost["stone"], 0):
            # Add new building
            add_building(user_id, b_type)
            
            # System 4: Daily Mission progress update
            update_mission_progress(user_id, "build_upgrade", 1)
            
            # Clamp resources
            clamp_player_resources_to_capacity(user_id)
            
            # Log
            add_game_log(user_id, "build", f"🏗️ احداث {b_info['name']} جدید در شهر")
            
            # Fetch updated resources
            player = get_player(user_id)
            await query.edit_message_text(
                f"🎉 *تبریک! ساختمان {b_info['name']} با موفقیت در شهر شما احداث شد!*\n\n"
                f"💸 هزینه‌های کسر شده:\n"
                f"🪙 طلا: `{cost['gold']}` | 🪵 چوب: `{cost['wood']}` | 🪨 سنگ: `{cost['stone']}`\n\n"
                f"هم‌اکنون ظرفیت تولید منابع شما افزایش یافته است.",
                reply_markup=get_back_button("btn_build_menu"),
                parse_mode="Markdown"
            )
        else:
            await query.edit_message_text(
                f"❌ *منابع کافی برای احداث {b_info['name']} جدید ندارید.*\n\n"
                f"🧱 منابع مورد نیاز:\n"
                f"🪙 طلا: `{cost['gold']}` (موجودی: `{player['gold']}`)\n"
                f"🪵 چوب: `{cost['wood']}` (موجودی: `{player['wood']}`)\n"
                f"🪨 سنگ: `{cost['stone']}` (موجودی: `{player['stone']}`)",
                reply_markup=get_back_button("btn_build_menu"),
                parse_mode="Markdown"
            )

    # 3. Collect Resources
    elif data == "btn_collect_resources":
        g, w, s, f, hrs = process_offline_production(user_id)
        if hrs < 0.01:
            await query.edit_message_text(
                "🌾 *انبارها هنوز خالی هستند!*\n\n"
                "کارگران در حال کار هستند. لطفا دقایقی دیگر برای جمع‌آوری محصول تلاش کنید.",
                reply_markup=get_back_button(),
                parse_mode="Markdown"
            )
        else:
            buildings = get_buildings(user_id)
            maint_rate = calculate_building_maintenance_rate(buildings, user_id)
            maint_cost = int(maint_rate * hrs)
            gross_gold = max(0, g + maint_cost)
            
            await query.edit_message_text(
                f"🌾 *محصولات کارگاه‌ها با موفقیت برداشت شد!*\n\n"
                f"⏱️ زمان فعالیت کارگران: `{hrs:.1f}` ساعت\n\n"
                f"🎁 اقلام افزوده شده به انبار شهر:\n"
                f"🪙 طلا (خالص): `{g:+,}` سکه (تولید: `+{gross_gold}` | هزینه نگهداری: `-{maint_cost}`)\n"
                f"🪵 چوب: `+{w}` واحد\n"
                f"🪨 سنگ: `+{s}` واحد\n"
                f"🌾 غذا: `+{f}` واحد\n\n"
                f"کارگران مجدداً چرخه جدید تولید را آغاز کردند!",
                reply_markup=get_back_button(),
                parse_mode="Markdown"
            )

    # 4. Daily Reward
    elif data == "btn_daily_reward":
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        try:
            c.execute("BEGIN IMMEDIATE")
            c.execute("SELECT last_daily, gold, wood, stone FROM players WHERE user_id = ?", (user_id,))
            p_row = c.fetchone()
            if not p_row:
                await query.edit_message_text("❌ خطا: حساب شما یافت نشد.", reply_markup=get_back_button())
                return
                
            now = datetime.now()
            can_claim = True
            last_daily_str = p_row["last_daily"]
            if last_daily_str:
                last_daily = datetime.fromisoformat(last_daily_str)
                if now - last_daily < timedelta(days=1):
                    can_claim = False
                    time_rem = timedelta(days=1) - (now - last_daily)
                    hours_rem = int(time_rem.total_seconds() / 3600)
                    minutes_rem = int((time_rem.total_seconds() % 3600) / 60)
                    
            if can_claim:
                c.execute(
                    """UPDATE players 
                       SET gold = gold + 500, wood = wood + 200, stone = stone + 100, last_daily = ? 
                       WHERE user_id = ?""",
                    (now.isoformat(), user_id)
                )
                conn.commit()
                
                # Log and clamp resources
                add_game_log(user_id, "daily", "🎁 دریافت پاداش روزانه: ۵۰۰ سکه، ۲۰۰ چوب، ۱۰۰ سنگ")
                clamp_player_resources_to_capacity(user_id)
                
                await query.edit_message_text(
                    "🎁 *تبریک! پاداش روزانه فرماندهی به شما اعطا شد!*\n\n"
                    "📦 محتویات صندوق پاداش:\n"
                    "🪙 طلا: `+500` سکه\n"
                    "🪵 چوب: `+200` واحد\n"
                    "🪨 سنگ: `+100` واحد\n\n"
                    "فردا همین موقع برای صندوق پاداش بعدی سر بزنید!",
                    reply_markup=get_back_button(),
                    parse_mode="Markdown"
                )
            else:
                await query.edit_message_text(
                    f"⏳ *شما پاداش امروز خود را قبلاً دریافت کرده‌اید!*\n\n"
                    f"فرمانده عزیز، لطفا `{hours_rem}` ساعت و `{minutes_rem}` دقیقه دیگر مجددا تلاش فرمایید.",
                    reply_markup=get_back_button(),
                    parse_mode="Markdown"
                )
        except Exception as e:
            conn.rollback()
            logger.error(f"Error claiming daily reward for {user_id}: {e}")
            await query.edit_message_text("❌ خطا در دریافت پاداش روزانه.", reply_markup=get_back_button())
        finally:
            conn.close()

    # 4.5. Daily Missions & City Report
    elif data == "btn_daily_missions":
        missions = get_or_create_daily_missions(user_id)
        text = "🎯 *ماموریت‌های روزانه دهکده*\n"
        text += "هر روز ماموریت‌های جدیدی برای هدایت و رونق شهر دریافت می‌کنید. انجام هر کدام پاداش خاصی دارد!\n━━━━━━━━━━━━━━━━━━━━\n\n"
        
        keyboard = []
        for m in missions:
            m_type = m["mission_type"]
            progress = m["progress"]
            target = m["target"]
            reward_res = m["reward_res"]
            reward_amt = m["reward_amt"]
            claimed = m["claimed"]
            
            res_icon = {"gold": "🪙", "wood": "🪵", "stone": "🪨", "food": "🌾"}.get(reward_res, "🎁")
            res_name = {"gold": "طلا", "wood": "چوب", "stone": "سنگ", "food": "غذا"}.get(reward_res, reward_res)
            
            m_title = {
                "collect": "🌾 جمع‌آوری درآمد انبار",
                "build_upgrade": "🏗️ ساخت یا ارتقای یک ساختمان",
                "trade": "🤝 ثبت یک پیشنهاد تجاری در بازار",
                "transfer": "💸 ارسال هدیه مستقیم به دیگران"
            }.get(m_type, m_type)
            
            status_symbol = "✅" if claimed else ("🎁" if progress >= target else "⏳")
            
            text += f"{status_symbol} *{m_title}*\n"
            text += f"🔹 پیشرفت: `{progress}` از `{target}` | پاداش: `{reward_amt}` {res_icon} {res_name}\n"
            if claimed:
                text += "✅ *پاداش این ماموریت دریافت شده است.*\n\n"
            elif progress >= target:
                text += "🎉 *ماموریت انجام شده! برای دریافت پاداش دکمه زیر را کلیک کنید.*\n\n"
                keyboard.append([InlineKeyboardButton(f"🎁 دریافت پاداش: {m_title}", callback_data=f"claim_mission_{m_type}")])
            else:
                text += "⏳ در حال انجام...\n\n"
                
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    # 4.6. Research Menu
    elif data == "btn_research_menu":
        techs = get_player_technologies(user_id)
        text = "🔬 *آزمایشگاه و مرکز تحقیقات تکنولوژی*\n"
        text += "با ارتقای فناوری‌های مختلف، کارایی تولید، سرعت ساخت، ظرفیت انبارها و روابط دیپلماتیک خود را تقویت کنید!\n"
        text += "━━━━━━━━━━━━━━━━━━━━\n\n"
        
        keyboard = []
        for t_key, t_info in TECH_INFO.items():
            lvl = techs.get(t_key, 0)
            cost = get_tech_upgrade_cost(t_key, lvl)
            
            text += f"🔹 *{t_info['name']}* (سطح {lvl})\n"
            text += f"💡 {t_info['desc']}\n"
            text += f"✨ اثر فعلی: `{lvl * t_info['bonus_per_level'] * 100:.0f}%` بهبود\n"
            
            if cost:
                cost_text = f"🪙 {cost['gold']} طلا | 🪵 {cost['wood']} چوب | 🪨 {cost['stone']} سنگ"
                text += f"🧱 هزینه ارتقا به سطح {lvl+1}:\n   `{cost_text}`\n\n"
                keyboard.append([InlineKeyboardButton(f"🔬 ارتقا {t_info['name']} (سطح {lvl+1})", callback_data=f"upgtech_{t_key}")])
            else:
                text += "✅ *این فناوری به حداکثر سطح خود رسیده است.*\n\n"
                
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    elif data.startswith("upgtech_"):
        t_key = data[len("upgtech_"):]
        techs = get_player_technologies(user_id)
        lvl = techs.get(t_key, 0)
        
        success, msg = upgrade_technology(user_id, t_key)
        if success:
            update_mission_progress(user_id, "build_upgrade", 1)
            await answer_query("🔬 فناوری مورد نظر با موفقیت ارتقا یافت!")
            
            techs = get_player_technologies(user_id)
            text = "🔬 *آزمایشگاه و مرکز تحقیقات تکنولوژی*\n"
            text += "با ارتقای فناوری‌های مختلف، کارایی تولید، سرعت ساخت، ظرفیت انبارها و روابط دیپلماتیک خود را تقویت کنید!\n"
            text += "━━━━━━━━━━━━━━━━━━━━\n\n"
            
            keyboard = []
            for k, t_info in TECH_INFO.items():
                l = techs.get(k, 0)
                cost = get_tech_upgrade_cost(k, l)
                text += f"🔹 *{t_info['name']}* (سطح {l})\n"
                text += f"💡 {t_info['desc']}\n"
                text += f"✨ اثر فعلی: `{l * t_info['bonus_per_level'] * 100:.0f}%` بهبود\n"
                if cost:
                    cost_text = f"🪙 {cost['gold']} طلا | 🪵 {cost['wood']} چوب | 🪨 {cost['stone']} سنگ"
                    text += f"🧱 هزینه ارتقا به سطح {l+1}:\n   `{cost_text}`\n\n"
                    keyboard.append([InlineKeyboardButton(f"🔬 ارتقا {t_info['name']} (سطح {l+1})", callback_data=f"upgtech_{k}")])
                else:
                    text += "✅ *این فناوری به حداکثر سطح خود رسیده است.*\n\n"
                    
            keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
            await query.edit_message_text(f"✅ *{msg}*\n\n" + text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")
        else:
            await answer_query(f"❌ {msg}", show_alert=True)

    # 4.7. Diplomacy Menu
    elif data == "btn_diplomacy_menu":
        rep = player.get("reputation", 50.0)
        if rep is None:
            rep = 50.0
        if rep >= 90: rep_desc = "🟢 هم‌پیمان عالی‌رتبه (Holy Alliance)"
        elif rep >= 75: rep_desc = "🟢 شریک صمیمی (Close Friend)"
        elif rep >= 60: rep_desc = "🔵 دوست مطمئن (Trusted Partner)"
        elif rep >= 40: rep_desc = "🟡 بی‌طرف (Neutral)"
        elif rep >= 25: rep_desc = "🟠 بدبین (Suspicious)"
        else: rep_desc = "💀 دشمن ملی (Hostile)"
        
        filled_bars = int(rep / 10)
        bar = "🟩" * filled_bars + "⬜" * (10 - filled_bars)
        
        text = "🤝 *بخش دیپلماسی، شهرت و روابط بین‌الملل*\n"
        text += "در این بخش می‌توانید اعتبار جهانی شهر خود را مدیریت کرده و با سایر شهرداران متحد شوید.\n"
        text += "━━━━━━━━━━━━━━━━━━━━\n\n"
        
        text += f"📊 *اعتبار جهانی شما (Reputation):* `{rep:.1f}/100.0`\n"
        text += f"🎭 *وضعیت دیپلماتیک:* {rep_desc}\n"
        text += f"[{bar}]\n\n"
        
        active_evt = player.get("active_event")
        if active_evt and active_evt in EVENTS_INFO:
            evt_details = EVENTS_INFO[active_evt]
            expires_str = player.get("active_event_expires")
            rem_str = "ناشناس"
            if expires_str:
                try:
                    expires = datetime.fromisoformat(expires_str)
                    duration = expires - datetime.now()
                    rem_hours = duration.total_seconds() / 3600.0
                    if rem_hours > 0:
                        rem_str = f"`{rem_hours:.1f}` ساعت باقیمانده"
                    else:
                        rem_str = "در حال اتمام"
                except Exception:
                    pass
            text += f"🌍 *رویداد فعال در شهر شما:*\n"
            text += f"🚨 {evt_details['name']} - {evt_details['desc']}\n"
            text += f"⏳ زمان باقیمانده: {rem_str}\n\n"
        else:
            text += "🌍 *وضعیت جوی جهانی:* آسمان صاف و شرایط آرام است.\n\n"
            
        relations = get_diplomacy_relations(user_id)
        
        pending_received = [r for r in relations if r["status"] == "pending" and r["receiver_id"] == user_id]
        pending_sent = [r for r in relations if r["status"] == "pending" and r["sender_id"] == user_id]
        accepted_rels = [r for r in relations if r["status"] == "accepted"]
        
        keyboard = []
        
        if pending_received:
            text += "📥 *درخواست‌های دیپلماتیک دریافتی (معلق):*\n"
            for r in pending_received:
                r_type_fa = "پیمان دوستی" if r["type"] == "friendship" else "توافق تجاری"
                sender_city = r["sender_city"] or f"کاربر {r['sender_id']}"
                escaped_sender_city = escape_markdown(sender_city)
                text += f"🔸 از طرف *{escaped_sender_city}* برای *{r_type_fa}*\n"
                
                keyboard.append([
                    InlineKeyboardButton(f"✅ تایید {sender_city}", callback_data=f"dipl_acc_{r['id']}"),
                    InlineKeyboardButton(f"❌ رد", callback_data=f"dipl_rej_{r['id']}")
                ])
            text += "\n"
            
        if pending_sent:
            text += "📤 *پیشنهادهای دیپلماتیک ارسالی شما (منتظر پاسخ):*\n"
            for r in pending_sent:
                r_type_fa = "پیمان دوستی" if r["type"] == "friendship" else "توافق تجاری"
                receiver_city = r["receiver_city"] or f"کاربر {r['receiver_id']}"
                escaped_receiver_city = escape_markdown(receiver_city)
                text += f"🔹 به شهر *{escaped_receiver_city}* جهت *{r_type_fa}*\n"
            text += "\n"
            
        if accepted_rels:
            text += "📜 *روابط دیپلماتیک فعال شهر شما:*\n"
            for r in accepted_rels:
                r_type_fa = "🤝 پیمان دوستی" if r["type"] == "friendship" else "📈 توافق تجاری"
                other_city = r["receiver_city"] if r["sender_id"] == user_id else r["sender_city"]
                escaped_other_city = escape_markdown(other_city)
                text += f"✅ *{r_type_fa}* با شهر *{escaped_other_city}*\n"
            text += "\n"
            
        keyboard.append([InlineKeyboardButton("🤝 ارسال پیشنهاد دیپلماتیک جدید", callback_data="dipl_propose_new")])
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
        
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    elif data == "dipl_propose_new":
        USER_STATES[user_id] = {"state": "entering_diplomacy_target", "data": {}}
        await query.edit_message_text(
            "🤝 *ارسال پیشنهاد دیپلماتیک جدید*\n\n"
            "لطفاً آیدی عددی تلگرام یا نام کاربری (@username) شهر مقصد را ارسال نمایید تا فرستاده شما اعزام گردد:",
            reply_markup=get_back_button("btn_diplomacy_menu"),
            parse_mode="Markdown"
        )

    elif data.startswith("dipl_pro_"):
        parts = data.split("_")
        target_id = int(parts[2])
        p_type = "_".join(parts[3:])
        
        success, msg = send_diplomacy_proposal(user_id, target_id, p_type)
        if success:
            target_city = get_player(target_id)
            if target_city:
                try:
                    p_type_fa = "پیمان دوستی" if p_type == "friendship" else "توافق تجاری"
                    escaped_player_city = escape_markdown(player['city_name'])
                    await context.bot.send_message(
                        chat_id=target_id,
                        text=(
                            f"📬 *پیام دیپلماتیک جدید!*\n\n"
                            f"شهردار دهکده *«{escaped_player_city}»* پیشنهاد برقراری *«{p_type_fa}»* داده است.\n"
                            f"برای پاسخ، به بخش «🤝 امور دیپلماسی» مراجعه فرمایید."
                        ),
                        parse_mode="Markdown"
                    )
                except Exception:
                    pass
            await query.edit_message_text(f"✅ *{msg}*", reply_markup=get_back_button("btn_diplomacy_menu"), parse_mode="Markdown")
        else:
            await query.edit_message_text(f"❌ *خطا: {msg}*", reply_markup=get_back_button("btn_diplomacy_menu"), parse_mode="Markdown")

    elif data.startswith("dipl_acc_"):
        rel_id = int(data.split("_")[2])
        success, sender_id = accept_diplomacy_proposal(rel_id, user_id)
        if success:
            try:
                escaped_player_city = escape_markdown(player['city_name'])
                await context.bot.send_message(
                    chat_id=sender_id,
                    text=f"🤝 *پیمان دیپلماتیک شما با شهر «{escaped_player_city}» منعقد گردید!*",
                    parse_mode="Markdown"
                )
            except Exception:
                pass
            await query.edit_message_text("✅ پیمان دیپلماتیک با موفقیت منعقد گردید!", reply_markup=get_back_button("btn_diplomacy_menu"))
        else:
            await query.edit_message_text("❌ خطا در پذیرش پیمان یا پیمان غیرفعال شده است.", reply_markup=get_back_button("btn_diplomacy_menu"))

    elif data.startswith("dipl_rej_"):
        rel_id = int(data.split("_")[2])
        success = reject_diplomacy_proposal(rel_id, user_id)
        if success:
            await query.edit_message_text("❌ پیشنهاد دیپلماتیک با موفقیت رد شد.", reply_markup=get_back_button("btn_diplomacy_menu"))
        else:
            await query.edit_message_text("❌ خطا در لغو پیشنهاد.", reply_markup=get_back_button("btn_diplomacy_menu"))

    elif data.startswith("claim_mission_"):
        m_type = data[len("claim_mission_"):]
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        try:
            c.execute("BEGIN IMMEDIATE")
            today_str = datetime.now().date().isoformat()
            
            c.execute("""
                SELECT * FROM daily_missions 
                WHERE player_id = ? AND mission_date = ? AND mission_type = ?
            """, (user_id, today_str, m_type))
            m = c.fetchone()
            
            if not m:
                await answer_query("❌ ماموریت یافت نشد.", show_alert=True)
                return
            
            if m["claimed"] == 1:
                await answer_query("❌ پاداش این ماموریت قبلاً دریافت شده است.", show_alert=True)
                return
            
            if m["progress"] < m["target"]:
                await answer_query("❌ این ماموریت هنوز به اتمام نرسیده است.", show_alert=True)
                return
                
            # Complete claim
            reward_res = m["reward_res"]
            reward_amt = m["reward_amt"]
            
            # Mark as claimed
            c.execute("""
                UPDATE daily_missions SET claimed = 1 
                WHERE player_id = ? AND mission_date = ? AND mission_type = ?
            """, (user_id, today_str, m_type))
            
            # Add reward to player
            c.execute(f"UPDATE players SET {reward_res} = {reward_res} + ? WHERE user_id = ?", (reward_amt, user_id))
            conn.commit()
        except Exception as e:
            conn.rollback()
            logger.error(f"Error claiming daily mission reward for {user_id}: {e}")
            await answer_query("❌ خطا در ثبت دریافت پاداش ماموریت.", show_alert=True)
            return
        finally:
            conn.close()
            
        # Clamp capacity
        clamp_player_resources_to_capacity(user_id)
        
        # Log to game logs
        res_name = {"gold": "طلا", "wood": "چوب", "stone": "سنگ", "food": "غذا"}.get(reward_res, reward_res)
        add_game_log(user_id, "mission", f"🎯 دریافت پاداش ماموریت روزانه: {reward_amt} {res_name}")
        
        await answer_query(f"🎉 پاداش ماموریت روزانه دریافت شد: {reward_amt} {res_name}!", show_alert=True)
        
        # Reload missions menu
        missions = get_or_create_daily_missions(user_id)
        text = "🎯 *ماموریت‌های روزانه دهکده*\n"
        text += "هر روز ماموریت‌های جدیدی برای هدایت و رونق شهر دریافت می‌کنید. انجام هر کدام پاداش خاصی دارد!\n━━━━━━━━━━━━━━━━━━━━\n\n"
        
        keyboard = []
        for m in missions:
            m_type = m["mission_type"]
            progress = m["progress"]
            target = m["target"]
            reward_res = m["reward_res"]
            reward_amt = m["reward_amt"]
            claimed = m["claimed"]
            
            res_icon = {"gold": "🪙", "wood": "🪵", "stone": "🪨", "food": "🌾"}.get(reward_res, "🎁")
            res_name = {"gold": "طلا", "wood": "چوب", "stone": "سنگ", "food": "غذا"}.get(reward_res, reward_res)
            
            m_title = {
                "collect": "🌾 جمع‌آوری درآمد انبار",
                "build_upgrade": "🏗️ ساخت یا ارتقای یک ساختمان",
                "trade": "🤝 ثبت یک پیشنهاد تجاری در بازار",
                "transfer": "💸 ارسال هدیه مستقیم به دیگران"
            }.get(m_type, m_type)
            
            status_symbol = "✅" if claimed else ("🎁" if progress >= target else "⏳")
            
            text += f"{status_symbol} *{m_title}*\n"
            text += f"🔹 پیشرفت: `{progress}` از `{target}` | پاداش: `{reward_amt}` {res_icon} {res_name}\n"
            if claimed:
                text += "✅ *پاداش این ماموریت دریافت شده است.*\n\n"
            elif progress >= target:
                text += "🎉 *ماموریت انجام شده! برای دریافت پاداش دکمه زیر را کلیک کنید.*\n\n"
                keyboard.append([InlineKeyboardButton(f"🎁 دریافت پاداش: {m_title}", callback_data=f"claim_mission_{m_type}")])
            else:
                text += "⏳ در حال انجام...\n\n"
                
        keyboard.append([InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")])
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    elif data == "btn_city_report":
        # Ensure fresh offline production calculations are applied
        process_offline_production(user_id)
        
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        
        # Get player details
        c.execute("SELECT * FROM players WHERE user_id = ?", (user_id,))
        player = c.fetchone()
        
        # Get buildings count and total levels
        c.execute("SELECT building_type, COUNT(*) as cnt, SUM(level) as sum_lvl FROM buildings WHERE user_id = ? GROUP BY building_type", (user_id,))
        b_rows = c.fetchall()
        
        # Get recent 5 logs
        c.execute("SELECT * FROM game_logs WHERE player_id = ? ORDER BY id DESC LIMIT 5", (user_id,))
        logs = c.fetchall()
        conn.close()
        
        player = dict(player)
        buildings = get_buildings(user_id)
        rates = calculate_production_rates(buildings, user_id)
        maint_rate = calculate_building_maintenance_rate(buildings, user_id)
        hap_pressure_rate = calculate_happiness_pressure_rate(buildings)
        
        hap = int(player["happiness"])
        hap_mod = get_happiness_modifier(hap)
        net_gold_rate = (rates['gold'] * hap_mod) - maint_rate
        
        capacity = get_storage_capacity_for_player(user_id)
        max_pop = get_max_population_for_player(user_id)
        
        # Happiness status
        hap = int(player["happiness"])
        if hap >= 80:
            hap_status = "😀 بسیار راضی"
        elif hap >= 50:
            hap_status = "😐 معمولی"
        else:
            hap_status = "😡 ناراضی"
            
        b_summary = ""
        for b in b_rows:
            b_type = b["building_type"]
            cnt = b["cnt"]
            sum_lvl = b["sum_lvl"]
            name = BUILDING_INFO.get(b_type, {}).get("name", b_type)
            b_summary += f"▫️ {name}: `{cnt}` عدد (مجموع سطح: `{sum_lvl}`)\n"
        if not b_summary:
            b_summary = "هیچ ساختمانی ساخته نشده است.\n"
            
        logs_summary = ""
        for l in logs:
            time_str = l["created_at"].split("T")[1][:5] if "T" in l["created_at"] else l["created_at"]
            logs_summary += f"🕒 `{time_str}` | {l['message']}\n"
        if not logs_summary:
            logs_summary = "هیچ رویداد اخیری ثبت نشده است.\n"
            
        # Daily missions summary
        missions = get_or_create_daily_missions(user_id)
        mission_summary = ""
        completed_missions = 0
        for m in missions:
            m_type = m["mission_type"]
            progress = m["progress"]
            target = m["target"]
            claimed = m["claimed"]
            
            m_title = {
                "collect": "جمع‌آوری",
                "build_upgrade": "ساخت و ساز",
                "trade": "ثبت معامله",
                "transfer": "ارسال هدیه"
            }.get(m_type, m_type)
            
            status_char = "✅" if claimed else ("🎁" if progress >= target else "⏳")
            if progress >= target:
                completed_missions += 1
            mission_summary += f"{status_char} {m_title}: `{progress}`/`{target}`\n"
            
        # Warning section (low happiness, full storage, food shortage, low gold, low happiness trend)
        warnings = []
        if hap < 50:
            warnings.append("⚠️ *نارضایتی عمومی:* رضایت شهروندان به شدت پایین است! احتمال کاهش جمعیت و کاهش راندمان تولید وجود دارد.")
        
        # Check food consumption and shortage
        hourly_food_consumption = player["population"] * 0.5
        if player["food"] < hourly_food_consumption * 3: # less than 3 hours food left
            warnings.append("⚠️ *قحطی غذا:* انبار غلات خالی یا بسیار کم است! به زودی شهروندان دچار گرسنگی و مرگ‌ومیر خواهند شد.")
            
        # Check storage capacity warnings
        storage_filled = []
        if player["wood"] >= capacity:
            storage_filled.append("🪵 چوب")
        if player["stone"] >= capacity:
            storage_filled.append("🪨 سنگ")
        if player["food"] >= capacity:
            storage_filled.append("🌾 غذا")
        if storage_filled:
            warnings.append(f"⚠️ *تکمیل ظرفیت انبار:* مخازن {', '.join(storage_filled)} کاملاً پر شده و تولیدات جدید هدر می‌روند! ظرفیت انبارها را افزایش دهید.")
            
        # Check gold maintenance failure threat
        if player["gold"] < maint_rate * 6:
            warnings.append("⚠️ *تهدید مالی:* طلای کافی برای پرداخت هزینه‌های نگهداری ابنیه در ساعت‌های پیش‌رو را ندارید!")

        # Check happiness pressure threat
        has_food = player["food"] > 0
        base_hap_change = 2.0 if has_food else -5.0
        net_hap_change = base_hap_change - hap_pressure_rate
        if net_hap_change < 0:
            warnings.append(f"⚠️ *کاهش رضایت عمومی:* آلایندگی صنایع و شلوغی بیش از حد شهر بیشتر از رفاه است (کاهش تدریجی `{abs(net_hap_change * 24):.1f}%` رضایت در روز). برای افزایش روحیه مزارع یا تالار شهر را ارتقا دهید.")
            
        warnings_text = ""
        if warnings:
            warnings_text = "\n🚨 *هشدارهای مدیریتی و بحران‌ها:*\n" + "\n".join(warnings) + "\n"
        else:
            warnings_text = "\n🟢 *وضعیت شهر پایدار:* همه سیستم‌ها نرمال هستند و بحرانی شهر را تهدید نمی‌کند.\n"
            
        status_label = "تایید شده و رسمی" if player["status"] == "approved" else "معلق"
        
        report_text = (
            f"📊 *گزارش جامع شهرداری شهر «{escape_markdown(player['city_name'])}»*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"📍 *اطلاعات کلی فرمانروایی:*\n"
            f"🏛️ وضعیت رسمی شهر: `{status_label}`\n"
            f"👥 جمعیت فعلی: `{int(player['population'])}` از `{max_pop}` نفر\n"
            f"😊 شاخص رضایت: `{hap}%` ({hap_status})\n"
            f"📦 گنجایش انبار: `{capacity:,}` واحد\n\n"
            f"🏦 *موجودی انبارها:*\n"
            f"🪙 طلا: `{player['gold']:,}` سکه (نامحدود)\n"
            f"🪵 چوب: `{player['wood']:,}` / `{capacity:,}` واحد\n"
            f"🪨 سنگ: `{player['stone']:,}` / `{capacity:,}` واحد\n"
            f"🌾 غذا: `{player['food']:,}` / `{capacity:,}` واحد\n\n"
            f"📈 *نرخ تولید خالص منابع (در ساعت):*\n"
            f"🪙 طلا: `+{net_gold_rate:.1f}/h` (تولید: `+{rates['gold'] * hap_mod:.1f}/h` | نگهداری: `-{maint_rate:.1f}/h`)\n"
            f"😊 تغییر رضایت: `{net_hap_change:+.1f}/h` (تغذیه: `{base_hap_change:+.1f}/h` | آلایندگی ابنیه: `-{hap_pressure_rate:.1f}/h`)\n"
            f"🪵 چوب: `+{rates['wood'] * hap_mod:.1f}/h`\n"
            f"🪨 سنگ: `+{rates['stone'] * hap_mod:.1f}/h`\n"
            f"🌾 غذا: `+{rates['food'] * hap_mod - hourly_food_consumption:.1f}/h`\n\n"
            f"🧱 *خلاصه وضعیت ابنیه و ساختمان‌ها:*\n"
            f"{b_summary}\n"
            f"🎯 *ماموریت‌های روزانه فعال امروز:*\n"
            f"{mission_summary}"
            f"{warnings_text}\n"
            f"📜 *رویدادهای امنیتی و عمومی اخیر شهر:*\n"
            f"{logs_summary}"
            f"━━━━━━━━━━━━━━━━━━━━"
        )
        
        await query.edit_message_text(
            report_text,
            reply_markup=get_back_button("btn_city_stats"),
            parse_mode="Markdown"
        )

    # 5. Leaderboard
    elif data == "btn_leaderboard":
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT city_name, gold, wood, stone FROM players WHERE status = 'approved' ORDER BY gold DESC LIMIT 5")
        leaders = c.fetchall()
        conn.close()
        
        lead_text = "🏆 *جدول برترین شهرهای جهان*\n━━━━━━━━━━━━━━━━━━━━\n"
        for idx, l in enumerate(leaders):
            medal = "🥇" if idx == 0 else "🥈" if idx == 1 else "🥉" if idx == 2 else "🎖️"
            escaped_name = escape_markdown(l['city_name'])
            lead_text += f"{medal} *{escaped_name}* - ثروت: `{l['gold']:,}` طلا\n"
            
        lead_text += "\nشهر خود را گسترش دهید تا نام شما در صدر جدول بدرخشد!"
        await query.edit_message_text(lead_text, reply_markup=get_back_button(), parse_mode="Markdown")

    # 6. TRADE SYSTEM MENUS
    elif data == "btn_trade_menu":
        USER_STATES.pop(user_id, None) # Clear any wizard
        keyboard = [
            [
                InlineKeyboardButton("📥 پیشنهادات بازار", callback_data="trade_browse_0"),
                InlineKeyboardButton("➕ ثبت پیشنهاد جدید", callback_data="trade_create")
            ],
            [
                InlineKeyboardButton("💸 ارسال مستقیم هدیه", callback_data="trade_transfer_direct"),
                InlineKeyboardButton("📦 معاملات فعال من", callback_data="trade_my_offers")
            ],
            [InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")]
        ]
        await query.edit_message_text(
            "🤝 *بخش داد و ستد و تجارت بین‌المللی*\n\n"
            "به مجمع شهرهای تجاری خوش آمدید. در اینجا می‌توانید با دیگر بازیکنان داد و ستد کنید، هدیه بفرستید، و مازاد انبار خود را به نقدینگی تبدیل نمایید.",
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode="Markdown"
        )

    elif data == "trade_create":
        keyboard = [
            [
                InlineKeyboardButton("🪙 طلا", callback_data="sellres_gold"),
                InlineKeyboardButton("🪵 چوب", callback_data="sellres_wood")
            ],
            [
                InlineKeyboardButton("🪨 سنگ", callback_data="sellres_stone"),
                InlineKeyboardButton("🌾 غذا", callback_data="sellres_food")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="btn_trade_menu")]
        ]
        await query.edit_message_text(
            "➕ *ثبت پیشنهاد فروش جدید*\n\n"
            "ابتدا انتخاب کنید مایل به عرضه کدام منبع خود در بازار هستید:",
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode="Markdown"
        )

    elif data.startswith("sellres_"):
        res = data.split("_")[1]
        USER_STATES[user_id] = {"state": "entering_trade_sell_amount", "data": {"sell_resource": res}}
        await query.edit_message_text(
            f"➕ *عرضه {res}*\n\n"
            f"موجودی فعلی شما: `{player[res]:,}`\n\n"
            f"✍️ لطفاً مقداری که می‌خواهید برای فروش بگذارید را به صورت یک عدد ارسال کنید:"
        )

    elif data.startswith("buyres_"):
        res = data.split("_")[1]
        state_info = USER_STATES.get(user_id)
        if not state_info:
            await query.edit_message_text("❌ خطا! دوباره تلاش کنید.", reply_markup=get_back_button("btn_trade_menu"))
            return
        data_dict = state_info["data"]
        data_dict["buy_resource"] = res
        USER_STATES[user_id] = {"state": "entering_trade_buy_amount", "data": data_dict}
        await query.edit_message_text(
            f"➕ *ثبت پیشنهاد جدید*\n\n"
            f"منبع فروش: `{data_dict['sell_amount']}` {data_dict['sell_resource']}\n"
            f"منبع درخواستی: {res}\n\n"
            f"✍️ حالا تعیین کنید در قبال آن مایلید چند واحد {res} دریافت کنید (عدد ارسال کنید):"
        )

    elif data.startswith("trade_browse_"):
        page = int(data.split("_")[2])
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute(
            """SELECT t.*, p.city_name 
               FROM market_trades t 
               JOIN players p ON t.seller_id = p.user_id 
               WHERE t.status = 'open' AND t.seller_id != ? 
               ORDER BY t.id DESC""",
            (user_id,)
        )
        trades = [dict(r) for r in c.fetchall()]
        conn.close()

        if not trades:
            await query.edit_message_text(
                "📥 *بازارچه تجاری*\n\n"
                "در حال حاضر هیچ پیشنهاد خرید/فروش فعالی از دیگر شهردارها در بازار ثبت نشده است.",
                reply_markup=get_back_button("btn_trade_menu"),
                parse_mode="Markdown"
            )
            return

        # Simple Pagination (3 per page)
        per_page = 3
        total_pages = (len(trades) + per_page - 1) // per_page
        sliced = trades[page * per_page : (page + 1) * per_page]

        text = f"📥 *پیشنهادات موجود در بازار (صفحه {page+1} از {total_pages})*\n━━━━━━━━━━━━━━━━━━━━\n"
        keyboard = []
        for t in sliced:
            text += (
                f"🆔 شناسه: `{t['id']}`\n"
                f"🏰 فروشنده: *«{escape_markdown(t['city_name'])}»*\n"
                f"🤝 معامله: عرضه `{t['sell_amount']:,}` {t['sell_resource']} 🔀 خواستار `{t['buy_amount']:,}` {t['buy_resource']}\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
            )
            keyboard.append([InlineKeyboardButton(f"🤝 معامله #{t['id']}", callback_data=f"trade_accept_{t['id']}")])

        # Pagination controls
        nav_buttons = []
        if page > 0:
            nav_buttons.append(InlineKeyboardButton("◀️ قبلی", callback_data=f"trade_browse_{page-1}"))
        if page < total_pages - 1:
            nav_buttons.append(InlineKeyboardButton("بعدی ▶️", callback_data=f"trade_browse_{page+1}"))
        if nav_buttons:
            keyboard.append(nav_buttons)

        keyboard.append([InlineKeyboardButton("🔙 بازگشت به تجارت", callback_data="btn_trade_menu")])
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")

    elif data.startswith("trade_accept_"):
        trade_id = int(data.split("_")[2])
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        try:
            c.execute("BEGIN IMMEDIATE")
            # 1. Fetch trade details inside transaction with status check
            c.execute("SELECT * FROM market_trades WHERE id = ? AND status = 'open'", (trade_id,))
            trade = c.fetchone()
            if not trade:
                await query.edit_message_text("❌ معامله مورد نظر منقضی شده، لغو شده یا هم‌اکنون به اتمام رسیده است.", reply_markup=get_back_button("btn_trade_menu"))
                return

            trade = dict(trade)
            buy_res = trade["buy_resource"]
            buy_amt = trade["buy_amount"]
            sell_res = trade["sell_resource"]
            sell_amt = trade["sell_amount"]
            seller_id = trade["seller_id"]

            if buy_res not in ["gold", "wood", "stone", "food"] or sell_res not in ["gold", "wood", "stone", "food"]:
                await query.edit_message_text("❌ خطا: منابع معامله نامعتبر هستند.", reply_markup=get_back_button("btn_trade_menu"))
                return

            if seller_id == user_id:
                await query.edit_message_text("❌ شما نمی‌توانید معامله خودتان را بپذیرید!", reply_markup=get_back_button("btn_trade_menu"))
                return

            # 2. Recheck buyer and seller existence/approval inside transaction
            c.execute("SELECT status, gold, wood, stone, food FROM players WHERE user_id = ?", (user_id,))
            buyer_row = c.fetchone()
            c.execute("SELECT status, gold, wood, stone, food FROM players WHERE user_id = ?", (seller_id,))
            seller_row = c.fetchone()

            if not buyer_row or buyer_row["status"] != 'approved':
                await query.edit_message_text("❌ خطا: حساب شما تایید شده نیست.", reply_markup=get_back_button("btn_trade_menu"))
                return

            if not seller_row or seller_row["status"] != 'approved':
                await query.edit_message_text("❌ خطا: حساب فروشنده معتبر و تایید شده نیست.", reply_markup=get_back_button("btn_trade_menu"))
                return

            # 3. Check buyer's actual balance inside transaction
            if int(buyer_row[buy_res]) < buy_amt:
                await query.edit_message_text(
                    f"❌ شما به اندازه کافی {buy_res} برای پذیرش این معامله ندارید!\n\n"
                    f"منبع مورد نیاز: `{buy_amt}` {buy_res}\n"
                    f"موجودی واقعی انبار شما: `{buyer_row[buy_res]}`",
                    reply_markup=get_back_button("btn_trade_menu")
                )
                return

            # 4. Atomic state change to prevent double-claiming
            c.execute("UPDATE market_trades SET status = 'completed' WHERE id = ? AND status = 'open'", (trade_id,))
            if c.rowcount == 0:
                await query.edit_message_text("❌ خطا: این معامله هم‌اکنون به اتمام رسیده یا برداشته شده است.", reply_markup=get_back_button("btn_trade_menu"))
                return

            # 5. Buyer pays buy_res and receives sell_res
            c.execute(
                f"UPDATE players SET {buy_res} = {buy_res} - ?, {sell_res} = {sell_res} + ? WHERE user_id = ?",
                (buy_amt, sell_amt, user_id)
            )

            # 6. Seller receives buy_res (they already paid sell_res on creation)
            c.execute(
                f"UPDATE players SET {buy_res} = {buy_res} + ? WHERE user_id = ?",
                (buy_amt, seller_id)
            )

            conn.commit()

            # Log both events
            add_game_log(user_id, "trade", f"🤝 خرید {sell_amt} {sell_res} در قبال پرداخت {buy_amt} {buy_res} (معامله #{trade_id})")
            add_game_log(seller_id, "trade", f"🤝 فروش {sell_amt} {sell_res} در قبال دریافت {buy_amt} {buy_res} (معامله #{trade_id})")

            # Clamp resources for both players to their storage capacity
            clamp_player_resources_to_capacity(user_id)
            clamp_player_resources_to_capacity(seller_id)

            # Update trade daily mission progress for both buyer and seller
            update_mission_progress(user_id, "trade", 1)
            update_mission_progress(seller_id, "trade", 1)

            # Notify seller
            try:
                await context.bot.send_message(
                    chat_id=seller_id,
                    text=(
                        f"🎉 *معامله شما با موفقیت انجام شد!*\n\n"
                        f"شهرداری دیگر معامله شماره `{trade_id}` شما را پذیرفت.\n"
                        f"🪙 دریافتی شما: `{buy_amt}` {buy_res}"
                    ),
                    parse_mode="Markdown"
                )
            except Exception:
                pass

            await query.edit_message_text(
                f"🎉 *معامله با موفقیت نهایی شد!*\n\n"
                f"📦 شما مقدار `{buy_amt}` {buy_res} پرداخت کردید و مقدار `{sell_amt}` {sell_res} دریافت نمودید.",
                reply_markup=get_back_button("btn_trade_menu"),
                parse_mode="Markdown"
            )
        except Exception as e:
            conn.rollback()
            logger.error(f"Error completing trade {trade_id}: {e}")
            await query.edit_message_text("❌ متاسفانه در ثبت نهایی معامله خطایی رخ داد.", reply_markup=get_back_button("btn_trade_menu"))
        finally:
            conn.close()

    elif data == "trade_transfer_direct":
        USER_STATES[user_id] = {"state": "entering_transfer_target", "data": {}}
        await query.edit_message_text(
            "🐫 *کاروان حمایتی و ارسال کمک‌های مستقیم (Caravan Aid)*\n\n"
            "فرمانده عزیز، شما می‌توانید کاروان‌های تجاری حامل کمک‌های مستقیم (طلا، چوب، سنگ یا غذا) را برای هم‌پیمانان یا دیگر شهرهای تایید شده ارسال کنید.\n\n"
            "این کاروان‌ها به سرعت حرکت کرده و مستقیماً به مخازن شهر هدف تخلیه می‌شوند.\n\n"
            "✍️ لطفاً آیدی عددی (User ID) یا نام کاربری دقیق (Username بدون @) شهر مقصد را بفرستید:"
        )

    elif data.startswith("txres_"):
        res = data.split("_")[1]
        state_info = USER_STATES.get(user_id)
        if not state_info:
            await query.edit_message_text("❌ خطا! دوباره تلاش کنید.", reply_markup=get_back_button("btn_trade_menu"))
            return
        data_dict = state_info["data"]
        data_dict["transfer_resource"] = res
        USER_STATES[user_id] = {"state": "entering_transfer_amount", "data": data_dict}
        
        res_name = {"gold": "🪙 طلا", "wood": "🪵 چوب", "stone": "🪨 سنگ", "food": "🌾 غذا"}.get(res, res)
        await query.edit_message_text(
            f"🐫 *اعزام کاروان امدادی به «شهر {data_dict['target_city']}»*\n\n"
            f"📦 محموله انتخابی: {res_name}\n"
            f"📊 موجودی فعلی انبار شما: `{player[res]:,}` واحد\n\n"
            f"✍️ لطفاً مقدار منبع ارسالی را به صورت عددی ارسال فرمایید:"
        )

    elif data == "trade_my_offers":
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM market_trades WHERE seller_id = ? AND status = 'open'", (user_id,))
        offers = [dict(r) for r in c.fetchall()]
        conn.close()

        if not offers:
            await query.edit_message_text(
                "📦 *تراکنش‌های فعال شما*\n\n"
                "شما هیچ پیشنهاد فعالی در بازارچه ندارید.",
                reply_markup=get_back_button("btn_trade_menu")
            )
            return

        text = "📦 *تراکنش‌های فعال شما در بازار*\n\n"
        keyboard = []
        for o in offers:
            text += (
                f"🔸 معامله #{o['id']}\n"
                f"عرضه: `{o['sell_amount']}` {o['sell_resource']} 🔀 درخواست: `{o['buy_amount']}` {o['buy_resource']}\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
            )
            keyboard.append([InlineKeyboardButton(f"❌ لغو معامله #{o['id']}", callback_data=f"trade_cancel_{o['id']}")])

        keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="btn_trade_menu")])
        await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

    elif data.startswith("trade_cancel_"):
        trade_id = int(data.split("_")[2])
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        try:
            c.execute("BEGIN IMMEDIATE")
            # 1. Fetch trade details inside transaction
            c.execute("SELECT * FROM market_trades WHERE id = ? AND seller_id = ? AND status = 'open'", (trade_id, user_id))
            trade = c.fetchone()
            if not trade:
                await query.edit_message_text("❌ معامله یافت نشد یا قبلاً تکمیل شده است.", reply_markup=get_back_button("btn_trade_menu"))
                return

            trade = dict(trade)
            sell_res = trade["sell_resource"]
            sell_amt = trade["sell_amount"]

            if sell_res not in ["gold", "wood", "stone", "food"]:
                await query.edit_message_text("❌ خطا: منبع معامله نامعتبر است.", reply_markup=get_back_button("btn_trade_menu"))
                return

            # 2. Atomic update checking if it's still open
            c.execute("UPDATE market_trades SET status = 'cancelled' WHERE id = ? AND seller_id = ? AND status = 'open'", (trade_id, user_id))
            if c.rowcount == 0:
                await query.edit_message_text("❌ خطا: این معامله هم‌اکنون به اتمام رسیده یا برداشته شده است.", reply_markup=get_back_button("btn_trade_menu"))
                return
                
            c.execute(
                f"UPDATE players SET {sell_res} = {sell_res} + ? WHERE user_id = ?",
                (sell_amt, user_id)
            )
            conn.commit()
            
            # Log
            add_game_log(user_id, "trade", f"❌ لغو معامله #{trade_id} و پس‌گرفتن {sell_amt} {sell_res}")
            
            # Clamp resources
            clamp_player_resources_to_capacity(user_id)
            
            await query.edit_message_text(
                f"✅ معامله #{trade_id} لغو شد و مقدار `{sell_amt}` {sell_res} وثیقه شما به انبار بازگشت.",
                reply_markup=get_back_button("btn_trade_menu")
            )
        except Exception as e:
            conn.rollback()
            logger.error(f"Error cancelling trade {trade_id}: {e}")
            await query.edit_message_text("❌ خطا در کنسل کردن معامله.", reply_markup=get_back_button("btn_trade_menu"))
        finally:
            conn.close()

    # 7. ADMIN PANEL ACTIONS
    elif data == "btn_admin_panel":
        if not is_admin(user_id):
            await query.edit_message_text("❌ شما دسترسی به این بخش را ندارید!")
            return
            
        # Get count of pending approvals
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute("SELECT COUNT(*) FROM players WHERE status = 'pending_approval'")
        pending_cnt = c.fetchone()[0]
        conn.close()

        keyboard = [
            [InlineKeyboardButton(f"📥 بررسی نام‌ها ({pending_cnt})", callback_data="admin_city_requests")],
            [InlineKeyboardButton("🎁 شارژ مستقیم منابع بازیکن", callback_data="admin_give_resources")],
            [
                InlineKeyboardButton("📊 آمار سرور بازی", callback_data="admin_game_stats"),
                InlineKeyboardButton("📜 آخرین فعالیت‌ها", callback_data="admin_recent_logs")
            ],
            [InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")]
        ]
        await query.edit_message_text(
            f"⚙️ *پنل مدیریت ربات شهرسازی*\n\n"
            f"به بخش مدیریت خوش آمدید. در اینجا می‌توانید درخواست‌های شهرها را تایید کنید، به بازیکنان هدیه شارژ کنید، و وضعیت سرور را تحلیل نمایید.",
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode="Markdown"
        )

    elif data == "admin_game_stats":
        if not is_admin(user_id):
            return
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute("SELECT COUNT(*) FROM players")
        total_p = c.fetchone()[0]
        c.execute("SELECT COUNT(*) FROM players WHERE status = 'approved'")
        approved_p = c.fetchone()[0]
        c.execute("SELECT SUM(gold), SUM(wood), SUM(stone) FROM players")
        tot_gold, tot_wood, tot_stone = c.fetchone()
        c.execute("SELECT COUNT(*) FROM buildings")
        tot_builds = c.fetchone()[0]
        conn.close()

        stats_text = (
            f"📊 *آمار و ارقام سرور بازی*\n━━━━━━━━━━━━━━━━━━━━\n"
            f"👥 کل فرماندهان ثبت‌نام شده: `{total_p}` نفر\n"
            f"🏰 شهرهای تایید شده و فعال: `{approved_p}` شهر\n\n"
            f"💰 نقدینگی کل در جریان:\n"
            f"🪙 طلا: `{tot_gold or 0:,}` | 🪵 چوب: `{tot_wood or 0:,}` | 🪨 سنگ: `{tot_stone or 0:,}`\n\n"
            f"🧱 کل ابنیه احداث شده: `{tot_builds}` واحد\n━━━━━━━━━━━━━━━━━━━━"
        )
        await query.edit_message_text(stats_text, reply_markup=get_back_button("btn_admin_panel"), parse_mode="Markdown")

    elif data == "admin_recent_logs":
        if not is_admin(user_id):
            return
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("""
            SELECT l.*, p.city_name 
            FROM game_logs l 
            LEFT JOIN players p ON l.player_id = p.user_id 
            ORDER BY l.id DESC LIMIT 15
        """)
        logs = c.fetchall()
        conn.close()
        
        log_text = "📜 *آخرین فعالیت‌های بازیکنان سرور*\n━━━━━━━━━━━━━━━━━━━━\n"
        if not logs:
            log_text += "هیچ فعالیتی ثبت نشده است."
        else:
            for l in logs:
                time_str = l["created_at"].split("T")[1][:5] if "T" in l["created_at"] else l["created_at"]
                city = l["city_name"] or f"کاربر {l['player_id']}"
                escaped_city = escape_markdown(city)
                # Escape game log message too in case it contains markdown elements
                escaped_msg = escape_markdown(l['message'])
                log_text += f"🕒 `{time_str}` | *{escaped_city}*: {escaped_msg}\n"
        log_text += "\n━━━━━━━━━━━━━━━━━━━━"
        await query.edit_message_text(log_text, reply_markup=get_back_button("btn_admin_panel"), parse_mode="Markdown")

    elif data == "admin_city_requests":
        if not is_admin(user_id):
            return
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM players WHERE status = 'pending_approval' LIMIT 1")
        req = c.fetchone()
        conn.close()

        if not req:
            await query.edit_message_text(
                "📥 *لیست درخواست‌های معلق*\n\n"
                "در حال حاضر هیچ نام شهر معلقی در صف انتظار وجود ندارد.",
                reply_markup=get_back_button("btn_admin_panel")
            )
            return

        req = dict(req)
        keyboard = [
            [
                InlineKeyboardButton("✅ تایید شهر", callback_data=f"adm_app_{req['user_id']}"),
                InlineKeyboardButton("❌ رد نام شهر", callback_data=f"adm_rej_{req['user_id']}")
            ],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="btn_admin_panel")]
        ]
        escaped_username = escape_markdown(req['username'] or 'نامشخص')
        escaped_city_name = escape_markdown(req['city_name'])
        await query.edit_message_text(
            f"📥 *بررسی درخواست‌های معلق*\n\n"
            f"👤 کاربر: `{req['user_id']}` (@{escaped_username})\n"
            f"🏰 نام پیشنهادی شهر: *{escaped_city_name}*\n\n"
            f"موافقت خود را با احداث این شهر تایید کنید:",
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode="Markdown"
        )

    elif data.startswith("adm_app_"):
        if not is_admin(user_id):
            return
        target_uid = int(data.split("_")[2])
        success, msg = approve_city(target_uid)
        
        if success:
            # Notify user
            try:
                await context.bot.send_message(
                    chat_id=target_uid,
                    text=(
                        f"🎉 *تبریک! نام شهر شما مورد تایید قرار گرفت!*\n\n"
                        f"شهر شما رسماً احداث شد. برای باز کردن پنل فرماندهی دستور /start را بفرستید."
                    ),
                    parse_mode="Markdown"
                )
            except Exception:
                pass
            await query.edit_message_text(
                f"✅ {msg}\nدریافت درخواست بعدی...",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("⏩ بعدی", callback_data="admin_city_requests")]])
            )
        else:
            await answer_query(f"❌ خطا: {msg}", show_alert=True)
            await query.edit_message_text(
                f"❌ خطا در تایید: {msg}",
                reply_markup=get_back_button("btn_admin_panel")
            )

    elif data.startswith("adm_rej_"):
        if not is_admin(user_id):
            return
        target_uid = int(data.split("_")[2])
        success, msg = reject_city(target_uid)

        if success:
            # Notify user
            try:
                await context.bot.send_message(
                    chat_id=target_uid,
                    text=(
                        f"❌ *نام پیشنهادی شهر شما تایید نشد.*\n\n"
                        f"لطفاً نام مناسب، زیباتر و جدید دیگری برای شهر خود انتخاب کرده و ارسال نمایید."
                    ),
                    parse_mode="Markdown"
                )
            except Exception:
                pass
            await query.edit_message_text(
                f"❌ {msg}\nدریافت درخواست بعدی...",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("⏩ بعدی", callback_data="admin_city_requests")]])
            )
        else:
            await answer_query(f"❌ خطا: {msg}", show_alert=True)
            await query.edit_message_text(
                f"❌ خطا در رد نام: {msg}",
                reply_markup=get_back_button("btn_admin_panel")
            )

    elif data == "admin_give_resources":
        if not is_admin(user_id):
            return
        USER_STATES[user_id] = {"state": "admin_giving_resources_id", "data": {}}
        await query.edit_message_text(
            "🎁 *شارژ مستقیم منابع بازیکن*\n\n"
            "لطفاً شناسه عددی (User ID) بازیکن مورد نظر را ارسال کنید:"
        )

    # 8. OWNER PANEL ACTIONS
    elif data == "btn_owner_panel":
        if not is_owner(user_id):
            await query.edit_message_text("❌ شما دسترسی به این بخش را ندارید!")
            return
        keyboard = [
            [
                InlineKeyboardButton("➕ افزودن ادمین", callback_data="owner_add_admin"),
                InlineKeyboardButton("➖ حذف ادمین", callback_data="owner_remove_admin")
            ],
            [
                InlineKeyboardButton("📋 لیست ادمین‌ها", callback_data="owner_list_admins"),
                InlineKeyboardButton("📢 تنظیم کانال عضویت", callback_data="owner_set_channel")
            ],
            [InlineKeyboardButton("🔙 بازگشت به منوی اصلی", callback_data="menu_main")]
        ]
        await query.edit_message_text(
            f"👑 *پنل مالک و سازنده کل بازی*\n\n"
            f"در این بخش می‌توانید ادمین‌های ربات را مدیریت کنید و تنظیمات عضویت اجباری کانال را به صورت پویا تغییر دهید.",
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode="Markdown"
        )

    elif data == "owner_list_admins":
        if not is_owner(user_id):
            return
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute("SELECT user_id, role FROM admins")
        adms = c.fetchall()
        conn.close()

        text = "📋 *لیست ادمین‌های فعال ربات:*\n━━━━━━━━━━━━━━━━━━━━\n"
        for index, a in enumerate(adms):
            text += f"{index+1}. شناسه: `{a[0]}` (سطح: {a[1]})\n"
        text += "━━━━━━━━━━━━━━━━━━━━"
        await query.edit_message_text(text, reply_markup=get_back_button("btn_owner_panel"), parse_mode="Markdown")

    elif data == "owner_add_admin":
        if not is_owner(user_id):
            return
        USER_STATES[user_id] = {"state": "owner_adding_admin", "data": {}}
        await query.edit_message_text("✍️ لطفاً شناسه عددی (User ID) کاربر مورد نظر برای ارتقا به ادمین را ارسال کنید:")

    elif data == "owner_remove_admin":
        if not is_owner(user_id):
            return
        USER_STATES[user_id] = {"state": "owner_removing_admin", "data": {}}
        await query.edit_message_text("✍️ لطفاً شناسه عددی (User ID) ادمین مورد نظر جهت تنزل مقام را ارسال کنید:")

    elif data == "owner_set_channel":
        if not is_owner(user_id):
            return
        current = get_setting('required_channel')
        USER_STATES[user_id] = {"state": "owner_changing_channel", "data": {}}
        await query.edit_message_text(
            f"📢 *تنظیم کانال عضویت اجباری*\n\n"
            f"کانال فعلی: `{current}`\n\n"
            f"✍️ لطفاً آیدی جدید کانال را همراه با @ ارسال کنید (یا ارسال کلمه خالی برای حذف اجبار عضویت):",
            parse_mode="Markdown"
        )

    # Fallback to answer callback query if not answered explicitly in any branch
    if not query_answered:
        try:
            await query.answer()
        except Exception:
            pass

# ---------------------------------------------------------
# Main Application Launcher
# ---------------------------------------------------------
def main():
    init_db()
    logger.info("Initializing SQLite databases and trade tables...")
    
    app = ApplicationBuilder().token(BOT_TOKEN).build()
    
    # Standard Command Handlers
    app.add_handler(CommandHandler("start", start_command))
    
    # Interactive Callback Handlers for inline key buttons
    app.add_handler(CallbackQueryHandler(inline_button_handler))
    
    # Message Handler for receiving text messages (city name, trade amounts, admin commands)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, message_handler))
    
    logger.info("Bot started successfully. Polling updates...")
    app.run_polling()

if __name__ == "__main__":
    main()
