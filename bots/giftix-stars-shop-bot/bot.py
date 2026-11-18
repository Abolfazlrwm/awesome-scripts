# Telegram: @Abolfazl_rwm
# ============================================================
#    GiftIx -  
# ============================================================

import logging
import random
import string
import json
import os
import asyncio
from datetime import datetime, timedelta
from typing import Dict, Any, Optional

# ==========   ==========
from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    ReplyKeyboardMarkup,
    KeyboardButton,
)
from telegram.ext import (
    Application,
    CommandHandler,
    CallbackQueryHandler,
    MessageHandler,
    filters,
    ContextTypes,
    ConversationHandler,
)
from telegram.error import TelegramError

# ==========  ==========
from sqlalchemy import (
    create_engine,
    Column,
    Integer,
    String,
    Float,
    Boolean,
    DateTime,
    Text,
    ForeignKey,
    JSON,
)
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship
import pytz

# ==========   (  ) ==========
BOT_TOKEN = os.environ.get("BOT_TOKEN", "")
if not BOT_TOKEN:
    raise SystemExit("CRITICAL: BOT_TOKEN env var is not set. Get one from @BotFather.")          #  @BotFather 
BOT_USERNAME = "GiftIx1bot"            #  @

#  (  )
ADMINS = {
    123456789: "owner",   #  
    987654321: "admin",   #  
}

#  
REQUIRED_CHANNELS = ["GiftIx_1"]        #      
REPORTS_CHANNEL = "GiftIx1_Reports"    #    

#  (SQLite)
DATABASE_URL = "sqlite:///giftix.db"

#  
DAILY_DEPOSIT_LIMIT = 900_000           #      ()

#   ()
BASE_PRICES = {
    "ton": 298_225,
    "stars": 2_637,
    "gift_star": 3_514,
    "premium": {"3m": 1_577_000, "6m": 2_560_000, "12m": 4_460_000},
}
PROFIT_PERCENT = {"ton": 10, "stars": 8, "gift": 21, "premium": 11}

#   (/)
PAYMENT_GATEWAYS = {
    "zibal": {"enabled": True, "merchant": "zibal_merchant_id"},
    "zarinpal": {"enabled": True, "merchant": "zarinpal_merchant_id"},
    "aqayepardakht": {"enabled": True, "api_key": "aqayepardakht_api_key"},
}

#  
REFERRAL_COMMISSION = 5   # 
REFERRAL_BONUS = 1_000    #    ()

#  (  )
DEFAULT_LANG = "en"  # رشته‌های fa/ar در فایل اصلی خراب شده بودند؛ با TEXTS["fa"] دوباره فارسی‌سازی کنید

# ==========   ==========
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)
logger = logging.getLogger(__name__)

# ==========  ==========
TEHRAN_TZ = pytz.timezone("Asia/Tehran")

def now_tehran():
    return datetime.now(TEHRAN_TZ)

engine = create_engine(DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(bind=engine)
Base = declarative_base()

# ----------  ----------
class User(Base):
    __tablename__ = "users"
    id = Column(Integer, primary_key=True)
    telegram_id = Column(Integer, unique=True, index=True)
    username = Column(String, nullable=True)
    full_name = Column(String)
    language = Column(String, default="fa")
    balance = Column(Integer, default=0)
    discount_balance = Column(Integer, default=0)
    referral_code = Column(String, unique=True, index=True)
    referrer_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    level = Column(Integer, default=0)
    registered_at = Column(DateTime, default=now_tehran)
    is_active = Column(Boolean, default=True)
    is_banned = Column(Boolean, default=False)
    daily_deposit_used = Column(Integer, default=0)
    daily_deposit_date = Column(String, default="")

    referrer = relationship("User", remote_side=[id], backref="referrals")
    orders = relationship("Order", back_populates="user")
    transactions = relationship("Transaction", back_populates="user")
    tickets = relationship("Ticket", back_populates="user")

class Order(Base):
    __tablename__ = "orders"
    id = Column(Integer, primary_key=True)
    order_id = Column(String, unique=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    product_type = Column(String)
    product_detail = Column(JSON)
    price = Column(Integer)
    final_price = Column(Integer)
    status = Column(String, default="pending")
    wallet_address = Column(String, nullable=True)
    memo = Column(String, nullable=True)
    created_at = Column(DateTime, default=now_tehran)
    updated_at = Column(DateTime, default=now_tehran, onupdate=now_tehran)
    admin_note = Column(Text, nullable=True)

    user = relationship("User", back_populates="orders")

class Transaction(Base):
    __tablename__ = "transactions"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    amount = Column(Integer)
    type = Column(String)
    description = Column(String, nullable=True)
    reference_id = Column(String, nullable=True)
    status = Column(String, default="pending")
    created_at = Column(DateTime, default=now_tehran)

    user = relationship("User", back_populates="transactions")

class Ticket(Base):
    __tablename__ = "tickets"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    subject = Column(String)
    body = Column(Text)
    status = Column(String, default="open")
    priority = Column(String, default="normal")
    admin_response = Column(Text, nullable=True)
    created_at = Column(DateTime, default=now_tehran)
    updated_at = Column(DateTime, default=now_tehran, onupdate=now_tehran)

    user = relationship("User", back_populates="tickets")

class AdminLog(Base):
    __tablename__ = "admin_logs"
    id = Column(Integer, primary_key=True)
    admin_id = Column(Integer)
    action = Column(String)
    target_id = Column(Integer, nullable=True)
    details = Column(JSON)
    created_at = Column(DateTime, default=now_tehran)

class Backup(Base):
    __tablename__ = "backups"
    id = Column(Integer, primary_key=True)
    filename = Column(String)
    size = Column(Integer)
    created_at = Column(DateTime, default=now_tehran)

Base.metadata.create_all(engine)

# ----------   ----------
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

def get_user(db, telegram_id):
    return db.query(User).filter(User.telegram_id == telegram_id).first()

def create_user(db, telegram_id, username, full_name, referrer_id=None):
    ref_code = ''.join(random.choices(string.ascii_uppercase + string.digits, k=8))
    user = User(
        telegram_id=telegram_id,
        username=username,
        full_name=full_name,
        referral_code=ref_code,
        referrer_id=referrer_id,
    )
    db.add(user)
    db.commit()
    db.refresh(user)
    return user

def get_or_create_user(db, telegram_id, username, full_name):
    user = get_user(db, telegram_id)
    if not user:
        user = create_user(db, telegram_id, username, full_name)
    return user

# ==========   ( ) ==========
TEXTS = {
    "en": {
        "welcome": " Welcome to GiftIx Store Bot!",
        "products_title": " Choose your product:",
        "stars_buy_title": " Buy Telegram Stars",
        "premium_title": " Telegram Premium",
        "gift_list_title": " Send a gift on Telegram!",
        "ton_buy_title": " Buy TON coin",
        "order_success": " Order registered!\n Tracking ID: {order_id}",
        "order_done": " Order completed ",
        "admin_panel": " Admin Panel\n\nWelcome {admin_name}",
        "backup_done": " Backup at {time} - Size: {size}",
        "weekly_report": " Weekly Report\n\n{stats}",
        "survey_ask": " Rate your purchase (1-5):",
        "survey_thanks": " Thank you for your feedback!",
    },
    "ar": {
        "welcome": "      GiftIx!",
        "products_title": "   :",
        "stars_buy_title": "   ",
        "premium_title": "  ",
        "gift_list_title": "    !",
        "ton_buy_title": "   TON",
        "order_success": "   !\n  : {order_id}",
        "order_done": "    ",
        "admin_panel": "  \n\n {admin_name}",
        "backup_done": "    {time} - : {size}",
        "weekly_report": "  \n\n{stats}",
        "survey_ask": "     (1-5):",
        "survey_thanks": "  !",
    }
}

def get_text(key, lang="fa", **kwargs):
    lang = lang if lang in TEXTS else "en"
    text = TEXTS.get(lang, {}).get(key, key)
    if kwargs:
        try:
            text = text.format(**kwargs)
        except:
            pass
    return text

# ==========   ==========
def fmt(n: int) -> str:
    return f"{n:,}"

def today_str():
    return now_tehran().strftime("%Y-%m-%d")

def generate_order_id():
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=10))

def glass_button(text, callback_data, color="blue"):
    emoji = {"blue": "", "green": "", "red": "", "purple": "", "orange": "", "yellow": "", "gray": ""}
    return InlineKeyboardButton(f"{emoji.get(color, '')} {text}", callback_data=callback_data)

def product_keyboard(lang="fa"):
    return InlineKeyboardMarkup([
        [glass_button(" ", "buy_stars", "blue")],
        [glass_button(" ", "buy_premium", "purple")],
        [glass_button(" ", "buy_gift", "green")],
        [glass_button(" ", "buy_ton", "orange")],
        [glass_button("  ", "wallet", "yellow")],
        [glass_button("  ", "track_order", "red")],
        [glass_button(" ", "support", "green")],
        [glass_button(" ", "settings", "gray")],
    ])

def admin_keyboard(lang="fa"):
    return InlineKeyboardMarkup([
        [glass_button("  ", "admin_stats", "blue")],
        [glass_button(" ", "admin_users", "green")],
        [glass_button(" ", "admin_orders", "orange")],
        [glass_button(" ", "admin_transactions", "purple")],
        [glass_button(" ", "admin_tickets", "red")],
        [glass_button("  ", "admin_price", "yellow")],
        [glass_button(" ", "admin_broadcast", "blue")],
        [glass_button("  ", "admin_backup", "green")],
        [glass_button(" /", "admin_toggle", "red")],
    ])

# ==========  ==========
def get_price(product_type, detail=None):
    base = BASE_PRICES.get(product_type, 0)
    if product_type == "premium" and detail:
        base = BASE_PRICES["premium"].get(detail, 0)
    profit = PROFIT_PERCENT.get(product_type, 0)
    return round(base * (1 + profit / 100))

# ==========   (   ) ==========
user_temp = {}  # {user_id: {"step": "...", "data": {...}}}
pending_orders = {}  # {user_id: order_data}

# ==========  ==========

# -----    -----
async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    lang = context.user_data.get("lang", "fa")
    
    db = next(get_db())
    db_user = get_or_create_user(db, user.id, user.username, user.full_name)
    context.user_data["user_id"] = user.id

    #    
    not_member = []
    for ch in REQUIRED_CHANNELS:
        try:
            member = await context.bot.get_chat_member(f"@{ch}", user.id)
            if member.status not in ("member", "administrator", "creator"):
                not_member.append(ch)
        except:
            not_member.append(ch)
    
    if not_member:
        keyboard = []
        for ch in not_member:
            keyboard.append([InlineKeyboardButton(f"   {ch}", url=f"https://t.me/{ch}")])
        keyboard.append([InlineKeyboardButton("     ", callback_data="check_join")])
        await update.message.reply_text(
            "          :",
            reply_markup=InlineKeyboardMarkup(keyboard)
        )
        return

    await update.message.reply_text(
        get_text("welcome", lang),
        reply_markup=product_keyboard(lang)
    )

async def join_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user = query.from_user
    lang = context.user_data.get("lang", "fa")
    
    not_member = []
    for ch in REQUIRED_CHANNELS:
        try:
            member = await context.bot.get_chat_member(f"@{ch}", user.id)
            if member.status not in ("member", "administrator", "creator"):
                not_member.append(ch)
        except:
            not_member.append(ch)
    
    if not_member:
        keyboard = []
        for ch in not_member:
            keyboard.append([InlineKeyboardButton(f"   {ch}", url=f"https://t.me/{ch}")])
        keyboard.append([InlineKeyboardButton("   ", callback_data="check_join")])
        await query.edit_message_text(
            "      !",
            reply_markup=InlineKeyboardMarkup(keyboard)
        )
        return
    
    await query.edit_message_text(
        "    !",
        reply_markup=product_keyboard(lang)
    )

# -----   -----
async def products_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    await query.edit_message_text(
        get_text("products_title", lang),
        reply_markup=InlineKeyboardMarkup([
            [glass_button(" ", "buy_stars", "blue")],
            [glass_button(" ", "buy_premium", "purple")],
            [glass_button(" ", "buy_gift", "green")],
            [glass_button(" ", "buy_ton", "orange")],
            [glass_button(" ", "main_menu", "gray")],
        ])
    )

async def main_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    await query.edit_message_text(
        get_text("welcome", lang),
        reply_markup=product_keyboard(lang)
    )

# -----   -----
async def buy_stars(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    user_id = query.from_user.id
    
    price = get_price("stars")
    context.user_data["buying"] = "stars"
    context.user_data["price_per_star"] = price
    
    await query.edit_message_text(
        f"{get_text('stars_buy_title', lang)}\n\n   : {fmt(price)} \n  :  \n\n       :",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="products")],
        ])
    )
    context.user_data["step"] = "stars_count"

# -----   -----
async def buy_premium(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    
    keyboard = [
        [glass_button(" ", "premium_3m", "blue")],
        [glass_button(" ", "premium_6m", "green")],
        [glass_button(" ", "premium_12m", "purple")],
        [glass_button(" ", "products")],
    ]
    await query.edit_message_text(
        get_text("premium_title", lang),
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def premium_select(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    plan = query.data.split("_")[1]  # 3m, 6m, 12m
    context.user_data["premium_plan"] = plan
    context.user_data["buying"] = "premium"
    context.user_data["step"] = "premium_username"
    
    await query.edit_message_text(
        f"        ( @   @):\n\n: @Abolfazl_rwm",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="products")],
        ])
    )

# -----   -----
async def buy_gift(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    
    gifts = {
        "teddy": " ",
        "heart": " ",
        "rose": "  ",
        "kado": " ",
        "rocket": " ",
        "trophy": " ",
        "diamond": " ",
    }
    keyboard = []
    for key, name in gifts.items():
        price = get_price("gift_star") * 15 if key in ["teddy", "heart"] else get_price("gift_star") * 25
        keyboard.append([glass_button(f"{name} - {fmt(price)} ", f"gift_{key}", "green")])
    keyboard.append([glass_button(" ", "products", "gray")])
    
    await query.edit_message_text(
        get_text("gift_list_title", lang),
        reply_markup=InlineKeyboardMarkup(keyboard)
    )

async def gift_select(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    gift_key = query.data.split("_")[1]
    context.user_data["gift_key"] = gift_key
    context.user_data["buying"] = "gift"
    context.user_data["step"] = "gift_username"
    
    await query.edit_message_text(
        f"      :",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="products")],
        ])
    )

# -----   -----
async def buy_ton(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    
    price = get_price("ton")
    await query.edit_message_text(
        f"{get_text('ton_buy_title', lang)}\n\n   TON: {fmt(price)} \n  : . TON\n\n  TON     :",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="products")],
        ])
    )
    context.user_data["buying"] = "ton"
    context.user_data["price_per_ton"] = price
    context.user_data["step"] = "ton_amount"

# -----   -----
async def wallet_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    lang = context.user_data.get("lang", "fa")
    
    db = next(get_db())
    user = get_user(db, user_id)
    if not user:
        await query.edit_message_text("   !")
        return
    
    await query.edit_message_text(
        f" **  **\n\n"
        f" : {fmt(user.balance)} \n"
        f" : {fmt(user.discount_balance)} \n"
        f" : {user.level}\n"
        f"  : {len(user.referrals)}\n\n"
        f"   : `{user.referral_code}`\n"
        f"  : https://t.me/{BOT_USERNAME}?start=ref_{user.referral_code}",
        parse_mode="Markdown",
        reply_markup=InlineKeyboardMarkup([
            [glass_button("  ", "deposit", "green")],
            [glass_button("  ", "transactions", "blue")],
            [glass_button(" ", "main_menu", "gray")],
        ])
    )

async def deposit_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    user_id = query.from_user.id
    
    #   
    db = next(get_db())
    user = get_user(db, user_id)
    today = today_str()
    used = user.daily_deposit_used if user.daily_deposit_date == today else 0
    remaining = DAILY_DEPOSIT_LIMIT - used
    
    await query.edit_message_text(
        f" ** **\n\n"
        f"   : {fmt(DAILY_DEPOSIT_LIMIT)} \n"
        f"  : {fmt(remaining)} \n\n"
        f"        :",
        parse_mode="Markdown",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="wallet")],
        ])
    )
    context.user_data["step"] = "deposit_amount"

# -----   -----
async def track_order_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    
    await query.edit_message_text(
        " **      :**",
        parse_mode="Markdown",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="main_menu")],
        ])
    )
    context.user_data["step"] = "track_order"

# -----  -----
async def support_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    lang = context.user_data.get("lang", "fa")
    
    await query.edit_message_text(
        " ****\n\n"
        "       .\n\n"
        "        .",
        parse_mode="Markdown",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="main_menu")],
        ])
    )
    context.user_data["step"] = "support_message"

# ==========    ==========
async def text_input_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    text = update.message.text.strip()
    step = context.user_data.get("step")
    lang = context.user_data.get("lang", "fa")
    user_id = user.id
    
    if step == "stars_count":
        try:
            count = int(text)
            if count < 50:
                await update.message.reply_text("     .")
                return
            price = context.user_data.get("price_per_star", get_price("stars"))
            total = count * price
            
            #  
            db = next(get_db())
            db_user = get_user(db, user_id)
            if db_user.balance < total:
                await update.message.reply_text(f"      !\n : {fmt(db_user.balance)} \n   : {fmt(total)} ")
                return
            
            #  
            order_id = generate_order_id()
            order = Order(
                order_id=order_id,
                user_id=db_user.id,
                product_type="stars",
                product_detail={"count": count, "price_per": price},
                price=total,
                final_price=total,
                status="pending",
            )
            db.add(order)
            db_user.balance -= total
            db.commit()
            
            await update.message.reply_text(
                f"    !\n\n"
                f" : {count} \n"
                f" : {fmt(total)} \n"
                f"  : {order_id}\n\n"
                f"     ...",
                reply_markup=product_keyboard(lang)
            )
            context.user_data["step"] = None
            
            #   
            for admin_id in ADMINS:
                try:
                    await context.bot.send_message(
                        admin_id,
                        f"   \n\n"
                        f" : {user.full_name} (@{user.username})\n"
                        f" : {user_id}\n"
                        f" : {count}\n"
                        f" : {fmt(total)} \n"
                        f" : {order_id}"
                    )
                except:
                    pass
                    
        except ValueError:
            await update.message.reply_text("      .")

    elif step == "deposit_amount":
        try:
            amount = int(text)
            if amount <= 0:
                await update.message.reply_text("      .")
                return
            
            #   
            db = next(get_db())
            user_db = get_user(db, user_id)
            today = today_str()
            used = user_db.daily_deposit_used if user_db.daily_deposit_date == today else 0
            if used + amount > DAILY_DEPOSIT_LIMIT:
                remaining = DAILY_DEPOSIT_LIMIT - used
                await update.message.reply_text(f"    {fmt(DAILY_DEPOSIT_LIMIT)}  .\n: {fmt(remaining)} ")
                return
            
            #   
            context.user_data["deposit_amount"] = amount
            context.user_data["step"] = "deposit_receipt"
            
            await update.message.reply_text(
                f"  {fmt(amount)} \n\n"
                f"          .\n"
                f"  : `6037-9918-1234-5678`\n"
                f"  : ` `",
                parse_mode="Markdown"
            )
        except ValueError:
            await update.message.reply_text("      .")

    elif step == "deposit_receipt":
        if update.message.photo:
            #  
            photo = update.message.photo[-1].file_id
            amount = context.user_data.get("deposit_amount", 0)
            
            #    
            db = next(get_db())
            user_db = get_user(db, user_id)
            trans = Transaction(
                user_id=user_db.id,
                amount=amount,
                type="deposit",
                status="pending",
                description=f"  {amount} ",
            )
            db.add(trans)
            db.commit()
            
            #   
            for admin_id in ADMINS:
                try:
                    await context.bot.send_photo(
                        admin_id,
                        photo,
                        caption=f"   \n\n"
                                f" : {user.full_name} (@{user.username})\n"
                                f" : {user_id}\n"
                                f" : {fmt(amount)} \n"
                                f" : {trans.id}\n\n"
                                f" : /confirm_{trans.id}\n"
                                f" : /reject_{trans.id}"
                    )
                except:
                    pass
            
            await update.message.reply_text(
                "     .\n"
                "       .",
                reply_markup=product_keyboard(lang)
            )
            context.user_data["step"] = None
        else:
            await update.message.reply_text("       .")

    elif step == "track_order":
        db = next(get_db())
        order = db.query(Order).filter(Order.order_id == text).first()
        if not order:
            await update.message.reply_text("      .")
            return
        
        status_map = {
            "pending": "   ",
            "paid": "  ",
            "processing": "   ",
            "done": "  ",
            "cancelled": "  ",
        }
        await update.message.reply_text(
            f" ** **\n\n"
            f" : {order.order_id}\n"
            f" : {order.product_type}\n"
            f" : {fmt(order.price)} \n"
            f" : {status_map.get(order.status, order.status)}\n"
            f" : {order.created_at.strftime('%Y-%m-%d %H:%M')}",
            parse_mode="Markdown",
            reply_markup=product_keyboard(lang)
        )
        context.user_data["step"] = None

    elif step == "support_message":
        db = next(get_db())
        user_db = get_user(db, user_id)
        ticket = Ticket(
            user_id=user_db.id,
            subject=" ",
            body=text,
            status="open",
        )
        db.add(ticket)
        db.commit()
        
        #   
        for admin_id in ADMINS:
            try:
                await context.bot.send_message(
                    admin_id,
                    f"  \n\n"
                    f" : {user.full_name} (@{user.username})\n"
                    f" : {user_id}\n"
                    f" : {text}\n"
                    f" : {ticket.id}"
                )
            except:
                pass
        
        await update.message.reply_text(
            "      .\n"
            "     .",
            reply_markup=product_keyboard(lang)
        )
        context.user_data["step"] = None

    elif step == "premium_username":
        username = text.replace("@", "").strip()
        if not username:
            await update.message.reply_text("    .")
            return
        context.user_data["premium_username"] = username
        context.user_data["step"] = "premium_confirm"
        
        plan = context.user_data.get("premium_plan", "3m")
        price = get_price("premium", plan)
        
        await update.message.reply_text(
            f" : @{username}\n"
            f" : {plan}\n"
            f" : {fmt(price)} \n\n"
            f"    \n"
            f"      .",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton(" ", callback_data="premium_confirm_yes")],
                [InlineKeyboardButton(" ", callback_data="products")],
            ])
        )

    elif step == "ton_amount":
        try:
            amount = float(text)
            if amount < 0.1:
                await update.message.reply_text("   . TON .")
                return
            price = context.user_data.get("price_per_ton", get_price("ton"))
            total = int(amount * price)
            
            context.user_data["ton_amount"] = amount
            context.user_data["ton_total"] = total
            context.user_data["step"] = "ton_wallet"
            
            await update.message.reply_text(
                f" : {amount} TON\n"
                f" : {fmt(total)} \n\n"
                f"       :"
            )
        except ValueError:
            await update.message.reply_text("      .")

    elif step == "ton_wallet":
        wallet = text.strip()
        if not wallet or len(wallet) < 20:
            await update.message.reply_text("    .")
            return
        context.user_data["ton_wallet"] = wallet
        context.user_data["step"] = "ton_memo"
        
        await update.message.reply_text(
            "     ()  \n"
            "     .     ��  ."
        )

    elif step == "ton_memo":
        memo = text if text.lower() != "" else None
        amount = context.user_data.get("ton_amount", 0)
        total = context.user_data.get("ton_total", 0)
        wallet = context.user_data.get("ton_wallet", "")
        
        #  
        db = next(get_db())
        user_db = get_user(db, user_id)
        if user_db.balance < total:
            await update.message.reply_text(f"      !\n : {fmt(user_db.balance)} ")
            return
        
        order_id = generate_order_id()
        order = Order(
            order_id=order_id,
            user_id=user_db.id,
            product_type="ton",
            product_detail={"amount": amount, "wallet": wallet, "memo": memo},
            price=total,
            final_price=total,
            status="pending",
            wallet_address=wallet,
            memo=memo,
        )
        db.add(order)
        user_db.balance -= total
        db.commit()
        
        await update.message.reply_text(
            f"    !\n\n"
            f" : {amount} TON\n"
            f" : {fmt(total)} \n"
            f"  : {order_id}\n\n"
            f"     ...",
            reply_markup=product_keyboard(lang)
        )
        context.user_data["step"] = None

    elif step == "gift_username":
        username = text.replace("@", "").strip()
        if not username:
            await update.message.reply_text("    .")
            return
        context.user_data["gift_username"] = username
        context.user_data["step"] = "gift_confirm"
        
        gift_key = context.user_data.get("gift_key", "teddy")
        price = get_price("gift_star") * 15 if gift_key in ["teddy", "heart"] else get_price("gift_star") * 25
        
        await update.message.reply_text(
            f" : {gift_key}\n"
            f" : @{username}\n"
            f" : {fmt(price)} \n\n"
            f"       .",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton(" ", callback_data="gift_confirm_yes")],
                [InlineKeyboardButton(" ", callback_data="products")],
            ])
        )

# ==========   ==========
async def callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data
    user_id = query.from_user.id
    lang = context.user_data.get("lang", "fa")
    
    if data == "premium_confirm_yes":
        plan = context.user_data.get("premium_plan", "3m")
        username = context.user_data.get("premium_username", "")
        price = get_price("premium", plan)
        
        db = next(get_db())
        user_db = get_user(db, user_id)
        if user_db.balance < price:
            await query.edit_message_text(f"      !\n : {fmt(user_db.balance)} ")
            return
        
        order_id = generate_order_id()
        order = Order(
            order_id=order_id,
            user_id=user_db.id,
            product_type="premium",
            product_detail={"plan": plan, "username": username},
            price=price,
            final_price=price,
            status="pending",
        )
        db.add(order)
        user_db.balance -= price
        db.commit()
        
        await query.edit_message_text(
            f"    !\n\n"
            f" : {plan}\n"
            f" : @{username}\n"
            f" : {fmt(price)} \n"
            f"  : {order_id}",
            reply_markup=product_keyboard(lang)
        )
        context.user_data["step"] = None

    elif data == "gift_confirm_yes":
        gift_key = context.user_data.get("gift_key", "teddy")
        username = context.user_data.get("gift_username", "")
        price = get_price("gift_star") * 15 if gift_key in ["teddy", "heart"] else get_price("gift_star") * 25
        
        db = next(get_db())
        user_db = get_user(db, user_id)
        if user_db.balance < price:
            await query.edit_message_text(f"      !")
            return
        
        order_id = generate_order_id()
        order = Order(
            order_id=order_id,
            user_id=user_db.id,
            product_type="gift",
            product_detail={"gift": gift_key, "username": username},
            price=price,
            final_price=price,
            status="pending",
        )
        db.add(order)
        user_db.balance -= price
        db.commit()
        
        await query.edit_message_text(
            f"   !\n\n"
            f" : {gift_key}\n"
            f" : @{username}\n"
            f" : {fmt(price)} \n"
            f" : {order_id}",
            reply_markup=product_keyboard(lang)
        )
        context.user_data["step"] = None

# ==========   ==========
async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    
    if user_id not in ADMINS:
        await query.edit_message_text("    .")
        return
    
    lang = context.user_data.get("lang", "fa")
    admin_name = query.from_user.full_name
    await query.edit_message_text(
        get_text("admin_panel", lang, admin_name=admin_name),
        reply_markup=admin_keyboard(lang)
    )

async def admin_stats_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    if user_id not in ADMINS:
        return
    
    db = next(get_db())
    total_users = db.query(User).count()
    total_orders = db.query(Order).count()
    orders_done = db.query(Order).filter(Order.status == "done").count()
    total_revenue = db.query(Order).filter(Order.status == "done").with_entities(Order.final_price).all()
    total_revenue = sum(r[0] for r in total_revenue) if total_revenue else 0
    
    text = f"""
 ** **

  : {total_users}
  : {total_orders}
 : {orders_done}
  : {fmt(total_revenue)} 
    """
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup([
        [InlineKeyboardButton(" ", callback_data="admin_panel")],
    ]))

async def admin_broadcast_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    if user_id not in ADMINS:
        return
    await query.edit_message_text("      :")
    context.user_data["broadcast_step"] = 1

async def admin_broadcast_send(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    if user_id not in ADMINS:
        return
    text = update.message.text
    db = next(get_db())
    users = db.query(User).all()
    success = 0
    for user in users:
        try:
            await context.bot.send_message(user.telegram_id, text)
            success += 1
        except:
            pass
    await update.message.reply_text(f"   .\n: {success}")
    context.user_data["broadcast_step"] = None

async def admin_backup_now(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    if user_id not in ADMINS:
        return
    
    await query.edit_message_text("    ...")
    
    #    
    import shutil
    backup_dir = "backups"
    os.makedirs(backup_dir, exist_ok=True)
    filename = f"{backup_dir}/backup_{now_tehran().strftime('%Y%m%d_%H%M%S')}.db"
    shutil.copy2("giftix.db", filename)
    
    #    
    try:
        with open(filename, "rb") as f:
            await context.bot.send_document(
                chat_id=f"@{REPORTS_CHANNEL}",
                document=f,
                caption=f"   {now_tehran().strftime('%Y-%m-%d %H:%M')}"
            )
    except:
        pass
    
    await query.edit_message_text(
        "     .",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton(" ", callback_data="admin_panel")],
        ])
    )

# ==========   ==========
async def schedule_backup(bot):
    """      """
    while True:
        await asyncio.sleep(24 * 3600)
        try:
            import shutil
            os.makedirs("backups", exist_ok=True)
            filename = f"backups/backup_{now_tehran().strftime('%Y%m%d_%H%M%S')}.db"
            shutil.copy2("giftix.db", filename)
            
            with open(filename, "rb") as f:
                await bot.send_document(
                    chat_id=f"@{REPORTS_CHANNEL}",
                    document=f,
                    caption=f"   - {now_tehran().strftime('%Y-%m-%d %H:%M')}"
                )
        except Exception as e:
            logger.error(f"  : {e}")

# ==========   ==========
def main():
    app = Application.builder().token(BOT_TOKEN).build()
    
    #  
    app.add_handler(CommandHandler("start", start_command))
    app.add_handler(CallbackQueryHandler(join_handler, pattern="check_join"))
    app.add_handler(CallbackQueryHandler(main_menu, pattern="main_menu"))
    app.add_handler(CallbackQueryHandler(products_handler, pattern="products"))
    app.add_handler(CallbackQueryHandler(buy_stars, pattern="buy_stars"))
    app.add_handler(CallbackQueryHandler(buy_premium, pattern="buy_premium"))
    app.add_handler(CallbackQueryHandler(premium_select, pattern="premium_"))
    app.add_handler(CallbackQueryHandler(buy_gift, pattern="buy_gift"))
    app.add_handler(CallbackQueryHandler(gift_select, pattern="gift_"))
    app.add_handler(CallbackQueryHandler(buy_ton, pattern="buy_ton"))
    app.add_handler(CallbackQueryHandler(wallet_handler, pattern="wallet"))
    app.add_handler(CallbackQueryHandler(deposit_handler, pattern="deposit"))
    app.add_handler(CallbackQueryHandler(track_order_handler, pattern="track_order"))
    app.add_handler(CallbackQueryHandler(support_handler, pattern="support"))
    app.add_handler(CallbackQueryHandler(admin_panel, pattern="admin_panel"))
    app.add_handler(CallbackQueryHandler(admin_stats_callback, pattern="admin_stats"))
    app.add_handler(CallbackQueryHandler(admin_backup_now, pattern="admin_backup"))
    app.add_handler(CallbackQueryHandler(callback_handler, pattern="premium_confirm_yes"))
    app.add_handler(CallbackQueryHandler(callback_handler, pattern="gift_confirm_yes"))
    
    # 
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, text_input_handler))
    
    #   
    loop = asyncio.get_event_loop()
    loop.create_task(schedule_backup(app.bot))
    
    # 
    print("  GiftIx  !")
    app.run_polling()

if __name__ == "__main__":
    main()
