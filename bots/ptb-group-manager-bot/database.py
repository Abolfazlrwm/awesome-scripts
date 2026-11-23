# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import sqlite3
import json
from config import DB_FILE, DEFAULT_RULES, ADMIN_ID
class Database:
    def __init__(self):
        self.conn = sqlite3.connect(DB_FILE, check_same_thread=False)
        self.cursor = self.conn.cursor()
        self.setup()
    def setup(self):
        """Create necessary tables if they don't exist"""
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS groups (
            group_id INTEGER PRIMARY KEY,
            title TEXT,
            rules TEXT DEFAULT NULL,
            welcome_enabled INTEGER DEFAULT 1,
            antilink_enabled INTEGER DEFAULT 1,
            antiprofanity_enabled INTEGER DEFAULT 1,
            antiforward_enabled INTEGER DEFAULT 1,
            antiporn_enabled INTEGER DEFAULT 1,
            antispam_enabled INTEGER DEFAULT 1,
            anticheating_enabled INTEGER DEFAULT 1,
            antitabchi_enabled INTEGER DEFAULT 1,
            strict_mode INTEGER DEFAULT 0,
            locked INTEGER DEFAULT 0,
            message_limit INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY,
            username TEXT,
            first_name TEXT,
            last_name TEXT,
            is_admin INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS group_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            user_id INTEGER,
            warnings INTEGER DEFAULT 0,
            is_muted INTEGER DEFAULT 0,
            is_banned INTEGER DEFAULT 0,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            FOREIGN KEY (user_id) REFERENCES users (user_id),
            UNIQUE(group_id, user_id)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS statistics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            date TEXT,
            messages_count INTEGER DEFAULT 0,
            new_members INTEGER DEFAULT 0,
            removed_members INTEGER DEFAULT 0,
            warnings_issued INTEGER DEFAULT 0,
            links_blocked INTEGER DEFAULT 0,
            profanity_blocked INTEGER DEFAULT 0,
            forwards_blocked INTEGER DEFAULT 0,
            porn_blocked INTEGER DEFAULT 0,
            spam_blocked INTEGER DEFAULT 0,
            cheating_blocked INTEGER DEFAULT 0,
            tabchi_blocked INTEGER DEFAULT 0,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            UNIQUE(group_id, date)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS custom_commands (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            command TEXT,
            response TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            UNIQUE(group_id, command)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS user_activity (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            user_id INTEGER,
            message_count INTEGER DEFAULT 0,
            last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            FOREIGN KEY (user_id) REFERENCES users (user_id),
            UNIQUE(group_id, user_id)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS spam_detection (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            user_id INTEGER,
            message_count INTEGER DEFAULT 0,
            first_message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            FOREIGN KEY (user_id) REFERENCES users (user_id),
            UNIQUE(group_id, user_id)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS cheating_detection (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            user_id INTEGER,
            suspicious_links_count INTEGER DEFAULT 0,
            external_mentions_count INTEGER DEFAULT 0,
            last_detected TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups (group_id),
            FOREIGN KEY (user_id) REFERENCES users (user_id),
            UNIQUE(group_id, user_id)
        )
        ''')
        self.cursor.execute('''
        CREATE TABLE IF NOT EXISTS tabchi_detection (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            username TEXT,
            join_count INTEGER DEFAULT 0,
            suspicious_score INTEGER DEFAULT 0,
            last_detected TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(user_id)
        )
        ''')
        try:
            self.cursor.execute("SELECT antitabchi_enabled FROM groups LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE groups ADD COLUMN antitabchi_enabled INTEGER DEFAULT 1")
        try:
            self.cursor.execute("SELECT antispam_enabled FROM groups LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE groups ADD COLUMN antispam_enabled INTEGER DEFAULT 1")
        try:
            self.cursor.execute("SELECT anticheating_enabled FROM groups LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE groups ADD COLUMN anticheating_enabled INTEGER DEFAULT 1")
        try:
            self.cursor.execute("SELECT spam_blocked FROM statistics LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE statistics ADD COLUMN spam_blocked INTEGER DEFAULT 0")
        try:
            self.cursor.execute("SELECT cheating_blocked FROM statistics LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE statistics ADD COLUMN cheating_blocked INTEGER DEFAULT 0")
        try:
            self.cursor.execute("SELECT tabchi_blocked FROM statistics LIMIT 1")
        except:
            self.cursor.execute("ALTER TABLE statistics ADD COLUMN tabchi_blocked INTEGER DEFAULT 0")
        self.conn.commit()
    def add_user(self, user_id, username, first_name, last_name, is_admin=0):
        """Add or update a user in the database"""
        self.cursor.execute('''
        INSERT OR REPLACE INTO users (user_id, username, first_name, last_name, is_admin)
        VALUES (?, ?, ?, ?, ?)
        ''', (user_id, username, first_name, last_name, is_admin))
        self.conn.commit()
    def add_group(self, group_id, title):
        """Add or update a group in the database"""
        self.cursor.execute('''
        INSERT OR IGNORE INTO groups (group_id, title, rules)
        VALUES (?, ?, ?)
        ''', (group_id, title, DEFAULT_RULES))
        self.conn.commit()
    def add_group_member(self, group_id, user_id):
        """Add a user to a group in the database"""
        self.cursor.execute('''
        INSERT OR IGNORE INTO group_members (group_id, user_id)
        VALUES (?, ?)
        ''', (group_id, user_id))
        self.conn.commit()
    def get_group_settings(self, group_id):
        """Get group settings"""
        self.cursor.execute('''
        SELECT welcome_enabled, antilink_enabled, antiprofanity_enabled, 
               antiforward_enabled, antiporn_enabled, antispam_enabled, anticheating_enabled,
               antitabchi_enabled, strict_mode, locked, message_limit, rules
        FROM groups WHERE group_id = ?
        ''', (group_id,))
        result = self.cursor.fetchone()
        if result:
            return {
                "welcome_enabled": bool(result[0]),
                "antilink_enabled": bool(result[1]),
                "antiprofanity_enabled": bool(result[2]),
                "antiforward_enabled": bool(result[3]),
                "antiporn_enabled": bool(result[4]),
                "antispam_enabled": bool(result[5]),
                "anticheating_enabled": bool(result[6]),
                "antitabchi_enabled": bool(result[7]),
                "strict_mode": bool(result[8]),
                "locked": bool(result[9]),
                "message_limit": result[10],
                "rules": result[11] or DEFAULT_RULES
            }
        return None
    def update_group_setting(self, group_id, setting, value):
        """Update a specific group setting"""
        if setting not in ["welcome_enabled", "antilink_enabled", "antiprofanity_enabled", 
                          "antiforward_enabled", "antiporn_enabled", "antispam_enabled", 
                          "anticheating_enabled", "antitabchi_enabled", "strict_mode", "locked", "message_limit", "rules"]:
            return False
        self.cursor.execute(f'''
        UPDATE groups SET {setting} = ? WHERE group_id = ?
        ''', (value, group_id))
        self.conn.commit()
        return True
    def warn_user(self, group_id, user_id):
        """Add a warning to a user in a group"""
        self.cursor.execute('''
        UPDATE group_members SET warnings = warnings + 1
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        self.conn.commit()
        self.cursor.execute('''
        SELECT warnings FROM group_members
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        result = self.cursor.fetchone()
        if result:
            return result[0]
        return 0
    def unwarn_user(self, group_id, user_id):
        """Remove a warning from a user in a group"""
        self.cursor.execute('''
        UPDATE group_members SET warnings = MAX(0, warnings - 1)
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        self.conn.commit()
        self.cursor.execute('''
        SELECT warnings FROM group_members
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        result = self.cursor.fetchone()
        if result:
            return result[0]
        return 0
    def mute_user(self, group_id, user_id, mute=True):
        """Mute or unmute a user in a group"""
        self.cursor.execute('''
        UPDATE group_members SET is_muted = ?
        WHERE group_id = ? AND user_id = ?
        ''', (1 if mute else 0, group_id, user_id))
        self.conn.commit()
    def ban_user(self, group_id, user_id, ban=True):
        """Ban or unban a user from a group"""
        self.cursor.execute('''
        UPDATE group_members SET is_banned = ?
        WHERE group_id = ? AND user_id = ?
        ''', (1 if ban else 0, group_id, user_id))
        self.conn.commit()
    def is_user_admin(self, user_id):
        """Check if a user is an admin"""
        self.cursor.execute('''
        SELECT is_admin FROM users WHERE user_id = ?
        ''', (user_id,))
        result = self.cursor.fetchone()
        if result:
            return bool(result[0])
        return False
    def update_stats(self, group_id, stat_type, increment=1):
        """Update group statistics"""
        import datetime
        today = datetime.date.today().isoformat()
        self.cursor.execute('''
        INSERT OR IGNORE INTO statistics (group_id, date)
        VALUES (?, ?)
        ''', (group_id, today))
        if stat_type in ["messages_count", "new_members", "removed_members", 
                         "warnings_issued", "links_blocked", "profanity_blocked", "forwards_blocked", "porn_blocked"]:
            self.cursor.execute(f'''
            UPDATE statistics SET {stat_type} = {stat_type} + ?
            WHERE group_id = ? AND date = ?
            ''', (increment, group_id, today))
            self.conn.commit()
        if stat_type == "messages_count":
            user_id = increment
            if isinstance(user_id, int):
                self.update_user_activity(group_id, user_id)
    def update_user_activity(self, group_id, user_id):
        """Update a user's activity count"""
        self.add_group_member(group_id, user_id)
        self.cursor.execute('''
        INSERT INTO user_activity (group_id, user_id, message_count, last_active)
        VALUES (?, ?, 1, CURRENT_TIMESTAMP)
        ON CONFLICT(group_id, user_id) DO UPDATE SET
        message_count = message_count + 1,
        last_active = CURRENT_TIMESTAMP
        ''', (group_id, user_id))
        self.conn.commit()
    def get_top_users(self, group_id, limit=10, admins_only=False):
        """Get the most active users in a group"""
        query = '''
        SELECT u.user_id, u.first_name, u.last_name, u.username, u.is_admin, a.message_count
        FROM user_activity a
        JOIN users u ON a.user_id = u.user_id
        WHERE a.group_id = ?
        '''
        if admins_only:
            query += ' AND u.is_admin = 1'
        query += ' ORDER BY a.message_count DESC LIMIT ?'
        self.cursor.execute(query, (group_id, limit))
        results = self.cursor.fetchall()
        users = []
        for row in results:
            users.append({
                'user_id': row[0],
                'first_name': row[1],
                'last_name': row[2],
                'username': row[3],
                'is_admin': bool(row[4]),
                'message_count': row[5]
            })
        return users
    def get_group_stats(self, group_id, days=7):
        """Get group statistics for the last X days"""
        import datetime
        end_date = datetime.date.today()
        start_date = end_date - datetime.timedelta(days=days-1)
        self.cursor.execute('''
        SELECT date, messages_count, new_members, removed_members, 
               warnings_issued, links_blocked, profanity_blocked, forwards_blocked
        FROM statistics
        WHERE group_id = ? AND date BETWEEN ? AND ?
        ORDER BY date ASC
        ''', (group_id, start_date.isoformat(), end_date.isoformat()))
        results = self.cursor.fetchall()
        stats = {}
        for row in results:
            stats[row[0]] = {
                "messages_count": row[1],
                "new_members": row[2],
                "removed_members": row[3],
                "warnings_issued": row[4],
                "links_blocked": row[5],
                "profanity_blocked": row[6],
                "forwards_blocked": row[7]
            }
        return stats
    def add_custom_command(self, group_id, command, response):
        """Add or update a custom command for a group"""
        self.cursor.execute('''
        INSERT OR REPLACE INTO custom_commands (group_id, command, response)
        VALUES (?, ?, ?)
        ''', (group_id, command, response))
        self.conn.commit()
    def get_custom_command(self, group_id, command):
        """Get a custom command response"""
        self.cursor.execute('''
        SELECT response FROM custom_commands
        WHERE group_id = ? AND command = ?
        ''', (group_id, command))
        result = self.cursor.fetchone()
        if result:
            return result[0]
        return None
    def delete_custom_command(self, group_id, command):
        """Delete a custom command"""
        self.cursor.execute('''
        DELETE FROM custom_commands
        WHERE group_id = ? AND command = ?
        ''', (group_id, command))
        self.conn.commit()
    def get_all_custom_commands(self, group_id):
        """Get all custom commands for a group"""
        self.cursor.execute('''
        SELECT command, response FROM custom_commands
        WHERE group_id = ?
        ''', (group_id,))
        results = self.cursor.fetchall()
        commands = {}
        for row in results:
            commands[row[0]] = row[1]
        return commands
    def get_all_groups(self):
        """Get all group IDs from the database"""
        self.cursor.execute('SELECT group_id FROM groups')
        results = self.cursor.fetchall()
        return [row[0] for row in results]
    def close(self):
        """Close the database connection"""
        self.conn.close()
    def track_message_for_spam(self, group_id, user_id):
        """Track a message for spam detection"""
        import time
        current_time = int(time.time())
        self.cursor.execute('''
        INSERT INTO spam_detection (group_id, user_id, message_count, first_message_time, last_message_time)
        VALUES (?, ?, 1, ?, ?)
        ON CONFLICT(group_id, user_id) DO UPDATE SET
            message_count = message_count + 1,
            last_message_time = ?
        ''', (group_id, user_id, current_time, current_time, current_time))
        self.conn.commit()
        self.cursor.execute('''
        SELECT message_count, first_message_time, last_message_time
        FROM spam_detection
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        result = self.cursor.fetchone()
        if result:
            message_count = result[0]
            first_message_time = result[1]
            time_diff = current_time - first_message_time
            if time_diff > 60:
                self.cursor.execute('''
                UPDATE spam_detection
                SET message_count = 1, first_message_time = ?
                WHERE group_id = ? AND user_id = ?
                ''', (current_time, group_id, user_id))
                self.conn.commit()
                return False
            if message_count >= 5 and time_diff <= 5:
                return True
            if message_count >= 10 and time_diff <= 30:
                return True
        return False
    def track_cheating_activity(self, group_id, user_id, has_suspicious_link=False, has_external_mention=False):
        """Track potentially cheating activity"""
        import time
        current_time = int(time.time())
        self.cursor.execute('''
        INSERT INTO cheating_detection (
            group_id, user_id, suspicious_links_count, external_mentions_count, last_detected
        )
        VALUES (?, ?, ?, ?, ?)
        ON CONFLICT(group_id, user_id) DO UPDATE SET
            suspicious_links_count = suspicious_links_count + ?,
            external_mentions_count = external_mentions_count + ?,
            last_detected = ?
        ''', (
            group_id, user_id, 
            1 if has_suspicious_link else 0, 
            1 if has_external_mention else 0,
            current_time,
            1 if has_suspicious_link else 0,
            1 if has_external_mention else 0,
            current_time
        ))
        self.conn.commit()
        self.cursor.execute('''
        SELECT suspicious_links_count, external_mentions_count, last_detected
        FROM cheating_detection
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        result = self.cursor.fetchone()
        if result:
            suspicious_links_count = result[0]
            external_mentions_count = result[1]
            last_detected_time = result[2]
            time_diff = current_time - last_detected_time
            if time_diff > 3600:
                self.cursor.execute('''
                UPDATE cheating_detection
                SET suspicious_links_count = ?, external_mentions_count = ?, last_detected = ?
                WHERE group_id = ? AND user_id = ?
                ''', (
                    1 if has_suspicious_link else 0,
                    1 if has_external_mention else 0,
                    current_time,
                    group_id, user_id
                ))
                self.conn.commit()
                return False
            if suspicious_links_count >= 3 or external_mentions_count >= 5:
                return True
        return False
    def reset_spam_detection(self, group_id, user_id):
        """Reset spam detection for a user"""
        self.cursor.execute('''
        UPDATE spam_detection
        SET message_count = 0, first_message_time = CURRENT_TIMESTAMP
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        self.conn.commit()
    def reset_cheating_detection(self, group_id, user_id):
        """Reset cheating detection for a user"""
        self.cursor.execute('''
        UPDATE cheating_detection
        SET suspicious_links_count = 0, external_mentions_count = 0, last_detected = CURRENT_TIMESTAMP
        WHERE group_id = ? AND user_id = ?
        ''', (group_id, user_id))
        self.conn.commit()
    def track_user_join(self, user_id, username=None):
        """Track when a user joins a group to detect potential tabchi accounts"""
        import time
        current_time = int(time.time())
        self.cursor.execute('''
        INSERT INTO tabchi_detection (user_id, username, join_count, last_detected)
        VALUES (?, ?, 1, ?)
        ON CONFLICT(user_id) DO UPDATE SET
            join_count = join_count + 1,
            username = COALESCE(?, username),
            last_detected = ?
        ''', (user_id, username, current_time, username, current_time))
        self.conn.commit()
        self.cursor.execute('''
        SELECT join_count, last_detected
        FROM tabchi_detection
        WHERE user_id = ?
        ''', (user_id,))
        result = self.cursor.fetchone()
        if result:
            join_count = result[0]
            last_detected_time = result[1]
            time_diff = current_time - last_detected_time
            if join_count >= 3 and time_diff <= 3600:
                return True
        return False
    def check_tabchi_patterns(self, user_id, username=None, first_name=None, bio=None):
        """Check if a user matches common tabchi patterns"""
        suspicious_score = 0
        if username:
            tabchi_patterns = [
                r'tabchi', r'tab_?chi', r'add_?member', r'member_?adder', r'member_?add',
                r'add_?all', r'add_?pv', r'add_?contact', r'robot', r'bot', r'advertising',
                r'tabligh', r'tabliq', r'ads', r'join', r'member', r'invite', r'bulk',
                r'تبچی', r'تب_?چی', r'ممبر', r'ادد', r'اد_?ممبر', r'اد_?کن', r'تبلیغ',
                r'تبلیغات', r'عضو_?گیر', r'عضوگیر', r'ربات', r'بات'
            ]
            import re
            for pattern in tabchi_patterns:
                if re.search(pattern, username, re.IGNORECASE):
                    suspicious_score += 10
                    break
        if first_name:
            tabchi_name_patterns = [
                r'tabchi', r'add', r'member', r'join', r'robot', r'bot',
                r'تبچی', r'ممبر', r'ادد', r'اد', r'عضو', r'ربات', r'بات'
            ]
            import re
            for pattern in tabchi_name_patterns:
                if re.search(pattern, first_name, re.IGNORECASE):
                    suspicious_score += 5
                    break
        if bio:
            tabchi_bio_patterns = [
                r'add', r'member', r'join', r'group', r'channel', r'advertis',
                r'تبلیغات', r'تبلیغ', r'عضو', r'گروه', r'کانال', r'ادد', r'اد',
                r'ممبر', r'عضوگیری', r'فالوور', r'فالو'
            ]
            import re
            for pattern in tabchi_bio_patterns:
                if re.search(pattern, bio, re.IGNORECASE):
                    suspicious_score += 3
                    break
        self.cursor.execute('''
        UPDATE tabchi_detection
        SET suspicious_score = suspicious_score + ?
        WHERE user_id = ?
        ''', (suspicious_score, user_id))
        self.conn.commit()
        if suspicious_score >= 10:
            return True
        self.cursor.execute('''
        SELECT suspicious_score
        FROM tabchi_detection
        WHERE user_id = ?
        ''', (user_id,))
        result = self.cursor.fetchone()
        if result and result[0] >= 15:
            return True
        return False
    def is_tabchi(self, user_id, username=None, first_name=None, bio=None):
        """Determine if a user is likely a tabchi (automated spam account)"""
        self.cursor.execute('''
        SELECT suspicious_score, join_count
        FROM tabchi_detection
        WHERE user_id = ?
        ''', (user_id,))
        result = self.cursor.fetchone()
        if result:
            suspicious_score = result[0]
            join_count = result[1]
            if suspicious_score >= 15 or join_count >= 5:
                return True
        if self.check_tabchi_patterns(user_id, username, first_name, bio):
            return True
        return False
    def promote_user_to_admin(self, user_id):
        """Promote a user to admin status in the database"""
        self.cursor.execute('''
        UPDATE users SET is_admin = 1
        WHERE user_id = ?
        ''', (user_id,))
        self.conn.commit()
        return True
    def demote_admin(self, user_id):
        """Demote a user from admin status in the database"""
        if user_id == ADMIN_ID:
            return False
        self.cursor.execute('''
        UPDATE users SET is_admin = 0
        WHERE user_id = ?
        ''', (user_id,))
        self.conn.commit()
        return True
    def get_all_admins(self):
        """Get a list of all admin users"""
        self.cursor.execute('''
        SELECT user_id, username, first_name, last_name
        FROM users
        WHERE is_admin = 1
        ''')
        admins = []
        results = self.cursor.fetchall()
        for row in results:
            admins.append({
                'user_id': row[0],
                'username': row[1],
                'first_name': row[2],
                'last_name': row[3]
            })
        return admins 