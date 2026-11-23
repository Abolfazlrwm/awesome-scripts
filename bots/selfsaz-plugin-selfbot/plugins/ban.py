# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError
from telethon.tl.types import Channel, Chat, ChatBannedRights
import logging

logger = logging.getLogger(__name__)

HELP = """  
🚫 **مدیریت ممنوعیت کاربران** 🚫  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• مسدود کردن کاربران در **گروه‌ها و سوپرگروه‌ها**  
• پشتیبانی از دو روش:  
  - ریپلای روی پیام کاربر  
  - استفاده از آیدی/یوزرنیم (فقط در صورتی که به صورت @ یا یک عدد ارسال شود)  
• بررسی خودکار **دسترسی ادمین** قبل از اجرا  
• اخطارهای خودکار برای خطاهای دسترسی  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:

**انگلیسی:**
  `/ban [username/user ID]` ➤ مسدود کردن کاربر هدف  
  `/ban` (با ریپلای) ➤ مسدود کردن کاربر ریپلای شده  

**فارسی (بدون اسلش):**
  `بن [یوزرنیم/آیدی]` ➤ مسدود کردن کاربر هدف  
  `بن` (با ریپلای) ➤ مسدود کردن کاربر ریپلای شده  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. مسدود کردن با ریپلای:  
   `/ban` یا `بن` (ریپلای روی پیام کاربر)  
2. مسدود کردن با آیدی:  
   `/ban 123456789` یا `بن @username`  

⚠️ **نکات مهم**:  
- فقط برای ادمین‌های با دسترسی **Ban Users** قابل اجراست  
- در پیوی کاربران کار نمی‌کند  
- خطاها به صورت خودکار به مالک گزارش می‌شوند  
"""

class BanPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        logger.info(f"BanPlugin initialized for owner: {self.owner_id}")

    async def handle_events(self):
        @self.client.on(events.NewMessage)
        async def handler(event):
            # Only act if the sender is the owner.
            if str(event.sender_id) != self.owner_id:
                return

            message = event.raw_text.strip()
            words = message.split()
            if not words:
                return

            # Must start with either "/ban" or "بن".
            if words[0] not in ["/ban", "بن"]:
                return

            # For reply commands, the message should contain only one word.
            if event.is_reply:
                if len(words) != 1:
                    return  # Only accept the command word when replying.
            else:
                # For non-reply commands, expect exactly two words.
                if len(words) != 2:
                    return  # Incorrect format; ignore.
                target = words[1].strip()
                # Process only if the target begins with "@" or is a number.
                if not (target.startswith("@") or target.isdigit()):
                    return  # Likely a casual mention; ignore.

            try:
                chat = await event.get_chat()
                if not isinstance(chat, (Channel, Chat)):
                    await event.reply("❌ This command only works in groups!")
                    return

                sender = await event.get_sender()
                permissions = await self.client.get_permissions(chat, sender)
                if not permissions.is_admin or not permissions.ban_users:
                    await event.reply("❌ You need admin privileges with ban rights!")
                    return

                if event.is_reply:
                    reply_msg = await event.get_reply_message()
                    target_user = await reply_msg.get_sender()
                    target_id = target_user.id
                    target_name = target_user.first_name
                else:
                    target = words[1].strip()
                    if target.isdigit():
                        target_id = int(target)
                        target_name = f"User {target_id}"
                    else:
                        try:
                            target_user = await self.client.get_entity(target)
                            target_id = target_user.id
                            target_name = target_user.first_name
                        except Exception as e:
                            await event.reply(f"❌ Could not find user by username: {str(e)}")
                            return

                try:
                    banned_rights = ChatBannedRights(
                        until_date=None,
                        view_messages=True,
                        send_messages=True,
                        send_media=True,
                        send_stickers=True,
                        send_gifs=True,
                        send_games=True,
                        send_inline=True,
                        embed_links=True
                    )

                    try:
                        await self.client(functions.channels.EditBannedRequest(
                            channel=chat.id,
                            participant=target_id,
                            banned_rights=banned_rights
                        ))
                        await event.reply(f"🚫 Banned {target_name} ({target_id})")
                        return
                    except Exception as e:
                        logger.warning(f"First ban attempt failed: {str(e)}")

                    try:
                        await self.client(functions.channels.EditBannedRequest(
                            channel=chat.id,
                            participant=target_id,
                            banned_rights=ChatBannedRights(
                                until_date=None,
                                view_messages=True
                            )
                        ))
                        await event.reply(f"🚫 Banned {target_name} ({target_id})")
                        return
                    except Exception as e:
                        logger.warning(f"Second ban attempt failed: {str(e)}")

                    await self.client.edit_permissions(
                        entity=chat.id,
                        user=target_id,
                        view_messages=False
                    )
                    await event.reply(f"🚫 Banned {target_name} ({target_id})")

                except (ChatAdminRequiredError, UserAdminInvalidError):
                    await event.reply("❌ I don't have permission to ban users here!")
                except Exception as e:
                    await event.reply(f"❌ Error banning user: {str(e)}")
                    logger.exception("Ban command error:")

            except Exception as e:
                await event.reply(f"❌ Unexpected error: {str(e)}")
                logger.exception("Unexpected error in ban handler:")
