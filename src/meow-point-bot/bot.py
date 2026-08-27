# ========== بخش ۱: ایمپورت‌ها و تنظیمات ==========
import logging
import sqlite3
import time
import random
import re
from datetime import datetime
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup, ChatMember
from telegram.ext import Application, CommandHandler, CallbackQueryHandler, ContextTypes, MessageHandler, filters

# ---------- تنظیمات ----------
BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"        # توکن ربات را از @BotFather بگیر
OWNER_ID = 123456789                      # آیدی عددی خودت را از @userinfobot بگیر
GROUP_LINK = "https://t.me/YourGroupUsername"   # لینک گروه/کانالت
BOT_NAME = "هاپ پوینت"

logging.basicConfig(level=logging.INFO)

# ========== بخش ۲: کلاس دیتابیس ==========
class Database:
    def __init__(self, db_file='bot.db'):
        self.conn = sqlite3.connect(db_file, check_same_thread=False)
        self.cursor = self.conn.cursor()
        self.create_tables()

    def create_tables(self):
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY,
                username TEXT,
                points INTEGER DEFAULT 0,
                level INTEGER DEFAULT 1,
                exp INTEGER DEFAULT 0,
                pet_type TEXT DEFAULT 'none',
                pet_health INTEGER DEFAULT 100,
                pet_power INTEGER DEFAULT 10,
                last_meow INTEGER DEFAULT 0
            )
        ''')
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS bank (
                user_id INTEGER PRIMARY KEY,
                balance INTEGER DEFAULT 0
            )
        ''')
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS factory (
                user_id INTEGER PRIMARY KEY,
                level INTEGER DEFAULT 1,
                production INTEGER DEFAULT 0,
                last_collect INTEGER DEFAULT 0
            )
        ''')
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS inventory (
                user_id INTEGER,
                item_name TEXT,
                quantity INTEGER DEFAULT 0,
                PRIMARY KEY (user_id, item_name)
            )
        ''')
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS bans (
                user_id INTEGER PRIMARY KEY,
                banned_until INTEGER DEFAULT 0
            )
        ''')
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS items (
                name TEXT PRIMARY KEY,
                price INTEGER,
                type TEXT
            )
        ''')
        default_items = [
            ('استخوان', 50, 'bone'),
            ('قلاب ماهیگیری', 100, 'hook'),
            ('گربه معمولی', 200, 'pet'),
            ('گربه جنگجو', 500, 'pet'),
            ('سگ نگهبان', 400, 'pet')
        ]
        for name, price, type_ in default_items:
            self.cursor.execute('INSERT OR IGNORE INTO items (name, price, type) VALUES (?, ?, ?)', (name, price, type_))
        self.conn.commit()

    def get_user(self, user_id):
        self.cursor.execute('SELECT * FROM users WHERE user_id = ?', (user_id,))
        return self.cursor.fetchone()

    def create_user(self, user_id, username):
        self.cursor.execute('INSERT OR IGNORE INTO users (user_id, username) VALUES (?, ?)', (user_id, username))
        self.conn.commit()

    def update_username(self, user_id, username):
        self.cursor.execute('UPDATE users SET username = ? WHERE user_id = ?', (username, user_id))
        self.conn.commit()

    def get_user_by_username(self, username):
        self.cursor.execute('SELECT * FROM users WHERE username = ?', (username,))
        return self.cursor.fetchone()

    def get_points(self, user_id):
        self.cursor.execute('SELECT points FROM users WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row[0] if row else 0

    def add_points(self, user_id, amount):
        self.cursor.execute('UPDATE users SET points = points + ? WHERE user_id = ?', (amount, user_id))
        self.conn.commit()

    def set_points(self, user_id, amount):
        self.cursor.execute('UPDATE users SET points = ? WHERE user_id = ?', (amount, user_id))
        self.conn.commit()

    def get_level(self, user_id):
        self.cursor.execute('SELECT level FROM users WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row[0] if row else 1

    def set_level(self, user_id, level):
        self.cursor.execute('UPDATE users SET level = ? WHERE user_id = ?', (level, user_id))
        self.conn.commit()

    def get_exp(self, user_id):
        self.cursor.execute('SELECT exp FROM users WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row[0] if row else 0

    def add_exp(self, user_id, amount):
        self.cursor.execute('UPDATE users SET exp = exp + ? WHERE user_id = ?', (amount, user_id))
        self.conn.commit()
        level = self.get_level(user_id)
        exp = self.get_exp(user_id)
        if exp >= level * 100:
            self.set_level(user_id, level + 1)
            self.add_exp(user_id, -(level * 100))
            return True
        return False

    def get_last_meow(self, user_id):
        self.cursor.execute('SELECT last_meow FROM users WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row[0] if row else 0

    def set_last_meow(self, user_id, timestamp):
        self.cursor.execute('UPDATE users SET last_meow = ? WHERE user_id = ?', (timestamp, user_id))
        self.conn.commit()

    def get_pet(self, user_id):
        self.cursor.execute('SELECT pet_type, pet_health, pet_power FROM users WHERE user_id = ?', (user_id,))
        return self.cursor.fetchone()

    def set_pet(self, user_id, pet_type, health=100, power=10):
        self.cursor.execute('UPDATE users SET pet_type = ?, pet_health = ?, pet_power = ? WHERE user_id = ?', (pet_type, health, power, user_id))
        self.conn.commit()

    def update_pet_health(self, user_id, health):
        self.cursor.execute('UPDATE users SET pet_health = ? WHERE user_id = ?', (health, user_id))
        self.conn.commit()

    def get_bank_balance(self, user_id):
        self.cursor.execute('SELECT balance FROM bank WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row[0] if row else 0

    def set_bank_balance(self, user_id, amount):
        self.cursor.execute('INSERT OR REPLACE INTO bank (user_id, balance) VALUES (?, ?)', (user_id, amount))
        self.conn.commit()

    def add_bank_balance(self, user_id, amount):
        self.cursor.execute('INSERT INTO bank (user_id, balance) VALUES (?, ?) ON CONFLICT(user_id) DO UPDATE SET balance = balance + ?', (user_id, amount, amount))
        self.conn.commit()

    def get_factory(self, user_id):
        self.cursor.execute('SELECT level, production, last_collect FROM factory WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row if row else (1, 0, 0)

    def set_factory(self, user_id, level, production, last_collect):
        self.cursor.execute('INSERT OR REPLACE INTO factory (user_id, level, production, last_collect) VALUES (?, ?, ?, ?)', (user_id, level, production, last_collect))
        self.conn.commit()

    def add_factory_production(self, user_id, amount):
        self.cursor.execute('INSERT INTO factory (user_id, production) VALUES (?, ?) ON CONFLICT(user_id) DO UPDATE SET production = production + ?', (user_id, amount, amount))
        self.conn.commit()

    def upgrade_factory(self, user_id):
        level, production, last_collect = self.get_factory(user_id)
        new_level = level + 1
        self.cursor.execute('UPDATE factory SET level = ? WHERE user_id = ?', (new_level, user_id))
        self.conn.commit()
        return new_level

    def add_item(self, user_id, item_name, quantity=1):
        self.cursor.execute('INSERT INTO inventory (user_id, item_name, quantity) VALUES (?, ?, ?) ON CONFLICT(user_id, item_name) DO UPDATE SET quantity = quantity + ?', (user_id, item_name, quantity, quantity))
        self.conn.commit()

    def get_inventory(self, user_id):
        self.cursor.execute('SELECT item_name, quantity FROM inventory WHERE user_id = ?', (user_id,))
        return self.cursor.fetchall()

    def remove_item(self, user_id, item_name, quantity=1):
        self.cursor.execute('UPDATE inventory SET quantity = quantity - ? WHERE user_id = ? AND item_name = ?', (quantity, user_id, item_name))
        self.conn.commit()
        self.cursor.execute('DELETE FROM inventory WHERE user_id = ? AND item_name = ? AND quantity <= 0', (user_id, item_name))
        self.conn.commit()

    def get_all_items(self):
        self.cursor.execute('SELECT name, price, type FROM items')
        return self.cursor.fetchall()

    def get_item_price(self, name):
        self.cursor.execute('SELECT price FROM items WHERE name = ?', (name,))
        row = self.cursor.fetchone()
        return row[0] if row else None

    def is_banned(self, user_id):
        now = int(time.time())
        self.cursor.execute('SELECT banned_until FROM bans WHERE user_id = ?', (user_id,))
        row = self.cursor.fetchone()
        return row and row[0] > now

    def ban_user(self, user_id, duration_seconds):
        until = int(time.time()) + duration_seconds
        self.cursor.execute('INSERT OR REPLACE INTO bans (user_id, banned_until) VALUES (?, ?)', (user_id, until))
        self.conn.commit()

    def unban_user(self, user_id):
        self.cursor.execute('DELETE FROM bans WHERE user_id = ?', (user_id,))
        self.conn.commit()

    def get_top_users(self, limit=10):
        self.cursor.execute('SELECT user_id, username, points, level FROM users ORDER BY points DESC LIMIT ?', (limit,))
        return self.cursor.fetchall()

    def get_total_users(self):
        self.cursor.execute('SELECT COUNT(*) FROM users')
        return self.cursor.fetchone()[0]

    def get_total_points(self):
        self.cursor.execute('SELECT SUM(points) FROM users')
        row = self.cursor.fetchone()
        return row[0] if row[0] else 0

db = Database()

# ========== بخش ۳: توابع کمکی ==========
def is_admin(update):
    try:
        member = update.effective_chat.get_member(update.effective_user.id)
        return member.status in [ChatMember.ADMINISTRATOR, ChatMember.OWNER]
    except:
        return False

def is_owner(update):
    return update.effective_user.id == OWNER_ID

def extract_username(text):
    match = re.search(r'@(\w+)', text)
    return match.group(1) if match else None

def extract_amount(text):
    match = re.search(r'(\d+)', text)
    return int(match.group(1)) if match else None

def format_profile(data):
    user_id, username, points, level, exp, pet_type, pet_health, pet_power, last_meow = data
    txt = f"🐾 *پروفایل {username}*\n"
    txt += f"🆔 شناسه: `{user_id}`\n"
    txt += f"⭐ امتیاز: *{points}*\n"
    txt += f"📊 سطح: *{level}*\n"
    txt += f"📈 تجربه: {exp} / {level*100}\n"
    if pet_type != 'none':
        txt += f"🐱 حیوان: {pet_type} (❤️{pet_health} ⚔️{pet_power})\n"
    else:
        txt += "🐱 حیوان: ❌ ندارید\n"
    return txt

def format_top(users):
    txt = "🏆 *لیدربرد برترین‌ها*\n\n"
    for i, (uid, username, points, level) in enumerate(users, 1):
        medal = "🥇" if i == 1 else "🥈" if i == 2 else "🥉" if i == 3 else f"{i}."
        txt += f"{medal} @{username} — ⭐ {points} امتیاز (سطح {level})\n"
    return txt

# ========== بخش ۴: هندلرهای کاربر ==========
async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat
    user = update.effective_user
    if chat.type not in ['group', 'supergroup']:
        keyboard = [[InlineKeyboardButton("➕ اضافه کردن به گروه", url=GROUP_LINK)]]
        text = (
            f"🐾 *سلام {user.first_name}!*\n\n"
            "من یه ربات جمع‌آوری پوینت و مدیریت گروه هستم.\n"
            "برای استفاده، من رو به گروهت اضافه کن.\n\n"
            "✨ *دستورات اصلی:*\n"
            "/meow — جمع‌آوری پوینت (هر ۵ دقیقه)\n"
            "/profile — پروفایل\n"
            "/top — لیدربرد\n"
            "/transfer [مقدار] @username — انتقال امتیاز\n"
            "/bank — بانک\n"
            "/factory — کارخانه\n"
            "/market — بازار\n"
            "/city — شهر\n"
            "/games — منوی بازی‌ها\n"
            "/fight — جنگ گربه‌ها (ریپلای)\n"
        )
        await update.message.reply_text(text, parse_mode='Markdown', reply_markup=InlineKeyboardMarkup(keyboard))
        return
    db.create_user(user.id, user.username or user.first_name)
    await update.message.reply_text(f"🐾 {user.first_name} به گروه خوش اومدی! از دستورات استفاده کن.")

async def profile_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("این دستور فقط در گروه کار میکنه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 شما از ربات بن شدید.")
    data = db.get_user(user.id)
    if not data:
        db.create_user(user.id, user.username or user.first_name)
        data = db.get_user(user.id)
    await update.message.reply_text(format_profile(data), parse_mode='Markdown')

async def top_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("این دستور فقط در گروه کار میکنه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 شما از ربات بن شدید.")
    top_users = db.get_top_users(10)
    await update.message.reply_text(format_top(top_users), parse_mode='Markdown')

async def meow_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("این دستور فقط در گروه کار میکنه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 شما از ربات بن شدید.")
    now = int(time.time())
    last = db.get_last_meow(user.id)
    cooldown = 300
    if now - last < cooldown:
        rem = cooldown - (now - last)
        m, s = divmod(rem, 60)
        return await update.message.reply_text(f"⏳ صبر کن! {m} دقیقه و {s} ثانیه مونده.")
    points = 10 + db.get_level(user.id) * 2
    db.add_points(user.id, points)
    db.set_last_meow(user.id, now)
    level_up = db.add_exp(user.id, 5)
    msg = f"🐾 میو! {points} امتیاز گرفتی."
    if level_up:
        msg += f"\n🎉 سطحت به {db.get_level(user.id)} رسید!"
    await update.message.reply_text(msg)

async def transfer_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("این دستور فقط در گروه کار میکنه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 شما از ربات بن شدید.")
    text = update.message.text
    amount = extract_amount(text)
    target_username = extract_username(text)
    if not amount or not target_username:
        return await update.message.reply_text("❗ استفاده: /transfer [مقدار] @username")
    if amount <= 0:
        return await update.message.reply_text("❗ مقدار باید مثبت باشه.")
    if db.get_points(user.id) < amount:
        return await update.message.reply_text("❗ امتیاز کافی ندارید.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر مورد نظر یافت نشد.")
    if target[0] == user.id:
        return await update.message.reply_text("❗ نمیتونی به خودت بدی.")
    db.add_points(user.id, -amount)
    db.add_points(target[0], amount)
    await update.message.reply_text(f"✅ {amount} امتیاز به @{target_username} منتقل شد.")

# ========== بخش ۵: هندلرهای اقتصاد ==========
async def bank_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    balance = db.get_bank_balance(user.id)
    points = db.get_points(user.id)
    text = f"🏦 *بانک شما*\n💰 موجودی: {balance} سکه\n⭐ امتیاز قابل تبدیل: {points}\n\n/deposit [مقدار] — واریز\n/withdraw [مقدار] — برداشت"
    await update.message.reply_text(text, parse_mode='Markdown')

async def deposit_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    amount = extract_amount(update.message.text)
    if not amount or amount <= 0:
        return await update.message.reply_text("❗ مقدار معتبر وارد کن.")
    points = db.get_points(user.id)
    if points < amount:
        return await update.message.reply_text("❗ امتیاز کافی نیست.")
    db.add_points(user.id, -amount)
    db.add_bank_balance(user.id, amount)
    await update.message.reply_text(f"✅ {amount} امتیاز به بانک واریز شد. موجودی: {db.get_bank_balance(user.id)}")

async def withdraw_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    amount = extract_amount(update.message.text)
    if not amount or amount <= 0:
        return await update.message.reply_text("❗ مقدار معتبر وارد کن.")
    balance = db.get_bank_balance(user.id)
    if balance < amount:
        return await update.message.reply_text("❗ موجودی بانک کافی نیست.")
    db.set_bank_balance(user.id, balance - amount)
    db.add_points(user.id, amount)
    await update.message.reply_text(f"✅ {amount} سکه به امتیاز تبدیل شد. امتیاز فعلی: {db.get_points(user.id)}")

async def factory_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    level, prod, last = db.get_factory(user.id)
    text = f"🏭 *کارخانه*\nسطح: {level}\nتولید انباشته: {prod} واحد\nآخرین جمع‌آوری: {time.ctime(last) if last else 'هرگز'}\n\n/upgrade_factory — ارتقا (هزینه {level*100} امتیاز)\n/collect_factory — برداشت تولید"
    await update.message.reply_text(text, parse_mode='Markdown')

async def upgrade_factory(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    level, prod, last = db.get_factory(user.id)
    cost = level * 100
    if db.get_points(user.id) < cost:
        return await update.message.reply_text(f"❗ برای ارتقا به {cost} امتیاز نیاز داری.")
    db.add_points(user.id, -cost)
    new_level = db.upgrade_factory(user.id)
    await update.message.reply_text(f"✅ کارخانه به سطح {new_level} ارتقا یافت!")

async def collect_factory(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    level, prod, last = db.get_factory(user.id)
    if prod == 0:
        return await update.message.reply_text("❗ تولیدی برای برداشت نیست.")
    db.add_points(user.id, prod)
    db.set_factory(user.id, level, 0, int(time.time()))
    await update.message.reply_text(f"✅ {prod} امتیاز از کارخانه برداشت شد.")

async def market_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    items = db.get_all_items()
    text = "🛍 *بازار*\n\n"
    for name, price, type_ in items:
        text += f"• {name} — {price} امتیاز ({type_})\n"
    text += "\n/buy [نام کالا] [تعداد] — خرید"
    await update.message.reply_text(text, parse_mode='Markdown')

async def buy_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    parts = update.message.text.split(maxsplit=2)
    if len(parts) < 3:
        return await update.message.reply_text("❗ استفاده: /buy [نام کالا] [تعداد]")
    item_name = parts[1]
    try:
        qty = int(parts[2])
    except:
        return await update.message.reply_text("❗ تعداد باید عدد باشد.")
    price = db.get_item_price(item_name)
    if price is None:
        return await update.message.reply_text("❗ کالا نامعتبر.")
    total_cost = price * qty
    if db.get_points(user.id) < total_cost:
        return await update.message.reply_text(f"❗ {total_cost} امتیاز نیاز داری.")
    db.add_points(user.id, -total_cost)
    db.add_item(user.id, item_name, qty)
    await update.message.reply_text(f"✅ {qty} عدد {item_name} خریداری شد. موجودی: {db.get_inventory(user.id)}")

async def city_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    total_users = db.get_total_users()
    total_points = db.get_total_points()
    text = f"🏰 *وضعیت شهر گروه*\n👥 شهروندان: {total_users}\n⭐ مجموع امتیازات: {total_points}\n📈 رونق اقتصادی در حال پیشرفت!"
    await update.message.reply_text(text, parse_mode='Markdown')

# ========== بخش ۶: هندلرهای بازی ==========
async def games_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    keyboard = [
        [InlineKeyboardButton("⚔️ جنگ گربه‌ها", callback_data='fight_now')],
        [InlineKeyboardButton("🐱 خرید گربه", callback_data='buy_pet')],
        [InlineKeyboardButton("🦴 خرید استخوان", callback_data='buy_bone')],
        [InlineKeyboardButton("🎣 خرید قلاب", callback_data='buy_hook')],
    ]
    await update.message.reply_text("🎲 *منوی بازی‌ها*", parse_mode='Markdown', reply_markup=InlineKeyboardMarkup(keyboard))

async def fight_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    user = update.effective_user
    if db.is_banned(user.id):
        return await update.message.reply_text("🚫 بن شدید.")
    pet = db.get_pet(user.id)
    if not pet or pet[0] == 'none':
        return await update.message.reply_text("❗ شما گربه ندارید. اول بخرید.")
    reply = update.message.reply_to_message
    if not reply:
        return await update.message.reply_text("❗ به پیام حریف ریپلای کن.")
    opp_id = reply.from_user.id
    if opp_id == user.id:
        return await update.message.reply_text("❗ نمی‌تونی با خودت بجنگی.")
    opp_pet = db.get_pet(opp_id)
    if not opp_pet or opp_pet[0] == 'none':
        return await update.message.reply_text("❗ حریف گربه نداره.")
    my_health, my_power = pet[1], pet[2]
    opp_health, opp_power = opp_pet[1], opp_pet[2]
    my_dmg = random.randint(1, my_power)
    opp_dmg = random.randint(1, opp_power)
    my_health = max(0, my_health - opp_dmg)
    opp_health = max(0, opp_health - my_dmg)
    db.update_pet_health(user.id, my_health)
    db.update_pet_health(opp_id, opp_health)
    if my_health == 0 and opp_health == 0:
        result = "🤝 مساوی! هر دو گربه مردند."
    elif my_health == 0:
        result = f"💀 باختی! گربه‌ات مرد. به حریف {my_dmg} آسیب زدی."
    elif opp_health == 0:
        reward = random.randint(10, 30)
        db.add_points(user.id, reward)
        result = f"🎉 بردی! گربه حریف مرد. {reward} امتیاز گرفتی."
    else:
        result = f"⚔️ جنگ تموم شد! سلامت تو: {my_health} — حریف: {opp_health}"
    await update.message.reply_text(result, parse_mode='Markdown')

async def callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    data = query.data
    if data == 'fight_now':
        await query.edit_message_text("برای جنگ، دستور /fight رو با ریپلای به حریف بزن.")
    elif data == 'buy_pet':
        await query.edit_message_text("برای خرید گربه: /buy گربه معمولی [تعداد] یا /buy گربه جنگجو [تعداد]")
    elif data == 'buy_bone':
        await query.edit_message_text("برای خرید استخوان: /buy استخوان [تعداد]")
    elif data == 'buy_hook':
        await query.edit_message_text("برای خرید قلاب: /buy قلاب ماهیگیری [تعداد]")

# ========== بخش ۷: هندلرهای ادمین و مالک ==========
async def add_points_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 2:
        return await update.message.reply_text("❗ استفاده: /addpoints @username [مقدار]")
    target_username = args[0].replace('@', '')
    try:
        amount = int(args[1])
    except:
        return await update.message.reply_text("❗ مقدار عددی وارد کن.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    db.add_points(target[0], amount)
    await update.message.reply_text(f"✅ {amount} امتیاز به @{target_username} اضافه شد. امتیاز جدید: {db.get_points(target[0])}")

async def remove_points_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 2:
        return await update.message.reply_text("❗ استفاده: /removepoints @username [مقدار]")
    target_username = args[0].replace('@', '')
    try:
        amount = int(args[1])
    except:
        return await update.message.reply_text("❗ مقدار عددی وارد کن.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    if db.get_points(target[0]) < amount:
        return await update.message.reply_text("❗ کاربر به اندازه کافی امتیاز نداره.")
    db.add_points(target[0], -amount)
    await update.message.reply_text(f"✅ {amount} امتیاز از @{target_username} کم شد. امتیاز جدید: {db.get_points(target[0])}")

async def add_level_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 2:
        return await update.message.reply_text("❗ استفاده: /addlevel @username [تعداد]")
    target_username = args[0].replace('@', '')
    try:
        amount = int(args[1])
    except:
        return await update.message.reply_text("❗ مقدار عددی وارد کن.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    new_level = db.get_level(target[0]) + amount
    db.set_level(target[0], new_level)
    await update.message.reply_text(f"✅ سطح @{target_username} به {new_level} تغییر کرد.")

async def remove_level_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 2:
        return await update.message.reply_text("❗ استفاده: /removelevel @username [تعداد]")
    target_username = args[0].replace('@', '')
    try:
        amount = int(args[1])
    except:
        return await update.message.reply_text("❗ مقدار عددی وارد کن.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    new_level = max(1, db.get_level(target[0]) - amount)
    db.set_level(target[0], new_level)
    await update.message.reply_text(f"✅ سطح @{target_username} به {new_level} کاهش یافت.")

async def ban_user_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 2:
        return await update.message.reply_text("❗ استفاده: /ban @username [دقیقه]")
    target_username = args[0].replace('@', '')
    try:
        minutes = int(args[1])
    except:
        return await update.message.reply_text("❗ دقیقه را عدد وارد کن.")
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    duration = minutes * 60
    db.ban_user(target[0], duration)
    await update.message.reply_text(f"✅ @{target_username} به مدت {minutes} دقیقه بن شد.")

async def unban_user_admin(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not (is_admin(update) or is_owner(update)):
        return await update.message.reply_text("🚫 فقط ادمین‌ها.")
    if update.effective_chat.type not in ['group', 'supergroup']:
        return await update.message.reply_text("فقط در گروه.")
    args = context.args
    if len(args) < 1:
        return await update.message.reply_text("❗ استفاده: /unban @username")
    target_username = args[0].replace('@', '')
    target = db.get_user_by_username(target_username)
    if not target:
        return await update.message.reply_text("❗ کاربر یافت نشد.")
    db.unban_user(target[0])
    await update.message.reply_text(f"✅ بن @{target_username} برداشته شد.")

# ========== بخش ۸: راه‌اندازی ربات ==========
def main():
    app = Application.builder().token(BOT_TOKEN).build()

    app.add_handler(CommandHandler("start", start_command))
    app.add_handler(CommandHandler("profile", profile_command))
    app.add_handler(CommandHandler("top", top_command))
    app.add_handler(CommandHandler("meow", meow_command))
    app.add_handler(CommandHandler("transfer", transfer_command))

    app.add_handler(CommandHandler("bank", bank_command))
    app.add_handler(CommandHandler("deposit", deposit_command))
    app.add_handler(CommandHandler("withdraw", withdraw_command))
    app.add_handler(CommandHandler("factory", factory_command))
    app.add_handler(CommandHandler("upgrade_factory", upgrade_factory))
    app.add_handler(CommandHandler("collect_factory", collect_factory))
    app.add_handler(CommandHandler("market", market_command))
    app.add_handler(CommandHandler("buy", buy_command))
    app.add_handler(CommandHandler("city", city_command))

    app.add_handler(CommandHandler("games", games_command))
    app.add_handler(CommandHandler("fight", fight_command))
    app.add_handler(CallbackQueryHandler(callback_handler))

    app.add_handler(CommandHandler("addpoints", add_points_admin))
    app.add_handler(CommandHandler("removepoints", remove_points_admin))
    app.add_handler(CommandHandler("addlevel", add_level_admin))
    app.add_handler(CommandHandler("removelevel", remove_level_admin))
    app.add_handler(CommandHandler("ban", ban_user_admin))
    app.add_handler(CommandHandler("unban", unban_user_admin))

    print("🐾 ربات روشن شد!")
    app.run_polling()

if __name__ == "__main__":
    main()