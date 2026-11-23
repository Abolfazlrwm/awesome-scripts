# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/rtmp_stream.py
import logging
import asyncio
import os
import shutil
import subprocess
import re
import time
import aiosqlite
from .base import Plugin
from telethon import events, types
from telethon.errors import FloodWaitError
from FastTelethonhelper import fast_download  # Import the fast_download function

logger = logging.getLogger(__name__)

HELP = """
📺 استریمر RTMP تلگرام
▫️ عملکرد اصلی: استریم فایل‌های ویدیویی/صوتی تلگرام به سرورهای RTMP

▫️ دستورات مرتبط:

  • **English:**
       `/rtmp_stream`       ➔ شروع استریم فایل مدیا (ریپلای روی مدیا یا همراه با آپلود)
       `/rtmp_status`       ➔ نمایش وضعیت استریم
       `/rtmp_stop`         ➔ توقف استریم
       `/rtmp_set_url [url]`  ➔ تنظیم آدرس RTMP سرور
       `/rtmp_set_key [key]`  ➔ تنظیم کلید استریم
       `/rtmp_get_config`   ➔ نمایش تنظیمات فعلی RTMP
       `/rtmp_reset_config` ➔ بازگرداندن تنظیمات به حالت پیش‌فرض

  • **فارسی (بدون /):**
       `استریم`            ➔ شروع استریم فایل مدیا
       `وضعیت استریم`      ➔ نمایش وضعیت استریم
       `توقف استریم`        ➔ توقف استریم
       `تنظیم آدرس [url]`   ➔ تنظیم آدرس RTMP سرور
       `تنظیم کلید [key]`   ➔ تنظیم کلید استریم
       `تنظیمات استریم`     ➔ نمایش تنظیمات فعلی RTMP
       `ریست تنظیمات`       ➔ بازگردانی تنظیمات به حالت پیش‌فرض

▫️ راهنمای جامع:
- با ریپلای روی فایل مدیا و دستور `/rtmp_stream` یا `استریم` استریم را شروع کنید
- با دستور `/rtmp_status` یا `وضعیت استریم` وضعیت استریم فعلی را مشاهده کنید
- با دستور `/rtmp_stop` یا `توقف استریم` استریم را متوقف کنید
- با دستور `/rtmp_set_url` یا `تنظیم آدرس [url]` آدرس سرور RTMP را تنظیم کنید
- با دستور `/rtmp_set_key` یا `تنظیم کلید [key]` کلید استریم را تنظیم کنید
- با دستور `/rtmp_get_config` یا `تنظیمات استریم` تنظیمات فعلی را دریافت کنید
- با دستور `/rtmp_reset_config` یا `ریست تنظیمات` تنظیمات را به حالت پیش‌فرض بازگردانید
- حداکثر حجم فایل: ۵۰۰ مگابایت

مثال‌ها:
- "/rtmp_stream" (ریپلای روی فایل ویدیویی) یا "استریم" (ریپلای روی فایل ویدیویی)
- "/rtmp_set_url rtmp://live.twitch.tv/app/" یا "تنظیم آدرس rtmp://live.twitch.tv/app/"
- "/rtmp_set_key your_stream_key_here" یا "تنظیم کلید your_stream_key_here"

⚠️ توجه: دسترسی محدود به کاربر مالک ربات
"""

class RTMPStreamPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        # Default RTMP configuration
        self.default_rtmp_url = "rtmps://dc4-1.rtmp.t.me/s/"
        self.default_stream_key = "2466864112:zwPCG0hk6_ym-T_NsM1AFg"
        # Database path
        self.db_path = "data/rtp.db"
        # Set initial values
        self.rtmp_url = self.default_rtmp_url
        self.stream_key = self.default_stream_key
        self.active_stream = None
        self.ffmpeg_process = None
        self.stream_start_time = None
        logger.info(f"RTMPStreamPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        await self.clean_downloads()  # Clean residual files on startup
        await self.initialize_db()    # Initialize SQLite database
        await self.load_settings()    # Load custom RTMP settings
        logger.info("RTMP streaming plugin initialized")

    async def initialize_db(self):
        """Initialize the SQLite database and create tables if they don't exist"""
        try:
            # Ensure the directory exists
            os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
            
            async with aiosqlite.connect(self.db_path) as db:
                # Create RTMP settings table if it doesn't exist
                await db.execute('''
                    CREATE TABLE IF NOT EXISTS rtmp_settings (
                        id INTEGER PRIMARY KEY,
                        user_id TEXT NOT NULL,
                        rtmp_url TEXT NOT NULL,
                        stream_key TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ''')
                await db.commit()
                logger.info("Database initialized successfully")
        except Exception as e:
            logger.error(f"Database initialization error: {str(e)}")

    async def load_settings(self):
        """Load RTMP settings from SQLite database"""
        try:
            async with aiosqlite.connect(self.db_path) as db:
                # Query settings for the current user
                async with db.execute(
                    "SELECT rtmp_url, stream_key FROM rtmp_settings WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1",
                    (self.owner_id,)
                ) as cursor:
                    row = await cursor.fetchone()
                    if row:
                        self.rtmp_url = row[0]
                        self.stream_key = row[1]
                        logger.info(f"Loaded custom RTMP settings for user {self.owner_id}: URL={self.rtmp_url}")
                    else:
                        # No settings found, use defaults
                        logger.info(f"No custom RTMP settings found for user {self.owner_id}, using defaults")
        except Exception as e:
            logger.error(f"Error loading RTMP settings: {str(e)}")
            # If there's an error, revert to defaults
            self.rtmp_url = self.default_rtmp_url
            self.stream_key = self.default_stream_key

    async def save_settings(self):
        """Save RTMP settings to SQLite database"""
        try:
            async with aiosqlite.connect(self.db_path) as db:
                # Check if settings already exist for this user
                async with db.execute(
                    "SELECT COUNT(*) FROM rtmp_settings WHERE user_id = ?",
                    (self.owner_id,)
                ) as cursor:
                    count = await cursor.fetchone()
                    
                    if count and count[0] > 0:
                        # Update existing settings
                        await db.execute(
                            "UPDATE rtmp_settings SET rtmp_url = ?, stream_key = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?",
                            (self.rtmp_url, self.stream_key, self.owner_id)
                        )
                    else:
                        # Insert new settings
                        await db.execute(
                            "INSERT INTO rtmp_settings (user_id, rtmp_url, stream_key) VALUES (?, ?, ?)",
                            (self.owner_id, self.rtmp_url, self.stream_key)
                        )
                
                await db.commit()
                logger.info(f"Saved custom RTMP settings for user {self.owner_id}: URL={self.rtmp_url}")
                return True
                
        except Exception as e:
            logger.error(f"Error saving RTMP settings: {str(e)}")
            return False

    async def clean_downloads(self):
        """Clean downloads directory using async thread pool"""
        loop = asyncio.get_event_loop()
        if await loop.run_in_executor(None, os.path.exists, 'rtmp_downloads/'):
            await loop.run_in_executor(None, shutil.rmtree, 'rtmp_downloads/')
        await loop.run_in_executor(None, 
            lambda: os.makedirs('rtmp_downloads/', exist_ok=True)
        )

    async def delete_file(self, path: str):
        """Asynchronously delete a file"""
        loop = asyncio.get_event_loop()
        try:
            await loop.run_in_executor(None, os.remove, path)
        except Exception as e:
            logger.error(f"Error deleting file {path}: {str(e)}")

    async def is_media_format(self, file):
        """Check if file is a supported media format"""
        supported_mimetypes = [
            'video/mp4', 'video/x-matroska', 'video/webm', 
            'video/avi', 'video/quicktime', 'video/x-flv',
            'audio/mpeg', 'audio/mp4', 'audio/ogg', 'audio/wav'
        ]
        
        if hasattr(file, 'mime_type'):
            return file.mime_type in supported_mimetypes
        return False

    async def progress_callback(self, current, total, status_msg):
        """Callback for download progress updates"""
        try:
            percent = current * 100 // total
            progress_str = f"📥 دانلود در حال انجام: {percent}% ({current / 1024 / 1024:.2f} MB / {total / 1024 / 1024:.2f} MB)"
            
            # Update status message every 5%
            if current == total or percent % 5 == 0:
                await status_msg.edit(progress_str)
        except Exception as e:
            logger.error(f"Progress callback error: {str(e)}")

    async def start_ffmpeg_stream(self, file_path, status_msg):
        """Start FFmpeg process to stream media to RTMP server"""
        full_rtmp_url = f"{self.rtmp_url}{self.stream_key}"
        
        # Build FFmpeg command based on file type
        file_ext = os.path.splitext(file_path)[1].lower()
        
        if file_ext in ['.mp3', '.wav', '.ogg', '.aac']:
            # Audio file - add a black video stream
            cmd = [
                'ffmpeg', '-re', '-i', file_path, 
                '-f', 'lavfi', '-i', 'color=c=black:s=1280x720:r=30', 
                '-c:v', 'libx264', '-preset', 'veryfast', '-tune', 'zerolatency',
                '-c:a', 'aac', '-ar', '44100', '-b:a', '128k',
                '-shortest', '-pix_fmt', 'yuv420p', '-f', 'flv', 
                full_rtmp_url
            ]
        else:
            # Video file
            cmd = [
                'ffmpeg', '-re', '-i', file_path, 
                '-c:v', 'libx264', '-preset', 'veryfast', '-tune', 'zerolatency',
                '-c:a', 'aac', '-ar', '44100', '-b:a', '128k',
                '-pix_fmt', 'yuv420p', '-f', 'flv', 
                full_rtmp_url
            ]

        # Launch FFmpeg process
        try:
            logger.info(f"Starting FFmpeg with command: {' '.join(cmd)}")
            
            # Create process with pipe for stdout/stderr
            process = await asyncio.create_subprocess_exec(
                *cmd,
                stdout=asyncio.subprocess.PIPE,
                stderr=asyncio.subprocess.PIPE
            )
            
            self.ffmpeg_process = process
            self.stream_start_time = time.time()
            
            # Update status message
            await status_msg.edit("🔴 استریم به سرور RTMP شروع شد!")
            
            # Monitor process in background
            asyncio.create_task(self.monitor_stream(process, status_msg))
            
            return True
            
        except Exception as e:
            logger.error(f"FFmpeg error: {str(e)}")
            await status_msg.edit(f"❌ خطا در شروع استریم: {str(e)}")
            return False

    async def monitor_stream(self, process, status_msg):
        """Monitor FFmpeg process and handle completion or errors"""
        stdout, stderr = await process.communicate()
        
        if process.returncode != 0 and self.active_stream:  # Only log error if not manually stopped
            logger.error(f"FFmpeg process exited with code {process.returncode}")
            logger.error(f"Error output: {stderr.decode('utf-8', errors='replace')}")
            
            try:
                await status_msg.edit(f"❌ استریم با خطا متوقف شد. کد خطا: {process.returncode}")
            except Exception:
                pass
                
        self.ffmpeg_process = None
        
        if self.active_stream:
            self.active_stream = None
            self.stream_start_time = None

    async def stop_stream(self):
        """Stop the active stream"""
        if self.ffmpeg_process:
            try:
                self.ffmpeg_process.terminate()
                try:
                    await asyncio.wait_for(self.ffmpeg_process.wait(), timeout=5.0)
                except asyncio.TimeoutError:
                    self.ffmpeg_process.kill()
            except Exception as e:
                logger.error(f"Error stopping FFmpeg: {str(e)}")
            
            self.ffmpeg_process = None
            
        active_path = self.active_stream
        self.active_stream = None
        self.stream_start_time = None
        
        if active_path and os.path.exists(active_path):
            await self.delete_file(active_path)
            
        return True

    async def get_stream_duration(self):
        """Get current stream duration in seconds"""
        if self.stream_start_time:
            return int(time.time() - self.stream_start_time)
        return 0

    async def format_duration(self, seconds):
        """Format seconds to HH:MM:SS"""
        hours = seconds // 3600
        minutes = (seconds % 3600) // 60
        seconds = seconds % 60
        return f"{hours:02d}:{minutes:02d}:{seconds:02d}"

    # Custom progress bar function for fast_download
    async def progress_bar(self, done, total, status_msg):
        percent = round(done * 100 / total)
        if percent % 5 == 0:
            progress_bar = '▰' * (percent // 10) + '▱' * (10 - (percent // 10))
            await status_msg.edit(f"📥 دانلود در حال انجام: {percent}% |{progress_bar}| ({done / 1024 / 1024:.2f} MB / {total / 1024 / 1024:.2f} MB)")
        return ""

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_stream|استریم)$'))
        async def rtmp_stream_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if self.active_stream:
                await event.reply("❌ یک استریم در حال اجراست. لطفا ابتدا با دستور /rtmp_stop یا 'توقف استریم' آن را متوقف کنید.")
                await event.delete()
                return

            if not event.is_reply:
                await event.reply("❌ لطفاً به یک پیام ویدیویی یا صوتی پاسخ دهید تا استریم شود.")
                await event.delete()
                return

            replied_msg = await event.get_reply_message()
            file = None
            media_path = None

            try:
                if replied_msg.video:
                    file = replied_msg.video
                elif replied_msg.audio:
                    file = replied_msg.audio
                elif replied_msg.document:
                    if await self.is_media_format(replied_msg.document):
                        file = replied_msg.document
                    else:
                        await event.reply("❌ فرمت فایل پشتیبانی نمی‌شود.")
                        await event.delete()
                        return
                else:
                    await event.reply("❌ پیام پاسخ داده شده حاوی فایل مدیا نیست.")
                    await event.delete()
                    return

                if not file:
                    await event.reply("❌ خطا در شناسایی فایل مدیا.")
                    await event.delete()
                    return

                file_size = file.size
                if file_size > 500 * 1024 * 1024:
                    await event.reply("❌ حجم فایل بیش از ۵۰۰ مگابایت است.")
                    await event.delete()
                    return

                status_msg = await event.reply("🔄 در حال دانلود فایل...")
                
                async def progress_func(current, total):
                    percent = round(current * 100 / total)
                    if percent % 5 == 0 or current == total:
                        progress_bar = '▰' * (percent // 10) + '▱' * (10 - (percent // 10))
                        try:
                            await status_msg.edit(f"📥 دانلود در حال انجام: {percent}% |{progress_bar}| ({current / 1024 / 1024:.2f} MB / {total / 1024 / 1024:.2f} MB)")
                        except Exception as e:
                            logger.error(f"Error updating progress: {str(e)}")

                os.makedirs('rtmp_downloads/', exist_ok=True)
                
                media_path = await fast_download(
                    client=self.client,
                    msg=replied_msg,
                    reply=status_msg,
                    download_folder="rtmp_downloads/"
                )

                await status_msg.edit("📥 دانلود انجام شد. در حال شروع استریم...")
                
                success = await self.start_ffmpeg_stream(media_path, status_msg)
                
                if success:
                    self.active_stream = media_path
                else:
                    await self.delete_file(media_path)
                
                await event.delete()

            except Exception as e:
                logger.error(f"RTMP streaming error: {str(e)}")
                await event.reply(f"❌ استریم ناموفق بود: {str(e)}")
                if media_path and os.path.exists(media_path):
                    await self.delete_file(media_path)
                await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_status|وضعیت\s+استریم)$'))
        async def rtmp_status_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not self.active_stream:
                await event.reply("❌ هیچ استریم فعالی وجود ندارد.")
                await event.delete()
                return
                
            duration = await self.get_stream_duration()
            formatted_duration = await self.format_duration(duration)
            
            file_name = os.path.basename(self.active_stream)
            file_size = os.path.getsize(self.active_stream) / (1024 * 1024)
            
            status_message = (
                f"🔴 استریم فعال:\n"
                f"▫️ نام فایل: {file_name}\n"
                f"▫️ حجم فایل: {file_size:.2f} MB\n"
                f"▫️ مدت زمان استریم: {formatted_duration}\n"
                f"▫️ وضعیت: در حال پخش\n"
                f"▫️ سرور RTMP: {self.rtmp_url}"
            )
            
            await event.reply(status_message)
            await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_stop|توقف\s+استریم)$'))
        async def rtmp_stop_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not self.active_stream:
                await event.reply("❌ هیچ استریم فعالی وجود ندارد.")
                await event.delete()
                return
                
            duration = await self.get_stream_duration()
            formatted_duration = await self.format_duration(duration)
            
            success = await self.stop_stream()
            
            if success:
                await event.reply(f"⏹ استریم متوقف شد. مدت زمان کل: {formatted_duration}")
            else:
                await event.reply("❌ خطا در توقف استریم.")
                
            await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_set_url|تنظیم\s+آدرس)\s+(.+)$'))
        async def rtmp_set_url_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            url = event.pattern_match.group(1).strip()
            
            if not url.startswith(('rtmp://', 'rtmps://')):
                await event.reply("❌ فرمت آدرس نامعتبر است. آدرس باید با rtmp:// یا rtmps:// شروع شود.")
                await event.delete()
                return
                
            old_url = self.rtmp_url
            self.rtmp_url = url
            
            if await self.save_settings():
                await event.reply(f"✅ آدرس RTMP با موفقیت تنظیم شد:\n{url}")
            else:
                self.rtmp_url = old_url
                await event.reply("❌ خطا در ذخیره تنظیمات. لطفا دوباره تلاش کنید.")
                
            await event.delete()
                
        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_set_key|تنظیم\s+کلید)\s+(.+)$'))
        async def rtmp_set_key_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            key = event.pattern_match.group(1).strip()
            
            if not key or len(key) < 3:
                await event.reply("❌ کلید استریم خیلی کوتاه است.")
                await event.delete()
                return
                
            old_key = self.stream_key
            self.stream_key = key
            
            if await self.save_settings():
                masked_key = key[:3] + "*" * (len(key) - 6) + key[-3:] if len(key) > 6 else "****"
                await event.reply(f"✅ کلید استریم با موفقیت تنظیم شد:\n{masked_key}")
            else:
                self.stream_key = old_key
                await event.reply("❌ خطا در ذخیره تنظیمات. لطفا دوباره تلاش کنید.")
                
            await event.delete()
            
        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_get_config|تنظیمات\s+استریم)$'))
        async def rtmp_get_config_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            masked_key = self.stream_key[:3] + "*" * (len(self.stream_key) - 6) + self.stream_key[-3:] if len(self.stream_key) > 6 else "****"
            
            config_message = (
                f"📊 تنظیمات RTMP فعلی:\n"
                f"▫️ آدرس RTMP: {self.rtmp_url}\n"
                f"▫️ کلید استریم: {masked_key}\n\n"
                f"برای تغییر تنظیمات از دستورات زیر استفاده کنید:\n"
                f"/rtmp_set_url [آدرس جدید] یا تنظیم آدرس [آدرس جدید]\n"
                f"/rtmp_set_key [کلید جدید] یا تنظیم کلید [کلید جدید]"
            )
            
            await event.reply(config_message)
            await event.delete()
            
        @self.client.on(events.NewMessage(pattern=r'^(?:/rtmp_reset_config|ریست\s+تنظیمات)$'))
        async def rtmp_reset_config_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            self.rtmp_url = self.default_rtmp_url
            self.stream_key = self.default_stream_key
            
            if await self.save_settings():
                await event.reply("✅ تنظیمات RTMP به حالت پیش‌فرض بازگردانده شد.")
            else:
                await event.reply("❌ خطا در بازگردانی تنظیمات. لطفا دوباره تلاش کنید.")
                
            await event.delete()
