# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, Button
from telethon.tl.types import InputMediaPhotoExternal, InputMediaDocumentExternal
import requests
import urllib.parse
import os
from datetime import datetime

HELP = """
📥 دانلودر اینستاگرام 📥

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
📌 معرفی:
این افزونه به شما امکان دانلود پست‌ها، ریلس‌ها، استوری‌ها و پست‌های چندتایی (کاروسل) اینستاگرام را می‌دهد.

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
🎯 دستورات:

**انگلیسی:**
  `/igdl [لینک]`
    • دانلود محتوای اینستاگرام  
    • مثال: /igdl https://www.instagram.com/p/ABC123/

  `/ighelp`
    • نمایش راهنمای افزونه

**فارسی:**
  `دانلود اینستاگرام [لینک]`
    • دانلود محتوای اینستاگرام  
    • مثال: دانلود اینستاگرام https://www.instagram.com/p/ABC123/

  `راهنمای اینستاگرام`
    • نمایش راهنمای افزونه

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""

class InstagramDownloader(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.api_url = "https://api.mr-amiri.ir/api/instagram1.php"
        self.api_key = "PUT-YOUR-KEY-HERE"
        self.owner_id = str(user_id)
        self.max_carousel_items = 10  # حداکثر تعداد آیتم‌های کاروسل

    async def download_instagram(self, url):
        try:
            encoded_url = urllib.parse.quote(url)
            params = {
                'key': self.api_key,
                'type': 'download',
                'url': encoded_url
            }
            
            response = requests.get(self.api_url, params=params, timeout=10)
            data = response.json()
            
            if data.get('ok'):
                return data['result']
            return None
                
        except Exception as e:
            print(f"Error downloading Instagram content: {str(e)}")
            return None

    async def send_as_album(self, event, media_urls, caption):
        """ارسال چند رسانه به صورت آلبوم"""
        try:
            # محدود کردن تعداد رسانه‌ها
            media_urls = media_urls[:self.max_carousel_items]
            
            # ساخت لیست مدیا
            media = []
            for idx, url in enumerate(media_urls):
                if idx == 0:
                    # اولین مدیا با کپشن
                    media.append(InputMediaPhotoExternal(
                        url=url,
                        caption=caption[:1024] if caption else None
                    ))
                else:
                    media.append(InputMediaPhotoExternal(url=url))
            
            await self.client.send_file(
                event.chat_id,
                media,
                reply_to=event.id
            )
            return True
            
        except Exception as e:
            print(f"Error sending media album: {str(e)}")
            return False

    async def handle_events(self):
        # Download command: accepts /igdl or the Farsi alternative "دانلود اینستاگرام" (without a slash)
        @self.client.on(events.NewMessage(pattern=r'^(?:/igdl|دانلود\s+اینستاگرام)(?:\s+(https?://\S+))$'))
        async def igdl_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            url = event.pattern_match.group(1).strip()
            
            if not url.startswith(('https://www.instagram.com/', 'https://instagram.com/')):
                await event.reply("❌ لینک وارد شده معتبر نیست. لطفا یک لینک اینستاگرام وارد کنید.")
                return
            
            processing_msg = await event.reply("🔍 در حال پردازش لینک... لطفا صبر کنید.")
            
            result = await self.download_instagram(url)
            
            if not result:
                await processing_msg.edit("❌ خطا در دریافت محتوا. لطفا مطمئن شوید لینک صحیح است و دوباره امتحان کنید.")
                return
            
            # آماده‌سازی کپشن
            caption = f"📥 محتوای دانلود شده اینستاگرام\n\n"
            if result.get('caption'):
                caption += f"📝 کپشن: {result['caption']}\n\n"
            caption += f"❤️ لایک‌ها: {result.get('likes', 'نامعلوم')}\n"
            caption += f"💬 کامنت‌ها: {result.get('comments', 'نامعلوم')}\n\n"
            
            try:
                # بررسی نوع محتوا
                if result.get('video'):
                    # ویدئو
                    await self.client.send_file(
                        event.chat_id,
                        result['video'],
                        caption=caption,
                        reply_to=event.id,
                        supports_streaming=True
                    )
                elif result.get('carousel') and len(result['carousel']) > 1:
                    # پست کاروسل (چندتایی)
                    await event.reply(f"📸 پست دارای {len(result['carousel'])} رسانه است. در حال دانلود...")
                    
                    # اول سعی می‌کنیم به صورت آلبوم ارسال کنیم
                    success = await self.send_as_album(
                        event,
                        result['carousel'],
                        caption
                    )
                    
                    if not success:
                        # اگر ارسال آلبوم ناموفق بود، تک تک ارسال می‌کنیم
                        await event.reply("⚠️ در حال ارسال تک تک رسانه‌ها...")
                        await self.client.send_file(
                            event.chat_id,
                            result['carousel'][0],
                            caption=caption,
                            reply_to=event.id
                        )
                        for media in result['carousel'][1:self.max_carousel_items]:
                            await self.client.send_file(
                                event.chat_id,
                                media,
                                reply_to=event.id
                            )
                elif result.get('images'):
                    # پست عکس تک
                    await self.client.send_file(
                        event.chat_id,
                        result['images'][0],
                        caption=caption,
                        reply_to=event.id
                    )
                else:
                    await processing_msg.edit("❌ نوع محتوای نامعتبر. لطفا با پشتیبانی تماس بگیرید.")
                    return
                
                await processing_msg.delete()
                
            except Exception as e:
                print(f"Error sending media: {str(e)}")
                await processing_msg.edit("❌ خطا در ارسال محتوا. لطفا دوباره امتحان کنید.")

        # Help command for Instagram: accepts /ighelp or Farsi alternative "راهنمای اینستاگرام" (without slash)
        @self.client.on(events.NewMessage(pattern=r'^(?:/ighelp|راهنمای\s+اینستاگرام)$'))
        async def ighelp_handler(event):
            await event.reply(HELP)

    async def start(self):
        pass

    async def stop(self):
        pass
