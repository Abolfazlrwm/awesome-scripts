"""
migrate_users.py
One-time script to import users from users.json into the SQLite DB.

Usage:
    python migrate_users.py
"""
import json
from pathlib import Path
import logging
from database import init_db, add_user
from typing import List

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

USERS_JSON = Path(__file__).parent / "users.json"

def load_json_users(path: Path) -> List[dict]:
    """Load a JSON array of users from path. Each item expected to have 'id' and 'username'."""
    with open(path, "r", encoding="utf-8") as f:
        data = json.load(f)
        if not isinstance(data, list):
            raise ValueError("users.json must contain a JSON array.")
    return data

def migrate():
    """Perform migration: create DB, then insert users."""
    init_db()
    if not USERS_JSON.exists():
        logger.error("users.json not found at %s", USERS_JSON)
        return
    users = load_json_users(USERS_JSON)
    count = 0
    for u in users:
        try:
            add_user(int(u["id"]), u.get("username"))
            count += 1
        except Exception as e:
            logger.exception("Failed to add user: %s", u)
    logger.info("Migrated %d users from %s", count, USERS_JSON)

if __name__ == "__main__":
    migrate()
