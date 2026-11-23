# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
from datetime import datetime, timedelta
from telethon import events, types

from coins_db import coin_db

class GambleListener:
    """
    Allows users to gamble once every 24 hours by rolling the Telegram dice 🎲;
    if the result is 6, the user wins a reward.
    Usage: /gamble
    """
    def __init__(self, bot, cooldown_hours: int = 24, reward_coins: int = 20):
        self.bot = bot
        self.cooldown = timedelta(hours=cooldown_hours)
        self.reward = reward_coins

        # Ensure the gamble_logs table exists
        asyncio.get_event_loop().create_task(self._ensure_table())

        # Register the /gamble handler
        bot.add_event_handler(
            self._on_gamble,
            events.NewMessage(pattern=r'^/gamble$')
        )

    async def _ensure_table(self):
        pool = coin_db.pool
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS gamble_logs (
                        user_id BIGINT UNSIGNED PRIMARY KEY,
                        last_gamble TIMESTAMP NOT NULL
                    ) ENGINE=InnoDB CHARSET=utf8mb4;
                """)
                await conn.commit()

    async def _on_gamble(self, event):
        user_id = event.sender_id
        now = datetime.utcnow()
        pool = coin_db.pool

        # 1) Check cooldown
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT last_gamble FROM gamble_logs WHERE user_id=%s",
                    (user_id,)
                )
                row = await cur.fetchone()
                if row:
                    last = row[0]
                    elapsed = now - last
                    if elapsed < self.cooldown:
                        remaining = self.cooldown - elapsed
                        hrs, rem = divmod(remaining.seconds, 3600)
                        mins, secs = divmod(rem, 60)
                        return await event.reply(
                            f"⌛ شما قبلاً قمار کرده‌اید. لطفاً {hrs} ساعت و {mins} دقیقه دیگر تلاش کنید."
                        )
        # 2) Send a real Telegram dice
        dice_msg = await self.bot.send_file(
            event.chat_id,
            types.InputMediaDice('🎲')
        )
        # 3) Extract the roll result
        value = None
        if hasattr(dice_msg, 'media') and hasattr(dice_msg.media, 'value'):
            value = dice_msg.media.value

        # 4) Update last_gamble in DB
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                if row:
                    await cur.execute(
                        "UPDATE gamble_logs SET last_gamble=%s WHERE user_id=%s",
                        (now, user_id)
                    )
                else:
                    await cur.execute(
                        "INSERT INTO gamble_logs (user_id, last_gamble) VALUES (%s, %s)",
                        (user_id, now)
                    )
                await conn.commit()

        # 5) Award or lose
        if value is None:
            await event.reply("❌ خطا در انداختن تاس. لطفاً دوباره تلاش کنید.")
        elif value == 6:
            await coin_db.add_coins(user_id, self.reward)
            await event.reply(
                f"🎉 تبریک! شما عدد ۶ را انداختید و {self.reward} سکه دریافت کردید!"
            )
        else:
            await event.reply(f"🎲 شما عدد {value} را انداختید. شانس بعدی! 😸")
