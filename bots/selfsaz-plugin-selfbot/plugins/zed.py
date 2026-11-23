# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# plugins/zed.py
from .base import Plugin
from telethon import events, functions, types
from telethon.errors import ChatAdminRequiredError, UserAdminInvalidError
import logging
import time
import asyncio
from collections import defaultdict

logger = logging.getLogger(__name__)

HELP = """  
🛡 **افزونه ضد خیانت ادمین‌ها** 🛡  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
• نظارت هوشمند بر اقدامات حذف کاربران توسط ادمین‌ها  
• تشخیص خودکار الگوهای مشکوک حذف دسته‌جمعی  
• تنزل رتبه یا مسدودسازی ادمین‌های خاطی  
• ارسال هشدارهای فارسی به مالک ربات  
• مدیریت لیست ادمین‌های مورد اعتماد  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی (انگلیسی با "/")**:  
• `/ab help`     ➔ نمایش راهنمای کامل  
• `/ab config`   ➔ نمایش تنظیمات فعلی  
• `/ab status`   ➔ وضعیت نظارت در گروه‌ها  
• `/ab trust [آیدی]`    ➔ افزودن ادمین مورد اعتماد  
• `/ab untrust [آیدی]`  ➔ حذف ادمین مورد اعتماد  
• `/ab threshold [عدد]` ➔ تنظیم آستانه حذف (پیش‌فرض: ۵ کاربر)  
• `/ab window [دقیقه]`  ➔ تنظیم پنجره زمانی (پیش‌فرض: ۵ دقیقه)  
• `/ab toggle [ویژگی]` ➔ تغییر وضعیت قابلیت‌ها  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
• **دستورات فارسی (بدون "/" با فاصله‌های اضافی)**:  
   `ضد  خیانت   راهنما`       ➔ نمایش راهنمای کامل  
   `ضد  خیانت   تنظیمات`     ➔ نمایش تنظیمات فعلی  
   `ضد  خیانت   وضعیت`       ➔ نمایش وضعیت نظارت  
   `ضد  خیانت   اعتماد   [آیدی]`        ➔ افزودن ادمین مورد اعتماد  
   `ضد  خیانت   لغو   اعتماد   [آیدی]`  ➔ حذف ادمین مورد اعتماد  
   `ضد  خیانت   آستانه   [عدد]`         ➔ تنظیم آستانه حذف  
   `ضد  خیانت   پنجره   [دقیقه]`         ➔ تنظیم پنجره زمانی  
   `ضد  خیانت   تغییر   [ویژگی]`        ➔ تغییر وضعیت ویژگی‌ها  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. تنظیم آستانه حساسیت:  
   `/ab threshold 5`  
   یا به صورت فارسی:  
   `ضد  خیانت   آستانه    5`  
2. فعال‌سازی مسدودسازی خودکار:  
   `/ab toggle ban`  
   یا:  
   `ضد  خیانت   تغییر    ban`  
3. افزودن ادمین معتمد:  
   `/ab trust 123456789`  
   یا:  
   `ضد  خیانت   اعتماد    123456789`  
4. مشاهده گزارشات:  
   `/ab status`  
   یا:  
   `ضد  خیانت   وضعیت`  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ آستانه پیش‌فرض: ۵ کاربر در ۵ دقیقه  
▫️ اقدامات موجود: مسدودسازی - تنزل رتبه  
▫️ پشتیبانی از چندین گروه همزمان  
▫️ سیستم اطلاع‌رسانی به پیام خصوصی مالک  
▫️ رابط مدیریت کاملاً فارسی  

⚠️ **هشدارهای مهم**:  
- نیاز به دسترسی **ادمین** در گروه‌ها  
- فقط برای کاربر مالک ربات فعال است  
- تغییرات بلافاصله اعمال می‌شود  
- از تنظیم آستانه مناسب برای جلوگیری از خطا استفاده کنید  
- ادمین‌های معتمد از بررسی豁免 می‌شوند  
"""

FARSI_MESSAGES = {
    # Command responses and help/config messages (see original definitions)
    "invalid_command": "❌ فرمت دستور نامعتبر است.",
    "monitoring_enabled": "🛡 نظارت فعال شد.",
    "monitoring_disabled": "🛡 نظارت غیرفعال شد.",
    "auto_ban_enabled": "🛡 مسدودسازی خودکار فعال شد.",
    "auto_ban_disabled": "🛡 مسدودسازی خودکار غیرفعال شد.",
    "auto_demote_enabled": "🛡 تنزل رتبه خودکار فعال شد.",
    "auto_demote_disabled": "🛡 تنزل رتبه خودکار غیرفعال شد.",
    "notify_groups_enabled": "🛡 اطلاع‌رسانی به گروه‌ها فعال شد.",
    "notify_groups_disabled": "🛡 اطلاع‌رسانی به گروه‌ها غیرفعال شد.",
    "provide_user_id": "❌ لطفا یک شناسه کاربری برای اعتماد/سلب اعتماد وارد کنید.",
    "admin_trusted": "✅ ادمین {} به لیست مورد اعتماد اضافه شد.",
    "admin_untrusted": "✅ ادمین {} از لیست مورد اعتماد حذف شد.",
    "admin_not_trusted": "❌ ادمین {} در لیست مورد اعتماد نیست.",
    "invalid_user_id": "❌ فرمت شناسه کاربری نامعتبر است.",
    "provide_threshold": "❌ لطفا یک عدد وارد کنید. آستانه فعلی: {}",
    "threshold_min": "❌ آستانه باید حداقل 2 باشد.",
    "threshold_set": "✅ آستانه حذف به {} کاربر تنظیم شد.",
    "provide_window": "❌ لطفا یک عدد به دقیقه وارد کنید. پنجره زمانی فعلی: {} دقیقه",
    "window_min": "❌ پنجره زمانی باید حداقل 1 دقیقه باشد.",
    "window_set": "✅ پنجره زمانی به {} دقیقه تنظیم شد.",
    "specify_toggle": "❌ لطفا مشخص کنید چه چیزی را تغییر دهید (monitoring/ban/demote/notify_groups).",
    "unknown_feature": "❌ ویژگی ناشناخته. از monitoring، ban، demote یا notify_groups استفاده کنید.",
    "no_actions": "🛡 در حال حاضر هیچ اقدام ادمینی تحت نظارت نیست.",
    
    # Alert messages and configuration/status texts (same as defined above)
    "betrayal_alert": "⚠️ **هشدار خیانت** ⚠️\n\n",
    "admin": "ادمین: {}\n",
    "chat": "گروه: {}\n",
    "removed_users": "{} کاربر را در {} دقیقه حذف کرده است\n\n",
    "actions_taken": "اقدامات انجام شده:\n",
    "admin_demoted": "✅ ادمین تنزل رتبه داده شد",
    "admin_banned": "✅ ادمین مسدود شد",
    "demotion_failed": "❌ تنزل رتبه ناموفق بود: {}",
    "ban_failed": "❌ مسدودسازی ناموفق بود: {}",
    "group_message": "⚠️ ادمین {} تعداد زیادی کاربر را حذف کرده و به عنوان خیانت احتمالی پرچم‌گذاری شده است. اقدامات لازم انجام شد.",
    
    "help_title": "🛡 **راهنمای افزونه ضد خیانت** 🛡",
    "commands": "**دستورات:**",
    "help_cmd": "• `/ab help` - نمایش این پیام راهنما",
    "config_cmd": "• `/ab config` - نمایش پیکربندی فعلی",
    "status_cmd": "• `/ab status` - نمایش وضعیت نظارت",
    "trust_cmd": "• `/ab trust <user_id>` - افزودن ادمین مورد اعتماد",
    "untrust_cmd": "• `/ab untrust <user_id>` - حذف ادمین مورد اعتماد",
    "threshold_cmd": "• `/ab threshold <number>` - تنظیم آستانه حذف",
    "window_cmd": "• `/ab window <minutes>` - تنظیم پنجره زمانی",
    "toggle_cmd": "• `/ab toggle <feature>` - تغییر وضعیت ویژگی‌ها (monitoring/ban/demote/notify_groups)",
    "features": "**ویژگی‌ها:**",
    "feature_1": "- نظارت بر اقدامات ادمین در گروه‌ها",
    "feature_2": "- تشخیص الگوهای مشکوک حذف دسته‌جمعی",
    "feature_3": "- تنزل رتبه و مسدودسازی خودکار ادمین‌های خائن",
    "feature_4": "- ارسال اطلاعیه‌های فارسی به پیام‌های ذخیره شده شما",
    
    "config_title": "🛡 **پیکربندی ضد خیانت** 🛡",
    "monitoring": "• نظارت: {}",
    "auto_demote": "• تنزل رتبه خودکار: {}",
    "auto_ban": "• مسدودسازی خودکار: {}",
    "notify_groups": "• اطلاع‌رسانی به گروه‌ها: {}",
    "removal_threshold": "• آستانه حذف: {} کاربر",
    "time_window": "• پنجره زمانی: {} دقیقه",
    "notification_chat": "• چت اطلاع‌رسانی: پیام خصوصی شما",
    "trusted_admins": "• ادمین‌های مورد اعتماد: {}",
    "enabled": "✅ فعال",
    "disabled": "❌ غیرفعال",
    
    "status_title": "🛡 **وضعیت نظارت ضد خیانت** 🛡",
    "chat_header": "📢 **{}**\n",
    "admin_removals": "• {}: {} حذف\n",
    "chat_error": "• خطا در دریافت اطلاعات چت {}: {}\n\n"
}

class AntiBetrayalPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.admin_actions = defaultdict(lambda: defaultdict(list))
        self.monitoring_enabled = True
        self.remove_threshold = 5  # Default: flag after 5 removals
        self.time_window = 300     # Default: within 5 minutes (300 seconds)
        self.trusted_admins = set()
        self.auto_ban = True
        self.auto_demote = True
        self.notify_groups = False  # Default: don't send notifications to groups

    async def initialize(self, me):
        await super().initialize(me)
        logger.info("AntiBetrayalPlugin initialized")

    async def handle_events(self):
        @self.client.on(events.ChatAction())
        async def monitor_removals(event):
            if not self.monitoring_enabled:
                return
                
            # Skip if not a user removal event
            if event.action_message and not isinstance(event.action_message.action, types.MessageActionChatDeleteUser):
                return
                
            # Get chat where the action occurred
            try:
                chat = await event.get_chat()
                if not chat.admin_rights and not chat.creator:
                    return  # Not an admin—can't act here
                    
                # Get admin who performed the action
                admin_id = None
                action_message = event.action_message
                if action_message:
                    admin_id = str(action_message.from_id.user_id) if hasattr(action_message.from_id, 'user_id') else None
                    
                # Skip if owner or trusted admin
                if admin_id == self.owner_id or admin_id in self.trusted_admins:
                    return
                    
                if admin_id and str(chat.id) and event.user_id:
                    # Record the removal action
                    current_time = time.time()
                    chat_id = str(chat.id)
                    self.admin_actions[chat_id][admin_id].append((current_time, event.user_id))
                    
                    # Check for suspicious activity
                    await self._check_betrayal_threshold(chat_id, admin_id, chat)
                    
            except Exception as e:
                logger.error(f"Error monitoring removals: {str(e)}")

        # English command handler (/ab ...)
        @self.client.on(events.NewMessage(pattern=r'^/ab\s+(?:help|config|status|trust|untrust|threshold|window|toggle)'))
        async def ab_command_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            cmd_parts = event.raw_text.strip().split()
            if len(cmd_parts) < 2:
                await event.reply(FARSI_MESSAGES["invalid_command"])
                return
                
            sub_command = cmd_parts[1].lower()
            
            if sub_command == "help":
                await self._send_help(event)
            elif sub_command == "config":
                await self._send_config(event)
            elif sub_command == "status":
                await self._send_status(event)
            elif sub_command == "trust":
                await self._handle_trust(event, cmd_parts, True)
            elif sub_command == "untrust":
                await self._handle_trust(event, cmd_parts, False)
            elif sub_command == "threshold":
                await self._set_threshold(event, cmd_parts)
            elif sub_command == "window":
                await self._set_window(event, cmd_parts)
            elif sub_command == "toggle":
                await self._toggle_settings(event, cmd_parts)

        # Persian command handlers (commands without "/" and with extra spaces)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+راهنما'))
        async def ab_help_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            await self._send_help(event)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+تنظیمات'))
        async def ab_config_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            await self._send_config(event)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+وضعیت'))
        async def ab_status_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            await self._send_status(event)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+اعتماد\s+(.+)'))
        async def ab_trust_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Construct pseudo command: mimic English "/ab trust <user_id>"
            cmd_parts = ["dummy", "trust", event.pattern_match.group(1).strip()]
            await self._handle_trust(event, cmd_parts, True)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+لغو\s+اعتماد\s+(.+)'))
        async def ab_untrust_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Mimic English "/ab untrust <user_id>"
            cmd_parts = ["dummy", "untrust", event.pattern_match.group(1).strip()]
            await self._handle_trust(event, cmd_parts, False)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+آستانه\s+(.+)'))
        async def ab_threshold_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Mimic English "/ab threshold <number>"
            cmd_parts = ["dummy", "threshold", event.pattern_match.group(1).strip()]
            await self._set_threshold(event, cmd_parts)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+پنجره\s+(.+)'))
        async def ab_window_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Mimic English "/ab window <minutes>"
            cmd_parts = ["dummy", "window", event.pattern_match.group(1).strip()]
            await self._set_window(event, cmd_parts)

        @self.client.on(events.NewMessage(pattern=r'^ضد\s+خیانت\s+تغییر\s+(.+)'))
        async def ab_toggle_farsi(event):
            if str(event.sender_id) != self.owner_id:
                return
            # Mimic English "/ab toggle <feature>"
            cmd_parts = ["dummy", "toggle", event.pattern_match.group(1).strip()]
            await self._toggle_settings(event, cmd_parts)

    async def _check_betrayal_threshold(self, chat_id, admin_id, chat):
        """Check if admin has exceeded removal thresholds"""
        current_time = time.time()
        admin_actions = self.admin_actions[chat_id][admin_id]
        
        # Filter actions within time window
        recent_actions = [action for action in admin_actions 
                         if current_time - action[0] <= self.time_window]
        
        # Update the list with only recent actions
        self.admin_actions[chat_id][admin_id] = recent_actions
        
        # Check if threshold exceeded
        if len(recent_actions) >= self.remove_threshold:
            try:
                # Get admin info for reporting
                admin_name = "Unknown Admin"
                try:
                    admin = await self.client.get_entity(int(admin_id))
                    admin_name = f"{admin.first_name} {admin.last_name or ''}"
                except Exception as e:
                    logger.warning(f"Could not fetch admin entity for ID {admin_id}: {str(e)}")
                    admin_name = f"Admin (ID: {admin_id})"
                
                # Create betrayal report in Farsi
                warning_msg = FARSI_MESSAGES["betrayal_alert"]
                warning_msg += FARSI_MESSAGES["admin"].format(admin_name)
                warning_msg += FARSI_MESSAGES["chat"].format(getattr(chat, 'title', f'Chat {chat_id}'))
                warning_msg += FARSI_MESSAGES["removed_users"].format(len(recent_actions), self.time_window//60)
                
                # Take action based on settings
                actions_taken = []
                
                if self.auto_demote:
                    try:
                        await self.client(functions.channels.EditAdminRequest(
                            channel=chat.id,
                            user_id=int(admin_id),
                            admin_rights=types.ChatAdminRights(
                                change_info=False,
                                post_messages=False,
                                edit_messages=False,
                                delete_messages=False,
                                ban_users=False,
                                invite_users=False,
                                pin_messages=False,
                                add_admins=False,
                                manage_call=False
                            ),
                            rank=""
                        ))
                        actions_taken.append(FARSI_MESSAGES["admin_demoted"])
                    except Exception as e:
                        logger.error(f"Demotion failed: {str(e)}")
                        actions_taken.append(FARSI_MESSAGES["demotion_failed"].format(str(e)))
                
                if self.auto_ban:
                    try:
                        await self.client(functions.channels.EditBannedRequest(
                            channel=chat.id,
                            participant=int(admin_id),
                            banned_rights=types.ChatBannedRights(
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
                        ))
                        actions_taken.append(FARSI_MESSAGES["admin_banned"])
                    except Exception as e:
                        logger.error(f"Ban failed: {str(e)}")
                        actions_taken.append(FARSI_MESSAGES["ban_failed"].format(str(e)))
                
                # Add action summary to report in Farsi
                warning_msg += FARSI_MESSAGES["actions_taken"]
                for action in actions_taken:
                    warning_msg += f"- {action}\n"
                
                # Send to owner's private messages
                try:
                    await self.client.send_message(self.owner_id, warning_msg)
                except Exception as e:
                    logger.error(f"Failed to send notification to owner: {str(e)}")
                
                # Send Farsi message to the group if notify_groups is enabled
                if self.notify_groups:
                    try:
                        await self.client.send_message(
                            chat.id,
                            FARSI_MESSAGES["group_message"].format(admin_name)
                        )
                    except Exception as e:
                        logger.error(f"Failed to send message to chat: {str(e)}")
                
                # Reset the admin's actions
                self.admin_actions[chat_id][admin_id] = []
                
            except Exception as e:
                logger.error(f"Error handling betrayal: {str(e)}")
    
    async def _send_help(self, event):
        """Send help message for AntiBetrayalPlugin commands in Farsi"""
        help_text = f"""
{FARSI_MESSAGES["help_title"]}

{FARSI_MESSAGES["commands"]}
{FARSI_MESSAGES["help_cmd"]}
{FARSI_MESSAGES["config_cmd"]}
{FARSI_MESSAGES["status_cmd"]}
{FARSI_MESSAGES["trust_cmd"]}
{FARSI_MESSAGES["untrust_cmd"]}
{FARSI_MESSAGES["threshold_cmd"]}
{FARSI_MESSAGES["window_cmd"]}
{FARSI_MESSAGES["toggle_cmd"]}

{FARSI_MESSAGES["features"]}
{FARSI_MESSAGES["feature_1"]}
{FARSI_MESSAGES["feature_2"]}
{FARSI_MESSAGES["feature_3"]}
{FARSI_MESSAGES["feature_4"]}
"""
        await event.reply(help_text)
    
    async def _send_config(self, event):
        """Send current configuration values in Farsi"""
        config = f"""
{FARSI_MESSAGES["config_title"]}

{FARSI_MESSAGES["monitoring"].format(FARSI_MESSAGES["enabled"] if self.monitoring_enabled else FARSI_MESSAGES["disabled"])}
{FARSI_MESSAGES["auto_demote"].format(FARSI_MESSAGES["enabled"] if self.auto_demote else FARSI_MESSAGES["disabled"])}
{FARSI_MESSAGES["auto_ban"].format(FARSI_MESSAGES["enabled"] if self.auto_ban else FARSI_MESSAGES["disabled"])}
{FARSI_MESSAGES["notify_groups"].format(FARSI_MESSAGES["enabled"] if self.notify_groups else FARSI_MESSAGES["disabled"])}
{FARSI_MESSAGES["removal_threshold"].format(self.remove_threshold)}
{FARSI_MESSAGES["time_window"].format(self.time_window//60)}
{FARSI_MESSAGES["notification_chat"]}
{FARSI_MESSAGES["trusted_admins"].format(len(self.trusted_admins))}
"""
        await event.reply(config)
    
    async def _send_status(self, event):
        """Show monitoring status including admin actions in Farsi"""
        if not self.admin_actions:
            await event.reply(FARSI_MESSAGES["no_actions"])
            return
            
        status = f"{FARSI_MESSAGES['status_title']}\n\n"
        for chat_id, admins in self.admin_actions.items():
            try:
                chat_name = f"Chat {chat_id}"
                try:
                    chat = await self.client.get_entity(int(chat_id))
                    chat_name = getattr(chat, 'title', chat_name)
                except Exception:
                    pass
                status += FARSI_MESSAGES["chat_header"].format(chat_name)
                for admin_id, actions in admins.items():
                    if actions:
                        admin_name = f"Admin {admin_id}"
                        try:
                            admin = await self.client.get_entity(int(admin_id))
                            admin_name = f"{admin.first_name} {admin.last_name or ''}"
                        except Exception:
                            pass
                        status += FARSI_MESSAGES["admin_removals"].format(admin_name, len(actions))
                status += "\n"
            except Exception as e:
                status += FARSI_MESSAGES["chat_error"].format(chat_id, str(e))
        await event.reply(status)
    
    async def _handle_trust(self, event, cmd_parts, add_trust):
        """Add or remove trusted admins with Farsi messages"""
        if len(cmd_parts) < 3:
            await event.reply(FARSI_MESSAGES["provide_user_id"])
            return
        try:
            user_id = str(int(cmd_parts[2]))  # Validate numeric ID
            if add_trust:
                self.trusted_admins.add(user_id)
                await event.reply(FARSI_MESSAGES["admin_trusted"].format(user_id))
            else:
                if user_id in self.trusted_admins:
                    self.trusted_admins.remove(user_id)
                    await event.reply(FARSI_MESSAGES["admin_untrusted"].format(user_id))
                else:
                    await event.reply(FARSI_MESSAGES["admin_not_trusted"].format(user_id))
        except ValueError:
            await event.reply(FARSI_MESSAGES["invalid_user_id"])
    
    async def _set_threshold(self, event, cmd_parts):
        """Set the removal threshold with Farsi messages"""
        if len(cmd_parts) < 3:
            await event.reply(FARSI_MESSAGES["provide_threshold"].format(self.remove_threshold))
            return
        try:
            threshold = int(cmd_parts[2])
            if threshold < 2:
                await event.reply(FARSI_MESSAGES["threshold_min"])
                return
            self.remove_threshold = threshold
            await event.reply(FARSI_MESSAGES["threshold_set"].format(threshold))
        except ValueError:
            await event.reply(FARSI_MESSAGES["invalid_user_id"])
    
    async def _set_window(self, event, cmd_parts):
        """Set the time window in minutes with Farsi messages"""
        if len(cmd_parts) < 3:
            await event.reply(FARSI_MESSAGES["provide_window"].format(self.time_window//60))
            return
        try:
            minutes = int(cmd_parts[2])
            if minutes < 1:
                await event.reply(FARSI_MESSAGES["window_min"])
                return
            self.time_window = minutes * 60  # Convert to seconds
            await event.reply(FARSI_MESSAGES["window_set"].format(minutes))
        except ValueError:
            await event.reply(FARSI_MESSAGES["invalid_user_id"])
    
    async def _toggle_settings(self, event, cmd_parts):
        """Toggle various plugin settings with Farsi messages"""
        if len(cmd_parts) < 3:
            await event.reply(FARSI_MESSAGES["specify_toggle"])
            return
        feature = cmd_parts[2].lower()
        if feature == "monitoring":
            self.monitoring_enabled = not self.monitoring_enabled
            await event.reply(FARSI_MESSAGES["monitoring_enabled"] if self.monitoring_enabled else FARSI_MESSAGES["monitoring_disabled"])
        elif feature == "ban":
            self.auto_ban = not self.auto_ban
            await event.reply(FARSI_MESSAGES["auto_ban_enabled"] if self.auto_ban else FARSI_MESSAGES["auto_ban_disabled"])
        elif feature == "demote":
            self.auto_demote = not self.auto_demote
            await event.reply(FARSI_MESSAGES["auto_demote_enabled"] if self.auto_demote else FARSI_MESSAGES["auto_demote_disabled"])
        elif feature == "notify_groups":
            self.notify_groups = not self.notify_groups
            await event.reply(FARSI_MESSAGES["notify_groups_enabled"] if self.notify_groups else FARSI_MESSAGES["notify_groups_disabled"])
        else:
            await event.reply(FARSI_MESSAGES["unknown_feature"])
