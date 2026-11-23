# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
from telethon import events
from telethon.tl.functions.contacts import BlockRequest
from .base import Plugin
from .db_utils import execute_query, init_db_pool  # Now an async function based on aiomysql

HELP = """
🔒 **سیستم قفل پیام خصوصی هوشمند** 🔒

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
  • هشدار خودکار به کاربران ناشناس در پیام خصوصی
  • بلاک خودکار پس از رسیدن به حد مجاز هشدار
  • امکان معاف کردن کاربران خاص از محدودیت
  • تنظیم تعداد هشدارها قبل از بلاک شدن

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

  • **English:**
       `/pvlock on` ➔ فعال‌سازی قفل پیام خصوصی
       `/pvlock off` ➔ غیرفعال‌سازی قفل پیام خصوصی
       `/pvstatus` ➔ نمایش وضعیت فعلی قفل پیام خصوصی
       `/pvexempt [آیدی/یوزرنیم]` ➔ معاف کردن کاربر از محدودیت (با ریپلای یا آیدی)
       `/pvunexempt [آیدی/یوزرنیم]` ➔ حذف معافیت کاربر
       `/exemptlist` ➔ نمایش لیست کاربران معاف شده
       `/setwarnings [عدد]` ➔ تنظیم تعداد هشدارها قبل از بلاک (پیش‌فرض: 3)
       `/resetwarnings [آیدی/یوزرنیم]` ➔ ریست کردن هشدارهای یک کاربر

  • **فارسی (بدون /):**
       `قفل پیوی روشن` ➔ فعال‌سازی قفل پیام خصوصی
       `قفل پیوی خاموش` ➔ غیرفعال‌سازی قفل پیام خصوصی
       `وضعیت پیوی` ➔ نمایش وضعیت فعلی قفل پیام خصوصی
       `معاف پیوی [آیدی/یوزرنیم]` ➔ معاف کردن کاربر از محدودیت (با ریپلای یا آیدی)
       `لغو معاف پیوی [آیدی/یوزرنیم]` ➔ حذف معافیت کاربر
       `لیست معاف پیوی` ➔ نمایش لیست کاربران معاف شده
       `تنظیم هشدار [عدد]` ➔ تنظیم تعداد هشدارها قبل از بلاک
       `ریست هشدار [آیدی/یوزرنیم]` ➔ ریست کردن هشدارهای یک کاربر

▬▬▬▬▬▬▬▬▬▬▬▬
⚠️ **نکات مهم**:
  • هشدارها برای هر کاربر به صورت جداگانه ذخیره می‌شوند
  • کاربران معاف شده هیچ هشداری دریافت نمی‌کنند
  • می‌توانید پیام هشدار را با ویرایش فایل این پلاگین تغییر دهید
"""

class PVLockPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.enabled = False
        self.max_warnings = 3
        self.warning_message = (
            "⚠️ **هشدار**: شما در حال ارسال پیام به یک اکانت خصوصی هستید. "
            "لطفاً از ارسال پیام‌های غیرضروری خودداری کنید.\n\n"
            "این هشدار {current}/{max} است. در صورت ادامه، شما بلاک خواهید شد."
        )
        # In-memory caches for exempt users and user warnings
        self.exempt_cache = set()        # Set of user_ids that are exempted
        self.warnings_cache = dict()       # Mapping of user_id to current warning count
        self.db_initialized = False  # Flag to track if DB is initialized
        
        # We'll initialize the DB but not wait for it here
        # It will be properly awaited in handle_events
        self.init_task = asyncio.create_task(self.start())

    async def start(self):
        """
        Initialize the DB pool, create tables if necessary, load settings and caches.
        Call this method when the bot starts so that settings and caches are preloaded.
        """
        try:
            # Initialize the shared DB pool if not already done
            await init_db_pool()

            # Create tables if they do not exist.
            await self._init_db()

            # Load the settings for this owner.
            await self._load_settings()

            # Preload the exemption list.
            rows = await execute_query(
                "SELECT user_id FROM pvlock_exempt WHERE owner_id = %s",
                (self.owner_id,),
                fetch=True
            )
            self.exempt_cache = {str(row['user_id']) for row in rows} if rows else set()

            # Preload the warnings cache.
            rows = await execute_query(
                "SELECT user_id, warning_count FROM pvlock_warnings WHERE owner_id = %s",
                (self.owner_id,),
                fetch=True
            )
            self.warnings_cache = {str(row['user_id']): row['warning_count'] for row in rows} if rows else {}

            self.db_initialized = True
            print(f"PVLock caches loaded for owner {self.owner_id}. Status: {'Enabled' if self.enabled else 'Disabled'}, Max Warnings: {self.max_warnings}")
        except Exception as e:
            print(f"Error initializing PVLock plugin: {str(e)}")
            # Sleep and retry if there was an error
            await asyncio.sleep(5)
            return await self.start()

    async def _init_db(self):
        """
        Create the required tables in the MySQL database.
        """
        try:
            queries = [
                """
                CREATE TABLE IF NOT EXISTS pvlock_settings (
                    owner_id VARCHAR(255) PRIMARY KEY,
                    enabled TINYINT(1) DEFAULT 0,
                    max_warnings INT DEFAULT 3
                )
                """,
                """
                CREATE TABLE IF NOT EXISTS pvlock_exempt (
                    owner_id VARCHAR(255),
                    user_id VARCHAR(255),
                    PRIMARY KEY (owner_id, user_id)
                )
                """,
                """
                CREATE TABLE IF NOT EXISTS pvlock_warnings (
                    owner_id VARCHAR(255),
                    user_id VARCHAR(255),
                    warning_count INT DEFAULT 0,
                    PRIMARY KEY (owner_id, user_id)
                )
                """
            ]
            for query in queries:
                await execute_query(query, ())
            print("PVLock database tables initialized")
        except Exception as e:
            print(f"Error initializing PVLock database: {str(e)}")
            raise  # Re-raise the exception so we can catch it in start()

    async def _load_settings(self):
        """
        Load settings from the database for this owner.
        If settings are missing, insert default settings.
        """
        try:
            rows = await execute_query(
                "SELECT enabled, max_warnings FROM pvlock_settings WHERE owner_id = %s",
                (self.owner_id,),
                fetch=True
            )
            
            if rows and rows[0]:
                self.enabled = bool(rows[0]['enabled'])
                self.max_warnings = rows[0]['max_warnings']
                print(f"Loaded PVLock settings from DB: enabled={self.enabled}, max_warnings={self.max_warnings}")
            else:
                # Insert default settings if none exist
                await execute_query(
                    "INSERT INTO pvlock_settings (owner_id, enabled, max_warnings) VALUES (%s, %s, %s)",
                    (self.owner_id, 0, 3)
                )
                print(f"Created default PVLock settings for owner {self.owner_id}")
                # Verify settings were inserted
                rows = await execute_query(
                    "SELECT enabled, max_warnings FROM pvlock_settings WHERE owner_id = %s",
                    (self.owner_id,),
                    fetch=True
                )
                if rows and rows[0]:
                    self.enabled = bool(rows[0]['enabled'])
                    self.max_warnings = rows[0]['max_warnings']
                    print(f"Verified PVLock settings: enabled={self.enabled}, max_warnings={self.max_warnings}")
        except Exception as e:
            print(f"Error loading PVLock settings: {str(e)}")
            raise  # Re-raise the exception so we can catch it in start()

    async def _add_exempt(self, user_id):
        """
        Add a user to the exemption list and update the cache.
        """
        await execute_query(
            "INSERT IGNORE INTO pvlock_exempt (owner_id, user_id) VALUES (%s, %s)",
            (self.owner_id, user_id)
        )
        self.exempt_cache.add(user_id)

    async def _remove_exempt(self, user_id):
        """
        Remove a user from the exemption list and update the cache.
        """
        await execute_query(
            "DELETE FROM pvlock_exempt WHERE owner_id = %s AND user_id = %s",
            (self.owner_id, user_id)
        )
        self.exempt_cache.discard(user_id)

    async def _reset_warnings(self, user_id):
        """
        Reset the warning count for a user in both the database and the cache.
        """
        await execute_query(
            "DELETE FROM pvlock_warnings WHERE owner_id = %s AND user_id = %s",
            (self.owner_id, user_id)
        )
        self.warnings_cache.pop(user_id, None)

    async def _increment_warning(self, user_id):
        """
        Increment the warning count for a user, update (or insert) in the DB and cache.
        Returns the new warning count.
        """
        current = self.warnings_cache.get(user_id, 0) + 1
        self.warnings_cache[user_id] = current
        await execute_query(
            """
            INSERT INTO pvlock_warnings (owner_id, user_id, warning_count)
            VALUES (%s, %s, %s)
            ON DUPLICATE KEY UPDATE warning_count = %s
            """,
            (self.owner_id, user_id, current, current)
        )
        return current

    async def _resolve_target(self, event, target):
        """
        Resolve the target user's ID from either a reply or a given argument.
        """
        if event.is_reply:
            reply = await event.get_reply_message()
            return str(reply.sender_id)
        if target:
            if target.isdigit():
                return target
            try:
                entity = await self.client.get_entity(target)
                return str(entity.id)
            except Exception:
                return None
        return None

    async def handle_events(self):
        """
        Register Telethon event handlers for pvlock commands and private messages.
        """
        # Make sure initialization is complete before registering handlers
        if not self.db_initialized:
            try:
                # Wait for the init task if it's still running
                if not self.init_task.done():
                    print("Waiting for PVLock database initialization to complete...")
                    await self.init_task
                else:
                    # If the task is done but initialization failed, try again
                    if not self.db_initialized:
                        print("PVLock initialization incomplete, restarting...")
                        await self.start()
            except Exception as e:
                print(f"Error during PVLock initialization: {str(e)}")
                # If initialization failed, try one more time
                await self.start()

        @self.client.on(events.NewMessage(pattern=r'^(?:/pvlock|قفل\s+پیوی)\s+(on|off|روشن|خاموش)$'))
        async def pvlock_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            option = event.pattern_match.group(1)
            self.enabled = True if option in ("on", "روشن") else False
            try:
                await execute_query(
                    "UPDATE pvlock_settings SET enabled = %s WHERE owner_id = %s",
                    (int(self.enabled), self.owner_id)
                )
                # Verify the update succeeded
                rows = await execute_query(
                    "SELECT enabled FROM pvlock_settings WHERE owner_id = %s",
                    (self.owner_id,),
                    fetch=True
                )
                if rows and rows[0]:
                    db_enabled = bool(rows[0]['enabled'])
                    if db_enabled != self.enabled:
                        print(f"Warning: DB enabled state ({db_enabled}) doesn't match internal state ({self.enabled})")
                        self.enabled = db_enabled
                status = "فعال" if self.enabled else "غیرفعال"
                await event.reply(f"✅ قفل پیام خصوصی {status} شد")
            except Exception as e:
                print(f"Error updating PVLock status: {str(e)}")
                await event.reply(f"❌ خطا در بروزرسانی وضعیت: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/pvstatus|وضعیت\s+پیوی)$'))
        async def pvstatus_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            status = "فعال" if self.enabled else "غیرفعال"
            await event.reply(
                f"🔒 وضعیت قفل پیام خصوصی: {status}\n⚠️ هشدار قبل از بلاک: {self.max_warnings}"
            )

        @self.client.on(events.NewMessage(pattern=r'^(?:/pvexempt|معاف\s+پیوی)(?:\s+(.+))?$'))
        async def pvexempt_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            user_id = await self._resolve_target(event, target)
            if not user_id:
                return await event.reply("❌ کاربر نامعتبر است.")
            await self._add_exempt(user_id)
            await event.reply(f"✅ کاربر {user_id} معاف شد.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/pvunexempt|لغو\s+معاف\s+پیوی)(?:\s+(.+))?$'))
        async def pvunexempt_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            user_id = await self._resolve_target(event, target)
            if not user_id:
                return await event.reply("❌ کاربر نامعتبر است.")
            await self._remove_exempt(user_id)
            await event.reply(f"✅ کاربر {user_id} دیگر معاف نیست.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/exemptlist|لیست\s+معاف\s+پیوی)$'))
        async def exemptlist_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not self.exempt_cache:
                return await event.reply("⚠️ لیست معاف‌ها خالی است.")
            msg = "📋 کاربران معاف:\n\n"
            for user_id in self.exempt_cache:
                try:
                    user = await self.client.get_entity(int(user_id))
                    username = f"@{user.username}" if user.username else ""
                    msg += f"• {user.first_name} {username}\n"
                except Exception:
                    msg += f"• {user_id}\n"
            await event.reply(msg)

        @self.client.on(events.NewMessage(pattern=r'^(?:/setwarnings|تنظیم\s+هشدار)\s+(\d+)$'))
        async def setwarnings_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            count = int(event.pattern_match.group(1))
            if count < 1:
                return await event.reply("❌ هشدار باید حداقل ۱ باشد.")
            self.max_warnings = count
            try:
                await execute_query(
                    "UPDATE pvlock_settings SET max_warnings = %s WHERE owner_id = %s",
                    (count, self.owner_id)
                )
                # Verify the update succeeded
                rows = await execute_query(
                    "SELECT max_warnings FROM pvlock_settings WHERE owner_id = %s",
                    (self.owner_id,),
                    fetch=True
                )
                if rows and rows[0]:
                    db_max_warnings = rows[0]['max_warnings']
                    if db_max_warnings != self.max_warnings:
                        print(f"Warning: DB max_warnings ({db_max_warnings}) doesn't match internal state ({self.max_warnings})")
                        self.max_warnings = db_max_warnings
                await event.reply(f"✅ هشدارها به {count} تغییر یافت.")
            except Exception as e:
                print(f"Error updating max warnings: {str(e)}")
                await event.reply(f"❌ خطا در بروزرسانی تعداد هشدارها: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^(?:/resetwarnings|ریست\s+هشدار)(?:\s+(.+))?$'))
        async def resetwarnings_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            target = event.pattern_match.group(1)
            user_id = await self._resolve_target(event, target)
            if not user_id:
                return await event.reply("❌ کاربر نامعتبر است.")
            await self._reset_warnings(user_id)
            await event.reply(f"✅ هشدارهای {user_id} ریست شد.")

        @self.client.on(events.NewMessage(pattern=r'^(?:/pvlockhelp|راهنمای\s+پیوی)$'))
        async def pvlock_help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

        @self.client.on(events.NewMessage(func=lambda e: e.is_private))
        async def private_message_handler(event):
            # Ignore messages from the owner or if the lock is disabled.
            if not self.enabled or str(event.sender_id) == self.owner_id:
                return

            # Check if the sender is exempt using the in-memory cache.
            if str(event.sender_id) in self.exempt_cache:
                return

            # Increment the warning count in the cache and database.
            current = await self._increment_warning(str(event.sender_id))
            await event.reply(self.warning_message.format(current=current, max=self.max_warnings))

            # Notify the helper bot for control buttons.
            try:
                await self.client.send_message(
                    "@Panel_helperbot",
                    f"/pvlock_alert {event.sender_id} {self.owner_id}"
                )
            except Exception as e:
                if "blocked" in str(e).lower():
                    await self.client.send_message(
                        int(self.owner_id),
                        "❗️برای استفاده از دکمه‌های قفل پیوی، ابتدا @Panel_helperbot را از بلاک خارج کنید."
                    )
                else:
                    print(f"❌ Couldn't notify helper bot: {e}")

            # If warnings exceed the limit, block the user.
            if current >= self.max_warnings:
                try:
                    await self.client(BlockRequest(id=event.sender_id))
                    await event.reply("🚫 شما بلاک شدید.")
                    await self.client.send_message(
                        int(self.owner_id),
                        f"🚫 کاربر {event.sender_id} بلاک شد."
                    )
                except Exception as e:
                    print(f"❌ Block failed: {e}")