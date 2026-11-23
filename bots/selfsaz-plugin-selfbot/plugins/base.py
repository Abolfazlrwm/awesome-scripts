# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
import logging

logger = logging.getLogger(__name__)

class Plugin:
    def __init__(self, client, config):
        self.client = client
        self.config = config
        self.me = None
        
    async def handle_events(self):
        pass

    async def initialize(self, me):
        self.me = me
        logger.info(f"Initialized plugin {self.__class__.__name__}")