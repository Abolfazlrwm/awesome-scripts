#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Telegram: @Abolfazl_rwm
import re
import sqlite3
import random
import time
import html
import logging
from datetime import datetime

from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    Application, CommandHandler, CallbackQueryHandler,
    MessageHandler, ContextTypes, filters
)

# ================== تنظیمات ==================
BOT_TOKEN = "PUT_YOUR_BOT_TOKEN_HERE"
ADMIN_IDS = [111111111]  # آیدی عددی ادمین‌ها رو اینجا بذار (با @userinfobot بگیر)
DB_PATH = "life_sim.db"

GAME_DAY_SECONDS = 3600   # هر ۱ ساعت واقعی = ۱ روز داخل بازی
DECAY_SECONDS = 600       # هر ۱۰ دقیقه نیازها کم میشه

PREMIUM_ICONS_ENABLED = True

logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s")
log = logging.getLogger("lifesim")

# ================== داده‌های ثابت بازی ==================
SKILLS = {
    "programming": "برنامه‌نویسی",
    "medicine": "پزشکی",
    "management": "مدیریت",
    "business": "تجارت",
    "driving": "رانندگی",
    "cooking": "آشپزی",
    "farming": "کشاورزی",
    "art": "هنر",
    "investment": "سرمایه‌گذاری",
}

JOBS = {
    "worker":  {"name": "کارگر",       "salary": 150, "energy": 15, "skill": None,        "req": 0},
    "seller":  {"name": "فروشنده",     "salary": 220, "energy": 12, "skill": "business",   "req": 1},
    "driver":  {"name": "راننده",      "salary": 250, "energy": 18, "skill": "driving",    "req": 1},
    "cook":    {"name": "آشپز",        "salary": 260, "energy": 16, "skill": "cooking",    "req": 1},
    "farmer":  {"name": "کشاورز",      "salary": 200, "energy": 20, "skill": "farming",    "req": 0},
    "teacher": {"name": "معلم",        "salary": 350, "energy": 14, "skill": "management", "req": 2},
    "nurse":   {"name": "پرستار",      "salary": 400, "energy": 18, "skill": "medicine",   "req": 2},
    "engineer":{"name": "مهندس",       "salary": 450, "energy": 16, "skill": "programming","req": 2},
    "coder":   {"name": "برنامه‌نویس", "salary": 550, "energy": 15, "skill": "programming","req": 3},
    "doctor":  {"name": "پزشک",        "salary": 800, "energy": 20, "skill": "medicine",   "req": 4},
    "manager": {"name": "مدیر",        "salary": 700, "energy": 15, "skill": "management", "req": 4},
    "investor":{"name": "سرمایه‌گذار", "salary": 900, "energy": 10, "skill": "investment", "req": 4},
}

PROPERTIES = {
    "land":      {"name": "زمین",     "price": 20000,  "income": 60},
    "apartment": {"name": "آپارتمان", "price": 80000,  "income": 180},
    "house":     {"name": "خانه",     "price": 150000, "income": 300},
    "shop":      {"name": "مغازه",    "price": 100000, "income": 350},
    "villa":     {"name": "ویلا",     "price": 400000, "income": 600},
    "factory":   {"name": "کارخانه",  "price": 900000, "income": 1500},
}

VEHICLES = {
    "bike":   {"name": "دوچرخه",     "price": 1000,   "bonus": 2},
    "moto":   {"name": "موتور",      "price": 8000,   "bonus": 5},
    "car":    {"name": "ماشین",      "price": 40000,  "bonus": 10},
    "truck":  {"name": "کامیون",     "price": 90000,  "bonus": 12},
    "bus":    {"name": "اتوبوس",     "price": 150000, "bonus": 14},
    "luxury": {"name": "ماشین لوکس", "price": 250000, "bonus": 18},
    "boat":   {"name": "قایق",       "price": 300000, "bonus": 15},
    "heli":   {"name": "هلیکوپتر",   "price": 900000, "bonus": 25},
}

CRIMES = {
    "steal":   {"name": "دزدی جزئی",   "reward": (500, 2000),  "risk": 35, "jail": 10, "fine": 1500},
    "fraud":   {"name": "کلاهبرداری", "reward": (2000, 7000), "risk": 55, "jail": 25, "fine": 5000},
    "smuggle": {"name": "قاچاق",       "reward": (7000, 18000),"risk": 70, "jail": 45, "fine": 12000},
}

COURSES = {
    "school":     {"name": "مدرسه",            "price": 3000,  "int_gain": 5},
    "university": {"name": "دانشگاه",          "price": 20000, "int_gain": 15},
    "shortcourse":{"name": "دوره آموزشی کوتاه","price": 5000,  "int_gain": 3},
}

MISSION_POOL = [
    ("work_once",   "امروز یک بار کار کن",        800, 20),
    ("train_once",  "یک مهارت رو تمرین کن",        600, 15),
    ("deposit_any", "مقداری پول تو بانک بذار",     500, 10),
    ("visit_shop",  "یه سر به بازار بزن",           300, 5),
]

# ================== ایموجی‌ها ==================
EMOJI = {
  "profile": "<tg-emoji emoji-id=\"5843973755545590553\">👤</tg-emoji>",
  "job": "<tg-emoji emoji-id=\"5908979477139955285\">💼</tg-emoji>",
  "bank": "<tg-emoji emoji-id=\"5332455502917949981\">🏦</tg-emoji>",
  "skill": "<tg-emoji emoji-id=\"5857142769520217225\">📘</tg-emoji>",
  "estate": "<tg-emoji emoji-id=\"5965086602022625817\">🏠</tg-emoji>",
  "vehicle": "<tg-emoji emoji-id=\"5785109764170060865\">🚗</tg-emoji>",
  "company": "<tg-emoji emoji-id=\"5967822972931542886\">🏠</tg-emoji>",
  "market": "<tg-emoji emoji-id=\"5312361253610475399\">🛒</tg-emoji>",
  "missions": "<tg-emoji emoji-id=\"5832335747088130009\">🎯</tg-emoji>",
  "leaderboard": "<tg-emoji emoji-id=\"5264810111603354463\">💎</tg-emoji>",
  "social": "<tg-emoji emoji-id=\"5859432880442187580\">👥</tg-emoji>",
  "crime": "⚖️",
  "admin": "<tg-emoji emoji-id=\"5857472524224302574\">🛠️</tg-emoji>",
  "back": "<tg-emoji emoji-id=\"5352759161945867747\">🔙</tg-emoji>",
  "work": "<tg-emoji emoji-id=\"5857472524224302574\">🛠️</tg-emoji>",
  "quit": "<tg-emoji emoji-id=\"5877341274863832725\">🚪</tg-emoji>",
  "apply": "<tg-emoji emoji-id=\"6296115201997542381\">✅</tg-emoji>",
  "deposit": "<tg-emoji emoji-id=\"5462928852659505252\">☝️</tg-emoji>",
  "withdraw": "<tg-emoji emoji-id=\"5463344588313890765\">☝️</tg-emoji>",
  "transfer": "<tg-emoji emoji-id=\"5888625992196431593\">💳</tg-emoji>",
  "loan": "<tg-emoji emoji-id=\"5433811242135331842\">📥</tg-emoji>",
  "pay_loan": "<tg-emoji emoji-id=\"5433614747381538714\">📤</tg-emoji>",
  "education": "<tg-emoji emoji-id=\"6005859332153939506\">🎓</tg-emoji>",
  "course": "<tg-emoji emoji-id=\"5445009617740375254\">📖</tg-emoji>",
  "estate_buy": "<tg-emoji emoji-id=\"5465226866321268133\">🏠</tg-emoji>",
  "estate_mine": "<tg-emoji emoji-id=\"5197269100878907942\">✍️</tg-emoji>",
  "vehicle_buy": "<tg-emoji emoji-id=\"6089312981961676994\">🚘</tg-emoji>",
  "vehicle_soldout": "<tg-emoji emoji-id=\"5147764152478992339\">❌</tg-emoji>",
  "company_withdraw": "<tg-emoji emoji-id=\"5278467510604160626\">💰</tg-emoji>",
  "company_hire": "<tg-emoji emoji-id=\"5456461374941053404\">🤠</tg-emoji>",
  "company_create": "🏗️",
  "market_list": "<tg-emoji emoji-id=\"5197269100878907942\">✍️</tg-emoji>",
  "market_sellvehicle": "<tg-emoji emoji-id=\"5974155859259824494\">🚲</tg-emoji>",
  "market_sell": "<tg-emoji emoji-id=\"4997165157219043027\">📦</tg-emoji>",
  "market_pick": "<tg-emoji emoji-id=\"5785109764170060865\">🚗</tg-emoji>",
  "mission_claim": "<tg-emoji emoji-id=\"5832335747088130009\">🎯</tg-emoji>",
  "mission_done": "<tg-emoji emoji-id=\"5933599273357675660\">✔</tg-emoji>",
  "friend": "<tg-emoji emoji-id=\"5357080225463149588\">🤝</tg-emoji>",
  "gift": "<tg-emoji emoji-id=\"5850323366476519158\">🎁</tg-emoji>",
  "marry": "<tg-emoji emoji-id=\"5987899526973756690\">💍</tg-emoji>",
  "divorce": "<tg-emoji emoji-id=\"5278454020111887994\">💔</tg-emoji>",
  "friend_list": "<tg-emoji emoji-id=\"5857288029609135806\">📜</tg-emoji>",
  "crime_do": "<tg-emoji emoji-id=\"5373153968769735192\">🧐</tg-emoji>",
  "admin_stats": "<tg-emoji emoji-id=\"5967391010890731343\">📊</tg-emoji>",
  "admin_give": "<tg-emoji emoji-id=\"5197434882321567830\">💵</tg-emoji>",
  "admin_ban": "<tg-emoji emoji-id=\"5929331428780151040\">🚫</tg-emoji>",
  "admin_vehicles": "<tg-emoji emoji-id=\"4958801766301828295\">🚗</tg-emoji>",
  "admin_broadcast": "<tg-emoji emoji-id=\"5298609030321691620\">📣</tg-emoji>",
  "admin_event": "<tg-emoji emoji-id=\"5399898266265475100\">🌍</tg-emoji>",
  "admin_vehicle_edit": "<tg-emoji emoji-id=\"5929295741896889537\">⚙</tg-emoji>",
  "event_item": "<tg-emoji emoji-id=\"5224450179368767019\">🌎</tg-emoji>"
}

_EMOJI_ID_RE = re.compile(r'emoji-id="(\d+)"')
_EMOJI_INNER_RE = re.compile(r'>([^<]*)<')


def _emoji_id(key):
    """آی‌دی عددیِ ایموجی پرمیوم رو از تگ tg-emoji در میاره؛ اگه ایموجی ساده بود None میده."""
    tag = EMOJI.get(key, "")
    m = _EMOJI_ID_RE.search(tag)
    return m.group(1) if m else None


def _emoji_fallback(key):
    """کاراکتر یونیکدِ ساده رو برمی‌گردونه (چه از داخل تگ، چه خودِ ایموجی ساده)."""
    tag = EMOJI.get(key, "")
    m = _EMOJI_INNER_RE.search(tag)
    return m.group(1) if m else tag

# ================== دیتابیس ==================

def get_conn():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_conn()
    c = conn.cursor()
    c.execute("""CREATE TABLE IF NOT EXISTS users(
        user_id INTEGER PRIMARY KEY,
        username TEXT,
        name TEXT,
        bio TEXT DEFAULT '',
        age INTEGER DEFAULT 18,
        energy INTEGER DEFAULT 100,
        health INTEGER DEFAULT 100,
        happiness INTEGER DEFAULT 100,
        stress INTEGER DEFAULT 0,
        hunger INTEGER DEFAULT 100,
        sleep INTEGER DEFAULT 100,
        intelligence INTEGER DEFAULT 10,
        xp INTEGER DEFAULT 0,
        level INTEGER DEFAULT 1,
        cash INTEGER DEFAULT 5000,
        bank INTEGER DEFAULT 0,
        loan INTEGER DEFAULT 0,
        credit_score INTEGER DEFAULT 50,
        job TEXT,
        job_level INTEGER DEFAULT 1,
        job_xp INTEGER DEFAULT 0,
        married_to INTEGER,
        jailed_until INTEGER DEFAULT 0,
        banned INTEGER DEFAULT 0,
        vehicle_bonus INTEGER DEFAULT 0,
        last_work INTEGER DEFAULT 0,
        last_train INTEGER DEFAULT 0,
        last_daily TEXT DEFAULT '',
        card_number TEXT,
        created_at TEXT,
        last_active TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS skills(
        user_id INTEGER, skill TEXT, level INTEGER DEFAULT 1, xp INTEGER DEFAULT 0,
        PRIMARY KEY(user_id, skill)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS properties(
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, ptype TEXT, income INTEGER, bought_at TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS vehicles(
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, vtype TEXT, bought_at TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS companies(
        id INTEGER PRIMARY KEY AUTOINCREMENT, owner_id INTEGER, name TEXT, balance INTEGER DEFAULT 0, created_at TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS company_employees(
        company_id INTEGER, user_id INTEGER, salary INTEGER DEFAULT 100,
        PRIMARY KEY(company_id, user_id)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS market(
        id INTEGER PRIMARY KEY AUTOINCREMENT, seller_id INTEGER, item TEXT, price INTEGER, qty INTEGER, created_at TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS missions_done(
        user_id INTEGER, mission_key TEXT, day TEXT, PRIMARY KEY(user_id, mission_key, day)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS friends(
        user1 INTEGER, user2 INTEGER, PRIMARY KEY(user1, user2)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS marriage_requests(
        from_id INTEGER, to_id INTEGER, PRIMARY KEY(from_id, to_id)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS history(
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, event TEXT, time TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS world(
        key TEXT PRIMARY KEY, value TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS vehicle_shop(
        vtype TEXT PRIMARY KEY, price INTEGER, stock INTEGER
    )""")
    # مهاجرت آروم برای دیتابیس‌های قدیمی‌تر که این ستون‌ها رو ندارن
    for stmt in (
        "ALTER TABLE users ADD COLUMN card_number TEXT",
        "ALTER TABLE market ADD COLUMN vehicle_id INTEGER",
    ):
        try:
            c.execute(stmt)
        except sqlite3.OperationalError:
            pass
    conn.commit()
    conn.close()


def now_str():
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def today_str():
    return datetime.now().strftime("%Y-%m-%d")


def get_user(uid):
    conn = get_conn()
    row = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
    conn.close()
    return row


def ensure_user(uid, username, name):
    conn = get_conn()
    row = conn.execute("SELECT user_id FROM users WHERE user_id=?", (uid,)).fetchone()
    if not row:
        card = gen_card_number()
        while conn.execute("SELECT 1 FROM users WHERE card_number=?", (card,)).fetchone():
            card = gen_card_number()
        conn.execute(
            "INSERT INTO users(user_id, username, name, card_number, created_at, last_active) VALUES(?,?,?,?,?,?)",
            (uid, username or "", name or "بازیکن", card, now_str(), now_str())
        )
        for sk in SKILLS:
            conn.execute("INSERT OR IGNORE INTO skills(user_id, skill, level, xp) VALUES(?,?,1,0)", (uid, sk))
        conn.commit()
        conn.close()
        log_history(uid, "به بازی پیوست")
        return True
    conn.execute("UPDATE users SET last_active=? WHERE user_id=?", (now_str(), uid))
    conn.commit()
    conn.close()
    return False


def update_user(uid, **kwargs):
    if not kwargs:
        return
    conn = get_conn()
    cols = ", ".join(f"{k}=?" for k in kwargs)
    vals = list(kwargs.values()) + [uid]
    conn.execute(f"UPDATE users SET {cols} WHERE user_id=?", vals)
    conn.commit()
    conn.close()


def add_cash(uid, amount):
    conn = get_conn()
    conn.execute("UPDATE users SET cash = cash + ? WHERE user_id=?", (amount, uid))
    conn.commit()
    conn.close()


def add_bank(uid, amount):
    conn = get_conn()
    conn.execute("UPDATE users SET bank = bank + ? WHERE user_id=?", (amount, uid))
    conn.commit()
    conn.close()


def add_xp(uid, amount):
    conn = get_conn()
    conn.execute("UPDATE users SET xp = xp + ? WHERE user_id=?", (amount, uid))
    u = conn.execute("SELECT xp, level FROM users WHERE user_id=?", (uid,)).fetchone()
    new_level = 1 + u["xp"] // 500
    if new_level != u["level"]:
        conn.execute("UPDATE users SET level=? WHERE user_id=?", (new_level, uid))
    conn.commit()
    conn.close()


def log_history(uid, text):
    conn = get_conn()
    conn.execute("INSERT INTO history(user_id, event, time) VALUES(?,?,?)", (uid, text, now_str()))
    conn.commit()
    conn.close()


def get_skill(uid, skill):
    conn = get_conn()
    row = conn.execute("SELECT * FROM skills WHERE user_id=? AND skill=?", (uid, skill)).fetchone()
    conn.close()
    return row


def net_worth(u):
    conn = get_conn()
    props = conn.execute("SELECT COUNT(*) c, COALESCE(SUM(income),0) i FROM properties WHERE user_id=?", (u["user_id"],)).fetchone()
    conn.close()
    prop_value = props["c"] * 30000
    return u["cash"] + u["bank"] - u["loan"] + prop_value


def resolve_target(context, arg):
    """پیدا کردن آیدی کاربر از روی @یوزرنیم یا آیدی عددی"""
    arg = normalize_digits(arg)
    conn = get_conn()
    if arg.startswith("@"):
        row = conn.execute("SELECT user_id FROM users WHERE username=?", (arg[1:],)).fetchone()
    else:
        try:
            uid = int(arg)
            row = conn.execute("SELECT user_id FROM users WHERE user_id=?", (uid,)).fetchone()
        except ValueError:
            row = None
    conn.close()
    return row["user_id"] if row else None


def gen_card_number():
    """ساخت یک شماره کارت نمایشی ۱۶ رقمی"""
    return "6219" + "".join(str(random.randint(0, 9)) for _ in range(12))


def format_card(card):
    if not card:
        return "-"
    return " ".join(card[i:i + 4] for i in range(0, len(card), 4))


def ensure_card_number(uid):
    """اگه کاربر شماره کارت نداشت (مثلا حساب قدیمیه) یکی براش می‌سازه"""
    conn = get_conn()
    row = conn.execute("SELECT card_number FROM users WHERE user_id=?", (uid,)).fetchone()
    if row and row["card_number"]:
        conn.close()
        return row["card_number"]
    while True:
        card = gen_card_number()
        dup = conn.execute("SELECT 1 FROM users WHERE card_number=?", (card,)).fetchone()
        if not dup:
            break
    conn.execute("UPDATE users SET card_number=? WHERE user_id=?", (card, uid))
    conn.commit()
    conn.close()
    return card


def resolve_by_card(card_raw):
    """پیدا کردن کاربر از روی شماره کارتش"""
    card = normalize_digits(card_raw or "").replace(" ", "").replace("-", "").strip()
    if not card:
        return None
    conn = get_conn()
    row = conn.execute("SELECT user_id FROM users WHERE card_number=?", (card,)).fetchone()
    conn.close()
    return row["user_id"] if row else None


def get_vehicle_shop(key):
    """قیمت و موجودی فعلی یک نوع وسیله نقلیه تو فروشگاه (موجودی None یعنی نامحدود)"""
    conn = get_conn()
    row = conn.execute("SELECT * FROM vehicle_shop WHERE vtype=?", (key,)).fetchone()
    conn.close()
    price = row["price"] if row and row["price"] is not None else VEHICLES[key]["price"]
    stock = row["stock"] if row else None
    return price, stock


def recalc_vehicle_bonus(uid):
    """بعد از خرید/فروش وسیله نقلیه، بونوس درآمد کاری رو دوباره حساب می‌کنه"""
    conn = get_conn()
    rows = conn.execute("SELECT vtype FROM vehicles WHERE user_id=?", (uid,)).fetchall()
    conn.close()
    bonus = max([VEHICLES[r["vtype"]]["bonus"] for r in rows], default=0)
    update_user(uid, vehicle_bonus=bonus)


def esc(text):
    return html.escape(str(text or ""))


_DIGIT_MAP = {}
for _i, _d in enumerate("۰۱۲۳۴۵۶۷۸۹"):   # ارقام فارسی
    _DIGIT_MAP[_d] = str(_i)
for _i, _d in enumerate("٠١٢٣٤٥٦٧٨٩"):   # ارقام عربی
    _DIGIT_MAP[_d] = str(_i)


def normalize_digits(text):
    """ارقام فارسی/عربی رو به ارقام انگلیسی تبدیل می‌کنه و فاصله‌های اضافه رو حذف می‌کنه.
    این رفع باگ مهمیه: خیلی از کیبوردهای فارسی موبایل به‌صورت پیش‌فرض عدد فارسی تایپ می‌کنن
    و باعث می‌شد شماره کارت یا مبلغ درست تشخیص داده نشه."""
    if text is None:
        return text
    return "".join(_DIGIT_MAP.get(ch, ch) for ch in text).strip()


def _trim(val):
    s = f"{val:.1f}"
    return s[:-2] if s.endswith(".0") else s


def fmt_short(n):
    """نمایش کوتاه برای متن دکمه‌ها: 100 هزار تومان / 1.5 میلیون تومان"""
    n = int(n)
    sign = "-" if n < 0 else ""
    a = abs(n)
    if a >= 1_000_000_000:
        return f"{sign}{_trim(a / 1_000_000_000)} میلیارد تومان"
    if a >= 1_000_000:
        return f"{sign}{_trim(a / 1_000_000)} میلیون تومان"
    if a >= 1_000:
        return f"{sign}{_trim(a / 1_000)} هزار تومان"
    return f"{sign}{a:,} تومان"


def fmt(n):
    """نمایش کامل برای متن‌های اصلی: 100,000 تومان (100 هزار تومان)"""
    n = int(n)
    base = f"{n:,}"
    short = fmt_short(n)
    if abs(n) >= 1000:
        return f"{base} تومان ({short})"
    return f"{base} تومان"


STYLE_INFO = "primary"    # آبی - ناوبری / خنثی
STYLE_GOOD = "success"    # سبز - خرید / تایید / اقدام مثبت
STYLE_BAD = "danger"      # قرمز - بازگشت / لغو / پرخطر


def is_admin(uid):
    return uid in ADMIN_IDS


def is_jailed(u):
    return u["jailed_until"] and u["jailed_until"] > int(time.time())

# ================== منوها ==================

def sbtn(text, callback, style=None, emoji=None):
    """
    دکمه‌ساز مرکزی. سه تا کار می‌کنه:
      - رنگ واقعی دکمه (Bot API 9.4 - style)
      - اگه emoji پاس داده بشه و PREMIUM_ICONS_ENABLED فعال باشه و اون کلید تو EMOJI
        یه ایموجی پرمیوم واقعی باشه -> از icon_custom_emoji_id واقعی استفاده می‌کنه
        (دقیقاً طبق مستندات رسمی تلگرام برای دکمه‌های شیشه‌ای).
      - در غیر این صورت (Premium نداری، یا اون کلید ایموجی ساده‌ست) -> خودکار همون
        کاراکتر یونیکد رو کنار متن دکمه میذاره، بدون این‌که هیچ‌جا خطا بگیره.
    """
    kwargs = {"callback_data": callback}
    if style:
        kwargs["style"] = style

    if emoji:
        eid = _emoji_id(emoji)
        if PREMIUM_ICONS_ENABLED and eid:
            kwargs["icon_custom_emoji_id"] = eid
        else:
            fallback = _emoji_fallback(emoji)
            if fallback:
                text = f"{fallback} {text}"

    return InlineKeyboardButton(text, **kwargs)


def main_menu(uid):
    kb = [
        [sbtn("پروفایل", "cat:profile", STYLE_INFO, emoji="profile"),
         sbtn("شغل", "cat:job", STYLE_GOOD, emoji="job")],
        [sbtn("بانک", "cat:bank", STYLE_INFO, emoji="bank"),
         sbtn("مهارت و آموزش", "cat:skill", STYLE_GOOD, emoji="skill")],
        [sbtn("املاک", "cat:estate", STYLE_INFO, emoji="estate"),
         sbtn("وسایل نقلیه", "cat:vehicle", STYLE_GOOD, emoji="vehicle")],
        [sbtn("شرکت من", "cat:company", STYLE_INFO, emoji="company"),
         sbtn("بازار", "cat:market", STYLE_GOOD, emoji="market")],
        [sbtn("ماموریت‌ها", "cat:missions", STYLE_INFO, emoji="missions"),
         sbtn("رتبه‌بندی", "cat:leaderboard", STYLE_GOOD, emoji="leaderboard")],
        [sbtn("روابط", "cat:social", STYLE_INFO, emoji="social"),
         sbtn("جرم و قانون", "cat:crime", STYLE_BAD, emoji="crime")],
    ]
    if is_admin(uid):
        kb.append([sbtn("پنل مدیریت", "cat:admin", STYLE_BAD, emoji="admin")])
    return InlineKeyboardMarkup(kb)


def back_btn(target="menu"):
    return InlineKeyboardMarkup([[sbtn("بازگشت", target, STYLE_BAD, emoji="back")]])


async def send_or_edit(update, text, kb=None, parse_mode=None):
    if update.callback_query:
        await update.callback_query.edit_message_text(text, reply_markup=kb, parse_mode=parse_mode)
    else:
        await update.message.reply_text(text, reply_markup=kb, parse_mode=parse_mode)

# ================== هندلر شروع ==================

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    is_new = ensure_user(user.id, user.username, user.first_name)
    if is_new:
        text = (
            f"سلام {user.first_name}\n"
            "به شبیه‌ساز زندگی خوش اومدی.\n"
            "یه حساب تازه برات ساخته شد با ۵۰۰۰ تومان پول نقد.\n"
            "از منوی زیر شروع کن."
        )
    else:
        text = f"سلام {user.first_name}، برگشتی. از منوی زیر ادامه بده."
    await update.message.reply_text(text, reply_markup=main_menu(user.id))


async def help_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text(
        "دستورات:\n/start شروع یا بازگشت به منو\n/menu نمایش منو\n/help راهنما"
    )


async def menu_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("منوی اصلی:", reply_markup=main_menu(update.effective_user.id))

# ================== پروفایل ==================

async def show_profile(update, context, uid):
    u = get_user(uid)
    card = ensure_card_number(uid)
    nw = net_worth(u)
    job_name = JOBS[u["job"]]["name"] if u["job"] else "بیکار"
    status = ""
    if is_jailed(u):
        left = u["jailed_until"] - int(time.time())
        status += f"\n\n🚔 <b>در زندان</b>\nتا {left // 60} دقیقه دیگر"
    if u["married_to"]:
        p = get_user(u["married_to"])
        pname = esc(p["name"]) if p else "همسر"
        status += f"\n\n💍 <b>متاهل با</b> {pname}"
    text = (
        f"{EMOJI['profile']} <b>پروفایل {esc(u['name'])}</b>\n\n"
        f"آیدی عددی: <code>{u['user_id']}</code>\n\n"
        "━━━━━━━━━━━━\n\n"
        f"<b>سطح:</b> {u['level']}\n"
        f"<b>تجربه:</b> {u['xp']}\n\n"
        f"<b>سن:</b> {u['age']}\n"
        f"<b>شغل:</b> {esc(job_name)}\n\n"
        "━━━━━━━━━━━━\n\n"
        f"<b>💰 پول نقد:</b>\n{fmt(u['cash'])}\n\n"
        f"<b>🏦 موجودی بانک:</b>\n{fmt(u['bank'])}\n\n"
        f"<b>💳 شماره کارت:</b>\n<code>{format_card(card)}</code>\n"
        f"<i>برای دریافت انتقال از بقیه، همین شماره کارت رو بهشون بده.</i>\n\n"
        f"<b>📄 وام باز:</b>\n{fmt(u['loan'])}\n\n"
        f"<b>⭐ اعتبار بانکی:</b> {u['credit_score']}\n\n"
        f"<b>📊 دارایی خالص:</b>\n{fmt(nw)}\n\n"
        "━━━━━━━━━━━━\n\n"
        f"<b>⚡ انرژی:</b> {u['energy']}\n"
        f"<b>❤️ سلامت:</b> {u['health']}\n\n"
        f"<b>😊 شادی:</b> {u['happiness']}\n"
        f"<b>😖 استرس:</b> {u['stress']}\n\n"
        f"<b>🍔 گرسنگی:</b> {u['hunger']}\n"
        f"<b>😴 خواب:</b> {u['sleep']}\n\n"
        f"<b>🧠 هوش:</b> {u['intelligence']}"
        f"{status}"
    )
    await send_or_edit(update, text, back_btn(), parse_mode="HTML")

# ================== شغل ==================

def job_menu_kb(u):
    rows = []
    if not u["job"]:
        for key, j in JOBS.items():
            rows.append([sbtn(f"{j['name']} ({fmt_short(j['salary'])})", f"job:apply:{key}", STYLE_GOOD, emoji="apply")])
    else:
        rows.append([sbtn("کار کن", "job:work", STYLE_GOOD, emoji="work")])
        rows.append([sbtn("استعفا", "job:quit", STYLE_BAD, emoji="quit")])
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_job_menu(update, context, uid):
    u = get_user(uid)
    if u["job"]:
        j = JOBS[u["job"]]
        text = (f"{EMOJI['job']} شغل فعلی: {esc(j['name'])}\n"
                f"سطح شغلی: {u['job_level']}\nحقوق پایه: {fmt(j['salary'])}")
    else:
        text = f"{EMOJI['job']} شغلی نداری. یکی از مشاغل زیر رو انتخاب کن (ممکنه نیاز به سطح مهارت داشته باشه):"
    await send_or_edit(update, text, job_menu_kb(u), parse_mode="HTML")


async def job_apply(update, context, uid, key):
    u = get_user(uid)
    j = JOBS[key]
    if j["skill"]:
        sk = get_skill(uid, j["skill"])
        if sk["level"] < j["req"]:
            await update.callback_query.answer(
                f"برای این شغل به سطح {j['req']} مهارت {SKILLS[j['skill']]} نیاز داری.", show_alert=True)
            return
    update_user(uid, job=key, job_level=1, job_xp=0)
    log_history(uid, f"استخدام شد به عنوان {j['name']}")
    await update.callback_query.answer(f"✅ با موفقیت به عنوان {j['name']} استخدام شدی.")
    await show_job_menu(update, context, uid)


async def job_quit(update, context, uid):
    update_user(uid, job=None, job_level=1, job_xp=0)
    await update.callback_query.answer("از شغلت استعفا دادی.")
    await show_job_menu(update, context, uid)


async def job_work(update, context, uid):
    u = get_user(uid)
    if is_jailed(u):
        await update.callback_query.answer("تو زندانی هستی، نمی‌تونی کار کنی.", show_alert=True)
        return
    if not u["job"]:
        await update.callback_query.answer("اول یه شغل انتخاب کن.", show_alert=True)
        return
    j = JOBS[u["job"]]
    if u["energy"] < j["energy"]:
        await update.callback_query.answer("انرژی کافی نداری، استراحت کن.", show_alert=True)
        return
    now = int(time.time())
    if now - u["last_work"] < 600:
        wait = 600 - (now - u["last_work"])
        await update.callback_query.answer(f"{wait // 60 + 1} دقیقه دیگه دوباره امتحان کن.", show_alert=True)
        return
    pay = j["salary"] + u["job_level"] * 20 + u["vehicle_bonus"]
    add_cash(uid, pay)
    add_xp(uid, 15)
    new_energy = max(0, u["energy"] - j["energy"])
    new_jxp = u["job_xp"] + 10
    new_jlevel = u["job_level"]
    if new_jxp >= 100:
        new_jxp = 0
        new_jlevel += 1
    update_user(uid, energy=new_energy, last_work=now, job_xp=new_jxp, job_level=new_jlevel,
                stress=min(100, u["stress"] + 5))
    if j["skill"]:
        conn = get_conn()
        conn.execute("UPDATE skills SET xp = xp + 8 WHERE user_id=? AND skill=?", (uid, j["skill"]))
        sk = conn.execute("SELECT * FROM skills WHERE user_id=? AND skill=?", (uid, j["skill"])).fetchone()
        if sk["xp"] >= 100:
            conn.execute("UPDATE skills SET xp=0, level=level+1 WHERE user_id=? AND skill=?", (uid, j["skill"]))
        conn.commit()
        conn.close()
    await update.callback_query.answer(f"کار کردی و {fmt(pay)} گرفتی.")
    await show_job_menu(update, context, uid)

# ================== بانک ==================

def bank_menu_kb():
    rows = [
        [sbtn("واریز به بانک", "bank:dep", STYLE_GOOD, emoji="deposit"),
         sbtn("برداشت از بانک", "bank:wd", STYLE_INFO, emoji="withdraw")],
        [sbtn("انتقال به کاربر دیگر", "bank:transfer", STYLE_GOOD, emoji="transfer")],
        [sbtn("گرفتن وام", "bank:loan", STYLE_INFO, emoji="loan"),
         sbtn("پرداخت وام", "bank:pay", STYLE_GOOD, emoji="pay_loan")],
        [sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")],
    ]
    return InlineKeyboardMarkup(rows)


async def show_bank_menu(update, context, uid):
    u = get_user(uid)
    card = ensure_card_number(uid)
    text = (
        f"{EMOJI['bank']} <b>بانک</b>\n\n"
        f"پول نقد: {fmt(u['cash'])}\n"
        f"موجودی بانک: {fmt(u['bank'])}\n"
        f"{EMOJI['transfer']} شماره کارت شما: {esc(format_card(card))}\n"
        f"وام باز: {fmt(u['loan'])}\n"
        f"اعتبار بانکی: {u['credit_score']}\n\n"
        "سقف وام بر اساس اعتبار بانکی‌ته.\n"
        "برای انتقال وجه به شخص دیگه، شماره کارتش رو ازش بگیر و از گزینه «انتقال به کاربر دیگر» استفاده کن."
    )
    await send_or_edit(update, text, bank_menu_kb(), parse_mode="HTML")


async def ask_amount(update, context, awaiting, prompt):
    context.user_data["awaiting"] = awaiting
    await update.callback_query.edit_message_text(prompt, reply_markup=back_btn("cat:bank"))

# ================== مهارت و آموزش ==================

def skill_menu_kb():
    rows = []
    items = list(SKILLS.items())
    for i in range(0, len(items), 2):
        pair = items[i:i+2]
        rows.append([sbtn(name, f"skill:train:{key}", STYLE_GOOD, emoji="skill") for key, name in pair])
    rows.append([sbtn("آموزش (مدرسه/دانشگاه)", "cat:education", STYLE_INFO, emoji="education")])
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_skill_menu(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM skills WHERE user_id=?", (uid,)).fetchall()
    conn.close()
    lines = [f"{esc(SKILLS[r['skill']])}: سطح {r['level']}" for r in rows]
    text = f"{EMOJI['skill']} <b>مهارت‌های تو</b>:\n" + "\n".join(lines)
    await send_or_edit(update, text, skill_menu_kb(), parse_mode="HTML")


async def skill_train(update, context, uid, key):
    u = get_user(uid)
    if is_jailed(u):
        await update.callback_query.answer("تو زندانی هستی.", show_alert=True)
        return
    if u["energy"] < 10:
        await update.callback_query.answer("انرژی کافی نداری.", show_alert=True)
        return
    now = int(time.time())
    if now - u["last_train"] < 300:
        wait = 300 - (now - u["last_train"])
        await update.callback_query.answer(f"{wait // 60 + 1} دقیقه دیگه دوباره تمرین کن.", show_alert=True)
        return
    conn = get_conn()
    conn.execute("UPDATE skills SET xp = xp + 20 WHERE user_id=? AND skill=?", (uid, key))
    sk = conn.execute("SELECT * FROM skills WHERE user_id=? AND skill=?", (uid, key)).fetchone()
    leveled = False
    if sk["xp"] >= 100:
        conn.execute("UPDATE skills SET xp=0, level=level+1 WHERE user_id=? AND skill=?", (uid, key))
        leveled = True
    conn.commit()
    conn.close()
    update_user(uid, energy=max(0, u["energy"] - 10), last_train=now)
    add_xp(uid, 5)
    msg = f"مهارت {SKILLS[key]} رو تمرین کردی."
    if leveled:
        msg += " سطح مهارت بالا رفت."
    await update.callback_query.answer(msg)
    await show_skill_menu(update, context, uid)


def education_menu_kb():
    rows = [[sbtn(f"{c['name']} ({fmt_short(c['price'])})", f"edu:buy:{k}", STYLE_GOOD, emoji="course")]
            for k, c in COURSES.items()]
    rows.append([sbtn("بازگشت", "cat:skill", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_education(update, context, uid):
    await send_or_edit(update, f"{EMOJI['education']} دوره‌های آموزشی موجود:", education_menu_kb(),
                        parse_mode="HTML")


async def buy_course(update, context, uid, key):
    u = get_user(uid)
    c = COURSES[key]
    if u["cash"] < c["price"]:
        await update.callback_query.answer("پول نقد کافی نداری.", show_alert=True)
        return
    add_cash(uid, -c["price"])
    update_user(uid, intelligence=u["intelligence"] + c["int_gain"])
    add_xp(uid, 30)
    log_history(uid, f"دوره {c['name']} رو گذروند")
    await update.callback_query.answer(f"{c['name']} رو با موفقیت گذروندی. هوش بالا رفت.")
    await show_education(update, context, uid)

# ================== املاک ==================

def estate_menu_kb():
    rows = [[sbtn(f"{p['name']} ({fmt_short(p['price'])})", f"estate:buy:{k}", STYLE_GOOD, emoji="estate_buy")]
            for k, p in PROPERTIES.items()]
    rows.append([sbtn("املاک من", "estate:mine", STYLE_INFO, emoji="estate_mine")])
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_estate_menu(update, context, uid):
    await send_or_edit(update, f"{EMOJI['estate']} املاک قابل خرید (درآمد روزانه میدن):", estate_menu_kb(),
                        parse_mode="HTML")


async def buy_property(update, context, uid, key):
    u = get_user(uid)
    p = PROPERTIES[key]
    if u["bank"] + u["cash"] < p["price"]:
        await update.callback_query.answer("پول کافی نداری (نقد یا بانک).", show_alert=True)
        return
    # اول از نقد، بعد از بانک کم میشه
    price = p["price"]
    from_cash = min(u["cash"], price)
    from_bank = price - from_cash
    update_user(uid, cash=u["cash"] - from_cash, bank=u["bank"] - from_bank)
    conn = get_conn()
    conn.execute("INSERT INTO properties(user_id, ptype, income, bought_at) VALUES(?,?,?,?)",
                 (uid, key, p["income"], now_str()))
    conn.commit()
    conn.close()
    log_history(uid, f"{p['name']} رو خرید")
    await update.callback_query.answer(f"{p['name']} رو خریدی.")
    await show_estate_menu(update, context, uid)


async def show_my_estate(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM properties WHERE user_id=?", (uid,)).fetchall()
    conn.close()
    if not rows:
        text = f"{EMOJI['estate_mine']} هنوز ملکی نداری."
    else:
        total = sum(r["income"] for r in rows)
        lines = [f"{esc(PROPERTIES[r['ptype']]['name'])} - درآمد روزانه {fmt(r['income'])}" for r in rows]
        text = (f"{EMOJI['estate_mine']} <b>املاک من</b>:\n" + "\n".join(lines) +
                f"\n\nمجموع درآمد روزانه: {fmt(total)}")
    await send_or_edit(update, text, back_btn("cat:estate"), parse_mode="HTML")

# ================== وسایل نقلیه ==================

def vehicle_menu_kb():
    rows = []
    for key, v in VEHICLES.items():
        price, stock = get_vehicle_shop(key)
        if stock is not None and stock <= 0:
            rows.append([sbtn(f"{v['name']} (ناموجود)", "noop", emoji="vehicle_soldout")])
        else:
            stock_txt = f" | موجودی: {stock}" if stock is not None else ""
            rows.append([sbtn(f"{v['name']} ({fmt_short(price)}{stock_txt})", f"vehicle:buy:{key}", STYLE_GOOD, emoji="vehicle_buy")])
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_vehicle_menu(update, context, uid):
    u = get_user(uid)
    text = (f"{EMOJI['vehicle']} <b>وسایل نقلیه</b>\n\n"
            f"جایزه فعلی درآمد کاری از وسیله نقلیه: {u['vehicle_bonus']}\n\nوسایل نقلیه قابل خرید:")
    await send_or_edit(update, text, vehicle_menu_kb(), parse_mode="HTML")


async def buy_vehicle(update, context, uid, key):
    u = get_user(uid)
    v = VEHICLES[key]
    price, stock = get_vehicle_shop(key)
    if stock is not None and stock <= 0:
        await update.callback_query.answer("متاسفانه این وسیله فعلاً ناموجوده.", show_alert=True)
        return
    if u["cash"] < price:
        await update.callback_query.answer("پول نقد کافی نداری.", show_alert=True)
        return
    add_cash(uid, -price)
    update_user(uid, vehicle_bonus=max(u["vehicle_bonus"], v["bonus"]))
    conn = get_conn()
    conn.execute("INSERT INTO vehicles(user_id, vtype, bought_at) VALUES(?,?,?)", (uid, key, now_str()))
    if stock is not None:
        conn.execute("UPDATE vehicle_shop SET stock = stock - 1 WHERE vtype=?", (key,))
    conn.commit()
    conn.close()
    log_history(uid, f"{v['name']} رو خرید")
    await update.callback_query.answer(f"🎉 {v['name']} رو با موفقیت خریدی.")
    await show_vehicle_menu(update, context, uid)

# ================== شرکت ==================

def company_menu_kb(has_company):
    if has_company:
        rows = [
            [sbtn("برداشت سود شرکت", "company:withdraw", STYLE_GOOD, emoji="company_withdraw")],
            [sbtn("استخدام (با یوزرنیم)", "company:hire_info", STYLE_INFO, emoji="company_hire")],
        ]
    else:
        rows = [[sbtn(f"ثبت شرکت ({fmt_short(10000)})", "company:create", STYLE_GOOD, emoji="company_create")]]
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_company_menu(update, context, uid):
    conn = get_conn()
    comp = conn.execute("SELECT * FROM companies WHERE owner_id=?", (uid,)).fetchone()
    conn.close()
    if comp:
        text = f"{EMOJI['company']} شرکت: {esc(comp['name'])}\nموجودی شرکت: {fmt(comp['balance'])}"
    else:
        text = f"{EMOJI['company']} هنوز شرکتی ثبت نکردی. هزینه ثبت: 10,000"
    await send_or_edit(update, text, company_menu_kb(bool(comp)), parse_mode="HTML")


async def create_company(update, context, uid):
    u = get_user(uid)
    price = 10000
    if u["cash"] < price:
        await update.callback_query.answer("پول نقد کافی نداری.", show_alert=True)
        return
    add_cash(uid, -price)
    conn = get_conn()
    conn.execute("INSERT INTO companies(owner_id, name, balance, created_at) VALUES(?,?,0,?)",
                 (uid, f"شرکت {u['name']}", now_str()))
    conn.commit()
    conn.close()
    log_history(uid, "یک شرکت ثبت کرد")
    await update.callback_query.answer("شرکتت ثبت شد.")
    await show_company_menu(update, context, uid)


async def withdraw_company(update, context, uid):
    conn = get_conn()
    comp = conn.execute("SELECT * FROM companies WHERE owner_id=?", (uid,)).fetchone()
    if not comp or comp["balance"] <= 0:
        conn.close()
        await update.callback_query.answer("چیزی برای برداشت نیست.", show_alert=True)
        return
    amount = comp["balance"]
    conn.execute("UPDATE companies SET balance=0 WHERE id=?", (comp["id"],))
    conn.commit()
    conn.close()
    add_cash(uid, amount)
    await update.callback_query.answer(f"{fmt(amount)} از شرکت برداشت شد.")
    await show_company_menu(update, context, uid)

# ================== بازار (اقتصاد پویا، قیمت‌ها دست کاربرها) ==================

def market_menu_kb():
    rows = [
        [sbtn("لیست آگهی‌های بازار", "market:list", STYLE_INFO, emoji="market_list")],
        [sbtn("فروش وسیله نقلیه‌ام", "market:sellvehicle", STYLE_GOOD, emoji="market_sellvehicle")],
        [sbtn("ثبت آگهی کالای دیگر", "market:sell", STYLE_GOOD, emoji="market_sell")],
        [sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")],
    ]
    return InlineKeyboardMarkup(rows)


async def show_market_menu(update, context, uid):
    await send_or_edit(update, f"{EMOJI['market']} بازار آزاد - قیمت‌ها رو خود کاربرا تعیین می‌کنن:",
                        market_menu_kb(), parse_mode="HTML")


async def show_my_vehicles_for_sale(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM vehicles WHERE user_id=?", (uid,)).fetchall()
    conn.close()
    if not rows:
        await send_or_edit(
            update, "فعلاً هیچ وسیله نقلیه‌ای تو دارایی‌هات نداری که بخوای بفروشی.", back_btn("cat:market"))
        return
    kb_rows = [
        [sbtn(f"{VEHICLES[r['vtype']]['name']} (کد #{r['id']})", f"market:pickveh:{r['id']}", STYLE_GOOD, emoji="market_pick")]
        for r in rows
    ]
    kb_rows.append([sbtn("بازگشت", "cat:market", STYLE_BAD, emoji="back")])
    await send_or_edit(update, f"{EMOJI['market_sellvehicle']} لطفاً وسیله نقلیه‌ای که می‌خوای بفروشی رو انتخاب کن:",
                        InlineKeyboardMarkup(kb_rows), parse_mode="HTML")


async def show_market_list(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM market ORDER BY id DESC LIMIT 15").fetchall()
    conn.close()
    if not rows:
        text = f"{EMOJI['market_list']} فعلا آگهی‌ای تو بازار نیست."
    else:
        lines = [f"#{r['id']} - {esc(r['item'])} × {r['qty']} - {fmt(r['price'])}" for r in rows]
        text = (f"{EMOJI['market_list']} <b>آگهی‌های اخیر بازار</b>:\n" + "\n".join(lines) +
                "\n\nبرای خرید: /buy شماره_آگهی")
    await send_or_edit(update, text, back_btn("cat:market"), parse_mode="HTML")


async def buy_market_item(update, context, uid, market_id):
    conn = get_conn()
    row = conn.execute("SELECT * FROM market WHERE id=?", (market_id,)).fetchone()
    if not row:
        conn.close()
        await update.message.reply_text("این آگهی وجود نداره یا قبلاً فروخته شده.")
        return
    if row["seller_id"] == uid:
        conn.close()
        await update.message.reply_text("متاسفانه نمی‌تونی آگهی خودت رو بخری.")
        return
    u = get_user(uid)
    if u["cash"] < row["price"]:
        conn.close()
        await update.message.reply_text("پول نقد کافی نداری.")
        return
    vehicle_id = row["vehicle_id"] if "vehicle_id" in row.keys() else None
    if vehicle_id:
        veh = conn.execute(
            "SELECT * FROM vehicles WHERE id=? AND user_id=?", (vehicle_id, row["seller_id"])).fetchone()
        if not veh:
            conn.execute("DELETE FROM market WHERE id=?", (market_id,))
            conn.commit()
            conn.close()
            await update.message.reply_text("متاسفانه این آگهی دیگه معتبر نیست.")
            return
        conn.execute("UPDATE vehicles SET user_id=? WHERE id=?", (uid, vehicle_id))
    add_cash(uid, -row["price"])
    add_cash(row["seller_id"], row["price"])
    conn.execute("DELETE FROM market WHERE id=?", (market_id,))
    conn.commit()
    conn.close()
    if vehicle_id:
        recalc_vehicle_bonus(uid)
        recalc_vehicle_bonus(row["seller_id"])
    await update.message.reply_text(f"✅ {row['item']} رو با موفقیت خریدی.")
    try:
        buyer = get_user(uid)
        await context.bot.send_message(
            row["seller_id"],
            f"🎉 خبر خوب! {esc(buyer['name'])} کالای «{esc(row['item'])}» تو رو خرید "
            f"و {fmt(row['price'])} به حسابت واریز شد.")
    except Exception:
        pass

# ================== ماموریت‌ها ==================

def missions_kb(uid):
    conn = get_conn()
    done_today = {r["mission_key"] for r in conn.execute(
        "SELECT mission_key FROM missions_done WHERE user_id=? AND day=?", (uid, today_str())).fetchall()}
    conn.close()
    rows = []
    for key, name, reward, xp in MISSION_POOL:
        done = key in done_today
        if done:
            btn = sbtn(name, "noop", emoji="mission_done")
        else:
            btn = sbtn(f"{name} (+{fmt_short(reward)})", f"mission:claim:{key}", STYLE_GOOD, emoji="mission_claim")
        rows.append([btn])
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_missions(update, context, uid):
    await send_or_edit(update, f"{EMOJI['missions']} ماموریت‌های امروز:", missions_kb(uid), parse_mode="HTML")


async def claim_mission(update, context, uid, key):
    conn = get_conn()
    exists = conn.execute("SELECT 1 FROM missions_done WHERE user_id=? AND mission_key=? AND day=?",
                           (uid, key, today_str())).fetchone()
    if exists:
        conn.close()
        await update.callback_query.answer("امروز قبلا گرفتیش.", show_alert=True)
        return
    reward = next(r for k, n, r, x in MISSION_POOL if k == key)
    xp = next(x for k, n, r, x in MISSION_POOL if k == key)
    conn.execute("INSERT INTO missions_done(user_id, mission_key, day) VALUES(?,?,?)", (uid, key, today_str()))
    conn.commit()
    conn.close()
    add_cash(uid, reward)
    add_xp(uid, xp)
    await update.callback_query.answer(f"{fmt(reward)} گرفتی.")
    await show_missions(update, context, uid)

# ================== رتبه‌بندی ==================

async def show_leaderboard(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM users WHERE banned=0").fetchall()
    conn.close()
    ranked = sorted(rows, key=lambda u: net_worth(u), reverse=True)[:10]
    lines = [f"{i+1}. {esc(r['name'])} - {fmt(net_worth(r))}" for i, r in enumerate(ranked)]
    text = (f"{EMOJI['leaderboard']} <b>ثروتمندترین کاربران</b>:\n" + "\n".join(lines)
            if lines else f"{EMOJI['leaderboard']} هنوز کاربری ثبت نشده.")
    await send_or_edit(update, text, back_btn(), parse_mode="HTML")

# ================== روابط اجتماعی ==================

def social_menu_kb():
    rows = [
        [sbtn("افزودن دوست", "social:friend", STYLE_GOOD, emoji="friend")],
        [sbtn("هدیه دادن", "social:gift", STYLE_GOOD, emoji="gift")],
        [sbtn("درخواست ازدواج", "social:marry", STYLE_GOOD, emoji="marry")],
        [sbtn("طلاق", "social:divorce", STYLE_BAD, emoji="divorce")],
        [sbtn("لیست دوستان", "social:list", STYLE_INFO, emoji="friend_list")],
        [sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")],
    ]
    return InlineKeyboardMarkup(rows)


async def show_social_menu(update, context, uid):
    await send_or_edit(update, f"{EMOJI['social']} روابط اجتماعی - برای هرکدوم یوزرنیم طرف رو با @ وارد کن:",
                        social_menu_kb(), parse_mode="HTML")


async def show_friend_list(update, context, uid):
    conn = get_conn()
    rows = conn.execute("SELECT * FROM friends WHERE user1=? OR user2=?", (uid, uid)).fetchall()
    conn.close()
    if not rows:
        text = f"{EMOJI['friend_list']} هنوز دوستی نداری."
    else:
        names = []
        for r in rows:
            other = r["user2"] if r["user1"] == uid else r["user1"]
            ou = get_user(other)
            if ou:
                names.append(esc(ou["name"]))
        text = f"{EMOJI['friend_list']} <b>دوستان تو</b>:\n" + "\n".join(names)
    await send_or_edit(update, text, back_btn("cat:social"), parse_mode="HTML")


async def divorce(update, context, uid):
    u = get_user(uid)
    if not u["married_to"]:
        await update.callback_query.answer("متاهل نیستی.", show_alert=True)
        return
    partner = u["married_to"]
    update_user(uid, married_to=None)
    update_user(partner, married_to=None)
    log_history(uid, "طلاق گرفت")
    await update.callback_query.answer("طلاق ثبت شد.")
    await show_social_menu(update, context, uid)

# ================== جرم و قانون ==================

def crime_menu_kb():
    rows = [[sbtn(f"{c['name']} (ریسک {c['risk']}%)", f"crime:do:{k}", STYLE_BAD, emoji="crime_do")]
            for k, c in CRIMES.items()]
    rows.append([sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")])
    return InlineKeyboardMarkup(rows)


async def show_crime_menu(update, context, uid):
    u = get_user(uid)
    if is_jailed(u):
        left = u["jailed_until"] - int(time.time())
        await send_or_edit(update, f"{EMOJI['crime']} تو زندانی هستی، {left // 60 + 1} دقیقه دیگه مونده.",
                            back_btn(), parse_mode="HTML")
        return
    await send_or_edit(update, f"{EMOJI['crime']} این کارها غیرقانونی‌ان و ریسک زندان دارن:", crime_menu_kb(),
                        parse_mode="HTML")


async def do_crime(update, context, uid, key):
    u = get_user(uid)
    if is_jailed(u):
        await update.callback_query.answer("تو زندانی هستی.", show_alert=True)
        return
    c = CRIMES[key]
    caught = random.randint(1, 100) <= c["risk"]
    if caught:
        jail_until = int(time.time()) + c["jail"] * 60
        new_cash = max(0, u["cash"] - c["fine"])
        update_user(uid, jailed_until=jail_until, cash=new_cash,
                     credit_score=max(0, u["credit_score"] - 5))
        log_history(uid, f"در حین {c['name']} دستگیر شد")
        await update.callback_query.answer(f"دستگیر شدی! {c['jail']} دقیقه زندان و {fmt(c['fine'])} جریمه.", show_alert=True)
    else:
        reward = random.randint(*c["reward"])
        add_cash(uid, reward)
        log_history(uid, f"{c['name']} کرد و {fmt(reward)} گرفت")
        await update.callback_query.answer(f"موفق شدی و {fmt(reward)} گرفتی.", show_alert=True)
    await show_crime_menu(update, context, uid)

# ================== پنل مدیریت ==================

def admin_menu_kb():
    rows = [
        [sbtn("آمار کلی", "admin:stats", STYLE_INFO, emoji="admin_stats")],
        [sbtn("دادن پول به کاربر", "admin:give", STYLE_GOOD, emoji="admin_give")],
        [sbtn("مسدود / رفع مسدودی کاربر", "admin:ban", STYLE_BAD, emoji="admin_ban")],
        [sbtn("مدیریت وسایل نقلیه فروشگاه", "admin:vehicles", STYLE_INFO, emoji="admin_vehicles")],
        [sbtn("پیام همگانی", "admin:broadcast", STYLE_INFO, emoji="admin_broadcast")],
        [sbtn("رویداد جهانی", "admin:event", STYLE_INFO, emoji="admin_event")],
        [sbtn("بازگشت", "menu", STYLE_BAD, emoji="back")],
    ]
    return InlineKeyboardMarkup(rows)


async def show_admin_menu(update, context, uid):
    # توجه: چک دسترسی ادمین قبل از فراخوانی این تابع تو button_handler انجام میشه
    await send_or_edit(update, f"{EMOJI['admin']} پنل مدیریت:", admin_menu_kb(), parse_mode="HTML")


async def show_admin_vehicle_menu(update, context, uid):
    rows = []
    for key, v in VEHICLES.items():
        price, stock = get_vehicle_shop(key)
        stock_txt = "نامحدود" if stock is None else str(stock)
        rows.append([sbtn(f"{v['name']} | قیمت: {fmt_short(price)} | موجودی: {stock_txt}",
                           f"admin:vehedit:{key}", STYLE_INFO, emoji="admin_vehicle_edit")])
    rows.append([sbtn("بازگشت", "cat:admin", STYLE_BAD, emoji="back")])
    await send_or_edit(update, f"{EMOJI['admin_vehicles']} مدیریت وسایل نقلیه فروشگاه:",
                        InlineKeyboardMarkup(rows), parse_mode="HTML")


async def admin_stats(update, context, uid):
    conn = get_conn()
    total_users = conn.execute("SELECT COUNT(*) c FROM users").fetchone()["c"]
    total_cash = conn.execute("SELECT COALESCE(SUM(cash+bank),0) s FROM users").fetchone()["s"]
    banned = conn.execute("SELECT COUNT(*) c FROM users WHERE banned=1").fetchone()["c"]
    conn.close()
    text = (f"{EMOJI['admin_stats']} <b>آمار کلی</b>\n\n"
            f"تعداد کاربران: {total_users}\nمجموع نقدینگی در بازی: {fmt(total_cash)}\nمسدود شده: {banned}")
    await send_or_edit(update, text, back_btn("cat:admin"), parse_mode="HTML")


async def admin_event(update, context, uid):
    events = [
        ("رکود اقتصادی", "recession"),
        ("رونق اقتصادی", "boom"),
        ("بحران انرژی", "energy_crisis"),
        ("جشنواره شهری", "festival"),
    ]
    rows = [[sbtn(name, f"admin:doevent:{key}", STYLE_INFO, emoji="event_item")] for name, key in events]
    rows.append([sbtn("بازگشت", "cat:admin", STYLE_BAD, emoji="back")])
    await send_or_edit(update, f"{EMOJI['admin_event']} یک رویداد جهانی رو فعال کن:",
                        InlineKeyboardMarkup(rows), parse_mode="HTML")


async def admin_do_event(update, context, uid, key):
    conn = get_conn()
    if key == "recession":
        conn.execute("UPDATE users SET bank = bank - CAST(bank*0.05 AS INT)")
        text = "رکود اقتصادی اعلام شد. موجودی بانکی همه کمی کم شد."
    elif key == "boom":
        conn.execute("UPDATE users SET bank = bank + CAST(bank*0.05 AS INT)")
        text = "رونق اقتصادی اعلام شد. موجودی بانکی همه کمی زیاد شد."
    elif key == "energy_crisis":
        conn.execute("UPDATE users SET energy = MAX(0, energy - 20)")
        text = "بحران انرژی! انرژی همه کاربرا افت کرد."
    else:
        conn.execute("UPDATE users SET happiness = MIN(100, happiness + 15)")
        text = "جشنواره شهری برگزار شد. شادی همه بالا رفت."
    conn.commit()
    conn.close()
    await update.callback_query.answer("رویداد اجرا شد.")
    await send_or_edit(update, f"{EMOJI['admin_event']} {text}", back_btn("cat:admin"), parse_mode="HTML")

# ================== هندلر دکمه‌ها ==================

async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    uid = query.from_user.id
    ensure_user(uid, query.from_user.username, query.from_user.first_name)
    data = query.data

    if data == "noop":
        await query.answer()
        return
    if data == "menu":
        await query.answer()
        await query.edit_message_text("منوی اصلی:", reply_markup=main_menu(uid))
        return

    parts = data.split(":")
    root = parts[0]

    if root == "cat":
        sub = parts[1]
        if sub == "admin":
            if not is_admin(uid):
                await query.answer("دسترسی نداری.", show_alert=True)
                return
            await query.answer()
            await show_admin_menu(update, context, uid)
            return
        await query.answer()
        if sub == "profile":
            await show_profile(update, context, uid)
        elif sub == "job":
            await show_job_menu(update, context, uid)
        elif sub == "bank":
            await show_bank_menu(update, context, uid)
        elif sub == "skill":
            await show_skill_menu(update, context, uid)
        elif sub == "education":
            await show_education(update, context, uid)
        elif sub == "estate":
            await show_estate_menu(update, context, uid)
        elif sub == "vehicle":
            await show_vehicle_menu(update, context, uid)
        elif sub == "company":
            await show_company_menu(update, context, uid)
        elif sub == "market":
            await show_market_menu(update, context, uid)
        elif sub == "missions":
            await show_missions(update, context, uid)
        elif sub == "leaderboard":
            await show_leaderboard(update, context, uid)
        elif sub == "social":
            await show_social_menu(update, context, uid)
        elif sub == "crime":
            await show_crime_menu(update, context, uid)
        return

    if root == "job":
        action = parts[1]
        if action == "apply":
            await job_apply(update, context, uid, parts[2])
        elif action == "work":
            await job_work(update, context, uid)
        elif action == "quit":
            await job_quit(update, context, uid)
        return

    if root == "bank":
        action = parts[1]
        await query.answer()
        if action == "dep":
            await ask_amount(update, context, "bank_dep", "چقدر می‌خوای به بانک واریز کنی؟ عدد رو بفرست.")
        elif action == "wd":
            await ask_amount(update, context, "bank_wd", "چقدر می‌خوای از بانک برداشت کنی؟ عدد رو بفرست.")
        elif action == "transfer":
            context.user_data["awaiting"] = "bank_transfer"
            await query.edit_message_text(
                "لطفاً شماره کارت گیرنده و مبلغ رو با فاصله ارسال کنید.\n"
                "مثال: 6219861912345678 50000",
                reply_markup=back_btn("cat:bank"))
        elif action == "loan":
            await ask_amount(update, context, "bank_loan", "چقدر وام می‌خوای؟ (سقف بر اساس اعتبار بانکی‌ته)")
        elif action == "pay":
            await ask_amount(update, context, "bank_pay", "چقدر می‌خوای بابت وام پرداخت کنی؟")
        return

    if root == "skill":
        action = parts[1]
        if action == "train":
            await skill_train(update, context, uid, parts[2])
        return

    if root == "edu":
        if parts[1] == "buy":
            await query.answer()
            await buy_course(update, context, uid, parts[2])
        return

    if root == "estate":
        action = parts[1]
        if action == "buy":
            await buy_property(update, context, uid, parts[2])
        elif action == "mine":
            await query.answer()
            await show_my_estate(update, context, uid)
        return

    if root == "vehicle":
        if parts[1] == "buy":
            await buy_vehicle(update, context, uid, parts[2])
        return

    if root == "company":
        action = parts[1]
        await query.answer()
        if action == "create":
            await create_company(update, context, uid)
        elif action == "withdraw":
            await withdraw_company(update, context, uid)
        elif action == "hire_info":
            await query.edit_message_text(
                "برای استخدام از دستور زیر تو گروه یا خصوصی استفاده کن:\n/hire @یوزرنیم مبلغ_حقوق",
                reply_markup=back_btn("cat:company"))
        return

    if root == "market":
        action = parts[1]
        await query.answer()
        if action == "list":
            await show_market_list(update, context, uid)
        elif action == "sell":
            context.user_data["awaiting"] = "market_sell"
            await query.edit_message_text(
                "آگهی فروش رو به این شکل بفرست:\nنام‌کالا مقدار قیمت\nمثال: کتاب 1 2000",
                reply_markup=back_btn("cat:market"))
        elif action == "sellvehicle":
            await show_my_vehicles_for_sale(update, context, uid)
        elif action == "pickveh":
            veh_id = int(parts[2])
            conn = get_conn()
            veh = conn.execute("SELECT * FROM vehicles WHERE id=? AND user_id=?", (veh_id, uid)).fetchone()
            conn.close()
            if not veh:
                await query.answer("این وسیله دیگه در دارایی تو نیست.", show_alert=True)
                return
            context.user_data["awaiting"] = "market_sell_vehicle_price"
            context.user_data["sell_vehicle_id"] = veh_id
            await query.edit_message_text(
                f"لطفاً قیمت فروش «{VEHICLES[veh['vtype']]['name']}» رو به تومان بفرست:",
                reply_markup=back_btn("cat:market"))
        return

    if root == "mission":
        if parts[1] == "claim":
            await claim_mission(update, context, uid, parts[2])
        return

    if root == "social":
        action = parts[1]
        await query.answer()
        if action == "list":
            await show_friend_list(update, context, uid)
        elif action == "divorce":
            await divorce(update, context, uid)
        elif action in ("friend", "gift", "marry"):
            context.user_data["awaiting"] = f"social_{action}"
            prompt = {
                "friend": "یوزرنیم فردی که می‌خوای دوستش بشی رو بفرست (با @)",
                "gift": "به این شکل بفرست: @یوزرنیم مبلغ",
                "marry": "یوزرنیم فردی که می‌خوای بهش پیشنهاد ازدواج بدی رو بفرست (با @)",
            }[action]
            await query.edit_message_text(prompt, reply_markup=back_btn("cat:social"))
        return

    if root == "crime":
        if parts[1] == "do":
            await do_crime(update, context, uid, parts[2])
        return

    if root == "admin":
        action = parts[1]
        if not is_admin(uid):
            await query.answer("دسترسی نداری.", show_alert=True)
            return
        await query.answer()
        if action == "stats":
            await admin_stats(update, context, uid)
        elif action == "event":
            await admin_event(update, context, uid)
        elif action == "doevent":
            await admin_do_event(update, context, uid, parts[2])
        elif action == "give":
            context.user_data["awaiting"] = "admin_give"
            await query.edit_message_text("به این شکل بفرست: @یوزرنیم مبلغ", reply_markup=back_btn("cat:admin"))
        elif action == "ban":
            context.user_data["awaiting"] = "admin_ban"
            await query.edit_message_text(
                "به این شکل بفرست: @یوزرنیم on یا @یوزرنیم off\n(on یعنی مسدود کن)",
                reply_markup=back_btn("cat:admin"))
        elif action == "broadcast":
            context.user_data["awaiting"] = "admin_broadcast"
            await query.edit_message_text("متن پیام همگانی رو بفرست:", reply_markup=back_btn("cat:admin"))
        elif action == "vehicles":
            await show_admin_vehicle_menu(update, context, uid)
        elif action == "vehedit":
            key = parts[2]
            context.user_data["awaiting"] = "admin_vehicle_edit"
            context.user_data["admin_vehicle_key"] = key
            await query.edit_message_text(
                f"برای «{VEHICLES[key]['name']}» به این شکل بفرست:\n"
                "قیمت_جدید تعداد_اضافه‌شونده_به_موجودی\n"
                "مثال: 1200 2000\n"
                "اگه نمی‌خوای قیمت رو عوض کنی، به جاش عدد 0 بفرست.",
                reply_markup=back_btn("cat:admin"))
        return

# ================== هندلر متن (برای مراحلی که ورودی عددی/متنی لازمه) ==================

async def text_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    uid = update.effective_user.id
    ensure_user(uid, update.effective_user.username, update.effective_user.first_name)
    awaiting = context.user_data.get("awaiting")
    if not awaiting:
        return
    text = normalize_digits(update.message.text.strip())
    u = get_user(uid)

    try:
        if awaiting == "bank_dep":
            amount = int(text)
            if amount <= 0 or amount > u["cash"]:
                await update.message.reply_text("مبلغ نامعتبره یا پول نقد کافی نداری.")
                return
            update_user(uid, cash=u["cash"] - amount, bank=u["bank"] + amount)
            await update.message.reply_text(f"{fmt(amount)} به بانک واریز شد.", reply_markup=main_menu(uid))

        elif awaiting == "bank_wd":
            amount = int(text)
            if amount <= 0 or amount > u["bank"]:
                await update.message.reply_text("مبلغ نامعتبره یا موجودی بانک کافی نیست.")
                return
            update_user(uid, cash=u["cash"] + amount, bank=u["bank"] - amount)
            await update.message.reply_text(f"{fmt(amount)} از بانک برداشت شد.", reply_markup=main_menu(uid))

        elif awaiting == "bank_transfer":
            parts_in = text.split()
            if len(parts_in) < 2:
                await update.message.reply_text(
                    "فرمت درست نیست. لطفاً شماره کارت و مبلغ رو با فاصله بفرست.\nمثال: 6219861912345678 50000")
                return
            amount_s = parts_in[-1]
            card_s = "".join(parts_in[:-1])   # اگه شماره کارت با فاصله کپی شده باشه هم درست کار کنه
            try:
                amount = int(amount_s)
            except ValueError:
                await update.message.reply_text("مبلغ باید یک عدد باشه.")
                return
            target = resolve_by_card(card_s)
            if not target:
                await update.message.reply_text(
                    "متاسفانه شماره کارتی با این مشخصات پیدا نشد. لطفاً شماره کارت رو دوباره چک کن.")
                return
            if target == uid:
                await update.message.reply_text("نمی‌تونی به کارت خودت انتقال بدی.")
                return
            if amount <= 0 or amount > u["bank"]:
                await update.message.reply_text("مبلغ نامعتبره یا موجودی بانکت کافی نیست.")
                return
            update_user(uid, bank=u["bank"] - amount)
            add_bank(target, amount)
            log_history(uid, f"{fmt(amount)} به کاربر دیگری انتقال داد")
            log_history(target, f"{fmt(amount)} از کاربر دیگری دریافت کرد")
            await update.message.reply_text(
                f"✅ انتقال با موفقیت انجام شد. {fmt(amount)} به حساب مقصد واریز شد.",
                reply_markup=main_menu(uid))
            try:
                sender = get_user(uid)
                await context.bot.send_message(
                    target,
                    f"💳 واریز جدید\n{esc(sender['name'])} مبلغ {fmt(amount)} به حساب بانکی شما واریز کرد.")
            except Exception:
                pass

        elif awaiting == "bank_loan":
            amount = int(text)
            max_loan = u["credit_score"] * 1000
            if amount <= 0 or u["loan"] + amount > max_loan:
                await update.message.reply_text(f"سقف وام مجاز تو {fmt(max_loan)} هست.")
                return
            update_user(uid, cash=u["cash"] + amount, loan=u["loan"] + amount)
            await update.message.reply_text(f"{fmt(amount)} وام گرفتی.", reply_markup=main_menu(uid))

        elif awaiting == "bank_pay":
            amount = int(text)
            if amount <= 0 or amount > u["cash"] or amount > u["loan"]:
                await update.message.reply_text("مبلغ نامعتبره.")
                return
            update_user(uid, cash=u["cash"] - amount, loan=u["loan"] - amount,
                        credit_score=min(100, u["credit_score"] + 2))
            await update.message.reply_text(f"{fmt(amount)} بابت وام پرداخت شد.", reply_markup=main_menu(uid))

        elif awaiting == "market_sell":
            item, qty, price = text.rsplit(" ", 2)
            qty = int(qty)
            price = int(price)
            conn = get_conn()
            conn.execute("INSERT INTO market(seller_id, item, price, qty, created_at) VALUES(?,?,?,?,?)",
                         (uid, item, price, qty, now_str()))
            conn.commit()
            conn.close()
            await update.message.reply_text("✅ آگهی با موفقیت ثبت شد.", reply_markup=main_menu(uid))

        elif awaiting == "market_sell_vehicle_price":
            veh_id = context.user_data.get("sell_vehicle_id")
            try:
                price = int(text)
            except ValueError:
                await update.message.reply_text("قیمت باید یک عدد باشه، لطفاً دوباره امتحان کن.")
                return
            if price <= 0:
                await update.message.reply_text("قیمت باید بزرگ‌تر از صفر باشه.")
                return
            conn = get_conn()
            veh = conn.execute("SELECT * FROM vehicles WHERE id=? AND user_id=?", (veh_id, uid)).fetchone()
            if not veh:
                conn.close()
                await update.message.reply_text("این وسیله دیگه در دارایی تو نیست.")
                return
            conn.execute(
                "INSERT INTO market(seller_id, item, price, qty, created_at, vehicle_id) VALUES(?,?,?,1,?,?)",
                (uid, VEHICLES[veh["vtype"]]["name"], price, now_str(), veh_id))
            conn.commit()
            conn.close()
            context.user_data["sell_vehicle_id"] = None
            await update.message.reply_text(
                "✅ آگهی فروش با موفقیت ثبت شد. تا وقتی خریداری پیدا نشه، وسیله همچنان مال توئه.",
                reply_markup=main_menu(uid))

        elif awaiting == "social_friend":
            target = resolve_target(context, text)
            if not target or target == uid:
                await update.message.reply_text("کاربر پیدا نشد.")
                return
            conn = get_conn()
            conn.execute("INSERT OR IGNORE INTO friends(user1, user2) VALUES(?,?)",
                         (min(uid, target), max(uid, target)))
            conn.commit()
            conn.close()
            await update.message.reply_text("دوست اضافه شد.", reply_markup=main_menu(uid))

        elif awaiting == "social_gift":
            target_s, amount_s = text.split()
            target = resolve_target(context, target_s)
            amount = int(amount_s)
            if not target or target == uid:
                await update.message.reply_text("کاربر پیدا نشد.")
                return
            if amount <= 0 or amount > u["cash"]:
                await update.message.reply_text("پول نقد کافی نداری.")
                return
            add_cash(uid, -amount)
            add_cash(target, amount)
            await update.message.reply_text("هدیه ارسال شد.", reply_markup=main_menu(uid))
            try:
                await context.bot.send_message(target, f"{fmt(amount)} هدیه دریافت کردی.")
            except Exception:
                pass

        elif awaiting == "social_marry":
            target = resolve_target(context, text)
            if not target or target == uid:
                await update.message.reply_text("کاربر پیدا نشد.")
                return
            conn = get_conn()
            conn.execute("INSERT OR IGNORE INTO marriage_requests(from_id, to_id) VALUES(?,?)", (uid, target))
            conn.commit()
            conn.close()
            await update.message.reply_text("درخواست ازدواج ارسال شد.", reply_markup=main_menu(uid))
            try:
                await context.bot.send_message(
                    target, f"{u['name']} بهت پیشنهاد ازدواج داده. با /accept_{uid} قبول کن.")
            except Exception:
                pass

        elif awaiting == "admin_give":
            if not is_admin(uid):
                return
            target_s, amount_s = text.split()
            target = resolve_target(context, target_s)
            amount = int(amount_s)
            if not target:
                await update.message.reply_text("کاربر پیدا نشد.")
                return
            add_cash(target, amount)
            await update.message.reply_text("انجام شد.", reply_markup=main_menu(uid))

        elif awaiting == "admin_ban":
            if not is_admin(uid):
                return
            target_s, mode = text.split()
            target = resolve_target(context, target_s)
            if not target:
                await update.message.reply_text("کاربر پیدا نشد.")
                return
            update_user(target, banned=1 if mode == "on" else 0)
            await update.message.reply_text("انجام شد.", reply_markup=main_menu(uid))

        elif awaiting == "admin_vehicle_edit":
            if not is_admin(uid):
                return
            key = context.user_data.get("admin_vehicle_key")
            if key not in VEHICLES:
                await update.message.reply_text("خطا: نوع وسیله نامعتبره.")
                return
            price_s, add_s = text.split()
            new_price = int(price_s)
            add_stock = int(add_s)
            if add_stock < 0:
                await update.message.reply_text("تعداد اضافه‌شونده نمی‌تونه منفی باشه.")
                return
            conn = get_conn()
            row = conn.execute("SELECT * FROM vehicle_shop WHERE vtype=?", (key,)).fetchone()
            if row:
                cur_price = row["price"] if new_price == 0 else new_price
                cur_stock = (row["stock"] or 0) + add_stock if row["stock"] is not None else add_stock
                conn.execute("UPDATE vehicle_shop SET price=?, stock=? WHERE vtype=?", (cur_price, cur_stock, key))
            else:
                base_price = VEHICLES[key]["price"] if new_price == 0 else new_price
                conn.execute("INSERT INTO vehicle_shop(vtype, price, stock) VALUES(?,?,?)",
                             (key, base_price, add_stock))
            conn.commit()
            conn.close()
            context.user_data["admin_vehicle_key"] = None
            await update.message.reply_text(
                f"✅ تنظیمات «{VEHICLES[key]['name']}» با موفقیت به‌روزرسانی شد.", reply_markup=main_menu(uid))

        elif awaiting == "admin_broadcast":
            if not is_admin(uid):
                return
            conn = get_conn()
            all_ids = [r["user_id"] for r in conn.execute("SELECT user_id FROM users").fetchall()]
            conn.close()
            sent = 0
            for other in all_ids:
                try:
                    await context.bot.send_message(other, f"پیام مدیریت:\n{text}")
                    sent += 1
                except Exception:
                    pass
            await update.message.reply_text(f"پیام برای {sent} کاربر ارسال شد.", reply_markup=main_menu(uid))

    except (ValueError, IndexError):
        await update.message.reply_text("فرمت پیام درست نبود، دوباره تلاش کن.")
        return

    context.user_data["awaiting"] = None

# ================== دستورات مستقیم ==================

async def buy_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.args:
        await update.message.reply_text("استفاده: /buy شماره_آگهی")
        return
    try:
        market_id = int(context.args[0])
    except ValueError:
        await update.message.reply_text("شماره آگهی نامعتبره.")
        return
    await buy_market_item(update, context, update.effective_user.id, market_id)


async def hire_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    uid = update.effective_user.id
    if len(context.args) < 2:
        await update.message.reply_text("استفاده: /hire @یوزرنیم مبلغ_حقوق")
        return
    conn = get_conn()
    comp = conn.execute("SELECT * FROM companies WHERE owner_id=?", (uid,)).fetchone()
    if not comp:
        conn.close()
        await update.message.reply_text("اول باید شرکت ثبت کنی.")
        return
    target = resolve_target(context, context.args[0])
    if not target:
        conn.close()
        await update.message.reply_text("کاربر پیدا نشد.")
        return
    try:
        salary = int(context.args[1])
    except ValueError:
        conn.close()
        await update.message.reply_text("مبلغ حقوق نامعتبره.")
        return
    conn.execute("INSERT OR REPLACE INTO company_employees(company_id, user_id, salary) VALUES(?,?,?)",
                 (comp["id"], target, salary))
    conn.commit()
    conn.close()
    await update.message.reply_text("کارمند استخدام شد.")
    try:
        await context.bot.send_message(target, f"تو شرکت {comp['name']} استخدام شدی، حقوق روزانه: {fmt(salary)}")
    except Exception:
        pass


async def accept_marriage_generic(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """دستور /accept_<from_id> برای قبول درخواست ازدواج"""
    cmd = update.message.text.split()[0]
    try:
        from_id = int(cmd.split("_", 1)[1])
    except (IndexError, ValueError):
        return
    uid = update.effective_user.id
    conn = get_conn()
    row = conn.execute("SELECT * FROM marriage_requests WHERE from_id=? AND to_id=?", (from_id, uid)).fetchone()
    if not row:
        conn.close()
        await update.message.reply_text("همچین درخواستی پیدا نشد.")
        return
    conn.execute("DELETE FROM marriage_requests WHERE from_id=? AND to_id=?", (from_id, uid))
    conn.commit()
    conn.close()
    update_user(from_id, married_to=uid)
    update_user(uid, married_to=from_id)
    log_history(uid, "ازدواج کرد")
    log_history(from_id, "ازدواج کرد")
    await update.message.reply_text("ازدواج ثبت شد، تبریک.")
    try:
        await context.bot.send_message(from_id, "درخواست ازدواجت قبول شد.")
    except Exception:
        pass


async def admin_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    uid = update.effective_user.id
    if not is_admin(uid):
        return
    await update.message.reply_text("پنل مدیریت:", reply_markup=admin_menu_kb())

# ================== کارهای دوره‌ای (تیک بازی) ==================

async def decay_tick(context: ContextTypes.DEFAULT_TYPE):
    conn = get_conn()
    conn.execute("""UPDATE users SET
        hunger = MAX(0, hunger - 5),
        sleep = MAX(0, sleep - 5),
        energy = MIN(100, energy + 8)
        WHERE banned = 0
    """)
    conn.execute("""UPDATE users SET
        health = MAX(0, health - 3), happiness = MAX(0, happiness - 3)
        WHERE hunger <= 10 OR sleep <= 10
    """)
    conn.commit()
    conn.close()


async def daily_tick(context: ContextTypes.DEFAULT_TYPE):
    conn = get_conn()
    users = conn.execute("SELECT * FROM users WHERE banned=0").fetchall()
    for u in users:
        # درآمد ملک
        income = conn.execute("SELECT COALESCE(SUM(income),0) s FROM properties WHERE user_id=?",
                               (u["user_id"],)).fetchone()["s"]
        if income:
            conn.execute("UPDATE users SET bank = bank + ? WHERE user_id=?", (income, u["user_id"]))
        # بهره وام
        if u["loan"] > 0:
            interest = max(1, int(u["loan"] * 0.02))
            conn.execute("UPDATE users SET loan = loan + ? WHERE user_id=?", (interest, u["user_id"]))
        # حقوق کارمندهای شرکت
    # پرداخت حقوق کارمندها از موجودی شرکت
    employees = conn.execute("SELECT * FROM company_employees").fetchall()
    for e in employees:
        comp = conn.execute("SELECT * FROM companies WHERE id=?", (e["company_id"],)).fetchone()
        if comp and comp["balance"] >= e["salary"]:
            conn.execute("UPDATE companies SET balance = balance - ? WHERE id=?", (e["salary"], comp["id"]))
            conn.execute("UPDATE users SET cash = cash + ? WHERE user_id=?", (e["salary"], e["user_id"]))
    conn.commit()
    conn.close()
    log.info("daily_tick اجرا شد")

# ================== اجرای برنامه ==================

def main():
    init_db()
    app = Application.builder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("help", help_cmd))
    app.add_handler(CommandHandler("menu", menu_cmd))
    app.add_handler(CommandHandler("buy", buy_cmd))
    app.add_handler(CommandHandler("hire", hire_cmd))
    app.add_handler(CommandHandler("admin", admin_cmd))
    app.add_handler(MessageHandler(filters.Regex(r"^/accept_\d+"), accept_marriage_generic))
    app.add_handler(CallbackQueryHandler(button_handler))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, text_handler))

    app.job_queue.run_repeating(decay_tick, interval=DECAY_SECONDS, first=DECAY_SECONDS)
    app.job_queue.run_repeating(daily_tick, interval=GAME_DAY_SECONDS, first=GAME_DAY_SECONDS)

    log.info("ربات روشن شد")
    app.run_polling()


if __name__ == "__main__":
    main()
