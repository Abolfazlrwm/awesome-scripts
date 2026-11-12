import os
import logging
import sqlite3
import asyncio
from datetime import datetime, time
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    Application, CommandHandler, CallbackQueryHandler,
    MessageHandler, filters, ContextTypes, ConversationHandler,
    ApplicationHandlerStop
)


TOKEN = os.environ.get("BOT_TOKEN", "")                # توکن ربات از @BotFather
ADMIN_ID = int(os.environ.get("ADMIN_ID", "0") or 0)   # آیدی عددی ادمین
GROUP_1 = os.environ.get("GROUP_1", "https://t.me/")   # لینک کانال/گروه ۱
GROUP_2 = os.environ.get("GROUP_2", "https://t.me/")   # لینک کانال/گروه ۲
GROUP_1_ID = int(os.environ.get("GROUP_1_ID", "0") or 0)   # آیدی عددی کانال یا گروه
CHANNEL_ID = int(os.environ.get("CHANNEL_ID", "0") or 0)
REQUIRED_CHANNELS = [c.strip() for c in os.environ.get("REQUIRED_CHANNELS", "").split(",") if c.strip()]


ALLIANCE_CREATE_COST = 20_000_000_000  
MAX_ALLIANCES = 2                      

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


(MAIN_MENU, SELECT_COUNTRY, SELECT_GROUP, COUNTRY_MENU,
 SHOP_MENU, SHOP_CATEGORY, SHOP_ITEM, SHOP_QUANTITY,
 COMPANY_MENU, TRADE_MENU, TRADE_SELECT_ITEM, TRADE_QUANTITY,
 TRADE_PRICE, TRADE_SELECT_COUNTRY, TRADE_CONFIRM,
 DECLARATION_TEXT, DECLARATION_CONFIRM,
 ATTACK_SELECT_TARGET, ATTACK_PERCENT, ATTACK_PERCENT_TEXT, ATTACK_CONFIRM) = range(21)


COUNTRIES = {
    "USA": {"name": "ایالات متحده آمریکا", "flag": "🇺🇸", "oil": False, "vip": True},
    "ISRAEL": {"name": "اسرائیل", "flag": "🇮🇱", "oil": False, "vip": False},
    "IRAN": {"name": "ایران", "flag": "🇮🇷", "oil": True, "vip": False},
    "RUSSIA": {"name": "روسیه", "flag": "🇷🇺", "oil": True, "vip": True},
    "IRAQ": {"name": "عراق", "flag": "🇮🇶", "oil": True, "vip": False},
    "SAUDI": {"name": "عربستان سعودی", "flag": "🇸🇦", "oil": True, "vip": False},
    "UAE": {"name": "امارات", "flag": "🇦🇪", "oil": True, "vip": False},
    "PAKISTAN": {"name": "پاکستان", "flag": "🇵🇰", "oil": False, "vip": False},
    "INDIA": {"name": "هند", "flag": "🇮🇳", "oil": False, "vip": False},
    "NORTH_KOREA": {"name": "کره شمالی", "flag": "🇰🇵", "oil": False, "vip": True},
    "SOUTH_KOREA": {"name": "کره جنوبی", "flag": "🇰🇷", "oil": False, "vip": False},
    "JAPAN": {"name": "ژاپن", "flag": "🇯🇵", "oil": False, "vip": False},
    "CHINA": {"name": "چین", "flag": "🇨🇳", "oil": False, "vip": True},
    "CANADA": {"name": "کانادا", "flag": "🇨🇦", "oil": False, "vip": False},
    "UK": {"name": "انگلیس", "flag": "🇬🇧", "oil": False, "vip": True},
    "FRANCE": {"name": "فرانسه", "flag": "🇫🇷", "oil": False, "vip": True},
    "VENEZUELA": {"name": "ونزوئلا", "flag": "🇻🇪", "oil": True, "vip": False},
    "ITALY": {"name": "ایتالیا", "flag": "🇮🇹", "oil": False, "vip": False},
    "GERMANY": {"name": "آلمان", "flag": "🇩🇪", "oil": False, "vip": True},
    "ARGENTINA": {"name": "آرژانتین", "flag": "🇦🇷", "oil": False, "vip": False},
    "TURKEY": {"name": "ترکیه", "flag": "🇹🇷", "oil": False, "vip": False},
    "SPAIN": {"name": "اسپانیا", "flag": "🇪🇸", "oil": False, "vip": False},
    "YEMEN": {"name": "یمن", "flag": "🇾🇪", "oil": True, "vip": False},
    "BRAZIL": {"name": "برزیل", "flag": "🇧🇷", "oil": True, "vip": False},
    "MEXICO": {"name": "مکزیک", "flag": "🇲🇽", "oil": True, "vip": False},
    "EGYPT": {"name": "مصر", "flag": "🇪🇬", "oil": False, "vip": False},
    "NIGERIA": {"name": "نیجریه", "flag": "🇳🇬", "oil": True, "vip": False},
    "SOUTH_AFRICA": {"name": "آفریقای جنوبی", "flag": "🇿🇦", "oil": False, "vip": False},
    "ETHIOPIA": {"name": "اتیوپی", "flag": "🇪🇹", "oil": False, "vip": False},
    "KENYA": {"name": "کنیا", "flag": "🇰🇪", "oil": False, "vip": False},
    "MOROCCO": {"name": "مراکش", "flag": "🇲🇦", "oil": False, "vip": False},
    "ALGERIA": {"name": "الجزایر", "flag": "🇩🇿", "oil": True, "vip": False},
    "LIBYA": {"name": "لیبی", "flag": "🇱🇾", "oil": True, "vip": False},
    "JORDAN": {"name": "اردن", "flag": "🇯🇴", "oil": False, "vip": False},
    "SYRIA": {"name": "سوریه", "flag": "🇸🇾", "oil": False, "vip": False},
    "LEBANON": {"name": "لبنان", "flag": "🇱🇧", "oil": False, "vip": False},
    "AFGHANISTAN": {"name": "افغانستان", "flag": "🇦🇫", "oil": False, "vip": False},
    "UKRAINE": {"name": "اوکراین", "flag": "🇺🇦", "oil": False, "vip": False},
    "POLAND": {"name": "لهستان", "flag": "🇵🇱", "oil": False, "vip": False},
    "NETHERLANDS": {"name": "هلند", "flag": "🇳🇱", "oil": False, "vip": False},
    "SWEDEN": {"name": "سوئد", "flag": "🇸🇪", "oil": False, "vip": False},
    "NORWAY": {"name": "نروژ", "flag": "🇳🇴", "oil": True, "vip": False},
    "DENMARK": {"name": "دانمارک", "flag": "🇩🇰", "oil": False, "vip": False},
    "FINLAND": {"name": "فنلاند", "flag": "🇫🇮", "oil": False, "vip": False},
    "SWITZERLAND": {"name": "سوئیس", "flag": "🇨🇭", "oil": False, "vip": False},
    "AUSTRIA": {"name": "اتریش", "flag": "🇦🇹", "oil": False, "vip": False},
    "PORTUGAL": {"name": "پرتغال", "flag": "🇵🇹", "oil": False, "vip": False},
    "GREECE": {"name": "یونان", "flag": "🇬🇷", "oil": False, "vip": False},
    "BELGIUM": {"name": "بلژیک", "flag": "🇧🇪", "oil": False, "vip": False},
    "CZECH": {"name": "چک", "flag": "🇨🇿", "oil": False, "vip": False},
    "HUNGARY": {"name": "مجارستان", "flag": "🇭🇺", "oil": False, "vip": False},
    "ROMANIA": {"name": "رومانی", "flag": "🇷🇴", "oil": False, "vip": False},
    "SERBIA": {"name": "صربستان", "flag": "🇷🇸", "oil": False, "vip": False},
    "KAZAKHSTAN": {"name": "قزاقستان", "flag": "🇰🇿", "oil": True, "vip": False},
    "UZBEKISTAN": {"name": "ازبکستان", "flag": "🇺🇿", "oil": False, "vip": False},
    "AZERBAIJAN": {"name": "آذربایجان", "flag": "🇦🇿", "oil": True, "vip": False},
    "GEORGIA": {"name": "گرجستان", "flag": "🇬🇪", "oil": False, "vip": False},
    "THAILAND": {"name": "تایلند", "flag": "🇹🇭", "oil": False, "vip": False},
    "VIETNAM": {"name": "ویتنام", "flag": "🇻🇳", "oil": False, "vip": False},
    "INDONESIA": {"name": "اندونزی", "flag": "🇮🇩", "oil": True, "vip": False},
    "MALAYSIA": {"name": "مالزی", "flag": "🇲🇾", "oil": True, "vip": False},
    "PHILIPPINES": {"name": "فیلیپین", "flag": "🇵🇭", "oil": False, "vip": False},
    "MYANMAR": {"name": "میانمار", "flag": "🇲🇲", "oil": False, "vip": False},
    "BANGLADESH": {"name": "بنگلادش", "flag": "🇧🇩", "oil": False, "vip": False},
    "SRI_LANKA": {"name": "سریلانکا", "flag": "🇱🇰", "oil": False, "vip": False},
    "NEPAL": {"name": "نپال", "flag": "🇳🇵", "oil": False, "vip": False},
    "MONGOLIA": {"name": "مغولستان", "flag": "🇲🇳", "oil": False, "vip": False},
    "TAIWAN": {"name": "تایوان", "flag": "🇹🇼", "oil": False, "vip": False},
    "SINGAPORE": {"name": "سنگاپور", "flag": "🇸🇬", "oil": False, "vip": False},
    "AUSTRALIA": {"name": "استرالیا", "flag": "🇦🇺", "oil": True, "vip": True},
    "NEW_ZEALAND": {"name": "نیوزیلند", "flag": "🇳🇿", "oil": False, "vip": False},
    "CUBA": {"name": "کوبا", "flag": "🇨🇺", "oil": False, "vip": False},
    "COLOMBIA": {"name": "کلمبیا", "flag": "🇨🇴", "oil": True, "vip": False},
    "PERU": {"name": "پرو", "flag": "🇵🇪", "oil": False, "vip": False},
    "CHILE": {"name": "شیلی", "flag": "🇨🇱", "oil": False, "vip": False},
    "ECUADOR": {"name": "اکوادور", "flag": "🇪🇨", "oil": True, "vip": False},
    "BOLIVIA": {"name": "بولیوی", "flag": "🇧🇴", "oil": False, "vip": False},
    "GHANA": {"name": "غنا", "flag": "🇬🇭", "oil": True, "vip": False},
    "ANGOLA": {"name": "آنگولا", "flag": "🇦🇴", "oil": True, "vip": False},
    "MOZAMBIQUE": {"name": "موزامبیک", "flag": "🇲🇿", "oil": False, "vip": False},
    "TANZANIA": {"name": "تانزانیا", "flag": "🇹🇿", "oil": False, "vip": False},
    "SUDAN": {"name": "سودان", "flag": "🇸🇩", "oil": True, "vip": False},
    "SOMALIA": {"name": "سومالی", "flag": "🇸🇴", "oil": False, "vip": False},
    "OMAN": {"name": "عمان", "flag": "🇴🇲", "oil": True, "vip": False},
    "KUWAIT": {"name": "کویت", "flag": "🇰🇼", "oil": True, "vip": False},
    "QATAR": {"name": "قطر", "flag": "🇶🇦", "oil": True, "vip": False},
    "BAHRAIN": {"name": "بحرین", "flag": "🇧🇭", "oil": True, "vip": False},
    "ARMENIA": {"name": "ارمنستان", "flag": "🇦🇲", "oil": False, "vip": False},
    "BELARUS": {"name": "بلاروس", "flag": "🇧🇾", "oil": False, "vip": False},
    "CROATIA": {"name": "کرواسی", "flag": "🇭🇷", "oil": False, "vip": False},
    "SLOVAKIA": {"name": "اسلواکی", "flag": "🇸🇰", "oil": False, "vip": False},
    "BULGARIA": {"name": "بلغارستان", "flag": "🇧🇬", "oil": False, "vip": False},
    "IRELAND": {"name": "ایرلند", "flag": "🇮🇪", "oil": False, "vip": False},
    "IRAQ2": {"name": "اقلیم کردستان", "flag": "🏴", "oil": True, "vip": False},
    "MYANMAR2": {"name": "تایلند شمالی", "flag": "🏴", "oil": False, "vip": False},
}

GROUPS = {
    "CENTCOM": {"name": "سنتکام", "flag": "🏴‍☠️"},
    "HAMAS": {"name": "حماس", "flag": "🏴‍☠️"},
    "DAESH": {"name": "داعش", "flag": "🏴‍☠️"},
    "AL_QAEDA": {"name": "القاعده", "flag": "🏴‍☠️"},
    "MOSSAD": {"name": "موساد", "flag": "🏴‍☠️"},
    "ANONYMOUS": {"name": "انانیموس", "flag": "🏴‍☠️"},
}


SHOP_ITEMS = {
    "ground": {
        "name": "نیروی زمینی 🔫",
        "items": {
            "commander": {"name": "فرمانده", "price": 40000},
            "soldier": {"name": "سرباز", "price": 20000},
            "police": {"name": "پلیس", "price": 20000},
            "border_guard": {"name": "مرزبان", "price": 30000},
            "bomb_defuser": {"name": "خنثی کننده بمب", "price": 50000},
            "bomber": {"name": "بمب گذار", "price": 40000},
            "special_forces": {"name": "یگان ویژه", "price": 30000},
            "mine_layer": {"name": "مین گذار", "price": 25000},
            "mine_defuser": {"name": "خنثی کننده مین", "price": 30000},
            "spy": {"name": "جاسوس", "price": 50000},
            "sniper": {"name": "تک تیرانداز", "price": 20000},
            "rpg": {"name": "ار پی جی زن", "price": 20000},
        }
    },
    "air": {
        "name": "نیروی هوایی ✈️",
        "items": {
            "f16": {"name": "F-16", "price": 1000000},
            "f18": {"name": "F-18", "price": 1200000},
            "f22": {"name": "F-22", "price": 1500000},
            "f35": {"name": "F-35", "price": 2000000, "vip": True},
            "b1": {"name": "B-1", "price": 1000000},
            "b2": {"name": "B-2", "price": 10000000, "vip": True},
            "b52": {"name": "B-52", "price": 15000000, "vip": True},
        }
    },
    "navy": {
        "name": "نیروی دریایی ⚓️",
        "items": {
            "oil_tanker": {"name": "نفت کش", "price": 1000000},
            "cargo_ship": {"name": "کشتی صادرات واردات", "price": 1000000},
            "aircraft_carrier": {"name": "ناو هواپیمابر", "price": 5000000},
            "warboat": {"name": "قایق جنگی", "price": 200000},
            "submarine": {"name": "زیر دریایی آیداهو", "price": 500000},
            "gerald_ford": {"name": "ناو جرالد فورد", "price": 10000000},
            "abraham_lincoln": {"name": "ناو ابراهام لینکن", "price": 40000000, "vip": True},
        }
    },
    "missile": {
        "name": "موشک 🚀",
        "items": {
            "precision": {"name": "موشک نقطه زن", "price": 200000},
            "cruise": {"name": "موشک کروز", "price": 300000},
            "khaibar": {"name": "موشک خیبرشکن", "price": 500000},
            "khorramshahr": {"name": "موشک خرمشهر ۴", "price": 800000, "vip": True},
            "df26": {"name": "موشک DF-26", "price": 1000000, "vip": True},
            "atom_bomb": {"name": "بمب اتم", "price": 60000000},
        }
    },
    "drone": {
        "name": "پهباد 🛬",
        "items": {
            "suicide_drone": {"name": "پهباد انتحاری", "price": 100000},
            "precision_drone": {"name": "پهباد نقطه زن", "price": 200000},
            "recon_drone": {"name": "پهباد شناسایی", "price": 300000},
        }
    },
    "helicopter": {
        "name": "بالگرد 🚁",
        "items": {
            "crocodile": {"name": "بالگرد تمساح", "price": 50000},
            "apache": {"name": "بالگرد آپاچی", "price": 100000},
            "cobra": {"name": "بالگرد کبری", "price": 150000},
            "bell12": {"name": "بالگرد بل ۱۲", "price": 200000},
        }
    },
    "defense": {
        "name": "پدافند 🛰",
        "items": {
            "patriot": {"name": "پدافند پاتریوت", "price": 100000},
            "phalanx": {"name": "پدافند فلانکس", "price": 100000},
            "thaad": {"name": "پدافند تاد", "price": 300000},
        }
    },
    "tank": {
        "name": "تانک 🚜",
        "items": {
            "zolfaghar": {"name": "تانک ذوالفقار", "price": 30000},
            "panther": {"name": "تانک پنتر", "price": 50000},
            "karrar": {"name": "تانک کرار", "price": 150000},
        }
    },
    "system": {
        "name": "سیستم 💻",
        "items": {
            "asset_hack": {"name": "هک دارایی", "price": 100000},
            "anti_asset_hack": {"name": "ضد هک دارایی", "price": 200000},
            "military_hack": {"name": "هک نظامی", "price": 400000},
            "anti_military_hack": {"name": "ضد هک نظامی", "price": 600000},
        }
    },
    "public": {
        "name": "مردمی 📈",
        "items": {
            "supermarket": {"name": "سوپر مارکت", "price": 30000},
            "school": {"name": "مدرسه", "price": 100000},
            "kindergarten": {"name": "مهد کودک", "price": 50000},
            "mall": {"name": "پاساژ", "price": 500000},
            "shelter": {"name": "پناهگاه", "price": 700000},
            "pool": {"name": "استخر", "price": 50000},
            "hotel": {"name": "هتل", "price": 200000},
            "metro": {"name": "مترو", "price": 5000000},
            "bus": {"name": "اتوبوس", "price": 20000},
            "airplane": {"name": "هواپیما", "price": 1000000},
            "amusement_park": {"name": "شهربازی", "price": 300000},
        }
    },
    "mine": {
        "name": "معدن 🚧",
        "items": {
            "diamond_mine": {"name": "معدن الماس", "price": 30000000, "daily_income": 20000000},
            "gold_mine": {"name": "معدن طلا", "price": 20000000, "daily_income": 7000000},
            "silver_mine": {"name": "معدن نقره", "price": 10000000, "daily_income": 5000000},
        }
    },
}


MILITARY_BUNDLE_CATEGORIES = ["ground", "air", "navy", "missile", "drone", "helicopter", "defense", "tank"]
MILITARY_BUNDLE_QTY_PER_ITEM = 100
MILITARY_BUNDLE_ORIGINAL_PRICE = 2_000_000_000_000 

MILITARY_BUNDLE_FIXED_PRICE = 1_000_000_000_000     


def compute_military_bundle():

    items = {}
    for cat_key in MILITARY_BUNDLE_CATEGORIES:
        cat = SHOP_ITEMS.get(cat_key, {})
        for item_key, item in cat.get("items", {}).items():
            items[item_key] = {"name": item["name"], "price": item["price"], "qty": MILITARY_BUNDLE_QTY_PER_ITEM}

    return items, MILITARY_BUNDLE_ORIGINAL_PRICE, MILITARY_BUNDLE_FIXED_PRICE


POWER_WEIGHTS = {
    "commander": 8, "soldier": 2, "police": 1, "border_guard": 2,
    "bomb_defuser": 1, "bomber": 4, "special_forces": 6, "mine_layer": 2,
    "mine_defuser": 1, "spy": 1, "sniper": 5, "rpg": 4,
    "f16": 50, "f18": 55, "f22": 70, "f35": 90, "b1": 60, "b2": 150, "b52": 130,
    "oil_tanker": 0, "cargo_ship": 0, "aircraft_carrier": 200, "warboat": 15,
    "submarine": 40, "gerald_ford": 400, "abraham_lincoln": 600,
    "precision": 30, "cruise": 45, "khaibar": 70, "khorramshahr": 100,
    "df26": 130,
    "suicide_drone": 10, "precision_drone": 15, "recon_drone": 3,
    "crocodile": 10, "apache": 20, "cobra": 15, "bell12": 8,
    "patriot": 40, "phalanx": 35, "thaad": 60,
    "zolfaghar": 12, "panther": 15, "karrar": 25,
}


DEFENSE_ONLY_UNITS = {"patriot", "phalanx", "thaad"}

ATTACK_COOLDOWN_SECONDS =200        
NEWBIE_PROTECTION_SECONDS = 990       
ATTACK_LOSS_RATIO = 0.2            
ATTACK_BUDGET_TRANSFER_RATIO = 0.15 
ATTACK_CONQUER_THRESHOLD = 70         
ATTACK_ANNIHILATE_THRESHOLD = 85     

NUKE_FULL_DESTROY_COUNT = 10    
NUKE_HALF_DAMAGE_COUNT = 5      
NUKE_HALF_DAMAGE_RATIO = 0.5    


MAX_WARNINGS = 5

WARNING_PENALTIES = {
    1: 0.05,  
    2: 0.10,  
    3: 0.20,  
    4: 0.35,  
    5: 1.00,   
}


def compute_power_rankings():

    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players WHERE country IS NOT NULL")
    players = [dict(r) for r in c.fetchall()]
    conn.close()

    scored = []
    for p in players:
        info = get_country_info(p.get("country"))
        if not info:
            continue
        score = calc_attack_power(p, 100) + calc_defense_power(p)
        scored.append({"country": p["country"], "info": info, "score": score})

    if not scored:
        return []

    max_score = max(s["score"] for s in scored) or 1
    for s in scored:
        s["percent"] = (s["score"] / max_score) * 100 if max_score > 0 else 0

    scored.sort(key=lambda s: s["score"], reverse=True)
    return scored


def give_warning(country_code):

    player = get_player_by_country(country_code)
    if not player:
        return None

    current_warnings = player.get("warnings", 0) or 0
    new_warnings = min(MAX_WARNINGS, current_warnings + 1)
    penalty_ratio = WARNING_PENALTIES.get(new_warnings, 0)

    if new_warnings >= MAX_WARNINGS:
        delete_player(player["user_id"])
        return {
            "deleted": True,
            "warnings": new_warnings,
            "user_id": player["user_id"],
            "penalty_ratio": penalty_ratio,
            "penalty_amount": player.get("budget", 0) or 0,
            "new_budget": 0,
        }

    current_budget = player.get("budget", 0) or 0
    penalty_amount = int(current_budget * penalty_ratio)
    new_budget = max(0, current_budget - penalty_amount)
    update_player(player["user_id"], {"warnings": new_warnings, "budget": new_budget})

    return {
        "deleted": False,
        "warnings": new_warnings,
        "user_id": player["user_id"],
        "penalty_ratio": penalty_ratio,
        "penalty_amount": penalty_amount,
        "new_budget": new_budget,
    }


def clear_warning(country_code):

    player = get_player_by_country(country_code)
    if not player:
        return None

    current_warnings = player.get("warnings", 0) or 0
    new_warnings = max(0, current_warnings - 1)
    update_player(player["user_id"], {"warnings": new_warnings})

    return {"warnings": new_warnings, "user_id": player["user_id"]}


async def admin_delete_country_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):

    query = update.callback_query
    if query.from_user.id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return
    await query.answer()

    code = query.data.replace("adm_delconfirm|", "")
    player = get_player_by_country(code)
    info = get_country_info(code)
    if not player or not info:
        await query.edit_message_text("⚠️ این کشور بازیکن فعالی نداره.", parse_mode="Markdown")
        return

    delete_player(player["user_id"])

    await query.edit_message_text(
        f"☠️ *کشور {info['flag']} {info['name']} کاملاً حذف شد*\n"
        f"این کشور الان آزاده و کس دیگه‌ای می‌تونه انتخابش کنه.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
        parse_mode="Markdown"
    )

    try:
        await context.bot.send_message(
            player["user_id"],
            f"☠️ *کشورت توسط سازمان جهانی کاملاً حذف شد!*\n"
            f"می‌تونی دوباره یه کشور جدید انتخاب کنی."
        )
    except Exception as e:
        logger.error(f"Notify country delete error: {e}")

    try:
        await context.bot.send_message(
            GROUP_1_ID,
            f"☠️ *کشور {info['flag']} {info['name']} توسط سازمان جهانی حذف شد!*\nاین کشور الان آزاده.",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Group delete announce error: {e}")


async def publish_power_rankings(context: ContextTypes.DEFAULT_TYPE):

    rankings = compute_power_rankings()

    if not rankings:
        try:
            await context.bot.send_message(CHANNEL_ID, "🏆 هنوز هیچ کشوری برای رتبه‌بندی ابرقدرت‌ها وجود نداره.")
        except Exception as e:
            logger.error(f"Power ranking empty announce error: {e}")
        return

    medal = {0: "🥇", 1: "🥈", 2: "🥉"}
    lines = [
        "🏆 *رتبه‌بندی ابرقدرت‌های جهان*",
        "━━━━━━━━━━━━━━━━━━━━",
        "📊 بر اساس درصد قدرت نظامی نسبت به قوی‌ترین کشور دنیا:",
        "",
    ]
    for i, s in enumerate(rankings):
        rank_icon = medal.get(i, f"{i+1}.")
        info = s["info"]
        lines.append(f"{rank_icon} {info['flag']} *{info['name']}* — `{round(s['percent'])}٪`")

    lines.append("")
    lines.append("━━━━━━━━━━━━━━━━━━━━")

    text = "\n".join(lines)
    
    chunks = [text[i:i + 3800] for i in range(0, len(text), 3800)] or [text]
    for chunk in chunks:
        try:
            await context.bot.send_message(CHANNEL_ID, chunk, parse_mode="Markdown")
        except Exception as e:
            logger.error(f"Power ranking announce error: {e}")


def calc_attack_power(p, percent=100):

    factor = max(0, min(100, percent)) / 100
    total = 0
    for key, weight in POWER_WEIGHTS.items():
        if key in DEFENSE_ONLY_UNITS:
            continue
        total += (p.get(key, 0) or 0) * weight * factor
    return total


def calc_max_attack_power(p):

    return calc_attack_power(p, 100)


def get_protection_remaining(defender):

    created_at = defender.get("country_created_at") if defender else None
    if not created_at:
        return None
    try:
        elapsed = (datetime.now() - datetime.fromisoformat(created_at)).total_seconds()
    except ValueError:
        return None
    protection_seconds = get_newbie_protection_seconds()
    if elapsed < protection_seconds:
        return int(protection_seconds - elapsed)
    return None


def calc_defense_power(p):
    total = calc_attack_power(p)
    for key in DEFENSE_ONLY_UNITS:
        total += (p.get(key, 0) or 0) * POWER_WEIGHTS.get(key, 0)
    return total


def resolve_attack(attacker_country, defender_country, percent=100):

    percent = max(1, min(100, percent))
    attacker = get_player_by_country(attacker_country)
    defender = get_player_by_country(defender_country)
    if not attacker or not defender:
        return {"error": "not_found"}

    if get_protection_remaining(defender) is not None:
        return {"error": "protected"}

    atk_power = calc_attack_power(attacker, percent)
    def_power = calc_defense_power(defender)

    if atk_power <= 0:
        return {"error": "no_equipment"}

    raw_percent = (atk_power / (atk_power + def_power)) * 100
    damage_percent = max(5, min(95, raw_percent))

    annihilated = damage_percent >= ATTACK_ANNIHILATE_THRESHOLD
    conquered = damage_percent >= ATTACK_CONQUER_THRESHOLD
    winner = "attack" if conquered else "defense"


    attacker_updates = {}
    consumed_items = {}
    for key in POWER_WEIGHTS:
        if key in DEFENSE_ONLY_UNITS:
            continue  
        current = attacker.get(key, 0) or 0
        if current > 0:
            used = int(current * percent / 100)
            if used > 0:
                consumed_items[key] = used
            attacker_updates[key] = max(0, current - used)

    if annihilated:
        
        transferred = defender.get("budget", 0) or 0
        attacker_updates["budget"] = (attacker.get("budget", 0) or 0) + transferred
        update_player(attacker["user_id"], attacker_updates)
        delete_player(defender["user_id"])
        return {
            "attacker_country": attacker_country,
            "defender_country": defender_country,
            "atk_power": atk_power,
            "def_power": def_power,
            "percent_used": percent,
            "damage_percent": 100.0,
            "conquered": True,
            "annihilated": True,
            "winner": "attack",
            "consumed_items": consumed_items,
            "transferred": transferred,
        }

    if conquered:
        
        defender_updates = {key: 0 for key in POWER_WEIGHTS if (defender.get(key, 0) or 0) > 0}
        transferred = defender.get("budget", 0) or 0
        defender_updates["budget"] = 0
        attacker_updates["budget"] = (attacker.get("budget", 0) or 0) + transferred
    else:
        defender_updates = {}
        for key in POWER_WEIGHTS:
            current = defender.get(key, 0) or 0
            if current > 0:
                defender_updates[key] = max(0, current - int(current * damage_percent / 100))
        transferred = int((defender.get("budget", 0) or 0) * damage_percent / 100)
        defender_updates["budget"] = max(0, (defender.get("budget", 0) or 0) - transferred)
        attacker_updates["budget"] = (attacker.get("budget", 0) or 0) + transferred

    update_player(attacker["user_id"], attacker_updates)
    update_player(defender["user_id"], defender_updates)


    
    return {
        "attacker_country": attacker_country,
        "defender_country": defender_country,
        "atk_power": atk_power,
        "def_power": def_power,
        "percent_used": percent,
        "damage_percent": round(damage_percent, 1),
        "conquered": conquered,
        "annihilated": False,
        "winner": winner,
        "consumed_items": consumed_items,
        "transferred": transferred,
    }



def resolve_nuke_attack(attacker_country, defender_country, nuke_count):

   
    attacker = get_player_by_country(attacker_country)
    defender = get_player_by_country(defender_country)
    if not attacker or not defender:
        return {"error": "not_found"}

    available = attacker.get("atom_bomb", 0) or 0
    if available < nuke_count:
        return {"error": "not_enough_nukes"}

   
    update_player(attacker["user_id"], {"atom_bomb": available - nuke_count})

    if nuke_count >= NUKE_FULL_DESTROY_COUNT:
        transferred = defender.get("budget", 0) or 0
        update_player(attacker["user_id"], {"budget": (attacker.get("budget", 0) or 0) + transferred})
        delete_player(defender["user_id"])
        return {
            "attacker_country": attacker_country,
            "defender_country": defender_country,
            "nuke_count": nuke_count,
            "annihilated": True,
            "damage_percent": 100.0,
            "transferred": transferred,
        }

    if nuke_count >= NUKE_HALF_DAMAGE_COUNT:
        defender_updates = {}
        for key in POWER_WEIGHTS:
            current = defender.get(key, 0) or 0
            if current > 0:
                defender_updates[key] = max(0, current - int(current * NUKE_HALF_DAMAGE_RATIO))
        transferred = int((defender.get("budget", 0) or 0) * NUKE_HALF_DAMAGE_RATIO)
        defender_updates["budget"] = max(0, (defender.get("budget", 0) or 0) - transferred)
        update_player(defender["user_id"], defender_updates)
        update_player(attacker["user_id"], {"budget": (attacker.get("budget", 0) or 0) + transferred})
        return {
            "attacker_country": attacker_country,
            "defender_country": defender_country,
            "nuke_count": nuke_count,
            "annihilated": False,
            "damage_percent": NUKE_HALF_DAMAGE_RATIO * 100,
            "transferred": transferred,
        }

    return {
        "attacker_country": attacker_country,
        "defender_country": defender_country,
        "nuke_count": nuke_count,
        "annihilated": False,
        "damage_percent": 0,
        "transferred": 0
    }


def resolve_group_attack(alliance_id, leader_country, defender_country, percent):

    
    percent = max(1, min(100, percent))
    defender = get_player_by_country(defender_country)
    if not defender:
        return {"error": "not_found"}

    if get_protection_remaining(defender) is not None:
        return {"error": "protected"}

    members = get_alliance_members_players(alliance_id)
    if not members:
        return {"error": "no_members"}

    total_atk = 0
    participant_updates = []
    participant_countries = []
    for m in members:
        atk = calc_attack_power(m, percent)
        if atk <= 0:
            continue
        total_atk += atk
        participant_countries.append(m.get("country"))

        updates = {}
        for key in POWER_WEIGHTS:
            if key in DEFENSE_ONLY_UNITS:
                continue
            current = m.get(key, 0) or 0
            if current > 0:
                used = int(current * percent / 100)
                if used > 0:
                    updates[key] = max(0, current - used)
        participant_updates.append((m["user_id"], updates))

    if total_atk <= 0:
        return {"error": "no_equipment"}

    def_power = calc_defense_power(defender)
    raw_percent = (total_atk / (total_atk + def_power)) * 100
    damage_percent = max(5, min(95, raw_percent))
    annihilated = damage_percent >= ATTACK_ANNIHILATE_THRESHOLD
    conquered = damage_percent >= ATTACK_CONQUER_THRESHOLD

    
    for uid, updates in participant_updates:
        if updates:
            update_player(uid, updates)

    leader_player = get_player_by_country(leader_country)

    if annihilated:
        transferred = defender.get("budget", 0) or 0
        update_player(leader_player["user_id"], {"budget": (leader_player.get("budget", 0) or 0) + transferred})
        delete_player(defender["user_id"])
        return {
            "attacker_country": leader_country,
            "defender_country": defender_country,
            "atk_power": total_atk,
            "def_power": def_power,
            "percent_used": percent,
            "damage_percent": 100.0,
            "conquered": True,
            "annihilated": True,
            "transferred": transferred,
            "participants": participant_countries,
        }

    if conquered:
        defender_updates = {key: 0 for key in POWER_WEIGHTS if (defender.get(key, 0) or 0) > 0}
        transferred = defender.get("budget", 0) or 0
        defender_updates["budget"] = 0
    else:
        defender_updates = {}
        for key in POWER_WEIGHTS:
            current = defender.get(key, 0) or 0
            if current > 0:
                defender_updates[key] = max(0, current - int(current * damage_percent / 100))
        transferred = int((defender.get("budget", 0) or 0) * damage_percent / 100)
        defender_updates["budget"] = max(0, (defender.get("budget", 0) or 0) - transferred)

    update_player(defender["user_id"], defender_updates)
    update_player(leader_player["user_id"], {"budget": (leader_player.get("budget", 0) or 0) + transferred})

    return {
        "attacker_country": leader_country,
        "defender_country": defender_country,
        "atk_power": total_atk,
        "def_power": def_power,
        "percent_used": percent,
        "damage_percent": round(damage_percent, 1),
        "conquered": conquered,
        "annihilated": False,
        "transferred": transferred,
        "participants": participant_countries,
    }



COMPANIES = {
    "airplane_co": {
        "name": "شرکت هواپیماسازی ✈️",
        "price": 700000000,
        "income": 200000000,
        "oil_needed": 85000000,
        "daily_produce": {"airplane": 500},
        "description": "تولید روزانه ۵۰۰ عدد از هر نوع هواپیما"
    },
    "tank_co": {
        "name": "شرکت تانک‌سازی 🚜",
        "price": 500000000,
        "income": 120000000,
        "oil_needed": 30000000,
        "daily_produce": {"zolfaghar": 500, "panther": 500, "karrar": 500},
        "description": "تولید روزانه ۵۰۰ عدد تانک"
    },
    "public_co": {
        "name": "شرکت ساخت وسایل مردمی 🏙️",
        "price": 300000000,
        "income": 90000000,
        "oil_needed": 10000000,
        "daily_produce": {"supermarket": 100},
        "satisfaction_bonus": 20,
        "description": "تولید روزانه ۱۰۰ عدد + ۲۰٪ رضایت مردم"
    },
    "drone_co": {
        "name": "شرکت ساخت پهباد 🛬",
        "price": 500000000,
        "income": 100000000,
        "oil_needed": 35000000,
        "daily_produce": {"suicide_drone": 500, "precision_drone": 500, "recon_drone": 500},
        "description": "تولید روزانه ۵۰۰ عدد از هر نوع پهباد"
    },
    "missile_co": {
        "name": "شرکت ساخت موشک 🚀",
        "price": 600000000,
        "income": 140000000,
        "oil_needed": 40000000,
        "daily_produce": {"precision": 500, "cruise": 500, "khaibar": 500},
        "description": "تولید روزانه ۵۰۰ عدد از هر نوع موشک"
    },
    "hack_co": {
        "name": "شرکت هکری 💻",
        "price": 500000000,
        "income": 100000000,
        "oil_needed": 25000000,
        "daily_produce": {"asset_hack": 500, "anti_asset_hack": 500, "military_hack": 500, "anti_military_hack": 500},
        "description": "تولید روزانه ۵۰۰ عدد از هر سیستم"
    },
    "navy_co": {
        "name": "شرکت نیروی دریایی ⚓️",
        "price": 500000000,
        "income": 130000000,
        "oil_needed": 45000000,
        "daily_produce": {"warboat": 200, "submarine": 200, "oil_tanker": 200},
        "description": "تولید روزانه ۲۰۰ عدد از هر کدام"
    },
    "apple_co": {
        "name": "شرکت ساخت آیفون 📱",
        "price": 400000000,
        "income": 150000000,
        "oil_needed": 30000000,
        "daily_produce": {},
        "description": "بدون تولید محصول، فقط درآمد"
    },
    "helicopter_co": {
        "name": "شرکت ساخت بالگرد 🚁",
        "price": 450000000,
        "income": 110000000,
        "oil_needed": 28000000,
        "daily_produce": {"apache": 200, "cobra": 200, "crocodile": 200},
        "description": "تولید روزانه ۲۰۰ عدد از هر نوع بالگرد"
    },
    "defense_co": {
        "name": "شرکت پدافند 🛰️",
        "price": 550000000,
        "income": 125000000,
        "oil_needed": 32000000,
        "daily_produce": {"patriot": 300, "phalanx": 300, "thaad": 300},
        "description": "تولید روزانه ۳۰۰ عدد از هر نوع پدافند"
    },
    "mine_co": {
        "name": "شرکت معدن‌کاری ⛏️",
        "price": 800000000,
        "income": 180000000,
        "oil_needed": 50000000,
        "daily_produce": {},
        "description": "افزایش ۳۰٪ به درآمد معادن"
    },
    "ship_co": {
        "name": "شرکت کشتی‌سازی 🚢",
        "price": 600000000,
        "income": 140000000,
        "oil_needed": 42000000,
        "daily_produce": {"cargo_ship": 100, "aircraft_carrier": 50},
        "description": "تولید روزانه ۱۰۰ کشتی و ۵۰ ناو"
    },
    "ground_co": {
        "name": "شرکت تجهیزات زمینی 🔫",
        "price": 350000000,
        "income": 95000000,
        "oil_needed": 20000000,
        "daily_produce": {"soldier": 1000, "special_forces": 300, "sniper": 200},
        "description": "تولید روزانه ۱۰۰۰ سرباز + ۳۰۰ یگان ویژه + ۲۰۰ تک‌تیرانداز"
    },
    "energy_co": {
        "name": "شرکت انرژی ⚡",
        "price": 700000000,
        "income": 200000000,
        "oil_needed": 60000000,
        "daily_produce": {},
        "description": "افزایش ۱۵٪ درآمد روزانه کشور"
    },
    "intel_co": {
        "name": "شرکت اطلاعاتی 🕵️",
        "price": 480000000,
        "income": 115000000,
        "oil_needed": 22000000,
        "daily_produce": {"spy": 200, "recon_drone": 100},
        "description": "تولید روزانه ۲۰۰ جاسوس + ۱۰۰ پهباد شناسایی"
    },
}


def init_db():
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    
    c.execute("""CREATE TABLE IF NOT EXISTS players (
        user_id INTEGER PRIMARY KEY,
        username TEXT,
        country TEXT,
        is_group INTEGER DEFAULT 0,
        budget INTEGER DEFAULT 150000000,
        daily_income INTEGER DEFAULT 70000000,
        oil_income INTEGER DEFAULT 0,
        oil_reserves INTEGER DEFAULT 0,
        satisfaction INTEGER DEFAULT 100,
        commander INTEGER DEFAULT 0,
        soldier INTEGER DEFAULT 0,
        police INTEGER DEFAULT 0,
        border_guard INTEGER DEFAULT 0,
        bomb_defuser INTEGER DEFAULT 0,
        bomber INTEGER DEFAULT 0,
        special_forces INTEGER DEFAULT 0,
        mine_layer INTEGER DEFAULT 0,
        mine_defuser INTEGER DEFAULT 0,
        spy INTEGER DEFAULT 0,
        sniper INTEGER DEFAULT 0,
        rpg INTEGER DEFAULT 0,
        f16 INTEGER DEFAULT 0,
        f18 INTEGER DEFAULT 0,
        f22 INTEGER DEFAULT 0,
        f35 INTEGER DEFAULT 0,
        b1 INTEGER DEFAULT 0,
        b2 INTEGER DEFAULT 0,
        b52 INTEGER DEFAULT 0,
        oil_tanker INTEGER DEFAULT 0,
        cargo_ship INTEGER DEFAULT 0,
        aircraft_carrier INTEGER DEFAULT 0,
        warboat INTEGER DEFAULT 0,
        submarine INTEGER DEFAULT 0,
        gerald_ford INTEGER DEFAULT 0,
        abraham_lincoln INTEGER DEFAULT 0,
        precision INTEGER DEFAULT 0,
        cruise INTEGER DEFAULT 0,
        khaibar INTEGER DEFAULT 0,
        khorramshahr INTEGER DEFAULT 0,
        df26 INTEGER DEFAULT 0,
        atom_bomb INTEGER DEFAULT 0,
        suicide_drone INTEGER DEFAULT 0,
        precision_drone INTEGER DEFAULT 0,
        recon_drone INTEGER DEFAULT 0,
        crocodile INTEGER DEFAULT 0,
        apache INTEGER DEFAULT 0,
        cobra INTEGER DEFAULT 0,
        bell12 INTEGER DEFAULT 0,
        patriot INTEGER DEFAULT 0,
        phalanx INTEGER DEFAULT 0,
        thaad INTEGER DEFAULT 0,
        zolfaghar INTEGER DEFAULT 0,
        panther INTEGER DEFAULT 0,
        karrar INTEGER DEFAULT 0,
        asset_hack INTEGER DEFAULT 0,
        anti_asset_hack INTEGER DEFAULT 0,
        military_hack INTEGER DEFAULT 0,
        anti_military_hack INTEGER DEFAULT 0,
        supermarket INTEGER DEFAULT 0,
        school INTEGER DEFAULT 0,
        kindergarten INTEGER DEFAULT 0,
        mall INTEGER DEFAULT 0,
        shelter INTEGER DEFAULT 0,
        pool INTEGER DEFAULT 0,
        hotel INTEGER DEFAULT 0,
        metro INTEGER DEFAULT 0,
        bus INTEGER DEFAULT 0,
        airplane INTEGER DEFAULT 0,
        amusement_park INTEGER DEFAULT 0,
        diamond_mine INTEGER DEFAULT 0,
        gold_mine INTEGER DEFAULT 0,
        silver_mine INTEGER DEFAULT 0
    )""")

    c.execute("""CREATE TABLE IF NOT EXISTS companies (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        company_key TEXT,
        owner_country TEXT,
        owner_user_id INTEGER
    )""")

    c.execute("""CREATE TABLE IF NOT EXISTS trades (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_country TEXT,
        receiver_country TEXT,
        item TEXT,
        quantity INTEGER,
        price INTEGER,
        status TEXT DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )""")

    c.execute("""CREATE TABLE IF NOT EXISTS declarations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        country TEXT,
        text TEXT,
        status TEXT DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )""")
    
    c.execute("""CREATE TABLE IF NOT EXISTS nuke_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    attacker_country TEXT,
    defender_country TEXT,
    attacker_user_id INTEGER,
    nuke_count INTEGER,
    reason TEXT,
    status TEXT DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)""")

    c.execute("""CREATE TABLE IF NOT EXISTS alliances (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        leader_user_id INTEGER,
        leader_country TEXT,
        created_at TEXT
    )""")


    c.execute("""CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )""")
    default_settings = {
        "war_enabled": "1",
        "group_war_enabled": "1",
        "shop_enabled": "1",
        "trade_enabled": "1",
        "war_cooldown_min": str(ATTACK_COOLDOWN_SECONDS // 60),
        "group_war_cooldown_min": str(ATTACK_COOLDOWN_SECONDS // 60),
        "newbie_protection_min": str(NEWBIE_PROTECTION_SECONDS // 60),
    }
    for k, v in default_settings.items():
        c.execute("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)", (k, v))

    
    new_columns = [
        ("oil_reserves", "INTEGER DEFAULT 0"),
        ("last_attack", "TEXT"),
        ("country_created_at", "TEXT"),
        ("warnings", "INTEGER DEFAULT 0"),
        ("alliance_id", "INTEGER"),
    ]
    c.execute("PRAGMA table_info(players)")
    existing = {row[1] for row in c.fetchall()}
    for col_name, col_def in new_columns:
        if col_name not in existing:
            c.execute(f"ALTER TABLE players ADD COLUMN {col_name} {col_def}")
            logger.info(f"✅ ستون {col_name} به دیتابیس اضافه شد")

    conn.commit()
    conn.close()

def get_player(user_id):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT * FROM players WHERE user_id=?", (user_id,))
    row = c.fetchone()
    conn.close()
    if row:
        cols = [d[0] for d in c.description] if c.description else []
        return dict(zip(cols, row)) if cols else None
    return None

def get_player_by_country(country):
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players WHERE country=?", (country,))
    row = c.fetchone()
    conn.close()
    return dict(row) if row else None

def save_player(user_id, data: dict):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    cols = ", ".join(data.keys())
    placeholders = ", ".join(["?" for _ in data])
    updates = ", ".join([f"{k}=?" for k in data])
    vals = list(data.values())
    c.execute(f"INSERT OR REPLACE INTO players (user_id, {cols}) VALUES (?, {placeholders})",
              [user_id] + vals)
    conn.commit()
    conn.close()

def update_player(user_id, updates: dict):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    set_clause = ", ".join([f"{k}=?" for k in updates])
    vals = list(updates.values()) + [user_id]
    c.execute(f"UPDATE players SET {set_clause} WHERE user_id=?", vals)
    conn.commit()
    conn.close()

def delete_player(user_id):
   
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("DELETE FROM players WHERE user_id=?", (user_id,))
    conn.commit()
    conn.close()

def is_country_taken(country):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT user_id FROM players WHERE country=?", (country,))
    row = c.fetchone()
    conn.close()
    return row is not None

def get_all_active_countries():
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT country FROM players WHERE country IS NOT NULL")
    rows = c.fetchall()
    conn.close()
    return [r[0] for r in rows]

def get_player_by_id_full(user_id):
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players WHERE user_id=?", (user_id,))
    row = c.fetchone()
    conn.close()
    return dict(row) if row else None


def get_alliance(alliance_id):
    if not alliance_id:
        return None
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM alliances WHERE id=?", (alliance_id,))
    row = c.fetchone()
    conn.close()
    return dict(row) if row else None

def get_all_alliances():
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM alliances ORDER BY id")
    rows = [dict(r) for r in c.fetchall()]
    conn.close()
    return rows

def count_alliances():
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT COUNT(*) FROM alliances")
    n = c.fetchone()[0]
    conn.close()
    return n

def create_alliance_db(name, leader_user_id, leader_country):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute(
        "INSERT INTO alliances (name, leader_user_id, leader_country, created_at) VALUES (?, ?, ?, ?)",
        (name, leader_user_id, leader_country, datetime.now().isoformat())
    )
    alliance_id = c.lastrowid
    conn.commit()
    conn.close()
    update_player(leader_user_id, {"alliance_id": alliance_id})
    return alliance_id

def delete_alliance_db(alliance_id):
    
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("UPDATE players SET alliance_id=NULL WHERE alliance_id=?", (alliance_id,))
    c.execute("DELETE FROM alliances WHERE id=?", (alliance_id,))
    conn.commit()
    conn.close()

def get_alliance_members_players(alliance_id):
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players WHERE alliance_id=?", (alliance_id,))
    rows = [dict(r) for r in c.fetchall()]
    conn.close()
    return rows

def set_player_alliance(user_id, alliance_id):
    update_player(user_id, {"alliance_id": alliance_id})


def get_setting(key, default=None):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT value FROM settings WHERE key=?", (key,))
    row = c.fetchone()
    conn.close()
    return row[0] if row else default

def set_setting(key, value):
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)", (key, str(value)))
    conn.commit()
    conn.close()

def get_bool_setting(key, default=True):
    val = get_setting(key, "1" if default else "0")
    return val == "1"

def set_bool_setting(key, value: bool):
    set_setting(key, "1" if value else "0")

def get_int_setting(key, default):
    val = get_setting(key, str(default))
    try:
        return int(val)
    except (TypeError, ValueError):
        return default

def is_war_enabled():
    return get_bool_setting("war_enabled", True)

def is_group_war_enabled():
    return get_bool_setting("group_war_enabled", True)

def is_shop_enabled():
    return get_bool_setting("shop_enabled", True)

def is_trade_enabled():
    return get_bool_setting("trade_enabled", True)

def get_war_cooldown_seconds():
    return get_int_setting("war_cooldown_min", ATTACK_COOLDOWN_SECONDS // 60) * 60

def get_group_war_cooldown_seconds():
    return get_int_setting("group_war_cooldown_min", ATTACK_COOLDOWN_SECONDS // 60) * 60

def get_newbie_protection_seconds():
    return get_int_setting("newbie_protection_min", NEWBIE_PROTECTION_SECONDS // 60) * 60


def trade_field(item_key):
   
    if item_key == "oil":
        return "oil_reserves"
    return item_key


def fmt(n):
    return f"{n:,}"

def get_country_info(code):
    if code in COUNTRIES:
        return COUNTRIES[code]
    if code in GROUPS:
        return GROUPS[code]
    return None

def get_item_name(item_key):
   
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            return cat["items"][item_key]["name"]
    return item_key

async def check_membership(user_id, bot):
    for ch in REQUIRED_CHANNELS:
        try:
            member = await bot.get_chat_member(ch, user_id)
            if member.status in ["left", "kicked", "banned"]:
                return False
        except Exception:
            return False
    return True

def country_status_text(p):
    code = p.get("country", "")
    info = get_country_info(code)
    name = info["name"] if info else code
    flag = info.get("flag", "") if info else ""
    oil_line = ""
    if p.get('oil_income', 0) > 0:
        oil_line = f"\n🛢️ درآمد نفتی روزانه: `{fmt(p.get('oil_income',0))}`\n🛢️ ذخایر نفت: `{fmt(p.get('oil_reserves',0))}`"

    sat = p.get('satisfaction', 100)
    if sat >= 80:
        sat_emoji = "😍"
    elif sat >= 50:
        sat_emoji = "😐"
    else:
        sat_emoji = "😡"

    text = (
        f"{'━'*20}\n"
        f"🏛️ *داشبورد فرماندهی*\n"
        f"{'━'*20}\n\n"
        f"{flag} *{name}*\n\n"
        f"💰 درآمد روزانه: `{fmt(p.get('daily_income', 70000000))}`\n"
        f"🏦 بودجه دولت: `{fmt(p.get('budget', 150000000))}`\n"
        f"{oil_line}\n"
        f"{sat_emoji} رضایت مردمی: `{sat}٪`\n"
        f"{alliance_status_text(p)}\n\n"
        f"{'─'*18}\n"
        f"⚔️ *نیروی زمینی*\n"
        f"{'─'*18}\n"
        f"🎖️ فرمانده: `{p.get('commander',0)}`   🪖 سرباز: `{p.get('soldier',0)}`\n"
        f"👮 پلیس: `{p.get('police',0)}`   🛡️ مرزبان: `{p.get('border_guard',0)}`\n"
        f"🕵️ جاسوس: `{p.get('spy',0)}`   🦅 یگان ویژه: `{p.get('special_forces',0)}`\n"
        f"🎯 تک‌تیرانداز: `{p.get('sniper',0)}`   💥 ار پی جی: `{p.get('rpg',0)}`\n"
        f"💣 بمب‌گذار: `{p.get('bomber',0)}`   🔧 خنثی‌کننده: `{p.get('bomb_defuser',0)}`\n"
        f"🌋 مین‌گذار: `{p.get('mine_layer',0)}`   🧹 خنثی‌کننده مین: `{p.get('mine_defuser',0)}`\n\n"
        f"{'─'*18}\n"
        f"✈️ *نیروی هوایی*\n"
        f"{'─'*18}\n"
        f"F‑16: `{p.get('f16',0)}`  F‑18: `{p.get('f18',0)}`  F‑22: `{p.get('f22',0)}`\n"
        f"F‑35: `{p.get('f35',0)}`  B‑1: `{p.get('b1',0)}`  B‑2: `{p.get('b2',0)}`  B‑52: `{p.get('b52',0)}`\n\n"
        f"{'─'*18}\n"
        f"⚓ *نیروی دریایی*\n"
        f"{'─'*18}\n"
        f"🛢️ نفت‌کش: `{p.get('oil_tanker',0)}`   🚢 کشتی: `{p.get('cargo_ship',0)}`\n"
        f"🛳️ ناو هواپیمابر: `{p.get('aircraft_carrier',0)}`   ⛵ قایق: `{p.get('warboat',0)}`\n"
        f"🤿 زیردریایی: `{p.get('submarine',0)}`   ⚔️ ناو جرالد فورد: `{p.get('gerald_ford',0)}`\n"
        f"👑 ناو ابراهام لینکن: `{p.get('abraham_lincoln',0)}`\n\n"
        f"{'─'*18}\n"
        f"🚀 *زرادخانه موشکی*\n"
        f"{'─'*18}\n"
        f"🎯 نقطه‌زن: `{p.get('precision',0)}`   💨 کروز: `{p.get('cruise',0)}`\n"
        f"⚡ خیبرشکن: `{p.get('khaibar',0)}`   🔥 خرمشهر ۴: `{p.get('khorramshahr',0)}`\n"
        f"🌐 DF‑26: `{p.get('df26',0)}`   ☢️ بمب اتم: `{p.get('atom_bomb',0)}`\n\n"
        f"{'─'*18}\n"
        f"🛬 *پهباد*\n"
        f"{'─'*18}\n"
        f"💥 انتحاری: `{p.get('suicide_drone',0)}`   🎯 نقطه‌زن: `{p.get('precision_drone',0)}`   👁️ شناسایی: `{p.get('recon_drone',0)}`\n\n"
        f"{'─'*18}\n"
        f"🚁 *بالگرد*\n"
        f"{'─'*18}\n"
        f"🐊 تمساح: `{p.get('crocodile',0)}`   🦅 آپاچی: `{p.get('apache',0)}`   🐍 کبری: `{p.get('cobra',0)}`   🔔 بل ۱۲: `{p.get('bell12',0)}`\n\n"
        f"{'─'*18}\n"
        f"🛡️ *پدافند*\n"
        f"{'─'*18}\n"
        f"🇺🇸 پاتریوت: `{p.get('patriot',0)}`   🌀 فلانکس: `{p.get('phalanx',0)}`   🔵 تاد: `{p.get('thaad',0)}`\n\n"
        f"{'─'*18}\n"
        f"🚜 *زرهپوش و تانک*\n"
        f"{'─'*18}\n"
        f"⚔️ ذوالفقار: `{p.get('zolfaghar',0)}`   🐆 پنتر: `{p.get('panther',0)}`   🦁 کرار: `{p.get('karrar',0)}`\n\n"
        f"{'─'*18}\n"
        f"💻 *جنگ سایبری*\n"
        f"{'─'*18}\n"
        f"🔓 هک دارایی: `{p.get('asset_hack',0)}`   🔒 ضد هک: `{p.get('anti_asset_hack',0)}`\n"
        f"⚔️ هک نظامی: `{p.get('military_hack',0)}`   🛡️ ضد هک نظامی: `{p.get('anti_military_hack',0)}`\n\n"
        f"{'─'*18}\n"
        f"🏙️ *زیرساخت مردمی*\n"
        f"{'─'*18}\n"
        f"🛒 سوپرمارکت: `{p.get('supermarket',0)}`   🏫 مدرسه: `{p.get('school',0)}`   🎒 مهد کودک: `{p.get('kindergarten',0)}`\n"
        f"🏬 پاساژ: `{p.get('mall',0)}`   ⛺ پناهگاه: `{p.get('shelter',0)}`   🏊 استخر: `{p.get('pool',0)}`\n"
        f"🏨 هتل: `{p.get('hotel',0)}`   🚇 مترو: `{p.get('metro',0)}`   🚌 اتوبوس: `{p.get('bus',0)}`\n"
        f"✈️ هواپیما: `{p.get('airplane',0)}`   🎡 شهربازی: `{p.get('amusement_park',0)}`\n\n"
        f"{'─'*18}\n"
        f"⛏️ *معادن*\n"
        f"{'─'*18}\n"
        f"💎 الماس: `{p.get('diamond_mine',0)}`   🥇 طلا: `{p.get('gold_mine',0)}`   🥈 نقره: `{p.get('silver_mine',0)}`\n"
        f"{'━'*20}"
    )
    return text

def main_menu_keyboard(user_id=None):
    rows = [
        [InlineKeyboardButton("🌍 کشور من", callback_data="my_country"),
         InlineKeyboardButton("🛒 بازار تسلیحات", callback_data="shop")],
        [InlineKeyboardButton("🏢 شرکت‌های بین‌المللی", callback_data="companies"),
         InlineKeyboardButton("📦 صادرات/واردات", callback_data="trade")],
        [InlineKeyboardButton("📢 بیانیه رسمی", callback_data="declaration"),
         InlineKeyboardButton("⚔️ قوانین جنگ", callback_data="rules")],
        [InlineKeyboardButton("💣 حمله نظامی", callback_data="attack"),
         InlineKeyboardButton("☢️ حمله اتمی", callback_data="nuke_menu")],
        [InlineKeyboardButton("🤝 اتحاد", callback_data="alliance_menu")], 
    ]
    if user_id and user_id == ADMIN_ID:
        rows.append([InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")])
    return InlineKeyboardMarkup(rows)


async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    
    
    is_member = await check_membership(user_id, context.bot)
    if not is_member:
        channels_text = "\n".join(f"{i+1}️⃣ {ch}" for i, ch in enumerate(REQUIRED_CHANNELS))
        await update.message.reply_text(
            "🔒 *دسترسی محدود شد!*\n\n"
            "برای ورود به میدان جنگ، باید عضو کانال‌های رسمی بشی:\n\n"
            f"{channels_text}\n\n"
            "بعد از عضویت، دوباره /start بزن تا وارد بازی بشی! ⚔️",
            disable_web_page_preview=True,
            parse_mode="Markdown"
        )
        return ConversationHandler.END
    
    p = get_player_by_id_full(user_id)
    if p and p.get("country"):
        info = get_country_info(p['country'])
        kbd = main_menu_keyboard(user_id)
        await update.message.reply_text(
            f"🎖️ *فرمانده، خوش برگشتی!*\n\n"
            f"🌍 کشور: {info['flag']} *{info['name']}*\n"
            f"🏦 بودجه: `{fmt(p.get('budget', 0))}`\n\n"
            f"میدان جنگ منتظرته... ⚔️",
            reply_markup=kbd,
            parse_mode="Markdown"
        )
        return MAIN_MENU

    keyboard = [
        [InlineKeyboardButton("🌍 انتخاب کشور", callback_data="pick_country")],
        [InlineKeyboardButton("🏴‍☠️ انتخاب گروهک", callback_data="pick_group")],
    ]
    await update.message.reply_text(
        "☠️ *به جنگ جهانی خوش اومدی!*\n\n"
        "━━━━━━━━━━━━━━━━━━━━\n"
        "⚔️ اینجا کشورها با هم می‌جنگن\n"
        "💰 بودجه‌ات رو مدیریت کن\n"
        "🚀 ارتشت رو قوی کن\n"
        "🌐 با دیگران تجارت کن\n"
        "━━━━━━━━━━━━━━━━━━━━\n\n"
        "کشور یا گروهک خودت رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(keyboard),
        parse_mode="Markdown"
    )
    return SELECT_COUNTRY

async def pick_country_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    taken = get_all_active_countries()
    rows = []
    row = []
    for code, info in COUNTRIES.items():
        oil = " 🛢️" if info["oil"] else ""
        taken_mark = " ✅" if code in taken else ""
        label = f"{info['flag']}{oil}{taken_mark}"
        row.append(InlineKeyboardButton(label, callback_data=f"sel_country_{code}"))
        if len(row) == 3:
            rows.append(row)
            row = []
    if row:
        rows.append(row)
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="back_start")])
    
    await query.edit_message_text(
        "🌍 *انتخاب کشور*\n\n🛢️ = نفت‌خیز | ✅ = گرفته شده\n\nروی کشور دلخواه بزن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SELECT_COUNTRY

async def pick_group_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    taken = get_all_active_countries()
    rows = []
    for code, info in GROUPS.items():
        taken_mark = " ✅" if code in taken else ""
        rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}{taken_mark}", callback_data=f"sel_country_{code}")])
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="back_start")])
    
    await query.edit_message_text(
        "🏴‍☠️ *انتخاب گروهک*\n\n✅ = گرفته شده\n\nروی گروهک دلخواه بزن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SELECT_COUNTRY

async def select_country(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    
    code = query.data.replace("sel_country_", "")
    
    if is_country_taken(code):
        info = get_country_info(code)
        await query.answer(f"❌ {info['name']} قبلاً گرفته شده!", show_alert=True)
        return SELECT_COUNTRY
    
    info = get_country_info(code)
    is_oil = info.get("oil", False) if info else False
    oil_income = 30000000 if is_oil else 0
    
   
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("""INSERT OR REPLACE INTO players 
        (user_id, username, country, budget, daily_income, oil_income, satisfaction, country_created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)""",
        (user_id, query.from_user.username or query.from_user.first_name,
         code, 150000000, 70000000, oil_income, 100, datetime.now().isoformat()))
    conn.commit()
    conn.close()
    
    oil_msg = f"\n🛢️ درآمد نفتی: `{fmt(oil_income)}` در روز" if is_oil else ""
    vip_msg = "\n👑 *کشور VIP — دسترسی به سلاح‌های ویژه!*" if info.get("vip") else ""

    await query.edit_message_text(
        f"✅ *فرماندهی {info['flag']} {info['name']} رو به دست گرفتی!*\n\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 درآمد روزانه: `{fmt(70000000)}`\n"
        f"🏦 بودجه اولیه: `{fmt(150000000)}`{oil_msg}{vip_msg}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"🎯 حالا وقت استراتژیه فرمانده!\n"
        f"ارتشت رو بساز و دنیا رو تسخیر کن ⚔️",
        reply_markup=main_menu_keyboard(user_id),
        parse_mode="Markdown"
    )
    return MAIN_MENU

async def back_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    keyboard = [
        [InlineKeyboardButton("🌍 انتخاب کشور", callback_data="pick_country")],
        [InlineKeyboardButton("🏴‍☠️ انتخاب گروهک", callback_data="pick_group")],
    ]
    await query.edit_message_text(
        "⚔️ *به جنگ جهانی خوش اومدی!*\n\nکشور یا گروهک انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(keyboard),
        parse_mode="Markdown"
    )
    return SELECT_COUNTRY


async def main_menu_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    
    is_member = await check_membership(user_id, context.bot)
    if not is_member:
        channels_text = "\n".join(f"{i+1}️⃣ {ch}" for i, ch in enumerate(REQUIRED_CHANNELS))
        await query.edit_message_text(
            "🔒 *دسترسی قطع شد!*\n\n"
            "فرمانده، عضویتت در کانال‌ها تأیید نشد!\n\n"
            f"{channels_text}\n\n"
            "عضو بشو و دوباره /start بزن ⚔️",
            parse_mode="Markdown",
            disable_web_page_preview=True
        )
        return ConversationHandler.END
    
    data = query.data
    
    if data == "my_country":
        return await show_my_country(update, context)
    elif data == "shop":
        return await show_shop(update, context)
    elif data == "companies":
        return await show_companies(update, context)
    elif data == "trade":
        return await show_trade(update, context)
    elif data == "declaration":
        return await show_declaration(update, context)
    elif data == "rules":
        return await show_rules(update, context)
    elif data == "attack":
        return await show_attack(update, context)
    elif data == "adm_manual_income":
        return await admin_manual_income(update, context)
    elif data == "admin_panel":
        if user_id == ADMIN_ID:
            await query.edit_message_text(
                "👑 *پنل ادمین*\n━━━━━━━━━━━━━━━━━━━━",
                reply_markup=admin_menu_keyboard(),
                parse_mode="Markdown"
            )
        return MAIN_MENU
    elif data == "main_menu":
        p = get_player_by_id_full(user_id)
        info = get_country_info(p["country"])
        await query.edit_message_text(
            f"🎖️ *مرکز فرماندهی*\n\n"
            f"{info['flag']} *{info['name']}*\n"
            f"🏦 بودجه: `{fmt(p.get('budget',0))}`\n\n"
            f"دستورت رو بده فرمانده ⚔️",
            reply_markup=main_menu_keyboard(user_id),
            parse_mode="Markdown"
        )
        return MAIN_MENU


async def show_my_country(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    if not p:
        await query.answer("ابتدا /start بزن!", show_alert=True)
        return MAIN_MENU
    
    
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT company_key FROM companies WHERE owner_user_id=?", (user_id,))
    user_companies = [r[0] for r in c.fetchall()]
    conn.close()
    
    text = country_status_text(p)
    
    if user_companies:
        text += "\n\n🏢 *شرکت‌های شما:*\n"
        for ck in user_companies:
            co = COMPANIES.get(ck)
            if co:
                text += f"• {co['name']}\n"
    
    keyboard = [[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(keyboard), parse_mode="Markdown")
    return MAIN_MENU


async def show_shop(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_shop_enabled():
        await query.edit_message_text(
            "🚫 *بازار تسلیحات موقتاً توسط سازمان جهانی بسته شده!*\nبعداً دوباره امتحان کن فرمانده.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU

    rows = []
    for cat_key, cat in SHOP_ITEMS.items():
        rows.append([InlineKeyboardButton(cat["name"], callback_data=f"shop_cat_{cat_key}")])

    rows.append([InlineKeyboardButton("💎 بسته ویژه ابرقدرت نظامی (تخفیف‌دار)", callback_data="shop_bundle")])

    cart = context.user_data.get("cart", {})
    cart_text = ""
    if cart:
        total = sum(v["price"] * v["qty"] for v in cart.values())
        cart_text = f"\n\n🛒 سبد خرید: *{len(cart)}* آیتم | جمع: `{fmt(total)}`"
        rows.append([InlineKeyboardButton("✅ تسویه حساب", callback_data="checkout"),
                     InlineKeyboardButton("🗑️ خالی کردن سبد", callback_data="clear_cart")])

    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")])

    await query.edit_message_text(
        f"🏪 *بازار تسلیحات جهانی*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 بودجه موجود: `{fmt(p.get('budget',0))}`{cart_text}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"دسته‌بندی مورد نظرت رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SHOP_MENU

async def shop_category(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    cat_key = query.data.replace("shop_cat_", "")
    cat = SHOP_ITEMS[cat_key]
    
    
    country_info = get_country_info(p.get("country", ""))
    is_vip = country_info.get("vip", False) if country_info else False
    
    context.user_data["shop_cat"] = cat_key
    
    rows = []
    for item_key, item in cat["items"].items():
        if item.get("vip") and not is_vip:
            continue
        cart = context.user_data.get("cart", {})
        in_cart = cart.get(item_key, {}).get("qty", 0)
        cart_badge = f" [{in_cart}]" if in_cart > 0 else ""
        rows.append([InlineKeyboardButton(
            f"{item['name']} - {fmt(item['price'])}{cart_badge}",
            callback_data=f"shop_item_{item_key}"
        )])
    
    rows.append([InlineKeyboardButton("🔙 برگشت به شاپ", callback_data="shop")])
    
    cart = context.user_data.get("cart", {})
    cart_text = ""
    if cart:
        total = sum(v["price"] * v["qty"] for v in cart.values())
        cart_text = f"\n🛒 سبد: *{len(cart)}* آیتم | `{fmt(total)}`"

    await query.edit_message_text(
        f"🏪 *{cat['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 بودجه: `{fmt(p.get('budget',0))}`{cart_text}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"محصول مورد نظرت رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SHOP_CATEGORY

async def shop_item(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    item_key = query.data.replace("shop_item_", "")
    
    
    item = None
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item = cat["items"][item_key]
            break
    
    if not item:
        return SHOP_CATEGORY
    
    context.user_data["shop_item"] = item_key
    cart = context.user_data.get("cart", {})
    in_cart = cart.get(item_key, {}).get("qty", 0)
    subtotal = item["price"] * in_cart

    rows = [
        [
            InlineKeyboardButton("1️⃣ +۱", callback_data="add_1"),
            InlineKeyboardButton("🔟 +۱۰", callback_data="add_10"),
        ],
        [
            InlineKeyboardButton("💯 +۱۰۰", callback_data="add_100"),
            InlineKeyboardButton("🔢 +۱۰۰۰", callback_data="add_1000"),
        ],
        [InlineKeyboardButton("✏️ تعداد دلخواه", callback_data="shop_qty_custom")],
        [InlineKeyboardButton("🛒 مشاهده سبد خرید", callback_data="view_cart")],
        [InlineKeyboardButton("🔙 برگشت", callback_data=f"shop_cat_{context.user_data.get('shop_cat','')}")],
    ]

    await query.edit_message_text(
        f"🔫 *{item['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💵 قیمت هر عدد: `{fmt(item['price'])}`\n"
        f"📦 در سبد: `{in_cart}` عدد\n"
        f"💰 جمع: `{fmt(subtotal)}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"چند تا اضافه کنم به سبدت؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SHOP_ITEM

async def add_to_cart(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    qty_map = {"add_1": 1, "add_10": 10, "add_100": 100, "add_1000": 1000}
    qty = qty_map.get(query.data, 1)
    
    item_key = context.user_data.get("shop_item")
    if not item_key:
        return SHOP_ITEM
    
    item = None
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item = cat["items"][item_key]
            break
    
    if not item:
        return SHOP_ITEM
    
    cart = context.user_data.get("cart", {})
    if item_key not in cart:
        cart[item_key] = {"name": item["name"], "price": item["price"], "qty": 0}
    cart[item_key]["qty"] += qty
    context.user_data["cart"] = cart
    
    in_cart = cart[item_key]["qty"]
    total_this = item["price"] * in_cart

    rows = [
        [
            InlineKeyboardButton("1️⃣ +۱", callback_data="add_1"),
            InlineKeyboardButton("🔟 +۱۰", callback_data="add_10"),
        ],
        [
            InlineKeyboardButton("💯 +۱۰۰", callback_data="add_100"),
            InlineKeyboardButton("🔢 +۱۰۰۰", callback_data="add_1000"),
        ],
        [InlineKeyboardButton("✏️ تعداد دلخواه", callback_data="shop_qty_custom")],
        [InlineKeyboardButton("🛒 مشاهده سبد خرید", callback_data="view_cart")],
        [InlineKeyboardButton("🔙 برگشت", callback_data=f"shop_cat_{context.user_data.get('shop_cat','')}")],
    ]

    await query.edit_message_text(
        f"✅ *{item['name']}* اضافه شد!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💵 قیمت هر عدد: `{fmt(item['price'])}`\n"
        f"📦 در سبد: `{in_cart}` عدد\n"
        f"💰 جمع این محصول: `{fmt(total_this)}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"بیشتر اضافه کنم؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SHOP_ITEM


async def shop_qty_custom_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    
    query = update.callback_query
    await query.answer()

    item_key = context.user_data.get("shop_item")
    item = None
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item = cat["items"][item_key]
            break

    if not item:
        return SHOP_ITEM

    await query.edit_message_text(
        f"✏️ *تعداد دلخواه — {item['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💵 قیمت هر عدد: `{fmt(item['price'])}`\n\n"
        f"یه عدد بفرست (مثلاً 250)، ربات قیمتش رو حساب می‌کنه و میندازه توی سبد:",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton("🔙 لغو", callback_data=f"shop_item_{item_key}")]
        ]),
        parse_mode="Markdown"
    )
    return SHOP_QUANTITY


async def shop_qty_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
  
    text = (update.message.text or "").strip().replace(",", "").replace("،", "")

    item_key = context.user_data.get("shop_item")
    item = None
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item = cat["items"][item_key]
            break

    if not item:
        await update.message.reply_text(
            "❌ آیتم پیدا نشد، دوباره از فروشگاه شروع کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🛒 برو به فروشگاه", callback_data="shop")]])
        )
        return SHOP_MENU

    if not text.isdigit() or int(text) <= 0:
        await update.message.reply_text(
            "❌ عدد نامعتبره. یه عدد صحیح و مثبت بفرست (مثلاً 250):",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("🔙 لغو", callback_data=f"shop_item_{item_key}")]
            ])
        )
        return SHOP_QUANTITY

    qty = int(text)

    cart = context.user_data.get("cart", {})
    if item_key not in cart:
        cart[item_key] = {"name": item["name"], "price": item["price"], "qty": 0}
    cart[item_key]["qty"] += qty
    context.user_data["cart"] = cart

    in_cart = cart[item_key]["qty"]
    total_this = item["price"] * in_cart
    added_cost = item["price"] * qty

    rows = [
        [
            InlineKeyboardButton("1️⃣ +۱", callback_data="add_1"),
            InlineKeyboardButton("🔟 +۱۰", callback_data="add_10"),
        ],
        [
            InlineKeyboardButton("💯 +۱۰۰", callback_data="add_100"),
            InlineKeyboardButton("🔢 +۱۰۰۰", callback_data="add_1000"),
        ],
        [InlineKeyboardButton("✏️ تعداد دلخواه", callback_data="shop_qty_custom")],
        [InlineKeyboardButton("🛒 مشاهده سبد خرید", callback_data="view_cart")],
        [InlineKeyboardButton("🔙 برگشت", callback_data=f"shop_cat_{context.user_data.get('shop_cat','')}")],
    ]

    await update.message.reply_text(
        f"✅ *{qty}* عدد *{item['name']}* اضافه شد!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💵 قیمت این خرید: `{fmt(added_cost)}`\n"
        f"📦 در سبد: `{in_cart}` عدد\n"
        f"💰 جمع این محصول: `{fmt(total_this)}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"بیشتر اضافه کنم؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return SHOP_ITEM

async def view_cart(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    cart = context.user_data.get("cart", {})
    if not cart:
        await query.answer("🛒 سبد خریدت خالیه!", show_alert=True)
        return SHOP_ITEM

    text = (
        "🛒 *سبد خرید شما*\n"
        "━━━━━━━━━━━━━━━━━━━━\n\n"
    )
    total = 0
    rows = []
    for k, v in cart.items():
        subtotal = v["price"] * v["qty"]
        total += subtotal
        text += f"• *{v['name']}*\n  `{v['qty']}` عدد × `{fmt(v['price'])}` = `{fmt(subtotal)}`\n\n"
        rows.append([InlineKeyboardButton(f"🗑️ حذف {v['name']}", callback_data=f"remove_item_{k}")])

    text += f"━━━━━━━━━━━━━━━━━━━━\n"
    text += f"💰 *جمع کل: `{fmt(total)}`*\n"
    text += f"🏦 بودجه: `{fmt(p.get('budget',0))}`"

    if total > p.get("budget", 0):
        text += "\n\n❌ *بودجه کافی نیست!*"
        rows.append([InlineKeyboardButton("🗑️ خالی کردن سبد", callback_data="clear_cart")])
        rows.append([InlineKeyboardButton("🔙 برگشت به بازار", callback_data="shop")])
    else:
        rows.append([InlineKeyboardButton("✅ تسویه و خرید", callback_data="checkout")])
        rows.append([InlineKeyboardButton("🗑️ خالی کردن سبد", callback_data="clear_cart")])
        rows.append([InlineKeyboardButton("🔙 ادامه خرید", callback_data="shop")])

    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")
    return SHOP_MENU

async def remove_cart_item(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    item_key = query.data.replace("remove_item_", "")
    cart = context.user_data.get("cart", {})
    if item_key in cart:
        del cart[item_key]
    context.user_data["cart"] = cart
    if not cart:
        await query.edit_message_text(
            "🛒 سبد خریدت خالی شد!",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🏪 برگشت به بازار", callback_data="shop")]])
        )
        return SHOP_MENU
    return await view_cart(update, context)

async def checkout(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_shop_enabled():
        await query.answer("🚫 بازار تسلیحات موقتاً بسته است!", show_alert=True)
        return SHOP_MENU

    cart = context.user_data.get("cart", {})
    if not cart:
        await query.answer("🛒 سبد خالیه!", show_alert=True)
        return SHOP_MENU

    total = sum(v["price"] * v["qty"] for v in cart.values())

    if total > p.get("budget", 0):
        await query.answer("❌ بودجه کافی نیست!", show_alert=True)
        return SHOP_MENU

    updates = {"budget": p["budget"] - total}
    for k, v in cart.items():
        current = p.get(k, 0) or 0
        updates[k] = current + v["qty"]
        mine_income = SHOP_ITEMS.get("mine", {}).get("items", {}).get(k, {}).get("daily_income", 0)
        if mine_income:
            updates["daily_income"] = updates.get("daily_income", p.get("daily_income", 70000000)) + mine_income * v["qty"]

    update_player(user_id, updates)
    context.user_data["cart"] = {}

  
    info = get_country_info(p.get("country", ""))
    items_text = "\n".join([f"  • {v['name']}: {v['qty']} عدد" for v in cart.values()])
    try:
        await context.bot.send_message(
            GROUP_1_ID,
            f"🛒 *خرید تسلیحاتی جدید!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 {info['flag']} *{info['name']}* تجهیزات خرید:\n"
            f"{items_text}\n"
            f"💸 مبلغ: `{fmt(total)}`\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Group announce error: {e}")

    await query.edit_message_text(
        f"✅ *خرید با موفقیت انجام شد!*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💸 پرداخت شده: `{fmt(total)}`\n"
        f"🏦 بودجه باقی‌مانده: `{fmt(p['budget'] - total)}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"تجهیزات به ارتشت اضافه شد فرمانده! ⚔️",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return MAIN_MENU

async def shop_bundle_preview(update: Update, context: ContextTypes.DEFAULT_TYPE):
    
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    items, original_total, discounted_total = compute_military_bundle()
    discount_pct = round((1 - discounted_total / original_total) * 100, 1) if original_total > 0 else 0
    savings = original_total - discounted_total

    cats_text = "، ".join(SHOP_ITEMS[c]["name"] for c in MILITARY_BUNDLE_CATEGORIES)

    rows = [
        [InlineKeyboardButton("💳 خرید بسته با تخفیف", callback_data="shop_bundle_confirm")],
        [InlineKeyboardButton("🔙 برگشت", callback_data="shop")],
    ]
    await query.edit_message_text(
        f"💎 *بسته ویژه ابرقدرت نظامی*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📦 شامل: از هر آیتم نظامی *{MILITARY_BUNDLE_QTY_PER_ITEM}* عدد\n"
        f"🗂 دسته‌ها: {cats_text}\n"
        f"🔢 تعداد کل آیتم‌ها: `{len(items)}` نوع تجهیزات\n\n"
        f"💰 قیمت اصلی: `{fmt(original_total)}`\n"
        f"🔥 تخفیف: `{discount_pct}٪`\n"
        f"✅ قیمت نهایی: `{fmt(discounted_total)}`\n"
        f"💸 سود تو از این خرید: `{fmt(savings)}`\n\n"
        f"🏦 بودجه فعلی‌ت: `{fmt(p.get('budget', 0))}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"⚠️ این یه خرید یکباره و سنگینه، با احتیاط تصمیم بگیر فرمانده!",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )


async def shop_bundle_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):
  
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_shop_enabled():
        await query.answer("🚫 بازار تسلیحات موقتاً بسته است!", show_alert=True)
        return

    items, original_total, discounted_total = compute_military_bundle()

    if discounted_total > p.get("budget", 0):
        await query.answer("❌ بودجه‌ات برای این بسته کافی نیست!", show_alert=True)
        return

    updates = {"budget": p.get("budget", 0) - discounted_total}
    for k, v in items.items():
        current = p.get(k, 0) or 0
        updates[k] = current + v["qty"]
    update_player(user_id, updates)

    info = get_country_info(p.get("country", ""))
    try:
        await context.bot.send_message(
            GROUP_1_ID,
            f"💎 *خرید بسته ویژه ابرقدرت نظامی!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 {info['flag']} *{info['name']}* یه بسته‌ی کامل تسلیحاتی خرید!\n"
            f"📦 از هر آیتم نظامی {MILITARY_BUNDLE_QTY_PER_ITEM} عدد\n"
            f"💸 مبلغ پرداختی: `{fmt(discounted_total)}`\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Bundle group announce error: {e}")

    await query.edit_message_text(
        f"✅ *بسته ویژه ابرقدرت نظامی خریداری شد!*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📦 از هر آیتم نظامی `{MILITARY_BUNDLE_QTY_PER_ITEM}` عدد به ارتشت اضافه شد\n"
        f"💸 پرداخت شده: `{fmt(discounted_total)}`\n"
        f"🏦 بودجه باقی‌مانده: `{fmt(updates['budget'])}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"حالا یه ابرقدرت واقعی شدی فرمانده! ⚔️",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت به مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )


async def clear_cart(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    context.user_data["cart"] = {}
    await query.edit_message_text(
        "🗑️ *سبد خرید خالی شد!*\n\nمیتونی دوباره خرید کنی فرمانده.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🏪 برگشت به بازار", callback_data="shop")]]),
        parse_mode="Markdown"
    )
    return SHOP_MENU


async def show_companies(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT company_key, owner_country FROM companies")
    owned = {r[0]: r[1] for r in c.fetchall()}
    conn.close()
    
    country_info = get_country_info(p.get("country", ""))
    is_oil = country_info.get("oil", False) if country_info else False
    oil = p.get("oil_income", 0)
    
    rows = []
    for co_key, co in COMPANIES.items():
        owner = owned.get(co_key)
        if owner:
            owner_info = get_country_info(owner)
            owner_name = owner_info["name"] if owner_info else owner
            label = f"🔒 {co['name']} | {owner_name}"
        else:
            label = f"🏢 {co['name']} | {fmt(co['price'])}"
        rows.append([InlineKeyboardButton(label, callback_data=f"co_detail_{co_key}")])
    
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")])
    
    await query.edit_message_text(
        f"🏢 *شرکت‌ها*\n💰 بودجه: {fmt(p.get('budget',0))}\n🛢️ نفت: {fmt(oil)}\n\nروی شرکت بزن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return COMPANY_MENU

async def company_detail(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    co_key = query.data.replace("co_detail_", "")
    co = COMPANIES.get(co_key)
    if not co:
        return COMPANY_MENU
    
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT owner_country FROM companies WHERE company_key=?", (co_key,))
    row = c.fetchone()
    conn.close()
    
    oil = p.get("oil_reserves", 0) or 0
    can_afford_budget = p.get("budget", 0) >= co["price"]
    can_afford_oil = oil >= co["oil_needed"] if co["oil_needed"] > 0 else True
    
    if row:
        owner = row["owner_country"]
        owner_info = get_country_info(owner)
        text = (
            f"🏢 *{co['name']}*\n\n"
            f"✅ خریداری شده توسط: {owner_info['flag']} {owner_info['name']}\n\n"
            f"💰 قیمت: {fmt(co['price'])}\n"
            f"📈 درآمد روزانه: {fmt(co['income'])}\n"
            f"🛢️ نفت مورد نیاز: {fmt(co['oil_needed'])}\n"
            f"📦 {co['description']}"
        )
        rows = [[InlineKeyboardButton("🔙 برگشت", callback_data="companies")]]
    else:
        text = (
            f"🏢 *{co['name']}*\n\n"
            f"💰 قیمت: {fmt(co['price'])}\n"
            f"📈 درآمد روزانه: {fmt(co['income'])}\n"
            f"🛢️ نفت مورد نیاز: {fmt(co['oil_needed'])}\n"
            f"📦 {co['description']}\n\n"
            f"💼 بودجه شما: {fmt(p.get('budget',0))}\n"
            f"🛢️ ذخایر نفت شما: {fmt(oil)}"
        )
        rows = []
        if can_afford_budget and can_afford_oil:
            rows.append([InlineKeyboardButton("✅ خرید شرکت", callback_data=f"buy_co_{co_key}")])
        else:
            rows.append([InlineKeyboardButton("❌ بودجه یا نفت کافی نیست", callback_data="companies")])
        rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="companies")])
    
    await query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")
    return COMPANY_MENU

async def buy_company(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    co_key = query.data.replace("buy_co_", "")
    co = COMPANIES.get(co_key)
    
    
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT id FROM companies WHERE company_key=?", (co_key,))
    if c.fetchone():
        await query.answer("این شرکت قبلاً خریداری شده!", show_alert=True)
        conn.close()
        return COMPANY_MENU
    
    new_budget = p["budget"] - co["price"]
    new_income = p.get("daily_income", 70000000) + co["income"]
    new_oil_reserves = (p.get("oil_reserves", 0) or 0) - co.get("oil_needed", 0)

    c.execute("INSERT INTO companies (company_key, owner_country, owner_user_id) VALUES (?,?,?)",
              (co_key, p["country"], user_id))
    conn.commit()
    conn.close()

    update_player(user_id, {"budget": new_budget, "daily_income": new_income, "oil_reserves": max(0, new_oil_reserves)})

    
    info = get_country_info(p.get("country", ""))
    try:
        await context.bot.send_message(
            GROUP_1_ID,
            f"🏢 *خرید شرکت بین‌المللی!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 {info['flag']} *{info['name']}*\n"
            f"🏭 شرکت *{co['name']}* رو خرید!\n"
            f"💰 ارزش: `{fmt(co['price'])}`\n"
            f"📈 درآمد روزانه: `{fmt(co['income'])}`\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Group announce error: {e}")

    await query.edit_message_text(
        f"✅ *{co['name']}* با موفقیت خریداری شد!\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"💸 پرداخت: `{fmt(co['price'])}`\n"
        f"📈 درآمد روزانه جدید: `{fmt(new_income)}`\n"
        f"🏦 بودجه باقی‌مانده: `{fmt(new_budget)}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"شرکت به اموال کشورت اضافه شد فرمانده! 🎉",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return MAIN_MENU


async def show_trade(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    if not is_trade_enabled():
        await query.edit_message_text(
            "🚫 *صادرات و واردات موقتاً توسط سازمان جهانی بسته شده!*\nبعداً دوباره امتحان کن فرمانده.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU

    
    tradeable = []
    
    
    all_items = {}
    for cat in SHOP_ITEMS.values():
        for k, v in cat["items"].items():
            all_items[k] = v["name"]
    
    for k, name in all_items.items():
        qty = p.get(k, 0) or 0
        if qty > 0:
            tradeable.append((k, name, qty))
    
    
    oil = p.get("oil_reserves", 0) or 0
    if oil > 0:
        tradeable.append(("oil", "نفت 🛢️", oil))
    
    if not tradeable:
        await query.edit_message_text(
            "📦 *صادرات/واردات*\n\n❌ هیچ محصولی برای صادرات نداری!",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU
    
    rows = []
    for k, name, qty in tradeable:
        rows.append([InlineKeyboardButton(f"{name} ({qty})", callback_data=f"trade_item_{k}")])
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")])
    
    context.user_data["tradeable"] = {k: qty for k, _, qty in tradeable}
    
    await query.edit_message_text(
        f"📦 *صادرات/واردات*\n\nمحصولی که میخوای بفروشی رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return TRADE_SELECT_ITEM

async def trade_item_selected(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    item_key = query.data.replace("trade_item_", "")
    context.user_data["trade_item"] = item_key
    
    tradeable = context.user_data.get("tradeable", {})
    max_qty = tradeable.get(item_key, 0)
    
    
    item_name = "نفت 🛢️"
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item_name = cat["items"][item_key]["name"]
            break
    
    context.user_data["trade_item_name"] = item_name
    context.user_data["trade_max_qty"] = max_qty
    
    await query.edit_message_text(
        f"📦 *{item_name}*\n\nحداکثر تعداد: {max_qty}\n\nتعداد که میخوای بفرستی رو تایپ کن (عدد انگلیسی):",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    context.user_data["trade_step"] = "qty"
    return TRADE_QUANTITY

async def trade_quantity_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    text = update.message.text.strip()
    
    step = context.user_data.get("trade_step")
    
    if step == "qty":
        try:
            qty = int(text)
        except ValueError:
            await update.message.reply_text("❌ عدد انگلیسی وارد کن!")
            return TRADE_QUANTITY
        
        max_qty = context.user_data.get("trade_max_qty", 0)
        if qty <= 0 or qty > max_qty:
            await update.message.reply_text(f"❌ تعداد باید بین ۱ تا {max_qty} باشه!")
            return TRADE_QUANTITY
        
        context.user_data["trade_qty"] = qty
        context.user_data["trade_step"] = "price"
        
        await update.message.reply_text(
            f"✅ تعداد: {qty}\n\nحالا قیمت کل رو بنویس (عدد انگلیسی):",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("🎁 رایگان", callback_data="trade_free")]
            ])
        )
        return TRADE_PRICE
    
    elif step == "price":
        try:
            price = int(text)
        except ValueError:
            await update.message.reply_text("❌ عدد انگلیسی وارد کن!")
            return TRADE_PRICE
        
        context.user_data["trade_price"] = price
        return await show_trade_confirm(update, context)

async def trade_free(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    context.user_data["trade_price"] = 0
    return await show_trade_confirm_query(update, context)

async def show_trade_confirm(update, context):
    item_name = context.user_data.get("trade_item_name", "")
    qty = context.user_data.get("trade_qty", 0)
    price = context.user_data.get("trade_price", 0)
    
    price_text = "رایگان 🎁" if price == 0 else fmt(price)
    
    rows = [
        [InlineKeyboardButton("✅ تایید", callback_data="trade_confirm"),
         InlineKeyboardButton("✏️ ویرایش", callback_data="trade_edit")],
        [InlineKeyboardButton("❌ لغو", callback_data="main_menu")]
    ]
    
    await update.message.reply_text(
        f"📋 *خلاصه صادرات*\n\n"
        f"📦 محصول: {item_name}\n"
        f"🔢 تعداد: {qty}\n"
        f"💰 قیمت: {price_text}\n\n"
        f"تایید میکنی؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return TRADE_CONFIRM

async def show_trade_confirm_query(update, context):
    query = update.callback_query
    await query.answer()
    
    item_name = context.user_data.get("trade_item_name", "")
    qty = context.user_data.get("trade_qty", 0)
    price = context.user_data.get("trade_price", 0)
    price_text = "رایگان 🎁" if price == 0 else fmt(price)
    
    rows = [
        [InlineKeyboardButton("✅ تایید", callback_data="trade_confirm"),
         InlineKeyboardButton("✏️ ویرایش", callback_data="trade_edit")],
        [InlineKeyboardButton("❌ لغو", callback_data="main_menu")]
    ]
    
    await query.edit_message_text(
        f"📋 *خلاصه صادرات*\n\n"
        f"📦 محصول: {item_name}\n"
        f"🔢 تعداد: {qty}\n"
        f"💰 قیمت: {price_text}\n\n"
        f"تایید میکنی؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return TRADE_CONFIRM

async def trade_confirm_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    
    active = get_all_active_countries()
    my_country = p.get("country")
    
    rows = []
    for code in active:
        if code == my_country:
            continue
        info = get_country_info(code)
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"trade_to_{code}")])
    
    rows.append([InlineKeyboardButton("❌ لغو", callback_data="main_menu")])
    
    await query.edit_message_text(
        "🌍 کشور مقصد رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows)
    )
    return TRADE_SELECT_COUNTRY

async def trade_to_country(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)
    
    target_country = query.data.replace("trade_to_", "")
    context.user_data["trade_target"] = target_country
    
    target_info = get_country_info(target_country)
    item_name = context.user_data.get("trade_item_name", "")
    qty = context.user_data.get("trade_qty", 0)
    price = context.user_data.get("trade_price", 0)
    price_text = "رایگان 🎁" if price == 0 else fmt(price)
    
    rows = [
        [InlineKeyboardButton("🚀 ارسال", callback_data="trade_send"),
         InlineKeyboardButton("🔙 برگشت", callback_data="trade_confirm")]
    ]
    
    await query.edit_message_text(
        f"📤 *تایید نهایی*\n\n"
        f"📦 {item_name} × {qty}\n"
        f"💰 {price_text}\n"
        f"🎯 مقصد: {target_info['flag']} {target_info['name']}\n\n"
        f"ارسال کنم؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return TRADE_SELECT_COUNTRY

async def trade_send(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_trade_enabled():
        await query.edit_message_text(
            "🚫 *صادرات و واردات موقتاً توسط سازمان جهانی بسته شده!*",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU
    
    item_key = context.user_data.get("trade_item")
    qty = context.user_data.get("trade_qty", 0)
    price = context.user_data.get("trade_price", 0)
    target_country = context.user_data.get("trade_target")
    item_name = context.user_data.get("trade_item_name", "")
    
    target_player = get_player_by_country(target_country)
    if not target_player:
        await query.answer("کشور مقصد پیدا نشد!", show_alert=True)
        return MAIN_MENU
    
    
    if price > 0 and target_player.get("budget", 0) < price:
        await query.answer("❌ بودجه کشور مقصد کافی نیست!", show_alert=True)
        return MAIN_MENU
    
    
    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("INSERT INTO trades (sender_country, receiver_country, item, quantity, price) VALUES (?,?,?,?,?)",
              (p["country"], target_country, item_key, qty, price))
    trade_id = c.lastrowid
    conn.commit()
    conn.close()
    
    
    sender_info = get_country_info(p["country"])
    price_text = "🎁 رایگان" if price == 0 else f"`{fmt(price)}`"

    try:
        await context.bot.send_message(
            target_player["user_id"],
            f"📬 *پیشنهاد رسمی صادرات!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 از: {sender_info['flag']} *{sender_info['name']}*\n"
            f"📦 محصول: *{item_name}* × `{qty}`\n"
            f"💰 قیمت: {price_text}\n"
            f"━━━━━━━━━━━━━━━━━━━━\n\n"
            f"آیا این معامله رو قبول میکنی فرمانده؟",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("✅ قبول معامله", callback_data=f"trade_accept_{trade_id}"),
                 InlineKeyboardButton("❌ رد معامله", callback_data=f"trade_reject_{trade_id}")]
            ]),
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Error sending trade offer: {e}")

    target_info = get_country_info(target_country)
    await query.edit_message_text(
        f"📤 *پیشنهاد صادرات ارسال شد!*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📦 {item_name} × `{qty}`\n"
        f"💰 قیمت: {price_text}\n"
        f"🎯 مقصد: {target_info['flag']} *{target_info['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"⏳ منتظر تایید طرف مقابل باش فرمانده...",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return MAIN_MENU

async def trade_accept(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id

    trade_id = int(query.data.replace("trade_accept_", ""))

    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM trades WHERE id=? AND status='pending'", (trade_id,))
    trade = c.fetchone()

    if not trade:
        await query.answer("⚠️ این معامله دیگه معتبر نیست!", show_alert=True)
        conn.close()
        return

    trade = dict(trade)
    sender = get_player_by_country(trade["sender_country"])
    receiver = get_player_by_id_full(user_id)

    if not sender or not receiver:
        await query.answer("❌ خطا در اطلاعات بازیکنان!", show_alert=True)
        conn.close()
        return

    price = trade["price"]
    item_key = trade["item"]
    qty = trade["quantity"]
    field = trade_field(item_key)

    if price > 0 and receiver.get("budget", 0) < price:
        await query.answer("❌ بودجه کافی نداری!", show_alert=True)
        conn.close()
        return

    sender_item = sender.get(field, 0) or 0
    if sender_item < qty:
        await query.answer("❌ فرستنده دیگه این مقدار رو نداره!", show_alert=True)
        conn.close()
        return

    
    mine_income_per = SHOP_ITEMS.get("mine", {}).get("items", {}).get(item_key, {}).get("daily_income", 0)

    
    sender_upd = {field: sender_item - qty, "budget": sender.get("budget", 0) + price}
    if mine_income_per > 0:
        sender_upd["daily_income"] = max(70000000, sender.get("daily_income", 70000000) - mine_income_per * qty)
    update_player(sender["user_id"], sender_upd)

   
    receiver_item = receiver.get(field, 0) or 0
    recv_upd = {field: receiver_item + qty, "budget": receiver.get("budget", 0) - price}
    if mine_income_per > 0:
        recv_upd["daily_income"] = receiver.get("daily_income", 70000000) + mine_income_per * qty
    update_player(user_id, recv_upd)

    c.execute("UPDATE trades SET status='accepted' WHERE id=?", (trade_id,))
    conn.commit()
    conn.close()

    sender_info = get_country_info(sender["country"])
    receiver_info = get_country_info(receiver["country"])
    item_name = "نفت 🛢️" if item_key == "oil" else item_key
    for cat in SHOP_ITEMS.values():
        if item_key in cat["items"]:
            item_name = cat["items"][item_key]["name"]
            break

    price_text = f"`{fmt(price)}`" if price > 0 else "🎁 رایگان"

    
    try:
        await query.edit_message_text(
            f"✅ *معامله قبول شد!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"📦 {item_name} × `{qty}`\n"
            f"💰 پرداختی: {price_text}\n"
            f"━━━━━━━━━━━━━━━━━━━━\n\n"
            f"🚚 محموله داره حرکت میکنه... ۱۵ دقیقه دیگه میرسه!",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Edit trade msg error: {e}")
        try:
            await context.bot.send_message(user_id,
                "✅ *معامله قبول شد!* محموله ۱۵ دقیقه دیگه میرسه 🚚",
                parse_mode="Markdown")
        except:
            pass

    
    try:
        await context.bot.send_message(
            sender["user_id"],
            f"✅ *معامله تایید شد!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 {receiver_info['flag']} *{receiver_info['name']}* قبول کرد!\n"
            f"📦 {item_name} × `{qty}` در راهه...\n"
            f"⏱️ تحویل در ۱۵ دقیقه\n"
            f"{'💰 دریافتی: ' + fmt(price) if price > 0 else '🎁 رایگان ارسال کردی'}\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Sender notify error: {e}")

    
    try:
        await context.bot.send_message(
            GROUP_1_ID,
            f"🤝 *معامله تجاری بین‌المللی!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"📤 {sender_info['flag']} *{sender_info['name']}*\n"
            f"    ⬇️\n"
            f"📥 {receiver_info['flag']} *{receiver_info['name']}*\n\n"
            f"📦 {item_name} × `{qty}`\n"
            f"💰 ارزش: {price_text}\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Group announce error: {e}")

    asyncio.create_task(deliver_trade_animated(context, user_id, item_name, qty, 15 * 60))

async def deliver_trade_animated(context, user_id, item_name, qty, delay):
    steps = [
        (delay // 3, "🚛 *محموله آماده ارسال شد...*\n📦 بارگیری کامل شد"),
        (delay // 3, "🛣️ *محموله در مسیره...*\n⏳ کمی صبر کن فرمانده"),
        (delay // 3, None),  
    ]
    for wait, msg in steps:
        await asyncio.sleep(wait)
        if msg:
            try:
                await context.bot.send_message(user_id, msg, parse_mode="Markdown")
            except:
                pass
    try:
        await context.bot.send_message(
            user_id,
            f"🎉 *محموله رسید فرمانده!*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"✅ {item_name} × `{qty}` به زرادخانه‌ات اضافه شد!\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Deliver error: {e}")

async def trade_reject(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()

    trade_id = int(query.data.replace("trade_reject_", ""))

    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("SELECT sender_country FROM trades WHERE id=? AND status='pending'", (trade_id,))
    row = c.fetchone()
    if not row:
        await query.edit_message_text("⚠️ این معامله قبلاً پردازش شده.")
        conn.close()
        return
    c.execute("UPDATE trades SET status='rejected' WHERE id=?", (trade_id,))
    conn.commit()
    conn.close()

    receiver_p = get_player_by_id_full(query.from_user.id)
    receiver_info = get_country_info(receiver_p.get("country", "")) if receiver_p else None

    sender = get_player_by_country(row[0])
    if sender:
        try:
            rej_msg = (
                f"❌ *معامله رد شد!*\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
            )
            if receiver_info:
                rej_msg += f"🌍 {receiver_info['flag']} *{receiver_info['name']}* پیشنهاد رو رد کرد.\n"
            rej_msg += f"━━━━━━━━━━━━━━━━━━━━\n\nمیتونی پیشنهاد جدید بدی فرمانده."
            await context.bot.send_message(sender["user_id"], rej_msg, parse_mode="Markdown")
        except Exception as e:
            logger.error(f"Trade reject notify error: {e}")

    
    try:
        await query.edit_message_text(
            f"❌ *معامله رد شد.*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"پیشنهاد فرستنده رو رد کردی فرمانده.\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Edit reject msg error: {e}")

async def trade_edit(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    item_name = context.user_data.get("trade_item_name", "")
    max_qty = context.user_data.get("trade_max_qty", 0)
    context.user_data["trade_step"] = "qty"
    
    await query.edit_message_text(
        f"📦 *{item_name}*\n\nحداکثر: {max_qty}\n\nتعداد جدید رو تایپ کن:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("❌ لغو", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return TRADE_QUANTITY


async def show_declaration(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    info = get_country_info(p.get("country", ""))

    await query.edit_message_text(
        f"📢 *صدور بیانیه رسمی*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🌍 کشور: {info['flag']} *{info['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"✍️ متن بیانیه‌ات رو بنویس:\n"
        f"_(بعد از ارسال، ادمین بررسی میکنه)_",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    context.user_data["decl_step"] = "text"
    return DECLARATION_TEXT

async def declaration_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    p = get_player_by_id_full(user_id)
    info = get_country_info(p.get("country", ""))

    text = update.message.text.strip()
    context.user_data["decl_text"] = text

    rows = [
        [InlineKeyboardButton("📤 ارسال برای تایید ادمین", callback_data="decl_submit")],
        [InlineKeyboardButton("❌ لغو", callback_data="main_menu")]
    ]

    await update.message.reply_text(
        f"📋 *پیش‌نمایش بیانیه*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🌍 {info['flag']} *{info['name']}*\n\n"
        f"📝 {text}\n"
        f"━━━━━━━━━━━━━━━━━━━━\n\n"
        f"ارسال میکنم برای تایید ادمین؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return DECLARATION_CONFIRM

async def declaration_submit(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    text = context.user_data.get("decl_text", "")
    info = get_country_info(p.get("country", ""))

    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute("INSERT INTO declarations (country, text) VALUES (?,?)", (p["country"], text))
    decl_id = c.lastrowid
    conn.commit()
    conn.close()

    try:
        await context.bot.send_message(
            ADMIN_ID,
            f"📢 *بیانیه جدید برای بررسی*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 کشور: {info['flag']} *{info['name']}*\n"
            f"🆔 شناسه: `{decl_id}`\n\n"
            f"📝 *متن بیانیه:*\n{text}\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("✅ تایید و انتشار", callback_data=f"adm_decl_ok_{decl_id}"),
                 InlineKeyboardButton("❌ رد کردن", callback_data=f"adm_decl_no_{decl_id}")]
            ]),
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Error sending to admin: {e}")

    await query.edit_message_text(
        f"✅ *بیانیه ارسال شد!*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"⏳ منتظر بررسی ادمین باش فرمانده.\n"
        f"بعد از تایید، بیانیه‌ات در گروه منتشر میشه 📢",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return MAIN_MENU

async def admin_decl_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id

    if user_id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return

    await query.answer("⏳ در حال پردازش...")

    
    if query.data.startswith("adm_decl_ok_"):
        decl_id = int(query.data.replace("adm_decl_ok_", ""))

        conn = sqlite3.connect("game.db")
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM declarations WHERE id=? AND status='pending'", (decl_id,))
        decl = c.fetchone()
        if not decl:
            conn.close()
            try:
                await query.edit_message_text(
                    "⚠️ این بیانیه قبلاً پردازش شده یا وجود نداره.",
                    parse_mode="Markdown"
                )
            except:
                pass
            return

        decl = dict(decl)
        c.execute("UPDATE declarations SET status='approved' WHERE id=?", (decl_id,))
        conn.commit()
        conn.close()

        info = get_country_info(decl["country"])
        flag = info["flag"] if info else "🌍"
        name = info["name"] if info else decl["country"]

       
        pub_msg = (
            f"📜 *بیانیه رسمی*\n"
            f"{'━'*22}\n"
            f"{flag} *{name}*\n\n"
            f"🗣️ {decl['text']}\n\n"
            f"{'━'*22}\n"
            f"_این بیانیه توسط سازمان جهانی تایید شده است_ ✅"
        )

        
        sent_ids = set()
        sent_ok = False
        for chat_id in [GROUP_1_ID, CHANNEL_ID]:
            if chat_id in sent_ids:
                continue
            sent_ids.add(chat_id)
            try:
                await context.bot.send_message(chat_id, pub_msg, parse_mode="Markdown")
                sent_ok = True
            except Exception as e:
                logger.error(f"Declaration send error to {chat_id}: {e}")

        
        player = get_player_by_country(decl["country"])
        if player:
            try:
                await context.bot.send_message(
                    player["user_id"],
                    f"🎉 *بیانیه‌ات تایید شد فرمانده!*\n"
                    f"{'━'*22}\n"
                    f"📢 بیانیه‌ات رسماً در گروه منتشر شد.\n"
                    f"همه کشورها الان میتونن ببیننش! 🌍\n"
                    f"{'━'*22}",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify decl user error: {e}")

        
        status_line = "✅ در گروه منتشر شد" if sent_ok else "⚠️ ارسال به گروه ناموفق — آیدی رو چک کن"
        try:
            await query.edit_message_text(
                f"✅ *بیانیه تایید و منتشر شد*\n"
                f"{'━'*22}\n"
                f"{flag} {name}\n"
                f"🆔 شناسه: `{decl_id}`\n"
                f"{status_line}",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Edit admin decl ok msg error: {e}")

    elif query.data.startswith("adm_decl_no_"):
        decl_id = int(query.data.replace("adm_decl_no_", ""))

        conn = sqlite3.connect("game.db")
        conn.row_factory = sqlite3.Row
        c = conn.cursor()
        c.execute("SELECT * FROM declarations WHERE id=? AND status='pending'", (decl_id,))
        row = c.fetchone()
        if not row:
            conn.close()
            try:
                await query.edit_message_text(
                    "⚠️ این بیانیه قبلاً پردازش شده یا وجود نداره.",
                    parse_mode="Markdown"
                )
            except:
                pass
            return

        row = dict(row)
        c.execute("UPDATE declarations SET status='rejected' WHERE id=?", (decl_id,))
        conn.commit()
        conn.close()

        info = get_country_info(row["country"])
        flag = info["flag"] if info else "🌍"
        name = info["name"] if info else row["country"]

        
        player = get_player_by_country(row["country"])
        if player:
            try:
                await context.bot.send_message(
                    player["user_id"],
                    f"❌ *بیانیه‌ات رد شد فرمانده!*\n"
                    f"{'━'*22}\n"
                    f"متن بیانیه‌ات توسط ادمین تایید نشد.\n"
                    f"میتونی بیانیه جدیدی صادر کنی. ✍️\n"
                    f"{'━'*22}",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify decl reject error: {e}")

        
        try:
            await query.edit_message_text(
                f"❌ *بیانیه رد شد*\n"
                f"{'━'*22}\n"
                f"{flag} {name}\n"
                f"🆔 شناسه: `{decl_id}`\n"
                f"کاربر مطلع شد.",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Edit admin decl no msg error: {e}")


def admin_menu_keyboard():
    return InlineKeyboardMarkup([
        [InlineKeyboardButton("💰 واریز دستی برای همه", callback_data="adm_manual_income")],
        [InlineKeyboardButton("📢 ارسال بیانیه ادمین", callback_data="adm_broadcast")],
        [InlineKeyboardButton("💵 افزایش بودجه کشور", callback_data="adm_pick_ma"),
         InlineKeyboardButton("💸 کسر بودجه کشور", callback_data="adm_pick_ms")],
        [InlineKeyboardButton("📦 افزودن تجهیزات", callback_data="adm_pick_ea"),
         InlineKeyboardButton("🗑 کسر تجهیزات", callback_data="adm_pick_es")],
        [InlineKeyboardButton("⚠️ اخطار به کشور", callback_data="adm_pick_wn"),
         InlineKeyboardButton("✅ حذف اخطار کشور", callback_data="adm_pick_wr")],
        [InlineKeyboardButton("☠️ حذف کامل کشور", callback_data="adm_pick_dc")],
        [InlineKeyboardButton("🤝 حذف اتحاد", callback_data="adm_alliance_del_list")],
        [InlineKeyboardButton("🏆 رتبه‌بندی ابرقدرت‌ها", callback_data="adm_power_rank")],
        [InlineKeyboardButton("⚙️ تنظیمات جنگ و بازار", callback_data="adm_settings")],
        [InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")],
    ])

def admin_settings_text():
    return (
        "⚙️ *تنظیمات جنگ و بازار*\n"
        "━━━━━━━━━━━━━━━━━━━━\n"
        "با زدن هر دکمه می‌تونی بازش کنی یا ببندیش.\n"
        "وقتی چیزی رو ببندی، تا وقتی دوباره بازش نکنی هیچ بازیکنی نمی‌تونه اون کارو انجام بده.\n"
        "برای تنظیم زمان‌ها هم روی دکمه‌ی مربوطه بزن و عدد دقیقه رو بفرست."
    )

def admin_settings_keyboard():
    war_on = is_war_enabled()
    gwar_on = is_group_war_enabled()
    shop_on = is_shop_enabled()
    trade_on = is_trade_enabled()
    war_cd = get_int_setting("war_cooldown_min", ATTACK_COOLDOWN_SECONDS // 60)
    gwar_cd = get_int_setting("group_war_cooldown_min", ATTACK_COOLDOWN_SECONDS // 60)
    protect_min = get_int_setting("newbie_protection_min", NEWBIE_PROTECTION_SECONDS // 60)

    return InlineKeyboardMarkup([
        [InlineKeyboardButton(
            f"{'🟢 بازه' if war_on else '🔴 بسته‌ست'} — 💣 جنگ عادی", callback_data="adm_toggle_war"
        )],
        [InlineKeyboardButton(
            f"{'🟢 بازه' if gwar_on else '🔴 بسته‌ست'} — ⚔️ جنگ گروهی", callback_data="adm_toggle_group_war"
        )],
        [InlineKeyboardButton(
            f"{'🟢 بازه' if shop_on else '🔴 بسته‌ست'} — 🏪 خرید تجهیزات", callback_data="adm_toggle_shop"
        )],
        [InlineKeyboardButton(
            f"{'🟢 بازه' if trade_on else '🔴 بسته‌ست'} — 📦 صادرات/واردات", callback_data="adm_toggle_trade"
        )],
        [InlineKeyboardButton(f"⏱ کول‌داون جنگ عادی: {war_cd} دقیقه ✏️", callback_data="adm_set_war_cd")],
        [InlineKeyboardButton(f"⏱ کول‌داون جنگ گروهی: {gwar_cd} دقیقه ✏️", callback_data="adm_set_group_cd")],
        [InlineKeyboardButton(f"🛡 محافظت تازه‌کار (ضد ضعیف‌کشی): {protect_min} دقیقه ✏️", callback_data="adm_set_protect")],
        [InlineKeyboardButton("🔙 برگشت", callback_data="admin_panel")],
    ])

async def admin_settings_callbacks(update: Update, context: ContextTypes.DEFAULT_TYPE):
    
    query = update.callback_query
    user_id = query.from_user.id
    if user_id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return
    await query.answer()
    data = query.data

    if data == "adm_settings":
        await query.edit_message_text(admin_settings_text(), reply_markup=admin_settings_keyboard(), parse_mode="Markdown")
        return

    if data == "adm_toggle_war":
        set_bool_setting("war_enabled", not is_war_enabled())
    elif data == "adm_toggle_group_war":
        set_bool_setting("group_war_enabled", not is_group_war_enabled())
    elif data == "adm_toggle_shop":
        set_bool_setting("shop_enabled", not is_shop_enabled())
    elif data == "adm_toggle_trade":
        set_bool_setting("trade_enabled", not is_trade_enabled())
    elif data == "adm_set_war_cd":
        context.user_data["admin_await"] = "set_war_cd"
        await query.edit_message_text(
            "⏱ *تنظیم کول‌داون جنگ عادی*\n━━━━━━━━━━━━━━━━━━━━\n"
            "✍️ هر چند دقیقه یک‌بار بازیکن‌ها بتونن جنگ عادی راه بندازن؟ عدد دقیقه رو بفرست (مثلاً 30):",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="adm_settings")]]),
            parse_mode="Markdown"
        )
        return
    elif data == "adm_set_group_cd":
        context.user_data["admin_await"] = "set_group_cd"
        await query.edit_message_text(
            "⏱ *تنظیم کول‌داون جنگ گروهی*\n━━━━━━━━━━━━━━━━━━━━\n"
            "✍️ هر چند دقیقه یک‌بار لیدر اتحاد بتونه حمله گروهی راه بندازه؟ عدد دقیقه رو بفرست (مثلاً 30):",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="adm_settings")]]),
            parse_mode="Markdown"
        )
        return
    elif data == "adm_set_protect":
        context.user_data["admin_await"] = "set_protect"
        await query.edit_message_text(
            "🛡 *تنظیم زمان محافظت تازه‌کار (ضد ضعیف‌کشی)*\n━━━━━━━━━━━━━━━━━━━━\n"
            "✍️ تا چند دقیقه بعد از ساخت کشور، نشه بهش حمله کرد؟ عدد دقیقه رو بفرست (مثلاً 30):",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="adm_settings")]]),
            parse_mode="Markdown"
        )
        return
    else:
        return

    
    await query.edit_message_text(admin_settings_text(), reply_markup=admin_settings_keyboard(), parse_mode="Markdown")


def admin_country_list_keyboard(action):
   
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT user_id, country FROM players WHERE country IS NOT NULL")
    rows = [dict(r) for r in c.fetchall()]
    conn.close()

    def sort_key(r):
        info = get_country_info(r["country"])
        return info["name"] if info else r["country"]

    rows.sort(key=sort_key)

    buttons = []
    row = []
    for r in rows:
        info = get_country_info(r["country"])
        if not info:
            continue
        label = f"{info['flag']} {info['name']}"
        row.append(InlineKeyboardButton(label, callback_data=f"adm_country|{action}|{r['country']}"))
        if len(row) == 2:
            buttons.append(row)
            row = []
    if row:
        buttons.append(row)

    if not rows:
        buttons.append([InlineKeyboardButton("⚠️ هیچ کشور فعالی نیست", callback_data="admin_panel")])

    buttons.append([InlineKeyboardButton("🔙 برگشت", callback_data="admin_panel")])
    return InlineKeyboardMarkup(buttons)

def admin_category_keyboard(action, code):
    buttons = []
    row = []
    for cat_key, cat in SHOP_ITEMS.items():
        row.append(InlineKeyboardButton(cat["name"], callback_data=f"adm_cat|{action}|{code}|{cat_key}"))
        if len(row) == 2:
            buttons.append(row)
            row = []
    if row:
        buttons.append(row)
    buttons.append([InlineKeyboardButton("🔙 برگشت", callback_data=f"adm_pick_{action}")])
    return InlineKeyboardMarkup(buttons)

def admin_item_keyboard(action, code, cat):
    buttons = []
    row = []
    items = SHOP_ITEMS.get(cat, {}).get("items", {})
    for item_key, item in items.items():
        row.append(InlineKeyboardButton(item["name"], callback_data=f"adm_item|{action}|{code}|{item_key}"))
        if len(row) == 2:
            buttons.append(row)
            row = []
    if row:
        buttons.append(row)
    buttons.append([InlineKeyboardButton("🔙 برگشت", callback_data=f"adm_country|{action}|{code}")])
    return InlineKeyboardMarkup(buttons)


def admin_alliance_list_keyboard():
    buttons = []
    for a in get_all_alliances():
        leader_info = get_country_info(a["leader_country"])
        label = f"🤝 {a['name']} ({leader_info['flag'] if leader_info else ''} {leader_info['name'] if leader_info else a['leader_country']})"
        buttons.append([InlineKeyboardButton(label, callback_data=f"adm_alliance_del_{a['id']}")])
    if not buttons:
        buttons.append([InlineKeyboardButton("⚠️ هیچ اتحادی وجود نداره", callback_data="admin_panel")])
    buttons.append([InlineKeyboardButton("🔙 برگشت", callback_data="admin_panel")])
    return InlineKeyboardMarkup(buttons)


async def admin_alliance_delete_list(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if query.from_user.id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return
    await query.answer()
    await query.edit_message_text(
        "🤝 *حذف اتحاد*\n━━━━━━━━━━━━━━━━━━━━\nاتحادی که می‌خوای حذف کنی رو انتخاب کن:",
        reply_markup=admin_alliance_list_keyboard(),
        parse_mode="Markdown"
    )


async def admin_alliance_delete_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    if query.from_user.id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return
    await query.answer()
    alliance_id = int(query.data.replace("adm_alliance_del_", ""))
    alliance = get_alliance(alliance_id)
    if not alliance:
        await query.edit_message_text(
            "⚠️ این اتحاد دیگه وجود نداره.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="admin_panel")]])
        )
        return

    members = get_alliance_members_players(alliance_id)
    delete_alliance_db(alliance_id)

    await query.edit_message_text(
        f"✅ اتحاد *{alliance['name']}* توسط ادمین حذف شد.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
        parse_mode="Markdown"
    )
    for m in members:
        try:
            await context.bot.send_message(
                m["user_id"],
                f"🗑 *اتحاد منحل شد!*\nاتحاد *{alliance['name']}* توسط ادمین حذف شد.",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Notify admin alliance delete error: {e}")


async def admin_menu_callbacks(update: Update, context: ContextTypes.DEFAULT_TYPE):
   
    query = update.callback_query
    user_id = query.from_user.id

    if user_id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return

    await query.answer()
    data = query.data

   
    if data == "adm_broadcast":
        context.user_data["admin_await"] = "broadcast"
        await query.edit_message_text(
            "📢 *ارسال بیانیه ادمین*\n"
            "━━━━━━━━━━━━━━━━━━━━\n\n"
            "✍️ متن بیانیه رو بنویس، مستقیم در گروه و کانال منتشر میشه:",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="admin_panel")]]),
            parse_mode="Markdown"
        )
        return

   
    if data == "adm_power_rank":
        await query.edit_message_text(
            "🏆 در حال محاسبه و انتشار رتبه‌بندی ابرقدرت‌ها...",
            parse_mode="Markdown"
        )
        await publish_power_rankings(context)
        await query.edit_message_text(
            "✅ رتبه‌بندی ابرقدرت‌ها در کانال منتشر شد.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
            parse_mode="Markdown"
        )
        return


    if data.startswith("adm_pick_"):
        action = data.replace("adm_pick_", "")
        titles = {
            "ma": "💵 افزایش بودجه — کشور رو انتخاب کن",
            "ms": "💸 کسر بودجه — کشور رو انتخاب کن",
            "ea": "📦 افزودن تجهیزات — کشور رو انتخاب کن",
            "es": "🗑 کسر تجهیزات — کشور رو انتخاب کن",
            "wn": "⚠️ اخطار به کشور — کشور رو انتخاب کن",
            "wr": "✅ حذف اخطار — کشور رو انتخاب کن",
            "dc": "☠️ حذف کامل کشور — کشور رو انتخاب کن",
        }
        if action not in titles:
            return
        await query.edit_message_text(
            f"👑 *{titles[action]}*",
            reply_markup=admin_country_list_keyboard(action),
            parse_mode="Markdown"
        )
        return

   
    if data.startswith("adm_country|"):
        _, action, code = data.split("|", 2)
        info = get_country_info(code)
        if not info:
            await query.edit_message_text("⚠️ کشور نامعتبر است.", parse_mode="Markdown")
            return

        if action in ("ma", "ms"):
            context.user_data["admin_await"] = "money"
            context.user_data["admin_action"] = action
            context.user_data["admin_target"] = code
            label = "افزایش بودجه" if action == "ma" else "کسر بودجه"
            await query.edit_message_text(
                f"💰 *{label}*\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
                f"🌍 کشور: {info['flag']} {info['name']}\n\n"
                f"✍️ مبلغ مورد نظر رو به عدد بفرست:",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="admin_panel")]]),
                parse_mode="Markdown"
            )
        elif action in ("ea", "es"):
            await query.edit_message_text(
                f"📦 *انتخاب دسته تجهیزات*\n"
                f"🌍 {info['flag']} {info['name']}\n\n"
                f"یه دسته رو انتخاب کن:",
                reply_markup=admin_category_keyboard(action, code),
                parse_mode="Markdown"
            )
        elif action == "wn":
            result = give_warning(code)
            if not result:
                await query.edit_message_text("⚠️ خطا در اعمال اخطار.", parse_mode="Markdown")
                return
            if result["deleted"]:
                await query.edit_message_text(
                    f"☠️ *کشور {info['flag']} {info['name']} به اخطار پنجم رسید و کاملاً حذف شد!*\n"
                    f"این کشور الان آزاده و کس دیگه‌ای می‌تونه انتخابش کنه.",
                    reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
                    parse_mode="Markdown"
                )
                try:
                    await context.bot.send_message(
                        GROUP_1_ID,
                        f"☠️ *کشور {info['flag']} {info['name']} به‌خاطر تخلف مکرر توسط سازمان جهانی حذف شد!*",
                        parse_mode="Markdown"
                    )
                except Exception as e:
                    logger.error(f"Warning delete announce error: {e}")
            else:
                penalty_pct = int(result["penalty_ratio"] * 100)
                penalty_text = f"💸 جریمه: `{fmt(result['penalty_amount'])}` ({penalty_pct}٪ بودجه)" if result["penalty_amount"] > 0 else "💸 جریمه: بدون جریمه مالی"
                await query.edit_message_text(
                    f"⚠️ *اخطار به {info['flag']} {info['name']} ثبت شد*\n"
                    f"━━━━━━━━━━━━━━━━━━━━\n"
                    f"🔢 تعداد اخطار: `{result['warnings']}` از `{MAX_WARNINGS}`\n"
                    f"{penalty_text}\n"
                    f"🏦 بودجه جدید: `{fmt(result['new_budget'])}`",
                    reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
                    parse_mode="Markdown"
                )
                try:
                    await context.bot.send_message(
                        result["user_id"],
                        f"⚠️ *اخطار رسمی از طرف سازمان جهانی!*\n"
                        f"━━━━━━━━━━━━━━━━━━━━\n"
                        f"🔢 تعداد اخطار: `{result['warnings']}` از `{MAX_WARNINGS}`\n"
                        f"{penalty_text}\n"
                        f"🏦 بودجه جدید: `{fmt(result['new_budget'])}`\n\n"
                        f"⛔️ با اخطار پنجم کشورت کاملاً حذف می‌شه!",
                        parse_mode="Markdown"
                    )
                except Exception as e:
                    logger.error(f"Notify warning error: {e}")
        elif action == "wr":
            result = clear_warning(code)
            if not result:
                await query.edit_message_text("⚠️ خطا در حذف اخطار.", parse_mode="Markdown")
                return
            await query.edit_message_text(
                f"✅ *یک اخطار از {info['flag']} {info['name']} حذف شد*\n"
                f"🔢 تعداد اخطار باقیمانده: `{result['warnings']}` از `{MAX_WARNINGS}`",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
                parse_mode="Markdown"
            )
            try:
                await context.bot.send_message(
                    result["user_id"],
                    f"✅ *یکی از اخطارهات توسط سازمان جهانی حذف شد!*\n"
                    f"🔢 تعداد اخطار باقیمانده: `{result['warnings']}` از `{MAX_WARNINGS}`",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify warning clear error: {e}")
        elif action == "dc":
            await query.edit_message_text(
                f"☠️ *حذف کامل کشور*\n"
                f"━━━━━━━━━━━━━━━━━━━━\n"
                f"🌍 کشور: {info['flag']} {info['name']}\n\n"
                f"⚠️ این عمل غیرقابل بازگشته! کشور کاملاً از بازی حذف می‌شه و آزاد می‌شه.\n"
                f"مطمئنی؟",
                reply_markup=InlineKeyboardMarkup([
                    [InlineKeyboardButton("☠️ بله، حذف کن", callback_data=f"adm_delconfirm|{code}"),
                     InlineKeyboardButton("🔙 لغو", callback_data="admin_panel")]
                ]),
                parse_mode="Markdown"
            )
        return

  
    if data.startswith("adm_cat|"):
        _, action, code, cat = data.split("|", 3)
        info = get_country_info(code)
        cat_info = SHOP_ITEMS.get(cat)
        if not info or not cat_info:
            return
        await query.edit_message_text(
            f"📦 *{cat_info['name']}*\n"
            f"🌍 {info['flag']} {info['name']}\n\n"
            f"آیتم مورد نظر رو انتخاب کن:",
            reply_markup=admin_item_keyboard(action, code, cat),
            parse_mode="Markdown"
        )
        return

  
    if data.startswith("adm_item|"):
        _, action, code, item_key = data.split("|", 3)
        info = get_country_info(code)
        if not info:
            return
        item_name = get_item_name(item_key)
        context.user_data["admin_await"] = "equip"
        context.user_data["admin_action"] = action
        context.user_data["admin_target"] = code
        context.user_data["admin_item"] = item_key
        label = "افزودن" if action == "ea" else "کسر"
        await query.edit_message_text(
            f"📦 *{label} تجهیزات*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🌍 کشور: {info['flag']} {info['name']}\n"
            f"🎯 آیتم: {item_name}\n\n"
            f"✍️ تعداد مورد نظر رو به عدد بفرست:",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="admin_panel")]]),
            parse_mode="Markdown"
        )
        return


async def admin_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
    
    if update.effective_user.id != ADMIN_ID:
        return

    awaiting = context.user_data.get("admin_await")
    if not awaiting:
        return  

    text = (update.message.text or "").strip()

  
    if awaiting == "broadcast":
        context.user_data["admin_await"] = None
        msg = (
            f"📢 *بیانیه رسمی سازمان جهانی*\n"
            f"{'━'*22}\n\n"
            f"{text}\n\n"
            f"{'━'*22}"
        )
        sent_ids = set()
        ok = 0
        for chat_id in [GROUP_1_ID, CHANNEL_ID]:
            if chat_id in sent_ids:
                continue
            sent_ids.add(chat_id)
            try:
                await context.bot.send_message(chat_id, msg, parse_mode="Markdown")
                ok += 1
            except Exception as e:
                logger.error(f"Admin broadcast error to {chat_id}: {e}")

        await update.message.reply_text(
            f"✅ بیانیه ادمین ارسال شد. (به {ok} مقصد)",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]]),
            parse_mode="Markdown"
        )
        raise ApplicationHandlerStop()


    if awaiting == "money":
        action = context.user_data.get("admin_action")
        code = context.user_data.get("admin_target")

        try:
            amount = int(text.replace(",", "").replace("،", "").strip())
        except ValueError:
            await update.message.reply_text("⚠️ لطفاً فقط عدد بفرست.")
            raise ApplicationHandlerStop()

        if amount <= 0:
            await update.message.reply_text("⚠️ عدد باید بزرگتر از صفر باشه.")
            raise ApplicationHandlerStop()

        player = get_player_by_country(code)
        info = get_country_info(code)
        if not player or not info:
            context.user_data["admin_await"] = None
            await update.message.reply_text("⚠️ این کشور بازیکن فعالی نداره.")
            raise ApplicationHandlerStop()

        context.user_data["admin_await"] = None

        if action == "ma":
            new_budget = (player.get("budget", 0) or 0) + amount
            update_player(player["user_id"], {"budget": new_budget})
            await update.message.reply_text(
                f"✅ مبلغ `{fmt(amount)}` به {info['flag']} {info['name']} واریز شد.\n"
                f"🏦 بودجه جدید: `{fmt(new_budget)}`",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]])
            )
            try:
                await context.bot.send_message(
                    player["user_id"],
                    f"💰 *واریز خصوصی از طرف سازمان جهانی!*\n"
                    f"{'━'*20}\n"
                    f"مبلغ `{fmt(amount)}` به بودجه دولتت واریز شد.\n"
                    f"🏦 بودجه جدید: `{fmt(new_budget)}`",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify money add error: {e}")
        else:  
            current = player.get("budget", 0) or 0
            new_budget = max(0, current - amount)
            deducted = current - new_budget
            update_player(player["user_id"], {"budget": new_budget})
            await update.message.reply_text(
                f"✅ مبلغ `{fmt(deducted)}` از {info['flag']} {info['name']} کسر شد.\n"
                f"🏦 بودجه جدید: `{fmt(new_budget)}`",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]])
            )
            try:
                await context.bot.send_message(
                    player["user_id"],
                    f"⚠️ *کسر بودجه توسط سازمان جهانی!*\n"
                    f"{'━'*20}\n"
                    f"مبلغ `{fmt(deducted)}` از بودجه دولتت کسر شد.\n"
                    f"🏦 بودجه جدید: `{fmt(new_budget)}`",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify money sub error: {e}")

        raise ApplicationHandlerStop()


    if awaiting == "equip":
        action = context.user_data.get("admin_action")
        code = context.user_data.get("admin_target")
        item_key = context.user_data.get("admin_item")

        try:
            qty = int(text.replace(",", "").replace("،", "").strip())
        except ValueError:
            await update.message.reply_text("⚠️ لطفاً فقط عدد بفرست.")
            raise ApplicationHandlerStop()

        if qty <= 0:
            await update.message.reply_text("⚠️ عدد باید بزرگتر از صفر باشه.")
            raise ApplicationHandlerStop()

        player = get_player_by_country(code)
        info = get_country_info(code)
        if not player or not info:
            context.user_data["admin_await"] = None
            await update.message.reply_text("⚠️ این کشور بازیکن فعالی نداره.")
            raise ApplicationHandlerStop()

        context.user_data["admin_await"] = None
        item_name = get_item_name(item_key)
        current = player.get(item_key, 0) or 0

        if action == "ea":
            new_val = current + qty
            verb = "اضافه شد"
            verb_user = "به ارتشت اضافه شد"
            applied_qty = qty
        else:  
            new_val = max(0, current - qty)
            applied_qty = current - new_val
            verb = "کسر شد"
            verb_user = "از ارتشت کسر شد"

        update_player(player["user_id"], {item_key: new_val})

        await update.message.reply_text(
            f"✅ تعداد `{fmt(applied_qty)}` {item_name} {verb} ({info['flag']} {info['name']}).\n"
            f"📦 موجودی جدید: `{fmt(new_val)}`",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("👑 پنل ادمین", callback_data="admin_panel")]])
        )
        try:
            await context.bot.send_message(
                player["user_id"],
                f"📦 *تغییر تجهیزات توسط سازمان جهانی!*\n"
                f"{'━'*20}\n"
                f"تعداد `{fmt(applied_qty)}` {item_name} {verb_user}.\n"
                f"📦 موجودی جدید: `{fmt(new_val)}`",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Notify equip change error: {e}")

        raise ApplicationHandlerStop()


   
    if awaiting == "set_war_cd":
        context.user_data["admin_await"] = None
        try:
            minutes = int(text.replace(",", "").replace("،", "").strip())
        except ValueError:
            await update.message.reply_text("⚠️ لطفاً فقط عدد بفرست (دقیقه).")
            raise ApplicationHandlerStop()
        if minutes <= 0:
            await update.message.reply_text("⚠️ عدد باید بزرگتر از صفر باشه.")
            raise ApplicationHandlerStop()
        set_setting("war_cooldown_min", minutes)
        await update.message.reply_text(
            f"✅ کول‌داون جنگ عادی روی `{minutes}` دقیقه تنظیم شد.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("⚙️ تنظیمات", callback_data="adm_settings")]])
        )
        raise ApplicationHandlerStop()

    
    if awaiting == "set_group_cd":
        context.user_data["admin_await"] = None
        try:
            minutes = int(text.replace(",", "").replace("،", "").strip())
        except ValueError:
            await update.message.reply_text("⚠️ لطفاً فقط عدد بفرست (دقیقه).")
            raise ApplicationHandlerStop()
        if minutes <= 0:
            await update.message.reply_text("⚠️ عدد باید بزرگتر از صفر باشه.")
            raise ApplicationHandlerStop()
        set_setting("group_war_cooldown_min", minutes)
        await update.message.reply_text(
            f"✅ کول‌داون جنگ گروهی روی `{minutes}` دقیقه تنظیم شد.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("⚙️ تنظیمات", callback_data="adm_settings")]])
        )
        raise ApplicationHandlerStop()

    
    if awaiting == "set_protect":
        context.user_data["admin_await"] = None
        try:
            minutes = int(text.replace(",", "").replace("،", "").strip())
        except ValueError:
            await update.message.reply_text("⚠️ لطفاً فقط عدد بفرست (دقیقه).")
            raise ApplicationHandlerStop()
        if minutes < 0:
            await update.message.reply_text("⚠️ عدد نمی‌تونه منفی باشه.")
            raise ApplicationHandlerStop()
        set_setting("newbie_protection_min", minutes)
        await update.message.reply_text(
            f"✅ زمان محافظت تازه‌کار (ضد ضعیف‌کشی) روی `{minutes}` دقیقه تنظیم شد.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("⚙️ تنظیمات", callback_data="adm_settings")]])
        )
        raise ApplicationHandlerStop()


async def admin_panel(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    if user_id != ADMIN_ID:
        return
    await update.message.reply_text(
        "👑 *پنل ادمین*\n━━━━━━━━━━━━━━━━━━━━",
        reply_markup=admin_menu_keyboard(),
        parse_mode="Markdown"
    )

async def admin_manual_income(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    if user_id != ADMIN_ID:
        await query.answer("❌ دسترسی ندارید!", show_alert=True)
        return

    await query.edit_message_text(
        "⏳ *در حال واریز دستی برای همه کشورها...*\n🔄 لطفاً صبر کن.",
        parse_mode="Markdown"
    )

    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players")
    players = [dict(r) for r in c.fetchall()]
    conn.close()

    count = 0
    for p in players:
        total_income = p.get("daily_income", 70000000) + p.get("oil_income", 0)
        new_budget = p["budget"] + total_income
        oil_add = p.get("oil_income", 0)  
        
        upd = {"budget": new_budget}
        if oil_add > 0:
            upd["oil_reserves"] = (p.get("oil_reserves", 0) or 0) + oil_add
        update_player(p["user_id"], upd)

        conn2 = sqlite3.connect("game.db")
        c2 = conn2.cursor()
        c2.execute("SELECT company_key FROM companies WHERE owner_user_id=?", (p["user_id"],))
        companies = [r[0] for r in c2.fetchall()]
        conn2.close()

        co_income = sum(COMPANIES[ck]["income"] for ck in companies if ck in COMPANIES)
        if co_income > 0:
            p2 = get_player_by_id_full(p["user_id"])
            update_player(p["user_id"], {"budget": p2["budget"] + co_income})
            for ck in companies:
                co = COMPANIES.get(ck)
                if co and co.get("daily_produce"):
                    p3 = get_player_by_id_full(p["user_id"])
                    prod_updates = {}
                    for prod_key, prod_qty in co["daily_produce"].items():
                        curr = p3.get(prod_key, 0) or 0
                        prod_updates[prod_key] = curr + prod_qty
                    if prod_updates:
                        update_player(p["user_id"], prod_updates)

        try:
            info = get_country_info(p.get("country", ""))
            p_final = get_player_by_id_full(p["user_id"])
            lines = [
                f"💰 *واریز دستی توسط ادمین!*",
                f"{'━'*20}",
                f"{info['flag']} *{info['name']}*",
                f"",
                f"💰 درآمد روزانه: +`{fmt(p.get('daily_income',70000000))}`",
            ]
            if p.get("oil_income", 0) > 0:
                lines.append(f"🛢️ درآمد نفتی: +`{fmt(p.get('oil_income',0))}`")
            if co_income > 0:
                lines.append(f"🏢 درآمد شرکت‌ها: +`{fmt(co_income)}`")
            lines += [
                f"{'─'*18}",
                f"🏦 بودجه جدید: `{fmt(p_final.get('budget',0))}`",
                f"{'━'*20}",
            ]
            await context.bot.send_message(
                p["user_id"],
                "\n".join(lines),
                parse_mode="Markdown"
            )
            count += 1
        except Exception as e:
            logger.error(f"Manual income notify error: {e}")

    await query.edit_message_text(
        f"✅ *واریز دستی انجام شد!*\n\n"
        f"💰 به `{count}` کشور واریز شد.\n\n"
        f"👑 پنل ادمین",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton("💰 واریز مجدد", callback_data="adm_manual_income")],
            [InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")],
        ]),
        parse_mode="Markdown"
    )
    return MAIN_MENU





DANCE_FRAMES = [
    "🕺",
    "🕺💃",
    "🕺💃🕺",
    "💃🕺💃",
    "🕺💃🕺💃",
    "⚡🕺💃🕺⚡",
    "🔥💃🕺💃🔥",
    "💥🕺💃🕺💃💥",
]

async def show_attack(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_war_enabled():
        await query.edit_message_text(
            "🚫 *جنگ عادی موقتاً توسط سازمان جهانی بسته شده!*\nبعداً دوباره امتحان کن فرمانده.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU

    
    last_attack = p.get("last_attack")
    if last_attack:
        elapsed = (datetime.now() - datetime.fromisoformat(last_attack)).total_seconds()
        cooldown = get_war_cooldown_seconds()
        if elapsed < cooldown:
            remain = int(cooldown - elapsed)
            await query.edit_message_text(
                f"⏳ فرمانده، ارتشت هنوز نفس‌نفس می‌زنه!\n"
                f"باید {remain // 60} دقیقه و {remain % 60} ثانیه دیگه صبر کنی.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
                parse_mode="Markdown"
            )
            return MAIN_MENU

    active = get_all_active_countries()
    my_country = p.get("country")
    rows = []
    protected_count = 0
    for code in active:
        if code == my_country:
            continue
        defender = get_player_by_country(code)
        if get_protection_remaining(defender) is not None:
            protected_count += 1
            continue
        info = get_country_info(code)
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"attack_target_{code}")])

    if not rows:
        msg = "❌ هیچ کشور دیگه‌ای برای حمله وجود نداره."
        if protected_count:
            msg += f"\n🛡 {protected_count} کشور تازه‌ساز هنوز توی دوره‌ی محافظته."
        await query.edit_message_text(
            msg,
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return MAIN_MENU

    rows.append([InlineKeyboardButton("❌ لغو", callback_data="main_menu")])
    protection_note = f"\n🛡 {protected_count} کشور تازه‌ساز توی دوره‌ی محافظته و توی لیست نیست." if protected_count else ""
    await query.edit_message_text(
        f"💣 *سامانه حمله نظامی*\n━━━━━━━━━━━━━━━━━━━━\n🎯 کشور هدف رو انتخاب کن:{protection_note}",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return ATTACK_SELECT_TARGET


async def attack_select_target(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    target_country = query.data.replace("attack_target_", "")
    context.user_data["attack_target"] = target_country

   
    defender = get_player_by_country(target_country)
    remain = get_protection_remaining(defender)
    if remain is not None:
        await query.edit_message_text(
            f"🛡 *این کشور هنوز تازه‌ساز و در حال محافظته!*\n\n"
            f"باید {remain // 60} دقیقه و {remain % 60} ثانیه دیگه صبر کنی تا بشه بهش حمله کرد.\n"
            f"(ضد ضعیف‌کشی — کشورهای تازه‌ساخته‌شده تا ۳۰ دقیقه امن هستن)",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU

    max_atk_power = calc_max_attack_power(p)
    if max_atk_power <= 0:
        await query.edit_message_text(
            "❌ هیچ تجهیزات حمله‌ای نداری! اول از بازار تسلیحات خرید کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🛒 برو به بازار", callback_data="shop")]])
        )
        return MAIN_MENU

    return await ask_attack_percent(update, context)


async def ask_attack_percent(update: Update, context: ContextTypes.DEFAULT_TYPE):
   
    query = update.callback_query
    target_country = context.user_data.get("attack_target")
    target_info = get_country_info(target_country)

    rows = [
        [InlineKeyboardButton("25٪", callback_data="attack_pct_25"),
         InlineKeyboardButton("50٪", callback_data="attack_pct_50")],
        [InlineKeyboardButton("75٪", callback_data="attack_pct_75"),
         InlineKeyboardButton("💯 100٪ (همه‌ی تجهیزات)", callback_data="attack_pct_100")],
        [InlineKeyboardButton("✏️ تایپ درصد دلخواه", callback_data="attack_pct_custom")],
        [InlineKeyboardButton("🔙 لغو", callback_data="main_menu")],
    ]
    await query.edit_message_text(
        f"🎯 هدف: {target_info['flag']} *{target_info['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📦 چند درصد از تجهیزات حمله‌ات رو وارد این عملیات می‌کنی؟\n\n"
        f"از دکمه‌ها انتخاب کن یا یه عدد بین ۱ تا ۱۰۰ تایپ کن.\n"
        f"⚠️ فقط همون درصدی که انتخاب کنی مصرف می‌شه؛ بقیه دست‌نخورده می‌مونه.",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    return ATTACK_PERCENT


async def attack_percent_button(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    percent = int(query.data.replace("attack_pct_", ""))
    context.user_data["attack_percent"] = percent
    return await show_attack_preview(update, context)


async def attack_percent_custom_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text(
        "✏️ یه عدد بین ۱ تا ۱۰۰ بفرست (درصد تجهیزاتی که می‌خوای وارد حمله کنی):",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]])
    )
    return ATTACK_PERCENT_TEXT


async def attack_percent_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
    text = (update.message.text or "").strip().replace("٪", "").replace("%", "")
    if not text.isdigit() or not (1 <= int(text) <= 100):
        await update.message.reply_text(
            "❌ عدد نامعتبره. یه عدد بین ۱ تا ۱۰۰ بفرست.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]])
        )
        return ATTACK_PERCENT_TEXT

    context.user_data["attack_percent"] = int(text)
    return await show_attack_preview(update, context, from_message=True)


async def show_attack_preview(update: Update, context: ContextTypes.DEFAULT_TYPE, from_message=False):
   
    user_id = update.effective_user.id
    p = get_player_by_id_full(user_id)

    target_country = context.user_data.get("attack_target")
    percent = context.user_data.get("attack_percent", 100)

    target = get_player_by_country(target_country)
    target_info = get_country_info(target_country)
    my_info = get_country_info(p.get("country"))

    atk_power = calc_attack_power(p, percent)
    def_power = calc_defense_power(target)

    if atk_power <= 0:
        text = "❌ با این درصد، قدرت حمله‌ات صفره! درصد بیشتری انتخاب کن."
        kb = InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        if from_message:
            await update.message.reply_text(text, reply_markup=kb)
        else:
            await update.callback_query.edit_message_text(text, reply_markup=kb)
        return MAIN_MENU

    predicted_percent = max(5, min(95, (atk_power / (atk_power + def_power)) * 100))
    if predicted_percent >= ATTACK_ANNIHILATE_THRESHOLD:
        conquest_warning = "\n\n☢️ *در صورت آسیب ۸۵٪+، کشور حریف کاملاً نابود و حذف می‌شه!*"
    elif predicted_percent >= ATTACK_CONQUER_THRESHOLD:
        conquest_warning = "\n\n🏴 *در صورت آسیب ۷۰٪+، کشور حریف کاملاً فتح می‌شه!*"
    else:
        conquest_warning = ""

    rows = [
        [InlineKeyboardButton("💥 شروع حمله", callback_data="attack_confirm"),
         InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]
    ]
    text = (
        f"🎯 *پیش‌نمایش حمله*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"{my_info['flag']} {my_info['name']} ⚔️ {target_info['flag']} {target_info['name']}\n\n"
        f"📦 تجهیزات استفاده‌شده: `{percent}٪`\n"
        f"💪 قدرت حمله‌ی شما: `{fmt(atk_power)}`\n"
        f"🛡 قدرت دفاع حریف: `{fmt(def_power)}`\n\n"
        f"📊 درصد آسیب تخمینی: `{round(predicted_percent)}٪`{conquest_warning}\n\n"
        f"⚠️ توجه: فقط {percent}٪ از تجهیزات حمله‌ات مصرف می‌شه؛ بقیه‌اش برات می‌مونه.\n"
        f"مطمئنی می‌خوای حمله کنی؟"
    )

    if from_message:
        await update.message.reply_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")
    else:
        await update.callback_query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")
    return ATTACK_CONFIRM


async def attack_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not is_war_enabled():
        await query.edit_message_text(
            "🚫 *جنگ عادی موقتاً توسط سازمان جهانی بسته شده!*",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
            parse_mode="Markdown"
        )
        return MAIN_MENU

    target_country = context.user_data.get("attack_target")
    percent = context.user_data.get("attack_percent", 100)
    if not target_country:
        await query.edit_message_text(
            "❌ هدف حمله مشخص نیست. دوباره از منو شروع کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return MAIN_MENU

    result = resolve_attack(p["country"], target_country, percent)

    if result.get("error") == "no_equipment":
        await query.edit_message_text(
            "❌ هیچ تجهیزات حمله‌ای نداری! اول از بازار تسلیحات خرید کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🛒 برو به بازار", callback_data="shop")]])
        )
        return MAIN_MENU

    if result.get("error") == "protected":
        await query.edit_message_text(
            "🛡 این کشور تازه‌ساز و توی دوره‌ی محافظته، نمیشه بهش حمله کرد.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return MAIN_MENU

    if result.get("error"):
        await query.edit_message_text(
            "❌ خطا در اجرای حمله.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return MAIN_MENU

    update_player(user_id, {"last_attack": datetime.now().isoformat()})
    context.user_data.pop("attack_target", None)
    context.user_data.pop("attack_percent", None)

    attacker_info = get_country_info(result["attacker_country"])
    defender_info = get_country_info(result["defender_country"])

    if result["annihilated"]:
        winner_line = f"🏆 *برنده: حمله* — کشور {defender_info['flag']} {defender_info['name']} کاملاً نابود و حذف شد!"
        outcome_line = "☢️ این کشور الان آزاده و کس دیگه‌ای می‌تونه انتخابش کنه."
    elif result["conquered"]:
        winner_line = f"🏆 *برنده: حمله* — کشور {defender_info['flag']} {defender_info['name']} فتح شد!"
        outcome_line = f"🏴 *کشور {defender_info['flag']} {defender_info['name']} کاملاً فتح شد!*"
    else:
        winner_line = f"🛡 *برنده: دفاع* — {defender_info['flag']} {defender_info['name']} در برابر حمله مقاومت کرد."
        outcome_line = f"💥 *{round(result['damage_percent'])}٪ آسیب* به کشور حریف وارد شد."

    result_text = (
        f"⚔️ *گزارش حمله نظامی*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🗡 حمله‌کننده: {attacker_info['flag']} *{attacker_info['name']}*\n"
        f"🛡 دفاع‌کننده: {defender_info['flag']} *{defender_info['name']}*\n\n"
        f"{winner_line}\n\n"
        f"📦 درصد تجهیزات استفاده‌شده: `{result['percent_used']}٪`\n"
        f"💪 قدرت حمله: `{fmt(result['atk_power'])}`\n"
        f"🛡 قدرت دفاع: `{fmt(result['def_power'])}`\n"
        f"📊 درصد آسیب: `{result['damage_percent']}٪`\n\n"
        f"{outcome_line}\n"
        f"💸 غرامت دریافتی: `{fmt(result['transferred'])}`\n"
        f"━━━━━━━━━━━━━━━━━━━━"
    )

    await query.edit_message_text(
        result_text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )


    try:
        await context.bot.send_message(GROUP_1_ID, result_text, parse_mode="Markdown")
    except Exception as e:
        logger.error(f"Error announcing attack result: {e}")


    defender_player = get_player_by_country(result["defender_country"])
    if defender_player and defender_player["user_id"] != user_id:
        try:
            await context.bot.send_message(
                defender_player["user_id"],
                f"🚨 *کشورت مورد حمله قرار گرفت!*\n\n{result_text}",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Error notifying defender: {e}")

    return MAIN_MENU


def alliance_status_text(p):
  
    if not p or not p.get("alliance_id"):
        return "🤝 اتحاد: عضو هیچ اتحادی نیستی"
    alliance = get_alliance(p["alliance_id"])
    if not alliance:
        return "🤝 اتحاد: عضو هیچ اتحادی نیستی"
    role = "👑 لیدر" if alliance["leader_user_id"] == p["user_id"] else "👤 عضو"
    return f"🤝 اتحاد: *{alliance['name']}* ({role})"


def alliance_main_keyboard(p):
    alliance_id = p.get("alliance_id") if p else None

    if not alliance_id:
        rows = []
        total = count_alliances()
        if total < MAX_ALLIANCES:
            rows.append([InlineKeyboardButton(
                f"🏗 ساخت اتحاد جدید ({fmt(ALLIANCE_CREATE_COST)})", callback_data="alli_create_prompt"
            )])
        for alliance in get_all_alliances():
            info = get_country_info(alliance["leader_country"])
            label = f"🤝 پیوستن به «{alliance['name']}»"
            rows.append([InlineKeyboardButton(label, callback_data=f"alli_join_{alliance['id']}")])
        rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")])
        return InlineKeyboardMarkup(rows)

    alliance = get_alliance(alliance_id)
    is_leader = alliance and alliance["leader_user_id"] == p["user_id"]

    if is_leader:
        rows = [
            [InlineKeyboardButton("👥 اعضای اتحاد", callback_data="alli_members")],
            [InlineKeyboardButton("➕ افزودن عضو", callback_data="alli_add_prompt"),
             InlineKeyboardButton("➖ اخراج عضو", callback_data="alli_kick_prompt")],
            [InlineKeyboardButton("⚔️ حمله گروهی", callback_data="alli_attack_menu")],
            [InlineKeyboardButton("🗑 انحلال اتحاد", callback_data="alli_disband_confirm")],
            [InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")],
        ]
    else:
        rows = [
            [InlineKeyboardButton("👥 اعضای اتحاد", callback_data="alli_members")],
            [InlineKeyboardButton("🚪 خروج از اتحاد", callback_data="alli_leave")],
            [InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")],
        ]
    return InlineKeyboardMarkup(rows)


async def alliance_main_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)

    if not p or not p.get("country"):
        await query.edit_message_text(
            "❌ اول باید یه کشور انتخاب کنی.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        raise ApplicationHandlerStop()

    alliance_id = p.get("alliance_id")
    if not alliance_id:
        total = count_alliances()
        lines = [f"🤝 *سیستم اتحاد*\n━━━━━━━━━━━━━━━━━━━━",
                 f"💰 هزینه‌ی ساخت اتحاد: `{fmt(ALLIANCE_CREATE_COST)}`",
                 f"📊 اتحادهای فعال: {total} از {MAX_ALLIANCES}\n"]
        alliances = get_all_alliances()
        if alliances:
            lines.append("اتحادهای موجود:")
            for a in alliances:
                leader_info = get_country_info(a["leader_country"])
                members_count = len(get_alliance_members_players(a["id"]))
                lines.append(f"  • *{a['name']}* — لیدر: {leader_info['flag']} {leader_info['name']} ({members_count} عضو)")
        else:
            lines.append("هنوز هیچ اتحادی ساخته نشده.")
        text = "\n".join(lines)
        await query.edit_message_text(text, reply_markup=alliance_main_keyboard(p), parse_mode="Markdown")
        raise ApplicationHandlerStop()

    alliance = get_alliance(alliance_id)
    if not alliance:
        update_player(user_id, {"alliance_id": None})
        await query.edit_message_text(
            "⚠️ اتحاد قبلیت دیگه وجود نداره.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        raise ApplicationHandlerStop()

    members = get_alliance_members_players(alliance_id)
    leader_info = get_country_info(alliance["leader_country"])
    text = (
        f"🤝 *{alliance['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"👑 لیدر: {leader_info['flag']} {leader_info['name']}\n"
        f"👥 تعداد اعضا: {len(members)}\n\n"
        f"از منوی زیر استفاده کن:"
    )
    await query.edit_message_text(text, reply_markup=alliance_main_keyboard(p), parse_mode="Markdown")
    raise ApplicationHandlerStop()


async def alliance_create_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)

    if not p or not p.get("country"):
        await query.edit_message_text(
            "❌ اول باید یه کشور انتخاب کنی.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        raise ApplicationHandlerStop()

    if p.get("alliance_id"):
        await query.edit_message_text(
            "❌ تو قبلاً عضو یه اتحادی هستی.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    if count_alliances() >= MAX_ALLIANCES:
        await query.edit_message_text(
            f"❌ ظرفیت اتحادها پره! حداکثر {MAX_ALLIANCES} اتحاد توی این بازی مجازه.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    if (p.get("budget", 0) or 0) < ALLIANCE_CREATE_COST:
        await query.edit_message_text(
            f"❌ بودجه‌ات کافی نیست!\n💰 هزینه‌ی ساخت اتحاد: `{fmt(ALLIANCE_CREATE_COST)}`\n🏦 بودجه‌ی تو: `{fmt(p.get('budget', 0))}`",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
            parse_mode="Markdown"
        )
        raise ApplicationHandlerStop()

    context.user_data["awaiting_alliance_name"] = True
    await query.edit_message_text(
        f"🏗 *ساخت اتحاد جدید*\n━━━━━━━━━━━━━━━━━━━━\n"
        f"💰 هزینه: `{fmt(ALLIANCE_CREATE_COST)}`\n\n"
        f"✍️ یه اسم برای اتحادت بفرست:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="alliance_menu")]]),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_join(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)

    if not p or not p.get("country"):
        await query.answer("❌ اول کشورت رو انتخاب کن.", show_alert=True)
        raise ApplicationHandlerStop()

    if p.get("alliance_id"):
        await query.answer("❌ تو قبلاً عضو یه اتحادی هستی.", show_alert=True)
        raise ApplicationHandlerStop()

    alliance_id = int(query.data.replace("alli_join_", ""))
    alliance = get_alliance(alliance_id)
    if not alliance:
        await query.answer("❌ این اتحاد دیگه وجود نداره.", show_alert=True)
        raise ApplicationHandlerStop()

    set_player_alliance(user_id, alliance_id)
    await query.edit_message_text(
        f"✅ به اتحاد *{alliance['name']}* پیوستی!",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🤝 منوی اتحاد", callback_data="alliance_menu")]]),
        parse_mode="Markdown"
    )
    try:
        my_info = get_country_info(p.get("country"))
        await context.bot.send_message(
            alliance["leader_user_id"],
            f"👋 *عضو جدید به اتحادت پیوست!*\n{my_info['flag']} {my_info['name']} به *{alliance['name']}* پیوست.",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Notify alliance leader join error: {e}")
    raise ApplicationHandlerStop()


async def alliance_leave(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None

    if not alliance_id:
        await query.answer("❌ عضو هیچ اتحادی نیستی.", show_alert=True)
        raise ApplicationHandlerStop()

    alliance = get_alliance(alliance_id)
    if alliance and alliance["leader_user_id"] == user_id:
        await query.answer("👑 تو لیدر این اتحادی! اگه می‌خوای ترکش کنی باید منحلش کنی.", show_alert=True)
        raise ApplicationHandlerStop()

    set_player_alliance(user_id, None)
    await query.edit_message_text(
        "✅ از اتحاد خارج شدی.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
    )
    if alliance:
        try:
            my_info = get_country_info(p.get("country"))
            await context.bot.send_message(
                alliance["leader_user_id"],
                f"🚪 {my_info['flag']} {my_info['name']} از اتحاد *{alliance['name']}* خارج شد.",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Notify alliance leader leave error: {e}")
    raise ApplicationHandlerStop()


async def alliance_members_view(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance:
        await query.answer("❌ عضو هیچ اتحادی نیستی.", show_alert=True)
        raise ApplicationHandlerStop()

    members = get_alliance_members_players(alliance_id)
    lines = [f"👥 *اعضای اتحاد {alliance['name']}*\n━━━━━━━━━━━━━━━━━━━━"]
    for m in members:
        info = get_country_info(m.get("country"))
        role = "👑" if m["user_id"] == alliance["leader_user_id"] else "👤"
        if info:
            lines.append(f"{role} {info['flag']} {info['name']}")
    await query.edit_message_text(
        "\n".join(lines),
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_add_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه عضو اضافه کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    active = get_all_active_countries()
    rows = []
    for code in active:
        other = get_player_by_country(code)
        if other and other.get("alliance_id"):
            continue
        info = get_country_info(code)
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"alli_addpick_{code}")])

    if not rows:
        await query.edit_message_text(
            "⚠️ هیچ کشور آزادی (بدون اتحاد) برای اضافه کردن نیست.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")])
    await query.edit_message_text(
        "➕ *افزودن عضو به اتحاد*\n\nیه کشور رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_add_pick(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه عضو اضافه کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    code = query.data.replace("alli_addpick_", "")
    target = get_player_by_country(code)
    info = get_country_info(code)
    if not target or not info:
        await query.answer("❌ این کشور پیدا نشد.", show_alert=True)
        raise ApplicationHandlerStop()

    if target.get("alliance_id"):
        await query.answer("❌ این کشور قبلاً عضو یه اتحاد دیگه‌ست.", show_alert=True)
        raise ApplicationHandlerStop()

    set_player_alliance(target["user_id"], alliance_id)
    await query.edit_message_text(
        f"✅ {info['flag']} {info['name']} به اتحاد *{alliance['name']}* اضافه شد.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🤝 منوی اتحاد", callback_data="alliance_menu")]]),
        parse_mode="Markdown"
    )
    try:
        await context.bot.send_message(
            target["user_id"],
            f"🤝 *به اتحاد دعوت شدی!*\nلیدر اتحاد *{alliance['name']}* تو رو به اتحادش اضافه کرد.",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Notify new alliance member error: {e}")
    raise ApplicationHandlerStop()


async def alliance_kick_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه عضو اخراج کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    members = [m for m in get_alliance_members_players(alliance_id) if m["user_id"] != user_id]
    if not members:
        await query.edit_message_text(
            "⚠️ هیچ عضوی (به‌جز خودت) توی اتحاد نیست.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    rows = []
    for m in members:
        info = get_country_info(m.get("country"))
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"alli_kickpick_{m['country']}")])
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")])
    await query.edit_message_text(
        "➖ *اخراج عضو از اتحاد*\n\nیه کشور رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_kick_pick(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه عضو اخراج کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    code = query.data.replace("alli_kickpick_", "")
    target = get_player_by_country(code)
    info = get_country_info(code)
    if not target or not info:
        await query.answer("❌ این کشور پیدا نشد.", show_alert=True)
        raise ApplicationHandlerStop()

    set_player_alliance(target["user_id"], None)
    await query.edit_message_text(
        f"✅ {info['flag']} {info['name']} از اتحاد *{alliance['name']}* اخراج شد.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🤝 منوی اتحاد", callback_data="alliance_menu")]]),
        parse_mode="Markdown"
    )
    try:
        await context.bot.send_message(
            target["user_id"],
            f"🚫 *از اتحاد اخراج شدی!*\nلیدر اتحاد *{alliance['name']}* تو رو از اتحاد اخراج کرد.",
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Notify kicked member error: {e}")
    raise ApplicationHandlerStop()


async def alliance_disband_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه منحلش کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    await query.edit_message_text(
        f"⚠️ *مطمئنی می‌خوای اتحاد «{alliance['name']}» رو منحل کنی؟*\nاین کار غیرقابل بازگشته.",
        reply_markup=InlineKeyboardMarkup([
            [InlineKeyboardButton("✅ بله، منحل کن", callback_data="alli_disband_yes"),
             InlineKeyboardButton("❌ نه", callback_data="alliance_menu")]
        ]),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_disband_yes(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه منحلش کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    members = get_alliance_members_players(alliance_id)
    delete_alliance_db(alliance_id)

    await query.edit_message_text(
        f"✅ اتحاد *{alliance['name']}* منحل شد.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    for m in members:
        if m["user_id"] == user_id:
            continue
        try:
            await context.bot.send_message(
                m["user_id"],
                f"🗑 اتحاد *{alliance['name']}* توسط لیدرش منحل شد.",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Notify disband member error: {e}")
    raise ApplicationHandlerStop()


async def alliance_attack_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)

    if not is_group_war_enabled():
        await query.edit_message_text(
            "🚫 *جنگ گروهی موقتاً توسط سازمان جهانی بسته شده!*\nبعداً دوباره امتحان کن فرمانده.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
            parse_mode="Markdown"
        )
        raise ApplicationHandlerStop()

    alliance_id = p.get("alliance_id") if p else None
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه حمله گروهی رو شروع کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    last_attack = p.get("last_attack")
    if last_attack:
        elapsed = (datetime.now() - datetime.fromisoformat(last_attack)).total_seconds()
        cooldown = get_group_war_cooldown_seconds()
        if elapsed < cooldown:
            remain = int(cooldown - elapsed)
            await query.edit_message_text(
                f"⏳ فرمانده، ارتشت هنوز نفس‌نفس می‌زنه!\nباید {remain // 60} دقیقه و {remain % 60} ثانیه دیگه صبر کنی.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
                parse_mode="Markdown"
            )
            raise ApplicationHandlerStop()

    member_countries = {m.get("country") for m in get_alliance_members_players(alliance_id)}
    active = get_all_active_countries()
    rows = []
    protected_count = 0
    for code in active:
        if code in member_countries:
            continue
        defender = get_player_by_country(code)
        if get_protection_remaining(defender) is not None:
            protected_count += 1
            continue
        info = get_country_info(code)
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"alli_atk_target_{code}")])

    if not rows:
        msg = "❌ هیچ کشور دیگه‌ای برای حمله گروهی وجود نداره."
        if protected_count:
            msg += f"\n🛡 {protected_count} کشور تازه‌ساز هنوز توی دوره‌ی محافظته."
        await query.edit_message_text(
            msg, reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    rows.append([InlineKeyboardButton("❌ لغو", callback_data="alliance_menu")])
    await query.edit_message_text(
        f"⚔️ *حمله گروهی اتحاد*\n━━━━━━━━━━━━━━━━━━━━\n🎯 کشور هدف رو انتخاب کن:",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_attack_target(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    target_country = query.data.replace("alli_atk_target_", "")
    context.user_data["alli_attack_target"] = target_country

    target_info = get_country_info(target_country)
    rows = [
        [InlineKeyboardButton("25٪", callback_data="alli_atk_pct_25"),
         InlineKeyboardButton("50٪", callback_data="alli_atk_pct_50")],
        [InlineKeyboardButton("75٪", callback_data="alli_atk_pct_75"),
         InlineKeyboardButton("💯 100٪ (همه‌ی تجهیزات اتحاد)", callback_data="alli_atk_pct_100")],
        [InlineKeyboardButton("✏️ تایپ درصد دلخواه", callback_data="alli_atk_pct_custom")],
        [InlineKeyboardButton("🔙 لغو", callback_data="alliance_menu")],
    ]
    await query.edit_message_text(
        f"🎯 هدف: {target_info['flag']} *{target_info['name']}*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"📦 چند درصد از تجهیزات هر عضو اتحاد وارد این حمله بشه؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )
    raise ApplicationHandlerStop()


async def alliance_attack_percent_button(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    percent = int(query.data.replace("alli_atk_pct_", ""))
    context.user_data["alli_attack_percent"] = percent
    await alliance_attack_preview(update, context)
    raise ApplicationHandlerStop()


async def alliance_attack_percent_custom_prompt(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    context.user_data["awaiting_alliance_percent"] = True
    await query.edit_message_text(
        "✏️ یه عدد بین ۱ تا ۱۰۰ بفرست:",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="alliance_menu")]])
    )
    raise ApplicationHandlerStop()


async def alliance_attack_preview(update: Update, context: ContextTypes.DEFAULT_TYPE, from_message=False):
    user_id = update.effective_user.id
    p = get_player_by_id_full(user_id)
    alliance_id = p.get("alliance_id")
    alliance = get_alliance(alliance_id)
    target_country = context.user_data.get("alli_attack_target")
    percent = context.user_data.get("alli_attack_percent", 100)

    target = get_player_by_country(target_country)
    target_info = get_country_info(target_country)
    if not target or not target_info or not alliance:
        text = "❌ خطا. دوباره از منوی اتحاد امتحان کن."
        kb = InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        if from_message:
            await update.message.reply_text(text, reply_markup=kb)
        else:
            await update.callback_query.edit_message_text(text, reply_markup=kb)
        return

    members = get_alliance_members_players(alliance_id)
    total_atk = sum(calc_attack_power(m, percent) for m in members)
    def_power = calc_defense_power(target)

    if total_atk <= 0:
        text = "❌ با این درصد، قدرت حمله‌ی اتحادت صفره! درصد بیشتری انتخاب کن."
        kb = InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        if from_message:
            await update.message.reply_text(text, reply_markup=kb)
        else:
            await update.callback_query.edit_message_text(text, reply_markup=kb)
        return

    predicted_percent = max(5, min(95, (total_atk / (total_atk + def_power)) * 100))
    if predicted_percent >= ATTACK_ANNIHILATE_THRESHOLD:
        conquest_warning = "\n\n☢️ *در صورت آسیب ۸۵٪+، کشور حریف کاملاً نابود و حذف می‌شه!*"
    elif predicted_percent >= ATTACK_CONQUER_THRESHOLD:
        conquest_warning = "\n\n🏴 *در صورت آسیب ۷۰٪+، کشور حریف کاملاً فتح می‌شه!*"
    else:
        conquest_warning = ""

    rows = [[InlineKeyboardButton("💥 شروع حمله گروهی", callback_data="alli_atk_confirm"),
             InlineKeyboardButton("🔙 لغو", callback_data="alliance_menu")]]
    text = (
        f"🎯 *پیش‌نمایش حمله گروهی اتحاد*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🤝 اتحاد: *{alliance['name']}* ({len(members)} عضو) ⚔️ {target_info['flag']} {target_info['name']}\n\n"
        f"📦 درصد تجهیزات هر عضو: `{percent}٪`\n"
        f"💪 قدرت حمله‌ی ترکیبی اتحاد: `{fmt(total_atk)}`\n"
        f"🛡 قدرت دفاع حریف: `{fmt(def_power)}`\n\n"
        f"📊 درصد آسیب تخمینی: `{round(predicted_percent)}٪`{conquest_warning}\n\n"
        f"مطمئنی می‌خوای حمله گروهی رو شروع کنی؟"
    )
    if from_message:
        await update.message.reply_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")
    else:
        await update.callback_query.edit_message_text(text, reply_markup=InlineKeyboardMarkup(rows), parse_mode="Markdown")


async def alliance_attack_confirm(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id
    await query.answer()
    p = get_player_by_id_full(user_id)

    if not is_group_war_enabled():
        await query.edit_message_text(
            "🚫 *جنگ گروهی موقتاً توسط سازمان جهانی بسته شده!*",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
            parse_mode="Markdown"
        )
        raise ApplicationHandlerStop()

    alliance_id = p.get("alliance_id")
    alliance = get_alliance(alliance_id)
    if not alliance or alliance["leader_user_id"] != user_id:
        await query.answer("❌ فقط لیدر اتحاد می‌تونه حمله گروهی رو اجرا کنه.", show_alert=True)
        raise ApplicationHandlerStop()

    target_country = context.user_data.get("alli_attack_target")
    percent = context.user_data.get("alli_attack_percent", 100)

    if not target_country:
        await query.edit_message_text(
            "❌ هدف حمله مشخص نیست. دوباره از منوی اتحاد شروع کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    result = resolve_group_attack(alliance_id, p["country"], target_country, percent)

    if result.get("error") == "no_equipment":
        await query.edit_message_text(
            "❌ اتحادت هیچ تجهیزات حمله‌ای نداره!",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()
    if result.get("error") == "protected":
        await query.edit_message_text(
            "🛡 این کشور تازه‌ساز و توی دوره‌ی محافظته.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()
    if result.get("error"):
        await query.edit_message_text(
            "❌ خطا در اجرای حمله گروهی.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
        )
        raise ApplicationHandlerStop()

    update_player(user_id, {"last_attack": datetime.now().isoformat()})
    context.user_data.pop("alli_attack_target", None)
    context.user_data.pop("alli_attack_percent", None)

    attacker_info = get_country_info(result["attacker_country"])
    defender_info = get_country_info(result["defender_country"])

    if result["annihilated"]:
        winner_line = f"🏆 *برنده: اتحاد* — کشور {defender_info['flag']} {defender_info['name']} کاملاً نابود و حذف شد!"
        outcome_line = "☢️ این کشور الان آزاده و کس دیگه‌ای می‌تونه انتخابش کنه."
    elif result["conquered"]:
        winner_line = f"🏆 *برنده: اتحاد* — کشور {defender_info['flag']} {defender_info['name']} فتح شد!"
        outcome_line = f"🏴 *کشور {defender_info['flag']} {defender_info['name']} کاملاً فتح شد!*"
    else:
        winner_line = f"🛡 *برنده: دفاع* — {defender_info['flag']} {defender_info['name']} در برابر حمله‌ی اتحاد مقاومت کرد."
        outcome_line = f"💥 *{round(result['damage_percent'])}٪ آسیب* به کشور حریف وارد شد."

    result_text = (
        f"⚔️ *گزارش حمله گروهی اتحاد*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🤝 اتحاد حمله‌کننده: *{alliance['name']}*\n"
        f"🗡 لیدر عملیات: {attacker_info['flag']} *{attacker_info['name']}*\n"
        f"🛡 دفاع‌کننده: {defender_info['flag']} *{defender_info['name']}*\n\n"
        f"{winner_line}\n\n"
        f"📦 درصد تجهیزات استفاده‌شده: `{result['percent_used']}٪`\n"
        f"💪 قدرت حمله ترکیبی: `{fmt(result['atk_power'])}`\n"
        f"🛡 قدرت دفاع: `{fmt(result['def_power'])}`\n"
        f"📊 درصد آسیب: `{result['damage_percent']}٪`\n\n"
        f"{outcome_line}\n"
        f"💸 غرامت دریافتی (به بودجه‌ی لیدر): `{fmt(result['transferred'])}`\n"
        f"━━━━━━━━━━━━━━━━━━━━"
    )

    await query.edit_message_text(
        result_text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )

    try:
        await context.bot.send_message(GROUP_1_ID, result_text, parse_mode="Markdown")
    except Exception as e:
        logger.error(f"Error announcing group attack result: {e}")

    defender_player = get_player_by_country(result["defender_country"])
    if defender_player and defender_player["user_id"] != user_id:
        try:
            await context.bot.send_message(
                defender_player["user_id"],
                f"🚨 *کشورت مورد حمله‌ی یه اتحاد قرار گرفت!*\n\n{result_text}",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Error notifying defender (group attack): {e}")

    raise ApplicationHandlerStop()


async def alliance_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):


    user_id = update.effective_user.id

    if context.user_data.get("awaiting_alliance_name"):
        p = get_player_by_id_full(user_id)
        name = (update.message.text or "").strip()
        context.user_data["awaiting_alliance_name"] = None

        if not (2 <= len(name) <= 40):
            context.user_data["awaiting_alliance_name"] = True
            await update.message.reply_text("❌ اسم باید بین ۲ تا ۴۰ کاراکتر باشه. دوباره بفرست:")
            raise ApplicationHandlerStop()

        if p.get("alliance_id"):
            await update.message.reply_text(
                "❌ تو قبلاً عضو یه اتحادی هستی.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
            )
            raise ApplicationHandlerStop()

        if count_alliances() >= MAX_ALLIANCES:
            await update.message.reply_text(
                f"❌ ظرفیت اتحادها پره! حداکثر {MAX_ALLIANCES} اتحاد مجازه.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]])
            )
            raise ApplicationHandlerStop()

        if (p.get("budget", 0) or 0) < ALLIANCE_CREATE_COST:
            await update.message.reply_text(
                f"❌ بودجه‌ات کافی نیست! هزینه: `{fmt(ALLIANCE_CREATE_COST)}`",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="alliance_menu")]]),
                parse_mode="Markdown"
            )
            raise ApplicationHandlerStop()

        new_budget = (p.get("budget", 0) or 0) - ALLIANCE_CREATE_COST
        update_player(user_id, {"budget": new_budget})
        alliance_id = create_alliance_db(name, user_id, p["country"])

        await update.message.reply_text(
            f"✅ اتحاد *{name}* ساخته شد! تو لیدرشی 👑\n🏦 بودجه‌ی باقی‌مونده: `{fmt(new_budget)}`",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🤝 منوی اتحاد", callback_data="alliance_menu")]]),
            parse_mode="Markdown"
        )
        try:
            await context.bot.send_message(
                GROUP_1_ID,
                f"🤝 *اتحاد جدید تشکیل شد!*\n«{name}» با لیدری {get_country_info(p['country'])['flag']} {get_country_info(p['country'])['name']} پا به میدون گذاشت.",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Announce new alliance error: {e}")
        raise ApplicationHandlerStop()

    if context.user_data.get("awaiting_alliance_percent"):
        text = (update.message.text or "").strip().replace("٪", "").replace("%", "")
        if not text.isdigit() or not (1 <= int(text) <= 100):
            await update.message.reply_text(
                "❌ عدد نامعتبره. یه عدد بین ۱ تا ۱۰۰ بفرست.",
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="alliance_menu")]])
            )
            raise ApplicationHandlerStop()
        
        context.user_data["awaiting_alliance_percent"] = None
        context.user_data["alli_attack_percent"] = int(text)
        await alliance_attack_preview(update, context, from_message=True)
        raise ApplicationHandlerStop()

    return  
    
async def alliance_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):
   
    user_id = update.effective_user.id

    if context.user_data.get("awaiting_nuke_reason"):
        return await nuke_text_input(update, context)

    if context.user_data.get("awaiting_alliance_name"):

        pass

def nuke_main_keyboard(my_country):
    active = get_all_active_countries()
    rows = []
    for code in active:
        if code == my_country:
            continue
        info = get_country_info(code)
        if info:
            rows.append([InlineKeyboardButton(f"{info['flag']} {info['name']}", callback_data=f"nuke_target_{code}")])
    rows.append([InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")])
    return InlineKeyboardMarkup(rows)


async def nuke_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    if not p or not p.get("country"):
        await query.edit_message_text("❌ اول باید یه کشور انتخاب کنی.")
        return

    have = p.get("atom_bomb", 0) or 0
    if have < NUKE_HALF_DAMAGE_COUNT:
        await query.edit_message_text(
            f"❌ برای حمله‌ی اتمی حداقل {NUKE_HALF_DAMAGE_COUNT} بمب اتم لازم داری.\n"
            f"تعداد فعلی‌ات: {have}\n"
            f"از بازار تسلیحات (دسته‌ی موشک 🚀) خریداری کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return

    await query.edit_message_text(
        f"☢️ *سامانه حمله اتمی*\n━━━━━━━━━━━━━━━━━━━━\n"
        f"💣 تعداد بمب اتم شما: `{have}`\n\n"
        f"🎯 کشور هدف رو انتخاب کن:",
        reply_markup=nuke_main_keyboard(p["country"]),
        parse_mode="Markdown"
    )


async def nuke_select_target(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    target_country = query.data.replace("nuke_target_", "")
    context.user_data["nuke_target"] = target_country

    target_info = get_country_info(target_country)
    rows = [
        [InlineKeyboardButton(f"☢️ {NUKE_HALF_DAMAGE_COUNT} اتم (۵۰٪ آسیب)", callback_data=f"nuke_count_{NUKE_HALF_DAMAGE_COUNT}")],
        [InlineKeyboardButton(f"☢️☢️ {NUKE_FULL_DESTROY_COUNT} اتم (نابودی کامل)", callback_data=f"nuke_count_{NUKE_FULL_DESTROY_COUNT}")],
        [InlineKeyboardButton("🔙 لغو", callback_data="main_menu")],
    ]
    await query.edit_message_text(
        f"🎯 هدف: {target_info['flag']} *{target_info['name']}*\n\n"
        f"چند اتم می‌خوای پرتاب کنی؟",
        reply_markup=InlineKeyboardMarkup(rows),
        parse_mode="Markdown"
    )


async def nuke_select_count(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    user_id = query.from_user.id
    p = get_player_by_id_full(user_id)

    nuke_count = int(query.data.replace("nuke_count_", ""))
    have = p.get("atom_bomb", 0) or 0
    if have < nuke_count:
        await query.edit_message_text(
            f"❌ تعداد بمب اتمت کافی نیست! ({have} عدد داری)",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        return

    context.user_data["nuke_count"] = nuke_count
    context.user_data["awaiting_nuke_reason"] = True

    await query.edit_message_text(
        "✍️ *دلیل حمله‌ی اتمی رو بنویس:*\n"
        "_(این دلیل برای بررسی ادمین ارسال می‌شه)_",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 لغو", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )


async def nuke_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE):

    if not context.user_data.get("awaiting_nuke_reason"):
        return  

    user_id = update.effective_user.id
    p = get_player_by_id_full(user_id)
    reason = (update.message.text or "").strip()

    if not (3 <= len(reason) <= 500):
        await update.message.reply_text("❌ دلیل باید بین ۳ تا ۵۰۰ کاراکتر باشه. دوباره بفرست:")
        raise ApplicationHandlerStop()

    context.user_data["awaiting_nuke_reason"] = None
    target_country = context.user_data.get("nuke_target")
    nuke_count = context.user_data.get("nuke_count")

    if not target_country or not nuke_count:
        await update.message.reply_text(
            "❌ اطلاعات حمله ناقصه. دوباره از منو شروع کن.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        raise ApplicationHandlerStop()

    have = p.get("atom_bomb", 0) or 0
    if have < nuke_count:
        await update.message.reply_text(
            f"❌ تعداد بمب اتمت کافی نیست! ({have} عدد داری)",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]])
        )
        raise ApplicationHandlerStop()

    conn = sqlite3.connect("game.db")
    c = conn.cursor()
    c.execute(
        "INSERT INTO nuke_requests (attacker_country, defender_country, attacker_user_id, nuke_count, reason) VALUES (?,?,?,?,?)",
        (p["country"], target_country, user_id, nuke_count, reason)
    )
    request_id = c.lastrowid
    conn.commit()
    conn.close()

    attacker_info = get_country_info(p["country"])
    target_info = get_country_info(target_country)

    try:
        await context.bot.send_message(
            ADMIN_ID,
            f"☢️ *درخواست حمله اتمی جدید*\n"
            f"━━━━━━━━━━━━━━━━━━━━\n"
            f"🗡 حمله‌کننده: {attacker_info['flag']} *{attacker_info['name']}*\n"
            f"🎯 هدف: {target_info['flag']} *{target_info['name']}*\n"
            f"💣 تعداد اتم: `{nuke_count}`\n"
            f"🆔 شناسه: `{request_id}`\n\n"
            f"📝 *دلیل حمله:*\n{reason}\n"
            f"━━━━━━━━━━━━━━━━━━━━",
            reply_markup=InlineKeyboardMarkup([
                [InlineKeyboardButton("✅ تایید و اجرا", callback_data=f"adm_nuke_ok_{request_id}"),
                 InlineKeyboardButton("❌ رد کردن", callback_data=f"adm_nuke_no_{request_id}")]
            ]),
            parse_mode="Markdown"
        )
    except Exception as e:
        logger.error(f"Error sending nuke request to admin: {e}")

    await update.message.reply_text(
        "☢️ *درخواست حمله‌ی اتمی ارسال شد!*\n"
        "⏳ منتظر تایید ادمین باش. تا اون موقع هیچ اتفاقی نمی‌افته.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 مرکز فرماندهی", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    context.user_data.pop("nuke_target", None)
    context.user_data.pop("nuke_count", None)
    raise ApplicationHandlerStop()


async def admin_nuke_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user_id = query.from_user.id

    if user_id != ADMIN_ID:
        await query.answer("❌ فقط ادمین میتونه این کارو بکنه!", show_alert=True)
        return

    await query.answer("⏳ در حال پردازش...")

    is_approve = query.data.startswith("adm_nuke_ok_")
    request_id = int(query.data.replace("adm_nuke_ok_" if is_approve else "adm_nuke_no_", ""))

    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM nuke_requests WHERE id=? AND status='pending'", (request_id,))
    row = c.fetchone()
    if not row:
        conn.close()
        try:
            await query.edit_message_text("⚠️ این درخواست قبلاً پردازش شده یا وجود نداره.")
        except:
            pass
        return

    row = dict(row)
    new_status = "approved" if is_approve else "rejected"
    c.execute("UPDATE nuke_requests SET status=? WHERE id=?", (new_status, request_id))
    conn.commit()
    conn.close()

    attacker_info = get_country_info(row["attacker_country"])
    defender_info = get_country_info(row["defender_country"])
    attacker_player = get_player_by_country(row["attacker_country"])
    defender_player = get_player_by_country(row["defender_country"])

    if not is_approve:
        if attacker_player:
            try:
                await context.bot.send_message(
                    attacker_player["user_id"],
                    f"❌ *درخواست حمله‌ی اتمی‌ات رد شد!*\n"
                    f"🎯 هدف: {defender_info['flag']} {defender_info['name']}\n"
                    f"ادمین این حمله رو تایید نکرد.",
                    parse_mode="Markdown"
                )
            except Exception as e:
                logger.error(f"Notify nuke reject error: {e}")
        try:
            await query.edit_message_text(
                f"❌ *درخواست حمله اتمی رد شد*\n🆔 شناسه: `{request_id}`",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Edit admin nuke reject msg error: {e}")
        return


    result = resolve_nuke_attack(row["attacker_country"], row["defender_country"], row["nuke_count"])

    if result.get("error"):
        try:
            await query.edit_message_text(f"⚠️ خطا در اجرای حمله: {result['error']}")
        except:
            pass
        return

    if result["annihilated"]:
        outcome_line = f"☢️ کشور {defender_info['flag']} {defender_info['name']} کاملاً نابود و حذف شد!"
    else:
        outcome_line = f"💥 {round(result['damage_percent'])}٪ از کشور {defender_info['flag']} {defender_info['name']} نابود شد."

    result_text = (
        f"☢️ *گزارش حمله اتمی*\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"🗡 حمله‌کننده: {attacker_info['flag']} *{attacker_info['name']}*\n"
        f"🎯 هدف: {defender_info['flag']} *{defender_info['name']}*\n"
        f"💣 تعداد اتم استفاده‌شده: `{result['nuke_count']}`\n\n"
        f"📝 *دلیل اعلام‌شده:*\n{row['reason']}\n\n"
        f"{outcome_line}\n"
        f"💸 غرامت دریافتی: `{fmt(result['transferred'])}`\n"
        f"━━━━━━━━━━━━━━━━━━━━\n"
        f"_این حمله توسط ادمین تایید و اجرا شده است_ ✅"
    )

    try:
        await query.edit_message_text(f"✅ *حمله اتمی تایید و اجرا شد*\n🆔 شناسه: `{request_id}`", parse_mode="Markdown")
    except Exception as e:
        logger.error(f"Edit admin nuke approve msg error: {e}")

    if attacker_player:
        try:
            await context.bot.send_message(attacker_player["user_id"], result_text, parse_mode="Markdown")
        except Exception as e:
            logger.error(f"Notify nuke attacker error: {e}")

    if defender_player:
        try:
            await context.bot.send_message(
                defender_player["user_id"],
                f"🚨 *کشورت هدف حمله‌ی اتمی قرار گرفت!*\n\n{result_text}",
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Notify nuke defender error: {e}")

    try:
        await context.bot.send_message(GROUP_1_ID, result_text, parse_mode="Markdown")
    except Exception as e:
        logger.error(f"Announce nuke attack error: {e}")


async def show_rules(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    
    text = """⚔️ *قوانین جنگ جهانی*

🗓 *روزهای انتحاری:* یکشنبه، سه‌شنبه، پنج‌شنبه
⏰ *زمان انتحاری:* ۱۲ تا ۱۹

🚫 *مجازات‌ها:*
پهپاد انتحاری، نقطه‌زن، شناسایی، جاسوس، بمب‌گذار
بعد از پیدا شدن رهبر = مجوز حمله با موشک/پهپاد/جنگنده

⚓ *دزدیدن ناو:*
تا ۵۰ کیلومتر نزدیک شوید + قایق/زیرسطحی + هک نظامی
اگه دفاعش کم باشه = دزدی موفق

🏴‍☠️ *دزدان دریایی:*
روزی یک‌بار می‌توانند هنگام صادرات به ناوها حمله کنند

⛔ *بستن تنگه هرمز:*
بدون دلیل = ۳۰٪ تحریم | در زمان جنگ = مجاز

☢️ *بمب اتم:*
نیاز به مجوز سازمان ملل + دلیل محکم
کشور هدف کاملاً نابود می‌شود

🛡️ *مرزبانی:*
داشتن مرزبان اجباری است

⏰ *زمان‌بندی:*
خرید تجهیزات: ۱۲ ظهر تا ۱۲:۱۵ شب
جنگ اصلی: ۱۲ تا ۲۰"""
    
    await query.edit_message_text(
        text,
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 برگشت", callback_data="main_menu")]]),
        parse_mode="Markdown"
    )
    return MAIN_MENU


async def midnight_income(context: ContextTypes.DEFAULT_TYPE):
    conn = sqlite3.connect("game.db")
    conn.row_factory = sqlite3.Row
    c = conn.cursor()
    c.execute("SELECT * FROM players")
    players = [dict(r) for r in c.fetchall()]
    conn.close()

    for p in players:
        total_income = p.get("daily_income", 70000000) + p.get("oil_income", 0)
        new_budget = p["budget"] + total_income
        oil_add = p.get("oil_income", 0)  
        upd = {"budget": new_budget}
        if oil_add > 0:
            upd["oil_reserves"] = (p.get("oil_reserves", 0) or 0) + oil_add
        update_player(p["user_id"], upd)

        
        conn2 = sqlite3.connect("game.db")
        c2 = conn2.cursor()
        c2.execute("SELECT company_key FROM companies WHERE owner_user_id=?", (p["user_id"],))
        companies = [r[0] for r in c2.fetchall()]
        conn2.close()

        co_income = sum(COMPANIES[ck]["income"] for ck in companies if ck in COMPANIES)
        if co_income > 0:
            p2 = get_player_by_id_full(p["user_id"])
            update_player(p["user_id"], {"budget": p2["budget"] + co_income})

            for ck in companies:
                co = COMPANIES.get(ck)
                if co and co.get("daily_produce"):
                    p3 = get_player_by_id_full(p["user_id"])
                    prod_updates = {}
                    for prod_key, prod_qty in co["daily_produce"].items():
                        curr = p3.get(prod_key, 0) or 0
                        prod_updates[prod_key] = curr + prod_qty
                    if prod_updates:
                        update_player(p["user_id"], prod_updates)

        try:
            info = get_country_info(p.get("country", ""))
            p_final = get_player_by_id_full(p["user_id"])
            mine_income = 0
            for mine_key in ["diamond_mine", "gold_mine", "silver_mine"]:
                cnt = p.get(mine_key, 0) or 0
                inc = SHOP_ITEMS["mine"]["items"].get(mine_key, {}).get("daily_income", 0)
                mine_income += cnt * inc

            lines = [
                f"🌙 *گزارش مالی شبانه*",
                f"{'━'*20}",
                f"{info['flag']} *{info['name']}*",
                f"",
                f"💰 درآمد روزانه: +`{fmt(p.get('daily_income',70000000))}`",
            ]
            if p.get("oil_income", 0) > 0:
                lines.append(f"🛢️ درآمد نفتی: +`{fmt(p.get('oil_income',0))}`")
                lines.append(f"🛢️ ذخایر نفت جدید: `{fmt(p_final.get('oil_reserves',0))}`")
            if co_income > 0:
                lines.append(f"🏢 درآمد شرکت‌ها: +`{fmt(co_income)}`")
            if mine_income > 0:
                lines.append(f"⛏️ درآمد معادن: +`{fmt(mine_income)}`")
            lines += [
                f"{'─'*18}",
                f"🏦 بودجه جدید: `{fmt(p_final.get('budget',0))}`",
                f"{'━'*20}",
                f"",
                f"🌅 صبح بخیر فرمانده! آماده جنگ باش ⚔️",
            ]
            await context.bot.send_message(
                p["user_id"],
                "\n".join(lines),
                parse_mode="Markdown"
            )
        except Exception as e:
            logger.error(f"Error sending midnight income to {p['user_id']}: {e}")



def main():
    init_db()
    
    app = Application.builder().token(TOKEN).build()
    
   
    app.job_queue.run_daily(midnight_income, time=time(hour=0, minute=0))
    
    conv_handler = ConversationHandler(
        entry_points=[CommandHandler("start", start)],
        states={
            SELECT_COUNTRY: [
                CallbackQueryHandler(pick_country_menu, pattern="^pick_country$"),
                CallbackQueryHandler(pick_group_menu, pattern="^pick_group$"),
                CallbackQueryHandler(select_country, pattern="^sel_country_"),
                CallbackQueryHandler(back_start, pattern="^back_start$"),
            ],
            MAIN_MENU: [
                CallbackQueryHandler(main_menu_handler),
            ],
            SHOP_MENU: [
                CallbackQueryHandler(show_shop, pattern="^shop$"),
                CallbackQueryHandler(shop_category, pattern="^shop_cat_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
                CallbackQueryHandler(view_cart, pattern="^view_cart$"),
                CallbackQueryHandler(checkout, pattern="^checkout$"),
                CallbackQueryHandler(clear_cart, pattern="^clear_cart$"),
                CallbackQueryHandler(remove_cart_item, pattern="^remove_item_"),
            ],
            SHOP_CATEGORY: [
                CallbackQueryHandler(show_shop, pattern="^shop$"),
                CallbackQueryHandler(shop_category, pattern="^shop_cat_"),
                CallbackQueryHandler(shop_item, pattern="^shop_item_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
                CallbackQueryHandler(remove_cart_item, pattern="^remove_item_"),
            ],
            SHOP_ITEM: [
                CallbackQueryHandler(add_to_cart, pattern="^add_"),
                CallbackQueryHandler(shop_qty_custom_prompt, pattern="^shop_qty_custom$"),
                CallbackQueryHandler(view_cart, pattern="^view_cart$"),
                CallbackQueryHandler(shop_category, pattern="^shop_cat_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
                CallbackQueryHandler(remove_cart_item, pattern="^remove_item_"),
            ],
            SHOP_QUANTITY: [
                MessageHandler(filters.TEXT & ~filters.COMMAND, shop_qty_text_input),
                CallbackQueryHandler(shop_item, pattern="^shop_item_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            COMPANY_MENU: [
                CallbackQueryHandler(show_companies, pattern="^companies$"),
                CallbackQueryHandler(company_detail, pattern="^co_detail_"),
                CallbackQueryHandler(buy_company, pattern="^buy_co_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            TRADE_SELECT_ITEM: [
                CallbackQueryHandler(trade_item_selected, pattern="^trade_item_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            TRADE_QUANTITY: [
                MessageHandler(filters.TEXT & ~filters.COMMAND, trade_quantity_input),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            TRADE_PRICE: [
                MessageHandler(filters.TEXT & ~filters.COMMAND, trade_quantity_input),
                CallbackQueryHandler(trade_free, pattern="^trade_free$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            TRADE_CONFIRM: [
                CallbackQueryHandler(trade_confirm_handler, pattern="^trade_confirm$"),
                CallbackQueryHandler(trade_edit, pattern="^trade_edit$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            TRADE_SELECT_COUNTRY: [
                CallbackQueryHandler(trade_to_country, pattern="^trade_to_"),
                CallbackQueryHandler(trade_send, pattern="^trade_send$"),
                CallbackQueryHandler(trade_confirm_handler, pattern="^trade_confirm$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            DECLARATION_TEXT: [
                MessageHandler(filters.TEXT & ~filters.COMMAND, declaration_text_input),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            DECLARATION_CONFIRM: [
                CallbackQueryHandler(declaration_submit, pattern="^decl_submit$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            ATTACK_SELECT_TARGET: [
                CallbackQueryHandler(attack_select_target, pattern="^attack_target_"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            ATTACK_PERCENT: [
                CallbackQueryHandler(attack_percent_custom_prompt, pattern="^attack_pct_custom$"),
                CallbackQueryHandler(attack_percent_button, pattern="^attack_pct_\\d+$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            ATTACK_PERCENT_TEXT: [
                MessageHandler(filters.TEXT & ~filters.COMMAND, attack_percent_text_input),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
            ATTACK_CONFIRM: [
                CallbackQueryHandler(attack_confirm, pattern="^attack_confirm$"),
                CallbackQueryHandler(main_menu_handler, pattern="^main_menu$"),
            ],
        },
        fallbacks=[CommandHandler("start", start)],
        per_user=True,
        per_chat=True,
    )
    

    app.add_handler(CallbackQueryHandler(admin_decl_handler, pattern="^adm_decl_"), group=0)
    app.add_handler(CallbackQueryHandler(trade_accept, pattern="^trade_accept_"), group=0)
    app.add_handler(CallbackQueryHandler(trade_reject, pattern="^trade_reject_"), group=0)
    app.add_handler(CallbackQueryHandler(admin_manual_income, pattern="^adm_manual_income$"), group=0)
    app.add_handler(CallbackQueryHandler(shop_bundle_preview, pattern="^shop_bundle$"), group=0)
    app.add_handler(CallbackQueryHandler(shop_bundle_confirm, pattern="^shop_bundle_confirm$"), group=0)
    app.add_handler(CallbackQueryHandler(admin_delete_country_confirm, pattern="^adm_delconfirm\\|"), group=0)
    app.add_handler(CallbackQueryHandler(admin_alliance_delete_list, pattern="^adm_alliance_del_list$"), group=0)
    app.add_handler(CallbackQueryHandler(admin_alliance_delete_confirm, pattern="^adm_alliance_del_\\d+$"), group=0)
    
 
    app.add_handler(CallbackQueryHandler(admin_menu_callbacks, pattern="^admin_menu_callbacks$"), group=0)
    

    app.add_handler(CallbackQueryHandler(nuke_menu, pattern="^nuke_menu$"), group=0)
    app.add_handler(CallbackQueryHandler(nuke_select_target, pattern="^nuke_target_"), group=0)
    app.add_handler(CallbackQueryHandler(nuke_select_count, pattern="^nuke_count_\\d+$"), group=0)
    app.add_handler(CallbackQueryHandler(admin_nuke_handler, pattern="^adm_nuke_"), group=0)
    app.add_handler(CallbackQueryHandler(
        admin_menu_callbacks,
        pattern=r"^adm_(broadcast$|power_rank$|pick_(ma|ms|ea|es|wn|wr|dc)$|country\||cat\||item\|)"
    ), group=0)
    app.add_handler(CallbackQueryHandler(
        admin_settings_callbacks,
        pattern=r"^adm_(settings$|toggle_war$|toggle_group_war$|toggle_shop$|toggle_trade$|set_war_cd$|set_group_cd$|set_protect$)"
    ), group=0)
    
    app.add_handler(MessageHandler(
        filters.TEXT & ~filters.COMMAND & filters.User(user_id=ADMIN_ID),
        admin_text_input
    ), group=-1)
    app.add_handler(CommandHandler("admin", admin_panel), group=0)


    
    app.add_handler(CallbackQueryHandler(alliance_main_menu, pattern="^alliance_menu$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_create_prompt, pattern="^alli_create_prompt$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_join, pattern="^alli_join_\\d+$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_leave, pattern="^alli_leave$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_members_view, pattern="^alli_members$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_add_prompt, pattern="^alli_add_prompt$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_add_pick, pattern="^alli_addpick_"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_kick_prompt, pattern="^alli_kick_prompt$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_kick_pick, pattern="^alli_kickpick_"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_disband_confirm, pattern="^alli_disband_confirm$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_disband_yes, pattern="^alli_disband_yes$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_attack_menu, pattern="^alli_attack_menu$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_attack_target, pattern="^alli_atk_target_"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_attack_percent_custom_prompt, pattern="^alli_atk_pct_custom$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_attack_percent_button, pattern="^alli_atk_pct_\\d+$"), group=0)
    app.add_handler(CallbackQueryHandler(alliance_attack_confirm, pattern="^alli_atk_confirm$"), group=0)
    app.add_handler(MessageHandler(
        filters.TEXT & ~filters.COMMAND,
        alliance_text_input
    ), group=0)

    app.add_handler(conv_handler, group=1)
    
    print("🤖 بات در حال اجراست...")
    app.run_polling(drop_pending_updates=True)

if __name__ == "__main__":
    main()

