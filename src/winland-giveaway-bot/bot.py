#!/usr/bin/env python3
"""
🤖 WinLand-style Giveaway & Raffle Bot — Professional Version
Telegram Giveaway/Raffle Bot with Admin Panel, Multi-Giveaway, Referral & Points System
"""

import asyncio
import logging
import random
import sqlite3
import string
from datetime import datetime, timedelta
from typing import Optional, Dict, List, Tuple
from uuid import uuid4

from telegram import (
    Update, InlineKeyboardButton, InlineKeyboardMarkup,
    ChatMember, Chat, BotCommand, BotCommandScopeDefault
)
from telegram.constants import ParseMode
from telegram.ext import (
    Application, CommandHandler, CallbackQueryHandler,
    MessageHandler, filters, ContextTypes, ConversationHandler
)
from telegram.error import TelegramError

# =============================================================================
# CONFIGURATION
# =============================================================================

CONFIG = {
    "BOT_TOKEN": "YOUR_BOT_TOKEN_HERE",  # از @BotFather بگیر
    
    "ADMIN_IDS": [123456789],  # آیدی عددی ادمین‌ها
    
    "CHANNEL_USERNAME": "@YourChannel",  # کانال اصلی برای عضوگیری
    "CHANNEL_ID": -1001234567890,  # آیدی عددی کانال
    
    "SUPPORT_LINK": "https://t.me/your_support",
    "BOT_USERNAME": "YourBot",  # یوزرنیم بات بدون @
    
    # تنظیمات گیو‌آوی
    "DEFAULT_WINNER_COUNT": 1,
    "MAX_WINNER_COUNT": 50,
    "MIN_PARTICIPANTS_FOR_AUTO_DRAW": 2,
    
    # امتیاز و ارجاع
    "REFERRAL_BONUS": 50,  # امتیاز به ازای هر دعوت
    "DAILY_BONUS": 10,  # امتیاز روزانه
    "JOIN_BONUS": 25,  # امتیاز اولیه عضویت
    
    # محدودیت‌ها
    "MAX_ACTIVE_GIVEAWAYS": 10,
    "COOLDOWN_HOURS": 24,  # ساعت بین شرکت در گیوآوی
    
    # دیتابیس
    "DB_PATH": "giveaway_bot.db",
    
    # لاگ
    "LOG_LEVEL": "INFO",
}


# =============================================================================
# DATABASE LAYER
# =============================================================================

class Database:
    """SQLite database manager for the bot."""
    
    def __init__(self, db_path: str):
        self.db_path = db_path
        self.conn = sqlite3.connect(db_path, check_same_thread=False)
        self.conn.row_factory = sqlite3.Row
        self._create_tables()
    
    def _create_tables(self):
        cursor = self.conn.cursor()
        
        # Users table
        cursor.executescript("""
            CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY,
                username TEXT,
                first_name TEXT,
                last_name TEXT,
                points INTEGER DEFAULT 0,
                total_joins INTEGER DEFAULT 0,
                total_wins INTEGER DEFAULT 0,
                referral_code TEXT UNIQUE,
                referred_by INTEGER DEFAULT NULL,
                join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_daily TIMESTAMP DEFAULT NULL,
                banned INTEGER DEFAULT 0
            );
            
            CREATE TABLE IF NOT EXISTS giveaways (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT DEFAULT '',
                prize TEXT NOT NULL,
                prize_count INTEGER DEFAULT 1,
                winner_count INTEGER DEFAULT 1,
                created_by INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ends_at TIMESTAMP,
                status TEXT DEFAULT 'active',
                min_points INTEGER DEFAULT 0,
                require_channel_join INTEGER DEFAULT 1,
                max_participants INTEGER DEFAULT 0,
                image_url TEXT DEFAULT NULL,
                winners TEXT DEFAULT NULL,
                drawn_at TIMESTAMP DEFAULT NULL,
                FOREIGN KEY (created_by) REFERENCES users(user_id)
            );
            
            CREATE TABLE IF NOT EXISTS participants (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                giveaway_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ticket_count INTEGER DEFAULT 1,
                UNIQUE(giveaway_id, user_id),
                FOREIGN KEY (giveaway_id) REFERENCES giveaways(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id)
            );
            
            CREATE TABLE IF NOT EXISTS referral_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                referrer_id INTEGER NOT NULL,
                referred_id INTEGER NOT NULL,
                points_earned INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (referrer_id) REFERENCES users(user_id),
                FOREIGN KEY (referred_id) REFERENCES users(user_id)
            );
            
            CREATE TABLE IF NOT EXISTS daily_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                date TEXT NOT NULL,
                UNIQUE(user_id, date),
                FOREIGN KEY (user_id) REFERENCES users(user_id)
            );
        """)
        self.conn.commit()
    
    # ---------- Users ----------
    
    def get_user(self, user_id: int) -> Optional[Dict]:
        cursor = self.conn.execute(
            "SELECT * FROM users WHERE user_id = ?", (user_id,)
        )
        row = cursor.fetchone()
        return dict(row) if row else None
    
    def create_user(self, user_id: int, username: str = None,
                    first_name: str = None, last_name: str = None,
                    referred_by: int = None) -> Dict:
        referral_code = self._generate_referral_code(user_id)
        
        self.conn.execute("""
            INSERT OR IGNORE INTO users 
            (user_id, username, first_name, last_name, referral_code, referred_by)
            VALUES (?, ?, ?, ?, ?, ?)
        """, (user_id, username, first_name, last_name, referral_code, referred_by))
        self.conn.commit()
        
        user = self.get_user(user_id)
        
        # Give join bonus if first time
        if referred_by:
            self.add_points(user_id, CONFIG["JOIN_BONUS"])
            self.add_points(referred_by, CONFIG["REFERRAL_BONUS"])
            self._log_referral(referred_by, user_id, CONFIG["REFERRAL_BONUS"])
        
        return user
    
    def add_points(self, user_id: int, points: int):
        self.conn.execute(
            "UPDATE users SET points = points + ? WHERE user_id = ?",
            (points, user_id)
        )
        self.conn.commit()
    
    def deduct_points(self, user_id: int, points: int) -> bool:
        user = self.get_user(user_id)
        if user and user["points"] >= points:
            self.conn.execute(
                "UPDATE users SET points = points - ? WHERE user_id = ?",
                (points, user_id)
            )
            self.conn.commit()
            return True
        return False
    
    def update_username(self, user_id: int, username: str, first_name: str, last_name: str):
        self.conn.execute("""
            UPDATE users SET username = ?, first_name = ?, last_name = ?
            WHERE user_id = ?
        """, (username, first_name, last_name, user_id))
        self.conn.commit()
    
    def _generate_referral_code(self, user_id: int) -> str:
        chars = string.ascii_uppercase + string.digits
        code = ''.join(random.choices(chars, k=8))
        return f"REF{code}{user_id % 100:02d}"
    
    def get_user_by_referral(self, code: str) -> Optional[Dict]:
        cursor = self.conn.execute(
            "SELECT * FROM users WHERE referral_code = ?", (code,)
        )
        row = cursor.fetchone()
        return dict(row) if row else None
    
    def _log_referral(self, referrer_id: int, referred_id: int, points: int):
        self.conn.execute("""
            INSERT INTO referral_log (referrer_id, referred_id, points_earned)
            VALUES (?, ?, ?)
        """, (referrer_id, referred_id, points))
        self.conn.commit()
    
    def get_referral_count(self, user_id: int) -> int:
        cursor = self.conn.execute(
            "SELECT COUNT(*) as count FROM users WHERE referred_by = ?",
            (user_id,)
        )
        return cursor.fetchone()["count"]
    
    # ---------- Daily Bonus ----------
    
    def can_claim_daily(self, user_id: int) -> bool:
        today = datetime.now().strftime("%Y-%m-%d")
        cursor = self.conn.execute(
            "SELECT id FROM daily_log WHERE user_id = ? AND date = ?",
            (user_id, today)
        )
        return cursor.fetchone() is None
    
    def claim_daily(self, user_id: int) -> bool:
        if not self.can_claim_daily(user_id):
            return False
        
        today = datetime.now().strftime("%Y-%m-%d")
        self.conn.execute("""
            INSERT INTO daily_log (user_id, date) VALUES (?, ?)
        """, (user_id, today))
        self.add_points(user_id, CONFIG["DAILY_BONUS"])
        self.conn.commit()
        return True
    
    # ---------- Giveaways ----------
    
    def create_giveaway(self, title: str, description: str, prize: str,
                        winner_count: int, created_by: int, ends_at: datetime,
                        min_points: int = 0, require_channel: bool = True,
                        max_participants: int = 0, image_url: str = None) -> int:
        cursor = self.conn.execute("""
            INSERT INTO giveaways 
            (title, description, prize, winner_count, created_by, 
             ends_at, min_points, require_channel_join, max_participants, image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (title, description, prize, winner_count, created_by,
              ends_at.isoformat(), min_points, int(require_channel),
              max_participants, image_url))
        self.conn.commit()
        return cursor.lastrowid
    
    def get_giveaway(self, giveaway_id: int) -> Optional[Dict]:
        cursor = self.conn.execute(
            "SELECT * FROM giveaways WHERE id = ?", (giveaway_id,)
        )
        row = cursor.fetchone()
        return dict(row) if row else None
    
    def get_active_giveaways(self) -> List[Dict]:
        cursor = self.conn.execute("""
            SELECT * FROM giveaways 
            WHERE status = 'active' AND ends_at > ?
            ORDER BY created_at DESC
        """, (datetime.now().isoformat(),))
        return [dict(row) for row in cursor.fetchall()]
    
    def get_all_giveaways(self, limit: int = 50) -> List[Dict]:
        cursor = self.conn.execute("""
            SELECT * FROM giveaways ORDER BY created_at DESC LIMIT ?
        """, (limit,))
        return [dict(row) for row in cursor.fetchall()]
    
    def join_giveaway(self, giveaway_id: int, user_id: int) -> Tuple[bool, str]:
        giveaway = self.get_giveaway(giveaway_id)
        if not giveaway:
            return False, "❌ Giveaway not found!"
        
        if giveaway["status"] != "active":
            return False, "❌ This giveaway has ended!"
        
        ends_at = datetime.fromisoformat(giveaway["ends_at"])
        if datetime.now() > ends_at:
            self._close_giveaway(giveaway_id)
            return False, "❌ This giveaway has ended!"
        
        user = self.get_user(user_id)
        if not user:
            return False, "❌ Please start the bot first with /start"
        
        if user["banned"]:
            return False, "❌ You are banned from participating!"
        
        if giveaway["min_points"] > 0 and user["points"] < giveaway["min_points"]:
            return False, (f"❌ You need {giveaway['min_points']:,} points to join!\n"
                          f"You have {user['points']:,} points.")
        
        if giveaway["max_participants"] > 0:
            count = self.get_participant_count(giveaway_id)
            if count >= giveaway["max_participants"]:
                return False, "❌ This giveaway is full!"
        
        try:
            self.conn.execute("""
                INSERT INTO participants (giveaway_id, user_id)
                VALUES (?, ?)
            """, (giveaway_id, user_id))
            self.conn.execute(
                "UPDATE users SET total_joins = total_joins + 1 WHERE user_id = ?",
                (user_id,)
            )
            self.conn.commit()
            return True, "✅ You joined the giveaway!"
        except sqlite3.IntegrityError:
            return False, "⚠️ You already joined this giveaway!"
    
    def leave_giveaway(self, giveaway_id: int, user_id: int) -> bool:
        cursor = self.conn.execute("""
            DELETE FROM participants WHERE giveaway_id = ? AND user_id = ?
        """, (giveaway_id, user_id))
        self.conn.commit()
        return cursor.rowcount > 0
    
    def get_participant_count(self, giveaway_id: int) -> int:
        cursor = self.conn.execute(
            "SELECT COUNT(*) as count FROM participants WHERE giveaway_id = ?",
            (giveaway_id,)
        )
        return cursor.fetchone()["count"]
    
    def get_participants(self, giveaway_id: int) -> List[Dict]:
        cursor = self.conn.execute("""
            SELECT p.*, u.username, u.first_name
            FROM participants p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.giveaway_id = ?
        """, (giveaway_id,))
        return [dict(row) for row in cursor.fetchall()]
    
    def draw_winners(self, giveaway_id: int) -> Tuple[bool, List[Dict], str]:
        giveaway = self.get_giveaway(giveaway_id)
        if not giveaway:
            return False, [], "Giveaway not found"
        
        if giveaway["status"] != "active":
            return False, [], "Giveaway already drawn"
        
        participants = self.get_participants(giveaway_id)
        if len(participants) < 1:
            self._close_giveaway(giveaway_id)
            return False, [], "No participants"
        
        winner_count = min(giveaway["winner_count"], len(participants))
        winners = random.sample(participants, winner_count)
        
        # Update giveaway
        winner_ids = [w["user_id"] for w in winners]
        self.conn.execute("""
            UPDATE giveaways 
            SET status = 'completed', winners = ?, drawn_at = ?
            WHERE id = ?
        """, (json.dumps(winner_ids), datetime.now().isoformat(), giveaway_id))
        
        # Update winner stats
        for w in winners:
            self.conn.execute(
                "UPDATE users SET total_wins = total_wins + 1 WHERE user_id = ?",
                (w["user_id"],)
            )
        
        self.conn.commit()
        return True, winners, giveaway["prize"]
    
    def _close_giveaway(self, giveaway_id: int):
        self.conn.execute(
            "UPDATE giveaways SET status = 'closed' WHERE id = ?",
            (giveaway_id,)
        )
        self.conn.commit()
    
    def cancel_giveaway(self, giveaway_id: int) -> bool:
        cursor = self.conn.execute("""
            UPDATE giveaways SET status = 'cancelled' WHERE id = ? AND status = 'active'
        """, (giveaway_id,))
        self.conn.commit()
        return cursor.rowcount > 0
    
    # ---------- Leaderboard ----------
    
    def get_leaderboard(self, stat: str = "points", limit: int = 10) -> List[Dict]:
        valid_stats = {"points": "points", "wins": "total_wins", "joins": "total_joins"}
        column = valid_stats.get(stat, "points")
        
        cursor = self.conn.execute(f"""
            SELECT user_id, username, first_name, {column} as value
            FROM users WHERE banned = 0 AND {column} > 0
            ORDER BY {column} DESC LIMIT ?
        """, (limit,))
        return [dict(row) for row in cursor.fetchall()]
    
    # ---------- Admin ----------
    
    def get_stats(self) -> Dict:
        cursor = self.conn.execute("SELECT COUNT(*) as count FROM users")
        total_users = cursor.fetchone()["count"]
        
        cursor = self.conn.execute(
            "SELECT COUNT(*) as count FROM giveaways WHERE status = 'active'"
        )
        active_giveaways = cursor.fetchone()["count"]
        
        cursor = self.conn.execute("SELECT COUNT(*) as count FROM giveaways")
        total_giveaways = cursor.fetchone()["count"]
        
        cursor = self.conn.execute("SELECT COUNT(*) as count FROM participants")
        total_participations = cursor.fetchone()["count"]
        
        return {
            "total_users": total_users,
            "active_giveaways": active_giveaways,
            "total_giveaways": total_giveaways,
            "total_participations": total_participations,
        }


import json  # needed for json.dumps above


# =============================================================================
# TELEGRAM BOT
# =============================================================================

logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=getattr(logging, CONFIG["LOG_LEVEL"])
)
logger = logging.getLogger(__name__)

db = Database(CONFIG["DB_PATH"])

# Conversation states
(GET_TITLE, GET_DESC, GET_PRIZE, GET_WINNERS, GET_DURATION,
 GET_MIN_POINTS, GET_MAX_PARTICIPANTS, CONFIRM) = range(8)


# =============================================================================
# HELPER FUNCTIONS
# =============================================================================

def is_admin(user_id: int) -> bool:
    return user_id in CONFIG["ADMIN_IDS"]


def format_number(n: int) -> str:
    return f"{n:,}"


async def is_channel_member(context: ContextTypes.DEFAULT_TYPE,
                             user_id: int) -> bool:
    """Check if user is member of the required channel."""
    try:
        member = await context.bot.get_chat_member(
            CONFIG["CHANNEL_ID"], user_id
        )
        return member.status in [
            ChatMember.MEMBER, ChatMember.ADMINISTRATOR, ChatMember.OWNER
        ]
    except TelegramError:
        return False


def build_giveaway_text(giveaway: Dict, participant_count: int = 0) -> str:
    """Build a formatted giveaway message."""
    ends_at = datetime.fromisoformat(giveaway["ends_at"])
    remaining = ends_at - datetime.now()
    
    if remaining.total_seconds() > 0:
        hours, rem = divmod(int(remaining.total_seconds()), 3600)
        minutes, _ = divmod(rem, 60)
        time_str = f"{hours}h {minutes}m" if hours else f"{minutes}m"
    else:
        time_str = "Ended 🏁"
    
    status_emoji = {
        "active": "🟢",
        "completed": "✅",
        "cancelled": "❌",
        "closed": "🔒"
    }.get(giveaway["status"], "⚪")
    
    text = (
        f"{status_emoji} *{giveaway['title']}*\n\n"
        f"{giveaway['description']}\n\n"
        f"🎁 *Prize:* {giveaway['prize']}\n"
        f"👥 *Winners:* {giveaway['winner_count']}\n"
        f"👤 *Participants:* {participant_count}\n"
        f"⏳ *Ends in:* {time_str}\n"
    )
    
    if giveaway["min_points"] > 0:
        text += f"⭐ *Min. Points:* {format_number(giveaway['min_points'])}\n"
    
    if giveaway["max_participants"] > 0:
        text += f"🚦 *Max Participants:* {format_number(giveaway['max_participants'])}\n"
    
    if giveaway["require_channel_join"]:
        text += f"📢 *Required:* Join {CONFIG['CHANNEL_USERNAME']}\n"
    
    return text


def build_giveaway_keyboard(giveaway_id: int, has_joined: bool = False,
                             is_admin_user: bool = False,
                             ended: bool = False) -> InlineKeyboardMarkup:
    """Build the inline keyboard for a giveaway."""
    buttons = []
    
    if not ended:
        if has_joined:
            buttons.append([
                InlineKeyboardButton("✅ Joined", callback_data=f"joined_{giveaway_id}")
            ])
        else:
            buttons.append([
                InlineKeyboardButton("🎟️ Participate", callback_data=f"join_{giveaway_id}")
            ])
    
    buttons.append([
        InlineKeyboardButton("📊 Status", callback_data=f"status_{giveaway_id}"),
        InlineKeyboardButton("👥 Participants", callback_data=f"participants_{giveaway_id}")
    ])
    
    if is_admin_user:
        buttons.append([
            InlineKeyboardButton("🎲 Draw Winners", callback_data=f"draw_{giveaway_id}"),
            InlineKeyboardButton("❌ Cancel", callback_data=f"cancel_{giveaway_id}")
        ])
    
    return InlineKeyboardMarkup(buttons)


# =============================================================================
# COMMAND HANDLERS
# =============================================================================

async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handle /start command."""
    user = update.effective_user
    args = context.args
    
    referred_by = None
    if args and args[0].startswith("ref"):
        ref_user = db.get_user_by_referral(args[0].upper())
        if ref_user and ref_user["user_id"] != user.id:
            referred_by = ref_user["user_id"]
    
    db_user = db.get_user(user.id)
    if not db_user:
        db.create_user(
            user.id, user.username, user.first_name, user.last_name, referred_by
        )
        welcome_bonus = CONFIG["JOIN_BONUS"]
        if referred_by:
            welcome_bonus += CONFIG["REFERRAL_BONUS"]
    else:
        db.update_username(user.id, user.username, user.first_name, user.last_name)
        welcome_bonus = None
    
    text = (
        f"👋 *Welcome to {CONFIG['BOT_USERNAME']}!*\n\n"
        f"🎁 Participate in giveaways and win amazing prizes!\n"
        f"⭐ Earn points by inviting friends and being active.\n\n"
    )
    
    if welcome_bonus:
        text += f"🎉 You received *{format_number(welcome_bonus)} points* as welcome bonus!\n"
    
    if referred_by:
        referrer = db.get_user(referred_by)
        if referrer:
            text += f"👤 Invited by: {referrer.get('first_name', 'Someone')}\n"
    
    text += (
        f"\n📋 *Commands:*\n"
        f"/giveaways - View active giveaways\n"
        f"/points - Check your points\n"
        f"/referral - Get your referral link\n"
        f"/daily - Claim daily bonus\n"
        f"/leaderboard - Top users\n"
        f"/profile - Your profile\n"
    )
    
    if is_admin(user.id):
        text += (
            f"\n🔧 *Admin Commands:*\n"
            f"/newgiveaway - Create a giveaway\n"
            f"/admin - Admin panel\n"
        )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def giveaways_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """List active giveaways."""
    giveaways = db.get_active_giveaways()
    
    if not giveaways:
        await update.message.reply_text(
            "📭 *No active giveaways right now.*\n"
            "Check back later for new opportunities!",
            parse_mode=ParseMode.MARKDOWN
        )
        return
    
    text = "🎁 *Active Giveaways:*\n\n"
    
    for g in giveaways[:10]:
        count = db.get_participant_count(g["id"])
        ends = datetime.fromisoformat(g["ends_at"])
        remaining = ends - datetime.now()
        hours = int(remaining.total_seconds() // 3600)
        
        text += (
            f"🔹 *{g['title']}*\n"
            f"   🎁 {g['prize']} | 👥 {count} participants\n"
            f"   ⏳ {hours}h remaining\n\n"
        )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def points_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Check user points."""
    user = db.get_user(update.effective_user.id)
    if not user:
        await update.message.reply_text("❌ Please /start first!")
        return
    
    text = (
        f"⭐ *Your Points:* {format_number(user['points'])}\n\n"
        f"📊 *Stats:*\n"
        f"• Giveaways joined: {user['total_joins']}\n"
        f"• Giveaways won: {user['total_wins']}\n"
        f"• Referrals: {db.get_referral_count(user['user_id'])}\n"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def referral_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get referral link."""
    user = db.get_user(update.effective_user.id)
    if not user:
        await update.message.reply_text("❌ Please /start first!")
        return
    
    bot_username = CONFIG["BOT_USERNAME"]
    referral_link = f"https://t.me/{bot_username}?start=ref{user['referral_code']}"
    
    text = (
        f"🤝 *Your Referral Link:*\n"
        f"`{referral_link}`\n\n"
        f"⭐ *Bonus:* {format_number(CONFIG['REFERRAL_BONUS'])} points per referral\n"
        f"👥 *Total Referrals:* {db.get_referral_count(user['user_id'])}\n\n"
        f"Share your link and earn points!"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def daily_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Claim daily bonus."""
    user_id = update.effective_user.id
    
    if db.claim_daily(user_id):
        await update.message.reply_text(
            f"✅ *Daily Bonus Claimed!*\n"
            f"⭐ +{format_number(CONFIG['DAILY_BONUS'])} points added!",
            parse_mode=ParseMode.MARKDOWN
        )
    else:
        await update.message.reply_text(
            "⏰ *Already claimed today!*\n"
            "Come back tomorrow for your daily bonus.",
            parse_mode=ParseMode.MARKDOWN
        )


async def leaderboard_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Show leaderboard."""
    leaderboard = db.get_leaderboard("points", 15)
    
    if not leaderboard:
        await update.message.reply_text("📭 No users on the leaderboard yet!")
        return
    
    text = "🏆 *Points Leaderboard:*\n\n"
    medals = ["🥇", "🥈", "🥉"]
    
    for i, entry in enumerate(leaderboard):
        medal = medals[i] if i < 3 else f"{i+1}."
        name = entry.get("first_name") or f"User {entry['user_id']}"
        if entry.get("username"):
            name += f" (@{entry['username']})"
        text += f"{medal} {name} — ⭐{format_number(entry['value'])}\n"
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def profile_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Show user profile."""
    user = db.get_user(update.effective_user.id)
    if not user:
        await update.message.reply_text("❌ Please /start first!")
        return
    
    text = (
        f"👤 *User Profile*\n\n"
        f"🆔 ID: `{user['user_id']}`\n"
        f"⭐ Points: {format_number(user['points'])}\n"
        f"🎁 Joined: {user['total_joins']} giveaways\n"
        f"🏆 Won: {user['total_wins']} times\n"
        f"👥 Referrals: {db.get_referral_count(user['user_id'])}\n"
        f"🔗 Code: `{user['referral_code']}`\n"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


# =============================================================================
# GIVEAWAY INTERACTION
# =============================================================================

async def giveaway_button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handle giveaway button callbacks."""
    query = update.callback_query
    await query.answer()
    
    user_id = query.from_user.id
    data = query.data
    
    if data.startswith("join_"):
        giveaway_id = int(data.split("_")[1])
        
        # Check channel membership
        if not await is_channel_member(context, user_id):
            await query.edit_message_text(
                f"❌ You must join {CONFIG['CHANNEL_USERNAME']} first!\n"
                f"After joining, click /giveaways and try again.",
                parse_mode=ParseMode.MARKDOWN
            )
            return
        
        success, msg = db.join_giveaway(giveaway_id, user_id)
        
        if success:
            giveaway = db.get_giveaway(giveaway_id)
            count = db.get_participant_count(giveaway_id)
            text = build_giveaway_text(giveaway, count)
            keyboard = build_giveaway_keyboard(
                giveaway_id, has_joined=True,
                is_admin_user=is_admin(user_id),
                ended=False
            )
            await query.edit_message_text(
                text, parse_mode=ParseMode.MARKDOWN,
                reply_markup=keyboard
            )
        else:
            await query.edit_message_text(msg)
    
    elif data.startswith("status_"):
        giveaway_id = int(data.split("_")[1])
        giveaway = db.get_giveaway(giveaway_id)
        if not giveaway:
            await query.edit_message_text("❌ Giveaway not found!")
            return
        
        count = db.get_participant_count(giveaway_id)
        text = build_giveaway_text(giveaway, count)
        
        has_joined = db.conn.execute(
            "SELECT id FROM participants WHERE giveaway_id = ? AND user_id = ?",
            (giveaway_id, user_id)
        ).fetchone() is not None
        
        ended = giveaway["status"] != "active"
        keyboard = build_giveaway_keyboard(
            giveaway_id, has_joined, is_admin(user_id), ended
        )
        
        await query.edit_message_text(
            text, parse_mode=ParseMode.MARKDOWN,
            reply_markup=keyboard
        )
    
    elif data.startswith("participants_"):
        giveaway_id = int(data.split("_")[1])
        participants = db.get_participants(giveaway_id)
        count = len(participants)
        
        text = f"👥 *Participants ({count}):*\n\n"
        
        if count == 0:
            text += "No one has joined yet. Be the first!"
        else:
            for i, p in enumerate(participants[:30], 1):
                name = p.get("first_name") or f"User {p['user_id']}"
                text += f"{i}. {name}"
                if p.get("username"):
                    text += f" (@{p['username']})"
                text += "\n"
            
            if count > 30:
                text += f"\n... and {count - 30} more"
        
        await query.edit_message_text(
            text, parse_mode=ParseMode.MARKDOWN
        )
    
    elif data.startswith("draw_"):
        if not is_admin(user_id):
            await query.edit_message_text("❌ Only admins can draw winners!")
            return
        
        giveaway_id = int(data.split("_")[1])
        success, winners, prize = db.draw_winners(giveaway_id)
        
        if not success:
            await query.edit_message_text(f"❌ Draw failed: {prize}")
            return
        
        giveaway = db.get_giveaway(giveaway_id)
        
        # Announce winners
        winner_mentions = []
        for w in winners:
            try:
                mention = f"@{w['username']}" if w.get("username") else f"[User](tg://user?id={w['user_id']})"
                winner_mentions.append(mention)
            except:
                winner_mentions.append(f"`{w['user_id']}`")
        
        text = (
            f"🎉 *GIVEAWAY RESULT* 🎉\n\n"
            f"🏆 *{giveaway['title']}*\n"
            f"🎁 Prize: {prize}\n\n"
            f"👑 *Winners:*\n"
        )
        for i, mention in enumerate(winner_mentions, 1):
            text += f"{i}. {mention}\n"
        
        await query.edit_message_text(
            text, parse_mode=ParseMode.MARKDOWN
        )
        
        # Try to notify winners
        for w in winners:
            try:
                await context.bot.send_message(
                    w["user_id"],
                    f"🎉 *Congratulations!*\n\n"
                    f"You won *{prize}* in the giveaway "
                    f"\"{giveaway['title']}\"!\n\n"
                    f"Contact support: {CONFIG['SUPPORT_LINK']}",
                    parse_mode=ParseMode.MARKDOWN
                )
            except TelegramError:
                pass
    
    elif data.startswith("cancel_"):
        if not is_admin(user_id):
            await query.edit_message_text("❌ Only admins can cancel giveaways!")
            return
        
        giveaway_id = int(data.split("_")[1])
        if db.cancel_giveaway(giveaway_id):
            await query.edit_message_text("❌ Giveaway cancelled.")
        else:
            await query.edit_message_text("❌ Cannot cancel. Already ended.")


# =============================================================================
# ADMIN: CREATE GIVEAWAY (Conversation)
# =============================================================================

async def newgiveaway_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Start giveaway creation (Admin only)."""
    if not is_admin(update.effective_user.id):
        await update.message.reply_text("❌ Admin only command!")
        return
    
    active_count = len(db.get_active_giveaways())
    if active_count >= CONFIG["MAX_ACTIVE_GIVEAWAYS"]:
        await update.message.reply_text(
            f"❌ Maximum {CONFIG['MAX_ACTIVE_GIVEAWAYS']} active giveaways reached!"
        )
        return
    
    await update.message.reply_text(
        "🎁 *Create a New Giveaway*\n\n"
        "Send me the *title* of the giveaway:",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_TITLE


async def get_title(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get giveaway title."""
    context.user_data["giveaway_title"] = update.message.text[:100]
    await update.message.reply_text(
        "✅ Title saved!\n\n"
        "Now send me a *description* (or /skip for empty):",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_DESC


async def get_description(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get giveaway description."""
    if update.message.text == "/skip":
        context.user_data["giveaway_desc"] = ""
    else:
        context.user_data["giveaway_desc"] = update.message.text[:500]
    
    await update.message.reply_text(
        "✅ Description saved!\n\n"
        "Now send me the *prize* (what are you giving away?):",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_PRIZE


async def get_prize(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get giveaway prize."""
    context.user_data["giveaway_prize"] = update.message.text[:200]
    await update.message.reply_text(
        "✅ Prize saved!\n\n"
        "How many *winners*? (default: 1, max: 50):",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_WINNERS


async def get_winners(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get winner count."""
    try:
        count = int(update.message.text)
        if count < 1 or count > CONFIG["MAX_WINNER_COUNT"]:
            raise ValueError
        context.user_data["giveaway_winners"] = count
    except (ValueError, TypeError):
        await update.message.reply_text(
            f"❌ Please send a valid number (1-{CONFIG['MAX_WINNER_COUNT']}):"
        )
        return GET_WINNERS
    
    await update.message.reply_text(
        "✅ Winner count saved!\n\n"
        "How long should the giveaway last?\n"
        "Send in format: `1h`, `24h`, `48h`, `7d` etc.",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_DURATION


async def get_duration(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Parse duration and ask for min points."""
    text = update.message.text.lower().strip()
    
    try:
        if text.endswith("h"):
            hours = int(text.rstrip("h"))
            duration = timedelta(hours=hours)
        elif text.endswith("d"):
            days = int(text.rstrip("d"))
            duration = timedelta(days=days)
        elif text.endswith("m"):
            mins = int(text.rstrip("m"))
            duration = timedelta(minutes=mins)
        else:
            raise ValueError
    except (ValueError, TypeError):
        await update.message.reply_text(
            "❌ Invalid format! Use: `1h`, `24h`, `7d`, `30m`",
            parse_mode=ParseMode.MARKDOWN
        )
        return GET_DURATION
    
    context.user_data["giveaway_duration"] = duration
    context.user_data["giveaway_ends"] = datetime.now() + duration
    
    await update.message.reply_text(
        f"✅ Duration: {text}\n\n"
        "Minimum *points* required to join? (0 for none):",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_MIN_POINTS


async def get_min_points(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get minimum points requirement."""
    try:
        points = int(update.message.text)
        if points < 0:
            raise ValueError
        context.user_data["giveaway_min_points"] = points
    except ValueError:
        await update.message.reply_text(
            "❌ Please send a valid number (0 for no minimum):"
        )
        return GET_MIN_POINTS
    
    await update.message.reply_text(
        "✅ Min points saved!\n\n"
        "Maximum *participants*? (0 for unlimited):",
        parse_mode=ParseMode.MARKDOWN
    )
    return GET_MAX_PARTICIPANTS


async def get_max_participants(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Get max participants limit."""
    try:
        max_p = int(update.message.text)
        if max_p < 0:
            raise ValueError
        context.user_data["giveaway_max_participants"] = max_p
    except ValueError:
        await update.message.reply_text(
            "❌ Please send a valid number (0 for unlimited):"
        )
        return GET_MAX_PARTICIPANTS
    
    # Show summary
    data = context.user_data
    text = (
        "📋 *Giveaway Summary:*\n\n"
        f"📌 Title: {data['giveaway_title']}\n"
        f"📝 Desc: {data.get('giveaway_desc', '(none)')[:50]}\n"
        f"🎁 Prize: {data['giveaway_prize']}\n"
        f"👑 Winners: {data['giveaway_winners']}\n"
        f"⏳ Duration: {data['giveaway_ends'] - datetime.now()}\n"
        f"⭐ Min Points: {data['giveaway_min_points']}\n"
        f"🚦 Max Participants: {data['giveaway_max_participants'] or 'Unlimited'}\n\n"
        f"✅ Confirm? /confirm_create\n"
        f"❌ Cancel: /cancel"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)
    return CONFIRM


async def confirm_create(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Create the giveaway."""
    if update.message.text != "/confirm_create":
        await update.message.reply_text("Send /confirm_create to confirm.")
        return CONFIRM
    
    data = context.user_data
    g_id = db.create_giveaway(
        title=data["giveaway_title"],
        description=data.get("giveaway_desc", ""),
        prize=data["giveaway_prize"],
        winner_count=data["giveaway_winners"],
        created_by=update.effective_user.id,
        ends_at=data["giveaway_ends"],
        min_points=data["giveaway_min_points"],
        require_channel=True,
        max_participants=data["giveaway_max_participants"],
    )
    
    text = (
        f"✅ *Giveaway Created!* (ID: {g_id})\n\n"
        f"📌 {data['giveaway_title']}\n"
        f"🎁 Prize: {data['giveaway_prize']}\n\n"
        f"Share it with users now!"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)
    context.user_data.clear()
    return ConversationHandler.END


async def cancel_create(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Cancel giveaway creation."""
    context.user_data.clear()
    await update.message.reply_text("❌ Giveaway creation cancelled.")
    return ConversationHandler.END


# =============================================================================
# ADMIN PANEL
# =============================================================================

async def admin_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Admin panel."""
    if not is_admin(update.effective_user.id):
        await update.message.reply_text("❌ Admin only command!")
        return
    
    stats = db.get_stats()
    giveaways = db.get_all_giveaways(5)
    
    text = (
        "🔧 *Admin Panel*\n\n"
        f"📊 *Statistics:*\n"
        f"👥 Total Users: {format_number(stats['total_users'])}\n"
        f"🎁 Total Giveaways: {format_number(stats['total_giveaways'])}\n"
        f"🟢 Active: {stats['active_giveaways']}\n"
        f"📝 Total Participations: {format_number(stats['total_participations'])}\n\n"
        f"📋 *Recent Giveaways:*\n"
    )
    
    for g in giveaways:
        emoji = {"active": "🟢", "completed": "✅", "cancelled": "❌", "closed": "🔒"}.get(g["status"], "⚪")
        participants = db.get_participant_count(g["id"])
        text += f"{emoji} `#{g['id']}` {g['title']} — {participants} participants\n"
    
    text += (
        f"\n🔧 *Commands:*\n"
        f"/newgiveaway - Create giveaway\n"
        f"/broadcast <msg> - Send message to all users\n"
        f"/ban <user_id> - Ban a user\n"
        f"/unban <user_id> - Unban a user\n"
    )
    
    await update.message.reply_text(text, parse_mode=ParseMode.MARKDOWN)


async def broadcast_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Broadcast message to all users (Admin)."""
    if not is_admin(update.effective_user.id):
        return
    
    if not context.args:
        await update.message.reply_text("Usage: /broadcast <message>")
        return
    
    message = " ".join(context.args)
    await update.message.reply_text(f"📢 Broadcasting... (this may take a while)")
    
    # Get all users
    cursor = db.conn.execute("SELECT user_id FROM users WHERE banned = 0")
    users = cursor.fetchall()
    
    sent = 0
    failed = 0
    for user in users:
        try:
            await context.bot.send_message(
                user["user_id"],
                f"📢 *Broadcast:*\n\n{message}",
                parse_mode=ParseMode.MARKDOWN
            )
            sent += 1
            await asyncio.sleep(0.05)  # Rate limit
        except TelegramError:
            failed += 1
    
    await update.message.reply_text(
        f"✅ Broadcast complete!\n"
        f"✓ Sent: {sent}\n"
        f"✗ Failed: {failed}"
    )


async def ban_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Ban a user (Admin)."""
    if not is_admin(update.effective_user.id):
        return
    
    if not context.args:
        await update.message.reply_text("Usage: /ban <user_id>")
        return
    
    try:
        user_id = int(context.args[0])
        db.conn.execute("UPDATE users SET banned = 1 WHERE user_id = ?", (user_id,))
        db.conn.commit()
        await update.message.reply_text(f"✅ User {user_id} banned.")
    except (ValueError, TypeError):
        await update.message.reply_text("❌ Invalid user ID.")


async def unban_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Unban a user (Admin)."""
    if not is_admin(update.effective_user.id):
        return
    
    if not context.args:
        await update.message.reply_text("Usage: /unban <user_id>")
        return
    
    try:
        user_id = int(context.args[0])
        db.conn.execute("UPDATE users SET banned = 0 WHERE user_id = ?", (user_id,))
        db.conn.commit()
        await update.message.reply_text(f"✅ User {user_id} unbanned.")
    except (ValueError, TypeError):
        await update.message.reply_text("❌ Invalid user ID.")


# =============================================================================
# MAIN
# =============================================================================

async def post_init(application: Application):
    """Set bot commands after initialization."""
    commands = [
        BotCommand("start", "Start the bot"),
        BotCommand("giveaways", "View active giveaways"),
        BotCommand("points", "Check your points"),
        BotCommand("referral", "Get referral link"),
        BotCommand("daily", "Claim daily bonus"),
        BotCommand("leaderboard", "Top users"),
        BotCommand("profile", "Your profile"),
    ]
    await application.bot.set_my_commands(commands, BotCommandScopeDefault())


async def error_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handle errors gracefully."""
    logger.error(f"Update {update} caused error {context.error}")
    try:
        if update and update.effective_chat:
            await context.bot.send_message(
                update.effective_chat.id,
                "⚠️ An internal error occurred. Please try again later."
            )
    except TelegramError:
        pass


def main():
    """Start the bot."""
    token = CONFIG["BOT_TOKEN"]
    if token == "YOUR_BOT_TOKEN_HERE":
        print("❌ ERROR: Set your BOT_TOKEN in CONFIG first!")
        print("Get it from @BotFather")
        return
    
    # Create application
    app = Application.builder() \
        .token(token) \
        .post_init(post_init) \
        .build()
    
    # Basic commands
    app.add_handler(CommandHandler("start", start_command))
    app.add_handler(CommandHandler("giveaways", giveaways_command))
    app.add_handler(CommandHandler("points", points_command))
    app.add_handler(CommandHandler("referral", referral_command))
    app.add_handler(CommandHandler("daily", daily_command))
    app.add_handler(CommandHandler("leaderboard", leaderboard_command))
    app.add_handler(CommandHandler("profile", profile_command))
    
    # Admin commands
    app.add_handler(CommandHandler("admin", admin_command))
    app.add_handler(CommandHandler("broadcast", broadcast_command))
    app.add_handler(CommandHandler("ban", ban_command))
    app.add_handler(CommandHandler("unban", unban_command))
    
    # Giveaway creation conversation (Admin)
    conv_handler = ConversationHandler(
        entry_points=[CommandHandler("newgiveaway", newgiveaway_command)],
        states={
            GET_TITLE: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_title)],
            GET_DESC: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_description),
                       CommandHandler("skip", get_description)],
            GET_PRIZE: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_prize)],
            GET_WINNERS: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_winners)],
            GET_DURATION: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_duration)],
            GET_MIN_POINTS: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_min_points)],
            GET_MAX_PARTICIPANTS: [MessageHandler(filters.TEXT & ~filters.COMMAND, get_max_participants)],
            CONFIRM: [
                CommandHandler("confirm_create", confirm_create),
                CommandHandler("cancel", cancel_create),
                MessageHandler(filters.TEXT, confirm_create),
            ],
        },
        fallbacks=[CommandHandler("cancel", cancel_create)],
        allow_reentry=True,
    )
    app.add_handler(conv_handler)
    
    # Callback handlers
    app.add_handler(CallbackQueryHandler(giveaway_button_handler, pattern="^(join_|status_|participants_|draw_|cancel_|joined_)"))
    
    # Error handler
    app.add_error_handler(error_handler)
    
    print(f"🤖 Bot started! Username: @{CONFIG['BOT_USERNAME']}")
    print(f"📊 Database: {CONFIG['DB_PATH']}")
    print(f"👥 Admin IDs: {CONFIG['ADMIN_IDS']}")
    app.run_polling(allowed_updates=Update.ALL_TYPES)


if __name__ == "__main__":
    main()