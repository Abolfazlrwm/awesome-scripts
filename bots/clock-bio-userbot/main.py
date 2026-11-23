# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

import asyncio
import random
from datetime import datetime
from pyrogram import Client
from config import biography, hearts

class BioClock:
    def __init__(self, client: Client):
        self.client = client

    def get_time(self):
        return datetime.now().strftime("%H:%M")

    async def wait_until_full_minute(self):
        now = datetime.now()
        wait = 60 - now.second - now.microsecond / 1_000_000
        await asyncio.sleep(wait)

    async def start(self):
        await self.wait_until_full_minute()
        while True:
            bio = f"{random.choice(biography)}{self.get_time()} {random.choice(hearts)}"
            await self.client.update_profile(bio=bio)
            await asyncio.sleep(60)

# Change
API_ID = ...
API_HASH = ...

async def main():
    app = Client("my_session", api_id=API_ID, api_hash=API_HASH)
    await app.start() #t.me/silentAw
    await BioClock(app).start()

if __name__ == "__main__":
    asyncio.run(main())
