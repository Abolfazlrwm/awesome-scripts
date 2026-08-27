# ============================================================
# ربات فروش کانفیگ نسخه نهایی ۵.۰
# قابلیت‌ها: خرید اشتراک | گرونه شانس | تست | سرویس‌های من
# کیف پول | زیر مجموعه | نمایندگی | پنل مدیریت کامل
# مخزن کانفیگ با انتخاب تست/خرید | تنظیمات جوین اجباری
# ============================================================

import telebot
from telebot import types
import sqlite3
import random
import time
from datetime import datetime, timedelta
import os
import sys
import json

# ---------- تنظیمات اصلی (ویرایش کن) ----------
API_TOKEN = "توکن رباتت اینجا جای‌گذاری کن"
OWNER_ID = 123456789  # آیدی عددی ادمین را اینجا وارد کنید
CHANNEL_ID = "آیدی کانالتون اینجا وارد کنید"
CARD_NUMBER = "شماره کارت"
CARD_NAME = "دارنده کارت"
REFERRAL_BONUS = 5000
LUCKY_BONUS = 5000
DB_NAME = "shop_bot.db"

bot = telebot.TeleBot(API_TOKEN)
user_states = {}
BOT_SETTINGS = {"locked": False}

# ============================================================
# دیتابیس
# ============================================================

def init_db():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        balance INTEGER DEFAULT 0,
        is_agent INTEGER DEFAULT 0,
        is_banned INTEGER DEFAULT 0,
        has_rules INTEGER DEFAULT 0,
        referred_by INTEGER DEFAULT 0,
        last_test TEXT,
        join_date TEXT,
        total_spent INTEGER DEFAULT 0
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS admins (user_id INTEGER PRIMARY KEY)''')
    c.execute("INSERT OR IGNORE INTO admins (user_id) VALUES (?)", (OWNER_ID,))
    c.execute('''CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price INTEGER,
        duration INTEGER,
        is_active INTEGER DEFAULT 1
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS configs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER,
        content TEXT,
        is_used INTEGER DEFAULT 0,
        used_by INTEGER DEFAULT 0,
        used_date TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS user_services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        product_id INTEGER,
        product_name TEXT,
        config_content TEXT,
        price INTEGER,
        buy_date TEXT,
        expire_date TEXT,
        is_active INTEGER DEFAULT 1,
        config_id INTEGER
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        type TEXT,
        amount INTEGER,
        date TEXT,
        description TEXT
    )''')
    c.execute('''CREATE TABLE IF NOT EXISTS channel_settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )''')
    c.execute("INSERT OR IGNORE INTO channel_settings (key, value) VALUES (?, ?)", ("channel_id", CHANNEL_ID))
    c.execute("INSERT OR IGNORE INTO channel_settings (key, value) VALUES (?, ?)", ("force_join_enabled", "true"))
    conn.commit()
    conn.close()

init_db()

# ============================================================
# توابع دیتابیس
# ============================================================

def get_user(user_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT balance, is_agent, is_banned, has_rules, referred_by, last_test, join_date, total_spent FROM users WHERE user_id = ?", (user_id,))
    row = c.fetchone()
    if not row:
        c.execute("INSERT INTO users (user_id, join_date) VALUES (?, ?)", (user_id, datetime.now().strftime("%Y-%m-%d %H:%M:%S")))
        conn.commit()
        row = (0, 0, 0, 0, 0, None, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), 0)
    conn.close()
    return {
        "balance": row[0],
        "is_agent": row[1],
        "is_banned": row[2],
        "has_rules": row[3],
        "referred_by": row[4],
        "last_test": row[5],
        "join_date": row[6],
        "total_spent": row[7]
    }

def update_balance(user_id, amount):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("UPDATE users SET balance = balance + ? WHERE user_id = ?", (amount, user_id))
    conn.commit()
    conn.close()

def is_admin(user_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT user_id FROM admins WHERE user_id = ?", (user_id,))
    res = c.fetchone()
    conn.close()
    return res is not None

def get_products():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, name, price, duration FROM products WHERE is_active = 1")
    rows = c.fetchall()
    conn.close()
    return rows

def get_product(product_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, name, price, duration FROM products WHERE id = ? AND is_active = 1", (product_id,))
    row = c.fetchone()
    conn.close()
    return row

def get_configs_count(product_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT COUNT(*) FROM configs WHERE product_id = ? AND is_used = 0", (product_id,))
    count = c.fetchone()[0]
    conn.close()
    return count

def get_user_services(user_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, product_name, config_content, price, buy_date, expire_date, is_active FROM user_services WHERE user_id = ? ORDER BY id DESC", (user_id,))
    rows = c.fetchall()
    conn.close()
    return rows

def get_user_service(service_id, user_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, product_id, product_name, config_content, price, buy_date, expire_date, is_active, config_id FROM user_services WHERE id = ? AND user_id = ?", (service_id, user_id))
    row = c.fetchone()
    conn.close()
    return row

def add_transaction(user_id, type_, amount, description=""):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, ?, ?, ?, ?)",
              (user_id, type_, amount, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), description))
    conn.commit()
    conn.close()

def get_all_users():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT user_id FROM users")
    rows = c.fetchall()
    conn.close()
    return rows

def get_stats():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    total_users = c.execute("SELECT COUNT(*) FROM users").fetchone()[0]
    total_configs = c.execute("SELECT COUNT(*) FROM configs WHERE is_used = 0").fetchone()[0]
    total_services = c.execute("SELECT COUNT(*) FROM user_services").fetchone()[0]
    total_revenue = c.execute("SELECT SUM(price) FROM user_services").fetchone()[0] or 0
    active_services = c.execute("SELECT COUNT(*) FROM user_services WHERE is_active = 1").fetchone()[0]
    conn.close()
    return {"total_users": total_users, "total_configs": total_configs, "total_services": total_services, "total_revenue": total_revenue, "active_services": active_services}

def get_channel_setting(key):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT value FROM channel_settings WHERE key = ?", (key,))
    row = c.fetchone()
    conn.close()
    return row[0] if row else None

def set_channel_setting(key, value):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("INSERT OR REPLACE INTO channel_settings (key, value) VALUES (?, ?)", (key, value))
    conn.commit()
    conn.close()

def get_channel_id():
    channel = get_channel_setting("channel_id")
    return channel if channel else CHANNEL_ID

def is_force_join_enabled():
    val = get_channel_setting("force_join_enabled")
    return val == "true" if val is not None else True

# ============================================================
# توابع کمکی و کیبوردها
# ============================================================

def check_channel(user_id):
    if not is_force_join_enabled():
        return True
    channel_id = get_channel_id()
    if not channel_id:
        return True
    try:
        member = bot.get_chat_member(channel_id, user_id)
        return member.status in ['creator', 'administrator', 'member']
    except:
        return True

def get_main_keyboard(user_id):
    markup = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    buttons = [
        "🛒 خرید اشتراک", "🎲 گرونه شانس", "🔑 اکانت تست",
        "🛍️ سرویس‌های من", "🏦 کیف پول + شارژ", "👥 زیر مجموعه‌گیری",
        "🙋‍♀️ درخواست نمایندگی", "☎️ پشتیبانی"
    ]
    if is_admin(user_id):
        buttons.append("👨‍💼 پنل مدیریت")
    row = []
    for btn in buttons:
        row.append(types.KeyboardButton(btn))
        if len(row) == 2:
            markup.row(*row)
            row = []
    if row:
        markup.row(*row)
    return markup

def get_admin_keyboard():
    markup = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    markup.row("📦 مخزن کانفیگ", "📊 آمار ربات")
    markup.row("🛠️ مدیریت کاربران", "📢 ارسال همگانی")
    markup.row("⚙️ تنظیمات عمومی", "🔙 بازگشت به منوی اصلی")
    return markup

def get_service_keyboard(service_id):
    markup = types.InlineKeyboardMarkup(row_width=1)
    markup.add(types.InlineKeyboardButton("🔄 تمدید اشتراک", callback_data=f"renew_{service_id}"))
    markup.add(types.InlineKeyboardButton("📥 دریافت مجدد لینک", callback_data=f"getlink_{service_id}"))
    markup.add(types.InlineKeyboardButton("✏️ تغییر لینک ساب", callback_data=f"changelink_{service_id}"))
    markup.add(types.InlineKeyboardButton("🗑️ حذف کانفیگ", callback_data=f"deletecfg_{service_id}"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="back_services"))
    return markup

def get_join_settings_keyboard():
    markup = types.InlineKeyboardMarkup(row_width=1)
    markup.add(types.InlineKeyboardButton("📋 وضعیت فعلی", callback_data="join_status"))
    markup.add(types.InlineKeyboardButton("🔄 تغییر وضعیت", callback_data="join_toggle"))
    markup.add(types.InlineKeyboardButton("✏️ تغییر کانال", callback_data="join_set_channel"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="join_back"))
    return markup

# ============================================================
# کالبک‌های قوانین و عضویت
# ============================================================

@bot.callback_query_handler(func=lambda call: call.data == "accept_rules")
def accept_rules(call):
    user_id = call.from_user.id
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("UPDATE users SET has_rules = 1 WHERE user_id = ?", (user_id,))
    conn.commit()
    conn.close()
    bot.delete_message(call.message.chat.id, call.message.message_id)
    u = get_user(user_id)
    welcome = f"🎯 *به ربات خوش آمدید!*\n💰 موجودی: {u['balance']:,} تومان"
    bot.send_message(user_id, welcome, reply_markup=get_main_keyboard(user_id), parse_mode='Markdown')
    if u["referred_by"] != 0:
        update_balance(u["referred_by"], REFERRAL_BONUS)

@bot.callback_query_handler(func=lambda call: call.data == "check_channel")
def check_channel_callback(call):
    user_id = call.from_user.id
    if check_channel(user_id):
        bot.delete_message(call.message.chat.id, call.message.message_id)
        u = get_user(user_id)
        if u["has_rules"] == 0:
            rules = "📜 قوانین:\n1️⃣ هر اشتراک فقط برای یک کاربر\n2️⃣ عدم عودت وجه\n3️⃣ رسید جعلی = مسدودیت\n4️⃣ استفاده = پذیرش قوانین"
            markup = types.InlineKeyboardMarkup()
            markup.add(types.InlineKeyboardButton("✅ قبول می‌کنم", callback_data="accept_rules"))
            bot.send_message(user_id, rules, reply_markup=markup, parse_mode='Markdown')
        else:
            bot.send_message(user_id, "✅ خوش آمدید!", reply_markup=get_main_keyboard(user_id))
    else:
        bot.answer_callback_query(call.id, "❌ عضو کانال نشدی!", show_alert=True)

# ============================================================
# استارت - اصلاح شده با try/except
# ============================================================

@bot.message_handler(commands=['start'])
def start(message):
    try:
        user_id = message.from_user.id
        user_states[user_id] = None

        if BOT_SETTINGS["locked"] and not is_admin(user_id):
            bot.send_message(user_id, "🔧 ربات در دست تعمیرات.")
            return

        u = get_user(user_id)

        if u["referred_by"] == 0 and len(message.text.split()) > 1:
            ref_id = message.text.split()[1]
            if ref_id.isdigit() and int(ref_id) != user_id:
                conn = sqlite3.connect(DB_NAME)
                c = conn.cursor()
                c.execute("UPDATE users SET referred_by = ? WHERE user_id = ?", (int(ref_id), user_id))
                conn.commit()
                conn.close()
                update_balance(int(ref_id), REFERRAL_BONUS)

        if not check_channel(user_id):
            channel_id = get_channel_id()
            markup = types.InlineKeyboardMarkup()
            markup.add(types.InlineKeyboardButton("📢 عضویت در کانال", url=f"https://t.me/{channel_id.replace('@','')}"))
            markup.add(types.InlineKeyboardButton("✅ بررسی عضویت", callback_data="check_channel"))
            bot.send_message(user_id, f"🔒 *اول عضو کانال شو:*\n{channel_id}", reply_markup=markup, parse_mode='Markdown')
            return

        if u["has_rules"] == 0:
            rules = "📜 قوانین:\n1️⃣ هر اشتراک فقط برای یک کاربر\n2️⃣ عدم عودت وجه\n3️⃣ رسید جعلی = مسدودیت\n4️⃣ استفاده = پذیرش قوانین"
            markup = types.InlineKeyboardMarkup()
            markup.add(types.InlineKeyboardButton("✅ قبول می‌کنم", callback_data="accept_rules"))
            bot.send_message(user_id, rules, reply_markup=markup, parse_mode='Markdown')
            return

        welcome = f"🎯 *به ربات خوش آمدید!*\n💰 موجودی: {u['balance']:,} تومان"
        bot.send_message(user_id, welcome, reply_markup=get_main_keyboard(user_id), parse_mode='Markdown')
    except Exception as e:
        print(f"خطا در start: {e}")
        bot.send_message(message.from_user.id, "⚠️ خطا در راه‌اندازی، دوباره تلاش کن.")

# ============================================================
# خرید اشتراک
# ============================================================

@bot.message_handler(func=lambda m: m.text == "🛒 خرید اشتراک")
def buy_subscription(message):
    user_id = message.from_user.id
    u = get_user(user_id)
    if u["is_banned"] == 1:
        bot.send_message(user_id, "🚫 شما مسدود شده‌اید.")
        return
    products = get_products()
    if not products:
        bot.send_message(user_id, "❌ محصولی موجود نیست.")
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    for p in products:
        count = get_configs_count(p[0])
        markup.add(types.InlineKeyboardButton(f"📦 {p[1]} - {p[2]:,} تومان ({p[3]} روز) - {count} عدد", callback_data=f"buy_{p[0]}"))
    bot.send_message(user_id, "🛒 *لیست محصولات:*", reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("buy_"))
def buy_callback(call):
    user_id = call.from_user.id
    prod_id = int(call.data.split("_")[1])
    u = get_user(user_id)
    product = get_product(prod_id)
    if not product:
        bot.answer_callback_query(call.id, "محصول یافت نشد!", show_alert=True)
        return
    final_price = int(product[2] * 0.9) if u["is_agent"] == 1 else product[2]
    role = "👑 نماینده (۱۰٪ تخفیف)" if u["is_agent"] == 1 else "👤 کاربر عادی"
    markup = types.InlineKeyboardMarkup(row_width=1)
    markup.add(types.InlineKeyboardButton("💳 پرداخت با کارت", callback_data=f"card_{prod_id}"))
    markup.add(types.InlineKeyboardButton("🏦 پرداخت از کیف پول", callback_data=f"wallet_{prod_id}"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="back_buy"))
    text = f"📦 *{product[1]}*\n💰 قیمت اصلی: {product[2]:,}\n💎 قیمت شما: {final_price:,}\n👤 {role}\n⏳ مدت: {product[3]} روز\n🏦 موجودی: {u['balance']:,}"
    bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("card_"))
def card_pay(call):
    user_id = call.from_user.id
    prod_id = int(call.data.split("_")[1])
    user_states[user_id] = {"action": "waiting_receipt", "product_id": prod_id}
    text = f"💳 *پرداخت کارت*\n🏦 شماره کارت: `{CARD_NUMBER}`\n👤 {CARD_NAME}\n📸 تصویر رسید را ارسال کن."
    bot.edit_message_text(text, call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("wallet_"))
def wallet_pay(call):
    user_id = call.from_user.id
    prod_id = int(call.data.split("_")[1])
    u = get_user(user_id)
    product = get_product(prod_id)
    if not product:
        bot.answer_callback_query(call.id, "محصول یافت نشد!", show_alert=True)
        return
    final_price = int(product[2] * 0.9) if u["is_agent"] == 1 else product[2]
    if u["balance"] < final_price:
        bot.answer_callback_query(call.id, f"💰 موجودی کافی نیست! نیاز: {final_price:,}", show_alert=True)
        return
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content FROM configs WHERE product_id = ? AND is_used = 0 LIMIT 1", (prod_id,))
    config = c.fetchone()
    if not config:
        bot.answer_callback_query(call.id, "❗ مخزن خالی است!", show_alert=True)
        conn.close()
        return
    expire = datetime.now() + timedelta(days=product[3])
    c.execute("UPDATE configs SET is_used = 1, used_by = ?, used_date = ? WHERE id = ?", (user_id, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("INSERT INTO user_services (user_id, product_id, product_name, config_content, price, buy_date, expire_date, config_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
              (user_id, prod_id, product[1], config[1], final_price, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), expire.strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    conn.commit()
    conn.close()
    update_balance(user_id, -final_price)
    add_transaction(user_id, "buy", -final_price, f"خرید {product[1]}")
    bot.edit_message_text(f"✅ *خرید موفق!*\n📦 {product[1]}\n💰 {final_price:,}\n⏳ انقضا: {expire.strftime('%Y-%m-%d %H:%M:%S')}\n🔑 `{config[1]}`", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "back_buy")
def back_buy(call):
    user_id = call.from_user.id
    products = get_products()
    if not products:
        bot.edit_message_text("❌ محصولی موجود نیست.", call.message.chat.id, call.message.message_id)
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    for p in products:
        count = get_configs_count(p[0])
        markup.add(types.InlineKeyboardButton(f"📦 {p[1]} - {p[2]:,} تومان ({p[3]} روز) - {count} عدد", callback_data=f"buy_{p[0]}"))
    bot.edit_message_text("🛒 *لیست محصولات:*", call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

# ============================================================
# گرونه شانس
# ============================================================

@bot.message_handler(func=lambda m: m.text == "🎲 گرونه شانس")
def lucky_game(message):
    user_id = message.from_user.id
    u = get_user(user_id)
    text = f"🎲 *گرونه شانس*\nهزینه: ۱,۰۰۰ تومان\n🎯 عدد ۶ = {LUCKY_BONUS:,} تومان\n🏦 موجودی: {u['balance']:,}"
    markup = types.InlineKeyboardMarkup()
    markup.add(types.InlineKeyboardButton("🎲 بزن بریم!", callback_data="roll_dice"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="back_main"))
    bot.send_message(user_id, text, reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "roll_dice")
def roll_dice(call):
    user_id = call.from_user.id
    u = get_user(user_id)
    if u["balance"] < 1000:
        bot.answer_callback_query(call.id, "💰 پول کافی نداری!", show_alert=True)
        return
    update_balance(user_id, -1000)
    add_transaction(user_id, "lucky_cost", -1000, "هزینه شانس")
    dice = random.randint(1, 6)
    if dice == 6:
        update_balance(user_id, LUCKY_BONUS)
        add_transaction(user_id, "lucky_win", LUCKY_BONUS, "برد شانس")
        result = f"🎉 *تبریک! عدد ۶ آمد!*\n💰 {LUCKY_BONUS:,} تومان بردی!"
    else:
        result = f"😅 عدد {dice} آمد... بازنده شدی!"
    u2 = get_user(user_id)
    result += f"\n💰 موجودی جدید: {u2['balance']:,}"
    markup = types.InlineKeyboardMarkup()
    markup.add(types.InlineKeyboardButton("🎲 دوباره", callback_data="roll_dice"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="back_main"))
    bot.edit_message_text(result, call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

# ============================================================
# سرویس‌های من
# ============================================================

@bot.message_handler(func=lambda m: m.text == "🛍️ سرویس‌های من")
def my_services(message):
    user_id = message.from_user.id
    services = get_user_services(user_id)
    if not services:
        bot.send_message(user_id, "📭 سرویسی ندارید.", reply_markup=get_main_keyboard(user_id))
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    for s in services:
        status = "✅ فعال" if s[6] == 1 else "❌ منقضی"
        markup.add(types.InlineKeyboardButton(f"📦 {s[1]} - {status}", callback_data=f"service_{s[0]}"))
    bot.send_message(user_id, "🛍️ *سرویس‌های شما:*", reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("service_"))
def service_detail(call):
    service_id = int(call.data.split("_")[1])
    user_id = call.from_user.id
    service = get_user_service(service_id, user_id)
    if not service:
        bot.answer_callback_query(call.id, "سرویس یافت نشد!", show_alert=True)
        return
    status = "✅ فعال" if service[6] == 1 else "❌ منقضی"
    text = f"📦 *{service[2]}*\n💰 قیمت: {service[4]:,}\n📅 خرید: {service[5]}\n⏳ انقضا: {service[6]}\n📊 وضعیت: {status}"
    markup = get_service_keyboard(service_id)
    bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("renew_"))
def renew_service(call):
    service_id = int(call.data.split("_")[1])
    user_id = call.from_user.id
    u = get_user(user_id)
    service = get_user_service(service_id, user_id)
    if not service:
        bot.answer_callback_query(call.id, "سرویس یافت نشد!", show_alert=True)
        return
    product = get_product(service[1])
    if not product:
        bot.answer_callback_query(call.id, "محصول یافت نشد!", show_alert=True)
        return
    final_price = int(product[2] * 0.9) if u["is_agent"] == 1 else product[2]
    if u["balance"] < final_price:
        bot.answer_callback_query(call.id, f"💰 نیاز: {final_price:,}", show_alert=True)
        return
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content FROM configs WHERE product_id = ? AND is_used = 0 LIMIT 1", (service[1],))
    config = c.fetchone()
    if not config:
        bot.answer_callback_query(call.id, "❗ مخزن خالی است!", show_alert=True)
        conn.close()
        return
    expire = datetime.now() + timedelta(days=product[3])
    c.execute("UPDATE configs SET is_used = 1, used_by = ?, used_date = ? WHERE id = ?", (user_id, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("UPDATE user_services SET is_active = 1, expire_date = ?, config_content = ?, config_id = ? WHERE id = ?",
              (expire.strftime("%Y-%m-%d %H:%M:%S"), config[1], config[0], service_id))
    conn.commit()
    conn.close()
    update_balance(user_id, -final_price)
    add_transaction(user_id, "renew", -final_price, f"تمدید {service[2]}")
    bot.edit_message_text(f"✅ *تمدید شد!*\n🔑 `{config[1]}`\n⏳ انقضای جدید: {expire.strftime('%Y-%m-%d %H:%M:%S')}", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("getlink_"))
def get_link(call):
    service_id = int(call.data.split("_")[1])
    user_id = call.from_user.id
    service = get_user_service(service_id, user_id)
    if not service:
        bot.answer_callback_query(call.id, "سرویس یافت نشد!", show_alert=True)
        return
    bot.send_message(user_id, f"🔑 *لینک ساب:*\n`{service[3]}`", parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("changelink_"))
def change_link(call):
    service_id = int(call.data.split("_")[1])
    user_id = call.from_user.id
    service = get_user_service(service_id, user_id)
    if not service:
        bot.answer_callback_query(call.id, "سرویس یافت نشد!", show_alert=True)
        return
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content FROM configs WHERE product_id = ? AND is_used = 0 LIMIT 1", (service[1],))
    config = c.fetchone()
    if not config:
        bot.answer_callback_query(call.id, "❗ مخزن خالی است!", show_alert=True)
        conn.close()
        return
    c.execute("UPDATE configs SET is_used = 1, used_by = ?, used_date = ? WHERE id = ?", (user_id, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("UPDATE user_services SET config_content = ?, config_id = ? WHERE id = ?", (config[1], config[0], service_id))
    conn.commit()
    conn.close()
    bot.edit_message_text(f"✅ *لینک تغییر کرد!*\n🔑 `{config[1]}`", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("deletecfg_"))
def delete_config(call):
    service_id = int(call.data.split("_")[1])
    user_id = call.from_user.id
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("UPDATE user_services SET is_active = 0 WHERE id = ? AND user_id = ?", (service_id, user_id))
    conn.commit()
    conn.close()
    bot.edit_message_text("🗑️ *کانفیگ حذف شد.*", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "back_services")
def back_services(call):
    user_id = call.from_user.id
    services = get_user_services(user_id)
    if not services:
        bot.edit_message_text("📭 سرویسی ندارید.", call.message.chat.id, call.message.message_id)
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    for s in services:
        status = "✅ فعال" if s[6] == 1 else "❌ منقضی"
        markup.add(types.InlineKeyboardButton(f"📦 {s[1]} - {status}", callback_data=f"service_{s[0]}"))
    bot.edit_message_text("🛍️ *سرویس‌های شما:*", call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

# ============================================================
# سایر دکمه‌های منو
# ============================================================

@bot.message_handler(func=lambda m: m.text == "🔑 اکانت تست")
def test_account(message):
    user_id = message.from_user.id
    u = get_user(user_id)
    today = datetime.now().strftime("%Y-%m-%d")
    if u["is_agent"] == 0:
        if u["last_test"]:
            bot.send_message(user_id, "❌ قبلاً تست گرفتی!", reply_markup=get_main_keyboard(user_id))
            return
    else:
        if u["last_test"] == today:
            bot.send_message(user_id, "❌ سهمیه امروزت رو گرفتی!", reply_markup=get_main_keyboard(user_id))
            return
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content FROM configs WHERE product_id = 0 AND is_used = 0 LIMIT 1")
    config = c.fetchone()
    if not config:
        bot.send_message(user_id, "❗ تست موجود نیست.", reply_markup=get_main_keyboard(user_id))
        conn.close()
        return
    c.execute("UPDATE configs SET is_used = 1, used_by = ?, used_date = ? WHERE id = ?", (user_id, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("INSERT INTO user_services (user_id, product_id, product_name, config_content, price, buy_date, expire_date, config_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
              (user_id, 0, "تست رایگان", config[1], 0, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), (datetime.now() + timedelta(days=1)).strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("UPDATE users SET last_test = ? WHERE user_id = ?", (today, user_id))
    conn.commit()
    conn.close()
    bot.send_message(user_id, f"🔑 *تست رایگان:*\n`{config[1]}`", parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "🏦 کیف پول + شارژ")
def wallet(message):
    user_id = message.from_user.id
    u = get_user(user_id)
    markup = types.InlineKeyboardMarkup()
    markup.add(types.InlineKeyboardButton("➕ افزایش موجودی", callback_data="charge_wallet"))
    bot.send_message(user_id, f"🏦 *کیف پول*\n💰 موجودی: {u['balance']:,} تومان", reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "charge_wallet")
def charge_wallet(call):
    user_id = call.from_user.id
    user_states[user_id] = "waiting_charge_amount"
    bot.edit_message_text(f"💳 *شارژ کیف پول*\nلطفاً مبلغ را به تومان وارد کنید:\n🏦 شماره کارت: `{CARD_NUMBER}`\n👤 {CARD_NAME}", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "👥 زیر مجموعه‌گیری")
def referral(message):
    user_id = message.from_user.id
    bot_info = bot.get_me()
    link = f"https://t.me/{bot_info.username}?start={user_id}"
    bot.send_message(user_id, f"👥 *زیر مجموعه‌گیری*\nهر دعوت {REFERRAL_BONUS:,} تومان\n🔗 لینک شما:\n`{link}`", parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "🙋‍♀️ درخواست نمایندگی")
def agency_request(message):
    user_id = message.from_user.id
    u = get_user(user_id)
    if u["is_agent"] == 1:
        bot.send_message(user_id, "👑 شما قبلاً نماینده هستید!")
        return
    markup = types.InlineKeyboardMarkup()
    markup.add(types.InlineKeyboardButton("✅ تایید", callback_data=f"ok_agency_{user_id}"))
    markup.add(types.InlineKeyboardButton("❌ رد", callback_data=f"no_agency_{user_id}"))
    username = f"@{message.from_user.username}" if message.from_user.username else "ندارد"
    bot.send_message(OWNER_ID, f"📩 درخواست نمایندگی\n👤 {username}\n🆔 {user_id}", reply_markup=markup)
    bot.send_message(user_id, "✅ درخواست شما ارسال شد. منتظر تایید باشید.")

@bot.message_handler(func=lambda m: m.text == "☎️ پشتیبانی")
def support(message):
    user_id = message.from_user.id
    user_states[user_id] = "waiting_support"
    bot.send_message(user_id, "📝 پیام خود را بنویسید.")

@bot.message_handler(func=lambda m: m.text == "👨‍💼 پنل مدیریت" and is_admin(m.from_user.id))
def admin_panel(message):
    bot.send_message(message.from_user.id, "👑 *پنل مدیریت*", reply_markup=get_admin_keyboard(), parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "🔙 بازگشت به منوی اصلی")
def back_main(message):
    user_id = message.from_user.id
    bot.send_message(user_id, "🏡 منوی اصلی", reply_markup=get_main_keyboard(user_id))

# ============================================================
# پنل مدیریت کامل
# ============================================================

@bot.message_handler(func=lambda m: m.text == "📦 مخزن کانفیگ" and is_admin(m.from_user.id))
def repo_panel(message):
    user_id = message.from_user.id
    products = get_products()
    markup = types.InlineKeyboardMarkup(row_width=1)
    for p in products:
        count = get_configs_count(p[0])
        markup.add(types.InlineKeyboardButton(f"📦 {p[1]} - {count} عدد", callback_data=f"repo_{p[0]}"))
    markup.add(types.InlineKeyboardButton("➕ اضافه کردن دستی", callback_data="repo_add_choice"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="repo_back"))
    bot.send_message(user_id, "📦 *مدیریت مخزن*", reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "repo_add_choice")
def repo_add_choice(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    markup.add(types.InlineKeyboardButton("🔑 اکانت تست", callback_data="repo_add_test"))
    markup.add(types.InlineKeyboardButton("🛒 خرید اشتراک", callback_data="repo_add_buy"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="repo_back"))
    bot.edit_message_text("📥 *افزودن کانفیگ به مخزن*\nانتخاب کنید:", call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "repo_add_test")
def repo_add_test(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    user_states[user_id] = {"action": "add_config_test"}
    bot.edit_message_text("🔑 *افزودن به مخزن تست*\nکانفیگ‌ها را ارسال کن (هر خط یکی):", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "repo_add_buy")
def repo_add_buy(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    products = get_products()
    if not products:
        bot.edit_message_text("❌ محصولی وجود ندارد.", call.message.chat.id, call.message.message_id)
        return
    markup = types.InlineKeyboardMarkup(row_width=1)
    for p in products:
        markup.add(types.InlineKeyboardButton(f"📦 {p[1]}", callback_data=f"repo_add_buy_prod_{p[0]}"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="repo_back"))
    bot.edit_message_text("🛒 *انتخاب محصول*", call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("repo_add_buy_prod_"))
def repo_add_buy_prod(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    product_id = int(call.data.split("_")[4])
    user_states[user_id] = {"action": "add_config_buy", "product_id": product_id}
    product = get_product(product_id)
    bot.edit_message_text(f"📦 *افزودن به محصول {product[1]}*\nکانفیگ‌ها را ارسال کن (هر خط یکی):", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("repo_") and not call.data.startswith("repo_add"))
def repo_callback(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    data = call.data.split("_")
    if data[1] == "back":
        products = get_products()
        markup = types.InlineKeyboardMarkup(row_width=1)
        for p in products:
            count = get_configs_count(p[0])
            markup.add(types.InlineKeyboardButton(f"📦 {p[1]} - {count} عدد", callback_data=f"repo_{p[0]}"))
        markup.add(types.InlineKeyboardButton("➕ اضافه کردن دستی", callback_data="repo_add_choice"))
        markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="repo_back"))
        bot.edit_message_text("📦 *مدیریت مخزن*", call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')
        return
    if data[1] == "clear":
        product_id = int(data[2])
        conn = sqlite3.connect(DB_NAME)
        c = conn.cursor()
        c.execute("DELETE FROM configs WHERE product_id = ? AND is_used = 1", (product_id,))
        deleted = c.rowcount
        conn.commit()
        conn.close()
        bot.answer_callback_query(call.id, f"{deleted} کانفیگ حذف شد.", show_alert=True)
        return
    product_id = int(data[1])
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content, is_used FROM configs WHERE product_id = ? ORDER BY id DESC LIMIT 10", (product_id,))
    configs = c.fetchall()
    conn.close()
    if not configs:
        text = "📭 هیچ کانفیگی موجود نیست."
    else:
        text = f"📦 کانفیگ‌ها (آخرین ۱۰ عدد):\n\n"
        for cfg in configs:
            status = "✅" if cfg[2] == 0 else "❌"
            text += f"{status} `{cfg[1][:40]}...` (ID: {cfg[0]})\n"
    markup = types.InlineKeyboardMarkup()
    markup.add(types.InlineKeyboardButton("🗑️ حذف استفاده‌شده‌ها", callback_data=f"repo_clear_{product_id}"))
    markup.add(types.InlineKeyboardButton("🔙 بازگشت", callback_data="repo_back"))
    bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=markup, parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "📊 آمار ربات" and is_admin(m.from_user.id))
def stats_panel(message):
    stats = get_stats()
    text = f"📊 *آمار*\n👥 کاربران: {stats['total_users']}\n📦 کانفیگ موجود: {stats['total_configs']}\n🛍️ سرویس‌ها: {stats['total_services']}\n✅ فعال: {stats['active_services']}\n💰 درآمد: {stats['total_revenue']:,}"
    bot.send_message(message.from_user.id, text, parse_mode='Markdown', reply_markup=get_admin_keyboard())

@bot.message_handler(func=lambda m: m.text == "🛠️ مدیریت کاربران" and is_admin(m.from_user.id))
def user_management(message):
    markup = types.InlineKeyboardMarkup(row_width=2)
    markup.add(
        types.InlineKeyboardButton("🔍 اطلاعات", callback_data="admin_userinfo"),
        types.InlineKeyboardButton("💰 افزایش موجودی", callback_data="admin_addbal"),
        types.InlineKeyboardButton("🚫 بن/آنبن", callback_data="admin_ban"),
        types.InlineKeyboardButton("👑 نمایندگی", callback_data="admin_agent"),
        types.InlineKeyboardButton("📋 لیست کاربران", callback_data="admin_userslist")
    )
    bot.send_message(message.from_user.id, "🛠️ *مدیریت کاربران*", reply_markup=markup, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data.startswith("admin_"))
def admin_callback(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    action = call.data.split("_")[1]
    if action == "userinfo":
        user_states[user_id] = "admin_get_user"
        bot.edit_message_text("🔍 آیدی کاربر را وارد کن:", call.message.chat.id, call.message.message_id)
    elif action == "addbal":
        user_states[user_id] = "admin_add_balance"
        bot.edit_message_text("💰 فرمت: `user_id|amount`", call.message.chat.id, call.message.message_id, parse_mode='Markdown')
    elif action == "ban":
        user_states[user_id] = "admin_ban_user"
        bot.edit_message_text("🚫 آیدی کاربر را وارد کن:", call.message.chat.id, call.message.message_id)
    elif action == "agent":
        user_states[user_id] = "admin_agent_user"
        bot.edit_message_text("👑 آیدی کاربر را وارد کن:", call.message.chat.id, call.message.message_id)
    elif action == "userslist":
        users = get_all_users()
        text = f"📋 *لیست کاربران ({len(users)} نفر)*\n\n" + "\n".join([f"{i+1}. `{u[0]}`" for i, u in enumerate(users[:20])])
        bot.edit_message_text(text, call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.message_handler(func=lambda m: m.text == "📢 ارسال همگانی" and is_admin(m.from_user.id))
def broadcast_panel(message):
    user_states[message.from_user.id] = "waiting_broadcast"
    bot.send_message(message.from_user.id, "📢 پیام همگانی را بنویس:", reply_markup=get_admin_keyboard())

@bot.message_handler(func=lambda m: m.text == "⚙️ تنظیمات عمومی" and is_admin(m.from_user.id))
def settings_panel(message):
    markup = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    markup.row("💳 تغییر کارت", "🎲 تغییر جایزه")
    markup.row("➕ افزودن محصول", "❌ حذف محصول")
    markup.row("🔗 تنظیمات جوین", "🔒 قفل ربات")
    markup.row("🔙 بازگشت به منوی اصلی")
    bot.send_message(message.from_user.id, "⚙️ *تنظیمات عمومی*", reply_markup=markup, parse_mode='Markdown')

# ============================================================
# هندلرهای پیام و state‌ها
# ============================================================

@bot.message_handler(func=lambda m: True)
def handle_messages(message):
    user_id = message.from_user.id
    text = message.text
    state = user_states.get(user_id)

    if isinstance(state, dict) and state.get("action") == "add_config_test":
        configs = text.split('\n')
        conn = sqlite3.connect(DB_NAME)
        c = conn.cursor()
        added = 0
        for cfg in configs:
            if cfg.strip():
                c.execute("INSERT INTO configs (product_id, content) VALUES (?, ?)", (0, cfg.strip()))
                added += 1
        conn.commit()
        conn.close()
        user_states[user_id] = None
        bot.send_message(user_id, f"✅ {added} کانفیگ به مخزن تست اضافه شد.", reply_markup=get_admin_keyboard())
        return

    if isinstance(state, dict) and state.get("action") == "add_config_buy":
        product_id = state.get("product_id")
        configs = text.split('\n')
        conn = sqlite3.connect(DB_NAME)
        c = conn.cursor()
        added = 0
        for cfg in configs:
            if cfg.strip():
                c.execute("INSERT INTO configs (product_id, content) VALUES (?, ?)", (product_id, cfg.strip()))
                added += 1
        conn.commit()
        conn.close()
        user_states[user_id] = None
        product = get_product(product_id)
        bot.send_message(user_id, f"✅ {added} کانفیگ به محصول {product[1]} اضافه شد.", reply_markup=get_admin_keyboard())
        return

    if state == "waiting_charge_amount":
        try:
            amount = int(text)
            user_states[user_id] = {"action": "waiting_receipt_charge", "amount": amount}
            bot.send_message(user_id, f"💰 مبلغ {amount:,} تومان\n📸 تصویر رسید را ارسال کن:", parse_mode='Markdown')
        except:
            bot.send_message(user_id, "❌ عدد معتبر وارد کن.")
        return

    if state == "waiting_broadcast" and is_admin(user_id):
        user_states[user_id] = None
        users = get_all_users()
        sent = 0
        for u in users:
            try:
                bot.send_message(u[0], f"📢 *پیام همگانی*\n\n{text}", parse_mode='Markdown')
                sent += 1
                time.sleep(0.05)
            except:
                pass
        bot.send_message(user_id, f"✅ به {sent} کاربر ارسال شد.", reply_markup=get_admin_keyboard())
        return

    if state == "waiting_support":
        user_states[user_id] = None
        bot.forward_message(OWNER_ID, user_id, message.message_id)
        bot.send_message(user_id, "✅ پیام شما به ادمین ارسال شد.")
        return

    if state == "admin_get_user" and is_admin(user_id):
        user_states[user_id] = None
        try:
            target = int(text)
            u = get_user(target)
            bot.send_message(user_id, f"👤 *کاربر {target}*\n💰 موجودی: {u['balance']:,}\n👑 نماینده: {'بله' if u['is_agent'] else 'خیر'}\n🚫 بن: {'بله' if u['is_banned'] else 'خیر'}", parse_mode='Markdown')
        except:
            bot.send_message(user_id, "❌ آیدی نامعتبر!")
        return

    if state == "admin_add_balance" and is_admin(user_id):
        user_states[user_id] = None
        try:
            parts = text.split('|')
            target = int(parts[0])
            amount = int(parts[1])
            update_balance(target, amount)
            add_transaction(target, "admin_add", amount, "افزایش توسط ادمین")
            bot.send_message(user_id, f"✅ {amount:,} تومان به {target} اضافه شد.")
        except:
            bot.send_message(user_id, "❌ فرمت اشتباه! استفاده: `user_id|amount`")
        return

    if state == "admin_ban_user" and is_admin(user_id):
        user_states[user_id] = None
        try:
            target = int(text)
            conn = sqlite3.connect(DB_NAME)
            c = conn.cursor()
            c.execute("SELECT is_banned FROM users WHERE user_id = ?", (target,))
            row = c.fetchone()
            new_status = 0 if row and row[0] == 1 else 1
            c.execute("UPDATE users SET is_banned = ? WHERE user_id = ?", (new_status, target))
            conn.commit()
            conn.close()
            status = "بن شد" if new_status == 1 else "آنبن شد"
            bot.send_message(user_id, f"✅ کاربر {target} {status}.")
        except:
            bot.send_message(user_id, "❌ آیدی نامعتبر!")
        return

    if state == "admin_agent_user" and is_admin(user_id):
        user_states[user_id] = None
        try:
            target = int(text)
            conn = sqlite3.connect(DB_NAME)
            c = conn.cursor()
            c.execute("SELECT is_agent FROM users WHERE user_id = ?", (target,))
            row = c.fetchone()
            new_status = 0 if row and row[0] == 1 else 1
            c.execute("UPDATE users SET is_agent = ? WHERE user_id = ?", (new_status, target))
            conn.commit()
            conn.close()
            status = "نماینده شد" if new_status == 1 else "نمایندگی لغو شد"
            bot.send_message(user_id, f"✅ کاربر {target} {status}.")
        except:
            bot.send_message(user_id, "❌ آیدی نامعتبر!")
        return

    if state == "join_set_channel" and is_admin(user_id):
        user_states[user_id] = None
        new_channel = text.strip()
        if not new_channel.startswith("@"):
            new_channel = "@" + new_channel
        set_channel_setting("channel_id", new_channel)
        bot.send_message(user_id, f"✅ کانال اجباری به `{new_channel}` تغییر کرد.", parse_mode='Markdown')
        return

    user_states[user_id] = None

    if text == "💳 تغییر کارت" and is_admin(user_id):
        user_states[user_id] = "change_card"
        bot.send_message(user_id, "💳 شماره کارت جدید را وارد کن:")
    elif text == "🎲 تغییر جایزه" and is_admin(user_id):
        user_states[user_id] = "change_lucky"
        bot.send_message(user_id, "🎲 مبلغ جایزه جدید را وارد کن (تومان):")
    elif text == "➕ افزودن محصول" and is_admin(user_id):
        user_states[user_id] = "add_product"
        bot.send_message(user_id, "📦 فرمت: `نام|قیمت|مدت(روز)`\nمثال: `اشتراک ماهانه|100000|30`")
    elif text == "❌ حذف محصول" and is_admin(user_id):
        products = get_products()
        if not products:
            bot.send_message(user_id, "❌ محصولی موجود نیست.")
            return
        markup = types.InlineKeyboardMarkup(row_width=1)
        for p in products:
            markup.add(types.InlineKeyboardButton(f"🗑️ {p[1]}", callback_data=f"delprod_{p[0]}"))
        bot.send_message(user_id, "🗑️ محصول را انتخاب کن:", reply_markup=markup)
    elif text == "🔒 قفل ربات" and is_admin(user_id):
        BOT_SETTINGS["locked"] = not BOT_SETTINGS["locked"]
        status = "قفل" if BOT_SETTINGS["locked"] else "باز"
        bot.send_message(user_id, f"🔒 ربات {status} شد.")
    elif text == "🔗 تنظیمات جوین" and is_admin(user_id):
        channel = get_channel_id()
        enabled = is_force_join_enabled()
        status_text = "✅ فعال" if enabled else "❌ غیرفعال"
        text_join = f"🔗 *تنظیمات جوین*\n📢 کانال: `{channel}`\n📊 وضعیت: {status_text}"
        bot.send_message(user_id, text_join, reply_markup=get_join_settings_keyboard(), parse_mode='Markdown')

    if state == "change_card" and is_admin(user_id):
        user_states[user_id] = None
        global CARD_NUMBER
        CARD_NUMBER = text
        bot.send_message(user_id, f"✅ شماره کارت به {text} تغییر کرد.")
    if state == "change_lucky" and is_admin(user_id):
        user_states[user_id] = None
        global LUCKY_BONUS
        try:
            LUCKY_BONUS = int(text)
            bot.send_message(user_id, f"✅ جایزه شانس به {text:,} تومان تغییر کرد.")
        except:
            bot.send_message(user_id, "❌ عدد معتبر وارد کن.")
    if state == "add_product" and is_admin(user_id):
        user_states[user_id] = None
        try:
            parts = text.split('|')
            name = parts[0]
            price = int(parts[1])
            duration = int(parts[2])
            conn = sqlite3.connect(DB_NAME)
            c = conn.cursor()
            c.execute("INSERT INTO products (name, price, duration) VALUES (?, ?, ?)", (name, price, duration))
            conn.commit()
            conn.close()
            bot.send_message(user_id, f"✅ محصول {name} اضافه شد.")
        except:
            bot.send_message(user_id, "❌ فرمت اشتباه!")

# ============================================================
# کالبک‌های حذف محصول، رسید، نمایندگی و جوین
# ============================================================

@bot.callback_query_handler(func=lambda call: call.data.startswith("delprod_"))
def delete_product(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    prod_id = int(call.data.split("_")[1])
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("DELETE FROM products WHERE id = ?", (prod_id,))
    c.execute("DELETE FROM configs WHERE product_id = ?", (prod_id,))
    conn.commit()
    conn.close()
    bot.edit_message_text("🗑️ محصول حذف شد.", call.message.chat.id, call.message.message_id)

@bot.callback_query_handler(func=lambda call: call.data.startswith("ok_receipt_"))
def ok_receipt(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    parts = call.data.split("_")
    target = int(parts[2])
    prod_id = int(parts[3])
    product = get_product(prod_id)
    if not product:
        bot.answer_callback_query(call.id, "محصول یافت نشد!", show_alert=True)
        return
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("SELECT id, content FROM configs WHERE product_id = ? AND is_used = 0 LIMIT 1", (prod_id,))
    config = c.fetchone()
    if not config:
        bot.answer_callback_query(call.id, "مخزن خالی است!", show_alert=True)
        conn.close()
        return
    u = get_user(target)
    final_price = int(product[2] * 0.9) if u["is_agent"] == 1 else product[2]
    expire = datetime.now() + timedelta(days=product[3])
    c.execute("UPDATE configs SET is_used = 1, used_by = ?, used_date = ? WHERE id = ?", (target, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    c.execute("INSERT INTO user_services (user_id, product_id, product_name, config_content, price, buy_date, expire_date, config_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
              (target, prod_id, product[1], config[1], final_price, datetime.now().strftime("%Y-%m-%d %H:%M:%S"), expire.strftime("%Y-%m-%d %H:%M:%S"), config[0]))
    conn.commit()
    conn.close()
    add_transaction(target, "buy", -final_price, f"خرید {product[1]} (تایید ادمین)")
    bot.edit_message_text(f"✅ خرید کاربر {target} تایید شد.", call.message.chat.id, call.message.message_id)
    try:
        bot.send_message(target, f"✅ *خرید شما تایید شد!*\n📦 {product[1]}\n🔑 `{config[1]}`\n⏳ انقضا: {expire.strftime('%Y-%m-%d %H:%M:%S')}", parse_mode='Markdown')
    except:
        pass

@bot.callback_query_handler(func=lambda call: call.data.startswith("no_receipt_"))
def no_receipt(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    target = int(call.data.split("_")[2])
    bot.edit_message_text(f"❌ رسید کاربر {target} رد شد.", call.message.chat.id, call.message.message_id)

@bot.callback_query_handler(func=lambda call: call.data.startswith("ok_agency_"))
def ok_agency(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    target = int(call.data.split("_")[2])
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute("UPDATE users SET is_agent = 1 WHERE user_id = ?", (target,))
    conn.commit()
    conn.close()
    bot.edit_message_text(f"✅ کاربر {target} نماینده شد.", call.message.chat.id, call.message.message_id)
    try:
        bot.send_message(target, "👑 *تبریک! شما نماینده شدید.*\n✅ ۱۰٪ تخفیف دائمی\n✅ سهمیه تست روزانه")
    except:
        pass

@bot.callback_query_handler(func=lambda call: call.data.startswith("no_agency_"))
def no_agency(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    target = int(call.data.split("_")[2])
    bot.edit_message_text(f"❌ درخواست نمایندگی کاربر {target} رد شد.", call.message.chat.id, call.message.message_id)

@bot.callback_query_handler(func=lambda call: call.data == "join_status")
def join_status(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    channel = get_channel_id()
    enabled = is_force_join_enabled()
    status_text = "✅ فعال" if enabled else "❌ غیرفعال"
    text = f"📋 *وضعیت جوین*\n📢 کانال: `{channel}`\n📊 وضعیت: {status_text}"
    bot.edit_message_text(text, call.message.chat.id, call.message.message_id, reply_markup=get_join_settings_keyboard(), parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "join_toggle")
def join_toggle(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    current = is_force_join_enabled()
    new_status = not current
    set_channel_setting("force_join_enabled", "true" if new_status else "false")
    bot.answer_callback_query(call.id, f"جوین اجباری {'فعال' if new_status else 'غیرفعال'} شد!", show_alert=True)
    join_status(call)

@bot.callback_query_handler(func=lambda call: call.data == "join_set_channel")
def join_set_channel(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    user_states[user_id] = "join_set_channel"
    bot.edit_message_text("✏️ *تغییر کانال*\nلطفاً آیدی کانال جدید را وارد کنید:\nمثال: `@my_channel`", call.message.chat.id, call.message.message_id, parse_mode='Markdown')

@bot.callback_query_handler(func=lambda call: call.data == "join_back")
def join_back(call):
    user_id = call.from_user.id
    if not is_admin(user_id):
        bot.answer_callback_query(call.id, "دسترسی ندارید!", show_alert=True)
        return
    bot.delete_message(call.message.chat.id, call.message.message_id)
    settings_panel(call.message)

@bot.callback_query_handler(func=lambda call: call.data == "back_main")
def back_main_callback(call):
    user_id = call.from_user.id
    bot.delete_message(call.message.chat.id, call.message.message_id)
    u = get_user(user_id)
    bot.send_message(user_id, f"🎯 *منوی اصلی*\n💰 موجودی: {u['balance']:,}", reply_markup=get_main_keyboard(user_id), parse_mode='Markdown')

# ============================================================
# هندلر رسید (عکس)
# ============================================================

@bot.message_handler(content_types=['photo'])
def handle_receipt(message):
    user_id = message.from_user.id
    state = user_states.get(user_id)
    if isinstance(state, dict) and state.get("action") == "waiting_receipt_charge":
        amount = state.get("amount")
        user_states[user_id] = None
        markup = types.InlineKeyboardMarkup()
        markup.add(types.InlineKeyboardButton("✅ تایید", callback_data=f"ok_receipt_{user_id}_charge_{amount}"))
        markup.add(types.InlineKeyboardButton("❌ رد", callback_data=f"no_receipt_{user_id}"))
        bot.send_photo(OWNER_ID, message.photo[-1].file_id, caption=f"💳 درخواست شارژ\n👤 {user_id}\n💰 {amount:,} تومان", reply_markup=markup)
        bot.send_message(user_id, "✅ رسید ارسال شد.")
        return
    if isinstance(state, dict) and state.get("action") == "waiting_receipt":
        prod_id = state.get("product_id")
        user_states[user_id] = None
        markup = types.InlineKeyboardMarkup()
        markup.add(types.InlineKeyboardButton("✅ تایید", callback_data=f"ok_receipt_{user_id}_{prod_id}"))
        markup.add(types.InlineKeyboardButton("❌ رد", callback_data=f"no_receipt_{user_id}"))
        bot.send_photo(OWNER_ID, message.photo[-1].file_id, caption=f"💳 درخواست خرید\n👤 {user_id}\n📦 محصول: {prod_id}", reply_markup=markup)
        bot.send_message(user_id, "✅ رسید ارسال شد.")

# ============================================================
# اجرا
# ============================================================

print("=" * 50)
print("🤖 ربات فروش کانفیگ نسخه ۵.۰")
print(f"👑 مالک: {OWNER_ID}")
print(f"📢 کانال: {get_channel_id()}")
print(f"🔗 جوین اجباری: {'فعال' if is_force_join_enabled() else 'غیرفعال'}")
print("=" * 50)
print("✅ ربات با موفقیت روشن شد!")

bot.infinity_polling()