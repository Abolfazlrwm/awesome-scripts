"""
config.py
Central configuration loader for the bot.
Parses environment variables and provides typed values to the rest of the app.
"""
from pathlib import Path
import os
from dotenv import load_dotenv
from typing import Set

load_dotenv()

TOKEN: str = os.getenv("TOKEN", "").strip()
ADMIN_USER_IDS_RAW: str = os.getenv("ADMIN_USER_IDS", "").strip()
DB_PATH: str = os.getenv("DB_PATH", str(Path(__file__).parent / "users.db"))

def parse_admin_ids(raw: str) -> Set[int]:
    """
    Parse a comma-separated string of admin ids to a set of ints.
    Ignores empty entries and invalid integers.
    """
    result = set()
    for part in (raw or "").split(","):
        part = part.strip()
        if not part:
            continue
        try:
            result.add(int(part))
        except ValueError:
            # ignore invalid values
            continue
    return result

ADMIN_USER_IDS = parse_admin_ids(ADMIN_USER_IDS_RAW)

# Basic validation
if not TOKEN:
    raise RuntimeError("BOT TOKEN is not set. Please set TOKEN in your .env file.")
