# Telegram: @Abolfazl_rwm
import asyncio
import os
import time
import re
import random
import logging
import html
import tempfile
from pathlib import Path
from datetime import datetime, timezone

try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

from telethon import TelegramClient, events, functions, Button
from telethon.sessions import StringSession
from telethon.errors import (
    FloodWaitError, MessageNotModifiedError, InviteHashExpiredError,
    InviteHashInvalidError, UserAlreadyParticipantError, UsersTooMuchError,
    SessionPasswordNeededError, PhoneCodeInvalidError, PasswordHashInvalidError,
    RPCError, SlowModeWaitError, ChatAdminRequiredError, UserNotParticipantError,
)
from telethon.tl.functions.messages import (
    ImportChatInviteRequest, ForwardMessagesRequest,
    CheckChatInviteRequest, DeleteChatUserRequest,
)
from telethon.tl.functions.channels import (
    JoinChannelRequest, EditBannedRequest, LeaveChannelRequest,
)
from telethon.tl.functions.account import (
    UpdateProfileRequest, GetAuthorizationsRequest, ResetAuthorizationRequest,
)
from telethon.tl.functions.photos import UploadProfilePhotoRequest
from telethon.tl.types import PeerUser, ChatBannedRights

# ==============================================================================
# PATH CONFIGURATION
# ==============================================================================
BASE_DIR = Path(__file__).resolve().parent

DATA_DIR     = BASE_DIR / "data"
ACCOUNTS_DIR = DATA_DIR / "accounts"
SETTINGS_DIR = DATA_DIR / "settings"
RUNTIME_DIR  = DATA_DIR / "runtime"

SESSION_DIR = BASE_DIR / "sessions"
DB_DIR      = BASE_DIR / "databases"
LOG_DIR     = BASE_DIR / "logs"
TEMP_DIR    = BASE_DIR / "temp"

for folder in [DATA_DIR, ACCOUNTS_DIR, SETTINGS_DIR, RUNTIME_DIR,
               SESSION_DIR, DB_DIR, LOG_DIR, TEMP_DIR]:
    folder.mkdir(parents=True, exist_ok=True)

ACCOUNTS_DATA_FILE = ACCOUNTS_DIR / "accounts.json"
SETTINGS_FILE      = SETTINGS_DIR / "settings.json"
PING_FILE          = RUNTIME_DIR  / "ping_status.txt"
LOG_FILE           = LOG_DIR      / "network_debug.log"
CONTROL_STATE_FILE = RUNTIME_DIR  / "control_mode.txt"

# ==============================================================================
# SYSTEM OPTIMIZERS
# ==============================================================================
try:
    import uvloop
    asyncio.set_event_loop_policy(uvloop.EventLoopPolicy())
    print("UVLoop initialized successfully.")
except ImportError:
    print("UVLoop not found. Using standard asyncio loop.")

try:
    import orjson
    HAS_ORJSON = True
except ImportError:
    import json
    HAS_ORJSON = False

try:
    import aiofiles
    HAS_AIOFILES = True
except ImportError:
    HAS_AIOFILES = False

logging.basicConfig(
    level=logging.WARNING,
    format="%(asctime)s - %(levelname)s - %(message)s",
    filename=str(LOG_FILE),
)

# ==============================================================================
# SECRETS (فقط از env، بدون هاردکد)
# ==============================================================================
def _env_int(name, default=0):
    try:
        return int(os.getenv(name, str(default)))
    except (TypeError, ValueError):
        return default

SUPER_ADMIN_ID = _env_int("SUPER_ADMIN_ID", 0)

# ✅ FIX: هاردکد حذف شد. فقط از env یا SUPER_ADMIN_ID
_extra_owners_raw = os.getenv("MAIN_OWNERS", "")
MAIN_OWNERS = {SUPER_ADMIN_ID} if SUPER_ADMIN_ID else set()
for _tok in re.split(r"[,;\s]+", _extra_owners_raw):
    if _tok.strip().lstrip("-").isdigit():
        MAIN_OWNERS.add(int(_tok.strip()))

api_id   = _env_int("API_ID", 0)
api_hash = os.getenv("API_HASH", "").strip()
BOT_TOKEN = os.getenv("BOT_TOKEN", "").strip()

if not api_id or not api_hash:
    raise SystemExit("❌ API_ID / API_HASH در .env تنظیم نشده‌اند.")

# ==============================================================================
# GLOBALS
# ==============================================================================
multi_db           = {}
active_tasks       = {}
running_clients    = {}
running_by_id      = {}
login_states       = {}
accounts_data      = {}
me_main_id         = None
bot_client         = None
main_client_global = None

db_locks          = {}
global_file_lock  = asyncio.Lock()
global_control_mode = False


def is_main_owner(user_id):
    if not user_id:
        return False
    try:
        return int(user_id) in MAIN_OWNERS
    except (TypeError, ValueError):
        return False


def load_control_mode():
    global global_control_mode
    try:
        if CONTROL_STATE_FILE.exists():
            global_control_mode = CONTROL_STATE_FILE.read_text(encoding="utf-8").strip() == "1"
    except Exception:
        global_control_mode = False


def save_control_mode():
    try:
        CONTROL_STATE_FILE.write_text("1" if global_control_mode else "0", encoding="utf-8")
    except Exception:
        pass


load_control_mode()

# ==============================================================================
# MIGRATION
# ==============================================================================
def run_migrations():
    old_acc_file = BASE_DIR / "accounts_data.json"
    if old_acc_file.exists():
        try:
            if not ACCOUNTS_DATA_FILE.exists():
                old_acc_file.rename(ACCOUNTS_DATA_FILE)
            else:
                old_acc_file.unlink()
        except Exception:
            pass

    old_set_file = BASE_DIR / "settings.json"
    if old_set_file.exists():
        try:
            if not SETTINGS_FILE.exists():
                old_set_file.rename(SETTINGS_FILE)
            else:
                old_set_file.unlink()
        except Exception:
            pass

    for item in BASE_DIR.iterdir():
        if item.is_file() and item.suffix == ".session":
            dest = SESSION_DIR / item.name
            if not dest.exists():
                try:
                    item.rename(dest)
                except Exception:
                    pass


run_migrations()

# ==============================================================================
# UTILS
# ==============================================================================
def escape_html(val) -> str:
    if val is None:
        return ""
    return html.escape(str(val))


def clean_phone(phone_text):
    cleaned = re.sub(r"[^\d+]", "", str(phone_text))
    if cleaned.startswith("00"):
        cleaned = "+" + cleaned[2:]
    if not cleaned.startswith("+"):
        if cleaned.startswith("0"):
            cleaned = cleaned[1:]
        cleaned = "+" + cleaned
    return cleaned


async def anti_shutdown_loop():
    while True:
        try:
            now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            content = f"Status: ACTIVE\nLast Ping: {now}"
            if HAS_AIOFILES:
                async with aiofiles.open(PING_FILE, "w", encoding="utf-8") as f:
                    await f.write(content)
            else:
                with open(PING_FILE, "w", encoding="utf-8") as f:
                    f.write(content)
        except Exception:
            pass
        await asyncio.sleep(3)


async def leave_target_chat(client, target):
    chat_entity = None
    try:
        target_str = str(target).strip()
        if "joinchat/" in target_str or "+" in target_str:
            invite_hash = (
                target_str.split("+")[-1]
                if "+" in target_str
                else target_str.split("joinchat/")[-1]
            )
            invite_hash = invite_hash.split("/")[0].split("?")[0].split("#")[0]
            check_res = await client(CheckChatInviteRequest(invite_hash))
            if hasattr(check_res, "chat"):
                chat_entity = check_res.chat
                await client(LeaveChannelRequest(chat_entity))
                return True
            return False

        if isinstance(target, int):
            chat_entity = await client.get_entity(target)
        elif target_str.startswith("http") or target_str.startswith("@") or "t.me/" in target_str:
            clean_target = target_str.split("t.me/")[-1].replace("@", "").split("/")[0]
            chat_entity = await client.get_entity(clean_target)
        else:
            chat_entity = await client.get_entity(target)

        await client(LeaveChannelRequest(chat_entity))
        return True
    except Exception:
        try:
            if isinstance(target, int):
                await client(DeleteChatUserRequest(target, "me"))
                return True
            if chat_entity is not None:
                await client(DeleteChatUserRequest(chat_entity.id, "me"))
                return True
        except Exception:
            pass
    return False

# ==============================================================================
# SYNC STATE
# ==============================================================================
class SyncState:
    def __init__(self, chat_id, leader_id=None, host_bot_id=None, workers=None, worker_ids=None):
        self.chat_id      = chat_id
        self.leader_id    = leader_id
        self.host_bot_id  = host_bot_id
        self.workers      = workers or []
        self.worker_ids   = worker_ids or []
        self.state        = "IDLE"
        self.current_operation = None
        self.sync_tasks   = []
        self.lock         = asyncio.Lock()
        self.stop_event   = asyncio.Event()
        self.turn_index   = 0


sync_states = {}


def get_or_create_sync_state(chat_id, leader_id=None, host_bot_id=None, workers=None, worker_ids=None):
    if chat_id not in sync_states:
        sync_states[chat_id] = SyncState(chat_id, leader_id, host_bot_id, workers, worker_ids)
    else:
        s = sync_states[chat_id]
        if leader_id   is not None: s.leader_id   = leader_id
        if host_bot_id is not None: s.host_bot_id = host_bot_id
        if workers     is not None: s.workers     = workers
        if worker_ids  is not None: s.worker_ids  = worker_ids
    return sync_states[chat_id]


def is_sync_authorized_and_host(chat_id, sender_id, my_id):
    if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
        s = sync_states[chat_id]
        auth = (sender_id == s.leader_id or is_main_owner(sender_id) or sender_id == me_main_id)
        return True, auth, (my_id == s.host_bot_id), (my_id in s.worker_ids)
    return False, False, False, False

# ==============================================================================
# DATABASE
# ==============================================================================
def init_user_db(user_id):
    if user_id not in db_locks:
        db_locks[user_id] = asyncio.Lock()

    db_path = DB_DIR / f"db_{user_id}.json"
    if db_path.exists():
        try:
            with open(db_path, "rb") as f:
                raw = f.read()
            multi_db[user_id] = orjson.loads(raw) if HAS_ORJSON else json.loads(raw)
        except Exception:
            multi_db[user_id] = {}
    else:
        multi_db[user_id] = {}

    defaults = {
        "kill_messages": [],
        "attack_messages": [],
        "burn_messages": [],
        "targets": [],
        "muted_gap_users": [],
        "muted_pv_users": [],
        "muted_all_gaps": [],
        "bot_admins": [],
        "mention_symbol": "@",
        "fast_replies": {},
        "quick_replies": {},
        "bom_target": None,
        "spam_delay": 0.0,
        "kill_delay": 0.0,
        "attack_delay": 0.0,
        "burn_delay": 2.0,
        "anti_log": False,
        "allowed_sessions": [],
    }
    for dk in ["enemies", "enemy_texts", "snippets", "language"]:
        multi_db[user_id].pop(dk, None)
    for key, val in defaults.items():
        multi_db[user_id].setdefault(key, val)


async def save_user_db(user_id):
    if user_id not in multi_db:
        return
    lock = db_locks.setdefault(user_id, asyncio.Lock())
    async with lock:
        db_path  = DB_DIR / f"db_{user_id}.json"
        tmp_path = DB_DIR / f"db_{user_id}.json.tmp"
        try:
            if HAS_ORJSON:
                data = orjson.dumps(multi_db[user_id], option=orjson.OPT_INDENT_2)
                if HAS_AIOFILES:
                    async with aiofiles.open(tmp_path, "wb") as f:
                        await f.write(data)
                else:
                    with open(tmp_path, "wb") as f:
                        f.write(data)
            else:
                text = json.dumps(multi_db[user_id], ensure_ascii=False, indent=4)
                if HAS_AIOFILES:
                    async with aiofiles.open(tmp_path, "w", encoding="utf-8") as f:
                        await f.write(text)
                else:
                    with open(tmp_path, "w", encoding="utf-8") as f:
                        f.write(text)
            if tmp_path.exists():
                tmp_path.replace(db_path)
        except Exception as e:
            logging.error(f"Error saving DB for {user_id}: {e}")


async def load_global_data():
    global accounts_data
    if ACCOUNTS_DATA_FILE.exists():
        try:
            with open(ACCOUNTS_DATA_FILE, "rb") as f:
                raw = f.read()
            accounts_data = orjson.loads(raw) if HAS_ORJSON else json.loads(raw)
        except Exception:
            accounts_data = {}
    else:
        accounts_data = {}


async def save_global_data():
    async with global_file_lock:
        try:
            if HAS_ORJSON:
                data = orjson.dumps(accounts_data, option=orjson.OPT_INDENT_2)
                if HAS_AIOFILES:
                    async with aiofiles.open(ACCOUNTS_DATA_FILE, "wb") as f:
                        await f.write(data)
                else:
                    with open(ACCOUNTS_DATA_FILE, "wb") as f:
                        f.write(data)
            else:
                text = json.dumps(accounts_data, ensure_ascii=False, indent=4)
                if HAS_AIOFILES:
                    async with aiofiles.open(ACCOUNTS_DATA_FILE, "w", encoding="utf-8") as f:
                        await f.write(text)
                else:
                    with open(ACCOUNTS_DATA_FILE, "w", encoding="utf-8") as f:
                        f.write(text)
        except Exception as e:
            logging.error(f"Error saving global data: {e}")

# ==============================================================================
# TASK STOPPERS
# ==============================================================================
async def cancel_and_await_task(task):
    if task and not task.done():
        task.cancel()
        try:
            await task
        except asyncio.CancelledError:
            pass
        except Exception as e:
            logging.error(f"Error waiting for cancelled task: {e}")


async def cancel_task_in_chat(user_id, chat_id):
    if user_id in active_tasks and chat_id in active_tasks[user_id]:
        info = active_tasks[user_id].pop(chat_id)
        await cancel_and_await_task(info.get("task"))


async def stop_all_tasks_for_account(user_id):
    if user_id in active_tasks:
        items = list(active_tasks[user_id].items())
        active_tasks[user_id].clear()
        for _, info in items:
            await cancel_and_await_task(info.get("task"))


async def stop_sync_in_chat(chat_id):
    if chat_id in sync_states:
        s = sync_states[chat_id]
        async with s.lock:
            s.state = "STOPPING"
            s.stop_event.set()
            for t in s.sync_tasks:
                await cancel_and_await_task(t)
            s.sync_tasks.clear()
            s.current_operation = None
            s.state = "IDLE"
            s.stop_event.clear()

# ==============================================================================
# SINGLE WORKERS
# ==============================================================================
def user_is_stopped(user_id, chat_id):
    return (user_id not in active_tasks) or (chat_id not in active_tasks[user_id])


async def spam_worker(client, my_id, chat_id, text_to_spam, reply_id=None):
    try:
        while not user_is_stopped(my_id, chat_id):
            try:
                await client.send_message(chat_id, text_to_spam, reply_to=reply_id, parse_mode="html")
                delay = float(multi_db.get(my_id, {}).get("spam_delay", 0.0))
                await asyncio.sleep(delay if delay > 0 else 0.001)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds)
            except SlowModeWaitError as e:
                await asyncio.sleep(e.seconds)
            except Exception:
                await asyncio.sleep(0.05)
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)


async def kill_worker(client, my_id, chat_id, reply_id):
    try:
        idx = 0
        while not user_is_stopped(my_id, chat_id):
            k_msgs = multi_db.get(my_id, {}).get("kill_messages", [])
            if not k_msgs:
                await asyncio.sleep(0.5)
                continue
            try:
                await client.send_message(chat_id, k_msgs[idx % len(k_msgs)], reply_to=reply_id, parse_mode="html")
                idx += 1
                delay = float(multi_db.get(my_id, {}).get("kill_delay", 0.0))
                await asyncio.sleep(delay if delay > 0 else 0.001)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds)
            except SlowModeWaitError as e:
                await asyncio.sleep(e.seconds)
            except Exception:
                await asyncio.sleep(0.05)
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)


async def attack_worker(client, my_id, chat_id):
    try:
        idx = 0
        while not user_is_stopped(my_id, chat_id):
            a_msgs = multi_db.get(my_id, {}).get("attack_messages", [])
            targs  = multi_db.get(my_id, {}).get("targets", [])
            symbol = multi_db.get(my_id, {}).get("mention_symbol", "@")
            if not a_msgs or not targs:
                await asyncio.sleep(0.5)
                continue
            mentions = " ".join(
                f'<a href="tg://user?id={uid}">{escape_html(symbol)}</a>' for uid in targs
            )
            full_text = f"{a_msgs[idx % len(a_msgs)]}\n\n{mentions}"
            try:
                await client.send_message(chat_id, full_text, parse_mode="html")
                idx += 1
                delay = float(multi_db.get(my_id, {}).get("attack_delay", 0.0))
                await asyncio.sleep(delay if delay > 0 else 0.001)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds)
            except SlowModeWaitError as e:
                await asyncio.sleep(e.seconds)
            except Exception:
                await asyncio.sleep(0.05)
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)


async def bom_worker(client, my_id, chat_id, from_chat, msg_id):
    try:
        while not user_is_stopped(my_id, chat_id):
            try:
                await client(ForwardMessagesRequest(
                    from_peer=from_chat, id=[msg_id], to_peer=chat_id, with_my_score=False,
                ))
                await asyncio.sleep(0.001)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds)
            except SlowModeWaitError as e:
                await asyncio.sleep(e.seconds)
            except Exception:
                await asyncio.sleep(0.05)
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)


async def burn_worker(client, my_id, chat_id, target_user_id):
    try:
        idx = 0
        while not user_is_stopped(my_id, chat_id):
            b_msgs = multi_db.get(my_id, {}).get("burn_messages", [])
            if not b_msgs:
                await asyncio.sleep(1)
                continue
            latest_msg = None
            try:
                async for m in client.iter_messages(chat_id, limit=30):
                    if m.sender_id and int(m.sender_id) == int(target_user_id):
                        latest_msg = m
                        break
            except Exception:
                pass
            text_to_send = b_msgs[idx % len(b_msgs)]
            reply_target = latest_msg.id if latest_msg else None
            try:
                await client.send_message(chat_id, text_to_send, reply_to=reply_target, parse_mode="html")
                idx += 1
                delay = float(multi_db.get(my_id, {}).get("burn_delay", 2.0))
                await asyncio.sleep(delay if delay > 0 else 0.1)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds)
            except SlowModeWaitError as e:
                await asyncio.sleep(e.seconds)
            except Exception:
                await asyncio.sleep(0.1)
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)


async def ban_all_worker(client, my_id, chat_id):
    try:
        async for user in client.iter_participants(chat_id):
            if user_is_stopped(my_id, chat_id):
                break
            # ✅ FIX: گارد None برای me_main_id
            if user.id == my_id:
                continue
            if me_main_id is not None and user.id == me_main_id:
                continue
            try:
                rights = ChatBannedRights(until_date=None, view_messages=True)
                await client(EditBannedRequest(chat_id, user.id, rights))
                await asyncio.sleep(0.5)
            except FloodWaitError as e:
                await asyncio.sleep(e.seconds + 1)
            except Exception:
                pass
    except asyncio.CancelledError:
        pass
    finally:
        active_tasks.get(my_id, {}).pop(chat_id, None)

# ==============================================================================
# SYNC WORKERS
# ==============================================================================
async def _sync_queue_task(sync_state, operation_type, **kwargs):
    msg_idx        = 0
    dest_chat_id   = kwargs.get("dest_chat_id", sync_state.chat_id)
    while not sync_state.stop_event.is_set():
        workers    = list(sync_state.workers)
        worker_ids = list(sync_state.worker_ids)
        total      = min(len(workers), len(worker_ids))
        if total == 0:
            await asyncio.sleep(0.5)
            continue

        current_index = sync_state.turn_index % total
        clt = workers[current_index]
        uid = worker_ids[current_index]
        sync_state.turn_index = (current_index + 1) % total

        try:
            if not clt.is_connected():
                await asyncio.sleep(0.1)
                continue

            leader_db = multi_db.get(sync_state.leader_id, {})
            key = f"{operation_type}_delay"
            if key in leader_db:
                delay = float(leader_db[key])
            else:
                host_db = multi_db.get(sync_state.host_bot_id, {})
                delay = float(host_db.get(key, 0.0))

            if operation_type == "spam":
                await clt.send_message(
                    dest_chat_id, kwargs["text"],
                    reply_to=kwargs.get("reply_id"), parse_mode="html",
                )
            elif operation_type == "kill":
                k_msgs = multi_db.get(uid, {}).get("kill_messages", [])
                if k_msgs:
                    await clt.send_message(
                        dest_chat_id, k_msgs[msg_idx % len(k_msgs)],
                        reply_to=kwargs.get("reply_id"), parse_mode="html",
                    )
            elif operation_type == "attack":
                a_msgs = multi_db.get(uid, {}).get("attack_messages", [])
                targs  = multi_db.get(uid, {}).get("targets", [])
                symbol = multi_db.get(uid, {}).get("mention_symbol", "@")
                if a_msgs and targs:
                    mentions = " ".join(
                        f'<a href="tg://user?id={tid}">{escape_html(symbol)}</a>'
                        for tid in targs
                    )
                    await clt.send_message(
                        dest_chat_id,
                        f"{a_msgs[msg_idx % len(a_msgs)]}\n\n{mentions}",
                        parse_mode="html",
                    )
            elif operation_type == "burn":
                target_user_id = kwargs.get("target_user_id")
                b_msgs = multi_db.get(uid, {}).get("burn_messages", [])
                if b_msgs and target_user_id:
                    latest_msg = None
                    try:
                        async for m in clt.iter_messages(dest_chat_id, limit=30):
                            if m.sender_id and int(m.sender_id) == int(target_user_id):
                                latest_msg = m
                                break
                    except Exception:
                        pass
                    text_to_send = b_msgs[msg_idx % len(b_msgs)]
                    reply_target = latest_msg.id if latest_msg else None
                    await clt.send_message(
                        dest_chat_id, text_to_send,
                        reply_to=reply_target, parse_mode="html",
                    )

            msg_idx += 1
            await asyncio.sleep(delay if delay > 0 else 0.001)

        except FloodWaitError as e:
            await asyncio.sleep(e.seconds)
        except SlowModeWaitError as e:
            await asyncio.sleep(e.seconds)
        except asyncio.CancelledError:
            raise
        except Exception as e:
            logging.warning("Sync worker %s failed on turn %s: %s", uid, current_index, e)
            await asyncio.sleep(0.05)


def launch_sync_tasks(sync_state, operation_type, **kwargs):
    for task in list(sync_state.sync_tasks):
        if task and not task.done():
            task.cancel()
    sync_state.sync_tasks.clear()
    sync_state.turn_index = 0
    sync_state.current_operation = operation_type
    sync_state.sync_tasks.append(
        asyncio.create_task(_sync_queue_task(sync_state, operation_type, **kwargs))
    )

# ==============================================================================
# UI COMPONENTS
# ==============================================================================
def make_home_text(my_id=None):
    return (
        "<b>▸ Creator:</b> @Abolfazl_rwm\n\n"
        "<b>𝖢𝗈𝗇𝗍𝗋𝗈𝗅 𝖯𝖺𝗇𝖾𝗅</b>\n\n"
        "<b>𝖲𝗍𝖺𝗍𝗎𝗌:</b> Active\n"
        "<b>𝖲𝖾𝗅𝖾𝖼𝗍 𝖺𝗇 𝗈𝗉𝗍𝗂𝗈𝗇 𝖻𝖾𝗅𝗈𝗐 ↓</b>"
    )


def make_home_markup(my_id):
    return [
        [Button.inline("▸ 𝖢𝗈𝗇𝗍𝗋𝗈𝗅 𝖯𝖺𝗇𝖾𝗅", data=f"panel_stats_{my_id}")],
        [Button.inline("𝖦𝗎𝗂𝖽𝖾", data=f"panel_guide_{my_id}"), Button.inline("𝖲𝗉𝖾𝖾𝖽", data=f"panel_speed_{my_id}")],
        [Button.inline("𝖠𝖽𝗆𝗂𝗇 𝖫𝗂𝗌𝗍", data=f"panel_admins_{my_id}"), Button.inline("𝖫𝗂𝗌𝗍", data=f"panel_lists_{my_id}")],
    ]


def make_text_panel(my_id):
    db = multi_db.get(my_id, {})
    return (
        "<b>Self-Bot Control Panel</b>\n\n"
        "<b>Statistics:</b>\n"
        f"> Targets: <code>{len(db.get('targets', []))}</code>\n"
        f"> Kill Words: <code>{len(db.get('kill_messages', []))}</code>\n"
        f"> Attack Words: <code>{len(db.get('attack_messages', []))}</code>\n"
        f"> Burn Words: <code>{len(db.get('burn_messages', []))}</code>\n"
        f"> Muted Groups: <code>{len(db.get('muted_all_gaps', [])) + len(db.get('muted_gap_users', []))}</code>\n"
        f"> Muted PVs: <code>{len(db.get('muted_pv_users', []))}</code>\n"
        f"> Admins: <code>{len(db.get('bot_admins', []))}</code>\n"
        f"> Active Tasks: <code>{len(active_tasks.get(my_id, {}))}</code>\n"
        f"> Attack Symbol: <code>{escape_html(db.get('mention_symbol', '@'))}</code>\n"
        f"> Spam Delay: <code>{db.get('spam_delay', 0.0)}s</code>\n"
        f"> Kill Delay: <code>{db.get('kill_delay', 0.0)}s</code>\n"
        f"> Attack Delay: <code>{db.get('attack_delay', 0.0)}s</code>\n"
        f"> Burn Delay: <code>{db.get('burn_delay', 2.0)}s</code>\n"
    )


def make_panel_markup(my_id):
    return [[
        Button.inline("𝖱𝖾𝖿𝗋𝖾𝗌𝗁", data=f"panel_stats_{my_id}"),
        Button.inline("𝖡𝖺𝖼𝗄",    data=f"panel_home_{my_id}"),
    ]]


def make_lists_markup(my_id):
    return [
        [Button.inline("𝖳𝖺𝗋𝗀𝖾𝗍𝗌", data=f"panel_targets_{my_id}"),    Button.inline("𝖪𝗂𝗅𝗅 𝖫𝗂𝗌𝗍", data=f"panel_kills_{my_id}")],
        [Button.inline("𝖠𝗍𝗍𝖺𝖼𝗄 𝖫𝗂𝗌𝗍", data=f"panel_attacks_{my_id}"), Button.inline("𝖡𝗎𝗋𝗇 𝖫𝗂𝗌𝗍", data=f"panel_burns_{my_id}")],
        [Button.inline("𝖥𝖺𝗌𝗍 𝖱𝖾𝗉𝗅𝗂𝖾𝗌", data=f"panel_fastreplies_{my_id}"), Button.inline("𝖰𝗎𝗂𝖼𝗄 𝖱𝖾𝗉𝗅𝗂𝖾𝗌", data=f"panel_quickreplies_{my_id}")],
        [Button.inline("𝖬𝗎𝗍𝖾𝖽 𝖦𝗋𝗈𝗎𝗉𝗌", data=f"panel_mutedgroups_{my_id}"), Button.inline("𝖬𝗎𝗍𝖾𝖽 𝖯𝖵𝗌", data=f"panel_mutedpvs_{my_id}")],
        [Button.inline("𝖠𝖼𝖼𝗈𝗎𝗇𝗍𝗌", data=f"panel_accounts_{my_id}"), Button.inline("𝖠𝖽𝗆𝗂𝗇 𝖫𝗂𝗌𝗍", data=f"panel_admins_{my_id}")],
        [Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{my_id}")],
    ]


def make_guide_text():
    return (
        "<b>Self-Bot Guide Manual</b>\n\n"
        "<b>General Commands:</b>\n"
        "<code>panel</code> -> Control panel\n"
        "<code>ping</code> -> Check bot latency\n"
        "<code>id</code> -> Get formatted ID\n"
        "<code>join [link/reply]</code> -> Join chat\n"
        "<code>leave [link/reply]</code> -> Leave chat\n"
        "<code>set symbol [char]</code> -> Set attack mention symbol\n"
        "<code>del symbol</code> -> Reset symbol\n"
        "<code>save</code> -> Save message to Saved Messages\n\n"
        "<b>Profile Controls (Reply to content):</b>\n"
        "<code>set profile</code> -> Set photo as profile picture\n"
        "<code>set name</code> -> Set profile name\n"
        "<code>set bio</code> -> Set profile bio\n\n"
        "<b>Fast Responses:</b>\n"
        "<code>reply [command]</code> -> Save full message (Reply)\n"
        "<code>del reply [command]</code> -> Delete saved reply\n"
        "<code>quickreply [command]</code> -> Save comma separated text\n"
        "<code>del quickreply [command]</code> -> Delete quick reply\n"
        "<code>reply list</code> -> View all replies\n"
        "<code>[command]</code> -> Trigger fast response\n\n"
        "<b>Sync Management:</b>\n"
        "<code>sync</code> / <code>sync off</code> -> Toggle sync mode\n\n"
        "<b>Attack & Execution:</b>\n"
        "<code>save bom</code> -> Save bom target (Reply)\n"
        "<code>bom</code> -> Fast forward target msg\n"
        "<code>spam [text]</code> -> Sequential spamming\n"
        "<code>kill</code> -> Burst kill on target (Reply)\n"
        "<code>attack</code> -> Mention targets with attack texts\n"
        "<code>burn</code> -> Fast burn target user (Reply)\n"
        "<code>purge [count]</code> -> Delete specific amount of messages (1..500)\n"
        "<code>stop</code> / <code>stop all</code> -> Stop operations\n\n"
        "<b>Delay Settings (Seconds):</b>\n"
        "<code>set spam [s]</code> -> Set spam delay\n"
        "<code>set kill [s]</code> -> Set kill delay\n"
        "<code>set attack [s]</code> -> Set attack delay\n"
        "<code>set burn [s]</code> -> Set burn delay\n\n"
        "<b>Database Management:</b>\n"
        "<code>add target</code> | <code>del target [reply/id/index]</code> | <code>target list</code> | <code>clean targets</code>\n"
        "<code>add kill [text/reply]</code> | <code>del kill [index]</code> | <code>kill list</code>\n"
        "<code>add attack [text/reply]</code> | <code>del attack [index]</code> | <code>attack list</code>\n"
        "<code>add burn [text/reply]</code> | <code>del burn [index]</code> | <code>burn list</code>\n\n"
        "<b>Mute Systems (Owner Only):</b>\n"
        "<code>mute all</code> | <code>unmute all</code> -> Group chat lock\n"
        "<code>mute</code> | <code>unmute [reply/id]</code> -> Mute in Group/PV\n"
        "<code>mute pv</code> | <code>unmute pv</code> -> Force PV mute"
    )


def make_list_text(my_id, kind):
    db = multi_db.get(my_id, {})
    if kind == "targets":
        title, values = "Target List", db.get("targets", [])
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "kills":
        title, values = "Kill List", db.get("kill_messages", [])
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "attacks":
        title, values = "Attack List", db.get("attack_messages", [])
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "burns":
        title, values = "Burn List", db.get("burn_messages", [])
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "fastreplies":
        values = db.get("fast_replies", {})
        title  = "Fast Replies"
        rows = [f"{i}. <code>{escape_html(str(k))}</code> - {escape_html(str(v.get('type', 'message')))}" for i, (k, v) in enumerate(values.items(), 1)]
    elif kind == "quickreplies":
        values = db.get("quick_replies", {})
        title  = "Quick Replies"
        rows = [f"{i}. <code>{escape_html(str(k))}</code> - {escape_html(', '.join(map(str, v)))}" for i, (k, v) in enumerate(values.items(), 1)]
    elif kind == "mutedgroups":
        values = list(db.get("muted_all_gaps", [])) + list(db.get("muted_gap_users", []))
        title  = "Muted Groups / Users"
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "mutedpvs":
        values = db.get("muted_pv_users", [])
        title  = "Muted PVs"
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    elif kind == "admins":
        values = db.get("bot_admins", [])
        title  = "Admin List"
        rows = [f"{i}. <code>{escape_html(str(v))}</code>" for i, v in enumerate(values, 1)]
    else:
        return make_admin_text(my_id)
    body = "\n".join(rows) if rows else "No entries yet."
    return f"<b>{title}</b>\n\n{body}"


def make_list_page(my_id, kind, page=0, max_chars=3000):
    full_text = make_list_text(my_id, kind)
    lines = full_text.splitlines()
    title = lines[0] if lines else "<b>List</b>"
    entries = [line for line in lines[2:] if line.strip()] or ["No entries yet."]

    safe_entries = []
    for line in entries:
        if len(line) > max_chars - 100:
            line = line[:max_chars - 104] + "…"
        safe_entries.append(line)

    pages, current, current_len = [], [], len(title) + 2
    for line in safe_entries:
        extra = len(line) + 1
        if current and current_len + extra > max_chars:
            pages.append(current)
            current, current_len = [], len(title) + 2
        current.append(line)
        current_len += extra
    if current or not pages:
        pages.append(current)

    page = max(0, min(int(page), len(pages) - 1))
    text = title + "\n\n" + "\n".join(pages[page])
    text += f"\n\nPage <code>{page + 1}/{len(pages)}</code>"
    return text, page, len(pages)


def make_list_page_markup(my_id, kind, page, total_pages):
    prev_page = max(0, page - 1)
    next_page = min(max(0, total_pages - 1), page + 1)
    return [[
        Button.inline("‹ 𝖯𝗋𝖾𝗏", data=f"panel_{kind}p{prev_page}_{my_id}"),
        Button.inline("𝖡𝖺𝖼𝗄",     data=f"panel_lists_{my_id}"),
        Button.inline("𝖭𝖾𝗑𝗍 ›",   data=f"panel_{kind}p{next_page}_{my_id}"),
    ]]


def make_admin_text(my_id):
    admins = multi_db.get(my_id, {}).get("bot_admins", [])
    rows = [f"{i}. <code>{int(uid)}</code>" for i, uid in enumerate(admins, 1)]
    body = "\n".join(rows) if rows else "No admins added."
    return "<b>Admin List</b>\n\n" + body


async def make_accounts_text(target_uid):
    target_uid = int(target_uid)
    matches = []
    for account_id, account_db in multi_db.items():
        try:
            account_id_int = int(account_id)
        except (TypeError, ValueError):
            continue
        if account_id_int == target_uid:
            continue
        admins = account_db.get("bot_admins", []) if isinstance(account_db, dict) else []
        if target_uid not in {int(x) for x in admins if str(x).lstrip("-").isdigit()}:
            continue

        display_name, is_online = "", False
        clt = running_by_id.get(account_id_int) or running_by_id.get(str(account_id_int))
        if clt and clt.is_connected():
            try:
                me = await clt.get_me()
                display_name = " ".join(filter(None, [
                    getattr(me, "first_name", None),
                    getattr(me, "last_name", None),
                ]))
                if not display_name:
                    display_name = getattr(me, "username", None) or ""
            except Exception:
                pass
        matches.append((account_id_int, display_name, bool(clt and clt.is_connected())))

    matches.sort(key=lambda x: (x[1].casefold(), x[0]))
    lines = ["<b>Accounts</b>", f"<b>Total:</b> <code>{len(matches)}</code>", ""]
    if not matches:
        lines.append("No self accounts have added this account as admin.")
    else:
        for i, (aid, name, online) in enumerate(matches, 1):
            shown_name = escape_html(name) if name else "Unknown"
            status = "Online" if online else "Offline"
            lines.append(f"{i}. <b>{shown_name}</b> · <code>{aid}</code> · {status}")
    return "\n".join(lines)


def make_speed_text(my_id):
    db = multi_db.get(my_id, {})
    return (
        "<b>Speed Settings</b>\n\n"
        "Faster reduces delay; slower increases it.\n\n"
        f"Spam Delay: <code>{float(db.get('spam_delay', 0.0)):.1f}s</code>\n"
        f"Kill Delay: <code>{float(db.get('kill_delay', 0.0)):.1f}s</code>\n"
        f"Attack Delay: <code>{float(db.get('attack_delay', 0.0)):.1f}s</code>\n"
        f"Burn Delay: <code>{float(db.get('burn_delay', 2.0)):.1f}s</code>"
    )


def make_speed_markup(my_id):
    return [
        [Button.inline("𝖲𝗉𝖺𝗆 -", data=f"panel_speeddownspam_{my_id}"),   Button.inline("𝖲𝗉𝖺𝗆 +", data=f"panel_speedupspam_{my_id}")],
        [Button.inline("𝖪𝗂𝗅𝗅 -", data=f"panel_speeddownkill_{my_id}"),   Button.inline("𝖪𝗂𝗅𝗅 +", data=f"panel_speedupkill_{my_id}")],
        [Button.inline("𝖠𝗍𝗍𝖺𝖼𝗄 -", data=f"panel_speeddownattack_{my_id}"), Button.inline("𝖠𝗍𝗍𝖺𝖼𝗄 +", data=f"panel_speedupattack_{my_id}")],
        [Button.inline("𝖡𝗎𝗋𝗇 -", data=f"panel_speeddownburn_{my_id}"),   Button.inline("𝖡𝗎𝗋𝗇 +", data=f"panel_speedupburn_{my_id}")],
        [Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{my_id}")],
    ]

# ==============================================================================
# MESSAGE LINK HELPERS
# ==============================================================================
def parse_telegram_message_link(value):
    value = (value or "").strip().rstrip(".,);]>")
    match = re.search(
        r"(?:https?://)?(?:www\.)?t\.me/(?:s/)?([^/?#]+)/([0-9]+)",
        value, re.IGNORECASE,
    )
    if not match:
        return None, None
    chat_ref, message_id = match.group(1), int(match.group(2))
    if chat_ref == "c":
        m2 = re.search(r"(?:https?://)?(?:www\.)?t\.me/c/([0-9]+)/([0-9]+)", value, re.IGNORECASE)
        if not m2:
            return None, None
        return ("internal_channel_id", int(m2.group(1))), int(m2.group(2))
    if chat_ref.startswith("+") or chat_ref == "joinchat":
        return None, None
    return chat_ref, message_id


async def resolve_message_link(client, link):
    chat_ref, message_id = parse_telegram_message_link(link)
    if chat_ref is None or message_id is None:
        raise ValueError("Invalid message link.")
    if isinstance(chat_ref, tuple) and chat_ref[0] == "internal_channel_id":
        internal_id = chat_ref[1]
        entity = None
        async for dialog in client.iter_dialogs():
            candidate = dialog.entity
            if getattr(candidate, "id", None) == internal_id:
                entity = candidate
                break
        if entity is None:
            raise ValueError("This account cannot access that private chat.")
    else:
        entity = await client.get_entity(chat_ref)
    source_message = await client.get_messages(entity, ids=message_id)
    if not source_message:
        raise ValueError("Message not found.")
    return entity, source_message


async def save_message_to_saved_messages(client, source_chat, source_message):
    try:
        return await client.forward_messages("me", source_message, from_peer=source_chat)
    except Exception:
        media = None
        try:
            if source_message.media:
                media = await client.download_media(source_message, file=str(TEMP_DIR))
            return await client.send_message(
                "me",
                message=source_message.text or "",
                file=media,
                formatting_entities=source_message.entities,
                parse_mode="html",
            )
        finally:
            # ✅ FIX: پاک‌سازی فایل temp در همه حالات
            if media and os.path.exists(media):
                try:
                    os.remove(media)
                except Exception:
                    pass


async def parse_command_with_link(client, text_content, default_chat_id):
    words = text_content.split()
    target_chat_id = default_chat_id
    message_id = None
    clean_words = []
    for w in words:
        if "t.me/" in w:
            try:
                chat_entity, msg_obj = await resolve_message_link(client, w)
                if chat_entity:
                    target_chat_id = chat_entity.id if hasattr(chat_entity, "id") else default_chat_id
                    if msg_obj:
                        message_id = msg_obj.id
                    continue
            except Exception:
                # ✅ FIX: لینک دعوت یا لینک نامعتبر را در clean_words نگه دار
                clean_words.append(w)
                continue
        elif w.lstrip("-").isdigit() and len(w) > 4:
            try:
                target_chat_id = int(w)
                continue
            except Exception:
                pass
        clean_words.append(w)
    return target_chat_id, message_id, " ".join(clean_words)

# ==============================================================================
# BOT HANDLERS
# ==============================================================================
def register_bot_handlers(b_client):
    @b_client.on(events.NewMessage(pattern="/start", func=lambda e: e.is_private))
    async def bot_start_handler(event):
        sender_id = event.sender_id
        if not is_main_owner(sender_id):
            await event.respond("Access Denied.")
            return
        txt = (
            "<b>Owner Control Panel</b>\n\n"
            "Welcome to the Owner Management System.\n"
            "Choose an option below or type commands:"
        )
        buttons = [[
            Button.inline("Account List", data="owner_acc_list"),
            Button.inline("Owner Panel",  data="owner_panel"),
        ]]
        await event.respond(txt, buttons=buttons, parse_mode="html")

    @b_client.on(events.NewMessage(func=lambda e: e.is_private))
    async def bot_private_cmd_handler(event):
        try:
            await _bot_private_cmd_impl(event)
        except Exception as e:
            logging.error(f"bot_private_cmd_handler: {e}")
            try:
                await event.respond(f"Internal error: {e}")
            except Exception:
                pass

    async def _bot_private_cmd_impl(event):
        sender_id = event.sender_id
        if not is_main_owner(sender_id):
            return
        text       = event.raw_text.strip() if event.raw_text else ""
        lower_text = text.lower().strip()
        chat_id    = event.chat_id

        if lower_text == "account list":
            await load_global_data()
            if not accounts_data:
                return await event.respond("No connected accounts found.")
            out = "<b>Connected Accounts:</b>\n\n"
            for idx, (ph, info) in enumerate(accounts_data.items(), 1):
                st = "Active" if ph in running_clients else "Inactive"
                name = escape_html(info.get("first_name", "Unknown"))
                out += f"{idx}. <b>{name}</b>\nPhone: <code>{escape_html(ph)}</code>\nStatus: {st}\n\n"
            return await event.respond(out, parse_mode="html")

        elif lower_text.startswith("add account "):
            parts = text.split()
            if len(parts) < 3:
                return await event.respond("Format: add account +1234567890")
            phone  = clean_phone(parts[2])
            s_name = f"bot_{phone.replace('+', '')}"

            old = login_states.pop(chat_id, None)
            if old and old.get("clt"):
                try: await old["clt"].disconnect()
                except Exception: pass

            new_clt = TelegramClient(str(SESSION_DIR / s_name), api_id, api_hash)
            await new_clt.connect()
            try:
                if await new_clt.is_user_authorized():
                    try: await new_clt.log_out()
                    except Exception: pass
                    await new_clt.disconnect()
                    new_clt = TelegramClient(str(SESSION_DIR / s_name), api_id, api_hash)
                    await new_clt.connect()

                res = await new_clt.send_code_request(phone)
                sent = await event.respond("Code sent. Reply: code 12345")
                login_states[chat_id] = {
                    "step": "code", "phone": phone, "s_name": s_name,
                    "hash": res.phone_code_hash, "clt": new_clt, "msg_id": sent.id,
                }
            except Exception as e:
                try: await new_clt.disconnect()
                except Exception: pass
                login_states.pop(chat_id, None)
                await event.respond(f"Error: {e}")
            return

        elif lower_text.startswith("code ") and chat_id in login_states and login_states[chat_id]["step"] == "code":
            reply = await event.get_reply_message()
            state = login_states[chat_id]
            if not reply or reply.id != state["msg_id"]:
                return await event.respond("Please reply directly to the code request message.")
            try:
                code_val = text.split()[1].strip()
                await state["clt"].sign_in(phone=state["phone"], code=code_val, phone_code_hash=state["hash"])
                u = await state["clt"].get_me()
                accounts_data[state["phone"]] = {
                    "session_name": state["s_name"],
                    "user_id": u.id,
                    "first_name": u.first_name,
                    "string_session": state["clt"].session.save(),
                }
                await save_global_data()
                running_clients[state["phone"]] = state["clt"]
                running_by_id[u.id] = state["clt"]
                register_handlers(state["clt"], u.id)
                login_states.pop(chat_id, None)
                await event.respond(f"Account {state['phone']} connected successfully.")
            except SessionPasswordNeededError:
                state["step"] = "pass"
                sent = await event.respond("2FA Active. Reply: password [pass]")
                state["msg_id"] = sent.id
            except PhoneCodeInvalidError:
                failed = login_states.pop(chat_id, None)
                if failed:
                    try: await failed["clt"].disconnect()
                    except Exception: pass
                sp = SESSION_DIR / f"{state['s_name']}.session"
                try:
                    if sp.exists(): sp.unlink()
                except Exception: pass
                await event.respond("Invalid code. Login reset. Use add account [phone] to try again.")
            except Exception as e:
                await event.respond(f"Error: {e}")
            return

        elif lower_text.startswith("password ") and chat_id in login_states and login_states[chat_id]["step"] == "pass":
            reply = await event.get_reply_message()
            state = login_states[chat_id]
            if not reply or reply.id != state["msg_id"]:
                return await event.respond("Please reply directly to the password request message.")
            try:
                pass_val = text.split(maxsplit=1)[1].strip()
                await state["clt"].sign_in(password=pass_val)
                u = await state["clt"].get_me()
                accounts_data[state["phone"]] = {
                    "session_name": state["s_name"],
                    "user_id": u.id,
                    "first_name": u.first_name,
                    "string_session": state["clt"].session.save(),
                }
                await save_global_data()
                running_clients[state["phone"]] = state["clt"]
                running_by_id[u.id] = state["clt"]
                register_handlers(state["clt"], u.id)
                login_states.pop(chat_id, None)
                await event.respond(f"Account {state['phone']} connected successfully.")
            except PasswordHashInvalidError:
                failed = login_states.pop(chat_id, None)
                if failed:
                    try: await failed["clt"].disconnect()
                    except Exception: pass
                sp = SESSION_DIR / f"{state['s_name']}.session"
                try:
                    if sp.exists(): sp.unlink()
                except Exception: pass
                await event.respond("Invalid password. Login reset. Use add account [phone] to try again.")
            except Exception as e:
                await event.respond(f"Error: {e}")
            return

        elif lower_text.startswith("del account "):
            parts = text.split()
            if len(parts) < 3:
                return await event.respond("Format: del account +1234567890")
            target = clean_phone(parts[2])
            found = next((k for k in accounts_data if clean_phone(k) == target), None)
            if found:
                if found in running_clients:
                    clt_del = running_clients.pop(found)
                    uid_del = accounts_data[found].get("user_id")
                    running_by_id.pop(uid_del, None)
                    await stop_all_tasks_for_account(uid_del)
                    try: await clt_del.log_out()
                    except Exception: pass
                accounts_data.pop(found, None)
                await save_global_data()
                await event.respond(f"Account {found} deleted.")
            else:
                await event.respond("Account not found.")
            return

        elif lower_text == "login" or lower_text.startswith("login "):
            parts = text.split(maxsplit=1)
            t_clt = main_client_global
            if len(parts) == 2:
                req = parts[1].strip()
                if req.isdigit():
                    t_clt = running_by_id.get(int(req))
                else:
                    try: req_phone = clean_phone(req)
                    except Exception: req_phone = req
                    matched = next((p for p in running_clients if clean_phone(p) == req_phone), None)
                    t_clt = running_clients.get(matched) if matched else None
                if t_clt is None:
                    return await event.respond("Account not found or offline.")
            try:
                me_info = await t_clt.get_me()
                code_msg = "No code found."
                async for m in t_clt.iter_messages(777000, limit=5):
                    if m.text:
                        code_msg = m.text
                        break
                phone_display = getattr(me_info, "phone", None) or "N/A"
                await event.respond(
                    f"Phone: +{phone_display}\nID: {me_info.id}\nMsg:\n{code_msg}"
                )
            except Exception as e:
                await event.respond(f"Error: {e}")
            return

        elif lower_text.startswith("terminate"):
            parts = text.split()
            t_clt = main_client_global
            if len(parts) == 2 and parts[1].isdigit():
                tid = int(parts[1])
                if tid in running_by_id:
                    t_clt = running_by_id[tid]
                else:
                    return await event.respond("Account offline.")
            try:
                await t_clt(functions.account.ResetAuthorizationRequest(hash=0))
                await event.respond("Other sessions terminated.")
            except Exception as e:
                await event.respond(f"Error: {e}")
            return

    @b_client.on(events.CallbackQuery)
    async def bot_callback_handler(event):
        try:
            data = event.data.decode("utf-8") if isinstance(event.data, bytes) else str(event.data)
            if not data:
                return
            sender_id = int(event.sender_id or 0)

            if data.startswith("owner_") and not is_main_owner(sender_id):
                await event.answer("Unauthorized", alert=True)
                return

            if data.startswith("panel_"):
                parts_panel = data.split("_")
                panel_uid = int(parts_panel[-1]) if parts_panel and parts_panel[-1].isdigit() else 0
                if sender_id != panel_uid and not (global_control_mode and is_main_owner(sender_id)):
                    await event.answer("Unauthorized", alert=True)
                    return
                panel_client = running_by_id.get(panel_uid)
                if panel_client and not event.is_private:
                    try:
                        perms = await panel_client.get_permissions(event.chat_id, "me")
                        if not perms or not (
                            getattr(perms, "is_admin", False) or getattr(perms, "is_creator", False)
                        ):
                            await event.answer("This account must be an administrator in this chat.", alert=True)
                            return
                    except Exception:
                        await event.answer("Administrator status could not be verified.", alert=True)
                        return
                elif not panel_client and not is_main_owner(sender_id):
                    await event.answer("Account offline.", alert=True)
                    return

            if data == "owner_acc_list":
                if not accounts_data:
                    await event.edit("Account List:\n\nNo accounts registered.", buttons=[[Button.inline("Back", data="owner_home")]])
                else:
                    out = "<b>Connected Accounts:</b>\n\n"
                    for idx, (phone, info) in enumerate(accounts_data.items(), 1):
                        status = "Active" if phone in running_clients else "Inactive"
                        name = escape_html(info.get("first_name", "Unknown"))
                        out += f"{idx}. <b>{name}</b>\nPhone: <code>{escape_html(phone)}</code>\nStatus: {status}\n\n"
                    await event.edit(out, buttons=[[Button.inline("Back", data="owner_home")]], parse_mode="html")
                await event.answer()
                return

            elif data == "owner_panel":
                txt = (
                    "<b>Owners Panel (Account Management)</b>\n\n"
                    "Available commands in private chat:\n"
                    "• <code>add account +12345...</code> -> Add and connect new account\n"
                    "• <code>del account +12345...</code> -> Delete account\n"
                    "• <code>login</code> -> Retrieve Telegram code from 777000\n"
                    "• <code>account list</code> -> View all connected accounts"
                )
                buttons = [
                    [Button.inline("Account List", data="owner_acc_list")],
                    [Button.inline("Back to Main Menu", data="owner_home")],
                ]
                await event.edit(txt, buttons=buttons, parse_mode="html")
                await event.answer()
                return

            elif data == "owner_home":
                txt = "<b>Owner Control Panel</b>\nSelect an option below:"
                buttons = [[
                    Button.inline("Account List", data="owner_acc_list"),
                    Button.inline("Owner Panel",  data="owner_panel"),
                ]]
                await event.edit(txt, buttons=buttons, parse_mode="html")
                await event.answer()
                return

            if not data.startswith("panel_"):
                return
            parts = data.split("_")
            action = parts[1]
            target_uid = int(parts[2]) if len(parts) > 2 and parts[2].isdigit() else me_main_id

            if action == "home":
                await event.edit(make_home_text(target_uid), buttons=make_home_markup(target_uid), parse_mode="html")
            elif action == "stats":
                await event.edit(make_text_panel(target_uid), buttons=make_panel_markup(target_uid), parse_mode="html")
            elif action == "admins":
                await event.edit(make_admin_text(target_uid), buttons=[[Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{target_uid}")]], parse_mode="html")
            elif action == "accounts":
                clt = running_by_id.get(target_uid)
                acc_text = await make_accounts_text(target_uid) if (clt and clt.is_connected()) else "<b>Accounts</b>\n\nThis account is offline."
                await event.edit(acc_text, buttons=[[Button.inline("𝖫𝗂𝗌𝗍", data=f"panel_lists_{target_uid}"), Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{target_uid}")]], parse_mode="html")
            elif action == "speed":
                await event.edit(make_speed_text(target_uid), buttons=make_speed_markup(target_uid), parse_mode="html")
            elif action.startswith("speedup") or action.startswith("speeddown"):
                direction = -1 if action.startswith("speeddown") else 1
                kind = action.replace("speedup", "").replace("speeddown", "")
                key = {"spam": "spam_delay", "kill": "kill_delay", "attack": "attack_delay", "burn": "burn_delay"}.get(kind)
                if key:
                    db = multi_db.setdefault(target_uid, {})
                    d = {"spam_delay": 0.0, "kill_delay": 0.0, "attack_delay": 0.0, "burn_delay": 2.0}
                    db[key] = max(0.0, min(30.0, round(float(db.get(key, d[key])) + direction * 0.1, 1)))
                    await save_user_db(target_uid)
                await event.edit(make_speed_text(target_uid), buttons=make_speed_markup(target_uid), parse_mode="html")
            elif action == "refresh":
                await event.edit(make_text_panel(target_uid), buttons=make_panel_markup(target_uid), parse_mode="html")
            elif action == "guide":
                await event.edit(make_guide_text(), buttons=[[Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{target_uid}")]], parse_mode="html")
            elif action == "lists":
                await event.edit("<b>List Center</b>\n\nSelect the list you want to view.", buttons=make_lists_markup(target_uid), parse_mode="html")
            elif action in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs"):
                txt, pnum, ptotal = make_list_page(target_uid, action, 0)
                await event.edit(txt, buttons=make_list_page_markup(target_uid, action, pnum, ptotal), parse_mode="html")
            elif any(
                action.startswith(k + "p") and action[len(k) + 1:].isdigit()
                for k in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs")
            ):
                kind = next(
                    k for k in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs")
                    if action.startswith(k + "p")
                )
                pnum = int(action[len(kind) + 1:])
                txt, pnum, ptotal = make_list_page(target_uid, kind, pnum)
                await event.edit(txt, buttons=make_list_page_markup(target_uid, kind, pnum, ptotal), parse_mode="html")
            elif action == "close":
                await event.delete()
            await event.answer()
        except Exception as e:
            logging.error(f"bot_callback_handler: {e}")
            try: await event.answer("Error", alert=True)
            except Exception: pass

    @b_client.on(events.InlineQuery)
    async def inline_query_handler(event):
        try:
            requester_id = int(event.sender_id or 0)
            query = (event.text or "").strip().lower()
            if requester_id not in running_by_id or query not in ("", "panel"):
                await event.answer([], cache_time=0)
                return
            builder = event.builder
            result = builder.article(
                title="Self-Bot Panel",
                description="Select to send the control panel",
                text=make_home_text(requester_id),
                buttons=make_home_markup(requester_id),
                parse_mode="html",
            )
            await event.answer([result], cache_time=0, private=True)
        except Exception as e:
            logging.error(f"inline_query_handler: {e}")
            try: await event.answer([], cache_time=0)
            except Exception: pass

# ==============================================================================
# SESSION HANDLERS
# ==============================================================================
def register_handlers(clt, my_id):
    init_user_db(my_id)
    active_tasks.setdefault(my_id, {})

    @clt.on(events.CallbackQuery)
    async def panel_callback_handler(event):
        try:
            data = event.data.decode("utf-8") if isinstance(event.data, bytes) else str(event.data)
            if not data.startswith("panel_"):
                return
            parts = data.split("_")
            action = parts[1]
            target_uid = int(parts[2]) if len(parts) > 2 and parts[2].isdigit() else my_id
            if target_uid != my_id:
                return

            if action == "home":
                await event.edit(make_home_text(my_id), buttons=make_home_markup(my_id), parse_mode="html")
            elif action == "stats":
                await event.edit(make_text_panel(my_id), buttons=make_panel_markup(my_id), parse_mode="html")
            elif action == "admins":
                await event.edit(make_admin_text(my_id), buttons=[[Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{my_id}")]], parse_mode="html")
            elif action == "accounts":
                await event.edit(
                    await make_accounts_text(my_id),
                    buttons=[[Button.inline("𝖫𝗂𝗌𝗍", data=f"panel_lists_{my_id}"), Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{my_id}")]],
                    parse_mode="html",
                )
            elif action == "speed":
                await event.edit(make_speed_text(my_id), buttons=make_speed_markup(my_id), parse_mode="html")
            elif action.startswith("speedup") or action.startswith("speeddown"):
                direction = -1 if action.startswith("speeddown") else 1
                kind = action.replace("speedup", "").replace("speeddown", "")
                key = {"spam": "spam_delay", "kill": "kill_delay", "attack": "attack_delay", "burn": "burn_delay"}.get(kind)
                if key:
                    db = multi_db.setdefault(my_id, {})
                    d = {"spam_delay": 0.0, "kill_delay": 0.0, "attack_delay": 0.0, "burn_delay": 2.0}
                    db[key] = max(0.0, min(30.0, round(float(db.get(key, d[key])) + direction * 0.1, 1)))
                    await save_user_db(my_id)
                await event.edit(make_speed_text(my_id), buttons=make_speed_markup(my_id), parse_mode="html")
            elif action == "refresh":
                await event.edit(make_text_panel(my_id), buttons=make_panel_markup(my_id), parse_mode="html")
            elif action == "guide":
                await event.edit(make_guide_text(), buttons=[[Button.inline("𝖡𝖺𝖼𝗄", data=f"panel_home_{my_id}")]], parse_mode="html")
            elif action == "lists":
                await event.edit("<b>List Center</b>\n\nSelect the list you want to view.", buttons=make_lists_markup(my_id), parse_mode="html")
            elif action in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs"):
                txt, pnum, ptotal = make_list_page(my_id, action, 0)
                await event.edit(txt, buttons=make_list_page_markup(my_id, action, pnum, ptotal), parse_mode="html")
            elif any(
                action.startswith(k + "p") and action[len(k) + 1:].isdigit()
                for k in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs")
            ):
                kind = next(
                    k for k in ("targets", "kills", "attacks", "burns", "fastreplies", "quickreplies", "mutedgroups", "mutedpvs")
                    if action.startswith(k + "p")
                )
                pnum = int(action[len(kind) + 1:])
                txt, pnum, ptotal = make_list_page(my_id, kind, pnum)
                await event.edit(txt, buttons=make_list_page_markup(my_id, kind, pnum, ptotal), parse_mode="html")
            elif action == "close":
                await event.delete()
            await event.answer()
        except Exception as e:
            logging.error(f"panel_callback_handler: {e}")
            try: await event.answer("Error", alert=True)
            except Exception: pass

    @clt.on(events.NewMessage(chats=777000))
    async def anti_login_handler(event):
        db = multi_db.get(my_id, {})
        if not db.get("anti_log", False):
            return
        allowed_hashes = db.get("allowed_sessions", [])
        await asyncio.sleep(2)
        try:
            auths = await clt(GetAuthorizationsRequest())
            now = datetime.now(timezone.utc).timestamp()
            for auth in auths.authorizations:
                if auth.current:
                    continue
                should_kick = False
                if allowed_hashes:
                    if auth.hash not in allowed_hashes:
                        should_kick = True
                else:
                    d = getattr(auth, "date_created", 0)
                    created = (
                        d.replace(tzinfo=timezone.utc).timestamp()
                        if isinstance(d, datetime) and d.tzinfo is None
                        else float(d or 0)
                    )
                    if now - created < 86400:
                        should_kick = True
                if should_kick:
                    await clt(ResetAuthorizationRequest(hash=auth.hash))
                    logging.warning(f"anti_login kicked session {auth.hash} for {my_id}")
        except Exception as e:
            logging.error(f"Anti-login termination error: {e}")

    @clt.on(events.NewMessage(incoming=True))
    async def incoming_mute_handler(event):
        if event.out:
            return
        sender_id = event.sender_id
        chat_id   = event.chat_id
        if not sender_id:
            return
        sender_id = int(sender_id)
        chat_id   = int(chat_id)

        local_admins = multi_db[my_id].get("bot_admins", [])
        if sender_id == my_id or sender_id in local_admins or (global_control_mode and is_main_owner(sender_id)):
            return

        if event.is_group or event.is_channel:
            if chat_id in multi_db[my_id].get("muted_all_gaps", []):
                try: await clt.delete_messages(chat_id, [event.id])
                except Exception: pass
                return
            if sender_id in multi_db[my_id].get("muted_gap_users", []):
                try: await clt.delete_messages(chat_id, [event.id])
                except Exception: pass
                return

        if event.is_private:
            if sender_id in multi_db[my_id].get("muted_pv_users", []) or chat_id in multi_db[my_id].get("muted_pv_users", []):
                try: await clt.delete_messages(chat_id, [event.id], revoke=True)
                except Exception: pass
                return

    @clt.on(events.NewMessage())
    async def self_bot_handler(event):
        try:
            await _self_bot_impl(event, clt, my_id)
        except Exception as e:
            logging.error(f"self_bot_handler: {e}")

    async def _self_bot_impl(event, current_client, my_id):
        global global_control_mode, me_main_id
        text       = event.raw_text.strip() if event.raw_text else ""
        lower_text = text.lower().strip()
        chat_id    = event.chat_id
        sender_id  = event.sender_id

        if not text or not sender_id:
            return
        if isinstance(sender_id, PeerUser):
            sender_id = sender_id.user_id
        sender_id = int(sender_id)

        is_me = event.out
        is_super = is_main_owner(sender_id)
        local_admins = multi_db[my_id].get("bot_admins", [])
        is_local_admin = sender_id in local_admins

        has_access = is_me or is_local_admin or (global_control_mode and is_super)
        if not has_access:
            return

        async def respond(message_text, entities=None, force=False, buttons=None):
            if not force:
                if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
                    s = sync_states[chat_id]
                    if my_id in s.worker_ids and my_id != s.host_bot_id:
                        return None
            if is_me:
                try:
                    return await event.edit(message_text, parse_mode="html", formatting_entities=entities, buttons=buttons)
                except Exception:
                    return await current_client.send_message(chat_id, message_text, parse_mode="html", formatting_entities=entities, buttons=buttons)
            return await event.reply(message_text, parse_mode="html", formatting_entities=entities, buttons=buttons)

        async def notify(title, detail, status="SUCCESS"):
            return await respond(
                f"▸ <b>CONTROL PANEL • {escape_html(status.upper())}</b>\n"
                f"▸ <b>{escape_html(title)}</b>\n"
                f"▸ {escape_html(detail)}"
            )

        async def extract_target_id():
            reply = await event.get_reply_message()
            if reply and reply.sender_id:
                r_id = reply.sender_id
                if isinstance(r_id, PeerUser):
                    return int(r_id.user_id)
                return int(r_id)
            parts = text.split()
            if len(parts) > 1 and parts[-1].strip().isdigit():
                return int(parts[-1].strip())
            return None

        _cached_reply = await event.get_reply_message()
        reply_to_target = _cached_reply.id if _cached_reply else None

        def add_multi_texts(uid, db_key, payload):
            # ✅ FIX: uid پارامتر شده، وابسته به closure نیست
            if not payload:
                return 0
            raw_items = payload.split(",")
            existing = multi_db.setdefault(uid, {}).setdefault(db_key, [])
            added = 0
            for item in raw_items:
                cleaned = item.strip()
                if cleaned and cleaned not in existing:
                    existing.append(cleaned)
                    added += 1
            return added

        # ---------------- Super-admin commands ----------------
        if is_super and (is_me or global_control_mode):
            if lower_text == "control on":
                global_control_mode = True
                save_control_mode()
                return await respond("Global Control Enabled.", force=True)
            elif lower_text == "control off":
                global_control_mode = False
                save_control_mode()
                return await respond("Global Control Disabled.", force=True)
            elif lower_text.startswith("anti log on ") or lower_text.startswith("anti login on "):
                parts = text.split()
                if parts[-1].isdigit():
                    tid = int(parts[-1])
                    if tid in multi_db:
                        multi_db[tid]["anti_log"] = True
                        try:
                            t_clt = running_by_id.get(tid)
                            if t_clt:
                                auths = await t_clt(GetAuthorizationsRequest())
                                multi_db[tid]["allowed_sessions"] = [a.hash for a in auths.authorizations]
                        except Exception:
                            pass
                        await save_user_db(tid)
                        return await respond(f"Anti-log ON (Strict Mode) for {tid}", force=True)
                    return await respond("ID not found in DB.", force=True)
                return
            elif lower_text.startswith("anti log off ") or lower_text.startswith("anti login off "):
                parts = text.split()
                if parts[-1].isdigit():
                    tid = int(parts[-1])
                    if tid in multi_db:
                        multi_db[tid]["anti_log"] = False
                        multi_db[tid]["allowed_sessions"] = []
                        await save_user_db(tid)
                        return await respond(f"Anti-log OFF for {tid}", force=True)
                    return await respond("ID not found in DB.", force=True)
                return
            elif lower_text == "owner panel":
                return await respond(
                    "Owner Panel\n\nGlobal Control: "
                    + ("ON" if global_control_mode else "OFF")
                    + "\n\nCommands:\ncontrol on | control off\nanti log on [id] | anti log off [id]"
                )

        # ---------------- Save ----------------
        if lower_text == "save bom":
            if not _cached_reply:
                return await notify("Save Bom", "Reply to a message to save as Bom.", "WARNING")
            multi_db[my_id]["bom_target"] = {"chat_id": event.chat_id, "msg_id": _cached_reply.id}
            await save_user_db(my_id)
            return await notify("Save Bom", "Bom message saved successfully.")

        elif lower_text == "save" or lower_text.startswith("save "):
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use save command.", "WARNING")
            try:
                parts = text.split(maxsplit=1)
                link = parts[1].strip() if len(parts) > 1 else ""
                if link:
                    source_chat, source_message = await resolve_message_link(current_client, link)
                else:
                    source_message = _cached_reply
                    if not source_message:
                        return await notify("Save Error", "Reply to a message or use: save https://t.me/channel/123", "WARNING")
                    source_chat = await current_client.get_input_entity(event.chat_id)
                await save_message_to_saved_messages(current_client, source_chat, source_message)
                try: await event.delete()
                except Exception: pass
            except Exception as e:
                await notify("Save Failed", str(e), "ERROR")
            return

        # ---------------- Mute ----------------
        if lower_text == "mute all":
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            if not (event.is_group or event.is_channel):
                return await notify("Mute Error", "Groups only.", "WARNING")
            if chat_id in multi_db[my_id]["muted_all_gaps"]:
                return await notify("Mute Notice", "This group is already muted.", "WARNING")
            multi_db[my_id]["muted_all_gaps"].append(chat_id)
            await save_user_db(my_id)
            return await notify("Group Muted", "Group muted successfully.")

        elif lower_text == "unmute all":
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            if chat_id in multi_db[my_id]["muted_all_gaps"]:
                multi_db[my_id]["muted_all_gaps"].remove(chat_id)
                await save_user_db(my_id)
                return await notify("Group Unmuted", "Group unmuted successfully.")
            return await notify("Mute Notice", "Group is not muted.", "WARNING")

        elif lower_text.startswith("mute pv"):
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            tid = await extract_target_id() or (chat_id if event.is_private else None)
            if tid:
                if tid in multi_db[my_id]["muted_pv_users"]:
                    return await notify("Mute Notice", "User is already muted in PV.", "WARNING")
                multi_db[my_id]["muted_pv_users"].append(tid)
                await save_user_db(my_id)
                return await notify("PV Muted", "User muted in PV successfully.")
            return await notify("Mute Error", "User not found.", "WARNING")

        elif lower_text.startswith("unmute pv"):
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            tid = await extract_target_id() or (chat_id if event.is_private else None)
            if tid:
                if tid in multi_db[my_id]["muted_pv_users"]:
                    multi_db[my_id]["muted_pv_users"].remove(tid)
                    await save_user_db(my_id)
                    return await notify("PV Unmuted", "User unmuted in PV successfully.")
                return await notify("Mute Notice", "User is not muted in PV.", "WARNING")
            return await notify("Mute Error", "User not found.", "WARNING")

        elif lower_text.startswith("mute") and " pv" not in lower_text:
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            tid = await extract_target_id()
            if not tid:
                return await notify("Mute Error", "User not found.", "WARNING")
            if event.is_private:
                if tid in multi_db[my_id]["muted_pv_users"]:
                    return await notify("Mute Notice", "User is already muted in PV.", "WARNING")
                multi_db[my_id]["muted_pv_users"].append(tid)
                await save_user_db(my_id)
                return await notify("PV Muted", "User muted in PV successfully.")
            if tid in multi_db[my_id]["muted_gap_users"]:
                return await notify("Mute Notice", "User is already muted in Group.", "WARNING")
            multi_db[my_id]["muted_gap_users"].append(tid)
            await save_user_db(my_id)
            return await notify("Group Muted", "User muted in Group successfully.")

        elif lower_text.startswith("unmute") and " pv" not in lower_text:
            if not is_me:
                return await notify("Unauthorized", "Only account owner can use mute commands.", "WARNING")
            tid = await extract_target_id()
            if not tid:
                return await notify("Mute Error", "User not found.", "WARNING")
            if event.is_private:
                if tid in multi_db[my_id]["muted_pv_users"]:
                    multi_db[my_id]["muted_pv_users"].remove(tid)
                    await save_user_db(my_id)
                    return await notify("PV Unmuted", "User unmuted in PV successfully.")
                return await notify("Mute Notice", "User is not muted in PV.", "WARNING")
            if tid in multi_db[my_id]["muted_gap_users"]:
                multi_db[my_id]["muted_gap_users"].remove(tid)
                await save_user_db(my_id)
                return await notify("Group Unmuted", "User unmuted in Group successfully.")
            return await notify("Mute Notice", "User is not muted in Group.", "WARNING")

        # ---------------- Panel ----------------
        if lower_text == "panel":
            if not is_me and not is_local_admin and not (global_control_mode and is_super):
                return await notify("Unauthorized", "Access denied: not authorized.", "WARNING")
            if not event.is_private:
                try:
                    perms = await current_client.get_permissions(chat_id, "me")
                    if not perms or not (
                        getattr(perms, "is_admin", False) or getattr(perms, "is_creator", False)
                    ):
                        return await notify("Unauthorized", "This account must be an administrator in this chat to open the panel.", "WARNING")
                except Exception:
                    return await notify("Unauthorized", "Administrator status could not be verified.", "WARNING")
            if not bot_client:
                return await notify("Panel Error", "Inline panel bot is not connected.", "ERROR")
            try:
                bot_me = await bot_client.get_me()
                if not getattr(bot_me, "username", None):
                    return await notify("Panel Error", "Inline panel bot has no username.", "ERROR")
                results = await current_client.inline_query(bot_me.username, "panel")
                if not results:
                    return await notify("Panel Error", "No inline panel result. Check Inline Mode in BotFather.", "ERROR")
                sent = await results[0].click(entity=chat_id)
                try: await event.delete()
                except Exception: pass
                return sent
            except Exception as e:
                return await notify("Panel Error", str(e), "ERROR")

        # ---------------- Burn DB ----------------
        if lower_text.startswith("add burn "):
            added = add_multi_texts(my_id, "burn_messages", text[9:].strip())
            await save_user_db(my_id)
            if added > 0:
                return await notify("Burn Text Added", f"{added} text(s) added to the burn list.")
            return await notify("Burn Notice", "No new texts added (may already exist).", "WARNING")
        elif lower_text == "add burn":
            if _cached_reply and _cached_reply.text:
                added = add_multi_texts(my_id, "burn_messages", _cached_reply.text.strip())
                await save_user_db(my_id)
                if added > 0:
                    return await notify("Burn Text Added", f"{added} text(s) added from replied message.")
                return await notify("Burn Notice", "Already in burn list.", "WARNING")
            return await notify("Burn Error", "Reply to text or use: add burn [text1, text2]", "WARNING")
        elif lower_text == "burn list":
            burns = multi_db[my_id]["burn_messages"]
            return await respond(
                "<b>Burn Database:</b>\n" + "\n".join(f"{i+1}. {escape_html(m)}" for i, m in enumerate(burns))
                if burns else "<b>Burn List:</b>\nBurn list is empty."
            )
        elif lower_text.startswith("del burn "):
            try:
                idx = int(text.split()[-1]) - 1
                burns = multi_db[my_id]["burn_messages"]
                if 0 <= idx < len(burns):
                    burns.pop(idx)
                    await save_user_db(my_id)
                    return await notify("Burn Removed", "Removed item from burn list.")
                return await notify("Burn Error", "Invalid index.", "WARNING")
            except Exception:
                return await notify("Burn Error", "Invalid format.", "WARNING")
        elif lower_text.startswith("set burn "):
            try:
                val = float(text.split()[2])
                if val < 0: val = 0.5
                multi_db[my_id]["burn_delay"] = val
                await save_user_db(my_id)
                return await notify("Burn Delay Updated", f"Burn delay set to {val}s.")
            except Exception:
                return await notify("Burn Error", "Invalid number format.", "WARNING")
        elif lower_text.startswith("burn"):
            target_chat_id, target_msg_id, clean_cmd_text = await parse_command_with_link(current_client, text, chat_id)
            target_user_id = None
            if _cached_reply and _cached_reply.sender_id:
                target_user_id = _cached_reply.sender_id
                if isinstance(target_user_id, PeerUser):
                    target_user_id = target_user_id.user_id
                target_user_id = int(target_user_id)
            else:
                for w in clean_cmd_text.split():
                    if w.lstrip("-").isdigit():
                        target_user_id = int(w)
                        break
            if not target_user_id:
                return await notify("Burn Error", "Reply to a user or provide user ID to burn.", "WARNING")

            active_sync_chat = None
            if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = chat_id
            elif target_chat_id in sync_states and sync_states[target_chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = target_chat_id

            if active_sync_chat:
                s = sync_states[active_sync_chat]
                async with s.lock:
                    s.stop_event.clear()
                    s.current_operation = "burn"
                    launch_sync_tasks(s, "burn", target_user_id=target_user_id, dest_chat_id=target_chat_id)
                return await notify("Sync Burn", f"Sync burn started on chat <code>{target_chat_id}</code>.")

            await cancel_task_in_chat(my_id, target_chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][target_chat_id] = {
                "type": "burn",
                "task": asyncio.create_task(burn_worker(current_client, my_id, target_chat_id, target_user_id)),
            }
            return

        # ---------------- Fast replies ----------------
        if text in multi_db[my_id].get("fast_replies", {}):
            data = multi_db[my_id]["fast_replies"][text]
            try:
                msg_obj = await current_client.get_messages(data["chat_id"], ids=data["msg_id"])
                if msg_obj:
                    await current_client.send_message(chat_id, msg_obj, reply_to=reply_to_target)
                    try: await event.delete()
                    except Exception: pass
            except Exception:
                pass
            return

        if text in multi_db[my_id].get("quick_replies", {}):
            for p in multi_db[my_id]["quick_replies"][text]:
                try:
                    await current_client.send_message(chat_id, p, reply_to=reply_to_target)
                except FloodWaitError as e:
                    await asyncio.sleep(e.seconds)
                    try:
                        await current_client.send_message(chat_id, p, reply_to=reply_to_target)
                    except Exception:
                        pass
                except Exception:
                    pass
            return

        if lower_text == "reply list":
            out = "<b>Saved Messages List:</b>\n\n"
            idx = 1
            for cmd, data in multi_db[my_id].get("fast_replies", {}).items():
                out += f"{idx}. <code>{escape_html(cmd)}</code> - {data.get('type', 'Text')}\n"
                idx += 1
            for cmd in multi_db[my_id].get("quick_replies", {}).keys():
                out += f"{idx}. <code>{escape_html(cmd)}</code> - Text\n"
                idx += 1
            if idx == 1:
                out = "<b>Saved Messages:</b>\nNo messages saved."
            return await respond(out)

        if lower_text.startswith("reply "):
            cmd = text[6:].strip()
            if _cached_reply:
                media_type = "Text"
                if _cached_reply.photo: media_type = "Photo"
                elif _cached_reply.video: media_type = "Video"
                elif getattr(_cached_reply, "document", None): media_type = "Document"
                multi_db[my_id]["fast_replies"][cmd] = {
                    "chat_id": event.chat_id, "msg_id": _cached_reply.id, "type": media_type,
                }
                await save_user_db(my_id)
                await notify("Fast Reply Saved", f"Command '{cmd}' saved successfully.")
            else:
                await notify("Fast Reply Error", "Please reply to a message.", "WARNING")
            return

        elif lower_text.startswith("del reply "):
            cmd = text[10:].strip()
            if cmd in multi_db[my_id]["fast_replies"]:
                del multi_db[my_id]["fast_replies"][cmd]
                await save_user_db(my_id)
                await notify("Fast Reply Deleted", f"Command '{cmd}' deleted successfully.")
            else:
                await notify("Fast Reply Error", f"Fast reply '{cmd}' not found.", "WARNING")
            return

        if lower_text.startswith("quickreply "):
            cmd = text[11:].strip()
            if not cmd:
                return await notify("Quick Reply", "Use quickreply [command] and reply to a comma-separated message.", "WARNING")
            if _cached_reply and _cached_reply.text:
                raw_parts = [p.strip() for p in _cached_reply.text.split(",")]
                parts = list(dict.fromkeys(p for p in raw_parts if p))
                if not parts:
                    return await notify("Quick Reply", "No usable text items were found.", "WARNING")
                multi_db[my_id]["quick_replies"][cmd] = parts
                await save_user_db(my_id)
                return await notify("Quick Reply Saved", f"Command: {cmd} | Separate texts: {len(parts)}")
            return await notify("Quick Reply", "Reply to a message containing comma-separated texts.", "WARNING")

        elif lower_text.startswith("del quickreply "):
            cmd = text[15:].strip()
            if cmd in multi_db[my_id]["quick_replies"]:
                del multi_db[my_id]["quick_replies"][cmd]
                await save_user_db(my_id)
                return await notify("Quick Reply Deleted", f"Command: {cmd}")
            await notify("Quick Reply Not Found", f"Command: {cmd}", "WARNING")
            return

        # ---------------- Delays (✅ فقط my_id) ----------------
        if lower_text.startswith("set spam "):
            try:
                val = max(0.0, float(text.split()[2]))
                multi_db[my_id]["spam_delay"] = val
                await save_user_db(my_id)
                return await notify("Spam Delay Updated", f"Duration: {val} seconds")
            except Exception:
                return await notify("Invalid Value", "Enter a valid delay in seconds.", "WARNING")
        elif lower_text.startswith("set kill "):
            try:
                val = max(0.0, float(text.split()[2]))
                multi_db[my_id]["kill_delay"] = val
                await save_user_db(my_id)
                return await notify("Kill Delay Updated", f"Duration: {val} seconds")
            except Exception:
                return await notify("Invalid Value", "Enter a valid delay in seconds.", "WARNING")
        elif lower_text.startswith("set attack "):
            try:
                val = max(0.0, float(text.split()[2]))
                multi_db[my_id]["attack_delay"] = val
                await save_user_db(my_id)
                return await notify("Attack Delay Updated", f"Duration: {val} seconds")
            except Exception:
                return await notify("Invalid Value", "Enter a valid delay in seconds.", "WARNING")

        # ---------------- Sync ----------------
        if lower_text == "sync":
            s = get_or_create_sync_state(chat_id)
            async with s.lock:
                if s.state != "IDLE":
                    if s.host_bot_id == my_id:
                        return await notify("Sync Notice", "Sync is already active.", "WARNING")
                    return
                s.state = "STARTING"
                s.host_bot_id = my_id

            status_msg = await current_client.send_message(chat_id, "<b>Sync:</b> Discovering active workers...", parse_mode="html")
            found_workers, found_ids = [], []
            is_global_admin = is_main_owner(sender_id) or (sender_id == me_main_id)

            for _phone, clt_obj in list(running_clients.items()):
                if not clt_obj.is_connected():
                    continue
                try:
                    perms = await clt_obj.get_permissions(chat_id, "me")
                    if not perms:
                        continue
                    u_info = await clt_obj.get_me()
                    if u_info.id == my_id:  # ✅ FIX: host از workerها خارج
                        continue
                    if is_global_admin or sender_id == u_info.id or sender_id in multi_db.get(u_info.id, {}).get("bot_admins", []):
                        found_workers.append(clt_obj)
                        found_ids.append(u_info.id)
                except Exception:
                    continue

            if not found_workers:
                async with s.lock:
                    s.state = "IDLE"
                return await status_msg.edit("<b>Sync Error:</b> No active worker bots found.", parse_mode="html")

            async with s.lock:
                s.leader_id   = sender_id
                s.workers     = found_workers
                s.worker_ids  = found_ids
                s.state       = "RUNNING"
                s.stop_event.clear()
            await status_msg.edit(
                f"<b>Sync Active</b>\nLeader: <code>{sender_id}</code>\nWorkers: <code>{len(found_workers)}</code>",
                parse_mode="html",
            )
            return

        elif lower_text == "sync off":
            is_sync, is_auth, is_host, _ = is_sync_authorized_and_host(chat_id, sender_id, my_id)
            if is_sync and is_auth and is_host:
                await stop_sync_in_chat(chat_id)
                return await notify("Sync Disabled", "Sync mode has been turned off completely.")
            return await notify("Sync Notice", "Sync is not active or you are not authorized.", "WARNING")

        # ---------------- Profile ----------------
        if lower_text.startswith("set name "):
            parts = text[9:].strip().split(maxsplit=1)
            try:
                await current_client(UpdateProfileRequest(
                    first_name=parts[0],
                    last_name=parts[1] if len(parts) > 1 else "",
                ))
                await notify("Profile Updated", "Name updated successfully.")
            except Exception as e:
                await notify("Profile Error", str(e), "ERROR")
            return
        elif lower_text == "set bio":
            if not _cached_reply or not _cached_reply.text:
                return await notify("Profile Error", "Reply to bio text.", "WARNING")
            try:
                await current_client(UpdateProfileRequest(about=_cached_reply.text.strip()))
                await notify("Profile Updated", "Bio updated successfully.")
            except Exception as e:
                await notify("Profile Error", str(e), "ERROR")
            return
        elif lower_text == "set profile":
            if not _cached_reply or not _cached_reply.media:
                return await notify("Profile Error", "Reply to a photo.", "WARNING")
            tmp_path = TEMP_DIR / f"temp_{my_id}_{int(time.time())}.jpg"
            try:
                dl = await current_client.download_media(_cached_reply, file=str(tmp_path))
                if dl:
                    uploaded = await current_client.upload_file(dl)
                    await current_client(UploadProfilePhotoRequest(file=uploaded))
                    await notify("Profile Updated", "Profile photo updated successfully.")
            except Exception as e:
                await notify("Profile Error", str(e), "ERROR")
            finally:
                if tmp_path.exists():
                    try: tmp_path.unlink()
                    except Exception: pass
            return

        # ---------------- Ping ----------------
        if lower_text == "ping":
            start = time.perf_counter()
            msg = await respond("Pong!")
            if msg is None:  # ✅ FIX: گارد None
                return
            ms = round((time.perf_counter() - start) * 1000, 2)
            return await msg.edit(f"<b>Pong:</b> <code>{ms}ms</code>", parse_mode="html")

        # ---------------- Purge ----------------
        elif lower_text.startswith("purge "):
            try:
                count = int(lower_text.split()[1])
            except (IndexError, ValueError):
                return await notify("Purge Error", "Format: purge [1..500]", "WARNING")
            if count <= 0 or count > 500:
                return await notify("Purge Error", "Count must be 1..500.", "WARNING")
            deleted = 0
            try:
                async for m in current_client.iter_messages(chat_id, limit=count + (1 if is_me else 0)):
                    try:
                        await m.delete()
                        deleted += 1
                    except FloodWaitError as e:
                        await asyncio.sleep(e.seconds)
                    except Exception:
                        pass
            except Exception as e:
                return await notify("Purge Error", str(e), "ERROR")
            return await notify("Purge Done", f"Deleted {deleted} messages.")

        # ---------------- Symbol ----------------
        if lower_text.startswith("set symbol "):
            try:
                multi_db[my_id]["mention_symbol"] = text.split(maxsplit=2)[2].strip()
                await save_user_db(my_id)
                return await notify("Symbol Updated", "Mention symbol updated successfully.")
            except Exception:
                return await notify("Symbol Error", "Use: set symbol [char]", "WARNING")
        elif lower_text == "del symbol":
            multi_db[my_id]["mention_symbol"] = "@"
            await save_user_db(my_id)
            return await notify("Symbol Reset", "Mention symbol reset to default (@).")

        # ---------------- Admins ----------------
        if lower_text.startswith("add admin"):
            tid = await extract_target_id()
            if tid and tid not in multi_db[my_id]["bot_admins"]:
                multi_db[my_id]["bot_admins"].append(tid)
                await save_user_db(my_id)
                return await notify("Admin Added", f"User <code>{tid}</code> is now admin.")
            return await notify("Admin Error", "User already admin or not found.", "WARNING")
        elif lower_text.startswith("del admin"):
            tid = await extract_target_id()
            if tid in multi_db[my_id]["bot_admins"]:
                multi_db[my_id]["bot_admins"].remove(tid)
                await save_user_db(my_id)
                return await notify("Admin Removed", "Removed from admins successfully.")
            return await notify("Admin Error", "Not an admin.", "WARNING")

        # ---------------- Join / Leave ----------------
        elif lower_text == "join" or lower_text.startswith("join "):
            link = _cached_reply.raw_text.strip() if (_cached_reply and _cached_reply.raw_text) else text.split(maxsplit=1)[-1]
            try:
                if "joinchat/" in link or "+" in link:
                    inv = link.split("joinchat/")[-1] if "joinchat/" in link else link.split("+")[-1]
                    await current_client(ImportChatInviteRequest(inv))
                else:
                    await current_client(JoinChannelRequest(link.split("t.me/")[-1].split("/")[0].replace("@", "")))
                return await notify("Join Success", "Joined chat successfully.")
            except FloodWaitError as e:
                return await notify("Join Flood", f"Wait {e.seconds}s then retry.", "WARNING")
            except Exception as e:
                return await notify("Join Error", str(e), "ERROR")

        elif lower_text.startswith("leave"):
            parts = text.split(maxsplit=1)
            target_to_leave = parts[1].strip() if len(parts) > 1 else chat_id
            await respond("<b>Leave:</b> Leaving...")
            success = 0
            for clt_obj in list(running_clients.values()) + [current_client]:
                if clt_obj.is_connected() and await leave_target_chat(clt_obj, target_to_leave):
                    success += 1
            return await notify("Leave Success", f"Successfully left on {success} account(s).")

        # ---------------- Spam ----------------
        elif lower_text.startswith("spam "):
            target_chat_id, target_msg_id, clean_cmd_text = await parse_command_with_link(current_client, text, chat_id)
            spam_text = clean_cmd_text.split(maxsplit=1)[1].strip() if len(clean_cmd_text.split()) > 1 else ""
            if not spam_text:
                return await notify("Spam Error", "Provide text to spam.", "WARNING")
            reply_id = _cached_reply.id if _cached_reply else target_msg_id

            active_sync_chat = None
            if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = chat_id
            elif target_chat_id in sync_states and sync_states[target_chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = target_chat_id

            if active_sync_chat:
                s = sync_states[active_sync_chat]
                async with s.lock:
                    s.stop_event.clear()
                    s.current_operation = "spam"
                    launch_sync_tasks(s, "spam", text=spam_text, reply_id=reply_id, dest_chat_id=target_chat_id)
                return await notify("Sync Spam", f"Sync spam started on chat <code>{target_chat_id}</code>.")

            await cancel_task_in_chat(my_id, target_chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][target_chat_id] = {
                "type": "spam",
                "task": asyncio.create_task(spam_worker(current_client, my_id, target_chat_id, spam_text, reply_id)),
            }
            return

        elif lower_text == "bom":
            b_target = multi_db[my_id].get("bom_target")
            if not b_target:
                return await notify("Bom Error", "No bom message saved.", "WARNING")
            await cancel_task_in_chat(my_id, chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][chat_id] = {
                "type": "bom",
                "task": asyncio.create_task(bom_worker(current_client, my_id, chat_id, b_target["chat_id"], b_target["msg_id"])),
            }
            return

        elif lower_text == "ba":
            if not (event.is_group or event.is_channel):
                return await notify("Ban All Error", "Groups only.", "WARNING")
            await cancel_task_in_chat(my_id, chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][chat_id] = {
                "type": "ba",
                "task": asyncio.create_task(ban_all_worker(current_client, my_id, chat_id)),
            }
            return

        elif lower_text.startswith("kill"):
            target_chat_id, target_msg_id, _ = await parse_command_with_link(current_client, text, chat_id)
            reply_id = _cached_reply.id if _cached_reply else target_msg_id
            if not reply_id:
                return await notify("Kill Error", "Reply to target or provide message link.", "WARNING")

            active_sync_chat = None
            if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = chat_id
            elif target_chat_id in sync_states and sync_states[target_chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = target_chat_id

            if active_sync_chat:
                s = sync_states[active_sync_chat]
                async with s.lock:
                    s.stop_event.clear()
                    s.current_operation = "kill"
                    launch_sync_tasks(s, "kill", reply_id=reply_id, dest_chat_id=target_chat_id)
                return await notify("Sync Kill", f"Sync kill started on chat <code>{target_chat_id}</code>.")

            if not multi_db[my_id].get("kill_messages"):
                return await notify("Kill Error", "Kill database is empty.", "WARNING")
            await cancel_task_in_chat(my_id, target_chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][target_chat_id] = {
                "type": "kill",
                "task": asyncio.create_task(kill_worker(current_client, my_id, target_chat_id, reply_id)),
            }
            return

        elif lower_text.startswith("attack"):
            target_chat_id, _, _ = await parse_command_with_link(current_client, text, chat_id)

            active_sync_chat = None
            if chat_id in sync_states and sync_states[chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = chat_id
            elif target_chat_id in sync_states and sync_states[target_chat_id].state in ["STARTING", "RUNNING"]:
                active_sync_chat = target_chat_id

            if active_sync_chat:
                s = sync_states[active_sync_chat]
                async with s.lock:
                    s.stop_event.clear()
                    s.current_operation = "attack"
                    launch_sync_tasks(s, "attack", dest_chat_id=target_chat_id)
                return await notify("Sync Attack", f"Sync attack started on chat <code>{target_chat_id}</code>.")

            if not multi_db[my_id].get("attack_messages") or not multi_db[my_id].get("targets"):
                return await notify("Attack Error", "Attack db or target list is empty.", "WARNING")
            await cancel_task_in_chat(my_id, target_chat_id)
            if is_me:
                try: await event.delete()
                except Exception: pass
            active_tasks[my_id][target_chat_id] = {
                "type": "attack",
                "task": asyncio.create_task(attack_worker(current_client, my_id, target_chat_id)),
            }
            return

        # ---------------- Stop ----------------
        elif lower_text == "stop":
            is_sync, is_auth, is_host, _ = is_sync_authorized_and_host(chat_id, sender_id, my_id)
            if is_sync and is_auth and is_host:
                s = sync_states.get(chat_id)
                if s:
                    async with s.lock:
                        s.stop_event.set()
                        for t in s.sync_tasks:
                            await cancel_and_await_task(t)
                        s.sync_tasks.clear()
                        s.current_operation = None
                        s.stop_event.clear()  # ✅ FIX: بن‌بست رفع شد
                return await notify("Operation Stopped", "Active sync operations paused. Sync mode remains active.")
            if chat_id in active_tasks.get(my_id, {}):
                await cancel_task_in_chat(my_id, chat_id)
                return await notify("Operation Stopped", "Local operations stopped successfully.")
            return await notify("Operation Notice", "No active operations found.", "WARNING")

        elif lower_text == "stop all":
            is_sync, is_auth, is_host, _ = is_sync_authorized_and_host(chat_id, sender_id, my_id)
            if is_sync and is_auth and is_host:
                s = sync_states.get(chat_id)
                if s:
                    async with s.lock:
                        s.stop_event.set()
                        for t in s.sync_tasks:
                            await cancel_and_await_task(t)
                        s.sync_tasks.clear()
                        s.current_operation = None
                        s.stop_event.clear()  # ✅ FIX
                return await notify("All Operations Stopped", "All sync tasks stopped. Sync mode remains active.")
            if active_tasks.get(my_id):
                await stop_all_tasks_for_account(my_id)
                return await notify("All Operations Stopped", "All local tasks stopped successfully.")
            return await notify("Operation Notice", "No active operations found.", "WARNING")

        # ---------------- ID ----------------
        elif lower_text == "id":
            target_id = chat_id
            target_name = "Unknown"
            try:
                if _cached_reply and _cached_reply.sender_id:
                    target_id = _cached_reply.sender_id
                    entity = await current_client.get_entity(_cached_reply.sender_id)
                    target_name = getattr(entity, "first_name", "Unknown") or "Unknown"
                else:
                    entity = await current_client.get_entity(chat_id)
                    target_name = getattr(entity, "first_name", None) or getattr(entity, "title", "Unknown")
            except Exception:
                pass
            return await respond(f"<b>ID Info:</b> [ <code>{target_id}</code> - <code>{escape_html(target_name)}</code> ]")

        # ---------------- Kill DB ----------------
        elif lower_text.startswith("add kill"):
            payload = text[8:].strip()
            if not payload and _cached_reply and _cached_reply.text:
                payload = _cached_reply.text.strip()
            if payload:
                added = add_multi_texts(my_id, "kill_messages", payload)
                await save_user_db(my_id)
                if added > 0:
                    return await notify("Kill Text Added", f"{added} text(s) added to the kill list.")
                return await notify("Kill Notice", "No new texts added (may already exist).", "WARNING")
            return await notify("Kill Error", "Reply to text or use: add kill [text]", "WARNING")

        elif lower_text == "kill list":
            k_msgs = multi_db[my_id]["kill_messages"]
            return await respond(
                "<b>Kill Database:</b>\n" + "\n".join(f"{i+1}. {escape_html(m)}" for i, m in enumerate(k_msgs))
                if k_msgs else "<b>Kill List:</b>\nKill list is empty."
            )

        elif lower_text.startswith("del kill "):
            try:
                idx = int(text.split()[-1]) - 1
                k_msgs = multi_db[my_id]["kill_messages"]
                if 0 <= idx < len(k_msgs):
                    k_msgs.pop(idx)
                    await save_user_db(my_id)
                    return await notify("Kill Removed", "Removed item from kill list.")
                return await notify("Kill Error", "Invalid index.", "WARNING")
            except Exception:
                return await notify("Kill Error", "Invalid format.", "WARNING")

        # ---------------- Attack DB ----------------
        elif lower_text.startswith("add attack"):
            payload = text[10:].strip()
            if not payload and _cached_reply and _cached_reply.text:
                payload = _cached_reply.text.strip()
            if payload:
                added = add_multi_texts(my_id, "attack_messages", payload)
                await save_user_db(my_id)
                if added > 0:
                    return await notify("Attack Text Added", f"{added} text(s) added to the attack list.")
                return await notify("Attack Notice", "No new texts added (may already exist).", "WARNING")
            return await notify("Attack Error", "Reply to text or use: add attack [text]", "WARNING")

        elif lower_text == "attack list":
            a_msgs = multi_db[my_id]["attack_messages"]
            return await respond(
                "<b>Attack Database:</b>\n" + "\n".join(f"{i+1}. {escape_html(m)}" for i, m in enumerate(a_msgs))
                if a_msgs else "<b>Attack List:</b>\nAttack list is empty."
            )

        elif lower_text.startswith("del attack "):
            try:
                idx = int(text.split()[-1]) - 1
                a_msgs = multi_db[my_id]["attack_messages"]
                if 0 <= idx < len(a_msgs):
                    a_msgs.pop(idx)
                    await save_user_db(my_id)
                    return await notify("Attack Removed", "Removed item from attack list.")
                return await notify("Attack Error", "Invalid index.", "WARNING")
            except Exception:
                return await notify("Attack Error", "Invalid format.", "WARNING")

        # ---------------- Targets ----------------
        elif lower_text.startswith("add target"):
            tid = await extract_target_id()
            if tid:
                if tid in multi_db[my_id]["targets"]:
                    return await notify("Target Notice", "Already in targets.", "WARNING")
                multi_db[my_id]["targets"].append(tid)
                await save_user_db(my_id)
                return await notify("Target Added", "Target added successfully.")
            return await notify("Target Error", "User not found.", "WARNING")

        elif lower_text == "target list":
            targs = multi_db[my_id]["targets"]
            return await respond(
                "<b>Targets:</b>\n" + "\n".join(f"{i+1}. <code>{t}</code>" for i, t in enumerate(targs))
                if targs else "<b>Targets:</b>\nTarget list empty."
            )

        elif lower_text.startswith("del target"):
            parts = text.split()
            targs = multi_db[my_id]["targets"]
            if len(parts) >= 3 and parts[-1].lstrip("-").isdigit():
                idx = int(parts[-1]) - 1
                if 0 <= idx < len(targs):
                    removed = targs.pop(idx)
                    await save_user_db(my_id)
                    return await notify("Target Removed", f"Target <code>{removed}</code> removed.")
                return await notify("Target Error", "Invalid index.", "WARNING")
            tid = await extract_target_id()
            if tid and tid in targs:
                targs.remove(tid)
                await save_user_db(my_id)
                return await notify("Target Removed", "Target removed successfully.")
            return await notify("Target Error", "Target not found.", "WARNING")

        elif lower_text == "clean targets":
            multi_db[my_id]["targets"] = []
            await save_user_db(my_id)
            return await notify("Targets Cleared", "All targets cleared successfully.")

# ==============================================================================
# CLIENT STARTUP
# ==============================================================================
async def start_single_secondary_client(phone, info):
    clt = None
    try:
        if me_main_id is not None and info.get("user_id") == me_main_id:
            return
        if info.get("string_session"):
            session_obj = StringSession(info["string_session"])
        elif info.get("session_name"):
            session_obj = str(SESSION_DIR / info["session_name"])
        else:
            return

        clt = TelegramClient(
            session_obj, api_id, api_hash,
            connection_retries=20, auto_reconnect=True, timeout=15,
        )
        await clt.connect()
        if not await clt.is_user_authorized():
            await clt.disconnect()
            return
        me_det = await clt.get_me()
        if me_main_id is not None and me_det.id == me_main_id:
            await clt.disconnect()
            return

        running_clients[phone]      = clt
        running_by_id[me_det.id]    = clt
        register_handlers(clt, me_det.id)
        print(f"Client Online: {phone} | ID: {me_det.id}")
    except Exception as e:
        print(f"Error starting {phone}: {e}")
        if clt:
            try: await clt.disconnect()
            except Exception: pass


async def main():
    global me_main_id, bot_client, main_client_global
    await load_global_data()
    asyncio.create_task(anti_shutdown_loop())

    main_client = TelegramClient(
        str(SESSION_DIR / "bot_main_session"), api_id, api_hash,
        connection_retries=20, auto_reconnect=True, timeout=15,
    )
    main_client_global = main_client
    secondary_tasks = []

    try:
        await main_client.start()
        me_main = await main_client.get_me()
        me_main_id = me_main.id
        running_by_id[me_main_id] = main_client
        register_handlers(main_client, me_main_id)
        print(f"Main Client Active | ID: {me_main_id}")

        if BOT_TOKEN:
            try:
                bot_client = TelegramClient(str(SESSION_DIR / "inline_bot_session"), api_id, api_hash)
                await bot_client.start(bot_token=BOT_TOKEN)
                register_bot_handlers(bot_client)
                bot_me = await bot_client.get_me()
                print(f"Inline Bot Active | Username: @{bot_me.username}")
            except Exception as e:
                print(f"Failed to start bot with token: {e}")

        main_path = (SESSION_DIR / "bot_main_session").resolve()
        for phone, info in accounts_data.items():
            if me_main_id is not None and info.get("user_id") == me_main_id:
                continue
            if info.get("session_name") and (SESSION_DIR / info.get("session_name")).resolve() == main_path:
                continue
            secondary_tasks.append(asyncio.create_task(start_single_secondary_client(phone, info)))
            await asyncio.sleep(0.3)

        if bot_client:
            await asyncio.gather(
                main_client.run_until_disconnected(),
                bot_client.run_until_disconnected(),
            )
        else:
            await main_client.run_until_disconnected()

    finally:
        for task in secondary_tasks:
            if not task.done():
                task.cancel()
        if secondary_tasks:
            await asyncio.gather(*secondary_tasks, return_exceptions=True)

        seen = set()
        clients_to_disconnect = list(running_clients.values()) + [main_client]
        if bot_client:
            clients_to_disconnect.append(bot_client)
        for client in clients_to_disconnect:
            if id(client) in seen:
                continue
            seen.add(id(client))
            try: await client.disconnect()
            except Exception: pass


if __name__ == "__main__":
    try:
        asyncio.run(main())
    except (KeyboardInterrupt, SystemExit):
        print("System Stopped.")
