# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/timepfp.py
import logging
import asyncio
import math
from datetime import datetime, timedelta
import pytz
from io import BytesIO
from PIL import Image, ImageDraw, ImageFont, ImageFilter
from telethon.tl.functions.photos import UploadProfilePhotoRequest, DeletePhotosRequest
from telethon import events
from telethon.errors import FloodWaitError
from .base import Plugin
from db import get_time_pfp_settings, set_time_pfp_settings

logger = logging.getLogger(__name__)
HELP = """  
🕒 **پروفایل زمانی پویا** 🕒  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• نمایش زمان تهران روی تصویر پروفایل  
• تغییر خودکار طرح رنگی بر اساس ساعت روز  
• به‌روزرسانی هر دقیقه با طراحی دقیق ساعت  
• پشتیبانی از افکت‌های بصری پیشرفته  
• مدیریت خودکار محدودیت‌های سرور  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  
• `/timepfp on` ➔ فعال‌سازی پروفایل زمانی  
• `/timepfp off` ➔ غیرفعال‌سازی پروفایل زمانی  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. فعال‌سازی سرویس با دستور:  
   `/timepfp on`  
2. مشاهده تغییرات در پروفایل طی ۱ دقیقه  
3. غیرفعال‌سازی با دستور:  
   `/timepfp off`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ ابعاد تصویر: 512x512 پیکسل  
▫️ طرح‌های رنگی: ۴ حالت صبح/عصر/غرب/شب  
▫️ افکت‌های بصری: سایه‌ها - گرادیان - بلور  
▫️ منطقه زمانی: Asia/Tehran  
▫️ فرمت خروجی: JPEG با کیفیت 95%  

⚠️ **هشدارهای مهم**:  
- نیاز به دسترسی **تغییر پروفایل** برای ربات  
- فقط برای کاربر مالک ربات فعال است  
- تغییرات ممکن است تا ۱ دقیقه اعمال نشود  
- استفاده مداوم ممکن است باعث محدودیت موقت شود  
- از غیرفعال‌سازی قبل از خاموشی ربات اطمینان حاصل کنید  
"""  
class TimePfpPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.time_updater_task = None
        self.tehran_tz = pytz.timezone('Asia/Tehran')
        self.font = ImageFont.truetype("arial.ttf", 50)
        logger.info(f"TimePfpPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        logger.debug("TimePfpPlugin initialization complete")

        @self.client.on(events.NewMessage(pattern=r'^/timepfp\s+(on|off)$'))
        async def toggle_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            command = event.pattern_match.group(1).strip().lower()
            new_state = command == 'on'

            await set_time_pfp_settings(self.owner_id, new_state)
            if new_state:
                await event.reply("✅ Time on Profile Picture enabled.")
                await self.start_time_updater()
            else:
                await event.reply("❌ Time on Profile Picture disabled.")
                await self.stop_time_updater()

    async def start_time_updater(self):
        if self.time_updater_task and not self.time_updater_task.done():
            return
        self.time_updater_task = asyncio.create_task(self._update_pfp_loop())

    async def stop_time_updater(self):
        if self.time_updater_task:
            self.time_updater_task.cancel()
            self.time_updater_task = None

    def prepare_image_for_telegram(self, image):
        """Prepare image for Telegram upload with proper format and size."""
        # Convert to RGB mode
        if image.mode != 'RGB':
            # Create white background
            background = Image.new('RGB', image.size, (255, 255, 255))
            if image.mode == 'RGBA':
                background.paste(image, mask=image.split()[3])
            else:
                background.paste(image)
            image = background

        # Ensure proper dimensions (max 640x640)
        max_size = 640
        if image.width > max_size or image.height > max_size:
            image.thumbnail((max_size, max_size), Image.Resampling.LANCZOS)

        return image

    async def _update_pfp_loop(self):
        while True:
            try:
                settings = await get_time_pfp_settings(self.owner_id)
                if not settings or not settings['enabled']:
                    break

                # Generate and prepare the clock image
                clock_img = await self.create_clock_image()
                prepared_img = self.prepare_image_for_telegram(clock_img)

                # Save to BytesIO
                bio = BytesIO()
                prepared_img.save(bio, 'JPEG', quality=95)
                bio.seek(0)
                
                # Log image details for debugging
                logger.debug(f"Image size: {len(bio.getvalue())} bytes")
                logger.debug(f"Image mode: {prepared_img.mode}")
                logger.debug(f"Image dimensions: {prepared_img.size}")

                # Update profile picture
                photos = await self.client.get_profile_photos('me')
                if photos:
                    await self._safe_delete_photo(photos[0])

                # Ensure we're at the start of the buffer before uploading
                bio.seek(0)
                file = await self.client.upload_file(bio, file_name='profile_picture.jpg')
                await self.client(UploadProfilePhotoRequest(file=file))

                logger.debug(f"Successfully updated profile picture for {self.owner_id}")

                # Calculate delay until next update
                now = datetime.now(self.tehran_tz)
                next_minute = (now + timedelta(minutes=1)).replace(second=0, microsecond=0)
                delay = (next_minute - now).total_seconds()
                await asyncio.sleep(delay)

            except FloodWaitError as e:
                logger.warning(f"FloodWait: Waiting for {e.seconds} seconds.")
                await asyncio.sleep(e.seconds)
            except Exception as e:
                logger.error(f"Error updating profile picture: {str(e)}", exc_info=True)
                await asyncio.sleep(10)

    async def _safe_delete_photo(self, photo):
        try:
            await self.client(DeletePhotosRequest([photo]))
        except FloodWaitError as e:
            logger.warning(f"FloodWait during photo deletion: Waiting for {e.seconds} seconds.")
            await asyncio.sleep(e.seconds)
            await self._safe_delete_photo(photo)
        except Exception as e:
            logger.error(f"Error deleting photo: {str(e)}")

    async def _safe_upload_photo(self, bio):
        try:
            await self.client(UploadProfilePhotoRequest(file=await self.client.upload_file(bio)))
        except FloodWaitError as e:
            logger.warning(f"FloodWait during photo upload: Waiting for {e.seconds} seconds.")
            await asyncio.sleep(e.seconds)
            await self._safe_upload_photo(bio)
        except Exception as e:
            logger.error(f"Error uploading photo: {str(e)}")

    async def create_clock_image(self, size=512):
        # Create base image with enhanced gradient
        img = Image.new('RGBA', (size, size))
        draw = ImageDraw.Draw(img)
        
        # Enhanced time-based color schemes
        hour = datetime.now(self.tehran_tz).hour
        if 5 <= hour < 12:  # Morning
            bg_gradient = [(142, 202, 230), (2, 128, 144)]  # Fresh morning blue
        elif 12 <= hour < 17:  # Afternoon
            bg_gradient = [(255, 183, 77), (255, 136, 0)]  # Warm afternoon orange
        elif 17 <= hour < 20:  # Evening
            bg_gradient = [(251, 123, 168), (179, 55, 113)]  # Sunset pink/purple
        else:  # Night
            bg_gradient = [(25, 25, 85), (10, 10, 40)]  # Deep night blue
            
        # Create sophisticated background with multiple layers
        # Layer 1: Base gradient
        for i in range(size):
            ratio = i/size
            r = int(bg_gradient[0][0] * (1-ratio) + bg_gradient[1][0] * ratio)
            g = int(bg_gradient[0][1] * (1-ratio) + bg_gradient[1][1] * ratio)
            b = int(bg_gradient[0][2] * (1-ratio) + bg_gradient[1][2] * ratio)
            draw.line([(0, i), (size, i)], fill=(r, g, b, 255))
        
        # Layer 2: Radial overlay
        overlay = Image.new('RGBA', (size, size))
        overlay_draw = ImageDraw.Draw(overlay)
        for i in range(size//2, -1, -1):
            ratio = i/(size//2)
            alpha = int(25 * ratio)
            overlay_draw.ellipse([size//2-i, size//2-i, size//2+i, size//2+i], 
                               fill=(255, 255, 255, alpha))
        img = Image.alpha_composite(img, overlay)
        
        # Layer 3: Add subtle patterns
        pattern = Image.new('RGBA', (size, size))
        pattern_draw = ImageDraw.Draw(pattern)
        for i in range(0, size, 20):
            alpha = 10 + (math.sin(i/50) * 5)
            pattern_draw.line([(0, i), (size, i)], fill=(255, 255, 255, int(alpha)))
            pattern_draw.line([(i, 0), (i, size)], fill=(255, 255, 255, int(alpha)))
        pattern = pattern.filter(ImageFilter.GaussianBlur(1))
        img = Image.alpha_composite(img, pattern)
        
        # Draw modern clock face
        draw = ImageDraw.Draw(img)
        center = size // 2
        radius = size // 2 - 40
        
        # Draw elegant hour markers
        for i in range(12):
            angle = i * 30 - 90
            rad = math.radians(angle)
            # Major markers for 3, 6, 9, 12
            if i % 3 == 0:
                outer_point = (center + int((radius-5) * math.cos(rad)),
                             center + int((radius-5) * math.sin(rad)))
                inner_point = (center + int((radius-25) * math.cos(rad)),
                             center + int((radius-25) * math.sin(rad)))
                draw.line([outer_point, inner_point], fill=(255, 255, 255, 220), width=4)
            else:
                # Minor markers
                outer_point = (center + int((radius-5) * math.cos(rad)),
                             center + int((radius-5) * math.sin(rad)))
                inner_point = (center + int((radius-15) * math.cos(rad)),
                             center + int((radius-15) * math.sin(rad)))
                draw.line([outer_point, inner_point], fill=(255, 255, 255, 180), width=2)
        
        # Get current time
        now = datetime.now(self.tehran_tz)
        hours = now.hour % 12
        minutes = now.minute
        seconds = now.second
        
        # Draw clock hands with enhanced styling
        # Hour hand
        self.draw_hand(draw, center, center,
                      hours * 30 + minutes / 2,
                      radius * 0.5, 8,
                      (255, 255, 255, 230),
                      (255, 255, 255, 100))
        
        # Minute hand
        self.draw_hand(draw, center, center,
                      minutes * 6,
                      radius * 0.7, 6,
                      (255, 255, 255, 230),
                      (255, 255, 255, 100))
        
        # Second hand with glowing effect
        second_color = (255, 100, 100, 230)  # Bright red for seconds
        glow = Image.new('RGBA', (size, size))
        glow_draw = ImageDraw.Draw(glow)
        self.draw_hand(glow_draw, center, center,
                      seconds * 6,
                      radius * 0.8, 2,
                      second_color,
                      (255, 100, 100, 50))
        glow = glow.filter(ImageFilter.GaussianBlur(3))
        img = Image.alpha_composite(img, glow)
        
        # Add central decoration
        for i in range(15, 0, -5):
            alpha = 150 - i * 5
            draw.ellipse([center-i, center-i, center+i, center+i],
                        fill=(255, 255, 255, alpha))
        
        # Add final subtle vignette
        vignette = Image.new('RGBA', (size, size))
        vignette_draw = ImageDraw.Draw(vignette)
        for i in range(size//2):
            alpha = int(i / (size/2) * 50)
            vignette_draw.ellipse([i, i, size-i, size-i],
                                outline=(0, 0, 0, alpha))
        img = Image.alpha_composite(img, vignette)
        
        return img

    def draw_hand(self, draw, cx, cy, angle, length, width, color, glow_color):
        angle -= 90  # Adjust for clock orientation
        rad = math.radians(angle)
        x = cx + length * math.cos(rad)
        y = cy + length * math.sin(rad)
        
        # Draw smooth glow effect
        for i in range(width + 6, width - 1, -1):
            alpha = int(100 * (i / (width + 6)))
            current_color = (glow_color[0], glow_color[1], glow_color[2], alpha)
            draw.line([(cx, cy), (x, y)], fill=current_color, width=i)
        
        # Draw main hand
        draw.line([(cx, cy), (x, y)], fill=color, width=width)
        
        # Add decorative tip
        tip_size = width // 2
        draw.ellipse([x-tip_size, y-tip_size, x+tip_size, y+tip_size],
                    fill=color)

