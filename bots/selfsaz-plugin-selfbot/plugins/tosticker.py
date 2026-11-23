# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import os
import asyncio
from PIL import Image
import tempfile
import logging
from telethon.tl.types import DocumentAttributeSticker, InputStickerSetEmpty
import time
import shutil

HELP = """
🔄 **تبدیل کننده استیکر و تصویر** 🔄

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **امکانات اصلی**:  
• تبدیل استیکر به تصویر با فرمت PNG  
• تبدیل تصویر به استیکر با فرمت WebP  
• پشتیبانی از استیکرهای متحرک  
• دستورات ساده و سریع  

🎯 **دستورات فارسی**:  
`تبدیل به عکس` - تبدیل استیکر به عکس (ریپلای روی استیکر)  
`تبدیل به استیکر` - تبدیل عکس به استیکر (ریپلای روی عکس)  

✨ **نحوه استفاده**:  
1. روی یک استیکر یا عکس ریپلای کنید  
2. دستور مناسب را ارسال کنید  
3. فایل تبدیل شده را دریافت نمایید!  

⚠️ **ملاحظات**:  
• حداکثر حجم فایل: ۵ مگابایت  
• استیکرهای متحرک به عکس ثابت تبدیل می‌شوند  
• کیفیت عکس بر نتیجه استیکر تأثیر می‌گذارد
"""

class StickerImageConverterPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.temp_dir = os.path.join(tempfile.gettempdir(), f"sticker_converter_{user_id}")
        os.makedirs(self.temp_dir, exist_ok=True)
        logging.info(f"StickerImageConverter: Temp directory created at {self.temp_dir}")

    async def handle_events(self):
        # English commands (kept for backward compatibility)
        @self.client.on(events.NewMessage(pattern=r'^/toimg$'))
        async def english_convert_sticker_to_image(event):
            await self._convert_sticker_to_image(event)

        @self.client.on(events.NewMessage(pattern=r'^/tosticker$'))
        async def english_convert_image_to_sticker(event):
            await self._convert_image_to_sticker(event)

        # Farsi commands
        @self.client.on(events.NewMessage(pattern=r'^تبدیل به عکس$'))
        async def farsi_convert_sticker_to_image(event):
            await self._convert_sticker_to_image(event)

        @self.client.on(events.NewMessage(pattern=r'^تبدیل به استیکر$'))
        async def farsi_convert_image_to_sticker(event):
            await self._convert_image_to_sticker(event)

        @self.client.on(events.NewMessage(pattern=r'^/help$'))
        async def help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

    async def _convert_sticker_to_image(self, event):
        if str(event.sender_id) != self.owner_id:
            return

        if not event.is_reply:
            await event.reply("❌ لطفاً روی یک استیکر ریپلای کنید")
            return

        reply_message = await event.get_reply_message()
        if not reply_message.sticker:
            await event.reply("❌ این پیام حاوی استیکر نیست")
            return

        status_message = await event.reply("⏳ در حال تبدیل استیکر به تصویر...")
        
        try:
            timestamp = int(time.time())
            unique_id = f"{timestamp}_{event.id}"
            output_path = os.path.join(self.temp_dir, f"image_{unique_id}.png")
            
            if reply_message.sticker.mime_type == "application/x-tgsticker":
                sticker_path = os.path.join(self.temp_dir, f"sticker_{unique_id}.tgs")
                await reply_message.download_media(sticker_path)
                
                converted = False
                
                if hasattr(reply_message.sticker, 'thumbs') and reply_message.sticker.thumbs:
                    try:
                        thumb_path = os.path.join(self.temp_dir, f"thumb_{unique_id}.jpg")
                        await self.client.download_media(
                            reply_message.sticker,
                            thumb_path,
                            thumb=0
                        )
                        
                        if os.path.exists(thumb_path) and os.path.getsize(thumb_path) > 0:
                            with Image.open(thumb_path) as img:
                                img.save(output_path, "PNG")
                            os.remove(thumb_path)
                            converted = True
                    except Exception as e:
                        logging.warning(f"خطا در استخراج تصویر کوچک: {e}")
                
                if not converted and shutil.which("lottie_convert.py"):
                    try:
                        temp_png = os.path.join(self.temp_dir, f"temp_{unique_id}.png")
                        conversion_cmd = f"lottie_convert.py {sticker_path} {temp_png}"
                        process = await asyncio.create_subprocess_shell(
                            conversion_cmd,
                            stdout=asyncio.subprocess.PIPE,
                            stderr=asyncio.subprocess.PIPE
                        )
                        await process.communicate()
                        
                        if os.path.exists(temp_png):
                            shutil.move(temp_png, output_path)
                            converted = True
                    except Exception as e:
                        logging.warning(f"خطا در تبدیل خارجی: {e}")
                
                if not converted:
                    img = Image.new('RGBA', (512, 512), color=(255, 255, 255, 255))
                    img.save(output_path, "PNG")
                    converted = True
                    
                if os.path.exists(sticker_path):
                    os.remove(sticker_path)
            
            else:
                sticker_path = await reply_message.download_media(self.temp_dir)
                
                try:
                    with Image.open(sticker_path) as img:
                        if img.mode != 'RGBA':
                            img = img.convert('RGBA')
                        img.save(output_path, "PNG")
                except Exception as e:
                    logging.error(f"خطا در تبدیل تصویر: {e}")
                    await status_message.edit("❌ خطا در پردازش تصویر")
                    return
                finally:
                    if os.path.exists(sticker_path):
                        os.remove(sticker_path)
            
            if os.path.exists(output_path) and os.path.getsize(output_path) > 0:
                await asyncio.sleep(0.5)
                await self.client.send_file(
                    event.chat_id,
                    output_path,
                    reply_to=reply_message.id,
                    caption="🖼️ استیکر تبدیل شده به تصویر"
                )
                await status_message.delete()
            else:
                await status_message.edit("❌ خطا در ایجاد فایل تصویر")
            
        except Exception as e:
            logging.error(f"خطا در تبدیل استیکر: {str(e)}")
            await status_message.edit(f"❌ خطا در تبدیل استیکر")
        finally:
            if os.path.exists(output_path):
                try:
                    os.remove(output_path)
                except:
                    pass

    async def _convert_image_to_sticker(self, event):
        if str(event.sender_id) != self.owner_id:
            return

        if not event.is_reply:
            await event.reply("❌ لطفاً روی یک تصویر ریپلای کنید")
            return

        reply_message = await event.get_reply_message()
        if not reply_message.photo and not (reply_message.document and reply_message.document.mime_type and reply_message.document.mime_type.startswith('image/')):
            await event.reply("❌ این پیام حاوی تصویر نیست")
            return

        try:
            status_message = await event.reply("⏳ در حال تبدیل تصویر به استیکر...")
            
            timestamp = int(time.time())
            unique_id = f"{timestamp}_{event.id}"
            file_path = os.path.join(self.temp_dir, f"input_{unique_id}")
            output_path = os.path.join(self.temp_dir, f"sticker_{unique_id}.webp")
            
            file_path = await reply_message.download_media(file_path)
            
            if not os.path.exists(file_path):
                await status_message.edit("❌ خطا در دریافت تصویر")
                return
            
            try:
                with Image.open(file_path) as img:
                    if img.mode != 'RGBA':
                        img = img.convert('RGBA')
                    
                    max_size = 512
                    width, height = img.size
                    
                    if width > max_size or height > max_size:
                        ratio = min(max_size/width, max_size/height)
                        new_size = (int(width*ratio), int(height*ratio))
                        img = img.resize(new_size, Image.LANCZOS)
                    
                    img.save(output_path, "WebP", quality=95)
                    
            except Exception as e:
                logging.error(f"خطا در پردازش تصویر: {e}")
                await status_message.edit("❌ خطا در پردازش تصویر")
                if os.path.exists(file_path):
                    os.remove(file_path)
                return
            
            if os.path.exists(file_path):
                os.remove(file_path)
            
            if os.path.exists(output_path) and os.path.getsize(output_path) > 0:
                sticker_attributes = [
                    DocumentAttributeSticker(
                        alt="",
                        stickerset=InputStickerSetEmpty()
                    )
                ]
                
                await self.client.send_file(
                    event.chat_id,
                    output_path,
                    reply_to=reply_message.id,
                    attributes=sticker_attributes,
                    force_document=False
                )
                await status_message.delete()
            else:
                await status_message.edit("❌ خطا در ایجاد فایل استیکر")
            
        except Exception as e:
            logging.error(f"خطا در تبدیل تصویر به استیکر: {str(e)}")
            await status_message.edit("❌ خطا در تبدیل تصویر به استیکر")
        finally:
            if os.path.exists(output_path):
                try:
                    os.remove(output_path)
                except:
                    pass

    async def cleanup(self):
        try:
            if os.path.exists(self.temp_dir):
                shutil.rmtree(self.temp_dir, ignore_errors=True)
                logging.info(f"پوشه موقت پاک شد: {self.temp_dir}")
        except Exception as e:
            logging.error(f"خطا در پاکسازی: {e}")