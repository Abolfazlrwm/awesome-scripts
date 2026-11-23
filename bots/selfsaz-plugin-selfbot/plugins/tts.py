# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import os
from persianttsfarsi import PersianTTS
import concurrent.futures

HELP = """
🎤 تبدیل متن به گفتار (TTS)

دستورات:
- `/tts [متن]` - تبدیل متن به صوت
- `/tts` (در پاسخ به یک پیام) - تبدیل متن پیام پاسخ داده شده به صوت

محدودیت:
- حداکثر طول متن: 500 کاراکتر
"""

MAX_TEXT_LENGTH = 500

class PersianTTSPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.tts = PersianTTS()
        self.executor = concurrent.futures.ThreadPoolExecutor()

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/tts($| .*)'))
        async def tts_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            # استخراج متن
            text = event.pattern_match.group(1).strip()
            replied = await event.get_reply_message()

            if not text and replied and replied.text:
                text = replied.text
            elif not text:
                await event.reply("❌ لطفا متن را وارد کنید یا روی یک پیام ریپلای کنید")
                return

            text = text[:MAX_TEXT_LENGTH].strip()
            if not text:
                await event.reply("❌ متن وارد شده معتبر نیست")
                return

            output_file = f"tts_output_{event.id}.mp3"
            
            try:
                # اجرا در thread جداگانه بدون استفاده از asyncio
                await event.client.loop.run_in_executor(
                    self.executor,
                    lambda: self.tts.synthesize(text, output_file)
                )
                
                await self.client.send_file(
                    event.chat_id,
                    output_file,
                    voice_note=True,
                    reply_to=event.message.id
                )
                
            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")
            finally:
                if os.path.exists(output_file):
                    try:
                        os.remove(output_file)
                    except:
                        pass