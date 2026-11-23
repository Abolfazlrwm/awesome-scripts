# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import logging
import json
import re
import random
import tempfile
from .game_hack import GameeScoreSubmitter

logger = logging.getLogger(__name__)

HELP = """
🎮 **افزونه ارسال امتیاز Gamee** 🎮

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌های اصلی**:
• ارسال امتیاز به بازی‌های Gamee
• زمان بازی تصادفی (حداکثر 43 ثانیه)
• خروجی به صورت فایل JSON

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

**انگلیسی:**
  `/hack <امتیاز> <لینک>`  
  `/hack <امتیاز>` (پاسخ به پیام حاوی لینک)

**فارسی:**
  `هک <امتیاز> <لینک>`  
  `هک <امتیاز>` (پاسخ به پیام حاوی لینک)

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه اجرا**:
1. ارسال امتیاز با لینک:
   `/hack 100 https://...` یا `هک 100 https://...`
2. ارسال امتیاز با ریپلای:
   `/hack 100` یا `هک 100`
"""

class GameeScorePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"پلاگین برای مدیر {self.owner_id} فعال شد")

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/hack|هک)(?:\s+(\d+)(?:\s+(https?://\S+))?)?$'))
        async def handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                # Extract game URL from the command or from a replied message
                game_url = await self._extract_game_url(event)
                if not game_url:
                    await event.reply("❌ لینک معتبر یافت نشد")
                    return

                # Extract score
                score = await self._extract_score(event)
                if score is None:
                    return

                # Set random play_time (in milliseconds, up to 43 seconds)
                play_time = random.randint(30000, 43000)

                result = await self._submit_score(game_url, score, play_time)
                
                await self._send_json_result(event, score, play_time, game_url, result)

            except Exception as e:
                await event.reply(f"❌ خطا: {str(e)}")
                logger.exception("خطا در پردازش دستور:")

    async def _extract_game_url(self, event):
        parts = event.message.text.split()
        if len(parts) >= 3 and parts[-1].startswith('http'):
            return parts[-1]
        
        if event.is_reply:
            reply_msg = await event.get_reply_message()
            if reply_msg and reply_msg.text:
                url_match = re.search(r'https?://[^\s]+', reply_msg.text)
                return url_match.group(0) if url_match else None
        return None

    async def _extract_score(self, event):
        try:
            parts = event.message.text.split()
            return int(parts[1]) if len(parts) > 1 else None
        except (IndexError, ValueError):
            await event.reply("❌ فرمت امتیاز نامعتبر است")
            return None

    async def _submit_score(self, game_url, score, play_time):
        submitter = GameeScoreSubmitter(game_url)
        return submitter.submit_score(score=score, play_time=play_time)

    async def _send_json_result(self, event, score, play_time, game_url, result):
        data = {
            "status": "success" if not isinstance(result, dict) or "error" not in result else "error",
            "score": score,
            "play_time": play_time,
            "game_url": game_url,
            "server_response": result
        }

        with tempfile.NamedTemporaryFile(mode='w+', suffix='.json', encoding='utf-8') as tmp:
            json.dump(data, tmp, indent=2, ensure_ascii=False)
            tmp.flush()
            await event.reply(
                f"✅ نتیجه ارسال امتیاز:\n"
                f"امتیاز: {score}\n"
                f"زمان بازی: {play_time} میلی‌ثانیه\n"
                f"لینک بازی: {game_url}",
                file=tmp.name
            )

    def extract_url_from_text(self, text):
        url_match = re.search(r'https?://[^\s]+', text)
        return url_match.group(0) if url_match else None
