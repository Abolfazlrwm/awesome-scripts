# ========== ربات یادآوری و تسک روزانه (Reminder & Task Bot) ==========
# /task <متن>          -> افزودن یک کار به لیست تسک‌ها (بدون زمان)
# /tasks                -> نمایش لیست تسک‌ها با دکمه‌ی انجام‌شد/حذف
# /remind <دقیقه> <متن> -> یادآوری یک‌باره بعد از N دقیقه
# /remind_at HH:MM <متن>-> یادآوری یک‌باره امروز (یا فردا اگر ساعت گذشته) در ساعت مشخص
# /remind_daily HH:MM <متن> -> یادآوری تکرارشونده‌ی روزانه در ساعت مشخص
# /myreminders          -> لیست یادآوری‌های فعال با دکمه‌ی لغو
# دکمه‌ی «۱۰ دقیقه دیگه یادم بنداز» زیر هر یادآوری هم هست (Snooze)

import sqlite3
import logging
from datetime import datetime, timedelta, time as dtime

from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    Application, CommandHandler, CallbackQueryHandler, ContextTypes
)

logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

# ---------- تنظیمات ----------
BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"   # توکن ربات را از @BotFather بگیر
DB_PATH = "reminders.db"


# ---------- دیتابیس ----------
def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_db()
    conn.execute("""CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        chat_id INTEGER NOT NULL,
        text TEXT NOT NULL,
        done INTEGER DEFAULT 0,
        created_at TEXT
    )""")
    conn.execute("""CREATE TABLE IF NOT EXISTS reminders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        chat_id INTEGER NOT NULL,
        text TEXT NOT NULL,
        run_at TEXT,          -- ISO datetime for one-shot reminders
        daily_time TEXT,      -- "HH:MM" for recurring daily reminders
        active INTEGER DEFAULT 1
    )""")
    conn.commit()
    conn.close()


# ---------- تسک‌ها ----------
async def cmd_task(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.args:
        await update.message.reply_text("فرمت درست: /task خرید نان و شیر")
        return
    text = " ".join(context.args)
    conn = get_db()
    conn.execute(
        "INSERT INTO tasks (chat_id, text, created_at) VALUES (?,?,?)",
        (update.effective_chat.id, text, datetime.now().isoformat())
    )
    conn.commit()
    conn.close()
    await update.message.reply_text(f"✅ اضافه شد: {text}")


def build_tasks_keyboard(rows):
    buttons = []
    for row in rows:
        mark = "☑️" if row["done"] else "⬜️"
        buttons.append([
            InlineKeyboardButton(f"{mark} {row['text']}", callback_data=f"tsk_toggle_{row['id']}"),
            InlineKeyboardButton("🗑", callback_data=f"tsk_del_{row['id']}"),
        ])
    return InlineKeyboardMarkup(buttons) if buttons else None


async def cmd_tasks(update: Update, context: ContextTypes.DEFAULT_TYPE):
    conn = get_db()
    rows = conn.execute(
        "SELECT * FROM tasks WHERE chat_id=? ORDER BY done ASC, id DESC",
        (update.effective_chat.id,)
    ).fetchall()
    conn.close()
    if not rows:
        await update.message.reply_text("لیست تسک‌هات خالیه 🎉 با /task یه کار اضافه کن.")
        return
    await update.message.reply_text("📋 تسک‌های تو:", reply_markup=build_tasks_keyboard(rows))


async def on_task_button(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    data = query.data
    conn = get_db()
    if data.startswith("tsk_toggle_"):
        task_id = int(data.split("_")[-1])
        row = conn.execute("SELECT done FROM tasks WHERE id=?", (task_id,)).fetchone()
        if row is not None:
            conn.execute("UPDATE tasks SET done=? WHERE id=?", (0 if row["done"] else 1, task_id))
            conn.commit()
    elif data.startswith("tsk_del_"):
        task_id = int(data.split("_")[-1])
        conn.execute("DELETE FROM tasks WHERE id=?", (task_id,))
        conn.commit()

    rows = conn.execute(
        "SELECT * FROM tasks WHERE chat_id=? ORDER BY done ASC, id DESC",
        (query.message.chat_id,)
    ).fetchall()
    conn.close()
    if rows:
        await query.edit_message_text("📋 تسک‌های تو:", reply_markup=build_tasks_keyboard(rows))
    else:
        await query.edit_message_text("لیست تسک‌هات خالیه 🎉")


# ---------- یادآوری‌ها ----------
async def send_reminder(context: ContextTypes.DEFAULT_TYPE):
    job = context.job
    chat_id = job.data["chat_id"]
    text = job.data["text"]
    reminder_id = job.data.get("reminder_id")
    keyboard = InlineKeyboardMarkup([[
        InlineKeyboardButton("⏰ ۱۰ دقیقه دیگه یادم بنداز", callback_data=f"snooze_{reminder_id}_{chat_id}")
    ]])
    await context.bot.send_message(chat_id=chat_id, text=f"🔔 یادآوری:\n{text}", reply_markup=keyboard)

    # one-shot reminders get deactivated after firing; daily ones stay active
    if reminder_id is not None and not job.data.get("daily"):
        conn = get_db()
        conn.execute("UPDATE reminders SET active=0 WHERE id=?", (reminder_id,))
        conn.commit()
        conn.close()


def schedule_once(app: Application, chat_id: int, text: str, run_at: datetime, reminder_id: int):
    delay = max(1, (run_at - datetime.now()).total_seconds())
    app.job_queue.run_once(
        send_reminder, when=delay,
        data={"chat_id": chat_id, "text": text, "reminder_id": reminder_id, "daily": False},
        name=f"reminder_{reminder_id}"
    )


def schedule_daily(app: Application, chat_id: int, text: str, hh: int, mm: int, reminder_id: int):
    app.job_queue.run_daily(
        send_reminder, time=dtime(hour=hh, minute=mm),
        data={"chat_id": chat_id, "text": text, "reminder_id": reminder_id, "daily": True},
        name=f"reminder_{reminder_id}"
    )


async def cmd_remind(update: Update, context: ContextTypes.DEFAULT_TYPE):
    # /remind <minutes> <text>
    if len(context.args) < 2 or not context.args[0].isdigit():
        await update.message.reply_text("فرمت درست: /remind 30 آب بخور  (یعنی ۳۰ دقیقه دیگه)")
        return
    minutes = int(context.args[0])
    text = " ".join(context.args[1:])
    run_at = datetime.now() + timedelta(minutes=minutes)

    conn = get_db()
    cur = conn.execute(
        "INSERT INTO reminders (chat_id, text, run_at) VALUES (?,?,?)",
        (update.effective_chat.id, text, run_at.isoformat())
    )
    conn.commit()
    reminder_id = cur.lastrowid
    conn.close()

    schedule_once(context.application, update.effective_chat.id, text, run_at, reminder_id)
    await update.message.reply_text(f"⏰ باشه، {minutes} دقیقه‌ی دیگه یادت می‌ندازم: {text}")


async def cmd_remind_at(update: Update, context: ContextTypes.DEFAULT_TYPE):
    # /remind_at HH:MM <text>
    if len(context.args) < 2 or ":" not in context.args[0]:
        await update.message.reply_text("فرمت درست: /remind_at 18:30 جلسه با تیم")
        return
    try:
        hh, mm = map(int, context.args[0].split(":"))
        run_at = datetime.now().replace(hour=hh, minute=mm, second=0, microsecond=0)
        if run_at <= datetime.now():
            run_at += timedelta(days=1)
    except ValueError:
        await update.message.reply_text("ساعت نامعتبره. مثال درست: /remind_at 09:00 متن")
        return
    text = " ".join(context.args[1:])

    conn = get_db()
    cur = conn.execute(
        "INSERT INTO reminders (chat_id, text, run_at) VALUES (?,?,?)",
        (update.effective_chat.id, text, run_at.isoformat())
    )
    conn.commit()
    reminder_id = cur.lastrowid
    conn.close()

    schedule_once(context.application, update.effective_chat.id, text, run_at, reminder_id)
    await update.message.reply_text(f"⏰ باشه، در {run_at.strftime('%H:%M')} یادت می‌ندازم: {text}")


async def cmd_remind_daily(update: Update, context: ContextTypes.DEFAULT_TYPE):
    # /remind_daily HH:MM <text>
    if len(context.args) < 2 or ":" not in context.args[0]:
        await update.message.reply_text("فرمت درست: /remind_daily 08:00 ویتامین‌هات رو بخور")
        return
    try:
        hh, mm = map(int, context.args[0].split(":"))
    except ValueError:
        await update.message.reply_text("ساعت نامعتبره. مثال درست: /remind_daily 08:00 متن")
        return
    text = " ".join(context.args[1:])

    conn = get_db()
    cur = conn.execute(
        "INSERT INTO reminders (chat_id, text, daily_time) VALUES (?,?,?)",
        (update.effective_chat.id, text, f"{hh:02d}:{mm:02d}")
    )
    conn.commit()
    reminder_id = cur.lastrowid
    conn.close()

    schedule_daily(context.application, update.effective_chat.id, text, hh, mm, reminder_id)
    await update.message.reply_text(f"🔁 باشه، هر روز ساعت {hh:02d}:{mm:02d} یادت می‌ندازم: {text}")


async def cmd_myreminders(update: Update, context: ContextTypes.DEFAULT_TYPE):
    conn = get_db()
    rows = conn.execute(
        "SELECT * FROM reminders WHERE chat_id=? AND active=1 ORDER BY id DESC",
        (update.effective_chat.id,)
    ).fetchall()
    conn.close()
    if not rows:
        await update.message.reply_text("یادآوری فعالی نداری.")
        return
    buttons = []
    for r in rows:
        when = r["daily_time"] and f"هر روز {r['daily_time']}" or r["run_at"][:16].replace("T", " ")
        buttons.append([InlineKeyboardButton(f"🗑 {when} — {r['text'][:24]}", callback_data=f"rem_cancel_{r['id']}")])
    await update.message.reply_text("⏰ یادآوری‌های فعال (برای لغو لمس کن):", reply_markup=InlineKeyboardMarkup(buttons))


async def on_reminder_button(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    data = query.data

    if data.startswith("rem_cancel_"):
        reminder_id = int(data.split("_")[-1])
        conn = get_db()
        conn.execute("UPDATE reminders SET active=0 WHERE id=?", (reminder_id,))
        conn.commit()
        conn.close()
        for job in context.application.job_queue.get_jobs_by_name(f"reminder_{reminder_id}"):
            job.schedule_removal()
        await query.edit_message_text("لغو شد ✅")

    elif data.startswith("snooze_"):
        _, reminder_id, chat_id = data.split("_")
        chat_id = int(chat_id)
        # re-fetch original text from the message itself
        original_text = query.message.text.replace("🔔 یادآوری:\n", "")
        run_at = datetime.now() + timedelta(minutes=10)
        context.application.job_queue.run_once(
            send_reminder, when=timedelta(minutes=10),
            data={"chat_id": chat_id, "text": original_text, "reminder_id": None, "daily": False},
            name=f"snooze_{chat_id}_{int(run_at.timestamp())}"
        )
        await query.edit_message_text(f"🔔 یادآوری:\n{original_text}\n\n⏰ باشه، ۱۰ دقیقه‌ی دیگه دوباره می‌گم.")


# ---------- بازیابی یادآوری‌ها بعد از ری‌استارت ----------
async def reload_pending_reminders(app: Application):
    conn = get_db()
    rows = conn.execute("SELECT * FROM reminders WHERE active=1").fetchall()
    conn.close()
    count = 0
    for r in rows:
        if r["daily_time"]:
            hh, mm = map(int, r["daily_time"].split(":"))
            schedule_daily(app, r["chat_id"], r["text"], hh, mm, r["id"])
            count += 1
        elif r["run_at"]:
            run_at = datetime.fromisoformat(r["run_at"])
            if run_at > datetime.now():
                schedule_once(app, r["chat_id"], r["text"], run_at, r["id"])
                count += 1
            else:
                conn = get_db()
                conn.execute("UPDATE reminders SET active=0 WHERE id=?", (r["id"],))
                conn.commit()
                conn.close()
    logger.info(f"{count} یادآوری فعال دوباره زمان‌بندی شد.")


async def cmd_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text(
        "سلام! من ربات یادآوری و تسک هستم 👋\n\n"
        "📋 تسک‌ها:\n"
        "/task <متن> — افزودن کار\n"
        "/tasks — نمایش لیست کارها\n\n"
        "⏰ یادآوری‌ها:\n"
        "/remind <دقیقه> <متن> — مثال: /remind 30 آب بخور\n"
        "/remind_at HH:MM <متن> — مثال: /remind_at 18:30 جلسه\n"
        "/remind_daily HH:MM <متن> — یادآوری تکرارشونده‌ی روزانه\n"
        "/myreminders — لیست یادآوری‌های فعال"
    )


def main():
    init_db()
    app = Application.builder().token(BOT_TOKEN).post_init(reload_pending_reminders).build()

    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("task", cmd_task))
    app.add_handler(CommandHandler("tasks", cmd_tasks))
    app.add_handler(CommandHandler("remind", cmd_remind))
    app.add_handler(CommandHandler("remind_at", cmd_remind_at))
    app.add_handler(CommandHandler("remind_daily", cmd_remind_daily))
    app.add_handler(CommandHandler("myreminders", cmd_myreminders))
    app.add_handler(CallbackQueryHandler(on_task_button, pattern=r"^tsk_"))
    app.add_handler(CallbackQueryHandler(on_reminder_button, pattern=r"^(rem_cancel_|snooze_)"))

    logger.info("ربات یادآوری و تسک روشن شد...")
    app.run_polling()


if __name__ == "__main__":
    main()
