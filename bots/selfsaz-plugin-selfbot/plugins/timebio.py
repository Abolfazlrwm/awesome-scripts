# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import asyncio
import random
import pytz
from datetime import datetime, timedelta
from telethon import events
from telethon.tl.functions.account import UpdateProfileRequest
from khayyam import JalaliDatetime

from .base import Plugin
from .db_utils import execute_query

logger = logging.getLogger(__name__)

# Fonts for digit transformation
FONTS = [
    [":", "𝟶", "𝟷", "𝟸", "𝟹", "𝟺", "𝟻", "𝟼", "𝟽", "𝟾", "𝟿"],
    [":", "𝟬", "𝟭", "𝟮", "𝟯", "𝟰", "𝟱", "𝟲", "𝟳", "𝟴", "𝟵"],
    [":", "０", "۱", "۲", "۳", "۴", "۵", "۶", "７", "８", "９"],
    [":", "₀", "₁", "₂", "₃", "₄", "₅", "₆", "₇", "₈", "₉"],
    [":", "𝟎", "𝟏", "𝟐", "𝟑", "𝟒", "𝟓", "𝟔", "𝟕", "𝟖", "𝟗"],
    [":", "⓿", "❶", "❷", "❸", "❹", "❺", "❻", "❼", "❽", "❾"],
    [":", "🄌", "➊", "➋", "➌", "➍", "➎", "➏", "➐", "➑", "➒"],
    [":", "➀", "➁", "➂", "➃", "➄", "➅", "➆", "➇", "➈", "➉"],
    [":", "𝙶", "𝟷", "𝟸", "𝟹", "𝟺", "𝟻", "𝟼", "𝟽", "𝟾", "𝟿"],
]

# Maps user-friendly strings to actual strftime tokens
FORMAT_CONVERSIONS = {
    "HH": "%H",
    "hh": "%I",
    "mm": "%M",
    "ss": "%S",
    "HH:MM": "%H:%M",
    "HH:MM:SS": "%H:%M:%S",
    "hh:mm A": "%I:%M %p",
}

# Persian usage instructions
HELP = """
📌 استفاده از بیو زمان‌دار:

🔹 فعال/غیرفعال‌سازی:
بیو زمان

🔹 افزودن متن جدید:
بیو متن من در حال کار هستم

🔹 نمایش همه متن‌ها:
بیو لیست

🔹 حذف همه متن‌ها:
بیو حذف

🔹 انتخاب فونت:
بیو فونت 0 تا 8 یا random

🔹 فرمت زمان:
بیو فرمت HH:MM یا hh:mm A

🔹 تنظیم منطقه زمانی:
بیو منطقه Asia/Tehran

🔹 وضعیت:
بیو وضعیت
"""

# Default plugin config values
DEFAULTS = {
    'enabled': 0,
    'text': '{time}',
    'font_index': 0,
    'format': 'HH:MM',
    'timezone': 'Asia/Tehran'
}

# An in-memory cache to avoid repeated DB fetches
CONFIG_CACHE = {}

class TimeBioPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.running = False
        self.task = None

    # --------------------------------------------
    #                AUTH CHECK
    # --------------------------------------------
    async def _is_authorized(self, user_id: str) -> bool:
        """
        Same logic as in your aichat.py plugin:
        Only let users in 'authorized_users' table run commands.
        """
        try:
            result = await execute_query(
                "SELECT 1 FROM authorized_users WHERE user_id = %s",
                (user_id,),
                fetch=True
            )
            return bool(result)
        except Exception as e:
            logger.error(f"❌ Authorization check failed: {e}")
            return False
    # --------------------------------------------

    async def _create_table_if_not_exists(self):
        """
        Creates the 'timebio_config' table if it doesn't exist.
        """
        await execute_query("""
            CREATE TABLE IF NOT EXISTS `botdata`.`timebio_config` (
                `user_id` BIGINT PRIMARY KEY,
                `enabled` TINYINT DEFAULT 0,
                `text` TEXT,
                `font_index` INT DEFAULT 0,
                `format` VARCHAR(20) DEFAULT 'HH:MM',
                `timezone` VARCHAR(50) DEFAULT 'Asia/Tehran'
            );
        """, None, fetch=False)

    async def get_config(self):
        # Ensure the table exists BEFORE reading or writing config
        await self._create_table_if_not_exists()

        if self.owner_id in CONFIG_CACHE:
            return CONFIG_CACHE[self.owner_id]

        result = await execute_query(
            "SELECT * FROM botdata.timebio_config WHERE user_id = %s",
            (self.owner_id,), fetch=True
        )

        if result:
            # Use values from database
            config = dict(result[0])
            # Fill in any missing (NULL) columns with defaults
            for key, default_value in DEFAULTS.items():
                if config.get(key) is None:
                    config[key] = default_value
        else:
            # If user row doesn't exist, create it with defaults
            config = DEFAULTS.copy()
            await execute_query("""
                INSERT INTO botdata.timebio_config
                (user_id, enabled, text, font_index, format, timezone)
                VALUES (%s, %s, %s, %s, %s, %s)
            """, (
                self.owner_id,
                config['enabled'],
                config['text'],
                config['font_index'],
                config['format'],
                config['timezone']
            ))

        CONFIG_CACHE[self.owner_id] = config
        return config

    async def save_config(self, config):
        # Update our in-memory cache
        CONFIG_CACHE[self.owner_id] = config

        # Update DB row
        try:
            await execute_query("""
                UPDATE botdata.timebio_config
                SET enabled=%s, text=%s, font_index=%s, format=%s, timezone=%s
                WHERE user_id=%s
            """, (
                config['enabled'],
                config['text'],
                config['font_index'],
                config['format'],
                config['timezone'],
                self.owner_id
            ))
        except Exception as e:
            logger.error(f"Failed to save config: {e}")
            # If update fails, try to insert (in case row was removed)
            try:
                await execute_query("""
                    INSERT INTO botdata.timebio_config
                    (user_id, enabled, text, font_index, format, timezone)
                    VALUES (%s, %s, %s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE
                    enabled=%s, text=%s, font_index=%s, format=%s, timezone=%s
                """, (
                    self.owner_id,
                    config['enabled'],
                    config['text'],
                    config['font_index'],
                    config['format'],
                    config['timezone'],
                    config['enabled'],
                    config['text'],
                    config['font_index'],
                    config['format'],
                    config['timezone']
                ))
            except Exception as e2:
                logger.error(f"Failed to insert config: {e2}")

    def _apply_font(self, raw: str, font_index: int) -> str:
        """Replace digits (0-9) and ':' with chosen or random font style."""
        if font_index == -1:
            # random font
            font = FONTS[random.randint(0, len(FONTS)-1)]
        else:
            idx = font_index % len(FONTS)
            font = FONTS[idx]

        for i in range(10):
            raw = raw.replace(str(i), font[i+1])
        return raw.replace(":", font[0])

    async def _update_loop(self):
        """Repeatedly update the user’s bio every minute with the current time."""
        while self.running:
            try:
                config = await self.get_config()
                now = datetime.now(pytz.timezone(config['timezone']))

                # Convert "HH:MM" -> "%H:%M" etc.
                fmt = FORMAT_CONVERSIONS.get(config['format'], config['format'])
                time_str = now.strftime(fmt)
                time_str = self._apply_font(time_str, config['font_index'])

                # Persian date or standard date
                jdate = JalaliDatetime(now).strftime("%Y/%m/%d")
                gdate = now.strftime("%Y/%m/%d")

                # If 'text' can contain multiple lines separated by "||",
                # pick one at random
                texts = [t.strip() for t in config['text'].split("||") if t.strip()]
                chosen = random.choice(texts) if texts else '{time}'

                final_bio = chosen
                final_bio = final_bio.replace("{time}", time_str)
                final_bio = final_bio.replace("{jdate}", jdate)
                final_bio = final_bio.replace("{date}", gdate)

                # Update the Telegram "about" (bio)
                await self.client(UpdateProfileRequest(about=final_bio))

                # Sleep until the next minute starts
                next_min = now + timedelta(minutes=1)
                next_min = next_min.replace(second=0, microsecond=0)
                await asyncio.sleep((next_min - now).total_seconds())

            except Exception as e:
                logger.error(f"Bio update failed: {e}")
                # If something goes wrong, wait 60s and try again
                await asyncio.sleep(60)

    async def start_bio(self):
        """Called to enable the background updating loop."""
        if not self.running:
            self.running = True
            self.task = asyncio.create_task(self._update_loop())

    async def stop_bio(self):
        """Stop updating the user's bio (and cancel the background task)."""
        self.running = False
        if self.task:
            self.task.cancel()
            try:
                await self.task
            except asyncio.CancelledError:
                pass
            self.task = None

    async def start(self):
        """Called automatically by the system when the bot is loaded."""
        config = await self.get_config()
        if config['enabled']:
            await self.start_bio()

    async def stop(self):
        """Called automatically when the bot is shutting down."""
        await self.stop_bio()

    async def handle_events(self):
        """
        Hook up our Telegram command/event handlers.
        We'll add an auth check for each command:
        1) Must match self.owner_id
        2) Must be in authorized_users
        """
        @self.client.on(events.NewMessage(pattern=r'^بیو زمان$'))
        async def toggle(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            # Auth check
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            config['enabled'] = 0 if config['enabled'] else 1
            await self.save_config(config)

            if config['enabled']:
                await self.start_bio()
                await event.reply("✅ بیو زمان فعال شد.")
            else:
                await self.stop_bio()
                await event.reply("❌ بیو زمان غیرفعال شد.")

        @self.client.on(events.NewMessage(pattern=r'^بیو متن (.+)$'))
        async def add_text(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            new_text = event.pattern_match.group(1).strip()

            # If config['text'] was default '{time}', replace it
            if config['text'] == '{time}':
                config['text'] = new_text
            else:
                # Append using "||" separator
                config['text'] = f"{config['text']} || {new_text}"
            await self.save_config(config)
            await event.reply("✅ متن به لیست بیوها اضافه شد.")

        @self.client.on(events.NewMessage(pattern=r'^بیو لیست$'))
        async def list_texts(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            texts = [t.strip() for t in config['text'].split("||") if t.strip()]
            if texts:
                msg = "📋 لیست بیوها:\n"
                for i, txt in enumerate(texts, 1):
                    msg += f"{i}. {txt}\n"
            else:
                msg = "❌ لیست خالی است."
            await event.reply(msg)

        @self.client.on(events.NewMessage(pattern=r'^بیو حذف$'))
        async def clear(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            config['text'] = '{time}'
            await self.save_config(config)
            await event.reply("✅ تمام متن‌ها حذف شدند. (حالت پیش‌فرض)")

        @self.client.on(events.NewMessage(pattern=r'^بیو فونت (\d+|random)$'))
        async def font_cmd(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            value = event.pattern_match.group(1).strip()
            config = await self.get_config()
            if value.lower() == 'random':
                config['font_index'] = -1
            else:
                config['font_index'] = int(value)
            await self.save_config(config)
            await event.reply("✅ فونت تنظیم شد.")

        @self.client.on(events.NewMessage(pattern=r'^بیو فرمت (.+)$'))
        async def format_set(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            new_format = event.pattern_match.group(1).strip()
            config['format'] = new_format
            await self.save_config(config)
            await event.reply("✅ فرمت زمان تنظیم شد.")

        @self.client.on(events.NewMessage(pattern=r'^بیو منطقه (.+)$'))
        async def timezone_set(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            zone = event.pattern_match.group(1).strip()
            try:
                pytz.timezone(zone)  # Validate
                config = await self.get_config()
                config['timezone'] = zone
                await self.save_config(config)
                await event.reply("✅ منطقه زمانی تنظیم شد.")
            except:
                await event.reply("❌ منطقه زمانی نامعتبر است.")

        @self.client.on(events.NewMessage(pattern=r'^بیو وضعیت$'))
        async def status_cmd(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            config = await self.get_config()
            texts = [t for t in config['text'].split("||") if t.strip()]
            msg = (
                f"🕒 وضعیت بیو زمان:\n\n"
                f"• فعال: {'بله' if config['enabled'] else 'خیر'}\n"
                f"• تعداد متون: {len(texts)}\n"
                f"• فونت: {config['font_index']}\n"
                f"• فرمت: {config['format']}\n"
                f"• منطقه زمانی: {config['timezone']}\n"
            )
            await event.reply(msg)

        @self.client.on(events.NewMessage(pattern=r'^راهنما$'))
        async def help_cmd(event):
            sender_id = str(event.sender_id)
            if sender_id != self.owner_id:
                return
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            await event.reply(HELP)
