import logging
import sqlite3
import json
import random
import os
import time
from datetime import datetime, timedelta
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import Application, CommandHandler, CallbackQueryHandler, ConversationHandler, MessageHandler, filters, ContextTypes

# ========== توکن و تنظیمات ==========
TOKEN = os.environ.get("BOT_TOKEN", "")  # توکن ربات از @BotFather
ADMIN_IDS = [int(x) for x in os.environ.get("ADMIN_IDS", "").replace(" ", "").split(",") if x.strip().isdigit()]
CHANNEL_ID = int(os.environ.get("CHANNEL_ID", "0") or 0)  # آیدی کانال برای گزارش‌ها (اختیاری)

logging.basicConfig(level=logging.INFO)

# ========== مرحله‌های ConversationHandler ==========
COUNTRY_NAME, BET_AMOUNT, CLAN_NAME, DUEL_OPPONENT = range(4)

# ========== دیتابیس ==========
DB_NAME = "game.db"

def init_db():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    
    c.execute('''
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY,
            country_name TEXT,
            gold INTEGER DEFAULT 1000,
            oil INTEGER DEFAULT 500,
            army INTEGER DEFAULT 5,
            economy INTEGER DEFAULT 10,
            tech INTEGER DEFAULT 5,
            population INTEGER DEFAULT 100,
            equipment TEXT DEFAULT '{"soldiers":5,"tanks":0,"fighters":0,"ships":0,"missiles":0,"defense":0}',
            nuke BOOLEAN DEFAULT 0,
            shelter BOOLEAN DEFAULT 0,
            alliance TEXT DEFAULT '',
            clan TEXT DEFAULT '',
            last_attack_time INTEGER DEFAULT 0,
            last_free_money INTEGER DEFAULT 0,
            last_company_time INTEGER DEFAULT 0,
            last_daily_mission INTEGER DEFAULT 0,
            last_daily_gift INTEGER DEFAULT 0,
            total_wins INTEGER DEFAULT 0,
            total_losses INTEGER DEFAULT 0,
            statement TEXT DEFAULT '',
            subsidiary TEXT DEFAULT '',
            secret_chat TEXT DEFAULT '',
            is_vip BOOLEAN DEFAULT 0,
            vip_buildings TEXT DEFAULT '{"hospital":0,"factory":0,"refinery":0,"university":0,"airport":0,"shelter_advanced":0}',
            drugs INTEGER DEFAULT 0,
            cyber_attacks INTEGER DEFAULT 0,
            is_banned BOOLEAN DEFAULT 0,
            total_bets INTEGER DEFAULT 0,
            total_bet_wins INTEGER DEFAULT 0,
            daily_streak INTEGER DEFAULT 0,
            shares TEXT DEFAULT '{}',
            duel_wins INTEGER DEFAULT 0,
            duel_losses INTEGER DEFAULT 0,
            group_points INTEGER DEFAULT 0
        )
    ''')
    
    c.execute('''
        CREATE TABLE IF NOT EXISTS clans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE,
            owner_id INTEGER,
            members TEXT DEFAULT '[]',
            gold INTEGER DEFAULT 0,
            oil INTEGER DEFAULT 0,
            level INTEGER DEFAULT 1,
            wins INTEGER DEFAULT 0,
            losses INTEGER DEFAULT 0,
            created_at INTEGER
        )
    ''')
    
    c.execute('''
        CREATE TABLE IF NOT EXISTS npc_countries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            gold INTEGER,
            oil INTEGER,
            army INTEGER,
            equipment TEXT,
            is_alive BOOLEAN DEFAULT 1,
            defense_power INTEGER DEFAULT 5,
            share_price INTEGER DEFAULT 100
        )
    ''')
    
    c.execute('''
        CREATE TABLE IF NOT EXISTS attack_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            attacker_id INTEGER,
            attacker_name TEXT,
            defender_id INTEGER,
            defender_name TEXT,
            result TEXT,
            gold_stolen INTEGER,
            oil_stolen INTEGER,
            timestamp INTEGER
        )
    ''')
    
    c.execute('''
        CREATE TABLE IF NOT EXISTS transfers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sender_id INTEGER,
            sender_name TEXT,
            receiver_id INTEGER,
            receiver_name TEXT,
            amount INTEGER,
            timestamp INTEGER
        )
    ''')
    
    c.execute('SELECT COUNT(*) FROM npc_countries')
    if c.fetchone()[0] == 0:
        npcs = [
            ("🇪🇸 اسپانیا", 5000, 2000, 10, '{"soldiers":10,"tanks":2,"fighters":1,"ships":0,"missiles":0,"defense":1}', 8, 100),
            ("🇦🇫 افغانستان", 3000, 1000, 8, '{"soldiers":8,"tanks":1,"fighters":0,"ships":0,"missiles":0,"defense":0}', 5, 80),
            ("🏛️ هخامنشیان", 8000, 3000, 15, '{"soldiers":15,"tanks":3,"fighters":2,"ships":1,"missiles":1,"defense":2}', 12, 150),
            ("🇮🇷 ایران", 10000, 5000, 20, '{"soldiers":20,"tanks":5,"fighters":3,"ships":2,"missiles":2,"defense":3}', 15, 200),
            ("🇵🇰 پاکستان", 6000, 2500, 12, '{"soldiers":12,"tanks":2,"fighters":1,"ships":1,"missiles":1,"defense":1}', 10, 120),
            ("🇰🇿 قزاقستان", 4000, 3000, 8, '{"soldiers":8,"tanks":1,"fighters":0,"ships":0,"missiles":0,"defense":0}', 6, 90),
            ("🇦🇱 آلبانیا", 2000, 800, 5, '{"soldiers":5,"tanks":0,"fighters":0,"ships":0,"missiles":0,"defense":0}', 4, 70),
            ("🇿🇼 زیمبابوه", 1500, 500, 4, '{"soldiers":4,"tanks":0,"fighters":0,"ships":0,"missiles":0,"defense":0}', 3, 60),
            ("🤝 مشترک", 7000, 2000, 14, '{"soldiers":14,"tanks":3,"fighters":2,"ships":1,"missiles":1,"defense":2}', 11, 130),
            ("⚔️ شورشی ها", 2500, 1000, 6, '{"soldiers":6,"tanks":1,"fighters":0,"ships":0,"missiles":0,"defense":0}', 5, 85)
        ]
        for npc in npcs:
            c.execute('INSERT INTO npc_countries (name, gold, oil, army, equipment, defense_power, share_price) VALUES (?, ?, ?, ?, ?, ?, ?)', npc)
    
    conn.commit()
    conn.close()

init_db()

# ========== توابع کمکی ==========
def get_user(user_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT * FROM users WHERE user_id = ?', (user_id,))
    row = c.fetchone()
    conn.close()
    if row:
        return {
            "user_id": row[0],
            "country_name": row[1],
            "gold": row[2],
            "oil": row[3],
            "army": row[4],
            "economy": row[5],
            "tech": row[6],
            "population": row[7],
            "equipment": json.loads(row[8]),
            "nuke": bool(row[9]),
            "shelter": bool(row[10]),
            "alliance": row[11],
            "clan": row[12],
            "last_attack_time": row[13],
            "last_free_money": row[14],
            "last_company_time": row[15],
            "last_daily_mission": row[16],
            "last_daily_gift": row[17],
            "total_wins": row[18],
            "total_losses": row[19],
            "statement": row[20] if len(row) > 20 else '',
            "subsidiary": row[21] if len(row) > 21 else '',
            "secret_chat": row[22] if len(row) > 22 else '',
            "is_vip": bool(row[23]) if len(row) > 23 else 0,
            "vip_buildings": json.loads(row[24]) if len(row) > 24 else {"hospital":0,"factory":0,"refinery":0,"university":0,"airport":0,"shelter_advanced":0},
            "drugs": row[25] if len(row) > 25 else 0,
            "cyber_attacks": row[26] if len(row) > 26 else 0,
            "is_banned": bool(row[27]) if len(row) > 27 else 0,
            "total_bets": row[28] if len(row) > 28 else 0,
            "total_bet_wins": row[29] if len(row) > 29 else 0,
            "daily_streak": row[30] if len(row) > 30 else 0,
            "shares": json.loads(row[31]) if len(row) > 31 else {},
            "duel_wins": row[32] if len(row) > 32 else 0,
            "duel_losses": row[33] if len(row) > 33 else 0,
            "group_points": row[34] if len(row) > 34 else 0
        }
    return None

def get_all_users():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT user_id, country_name, gold, oil, army, economy, tech, total_wins, total_losses, is_vip, clan FROM users WHERE is_banned = 0')
    rows = c.fetchall()
    conn.close()
    return [{"user_id": r[0], "name": r[1], "gold": r[2], "oil": r[3], "army": r[4], "economy": r[5], "tech": r[6], "wins": r[7], "losses": r[8], "is_vip": bool(r[9]), "clan": r[10]} for r in rows]

def create_user(user_id, country_name):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('INSERT INTO users (user_id, country_name) VALUES (?, ?)', (user_id, country_name))
    conn.commit()
    conn.close()

def update_user(user_id, **kwargs):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    for key, value in kwargs.items():
        if key in ["equipment", "vip_buildings", "shares"]:
            value = json.dumps(value)
        c.execute(f'UPDATE users SET {key} = ? WHERE user_id = ?', (value, user_id))
    conn.commit()
    conn.close()

def get_npc_countries():
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT id, name, gold, oil, army, equipment, defense_power, share_price FROM npc_countries WHERE is_alive = 1')
    rows = c.fetchall()
    conn.close()
    return [{"id": r[0], "name": r[1], "gold": r[2], "oil": r[3], "army": r[4], "equipment": json.loads(r[5]), "defense_power": r[6], "share_price": r[7]} for r in rows]

def get_npc_by_id(npc_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT id, name, gold, oil, army, equipment, defense_power, share_price FROM npc_countries WHERE id = ? AND is_alive = 1', (npc_id,))
    row = c.fetchone()
    conn.close()
    if row:
        return {"id": row[0], "name": row[1], "gold": row[2], "oil": row[3], "army": row[4], "equipment": json.loads(row[5]), "defense_power": row[6], "share_price": row[7]}
    return None

def update_npc(npc_id, **kwargs):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    for key, value in kwargs.items():
        if key == "equipment":
            value = json.dumps(value)
        c.execute(f'UPDATE npc_countries SET {key} = ? WHERE id = ?', (value, npc_id))
    conn.commit()
    conn.close()

def calculate_attack_power(equipment, army, tech, vip_buildings=None):
    power = army * 2
    power += equipment.get("soldiers", 0) * 1
    power += equipment.get("tanks", 0) * 5
    power += equipment.get("fighters", 0) * 10
    power += equipment.get("ships", 0) * 8
    power += equipment.get("missiles", 0) * 15
    power += equipment.get("defense", 0) * 3
    power += tech * 2
    if vip_buildings:
        power += vip_buildings.get("airport", 0) * 20
        power += vip_buildings.get("factory", 0) * 15
    return power

def add_attack_log(attacker_id, attacker_name, defender_id, defender_name, result, gold_stolen, oil_stolen):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('INSERT INTO attack_logs (attacker_id, attacker_name, defender_id, defender_name, result, gold_stolen, oil_stolen, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
              (attacker_id, attacker_name, defender_id, defender_name, result, gold_stolen, oil_stolen, int(time.time())))
    conn.commit()
    conn.close()

def add_transfer_log(sender_id, sender_name, receiver_id, receiver_name, amount):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('INSERT INTO transfers (sender_id, sender_name, receiver_id, receiver_name, amount, timestamp) VALUES (?, ?, ?, ?, ?, ?)',
              (sender_id, sender_name, receiver_id, receiver_name, amount, int(time.time())))
    conn.commit()
    conn.close()

async def send_to_channel(text):
    try:
        app = Application.builder().token(TOKEN).build()
        await app.bot.send_message(chat_id=CHANNEL_ID, text=text)
    except Exception as e:
        logging.error(f"خطا در ارسال به کانال: {e}")

def get_clan(clan_name):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT * FROM clans WHERE name = ?', (clan_name,))
    row = c.fetchone()
    conn.close()
    if row:
        return {
            "id": row[0],
            "name": row[1],
            "owner_id": row[2],
            "members": json.loads(row[3]),
            "gold": row[4],
            "oil": row[5],
            "level": row[6],
            "wins": row[7],
            "losses": row[8],
            "created_at": row[9]
        }
    return None

def create_clan(clan_name, owner_id):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('INSERT INTO clans (name, owner_id, members, created_at) VALUES (?, ?, ?, ?)',
              (clan_name, owner_id, json.dumps([owner_id]), int(time.time())))
    conn.commit()
    conn.close()

def update_clan(clan_name, **kwargs):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    for key, value in kwargs.items():
        if key == "members":
            value = json.dumps(value)
        c.execute(f'UPDATE clans SET {key} = ? WHERE name = ?', (value, clan_name))
    conn.commit()
    conn.close()

# ========== دکمه‌های منوی اصلی ==========
def get_main_menu(user):
    menu = [
        [InlineKeyboardButton("🏳️ کشور من", callback_data="my_country"), 
         InlineKeyboardButton("💰 انتقال طلا", callback_data="transfer_gold")],
        [InlineKeyboardButton("🛒 بازار تسلیحات", callback_data="arms_market"), 
         InlineKeyboardButton("⚔️ حمله به NPC", callback_data="military_attack")],
        [InlineKeyboardButton("⚔️ حمله به کاربر", callback_data="attack_user"), 
         InlineKeyboardButton("💣 حمله اتمی", callback_data="nuke_attack")],
        [InlineKeyboardButton("💰 پول رایگان", callback_data="free_money"), 
         InlineKeyboardButton("🏭 شرکت‌ها", callback_data="companies")],
        [InlineKeyboardButton("🛢️ نفت و انرژی", callback_data="oil_energy"), 
         InlineKeyboardButton("📦 صادرات/واردات", callback_data="trade")],
        [InlineKeyboardButton("🕵️ جاسوسی", callback_data="spy"), 
         InlineKeyboardButton("🤝 اتحاد", callback_data="alliance")],
        [InlineKeyboardButton("🏗️ پروژه‌های ملی", callback_data="national_projects"), 
         InlineKeyboardButton("🏴 بازار سیاه", callback_data="black_market")],
        [InlineKeyboardButton("🗺️ نقشه جنگی", callback_data="war_map"), 
         InlineKeyboardButton("📊 رتبه‌بندی", callback_data="rankings")],
        [InlineKeyboardButton("🎰 شرط‌بندی", callback_data="betting"), 
         InlineKeyboardButton("🏰 کلن‌ها", callback_data="clans")],
        [InlineKeyboardButton("⚔️ دوئل", callback_data="duel"), 
         InlineKeyboardButton("📈 بازار سهام", callback_data="stock_market")],
        [InlineKeyboardButton("🎯 ماموریت روزانه", callback_data="daily_mission"), 
         InlineKeyboardButton("🎁 هدیه روزانه", callback_data="daily_gift")],
        [InlineKeyboardButton("🏆 مسابقه گروهی", callback_data="group_contest"), 
         InlineKeyboardButton("👑 پنل VIP", callback_data="vip_panel")] if user.get("is_vip") else 
        [InlineKeyboardButton("🏆 مسابقه گروهی", callback_data="group_contest")],
    ]
    return InlineKeyboardMarkup(menu)

# ========== شروع /start ==========
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    if not user:
        await update.message.reply_text("🌟 به جنگ جهانی ریات خوش آمدید!\nلطفاً نام کشور خود را وارد کنید:")
        return COUNTRY_NAME
    else:
        if user.get("is_banned"):
            await update.message.reply_text("🚫 شما توسط ادمین بن شده‌اید!")
            return ConversationHandler.END
        await show_main_menu(update, context)
        return ConversationHandler.END

async def receive_country_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    country_name = update.message.text.strip()
    if not country_name:
        await update.message.reply_text("نام کشور نمی‌تواند خالی باشد. دوباره وارد کنید:")
        return COUNTRY_NAME
    create_user(user_id, country_name)
    await update.message.reply_text(f"✅ کشور {country_name} با موفقیت ثبت شد!\nشروع با ۱,۰۰۰ طلا و ۵۰۰ نفت")
    await show_main_menu(update, context)
    return ConversationHandler.END

async def cancel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("ثبت نام لغو شد. برای شروع مجدد /start را بزنید.")
    return ConversationHandler.END

# ========== نمایش منوی اصلی ==========
async def show_main_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    if not user:
        if update.callback_query:
            await update.callback_query.edit_message_text("ابتدا /start کنید")
        else:
            await update.message.reply_text("ابتدا /start کنید")
        return
    
    vip_tag = " 👑 VIP" if user.get("is_vip") else ""
    
    if update.callback_query:
        query = update.callback_query
        await query.answer()
        await query.edit_message_text(
            f"⚔️ جنگ جهانی ریات\n🏳️ {user['country_name']}{vip_tag}\n\n"
            f"💰 طلا: {user['gold']:,} | 🛢️ نفت: {user['oil']:,}\n"
            f"⚔️ ارتش: {user['army']} | 🏭 اقتصاد: {user['economy']}\n"
            f"🏰 کلن: {user['clan'] or 'ندارد'}\n\n"
            f"📋 یکی از گزینه‌ها را انتخاب کنید:",
            reply_markup=get_main_menu(user)
        )
    else:
        await update.message.reply_text(
            f"⚔️ جنگ جهانی ریات\n🏳️ {user['country_name']}{vip_tag}\n\n"
            f"💰 طلا: {user['gold']:,} | 🛢️ نفت: {user['oil']:,}\n"
            f"⚔️ ارتش: {user['army']} | 🏭 اقتصاد: {user['economy']}\n"
            f"🏰 کلن: {user['clan'] or 'ندارد'}\n\n"
            f"📋 یکی از گزینه‌ها را انتخاب کنید:",
            reply_markup=get_main_menu(user)
        )

# ========== کشور من ==========
async def my_country(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user = get_user(query.from_user.id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    equip = user["equipment"]
    buildings = user["vip_buildings"]
    shares = user.get("shares", {})
    
    text = (
        f"🏳️ {user['country_name']}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلا: {user['gold']:,}\n"
        f"🛢️ نفت: {user['oil']:,}\n"
        f"👥 جمعیت: {user['population']:,}\n"
        f"⚔️ ارتش: {user['army']}\n"
        f"🏭 اقتصاد: {user['economy']}\n"
        f"🔬 فناوری: {user['tech']}\n"
        f"🏰 کلن: {user['clan'] or 'ندارد'}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🎯 تجهیزات:\n"
        f"• 🪖 سرباز: {equip.get('soldiers', 0)}\n"
        f"• 🚜 تانک: {equip.get('tanks', 0)}\n"
        f"• ✈️ جنگنده: {equip.get('fighters', 0)}\n"
        f"• 🚢 ناو: {equip.get('ships', 0)}\n"
        f"• 🚀 موشک: {equip.get('missiles', 0)}\n"
        f"• 🛡️ پدافند: {equip.get('defense', 0)}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏆 پیروزی‌ها: {user['total_wins']}\n"
        f"💔 شکست‌ها: {user['total_losses']}\n"
        f"⚔️ دوئل‌ها: {user['duel_wins']} برد - {user['duel_losses']} باخت\n"
        f"🎰 شرط‌بندی‌ها: {user['total_bets']}\n"
        f"🎯 برد شرط‌ها: {user['total_bet_wins']}\n"
        f"🔥 استریک روزانه: {user['daily_streak']}\n"
        f"📊 امتیاز گروه: {user['group_points']}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📈 سهام:\n"
    )
    
    if shares:
        for country, amount in shares.items():
            text += f"• {country}: {amount}\n"
    else:
        text += "• هیچ سهمی ندارید\n"
    
    text += f"━━━━━━━━━━━━━━━━━━━━\n"
    text += f"🤝 اتحاد: {user['alliance'] or 'ندارد'}\n"
    text += f"☢️ سلاح هسته‌ای: {'دارد' if user['nuke'] else 'ندارد'}\n"
    text += f"🏠 پناهگاه: {'دارد' if user['shelter'] else 'ندارد'}\n"
    text += f"👑 VIP: {'بله' if user['is_vip'] else 'خیر'}\n"
    
    if user["is_vip"]:
        text += f"━━━━━━━━━━━━━━━━━━━━\n"
        text += f"🏗️ ساختمان‌های VIP:\n"
        text += f"• 🏥 بیمارستان: {buildings.get('hospital', 0)}\n"
        text += f"• 🏭 کارخانه: {buildings.get('factory', 0)}\n"
        text += f"• 🛢️ پالایشگاه: {buildings.get('refinery', 0)}\n"
        text += f"• 🎓 دانشگاه: {buildings.get('university', 0)}\n"
        text += f"• ✈️ فرودگاه: {buildings.get('airport', 0)}\n"
        text += f"• 🛡️ پناهگاه پیشرفته: {buildings.get('shelter_advanced', 0)}\n"
        text += f"• 💊 مواد مخدر: {user.get('drugs', 0)}\n"
        text += f"• 💻 حملات سایبری: {user.get('cyber_attacks', 0)}\n"
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]]
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

# ========== بازار تسلیحات ==========
async def arms_market(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user = get_user(query.from_user.id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    keyboard = [
        [InlineKeyboardButton("🪖 سرباز (۱۰۰ طلا)", callback_data="buy_soldier"),
         InlineKeyboardButton("🚜 تانک (۵۰۰ طلا)", callback_data="buy_tank")],
        [InlineKeyboardButton("✈️ جنگنده (۱,۰۰۰ طلا)", callback_data="buy_fighter"),
         InlineKeyboardButton("🚢 ناو جنگی (۲,۰۰۰ طلا)", callback_data="buy_ship")],
        [InlineKeyboardButton("🚀 موشک (۳,۰۰۰ طلا)", callback_data="buy_missile"),
         InlineKeyboardButton("🛡️ پدافند (۲,۵۰۰ طلا)", callback_data="buy_defense")],
    ]
    
    if user["is_vip"]:
        keyboard.insert(0, [InlineKeyboardButton("💎 موشک بالستیک (۱۰,۰۰۰ طلا)", callback_data="buy_ballistic"),
                           InlineKeyboardButton("💎 پهپاد (۵,۰۰۰ طلا)", callback_data="buy_drone")])
    
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"🛒 بازار تسلیحات\n💰 طلا: {user['gold']:,}\n\n📋 قیمت‌ها:\n🪖 سرباز: ۱۰۰\n🚜 تانک: ۵۰۰\n✈️ جنگنده: ۱,۰۰۰\n🚢 ناو: ۲,۰۰۰\n🚀 موشک: ۳,۰۰۰\n🛡️ پدافند: ۲,۵۰۰" + 
        ("\n💎 موشک بالستیک: ۱۰,۰۰۰\n💎 پهپاد: ۵,۰۰۰" if user["is_vip"] else ""),
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

# ========== خرید تجهیزات ==========
async def buy_equipment(update: Update, context: ContextTypes.DEFAULT_TYPE, item_name, item_key, price):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.answer("خطا!", show_alert=True)
        return
    
    if user["gold"] < price:
        await query.answer(f"طلای کافی ندارید! نیاز: {price:,}", show_alert=True)
        return
    
    equip = user["equipment"]
    equip[item_key] = equip.get(item_key, 0) + 1
    new_gold = user["gold"] - price
    
    update_user(user_id, gold=new_gold, equipment=equip)
    await query.answer(f"✅ {item_name} خریداری شد!", show_alert=True)
    await query.edit_message_text(
        f"✅ {item_name} با موفقیت خریداری شد!\n💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به بازار", callback_data="arms_market")]])
    )

# ========== حمله به NPC ==========
async def military_attack(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user = get_user(query.from_user.id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    last = user.get("last_attack_time", 0)
    now = int(time.time())
    cooldown = 300 if user["is_vip"] else 900
    if now - last < cooldown:
        remaining = cooldown - (now - last)
        minutes = remaining // 60
        await query.answer(f"⏳ {minutes} دقیقه صبر کنید!", show_alert=True)
        return
    
    npcs = get_npc_countries()
    if not npcs:
        await query.edit_message_text("❌ هیچ کشوری برای حمله وجود ندارد!")
        return
    
    keyboard = []
    row = []
    for npc in npcs:
        row.append(InlineKeyboardButton(npc["name"], callback_data=f"npc_{npc['id']}"))
        if len(row) == 2:
            keyboard.append(row)
            row = []
    if row:
        keyboard.append(row)
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"⚔️ حمله به NPC\n🎯 کشور هدف را انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

# ========== حمله به کاربر ==========
async def attack_user(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    last = user.get("last_attack_time", 0)
    now = int(time.time())
    cooldown = 300 if user["is_vip"] else 900
    if now - last < cooldown:
        remaining = cooldown - (now - last)
        minutes = remaining // 60
        await query.answer(f"⏳ {minutes} دقیقه صبر کنید!", show_alert=True)
        return
    
    all_users = get_all_users()
    other_users = [u for u in all_users if u["user_id"] != user_id]
    
    if not other_users:
        await query.edit_message_text("❌ هیچ کاربر دیگری وجود ندارد!")
        return
    
    keyboard = []
    row = []
    for u in other_users[:20]:
        row.append(InlineKeyboardButton(f"🏳️ {u['name']}", callback_data=f"attack_user_{u['user_id']}"))
        if len(row) == 2:
            keyboard.append(row)
            row = []
    if row:
        keyboard.append(row)
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"⚔️ حمله به کاربر\n🎯 کاربر هدف را انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

# ========== انتخاب درصد حمله ==========
async def select_attack_percent(update: Update, context: ContextTypes.DEFAULT_TYPE, npc_id):
    query = update.callback_query
    await query.answer()
    context.user_data['npc_id'] = npc_id
    
    keyboard = [
        [InlineKeyboardButton("۱۰%", callback_data="attack_10"),
         InlineKeyboardButton("۲۰%", callback_data="attack_20")],
        [InlineKeyboardButton("۳۰%", callback_data="attack_30"),
         InlineKeyboardButton("۴۰%", callback_data="attack_40")],
        [InlineKeyboardButton("۵۰%", callback_data="attack_50"),
         InlineKeyboardButton("۶۰%", callback_data="attack_60")],
        [InlineKeyboardButton("۷۰%", callback_data="attack_70"),
         InlineKeyboardButton("۸۰%", callback_data="attack_80")],
        [InlineKeyboardButton("۹۰%", callback_data="attack_90"),
         InlineKeyboardButton("❌ انصراف", callback_data="menu")]
    ]
    await query.edit_message_text(
        f"🎯 چند درصد از نیروهای خود را استفاده می‌کنید؟",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

# ========== اجرای حمله به NPC ==========
async def execute_attack(update: Update, context: ContextTypes.DEFAULT_TYPE, percent):
    query = update.callback_query
    await query.answer()
    
    user_id = query.from_user.id
    user = get_user(user_id)
    npc_id = context.user_data.get('npc_id')
    npc = get_npc_by_id(npc_id)
    
    if not user or not npc:
        await query.edit_message_text("❌ خطا در دریافت اطلاعات")
        return
    
    percent_float = int(percent) / 100
    attack_power = calculate_attack_power(user["equipment"], user["army"], user["tech"], user.get("vip_buildings")) * percent_float
    defense_power = calculate_attack_power(npc["equipment"], npc["army"], npc["defense_power"])
    
    win_chance = attack_power / (attack_power + defense_power) * 100
    is_win = random.random() * 100 < win_chance
    
    if is_win:
        gold_stolen = int(npc["gold"] * (0.3 + random.random() * 0.3))
        oil_stolen = int(npc["oil"] * (0.3 + random.random() * 0.3))
        
        new_gold = user["gold"] + gold_stolen
        new_oil = user["oil"] + oil_stolen
        new_wins = user["total_wins"] + 1
        
        equip = user["equipment"]
        for key in equip:
            if equip[key] > 0 and random.random() < 0.15:
                equip[key] = max(0, equip[key] - 1)
        
        update_user(user_id, gold=new_gold, oil=new_oil, total_wins=new_wins, equipment=equip, last_attack_time=int(time.time()))
        
        npc_equip = npc["equipment"]
        for key in npc_equip:
            if npc_equip[key] > 0 and random.random() < 0.3:
                npc_equip[key] = max(0, npc_equip[key] - 1)
        
        new_npc_gold = npc["gold"] - gold_stolen
        new_npc_oil = npc["oil"] - oil_stolen
        update_npc(npc_id, gold=max(0, new_npc_gold), oil=max(0, new_npc_oil), equipment=npc_equip)
        
        add_attack_log(user_id, user["country_name"], npc_id, npc["name"], "پیروزی", gold_stolen, oil_stolen)
        
        result_text = (
            f"🎉 پیروزی در حمله!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"⚔️ قدرت شما: {int(attack_power)} | حریف: {int(defense_power)}\n"
            f"🎯 شانس پیروزی: {int(win_chance)}%\n"
            f"💰 غنیمت طلا: {gold_stolen:,}\n"
            f"🛢️ غنیمت نفت: {oil_stolen:,}\n"
            f"📉 تلفات شما: حداقل\n"
            f"📈 تلفات حریف: سنگین"
        )
        
        channel_text = (
            f"⚔️ گزارش حمله\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🗡️ مهاجم: {user['country_name']}\n"
            f"🛡️ مدافع: {npc['name']}\n"
            f"✅ نتیجه: پیروزی مهاجم\n"
            f"💰 غنیمت طلا: {gold_stolen:,}\n"
            f"🛢️ غنیمت نفت: {oil_stolen:,}"
        )
        await send_to_channel(channel_text)
        
    else:
        gold_lost = int(user["gold"] * (0.05 + random.random() * 0.15))
        oil_lost = int(user["oil"] * (0.05 + random.random() * 0.15))
        new_losses = user["total_losses"] + 1
        
        equip = user["equipment"]
        for key in equip:
            if equip[key] > 0 and random.random() < 0.25:
                equip[key] = max(0, equip[key] - 1)
        
        update_user(user_id, gold=max(0, user["gold"] - gold_lost), oil=max(0, user["oil"] - oil_lost), 
                   total_losses=new_losses, equipment=equip, last_attack_time=int(time.time()))
        
        add_attack_log(user_id, user["country_name"], npc_id, npc["name"], "شکست", 0, 0)
        
        result_text = (
            f"💔 شکست در حمله!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"⚔️ قدرت شما: {int(attack_power)} | حریف: {int(defense_power)}\n"
            f"🎯 شانس پیروزی: {int(win_chance)}%\n"
            f"💰 طلای از دست رفته: {gold_lost:,}\n"
            f"🛢️ نفت از دست رفته: {oil_lost:,}\n"
            f"📉 تلفات شما: سنگین\n"
            f"📈 تلفات حریف: حداقل"
        )
        
        channel_text = (
            f"⚔️ گزارش حمله\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🗡️ مهاجم: {user['country_name']}\n"
            f"🛡️ مدافع: {npc['name']}\n"
            f"❌ نتیجه: شکست مهاجم\n"
            f"💰 طلای از دست رفته: {gold_lost:,}\n"
            f"🛢️ نفت از دست رفته: {oil_lost:,}"
        )
        await send_to_channel(channel_text)
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت به منو", callback_data="menu")]]
    await query.edit_message_text(result_text, reply_markup=InlineKeyboardMarkup(keyboard))

# ========== اجرای حمله به کاربر ==========
async def execute_attack_user(update: Update, context: ContextTypes.DEFAULT_TYPE, target_user_id):
    query = update.callback_query
    await query.answer()
    
    user_id = query.from_user.id
    user = get_user(user_id)
    target = get_user(target_user_id)
    
    if not user or not target:
        await query.edit_message_text("❌ خطا در دریافت اطلاعات")
        return
    
    user_power = calculate_attack_power(user["equipment"], user["army"], user["tech"], user.get("vip_buildings"))
    target_power = calculate_attack_power(target["equipment"], target["army"], target["tech"], target.get("vip_buildings"))
    
    win_chance = user_power / (user_power + target_power) * 100
    is_win = random.random() * 100 < win_chance
    
    if is_win:
        gold_stolen = int(target["gold"] * (0.1 + random.random() * 0.2))
        oil_stolen = int(target["oil"] * (0.1 + random.random() * 0.2))
        
        new_user_gold = user["gold"] + gold_stolen
        new_target_gold = target["gold"] - gold_stolen
        new_user_oil = user["oil"] + oil_stolen
        new_target_oil = target["oil"] - oil_stolen
        
        update_user(user_id, gold=new_user_gold, oil=new_user_oil, total_wins=user["total_wins"] + 1, last_attack_time=int(time.time()))
        update_user(target_user_id, gold=max(0, new_target_gold), oil=max(0, new_target_oil), total_losses=target["total_losses"] + 1)
        
        add_attack_log(user_id, user["country_name"], target_user_id, target["country_name"], "پیروزی (کاربر)", gold_stolen, oil_stolen)
        
        channel_text = (
            f"⚔️ گزارش حمله کاربری\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🗡️ مهاجم: {user['country_name']}\n"
            f"🛡️ مدافع: {target['country_name']}\n"
            f"✅ نتیجه: پیروزی مهاجم\n"
            f"💰 غنیمت طلا: {gold_stolen:,}\n"
            f"🛢️ غنیمت نفت: {oil_stolen:,}"
        )
        await send_to_channel(channel_text)
        
        result_text = (
            f"🎉 پیروزی در حمله به {target['country_name']}!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"⚔️ قدرت شما: {int(user_power)} | حریف: {int(target_power)}\n"
            f"🎯 شانس پیروزی: {int(win_chance)}%\n"
            f"💰 غنیمت طلا: {gold_stolen:,}\n"
            f"🛢️ غنیمت نفت: {oil_stolen:,}"
        )
    else:
        gold_lost = int(user["gold"] * (0.05 + random.random() * 0.15))
        oil_lost = int(user["oil"] * (0.05 + random.random() * 0.15))
        
        update_user(user_id, gold=max(0, user["gold"] - gold_lost), oil=max(0, user["oil"] - oil_lost), 
                   total_losses=user["total_losses"] + 1, last_attack_time=int(time.time()))
        
        add_attack_log(user_id, user["country_name"], target_user_id, target["country_name"], "شکست (کاربر)", 0, 0)
        
        channel_text = (
            f"⚔️ گزارش حمله کاربری\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🗡️ مهاجم: {user['country_name']}\n"
            f"🛡️ مدافع: {target['country_name']}\n"
            f"❌ نتیجه: شکست مهاجم\n"
            f"💰 طلای از دست رفته: {gold_lost:,}\n"
            f"🛢️ نفت از دست رفته: {oil_lost:,}"
        )
        await send_to_channel(channel_text)
        
        result_text = (
            f"💔 شکست در حمله به {target['country_name']}!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"⚔️ قدرت شما: {int(user_power)} | حریف: {int(target_power)}\n"
            f"🎯 شانس پیروزی: {int(win_chance)}%\n"
            f"💰 طلای از دست رفته: {gold_lost:,}\n"
            f"🛢️ نفت از دست رفته: {oil_lost:,}"
        )
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت به منو", callback_data="menu")]]
    await query.edit_message_text(result_text, reply_markup=InlineKeyboardMarkup(keyboard))

# ========== انتقال طلا ==========
async def transfer_gold(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    all_users = get_all_users()
    other_users = [u for u in all_users if u["user_id"] != user_id]
    
    if not other_users:
        await query.edit_message_text("❌ هیچ کاربر دیگری وجود ندارد!")
        return
    
    keyboard = []
    row = []
    for u in other_users[:20]:
        row.append(InlineKeyboardButton(f"🏳️ {u['name']}", callback_data=f"transfer_to_{u['user_id']}"))
        if len(row) == 2:
            keyboard.append(row)
            row = []
    if row:
        keyboard.append(row)
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"💰 انتقال طلا\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلای شما: {user['gold']:,}\n"
        f"💵 قیمت هر ۱۰۰,۰۰۰ طلا: ۱۰۰,۰۰۰ تومان\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🎯 کاربر مقصد را انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def select_transfer_amount(update: Update, context: ContextTypes.DEFAULT_TYPE, target_user_id):
    query = update.callback_query
    await query.answer()
    context.user_data['transfer_target'] = target_user_id
    
    target = get_user(target_user_id)
    if not target:
        await query.edit_message_text("❌ کاربر یافت نشد!")
        return
    
    await query.edit_message_text(
        f"💰 انتقال طلا به {target['country_name']}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلای شما: {get_user(query.from_user.id)['gold']:,}\n"
        f"💵 قیمت هر ۱۰۰,۰۰۰ طلا: ۱۰۰,۰۰۰ تومان\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📝 مقدار طلای مورد نظر را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="transfer_gold")]])
    )
    context.user_data['waiting_for'] = 'transfer_amount'

async def receive_transfer_amount(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    target_id = context.user_data.get('transfer_target')
    target = get_user(target_id) if target_id else None
    
    try:
        amount = int(update.message.text)
    except:
        await update.message.reply_text("❌ عدد معتبر وارد کنید!")
        return
    
    if amount < 1000:
        await update.message.reply_text("❌ حداقل انتقال ۱,۰۰۰ طلا است!")
        return
    
    if amount > user["gold"]:
        await update.message.reply_text(f"❌ طلای کافی ندارید! (موجودی: {user['gold']:,})")
        return
    
    if not target:
        await update.message.reply_text("❌ کاربر مقصد یافت نشد!")
        return
    
    price_in_toman = (amount / 100000) * 100000
    
    keyboard = [
        [InlineKeyboardButton("✅ تایید انتقال", callback_data=f"confirm_transfer_{amount}")],
        [InlineKeyboardButton("❌ انصراف", callback_data="transfer_gold")]
    ]
    
    await update.message.reply_text(
        f"💰 تایید انتقال طلا\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📍 از: {user['country_name']}\n"
        f"📍 به: {target['country_name']}\n"
        f"💰 مقدار: {amount:,} طلا\n"
        f"💵 معادل: {price_in_toman:,.0f} تومان\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"آیا مطمئن هستید؟",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )
    context.user_data['waiting_for'] = None
    context.user_data['transfer_amount'] = amount

async def confirm_transfer(update: Update, context: ContextTypes.DEFAULT_TYPE, amount):
    query = update.callback_query
    await query.answer()
    
    user_id = query.from_user.id
    user = get_user(user_id)
    target_id = context.user_data.get('transfer_target')
    target = get_user(target_id) if target_id else None
    
    if not user or not target:
        await query.edit_message_text("❌ خطا در دریافت اطلاعات!")
        return
    
    new_sender_gold = user["gold"] - amount
    new_receiver_gold = target["gold"] + amount
    
    update_user(user_id, gold=new_sender_gold)
    update_user(target_id, gold=new_receiver_gold)
    
    add_transfer_log(user_id, user["country_name"], target_id, target["country_name"], amount)
    
    await query.edit_message_text(
        f"✅ انتقال طلا موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📍 از: {user['country_name']}\n"
        f"📍 به: {target['country_name']}\n"
        f"💰 مقدار: {amount:,} طلا\n"
        f"💵 معادل: {(amount / 100000 * 100000):,.0f} تومان\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 موجودی شما: {new_sender_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    
    channel_text = (
        f"💰 انتقال طلا\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📍 از: {user['country_name']}\n"
        f"📍 به: {target['country_name']}\n"
        f"💰 مقدار: {amount:,} طلا\n"
        f"💵 معادل: {(amount / 100000 * 100000):,.0f} تومان"
    )
    await send_to_channel(channel_text)

# ========== حمله اتمی ==========
async def nuke_attack(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user = get_user(query.from_user.id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    if not user["nuke"]:
        await query.edit_message_text(
            "☢️ شما سلاح هسته‌ای ندارید!\n💡 برای ساخت آن به پروژه‌های ملی بروید.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
        )
        return
    
    npcs = get_npc_countries()
    if not npcs:
        await query.edit_message_text("❌ هیچ کشوری برای حمله وجود ندارد!")
        return
    
    keyboard = []
    row = []
    for npc in npcs:
        row.append(InlineKeyboardButton(npc["name"], callback_data=f"nuke_{npc['id']}"))
        if len(row) == 2:
            keyboard.append(row)
            row = []
    if row:
        keyboard.append(row)
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"☢️ حمله اتمی\n🎯 کشور هدف را انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def execute_nuke(update: Update, context: ContextTypes.DEFAULT_TYPE, npc_id):
    query = update.callback_query
    await query.answer()
    
    user_id = query.from_user.id
    user = get_user(user_id)
    npc = get_npc_by_id(npc_id)
    
    if not user or not npc:
        await query.edit_message_text("❌ خطا!")
        return
    
    gold_stolen = int(npc["gold"] * 0.8)
    oil_stolen = int(npc["oil"] * 0.8)
    
    new_gold = user["gold"] + gold_stolen
    new_oil = user["oil"] + oil_stolen
    
    update_user(user_id, gold=new_gold, oil=new_oil, nuke=False, last_attack_time=int(time.time()))
    update_npc(npc_id, is_alive=0, gold=0, oil=0)
    
    add_attack_log(user_id, user["country_name"], npc_id, npc["name"], "پیروزی (اتمی)", gold_stolen, oil_stolen)
    
    channel_text = (
        f"☢️ حمله اتمی!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🗡️ مهاجم: {user['country_name']}\n"
        f"🛡️ مدافع: {npc['name']}\n"
        f"💥 کشور {npc['name']} نابود شد!\n"
        f"💰 غنیمت طلا: {gold_stolen:,}\n"
        f"🛢️ غنیمت نفت: {oil_stolen:,}"
    )
    await send_to_channel(channel_text)
    
    await query.edit_message_text(
        f"☢️ حمله اتمی موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💥 کشور {npc['name']} نابود شد!\n"
        f"💰 غنیمت طلا: {gold_stolen:,}\n"
        f"🛢️ غنیمت نفت: {oil_stolen:,}\n"
        f"⚠️ سلاح هسته‌ای شما مصرف شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به منو", callback_data="menu")]])
    )

# ========== پول رایگان ==========
async def free_money(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    cooldown = 21600 if user["is_vip"] else 43200
    last = user.get("last_free_money", 0)
    now = int(time.time())
    
    if now - last < cooldown:
        remaining = cooldown - (now - last)
        hours = remaining // 3600
        minutes = (remaining % 3600) // 60
        await query.answer(f"⏳ {hours} ساعت و {minutes} دقیقه دیگر", show_alert=True)
        return
    
    bonus_gold = 1000 if user["is_vip"] else 500
    bonus_oil = 400 if user["is_vip"] else 200
    
    new_gold = user["gold"] + bonus_gold
    new_oil = user["oil"] + bonus_oil
    update_user(user_id, gold=new_gold, oil=new_oil, last_free_money=now)
    await query.answer("💰 پول رایگان دریافت شد!", show_alert=True)
    await query.edit_message_text(
        f"✅ پول رایگان دریافت شد!\n💰 +{bonus_gold:,} طلا\n🛢️ +{bonus_oil} نفت",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== شرکت‌ها ==========
async def companies(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    now = int(time.time())
    last = user.get("last_company_time", 0)
    cooldown = 1800 if user["is_vip"] else 3600
    
    if now - last >= cooldown:
        base_income = user["economy"] * 10
        if user["is_vip"]:
            buildings = user["vip_buildings"]
            base_income += buildings.get("hospital", 0) * 50
            base_income += buildings.get("factory", 0) * 100
            base_income += buildings.get("refinery", 0) * 75
        
        income = base_income
        new_gold = user["gold"] + income
        update_user(user_id, gold=new_gold, last_company_time=now)
        await query.edit_message_text(
            f"🏭 شرکت‌ها\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"💰 درآمد: {income:,} طلا\n"
            f"📊 اقتصاد: {user['economy']}\n"
            f"✅ درآمد دریافت شد!",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
        )
    else:
        remaining = cooldown - (now - last)
        minutes = remaining // 60
        await query.edit_message_text(
            f"🏭 شرکت‌ها\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"⏳ {minutes} دقیقه تا دریافت بعدی\n"
            f"💰 درآمد هر {cooldown//60} دقیقه: {user['economy'] * 10:,} طلا",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
        )

# ========== نفت و انرژی ==========
async def oil_energy(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    keyboard = [
        [InlineKeyboardButton("💰 فروش ۵۰ نفت (۱۰۰ طلا)", callback_data="sell_oil_50"),
         InlineKeyboardButton("💰 فروش ۱۰۰ نفت (۲۰۰ طلا)", callback_data="sell_oil_100")],
    ]
    
    if user["is_vip"]:
        keyboard.append([InlineKeyboardButton("💰 فروش ۵۰۰ نفت (۱,۰۰۰ طلا)", callback_data="sell_oil_500"),
                        InlineKeyboardButton("💰 فروش ۱,۰۰۰ نفت (۲,۰۰۰ طلا)", callback_data="sell_oil_1000")])
    
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"🛢️ نفت و انرژی\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🛢️ نفت فعلی: {user['oil']}\n"
        f"💰 قیمت هر نفت: ۲ طلا\n\n"
        f"📋 فروش نفت:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def sell_oil(update: Update, context: ContextTypes.DEFAULT_TYPE, amount):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["oil"] < amount:
        await query.answer(f"نفت کافی ندارید! ({amount})", show_alert=True)
        return
    
    gold_earned = amount * 2
    new_gold = user["gold"] + gold_earned
    new_oil = user["oil"] - amount
    update_user(user_id, gold=new_gold, oil=new_oil)
    
    await query.edit_message_text(
        f"✅ فروش نفت موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🛢️ فروخته شد: {amount} نفت\n"
        f"💰 دریافت: {gold_earned:,} طلا",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="oil_energy")]])
    )

# ========== صادرات/واردات ==========
async def trade(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    keyboard = [
        [InlineKeyboardButton("💰 خرید ۵۰ نفت (۱۰۰ طلا)", callback_data="buy_oil_50"),
         InlineKeyboardButton("💰 خرید ۱۰۰ نفت (۲۰۰ طلا)", callback_data="buy_oil_100")],
    ]
    
    if user["is_vip"]:
        keyboard.append([InlineKeyboardButton("💰 خرید ۵۰۰ نفت (۹۰۰ طلا)", callback_data="buy_oil_500"),
                        InlineKeyboardButton("💰 خرید ۱,۰۰۰ نفت (۱,۷۰۰ طلا)", callback_data="buy_oil_1000")])
    
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"📦 صادرات/واردات\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلا: {user['gold']:,}\n"
        f"🛢️ نفت: {user['oil']}\n\n"
        f"📋 خرید نفت:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def buy_oil(update: Update, context: ContextTypes.DEFAULT_TYPE, amount, price):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["gold"] < price:
        await query.answer(f"طلای کافی ندارید! نیاز: {price}", show_alert=True)
        return
    
    new_gold = user["gold"] - price
    new_oil = user["oil"] + amount
    update_user(user_id, gold=new_gold, oil=new_oil)
    
    await query.edit_message_text(
        f"✅ خرید نفت موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🛢️ خرید: {amount} نفت\n"
        f"💰 پرداخت: {price:,} طلا",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="trade")]])
    )

# ========== جاسوسی ==========
async def spy(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    npcs = get_npc_countries()
    if not npcs:
        await query.edit_message_text("هیچ کشوری وجود ندارد!")
        return
    
    count = 5 if user["is_vip"] else 3
    selected = random.sample(npcs, min(count, len(npcs)))
    text = "🕵️ گزارش جاسوسی\n━━━━━━━━━━━━━━━━━━━━\n\n"
    for npc in selected:
        text += f"🏳️ {npc['name']}\n"
        text += f"💰 طلا: {npc['gold']:,}\n"
        text += f"🛢️ نفت: {npc['oil']:,}\n"
        text += f"⚔️ ارتش: {npc['army']}\n"
        text += f"📈 سهام: {npc['share_price']}\n"
        text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== اتحاد ==========
async def alliance(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["alliance"]:
        await query.edit_message_text(
            f"🤝 اتحاد فعلی: {user['alliance']}\n\n"
            f"برای لغو اتحاد روی دکمه زیر کلیک کنید:",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("❌ لغو اتحاد", callback_data="cancel_alliance")],
                [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
            ])
        )
        return
    
    npcs = get_npc_countries()
    keyboard = []
    row = []
    for npc in npcs[:6]:
        row.append(InlineKeyboardButton(npc["name"], callback_data=f"ally_{npc['id']}"))
        if len(row) == 2:
            keyboard.append(row)
            row = []
    if row:
        keyboard.append(row)
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"🤝 اتحاد\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 کشورهای قابل اتحاد:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def create_alliance(update: Update, context: ContextTypes.DEFAULT_TYPE, npc_id):
    query = update.callback_query
    user_id = query.from_user.id
    npc = get_npc_by_id(npc_id)
    
    if not npc:
        await query.edit_message_text("❌ کشور یافت نشد!")
        return
    
    update_user(user_id, alliance=npc["name"])
    await query.edit_message_text(
        f"🤝 اتحاد با {npc['name']} برقرار شد!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"✅ حالا می‌توانید از نیروهای متحد استفاده کنید.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

async def cancel_alliance(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    update_user(user_id, alliance="")
    await query.edit_message_text(
        "❌ اتحاد لغو شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== پروژه‌های ملی ==========
async def national_projects(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    keyboard = [
        [InlineKeyboardButton("🏭 توسعه اقتصاد (۵۰۰ طلا)", callback_data="project_economy"),
         InlineKeyboardButton("🔬 توسعه فناوری (۵۰۰ طلا)", callback_data="project_tech")],
        [InlineKeyboardButton("👥 افزایش جمعیت (۳۰۰ طلا)", callback_data="project_population"),
         InlineKeyboardButton("☢️ سلاح هسته‌ای (۲,۰۰۰ طلا)", callback_data="project_nuke")],
        [InlineKeyboardButton("🏠 پناهگاه اتمی (۱,۰۰۰ طلا)", callback_data="project_shelter"),
         InlineKeyboardButton("⚔️ توسعه ارتش (۴۰۰ طلا)", callback_data="project_army")],
    ]
    
    if user["is_vip"]:
        keyboard.append([InlineKeyboardButton("👑 خرید VIP (۲۰,۰۰۰,۰۰۰ طلا)", callback_data="buy_vip")])
    
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"🏗️ پروژه‌های ملی\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلا: {user['gold']:,}\n"
        f"📊 اقتصاد: {user['economy']}\n"
        f"🔬 فناوری: {user['tech']}\n"
        f"👥 جمعیت: {user['population']:,}\n"
        f"☢️ سلاح هسته‌ای: {'دارد' if user['nuke'] else 'ندارد'}\n"
        f"🏠 پناهگاه: {'دارد' if user['shelter'] else 'ندارد'}\n"
        f"👑 VIP: {'بله' if user['is_vip'] else 'خیر'}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 پروژه‌های قابل اجرا:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def execute_project(update: Update, context: ContextTypes.DEFAULT_TYPE, project, cost, effect):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["gold"] < cost:
        await query.answer(f"طلای کافی ندارید! نیاز: {cost:,}", show_alert=True)
        return
    
    new_gold = user["gold"] - cost
    updates = {"gold": new_gold}
    
    if project == "economy":
        updates["economy"] = user["economy"] + 5
        msg = f"🏭 اقتصاد به {user['economy'] + 5} افزایش یافت!"
    elif project == "tech":
        updates["tech"] = user["tech"] + 5
        msg = f"🔬 فناوری به {user['tech'] + 5} افزایش یافت!"
    elif project == "population":
        updates["population"] = user["population"] + 100
        msg = f"👥 جمعیت به {user['population'] + 100} افزایش یافت!"
    elif project == "nuke":
        updates["nuke"] = True
        msg = "☢️ سلاح هسته‌ای ساخته شد!"
    elif project == "shelter":
        updates["shelter"] = True
        msg = "🏠 پناهگاه اتمی ساخته شد!"
    elif project == "army":
        updates["army"] = user["army"] + 3
        msg = f"⚔️ ارتش به {user['army'] + 3} افزایش یافت!"
    
    update_user(user_id, **updates)
    await query.answer("✅ پروژه تکمیل شد!", show_alert=True)
    await query.edit_message_text(
        f"✅ {msg}\n"
        f"💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پروژه‌ها", callback_data="national_projects")]])
    )

# ========== خرید VIP ==========
async def buy_vip(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["is_vip"]:
        await query.answer("شما قبلاً VIP هستید!", show_alert=True)
        return
    
    if user["gold"] < 20000000:
        await query.answer(f"طلای کافی ندارید! نیاز: ۲۰,۰۰۰,۰۰۰", show_alert=True)
        return
    
    new_gold = user["gold"] - 20000000
    update_user(user_id, gold=new_gold, is_vip=True)
    await query.answer("👑 شما VIP شدید!", show_alert=True)
    await query.edit_message_text(
        f"👑 تبریک! شما VIP شدید!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"✅ امکانات VIP:\n"
        f"• 🏥 ساخت بیمارستان\n"
        f"• 🏭 ساخت کارخانه اسلحه‌سازی\n"
        f"• 🛢️ ساخت پالایشگاه نفت\n"
        f"• 🎓 ساخت دانشگاه\n"
        f"• ✈️ ساخت فرودگاه\n"
        f"• 🛡️ ساخت پناهگاه پیشرفته\n"
        f"• 💊 خرید و فروش مواد مخدر\n"
        f"• 💻 حمله سایبری\n"
        f"• ⏳ زمان حمله کمتر (۵ دقیقه)\n"
        f"• 💰 پول رایگان بیشتر\n"
        f"• 🕵️ جاسوسی پیشرفته\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== پنل VIP ==========
async def vip_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if not user["is_vip"]:
        await query.edit_message_text("❌ شما VIP نیستید!")
        return
    
    keyboard = [
        [InlineKeyboardButton("🏥 ساخت بیمارستان (۵,۰۰۰ طلا)", callback_data="vip_hospital"),
         InlineKeyboardButton("🏭 ساخت کارخانه (۱۰,۰۰۰ طلا)", callback_data="vip_factory")],
        [InlineKeyboardButton("🛢️ ساخت پالایشگاه (۷,۵۰۰ طلا)", callback_data="vip_refinery"),
         InlineKeyboardButton("🎓 ساخت دانشگاه (۵,۰۰۰ طلا)", callback_data="vip_university")],
        [InlineKeyboardButton("✈️ ساخت فرودگاه (۱۵,۰۰۰ طلا)", callback_data="vip_airport"),
         InlineKeyboardButton("🛡️ پناهگاه پیشرفته (۱۲,۰۰۰ طلا)", callback_data="vip_shelter_adv")],
        [InlineKeyboardButton("💊 خرید مواد مخدر (۲,۰۰۰ طلا)", callback_data="vip_buy_drugs"),
         InlineKeyboardButton("💰 فروش مواد مخدر (۴,۰۰۰ طلا)", callback_data="vip_sell_drugs")],
        [InlineKeyboardButton("💻 حمله سایبری (۵,۰۰۰ طلا)", callback_data="vip_cyber_attack")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    buildings = user["vip_buildings"]
    await query.edit_message_text(
        f"👑 پنل VIP\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلا: {user['gold']:,}\n"
        f"💊 مواد مخدر: {user.get('drugs', 0)}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏗️ ساختمان‌ها:\n"
        f"• 🏥 بیمارستان: {buildings.get('hospital', 0)}\n"
        f"• 🏭 کارخانه: {buildings.get('factory', 0)}\n"
        f"• 🛢️ پالایشگاه: {buildings.get('refinery', 0)}\n"
        f"• 🎓 دانشگاه: {buildings.get('university', 0)}\n"
        f"• ✈️ فرودگاه: {buildings.get('airport', 0)}\n"
        f"• 🛡️ پناهگاه پیشرفته: {buildings.get('shelter_advanced', 0)}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

# ========== ساخت ساختمان VIP ==========
async def build_vip_building(update: Update, context: ContextTypes.DEFAULT_TYPE, building, cost, effect_desc):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if not user["is_vip"]:
        await query.answer("شما VIP نیستید!", show_alert=True)
        return
    
    if user["gold"] < cost:
        await query.answer(f"طلای کافی ندارید! نیاز: {cost:,}", show_alert=True)
        return
    
    buildings = user["vip_buildings"]
    buildings[building] = buildings.get(building, 0) + 1
    new_gold = user["gold"] - cost
    
    if building == "hospital":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, population=user["population"] + 500)
    elif building == "factory":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, army=user["army"] + 10)
    elif building == "refinery":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, oil=user["oil"] + 200)
    elif building == "university":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, tech=user["tech"] + 10)
    elif building == "airport":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, economy=user["economy"] + 15)
    elif building == "shelter_advanced":
        update_user(user_id, gold=new_gold, vip_buildings=buildings, shelter=True)
    else:
        update_user(user_id, gold=new_gold, vip_buildings=buildings)
    
    await query.answer(f"✅ {effect_desc} ساخته شد!", show_alert=True)
    await query.edit_message_text(
        f"✅ {effect_desc} با موفقیت ساخته شد!\n"
        f"💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پنل VIP", callback_data="vip_panel")]])
    )

# ========== خرید و فروش مواد مخدر ==========
async def drugs_trade(update: Update, context: ContextTypes.DEFAULT_TYPE, action):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if not user["is_vip"]:
        await query.answer("شما VIP نیستید!", show_alert=True)
        return
    
    if action == "buy":
        if user["gold"] < 2000:
            await query.answer("طلای کافی ندارید! نیاز: ۲,۰۰۰", show_alert=True)
            return
        new_gold = user["gold"] - 2000
        new_drugs = user.get("drugs", 0) + 10
        update_user(user_id, gold=new_gold, drugs=new_drugs)
        await query.edit_message_text(
            f"💊 خرید مواد مخدر موفق!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"✅ ۱۰ واحد مواد مخدر خریداری شد\n"
            f"💰 پرداخت: ۲,۰۰۰ طلا\n"
            f"💊 موجودی: {new_drugs}",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پنل VIP", callback_data="vip_panel")]])
        )
    
    elif action == "sell":
        drugs = user.get("drugs", 0)
        if drugs < 10:
            await query.answer("مواد مخدر کافی ندارید! (حداقل ۱۰)", show_alert=True)
            return
        if random.random() < 0.2:
            update_user(user_id, drugs=0)
            await query.edit_message_text(
                f"💀 دستگیر شدید!\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
                f"❌ پلیس شما را دستگیر کرد!\n"
                f"💊 تمام مواد مخدر شما ضبط شد!",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پنل VIP", callback_data="vip_panel")]])
            )
            return
        
        gold_earned = 4000
        new_gold = user["gold"] + gold_earned
        new_drugs = drugs - 10
        update_user(user_id, gold=new_gold, drugs=new_drugs)
        await query.edit_message_text(
            f"💰 فروش مواد مخدر موفق!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"✅ ۱۰ واحد مواد مخدر فروخته شد\n"
            f"💰 دریافت: ۴,۰۰۰ طلا\n"
            f"💊 موجودی: {new_drugs}",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پنل VIP", callback_data="vip_panel")]])
        )

# ========== حمله سایبری ==========
async def cyber_attack(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if not user["is_vip"]:
        await query.answer("شما VIP نیستید!", show_alert=True)
        return
    
    if user["gold"] < 5000:
        await query.answer("طلای کافی ندارید! نیاز: ۵,۰۰۰", show_alert=True)
        return
    
    npcs = get_npc_countries()
    if not npcs:
        await query.edit_message_text("هیچ کشوری برای حمله وجود ندارد!")
        return
    
    target = random.choice(npcs)
    stolen = int(target["gold"] * random.uniform(0.1, 0.3))
    
    new_gold = user["gold"] + stolen - 5000
    new_npc_gold = target["gold"] - stolen
    update_npc(target["id"], gold=max(0, new_npc_gold))
    update_user(user_id, gold=new_gold)
    
    await query.edit_message_text(
        f"💻 حمله سایبری موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🎯 هدف: {target['name']}\n"
        f"💰 طلای دزدیده شده: {stolen:,}\n"
        f"💸 هزینه حمله: ۵,۰۰۰ طلا\n"
        f"💰 سود خالص: {stolen - 5000:,} طلا",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به پنل VIP", callback_data="vip_panel")]])
    )

# ========== بازار سیاه ==========
async def black_market(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    rand_price = random.randint(50, 200)
    keyboard = [
        [InlineKeyboardButton(f"💰 خرید اسلحه قاچاق ({rand_price} طلا)", callback_data=f"black_weapon_{rand_price}"),
         InlineKeyboardButton(f"🛢️ خرید نفت قاچاق ({rand_price//2} طلا)", callback_data=f"black_oil_{rand_price//2}")],
    ]
    
    if user["is_vip"]:
        keyboard.append([InlineKeyboardButton("💎 خرید موشک قاچاق (۵,۰۰۰ طلا)", callback_data="black_missile"),
                        InlineKeyboardButton("💎 خرید پهپاد قاچاق (۳,۰۰۰ طلا)", callback_data="black_drone")])
    
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    await query.edit_message_text(
        f"🏴 بازار سیاه\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"⚠️ کالاهای قاچاق!\n"
        f"💰 طلا: {user['gold']:,}\n\n"
        f"📋 پیشنهادات امروز:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def black_market_buy(update: Update, context: ContextTypes.DEFAULT_TYPE, item, price):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    
    if user["gold"] < price:
        await query.answer(f"طلای کافی ندارید! نیاز: {price}", show_alert=True)
        return
    
    new_gold = user["gold"] - price
    
    if "weapon" in item:
        equip = user["equipment"]
        equip["soldiers"] = equip.get("soldiers", 0) + 10
        update_user(user_id, gold=new_gold, equipment=equip)
        msg = "🪖 ۱۰ سرباز قاچاق دریافت کردید!"
    elif "oil" in item:
        new_oil = user["oil"] + 50
        update_user(user_id, gold=new_gold, oil=new_oil)
        msg = "🛢️ ۵۰ نفت قاچاق دریافت کردید!"
    elif "missile" in item:
        equip = user["equipment"]
        equip["missiles"] = equip.get("missiles", 0) + 5
        update_user(user_id, gold=new_gold, equipment=equip)
        msg = "🚀 ۵ موشک قاچاق دریافت کردید!"
    elif "drone" in item:
        equip = user["equipment"]
        equip["fighters"] = equip.get("fighters", 0) + 3
        update_user(user_id, gold=new_gold, equipment=equip)
        msg = "✈️ ۳ پهپاد قاچاق دریافت کردید!"
    
    await query.edit_message_text(
        f"✅ خرید قاچاق موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"{msg}\n"
        f"💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به بازار سیاه", callback_data="black_market")]])
    )

# ========== نقشه جنگی ==========
async def war_map(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    npcs = get_npc_countries()
    text = "🗺️ نقشه جنگی\n━━━━━━━━━━━━━━━━━━━━\n\n"
    
    if not npcs:
        text += "❌ هیچ کشوری برای نبرد وجود ندارد!"
    else:
        for npc in npcs:
            power = calculate_attack_power(npc['equipment'], npc['army'], npc['defense_power'])
            text += f"🏳️ {npc['name']}\n"
            text += f"⚔️ قدرت: {power}\n"
            text += f"💰 طلا: {npc['gold']:,}\n"
            text += f"📈 سهام: {npc['share_price']}\n"
            text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    # نمایش کاربران هم
    users = get_all_users()
    if users:
        text += "\n👥 کاربران:\n"
        for u in users[:5]:
            user_obj = get_user(u["user_id"])
            if user_obj:
                power = calculate_attack_power(user_obj["equipment"], user_obj["army"], user_obj["tech"], user_obj.get("vip_buildings"))
                text += f"🏳️ {u['name']} | ⚔️ {power}\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== رتبه‌بندی ==========
async def rankings(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT country_name, gold, total_wins, economy, is_vip, clan FROM users ORDER BY gold DESC LIMIT 10')
    rows = c.fetchall()
    conn.close()
    
    if not rows:
        await query.edit_message_text("❌ هیچ کاربری وجود ندارد")
        return
    
    text = "🏆 رتبه‌بندی کشورها\n━━━━━━━━━━━━━━━━━━━━\n\n"
    for i, row in enumerate(rows, 1):
        medal = "🥇" if i == 1 else "🥈" if i == 2 else "🥉" if i == 3 else f"{i}."
        vip = " 👑" if row[4] else ""
        clan = f" [{row[5]}]" if row[5] else ""
        text += f"{medal} {row[0]}{vip}{clan}\n"
        text += f"💰 طلا: {row[1]:,}\n"
        text += f"🏆 پیروزی‌ها: {row[2]}\n"
        text += f"🏭 اقتصاد: {row[3]}\n"
        text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]]
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

# ========== شرط‌بندی ==========
async def betting(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    npcs = get_npc_countries()
    if len(npcs) < 2:
        await query.edit_message_text("کشور کافی برای شرط‌بندی وجود ندارد!")
        return
    
    selected = random.sample(npcs, 2)
    country1, country2 = selected[0], selected[1]
    
    context.user_data['bet_country1'] = country1
    context.user_data['bet_country2'] = country2
    
    keyboard = [
        [InlineKeyboardButton(f"🎯 {country1['name']} (شانس {random.randint(30, 70)}%)", callback_data="bet_1")],
        [InlineKeyboardButton(f"🎯 {country2['name']} (شانس {random.randint(30, 70)}%)", callback_data="bet_2")],
        [InlineKeyboardButton("💰 مقدار شرط را وارد کنید", callback_data="bet_amount")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    await query.edit_message_text(
        f"🎰 شرط‌بندی\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 طلای شما: {user['gold']:,}\n"
        f"📋 دو کشور برای شرط:\n"
        f"۱. {country1['name']} (قدرت: {calculate_attack_power(country1['equipment'], country1['army'], country1['defense_power'])})\n"
        f"۲. {country2['name']} (قدرت: {calculate_attack_power(country2['equipment'], country2['army'], country2['defense_power'])})\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"ابتدا کشور را انتخاب کنید، سپس مقدار شرط را وارد کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def bet_amount(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "💰 مقدار طلای شرط را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="betting")]])
    )
    context.user_data['waiting_for'] = 'bet_amount'
    return

async def receive_bet_amount(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    
    try:
        amount = int(update.message.text)
    except:
        await update.message.reply_text("❌ عدد معتبر وارد کنید!")
        return
    
    if amount < 100:
        await update.message.reply_text("❌ حداقل شرط ۱۰۰ طلا است!")
        return
    
    if amount > user["gold"]:
        await update.message.reply_text(f"❌ طلای کافی ندارید! (موجودی: {user['gold']:,})")
        return
    
    context.user_data['bet_amount'] = amount
    await update.message.reply_text(
        f"✅ شرط {amount:,} طلا ثبت شد!\n"
        f"منتظر نتیجه بمانید...",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    
    country1 = context.user_data.get('bet_country1')
    country2 = context.user_data.get('bet_country2')
    
    if not country1 or not country2:
        await update.message.reply_text("❌ خطا! دوباره شرط ببندید.")
        return
    
    power1 = calculate_attack_power(country1['equipment'], country1['army'], country1['defense_power'])
    power2 = calculate_attack_power(country2['equipment'], country2['army'], country2['defense_power'])
    
    win_chance = power1 / (power1 + power2) * 100
    is_win = random.random() * 100 < win_chance
    
    if is_win:
        winnings = amount * 2
        new_gold = user["gold"] + winnings
        new_bets = user["total_bets"] + 1
        new_bet_wins = user["total_bet_wins"] + 1
        update_user(user_id, gold=new_gold, total_bets=new_bets, total_bet_wins=new_bet_wins)
        await update.message.reply_text(
            f"🎉 برنده شدید!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"✅ {country1['name']} پیروز شد!\n"
            f"💰 برد شما: {winnings:,} طلا\n"
            f"💰 موجودی جدید: {new_gold:,}"
        )
    else:
        new_bets = user["total_bets"] + 1
        update_user(user_id, total_bets=new_bets)
        await update.message.reply_text(
            f"💔 باختید!\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"❌ {country2['name']} پیروز شد!\n"
            f"💸 {amount:,} طلا از دست دادید.\n"
            f"💰 موجودی جدید: {user['gold'] - amount:,}"
        )
    
    context.user_data['waiting_for'] = None

# ========== کلن‌ها ==========
async def clans(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    if user["clan"]:
        clan = get_clan(user["clan"])
        if clan:
            text = (
                f"🏰 کلن {clan['name']}\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
                f"👤 رهبر: {clan['owner_id']}\n"
                f"👥 اعضا: {len(clan['members'])} نفر\n"
                f"💰 طلا: {clan['gold']:,}\n"
                f"🛢️ نفت: {clan['oil']:,}\n"
                f"📈 سطح: {clan['level']}\n"
                f"🏆 برد: {clan['wins']} | شکست: {clan['losses']}\n"
            )
            keyboard = [
                [InlineKeyboardButton("📋 لیست اعضا", callback_data="clan_members")],
                [InlineKeyboardButton("🚪 خروج از کلن", callback_data="clan_leave")],
                [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
            ]
            await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))
        else:
            update_user(user_id, clan="")
            await query.edit_message_text("کلن شما حذف شده است!", reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]]))
    else:
        keyboard = [
            [InlineKeyboardButton("🏰 ایجاد کلن جدید", callback_data="clan_create")],
            [InlineKeyboardButton("📋 لیست کلن‌ها", callback_data="clan_list")],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
        ]
        await query.edit_message_text(
            f"🏰 کلن‌ها\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"شما عضو هیچ کلنی نیستید.\n\n"
            f"📋 انتخاب کنید:",
            reply_markup=InlineKeyboardMarkup(keyboard)
        )

async def clan_create(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "🏰 نام کلن را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="clans")]])
    )
    context.user_data['waiting_for'] = 'clan_name'

async def receive_clan_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    clan_name = update.message.text.strip()
    
    if not clan_name:
        await update.message.reply_text("نام کلن نمی‌تواند خالی باشد!")
        return
    
    if get_clan(clan_name):
        await update.message.reply_text("❌ این نام قبلاً ثبت شده است!")
        return
    
    create_clan(clan_name, user_id)
    update_user(user_id, clan=clan_name)
    
    await update.message.reply_text(
        f"✅ کلن {clan_name} با موفقیت ایجاد شد!\n"
        f"شما رهبر کلن هستید.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    context.user_data['waiting_for'] = None

async def clan_list(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT name, owner_id, level, wins FROM clans ORDER BY level DESC LIMIT 10')
    rows = c.fetchall()
    conn.close()
    
    if not rows:
        await query.edit_message_text("❌ هیچ کلنی وجود ندارد!")
        return
    
    text = "🏰 لیست کلن‌ها\n━━━━━━━━━━━━━━━━━━━━\n\n"
    for i, row in enumerate(rows, 1):
        text += f"{i}. {row[0]}\n"
        text += f"👤 رهبر: {row[1]}\n"
        text += f"📈 سطح: {row[2]} | 🏆 برد: {row[3]}\n"
        text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data="clans")]]
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def clan_members(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user or not user["clan"]:
        await query.edit_message_text("شما عضو هیچ کلنی نیستید!")
        return
    clan = get_clan(user["clan"])
    if not clan:
        await query.edit_message_text("کلن یافت نشد!")
        return
    members_text = "📋 اعضای کلن\n━━━━━━━━━━━━━━━━━━━━\n\n"
    for member_id in clan["members"]:
        member = get_user(member_id)
        if member:
            members_text += f"• {member['country_name']} (ID: {member_id})\n"
    await query.edit_message_text(
        members_text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="clans")]])
    )

async def clan_leave(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user or not user["clan"]:
        await query.edit_message_text("شما عضو هیچ کلنی نیستید!")
        return
    clan = get_clan(user["clan"])
    if clan["owner_id"] == user_id:
        await query.edit_message_text("شما رهبر کلن هستید، ابتدا رهبری را به دیگری واگذار کنید!")
        return
    members = clan["members"]
    members.remove(user_id)
    update_clan(user["clan"], members=members)
    update_user(user_id, clan="")
    await query.edit_message_text("✅ شما از کلن خارج شدید!")

# ========== دوئل ==========
async def duel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    keyboard = [
        [InlineKeyboardButton("⚔️ درخواست دوئل", callback_data="duel_request")],
        [InlineKeyboardButton("📊 آمار دوئل‌ها", callback_data="duel_stats")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    await query.edit_message_text(
        f"⚔️ دوئل\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏆 برد: {user['duel_wins']}\n"
        f"💔 باخت: {user['duel_losses']}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def duel_request(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "⚔️ آیدی عددی حریف را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="duel")]])
    )
    context.user_data['waiting_for'] = 'duel_opponent'

async def receive_duel_opponent(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    
    try:
        opponent_id = int(update.message.text)
    except:
        await update.message.reply_text("❌ آیدی عددی معتبر وارد کنید!")
        return
    
    if opponent_id == user_id:
        await update.message.reply_text("❌ نمی‌توانید با خودتان دوئل کنید!")
        return
    
    opponent = get_user(opponent_id)
    if not opponent:
        await update.message.reply_text("❌ کاربر یافت نشد!")
        return
    
    user_power = calculate_attack_power(user["equipment"], user["army"], user["tech"], user.get("vip_buildings"))
    opponent_power = calculate_attack_power(opponent["equipment"], opponent["army"], opponent["tech"], opponent.get("vip_buildings"))
    
    if user_power > opponent_power:
        gold_win = int(opponent["gold"] * 0.1)
        new_gold = user["gold"] + gold_win
        new_opponent_gold = opponent["gold"] - gold_win
        update_user(user_id, gold=new_gold, duel_wins=user["duel_wins"] + 1)
        update_user(opponent_id, gold=new_opponent_gold, duel_losses=opponent["duel_losses"] + 1)
        
        result_text = (
            f"⚔️ نتیجه دوئل\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"✅ {user['country_name']} پیروز شد!\n"
            f"💰 برد: {gold_win:,} طلا\n"
            f"⚔️ قدرت شما: {user_power}\n"
            f"⚔️ قدرت حریف: {opponent_power}"
        )
    else:
        gold_lost = int(user["gold"] * 0.1)
        new_gold = user["gold"] - gold_lost
        new_opponent_gold = opponent["gold"] + gold_lost
        update_user(user_id, gold=new_gold, duel_losses=user["duel_losses"] + 1)
        update_user(opponent_id, gold=new_opponent_gold, duel_wins=opponent["duel_wins"] + 1)
        
        result_text = (
            f"⚔️ نتیجه دوئل\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"❌ {opponent['country_name']} پیروز شد!\n"
            f"💸 طلای از دست رفته: {gold_lost:,}\n"
            f"⚔️ قدرت شما: {user_power}\n"
            f"⚔️ قدرت حریف: {opponent_power}"
        )
    
    await update.message.reply_text(result_text)
    context.user_data['waiting_for'] = None

async def duel_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    await query.edit_message_text(
        f"📊 آمار دوئل‌ها\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏆 برد: {user['duel_wins']}\n"
        f"💔 باخت: {user['duel_losses']}\n"
        f"📈 درصد برد: {int(user['duel_wins'] / (user['duel_wins'] + user['duel_losses']) * 100) if user['duel_wins'] + user['duel_losses'] > 0 else 0}%",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="duel")]])
    )

# ========== بازار سهام ==========
async def stock_market(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    npcs = get_npc_countries()
    keyboard = []
    for npc in npcs:
        keyboard.append([InlineKeyboardButton(f"📈 {npc['name']} (قیمت: {npc['share_price']})", callback_data=f"stock_{npc['id']}")])
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="menu")])
    
    shares = user.get("shares", {})
    text = f"📈 بازار سهام\n━━━━━━━━━━━━━━━━━━━━\n💰 طلا: {user['gold']:,}\n\n"
    if shares:
        text += "📋 سهام شما:\n"
        for country, amount in shares.items():
            text += f"• {country}: {amount}\n"
    else:
        text += "❌ هیچ سهمی ندارید\n"
    text += f"\n📋 انتخاب کنید:"
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def stock_buy(update: Update, context: ContextTypes.DEFAULT_TYPE, npc_id):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    npc = get_npc_by_id(npc_id)
    
    if not npc:
        await query.edit_message_text("❌ کشور یافت نشد!")
        return
    
    price = npc["share_price"]
    if user["gold"] < price:
        await query.answer(f"طلای کافی ندارید! نیاز: {price}", show_alert=True)
        return
    
    shares = user.get("shares", {})
    shares[npc["name"]] = shares.get(npc["name"], 0) + 1
    new_gold = user["gold"] - price
    
    new_price = int(price * (1 + random.random() * 0.1))
    update_npc(npc_id, share_price=new_price)
    update_user(user_id, gold=new_gold, shares=shares)
    
    await query.edit_message_text(
        f"✅ خرید سهام موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏳️ {npc['name']}\n"
        f"💰 قیمت: {price}\n"
        f"📈 قیمت جدید: {new_price}\n"
        f"💰 طلای باقی‌مانده: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به بازار", callback_data="stock_market")]])
    )

async def stock_sell(update: Update, context: ContextTypes.DEFAULT_TYPE, npc_id):
    query = update.callback_query
    user_id = query.from_user.id
    user = get_user(user_id)
    npc = get_npc_by_id(npc_id)
    
    if not npc:
        await query.edit_message_text("❌ کشور یافت نشد!")
        return
    
    shares = user.get("shares", {})
    if npc["name"] not in shares or shares[npc["name"]] == 0:
        await query.answer("شما این سهم را ندارید!", show_alert=True)
        return
    
    shares[npc["name"]] -= 1
    if shares[npc["name"]] == 0:
        del shares[npc["name"]]
    
    price = npc["share_price"]
    new_gold = user["gold"] + price
    
    new_price = max(50, int(price * (1 - random.random() * 0.1)))
    update_npc(npc_id, share_price=new_price)
    update_user(user_id, gold=new_gold, shares=shares)
    
    await query.edit_message_text(
        f"✅ فروش سهام موفق!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🏳️ {npc['name']}\n"
        f"💰 قیمت: {price}\n"
        f"📈 قیمت جدید: {new_price}\n"
        f"💰 طلای جدید: {new_gold:,}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به بازار", callback_data="stock_market")]])
    )

# ========== ماموریت روزانه ==========
async def daily_mission(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    now = int(time.time())
    last = user.get("last_daily_mission", 0)
    
    if now - last < 86400:
        remaining = 86400 - (now - last)
        hours = remaining // 3600
        minutes = (remaining % 3600) // 60
        await query.answer(f"⏳ {hours} ساعت و {minutes} دقیقه دیگر", show_alert=True)
        return
    
    missions = [
        {"type": "حمله", "target": 3, "reward": 500},
        {"type": "خرید", "target": 5, "reward": 300},
        {"type": "فروش", "target": 10, "reward": 400},
        {"type": "جاسوسی", "target": 2, "reward": 200},
    ]
    
    mission = random.choice(missions)
    update_user(user_id, last_daily_mission=now)
    
    await query.edit_message_text(
        f"🎯 ماموریت روزانه\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 ماموریت: {mission['target']} بار {mission['type']}\n"
        f"🎁 جایزه: {mission['reward']} طلا\n"
        f"🔥 استریک: {user['daily_streak'] + 1}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"✅ ماموریت جدید ثبت شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== هدیه روزانه ==========
async def daily_gift(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    now = int(time.time())
    last = user.get("last_daily_gift", 0)
    
    if now - last < 86400:
        remaining = 86400 - (now - last)
        hours = remaining // 3600
        minutes = (remaining % 3600) // 60
        await query.answer(f"⏳ {hours} ساعت و {minutes} دقیقه دیگر", show_alert=True)
        return
    
    gifts = [
        {"name": "طلا", "amount": random.randint(100, 500)},
        {"name": "نفت", "amount": random.randint(50, 200)},
        {"name": "سرباز", "amount": random.randint(1, 5)},
        {"name": "موشک", "amount": random.randint(1, 3)},
    ]
    
    gift = random.choice(gifts)
    new_streak = user["daily_streak"] + 1
    
    if gift["name"] == "طلا":
        new_gold = user["gold"] + gift["amount"]
        update_user(user_id, gold=new_gold, last_daily_gift=now, daily_streak=new_streak)
        msg = f"💰 {gift['amount']} طلا"
    elif gift["name"] == "نفت":
        new_oil = user["oil"] + gift["amount"]
        update_user(user_id, oil=new_oil, last_daily_gift=now, daily_streak=new_streak)
        msg = f"🛢️ {gift['amount']} نفت"
    elif gift["name"] == "سرباز":
        equip = user["equipment"]
        equip["soldiers"] = equip.get("soldiers", 0) + gift["amount"]
        update_user(user_id, equipment=equip, last_daily_gift=now, daily_streak=new_streak)
        msg = f"🪖 {gift['amount']} سرباز"
    elif gift["name"] == "موشک":
        equip = user["equipment"]
        equip["missiles"] = equip.get("missiles", 0) + gift["amount"]
        update_user(user_id, equipment=equip, last_daily_gift=now, daily_streak=new_streak)
        msg = f"🚀 {gift['amount']} موشک"
    
    await query.edit_message_text(
        f"🎁 هدیه روزانه\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"✅ شما دریافت کردید: {msg}\n"
        f"🔥 استریک: {new_streak}\n"
        f"💪 پاداش استریک: {new_streak * 10} طلا اضافه شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== مسابقه گروهی ==========
async def group_contest(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    if not user:
        await query.edit_message_text("ابتدا /start کنید")
        return
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT country_name, group_points FROM users ORDER BY group_points DESC LIMIT 5')
    rows = c.fetchall()
    conn.close()
    
    text = "🏆 مسابقه گروهی\n━━━━━━━━━━━━━━━━━━━━\n\n"
    text += "📊 امتیاز شما: {}\n\n".format(user["group_points"])
    text += "🏅 برترین‌های گروه:\n"
    
    for i, row in enumerate(rows, 1):
        medal = "🥇" if i == 1 else "🥈" if i == 2 else "🥉" if i == 3 else f"{i}."
        text += f"{medal} {row[0]}: {row[1]} امتیاز\n"
    
    keyboard = [
        [InlineKeyboardButton("🎯 شرکت در مسابقه", callback_data="contest_join")],
        [InlineKeyboardButton("📋 قوانین مسابقه", callback_data="contest_rules")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def contest_join(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    points = random.randint(1, 10)
    new_points = user["group_points"] + points
    update_user(user_id, group_points=new_points)
    
    await query.edit_message_text(
        f"🎯 شما در مسابقه شرکت کردید!\n"
        f"📊 {points} امتیاز دریافت کردید!\n"
        f"📊 امتیاز کل: {new_points}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="group_contest")]])
    )

async def contest_rules(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    rules = (
        "📋 قوانین مسابقه گروهی\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        "۱. هر کاربر می‌تواند روزانه ۵ بار در مسابقه شرکت کند.\n"
        "۲. هر بار شرکت = ۱ تا ۱۰ امتیاز تصادفی.\n"
        "۳. در پایان هر هفته به ۳ نفر برتر جایزه تعلق می‌گیرد.\n"
        "۴. جایزه اول: ۵,۰۰۰ طلا\n"
        "۵. جایزه دوم: ۳,۰۰۰ طلا\n"
        "۶. جایزه سوم: ۱,۰۰۰ طلا\n"
        "۷. مسابقه هر هفته یکشنبه ساعت ۲۴:۰۰ بازنشانی می‌شود."
    )
    
    await query.edit_message_text(
        rules,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="group_contest")]])
    )

# ========== بیانیه رسمی ==========
async def official_statement(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    keyboard = [
        [InlineKeyboardButton("📝 ثبت بیانیه", callback_data="set_statement")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    text = f"📜 بیانیه رسمی\n━━━━━━━━━━━━━━━━━━━━\n\n"
    if user["statement"]:
        text += f"بیانیه فعلی:\n{user['statement']}\n"
    else:
        text += "❌ هیچ بیانیه‌ای ثبت نشده است."
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def set_statement(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "📝 بیانیه جدید را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="official_statement")]])
    )
    context.user_data['waiting_for'] = 'statement'

async def receive_statement(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    statement = update.message.text
    update_user(user_id, statement=statement)
    await update.message.reply_text(
        f"✅ بیانیه ثبت شد!\n📜 {statement}",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    context.user_data['waiting_for'] = None

# ========== زیرمجموعه‌گیری ==========
async def subsidiary(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    keyboard = [
        [InlineKeyboardButton("➕ ثبت زیرمجموعه", callback_data="set_subsidiary")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    text = f"👥 زیرمجموعه‌گیری\n━━━━━━━━━━━━━━━━━━━━\n\n"
    if user["subsidiary"]:
        text += f"زیرمجموعه فعلی:\n{user['subsidiary']}"
    else:
        text += "❌ هیچ زیرمجموعه‌ای ثبت نشده است."
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def set_subsidiary(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "👥 نام زیرمجموعه را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="subsidiary")]])
    )
    context.user_data['waiting_for'] = 'subsidiary'

async def receive_subsidiary(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    subsidiary = update.message.text
    update_user(user_id, subsidiary=subsidiary)
    await update.message.reply_text(
        f"✅ زیرمجموعه {subsidiary} ثبت شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    context.user_data['waiting_for'] = None

# ========== قوانین جنگ ==========
async def war_rules(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    rules = (
        "📋 قوانین جنگ\n"
        "━━━━━━━━━━━━━━━━━━━━\n"
        "⚔️ ۱. هر بازیکن می‌تواند هر ۱۵ دقیقه یکبار حمله کند. (VIP: ۵ دقیقه)\n"
        "💣 ۲. حمله اتمی نیازمند سلاح هسته‌ای است.\n"
        "💰 ۳. پیروزی = غنیمت +۳۰٪ تا ۶۰٪ از منابع حریف.\n"
        "📉 ۴. شکست = از دست دادن ۵٪ تا ۱۵٪ منابع.\n"
        "🛡️ ۵. پدافند قدرت دفاعی را افزایش می‌دهد.\n"
        "🤝 ۶. اتحاد با کشورها باعث افزایش قدرت می‌شود.\n"
        "🏗️ ۷. پروژه‌های ملی باعث رشد کشور می‌شوند.\n"
        "⏳ ۸. پول رایگان هر ۱۲ ساعت قابل دریافت است. (VIP: ۶ ساعت)\n"
        "🏭 ۹. شرکت‌ها هر ساعت درآمد دارند. (VIP: ۳۰ دقیقه)\n"
        "☢️ ۱۰. سلاح هسته‌ای یکبار مصرف است.\n"
        "👑 ۱۱. VIP با ۲۰,۰۰۰,۰۰۰ طلا قابل خرید است.\n"
        "💊 ۱۲. خرید و فروش مواد مخدر فقط برای VIP ها.\n"
        "💻 ۱۳. حمله سایبری فقط برای VIP ها.\n"
        "🏰 ۱۴. کلن‌ها: ایجاد، عضوگیری و جنگ کلن‌ها.\n"
        "⚔️ ۱۵. دوئل: مبارزه ۱ به ۱ بین کاربران.\n"
        "📈 ۱۶. بازار سهام: خرید و فروش سهام کشورها.\n"
        "🎯 ۱۷. ماموریت روزانه: هر روز ماموریت جدید.\n"
        "🎁 ۱۸. هدیه روزانه: هر روز هدیه بگیرید.\n"
        "🏆 ۱۹. مسابقه گروهی: رقابت بین کاربران گروه."
    )
    
    await query.edit_message_text(
        rules,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== گفتگوی محرمانه ==========
async def secret_chat(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    user = get_user(user_id)
    
    keyboard = [
        [InlineKeyboardButton("📝 ارسال پیام محرمانه", callback_data="send_secret")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    text = f"💬 گفتگوی محرمانه\n━━━━━━━━━━━━━━━━━━━━\n\n"
    if user["secret_chat"]:
        text += f"آخرین پیام:\n{user['secret_chat']}"
    else:
        text += "❌ هیچ پیام محرمانه‌ای وجود ندارد."
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard))

async def send_secret(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "💬 پیام محرمانه خود را وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="secret_chat")]])
    )
    context.user_data['waiting_for'] = 'secret'

async def receive_secret(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    secret = update.message.text
    update_user(user_id, secret_chat=secret)
    await update.message.reply_text(
        f"✅ پیام محرمانه ثبت شد!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )
    context.user_data['waiting_for'] = None

# ========== رویداد جهانی ==========
async def global_event(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    events = [
        "🌍 رویداد جهانی: جنگ جهانی سوم! همه کشورها درگیر هستند.",
        "🌍 رویداد جهانی: کشف نفت جدید! نفت همه کشورها +۱۰۰",
        "🌍 رویداد جهانی: بحران اقتصادی! طلای همه کشورها -۲۰۰",
        "🌍 رویداد جهانی: اتحاد جهانی! همه کشورها متحد می‌شوند.",
        "🌍 رویداد جهانی: حمله بیگانگان! همه کشورها باید متحد شوند.",
        "🌍 رویداد جهانی: پیشرفت فناوری! فناوری همه کشورها +۵"
    ]
    
    event = random.choice(events)
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    if "نفت" in event:
        c.execute('UPDATE users SET oil = oil + 100')
    elif "طلا" in event:
        c.execute('UPDATE users SET gold = gold - 200')
    elif "فناوری" in event:
        c.execute('UPDATE users SET tech = tech + 5')
    conn.commit()
    conn.close()
    
    await query.edit_message_text(
        f"{event}\n\n✅ رویداد به همه کشورها اعمال شد.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== ناظر کشورها ==========
async def observer(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    npcs = get_npc_countries()
    text = "👁️ ناظر کشورها\n━━━━━━━━━━━━━━━━━━━━\n\n"
    
    if not npcs:
        text += "❌ هیچ کشوری وجود ندارد!"
    else:
        for npc in npcs:
            power = calculate_attack_power(npc['equipment'], npc['army'], npc['defense_power'])
            status = "🟢 قوی" if power > 50 else "🟡 متوسط" if power > 25 else "🔴 ضعیف"
            text += f"{npc['name']}\n"
            text += f"⚔️ قدرت: {power} | {status}\n"
            text += f"💰 طلا: {npc['gold']:,} | 🛢️ نفت: {npc['oil']:,}\n"
            text += f"📈 سهام: {npc['share_price']}\n"
            text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
    )

# ========== پنل ادمین ==========
async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_user.id not in ADMIN_IDS:
        await update.message.reply_text("🚫 شما دسترسی ادمین ندارید!")
        return
    
    users = get_all_users()
    npcs = get_npc_countries()
    
    keyboard = [
        [InlineKeyboardButton("📊 آمار کاربران", callback_data="admin_stats")],
        [InlineKeyboardButton("💰 افزودن طلا", callback_data="admin_add_gold")],
        [InlineKeyboardButton("🛢️ افزودن نفت", callback_data="admin_add_oil")],
        [InlineKeyboardButton("👑 اعطای VIP", callback_data="admin_vip")],
        [InlineKeyboardButton("🚫 بن کاربر", callback_data="admin_ban")],
        [InlineKeyboardButton("📋 لیست کاربران", callback_data="admin_users")],
        [InlineKeyboardButton("🌍 لیست NPC ها", callback_data="admin_npcs")],
        [InlineKeyboardButton("➕ اضافه کردن کشور", callback_data="admin_add_country")],
        [InlineKeyboardButton("📊 گزارش حملات", callback_data="admin_logs")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]
    ]
    
    await update.message.reply_text(
        f"👑 پنل مدیریت\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"👥 کاربران: {len(users)}\n"
        f"🤖 NPC ها: {len(npcs)}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📋 انتخاب کنید:",
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def admin_stats(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT COUNT(*) FROM users')
    total_users = c.fetchone()[0]
    c.execute('SELECT SUM(gold) FROM users')
    total_gold = c.fetchone()[0] or 0
    c.execute('SELECT COUNT(*) FROM users WHERE is_vip = 1')
    total_vip = c.fetchone()[0]
    c.execute('SELECT COUNT(*) FROM attack_logs')
    total_attacks = c.fetchone()[0]
    c.execute('SELECT COUNT(*) FROM transfers')
    total_transfers = c.fetchone()[0]
    conn.close()
    
    await query.edit_message_text(
        f"📊 آمار ربات\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"👥 کل کاربران: {total_users}\n"
        f"💰 کل طلا: {total_gold:,}\n"
        f"👑 VIP ها: {total_vip}\n"
        f"⚔️ کل حملات: {total_attacks}\n"
        f"💰 انتقال‌ها: {total_transfers}\n"
        f"━━━━━━━━━━━━━━━━━━━━",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="admin")]])
    )

async def admin_users(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    users = get_all_users()
    text = "🌍 لیست کاربران\n━━━━━━━━━━━━━━━━━━━━\n\n"
    
    if not users:
        text += "❌ هیچ کاربری وجود ندارد!"
    else:
        for i, user in enumerate(users[:20], 1):
            vip = " 👑" if user["is_vip"] else ""
            clan = f" [{user['clan']}]" if user['clan'] else ""
            text += f"{i}. {user['name']}{vip}{clan}\n"
            text += f"💰 طلا: {user['gold']:,} | 🏆 برد: {user['wins']}\n"
            text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="admin")]])
    )

async def admin_npcs(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    npcs = get_npc_countries()
    text = "🤖 لیست NPC ها\n━━━━━━━━━━━━━━━━━━━━\n\n"
    
    if not npcs:
        text += "❌ هیچ NPC ای وجود ندارد!"
    else:
        for i, npc in enumerate(npcs[:20], 1):
            text += f"{i}. {npc['name']}\n"
            text += f"💰 طلا: {npc['gold']:,} | ⚔️ ارتش: {npc['army']}\n"
            text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="admin")]])
    )

async def admin_logs(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT attacker_name, defender_name, result, gold_stolen, timestamp FROM attack_logs ORDER BY timestamp DESC LIMIT 10')
    rows = c.fetchall()
    conn.close()
    
    text = "📊 آخرین گزارش‌های حمله\n━━━━━━━━━━━━━━━━━━━━\n\n"
    
    if not rows:
        text += "❌ هیچ حمله‌ای ثبت نشده است!"
    else:
        for row in rows:
            time_str = datetime.fromtimestamp(row[4]).strftime("%H:%M")
            text += f"🕐 {time_str} - {row[0]} → {row[1]}\n"
            text += f"📊 نتیجه: {row[2]} | 💰 غنیمت: {row[3]}\n"
            text += f"━━━━━━━━━━━━━━━━━━━━\n"
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="admin")]])
    )

async def admin_add_gold(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "💰 آیدی عددی کاربر و مقدار طلا را وارد کنید:\nمثال: 123456789 5000",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="admin")]])
    )
    context.user_data['waiting_for'] = 'admin_gold'

async def admin_add_oil(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "🛢️ آیدی عددی کاربر و مقدار نفت را وارد کنید:\nمثال: 123456789 500",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="admin")]])
    )
    context.user_data['waiting_for'] = 'admin_oil'

async def admin_vip(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "👑 آیدی عددی کاربر را برای اعطای VIP وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="admin")]])
    )
    context.user_data['waiting_for'] = 'admin_vip'

async def admin_ban(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "🚫 آیدی عددی کاربر را برای بن کردن وارد کنید:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="admin")]])
    )
    context.user_data['waiting_for'] = 'admin_ban'

async def admin_add_country(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "➕ اطلاعات کشور جدید را وارد کنید:\n"
        "فرمت: نام | طلا | نفت | ارتش\n"
        "مثال: 🇩🇪 آلمان | 5000 | 2000 | 10",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="admin")]])
    )
    context.user_data['waiting_for'] = 'admin_add_country'

async def admin_text_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    waiting = context.user_data.get('waiting_for')
    
    if waiting == 'admin_gold':
        try:
            parts = update.message.text.split()
            target_id = int(parts[0])
            amount = int(parts[1])
        except:
            await update.message.reply_text("❌ فرمت اشتباه! مثال: 123456789 5000")
            return
        
        user = get_user(target_id)
        if not user:
            await update.message.reply_text("❌ کاربر یافت نشد!")
            return
        
        new_gold = user["gold"] + amount
        update_user(target_id, gold=new_gold)
        await update.message.reply_text(f"✅ {amount:,} طلا به {user['country_name']} اضافه شد!")
        context.user_data['waiting_for'] = None
    
    elif waiting == 'admin_oil':
        try:
            parts = update.message.text.split()
            target_id = int(parts[0])
            amount = int(parts[1])
        except:
            await update.message.reply_text("❌ فرمت اشتباه! مثال: 123456789 500")
            return
        
        user = get_user(target_id)
        if not user:
            await update.message.reply_text("❌ کاربر یافت نشد!")
            return
        
        new_oil = user["oil"] + amount
        update_user(target_id, oil=new_oil)
        await update.message.reply_text(f"✅ {amount} نفت به {user['country_name']} اضافه شد!")
        context.user_data['waiting_for'] = None
    
    elif waiting == 'admin_vip':
        try:
            target_id = int(update.message.text)
        except:
            await update.message.reply_text("❌ آیدی عددی معتبر وارد کنید!")
            return
        
        user = get_user(target_id)
        if not user:
            await update.message.reply_text("❌ کاربر یافت نشد!")
            return
        
        update_user(target_id, is_vip=True)
        await update.message.reply_text(f"✅ کاربر {user['country_name']} VIP شد!")
        context.user_data['waiting_for'] = None
    
    elif waiting == 'admin_ban':
        try:
            target_id = int(update.message.text)
        except:
            await update.message.reply_text("❌ آیدی عددی معتبر وارد کنید!")
            return
        
        user = get_user(target_id)
        if not user:
            await update.message.reply_text("❌ کاربر یافت نشد!")
            return
        
        update_user(target_id, is_banned=True)
        await update.message.reply_text(f"🚫 کاربر {user['country_name']} بن شد!")
        context.user_data['waiting_for'] = None
    
    elif waiting == 'admin_add_country':
        try:
            parts = update.message.text.split('|')
            name = parts[0].strip()
            gold = int(parts[1].strip())
            oil = int(parts[2].strip())
            army = int(parts[3].strip())
        except:
            await update.message.reply_text("❌ فرمت اشتباه! مثال: 🇩🇪 آلمان | 5000 | 2000 | 10")
            return
        
        conn = sqlite3.connect(DB_NAME)
        c = conn.cursor()
        c.execute('INSERT INTO npc_countries (name, gold, oil, army, equipment, defense_power, share_price) VALUES (?, ?, ?, ?, ?, ?, ?)',
                  (name, gold, oil, army, '{"soldiers":10,"tanks":2,"fighters":1,"ships":0,"missiles":0,"defense":1}', 5, 100))
        conn.commit()
        conn.close()
        
        await update.message.reply_text(f"✅ کشور {name} با موفقیت اضافه شد!")
        context.user_data['waiting_for'] = None

async def group_attack(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    if not user:
        await update.message.reply_text("ابتدا در پیوی /start کنید!")
        return
    
    npcs = get_npc_countries()
    if not npcs:
        await update.message.reply_text("❌ هیچ کشوری برای حمله وجود ندارد!")
        return
    
    target = random.choice(npcs)
    power = calculate_attack_power(user["equipment"], user["army"], user["tech"], user.get("vip_buildings"))
    target_power = calculate_attack_power(target["equipment"], target["army"], target["defense_power"])
    
    if power > target_power:
        gold = int(target["gold"] * 0.2)
        oil = int(target["oil"] * 0.2)
        update_user(user_id, gold=user["gold"] + gold, oil=user["oil"] + oil)
        update_npc(target["id"], gold=target["gold"] - gold, oil=target["oil"] - oil)
        await update.message.reply_text(
            f"⚔️ {user['country_name']} به {target['name']} حمله کرد!\n"
            f"✅ پیروزی! 💰 {gold} طلا و 🛢️ {oil} نفت غنیمت گرفت!"
        )
    else:
        lost = int(user["gold"] * 0.1)
        update_user(user_id, gold=user["gold"] - lost)
        await update.message.reply_text(
            f"⚔️ {user['country_name']} به {target['name']} حمله کرد!\n"
            f"❌ شکست! 💰 {lost} طلا از دست داد!"
        )

async def group_profile(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    user = get_user(user_id)
    if not user:
        await update.message.reply_text("ابتدا در پیوی /start کنید!")
        return
    
    await update.message.reply_text(
        f"🏳️ {user['country_name']}\n"
        f"💰 طلا: {user['gold']:,}\n"
        f"🛢️ نفت: {user['oil']:,}\n"
        f"⚔️ ارتش: {user['army']}\n"
        f"🏆 پیروزی‌ها: {user['total_wins']}\n"
        f"💔 شکست‌ها: {user['total_losses']}\n"
        f"🏰 کلن: {user['clan'] or 'ندارد'}\n"
        f"👑 VIP: {'بله' if user['is_vip'] else 'خیر'}"
    )

async def group_rank(update: Update, context: ContextTypes.DEFAULT_TYPE):
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    c.execute('SELECT country_name, gold, total_wins FROM users ORDER BY gold DESC LIMIT 5')
    rows = c.fetchall()
    conn.close()
    
    text = "🏆 رتبه‌بندی گروه\n━━━━━━━━━━━━━━━━━━━━\n\n"
    for i, row in enumerate(rows, 1):
        medal = "🥇" if i == 1 else "🥈" if i == 2 else "🥉" if i == 3 else f"{i}."
        text += f"{medal} {row[0]}\n💰 {row[1]:,} طلا | 🏆 {row[2]} برد\n"
    
    await update.message.reply_text(text)

# ========== مدیریت کل‌بک‌ها ==========
async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data
    
    if data == "menu":
        await show_main_menu(update, context)
        return
    
    elif data == "my_country":
        await my_country(update, context)
    
    elif data == "transfer_gold":
        await transfer_gold(update, context)
    
    elif data.startswith("transfer_to_"):
        target_id = int(data.split("_")[2])
        await select_transfer_amount(update, context, target_id)
    
    elif data.startswith("confirm_transfer_"):
        amount = int(data.split("_")[2])
        await confirm_transfer(update, context, amount)
    
    elif data == "arms_market":
        await arms_market(update, context)
    
    elif data.startswith("buy_"):
        if data == "buy_soldier":
            await buy_equipment(update, context, "سرباز", "soldiers", 100)
        elif data == "buy_tank":
            await buy_equipment(update, context, "تانک", "tanks", 500)
        elif data == "buy_fighter":
            await buy_equipment(update, context, "جنگنده", "fighters", 1000)
        elif data == "buy_ship":
            await buy_equipment(update, context, "ناو جنگی", "ships", 2000)
        elif data == "buy_missile":
            await buy_equipment(update, context, "موشک", "missiles", 3000)
        elif data == "buy_defense":
            await buy_equipment(update, context, "پدافند", "defense", 2500)
        elif data == "buy_ballistic":
            await buy_equipment(update, context, "موشک بالستیک", "missiles", 10000)
        elif data == "buy_drone":
            await buy_equipment(update, context, "پهپاد", "fighters", 5000)
    
    elif data == "military_attack":
        await military_attack(update, context)
    
    elif data == "attack_user":
        await attack_user(update, context)
    
    elif data.startswith("attack_user_"):
        target_id = int(data.split("_")[2])
        await execute_attack_user(update, context, target_id)
    
    elif data.startswith("npc_"):
        npc_id = int(data.split("_")[1])
        await select_attack_percent(update, context, npc_id)
    
    elif data.startswith("attack_"):
        percent = data.split("_")[1]
        await execute_attack(update, context, percent)
    
    elif data == "nuke_attack":
        await nuke_attack(update, context)
    
    elif data.startswith("nuke_"):
        npc_id = int(data.split("_")[1])
        await execute_nuke(update, context, npc_id)
    
    elif data == "free_money":
        await free_money(update, context)
    
    elif data == "companies":
        await companies(update, context)
    
    elif data == "oil_energy":
        await oil_energy(update, context)
    
    elif data.startswith("sell_oil_"):
        amount = int(data.split("_")[2])
        await sell_oil(update, context, amount)
    
    elif data == "trade":
        await trade(update, context)
    
    elif data.startswith("buy_oil_"):
        if data == "buy_oil_50":
            await buy_oil(update, context, 50, 100)
        elif data == "buy_oil_100":
            await buy_oil(update, context, 100, 200)
        elif data == "buy_oil_500":
            await buy_oil(update, context, 500, 900)
        elif data == "buy_oil_1000":
            await buy_oil(update, context, 1000, 1700)
    
    elif data == "spy":
        await spy(update, context)
    
    elif data == "alliance":
        await alliance(update, context)
    
    elif data.startswith("ally_"):
        npc_id = int(data.split("_")[1])
        await create_alliance(update, context, npc_id)
    
    elif data == "cancel_alliance":
        await cancel_alliance(update, context)
    
    elif data == "national_projects":
        await national_projects(update, context)
    
    elif data.startswith("project_"):
        if data == "project_economy":
            await execute_project(update, context, "economy", 500, None)
        elif data == "project_tech":
            await execute_project(update, context, "tech", 500, None)
        elif data == "project_population":
            await execute_project(update, context, "population", 300, None)
        elif data == "project_nuke":
            await execute_project(update, context, "nuke", 2000, None)
        elif data == "project_shelter":
            await execute_project(update, context, "shelter", 1000, None)
        elif data == "project_army":
            await execute_project(update, context, "army", 400, None)
    
    elif data == "buy_vip":
        await buy_vip(update, context)
    
    elif data == "vip_panel":
        await vip_panel(update, context)
    
    elif data.startswith("vip_"):
        if data == "vip_hospital":
            await build_vip_building(update, context, "hospital", 5000, "بیمارستان")
        elif data == "vip_factory":
            await build_vip_building(update, context, "factory", 10000, "کارخانه اسلحه‌سازی")
        elif data == "vip_refinery":
            await build_vip_building(update, context, "refinery", 7500, "پالایشگاه نفت")
        elif data == "vip_university":
            await build_vip_building(update, context, "university", 5000, "دانشگاه")
        elif data == "vip_airport":
            await build_vip_building(update, context, "airport", 15000, "فرودگاه")
        elif data == "vip_shelter_adv":
            await build_vip_building(update, context, "shelter_advanced", 12000, "پناهگاه پیشرفته")
        elif data == "vip_buy_drugs":
            await drugs_trade(update, context, "buy")
        elif data == "vip_sell_drugs":
            await drugs_trade(update, context, "sell")
        elif data == "vip_cyber_attack":
            await cyber_attack(update, context)
    
    elif data == "black_market":
        await black_market(update, context)
    
    elif data.startswith("black_"):
        if data == "black_missile":
            await black_market_buy(update, context, "missile", 5000)
        elif data == "black_drone":
            await black_market_buy(update, context, "drone", 3000)
        else:
            parts = data.split("_")
            if len(parts) >= 3:
                item = parts[1]
                price = int(parts[2])
                await black_market_buy(update, context, item, price)
    
    elif data == "war_map":
        await war_map(update, context)
    
    elif data == "rankings":
        await rankings(update, context)
    
    elif data == "betting":
        await betting(update, context)
    
    elif data == "bet_amount":
        await bet_amount(update, context)
    
    elif data.startswith("bet_"):
        if data == "bet_1" or data == "bet_2":
            context.user_data['bet_choice'] = data
            await query.edit_message_text(
                "💰 مقدار طلای شرط را وارد کنید (حداقل ۱۰۰):",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 انصراف", callback_data="betting")]])
            )
            context.user_data['waiting_for'] = 'bet_amount'
    
    elif data == "clans":
        await clans(update, context)
    
    elif data == "clan_create":
        await clan_create(update, context)
    
    elif data == "clan_list":
        await clan_list(update, context)
    
    elif data == "clan_members":
        await clan_members(update, context)
    
    elif data == "clan_leave":
        await clan_leave(update, context)
    
    elif data == "duel":
        await duel(update, context)
    
    elif data == "duel_request":
        await duel_request(update, context)
    
    elif data == "duel_stats":
        await duel_stats(update, context)
    
    elif data == "stock_market":
        await stock_market(update, context)
    
    elif data.startswith("stock_"):
        npc_id = int(data.split("_")[1])
        user_id = query.from_user.id
        user = get_user(user_id)
        npc = get_npc_by_id(npc_id)
        if not npc:
            await query.edit_message_text("❌ کشور یافت نشد!")
            return
        keyboard = [
            [InlineKeyboardButton("💰 خرید سهام", callback_data=f"stock_buy_{npc_id}")],
            [InlineKeyboardButton("💰 فروش سهام", callback_data=f"stock_sell_{npc_id}")],
            [InlineKeyboardButton("🔙 بازگشت", callback_data="stock_market")]
        ]
        await query.edit_message_text(
            f"📈 {npc['name']}\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"💰 قیمت سهام: {npc['share_price']}\n"
            f"💰 طلای شما: {user['gold']:,}\n"
            f"📊 سهام شما: {user.get('shares', {}).get(npc['name'], 0)}",
            reply_markup=InlineKeyboardMarkup(keyboard)
        )
    
    elif data.startswith("stock_buy_"):
        npc_id = int(data.split("_")[2])
        await stock_buy(update, context, npc_id)
    
    elif data.startswith("stock_sell_"):
        npc_id = int(data.split("_")[2])
        await stock_sell(update, context, npc_id)
    
    elif data == "daily_mission":
        await daily_mission(update, context)
    
    elif data == "daily_gift":
        await daily_gift(update, context)
    
    elif data == "group_contest":
        await group_contest(update, context)
    
    elif data == "contest_join":
        await contest_join(update, context)
    
    elif data == "contest_rules":
        await contest_rules(update, context)
    
    elif data == "official_statement":
        await official_statement(update, context)
    
    elif data == "set_statement":
        await set_statement(update, context)
    
    elif data == "subsidiary":
        await subsidiary(update, context)
    
    elif data == "set_subsidiary":
        await set_subsidiary(update, context)
    
    elif data == "war_rules":
        await war_rules(update, context)
    
    elif data == "secret_chat":
        await secret_chat(update, context)
    
    elif data == "send_secret":
        await send_secret(update, context)
    
    elif data == "global_event":
        await global_event(update, context)
    
    elif data == "observer":
        await observer(update, context)
    
    elif data == "admin":
        await admin_panel(update, context)
    
    elif data == "admin_stats":
        await admin_stats(update, context)
    
    elif data == "admin_users":
        await admin_users(update, context)
    
    elif data == "admin_npcs":
        await admin_npcs(update, context)
    
    elif data == "admin_logs":
        await admin_logs(update, context)
    
    elif data == "admin_add_gold":
        await admin_add_gold(update, context)
    
    elif data == "admin_add_oil":
        await admin_add_oil(update, context)
    
    elif data == "admin_vip":
        await admin_vip(update, context)
    
    elif data == "admin_ban":
        await admin_ban(update, context)
    
    elif data == "admin_add_country":
        await admin_add_country(update, context)
    
    else:
        await query.edit_message_text(
            "❌ این گزینه در حال توسعه است.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="menu")]])
        )

async def text_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    waiting = context.user_data.get('waiting_for')
    
    if waiting == 'transfer_amount':
        await receive_transfer_amount(update, context)
    
    elif waiting == 'statement':
        await receive_statement(update, context)
    
    elif waiting == 'subsidiary':
        await receive_subsidiary(update, context)
    
    elif waiting == 'secret':
        await receive_secret(update, context)
    
    elif waiting == 'bet_amount':
        await receive_bet_amount(update, context)
    
    elif waiting == 'clan_name':
        await receive_clan_name(update, context)
    
    elif waiting == 'duel_opponent':
        await receive_duel_opponent(update, context)
    
    elif waiting in ['admin_gold', 'admin_oil', 'admin_vip', 'admin_ban', 'admin_add_country']:
        await admin_text_handler(update, context)
    
    else:
        await update.message.reply_text("برای دیدن منو /start را بزنید.")

def main():
    app = Application.builder().token(TOKEN).build()
    
    conv_handler = ConversationHandler(
        entry_points=[CommandHandler("start", start)],
        states={
            COUNTRY_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, receive_country_name)],
        },
        fallbacks=[CommandHandler("cancel", cancel)]
    )
    app.add_handler(conv_handler)
    
    app.add_handler(CommandHandler("attack", group_attack))
    app.add_handler(CommandHandler("profile", group_profile))
    app.add_handler(CommandHandler("rank", group_rank))
    app.add_handler(CommandHandler("admin", admin_panel))
    
    app.add_handler(CallbackQueryHandler(button_handler))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, text_handler))
    
    app.run_polling()

if __name__ == "__main__":
    main()
