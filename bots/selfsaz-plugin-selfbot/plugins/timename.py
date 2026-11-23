# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions, types
import asyncio
import time
from datetime import datetime
import pytz
import os
import random
from .db_utils import execute_query  # Using your MySQL connection pool & functions

HELP = """
⏰ **نمایش داینامیک زمان در نام / بیو** ⏰

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌ها**:
• نمایش زمان فعلی در نام یا بیو  
• پشتیبانی از چند فونت مختلف و فرمت‌های گوناگون  
• امکان تنظیم منطقه زمانی (پیش‌فرض: تهران)  
• بروزرسانی خودکار در بازه‌های زمانی مشخص  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات فارسی**:

`شروع نام` ➤ شروع نمایش زمان در نام  
`توقف نام` ➤ توقف نمایش زمان در نام  
`فونت نام [0-21|random]` ➤ تغییر فونت زمان برای نام  
`فرمت نام [فرمت]` ➤ تنظیم فرمت نمایش زمان  
`منطقه زمانی نام [منطقه]` ➤ تنظیم منطقه زمانی  
`پیشوند نام [متن]` ➤ افزودن متن پیش از زمان  
`پسوند نام [متن]` ➤ افزودن متن پس از زمان  
`بروزرسانی نام [ثانیه]` ➤ تعیین فاصله بروزرسانی  
`سبک نام [normal|bio]` ➤ نمایش در نام یا بیو  
`وضعیت نام` ➤ مشاهده تنظیمات فعلی  

▬▬▬▬▬▬▬▬▬▬▬▬  
🌐 **دستورات انگلیسی (در صورت نیاز):**

`/timename start` ➤ Start time display  
`/timename stop` ➤ Stop time display  
`/timename font [0-21|random]` ➤ Change font  
`/timename format [format]` ➤ Set format  
`/timename timezone [zone]` ➤ Set timezone  
`/timename prefix [text]` ➤ Add prefix  
`/timename suffix [text]` ➤ Add suffix  
`/timename update [seconds]` ➤ Set update interval  
`/timename style [normal|bio]` ➤ Display in name or bio  
`/timename status` ➤ Show current settings  

▬▬▬▬▬▬▬▬▬▬▬▬  
📆 **نمونه فرمت‌های زمان**:  
`HH:MM` (14:30)  
`hh:mm A` (02:30 PM)  
`HH:MM:SS` (14:30:45)  
`DD/MM HH:MM` (15/03 14:30)  

🌍 **نمونه مناطق زمانی**:  
Asia/Tehran  
Europe/London  
America/New_York
"""


# Font styles for time formatting
FONTS = [
    [":", "𝟶", "𝟷", "𝟸", "𝟹", "𝟺", "𝟻", "𝟼", "𝟽", "𝟾", "𝟿"],
    [":", "𝟬", "𝟭", "𝟮", "𝟯", "𝟰", "𝟱", "𝟲", "𝟳", "𝟴", "𝟵"],
    [":", "０", "１", "２", "３", "４", "５", "６", "７", "８", "９"],
    [":", "₀", "₁", "₂", "₃", "₄", "₅", "₆", "₇", "₈", "₉"],
    [":", "𝟎", "𝟏", "𝟐", "𝟑", "𝟒", "𝟓", "𝟔", "𝟕", "𝟖", "𝟉"],
    [":", "0⃣", "1⃣", "2⃣", "3⃣", "4⃣", "5⃣", "6⃣", "7⃣", "8⃣", "9⃣"],
    [":", "⓪", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨"],
    [":", "０", "１", "２", "３", "４", "５", "６", "７", "８", "９"],
    [":", "⑩", "⑴", "⑵", "⑶", "⑷", "⑸", "⑹", "⑺", "⑻", "⑼"],
    [":", "₀", "₁", "₂", "₃", "₄", "₅", "₆", "₇", "₈", "₉"],
    [":", "⁰", "¹", "²", "³", "⁴", "⁵", "⁶", "⁷", "⁸", "⁹"],
    ["🄃", "🄁", "🄂", "🄃", "🄄", "🄅", "🄆", "🄇", "🄈", "🄉", "🄊"],
    [":", "🄀", "⒈", "⒉", "⒊", "⒋", "⒌", "⒍", "⒎", "⒏", "⒐"],
    [":", "⓪", "➀", "➁", "➂", "➃", "➄", "➅", "➆", "➇", "➈"],
    [":", "⓿", "➊", "➋", "➌", "➍", "➎", "➏", "➐", "➑", "➒"],
    [":", "❶", "❷", "❸", "❹", "❺", "❻", "❼", "❽", "❾", "⓿"],
    [":", "1꣡", "2꣢", "3꣣", "4꣤", "5꣥", "6꣦", "7꣧", "8꣨", "9꣩", "0꣠"],
    [":", "𝟏", "𝟐", "𝟑", "𝟒", "𝟓", "𝟔", "𝟕", "𝟖", "𝟗", "𝟎"],
    [":", "𝟙", "𝟚", "𝟛", "𝟜", "𝟝", "𝟞", "𝟟", "𝟠", "𝟡", "𝟘"],
    [":", "𝟣", "𝟤", "𝟥", "𝟦", "𝟧", "𝟨", "𝟩", "𝟪", "𝟫", "𝟢"],
    [":", "𝟭", "𝟮", "𝟯", "𝟰", "𝟱", "𝟲", "𝟳", "𝟴", "𝟵", "𝟬"],
    [":", "𝟷", "𝟸", "𝟹", "𝟺", "𝟻", "𝟼", "𝟽", "𝟾", "𝟿", "𝟶"],
]

FORMAT_CONVERSIONS = {
    "HH:MM": "%H:%M",
    "hh:mm": "%I:%M",
    "HH:MM:SS": "%H:%M:%S",
    "hh:mm:ss": "%I:%M:%S",
    "hh:mm A": "%I:%M %p",
    "MM/DD HH:MM": "%m/%d %H:%M",
    "DD/MM HH:MM": "%d/%m %H:%M",
}

class TimeNamePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        
        # We do NOT set self.config to defaults here, so we don't overwrite existing user settings.
        self.config = None
        self.task = None
        self.running = False

        # Run setup in sequence (no race conditions).
        asyncio.create_task(self._setup())

    async def _setup(self):
        """Initialize DB, run migration, then load (or insert) config in the correct order."""
        await self._init_db_async()
        await self._migrate_data_async()
        await self._load_config_async()

    async def _init_db_async(self):
        """Asynchronously create timename_config and timename_status tables in MySQL if they don't exist."""
        query_config = """
        CREATE TABLE IF NOT EXISTS timename_config (
            owner_id VARCHAR(255) PRIMARY KEY,
            font_index INT DEFAULT 0,
            time_format VARCHAR(255) DEFAULT 'HH:MM',
            timezone VARCHAR(255) DEFAULT 'Asia/Tehran',
            prefix VARCHAR(255) DEFAULT '',
            suffix VARCHAR(255) DEFAULT '',
            update_interval INT DEFAULT 60,
            display_style VARCHAR(50) DEFAULT 'normal'
        )
        """
        query_status = """
        CREATE TABLE IF NOT EXISTS timename_status (
            owner_id VARCHAR(255) PRIMARY KEY,
            running INT DEFAULT 0
        )
        """
        try:
            await execute_query(query_config, fetch=False)
            await execute_query(query_status, fetch=False)
        except Exception as e:
            print(f"Error initializing timename tables: {e}")

    async def _migrate_data_async(self):
        """Run the migration from the old SQLite database in a separate thread."""
        await asyncio.to_thread(self._migrate_data)

    def _migrate_data(self):
        """
        Migrate existing data from the old SQLite database into MySQL.
        This function migrates data from both the timename_config and timename_status tables.
        Only if 100% of the rows are successfully inserted into MySQL,
        the old SQLite file will be deleted.
        """
        old_db_path = "data/timename.db"
        if not os.path.exists(old_db_path):
            return
        try:
            import sqlite3
            conn = sqlite3.connect(old_db_path)
            cursor = conn.execute(
                "SELECT owner_id, font_index, time_format, timezone, prefix, suffix, update_interval, display_style FROM timename_config"
            )
            config_rows = cursor.fetchall()
            conn.close()

            total_config = len(config_rows)
            migrated_config = 0
            for row in config_rows:
                query = """
                INSERT INTO timename_config (owner_id, font_index, time_format, timezone, prefix, suffix, update_interval, display_style)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE 
                    font_index = VALUES(font_index),
                    time_format = VALUES(time_format),
                    timezone = VALUES(timezone),
                    prefix = VALUES(prefix),
                    suffix = VALUES(suffix),
                    update_interval = VALUES(update_interval),
                    display_style = VALUES(display_style)
                """
                res = execute_query(query, row, fetch=False)
                if res is not None:
                    migrated_config += 1

            # Migrate timename_status if exists
            migrated_status = 0
            total_status = 0
            try:
                conn = sqlite3.connect(old_db_path)
                cursor = conn.execute("SELECT owner_id, running FROM timename_status")
                status_rows = cursor.fetchall()
                conn.close()
                total_status = len(status_rows)
                for row in status_rows:
                    query = """
                    INSERT INTO timename_status (owner_id, running)
                    VALUES (%s, %s)
                    ON DUPLICATE KEY UPDATE running = VALUES(running)
                    """
                    res = execute_query(query, row, fetch=False)
                    if res is not None:
                        migrated_status += 1
            except Exception as e:
                total_status = 0
                migrated_status = 0

            if migrated_config == total_config and migrated_status == total_status:
                os.remove(old_db_path)
                print(f"✅ Migration successful: {migrated_config} config rows and {migrated_status} status rows migrated. Old SQLite DB deleted.")
            else:
                print(f"❌ Migration incomplete: {migrated_config}/{total_config} config rows and {migrated_status}/{total_status} status rows migrated. Old SQLite DB NOT deleted.")
        except Exception as e:
            print(f"Error migrating timename data: {e}")

    async def _load_config_async(self):
        """
        Asynchronously load configuration from MySQL for self.owner_id.
        If no record is found, insert default config.
        """
        query = """
            SELECT font_index, time_format, timezone, prefix, suffix, update_interval, display_style
            FROM timename_config
            WHERE owner_id = %s
        """
        try:
            result = await execute_query(query, (self.owner_id,), fetch=True)
        except Exception as e:
            print(f"Error loading config: {e}")
            result = None

        # If a row exists, set from DB
        if result and len(result) > 0:
            row = result[0]
            self.config = {
                'font_index': row['font_index'],
                'time_format': row['time_format'],
                'timezone': row['timezone'],
                'prefix': row['prefix'],
                'suffix': row['suffix'],
                'update_interval': row['update_interval'],
                'display_style': row['display_style']
            }
        else:
            # No row -> Insert defaults for new user
            self.config = {
                'font_index': 0,
                'time_format': 'HH:MM',
                'timezone': 'Asia/Tehran',
                'prefix': '',
                'suffix': '',
                'update_interval': 60,
                'display_style': 'normal'
            }
            insert_query = """
            INSERT INTO timename_config (owner_id, font_index, time_format, timezone, prefix, suffix, update_interval, display_style)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            try:
                await execute_query(
                    insert_query,
                    (
                        self.owner_id,
                        self.config['font_index'],
                        self.config['time_format'],
                        self.config['timezone'],
                        self.config['prefix'],
                        self.config['suffix'],
                        self.config['update_interval'],
                        self.config['display_style']
                    ),
                    fetch=False
                )
            except Exception as e:
                print(f"Error inserting default config: {e}")

        return self.config

    async def _save_config_async(self):
        """Asynchronously save the current configuration to MySQL."""
        query = """
        UPDATE timename_config
        SET font_index = %s, time_format = %s, timezone = %s, prefix = %s, suffix = %s, update_interval = %s, display_style = %s
        WHERE owner_id = %s
        """
        try:
            await execute_query(
                query,
                (
                    self.config['font_index'],
                    self.config['time_format'],
                    self.config['timezone'],
                    self.config['prefix'],
                    self.config['suffix'],
                    self.config['update_interval'],
                    self.config['display_style'],
                    self.owner_id
                ),
                fetch=False
            )
        except Exception as e:
            print(f"Error saving config: {e}")

    async def _update_running_status_async(self, running: bool):
        """Asynchronously update the running status in MySQL."""
        query = """
        INSERT INTO timename_status (owner_id, running)
        VALUES (%s, %s)
        ON DUPLICATE KEY UPDATE running = %s
        """
        try:
            await execute_query(query, (self.owner_id, 1 if running else 0, 1 if running else 0), fetch=False)
        except Exception as e:
            print(f"Error updating running status: {e}")

    def _get_strftime_format(self, format_string):
        """Convert our friendly label (like 'HH:MM') to a real strftime pattern."""
        if format_string in FORMAT_CONVERSIONS:
            return FORMAT_CONVERSIONS[format_string]
        return format_string

    def _format_time(self, dt, font_index):
        """Format the datetime using the user’s chosen format and font style."""
        strftime_format = self._get_strftime_format(self.config['time_format'])
        time_str = dt.strftime(strftime_format)
        # Some extra logic if needed to handle 24-hour wrap or custom placeholders
        if font_index == -1:
            font_index = random.randint(0, len(FONTS) - 1)
        font = FONTS[font_index]
        for i in range(10):
            time_str = time_str.replace(str(i), font[i+1])
        time_str = time_str.replace(":", font[0])
        return time_str

    async def _update_name(self):
        """Loop that updates the user’s name or bio every minute, until stopped."""
        try:
            while self.running:
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                new_text = f"{self.config['prefix']} {formatted_time} {self.config['suffix']}".strip()
                try:
                    if self.config['display_style'] == 'normal':
                        await self.client(functions.account.UpdateProfileRequest(
                            first_name=new_text
                        ))
                    else:
                        await self.client(functions.account.UpdateProfileRequest(
                            about=new_text
                        ))
                except Exception as e:
                    print(f"Error updating name/bio: {str(e)}")

                # Sleep until the start of the next minute
                now = datetime.now(pytz.timezone(self.config['timezone']))
                delay = 60 - now.second - (now.microsecond / 1_000_000)
                await asyncio.sleep(delay)
        except asyncio.CancelledError:
            pass
        except Exception as e:
            print(f"Time name task error: {str(e)}")

    async def start_time_name(self):
        """Start the background task (if not already running)."""
        if self.running:
            return
        self.running = True
        await self._update_running_status_async(True)
        self.task = asyncio.create_task(self._update_name())

    async def stop_time_name(self):
        """Stop the background task (if it’s running)."""
        if not self.running:
            return
        self.running = False
        await self._update_running_status_async(False)
        if self.task:
            self.task.cancel()
            try:
                await self.task
            except asyncio.CancelledError:
                pass
            self.task = None

    async def handle_events(self):
        """
        Hooks up the Telegram commands/events.
        Call this once after plugin creation to start listening for commands.
        """
        @self.client.on(events.NewMessage(pattern=r'^/timename\s+(.+)'))
        async def timename_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            args = event.pattern_match.group(1).split(' ', 1)
            command = args[0].lower()
            if command == 'start':
                await self.start_time_name()
                await event.reply("✅ Time display activated")
            elif command == 'stop':
                await self.stop_time_name()
                await event.reply("✅ Time display stopped")
            elif command == 'font' and len(args) > 1:
                try:
                    if args[1].lower() == 'random':
                        self.config['font_index'] = -1
                        await self._save_config_async()
                        await event.reply("✅ Random font enabled")
                    else:
                        font_index = int(args[1])
                        if 0 <= font_index < len(FONTS):
                            self.config['font_index'] = font_index
                            await self._save_config_async()
                            now = datetime.now(pytz.timezone(self.config['timezone']))
                            formatted_time = self._format_time(now, font_index)
                            await event.reply(f"✅ Font set to {font_index}\nSample: {formatted_time}")
                        else:
                            await event.reply(f"❌ Font index must be 0-{len(FONTS)-1}")
                except ValueError:
                    await event.reply("❌ Invalid font index")
            elif command == 'format' and len(args) > 1:
                time_format = args[1]
                try:
                    _ = datetime.now().strftime(self._get_strftime_format(time_format))
                    self.config['time_format'] = time_format
                    await self._save_config_async()
                    now = datetime.now(pytz.timezone(self.config['timezone']))
                    formatted_time = self._format_time(now, self.config['font_index'])
                    await event.reply(f"✅ Time format set to {time_format}\nSample: {formatted_time}")
                except Exception as e:
                    await event.reply(f"❌ Invalid time format: {str(e)}")
            elif command == 'timezone' and len(args) > 1:
                timezone = args[1]
                try:
                    pytz.timezone(timezone)
                    self.config['timezone'] = timezone
                    await self._save_config_async()
                    now = datetime.now(pytz.timezone(timezone))
                    formatted_time = self._format_time(now, self.config['font_index'])
                    await event.reply(f"✅ Timezone set to {timezone}\nSample: {formatted_time}")
                except Exception:
                    await event.reply("❌ Invalid timezone")
            elif command == 'prefix' and len(args) > 1:
                prefix = args[1]
                self.config['prefix'] = prefix
                await self._save_config_async()
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{prefix}{formatted_time}{self.config['suffix']}"
                await event.reply(f"✅ Prefix set to '{prefix}'\nSample: {full_sample}")
            elif command == 'suffix' and len(args) > 1:
                suffix = args[1]
                self.config['suffix'] = suffix
                await self._save_config_async()
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{self.config['prefix']}{formatted_time}{suffix}"
                await event.reply(f"✅ Suffix set to '{suffix}'\nSample: {full_sample}")
            elif command == 'update' and len(args) > 1:
                try:
                    interval = int(args[1])
                    if interval < 30:
                        await event.reply("❌ Update interval must be ≥30 seconds")
                    else:
                        self.config['update_interval'] = interval
                        await self._save_config_async()
                        await event.reply(f"✅ Update interval set to {interval} seconds")
                except ValueError:
                    await event.reply("❌ Invalid interval number")
            elif command == 'style' and len(args) > 1:
                style = args[1].lower()
                if style in ['normal', 'bio']:
                    self.config['display_style'] = style
                    await self._save_config_async()
                    display_type = "name" if style == 'normal' else "bio"
                    await event.reply(f"✅ Display style set to {display_type}")
                else:
                    await event.reply("❌ Style must be 'normal' or 'bio'")
            elif command == 'status':
                status = f"⚙️ **Current Settings**:\n\n"
                status += f"• **Status**: {'Active' if self.running else 'Inactive'}\n"
                status += f"• **Font**: {'Random' if self.config['font_index'] == -1 else self.config['font_index']}\n"
                status += f"• **Time Format**: {self.config['time_format']}\n"
                status += f"• **Timezone**: {self.config['timezone']}\n"
                status += f"• **Prefix**: '{self.config['prefix']}'\n"
                status += f"• **Suffix**: '{self.config['suffix']}'\n"
                status += f"• **Update Interval**: {self.config['update_interval']} seconds\n"
                status += f"• **Display Style**: {'Name' if self.config['display_style'] == 'normal' else 'Bio'}\n\n"
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{self.config['prefix']}{formatted_time}{self.config['suffix']}"
                status += f"**Sample**: {full_sample}"
                await event.reply(status)
            elif command == 'help':
                await event.reply(HELP)
            else:
                await event.reply("❌ Invalid command. Use `/timename help`")

        @self.client.on(events.NewMessage(pattern=r'^(شروع نام|توقف نام|فونت نام|فرمت نام|منطقه زمانی نام|پیشوند نام|پسوند نام|بروزرسانی نام|سبک نام|وضعیت نام)\s*(.*)?$'))
        async def farsi_timename_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            command = event.pattern_match.group(1)
            args = event.pattern_match.group(2).strip() if event.pattern_match.group(2) else ""
            if command == "شروع نام":
                await self.start_time_name()
                await event.reply("✅ نمایش زمان در نام فعال شد")
            elif command == "توقف نام":
                await self.stop_time_name()
                await event.reply("✅ نمایش زمان در نام متوقف شد")
            elif command.startswith("فونت نام"):
                try:
                    if args.lower() == 'random':
                        self.config['font_index'] = -1
                        await self._save_config_async()
                        await event.reply("✅ فونت تصادفی فعال شد")
                    else:
                        font_index = int(args)
                        if 0 <= font_index < len(FONTS):
                            self.config['font_index'] = font_index
                            await self._save_config_async()
                            now = datetime.now(pytz.timezone(self.config['timezone']))
                            formatted_time = self._format_time(now, font_index)
                            await event.reply(f"✅ فونت به شماره {font_index} تنظیم شد\nنمونه: {formatted_time}")
                        else:
                            await event.reply(f"❌ شماره فونت باید بین ۰ تا {len(FONTS)-1} باشد")
                except ValueError:
                    await event.reply("❌ شماره فونت نامعتبر است")
            elif command.startswith("فرمت نام"):
                try:
                    _ = datetime.now().strftime(self._get_strftime_format(args))
                    self.config['time_format'] = args
                    await self._save_config_async()
                    now = datetime.now(pytz.timezone(self.config['timezone']))
                    formatted_time = self._format_time(now, self.config['font_index'])
                    await event.reply(f"✅ فرمت زمان در نام به {args} تنظیم شد\nنمونه: {formatted_time}")
                except Exception as e:
                    await event.reply(f"❌ فرمت زمان نامعتبر: {str(e)}")
            elif command.startswith("منطقه زمانی نام"):
                try:
                    pytz.timezone(args)
                    self.config['timezone'] = args
                    await self._save_config_async()
                    now = datetime.now(pytz.timezone(self.config['timezone']))
                    formatted_time = self._format_time(now, self.config['font_index'])
                    await event.reply(f"✅ منطقه زمانی در نام به {args} تنظیم شد\nزمان فعلی: {formatted_time}")
                except Exception:
                    await event.reply("❌ منطقه زمانی نامعتبر است")
            elif command.startswith("پیشوند نام"):
                self.config['prefix'] = args
                await self._save_config_async()
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{args}{formatted_time}{self.config['suffix']}"
                await event.reply(f"✅ پیشوند نام به '{args}' تنظیم شد\nنمونه: {full_sample}")
            elif command.startswith("پسوند نام"):
                self.config['suffix'] = args
                await self._save_config_async()
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{self.config['prefix']}{formatted_time}{args}"
                await event.reply(f"✅ پسوند نام به '{args}' تنظیم شد\nنمونه: {full_sample}")
            elif command.startswith("بروزرسانی نام"):
                try:
                    interval = int(args)
                    if interval < 30:
                        await event.reply("❌ فاصله بروزرسانی باید حداقل ۳۰ ثانیه باشد")
                    else:
                        self.config['update_interval'] = interval
                        await self._save_config_async()
                        await event.reply(f"✅ فاصله بروزرسانی نام به {interval} ثانیه تنظیم شد")
                except ValueError:
                    await event.reply("❌ عدد نامعتبر")
            elif command.startswith("سبک نام"):
                if args in ['normal', 'bio']:
                    self.config['display_style'] = args
                    await self._save_config_async()
                    display_type = "نام کاربری" if args == 'normal' else "بیوگرافی"
                    await event.reply(f"✅ حالت نمایش در نام به {display_type} تنظیم شد")
                else:
                    await event.reply("❌ سبک باید 'normal' یا 'bio' باشد")
            elif command == "وضعیت نام":
                status = f"⚙️ **تنظیمات زمان در نام**:\n\n"
                status += f"• **وضعیت**: {'فعال' if self.running else 'غیرفعال'}\n"
                status += f"• **فونت**: {'تصادفی' if self.config['font_index'] == -1 else self.config['font_index']}\n"
                status += f"• **فرمت زمان**: {self.config['time_format']}\n"
                status += f"• **منطقه زمانی**: {self.config['timezone']}\n"
                status += f"• **پیشوند**: '{self.config['prefix']}'\n"
                status += f"• **پسوند**: '{self.config['suffix']}'\n"
                status += f"• **فاصله بروزرسانی**: {self.config['update_interval']} ثانیه\n\n"
                now = datetime.now(pytz.timezone(self.config['timezone']))
                formatted_time = self._format_time(now, self.config['font_index'])
                full_sample = f"{self.config['prefix']}{formatted_time}{self.config['suffix']}"
                status += f"**نمونه**: {full_sample}"
                await event.reply(status)

    async def start(self):
        """
        Called (for example) once we have a running event loop and want
        to resume previous 'running' status from DB if it was active before.
        """
        try:
            query = "SELECT running FROM timename_status WHERE owner_id = %s"
            result = await execute_query(query, (self.owner_id,), fetch=True)
            if result and result[0]['running'] == 1:
                # Load config if not loaded yet, then start
                if not self.config:
                    await self._load_config_async()
                await self.start_time_name()
                print("✅ Timename auto-started based on previous state.")
            else:
                print("ℹ️ Timename was not active previously.")
        except Exception as e:
            print(f"Error loading timename status on startup: {str(e)}")

    async def stop(self):
        """
        If the plugin is shutting down, we store the status, then actually stop the name updates.
        """
        try:
            query = """
            INSERT INTO timename_status (owner_id, running)
            VALUES (%s, %s)
            ON DUPLICATE KEY UPDATE running = %s
            """
            await execute_query(query, (self.owner_id, 1 if self.running else 0, 1 if self.running else 0), fetch=False)
        except Exception as e:
            print(f"Error saving timename status: {str(e)}")
        await self.stop_time_name()
