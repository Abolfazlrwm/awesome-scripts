from __future__ import annotations

import asyncio
import io
import json
import logging
import os
import re
import shutil
import sys
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import urlparse

from dotenv import load_dotenv
import imageio_ffmpeg
import qrcode
from telethon import Button, TelegramClient, events, utils
from telethon.errors import FloodWaitError, SessionPasswordNeededError, UserAlreadyParticipantError
from telethon.sessions import StringSession
from telethon.tl.functions.channels import JoinChannelRequest
from telethon.tl.functions.messages import CheckChatInviteRequest, ImportChatInviteRequest

from pytgcalls import PyTgCalls
from pytgcalls import filters as call_filters
from pytgcalls.exceptions import NoActiveGroupCall
from pytgcalls.types import ChatUpdate, GroupCallConfig

from media_features import MediaFeatures


load_dotenv()


def configure_local_ffmpeg() -> str:
    python_bin = Path(sys.executable).resolve().parent
    os.environ["PATH"] = f"{python_bin}{os.pathsep}{os.environ.get('PATH', '')}"
    system_ffmpeg = shutil.which("ffmpeg")
    if system_ffmpeg:
        return system_ffmpeg
    bundled_ffmpeg = Path(imageio_ffmpeg.get_ffmpeg_exe()).resolve()
    runtime_bin = Path(__file__).resolve().parent / ".runtime-bin"
    runtime_bin.mkdir(parents=True, exist_ok=True)
    local_ffmpeg = runtime_bin / "ffmpeg"
    if local_ffmpeg.is_symlink() and not local_ffmpeg.exists():
        local_ffmpeg.unlink()
    if not local_ffmpeg.exists():
        try:
            local_ffmpeg.symlink_to(bundled_ffmpeg)
        except OSError:
            shutil.copy2(bundled_ffmpeg, local_ffmpeg)
            local_ffmpeg.chmod(0o755)
    os.environ["PATH"] = f"{runtime_bin}{os.pathsep}{os.environ.get('PATH', '')}"
    os.environ["IMAGEIO_FFMPEG_EXE"] = str(local_ffmpeg)
    return str(local_ffmpeg)


FFMPEG_PATH = configure_local_ffmpeg()


def required_env(name: str) -> str:
    value = os.getenv(name, "").strip()
    if not value:
        raise RuntimeError(f"متغیر {name} داخل فایل .env تنظیم نشده است.")
    return value


def parse_ids(value: str) -> set[int]:
    if not value.strip():
        return set()
    try:
        return {int(item.strip()) for item in value.split(",") if item.strip()}
    except ValueError as exc:
        raise RuntimeError("لیست آیدی‌ها باید فقط شامل عدد و ویرگول باشد.") from exc


def env_bool(name: str, default: bool) -> bool:
    value = os.getenv(name)
    if value is None:
        return default
    return value.strip().lower() in {"1", "true", "yes", "on"}


API_ID = int(required_env("API_ID"))
API_HASH = required_env("API_HASH")
SESSION_STRING = required_env("SESSION_STRING")
BOT_TOKEN = required_env("BOT_TOKEN")
OWNER_ID = int(required_env("OWNER_ID"))
INITIAL_ADMIN_IDS = parse_ids(os.getenv("ADMIN_IDS", ""))
ALLOWED_CHAT_IDS = parse_ids(os.getenv("ALLOWED_CHAT_IDS", ""))
AUTO_JOIN = env_bool("AUTO_JOIN", True)
SCAN_INTERVAL = max(10, int(os.getenv("SCAN_INTERVAL", "20")))
DOWNLOAD_DIR = Path(os.getenv("DOWNLOAD_DIR", "downloads")).resolve()
DATA_DIR = Path(os.getenv("DATA_DIR", "data")).resolve()
STATE_FILE = DATA_DIR / "panel-state.json"
PANEL_IMAGE_FILE = DATA_DIR / "panel-image.jpg"
SUPPORT_URL = os.getenv("SUPPORT_URL", "").strip()
CHANNEL_URL = os.getenv("CHANNEL_URL", "").strip()
SUBSCRIPTION_END = os.getenv("SUBSCRIPTION_END", "").strip()

logging.basicConfig(
    level=os.getenv("LOG_LEVEL", "INFO").upper(),
    format="%(asctime)s | %(levelname)s | %(message)s",
)
log = logging.getLogger("voicecall-userbot")
log.info("FFmpeg executable: %s", FFMPEG_PATH)

# The assistant account joins calls; the BotFather bot only displays the panel.
APP_LOOP = asyncio.new_event_loop()
asyncio.set_event_loop(APP_LOOP)
assistant = TelegramClient(StringSession(SESSION_STRING), API_ID, API_HASH)
panel = TelegramClient(StringSession(), API_ID, API_HASH)
calls = PyTgCalls(assistant)

joined_chats: set[int] = set()
join_locks: dict[int, asyncio.Lock] = {}
pending_inputs: dict[int, str] = {}
qr_login_busy: set[int] = set()


def load_state() -> dict:
    try:
        data = json.loads(STATE_FILE.read_text(encoding="utf-8"))
        if not isinstance(data, dict):
            raise ValueError
    except (FileNotFoundError, json.JSONDecodeError, ValueError):
        data = {}
    data.setdefault("admins", [])
    data.setdefault("last_targets", {})
    if "monitored_chats" not in data:
        data["monitored_chats"] = sorted({int(item) for item in data["last_targets"].values()})
    data.setdefault("auto_join", True)
    return data


state = load_state()
admin_ids = {OWNER_ID, *INITIAL_ADMIN_IDS, *(int(item) for item in state["admins"])}


def save_state() -> None:
    DATA_DIR.mkdir(parents=True, exist_ok=True)
    state["admins"] = sorted(admin_ids - {OWNER_ID})
    temporary = STATE_FILE.with_suffix(".tmp")
    temporary.write_text(json.dumps(state, ensure_ascii=False, indent=2), encoding="utf-8")
    temporary.replace(STATE_FILE)


def save_session_string(session_string: str) -> None:
    env_path = Path(".env")
    lines = env_path.read_text(encoding="utf-8").splitlines() if env_path.exists() else []
    output: list[str] = []
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


def is_admin(user_id: int | None) -> bool:
    return user_id is not None and user_id in admin_ids


def get_last_target(user_id: int) -> int | None:
    value = state["last_targets"].get(str(user_id))
    return int(value) if value is not None else None


def set_last_target(user_id: int, chat_id: int) -> None:
    state["last_targets"][str(user_id)] = chat_id
    save_state()


def add_monitored_chat(chat_id: int) -> None:
    monitored = {int(item) for item in state["monitored_chats"]}
    monitored.add(chat_id)
    state["monitored_chats"] = sorted(monitored)
    save_state()


def monitored_chat_ids() -> set[int]:
    return {int(item) for item in state["monitored_chats"]} | ALLOWED_CHAT_IDS


def no_create_config() -> GroupCallConfig:
    # Critical safety rule: never create a missing group call.
    return GroupCallConfig(auto_start=False)


def chat_lock(chat_id: int) -> asyncio.Lock:
    return join_locks.setdefault(chat_id, asyncio.Lock())


async def join_voice_chat(chat_id: int, source: str | Path | None = None) -> bool:
    """Join an existing call only. Returns False if it was already connected."""
    async with chat_lock(chat_id):
        if source is None and chat_id in joined_chats:
            return False
        await calls.play(chat_id, source, config=no_create_config())
        joined_chats.add(chat_id)
        log.info("Joined active voice chat: %s", chat_id)
        return True


async def stop_audio(chat_id: int) -> None:
    async with chat_lock(chat_id):
        await calls.play(chat_id, None, config=no_create_config())
        joined_chats.add(chat_id)


async def leave_voice_chat(chat_id: int) -> None:
    async with chat_lock(chat_id):
        await calls.leave_call(chat_id)
        joined_chats.discard(chat_id)
        log.info("Left voice chat: %s", chat_id)


def parse_telegram_link(raw_value: str) -> tuple[str, str]:
    value = raw_value.strip()
    if value.startswith("@"):
        username = value[1:]
        if not re.fullmatch(r"[A-Za-z0-9_]{4,32}", username):
            raise ValueError("یوزرنیم عمومی معتبر نیست.")
        return "public", username

    if value.startswith(("t.me/", "telegram.me/", "telegram.dog/")):
        value = f"https://{value}"
    parsed = urlparse(value)
    if parsed.scheme not in {"http", "https"} or parsed.netloc.lower() not in {
        "t.me",
        "www.t.me",
        "telegram.me",
        "www.telegram.me",
        "telegram.dog",
    }:
        raise ValueError("لینک باید مربوط به t.me باشد.")

    parts = [part for part in parsed.path.split("/") if part]
    if not parts:
        raise ValueError("لینک تلگرام ناقص است.")
    if parts[0] == "joinchat" and len(parts) >= 2:
        return "private", parts[1]
    if parts[0].startswith("+") and len(parts[0]) > 1:
        return "private", parts[0][1:]

    username = parts[0]
    if username in {"c", "s"} or not re.fullmatch(r"[A-Za-z0-9_]{4,32}", username):
        raise ValueError("لینک عمومی گروه یا کانال معتبر نیست.")
    return "public", username


async def join_group_from_link(raw_link: str, expected_kind: str | None = None) -> tuple[int, str]:
    kind, token = parse_telegram_link(raw_link)
    if expected_kind is not None and kind != expected_kind:
        label = "خصوصی" if expected_kind == "private" else "عمومی"
        raise ValueError(f"از بخش اتصال {label} استفاده کرده‌اید؛ نوع لینک درست نیست.")

    if kind == "private":
        try:
            result = await assistant(ImportChatInviteRequest(token))
            entity = result.chats[0]
        except UserAlreadyParticipantError:
            checked = await assistant(CheckChatInviteRequest(token))
            entity = getattr(checked, "chat", None)
            if entity is None:
                raise ValueError("اکانت عضو است، اما گروه از روی این لینک قابل شناسایی نیست.")
    else:
        entity = await assistant.get_entity(token)
        try:
            result = await assistant(JoinChannelRequest(entity))
            if getattr(result, "chats", None):
                entity = result.chats[0]
        except UserAlreadyParticipantError:
            pass

    chat_id = int(utils.get_peer_id(entity))
    if chat_id > 0:
        raise ValueError("این لینک مربوط به گروه یا کانال نیست.")
    title = getattr(entity, "title", None) or str(chat_id)
    return chat_id, title


def main_menu_buttons() -> list[list[Button]]:
    auto_status = "روشن ✅" if state.get("auto_join") else "خاموش ❌"
    return [
        [
            Button.inline("🔶 اتصال به لینک خصوصی", b"join_private"),
            Button.inline("🔷 اتصال به لینک عمومی", b"join_public"),
        ],
        [Button.inline("🔗 اتصال چند لینک", b"join_bulk")],
        [Button.inline(f"🤖 ورود خودکار: {auto_status}", b"auto_join_toggle")],
        [Button.inline("🎵 پخش، شبکه‌ها و اشتراک گروه", b"media_menu")],
        [
            Button.inline("📊 لیست اکانت‌ها", b"account_list"),
            Button.inline("♻️ بررسی حساب", b"account_check"),
        ],
        [
            Button.inline("➕ افزودن/تعویض اکانت", b"account_setup"),
            Button.inline("🛠 مدیریت ویس‌کال", b"voice_menu"),
        ],
        [Button.inline("⌛ اعتبار اشتراک", b"subscription")],
        [Button.inline("👥 مدیریت ادمین‌ها", b"admin_menu")],
        [Button.inline("🖼 مدیریت عکس پنل", b"panel_image")],
        [
            Button.inline("🔐 پشتیبانی", b"support"),
            Button.inline("📚 کانال", b"channel"),
        ],
    ]


def back_button(target: bytes = b"main") -> list[list[Button]]:
    return [[Button.inline("🔙 بازگشت", target)]]


async def render_main(event, *, edit: bool = False) -> None:
    text = (
        "● سلام، به پنل مدیریت Voice Call خوش آمدید.\n\n"
        "● لینک عمومی یا خصوصی گروه را بفرستید.\n"
        "● اکانت عضو گروه می‌شود و فقط به Voice Chat فعال می‌پیوندد.\n"
        "● اگر تماسی فعال نباشد، ربات هرگز تماس جدید ایجاد نمی‌کند.\n\n"
        "لطفاً یکی از گزینه‌ها را انتخاب کنید:"
    )
    if PANEL_IMAGE_FILE.exists():
        # A media message cannot reliably replace every kind of callback message,
        # so callbacks get a fresh menu message.
        if edit:
            try:
                await event.delete()
            except Exception:
                pass
        await event.respond(text, file=str(PANEL_IMAGE_FILE), buttons=main_menu_buttons())
    elif edit:
        await event.edit(text, buttons=main_menu_buttons())
    else:
        await event.respond(text, buttons=main_menu_buttons())


async def require_target(event) -> int | None:
    target = get_last_target(int(event.sender_id))
    if target is None:
        await event.edit("⚠️ اول یک لینک گروه ارسال کنید.", buttons=back_button())
        return None
    return target


def subscription_text() -> str:
    if not SUBSCRIPTION_END:
        return "⌛ اعتبار اشتراک: نامحدود"
    try:
        end = datetime.fromisoformat(SUBSCRIPTION_END).replace(tzinfo=timezone.utc)
        remaining = (end - datetime.now(timezone.utc)).days
        status = "منقضی شده" if remaining < 0 else f"{remaining + 1} روز باقی‌مانده"
        return f"⌛ پایان اشتراک: {end.date().isoformat()}\nوضعیت: {status}"
    except ValueError:
        return f"⌛ اعتبار اشتراک: {SUBSCRIPTION_END}"


@panel.on(events.NewMessage(pattern=r"^/start(?:@\w+)?$"))
async def start_handler(event: events.NewMessage.Event) -> None:
    if not is_admin(event.sender_id):
        await event.respond("⛔ شما اجازه استفاده از این پنل را ندارید.")
        return
    pending_inputs.pop(int(event.sender_id), None)
    await render_main(event)


@panel.on(events.NewMessage(pattern=r"^/cancel$"))
async def cancel_message_handler(event: events.NewMessage.Event) -> None:
    if not is_admin(event.sender_id):
        return
    pending_inputs.pop(int(event.sender_id), None)
    await render_main(event)


@panel.on(events.CallbackQuery())
async def callback_handler(event: events.CallbackQuery.Event) -> None:
    if not is_admin(event.sender_id):
        await event.answer("شما دسترسی ندارید.", alert=True)
        return

    user_id = int(event.sender_id)
    action = event.data.decode("utf-8")
    if action.startswith("media_"):
        return
    await event.answer()

    if action in {"main", "cancel"}:
        pending_inputs.pop(user_id, None)
        await render_main(event, edit=True)
        return
    if action in {"join_private", "join_public", "join_bulk"}:
        pending_inputs[user_id] = action
        label = {"join_private": "خصوصی", "join_public": "عمومی", "join_bulk": "عمومی یا خصوصی"}[action]
        example = {
            "join_private": "https://t.me/+AbCdEf...",
            "join_public": "https://t.me/group_username",
            "join_bulk": "هر خط یک لینک (حداکثر ۲۰ لینک)",
        }[action]
        await event.edit(
            f"لینک {label} گروه را ارسال کنید. چند لینک را هرکدام در یک خط بفرستید.\n\nنمونه: `{example}`",
            buttons=[[Button.inline("❌ انصراف", b"cancel")]],
        )
        return
    if action == "auto_join_toggle":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        state["auto_join"] = not bool(state.get("auto_join"))
        save_state()
        status = "روشن ✅" if state["auto_join"] else "خاموش ❌"
        await event.edit(
            f"🤖 ورود خودکار: {status}\nگروه‌های ثبت‌شده: {len(monitored_chat_ids())}\n\n"
            "هر لینکی که در پنل بفرستید ثبت می‌شود. ربات فقط وقتی وارد می‌شود که Voice Chat از قبل فعال باشد.",
            buttons=[
                [Button.inline("🗑 پاک‌کردن لیست گروه‌ها", b"monitor_clear")],
                [Button.inline("🔙 بازگشت", b"main")],
            ],
        )
        return
    if action == "monitor_clear":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        state["monitored_chats"] = []
        save_state()
        await event.edit("✅ لیست گروه‌های ثبت‌شده پاک شد.", buttons=back_button())
        return
    if action == "panel_image":
        status = "تنظیم شده ✅" if PANEL_IMAGE_FILE.exists() else "تنظیم نشده"
        await event.edit(
            f"🖼 عکس پنل\n\nوضعیت: {status}",
            buttons=[
                [Button.inline("📤 افزودن/تعویض عکس", b"panel_image_set")],
                [Button.inline("🗑 حذف عکس", b"panel_image_remove")],
                [Button.inline("🔙 بازگشت", b"main")],
            ],
        )
        return
    if action == "panel_image_set":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی می‌تواند عکس پنل را تغییر دهد.", buttons=back_button())
            return
        pending_inputs[user_id] = "panel_image_set"
        await event.edit("یک عکس ارسال کنید.", buttons=[[Button.inline("❌ انصراف", b"cancel")]])
        return
    if action == "panel_image_remove":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        PANEL_IMAGE_FILE.unlink(missing_ok=True)
        await event.edit("✅ عکس پنل حذف شد.", buttons=back_button())
        return
    if action in {"account_list", "account_check"}:
        try:
            me = await assistant.get_me()
            username = f"@{me.username}" if me.username else "ندارد"
            status = "متصل و سالم ✅" if assistant.is_connected() else "قطع ❌"
            await event.edit(
                "📊 اکانت متصل\n\n"
                f"نام: {me.first_name or '-'}\n"
                f"آیدی: `{me.id}`\n"
                f"یوزرنیم: {username}\n"
                f"وضعیت: {status}\n"
                f"ویس‌های متصل: {len(joined_chats)}",
                buttons=back_button(),
            )
        except Exception:
            log.exception("Account health check failed")
            await event.edit("❌ بررسی اکانت ناموفق بود.", buttons=back_button())
        return
    if action == "account_setup":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        await event.edit(
            "➕ افزودن یا تعویض اکانت\n\n"
            "ورود امن با QR انجام می‌شود. کد ورود یا رمز دومرحله‌ای داخل ربات دریافت نمی‌شود.",
            buttons=[
                [Button.inline("📷 ورود با QR", b"account_qr")],
                [Button.inline("🔙 بازگشت", b"main")],
            ],
        )
        return
    if action == "account_qr":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        if user_id in qr_login_busy:
            await event.edit("⏳ ورود QR در حال اجراست.", buttons=back_button())
            return
        qr_login_busy.add(user_id)
        login_client = TelegramClient(StringSession(), API_ID, API_HASH)
        qr_message = None
        try:
            await event.edit("⏳ در حال ساخت QR ورود...")
            await login_client.connect()
            qr = await login_client.qr_login()
            buffer = io.BytesIO()
            buffer.name = "telegram-login-qr.png"
            qrcode.make(qr.url).save(buffer, format="PNG")
            buffer.seek(0)
            qr_message = await event.respond(
                "QR را با تلگرام اکانت موردنظر اسکن کنید:\n"
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
            await event.respond(
                "✅ اکانت با موفقیت متصل شد. برای فعال‌شدن نشست جدید، ربات را راه‌اندازی مجدد کنید.",
                buttons=[[Button.inline("🔄 راه‌اندازی مجدد", b"restart_bot")]],
            )
        except asyncio.TimeoutError:
            await event.respond("⌛ QR منقضی شد. دوباره تلاش کنید.", buttons=back_button())
        except SessionPasswordNeededError:
            await event.respond(
                "⚠️ این اکانت رمز دومرحله‌ای دارد. برای امنیت، رمز داخل چت دریافت نمی‌شود. "
                "روی سرور `venv/bin/python generate_session.py --write-env` را اجرا کنید.",
                buttons=back_button(),
            )
        except Exception as exc:
            log.exception("QR login failed")
            await event.respond(f"❌ ورود QR ناموفق بود: `{type(exc).__name__}`", buttons=back_button())
        finally:
            qr_login_busy.discard(user_id)
            await login_client.disconnect()
        return
    if action == "restart_bot":
        if user_id != OWNER_ID:
            await event.answer("دسترسی ندارید.", alert=True)
            return
        await event.edit("🔄 ربات در حال راه‌اندازی مجدد است...")
        await asyncio.sleep(1)
        os.execv(sys.executable, [sys.executable, *sys.argv])
        return
    if action == "voice_menu":
        target = get_last_target(user_id)
        target_text = f"`{target}`" if target else "هنوز انتخاب نشده"
        await event.edit(
            f"🛠 مدیریت ویس‌کال\n\nآخرین گروه: {target_text}",
            buttons=[
                [Button.inline("🎵 پخش فایل صوتی", b"play_audio")],
                [
                    Button.inline("⏸ توقف موقت", b"pause"),
                    Button.inline("▶️ ادامه پخش", b"resume"),
                ],
                [
                    Button.inline("⏹ قطع صدا", b"stop_audio"),
                    Button.inline("🚪 خروج از ویس", b"leave"),
                ],
                [Button.inline("📡 وضعیت اتصال", b"voice_status")],
                [Button.inline("🔙 بازگشت", b"main")],
            ],
        )
        return
    if action == "play_audio":
        if await require_target(event) is None:
            return
        pending_inputs[user_id] = "play_audio"
        await event.edit(
            "یک فایل صوتی ارسال کنید یا لینک مستقیم فایل صوتی را بفرستید.",
            buttons=[[Button.inline("❌ انصراف", b"cancel")]],
        )
        return
    if action in {"pause", "resume", "stop_audio", "leave", "voice_status"}:
        target = await require_target(event)
        if target is None:
            return
        try:
            if action == "pause":
                await calls.pause(target)
                message = "⏸ پخش صدا متوقف شد."
            elif action == "resume":
                await calls.resume(target)
                message = "▶️ پخش صدا ادامه پیدا کرد."
            elif action == "stop_audio":
                await stop_audio(target)
                message = "⏹ صدا قطع شد؛ اکانت داخل تماس ماند."
            elif action == "leave":
                await leave_voice_chat(target)
                message = "🚪 اکانت از Voice Chat خارج شد."
            else:
                connected = target in joined_chats
                message = f"📡 وضعیت `{target}`: " + ("متصل ✅" if connected else "متصل نیست ❌")
            await event.edit(message, buttons=back_button(b"voice_menu"))
        except NoActiveGroupCall:
            await event.edit(
                "⚠️ Voice Chat فعالی وجود ندارد؛ هیچ تماس جدیدی ساخته نشد.",
                buttons=back_button(b"voice_menu"),
            )
        except Exception as exc:
            log.exception("Voice control failed: %s", action)
            await event.edit(f"❌ عملیات ناموفق بود: `{type(exc).__name__}`", buttons=back_button(b"voice_menu"))
        return
    if action == "subscription":
        await event.edit(subscription_text(), buttons=back_button())
        return
    if action == "admin_menu":
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی می‌تواند ادمین‌ها را مدیریت کند.", buttons=back_button())
            return
        await event.edit(
            "👥 مدیریت ادمین‌ها",
            buttons=[
                [Button.inline("➕ افزودن ادمین", b"admin_add"), Button.inline("➖ حذف ادمین", b"admin_remove")],
                [Button.inline("📋 لیست ادمین‌ها", b"admin_list")],
                [Button.inline("🔙 بازگشت", b"main")],
            ],
        )
        return
    if action in {"admin_add", "admin_remove"}:
        if user_id != OWNER_ID:
            await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=back_button())
            return
        pending_inputs[user_id] = action
        verb = "افزودن" if action == "admin_add" else "حذف"
        await event.edit(
            f"آیدی عددی کاربر موردنظر برای {verb} را ارسال کنید.",
            buttons=[[Button.inline("❌ انصراف", b"cancel")]],
        )
        return
    if action == "admin_list":
        lines = [f"• `{item}`" + (" (مالک)" if item == OWNER_ID else "") for item in sorted(admin_ids)]
        await event.edit("📋 ادمین‌ها\n\n" + "\n".join(lines), buttons=back_button(b"admin_menu"))
        return
    if action in {"support", "channel"}:
        url = SUPPORT_URL if action == "support" else CHANNEL_URL
        title = "پشتیبانی" if action == "support" else "کانال"
        message = f"{title}: {url}" if url else f"لینک {title} هنوز در فایل `.env` تنظیم نشده است."
        await event.edit(message, buttons=back_button())


@panel.on(events.NewMessage(incoming=True))
async def input_handler(event: events.NewMessage.Event) -> None:
    if not event.is_private or not is_admin(event.sender_id):
        return
    user_id = int(event.sender_id)
    action = pending_inputs.get(user_id)
    if not action or event.raw_text.startswith("/"):
        return

    pending_inputs.pop(user_id, None)
    if action in {"join_private", "join_public", "join_bulk"}:
        links = [line.strip() for line in event.raw_text.splitlines() if line.strip()]
        if not links:
            await event.respond("⚠️ حداقل یک لینک ارسال کنید.", buttons=main_menu_buttons())
            return
        if len(links) > 20:
            await event.respond("⚠️ در هر نوبت حداکثر ۲۰ لینک پذیرفته می‌شود.", buttons=main_menu_buttons())
            return
        progress = await event.respond(f"⏳ در حال بررسی {len(links)} لینک و Voice Chatهای فعال...")
        results: list[str] = []
        for index, link in enumerate(links, start=1):
            try:
                expected_kind = None
                if action == "join_private":
                    expected_kind = "private"
                elif action == "join_public":
                    expected_kind = "public"
                chat_id, title = await join_group_from_link(link, expected_kind)
                set_last_target(user_id, chat_id)
                add_monitored_chat(chat_id)
                try:
                    connected_now = await join_voice_chat(chat_id)
                    detail = "وارد ویس فعال شد" if connected_now else "از قبل داخل ویس بود"
                    results.append(f"{index}. ✅ {title}: {detail}")
                except NoActiveGroupCall:
                    results.append(f"{index}. ⚠️ {title}: ویس فعال نبود؛ تماسی ساخته نشد")
            except FloodWaitError as exc:
                results.append(f"{index}. ⏳ محدودیت تلگرام: {exc.seconds} ثانیه")
                break
            except ValueError as exc:
                results.append(f"{index}. ❌ {exc}")
            except Exception as exc:
                log.exception("Joining from link failed")
                results.append(f"{index}. ❌ خطای {type(exc).__name__}")
            await asyncio.sleep(1)
        await progress.edit("\n".join(results), buttons=main_menu_buttons())
        return

    if action == "panel_image_set":
        if user_id != OWNER_ID:
            return
        if not event.photo:
            await event.respond("⚠️ پیام باید یک عکس داشته باشد.", buttons=main_menu_buttons())
            return
        DATA_DIR.mkdir(parents=True, exist_ok=True)
        temporary = DATA_DIR / "panel-image-new.jpg"
        await event.download_media(file=str(temporary))
        temporary.replace(PANEL_IMAGE_FILE)
        await event.respond("✅ عکس منوی اصلی ذخیره شد.", file=str(PANEL_IMAGE_FILE), buttons=main_menu_buttons())
        return

    if action == "play_audio":
        target = get_last_target(user_id)
        if target is None:
            await event.respond("⚠️ ابتدا لینک یک گروه را ارسال کنید.", buttons=main_menu_buttons())
            return
        progress = await event.respond("⏳ در حال آماده‌سازی فایل صوتی...")
        try:
            if event.media:
                DOWNLOAD_DIR.mkdir(parents=True, exist_ok=True)
                source = await event.download_media(file=str(DOWNLOAD_DIR))
                if not source:
                    raise RuntimeError("دانلود فایل انجام نشد.")
            else:
                source = event.raw_text.strip()
                if not source.startswith(("http://", "https://")):
                    raise ValueError("یک فایل صوتی یا لینک مستقیم http/https ارسال کنید.")
            await join_voice_chat(target, source)
            await progress.edit("▶️ پخش فایل صوتی شروع شد.", buttons=main_menu_buttons())
        except NoActiveGroupCall:
            await progress.edit(
                "⚠️ Voice Chat فعالی وجود ندارد؛ هیچ تماس جدیدی ساخته نشد.",
                buttons=main_menu_buttons(),
            )
        except ValueError as exc:
            await progress.edit(f"⚠️ {exc}", buttons=main_menu_buttons())
        except Exception as exc:
            log.exception("Audio playback failed")
            await progress.edit(f"❌ پخش ناموفق بود: `{type(exc).__name__}`", buttons=main_menu_buttons())
        return

    if action in {"admin_add", "admin_remove"}:
        if user_id != OWNER_ID:
            return
        try:
            target_admin = int(event.raw_text.strip())
            if action == "admin_add":
                admin_ids.add(target_admin)
                message = f"✅ کاربر `{target_admin}` ادمین شد."
            else:
                if target_admin == OWNER_ID:
                    raise ValueError("مالک اصلی قابل حذف نیست.")
                admin_ids.discard(target_admin)
                message = f"✅ کاربر `{target_admin}` از ادمین‌ها حذف شد."
            save_state()
            await event.respond(message, buttons=main_menu_buttons())
        except ValueError as exc:
            await event.respond(f"⚠️ آیدی نامعتبر است: {exc}", buttons=main_menu_buttons())


# Outgoing commands remain available directly from the assistant account.
def command_chat_id(event: events.NewMessage.Event, explicit: str | None = None) -> int:
    if explicit:
        return int(explicit)
    if event.chat_id is None or event.chat_id > 0:
        raise ValueError("این دستور را داخل گروه بفرست یا آیدی گروه را بعد از آن بنویس.")
    return int(event.chat_id)


async def show_command_result(event: events.NewMessage.Event, text: str) -> None:
    try:
        await event.edit(text)
    except Exception:
        log.exception("Could not edit command message")


@assistant.on(events.NewMessage(outgoing=True, pattern=r"^\.vcid$"))
async def chat_id_handler(event: events.NewMessage.Event) -> None:
    await show_command_result(event, f"Chat ID: `{event.chat_id}`")


@assistant.on(events.NewMessage(outgoing=True, pattern=r"^\.vcjoin(?:\s+(-?\d+))?$"))
async def join_command_handler(event: events.NewMessage.Event) -> None:
    try:
        chat_id = command_chat_id(event, event.pattern_match.group(1))
        await join_voice_chat(chat_id)
        await show_command_result(event, f"✅ وارد Voice Chat فعال `{chat_id}` شدم.")
    except NoActiveGroupCall:
        await show_command_result(event, "⚠️ Voice Chat فعالی وجود ندارد؛ تماسی ساخته نشد.")
    except Exception as exc:
        log.exception("Manual join failed")
        await show_command_result(event, f"❌ خطا: `{type(exc).__name__}`")


@assistant.on(events.NewMessage(outgoing=True, pattern=r"^\.vcleave(?:\s+(-?\d+))?$"))
async def leave_command_handler(event: events.NewMessage.Event) -> None:
    try:
        chat_id = command_chat_id(event, event.pattern_match.group(1))
        await leave_voice_chat(chat_id)
        await show_command_result(event, f"✅ از Voice Chat `{chat_id}` خارج شدم.")
    except Exception as exc:
        log.exception("Manual leave failed")
        await show_command_result(event, f"❌ خطا: `{type(exc).__name__}`")


@calls.on_update(call_filters.chat_update(ChatUpdate.Status.LEFT_CALL))
async def call_ended_handler(_: PyTgCalls, update: ChatUpdate) -> None:
    joined_chats.discard(update.chat_id)
    media_features.clear_runtime(int(update.chat_id))
    log.info("Voice chat ended/left: %s", update.chat_id)


async def auto_join_worker() -> None:
    log.info("Automatic watcher started.")
    while True:
        if not state.get("auto_join"):
            await asyncio.sleep(SCAN_INTERVAL)
            continue
        for chat_id in monitored_chat_ids():
            if chat_id in joined_chats:
                continue
            try:
                await join_voice_chat(chat_id)
            except NoActiveGroupCall:
                pass
            except Exception:
                log.exception("Auto-join failed for %s", chat_id)
            await asyncio.sleep(1)
        await asyncio.sleep(SCAN_INTERVAL)


media_features = MediaFeatures(
    assistant=assistant,
    panel=panel,
    calls=calls,
    owner_id=OWNER_ID,
    admin_ids=admin_ids,
    state=state,
    save_state=save_state,
    download_dir=DOWNLOAD_DIR,
    main_menu_buttons=main_menu_buttons,
    joined_chats=joined_chats,
)
media_features.register()


async def main() -> None:
    save_state()
    await calls.start()
    await panel.start(bot_token=BOT_TOKEN)
    me = await assistant.get_me()
    bot_me = await panel.get_me()
    log.info("Assistant started as %s (%s)", me.first_name, me.id)
    log.info("Panel started as @%s", bot_me.username)
    worker = asyncio.create_task(auto_join_worker())
    try:
        await asyncio.gather(
            assistant.run_until_disconnected(),
            panel.run_until_disconnected(),
        )
    finally:
        worker.cancel()
        await asyncio.gather(worker, return_exceptions=True)


if __name__ == "__main__":
    APP_LOOP.run_until_complete(main())
