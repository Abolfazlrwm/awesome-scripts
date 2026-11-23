# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os
import logging
from datetime import datetime
import asyncio
import uuid
import json
import html
from dotenv import load_dotenv
from telegram import (
    Update, InlineKeyboardButton, InlineKeyboardMarkup, 
    InlineQueryResultArticle, InputTextMessageContent,
    ReplyKeyboardMarkup, ReplyKeyboardRemove
)
from telegram.ext import (
    Application,
    CommandHandler,
    MessageHandler,
    CallbackQueryHandler,
    InlineQueryHandler,
    ContextTypes,
    filters,
    ConversationHandler
)
from telegram.constants import ParseMode
from database import Database


load_dotenv()
TOKEN = 'BOT TOken' #توکن ربات را اینجا وارد کنید


logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)
logger = logging.getLogger(__name__)


db = Database()


SELECTING_OPTION, TYPING_MESSAGE, SELECTING_THEME = range(3)


THEMES = {
    'default': '🔵 پیش‌فرض',
    'dark': '⚫️ تیره',
    'light': '⚪️ روشن',
    'fancy': '🌈 فانتزی',
    'love': '❤️ عاشقانه',
    'business': '💼 رسمی'
}

def create_whisper_id():
    """Create a unique whisper ID"""
    return f"w_{str(uuid.uuid4())[:8]}"

async def format_whisper(message: str, theme: str = 'default', from_user: str = None) -> str:
    """Format whisper message based on theme"""
    timestamp = datetime.now().strftime("%H:%M")
    
    themes = {
        'default': lambda m: (
            f"🤫 <b>نجوای جدید</b>\n\n"
            f"┏━━━━━━━━━━━━━━\n"
            f"┃ {m}\n"
            f"┗━━━━━━━━━━━━━━\n"
            f"⏰ {timestamp}"
        ),
        'dark': lambda m: (
            f"🌙 <b>نجوای شبانه</b>\n\n"
            f"⬛️ {m} ⬛️\n\n"
            f"⏰ {timestamp}"
        ),
        'light': lambda m: (
            f"☀️ <b>نجوای روشن</b>\n\n"
            f"╭──────────────╮\n"
            f"  {m}\n"
            f"╰──────────────╯\n"
            f"⏰ {timestamp}"
        ),
        'fancy': lambda m: (
            f"✨ <b>نجوای جادویی</b> ✨\n\n"
            f"•°•°•°•°•°•°•°•°•°•\n"
            f"{m}\n"
            f"•°•°•°•°•°•°•°•°•°•\n"
            f"⏰ {timestamp}"
        ),
        'love': lambda m: (
            f"❤️ <b>نجوای عاشقانه</b> ❤️\n\n"
            f"💝 {m} 💝\n\n"
            f"⏰ {timestamp}"
        ),
        'business': lambda m: (
            f"💼 <b>پیام محرمانه</b>\n\n"
            f"📑 {m}\n\n"
            f"⏰ {timestamp}"
        )
    }
    
    formatted = themes.get(theme, themes['default'])(html.escape(message))
    if from_user:
        formatted += f"\n👤 از طرف: {html.escape(from_user)}"
    return formatted

async def create_whisper_preview(whisper_id: str, message: str, theme: str = 'default', to_username: str = None) -> dict:
    """Create a preview message for the whisper"""
    preview_text = "🤫 <b>یک نجوای جدید</b>"
    

    if to_username:
        preview_text += f"\n👤 برای: @{html.escape(to_username)}"
    
    preview_text += "\n\nبرای مشاهده پیام روی دکمه زیر کلیک کنید"
    

    view_data = f"view_{whisper_id}"  
    
    keyboard = [[
        InlineKeyboardButton("👀 مشاهده پیام", callback_data=view_data)
    ]]
    
    if theme != 'default':
        theme_icon = THEMES[theme].split()[0]
        preview_text = f"{theme_icon} {preview_text}"
    
    return {
        'text': preview_text,
        'reply_markup': InlineKeyboardMarkup(keyboard),
        'parse_mode': ParseMode.HTML
    }

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Enhanced start command handler"""
    user = update.effective_user
    welcome_message = (
        f"سلام {user.first_name} عزیز! 👋\n\n"
        "به ربات نجوا خوش آمدید! 🌟\n\n"
        "با این ربات می‌توانید:\n"
        "📨 پیام‌های خصوصی در گروه‌ها ارسال کنید\n"
        "🎨 از تم‌های مختلف استفاده کنید\n"
        "📊 آمار پیام‌های خود را ببینید\n"
        "⚙️ تنظیمات شخصی‌سازی داشته باشید\n\n"
        "برای شروع، از دکمه‌های زیر استفاده کنید:"
    )
    
    keyboard = [
        [
            InlineKeyboardButton("📝 ارسال نجوا", switch_inline_query_current_chat=""),
            InlineKeyboardButton("⚙️ تنظیمات", callback_data="settings")
        ],
        [
            InlineKeyboardButton("📚 راهنما", callback_data="help"),
            InlineKeyboardButton("📊 آمار من", callback_data="mystats")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    

    await db.get_or_create_user(
        user.id,
        username=user.username,
        first_name=user.first_name
    )
    
    await update.message.reply_text(
        welcome_message,
        reply_markup=reply_markup,
        parse_mode=ParseMode.HTML
    )

async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Enhanced help command handler"""
    help_text = (
        "🔰 راهنمای کامل ربات نجوا:\n\n"
        "📝 ارسال نجوا:\n"
        "1. ربات را در چت تگ کنید @NajvaWhisperBot\n"
        "2. پیام خود را بنویسید\n"
        "3. نام کاربری گیرنده را با @ در هر جای پیام وارد کنید\n\n"
        "🎨 تغییر تم نجوا:\n"
        "1. به تنظیمات بروید ⚙️\n"
        "2. گزینه 'تم نجوا' را انتخاب کنید\n"
        "3. از بین تم‌های موجود انتخاب کنید\n\n"
        "📊 مشاهده آمار:\n"
        "- تعداد نجواهای ارسالی\n"
        "- تعداد نجواهای دریافتی\n"
        "- نجواهای خوانده نشده\n\n"
        "⚡️ ویژگی‌های ویژه:\n"
        "- امکان ارسال نجوا با تایمر خودکار\n"
        "- قابلیت حذف خودکار پس از خواندن\n"
        "- پشتیبانی از فرمت‌های مختلف متن\n"
        "- امکان ارسال نجوای گروهی\n\n"
        "🔐 حریم خصوصی:\n"
        "- رمزنگاری پیام‌ها\n"
        "- عدم ذخیره محتوای حساس\n"
        "- کنترل کامل روی پیام‌های ارسالی"
    )
    
    keyboard = [
        [
            InlineKeyboardButton("🎯 شروع استفاده", switch_inline_query_current_chat=""),
            InlineKeyboardButton("📱 تنظیمات", callback_data="settings")
        ]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    
    await update.message.reply_text(
        help_text,
        reply_markup=reply_markup,
        parse_mode=ParseMode.HTML
    )

async def settings(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Enhanced settings command handler"""
    user = update.effective_user if update.message else update.callback_query.from_user
    user_settings = await db.get_user_settings(user.id) or {}
    
    if isinstance(user_settings, str):
        user_settings = json.loads(user_settings)
    
    notifications_status = "🔔 روشن" if user_settings.get('notifications', True) else "🔕 خاموش"
    auto_delete = "✅ فعال" if user_settings.get('auto_delete', False) else "❌ غیرفعال"
    current_theme = THEMES.get(user_settings.get('theme', 'default'), THEMES['default'])
    
    settings_text = (
        "⚙️ <b>تنظیمات نجوا</b>\n\n"
        f"🔔 وضعیت اعلان‌ها: {notifications_status}\n"
        f"🗑 حذف خودکار: {auto_delete}\n"
        f"🎨 تم فعلی: {current_theme}\n\n"
        "از دکمه‌های زیر برای تغییر تنظیمات استفاده کنید:"
    )
    
    keyboard = [
        [
            InlineKeyboardButton(f"اعلان‌ها: {notifications_status}", callback_data='toggle_notifications'),
            InlineKeyboardButton(f"حذف خودکار: {auto_delete}", callback_data='toggle_auto_delete')
        ],
        [
            InlineKeyboardButton("🎨 تغییر تم", callback_data='change_theme'),
            InlineKeyboardButton("⏱ تنظیم تایمر", callback_data='set_timer')
        ],
        [
            InlineKeyboardButton("📚 تاریخچه نجواها", callback_data='history'),
            InlineKeyboardButton("📊 آمار من", callback_data='stats')
        ],
        [
            InlineKeyboardButton("🏠 بازگشت به منو", callback_data='main_menu')
        ]
    ]
    
    if update.callback_query:
        await update.callback_query.edit_message_text(
            settings_text,
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode=ParseMode.HTML
        )
    else:
        await update.message.reply_text(
            settings_text,
            reply_markup=InlineKeyboardMarkup(keyboard),
            parse_mode=ParseMode.HTML
        )

async def inline_query(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Enhanced inline query handler"""
    query = update.inline_query.query
    user = update.effective_user
    logger.info(f"Received inline query from {user.id}: {query}")
    
    try:
        user_settings = await db.get_user_settings(user.id) or {}
        if isinstance(user_settings, str):
            user_settings = json.loads(user_settings)
        
        theme = user_settings.get('theme', 'default')
        

        import re
        username_match = re.search(r'@(\w+)', query)
        to_username = username_match.group(1) if username_match else None
        message = query.replace(f"@{to_username}", "").strip() if to_username else query
        
        results = []
        whisper_id = create_whisper_id()
        logger.info(f"Created whisper ID: {whisper_id}")
        
        if not query:

            templates = [
                ("👋 سلام! میشه صحبت کنیم؟", "درخواست گفتگو", "default"),
                ("📅 میتونیم قرار ملاقات بذاریم؟", "درخواست ملاقات", "business"),
                ("🤝 پیشنهاد همکاری دارم", "پیشنهاد کاری", "business"),
                ("❤️ یه پیام محرمانه دارم", "پیام شخصی", "love"),
                ("✨ یه سورپرایز برات دارم", "سورپرایز", "fancy"),
                ("🌙 شب بخیر عزیزم", "پیام شبانه", "dark")
            ]
            
            for template_text, description, template_theme in templates:
                template_whisper_id = create_whisper_id()
                formatted_message = await format_whisper(template_text, template_theme, user.first_name)
                preview = await create_whisper_preview(template_whisper_id, template_text, template_theme)
                
                results.append(
                    InlineQueryResultArticle(
                        id=template_whisper_id,
                        title=description,
                        description=template_text,
                        input_message_content=InputTextMessageContent(
                            message_text=preview['text'],
                            parse_mode=ParseMode.HTML
                        ),
                        reply_markup=preview['reply_markup']
                    )
                )
        else:

            title = "ارسال نجوا"
            if to_username:
                title += f" به @{to_username}"
            else:
                title += " (بدون مخاطب مشخص)"
            
            description = message[:50] + "..." if len(message) > 50 else message
            
            formatted_message = await format_whisper(message, theme, user.first_name)
            preview = await create_whisper_preview(whisper_id, message, theme, to_username)
            

            try:
                logger.info(f"Saving whisper {whisper_id} to database...")
                await db.save_whisper(
                    user.id,
                    to_username if to_username else "",
                    formatted_message,
                    whisper_id,
                    theme=theme
                )
                logger.info(f"Whisper {whisper_id} saved successfully")
            except Exception as save_error:
                logger.error(f"Error saving whisper: {save_error}")
                raise
            
            results.append(
                InlineQueryResultArticle(
                    id=whisper_id,
                    title=title,
                    description=description,
                    input_message_content=InputTextMessageContent(
                        message_text=preview['text'],
                        parse_mode=ParseMode.HTML
                    ),
                    reply_markup=preview['reply_markup']
                )
            )
        
        await update.inline_query.answer(results, cache_time=0)
        logger.info(f"Sent {len(results)} inline query results")
    
    except Exception as e:
        logger.error(f"Error in inline query: {e}")
        error_whisper_id = create_whisper_id()
        error_preview = await create_whisper_preview(
            error_whisper_id,
            "خطا در ارسال نجوا. لطفا دوباره تلاش کنید.",
            "default"
        )
        
        results = [
            InlineQueryResultArticle(
                id=error_whisper_id,
                title="خطا در ارسال نجوا",
                description="لطفا دوباره تلاش کنید",
                input_message_content=InputTextMessageContent(
                    message_text=error_preview['text'],
                    parse_mode=ParseMode.HTML
                ),
                reply_markup=error_preview['reply_markup']
            )
        ]
        await update.inline_query.answer(results, cache_time=0)

async def show_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Show user statistics"""
    user = update.callback_query.from_user
    stats = await db.get_user_stats(user.id)
    
    if stats:
        stats_text = (
            "📊 <b>آمار شما</b>\n\n"
            f"📤 نجواهای ارسالی: {stats['total_sent']}\n"
            f"📥 نجواهای دریافتی: {stats['total_received']}\n"
            f"📫 نجواهای نخوانده: {stats['unread_count']}\n"
            f"📅 عضویت از: {stats['member_since'].strftime('%Y-%m-%d')}\n"
            f"⏱ آخرین فعالیت: {stats['last_active'].strftime('%H:%M:%S')}"
        )
    else:
        stats_text = "❌ آماری موجود نیست"
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data="settings")]]
    await update.callback_query.edit_message_text(
        stats_text,
        reply_markup=InlineKeyboardMarkup(keyboard),
        parse_mode=ParseMode.HTML
    )

async def show_history(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Show user's whisper history"""
    user = update.callback_query.from_user
    history = await db.get_user_whisper_history(user.id, limit=5)
    
    history_text = "📚 <b>تاریخچه نجواها</b>\n\n"
    
    if history['sent']:
        history_text += "📤 <b>نجواهای ارسالی اخیر:</b>\n"
        for whisper in history['sent']:
            date = whisper['created_at'].strftime('%Y-%m-%d %H:%M')
            status = "✅ خوانده شده" if whisper['read_at'] else "👁 خوانده نشده"
            recipient = f"به @{whisper['to_username']}" if whisper['to_username'] else "بدون مخاطب"
            history_text += f"• {date} - {recipient} - {status}\n"
    else:
        history_text += "📤 نجوای ارسالی ندارید\n"
    
    history_text += "\n"
    
    if history['received']:
        history_text += "📥 <b>نجواهای دریافتی اخیر:</b>\n"
        for whisper in history['received']:
            date = whisper['created_at'].strftime('%Y-%m-%d %H:%M')
            history_text += f"• {date} - {whisper['theme']}\n"
    else:
        history_text += "📥 نجوای دریافتی ندارید"
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data="settings")]]
    await update.callback_query.edit_message_text(
        history_text,
        reply_markup=InlineKeyboardMarkup(keyboard),
        parse_mode=ParseMode.HTML
    )

async def toggle_setting(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Toggle user settings like notifications and auto-delete"""
    query = update.callback_query
    user = query.from_user
    data = query.data
    
    user_settings = await db.get_user_settings(user.id)
    if isinstance(user_settings, str):
        user_settings = json.loads(user_settings)
    
    if data == 'toggle_notifications':
        current = user_settings.get('notifications', True)
        user_settings['notifications'] = not current
        status = "روشن" if user_settings['notifications'] else "خاموش"
        await query.answer(f"🔔 اعلان‌ها {status} شد")
    
    elif data == 'toggle_auto_delete':
        current = user_settings.get('auto_delete', False)
        user_settings['auto_delete'] = not current
        status = "فعال" if user_settings['auto_delete'] else "غیرفعال"
        await query.answer(f"🗑 حذف خودکار {status} شد")
    
    await db.update_user_settings(user.id, json.dumps(user_settings))
    await settings(update, context)

async def button_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Enhanced button callback handler"""
    query = update.callback_query
    user = query.from_user
    data = query.data
    logger.info(f"Button callback from {user.id}: {data}")
    
    try:
        if data.startswith('view_'):

            whisper_id = data  
            if '_' in whisper_id:
                whisper_id = whisper_id.replace('view_', '')  
            
            logger.info(f"Fetching whisper {whisper_id} from database...")
            whisper = await db.get_whisper(whisper_id)
            logger.info(f"Whisper data: {whisper}")
            
            if not whisper:
                error_msg = (
                    "❌ این نجوا در دسترس نیست!\n"
                    f"شناسه نجوا: {whisper_id}\n"
                    "ممکن است حذف یا منقضی شده باشد."
                )
                logger.error(f"Whisper not found: {whisper_id}")
                await query.answer(error_msg, show_alert=True)
                return
            
            if whisper['to_username'] and whisper['to_username'] != "" and not user.username.lower() == whisper['to_username'].lower():
                await query.answer(
                    "⛔️ این نجوا برای شما نیست!\n"
                    "فقط گیرنده مشخص شده می‌تواند آن را بخواند.",
                    show_alert=True
                )
                return
            

            logger.info(f"Marking whisper {whisper_id} as read...")
            await db.mark_whisper_as_read(whisper_id, user.id)
            

            await query.answer(whisper['message'], show_alert=True)
            

            read_message = (
                f"✅ <b>نجوا خوانده شد</b>\n"
                f"⏰ {datetime.now().strftime('%H:%M')}"
            )
            

            user_settings = await db.get_user_settings(whisper['from_user_id']) or {}
            if isinstance(user_settings, str):
                user_settings = json.loads(user_settings)
            
            if user_settings.get('auto_delete', False):
                await query.edit_message_text(
                    "📭 این نجوا پس از خواندن حذف شد",
                    parse_mode=ParseMode.HTML
                )
            else:
                keyboard = [[
                    InlineKeyboardButton("✅ خوانده شد", callback_data="read_already")
                ]]
                await query.edit_message_text(
                    read_message,
                    reply_markup=InlineKeyboardMarkup(keyboard),
                    parse_mode=ParseMode.HTML
                )
        
        elif data == 'read_already':
            await query.answer("✅ این نجوا قبلاً خوانده شده است", show_alert=True)
        
        elif data == 'settings':
            await settings(update, context)
        
        elif data == 'help':
            await help_command(update, context)
        
        elif data == 'stats':
            await show_stats(update, context)
        
        elif data == 'history':
            await show_history(update, context)
        
        elif data in ['toggle_notifications', 'toggle_auto_delete']:
            await toggle_setting(update, context)
        
        elif data == 'main_menu':

            keyboard = [
                [
                    InlineKeyboardButton("📝 ارسال نجوا", switch_inline_query_current_chat=""),
                    InlineKeyboardButton("⚙️ تنظیمات", callback_data="settings")
                ],
                [
                    InlineKeyboardButton("📚 راهنما", callback_data="help"),
                    InlineKeyboardButton("📊 آمار من", callback_data="stats")
                ]
            ]
            await query.edit_message_text(
                "🏠 منوی اصلی\n\nاز دکمه‌های زیر استفاده کنید:",
                reply_markup=InlineKeyboardMarkup(keyboard),
                parse_mode=ParseMode.HTML
            )
        
        else:
            await query.answer("⚙️ این قابلیت به زودی اضافه خواهد شد", show_alert=True)
    
    except Exception as e:
        logger.error(f"Error in button callback: {e}")
        await query.answer(
            "❌ خطایی رخ داد. لطفا دوباره تلاش کنید.\n"
            "اگر مشکل ادامه داشت، دوباره نجوا را ارسال کنید.",
            show_alert=True
        )

async def post_init(application: Application):
    """Post initialization hook"""
    logger.info("Initializing database...")
    await db.init_db()
    logger.info("Database initialized")

def main():
    """Start the bot"""
    try:

        logger.info("Creating application...")
        application = Application.builder().token(TOKEN).post_init(post_init).build()
        

        logger.info("Registering handlers...")
        application.add_handler(CommandHandler("start", start))
        application.add_handler(CommandHandler("help", help_command))
        application.add_handler(CommandHandler("settings", settings))
        application.add_handler(InlineQueryHandler(inline_query))
        application.add_handler(CallbackQueryHandler(button_callback))
        

        logger.info("Starting bot...")
        application.run_polling(allowed_updates=Update.ALL_TYPES)
        logger.info("Bot stopped")
    
    except Exception as e:
        logger.error(f"Fatal error: {e}")
        raise

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        logger.info("Bot stopped by user")
    except Exception as e:
        logger.error(f"Error: {e}")
        raise