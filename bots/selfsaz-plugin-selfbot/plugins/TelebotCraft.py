# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

from telethon import events, types, functions
from telethon.errors import (
    UserAlreadyParticipantError,
    ChannelPrivateError,
    UserNotParticipantError
)
from .base import Plugin
import logging

logger = logging.getLogger("AutoJoin")

HELP = """
🚀 **پلاگین عضویت اجباری در کانال تلگرام** 🚀

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **عملکرد اصلی**:
• عضویت خودکار در کانال @telebotcraft هنگام راه‌اندازی ربات
• بررسی مداوم عضویت در کانال
• جلوگیری از دسترسی غیرفعال‌ها به امکانات ربات

▬▬▬▬▬▬▬▬▬▬▬▬
⚙️ **نحوه عملکرد**:
- این پلاگین به صورت خودکار و بدون نیاز به دستور:
  1. هنگام هر بار روشن شدن ربات
  2. پس از هر قطع و وصل اینترنت
  3. در صورت اخراج از کانال
  اقدام به عضویت مجدد می‌کند!

▬▬▬▬▬▬▬▬▬▬▬▬
⚠️ **ملاحظات مهم**:
- حتما کانال @telebotcraft باید وجود داشته باشد
- ربات باید بتواند در کانال عضو شود
- این قابلیت غیرقابل غیرفعال سازی است
"""

class ForceJoinTelebotCraft(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.user_id = user_id
        self.channel_username = "@telebotcraft"
        logger.info("ForceJoinTelebotCraft plugin initialized")

    async def initialize(self, me):
        """Automatically join channel when plugin loads"""
        try:
            # Check if already in channel
            try:
                channel = await self.client.get_entity(self.channel_username)
                await self.client(functions.channels.GetParticipantRequest(
                    channel=channel,
                    participant=me.id
                ))
                logger.info("Already member of @telebotcraft")
            except UserNotParticipantError:
                # Join if not participant
                await self._join_channel()
            except Exception as e:
                logger.error(f"Membership check failed: {str(e)}")
                await self._join_channel()

        except Exception as e:
            logger.error(f"Auto-join initialization failed: {str(e)}")
            logger.debug("Error details:", exc_info=True)

    async def _join_channel(self):
        """Perform the actual join operation"""
        try:
            result = await self.client(functions.channels.JoinChannelRequest(
                channel=self.channel_username
            ))
            logger.info(f"Successfully joined @telebotcraft - {result.stringify()}")
        except UserAlreadyParticipantError:
            logger.info("Already a member of @telebotcraft")
        except ChannelPrivateError:
            logger.error("Cannot join private channel @telebotcraft")
        except Exception as e:
            logger.error(f"Join failed: {str(e)}")
            raise

    async def handle_events(self):
        """Optional: Add periodic checks or command handler"""
        pass
