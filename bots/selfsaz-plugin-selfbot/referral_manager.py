# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import re
from datetime import date
from telethon import events
from telethon import Button
from coins_db import (
    init_coin_db_pool,
    init_coins_table,
    get_coin_db_cursor,
    ensure_refill,
    change_user_coins
)

logger = logging.getLogger("ReferralManager")
REFERRAL_REWARD = 10  # coins awarded to referrer and referred

# -----------------------------
# Referral DB setup & helpers
# -----------------------------
async def init_referral_table():
    """
    Create the user_referrals table if it doesn't exist.
    Columns:
      - user_id (PK)
      - referrer_id
    """
    async with get_coin_db_cursor() as cur:
        await cur.execute("""
        CREATE TABLE IF NOT EXISTS user_referrals (
            user_id VARCHAR(50) PRIMARY KEY,
            referrer_id VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """)

async def set_referrer(user_id: str, referrer_id: str) -> bool:
    """
    Record the referrer for a new user. Returns False if already recorded.
    """
    async with get_coin_db_cursor() as cur:
        await cur.execute(
            "SELECT 1 FROM user_referrals WHERE user_id = %s",
            (user_id,)
        )
        if await cur.fetchone():
            return False
        await cur.execute(
            "INSERT INTO user_referrals (user_id, referrer_id) VALUES (%s, %s)",
            (user_id, referrer_id)
        )
        return True

async def get_referrals_count(referrer_id: str) -> int:
    """
    Count how many users were referred by referrer_id.
    """
    async with get_coin_db_cursor() as cur:
        await cur.execute(
            "SELECT COUNT(*) AS cnt FROM user_referrals WHERE referrer_id = %s",
            (referrer_id,)
        )
        row = await cur.fetchone()
        return row['cnt'] if row else 0

# -----------------------------
# ReferralManager Class
# -----------------------------
class ReferralManager:
    def __init__(self, bot, reward: int = REFERRAL_REWARD):
        self.bot = bot
        self.reward = reward
        self._register_handlers()
        logger.info(f"ReferralManager initialized with reward={reward}")

    def _register_handlers(self):
        # Show referral link and count
        @self.bot.on(events.NewMessage(pattern=r'^/referral$'))
        async def _show_referral(event):
            me = await self.bot.get_me()
            username = me.username or "<bot>"
            uid = str(event.sender_id)
            count = await get_referrals_count(uid)
            link = f"https://t.me/{username}?start={uid}"
            msg = (
                f"🔗 لینک دعوت شما:\n{link}\n"
                f"🎉 تعداد دعوت‌ها: {count} نفر"
            )
            await event.respond(msg)

        # Handle /start with optional referral code
        @self.bot.on(events.NewMessage(pattern=r'^/start(?:=(\d+))?$'))
        async def _on_start(event):
            text = event.raw_text.strip()
            uid = str(event.sender_id)

            # 1) Referral credit
            m = re.match(r'^/start=(\d+)$', text)
            if m:
                ref_id = m.group(1)
                # Prevent self-referral
                if ref_id != uid:
                    first = await set_referrer(uid, ref_id)
                    if first:
                        # Refill month if needed
                        await ensure_refill(ref_id)
                        await ensure_refill(uid)
                        # Award coins
                        await change_user_coins(ref_id, self.reward)
                        await change_user_coins(uid, self.reward)
                        await event.respond(
                            f"🎉 شما و کاربر دعوت‌کننده هر دو {self.reward} سکه دریافت کردید!"
                        )
            # 2) Let other handlers (TOS, join, welcome) run
            # do not stop propagation
            return

# -----------------------------
# Initialization helper
# -----------------------------
async def setup_referrals(bot):
    """
    Call this in main(): initializes coin & referral tables and instantiates manager.
    """
    # Ensure coin DB is ready
    await init_coin_db_pool()
    await init_coins_table()
    # Referral table
    await init_referral_table()
    # Install handlers
    ReferralManager(bot)
