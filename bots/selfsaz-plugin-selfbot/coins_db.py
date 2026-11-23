# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import aiomysql
import secrets
import string
from datetime import datetime, timedelta
from config import COIN_DB_CONFIG


class CoinDB:
    """
    Asynchronous database module for managing user coins and license codes.
    Tables are created if they don't exist, but never dropped—so your data stays
    intact across bot restarts.
    """
    def __init__(self):
        self.pool = None

    async def init(self):
        """
        Create the connection pool and ensure necessary tables exist.
        """
        self.pool = await aiomysql.create_pool(**COIN_DB_CONFIG)
        await self._create_tables()

    async def _create_tables(self):
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                # Ensure user_coins exists
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS user_coins (
                        user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
                        coins   BIGINT         NOT NULL DEFAULT 0
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                """)
                await conn.commit()

                # Ensure licenses exists (with timestamps)
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS licenses (
                        code            VARCHAR(64)         PRIMARY KEY,
                        user_id         BIGINT UNSIGNED     NOT NULL,
                        duration_months INT                 NOT NULL,
                        created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        used            BOOLEAN             NOT NULL DEFAULT FALSE,
                        redeemed_at     TIMESTAMP           NULL DEFAULT NULL,
                        expires_at      TIMESTAMP           NULL DEFAULT NULL,
                        INDEX (user_id),
                        FOREIGN KEY (user_id)
                          REFERENCES user_coins(user_id)
                          ON UPDATE CASCADE
                          ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                """)
                await conn.commit()

                # Ensure coin_transfers exists
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS coin_transfers (
                        id          INT AUTO_INCREMENT PRIMARY KEY,
                        admin_id    BIGINT UNSIGNED NOT NULL,
                        user_id     BIGINT UNSIGNED NOT NULL,
                        amount      BIGINT         NOT NULL,
                        created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        INDEX (admin_id),
                        INDEX (user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                """)
                await conn.commit()

    async def get_balance(self, user_id: int) -> int:
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT coins FROM user_coins WHERE user_id=%s", (user_id,)
                )
                row = await cur.fetchone()
                return row[0] if row else 0

    async def add_coins(self, user_id: int, amount: int) -> None:
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute("""
                    INSERT INTO user_coins (user_id, coins)
                    VALUES (%s, %s)
                    ON DUPLICATE KEY UPDATE coins = coins + VALUES(coins)
                """, (user_id, amount))
                await conn.commit()

    def _generate_code(self, length: int = 16) -> str:
        alphabet = string.ascii_uppercase + string.digits
        return ''.join(secrets.choice(alphabet) for _ in range(length))

    async def create_license(
        self, user_id: int, duration_months: int, cost_per_month: int
    ) -> str:
        total_cost = duration_months * cost_per_month
        current = await self.get_balance(user_id)
        if current < total_cost:
            raise ValueError(
                f"Insufficient coins: {current} available, {total_cost} required"
            )

        # Ensure user exists first (coins=0)
        await self.add_coins(user_id, 0)
        # Deduct cost
        await self.add_coins(user_id, -total_cost)

        # Generate and store license
        code = self._generate_code()
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "INSERT INTO licenses (code, user_id, duration_months) VALUES (%s, %s, %s)",
                    (code, user_id, duration_months)
                )
                await conn.commit()
        return code

    async def get_license(self, code: str) -> dict:
        async with self.pool.acquire() as conn:
            async with conn.cursor(aiomysql.DictCursor) as cur:
                await cur.execute(
                    "SELECT * FROM licenses WHERE code=%s", (code,)
                )
                return await cur.fetchone()

    async def mark_license_used(self, code: str) -> None:
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "UPDATE licenses SET used=TRUE WHERE code=%s", (code,)
                )
                await conn.commit()

    async def update_license_timestamps(
        self, code: str, redeemed_at: datetime, expires_at: datetime
    ) -> None:
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute("""
                    UPDATE licenses
                    SET used=TRUE, redeemed_at=%s, expires_at=%s
                    WHERE code=%s
                """, (redeemed_at, expires_at, code))
                await conn.commit()

    async def get_active_license_for_user(self, user_id: int) -> dict:
        async with self.pool.acquire() as conn:
            async with conn.cursor(aiomysql.DictCursor) as cur:
                await cur.execute("""
                    SELECT * FROM licenses
                    WHERE user_id=%s AND used=TRUE AND expires_at > CURRENT_TIMESTAMP
                    ORDER BY expires_at DESC
                    LIMIT 1
                """, (user_id,))
                return await cur.fetchone()

    async def get_transfer_history(self, limit: int = 100) -> list:
        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute("""
                    SELECT id, admin_id, user_id, amount, created_at
                    FROM coin_transfers
                    ORDER BY created_at DESC
                    LIMIT %s
                """, (limit,))
                return await cur.fetchall()


# Create a singleton instance for easy import
coin_db = CoinDB()
