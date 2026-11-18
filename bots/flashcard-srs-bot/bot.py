#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Flashcard SRS bot — spaced-repetition study bot for Telegram.

Create decks, add front/back cards, and study with a lightweight SM-2
scheduler: after seeing the answer you grade yourself
(again / hard / good / easy) and the bot schedules the next review —
minutes for lapses, days for successful recalls — so every card shows
up right before you would forget it. Includes due-count overview and
per-deck progress stats.

Stdlib (sqlite3) + python-telegram-bot.
"""

from __future__ import annotations

import datetime as dt
import math
import sqlite3
from pathlib import Path

from telegram import InlineKeyboardButton, InlineKeyboardMarkup, Update
from telegram.ext import (
    Application,
    CallbackQueryHandler,
    CommandHandler,
    ContextTypes,
    MessageHandler,
    filters,
)

BOT_TOKEN = ""  # توکن ربات از @BotFather
DB_PATH = Path(__file__).with_name("flashcards.db")

TEHRAN = dt.timezone(dt.timedelta(hours=3, minutes=30))

# Grade buttons → (label, quality, interval multiplier)
GRADES = [
    ("🔁 فراموش کردم", 0, 0.0),
    ("😰 سخت", 3, 1.2),
    ("🙂 خوب", 4, 1.0),
    ("😎 آسان", 5, 1.3),
]
LEARNED_DAYS = 21  # a card with interval ≥ this is considered "learned"


def now() -> dt.datetime:
    return dt.datetime.now(TEHRAN)


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS decks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    created TEXT NOT NULL,
    UNIQUE(user_id, name)
);
CREATE TABLE IF NOT EXISTS cards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    deck_id INTEGER NOT NULL REFERENCES decks(id) ON DELETE CASCADE,
    front TEXT NOT NULL,
    back TEXT NOT NULL,
    ease REAL NOT NULL DEFAULT 2.5,
    interval_min INTEGER NOT NULL DEFAULT 0,   -- minutes until next review
    reps INTEGER NOT NULL DEFAULT 0,
    lapses INTEGER NOT NULL DEFAULT 0,
    due_at TEXT NOT NULL,
    last_grade INTEGER NOT NULL DEFAULT -1
);
CREATE INDEX IF NOT EXISTS idx_cards_deck ON cards(deck_id);
CREATE INDEX IF NOT EXISTS idx_cards_due ON cards(due_at);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys=ON")
    return conn


# ---------------------------------------------------------------- SM-2
def apply_grade(card_id: int, quality: int) -> None:
    """Store the graded review and the new due timestamp."""
    with db() as conn:
        card = conn.execute("SELECT * FROM cards WHERE id=?", (card_id,)).fetchone()
        if not card:
            return
        if quality == 0:
            ease, minutes = max(1.3, card["ease"] - 0.2), 10
            lapses, reps, first_success = card["lapses"] + 1, 0, False
        elif card["reps"] == 0 or card["interval_min"] <= 0:
            ease = max(1.3, card["ease"] - (0.15 if quality == 3 else 0) + (0.12 if quality == 5 else 0))
            minutes = 60 if quality == 3 else (8 * 60 if quality == 4 else 4 * 60)
            lapses, reps, first_success = card["lapses"], 1, True
        else:
            ease = max(1.3, card["ease"] - (0.15 if quality == 3 else 0) + (0.12 if quality == 5 else 0))
            mult = 1.2 if quality == 3 else (ease if quality == 4 else ease * 1.3)
            minutes = max(60, int(round(card["interval_min"] * mult)))
            lapses, reps, first_success = card["lapses"], card["reps"] + 1, True
        due = (now() + dt.timedelta(minutes=minutes)).isoformat()
        conn.execute(
            """UPDATE cards SET ease=?, interval_min=?, reps=?, lapses=?,
                                 due_at=?, last_grade=? WHERE id=?""",
            (ease, minutes, reps, lapses, due, quality, card_id),
        )


# ---------------------------------------------------------------- helpers
def fa(n) -> str:
    return str(n).translate(str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹"))


def deck_stats(deck_id: int) -> dict:
    with db() as conn:
        total = conn.execute("SELECT COUNT(*) c FROM cards WHERE deck_id=?", (deck_id,)).fetchone()["c"]
        due = conn.execute(
            "SELECT COUNT(*) c FROM cards WHERE deck_id=? AND due_at<=?",
            (deck_id, now().isoformat()),
        ).fetchone()["c"]
        learned = conn.execute(
            "SELECT COUNT(*) c FROM cards WHERE deck_id=? AND interval_min>=?",
            (deck_id, LEARNED_DAYS * 24 * 60),
        ).fetchone()["c"]
        learning = total - learned
    return {"total": total, "due": due, "learned": learned, "learning": learning}


def main_menu() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("📚 کالجک‌های من", callback_data="decks")],
        [InlineKeyboardButton("➕ کالجک جدید", callback_data="newdeck"),
         InlineKeyboardButton("🎓 مطالعه", callback_data="study")],
        [InlineKeyboardButton("📊 پیشرفت", callback_data="progress"),
         InlineKeyboardButton("❓ راهنما", callback_data="help")],
    ])


async def reply_menu(update: Update, text: str) -> None:
    await update.effective_message.reply_text(text, parse_mode="HTML", reply_markup=main_menu())


HELP_TEXT = (
    "🎓 <b>فلش‌کد فارسی — راهنما</b>\n\n"
    "سیستم تکرار فاصله‌دار (SRS) برای مطالعه:\n\n"
    "1️⃣ <code>/newdeck نام</code> — ساخت کالجک (مثلاً «۵۰۴ واژه»)\n"
    "2️⃣ <code>/add</code> — افزودن کارت (جلو / عین / خط جدا)\n"
    "3️⃣ <code>/study</code> — مطالعه‌ی کارت‌های سررسیده؛ بعد از دیدن جواب خودت را نمره بده:\n"
    "🔁 فراموش → ۱۰ دقیقه بعد · 😰 سخت → کوتاه‌تر · 🙂 خوب → طبق الگوریتم · 😎 آسان → طولانی‌تر\n\n"
    "الگوریتم <b>SM-2</b> زمان بعدی مرور را طوری می‌چیند که هر کارت دقیقاً قبل از فراموشی ظاهر شود."
)


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "🎓 <b>فلش‌کد فارسی</b> خوش آمدی!\n\n" + HELP_TEXT,
        parse_mode="HTML",
        reply_markup=main_menu(),
    )


async def cmd_newdeck(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    name = (update.message.text or "").split(maxsplit=1)
    if len(name) < 2:
        await reply_menu(update, "✍️ استفاده: <code>/newdeck نام‌کالجک</code>")
        return
    title = name[1].strip()[:60]
    with db() as conn:
        try:
            conn.execute(
                "INSERT INTO decks (user_id, name, created) VALUES (?,?,?)",
                (update.effective_user.id, title, now().isoformat()),
            )
        except sqlite3.IntegrityError:
            await reply_menu(update, f"⚠️ کالجک «{title}» از قبل وجود دارد.")
            return
    await reply_menu(update, f"✅ کالجک «{title}» ساخته شد! حالا با <code>/add</code> کارت اضافه کن.")


async def cmd_add(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    parts = (update.message.text or "").split(maxsplit=1)
    if len(parts) < 2 or "|" not in parts[1]:
        await reply_menu(update, "✍️ استفاده: <code>/add جلو | عین</code>\nمثال: <code>/add ephemeral | گذرا، زودگذر</code>")
        return
    front, _, back = parts[1].partition("|")
    front, back = front.strip(), back.strip()
    if not front or not back:
        await reply_menu(update, "⚠️ هر دو طرف کارت لازم است.")
        return
    deck = _pick_deck(update.effective_user.id, None)
    if not deck:
        await reply_menu(update, "⚠️ اول یک کالجک بساز: <code>/newdeck نام</code>")
        return
    with db() as conn:
        conn.execute(
            "INSERT INTO cards (deck_id, front, back, due_at) VALUES (?,?,?,?)",
            (deck["id"], front, back, now().isoformat()),
        )
    await reply_menu(update, f"✅ کارت به «{deck['name']}» اضافه شد (جلو: {front[:40]}…)"[:120] + "…")


def _pick_deck(user_id: int, name: str | None) -> sqlite3.Row | None:
    with db() as conn:
        if name:
            return conn.execute(
                "SELECT * FROM decks WHERE user_id=? AND name LIKE ? ORDER BY id DESC",
                (user_id, f"%{name}%"),
            ).fetchone()
        return conn.execute(
            "SELECT * FROM decks WHERE user_id=? ORDER BY id DESC", (user_id,)
        ).fetchone()


async def cmd_decks(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await send_decks(update)


async def cmd_study(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    arg = " ".join(ctx.args) if ctx.args else None
    with db() as conn:
        decks = conn.execute(
            "SELECT * FROM decks WHERE user_id=? ORDER BY id", (user_id,)
        ).fetchall()
    if not decks:
        await reply_menu(update, "⚠️ هنوز کالجکی نداری — <code>/newdeck نام</code>")
        return
    deck = None
    if arg:
        deck = _pick_deck(user_id, arg)
    else:
        due = [d for d in decks if deck_stats(d["id"])["due"] > 0]
        deck = due[0] if due else decks[0]
    if not deck:
        await reply_menu(update, "⚠️ کالجک پیدا نشد.")
        return
    await start_card(update, ctx, deck["id"])


async def start_card(update: Update, ctx: ContextTypes.DEFAULT_TYPE, deck_id: int) -> None:
    with db() as conn:
        card = conn.execute(
            "SELECT * FROM cards WHERE deck_id=? AND due_at<=? ORDER BY due_at LIMIT 1",
            (deck_id, now().isoformat()),
        ).fetchone()
    if not card:
        s = deck_stats(deck_id)
        await reply_menu(
            update,
            f"🎉 هیچ کارت سررسیده‌ای نیست!\nمجموع: {fa(s['total'])} · یادگرفته: {fa(s['learned'])} · در حال یادگیری: {fa(s['learning'])}",
        )
        return
    ctx.user_data["card_id"] = card["id"]
    ctx.user_data["deck_id"] = deck_id
    kb = InlineKeyboardMarkup([[InlineKeyboardButton("👁 دیدن جواب", callback_data="show")]])
    await update.effective_message.reply_text(
        f"📇 <b>جلو:</b>\n{card['front']}", parse_mode="HTML", reply_markup=kb
    )


async def send_decks(update: Update) -> None:
    user_id = update.effective_user.id
    with db() as conn:
        decks = conn.execute(
            "SELECT * FROM decks WHERE user_id=? ORDER BY id", (user_id,)
        ).fetchall()
    if not decks:
        await reply_menu(update, "📚 هنوز کالجکی نداری — <code>/newdeck نام</code>")
        return
    lines = ["<b>📚 کالجک‌های تو:</b>\n"]
    for d in decks:
        s = deck_stats(d["id"])
        lines.append(f"• <b>{d['name']}</b> — {fa(s['total'])} کارت · سررسیده: {fa(s['due'])} · یادگرفته: {fa(s['learned'])}")
    lines.append("\nمطالعه: <code>/study</code> · افزودن کارت: <code>/add جلو | عین</code>")
    await reply_menu(update, "\n".join(lines))


async def cmd_progress(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await send_decks(update)


async def on_menu(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    data = query.data
    user_id = update.effective_user.id

    if data == "help":
        await query.answer()
        await query.message.reply_text(HELP_TEXT, parse_mode="HTML", reply_markup=main_menu())
        return
    if data == "decks":
        await query.answer()
        await send_decks(update)
        return
    if data == "progress":
        await query.answer()
        await send_decks(update)
        return
    if data == "newdeck":
        await query.answer("دستور /newdeck نام را بفرست", show_alert=False)
        return
    if data == "study":
        await query.answer()
        await cmd_study(update, ctx)
        return
    if data == "show":
        await query.answer()
        card_id = ctx.user_data.get("card_id")
        if not card_id:
            await query.message.reply_text("کارت فعالی نیست — /study را بزن.")
            return
        with db() as conn:
            card = conn.execute("SELECT * FROM cards WHERE id=?", (card_id,)).fetchone()
        kb = InlineKeyboardMarkup(
            [[InlineKeyboardButton(label, callback_data=f"grade:{quality}")] for label, quality, _ in GRADES]
        )
        await query.message.reply_text(
            f"🧠 <b>جواب:</b>\n{card['back']}\n\nچقدر خوب یاد داشتی؟",
            parse_mode="HTML",
            reply_markup=kb,
        )
        return
    if data.startswith("grade:"):
        quality = int(data.split(":")[1])
        card_id = ctx.user_data.get("card_id")
        deck_id = ctx.user_data.get("deck_id")
        await query.answer()
        if not card_id:
            await query.message.reply_text("کارت فعالی نیست.")
            return
        apply_grade(card_id, quality)
        label = next(l for l, q, _ in GRADES if q == quality)
        await query.message.reply_text(f"ثبت شد: {label} ✓")
        ctx.user_data.pop("card_id", None)
        await start_card(update, ctx, deck_id)
        return


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("newdeck", cmd_newdeck))
    app.add_handler(CommandHandler("add", cmd_add))
    app.add_handler(CommandHandler("decks", cmd_decks))
    app.add_handler(CommandHandler("progress", cmd_progress))
    app.add_handler(CommandHandler("study", cmd_study))
    app.add_handler(CallbackQueryHandler(on_menu))
    print("Flashcard SRS bot up")
    app.run_polling()


if __name__ == "__main__":
    main()
