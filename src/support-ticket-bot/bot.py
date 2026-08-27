# ========== ربات پشتیبانی و تیکتینگ (Support Ticket Bot) ==========
# کاربر توی پیوی پیام می‌ده -> ربات به گروه پشتیبانی فوروارد می‌کنه
# ادمین توی گروه روی همون پیام ریپلای می‌کنه -> ربات جواب رو برای کاربر می‌فرسته

import sqlite3
import logging
from datetime import datetime
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import Application, CommandHandler, MessageHandler, ContextTypes, filters

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# ---------- تنظیمات ----------
BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"       # توکن ربات را از @BotFather بگیر
SUPPORT_GROUP_ID = -1001234567890        # آیدی عددی گروه پشتیبانی (با فوروارد یک پیام از گروه به @userinfobot پیدا کن)
DB_PATH = "tickets.db"


def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_db()
    conn.execute("""CREATE TABLE IF NOT EXISTS tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        user_name TEXT,
        status TEXT DEFAULT 'open',
        created_at TEXT
    )""")
    # نگاشت بین پیام فوروادشده در گروه پشتیبانی و تیکت مربوطه (برای ریپلای)
    conn.execute("""CREATE TABLE IF NOT EXISTS message_map (
        support_message_id INTEGER PRIMARY KEY,
        ticket_id INTEGER,
        user_id INTEGER
    )""")
    conn.commit()
    conn.close()


def get_or_create_open_ticket(user_id, user_name):
    conn = get_db()
    ticket = conn.execute(
        "SELECT * FROM tickets WHERE user_id=? AND status='open' ORDER BY id DESC LIMIT 1",
        (user_id,)
    ).fetchone()
    if ticket:
        conn.close()
        return ticket["id"]

    cur = conn.execute(
        "INSERT INTO tickets (user_id, user_name, status, created_at) VALUES (?,?,?,?)",
        (user_id, user_name, "open", datetime.now().isoformat())
    )
    conn.commit()
    ticket_id = cur.lastrowid
    conn.close()
    return ticket_id


def close_ticket(ticket_id):
    conn = get_db()
    conn.execute("UPDATE tickets SET status='closed' WHERE id=?", (ticket_id,))
    conn.commit()
    conn.close()


def save_message_map(support_message_id, ticket_id, user_id):
    conn = get_db()
    conn.execute(
        "INSERT OR REPLACE INTO message_map (support_message_id, ticket_id, user_id) VALUES (?,?,?)",
        (support_message_id, ticket_id, user_id)
    )
    conn.commit()
    conn.close()


def get_ticket_by_support_message(support_message_id):
    conn = get_db()
    row = conn.execute(
        "SELECT * FROM message_map WHERE support_message_id=?", (support_message_id,)
    ).fetchone()
    conn.close()
    return row


# ---------- دستورات عمومی ----------
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type != "private":
        return
    await update.message.reply_text(
        "🎫 سلام! هر سوال یا مشکلی داری بنویس، مستقیم به تیم پشتیبانی می‌رسه.\n"
        "به‌محض اینکه جواب داده بشه همینجا بهت اطلاع می‌دیم."
    )


async def close_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type != "private":
        return
    user_id = update.effective_user.id
    conn = get_db()
    ticket = conn.execute(
        "SELECT * FROM tickets WHERE user_id=? AND status='open' ORDER BY id DESC LIMIT 1",
        (user_id,)
    ).fetchone()
    conn.close()

    if not ticket:
        await update.message.reply_text("تیکت باز فعالی نداری.")
        return

    close_ticket(ticket["id"])
    await update.message.reply_text(f"✅ تیکت #{ticket['id']} بسته شد. برای سوال جدید فقط پیام بده.")
    await context.bot.send_message(
        SUPPORT_GROUP_ID,
        f"🔒 تیکت #{ticket['id']} توسط کاربر بسته شد."
    )


# ---------- پیام کاربر در پیوی -> فوروارد به گروه پشتیبانی ----------
async def handle_private_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type != "private":
        return
    if not update.message or update.message.text is None:
        return
    if update.message.text.startswith("/"):
        return

    user = update.effective_user
    ticket_id = get_or_create_open_ticket(user.id, user.full_name)

    header = (
        f"🎫 <b>تیکت #{ticket_id}</b>\n"
        f"👤 {user.full_name} (@{user.username or 'بدون یوزرنیم'})\n"
        f"🆔 <code>{user.id}</code>\n"
        f"━━━━━━━━━━━━━\n"
        f"{update.message.text}"
    )
    keyboard = InlineKeyboardMarkup([[
        InlineKeyboardButton("🔒 بستن تیکت", callback_data=f"close_{ticket_id}")
    ]])

    sent = await context.bot.send_message(
        SUPPORT_GROUP_ID, header, parse_mode="HTML", reply_markup=keyboard
    )
    save_message_map(sent.message_id, ticket_id, user.id)

    await update.message.reply_text("✅ پیامت به تیم پشتیبانی ارسال شد. منتظر جواب باش.")


# ---------- ریپلای ادمین در گروه پشتیبانی -> ارسال به کاربر ----------
async def handle_support_reply(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.id != SUPPORT_GROUP_ID:
        return
    if not update.message.reply_to_message:
        return

    replied_id = update.message.reply_to_message.message_id
    mapping = get_ticket_by_support_message(replied_id)
    if not mapping:
        return  # این ریپلای مربوط به یه پیام تیکت نبود

    target_user_id = mapping["user_id"]
    ticket_id = mapping["ticket_id"]

    try:
        await context.bot.send_message(
            target_user_id,
            f"💬 <b>پاسخ پشتیبانی (تیکت #{ticket_id}):</b>\n\n{update.message.text}",
            parse_mode="HTML"
        )
        await update.message.reply_text("✅ پاسخ برای کاربر ارسال شد.")
    except Exception as e:
        await update.message.reply_text(f"❌ خطا در ارسال پاسخ به کاربر: {e}")


# ---------- بستن تیکت با دکمه ----------
async def handle_close_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    ticket_id = int(query.data.split("_")[1])
    close_ticket(ticket_id)
    await query.edit_message_reply_markup(reply_markup=None)
    await context.bot.send_message(SUPPORT_GROUP_ID, f"🔒 تیکت #{ticket_id} بسته شد.")


# ---------- دستور آمار (فقط داخل گروه پشتیبانی) ----------
async def stats_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.id != SUPPORT_GROUP_ID:
        return
    conn = get_db()
    open_count = conn.execute("SELECT COUNT(*) c FROM tickets WHERE status='open'").fetchone()["c"]
    closed_count = conn.execute("SELECT COUNT(*) c FROM tickets WHERE status='closed'").fetchone()["c"]
    conn.close()
    await update.message.reply_text(f"📊 تیکت‌های باز: {open_count}\n📁 تیکت‌های بسته‌شده: {closed_count}")


def main():
    init_db()
    app = Application.builder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("close", close_cmd))
    app.add_handler(CommandHandler("stats", stats_cmd))

    from telegram.ext import CallbackQueryHandler
    app.add_handler(CallbackQueryHandler(handle_close_callback, pattern=r"^close_\d+$"))

    # پیام‌های پیوی (غیر از دستورات) -> کاربر جدید یا ادامه‌ی تیکت
    app.add_handler(MessageHandler(
        filters.ChatType.PRIVATE & filters.TEXT & ~filters.COMMAND, handle_private_message
    ))
    # ریپلای‌های داخل گروه پشتیبانی -> پاسخ به کاربر
    app.add_handler(MessageHandler(
        filters.Chat(chat_id=SUPPORT_GROUP_ID) & filters.TEXT & filters.REPLY, handle_support_reply
    ))

    logger.info("🎫 ربات پشتیبانی روشن شد...")
    app.run_polling(allowed_updates=Update.ALL_TYPES)


if __name__ == "__main__":
    main()
