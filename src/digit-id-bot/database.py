"""
database.py
Simple SQLite wrapper for storing and retrieving users.
All functions are small and documented to be easy to test.
"""
import sqlite3
from pathlib import Path
from typing import List, Dict
import logging
from contextlib import contextmanager
import config

logger = logging.getLogger(__name__)
DB_PATH = Path(config.DB_PATH)

@contextmanager
def get_conn():
    """Context manager that yields a sqlite3 connection and commits/closes it."""
    conn = sqlite3.connect(DB_PATH)
    try:
        yield conn
        conn.commit()
    finally:
        conn.close()

def init_db() -> None:
    """
    Initialize the database and create the 'users' table if it doesn't exist.
    'id' is primary key (telegram numeric id) and username is optional text.
    """
    DB_PATH.parent.mkdir(parents=True, exist_ok=True)
    with get_conn() as conn:
        cur = conn.cursor()
        cur.execute("""
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                username TEXT
            );
        """)
    logger.info("Database initialized at %s", DB_PATH)

def add_user(user_id: int, username: str | None) -> None:
    """
    Insert a user into the database if not exists. Uses INSERT OR IGNORE.
    Args:
        user_id: numeric telegram id
        username: username string or None
    """
    username_val = username if username else "ندارد"
    with get_conn() as conn:
        cur = conn.cursor()
        cur.execute("INSERT OR IGNORE INTO users (id, username) VALUES (?, ?)",
                    (user_id, username_val))
    logger.debug("add_user: %s / %s", user_id, username_val)

def get_all_users() -> List[Dict]:
    """
    Return list of users as dicts: [{"id":..., "username":...}, ...]
    """
    with get_conn() as conn:
        cur = conn.cursor()
        cur.execute("SELECT id, username FROM users")
        rows = cur.fetchall()
    return [{"id": row[0], "username": row[1]} for row in rows]

def user_exists(user_id: int) -> bool:
    """Check whether a user exists in the DB."""
    with get_conn() as conn:
        cur = conn.cursor()
        cur.execute("SELECT 1 FROM users WHERE id = ? LIMIT 1", (user_id,))
        return cur.fetchone() is not None
