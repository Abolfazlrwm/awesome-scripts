# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
import sqlite3
import os
import asyncio
import aiohttp
import json
import random



HELP = """
⚡ **دستیار هوش مصنوعی Groq** ⚡

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌ها**:
• پاسخ‌های سریع با Groq Cloud
• پشتیبانی از تمام مدل‌ها
• شبیه‌سازی واقعی تایپ
• حفظ تاریخچه گفتگو
• تقسیم خودکار پیام‌های طولانی

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:
• `/setapikey <کلید>` - تنظیم کلید API
• `/gpt <متن>` - دریافت پاسخ
• `/clearchat` - پاکسازی تاریخچه
• `/model <نام>` - تغییر مدل
• `/limits` - مشاهده محدودیت‌ها

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه**:
1. تنظیم کلید:
   `/setapikey کلید_شما`
2. پرسش:
   `/gpt هوش مصنوعی را توضیح بده`
"""

class GroqPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/groq.db"
        self._init_db()
        self.api_key = None
        self.base_url = "https://api.groq.com/openai/v1"
        self.default_model = "llama3-70b-8192"
        self.conversations = {}
        self.session = aiohttp.ClientSession()
        self.max_message_length = 4000
        self.typing_chars = ["▌", "▐", "▎"]
        self.typing_speed = 0.03  # seconds per character
        self.chunk_size = 190    
        # characters per edit

        self.available_models = {
            "llama3-70b-8192": "Llama 3 70B",
            "llama3-8b-8192": "Llama 3 8B", 
            "mixtral-8x7b-32768": "Mixtral 8x7B",
            "gemma2-9b-it": "Gemma 2 9B",
            "whisper-large-v3": "Whisper (Speech)"
        }

    def _init_db(self):
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                CREATE TABLE IF NOT EXISTS groq_keys (
                    owner_id TEXT PRIMARY KEY,
                    api_key TEXT
                )
            """)
            conn.commit()

    async def _db_execute(self, query, params):
        async with asyncio.Lock():
            with sqlite3.connect(self.db_path) as conn:
                conn.execute(query, params)
                conn.commit()

    async def _db_fetch(self, query, params):
        async with asyncio.Lock():
            with sqlite3.connect(self.db_path) as conn:
                cursor = conn.execute(query, params)
                return cursor.fetchone()

    async def _simulate_typing(self, event, full_text):
        """Simulate realistic AI typing with editing"""
        msg = await event.reply(random.choice(self.typing_chars))
        displayed_text = ""
        
        for i in range(0, len(full_text), self.chunk_size):
            chunk = full_text[i:i+self.chunk_size]
            displayed_text += chunk
            
            # Add random typing indicator
            typing_char = random.choice(self.typing_chars)
            temp_text = displayed_text + typing_char
            
            try:
                await msg.edit(temp_text)
            except Exception:
                # If message too long, send new part
                await msg.edit(displayed_text)
                msg = await event.reply(typing_char)
                displayed_text = ""
            
            await asyncio.sleep(self.typing_speed * len(chunk))
        
        await msg.edit(full_text)
        return msg

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/setapikey\s+(.+)$'))
        async def setapikey_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            api_key = event.pattern_match.group(1).strip()
            try:
                await self._db_execute(
                    "INSERT OR REPLACE INTO groq_keys (owner_id, api_key) VALUES (?, ?)",
                    (self.owner_id, api_key)
                )
                self.api_key = api_key
                await event.reply("✅ API key set successfully!\n✅ کلید API تنظیم شد")
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}\n❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^/gpt(?:\s+(.+))?$'))
        async def gpt_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            # Load API key
            if not self.api_key:
                result = await self._db_fetch(
                    "SELECT api_key FROM groq_keys WHERE owner_id = ?",
                    (self.owner_id,)
                )
                if result:
                    self.api_key = result[0]
                else:
                    await event.reply("❌ API key not set! Use /setapikey\n❌ کلید API تنظیم نشده")
                    return

            prompt = event.pattern_match.group(1)
            if not prompt:
                await event.reply("❌ Please provide a prompt\n❌ لطفا یک متن وارد کنید")
                return

            # Manage conversation history
            chat_id = event.chat_id
            if chat_id not in self.conversations:
                self.conversations[chat_id] = []

            self.conversations[chat_id].append({
                "role": "user",
                "content": prompt
            })

            try:
                # Show typing action
                async with self.client.action(event.chat_id, 'typing'):
                    response = await self._get_groq_response(self.conversations[chat_id])
                    full_reply = response['choices'][0]['message']['content']
                
                # Store response
                self.conversations[chat_id].append({
                    "role": "assistant",
                    "content": full_reply
                })
                
                # Show with typing simulation
                await self._simulate_typing(event, full_reply)
                
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}\n❌ خطا: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^/clearchat$'))
        async def clearchat_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            chat_id = event.chat_id
            if chat_id in self.conversations:
                self.conversations[chat_id] = []
                await event.reply("✅ Chat history cleared\n✅ تاریخچه پاکسازی شد")
            else:
                await event.reply("❌ No active chat\n❌ مکالمه‌ای وجود ندارد")

        @self.client.on(events.NewMessage(pattern=r'^/model\s+(.+)$'))
        async def model_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            model = event.pattern_match.group(1).strip()
            if model not in self.available_models:
                models_list = "\n".join([f"- {k} ({v})" for k,v in self.available_models.items()])
                await event.reply(f"❌ Invalid model. Available:\n{models_list}\n\n❌ مدل نامعتبر. مدل‌های موجود:\n{models_list}")
                return
            
            self.default_model = model
            await event.reply(f"✅ Model changed to {model}\n✅ مدل تغییر کرد به {model}")

        @self.client.on(events.NewMessage(pattern=r'^/limits$'))
        async def limits_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            limits_info = "📊 Usage Limits:\n"
            limits_info += f"Model: {self.available_models.get(self.default_model, self.default_model)}\n"
            limits_info += "Default Limits:\n"
            limits_info += "- RPM: 30\n- TPM: 6,000\n\n"
            limits_info += "📊 محدودیت‌های استفاده:\n"
            limits_info += f"مدل: {self.available_models.get(self.default_model, self.default_model)}\n"
            await event.reply(limits_info)

        @self.client.on(events.NewMessage(pattern=r'^/gpthelp$'))
        async def help_handler(event):
            await event.reply(HELP_EN + "\n\n" + HELP_FA)

    async def _get_groq_response(self, messages):
        headers = {
            "Content-Type": "application/json",
            "Authorization": f"Bearer {self.api_key}"
        }
        
        payload = {
            "model": self.default_model,
            "messages": messages,
            "temperature": 0.7
        }
        
        async with self.session.post(
            f"{self.base_url}/chat/completions",
            headers=headers,
            json=payload
        ) as response:
            if response.status != 200:
                error_text = await response.text()
                raise Exception(f"API error: {error_text}")
            return await response.json()

    async def close(self):
        """Cleanup resources"""
        await self.session.close()
        await super().close()