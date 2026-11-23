# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Migration script to add metadata column to user_stats table
"""
import os
import sqlite3
import logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger('migration')
def run_migration():
    """Run the database migration"""
    db_path = os.path.join('data', 'users.db')
    if not os.path.exists(db_path):
        logger.info("Database file not found, nothing to migrate")
        return
    conn = None
    try:
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("PRAGMA table_info(user_stats)")
        columns = [column[1] for column in cursor.fetchall()]
        if 'metadata' not in columns:
            logger.info("Adding metadata column to user_stats table")
            cursor.execute('''
                ALTER TABLE user_stats
                ADD COLUMN metadata TEXT
            ''')
            conn.commit()
            logger.info("Migration completed successfully")
        else:
            logger.info("Metadata column already exists, no migration needed")
    except sqlite3.Error as e:
        logger.error(f"Error during migration: {str(e)}")
        if conn:
            conn.rollback()
    finally:
        if conn:
            conn.close()
if __name__ == "__main__":
    run_migration()
