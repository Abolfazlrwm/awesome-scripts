#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Fortune Teller bot — فال و طالع‌بینی سرگرم‌کننده برای تلگرام.

Everything is 100% offline and deterministic: your fortune for a given
Tehran-day is seeded by (user_id, date), so re-asking on the same day
always returns the same card — just like a real فال daily. Includes a
daily fortune card, a zodiac-of-the-day vibe, pair compatibility
(reply to someone), and a 3-dice reading.

⚠️ Entertainment only — all fortune texts are original, no real
divination is claimed or implied.

Stdlib + python-telegram-bot.
"""

from __future__ import annotations

import datetime as dt
import random
import sqlite3
from pathlib import Path

from telegram import Update
from telegram.ext import Application, CommandHandler, ContextTypes

BOT_TOKEN = ""  # توکن ربات از @BotFather
DB_PATH = Path(__file__).with_name("fortunes.db")
TEHRAN_TZ = dt.timezone(dt.timedelta(hours=3, minutes=30))

FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")
COLORS = ["سبز", "قرمز", "آبی", "زرد", "بنفش", "نارنجی", "فیروزه‌ای", "صورتی", "سفید", "مشکی"]

# ---- original fortune cards (written from scratch — entertainment only) ----
CARDS = [
    ("🌟", "راهی که امروز جلوی پات است، همان راهی است که دیشب در خیالت ساختی — با اعتماد قدم بردار."),
    ("🌱", "یک شروع کوچک امروز، ریشه‌ای می‌شود که سال دیگر به آن افتخار می‌کنی."),
    ("🔑", "دِری که به نظر بسته است، فقط سخت‌باز می‌شود؛ صبرت کلید است."),
    ("🍀", "شانس امروز با نامه‌هایی همراه است که مدت‌ها بی‌جواب مانده‌اند."),
    ("🌊", "مثل آب، سخت‌ترین سنگ را با ملایمت شکست بده؛ امروز وقت ملایمت است."),
    ("🔥", "انرژی‌ای که در قلبت جمع شده، امروز باید به ساختن چیزی برسد نه به شکستن."),
    ("🎈", "یک خبر خوش از راه می‌رسد؛ صورتت را آماده‌ی لبخند نگه دار."),
    ("🧭", "اگر بین دو راه سرگردانی، همان که ترست را کم می‌کند انتخاب کن نه حرف دیگران را."),
    ("💎", "ارزش تو در چشم درست، مثل طلاست؛ منتظر چشم درست بمان."),
    ("🌙", "امشب دلت سبک می‌شود؛ چیزی که سنگینی می‌کرد را زمین بگذار."),
    ("☕", "یک گفت‌وگوی ساده، امروز گرگی را که سال‌ها با آن زندگی کرده‌ای اهلی می‌کند."),
    ("🌞", "آفتابی که پشت ابرهاست محو نشده؛ امروز ابرها کنار می‌روند."),
    ("📜", "نوشته‌ای که مدت‌ها به تعویق انداختی، امروز قاب‌شدنی است."),
    ("🤝", "دستی که امروز به کسی بدهی، فردا در سخت‌ترین پله پیش توست."),
    ("🛠", "چیزی که خراب به نظر می‌رسد، فقط به دست نو تعمیر نیاز دارد — دستِ تو."),
    ("🎁", "پشت یک ماجرا، هدیه‌ای پنهان است که هنوز کاغذش را باز نکرده‌ای."),
    ("⛵", "باد امروز به نفع توست؛ شراع کهنه را باز کن."),
    ("🕯", "نور کوچکی که خودت روشن کرده‌ای، تاریک‌ترین ساعت امروز را نگه می‌دارد."),
    ("🪶", "کلمه‌ای که امروز می‌نویسی، مسیر فردای تو را عوض می‌کند."),
    ("⛰", "پله‌ای که سنگین‌ترین به نظر می‌رسد، قله‌ی نزدیک است؛ نایست."),
    ("🌈", "بعد از این باران، رنگی در آسمانت است که قبلاً ندیده‌ای."),
    ("🍎", "سلامتی امروز سرمایه‌ی فرداست؛ یک انتخاب درست خوراکی بکن."),
    ("💼", "کاری که امروز انجام می‌دهی، امضای تو را برای سال‌ها نگه می‌دارد؛ درست امضا کن."),
    ("🪞", "آینه‌ی امروز یک خبر خوش دارد: تصویر کسی که دارد قوی‌تر می‌شود."),
    ("🎲", "برنده‌ی امروز کسی است که ریسک حساب‌شده می‌کند، نه کسی که شانسی می‌زند."),
    ("🌿", "آرامش امروز از یک فاصله‌ی سالم می‌آید؛ فاصله را رعایت کن."),
    ("📖", "فصلی از کتاب زندگی‌ات امروز بسته می‌شود تا فصل بهتری باز شود."),
    ("💫", "یکی از آرزوهای قدیمی‌ات امروز به قدم‌هایت می‌چسبد."),
    ("🎺", "صدای خوش‌حالی از گوشه‌ای از زندگی‌ات می‌آید؛ گوش کن."),
    ("🏡", "لانه‌ی تو امروز گرم‌تر از همیشه است؛ به همین‌ها نگاه کن."),
    ("🧩", "تکه‌ی گم‌شده‌ی پازل یک قرار است که هنوز نگرفته‌ای."),
    ("🚀", "امروز روز پرواز است؛ اسباب‌بازی‌های قدیمی را زمین بگذار."),
    ("🌟", "ستاره‌ای که به آن اشاره می‌کنی، اسم تو را می‌شناسد."),
    ("🪴", "آنچه کاشته‌ای بدون تو هم رشد می‌کند؛ امروز اجازه بده رشد کند."),
    ("🎡", "چرخه‌ی بدشانسی تمام شد؛ امروز سرِ شیرین چرخ است."),
    ("🤫", "رازی که نگه داشتی، امروز امتیازت می‌شود؛ زبانت را نگه دار."),
]

# ---- original zodiac vibes (my own one-liners) ----
ZODIAC = [
    ("♈ حمل", "امروز جرئتت جلوتر از احتیاطت است؛ فقط پل را پشت سر نسوزان."),
    ("♉ ثور", "صبوری امروزت مثل سپرده‌ی بانکی است؛ سودش فردا می‌رسد."),
    ("♊ جوزا", "دو ایده‌ی نصفه‌ی امروز، با یک گفت‌وگو یکی می‌شوند."),
    ("♋ سرطان", "خانه‌ی درونت امروز بندری امن است؛ از آن پذیرایی کن."),
    ("♌ اسد", "نور خورشید پشت توست؛ فقط سایه را روی کسی نینداز."),
    ("♍ سنبله", "نظم کوچکی که امروز می‌سازی، هفته‌ی بعد معجزه می‌شود."),
    ("♎ میزان", "ترازویت امروز دقیق است؛ تصمیمی که بوده را بگیر."),
    ("♏ عقرب", "ذهن تیزت امروز می‌تواند قفل را باز کند یا بشکند؛ درست بکارش ببر."),
    ("♐ قوس", "تیرت امروز دور می‌رود؛ هدف را نزدیک‌تر انتخاب کن."),
    ("♑ جدی", "پله‌به‌پله‌ی امروزت از پرواز یک‌شبه خوش‌نام‌تر است."),
    ("♒ دلو", "ایده‌ی عجیب امروز، عادیِ ده سال بعد است؛ زودتر بگو."),
    ("♓ حوت", "دریای درونت امروز مرواریدی دارد؛ فقط شنا را یادت نرود."),
]

PAIR_VERDICTS = [
    "این دو نفر با هم مثل چای و لیمو هستند — هر کدام تنها تلخ‌اند!",
    "پیش‌بینی می‌شود این دو با هم بهترین کارها را انجام دهند؛ یکی خلاق، دیگری منظم!",
    "این جفت مثل قطب‌های آهنربا هستند؛ کمی فاصله، کشش بیشتر!",
    "پیام‌رسان رسمی این دو نفر همین ربات است؛ سلام رد و بدل کنید!",
    "این دو نفر تیم آینده‌اند؛ فقط قرعه‌کشی اسم تیم را بگذارید!",
    "میانشان یک دوستی ۹۰ درصدی است؛ ۱۰ درصد باقی مانده برای کیک تولد!",
    "این دو تا هم‌دل‌اند؛ اگر تا حالا نساخته‌اید، امروز بسازید!",
    "مشترکاتشان از اختلافاتشان بیشتر است؛ ادامه بدهید!",
]


def fa(n) -> str:
    return str(n).translate(FA_DIGITS)


def today() -> dt.date:
    return dt.datetime.now(TEHRAN_TZ).date()


def seed_for(*parts) -> int:
    return hash(("fortune-bot", *parts))


# ---------------------------------------------------------------- database
SCHEMA = """
CREATE TABLE IF NOT EXISTS fal_log (
    user_id INTEGER NOT NULL,
    day TEXT NOT NULL,             -- Tehran date iso
    card INTEGER NOT NULL,
    PRIMARY KEY (user_id, day)
);
"""


def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


# ---------------------------------------------------------------- handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "🔮 <b>فال‌گر شوخ</b> — فال روزانه، طالع، سوزنی و تاس!\n\n"
        "🎴 <code>/fal</code> — کارت فال امروز (روزی یک بار، برای همه‌ی روز ثابت)\n"
        "♒ <code>/zodiac جدی</code> — طالع امروز برج\n"
        "💘 <code>/sooti</code> — سوزنی دو نفر (روی پیام طرف reply کن)\n"
        "🎲 <code>/dicefal</code> — فال سه‌تاسی\n\n"
        "<i>⚠️ همه‌چیز فقط برای سرگرمی است — هیچ پیشگویی واقعی نیست و متن‌ها اورجینال‌اند.</i>",
        parse_mode="HTML",
    )


async def cmd_fal(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    uid = update.effective_user.id
    day = today().isoformat()
    with db() as conn:
        row = conn.execute("SELECT card FROM fal_log WHERE user_id=? AND day=?", (uid, day)).fetchone()
        if row:
            card = row["card"]
            again = "\n\n<i>(فال امروزت را گرفته‌ای — تا فردا همین است!)</i>"
        else:
            card = random.Random(seed_for(uid, day)).randrange(len(CARDS))
            conn.execute("INSERT OR IGNORE INTO fal_log (user_id, day, card) VALUES (?,?,?)", (uid, day, card))
            again = ""
    emoji, text = CARDS[card]
    rng = random.Random(seed_for(uid, day, "luck"))
    lucky_num = rng.randint(1, 99)
    color = rng.choice(COLORS)
    await update.message.reply_text(
        f"🎴 <b>فال امروزِ {update.effective_user.first_name}</b>\n\n"
        f"{emoji} {text}\n\n"
        f"🔢 عدد خوش‌شانس: {fa(lucky_num)} · 🎨 رنگ روز: {color}\n"
        f"📅 {fa(today().year)}/{fa(today().month)}/{fa(today().day)}{again}\n\n"
        "<i>فقط سرگرمی! 😉</i>",
        parse_mode="HTML",
    )


async def cmd_zodiac(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    arg = " ".join(ctx.args).strip()
    keywords = [name.split()[1] for name, _ in ZODIAC]  # حمل، ثور، ...
    sign = None
    if arg:
        norm = arg.replace("‌", "")
        for i, kw in enumerate(keywords):
            if norm in kw.replace("‌", "") or kw.replace("‌", "").startswith(norm):
                sign = i
                break
    if sign is None:
        await update.message.reply_text(
            "✍️ استفاده: <code>/zodiac نام‌برج</code>\n"
            "برج‌ها: " + " · ".join(keywords),
            parse_mode="HTML",
        )
        return
    label, vibe = ZODIAC[sign]
    rng = random.Random(seed_for("zodiac", today().isoformat(), sign))
    color = rng.choice(COLORS)
    await update.message.reply_text(
        f"✨ <b>طالع امروز — {label}</b>\n\n{vibe}\n\n🎨 رنگ امروز: {color}\n<i>فقط سرگرمی!</i>",
        parse_mode="HTML",
    )


async def cmd_sooti(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    msg = update.message.reply_to_message
    if not msg or not msg.from_user or msg.from_user.is_bot:
        await update.message.reply_text("💘 روی پیام نفر دوم <b>reply</b> کن و بعد /sooti را بفرست.")
        return
    a, b = sorted([update.effective_user.id, msg.from_user.id])
    rng = random.Random(seed_for("sooti", a, b, today().isoformat()))
    pct = rng.randint(55, 99)
    verdict = rng.choice(PAIR_VERDICTS)
    bar = "❤️" * (pct // 10) + "🤍" * (10 - pct // 10)
    await update.message.reply_text(
        f"💘 <b>سوزنی‌سنج</b>\n\n"
        f"{update.effective_user.first_name} × {msg.from_user.first_name}\n\n"
        f"{bar}\n"
        f"تطابق: <b>{fa(pct)}٪</b>\n\n{verdict}\n\n<i>فقط سرگرمی!</i>",
        parse_mode="HTML",
    )


DICE_NAMES = ["دو", "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه", "ده", "یازده", "دوازده", "سیزده", "چهارده"]


async def cmd_dicefal(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    rng = random.Random(seed_for("dice", update.effective_user.id, today().isoformat(),
                                 update.message.message_id))
    dice = [rng.randint(1, 6) for _ in range(3)]
    total = sum(dice)
    faces = ["⚀", "⚁", "⚂", "⚃", "⚄", "⚅"]
    text = rng.choice(CARDS)[1]
    await update.message.reply_text(
        f"🎲 <b>فال سه‌تاسی</b>\n\n"
        f"{''.join(faces[d-1] for d in dice)} — جمع: {fa(total)}\n\n"
        f"📜 پیام تاس‌ها: {text}\n\n<i>فقط سرگرمی!</i>",
        parse_mode="HTML",
    )


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    with db() as conn:
        conn.executescript(SCHEMA)

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("fal", cmd_fal))
    app.add_handler(CommandHandler("zodiac", cmd_zodiac))
    app.add_handler(CommandHandler("sooti", cmd_sooti))
    app.add_handler(CommandHandler("dicefal", cmd_dicefal))
    print("Fortune Teller bot up —", len(CARDS), "original cards")
    app.run_polling()


if __name__ == "__main__":
    main()
