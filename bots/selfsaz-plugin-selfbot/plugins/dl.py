# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
import os
import time
import random
from pathlib import Path
from datetime import timedelta
from telethon import events, types
from yt_dlp import YoutubeDL
from FastTelethonhelper import fast_upload, fast_download
from aiohttp import ClientSession
from .base import Plugin
from db import get_dl_usage, record_dl_usage
from .db_utils import execute_query
import psutil

# Maximum duration of video (1 hour)
MAX_DURATION = 3600
MAX_CONCURRENT = 5
USER_COOLDOWN = 840
DAILY_LIMIT = 10
TEMP_DIR = Path("downloads")

# Allowed domains (include SoundCloud)
ALLOWED_DOMAINS = [
    'youtube.com', 'youtu.be', 'vm.tiktok.com',
    'xvideos.com', 'pornhub.com', 'soundcloud.com'
]

# List of user-agent strings for stealth.
USER_AGENTS = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 12_3_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Safari/605.1.15',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36'
]

HELP = """  
📥 **دانلودر پیشرفته ویدیو از پلتفرم‌های مختلف** 📥  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• دانلود از **یوتیوب، تیکتاک، XVideos، PornHub و SoundCloud**  
• پشتیبانی از دانلود به صورت ویدیو یا صوت (`-audio`)  
• نمایش پیشرفت دانلود/آپلود با نوار پیشرفت  
• محدودیت روزانه (۱۰ دانلود در روز)  
• مدیریت خودکار حافظه و پردازنده  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/dl [لینک]` ➔ دانلود ویدیو با بهترین کیفیت  
  `/dl -audio [لینک]` ➔ تبدیل به فایل صوتی (MP3)  

**فارسی:**
  `دانلود [لینک]` ➔ دانلود ویدیو با بهترین کیفیت  
  `دانلود صوتی [لینک]` ➔ تبدیل به فایل صوتی (MP3)  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. ارسال لینک یوتیوب یا SoundCloud:  
   `/dl https://youtu.be/xxxx` یا `دانلود https://soundcloud.com/xxxx`  
2. دانلود به صورت صوت:  
   `/dl -audio https://tiktok.com/xxxx` یا `دانلود صوتی https://tiktok.com/xxxx`  

⚠️ **محدودیت‌ها و نکات**:  
- حداکثر مدت ویدیو: ۱ ساعت  
- فاصله بین دانلودها: ۵ دقیقه  
- حداکثر حجم فایل: ۵۰۰ مگابایت  
- فایل‌ها پس از ۱ ساعت به صورت خودکار حذف می‌شوند  
"""

class DownloadPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.semaphore = asyncio.Semaphore(MAX_CONCURRENT)
        TEMP_DIR.mkdir(exist_ok=True)
        self.cleanup_task = asyncio.create_task(self.cleanup_scheduler())
        self.http_session = None
        self.loop = asyncio.get_event_loop()
        self.last_progress_update = 0
        self.update_interval = 10
        # Cache for metadata to reduce repeated requests.
        self.metadata_cache = {}
        # Cache expiry time (30 minutes)
        self.cache_expiry = 1800

    async def start(self):
        self.http_session = ClientSession()

    async def stop(self):
        if self.http_session:
            await self.http_session.close()

    async def _is_authorized(self, user_id: str) -> bool:
        try:
            result = await execute_query(
                "SELECT 1 FROM authorized_users WHERE user_id = %s",
                (user_id,),
                fetch=True
            )
            return bool(result)
        except Exception as e:
            print(f"❌ Authorization check failed: {e}")
            return False

    def get_stealth_opts(self, url):
        """
        Returns an extensive set of HTTP headers to mimic modern browser requests,
        enhancing stealth without the use of proxies.
        """
        return {
            'user_agent': random.choice(USER_AGENTS),
            'referer': url,
            'accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'accept_language': 'en-US,en;q=0.9',
            'accept_encoding': 'gzip, deflate, br',
            'cache_control': 'no-cache',
            'pragma': 'no-cache',
            'connection': 'keep-alive',
            'upgrade_insecure_requests': '1',
            'dnt': '1',
            'sec-fetch-dest': 'document',
            'sec-fetch-mode': 'navigate',
            'sec-fetch-site': 'none',
            'sec-fetch-user': '?1'
        }

    async def handle_events(self):
        # Use regex pattern to neatly extract an optional option and a URL.
        pattern = r'^(?:/dl|دانلود)(?:\s+(?P<option>-audio|صوتی))?\s+(?P<url>\S+)$'
        @self.client.on(events.NewMessage(pattern=pattern))
        async def download_handler(event):
            # Step 1: Verify that the sender is the owner.
            if str(event.sender_id) != self.owner_id:
                return

            # Step 2: Check authorization.



            # Step 3: Extract the option and URL from the message.
            option = event.pattern_match.group('option')
            if option and option.strip() == "صوتی":
                option = "-audio"
            url = event.pattern_match.group('url').strip()

            # Step 4: Validate that the URL includes an allowed domain.
            if not any(domain in url for domain in ALLOWED_DOMAINS):
                return 
            
            if not await self._is_authorized(str(event.sender_id)):
                    await event.reply(
                        "🚫 سلف شما هنوز ویژه نیست!\n\n"
                        "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                        "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                        "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                        "👉 @VIPSelfsazxBot"
                    )
                    return
            # Step 5: Check user download limits and cooldown.
            usage = await get_dl_usage(self.owner_id)
            if usage['daily'] >= DAILY_LIMIT:
                return await event.reply("❌ محدودیت دانلود روزانه تکمیل شده است")
            if time.time() - usage['last'] < USER_COOLDOWN:
                remaining = int(USER_COOLDOWN - (time.time() - usage['last']))
                return await event.reply(f"⏳ لطفاً {remaining} ثانیه قبل از دانلود بعدی صبر کنید")

            # Step 6: Acquire the semaphore to limit concurrent downloads.
            async with self.semaphore:
                file_path, thumb_path = None, None
                try:
                    await self.check_resources()

                    # Get metadata with caching.
                    meta = await self.get_metadata_cached(url)
                    if not meta:
                        return await event.reply("❌ دریافت اطلاعات ویدیو ناموفق بود")

                    if meta['duration'] > MAX_DURATION:
                        return await event.reply(f"❌ ویدیو خیلی طولانی است (حداکثر {timedelta(seconds=MAX_DURATION)})")

                    msg = await event.reply(f"⏳ شروع دانلود: {meta['title']}")

                    # Step 7: Download content with optimized, stealthy settings.
                    file_path, thumb_path = await self.download_content_optimized(url, meta, option, msg)

                    # Step 8: Send the final result.
                    await self.send_result(event, file_path, thumb_path, meta, msg)
                    await msg.delete()

                    await record_dl_usage(self.owner_id)

                except Exception as e:
                    await event.reply(f"❌ خطا: {str(e)}")
                finally:
                    await self.cleanup_files(file_path, thumb_path)

    async def check_resources(self):
        mem = psutil.virtual_memory()
        if mem.percent > 90:
            raise RuntimeError("مصرف حافظه خیلی بالا است (90%+)")
        if psutil.cpu_percent() > 80:
            raise RuntimeError("مصرف CPU خیلی بالا است (80%+)")

    async def get_metadata_cached(self, url):
        cache_key = url
        current_time = time.time()
        if cache_key in self.metadata_cache:
            cached_data, timestamp = self.metadata_cache[cache_key]
            if current_time - timestamp < self.cache_expiry:
                return cached_data
        metadata = await self.get_metadata(url)
        if metadata:
            self.metadata_cache[cache_key] = (metadata, current_time)
        return metadata

    async def get_metadata(self, url):
        stealth_opts = self.get_stealth_opts(url)
        ydl_opts = {
            'quiet': True,
            'no_warnings': True,
            'skip_download': True,
            'force_generic_extractor': False,
            'extract_flat': True,
            'youtube_include_dash_manifest': False,
            'youtube_include_hls_manifest': False,
            'ignoreerrors': True,
            'socket_timeout': 10,
            'retries': 1,
            'nooverwrites': True,
            'noplaylist': True,
        }
        ydl_opts.update(stealth_opts)
        with YoutubeDL(ydl_opts) as ydl:
            try:
                info = ydl.extract_info(url, download=False)
                return {
                    'title': info.get('title', 'بدون عنوان'),
                    'duration': info.get('duration', 0),
                    'webpage_url': info.get('webpage_url', url),
                    'thumbnail': info.get('thumbnail'),
                    'uploader': info.get('uploader', 'ناشناس'),
                    'extractor': info.get('extractor_key', 'ناشناس')
                }
            except Exception as e:
                print(f"خطای متادیتا: {str(e)}")
                return None

    async def download_content_optimized(self, url, meta, option, msg):
        # Force audio-only for SoundCloud URLs.
        if "soundcloud.com" in url:
            format_selector = 'bestaudio/best'
        else:
            format_selector = ('bestaudio/best' if option == '-audio'
                               else 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/best[ext=mp4]/best')
                               
        stealth_opts = self.get_stealth_opts(url)
        ydl_opts = {
            'format': format_selector,
            'outtmpl': str(TEMP_DIR / '%(id)s.%(ext)s'),
            'writethumbnail': True,
            'postprocessors': (
                [
                    {'key': 'FFmpegMetadata'},
                    {'key': 'EmbedThumbnail'},
                    {'key': 'FFmpegVideoConvertor', 'preferedformat': 'mp4'}
                ] if option != '-audio' else [
                    {'key': 'FFmpegMetadata'},
                    {'key': 'EmbedThumbnail'}
                ]
            ),
            'max_filesize': 500_000_000,
            'noplaylist': True,
            'progress_hooks': [lambda d: self.update_progress(d, msg, "در حال دانلود")],
            'concurrent_fragment_downloads': 1,
            'retries': 1,
            'fragment_retries': 1,
            'skip_unavailable_fragments': True,
            'hls_use_mpegts': True,
            'external_downloader': 'ffmpeg',
            'ffmpeg_location': '/usr/bin/ffmpeg',
            'external_downloader_args': ['-threads', '2'],
            'youtube_include_dash_manifest': False,
            'youtube_include_hls_manifest': False,
            'ignoreerrors': True,
            'socket_timeout': 30,
            'http_chunk_size': 10485760,
            'geo_bypass': True,
            'geo_bypass_country': 'US',
            'nocheckcertificate': True,
        }
        ydl_opts.update(stealth_opts)
        try:
            await msg.edit(f"⏳ دانلود آغاز شد: {meta['title']}")
            with YoutubeDL(ydl_opts) as ydl:
                info = await asyncio.to_thread(ydl.extract_info, url, download=True)
                file_path = TEMP_DIR / f"{info['id']}.mp4"
                thumb_path = TEMP_DIR / f"{info['id']}.jpg"
                if meta['thumbnail'] and not thumb_path.exists():
                    await self.download_thumbnail(meta['thumbnail'], thumb_path)
                await msg.edit(f"✅ دانلود تکمیل شد: {meta['title']}\n⏳ در حال آپلود...")
                return file_path, thumb_path
        except Exception as e:
            print(f"دانلود ناموفق: {str(e)}")
            raise RuntimeError(f"دانلود ناموفق: {str(e)}")

    async def download_thumbnail(self, url, path):
        try:
            if not path.exists():
                async with self.http_session.get(url, timeout=10) as response:
                    if response.status == 200:
                        with open(path, 'wb') as f:
                            f.write(await response.read())
        except Exception as e:
            print(f"دانلود تصویر کوچک ناموفق بود: {str(e)}")

    async def send_result(self, event, file_path, thumb_path, meta, msg):
        caption = (
            f"🎬 **{meta['title']}**\n"
            f"⏱️ {timedelta(seconds=meta['duration'])}\n"
            f"📤 {meta['uploader']}\n"
            f"🔗 [منبع]({meta['webpage_url']})"
        )
        try:
            await msg.edit(f"⏳ آپلود شروع شد: {meta['title']}")
            uploaded_file = await fast_upload(
                client=event.client,
                file_location=file_path,
                reply=msg,
                name=file_path.name,
                progress_bar_function=lambda d, t: self.create_progress_string(d, t, "در حال آپلود")
            )
            uploaded_thumb = None
            if thumb_path and thumb_path.exists():
                uploaded_thumb = await fast_upload(
                    client=event.client,
                    file_location=thumb_path,
                    name=thumb_path.name
                )
            await event.client.send_file(
                event.chat_id,
                file=uploaded_file,
                caption=caption,
                thumb=uploaded_thumb,
                attributes=[
                    types.DocumentAttributeVideo(
                        duration=meta['duration'],
                        w=0,
                        h=0,
                        supports_streaming=True
                    )
                ] if not str(file_path).endswith('.mp3') else None,
                reply_to=event.id
            )
        except Exception as e:
            await event.reply(f"❌ آپلود ناموفق بود: {str(e)}")
            raise

    def update_progress(self, data, msg, stage):
        current_time = time.time()
        if current_time - self.last_progress_update < self.update_interval:
            return
        self.last_progress_update = current_time
        done = data.get('downloaded_bytes', 0)
        total = data.get('total_bytes', 1)
        if done > 0 and total > 0:
            percent = (done / total) * 100
            progress_bar = self.create_progress_bar(percent)
            asyncio.run_coroutine_threadsafe(
                msg.edit(f"⏳ {stage}...\n{progress_bar} {percent:.1f}%"),
                self.loop
            )

    def create_progress_string(self, done, total, stage):
        current_time = time.time()
        if current_time - self.last_progress_update < self.update_interval:
            return None
        self.last_progress_update = current_time
        percent = (done / total) * 100
        progress_bar = self.create_progress_bar(percent)
        return f"⏳ {stage}...\n{progress_bar} {percent:.1f}%"

    def create_progress_bar(self, percent):
        bar_length = 20
        filled = int(bar_length * percent / 100)
        return "[" + "=" * filled + " " * (bar_length - filled) + "]"

    async def validate_url(self, url):
        return any(domain in url for domain in ALLOWED_DOMAINS)

    async def cleanup_files(self, *paths):
        for path in paths:
            try:
                if path and path.exists():
                    os.remove(path)
            except Exception as e:
                print(f"خطای پاکسازی: {str(e)}")

    async def cleanup_scheduler(self):
        while True:
            await asyncio.sleep(3600)
            try:
                for file in TEMP_DIR.glob('*'):
                    if file.stat().st_mtime < (time.time() - 3600):
                        await self.cleanup_files(file)
                current_time = time.time()
                expired_keys = [
                    k for k, (_, timestamp) in self.metadata_cache.items()
                    if current_time - timestamp > self.cache_expiry
                ]
                for key in expired_keys:
                    del self.metadata_cache[key]
            except Exception as e:
                print(f"خطای زمان‌بندی پاکسازی: {str(e)}")

    def __del__(self):
        if self.cleanup_task:
            self.cleanup_task.cancel()
