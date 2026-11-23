# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import asyncio
import logging
import glob
import importlib.util
import sys
from typing import List
import subprocess
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)
logger = logging.getLogger(__name__)
from telethon import TelegramClient, events
from telethon.sessions import StringSession
from telethon.tl.custom import Button
from plugins.cache import currency_cache
from handlers.main_currencies import register_handlers as register_main_currency_handlers
from handlers.main_currencies import show_main_currencies_page
from handlers.minor_currencies import register_handlers as register_minor_currency_handlers
from handlers.minor_currencies import show_minor_currencies_page
from handlers.gold_display import register_handlers as register_gold_display_handlers, show_gold_page
from plugins.inline_query import register_inline_handlers
from plugins.crypto import register_crypto_handlers, crypto_cache as crypto_data_cache
from plugins.user_db import user_db
if sys.platform != 'win32':
    try:
        import uvloop
        asyncio.set_event_loop_policy(uvloop.EventLoopPolicy())
        logger.info("Using uvloop for improved performance")
    except ImportError:
        logger.info("uvloop not available, using default asyncio event loop")
else:
    logger.info("Running on Windows - uvloop is not supported, using default asyncio event loop")
API_ID = ''
API_HASH = ''
BOT_TOKEN = ''
ADMIN_IDS = [7150795159]
def load_module(file_path):
    """Load a Python module from file path, setting its package context."""
    module_name_for_spec = None
    module_object = None
    try:
        normalized_file_path = os.path.normpath(file_path)
        module_name_for_spec = os.path.splitext(normalized_file_path)[0].replace(os.sep, '.')
        spec = importlib.util.spec_from_file_location(module_name_for_spec, file_path)
        if spec is None:
            logger.error(f"Could not create spec for module {file_path} (intended name {module_name_for_spec})")
            return None
        module_object = importlib.util.module_from_spec(spec)
        sys.modules[module_name_for_spec] = module_object
        spec.loader.exec_module(module_object)
        return module_object
    except Exception as e:
        effective_module_name = module_name_for_spec if module_name_for_spec is not None else "unknown_module_path"
        if module_object is not None and module_name_for_spec is not None and module_name_for_spec in sys.modules:
            if sys.modules[module_name_for_spec] is module_object:
                del sys.modules[module_name_for_spec]
        logger.error(f"Failed to load module {file_path} (as {effective_module_name}): {e}")
        return None
def register_currency_handlers(client):
    """Register all currency handlers from plugins directory"""
    currency_files = glob.glob('plugins/*.py')
    for file_path in currency_files:
        if file_path.endswith(('__init__.py', 'utils.py', 'cache.py', 'currency_template.py', 'generate_handlers.py')):
            continue
        module = load_module(file_path)
        if module and hasattr(module, 'TRIGGERS') and hasattr(module, 'handle_currency'):
            registered_from_this_file = False
            for trigger in module.TRIGGERS:
                pattern_key = f"^{trigger}$"
                if pattern_key not in client.registered_message_patterns:
                    client.add_event_handler(
                        lambda event, current_module=module: asyncio.create_task(self_hosted_handle_currency_wrapper(event, client, current_module)),
                        events.NewMessage(pattern=pattern_key, incoming=True)
                    )
                    client.registered_message_patterns.add(pattern_key)
                    logger.info(f"Registered currency handler for trigger '{trigger}' from {file_path}")
                    registered_from_this_file = True
                else:
                    logger.warning(f"Skipping duplicate currency handler registration for trigger '{trigger}' from {file_path}. It's already handled.")
            if registered_from_this_file:
                 logger.info(f"Successfully processed currency handlers from {file_path}")
def register_gold_handlers(client):
    """Register all gold handlers from plugins/gold directory"""
    gold_files = glob.glob('plugins/gold/*.py')
    for file_path in gold_files:
        if file_path.endswith(('__init__.py', 'generate_handlers.py')):
            continue
        module = load_module(file_path)
        if module:
            if hasattr(module, 'TRIGGERS') and hasattr(module, 'handle_gold'):
                registered_trigger_based_handler = False
                for trigger in module.TRIGGERS:
                    pattern_key = f"^{trigger}$"
                    if pattern_key not in client.registered_message_patterns:
                        client.add_event_handler(
                            lambda event, current_module=module: asyncio.create_task(self_hosted_handle_gold_wrapper(event, client, current_module)),
                            events.NewMessage(pattern=pattern_key, incoming=True)
                        )
                        client.registered_message_patterns.add(pattern_key)
                        logger.info(f"Registered gold handler for trigger '{trigger}' from {file_path}")
                        registered_trigger_based_handler = True
                    else:
                        logger.warning(f"Skipping duplicate gold handler registration for trigger '{trigger}' from {file_path}. It's already handled.")
                if registered_trigger_based_handler:
                    logger.info(f"Successfully processed trigger-based gold handlers from {file_path}")
            elif hasattr(module, 'handle_gold_command'):
                pattern_key_command = "^/gold$"
                if pattern_key_command not in client.registered_message_patterns:
                    client.add_event_handler(
                        lambda event, current_module=module: asyncio.create_task(self_hosted_handle_gold_command_wrapper(event, client, current_module)),
                        events.NewMessage(pattern=pattern_key_command)
                    )
                    client.registered_message_patterns.add(pattern_key_command)
                    logger.info(f"Registered gold command handler for '/gold' from {file_path}")
                else:
                    logger.warning(f"Skipping duplicate gold command handler registration for '/gold' from {file_path}. It's already handled by another plugin.")
async def self_hosted_handle_currency_wrapper(event, client, current_module):
    client.currency_data = client.currency_cache.get_data()
    await current_module.handle_currency(event, client)
async def self_hosted_handle_gold_wrapper(event, client, current_module):
    full_cache_data = client.currency_cache.get_data()
    client.currency_data = full_cache_data
    client.gold_data = full_cache_data.get('GoldType', {})
    await current_module.handle_gold(event, client)
async def self_hosted_handle_gold_command_wrapper(event, client, current_module):
    full_cache_data = client.currency_cache.get_data()
    client.currency_data = full_cache_data
    client.gold_data = full_cache_data.get('GoldType', {})
    await current_module.handle_gold_command(event, client)
async def init_client():
    """Initialize and connect the client"""
    global client
    session_file = 'session.txt'
    if os.path.exists(session_file):
        with open(session_file, 'r') as file:
            session_string = file.read().strip()
        client = TelegramClient(StringSession(session_string), API_ID, API_HASH)
    else:
        client = TelegramClient(StringSession(), API_ID, API_HASH)
    logger.info("Running database migrations...")
    try:
        subprocess.run([sys.executable, 'run_migrations.py'], check=True)
        logger.info("Database migrations completed successfully")
    except subprocess.CalledProcessError as e:
        logger.error(f"Error running migrations: {e}")
        raise
    await client.start(bot_token=BOT_TOKEN)
    from plugins.admin import AdminPanel
    admin_panel = AdminPanel(client, user_db, ADMIN_IDS)
    logger.info("Admin panel initialized")
    try:
        logger.info("Connecting to Telegram...")
        if not os.path.exists(session_file):
            session_string = client.session.save()
            with open(session_file, 'w') as file:
                file.write(session_string)
        logger.info("Successfully connected to Telegram!")
        return client
    except Exception as e:
        logger.error(f"Failed to connect to Telegram: {str(e)}")
        raise
client = None
@events.register(events.NewMessage(pattern='/start'))
async def start(event):
    """Handle /start command"""
    sender = await event.get_sender()
    logger.info(f"User {sender.id} ({sender.username}) started bot with /start command.")
    try:
        user_db.add_user(
            user_id=sender.id,
            username=sender.username,
            first_name=sender.first_name,
            last_name=sender.last_name,
            is_bot=sender.bot,
            language_code=getattr(sender, 'lang_code', None)
        )
        logger.info(f"Saved user {sender.id} to database")
    except Exception as e:
        logger.error(f"Error saving user to database: {str(e)}")
    if event.is_private:
        welcome_text = """
<b>🌟 به ربات نرخ ارز خوش آمدید!</b>
<b>📈 برای دریافت نرخ لحظه‌ای:</b>
• دستور /rates را برای مشاهده ارزها ارسال کنید
• یا نام ارز مورد نظر را به فارسی یا انگلیسی تایپ کنید
مثال: دلار، euro، usd
<b>💱 برای تبدیل ارز:</b>
• کافیست مقدار و نام ارز را بنویسید (مثال: 100 دلار، 50 یورو)
• یا از فرمت «مقدار ارز_مبدا به ارز_مقصد» استفاده کنید
<b>🪙 برای قیمت ارزهای دیجیتال:</b>
• نام ارز دیجیتال را به فارسی یا انگلیسی تایپ کنید
مثال: بیت کوین، btc، اتریوم
• یا از فرمت «مقدار نام_ارز» استفاده کنید (مثال: 2 btc، ۱۰ اتریوم)
        """
        buttons = [
            [Button.inline("🥇 قیمت طلا", b'cmd_gold_display'), Button.inline("📊 قیمت ارزهای اصلی", b'cmd_main_currencies')],
            [Button.inline("📈 قیمت سایر ارزها", b'cmd_minor_currencies'), Button.inline("₿ قیمت رمزارزها", b'cmd_crypto')],
            [Button.inline("💱 تبدیل ارز", b'cmd_currency_convert')],
            [Button.url("📢 کانال ما", "https://t.me/TelebotCraft"),
             Button.url("➕ افزودن به گروه", f"https://t.me/{(await event.client.get_me()).username}?startgroup=true")]
        ]
        await event.reply(welcome_text, buttons=buttons, parse_mode='html')
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=r"cmd_main_curr"))
async def handle_main_currencies_command(event):
    """Handle main currencies command button"""
    try:
        event.client.currency_data = currency_cache.get_data()
        await show_main_currencies_page(event, 1, event.client)
        await event.answer()
    except Exception as e:
        await event.answer(f"خطا در نمایش ارزهای اصلی: {str(e)}", alert=True)
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=r"cmd_minor_curr"))
async def handle_minor_currencies_command(event):
    """Handle minor currencies command button"""
    try:
        event.client.currency_data = currency_cache.get_data()
        await show_minor_currencies_page(event, 1, event.client)
        await event.answer()
    except Exception as e:
        await event.answer(f"خطا در نمایش ارزهای فرعی: {str(e)}", alert=True)
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=r"cmd_gold_display"))
async def handle_gold_display_command(event):
    """Handle gold display command button"""
    try:
        event.client.currency_data = currency_cache.get_data()
        await show_gold_page(event, 1, event.client)
        await event.answer()
    except Exception as e:
        await event.answer(f"خطا در نمایش قیمت طلا: {str(e)}", alert=True)
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=b"cmd_crypto"))
async def handle_crypto_command(event):
    """Handle crypto command button"""
    try:
        logger.info("Crypto button clicked - using show_crypto_list from crypto_handler.py")
        await event.respond("🔍 درحال بارگذاری لیست ارزهای دیجیتال... لطفاً صبر کنید.")
        from plugins.crypto.crypto_handler import show_crypto_list
        await show_crypto_list(event, event.client)
        await event.answer()
    except Exception as e:
        logger.error(f"Error in crypto command: {str(e)}", exc_info=True)
        await event.answer(f"خطا در نمایش ارزهای دیجیتال: {str(e)}", alert=True)
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=r"cmd_currency_convert"))
async def handle_currency_convert_command(event):
    """Handle currency conversion command button"""
    try:
        help_text = """
💱 راهنمای تبدیل ارز:
برای تبدیل به تومان، کافیست مقدار و نام ارز را بنویسید:
• `100 دلار`
• `50 usd`
• `۱۰۰ یورو`
برای تبدیل بین دو ارز، از فرمت زیر استفاده کنید:
`مقدار ارز_مبدا به ارز_مقصد`
مثال‌ها:
• `100 دلار به یورو`
• `500 تومان به یورو`
• `50 usd to eur`
ارزهای پشتیبانی شده:
• دلار (USD)
• یورو (EUR)
• پوند (GBP)
• درهم (AED)
• لیر (TRY)
• تومان (TOMAN)
• ریال (IRR)
و سایر ارزهای موجود در ربات
        """
        buttons = [
            [Button.inline("🏠 بازگشت به خانه", b'home')],
            [Button.url("📢 کانال ما", "https://t.me/TelebotCraft")]
        ]
        await event.edit(help_text, buttons=buttons)
        await event.answer()
    except Exception as e:
        await event.answer(f"خطا در نمایش راهنمای تبدیل ارز: {str(e)}", alert=True)
    raise events.StopPropagation
@events.register(events.CallbackQuery(pattern=r"home"))
async def handle_home(event):
    """Handle home button press"""
    try:
        welcome_text = """
<b>🌟 به ربات نرخ ارز خوش آمدید!</b>
<b>📈 برای دریافت نرخ لحظه‌ای:</b>
• دستور /rates را برای مشاهده ارزها ارسال کنید
• یا نام ارز مورد نظر را به فارسی یا انگلیسی تایپ کنید
مثال: دلار، euro، usd
<b>💱 برای تبدیل ارز:</b>
• کافیست مقدار و نام ارز را بنویسید (مثال: 100 دلار، 50 یورو)
• یا از فرمت «مقدار ارز_مبدا به ارز_مقصد» استفاده کنید
<b>🪙 برای قیمت ارزهای دیجیتال:</b>
• نام ارز دیجیتال را به فارسی یا انگلیسی تایپ کنید
مثال: بیت کوین، btc، اتریوم
• یا از فرمت «مقدار نام_ارز» استفاده کنید (مثال: 2 btc، ۱۰ اتریوم)
        """
        buttons = [
            [Button.inline("💵 ارزهای اصلی", b'cmd_main_curr'), Button.inline("💴 ارزهای فرعی", b'cmd_minor_curr')],
            [Button.inline("🥇 قیمت طلا", b'cmd_gold_display'), Button.inline("🪙 ارزهای دیجیتال", b'cmd_crypto')],
            [Button.inline("💱 تبدیل ارز", b'cmd_currency_convert')],
            [Button.url("📢 کانال ما", "https://t.me/TelebotCraft"),
             Button.url("➕ افزودن به گروه", f"https://t.me/{(await event.client.get_me()).username}?startgroup=true")]
        ]
        await event.edit(welcome_text, buttons=buttons, parse_mode='html')
        await event.answer()
    except Exception as e:
        await event.answer(f"خطا در بازگشت به صفحه اصلی: {str(e)}", alert=True)
    raise events.StopPropagation
async def main():
    """Main function to start the bot"""
    try:
        global client
        client = await init_client()
        client.currency_data = None
        client.currency_cache = currency_cache
        client.gold_data = {}
        client.registered_message_patterns = set()
        client.add_event_handler(start)
        client.add_event_handler(handle_main_currencies_command)
        client.add_event_handler(handle_minor_currencies_command)
        client.add_event_handler(handle_gold_display_command)
        client.add_event_handler(handle_crypto_command)
        client.add_event_handler(handle_currency_convert_command)
        client.add_event_handler(handle_home)
        register_currency_handlers(client)
        register_gold_handlers(client)
        register_main_currency_handlers(client)
        register_minor_currency_handlers(client)
        register_gold_display_handlers(client)
        register_inline_handlers(client)
        logger.info("Registered inline query handlers")
        register_crypto_handlers(client)
        logger.info("Registered crypto handlers")
        from plugins.currency_converter import TRIGGERS as converter_triggers, handle_currency as handle_currency_converter
        async def handle_currency_converter_wrapper(event):
            client.currency_data = client.currency_cache.get_data()
            await handle_currency_converter(event, client)
        for trigger in converter_triggers:
            pattern_key = f"^{trigger}$"
            if pattern_key not in client.registered_message_patterns:
                client.add_event_handler(
                    handle_currency_converter_wrapper,
                    events.NewMessage(pattern=pattern_key, incoming=True)
                )
                client.registered_message_patterns.add(pattern_key)
                logger.info(f"Registered currency converter handler for trigger '{trigger}'")
        client.add_event_handler(
            handle_currency_converter_wrapper,
            events.NewMessage(pattern=r'\d+\s*[a-zA-Z\u0600-\u06FF]+', incoming=True)
        )
        logger.info("Registered currency converter handler for amount patterns")
        from plugins.crypto.crypto_handler import initialize_crypto_plugin
        initialize_crypto_plugin(client)
        logger.info("Crypto plugin initialized")
        currency_cache.start()
        logger.info("Bot started successfully!")
        await client.run_until_disconnected()
    except Exception as e:
        logger.error(f"Error in main function: {str(e)}")
        raise
    finally:
        currency_cache.stop()
        try:
            user_db.close()
            logger.info("User database connection closed")
        except Exception as e:
            logger.error(f"Error closing user database: {str(e)}")
        if client:
            await client.disconnect()
if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        logger.info("Bot stopped by user")
    except Exception as e:
        logger.error(f"Fatal error: {str(e)}")
