# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import os
import logging
import tempfile
import shutil
from shazamio import Shazam
from pydub import AudioSegment
import time

logger = logging.getLogger(__name__)

HELP = """
🎵 **پلاگین شناسایی آهنگ** 🎵

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌ها**:
- شناسایی آهنگ از پیام‌های صوتی/تصویری
- دریافت اطلاعات کامل آهنگ
- نمایش لینک‌های استریم
- پشتیبانی از فرمت‌های MP3، MP4 و پیام‌های صوتی

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

  • **English:**
       `/shazam` - پاسخ به مدیا
       `/shazam` - ارسال همراه با مدیا

  • **فارسی (بدون /):**
       `شازم` - پاسخ به مدیا
       `شازم` - ارسال همراه با مدیا

⚠️ **محدودیت‌ها**:
- حداکثر مدت زمان: 60 ثانیه
- بهترین عملکرد با صدای واضح
"""

class ShazamPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.shazam = Shazam()
        self.owner_id = str(user_id)
        
        # ایجاد دایرکتوری موقت با مدیریت خطا
        try:
            self.temp_dir = tempfile.mkdtemp(prefix="shazam_")
            logger.info(f"پلاگین Shazam با دایرکتوری موقت راه‌اندازی شد: {self.temp_dir}")
        except Exception as e:
            logger.error(f"خطا در ایجاد دایرکتوری موقت: {str(e)}")
            self.temp_dir = None

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/shazam|شازم)(?:@\w+)?$'))
        async def handler(event):
            # بررسی مالک بودن ارسال کننده
            if str(event.sender_id) != self.owner_id:
                return
                
            # بررسی وجود دایرکتوری موقت
            if not self.temp_dir or not os.path.exists(self.temp_dir):
                await event.reply("❌ خطا در راه‌اندازی پلاگین. لطفا لاگ‌ها را بررسی کنید.")
                return

            processing_msg = await event.reply("🔍 در حال تحلیل صدا...")
            
            try:
                # دریافت مدیا از پیام یا پاسخ
                media = await self._get_media(event)
                if not media:
                    await processing_msg.edit("❌ مدیایی یافت نشد. به یک پیام صوتی/تصویری پاسخ دهید.")
                    return
                
                # ایجاد نام فایل منحصر به فرد
                file_name = f"audio_{int(time.time())}"
                downloaded_path = os.path.join(self.temp_dir, file_name)
                
                # دانلود مدیا با لاگ‌گیری دقیق
                logger.info(f"در حال دانلود مدیا به مسیر {downloaded_path}")
                downloaded_file = await self.client.download_media(media, downloaded_path)
                
                if not downloaded_file or not os.path.exists(downloaded_file):
                    logger.error(f"دانلود ناموفق. مسیر: {downloaded_path}")
                    await processing_msg.edit("❌ دانلود مدیا ناموفق بود. لطفا مجددا تلاش کنید.")
                    return
                
                logger.info(f"مدیا با موفقیت به مسیر {downloaded_file} دانلود شد")
                
                # تبدیل به MP3
                mp3_path = os.path.join(self.temp_dir, f"{file_name}.mp3")
                audio = AudioSegment.from_file(downloaded_file)
                audio = audio[:60000]  # محدودیت 60 ثانیه
                audio.export(mp3_path, format="mp3")
                
                # شناسایی آهنگ
                logger.info(f"در حال شناسایی آهنگ از {mp3_path}")
                result = await self.shazam.recognize_song(mp3_path)
                
                # بررسی شناسایی آهنگ
                if not result or 'track' not in result:
                    await processing_msg.edit("❌ آهنگی شناسایی نشد. لطفا با یک کلیپ صوتی دیگر امتحان کنید.")
                    return
                
                # فرمت‌دهی و ارسال پاسخ
                response = self._format_response(result)
                await processing_msg.edit(response)
                
            except Exception as e:
                logger.error(f"خطا در پردازش درخواست: {str(e)}", exc_info=True)
                await processing_msg.edit(f"❌ خطا: {str(e)}")
            
            finally:
                # پاکسازی فایل‌های موقت
                try:
                    for file in os.listdir(self.temp_dir):
                        if file.startswith(file_name):
                            os.remove(os.path.join(self.temp_dir, file))
                except Exception as e:
                    logger.error(f"خطا در پاکسازی: {str(e)}")

    async def _get_media(self, event):
        """استخراج مدیا از پیام یا پاسخ"""
        if event.is_reply:
            reply = await event.get_reply_message()
            return reply.media
        return event.media

    def _format_response(self, result):
        """فرمت‌دهی پاسخ Shazam"""
        track = result.get('track', {})
        
        # دریافت متادیتا
        title = track.get('title', 'عنوان نامعلوم')
        artist = track.get('subtitle', 'هنرمند نامعلوم')
        
        # دریافت آلبوم و تاریخ انتشار
        album = "نامعلوم"
        release_date = "نامعلوم"
        
        if 'sections' in track and track['sections']:
            metadata = track['sections'][0].get('metadata', [])
            if len(metadata) > 0:
                album = metadata[0].get('text', 'نامعلوم')
            if len(metadata) > 1:
                release_date = metadata[1].get('text', 'نامعلوم')
        
        # ساخت پاسخ
        sections = [
            f"🎶 **{title}**",
            f"👤 **هنرمند**: {artist}",
            f"💿 **آلبوم**: {album}",
            f"📅 **تاریخ انتشار**: {release_date}"
        ]
        
        # افزودن لینک‌های استریم
        if 'hub' in track:
            for action in track['hub'].get('actions', []):
                if action.get('type') == 'applemusic':
                    sections.append(f"🍎 [Apple Music]({action.get('uri', '#')})")
                elif action.get('type') == 'spotify':
                    sections.append(f"🎧 [Spotify]({action.get('uri', '#')})")
        
        # افزودن تصویر آلبوم در صورت وجود
        if 'images' in track and track['images'].get('coverarthq'):
            sections.append(f"🖼️ [تصویر آلبوم]({track['images']['coverarthq']})")
        
        return "\n".join(sections)
