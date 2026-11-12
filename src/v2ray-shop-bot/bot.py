# Telegram: @Abolfazl_rwm
# -*- coding: utf-8 -*-
"""
Abolfazl V2Ray Shop — single-file shop bot («ویتوری رایگان | free v2ray»)
================================================================================
Single-file bot built with aiogram 3.x + SQLite (stdlib sqlite3).

A clean-room re-implementation of everything a normal user can SEE in a full shop bot:
forced-join gate, main menu, V2Ray / OpenVPN shop,
card-to-card payment with receipt approval, wallet (card / Telegram Stars /
gift code), free trial service, my-services with .ovpn config delivery,
connection guide, referral, support tickets, plus a full admin panel.

Real panel / Xray / OpenVPN provisioning is a TODO stub — see
generate_config_text(): it produces a placeholder .ovpn text that a real
deployment must replace with a config issued by the actual server panel.

Button colours — CRITICAL (observed in Telegram Web):
  Inline buttons render in a REPEATING CYCLE in button order on each screen:
      1st blue/teal  ≈ Bot API style "primary"
      2nd green      ≈ Bot API style "success"
      3rd brown/orange ≈ Bot API style "danger"
      4th blue, 5th green, 6th brown, ...
  So EVERY inline keyboard in this replica is built through styled_kb(),
  which assigns style = STYLE_CYCLE[index % 3] by flat button position.

Custom (premium) emojis:
  NO premium/custom emoji was observed anywhere in the original bot — all
  standard Unicode emoji. CUSTOM_EMOJI_IDS therefore ships EMPTY and labels
  keep their standard emoji prefixes. The btn() helper still accepts
  icon_custom_emoji_id so IDs can be wired later if ever needed.
"""

import asyncio
import logging
import os
import random
import sqlite3
import string
import time
from contextlib import closing
from datetime import datetime, timezone

from aiogram import Bot, Dispatcher, F, Router
from aiogram.enums import ParseMode
from aiogram.exceptions import TelegramBadRequest
from aiogram.filters import Command, CommandStart, StateFilter
from aiogram.fsm.context import FSMContext
from aiogram.fsm.state import State, StatesGroup
from aiogram.types import (
    BufferedInputFile,
    CallbackQuery,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    LabeledPrice,
    Message,
    PreCheckoutQuery,
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
BOT_USERNAME = os.environ.get("BOT_USERNAME", "Abolfazl_Bot")
ADMIN_IDS = {
    int(x) for x in os.environ.get("ADMIN_IDS", "").replace(";", ",").split(",")
    if x.strip().lstrip("-").isdigit()
}
DB_PATH = os.environ.get("DB_PATH", os.path.join(
    os.path.dirname(os.path.abspath(__file__)), "bot.db"))
FILES_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "files")

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s %(levelname)s %(name)s: %(message)s")
log = logging.getLogger("shopvip-replica")

# ---------------------------------------------------------------------------
# Custom emoji IDs — EMPTY (no premium emojis observed in the original).
# ---------------------------------------------------------------------------
CUSTOM_EMOJI_IDS = {
    # No premium/custom emoji anywhere in @Abolfazl_rwm — all standard
    # Unicode emoji. Keys kept only so btn() call-sites stay uniform with
    # the other replicas; every value is None on purpose.
    "shop": None,
    "wallet": None,
    "gift": None,
    "check": None,
}

# ---------------------------------------------------------------------------
# Button colour cycle — primary (blue) -> success (green) -> danger (brown)
# ---------------------------------------------------------------------------
STYLE_CYCLE = ["primary", "success", "danger"]
STYLE_PRIMARY = "primary"   # 1st, 4th, 7th...  blue/teal
STYLE_SUCCESS = "success"   # 2nd, 5th, 8th...  green
STYLE_DANGER = "danger"     # 3rd, 6th, 9th...  brown/orange in Telegram Web


def btn(text, callback_data=None, *, url=None, style=None,
        icon_custom_emoji_id=None, emoji_key=None):
    """One InlineKeyboardButton with optional Bot API style + custom emoji.

    Falls back to a plain button when the installed aiogram version does
    not know the style / icon_custom_emoji_id fields yet. When `style` is
    None the button is created unstyled — keyboards in this bot are built
    through styled_kb(), which assigns the cycle style by position.
    """
    kwargs = {"text": text}
    if callback_data is not None:
        kwargs["callback_data"] = callback_data
    if url is not None:
        kwargs["url"] = url
    icon = icon_custom_emoji_id
    if icon is None and emoji_key:
        icon = CUSTOM_EMOJI_IDS.get(emoji_key)
    if style or icon:
        try:
            return InlineKeyboardButton(
                **kwargs, style=style, icon_custom_emoji_id=icon)
        except TypeError:
            pass  # older aiogram: plain button below
    return InlineKeyboardButton(**kwargs)


def _styled_btn(spec, index):
    """Create a button from a spec dict, styled by flat position `index`."""
    style = STYLE_CYCLE[index % len(STYLE_CYCLE)]
    return btn(
        spec["text"],
        spec.get("callback_data"),
        url=spec.get("url"),
        style=spec.get("style", style),
        icon_custom_emoji_id=spec.get("icon_custom_emoji_id"),
        emoji_key=spec.get("emoji_key"),
    )


def styled_kb(rows):
    """Build an InlineKeyboardMarkup from rows of spec dicts.

    `rows` is a list of rows; each row is a list of dicts like
    {"text": ..., "callback_data": ...} or {"text": ..., "url": ...}.
    Styles are assigned by FLAT button order across the whole keyboard:
    primary, success, danger, primary, success, danger, ...
    A spec may pin its own "style" to override the cycle for that button.
    """
    out_rows = []
    index = 0
    for row in rows:
        out_row = []
        for spec in row:
            if isinstance(spec, InlineKeyboardButton):
                out_row.append(spec)
            else:
                out_row.append(_styled_btn(spec, index))
            index += 1
        out_rows.append(out_row)
    return InlineKeyboardMarkup(inline_keyboard=out_rows)


# Backwards-friendly alias (naming used in the task spec).
styled_buttons = styled_kb


def B(text, callback_data=None, url=None):
    """Short spec-dict helper for styled_kb() rows."""
    spec = {"text": text}
    if callback_data is not None:
        spec["callback_data"] = callback_data
    if url is not None:
        spec["url"] = url
    return spec


def ikb(rows):
    return InlineKeyboardMarkup(inline_keyboard=rows)


# ---------------------------------------------------------------------------
# Small helpers
# ---------------------------------------------------------------------------

def fmt(n) -> str:
    """1234567 -> '1,234,567' (the original formats Toman with commas)."""
    try:
        n = float(n)
    except (TypeError, ValueError):
        return str(n)
    if n == int(n):
        return f"{int(n):,}"
    return f"{n:,.2f}".rstrip("0").rstrip(".")


def money(toman) -> str:
    """'{toman:,} تومان / {toman*10:,} ریال' — the original's dual display."""
    t = int(toman)
    return f"{fmt(t)} تومان / {fmt(t * 10)} ریال"


def progress_bar(percent: float, width: int = 10) -> str:
    filled = int(round(max(0.0, min(100.0, percent)) / 100.0 * width))
    return "█" * filled + "░" * (width - filled)


def random_suffix(n: int = 8) -> str:
    alphabet = string.ascii_lowercase + string.digits
    return "".join(random.choice(alphabet) for _ in range(n))


# --- Gregorian -> Jalali (stdlib-only jalaali algorithm) -------------------

def _div(a, b):
    return a // b


def gregorian_to_jalali(gy, gm, gd):
    """Gregorian (y, m, d) -> Jalali (jy, jm, jd). Standard jalaali port."""
    g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334]
    gy2 = gy + 1 if gm > 2 else gy
    days = (355666 + 365 * gy + _div(gy2 + 3, 4) - _div(gy2 + 99, 100)
            + _div(gy2 + 399, 400) + gd + g_d_m[gm - 1])
    jy = -1595 + 33 * _div(days, 12053)
    days %= 12053
    jy += 4 * _div(days, 1461)
    days %= 1461
    if days > 365:
        jy += _div(days - 1, 365)
        days = (days - 1) % 365
    if days < 186:
        jm = 1 + _div(days, 31)
        jd = 1 + (days % 31)
    else:
        jm = 7 + _div(days - 186, 30)
        jd = 1 + ((days - 186) % 30)
    return jy, jm, jd


def jalali_str(ts) -> str:
    """Unix timestamp -> '1405/08/16' Jalali date string."""
    dt = datetime.fromtimestamp(int(ts), tz=timezone.utc)
    jy, jm, jd = gregorian_to_jalali(dt.year, dt.month, dt.day)
    return f"{jy}/{jm:02d}/{jd:02d}"


def generate_config_text(service_name: str, plan_name: str, gb, user_id) -> str:
    """TODO stub: placeholder OpenVPN config.

    A real deployment must replace this with a config actually issued by
    the OpenVPN / Xray panel (client cert/key, remote host, ...). Only the
    visible delivery behaviour (a .ovpn document) is replicated here.
    """
    return (
        "# Abolfazl — placeholder config (TODO: real panel integration)\n"
        f"# service: {service_name}\n"
        f"# plan: {plan_name} ({gb} GB)\n"
        f"# user: {user_id}\n"
        "client\n"
        "dev tun\n"
        "proto udp\n"
        "remote vpn.example.com 1194\n"
        "resolv-retry infinite\n"
        "nobind\n"
        "persist-key\n"
        "persist-tun\n"
        "auth SHA256\n"
        "cipher AES-256-GCM\n"
        "verb 3\n"
        "<ca>\n# TODO: CA certificate from the real panel\n</ca>\n"
        "<cert>\n# TODO: client certificate from the real panel\n</cert>\n"
        "<key>\n# TODO: client key from the real panel\n</key>\n"
    )


# ---------------------------------------------------------------------------
# Database (SQLite)
# ---------------------------------------------------------------------------

def db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


DEFAULT_SETTINGS = {
    "card_number": "6219861430412378",
    "card_holder": "عربزاده",
    "gate_enabled": "1",
    "gate_channel": "@Abolfazl_rwm",
    "min_wallet_charge": "10000",
    "stars_rate": "5000",          # Toman per 1 Telegram Star
    "referral_reward": "2500",     # Toman, credited on referred first purchase
    "support_url": "https://t.me/Abolfazl_rwm",  # placeholder — see REPORT.md
}

DEFAULT_PRODUCTS = [
    # id, name, volume_label, gb, days, price (Toman)
    ("v2ray", "V2ray", "1 گیگ", 1, 30, 2000),
    ("openvpn", "Open vpn", "1 گیگ", 1, 30, 4000),
    ("v2ray_hajmi", "V2ray - حجمی", "5 گیگ", 5, 30, 15000),
    ("v2ray20", "V2ray 20gb", "20 گیگ", 20, 30, 50000),
]

# Stars top-up packages: (toman, stars) — stars derived from stars_rate seed.
STARS_PACKAGES = [(50000, 10), (100000, 20), (200000, 40), (500000, 100)]

OPENVPN_ANDROID_URL = "https://play.google.com/store/apps/details?id=net.openvpn.openvpn"
OPENVPN_IOS_URL = "https://apps.apple.com/app/openvpn-connect/id590379981"


def init_db() -> None:
    os.makedirs(FILES_DIR, exist_ok=True)
    with closing(db()) as conn:
        conn.executescript(
            """
            CREATE TABLE IF NOT EXISTS users(
                id INTEGER PRIMARY KEY,
                username TEXT DEFAULT '',
                first_name TEXT DEFAULT '',
                balance INTEGER DEFAULT 0,
                referrer_id INTEGER DEFAULT 0,
                referral_earned INTEGER DEFAULT 0,
                referral_paid_flag INTEGER DEFAULT 0,
                trial_used INTEGER DEFAULT 0,
                banned INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS products(
                id TEXT PRIMARY KEY,
                name TEXT DEFAULT '',
                volume_label TEXT DEFAULT '',
                gb REAL DEFAULT 0,
                days INTEGER DEFAULT 30,
                price INTEGER DEFAULT 0,
                active INTEGER DEFAULT 1
            );
            CREATE TABLE IF NOT EXISTS invoices(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                kind TEXT DEFAULT '',
                product_id TEXT DEFAULT '',
                amount INTEGER DEFAULT 0,
                discount_code TEXT DEFAULT '',
                status TEXT DEFAULT 'unpaid',
                receipt_file_id TEXT DEFAULT '',
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS services(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                name TEXT DEFAULT '',
                plan_name TEXT DEFAULT '',
                gb REAL DEFAULT 0,
                used_gb REAL DEFAULT 0,
                expires_at INTEGER DEFAULT 0,
                config_text TEXT DEFAULT '',
                is_trial INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS discount_codes(
                code TEXT PRIMARY KEY,
                percent INTEGER NOT NULL,
                active INTEGER DEFAULT 1,
                max_uses INTEGER DEFAULT 0,
                used INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS gift_codes(
                code TEXT PRIMARY KEY,
                amount_toman INTEGER NOT NULL,
                used_by INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS settings(
                key TEXT PRIMARY KEY,
                value TEXT DEFAULT ''
            );
            CREATE TABLE IF NOT EXISTS support_tickets(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                text TEXT DEFAULT '',
                photo_file_id TEXT DEFAULT '',
                created_at INTEGER DEFAULT 0
            );
            """
        )
        for key, value in DEFAULT_SETTINGS.items():
            conn.execute(
                "INSERT OR IGNORE INTO settings(key, value) VALUES(?, ?)",
                (key, value))
        for row in DEFAULT_PRODUCTS:
            conn.execute(
                "INSERT OR IGNORE INTO products"
                "(id, name, volume_label, gb, days, price, active)"
                " VALUES(?, ?, ?, ?, ?, ?, 1)", row)
        conn.commit()


def get_setting(key, default=""):
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT value FROM settings WHERE key=?", (key,)).fetchone()
    return row["value"] if row else default


def set_setting(key, value):
    with closing(db()) as conn:
        conn.execute(
            "INSERT INTO settings(key, value) VALUES(?, ?)"
            " ON CONFLICT(key) DO UPDATE SET value=excluded.value",
            (key, str(value)))
        conn.commit()


def setting_int(key, default=0):
    try:
        return int(get_setting(key, str(default)))
    except (TypeError, ValueError):
        return default


def ensure_user(user_id, username="", first_name="", referrer_id=0):
    now = int(time.time())
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        if row is None:
            conn.execute(
                "INSERT INTO users(id, username, first_name, referrer_id,"
                " created_at) VALUES(?, ?, ?, ?, ?)",
                (user_id, username or "", first_name or "",
                 referrer_id if referrer_id and referrer_id != user_id else 0,
                 now))
            conn.commit()
            row = conn.execute(
                "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        else:
            conn.execute(
                "UPDATE users SET username=?, first_name=? WHERE id=?",
                (username or row["username"], first_name or row["first_name"],
                 user_id))
            conn.commit()
    return row


def get_user(user_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM users WHERE id=?", (user_id,)).fetchone()


def is_admin(user_id) -> bool:
    return user_id in ADMIN_IDS


def is_banned(user_id) -> bool:
    row = get_user(user_id)
    return bool(row and row["banned"])


def change_balance(user_id, delta):
    with closing(db()) as conn:
        conn.execute(
            "UPDATE users SET balance = balance + ? WHERE id=?",
            (int(delta), user_id))
        conn.commit()
        row = conn.execute(
            "SELECT balance FROM users WHERE id=?", (user_id,)).fetchone()
    return row["balance"] if row else 0


def get_products(active_only=True):
    with closing(db()) as conn:
        if active_only:
            return conn.execute(
                "SELECT * FROM products WHERE active=1 ORDER BY rowid"
            ).fetchall()
        return conn.execute("SELECT * FROM products ORDER BY rowid").fetchall()


def get_product(product_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM products WHERE id=?", (product_id,)).fetchone()


def create_invoice(user_id, kind, amount, product_id="", discount_code=""):
    with closing(db()) as conn:
        cur = conn.execute(
            "INSERT INTO invoices(user_id, kind, product_id, amount,"
            " discount_code, status, created_at) VALUES(?, ?, ?, ?, ?,"
            " 'unpaid', ?)",
            (user_id, kind, product_id, int(amount), discount_code,
             int(time.time())))
        conn.commit()
        return cur.lastrowid


def get_invoice(invoice_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM invoices WHERE id=?", (invoice_id,)).fetchone()


def set_invoice_status(invoice_id, status, receipt_file_id=None):
    with closing(db()) as conn:
        if receipt_file_id is not None:
            conn.execute(
                "UPDATE invoices SET status=?, receipt_file_id=? WHERE id=?",
                (status, receipt_file_id, invoice_id))
        else:
            conn.execute(
                "UPDATE invoices SET status=? WHERE id=?", (status, invoice_id))
        conn.commit()


def latest_unpaid_invoice(user_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM invoices WHERE user_id=? AND status='unpaid'"
            " ORDER BY id DESC LIMIT 1", (user_id,)).fetchone()


def create_service(user_id, plan_name, gb, days, is_trial=0, product=None):
    """Create a service row + placeholder .ovpn config (stub provisioning)."""
    name = f"{user_id}-{random_suffix(8)}"
    if product is not None:
        plan_name = product["name"]
        gb = product["gb"]
        days = product["days"]
    expires_at = int(time.time()) + int(days) * 86400
    config_text = generate_config_text(name, plan_name, gb, user_id)
    with closing(db()) as conn:
        cur = conn.execute(
            "INSERT INTO services(user_id, name, plan_name, gb, used_gb,"
            " expires_at, config_text, is_trial, created_at)"
            " VALUES(?, ?, ?, ?, 0, ?, ?, ?, ?)",
            (user_id, name, plan_name, gb, expires_at, config_text,
             int(is_trial), int(time.time())))
        conn.commit()
        service_id = cur.lastrowid
    return get_service(service_id)


def get_service(service_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM services WHERE id=?", (service_id,)).fetchone()


def get_user_services(user_id):
    with closing(db()) as conn:
        return conn.execute(
            "SELECT * FROM services WHERE user_id=? ORDER BY id DESC",
            (user_id,)).fetchall()


def maybe_pay_referral(buyer_id):
    """Credit the referrer once, on the buyer's FIRST paid purchase."""
    buyer = get_user(buyer_id)
    if not buyer or not buyer["referrer_id"] or buyer["referral_paid_flag"]:
        return
    reward = setting_int("referral_reward", 2500)
    referrer_id = buyer["referrer_id"]
    if get_user(referrer_id) is None:
        return
    with closing(db()) as conn:
        conn.execute(
            "UPDATE users SET referral_paid_flag=1 WHERE id=?", (buyer_id,))
        conn.execute(
            "UPDATE users SET balance = balance + ?,"
            " referral_earned = referral_earned + ? WHERE id=?",
            (reward, reward, referrer_id))
        conn.commit()


def referral_count(user_id):
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT COUNT(*) AS c FROM users WHERE referrer_id=?",
            (user_id,)).fetchone()
    return row["c"] if row else 0


# ---------------------------------------------------------------------------
# Guide text — verbatim from the original bot (/help and menu:guide)
# ---------------------------------------------------------------------------
GUIDE_TEXT = """📖 راهنمای اتصال سرویس‌ها
📦 ۱) کانفیگ را از کجا دریافت کنم؟
بعد از خرید، وارد «📦 سرویس‌های من» شوید و کانفیگ یا لینک اشتراک خود را دریافت کنید.
📱 ۲) چه برنامه‌ای نصب کنم؟
🌐 • اندروید: Hiddify یا v2rayNG
• آیفون: Streisand یا V2Box
• ویندوز: v2rayN
🛡 WireGuard
• اندروید: Amnezia VPN
• آیفون: WireGuard
🔐 OpenVPN
• اندروید، آیفون و ویندوز: OpenVPN Connect
⚙️ ۳) چطور کانفیگ را وارد کنم؟
🌐 V2Ray:
۱. لینک کانفیگ را کپی کنید.
۲. برنامه را باز کرده و روی ➕ بزنید.
۳. گزینه Import from Clipboard را انتخاب کنید.
۴. سرور را انتخاب کرده و متصل شوید.
💡 برای لینک اشتراک، گزینه «بروزرسانی اشتراک» را بزنید.
🛡 WireGuard:
۱. برنامه سازگار را نصب کنید.
۲. فایل .conf یا کد QR را دریافت کنید.
۳. کانفیگ را وارد کرده و اتصال را فعال کنید.
🔐 OpenVPN:
۱. برنامه OpenVPN Connect را باز کنید.
۲. فایل .ovpn را وارد کنید.
۳. روی Connect بزنید.
🔧 ۴) اگر متصل نشدم چه کنم؟
• ساعت گوشی را روی حالت خودکار بگذارید.
• اشتراک را بروزرسانی کنید.
• حجم و تاریخ انقضا را بررسی کنید.
• اینترنت یا سرور دیگری را امتحان کنید.
• برنامه را بروزرسانی کنید.
📥 ۵) لینک دانلود برنامه‌ها
🤖 V2Ray اندروید: https://github.com/hiddify/hiddify-next/releases
🍎 V2Ray آیفون: https://apps.apple.com/app/streisand/id6450534064
🛡 WireGuard اندروید: https://play.google.com/store/apps/details?id=org.amnezia.vpn
🍎 WireGuard آیفون: https://share.google/9QsRtGfK8Q5gRVhkl
🔐 OpenVPN Connect: https://openvpn.net/client/
💬 ۶) پشتیبانی
اگر مشکل اتصال دارید، از بخش «☎️ پشتیبانی» پیام دهید و تصویر خطا را ارسال کنید.
💎 IR VIP SHOP | اتصال سریع و پایدار"""

GATE_TEXT = ("📢 برای استفاده از ربات، ابتدا در کانال زیر عضو شوید:\n"
             "@Abolfazl_rwm\n"
             "سپس دکمهٔ «بررسی عضویت» را بزنید.")


# ---------------------------------------------------------------------------
# Text builders
# ---------------------------------------------------------------------------

def welcome_text(balance) -> str:
    return (
        "𝐕𝐈𝐏 𝐈𝐑\n"
        "به فروشگاه ما خوش آمدید 🌹\n"
        "از منوی زیر سرویس دلخواهتان را تهیه کنید.\n"
        "────────────\n"
        f"👛 موجودی شما: {money(balance)}"
    )


def product_text(product, balance, discount_percent=0, discount_code="") -> str:
    price = int(product["price"])
    final_price = price
    if discount_percent:
        final_price = price - (price * int(discount_percent) // 100)
    text = (
        f"🛒 {product['name']}\n"
        "────────────\n"
        f"📦 حجم: {product['volume_label']}\n"
        f"📅 مدت: {int(product['days'])} روز\n"
        f"💳 قیمت: {money(final_price)}\n"
        f"👛 موجودی: {money(balance)}"
    )
    if discount_percent:
        text += (f"\n🎟 کد تخفیف «{discount_code}» اعمال شد"
                 f" ({int(discount_percent)}٪ تخفیف)")
    if balance < final_price:
        needed = final_price - balance
        text += (
            f"\n⚠️ موجودی کیف پول کافی نیست ({money(needed)} کم دارید)."
            " میتوانید مستقیم کارتبهکارت پرداخت کنید یا کیف پول را شارژ کنید."
        )
    return text


def buy_payment_text(product, amount) -> str:
    return (
        f"💳 پرداخت سرویس — {product['name']}\n"
        f"📦 {product['volume_label']} · 📅 {int(product['days'])} روز\n"
        "────────────\n"
        "دقیقاً همین مبلغ را کارت‌به‌کارت کنید:\n"
        f"🔥 {money(amount)}\n"
        f"💳 {get_setting('card_number')}\n"
        f"👤 {get_setting('card_holder')}\n"
        "────────────\n"
        "پس از واریز، عکس رسید را بفرستید تا سرویس شما ساخته شود."
    )


def wallet_payment_text(amount) -> str:
    return (
        "💳 شارژ کیف پول\n"
        "────────────\n"
        "دقیقاً همین مبلغ را کارت‌به‌کارت کنید:\n"
        f"🔥 {money(amount)}\n"
        f"💳 {get_setting('card_number')}\n"
        f"👤 {get_setting('card_holder')}\n"
        "────────────\n"
        "پس از واریز، عکس رسید را بفرستید تا تأیید شود."
    )


def wallet_text(user_id) -> str:
    user = get_user(user_id)
    balance = user["balance"] if user else 0
    text = (
        "👛 کیف پول\n"
        "────────────\n"
        f"موجودی: {money(balance)}"
    )
    if latest_unpaid_invoice(user_id):
        text += "\n🧾 یک فاکتور پرداختنشده دارید."
    return text


def service_detail_text(service) -> str:
    gb = service["gb"]
    used = service["used_gb"] or 0
    percent = (used / gb * 100) if gb else 0
    text = (
        f"🔧 {service['name']}\n"
        "────────────\n"
        f"📦 پلن: {service['plan_name']}\n"
        f"📅 انقضا: {jalali_str(service['expires_at'])}\n"
        f"📊 مصرف: {used:g} از {gb:g} گیگ\n"
        f"{progress_bar(percent)} {percent:.0f}%\n"
    )
    if service["is_trial"]:
        text += "🎁 سرویس تست — قابل تمدید نیست\n"
    text += "📄 فایل کانفیگ با دکمهٔ زیر ارسال میشود."
    return text


def referral_text(user_id, bot_username) -> str:
    user = get_user(user_id)
    earned = user["referral_earned"] if user else 0
    count = referral_count(user_id)
    username = bot_username or BOT_USERNAME
    return (
        "👥 دعوت دوستان\n"
        "────────────\n"
        "با دعوت هر دوست، بعد از اولین خرید او مبلغ 2,500 تومان / 25,000 ریال"
        " به کیف پول شما اضافه میشود.\n"
        "🔗 لینک دعوت شما:\n"
        f"https://t.me/{username}?start={user_id}\n"
        f"👤 دعوتشدهها: {count}\n"
        f"💰 درآمد دعوت: {money(earned)}"
    )


# ---------------------------------------------------------------------------
# Keyboards — EVERY inline keyboard goes through styled_kb() so the
# primary -> success -> danger colour cycle matches the original screen
# by screen, in flat button order.
# ---------------------------------------------------------------------------

def kb_gate():
    channel = get_setting("gate_channel", "@Abolfazl_rwm").lstrip("@")
    return styled_kb([
        [B("📢 عضویت در کانال", url=f"https://t.me/{channel}")],
        [B("✅ بررسی عضویت", "check_join")],
    ])


def kb_main_menu():
    return styled_kb([
        [B("🛒 خرید سرویس", "menu:shop")],
        [B("📦 سرویسهای من", "menu:myservices"),
         B("👛 کیف پول", "menu:wallet")],
        [B("🎁 سرویس تست رایگان", "menu:trial")],
        [B("📖 راهنمای اتصال", "menu:guide"),
         B("👥 دعوت دوستان", "menu:referral")],
        [B("🎫 پشتیبانی", "menu:support")],
    ])


def kb_shop(products=None):
    if products is None:
        products = get_products()
    rows = [[B(p["name"], f"product:{p['id']}")] for p in products]
    rows.append([B("↩️ بازگشت", "menu:main")])
    return styled_kb(rows)


def kb_product(product_id, balance_sufficient=False):
    rows = []
    if balance_sufficient:
        # Inferred standard behaviour (see REPORT.md): wallet-pay first.
        rows.append([B("💰 پرداخت از کیف پول", f"wallet_pay:{product_id}")])
    rows.append([B("💳 پرداخت کارتبهکارت", f"pay_card:{product_id}")])
    rows.append([B("➕ شارژ کیف پول", "wallet:charge")])
    rows.append([B("🎟 کد تخفیف دارم", f"discount:{product_id}")])
    rows.append([B("↩️ بازگشت", "menu:shop")])
    return styled_kb(rows)


def kb_payment(back_callback="menu:shop"):
    return styled_kb([
        [B("📸 ارسال رسید", "receipt:send")],
        [B("↩️ بازگشت", back_callback)],
    ])


def kb_discount_cancel(product_id):
    return styled_kb([
        [B("بیخیال", f"product:{product_id}")],
    ])


def kb_back(callback="menu:main"):
    return styled_kb([[B("↩️ بازگشت", callback)]])


def kb_myservices(services):
    if not services:
        return styled_kb([
            [B("🛒 خرید سرویس", "menu:shop")],
            [B("↩️ بازگشت", "menu:main")],
        ])
    rows = []
    for s in services:
        label = (f"🟢 {s['name']} · {s['gb']:g} گیگ · "
                 f"{jalali_str(s['expires_at'])}")
        rows.append([B(label, f"service:{s['id']}")])
    rows.append([B("↩️ بازگشت", "menu:main")])
    return styled_kb(rows)


def kb_service_detail(service_id):
    return styled_kb([
        [B("🤖 دانلود اندروید", url=OPENVPN_ANDROID_URL),
         B("🍎 دانلود آیفون", url=OPENVPN_IOS_URL)],
        [B("📥 دریافت فایل کانفیگ", f"service:config:{service_id}")],
        [B("↩️ بازگشت", "menu:myservices"),
         B("🏠 منوی اصلی", "menu:main")],
    ])


def kb_wallet(has_unpaid=False):
    rows = [
        [B("➕ شارژ کیف پول", "wallet:charge")],
        [B("⭐️ شارژ با استارز", "wallet:stars")],
        [B("🎁 کد هدیه", "wallet:gift")],
    ]
    if has_unpaid:
        rows.append([B("🧾 ادامهٔ پرداخت", "wallet:continue")])
    rows.append([B("↩️ بازگشت", "menu:main")])
    return styled_kb(rows)


def kb_stars():
    rows = []
    for toman, stars in STARS_PACKAGES:
        label = f"{fmt(toman)} تومان / {fmt(toman * 10)} ریال — ⭐️ {stars}"
        rows.append([B(label, f"stars:buy:{stars}")])
    rows.append([B("↩️ بازگشت", "menu:wallet")])
    return styled_kb(rows)


def kb_guide():
    return styled_kb([
        [B("📦 سرویس‌های من", "menu:myservices")],
        [B("🎧 تلگرام", url=get_setting("support_url", "https://t.me/Abolfazl_rwm"))],
        [B("🏠 منوی اصلی", "menu:main")],
    ])


def kb_referral():
    return styled_kb([[B("↩️ بازگشت", "menu:main")]])


def kb_support():
    return styled_kb([
        [B("✍️ ارسال پیام", "support:write")],
        [B("↩️ بازگشت", "menu:main")],
    ])


def kb_trial_done():
    return styled_kb([
        [B("🤖 دانلود اندروید", url=OPENVPN_ANDROID_URL),
         B("🍎 دانلود آیفون", url=OPENVPN_IOS_URL)],
        [B("🛒 خرید سرویس", "menu:shop")],
        [B("🏠 منوی اصلی", "menu:main")],
    ])


def kb_admin_main():
    return styled_kb([
        [B("📊 آمار", "admin:stats")],
        [B("🧾 رسیدهای در انتظار", "admin:receipts"),
         B("🛒 محصولات", "admin:products")],
        [B("⚙️ تنظیمات", "admin:settings"),
         B("👥 کاربران", "admin:users:0")],
        [B("🎟 کدهای تخفیف", "admin:discounts"),
         B("🎁 کدهای هدیه", "admin:gifts")],
        [B("📣 ارسال همگانی", "admin:broadcast")],
        [B("🏠 منوی اصلی", "menu:main")],
    ])


def kb_admin_receipt(invoice_id):
    return styled_kb([
        [B("✅ تأیید", f"admin_approve:{invoice_id}"),
         B("❌ رد", f"admin_reject:{invoice_id}")],
    ])


def kb_admin_back():
    return styled_kb([[B("↩️ بازگشت", "admin:main")]])


def kb_confirm_broadcast():
    return styled_kb([
        [B("✅ تأیید ارسال", "admin:broadcast_confirm"),
         B("❌ لغو", "admin:main")],
    ])


def kb_support_admin(user_id):
    return styled_kb([
        [B("✍️ پاسخ به کاربر", f"support_reply:{user_id}")],
    ])


# ---------------------------------------------------------------------------
# FSM states
# ---------------------------------------------------------------------------

class ShopStates(StatesGroup):
    waiting_receipt = State()        # data: invoice_id
    waiting_discount = State()       # data: product_id
    waiting_wallet_amount = State()
    waiting_gift_code = State()
    waiting_support_text = State()


class AdminStates(StatesGroup):
    waiting_product_price = State()  # data: product_id
    waiting_product_volume = State()  # data: product_id (label + gb)
    waiting_setting_value = State()  # data: setting_key
    waiting_discount_new = State()   # "CODE PERCENT MAX_USES"
    waiting_gift_new = State()       # "CODE AMOUNT"
    waiting_user_balance = State()   # data: user_id, mode add/deduct
    waiting_broadcast = State()
    waiting_broadcast_confirm = State()  # data: text / photo_file_id
    waiting_support_reply = State()  # data: target_user_id


router = Router()

# Pending discount per user: user_id -> (product_id, code, percent)
PENDING_DISCOUNT = {}
# Pending invoice whose receipt is expected is tracked in FSM data; this
# dict mirrors the latest payment screen per user for «ادامهٔ پرداخت».
PENDING_INVOICE = {}


# ---------------------------------------------------------------------------
# Gate / membership helpers
# ---------------------------------------------------------------------------

async def is_gate_member(bot: Bot, user_id) -> bool:
    if is_admin(user_id):
        return True
    if get_setting("gate_enabled", "1") != "1":
        return True
    channel = get_setting("gate_channel", "@Abolfazl_rwm")
    try:
        member = await bot.get_chat_member(channel, user_id)
        return member.status not in ("left", "kicked")
    except Exception:
        # If the bot cannot check (not admin in the group, ...), fail open
        # for admins only is handled above; for users, treat as not joined
        # only when Telegram explicitly says so — here we fail closed=False
        # is wrong for a gate, so report not-member on API errors except
        # «chat not found» style errors are indistinguishable; keep closed.
        log.warning("gate check failed for %s in %s", user_id, channel)
        return False


async def show_gate(target: Message, edit=False):
    if edit:
        try:
            await target.edit_text(GATE_TEXT, reply_markup=kb_gate())
            return
        except TelegramBadRequest:
            pass
    await target.answer(GATE_TEXT, reply_markup=kb_gate())


async def show_main_menu(target, user_id, bot_username=None, edit=False):
    user = get_user(user_id)
    balance = user["balance"] if user else 0
    text = welcome_text(balance)
    if isinstance(target, CallbackQuery):
        try:
            await target.message.edit_text(text, reply_markup=kb_main_menu())
        except TelegramBadRequest:
            await target.message.answer(text, reply_markup=kb_main_menu())
    elif edit:
        try:
            await target.edit_text(text, reply_markup=kb_main_menu())
        except TelegramBadRequest:
            await target.answer(text, reply_markup=kb_main_menu())
    else:
        await target.answer(text, reply_markup=kb_main_menu())


async def edit_or_answer(call: CallbackQuery, text, reply_markup=None):
    try:
        await call.message.edit_text(text, reply_markup=reply_markup)
    except TelegramBadRequest:
        await call.message.answer(text, reply_markup=reply_markup)


async def send_config_file(bot: Bot, chat_id, service):
    """Send the service's .ovpn document (caption «فایل کانفیگ 🔓»)."""
    data = (service["config_text"] or "").encode("utf-8")
    doc = BufferedInputFile(data, filename=f"{service['name']}.ovpn")
    await bot.send_document(chat_id, doc, caption="فایل کانفیگ 🔓")


async def notify_admins(bot: Bot, text, reply_markup=None, photo=None):
    for admin_id in ADMIN_IDS:
        try:
            if photo:
                await bot.send_photo(admin_id, photo, caption=text,
                                     reply_markup=reply_markup)
            else:
                await bot.send_message(admin_id, text,
                                       reply_markup=reply_markup)
        except Exception:
            log.warning("could not notify admin %s", admin_id)


async def complete_paid_invoice(bot: Bot, invoice):
    """Apply a paid invoice: create the service / credit the wallet.

    Also fires the one-time referral reward on a first paid purchase.
    """
    user_id = invoice["user_id"]
    if invoice["kind"] == "wallet":
        change_balance(user_id, invoice["amount"])
        await bot.send_message(
            user_id,
            f"✅ شارژ کیف پول تأیید شد.\nموجودی جدید: "
            f"{money(get_user(user_id)['balance'])}")
        return
    # kind == buy
    product = get_product(invoice["product_id"])
    if product is None:
        await bot.send_message(
            user_id, "✅ پرداخت تأیید شد، اما محصول یافت نشد؛ با پشتیبانی در تماس باشید.")
        return
    service = create_service(user_id, product["name"], product["gb"],
                             product["days"], is_trial=0, product=product)
    maybe_pay_referral(user_id)
    await bot.send_message(
        user_id,
        "✅ پرداخت تأیید شد و سرویس شما ساخته شد:\n\n"
        + service_detail_text(service),
        reply_markup=kb_service_detail(service["id"]))
    await send_config_file(bot, user_id, service)


# ---------------------------------------------------------------------------
# Handlers — start / help / gate
# ---------------------------------------------------------------------------

@router.message(CommandStart())
async def cmd_start(message: Message, state: FSMContext, bot: Bot):
    await state.clear()
    args = (message.text or "").split(maxsplit=1)
    referrer_id = 0
    if len(args) > 1:
        try:
            referrer_id = int(args[1].strip())
        except ValueError:
            referrer_id = 0
    user = ensure_user(message.from_user.id, message.from_user.username,
                       message.from_user.first_name, referrer_id)
    if user and user["banned"]:
        await message.answer("⛔️ حساب شما مسدود شده است.")
        return
    if not await is_gate_member(bot, message.from_user.id):
        await show_gate(message)
        return
    await show_main_menu(message, message.from_user.id)


@router.message(Command("help"))
async def cmd_help(message: Message, state: FSMContext, bot: Bot):
    await state.clear()
    ensure_user(message.from_user.id, message.from_user.username,
                message.from_user.first_name)
    if is_banned(message.from_user.id):
        await message.answer("⛔️ حساب شما مسدود شده است.")
        return
    if not await is_gate_member(bot, message.from_user.id):
        await show_gate(message)
        return
    await message.answer(GUIDE_TEXT, reply_markup=kb_guide())


@router.message(Command("cancel"))
async def cmd_cancel(message: Message, state: FSMContext):
    await state.clear()
    await message.answer("لغو شد.")


@router.callback_query(F.data == "check_join")
async def cb_check_join(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    ensure_user(call.from_user.id, call.from_user.username,
                call.from_user.first_name)
    if await is_gate_member(bot, call.from_user.id):
        await call.answer("✅ عضویت تأیید شد", show_alert=True)
        # Original: user must /start again; this replica shows the main
        # menu right away (difference noted in REPORT.md).
        await show_main_menu(call, call.from_user.id)
    else:
        await call.answer("❌ هنوز عضو نشده‌اید.", show_alert=True)


# ---------------------------------------------------------------------------
# Handlers — main menu navigation (edit-in-place; any menu callback clears
# the current waiting state first — the user's standing state rule)
# ---------------------------------------------------------------------------

async def _guard(call: CallbackQuery, bot: Bot) -> bool:
    """Common gate/ban guard for callbacks. Returns True if allowed."""
    ensure_user(call.from_user.id, call.from_user.username,
                call.from_user.first_name)
    if is_banned(call.from_user.id):
        await call.answer("⛔️ حساب شما مسدود شده است.", show_alert=True)
        return False
    if not await is_gate_member(bot, call.from_user.id):
        await show_gate(call.message)
        await call.answer()
        return False
    return True


@router.callback_query(F.data == "menu:main")
async def cb_menu_main(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    await show_main_menu(call, call.from_user.id)
    await call.answer()


@router.callback_query(F.data == "menu:shop")
async def cb_menu_shop(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    await edit_or_answer(
        call, "🛒 خرید سرویس\nیکی از بسته‌ها را انتخاب کنید:",
        kb_shop())
    await call.answer()


@router.callback_query(F.data.startswith("product:"))
async def cb_product(call: CallbackQuery, state: FSMContext, bot: Bot):
    # NOTE: pressing a different button cancels any waiting state.
    current = await state.get_state()
    if current not in (None, ShopStates.waiting_discount.state):
        await state.clear()
    if not await _guard(call, bot):
        return
    product_id = call.data.split(":", 1)[1]
    product = get_product(product_id)
    if product is None:
        await call.answer("محصول یافت نشد.", show_alert=True)
        return
    user = get_user(call.from_user.id)
    balance = user["balance"] if user else 0
    pend = PENDING_DISCOUNT.get(call.from_user.id)
    percent, code = (0, "")
    if pend and pend[0] == product_id:
        _, code, percent = pend
    price = int(product["price"])
    final_price = price - (price * percent // 100) if percent else price
    await edit_or_answer(
        call, product_text(product, balance, percent, code),
        kb_product(product_id, balance_sufficient=balance >= final_price))
    await call.answer()


@router.callback_query(F.data.startswith("discount:"))
async def cb_discount(call: CallbackQuery, state: FSMContext, bot: Bot):
    if not await _guard(call, bot):
        return
    product_id = call.data.split(":", 1)[1]
    await state.set_state(ShopStates.waiting_discount)
    await state.update_data(product_id=product_id)
    await call.message.answer(
        "🎟 کد تخفیف\nکد تخفیف را بفرستید:",
        reply_markup=kb_discount_cancel(product_id))
    await call.answer()


@router.message(ShopStates.waiting_discount, F.text)
async def on_discount_code(message: Message, state: FSMContext):
    data = await state.get_data()
    product_id = data.get("product_id", "")
    code = (message.text or "").strip()
    await state.clear()
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM discount_codes WHERE code=?", (code,)).fetchone()
    product = get_product(product_id)
    if (row is None or not row["active"]
            or (row["max_uses"] and row["used"] >= row["max_uses"])):
        await message.answer("❌ کد تخفیف معتبر نیست.")
        if product is not None:
            user = get_user(message.from_user.id)
            balance = user["balance"] if user else 0
            await message.answer(
                product_text(product, balance),
                reply_markup=kb_product(product_id, balance >= product["price"]))
        return
    # Inferred behaviour (see REPORT.md): apply % off and re-show detail.
    PENDING_DISCOUNT[message.from_user.id] = (product_id, code, row["percent"])
    with closing(db()) as conn:
        conn.execute(
            "UPDATE discount_codes SET used = used + 1 WHERE code=?", (code,))
        conn.commit()
    user = get_user(message.from_user.id)
    balance = user["balance"] if user else 0
    price = int(product["price"])
    final_price = price - (price * row["percent"] // 100)
    await message.answer(
        product_text(product, balance, row["percent"], code),
        reply_markup=kb_product(product_id, balance >= final_price))


@router.callback_query(F.data.startswith("pay_card:"))
async def cb_pay_card(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    product_id = call.data.split(":", 1)[1]
    product = get_product(product_id)
    if product is None:
        await call.answer("محصول یافت نشد.", show_alert=True)
        return
    pend = PENDING_DISCOUNT.pop(call.from_user.id, None)
    percent, code = (0, "")
    if pend and pend[0] == product_id:
        _, code, percent = pend
    price = int(product["price"])
    amount = price - (price * percent // 100) if percent else price
    invoice_id = create_invoice(call.from_user.id, "buy", amount,
                                 product_id=product_id, discount_code=code)
    PENDING_INVOICE[call.from_user.id] = invoice_id
    await state.update_data(invoice_id=invoice_id)
    await edit_or_answer(call, buy_payment_text(product, amount), kb_payment())
    await call.answer()


@router.callback_query(F.data.startswith("wallet_pay:"))
async def cb_wallet_pay(call: CallbackQuery, state: FSMContext, bot: Bot):
    """Inferred: pay from wallet balance, deduct and create the service."""
    await state.clear()
    if not await _guard(call, bot):
        return
    product_id = call.data.split(":", 1)[1]
    product = get_product(product_id)
    if product is None:
        await call.answer("محصول یافت نشد.", show_alert=True)
        return
    pend = PENDING_DISCOUNT.pop(call.from_user.id, None)
    percent = pend[2] if pend and pend[0] == product_id else 0
    price = int(product["price"])
    amount = price - (price * percent // 100) if percent else price
    user = get_user(call.from_user.id)
    if not user or user["balance"] < amount:
        await call.answer("⚠️ موجودی کیف پول کافی نیست.", show_alert=True)
        return
    change_balance(call.from_user.id, -amount)
    invoice_id = create_invoice(call.from_user.id, "buy", amount,
                                 product_id=product_id)
    set_invoice_status(invoice_id, "paid")
    invoice = get_invoice(invoice_id)
    await complete_paid_invoice(bot, invoice)
    await call.answer("✅ پرداخت از کیف پول انجام شد.")


@router.callback_query(F.data == "receipt:send")
async def cb_receipt_send(call: CallbackQuery, state: FSMContext):
    data = await state.get_data()
    invoice_id = data.get("invoice_id") or PENDING_INVOICE.get(call.from_user.id)
    if not invoice_id:
        # Fall back to the latest unpaid invoice (e.g. after «ادامهٔ پرداخت»).
        inv = latest_unpaid_invoice(call.from_user.id)
        invoice_id = inv["id"] if inv else None
    if not invoice_id:
        await call.answer("فاکتور فعالی یافت نشد.", show_alert=True)
        return
    await state.set_state(ShopStates.waiting_receipt)
    await state.update_data(invoice_id=invoice_id)
    await call.message.answer("📸 عکس رسید را بفرستید:")
    await call.answer()


@router.message(ShopStates.waiting_receipt, F.photo)
async def on_receipt_photo(message: Message, state: FSMContext, bot: Bot):
    data = await state.get_data()
    invoice_id = data.get("invoice_id")
    await state.clear()
    invoice = get_invoice(invoice_id) if invoice_id else None
    if invoice is None:
        await message.answer("فاکتور یافت نشد.")
        return
    photo_id = message.photo[-1].file_id
    set_invoice_status(invoice_id, "pending_review", receipt_file_id=photo_id)
    user = get_user(message.from_user.id)
    uname = f"@{user['username']}" if user and user["username"] else "—"
    caption = (
        f"🧾 رسید جدید — فاکتور #{invoice_id}\n"
        f"👤 کاربر: {message.from_user.id} ({uname})\n"
        f"💰 مبلغ: {money(invoice['amount'])}\n"
        f"📦 نوع: {invoice['kind']}"
        + (f" / {invoice['product_id']}" if invoice["product_id"] else "")
    )
    await notify_admins(bot, caption, reply_markup=kb_admin_receipt(invoice_id),
                        photo=photo_id)
    await message.answer("✅ رسید شما ارسال شد و پس از تأیید ادمین بررسی میشود.")


@router.message(ShopStates.waiting_receipt)
async def on_receipt_not_photo(message: Message, state: FSMContext):
    await message.answer("📸 لطفاً عکس رسید را بفرستید:")


# ---------------------------------------------------------------------------
# Handlers — my services
# ---------------------------------------------------------------------------

@router.callback_query(F.data == "menu:myservices")
async def cb_myservices(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    services = get_user_services(call.from_user.id)
    if not services:
        await edit_or_answer(call, "هنوز سرویسی ندارید.", kb_myservices([]))
    else:
        await edit_or_answer(call, "📦 سرویس‌های من", kb_myservices(services))
    await call.answer()


@router.callback_query(F.data.startswith("service:config:"))
async def cb_service_config(call: CallbackQuery, bot: Bot):
    service_id = int(call.data.rsplit(":", 1)[1])
    service = get_service(service_id)
    if service is None or service["user_id"] != call.from_user.id:
        await call.answer("سرویس یافت نشد.", show_alert=True)
        return
    await send_config_file(bot, call.from_user.id, service)
    await call.answer()


@router.callback_query(F.data.startswith("service:"))
async def cb_service_detail(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    service_id = int(call.data.split(":", 1)[1])
    service = get_service(service_id)
    if service is None or service["user_id"] != call.from_user.id:
        await call.answer("سرویس یافت نشد.", show_alert=True)
        return
    await edit_or_answer(call, service_detail_text(service),
                         kb_service_detail(service_id))
    await call.answer()


# ---------------------------------------------------------------------------
# Handlers — wallet
# ---------------------------------------------------------------------------

@router.callback_query(F.data == "menu:wallet")
async def cb_wallet(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    has_unpaid = latest_unpaid_invoice(call.from_user.id) is not None
    await edit_or_answer(call, wallet_text(call.from_user.id),
                         kb_wallet(has_unpaid))
    await call.answer()


@router.callback_query(F.data == "wallet:charge")
async def cb_wallet_charge(call: CallbackQuery, state: FSMContext, bot: Bot):
    # Works both from the wallet screen and from a product screen.
    current = await state.get_state()
    if current is not None:
        await state.clear()
    if not await _guard(call, bot):
        return
    await state.set_state(ShopStates.waiting_wallet_amount)
    await call.message.answer("مبلغ شارژ را به تومان بفرستید: حداقل 10000",
                              reply_markup=kb_back("menu:wallet"))
    await call.answer()


@router.message(ShopStates.waiting_wallet_amount, F.text)
async def on_wallet_amount(message: Message, state: FSMContext):
    raw = (message.text or "").strip().replace(",", "")
    min_charge = setting_int("min_wallet_charge", 10000)
    try:
        amount = int(raw)
    except ValueError:
        await message.answer("❌ مبلغ معتبر نیست. مبلغ شارژ را به تومان بفرستید:")
        return
    if amount < min_charge:
        await message.answer(
            f"❌ مبلغ کمتر از حداقل شارژ است (حداقل {fmt(min_charge)} تومان).")
        return
    await state.clear()
    invoice_id = create_invoice(message.from_user.id, "wallet", amount)
    PENDING_INVOICE[message.from_user.id] = invoice_id
    await state.update_data(invoice_id=invoice_id)
    await message.answer(wallet_payment_text(amount), reply_markup=kb_payment())


@router.callback_query(F.data == "wallet:stars")
async def cb_wallet_stars(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    await edit_or_answer(
        call,
        "⭐️ شارژ با استارز تلگرام\n"
        "────────────\n"
        "مبلغ موردنظر را انتخاب کنید. پرداخت مستقیم با استارز تلگرام انجام میشود.",
        kb_stars())
    await call.answer()


@router.callback_query(F.data.startswith("stars:buy:"))
async def cb_stars_buy(call: CallbackQuery, bot: Bot):
    stars = int(call.data.split(":")[-1])
    rate = setting_int("stars_rate", 5000)
    toman = stars * rate
    await bot.send_invoice(
        chat_id=call.from_user.id,
        title="شارژ کیف پول",
        description=f"شارژ کیف پول به مبلغ {money(toman)}",
        payload=f"stars_wallet:{toman}",
        currency="XTR",
        prices=[LabeledPrice(label="شارژ کیف پول", amount=stars)],
    )
    await call.answer()


@router.pre_checkout_query()
async def on_pre_checkout(query: PreCheckoutQuery, bot: Bot):
    await bot.answer_pre_checkout_query(query.id, ok=True)


@router.message(F.successful_payment)
async def on_successful_payment(message: Message):
    payload = message.successful_payment.invoice_payload or ""
    if payload.startswith("stars_wallet:"):
        try:
            toman = int(payload.split(":", 1)[1])
        except ValueError:
            toman = 0
        if toman:
            change_balance(message.from_user.id, toman)
            await message.answer(
                f"✅ پرداخت با استارز انجام شد.\nموجودی جدید: "
                f"{money(get_user(message.from_user.id)['balance'])}")


@router.callback_query(F.data == "wallet:gift")
async def cb_wallet_gift(call: CallbackQuery, state: FSMContext, bot: Bot):
    if not await _guard(call, bot):
        return
    await state.set_state(ShopStates.waiting_gift_code)
    await call.message.answer("🎁 کد هدیه را بفرستید:",
                              reply_markup=kb_back("menu:wallet"))
    await call.answer()


@router.message(ShopStates.waiting_gift_code, F.text)
async def on_gift_code(message: Message, state: FSMContext):
    code = (message.text or "").strip()
    await state.clear()
    with closing(db()) as conn:
        row = conn.execute(
            "SELECT * FROM gift_codes WHERE code=?", (code,)).fetchone()
        if row is not None and not row["used_by"]:
            conn.execute(
                "UPDATE gift_codes SET used_by=? WHERE code=?",
                (message.from_user.id, code))
            conn.commit()
    if row is None or row["used_by"]:
        await message.answer("❌ کد هدیه معتبر نیست یا قبلاً استفاده شده است.")
        return
    change_balance(message.from_user.id, row["amount_toman"])
    await message.answer(
        f"🎁 کد هدیه اعمال شد! {money(row['amount_toman'])} به کیف پول شما"
        f" اضافه شد.\nموجودی جدید: {money(get_user(message.from_user.id)['balance'])}")


@router.callback_query(F.data == "wallet:continue")
async def cb_wallet_continue(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    invoice = latest_unpaid_invoice(call.from_user.id)
    if invoice is None:
        await call.answer("فاکتور پرداختنشده‌ای ندارید.", show_alert=True)
        return
    PENDING_INVOICE[call.from_user.id] = invoice["id"]
    await state.update_data(invoice_id=invoice["id"])
    if invoice["kind"] == "wallet":
        text = wallet_payment_text(invoice["amount"])
    else:
        product = get_product(invoice["product_id"])
        text = (buy_payment_text(product, invoice["amount"]) if product
                else wallet_payment_text(invoice["amount"]))
    await edit_or_answer(call, text, kb_payment())
    await call.answer()


# ---------------------------------------------------------------------------
# Handlers — free trial
# ---------------------------------------------------------------------------

@router.callback_query(F.data == "menu:trial")
async def cb_trial(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    user = get_user(call.from_user.id)
    if user and user["trial_used"]:
        await call.answer("شما قبلاً سرویس تست رایگان را دریافت کردهاید 🌹",
                          show_alert=True)
        return
    with closing(db()) as conn:
        conn.execute("UPDATE users SET trial_used=1 WHERE id=?",
                     (call.from_user.id,))
        conn.commit()
    service = create_service(call.from_user.id, "سرویس تست", 0.1, 1, is_trial=1)
    await edit_or_answer(
        call,
        "🎁 سرویس تست شما آماده شد\n"
        "────────────\n"
        "📦 0.1 گیگ ·\n"
        "📅 1 روز\n"
        "📄 فایل کانفیگ برایتان ارسال شد.",
        kb_trial_done())
    await send_config_file(bot, call.from_user.id, service)
    await call.answer()


# ---------------------------------------------------------------------------
# Handlers — guide / referral / support
# ---------------------------------------------------------------------------

@router.callback_query(F.data == "menu:guide")
async def cb_guide(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    await edit_or_answer(call, GUIDE_TEXT, kb_guide())
    await call.answer()


@router.callback_query(F.data == "menu:referral")
async def cb_referral(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    me = await bot.get_me()
    await edit_or_answer(call, referral_text(call.from_user.id, me.username),
                         kb_referral())
    await call.answer()


@router.callback_query(F.data == "menu:support")
async def cb_support(call: CallbackQuery, state: FSMContext, bot: Bot):
    await state.clear()
    if not await _guard(call, bot):
        return
    await edit_or_answer(
        call,
        "🎫 پشتیبانی\n"
        "────────────\n"
        "سؤال یا مشکلی دارید؟ پیام خود را برای پشتیبانی بفرستید.",
        kb_support())
    await call.answer()


@router.callback_query(F.data == "support:write")
async def cb_support_write(call: CallbackQuery, state: FSMContext, bot: Bot):
    if not await _guard(call, bot):
        return
    await state.set_state(ShopStates.waiting_support_text)
    await call.message.answer("🎫 پیام خود را برای پشتیبانی بنویسید:",
                              reply_markup=kb_back("menu:support"))
    await call.answer()


@router.message(ShopStates.waiting_support_text)
async def on_support_message(message: Message, state: FSMContext, bot: Bot):
    await state.clear()
    text = message.text or message.caption or ""
    photo_id = message.photo[-1].file_id if message.photo else ""
    with closing(db()) as conn:
        conn.execute(
            "INSERT INTO support_tickets(user_id, text, photo_file_id,"
            " created_at) VALUES(?, ?, ?, ?)",
            (message.from_user.id, text, photo_id, int(time.time())))
        conn.commit()
    user = get_user(message.from_user.id)
    uname = f"@{user['username']}" if user and user["username"] else "—"
    admin_text = (f"🎫 پیام پشتیبانی جدید\n"
                  f"👤 کاربر: {message.from_user.id} ({uname})\n"
                  f"💬 {text}")
    await notify_admins(bot, admin_text,
                        reply_markup=kb_support_admin(message.from_user.id),
                        photo=photo_id or None)
    await message.answer("✅ پیام شما برای پشتیبانی ارسال شد.")


# ---------------------------------------------------------------------------
# Handlers — admin panel (/admin)
# ---------------------------------------------------------------------------

SETTING_LABELS = {
    "card_number": "💳 شماره کارت",
    "card_holder": "👤 نام صاحب کارت",
    "gate_enabled": "🚪 گیت عضویت (1/0)",
    "gate_channel": "📢 کانال/گروه گیت",
    "min_wallet_charge": "➕ حداقل شارژ کیف پول (تومان)",
    "stars_rate": "⭐️ نرخ هر استار (تومان)",
    "referral_reward": "👥 پاداش دعوت (تومان)",
    "support_url": "🎧 لینک پشتیبانی",
}

USERS_PAGE_SIZE = 10


def admin_stats_text() -> str:
    with closing(db()) as conn:
        users = conn.execute("SELECT COUNT(*) c FROM users").fetchone()["c"]
        services = conn.execute("SELECT COUNT(*) c FROM services").fetchone()["c"]
        active = conn.execute(
            "SELECT COUNT(*) c FROM services WHERE expires_at > ?",
            (int(time.time()),)).fetchone()["c"]
        pending = conn.execute(
            "SELECT COUNT(*) c FROM invoices WHERE status='pending_review'"
        ).fetchone()["c"]
        paid = conn.execute(
            "SELECT COUNT(*) c FROM invoices WHERE status='paid'"
        ).fetchone()["c"]
        income = conn.execute(
            "SELECT COALESCE(SUM(amount),0) s FROM invoices WHERE status='paid'"
        ).fetchone()["s"]
        balances = conn.execute(
            "SELECT COALESCE(SUM(balance),0) s FROM users").fetchone()["s"]
    return (
        "📊 آمار ربات\n"
        "────────────\n"
        f"👥 کاربران: {fmt(users)}\n"
        f"📦 سرویس‌ها: {fmt(services)} (فعال: {fmt(active)})\n"
        f"🧾 فاکتور در انتظار بررسی: {fmt(pending)}\n"
        f"🧾 فاکتور پرداخت‌شده: {fmt(paid)}\n"
        f"💰 درآمد (فاکتورهای پرداخت‌شده): {money(income)}\n"
        f"👛 مجموع موجودی کیف پول‌ها: {money(balances)}"
    )


@router.message(Command("admin"))
async def cmd_admin(message: Message, state: FSMContext):
    await state.clear()
    if not is_admin(message.from_user.id):
        return
    await message.answer("🛠 پنل ادمین", reply_markup=kb_admin_main())


@router.callback_query(F.data == "admin:main")
async def cb_admin_main(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    await edit_or_answer(call, "🛠 پنل ادمین", kb_admin_main())
    await call.answer()


@router.callback_query(F.data == "admin:stats")
async def cb_admin_stats(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    await edit_or_answer(call, admin_stats_text(), kb_admin_back())
    await call.answer()


@router.callback_query(F.data == "admin:receipts")
async def cb_admin_receipts(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM invoices WHERE status='pending_review'"
            " ORDER BY id DESC LIMIT 20").fetchall()
    if not rows:
        await edit_or_answer(call, "🧾 رسیدی در انتظار بررسی نیست.",
                             kb_admin_back())
        await call.answer()
        return
    spec_rows = []
    for inv in rows:
        label = f"#{inv['id']} · {inv['kind']} · {fmt(inv['amount'])} تومان"
        spec_rows.append([B(label, f"admin:receipt:{inv['id']}")])
    spec_rows.append([B("↩️ بازگشت", "admin:main")])
    await edit_or_answer(call, "🧾 رسیدهای در انتظار — یکی را انتخاب کنید:",
                         styled_kb(spec_rows))
    await call.answer()


@router.callback_query(F.data.startswith("admin:receipt:"))
async def cb_admin_receipt_view(call: CallbackQuery, bot: Bot):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    invoice_id = int(call.data.rsplit(":", 1)[1])
    invoice = get_invoice(invoice_id)
    if invoice is None:
        await call.answer("یافت نشد.", show_alert=True)
        return
    caption = (f"🧾 فاکتور #{invoice_id}\n👤 کاربر: {invoice['user_id']}\n"
               f"💰 مبلغ: {money(invoice['amount'])}\n📦 نوع: {invoice['kind']}")
    if invoice["receipt_file_id"]:
        await bot.send_photo(call.from_user.id, invoice["receipt_file_id"],
                             caption=caption,
                             reply_markup=kb_admin_receipt(invoice_id))
    else:
        await call.message.answer(caption,
                                  reply_markup=kb_admin_receipt(invoice_id))
    await call.answer()


@router.callback_query(F.data.startswith("admin_approve:"))
async def cb_admin_approve(call: CallbackQuery, bot: Bot):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    invoice_id = int(call.data.split(":", 1)[1])
    invoice = get_invoice(invoice_id)
    if invoice is None or invoice["status"] == "paid":
        await call.answer("این فاکتور قبلاً پردازش شده است.", show_alert=True)
        return
    set_invoice_status(invoice_id, "paid")
    paid_invoice = get_invoice(invoice_id)
    await complete_paid_invoice(bot, paid_invoice)
    try:
        await call.message.edit_caption(
            caption=f"✅ فاکتور #{invoice_id} تأیید شد.")
    except Exception:
        try:
            await call.message.edit_text(f"✅ فاکتور #{invoice_id} تأیید شد.")
        except Exception:
            pass
    await call.answer("✅ تأیید شد")


@router.callback_query(F.data.startswith("admin_reject:"))
async def cb_admin_reject(call: CallbackQuery, bot: Bot):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    invoice_id = int(call.data.split(":", 1)[1])
    invoice = get_invoice(invoice_id)
    if invoice is None or invoice["status"] in ("paid", "rejected"):
        await call.answer("این فاکتور قبلاً پردازش شده است.", show_alert=True)
        return
    set_invoice_status(invoice_id, "rejected")
    try:
        await bot.send_message(
            invoice["user_id"],
            f"❌ رسید فاکتور #{invoice_id} رد شد. در صورت اشتباه با پشتیبانی در تماس باشید.")
    except Exception:
        pass
    try:
        await call.message.edit_caption(
            caption=f"❌ فاکتور #{invoice_id} رد شد.")
    except Exception:
        try:
            await call.message.edit_text(f"❌ فاکتور #{invoice_id} رد شد.")
        except Exception:
            pass
    await call.answer("❌ رد شد")


# --- Admin: products --------------------------------------------------------

@router.callback_query(F.data == "admin:products")
async def cb_admin_products(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    products = get_products(active_only=False)
    spec_rows = []
    for p in products:
        label = f"{p['name']} · {p['volume_label']} · {fmt(p['price'])} تومان"
        spec_rows.append([B(label, f"admin:product:{p['id']}")])
    spec_rows.append([B("↩️ بازگشت", "admin:main")])
    await edit_or_answer(call, "🛒 محصولات — یکی را انتخاب کنید:",
                         styled_kb(spec_rows))
    await call.answer()


@router.callback_query(F.data.startswith("admin:product:"))
async def cb_admin_product(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    product_id = call.data.rsplit(":", 1)[1]
    p = get_product(product_id)
    if p is None:
        await call.answer("یافت نشد.", show_alert=True)
        return
    text = (f"🛒 {p['name']}\n📦 حجم: {p['volume_label']} ({p['gb']:g} گیگ)\n"
            f"📅 مدت: {int(p['days'])} روز\n💳 قیمت: {money(p['price'])}\n"
            f"وضعیت: {'فعال' if p['active'] else 'غیرفعال'}")
    kb = styled_kb([
        [B("💳 ویرایش قیمت", f"admin:edit_price:{product_id}")],
        [B("📦 ویرایش حجم", f"admin:edit_volume:{product_id}")],
        [B("🔄 فعال/غیرفعال", f"admin:toggle_product:{product_id}")],
        [B("↩️ بازگشت", "admin:products")],
    ])
    await edit_or_answer(call, text, kb)
    await call.answer()


@router.callback_query(F.data.startswith("admin:toggle_product:"))
async def cb_admin_toggle_product(call: CallbackQuery):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    product_id = call.data.rsplit(":", 1)[1]
    with closing(db()) as conn:
        conn.execute(
            "UPDATE products SET active = 1 - active WHERE id=?", (product_id,))
        conn.commit()
    await call.answer("وضعیت محصول تغییر کرد.")


@router.callback_query(F.data.startswith("admin:edit_price:"))
async def cb_admin_edit_price(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    product_id = call.data.rsplit(":", 1)[1]
    await state.set_state(AdminStates.waiting_product_price)
    await state.update_data(product_id=product_id)
    await call.message.answer("💳 قیمت جدید را به تومان بفرستید:")
    await call.answer()


@router.message(AdminStates.waiting_product_price, F.text)
async def on_admin_product_price(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    try:
        price = int((message.text or "").replace(",", "").strip())
    except ValueError:
        await message.answer("❌ قیمت معتبر نیست.")
        return
    with closing(db()) as conn:
        conn.execute("UPDATE products SET price=? WHERE id=?",
                     (price, data.get("product_id")))
        conn.commit()
    await message.answer(f"✅ قیمت به‌روز شد: {money(price)}",
                         reply_markup=kb_admin_back())


@router.callback_query(F.data.startswith("admin:edit_volume:"))
async def cb_admin_edit_volume(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    product_id = call.data.rsplit(":", 1)[1]
    await state.set_state(AdminStates.waiting_product_volume)
    await state.update_data(product_id=product_id)
    await call.message.answer(
        "📦 حجم جدید را بفرستید — قالب: «برچسب|گیگ» مثال: 5 گیگ|5")
    await call.answer()


@router.message(AdminStates.waiting_product_volume, F.text)
async def on_admin_product_volume(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    parts = (message.text or "").split("|")
    if len(parts) != 2:
        await message.answer("❌ قالب نادرست است. مثال: 5 گیگ|5")
        return
    label, gb_raw = parts[0].strip(), parts[1].strip()
    try:
        gb = float(gb_raw)
    except ValueError:
        await message.answer("❌ عدد حجم معتبر نیست.")
        return
    with closing(db()) as conn:
        conn.execute(
            "UPDATE products SET volume_label=?, gb=? WHERE id=?",
            (label, gb, data.get("product_id")))
        conn.commit()
    await message.answer("✅ حجم به‌روز شد.", reply_markup=kb_admin_back())


# --- Admin: settings --------------------------------------------------------

@router.callback_query(F.data == "admin:settings")
async def cb_admin_settings(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    spec_rows = []
    for key, label in SETTING_LABELS.items():
        spec_rows.append([B(f"{label}: {get_setting(key)}",
                            f"admin:set:{key}")])
    spec_rows.append([B("↩️ بازگشت", "admin:main")])
    await edit_or_answer(call, "⚙️ تنظیمات — برای ویرایش انتخاب کنید:",
                         styled_kb(spec_rows))
    await call.answer()


@router.callback_query(F.data.startswith("admin:set:"))
async def cb_admin_set(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    key = call.data.split(":", 2)[2]
    await state.set_state(AdminStates.waiting_setting_value)
    await state.update_data(setting_key=key)
    await call.message.answer(
        f"⚙️ مقدار جدید برای «{SETTING_LABELS.get(key, key)}» را بفرستید:"
        f"\nمقدار فعلی: {get_setting(key)}")
    await call.answer()


@router.message(AdminStates.waiting_setting_value, F.text)
async def on_admin_setting_value(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    key = data.get("setting_key", "")
    set_setting(key, (message.text or "").strip())
    await message.answer("✅ تنظیمات ذخیره شد.", reply_markup=kb_admin_back())


# --- Admin: discount & gift codes ------------------------------------------

@router.callback_query(F.data == "admin:discounts")
async def cb_admin_discounts(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    with closing(db()) as conn:
        rows = conn.execute("SELECT * FROM discount_codes").fetchall()
    lines = ["🎟 کدهای تخفیف:"]
    spec_rows = []
    for r in rows:
        lines.append(f"• {r['code']} — {r['percent']}٪ — استفاده: {r['used']}"
                     f"/{r['max_uses'] or '∞'} — {'فعال' if r['active'] else 'غیرفعال'}")
        spec_rows.append([B(f"🗑 حذف {r['code']}",
                            f"admin:discount_del:{r['code']}")])
    spec_rows.append([B("➕ کد جدید", "admin:discount_add")])
    spec_rows.append([B("↩️ بازگشت", "admin:main")])
    await edit_or_answer(call, "\n".join(lines), styled_kb(spec_rows))
    await call.answer()


@router.callback_query(F.data == "admin:discount_add")
async def cb_admin_discount_add(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    await state.set_state(AdminStates.waiting_discount_new)
    await call.message.answer(
        "🎟 کد جدید را بفرستید — قالب: «کد درصد سقف‌استفاده» مثال: OFF20 20 100"
        " (سقف 0 = نامحدود)")
    await call.answer()


@router.message(AdminStates.waiting_discount_new, F.text)
async def on_admin_discount_new(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    await state.clear()
    parts = (message.text or "").split()
    if len(parts) != 3:
        await message.answer("❌ قالب نادرست است. مثال: OFF20 20 100")
        return
    try:
        percent, max_uses = int(parts[1]), int(parts[2])
    except ValueError:
        await message.answer("❌ درصد/سقف باید عدد باشد.")
        return
    with closing(db()) as conn:
        conn.execute(
            "INSERT OR REPLACE INTO discount_codes(code, percent, active,"
            " max_uses, used) VALUES(?, ?, 1, ?, 0)",
            (parts[0], percent, max_uses))
        conn.commit()
    await message.answer("✅ کد تخفیف ساخته شد.", reply_markup=kb_admin_back())


@router.callback_query(F.data.startswith("admin:discount_del:"))
async def cb_admin_discount_del(call: CallbackQuery):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    code = call.data.rsplit(":", 1)[1] if ":" in call.data else ""
    code = call.data[len("admin:discount_del:"):]
    with closing(db()) as conn:
        conn.execute("DELETE FROM discount_codes WHERE code=?", (code,))
        conn.commit()
    await call.answer("🗑 حذف شد.")


@router.callback_query(F.data == "admin:gifts")
async def cb_admin_gifts(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    with closing(db()) as conn:
        rows = conn.execute(
            "SELECT * FROM gift_codes ORDER BY created_at DESC LIMIT 30"
        ).fetchall()
    lines = ["🎁 کدهای هدیه:"]
    for r in rows:
        status = f"استفاده‌شده توسط {r['used_by']}" if r["used_by"] else "استفاده‌نشده"
        lines.append(f"• {r['code']} — {money(r['amount_toman'])} — {status}")
    kb = styled_kb([
        [B("➕ کد جدید", "admin:gift_add")],
        [B("↩️ بازگشت", "admin:main")],
    ])
    await edit_or_answer(call, "\n".join(lines), kb)
    await call.answer()


@router.callback_query(F.data == "admin:gift_add")
async def cb_admin_gift_add(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    await state.set_state(AdminStates.waiting_gift_new)
    await call.message.answer(
        "🎁 کد جدید را بفرستید — قالب: «کد مبلغ‌تومان» مثال: GIFT1 50000")
    await call.answer()


@router.message(AdminStates.waiting_gift_new, F.text)
async def on_admin_gift_new(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    await state.clear()
    parts = (message.text or "").split()
    if len(parts) != 2:
        await message.answer("❌ قالب نادرست است. مثال: GIFT1 50000")
        return
    try:
        amount = int(parts[1].replace(",", ""))
    except ValueError:
        await message.answer("❌ مبلغ باید عدد باشد.")
        return
    with closing(db()) as conn:
        conn.execute(
            "INSERT OR REPLACE INTO gift_codes(code, amount_toman, used_by,"
            " created_at) VALUES(?, ?, 0, ?)",
            (parts[0], amount, int(time.time())))
        conn.commit()
    await message.answer("✅ کد هدیه ساخته شد.", reply_markup=kb_admin_back())


# --- Admin: users -----------------------------------------------------------

@router.callback_query(F.data.startswith("admin:users:"))
async def cb_admin_users(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    try:
        page = int(call.data.rsplit(":", 1)[1])
    except ValueError:
        page = 0
    with closing(db()) as conn:
        total = conn.execute("SELECT COUNT(*) c FROM users").fetchone()["c"]
        rows = conn.execute(
            "SELECT * FROM users ORDER BY id DESC LIMIT ? OFFSET ?",
            (USERS_PAGE_SIZE, page * USERS_PAGE_SIZE)).fetchall()
    spec_rows = []
    for u in rows:
        uname = f"@{u['username']}" if u["username"] else u["first_name"]
        label = f"{u['id']} · {uname} · {fmt(u['balance'])} تومان"
        if u["banned"]:
            label = "⛔️ " + label
        spec_rows.append([B(label, f"admin:user:{u['id']}")])
    nav = []
    if page > 0:
        nav.append(B("⬅️ قبلی", f"admin:users:{page - 1}"))
    if (page + 1) * USERS_PAGE_SIZE < total:
        nav.append(B("➡️ بعدی", f"admin:users:{page + 1}"))
    if nav:
        spec_rows.append(nav)
    spec_rows.append([B("↩️ بازگشت", "admin:main")])
    await edit_or_answer(
        call, f"👥 کاربران (صفحه {page + 1} — مجموع {fmt(total)}):",
        styled_kb(spec_rows))
    await call.answer()


@router.callback_query(F.data.startswith("admin:user:"))
async def cb_admin_user(call: CallbackQuery, state: FSMContext):
    await state.clear()
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    user_id = int(call.data.rsplit(":", 1)[1])
    u = get_user(user_id)
    if u is None:
        await call.answer("یافت نشد.", show_alert=True)
        return
    text = (
        f"👤 کاربر {u['id']}\n"
        f"نام: {u['first_name']} · @{u['username'] or '—'}\n"
        f"👛 موجودی: {money(u['balance'])}\n"
        f"👥 معرف: {u['referrer_id'] or '—'} · درآمد دعوت: {money(u['referral_earned'])}\n"
        f"🎁 تست استفاده‌شده: {'بله' if u['trial_used'] else 'خیر'}\n"
        f"وضعیت: {'⛔️ مسدود' if u['banned'] else 'فعال'}"
    )
    kb = styled_kb([
        [B("➕ افزودن موجودی", f"admin:user_add:{user_id}"),
         B("➖ کسر موجودی", f"admin:user_deduct:{user_id}")],
        [B("⛔️ بن / آن‌بن", f"admin:user_ban:{user_id}")],
        [B("↩️ بازگشت", "admin:users:0")],
    ])
    await edit_or_answer(call, text, kb)
    await call.answer()


@router.callback_query(F.data.startswith("admin:user_add:"))
@router.callback_query(F.data.startswith("admin:user_deduct:"))
async def cb_admin_user_balance(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    mode = "add" if ":user_add:" in call.data else "deduct"
    user_id = int(call.data.rsplit(":", 1)[1])
    await state.set_state(AdminStates.waiting_user_balance)
    await state.update_data(user_id=user_id, mode=mode)
    await call.message.answer("💰 مبلغ را به تومان بفرستید:")
    await call.answer()


@router.message(AdminStates.waiting_user_balance, F.text)
async def on_admin_user_balance(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    try:
        amount = int((message.text or "").replace(",", "").strip())
    except ValueError:
        await message.answer("❌ مبلغ معتبر نیست.")
        return
    delta = amount if data.get("mode") == "add" else -amount
    new_balance = change_balance(int(data.get("user_id", 0)), delta)
    await message.answer(f"✅ موجودی جدید کاربر: {money(new_balance)}",
                         reply_markup=kb_admin_back())


@router.callback_query(F.data.startswith("admin:user_ban:"))
async def cb_admin_user_ban(call: CallbackQuery):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    user_id = int(call.data.rsplit(":", 1)[1])
    with closing(db()) as conn:
        conn.execute(
            "UPDATE users SET banned = 1 - banned WHERE id=?", (user_id,))
        conn.commit()
    await call.answer("وضعیت بن کاربر تغییر کرد.")


# --- Admin: broadcast -------------------------------------------------------

@router.callback_query(F.data == "admin:broadcast")
async def cb_admin_broadcast(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    await state.set_state(AdminStates.waiting_broadcast)
    await call.message.answer(
        "📣 پیام همگانی را بفرستید (متن یا عکس با کپشن):")
    await call.answer()


@router.message(AdminStates.waiting_broadcast)
async def on_admin_broadcast(message: Message, state: FSMContext):
    if not is_admin(message.from_user.id):
        return
    text = message.text or message.caption or ""
    photo_id = message.photo[-1].file_id if message.photo else ""
    if not text and not photo_id:
        await message.answer("❌ پیام خالی است؛ متن یا عکس بفرستید.")
        return
    await state.set_state(AdminStates.waiting_broadcast_confirm)
    await state.update_data(text=text, photo_file_id=photo_id)
    if photo_id:
        await message.answer_photo(photo_id, caption=text,
                                   reply_markup=kb_confirm_broadcast())
    else:
        await message.answer(text, reply_markup=kb_confirm_broadcast())


@router.callback_query(F.data == "admin:broadcast_confirm")
async def cb_admin_broadcast_confirm(call: CallbackQuery, state: FSMContext,
                                     bot: Bot):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    data = await state.get_data()
    await state.clear()
    text = data.get("text", "")
    photo_id = data.get("photo_file_id", "")
    with closing(db()) as conn:
        user_ids = [r["id"] for r in
                    conn.execute("SELECT id FROM users").fetchall()]
    ok = fail = 0
    for uid in user_ids:
        try:
            if photo_id:
                await bot.send_photo(uid, photo_id, caption=text)
            else:
                await bot.send_message(uid, text)
            ok += 1
        except Exception:
            fail += 1
    await call.message.answer(
        f"📣 ارسال همگانی تمام شد.\n✅ موفق: {fmt(ok)}\n❌ ناموفق: {fmt(fail)}",
        reply_markup=kb_admin_back())
    await call.answer()


# --- Admin: support reply ---------------------------------------------------

@router.callback_query(F.data.startswith("support_reply:"))
async def cb_support_reply(call: CallbackQuery, state: FSMContext):
    if not is_admin(call.from_user.id):
        await call.answer()
        return
    target_id = int(call.data.split(":", 1)[1])
    await state.set_state(AdminStates.waiting_support_reply)
    await state.update_data(target_user_id=target_id)
    await call.message.answer(
        f"✍️ پاسخ خود را برای کاربر {target_id} بنویسید:")
    await call.answer()


@router.message(AdminStates.waiting_support_reply)
async def on_support_reply(message: Message, state: FSMContext, bot: Bot):
    if not is_admin(message.from_user.id):
        return
    data = await state.get_data()
    await state.clear()
    target_id = int(data.get("target_user_id", 0))
    text = message.text or message.caption or ""
    photo_id = message.photo[-1].file_id if message.photo else ""
    try:
        if photo_id:
            await bot.send_photo(target_id, photo_id,
                                 caption=f"🎫 پاسخ پشتیبانی:\n{text}")
        else:
            await bot.send_message(target_id, f"🎫 پاسخ پشتیبانی:\n{text}")
        await message.answer("✅ پاسخ ارسال شد.", reply_markup=kb_admin_back())
    except Exception:
        await message.answer("❌ ارسال پاسخ ناموفق بود.",
                             reply_markup=kb_admin_back())


# ---------------------------------------------------------------------------
# Fallbacks — state rule: stray text while not waiting is ignored politely;
# /start and /cancel always exit waiting states (handled above).
# ---------------------------------------------------------------------------

@router.message(StateFilter(None), F.text)
async def on_plain_text(message: Message):
    # No active flow: nudge the user back to the menu.
    if message.text and message.text.startswith("/"):
        return
    await message.answer("از منوی اصلی استفاده کنید: /start")


# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------

async def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN is not set (see .env.example)")
    init_db()
    bot = Bot(BOT_TOKEN)
    dp = Dispatcher()
    dp.include_router(router)
    # Bot commands: /start (منوی اصلی) and /help (راهنمای اتصال)
    from aiogram.types import BotCommand
    await bot.set_my_commands([
        BotCommand(command="start", description="منوی اصلی"),
        BotCommand(command="help", description="راهنمای اتصال"),
    ])
    log.info("Abolfazl replica starting (admins: %s)", sorted(ADMIN_IDS))
    await dp.start_polling(bot)


if __name__ == "__main__":
    asyncio.run(main())
