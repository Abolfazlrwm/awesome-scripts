# -*- coding: utf-8 -*-
"""
====================================================================
 ربات فروشگاهی حرفه‌ای با تمام قابلیت‌های یک سایت مدرن
====================================================================

قابلیت‌ها:
  - کاتالوگ با تنوع محصول (رنگ/سایز/مدل)
  - سبد خرید با اعمال کوپن تخفیف
  - ثبت سفارش + پرداخت کارت به کارت (ارسال رسید)
  - سیستم ارجاع (رفرال) و پورسانت
  - پشتیبانی (تیکت‌ها با پاسخگویی)
  - مشاهده سفارشات قبلی خریدار
  - پنل مدیریت کامل (محصول، سفارش، پرداخت، تیکت، کاربران، آمار)
  - استفاده از دکمه‌های رنگی (style) در بخش‌های مدیریتی

تنظیمات اولیه (پایین همین فایل):
    BOT_TOKEN   -> از @BotFather
    ADMIN_IDS   -> لیست آیدی عددی مدیران (با @userinfobot بگیرید)

اجرا:
    python bot.py
====================================================================
"""

import logging
import sqlite3
import os
import re
import random
import string
from datetime import datetime, timedelta
from contextlib import closing

from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    ReplyKeyboardMarkup,
    ReplyKeyboardRemove,
)
from telegram.constants import ParseMode
from telegram.ext import (
    Application,
    CommandHandler,
    CallbackQueryHandler,
    MessageHandler,
    ConversationHandler,
    ContextTypes,
    filters,
)

# ====================================================================
#  تنظیمات اصلی – حتماً ویرایش کنید
# ====================================================================

BOT_TOKEN = "توکن ربات را اینجا وارد کنید"   # از @BotFather بگیرید
ADMIN_IDS = [123456789]                        # لیست آیدی عددی مدیران (با @userinfobot بگیرید)
CURRENCY = "تومان"
DB_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "shop_advanced.db")
REFERRAL_BONUS_PERCENT = 5  # درصد پورسانت برای معرف (۵٪)

# لاگ‌گیری
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)
logger = logging.getLogger(__name__)

# ====================================================================
#  دیتابیس (SQLite) – جداول جدید
# ====================================================================

def get_conn():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys = ON")
    return conn

def init_db():
    with closing(get_conn()) as conn, conn:
        conn.executescript("""
            PRAGMA foreign_keys = ON;

            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
            );

            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                description TEXT DEFAULT '',
                is_active INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            );

            -- تنوع محصول (رنگ، سایز، مدل)
            CREATE TABLE IF NOT EXISTS product_variants (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                variant_name TEXT NOT NULL,   -- مثلاً "قرمز - سایز ۴۰"
                price INTEGER NOT NULL,
                stock INTEGER NOT NULL DEFAULT 0,
                photo_file_id TEXT DEFAULT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                variant_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                username TEXT,
                full_name TEXT,
                phone TEXT,
                address TEXT,
                total_price INTEGER NOT NULL,
                discount_amount INTEGER DEFAULT 0,
                final_price INTEGER NOT NULL,
                status TEXT NOT NULL DEFAULT 'pending',   -- pending, paid, confirmed, shipped, cancelled
                payment_status TEXT DEFAULT 'unpaid',      -- unpaid, awaiting_verify, paid
                created_at TEXT NOT NULL,
                paid_at TEXT,
                tracking_code TEXT,
                coupon_code TEXT,
                referrer_id INTEGER,
                FOREIGN KEY (referrer_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                variant_name TEXT NOT NULL,
                product_name TEXT NOT NULL,
                quantity INTEGER NOT NULL,
                unit_price INTEGER NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );

            -- جدول کاربران (برای ذخیره کد معرف و کیف پول)
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                username TEXT,
                first_name TEXT,
                last_name TEXT,
                referral_code TEXT UNIQUE,
                referrer_id INTEGER,
                wallet_balance INTEGER DEFAULT 0,
                created_at TEXT,
                FOREIGN KEY (referrer_id) REFERENCES users(id)
            );

            -- جدول تراکنش‌های کیف پول (پورسانت‌ها)
            CREATE TABLE IF NOT EXISTS wallet_transactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                amount INTEGER NOT NULL,
                description TEXT,
                created_at TEXT,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            -- جدول کوپن‌های تخفیف
            CREATE TABLE IF NOT EXISTS coupons (
                code TEXT PRIMARY KEY,
                discount_type TEXT NOT NULL,  -- 'percent' or 'fixed'
                discount_value INTEGER NOT NULL,
                min_order_amount INTEGER DEFAULT 0,
                expires_at TEXT,
                usage_limit INTEGER DEFAULT 1,
                used_count INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1
            );

            -- جدول تیکت‌های پشتیبانی
            CREATE TABLE IF NOT EXISTS support_tickets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                subject TEXT,
                message TEXT,
                status TEXT DEFAULT 'open',   -- open, in_progress, closed
                created_at TEXT,
                updated_at TEXT,
                admin_response TEXT,
                responded_at TEXT
            );

            -- جدول پرداخت‌های کارت به کارت (رسیدها)
            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                card_number TEXT,
                receipt_photo_id TEXT,
                amount INTEGER NOT NULL,
                status TEXT DEFAULT 'pending',   -- pending, verified, rejected
                verified_by INTEGER,
                created_at TEXT,
                verified_at TEXT,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );

            -- ایندکس‌ها برای سرعت
            CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id);
            CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
            CREATE INDEX IF NOT EXISTS idx_payments_order_id ON payments(order_id);
            CREATE INDEX IF NOT EXISTS idx_tickets_user_id ON support_tickets(user_id);
        """)

# ====================================================================
#  توابع کمکی دیتابیس (کلاس‌های عملیاتی)
# ====================================================================

# ---- کاربران و رفرال ----------------------------------------------
def get_or_create_user(user_id, username, first_name, last_name="", referrer_code=None):
    with closing(get_conn()) as conn, conn:
        user = conn.execute("SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        if not user:
            referral_code = ''.join(random.choices(string.ascii_uppercase + string.digits, k=8))
            referrer_id = None
            if referrer_code:
                ref_user = conn.execute("SELECT id FROM users WHERE referral_code=?", (referrer_code,)).fetchone()
                if ref_user:
                    referrer_id = ref_user["id"]
            conn.execute(
                """INSERT INTO users (id, username, first_name, last_name, referral_code, referrer_id, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?)""",
                (user_id, username, first_name, last_name, referral_code, referrer_id, datetime.now().isoformat())
            )
            user = conn.execute("SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
        return user

def get_user_by_id(user_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM users WHERE id=?", (user_id,)).fetchone()

def get_user_by_referral_code(code):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM users WHERE referral_code=?", (code,)).fetchone()

def add_wallet_transaction(user_id, amount, description):
    with closing(get_conn()) as conn, conn:
        conn.execute(
            "INSERT INTO wallet_transactions (user_id, amount, description, created_at) VALUES (?, ?, ?, ?)",
            (user_id, amount, description, datetime.now().isoformat())
        )
        conn.execute("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id=?", (amount, user_id))

# ---- محصولات و واریانت‌ها --------------------------------------------
def db_add_category(name):
    with closing(get_conn()) as conn, conn:
        cur = conn.execute("INSERT INTO categories (name) VALUES (?)", (name,))
        return cur.lastrowid

def db_get_categories():
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM categories ORDER BY name").fetchall()

def db_delete_category(category_id):
    with closing(get_conn()) as conn, conn:
        conn.execute("DELETE FROM categories WHERE id=?", (category_id,))

def db_add_product(category_id, name, description=""):
    with closing(get_conn()) as conn, conn:
        cur = conn.execute(
            "INSERT INTO products (category_id, name, description) VALUES (?, ?, ?)",
            (category_id, name, description)
        )
        return cur.lastrowid

def db_add_variant(product_id, variant_name, price, stock, photo_file_id=None):
    with closing(get_conn()) as conn, conn:
        cur = conn.execute(
            """INSERT INTO product_variants (product_id, variant_name, price, stock, photo_file_id)
               VALUES (?, ?, ?, ?, ?)""",
            (product_id, variant_name, price, stock, photo_file_id)
        )
        return cur.lastrowid

def db_get_product(product_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM products WHERE id=? AND is_active=1", (product_id,)).fetchone()

def db_get_variants_by_product(product_id):
    with closing(get_conn()) as conn:
        return conn.execute(
            "SELECT * FROM product_variants WHERE product_id=? ORDER BY variant_name",
            (product_id,)
        ).fetchall()

def db_get_variant(variant_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM product_variants WHERE id=?", (variant_id,)).fetchone()

def db_update_variant_stock(variant_id, new_stock):
    with closing(get_conn()) as conn, conn:
        conn.execute("UPDATE product_variants SET stock=? WHERE id=?", (new_stock, variant_id))

def db_deactivate_product(product_id):
    with closing(get_conn()) as conn, conn:
        conn.execute("UPDATE products SET is_active=0 WHERE id=?", (product_id,))

# ---- تابع جدید: همه محصولات حتی بدون واریانت ----------
def db_get_all_products_with_possible_variants():
    """همه محصولات فعال را به همراه واریانت‌هایشان (در صورت وجود) برمی‌گرداند.
       اگر محصولی واریانت نداشته باشد، فیلدهای variant_id و غیره NULL خواهند بود.
    """
    with closing(get_conn()) as conn:
        return conn.execute("""
            SELECT p.id AS product_id, p.name AS product_name, p.description,
                   c.name AS category_name,
                   v.id AS variant_id, v.variant_name, v.price, v.stock, v.photo_file_id
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants v ON p.id = v.product_id
            WHERE p.is_active = 1
            ORDER BY c.name, p.name, v.variant_name
        """).fetchall()

# ---- سبد خرید ------------------------------------------------------
def db_add_to_cart(user_id, variant_id, quantity=1):
    with closing(get_conn()) as conn, conn:
        existing = conn.execute(
            "SELECT * FROM cart_items WHERE user_id=? AND variant_id=?",
            (user_id, variant_id)
        ).fetchone()
        if existing:
            conn.execute(
                "UPDATE cart_items SET quantity = quantity + ? WHERE id=?",
                (quantity, existing["id"])
            )
        else:
            conn.execute(
                "INSERT INTO cart_items (user_id, variant_id, quantity) VALUES (?, ?, ?)",
                (user_id, variant_id, quantity)
            )

def db_get_cart(user_id):
    with closing(get_conn()) as conn:
        return conn.execute("""
            SELECT ci.id AS cart_id, ci.quantity,
                   v.id AS variant_id, v.variant_name, v.price, v.stock,
                   p.id AS product_id, p.name AS product_name
            FROM cart_items ci
            JOIN product_variants v ON ci.variant_id = v.id
            JOIN products p ON v.product_id = p.id
            WHERE ci.user_id = ?
        """, (user_id,)).fetchall()

def db_clear_cart(user_id):
    with closing(get_conn()) as conn, conn:
        conn.execute("DELETE FROM cart_items WHERE user_id=?", (user_id,))

def db_remove_cart_item(cart_id):
    with closing(get_conn()) as conn, conn:
        conn.execute("DELETE FROM cart_items WHERE id=?", (cart_id,))

# ---- سفارشات -------------------------------------------------------
def db_create_order(user_id, username, full_name, phone, address, cart_items, coupon_code=None, referrer_id=None):
    total = sum(item["price"] * item["quantity"] for item in cart_items)
    discount = 0
    if coupon_code:
        coupon = db_get_coupon(coupon_code)
        if coupon and coupon["is_active"] and coupon["used_count"] < coupon["usage_limit"]:
            if coupon["expires_at"] is None or datetime.now() < datetime.fromisoformat(coupon["expires_at"]):
                if total >= coupon["min_order_amount"]:
                    if coupon["discount_type"] == "percent":
                        discount = int(total * coupon["discount_value"] / 100)
                    else:
                        discount = min(coupon["discount_value"], total)
                    conn = get_conn()
                    with conn:
                        conn.execute(
                            "UPDATE coupons SET used_count = used_count + 1 WHERE code = ?",
                            (coupon_code,)
                        )
    final_price = total - discount

    with closing(get_conn()) as conn, conn:
        cur = conn.execute(
            """INSERT INTO orders
               (user_id, username, full_name, phone, address, total_price, discount_amount, final_price,
                status, payment_status, created_at, coupon_code, referrer_id)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, ?, ?)""",
            (user_id, username, full_name, phone, address, total, discount, final_price,
             datetime.now().isoformat(), coupon_code, referrer_id)
        )
        order_id = cur.lastrowid
        for item in cart_items:
            conn.execute(
                """INSERT INTO order_items (order_id, variant_name, product_name, quantity, unit_price)
                   VALUES (?, ?, ?, ?, ?)""",
                (order_id, item["variant_name"], item["product_name"], item["quantity"], item["price"])
            )
            new_stock = max(0, item["stock"] - item["quantity"])
            conn.execute("UPDATE product_variants SET stock=? WHERE id=?", (new_stock, item["variant_id"]))
        conn.execute("DELETE FROM cart_items WHERE user_id=?", (user_id,))
    return order_id, final_price

def db_get_order(order_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM orders WHERE id=?", (order_id,)).fetchone()

def db_get_order_items(order_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM order_items WHERE order_id=?", (order_id,)).fetchall()

def db_update_order_status(order_id, status):
    with closing(get_conn()) as conn, conn:
        conn.execute("UPDATE orders SET status=? WHERE id=?", (status, order_id))

def db_update_order_payment_status(order_id, payment_status):
    with closing(get_conn()) as conn, conn:
        conn.execute("UPDATE orders SET payment_status=? WHERE id=?", (payment_status, order_id))

def db_get_user_orders(user_id, limit=20):
    with closing(get_conn()) as conn:
        return conn.execute(
            "SELECT * FROM orders WHERE user_id=? ORDER BY id DESC LIMIT ?",
            (user_id, limit)
        ).fetchall()

def db_get_all_orders(status=None, limit=20):
    with closing(get_conn()) as conn:
        if status:
            return conn.execute(
                "SELECT * FROM orders WHERE status=? ORDER BY id DESC LIMIT ?",
                (status, limit)
            ).fetchall()
        return conn.execute(
            "SELECT * FROM orders ORDER BY id DESC LIMIT ?", (limit,)
        ).fetchall()

def db_get_stats():
    with closing(get_conn()) as conn:
        total_orders = conn.execute("SELECT COUNT(*) c FROM orders").fetchone()["c"]
        total_revenue = conn.execute(
            "SELECT COALESCE(SUM(final_price), 0) s FROM orders WHERE status != 'cancelled'"
        ).fetchone()["s"]
        pending_orders = conn.execute("SELECT COUNT(*) c FROM orders WHERE status='pending'").fetchone()["c"]
        paid_orders = conn.execute("SELECT COUNT(*) c FROM orders WHERE payment_status='paid'").fetchone()["c"]
        products_count = conn.execute("SELECT COUNT(*) c FROM products WHERE is_active=1").fetchone()["c"]
        users_count = conn.execute("SELECT COUNT(*) c FROM users").fetchone()["c"]
        return {
            "total_orders": total_orders,
            "total_revenue": total_revenue,
            "pending_orders": pending_orders,
            "paid_orders": paid_orders,
            "products_count": products_count,
            "users_count": users_count,
        }

# ---- کوپن‌ها -------------------------------------------------------
def db_add_coupon(code, discount_type, discount_value, min_order_amount=0, expires_at=None, usage_limit=1):
    with closing(get_conn()) as conn, conn:
        conn.execute(
            """INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, expires_at, usage_limit)
               VALUES (?, ?, ?, ?, ?, ?)""",
            (code, discount_type, discount_value, min_order_amount, expires_at, usage_limit)
        )

def db_get_coupon(code):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM coupons WHERE code=?", (code,)).fetchone()

def db_get_all_coupons():
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM coupons ORDER BY code").fetchall()

def db_delete_coupon(code):
    with closing(get_conn()) as conn, conn:
        conn.execute("DELETE FROM coupons WHERE code=?", (code,))

# ---- پرداخت‌ها -----------------------------------------------------
def db_add_payment(order_id, user_id, card_number, receipt_photo_id, amount):
    with closing(get_conn()) as conn, conn:
        cur = conn.execute(
            """INSERT INTO payments (order_id, user_id, card_number, receipt_photo_id, amount, created_at)
               VALUES (?, ?, ?, ?, ?, ?)""",
            (order_id, user_id, card_number, receipt_photo_id, amount, datetime.now().isoformat())
        )
        return cur.lastrowid

def db_get_payment(payment_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM payments WHERE id=?", (payment_id,)).fetchone()

def db_update_payment_status(payment_id, status, verified_by=None):
    with closing(get_conn()) as conn, conn:
        if status == "verified":
            conn.execute(
                "UPDATE payments SET status=?, verified_by=?, verified_at=? WHERE id=?",
                (status, verified_by, datetime.now().isoformat(), payment_id)
            )
        else:
            conn.execute("UPDATE payments SET status=? WHERE id=?", (status, payment_id))

def db_get_pending_payments():
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM payments WHERE status='pending' ORDER BY id DESC").fetchall()

# ---- تیکت‌های پشتیبانی --------------------------------------------
def db_create_ticket(user_id, subject, message):
    with closing(get_conn()) as conn, conn:
        cur = conn.execute(
            """INSERT INTO support_tickets (user_id, subject, message, status, created_at, updated_at)
               VALUES (?, ?, ?, 'open', ?, ?)""",
            (user_id, subject, message, datetime.now().isoformat(), datetime.now().isoformat())
        )
        return cur.lastrowid

def db_get_tickets(status=None):
    with closing(get_conn()) as conn:
        if status:
            return conn.execute(
                "SELECT * FROM support_tickets WHERE status=? ORDER BY id DESC", (status,)
            ).fetchall()
        return conn.execute("SELECT * FROM support_tickets ORDER BY id DESC").fetchall()

def db_get_ticket(ticket_id):
    with closing(get_conn()) as conn:
        return conn.execute("SELECT * FROM support_tickets WHERE id=?", (ticket_id,)).fetchone()

def db_update_ticket_response(ticket_id, admin_response):
    with closing(get_conn()) as conn, conn:
        conn.execute(
            """UPDATE support_tickets SET admin_response=?, status='in_progress', responded_at=?, updated_at=?
               WHERE id=?""",
            (admin_response, datetime.now().isoformat(), datetime.now().isoformat(), ticket_id)
        )

def db_close_ticket(ticket_id):
    with closing(get_conn()) as conn, conn:
        conn.execute("UPDATE support_tickets SET status='closed', updated_at=? WHERE id=?", (datetime.now().isoformat(), ticket_id))

# ---- توابع کمکی عمومی ---------------------------------------------
STATUS_LABELS = {
    "pending": "⏳ در انتظار بررسی",
    "confirmed": "✅ تایید شده",
    "shipped": "📦 ارسال شده",
    "cancelled": "❌ لغو شده",
    "paid": "💰 پرداخت شده",
}
PAYMENT_STATUS_LABELS = {
    "unpaid": "پرداخت نشده",
    "awaiting_verify": "در انتظار تایید رسید",
    "paid": "پرداخت شده",
}

def is_admin(user_id: int) -> bool:
    return user_id in ADMIN_IDS

def format_price(amount: int) -> str:
    return f"{amount:,} {CURRENCY}"

def main_menu_keyboard(user_id: int) -> ReplyKeyboardMarkup:
    rows = [
        ["🛍 مشاهده محصولات", "🛒 سبد خرید"],
        ["🧾 سفارشات من", "📞 پشتیبانی"],
    ]
    if is_admin(user_id):
        rows.append(["⚙️ پنل مدیریت"])
    return ReplyKeyboardMarkup(rows, resize_keyboard=True)

def admin_menu_keyboard() -> ReplyKeyboardMarkup:
    rows = [
        ["➕ افزودن دسته", "➕ افزودن محصول", "➕ افزودن واریانت"],
        ["📋 لیست محصولات", "🧾 مدیریت سفارشات"],
        ["💳 مدیریت پرداخت‌ها", "🎫 مدیریت کوپن‌ها"],
        ["📩 تیکت‌های پشتیبانی", "📊 آمار فروش"],
        ["👥 کاربران", "🔙 بازگشت به منوی اصلی"],
    ]
    return ReplyKeyboardMarkup(rows, resize_keyboard=True)

# ====================================================================
#  حالت‌های مکالمه (ConversationHandler)
# ====================================================================
(
    ADD_PRODUCT_CATEGORY,
    ADD_PRODUCT_NAME,
    ADD_PRODUCT_DESCRIPTION,
    ADD_VARIANT_PRODUCT,
    ADD_VARIANT_NAME,
    ADD_VARIANT_PRICE,
    ADD_VARIANT_STOCK,
    ADD_VARIANT_PHOTO,
    CHECKOUT_NAME,
    CHECKOUT_PHONE,
    CHECKOUT_ADDRESS,
    CHECKOUT_COUPON,
    CHECKOUT_PAYMENT,
    PAYMENT_CARD_NUMBER,
    PAYMENT_RECEIPT,
    TICKET_SUBJECT,
    TICKET_MESSAGE,
    TICKET_RESPONSE,
    COUPON_CODE,
    COUPON_TYPE,
    COUPON_VALUE,
    COUPON_MIN_ORDER,
    COUPON_EXPIRY,
    COUPON_LIMIT,
) = range(24)

# ====================================================================
#  هندلرهای عمومی
# ====================================================================
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    args = context.args
    referrer_code = args[0] if args else None
    if referrer_code:
        context.user_data["referrer_code"] = referrer_code
    get_or_create_user(
        user_id=user.id,
        username=user.username,
        first_name=user.first_name,
        last_name=user.last_name or "",
        referrer_code=referrer_code
    )
    msg = f"سلام {user.first_name} 👋\nبه فروشگاه آنلاین خوش اومدی!\n"
    if referrer_code:
        ref_user = get_user_by_referral_code(referrer_code)
        if ref_user:
            msg += f"توسط @{ref_user['username'] or ref_user['first_name']} دعوت شدی!\n"
    msg += "از منوی پایین استفاده کن."
    await update.message.reply_text(msg, reply_markup=main_menu_keyboard(user.id))

async def cancel_conversation(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data.clear()
    await update.message.reply_text("عملیات لغو شد.", reply_markup=main_menu_keyboard(update.effective_user.id))
    return ConversationHandler.END

# ====================================================================
#  بخش مشتری: کاتالوگ با تنوع
# ====================================================================
async def show_categories(update: Update, context: ContextTypes.DEFAULT_TYPE):
    categories = db_get_categories()
    if not categories:
        await update.message.reply_text("فعلاً دسته‌بندی وجود ندارد.")
        return
    keyboard = [[InlineKeyboardButton(cat["name"], callback_data=f"cat:{cat['id']}")] for cat in categories]
    await update.message.reply_text("دسته‌بندی را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))

async def show_products_in_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    category_id = int(query.data.split(":")[1])
    with closing(get_conn()) as conn:
        products = conn.execute(
            "SELECT * FROM products WHERE category_id=? AND is_active=1", (category_id,)
        ).fetchall()
    if not products:
        await query.edit_message_text("محصولی در این دسته نیست.")
        return
    for product in products:
        text = f"🛍 <b>{product['name']}</b>\n{product['description'] or ''}"
        variants = db_get_variants_by_product(product['id'])
        if variants:
            keyboard = InlineKeyboardMarkup([
                [InlineKeyboardButton("🔽 انتخاب تنوع", callback_data=f"variants:{product['id']}", style="primary")]
            ])
        else:
            keyboard = InlineKeyboardMarkup([
                [InlineKeyboardButton("⛔ بدون تنوع (ناموجود)", callback_data="noop", style="danger")]
            ])
        photo_id = None
        for v in variants:
            if v['photo_file_id']:
                photo_id = v['photo_file_id']
                break
        if photo_id:
            await context.bot.send_photo(chat_id=query.message.chat_id, photo=photo_id, caption=text, parse_mode=ParseMode.HTML, reply_markup=keyboard)
        else:
            await context.bot.send_message(chat_id=query.message.chat_id, text=text, parse_mode=ParseMode.HTML, reply_markup=keyboard)

async def show_variants(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    product_id = int(query.data.split(":")[1])
    variants = db_get_variants_by_product(product_id)
    if not variants:
        await query.edit_message_text("این محصول هیچ تنوعی ندارد.")
        return
    keyboard = []
    for v in variants:
        btn_text = f"{v['variant_name']} - {format_price(v['price'])} (موجودی: {v['stock']})"
        keyboard.append([InlineKeyboardButton(btn_text, callback_data=f"add:{v['id']}", style="primary")])
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data=f"cat:{query.data.split(':')[1]}")])
    await query.edit_message_text("تنوع مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))

async def add_to_cart_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    variant_id = int(query.data.split(":")[1])
    variant = db_get_variant(variant_id)
    if not variant or variant["stock"] <= 0:
        await query.answer("این تنوع موجود نیست.", show_alert=True)
        return
    db_add_to_cart(query.from_user.id, variant_id, 1)
    await query.answer("به سبد خرید اضافه شد ✅")

# ====================================================================
#  سبد خرید و تسویه (با کوپن و پرداخت)
# ====================================================================
async def show_cart(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    items = db_get_cart(user_id)
    if not items:
        await update.message.reply_text("سبد خرید خالی است.")
        return
    text_lines = ["🛒 <b>سبد خرید شما:</b>\n"]
    total = 0
    keyboard_rows = []
    for item in items:
        subtotal = item["price"] * item["quantity"]
        total += subtotal
        text_lines.append(f"• {item['product_name']} - {item['variant_name']} × {item['quantity']} = {format_price(subtotal)}")
        keyboard_rows.append([InlineKeyboardButton(f"🗑 حذف", callback_data=f"rmcart:{item['cart_id']}", style="danger")])
    text_lines.append(f"\n💰 جمع کل: {format_price(total)}")
    coupon_code = context.user_data.get("coupon_code")
    if coupon_code:
        coupon = db_get_coupon(coupon_code)
        if coupon:
            discount = 0
            if coupon["discount_type"] == "percent":
                discount = int(total * coupon["discount_value"] / 100)
            else:
                discount = min(coupon["discount_value"], total)
            text_lines.append(f"🎫 تخفیف ({coupon_code}): -{format_price(discount)}")
            text_lines.append(f"💰 مبلغ قابل پرداخت: {format_price(total - discount)}")
    keyboard_rows.append([InlineKeyboardButton("🎫 اعمال کوپن", callback_data="apply_coupon", style="primary")])
    keyboard_rows.append([InlineKeyboardButton("✅ ثبت سفارش", callback_data="checkout", style="success")])
    keyboard_rows.append([InlineKeyboardButton("❌ خالی کردن سبد", callback_data="clearcart", style="danger")])
    await update.message.reply_text("\n".join(text_lines), parse_mode=ParseMode.HTML, reply_markup=InlineKeyboardMarkup(keyboard_rows))

async def remove_cart_item_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    cart_id = int(query.data.split(":")[1])
    db_remove_cart_item(cart_id)
    await query.answer("حذف شد")
    await query.edit_message_text("آیتم حذف شد. برای دیدن سبد به‌روز، دوباره «🛒 سبد خرید» را بزنید.")

async def clear_cart_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    db_clear_cart(query.from_user.id)
    await query.answer("سبد خرید خالی شد")
    await query.edit_message_text("سبد خرید شما خالی شد.")

async def apply_coupon_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("کد تخفیف را وارد کنید:")
    return CHECKOUT_COUPON

async def apply_coupon_text(update: Update, context: ContextTypes.DEFAULT_TYPE):
    code = update.message.text.strip().upper()
    coupon = db_get_coupon(code)
    if not coupon or not coupon["is_active"] or coupon["used_count"] >= coupon["usage_limit"]:
        await update.message.reply_text("کد نامعتبر یا منقضی شده.")
        return ConversationHandler.END
    if coupon["expires_at"] and datetime.now() > datetime.fromisoformat(coupon["expires_at"]):
        await update.message.reply_text("کد منقضی شده.")
        return ConversationHandler.END
    context.user_data["coupon_code"] = code
    await update.message.reply_text("کد تخفیف اعمال شد. دوباره سبد خرید را مشاهده کنید.")
    return ConversationHandler.END

coupon_conversation = ConversationHandler(
    entry_points=[CallbackQueryHandler(apply_coupon_callback, pattern="^apply_coupon$")],
    states={
        CHECKOUT_COUPON: [MessageHandler(filters.TEXT & ~filters.COMMAND, apply_coupon_text)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

async def checkout_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    items = db_get_cart(query.from_user.id)
    if not items:
        await query.edit_message_text("سبد خرید خالی است.")
        return ConversationHandler.END
    for item in items:
        if item["quantity"] > item["stock"]:
            await query.edit_message_text(f"موجودی {item['product_name']} - {item['variant_name']} کافی نیست.")
            return ConversationHandler.END
    await context.bot.send_message(chat_id=query.message.chat_id, text="لطفاً نام و نام خانوادگی خود را وارد کنید:", reply_markup=ReplyKeyboardRemove())
    return CHECKOUT_NAME

async def checkout_get_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data["full_name"] = update.message.text.strip()
    await update.message.reply_text("شماره تماس خود را وارد کنید:")
    return CHECKOUT_PHONE

async def checkout_get_phone(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data["phone"] = update.message.text.strip()
    await update.message.reply_text("آدرس کامل خود را وارد کنید:")
    return CHECKOUT_ADDRESS

async def checkout_get_address(update: Update, context: ContextTypes.DEFAULT_TYPE):
    address = update.message.text.strip()
    user = update.effective_user
    cart_items = db_get_cart(user.id)
    if not cart_items:
        await update.message.reply_text("سبد خرید خالی است.", reply_markup=main_menu_keyboard(user.id))
        return ConversationHandler.END
    user_row = get_user_by_id(user.id)
    referrer_id = user_row["referrer_id"] if user_row else None
    order_id, final_price = db_create_order(
        user_id=user.id,
        username=user.username or "",
        full_name=context.user_data.get("full_name", ""),
        phone=context.user_data.get("phone", ""),
        address=address,
        cart_items=cart_items,
        coupon_code=context.user_data.get("coupon_code"),
        referrer_id=referrer_id
    )
    context.user_data.pop("coupon_code", None)
    context.user_data["order_id"] = order_id
    await update.message.reply_text(
        f"✅ سفارش #{order_id} با موفقیت ثبت شد.\n"
        f"💰 مبلغ قابل پرداخت: {format_price(final_price)}\n\n"
        "جهت پرداخت، روش کارت به کارت را انتخاب کنید.",
        reply_markup=main_menu_keyboard(user.id)
    )
    keyboard = InlineKeyboardMarkup([
        [InlineKeyboardButton("💳 پرداخت کارت به کارت", callback_data=f"pay:{order_id}", style="success")],
        [InlineKeyboardButton("🔙 منوی اصلی", callback_data="main_menu")]
    ])
    await update.message.reply_text(
        "لطفاً یکی از گزینه‌های زیر را انتخاب کنید:",
        reply_markup=keyboard
    )
    return ConversationHandler.END

checkout_conversation = ConversationHandler(
    entry_points=[CallbackQueryHandler(checkout_start, pattern=r"^checkout$")],
    states={
        CHECKOUT_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, checkout_get_name)],
        CHECKOUT_PHONE: [MessageHandler(filters.TEXT & ~filters.COMMAND, checkout_get_phone)],
        CHECKOUT_ADDRESS: [MessageHandler(filters.TEXT & ~filters.COMMAND, checkout_get_address)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

# ====================================================================
#  پرداخت کارت به کارت
# ====================================================================
async def payment_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    order_id = int(query.data.split(":")[1])
    order = db_get_order(order_id)
    if not order:
        await query.edit_message_text("سفارش یافت نشد.")
        return ConversationHandler.END
    card_number = "6037-9975-1234-5678"
    await query.edit_message_text(
        f"💳 شماره کارت واریز:\n`{card_number}`\n\n"
        f"مبلغ قابل پرداخت: {format_price(order['final_price'])}\n\n"
        "پس از واریز، شماره کارت خود را وارد کنید:"
    )
    context.user_data["order_id"] = order_id
    return PAYMENT_CARD_NUMBER

async def payment_get_card(update: Update, context: ContextTypes.DEFAULT_TYPE):
    card = update.message.text.strip()
    context.user_data["card_number"] = card
    await update.message.reply_text("حالا تصویر رسید پرداخت را ارسال کنید:")
    return PAYMENT_RECEIPT

async def payment_get_receipt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    order_id = context.user_data.get("order_id")
    if not order_id:
        await update.message.reply_text("خطا، سفارش یافت نشد.")
        return ConversationHandler.END
    if not update.message.photo:
        await update.message.reply_text("لطفاً یک عکس ارسال کنید.")
        return PAYMENT_RECEIPT
    photo_id = update.message.photo[-1].file_id
    card_number = context.user_data.get("card_number", "")
    order = db_get_order(order_id)
    if not order:
        await update.message.reply_text("سفارش نامعتبر.")
        return ConversationHandler.END
    payment_id = db_add_payment(order_id, user.id, card_number, photo_id, order["final_price"])
    await update.message.reply_text(
        "✅ رسید شما دریافت شد. پس از تأیید مدیر، سفارش شما پردازش خواهد شد.",
        reply_markup=main_menu_keyboard(user.id)
    )
    admin_text = (
        f"💰 <b>پرداخت جدید</b>\n"
        f"سفارش #{order_id}\n"
        f"مبلغ: {format_price(order['final_price'])}\n"
        f"شماره کارت: {card_number}\n"
        f"کاربر: {user.first_name} (@{user.username or 'no_username'})"
    )
    keyboard = InlineKeyboardMarkup([
        [InlineKeyboardButton("✅ تأیید پرداخت", callback_data=f"verify_payment:{payment_id}", style="success")],
        [InlineKeyboardButton("❌ رد پرداخت", callback_data=f"reject_payment:{payment_id}", style="danger")]
    ])
    for admin_id in ADMIN_IDS:
        await context.bot.send_photo(chat_id=admin_id, photo=photo_id, caption=admin_text, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    context.user_data.clear()
    return ConversationHandler.END

payment_conversation = ConversationHandler(
    entry_points=[CallbackQueryHandler(payment_start, pattern=r"^pay:\d+$")],
    states={
        PAYMENT_CARD_NUMBER: [MessageHandler(filters.TEXT & ~filters.COMMAND, payment_get_card)],
        PAYMENT_RECEIPT: [MessageHandler(filters.PHOTO, payment_get_receipt)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

async def verify_payment_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if not is_admin(query.from_user.id):
        await query.answer("دسترسی ندارید.", show_alert=True)
        return
    action, payment_id = query.data.split(":")
    payment_id = int(payment_id)
    payment = db_get_payment(payment_id)
    if not payment:
        await query.answer("پرداخت یافت نشد.")
        return
    if action == "verify_payment":
        db_update_payment_status(payment_id, "verified", verified_by=query.from_user.id)
        db_update_order_payment_status(payment["order_id"], "paid")
        db_update_order_status(payment["order_id"], "confirmed")
        await query.answer("پرداخت تأیید شد.")
        try:
            await context.bot.send_message(
                chat_id=payment["user_id"],
                text=f"✅ پرداخت سفارش #{payment['order_id']} تأیید شد. سفارش شما در حال پردازش است."
            )
        except:
            pass
        order = db_get_order(payment["order_id"])
        if order and order["referrer_id"]:
            bonus = int(order["final_price"] * REFERRAL_BONUS_PERCENT / 100)
            if bonus > 0:
                add_wallet_transaction(order["referrer_id"], bonus, f"پورسانت معرفی برای سفارش #{order['id']}")
                try:
                    ref_user = get_user_by_id(order["referrer_id"])
                    if ref_user:
                        await context.bot.send_message(
                            chat_id=order["referrer_id"],
                            text=f"🎉 پورسانت معرفی به مبلغ {format_price(bonus)} به کیف پول شما اضافه شد."
                        )
                except:
                    pass
        await query.edit_message_text(f"✅ پرداخت تأیید شد. سفارش #{payment['order_id']} اکنون تأیید شده است.")
    else:
        db_update_payment_status(payment_id, "rejected", verified_by=query.from_user.id)
        db_update_order_payment_status(payment["order_id"], "unpaid")
        await query.answer("پرداخت رد شد.")
        try:
            await context.bot.send_message(
                chat_id=payment["user_id"],
                text=f"❌ پرداخت سفارش #{payment['order_id']} رد شد. لطفاً با پشتیبانی تماس بگیرید."
            )
        except:
            pass
        await query.edit_message_text(f"❌ پرداخت رد شد. سفارش #{payment['order_id']} لغو شد.")
    await query.answer()

# ====================================================================
#  سفارشات من
# ====================================================================
async def my_orders(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    orders = db_get_user_orders(user_id)
    if not orders:
        await update.message.reply_text("شما هیچ سفارشی ندارید.")
        return
    for order in orders:
        items = db_get_order_items(order["id"])
        items_text = "\n".join([f"• {it['product_name']} - {it['variant_name']} × {it['quantity']}" for it in items])
        text = (
            f"🧾 <b>سفارش #{order['id']}</b>\n"
            f"📅 تاریخ: {order['created_at'][:10]}\n"
            f"💰 مبلغ: {format_price(order['final_price'])}\n"
            f"📌 وضعیت: {STATUS_LABELS.get(order['status'], order['status'])}\n"
            f"💳 پرداخت: {PAYMENT_STATUS_LABELS.get(order['payment_status'], order['payment_status'])}\n\n"
            f"🛍 اقلام:\n{items_text}"
        )
        await update.message.reply_text(text, parse_mode=ParseMode.HTML)

# ====================================================================
#  پشتیبانی (تیکت)
# ====================================================================
async def support_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text(
        "لطفاً موضوع پیام خود را وارد کنید:",
        reply_markup=ReplyKeyboardRemove()
    )
    return TICKET_SUBJECT

async def support_subject(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data["ticket_subject"] = update.message.text.strip()
    await update.message.reply_text("حالا پیام خود را بنویسید:")
    return TICKET_MESSAGE

async def support_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    subject = context.user_data.get("ticket_subject", "بدون موضوع")
    message = update.message.text.strip()
    user = update.effective_user
    ticket_id = db_create_ticket(user.id, subject, message)
    await update.message.reply_text(
        f"✅ تیکت شما با شماره #{ticket_id} ثبت شد.\n"
        "به‌زودی پاسخ داده می‌شود.",
        reply_markup=main_menu_keyboard(user.id)
    )
    admin_text = (
        f"📩 <b>تیکت جدید #{ticket_id}</b>\n"
        f"از: {user.first_name} (@{user.username or 'no_username'})\n"
        f"موضوع: {subject}\n"
        f"پیام: {message}"
    )
    keyboard = InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ پاسخ به تیکت", callback_data=f"reply_ticket:{ticket_id}", style="primary")]
    ])
    for admin_id in ADMIN_IDS:
        await context.bot.send_message(chat_id=admin_id, text=admin_text, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    return ConversationHandler.END

async def admin_reply_ticket_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if not is_admin(query.from_user.id):
        await query.answer("دسترسی ندارید.", show_alert=True)
        return
    ticket_id = int(query.data.split(":")[1])
    context.user_data["reply_ticket_id"] = ticket_id
    await query.answer()
    await query.edit_message_text("لطفاً پاسخ خود را بنویسید:")
    return TICKET_RESPONSE

async def admin_reply_ticket_text(update: Update, context: ContextTypes.DEFAULT_TYPE):
    ticket_id = context.user_data.get("reply_ticket_id")
    if not ticket_id:
        await update.message.reply_text("خطا.")
        return ConversationHandler.END
    response = update.message.text.strip()
    db_update_ticket_response(ticket_id, response)
    ticket = db_get_ticket(ticket_id)
    if ticket:
        try:
            await context.bot.send_message(
                chat_id=ticket["user_id"],
                text=f"📩 پاسخ به تیکت #{ticket_id}:\n\n{response}\n\nبرای بستن تیکت، /close_ticket {ticket_id} را بزنید."
            )
        except:
            pass
    await update.message.reply_text("پاسخ شما ارسال شد.", reply_markup=admin_menu_keyboard())
    return ConversationHandler.END

async def close_ticket_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if len(context.args) != 1:
        await update.message.reply_text("استفاده: /close_ticket <شماره تیکت>")
        return
    try:
        ticket_id = int(context.args[0])
        ticket = db_get_ticket(ticket_id)
        if not ticket or ticket["user_id"] != update.effective_user.id:
            await update.message.reply_text("تیکت یافت نشد یا متعلق به شما نیست.")
            return
        db_close_ticket(ticket_id)
        await update.message.reply_text(f"تیکت #{ticket_id} بسته شد.")
    except ValueError:
        await update.message.reply_text("شناسه نامعتبر.")

# ====================================================================
#  پنل مدیریت – محصولات و واریانت‌ها
# ====================================================================
async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        await update.message.reply_text("دسترسی محدود.")
        return
    await update.message.reply_text("پنل مدیریت:", reply_markup=admin_menu_keyboard())

async def add_category_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return ConversationHandler.END
    await update.message.reply_text("نام دسته‌بندی جدید را وارد کنید (یا /cancel برای لغو):", reply_markup=ReplyKeyboardRemove())
    return ADD_PRODUCT_CATEGORY

async def add_category_save(update: Update, context: ContextTypes.DEFAULT_TYPE):
    name = update.message.text.strip()
    try:
        db_add_category(name)
        await update.message.reply_text(f"دسته {name} اضافه شد ✅", reply_markup=admin_menu_keyboard())
    except sqlite3.IntegrityError:
        await update.message.reply_text("این دسته قبلاً وجود دارد.", reply_markup=admin_menu_keyboard())
    return ConversationHandler.END

add_category_conversation = ConversationHandler(
    entry_points=[MessageHandler(filters.Regex("^➕ افزودن دسته$"), add_category_start)],
    states={ADD_PRODUCT_CATEGORY: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_category_save)]},
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

async def add_product_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return ConversationHandler.END
    categories = db_get_categories()
    if not categories:
        await update.message.reply_text("ابتدا دسته‌بندی بسازید.")
        return ConversationHandler.END
    keyboard = [[InlineKeyboardButton(cat["name"], callback_data=f"addprod_cat:{cat['id']}")] for cat in categories]
    await update.message.reply_text("محصول در کدام دسته باشد؟", reply_markup=InlineKeyboardMarkup(keyboard))
    return ADD_PRODUCT_CATEGORY

async def add_product_category_chosen(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    cat_id = int(query.data.split(":")[1])
    context.user_data["new_product_cat"] = cat_id
    await query.edit_message_text("نام محصول را وارد کنید:")
    return ADD_PRODUCT_NAME

async def add_product_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data["new_product_name"] = update.message.text.strip()
    await update.message.reply_text("توضیحات محصول را وارد کنید (یا '-' برای رد کردن):")
    return ADD_PRODUCT_DESCRIPTION

async def add_product_description(update: Update, context: ContextTypes.DEFAULT_TYPE):
    desc = update.message.text.strip()
    if desc == "-":
        desc = ""
    context.user_data["new_product_desc"] = desc
    product_id = db_add_product(
        category_id=context.user_data["new_product_cat"],
        name=context.user_data["new_product_name"],
        description=desc
    )
    context.user_data["product_id"] = product_id
    await update.message.reply_text(
        f"محصول «{context.user_data['new_product_name']}» اضافه شد. اکنون می‌توانید واریانت‌های آن را اضافه کنید.\n"
        "از منوی مدیریت گزینه «➕ افزودن واریانت» را انتخاب کنید.",
        reply_markup=admin_menu_keyboard()
    )
    return ConversationHandler.END

add_product_conversation = ConversationHandler(
    entry_points=[MessageHandler(filters.Regex("^➕ افزودن محصول$"), add_product_start)],
    states={
        ADD_PRODUCT_CATEGORY: [CallbackQueryHandler(add_product_category_chosen, pattern=r"^addprod_cat:")],
        ADD_PRODUCT_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_product_name)],
        ADD_PRODUCT_DESCRIPTION: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_product_description)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

async def add_variant_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return ConversationHandler.END
    products = db_get_all_products_with_possible_variants()
    unique_products = {}
    for row in products:
        if row["product_id"] not in unique_products:
            unique_products[row["product_id"]] = row["product_name"]
    if not unique_products:
        await update.message.reply_text("هیچ محصولی موجود نیست. ابتدا محصول اضافه کنید.")
        return ConversationHandler.END
    keyboard = [[InlineKeyboardButton(name, callback_data=f"variant_prod:{pid}")] for pid, name in unique_products.items()]
    await update.message.reply_text("محصول مورد نظر را انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))
    return ADD_VARIANT_PRODUCT

async def add_variant_product_chosen(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    product_id = int(query.data.split(":")[1])
    context.user_data["variant_product_id"] = product_id
    await query.edit_message_text("نام واریانت را وارد کنید (مثلاً 'قرمز - سایز ۴۰'):")
    return ADD_VARIANT_NAME

async def add_variant_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data["variant_name"] = update.message.text.strip()
    await update.message.reply_text("قیمت این واریانت را به تومان وارد کنید (فقط عدد):")
    return ADD_VARIANT_PRICE

async def add_variant_price(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        price = int(update.message.text.strip().replace(",", ""))
        context.user_data["variant_price"] = price
        await update.message.reply_text("موجودی انبار را وارد کنید:")
        return ADD_VARIANT_STOCK
    except ValueError:
        await update.message.reply_text("عدد معتبر وارد کنید.")
        return ADD_VARIANT_PRICE

async def add_variant_stock(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        stock = int(update.message.text.strip())
        context.user_data["variant_stock"] = stock
        await update.message.reply_text("(اختیاری) یک عکس برای این واریانت ارسال کنید یا '-' برای رد کردن:")
        return ADD_VARIANT_PHOTO
    except ValueError:
        await update.message.reply_text("عدد معتبر وارد کنید.")
        return ADD_VARIANT_STOCK

async def add_variant_photo(update: Update, context: ContextTypes.DEFAULT_TYPE):
    photo_id = None
    if update.message.photo:
        photo_id = update.message.photo[-1].file_id
    elif update.message.text and update.message.text.strip() != "-":
        await update.message.reply_text("لطفاً عکس ارسال کنید یا '-' بفرستید.")
        return ADD_VARIANT_PHOTO
    product_id = context.user_data["variant_product_id"]
    variant_id = db_add_variant(
        product_id=product_id,
        variant_name=context.user_data["variant_name"],
        price=context.user_data["variant_price"],
        stock=context.user_data["variant_stock"],
        photo_file_id=photo_id
    )
    await update.message.reply_text(
        f"✅ واریانت «{context.user_data['variant_name']}» با موفقیت اضافه شد.",
        reply_markup=admin_menu_keyboard()
    )
    return ConversationHandler.END

add_variant_conversation = ConversationHandler(
    entry_points=[MessageHandler(filters.Regex("^➕ افزودن واریانت$"), add_variant_start)],
    states={
        ADD_VARIANT_PRODUCT: [CallbackQueryHandler(add_variant_product_chosen, pattern=r"^variant_prod:")],
        ADD_VARIANT_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_variant_name)],
        ADD_VARIANT_PRICE: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_variant_price)],
        ADD_VARIANT_STOCK: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_variant_stock)],
        ADD_VARIANT_PHOTO: [MessageHandler((filters.PHOTO | filters.TEXT) & ~filters.COMMAND, add_variant_photo)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

async def admin_list_products(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    products = db_get_all_products_with_possible_variants()
    if not products:
        await update.message.reply_text("محصولی ثبت نشده.")
        return
    grouped = {}
    for row in products:
        pid = row["product_id"]
        if pid not in grouped:
            grouped[pid] = {
                "name": row["product_name"],
                "category": row["category_name"],
                "variants": []
            }
        if row["variant_id"] is not None:
            grouped[pid]["variants"].append({
                "variant_id": row["variant_id"],
                "variant_name": row["variant_name"],
                "price": row["price"],
                "stock": row["stock"]
            })
    for pid, data in grouped.items():
        text = f"🛍 <b>{data['name']}</b> (دسته: {data['category']})\n"
        if data["variants"]:
            for v in data["variants"]:
                text += f"   • {v['variant_name']} - {format_price(v['price'])} (موجودی: {v['stock']})\n"
        else:
            text += "   ⚠️ بدون واریانت (قابل خرید نیست)\n"
        keyboard = InlineKeyboardMarkup([
            [InlineKeyboardButton("🗑 حذف محصول", callback_data=f"delprod:{pid}", style="danger")]
        ])
        await update.message.reply_text(text, parse_mode=ParseMode.HTML, reply_markup=keyboard)

async def admin_delete_product_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if not is_admin(query.from_user.id):
        await query.answer("دسترسی ندارید.", show_alert=True)
        return
    product_id = int(query.data.split(":")[1])
    db_deactivate_product(product_id)
    await query.answer("محصول غیرفعال شد")
    await query.edit_message_text("✅ محصول غیرفعال شد.")

# ====================================================================
#  مدیریت سفارشات (ادمین)
# ====================================================================
async def admin_show_orders(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    orders = db_get_all_orders(limit=20)
    if not orders:
        await update.message.reply_text("سفارشی وجود ندارد.")
        return
    for order in orders:
        text = (
            f"🧾 سفارش #{order['id']}\n"
            f"👤 {order['full_name']} | 📞 {order['phone']}\n"
            f"💰 {format_price(order['final_price'])}"
            f" (تخفیف: {format_price(order['discount_amount'])})\n"
            f"📌 وضعیت: {STATUS_LABELS.get(order['status'], order['status'])}\n"
            f"💳 پرداخت: {PAYMENT_STATUS_LABELS.get(order['payment_status'], order['payment_status'])}"
        )
        keyboard = InlineKeyboardMarkup([
            [
                InlineKeyboardButton("✅ تأیید سفارش", callback_data=f"ordstatus:{order['id']}:confirmed", style="success"),
                InlineKeyboardButton("📦 ارسال شد", callback_data=f"ordstatus:{order['id']}:shipped", style="primary"),
                InlineKeyboardButton("❌ لغو", callback_data=f"ordstatus:{order['id']}:cancelled", style="danger")
            ]
        ])
        await update.message.reply_text(text, reply_markup=keyboard)

async def order_status_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if not is_admin(query.from_user.id):
        await query.answer("دسترسی ندارید.", show_alert=True)
        return
    _, order_id_str, new_status = query.data.split(":")
    order_id = int(order_id_str)
    db_update_order_status(order_id, new_status)
    await query.answer(f"وضعیت به {STATUS_LABELS.get(new_status, new_status)} تغییر کرد.")
    order = db_get_order(order_id)
    if order:
        try:
            await context.bot.send_message(
                chat_id=order["user_id"],
                text=f"وضعیت سفارش #{order_id} شما به {STATUS_LABELS.get(new_status, new_status)} تغییر کرد."
            )
        except:
            pass
    await query.edit_message_text(f"وضعیت سفارش #{order_id} به‌روز شد.")

# ====================================================================
#  مدیریت پرداخت‌ها (ادمین)
# ====================================================================
async def admin_show_payments(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    payments = db_get_pending_payments()
    if not payments:
        await update.message.reply_text("هیچ پرداخت در انتظار تاییدی نیست.")
        return
    for payment in payments:
        order = db_get_order(payment["order_id"])
        if not order:
            continue
        text = (
            f"💰 پرداخت #{payment['id']} - سفارش #{payment['order_id']}\n"
            f"مبلغ: {format_price(payment['amount'])}\n"
            f"کاربر: {order['full_name']} (آیدی: {payment['user_id']})"
        )
        keyboard = InlineKeyboardMarkup([
            [InlineKeyboardButton("✅ تأیید", callback_data=f"verify_payment:{payment['id']}", style="success"),
             InlineKeyboardButton("❌ رد", callback_data=f"reject_payment:{payment['id']}", style="danger")]
        ])
        if payment["receipt_photo_id"]:
            await context.bot.send_photo(chat_id=update.effective_chat.id, photo=payment["receipt_photo_id"], caption=text, reply_markup=keyboard)
        else:
            await update.message.reply_text(text, reply_markup=keyboard)

# ====================================================================
#  مدیریت کوپن‌ها
# ====================================================================
async def admin_coupon_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    coupons = db_get_all_coupons()
    if coupons:
        text = "🎫 لیست کوپن‌ها:\n"
        for c in coupons:
            text += f"• {c['code']} - {c['discount_type']} {c['discount_value']} (حداقل سفارش: {c['min_order_amount']}) - استفاده: {c['used_count']}/{c['usage_limit']}\n"
        await update.message.reply_text(text)
    else:
        await update.message.reply_text("هیچ کوپنی موجود نیست.")
    await update.message.reply_text("برای افزودن کوپن جدید، کد آن را وارد کنید (یا /cancel برای لغو):")
    return COUPON_CODE

async def coupon_get_code(update: Update, context: ContextTypes.DEFAULT_TYPE):
    code = update.message.text.strip().upper()
    if db_get_coupon(code):
        await update.message.reply_text("این کد قبلاً وجود دارد. کد دیگری وارد کنید.")
        return COUPON_CODE
    context.user_data["coupon_code"] = code
    await update.message.reply_text("نوع تخفیف را انتخاب کنید (percent یا fixed):")
    return COUPON_TYPE

async def coupon_get_type(update: Update, context: ContextTypes.DEFAULT_TYPE):
    dtype = update.message.text.strip().lower()
    if dtype not in ["percent", "fixed"]:
        await update.message.reply_text("لطفاً percent یا fixed وارد کنید.")
        return COUPON_TYPE
    context.user_data["coupon_type"] = dtype
    await update.message.reply_text("مقدار تخفیف را وارد کنید (برای percent عدد بین ۱ تا ۱۰۰، برای fixed مبلغ به تومان):")
    return COUPON_VALUE

async def coupon_get_value(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        value = int(update.message.text.strip())
        if context.user_data["coupon_type"] == "percent" and (value < 1 or value > 100):
            await update.message.reply_text("درصد باید بین ۱ تا ۱۰۰ باشد.")
            return COUPON_VALUE
        context.user_data["coupon_value"] = value
        await update.message.reply_text("حداقل مبلغ سفارش برای اعمال کوپن را وارد کنید (یا ۰ برای بدون محدودیت):")
        return COUPON_MIN_ORDER
    except ValueError:
        await update.message.reply_text("عدد معتبر وارد کنید.")
        return COUPON_VALUE

async def coupon_get_min_order(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        min_order = int(update.message.text.strip())
        context.user_data["coupon_min_order"] = min_order
        await update.message.reply_text("تاریخ انقضا را به فرمت YYYY-MM-DD وارد کنید (یا '-' برای بدون انقضا):")
        return COUPON_EXPIRY
    except ValueError:
        await update.message.reply_text("عدد معتبر وارد کنید.")
        return COUPON_MIN_ORDER

async def coupon_get_expiry(update: Update, context: ContextTypes.DEFAULT_TYPE):
    expiry = None
    txt = update.message.text.strip()
    if txt != "-":
        try:
            expiry = datetime.strptime(txt, "%Y-%m-%d").isoformat()
        except ValueError:
            await update.message.reply_text("فرمت تاریخ اشتباه. دوباره وارد کنید (YYYY-MM-DD) یا '-'")
            return COUPON_EXPIRY
    context.user_data["coupon_expiry"] = expiry
    await update.message.reply_text("تعداد دفعات استفاده محدود را وارد کنید (پیش‌فرض ۱):")
    return COUPON_LIMIT

async def coupon_get_limit(update: Update, context: ContextTypes.DEFAULT_TYPE):
    try:
        limit = int(update.message.text.strip())
    except ValueError:
        limit = 1
    db_add_coupon(
        code=context.user_data["coupon_code"],
        discount_type=context.user_data["coupon_type"],
        discount_value=context.user_data["coupon_value"],
        min_order_amount=context.user_data["coupon_min_order"],
        expires_at=context.user_data["coupon_expiry"],
        usage_limit=limit
    )
    await update.message.reply_text(f"کوپن {context.user_data['coupon_code']} با موفقیت اضافه شد.", reply_markup=admin_menu_keyboard())
    context.user_data.clear()
    return ConversationHandler.END

coupon_conversation_admin = ConversationHandler(
    entry_points=[MessageHandler(filters.Regex("^🎫 مدیریت کوپن‌ها$"), admin_coupon_menu)],
    states={
        COUPON_CODE: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_code)],
        COUPON_TYPE: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_type)],
        COUPON_VALUE: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_value)],
        COUPON_MIN_ORDER: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_min_order)],
        COUPON_EXPIRY: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_expiry)],
        COUPON_LIMIT: [MessageHandler(filters.TEXT & ~filters.COMMAND, coupon_get_limit)],
    },
    fallbacks=[CommandHandler("cancel", cancel_conversation)],
)

# ====================================================================
#  مدیریت تیکت‌ها (ادمین)
# ====================================================================
async def admin_show_tickets(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    tickets = db_get_tickets(status=None)
    if not tickets:
        await update.message.reply_text("هیچ تیکتی موجود نیست.")
        return
    for t in tickets:
        status_emoji = "🔴" if t["status"] == "open" else "🟡" if t["status"] == "in_progress" else "🟢"
        text = (
            f"{status_emoji} تیکت #{t['id']} - {t['subject']}\n"
            f"از کاربر: {t['user_id']}\n"
            f"وضعیت: {t['status']}\n"
            f"پیام: {t['message'][:100]}..."
        )
        keyboard = InlineKeyboardMarkup([
            [InlineKeyboardButton("✏️ پاسخ", callback_data=f"reply_ticket:{t['id']}", style="primary")],
            [InlineKeyboardButton("❌ بستن تیکت", callback_data=f"close_ticket:{t['id']}", style="danger")]
        ])
        await update.message.reply_text(text, reply_markup=keyboard)

async def admin_close_ticket_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if not is_admin(query.from_user.id):
        await query.answer("دسترسی ندارید.", show_alert=True)
        return
    ticket_id = int(query.data.split(":")[1])
    db_close_ticket(ticket_id)
    await query.answer("تیکت بسته شد.")
    await query.edit_message_text(f"تیکت #{ticket_id} بسته شد.")

# ====================================================================
#  آمار و کاربران
# ====================================================================
async def admin_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    stats = db_get_stats()
    text = (
        "📊 <b>آمار فروشگاه</b>\n\n"
        f"🧾 کل سفارشات: {stats['total_orders']}\n"
        f"⏳ سفارشات در انتظار: {stats['pending_orders']}\n"
        f"💰 کل فروش: {format_price(stats['total_revenue'])}\n"
        f"💳 سفارشات پرداخت شده: {stats['paid_orders']}\n"
        f"🛍 محصولات فعال: {stats['products_count']}\n"
        f"👥 کاربران ثبت‌نامی: {stats['users_count']}"
    )
    await update.message.reply_text(text, parse_mode=ParseMode.HTML)

async def admin_users_list(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.effective_user.id):
        return
    with closing(get_conn()) as conn:
        users = conn.execute("SELECT * FROM users ORDER BY id LIMIT 50").fetchall()
    if not users:
        await update.message.reply_text("کاربری ثبت‌نام نکرده.")
        return
    text = "👥 لیست کاربران:\n"
    for u in users:
        text += f"• {u['first_name']} (@{u['username'] or 'no_username'}) - کد: {u['referral_code']} - کیف پول: {format_price(u['wallet_balance'])}\n"
    await update.message.reply_text(text)

# ====================================================================
#  برگشت به منوی اصلی
# ====================================================================
async def admin_back_to_main(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("بازگشت به منوی اصلی.", reply_markup=main_menu_keyboard(update.effective_user.id))

# ====================================================================
#  ثبت هندلرها و اجرا
# ====================================================================
def main():
    init_db()
    app = Application.builder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("cancel", cancel_conversation))
    app.add_handler(CommandHandler("close_ticket", close_ticket_command))

    app.add_handler(coupon_conversation)
    app.add_handler(checkout_conversation)
    app.add_handler(payment_conversation)
    app.add_handler(ConversationHandler(
        entry_points=[MessageHandler(filters.Regex("^📞 پشتیبانی$"), support_start)],
        states={
            TICKET_SUBJECT: [MessageHandler(filters.TEXT & ~filters.COMMAND, support_subject)],
            TICKET_MESSAGE: [MessageHandler(filters.TEXT & ~filters.COMMAND, support_message)],
        },
        fallbacks=[CommandHandler("cancel", cancel_conversation)],
    ))

    app.add_handler(add_category_conversation)
    app.add_handler(add_product_conversation)
    app.add_handler(add_variant_conversation)
    app.add_handler(coupon_conversation_admin)

    app.add_handler(ConversationHandler(
        entry_points=[CallbackQueryHandler(admin_reply_ticket_callback, pattern=r"^reply_ticket:")],
        states={TICKET_RESPONSE: [MessageHandler(filters.TEXT & ~filters.COMMAND, admin_reply_ticket_text)]},
        fallbacks=[CommandHandler("cancel", cancel_conversation)],
    ))

    app.add_handler(MessageHandler(filters.Regex("^🛍 مشاهده محصولات$"), show_categories))
    app.add_handler(MessageHandler(filters.Regex("^🛒 سبد خرید$"), show_cart))
    app.add_handler(MessageHandler(filters.Regex("^🧾 سفارشات من$"), my_orders))

    app.add_handler(MessageHandler(filters.Regex("^⚙️ پنل مدیریت$"), admin_panel))
    app.add_handler(MessageHandler(filters.Regex("^🔙 بازگشت به منوی اصلی$"), admin_back_to_main))
    app.add_handler(MessageHandler(filters.Regex("^📋 لیست محصولات$"), admin_list_products))
    app.add_handler(MessageHandler(filters.Regex("^🧾 مدیریت سفارشات$"), admin_show_orders))
    app.add_handler(MessageHandler(filters.Regex("^💳 مدیریت پرداخت‌ها$"), admin_show_payments))
    app.add_handler(MessageHandler(filters.Regex("^📩 تیکت‌های پشتیبانی$"), admin_show_tickets))
    app.add_handler(MessageHandler(filters.Regex("^📊 آمار فروش$"), admin_stats))
    app.add_handler(MessageHandler(filters.Regex("^👥 کاربران$"), admin_users_list))

    app.add_handler(CallbackQueryHandler(show_products_in_category, pattern=r"^cat:"))
    app.add_handler(CallbackQueryHandler(show_variants, pattern=r"^variants:"))
    app.add_handler(CallbackQueryHandler(add_to_cart_callback, pattern=r"^add:"))
    app.add_handler(CallbackQueryHandler(remove_cart_item_callback, pattern=r"^rmcart:"))
    app.add_handler(CallbackQueryHandler(clear_cart_callback, pattern=r"^clearcart$"))
    app.add_handler(CallbackQueryHandler(verify_payment_callback, pattern=r"^(verify_payment|reject_payment):"))
    app.add_handler(CallbackQueryHandler(order_status_callback, pattern=r"^ordstatus:"))
    app.add_handler(CallbackQueryHandler(admin_delete_product_callback, pattern=r"^delprod:"))
    app.add_handler(CallbackQueryHandler(admin_close_ticket_callback, pattern=r"^close_ticket:"))
    app.add_handler(CallbackQueryHandler(lambda u, c: u.edit_message_text("به منوی اصلی بازگشتید."), pattern="^main_menu$"))

    logger.info("ربات با تمام قابلیت‌ها راه‌اندازی شد.")
    app.run_polling(allowed_updates=Update.ALL_TYPES)

if __name__ == "__main__":
    main()