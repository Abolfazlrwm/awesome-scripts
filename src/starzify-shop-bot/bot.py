# Telegram: @Abolfazl_rwm
# -*- coding: utf-8 -*-
"""
Abolfazl Stars Shop — single-file shop bot (visible-behaviour rebuild)
======================================================================
Single-file bot built with aiogram 3.x + SQLite (stdlib sqlite3).

What this is:
  * A clean-room re-implementation of a complete stars/premium/gifts shop
    experience: forced-join gate, welcome text, the stretched (kashida)
    reply keyboard, the coloured inline product menu, Stars / Premium /
    crypto / gifts / reactions / boost shops, wallet top-up, account,
    challenges (dice & bowling), referral system, support & order tracking,
    plus a compact but functional admin panel.
  * Supplier / payment backends are TODO stubs (see deliver_order() and the
    "Payment Check" handler). Only visible behaviour is replicated.

Coloured buttons (Bot API 9.x):
  Telegram inline/reply buttons support `style`:
      "success" -> green, "primary" -> blue/teal, "danger" -> red.
  There is NO brown style in the Bot API. In the original bot the brown
  buttons are the Back / secondary / cancel buttons, so in this replica:

      COLOR_MAP = {
          "green": "success",   # buy / confirm rows in the original
          "blue":  "primary",   # teal/blue service rows in the original
          "red":   "danger",    # destructive rows
          "brown": None,        # back/secondary -> default (no style)
      }

  btn()/kbtn() below pass style= and icon_custom_emoji_id= when the
  installed aiogram version supports them and silently fall back to plain
  buttons on older aiogram versions.

Custom (premium) emojis:
  The original bot decorates many labels with Telegram custom emojis.
  Their custom_emoji_id values CANNOT be extracted from Telegram Web, so
  CUSTOM_EMOJI_IDS ships EMPTY (all None) and the labels keep their standard
  emoji prefixes. To use the real premium emojis: open your own bot, send
  yourself a message containing each premium emoji, read its
  custom_emoji_id (e.g. via a debug handler printing message.entities),
  paste the IDs into CUSTOM_EMOJI_IDS and the buttons will render them via
  icon_custom_emoji_id automatically.
"""

import asyncio
import logging
import os
import random
import sqlite3
import time
from contextlib import closing

from aiogram import Bot, Dispatcher, F, Router
from aiogram.enums import ParseMode
from aiogram.exceptions import TelegramBadRequest
from aiogram.filters import Command, CommandStart, StateFilter
from aiogram.fsm.context import FSMContext
from aiogram.fsm.state import State, StatesGroup
from aiogram.types import (
    CallbackQuery,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    KeyboardButton,
    Message,
    ReplyKeyboardMarkup,
)

# ---------------------------------------------------------------------------
# Config (.env)
# ---------------------------------------------------------------------------

def _load_dotenv(path: str = ".env") -> None:
    """Tiny .env loader (no external dependency)."""
    if not os.path.exists(path):
        return
    with open(path, encoding="utf-8") as fh:
        for line in fh:
            line = line.strip()
            if not line or line.startswith("#") or "=" not in line:
                continue
            key, _, value = line.partition("=")
            os.environ.setdefault(key.strip(), value.strip())


_load_dotenv(os.path.join(os.path.dirname(os.path.abspath(__file__)), ".env"))

BOT_TOKEN = os.environ.get("BOT_TOKEN", "")
ADMIN_IDS = {
    int(x) for x in os.environ.get("ADMIN_IDS", "").replace(";", ",").split(",")
    if x.strip().lstrip("-").isdigit()
}
# Forced-join channels (also editable in the settings table / admin panel).
REQUIRED_CHANNELS = [
    c.strip() for c in os.environ.get(
        "REQUIRED_CHANNELS", "@Abolfazl_rwm,@Abolfazl_rwm").split(",") if c.strip()
]
SUPPORT_USERNAME = os.environ.get("SUPPORT_USERNAME", "@Abolfazl_rwm")
DB_PATH = os.environ.get("DB_PATH", os.path.join(
    os.path.dirname(os.path.abspath(__file__)), "bot.db"))

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s %(levelname)s %(name)s: %(message)s")
log = logging.getLogger("starzify-replica")

# ---------------------------------------------------------------------------
# Custom emoji IDs — EMPTY placeholders (see module docstring).
# Paste real custom_emoji_id values here and buttons pick them up via
# icon_custom_emoji_id. Labels keep their standard emoji prefix either way.
# ---------------------------------------------------------------------------
CUSTOM_EMOJI_IDS = {
    "star": None,        # premium star used next to «خرید استارز»
    "premium": None,     # premium diamond next to «خرید پرمیوم»
    "crypto": None,      # coin next to «خرید ارز دیجیتال»
    "gift": None,        # gift box next to «خرید گیفت»
    "reaction": None,    # heart next to «خرید ری‌اکشن»
    "boost": None,       # rocket next to «خرید بوست»
    "wallet": None,      # wallet next to «افزایش موجودی»
    "account": None,     # person next to «حساب کاربری»
    "check": None,       # magnifier on «بررسی عضویت»
}

# Colour mapping observed in the original bot (see module docstring).
STYLE_SUCCESS = "success"   # green  — buy / confirm
STYLE_PRIMARY = "primary"   # blue/teal — service rows
STYLE_DANGER = "danger"     # red
STYLE_DEFAULT = None        # brown in the original = back/secondary (no style)


def btn(text, callback_data=None, *, style=None, emoji_key=None, url=None):
    """InlineKeyboardButton with optional Bot API style + custom emoji icon.

    Falls back to a plain button when the installed aiogram version does not
    know the style/icon_custom_emoji_id fields yet.
    """
    kwargs = {"text": text}
    if callback_data is not None:
        kwargs["callback_data"] = callback_data
    if url is not None:
        kwargs["url"] = url
    icon = CUSTOM_EMOJI_IDS.get(emoji_key) if emoji_key else None
    if style or icon:
        try:
            return InlineKeyboardButton(
                **kwargs, style=style, icon_custom_emoji_id=icon)
        except TypeError:
            pass  # older aiogram: plain button below
    return InlineKeyboardButton(**kwargs)


def kbtn(text, *, style=None, emoji_key=None, request_contact=False):
    """Reply-keyboard KeyboardButton with optional style + custom emoji icon."""
    kwargs = {"text": text}
    if request_contact:
        kwargs["request_contact"] = True
    icon = CUSTOM_EMOJI_IDS.get(emoji_key) if emoji_key else None
    if style or icon:
        try:
            return KeyboardButton(
                **kwargs, style=style, icon_custom_emoji_id=icon)
        except TypeError:
            pass
    return KeyboardButton(**kwargs)


def ikb(rows):
    return InlineKeyboardMarkup(inline_keyboard=rows)


def fmt(n) -> str:
    """1234567 -> '1,234,567' (the original formats Toman with commas)."""
    try:
        n = float(n)
    except (TypeError, ValueError):
        return str(n)
    if n == int(n):
        return f"{int(n):,}"
    return f"{n:,.2f}".rstrip("0").rstrip(".")


# ---------------------------------------------------------------------------
# Database (SQLite)
# ---------------------------------------------------------------------------

def db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


DEFAULT_SETTINGS = {
    # Prices were captured during exploration (2026-11-06) and FLOAT with the
    # market — every one of them is editable in /admin -> «تنظیمات قیمت‌ها».
    "price_per_star": "4523.24",        # 50 stars = 226,162 Toman at capture
    "premium_3": "3648604",             # Premium 3 months  (Toman)
    "premium_6": "4865731",             # Premium 6 months  (Toman)
    "premium_12": "8821610",            # Premium 12 months (Toman)
    "ton_wallet_activation": "35864",   # fixed price of 0.1 TON wallet activation
    "ton_personal_price": "329354",     # Toman per 1 TON (personal wallet), min 0.1
    "ton_balance_price": "332348",      # Toman per 1 TON (TON balance), min 1
    "trx_price": "95279",               # Toman per 1 TRX, min 1.5 (+1 TRX fee)
    "dogs_price": "9",                  # Toman per 1 DOGS, min 5000
    "usdt_price": "298870",             # Toman per 1 USDT, min 1
    "wallet_address": "UQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
    "wallet_memo": "000000",
    "required_channels": ",".join(REQUIRED_CHANNELS),
    "gate_enabled": "1",
}


def init_db() -> None:
    with closing(db()) as conn:
        conn.executescript(
            """
            CREATE TABLE IF NOT EXISTS users(
                id INTEGER PRIMARY KEY,
                name TEXT DEFAULT '',
                username TEXT DEFAULT '',
                phone TEXT DEFAULT '',
                card_number TEXT DEFAULT '',
                card_status TEXT DEFAULT '',
                balance INTEGER DEFAULT 0,
                discount_balance INTEGER DEFAULT 0,
                points INTEGER DEFAULT 0,
                ref_by INTEGER DEFAULT 0,
                ref_count INTEGER DEFAULT 0,
                ref_earnings INTEGER DEFAULT 0,
                total_ref_earned INTEGER DEFAULT 0,
                verified INTEGER DEFAULT 0,
                dice_last TEXT DEFAULT '',
                bowling_last TEXT DEFAULT '',
                joined_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS orders(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                kind TEXT DEFAULT '',
                title TEXT DEFAULT '',
                qty REAL DEFAULT 0,
                recipient TEXT DEFAULT '',
                total INTEGER DEFAULT 0,
                paid_from TEXT DEFAULT '',
                status TEXT DEFAULT 'pending',
                tracking_code TEXT UNIQUE,
                extra TEXT DEFAULT '',
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS discount_codes(
                code TEXT PRIMARY KEY,
                percent INTEGER NOT NULL,
                active INTEGER DEFAULT 1,
                owner_id INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS user_discount_codes(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                code TEXT NOT NULL,
                percent INTEGER NOT NULL,
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS settings(
                key TEXT PRIMARY KEY,
                value TEXT DEFAULT ''
            );
            """
        )
        for key, value in DEFAULT_SETTINGS.items():
            conn.execute(
                "INSERT OR IGNORE INTO settings(key,value) VALUES(?,?)",
                (key, value))
        # Seed discount code observed in the original bot.
        conn.execute(
            "INSERT OR IGNORE INTO discount_codes(code,percent,active,owner_id)"
            " VALUES('WelcomeCode',5,1,0)")
        conn.commit()


def get_setting(key: str) -> str:
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT value FROM settings WHERE key=?", (key,)).fetchone()
    return row["value"] if row else DEFAULT_SETTINGS.get(key, "")


def set_setting(key: str, value: str) -> None:
    with closing(db()) as conn:
        conn.execute(
            "INSERT INTO settings(key,value) VALUES(?,?)"
            " ON CONFLICT(key) DO UPDATE SET value=excluded.value",
            (key, value))
        conn.commit()


def price(key: str) -> float:
    try:
        return float(get_setting(key))
    except ValueError:
        return 0.0


def required_channels() -> list:
    raw = get_setting("required_channels") or ",".join(REQUIRED_CHANNELS)
    return [c.strip() for c in raw.split(",") if c.strip()]


def ensure_user(user_id: int, name: str = "", username: str = "",
                ref_by: int = 0) -> sqlite3.Row:
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        if row is None:
            conn.execute(
                "INSERT INTO users(id,name,username,ref_by,joined_at)"
                " VALUES(?,?,?,?,?)",
                (user_id, name, username or "", ref_by, int(time.time())))
            if ref_by and ref_by != user_id:
                conn.execute(
                    "UPDATE users SET ref_count=ref_count+1 WHERE id=?",
                    (ref_by,))
            conn.commit()
            row = conn.execute(
                "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        else:
            conn.execute(
                "UPDATE users SET name=?, username=? WHERE id=?",
                (name, username or "", user_id))
            conn.commit()
            row = conn.execute(
                "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
    return row


def get_user(user_id: int) -> sqlite3.Row | None:
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()


def update_user(user_id: int, **fields) -> None:
    if not fields:
        return
    cols = ", ".join(f"{k}=?" for k in fields)
    with closing(db()) as conn:
        conn.execute(f"UPDATE users SET {cols} WHERE id=?",
                     (*fields.values(), user_id))
        conn.commit()


def add_balance(user_id: int, amount: int, column: str = "balance") -> None:
    assert column in ("balance", "discount_balance", "ref_earnings",
                      "total_ref_earned", "points")
    with closing(db()) as conn:
        conn.execute(
            f"UPDATE users SET {column}={column}+? WHERE id=?",
            (amount, user_id))
        conn.commit()


def new_tracking_code() -> str:
    while True:
        code = "STF-" + "".join(random.choices("0123456789", k=8))
        with closing(db()) as conn:
            exists = conn.execute(
                "SELECT 1 FROM orders WHERE tracking_code=?",
                (code,)).fetchone()
        if not exists:
            return code


def create_order(user_id: int, kind: str, title: str, qty: float,
                 recipient: str, total: int, paid_from: str,
                 extra: str = "") -> str:
    code = new_tracking_code()
    with closing(db()) as conn:
        conn.execute(
            "INSERT INTO orders(user_id,kind,title,qty,recipient,total,"
            "paid_from,status,tracking_code,extra,created_at)"
            " VALUES(?,?,?,?,?,?,?, 'pending', ?, ?, ?)",
            (user_id, kind, title, qty, recipient, total, paid_from, code,
             extra, int(time.time())))
        conn.commit()
    return code


def set_order_status(code: str, status: str) -> None:
    with closing(db()) as conn:
        conn.execute(
            "UPDATE orders SET status=? WHERE tracking_code=?",
            (status, code))
        conn.commit()


def referral_percent(ref_count: int) -> int:
    """Referral commission levels (see §14): 5% / 6% / 8%."""
    if ref_count >= 30:
        return 8
    if ref_count >= 10:
        return 6
    return 5


def referral_level_name(ref_count: int) -> str:
    if ref_count >= 30:
        return "طلایی 🥇"
    if ref_count >= 10:
        return "نقره‌ای 🥈"
    return "برنزی 🥉"


# ---------------------------------------------------------------------------
# FSM states
# ---------------------------------------------------------------------------

class Shop(StatesGroup):
    stars_qty = State()
    premium_recipient = State()
    recipient = State()          # generic recipient step (stars/premium/gift)
    crypto_qty = State()
    gift_qty = State()
    gift_comment = State()
    reaction_qty = State()
    reaction_link = State()
    discount_code = State()


class TopUp(StatesGroup):
    rial_amount = State()
    crypto_amount = State()
    crypto_currency = State()


class Account(StatesGroup):
    card_number = State()
    track_code = State()


class Referral(StatesGroup):
    withdraw_amount = State()
    code_name = State()
    code_percent = State()


class Admin(StatesGroup):
    setting_value = State()
    discount_add = State()
    broadcast_text = State()
    broadcast_confirm = State()
    user_adjust_id = State()
    user_adjust_amount = State()


router = Router()
BOT_USERNAME = ""  # filled in main() via get_me()


# ---------------------------------------------------------------------------
# Texts (verbatim from the exploration report where quoted there)
# ---------------------------------------------------------------------------

WELCOME_TEXT = (
    "🌟 به ابوالفضل ، مرجع اصلی خرید استارز ، تون و پرمیوم تلگرام خوش آمدید! 🌟\n"
    "با بهترین قیمت و تحویل سریع 🚀 خدمات موردنیاز خود را با خیال راحت تهیه کنید.\n"
    "\n"
    "🛍️ خدمات ما:\n"
    "⭐️ استارز تلگرام\n"
    "🌟 پرمیوم تلگرام\n"
    "💎 خرید ارز تون ( GRAM)\n"
    "🪙 خرید ارز ترون ( TRX)\n"
    "🪙 شارژ تون بالانس\n"
    "🎁 گیفتهای استارزی\n"
    "⚡️ بوست تلگرام\n"
    "\n"
    "⭐️ برای شروع ، یکی از گزینههای منو را انتخاب کنید 👇"
)

GATE_TEXT = (
    "⚠️ برای استفاده از ربات، باید در کانالهای زیر عضو شوید:\n"
    "\n"
    "📢 @Abolfazl_rwm\n"
    "📣 @Abolfazl_rwm"
)
GATE_SUCCESS_POPUP = "✅ عضویت شما با موفقیت تأیید شد!"
GATE_FAIL_POPUP = "❌ هنوز در همه کانال‌ها عضو نشده‌اید!\nلطفاً ابتدا عضو شوید و دوباره تلاش کنید."

CANCEL_TEXT = "🚫 عملیات لغو شد.\n\nاز منوی اصلی می‌توانید دوباره شروع کنید 👇"

STARS_INTRO = (
    "🌟 وقت درخشیدن با استارز تلگرامه!\n"
    "\n"
    "💫 کاربردهای استارز:\n"
    "✨ فعالسازی ریاکشنهای استارز در چتها\n"
    "🪙 خرید یا تمدید اکانت پرمیوم تلگرام\n"
    "💸 پرداخت هزینه تبلیغات تلگرام و تبلیغ کانال یا ربات خود\n"
    "🛍 استفاده در خریدهای درونبرنامهای و مینیاپها\n"
    "\n"
    "🚀 سفارشهای استارز در کمتر از ۲ دقیقه انجام میشن ، با پشتیبانی کامل و لحظهای!\n"
    "\n"
    "📊 حداقل خرید: 50 استارز\n"
    "🔢 لطفاً تعداد استارز مورد نظر خود را ارسال کنید:"
)

PREMIUM_INTRO = (
    "🪙 خرید پرمیوم تلگرام 🌟\n"
    "\n"
    "با پرمیوم تلگرام از تمامی ویژگیهای پیشرفته و تجربهای حرفهای در تلگرام لذت ببرید!\n"
    "\n"
    "💫 مزایای پرمیوم:\n"
    "• 🚨 آپلود فایل تا 2GB\n"
    "• 🎨 استیکرهای انیمیشنی و پکهای اختصاصی\n"
    "• 🔍 جستجوی پیشرفته در چتها و کانالها\n"
    "• 📊 آمار دقیق پیامها و کانالها\n"
    "• 🎯 ویژگیهای کاربردی دیگر برای مدیریت بهتر\n"
    "\n"
    "🪙 پرمیوم تلگرام بهصورت کاملاً قانونی ، بدون نیاز به ورود به اکانت و فقط با یوزرنیم شما ، با بالاترین سرعت فعال میشود 😉\n"
    "\n"
    "کدام پلن را انتخاب میکنید؟"
)

RECIPIENT_TEXT = (
    "🔗 انتخاب اکانت دریافتکننده\n"
    "\n"
    "✅ اگر قصد خرید برای اکانت خودتان را دارید ، روی دکمه «برای خودم» کلیک کنید.\n"
    "✅ اگر قصد خرید برای شخص دیگری را دارید ، یوزرنیم ( آیدی) تلگرام او را بدون علامت @ ارسال کنید.\n"
    "\n"
    "✅ Pedarfarsi\n"
    "❌ @Pedarfarsi"
)

REACTION_INTRO = (
    "⭐️ وقتشه پستهات با ریاکشن استارزی بدرخشن!\n"
    "\n"
    "💫 ریاکشن استارزی چیه ؟\n"
    "ما به تعداد دلخواهت روی پست موردنظرت ریاکشن ⭐️ ثبت میکنیم — سریع ، امن و خفن.\n"
    "\n"
    "⭐️ کاربردها:\n"
    "✨ افزایش جذابیت و اعتبار پستها\n"
    "🚀 جلب توجه بیشتر مخاطبها\n"
    "🔥 کمک به دیدهشدن و وایرال شدن محتوا\n"
    "\n"
    "🪙 ثبت سفارش در کمترین زمان ممکن ، همراه با پشتیبانی کامل و لحظهای!\n"
    "\n"
    "📊 حداقل خرید: 5 استارز\n"
    "⚡️ لطفاً تعداد ریاکشن استارزی موردنظر خود را ارسال کنید 👇"
)
REACTION_LINK_TEXT = (
    "🔗 ارسال لینک پست\n"
    "\n"
    "لطفاً لینک پستی که میخواهید ریاکشنهای استارزی روی آن ثبت شود را ارسال کنید 👇\n"
    "\n"
    "⚠️ قبل از ارسال لینک ، موارد زیر را بررسی کنید:\n"
    "✅ کانال شما باید عمومی (Public) باشد.\n"
    "✅ قابلیت دریافت ریاکشن ⭐️ روی پست موردنظر فعال باشد.\n"
    "\n"
    "📎 نمونه لینک صحیح: https://t.me/yourchannel/123"
)

BOOST_DISABLED_TEXT = "بوست فعلاً غیرفعال است."

TOPUP_TEXT = (
    "💰 افـزایـش مـوجـودی حساب شما\n"
    "\n"
    "شما میتوانید موجودی خود را به سه روش امن و سریع افزایش دهید:\n"
    "💳 پرداخت درگاهی – شارژ آنی موجودی از طریق درگاه پرداخت امن بانکی.\n"
    "💳 پرداخت ریالی – پس از واریز ، موجودی شما به سرعت با تایید پشتیبانی شارژ میشود.\n"
    "💰 پرداخت با ارز دیجیتال – پرداختها به صورت آنی و لحظهای انجام میشوند و موجودی شما بلافاصله شارژ میشود.\n"
    "\n"
    "🎥 آموزش افزایش موجودی: https://t.me/Abolfazl_rwm\n"
    "اگر با نحوه شارژ کیف پول آشنایی ندارید ، روی «آموزش افزایش موجودی» کلیک کنید و ویدیوی آموزشی را مشاهده نمایید.\n"
    "\n"
    "🚀 فرآیند افزایش موجودی ساده ، سریع و امن است و پس از پرداخت میتوانید به راحتی از تمامی خدمات ربات استفاده کنید ✔️"
)
GATEWAY_MAINTENANCE_TEXT = (
    "⚠️ درگاهی در حال بهروزرسانی میباشد. لطفاً از روشهای دیگر استفاده کنید."
)
VERIFICATION_REQUIRED_TEXT = (
    "🔐 برای افزایش موجودی ریالی، ابتدا باید احراز هویت انجام دهید:\n"
    "\n"
    "📱 ۱- شماره موبایل خود را از بخش «حساب کاربری» تایید کنید.\n"
    "💳 ۲- یک کارت بانکی به نام خودتان ثبت کنید.\n"
    "\n"
    "✅ بعد از تایید کارت توسط پشتیبانی، امکان پرداخت ریالی فعال می‌شود."
)

SUPPORT_TEXT = (
    "🎧 پشتیبانی ابوالفضل 🎧\n"
    "\n"
    "💬 پشتیبانی آنلاین: @Abolfazl_rwm\n"
    "📢 آیدی پشتیبانی دوم: @Abolfazl_rwm\n"
    "👥 گروه گفتگو: @Abolfazl_rwm\n"
    "\n"
    "⏰ پشتیبانی ۲۴ ساعته، حتی در روزهای تعطیل\n"
    "📌 برای پیگیری سفارش، کد رهگیری را برای پشتیبانی ارسال کنید."
)

TRUST_TEXT = (
    "🛡 اعتماد و نمادهای ابوالفضل\n"
    "\n"
    "📢 کانال اصلی و اطلاع‌رسانی: @Abolfazl_rwm\n"
    "📣 کانال گزارش سفارش‌ها و رسیدها: @Abolfazl_rwm\n"
    "👥 گروه گفتگوی کاربران: @Abolfazl_rwm\n"
    "🎧 پشتیبانی: @Abolfazl_rwm\n"
    "\n"
    "✅ همه سفارش‌ها به صورت خودکار و آنی تحویل داده می‌شوند و در صورت بروز"
    " مشکل، مبلغ به کیف پول شما بازگردانده می‌شود."
)

TRACK_INTRO_TEXT = (
    "✍️ پیگیری سفارش\n"
    "\n"
    "🔢 لطفاً کد پیگیری سفارش خود را وارد کنید:"
)
TRACK_NOT_FOUND = "❌ سفارش با کد پیگیری {code} یافت نشد!"


def insufficient_text(need: int, have: int) -> str:
    """Insufficient-balance error, in the original bot's format."""
    return (
        "❌ موجودی کیف پول شما کافی نیست!\n"
        "\n"
        f"💵 مبلغ مورد نیاز: {fmt(need)} تومان\n"
        f"👛 موجودی شما: {fmt(have)} تومان\n"
        f"📉 کسری موجودی: {fmt(max(0, need - have))} تومان\n"
        "\n"
        "💰 از بخش «افزایش موجودی» کیف پول خود را شارژ کنید."
    )


# ---------------------------------------------------------------------------
# Keyboards
# ---------------------------------------------------------------------------
# Main REPLY keyboard: exact stretched (kashida ـ) labels, row layout as
# observed — green row = success, blue rows = primary, red row = danger.

LBL_SHOP = "🛒 خـریـد مـحـصـول"
LBL_TOPUP = "➕ افـزایـش مـوجـودی"
LBL_ACCOUNT = "💳 حـسـاب کـاربـری"
LBL_REFERRAL = "👥 زیـر مـجـمـوعه گیـری"
LBL_SUPPORT = "📞 پـشتـیـبـانـی"
LBL_TRACK = "📋 پـیـگیـری سـفـارش"
LBL_TRUST = "🤔 چـطـوری مـیتوانـم بـه شـمـا اعـتـماد کـنـم"

MAIN_MENU_LABELS = {
    LBL_SHOP, LBL_TOPUP, LBL_ACCOUNT, LBL_REFERRAL,
    LBL_SUPPORT, LBL_TRACK, LBL_TRUST,
}


def main_reply_kb() -> ReplyKeyboardMarkup:
    return ReplyKeyboardMarkup(
        keyboard=[
            [kbtn(LBL_SHOP, style=STYLE_SUCCESS, emoji_key="star")],
            [kbtn(LBL_TOPUP, style=STYLE_PRIMARY, emoji_key="wallet"),
             kbtn(LBL_ACCOUNT, style=STYLE_PRIMARY, emoji_key="account")],
            [kbtn(LBL_REFERRAL, style=STYLE_DANGER)],
            [kbtn(LBL_SUPPORT, style=STYLE_PRIMARY),
             kbtn(LBL_TRACK, style=STYLE_PRIMARY)],
            [kbtn(LBL_TRUST, style=STYLE_DANGER)],
        ],
        resize_keyboard=True,
    )


def gate_kb() -> InlineKeyboardMarkup:
    # As observed in the original: a single green check button; the channel
    # usernames in the gate text itself are tappable links.
    return ikb([[btn("🔍 بررسی عضویت 🔍", "gate:check",
                     style=STYLE_SUCCESS, emoji_key="check")]])


def back_kb(callback: str = "nav:shop") -> InlineKeyboardMarkup:
    # Brown in the original = back/secondary -> default style (no style).
    return ikb([[btn("بــرگـشـت", callback)]])


def shop_menu_kb() -> InlineKeyboardMarkup:
    """Product inline menu: layout and colours exactly as observed."""
    return ikb([
        [btn("سفارش های اخیر من", "acc:orders", style=STYLE_SUCCESS)],
        [btn("استارز", "shop:stars", style=STYLE_PRIMARY, emoji_key="star"),
         btn("پــرمــیــوم", "shop:premium", style=STYLE_PRIMARY, emoji_key="premium")],
        [btn("خرید رمز ارز", "shop:crypto", style=STYLE_PRIMARY, emoji_key="crypto")],
        [btn("گــیفــت اسـتــارزی", "shop:gifts", style=STYLE_PRIMARY, emoji_key="gift"),
         btn("ریاکـشـن اسـتــارزی", "shop:reactions", style=STYLE_PRIMARY, emoji_key="reaction")],
        [btn("بــوســت تـلـگـرام", "shop:boost", style=STYLE_PRIMARY, emoji_key="boost")],
        [btn("بــرگـشـت", "nav:main")],
    ])


def recipient_kb(first_name: str) -> InlineKeyboardMarkup:
    return ikb([
        [btn(f"👤 برای خودم ({first_name})", "rcpt:self", style=STYLE_SUCCESS)],
        [btn("بــرگـشـت", "nav:shop")],
    ])


def gift_recipient_kb(first_name: str) -> InlineKeyboardMarkup:
    """Recipient keyboard for gifts, with the hide/comment toggles (§9)."""
    return ikb([
        [btn(f"👤 برای خودم ({first_name})", "rcpt:self", style=STYLE_SUCCESS)],
        [btn("🙈 مخفی کردن از پروفایل", "giftx:hide", style=STYLE_PRIMARY),
         btn("✍️ افزودن متن همراه", "giftx:comment", style=STYLE_PRIMARY)],
        [btn("بــرگـشـت", "nav:shop")],
    ])


def invoice_kb(has_discount_balance: bool, has_code: bool) -> InlineKeyboardMarkup:
    rows = [
        [btn("تــایــیــد ✅", "inv:confirm", style=STYLE_SUCCESS),
         btn("لــغــو خـریـد", "inv:cancel")],
    ]
    second = []
    if has_discount_balance:
        second.append(btn("اعمال تخفیف", "inv:use_discount",
                          style=STYLE_PRIMARY))
    if has_code:
        second.append(btn("حذف کد تخفیف", "inv:remove_code",
                          style=STYLE_DANGER))
    else:
        second.append(btn("اعمال کد تخفیف", "inv:code",
                          style=STYLE_PRIMARY))
    if second:
        rows.append(second)
    return ikb(rows)


def invoice_text(data: dict, user) -> str:
    title = data.get("title", "")
    lines = [f"⏰ فاکتور خرید {title}".rstrip(), ""]
    if data.get("qty_label"):
        lines.append(f"💫 مقدار خرید: {data['qty_label']}")
    lines.append(f"🔗 یوزر دریافتکننده: {data.get('recipient', '—')}")
    lines.append(f"💰 مبلغ فاکتور: {fmt(data['gross'])} تومان")
    if data.get("code"):
        lines.append(
            f"🎫 تخفیف کد: {data['code_percent']}% "
            f"({fmt(data.get('code_discount', 0))} تومان)")
    lines.append(f"🎁 کل موجودی تخفیف: {fmt(user['discount_balance'])} تومان")
    max_disc = min(int(user["discount_balance"]), int(data["gross"]))
    lines.append(f"💡 حداکثر تخفیف قابل اعمال: {fmt(max_disc)} تومان")
    if data.get("use_discount"):
        lines.append(
            f"🎁 تخفیف موجودی: {fmt(data.get('discount_used', 0))} تومان")
    lines.append(f"💳 مبلغ نهایی: {fmt(data['payable'])} تومان")
    lines.append("")
    lines.append("💼 در صورتی که جزئیات بالا مورد تأیید شماست ✓ روی دکمه «تأیید ✅» کلیک کنید.")
    return "\n".join(lines)


# ---------------------------------------------------------------------------
# Forced-join gate
# ---------------------------------------------------------------------------

async def is_member(bot: Bot, user_id: int) -> bool:
    if get_setting("gate_enabled") != "1" or user_id in ADMIN_IDS:
        return True
    for ch in required_channels():
        try:
            member = await bot.get_chat_member(ch, user_id)
        except TelegramBadRequest:
            return False
        if member.status in ("left", "kicked"):
            return False
    return True


@router.callback_query(F.data == "gate:check")
async def gate_check(call: CallbackQuery, state: FSMContext):
    if await is_member(call.bot, call.from_user.id):
        await call.answer(GATE_SUCCESS_POPUP, show_alert=True)
        await state.clear()
        user = ensure_user(call.from_user.id, call.from_user.full_name,
                           call.from_user.username)
        try:
            await call.message.delete()
        except TelegramBadRequest:
            pass
        await call.message.answer(
            WELCOME_TEXT, reply_markup=main_reply_kb())
        _ = user
    else:
        await call.answer(GATE_FAIL_POPUP, show_alert=True)


async def require_member(message: Message, state: FSMContext) -> bool:
    """Returns True when the user may continue; otherwise sends the gate."""
    if await is_member(message.bot, message.from_user.id):
        return True
    await state.clear()
    await message.answer(GATE_TEXT, reply_markup=gate_kb())
    return False


# ---------------------------------------------------------------------------
# Waiting-state rule (standing rule from the user's other bots):
# pressing another menu button — or /start / /cancel — ALWAYS exits the
# current FSM state and the newly pressed button works immediately.
# This handler is registered FIRST so it wins over every state handler.
# ---------------------------------------------------------------------------

@router.message(F.text.in_(MAIN_MENU_LABELS))
async def menu_button_any_state(message: Message, state: FSMContext):
    await state.clear()
    if not await require_member(message, state):
        return
    ensure_user(message.from_user.id, message.from_user.full_name,
                message.from_user.username)
    handler = {
        LBL_SHOP: show_shop_menu,
        LBL_TOPUP: show_topup,
        LBL_ACCOUNT: show_account,
        LBL_REFERRAL: show_referral,
        LBL_SUPPORT: show_support,
        LBL_TRACK: ask_track_code,
        LBL_TRUST: show_trust,
    }[message.text]
    await handler(message, state)


@router.message(CommandStart())
async def cmd_start(message: Message, state: FSMContext, command=None):
    await state.clear()
    args = (message.text or "").split(maxsplit=1)
    ref_by = 0
    if len(args) == 2 and args[1].startswith("ref_"):
        try:
            ref_by = int(args[1][4:])
        except ValueError:
            ref_by = 0
    ensure_user(message.from_user.id, message.from_user.full_name,
                message.from_user.username, ref_by=ref_by)
    if not await require_member(message, state):
        return
    await message.answer(WELCOME_TEXT, reply_markup=main_reply_kb())


@router.message(Command("cancel"))
async def cmd_cancel(message: Message, state: FSMContext):
    await state.clear()
    await message.answer(CANCEL_TEXT, reply_markup=main_reply_kb())


@router.message(F.text == "❌ لغو")
async def text_cancel(message: Message, state: FSMContext):
    await state.clear()
    await message.answer(CANCEL_TEXT, reply_markup=main_reply_kb())


# ---------------------------------------------------------------------------
# Main sections (reply-keyboard destinations)
# ---------------------------------------------------------------------------

async def show_shop_menu(message: Message, state: FSMContext):
    await message.answer(
        "🛒 وقتشه محصولت رو انتخاب کنی! 🚀 تمامی سفارشات با بالاترین سرعت و همراه با پشتیبانی کامل انجام میشن تا تجربهای مطمئن و بیدغدغه داشته باشی! 🫶 از لیست زیر ، گزینه مورد نظرت رو انتخاب کن:",
        reply_markup=shop_menu_kb())


def support_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("ارتباط غیرمستقیم 📧", url="https://t.me/Abolfazl_rwm",
             style=STYLE_PRIMARY),
         btn("ارتباط مستقیم 💬", url="https://t.me/Abolfazl_rwm",
             style=STYLE_PRIMARY)],
        [btn("گروه ابوالفضل 👥", url="https://t.me/Abolfazl_rwm",
             style=STYLE_SUCCESS)],
        [btn("بــرگـشـت", "nav:main")],
    ])


async def show_support(message: Message, state: FSMContext):
    await message.answer(
        "لطفاً یکی از گزینههای زیر را انتخاب کنید تا با پشتیبانی ارتباط بگیرید 👇",
        reply_markup=support_kb())


async def show_trust(message: Message, state: FSMContext):
    await message.answer(TRUST_TEXT, reply_markup=back_kb("nav:main"))


async def ask_track_code(message: Message, state: FSMContext):
    await state.set_state(Account.track_code)
    await message.answer(TRACK_INTRO_TEXT, reply_markup=back_kb("nav:main"))


@router.message(Account.track_code, F.text)
async def track_code_input(message: Message, state: FSMContext):
    code = (message.text or "").strip()
    await state.clear()
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM orders WHERE tracking_code=?", (code,)).fetchone()
    if row is None:
        await message.answer(TRACK_NOT_FOUND.format(code=code),
                             reply_markup=main_reply_kb())
        return
    status_fa = {
        "pending": "⏳ در انتظار انجام",
        "paid": "✅ پرداخت شده — در حال انجام",
        "delivered": "🎉 تحویل داده شده",
        "rejected": "❌ رد شده",
        "refunded": "↩️ بازگشت وجه",
    }.get(row["status"], row["status"])
    await message.answer(
        "🧾 وضعیت سفارش\n"
        "\n"
        f"🔎 کد رهگیری: <code>{row['tracking_code']}</code>\n"
        f"📦 محصول: {row['title']}\n"
        f"💵 مبلغ: {fmt(row['total'])} تومان\n"
        f"📌 وضعیت: {status_fa}",
        reply_markup=main_reply_kb())


@router.callback_query(F.data == "nav:main")
async def nav_main(call: CallbackQuery, state: FSMContext):
    await state.clear()
    await call.answer()
    await call.message.answer(WELCOME_TEXT, reply_markup=main_reply_kb())


@router.callback_query(F.data == "nav:shop")
async def nav_shop(call: CallbackQuery, state: FSMContext):
    await state.clear()
    await call.answer()
    try:
        await call.message.edit_text(
            "🛍 دسته‌بندی محصولات را انتخاب کنید:", reply_markup=shop_menu_kb())
    except TelegramBadRequest:
        await call.message.answer(
            "🛍 دسته‌بندی محصولات را انتخاب کنید:", reply_markup=shop_menu_kb())


# ---------------------------------------------------------------------------
# Invoice engine (shared by stars / premium / crypto / gifts / reactions)
# FSM data keys: kind, title, qty, qty_label, unit_price, gross, recipient,
#                code, code_percent, code_discount, use_discount,
#                discount_used, payable, extra
# ---------------------------------------------------------------------------

async def open_invoice(message: Message, state: FSMContext, **inv):
    inv.setdefault("code", None)
    inv.setdefault("code_percent", 0)
    inv.setdefault("code_discount", 0)
    inv.setdefault("use_discount", False)
    inv.setdefault("discount_used", 0)
    inv["payable"] = int(inv["gross"] - inv["code_discount"])
    await state.set_data(inv)
    user = get_user(message.from_user.id)
    await state.set_state(None)
    await message.answer(
        invoice_text(inv, user),
        reply_markup=invoice_kb(user["discount_balance"] > 0, bool(inv["code"])))


async def refresh_invoice(call: CallbackQuery, state: FSMContext):
    data = await state.get_data()
    user = get_user(call.from_user.id)
    try:
        await call.message.edit_text(
            invoice_text(data, user),
            reply_markup=invoice_kb(user["discount_balance"] > 0,
                                    bool(data.get("code"))))
    except TelegramBadRequest:
        pass


@router.callback_query(F.data == "inv:code")
async def invoice_code_prompt(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await state.set_state(Shop.discount_code)
    await call.message.answer(
        "🎟 کد تخفیف خود را ارسال کنید:\n"
        "📌 مثال: WelcomeCode\n\n"
        "برای انصراف /cancel را بزنید.")


@router.message(Shop.discount_code, F.text)
async def invoice_code_input(message: Message, state: FSMContext):
    code = (message.text or "").strip()
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM discount_codes WHERE code=? AND active=1",
            (code,)).fetchone()
    data = await state.get_data()
    if row is None:
        await message.answer(
            "❌ کد تخفیف وارد شده معتبر نیست یا منقضی شده است!\n"
            "دوباره تلاش کنید یا /cancel را بزنید.")
        return
    await state.set_state(None)
    percent = row["percent"]
    code_discount = int(data["gross"] * percent / 100)
    data.update(code=code, code_percent=percent, code_discount=code_discount)
    data["payable"] = int(data["gross"] - code_discount - data.get("discount_used", 0))
    await state.set_data(data)
    user = get_user(message.from_user.id)
    await message.answer(
        f"✅ کد تخفیف «{code}» با {percent}٪ تخفیف اعمال شد.")
    await message.answer(
        invoice_text(data, user),
        reply_markup=invoice_kb(user["discount_balance"] > 0, True))


@router.callback_query(F.data == "inv:remove_code")
async def invoice_remove_code(call: CallbackQuery, state: FSMContext):
    data = await state.get_data()
    data.update(code=None, code_percent=0, code_discount=0)
    data["payable"] = int(data["gross"] - data.get("discount_used", 0))
    await state.set_data(data)
    await call.answer("🗑 کد تخفیف حذف شد.")
    await refresh_invoice(call, state)


@router.callback_query(F.data == "inv:use_discount")
async def invoice_use_discount(call: CallbackQuery, state: FSMContext):
    data = await state.get_data()
    user = get_user(call.from_user.id)
    remaining = int(data["gross"] - data.get("code_discount", 0))
    use = min(user["discount_balance"], remaining)
    if use <= 0:
        await call.answer("🎁 موجودی تخفیف شما صفر است.", show_alert=True)
        return
    data["use_discount"] = True
    data["discount_used"] = use
    data["payable"] = remaining - use
    await state.set_data(data)
    await call.answer(f"🎁 {fmt(use)} تومان از موجودی تخفیف شما اعمال شد.")
    await refresh_invoice(call, state)


@router.callback_query(F.data == "inv:cancel")
async def invoice_cancel(call: CallbackQuery, state: FSMContext):
    await state.clear()
    await call.answer("🚫 خرید لغو شد.")
    try:
        await call.message.edit_text(CANCEL_TEXT, reply_markup=shop_menu_kb())
    except TelegramBadRequest:
        await call.message.answer(CANCEL_TEXT, reply_markup=shop_menu_kb())


@router.callback_query(F.data == "inv:confirm")
async def invoice_confirm(call: CallbackQuery, state: FSMContext):
    data = await state.get_data()
    if not data or "payable" not in data:
        await call.answer("❌ فاکتوری یافت نشد.", show_alert=True)
        return
    user = get_user(call.from_user.id)
    payable = int(data["payable"])
    if user["balance"] < payable:
        await call.answer("❌ موجودی کافی نیست!", show_alert=True)
        await call.message.answer(
            insufficient_text(payable, user["balance"]),
            reply_markup=main_reply_kb())
        return
    # Deduct: discount balance first (if applied), then main balance.
    if data.get("use_discount") and data.get("discount_used"):
        add_balance(call.from_user.id, -int(data["discount_used"]),
                    "discount_balance")
    if payable:
        add_balance(call.from_user.id, -payable, "balance")
    paid_from = "wallet+discount" if data.get("use_discount") else "wallet"
    code = create_order(
        call.from_user.id, data["kind"], data["title"], data.get("qty", 0),
        data.get("recipient", ""), payable + int(data.get("discount_used", 0)),
        paid_from, data.get("extra", ""))
    await state.clear()
    await call.answer("✅ پرداخت با موفقیت انجام شد.")
    await call.message.answer(
        "✅ سفارش شما با موفقیت ثبت شد!\n"
        "\n"
        f"📦 محصول: {data['title']}\n"
        f"💵 مبلغ پرداختی: {fmt(payable + int(data.get('discount_used', 0)))} تومان\n"
        f"🔎 کد رهگیری: <code>{code}</code>\n"
        "\n"
        "🚀 سفارش به صورت خودکار در حال انجام است.\n"
        "📌 با دکمه «پیگیری سفارش» می‌توانید وضعیت آن را ببینید.",
        reply_markup=main_reply_kb())
    await deliver_order(call.bot, code)


async def deliver_order(bot: Bot, tracking_code: str) -> None:
    """TODO(stub): real supplier integration goes here.

    Visible behaviour only: the order is marked delivered, the buyer earns
    referral points, the referrer (if any) earns their level commission, and
    admins are notified. Replace the body with the real Fragment / supplier
    API calls when connecting the production backend.
    """
    with closing(db()) as conn:
        order = conn.execute(
            "SELECT * FROM orders WHERE tracking_code=?",
            (tracking_code,)).fetchone()
    if order is None:
        return
    set_order_status(tracking_code, "delivered")
    buyer = get_user(order["user_id"])
    if buyer is None:
        return
    # Buyer points: 1 point per 100,000 Toman spent (min 1 per order).
    points = max(1, int(order["total"]) // 100000)
    add_balance(buyer["id"], points, "points")
    # Referral commission -> referrer's earnings (level 5% / 6% / 8%).
    if buyer["ref_by"]:
        referrer = get_user(buyer["ref_by"])
        if referrer is not None:
            pct = referral_percent(referrer["ref_count"])
            commission = int(order["total"] * pct / 100)
            if commission > 0:
                add_balance(referrer["id"], commission, "ref_earnings")
                add_balance(referrer["id"], commission, "total_ref_earned")
                try:
                    await bot.send_message(
                        referrer["id"],
                        "🎉 کمیسیون زیرمجموعه‌گیری!\n"
                        f"💰 {fmt(commission)} تومان ({pct}٪ از خرید زیرمجموعه شما) "
                        "به درآمد شما اضافه شد.")
                except Exception:
                    pass
    for admin_id in ADMIN_IDS:
        try:
            await bot.send_message(
                admin_id,
                "🛍 سفارش جدید (تحویل خودکار - استاب)\n"
                f"🔎 کد: <code>{tracking_code}</code>\n"
                f"📦 {order['title']}\n"
                f"👤 کاربر: {order['user_id']}\n"
                f"💵 مبلغ: {fmt(order['total'])} تومان")
        except Exception:
            pass


# ---------------------------------------------------------------------------
# Stars (§6)
# ---------------------------------------------------------------------------

def stars_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("محاسبه با موجودی من", "stars:calc", style=STYLE_PRIMARY),
         btn("بــرگـشـت", "nav:shop")],
    ])


@router.callback_query(F.data == "shop:stars")
async def stars_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await state.set_state(Shop.stars_qty)
    await state.set_data({"kind": "stars"})
    await call.message.answer(STARS_INTRO, reply_markup=stars_kb())


@router.callback_query(F.data == "stars:calc", Shop.stars_qty)
async def stars_calc(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    unit = price("price_per_star")
    affordable = int(user["balance"] // unit) if unit else 0
    if affordable < 50:
        need = int(round(50 * unit))
        await call.answer()
        await call.message.answer(
            "❌ موجودی ناکافی!\n"
            f"💰 موجودی اصلی شما: {fmt(user['balance'])} تومان\n"
            f"🎁 موجودی تخفیف: {fmt(user['discount_balance'])} تومان\n"
            f"‼️ راهکار: شما میتوانید حساب موجودی اصلی خود را به مبلغ {fmt(need)} تومان شارژ کنید و 50 استارز را خریداری کنید!")
        return
    data = await state.get_data()
    data.update(qty=affordable, unit_price=unit)
    await state.set_data(data)
    await state.set_state(Shop.recipient)
    await call.answer()
    await call.message.answer(
        RECIPIENT_TEXT, reply_markup=recipient_kb(call.from_user.first_name))


@router.message(Shop.stars_qty, F.text)
async def stars_qty_input(message: Message, state: FSMContext):
    text = (message.text or "").strip()
    if not text.isdigit():
        await message.answer("❌ لطفاً فقط عدد ارسال کنید.\n📌 مثال: 100")
        return
    qty = int(text)
    if qty < 50:
        await message.answer(
            "❌ حداقل خرید استارز 50 عدد است!\n"
            f"🔢 تعداد وارد شده: {qty}\n"
            "لطفاً تعداد بیشتری وارد کنید.")
        return
    data = await state.get_data()
    data.update(qty=qty, unit_price=price("price_per_star"))
    await state.set_data(data)
    await state.set_state(Shop.recipient)
    await message.answer(
        RECIPIENT_TEXT, reply_markup=recipient_kb(message.from_user.first_name))


async def after_recipient(message_or_call, state: FSMContext, recipient: str):
    """Build the invoice once the recipient is known (shared by shops)."""
    data = await state.get_data()
    data["recipient"] = recipient
    kind = data.get("kind")
    if kind == "stars":
        gross = int(round(data["qty"] * data["unit_price"]))
        data.update(title="استارز",
                    qty_label=f"{data['qty']}", gross=gross)
    elif kind == "premium":
        data.update(title="پرمیوم",
                    qty_label=f"{data['months']}.00 ماهه",
                    gross=int(data["unit_price"]))
    elif kind == "gift":
        unit = price("price_per_star")
        gross = int(round(data["gift_stars"] * data["gift_qty"] * unit))
        if not data.get("extra"):
            data["extra"] = (
                f"comment={data.get('gift_comment', '')};"
                f"hide={int(bool(data.get('gift_hide')))}")
        data.update(
            title="گیفت",
            qty_label=f"گیفت {data['gift_name']} ({data['gift_stars']} ⭐️)",
            gross=gross)
    elif kind in ("ton_wallet", "ton_personal", "ton_balance",
                  "trx", "dogs", "usdt"):
        data["gross"] = int(round(data["qty"] * data["unit_price"]))
    elif kind == "reaction":
        unit = price("price_per_star") / 10  # reaction unit price derived, see note
        data["unit_price"] = unit
        data.update(title=f"❤️ ری‌اکشن تلگرام ({data['qty']} عدد)",
                    qty_label=f"{data['qty']} ری‌اکشن",
                    gross=int(round(data["qty"] * unit)))
    await state.set_data(data)
    msg = message_or_call.message if isinstance(message_or_call, CallbackQuery) \
        else message_or_call
    await open_invoice(msg, state, **data)


@router.callback_query(F.data == "rcpt:self", Shop.recipient)
async def recipient_self(call: CallbackQuery, state: FSMContext):
    await call.answer()
    recip = f"@{call.from_user.username}" if call.from_user.username \
        else str(call.from_user.id)
    await after_recipient(call, state, recip)


@router.message(Shop.recipient, F.text)
async def recipient_input(message: Message, state: FSMContext):
    recip = (message.text or "").strip()
    if len(recip) < 3:
        await message.answer("❌ آیدی گیرنده معتبر نیست، دوباره ارسال کنید.")
        return
    await after_recipient(message, state, recip)


# ---------------------------------------------------------------------------
# Premium (§7)
# ---------------------------------------------------------------------------

PREMIUM_PLANS = [(3, "premium_3"), (6, "premium_6"), (12, "premium_12")]


def premium_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("3 ماهه", "prem:3", style=STYLE_PRIMARY),
         btn("6 ماهه", "prem:6", style=STYLE_PRIMARY)],
        [btn("1 ساله", "prem:12", style=STYLE_PRIMARY)],
        [btn("بــرگـشـت", "nav:shop")],
    ])


@router.callback_query(F.data == "shop:premium")
async def premium_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await call.message.answer(PREMIUM_INTRO, reply_markup=premium_kb())


@router.callback_query(F.data.startswith("prem:"))
async def premium_pick(call: CallbackQuery, state: FSMContext):
    months = int(call.data.split(":")[1])
    key = dict(PREMIUM_PLANS)[months]
    await call.answer()
    await state.set_data({"kind": "premium", "months": months,
                          "qty": months, "unit_price": price(key)})
    await state.set_state(Shop.recipient)
    await call.message.answer(
        RECIPIENT_TEXT, reply_markup=recipient_kb(call.from_user.first_name))


# ---------------------------------------------------------------------------
# Crypto (§8)
# ---------------------------------------------------------------------------

CRYPTO_INTRO = (
    "🪙 خرید ارز دیجیتال 🪙\n"
    "\n"
    "💱 خرید آنی تون‌کوین، ترون، داگز و تتر با موجودی کیف پول ریالی شما\n"
    "🚀 انتقال خودکار به آدرس کیف پول شما\n"
    "\n"
    "🔻 ارز مورد نظر را انتخاب کنید:"
)

TON_INTRO = (
    "💎 خرید تون‌کوین (TON)\n"
    "\n"
    "سه روش خرید تون در ابوالفضل:\n"
    "🔹 فعال‌سازی کیف پول: دریافت 0.1 تون برای فعال‌سازی کیف پول تون شما"
    " با قیمت ثابت.\n"
    "🔹 کیف پول شخصی: خرید تون و انتقال به آدرس کیف پول شخصی شما.\n"
    "🔹 موجودی تون: خرید تون و نگهداری به صورت موجودی داخل ربات.\n"
    "\n"
    "🔻 روش مورد نظر را انتخاب کنید:"
)


def crypto_menu_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("💎 خرید ارز تون", "crypto:ton", style=STYLE_PRIMARY, emoji_key="crypto"),
         btn("خرید ارز ترون", "crypto:trx", style=STYLE_PRIMARY)],
        [btn("🐶 خرید ارز داگز", "crypto:dogs", style=STYLE_PRIMARY),
         btn("خرید ارز تتر", "crypto:usdt", style=STYLE_PRIMARY)],
        [btn("بــرگـشـت", "nav:shop")],
    ])


def ton_menu_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("فعالسازی ولت تلگرام", "crypto:ton_wallet", style=STYLE_SUCCESS),
         btn("واریز به ولت شخصی", "crypto:ton_personal", style=STYLE_PRIMARY)],
        [btn("شارژ مستقیم اکانت تلگرام ( تون بالانس)", "crypto:ton_balance",
             style=STYLE_PRIMARY)],
        [btn("بــرگـشـت", "shop:crypto")],
    ])


@router.callback_query(F.data == "shop:crypto")
async def crypto_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    try:
        await call.message.edit_text(CRYPTO_INTRO, reply_markup=crypto_menu_kb())
    except TelegramBadRequest:
        await call.message.answer(CRYPTO_INTRO, reply_markup=crypto_menu_kb())


@router.callback_query(F.data == "crypto:ton")
async def crypto_ton(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await call.message.answer(TON_INTRO, reply_markup=ton_menu_kb())


@router.callback_query(F.data == "crypto:calc")
async def crypto_calc(call: CallbackQuery, state: FSMContext):
    """«محاسبه با موجودی من»: how much TON the wallet balance buys."""
    user = get_user(call.from_user.id)
    unit = price("ton_balance_price")
    amount = (user["balance"] / unit) if unit else 0
    await call.answer(
        f"👛 موجودی شما: {fmt(user['balance'])} تومان\n"
        f"💎 معادل تقریبی: {amount:.4f} تون (با نرخ موجودی تون)",
        show_alert=True)


CRYPTO_SPECS = {
    # kind -> (title, unit price setting, min qty, fee note)
    "trx": ("🔴 ترون (TRX)", "trx_price", 1.5, "➕ کارمزد انتقال: 1 TRX"),
    "dogs": ("🐶 داگز (DOGS)", "dogs_price", 5000, ""),
    "usdt": ("💵 تتر (USDT)", "usdt_price", 1, ""),
    "ton_personal": ("💎 تون‌کوین — کیف پول شخصی", "ton_personal_price", 0.1, ""),
    "ton_balance": ("💎 تون‌کوین — موجودی تون", "ton_balance_price", 1, ""),
}


@router.callback_query(F.data.startswith("crypto:"))
async def crypto_pick(call: CallbackQuery, state: FSMContext):
    kind = call.data.split(":", 1)[1]
    if kind == "ton_wallet":
        await call.answer()
        # Fixed total price for the 0.1 TON activation: store it as a
        # per-unit price of (fixed / 0.1) so qty*unit == the fixed price.
        unit = price("ton_wallet_activation") / 0.1
        await state.set_data({"kind": "ton_wallet",
                              "title": "💎 فعال‌سازی کیف پول تون (0.1 TON)",
                              "qty": 0.1, "qty_label": "0.1 TON",
                              "unit_price": unit})
        await state.set_state(Shop.recipient)
        await call.message.answer(
            "👛 آدرس کیف پول تون خود را ارسال کنید تا 0.1 تون فعال‌سازی "
            "به آن واریز شود:")
        return
    if kind not in CRYPTO_SPECS:
        return
    title, key, min_qty, fee = CRYPTO_SPECS[kind]
    await call.answer()
    await state.set_data({"kind": kind, "unit_price": price(key),
                          "min_qty": min_qty})
    await state.set_state(Shop.crypto_qty)
    text = (
        f"{title}\n"
        "\n"
        f"💰 قیمت هر واحد: {fmt(price(key))} تومان\n"
        f"🔢 حداقل خرید: {fmt(min_qty)}\n"
    )
    if fee:
        text += f"{fee}\n"
    if kind == "ton_personal":
        text += "\n📝 در مرحله بعد آدرس کیف پول شما خواسته می‌شود."
    text += "\n✍️ مقدار مورد نظر را ارسال کنید:"
    await call.message.answer(text, reply_markup=back_kb())


@router.message(Shop.crypto_qty, F.text)
async def crypto_qty_input(message: Message, state: FSMContext):
    raw = (message.text or "").strip().replace(",", ".")
    try:
        qty = float(raw)
    except ValueError:
        await message.answer("❌ لطفاً مقدار را به عدد ارسال کنید.\n📌 مثال: 2.5")
        return
    data = await state.get_data()
    if qty < data["min_qty"]:
        await message.answer(
            f"❌ حداقل خرید این ارز {fmt(data['min_qty'])} واحد است!\n"
            f"🔢 مقدار وارد شده: {fmt(qty)}")
        return
    titles = {k: v[0] for k, v in CRYPTO_SPECS.items()}
    fee_qty = 1 if data["kind"] == "trx" else 0  # +1 TRX transfer fee
    data.update(qty=qty + fee_qty,
                qty_label=f"{fmt(qty)} واحد" + (" + 1 کارمزد" if fee_qty else ""),
                title=titles[data["kind"]])
    await state.set_data(data)
    if data["kind"] == "ton_personal":
        await state.set_state(Shop.recipient)
        await message.answer(
            "👛 آدرس کیف پول تون شخصی خود را ارسال کنید:")
    elif data["kind"] == "ton_balance":
        data["recipient"] = "موجودی تون داخل ربات"
        await state.set_data(data)
        await after_recipient(message, state, data["recipient"])
    else:
        await state.set_state(Shop.recipient)
        await message.answer(RECIPIENT_TEXT,
                             reply_markup=recipient_kb(message.from_user.first_name))


# ---------------------------------------------------------------------------
# Gifts (§9) — 11 gifts, prices in Stars
# ---------------------------------------------------------------------------

GIFTS = [
    ("💝", "قلب", 15), ("🧸", "تدی", 15),
    ("🎁", "کادو", 25), ("🌹", "گل رز", 25),
    ("🎂", "کیک", 50), ("💐", "گل", 50),
    ("🍾", "بطری", 50), ("🚀", "سفینه", 50),
    ("🏆", "جام", 100), ("💍", "حلقه", 100),
    ("💎", "الماس", 100),
]


def gifts_kb() -> InlineKeyboardMarkup:
    rows, row = [], []
    for idx, (emoji, name, stars) in enumerate(GIFTS):
        row.append(btn(f"{emoji} گیفت {name} ({stars} ⭐️)", f"gift:{idx}",
                       style=STYLE_SUCCESS, emoji_key="gift"))
        if len(row) == 2:
            rows.append(row)
            row = []
    if row:
        rows.append(row)
    rows.append([btn("بــرگـشـت", "nav:shop")])
    return ikb(rows)


def gift_qty_kb(qty: int) -> InlineKeyboardMarkup:
    return ikb([
        [btn("➖ کم کردن", "gqty:-", style=STYLE_DANGER),
         btn(f"{qty}", "gqty:noop", style=STYLE_PRIMARY),
         btn("➕ اضافه کردن", "gqty:+", style=STYLE_SUCCESS)],
        [btn("ادامه", "gqty:go", style=STYLE_SUCCESS)],
        [btn("بــرگـشـت", "shop:gifts")],
    ])


@router.callback_query(F.data == "shop:gifts")
async def gifts_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await call.message.answer(
        "🎁 هدیه دادن گیفت ، تجربهای خاص در تلگرام!\n"
        "\n"
        "🌟 با گیفتهای استارزی میتونی دوستان، خانواده یا معشوقت رو شگفتزده کنی!\n"
        "\n"
        "💫 مزایای گیفتهای استارزی:\n"
        "• 🎁 ارسال هدیه به دوستان و آشنایان برای سوپرایز کردن\n"
        "• 👾 قابل نمایش روی پروفایل تلگرام\n"
        "\n"
        "💱 لطفاً دستهبندی گیفت مورد نظر خود را انتخاب کنید:",
        reply_markup=ikb([
            [btn("گیفت های عادی", "gifts:normal", style=STYLE_PRIMARY)],
            [btn("بــرگـشـت", "nav:shop")],
        ]))


@router.callback_query(F.data == "gifts:normal")
async def gifts_normal(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await call.message.answer(
        "🎁 گیفت های عادی\n"
        "\n"
        "💫 لطفاً گیفت مورد نظر خود را انتخاب کنید:",
        reply_markup=gifts_kb())


@router.callback_query(F.data.startswith("gift:"))
async def gift_pick(call: CallbackQuery, state: FSMContext):
    idx = int(call.data.split(":")[1])
    emoji, name, stars = GIFTS[idx]
    unit = price("price_per_star")
    await call.answer()
    await state.set_data({"kind": "gift", "gift_idx": idx,
                          "gift_name": f"{emoji} {name}",
                          "gift_stars": stars, "gift_qty": 1})
    await call.message.answer(
        f"🎁 گیفت {emoji} {name}\n"
        f"⭐️ ارزش: {stars} استارز\n"
        f"💰 قیمت تقریبی هر عدد: {fmt(int(round(stars * unit)))} تومان\n"
        "\n"
        "🔢 تعداد را با دکمه‌های زیر مشخص کنید:",
        reply_markup=gift_qty_kb(1))


@router.callback_query(F.data.startswith("gqty:"))
async def gift_qty_buttons(call: CallbackQuery, state: FSMContext):
    action = call.data.split(":")[1]
    data = await state.get_data()
    qty = data.get("gift_qty", 1)
    if action == "+":
        qty = min(100, qty + 1)
    elif action == "-":
        qty = max(1, qty - 1)
    elif action == "noop":
        await call.answer(f"🔢 تعداد فعلی: {qty}")
        return
    elif action == "go":
        await call.answer()
        await state.set_state(Shop.recipient)
        await call.message.answer(
            RECIPIENT_TEXT
            + "\n\n🎁 برای گیفت می‌توانید از دکمه‌های زیر، مخفی‌کردن از پروفایل"
              " یا متن همراه (حداکثر ۱۲۸ کاراکتر، نوع «عادی» رایگان) را هم تنظیم کنید:",
            reply_markup=gift_recipient_kb(call.from_user.first_name))
        return
    data["gift_qty"] = qty
    await state.set_data(data)
    await call.answer()
    try:
        await call.message.edit_reply_markup(reply_markup=gift_qty_kb(qty))
    except TelegramBadRequest:
        pass


# Gift invoice extras: hide-from-profile toggle + comment (max 128 chars).
@router.callback_query(F.data.startswith("giftx:"))
async def gift_extras(call: CallbackQuery, state: FSMContext):
    """Toggle 'hide gift' / set comment before the gift invoice is paid.

    Kept as extra buttons attached on the recipient step for gifts; the
    flags live in FSM data and are copied into the order's extra field.
    """
    data = await state.get_data()
    if call.data == "giftx:hide":
        data["gift_hide"] = not data.get("gift_hide", False)
        await state.set_data(data)
        await call.answer(
            "🙈 گیفت از پروفایل گیرنده مخفی می‌شود." if data["gift_hide"]
            else "👁 گیفت در پروفایل گیرنده نمایش داده می‌شود.")
    elif call.data == "giftx:comment":
        await call.answer()
        await state.set_state(Shop.gift_comment)
        await call.message.answer(
            "✍️ متن همراه گیفت را ارسال کنید (حداکثر 128 کاراکتر):\n"
            "📌 نوع «عادی» رایگان است.")


@router.message(Shop.gift_comment, F.text)
async def gift_comment_input(message: Message, state: FSMContext):
    text = (message.text or "").strip()
    if len(text) > 128:
        await message.answer(
            f"❌ متن همراه گیفت حداکثر 128 کاراکتر است! (الان: {len(text)})\n"
            "دوباره ارسال کنید.")
        return
    data = await state.get_data()
    data["gift_comment"] = text
    data["extra"] = f"comment={text};hide={int(bool(data.get('gift_hide')))}"
    await state.set_data(data)
    await state.set_state(Shop.recipient)
    await message.answer("✅ متن همراه گیفت ثبت شد.")
    await message.answer(
        RECIPIENT_TEXT,
        reply_markup=gift_recipient_kb(message.from_user.first_name))


# ---------------------------------------------------------------------------
# Reactions (§10) & Boost (§11)
# ---------------------------------------------------------------------------

@router.callback_query(F.data == "shop:reactions")
async def reactions_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await state.set_data({"kind": "reaction"})
    await state.set_state(Shop.reaction_qty)
    await call.message.answer(REACTION_INTRO, reply_markup=back_kb())


@router.message(Shop.reaction_qty, F.text)
async def reaction_qty_input(message: Message, state: FSMContext):
    text = (message.text or "").strip()
    if not text.isdigit():
        await message.answer("❌ لطفاً فقط عدد ارسال کنید.\n📌 مثال: 100")
        return
    qty = int(text)
    if qty < 5:
        await message.answer(
            "❌ حداقل سفارش ری‌اکشن 5 عدد است!\n"
            f"🔢 تعداد وارد شده: {qty}")
        return
    data = await state.get_data()
    data["qty"] = qty
    await state.set_data(data)
    await state.set_state(Shop.reaction_link)
    await message.answer(REACTION_LINK_TEXT)


@router.message(Shop.reaction_link, F.text)
async def reaction_link_input(message: Message, state: FSMContext):
    link = (message.text or "").strip()
    if "t.me/" not in link:
        await message.answer(
            "❌ لینک معتبر نیست!\n📌 مثال: https://t.me/channel/123")
        return
    data = await state.get_data()
    data["recipient"] = link
    data["extra"] = f"post={link}"
    await state.set_data(data)
    await after_recipient(message, state, link)


@router.callback_query(F.data == "shop:boost")
async def boost_click(call: CallbackQuery, state: FSMContext):
    await call.answer(BOOST_DISABLED_TEXT, show_alert=True)


# ---------------------------------------------------------------------------
# Top-up (§12)
# ---------------------------------------------------------------------------

def topup_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("پــرداخـت با ارز دیـجـیــتال", "topup:crypto", style=STYLE_SUCCESS, emoji_key="crypto")],
        [btn("پــرداخـت ریـالـی", "topup:rial", style=STYLE_SUCCESS)],
        [btn("پــرداخـت درگـاهـی", "topup:gateway", style=STYLE_SUCCESS, emoji_key="wallet")],
        [btn("بــرگـشـت", "nav:main")],
    ])


async def show_topup(message: Message, state: FSMContext):
    user = ensure_user(message.from_user.id, message.from_user.full_name,
                       message.from_user.username)
    await message.answer(TOPUP_TEXT.format(balance=fmt(user["balance"])),
                         reply_markup=topup_kb())


@router.callback_query(F.data == "topup:rial")
async def topup_rial(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    # Rial gateway button in the original shows a maintenance alert, and the
    # rial flow itself requires a verified phone + approved bank card.
    if not user["verified"] or user["card_status"] != "approved":
        await call.answer()
        await call.message.answer(VERIFICATION_REQUIRED_TEXT,
                                  reply_markup=back_kb("nav:main"))
        return
    await call.answer(GATEWAY_MAINTENANCE_TEXT, show_alert=True)


@router.callback_query(F.data == "topup:gateway")
async def topup_gateway(call: CallbackQuery, state: FSMContext):
    # In the original the gateway is under maintenance (alert popup).
    await call.answer(GATEWAY_MAINTENANCE_TEXT, show_alert=True)


@router.callback_query(F.data == "topup:crypto")
async def topup_crypto_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await state.set_state(TopUp.crypto_amount)
    await call.message.answer(
        "🪙 افزایش موجودی با ارز دیجیتال\n"
        "\n"
        "✍️ مبلغ مورد نظر به تومان را ارسال کنید:\n"
        "📌 مثال: 500000")


@router.message(TopUp.crypto_amount, F.text)
async def topup_crypto_amount(message: Message, state: FSMContext):
    text = (message.text or "").strip().replace(",", "")
    if not text.isdigit() or int(text) < 10000:
        await message.answer(
            "❌ مبلغ معتبر نیست! حداقل مبلغ شارژ 10,000 تومان است.\n"
            "📌 مثال: 500000")
        return
    await state.update_data(topup_amount=int(text))
    await state.set_state(TopUp.crypto_currency)
    await message.answer(
        f"💵 مبلغ شارژ: {fmt(int(text))} تومان\n"
        "\n"
        "🔻 ارز پرداختی را انتخاب کنید:",
        reply_markup=ikb([
            [btn("🔴 ترون (TRX)", "topupcur:trx", style=STYLE_PRIMARY),
             btn("💎 تون‌کوین (TON)", "topupcur:ton", style=STYLE_PRIMARY)],
            [btn("بــرگـشـت", "nav:main")],
        ]))


@router.callback_query(F.data.startswith("topupcur:"))
async def topup_crypto_invoice(call: CallbackQuery, state: FSMContext):
    cur = call.data.split(":")[1]
    data = await state.get_data()
    amount_toman = data.get("topup_amount", 0)
    await state.clear()
    if cur == "trx":
        unit, symbol = price("trx_price"), "TRX"
    else:
        unit, symbol = price("ton_balance_price"), "TON"
    crypto_amount = (amount_toman / unit) if unit else 0
    address = get_setting("wallet_address")
    memo = str(call.from_user.id)  # per-user memo, as in the original
    tonkeeper_url = (
        f"https://app.tonkeeper.com/transfer/{address}"
        f"?amount={int(crypto_amount * 1e9)}&text={memo}") if symbol == "TON" else None
    rows = [[btn("🔍 بررسی پرداخت", "topup:check", style=STYLE_SUCCESS)]]
    if tonkeeper_url:
        rows.append([btn("💎 پرداخت با Tonkeeper", url=tonkeeper_url,
                         style=STYLE_PRIMARY)])
    rows.append([btn("بــرگـشـت", "nav:main")])
    await call.answer()
    await call.message.answer(
        "🧾 فاکتور شارژ کیف پول\n"
        "\n"
        f"💵 مبلغ: {fmt(amount_toman)} تومان\n"
        f"🪙 معادل: {crypto_amount:.6f} {symbol}\n"
        "\n"
        f"👛 آدرس واریز:\n<code>{address}</code>\n"
        f"🏷 ممو / کامنت (الزامی): <code>{memo}</code>\n"
        "\n"
        "⚠️ حتماً ممو را دقیق وارد کنید؛ بدون ممو شارژ قابل شناسایی نیست.\n"
        "✅ بعد از واریز، دکمه «بررسی پرداخت» را بزنید.",
        reply_markup=ikb(rows))


@router.callback_query(F.data == "topup:check")
async def topup_check(call: CallbackQuery, state: FSMContext):
    # TODO(stub): query the chain / payment provider for the deposit here.
    await call.answer(
        "⏳ پرداختی با ممو شما یافت نشد!\n"
        "اگر تازه واریز کرده‌اید، چند دقیقه صبر کنید و دوباره بررسی کنید.",
        show_alert=True)


# ---------------------------------------------------------------------------
# Account (§13) + challenges (dice / bowling)
# ---------------------------------------------------------------------------

def account_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("احراز هویت شماره ✓", "acc:phone", style=STYLE_SUCCESS),
         btn("مدیریت کارت 💳", "acc:card", style=STYLE_SUCCESS)],
        [btn("چالش 🎁", "acc:challenges", style=STYLE_SUCCESS),
         btn("سفارش های معلق من 📋", "acc:pending", style=STYLE_SUCCESS)],
        [btn("سفارش های اخیر من", "acc:orders", style=STYLE_SUCCESS)],
        [btn("بــرگـشـت", "nav:main")],
    ])


def profile_text(user) -> str:
    name = user["name"] or "—"
    username = f"@{user['username']}" if user["username"] else "—"
    phone = user["phone"] or "تایید نشده ❌"
    card = ("****" + user["card_number"][-4:]) if user["card_number"] else "ثبت نشده ❌"
    level = referral_level_name(user["ref_count"])
    return (
        "👤 حساب کاربری شما\n"
        "\n"
        f"📛 نام: {name}\n"
        f"🆔 آیدی عددی: <code>{user['id']}</code>\n"
        f"🔗 یوزرنیم: {username}\n"
        f"📱 شماره موبایل: {phone}\n"
        f"🏅 سطح کاربری: برنز\n"
        f"💳 کارت بانکی: {card}\n"
        f"👥 سطح زیرمجموعه‌گیری: {level} ({user['ref_count']} نفر)\n"
        f"⭐️ امتیاز: {fmt(user['points'])}\n"
        "\n"
        f"👛 موجودی اصلی: {fmt(user['balance'])} تومان\n"
        f"🎁 موجودی تخفیف: {fmt(user['discount_balance'])} تومان\n"
        f"💸 درآمد زیرمجموعه‌گیری: {fmt(user['ref_earnings'])} تومان"
    )


async def show_account(message: Message, state: FSMContext):
    user = ensure_user(message.from_user.id, message.from_user.full_name,
                       message.from_user.username)
    await message.answer(profile_text(user), reply_markup=account_kb())


@router.callback_query(F.data == "acc:phone")
async def account_phone(call: CallbackQuery, state: FSMContext):
    await call.answer()
    kb = ReplyKeyboardMarkup(
        keyboard=[[kbtn("📱 ارسال شماره موبایل", request_contact=True,
                        style=STYLE_SUCCESS)],
                  [kbtn("❌ لغو")]],
        resize_keyboard=True, one_time_keyboard=True)
    await call.message.answer(
        "📱 برای تایید شماره، دکمه زیر را بزنید و شماره تلگرام خود را به "
        "اشتراک بگذارید:", reply_markup=kb)


@router.message(F.contact)
async def contact_received(message: Message, state: FSMContext):
    if message.contact.user_id != message.from_user.id:
        await message.answer("❌ فقط شماره خودتان قابل قبول است!")
        return
    update_user(message.from_user.id, phone=message.contact.phone_number,
                verified=1)
    await state.clear()
    await message.answer(
        f"✅ شماره {message.contact.phone_number} با موفقیت تایید شد.",
        reply_markup=main_reply_kb())


@router.callback_query(F.data == "acc:card")
async def account_card(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    await call.answer()
    if user["card_number"]:
        status_fa = {"pending": "⏳ در انتظار تایید پشتیبانی",
                     "approved": "✅ تایید شده",
                     "rejected": "❌ رد شده"}.get(user["card_status"], "—")
        await call.message.answer(
            "💳 کارت بانکی شما\n"
            "\n"
            f"🔢 شماره کارت: ****{user['card_number'][-4:]}\n"
            f"📌 وضعیت: {status_fa}",
            reply_markup=ikb([
                [btn("✏️ تغییر کارت", "acc:card_add", style=STYLE_PRIMARY)],
                [btn("بــرگـشـت", "nav:main")]]))
    else:
        await call.message.answer(
            "💳 مدیریت کارت بانکی\n"
            "\n"
            "❌ هنوز کارت بانکی ثبت نکرده‌اید.\n"
            "📌 برای پرداخت ریالی و برداشت درآمد، ثبت کارت به نام خودتان الزامی است.",
            reply_markup=ikb([
                [btn("➕ افزودن کارت", "acc:card_add", style=STYLE_SUCCESS)],
                [btn("بــرگـشـت", "nav:main")]]))


@router.callback_query(F.data == "acc:card_add")
async def card_add_start(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await state.set_state(Account.card_number)
    await call.message.answer(
        "💳 شماره کارت ۱۶ رقمی خود را ارسال کنید:\n"
        "📌 کارت باید به نام خودتان باشد.")


@router.message(Account.card_number, F.text)
async def card_number_input(message: Message, state: FSMContext):
    num = (message.text or "").strip().replace(" ", "")
    if not num.isdigit() or len(num) != 16:
        await message.answer(
            "❌ شماره کارت معتبر نیست! شماره کارت باید ۱۶ رقم باشد.\n"
            "دوباره ارسال کنید یا /cancel را بزنید.")
        return
    await state.clear()
    update_user(message.from_user.id, card_number=num, card_status="pending")
    await message.answer(
        "✅ کارت شما ثبت شد و در انتظار تایید پشتیبانی است.\n"
        f"🔢 شماره کارت: ****{num[-4:]}\n"
        "🎧 نتیجه بررسی از طریق همین ربات اطلاع‌رسانی می‌شود.",
        reply_markup=main_reply_kb())


async def _orders_list(call: CallbackQuery, statuses: tuple, empty_text: str):
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM orders WHERE user_id=? AND status IN ({})"
            " ORDER BY id DESC LIMIT 10".format(
                ",".join("?" * len(statuses))),
            (call.from_user.id, *statuses)).fetchall()
    if not rows:
        await call.message.answer(empty_text, reply_markup=back_kb("nav:main"))
        return
    lines = []
    for r in rows:
        lines.append(
            f"📦 {r['title']}\n"
            f"💵 {fmt(r['total'])} تومان | 🔎 <code>{r['tracking_code']}</code>"
            f" | 📌 {r['status']}")
    await call.message.answer("\n\n".join(lines), reply_markup=back_kb("nav:main"))


@router.callback_query(F.data == "acc:orders")
async def account_orders(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await _orders_list(call, ("delivered", "paid", "pending", "rejected"),
                       "🧾 هنوز سفارشی ثبت نکرده‌اید.")


@router.callback_query(F.data == "acc:pending")
async def account_pending(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await _orders_list(call, ("pending", "paid"),
                       "⏳ سفارش در انتظاری ندارید.")


# --- Challenges -------------------------------------------------------------
# Prize maps are approximate, documented from the exploration report:
# dice roll 2 -> 1,000 Toman was observed; the exploration test account
# gained 2,500 Toman of discount balance across the dice/bowling tests.
DICE_PRIZES = {1: 0, 2: 1000, 3: 1500, 4: 2500, 5: 4000, 6: 7500}
BOWLING_PRIZES = {1: 0, 2: 500, 3: 1000, 4: 1500, 5: 2500, 6: 5000}


def challenges_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("🎲 چالش تاس", "ch:dice", style=STYLE_SUCCESS),
         btn("🎳 چالش بولینگ", "ch:bowling", style=STYLE_SUCCESS)],
        [btn("بــرگـشـت", "nav:main")],
    ])


@router.callback_query(F.data == "acc:challenges")
async def challenges_menu(call: CallbackQuery, state: FSMContext):
    await call.answer()
    await call.message.answer(
        "🎲 چالش‌های روزانه ابوالفضل 🎳\n"
        "\n"
        "🎯 هر روز در هر بازی یک شانس رایگان دارید!\n"
        "🎁 جوایز به موجودی انتخابی شما اضافه می‌شود.\n"
        "📌 نکته: اگر جایزه را روی «موجودی تخفیف» بگیرید، محدودیت کول‌داون "
        "بین بازی‌ها برای شما اعمال نمی‌شود.\n"
        "\n"
        "🔻 بازی را انتخاب کنید:",
        reply_markup=challenges_kb())


@router.callback_query(F.data.startswith("ch:"))
async def challenge_pick_game(call: CallbackQuery, state: FSMContext):
    game = call.data.split(":")[1]
    await call.answer()
    await call.message.answer(
        "🎁 جایزه به کدام موجودی اضافه شود؟",
        reply_markup=ikb([
            [btn("👛 موجودی اصلی", f"chbal:{game}:balance", style=STYLE_PRIMARY),
             btn("🎁 موجودی تخفیف", f"chbal:{game}:discount_balance",
                 style=STYLE_SUCCESS)],
            [btn("بــرگـشـت", "acc:challenges")],
        ]))


@router.callback_query(F.data.startswith("chbal:"))
async def challenge_play(call: CallbackQuery, state: FSMContext):
    _, game, column = call.data.split(":")
    user = get_user(call.from_user.id)
    today = time.strftime("%Y-%m-%d")
    last_field = "dice_last" if game == "dice" else "bowling_last"
    # Daily per-game limit (stored on the user row).
    if user[last_field] == today:
        await call.answer(
            "⏳ شانس امروز این بازی را استفاده کرده‌اید!\n"
            "فردا دوباره تلاش کنید 🎲", show_alert=True)
        return
    update_user(call.from_user.id, **{last_field: today})
    await call.answer()
    emoji = "🎲" if game == "dice" else "🎳"
    dice_msg = await call.message.answer_dice(emoji=emoji)
    await asyncio.sleep(3.5)  # let the dice animation finish
    value = dice_msg.dice.value
    prizes = DICE_PRIZES if game == "dice" else BOWLING_PRIZES
    prize = prizes.get(value, 0)
    if prize > 0:
        add_balance(call.from_user.id, prize, column)
        dest = "موجودی اصلی" if column == "balance" else "موجودی تخفیف"
        await call.message.answer(
            f"🎉 تبریک! نتیجه: {value}\n"
            f"🎁 {fmt(prize)} تومان به {dest} شما اضافه شد.")
    else:
        await call.message.answer(
            f"😔 نتیجه: {value}\nاین بار جایزه‌ای نبردید؛ فردا دوباره شانس خود را امتحان کنید!")


# ---------------------------------------------------------------------------
# Referral (§14)
# ---------------------------------------------------------------------------

def ref_level_thresholds_text() -> str:
    return (
        "👥 زیرمجموعه‌گیری و کسب درآمد\n"
        "\n"
        "💸 از هر خرید زیرمجموعه‌های شما، کمیسیون دائمی دریافت می‌کنید:\n"
        "🥉 سطح برنزی (۰ تا ۹ زیرمجموعه): ۵٪\n"
        "🥈 سطح نقره‌ای (۱۰ تا ۲۹ زیرمجموعه): ۶٪\n"
        "🥇 سطح طلایی (۳۰ زیرمجموعه به بالا): ۸٪\n"
        "\n"
        "⭐️ با امتیازهای خود می‌توانید کد تخفیف اختصاصی بسازید (۳۰ امتیاز).\n"
        "💰 حداقل برداشت درآمد: 100,000 تومان"
    )


def referral_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("آمار تفصیلی 📊", "ref:stats", style=STYLE_PRIMARY),
         btn("دریافت جایزه رفرالها 🎁", "ref:withdraw", style=STYLE_PRIMARY)],
        [btn("ساخت کد تخفیف 🎫", "ref:create_code", style=STYLE_PRIMARY),
         btn("لیست کد تخفیفهای من 📋", "ref:my_codes", style=STYLE_PRIMARY)],
        [btn("ساخت بنر رفرال 🖼️", "ref:banner", style=STYLE_PRIMARY)],
        [btn("بــرگـشـت", "nav:main")],
    ])


async def show_referral(message: Message, state: FSMContext):
    user = ensure_user(message.from_user.id, message.from_user.full_name,
                       message.from_user.username)
    link = f"https://t.me/{BOT_USERNAME}?start=ref_{user['id']}"
    await message.answer(
        ref_level_thresholds_text()
        + f"\n\n🔗 لینک دعوت اختصاصی شما:\n{link}",
        reply_markup=referral_kb())


@router.callback_query(F.data == "ref:stats")
async def referral_stats(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    await call.answer()
    await call.message.answer(
        "📊 آمار زیرمجموعه‌گیری شما\n"
        "\n"
        f"👥 تعداد زیرمجموعه‌ها: {user['ref_count']} نفر\n"
        f"🏅 سطح فعلی: {referral_level_name(user['ref_count'])} "
        f"({referral_percent(user['ref_count'])}٪)\n"
        f"💸 درآمد قابل برداشت: {fmt(user['ref_earnings'])} تومان\n"
        f"💰 مجموع درآمد کسب‌شده: {fmt(user['total_ref_earned'])} تومان\n"
        f"⭐️ امتیاز: {fmt(user['points'])}",
        reply_markup=back_kb("nav:main"))


@router.callback_query(F.data == "ref:banner")
async def referral_banner(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    link = f"https://t.me/{BOT_USERNAME}?start=ref_{user['id']}"
    await call.answer()
    # TODO(stub): render a real image banner here; text banner for now.
    await call.message.answer(
        "🖼 بنر دعوت شما\n"
        "━━━━━━━━━━━━━━━\n"
        "🌟 ابوالفضل 🌟\n"
        "⭐️ خرید آنی استارز و پرمیوم تلگرام\n"
        "🎁 همین حالا عضو شو و جایزه بگیر!\n"
        f"🔗 {link}\n"
        "━━━━━━━━━━━━━━━\n"
        "📌 این متن را کپی و برای دوستانتان ارسال کنید.")


@router.callback_query(F.data == "ref:withdraw")
async def referral_withdraw(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    if user["ref_earnings"] < 100000:
        await call.answer(
            "❌ حداقل مبلغ برداشت 100,000 تومان است!\n"
            f"💸 درآمد فعلی شما: {fmt(user['ref_earnings'])} تومان",
            show_alert=True)
        return
    await call.answer()
    await state.set_state(Referral.withdraw_amount)
    await call.message.answer(
        f"💰 برداشت درآمد\n\n💸 موجودی قابل برداشت: {fmt(user['ref_earnings'])} تومان\n"
        "✍️ مبلغ برداشت را به تومان ارسال کنید:")


@router.message(Referral.withdraw_amount, F.text)
async def withdraw_amount_input(message: Message, state: FSMContext):
    text = (message.text or "").strip().replace(",", "")
    user = get_user(message.from_user.id)
    if not text.isdigit():
        await message.answer("❌ مبلغ را به عدد ارسال کنید.")
        return
    amount = int(text)
    if amount < 100000:
        await message.answer("❌ حداقل مبلغ برداشت 100,000 تومان است!")
        return
    if amount > user["ref_earnings"]:
        await message.answer(insufficient_text(amount, user["ref_earnings"]))
        return
    await state.clear()
    add_balance(message.from_user.id, -amount, "ref_earnings")
    # TODO(stub): real payout to the user's registered card goes here.
    for admin_id in ADMIN_IDS:
        try:
            await message.bot.send_message(
                admin_id,
                "💸 درخواست برداشت درآمد\n"
                f"👤 کاربر: {message.from_user.id}\n"
                f"💰 مبلغ: {fmt(amount)} تومان")
        except Exception:
            pass
    await message.answer(
        "✅ درخواست برداشت شما ثبت شد.\n"
        f"💰 مبلغ: {fmt(amount)} تومان\n"
        "🎧 مبلغ پس از بررسی پشتیبانی به کارت ثبت‌شده شما واریز می‌شود.",
        reply_markup=main_reply_kb())


@router.callback_query(F.data == "ref:create_code")
async def referral_create_code(call: CallbackQuery, state: FSMContext):
    user = get_user(call.from_user.id)
    if user["points"] < 30:
        await call.answer(
            "❌ برای ساخت کد تخفیف به 30 امتیاز نیاز دارید!\n"
            f"⭐️ امتیاز فعلی شما: {fmt(user['points'])}",
            show_alert=True)
        return
    await call.answer()
    await state.set_state(Referral.code_name)
    await call.message.answer(
        "🎟 ساخت کد تخفیف اختصاصی\n"
        "\n"
        "✍️ نام کد را ارسال کنید (انگلیسی، بدون فاصله):\n"
        "📌 مثال: MyCode20")


@router.message(Referral.code_name, F.text)
async def ref_code_name(message: Message, state: FSMContext):
    code = (message.text or "").strip()
    if len(code) < 4 or " " in code:
        await message.answer("❌ نام کد معتبر نیست! حداقل ۴ کاراکتر و بدون فاصله.")
        return
    with closing(db()) as conn:
        exists = conn.execute(
            "SELECT 1 FROM discount_codes WHERE code=?", (code,)).fetchone()
    if exists:
        await message.answer("❌ این کد قبلاً ثبت شده است؛ نام دیگری انتخاب کنید.")
        return
    await state.update_data(new_code=code)
    await state.set_state(Referral.code_percent)
    await message.answer("🎟 درصد تخفیف را ارسال کنید (۵ تا ۵۰):")


@router.message(Referral.code_percent, F.text)
async def ref_code_percent(message: Message, state: FSMContext):
    text = (message.text or "").strip()
    if not text.isdigit() or not (5 <= int(text) <= 50):
        await message.answer("❌ درصد باید عددی بین ۵ تا ۵۰ باشد.")
        return
    data = await state.get_data()
    await state.clear()
    code, percent = data["new_code"], int(text)
    with closing(db()) as conn:
        conn.execute(
            "INSERT INTO discount_codes(code,percent,active,owner_id)"
            " VALUES(?,?,1,?)", (code, percent, message.from_user.id))
        conn.execute(
            "INSERT INTO user_discount_codes(user_id,code,percent,created_at)"
            " VALUES(?,?,?,?)",
            (message.from_user.id, code, percent, int(time.time())))
        conn.commit()
    add_balance(message.from_user.id, -30, "points")
    await message.answer(
        f"✅ کد تخفیف «{code}» با {percent}٪ تخفیف ساخته شد!\n"
        "⭐️ ۳۰ امتیاز از حساب شما کسر شد.",
        reply_markup=main_reply_kb())


@router.callback_query(F.data == "ref:my_codes")
async def referral_my_codes(call: CallbackQuery, state: FSMContext):
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM user_discount_codes WHERE user_id=? ORDER BY id DESC",
            (call.from_user.id,)).fetchall()
    await call.answer()
    if not rows:
        await call.message.answer(
            "🎟 هنوز کد تخفیفی نساخته‌اید.\n"
            "⭐️ با ۳۰ امتیاز می‌توانید از بخش زیرمجموعه‌گیری کد اختصاصی بسازید.",
            reply_markup=back_kb("nav:main"))
        return
    lines = ["🎟 کدهای تخفیف شما:\n"]
    for r in rows:
        lines.append(f"🔹 <code>{r['code']}</code> — {r['percent']}٪ تخفیف")
    await call.message.answer("\n".join(lines), reply_markup=back_kb("nav:main"))


# ---------------------------------------------------------------------------
# Admin panel (/admin)
# ---------------------------------------------------------------------------

def is_admin(user_id: int) -> bool:
    return user_id in ADMIN_IDS


def admin_kb() -> InlineKeyboardMarkup:
    return ikb([
        [btn("📊 آمار کلی", "adm:stats", style=STYLE_PRIMARY),
         btn("🧾 سفارش‌های در انتظار", "adm:pending", style=STYLE_PRIMARY)],
        [btn("⚙️ تنظیمات قیمت‌ها", "adm:settings", style=STYLE_PRIMARY),
         btn("🎟 کدهای تخفیف", "adm:codes", style=STYLE_PRIMARY)],
        [btn("👥 کاربران", "adm:users", style=STYLE_PRIMARY),
         btn("💰 تنظیم موجودی کاربر", "adm:adjust", style=STYLE_PRIMARY)],
        [btn("📣 ارسال همگانی", "adm:broadcast", style=STYLE_SUCCESS)],
        [btn("🔙 خروج", "nav:main")],
    ])


@router.message(Command("admin"))
async def cmd_admin(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    await state.clear()
    await message.answer("👑 پنل مدیریت ابوالفضل", reply_markup=admin_kb())


@router.callback_query(F.data == "adm:stats")
async def admin_stats(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    with closing(db()) as conn:
        users = conn.execute("SELECT COUNT(*) c FROM users").fetchone()["c"]
        orders = conn.execute("SELECT COUNT(*) c FROM orders").fetchone()["c"]
        pending = conn.execute(
            "SELECT COUNT(*) c FROM orders WHERE status='pending'"
        ).fetchone()["c"]
        delivered = conn.execute(
            "SELECT COUNT(*) c FROM orders WHERE status='delivered'"
        ).fetchone()["c"]
        revenue = conn.execute(
            "SELECT COALESCE(SUM(total),0) s FROM orders"
            " WHERE status='delivered'").fetchone()["s"]
    await call.answer()
    await call.message.answer(
        "📊 آمار کلی ربات\n"
        "\n"
        f"👥 کاربران: {fmt(users)}\n"
        f"🧾 کل سفارش‌ها: {fmt(orders)}\n"
        f"⏳ در انتظار: {fmt(pending)}\n"
        f"🎉 تحویل‌شده: {fmt(delivered)}\n"
        f"💰 درآمد (تحویل‌شده): {fmt(revenue)} تومان",
        reply_markup=admin_kb())


@router.callback_query(F.data == "adm:pending")
async def admin_pending(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM orders WHERE status IN ('pending','paid')"
            " ORDER BY id DESC LIMIT 10").fetchall()
    await call.answer()
    if not rows:
        await call.message.answer("⏳ سفارش در انتظاری وجود ندارد.",
                                  reply_markup=admin_kb())
        return
    for r in rows:
        await call.message.answer(
            f"🧾 سفارش <code>{r['tracking_code']}</code>\n"
            f"📦 {r['title']}\n"
            f"👤 کاربر: {r['user_id']} | 👤 گیرنده: {r['recipient']}\n"
            f"💵 {fmt(r['total'])} تومان | 📌 {r['status']}",
            reply_markup=ikb([[
                btn("✅ تایید و تحویل", f"admok:{r['tracking_code']}",
                    style=STYLE_SUCCESS),
                btn("❌ رد و بازگشت وجه", f"admno:{r['tracking_code']}",
                    style=STYLE_DANGER),
            ]]))


@router.callback_query(F.data.startswith("admok:"))
async def admin_approve(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    code = call.data.split(":", 1)[1]
    await call.answer("✅ سفارش تایید و تحویل شد.")
    await deliver_order(call.bot, code)
    with closing(db()) as conn:
        order = conn.execute(
            "SELECT * FROM orders WHERE tracking_code=?", (code,)).fetchone()
    if order is not None:
        try:
            await call.bot.send_message(
                order["user_id"],
                "🎉 سفارش شما تحویل داده شد!\n"
                f"🔎 کد رهگیری: <code>{code}</code>\n"
                f"📦 {order['title']}")
        except Exception:
            pass
    try:
        await call.message.edit_text(call.message.text + "\n\n✅ تحویل شد.")
    except TelegramBadRequest:
        pass


@router.callback_query(F.data.startswith("admno:"))
async def admin_reject(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    code = call.data.split(":", 1)[1]
    with closing(db()) as conn:
        order = conn.execute(
            "SELECT * FROM orders WHERE tracking_code=?", (code,)).fetchone()
    if order is None or order["status"] in ("rejected", "refunded"):
        await call.answer("❌ سفارش یافت نشد یا قبلاً بسته شده است.",
                          show_alert=True)
        return
    set_order_status(code, "refunded")
    add_balance(order["user_id"], int(order["total"]), "balance")
    await call.answer("↩️ سفارش رد و مبلغ به کیف پول کاربر برگشت.")
    try:
        await call.bot.send_message(
            order["user_id"],
            "❌ سفارش شما رد شد و مبلغ به کیف پولتان بازگشت.\n"
            f"🔎 کد رهگیری: <code>{code}</code>\n"
            f"💵 مبلغ بازگشتی: {fmt(order['total'])} تومان")
    except Exception:
        pass


SETTING_LABELS = [
    ("price_per_star", "⭐️ قیمت هر استارز (تومان)"),
    ("premium_3", "💎 پرمیوم ۳ ماهه"),
    ("premium_6", "💎 پرمیوم ۶ ماهه"),
    ("premium_12", "💎 پرمیوم ۱۲ ماهه"),
    ("ton_wallet_activation", "💎 فعال‌سازی کیف پول تون (ثابت)"),
    ("ton_personal_price", "💎 تون — کیف پول شخصی (هر واحد)"),
    ("ton_balance_price", "💎 تون — موجودی تون (هر واحد)"),
    ("trx_price", "🔴 ترون (هر واحد)"),
    ("dogs_price", "🐶 داگز (هر واحد)"),
    ("usdt_price", "💵 تتر (هر واحد)"),
    ("wallet_address", "👛 آدرس کیف پول شارژ"),
    ("wallet_memo", "🏷 ممو پیش‌فرض"),
    ("required_channels", "📢 کانال‌های عضویت اجباری (با کاما)"),
    ("gate_enabled", "🔒 عضویت اجباری (1=فعال، 0=غیرفعال)"),
]


@router.callback_query(F.data == "adm:settings")
async def admin_settings(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    await call.answer()
    rows = [[btn(f"{label}: {get_setting(key)}", f"admset:{key}",
                 style=STYLE_PRIMARY)] for key, label in SETTING_LABELS]
    rows.append([btn("بــرگـشـت", "adm:home")])
    await call.message.answer("⚙️ تنظیمات — برای ویرایش روی هر مورد بزنید:",
                              reply_markup=ikb(rows))


@router.callback_query(F.data == "adm:home")
async def admin_home(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    await call.answer()
    await call.message.answer("👑 پنل مدیریت ابوالفضل", reply_markup=admin_kb())


@router.callback_query(F.data.startswith("admset:"))
async def admin_setting_pick(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    key = call.data.split(":", 1)[1]
    label = dict(SETTING_LABELS).get(key, key)
    await call.answer()
    await state.set_state(Admin.setting_value)
    await state.update_data(setting_key=key)
    await call.message.answer(
        f"⚙️ ویرایش «{label}»\nمقدار فعلی: <code>{get_setting(key)}</code>\n"
        "✍️ مقدار جدید را ارسال کنید:")


@router.message(Admin.setting_value, F.text)
async def admin_setting_value(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    set_setting(data["setting_key"], (message.text or "").strip())
    await message.answer(
        f"✅ تنظیم «{data['setting_key']}» به‌روزرسانی شد: "
        f"<code>{(message.text or '').strip()}</code>",
        reply_markup=admin_kb())


@router.callback_query(F.data == "adm:codes")
async def admin_codes(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM discount_codes ORDER BY code").fetchall()
    await call.answer()
    lines = ["🎟 کدهای تخفیف:\n"]
    kb_rows = []
    for r in rows:
        lines.append(
            f"🔹 <code>{r['code']}</code> — {r['percent']}٪ "
            f"({'فعال' if r['active'] else 'غیرفعال'})")
        kb_rows.append([btn(f"🗑 حذف {r['code']}", f"admdel:{r['code']}",
                            style=STYLE_DANGER)])
    kb_rows.append([btn("➕ افزودن کد", "adm:addcode", style=STYLE_SUCCESS)])
    kb_rows.append([btn("بــرگـشـت", "adm:home")])
    await call.message.answer("\n".join(lines), reply_markup=ikb(kb_rows))


@router.callback_query(F.data.startswith("admdel:"))
async def admin_code_delete(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    code = call.data.split(":", 1)[1]
    with closing(db()) as conn:
        conn.execute("DELETE FROM discount_codes WHERE code=?", (code,))
        conn.commit()
    await call.answer(f"🗑 کد «{code}» حذف شد.")


@router.callback_query(F.data == "adm:addcode")
async def admin_code_add(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    await call.answer()
    await state.set_state(Admin.discount_add)
    await call.message.answer(
        "🎟 افزودن کد تخفیف\n✍️ به صورت «کد:درصد» ارسال کنید.\n📌 مثال: Sale20:20")


@router.message(Admin.discount_add, F.text)
async def admin_code_add_input(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    raw = (message.text or "").strip()
    if ":" not in raw:
        await message.answer("❌ فرمت اشتباه است. مثال: Sale20:20")
        return
    code, _, pct = raw.partition(":")
    if not pct.strip().isdigit():
        await message.answer("❌ درصد باید عدد باشد. مثال: Sale20:20")
        return
    await state.clear()
    with closing(db()) as conn:
        conn.execute(
            "INSERT OR REPLACE INTO discount_codes(code,percent,active,owner_id)"
            " VALUES(?,?,1,0)", (code.strip(), int(pct)))
        conn.commit()
    await message.answer(f"✅ کد «{code.strip()}» با {pct.strip()}٪ تخفیف ثبت شد.",
                         reply_markup=admin_kb())


@router.callback_query(F.data == "adm:users")
async def admin_users(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM users ORDER BY id DESC LIMIT 15").fetchall()
    await call.answer()
    lines = ["👥 آخرین کاربران:\n"]
    for r in rows:
        uname = f"@{r['username']}" if r["username"] else "—"
        lines.append(
            f"🔹 <code>{r['id']}</code> {r['name']} ({uname})\n"
            f"    👛 {fmt(r['balance'])} | 🎁 {fmt(r['discount_balance'])}"
            f" | 👥 {r['ref_count']} زیرمجموعه")
    await call.message.answer("\n".join(lines), reply_markup=admin_kb())


@router.callback_query(F.data == "adm:adjust")
async def admin_adjust_start(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    await call.answer()
    await state.set_state(Admin.user_adjust_id)
    await call.message.answer(
        "💰 تنظیم موجودی\n✍️ آیدی عددی کاربر را ارسال کنید:")


@router.message(Admin.user_adjust_id, F.text)
async def admin_adjust_id(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    text = (message.text or "").strip()
    if not text.lstrip("-").isdigit() or get_user(int(text)) is None:
        await message.answer("❌ کاربر یافت نشد؛ آیدی عددی معتبر ارسال کنید.")
        return
    await state.update_data(adjust_uid=int(text))
    await state.set_state(Admin.user_adjust_amount)
    await message.answer(
        "✍️ مبلغ را ارسال کنید (مثبت = افزایش، منفی = کاهش).\n📌 مثال: 50000 یا -20000")


@router.message(Admin.user_adjust_amount, F.text)
async def admin_adjust_amount(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    text = (message.text or "").strip().replace(",", "")
    if not text.lstrip("-").isdigit():
        await message.answer("❌ مبلغ معتبر نیست.")
        return
    data = await state.get_data()
    await state.clear()
    add_balance(data["adjust_uid"], int(text), "balance")
    user = get_user(data["adjust_uid"])
    await message.answer(
        f"✅ موجودی کاربر {data['adjust_uid']} به‌روزرسانی شد.\n"
        f"👛 موجودی جدید: {fmt(user['balance'])} تومان",
        reply_markup=admin_kb())


@router.callback_query(F.data == "adm:broadcast")
async def admin_broadcast_start(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    await call.answer()
    await state.set_state(Admin.broadcast_text)
    await call.message.answer("📣 متن پیام همگانی را ارسال کنید:")


@router.message(Admin.broadcast_text, F.text)
async def admin_broadcast_text(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    await state.update_data(broadcast=message.text)
    await state.set_state(Admin.broadcast_confirm)
    await message.answer(
        "📣 پیش‌نمایش پیام همگانی:\n\n" + message.text,
        reply_markup=ikb([[
            btn("✅ ارسال به همه", "adm:send", style=STYLE_SUCCESS),
            btn("❌ لغو", "adm:home", style=STYLE_DANGER)]]))


@router.callback_query(F.data == "adm:send")
async def admin_broadcast_send(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        return
    data = await state.get_data()
    text = data.get("broadcast", "")
    await state.clear()
    with closing(db()) as conn:
        ids = [r["id"] for r in conn.execute("SELECT id FROM users").fetchall()]
    ok = fail = 0
    for uid in ids:
        try:
            await call.bot.send_message(uid, text)
            ok += 1
        except Exception:
            fail += 1
        await asyncio.sleep(0.05)
    await call.answer()
    await call.message.answer(
        f"📣 ارسال همگانی تمام شد.\n✅ موفق: {ok}\n❌ ناموفق: {fail}",
        reply_markup=admin_kb())


# ---------------------------------------------------------------------------
# Fallback: unknown text while idle
# ---------------------------------------------------------------------------

@router.message(StateFilter(None), F.text)
async def fallback_text(message: Message, state: FSMContext):
    if not await require_member(message, state):
        return
    ensure_user(message.from_user.id, message.from_user.full_name,
                message.from_user.username)
    await message.answer(
        "🤔 این گزینه را پیدا نکردم!\nاز منوی زیر یکی را انتخاب کنید 👇",
        reply_markup=main_reply_kb())


# ---------------------------------------------------------------------------
# main()
# ---------------------------------------------------------------------------

async def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN is not set — copy .env.example to .env "
                         "and put your bot token there.")
    init_db()
    from aiogram.client.default import DefaultBotProperties
    bot = Bot(BOT_TOKEN, default=DefaultBotProperties(parse_mode=ParseMode.HTML))
    global BOT_USERNAME
    me = await bot.get_me()
    BOT_USERNAME = me.username or "Abolfazl_Bot"
    dp = Dispatcher()
    dp.include_router(router)
    log.info("Starting replica as @%s (admins: %s)", BOT_USERNAME, ADMIN_IDS)
    await dp.start_polling(bot)


if __name__ == "__main__":
    asyncio.run(main())
