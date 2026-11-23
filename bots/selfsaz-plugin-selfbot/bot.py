# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import sys
import asyncio
import logging
import time
import random
from telethon import TelegramClient, events, Button
from telethon.errors import UserNotParticipantError
from telethon.tl.functions.channels import GetParticipantRequest
from config import API_ID, API_HASH, BOT_TOKEN, PERSIAN_NUMERALS, ENGLISH_NUMERALS, FORCE_JOIN_CHANNEL_ID, FORCE_JOIN_CHANNEL_USERNAME
from core import SessionManager, UserClient, convert_persian_numbers
from db import (
    init_db, get_session, save_session,
    save_user_credentials, get_user_credentials,
    save_registration_progress, get_registration_progress, delete_registration_progress,
    save_tos_acceptance, get_tos_acceptance, clear_temp_credentials, save_temp_api_id, get_temp_api_id
)
from support_system import SupportSystem
from bug_report import BugReportSystem, BugStatus, BugPriority
import importlib.util
from plugins.globalban import is_banned
from coins_db import coin_db
from urllib.parse import parse_qs
from admin_coin import AdminCoinManager
from referal import ReferralListener
import secrets
from gamble import GambleListener
from datetime import datetime, timedelta




# Configure logging
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)
logger = logging.getLogger(__name__)

admin_id = 7150795159
bot = TelegramClient(os.environ.get("STRING_SESSION", ""), API_ID, API_HASH)
session_manager = SessionManager()
bug_system = BugReportSystem(bot=bot, admin_id=7150795159)  # ادمین عددی
support_system = SupportSystem(bot=bot, admin_id=7150795159)

# Dictionary to hold clients pending 2FA password input
pending_password_users = {}  # key: user_id (int) -> UserClient

WELCOME_MESSAGE = """
🌟 **به ربات سلف ساز | Self Saz خوش آمدید!** 🌟

این ربات به شما کمک می‌کند تا به راحتی اکانت تلگرام خود را مدیریت و سلف‌سازی کنید.

📱 **امکانات اصلی سلف ساز**:
• مدیریت حرفه‌ای حساب کاربری
• ساخت سلف‌های امن و سریع
• امنیت بالا با رمزنگاری پیشرفته
• پشتیبانی ۲۴ ساعته

🔐 **امنیت و حریم خصوصی در سلف ساز**:
تمامی اطلاعات شما با بالاترین استانداردهای امنیتی محافظت می‌شوند.

برای شروع، لطفاً یکی از گزینه‌های زیر را انتخاب کنید:
"""

TERMS_OF_SERVICE = """
📜 **قوانین و شرایط استفاده از ربات سلف ساز | Self Saz**

1️⃣ **پذیرش قوانین**:
• با استفاده از ربات سلف ساز، شما این شرایط را می‌پذیرید.
• در صورت عدم موافقت، لطفاً از ربات استفاده نکنید.

2️⃣ **استفاده مسئولانه**:
• استفاده قانونی و اخلاقی از ربات سلف ساز الزامی است.
• سوءاستفاده منجر به مسدودیت می‌شود.

3️⃣ **حریم خصوصی**:
• اطلاعات شخصی شما در سلف ساز محرمانه می‌ماند.
• داده‌های شما با اشخاص ثالث به اشتراک گذاشته نمی‌شود.

4️⃣ **تغییرات در قوانین**:
• امکان تغییر قوانین در هر زمان وجود دارد.
• ادامه استفاده به معنای پذیرش تغییرات است.

5️⃣ **مسئولیت‌ها**:
• مسئولیت تمام فعالیت‌ها با کاربر است.
• حساب‌های متخلف مسدود خواهند شد.

6️⃣ **محدودیت‌ها**:
• امکان محدودیت خدمات بنا به دلایل فنی.
• حق تعلیق سرویس محفوظ است.

7️⃣ **حذف حساب**:
• مسئولیتی در قبال حذف حساب نداریم.
• پشتیبان‌گیری از داده‌ها توصیه می‌شود.

🔔 با کلیک روی "موافقت می‌کنم"، این شرایط را می‌پذیرید.
"""

REGISTRATION_STEPS = """
📝 **مراحل ثبت نام در سلف ساز** (مرحله {current}/3)

{steps}

⚡️ مرحله فعلی: {current_step}
"""

REGISTRATION_STEPS_TEMPLATE = """
1. دریافت API ID {step1_status}
2. دریافت API Hash {step2_status}
3. تأیید شماره تلفن {step3_status}
"""

ERROR_MESSAGES = {
    "PHONE_NUMBER_INVALID": """
❌ **خطا در شماره تلفن**

شماره تلفن وارد شده نامعتبر است.
لطفاً در فرمت صحیح وارد کنید:
▫️ مثال: +989123456789

🔄 دوباره تلاش کنید.
    """,
    "PHONE_NUMBER_BANNED": """
⛔️ **شماره تلفن مسدود شده**

متأسفانه این شماره تلفن توسط تلگرام مسدود شده است.
لطفاً از شماره دیگری استفاده کنید.

📞 برای پشتیبانی با ما تماس بگیرید.
    """,
    "API_ERROR": """
⚠️ **خطا در اطلاعات API**

اطلاعات API وارد شده نامعتبر است.
لطفاً مراحل زیر را دنبال کنید:
1. به my.telegram.org مراجعه کنید
2. وارد حساب خود شوید
3. اطلاعات API را دریافت کنید
4. دوباره تلاش کنید

🔄 برای شروع مجدد از /register استفاده کنید.
    """
}

SUCCESS_MESSAGES = {
    "registration": """
🎉 **ثبت نام در سلف ساز با موفقیت انجام شد!**

✅ اطلاعات API ذخیره شد  
✅ حساب شما تأیید شد  
✅ دسترسی‌های لازم اعطا شد  

📱 اکنون می‌توانید از تمام امکانات ربات سلف ساز استفاده کنید.
برای شروع، منوی اصلی را بررسی کنید.
    """,
    "login": """
🌟 **ورود موفقیت‌آمیز به سلف ساز!**

خوش آمدید. حساب شما با موفقیت متصل شد.  
⌛️ **دوره آزمایشی ۱۵ دقیقه‌ای شما آغاز شد.**  
اگر در این بازه هیچ دستوری ارسال نکنید، ربات خودکار خارج خواهد شد.

🎫 **نحوه فعال‌سازی لایسنس**  
برای تبدیل این دوره آزمایشی به لایسنس دائمی یا چندماهه، کد لایسنس خود را با یکی از دستورات زیر وارد کنید:  
• `/redeem <license_code>`  
• `فعالسازی <license_code>`  

✍️ مثال:  
/redeem ABCD1234EFGH5678

🔻 برای دسترسی به پنل مدیریت، دستور زیر را ارسال کنید:  
`/panel`
    """
}

LOADING_MESSAGES = [
    "درحال پردازش ⏳ | سلف ساز",
    "لطفاً صبر کنید ⌛️ | سلف ساز",
    "درحال اتصال 🔄 | سلف ساز",
    "درحال تأیید ✨ | سلف ساز"
]

help_text = """\
📚 **راهنمای کامل دستورات ربات**
...
"""

@bot.on(events.NewMessage)
@bot.on(events.CallbackQuery)
async def global_ban_guard(event):
    user_id = str(event.sender_id)
    if is_banned(user_id):
        try:
            await event.respond(
                "🚫 شما توسط ادمین از استفاده از این ربات محروم شده‌اید.\n"
                "برای رفع مشکل با [@Mohammad777777777777777777](https://t.me/Mohammad777777777777777777) تماس بگیرید."
            )
        except Exception:
            pass
        raise events.StopPropagation

def get_plugins():
    plugins = [
        f[:-3] for f in os.listdir("plugins")
        if f.endswith(".py") and f != "__init__.py" and not f.startswith("__")
    ]
    return sorted(plugins, key=lambda x: x.lower())

def get_plugin_help(plugin_name):
    try:
        spec = importlib.util.spec_from_file_location(
            f"plugins.{plugin_name}", 
            f"plugins/{plugin_name}.py"
        )
        module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(module)
        return getattr(module, "HELP", "❌ راهنمایی برای این پلاگین موجود نیست")
    except Exception as e:
        return f"⚠️ خطا در بارگذاری پلاگین: {str(e)}"

def create_main_keyboard():
    return [
        # Registration & Help
        [ Button.inline("🔐 ثبت نام", b"register"),
          Button.inline("📚 راهنما", b"help_menu") ],

        # Support & About
        [ Button.inline("📞 پشتیبانی", b"support"),
          Button.inline("ℹ️ درباره ما", b"about") ],

        # Tutorial
        [ Button.inline("🎬 آموزش تصویری", b"tutorial") ],

        # Support Group & Channel Links
        [ Button.url("👥 گروه پشتیبانی", "https://t.me/TelebotCraftChat"),
          Button.url("📢 کانال ما",     "https://t.me/TelebotCraft") ],

        # Bug Report
        [ Button.inline("🐛 گزارش مشکل", b"report_bug") ],

        # Account, License Status & How to Get Coins
        [ Button.inline("👤 حساب من",        data=b"my_account"),
          Button.inline("⏳ وضعیت لایسنس",   data=b"license_status"),
          Button.inline("🎯 نحوه دریافت سکه", data=b"how_get_coins") ],

        # License Purchase
        [
          Button.inline("💎 لایسنس ۱‌ماهه", data=b"buy?duration=1"),
          Button.inline("💎 لایسنس ۲‌ماهه", data=b"buy?duration=2"),
          Button.inline("💎 لایسنس ۳‌ماهه", data=b"buy?duration=3"),
        ]
    ]

def create_enhanced_persian_keyboard():
    return [
        [
            Button.inline(f"🔢 {PERSIAN_NUMERALS[7]}", str(7).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[8]}", str(8).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[9]}", str(9).encode())
        ],
        [
            Button.inline(f"🔢 {PERSIAN_NUMERALS[4]}", str(4).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[5]}", str(5).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[6]}", str(6).encode())
        ],
        [
            Button.inline(f"🔢 {PERSIAN_NUMERALS[1]}", str(1).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[2]}", str(2).encode()),
            Button.inline(f"🔢 {PERSIAN_NUMERALS[3]}", str(3).encode())
        ],
        [
            Button.inline(f"🔢 {PERSIAN_NUMERALS[0]}", str(0).encode()),
            Button.inline("✅ تأیید",    b"submit_code"),
            Button.inline("🔄 پاک کردن", b"clear_code")
        ]
    ]


async def show_loading_message(event, message_index=0):
    msg = await event.respond(LOADING_MESSAGES[message_index])
    for i in range(3):
        await asyncio.sleep(0.5)
        await msg.edit(LOADING_MESSAGES[(message_index + i + 1) % len(LOADING_MESSAGES)])
    return msg

async def is_user_member(user_id: int):
    try:
        channel = await bot.get_entity(FORCE_JOIN_CHANNEL_ID)
        await bot(GetParticipantRequest(
            channel=channel,
            participant=user_id
        ))
        return True
    except UserNotParticipantError:
        return False
    except Exception as e:
        logger.error(f"Error checking membership: {str(e)}")
        return False

@bot.on(events.NewMessage(pattern=r'^/start$'))
async def start_handler(event):
    user_id = str(event.sender_id)
    user_id2 = event.sender_id

    await delete_registration_progress(user_id)

    if not await get_tos_acceptance(user_id):
        await event.respond(
            TERMS_OF_SERVICE,
            buttons=[
                [Button.inline("✅ موافقت می‌کنم", b"accept_tos")],
                [Button.inline("❌ لغو", b"cancel_tos")]
            ]
        )
        return

    if not await is_user_member(user_id2):
        await event.respond(
            f"⚠️ لطفا ابتدا در کانال ما عضو شوید!\n\n"
            f"🔗 عضویت: @{FORCE_JOIN_CHANNEL_USERNAME}\n"
            "پس از عضویت، دکمه زیر را کلیک کنید.",
            buttons=[Button.inline("✅ تأیید عضویت", b"verify_join")]
        )
        return

    await event.respond(
        WELCOME_MESSAGE,
        buttons=create_main_keyboard()
    )

@bot.on(events.CallbackQuery(data=b"accept_tos"))
async def accept_tos_handler(event):
    user_id = str(event.sender_id)
    loading_msg = await show_loading_message(event)
    await save_tos_acceptance(user_id, True)
    await loading_msg.delete()
    await event.respond(
        "✅ با تشکر از پذیرش قوانین!\n"
        "اکنون می‌توانید از ربات استفاده کنید.\n\n"
        "🔄 برای شروع از /start استفاده کنید."
    )

@bot.on(events.CallbackQuery(data=b"cancel_tos"))
async def cancel_tos_handler(event):
    await event.respond(
        "❌ شما قوانین را نپذیرفتید.\n"
        "برای استفاده از ربات باید قوانین را بپذیرید.\n\n"
        "🔄 هر زمان که آماده بودید، از /start استفاده کنید."
    )

@bot.on(events.CallbackQuery(data=b"register"))
async def register_button_handler(event):
    user_id = str(event.sender_id)
    logger.info(f"Starting registration for user {user_id}")

    try:
        await delete_registration_progress(user_id)
        await save_registration_progress(user_id, "awaiting_api_id")
        
        steps = REGISTRATION_STEPS_TEMPLATE.format(
            step1_status="⏳",
            step2_status="⭕️",
            step3_status="⭕️"
        )
        
        registration_message = REGISTRATION_STEPS.format(
            current=1,
            steps=steps,
            current_step="وارد کردن API ID"
        )
        
        await event.respond(
            f"{registration_message}\n\n"
            "🔑 لطفاً API ID خود را وارد کنید:\n"
            "🌐 دریافت از: https://my.telegram.org"
        )
    except Exception as e:
        logger.error(f"Error in registration: {str(e)}")
        await event.respond(ERROR_MESSAGES["API_ERROR"])

@bot.on(events.NewMessage)
async def main_handler(event):
    user_id = event.sender_id

    # If the user is pending a two-step (2FA) password, process it here.
    if user_id in pending_password_users:
        client = pending_password_users.pop(user_id)
        try:
            loading_msg = await show_loading_message(event)
            login_success = await client.complete_two_step_login(event.text.strip())
            await loading_msg.delete()
            if login_success:
                await session_manager.activate_client(client.user_id, client)
                await session_manager.remove_pending(user_id)
                await event.respond(SUCCESS_MESSAGES["login"], buttons=create_main_keyboard())
            else:
                await session_manager.remove_pending(user_id)
                await event.respond("❌ رمز نادرست بود. لطفاً دوباره تلاش کنید.")
        except Exception as e:
            await session_manager.remove_pending(user_id)
            await event.respond("❌ ورود با رمز دومرحله‌ای با خطا مواجه شد.")
            logger.error(f"2FA login error for {user_id}: {str(e)}")
        raise events.StopPropagation

    text = event.text.strip()
    user_id_str = str(user_id)

    # Check if there is a pending login process (not 2FA) and proceed
    client = session_manager.get_pending(user_id)
    if client and client._login_in_progress and not client.verification_code:
        try:
            loading_msg = await show_loading_message(event)
            login_success = await client.complete_two_step_login(text)
            await loading_msg.delete()
            if login_success:
                await session_manager.activate_client(client.user_id, client)
                await session_manager.remove_pending(user_id)
                await event.respond(SUCCESS_MESSAGES["login"], buttons=create_main_keyboard())
            else:
                await session_manager.remove_pending(user_id)
                await event.respond("❌ رمز نادرست بود. لطفاً دوباره تلاش کنید.")
        except Exception as e:
            await session_manager.remove_pending(user_id)
            await event.respond("❌ ورود با رمز دومرحله‌ای با خطا مواجه شد.")
            logger.error(f"2FA login error for {user_id_str}: {str(e)}")
        return

    try:
        reg_progress = await get_registration_progress(user_id_str)
        if reg_progress:
            logger.info(f"Processing registration step: {reg_progress} for user {user_id_str}")
            current_step = reg_progress.get('step') if isinstance(reg_progress, dict) else reg_progress
            
            if current_step == "awaiting_api_id":
                if not text.isdigit():
                    await event.respond(
                        "❌ **خطا در API ID**\n\n"
                        "لطفاً فقط اعداد وارد کنید.\n"
                        "🔢 مثال: 12345"
                    )
                    return
                try:
                    loading_msg = await show_loading_message(event)
                    await save_temp_api_id(user_id_str, text)
                    await save_registration_progress(user_id_str, "awaiting_api_hash")
                    
                    steps = REGISTRATION_STEPS_TEMPLATE.format(
                        step1_status="✅",
                        step2_status="⏳",
                        step3_status="⭕️"
                    )
                    
                    registration_message = REGISTRATION_STEPS.format(
                        current=2,
                        steps=steps,
                        current_step="وارد کردن API Hash"
                    )
                    
                    await loading_msg.delete()
                    await event.respond(
                        f"{registration_message}\n\n"
                        "🔑 لطفاً API Hash خود را وارد کنید:"
                    )
                    logger.info(f"Saved temporary API ID for user {user_id_str}, awaiting hash")
                except Exception as e:
                    logger.error(f"Error saving API ID: {str(e)}")
                    await event.respond(ERROR_MESSAGES["API_ERROR"])
                return
                
            elif current_step == "awaiting_api_hash":
                if len(text) < 30:
                    await event.respond(
                        "❌ **خطا در API Hash**\n\n"
                        "API Hash وارد شده معتبر نیست.\n"
                        "لطفاً دوباره تلاش کنید."
                    )
                    return
                try:
                    loading_msg = await show_loading_message(event)
                    api_id = await get_temp_api_id(user_id_str)
                    if api_id:
                        await save_user_credentials(user_id_str, api_id, text)
                        await clear_temp_credentials(user_id_str)
                        await delete_registration_progress(user_id_str)
                        
                        steps = REGISTRATION_STEPS_TEMPLATE.format(
                            step1_status="✅",
                            step2_status="✅",
                            step3_status="⏳"
                        )
                        
                        registration_message = REGISTRATION_STEPS.format(
                            current=3,
                            steps=steps,
                            current_step="تأیید شماره تلفن"
                        )
                        
                        await loading_msg.delete()
                        await event.respond(
                            f"{registration_message}\n\n"
                            "📱 اکنون می‌توانید شماره تلفن خود را با فرمت زیر ارسال کنید:\n"
                            "مثال: +989123456789"
                        )
                        logger.info(f"Completed API registration for user {user_id_str}")
                    else:
                        await event.respond(
                            "❌ خطا در فرآیند ثبت نام.\n"
                            "🔄 لطفاً دوباره از /register شروع کنید."
                        )
                        await delete_registration_progress(user_id_str)
                except Exception as e:
                    logger.error(f"Error saving credentials: {str(e)}")
                    await event.respond(ERROR_MESSAGES["API_ERROR"])
                return

        # Bug report handling
        if bug_system.get_user_state(user_id_str):
            await bug_system.handle_input(event)
            return

        # Force join check
        if not await is_user_member(user_id):
            await event.respond(
                "⚠️ **دسترسی محدود**\n\n"
                "برای استفاده از ربات باید در کانال ما عضو باشید!\n\n"
                f"🔗 عضویت: @{FORCE_JOIN_CHANNEL_USERNAME}\n"
                "پس از عضویت، دکمه زیر را کلیک کنید.",
                buttons=[Button.inline("✅ تأیید عضویت", b"verify_join")]
            )
            return

        # Handle phone number input (registration/login)
        if text.startswith('+'):
            if session_manager.get_client(event.chat_id):
                await event.respond(
                    "✅ **حساب شما فعال است**\n\n"
                    "شما قبلاً وارد شده‌اید!"
                )
                return
            if session_manager.get_pending(event.chat_id):
                await event.respond(
                    "⚠️ **درخواست در حال انجام**\n\n"
                    "یک درخواست ورود در حال انجام است.\n"
                    "لطفاً صبر کنید."
                )
                return

            credentials = await get_user_credentials(user_id_str)
            if not credentials or not all(credentials):
                await event.respond(
                    "❌ **اطلاعات API ناقص**\n\n"
                    "لطفاً ابتدا اطلاعات API خود را ثبت کنید.\n"
                    "🔄 از دستور /register استفاده کنید."
                )
                return

            try:
                loading_msg = await show_loading_message(event)
                api_id, api_hash = credentials
                new_client = UserClient(api_id=int(api_id), api_hash=api_hash)
                await new_client.set_phone_number(text)
                try:
                    code_sent = await new_client.start_login_flow(text)
                    await loading_msg.delete()
                    if code_sent:
                        await session_manager.add_pending(event.chat_id, new_client)
                        await event.respond(
                            "🔑 **کد تأیید ارسال شد!**\n\n"
                            "لطفاً کد را با استفاده از صفحه کلید زیر وارد کنید:\n\n"
                            "⚠️ اگر کد را دریافت نکردید، لطفاً ۲ دقیقه صبر کنید و دوباره شماره را ارسال کنید.",
                            buttons=create_enhanced_persian_keyboard()
                        )
                    else:
                        await event.respond(
                            "❌ **خطا در ارسال کد**\n\n"
                            "خطایی در ارسال کد تأیید رخ داد.\n"
                            "🔄 لطفاً دوباره تلاش کنید."
                        )
                except Exception as e:
                    await loading_msg.delete()
                    error_msg = str(e)
                    if "PHONE_CODE_INVALID" in error_msg:
                        await event.respond(ERROR_MESSAGES["PHONE_NUMBER_INVALID"])
                    elif "PHONE_CODE_EXPIRED" in error_msg:
                        await event.respond(
                            "❌ **کد منقضی شده**\n\n"
                            "کد تأیید منقضی شده است.\n"
                            "🔄 لطفاً دوباره شماره خود را ارسال کنید."
                        )
                    else:
                        await event.respond(
                            "❌ **خطای غیرمنتظره**\n\n"
                            "خطایی رخ داد. لطفاً چند دقیقه صبر کنید و دوباره تلاش کنید.\n"
                            "🔄 اگر مشکل ادامه داشت، از /start استفاده کنید."
                        )
                        logger.error(f"Login flow error: {str(e)}")
            except Exception as e:
                error_msg = str(e)
                if "PHONE_NUMBER_INVALID" in error_msg:
                    await event.respond(ERROR_MESSAGES["PHONE_NUMBER_INVALID"])
                elif "PHONE_NUMBER_BANNED" in error_msg:
                    await event.respond(ERROR_MESSAGES["PHONE_NUMBER_BANNED"])
                else:
                    await event.respond(
                        "❌ **خطای غیرمنتظره**\n\n"
                        "خطایی رخ داد. لطفاً چند دقیقه صبر کنید و دوباره تلاش کنید.\n"
                        "🔄 اگر مشکل ادامه داشت، از /start استفاده کنید."
                    )
                logger.error(f"Login error for {user_id_str}: {error_msg}")

    except Exception as e:
        logger.error(f"Unexpected error in main handler: {str(e)}")
        await event.respond(
            "❌ **خطای سیستمی**\n\n"
            "متأسفانه خطای غیرمنتظره‌ای رخ داد.\n"
            "🔄 لطفاً از /start استفاده کنید."
        )

@bot.on(events.CallbackQuery)
async def handle_persian_keyboard(event):
    user_id = str(event.sender_id)
    client = session_manager.get_pending(event.chat_id)

    if not client and not admin_id:
        try:
            await event.edit(
                WELCOME_MESSAGE,
                buttons=create_main_keyboard()
            )
            return
        except Exception as e:
            logger.error(f"Error clearing keyboard: {str(e)}")

    button_value = event.data.decode("utf-8")

    if button_value == "submit_code":
        if not hasattr(client, "verification_code") or not client.verification_code:
            await event.answer("⚠️ لطفاً ابتدا کد را وارد کنید.", alert=True)
            return

        try:
            loading_msg = await show_loading_message(event)
            login_result = await client.complete_login()
            if login_result == "need_password":
                pending_password_users[event.sender_id] = client
                await loading_msg.delete()
                await event.respond(
                    "🔐 حساب شما دارای رمز دومرحله‌ای است.\n\n"
                    "لطفاً رمز عبور خود را وارد کرده و سپس ارسال کنید:"
                )
                return
            if login_result is True:
                await session_manager.activate_client(client.user_id, client)
                await loading_msg.delete()
                await event.edit(SUCCESS_MESSAGES["login"], buttons=create_main_keyboard())
                await session_manager.remove_pending(event.sender_id)
            else:
                await loading_msg.delete()
                await session_manager.remove_pending(event.sender_id)
                await event.edit("❌ کد تأیید نادرست بود یا مشکلی پیش آمد.", buttons=create_main_keyboard())
        except Exception as e:
            error_msg = str(e)
            await loading_msg.delete()
            await session_manager.remove_pending(event.chat_id)
            
            if "PHONE_CODE_INVALID" in error_msg:
                await event.edit(
                    "❌ **کد نادرست**\n\n"
                    "کد وارد شده نادرست است.\n"
                    "🔄 لطفاً دوباره شماره خود را ارسال کنید.",
                    buttons=create_main_keyboard()
                )
            elif "PHONE_CODE_EXPIRED" in error_msg:
                await event.edit(
                    "❌ **کد منقضی شده**\n\n"
                    "کد تأیید منقضی شده است.\n"
                    "🔄 لطفاً دوباره شماره خود را ارسال کنید.",
                    buttons=create_main_keyboard()
                )
            else:
                await event.edit(
                    "❌ **خطای غیرمنتظره**\n\n"
                    "خطایی رخ داد. لطفاً چند دقیقه صبر کنید و دوباره تلاش کنید.\n"
                    "🔄 اگر مشکل ادامه داشت، از /start استفاده کنید.",
                    buttons=create_main_keyboard()
                )
            logger.error(f"Code verification error for {user_id}: {error_msg}")

    elif button_value == "clear_code":
        if hasattr(client, "verification_code"):
            client.verification_code = ""
            await event.edit(
                "🔄 **کد پاک شد**\n\n"
                "لطفاً کد جدید را وارد کنید:",
                buttons=create_enhanced_persian_keyboard()
            )
    else:
        try:
            if not hasattr(client, "verification_code") or client.verification_code is None:
                client.verification_code = ""
            
            if button_value in PERSIAN_NUMERALS:
                button_value = str(PERSIAN_NUMERALS.index(button_value))
            
            client.verification_code = str(client.verification_code) + str(button_value)
            
            displayed_code = ""
            for digit in str(client.verification_code):
                if digit.isdigit():
                    displayed_code += PERSIAN_NUMERALS[int(digit)]
                else:
                    displayed_code += digit

            await event.edit(
                f"🔢 **کد وارد شده**: {displayed_code}\n\n"
                "• برای تأیید کد، دکمه ✅ را بزنید\n"
                "• برای پاک کردن کد، دکمه 🔄 را بزنید\n"
                "• برای ادامه ورود کد، از دکمه‌های زیر استفاده کنید:",
                buttons=create_enhanced_persian_keyboard()
            )
        except Exception as e:
            logger.error(f"Error handling keyboard input: {str(e)}")
            await session_manager.remove_pending(event.chat_id)

@bot.on(events.CallbackQuery(data=b"verify_join"))
async def verify_join_handler(event):
    user_id = event.sender_id
    loading_msg = await show_loading_message(event)
    is_member = await is_user_member(user_id)
    await loading_msg.delete()
    if is_member:
        await event.respond(
            "✅ **عضویت تأیید شد**\n\n"
            "با تشکر از عضویت شما!\n"
            "اکنون می‌توانید از ربات استفاده کنید.\n\n"
            "🔄 برای شروع از /start استفاده کنید."
        )
    else:
        await event.respond(
            "❌ **عضویت تأیید نشد**\n\n"
            "شما هنوز در کانال عضو نشده‌اید!\n\n"
            f"🔗 عضویت: @{FORCE_JOIN_CHANNEL_USERNAME}\n"
            "پس از عضویت، دکمه زیر را کلیک کنید.",
            buttons=[Button.inline("✅ تأیید عضویت", b"verify_join")]
        )

@bot.on(events.NewMessage(pattern='/help'))
async def help_handler(event):
    user_id = event.sender_id
    plugins = get_plugins()
    
    buttons = []
    for i in range(0, len(plugins), 2):
        row = []
        for plugin in plugins[i:i+2]:
            row.append(Button.inline(
                f"📁 {plugin}",
                data=f"help:{plugin}:{user_id}".encode()
            ))
        buttons.append(row)
    
    await event.respond(
        f"🖥 **لیست پلاگین‌ها (تعداد: {len(plugins)}):**",
        buttons=buttons
    )

@bot.on(events.CallbackQuery(data=b"about"))
async def about_handler(event):
    try:
        about_msg = (
            "ℹ️ **درباره ربات سلف ساز | Self Saz**\n\n"
            "نسخه: 2.4.1\n"
            "تاریخ انتشار: ۱۴۰۳/۰۵/۱۵\n\n"
            "🌟 **ویژگی‌های اصلی سلف ساز**:\n"
            "- مدیریت حرفه‌ای حساب‌های تلگرام\n"
            "- ساخت سلف‌های امن و سریع\n"
            "- ابزارهای امنیتی پیشرفته\n"
            "- پشتیبانی از دستورات فارسی\n"
            "- به روزرسانی‌های منظم\n\n"
            "🔒 **امنیت در سلف ساز**:\n"
            "- رمزنگاری پیشرفته\n"
            "- احراز هویت دو مرحله‌ای\n"
            "- سیستم تشخیص فعالیت غیرعادی"
        )
        await event.edit(
            about_msg,
            buttons=[
                [Button.inline("بازگشت به منوی اصلی", b"main_menu")]
            ]
        )
    except Exception as e:
        logger.error(f"About error: {str(e)}")
        await event.respond("❌ خطا در نمایش اطلاعات. لطفا مجددا تلاش کنید.")

@bot.on(events.CallbackQuery(pattern=b'help:'))
async def plugin_help_handler(event):
    data = event.data.decode().split(':')
    plugin_name = data[1]
    owner_id = int(data[2])
    if event.sender_id != owner_id:
        await event.answer("❌ فقط سازنده منو می‌تواند از این گزینه استفاده کند!", alert=True)
        return
    help_text = get_plugin_help(plugin_name)
    buttons = [
        [
            Button.inline("🔙 لیست پلاگین‌ها", data=f"back_plugins:{owner_id}".encode()),
            Button.inline("🏠 منوی اصلی", data=f"back_main:{owner_id}".encode())
        ]
    ]
    await event.edit(
        f"📚 **راهنمای {plugin_name}:**\n\n{help_text}",
        buttons=buttons
    )

@bot.on(events.CallbackQuery(data=b"main_menu"))
async def main_menu_handler(event):
    try:
        await event.edit(
            WELCOME_MESSAGE,
            buttons=create_main_keyboard()
        )
    except Exception as e:
        logger.error(f"Menu error: {str(e)}")

@bot.on(events.CallbackQuery(data=b"report_bug"))
async def start_bug_report(event):
    try:
        await bug_system.start_report(event)
    except Exception as e:
        logger.error(f"Error starting bug report: {str(e)}")
        await event.respond(
            "❌ **خطا در شروع گزارش**\n\n"
            "متأسفانه خطایی رخ داد. لطفاً دوباره تلاش کنید."
        )

@bot.on(events.CallbackQuery(data=b"help_menu"))
async def help_menu_handler(event):
    user_id = event.sender_id
    plugins = get_plugins()
    
    buttons = []
    for i in range(0, len(plugins), 2):
        row = []
        for plugin in plugins[i:i+2]:
            row.append(Button.inline(
                f"📁 {plugin}",
                data=f"help:{plugin}:{user_id}".encode()
            ))
        buttons.append(row)
    
    buttons.append([Button.inline("🏠 منوی اصلی", data=f"back_main:{user_id}".encode())])
    await event.edit(
        f"🖥 **لیست پلاگین‌ها (تعداد: {len(plugins)}):**",
        buttons=buttons
    )

@bot.on(events.NewMessage)
async def bug_report_message_handler(event):
    if event.text.startswith('/'):
        return
    try:
        user_id = event.sender_id
        current_state = bug_system.get_user_state(str(user_id))
        if current_state:
            await bug_system.handle_input(event)
    except Exception as e:
        logger.error(f"Error in bug report message handler: {str(e)}")
        await event.respond(
            "❌ **خطا در پردازش گزارش**\n\n"
            "متأسفانه خطایی رخ داد. لطفاً دوباره از /report استفاده کنید."
        )

@bot.on(events.CallbackQuery(pattern=r'^bug_priority_'))
async def handle_bug_priority(event):
    try:
        priority = event.data.decode('utf-8').split('_')[2]
        user_id = event.sender_id
        if not bug_system.get_user_state(str(user_id)):
            await event.answer("⚠️ گزارش فعالی وجود ندارد!", alert=True)
            return
        await bug_system.handle_priority_selection(event, priority)
    except Exception as e:
        logger.error(f"Error handling bug priority: {str(e)}")
        await event.answer("❌ خطا در ثبت اولویت", alert=True)

@bot.on(events.CallbackQuery(data=b"cancel_bug_report"))
async def cancel_bug_report(event):
    try:
        user_id = event.sender_id
        if bug_system.get_user_state(str(user_id)):
            await bug_system.cancel_report(event)
            await event.respond(
                "✅ **گزارش لغو شد**\n\n"
                "می‌توانید هر زمان که خواستید دوباره گزارش جدیدی ثبت کنید.",
                buttons=create_main_keyboard()
            )
        else:
            await event.answer("⚠️ گزارش فعالی وجود ندارد!", alert=True)
    except Exception as e:
        logger.error(f"Error canceling bug report: {str(e)}")
        await event.answer("❌ خطا در لغو گزارش", alert=True)

@bot.on(events.NewMessage(pattern='/admin_bugs'))
async def admin_bug_list(event):
    if event.sender_id != bug_system.admin_id:
        return
    try:
        bugs = await bug_system._get_all_bugs()
        message = "🐛 **لیست گزارش‌های مشکل**\n\n"
        for status in BugStatus:
            status_bugs = [b for b in bugs if b['status'] == status.value]
            if status_bugs:
                message += f"\n**{status.value.upper()}** ({len(status_bugs)})\n"
                for bug in status_bugs[:5]:
                    message += (
                        f"• #{bug['id']} - {bug['title'][:30]}\n"
                        f"  اولویت: {bug['priority']}\n"
                        f"  کاربر: @{bug['username']}\n"
                    )
        await event.respond(message)
    except Exception as e:
        logger.error(f"Error listing bugs: {str(e)}")
        await event.respond("❌ خطا در نمایش لیست گزارش‌ها")

@bot.on(events.NewMessage(pattern='/bug_stats'))
async def admin_bug_stats(event):
    if event.sender_id != bug_system.admin_id:
        return
    try:
        bugs = await bug_system._get_all_bugs()
        total_bugs = len(bugs)
        status_counts = {}
        priority_counts = {}
        for bug in bugs:
            status_counts[bug['status']] = status_counts.get(bug['status'], 0) + 1
            priority_counts[bug['priority']] = priority_counts.get(bug['priority'], 0) + 1
        message = (
            "📊 **آمار گزارش‌های مشکل**\n\n"
            f"🔢 تعداد کل: {total_bugs}\n\n"
            "📌 **وضعیت**:\n"
        )
        for status in BugStatus:
            count = status_counts.get(status.value, 0)
            message += f"• {status.value}: {count}\n"
        message += "\n🎯 **اولویت**:\n"
        for priority in BugPriority:
            count = priority_counts.get(priority.value, 0)
            message += f"• {priority.value}: {count}\n"
        await event.respond(message)
    except Exception as e:
        logger.error(f"Error showing bug stats: {str(e)}")
        await event.respond("❌ خطا در نمایش آمار")

@bot.on(events.CallbackQuery(pattern=b'support'))
async def support_button_handler(event):
    try:
        await event.edit("در حال ورود به بخش پشتیبانی...")
        await support_system.start_support_chat(event)
    except Exception as e:
        logger.error(f"Error in support button handler: {str(e)}")
        await event.respond(
            "❌ خطا در اتصال به پشتیبانی. لطفاً دوباره تلاش کنید.",
            buttons=create_main_keyboard()
        )

@bot.on(events.CallbackQuery(pattern=b'back_plugins:'))
async def back_to_plugins_handler(event):
    owner_id = int(event.data.decode().split(':')[1])
    if event.sender_id != owner_id:
        await event.answer("❌ دسترسی غیرمجاز!", alert=True)
        return
    plugins = get_plugins()
    buttons = []
    for i in range(0, len(plugins), 2):
        row = []
        for plugin in plugins[i:i+2]:
            row.append(Button.inline(
                f"📁 {plugin}",
                data=f"help:{plugin}:{owner_id}".encode()
            ))
        buttons.append(row)
    buttons.append([Button.inline("🏠 منوی اصلی", data=f"back_main:{owner_id}".encode())])
    await event.edit(
        f"🖥 **لیست پلاگین‌ها (تعداد: {len(plugins)}):**",
        buttons=buttons
    )

@bot.on(events.CallbackQuery(pattern=b'back_main:'))
async def back_to_main_handler(event):
    owner_id = int(event.data.decode().split(':')[1])
    if event.sender_id != owner_id:
        await event.answer("❌ دسترسی غیرمجاز!", alert=True)
        return
    await event.edit(
        WELCOME_MESSAGE,
        buttons=create_main_keyboard()
    )

@bot.on(events.CallbackQuery(data=b"tutorial"))
async def tutorial_handler(event):
    try:
        tutorial_link = "https://t.me/SelfxSaz/3"
        await event.edit(
            "🎥 **آموزش تصویری استفاده از ربات**\n\n"
            "برای مشاهده آموزش کامل، لطفاً روی دکمه زیر کلیک کنید:",
            buttons=[
                [Button.url("مشاهده آموزش در تلگرام", tutorial_link)],
                [Button.inline("بازگشت به منوی اصلی", b"main_menu")]
            ]
        )
    except Exception as e:
        logger.error(f"Error in tutorial handler: {str(e)}")
        await event.respond(
            f"✅ لینک آموزش: {tutorial_link}",
            buttons=create_main_keyboard()
        )


@bot.on(events.CallbackQuery(data=b"my_account"))
async def my_account_handler(event):
    user_id = event.sender_id
    balance = await coin_db.get_balance(user_id)

    # 1) Ensure referral_codes table exists (one‑off)
    async with coin_db.pool.acquire() as conn:
        async with conn.cursor() as cur:
            await cur.execute("""
                CREATE TABLE IF NOT EXISTS referral_codes (
                    user_id BIGINT UNSIGNED PRIMARY KEY,
                    code    VARCHAR(32)     NOT NULL UNIQUE
                ) ENGINE=InnoDB CHARSET=utf8mb4;
            """)
            await conn.commit()

            # 2) Fetch or generate the unique code
            await cur.execute(
                "SELECT code FROM referral_codes WHERE user_id=%s", (user_id,)
            )
            row = await cur.fetchone()
            if row:
                code = row[0]
            else:
                code = secrets.token_urlsafe(8)
                await cur.execute(
                    "INSERT INTO referral_codes (user_id, code) VALUES (%s, %s)",
                    (user_id, code)
                )
                await conn.commit()

    # 3) Build deep-link
    me = await bot.get_me()
    username = me.username or "<bot_username>"
    link = f"https://t.me/{username}?start={code}"

    # 4) Respond
    await event.respond(
        f"💰 موجودی شما: {balance} سکه\n"
        f"🔗 لینک معرفی شما:\n{link}"
    )



# ← NEW HANDLER: buy license
@bot.on(events.CallbackQuery(pattern=b"buy"))
async def buy_license_handler(event):
    # parse duration as before…
    _, qs = event.data.decode().split("?", 1)
    params = parse_qs(qs)
    duration = int(params.get("duration", ["0"])[0])

    cost_per_month = 150
    required = duration * cost_per_month
    balance  = await coin_db.get_balance(event.sender_id)

    if balance < required:
        shortfall = required - balance
        return await event.respond(
            f"❌ موجودی کافی نیست.\n"
            f"💰 موجودی شما: {balance} سکه\n"
            f"⚠️ نیاز به: {required} ({shortfall} کم دارید)"
        )

    try:
        code = await coin_db.create_license(event.sender_id, duration, cost_per_month)
        await event.respond(
            f"✅ خرید موفق!\n"
            f"🎫 کد لایسنس {duration}‌ماهه شما:\n`{code}`",
            buttons=[
                [Button.inline("✅ فعال‌سازی لایسنس", data=f"redeem?code={code}".encode())]
            ]
        )
    except Exception as e:
        logger.error(f"License creation error: {str(e)}")
        await event.respond("❌ خطا در ایجاد لایسنس. لطفاً دوباره تلاش کنید.")


@bot.on(events.CallbackQuery(pattern=r"^redeem\?code="))
async def redeem_button_handler(event):
    # Parse the code out of the callback data
    raw = event.data.decode()               # e.g. "redeem?code=ABC123"
    _, qs = raw.split("?", 1)
    params = parse_qs(qs)
    code = params.get("code", [""])[0]

    # Fetch and validate the license
    lic = await coin_db.get_license(code)
    if not lic:
        return await event.answer("❌ لایسنس یافت نشد.", alert=True)
    if lic["used"]:
        return await event.answer("❌ این لایسنس قبلاً استفاده شده است.", alert=True)

    # Mark it used and compute expiry
    now = datetime.utcnow()
    dur = lic["duration_months"]
    expires = now + timedelta(days=dur * 30)
    await coin_db.update_license_timestamps(code, now, expires)

    # Schedule automatic logout when it expires
    async def _expire():
        await asyncio.sleep((expires - datetime.utcnow()).total_seconds())
        await bot.send_message(event.sender_id, "🔔 لایسنس شما منقضی شد، ربات خارج می‌شود.")
        await bot.log_out()

    asyncio.get_event_loop().create_task(_expire())

    # Notify instantly
    await event.answer(f"✅ لایسنس {dur} ماهه فعال شد!", alert=True)



@bot.on(events.NewMessage(pattern=r"^/redeem\s+(\w+)$"))
async def redeem_text(event):
    code = event.pattern_match.group(1)
    user_id = event.sender_id

    lic = await coin_db.get_license(code)
    if not lic:
        return await event.reply("❌ این کد یافت نشد.")
    if lic["used"]:
        return await event.reply("🔔 این لایسنس قبلاً فعال شده.")
    if lic["user_id"] != user_id:
        return await event.reply("❌ این لایسنس برای شما نیست!")

    await coin_db.mark_license_used(code)
    await event.reply("✅ لایسنس با موفقیت فعال شد!")




@bot.on(events.CallbackQuery(data=b"license_status"))
async def license_status_cb(event):
    user_id = event.sender_id
    lic = await coin_db.get_active_license_for_user(user_id)  # needs your plugin’s helper
    if not lic or not lic.get('used'):
        return await event.answer("⚠️ شما لایسنس فعالی ندارید.", alert=True)

    now = datetime.utcnow()
    expires = lic['expires_at']
    rem = expires - now
    if rem.total_seconds() <= 0:
        return await event.answer("⏰ لایسنس شما منقضی شده است.", alert=True)

    days = rem.days
    hours, rem_s = divmod(rem.seconds, 3600)
    minutes, _ = divmod(rem_s, 60)
    text = f"⏳ زمان باقی‌مانده: {days} روز، {hours} ساعت، {minutes} دقیقه"
    await event.answer(text, alert=True)



@bot.on(events.CallbackQuery(data=b"how_get_coins"))
async def how_get_coins(event):
    """
    Explains all the ways users can earn coins,
    by editing the inline‑menu message instead of showing an alert.
    """
    text = (
        "🎉 **روش‌های دریافت سکه**:\n\n"
        "1️⃣ **فعالیت در گروه**\n"
        "   • با ارسال پیام در گروه رسمی @telebotcraftchat\n"
        "   • هر دقیقه یک سکه (تا سقف روزانه)\n\n"
        "2️⃣ **قمار روزانه**\n"
        "   • دستور `/gamble` را در هر چت بزنید\n"
        "   • هر ۲۴ ساعت یکبار می‌توانید تاس 🎲 بیندازید\n"
        "   • اگر عدد **۶** بیفتد، ۲۰ سکه برنده می‌شوید\n\n"
        "3️⃣ **دعوت دوستان**\n"
        "   • لینک رفرال خود را از منوی “👤 حساب من” کپی کنید\n"
        "   • وقتی دوستتان با لینک شما `/start=کدشما` وارد شد\n"
        "   • هم شما و هم دوستتان سکه‌ی هدیه دریافت می‌کنید\n\n"
        "4️⃣ **انتقال از طرف ادمین**\n"
        "   • ادمین می‌تواند با دستور `/auth` به شما سکه منتقل کند\n\n"
        "📊 **برای مشاهده موجودی فعلی:**\n"
        "   • از منوی “👤 حساب من” استفاده کنید\n\n"
        "امیدواریم همیشه برنده باشید! 🏆"
    )
    # Edit the original menu message to show this text,
    # plus a button to go back to the main menu:
    await event.edit(
        text,
        buttons=[[Button.inline("🏠 بازگشت به منوی اصلی", b"main_menu")]]
    )







async def main():
    try:
        # Initialize your primary database (user sessions, credentials, etc.)
        await init_db()

        # Initialize the coin database (creates user_coins, licenses, coin_transfers tables)
        await coin_db.init()

        # Set up admin coin commands
        admin_manager = AdminCoinManager(
            pool=coin_db.pool,
            admin_ids={admin_id},  # your super‑admin(s)
            bot=bot
        )
        # Keep a reference so it isn't garbage‑collected
        bot.admin_manager = admin_manager

        # Start the Telegram bot
        await bot.start(bot_token=BOT_TOKEN)
        # After bot.start(...) and session_manager.load_existing_sessions()
        ReferralListener(bot)
        GambleListener(bot)


        # Load any existing user sessions
        await session_manager.load_existing_sessions()
        logger.info("Bot is running with admin coin manager enabled…")

        # Run background cleanup and keep the bot alive
        await asyncio.gather(
            session_manager.cleanup_pending_task(),
            bot.run_until_disconnected()
        )

    except Exception as e:
        logger.error(f"Error in main(): {e}")
    finally:
        # Clean shutdown
        await session_manager.shutdown()
        await bot.disconnect()



if __name__ == "__main__":
    asyncio.run(main())