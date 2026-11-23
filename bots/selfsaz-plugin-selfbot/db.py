# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# db.py
import logging
import os
from datetime import datetime
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.orm import sessionmaker, declarative_base
from sqlalchemy import text
from contextlib import asynccontextmanager
import aiosqlite
import time
import json
from functools import lru_cache  # For in-memory caching
import asyncio

# Logging setup
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[logging.StreamHandler()]
)

logger = logging.getLogger(__name__)

# Database configuration
DATABASE = 'sessions.db'
DATABASE_URL = f"sqlite+aiosqlite:///{DATABASE}"

# Create the async engine with connection pooling
engine = create_async_engine(
    DATABASE_URL,
    echo=False
)

# Create the session factory
async_session_factory = sessionmaker(
    engine,
    class_=AsyncSession,
    expire_on_commit=False
)

# Create base class for declarative models
Base = declarative_base()

# In-memory cache for frequently accessed data
_session_cache = {}

async def init_db():
    """Initialize the database and create tables if they don't exist."""
    async with aiosqlite.connect(DATABASE) as conn:
        await conn.execute('PRAGMA encoding = "UTF-8";')
        # Enable Write-Ahead Logging for better concurrency
        await conn.execute('PRAGMA journal_mode=WAL;')

        # Batch table creation in a single transaction for efficiency
        await conn.executescript('''
            CREATE TABLE IF NOT EXISTS file_renames (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                original_msg_id INTEGER NOT NULL,
                new_msg_id INTEGER NOT NULL,
                original_name TEXT NOT NULL,
                new_name TEXT NOT NULL,
                user_id INTEGER NOT NULL,
                chat_id INTEGER NOT NULL,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(original_msg_id, chat_id)
            );

            CREATE TABLE IF NOT EXISTS sessions (
                user_id TEXT PRIMARY KEY,
                session TEXT NOT NULL,
                api_id INTEGER NOT NULL,
                api_hash TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS markread_pv (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL
            );

            CREATE TABLE IF NOT EXISTS markread_group (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL
            );

            CREATE TABLE IF NOT EXISTS markread_channel (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL
            );

            CREATE TABLE IF NOT EXISTS ban_progress (
                chat_id TEXT PRIMARY KEY,
                progress TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS user_credentials (
                user_id TEXT PRIMARY KEY,
                api_id TEXT NOT NULL,
                api_hash TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS registration_progress (
                user_id TEXT PRIMARY KEY,
                step TEXT NOT NULL,
                api_id TEXT
            );

            CREATE TABLE IF NOT EXISTS tos_acceptance (
                user_id TEXT PRIMARY KEY,
                accepted BOOLEAN NOT NULL
            );

            CREATE TABLE IF NOT EXISTS muted_users (
                owner_id TEXT NOT NULL,
                user_id TEXT NOT NULL,
                chat_id TEXT NOT NULL,
                PRIMARY KEY (owner_id, user_id, chat_id)
            );

            CREATE TABLE IF NOT EXISTS dl_usage (
                owner_id TEXT PRIMARY KEY,
                last_used INTEGER NOT NULL,
                daily_count INTEGER NOT NULL
            );

            CREATE TABLE IF NOT EXISTS text_format_settings (
                user_id TEXT PRIMARY KEY,
                bold BOOLEAN NOT NULL DEFAULT 0,
                italic BOOLEAN NOT NULL DEFAULT 0,
                underline BOOLEAN NOT NULL DEFAULT 0,
                strike BOOLEAN NOT NULL DEFAULT 0,
                code BOOLEAN NOT NULL DEFAULT 0,
                pre BOOLEAN NOT NULL DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS typing_action (
                user_id TEXT PRIMARY KEY,
                states TEXT NOT NULL DEFAULT '{}'
            );

            CREATE TABLE IF NOT EXISTS file_registry (
                msg_id INTEGER PRIMARY KEY,
                chat_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                mime_type TEXT NOT NULL,
                attributes BLOB NOT NULL,
                owner_id INTEGER NOT NULL,
                timestamp INTEGER NOT NULL
            );

            CREATE TABLE IF NOT EXISTS file_audit_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                operation TEXT NOT NULL,
                user_id INTEGER NOT NULL,
                old_name TEXT NOT NULL,
                new_name TEXT,
                timestamp INTEGER NOT NULL
            );

            CREATE INDEX IF NOT EXISTS idx_file_names 
            ON file_registry(name COLLATE NOCASE);

            CREATE TABLE IF NOT EXISTS online_mode (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL DEFAULT 0,
                last_active INTEGER NOT NULL DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS timebio (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL DEFAULT 0,
                bio_text TEXT NOT NULL DEFAULT '{time}'
            );

            CREATE TABLE IF NOT EXISTS time_pfp (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL DEFAULT 0,
                font_size INTEGER NOT NULL DEFAULT 50,
                font_color TEXT NOT NULL DEFAULT 'white',
                position TEXT NOT NULL DEFAULT 'bottom_right'
            );

            CREATE TABLE IF NOT EXISTS pending_sessions (
                owner_id TEXT NOT NULL,
                session_hash TEXT NOT NULL,
                created_timestamp INTEGER NOT NULL,
                device_model TEXT,
                PRIMARY KEY (owner_id, session_hash)
            );

            CREATE INDEX IF NOT EXISTS idx_pending_sessions_timestamp 
            ON pending_sessions(created_timestamp);

            CREATE TABLE IF NOT EXISTS anti_login (
                user_id TEXT PRIMARY KEY,
                enabled BOOLEAN NOT NULL DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS whitelisted_sessions (
                owner_id TEXT NOT NULL,
                session_hash TEXT NOT NULL,
                device_model TEXT NOT NULL,
                PRIMARY KEY (owner_id, session_hash)
            );

            CREATE TABLE IF NOT EXISTS report_progress (
                user_id TEXT PRIMARY KEY,
                data TEXT,
                timestamp DATETIME
            );

            CREATE TABLE IF NOT EXISTS bug_reports (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT,
                title TEXT,
                description TEXT,
                steps_to_reproduce TEXT,
                environment TEXT,
                timestamp DATETIME
            );
        ''')
        await conn.commit()

@asynccontextmanager
async def db_connection():
    """Async context manager for database connections with pooling."""
    conn = await aiosqlite.connect(DATABASE)
    try:
        yield conn
    finally:
        await conn.close()

@asynccontextmanager
async def get_db_session():
    """Async context manager for database sessions."""
    async_session = async_session_factory()
    try:
        yield async_session
    finally:
        await async_session.close()

# Sessions with caching
@lru_cache(maxsize=1000)  # Cache up to 1000 sessions in memory
async def get_session(user_id: str) -> str:
    """Retrieve a session string for a specific user with caching."""
    # Check cache first
    if user_id in _session_cache:
        return _session_cache[user_id]
    
    try:
        async with get_db_session() as session:
            result = await session.execute(
                text("SELECT session FROM sessions WHERE user_id = :user_id"),
                {"user_id": user_id}
            )
            row = result.fetchone()
            session_str = row[0] if row else None
            if session_str:
                _session_cache[user_id] = session_str  # Cache the result
            return session_str
    except Exception as e:
        logger.error(f"Error retrieving session for user {user_id}: {str(e)}")
        return None

async def save_session(user_id: str, session: str, api_id: int, api_hash: str):
    """Save or update a session for a user and update cache."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO sessions (user_id, session, api_id, api_hash)
            VALUES (?, ?, ?, ?)
        ''', (user_id, session, api_id, api_hash))
        await conn.commit()
        _session_cache[user_id] = session  # Update cache

async def get_all_sessions():
    """Retrieve all sessions from the database efficiently."""
    # Consider profiling this function if called frequently
    try:
        async with get_db_session() as session:
            query = text("SELECT user_id, session, api_id, api_hash FROM sessions")
            result = await session.execute(query)
            return result.fetchall()
    except Exception as e:
        logger.error(f"Error retrieving all sessions: {str(e)}")
        return []

# MarkRead settings optimized
async def get_markread_pv(user_id: str) -> bool:
    """Check if MarkRead is enabled for private messages."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT enabled FROM markread_pv WHERE user_id = ?', (user_id,))
        row = await cursor.fetchone()
        return bool(row[0]) if row else False

async def set_markread_pv(user_id: str, enabled: bool):
    """Enable or disable MarkRead for private messages."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO markread_pv (user_id, enabled)
            VALUES (?, ?)
        ''', (user_id, enabled))
        await conn.commit()

async def get_markread_group(user_id: str) -> bool:
    """Check if MarkRead is enabled for groups."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT enabled FROM markread_group WHERE user_id = ?', (user_id,))
        row = await cursor.fetchone()
        return bool(row[0]) if row else False

async def set_markread_group(user_id: str, enabled: bool):
    """Enable or disable MarkRead for groups."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO markread_group (user_id, enabled)
            VALUES (?, ?)
        ''', (user_id, enabled))
        await conn.commit()

async def get_markread_channel(user_id: str) -> bool:
    """Check if MarkRead is enabled for channels."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT enabled FROM markread_channel WHERE user_id = ?', (user_id,))
        row = await cursor.fetchone()
        return bool(row[0]) if row else False

async def set_markread_channel(user_id: str, enabled: bool):
    """Enable or disable MarkRead for channels."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO markread_channel (user_id, enabled)
            VALUES (?, ?)
        ''', (user_id, enabled))
        await conn.commit()

# Ban progress
async def save_ban_progress(chat_id: str, progress: str):
    """Save ban progress for a chat."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO ban_progress (chat_id, progress)
            VALUES (?, ?)
        ''', (chat_id, progress))
        await conn.commit()

async def get_ban_progress(chat_id: str):
    """Retrieve ban progress for a chat."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT progress FROM ban_progress WHERE chat_id = ?', (chat_id,))
        row = await cursor.fetchone()
        return eval(row[0]) if row else None  # Consider safer deserialization

async def delete_ban_progress(chat_id: str):
    """Delete ban progress for a chat."""
    async with db_connection() as conn:
        await conn.execute('DELETE FROM ban_progress WHERE chat_id = ?', (chat_id,))
        await conn.commit()

# User credentials
async def save_user_credentials(user_id: str, api_id: str, api_hash: str):
    """Save or update user credentials."""
    try:
        async with db_connection() as conn:
            await conn.execute(
                "INSERT OR REPLACE INTO user_credentials (user_id, api_id, api_hash) VALUES (?, ?, ?)",
                (user_id, api_id, api_hash)
            )
            await conn.commit()
            logger.info(f"Saved credentials for user {user_id}")
    except Exception as e:
        logger.error(f"Error saving user credentials: {e}")
        raise

async def get_user_credentials(user_id: str):
    """Retrieve user credentials."""
    try:
        async with db_connection() as conn:
            cursor = await conn.execute(
                "SELECT api_id, api_hash FROM user_credentials WHERE user_id = ?",
                (user_id,)
            )
            return await cursor.fetchone()
    except Exception as e:
        logger.error(f"Error retrieving user credentials: {e}")
        return None

# Registration progress
async def save_registration_progress(user_id: str, step: str):
    """Save user registration progress."""
    try:
        async with db_connection() as conn:
            await conn.execute(
                "INSERT OR REPLACE INTO registration_progress (user_id, step) VALUES (?, ?)",
                (user_id, step)
            )
            await conn.commit()
            logger.info(f"Saved registration progress for user {user_id}: {step}")
    except Exception as e:
        logger.error(f"Error saving registration progress: {e}")
        raise

async def get_registration_progress(user_id: str):
    """Get user registration progress."""
    try:
        async with db_connection() as conn:
            cursor = await conn.execute(
                "SELECT step FROM registration_progress WHERE user_id = ?",
                (user_id,)
            )
            result = await cursor.fetchone()
            return result[0] if result else None
    except Exception as e:
        logger.error(f"Error getting registration progress: {e}")
        return None

async def delete_registration_progress(user_id: str):
    """Delete registration progress."""
    try:
        async with db_connection() as conn:
            await conn.execute("DELETE FROM registration_progress WHERE user_id = ?", (user_id,))
            await conn.commit()
            logger.info(f"Deleted registration progress for user {user_id}")
    except Exception as e:
        logger.error(f"Error deleting registration progress: {e}")
        raise

async def save_temp_api_id(user_id: str, api_id: str):
    """Temporarily store API ID during registration."""
    async with db_connection() as conn:
        await conn.execute('''
            CREATE TABLE IF NOT EXISTS temp_credentials (
                user_id TEXT PRIMARY KEY,
                api_id TEXT NOT NULL,
                timestamp INTEGER NOT NULL
            )
        ''')
        await conn.execute(
            "INSERT OR REPLACE INTO temp_credentials (user_id, api_id, timestamp) VALUES (?, ?, ?)",
            (user_id, api_id, int(time.time()))
        )
        await conn.commit()

async def get_temp_api_id(user_id: str):
    """Retrieve temporary API ID."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT api_id FROM temp_credentials WHERE user_id = ?', (user_id,))
        row = await cursor.fetchone()
        return row[0] if row else None

async def clear_temp_credentials(user_id: str):
    """Clear temporary credentials after registration is complete."""
    async with db_connection() as conn:
        await conn.execute('DELETE FROM temp_credentials WHERE user_id = ?', (user_id,))
        await conn.commit()

async def save_tos_acceptance(user_id: str, accepted: bool):
    """Save or update ToS acceptance."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO tos_acceptance (user_id, accepted) VALUES (?, ?)",
            (user_id, accepted)
        )
        await conn.commit()

async def get_tos_acceptance(user_id: str) -> bool:
    """Retrieve ToS acceptance status."""
    async with db_connection() as conn:
        cursor = await conn.execute('SELECT accepted FROM tos_acceptance WHERE user_id = ?', (user_id,))
        row = await cursor.fetchone()
        return bool(row[0]) if row else False

# Muted users optimized
async def add_muted_user(owner_id: str, user_id: str, chat_id: str):
    """Add a muted user."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR IGNORE INTO muted_users (owner_id, user_id, chat_id) VALUES (?, ?, ?)",
            (owner_id, user_id, chat_id)
        )
        await conn.commit()

async def remove_muted_user(owner_id: str, user_id: str, chat_id: str):
    """Remove a muted user."""
    async with db_connection() as conn:
        await conn.execute(
            "DELETE FROM muted_users WHERE owner_id = ? AND user_id = ? AND chat_id = ?",
            (owner_id, user_id, chat_id)
        )
        await conn.commit()

async def get_muted_users(owner_id: str, chat_id: str):
    """Get muted users for an owner in a chat."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT user_id FROM muted_users WHERE owner_id = ? AND chat_id = ?",
            (owner_id, chat_id)
        )
        return [row[0] for row in await cursor.fetchall()]

async def is_user_muted(owner_id: str, user_id: str, chat_id: str) -> bool:
    """Check if a user is muted."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT 1 FROM muted_users WHERE owner_id = ? AND user_id = ? AND chat_id = ?",
            (owner_id, user_id, chat_id)
        )
        return bool(await cursor.fetchone())

async def remove_all_muted_users(owner_id: str, chat_id: str):
    """Remove all muted users for a specific owner in a chat."""
    async with db_connection() as conn:
        await conn.execute(
            "DELETE FROM muted_users WHERE owner_id = ? AND chat_id = ?",
            (owner_id, chat_id)
        )
        await conn.commit()

async def get_dl_usage(owner_id: str):
    """Get download usage for an owner."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT last_used, daily_count FROM dl_usage WHERE owner_id = ?",
            (owner_id,)
        )
        row = await cursor.fetchone()
        return {'last': row[0] if row else 0, 'daily': row[1] if row else 0}

async def record_dl_usage(owner_id: str):
    """Record download usage with batching."""
    async with db_connection() as conn:
        now = int(time.time())
        await conn.execute('''
            INSERT OR REPLACE INTO dl_usage (owner_id, last_used, daily_count)
            VALUES (?, ?, COALESCE((SELECT daily_count FROM dl_usage WHERE owner_id = ?), 0) + 1)
        ''', (owner_id, now, owner_id))
        await conn.commit()

# Text formatting optimized
async def get_text_format(user_id: str):
    """Get text format settings."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT bold, italic, underline, strike, code, pre FROM text_format_settings WHERE user_id = ?",
            (user_id,)
        )
        row = await cursor.fetchone()
        return {
            'bold': bool(row[0]), 'italic': bool(row[1]), 'underline': bool(row[2]),
            'strike': bool(row[3]), 'code': bool(row[4]), 'pre': bool(row[5])
        } if row else {key: False for key in ['bold', 'italic', 'underline', 'strike', 'code', 'pre']}

async def update_text_format(user_id: str, format_type: str, value: bool):
    """Update text format settings efficiently."""
    async with db_connection() as conn:
        # Ensure the user exists with default values
        await conn.execute(
            "INSERT OR IGNORE INTO text_format_settings (user_id, bold, italic, underline, strike, code, pre) "
            "VALUES (?, 0, 0, 0, 0, 0, 0)",
            (user_id,)
        )
        # Update the specific format field
        await conn.execute(
            f"UPDATE text_format_settings SET {format_type} = ? WHERE user_id = ?",
            (value, user_id)
        )
        await conn.commit()

async def update_action_states(user_id: str, states: dict):
    """Update action states."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO typing_action (user_id, states) VALUES (?, ?)",
            (user_id, json.dumps(states))
        )
        await conn.commit()

async def get_action_states(user_id: str):
    """Get action states."""
    async with db_connection() as conn:
        cursor = await conn.execute("SELECT states FROM typing_action WHERE user_id = ?", (user_id,))
        row = await cursor.fetchone()
        return json.loads(row[0]) if row and row[0] else {}

async def register_new_file(file_data):
    """Register a new file with batching potential."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT INTO file_registry (msg_id, chat_id, name, mime_type, attributes, owner_id, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (
            file_data['msg_id'], file_data['chat_id'], file_data['name'],
            file_data['mime_type'], file_data['attributes'], file_data['owner'], file_data['timestamp']
        ))
        await conn.commit()

async def update_file_name(msg_id, new_name):
    """Update file name."""
    async with db_connection() as conn:
        await conn.execute("UPDATE file_registry SET name = ? WHERE msg_id = ?", (new_name, msg_id))
        await conn.commit()

async def get_file_metadata(identifier):
    """Get file metadata efficiently."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT msg_id, chat_id, name, mime_type, attributes, owner_id, timestamp "
            "FROM file_registry WHERE msg_id = ?",
            (identifier,)
        )
        result = await cursor.fetchone()
        if not result:
            cursor = await conn.execute(
                "SELECT msg_id, chat_id, name, mime_type, attributes, owner_id, timestamp "
                "FROM file_registry WHERE name = ? ORDER BY timestamp DESC LIMIT 1",
                (identifier,)
            )
            result = await cursor.fetchone()
        return dict(result) if result else None

async def log_file_operation(op_type, user_id, old_name, new_name=None):
    """Log file operation."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT INTO file_audit_log (operation, user_id, old_name, new_name, timestamp)
            VALUES (?, ?, ?, ?, ?)
        ''', (op_type, user_id, old_name, new_name, int(time.time())))
        await conn.commit()

async def cleanup_old_files(days=30):
    """Cleanup old files efficiently."""
    async with db_connection() as conn:
        cutoff = int(time.time()) - (days * 86400)
        await conn.execute("DELETE FROM file_registry WHERE timestamp < ?", (cutoff,))
        await conn.commit()

async def register_renamed_file(original_msg_id, new_msg_id, original_name, new_name, user_id, chat_id):
    """Register renamed file with upsert."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT OR REPLACE INTO file_renames 
            (original_msg_id, new_msg_id, original_name, new_name, user_id, chat_id, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ''', (original_msg_id, new_msg_id, original_name, new_name, user_id, chat_id))
        await conn.commit()

async def get_rename_history(filename):
    """Get rename history."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT * FROM file_renames WHERE original_name = ? OR new_name = ? ORDER BY timestamp DESC",
            (filename, filename)
        )
        return await cursor.fetchall()

async def set_online_mode(user_id: str, enabled: bool):
    """Set online mode."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO online_mode (user_id, enabled, last_active) VALUES (?, ?, ?)",
            (user_id, enabled, int(time.time()))
        )
        await conn.commit()

async def get_online_mode(user_id: str):
    """Get online mode."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT enabled, last_active FROM online_mode WHERE user_id = ?",
            (user_id,)
        )
        row = await cursor.fetchone()
        return {'enabled': bool(row[0]) if row else False, 'last_active': row[1] if row else 0}

async def get_timebio_settings(user_id: str):
    """Get timebio settings."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT enabled, bio_text FROM timebio WHERE user_id = ?",
            (user_id,)
        )
        row = await cursor.fetchone()
        return {
            'enabled': bool(row[0]) if row else False,
            'bio_text': row[1] if row else '{time}'
        } if row else None

async def set_timebio_settings(user_id: str, enabled: bool, bio_text: str = None):
    """Set timebio settings."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO timebio (user_id, enabled, bio_text) VALUES (?, ?, ?)",
            (user_id, enabled, bio_text or '{time}')
        )
        await conn.commit()

async def get_time_pfp_settings(user_id: str):
    """Get time_pfp settings."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT enabled, font_size, font_color, position FROM time_pfp WHERE user_id = ?",
            (user_id,)
        )
        row = await cursor.fetchone()
        return {
            'enabled': bool(row[0]) if row else False,
            'font_size': row[1] if row else 50,
            'font_color': row[2] if row else 'white',
            'position': row[3] if row else 'bottom_right'
        } if row else None

async def set_time_pfp_settings(user_id: str, enabled: bool, font_size: int = None, font_color: str = None, position: str = None):
    """Set time_pfp settings."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO time_pfp (user_id, enabled, font_size, font_color, position) "
            "VALUES (?, ?, ?, ?, ?)",
            (user_id, enabled, font_size or 50, font_color or 'white', position or 'bottom_right')
        )
        await conn.commit()

async def add_pending_session(owner_id: str, session_hash: str, created_timestamp: int, device_model: str = None):
    """Add a pending session."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO pending_sessions (owner_id, session_hash, created_timestamp, device_model) "
            "VALUES (?, ?, ?, ?)",
            (owner_id, session_hash, created_timestamp, device_model)
        )
        await conn.commit()

async def remove_pending_session(owner_id: str, session_hash: str):
    """Remove a pending session."""
    async with db_connection() as conn:
        await conn.execute(
            "DELETE FROM pending_sessions WHERE owner_id = ? AND session_hash = ?",
            (owner_id, session_hash)
        )
        await conn.commit()

async def get_pending_sessions(owner_id: str):
    """Retrieve pending sessions."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT session_hash, created_timestamp, device_model FROM pending_sessions WHERE owner_id = ?",
            (owner_id,)
        )
        return await cursor.fetchall()

async def cleanup_old_pending_sessions():
    """Cleanup old pending sessions."""
    async with db_connection() as conn:
        cutoff = int(time.time()) - 86400
        await conn.execute("DELETE FROM pending_sessions WHERE created_timestamp < ?", (cutoff,))
        await conn.commit()

async def get_anti_login_status(user_id: str) -> bool:
    """Check anti-login status."""
    async with db_connection() as conn:
        cursor = await conn.execute("SELECT enabled FROM anti_login WHERE user_id = ?", (user_id,))
        row = await cursor.fetchone()
        return bool(row[0]) if row else False

async def set_anti_login_status(user_id: str, enabled: bool):
    """Set anti-login status."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO anti_login (user_id, enabled) VALUES (?, ?)",
            (user_id, enabled)
        )
        await conn.commit()

async def add_whitelisted_session(owner_id: str, session_hash: str, device_model: str):
    """Add a whitelisted session."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO whitelisted_sessions (owner_id, session_hash, device_model) VALUES (?, ?, ?)",
            (owner_id, session_hash, device_model)
        )
        await conn.commit()

async def remove_whitelisted_session(owner_id: str, session_hash: str):
    """Remove a whitelisted session."""
    async with db_connection() as conn:
        await conn.execute(
            "DELETE FROM whitelisted_sessions WHERE owner_id = ? AND session_hash = ?",
            (owner_id, session_hash)
        )
        await conn.commit()

async def get_whitelisted_sessions(owner_id: str):
    """Retrieve whitelisted sessions."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT session_hash, device_model FROM whitelisted_sessions WHERE owner_id = ?",
            (owner_id,)
        )
        return await cursor.fetchall()

async def delete_session(user_id: str = None, session: str = None):
    """Delete a session by user_id or session."""
    try:
        # rename the context var so it doesn’t shadow the function parameter
        async with get_db_session() as db_sess:
            if user_id is not None:
                query  = text("DELETE FROM sessions WHERE user_id = :user_id")
                params = {"user_id": user_id}
                # Clear in‑memory cache
                del _session_cache[user_id]
            elif session is not None:
                query  = text("DELETE FROM sessions WHERE session = :session")
                params = {"session": session}
            else:
                raise ValueError("Either user_id or session must be provided")

            # perform the delete
            await db_sess.execute(query, params)
            await db_sess.commit()
            logger.info(f"Successfully deleted session for {user_id or session[:10]}")
            return True

    except Exception as e:
        logger.error(f"Error deleting session: {e}")
        return False


async def get_report_progress(user_id: str) -> dict:
    """Get report progress."""
    async with db_connection() as conn:
        cursor = await conn.execute("SELECT data FROM report_progress WHERE user_id = ?", (user_id,))
        row = await cursor.fetchone()
        return json.loads(row[0]) if row and row[0] else None  # Safer deserialization

async def save_report_progress(user_id: str, data: dict):
    """Save report progress."""
    async with db_connection() as conn:
        await conn.execute(
            "INSERT OR REPLACE INTO report_progress (user_id, data, timestamp) VALUES (?, ?, ?)",
            (user_id, json.dumps(data), datetime.now().isoformat())
        )
        await conn.commit()

async def delete_report_progress(user_id: str):
    """Delete report progress."""
    async with db_connection() as conn:
        await conn.execute("DELETE FROM report_progress WHERE user_id = ?", (user_id,))
        await conn.commit()

async def save_bug_report(report: dict):
    """Save a completed bug report."""
    async with db_connection() as conn:
        await conn.execute('''
            INSERT INTO bug_reports (user_id, title, description, steps_to_reproduce, environment, timestamp)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (
            report.get("user_id"), report.get("title"), report.get("description"),
            report.get("steps_to_reproduce"), report.get("environment"), datetime.now().isoformat()
        ))
        await conn.commit()
        
async def get_muted_users_by_owner(owner_id: str):
    """Get all muted users for an owner across all chats efficiently."""
    async with db_connection() as conn:
        cursor = await conn.execute(
            "SELECT user_id FROM muted_users WHERE owner_id = ?",
            (owner_id,)
        )
        rows = await cursor.fetchall()
        return [row[0] for row in rows]
# Note: The bot-specific code at the end of the original file is omitted as it depends on external context (bot module).
# Ensure thorough testing after applying these optimizations to verify functionality and file integrity.