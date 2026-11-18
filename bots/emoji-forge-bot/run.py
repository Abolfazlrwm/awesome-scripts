# Telegram: @Abolfazl_rwm
"""Entry point. Without a token it prints the secure-setup guide and exits 2 —
it never pretends the bot is running."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), "src"))

from emoji_forge.bot import main

if __name__ == "__main__":
    raise SystemExit(main())
