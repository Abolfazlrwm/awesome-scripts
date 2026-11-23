# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events, Button
import logging
from telethon.errors import ButtonUrlInvalidError, MessageIdInvalidError, MessageNotModifiedError

logger = logging.getLogger(__name__)

class SupportSystem:
    def __init__(self, bot, admin_id):
        """
        Initialize the SupportSystem.
        
        :param bot: The TelegramClient instance of the bot.
        :param admin_id: The user ID of the admin.
        """
        self.bot = bot
        self.admin_id = admin_id
        self.user_states = {}
        self.active_chats = {}  # Stores chat data including message mappings
        self.message_mappings = {}  # Maps original message IDs to sent message IDs
        
        # Register event handlers
        self.register_handlers()

    def register_handlers(self):
        """Register all necessary event handlers."""
        @self.bot.on(events.NewMessage(pattern='/support'))
        async def support_command(event):
            await self.start_support_chat(event)

        @self.bot.on(events.CallbackQuery(pattern=r'cancel_chat_user_.*'))
        async def cancel_chat_user(event):
            try:
                data = event.data.decode('utf-8')
                user_id = int(data.split('_')[-1])
                await self.handle_cancel_button_user(event, user_id)
            except Exception as e:
                logger.error(f"Error in cancel_chat_user: {str(e)}")
                await event.answer("❌ خطا در لغو چت.", alert=True)

        @self.bot.on(events.CallbackQuery(pattern=r'cancel_chat_admin_.*'))
        async def cancel_chat_admin(event):
            try:
                data = event.data.decode('utf-8')
                user_id = int(data.split('_')[-1])
                await self.handle_cancel_button_admin(event, user_id)
            except Exception as e:
                logger.error(f"Error in cancel_chat_admin: {str(e)}")
                await event.answer("❌ خطا در لغو چت.", alert=True)

        @self.bot.on(events.NewMessage)
        async def message_handler(event):
            if event.sender_id == self.admin_id and event.is_reply:
                await self.handle_admin_reply(event)
            elif event.sender_id in self.user_states:
                await self.handle_user_message(event)

        @self.bot.on(events.MessageEdited)
        async def edit_handler(event):
            await self.handle_message_edit(event)

    async def start_support_chat(self, event):
        """Start a support chat for the user."""
        try:
            user_id = event.sender_id
            self.user_states[user_id] = "awaiting_support_message"
            self.active_chats[user_id] = {"messages": []}

            cancel_button = [
                [Button.inline("❌ لغو چت", data=f"cancel_chat_user_{user_id}")]
            ]

            await self.bot.send_message(
                user_id,
                "💬 **پشتیبانی آنلاین**\n\n"
                "لطفاً پیام خود را ارسال کنید. می‌توانید متن، عکس، ویدیو، فایل و سایر رسانه‌ها را ارسال کنید.\n"
                "پشتیبانی در اسرع وقت پاسخ خواهد داد.\n"
                "برای لغو چت، از دکمه ❌ لغو در زیر پیام خود استفاده کنید.",
                buttons=cancel_button
            )
            
        except Exception as e:
            logger.error(f"Error starting support chat: {str(e)}")
            await self.bot.send_message(
                user_id,
                "❌ خطا در شروع چت پشتیبانی. لطفاً دوباره تلاش کنید."
            )

    async def handle_user_message(self, event):
        """Handle messages from users in a support chat, including media."""
        try:
            user_id = event.sender_id
            
            if user_id not in self.user_states or self.user_states[user_id] != "awaiting_support_message":
                return

            message_header = f"📩 **پیام جدید از کاربر**\n\n👤 کاربر: {event.sender.first_name or 'Unknown'}\n🆔 آی‌دی: {user_id}\n\n"
            has_media = event.media is not None
            
            if has_media:
                sent_msg = await self.bot.send_file(
                    self.admin_id,
                    file=event.media,
                    caption=message_header + (f"📝 کپشن: {event.message.text}" if event.message.text else "")
                )
            else:
                sent_msg = await self.bot.send_message(
                    self.admin_id,
                    message_header + f"📝 پیام: {event.message.text}"
                )

            # Store message info and mapping
            self.active_chats[user_id]["messages"].append({
                "from": "user",
                "message_id": event.id,
                "sent_message_id": sent_msg.id,
                "has_media": has_media
            })
            self.message_mappings[event.id] = sent_msg.id

            admin_cancel_button = [
                [Button.inline("❌ لغو چت پشتیبانی", data=f"cancel_chat_admin_{user_id}")]
            ]

            await self.bot.send_message(
                self.admin_id,
                "برای پاسخ به این پیام، لطفاً روی آن ریپلای کنید.",
                buttons=admin_cancel_button,
                reply_to=sent_msg.id
            )

            user_cancel_button = [
                [Button.inline("❌ لغو چت", data=f"cancel_chat_user_{user_id}")]
            ]
            
            await self.bot.send_message(
                user_id,
                "✅ پیام شما ارسال شد. منتظر پاسخ پشتیبانی باشید.",
                buttons=user_cancel_button
            )

        except Exception as e:
            logger.error(f"Error handling user message: {str(e)}")
            await self.bot.send_message(
                user_id,
                "❌ خطا در ارسال پیام به پشتیبانی. لطفاً دوباره تلاش کنید."
            )

    async def handle_admin_reply(self, event):
        """Handle replies from the admin to a user, including media."""
        try:
            if not event.is_reply:
                await event.respond("❌ لطفاً به پیامی که کاربر ارسال کرده است پاسخ دهید.")
                return

            replied_msg = await event.get_reply_message()
            user_id = None
            
            for uid, chat_data in self.active_chats.items():
                for msg in chat_data["messages"]:
                    if replied_msg.id == msg.get("sent_message_id"):
                        user_id = uid
                        break
                if user_id:
                    break

            if not user_id:
                await event.respond("❌ کاربر مرتبط با این پیام یافت نشد.")
                return

            has_media = event.media is not None
            
            if has_media:
                sent_msg = await self.bot.send_file(
                    user_id,
                    file=event.media,
                    caption=f"👨‍💼 **پاسخ پشتیبانی:**\n\n{event.message.text}" if event.message.text else ""
                )
            else:
                sent_msg = await self.bot.send_message(
                    user_id,
                    f"👨‍💼 **پاسخ پشتیبانی:**\n\n{event.message.text}"
                )

            self.active_chats[user_id]["messages"].append({
                "from": "admin",
                "message_id": event.id,
                "sent_message_id": sent_msg.id,
                "has_media": has_media
            })
            self.message_mappings[event.id] = sent_msg.id

            await event.respond("✅ پاسخ شما به کاربر ارسال شد.")

        except Exception as e:
            logger.error(f"Error handling admin reply: {str(e)}")
            await event.respond("❌ خطا در ارسال پاسخ به کاربر.")

    async def handle_message_edit(self, event):
        """Handle edited messages from users or admin."""
        try:
            message_id = event.id
            if message_id not in self.message_mappings:
                return  # Not a tracked message

            sent_message_id = self.message_mappings[message_id]
            user_id = None
            sender = "user" if event.sender_id != self.admin_id else "admin"

            # Find the associated chat
            for uid, chat_data in self.active_chats.items():
                for msg in chat_data["messages"]:
                    if msg["message_id"] == message_id:
                        user_id = uid
                        break
                if user_id:
                    break

            if not user_id:
                return

            has_media = event.media is not None
            target_id = self.admin_id if sender == "user" else user_id
            prefix = "📩 **پیام ویرایش‌شده از کاربر**\n\n" if sender == "user" else "👨‍💼 **پاسخ ویرایش‌شده پشتیبانی:**\n\n"

            if has_media:
                await self.bot.edit_message(
                    target_id,
                    sent_message_id,
                    file=event.media,
                    text=prefix + (f"📝 کپشن: {event.message.text}" if event.message.text else "")
                )
            else:
                await self.bot.edit_message(
                    target_id,
                    sent_message_id,
                    prefix + f"📝 پیام: {event.message.text}"
                )

        except MessageNotModifiedError:
            pass  # Ignore if the message wasn't actually changed
        except Exception as e:
            logger.error(f"Error handling message edit: {str(e)}")

    async def handle_cancel_button_user(self, event, user_id):
        """Handle cancel button click from user."""
        try:
            if user_id not in self.active_chats:
                await event.answer("❌ چت پشتیبانی فعالی وجود ندارد.", alert=True)
                return

            # Clean up chat data and mappings
            for msg in self.active_chats[user_id]["messages"]:
                if msg["message_id"] in self.message_mappings:
                    del self.message_mappings[msg["message_id"]]
            del self.user_states[user_id]
            del self.active_chats[user_id]

            await self.bot.send_message(
                user_id,
                "🔚 **چت پشتیبانی لغو شد.**\n\n"
                "اگر سوال دیگری دارید، می‌توانید دوباره از دستور /support استفاده کنید."
            )

            await self.bot.send_message(
                self.admin_id,
                f"🔚 **چت پشتیبانی با کاربر {user_id} توسط کاربر لغو شد.**"
            )

            await event.answer("✅ چت پشتیبانی لغو شد.", alert=True)

        except Exception as e:
            logger.error(f"Error in handle_cancel_button_user: {str(e)}")
            await event.answer("❌ خطا در لغو چت.", alert=True)

    async def handle_cancel_button_admin(self, event, user_id):
        """Handle cancel button click from admin."""
        try:
            if user_id not in self.active_chats:
                await event.answer("❌ کاربر مورد نظر در حال حاضر در چت پشتیبانی نیست.", alert=True)
                return

            # Clean up chat data and mappings
            for msg in self.active_chats[user_id]["messages"]:
                if msg["message_id"] in self.message_mappings:
                    del self.message_mappings[msg["message_id"]]
            del self.user_states[user_id]
            del self.active_chats[user_id]

            await self.bot.send_message(
                user_id,
                "🔚 **چت پشتیبانی توسط پشتیبان لغو شد.**\n\n"
                "اگر سوال دیگری دارید، می‌توانید دوباره از دستور /support استفاده کنید."
            )

            await event.answer("✅ چت پشتیبانی لغو شد.", alert=True)
            try:
                await event.delete()
            except:
                pass

        except Exception as e:
            logger.error(f"Error in handle_cancel_button_admin: {str(e)}")
            await event.answer("❌ خطا در لغو چت.", alert=True)