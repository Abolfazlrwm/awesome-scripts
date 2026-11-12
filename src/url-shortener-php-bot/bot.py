# Telegram: @Abolfazl_rwm
import sqlite3
import time
import requests
from telegram import Update
from telegram.ext import Application, CommandHandler, MessageHandler, filters, CallbackContext
from threading import Lock
from datetime import datetime, timedelta

import os
TOKEN = os.environ.get("BOT_TOKEN", "") #توکن ربات 
ADMIN_ID = int(os.environ.get("ADMIN_ID", "0") or 0) # آیدی ادمین

conn = sqlite3.connect("bot.db", check_same_thread=False)
cursor = conn.cursor()
cursor.execute('''CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY, 
    blocked INTEGER DEFAULT 0, 
    last_message_time REAL, 
    message_count INTEGER DEFAULT 0, 
    block_until REAL DEFAULT 0
)''')
cursor.execute('''CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY, 
    value TEXT
)''')
cursor.execute('''CREATE TABLE IF NOT EXISTS messages (
    user_id INTEGER, 
    message TEXT, 
    timestamp REAL
)''')
conn.commit()

cursor.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('start_message', 'خوش آمدید! لینک خود را ارسال کنید تا کوتاه شود.')")
cursor.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('help_message', 'لطفاً یک لینک معتبر ارسال کنید تا کوتاه شود.')")
cursor.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('force_join', '0')")
cursor.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('channel_id', '')")
conn.commit()

db_lock = Lock()

def shorten_url(url):
    try:
        response = requests.get(f"http://tinyurl.com/api-create.php?url={url}")
        if response.status_code == 200:
            return response.text
        return None
    except:
        return None

async def check_channel_membership(context: CallbackContext, user_id: int):
    cursor.execute("SELECT value FROM settings WHERE key='force_join'")
    force_join = cursor.fetchone()[0] == '1'
    if not force_join:
        return True
    cursor.execute("SELECT value FROM settings WHERE key='channel_id'")
    channel_id = cursor.fetchone()[0]
    if not channel_id:
        return True
    try:
        member = await context.bot.get_chat_member(channel_id, user_id)
        return member.status in ['member', 'administrator', 'creator']
    except:
        return False

def check_rate_limit(user_id: int):
    with db_lock:
        cursor.execute("SELECT last_message_time, message_count, block_until FROM users WHERE user_id=?", (user_id,))
        result = cursor.fetchone()
        current_time = time.time()

        if result:
            last_time, msg_count, block_until = result
            if block_until > current_time:
                return False, f"شما تا {int(block_until - current_time)} ثانیه دیگر مسدود هستید."
            
            if last_time and current_time - last_time < 30:
                if msg_count >= 3:
                    block_until = current_time + 300
                    cursor.execute("UPDATE users SET block_until=? WHERE user_id=?", (block_until, user_id))
                    conn.commit()
                    return False, "شما بیش از حد پیام ارسال کردید. ۵ دقیقه مسدود شدید."
                cursor.execute("UPDATE users SET message_count=? WHERE user_id=?", (msg_count + 1, user_id))
            else:
                cursor.execute("UPDATE users SET message_count=1, last_message_time=? WHERE user_id=?", (current_time, user_id))
        else:
            cursor.execute("INSERT INTO users (user_id, last_message_time, message_count) VALUES (?, ?, 1)", (user_id, current_time))
        conn.commit()
        return True, ""

async def start(update: Update, context: CallbackContext):
    user_id = update.effective_user.id
    if not await check_channel_membership(context, user_id):
        cursor.execute("SELECT value FROM settings WHERE key='channel_id'")
        channel_id = cursor.fetchone()[0]
        await update.message.reply_text(f"لطفاً ابتدا در کانال {channel_id} عضو شوید.")
        return
    cursor.execute("SELECT value FROM settings WHERE key='start_message'")
    start_message = cursor.fetchone()[0]
    await update.message.reply_text(start_message)

async def help_command(update: Update, context: CallbackContext):
    user_id = update.effective_user.id
    if not await check_channel_membership(context, user_id):
        cursor.execute("SELECT value FROM settings WHERE key='channel_id'")
        channel_id = cursor.fetchone()[0]
        await update.message.reply_text(f"لطفاً ابتدا در کانال {channel_id} عضو شوید.")
        return
    cursor.execute("SELECT value FROM settings WHERE key='help_message'")
    help_message = cursor.fetchone()[0]
    await update.message.reply_text(help_message)

async def handle_message(update: Update, context: CallbackContext):
    user_id = update.effective_user.id
    text = update.message.text

    allowed, error_message = check_rate_limit(user_id)
    if not allowed:
        await update.message.reply_text(error_message)
        return

    if not await check_channel_membership(context, user_id):
        cursor.execute("SELECT value FROM settings WHERE key='channel_id'")
        channel_id = cursor.fetchone()[0]
        await update.message.reply_text(f"لطفاً ابتدا در کانال {channel_id} عضو شوید.")
        return

    if text.startswith("/support"):
        message = text[8:].strip()
        if message:
            with db_lock:
                cursor.execute("INSERT INTO messages (user_id, message, timestamp) VALUES (?, ?, ?)", 
                              (user_id, message, time.time()))
                conn.commit()
            await context.bot.send_message(ADMIN_ID, f"پیام از {user_id}:\n{message}")
            await update.message.reply_text("پیام شما به پشتیبانی ارسال شد.")
        else:
            await update.message.reply_text("لطفاً پیام خود را بعد از /support وارد کنید.")
        return

    shortened_url = shorten_url(text)
    if shortened_url:
        await update.message.reply_text(f"لینک کوتاه شده: {shortened_url}")
    else:
        await update.message.reply_text("لطفاً یک لینک معتبر وارد کنید.")

async def admin_panel(update: Update, context: CallbackContext):
    user_id = update.effective_user.id
    if user_id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی به پنل مدیریت ندارید.")
        return
    commands = (
        "/set_force_join <channel_id> - تنظیم جوین اجباری\n"
        "/remove_force_join - حذف جوین اجباری\n"
        "/set_start <message> - تغییر متن استارت\n"
        "/set_help <message> - تغییر متن راهنما\n"
        "/ban <user_id> - مسدود کردن کاربر\n"
        "/unban <user_id> - لغو مسدودیت کاربر\n"
        "/broadcast <message> - ارسال پیام همگانی\n"
        "/send <user_id> <message> - ارسال پیام تکی"
    )
    await update.message.reply_text(f"دستورات پنل مدیریت:\n{commands}")

async def set_force_join(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً آیدی کانال را وارد کنید (مثال: /set_force_join @Abolfazl_rwm)")
        return
    channel_id = context.args[0]
    with db_lock:
        cursor.execute("UPDATE settings SET value='1' WHERE key='force_join'")
        cursor.execute("UPDATE settings SET value=? WHERE key='channel_id'", (channel_id,))
        conn.commit()
    await update.message.reply_text(f"جوین اجباری برای کانال {channel_id} فعال شد.")

async def remove_force_join(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    with db_lock:
        cursor.execute("UPDATE settings SET value='0' WHERE key='force_join'")
        cursor.execute("UPDATE settings SET value='' WHERE key='channel_id'")
        conn.commit()
    await update.message.reply_text("جوین اجباری غیرفعال شد.")

async def set_start(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً متن جدید را وارد کنید.")
        return
    new_message = " ".join(context.args)
    with db_lock:
        cursor.execute("UPDATE settings SET value=? WHERE key='start_message'", (new_message,))
        conn.commit()
    await update.message.reply_text("متن استارت تغییر کرد.")

async def set_help(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً متن جدید را وارد کنید.")
        return
    new_message = " ".join(context.args)
    with db_lock:
        cursor.execute("UPDATE settings SET value=? WHERE key='help_message'", (new_message,))
        conn.commit()
    await update.message.reply_text("متن راهنما تغییر کرد.")

async def ban(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً آیدی کاربر را وارد کنید.")
        return
    try:
        user_id = int(context.args[0])
        with db_lock:
            cursor.execute("UPDATE users SET blocked=1 WHERE user_id=?", (user_id,))
            if cursor.rowcount == 0:
                cursor.execute("INSERT INTO users (user_id, blocked) VALUES (?, 1)", (user_id,))
            conn.commit()
        await update.message.reply_text(f"کاربر {user_id} مسدود شد.")
    except:
        await update.message.reply_text("لطفاً یک آیدی معتبر وارد کنید.")

async def unban(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً آیدی کاربر را وارد کنید.")
        return
    try:
        user_id = int(context.args[0])
        with db_lock:
            cursor.execute("UPDATE users SET blocked=0, block_until=0 WHERE user_id=?", (user_id,))
            conn.commit()
        await update.message.reply_text(f"مسدودیت کاربر {user_id} لغو شد.")
    except:
        await update.message.reply_text("لطفاً یک آیدی معتبر وارد کنید.")

async def broadcast(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if not context.args:
        await update.message.reply_text("لطفاً متن پیام را وارد کنید.")
        return
    message = " ".join(context.args)
    with db_lock:
        cursor.execute("SELECT user_id FROM users")
        users = cursor.fetchall()
    for user in users:
        try:
            await context.bot.send_message(user[0], message)
        except:
            pass
    await update.message.reply_text("پیام همگانی ارسال شد.")

async def send(update: Update, context: CallbackContext):
    if update.effective_user.id != ADMIN_ID:
        await update.message.reply_text("شما دسترسی ندارید.")
        return
    if len(context.args) < 2:
        await update.message.reply_text("لطفاً آیدی کاربر و متن پیام را وارد کنید (مثال: /send 123456789 متن پیام)")
        return
    try:
        user_id = int(context.args[0])
        message = " ".join(context.args[1:])
        await context.bot.send_message(user_id, message)
        await update.message.reply_text(f"پیام به کاربر {user_id} ارسال شد.")
    except:
        await update.message.reply_text("لطفاً یک آیدی معتبر و متن پیام وارد کنید.")

async def main():
    app = Application.builder().token(TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("help", help_command))
    app.add_handler(CommandHandler("admin", admin_panel))
    app.add_handler(CommandHandler("set_force_join", set_force_join))
    app.add_handler(CommandHandler("remove_force_join", remove_force_join))
    app.add_handler(CommandHandler("set_start", set_start))
    app.add_handler(CommandHandler("set_help", set_help))
    app.add_handler(CommandHandler("ban", ban))
    app.add_handler(CommandHandler("unban", unban))
    app.add_handler(CommandHandler("broadcast", broadcast))
    app.add_handler(CommandHandler("send", send))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))

    await app.run_polling()

if __name__ == '__main__':
    import asyncio
    asyncio.run(main())
