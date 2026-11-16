#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Auction House bot — live group auctions for Telegram.

Start a timed auction in any group: members tap the bid button to raise
the current price by the minimum increment, the bot edits the auction
post live, and an anti-snipe rule extends the timer when a bid lands in
the last 30 seconds. A lightweight asyncio watcher closes expired
auctions and announces the winner — no external scheduler dependency.

Stdlib (sqlite3) + python-telegram-bot.
"""

from __future__ import annotations

import asyncio
import datetime as dt
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
DB_PATH = Path(__file__).with_name("auctions.db")

TEHRAN_TZ = dt.timezone(dt.timedelta(hours=3, minutes=30))
DEFAULT_MINUTES = 10          # auction duration
DEFAULT_STEP = 5000           # min bid increment (toman)
ANTI_SNIPE_SECONDS = 30       # extend timer if a bid lands this close to the end
FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")


def fa(n) -> str:
    return str(n).translate(FA_DIGITS)


def money(amount: int) -> str:
    return f"{amount:,}".replace(",", "٬").translate(FA_DIGITS)


def now() -> dt.datetime:
    return dt.datetime.now(TEHRAN_TZ)


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS auctions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chat_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    creator_id INTEGER NOT NULL,
    start_price INTEGER NOT NULL,
    min_step INTEGER NOT NULL,
    current_bid INTEGER NOT NULL,
    current_bidder INTEGER,
    ends_at TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'open',      -- open | closed | cancelled
    message_id INTEGER
);
CREATE TABLE IF NOT EXISTS bids (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    auction_id INTEGER NOT NULL REFERENCES auctions(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    amount INTEGER NOT NULL,
    at TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS stats (
    user_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL DEFAULT '',
    wins INTEGER NOT NULL DEFAULT 0,
    spent INTEGER NOT NULL DEFAULT 0
);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys=ON")
    return conn


def parse_ends(minutes: int) -> str:
    return (now() + dt.timedelta(minutes=minutes)).isoformat()


def minutes_left(ends_at: str) -> float:
    return (dt.datetime.fromisoformat(ends_at) - now()).total_seconds() / 60


# ---------------------------------------------------------------- rendering
def auction_post(row: sqlite3.Row) -> str:
    lines = [f"🔨 <b>مزایده:</b> {row['title']}", ""]
    lines.append(f"💰 قیمت پایه: {money(row['start_price'])} تومان")
    if row["current_bidder"]:
        lead = db().execute(
            "SELECT name FROM bids WHERE auction_id=? ORDER BY amount DESC, id DESC LIMIT 1",
            (row["id"],),
        ).fetchone()
        lead_name = lead["name"] if lead else "ناشناس"
        lines.append(f"🏅 بالاترین پیشنهاد: <b>{money(row['current_bid'])}</b> توسط {lead_name}")
        lines.append(f"➕ گام بعدی: {money(row['current_bid'] + row['min_step'])} تومان")
    else:
        lines.append("🤝 هنوز پیشنهادی ثبت نشده — اولین نفر باش!")
    left = max(0, minutes_left(row["ends_at"]))
    lines.append(f"⏰ زمان باقی‌مانده: <b>{fa(round(left, 1))}</b> دقیقه")
    lines.append("\n<i>هر ضربه روی دکمه = یک گام افزایش قیمت. پیشنهاد در ۳۰ ثانیه‌ی آخر، تایمر ۳۰ ثانیه تمدید می‌شود.</i>")
    return "\n".join(lines)


def bid_keyboard(auction_id: int) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("💰 + یک گام", callback_data=f"bid:{auction_id}"),
         InlineKeyboardButton("📜 سابقه", callback_data=f"hist:{auction_id}")],
    ])


# ---------------------------------------------------------------- watcher
async def auction_watcher(app) -> None:
    """Background loop that closes expired auctions and announces winners."""
    while True:
        await asyncio.sleep(5)
        try:
            with db() as conn:
                rows = conn.execute(
                    "SELECT * FROM auctions WHERE status='open' AND ends_at<=?",
                    (now().isoformat(),),
                ).fetchall()
            for row in rows:
                with db() as conn:
                    conn.execute("UPDATE auctions SET status='closed' WHERE id=?", (row["id"],))
                try:
                    if row["current_bidder"]:
                        winner_row = db().execute(
                            "SELECT name FROM bids WHERE auction_id=? ORDER BY amount DESC, id DESC LIMIT 1",
                            (row["id"],),
                        ).fetchone()
                        winner_name = winner_row["name"] if winner_row else "ناشناس"
                        with db() as conn:
                            conn.execute(
                                """INSERT INTO stats (user_id, name, wins, spent) VALUES (?,?,1,?)
                                   ON CONFLICT(user_id) DO UPDATE SET
                                     name=excluded.name, wins=wins+1, spent=spent+excluded.spent""",
                                (row["current_bidder"], winner_name, row["current_bid"]),
                            )
                        text = (f"🏁 <b>مزایده «{row['title']}» تمام شد!</b>\n\n"
                                f"🏆 برنده: <b>{winner_name}</b>\n"
                                f"💰 قیمت نهایی: <b>{money(row['current_bid'])}</b> تومان\n\n"
                                "تبریک! 🎉")
                    else:
                        text = f"🏁 <b>مزایده «{row['title']}» بدون پیشنهاد بسته شد.</b>"
                    await app.bot.send_message(row["chat_id"], text, parse_mode="HTML")
                except Exception as exc:  # chat may be unreachable
                    print("close announce failed:", exc)
        except Exception as exc:
            print("watcher error:", exc)


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "🔨 <b>خانه‌ی مزایده</b> — مزایده زنده داخل گروه\n\n"
        "1️⃣ <code>/newauction قیمت‌پایه عنوان</code> — شروع مزایده (پیش‌فرض ۱۰ دقیقه، گام ۵٬۰۰۰ تومان)\n"
        "   با گزینه‌ها: <code>/newauction 100000 گوشی قدیمی — 15m — step 2000</code>\n"
        "2️⃣ اعضا با دکمه «+ یک گام» قیمت را بالا می‌برند\n"
        "3️⃣ پایان تایمر → اعلام برنده\n\n"
        "📜 <code>/auctions</code> — مزایده‌های باز این گروه\n"
        "🏆 <code>/top</code> — خریداران برتر\n"
        "❌ <code>/cancel</code> — لغو مزایده‌ی خودت (فقط اگر پیشنهاد ندارد)",
        parse_mode="HTML",
    )


async def cmd_newauction(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    args = list(ctx.args)
    if not args or not args[0].isdigit():
        await update.message.reply_text(
            "✍️ استفاده: <code>/newauction قیمت‌پایه عنوان</code>\n"
            "مثال: <code>/newauction 100000 سرویس چای کریستال</code>",
            parse_mode="HTML",
        )
        return
    start_price = int(args[0])
    title = " ".join(args[1:]) or "کالای بدون عنوان"

    minutes, step = DEFAULT_MINUTES, DEFAULT_STEP
    m = None
    for tok in args:
        if tok.endswith("m") and tok[:-1].isdigit():
            m = tok[:-1]
    if m:
        minutes = max(1, min(120, int(m)))
    for i, tok in enumerate(args):
        if tok == "step" and i + 1 < len(args) and args[i + 1].isdigit():
            step = int(args[i + 1])

    with db() as conn:
        if conn.execute(
            "SELECT 1 FROM auctions WHERE chat_id=? AND status='open'", (update.effective_chat.id,)
        ).fetchone():
            await update.message.reply_text("⚠️ این گروه یک مزایده‌ی باز دارد — صبر کن تمام شود یا /cancel بزن.")
            return
        cur = conn.execute(
            """INSERT INTO auctions (chat_id, title, creator_id, start_price, min_step,
                                     current_bid, ends_at)
               VALUES (?,?,?,?,?,?,?)""",
            (update.effective_chat.id, title[:80], update.effective_user.id,
             start_price, step, start_price, parse_ends(minutes)),
        )
        aid = cur.lastrowid

    msg = await update.message.reply_text(
        auction_post(db().execute("SELECT * FROM auctions WHERE id=?", (aid,)).fetchone()),
        parse_mode="HTML",
        reply_markup=bid_keyboard(aid),
    )
    with db() as conn:
        conn.execute("UPDATE auctions SET message_id=? WHERE id=?", (msg.message_id, aid))


async def on_button(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    action, aid_s = query.data.split(":")
    aid = int(aid_s)
    with db() as conn:
        row = conn.execute("SELECT * FROM auctions WHERE id=?", (aid,)).fetchone()

    if row is None or row["status"] != "open":
        await query.answer("این مزایده بسته شده است.", show_alert=True)
        return

    if action == "bid":
        new_bid = row["current_bid"] + row["min_step"]
        ends = dt.datetime.fromisoformat(row["ends_at"])
        seconds_left = (ends - now()).total_seconds()
        if 0 < seconds_left <= ANTI_SNIPE_SECONDS:
            ends = now() + dt.timedelta(seconds=ANTI_SNIPE_SECONDS)  # anti-snipe extension
        with db() as conn:
            conn.execute(
                """UPDATE auctions SET current_bid=?, current_bidder=?, ends_at=? WHERE id=?""",
                (new_bid, update.effective_user.id, ends.isoformat(), aid),
            )
            conn.execute(
                "INSERT INTO bids (auction_id, user_id, name, amount, at) VALUES (?,?,?,?,?)",
                (aid, update.effective_user.id, update.effective_user.first_name,
                 new_bid, now().isoformat()),
            )
        await query.answer(f"پیشنهاد {money(new_bid)} ثبت شد ✓")
        row = db().execute("SELECT * FROM auctions WHERE id=?", (aid,)).fetchone()
        await query.message.edit_text(auction_post(row), parse_mode="HTML", reply_markup=bid_keyboard(aid))
        return

    if action == "hist":
        with db() as conn:
            bids = conn.execute(
                "SELECT name, amount, at FROM bids WHERE auction_id=? ORDER BY amount DESC LIMIT 10",
                (aid,),
            ).fetchall()
        await query.answer()
        if not bids:
            await query.message.reply_text("📜 هنوز پیشنهادی ثبت نشده.")
            return
        lines = ["📜 <b>سابقه‌ی پیشنهادها:</b>\n"]
        for b in bids:
            t = dt.datetime.fromisoformat(b["at"]).strftime("%H:%M")
            lines.append(f"• {b['name']}: {money(b['amount'])} تومان ({fa(t)})")
        await query.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_auctions(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    with db() as conn:
        rows = conn.execute(
            "SELECT * FROM auctions WHERE chat_id=? AND status='open'", (update.effective_chat.id,)
        ).fetchall()
    if not rows:
        await update.message.reply_text("📭 مزایده‌ی بازی در این گروه نیست — /newauction را بزن.")
        return
    lines = ["🔨 <b>مزایده‌های باز:</b>\n"]
    for r in rows:
        lines.append(
            f"• {r['title']} — فعلی {money(r['current_bid'])} — {fa(round(minutes_left(r['ends_at']), 1))} دقیقه مانده"
        )
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_cancel(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    with db() as conn:
        row = conn.execute(
            "SELECT * FROM auctions WHERE chat_id=? AND status='open' ORDER BY id DESC LIMIT 1",
            (update.effective_chat.id,),
        ).fetchone()
    if not row:
        await update.message.reply_text("مزایده‌ی بازی نیست.")
        return
    if row["creator_id"] != update.effective_user.id:
        await update.message.reply_text("⛔ فقط سازنده‌ی مزایده می‌تواند لغو کند.")
        return
    if row["current_bidder"]:
        await update.message.reply_text("⛔ پیشنهادی ثبت شده — مزایده قابل لغو نیست.")
        return
    with db() as conn:
        conn.execute("UPDATE auctions SET status='cancelled' WHERE id=?", (row["id"],))
    await update.message.reply_text(f"🚫 مزایده «{row['title']}» لغو شد.")


async def cmd_top(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    with db() as conn:
        rows = conn.execute("SELECT name, wins, spent FROM stats ORDER BY spent DESC LIMIT 10").fetchall()
    if not rows:
        await update.message.reply_text("🏆 هنوز مزایده‌ای تمام نشده.")
        return
    lines = ["🏆 <b>خریداران برتر</b>\n"]
    for r in rows:
        lines.append(f"• {r['name']} — {fa(r['wins'])} برد، {money(r['spent'])} تومان")
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def post_init(app) -> None:
    app.create_task(auction_watcher(app))


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).post_init(post_init).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("newauction", cmd_newauction))
    app.add_handler(CommandHandler("auctions", cmd_auctions))
    app.add_handler(CommandHandler("cancel", cmd_cancel))
    app.add_handler(CommandHandler("top", cmd_top))
    app.add_handler(CallbackQueryHandler(on_button, pattern=r"^(bid|hist):"))
    print("Auction House bot up")
    app.run_polling()


if __name__ == "__main__":
    main()
