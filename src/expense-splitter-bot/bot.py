#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Expense Splitter bot — group cost sharing for Telegram groups.

Track shared expenses inside a group trip/room/office: members join a
session, any member adds an expense (paid by them, split equally or
across picked members), and the bot keeps running balances. The
/settle command solves who-pays-whom with a minimum-cash-flow greedy
solver so the group needs the fewest possible transfers to square up.

Stdlib (sqlite3) + python-telegram-bot.
"""

from __future__ import annotations

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
DB_PATH = Path(__file__).with_name("expenses.db")

TEHRAN = dt.timezone(dt.timedelta(hours=3, minutes=30))
FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")


def fa(n) -> str:
    return str(n).translate(FA_DIGITS)


def money(amount: float) -> str:
    """Format amounts with thousand separators + Persian digits."""
    return f"{int(round(amount)):,}".replace(",", "٬").translate(FA_DIGITS)


def now_iso() -> str:
    return dt.datetime.now(TEHRAN).isoformat()


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chat_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    active INTEGER NOT NULL DEFAULT 1,
    created TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS members (
    session_id INTEGER NOT NULL REFERENCES sessions(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    PRIMARY KEY (session_id, user_id)
);
CREATE TABLE IF NOT EXISTS expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL REFERENCES sessions(id) ON DELETE CASCADE,
    payer_id INTEGER NOT NULL,
    amount INTEGER NOT NULL,          -- tomans, integer amounts
    note TEXT NOT NULL DEFAULT '',
    created TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS shares (
    expense_id INTEGER NOT NULL REFERENCES expenses(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL,
    amount INTEGER NOT NULL,
    PRIMARY KEY (expense_id, user_id)
);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys=ON")
    return conn


def active_session(chat_id: int) -> sqlite3.Row | None:
    with db() as conn:
        return conn.execute(
            "SELECT * FROM sessions WHERE chat_id=? AND active=1 ORDER BY id DESC LIMIT 1",
            (chat_id,),
        ).fetchone()


def members_of(session_id: int) -> list[sqlite3.Row]:
    with db() as conn:
        return conn.execute(
            "SELECT * FROM members WHERE session_id=? ORDER BY user_id", (session_id,)
        ).fetchall()


def member_name(session_id: int, user_id: int) -> str:
    with db() as conn:
        row = conn.execute(
            "SELECT name FROM members WHERE session_id=? AND user_id=?",
            (session_id, user_id),
        ).fetchone()
    return row["name"] if row else f"«{user_id}»"


# ---------------------------------------------------------------- balances
def compute_balances(session_id: int) -> dict[int, int]:
    """net[payer] += paid; net[sharer] -= owed  →  positive = should receive."""
    balances: dict[int, int] = {}
    with db() as conn:
        exps = conn.execute("SELECT * FROM expenses WHERE session_id=?", (session_id,)).fetchall()
        for e in exps:
            balances[e["payer_id"]] = balances.get(e["payer_id"], 0) + e["amount"]
            shs = conn.execute(
                "SELECT * FROM shares WHERE expense_id=?", (e["id"],)
            ).fetchall()
            for s in shs:
                balances[s["user_id"]] = balances.get(s["user_id"], 0) - s["amount"]
    return balances


def settle_plan(balances: dict[int, int]) -> list[tuple[int, int, int]]:
    """Minimum-cash-flow greedy solver: repeatedly match the biggest debtor
    with the biggest creditor until everyone is square. Returns
    (from_user, to_user, amount) tuples."""
    debtors = [[u, -v] for u, v in balances.items() if v < 0]
    creditors = [[u, v] for u, v in balances.items() if v > 0]
    debtors.sort(key=lambda x: -x[1])
    creditors.sort(key=lambda x: -x[1])
    plan: list[tuple[int, int, int]] = []
    i = j = 0
    while i < len(debtors) and j < len(creditors):
        d, c = debtors[i], creditors[j]
        amount = min(d[1], c[1])
        if amount > 0:
            plan.append((d[0], c[0], amount))
        d[1] -= amount
        c[1] -= amount
        if d[1] == 0:
            i += 1
        if c[1] == 0:
            j += 1
    return plan


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "💰 <b>خرج‌شمار گروهی</b>\n\n"
        "هزینه‌های سفر/خوابگاه/دفتر را تقسیم کن:\n\n"
        "1️⃣ <code>/newtrip عنوان</code> — ساخت دوره‌ی خرج (در گروه)\n"
        "2️⃣ <code>/join</code> — عضویت در دوره\n"
        "3️⃣ <code>/expense مبلغ یادداشت</code> — ثبت هزینه (پرداخت‌کننده = خودت، تقسیم بین همه)\n"
        "   یا <code>/expense مبلغ یادداشت @user1 @user2</code> — تقسیم فقط بین منتخب‌ها\n"
        "4️⃣ <code>/balances</code> — مانده‌ی هر نفر\n"
        "5️⃣ <code>/settle</code> — کمترین تعداد حواله برای تسویه‌ی کامل\n"
        "6️⃣ <code>/history</code> · <code>/closetrip</code> — بستن دوره\n\n"
        "⚠️ مبلغ‌ها صحیح و به تومان؛ همه دستورها در گروه اجرا می‌شوند.",
        parse_mode="HTML",
    )


async def cmd_newtrip(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    if update.effective_chat.type == "private":
        await update.message.reply_text("این دستور را داخل گروه بزن.")
        return
    title = " ".join(ctx.args) if ctx.args else "دوره‌ی خرج"
    if active_session(update.effective_chat.id):
        await update.message.reply_text("⚠️ یک دوره‌ی باز از قبل وجود دارد — اول <code>/closetrip</code> را بزن.", parse_mode="HTML")
        return
    with db() as conn:
        cur = conn.execute(
            "INSERT INTO sessions (chat_id, title, created) VALUES (?,?,?)",
            (update.effective_chat.id, title[:60], now_iso()),
        )
        sid = cur.lastrowid
        conn.execute(
            "INSERT INTO members (session_id, user_id, name) VALUES (?,?,?)",
            (sid, update.effective_user.id, update.effective_user.first_name),
        )
    await update.message.reply_text(
        f"✅ دوره‌ی «{title}» ساخته شد — سازنده عضو شد.\nبقیه با <code>/join</code> اضافه شوند.",
        parse_mode="HTML",
    )


async def cmd_join(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم — <code>/newtrip عنوان</code>", parse_mode="HTML")
        return
    with db() as conn:
        try:
            conn.execute(
                "INSERT INTO members (session_id, user_id, name) VALUES (?,?,?)",
                (s["id"], update.effective_user.id, update.effective_user.first_name),
            )
        except sqlite3.IntegrityError:
            await update.message.reply_text("🙋 از قبل عضو هستی!")
            return
    await update.message.reply_text(f"🙋 {update.effective_user.first_name} عضو شد.")


async def cmd_expense(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم — <code>/newtrip عنوان</code>", parse_mode="HTML")
        return
    if update.effective_user.id not in {m["user_id"] for m in members_of(s["id"])}:
        await update.message.reply_text("⚠️ اول <code>/join</code> را بزن.", parse_mode="HTML")
        return

    args = list(ctx.args)
    if not args or not args[0].isdigit():
        await update.message.reply_text("✍️ استفاده: <code>/expense مبلغ یادداشت [@user1 @user2]</code>", parse_mode="HTML")
        return
    amount = int(args[0])
    rest = " ".join(args[1:])
    note, _, mentions = rest.partition("@")

    member_list = members_of(s["id"])
    by_uid = {m["user_id"]: m["name"] for m in member_list}

    targets: list[int] | None = None
    mention_list = "@" + mentions if mentions else ""
    if mention_list.strip():
        wanted = {t.strip().lstrip("@") for t in mention_list.split() if t.strip()}
        targets = [uid for uid, nm in by_uid.items() if nm in wanted]
        if not targets:
            await update.message.reply_text(
                "⚠️ هیچ عضوی با آن اسم‌ها پیدا نشد. نام‌ها را مثل نام تلگرامی اعضا بده.\nاعضا: " +
                "، ".join(by_uid.values())
            )
            return

    receivers = targets if targets is not None else list(by_uid.keys())
    if update.effective_user.id not in receivers:
        receivers.append(update.effective_user.id)  # payer always shares

    per_user = _split_evenly(amount, len(receivers))
    with db() as conn:
        cur = conn.execute(
            "INSERT INTO expenses (session_id, payer_id, amount, note, created) VALUES (?,?,?,?,?)",
            (s["id"], update.effective_user.id, amount, note.strip()[:80], now_iso()),
        )
        eid = cur.lastrowid
        for uid, share in zip(receivers, per_user):
            conn.execute(
                "INSERT INTO shares (expense_id, user_id, amount) VALUES (?,?,?)",
                (eid, uid, share),
            )
    who = "همه" if targets is None else "، ".join(by_uid[u] for u in receivers)
    remainder = amount - sum(per_user)
    await update.message.reply_text(
        f"🧾 ثبت شد: <b>{money(amount)}</b> تومان — {note.strip() or 'بدون توضیح'}\n"
        f"💳 پرداخت: {member_name(s['id'], update.effective_user.id)}\n"
        f"👥 تقسیم بین ({len(receivers)} نفر): {who}\n"
        f"سهم هر نفر: <b>{money(per_user[0])}</b>"
        + (f" (باقیمانده‌ی گردشدن {money(remainder)} به پرداخت‌کننده)" if remainder else ""),
        parse_mode="HTML",
    )


def _split_evenly(amount: int, n: int) -> list[int]:
    """Split `amount` across n people with remainder kept for the payer."""
    base, rem = divmod(amount, n)
    return [base + rem] + [base] * (n - 1)


async def cmd_balances(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم.")
        return
    bal = compute_balances(s["id"])
    if not bal:
        await update.message.reply_text("هنوز خرجی ثبت نشده.")
        return
    lines = [f"📊 <b>مانده‌ها — {s['title']}</b>\n"]
    for uid, v in sorted(bal.items(), key=lambda x: -x[1]):
        sign = "دریافت دارد" if v > 0 else ("بدهکار است" if v < 0 else "تسویه است")
        lines.append(f"• {member_name(s['id'], uid)}: {money(abs(v))} تومان ({sign})")
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_settle(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم.")
        return
    plan = settle_plan(compute_balances(s["id"]))
    if not plan:
        await update.message.reply_text("✅ همه چیز تسویه است — حواله‌ای لازم نیست!")
        return
    lines = [f"🤝 <b>پلن تسویه — {s['title']}</b> (کمترین حواله‌ها: {fa(len(plan))})\n"]
    for src, dst, amount in plan:
        lines.append(f"➡️ {member_name(s['id'], src)} به {member_name(s['id'], dst)}: {money(amount)} تومان")
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_history(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم.")
        return
    with db() as conn:
        exps = conn.execute(
            """SELECT e.*, (SELECT COUNT(*) FROM shares s WHERE s.expense_id=e.id) AS n_share
               FROM expenses e WHERE e.session_id=? ORDER BY e.id DESC LIMIT 15""",
            (s["id"],),
        ).fetchall()
    if not exps:
        await update.message.reply_text("🧾 هنوز خرجی ثبت نشده.")
        return
    lines = [f"🧾 <b>آخرین خرج‌ها — {s['title']}</b>\n"]
    for e in exps:
        lines.append(
            f"• {money(e['amount'])} — {e['note'] or 'بدون توضیح'} "
            f"(پرداخت: {member_name(s['id'], e['payer_id'])}، تقسیم بین {fa(e['n_share'])} نفر)"
        )
    await update.message.reply_text("\n".join(lines), parse_mode="HTML")


async def cmd_closetrip(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    s = active_session(update.effective_chat.id)
    if not s:
        await update.message.reply_text("⚠️ دوره‌ی بازی نداریم.")
        return
    with db() as conn:
        conn.execute("UPDATE sessions SET active=0 WHERE id=?", (s["id"],))
    bal = compute_balances(s["id"])
    unsettled = sum(1 for v in bal.values() if v != 0)
    await update.message.reply_text(
        f"🔒 دوره‌ی «{s['title']}» بسته شد."
        + (f"\n⚠️ {fa(unsettled)} نفر هنوز تسویه‌نشده دارند — /settle را ببین." if unsettled else " حساب‌ها صفر است ✅"),
    )


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("newtrip", cmd_newtrip))
    app.add_handler(CommandHandler("join", cmd_join))
    app.add_handler(CommandHandler("expense", cmd_expense))
    app.add_handler(CommandHandler("balances", cmd_balances))
    app.add_handler(CommandHandler("settle", cmd_settle))
    app.add_handler(CommandHandler("history", cmd_history))
    app.add_handler(CommandHandler("closetrip", cmd_closetrip))
    print("Expense Splitter bot up")
    app.run_polling()


if __name__ == "__main__":
    main()
