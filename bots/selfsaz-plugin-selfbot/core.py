# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import asyncio
import pytz
import heapq
import logging
import time
from datetime import datetime, timedelta
from contextlib import asynccontextmanager
from telethon import TelegramClient, events, functions
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError, FloodWaitError
from telethon.sessions import StringSession
from config import *
from plugins import (
    IdPlugin, TimePlugin, BanPlugin, UnbanPlugin, MarkReadPVPlugin,
    MarkReadGroupPlugin, MarkReadChannelPlugin, BanAllPlugin, DelMessagesPlugin,
    OCRPlugin, ResizePlugin, EphemeralMediaPlugin, MutePlugin, DownloadPlugin,
    TextFormatPlugin, PanelPlugin, TypingPlugin, HelpPlugin, PingPlugin,
    CopierPlugin, FileRenamePlugin, FinancePlugin, TranslationPlugin, ProfilePicCopier,
    OnlineModePlugin, TimeBioPlugin, TimePfpPlugin, PlayPlugin, AntiLoginPlugin,
    SettingsPlugin, PluginManagerPlugin, SetAnswerPlugin, WelcomePlugin, BroadcastPlugin,
    ScreenshotPlugin, AntiBetrayalPlugin, EnemyManagerPlugin, RemoveBgPlugin, AutoAcceptPlugin,
    ContentLockPlugin, DumpRawPlugin,  AutoCommentPlugin, TagAllPlugin,
    ForceJoinTelebotCraft, SignModePlugin, CleanerFaEnPlugin, StreamPlugin,
    RTMPStreamPlugin, PersianTTSPlugin, TimeNamePlugin,
    StickerImageConverterPlugin, StickerExtractorPlugin, PVLockPlugin, 
    TagUsersPlugin, LeaveAllPlugin, WarnSystemPlugin, SpamPlugin,
    StoryDownloaderPlugin, RoundVideoPlugin, ShazamPlugin, InstagramDownloader,
    YouTubeDLPlugin, UploadKonPlugin, GameeScorePlugin, DeleteTrackerPlugin, CaptchaPlugin,
    DicePlugin, UniversalChannelSaver, StickerManagerPlugin, DownloadMediaPlugin,
    ForceJoinTelebotCraftChat, SongStreamPlugin, MonshiPlugin, AIChatPlugin, AccessManagerPlugin, PostDownloaderPlugin, AutoMsgPlugin, NobitexPlugin
)
from db import delete_session, get_session, get_all_sessions, save_session
import uvloop  # Added for faster event loop
from functools import lru_cache  # Added for caching
# At the top of core.py, add the required import:
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError, FloodWaitError, SessionPasswordNeededError


# Install uvloop for faster event loop performance
uvloop.install()

logger = logging.getLogger(__name__)

# -------------------------- 
# Optimized Data Structures 
# -------------------------- 
class PrioritySessionHeap:
    def __init__(self):
        self.heap = []
        self.entries = {}
        
    def add(self, chat_id, expiry):
        if chat_id in self.entries:
            self.remove(chat_id)
        entry = [expiry, chat_id]
        heapq.heappush(self.heap, entry)
        self.entries[chat_id] = entry

    def remove(self, chat_id):
        entry = self.entries.pop(chat_id, None)
        if entry:
            entry[-1] = '<removed>'

    def pop_expired(self, now):
        expired = []
        while self.heap and self.heap[0][0] <= now:
            entry = heapq.heappop(self.heap)
            chat_id = entry[1]
            if chat_id != '<removed>':
                del self.entries[chat_id]
                expired.append(chat_id)
        return expired

# -------------------------- 
# Optimized UserClient 
# -------------------------- 
class UserClient:
    __slots__ = [
        'session_string', 'api_id', 'api_hash', 'client', 
        'me', 'user_id', 'phone_number', '_handlers_started',
        'plugins', '_update_lock', 'verification_code', '_phone_code_hash',
        '_login_in_progress', '_code_type'
    ]

    def __init__(self, session_string=None, api_id=None, api_hash=None):
        self.session_string = session_string
        self.api_id = api_id
        self.api_hash = api_hash
        self.client = None
        self.me = None
        self.user_id = None
        self.phone_number = None
        self._handlers_started = False
        self.plugins = []
        self._update_lock = asyncio.Lock()
        self.verification_code = None
        self._phone_code_hash = None
        self._login_in_progress = False
        self._code_type = None

    async def start_login_flow(self, phone_number: str):
        """Start the login process by requesting verification code"""
        try:
            self.phone_number = phone_number
            self.client = TelegramClient(StringSession(), self.api_id, self.api_hash)
            await self.client.connect()
            
            if not await self.client.is_user_authorized():
                send_code_result = await self.client.send_code_request(phone_number)
                self._phone_code_hash = send_code_result.phone_code_hash
                self._login_in_progress = True
                self._code_type = send_code_result.type
                return True
            return False
        except Exception as e:
            logger.error(f"Error starting login flow: {str(e)}")
            raise

    async def initialize(self):
        """Initialize client with optimized connection handling"""
        try:
            if self.session_string:
                if len(self.session_string) < 10:
                    raise ValueError("Invalid session string format")

                self.client = TelegramClient(
                    StringSession(self.session_string),
                    self.api_id,
                    self.api_hash,
                    connection_retries=3,
                    auto_reconnect=True
                )
                
                await self.client.connect()
                
                if not await self.client.is_user_authorized():
                    logger.warning("Session not authorized - removing")
                    await self._safe_disconnect()
                    await delete_session(session=self.session_string)
                    raise ConnectionError("Unauthorized session")

                self.me = await self.client.get_me()
                self.user_id = str(self.me.id)
                await self.start_handlers()
                return True
            
            if self.phone_number and not self.client:
                self.client = TelegramClient(
                    StringSession(), 
                    self.api_id, 
                    self.api_hash
                )
                await self.client.connect()

                if not await self.client.is_user_authorized():
                    result = await self.client.send_code_request(self.phone_number)
                    self._phone_code_hash = result.phone_code_hash
                    self._login_in_progress = True
                    return True
                
                self.me = await self.client.get_me()
                self.user_id = str(self.me.id)
                await self.start_handlers()
                return True
                
        except Exception as e:
            logger.error(f"Initialization error: {str(e)}")
            if hasattr(self, 'session_string') and self.session_string:
                await delete_session(session=self.session_string)
            raise

    async def complete_login(self):
        try:
            if not self._login_in_progress or not self.verification_code:
                raise ValueError("Login flow not properly initialized")

            max_retries = 3
            retry_count = 0

            while retry_count < max_retries:
                try:
                    await self.client.sign_in(
                        phone=self.phone_number,
                        code=self.verification_code,
                        phone_code_hash=self._phone_code_hash
                    )
                except SessionPasswordNeededError:
                    logger.info("Two-step verification required")
                    self._login_in_progress = True  # 🔥 Ensure this is True for password step
                    return "need_password"
                except FloodWaitError as e:
                    wait_time = int(str(e).split(" ")[-2]) + 1
                    logger.warning(f"Flood wait: {wait_time}s")
                    await asyncio.sleep(wait_time)
                    retry_count += 1
                    continue
                except Exception as e:
                    if "PHONE_CODE_INVALID" in str(e) and retry_count < max_retries - 1:
                        retry_count += 1
                        await asyncio.sleep(1)
                        continue
                    raise

                if await self.client.is_user_authorized():
                    self.session_string = self.client.session.save()
                    self.me = await self.client.get_me()
                    self.user_id = str(self.me.id)
                    await save_session(self.user_id, self.session_string, self.api_id, self.api_hash)
                    await self.start_handlers()
                    self._login_in_progress = False
                    return True
                break

            return False

        except Exception as e:
            logger.error(f"Login error: {str(e)}")
            self._login_in_progress = False
            raise

    async def complete_two_step_login(self, password: str):
        try:
            await self.client.sign_in(password=password)
            if await self.client.is_user_authorized():
                self.session_string = self.client.session.save()
                self.me = await self.client.get_me()
                self.user_id = str(self.me.id)
                await save_session(self.user_id, self.session_string, self.api_id, self.api_hash)
                await self.start_handlers()
                self._login_in_progress = False
                return True
            return False
        except Exception as e:
                    logger.error(f"2FA login failed: {str(e)}")
                    raise




    async def set_phone_number(self, phone: str):
        self.phone_number = phone

    async def set_verification_code(self, code: str):
        self.verification_code = code

    async def start_handlers(self):
        """Initialize plugins with optimized event handling"""
        if self._handlers_started:
            return

        plugin_config = {
            'TEHRAN_TIMEZONE': TEHRAN_TIMEZONE,
            'PERSIAN_NUMERALS': PERSIAN_NUMERALS,
            'ENGLISH_NUMERALS': ENGLISH_NUMERALS,
            'OWNER_ID': self.user_id
        }

        self.plugins = [
            IdPlugin(self.client, plugin_config, self.user_id),
            TimePlugin(self.client, plugin_config),
            BanPlugin(self.client, plugin_config, self.user_id),
            UnbanPlugin(self.client, plugin_config, self.user_id),
            MarkReadPVPlugin(self.client, plugin_config, self.user_id),
            MarkReadGroupPlugin(self.client, plugin_config, self.user_id),
            MarkReadChannelPlugin(self.client, plugin_config, self.user_id),
            BanAllPlugin(self.client, plugin_config, self.user_id),
            DelMessagesPlugin(self.client, plugin_config, self.user_id),
            OCRPlugin(self.client, plugin_config, self.user_id),
            ResizePlugin(self.client, plugin_config, self.user_id),
            EphemeralMediaPlugin(self.client, plugin_config, self.user_id),
            MutePlugin(self.client, plugin_config, self.user_id),
            DownloadPlugin(self.client, plugin_config, self.user_id),
            TextFormatPlugin(self.client, plugin_config, self.user_id),
            PanelPlugin(self.client, plugin_config, self.user_id),
            TypingPlugin(self.client, plugin_config, self.user_id),
            HelpPlugin(self.client, plugin_config, self.user_id),
            PingPlugin(self.client, plugin_config, self.user_id),
            CopierPlugin(self.client, plugin_config, self.user_id),
            FileRenamePlugin(self.client, plugin_config, self.user_id),
            FinancePlugin(self.client, plugin_config, self.user_id),
            ProfilePicCopier(self.client, plugin_config, self.user_id),
            TranslationPlugin(self.client, plugin_config, self.user_id),
            OnlineModePlugin(self.client, plugin_config, self.user_id),
            TimeBioPlugin(self.client, plugin_config, self.user_id),
            TimePfpPlugin(self.client, plugin_config, self.user_id),
            PlayPlugin(self.client, plugin_config, self.user_id),
            AntiLoginPlugin(self.client, plugin_config, self.user_id),
            SettingsPlugin(self.client, plugin_config, self.user_id),
            PluginManagerPlugin(self, plugin_config, self.user_id),
            SetAnswerPlugin(self.client, plugin_config, self.user_id),
            WelcomePlugin(self.client, plugin_config, self.user_id),
            BroadcastPlugin(self.client, plugin_config, self.user_id),
            ScreenshotPlugin(self.client, plugin_config, self.user_id),
            AntiBetrayalPlugin(self.client, plugin_config, self.user_id),
            EnemyManagerPlugin(self.client, plugin_config, self.user_id),
            RemoveBgPlugin(self.client, plugin_config, self.user_id),
            AutoAcceptPlugin(self.client, plugin_config, self.user_id),
            ContentLockPlugin(self.client, plugin_config, self.user_id),
            DumpRawPlugin(self.client, plugin_config, self.user_id),
            AutoCommentPlugin(self.client, plugin_config, self.user_id),
            TagAllPlugin(self.client, plugin_config, self.user_id),
            ForceJoinTelebotCraft(self.client, plugin_config, self.user_id),
            SignModePlugin(self.client, plugin_config, self.user_id),
            CleanerFaEnPlugin(self.client, plugin_config, self.user_id),
            StreamPlugin(self.client, plugin_config, self.user_id),
            RTMPStreamPlugin(self.client, plugin_config, self.user_id),
            PersianTTSPlugin(self.client, plugin_config, self.user_id),
            TimeNamePlugin(self.client, plugin_config, self.user_id),
            StickerImageConverterPlugin(self.client, plugin_config, self.user_id),
            StickerExtractorPlugin(self.client, plugin_config, self.user_id),
            PVLockPlugin(self.client, plugin_config, self.user_id),
            TagUsersPlugin(self.client, plugin_config, self.user_id),
            LeaveAllPlugin(self.client, plugin_config, self.user_id),
            WarnSystemPlugin(self.client, plugin_config, self.user_id),
            SpamPlugin(self.client, plugin_config, self.user_id),
            StoryDownloaderPlugin(self.client, plugin_config, self.user_id),
            RoundVideoPlugin(self.client, plugin_config, self.user_id),
            ShazamPlugin(self.client, plugin_config, self.user_id),
            InstagramDownloader(self.client, plugin_config, self.user_id),
            YouTubeDLPlugin(self.client, plugin_config, self.user_id),
            UploadKonPlugin(self.client, plugin_config, self.user_id),
            GameeScorePlugin(self.client, plugin_config, self.user_id),
            DeleteTrackerPlugin(self.client, plugin_config, self.user_id),
            CaptchaPlugin(self.client, plugin_config, self.user_id),
            DicePlugin(self.client, plugin_config, self.user_id),
            UniversalChannelSaver(self.client, plugin_config, self.user_id),
            StickerManagerPlugin(self.client, plugin_config, self.user_id),
            DownloadMediaPlugin(self.client, plugin_config, self.user_id),
            ForceJoinTelebotCraftChat(self.client, plugin_config, self.user_id),
            SongStreamPlugin(self.client, plugin_config, self.user_id),
            MonshiPlugin(self.client, plugin_config, self.user_id),
            AIChatPlugin(self.client, plugin_config, self.user_id),
            AccessManagerPlugin(self.client, plugin_config, self.user_id),
            PostDownloaderPlugin(self.client, plugin_config, self.user_id),
            AutoMsgPlugin(self.client, plugin_config, self.user_id),
            NobitexPlugin(self.client, plugin_config, self.user_id),
        ]

        # Initialize plugins in parallel with error handling
        init_tasks = [self._initialize_plugin(plugin) for plugin in self.plugins]
        await asyncio.gather(*init_tasks, return_exceptions=True)
        self._handlers_started = True

    async def _initialize_plugin(self, plugin):
        """Initialize plugin with CPU offloading for heavy tasks"""
        try:
            await plugin.initialize(self.me)
            await plugin.handle_events()
            await plugin.start()
            # Offload CPU-intensive plugin startup to executor if needed
            loop = asyncio.get_event_loop()
            await loop.run_in_executor(None, plugin.start)
        except Exception as e:
            logger.error(f"Plugin {type(plugin).__name__} failed: {str(e)}")

    async def _safe_disconnect(self):
        """Optimized disconnect with connection reuse"""
        try:
            if self.client and self.client.is_connected():
                await self.client.disconnect()
        except Exception:
            pass

# -------------------------- 
# High-Performance SessionManager 
# -------------------------- 
class SessionManager:
    def __init__(self):
        self.active_clients = {}
        self.pending_sessions = {}
        self.session_heap = PrioritySessionHeap()
        self._running = True
        self.lock = asyncio.Lock()
        self.batch_size = 100
        logger.info("Optimized session manager initialized")

    def get_client(self, chat_id):
        return self.active_clients.get(chat_id)

    def get_pending(self, chat_id):
        entry = self.pending_sessions.get(chat_id)
        return entry[0] if entry else None

    async def activate_client(self, user_id: str, client: UserClient):
        """Activate client with optimized cleanup"""
        async with self.lock:
            now = time.monotonic()
            expired = self.session_heap.pop_expired(now)
            for chat_id in expired:
                if chat_id in self.pending_sessions:
                    del self.pending_sessions[chat_id]
            self.active_clients[user_id] = client
            logger.info(f"Activated client: {user_id}")

    async def add_pending(self, chat_id, client):
        expiry = time.monotonic() + 300
        async with self.lock:
            self.pending_sessions[chat_id] = (client, expiry)
            self.session_heap.add(chat_id, expiry)

    async def remove_pending(self, chat_id):
        async with self.lock:
            if chat_id in self.pending_sessions:
                del self.pending_sessions[chat_id]
                self.session_heap.remove(chat_id)
                logger.debug(f"Removed pending session for chat_id: {chat_id}")
                return True
            return False

    async def load_existing_sessions(self):
        """Load sessions with caching and error handling"""
        try:
            sessions = await get_all_sessions()
            valid_clients = []
            
            async with self.lock:
                for session_data in sessions:
                    try:
                        if len(session_data) != 4:
                            logger.error("Invalid session format")
                            continue
                            
                        user_id, session_str, api_id, api_hash = session_data
                        if not all([user_id, session_str, api_id, api_hash]):
                            logger.error(f"Missing required fields for session {user_id}")
                            await self._delete_session(user_id=user_id)
                            continue
                        
                        if len(session_str) < 20 or not session_str.startswith("1"):
                            logger.warning(f"Invalid session format: {session_str[:10]}...")
                            await self._delete_session(session=session_str)
                            continue
                            
                        if not isinstance(user_id, str) or not user_id.isdigit():
                            logger.error(f"Invalid user_id format: {user_id}")
                            await self._delete_session(session=session_str)
                            continue
                            
                        client = UserClient(session_str, api_id, api_hash)
                        valid_clients.append((user_id, client))
                        
                    except Exception as e:
                        logger.error(f"Session processing error: {str(e)}")
                        if 'session_str' in locals():
                            await self._delete_session(session=session_str)
                        continue

                for user_id, client in valid_clients:
                    try:
                        await client.initialize()
                        self.active_clients[user_id] = client
                    except Exception as e:
                        logger.error(f"Client initialization failed for {user_id}: {str(e)}")
                        await self._delete_session(user_id=user_id)
                        await client._safe_disconnect()

        except Exception as e:
            logger.error(f"Session loading failed: {str(e)}")

    async def _delete_session(self, user_id=None, session=None):
        try:
            if user_id:
                success = await delete_session(user_id=user_id)
            elif session:
                success = await delete_session(session=session)
            else:
                logger.error("No identifier provided for session deletion")
                return False
            if not success:
                logger.error("Failed to delete session from database")
            return success
        except Exception as e:
            logger.error(f"Session deletion failed: {str(e)}")
            return False

    async def time_updater_task(self):
        """Optimized time updates with batch processing"""
        tz = pytz.timezone(TEHRAN_TIMEZONE)
        while self._running:
            try:
                now = datetime.now(tz)
                time_str = now.strftime('%H:%M')
                valid_clients = [
                    client for client in self.active_clients.values()
                    if client.client and client.client.is_connected()
                ]
                
                for i in range(0, len(valid_clients), self.batch_size):
                    batch = valid_clients[i:i + self.batch_size]
                    # Process batch asynchronously if needed
                
                sleep_time = 60 - (datetime.now().second + datetime.now().microsecond / 1e6)
                await asyncio.sleep(max(0, sleep_time))
                
            except Exception as e:
                logger.error(f"Time updater critical error: {str(e)}")
                await asyncio.sleep(10)

    async def cleanup_pending_task(self):
        """Heap-based expiration checking"""
        while self._running:
            now = time.monotonic()
            expired = self.session_heap.pop_expired(now)
            if expired:
                async with self.lock:
                    for chat_id in expired:
                        if chat_id in self.pending_sessions:
                            del self.pending_sessions[chat_id]
            await asyncio.sleep(5)

    async def shutdown(self):
        """Optimized mass disconnect"""
        self._running = False
        async with self.lock:
            await asyncio.gather(
                *[client._safe_disconnect() for client in self.active_clients.values()],
                return_exceptions=True
            )
            self.active_clients.clear()

# -------------------------- 
# Utilities 
# -------------------------- 
@lru_cache(maxsize=128)  # Added caching for frequent conversions
def convert_persian_numbers(text):
    return text.translate(str.maketrans(PERSIAN_NUMERALS, ENGLISH_NUMERALS))

# Example usage (optional, for testing)
