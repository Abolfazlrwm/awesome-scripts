# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Run database migrations
"""
import os
import sys
import logging
from pathlib import Path
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger('migrations')
def run_migrations():
    """Run all migration scripts in the migrations directory"""
    migrations_dir = Path(__file__).parent / 'migrations'
    if not migrations_dir.exists():
        logger.info("No migrations directory found")
        return
    migration_files = sorted([f for f in migrations_dir.glob('*.py')
                            if f.name != '__init__.py' and f.name.startswith(('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'))])
    if not migration_files:
        logger.info("No migration files found")
        return
    logger.info(f"Found {len(migration_files)} migration(s) to run")
    sys.path.insert(0, str(migrations_dir.parent))
    for migration_file in migration_files:
        try:
            module_name = f"migrations.{migration_file.stem}"
            logger.info(f"Running migration: {module_name}")
            module = __import__(module_name, fromlist=['run_migration'])
            module.run_migration()
        except Exception as e:
            logger.error(f"Error running migration {migration_file.name}: {str(e)}")
            raise
if __name__ == "__main__":
    run_migrations()
