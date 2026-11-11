from __future__ import annotations

import asyncio
import io
import os
import sys
from pathlib import Path

import qrcode
from dotenv import load_dotenv
from telethon import Button, TelegramClient, events
from telethon.errors import SessionPasswordNeededError
from telethon.sessions import StringSession


load_dotenv()
API_ID = int(os.environ["API_ID"])
API_HASH = os.environ["API_HASH"]
BOT_TOKEN = os.environ["BOT_TOKEN"]
OWNER_ID = int(os.environ["OWNER_ID"])

panel = TelegramClient(StringSession(), API_ID, API_HASH)
busy = False


def save_session_string(session_string: str) -> None:
    env_path = Path(".env")
    lines = env_path.read_text(encoding="utf-8").splitlines()
    output = []
    replaced = False
    for line in lines:
        if line.startswith("SESSION_STRING="):
            output.append(f"SESSION_STRING={session_string}")
            replaced = True
        else:
            output.append(line)
    if not replaced:
        output.append(f"SESSION_STRING={session_string}")
    temporary = env_path.with_suffix(".tmp")
    temporary.write_text("\n".join(output) + "\n", encoding="utf-8")
    temporary.replace(env_path)
    try:
        env_path.chmod(0o600)
    except OSError:
        pass


@panel.on(events.NewMessage(pattern=r"^/start(?:@\w+)?$"))
async def start_handler(event: events.NewMessage.Event) -> None:
    if event.sender_id != OWNER_ID:
        await event.respond("⛔ شما دسترسی ندارید.")
        return
    await event.respond(
        "سلام. برای متصل‌کردن اکانت تلگرام، روی دکمه زیر بزنید.\n\n"
        "کد ورود یا رمز دومرحله‌ای داخل ربات دریافت نمی‌شود.",
        buttons=[[Button.inline("📷 ورود امن با QR", b"qr_login")]],
    )


@panel.on(events.CallbackQuery(data=b"qr_login"))
async def qr_handler(event: events.CallbackQuery.Event) -> None:
    global busy
    if event.sender_id != OWNER_ID:
        await event.answer("دسترسی ندارید.", alert=True)
        return
    if busy:
        await event.answer("ورود QR در حال اجراست.", alert=True)
        return
    busy = True
    login_client = TelegramClient(StringSession(), API_ID, API_HASH)
    qr_message = None
    try:
        await event.answer()
        await event.edit("⏳ در حال ساخت QR ورود...")
        await login_client.connect()
        qr = await login_client.qr_login()
        buffer = io.BytesIO()
        buffer.name = "telegram-login-qr.png"
        qrcode.make(qr.url).save(buffer, format="PNG")
        buffer.seek(0)
        qr_message = await event.respond(
            "QR را با یک تلگرام از قبل واردشده اسکن کنید:\n"
            "Settings > Devices > Link Desktop Device\n\n"
            "QR تا دو دقیقه معتبر است. آن را برای کسی ارسال نکنید.",
            file=buffer,
        )
        await asyncio.wait_for(qr.wait(), timeout=120)
        session_string = login_client.session.save()
        save_session_string(session_string)
        os.environ["SESSION_STRING"] = session_string
        if qr_message:
            await qr_message.delete()
        await event.respond("✅ اکانت متصل شد. ربات در حال راه‌اندازی مجدد است...")
        await login_client.disconnect()
        await panel.disconnect()
        os.execv(sys.executable, [sys.executable, "launcher.py"])
    except asyncio.TimeoutError:
        await event.respond("⌛ QR منقضی شد. دوباره /start را بفرستید.")
    except SessionPasswordNeededError:
        await event.respond(
            "⚠️ این اکانت رمز دومرحله‌ای دارد. برای امنیت، رمز داخل چت دریافت نمی‌شود.\n"
            "در Terminal اجرا کنید: `venv/bin/python generate_session.py --write-env`"
        )
    except Exception as exc:
        await event.respond(f"❌ ورود QR ناموفق بود: `{type(exc).__name__}`")
    finally:
        busy = False
        if login_client.is_connected():
            await login_client.disconnect()


async def main() -> None:
    await panel.start(bot_token=BOT_TOKEN)
    me = await panel.get_me()
    print(f"Bootstrap panel started as @{me.username}")
    await panel.run_until_disconnected()


if __name__ == "__main__":
    asyncio.run(main())
