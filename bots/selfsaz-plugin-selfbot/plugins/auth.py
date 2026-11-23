# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# authmanager.py
import os
import sqlite3
from .base import Plugin
from telethon import events

DB_PATH = "data/ai_auth.db"
ACCESS_MANAGER_ID = "7150795159"

def ensure_db():
    os.makedirs("data", exist_ok=True)
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("""CREATE TABLE IF NOT EXISTS authorized_users (
                    user_id TEXT PRIMARY KEY
                )""")
    conn.commit()
    conn.close()

def authorize_user(user_id):
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("INSERT OR IGNORE INTO authorized_users (user_id) VALUES (?)", (str(user_id),))
    conn.commit()
    conn.close()

def revoke_user(user_id):
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("DELETE FROM authorized_users WHERE user_id = ?", (str(user_id),))
    conn.commit()
    conn.close()

class AuthManagerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        ensure_db()

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r"^/(auth|unauth)\s+(\d+)"))
        async def manage_auth(event):
            if str(event.sender_id) != ACCESS_MANAGER_ID:
                return

            action, uid = event.pattern_match.groups()
            if action == "auth":
                authorize_user(uid)
                await event.reply(f"✅ User {uid} authorized.")
            elif action == "unauth":
                revoke_user(uid)
                await event.reply(f"❌ User {uid} unauthorized.")
