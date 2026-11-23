# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import aiomysql
import logging
from functools import lru_cache
from contextlib import asynccontextmanager

# Configure logging
logger = logging.getLogger("telegram_bot.db")

# MySQL Connection Pool Configuration
DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'botadmin',
    'password': 'securepass',
    'db': 'botdata',
    'minsize': 15,  # Good balance for ~400 users
    'maxsize': 30,  # Allow for spikes
    'autocommit': True,  # Best for frequent read operations
}

# Global pool
pool = None

async def init_db_pool():
    global pool
    if pool is None:
        try:
            pool = await aiomysql.create_pool(**DB_CONFIG)
            logger.info("MySQL pool created")
        except Exception as e:
            logger.error(f"Failed to create MySQL pool: {e}")
            raise

@asynccontextmanager
async def get_db_connection():
    global pool
    if pool is None:
        await init_db_pool()
    async with pool.acquire() as conn:
        async with conn.cursor(aiomysql.DictCursor) as cursor:
            yield cursor

# In-memory caching with TTL
@lru_cache(maxsize=1000)  # Adjust based on your user count
async def is_banned_cached(user_id):
    """Check if user is banned with in-memory caching"""
    result = await execute_query(
        "SELECT 1 FROM global_bans WHERE user_id = %s LIMIT 1",
        (str(user_id),),
        fetch=True
    )
    return bool(result)

@lru_cache(maxsize=1000)
async def is_authorized_cached(user_id):
    """Check if user is authorized with in-memory caching"""
    result = await execute_query(
        "SELECT 1 FROM authorized_users WHERE user_id = %s LIMIT 1",
        (str(user_id),),
        fetch=True
    )
    return bool(result)

# Clear cache entry for a specific user
def clear_user_cache(user_id):
    """Clear the cache for a specific user ID"""
    user_id_str = str(user_id)
    is_banned_cached.cache_clear()
    is_authorized_cached.cache_clear()

async def execute_query(query, params=None, fetch=False):
    try:
        async with get_db_connection() as cursor:
            await cursor.execute(query, params or ())
            return await cursor.fetchall() if fetch else True
    except Exception as e:
        logger.error(f"Query failed: {e}")
        return None

# Fast ban/auth check functions
async def is_banned(user_id):
    return await is_banned_cached(str(user_id))

async def is_authorized(user_id):
    return await is_authorized_cached(str(user_id))

async def ban_user(user_id):
    user_id_str = str(user_id)
    query = "INSERT IGNORE INTO global_bans (user_id) VALUES (%s)"
    result = await execute_query(query, (user_id_str,))
    clear_user_cache(user_id_str)  # Clear the cache for this user
    return result is not None

async def unban_user(user_id):
    user_id_str = str(user_id)
    query = "DELETE FROM global_bans WHERE user_id = %s"
    result = await execute_query(query, (user_id_str,))
    clear_user_cache(user_id_str)  # Clear the cache for this user
    return result is not None

async def authorize_user(user_id):
    user_id_str = str(user_id)
    query = "INSERT IGNORE INTO authorized_users (user_id) VALUES (%s)"
    result = await execute_query(query, (user_id_str,))
    clear_user_cache(user_id_str)
    return result is not None

async def revoke_user(user_id):
    user_id_str = str(user_id)
    query = "DELETE FROM authorized_users WHERE user_id = %s"
    result = await execute_query(query, (user_id_str,))
    clear_user_cache(user_id_str)
    return result is not None

# For batch operations
async def batch_ban_users(user_ids):
    if not user_ids:
        return True
    try:
        # Convert all to string
        user_ids = [str(uid) for uid in user_ids]
        
        async with get_db_connection() as cursor:
            values = [(uid,) for uid in user_ids]
            await cursor.executemany(
                "INSERT IGNORE INTO global_bans (user_id) VALUES (%s)",
                values
            )
            
            # Clear cache for each banned user
            for uid in user_ids:
                clear_user_cache(uid)
                
            return True
    except Exception as e:
        logger.error(f"batch_ban_users failed: {e}")
        return False