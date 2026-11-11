from __future__ import annotations

import asyncio
import argparse
import os
from pathlib import Path

from dotenv import load_dotenv
from telethon import TelegramClient
from telethon.sessions import StringSession


load_dotenv()


def save_session_to_env(session: str, env_path: Path) -> None:
    lines = env_path.read_text(encoding="utf-8").splitlines() if env_path.exists() else []
    updated: list[str] = []
    replaced = False
    for line in lines:
        if line.startswith("SESSION_STRING="):
            updated.append(f"SESSION_STRING={session}")
            replaced = True
        else:
            updated.append(line)
    if not replaced:
        updated.append(f"SESSION_STRING={session}")
    temporary = env_path.with_suffix(".tmp")
    temporary.write_text("\n".join(updated) + "\n", encoding="utf-8")
    temporary.replace(env_path)
    try:
        env_path.chmod(0o600)
    except OSError:
        pass


async def main(write_env: bool = False) -> None:
    api_id = int(os.environ["API_ID"])
    api_hash = os.environ["API_HASH"]
    client = TelegramClient(StringSession(), api_id, api_hash)
    await client.start()
    print("\nSESSION_STRING (mahramane negah darid):\n")
    session = client.session.save()
    if write_env:
        save_session_to_env(session, Path(".env"))
        print("SESSION_STRING dakhel .env zakhire shod.")
    else:
        print(session)
    await client.disconnect()


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--write-env", action="store_true", help="Zakhire mostaghim SESSION_STRING dar .env")
    args = parser.parse_args()
    asyncio.run(main(write_env=args.write_env))
