# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
from datetime import datetime, timedelta
from telethon import events
from .base import Plugin
from coins_db import coin_db
import mysql.connector
from mysql.connector import pooling

# ── CONFIG ──────────────────────────────────────────────────────────
ADMIN_ID = "7150795159"  # only this ID can manage bans/auth
# The owner for license redemption is passed into the plugin as user_id
# ────────────────────────────────────────────────────────────────────

DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'botadmin',
    'password': 'securepass',
    'database': 'botdata',
    'pool_name': 'botpool',
    'pool_size': 10
}

_connection_pool = None

def get_connection_pool():
    global _connection_pool
    if _connection_pool is None:
        _connection_pool = pooling.MySQLConnectionPool(**DB_CONFIG)
        conn = _connection_pool.get_connection()
        cur = conn.cursor()
        cur.execute("CREATE TABLE IF NOT EXISTS authorized_users (user_id VARCHAR(32) PRIMARY KEY)")
        cur.execute("CREATE TABLE IF NOT EXISTS global_bans (user_id VARCHAR(32) PRIMARY KEY)")
        conn.commit()
        cur.close()
        conn.close()
    return _connection_pool

# --- Authorization / Ban Utilities ---

def authorize_user(user_id: str) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.execute("INSERT IGNORE INTO authorized_users (user_id) VALUES (%s)", (user_id,))
        conn.commit()
        return True
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

def revoke_user(user_id: str) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.execute("DELETE FROM authorized_users WHERE user_id=%s", (user_id,))
        conn.commit()
        return True
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

def ban_user(user_id: str) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.execute("INSERT IGNORE INTO global_bans (user_id) VALUES (%s)", (user_id,))
        conn.commit()
        return True
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

def unban_user(user_id: str) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.execute("DELETE FROM global_bans WHERE user_id=%s", (user_id,))
        conn.commit()
        return True
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

def batch_ban_users(user_ids: list) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.executemany("INSERT IGNORE INTO global_bans (user_id) VALUES (%s)", [(uid,) for uid in user_ids])
        conn.commit()
        return True
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

def is_banned(user_id: str) -> bool:
    try:
        pool = get_connection_pool()
        conn = pool.get_connection()
        cur = conn.cursor()
        cur.execute("SELECT 1 FROM global_bans WHERE user_id=%s", (user_id,))
        return bool(cur.fetchone())
    except Exception:
        return False
    finally:
        if 'cur' in locals(): cur.close()
        if 'conn' in locals(): conn.close()

class AccessManagerPlugin(Plugin):
    """
    /redeem → owner only
    /auth, /unauth, /globalban, /globalunban, /massban → admin only
    Automatically logs out after 15-minute trial unless a license is activated.
    Users can check remaining license time with /license
    """
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.client = client
        self.owner_id = int(user_id)
        self.license_redeemed = False

        # Initialize pools and tables
        get_connection_pool()
        # Bootstrap license or start trial
        asyncio.get_event_loop().create_task(self._bootstrap_license())

    async def _bootstrap_license(self):
        # Check for existing active license
        lic = await coin_db.get_active_license_for_user(self.owner_id)
        if lic and lic.get('used'):
            # License already activated → schedule expiration
            self.license_redeemed = True
            now = datetime.utcnow()
            expires = lic['expires_at']
            remaining = (expires - now).total_seconds()
            if remaining <= 0:
                await self.client.send_message(self.owner_id, "🔔 لایسنس منقضی شده، ربات خارج می‌شود.")
                await self.client.log_out()
            else:
                asyncio.get_event_loop().create_task(self._expiration(remaining))
        else:
            # No license → start 15-minute trial
            asyncio.get_event_loop().create_task(self._redeem_deadline())

    async def _redeem_deadline(self):
        await asyncio.sleep(15 * 60)
        # Re-check license activation before logout
        if not self.license_redeemed:
            lic = await coin_db.get_active_license_for_user(self.owner_id)
            if lic and lic.get('used'):
                # Activated during trial → schedule real expiration
                self.license_redeemed = True
                now = datetime.utcnow()
                expires = lic['expires_at']
                remaining = (expires - now).total_seconds()
                if remaining > 0:
                    asyncio.get_event_loop().create_task(self._expiration(remaining))
                    print(f"License activated, remaining time: {remaining} seconds.")
                    return
            # Still no license → logout
            await self.client.send_message(self.owner_id, '⏳ مهلت ۱۵ دقیقه‌ای تمام شد، ربات خارج می‌شود.')
            await self.client.log_out()

    async def _expiration(self, secs: float):
        await asyncio.sleep(secs)
        await self.client.send_message(self.owner_id, '🔔 زمان لایسنس به پایان رسید، ربات خارج می‌شود.')
        await self.client.log_out()

    async def handle_events(self):
        # Redeem handler (owner only)
        @self.client.on(events.NewMessage(pattern=r"^/(redeem|فعالسازی)\s+(\w+)$"))
        async def redeem_handler(event):
            if event.sender_id != self.owner_id:
                return
            code = event.pattern_match.group(2)
            lic = await coin_db.get_license(code)
            if not lic:
                return await event.reply(f"❌ لایسنس `{code}` یافت نشد.")
            if lic['used']:
                return await event.reply(f"❌ لایسنس `{code}` قبلاً استفاده شده.")
            now = datetime.utcnow()
            dur = lic['duration_months']
            exp = now + timedelta(days=dur * 30)
            # Update license timestamps
            await coin_db.update_license_timestamps(code, now, exp)
            self.license_redeemed = True
            asyncio.get_event_loop().create_task(self._expiration((exp - now).total_seconds()))
            await event.reply(f"✅ لایسنس {dur} ماهه فعال شد. پس از انقضا ربات خارج می‌شود.")

        # License status check (owner only)
        @self.client.on(events.NewMessage(pattern=r'^/license$'))
        async def license_status(event):
            if event.sender_id != self.owner_id:
                return
            lic = await coin_db.get_active_license_for_user(self.owner_id)
            if not lic or not lic.get('used'):
                return await event.reply("⚠️ شما لایسنس فعالی ندارید.")
            now = datetime.utcnow()
            expires = lic['expires_at']
            remaining = expires - now
            if remaining.total_seconds() <= 0:
                return await event.reply("⏰ لایسنس شما منقضی شده است.")
            days = remaining.days
            hours, rem = divmod(remaining.seconds, 3600)
            minutes, _ = divmod(rem, 60)
            await event.reply(f"⏳ زمان باقی مانده: {days} روز، {hours} ساعت، {minutes} دقیقه.")

        # Auth / Unauth (admin only)
        @self.client.on(events.NewMessage(pattern=r"^/(auth|unauth)\s+(\d+)$"))
        async def manage_auth(event):
            if str(event.sender_id) != ADMIN_ID:
                return
            act, uid = event.pattern_match.groups()
            ok = authorize_user(uid) if act == 'auth' else revoke_user(uid)
            msg = f"✅ `{uid}` {'مجاز شد' if act=='auth' else 'حذف دسترسی'}" if ok else "❌ خطا در عملیات"
            await event.reply(msg)

        # Globalban / Globalunban (admin only)
        @self.client.on(events.NewMessage(pattern=r"^/(globalban|globalunban)\s+(\d+)$"))
        async def manage_ban(event):
            if str(event.sender_id) != ADMIN_ID:
                return
            act, uid = event.pattern_match.groups()
            ok = ban_user(uid) if act=='globalban' else unban_user(uid)
            msg = f"🚫 `{uid}` {'محروم شد' if act=='globalban' else 'محرومیت لغو شد'}" if ok else "❌ خطا در عملیات"
            await event.reply(msg)

        # Massban (admin only)
        @self.client.on(events.NewMessage(pattern=r"^/massban\s+(.+)$"))
        async def mass_ban(event):
            if str(event.sender_id) != ADMIN_ID:
                return
            ids = event.pattern_match.group(1).split()
            ok = batch_ban_users(ids)
            msg = f"🚫 {len(ids)} کاربر محروم شدند." if ok else "❌ خطا در عملیات"
            await event.reply(msg)

        # Auto-logout if this bot gets banned
        @self.client.on(events.NewMessage(incoming=True))
        async def self_ban_check(event):
            me = await self.client.get_me()
            if is_banned(str(me.id)):
                await event.reply('🚫 این بات مسدود شده است. ربات خارج می‌شود.')
                await self.client.log_out()
