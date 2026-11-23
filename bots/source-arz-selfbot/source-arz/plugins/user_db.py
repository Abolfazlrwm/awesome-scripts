# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
User database module for the currency bot.
This module handles storing and retrieving user information.
"""
import os
import json
import logging
import sqlite3
import time
from typing import Dict, List, Optional, Any
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger('UserDB')
class UserDatabase:
    """Database for storing user information"""
    def __init__(self, db_path: str = 'data/users.db'):
        """Initialize the user database
        Args:
            db_path: Path to the SQLite database file
        """
        os.makedirs(os.path.dirname(db_path), exist_ok=True)
        self.db_path = db_path
        self.conn = None
        self.cursor = None
        self._connect()
        self._create_tables()
        logger.info(f"User database initialized at {db_path}")
    def _connect(self):
        """Connect to the SQLite database"""
        try:
            self.conn = sqlite3.connect(self.db_path)
            self.cursor = self.conn.cursor()
            logger.debug("Connected to the user database")
        except sqlite3.Error as e:
            logger.error(f"Error connecting to the database: {str(e)}")
            raise
    def _create_tables(self):
        """Create the necessary tables if they don't exist"""
        try:
            self.cursor.execute('''
                CREATE TABLE IF NOT EXISTS users (
                    user_id INTEGER PRIMARY KEY,
                    username TEXT,
                    first_name TEXT,
                    last_name TEXT,
                    is_bot BOOLEAN,
                    language_code TEXT,
                    first_seen TIMESTAMP,
                    last_seen TIMESTAMP,
                    interaction_count INTEGER DEFAULT 1
                )
            ''')
            self.cursor.execute('''
                CREATE TABLE IF NOT EXISTS user_stats (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    action TEXT,
                    timestamp TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users (user_id)
                )
            ''')
            self.conn.commit()
            logger.debug("Database tables created successfully")
        except sqlite3.Error as e:
            logger.error(f"Error creating tables: {str(e)}")
            raise
    def add_user(self, user_id: int, username: Optional[str] = None,
                 first_name: Optional[str] = None, last_name: Optional[str] = None,
                 is_bot: bool = False, language_code: Optional[str] = None) -> bool:
        """Add a new user or update an existing user
        Args:
            user_id: The Telegram user ID
            username: The user's username (without @)
            first_name: The user's first name
            last_name: The user's last name
            is_bot: Whether the user is a bot
            language_code: The user's language code
        Returns:
            True if successful, False otherwise
        """
        try:
            current_time = int(time.time())
            if username:
                username = username.lower().lstrip('@')
            self.cursor.execute("SELECT user_id, interaction_count FROM users WHERE user_id = ?", (user_id,))
            result = self.cursor.fetchone()
            if result:
                interaction_count = result[1] + 1
                self.cursor.execute('''
                    UPDATE users
                    SET username = ?, first_name = ?, last_name = ?,
                        language_code = ?, last_seen = ?, interaction_count = ?
                    WHERE user_id = ?
                ''', (username, first_name, last_name, language_code,
                      current_time, interaction_count, user_id))
                logger.debug(f"Updated user {user_id} ({username}), interaction count: {interaction_count}")
            else:
                self.cursor.execute('''
                    INSERT INTO users (user_id, username, first_name, last_name, is_bot, language_code, first_seen, last_seen, interaction_count)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ON CONFLICT(user_id) DO UPDATE SET
                        username = COALESCE(excluded.username, username),
                        first_name = COALESCE(excluded.first_name, first_name),
                        last_name = COALESCE(excluded.last_name, last_name),
                        is_bot = COALESCE(excluded.is_bot, is_bot),
                        language_code = COALESCE(excluded.language_code, language_code),
                        last_seen = excluded.last_seen,
                        interaction_count = users.interaction_count + 1
                ''', (user_id, username, first_name, last_name, is_bot, language_code, current_time, current_time))
            self.conn.commit()
            self.log_user_action(user_id, 'user_updated')
            return True
        except sqlite3.Error as e:
            logger.error(f"Error adding/updating user {user_id}: {str(e)}")
            return False
    def log_user_action(self, user_id: int, action: str, metadata: Optional[Dict[str, Any]] = None) -> bool:
        """Log a user action to the database
        Args:
            user_id: The Telegram user ID
            action: The action being logged (e.g., 'command_used', 'button_pressed')
            metadata: Optional additional data to store as JSON
        Returns:
            True if successful, False otherwise
        """
        try:
            self.cursor.execute('''
                INSERT INTO user_stats (user_id, action, timestamp, metadata)
                VALUES (?, ?, ?, ?)
            ''', (user_id, action, int(time.time()),
                 json.dumps(metadata) if metadata else None))
            self.cursor.execute('''
                UPDATE users
                SET last_seen = ?,
                    interaction_count = interaction_count + 1
                WHERE user_id = ?
            ''', (int(time.time()), user_id))
            self.conn.commit()
            return True
        except sqlite3.Error as e:
            logger.error(f"Error logging user action: {str(e)}")
            return False
    def get_user(self, user_id: int) -> Optional[Dict[str, Any]]:
        """Get user information by ID
        Args:
            user_id: The Telegram user ID
        Returns:
            Dictionary with user information or None if not found
        """
        try:
            self.cursor.execute('''
                SELECT user_id, username, first_name, last_name, is_bot,
                       language_code, first_seen, last_seen, interaction_count
                FROM users WHERE user_id = ?
            ''', (user_id,))
            result = self.cursor.fetchone()
            if result:
                return {
                    'user_id': result[0],
                    'username': result[1],
                    'first_name': result[2],
                    'last_name': result[3],
                    'is_bot': bool(result[4]),
                    'language_code': result[5],
                    'first_seen': result[6],
                    'last_seen': result[7],
                    'interaction_count': result[8]
                }
            return None
        except sqlite3.Error as e:
            logger.error(f"Error getting user {user_id}: {str(e)}")
            return None
    def get_all_users(self) -> List[Dict[str, Any]]:
        """Get all users in the database
        Returns:
            List of dictionaries with user information
        """
        try:
            self.cursor.execute('''
                SELECT user_id, username, first_name, last_name, is_bot,
                       language_code, first_seen, last_seen, interaction_count
                FROM users
            ''')
            results = self.cursor.fetchall()
            users = []
            for result in results:
                users.append({
                    'user_id': result[0],
                    'username': result[1],
                    'first_name': result[2],
                    'last_name': result[3],
                    'is_bot': bool(result[4]),
                    'language_code': result[5],
                    'first_seen': result[6],
                    'last_seen': result[7],
                    'interaction_count': result[8]
                })
            return users
        except sqlite3.Error as e:
            logger.error(f"Error getting all users: {str(e)}")
            return []
    def get_user_count(self) -> int:
        """Get the total number of users in the database
        Returns:
            Number of users
        """
        try:
            self.cursor.execute("SELECT COUNT(*) FROM users")
            result = self.cursor.fetchone()
            return result[0] if result else 0
        except sqlite3.Error as e:
            logger.error(f"Error getting user count: {str(e)}")
            return 0
    def log_user_action(self, user_id: int, action: str) -> bool:
        """Log a user action
        Args:
            user_id: The Telegram user ID
            action: The action performed
        Returns:
            True if successful, False otherwise
        """
        try:
            current_time = int(time.time())
            self.cursor.execute('''
                INSERT INTO user_stats (user_id, action, timestamp)
                VALUES (?, ?, ?)
            ''', (user_id, action, current_time))
            self.cursor.execute('''
                UPDATE users
                SET last_seen = ?, interaction_count = interaction_count + 1
                WHERE user_id = ?
            ''', (current_time, user_id))
            self.conn.commit()
            return True
        except sqlite3.Error as e:
            logger.error(f"Error logging action for user {user_id}: {str(e)}")
            self.conn.rollback()
            return False
    def close(self):
        """Close the database connection"""
        if self.conn:
            self.conn.close()
            logger.debug("Database connection closed")
user_db = UserDatabase()
