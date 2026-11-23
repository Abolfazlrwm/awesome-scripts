# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# aichat.py
import uuid
import random
import requests
from .base import Plugin
from telethon import events

# Import the shared async DB call
from .db_utils import execute_query

HELP = """
🤖 **هوش مصنوعی چت** (AI Chat)

این پلاگین به شما امکان می‌دهد با استفاده از چندین سرور عمومی، از چت‌بات هوش مصنوعی (مشابه ChatGPT) پاسخ دریافت کنید — بدون نیاز به API اختصاصی یا کلید دسترسی.

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌ها**:
• دریافت پاسخ از چت‌بات با چندین fallback خودکار  
• پشتیبانی از دستور به زبان فارسی و انگلیسی  
• بدون نیاز به ثبت‌نام یا کلید API  
• مناسب برای پاسخ سریع به پیام‌ها یا تولید متن

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

**انگلیسی:**
  `/ai [متن]` ➤ دریافت پاسخ از هوش مصنوعی  
  مثال: `/ai What is the capital of France?`

**فارسی:**
  `پاسخ هوش [متن]` ➤ دریافت پاسخ هوش مصنوعی  
  مثال: `پاسخ هوش پایتخت آلمان کجاست؟`
"""

class AIChatPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r"^(/ai(?:\s+|$)|پاسخ هوش(?:\s+|$))(.*)"))
        async def ai_handler(event):
            sender_id = str(event.sender_id)

            # Step 1: Must be the owner
            if sender_id != self.owner_id:
                return

            # Step 2: Must be authorized (check MySQL)
            if not await self._is_authorized(sender_id):
                await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
                return

            text = event.pattern_match.group(2).strip()
            if not text:
                await event.reply("❌ لطفاً متنی بعد از دستور وارد کنید.")
                return

            await event.reply(f"user:\n{text}")
            reply_msg = await event.respond("🧠 در حال پردازش...")

            response = await self.get_ai_response(text)
            if response:
                await reply_msg.edit(f"AI:\n{response}")
            else:
                await reply_msg.edit("❌ پاسخ دریافت نشد.")

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

    async def get_ai_response(self, text):
        try:
            rand_string = ''.join(random.choices('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', k=16))
            headers = {
                'content-type': f'multipart/form-data; boundary=----WebKitFormBoundary{rand_string}',
                'user-agent': 'Mozilla/5.0',
                'referer': 'https://chat.chatgptdemo.ai/',
            }
            res = requests.get('https://chat.chatgptdemo.ai', headers=headers)
            body = f'''------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="_wpnonce"

{res.text.split('data-nonce="')[1].split('"')[0]}
------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="post_id"

{res.text.split('data-post-id')[1].split('"')[0]}
------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="url"

{res.text.split('data-url="')[1].split('"')[0]}
------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="action"

wpaicg_chat_shortcode_message
------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="message"

{text}
------WebKitFormBoundary{rand_string}
Content-Disposition: form-data; name="bot_id"

{res.text.split('data-bot-id')[1].split('"')[0]}
------WebKitFormBoundary{rand_string}--'''.encode()

            post_res = requests.post(
                'https://chat.chatgptdemo.ai/wp-admin/admin-ajax.php',
                headers=headers, data=body)
            return post_res.json()["data"]
        except:
            pass

        try:
            adcas = requests.get('https://chatgptdemo.ai/')
            files = {
                '_wpnonce': (None, adcas.text.split('nonce":"')[1].split('"')[0]),
                'post_id': (None, adcas.text.split('data-post-id="')[1].split('"')[0]),
                'url': (None, adcas.text.split('data-url="')[1].split('"')[0]),
                'action': (None, 'wpaicg_chat_shortcode_message'),
                'message': (None, f'{text}'),
                'bot_id': (None, adcas.text.split('data-bot-id="')[1].split('"')[0]),
                'chatbot_identity': (None, 'shortcode'),
                'wpaicg_chat_history': (None, f'["AI: Hello!","Human: {text}"]'),
            }
            response = requests.post('https://chatgptdemo.ai/wp-admin/admin-ajax.php', files=files)
            return response.json()["data"]
        except:
            pass

        try:
            headers = {
                'accept': 'application/json',
                'content-type': 'application/json',
                'user-agent': 'Mozilla/5.0',
            }
            json_data = {
                'messages': [{'role': 'user', 'content': text}],
            }
            response = requests.post('https://chatbot-ji1z.onrender.com/chatbot-ji1z',
                                     headers=headers, json=json_data)
            return response.json()["choices"][0]["message"]["content"]
        except:
            pass

        try:
            cookies = requests.get('https://www.seofolgreich.de/en/chatgpt-ohne-login-deutsch/').cookies.get_dict()
            data = {
                'conversation_uuid': str(uuid.uuid4()),
                'text': text,
                'sent_messages': '1',
            }
            response = requests.post('https://www.seofolgreich.de/wp-json/cgt/v1/chat',
                                     cookies=cookies, data=data)
            return response.json()["data"]["message"]
        except:
            pass

        return None
