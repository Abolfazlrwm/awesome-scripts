#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Wordle FA — Persian word-guessing game bot.

A fully offline Wordle clone for Telegram in Persian: one 5-letter word
per Tehran-day shared by every player, six attempts, 🟩/🟨/⬛ feedback,
per-user stats with streaks and a guess distribution. A /random mode
lets you play unlimited practice rounds that do not affect daily stats.

Stdlib (sqlite3) + python-telegram-bot. No API keys other than the bot token.
"""

from __future__ import annotations

import datetime as dt
import re
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
DB_PATH = Path(__file__).with_name("wordle_fa.db")

TEHRAN = dt.timezone(dt.timedelta(hours=3, minutes=30))
MAX_ATTEMPTS = 6
GRID = {"hit": "🟩", "present": "🟨", "miss": "⬛"}

# Every word is exactly 5 Persian letters — enforced by _validate_words().
WORDS = [
    "مدرسه", "پنجره", "سلامت", "دوستی", "خاطره", "صندلی", "آسمان", "ستاره",
    "فرشته", "زندگی", "شیرین", "خوراک", "دلتنگ", "نانوا", "گنجشک", "پرنده",
    "بازار", "سنگین", "کوهست", "مهمان", "سفارت", "بارون", "شهرها", "ماهان",
    "ابرها", "گلبار", "پیروز", "مهتاب", "یاقوت", "ستایش", "شبانه", "سالها",
    "ترانه", "سازها", "قلمرو", "سخنور", "حرفها", "فکرات", "گلدان", "بادام",
    "انگور", "تربوز", "خرچنگ", "اسباب", "تابلو", "نقاشی", "مجسمه", "فلزات",
    "زنجیر", "دریچه", "راهرو", "آشپزی", "خورشت", "شکلات", "باران", "طوفان",
    "آفتاب", "آبشار", "مرداب", "جزیره", "مرجان", "سیاره", "طبیعت", "حیوان",
    "خزنده",
]

PERSIAN_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")


def fa(n: int) -> str:
    return str(n).translate(PERSIAN_DIGITS)


def normalize(word: str) -> str:
    """Fold Arabic variants to their Persian counterparts and trim."""
    return word.replace("ي", "ی").replace("ك", "ک").strip()


def _validate_words(words: list[str]) -> list[str]:
    seen: set[str] = set()
    out: list[str] = []
    for w in words:
        w = normalize(w)
        if len(w) == 5 and w not in seen:
            seen.add(w)
            out.append(w)
    return out


WORDS = _validate_words(WORDS)


def today_tehran() -> dt.date:
    return dt.datetime.now(TEHRAN).date()


def word_of_the_day() -> str:
    seed = today_tehran().toordinal()
    return WORDS[seed % len(WORDS)]


def grade(guess: str, target: str) -> list[str]:
    """Classic Wordle scoring with duplicate-letter handling."""
    marks = [GRID["miss"]] * 5
    remaining: dict[str, int] = {}
    for idx, t in enumerate(target):
        if guess[idx] == t:
            marks[idx] = GRID["hit"]
        else:
            remaining[t] = remaining.get(t, 0) + 1
    for idx, g in enumerate(guess):
        if marks[idx] == GRID["hit"] or g not in remaining:
            continue
        if remaining[g] > 0:
            marks[idx] = GRID["present"]
            remaining[g] -= 1
    return marks


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS stats (
    user_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL DEFAULT '',
    played INTEGER NOT NULL DEFAULT 0,
    wins INTEGER NOT NULL DEFAULT 0,
    cur_streak INTEGER NOT NULL DEFAULT 0,
    best_streak INTEGER NOT NULL DEFAULT 0,
    last_win TEXT NOT NULL DEFAULT '',
    last_played TEXT NOT NULL DEFAULT ''
);
CREATE TABLE IF NOT EXISTS dist (
    user_id INTEGER NOT NULL,
    row INTEGER NOT NULL,          -- 1..6 (attempt index)
    n INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (user_id, row)
);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def record_result(user_id: int, name: str, won: bool, attempts: int) -> None:
    today = today_tehran().isoformat()
    with db() as conn:
        row = conn.execute("SELECT * FROM stats WHERE user_id=?", (user_id,)).fetchone()
        cur = row["cur_streak"] if row else 0
        best = row["best_streak"] if row else 0
        streak = cur + 1 if won else 0
        conn.execute(
            """INSERT INTO stats (user_id, name, played, wins, cur_streak, best_streak,
                                 last_win, last_played)
               VALUES (?,?,?,?,?,?,?,?)
               ON CONFLICT(user_id) DO UPDATE SET
                 name=excluded.name,
                 played=stats.played+1,
                 wins=stats.wins+excluded.wins,
                 cur_streak=excluded.cur_streak,
                 best_streak=MAX(stats.best_streak, excluded.best_streak),
                 last_win=excluded.last_win,
                 last_played=excluded.last_played""",
            (
                user_id, name, 1, 1 if won else 0, streak, max(best, streak),
                today if won else (row["last_win"] if row else ""), today,
            ),
        )
        if won:
            conn.execute(
                """INSERT INTO dist (user_id, row, n) VALUES (?,?,1)
                   ON CONFLICT(user_id, row) DO UPDATE SET n = n + 1""",
                (user_id, attempts),
            )


# ---------------------------------------------------------------- rendering
def render_board(guesses: list[tuple[str, list[str]]]) -> str:
    lines = []
    for word, marks in guesses:
        spaced = "\u200c".join(word)
        lines.append(f"{spaced}\n{''.join(marks)}")
    return "\n\n".join(lines)


def hint_line(left: int) -> str:
    return f"🎯 تلاش باقی‌مانده: {fa(left)} از {fa(MAX_ATTEMPTS)}"


def main_menu_keyboard() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("📅 کلمه امروز", callback_data="daily"),
         InlineKeyboardButton("🎲 تمرین تصادفی", callback_data="random")],
        [InlineKeyboardButton("📊 آمار من", callback_data="stats"),
         InlineKeyboardButton("❓ راهنما", callback_data="help")],
    ])


HELP_TEXT = (
    "🎮 <b>وردل فارسی — راهنما</b>\n\n"
    "یک کلمه‌ی ۵ حرفی را در ۶ تلاش حدس بزنید:\n"
    "🟩 حرف سر جایش درست است\n"
    "🟨 حرف در کلمه هست ولی جای دیگری\n"
    "⬛ حرف در کلمه نیست\n\n"
    "📅 <b>کلمه امروز:</b> روزانه یک کلمه برای همه یکسان است و هر کاربر فقط یک بار در روز می‌تواند بازی کند؛ برد و باخت در آمار ثبت می‌شود.\n"
    "🎲 <b>تمرین تصادفی:</b> بی‌نهایت بازی تمرینی با کلمه‌های تصادفی، بدون اثر روی آمار.\n\n"
    "برای شروع دکمه‌های زیر را بزنید."
)


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "🎮 <b>وردل فارسی</b> خوش آمدی!\n\n" + HELP_TEXT,
        parse_mode="HTML",
        reply_markup=main_menu_keyboard(),
    )


async def cmd_play(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await start_daily(update, ctx)


async def cmd_random(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await start_random(update, ctx)


async def cmd_stats(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await send_stats(update)


async def start_daily(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    user_id = update.effective_user.id
    today = today_tehran().isoformat()
    with db() as conn:
        row = conn.execute("SELECT last_played FROM stats WHERE user_id=?", (user_id,)).fetchone()
    if row and row["last_played"] == today:
        await _menu_reply(update, "📅 کلمه امروز را قبلاً بازی کرده‌ای! فردا برگرد — یا با 🎲 تمرین کن.")
        return
    _new_game(ctx, word_of_the_day(), mode="daily")
    await _menu_reply(update, "📅 <b>کلمه امروز</b> شروع شد!\n\n" + hint_line(MAX_ATTEMPTS))


async def start_random(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    import random as _random

    _new_game(ctx, _random.choice(WORDS), mode="random")
    await _menu_reply(update, "🎲 <b>بازی تمرینی</b> شروع شد! (بدون اثر روی آمار)\n\n" + hint_line(MAX_ATTEMPTS))


def _new_game(ctx: ContextTypes.DEFAULT_TYPE, word: str, mode: str) -> None:
    ctx.user_data.clear()
    ctx.user_data["target"] = word
    ctx.user_data["mode"] = mode
    ctx.user_data["guesses"] = []


async def _menu_reply(update: Update, text: str) -> None:
    await update.effective_message.reply_text(text, parse_mode="HTML", reply_markup=main_menu_keyboard())


async def send_stats(update: Update) -> None:
    user = update.effective_user
    with db() as conn:
        row = conn.execute("SELECT * FROM stats WHERE user_id=?", (user.id,)).fetchone()
    if not row or row["played"] == 0:
        await _menu_reply(update, "📊 هنوز آماری ثبت نشده — اول یک بازی ببر یا بباخر!")
        return
    win_pct = round(100 * row["wins"] / row["played"])
    with db() as conn:
        dist = conn.execute(
            "SELECT row, n FROM dist WHERE user_id=? ORDER BY row", (user.id,)
        ).fetchall()
    peak = max((d["n"] for d in dist), default=1) or 1
    bars = ""
    for d in dist:
        filled = "█" * max(1, round(6 * d["n"] / peak)) if d["n"] else ""
        bars += f"{fa(d['row'])}/6 | {filled} {fa(d['n'])}\n"
    await _menu_reply(
        update,
        f"📊 <b>آمار {user.first_name}</b>\n\n"
        f"🎮 بازی‌ها: {fa(row['played'])}\n"
        f"✅ برد: {fa(row['wins'])} ({fa(win_pct)}٪)\n"
        f"🔥 برد متوالی: {fa(row['cur_streak'])} | رکورد: {fa(row['best_streak'])}\n\n"
        f"<b>پخش تعداد تلاش‌ها:</b>\n<pre>{bars}</pre>",
    )


async def on_menu(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    await query.answer()
    data = query.data
    if data == "daily":
        await start_daily(update, ctx)
    elif data == "random":
        await start_random(update, ctx)
    elif data == "stats":
        await send_stats(update)
    elif data == "help":
        await query.message.reply_text(HELP_TEXT, parse_mode="HTML", reply_markup=main_menu_keyboard())


async def on_guess(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    if "target" not in ctx.user_data:
        await update.message.reply_text(
            "بازی فعالی نداری — اول از منو شروع کن:", reply_markup=main_menu_keyboard()
        )
        return
    guess = normalize(update.message.text)
    if len(guess) != 5 or not re.fullmatch(r"[\u0600-\u06FF]+", guess):
        await update.message.reply_text(f"⚠️ دقیقاً یک کلمه‌ی {fa(5)} حرفی فارسی بفرست (فقط حروف).")
        return

    target: str = ctx.user_data["target"]
    guesses: list = ctx.user_data["guesses"]
    if guess in {g for g, _ in guesses}:
        await update.message.reply_text("♻️ این کلمه را قبلاً حدس زده‌ای!")
        return

    marks = grade(guess, target)
    guesses.append((guess, marks))
    board = render_board(guesses)
    left = MAX_ATTEMPTS - len(guesses)

    won = guess == target
    over = won or left == 0

    if won:
        record_result(update.effective_user.id, update.effective_user.first_name, True, len(guesses))
        await update.message.reply_text(
            f"{board}\n\n🎉 <b>عالی بود!</b> کلمه <code>{target}</code> در {fa(len(guesses))} تلاش پیدا شد.",
            parse_mode="HTML",
            reply_markup=main_menu_keyboard(),
        )
        ctx.user_data.clear()
    elif over:
        record_result(update.effective_user.id, update.effective_user.first_name, False, len(guesses))
        await update.message.reply_text(
            f"{board}\n\n😢 تلاش‌ها تمام شد!\nکلمه درست: <code>{target}</code>",
            parse_mode="HTML",
            reply_markup=main_menu_keyboard(),
        )
        ctx.user_data.clear()
    else:
        await update.message.reply_text(f"{board}\n\n{hint_line(left)}", parse_mode="HTML")


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler(["start", "help"], cmd_start))
    app.add_handler(CommandHandler("play", cmd_play))
    app.add_handler(CommandHandler("random", cmd_random))
    app.add_handler(CommandHandler("stats", cmd_stats))
    app.add_handler(CallbackQueryHandler(on_menu))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, on_guess))
    print(f"Wordle FA up — {len(WORDS)} words · word of day: {word_of_the_day()}")
    app.run_polling()


if __name__ == "__main__":
    main()
