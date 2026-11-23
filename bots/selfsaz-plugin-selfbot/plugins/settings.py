# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
from datetime import datetime
from typing import Dict, Any
from .base import Plugin
from telethon import events, Button
import asyncio
from db import (
    get_markread_pv, get_markread_group, get_markread_channel,
    get_online_mode, get_timebio_settings, get_time_pfp_settings,
    get_anti_login_status, get_text_format, get_action_states,
    get_dl_usage, get_muted_users_by_owner, get_pending_sessions,
    get_whitelisted_sessions, get_tos_acceptance
)

logger = logging.getLogger(__name__)
HELP = """  
🎛️ **پنل مدیریت تنظیمات پیشرفته** 🎛️  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • نمایش جامع تنظیمات حریم خصوصی و امنیتی  
  • مدیریت ظاهر حساب (بیوگرافی زمانی - قالب متن)  
  • نظارت بر وضعیت آنلاین و فعالیت‌های اخیر  
  • آمار مصرف منابع و تاریخچه دانلود  
  • کنترل نشست‌های فعال و دستگاه‌های متصل  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات کلیدی**:  

  • **English:**  
       `/settings` ➔ نمایش داشبورد مدیریت تنظیمات  

  • **فارسی (بدون /):**  
       `تنظیمات` ➔ نمایش داشبورد مدیریت تنظیمات  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **روش استفاده**:  
1. ارسال دستور `/settings` یا `تنظیمات` در چت خصوصی  
2. مشاهده تمام تنظیمات در قالب طبقه‌بندی شده  
3. استفاده از دکمه‌های تعاملی برای:  
   - بروزرسانی لحظه‌ای آمار  
   - تغییر تنظیمات پیشرفته  
   - مشاهده آمار مصرف  

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬  
⚙️ **مشخصات فنی**:  
▫️ قالب نمایش: طبقه‌بندی ۵ بخش اصلی  
▫️ نمادهای وضعیت: ✅/❌ با زمان‌بندی دقیق  
▫️ دکمه‌های تعاملی: بروزرسانی - تغییر - بستن  
▫️ پشتیبانی از فرمت‌های متن: Markdown  

⚠️ **هشدارهای مهم**:  
  - دسترسی انحصاری برای مالک ربات  
  - تغییرات از طریق دکمه‌ها نیاز به تایید دارد  
  - اطلاعات حساس امنیتی در این بخش نمایش داده می‌شود  
  - عدم دستکاری تنظیمات بدون آگاهی کامل  
"""  

class SettingsPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.last_settings_msg = None
        self.auto_refresh = False
        
    async def get_all_settings(self) -> Dict[str, Any]:
        """Fetch all user settings and stats from database with improved error handling"""
        try:
            online_mode = await get_online_mode(self.owner_id) or {"enabled": False, "last_active": 0}
            timebio = await get_timebio_settings(self.owner_id) or {"enabled": False, "bio_text": ""}
            timepfp = await get_time_pfp_settings(self.owner_id) or {
                "enabled": False,
                "font_size": 12,
                "font_color": "#000000",
                "position": "center"
            }
            text_format = await get_text_format(self.owner_id) or {
                "bold": False, "italic": False, "underline": False,
                "strike": False, "code": False, "pre": False
            }
            typing_states = await get_action_states(self.owner_id) or []
            dl_usage = await get_dl_usage(self.owner_id) or {"last": 0, "daily": 0}
            
            return {
                # Privacy Settings
                'privacy': {
                    'markread': {
                        'pv': await get_markread_pv(self.owner_id),
                        'group': await get_markread_group(self.owner_id),
                        'channel': await get_markread_channel(self.owner_id)
                    },
                    'muted_users': len(await get_muted_users_by_owner(self.owner_id)),
                    'anti_login': await get_anti_login_status(self.owner_id)
                },
                
                # Appearance Settings
                'appearance': {
                    'timebio': {
                        'enabled': timebio['enabled'],
                        'text': timebio['bio_text']
                    },
                    'timepfp': {
                        'enabled': timepfp['enabled'],
                        'font_size': timepfp['font_size'],
                        'color': timepfp['font_color'],
                        'position': timepfp['position']
                    },
                    'text_format': text_format
                },
                
                # Status Settings
                'status': {
                    'online_mode': online_mode['enabled'],
                    'last_online': online_mode['last_active'],
                    'typing_states': len(typing_states)
                },
                
                # Security Settings
                'security': {
                    'pending_sessions': len(await get_pending_sessions(self.owner_id)),
                    'whitelisted_sessions': len(await get_whitelisted_sessions(self.owner_id)),
                    'tos_accepted': await get_tos_acceptance(self.owner_id)
                },
                
                # Usage Stats
                'usage': {
                    'dl_usage': {
                        'last': dl_usage['last'],
                        'daily': dl_usage['daily']
                    }
                }
            }
        except Exception as e:
            logger.error(f"Error fetching settings: {str(e)}")
            raise

    def format_settings(self, settings: Dict[str, Any]) -> str:
        """Format settings with improved layout and emoji indicators"""
        def emoji_status(x): return '✅' if x else '❌'
        def time_fmt(ts): return "غیرفعال" if ts == 0 else datetime.fromtimestamp(ts).strftime("%Y-%m-%d %H:%M:%S")
        
        sections = []
        
        # Header
        sections.append("🎛️ **پنل تنظیمات کاربر** 🎛️\n")
        
        try:
            # Privacy Section
            privacy = settings['privacy']
            sections.append("🔐 **حریم خصوصی**\n"
                          f"┌ خواندن PV: {emoji_status(privacy['markread']['pv'])}\n"
                          f"├ خواندن گروه: {emoji_status(privacy['markread']['group'])}\n"
                          f"├ خواندن کانال: {emoji_status(privacy['markread']['channel'])}\n"
                          f"├ کاربران بی‌صدا: {privacy['muted_users']} 🔇\n"
                          f"└ ضد ورود: {emoji_status(privacy['anti_login'])}")
            
            # Appearance Section
            appearance = settings['appearance']
            sections.append("🎨 **شخصی‌سازی ظاهر**\n"
                          f"┌ بیو زمان: {emoji_status(appearance['timebio']['enabled'])} | {appearance['timebio']['text']}\n"
                          f"├ پروفایل زمان: {emoji_status(appearance['timepfp']['enabled'])}\n"
                          f"│  └ [فونت: {appearance['timepfp']['font_size']}px | رنگ: {appearance['timepfp']['color']}]\n"
                          f"└ قالب‌بندی متن:\n"
                          f"   ├ بولد: {emoji_status(appearance['text_format']['bold'])} | ایتالیک: {emoji_status(appearance['text_format']['italic'])}\n"
                          f"   ├ زیرخط: {emoji_status(appearance['text_format']['underline'])} | خط‌خورده: {emoji_status(appearance['text_format']['strike'])}\n"
                          f"   └ کد: {emoji_status(appearance['text_format']['code'])} | پیش‌فرمت: {emoji_status(appearance['text_format']['pre'])}")
            
            # Status Section
            status = settings['status']
            sections.append("📊 **وضعیت**\n"
                          f"┌ حالت آنلاین: {emoji_status(status['online_mode'])}\n"
                          f"├ آخرین آنلاین: {time_fmt(status['last_online'])}\n"
                          f"└ تایپ‌های فعال: {status['typing_states']} ⌨️")
            
            # Security Section
            security = settings['security']
            sections.append("🛡️ **امنیت**\n"
                          f"┌ نشست‌های در انتظار: {security['pending_sessions']} ⏳\n"
                          f"├ دستگاه‌های مجاز: {security['whitelisted_sessions']} 📱\n"
                          f"└ پذیرش قوانین: {emoji_status(security['tos_accepted'])}")
            
            # Usage Stats Section
            usage = settings['usage']['dl_usage']
            sections.append("📈 **آمار مصرف**\n"
                          f"┌ آخرین دانلود: {time_fmt(usage['last'])}\n"
                          f"└ دانلود روزانه: {usage['daily']} 📥/روز")
            
        except KeyError as e:
            logger.error(f"Error formatting settings: Missing key {str(e)}")
            return "❌ خطا در نمایش تنظیمات: داده‌های ناقص"
            
        return "\n\n".join(sections)

    async def get_settings_buttons(self) -> list:
        """Create inline buttons for settings management"""
        return [
            [Button.inline("🔄 بروزرسانی", "refresh_settings"),
             Button.inline("⚙️ تغییر تنظیمات", "change_settings")],
            [Button.inline("📊 آمار پیشرفته", "advanced_stats"),
             Button.inline("❌ بستن", "close_settings")]
        ]

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:[!/](?:settings)|تنظیمات)$', outgoing=True))
        async def settings_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                settings = await self.get_all_settings()
                message = self.format_settings(settings)
                buttons = await self.get_settings_buttons()
                
                # Delete previous settings message if it exists
                if self.last_settings_msg:
                    try:
                        await self.last_settings_msg.delete()
                    except:
                        pass
                
                self.last_settings_msg = await event.respond(
                    message,
                    buttons=buttons,
                    parse_mode='md'
                )
                
                # Delete the command message
                await event.delete()
                
            except Exception as e:
                logger.error(f"Settings error: {str(e)}")
                await event.reply("❌ خطا در دریافت تنظیمات")

        @self.client.on(events.CallbackQuery(pattern=r'^refresh_settings$'))
        async def refresh_callback(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            try:
                settings = await self.get_all_settings()
                message = self.format_settings(settings)
                buttons = await self.get_settings_buttons()
                
                await event.edit(
                    message,
                    buttons=buttons,
                    parse_mode='md'
                )
                await event.answer("✅ تنظیمات بروز شد")
                
            except Exception as e:
                logger.error(f"Refresh error: {str(e)}")
                await event.answer("❌ خطا در بروزرسانی تنظیمات", alert=True)

        @self.client.on(events.CallbackQuery(pattern=r'^close_settings$'))
        async def close_callback(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            await event.delete()
            self.last_settings_msg = None

    async def initialize(self, me):
        self.me = me
        logger.info("[Settings] Plugin initialized successfully")
