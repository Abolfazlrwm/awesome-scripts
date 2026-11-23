# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import re
import asyncio
from deep_translator import GoogleTranslator
from telethon import events
from .base import Plugin

logger = logging.getLogger(__name__)
HELP = """
🌐 **سرویس ترجمه پیشرفته** 🌐

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **امکانات اصلی**:
• ترجمه متن به ۱۰۰+ زبان مختلف
• تشخیص خودکار زبان مبدأ
• ترجمه متن مستقیم یا پیام ریپلای شده
• نمایش متن اصلی و ترجمه شده

🎯 **دستورات فارسی**:
`ترجمه [کد-زبان] [متن]` - ترجمه فوری متن
`ترجمه [کد-زبان]` - ترجمه پیام ریپلای شده

✨ **نمونه استفاده**:
1. ترجمه متن مستقیم:
   `ترجمه en سلام دنیا`
2. ترجمه پیام ریپلای شده:
   `ترجمه fa` (با ریپلای روی پیام)

📝 **کدهای زبان پرکاربرد**:
• فارسی: fa
• انگلیسی: en
• عربی: ar
• ترکی: tr
• آلمانی: de
• فرانسوی: fr
• اسپانیایی: es
• روسی: ru
• چینی: zh

⚠️ **نکات مهم**:
• حداکثر طول متن: 5000 کاراکتر
• از کدهای زبان معتبر استفاده کنید
• برای متن‌های طولانی ممکن است زمان بیشتری نیاز باشد
"""

class TranslationPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.default_target = 'fa'
        logger.info(f"TranslationPlugin initialized for owner: {self.owner_id}")

    async def initialize(self, me):
        self.me = me
        logger.debug("[Translate] Initialization complete")

    async def handle_events(self):
        # English command pattern (/tr)
        @self.client.on(events.NewMessage(
            pattern=re.compile(r'^/tr(?:\s+(\w{2}))?(?:\s+(.+))?$', re.IGNORECASE)))
        async def english_translate_handler(event):
            await self._process_translation(event)

        # Farsi command pattern (ترجمه)
        @self.client.on(events.NewMessage(
            pattern=re.compile(r'^ترجمه(?:\s+(\w{2}))?(?:\s+(.+))?$', re.IGNORECASE)))
        async def farsi_translate_handler(event):
            await self._process_translation(event)

    async def _process_translation(self, event):
        if str(event.sender_id) != self.owner_id:
            return

        match = event.pattern_match
        target_lang = (match.group(1) or self.default_target).lower()
        text = match.group(2) or ""

        if event.is_reply and not text:
            reply_msg = await event.get_reply_message()
            text = reply_msg.text

        if not text:
            await event.reply("❌ متنی برای ترجمه یافت نشد. لطفاً به پیامی ریپلای کنید یا متن را وارد نمایید.")
            return

        try:
            translation = await self._translate_text(text, target_lang)
            await event.reply(translation)
        except Exception as e:
            logger.error(f"Translation error: {str(e)}")
            await event.reply("❌ خطا در ترجمه. لطفاً کد زبان را بررسی کرده و مجدداً تلاش کنید.")

    async def _translate_text(self, text: str, target_lang: str) -> str:
        try:
            translator = GoogleTranslator(source='auto', target=target_lang)
            translated_text = translator.translate(text)
            
            return (
                f"🌐 ترجمه (خودکار → {target_lang.upper()}):\n\n"
                f"{translated_text}\n\n"
                f"🔹 متن اصلی:\n{text}"
            )
        except Exception as e:
            logger.error(f"Translation error: {str(e)}")
            raise