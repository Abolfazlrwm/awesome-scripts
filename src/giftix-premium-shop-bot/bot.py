# -*- coding: utf-8 -*-
# ============================================================
#  GiftiX Premium Shop Bot — فروشگاه استارز/پریمیوم/گیفت/تون
#  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
#  Repo: https://github.com/Abolfazlrwm/awesome-scripts
# ============================================================
import itertools
import json
import logging
import os
import random
from datetime import datetime, time as dt_time
from zoneinfo import ZoneInfo

try:
    import httpx
    HAS_HTTPX = True
except ImportError:
    HAS_HTTPX = False
    logging.warning("httpx یافت نشد. نصب کن: pip install httpx")

try:
    import jdatetime
    HAS_JDATETIME = True
except ImportError:
    HAS_JDATETIME = False

from telegram import (
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    KeyboardButton,
    ReplyKeyboardMarkup,
    Update,
)
from telegram.error import TelegramError
from telegram.ext import (
    Application,
    CallbackQueryHandler,
    CommandHandler,
    ContextTypes,
    MessageHandler,
    filters,
)

logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)

# Load environment variables from .env file (optional)
try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

BOT_TOKEN = os.environ.get("BOT_TOKEN", "token")      # یا در .env
ADMIN_CHAT_ID = int(os.environ.get("ADMIN_CHAT_ID", "0") or 0)  # آیدی عددی ادمین  

REQUIRED_CHANNEL = "Abolfazl_rwm"            
                                         
REPORTS_CHANNEL = "Abolfazl_rwm_Reports"     
                                         
BOT_USERNAME = "AbolfazlShopBot"            

try:
    DATA_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "giftix_data.json")
except NameError:
    DATA_FILE = os.path.join(os.path.expanduser("~"), "giftix_data.json")

CARD_NUMBER = "00000000000"
CARD_HOLDER = "name"

TON_PRICE = 298225        
MIN_TON = 0.1             

PREMIUM_PRICES = {
    "3": 1750000,
    "6": 2841000,
    "12": 4950000,
}

DAILY_LIMIT = 900000      

try:
    TEHRAN_TZ = ZoneInfo("Asia/Tehran")  
except Exception:
    from datetime import timezone, timedelta
    logging.warning(
        "دیتابیس tzdata پیدا نشد؛ از افست ثابت +03:30 استفاده می‌شه. "
        "برای دقت کامل (با احتساب تغییر ساعت رسمی احتمالی) نصب کن: pip install tzdata"
    )
    TEHRAN_TZ = timezone(timedelta(hours=3, minutes=30))

REFERRAL_COMMISSION_PERCENT = 5     
REFERRAL_SIGNUP_BONUS = 1000        
REFERRAL_POINTS_REQUIRED = 7        
REFERRAL_REWARD_GIFTS = ["teddy", "heart"]  

STARS_PRICE = 2847        
STARS_MIN = 50            

GIFT_STAR_PRICE = 3514    

BASE_TON_PRICE    = 298225  
BASE_STARS_PRICE  = 2637    
BASE_PREMIUM_PRICES = {"3": 1577000, "6": 2560000, "12": 4460000}

PROFIT_PERCENT = {
    "ton":      10,   
    "stars":     8,   
    "gift":     21,   
    "premium":  11,   
}

TON_PRICE_API_URL = "https://api.nobitex.ir/market/stats?srcCurrency=ton&dstCurrency=rls"

def recalc_prices() -> None:
    global STARS_PRICE, GIFT_STAR_PRICE, TON_PRICE
    STARS_PRICE   = round(BASE_STARS_PRICE * (1 + PROFIT_PERCENT["stars"] / 100))
    GIFT_STAR_PRICE = round(BASE_STARS_PRICE * (1 + PROFIT_PERCENT["gift"] / 100))
    TON_PRICE     = round(BASE_TON_PRICE * (1 + PROFIT_PERCENT["ton"] / 100))
    for plan, base in BASE_PREMIUM_PRICES.items():
        PREMIUM_PRICES[plan] = round(base * (1 + PROFIT_PERCENT["premium"] / 100))
    
    for key, g in GIFTS.items():
        g["price"] = round(g["stars"] * GIFT_STAR_PRICE)

GIFTS = {
    "teddy":        {"name": "تدی",          "emoji": "🧸",  "stars": 15},
    "heart":        {"name": "قلب",          "emoji": "💗",  "stars": 15},
    "rose":         {"name": "گل رز",        "emoji": "🌹",  "stars": 25},
    "kado":         {"name": "کادو",         "emoji": "🎁",  "stars": 25},
    "rocket":       {"name": "سفینه",        "emoji": "🚀",  "stars": 50},
    "trophy":       {"name": "جام",          "emoji": "🏆",  "stars": 100},
    "champagne":    {"name": "شامپاین",      "emoji": "🍾",  "stars": 50},
    "flowers":      {"name": "گل",           "emoji": "💐",  "stars": 50},
    "cake":         {"name": "کیک",          "emoji": "🎂",  "stars": 50},
    "ring":         {"name": "حلقه",         "emoji": "💍",  "stars": 100},
    "diamond":      {"name": "الماس",        "emoji": "💎",  "stars": 100},
    "heart_val":    {"name": "قلب ولن",      "emoji": "💝",  "stars": 52},
    "bear_val":     {"name": "خرس ولن",      "emoji": "🐻",  "stars": 52},
    "bear_wday":    {"name": "خرس روز زن",   "emoji": "🧸",  "stars": 52},
    "bear_april":   {"name": "خرس 1 اوریل",  "emoji": "🎪",  "stars": 52},
    "bear_xmas":    {"name": "خرس کریسمسی",  "emoji": "🎅",  "stars": 52},
    "bear_patrick": {"name": "خرس پاتریک",   "emoji": "🍀",  "stars": 52},
}

for _g in GIFTS.values():
    _g["price"] = round(_g["stars"] * GIFT_STAR_PRICE)
recalc_prices()

USERS: dict = {}
USER_NAMES: dict = {}               
TOPUP_REQUESTS: dict = {}
ORDERS: dict = {}           
ADMIN_WAITING_REJECT: dict = {}     
SUPPORT_TICKETS: dict = {}          
ADMIN_WAITING_SUPPORT_REPLY: dict = {}  
PENDING_REFERRALS: dict = {}        
BOT_ENABLED = True          
REFERRAL_POINTS_ENABLED = False     
_counter = itertools.count(1)

def get_user(uid: int) -> dict:
    if uid not in USERS:
        USERS[uid] = {
            "balance": 0,
            "discount_balance": 0,
            "score": 0,
            "referrals": 0,
            "referrer_id": None,
            "orders": [],
            "daily_topup_used": 0,
            "daily_topup_date": today_str(),
        }
    return USERS[uid]

async def register_user_and_referral(uid: int, context: ContextTypes.DEFAULT_TYPE) -> None:
    get_user(uid)
    ref_id = PENDING_REFERRALS.pop(uid, None)
    if ref_id and ref_id != uid and not USERS[uid].get("referrer_id"):
        USERS[uid]["referrer_id"] = ref_id
        referrer = get_user(ref_id)
        referrer["referrals"] += 1
        point_awarded = False
        if REFERRAL_POINTS_ENABLED:
            referrer["score"] += 1
            point_awarded = True
        invited_name = USER_NAMES.get(uid, "یک کاربر جدید")
        try:
            await context.bot.send_message(
                ref_id,
                referral_notification_text(
                    invited_name,
                    point_awarded,
                    referrer["referrals"],
                    referrer["discount_balance"],
                    referrer["score"],
                )
            )
        except Exception:
            logging.exception("خطا در ارسال پیام اطلاع‌رسانی رفرال به معرف")
        save_data()  

def fmt(n: int) -> str:
    return f"{n:,}"

def today_str() -> str:
    return datetime.now(TEHRAN_TZ).strftime("%Y-%m-%d")

def get_daily_remaining(uid: int) -> int:
    u = get_user(uid)
    if u.get("daily_topup_date") != today_str():
        u["daily_topup_date"] = today_str()
        u["daily_topup_used"] = 0
    return max(DAILY_LIMIT - u["daily_topup_used"], 0)

def use_daily_limit(uid: int, amount: int) -> None:
    get_daily_remaining(uid)  
    USERS[uid]["daily_topup_used"] += amount

def refund_daily_limit(uid: int, amount: int) -> None:
    u = get_user(uid)
    if u.get("daily_topup_date") == today_str():
        u["daily_topup_used"] = max(u["daily_topup_used"] - amount, 0)

def apply_referral_commission(buyer_id: int, price: int) -> None:
    if price <= 0:
        return
    buyer = get_user(buyer_id)
    referrer_id = buyer.get("referrer_id")
    if not referrer_id:
        return
    commission = round(price * REFERRAL_COMMISSION_PERCENT / 100)
    if commission <= 0:
        return
    referrer = get_user(referrer_id)
    referrer["discount_balance"] += commission

def compute_final_price(order: dict, user_id: int) -> int:
    price = order.get("price", 0)
    if order.get("discount_applied"):
        disc_balance = get_user(user_id)["discount_balance"]
        return max(price - min(disc_balance, price), 0)
    return price

def consume_discount_if_applied(order: dict, user_id: int) -> None:
    if order.get("discount_applied"):
        price = order.get("price", 0)
        u = get_user(user_id)
        used = min(u["discount_balance"], price)
        u["discount_balance"] -= used

def new_id() -> str:
    return str(random.randint(1000000, 9999999))

def new_req_id() -> str:
    return str(next(_counter))

def save_data() -> None:
    data = {
        "users": {str(k): v for k, v in USERS.items()},
        "user_names": {str(k): v for k, v in USER_NAMES.items()},
        "orders": ORDERS,
        "topup_requests": TOPUP_REQUESTS,
        "pending_referrals": {str(k): v for k, v in PENDING_REFERRALS.items()},
        "support_tickets": SUPPORT_TICKETS,
        "bot_enabled": BOT_ENABLED,
        "referral_points_enabled": REFERRAL_POINTS_ENABLED,
        "stars_price": STARS_PRICE,
        "ton_price": TON_PRICE,
        "premium_prices": PREMIUM_PRICES,
        "gifts_prices": {k: v["price"] for k, v in GIFTS.items()},
        "daily_limit": DAILY_LIMIT,
        "profit_percent": PROFIT_PERCENT,
        "base_ton_price": BASE_TON_PRICE,
        "base_stars_price": BASE_STARS_PRICE,
        "base_premium_prices": BASE_PREMIUM_PRICES,
    }
    try:
        tmp = DATA_FILE + ".tmp"
        with open(tmp, "w", encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        os.replace(tmp, DATA_FILE)
    except Exception:
        logging.exception("خطا در ذخیره داده‌ها")

def load_data() -> None:
    global BOT_ENABLED, REFERRAL_POINTS_ENABLED, STARS_PRICE, TON_PRICE, DAILY_LIMIT
    global BASE_TON_PRICE, BASE_STARS_PRICE
    if not os.path.exists(DATA_FILE):
        logging.info("فایل داده یافت نشد؛ با تنظیمات پیش‌فرض شروع می‌شه.")
        return
    try:
        with open(DATA_FILE, "r", encoding="utf-8") as f:
            data = json.load(f)

        for k, v in data.get("users", {}).items():
            USERS[int(k)] = v

        for k, v in data.get("user_names", {}).items():
            USER_NAMES[int(k)] = v

        ORDERS.update(data.get("orders", {}))
        TOPUP_REQUESTS.update(data.get("topup_requests", {}))
        SUPPORT_TICKETS.update(data.get("support_tickets", {}))

        for k, v in data.get("pending_referrals", {}).items():
            PENDING_REFERRALS[int(k)] = v

        BOT_ENABLED = data.get("bot_enabled", True)
        REFERRAL_POINTS_ENABLED = data.get("referral_points_enabled", False)
        STARS_PRICE = data.get("stars_price", STARS_PRICE)
        TON_PRICE = data.get("ton_price", TON_PRICE)
        PREMIUM_PRICES.update(data.get("premium_prices", {}))
        DAILY_LIMIT = data.get("daily_limit", DAILY_LIMIT)
        PROFIT_PERCENT.update(data.get("profit_percent", {}))
        BASE_TON_PRICE = data.get("base_ton_price", BASE_TON_PRICE)
        BASE_STARS_PRICE = data.get("base_stars_price", BASE_STARS_PRICE)
        BASE_PREMIUM_PRICES.update(data.get("base_premium_prices", {}))
        recalc_prices()  

        for key, price in data.get("gifts_prices", {}).items():
            if key in GIFTS:
                GIFTS[key]["price"] = price

        logging.info(
            f"داده‌ها بارگذاری شد: {len(USERS)} کاربر، {len(ORDERS)} سفارش"
        )
    except Exception:
        logging.exception("خطا در بارگذاری داده‌ها — با تنظیمات پیش‌فرض شروع می‌شه")

async def is_member(context: ContextTypes.DEFAULT_TYPE, user_id: int) -> bool:
    try:
        member = await context.bot.get_chat_member(f"@{REQUIRED_CHANNEL}", user_id)
        return member.status in ("member", "administrator", "creator")
    except TelegramError as e:
        logging.warning(
            f"⚠️ is_member failed (ربات احتمالا ادمین @{REQUIRED_CHANNEL} نیست): {e}"
        )
        
        return True

def persian_now_str() -> str:
    if HAS_JDATETIME:
        now = jdatetime.datetime.now()
    else:
        now = datetime.now()
    return now.strftime("%Y-%m-%d     %H:%M:%S")

WELCOME_TEXT = (
    "🌟 ب ربات فروشگاه Abolfazl خوش آمدید !\n\n"
    "✨با ما بهترین خدمات تلگرام را با کمترین قیمت و بالاترین سرعت دریافت کنید\n\n"
    "🎁 خدمات بات Abolfazl\n\n"
    "• ⭐️استارز تلگرام\n"
    "• 💎پرمیوم تلگرام\n"
    "• 🎁گیفت‌های استارزی و NFT\n"
    "• ⚡️فروش ارز تون ( GRAM )\n\n"
    "🚀 برای شروع ، یکی از گزینه‌های منو پایین را انتخاب کنید ☝️"
)

PRODUCT_TEXT = (
    "🛒 محصول مورد نظر خودتو انتخاب کن !\n\n"
    "🚀 تمامی سفارشات با بالاترین سرعت و همراه با پشتیبانی کامل انجام می‌شن "
    "تا تجربه‌ای مطمئن و بی‌دغدغه داشته باشی !\n\n"
    "🫶 از لیست زیر ، گزینه مورد نظرت رو انتخاب کن :"
)

PREMIUM_TEXT = (
    "🟪 Telegram Premium\n\n"
    "⭐️ سطح فوق جدیدی از امکانات تلگرام را تجربه کنید.\n\n"
    "✅فعال شدن تیک آبی \n"
    "💎 دسترسی به قابلیت‌های اختصاصی پریمیوم\n"
    "📂 افزایش محدودیت‌های آپلود\n"
    "⚡️ سرعت بیشتر در دانلود و استفاده از تلگرام\n"
    "👨\u200d🎨 استیکرها، ایموجی‌ها و قابلیت‌های ویژه\n"
    "📈 ابزارهای حرفه‌ای برای مدیریت بهتر\n\n"
    "🛡 فعال‌سازی رسمی، \n"
    "امن و بدون نیاز به دسترسی به اکانت\n\n"
    "🚀 انجام سفارش در کوتاه‌ترین زمان ممکن\n\n"
    "✍️ کدام گزینه را انتخاب می‌کنید؟"
)

ASK_USERNAME_TEXT = (
    "📎 یوزرنیم اکانت مورد نظر \n\n"
    "✅اگر قصد خرید برای اکانت خودتان را دارید ، روی دکمه «برای خودم» کلیک کنید.\n\n"
    "✅اگر قصد خرید برای شخص دیگری را دارید یوزرنیم تلگرام او را با علامت @ ارسال کنید\n\n"
    "نمونه:\n"
    "✅@Abolfazl_rwm\n"
    "😭 Abolfazl_rwm"
)

CANCELLED_TEXT = (
    "👎 سفارش شما لغو شد\n\n"
    "برای خرید مجدد، از منوی اصلی گزینه ' خـریـد مـحـصـول🛒' را انتخاب کنید."
)

TON_BUY_TEXT = (
    "💰 خرید ارز تون (GRAM)\n\n"
    "✨ ارز تون را با بهترین قیمت و تحویل مستقیم به کیف پول خود دریافت کنید.\n\n"
    "💎 انتقال مستقیم به ولت \n"
    "⚡️ انجام سریع سفارش\n"
    "🔒 تراکنش امن و قابل‌اعتماد\n"
    "🙏 پشتیبانی کامل در تمامی مراحل\n\n"
    f"📊 قیمت هر TON:  {fmt(TON_PRICE)} تومان\n"
    f"📌 حداقل سفارش: {MIN_TON} TON\n\n"
    "☝️ مقدار TON موردنیاز خود را وارد کنید.\n\n"
    "💡 مثال: 0.5 یا 2.5"
)

TON_WALLET_TEXT = (
    "💼 آدرس ولت تون ( TON )\n\n"
    "⚠️ نکات مهم قبل از ارسال آدرس :\n\n"
    "• آدرس باید مخصوص شبکه TON باشد.\n"
    "• آدرس را کامل و بدون فاصله یا تغییر ارسال نمایید.\n"
    "• مسئولیت صحت آدرس واردشده بر عهده کاربر است.\n\n"
    "📎 نمونه فرمت آدرس ولت تون :\n"
    "UQ….................…xY\n"
    "یا\n"
    "EQ….................…9K\n\n"
    "🔓 لطفاً آدرس ولت تون ( TON ) خود را ارسال کنید :"
)

TON_MEMO_QUESTION_TEXT = (
    "💼 آدرس ولت ثبت شد!\n\n"
    "💬 یک سوال مهم:\n\n"
    "بعضی ولت‌های TON برای دریافت درستِ ارز، نیاز به کامنت (ممو) دارن "
    "تا واریز به همون ولت بره و گم نشه!\n\n"
    "🌟 ولت شما کامنت/ممو داره؟\n"
    "• اگه داره، روی «بله، کامنت دارم» بزن و متن کامنت رو بفرست\n"
    "• اگه نداره یا مطمئن نیستی، روی «رد کردن» بزن"
)

TON_MEMO_INPUT_TEXT = (
    "💬 ارسال کامنت/ممو ولت\n\n"
    "لطفاً متن کامنت (ممو) ولت تون رو همینجا بفرستید "
    "تا دقیقاً همون برای واریز استفاده بشه."
)

WALLET_INCREASE_TEXT = (
    "💰 افـزایـش مـوجـودی حساب شما\n\n"
    "شما می‌توانید موجودی خود را به دو روش امن و سریع افزایش دهید :\n\n"
    "💳 پرداخت تومانی – پس از واریز ، موجودی شما به سرعت با تایید پشتیبانی شارژ میشود.\n\n"
    "🚀 فرآیند افزایش موجودی ساده ، سریع و امن است و پس از پرداخت می‌توانید "
    "به راحتی از تمامی خدمات ربات استفاده کنید ✔️"
)

RECEIPT_PROMPT_TEXT = "💳 رسید خود را در قالب عکس ارسال کنید"

RECEIPT_SENT_TEXT = (
    "✅ درخواست افزایش موجودی شما با موفقیت ارسال شد\n\n"
    "پس از تایید نهایی توسط ادمین، موجودی حساب شما به مبلغی که تعیین کرده اید شارژ میشود"
)

STARS_BUY_TEXT = (
    "🌟 خرید استارز تلگرام\n\n"
    "🚀 تحویل آنی، پشتیبانی لحظه‌ای و ثبت سفارش بدون تأخیر\n\n"
    "✨ مناسب برای:\n"
    "💎  خرید تلگرام پریمیوم \n"
    "⭐️ ری‌اکشن‌های استارزی\n"
    "📣 تبلیغات تلگرامی\n"
    "🛍 مینی‌اپ‌ها \n"
    "🎁خرید گیفت\n\n"
    f"📈 حداقل خرید:  {STARS_MIN} عدد\n\n"
    "👇 تعداد استارز موردنیاز خود را وارد کنید:"
)

STARS_MIN_ERROR_TEXT = (
    f"‼️ حداقل تعداد استارز برای خرید {STARS_MIN} عدد میباشد ..."
)

GIFT_LIST_TEXT = (
    "💰 هدیه دادن گیفت ، تجربه‌ای خاص در تلگرام!\n\n"
    "⭐️ با گیفت‌های استارزی می‌تونی دوستان، خانواده یا معشوقت رو شگفت‌زده کنی !\n\n"
    "💫 مزایای گیفت‌های استارزی :\n\n"
    "• 🎁 ارسال هدیه به دوستان و آشنایان برای سوپرایز کردن\n"
    "• 👾 قابل نمایش روی پروفایل تلگرام\n\n"
    "💱 لطفاً گیفت مورد نظر خود را از لیست زیر انتخاب کنید :"
)

GIFT_COMMENT_TYPE_TEXT = (
    "💬 تنظیم کامنت گیفت\n\n"
    "نوع کامنت را انتخاب کنید:"
)

GIFT_COMMENT_INPUT_TEXT = "کامنت خود را ارسال کنید:"

PRODUCT_PLACEHOLDER = {
    "product_gift_nft": "🖼 بخش گیفت NFT (در حال تکمیل...)",
}

MAIN_PLACEHOLDER = {}

TRACK_TEXT = (
    "📦 پیگیری سفارش\n\n\n"
    "🔢 در صورتی که کد پیگیری محصول خود را دارید دکمه زیر را فشار دهید\n"
    "‼️ برای اطلاع از وضعیت سفارش خود باید کد پیگیری داشته باشید"
)

TRACK_ASK_CODE_TEXT = "🔢 کد پیگیری سفارشتان را ارسال کنید 🔍"

TRACK_NOT_FOUND_TEXT = (
    "❌ کد پیگیری یافت نشد.\n\nلطفا کد پیگیری صحیح رو دوباره ارسال کن."
)

SUPPORT_USERNAME = "Abolfazl_rwm"  

SUPPORT_TEXT = "لطفاً یکی از گزینه‌های زیر را انتخاب کنید تا با پشتیبانی ارتباط بگیرید 👇"

SUPPORT_INDIRECT_TEXT = "💬 پیام خود را برای پشتیبانی ارسال کنید 📞"

SUPPORT_SENT_CONFIRM_TEXT = (
    "💬 پیام شما به پشتیبانی ارسال شد  و  در اسرع وقت به آن پاسخ داده خواهد شد✅"
)

TRUST_CHANNEL = "Abolfazl_rwm"  

TRUST_TEXT = (
    "⭐ چرا می‌توانید با خیال راحت به ما اعتماد کنید؟\n\n"
    "ما یک ساله در زمینه‌ی خرید و فروش استارز و پرمیوم فعالیت داریم و در این مدت "
    "با صداقت، سرعت و پشتیبانی قوی تونستیم اعتماد صدها کاربر رو جلب کنیم ❤️\n\n"
    "✅ دلایل اعتماد :\n"
    "• 💳 پرداخت سریع، مطمئن و شفاف\n"
    "• 🌟 بیش از 200 رضایت واقعی مشتری\n"
    "• 📞 پشتیبانی فعال 24 ساعته، قبل و بعد از خرید\n"
    "• 🧾 دارای نماد اعتبار و سابقه‌ی چندساله فعالیت بدون هیچ گزارش منفی\n\n"
    f"📣 برای مشاهده‌ی نظرات و رضایت کاربران، می‌تونید در کانال @{TRUST_CHANNEL} ما\n"
    "هشتگ زیر رو جست‌وجو کنید ☝️\n\n"
    "☝️ #رضایت_Abolfazl\n"
    "اعتماد شما باعث افتخار ماست 💙"
)

JOIN_TEXT = (
    "برای استفاده از ربات، باید در کانال‌های زیر عضو شوید\n\n"
    f"@{REQUIRED_CHANNEL}"
)

JOIN_CONFIRMED_TEXT = "✅ عضویتت تایید شد، حالا میتونی از ربات استفاده کنی."

BOT_OFF_TEXT = (
    "🔴 ربات در حال حاضر خاموش است.\n\n"
    "به زودی دوباره روشن خواهد شد، لطفا کمی صبر کنید 🙏"
)

ADMIN_BROADCAST_ASK_TEXT = "📢 متن پیامی که می‌خوای برای تمام کاربران ربات ارسال بشه رو بفرست:"

ADMIN_VIEW_USERS_EMPTY_TEXT = "👥 هنوز هیچ کاربری ربات رو استارت نکرده."

ADMIN_PRICE_MENU_TEXT = "💰 تنظیم قیمت محصولات\n\nمحصول مورد نظر برای تغییر قیمت رو انتخاب کن:"

ADMIN_PREMIUM_PLAN_SELECT_TEXT = "✏️ قیمت کدوم پلن پرمیوم رو می‌خوای تغییر بدی؟"

ADMIN_GIFT_PRICE_LIST_TEXT = "🎀 گیفت مورد نظر برای مشاهده/تغییر قیمت رو انتخاب کن:"

def confirm_username_text(username: str) -> str:
    return f"{username}\n\n✅ آیا یوزرنیم بالا را تایید می‌کنید؟"

def premium_invoice_text(plan, price, username, disc, final) -> str:
    return (
        "⏰ فاکتور خرید پرمیوم\n\n"
        f"💫 نوع پلن: {plan} ماهه\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ فاکتور:  {fmt(price)} تومان\n"
        f"🎁 کل موجودی تخفیف:  {fmt(disc)} تومان\n\n"
        f"💡 حداکثر تخفیف قابل اعمال:  {fmt(min(disc, price))} تومان\n\n"
        f"💳 مبلغ نهایی:  {fmt(final)} تومان\n\n"
        "🍾 در صورتی که جزئیات بالا مورد تأیید شماست \n\n"
        "روی دکمه «تأیید ✅» کلیک کنید."
    )

def premium_success_text(plan, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت ثبت شد!\n\n"
        f"💫 پلن: {plan} ماهه\n"
        f"📎 یوزر: {username}\n"
        f"💰 مبلغ پرداخت‌شده: {fmt(price)} تومان\n\n"
        f"🔑 کد پیگیری سفارش : {order_id}\n\n"
        "⏳ سفارش شما در حال پردازش است و به‌زودی وضعیت آن از طریق ربات اطلاع‌رسانی خواهد شد.\n\n"
        "🙏 از اعتماد شما سپاسگزاریم."
    )

def premium_done_text(plan, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت انجام شد 🎉\n\n"
        "☝️اطلاعات سفارش:\n\n"
        "ℹ️ نوع خدمات: پرمیوم تلگرام\n"
        f"💫 پلن: {plan} ماهه\n"
        f"📎 یوزر: {username}\n"
        f"💳 مبلغ نهایی پرداخت شده:  {fmt(price)} تومان\n"
        f"🔓 کد پیگیری سفارش: {order_id}\n\n"
        "😊 از اعتماد و انتخاب شما سپاسگزاریم."
    )

def admin_premium_text(user, plan, username, price, order_id) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "🛒 سفارش جدید - پرمیوم تلگرام\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"💫 پلن: {plan} ماهه\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ پرداخت‌شده: {fmt(price)} تومان\n"
        f"🔢 کد پیگیری: {order_id}"
    )

def ton_invoice_text(amount, wallet, memo, price, disc, final) -> str:
    return (
        "📑 فاکتور خرید ارز تون\n\n"
        f"💫 مقدار خرید: {amount} تون\n"
        f"🏦 ولت آدرس دریافتی: {wallet}\n"
        f"💬 ممو/کامنت ولت: {memo}\n"
        f"💰 مبلغ فاکتور:  {fmt(price)} تومان\n"
        f"🎁 کل موجودی تخفیف:  {fmt(disc)} تومان\n\n"
        f"💡 حداکثر تخفیف قابل اعمال:  {fmt(min(disc, price))} تومان\n\n"
        f"💳 مبلغ نهایی:  {fmt(final)} تومان\n\n"
        "🔮 در صورتی که جزئیات بالا مورد تأیید شماست ✓ \n"
        "روی دکمه «تأیید ✅» کلیک کنید."
    )

def ton_success_text(amount, wallet, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت ثبت شد.\n\n"
        "⛏️نوع خدمات : خرید ارز تون\n"
        f"🔢 تعداد : {amount}\n"
        f"📎 آدرس ولت : {wallet}\n"
        f"💰 مبلغ پرداختی :  {fmt(price)} تومان\n"
        f"🔑 کد پیگیری سفارش : {order_id}\n\n"
        "⏳ سفارش شما در حال پردازش است و به‌زودی وضعیت آن از طریق ربات اطلاع‌رسانی خواهد شد.\n\n"
        "🙏 از اعتماد شما سپاسگزاریم."
    )

def ton_done_text(amount, wallet, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت انجام شد 🎉\n\n"
        "☝️اطلاعات سفارش:\n\n"
        "ℹ️ نوع خدمات: خرید ارز تون\n"
        f"👀 تعداد: {amount} TON\n"
        f"🔗 ولت تون: {wallet}\n"
        f"💳 مبلغ نهایی پرداخت شده:  {fmt(price)} تومان\n"
        f"🔓 کد پیگیری سفارش: {order_id}\n\n"
        "😊 از اعتماد و انتخاب شما سپاسگزاریم."
    )

def stars_invoice_text(count, username, price, disc, final) -> str:
    return (
        "⏰ فاکتور خرید استارز\n\n"
        f"💫 مقدار خرید: {count}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ فاکتور:  {fmt(price)} تومان\n"
        f"🎁 کل موجودی تخفیف:  {fmt(disc)} تومان\n\n"
        f"💡 حداکثر تخفیف قابل اعمال:  {fmt(min(disc, price))} تومان\n\n"
        f"💳 مبلغ نهایی:  {fmt(final)} تومان\n\n"
        "🍾 در صورتی که جزئیات بالا مورد تأیید شماست \n\n"
        "روی دکمه «تأیید ✅» کلیک کنید."
    )

def stars_success_text(count, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت ثبت شد!\n\n"
        "⛏️ نوع خدمات: خرید استارز تلگرام\n"
        f"⭐️ تعداد: {count} استارز\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ پرداختی:  {fmt(price)} تومان\n"
        f"🔑 کد پیگیری سفارش : {order_id}\n\n"
        "⏳ سفارش شما در حال پردازش است و به‌زودی وضعیت آن از طریق ربات اطلاع‌رسانی خواهد شد.\n\n"
        "🙏 از اعتماد شما سپاسگزاریم."
    )

def stars_done_text(count, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت انجام شد 🎉\n\n"
        "☝️اطلاعات سفارش:\n\n"
        "ℹ️ نوع خدمات: خرید استارز تلگرام\n"
        f"⭐️ تعداد: {count} استارز\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💳 مبلغ نهایی پرداخت شده:  {fmt(price)} تومان\n"
        f"🔓 کد پیگیری سفارش: {order_id}\n\n"
        "😊 از اعتماد و انتخاب شما سپاسگزاریم."
    )

def admin_stars_text(user, count, username, price, order_id) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "🛒 سفارش جدید - استارز تلگرام\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"⭐️ تعداد استارز: {count}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ پرداخت‌شده: {fmt(price)} تومان\n"
        f"🔢 کد پیگیری: {order_id}"
    )

def gift_label(key: str) -> str:
    g = GIFTS[key]
    return f"گیفت {g['name']} ({g['stars']} ⭐️)"

def gift_invoice_text(key, username, hidden, comment, price, disc, final) -> str:
    hide_str = "✅ بله" if hidden else "❌ خیر"
    comment_str = comment if comment else "ندارد"
    return (
        "📑 فاکتور خرید گیفت\n\n"
        f"💫 مقدار خرید: {gift_label(key)}\n"
        f"🔗 یوزر دریافت‌کننده: {username}\n"
        f"🔒 گیفت هاید: {hide_str}\n"
        f"کامنت : {comment_str}\n\n"
        f"💰 مبلغ فاکتور:  {fmt(price)} تومان\n"
        f"🎁 کل موجودی تخفیف:  {fmt(disc)} تومان\n\n"
        f"💡 حداکثر تخفیف قابل اعمال:  {fmt(min(disc, price))} تومان\n\n"
        f"💳 مبلغ نهایی:  {fmt(final)} تومان\n\n"
        "🔮 در صورتی که جزئیات بالا مورد تأیید شماست ✓ \n"
        "روی دکمه «تأیید ✅» کلیک کنید."
    )

def gift_success_text(key, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت ثبت شد!\n\n"
        "⛏️ نوع خدمات: خرید گیفت استارز\n"
        f"🎁 گیفت: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💰 مبلغ پرداختی:  {fmt(price)} تومان\n"
        f"🔑 کد پیگیری سفارش : {order_id}\n\n"
        "⏳ سفارش شما در حال پردازش است و به‌زودی وضعیت آن از طریق ربات اطلاع‌رسانی خواهد شد.\n\n"
        "🙏 از اعتماد شما سپاسگزاریم."
    )

def gift_done_text(key, username, price, order_id) -> str:
    return (
        "✅ سفارش شما با موفقیت انجام شد 🎉\n\n"
        "☝️اطلاعات سفارش:\n\n"
        "ℹ️ نوع خدمات: خرید گیفت استارز\n"
        f"🎁 گیفت: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"💳 مبلغ نهایی پرداخت شده:  {fmt(price)} تومان\n"
        f"🔓 کد پیگیری سفارش: {order_id}\n\n"
        "😊 از اعتماد و انتخاب شما سپاسگزاریم."
    )

def admin_gift_text(user, key, username, hidden, comment, price, order_id) -> str:
    uname = f"@{user.username}" if user.username else "---"
    hide_str = "✅ بله" if hidden else "❌ خیر"
    comment_display = f"<code>{comment}</code>" if comment else "ندارد"
    return (
        "🛒 سفارش جدید - گیفت استارز\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"🎁 گیفت: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"🔒 هاید: {hide_str}\n"
        f"💬 کامنت: {comment_display}\n"
        f"💰 مبلغ پرداخت‌شده: {fmt(price)} تومان\n"
        f"🔢 کد پیگیری: {order_id}"
    )

def admin_support_text(user, message_text: str) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "📨 پیام جدید پشتیبانی\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n\n"
        f"💬 متن پیام:\n{message_text}"
    )

def order_report_fields(order: dict):
    if order["type"] == "premium":
        return "پرمیوم تلگرام", "💎", f"{order['plan']} ماهه"
    if order["type"] == "ton":
        return "خرید ارز تون", "💱", f"{order['amount']} TON"
    if order["type"] == "stars":
        return "استارز تلگرام", "⭐️", f"{order['count']} 🌟"
    if order["type"] == "referral_reward":
        return "جایزه رفرال", "🎁", gift_label(order["key"])
    
    return "گیفت استارز", "🎁", gift_label(order["key"])

def price_display(order: dict) -> str:
    if order.get("price", 0) <= 0:
        return "رایگان 🎁"
    return f"{fmt(order['price'])} تومان"

def report_text(buyer_name: str, order: dict) -> str:
    label, emoji, qty = order_report_fields(order)
    return (
        "🛍 خرید موفق ✅\n\n"
        f"👤 خریدار: {buyer_name}\n"
        f"🛒 سفارش : {label} {emoji}\n"
        f"⛏️ تعداد: {qty}\n"
        f"💰 مبلغ پرداخت شده :  {price_display(order)} \n\n"
        f"⏰ {persian_now_str()} \n"
        f"📱 @{BOT_USERNAME}"
    )

def order_status_text(order: dict) -> str:
    type_ = order["type"]
    status_label = "تکمیل شده ✅" if order.get("status") == "done" else "در حال انجام ⏳"

    if type_ == "premium":
        label, emoji = "پرمیوم تلگرام", "💎"
        qty = f"{order['plan']} ماهه"
        extra = f"📎 یوزر دریافت‌کننده: {order['username']}"
    elif type_ == "ton":
        label, emoji = "خرید ارز تون", "⚡️"
        qty = f"{order['amount']} تون"
        extra = f"🔓 آدرس ولت کاربر دریافت کننده: {order['wallet']}"
    elif type_ == "stars":
        label, emoji = "استارز تلگرام", "⭐️"
        qty = f"{order['count']} عدد"
        extra = f"📎 یوزر دریافت‌کننده: {order['username']}"
    elif type_ == "referral_reward":
        label, emoji = "جایزه رفرال", "🎁"
        qty = gift_label(order["key"])
        extra = f"📎 یوزر دریافت‌کننده: {order['username']}"
    else:  
        label, emoji = "گیفت استارز", "🎁"
        qty = gift_label(order["key"])
        extra = f"📎 یوزر دریافت‌کننده: {order['username']}"

    date_raw = order.get("created_at", persian_now_str())
    parts = date_raw.split()
    date_display = f"{parts[0]} ساعت {parts[-1]}" if len(parts) >= 2 else date_raw

    return (
        "🔢 مشخصات سفارش \n\n"
        f"📦 نوع سفارش: {label} {emoji}\n"
        f"📊 تعداد: {qty}\n"
        f"💼 قیمت:  {price_display(order)}\n"
        f"📱 وضعیت: {status_label}\n\n\n"
        f"{extra}\n"
        f"🗓️ تاریخ و ساعت: {date_display}"
    )

def admin_panel_text() -> str:
    status = "🟢 روشن" if BOT_ENABLED else "🔴 خاموش"
    ref_status = "🟢 فعال" if REFERRAL_POINTS_ENABLED else "🔴 غیرفعال"
    return (
        "🛠 پنل مدیریت ربات\n\n"
        f"وضعیت فعلی ربات: {status}\n"
        f"وضعیت جایزه دعوت: {ref_status}\n\n"
        "از گزینه‌های زیر یکی رو انتخاب کن:"
    )

def broadcast_confirm_text(text: str) -> str:
    return (
        "📢 متن زیر برای تمام کاربران ربات ارسال خواهد شد:\n\n"
        f"{text}\n\n"
        "آیا مطمئن هستید؟"
    )

def broadcast_message_text(text: str) -> str:
    return f"👤 پیام مدیریت به تمام کاربران\n\n{text}"

def broadcast_done_text(success: int, fail: int) -> str:
    return f"✅ پیام به {success} کاربر ارسال شد.\n❌ ارسال به {fail} کاربر ناموفق بود."

def view_users_text() -> str:
    if not USERS:
        return ADMIN_VIEW_USERS_EMPTY_TEXT
    lines = []
    for uid, udata in USERS.items():
        name = USER_NAMES.get(uid, "ناشناس")
        lines.append(f"👤 {name} | 🆔 {uid} | 💰 {fmt(udata['balance'])} تومان")
    header = f"👥 کاربران ربات ({len(USERS)} نفر)\n\n"
    body = "\n".join(lines)
    full = header + body
    if len(full) > 4000:
        full = full[:4000] + "\n\n...و بقیه (متن بریده شد)"
    return full

def admin_stars_price_text() -> str:
    return f"⭐️ قیمت فعلی هر ۱ استارز:\n\n💰 {fmt(STARS_PRICE)} تومان"

def admin_ask_stars_price_text() -> str:
    return "✏️ قیمت جدید هر ۱ استارز رو به تومان وارد کن:"

def admin_ton_price_text() -> str:
    price_per_tenth = round(TON_PRICE * MIN_TON)
    return f"💱 قیمت فعلی هر {MIN_TON} TON:\n\n💰 {fmt(price_per_tenth)} تومان"

def admin_ask_ton_price_text() -> str:
    return f"✏️ قیمت جدید هر {MIN_TON} TON رو به تومان وارد کن:"

def admin_premium_price_text() -> str:
    return (
        "💎 قیمت‌های فعلی پرمیوم تلگرام:\n\n"
        f"🔹 3 ماهه:  {fmt(PREMIUM_PRICES['3'])} تومان\n"
        f"🔹 6 ماهه:  {fmt(PREMIUM_PRICES['6'])} تومان\n"
        f"🔹 12 ماهه:  {fmt(PREMIUM_PRICES['12'])} تومان"
    )

def admin_ask_premium_price_text(plan: str) -> str:
    return f"✏️ قیمت جدید پلن {plan} ماهه رو به تومان وارد کن:"

def admin_gift_price_detail_text(key: str) -> str:
    return f"{gift_label(key)}\n\n💰 قیمت فعلی: {fmt(GIFTS[key]['price'])} تومان"

def admin_ask_gift_price_text(key: str) -> str:
    return f"✏️ قیمت جدید {gift_label(key)} رو به تومان وارد کن:"

def admin_daily_limit_text() -> str:
    return (
        "⚙️ تنظیم سقف شارژ روزانه\n\n"
        f"💳 سقف مجاز فعلی: {fmt(DAILY_LIMIT)} تومان\n\n"
        "این مقدار حداکثر شارژ هر کاربر در روز است."
    )

def admin_ask_daily_limit_text() -> str:
    return (
        f"✏️ سقف مجاز شارژ روزانه جدید رو به تومان وارد کن:\n\n"
        f"(سقف فعلی: {fmt(DAILY_LIMIT)} تومان)"
    )

def admin_profit_menu_text() -> str:
    return "📈 تغییر درصد سود محصولات\n\nمحصول مورد نظر رو انتخاب کن:"

def admin_profit_stars_text() -> str:
    pct = PROFIT_PERCENT["stars"]
    base = BASE_STARS_PRICE * 50
    profit_amt = round(base * pct / 100)
    sell = STARS_PRICE * 50
    return (
        "⭐️ درصد سود استارز تلگرام\n\n"
        f"📦 مقدار نمایشی: {STARS_MIN} استارز\n"
        f"💵 قیمت پایه {STARS_MIN} استارز:  {fmt(base)} تومان\n"
        f"📈 درصد سود فعلی: {pct}٪\n"
        f"💰 مقدار سود:  {fmt(profit_amt)} تومان\n"
        f"🏷 قیمت فروش {STARS_MIN} استارز:  {fmt(sell)} تومان"
    )

def admin_profit_ton_text() -> str:
    pct = PROFIT_PERCENT["ton"]
    base = BASE_TON_PRICE
    profit_amt = round(base * pct / 100)
    sell = TON_PRICE
    return (
        "💱 درصد سود ارز تون\n\n"
        f"📦 مقدار نمایشی: ۱ TON\n"
        f"💵 قیمت پایه ۱ TON:  {fmt(base)} تومان\n"
        f"📈 درصد سود فعلی: {pct}٪\n"
        f"💰 مقدار سود:  {fmt(profit_amt)} تومان\n"
        f"🏷 قیمت فروش ۱ TON:  {fmt(sell)} تومان"
    )

def admin_profit_gift_text() -> str:
    pct = PROFIT_PERCENT["gift"]
    base_per_star = BASE_STARS_PRICE
    sell_per_star = GIFT_STAR_PRICE
    profit_amt = sell_per_star - base_per_star
    return (
        "🎀 درصد سود گیفت استارز\n\n"
        f"📦 مقدار نمایشی: هر استارز گیفت\n"
        f"💵 قیمت پایه هر استارز:  {fmt(base_per_star)} تومان\n"
        f"📈 درصد سود فعلی: {pct}٪\n"
        f"💰 مقدار سود هر استارز:  {fmt(profit_amt)} تومان\n"
        f"🏷 قیمت فروش هر استارز گیفت:  {fmt(sell_per_star)} تومان\n\n"
        "⚙️ این درصد روی قیمت همه گیفت‌ها اعمال می‌شود."
    )

def admin_profit_premium_text() -> str:
    pct = PROFIT_PERCENT["premium"]
    lines = []
    for plan, base in BASE_PREMIUM_PRICES.items():
        sell = PREMIUM_PRICES[plan]
        profit_amt = sell - base
        lines.append(f"🔹 {plan} ماهه: {fmt(base)} ← {fmt(sell)} تومان (+{fmt(profit_amt)})")
    return (
        "💎 درصد سود پرمیوم تلگرام\n\n"
        f"📈 درصد سود فعلی: {pct}٪\n\n"
        + "\n".join(lines)
    )

def admin_ask_profit_text(product_fa: str) -> str:
    return f"✏️ درصد سود جدید {product_fa} رو وارد کن (فقط عدد، بدون %):"

def admin_ton_text(user, amount, wallet, memo, price, order_id) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "🛒 سفارش جدید - خرید ارز تون\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"💫 مقدار: {amount} TON\n"
        f"🏦 آدرس ولت:\n<code>{wallet}</code>\n"
        f"💬 ممو: {memo}\n"
        f"💰 مبلغ پرداخت‌شده: {fmt(price)} تومان\n"
        f"🔢 کد پیگیری: {order_id}"
    )

def insufficient_text(shortfall: int) -> str:
    return (
        f"برای ادامه خرید مبلغ کمبود شما {fmt(shortfall)} تومان است. "
        "یکی از روش های زیر را برای شارژ سریع انتخاب کنید:"
    )

def wallet_text(balance: int, remaining: int) -> str:
    return (
        "کیف پول 💰\n\n"
        f"موجودی شما:  {fmt(balance)} تومان 💸\n"
        f"باقی مانده سقف مجاز افزایش موجودی تومانی امروز:  {fmt(remaining)} 🚀\n\n"
        "برای افزایش موجودی خود روی دکمه زیر کلیک کنید 👇"
    )

def toman_payment_text(remaining: int) -> str:
    return (
        "💳 پرداخت تومانی \n\n"
        "ابتدا مبلغی که قصد دارید به موجودی خود اضافه کنید را وارد کنید.\n\n\n"
        f"📱 باقیمانده سقف مجاز پرداخت تومانی امروز :  {fmt(remaining)} تومان\n\n\n"
        "‼️ توجه کنید که مبلغ واریزی نباید بیشتر از مبلغ سقف مجاز باشد ، "
        "در غیر اینصورت حساب شما فقط در حد سقف مجاز شارژ خواهد شد "
        "و مسئولیت آن به عهده خودتان است ❌"
    )

def daily_limit_exceeded_text(remaining: int) -> str:
    if remaining <= 0:
        return (
            "❌ سقف مجاز شارژ تومانی امروز شما تموم شده.\n\n"
            "لطفا فردا دوباره تلاش کن یا با پشتیبانی در ارتباط باش."
        )
    return (
        "❌ مبلغی که وارد کردی بیشتر از سقف مجاز باقی‌مونده امروزته.\n\n"
        f"📱 سقف مجاز باقی‌مونده امروز شما:  {fmt(remaining)} تومان\n\n"
        "لطفا مبلغ کمتر یا مساوی همین مقدار رو وارد کن."
    )

def card_details_text(amount: int) -> str:
    return (
        f"💸 مبلغ واریزی:   {fmt(amount)} تومان\n\n"
        "💼 شماره کارت جهت واریز :\n\n"
        f"<code>{CARD_NUMBER}</code>\n\n"
        f"👤 به نام: {CARD_HOLDER}\n\n"
        "⚡️ لطفا پس از واریز پول،  «دکمه ارسال رسید» را بزنید و سپس "
        "رسید پرداخت را بصورت عکس ارسال کنید\n\n"
        "❗️توجه: چنانچه رسید بصورت متن بود از آن اسکرین شات بگیرید و اسکرین شات را ارسال کنید.\n\n"
        "❗️در صورت مغایرت مبلغ تعیین شده با مبلغ واریز شده به کارت مسئولیت آن به عهده خودتان است."
    )

def admin_topup_caption(user, amount: int, req_id: str) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "🧾 درخواست افزایش موجودی جدید\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"💸 مبلغ: {fmt(amount)} تومان\n"
        f"🔢 شماره درخواست: {req_id}"
    )

def approved_text(balance: int) -> str:
    return (
        "🔥 مشتری عزیز\n\n"
        "✔️ رسید افزایش موجودی شما با موفقیت تایید شد\n"
        f"موجودی شما:  {fmt(balance)} تومان"
    )

def rejected_text(reason: str) -> str:
    return f"❌ متاسفانه درخواست افزایش موجودی شما رد شد.\n\nدلیل: {reason}"

def account_text(user) -> str:
    u = get_user(user.id)
    name = user.full_name or (f"@{user.username}" if user.username else "بدون نام")
    last5 = u["orders"][-5:][::-1]
    orders_block = "\n".join(last5) if last5 else "هنوز سفارشی ثبت نکرده‌اید"
    return (
        "👤 حساب کاربری شما\n\n"
        f"🔴 نام : {name}\n"
        f"🔴 آیدی عددی : {user.id}\n\n\n"
        f"⭐️ امتیاز شما : {u['score']} امتیاز\n"
        f"⭐️ زیر مجموعه های شما : {u['referrals']} نفر\n\n"
        "📊 آخرین 5 سفارش شما:\n"
        f"<blockquote>{orders_block}</blockquote>\n\n"
        f"💰 موجودی فعلی:  {fmt(u['balance'])} تومان\n"
        f"🎁 موجودی تخفیف:  {fmt(u['discount_balance'])} تومان"
    )

def referral_text(user) -> str:
    u = get_user(user.id)
    link = f"https://t.me/{BOT_USERNAME}?start=ref_{user.id}"
    return (
        "👥 زیرمجموعه‌گیری ( Referral System )\n\n"
        f"🔗 لینک اختصاصی شما :\n{link}\n\n"
        f"👥 زیرمجموعه‌ها : {u['referrals']} نفر\n"
        f"💰 درآمد شما :  {fmt(u['discount_balance'])} تومان\n"
        f"🏅 امتیاز رفرال : {u['score']}\n\n"
        "❗️ توضیحات سیستم رفرال :\n\n"
        "• با ارسال لینک اختصاصی خود ، دوستانتان را به ربات دعوت کنید.\n\n"
        f"• هر زمان زیرمجموعه‌ی شما خریدی انجام دهد، {REFERRAL_COMMISSION_PERCENT}٪ از مبلغ "
        "آن خرید به موجودی تخفیف شما اضافه می‌شود.\n\n"
        "• اگر در تایم‌های اعلام‌شده توسط ادمین زیرمجموعه بگیرید ، به ازای هر دعوت "
        "1 امتیاز به امتیاز رفرال شما اضافه می‌شود.\n\n"
        f"• تایم‌ها از طریق اطلاع‌رسانی ربات و کانال تلگرام @{TRUST_CHANNEL} اعلام می‌شوند\n\n"
        f"• درصورت داشتن هر {REFERRAL_POINTS_REQUIRED} امتیاز "
        "شما قادر به دریافت یک جایزه هستید\n\n"
        "(جایزه : گیفت تدی یا قلب 15 استارزی به انتخاب خودتان)"
    )

def referral_notification_text(
    invited_name: str,
    point_awarded: bool,
    total_referrals: int = 0,
    total_discount: int = 0,
    total_score: int = 0,
) -> str:
    text = (
        "🎉 تبریک! یک زیرمجموعه جدید داری!\n\n"
        f"👤 کاربر «{invited_name}» با لینک دعوت شما عضو ربات شد.\n\n"
    )
    if point_awarded:
        text += f"🏅 1 امتیاز رفرال به امتیازهای شما اضافه شد.\n"
    text += (
        "\n━━━━━━━━━━━━━━━━━\n"
        f"👥 کل زیرمجموعه‌های شما: {total_referrals} نفر\n"
        f"💸 موجودی تخفیف شما: {fmt(total_discount)} تومان\n"
        f"🏅 امتیاز رفرال شما: {total_score}\n\n"
        f"💡 هر خریدی که زیرمجموعه‌تون انجام بده، {REFERRAL_COMMISSION_PERCENT}٪ "
        "از مبلغ خرید به موجودی تخفیف شما اضافه میشه."
    )
    return text

def referral_reward_select_text() -> str:
    return "🎁 یکی از گیفت‌های زیر رو به عنوان جایزه رفرال انتخاب کن:"

def referral_reward_invoice_text(key: str, username: str) -> str:
    return (
        "🎁 فاکتور جایزه رفرال\n\n"
        f"💫 جایزه: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"🏅 امتیاز مصرفی: {REFERRAL_POINTS_REQUIRED}\n"
        "💰 مبلغ قابل پرداخت: رایگان 🎁\n\n"
        "🍾 در صورتی که جزئیات بالا مورد تأیید شماست \n\n"
        "روی دکمه «تأیید ✅» کلیک کنید."
    )

def referral_reward_success_text(key: str, username: str, order_id: str) -> str:
    return (
        "✅ جایزه رفرال شما با موفقیت ثبت شد!\n\n"
        f"🎁 جایزه: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        "💰 مبلغ پرداختی: رایگان 🎁\n"
        f"🔑 کد پیگیری سفارش : {order_id}\n\n"
        "⏳ سفارش شما در حال پردازش است و به‌زودی وضعیت آن از طریق ربات اطلاع‌رسانی خواهد شد.\n\n"
        "🙏 از همراهی شما سپاسگزاریم."
    )

def admin_referral_reward_text(user, key: str, username: str, order_id: str) -> str:
    uname = f"@{user.username}" if user.username else "---"
    return (
        "🎁 سفارش جدید - جایزه رفرال (رایگان)\n\n"
        f"👤 کاربر: {user.full_name} ({uname})\n"
        f"🆔 آیدی عددی: {user.id}\n"
        f"🎁 گیفت: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        f"🔢 کد پیگیری: {order_id}"
    )

def referral_reward_done_text(key: str, username: str, order_id: str) -> str:
    return (
        "✅ جایزه رفرال شما با موفقیت انجام شد 🎉\n\n"
        "☝️اطلاعات سفارش:\n\n"
        "ℹ️ نوع خدمات: جایزه رفرال\n"
        f"🎁 گیفت: {gift_label(key)}\n"
        f"📎 یوزر دریافت‌کننده: {username}\n"
        "💳 مبلغ نهایی پرداخت شده: رایگان 🎁\n"
        f"🔓 کد پیگیری سفارش: {order_id}\n\n"
        "😊 از همراهی شما سپاسگزاریم."
    )

def main_kb(is_admin: bool = False) -> ReplyKeyboardMarkup:
    rows = [
        [KeyboardButton("🛒 خرید محصول")],
        [KeyboardButton("👛 شارژ موجودی"), KeyboardButton("🔗 لینک دعوت من")],
        [KeyboardButton("👤 حساب کاربری")],
        [KeyboardButton("📋 پیگیری سفارش"), KeyboardButton("📞 پشتیبانی")],
        [KeyboardButton("🔽 چطور میتوانم اعتماد کنم؟")],
    ]
    if is_admin:
        rows.append([KeyboardButton("🛠 پنل ادمین")])
    return ReplyKeyboardMarkup(rows, resize_keyboard=True)

def product_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [
            InlineKeyboardButton("💎 پرمیوم تلگرام", callback_data="product_premium"),
            InlineKeyboardButton("⭐️ استارز تلگرام", callback_data="product_stars"),
        ],
        [
            InlineKeyboardButton("🎀 گیفت استارز", callback_data="product_gift_stars"),
            InlineKeyboardButton("💱 خرید ارز تون", callback_data="product_buy_ton"),
        ],
        [InlineKeyboardButton("🖼 گیفت NFT", callback_data="product_gift_nft")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_welcome")],
    ])

def premium_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [
            InlineKeyboardButton("3 ماه", callback_data="premium_3"),
            InlineKeyboardButton("6 ماه", callback_data="premium_6"),
            InlineKeyboardButton("12 ماه", callback_data="premium_12"),
        ],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_products")],
    ])

def ask_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("👤 برای خودم", callback_data="username_self")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_premium")],
    ])

def confirm_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ بله", callback_data="confirm_yes"),
        InlineKeyboardButton("❌ خیر", callback_data="confirm_no"),
    ]])

def invoice_kb(discount_applied: bool = False) -> InlineKeyboardMarkup:
    discount_label = "❌ لغو تخفیف" if discount_applied else "💸 اعمال تخفیف"
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("تأیید ✅", callback_data="invoice_confirm")],
        [
            InlineKeyboardButton(discount_label, callback_data="invoice_discount"),
            InlineKeyboardButton("❌ کنسل", callback_data="invoice_cancel"),
        ],
    ])

def insufficient_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("💳 کارت به کارت", callback_data="topup_from_invoice")],
        [InlineKeyboardButton("🔙 بازگشت به منو", callback_data="back_to_products")],
    ])

def wallet_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("افزایش موجودی", callback_data="wallet_increase")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_welcome")],
    ])

def wallet_increase_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("💳 پرداخت تومانی", callback_data="topup_from_wallet")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="wallet_back")],
    ])

def toman_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("🔙 بازگشت", callback_data="topup_back")]
    ])

def card_details_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="card_back"),
        InlineKeyboardButton("📨 ارسال رسید", callback_data="send_receipt"),
    ]])

def receipt_prompt_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("🔙 بازگشت", callback_data="receipt_back")]
    ])

def admin_topup_kb(req_id: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ تایید", callback_data=f"topup_approve_{req_id}"),
        InlineKeyboardButton("❌ رد", callback_data=f"topup_reject_{req_id}"),
    ]])

def admin_order_kb(order_id: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ انجام شد", callback_data=f"order_done_{order_id}"),
    ]])

def buy_product_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🛒 خرید محصول", callback_data="back_to_products"),
    ]])

def ton_back_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_products"),
    ]])

def ton_wallet_back_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="ton_back_to_amount"),
    ]])

def ton_memo_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✅ بله، کامنت دارم", callback_data="ton_memo_yes")],
        [InlineKeyboardButton("❌ رد کردن", callback_data="ton_memo_skip")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="ton_memo_back")],
    ])

def ton_memo_input_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="ton_memo_input_back"),
    ]])

def ton_invoice_kb(discount_applied: bool = False) -> InlineKeyboardMarkup:
    discount_label = "❌ لغو تخفیف" if discount_applied else "💸 اعمال تخفیف"
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("تأیید ✅", callback_data="ton_invoice_confirm")],
        [
            InlineKeyboardButton(discount_label, callback_data="ton_invoice_discount"),
            InlineKeyboardButton("❌ کنسل", callback_data="ton_invoice_cancel"),
        ],
    ])

def stars_back_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_products"),
    ]])

def stars_min_error_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_products"),
    ]])

def ask_stars_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("👤 برای خودم", callback_data="stars_username_self")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="stars_back_to_amount")],
    ])

def confirm_stars_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ بله", callback_data="stars_confirm_yes"),
        InlineKeyboardButton("❌ خیر", callback_data="stars_confirm_no"),
    ]])

def stars_invoice_kb(discount_applied: bool = False) -> InlineKeyboardMarkup:
    discount_label = "❌ لغو تخفیف" if discount_applied else "💸 اعمال تخفیف"
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("تأیید ✅", callback_data="stars_invoice_confirm")],
        [
            InlineKeyboardButton(discount_label, callback_data="stars_invoice_discount"),
            InlineKeyboardButton("❌ کنسل", callback_data="stars_invoice_cancel"),
        ],
    ])

def gift_list_kb() -> InlineKeyboardMarkup:
    rows = []
    keys = list(GIFTS.keys())
    
    i = 0
    while i < len(keys):
        g = GIFTS[keys[i]]
        label_a = f"{g['emoji']} {g['name']} (⭐️ {g['stars']})"
        if i + 1 < len(keys):
            g2 = GIFTS[keys[i + 1]]
            label_b = f"{g2['emoji']} {g2['name']} (⭐️ {g2['stars']})"
            rows.append([
                InlineKeyboardButton(label_a, callback_data=f"gift_select_{keys[i]}"),
                InlineKeyboardButton(label_b, callback_data=f"gift_select_{keys[i+1]}"),
            ])
            i += 2
        else:
            rows.append([InlineKeyboardButton(label_a, callback_data=f"gift_select_{keys[i]}")])
            i += 1
    rows.append([InlineKeyboardButton("◀️ بازگشت", callback_data="back_to_products")])
    return InlineKeyboardMarkup(rows)

def ask_gift_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("👤 برای خودم", callback_data="gift_username_self")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="gift_back_to_list")],
    ])

def confirm_gift_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ بله", callback_data="gift_confirm_yes"),
        InlineKeyboardButton("❌ خیر", callback_data="gift_confirm_no"),
    ]])

def gift_invoice_kb(hidden: bool, has_comment: bool, discount_applied: bool = False) -> InlineKeyboardMarkup:
    hide_label = "🔓 هاید کردن" if not hidden else "🔒 لغو هاید بودن"
    comment_label = "🗑 حذف کامنت" if has_comment else "💬 تنظیم کامنت"
    comment_cb = "gift_del_comment" if has_comment else "gift_comment_type"
    discount_label = "❌ لغو تخفیف" if discount_applied else "💸 اعمال تخفیف"
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("تأیید ✅", callback_data="gift_invoice_confirm")],
        [
            InlineKeyboardButton(discount_label, callback_data="gift_invoice_discount"),
            InlineKeyboardButton("❌ کنسل", callback_data="gift_invoice_cancel"),
        ],
        [
            InlineKeyboardButton(comment_label, callback_data=comment_cb),
            InlineKeyboardButton(hide_label, callback_data="gift_toggle_hide"),
        ],
    ])

def gift_comment_type_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✍️ عادی (رایگان)", callback_data="gift_comment_free")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="gift_comment_back_to_invoice")],
    ])

def gift_comment_input_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="gift_comment_input_back"),
    ]])

def support_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("☎️ ارتباط مستقیم", url=f"https://t.me/{SUPPORT_USERNAME}"),
        InlineKeyboardButton("💬 ارتباط غیر مستقیم", callback_data="support_indirect"),
    ]])

def support_indirect_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="support_back"),
    ]])

def admin_support_kb(ticket_id: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✍️ پاسخ دادن", callback_data=f"support_reply_{ticket_id}"),
    ]])

def join_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("📢 عضویت در کانال", url=f"https://t.me/{REQUIRED_CHANNEL}")],
        [InlineKeyboardButton("✅ عضو شدم", callback_data="check_membership")],
    ])

def report_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🛍 حالا اقدام به خرید کن", url=f"https://t.me/{BOT_USERNAME}"),
    ]])

def track_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔢 کد پیگیری محصول دارم", callback_data="track_have_code"),
    ]])

def track_ask_code_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="track_back"),
    ]])

def invite_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🎁 دریافت جایزه", callback_data="referral_claim_reward"),
    ]])

def referral_reward_select_kb() -> InlineKeyboardMarkup:
    keyboard = []
    for k in REFERRAL_REWARD_GIFTS:
        g = GIFTS[k]
        keyboard.append([InlineKeyboardButton(f"{g['emoji']} گیفت {g['name']}", callback_data=f"referral_reward_{k}")])
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data="referral_back")])
    return InlineKeyboardMarkup(keyboard)

def ask_referral_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("👤 برای خودم", callback_data="referral_username_self")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="referral_reward_select_back")],
    ])

def confirm_referral_username_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ بله", callback_data="referral_confirm_yes"),
        InlineKeyboardButton("❌ خیر", callback_data="referral_confirm_no"),
    ]])

def referral_reward_invoice_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("تأیید ✅", callback_data="referral_invoice_confirm")],
        [InlineKeyboardButton("❌ کنسل", callback_data="referral_invoice_cancel")],
    ])

def admin_panel_kb() -> InlineKeyboardMarkup:
    toggle_label = "🔴 خاموش کردن ربات" if BOT_ENABLED else "🟢 روشن کردن ربات"
    ref_toggle_label = "🔴 غیرفعال‌سازی جایزه دعوت" if REFERRAL_POINTS_ENABLED else "🟢 فعال‌سازی جایزه دعوت"
    return InlineKeyboardMarkup([
        [InlineKeyboardButton(toggle_label, callback_data="admin_toggle_bot")],
        [InlineKeyboardButton(ref_toggle_label, callback_data="admin_toggle_referral_points")],
        [InlineKeyboardButton("📢 ارسال پیام به تمام کاربران", callback_data="admin_broadcast")],
        [InlineKeyboardButton("👥 دیدن کاربران ربات", callback_data="admin_view_users")],
        [InlineKeyboardButton("💰 تنظیم قیمت", callback_data="admin_price_menu")],
        [InlineKeyboardButton("📈 تغییر درصد سود", callback_data="admin_profit_menu")],
        [InlineKeyboardButton("📊 تنظیم سقف شارژ روزانه", callback_data="admin_daily_limit")],
        [InlineKeyboardButton("🗑 ریست کامل داده‌های ربات", callback_data="admin_reset_confirm")],
    ])

def admin_reset_confirm_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("✅ بله، همه چیز پاک بشه", callback_data="admin_reset_yes"),
        InlineKeyboardButton("❌ نه، برگرد", callback_data="admin_panel_back"),
    ]])

def admin_back_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_panel_back"),
    ]])

def admin_broadcast_ask_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_panel_back"),
    ]])

def admin_broadcast_confirm_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("❌ نه", callback_data="admin_broadcast_no"),
        InlineKeyboardButton("✅ بله", callback_data="admin_broadcast_yes"),
    ]])

def admin_price_menu_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("⭐️ استارز تلگرام", callback_data="admin_price_stars")],
        [InlineKeyboardButton("💎 پرمیوم تلگرام", callback_data="admin_price_premium")],
        [InlineKeyboardButton("💱 ارز تون", callback_data="admin_price_ton")],
        [InlineKeyboardButton("🎀 گیفت استارز", callback_data="admin_price_giftstars")],
        [InlineKeyboardButton("🖼 گیفت NFT", callback_data="admin_price_giftnft")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_panel_back")],
    ])

def admin_stars_price_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تغییر قیمت", callback_data="admin_change_stars_price")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_menu_back")],
    ])

def admin_stars_price_ask_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_stars_back"),
    ]])

def admin_ton_price_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تغییر قیمت", callback_data="admin_change_ton_price")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_menu_back")],
    ])

def admin_ton_price_ask_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_ton_back"),
    ]])

def admin_premium_price_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تغییر قیمت", callback_data="admin_change_premium_price")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_menu_back")],
    ])

def admin_premium_plan_select_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [
            InlineKeyboardButton("3 ماه", callback_data="admin_premium_plan_3"),
            InlineKeyboardButton("6 ماه", callback_data="admin_premium_plan_6"),
            InlineKeyboardButton("12 ماه", callback_data="admin_premium_plan_12"),
        ],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_premium_back")],
    ])

def admin_premium_price_ask_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_premium_plan_select_back"),
    ]])

def admin_gift_price_list_kb() -> InlineKeyboardMarkup:
    rows = []
    keys = list(GIFTS.keys())
    i = 0
    while i < len(keys):
        g = GIFTS[keys[i]]
        label_a = f"{g['emoji']} {g['name']}"
        if i + 1 < len(keys):
            g2 = GIFTS[keys[i + 1]]
            label_b = f"{g2['emoji']} {g2['name']}"
            rows.append([
                InlineKeyboardButton(label_a, callback_data=f"admin_gift_price_{keys[i]}"),
                InlineKeyboardButton(label_b, callback_data=f"admin_gift_price_{keys[i+1]}"),
            ])
            i += 2
        else:
            rows.append([InlineKeyboardButton(label_a, callback_data=f"admin_gift_price_{keys[i]}")])
            i += 1
    rows.append([InlineKeyboardButton("🔙 بازگشت", callback_data="admin_price_menu_back")])
    return InlineKeyboardMarkup(rows)

def admin_gift_price_detail_kb(key: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تغییر قیمت", callback_data=f"admin_change_gift_price_{key}")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_gift_price_list_back")],
    ])

def admin_gift_price_ask_kb(key: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data=f"admin_gift_price_detail_back_{key}"),
    ]])

def admin_daily_limit_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تنظیم سقف روزانه", callback_data="admin_change_daily_limit")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_panel_back")],
    ])

def admin_daily_limit_ask_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data="admin_daily_limit_back"),
    ]])

def admin_profit_menu_kb() -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("⭐️ استارز تلگرام", callback_data="admin_profit_stars")],
        [InlineKeyboardButton("💱 ارز تون", callback_data="admin_profit_ton")],
        [InlineKeyboardButton("🎀 گیفت استارز", callback_data="admin_profit_gift")],
        [InlineKeyboardButton("💎 پرمیوم تلگرام", callback_data="admin_profit_premium")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_panel_back")],
    ])

def admin_profit_detail_kb(product: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("✏️ تنظیم سود", callback_data=f"admin_set_profit_{product}")],
        [InlineKeyboardButton("🔙 بازگشت", callback_data="admin_profit_menu_back")],
    ])

def admin_profit_ask_kb(product: str) -> InlineKeyboardMarkup:
    return InlineKeyboardMarkup([[
        InlineKeyboardButton("🔙 بازگشت", callback_data=f"admin_profit_{product}"),
    ]])

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    context.user_data.clear()
    user = update.effective_user
    chat_id = update.effective_chat.id

    if context.args:
        payload = context.args[0]
        if payload.startswith("ref_"):
            try:
                ref_id = int(payload[len("ref_"):])
                if ref_id != user.id:
                    PENDING_REFERRALS[user.id] = ref_id
            except ValueError:
                pass

    if chat_id != ADMIN_CHAT_ID:
        if not BOT_ENABLED:
            await update.message.reply_text(BOT_OFF_TEXT)
            return
        if not await is_member(context, user.id):
            await update.message.reply_text(JOIN_TEXT, reply_markup=join_kb())
            return
    USER_NAMES[user.id] = user.full_name or (f"@{user.username}" if user.username else "بدون نام")
    await register_user_and_referral(user.id, context)
    await update.message.reply_text(WELCOME_TEXT, reply_markup=main_kb(chat_id == ADMIN_CHAT_ID))

async def text_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    text = update.message.text
    user = update.effective_user
    chat_id = update.effective_chat.id
    state = context.user_data.get("state")

    
    if chat_id == ADMIN_CHAT_ID and chat_id in ADMIN_WAITING_REJECT:
        req_id = ADMIN_WAITING_REJECT.pop(chat_id)
        req = TOPUP_REQUESTS.pop(req_id, None)
        if req:
            refund_daily_limit(req["user_id"], req["amount"])
            try:
                await context.bot.send_message(req["chat_id"], rejected_text(text))
            except Exception:
                logging.exception("خطا در ارسال پیام رد به کاربر")
        await update.message.reply_text("دلیل رد برای کاربر ارسال شد.")
        save_data()
        return

    
    if chat_id == ADMIN_CHAT_ID and chat_id in ADMIN_WAITING_SUPPORT_REPLY:
        ticket_id = ADMIN_WAITING_SUPPORT_REPLY.pop(chat_id)
        ticket = SUPPORT_TICKETS.pop(ticket_id, None)
        if ticket:
            try:
                await context.bot.send_message(
                    ticket["chat_id"],
                    f"📩 پاسخ پشتیبانی:\n\n{text}",
                    reply_to_message_id=ticket["message_id"],
                )
            except Exception:
                logging.exception("خطا در ارسال پاسخ پشتیبانی به کاربر")
        await update.message.reply_text("پاسخ شما برای کاربر ارسال شد.")
        return

    
    if chat_id != ADMIN_CHAT_ID and not BOT_ENABLED:
        await update.message.reply_text(BOT_OFF_TEXT)
        return

    
    if chat_id != ADMIN_CHAT_ID and not await is_member(context, user.id):
        context.user_data.clear()
        await update.message.reply_text(JOIN_TEXT, reply_markup=join_kb())
        return

    USER_NAMES[user.id] = user.full_name or (f"@{user.username}" if user.username else "بدون نام")
    await register_user_and_referral(user.id, context)  

    
    if chat_id == ADMIN_CHAT_ID:
        if state == "admin_awaiting_broadcast_text":
            context.user_data["broadcast_text"] = text
            context.user_data["state"] = None
            await update.message.reply_text(
                broadcast_confirm_text(text), reply_markup=admin_broadcast_confirm_kb()
            )
            return

        if state == "admin_awaiting_stars_price":
            raw = text.replace(",", "").strip()
            if not raw.isdigit():
                await update.message.reply_text(
                    "لطفا فقط عدد وارد کن.", reply_markup=admin_stars_price_ask_kb()
                )
                return
            global STARS_PRICE
            STARS_PRICE = int(raw)
            context.user_data["state"] = None
            save_data()
            await update.message.reply_text(
                admin_stars_price_text(), reply_markup=admin_stars_price_kb()
            )
            return

        if state == "admin_awaiting_ton_price":
            raw = text.replace(",", "").strip()
            if not raw.isdigit():
                await update.message.reply_text(
                    "لطفا فقط عدد وارد کن.", reply_markup=admin_ton_price_ask_kb()
                )
                return
            global TON_PRICE
            TON_PRICE = round(int(raw) / MIN_TON)
            context.user_data["state"] = None
            save_data()
            await update.message.reply_text(
                admin_ton_price_text(), reply_markup=admin_ton_price_kb()
            )
            return

        if state and state.startswith("admin_awaiting_premium_price_"):
            plan = state[len("admin_awaiting_premium_price_"):]
            raw = text.replace(",", "").strip()
            if not raw.isdigit():
                await update.message.reply_text(
                    "لطفا فقط عدد وارد کن.", reply_markup=admin_premium_price_ask_kb()
                )
                return
            PREMIUM_PRICES[plan] = int(raw)
            context.user_data["state"] = None
            save_data()
            await update.message.reply_text(
                admin_premium_price_text(), reply_markup=admin_premium_price_kb()
            )
            return

        if state and state.startswith("admin_awaiting_gift_price_"):
            key = state[len("admin_awaiting_gift_price_"):]
            raw = text.replace(",", "").strip()
            if not raw.isdigit():
                await update.message.reply_text(
                    "لطفا فقط عدد وارد کن.", reply_markup=admin_gift_price_ask_kb(key)
                )
                return
            GIFTS[key]["price"] = int(raw)
            context.user_data["state"] = None
            save_data()
            await update.message.reply_text(
                admin_gift_price_detail_text(key), reply_markup=admin_gift_price_detail_kb(key)
            )
            return

        if state == "admin_awaiting_daily_limit":
            raw = text.replace(",", "").strip()
            if not raw.isdigit() or int(raw) <= 0:
                await update.message.reply_text(
                    "لطفا فقط یک عدد مثبت وارد کن.", reply_markup=admin_daily_limit_ask_kb()
                )
                return
            global DAILY_LIMIT
            DAILY_LIMIT = int(raw)
            context.user_data["state"] = None
            save_data()
            await update.message.reply_text(
                admin_daily_limit_text(), reply_markup=admin_daily_limit_kb()
            )
            return

        if state and state.startswith("admin_awaiting_profit_"):
            product = state[len("admin_awaiting_profit_"):]
            raw = text.replace(",", "").replace("%", "").strip()
            if not raw.replace(".", "").isdigit() or float(raw) < 0:
                await update.message.reply_text(
                    "لطفا فقط یک عدد مثبت وارد کن (مثال: 10).",
                    reply_markup=admin_profit_ask_kb(product),
                )
                return
            PROFIT_PERCENT[product] = round(float(raw), 2)
            recalc_prices()
            context.user_data["state"] = None
            save_data()
            text_fn = {
                "stars": admin_profit_stars_text,
                "ton": admin_profit_ton_text,
                "gift": admin_profit_gift_text,
                "premium": admin_profit_premium_text,
            }.get(product, admin_profit_stars_text)
            await update.message.reply_text(text_fn(), reply_markup=admin_profit_detail_kb(product))
            return

    
    if state == "awaiting_username":
        username = text.strip()
        if not username.startswith("@"):
            username = "@" + username
        context.user_data.setdefault("pending_premium", {})["username"] = username
        context.user_data["state"] = None
        await update.message.reply_text(
            confirm_username_text(username), reply_markup=confirm_username_kb()
        )
        return

    if state == "awaiting_topup_amount":
        raw = text.replace(",", "").replace(" ", "")
        if not raw.isdigit():
            await update.message.reply_text("لطفا فقط عدد مبلغ رو بفرست. مثال: 40000")
            return
        amount = int(raw)
        remaining = get_daily_remaining(user.id)
        if amount > remaining:
            await update.message.reply_text(
                daily_limit_exceeded_text(remaining), reply_markup=toman_kb()
            )
            return
        use_daily_limit(user.id, amount)
        context.user_data["pending_topup"] = {"amount": amount}
        context.user_data["state"] = None
        await update.message.reply_text(
            card_details_text(amount), reply_markup=card_details_kb(), parse_mode="HTML"
        )
        return

    if state == "awaiting_receipt_photo":
        await update.message.reply_text("لطفا رسید پرداخت رو به صورت عکس ارسال کن 📷")
        return

    if state == "awaiting_stars_amount":
        raw = text.replace(",", "").strip()
        if not raw.isdigit():
            await update.message.reply_text(
                "لطفا فقط عدد وارد کن. مثال: 100",
                reply_markup=stars_min_error_kb(),
            )
            return
        count = int(raw)
        if count < STARS_MIN:
            await update.message.reply_text(
                STARS_MIN_ERROR_TEXT,
                reply_markup=stars_min_error_kb(),
            )
            return
        context.user_data["pending_stars"] = {"count": count}
        context.user_data["state"] = "awaiting_stars_username"
        await update.message.reply_text(ASK_USERNAME_TEXT, reply_markup=ask_stars_username_kb())
        return

    if state == "awaiting_stars_username":
        username = text.strip()
        if not username.startswith("@"):
            username = "@" + username
        context.user_data["pending_stars"]["username"] = username
        context.user_data["state"] = None
        await update.message.reply_text(
            confirm_username_text(username), reply_markup=confirm_stars_username_kb()
        )
        return

    if state == "awaiting_gift_username":
        username = text.strip()
        if not username.startswith("@"):
            username = "@" + username
        context.user_data["pending_gift"]["username"] = username
        context.user_data["state"] = None
        await update.message.reply_text(
            confirm_username_text(username), reply_markup=confirm_gift_username_kb()
        )
        return

    if state == "awaiting_gift_comment":
        comment = text.strip()
        g = context.user_data["pending_gift"]
        g["comment"] = comment
        context.user_data["state"] = None
        key = g["key"]
        price = g.get("price", GIFTS[key]["price"])
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(g, user.id)
        await update.message.reply_text(
            gift_invoice_text(key, g["username"], g["hidden"], comment, price, disc, final),
            reply_markup=gift_invoice_kb(g["hidden"], True, g.get("discount_applied", False)),
        )
        return

    if state == "awaiting_referral_username":
        username = text.strip()
        if not username.startswith("@"):
            username = "@" + username
        context.user_data["pending_referral_reward"]["username"] = username
        context.user_data["state"] = None
        await update.message.reply_text(
            confirm_username_text(username), reply_markup=confirm_referral_username_kb()
        )
        return

    if state == "awaiting_ton_amount":
        raw = text.replace(",", "").strip()
        try:
            amount = float(raw)
        except ValueError:
            await update.message.reply_text(f"لطفا عدد وارد کن. مثال: 0.5 یا 2")
            return
        if amount < MIN_TON:
            await update.message.reply_text(f"حداقل سفارش {MIN_TON} TON است.")
            return
        context.user_data["pending_ton"] = {"amount": amount}
        context.user_data["state"] = "awaiting_ton_wallet"
        await update.message.reply_text(TON_WALLET_TEXT, reply_markup=ton_wallet_back_kb())
        return

    if state == "awaiting_ton_wallet":
        wallet = text.strip()
        if not (wallet.startswith("UQ") or wallet.startswith("EQ")) or len(wallet) < 40:
            await update.message.reply_text(
                "آدرس ولت نامعتبر به نظر می‌رسد.\n"
                "آدرس باید با UQ یا EQ شروع شود و کامل باشد."
            )
            return
        context.user_data["pending_ton"]["wallet"] = wallet
        context.user_data["state"] = None
        await update.message.reply_text(TON_MEMO_QUESTION_TEXT, reply_markup=ton_memo_kb())
        return

    if state == "awaiting_ton_memo":
        memo = text.strip()
        ton = context.user_data["pending_ton"]
        ton["memo"] = memo
        ton["price"] = int(ton["amount"] * TON_PRICE)
        ton["discount_applied"] = False
        context.user_data["state"] = None
        price = ton["price"]
        disc = get_user(user.id)["discount_balance"]
        await update.message.reply_text(
            ton_invoice_text(ton["amount"], ton["wallet"], memo, price, disc, price),
            reply_markup=ton_invoice_kb(False),
        )
        return

    if state == "awaiting_support_message":
        ticket_id = new_req_id()
        SUPPORT_TICKETS[ticket_id] = {
            "user_id": user.id,
            "chat_id": chat_id,
            "message_id": update.message.message_id,
        }
        await update.message.reply_text(SUPPORT_SENT_CONFIRM_TEXT, reply_markup=main_kb(chat_id == ADMIN_CHAT_ID))
        context.user_data["state"] = None
        if ADMIN_CHAT_ID:
            try:
                await context.bot.send_message(
                    ADMIN_CHAT_ID,
                    admin_support_text(user, text),
                    reply_markup=admin_support_kb(ticket_id),
                )
            except Exception:
                logging.exception("خطا در ارسال پیام پشتیبانی به ادمین")
        return

    if state == "awaiting_tracking_code":
        code = text.strip()
        order = ORDERS.get(code)
        if not order:
            await update.message.reply_text(TRACK_NOT_FOUND_TEXT, reply_markup=track_ask_code_kb())
            return
        context.user_data["state"] = None
        await update.message.reply_text(order_status_text(order))
        return

    
    if text == "🛒 خرید محصول":
        await update.message.reply_text(PRODUCT_TEXT, reply_markup=product_kb())
        return

    if text == "👛 شارژ موجودی":
        balance = get_user(user.id)["balance"]
        remaining = get_daily_remaining(user.id)
        await update.message.reply_text(wallet_text(balance, remaining), reply_markup=wallet_kb())
        return

    if text == "👤 حساب کاربری":
        await update.message.reply_text(account_text(user), parse_mode="HTML")
        return

    if text == "🔗 لینک دعوت من":
        await update.message.reply_text(referral_text(user), reply_markup=invite_kb())
        return

    if text == "📞 پشتیبانی":
        await update.message.reply_text(SUPPORT_TEXT, reply_markup=support_kb())
        return

    if text == "📋 پیگیری سفارش":
        await update.message.reply_text(TRACK_TEXT, reply_markup=track_kb())
        return

    if text == "🔽 چطور میتوانم اعتماد کنم؟":
        await update.message.reply_text(TRUST_TEXT)
        return

    if text == "🛠 پنل ادمین" and chat_id == ADMIN_CHAT_ID:
        await update.message.reply_text(admin_panel_text(), reply_markup=admin_panel_kb())
        return

    reply = MAIN_PLACEHOLDER.get(text, "این بخش هنوز تکمیل نشده.")
    await update.message.reply_text(reply)

async def photo_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    if context.user_data.get("state") != "awaiting_receipt_photo":
        return
    user = update.effective_user
    pending = context.user_data.get("pending_topup")
    if not pending:
        await update.message.reply_text("درخواست شارژ پیدا نشد، لطفا دوباره شروع کن.")
        context.user_data["state"] = None
        return

    amount = pending["amount"]
    req_id = new_req_id()
    TOPUP_REQUESTS[req_id] = {
        "user_id": user.id,
        "chat_id": update.effective_chat.id,
        "amount": amount,
    }

    if ADMIN_CHAT_ID:
        try:
            await context.bot.send_photo(
                ADMIN_CHAT_ID,
                update.message.photo[-1].file_id,
                caption=admin_topup_caption(user, amount, req_id),
                reply_markup=admin_topup_kb(req_id),
            )
        except Exception:
            logging.exception("خطا در ارسال رسید به ادمین")

    context.user_data["state"] = None
    context.user_data["pending_topup"] = None
    await update.message.reply_text(RECEIPT_SENT_TEXT)

async def callback_router(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global BOT_ENABLED
    global REFERRAL_POINTS_ENABLED
    global DAILY_LIMIT
    query = update.callback_query
    data = query.data
    user = update.effective_user
    chat_id = update.effective_chat.id
    await query.answer()

    
    if data == "check_membership":
        if await is_member(context, user.id):
            USER_NAMES[user.id] = user.full_name or (f"@{user.username}" if user.username else "بدون نام")
            await register_user_and_referral(user.id, context)
            await query.edit_message_text(JOIN_CONFIRMED_TEXT)
            await context.bot.send_message(chat_id, WELCOME_TEXT, reply_markup=main_kb(chat_id == ADMIN_CHAT_ID))
        else:
            await query.answer(
                "هنوز در کانال عضو نشدید! لطفا ابتدا عضو شوید سپس دوباره امتحان کنید.",
                show_alert=True,
            )
        return

    if chat_id != ADMIN_CHAT_ID:
        if not BOT_ENABLED:
            await query.answer(BOT_OFF_TEXT, show_alert=True)
            return
        if not await is_member(context, user.id):
            context.user_data.clear()
            await query.edit_message_text(JOIN_TEXT, reply_markup=join_kb())
            return

    USER_NAMES[user.id] = user.full_name or (f"@{user.username}" if user.username else "بدون نام")
    await register_user_and_referral(user.id, context)  

    if data.startswith("admin_") and chat_id != ADMIN_CHAT_ID:
        return

    
    if data == "back_to_welcome":
        context.user_data.clear()
        await query.edit_message_text(WELCOME_TEXT)
        return

    if data == "back_to_products":
        context.user_data["state"] = None
        await query.edit_message_text(PRODUCT_TEXT, reply_markup=product_kb())
        return

    
    
    if data == "product_gift_stars":
        await query.edit_message_text(GIFT_LIST_TEXT, reply_markup=gift_list_kb())
        return

    if data == "gift_back_to_list":
        context.user_data["state"] = None
        await query.edit_message_text(GIFT_LIST_TEXT, reply_markup=gift_list_kb())
        return

    if data.startswith("gift_select_"):
        key = data[len("gift_select_"):]
        if key not in GIFTS:
            return
        context.user_data["pending_gift"] = {
            "key": key,
            "username": None,
            "hidden": True,
            "comment": None,
        }
        context.user_data["state"] = "awaiting_gift_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_gift_username_kb())
        return

    if data == "gift_username_self":
        if user.username:
            username = "@" + user.username
            context.user_data["pending_gift"]["username"] = username
            context.user_data["state"] = None
            await query.edit_message_text(
                confirm_username_text(username), reply_markup=confirm_gift_username_kb()
            )
        else:
            context.user_data["state"] = "awaiting_gift_username"
            await query.edit_message_text(
                "شما در تلگرام یوزرنیم ندارید.\n"
                "لطفا یوزرنیم شخص مورد نظر را با @ ارسال کنید.",
                reply_markup=InlineKeyboardMarkup([[
                    InlineKeyboardButton("🔙 بازگشت", callback_data="gift_back_to_list"),
                ]]),
            )
        return

    if data == "gift_confirm_no":
        context.user_data["state"] = "awaiting_gift_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_gift_username_kb())
        return

    if data == "gift_confirm_yes":
        g = context.user_data.get("pending_gift", {})
        key = g.get("key", "")
        username = g.get("username", "-")
        g["price"] = GIFTS[key]["price"]
        g["discount_applied"] = False
        price = g["price"]
        disc = get_user(user.id)["discount_balance"]
        await query.edit_message_text(
            gift_invoice_text(key, username, True, None, price, disc, price),
            reply_markup=gift_invoice_kb(True, False, False),
        )
        return

    if data == "gift_toggle_hide":
        g = context.user_data.get("pending_gift", {})
        g["hidden"] = not g.get("hidden", True)
        key = g["key"]
        username = g["username"]
        comment = g.get("comment")
        price = g.get("price", GIFTS[key]["price"])
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(g, user.id)
        await query.edit_message_text(
            gift_invoice_text(key, username, g["hidden"], comment, price, disc, final),
            reply_markup=gift_invoice_kb(g["hidden"], bool(comment), g.get("discount_applied", False)),
        )
        return

    if data == "gift_comment_type":
        await query.edit_message_text(
            GIFT_COMMENT_TYPE_TEXT, reply_markup=gift_comment_type_kb()
        )
        return

    if data == "gift_comment_back_to_invoice":
        g = context.user_data.get("pending_gift", {})
        key = g["key"]
        username = g["username"]
        comment = g.get("comment")
        price = g.get("price", GIFTS[key]["price"])
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(g, user.id)
        await query.edit_message_text(
            gift_invoice_text(key, username, g["hidden"], comment, price, disc, final),
            reply_markup=gift_invoice_kb(g["hidden"], bool(comment), g.get("discount_applied", False)),
        )
        return

    if data == "gift_comment_free":
        context.user_data["state"] = "awaiting_gift_comment"
        await query.edit_message_text(
            GIFT_COMMENT_INPUT_TEXT, reply_markup=gift_comment_input_kb()
        )
        return

    if data == "gift_comment_input_back":
        context.user_data["state"] = None
        await query.edit_message_text(
            GIFT_COMMENT_TYPE_TEXT, reply_markup=gift_comment_type_kb()
        )
        return

    if data == "gift_del_comment":
        g = context.user_data.get("pending_gift", {})
        g["comment"] = None
        key = g["key"]
        username = g["username"]
        price = g.get("price", GIFTS[key]["price"])
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(g, user.id)
        await query.edit_message_text(
            gift_invoice_text(key, username, g["hidden"], None, price, disc, final),
            reply_markup=gift_invoice_kb(g["hidden"], False, g.get("discount_applied", False)),
        )
        return

    if data == "gift_invoice_discount":
        g = context.user_data.get("pending_gift", {})
        g["discount_applied"] = not g.get("discount_applied", False)
        key = g["key"]
        username = g["username"]
        comment = g.get("comment")
        price = g.get("price", GIFTS[key]["price"])
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(g, user.id)
        await query.edit_message_text(
            gift_invoice_text(key, username, g["hidden"], comment, price, disc, final),
            reply_markup=gift_invoice_kb(g["hidden"], bool(comment), g["discount_applied"]),
        )
        return

    if data == "gift_invoice_cancel":
        context.user_data["pending_gift"] = None
        context.user_data["state"] = None
        await query.edit_message_text(CANCELLED_TEXT)
        return

    if data == "gift_invoice_confirm":
        g = context.user_data.get("pending_gift", {})
        key = g.get("key", "")
        username = g.get("username", "-")
        hidden = g.get("hidden", True)
        comment = g.get("comment")
        price = g.get("price", GIFTS[key]["price"])
        final = compute_final_price(g, user.id)
        u = get_user(user.id)
        if u["balance"] >= final:
            u["balance"] -= final
            consume_discount_if_applied(g, user.id)
            apply_referral_commission(user.id, final)
            order_id = new_id()
            u["orders"].append(
                f"🎁 خرید {gift_label(key)} - به مبلغ {fmt(final)} تومان"
            )
            u["orders"] = u["orders"][-50:]
            context.user_data["pending_gift"] = None
            ORDERS[order_id] = {
                "type": "gift",
                "chat_id": update.effective_chat.id,
                "user_id": user.id,
                "buyer_name": user.full_name,
                "key": key,
                "username": username,
                "price": final,
                "status": "pending",
                "created_at": persian_now_str(),
            }
            save_data()
            await query.edit_message_text(gift_success_text(key, username, final, order_id))
            if ADMIN_CHAT_ID:
                try:
                    await context.bot.send_message(
                        ADMIN_CHAT_ID,
                        admin_gift_text(user, key, username, hidden, comment, final, order_id),
                        reply_markup=admin_order_kb(order_id),
                        parse_mode="HTML",
                    )
                except Exception:
                    logging.exception("خطا در ارسال سفارش گیفت به ادمین")
        else:
            shortfall = final - u["balance"]
            context.user_data["topup_origin"] = "invoice"
            context.user_data["last_invoice_shortfall_price"] = final
            await query.edit_message_text(insufficient_text(shortfall), reply_markup=insufficient_kb())
        return

    
    if data == "product_premium":
        await query.edit_message_text(PREMIUM_TEXT, reply_markup=premium_kb())
        return

    if data in ("premium_3", "premium_6", "premium_12"):
        plan = data.split("_")[1]
        context.user_data["pending_premium"] = {
            "plan": plan,
            "price": PREMIUM_PRICES[plan],
        }
        context.user_data["state"] = "awaiting_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_username_kb())
        return

    if data == "back_to_premium":
        context.user_data["state"] = None
        await query.edit_message_text(PREMIUM_TEXT, reply_markup=premium_kb())
        return

    if data == "username_self":
        if user.username:
            username = "@" + user.username
            context.user_data.setdefault("pending_premium", {})["username"] = username
            context.user_data["state"] = None
            await query.edit_message_text(
                confirm_username_text(username), reply_markup=confirm_username_kb()
            )
        else:
            context.user_data["state"] = "awaiting_username"
            await query.edit_message_text(
                "شما در تلگرام یوزرنیم ندارید.\n"
                "لطفا یوزرنیم شخص مورد نظر را با @ ارسال کنید.",
                reply_markup=InlineKeyboardMarkup([[
                    InlineKeyboardButton("🔙 بازگشت", callback_data="back_to_ask_username"),
                ]]),
            )
        return

    if data == "back_to_ask_username":
        context.user_data["state"] = "awaiting_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_username_kb())
        return

    if data == "confirm_no":
        context.user_data["state"] = "awaiting_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_username_kb())
        return

    if data == "confirm_yes":
        order = context.user_data.get("pending_premium", {})
        order["discount_applied"] = False
        plan = order.get("plan", "?")
        price = order.get("price", 0)
        username = order.get("username", "-")
        disc = get_user(user.id)["discount_balance"]
        await query.edit_message_text(
            premium_invoice_text(plan, price, username, disc, price),
            reply_markup=invoice_kb(False),
        )
        return

    if data == "invoice_discount":
        order = context.user_data.get("pending_premium", {})
        order["discount_applied"] = not order.get("discount_applied", False)
        plan = order.get("plan", "?")
        price = order.get("price", 0)
        username = order.get("username", "-")
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(order, user.id)
        await query.edit_message_text(
            premium_invoice_text(plan, price, username, disc, final),
            reply_markup=invoice_kb(order["discount_applied"]),
        )
        return

    if data == "invoice_cancel":
        context.user_data["pending_premium"] = None
        context.user_data["state"] = None
        await query.edit_message_text(CANCELLED_TEXT)
        return

    if data == "invoice_confirm":
        order = context.user_data.get("pending_premium", {})
        plan = order.get("plan", "?")
        price = order.get("price", 0)
        username = order.get("username", "-")
        final = compute_final_price(order, user.id)
        u = get_user(user.id)
        if u["balance"] >= final:
            u["balance"] -= final
            consume_discount_if_applied(order, user.id)
            apply_referral_commission(user.id, final)
            order_id = new_id()
            u["orders"].append(
                f"💎 پرمیوم {plan} ماهه - به مبلغ {fmt(final)} تومان"
            )
            u["orders"] = u["orders"][-50:]
            context.user_data["pending_premium"] = None
            
            ORDERS[order_id] = {
                "type": "premium",
                "chat_id": update.effective_chat.id,
                "user_id": user.id,
                "buyer_name": user.full_name,
                "plan": plan,
                "username": username,
                "price": final,
                "status": "pending",
                "created_at": persian_now_str(),
            }
            save_data()
            await query.edit_message_text(premium_success_text(plan, username, final, order_id))
            if ADMIN_CHAT_ID:
                try:
                    await context.bot.send_message(
                        ADMIN_CHAT_ID,
                        admin_premium_text(user, plan, username, final, order_id),
                        reply_markup=admin_order_kb(order_id),
                    )
                except Exception:
                    logging.exception("خطا در ارسال سفارش پرمیوم به ادمین")
        else:
            shortfall = final - u["balance"]
            context.user_data["topup_origin"] = "invoice"
            context.user_data["last_invoice_shortfall_price"] = final
            await query.edit_message_text(
                insufficient_text(shortfall), reply_markup=insufficient_kb()
            )
        return

    
    if data == "wallet_increase":
        await query.edit_message_text(WALLET_INCREASE_TEXT, reply_markup=wallet_increase_kb())
        return

    if data == "wallet_back":
        balance = get_user(user.id)["balance"]
        remaining = get_daily_remaining(user.id)
        await query.edit_message_text(wallet_text(balance, remaining), reply_markup=wallet_kb())
        return

    if data in ("topup_from_invoice", "topup_from_wallet"):
        context.user_data["topup_origin"] = "invoice" if data == "topup_from_invoice" else "wallet"
        context.user_data["state"] = "awaiting_topup_amount"
        remaining = get_daily_remaining(user.id)
        await query.edit_message_text(toman_payment_text(remaining), reply_markup=toman_kb())
        return

    if data == "topup_back":
        context.user_data["state"] = None
        origin = context.user_data.get("topup_origin")
        if origin == "invoice":
            price = context.user_data.get("last_invoice_shortfall_price", 0)
            shortfall = max(price - get_user(user.id)["balance"], 0)
            await query.edit_message_text(insufficient_text(shortfall), reply_markup=insufficient_kb())
        else:
            await query.edit_message_text(WALLET_INCREASE_TEXT, reply_markup=wallet_increase_kb())
        return

    if data == "card_back":
        context.user_data["state"] = "awaiting_topup_amount"
        remaining = get_daily_remaining(user.id)
        await query.edit_message_text(toman_payment_text(remaining), reply_markup=toman_kb())
        return

    if data == "send_receipt":
        context.user_data["state"] = "awaiting_receipt_photo"
        await query.edit_message_text(RECEIPT_PROMPT_TEXT, reply_markup=receipt_prompt_kb())
        return

    if data == "receipt_back":
        pending = context.user_data.get("pending_topup") or {}
        amount = pending.get("amount", 0)
        context.user_data["state"] = None
        await query.edit_message_text(
            card_details_text(amount), reply_markup=card_details_kb(), parse_mode="HTML"
        )
        return

    
    if data.startswith("topup_approve_") or data.startswith("topup_reject_"):
        req_id = data.split("_", 2)[2]
        req = TOPUP_REQUESTS.get(req_id)
        if not req:
            await query.edit_message_caption(caption="این درخواست قبلا پردازش شده.")
            return
        if data.startswith("topup_approve_"):
            u = get_user(req["user_id"])
            u["balance"] += req["amount"]
            TOPUP_REQUESTS.pop(req_id, None)
            save_data()
            old_cap = query.message.caption or ""
            await query.edit_message_caption(caption=old_cap + "\n\n✅ تایید شد")
            try:
                await context.bot.send_message(
                    req["chat_id"],
                    approved_text(u["balance"]),
                    reply_markup=buy_product_kb(),
                )
            except Exception:
                logging.exception("خطا در ارسال تایید به کاربر")
        else:
            ADMIN_WAITING_REJECT[update.effective_chat.id] = req_id
            old_cap = query.message.caption or ""
            await query.edit_message_caption(caption=old_cap + "\n\n⏳ در انتظار دلیل رد...")
            await context.bot.send_message(
                update.effective_chat.id,
                "لطفا دلیل رد را در یک پیام بفرست تا برای کاربر ارسال شود."
            )
        return

    
    if data.startswith("order_done_"):
        order_id = data[len("order_done_"):]
        order = ORDERS.get(order_id)
        if not order or order.get("status") == "done":
            await query.edit_message_text((query.message.text or "") + "\n\n✅ قبلاً پردازش شده")
            return
        order["status"] = "done"
        save_data()
        
        await query.edit_message_text(
            (query.message.text or "") + "\n\n✅ انجام شد"
        )
        
        try:
            if order["type"] == "premium":
                msg = premium_done_text(
                    order["plan"], order["username"], order["price"], order_id
                )
            elif order["type"] == "ton":
                msg = ton_done_text(
                    order["amount"], order["wallet"], order["price"], order_id
                )
            elif order["type"] == "stars":
                msg = stars_done_text(
                    order["count"], order["username"], order["price"], order_id
                )
            elif order["type"] == "referral_reward":
                msg = referral_reward_done_text(
                    order["key"], order["username"], order_id
                )
            else:  
                msg = gift_done_text(
                    order["key"], order["username"], order["price"], order_id
                )
            await context.bot.send_message(
                order["chat_id"], msg, reply_markup=buy_product_kb()
            )
        except Exception:
            logging.exception("خطا در ارسال پیام انجام سفارش به کاربر")

        
        try:
            buyer_name = order.get("buyer_name") or "کاربر"
            await context.bot.send_message(
                f"@{REPORTS_CHANNEL}",
                report_text(buyer_name, order),
                reply_markup=report_kb(),
            )
        except Exception:
            logging.exception("خطا در ارسال گزارش به کانال (احتمالا ربات ادمین کانال نیست)")
        return

    
    if data == "track_have_code":
        context.user_data["state"] = "awaiting_tracking_code"
        await query.edit_message_text(TRACK_ASK_CODE_TEXT, reply_markup=track_ask_code_kb())
        return

    if data == "track_back":
        context.user_data["state"] = None
        await query.edit_message_text(TRACK_TEXT, reply_markup=track_kb())
        return

    
    if data == "referral_back":
        context.user_data["state"] = None
        await query.edit_message_text(referral_text(user), reply_markup=invite_kb())
        return

    if data == "referral_claim_reward":
        if not REFERRAL_POINTS_ENABLED:
            await query.answer(
                "❌ سیستم جایزه دعوت در حال حاضر توسط مدیریت غیرفعال شده است.",
                show_alert=True,
            )
            return
        if get_user(user.id)["score"] < REFERRAL_POINTS_REQUIRED:
            await query.answer(
                f"هنوز امتیاز کافی نداری! برای دریافت جایزه به {REFERRAL_POINTS_REQUIRED} امتیاز رفرال نیاز داری.",
                show_alert=True,
            )
            return
        await query.edit_message_text(
            referral_reward_select_text(), reply_markup=referral_reward_select_kb()
        )
        return

    if data == "referral_reward_select_back":
        context.user_data["state"] = None
        await query.edit_message_text(
            referral_reward_select_text(), reply_markup=referral_reward_select_kb()
        )
        return

    if data.startswith("referral_reward_") and data[len("referral_reward_"):] in REFERRAL_REWARD_GIFTS:
        key = data[len("referral_reward_"):]
        context.user_data["pending_referral_reward"] = {"key": key, "username": None}
        context.user_data["state"] = "awaiting_referral_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_referral_username_kb())
        return

    if data == "referral_username_self":
        if user.username:
            username = "@" + user.username
            context.user_data["pending_referral_reward"]["username"] = username
            context.user_data["state"] = None
            await query.edit_message_text(
                confirm_username_text(username), reply_markup=confirm_referral_username_kb()
            )
        else:
            context.user_data["state"] = "awaiting_referral_username"
            await query.edit_message_text(
                "شما در تلگرام یوزرنیم ندارید.\n"
                "لطفا یوزرنیم شخص مورد نظر را با @ ارسال کنید.",
                reply_markup=InlineKeyboardMarkup([[
                    InlineKeyboardButton("🔙 بازگشت", callback_data="referral_reward_select_back"),
                ]]),
            )
        return

    if data == "referral_confirm_no":
        context.user_data["state"] = "awaiting_referral_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_referral_username_kb())
        return

    if data == "referral_confirm_yes":
        r = context.user_data.get("pending_referral_reward", {})
        key = r.get("key", "")
        username = r.get("username", "-")
        await query.edit_message_text(
            referral_reward_invoice_text(key, username), reply_markup=referral_reward_invoice_kb()
        )
        return

    if data == "referral_invoice_cancel":
        context.user_data["pending_referral_reward"] = None
        context.user_data["state"] = None
        await query.edit_message_text(CANCELLED_TEXT)
        return

    if data == "referral_invoice_confirm":
        r = context.user_data.get("pending_referral_reward", {})
        key = r.get("key", "")
        username = r.get("username", "-")
        u = get_user(user.id)
        if not REFERRAL_POINTS_ENABLED:
            context.user_data["pending_referral_reward"] = None
            context.user_data["state"] = None
            await query.answer(
                "❌ سیستم جایزه دعوت در حال حاضر توسط مدیریت غیرفعال شده است.",
                show_alert=True,
            )
            await query.edit_message_text(referral_text(user), reply_markup=invite_kb())
            return
        if u["score"] < REFERRAL_POINTS_REQUIRED:
            context.user_data["pending_referral_reward"] = None
            context.user_data["state"] = None
            await query.edit_message_text(
                "❌ امتیاز رفرال شما کافی نیست.", reply_markup=invite_kb()
            )
            return
        u["score"] -= REFERRAL_POINTS_REQUIRED
        order_id = new_id()
        u["orders"].append(f"🎁 جایزه رفرال - {gift_label(key)} - رایگان")
        u["orders"] = u["orders"][-50:]
        context.user_data["pending_referral_reward"] = None
        ORDERS[order_id] = {
            "type": "referral_reward",
            "chat_id": update.effective_chat.id,
            "user_id": user.id,
            "buyer_name": user.full_name,
            "key": key,
            "username": username,
            "price": 0,
            "status": "pending",
            "created_at": persian_now_str(),
        }
        save_data()
        await query.edit_message_text(referral_reward_success_text(key, username, order_id))
        if ADMIN_CHAT_ID:
            try:
                await context.bot.send_message(
                    ADMIN_CHAT_ID,
                    admin_referral_reward_text(user, key, username, order_id),
                    reply_markup=admin_order_kb(order_id),
                )
            except Exception:
                logging.exception("خطا در ارسال جایزه رفرال به ادمین")
        return

    
    if data == "support_indirect":
        context.user_data["state"] = "awaiting_support_message"
        await query.edit_message_text(SUPPORT_INDIRECT_TEXT, reply_markup=support_indirect_kb())
        return

    if data == "support_back":
        context.user_data["state"] = None
        await query.edit_message_text(SUPPORT_TEXT, reply_markup=support_kb())
        return

    if data.startswith("support_reply_"):
        ticket_id = data[len("support_reply_"):]
        if ticket_id not in SUPPORT_TICKETS:
            await query.answer("این تیکت قبلا پاسخ داده شده یا پیدا نشد.", show_alert=True)
            return
        ADMIN_WAITING_SUPPORT_REPLY[update.effective_chat.id] = ticket_id
        await query.edit_message_text(
            (query.message.text or "") + "\n\nپاسخ خود را بگید"
        )
        return

    
    if data == "product_stars":
        context.user_data["state"] = "awaiting_stars_amount"
        context.user_data["pending_stars"] = {}
        await query.edit_message_text(STARS_BUY_TEXT, reply_markup=stars_back_kb())
        return

    if data == "stars_back_to_amount":
        context.user_data["state"] = "awaiting_stars_amount"
        context.user_data["pending_stars"] = {}
        await query.edit_message_text(STARS_BUY_TEXT, reply_markup=stars_back_kb())
        return

    if data == "stars_username_self":
        if user.username:
            username = "@" + user.username
            context.user_data.setdefault("pending_stars", {})["username"] = username
            context.user_data["state"] = None
            await query.edit_message_text(
                confirm_username_text(username), reply_markup=confirm_stars_username_kb()
            )
        else:
            context.user_data["state"] = "awaiting_stars_username"
            await query.edit_message_text(
                "شما در تلگرام یوزرنیم ندارید.\n"
                "لطفا یوزرنیم شخص مورد نظر را با @ ارسال کنید.",
                reply_markup=InlineKeyboardMarkup([[
                    InlineKeyboardButton("🔙 بازگشت", callback_data="stars_back_to_amount"),
                ]]),
            )
        return

    if data == "stars_confirm_no":
        context.user_data["state"] = "awaiting_stars_username"
        await query.edit_message_text(ASK_USERNAME_TEXT, reply_markup=ask_stars_username_kb())
        return

    if data == "stars_confirm_yes":
        s = context.user_data.get("pending_stars", {})
        count = s.get("count", 0)
        username = s.get("username", "-")
        s["price"] = count * STARS_PRICE
        s["discount_applied"] = False
        price = s["price"]
        disc = get_user(user.id)["discount_balance"]
        await query.edit_message_text(
            stars_invoice_text(count, username, price, disc, price),
            reply_markup=stars_invoice_kb(False),
        )
        return

    if data == "stars_invoice_discount":
        s = context.user_data.get("pending_stars", {})
        s["discount_applied"] = not s.get("discount_applied", False)
        count = s.get("count", 0)
        username = s.get("username", "-")
        price = s.get("price", count * STARS_PRICE)
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(s, user.id)
        await query.edit_message_text(
            stars_invoice_text(count, username, price, disc, final),
            reply_markup=stars_invoice_kb(s["discount_applied"]),
        )
        return

    if data == "stars_invoice_cancel":
        context.user_data["pending_stars"] = None
        context.user_data["state"] = None
        await query.edit_message_text(CANCELLED_TEXT)
        return

    if data == "stars_invoice_confirm":
        s = context.user_data.get("pending_stars", {})
        count = s.get("count", 0)
        username = s.get("username", "-")
        price = s.get("price", count * STARS_PRICE)
        final = compute_final_price(s, user.id)
        u = get_user(user.id)
        if u["balance"] >= final:
            u["balance"] -= final
            consume_discount_if_applied(s, user.id)
            apply_referral_commission(user.id, final)
            order_id = new_id()
            u["orders"].append(f"⭐️ خرید {count} استارز - به مبلغ {fmt(final)} تومان")
            u["orders"] = u["orders"][-50:]
            context.user_data["pending_stars"] = None
            ORDERS[order_id] = {
                "type": "stars",
                "chat_id": update.effective_chat.id,
                "user_id": user.id,
                "buyer_name": user.full_name,
                "count": count,
                "username": username,
                "price": final,
                "status": "pending",
                "created_at": persian_now_str(),
            }
            save_data()
            await query.edit_message_text(stars_success_text(count, username, final, order_id))
            if ADMIN_CHAT_ID:
                try:
                    await context.bot.send_message(
                        ADMIN_CHAT_ID,
                        admin_stars_text(user, count, username, final, order_id),
                        reply_markup=admin_order_kb(order_id),
                    )
                except Exception:
                    logging.exception("خطا در ارسال سفارش استارز به ادمین")
        else:
            shortfall = final - u["balance"]
            context.user_data["topup_origin"] = "invoice"
            context.user_data["last_invoice_shortfall_price"] = final
            await query.edit_message_text(insufficient_text(shortfall), reply_markup=insufficient_kb())
        return

    
    if data == "product_buy_ton":
        context.user_data["state"] = "awaiting_ton_amount"
        context.user_data["pending_ton"] = {}
        await query.edit_message_text(TON_BUY_TEXT, reply_markup=ton_back_kb())
        return

    if data == "ton_back_to_amount":
        context.user_data["state"] = "awaiting_ton_amount"
        context.user_data["pending_ton"] = {}
        await query.edit_message_text(TON_BUY_TEXT, reply_markup=ton_back_kb())
        return

    if data == "ton_memo_yes":
        context.user_data["state"] = "awaiting_ton_memo"
        await query.edit_message_text(TON_MEMO_INPUT_TEXT, reply_markup=ton_memo_input_kb())
        return

    if data == "ton_memo_input_back":
        context.user_data["state"] = None
        await query.edit_message_text(TON_MEMO_QUESTION_TEXT, reply_markup=ton_memo_kb())
        return

    if data == "ton_memo_skip":
        ton = context.user_data.get("pending_ton", {})
        amount = ton.get("amount", 0)
        wallet = ton.get("wallet", "-")
        ton["memo"] = "ندارد"
        ton["price"] = int(amount * TON_PRICE)
        ton["discount_applied"] = False
        price = ton["price"]
        disc = get_user(user.id)["discount_balance"]
        await query.edit_message_text(
            ton_invoice_text(amount, wallet, "ندارد", price, disc, price),
            reply_markup=ton_invoice_kb(False),
        )
        return

    if data == "ton_memo_back":
        context.user_data["state"] = "awaiting_ton_wallet"
        await query.edit_message_text(TON_WALLET_TEXT, reply_markup=ton_wallet_back_kb())
        return

    if data == "ton_invoice_discount":
        ton = context.user_data.get("pending_ton", {})
        ton["discount_applied"] = not ton.get("discount_applied", False)
        amount = ton.get("amount", 0)
        wallet = ton.get("wallet", "-")
        memo = ton.get("memo", "ندارد")
        price = ton.get("price", int(amount * TON_PRICE))
        disc = get_user(user.id)["discount_balance"]
        final = compute_final_price(ton, user.id)
        await query.edit_message_text(
            ton_invoice_text(amount, wallet, memo, price, disc, final),
            reply_markup=ton_invoice_kb(ton["discount_applied"]),
        )
        return

    if data == "ton_invoice_cancel":
        context.user_data["pending_ton"] = None
        context.user_data["state"] = None
        await query.edit_message_text(CANCELLED_TEXT)
        return

    if data == "ton_invoice_confirm":
        ton = context.user_data.get("pending_ton", {})
        amount = ton.get("amount", 0)
        wallet = ton.get("wallet", "-")
        memo = ton.get("memo", "ندارد")
        price = ton.get("price", int(amount * TON_PRICE))
        final = compute_final_price(ton, user.id)
        u = get_user(user.id)
        if u["balance"] >= final:
            u["balance"] -= final
            consume_discount_if_applied(ton, user.id)
            apply_referral_commission(user.id, final)
            order_id = new_id()
            u["orders"].append(
                f"💱 خرید ارز تون - به مبلغ {fmt(final)} تومان"
            )
            u["orders"] = u["orders"][-50:]
            context.user_data["pending_ton"] = None
            ORDERS[order_id] = {
                "type": "ton",
                "chat_id": update.effective_chat.id,
                "user_id": user.id,
                "buyer_name": user.full_name,
                "amount": amount,
                "wallet": wallet,
                "memo": memo,
                "price": final,
                "status": "pending",
                "created_at": persian_now_str(),
            }
            save_data()
            await query.edit_message_text(ton_success_text(amount, wallet, final, order_id))
            if ADMIN_CHAT_ID:
                try:
                    await context.bot.send_message(
                        ADMIN_CHAT_ID,
                        admin_ton_text(user, amount, wallet, memo, final, order_id),
                        reply_markup=admin_order_kb(order_id),
                        parse_mode="HTML",
                    )
                except Exception:
                    logging.exception("خطا در ارسال سفارش تون به ادمین")
        else:
            shortfall = final - u["balance"]
            context.user_data["topup_origin"] = "invoice"
            context.user_data["last_invoice_shortfall_price"] = final
            await query.edit_message_text(insufficient_text(shortfall), reply_markup=insufficient_kb())
        return

    
    if data == "admin_panel_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_panel_text(), reply_markup=admin_panel_kb())
        return

    if data == "admin_daily_limit":
        await query.edit_message_text(admin_daily_limit_text(), reply_markup=admin_daily_limit_kb())
        return

    if data == "admin_daily_limit_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_daily_limit_text(), reply_markup=admin_daily_limit_kb())
        return

    if data == "admin_change_daily_limit":
        context.user_data["state"] = "admin_awaiting_daily_limit"
        await query.edit_message_text(
            admin_ask_daily_limit_text(), reply_markup=admin_daily_limit_ask_kb()
        )
        return

    
    if data == "admin_profit_menu":
        await query.edit_message_text(admin_profit_menu_text(), reply_markup=admin_profit_menu_kb())
        return

    if data == "admin_profit_menu_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_profit_menu_text(), reply_markup=admin_profit_menu_kb())
        return

    if data == "admin_profit_stars":
        await query.edit_message_text(admin_profit_stars_text(), reply_markup=admin_profit_detail_kb("stars"))
        return

    if data == "admin_profit_ton":
        await query.edit_message_text(admin_profit_ton_text(), reply_markup=admin_profit_detail_kb("ton"))
        return

    if data == "admin_profit_gift":
        await query.edit_message_text(admin_profit_gift_text(), reply_markup=admin_profit_detail_kb("gift"))
        return

    if data == "admin_profit_premium":
        await query.edit_message_text(admin_profit_premium_text(), reply_markup=admin_profit_detail_kb("premium"))
        return

    if data.startswith("admin_set_profit_"):
        product = data[len("admin_set_profit_"):]
        fa_names = {"stars": "استارز", "ton": "تون", "gift": "گیفت استارز", "premium": "پرمیوم"}
        context.user_data["state"] = f"admin_awaiting_profit_{product}"
        await query.edit_message_text(
            admin_ask_profit_text(fa_names.get(product, product)),
            reply_markup=admin_profit_ask_kb(product),
        )
        return

    if data == "admin_reset_confirm":
        await query.edit_message_text(
            "⚠️ هشدار!\n\nاین عمل برگشت‌ناپذیر است.\n"
            "تمام کاربران، موجودی‌ها، سفارش‌ها و داده‌های ذخیره‌شده کاملاً پاک می‌شوند.\n\n"
            "آیا مطمئن هستید؟",
            reply_markup=admin_reset_confirm_kb(),
        )
        return

    if data == "admin_reset_yes":
        USERS.clear()
        USER_NAMES.clear()
        ORDERS.clear()
        TOPUP_REQUESTS.clear()
        PENDING_REFERRALS.clear()
        SUPPORT_TICKETS.clear()
        ADMIN_WAITING_REJECT.clear()
        ADMIN_WAITING_SUPPORT_REPLY.clear()
        
        try:
            if os.path.exists(DATA_FILE):
                os.remove(DATA_FILE)
        except Exception:
            logging.exception("خطا در پاک کردن فایل داده")
        save_data()
        await query.edit_message_text(
            "✅ تمام داده‌های ربات پاک شد.\n\nکاربران باید دوباره /start بزنند.",
            reply_markup=admin_back_kb(),
        )
        return

    if data == "admin_toggle_bot":
        BOT_ENABLED = not BOT_ENABLED
        save_data()
        await query.edit_message_text(admin_panel_text(), reply_markup=admin_panel_kb())
        return

    if data == "admin_toggle_referral_points":
        REFERRAL_POINTS_ENABLED = not REFERRAL_POINTS_ENABLED
        save_data()
        await query.edit_message_text(admin_panel_text(), reply_markup=admin_panel_kb())
        return

    
    if data == "admin_broadcast":
        context.user_data["state"] = "admin_awaiting_broadcast_text"
        await query.edit_message_text(ADMIN_BROADCAST_ASK_TEXT, reply_markup=admin_broadcast_ask_kb())
        return

    if data == "admin_broadcast_no":
        context.user_data["state"] = "admin_awaiting_broadcast_text"
        await query.edit_message_text(ADMIN_BROADCAST_ASK_TEXT, reply_markup=admin_broadcast_ask_kb())
        return

    if data == "admin_broadcast_yes":
        broadcast_text = context.user_data.get("broadcast_text", "")
        success, fail = 0, 0
        for uid in list(USERS.keys()):
            try:
                await context.bot.send_message(uid, broadcast_message_text(broadcast_text))
                success += 1
            except Exception:
                fail += 1
        context.user_data["broadcast_text"] = None
        await query.edit_message_text(
            broadcast_done_text(success, fail), reply_markup=admin_back_kb()
        )
        return

    
    if data == "admin_view_users":
        await query.edit_message_text(view_users_text(), reply_markup=admin_back_kb())
        return

    
    if data == "admin_price_menu":
        await query.edit_message_text(ADMIN_PRICE_MENU_TEXT, reply_markup=admin_price_menu_kb())
        return

    if data == "admin_price_menu_back":
        context.user_data["state"] = None
        await query.edit_message_text(ADMIN_PRICE_MENU_TEXT, reply_markup=admin_price_menu_kb())
        return

    if data == "admin_price_giftnft":
        await query.answer("این بخش بزودی فعال می‌شود ⏳", show_alert=True)
        return

    
    if data == "admin_price_stars":
        await query.edit_message_text(admin_stars_price_text(), reply_markup=admin_stars_price_kb())
        return

    if data == "admin_change_stars_price":
        context.user_data["state"] = "admin_awaiting_stars_price"
        await query.edit_message_text(
            admin_ask_stars_price_text(), reply_markup=admin_stars_price_ask_kb()
        )
        return

    if data == "admin_price_stars_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_stars_price_text(), reply_markup=admin_stars_price_kb())
        return

    
    if data == "admin_price_ton":
        await query.edit_message_text(admin_ton_price_text(), reply_markup=admin_ton_price_kb())
        return

    if data == "admin_change_ton_price":
        context.user_data["state"] = "admin_awaiting_ton_price"
        await query.edit_message_text(
            admin_ask_ton_price_text(), reply_markup=admin_ton_price_ask_kb()
        )
        return

    if data == "admin_price_ton_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_ton_price_text(), reply_markup=admin_ton_price_kb())
        return

    
    if data == "admin_price_premium":
        await query.edit_message_text(admin_premium_price_text(), reply_markup=admin_premium_price_kb())
        return

    if data == "admin_change_premium_price":
        await query.edit_message_text(
            ADMIN_PREMIUM_PLAN_SELECT_TEXT, reply_markup=admin_premium_plan_select_kb()
        )
        return

    if data == "admin_price_premium_back":
        context.user_data["state"] = None
        await query.edit_message_text(admin_premium_price_text(), reply_markup=admin_premium_price_kb())
        return

    if data in ("admin_premium_plan_3", "admin_premium_plan_6", "admin_premium_plan_12"):
        plan = data.split("_")[-1]
        context.user_data["state"] = f"admin_awaiting_premium_price_{plan}"
        await query.edit_message_text(
            admin_ask_premium_price_text(plan), reply_markup=admin_premium_price_ask_kb()
        )
        return

    if data == "admin_premium_plan_select_back":
        context.user_data["state"] = None
        await query.edit_message_text(
            ADMIN_PREMIUM_PLAN_SELECT_TEXT, reply_markup=admin_premium_plan_select_kb()
        )
        return

    
    if data == "admin_price_giftstars":
        await query.edit_message_text(ADMIN_GIFT_PRICE_LIST_TEXT, reply_markup=admin_gift_price_list_kb())
        return

    if data == "admin_gift_price_list_back":
        context.user_data["state"] = None
        await query.edit_message_text(ADMIN_GIFT_PRICE_LIST_TEXT, reply_markup=admin_gift_price_list_kb())
        return

    if data.startswith("admin_gift_price_detail_back_"):
        key = data[len("admin_gift_price_detail_back_"):]
        context.user_data["state"] = None
        await query.edit_message_text(
            admin_gift_price_detail_text(key), reply_markup=admin_gift_price_detail_kb(key)
        )
        return

    if data.startswith("admin_change_gift_price_"):
        key = data[len("admin_change_gift_price_"):]
        context.user_data["state"] = f"admin_awaiting_gift_price_{key}"
        await query.edit_message_text(
            admin_ask_gift_price_text(key), reply_markup=admin_gift_price_ask_kb(key)
        )
        return

    if data.startswith("admin_gift_price_"):
        key = data[len("admin_gift_price_"):]
        if key in GIFTS:
            await query.edit_message_text(
                admin_gift_price_detail_text(key), reply_markup=admin_gift_price_detail_kb(key)
            )
        return

async def reset_daily_limits_job(context: ContextTypes.DEFAULT_TYPE) -> None:
    today = today_str()
    for u in USERS.values():
        u["daily_topup_used"] = 0
        u["daily_topup_date"] = today
    logging.info("سقف مجاز شارژ روزانه همه کاربران ریست شد.")

async def update_prices_job(context: ContextTypes.DEFAULT_TYPE) -> None:
    global BASE_TON_PRICE
    if not HAS_HTTPX:
        return
    try:
        async with httpx.AsyncClient(timeout=8) as client:
            r = await client.get(TON_PRICE_API_URL)
            data = r.json()
            price_rls = float(data["stats"]["ton-rls"]["latest"])
            new_base = round(price_rls / 10)  
            if new_base > 0 and new_base != BASE_TON_PRICE:
                BASE_TON_PRICE = new_base
                recalc_prices()
                logging.info(f"قیمت تون بروز شد: {fmt(BASE_TON_PRICE)} تومان (پایه) ← {fmt(TON_PRICE)} تومان (فروش)")
    except Exception as e:
        logging.warning(f"خطا در دریافت قیمت تون از API: {e}")

async def autosave_job(context: ContextTypes.DEFAULT_TYPE) -> None:
    save_data()

def main() -> None:
    load_data()  

    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", start))
    app.add_handler(MessageHandler(filters.PHOTO, photo_handler))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, text_handler))
    app.add_handler(CallbackQueryHandler(callback_router))

    if app.job_queue:
        app.job_queue.run_daily(
            reset_daily_limits_job,
            time=dt_time(hour=0, minute=0, second=0, tzinfo=TEHRAN_TZ),
        )
        
        app.job_queue.run_repeating(autosave_job, interval=120, first=60)
        
        app.job_queue.run_repeating(update_prices_job, interval=10, first=5)
    else:
        logging.warning(
            "JobQueue در دسترس نیست؛ ریست خودکار سقف روزانه و ذخیره خودکار فعال نشد. "
            "برای فعال‌سازی نصب کن: pip install \"python-telegram-bot[job-queue]\" --upgrade"
        )

    print("ربات فروشگاه Abolfazl در حال اجراست... برای توقف Ctrl+C بزن.")
    print(f"داده‌ها در فایل ذخیره می‌شن: {DATA_FILE}")
    app.run_polling()
    save_data()  

if __name__ == "__main__":
    main()
