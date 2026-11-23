# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types, Button
from telethon.tl.types import UpdateMessageReactions, PeerChannel, PeerUser, PeerChat
import random
import string
import asyncio
import time
import aiosqlite
import os
from pathlib import Path
from telethon.tl.functions.messages import SetTypingRequest, SendReactionRequest
from telethon.tl.types import SendMessageTypingAction, ReactionEmoji

HELP = """
🔒 **سیستم حفاظتی کپچا مبتنی بر واکنش** 🔒

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **ویژگی‌ها**:
• فعال‌سازی با دستور `/captcha` یا `فعال کپچا`
• تنظیمات پیکربندی برای هر کاربر
• بی‌صدا کردن خودکار اعضای جدید
• چالش کپچا مبتنی بر واکنش
• لغو خودکار حالت بی‌صدا پس از تأیید موفق
• زمان و تلاش‌های قابل تنظیم

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات**:

**انگلیسی:**
  `/captcha` ➤ فعال‌سازی حفاظت  
  `/stopcaptcha` ➤ غیرفعال‌سازی حفاظت  
  `/captchastatus` ➤ بررسی وضعیت کپچا  
  `/setcaptcha [timeout] [attempts]` ➤ پیکربندی تنظیمات  
      (مثال: `/setcaptcha 300 3` برای ۵ دقیقه و ۳ تلاش)

**فارسی:**
  `فعال کپچا` ➤ فعال‌سازی حفاظت  
  `غیرفعال کپچا` ➤ غیرفعال‌سازی حفاظت  
  `وضعیت کپچا` ➤ بررسی وضعیت کپچا  
  `تنظیم کپچا [timeout] [attempts]` ➤ پیکربندی تنظیمات  
      (مثال: `تنظیم کپچا 300 3` برای ۵ دقیقه و ۳ تلاش)
"""

class CaptchaPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/captcha.db"
        self.active_chats = {}  # {chat_id: {'pending': {user_id: data}}}
        self.active_captchas = {}  # {msg_id: {'user_id': user_id, 'chat_id': chat_id, ...}}
        Path("data").mkdir(exist_ok=True)
        asyncio.create_task(self._init_db())
        
        # Common emoji reactions
        self.emoji_list = ["👍", "👎", "❤️", "🔥", "👏", "😁", "🎉", "🤔", "😢", "😮", "🙏", "👌", "🥳", "🤩", "😎"]

    async def _init_db(self):
        """Initialize database tables"""
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute("""
                CREATE TABLE IF NOT EXISTS captcha_settings (
                    owner_id TEXT PRIMARY KEY,
                    timeout INTEGER DEFAULT 300,
                    max_attempts INTEGER DEFAULT 3
                )
            """)
            await db.execute("""
                CREATE TABLE IF NOT EXISTS active_chats (
                    chat_id TEXT,
                    owner_id TEXT,
                    PRIMARY KEY (chat_id, owner_id)
                )
            """)
            await db.commit()

    async def _get_settings(self, owner_id):
        """Get user-specific settings"""
        async with aiosqlite.connect(self.db_path) as db:
            cursor = await db.execute(
                "SELECT timeout, max_attempts FROM captcha_settings WHERE owner_id = ?",
                (owner_id,)
            )
            settings = await cursor.fetchone()
            if not settings:
                await db.execute(
                    "INSERT INTO captcha_settings (owner_id, timeout, max_attempts) VALUES (?, ?, ?)",
                    (owner_id, 300, 3)
                )
                await db.commit()
                return (300, 3)
            return settings

    async def _update_settings(self, owner_id, timeout, max_attempts):
        """Update user settings"""
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(
                """INSERT OR REPLACE INTO captcha_settings 
                (owner_id, timeout, max_attempts) VALUES (?, ?, ?)""",
                (owner_id, timeout, max_attempts)
            )
            await db.commit()

    async def _add_active_chat(self, chat_id, owner_id):
        """Mark a chat as active with proper ID formatting"""
        chat_id = str(chat_id)
        if not chat_id.startswith('-100') and len(chat_id) > 0:
            chat_id = f"-100{chat_id}"
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(
                "INSERT OR IGNORE INTO active_chats (chat_id, owner_id) VALUES (?, ?)",
                (chat_id, owner_id)
            )
            await db.commit()

    async def _remove_active_chat(self, chat_id):
        """Remove chat from active list, handling both formats"""
        chat_id = str(chat_id)
        possible_ids = [chat_id]
        if chat_id.startswith('-100'):
            possible_ids.append(chat_id[4:])
        else:
            possible_ids.append(f"-100{chat_id}")
        
        async with aiosqlite.connect(self.db_path) as db:
            await db.execute(
                "DELETE FROM active_chats WHERE chat_id IN (?, ?) AND owner_id = ?",
                (possible_ids[0], possible_ids[1], self.owner_id)
            )
            await db.commit()

    async def _is_chat_active_for_owner(self, chat_id):
        """Check if chat is active for this specific owner, handling both regular and supergroup ID formats"""
        chat_id = str(chat_id)
        
        # Try both formats (with and without -100)
        possible_ids = [chat_id]
        if chat_id.startswith('-100'):
            possible_ids.append(chat_id[4:])  # Try without -100
        else:
            possible_ids.append(f"-100{chat_id}")  # Try with -100
        
        async with aiosqlite.connect(self.db_path) as db:
            # Use IN clause to check both formats
            cursor = await db.execute(
                "SELECT 1 FROM active_chats WHERE chat_id IN (?, ?) AND owner_id = ?",
                (possible_ids[0], possible_ids[1], self.owner_id)
            )
            return bool(await cursor.fetchone())

    def _generate_emoji_captcha(self):
        """Generate a reaction-based captcha with one correct emoji to select"""
        # Select 5 random emojis, one of which will be the correct answer
        selected_emojis = random.sample(self.emoji_list, 5)
        correct_emoji = random.choice(selected_emojis)
        
        return {
            'emojis': selected_emojis,
            'correct_emoji': correct_emoji
        }

    async def _get_user_id_from_peer(self, peer):
        """Convert peer object to user ID string"""
        if isinstance(peer, PeerUser):
            return str(peer.user_id)
        return None

    async def _get_chat_id_from_peer(self, peer):
        """Convert peer object to chat ID string"""
        if isinstance(peer, PeerChannel):
            return f"-100{peer.channel_id}"
        elif isinstance(peer, PeerChat):
            return str(-peer.chat_id)
        return None

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/captcha|فعال کپچا)$'))
        async def activate_captcha(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            chat = await event.get_chat()
            if not (isinstance(chat, types.Chat) or 
                   (isinstance(chat, types.Channel) and chat.megagroup)):
                await event.reply("❌ این دستور فقط در گروه‌ها/سوپرگروه‌ها کار می‌کند")
                return
                
            chat_id = str(chat.id)
            await self._add_active_chat(chat_id, self.owner_id)
            
            if chat_id not in self.active_chats:
                self.active_chats[chat_id] = {'pending': {}}
                
            timeout, attempts = await self._get_settings(self.owner_id)
            await event.reply(
                f"✅ سیستم حفاظتی کپچا مبتنی بر واکنش فعال شد!\n"
                f"• مهلت: {timeout//60} دقیقه\n"
                f"• تلاش‌ها: {attempts}"
            )

        @self.client.on(events.NewMessage(pattern=r'^(?:/captchastatus|وضعیت کپچا)$'))
        async def check_captcha_status(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            chat = await event.get_chat()
            chat_id = str(chat.id)
            
            is_active = await self._is_chat_active_for_owner(chat_id)
            timeout, attempts = await self._get_settings(self.owner_id)
            
            if is_active:
                pending_count = 0
                if chat_id in self.active_chats and 'pending' in self.active_chats[chat_id]:
                    pending_count = len(self.active_chats[chat_id]['pending'])
                
                await event.reply(
                    f"✅ کپچا در این گفتگو فعال است\n"
                    f"• مهلت: {timeout//60} دقیقه\n"
                    f"• حداکثر تلاش‌ها: {attempts}\n"
                    f"• تأییدهای در انتظار: {pending_count}"
                )
            else:
                await event.reply("❌ کپچا در این گفتگو فعال نیست")

        @self.client.on(events.NewMessage(pattern=r'^(?:/stopcaptcha|غیرفعال کپچا)$'))
        async def deactivate_captcha(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            chat = await event.get_chat()
            chat_id = str(chat.id)
            
            await self._remove_active_chat(chat_id)
            if chat_id in self.active_chats:
                del self.active_chats[chat_id]
                
            await event.reply("❌ سیستم حفاظتی کپچا غیرفعال شد")

        @self.client.on(events.NewMessage(pattern=r'^(?:/setcaptcha|تنظیم کپچا)(?:\s+(\d+)\s+(\d+))?$'))
        async def set_captcha(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            match = event.pattern_match
            if not match.group(1) or not match.group(2):
                timeout, attempts = await self._get_settings(self.owner_id)
                await event.reply(
                    f"تنظیمات فعلی:\n"
                    f"• مهلت: {timeout} ثانیه ({timeout//60} دقیقه)\n"
                    f"• تلاش‌ها: {attempts}\n\n"
                    f"استفاده: /setcaptcha [timeout] [attempts] یا تنظیم کپچا [timeout] [attempts]"
                )
                return
                
            try:
                timeout = int(match.group(1))
                attempts = int(match.group(2))
                if timeout < 60 or attempts < 1:
                    raise ValueError
                await self._update_settings(self.owner_id, timeout, attempts)
                await event.reply(f"✅ تنظیمات به‌روز شد:\n• مهلت: {timeout} ثانیه\n• تلاش‌ها: {attempts}")
            except:
                await event.reply("❌ مقادیر نامعتبر. استفاده: /setcaptcha [timeout_seconds] [max_attempts] یا تنظیم کپچا [timeout_seconds] [max_attempts]")

        @self.client.on(events.ChatAction())
        async def handle_new_member(event):
            # Check if the chat is active for this specific owner
            chat_id = str(event.chat_id)
            if not await self._is_chat_active_for_owner(chat_id):
                return
                
            # Only process user join events (not leave events)
            if not event.user_joined and not event.user_added:
                return
                
            # Get the bot's user ID
            try:
                me = await self.client.get_me()
                bot_id = str(me.id)
            except Exception as e:
                print(f"Error getting bot ID: {e}")
                return
            
            # Extract users depending on event type
            users = []
            
            if event.user_added:
                print("User added event detected")
                users = event.action.users
            elif event.user_joined:
                print("User joined event detected")
                if hasattr(event, 'user_id') and event.user_id:
                    users = [event.user_id]
            
            # Check for action attribute (MessageActionChatAddUser)
            if hasattr(event, 'action') and hasattr(event.action, 'users'):
                users = event.action.users
                print(f"Found users in action.users: {users}")
            
            # Extract from original_update if available
            if hasattr(event, 'original_update') and hasattr(event.original_update, 'message'):
                msg = event.original_update.message
                if hasattr(msg, 'from_id') and hasattr(msg.from_id, 'user_id'):
                    users.append(msg.from_id.user_id)
                    print(f"Found user_id in original_update: {msg.from_id.user_id}")
            
            # Extract from from_id as last resort
            if hasattr(event, 'action_message') and hasattr(event.action_message, 'from_id'):
                if hasattr(event.action_message.from_id, 'user_id'):
                    users.append(event.action_message.from_id.user_id)
                    print(f"Found user_id in action_message.from_id: {event.action_message.from_id.user_id}")
            
            # Remove duplicates
            users = list(set(users))
            print(f"Final extracted users: {users}")
            
            if not users:
                print("No users detected in the event")
                return
            
            if chat_id not in self.active_chats:
                self.active_chats[chat_id] = {'pending': {}}
            
            timeout, max_attempts = await self._get_settings(self.owner_id)
            
            for user in users:
                user_id = str(user)
                print(f"Processing user {user_id}")
                
                if user_id == bot_id or user_id == self.owner_id:
                    print(f"Skipping user {user_id} (bot or owner)")
                    continue
                    
                try:
                    # First get user entity
                    try:
                        print(f"Attempting to get entity for user {user_id}")
                        user_entity = await self.client.get_entity(int(user_id))
                    except ValueError:
                        # If it fails with int, try with string
                        print(f"Failed with int ID, trying string ID for user {user_id}")
                        user_entity = await self.client.get_entity(user_id)
                        print(f"Successfully got entity for user {user_id}")
                    except Exception as e:
                        print(f"Couldn't get entity for user {user_id}: {e}")
                        continue
                        
                    # Check bot permissions first
                    try:
                        print(f"Checking bot permissions in chat {event.chat_id}")
                        bot_permissions = await self.client.get_permissions(event.chat_id, me.id)
                        if not bot_permissions.ban_users:
                            print(f"Bot doesn't have permission to restrict users in chat {event.chat_id}")
                            await self.client.send_message(
                                event.chat_id,
                                "⚠️ ربات دسترسی محدود کردن کاربران را ندارد. لطفاً به ربات حقوق مدیریتی با دسترسی 'محدود کردن کاربران' بدهید."
                            )
                            continue
                        print("Bot has required permissions")
                    except Exception as e:
                        print(f"Failed to check bot permissions: {e}")
                        # Continue anyway, as the restriction might still work
                        
                    # Try to mute the user
                    print(f"Attempting to mute user {user_id}")
                    try:
                        await self.client.edit_permissions(
                            event.chat_id,
                            user_entity,
                            until_date=int(time.time()) + timeout + 60,
                            send_messages=False
                        )
                        print(f"Successfully muted user {user_id}")
                    except Exception as e:
                        print(f"Mute failed: {e}, trying integer IDs")
                        try:
                            await self.client.edit_permissions(
                                int(event.chat_id),
                                int(user_id),
                                send_messages=False
                            )
                            print(f"Successfully muted user {user_id} using integer IDs")
                        except Exception as e3:
                            print(f"All mute attempts failed for user {user_id}: {e3}")
                            # Skip to next user if we can't mute this one
                            continue
                
                    # Generate emoji captcha
                    captcha_data = self._generate_emoji_captcha()
                    
                    # Send reaction captcha
                    try:
                        user_name = getattr(user_entity, 'first_name', f"کاربر {user_id}")
                        captcha_message = await self.client.send_message(
                            event.chat_id,
                            f"🔒 **تأیید هویت کپچا برای {user_name}**\n\n"
                            f"برای اثبات انسان بودن خود، لطفاً با شکلک {captcha_data['correct_emoji']} به این پیام واکنش نشان دهید.\n\n"
                            f"شکلک‌های موجود: {' '.join(captcha_data['emojis'])}\n\n"
                            f"شما {max_attempts} تلاش و {timeout//60} دقیقه برای تکمیل این تأیید هویت دارید."
                        )
                        
                        msg_id = captcha_message.id
                        
                        # Store captcha info for verification
                        self.active_captchas[str(msg_id)] = {
                            'user_id': user_id,
                            'chat_id': chat_id,
                            'correct_emoji': captcha_data['correct_emoji'],
                            'join_time': time.time(),
                            'timeout': timeout,
                            'attempts': 0,
                            'max_attempts': max_attempts,
                            'owner_id': self.owner_id  # Store owner ID in captcha data
                        }
                        
                        # Also store in pending users
                        self.active_chats[chat_id]['pending'][user_id] = {
                            'msg_id': str(msg_id),
                            'join_time': time.time(),
                            'timeout': timeout,
                            'attempts': 0,
                            'max_attempts': max_attempts
                        }
                        
                        print(f"Successfully sent emoji captcha for user {user_id}")
                    except Exception as e:
                        print(f"Failed to send emoji captcha: {e}")
                        # If we can't create the captcha, unmute user
                        try:
                            await self.client.edit_permissions(event.chat_id, user_entity)
                            await self.client.send_message(
                                event.chat_id,
                                f"⚠️ ایجاد چالش تأیید هویت ممکن نبود. کاربر از حالت بی‌صدا خارج شد."
                            )
                        except Exception as unmute_error:
                            print(f"Failed to unmute user after captcha creation failure: {unmute_error}")
                except Exception as e:
                    print(f"Unexpected error in handle_new_member for user {user_id}: {e}")

        @self.client.on(events.Raw)
        async def reaction_handler(update):
            """Handle raw reaction updates for captcha verification"""
            # Check if this is a reaction update
            if not isinstance(update, UpdateMessageReactions):
                return
                
            print("Received a reaction update!")
            print("Message ID:", update.msg_id)
            print("update:", update)
            
            # Convert message ID to string for lookup
            msg_id = str(update.msg_id)
            
            # Check if this message has an active captcha
            if msg_id not in self.active_captchas:
                return
                
            # Verify this captcha belongs to this instance's owner
            if self.active_captchas[msg_id].get('owner_id') != self.owner_id:
                return
                
            # Get chat ID from peer
            chat_id = await self._get_chat_id_from_peer(update.peer)
            if not chat_id:
                print("Couldn't determine chat ID from peer")
                return
                
            captcha_data = self.active_captchas[msg_id]
            user_id = captcha_data['user_id']
            
            # Process reactions
            if not update.reactions or not hasattr(update.reactions, 'recent_reactions'):
                print("No recent_reactions data found")
                return
                
            # Look for the target user's reactions in recent_reactions
            for reaction in update.reactions.recent_reactions:
                # Check if we can determine who reacted
                if not hasattr(reaction, 'peer_id') or not reaction.peer_id:
                    continue
                    
                reactor_id = await self._get_user_id_from_peer(reaction.peer_id)
                if not reactor_id or reactor_id != user_id:
                    continue
                    
                # Get the emoji from the reaction
                user_emoji = None
                if hasattr(reaction, 'reaction'):
                    if isinstance(reaction.reaction, ReactionEmoji) and hasattr(reaction.reaction, 'emoticon'):
                        user_emoji = reaction.reaction.emoticon
                    elif isinstance(reaction.reaction, str):
                        user_emoji = reaction.reaction
                
                if not user_emoji:
                    print("Unable to determine user's emoji reaction")
                    continue
                    
                print(f"User {reactor_id} reacted with: {user_emoji}")
                print(f"Correct emoji is: {captcha_data['correct_emoji']}")
                
                # Check if correct
                if user_emoji == captcha_data['correct_emoji']:
                    # Correct answer - unmute user
                    try:
                        user_entity = await self.client.get_entity(int(user_id))
                        await self.client.edit_permissions(
                            int(chat_id),
                            user_entity,
                            send_messages=True
                        )
                        
                        # Send success message
                        await self.client.send_message(
                            int(chat_id),
                            f"✅ کاربر {user_entity.first_name if hasattr(user_entity, 'first_name') else user_id} تأیید هویت کپچا را با موفقیت انجام داد و اکنون می‌تواند صحبت کند!"
                        )
                        
                        # Edit the captcha message
                        try:
                            await self.client.edit_message(
                                int(chat_id),
                                int(msg_id),
                                f"✅ کپچا با موفقیت انجام شد! کاربر تأیید شد و اکنون می‌تواند صحبت کند."
                            )
                        except Exception as e:
                            print(f"Failed to edit message: {e}")
                    except Exception as e:
                        print(f"Failed to unmute user after correct captcha: {e}")
                        
                    # Clean up data
                    del self.active_captchas[msg_id]
                    if chat_id in self.active_chats and 'pending' in self.active_chats[chat_id]:
                        if user_id in self.active_chats[chat_id]['pending']:
                            del self.active_chats[chat_id]['pending'][user_id]
                    return  # Stop processing after successful verification
                else:
                    # Wrong answer - increment attempts
                    captcha_data['attempts'] += 1
                    if captcha_data['attempts'] >= captcha_data['max_attempts']:
                        # Too many failed attempts
                        await self.client.send_message(
                            int(chat_id),
                            f"❌ تأیید هویت کپچا پس از {captcha_data['attempts']} تلاش نادرست شکست خورد."
                        )
                        
                        # Edit the original message
                        try:
                            await self.client.edit_message(
                                int(chat_id),
                                int(msg_id),
                                f"❌ تأیید هویت کپچا شکست خورد. تعداد تلاش‌های نادرست بیش از حد مجاز بود."
                            )
                        except Exception as e:
                            print(f"Failed to edit message: {e}")
                        
                        # Clean up data
                        del self.active_captchas[msg_id]
                        if chat_id in self.active_chats and 'pending' in self.active_chats[chat_id]:
                            if user_id in self.active_chats[chat_id]['pending']:
                                del self.active_chats[chat_id]['pending'][user_id]
                    else:
                        # Still has attempts left
                        remaining = captcha_data['max_attempts'] - captcha_data['attempts']
                        
                        # Generate new captcha
                        new_captcha_data = self._generate_emoji_captcha()
                        
                        try:
                            user_entity = await self.client.get_entity(int(user_id))
                            user_name = getattr(user_entity, 'first_name', f"کاربر {user_id}")
                            
                            # Edit original message
                            try:
                                await self.client.edit_message(
                                    int(chat_id),
                                    int(msg_id),
                                    f"❌ واکنش نادرست. {remaining} تلاش باقی مانده است."
                                )
                            except Exception as e:
                                print(f"Failed to edit message: {e}")
                            
                            # Send new captcha message
                            new_captcha_message = await self.client.send_message(
                                int(chat_id),
                                f"🔄 **کپچای جدید برای {user_name}**\n\n"
                                f"برای اثبات انسان بودن خود، لطفاً با شکلک {new_captcha_data['correct_emoji']} به این پیام واکنش نشان دهید.\n\n"
                                f"شکلک‌های موجود: {' '.join(new_captcha_data['emojis'])}\n\n"
                                f"شما {remaining} تلاش و {captcha_data['timeout']//60} دقیقه زمان باقی‌مانده دارید."
                            )
                            
                            new_msg_id = new_captcha_message.id
                            
                            # Update captcha info for verification
                            self.active_captchas[str(new_msg_id)] = {
                                **captcha_data,
                                'correct_emoji': new_captcha_data['correct_emoji'],
                                'attempts': captcha_data['attempts'],  # Keep the attempt count
                                'owner_id': self.owner_id  # Maintain owner ID
                            }
                            
                            # Update pending data reference
                            if chat_id in self.active_chats and 'pending' in self.active_chats[chat_id]:
                                if user_id in self.active_chats[chat_id]['pending']:
                                    self.active_chats[chat_id]['pending'][user_id]['msg_id'] = str(new_msg_id)
                            
                            # Clean up old captcha data
                            del self.active_captchas[msg_id]
                        except Exception as e:
                            print(f"Failed to create new captcha after incorrect attempt: {e}")
                    return  # Stop processing after handling the reaction

        async def load_active_chats():
            async with aiosqlite.connect(self.db_path) as db:
                cursor = await db.execute(
                    "SELECT chat_id FROM active_chats WHERE owner_id = ?", 
                    (self.owner_id,)
                )
                rows = await cursor.fetchall()
                for row in rows:
                    self.active_chats[row[0]] = {'pending': {}}

        asyncio.create_task(load_active_chats())

        async def cleanup_pending():
            """Clean up expired captchas"""
            while True:
                await asyncio.sleep(60)
                current_time = time.time()
                
                # Check active captchas for timeouts
                for msg_id, captcha_data in list(self.active_captchas.items()):
                    # Only process captchas belonging to this owner
                    if captcha_data.get('owner_id') != self.owner_id:
                        continue
                        
                    if current_time - captcha_data['join_time'] > captcha_data['timeout']:
                        chat_id = captcha_data['chat_id']
                        user_id = captcha_data['user_id']
                        
                        try:
                            # Notify about timeout
                            await self.client.send_message(
                                int(chat_id),
                                f"⏰ زمان تأیید هویت کپچا برای کاربر {user_id} به پایان رسید."
                            )
                            
                            # Edit the original message
                            try:
                                await self.client.edit_message(
                                    int(chat_id),
                                    int(msg_id),
                                    f"⏰ این کپچا منقضی شده است. زمان تأیید هویت به پایان رسید."
                                )
                            except Exception as e:
                                print(f"Failed to edit message: {e}")
                        except Exception as e:
                            print(f"Failed to send timeout notification: {e}")
                            
                        # Clean up data
                        del self.active_captchas[msg_id]
                        if chat_id in self.active_chats and 'pending' in self.active_chats[chat_id]:
                            if user_id in self.active_chats[chat_id]['pending']:
                                del self.active_chats[chat_id]['pending'][user_id]

        asyncio.create_task(cleanup_pending())
