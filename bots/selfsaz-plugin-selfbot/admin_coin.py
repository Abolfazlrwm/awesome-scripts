# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
from typing import Set, List, Tuple
from aiomysql.pool import Pool
from telethon import TelegramClient, events

from coins_db import coin_db

class AdminCoinManager:
    """
    Allows designated admins to transfer coins to users and logs each transfer.
    Registers Telegram command handlers for `/addcoins` and `/transfers`.
    """
    def __init__(
        self,
        pool: Pool,
        admin_ids: Set[int],
        bot: TelegramClient
    ):
        self.pool = pool
        self.admin_ids = set(admin_ids)
        self.bot = bot
        self.logger = logging.getLogger(__name__)

        # Register Telegram handlers
        self.bot.add_event_handler(
            self._add_coins_handler,
            events.NewMessage(pattern=r'^/addcoins\s+(\d+)\s+(\d+)$')
        )
        self.bot.add_event_handler(
            self._transfers_history_handler,
            events.NewMessage(pattern=r'^/transfers(?:\s+(\d+))?$')
        )

    async def transfer(
        self,
        admin_id: int,
        user_id: int,
        amount: int
    ) -> None:
        """
        Transfer `amount` coins to `user_id`, initiated by `admin_id`.
        Raises PermissionError if not admin, ValueError if amount <= 0.
        """
        if admin_id not in self.admin_ids:
            raise PermissionError(f"User {admin_id} is not authorized.")
        if amount <= 0:
            raise ValueError("Transfer amount must be positive.")

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                # Ensure user has a row
                await cur.execute(
                    "INSERT INTO user_coins (user_id, coins) VALUES (%s, 0) "
                    "ON DUPLICATE KEY UPDATE coins=coins",
                    (user_id,)
                )
                # Add coins
                await cur.execute(
                    "UPDATE user_coins SET coins = coins + %s WHERE user_id = %s",
                    (amount, user_id)
                )
                # Log transfer
                await cur.execute(
                    "INSERT INTO coin_transfers (admin_id, user_id, amount) VALUES (%s, %s, %s)",
                    (admin_id, user_id, amount)
                )
            await conn.commit()

        self.logger.info(f"Admin {admin_id} transferred {amount} coins to user {user_id}.")

    async def get_transfer_history(
        self,
        limit: int = 100
    ) -> List[Tuple]:
        """
        Retrieve the most recent transfer logs as list of tuples:
        (id, admin_id, user_id, amount, created_at)
        """
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT id, admin_id, user_id, amount, created_at "
                    "FROM coin_transfers "
                    "ORDER BY created_at DESC LIMIT %s",
                    (limit,)
                )
                rows = await cur.fetchall()
        return rows

    async def _add_coins_handler(self, event):
        """
        Handler for `/addcoins <user_id> <amount>`
        """
        sender = event.sender_id
        if sender not in self.admin_ids:
            return  # ignore unauthorized

        user_id_str, amount_str = event.pattern_match.groups()
        user_id = int(user_id_str)
        amount = int(amount_str)

        try:
            await self.transfer(sender, user_id, amount)
            new_balance = await coin_db.get_balance(user_id)
            await event.respond(
                f"✅ Added {amount} coins to user `{user_id}`.\n"
                f"💰 New balance: {new_balance} coins."
            )
        except Exception as e:
            await event.respond(f"❌ Error transferring coins: {e}")

    async def _transfers_history_handler(self, event):
        """
        Handler for `/transfers [limit]` to view recent transfer logs.
        """
        sender = event.sender_id
        if sender not in self.admin_ids:
            return

        limit_group = event.pattern_match.group(1)
        limit = int(limit_group) if limit_group else 20
        rows = await self.get_transfer_history(limit)

        if not rows:
            return await event.respond("No transfers logged yet.")

        lines = [
            f"{r[0]}: admin {r[1]} → user {r[2]} = {r[3]} coins @ {r[4]}"
            for r in rows
        ]
        await event.respond("📜 Recent transfers:\n" + "\n".join(lines))
