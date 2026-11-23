# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types
from telethon.tl.functions.messages import SetTypingRequest
from telethon.errors import FloodWaitError
import logging
import asyncio
from collections import defaultdict
import time

from .db_utils import execute_query

logger = logging.getLogger(__name__)

HELP = """  
⌨️ **مدیریت وضعیت تایپ پیشرفته** ⌨️  
... (help text unchanged) ...
"""

# ------------------ In-Memory Cache ------------------

_ACTION_CACHE = {}
CACHE_TTL_SECONDS = 60  # Cache expiration in seconds

# Blocked chat where no typing actions should be sent
BLOCKED_CHATS = [-1002336804501]

async def get_action_states_cached(user_id: str) -> dict:
    now = time.time()
    cache_entry = _ACTION_CACHE.get(user_id)
    if cache_entry:
        data, expiry = cache_entry
        if now < expiry:
            return data

    # Fetch from DB (escape `call`)
    result = await execute_query(
        "SELECT typing, upload, record, interactive, `call` FROM action_states WHERE user_id = %s LIMIT 1",
        (user_id,),
        fetch=True
    )
    states = result[0] if result else {}
    _ACTION_CACHE[user_id] = (states, now + CACHE_TTL_SECONDS)
    return states

def clear_user_cache(user_id: str):
    _ACTION_CACHE.pop(user_id, None)

def update_cache_directly(user_id: str, states: dict):
    """Update the in-memory cache directly with new states"""
    now = time.time()
    _ACTION_CACHE[user_id] = (states, now + CACHE_TTL_SECONDS)

async def update_action_states(user_id: str, states: dict):
    keys = list(states.keys())
    values = [states[k] for k in keys]
    user_id = str(user_id)

    # Escape 'call' if present
    escaped_keys = [f"`{k}`" if k.lower() == "call" else k for k in keys]

    query = f"""
        INSERT INTO action_states (user_id, {', '.join(escaped_keys)})
        VALUES ({', '.join(['%s'] * (len(keys) + 1))})
        ON DUPLICATE KEY UPDATE {', '.join(f"{k} = VALUES({k})" for k in escaped_keys)}
    """
    await execute_query(query, [user_id] + values)
    
    # Update cache directly with the new states
    update_cache_directly(user_id, states)
    logger.debug(f"Cache updated for user {user_id} with states: {states}")

# ------------------------------------------------------

class TypingPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.me = None

        self.active_timers = defaultdict(lambda: None)
        self.action_indexes = defaultdict(int)
        self.all_actions = {
            'typing': [types.SendMessageTypingAction()],
            'upload': [
                types.SendMessageUploadPhotoAction(progress=0),
                types.SendMessageUploadVideoAction(progress=0),
                types.SendMessageUploadAudioAction(progress=0),
                types.SendMessageUploadDocumentAction(progress=0),
                types.SendMessageUploadRoundAction(progress=0)
            ],
            'record': [
                types.SendMessageRecordVideoAction(),
                types.SendMessageRecordAudioAction(),
                types.SendMessageRecordRoundAction()
            ],
            'interactive': [
                types.SendMessageChooseStickerAction(),
                types.SendMessageGeoLocationAction(),
                types.SendMessageGamePlayAction(),
                types.SendMessageChooseContactAction()
            ],
            'call': [
                types.SpeakingInGroupCallAction()
            ]
        }
        logger.info(f"TypingPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me

    async def ensure_table_exists(self):
        create_table_query = """
        CREATE TABLE IF NOT EXISTS action_states (
            user_id VARCHAR(255) PRIMARY KEY,
            typing BOOLEAN DEFAULT FALSE,
            upload BOOLEAN DEFAULT FALSE,
            record BOOLEAN DEFAULT FALSE,
            interactive BOOLEAN DEFAULT FALSE,
            `call` BOOLEAN DEFAULT FALSE
        );
        """
        try:
            await execute_query(create_table_query)
            logger.info("✅ Ensured action_states table exists")
        except Exception as e:
            logger.error(f"❌ Failed to create action_states table: {str(e)}")

    async def start(self):
        try:
            await self.ensure_table_exists()
            result = await execute_query(
                "SELECT typing, upload, record, interactive, `call` FROM action_states WHERE user_id = %s LIMIT 1",
                (self.owner_id,),
                fetch=True
            )
            states = result[0] if result else {}
            
            # Initialize cache with DB values
            if result:
                update_cache_directly(self.owner_id, states)
                
            if states and any(states.values()):
                logger.info(f"🔁 Loaded previous action states from DB for {self.owner_id}: {states}")
            else:
                logger.info(f"✅ No active action states for {self.owner_id} on startup.")
        except Exception as e:
            logger.error(f"❌ Failed to load action states at startup: {str(e)}")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/(typing|upload|record|interactive|call|allactions)$'))
        async def action_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            try:
                await event.delete()
                command = event.pattern_match.group(1)
                current_states = await get_action_states_cached(self.owner_id)
                new_states = self._toggle_actions(command, current_states)
                
                # Update DB and cache in one operation
                await update_action_states(self.owner_id, new_states)
                
                status = self._get_status_message(new_states)
                confirm_msg = await event.respond(f"🔄 وضعیت عملیات به‌روزرسانی شد:\n{status}")
                await asyncio.sleep(5)
                await confirm_msg.delete()
            except Exception as e:
                logger.error(f"خطا در تغییر وضعیت عملیات: {str(e)}")

        @self.client.on(events.NewMessage(incoming=True))
        async def message_handler(event):
            if self.me and event.sender_id == self.me.id:
                return
                
            chat_id = event.chat_id
            
            # Skip blocked chats
            if chat_id in BLOCKED_CHATS:
                logger.debug(f"Skipping typing actions for blocked chat: {chat_id}")
                return
                
            try:
                states = await get_action_states_cached(self.owner_id)
                if not any(states.values()):
                    return
                
                if self.active_timers[chat_id]:
                    self.active_timers[chat_id].cancel()
                self.active_timers[chat_id] = asyncio.create_task(
                    self.action_sequence(chat_id, states)
                )
            except Exception as e:
                logger.error(f"خطا در message_handler: {str(e)}")

    def _toggle_actions(self, command, current_states):
        action_map = {cat: False for cat in self.all_actions}
        action_map.update(current_states)
        if command == 'allactions':
            new_state = not any(action_map.values())
            for cat in action_map:
                action_map[cat] = new_state
        else:
            current = action_map.get(command, False)
            action_map[command] = not current
            if command != 'call' and action_map[command]:
                action_map['call'] = False
        return action_map

    def _get_status_message(self, states):
        icons = {
            'typing': '✍️',
            'upload': '📤',
            'record': '🎥',
            'interactive': '🎮',
            'call': '📞'
        }
        return '\n'.join([
            f"{icons[cat]} {cat.capitalize()}: {'فعال' if state else 'غیرفعال'}"
            for cat, state in states.items()
        ])

    async def action_sequence(self, chat_id, states):
        # Skip blocked chats
        if chat_id in BLOCKED_CHATS:
            logger.debug(f"Not sending typing actions to blocked chat: {chat_id}")
            return
            
        try:
            active_actions = self._get_active_actions(states)
            if not active_actions:
                return
            for _ in range(7):
                # Get the latest states directly from cache to reflect latest toggles
                current_states = await get_action_states_cached(self.owner_id)
                if not any(current_states.values()):
                    break
                    
                # Refresh active actions based on latest states
                active_actions = self._get_active_actions(current_states)
                if not active_actions:
                    break
                    
                action = active_actions[self.action_indexes[chat_id] % len(active_actions)]
                self.action_indexes[chat_id] += 1
                try:
                    await self.client(SetTypingRequest(chat_id, action))
                    await asyncio.sleep(4)
                except FloodWaitError as e:
                    await asyncio.sleep(e.seconds)
                except Exception as e:
                    logger.error(f"خطا در انجام عملیات: {str(e)}")
                    break
        finally:
            self.active_timers[chat_id] = None
            self.action_indexes[chat_id] = 0

    def _get_active_actions(self, states):
        return [
            action
            for category, enabled in states.items()
            for action in self.all_actions.get(category, [])
            if enabled
        ]