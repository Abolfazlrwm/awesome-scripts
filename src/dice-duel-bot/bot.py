#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Dice Duel bot — PvP wagered dice game for Telegram.

Challenge another player to a dice duel with a coin wager: the bot
posts an accept button, animates the rolls (editing the message a few
times), throws 2d6 per player and pays the pot to the winner. Ties are
re-rolled. Includes coin balance, daily bonus, streaks and a leaderboard.

Stdlib (sqlite3) + python-telegram-bot.
"""

from __future__ import annotations

import asyncio
import datetime as dt
import random
import sqlite3
from pathlib import Path

from telegram import InlineKeyboardButton, InlineKeyboardMarkup, Update
from telegram.ext import (
    Application,
    CallbackQueryHandler,
    CommandHandler,
    ContextTypes,
)

BOT_TOKEN = ""  # توکن ربات از @BotFather
DB_PATH = Path(__file__).with_name("dice_duel.db")

TEHRAN = dt.timezone(dt.timedelta(hours=3, minutes=30))
DICE_FACES = ["⚀", "⚁", "⚂", "⚃", "⚄", "⚅"]
START_COINS = 500
DAILY_BONUS = 250
ANIMATION_TICKS = 3


def fa(n) -> str:
    return str(n).translate(str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹"))


def today() -> str:
    return dt.datetime.now(TEHRAN).date().isoformat()


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS players (
    user_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL DEFAULT '',
    coins INTEGER NOT NULL DEFAULT 500,
    wins INTEGER NOT NULL DEFAULT 0,
    losses INTEGER NOT NULL DEFAULT 0,
    streak INTEGER NOT NULL DEFAULT 0,
    best_streak INTEGER NOT NULL DEFAULT 0,
    last_daily TEXT NOT NULL DEFAULT ''
);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def player(user_id: int, name: str = "") -> sqlite3.Row:
    with db() as conn:
        row = conn.execute("SELECT * FROM players WHERE user_id=?", (user_id,)).fetchone()
        if row is None:
            conn.execute(
                "INSERT INTO players (user_id, name) VALUES (?,?)", (user_id, name)
            )
            row = conn.execute("SELECT * FROM players WHERE user_id=?", (user_id,)).fetchone()
        elif name and row["name"] != name:
            conn.execute("UPDATE players SET name=? WHERE user_id=?", (name, user_id))
    return row


def add_coins(user_id: int, delta: int) -> None:
    with db() as conn:
        conn.execute("UPDATE players SET coins = MAX(0, coins + ?) WHERE user_id=?", (delta, user_id))


def record_result(winner_id: int, loser_id: int) -> None:
    with db() as conn:
        conn.execute(
            """UPDATE players SET wins=wins+1, streak=streak+1,
                 best_streak=MAX(best_streak, streak+1) WHERE user_id=?""",
            (winner_id,),
        )
        conn.execute("UPDATE players SET losses=losses+1, streak=0 WHERE user_id=?", (loser_id,))


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    player(update.effective_user.id, update.effective_user.first_name)
    await update.message.reply_text(
        "🎲 <b>نبرد تاس</b> — بازی روانی با سکه!\n\n"
        f"/startmoney: هر بازیکن {fa(START_COINS)} سکه شروع می‌کند.\n\n"
        "⚔️ <code>/duel مبلغ</code> — چالش در همین چت (پاسخ دادن به پیام طرف = چالش به او؛ وگرنه هرکس قبول کند)\n"
        f"🎁 <code>/daily</code> — {fa(DAILY_BONUS)} سکه‌ی روزانه\n"
        "💰 <code>/balance</code> — موجودی و برد/باخت\n"
        "🏆 <code>/top</code> — ثروتمندترین‌ها\n\n"
        "هر طرف ۲ تاس می‌اندازد؛ جمع بالاتر برنده‌ی کل شرط است. مساوی = انداختن دوباره!",
        parse_mode="HTML",
    )


async def cmd_daily(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    uid = update.effective_user.id
    row = player(uid, update.effective_user.first_name)
    if row["last_daily"] == today():
        await update.message.reply_text("🎁 سکه‌ی امروز را گرفته‌ای — فردا برگرد!")
        return
    with db() as conn:
        conn.execute("UPDATE players SET coins=coins+?, last_daily=? WHERE user_id=?",
                     (DAILY_BONUS, today(), uid))
    await update.message.reply_text(f"🎁 {fa(DAILY_BONUS)} سکه‌ی روزانه گرفته شد! موجودی: {fa(row['coins'] + DAILY_BONUS)}")


async def cmd_balance(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    r = player(update.effective_user.id, update.effective_user.first_name)
    games = r["wins"] + r["losses"]
    rate = f"{fa(round(100 * r['wins'] / games))}٪" if games else "—"
    await update.message.reply_text(
        f"💰 <b>{update.effective_user.first_name}</b>\n"
        f"🪙 سکه: {fa(r['coins'])}\n"
        f"⚔️ برد {fa(r['wins'])} · باخت {fa(r['losses'])} ({rate})\n"
        f"🔥 برد متوالی: {fa(r['streak'])} | رکورد: {fa(r['best_streak'])}",
        parse_mode="HTML",
    )


async def cmd_top(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    with db() as conn:
        rows = conn.execute(
            "SELECT name, coins, wins FROM players ORDER BY coins DESC LIMIT 10"
        ).fetchall()
    lines = ["🏆 <b>ثروتمندترین‌ها</b>\n"]
    for i, r in enumerate(rows, 1):
        medal = "🥇🥈🥉"[i - 1] if i <= 3 else f"{fa(i)}."
        lines.append(f"{medal} {r['name'] or 'بی‌نام'} — {fa(r['coins'])} سکه ({fa(r['wins'])} برد)")
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_duel(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    challenger = update.effective_user
    args = list(ctx.args)
    if not args or not args[0].isdigit() or int(args[0]) <= 0:
        await update.message.reply_text("✍️ استفاده: <code>/duel مبلغ</code> — مثلاً <code>/duel 100</code>", parse_mode="HTML")
        return
    wager = int(args[0])

    c_row = player(challenger.id, challenger.first_name)
    if c_row["coins"] < wager:
        await update.message.reply_text(f"🪙 سکه کافی نداری! موجودی: {fa(c_row['coins'])}")
        return

    # reply-to a message => challenge that specific player
    target = None
    if update.message.reply_to_message and not update.message.reply_to_message.from_user.is_bot:
        target = update.message.reply_to_message.from_user
        if target.id == challenger.id:
            await update.message.reply_text("🪞 خودت را به نبرد نمی‌توانی بکشی!")
            return
        t_row = player(target.id, target.first_name)
        if t_row["coins"] < wager:
            await update.message.reply_text(f"🪙 طرف مقابل سکه کافی ندارد ({fa(t_row['coins'])}).")
            return

    kb = InlineKeyboardMarkup([[
        InlineKeyboardButton("⚔️ قبول نبرد!", callback_data=f"accept:{challenger.id}:{wager}")
    ]])
    target_line = f"به {target.first_name}: " if target else "هرکس زودتر قبول کند: "
    await update.message.reply_text(
        f"⚔️ <b>نبرد تاس</b> — {target_line}{challenger.first_name} شرط گذاشته: <b>{fa(wager)}</b> سکه",
        parse_mode="HTML",
        reply_markup=kb,
    )


def roll_2d6() -> tuple[list[int], int]:
    dice = [random.randint(1, 6), random.randint(1, 6)]
    return dice, sum(dice)


def render_roll(name: str, dice: list[int]) -> str:
    return f"{name}: {''.join(DICE_FACES[d - 1] for d in dice)} = {fa(sum(dice))}"


async def on_accept(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    _, challenger_id_s, wager_s = query.data.split(":")
    challenger_id, wager = int(challenger_id_s), int(wager_s)
    opponent = update.effective_user

    if opponent.id == challenger_id:
        await query.answer("نم‌توانی با خودت بجنگی!", show_alert=True)
        return

    c_row = player(challenger_id)
    o_row = player(opponent.id, opponent.first_name)
    if c_row["coins"] < wager or o_row["coins"] < wager:
        await query.answer("یکی از طرفین سکه کافی ندارد!", show_alert=True)
        return

    await query.answer()
    msg = query.message
    challenger_name = c_row["name"] or "حریف"

    # reserve the wager immediately from both sides
    add_coins(challenger_id, -wager)
    add_coins(opponent.id, -wager)

    # roll animation: show random faces a few ticks, then the real roll
    for tick in range(ANIMATION_TICKS):
        d1, _ = roll_2d6()
        d2, _ = roll_2d6()
        await msg.edit_text(
            f"🎲 در حال انداختن… ({fa(tick + 1)}/{fa(ANIMATION_TICKS)})\n\n"
            f"{render_roll(challenger_name, d1)}\n{render_roll(opponent.first_name, d2)}"
        )
        await asyncio.sleep(0.9)

    while True:
        d1, s1 = roll_2d6()
        d2, s2 = roll_2d6()
        board = (
            f"⚔️ <b>نبرد تاس</b> — شرط {fa(wager)} سکه\n\n"
            f"{render_roll(challenger_name, d1)}\n{render_roll(opponent.first_name, d2)}\n\n"
        )
        if s1 == s2:
            board += "⚖️ مساوی! انداختن دوباره…"
            await msg.edit_text(board, parse_mode="HTML")
            await asyncio.sleep(1.0)
            continue
        if s1 > s2:
            winner_id, winner_name, loser_id = challenger_id, challenger_name, opponent.id
        else:
            winner_id, winner_name, loser_id = opponent.id, opponent.first_name, challenger_id
        record_result(winner_id, loser_id)
        add_coins(winner_id, wager * 2)
        w_row = player(winner_id)
        board += f"🏆 <b>{winner_name}</b> برد! (+{fa(wager)} سکه → موجودی {fa(w_row['coins'])})"
        await msg.edit_text(board, parse_mode="HTML")
        return


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("duel", cmd_duel))
    app.add_handler(CommandHandler("daily", cmd_daily))
    app.add_handler(CommandHandler("balance", cmd_balance))
    app.add_handler(CommandHandler("top", cmd_top))
    app.add_handler(CallbackQueryHandler(on_accept, pattern=r"^accept:"))
    print("Dice Duel bot up")
    app.run_polling()


if __name__ == "__main__":
    main()
