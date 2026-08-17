import sqlite3
import asyncio
import logging
import random
import io
from datetime import datetime, timedelta
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup, ReplyKeyboardMarkup, KeyboardButton, BotCommand
from telegram.ext import (
    Application, CommandHandler, MessageHandler,
    CallbackQueryHandler, filters, ContextTypes
)

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


def cbtn(
    text: str,
    callback_data: str = None,
    url: str = None,
    style: str = None,
    **kwargs
) -> InlineKeyboardButton:
    if style is None and callback_data is not None:
        cd = callback_data.lower()
        if any(x in cd for x in ["cancel"]):
            style = "danger"
        elif any(x in cd for x in [
            "buy", "collect", "interest", "sell", "upgrade", "rankup",
            "feed", "feeddog", "donate", "check_join", "confirm",
            "lottery_join", "lottery_draw", "produce", "factory_sell",
            "stray_", "bail", "bank_interest", "bank_deposit",
            "buyhook", "buydog",
        ]):
            style = "success"
        else:
            style = "primary"

    build_kwargs = {"text": text}
    if callback_data is not None:
        build_kwargs["callback_data"] = callback_data
    if url is not None:
        build_kwargs["url"] = url
    build_kwargs.update(kwargs)
    if style is not None:
        try:
            build_kwargs["style"] = style
        except TypeError:
            pass
    return InlineKeyboardButton(**build_kwargs)

BOT_TOKEN = "توکن بات"
BOT_USERNAME = "یوزرنیم بات"

REFERRAL_ENABLED       = True
REFERRAL_REWARD_SENDER = 5_000
REFERRAL_REWARD_JOINER = 1_000
ADMIN_IDS = [123456789]                              # لیست آیدی عددی مدیران

FORCE_JOIN_CHANNEL = "یوزرنیم کانال"
FORCE_JOIN_CHANNEL_LINK = "https://t.me/کانال"
FORCE_JOIN_CHANNEL_NAME = "اسم کانال"

HOP_COOLDOWN    = 300
HOP_BASE_POINTS = 50

CITY_LEVELS = {
    1:  (0,       0,    0,   0,   0),
    2:  (5_000,   50,   5,   10,  5),
    3:  (20_000,  150,  15,  30,  15),
    4:  (60_000,  400,  35,  80,  40),
    5:  (150_000, 900,  80,  200, 100),
    6:  (400_000, 2000, 180, 500, 250),
    7:  (1_000_000, 5000, 400, 1200, 600),
    8:  (3_000_000, 12000, 900, 3000, 1500),
    9:  (8_000_000, 30000, 2000, 8000, 4000),
    10: (20_000_000, 80000, 5000, 20000, 10000),
}
CITY_MAX_LEVEL = 10
CITY_HOP_BUFF        = 5
CITY_FISH_BUFF       = 30
CITY_STRAY_BUFF      = 0.05
HOP_LEVEL_BONUS = 0.20

LEVEL_THRESHOLDS = []
hops = 10
for i in range(1000):
    LEVEL_THRESHOLDS.append(hops)
    if i < 9:      hops += 20
    elif i < 49:   hops += 50
    elif i < 199:  hops += 100
    else:          hops += 200

DOG_MIN_LEVEL = 3
DOG_COST      = 1000
DOG_LEVEL_DATA = {
    1:  (0.10,   5_000,     1_000),
    2:  (0.20,   10_000,    2_400),
    3:  (0.35,   18_000,    5_000),
    4:  (0.55,   28_000,    9_000),
    5:  (0.80,   40_000,    16_000),
    6:  (1.10,   55_000,    26_000),
    7:  (1.50,   75_000,    40_000),
    8:  (2.00,   100_000,   60_000),
    9:  (2.60,   130_000,   90_000),
    10: (3.30,   170_000,   130_000),
    11: (4.10,   215_000,   180_000),
    12: (5.00,   265_000,   240_000),
    13: (6.00,   320_000,   316_000),
    14: (7.20,   380_000,   410_000),
    15: (8.50,   450_000,   520_000),
    16: (10.00,  530_000,   650_000),
    17: (11.80,  620_000,   800_000),
    18: (13.80,  720_000,   980_000),
    19: (16.00,  830_000,   1_180_000),
    20: (18.50,  950_000,   1_400_000),
}
DOG_MAX_LEVEL = 20
DOG_RANKS = {
    1:  ("سگ خیابونی 🐾",        1.0),
    2:  ("سگ تازه‌کار 🐶",        1.3),
    3:  ("سگ باتجربه 🐕",         1.6),
    4:  ("سگ آموزش‌دیده 🦮",      2.0),
    5:  ("سگ ماهر 🐕‍🦺",          2.5),
    6:  ("سگ نخبه 🌟🐕",          3.2),
    7:  ("سگ قهرمان 🏆🐕",        4.0),
    8:  ("سگ اسطوره 💎🐕",        5.0),
    9:  ("سگ افسانه‌ای 👑🐕",      6.5),
    10: ("سگ جاودان ✨👑🐕",      8.5),
}
BONE_COST     = 14_000
FEED_DURATION = 7200

FEED_BASE_BY_RARITY = {
    "⚪": 10 * 60,
    "🟢": 25 * 60,
    "🔵": 45 * 60,
    "🟣": 65 * 60,
    "🟠": 85 * 60,
    "🔴": 105 * 60,
    "🔥": 120 * 60,
}
FEED_WEIGHT_BONUS = 15 * 60


HOOK_MIN_LEVEL = 2
HOOK_DATA = {
    1:  (600,   3600,  0),
    2:  (0,     3300,  1_600),
    3:  (0,     2700,  3_600),
    4:  (0,     2400,  7_000),
    5:  (0,     2100,  12_000),
    6:  (0,     1800,  20_000),
    7:  (0,     1500,  32_000),
    8:  (0,     1200,  50_000),
    9:  (0,     1050,  76_000),
    10: (0,     900,   110_000),
    11: (0,     750,   160_000),
    12: (0,     600,   230_000),
    13: (0,     480,   320_000),
    14: (0,     360,   440_000),
    15: (0,     300,   600_000),
}
HOOK_MAX_LEVEL = 15
BONE_SELL_TIME = 120

CRISIS_TYPES = {
    "fire": {
        "name": "🔥 آتش‌سوزی",
        "desc": "بخشی از شهر آتش گرفته! باید سریع اقدام کنی.",
        "penalty": "کاهش ۲۰٪ خزانه و ضعف ۳۰ دقیقه کولداون هاپ",
        "treasury_cost": 0.15,
        "fix_cooldown": 1800,
        "penalty_type": "hop_cooldown",
        "penalty_value": 60,
        "reward": 8_000,
        "timeout": 600,
    },
    "blackout": {
        "name": "⚡ قطعی برق",
        "desc": "برق کل شهر قطع شده! کارخانه‌ها متوقف شدن.",
        "penalty": "توقف تولید کارخانه‌ها به مدت ۴۵ دقیقه",
        "treasury_cost": 0.10,
        "fix_cooldown": 2700,
        "penalty_type": "factory_freeze",
        "penalty_value": 2700,
        "reward": 6_000,
        "timeout": 600,
    },
    "pollution": {
        "name": "☣️ آلودگی شهر",
        "desc": "آلودگی شدید! سگ‌ها نمی‌تونن کار کنن.",
        "penalty": "توقف درآمد سگ‌ها به مدت ۱ ساعت",
        "treasury_cost": 0.08,
        "fix_cooldown": 3600,
        "penalty_type": "dog_freeze",
        "penalty_value": 3600,
        "reward": 5_000,
        "timeout": 600,
    },
    "factory_breakdown": {
        "name": "🏭 خرابی کارخانه‌ها",
        "desc": "ماشین‌آلات کارخانه‌ها خراب شدن!",
        "penalty": "۵۰٪ کاهش سرعت تولید برای ۱ ساعت",
        "treasury_cost": 0.12,
        "fix_cooldown": 3600,
        "penalty_type": "factory_slow",
        "penalty_value": 3600,
        "reward": 7_000,
        "timeout": 600,
    },
    "dog_disease": {
        "name": "🤒 بیماری سگ‌ها",
        "desc": "یه بیماری واگیر بین سگ‌های شهر پخش شده!",
        "penalty": "۳۰٪ کاهش درآمد سگ‌ها برای ۴۵ دقیقه",
        "treasury_cost": 0.06,
        "fix_cooldown": 2700,
        "penalty_type": "dog_slow",
        "penalty_value": 2700,
        "reward": 4_000,
        "timeout": 600,
    },
}

CRISIS_TRIGGER_CHANCE = 0.08
CRISIS_MIN_TREASURY   = 2_000

BONES_TABLE = [
    ("استخوان کوچیک 🦴",              0.1,   0.3,    1,    50, "⚪"),
    ("استخوان مرغ 🍗",                0.2,   0.5,    1,    80, "⚪"),
    ("استخوان گربه 🐱",               0.2,   0.6,    1,    110, "⚪"),
    ("استخوان سگ 🐩",                 0.3,   0.8,    1,    150, "⚪"),
    ("استخوان گاو 🐄",                0.4,   1.2,    1,    200, "⚪"),
    ("استخوان خوک 🐷",                0.5,   1.5,    1,    250, "⚪"),
    ("استخوان بز 🐐",                 0.5,   1.6,    1,    280, "⚪"),
    ("استخوان گوسفند 🐑",             0.6,   1.8,    2,    320, "⚪"),
    ("استخوان دنده 🦷",               0.7,   2.0,    2,    370, "⚪"),
    ("استخوان ران 🦵",                0.8,   2.3,    2,    430, "⚪"),
    ("استخوان آهو 🦌",                1.0,   2.8,    2,    500, "⚪"),
    ("استخوان گراز 🐗",               1.1,   3.0,    2,    560, "⚪"),
    ("استخوان اسب 🐴",                0.9,   2.5,    2,    520, "⚪"),
    ("استخوان خرگوش 🐇",              0.1,   0.3,    1,    70, "⚪"),
    ("استخوان روباه 🦊",              0.4,   1.0,    1,    170, "⚪"),

    ("استخوان خرس 🐻",               1.3,   3.5,    3,    750, "🟢"),
    ("استخوان پلنگ 🐆",              1.5,   4.0,    3,    950, "🟢"),
    ("استخوان کروکودیل 🐊",          1.8,   4.5,    3,    1_200, "🟢"),
    ("استخوان شیر 🦁",               2.0,   5.0,    3,    1_500, "🟢"),
    ("استخوان کرگدن 🦏",             2.2,   5.5,    3,    1_800, "🟢"),
    ("استخوان فیل 🐘",               2.5,   6.0,    4,    2_200, "🟢"),
    ("استخوان دایناسور 🦕",          2.8,   6.5,    4,    2_700, "🟢"),
    ("استخوان ماستادون 🦣",          3.2,   7.5,    4,    3_300, "🟢"),
    ("استخوان گوریل غول‌پیکر 🦍",    3.5,   8.0,    4,    4_000, "🟢"),
    ("استخوان هیپوپوتاموس 🦛",       2.8,   7.0,    4,    3_600, "🟢"),
    ("استخوان عقاب طلایی 🦅",        1.8,   4.8,    3,    1_600, "🟢"),

    ("استخوان نهنگ 🐋",              4.0,   9.0,    5,    5_000, "🔵"),
    ("استخوان کوسه 🦈",              4.5,   10.0,   5,    6_200, "🔵"),
    ("استخوان مارماهی غول‌آسا 🐍",   5.0,   11.0,   5,    7_500, "🔵"),
    ("استخوان کراکن 🐙",             5.5,   12.0,   5,    9_000, "🔵"),
    ("استخوان ماموت 🦣",             6.0,   13.0,   6,    11_000, "🔵"),
    ("استخوان گرگ عظیم 🐺",          6.5,   14.0,   6,    13_500, "🔵"),
    ("استخوان ببر دندان‌شمشیری 🐯",  7.0,   15.0,   6,    16_000, "🔵"),
    ("استخوان مگالودون 🦷",          7.5,   16.0,   6,    19_000, "🔵"),
    ("استخوان اژدمار دریایی 🌊",     6.8,   14.5,   6,    17_000, "🔵"),
    ("استخوان غول غار 🗿",           5.8,   12.5,   5,    8_200, "🔵"),

    ("استخوان غول 👾",               8.0,   17.0,   7,    23_000, "🟣"),
    ("استخوان سیمرغ 🦅",            9.0,   19.0,   7,    28_000, "🟣"),
    ("استخوان گریفین 🦁",            10.0,  21.0,   7,    34_000, "🟣"),
    ("استخوان هیدرا 🐲",             11.0,  23.0,   7,    41_000, "🟣"),
    ("استخوان اژدها 🐉",             12.0,  25.0,   8,    50_000, "🟣"),
    ("استخوان ققنوس 🔥",             13.5,  28.0,   8,    60_000, "🟣"),
    ("استخوان لویاتان 🌊",           15.0,  31.0,   8,    72_000, "🟣"),
    ("استخوان فضایی 🛸",             16.5,  34.0,   9,    86_000, "🟣"),
    ("استخوان موجود فضایی 👽",       18.0,  37.0,   9,    102_000, "🟣"),
    ("استخوان بهموت 🔱",             20.0,  40.0,   9,    120_000, "🟣"),
    ("استخوان اژدهای یخ 🧊",         14.0,  29.0,   8,    65_000, "🟣"),
    ("استخوان ققنوس تاریک 🖤",        17.0,  35.0,   9,    95_000, "🟣"),

    ("استخوان افسانه‌ای ✨",          23.0,  46.0,   10,   145_000, "🟠"),
    ("استخوان تایتان ⚙️",            26.0,  52.0,   10,   175_000, "🟠"),
    ("استخوان خدایان 👑",            30.0,  60.0,   10,   210_000, "🟠"),
    ("استخوان دیو باستانی 🏺",       34.0,  68.0,   11,   250_000, "🟠"),
    ("استخوان کیهانی 🌌",            38.0,  76.0,   11,   300_000, "🟠"),
    ("استخوان سیاهچاله 🕳️",          43.0,  86.0,   11,   360_000, "🟠"),
    ("استخوان اهریمن 😈",            48.0,  95.0,   12,   430_000, "🟠"),
    ("استخوان شیطان بزرگ 🩸",        54.0,  105.0,  12,   510_000, "🟠"),
    ("استخوان نمرود 🏛️",             60.0,  115.0,  12,   600_000, "🟠"),
    ("استخوان جن عظیم 🧿",           42.0,  82.0,   11,   340_000, "🟠"),
    ("استخوان ستاره مرده ⭐",         56.0,  108.0,  12,   570_000, "🟠"),

    ("استخوان فرشته 😇",             68.0,  128.0,  13,   700_000, "🔴"),
    ("استخوان سرافیم 🌠",            76.0,  144.0,  13,   850_000, "🔴"),
    ("استخوان آغازین ⚡️",            85.0,  160.0,  13,   1_020_000, "🔴"),
    ("استخوان ازلی 🌀",              95.0,  180.0,  14,   1_220_000, "🔴"),
    ("استخوان خالق 🌟",              108.0, 200.0,  14,   1_450_000, "🔴"),
    ("استخوان عرش 🕊️",              122.0, 225.0,  14,   1_720_000, "🔴"),
    ("استخوان آتش ازلی 🔥",          140.0, 260.0,  15,   2_100_000, "🔥"),
    ("استخوان فنا 💀",               162.0, 300.0,  15,   2_600_000, "🔥"),
    ("استخوان هستی ☀️",              188.0, 350.0,  15,   3_200_000, "🔥"),
    ("استخوان خدای خدایان 🌋",       220.0, 420.0,  15,   4_200_000, "🔥"),
    ("استخوان ملک‌الموت ☠️",          72.0,  136.0,  13,   790_000, "🔴"),
    ("استخوان نور ابدی 💫",           100.0, 190.0,  14,   1_350_000, "🔴"),
    ("استخوان اژدهای کیهانی 🌠",     250.0, 480.0,  15,   5_500_000, "🔥"),
]

RARITY_ICON_MAP = {
    "⚪": "عادی",
    "🟢": "غیرمعمول",
    "🔵": "نادر",
    "🟣": "حماسی",
    "🟠": "اسطوره‌ای",
    "🔴": "کمیاب",
    "🔥": "آتشی",
}

RARITY_WEIGHTS_BY_LEVEL = {
    1:  (100, 0,  0,  0,  0,  0,  0),
    2:  (90,  10, 0,  0,  0,  0,  0),
    3:  (75,  20, 5,  0,  0,  0,  0),
    4:  (65,  22, 10, 3,  0,  0,  0),
    5:  (55,  25, 13, 5,  2,  0,  0),
    6:  (48,  25, 15, 8,  4,  0,  0),
    7:  (40,  24, 18, 11, 5,  2,  0),
    8:  (35,  22, 18, 13, 7,  4,  1),
    9:  (30,  21, 18, 14, 9,  5,  3),
    10: (28,  20, 17, 14, 10, 7,  4),
    11: (25,  20, 16, 14, 11, 8,  6),
    12: (22,  19, 16, 14, 12, 10, 7),
    13: (20,  18, 15, 14, 13, 11, 9),
    14: (17,  17, 15, 14, 13, 13, 11),
    15: (15,  15, 14, 13, 13, 13, 17),
}
RARITY_ORDER = ["⚪", "🟢", "🔵", "🟣", "🟠", "🔴", "🔥"]

def get_bone_rarity(bone_name: str) -> str:
    for bone in BONES_TABLE:
        if bone[0] == bone_name:
            return bone[5]
    return "⚪"

def calc_feed_duration(bone_name: str, weight: float) -> int:
    rarity_icon = get_bone_rarity(bone_name)
    base = FEED_BASE_BY_RARITY.get(rarity_icon, FEED_BASE_BY_RARITY["⚪"])
    bonus = int(weight * FEED_WEIGHT_BONUS)
    return base + bonus

def catch_bone(hook_level: int):
    weights = RARITY_WEIGHTS_BY_LEVEL.get(hook_level, RARITY_WEIGHTS_BY_LEVEL[15])
    available_icons = []
    available_weights = []
    for i, icon in enumerate(RARITY_ORDER):
        if weights[i] == 0:
            continue
        has_bone = any(b[5] == icon and b[3] <= hook_level for b in BONES_TABLE)
        if has_bone:
            available_icons.append(icon)
            available_weights.append(weights[i])

    chosen_rarity = random.choices(available_icons, weights=available_weights, k=1)[0]

    pool = [b for b in BONES_TABLE if b[5] == chosen_rarity and b[3] <= hook_level]
    max_price = max(b[4] for b in pool)
    inner_weights = [max(1.0, (max_price / b[4]) ** 1.5) for b in pool]
    bone = random.choices(pool, weights=inner_weights, k=1)[0]

    name, w_min, w_max, _, base_price, rarity_icon = bone
    weight = round(random.uniform(w_min, w_max), 2)
    price  = int(base_price * (weight / w_min))
    rarity_name = RARITY_ICON_MAP.get(chosen_rarity, "")
    return name, weight, price, chosen_rarity, rarity_name

BANK_MIN_LEVEL       = 4
BANK_OPEN_COST       = 10_000
BANK_INTEREST_RATE   = 0.03
BANK_MAX_INTEREST    = 325_000
BANK_NUM_CHANGE_COST = 1_250
BANK_NUM_CHANGE_CD   = 72 * 3600

TRANSFER_MIN       = 50
TRANSFER_MAX       = 500_000
TRANSFER_MIN_LEVEL = 2
TRANSFER_COOLDOWN  = 30

STRAY_CHANCE       = 0.30
STRAY_MAX_TRIES    = 3
STRAY_BASE_COST    = 600
STRAY_COST_MULT    = 2.0
STRAY_TRIGGER_HOPS = 20

JAIL_DURATION     = 1800
BAIL_COST_PER_MIN = 100
JAIL_WORK_INTERVAL  = 300
JAIL_WORK_EARN      = 50
JAIL_ESCAPE_CHANCE  = 0.25

SPAM_WINDOW    = 3
SPAM_THRESHOLD = 3
SPAM_JAIL_BASE = 300

SMUGGLE_BASE_CATCH  = 0.10
SMUGGLE_CATCH_PER   = 0.10
SMUGGLE_REWARD_EACH = 800
SMUGGLE_JAIL_MINS   = 30

FACTORY_MIN_LEVEL  = 7
FACTORY_BUILD_COST = 20_000
FACTORY_WORKER_COST = 5_000

WAREHOUSE_LEVELS = {
    1:  (20,   0),
    2:  (40,   6_000),
    3:  (80,   16_000),
    4:  (150,  36_000),
    5:  (280,  80_000),
    6:  (500,  180_000),
    7:  (900,  400_000),
    8:  (1600, 900_000),
    9:  (2800, 2_000_000),
    10: (5000, 5_000_000),
}
WAREHOUSE_MAX_LEVEL = 10

MACHINE_LEVELS = {
    1:  (120, 0),
    2:  (90,  10_000),
    3:  (70,  24_000),
    4:  (55,  50_000),
    5:  (42,  110_000),
    6:  (32,  240_000),
    7:  (24,  520_000),
    8:  (18,  1_160_000),
    9:  (13,  2_600_000),
    10: (9,   6_000_000),
}
MACHINE_MAX_LEVEL = 10

FACTORY_LEVEL_EXP = {
    1:  100,
    2:  300,
    3:  700,
    4:  1_500,
    5:  3_000,
    6:  6_000,
    7:  12_000,
    8:  25_000,
    9:  50_000,
    10: 0,
}
FACTORY_MAX_LEVEL = 10
FACTORY_DAILY_CAP   = 5_000_000
FACTORY_SELL_TAX    = 0.10

FACTORY_PRODUCTS = [
    ("نخ میویی 🧵",            200,     400,     1,  5),
    ("پشمک پیشی 🍭",           350,     700,     1,  8),
    ("چرم گربه 🐾",            500,     950,     1,  10),
    ("جوراب پیشی 🧦",          800,     1_600,   2,  16),
    ("کلاه میویی 🎩",          1_200,   2_400,   2,  22),
    ("عطر پیشی 🌸",            2_000,   4_200,   3,  38),
    ("صابون میویی 🧼",         1_500,   3_100,   3,  28),
    ("کنسرو ماهی 🐟",          3_000,   6_500,   4,  58),
    ("شامپو پشمالو 🛁",        2_500,   5_200,   4,  46),
    ("ابزار چنگول 🔧",         5_000,   11_000,  5,  95),
    ("دستکش میویی 🧤",         4_000,   8_500,   5,  76),
    ("باتری پیشی ⚡",          8_000,   17_500,  6,  150),
    ("چیپ میویی 💾",           12_000,  26_000,  6,  220),
    ("ربات پیشی 🤖",           20_000,  45_000,  7,  380),
    ("موشک میویی 🚀",          35_000,  78_000,  7,  650),
    ("فضاپیمای پشمالو 🛸",    60_000,  45_000,  8,  1_100),
    ("کریستال میویی 💎",       80_000,  60_000,  8,  1_500),
    ("پورتال پیشی 🌀",         150_000, 113_000, 9,  2_800),
    ("قلب میویی افسانه‌ای ✨", 300_000, 230_000, 10, 6_000),
]

MARKET_PRICE_MIN = 0.6
MARKET_PRICE_MAX = 2.2

def get_db():
    conn = sqlite3.connect("happy_bot.db", timeout=10)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA journal_mode=WAL")
    conn.execute("PRAGMA synchronous=NORMAL")
    return conn

class db_conn:
    def __enter__(self):
        self.conn = get_db()
        return self.conn
    def __exit__(self, *_):
        try:
            self.conn.close()
        except Exception:
            pass

def init_db():
    conn = get_db()
    c = conn.cursor()
    c.execute("""CREATE TABLE IF NOT EXISTS users (
        user_id    INTEGER PRIMARY KEY,
        username   TEXT,
        first_name TEXT,
        hop_points REAL DEFAULT 0,
        total_hops INTEGER DEFAULT 0,
        level      INTEGER DEFAULT 1,
        last_hop   TEXT DEFAULT NULL,
        joined_at  TEXT DEFAULT CURRENT_TIMESTAMP
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS dogs (
        user_id      INTEGER PRIMARY KEY,
        name         TEXT DEFAULT 'سگولو',
        level        INTEGER DEFAULT 1,
        rank         INTEGER DEFAULT 1,
        points_box   REAL DEFAULT 0,
        fed_until    TEXT DEFAULT NULL,
        last_collect TEXT DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS groups (
        group_id    INTEGER PRIMARY KEY,
        title       TEXT,
        level       INTEGER DEFAULT 1,
        treasury    REAL DEFAULT 0,
        total_hops  INTEGER DEFAULT 0,
        total_dogs  INTEGER DEFAULT 0,
        total_bones INTEGER DEFAULT 0,
        total_fish  INTEGER DEFAULT 0
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS hooks (
        user_id   INTEGER PRIMARY KEY,
        level     INTEGER DEFAULT 1,
        last_cast TEXT DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS pending_bones (
        user_id   INTEGER PRIMARY KEY,
        bone_name TEXT,
        weight    REAL,
        price     INTEGER,
        caught_at TEXT
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS bank (
        user_id         INTEGER PRIMARY KEY,
        balance         REAL DEFAULT 0,
        account_number  TEXT UNIQUE,
        opened_at       TEXT DEFAULT CURRENT_TIMESTAMP,
        last_interest   TEXT DEFAULT NULL,
        last_num_change TEXT DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS transfers (
        user_id       INTEGER PRIMARY KEY,
        last_transfer TEXT DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS stray_dogs (
        group_id     INTEGER PRIMARY KEY,
        tries_left   INTEGER DEFAULT 3,
        current_cost INTEGER DEFAULT 300,
        appeared_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        rescuer_ids  TEXT DEFAULT ''
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS user_strays (
        user_id INTEGER PRIMARY KEY,
        count   INTEGER DEFAULT 0
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS jail (
        user_id     INTEGER PRIMARY KEY,
        jailed_at   TEXT,
        release_at  TEXT,
        reason      TEXT DEFAULT 'قاچاق',
        spam_count  INTEGER DEFAULT 0,
        work_points INTEGER DEFAULT 0,
        last_work   TEXT    DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS spam_tracker (
        user_id    INTEGER,
        group_id   INTEGER,
        msg_times  TEXT DEFAULT '[]',
        jail_count INTEGER DEFAULT 0,
        PRIMARY KEY (user_id, group_id)
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS factories (
        user_id         INTEGER PRIMARY KEY,
        level           INTEGER DEFAULT 1,
        exp             INTEGER DEFAULT 0,
        warehouse_level INTEGER DEFAULT 1,
        machine_level   INTEGER DEFAULT 1,
        stock           INTEGER DEFAULT 0,
        last_produced   TEXT    DEFAULT NULL,
        producing       INTEGER DEFAULT 0,
        product_idx     INTEGER DEFAULT 0,
        production_end  TEXT    DEFAULT NULL
    )""")
    c.execute("""CREATE TABLE IF NOT EXISTS market_prices (
        product_idx INTEGER PRIMARY KEY,
        multiplier  REAL    DEFAULT 1.0,
        updated_at  TEXT    DEFAULT CURRENT_TIMESTAMP
    )""")
    conn.commit()
    conn.close()

    conn = get_db()
    for table, col, definition in [
        ("groups", "total_fish", "INTEGER DEFAULT 0"),
    ]:
        try:
            conn.execute(f"ALTER TABLE {table} ADD COLUMN {col} {definition}")
            conn.commit()
        except Exception:
            pass
    conn.close()
    conn2 = get_db()
    conn2.execute("""CREATE TABLE IF NOT EXISTS sub_admins (
        user_id    INTEGER PRIMARY KEY,
        username   TEXT,
        first_name TEXT,
        added_by   INTEGER,
        added_at   TEXT DEFAULT CURRENT_TIMESTAMP
    )""")
    conn2.execute("""CREATE TABLE IF NOT EXISTS lotteries (
        lottery_id   TEXT PRIMARY KEY,
        title        TEXT,
        prize        INTEGER DEFAULT 0,
        winner_count INTEGER DEFAULT 1,
        state        TEXT DEFAULT 'open',
        created_by   INTEGER,
        created_at   TEXT DEFAULT CURRENT_TIMESTAMP,
        end_at       TEXT DEFAULT NULL
    )""")
    conn2.execute("""CREATE TABLE IF NOT EXISTS lottery_entries (
        lottery_id TEXT,
        user_id    INTEGER,
        username   TEXT,
        first_name TEXT,
        joined_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (lottery_id, user_id)
    )""")
    conn2.commit()
    conn2.close()

    conn3 = get_db()
    conn3.execute("""CREATE TABLE IF NOT EXISTS user_market (
        listing_id   TEXT PRIMARY KEY,
        seller_id    INTEGER,
        seller_name  TEXT,
        title        TEXT,
        description  TEXT,
        content      TEXT,
        price        INTEGER,
        max_buyers   INTEGER DEFAULT 1,
        buyer_count  INTEGER DEFAULT 0,
        status       TEXT DEFAULT 'pending',
        created_at   TEXT DEFAULT CURRENT_TIMESTAMP
    )""")
    conn3.execute("""CREATE TABLE IF NOT EXISTS user_market_buyers (
        listing_id TEXT,
        buyer_id   INTEGER,
        bought_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (listing_id, buyer_id)
    )""")
    conn3.execute("""CREATE TABLE IF NOT EXISTS bot_settings (
        key   TEXT PRIMARY KEY,
        value TEXT
    )""")
    conn3.execute("""CREATE TABLE IF NOT EXISTS referrals (
        user_id    INTEGER PRIMARY KEY,
        inviter_id INTEGER,
        rewarded   INTEGER DEFAULT 0,
        joined_at  TEXT DEFAULT CURRENT_TIMESTAMP
    )""")
    conn3.commit()
    conn3.close()

    conn4 = get_db()
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor (
        group_id    INTEGER PRIMARY KEY,
        user_id     INTEGER,
        username    TEXT,
        first_name  TEXT,
        elected_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        term_end    TEXT
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_decrees (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id    INTEGER,
        user_id     INTEGER,
        decree_type TEXT,
        decree_name TEXT,
        issued_at   TEXT DEFAULT CURRENT_TIMESTAMP,
        expires_at  TEXT
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_elections (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id    INTEGER,
        status      TEXT DEFAULT 'candidacy',
        started_by  INTEGER,
        started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        ended_at    TEXT DEFAULT NULL
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_candidates (
        election_id INTEGER,
        user_id     INTEGER,
        username    TEXT,
        first_name  TEXT,
        joined_at   TEXT DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (election_id, user_id)
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_election_votes (
        election_id    INTEGER,
        voter_id       INTEGER,
        candidate_id   INTEGER,
        voted_at       TEXT DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (election_id, voter_id)
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_protests (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id    INTEGER,
        user_id     INTEGER,
        username    TEXT,
        first_name  TEXT,
        reason      TEXT,
        status      TEXT DEFAULT 'pending',
        created_at  TEXT DEFAULT CURRENT_TIMESTAMP
    )""")
    conn4.execute("""CREATE TABLE IF NOT EXISTS mayor_protest_votes (
        protest_id  INTEGER,
        user_id     INTEGER,
        vote        TEXT,
        voted_at    TEXT DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (protest_id, user_id)
    )""")
    conn4.commit()
    conn4.close()

    conn5 = get_db()
    conn5.execute("""CREATE TABLE IF NOT EXISTS mayor_project_settings (
        key   TEXT PRIMARY KEY,
        value TEXT
    )""")
    conn5.execute("""CREATE TABLE IF NOT EXISTS mayor_projects (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id    INTEGER,
        project_key TEXT,
        level       INTEGER DEFAULT 1,
        started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
        done_at     TEXT,
        status      TEXT DEFAULT 'building'
    )""")
    conn5.execute("""CREATE TABLE IF NOT EXISTS mayor_contracts (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id     INTEGER,
        contract_key TEXT,
        started_at   TEXT DEFAULT CURRENT_TIMESTAMP,
        expires_at   TEXT,
        status       TEXT DEFAULT 'active'
    )""")
    conn5.execute("""CREATE TABLE IF NOT EXISTS mayor_contract_settings (
        key   TEXT PRIMARY KEY,
        value TEXT
    )""")
    conn5.commit()
    conn5.close()

    conn6 = get_db()
    conn6.execute("""CREATE TABLE IF NOT EXISTS city_crises (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id     INTEGER,
        crisis_type  TEXT,
        status       TEXT DEFAULT 'active',
        started_at   TEXT DEFAULT CURRENT_TIMESTAMP,
        expires_at   TEXT,
        resolved_by  INTEGER DEFAULT NULL,
        decision     TEXT DEFAULT NULL,
        resolved_at  TEXT DEFAULT NULL
    )""")
    conn6.execute("""CREATE TABLE IF NOT EXISTS city_crisis_penalties (
        group_id     INTEGER PRIMARY KEY,
        penalty_type TEXT,
        penalty_value INTEGER,
        expires_at   TEXT
    )""")
    conn6.commit()
    conn6.close()

    logger.info("✅ دیتابیس آماده شد")
    init_mayor_full_tables()
    init_leader_tables()
    init_force_join_table()
    migrate_db()

def migrate_db():
    logger.info("🔧 migrate_db شروع شد...")
    with db_conn() as conn:

        conn.execute("""CREATE TABLE IF NOT EXISTS transactions (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id    INTEGER,
            type       TEXT,
            amount     REAL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.commit()

        REQUIRED_COLUMNS = {
            "users": [
                ("hop_points",  "REAL    DEFAULT 0"),
                ("total_hops",  "INTEGER DEFAULT 0"),
                ("level",       "INTEGER DEFAULT 1"),
                ("last_hop",    "TEXT    DEFAULT NULL"),
                ("joined_at",   "TEXT    DEFAULT CURRENT_TIMESTAMP"),
                ("username",    "TEXT"),
                ("first_name",  "TEXT"),
            ],
            "dogs": [
                ("name",         "TEXT    DEFAULT 'سگولو'"),
                ("level",        "INTEGER DEFAULT 1"),
                ("rank",         "INTEGER DEFAULT 1"),
                ("points_box",   "REAL    DEFAULT 0"),
                ("fed_until",    "TEXT    DEFAULT NULL"),
                ("last_collect", "TEXT    DEFAULT NULL"),
            ],
            "groups": [
                ("title",       "TEXT"),
                ("level",       "INTEGER DEFAULT 1"),
                ("treasury",    "REAL    DEFAULT 0"),
                ("total_hops",  "INTEGER DEFAULT 0"),
                ("total_dogs",  "INTEGER DEFAULT 0"),
                ("total_bones", "INTEGER DEFAULT 0"),
                ("total_fish",  "INTEGER DEFAULT 0"),
            ],
            "hooks": [
                ("level",     "INTEGER DEFAULT 1"),
                ("last_cast", "TEXT    DEFAULT NULL"),
            ],
            "pending_bones": [
                ("bone_name", "TEXT"),
                ("weight",    "REAL"),
                ("price",     "INTEGER"),
                ("caught_at", "TEXT"),
            ],
            "bank": [
                ("balance",         "REAL DEFAULT 0"),
                ("account_number",  "TEXT UNIQUE"),
                ("opened_at",       "TEXT DEFAULT CURRENT_TIMESTAMP"),
                ("last_interest",   "TEXT DEFAULT NULL"),
                ("last_num_change", "TEXT DEFAULT NULL"),
            ],
            "factories": [
                ("level",           "INTEGER DEFAULT 1"),
                ("exp",             "INTEGER DEFAULT 0"),
                ("warehouse_level", "INTEGER DEFAULT 1"),
                ("machine_level",   "INTEGER DEFAULT 1"),
                ("stock",           "INTEGER DEFAULT 0"),
                ("last_produced",   "TEXT    DEFAULT NULL"),
                ("producing",       "INTEGER DEFAULT 0"),
                ("product_idx",     "INTEGER DEFAULT 0"),
                ("production_end",  "TEXT    DEFAULT NULL"),
            ],
            "jail": [
                ("jailed_at",   "TEXT"),
                ("release_at",  "TEXT"),
                ("reason",      "TEXT    DEFAULT 'قاچاق'"),
                ("spam_count",  "INTEGER DEFAULT 0"),
                ("work_points", "INTEGER DEFAULT 0"),
                ("last_work",   "TEXT    DEFAULT NULL"),
            ],
            "user_strays": [
                ("count", "INTEGER DEFAULT 0"),
            ],
            "spam_tracker": [
                ("msg_times",  "TEXT    DEFAULT '[]'"),
                ("jail_count", "INTEGER DEFAULT 0"),
            ],
            "market_prices": [
                ("multiplier",  "REAL DEFAULT 1.0"),
                ("updated_at",  "TEXT DEFAULT CURRENT_TIMESTAMP"),
            ],
            "transactions": [
                ("user_id",    "INTEGER"),
                ("type",       "TEXT"),
                ("amount",     "REAL"),
                ("created_at", "TEXT DEFAULT CURRENT_TIMESTAMP"),
            ],
        }

        for table, columns in REQUIRED_COLUMNS.items():
            exists = conn.execute(
                "SELECT name FROM sqlite_master WHERE type='table' AND name=?", (table,)
            ).fetchone()
            if not exists:
                logger.warning(f"⚠️ جدول {table} وجود نداره — رد میشه")
                continue

            current_cols = {
                row[1] for row in conn.execute(f"PRAGMA table_info({table})").fetchall()
            }

            for col_name, col_def in columns:
                if col_name not in current_cols:
                    try:
                        conn.execute(f"ALTER TABLE {table} ADD COLUMN {col_name} {col_def}")
                        conn.commit()
                        logger.info(f"✅ ستون '{col_name}' به جدول '{table}' اضافه شد")
                    except Exception as e:
                        logger.warning(f"⚠️ نتونست ستون {col_name} رو به {table} اضافه کنه: {e}")

        conn.execute("""
            INSERT OR IGNORE INTO factories (user_id)
            SELECT u.user_id FROM users u
            WHERE u.level >= ? AND u.user_id NOT IN (SELECT user_id FROM factories)
        """, (FACTORY_MIN_LEVEL,))
        restored = conn.execute("SELECT changes()").fetchone()[0]
        if restored:
            logger.info(f"✅ {restored} کارخونه برای کاربرای ریست‌شده دوباره ساخته شد")
        conn.commit()

    logger.info("✅ migrate_db تموم شد")

def get_user(user_id):
    with db_conn() as conn:
        return conn.execute("SELECT * FROM users WHERE user_id=?", (user_id,)).fetchone()

def ensure_user(user_id, username, first_name):
    with db_conn() as conn:
        conn.execute("INSERT OR IGNORE INTO users (user_id,username,first_name) VALUES (?,?,?)",
                     (user_id, username, first_name))
        conn.execute("UPDATE users SET username=?,first_name=? WHERE user_id=?",
                     (username, first_name, user_id))
        conn.commit()

def ensure_group(group_id, title):
    with db_conn() as conn:
        conn.execute("INSERT OR IGNORE INTO groups (group_id,title) VALUES (?,?)", (group_id, title))
        conn.execute("UPDATE groups SET title=? WHERE group_id=?", (title, group_id))
        conn.commit()

def get_level(total_hops):
    level = 1
    for i, threshold in enumerate(LEVEL_THRESHOLDS):
        if total_hops >= threshold:
            level = i + 2
        else:
            break
    return min(level, 1000)

def hops_for_next_level(level):
    if level >= 1000: return 0
    return LEVEL_THRESHOLDS[level - 1]

def calc_hop_reward(level):
    _base  = 56.74
    _ratio = 1.2336
    max_reward = int(_base * (_ratio ** (level - 1)))
    min_reward = 20
    if level >= 43:
        mid = int(max_reward * 0.60)
        if random.random() < 0.60:
            return random.randint(min_reward, mid)
        else:
            return random.randint(mid, max_reward)
    return random.randint(min_reward, max_reward)

def calc_dog_points(dog):
    if not dog["last_collect"]: return 0.0
    fed_until = dog["fed_until"]
    if not fed_until or datetime.fromisoformat(fed_until) < datetime.now(): return 0.0
    last    = datetime.fromisoformat(dog["last_collect"])
    seconds = (datetime.now() - last).total_seconds()
    rate, capacity, _ = DOG_LEVEL_DATA.get(dog["level"], (0.1, 5000, 500))
    rank_mult = DOG_RANKS.get(dog["rank"], ("", 1.0))[1]
    return min(seconds * rate * rank_mult, capacity)

def parse_amount(text):
    text = text.strip().replace(",", "").replace("_", "")
    multipliers = {"k": 1_000, "کی": 1_000, "کا": 1_000, "m": 1_000_000, "میل": 1_000_000}
    for suffix, mult in multipliers.items():
        if text.lower().endswith(suffix):
            try: return int(float(text[:-len(suffix)]) * mult)
            except: return -1
    try: return int(float(text))
    except: return -1

def is_in_jail(user_id):
    with db_conn() as conn:
        row = conn.execute("SELECT * FROM jail WHERE user_id=?", (user_id,)).fetchone()
        if not row: return False, None
        if datetime.fromisoformat(row["release_at"]) > datetime.now(): return True, row
        conn.execute("DELETE FROM jail WHERE user_id=?", (user_id,))
        conn.commit()
    return False, None

def jail_user(user_id, reason="قاچاق", duration_seconds=None):
    now = datetime.now()
    secs = duration_seconds if duration_seconds else JAIL_DURATION
    release = now + timedelta(seconds=secs)
    with db_conn() as conn:
        conn.execute(
            "INSERT OR REPLACE INTO jail (user_id,jailed_at,release_at,reason,spam_count,work_points,last_work) VALUES (?,?,?,?,0,0,NULL)",
            (user_id, now.isoformat(), release.isoformat(), reason)
        )
        conn.commit()

def jail_spam(user_id, group_id) -> int:
    import json, time
    now_ts = time.time()
    with db_conn() as conn:
        row = conn.execute(
            "SELECT * FROM spam_tracker WHERE user_id=? AND group_id=?", (user_id, group_id)
        ).fetchone()
        if row:
            times = json.loads(row["msg_times"])
            jail_count = row["jail_count"]
        else:
            times = []
            jail_count = 0

        times = [t for t in times if now_ts - t < SPAM_WINDOW]
        times.append(now_ts)

        if len(times) >= SPAM_THRESHOLD:
            duration = SPAM_JAIL_BASE * (2 ** jail_count)
            jail_count += 1
            times = []
            conn.execute(
                "INSERT OR REPLACE INTO spam_tracker (user_id,group_id,msg_times,jail_count) VALUES (?,?,?,?)",
                (user_id, group_id, json.dumps(times), jail_count)
            )
            conn.commit()
            jail_user(user_id, reason=f"اسپم (دفعه {jail_count})", duration_seconds=duration)
            return duration
        else:
            conn.execute(
                "INSERT OR REPLACE INTO spam_tracker (user_id,group_id,msg_times,jail_count) VALUES (?,?,?,?)",
                (user_id, group_id, json.dumps(times), jail_count)
            )
            conn.commit()
            return 0


async def dog_cmd_for_user(query, uid):
    conn = get_db()
    u   = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
    dog = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
    conn.close()
    if not dog:
        await query.message.reply_text("سگی نداری! بنویس *سگ* تا بخری 🐕", parse_mode="Markdown")
        return
    fed_until = dog["fed_until"]
    now = datetime.now()
    fed = fed_until and datetime.fromisoformat(fed_until) > now
    if fed:
        rem = int((datetime.fromisoformat(fed_until) - now).total_seconds())
        h2, r2 = divmod(rem, 3600); m2, s2 = divmod(r2, 60)
        fed_str = f"✅ سیر | {h2}:{m2:02d}:{s2:02d} مونده"
    else:
        fed_str = "😿 گرسنه!"
    pending = calc_dog_points(dog) + dog["points_box"]
    rank_name, rank_mult = DOG_RANKS.get(dog["rank"], ("نامشخص", 1))
    _, _, upgrade_cost = DOG_LEVEL_DATA.get(dog["level"], (0, 0, 0))
    text = (
        f"╮──「 🐕 پنل سگ 」\n\n"
        f"┐─ 🏷️ اسم : {dog['name']}\n"
        f"┐─ ⭐️ سطح : {dog['level']}/{DOG_MAX_LEVEL}\n"
        f"┐─ 🎖️ مقام : {rank_name}\n"
        f"┐─ 🍖 وضعیت : {fed_str}\n"
        f"└─ 📦 جعبه : {int(pending):,} هاپ پوینت\n\n"
        f"👛 موجودیت: {u['hop_points']:,.0f}"
    )
    kb = []
    if int(pending) >= 1:
        kb.append([cbtn(f"💰 برداشت ({int(pending):,})", callback_data=f"collect_{uid}")])
    kb.append([cbtn(f"🍖 خرید استخوان ({BONE_COST:,})", callback_data=f"feed_{uid}")])
    if dog["level"] < DOG_MAX_LEVEL:
        kb.append([cbtn(f"⬆️ ارتقا سطح ({upgrade_cost:,})", callback_data=f"upgradedog_{uid}")])
    if dog["rank"] < 10:
        kb.append([cbtn("🏅 ارتقا مقام", callback_data=f"rankup_{uid}")])
    kb.append([cbtn("✏️ تغییر اسم", callback_data=f"rename_{uid}")])
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

async def hook_cmd_for_user(query, uid):
    conn = get_db()
    u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
    hook = conn.execute("SELECT * FROM hooks WHERE user_id=?", (uid,)).fetchone()
    conn.close()
    if not hook:
        await query.message.reply_text("قلاب نداری! بنویس *قلاب* تا بخری 🦴", parse_mode="Markdown")
        return
    lvl = hook["level"]
    _, cd, upgrade_cost = HOOK_DATA[lvl]
    now = datetime.now()
    last = hook["last_cast"]
    if last:
        diff = (now - datetime.fromisoformat(last)).total_seconds()
        left = max(0, int(cd - diff))
        status = "✅ آماده صید!" if left == 0 else f"⌛️ {left} ثانیه مونده"
    else:
        status = "✅ آماده صید!"
    text = (
        f"╮──「 🦴 پنل قلاب 」\n\n"
        f"┐─ ⭐️ سطح : {lvl}/{HOOK_MAX_LEVEL}\n"
        f"┐─ ⌛️ cooldown : {cd} ثانیه\n"
        f"└─ 🎯 وضعیت : {status}\n\n"
        f"👛 موجودیت: {u['hop_points']:,.0f}"
    )
    kb = []
    if lvl < HOOK_MAX_LEVEL:
        kb.append([cbtn(f"⬆️ ارتقا قلاب ({upgrade_cost:,})", callback_data=f"upgradehook_{uid}")])
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb) if kb else None)

async def bank_cmd_for_user(query, uid):
    conn = get_db()
    u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
    bank = conn.execute("SELECT * FROM bank WHERE user_id=?", (uid,)).fetchone()
    conn.close()
    if not bank:
        await query.message.reply_text("حساب بانکی نداری! بنویس *بانک* تا بسازی 🏦", parse_mode="Markdown")
        return
    now = datetime.now()
    if bank["last_interest"]:
        diff = (now - datetime.fromisoformat(bank["last_interest"])).total_seconds()
        left = max(0, int(86400 - diff))
        h2, r2 = divmod(left, 3600); m2, s2 = divmod(r2, 60)
        interest_str = f"⌛️ {h2}:{m2:02d}:{s2:02d} تا سود بعدی"
    else:
        interest_str = "✅ سود آماده دریافته!"
    text = (
        f"╮──「 🏦 پنل بانک 」\n\n"
        f"┐─ 💳 شماره حساب : `{bank['account_number']}`\n"
        f"┐─ 💰 موجودی بانک : {bank['balance']:,.0f}\n"
        f"┐─ 📈 سود روزانه : ۳٪\n"
        f"└─ {interest_str}\n\n"
        f"👛 موجودی کیف: {u['hop_points']:,.0f}"
    )
    kb = [
        [cbtn("➕ واریز", callback_data=f"bank_deposit_{uid}"),
         cbtn("➖ برداشت", callback_data=f"bank_withdraw_{uid}")],
        [cbtn("💸 دریافت سود", callback_data=f"bank_interest_{uid}")],
        [cbtn("🔄 تغییر شماره حساب", callback_data=f"bank_changenum_{uid}")],
    ]
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

def get_factory(user_id):
    with db_conn() as conn:
        return conn.execute("SELECT * FROM factories WHERE user_id=?", (user_id,)).fetchone()

def refresh_market_prices():
    now = datetime.now()
    with db_conn() as conn:
        for idx in range(len(FACTORY_PRODUCTS)):
            row = conn.execute(
                "SELECT updated_at FROM market_prices WHERE product_idx=?", (idx,)
            ).fetchone()
            should_update = True
            if row and row["updated_at"]:
                diff = (now - datetime.fromisoformat(row["updated_at"])).total_seconds()
                if diff < 3600:
                    should_update = False
            if should_update:
                mult = round(random.uniform(MARKET_PRICE_MIN, MARKET_PRICE_MAX), 2)
                conn.execute(
                    "INSERT OR REPLACE INTO market_prices (product_idx, multiplier, updated_at) VALUES (?,?,?)",
                    (idx, mult, now.isoformat())
                )
        conn.commit()

def get_market_price(product_idx):
    refresh_market_prices()
    with db_conn() as conn:
        row = conn.execute(
            "SELECT multiplier FROM market_prices WHERE product_idx=?", (product_idx,)
        ).fetchone()
    base_price = FACTORY_PRODUCTS[product_idx][2]
    mult = row["multiplier"] if row else 1.0
    return int(base_price * mult), mult

def get_available_products(factory_level):
    return [
        (idx, p) for idx, p in enumerate(FACTORY_PRODUCTS)
        if p[3] <= factory_level
    ]

def get_stray_count_factory(user_id):
    with db_conn() as conn:
        row = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (user_id,)).fetchone()
    return row["count"] if row else 0

def check_production_done(factory):
    if not factory["producing"] or not factory["production_end"]:
        return False
    return datetime.now() >= datetime.fromisoformat(factory["production_end"])

def collect_production(user_id):
    factory = get_factory(user_id)
    if not factory or not check_production_done(factory):
        return 0
    workers = max(1, get_stray_count_factory(user_id))
    cap = WAREHOUSE_LEVELS[factory["warehouse_level"]][0]
    added = min(workers, max(0, cap - factory["stock"]))
    if added <= 0:
        return 0
    product = FACTORY_PRODUCTS[factory["product_idx"]]
    exp_gain = product[4] * added
    new_exp = factory["exp"] + exp_gain
    new_level = factory["level"]
    while new_level < FACTORY_MAX_LEVEL and FACTORY_LEVEL_EXP.get(new_level, 0) > 0 and new_exp >= FACTORY_LEVEL_EXP[new_level]:
        new_exp -= FACTORY_LEVEL_EXP[new_level]
        new_level += 1
    conn = get_db()
    conn.execute("""
        UPDATE factories SET stock=stock+?, exp=?, level=?, producing=0, production_end=NULL, last_produced=?
        WHERE user_id=?
    """, (added, new_exp, new_level, datetime.now().isoformat(), user_id))
    conn.commit()
    conn.close()
    return added

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat

    if chat.type == "private":
        ensure_user(user.id, user.username or "", user.first_name)

        args = context.args
        if args and args[0].startswith("ref_"):
            try:
                inviter_id = int(args[0].split("_")[1])
                if inviter_id != user.id:
                    with db_conn() as conn:
                        exists = conn.execute("SELECT 1 FROM referrals WHERE user_id=?", (user.id,)).fetchone()
                        if not exists:
                            conn.execute(
                                "INSERT OR IGNORE INTO referrals (user_id, inviter_id, rewarded) VALUES (?,?,0)",
                                (user.id, inviter_id)
                            )
                            conn.commit()
            except Exception:
                pass

        keyboard = ReplyKeyboardMarkup([
            ["🎁 دعوت دوستان", "🐾 هاپوهام"],
            ["🛒 مارکت", "📖 راهنما"],
            ["📊 لیدربرد"],
        ], resize_keyboard=True)

        await update.message.reply_text(
            f"🐕 سلام {user.first_name} عزیز!\n\n"
            f"من ربات هاپی هستم 🦴\n\n"
            f"📌 دستورات اصلی فقط *توی گروه* کار می‌کنن — منو به گروهت اضافه کن!\n\n"
            f"👇 از دکمه‌های زیر استفاده کن:",
            parse_mode="Markdown",
            reply_markup=keyboard
        )
        return

    ensure_user(user.id, user.username or "", user.first_name)
    ensure_group(chat.id, chat.title or "")
    await update.message.reply_text(
        "🐕 *ربات هاپی* اینجاست!\n\n🦴 توی گروه بنویس *هاپ* تا هاپ پوینت بگیری!\n"
        "هر ۵ دقیقه یه بار می‌تونی هاپ کنی 🐾\n\n📖 دستورات:\n"
        "پروفایل — پروفایلت\nبرترین — لیدربرد\nراهنما — راهنما",
        parse_mode="Markdown")

async def handle_hop(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text(f"🐕 هاپ فقط توی گروه کار می‌کنه!\nمنو به گروهت اضافه کن 👉 @{BOT_USERNAME}")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    ensure_group(chat.id, chat.title or "")

    _jailed, _jail_row = is_in_jail(user.id)
    if _jailed:
        _rel = datetime.fromisoformat(_jail_row["release_at"])
        _left = max(0, int((_rel - datetime.now()).total_seconds()))
        _m, _s = divmod(_left, 60)
        await update.message.reply_text(
            f"⛓️ *{user.first_name}، تو زندانی هستی!*\n"
            f"📌 دلیل: {_jail_row['reason']}\n"
            f"⌛️ {_m} دقیقه و {_s} ثانیه تا آزادی\n\n"
            f"بنویس *زندان* تا گزینه‌های آزادی رو ببینی.",
            parse_mode="Markdown"
        )
        return

    conn = get_db()
    u   = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    now = datetime.now()
    city_lvl = get_city_level(grp) if grp else 1
    effective_cd = city_hop_cooldown(city_lvl)
    effective_cd += crisis_hop_penalty(chat.id)
    if u["last_hop"]:
        diff = (now - datetime.fromisoformat(u["last_hop"])).total_seconds()
        if diff < effective_cd:
            remaining = int(effective_cd - diff)
            m, s = divmod(remaining, 60)
            conn.close()
            await update.message.reply_text(f"⏳ {user.first_name}، هنوز باید صبر کنی!\n⌛️ {m} دقیقه و {s} ثانیه دیگه می‌تونی هاپ کنی 🐾")
            return
    current_level = u["level"]
    reward     = calc_hop_reward(current_level)
    decree_mult = get_decree_hop_multiplier(chat.id)
    if decree_mult != 1.0:
        reward = int(reward * decree_mult)
    daily_prize = get_decree_daily_prize(chat.id)
    reward += daily_prize
    new_hops   = u["total_hops"] + 1
    new_points = u["hop_points"] + reward
    new_level  = get_level(new_hops)
    leveled_up = new_level > current_level
    conn.execute("UPDATE users SET hop_points=?,total_hops=?,level=?,last_hop=? WHERE user_id=?",
                 (new_points, new_hops, new_level, now.isoformat(), user.id))
    conn.execute("UPDATE groups SET total_hops=total_hops+1 WHERE group_id=?", (chat.id,))
    conn.commit()
    conn.close()
    next_lvl_hops = hops_for_next_level(new_level)
    progress = f"{new_hops}/{next_lvl_hops}" if new_level < 1000 else "MAX"
    decree_tag = " 🎉" if decree_mult != 1.0 else ""
    prize_tag  = f"\n🎁 جایزه روزانه: +{daily_prize:,}" if daily_prize > 0 else ""
    msg = (f"🐕 *هاپ هاپ!* {user.first_name}\n\n🦴 +{reward:,} هاپ پوینت{decree_tag}\n"
           f"💰 موجودی: {new_points:,.0f}\n⭐️ سطح: {new_level} | هاپ: {progress}{prize_tag}")
    if leveled_up:
        msg += f"\n\n🎉 *لِول آپ!* به سطح {new_level} رسیدی!"
    await update.message.reply_text(msg, parse_mode="Markdown")

    if new_hops == 1 and is_referral_enabled():
        with db_conn() as rconn:
            ref = rconn.execute(
                "SELECT * FROM referrals WHERE user_id=? AND rewarded=0", (user.id,)
            ).fetchone()
            if ref:
                inviter_id = ref["inviter_id"]
                reward_s = get_referral_reward_sender()
                reward_j = get_referral_reward_joiner()
                rconn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (reward_j, user.id))
                inviter = rconn.execute("SELECT user_id FROM users WHERE user_id=?", (inviter_id,)).fetchone()
                if inviter:
                    rconn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (reward_s, inviter_id))
                rconn.execute("UPDATE referrals SET rewarded=1 WHERE user_id=?", (user.id,))
                rconn.commit()
                await update.message.reply_text(
                    f"🎁 *جایزه دعوت!*\n\n+{reward_j:,} هاپ پوینت به خاطر ورود با لینک دعوت دریافت کردی!",
                    parse_mode="Markdown"
                )
                if inviter:
                    try:
                        await context.bot.send_message(
                            chat_id=inviter_id,
                            text=f"🎉 *دعوت موفق!*\n\n{user.first_name} با لینک دعوت تو وارد شد و اولین هاپش رو زد!\n💰 +{reward_s:,} هاپ پوینت به حسابت واریز شد 🦴",
                            parse_mode="Markdown"
                        )
                    except Exception:
                        pass

    with db_conn() as conn:
        fresh_grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    expire_old_crises()
    await trigger_city_crisis(update, context, chat.id, fresh_grp)

    await check_stray_dog(update, context)

async def hook_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🦴 این دستور فقط توی گروه کار می‌کنه!")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    conn = get_db()
    u    = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    hook = conn.execute("SELECT * FROM hooks WHERE user_id=?", (user.id,)).fetchone()
    conn.close()
    if u["level"] < HOOK_MIN_LEVEL:
        await update.message.reply_text(f"🔒 برای خرید قلاب باید حداقل سطح {HOOK_MIN_LEVEL} باشی!\nسطح فعلیت: {u['level']} ⭐️")
        return
    cost_buy, cooldown, _ = HOOK_DATA[1]
    if not hook:
        kb = InlineKeyboardMarkup([[
            cbtn(f"✅ خرید قلاب ({cost_buy:,} پوینت)", callback_data=f"buyhook_{user.id}"),
            cbtn("❌ انصراف", callback_data="cancel"),
        ]])
        await update.message.reply_text(
            f"🦴 *قلاب استخوان‌گیری*\n\n💰 هزینه: {cost_buy:,} هاپ پوینت\n"
            f"⌛️ cooldown اولیه: {cooldown} ثانیه\n📦 موجودیت: {u['hop_points']:,.0f}\n\nبعد از خرید بنویس *استخوان* تا بندازی!",
            parse_mode="Markdown", reply_markup=kb)
        return
    lvl = hook["level"]
    _, cd, upgrade_cost = HOOK_DATA[lvl]
    now  = datetime.now()
    last = hook["last_cast"]
    if last:
        diff  = (now - datetime.fromisoformat(last)).total_seconds()
        ready = diff >= cd
        left  = max(0, int(cd - diff))
        status = "✅ آماده صید!" if ready else f"⌛️ {left} ثانیه مونده"
    else:
        status = "✅ آماده صید!"
    text = (f"🦴 *قلاب استخوان‌گیری*\n\n⭐️ سطح قلاب: {lvl}/{HOOK_MAX_LEVEL}\n"
            f"⌛️ cooldown: {cd} ثانیه\n🎯 وضعیت: {status}\n")
    if lvl < HOOK_MAX_LEVEL:
        text += f"\n💰 هزینه ارتقا: {upgrade_cost:,} هاپ پوینت"
    kb = []
    if lvl < HOOK_MAX_LEVEL:
        kb.append([cbtn(f"⬆️ ارتقا قلاب ({upgrade_cost:,})", callback_data=f"upgradehook_{user.id}")])
    await update.message.reply_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb) if kb else None)

async def cast_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🦴 این دستور فقط توی گروه کار می‌کنه!")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    conn = get_db()
    hook = conn.execute("SELECT * FROM hooks WHERE user_id=?", (user.id,)).fetchone()
    if not hook:
        conn.close()
        await update.message.reply_text("❌ هنوز قلاب نداری!\n📌 بنویس *قلاب* تا بخری 🦴", parse_mode="Markdown")
        return
    pending = conn.execute("SELECT * FROM pending_bones WHERE user_id=?", (user.id,)).fetchone()
    if pending:
        age = (datetime.now() - datetime.fromisoformat(pending["caught_at"])).total_seconds()
        if age < BONE_SELL_TIME:
            conn.close()
            left = int(BONE_SELL_TIME - age)
            kb = InlineKeyboardMarkup([[
                cbtn("💰 فروش", callback_data=f"sellbone_{user.id}"),
                cbtn("🍖 غذای سگ", callback_data=f"feeddog_{user.id}"),
            ]])
            _p_icon = get_bone_rarity(pending['bone_name'])
            _p_rarity = RARITY_ICON_MAP.get(_p_icon, "")
            await update.message.reply_text(
                f"⚠️ هنوز روی صید قبلیت تصمیم نگرفتی!\n\n"
                f"🦴 *{pending['bone_name']}*\n"
                f"🏷️ دسته: {_p_icon} {_p_rarity}\n"
                f"⚖️ وزن: {pending['weight']} kg\n"
                f"💰 ارزش: {pending['price']:,} هاپ پوینت\n\n"
                f"⌛️ {left} ثانیه فرصت داری!",
                parse_mode="Markdown", reply_markup=kb)
            return
        else:
            conn.execute("DELETE FROM pending_bones WHERE user_id=?", (user.id,))
    lvl = hook["level"]
    _, cd, _ = HOOK_DATA[lvl]
    grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    cd  = city_fish_cooldown(get_city_level(grp), cd) if grp else cd
    now = datetime.now()
    if hook["last_cast"]:
        diff = (now - datetime.fromisoformat(hook["last_cast"])).total_seconds()
        if diff < cd:
            left = int(cd - diff)
            conn.close()
            left_m = left // 60
            left_s = left % 60
            if left_m > 0:
                time_str = f"{left_m} دقیقه و {left_s} ثانیه"
            else:
                time_str = f"{left_s} ثانیه"
            await update.message.reply_text(f"⌛️ قلابت هنوز توی آبه!\n🕐 {time_str} دیگه می‌تونی دوباره بندازی 🦴")
            return
    bone_name, weight, price, rarity_icon, rarity_name = catch_bone(lvl)
    caught_at = now.isoformat()
    conn.execute("UPDATE hooks SET last_cast=? WHERE user_id=?", (caught_at, user.id))
    conn.execute("INSERT OR REPLACE INTO pending_bones (user_id,bone_name,weight,price,caught_at) VALUES (?,?,?,?,?)",
                 (user.id, bone_name, weight, price, caught_at))
    conn.execute("UPDATE groups SET total_bones=total_bones+1, total_fish=total_fish+1 WHERE group_id=?", (chat.id,))
    conn.commit()
    conn.close()
    kb = InlineKeyboardMarkup([[
        cbtn("💰 فروش", callback_data=f"sellbone_{user.id}"),
        cbtn("🍖 غذای سگ", callback_data=f"feeddog_{user.id}"),
    ]])
    if rarity_icon == "🔥":
        header = f"🔥🔥🔥 *{user.first_name} یه استخوان آتشی صید کرد!* 🔥🔥🔥"
    elif rarity_icon == "🔴":
        header = f"💥 *{user.first_name} یه استخوان کمیاب صید کرد!* 💥"
    elif rarity_icon == "🟠":
        header = f"✨ *{user.first_name} یه استخوان اسطوره‌ای صید کرد!* ✨"
    else:
        header = f"🎣 *{user.first_name} یه استخوان صید کرد!*"
    await update.message.reply_text(
        f"{header}\n\n"
        f"🦴 {bone_name}\n"
        f"🏷️ دسته: {rarity_icon} {rarity_name}\n"
        f"⚖️ وزن: {weight} kg\n"
        f"💰 ارزش فروش: {price:,} هاپ پوینت\n\n"
        f"⌛️ {BONE_SELL_TIME} ثانیه فرصت داری تصمیم بگیری!",
        parse_mode="Markdown", reply_markup=kb)

async def dog_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🐕 این دستور فقط توی گروه کار می‌کنه!")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    conn = get_db()
    u   = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    dog = conn.execute("SELECT * FROM dogs WHERE user_id=?", (user.id,)).fetchone()
    conn.close()
    if dog:
        await show_dog_panel(update, user, u, dog)
        return
    if u["level"] < DOG_MIN_LEVEL:
        await update.message.reply_text(f"🔒 برای خرید سگ باید حداقل سطح {DOG_MIN_LEVEL} باشی!\nسطح فعلیت: {u['level']} ⭐️")
        return
    if u["hop_points"] < DOG_COST:
        await update.message.reply_text(f"💸 برای خرید سگ به {DOG_COST:,} هاپ پوینت نیاز داری!\nموجودیت: {u['hop_points']:,.0f} 🦴")
        return
    kb = InlineKeyboardMarkup([[
        cbtn("✅ خرید سگ", callback_data=f"buydog_{user.id}"),
        cbtn("❌ انصراف", callback_data="cancel"),
    ]])
    await update.message.reply_text(
        f"🐕 *خرید سگ*\n\n💰 هزینه: {DOG_COST:,} هاپ پوینت\n"
        f"📦 نرخ تولید: 0.1 هاپ‌پوینت/ثانیه\n🍖 هر ۲ ساعت باید بهش غذا (استخوان) بدی!\n\nمطمئنی می‌خوای سگ بخری؟",
        parse_mode="Markdown", reply_markup=kb)

async def show_dog_panel(update, user, u, dog):
    now       = datetime.now()
    fed_until = dog["fed_until"]
    is_fed    = fed_until and datetime.fromisoformat(fed_until) > now
    pending   = calc_dog_points(dog) + dog["points_box"]
    rate, capacity, upgrade_cost = DOG_LEVEL_DATA.get(dog["level"], (0.1, 5000, 500))
    rank_name, rank_mult = DOG_RANKS.get(dog["rank"], ("نامشخص", 1.0))
    effective_rate = rate * rank_mult
    if is_fed:
        fed_left = int((datetime.fromisoformat(fed_until) - now).total_seconds())
        fm, fs = divmod(fed_left, 60)
        fh, fm = divmod(fm, 60)
        fed_str = f"✅ سیر ({fh}:{fm:02d}:{fs:02d} مونده)"
    else:
        fed_str = "😋 گشنه — تولید متوقفه!"
    text = (f"🐕 *سگت: {dog['name']}*\n\n🎖 مقام: {rank_name}\n⭐️ سطح سگ: {dog['level']}/{DOG_MAX_LEVEL}\n"
            f"⚡️ تولید: {effective_rate:.2f} هاپ‌پوینت/ثانیه\n📦 جعبه: {pending:,.0f} / {capacity:,}\n🍖 شکم: {fed_str}\n")
    if dog["level"] < DOG_MAX_LEVEL:
        text += f"\n💰 هزینه ارتقا سطح: {upgrade_cost:,} هاپ‌پوینت"
    if dog["rank"] < 10:
        text += f"\n🌟 ارتقا مقام هر ۲ سطح یه بار"
    uid = user.id
    kb  = [
        [cbtn("📦 برداشت پوینت‌ها", callback_data=f"collect_{uid}")],
        [cbtn("🍖 خرید استخوان (غذا)", callback_data=f"feed_{uid}")],
    ]
    if dog["level"] < DOG_MAX_LEVEL:
        kb.append([cbtn(f"⬆️ ارتقا سطح ({upgrade_cost:,})", callback_data=f"upgradedog_{uid}")])
    if dog["rank"] < 10 and dog["level"] >= dog["rank"] * 2:
        kb.append([cbtn("🏅 ارتقا مقام", callback_data=f"rankup_{uid}")])
    kb.append([cbtn("✏️ تغییر اسم", callback_data=f"rename_{uid}")])
    await update.message.reply_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

async def callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data
    uid_self = query.from_user.id

    def check_owner(uid):
        return uid_self == uid
    
    if data.startswith("vmeow_"):
        target_id = int(data.split("_")[1])
        voter_id = query.from_user.id
        
        if voter_id == target_id:
            await query.answer("❌ داداش نمیتونی به خودت رای بدی!", show_alert=True)
            return

        if "meow_votes" not in context.bot_data:
            context.bot_data["meow_votes"] = {}
        if target_id not in context.bot_data["meow_votes"]:
            context.bot_data["meow_votes"][target_id] = set()
            
        votes_set = context.bot_data["meow_votes"][target_id]
        
        if voter_id in votes_set:
            await query.answer("❌ شما قبلاً رای داده‌اید!", show_alert=True)
            return
            
        votes_set.add(voter_id)
        current_votes = len(votes_set)
        
        if current_votes >= 3:
            await query.answer("🔒 کاربر به زندان فرستاده شد!", show_alert=True)
            
            conn = get_db()
            now = datetime.now()
            release_time = (now + timedelta(minutes=15)).isoformat()
            conn.execute("INSERT OR IGNORE INTO users (user_id, username, first_name) VALUES (?,?,?)", 
                        (target_id, "", "کاربر"))
            conn.execute(
                "INSERT OR REPLACE INTO jail (user_id, jailed_at, release_at, reason) VALUES (?, ?, ?, ?)",
                (target_id, now.isoformat(), release_time, "گفتن کلمه ممنوعه میو")
            )
            conn.commit()
            conn.close()
            
            del context.bot_data["meow_votes"][target_id]
            
            await query.edit_message_text(
                f"🔒 کاربر به دلیل گفتن «میو» با ۳ رای موافق به مدت ۱۵ دقیقه زندانی شد! 😾",
                parse_mode="Markdown"
            )
            return
        else:
            await query.answer("✅ رای شما ثبت شد.")
            keyboard = [[
                cbtn(f"رای به زندانی شدن ({current_votes}/3) ⚖️", callback_data=f"vmeow_{target_id}")
            ]]
            reply_markup = InlineKeyboardMarkup(keyboard)
            await query.edit_message_reply_markup(reply_markup=reply_markup)
            return

        
    if data.startswith("buyhook_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        cost, _, _ = HOOK_DATA[1]
        if u["hop_points"] < cost: conn.close(); await query.answer(f"پوینت کافی نداری! لازم: {cost:,}", show_alert=True); return
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (cost, uid))
        conn.execute("INSERT OR IGNORE INTO hooks (user_id,level) VALUES (?,1)", (uid,))
        conn.commit(); conn.close()
        await query.edit_message_text("🎉 *قلاب استخوان‌گیری خریدی!*\n\n🦴 حالا توی گروه بنویس *استخوان* تا بندازی!\n\n⏳ در حال بازگشت به پنل قلاب...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await hook_cmd_for_user(query, uid)
        return

    if data.startswith("upgradehook_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        hook = conn.execute("SELECT * FROM hooks WHERE user_id=?", (uid,)).fetchone()
        if not hook or hook["level"] >= HOOK_MAX_LEVEL: conn.close(); await query.answer("قلابت ماکسه!", show_alert=True); return
        _, _, upgrade_cost = HOOK_DATA[hook["level"]]
        if u["hop_points"] < upgrade_cost: conn.close(); await query.answer(f"پوینت کافی نداری! لازم: {upgrade_cost:,}", show_alert=True); return
        new_lvl = hook["level"] + 1
        _, new_cd, _ = HOOK_DATA[new_lvl]
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (upgrade_cost, uid))
        conn.execute("UPDATE hooks SET level=? WHERE user_id=?", (new_lvl, uid))
        conn.commit(); conn.close()
        await query.edit_message_text(f"⬆️ *قلاب ارتقا پیدا کرد!*\n\n⭐️ سطح جدید: {new_lvl}\n⌛️ cooldown جدید: {new_cd} ثانیه\n\n⏳ در حال بازگشت به پنل قلاب...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await hook_cmd_for_user(query, uid)
        return

    if data.startswith("sellbone_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn    = get_db()
        pending = conn.execute("SELECT * FROM pending_bones WHERE user_id=?", (uid,)).fetchone()
        if not pending: conn.close(); await query.answer("استخوانی برای فروش نیست!", show_alert=True); return
        age = (datetime.now() - datetime.fromisoformat(pending["caught_at"])).total_seconds()
        if age > BONE_SELL_TIME:
            conn.execute("DELETE FROM pending_bones WHERE user_id=?", (uid,))
            conn.commit(); conn.close(); await query.answer("⌛️ وقتت تموم شد! استخوان از دست رفت.", show_alert=True); return
        price = pending["price"]
        conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (price, uid))
        conn.execute("DELETE FROM pending_bones WHERE user_id=?", (uid,))
        conn.commit(); conn.close()
        await query.edit_message_text(f"💰 *{pending['bone_name']}* رو فروختی!\n\n⚖️ وزن: {pending['weight']} kg\n🦴 +{price:,} هاپ پوینت دریافت کردی!", parse_mode="Markdown")
        return

    if data.startswith("feeddog_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn    = get_db()
        pending = conn.execute("SELECT * FROM pending_bones WHERE user_id=?", (uid,)).fetchone()
        dog     = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
        if not pending: conn.close(); await query.answer("استخوانی نداری!", show_alert=True); return
        if not dog: conn.close(); await query.answer("سگی نداری! اول سگ بخر 🐕", show_alert=True); return
        age = (datetime.now() - datetime.fromisoformat(pending["caught_at"])).total_seconds()
        if age > BONE_SELL_TIME:
            conn.execute("DELETE FROM pending_bones WHERE user_id=?", (uid,))
            conn.commit(); conn.close(); await query.answer("⌛️ وقتت تموم شد! استخوان از دست رفت.", show_alert=True); return
        now       = datetime.now()
        fed_until = dog["fed_until"]
        duration  = calc_feed_duration(pending["bone_name"], pending["weight"])
        new_fed   = (datetime.fromisoformat(fed_until) + timedelta(seconds=duration)) if (fed_until and datetime.fromisoformat(fed_until) > now) else (now + timedelta(seconds=duration))
        rarity_icon = get_bone_rarity(pending["bone_name"])
        rarity_name = RARITY_ICON_MAP.get(rarity_icon, "")
        duration_min = duration // 60
        conn.execute("UPDATE dogs SET fed_until=? WHERE user_id=?", (new_fed.isoformat(), uid))
        conn.execute("DELETE FROM pending_bones WHERE user_id=?", (uid,))
        conn.commit(); conn.close()
        await query.edit_message_text(
            f"🍖 *{pending['bone_name']}* رو به سگت دادی!\n\n"
            f"⚖️ وزن: {pending['weight']} kg\n"
            f"🏷️ دسته: {rarity_icon} {rarity_name}\n"
            f"⏱️ مدت سیری: {duration_min} دقیقه\n"
            f"😋 سگت تا {new_fed.strftime('%H:%M')} سیره! 🐕",
            parse_mode="Markdown"
        )
        return

    if data.startswith("confirm_delete_user_"):
        if not is_admin(uid_self):
            await query.answer("فقط ادمین اصلی!", show_alert=True); return
        target_id = int(data.split("_")[3])
        if target_id in ADMIN_IDS:
            await query.answer("❌ نمیشه ادمین اصلی رو حذف کرد!", show_alert=True); return
        conn = get_db()
        tu = conn.execute("SELECT first_name FROM users WHERE user_id=?", (target_id,)).fetchone()
        if not tu:
            conn.close()
            await query.edit_message_text("❌ کاربر پیدا نشد!")
            return
        name = tu["first_name"]
        for table in ["users", "dogs", "hooks", "pending_bones", "bank", "transfers",
                      "user_strays", "jail", "factories", "sub_admins"]:
            conn.execute(f"DELETE FROM {table} WHERE user_id=?", (target_id,))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"🗑 *کاربر {name} از دیتابیس حذف شد!*\n\n"
            f"🪪 آیدی: `{target_id}`\n"
            f"تمام اطلاعاتش پاک شد.",
            parse_mode="Markdown"
        )
        return

    if data == "cancel":
        await query.edit_message_text("❌ عملیات لغو شد.")
        return

    if data.startswith("buydog_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        if u["hop_points"] < DOG_COST: conn.close(); await query.answer("پوینت کافی نداری!", show_alert=True); return
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (DOG_COST, uid))
        conn.execute("INSERT OR IGNORE INTO dogs (user_id,name,level,rank,points_box,last_collect) VALUES (?,'سگولو',1,1,0,?)", (uid, datetime.now().isoformat()))
        chat_id = query.message.chat_id
        conn.execute("UPDATE groups SET total_dogs=total_dogs+1 WHERE group_id=?", (chat_id,))
        conn.commit(); conn.close()
        await query.edit_message_text("🎉 *تبریک! سگت رو خریدی!*\n\n🐕 اسمش *سگولو* ه — می‌تونی عوضش کنی!\n🍖 یادت باشه هر ۲ ساعت استخوان بهش بدی!\n\n⏳ در حال بازگشت به پنل سگ...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await dog_cmd_for_user(query, uid)
        return

    if data.startswith("collect_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست! 🐕", show_alert=True); return
        conn = get_db()
        dog  = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
        if not dog: conn.close(); await query.answer("سگی نداری!", show_alert=True); return
        pending = calc_dog_points(dog) + dog["points_box"]
        if pending < 1: conn.close(); await query.answer("📦 جعبه خالیه!", show_alert=True); return
        amount = int(pending)
        conn.execute("UPDATE dogs SET points_box=0,last_collect=? WHERE user_id=?", (datetime.now().isoformat(), uid))
        conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (amount, uid))
        conn.commit(); conn.close()
        await query.edit_message_text(f"✅ *{amount:,}* هاپ پوینت از جعبه سگت برداشت شد! 🦴\n\n⏳ در حال بازگشت به پنل سگ...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await dog_cmd_for_user(query, uid)
        return

    if data.startswith("feed_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        dog  = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
        if not dog: conn.close(); await query.answer("سگی نداری!", show_alert=True); return
        if u["hop_points"] < BONE_COST: conn.close(); await query.answer(f"پوینت کافی نداری! لازم: {BONE_COST}", show_alert=True); return
        now       = datetime.now()
        fed_until = dog["fed_until"]
        duration  = calc_feed_duration("استخوان کوچیک 🦴", 0.2)
        new_fed   = (datetime.fromisoformat(fed_until) + timedelta(seconds=duration)) if (fed_until and datetime.fromisoformat(fed_until) > now) else (now + timedelta(seconds=duration))
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (BONE_COST, uid))
        conn.execute("UPDATE dogs SET fed_until=? WHERE user_id=?", (new_fed.isoformat(), uid))
        conn.commit(); conn.close()
        duration_min = duration // 60
        await query.edit_message_text(
            f"🍖 *استخوان کوچیک خریدی!*\n\n"
            f"💰 هزینه: {BONE_COST:,} هاپ پوینت\n"
            f"⏱️ مدت سیری: {duration_min} دقیقه\n"
            f"⏰ سگت تا {new_fed.strftime('%H:%M')} سیره! 🐕\n\n"
            f"⏳ در حال بازگشت به پنل سگ...",
            parse_mode="Markdown"
        )
        await asyncio.sleep(2)
        await dog_cmd_for_user(query, uid)
        return

    if data.startswith("upgradedog_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        u    = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        dog  = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
        if not dog or dog["level"] >= DOG_MAX_LEVEL: conn.close(); await query.answer("سگت ماکسه!", show_alert=True); return
        _, _, cost = DOG_LEVEL_DATA[dog["level"]]
        if u["hop_points"] < cost: conn.close(); await query.answer(f"پوینت کافی نداری! لازم: {cost:,}", show_alert=True); return
        new_level = dog["level"] + 1
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (cost, uid))
        conn.execute("UPDATE dogs SET level=? WHERE user_id=?", (new_level, uid))
        conn.commit(); conn.close()
        new_rate, new_cap, _ = DOG_LEVEL_DATA[new_level]
        await query.edit_message_text(f"⬆️ *سگت ارتقا پیدا کرد!*\n\n⭐️ سطح جدید: {new_level}\n⚡️ تولید جدید: {new_rate:.2f} هاپ‌پوینت/ثانیه\n📦 ظرفیت جدید: {new_cap:,}\n\n⏳ در حال بازگشت به پنل سگ...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await dog_cmd_for_user(query, uid)
        return

    if data.startswith("rankup_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        conn = get_db()
        dog  = conn.execute("SELECT * FROM dogs WHERE user_id=?", (uid,)).fetchone()
        if not dog or dog["rank"] >= 10: conn.close(); await query.answer("مقام سگت ماکسه!", show_alert=True); return
        if dog["level"] < dog["rank"] * 2: conn.close(); await query.answer(f"سگت باید حداقل سطح {dog['rank']*2} باشه!", show_alert=True); return
        new_rank = dog["rank"] + 1
        conn.execute("UPDATE dogs SET rank=?,level=1,points_box=0 WHERE user_id=?", (new_rank, uid))
        conn.commit(); conn.close()
        rank_name, rank_mult = DOG_RANKS[new_rank]
        await query.edit_message_text(f"🏅 *مقام ارتقا پیدا کرد!*\n\n🎖 مقام جدید: {rank_name}\n✨ ضریب تولید: {rank_mult}x\n\n⚠️ سطح سگ به ۱ برگشت اما قوی‌تر از قبله!\n\n⏳ در حال بازگشت به پنل سگ...", parse_mode="Markdown")
        await asyncio.sleep(2)
        await dog_cmd_for_user(query, uid)
        return

    if data.startswith("rename_"):
        uid = int(data.split("_")[1])
        if not check_owner(uid): await query.answer("این دکمه برای تو نیست!", show_alert=True); return
        context.user_data["waiting_rename"] = uid
        await query.edit_message_text("✏️ *اسم جدید سگت رو بنویس:*\n(حداکثر ۱۵ کاراکتر)", parse_mode="Markdown")
        return

async def handle_rename(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user    = update.effective_user
    waiting = context.user_data.get("waiting_rename")
    if not waiting or waiting != user.id: return False
    new_name = update.message.text.strip()
    if len(new_name) > 15:
        await update.message.reply_text("❌ اسم نباید بیشتر از ۱۵ کاراکتر باشه!")
        return True
    if len(new_name) < 1:
        await update.message.reply_text("❌ اسم نمی‌تونه خالی باشه!")
        return True
    conn = get_db()
    conn.execute("UPDATE dogs SET name=? WHERE user_id=?", (new_name, user.id))
    conn.commit(); conn.close()
    context.user_data.pop("waiting_rename", None)
    await update.message.reply_text(f"✅ اسم سگت به *{new_name}* تغییر پیدا کرد! 🐕", parse_mode="Markdown")
    class _FakeQuery:
        def __init__(self, msg): self.message = msg
    await dog_cmd_for_user(_FakeQuery(update.message), user.id)
    return True

async def bank_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🏦 بانک فقط توی گروه کار می‌کنه!")
        return

    jailed, jail_row = is_in_jail(user.id)
    if jailed:
        rel = datetime.fromisoformat(jail_row["release_at"])
        left = int((rel - datetime.now()).total_seconds())
        m, s = divmod(left, 60)
        await update.message.reply_text(
            f"⛓️ تو زندانی! نمی‌تونی به بانک دسترسی داشته باشی!\n"
            f"⌛️ {m} دقیقه و {s} ثانیه تا آزادی"
        )
        return

    conn = get_db()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    bank = conn.execute("SELECT * FROM bank WHERE user_id=?", (user.id,)).fetchone()
    conn.close()

    if not u:
        await update.message.reply_text("❌ اول توی گروه هاپ کن تا ثبت‌نام بشی!")
        return

    if u["level"] < BANK_MIN_LEVEL:
        await update.message.reply_text(
            f"🔒 برای باز کردن حساب بانکی باید حداقل سطح {BANK_MIN_LEVEL} باشی!\n"
            f"سطح فعلیت: {u['level']} ⭐️"
        )
        return

    if bank:
        await show_bank_panel(update, user, u, bank)
        return

    kb = InlineKeyboardMarkup([[
        cbtn(f"✅ افتتاح حساب ({BANK_OPEN_COST:,} پوینت)", callback_data=f"openbank_{user.id}"),
        cbtn("❌ انصراف", callback_data="cancel"),
    ]])
    await update.message.reply_text(
        f"🏦 *بانک هاپی*\n\n"
        f"💰 هزینه افتتاح: {BANK_OPEN_COST:,} هاپ پوینت\n"
        f"📈 سود روزانه: ۳٪ (حداکثر {BANK_MAX_INTEREST:,})\n"
        f"💳 شماره حساب ۱۲ رقمی اختصاصی\n"
        f"🔄 امکان کارت به کارت\n\n"
        f"موجودیت: {u['hop_points']:,.0f} 🦴",
        parse_mode="Markdown", reply_markup=kb
    )

async def show_bank_panel(update, user, u, bank):
    now = datetime.now()
    interest_ready = False
    if bank["last_interest"]:
        last_int = datetime.fromisoformat(bank["last_interest"])
        interest_ready = (now - last_int).total_seconds() >= 86400
    else:
        interest_ready = True

    interest_amount = min(bank["balance"] * BANK_INTEREST_RATE, BANK_MAX_INTEREST)

    text = (
        f"🏦 *بانک هاپی*\n\n"
        f"💳 شماره حساب: `{bank['account_number']}`\n"
        f"💰 موجودی بانک: {bank['balance']:,.0f} هاپ پوینت\n"
        f"👛 موجودی کیف: {u['hop_points']:,.0f} هاپ پوینت\n\n"
        f"📈 سود روزانه (۳٪): {interest_amount:,.0f}\n"
        f"{'✅ سود آماده دریافته!' if interest_ready else '⌛️ سود فردا قابل دریافته'}"
    )

    kb = [
        [cbtn("➕ واریز", callback_data=f"bank_deposit_{user.id}"),
         cbtn("➖ برداشت", callback_data=f"bank_withdraw_{user.id}")],
    ]
    if interest_ready and bank["balance"] > 0:
        kb.append([cbtn("💸 دریافت سود", callback_data=f"bank_interest_{user.id}")])
    kb.append([cbtn("🔄 تغییر شماره حساب", callback_data=f"bank_changenum_{user.id}")])

    await update.message.reply_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

def parse_amount(text: str) -> int:
    text = text.strip().replace(",", "").replace("_", "")
    multipliers = {
        "k": 1_000, "کی": 1_000, "کا": 1_000,
        "m": 1_000_000, "میل": 1_000_000,
    }
    for suffix, mult in multipliers.items():
        if text.lower().endswith(suffix):
            try:
                return int(float(text[:-len(suffix)]) * mult)
            except:
                return -1
    try:
        return int(float(text))
    except:
        return -1

async def transfer_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🧲 انتقال فقط توی گروه کار می‌کنه!")
        return

    jailed, _ = is_in_jail(user.id)
    if jailed:
        await update.message.reply_text("⛓️ تو زندانی! نمی‌تونی انتقال بدی!")
        return

    conn = get_db()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    conn.close()

    if not u or u["level"] < TRANSFER_MIN_LEVEL:
        await update.message.reply_text(f"🔒 برای انتقال باید حداقل سطح {TRANSFER_MIN_LEVEL} باشی!")
        return

    conn = get_db()
    tr = conn.execute("SELECT * FROM transfers WHERE user_id=?", (user.id,)).fetchone()
    conn.close()
    if tr and tr["last_transfer"]:
        diff = (datetime.now() - datetime.fromisoformat(tr["last_transfer"])).total_seconds()
        if diff < TRANSFER_COOLDOWN:
            left = int(TRANSFER_COOLDOWN - diff)
            await update.message.reply_text(f"⌛️ {left} ثانیه تا انتقال بعدی صبر کن!")
            return

    args = update.message.text.split()
    target_user = None
    amount = -1

    if update.message.reply_to_message:
        target_user = update.message.reply_to_message.from_user
        if len(args) >= 2:
            amount = parse_amount(args[1])
    elif len(args) >= 3:
        amount = parse_amount(args[1])
        username = args[2].lstrip("@")
        conn = get_db()
        row = conn.execute("SELECT * FROM users WHERE username=?", (username,)).fetchone()
        conn.close()
        if not row:
            await update.message.reply_text("❌ کاربری با این یوزرنیم پیدا نشد!")
            return
        class FakeUser:
            id = row["user_id"]
            first_name = row["first_name"]
        target_user = FakeUser()
    else:
        await update.message.reply_text(
            "📌 فرمت انتقال:\n"
            "`انتقال {مبلغ} @یوزرنیم`\n"
            "یا روی پیام کاربر ریپلای کن و بنویس:\n"
            "`انتقال {مبلغ}`",
            parse_mode="Markdown"
        )
        return

    if not target_user or target_user.id == user.id:
        await update.message.reply_text("❌ نمی‌تونی به خودت انتقال بدی!")
        return

    if amount <= 0:
        await update.message.reply_text("❌ مبلغ نامعتبره!")
        return

    if amount < TRANSFER_MIN:
        await update.message.reply_text(f"❌ حداقل مبلغ انتقال: {TRANSFER_MIN:,} هاپ پوینت")
        return

    if amount > TRANSFER_MAX:
        await update.message.reply_text(f"❌ حداکثر مبلغ انتقال: {TRANSFER_MAX:,} هاپ پوینت")
        return

    conn = get_db()
    target = conn.execute("SELECT * FROM users WHERE user_id=?", (target_user.id,)).fetchone()
    if not target or target["level"] < TRANSFER_MIN_LEVEL:
        conn.close()
        await update.message.reply_text(f"❌ کاربر مقصد باید حداقل سطح {TRANSFER_MIN_LEVEL} باشه!")
        return

    if u["hop_points"] < amount:
        conn.close()
        await update.message.reply_text(f"❌ موجودی کافی نداری!\nموجودی: {u['hop_points']:,.0f}")
        return

    conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (amount, user.id))
    conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (amount, target_user.id))
    conn.execute("""
        INSERT OR REPLACE INTO transfers (user_id, last_transfer) VALUES (?, ?)
    """, (user.id, datetime.now().isoformat()))
    conn.commit()
    conn.close()

    await update.message.reply_text(
        f"✅ *انتقال موفق!*\n\n"
        f"💸 {amount:,} هاپ پوینت به {target['first_name']} منتقل شد!\n"
        f"👛 موجودی جدیدت: {u['hop_points'] - amount:,.0f}",
        parse_mode="Markdown"
    )


def get_active_crisis(group_id: int):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM city_crises WHERE group_id=? AND status='active' ORDER BY id DESC LIMIT 1",
            (group_id,)
        ).fetchone()

def get_active_penalty(group_id: int):
    with db_conn() as conn:
        row = conn.execute(
            "SELECT * FROM city_crisis_penalties WHERE group_id=?", (group_id,)
        ).fetchone()
        if not row:
            return None
        if datetime.fromisoformat(row["expires_at"]) <= datetime.now():
            conn.execute("DELETE FROM city_crisis_penalties WHERE group_id=?", (group_id,))
            conn.commit()
            return None
        return row

def apply_crisis_penalty(group_id: int, crisis_key: str):
    c = CRISIS_TYPES[crisis_key]
    expires = (datetime.now() + timedelta(seconds=c["fix_cooldown"])).isoformat()
    with db_conn() as conn:
        conn.execute(
            """INSERT OR REPLACE INTO city_crisis_penalties
               (group_id, penalty_type, penalty_value, expires_at)
               VALUES (?,?,?,?)""",
            (group_id, c["penalty_type"], c["penalty_value"], expires)
        )
        conn.commit()

def resolve_crisis(crisis_id: int, decision: str, resolver_id: int = None):
    with db_conn() as conn:
        conn.execute(
            """UPDATE city_crises SET status=?, decision=?, resolved_by=?, resolved_at=?
               WHERE id=?""",
            ("resolved" if decision != "ignore" else "ignored",
             decision, resolver_id, datetime.now().isoformat(), crisis_id)
        )
        conn.commit()

def expire_old_crises():
    with db_conn() as conn:
        old = conn.execute(
            """SELECT * FROM city_crises WHERE status='active' AND expires_at <= datetime('now')"""
        ).fetchall()
        for row in old:
            conn.execute(
                "UPDATE city_crises SET status='ignored', decision='timeout' WHERE id=?",
                (row["id"],)
            )
            apply_crisis_penalty(row["group_id"], row["crisis_type"])
        if old:
            conn.commit()
    return old

def crisis_hop_penalty(group_id: int) -> int:
    p = get_active_penalty(group_id)
    if p and p["penalty_type"] == "hop_cooldown":
        return p["penalty_value"]
    return 0

def crisis_dog_multiplier(group_id: int) -> float:
    p = get_active_penalty(group_id)
    if not p:
        return 1.0
    if p["penalty_type"] == "dog_freeze":
        return 0.0
    if p["penalty_type"] == "dog_slow":
        return 0.70
    return 1.0

def crisis_factory_multiplier(group_id: int) -> float:
    p = get_active_penalty(group_id)
    if not p:
        return 1.0
    if p["penalty_type"] == "factory_freeze":
        return 0.0
    if p["penalty_type"] == "factory_slow":
        return 0.50
    return 1.0

async def trigger_city_crisis(update, context, group_id: int, grp: dict):
    if get_active_crisis(group_id):
        return
    if grp["treasury"] < CRISIS_MIN_TREASURY:
        return
    if random.random() > CRISIS_TRIGGER_CHANCE:
        return

    crisis_key = random.choice(list(CRISIS_TYPES.keys()))
    c = CRISIS_TYPES[crisis_key]
    max_cost = int(grp["treasury"] * c["treasury_cost"])
    expires_at = (datetime.now() + timedelta(seconds=c["timeout"])).isoformat()

    with db_conn() as conn:
        conn.execute(
            """INSERT INTO city_crises (group_id, crisis_type, status, expires_at)
               VALUES (?,?,'active',?)""",
            (group_id, crisis_key, expires_at)
        )
        conn.commit()
        crisis_id = conn.execute("SELECT last_insert_rowid()").fetchone()[0]

    mayor = get_mayor(group_id)
    mayor_mention = ""
    if mayor:
        mayor_mention = f"📣 شهردار @{mayor['username'] or mayor['first_name']} باید تصمیم بگیره!\n\n"

    kb = InlineKeyboardMarkup([
        [cbtn(f"💰 پرداخت از خزانه ({max_cost:,} هاپ)", callback_data=f"crisis_pay_{crisis_id}_{group_id}")],
        [cbtn("🏛 استفاده از منابع شهر", callback_data=f"crisis_resource_{crisis_id}_{group_id}")],
        [cbtn("😤 نادیده گرفتن (خطرناک!)", callback_data=f"crisis_ignore_{crisis_id}_{group_id}")],
    ])

    await update.message.reply_text(
        f"🚨 *بحران شهری!*\n\n"
        f"{c['name']}\n\n"
        f"📋 {c['desc']}\n\n"
        f"{mayor_mention}"
        f"⏳ *۱۰ دقیقه* فرصت تصمیم‌گیری دارید!\n\n"
        f"━━━━━━━━━━━━\n"
        f"✅ پرداخت از خزانه: حل کامل + {c['reward']:,} پاداش\n"
        f"🔧 استفاده از منابع: حل جزئی، بدون پاداش\n"
        f"❌ نادیده گرفتن: {c['penalty']}",
        parse_mode="Markdown",
        reply_markup=kb
    )

async def handle_crisis_callback(query, data, user, context) -> bool:
    if not data.startswith("crisis_"):
        return False

    parts = data.split("_")
    if len(parts) < 4:
        return False

    action = parts[1]
    crisis_id = int(parts[2])
    group_id = int(parts[3])

    with db_conn() as conn:
        crisis = conn.execute(
            "SELECT * FROM city_crises WHERE id=?", (crisis_id,)
        ).fetchone()

    if not crisis or crisis["status"] != "active":
        await query.answer("❌ این بحران دیگه فعال نیست!", show_alert=True)
        return True

    if datetime.fromisoformat(crisis["expires_at"]) <= datetime.now():
        resolve_crisis(crisis_id, "timeout")
        apply_crisis_penalty(group_id, crisis["crisis_type"])
        await query.edit_message_text(
            "⏰ *وقت تموم شد!*\n\nبحران مدیریت نشد و شهر جریمه گرفت.",
            parse_mode="Markdown"
        )
        return True

    mayor = get_mayor(group_id)
    c = CRISIS_TYPES[crisis["crisis_type"]]

    if action == "pay":
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار می‌تونه تصمیم بگیره!", show_alert=True)
            return True

        with db_conn() as conn:
            grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (group_id,)).fetchone()

        cost = int(grp["treasury"] * c["treasury_cost"])
        if grp["treasury"] < cost:
            await query.answer(f"❌ خزانه کافی نیست! لازم: {cost:,}", show_alert=True)
            return True

        with db_conn() as conn:
            conn.execute(
                "UPDATE groups SET treasury=treasury-? WHERE group_id=?", (cost, group_id)
            )
            conn.execute(
                "UPDATE users SET hop_points=hop_points+? WHERE user_id=?",
                (c["reward"], mayor["user_id"])
            )
            conn.commit()

        resolve_crisis(crisis_id, "pay", user.id)

        await query.edit_message_text(
            f"✅ *بحران مدیریت شد!*\n\n"
            f"{c['name']}\n\n"
            f"💰 {cost:,} هاپ از خزانه پرداخت شد.\n"
            f"🏆 شهردار {c['reward']:,} پاداش گرفت!\n\n"
            f"🏙 شهر دوباره آروم شد 🐾",
            parse_mode="Markdown"
        )
        try:
            await context.bot.send_message(
                group_id,
                f"🏛 *شهردار بحران رو مدیریت کرد!*\n\n{c['name']} حل شد.\n💰 {cost:,} از خزانه هزینه شد.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    if action == "resource":
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار می‌تونه تصمیم بگیره!", show_alert=True)
            return True

        half_penalty = c["fix_cooldown"] // 2
        expires = (datetime.now() + timedelta(seconds=half_penalty)).isoformat()
        with db_conn() as conn:
            conn.execute(
                """INSERT OR REPLACE INTO city_crisis_penalties
                   (group_id, penalty_type, penalty_value, expires_at)
                   VALUES (?,?,?,?)""",
                (group_id, c["penalty_type"], c["penalty_value"] // 2, expires)
            )
            conn.commit()

        resolve_crisis(crisis_id, "resource", user.id)

        mins = half_penalty // 60
        await query.edit_message_text(
            f"🔧 *بحران تا حدی کنترل شد!*\n\n"
            f"{c['name']}\n\n"
            f"📉 جریمه نصف شد: {mins} دقیقه اثر می‌ذاره.\n"
            f"💡 دفعه بعد از خزانه پرداخت کن تا کامل حل بشه!",
            parse_mode="Markdown"
        )
        return True

    if action == "ignore":
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار می‌تونه تصمیم بگیره!", show_alert=True)
            return True

        resolve_crisis(crisis_id, "ignore", user.id)
        apply_crisis_penalty(group_id, crisis["crisis_type"])

        mins = c["fix_cooldown"] // 60
        await query.edit_message_text(
            f"😤 *بحران نادیده گرفته شد!*\n\n"
            f"{c['name']}\n\n"
            f"⚠️ جریمه فعال شد: {c['penalty']}\n"
            f"⏳ مدت: {mins} دقیقه\n\n"
            f"دفعه بعد بهتر تصمیم بگیر! 🏛",
            parse_mode="Markdown"
        )
        return True

    return False

async def crisis_status_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🚨 این دستور فقط توی گروه کار می‌کنه!")
        return

    crisis = get_active_crisis(chat.id)
    penalty = get_active_penalty(chat.id)

    if not crisis and not penalty:
        await update.message.reply_text("✅ *شهر در آرامشه!* هیچ بحران یا جریمه‌ای فعال نیست 🏙", parse_mode="Markdown")
        return

    text = "🚨 *وضعیت بحران شهر*\n\n"

    if crisis:
        c = CRISIS_TYPES.get(crisis["crisis_type"], {})
        exp = datetime.fromisoformat(crisis["expires_at"])
        left = max(0, int((exp - datetime.now()).total_seconds()))
        m, s = divmod(left, 60)
        text += (
            f"🔴 *بحران فعال:*\n"
            f"{c.get('name', crisis['crisis_type'])}\n"
            f"⏳ {m} دقیقه و {s} ثانیه فرصت باقیه\n\n"
        )

    if penalty:
        exp_p = datetime.fromisoformat(penalty["expires_at"])
        left_p = max(0, int((exp_p - datetime.now()).total_seconds()))
        mp, sp = divmod(left_p, 60)
        penalty_labels = {
            "hop_cooldown": "🐾 کولداون هاپ بیشتر",
            "factory_freeze": "🏭 کارخانه متوقف",
            "factory_slow": "🏭 کارخانه کند",
            "dog_freeze": "🐕 سگ‌ها متوقف",
            "dog_slow": "🐕 سگ‌ها کند",
        }
        label = penalty_labels.get(penalty["penalty_type"], penalty["penalty_type"])
        text += (
            f"⚠️ *جریمه فعال:*\n"
            f"{label}\n"
            f"⏳ {mp} دقیقه و {sp} ثانیه مونده\n"
        )

    await update.message.reply_text(text, parse_mode="Markdown")

async def check_stray_dog(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat

    conn = get_db()
    grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    stray = conn.execute("SELECT * FROM stray_dogs WHERE group_id=?", (chat.id,)).fetchone()
    conn.close()

    if not grp:
        return

    if stray:
        return

    if grp["total_hops"] % STRAY_TRIGGER_HOPS != 0:
        return

    conn = get_db()
    conn.execute("""
        INSERT OR REPLACE INTO stray_dogs (group_id, tries_left, current_cost, appeared_at, rescuer_ids)
        VALUES (?, ?, ?, ?, '')
    """, (chat.id, STRAY_MAX_TRIES, STRAY_BASE_COST, datetime.now().isoformat()))
    conn.commit()
    conn.close()

    kb = InlineKeyboardMarkup([[
        cbtn(f"🐕 نجات ({STRAY_BASE_COST:,} پوینت)", callback_data=f"rescue_{chat.id}"),
    ]])
    await update.message.reply_text(
        f"🐕 *یه سگ خیابونی ظاهر شد!*\n\n"
        f"😿 این سگ بیچاره کنار خیابونه و به کمک نیاز داره!\n"
        f"🍀 شانس نجات: ۳۰٪\n"
        f"💰 هزینه تلاش: {STRAY_BASE_COST:,} هاپ پوینت\n"
        f"🔁 تعداد تلاش باقی‌مونده: {STRAY_MAX_TRIES}\n\n"
        f"کی اول نجاتش میده؟",
        parse_mode="Markdown", reply_markup=kb
    )

async def rescue_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    data = query.data

    group_id = int(data.split("_")[1])
    uid = query.from_user.id

    jailed, _ = is_in_jail(uid)
    if jailed:
        await query.answer("⛓️ تو زندانی! نمی‌تونی کمک کنی!", show_alert=True)
        return

    conn = get_db()
    stray = conn.execute("SELECT * FROM stray_dogs WHERE group_id=?", (group_id,)).fetchone()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()

    if not stray:
        conn.close()
        await query.answer("❌ این سگ دیگه اینجا نیست!", show_alert=True)
        return

    rescuers = stray["rescuer_ids"].split(",") if stray["rescuer_ids"] else []
    if str(uid) in rescuers:
        conn.close()
        await query.answer("❌ تو قبلاً تلاش کردی!", show_alert=True)
        return

    cost = stray["current_cost"]

    if not u or u["hop_points"] < cost:
        conn.close()
        await query.answer(f"❌ پوینت کافی نداری! لازم: {cost:,}", show_alert=True)
        return

    conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (cost, uid))

    success = random.random() < STRAY_CHANCE
    tries_left = stray["tries_left"] - 1
    rescuers.append(str(uid))
    new_cost = int(cost * STRAY_COST_MULT)

    if success:
        conn.execute("DELETE FROM stray_dogs WHERE group_id=?", (group_id,))
        conn.execute("""
            INSERT OR IGNORE INTO user_strays (user_id, count) VALUES (?, 0)
        """, (uid,))
        conn.execute("UPDATE user_strays SET count=count+1 WHERE user_id=?", (uid,))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"🎉 *{query.from_user.first_name} سگ رو نجات داد!*\n\n"
            f"🐕 سگ خیابونی الان در امنیته!\n"
            f"🏅 +۱ به آمار نجات سگت اضافه شد!",
            parse_mode="Markdown"
        )
    elif tries_left <= 0:
        conn.execute("DELETE FROM stray_dogs WHERE group_id=?", (group_id,))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"😢 *سگ خیابونی فرار کرد!*\n\n"
            f"متأسفانه هیچ‌کس نتونست این سگ رو نجات بده...\n"
            f"شاید دفعه بعد شانس بیشتری داشته باشید 🐾",
            parse_mode="Markdown",
            reply_markup=None
        )
    else:
        conn.execute("""
            UPDATE stray_dogs SET tries_left=?, current_cost=?, rescuer_ids=?
            WHERE group_id=?
        """, (tries_left, new_cost, ",".join(rescuers), group_id))
        conn.commit()
        conn.close()

        kb = InlineKeyboardMarkup([[
            cbtn(f"🐕 تلاش دوباره ({new_cost:,} پوینت)", callback_data=f"rescue_{group_id}"),
        ]])
        await query.edit_message_text(
            f"😿 *{query.from_user.first_name} موفق نشد!*\n\n"
            f"سگ هنوز اینجاست ولی ترسیده‌تره...\n"
            f"🔁 تلاش باقی‌مونده: {tries_left}\n"
            f"💰 هزینه بعدی: {new_cost:,} هاپ پوینت",
            parse_mode="Markdown", reply_markup=kb
        )

def is_admin(user_id: int) -> bool:
    return user_id in ADMIN_IDS

def is_sub_admin(user_id: int) -> bool:
    with db_conn() as conn:
        return conn.execute("SELECT 1 FROM sub_admins WHERE user_id=?", (user_id,)).fetchone() is not None

def is_any_admin(user_id: int) -> bool:
    return is_admin(user_id) or is_sub_admin(user_id)

async def add_admin_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if not is_admin(user.id):
        return
    if not update.message.reply_to_message:
        await update.message.reply_text("❌ باید روی پیام فرد موردنظر ریپلای کنی!")
        return
    target = update.message.reply_to_message.from_user
    if target.id in ADMIN_IDS:
        await update.message.reply_text("⚠️ این کاربر ادمین اصلیه!")
        return
    if is_sub_admin(target.id):
        await update.message.reply_text(f"⚠️ {target.first_name} قبلاً ادمین شده!")
        return
    ensure_user(target.id, target.username or "", target.first_name)
    with db_conn() as conn:
        conn.execute(
            "INSERT OR IGNORE INTO sub_admins (user_id, username, first_name, added_by) VALUES (?,?,?,?)",
            (target.id, target.username or "", target.first_name, user.id)
        )
        conn.commit()
    await update.message.reply_text(
        f"✅ *{target.first_name}* به عنوان ادمین اضافه شد!\n\n"
        f"🔑 دسترسی‌ها:\n"
        f"• افزایش/کاهش پوینت\n"
        f"• افزایش/کاهش لول",
        parse_mode="Markdown"
    )

async def remove_admin_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if not is_admin(user.id):
        return
    if not update.message.reply_to_message:
        await update.message.reply_text("❌ باید روی پیام فرد موردنظر ریپلای کنی!")
        return
    target = update.message.reply_to_message.from_user
    if target.id in ADMIN_IDS:
        await update.message.reply_text("❌ نمیشه ادمین اصلی رو حذف کرد!")
        return
    if not is_sub_admin(target.id):
        await update.message.reply_text(f"⚠️ {target.first_name} ادمین نیست!")
        return
    with db_conn() as conn:
        conn.execute("DELETE FROM sub_admins WHERE user_id=?", (target.id,))
        conn.commit()
    await update.message.reply_text(f"✅ دسترسی ادمین *{target.first_name}* حذف شد!", parse_mode="Markdown")

async def delete_user_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if not is_admin(user.id):
        return
    if not update.message.reply_to_message:
        await update.message.reply_text("❌ باید روی پیام کاربر موردنظر ریپلای کنی!")
        return
    target = update.message.reply_to_message.from_user
    if target.id in ADMIN_IDS:
        await update.message.reply_text("❌ نمیشه ادمین اصلی رو حذف کرد!")
        return
    tu = get_user(target.id)
    if not tu:
        await update.message.reply_text("❌ این کاربر در دیتابیس نیست!")
        return
    kb = InlineKeyboardMarkup([[
        cbtn("✅ بله، حذف کن", callback_data=f"confirm_delete_user_{target.id}"),
        cbtn("❌ انصراف", callback_data="cancel"),
    ]])
    await update.message.reply_text(
        f"⚠️ *آیا مطمئنی؟*\n\n"
        f"👤 کاربر: {target.first_name}\n"
        f"🪪 آیدی: `{target.id}`\n"
        f"💰 موجودی: {tu['hop_points']:,.0f}\n"
        f"⭐️ سطح: {tu['level']}\n\n"
        f"تمام اطلاعات این کاربر (پوینت، سگ، قلاب، بانک و ...) حذف میشه!",
        parse_mode="Markdown", reply_markup=kb
    )

async def admin_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if not is_any_admin(user.id):
        return
    if not update.message.reply_to_message:
        await update.message.reply_text(
            "❌ باید روی پیام فرد موردنظر ریپلای کنی!\n\n"
            "📋 دستورات:\n"
            "• افزایش پوینت [عدد]\n"
            "• کاهش پوینت [عدد]\n"
            "• افزایش لول [عدد]\n"
            "• کاهش لول [عدد]"
        )
        return
    target = update.message.reply_to_message.from_user
    tu = get_user(target.id)
    if not tu:
        await update.message.reply_text("❌ این کاربر هنوز ثبت‌نام نکرده!")
        return
    parts = update.message.text.strip().split()
    if len(parts) < 3:
        await update.message.reply_text("❌ عدد رو هم بنویس! مثلاً: افزایش پوینت 500")
        return
    try:
        amount = int(parts[2].replace(",", ""))
        if amount <= 0: raise ValueError
    except ValueError:
        await update.message.reply_text("❌ عدد نامعتبره!")
        return

    action = f"{parts[0]} {parts[1]}"
    conn = get_db()

    if action == "افزایش پوینت":
        conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (amount, target.id))
        conn.commit(); conn.close()
        await update.message.reply_text(
            f"✅ *{amount:,}* هاپ پوینت به *{target.first_name}* اضافه شد!\n"
            f"💰 موجودی جدید: {tu['hop_points'] + amount:,.0f}",
            parse_mode="Markdown"
        )
    elif action == "کاهش پوینت":
        conn.execute("UPDATE users SET hop_points=MAX(0,hop_points-?) WHERE user_id=?", (amount, target.id))
        conn.commit(); conn.close()
        await update.message.reply_text(
            f"✅ *{amount:,}* هاپ پوینت از *{target.first_name}* کم شد!\n"
            f"💰 موجودی جدید: {max(0, tu['hop_points'] - amount):,.0f}",
            parse_mode="Markdown"
        )
    elif action == "افزایش لول":
        new_lvl = min(1000, tu["level"] + amount)
        new_hops = LEVEL_THRESHOLDS[new_lvl - 2] if new_lvl >= 2 else 0
        conn.execute("UPDATE users SET level=?, total_hops=? WHERE user_id=?", (new_lvl, new_hops, target.id))
        conn.commit(); conn.close()
        await update.message.reply_text(
            f"⭐️ لول *{target.first_name}* از {tu['level']} به *{new_lvl}* رسید!",
            parse_mode="Markdown"
        )
    elif action == "کاهش لول":
        new_lvl = max(1, tu["level"] - amount)
        new_hops = LEVEL_THRESHOLDS[new_lvl - 2] if new_lvl >= 2 else 0
        conn.execute("UPDATE users SET level=?, total_hops=? WHERE user_id=?", (new_lvl, new_hops, target.id))
        conn.commit(); conn.close()
        await update.message.reply_text(
            f"⬇️ لول *{target.first_name}* از {tu['level']} به *{new_lvl}* رسید!",
            parse_mode="Markdown"
        )
    else:
        conn.close()

async def factory_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🏭 کارخونه فقط توی گروه کار می‌کنه!")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    u = get_user(user.id)
    if not u or u["level"] < FACTORY_MIN_LEVEL:
        await update.message.reply_text(
            f"🔒 *کارخونه میویی*\n\n"
            f"برای ساخت کارخونه باید حداقل *سطح {FACTORY_MIN_LEVEL}* باشی!\n"
            f"⭐️ سطح فعلیت: {u['level'] if u else 1}\n\n"
            f"💪 بیشتر میو بزن تا سطحت بالا بره!",
            parse_mode="Markdown"
        )
        return
    factory = get_factory(user.id)
    if not factory:
        kb = InlineKeyboardMarkup([[
            cbtn(
                f"🏗️ ساخت کارخونه ({FACTORY_BUILD_COST:,} پوینت)",
                callback_data=f"factory_build_{user.id}"
            ),
            cbtn("❌ انصراف", callback_data="cancel"),
        ]])
        await update.message.reply_text(
            f"🏭 *کارخونه میویی*\n\n"
            f"هنوز کارخونه نداری! 😿\n\n"
            f"💰 هزینه ساخت: {FACTORY_BUILD_COST:,} پوینت\n"
            f"👛 موجودیت: {u['hop_points']:,.0f}\n"
            f"🐕 کارگرهات (هاپوهای نجات‌داده): {get_stray_count_factory(user.id)}\n\n"
            f"✨ با کارخونه میتونی محصول تولید کنی و بفروشی!",
            parse_mode="Markdown", reply_markup=kb
        )
        return
    await show_factory_panel(update.message, user, u, factory)

async def show_factory_panel(message_obj, user, u, factory):
    workers = get_stray_count_factory(user.id)
    cap = WAREHOUSE_LEVELS[factory["warehouse_level"]][0]
    machine_cd = MACHINE_LEVELS[factory["machine_level"]][0]
    producing_status = "💤 بیکار"
    time_left_str = ""
    if check_production_done(factory):
        producing_status = "✅ محصول آماده جمع‌آوری!"
    elif factory["producing"] and factory["production_end"]:
        end = datetime.fromisoformat(factory["production_end"])
        left = int((end - datetime.now()).total_seconds())
        m, s = divmod(left, 60)
        producing_status = "⚙️ در حال تولید"
        time_left_str = f"\n└─ ⌛️ {m} دقیقه و {s} ثانیه تا آماده شدن"
    exp_needed = FACTORY_LEVEL_EXP.get(factory["level"], 0)
    if exp_needed > 0:
        filled = min(5, int((factory["exp"] / exp_needed) * 5))
        bar = "▰" * filled + "▱" * (5 - filled)
        exp_str = f"{factory['exp']}/{exp_needed} {bar}"
    else:
        exp_str = "MAX 🏆"
    product_name = FACTORY_PRODUCTS[factory["product_idx"]][0] if factory["producing"] else "—"
    text = (
        f"╮──「 🏭 کارخونه هاپویی 🐾 」\n\n"
        f"┐─ ⭐️ سطح کارخونه : {factory['level']}/{FACTORY_MAX_LEVEL}\n"
        f"└─ 📊 تجربه : {exp_str}\n\n"
        f"┐─ 🏗️ دستگاه : سطح {factory['machine_level']} | ⌛️ {machine_cd} ثانیه\n"
        f"┐─ 🧳 انبار : سطح {factory['warehouse_level']} | {factory['stock']}/{cap} محصول\n"
        f"└─ 🐕 کارگرها : {workers} هاپو\n\n"
        f"┐─ 🔄 وضعیت : {producing_status}\n"
        f"└─ 📦 محصول فعلی : {product_name}"
        f"{time_left_str}"
    )
    kb = []
    if check_production_done(factory):
        kb.append([cbtn(
            "📦 جمع‌آوری محصولات ✅", callback_data=f"factory_collect_{user.id}"
        )])
    kb.append([
        cbtn("🛒 تولید محصول", callback_data=f"factory_produce_{user.id}"),
        cbtn("💹 فروش در بازار", callback_data=f"factory_sell_{user.id}"),
    ])
    kb.append([
        cbtn("⬆️ ارتقا دستگاه", callback_data=f"factory_upgrade_machine_{user.id}"),
        cbtn("⬆️ ارتقا انبار",   callback_data=f"factory_upgrade_warehouse_{user.id}"),
    ])
    kb.append([
        cbtn("🐕 استخدام هاپوی خیابونی", callback_data=f"factory_hire_{user.id}"),
        cbtn(f"💰 خرید کارگر ({FACTORY_WORKER_COST:,})", callback_data=f"factory_buy_worker_{user.id}"),
    ])
    await message_obj.reply_text(text, parse_mode="Markdown",
                                  reply_markup=InlineKeyboardMarkup(kb))

async def factory_callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    query = update.callback_query
    data  = query.data
    uid_self = query.from_user.id

    def check_owner(uid):
        return uid_self == uid

    if data.startswith("factory_build_"):
        uid = int(data.split("_")[2])
        if not check_owner(uid):
            await query.answer("این دکمه برای تو نیست!", show_alert=True); return True
        conn = get_db()
        u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        if u["hop_points"] < FACTORY_BUILD_COST:
            conn.close()
            await query.answer(f"❌ پوینت کافی نداری! لازم: {FACTORY_BUILD_COST:,}", show_alert=True); return True
        if conn.execute("SELECT user_id FROM factories WHERE user_id=?", (uid,)).fetchone():
            conn.close()
            await query.answer("❌ قبلاً کارخونه ساختی!", show_alert=True); return True
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (FACTORY_BUILD_COST, uid))
        conn.execute("""
            INSERT INTO factories (user_id,level,exp,warehouse_level,machine_level,stock,producing,product_idx)
            VALUES (?,1,0,1,1,0,0,0)
        """, (uid,))
        conn.commit(); conn.close()
        await query.answer("✅ کارخونه ساخته شد!")
        await query.edit_message_text(
            f"🎉 *کارخونه‌ات ساخته شد!*\n\n"
            f"🏭 حالا می‌تونی محصول تولید کنی!\n"
            f"🐕 هر هاپوی خیابونی که نجات بدی = یه کارگر بیشتر\n"
            f"💰 یا با هاپ پوینت ({FACTORY_WORKER_COST:,} تا) کارگر بخر!\n\n"
            f"📌 بنویس *کارخونه* تا پنل رو ببینی",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_produce_"):
        uid = int(data.split("_")[2])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        if not factory:
            await query.answer("❌ کارخونه نداری!", show_alert=True); return True
        if factory["producing"] and factory["production_end"]:
            end = datetime.fromisoformat(factory["production_end"])
            if datetime.now() < end:
                left = int((end - datetime.now()).total_seconds())
                m, s = divmod(left, 60)
                await query.answer(f"⚙️ کارخونه داره کار می‌کنه! {m} دقیقه و {s} ثانیه مونده.", show_alert=True)
                return True
        if check_production_done(factory):
            await query.answer("📦 اول محصولات آماده رو جمع‌آوری کن!", show_alert=True)
            return True
        workers = get_stray_count_factory(uid)
        if workers == 0:
            await query.answer("❌ هیچ کارگری نداری! از دکمه «استخدام» هاپوی خیابونی بیار یا با پوینت بخر 🐕", show_alert=True)
            return True
        available = get_available_products(factory["level"])
        kb = []
        for idx, product in available:
            name, cost, base_price, min_lvl, exp_gain = product
            market_p, mult = get_market_price(idx)
            trend = "📈" if mult >= 1.2 else ("📉" if mult <= 0.75 else "➡️")
            kb.append([cbtn(
                f"{name} | {cost:,}🪙 | {market_p:,}{trend}",
                callback_data=f"factory_start_{uid}_{idx}"
            )])
        kb.append([cbtn("❌ انصراف", callback_data="cancel")])
        await query.answer()
        await query.edit_message_text(
            f"🛒 *انتخاب محصول برای تولید*\n\n"
            f"🐈 کارگران: {workers} | هر دوره {workers} محصول\n"
            f"⌛️ زمان هر دوره: {MACHINE_LEVELS[factory['machine_level']][0]} ثانیه\n\n"
            f"📊 قیمت‌های بازار هر ساعت عوض میشن!",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb)
        )
        return True

    if data.startswith("factory_start_"):
        parts = data.split("_")
        uid = int(parts[2])
        product_idx = int(parts[3])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        u = get_user(uid)
        name, cost, base_price, min_lvl, exp_gain = FACTORY_PRODUCTS[product_idx]
        if u["hop_points"] < cost:
            await query.answer(f"❌ پوینت کافی نداری! هزینه: {cost:,}", show_alert=True); return True
        cap = WAREHOUSE_LEVELS[factory["warehouse_level"]][0]
        if factory["stock"] >= cap:
            await query.answer("❌ انبارت پره! اول بفروش!", show_alert=True); return True
        machine_cd = MACHINE_LEVELS[factory["machine_level"]][0]
        production_end = (datetime.now() + timedelta(seconds=machine_cd)).isoformat()
        conn = get_db()
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (cost, uid))
        conn.execute("""
            UPDATE factories SET producing=1, product_idx=?, production_end=?, last_produced=?
            WHERE user_id=?
        """, (product_idx, production_end, datetime.now().isoformat(), uid))
        conn.commit(); conn.close()
        workers = get_stray_count_factory(uid)
        m2, s2 = divmod(machine_cd, 60)
        await query.answer("⚙️ تولید شروع شد!")
        await query.edit_message_text(
            f"⚙️ *تولید شروع شد!*\n\n"
            f"📦 محصول: {name}\n"
            f"💰 هزینه: {cost:,} پوینت\n"
            f"🐈 کارگران: {workers}\n"
            f"⌛️ زمان دوره: {m2} دقیقه و {s2} ثانیه\n\n"
            f"بنویس *کارخونه* تا محصولات رو جمع‌آوری کنی!",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_collect_"):
        uid = int(data.split("_")[2])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        if not factory or not check_production_done(factory):
            await query.answer("⌛️ هنوز تولید تموم نشده!", show_alert=True); return True
        added = collect_production(uid)
        factory = get_factory(uid)
        product_name = FACTORY_PRODUCTS[factory["product_idx"]][0]
        await query.answer(f"📦 {added} محصول جمع‌آوری شد!")
        await query.edit_message_text(
            f"📦 *{added} عدد {product_name} به انبار اضافه شد!*\n\n"
            f"🧳 موجودی انبار: {factory['stock']}/{WAREHOUSE_LEVELS[factory['warehouse_level']][0]}\n"
            f"⭐️ سطح کارخونه: {factory['level']}\n\n"
            f"💹 بنویس *کارخونه* ← فروش در بازار",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_sell_"):
        uid = int(data.split("_")[2])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        if not factory:
            await query.answer("❌ کارخونه نداری!", show_alert=True); return True
        if factory["stock"] <= 0:
            await query.answer("❌ انبارت خالیه! اول محصول تولید کن.", show_alert=True); return True
        market_price, mult = get_market_price(factory["product_idx"])
        total_earn = market_price * factory["stock"]
        name = FACTORY_PRODUCTS[factory["product_idx"]][0]
        trend = "📈 بازار داغه!" if mult >= 1.5 else ("📉 بازار سرده..." if mult <= 0.7 else "➡️ بازار معمولیه")
        with db_conn() as conn:
            mp_row = conn.execute(
                "SELECT updated_at FROM market_prices WHERE product_idx=?", (factory["product_idx"],)
            ).fetchone()
        time_left_market = ""
        if mp_row and mp_row["updated_at"]:
            diff = (datetime.now() - datetime.fromisoformat(mp_row["updated_at"])).total_seconds()
            left = max(0, int(3600 - diff))
            m2, s2 = divmod(left, 60)
            time_left_market = f"\n🕐 قیمت تا {m2} دقیقه دیگه ثابته"
        kb = InlineKeyboardMarkup([[
            cbtn(
                f"✅ فروش همه ({total_earn:,} پوینت)",
                callback_data=f"factory_confirm_sell_{uid}"
            ),
            cbtn("❌ صبر می‌کنم", callback_data="cancel"),
        ]])
        await query.answer()
        await query.edit_message_text(
            f"💹 *بازار میویی*\n\n"
            f"📦 محصول: {name}\n"
            f"🧳 موجودی: {factory['stock']} عدد\n\n"
            f"💰 قیمت هر عدد: {market_price:,} (×{mult})\n"
            f"🤑 درآمد کل: {total_earn:,} پوینت\n"
            f"{trend}{time_left_market}\n\n"
            f"⚠️ قیمت‌ها هر ساعت عوض میشن!",
            parse_mode="Markdown", reply_markup=kb
        )
        return True

    if data.startswith("factory_confirm_sell_"):
        uid = int(data.split("_")[3])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        if not factory or factory["stock"] <= 0:
            await query.answer("❌ انباری برای فروش نیست!", show_alert=True); return True
        market_price, mult = get_market_price(factory["product_idx"])
        total_earn = market_price * factory["stock"]
        product_name = FACTORY_PRODUCTS[factory["product_idx"]][0]
        sold_count = factory["stock"]

        today = datetime.now().strftime("%Y-%m-%d")
        with db_conn() as conn:
            sold_today_row = conn.execute(
                "SELECT COALESCE(SUM(amount),0) as total FROM transactions "
                "WHERE user_id=? AND type='factory_sell' AND DATE(created_at)=?",
                (uid, today)
            ).fetchone()
            sold_today = sold_today_row["total"] if sold_today_row else 0

        remaining_cap = max(0, FACTORY_DAILY_CAP - sold_today)
        if remaining_cap <= 0:
            await query.answer(f"❌ سقف فروش روزانه ({FACTORY_DAILY_CAP:,}) تموم شده! فردا بیا.", show_alert=True)
            return True
        if total_earn > remaining_cap:
            ratio = remaining_cap / total_earn
            sold_count = max(1, int(factory["stock"] * ratio))
            total_earn = market_price * sold_count

        tax = int(total_earn * FACTORY_SELL_TAX)
        net_earn = total_earn - tax

        with db_conn() as conn:
            conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (net_earn, uid))
            conn.execute("UPDATE factories SET stock=stock-? WHERE user_id=?", (sold_count, uid))
            conn.execute(
                "INSERT INTO transactions (user_id, type, amount, created_at) VALUES (?,?,?,?)",
                (uid, "factory_sell", total_earn, datetime.now().isoformat())
            )
            conn.commit()

        remaining_after = remaining_cap - total_earn
        cap_msg = f"\n📊 باقی‌مونده سقف امروز: {remaining_after:,}" if remaining_after > 0 else "\n🔴 سقف فروش امروز تموم شد!"
        profit_msg = "🤑 سود خوبی کردی!" if mult >= 1.2 else ("😿 ضرر کردی..." if mult < 1.0 else "😊 معامله منصفانه‌ای بود!")
        await query.answer(f"✅ {sold_count} عدد فروخته شد!")
        await query.edit_message_text(
            f"✅ *فروش انجام شد!*\n\n"
            f"📦 {sold_count} عدد {product_name} فروخته شد\n"
            f"💰 درآمد کل: {total_earn:,}\n"
            f"🏛 مالیات ۱۰٪: -{tax:,}\n"
            f"💵 خالص دریافتی: +{net_earn:,}\n"
            f"📊 ضریب بازار: ×{mult}\n\n"
            f"{profit_msg}{cap_msg}",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_upgrade_machine_"):
        uid = int(data.split("_")[3])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        u = get_user(uid)
        if factory["machine_level"] >= MACHINE_MAX_LEVEL:
            await query.answer("🏆 دستگاهت ماکسه!", show_alert=True); return True
        next_lvl = factory["machine_level"] + 1
        _, next_cost = MACHINE_LEVELS[next_lvl]
        if u["hop_points"] < next_cost:
            await query.answer(f"❌ پوینت کافی نداری! لازم: {next_cost:,}", show_alert=True); return True
        new_cd = MACHINE_LEVELS[next_lvl][0]
        conn = get_db()
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (next_cost, uid))
        conn.execute("UPDATE factories SET machine_level=? WHERE user_id=?", (next_lvl, uid))
        conn.commit(); conn.close()
        await query.answer(f"⬆️ دستگاه به سطح {next_lvl} ارتقا پیدا کرد!")
        await query.edit_message_text(
            f"⬆️ *دستگاه ارتقا پیدا کرد!*\n\n"
            f"⭐️ سطح جدید: {next_lvl}/{MACHINE_MAX_LEVEL}\n"
            f"⌛️ زمان تولید جدید: {new_cd} ثانیه\n\n"
            f"🏭 کارخونه‌ات سریع‌تر شد!",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_upgrade_warehouse_"):
        uid = int(data.split("_")[3])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        factory = get_factory(uid)
        u = get_user(uid)
        if factory["warehouse_level"] >= WAREHOUSE_MAX_LEVEL:
            await query.answer("🏆 انبارت ماکسه!", show_alert=True); return True
        next_wh_lvl = factory["warehouse_level"] + 1
        _, upgrade_cost = WAREHOUSE_LEVELS[next_wh_lvl]
        if u["hop_points"] < upgrade_cost:
            await query.answer(f"❌ پوینت کافی نداری! لازم: {upgrade_cost:,}", show_alert=True); return True
        new_cap = WAREHOUSE_LEVELS[next_wh_lvl][0]
        conn = get_db()
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (upgrade_cost, uid))
        conn.execute("UPDATE factories SET warehouse_level=? WHERE user_id=?", (next_wh_lvl, uid))
        conn.commit(); conn.close()
        await query.answer(f"⬆️ انبار به سطح {next_wh_lvl} ارتقا پیدا کرد!")
        await query.edit_message_text(
            f"⬆️ *انبار ارتقا پیدا کرد!*\n\n"
            f"⭐️ سطح جدید: {next_wh_lvl}/{WAREHOUSE_MAX_LEVEL}\n"
            f"🧳 ظرفیت جدید: {new_cap} محصول\n\n"
            f"📦 حالا می‌تونی بیشتر ذخیره کنی!",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_hire_") and not data.startswith("factory_hire_confirm_"):
        uid = int(data.split("_")[2])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        with db_conn() as conn:
            row = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (uid,)).fetchone()
        stray_count = row["count"] if row else 0
        if stray_count <= 0:
            await query.answer("❌ هیچ هاپوی خیابونی نداری!\nاول با دستور «هاپو» هاپوی خیابونی نجات بده.", show_alert=True)
            return True
        kb = []
        row_btns = []
        for n in range(1, min(stray_count, 10) + 1):
            row_btns.append(cbtn(
                f"🐈 {n} کارگر", callback_data=f"factory_hire_confirm_{uid}_{n}"
            ))
            if len(row_btns) == 3:
                kb.append(row_btns)
                row_btns = []
        if row_btns:
            kb.append(row_btns)
        kb.append([cbtn("❌ انصراف", callback_data="cancel")])
        await query.answer()
        await query.edit_message_text(
            f"🐕 *استخدام هاپوی خیابونی*\n\n"
            f"هاپوهای خیابونی موجود: *{stray_count}* تا\n\n"
            f"چند تا می‌خوای به کارخونه بیاری؟\n"
            f"_(هر کارگر = یه محصول بیشتر در هر دوره تولید)_",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb)
        )
        return True

    if data.startswith("factory_hire_confirm_"):
        parts = data.split("_")
        uid = int(parts[3])
        n = int(parts[4])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        with db_conn() as conn:
            row = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (uid,)).fetchone()
            stray_count = row["count"] if row else 0
            if stray_count < n:
                await query.answer("❌ هاپوی خیابونی کافی نداری!", show_alert=True); return True
            conn.execute(
                "UPDATE user_strays SET count = count - ? WHERE user_id=?", (n, uid)
            )
            conn.commit()
        await query.answer(f"✅ {n} کارگر استخدام شد!")
        await query.edit_message_text(
            f"✅ *{n} هاپوی خیابونی استخدام شد!*\n\n"
            f"🐕 این هاپوها الان توی کارخونه‌ات کار می‌کنن\n"
            f"⚙️ هر دوره تولید، {n} محصول بیشتر می‌گیری!\n\n"
            f"بنویس *کارخونه* تا پنل رو ببینی.",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("factory_buy_worker_") and not data.startswith("factory_buy_worker_confirm_"):
        uid = int(data.split("_")[3])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        u = get_user(uid)
        kb = []
        row_btns = []
        for n in [1, 2, 3, 5, 10]:
            total = n * FACTORY_WORKER_COST
            if u["hop_points"] >= total:
                row_btns.append(cbtn(
                    f"🐕 {n} نفر ({total:,})",
                    callback_data=f"factory_buy_worker_confirm_{uid}_{n}"
                ))
            if len(row_btns) == 2:
                kb.append(row_btns)
                row_btns = []
        if row_btns:
            kb.append(row_btns)
        if not kb:
            await query.answer(f"❌ پوینت کافی نداری! حداقل {FACTORY_WORKER_COST:,} لازمه.", show_alert=True)
            return True
        kb.append([cbtn("❌ انصراف", callback_data="cancel")])
        await query.answer()
        await query.edit_message_text(
            f"💰 *خرید کارگر با هاپ پوینت*\n\n"
            f"قیمت هر کارگر: {FACTORY_WORKER_COST:,} هاپ پوینت\n"
            f"👛 موجودی: {u['hop_points']:,.0f}\n\n"
            f"چند تا کارگر میخوای بخری؟",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb)
        )
        return True

    if data.startswith("factory_buy_worker_confirm_"):
        parts = data.split("_")
        uid = int(parts[4])
        n = int(parts[5])
        if not check_owner(uid):
            await query.answer("❌ این دکمه برای تو نیست!", show_alert=True); return True
        total = n * FACTORY_WORKER_COST
        u = get_user(uid)
        if u["hop_points"] < total:
            await query.answer(f"❌ پوینت کافی نداری! لازم: {total:,}", show_alert=True); return True
        conn = get_db()
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (total, uid))
        conn.execute("INSERT OR IGNORE INTO user_strays (user_id, count) VALUES (?, 0)", (uid,))
        conn.execute("UPDATE user_strays SET count=count+? WHERE user_id=?", (n, uid))
        conn.commit(); conn.close()
        await query.answer(f"✅ {n} کارگر خریداری شد!")
        await query.edit_message_text(
            f"✅ *{n} کارگر جدید به کارخونه‌ات اضافه شد!*\n\n"
            f"💰 هزینه: {total:,} هاپ پوینت\n"
            f"🐕 الان توی کارخونه‌ات کار می‌کنن\n"
            f"⚙️ هر دوره تولید، {n} محصول بیشتر می‌گیری!\n\n"
            f"بنویس *کارخونه* تا پنل رو ببینی.",
            parse_mode="Markdown"
        )
        return True

    return False

async def jail_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user

    if update.message and update.message.text and user.id in ADMIN_IDS:
        parts = update.message.text.strip().split()
        target = None
        mins = 30

        if update.message.reply_to_message:
            reply_user = update.message.reply_to_message.from_user
            mins_str = parts[1] if len(parts) >= 2 and parts[1].isdigit() else "30"
            mins = int(mins_str)
            with db_conn() as conn:
                target = conn.execute(
                    "SELECT * FROM users WHERE user_id=?", (reply_user.id,)
                ).fetchone()
            if not target:
                jail_user(reply_user.id, reason="زندان توسط ادمین", duration_seconds=mins*60)
                await update.message.reply_text(
                    f"⛓️ *{reply_user.first_name} به زندان فرستاده شد!*\n⌛️ مدت: {mins} دقیقه",
                    parse_mode="Markdown"
                )
                return

        elif len(parts) >= 2:
            target_username = parts[1].lstrip("@")
            mins = int(parts[2]) if len(parts) >= 3 and parts[2].isdigit() else 30
            with db_conn() as conn:
                target = conn.execute(
                    "SELECT * FROM users WHERE username=? OR CAST(user_id AS TEXT)=?",
                    (target_username, target_username)
                ).fetchone()
            if not target:
                await update.message.reply_text("❌ کاربر پیدا نشد!")
                return

        if target:
            jail_user(target["user_id"], reason="زندان توسط ادمین", duration_seconds=mins*60)
            await update.message.reply_text(
                f"⛓️ *{target['first_name']} به زندان فرستاده شد!*\n⌛️ مدت: {mins} دقیقه",
                parse_mode="Markdown"
            )
            return

    jailed, jail_row = is_in_jail(user.id)
    if not jailed:
        await update.message.reply_text("✅ تو آزادی! توی زندان نیستی 🐕")
        return

    rel = datetime.fromisoformat(jail_row["release_at"])
    left_sec = max(0, int((rel - datetime.now()).total_seconds()))
    m, s = divmod(left_sec, 60)
    bail_cost = int((left_sec / 60) * BAIL_COST_PER_MIN)
    work_pts = jail_row["work_points"] if "work_points" in jail_row.keys() else 0

    with db_conn() as conn:
        u = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()

    kb = InlineKeyboardMarkup([
        [cbtn(f"💸 آزادی ({bail_cost:,} پوینت)", callback_data=f"bail_{user.id}"),
         cbtn("🗝 فرار!", callback_data=f"jail_escape_{user.id}")],
        [cbtn(f"⛏ کار در زندان (+{JAIL_WORK_EARN} پوینت)", callback_data=f"jail_work_{user.id}")],
    ])

    await update.message.reply_text(
        f"⛓️ *تو زندانی!*\n\n"
        f"📌 دلیل: {jail_row['reason']}\n"
        f"⌛️ زمان آزادی: {m} دقیقه و {s} ثانیه دیگه\n"
        f"💸 آزادی با پوینت: {bail_cost:,}\n"
        f"⛏ پوینت جمع‌شده از کار: {work_pts:,}\n"
        f"👛 موجودی: {u['hop_points']:,.0f}\n\n"
        f"🗝 فرار: ۲۵٪ شانس — اگه گرفتن، +۱۵ دقیقه اضافه میشه!",
        parse_mode="Markdown", reply_markup=kb
    )

async def new_callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data

    if data.startswith("smuggle_"):
        parts = data.split("_")
        count = int(parts[1])
        uid   = int(parts[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        jailed, _ = is_in_jail(uid)
        if jailed:
            await query.answer("⛓️ الان زندانی هستی!", show_alert=True)
            return True
        conn = get_db()
        row = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (uid,)).fetchone()
        stray_count = row["count"] if row else 0
        if stray_count < count:
            conn.close()
            await query.answer("❌ پیشی خیابونی کافی نداری!", show_alert=True)
            return True
        catch_chance = SMUGGLE_BASE_CATCH + (count * SMUGGLE_CATCH_PER)
        caught = random.random() < catch_chance
        if caught:
            now = datetime.now()
            release = (now + timedelta(minutes=SMUGGLE_JAIL_MINS)).isoformat()
            conn.execute("INSERT OR REPLACE INTO jail (user_id,jailed_at,release_at,reason) VALUES (?,?,?,?)",
                        (uid, now.isoformat(), release, f"قاچاق {count} سگ خیابونی"))
            conn.commit(); conn.close()
            await query.edit_message_text(
                f"🚔 *لو رفتی!*\n\n"
                f"پلیس هاپو {count} تا از سگ‌هات رو مصادره کرد!\n"
                f"⛓️ {SMUGGLE_JAIL_MINS} دقیقه زندان منتظرته...",
                parse_mode="Markdown"
            )
        else:
            reward = count * SMUGGLE_REWARD_EACH
            conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (reward, uid))
            conn.commit(); conn.close()
            await query.edit_message_text(
                f"🥷 *محموله سالم رسید!*\n\n"
                f"🐕 {count} تا سگ قاچاق شدن!\n"
                f"💰 +{reward:,} هاپ پوینت به جیبت رفت!\n"
                f"⚠️ شانس لو رفتن بود: {catch_chance*100:.0f}٪",
                parse_mode="Markdown"
            )
        return True

    if data.startswith("openbank_"):
        uid = int(data.split("_")[1])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        conn = get_db()
        u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        if u["hop_points"] < BANK_OPEN_COST:
            conn.close()
            await query.answer(f"پوینت کافی نداری! لازم: {BANK_OPEN_COST:,}", show_alert=True)
            return True
        import random as _r
        acc_num = "".join([str(_r.randint(0, 9)) for _ in range(12)])
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (BANK_OPEN_COST, uid))
        conn.execute("""
            INSERT OR IGNORE INTO bank (user_id, balance, account_number, last_interest)
            VALUES (?, 0, ?, NULL)
        """, (uid, acc_num))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"🎉 *حساب بانکی افتتاح شد!*\n\n"
            f"💳 شماره حساب: `{acc_num}`\n"
            f"📈 سود روزانه ۳٪ از موجودی\n\n"
            f"برای مدیریت بانکت بنویس *بانک*",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("bank_deposit_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        context.user_data["bank_action"] = ("deposit", uid)
        await query.edit_message_text(
            "➕ *چقدر می‌خوای واریز کنی؟*\n"
            "مبلغ رو بنویس (مثلاً: 1000 یا 5k):",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("bank_withdraw_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        context.user_data["bank_action"] = ("withdraw", uid)
        await query.edit_message_text(
            "➖ *چقدر می‌خوای برداشت کنی؟*\n"
            "مبلغ رو بنویس (مثلاً: 1000 یا 5k):",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("bank_interest_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        conn = get_db()
        bank = conn.execute("SELECT * FROM bank WHERE user_id=?", (uid,)).fetchone()
        if not bank:
            conn.close()
            await query.answer("حساب بانکی نداری!", show_alert=True)
            return True
        if bank["last_interest"]:
            diff = (datetime.now() - datetime.fromisoformat(bank["last_interest"])).total_seconds()
            if diff < 86400:
                conn.close()
                left = int(86400 - diff)
                h, r = divmod(left, 3600)
                m2, s2 = divmod(r, 60)
                await query.answer(f"⌛️ {h}:{m2:02d}:{s2:02d} تا سود بعدی", show_alert=True)
                return True
        interest = min(bank["balance"] * BANK_INTEREST_RATE, BANK_MAX_INTEREST)
        if interest < 1:
            conn.close()
            await query.answer("موجودی خیلی کمه!", show_alert=True)
            return True
        interest = int(interest)
        conn.execute("UPDATE bank SET balance=balance+?, last_interest=? WHERE user_id=?",
                     (interest, datetime.now().isoformat(), uid))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"💸 *سود دریافت شد!*\n\n"
            f"📈 +{interest:,} هاپ پوینت به بانکت اضافه شد!\n"
            f"📅 سود بعدی: ۲۴ ساعت دیگه\n\n⏳ در حال بازگشت به پنل بانک...",
            parse_mode="Markdown"
        )
        await asyncio.sleep(2)
        await bank_cmd_for_user(query, uid)
        return True

    if data.startswith("bank_changenum_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        conn = get_db()
        bank = conn.execute("SELECT * FROM bank WHERE user_id=?", (uid,)).fetchone()
        u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        if bank["last_num_change"]:
            diff = (datetime.now() - datetime.fromisoformat(bank["last_num_change"])).total_seconds()
            if diff < BANK_NUM_CHANGE_CD:
                conn.close()
                left = int(BANK_NUM_CHANGE_CD - diff)
                h2, r2 = divmod(left, 3600)
                await query.answer(f"⌛️ {h2} ساعت دیگه میتونی عوض کنی", show_alert=True)
                return True
        if u["hop_points"] < BANK_NUM_CHANGE_COST:
            conn.close()
            await query.answer(f"پوینت کافی نداری! لازم: {BANK_NUM_CHANGE_COST:,}", show_alert=True)
            return True
        import random as _r2
        new_num = "".join([str(_r2.randint(0, 9)) for _ in range(12)])
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (BANK_NUM_CHANGE_COST, uid))
        conn.execute("UPDATE bank SET account_number=?, last_num_change=? WHERE user_id=?",
                     (new_num, datetime.now().isoformat(), uid))
        conn.commit()
        conn.close()
        await query.edit_message_text(
            f"🔄 *شماره حساب تغییر کرد!*\n\n"
            f"💳 شماره جدید: `{new_num}`\n\n⏳ در حال بازگشت به پنل بانک...",
            parse_mode="Markdown"
        )
        await asyncio.sleep(2)
        await bank_cmd_for_user(query, uid)
        return True

    if data.startswith("bail_"):
        uid = int(data.split("_")[1])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        jailed, jail_row = is_in_jail(uid)
        if not jailed:
            await query.answer("تو آزادی!", show_alert=True)
            return True
        rel = datetime.fromisoformat(jail_row["release_at"])
        left_sec = int((rel - datetime.now()).total_seconds())
        bail_cost = int((left_sec / 60) * BAIL_COST_PER_MIN)
        conn = get_db()
        u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
        if u["hop_points"] < bail_cost:
            conn.close()
            await query.answer(f"پوینت کافی نداری! لازم: {bail_cost:,}", show_alert=True)
            return True
        jail_row2 = conn.execute("SELECT work_points FROM jail WHERE user_id=?", (uid,)).fetchone()
        work_pts = jail_row2["work_points"] if jail_row2 and jail_row2["work_points"] else 0
        conn.execute("UPDATE users SET hop_points=hop_points-?+? WHERE user_id=?", (bail_cost, work_pts, uid))
        conn.execute("DELETE FROM jail WHERE user_id=?", (uid,))
        conn.commit()
        conn.close()
        msg = (
            f"🎉 *آزاد شدی!*\n\n"
            f"💸 {bail_cost:,} هاپ پوینت پرداخت کردی\n"
        )
        if work_pts > 0:
            msg += f"⛏ +{work_pts:,} پوینت از کار در زندان هم دریافت کردی!\n"
        msg += "🐕 حالا می‌تونی دوباره هاپ کنی!"
        await query.edit_message_text(msg, parse_mode="Markdown")
        return True

    if data.startswith("jail_escape_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        jailed, jail_row = is_in_jail(uid)
        if not jailed:
            await query.answer("تو آزادی!", show_alert=True)
            return True
        if random.random() < JAIL_ESCAPE_CHANCE:
            with db_conn() as conn:
                conn.execute("DELETE FROM jail WHERE user_id=?", (uid,))
                conn.commit()
            await query.edit_message_text(
                "🏃 *فرار موفق!*\n\nتونستی از زندان فرار کنی! 🎉",
                parse_mode="Markdown"
            )
        else:
            rel = datetime.fromisoformat(jail_row["release_at"])
            new_rel = rel + timedelta(minutes=15)
            with db_conn() as conn:
                conn.execute("UPDATE jail SET release_at=? WHERE user_id=?", (new_rel.isoformat(), uid))
                conn.commit()
            left = int((new_rel - datetime.now()).total_seconds())
            m, s = divmod(left, 60)
            await query.answer(f"❌ گرفتن! +۱۵ دقیقه اضافه شد. ({m}:{s:02d} مونده)", show_alert=True)
        return True

    if data.startswith("jail_work_"):
        uid = int(data.split("_")[2])
        if query.from_user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        jailed, jail_row = is_in_jail(uid)
        if not jailed:
            await query.answer("تو آزادی!", show_alert=True)
            return True
        now = datetime.now()
        last_work = jail_row["last_work"] if jail_row["last_work"] else None
        if last_work:
            elapsed = (now - datetime.fromisoformat(last_work)).total_seconds()
            if elapsed < JAIL_WORK_INTERVAL:
                left = int(JAIL_WORK_INTERVAL - elapsed)
                m, s = divmod(left, 60)
                await query.answer(f"⌛️ {m}:{s:02d} تا کار بعدی!", show_alert=True)
                return True
        with db_conn() as conn:
            conn.execute(
                "UPDATE jail SET work_points=work_points+?, last_work=? WHERE user_id=?",
                (JAIL_WORK_EARN, now.isoformat(), uid)
            )
            new_wp = conn.execute("SELECT work_points FROM jail WHERE user_id=?", (uid,)).fetchone()["work_points"]
            conn.commit()
        await query.answer(f"⛏ +{JAIL_WORK_EARN} پوینت جمع کردی! (جمع: {new_wp:,})", show_alert=True)
        return True

    if data.startswith("rescue_"):
        await rescue_callback(update, context)
        return True

    if data.startswith("factory_"):
        return await factory_callback_handler(update, context)

    return False

async def handle_bank_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    bank_action = context.user_data.get("bank_action")
    if not bank_action:
        return False

    action, uid = bank_action
    user = update.effective_user
    if user.id != uid:
        return False

    amount = parse_amount(update.message.text.strip())
    if amount <= 0:
        await update.message.reply_text("❌ مبلغ نامعتبره!")
        context.user_data.pop("bank_action", None)
        return True

    conn = get_db()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (uid,)).fetchone()
    bank = conn.execute("SELECT * FROM bank WHERE user_id=?", (uid,)).fetchone()

    if not bank:
        conn.close()
        context.user_data.pop("bank_action", None)
        await update.message.reply_text("❌ حساب بانکی نداری!")
        return True

    if action == "deposit":
        if u["hop_points"] < amount:
            conn.close()
            context.user_data.pop("bank_action", None)
            await update.message.reply_text(f"❌ موجودی کافی نداری! موجودی: {u['hop_points']:,.0f}")
            return True
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (amount, uid))
        conn.execute("UPDATE bank SET balance=balance+? WHERE user_id=?", (amount, uid))
        conn.commit()
        conn.close()
        context.user_data.pop("bank_action", None)
        await update.message.reply_text(
            f"✅ *{amount:,} هاپ پوینت واریز شد!*\n"
            f"🏦 موجودی بانک: {bank['balance'] + amount:,.0f}",
            parse_mode="Markdown"
        )

    elif action == "withdraw":
        if bank["balance"] < amount:
            conn.close()
            context.user_data.pop("bank_action", None)
            await update.message.reply_text(f"❌ موجودی بانک کافی نیست! موجودی: {bank['balance']:,.0f}")
            return True
        conn.execute("UPDATE bank SET balance=balance-? WHERE user_id=?", (amount, uid))
        conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (amount, uid))
        conn.commit()
        conn.close()
        context.user_data.pop("bank_action", None)
        await update.message.reply_text(
            f"✅ *{amount:,} هاپ پوینت برداشت شد!*\n"
            f"👛 موجودی کیف: {u['hop_points'] + amount:,.0f}",
            parse_mode="Markdown"
        )

    return True


GAME_MIN_LEVEL      = 2
TABLE_MIN_LEVEL     = 3
CASINO_MIN_LEVEL    = 4
CASINO_TABLE_LEVEL  = 5

XO_COOLDOWN         = 600
DICE_COOLDOWN       = 600
WHEEL_COOLDOWN      = 600
GAMBLE_COOLDOWN     = 600

XO_TIMEOUT          = 60
TABLE_WAIT_TIMEOUT  = 60

def init_casino_tables():
    conn = get_db()
    c = conn.cursor()

    c.execute("""
        CREATE TABLE IF NOT EXISTS game_tables (
            table_id    TEXT PRIMARY KEY,
            group_id    INTEGER,
            game_type   TEXT,
            creator_id  INTEGER,
            player2_id  INTEGER DEFAULT NULL,
            bet         INTEGER,
            state       TEXT DEFAULT 'waiting',
            board       TEXT DEFAULT NULL,
            current_turn INTEGER DEFAULT NULL,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )
    """)

    c.execute("""
        CREATE TABLE IF NOT EXISTS game_cooldowns (
            user_id     INTEGER,
            game_type   TEXT,
            last_play   TEXT,
            PRIMARY KEY (user_id, game_type)
        )
    """)

    conn.commit()
    conn.close()

def check_cooldown(user_id: int, game_type: str, seconds: int) -> int:
    with db_conn() as conn:
        row = conn.execute(
            "SELECT last_play FROM game_cooldowns WHERE user_id=? AND game_type=?",
            (user_id, game_type)
        ).fetchone()
    if not row:
        return 0
    diff = (datetime.now() - datetime.fromisoformat(row["last_play"])).total_seconds()
    return max(0, int(seconds - diff))

def set_cooldown(user_id: int, game_type: str):
    with db_conn() as conn:
        conn.execute("""
            INSERT OR REPLACE INTO game_cooldowns (user_id, game_type, last_play)
            VALUES (?, ?, ?)
        """, (user_id, game_type, datetime.now().isoformat()))
        conn.commit()

def get_user(user_id):
    conn = get_db()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (user_id,)).fetchone()
    conn.close()
    return u

def add_points(user_id, amount):
    with db_conn() as conn:
        conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (amount, user_id))
        conn.commit()

def deduct_points(user_id, amount):
    with db_conn() as conn:
        conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (amount, user_id))
        conn.commit()

async def games_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🕹 بازی‌ها فقط توی گروه کار می‌کنن!")
        return

    u = get_user(user.id)
    if not u or u["level"] < GAME_MIN_LEVEL:
        await update.message.reply_text(f"🔒 برای بازی باید حداقل سطح {GAME_MIN_LEVEL} باشی!")
        return

    kb = InlineKeyboardMarkup([
        [cbtn("🧩 XO", callback_data=f"game_xo_menu_{user.id}")],
        [cbtn("🃏 کازینو", callback_data=f"casino_menu_{user.id}")],
    ])
    await update.message.reply_text(
        f"🕹 *بازی‌های هاپی*\n\n"
        f"🧩 XO — نبرد استراتژیک ۳x۳\n"
        f"🃏 کازینو — قمار، تاس، گردونه\n\n"
        f"👛 موجودیت: {u['hop_points']:,.0f} 🦴",
        parse_mode="Markdown", reply_markup=kb
    )

async def casino_menu_show(update_or_query, user, u, edit=False):
    kb = InlineKeyboardMarkup([
        [cbtn("🍷 قمار گروهی", callback_data=f"casino_gamble_{user.id}"),
         cbtn("🎰 گردونه شانس", callback_data=f"casino_wheel_{user.id}")],
        [cbtn("🎲 تاس", callback_data=f"casino_dice_{user.id}")],
    ])
    text = (
        f"🃏 *کازینو هاپی*\n\n"
        f"🍷 قمار گروهی — ۲-۵ نفره، برنده همه رو میبره\n"
        f"🎰 گردونه شانس — تکی یا چندنفره\n"
        f"🎲 تاس — زوج/فرد یا عدد دقیق\n\n"
        f"👛 موجودیت: {u['hop_points']:,.0f} 🦴"
    )
    if edit:
        await update_or_query.edit_message_text(text, parse_mode="Markdown", reply_markup=kb)
    else:
        await update_or_query.message.reply_text(text, parse_mode="Markdown", reply_markup=kb)

def render_xo_board(board: list) -> str:
    symbols = {0: "⬜", 1: "❌", 2: "⭕"}
    rows = []
    for i in range(0, 9, 3):
        rows.append(" ".join(symbols[board[i+j]] for j in range(3)))
    return "\n".join(rows)

def check_xo_winner(board: list) -> int:
    wins = [(0,1,2),(3,4,5),(6,7,8),(0,3,6),(1,4,7),(2,5,8),(0,4,8),(2,4,6)]
    for a,b,c in wins:
        if board[a] == board[b] == board[c] != 0:
            return board[a]
    if all(x != 0 for x in board):
        return -1
    return 0

def xo_board_keyboard(board: list, table_id: str) -> InlineKeyboardMarkup:
    symbols = {0: "⬜", 1: "❌", 2: "⭕"}
    rows = []
    for i in range(0, 9, 3):
        row = []
        for j in range(3):
            idx = i + j
            row.append(cbtn(
                symbols[board[idx]],
                callback_data=f"xo_move_{table_id}_{idx}" if board[idx] == 0 else f"xo_noop"
            ))
        rows.append(row)
    return InlineKeyboardMarkup(rows)

async def xo_create(update: Update, context: ContextTypes.DEFAULT_TYPE, bet: int):
    user = update.effective_user
    chat = update.effective_chat

    left = check_cooldown(user.id, "xo", XO_COOLDOWN)
    if left:
        await update.message.reply_text(f"⌛️ {left} ثانیه تا بازی بعدی صبر کن!")
        return

    u = get_user(user.id)
    if u["hop_points"] < bet:
        await update.message.reply_text(f"❌ موجودی کافی نداری! موجودی: {u['hop_points']:,.0f}")
        return

    table_id = f"xo_{user.id}_{int(datetime.now().timestamp())}"
    deduct_points(user.id, bet)

    conn = get_db()
    conn.execute("""
        INSERT INTO game_tables (table_id, group_id, game_type, creator_id, bet, state, board, current_turn)
        VALUES (?, ?, 'xo', ?, ?, 'waiting', ?, ?)
    """, (table_id, chat.id, user.id, bet, "0"*9, user.id))
    conn.commit()
    conn.close()

    kb = InlineKeyboardMarkup([[
        cbtn(f"✋ پیوستن ({bet:,} پوینت)", callback_data=f"xo_join_{table_id}"),
        cbtn("❌ لغو", callback_data=f"xo_cancel_{table_id}"),
    ]])
    await update.message.reply_text(
        f"🧩 *{user.first_name} میز XO ساخت!*\n\n"
        f"💰 شرط: {bet:,} هاپ پوینت\n"
        f"⌛️ {TABLE_WAIT_TIMEOUT} ثانیه فرصت پیوستن",
        parse_mode="Markdown", reply_markup=kb
    )


async def dice_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🎲 تاس فقط توی گروه کار می‌کنه!")
        return

    u = get_user(user.id)
    if not u or u["level"] < CASINO_MIN_LEVEL:
        await update.message.reply_text(f"🔒 برای تاس باید سطح {CASINO_MIN_LEVEL} باشی!")
        return

    left = check_cooldown(user.id, "dice", DICE_COOLDOWN)
    if left:
        m, s = divmod(left, 60)
        await update.message.reply_text(f"⌛️ {m} دقیقه و {s} ثانیه تا تاس بعدی!")
        return

    context.user_data["dice_state"] = {"user_id": user.id, "step": "bet"}
    await update.message.reply_text(
        f"🎲 *تاس هاپی*\n\n"
        f"💰 شرط چقدر؟ (موجودی: {u['hop_points']:,.0f})\n"
        f"مبلغ رو بنویس:",
        parse_mode="Markdown"
    )

async def handle_dice_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message:
        return False

    state = context.user_data.get("dice_state")
    if not state or state["user_id"] != update.effective_user.id:
       return False

    user = update.effective_user

    text = update.message.text.strip() if update.message.text else ""
    if update.message.dice:
        return False

    if state["step"] == "bet":
       try:
           bet = int(text.replace(",", ""))
       except:
           await update.message.reply_text("❌ عدد معتبر وارد کن!")
           return True
       u = get_user(user.id)
       if bet <= 0 or u["hop_points"] < bet:
           await update.message.reply_text("❌ مبلغ نامعتبر یا موجودی کافی نیست!")
           return True
       state["bet"] = bet
       state["step"] = "mode"
       context.user_data["dice_state"] = state
       kb = InlineKeyboardMarkup([
           [cbtn("زوج یا فرد (1.7x)", callback_data=f"dice_mode_evenodd_{user.id}"),
            cbtn("عدد دقیق (4x)", callback_data=f"dice_mode_exact_{user.id}")],
       ])
       await update.message.reply_text("🎲 چه نوع شرطی؟", reply_markup=kb)
       return True

    if state["step"] == "guess_exact":
       try:
           guess = int(text)
           if guess < 1 or guess > 6:
               raise ValueError
       except:
           await update.message.reply_text("❌ عدد ۱ تا ۶ وارد کن!")
           return True

       bet = state["bet"]
       u = get_user(user.id)
       if u["hop_points"] < bet:
           await update.message.reply_text("❌ موجودی کافی نداری!")
           context.user_data.pop("dice_state", None)
           return True

       context.user_data.pop("dice_state", None)
       deduct_points(user.id, bet)
       set_cooldown(user.id, "dice")

       await update.message.reply_text(
           f"🎲 حدست: *{guess}* | شرط: {bet:,}\n\n⏳ در حال پرتاب تاس...",
           parse_mode="Markdown"
       )

       dice_msg = await update.message.chat.send_dice(emoji="🎲")
       roll = dice_msg.dice.value
       await asyncio.sleep(4)

       if roll == guess:
           win = int(bet * 4)
           add_points(user.id, win)
           await update.message.reply_text(
               f"🎯 عدد {roll}!\n\n"
               f"🎉 درست حدس زدی! +{win:,} هاپ پوینت"
           )
       else:
           await update.message.reply_text(
               f"💀 عدد {roll} (تو گفتی {guess})!\n\n"
               f"😢 اشتباه بود. -{bet:,} هاپ پوینت"
           )
       return True

    return False

async def handle_slot_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message or not update.message.dice:
        return False
    if update.message.dice.emoji != "🎰":
        return False

    wheel_state = context.user_data.get("wheel_state")
    if not wheel_state or wheel_state["user_id"] != update.effective_user.id:
        return False
    if wheel_state.get("step") != "waiting_slot":
        return False

    user = update.effective_user
    bet = wheel_state["bet"]
    slot_value = update.message.dice.value

    u = get_user(user.id)
    if u["hop_points"] < bet:
        await update.message.reply_text("❌ موجودی کافی نداری!")
        context.user_data.pop("wheel_state", None)
        return True

    context.user_data.pop("wheel_state", None)
    set_cooldown(user.id, "wheel")
    deduct_points(user.id, bet)

    await asyncio.sleep(3)

    mult, label = get_wheel_result(slot_value)
    win = int(bet * mult)

    if mult == 0.0:
        result = f"💀 {label}\n-{bet:,} هاپ پوینت"
    elif mult < 1.0:
        add_points(user.id, win)
        result = f"😬 {label} (×{mult})\n+{win:,} برگشت (باختی {bet-win:,})"
    else:
        add_points(user.id, win)
        result = f"{'🎰' if mult >= 15 else '🎉'} {label} (×{mult})\n+{win:,} هاپ پوینت"

    await update.message.reply_text(
        f"🎰 *نتیجه گردونه!*\n\n{result}",
        parse_mode="Markdown"
    )
    return True

WHEEL_MULTIPLIERS = [0.0, 0.0, 0.5, 0.5, 0.8, 1.2, 1.5, 2.0, 3.0, 5.0]
WHEEL_WEIGHTS     = [12,  10,  15,  12,  18,  14,  10,  6,   2,   1  ]

def get_wheel_result(slot_value: int) -> tuple:
    if slot_value == 64:
        return (15.0, "🎰 جکپات! 7️⃣7️⃣7️⃣")
    elif slot_value >= 49:
        return (2.5, "🎉 برنده!")
    elif slot_value >= 29:
        return (0.5, "😬 نصفه...")
    else:
        return (0.0, "💀 باختی!")

async def wheel_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🎰 گردونه فقط توی گروه!")
        return

    u = get_user(user.id)
    if not u or u["level"] < CASINO_MIN_LEVEL:
        await update.message.reply_text(f"🔒 سطح {CASINO_MIN_LEVEL} لازمه!")
        return

    left = check_cooldown(user.id, "wheel", WHEEL_COOLDOWN)
    if left:
        m, s = divmod(left, 60)
        await update.message.reply_text(f"⌛️ {m} دقیقه و {s} ثانیه تا گردونه بعدی!")
        return

    kb = InlineKeyboardMarkup([
        [cbtn("👤 تکی", callback_data=f"wheel_solo_{user.id}"),
         cbtn("👥 چندنفره", callback_data=f"wheel_multi_{user.id}")],
    ])
    await update.message.reply_text(
        f"🎰 *گردونه شانس*\n\n"
        f"ضرایب: 0x❌ | 0.5x | 1.5x | 2x | 3x | 5x🌟\n"
        f"👛 موجودی: {u['hop_points']:,.0f}\n\n"
        f"چه حالتی؟",
        parse_mode="Markdown", reply_markup=kb
    )

async def gamble_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🍷 قمار فقط توی گروه!")
        return

    u = get_user(user.id)
    if not u or u["level"] < CASINO_MIN_LEVEL:
        await update.message.reply_text(f"🔒 سطح {CASINO_MIN_LEVEL} لازمه!")
        return

    left = check_cooldown(user.id, "gamble", GAMBLE_COOLDOWN)
    if left:
        m, s = divmod(left, 60)
        await update.message.reply_text(f"⌛️ {m} دقیقه و {s} ثانیه تا قمار بعدی!")
        return

    context.user_data["gamble_state"] = {"user_id": user.id, "step": "bet", "group_id": chat.id}
    await update.message.reply_text(
        f"🍷 *قمار گروهی*\n\n"
        f"💰 شرط چقدر؟ (موجودی: {u['hop_points']:,.0f})\n"
        f"مبلغ رو بنویس:",
        parse_mode="Markdown"
    )

async def handle_gamble_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    state = context.user_data.get("gamble_state")
    if not state or state["user_id"] != update.effective_user.id:
        return False
    if state["step"] != "bet":
        return False

    user = update.effective_user
    text = update.message.text.strip()
    try:
        bet = int(text.replace(",", ""))
    except:
        await update.message.reply_text("❌ عدد معتبر وارد کن!")
        return True

    u = get_user(user.id)
    if bet <= 0 or u["hop_points"] < bet:
        await update.message.reply_text("❌ مبلغ نامعتبر یا موجودی کافی نیست!")
        return True

    group_id = state["group_id"]
    table_id = f"gamble_{user.id}_{int(datetime.now().timestamp())}"
    deduct_points(user.id, bet)
    context.user_data.pop("gamble_state", None)

    conn = get_db()
    conn.execute("""
        INSERT INTO game_tables (table_id, group_id, game_type, creator_id, bet, state, board)
        VALUES (?, ?, 'gamble', ?, ?, 'waiting', ?)
    """, (table_id, group_id, user.id, bet, f"{user.id}:{bet}"))
    conn.commit()
    conn.close()

    kb = InlineKeyboardMarkup([[
        cbtn(f"🍷 پیوستن ({bet:,} پوینت)", callback_data=f"gamble_join_{table_id}"),
        cbtn("🎯 شروع بازی!", callback_data=f"gamble_start_{table_id}"),
    ]])
    await update.message.reply_text(
        f"🍷 *{user.first_name} میز قمار ساخت!*\n\n"
        f"💰 شرط: {bet:,} هاپ پوینت\n"
        f"👥 بازیکن‌ها: 1 نفر\n"
        f"⌛️ منتظر بقیه...\n\n"
        f"(بعد از جمع شدن ۲-۵ نفر، سازنده دکمه شروع رو بزنه)",
        parse_mode="Markdown", reply_markup=kb
    )
    return True

async def casino_callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    query = update.callback_query
    data = query.data
    user = query.from_user

    if data.startswith("game_xo_menu_"):
        uid = int(data.split("_")[3])
        u = get_user(uid)
        if not u or u["level"] < TABLE_MIN_LEVEL:
            await query.answer(f"سطح {TABLE_MIN_LEVEL} لازمه!", show_alert=True)
            return True
        context.user_data["create_game"] = ("xo", uid)
        await query.edit_message_text(
            "🧩 *میز XO*\n\nشرط چقدر؟ مبلغ رو بنویس:",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("casino_menu_"):
        uid = int(data.split("_")[2])
        u = get_user(uid)
        if not u or u["level"] < CASINO_MIN_LEVEL:
            await query.answer(f"سطح {CASINO_MIN_LEVEL} لازمه!", show_alert=True)
            return True
        await casino_menu_show(query, user, u, edit=True)
        return True

    if data.startswith("casino_dice_"):
        uid = int(data.split("_")[2])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        left = check_cooldown(uid, "dice", DICE_COOLDOWN)
        if left:
            m, s = divmod(left, 60)
            await query.answer(f"⌛️ {m}:{s:02d} تا تاس بعدی!", show_alert=True)
            return True
        context.user_data["dice_state"] = {"user_id": uid, "step": "bet"}
        u = get_user(uid)
        await query.edit_message_text(
            f"🎲 *تاس*\n\n💰 موجودی: {u['hop_points']:,.0f} هاپ\n\nشرط چقدر؟ مبلغ رو بنویس:",
            parse_mode="Markdown"
        )
        return True

    if data.startswith("casino_wheel_"):
        uid = int(data.split("_")[2])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        left = check_cooldown(uid, "wheel", WHEEL_COOLDOWN)
        if left:
            m, s = divmod(left, 60)
            await query.answer(f"⌛️ {m}:{s:02d} تا گردونه بعدی!", show_alert=True)
            return True
        u = get_user(uid)
        await query.edit_message_text(
            f"🎰 *گردونه شانس*\n\n💰 موجودی: {u['hop_points']:,.0f} هاپ\n\nشرط چقدر؟ مبلغ رو بنویس:",
            parse_mode="Markdown"
        )
        context.user_data["wheel_state"] = {"user_id": uid, "step": "bet", "mode": "solo"}
        return True

    if data.startswith("casino_gamble_"):
        uid = int(data.split("_")[2])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        left = check_cooldown(uid, "gamble", GAMBLE_COOLDOWN)
        if left:
            m, s = divmod(left, 60)
            await query.answer(f"⌛️ {m}:{s:02d} تا قمار بعدی!", show_alert=True)
            return True
        u = get_user(uid)
        await query.edit_message_text(
            f"🃏 *قمار گروهی*\n\n💰 موجودی: {u['hop_points']:,.0f} هاپ\n\nشرط چقدر؟ مبلغ رو بنویس:",
            parse_mode="Markdown"
        )
        context.user_data["gamble_state"] = {"user_id": uid, "step": "bet", "group_id": query.message.chat_id}
        return True

    if data.startswith("dice_mode_"):
        parts = data.split("_")
        mode = parts[2]
        uid = int(parts[3])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        state = context.user_data.get("dice_state")
        if not state:
            return True
        bet = state.get("bet", 0)
        u = get_user(uid)

        if mode == "evenodd":
            kb = InlineKeyboardMarkup([[
                cbtn("زوج", callback_data=f"dice_guess_even_{uid}_{bet}"),
                cbtn("فرد", callback_data=f"dice_guess_odd_{uid}_{bet}"),
            ]])
            await query.edit_message_text("🎲 زوج یا فرد؟", reply_markup=kb)
            context.user_data.pop("dice_state", None)
        else:
            state["step"] = "guess_exact"
            context.user_data["dice_state"] = state
            await query.edit_message_text("🎲 عدد دقیق (۱ تا ۶) رو بنویس:")
        return True

    if data.startswith("dice_guess_"):
        parts = data.split("_")
        guess_type = parts[2]
        uid = int(parts[3])
        bet = int(parts[4])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        u = get_user(uid)
        if u["hop_points"] < bet:
            await query.answer("پوینت کافی نداری!", show_alert=True)
            return True

        u = get_user(uid)
        if u["hop_points"] < bet:
            await query.answer("پوینت کافی نداری!", show_alert=True)
            return True

        await query.edit_message_text(
            f"🎲 انتخابت: *{'زوج' if guess_type == 'even' else 'فرد'}* | شرط: {bet:,}\n\n"
            f"⏳ در حال پرتاب تاس...",
            parse_mode="Markdown"
        )

        deduct_points(uid, bet)
        set_cooldown(uid, "dice")
        context.user_data.pop("dice_state", None)

        dice_msg = await query.message.chat.send_dice(emoji="🎲")
        roll = dice_msg.dice.value
        await asyncio.sleep(4)

        is_even = roll % 2 == 0
        if (guess_type == "even" and is_even) or (guess_type == "odd" and not is_even):
            win = int(bet * 1.7)
            add_points(uid, win)
            await query.message.reply_text(
                f"✅ عدد {roll} ({'زوج' if is_even else 'فرد'})!\n\n"
                f"🎉 بردی! +{win:,} هاپ پوینت"
            )
        else:
            await query.message.reply_text(
                f"💀 عدد {roll} ({'زوج' if is_even else 'فرد'})!\n\n"
                f"😢 باختی! -{bet:,} هاپ پوینت"
            )
        return True

    if data.startswith("wheel_solo_"):
        uid = int(data.split("_")[2])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        context.user_data["wheel_state"] = {"user_id": uid, "step": "bet", "mode": "solo"}
        await query.edit_message_text("🎰 شرط چقدر؟ مبلغ رو بنویس:")
        return True

    if data.startswith("wheel_multi_"):
        uid = int(data.split("_")[2])
        if user.id != uid:
            await query.answer("این دکمه برای تو نیست!", show_alert=True)
            return True
        u = get_user(uid)
        if u["level"] < CASINO_TABLE_LEVEL:
            await query.answer(f"سطح {CASINO_TABLE_LEVEL} لازمه!", show_alert=True)
            return True
        context.user_data["wheel_state"] = {"user_id": uid, "step": "bet", "mode": "multi"}
        await query.edit_message_text("🎰 شرط چقدر؟ مبلغ رو بنویس:")
        return True

    if data.startswith("xo_join_"):
        table_id = data[8:]
        conn = get_db()
        table = conn.execute("SELECT * FROM game_tables WHERE table_id=?", (table_id,)).fetchone()

        if not table or table["state"] != "waiting":
            conn.close()
            await query.answer("این میز دیگه موجود نیست!", show_alert=True)
            return True
        if table["creator_id"] == user.id:
            conn.close()
            await query.answer("نمی‌تونی با خودت بازی کنی!", show_alert=True)
            return True

        u = get_user(user.id)
        bet = table["bet"]
        if u["hop_points"] < bet:
            conn.close()
            await query.answer(f"پوینت کافی نداری! لازم: {bet:,}", show_alert=True)
            return True

        deduct_points(user.id, bet)
        board = [0]*9
        conn.execute("""
            UPDATE game_tables SET player2_id=?, state='playing', board=?, current_turn=?
            WHERE table_id=?
        """, (user.id, ",".join(map(str, board)), table["creator_id"], table_id))
        conn.commit()

        creator = conn.execute("SELECT first_name FROM users WHERE user_id=?", (table["creator_id"],)).fetchone()
        conn.close()

        kb = xo_board_keyboard(board, table_id)
        await query.edit_message_text(
            f"🧩 *بازی XO شروع شد!*\n\n"
            f"❌ {creator['first_name']} vs ⭕ {user.first_name}\n"
            f"💰 جایزه: {bet*2:,} هاپ پوینت\n\n"
            f"نوبت: ❌ {creator['first_name']}\n\n"
            f"{render_xo_board(board)}",
            parse_mode="Markdown", reply_markup=kb
        )
        return True

    if data.startswith("xo_move_"):
        parts = data.split("_")
        idx = int(parts[-1])
        table_id = "_".join(parts[2:-1])

        conn = get_db()
        table = conn.execute("SELECT * FROM game_tables WHERE table_id=?", (table_id,)).fetchone()
        if not table or table["state"] != "playing":
            conn.close()
            await query.answer("بازی تموم شده!", show_alert=True)
            return True
        if table["current_turn"] != user.id:
            conn.close()
            await query.answer("نوبت تو نیست!", show_alert=True)
            return True

        board = list(map(int, table["board"].split(",")))
        if board[idx] != 0:
            conn.close()
            await query.answer("این خونه پره!", show_alert=True)
            return True

        player_num = 1 if user.id == table["creator_id"] else 2
        board[idx] = player_num

        winner = check_xo_winner(board)
        next_turn = table["player2_id"] if user.id == table["creator_id"] else table["creator_id"]

        conn2 = get_db()
        p1 = conn2.execute("SELECT first_name FROM users WHERE user_id=?", (table["creator_id"],)).fetchone()
        p2 = conn2.execute("SELECT first_name FROM users WHERE user_id=?", (table["player2_id"],)).fetchone()
        conn2.close()

        if winner == 0:
            conn.execute("""
                UPDATE game_tables SET board=?, current_turn=? WHERE table_id=?
            """, (",".join(map(str, board)), next_turn, table_id))
            conn.commit()
            conn.close()
            next_name = p1["first_name"] if next_turn == table["creator_id"] else p2["first_name"]
            next_sym = "❌" if next_turn == table["creator_id"] else "⭕"
            kb = xo_board_keyboard(board, table_id)
            await query.edit_message_text(
                f"🧩 *بازی XO*\n\n"
                f"❌ {p1['first_name']} vs ⭕ {p2['first_name']}\n\n"
                f"{render_xo_board(board)}\n\n"
                f"نوبت: {next_sym} {next_name}",
                parse_mode="Markdown", reply_markup=kb
            )
        elif winner == -1:
            conn.execute("UPDATE game_tables SET state='done' WHERE table_id=?", (table_id,))
            conn.commit()
            conn.close()
            add_points(table["creator_id"], table["bet"])
            add_points(table["player2_id"], table["bet"])
            set_cooldown(table["creator_id"], "xo")
            set_cooldown(table["player2_id"], "xo")
            await query.edit_message_text(
                f"🤝 *مساوی!*\n\n"
                f"{render_xo_board(board)}\n\n"
                f"پوینت‌ها برگشت داده شد!",
                parse_mode="Markdown"
            )
        else:
            winner_id = table["creator_id"] if winner == 1 else table["player2_id"]
            loser_id = table["player2_id"] if winner == 1 else table["creator_id"]
            prize = table["bet"] * 2
            conn.execute("UPDATE game_tables SET state='done' WHERE table_id=?", (table_id,))
            conn.commit()
            conn.close()
            add_points(winner_id, prize)
            set_cooldown(table["creator_id"], "xo")
            set_cooldown(table["player2_id"], "xo")
            winner_name = p1["first_name"] if winner_id == table["creator_id"] else p2["first_name"]
            await query.edit_message_text(
                f"🏆 *{winner_name} برنده شد!*\n\n"
                f"{render_xo_board(board)}\n\n"
                f"🎉 +{prize:,} هاپ پوینت",
                parse_mode="Markdown"
            )
        return True

    if data == "xo_noop":
        await query.answer()
        return True

    if data.startswith("xo_cancel_"):
        table_id = data[10:]
        conn = get_db()
        table = conn.execute("SELECT * FROM game_tables WHERE table_id=?", (table_id,)).fetchone()
        if not table:
            conn.close()
            await query.answer("میز پیدا نشد!", show_alert=True)
            return True
        if table["creator_id"] != user.id:
            conn.close()
            await query.answer("فقط سازنده میز میتونه لغو کنه!", show_alert=True)
            return True
        if table["state"] != "waiting":
            conn.close()
            await query.answer("بازی شروع شده!", show_alert=True)
            return True
        add_points(user.id, table["bet"])
        conn.execute("DELETE FROM game_tables WHERE table_id=?", (table_id,))
        conn.commit()
        conn.close()
        await query.edit_message_text("❌ میز لغو شد. پوینتت برگشت داده شد.")
        return True

    if data.startswith("gamble_join_"):
        table_id = data[12:]
        conn = get_db()
        table = conn.execute("SELECT * FROM game_tables WHERE table_id=?", (table_id,)).fetchone()
        if not table or table["state"] != "waiting":
            conn.close()
            await query.answer("این میز دیگه موجود نیست!", show_alert=True)
            return True

        players = [p for p in table["board"].split(",") if p]
        player_ids = [int(p.split(":")[0]) for p in players]

        if user.id in player_ids:
            conn.close()
            await query.answer("قبلاً پیوستی!", show_alert=True)
            return True
        if len(players) >= 5:
            conn.close()
            await query.answer("میز پره! (حداکثر ۵ نفر)", show_alert=True)
            return True

        bet = table["bet"]
        u = get_user(user.id)
        if u["hop_points"] < bet:
            conn.close()
            await query.answer(f"پوینت کافی نداری! لازم: {bet:,}", show_alert=True)
            return True

        deduct_points(user.id, bet)
        players.append(f"{user.id}:{bet}")
        conn.execute("UPDATE game_tables SET board=? WHERE table_id=?", (",".join(players), table_id))
        conn.commit()
        conn.close()

        kb = InlineKeyboardMarkup([[
            cbtn(f"🍷 پیوستن ({bet:,} پوینت)", callback_data=f"gamble_join_{table_id}"),
            cbtn("🎯 شروع!", callback_data=f"gamble_start_{table_id}"),
        ]])
        await query.edit_message_text(
            f"🍷 *میز قمار*\n\n"
            f"💰 شرط: {bet:,} هاپ پوینت\n"
            f"👥 بازیکن‌ها: {len(players)} نفر\n"
            f"(سازنده دکمه شروع رو بزنه)",
            parse_mode="Markdown", reply_markup=kb
        )
        return True

    if data.startswith("gamble_start_"):
        table_id = data[13:]
        conn = get_db()
        table = conn.execute("SELECT * FROM game_tables WHERE table_id=?", (table_id,)).fetchone()
        if not table:
            conn.close()
            await query.answer("میز پیدا نشد!", show_alert=True)
            return True
        if table["creator_id"] != user.id:
            conn.close()
            await query.answer("فقط سازنده میتونه شروع کنه!", show_alert=True)
            return True

        players = [p for p in table["board"].split(",") if p]
        if len(players) < 2:
            conn.close()
            await query.answer("حداقل ۲ نفر لازمه!", show_alert=True)
            return True

        winner_entry = random.choice(players)
        winner_id = int(winner_entry.split(":")[0])
        total_pot = sum(int(p.split(":")[1]) for p in players)

        add_points(winner_id, total_pot)
        conn.execute("UPDATE game_tables SET state='done' WHERE table_id=?", (table_id,))
        conn.commit()

        winner_row = conn.execute("SELECT first_name FROM users WHERE user_id=?", (winner_id,)).fetchone()
        conn.close()

        for p in players:
            set_cooldown(int(p.split(":")[0]), "gamble")

        await query.edit_message_text(
            f"🍀 *نتیجه قمار!*\n\n"
            f"🏆 *{winner_row['first_name']}* برنده شد!\n"
            f"💰 +{total_pot:,} هاپ پوینت برنده برد!",
            parse_mode="Markdown"
        )
        return True

    return False

async def handle_casino_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user

    game_state = context.user_data.get("create_game")
    if game_state:
        game_type, uid = game_state
        if user.id != uid:
            return False
        text = update.message.text.strip()
        try:
            bet = int(text.replace(",", ""))
            if bet <= 0:
                raise ValueError
        except:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        context.user_data.pop("create_game", None)
        if game_type == "xo":
            await xo_create(update, context, bet)
        return True

    wheel_state = context.user_data.get("wheel_state")
    if wheel_state and wheel_state["user_id"] == user.id:
        text = update.message.text.strip() if update.message.text else ""

        if wheel_state.get("step") == "waiting_slot":
            return False

        try:
            bet = int(text.replace(",", ""))
            if bet <= 0:
                raise ValueError
        except:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        u = get_user(user.id)
        if u["hop_points"] < bet:
            context.user_data.pop("wheel_state", None)
            await update.message.reply_text("❌ موجودی کافی نداری!")
            return True

        left = check_cooldown(user.id, "wheel", WHEEL_COOLDOWN)
        if left:
            context.user_data.pop("wheel_state", None)
            await update.message.reply_text(f"⌛️ {left} ثانیه تا گردونه بعدی!")
            return True

        context.user_data.pop("wheel_state", None)
        deduct_points(user.id, bet)
        set_cooldown(user.id, "wheel")

        await update.message.reply_text(
            f"🎰 شرط: *{bet:,}*\n\n⏳ در حال چرخوندن گردونه...",
            parse_mode="Markdown"
        )

        slot_msg = await update.message.chat.send_dice(emoji="🎰")
        slot_value = slot_msg.dice.value
        await asyncio.sleep(3)

        mult, label = get_wheel_result(slot_value)
        win = int(bet * mult)

        if mult == 0.0:
            result = f"💀 {label}\n-{bet:,} هاپ پوینت"
        elif mult < 1.0:
            add_points(user.id, win)
            result = f"😬 {label} (×{mult})\n+{win:,} برگشت (باختی {bet-win:,})"
        else:
            add_points(user.id, win)
            result = f"{'🎰' if mult >= 15 else '🎉'} {label} (×{mult})\n+{win:,} هاپ پوینت"

        await update.message.reply_text(
            f"🎰 *نتیجه گردونه!*\n\n{result}",
            parse_mode="Markdown"
        )
        return True

    if await handle_dice_input(update, context):
        return True

    if await handle_gamble_input(update, context):
        return True

    return False


def get_city_level(grp: dict) -> int:
    lvl = 1
    for i in range(2, CITY_MAX_LEVEL + 1):
        req = CITY_LEVELS[i]
        if (grp["treasury"]    >= req[0] and
            grp["total_hops"]  >= req[1] and
            grp["total_dogs"]  >= req[2] and
            grp["total_bones"] >= req[3] and
            grp["total_fish"]  >= req[4]):
            lvl = i
    return lvl

def city_hop_cooldown(city_level: int) -> int:
    return max(30, HOP_COOLDOWN - (city_level - 1) * CITY_HOP_BUFF)

def city_fish_cooldown(city_level: int, base_cd: int) -> int:
    return max(60, base_cd - (city_level - 1) * CITY_FISH_BUFF)

async def city_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🏰 دستور شهر فقط توی گروه کار می‌کنه!")
        return

    ensure_group(chat.id, chat.title or "گروه")
    conn = get_db()
    grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    city_lvl = get_city_level(grp)

    all_groups = conn.execute("SELECT group_id, treasury, total_hops, total_dogs, total_bones, total_fish FROM groups").fetchall()
    conn.close()

    def group_sort_key(g):
        lvl = get_city_level(g)
        return (lvl, g["treasury"])

    sorted_groups = sorted(all_groups, key=group_sort_key, reverse=True)
    r_city = next((i+1 for i, g in enumerate(sorted_groups) if g["group_id"] == chat.id), 1)

    title = grp["title"] or "شهر هاپو"

    def bar(val, need):
        if need == 0: return "▰▰▰▰▰"
        f = min(5, int((val / need) * 5))
        return "▰" * f + "▱" * (5 - f)

    if city_lvl < CITY_MAX_LEVEL:
        nxt = CITY_LEVELS[city_lvl + 1]
        progress = (
            f"\n📊 *پیشرفت به سطح {city_lvl + 1}:*\n"
            f"┐─ 🏦 خزانه : {grp['treasury']:,.0f} / {nxt[0]:,}  {bar(grp['treasury'], nxt[0])}\n"
            f"┐─ 🐾 هاپ‌های کل : {grp['total_hops']:,} / {nxt[1]:,}  {bar(grp['total_hops'], nxt[1])}\n"
            f"┐─ 🐕 سگ‌های خریداری شده : {grp['total_dogs']:,} / {nxt[2]:,}  {bar(grp['total_dogs'], nxt[2])}\n"
            f"┐─ 🦴 استخوان‌ها : {grp['total_bones']:,} / {nxt[3]:,}  {bar(grp['total_bones'], nxt[3])}\n"
            f"└─ 🎣 ماهی‌ها : {grp['total_fish']:,} / {nxt[4]:,}  {bar(grp['total_fish'], nxt[4])}"
        )
    else:
        progress = "\n🏆 *شهر به سطح ماکس رسیده!*"

    hop_cd   = city_hop_cooldown(city_lvl)
    fish_red = (city_lvl - 1) * CITY_FISH_BUFF
    stray_red = int((city_lvl - 1) * CITY_STRAY_BUFF * 100)

    stars = "⭐️" * city_lvl if city_lvl <= 5 else "⭐️" * 5 + f" +{city_lvl-5}"

    text = (
        f"╮──「 🏰 شهر هاپو 🐾 」\n\n"
        f"┐─ 🏙️ نام : {title}\n"
        f"┐─ 🎖️ رتبه جهانی : #{r_city}\n"
        f"└─ {stars}\n\n"
        f"📈 *آمار شهر:*\n"
        f"┐─ ⭐️ سطح : {city_lvl} / {CITY_MAX_LEVEL}\n"
        f"┐─ 🏦 خزانه : {grp['treasury']:,.0f} 🦴\n"
        f"┐─ 🐾 کل هاپ : {grp['total_hops']:,}\n"
        f"┐─ 🐕 کل سگ : {grp['total_dogs']:,}\n"
        f"┐─ 🦴 کل استخوان : {grp['total_bones']:,}\n"
        f"└─ 🎣 کل ماهی : {grp['total_fish']:,}\n"
        f"\n✨ *باف‌های فعال (سطح {city_lvl}):*\n"
        f"┐─ 🐾 کولداون هاپ : {hop_cd}s (اصلی {HOP_COOLDOWN}s)\n"
        f"┐─ 🎣 کاهش کولداون ماهیگیری : {fish_red}s\n"
        f"└─ 🐈 کاهش آستانه پیشی خیابونی : {stray_red}%\n"
        f"{progress}\n\n"
        f"💡 برای کمک به خزانه بنویس: *اهدا [مقدار]*"
    )
    await update.message.reply_text(text, parse_mode="Markdown")

async def donate_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat = update.effective_chat
    user = update.effective_user
    if chat.type == "private":
        await update.message.reply_text("🏦 این دستور فقط توی گروه کار می‌کنه!")
        return

    parts = update.message.text.strip().split()
    if len(parts) < 2:
        await update.message.reply_text("✍️ فرمت درست: *اهدا 500*", parse_mode="Markdown")
        return
    try:
        amount = int(parts[1].replace(",", ""))
        if amount <= 0:
            raise ValueError
    except ValueError:
        await update.message.reply_text("❌ مقدار نامعتبره! یه عدد مثبت بنویس.")
        return

    conn = get_db()
    u = conn.execute("SELECT hop_points FROM users WHERE user_id=?", (user.id,)).fetchone()
    if not u:
        conn.close()
        await update.message.reply_text("❌ اول باید هاپ بزنی تا ثبت بشی!")
        return
    if u["hop_points"] < amount:
        conn.close()
        await update.message.reply_text(
            f"❌ پوینت کافی نداری!\n💰 موجودی: {u['hop_points']:,.0f} هاپ پوینت")
        return

    ensure_group(chat.id, chat.title or "گروه")
    old_grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    old_lvl = get_city_level(old_grp)

    conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (amount, user.id))
    conn.execute("UPDATE groups SET treasury=treasury+? WHERE group_id=?", (amount, chat.id))
    conn.commit()

    new_grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (chat.id,)).fetchone()
    new_lvl = get_city_level(new_grp)
    conn.close()

    msg = (
        f"🏦 *{user.first_name}* {amount:,} هاپ پوینت به خزانه شهر اهدا کرد! 🎉\n"
        f"💰 خزانه فعلی: {new_grp['treasury']:,.0f}"
    )
    if new_lvl > old_lvl:
        msg += f"\n\n🎊 *شهر به سطح {new_lvl} ارتقا پیدا کرد!* 🏰"
    await update.message.reply_text(msg, parse_mode="Markdown")
async def hapoha_profile(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    conn = get_db()

    u = conn.execute("SELECT * FROM users WHERE user_id=?", (user.id,)).fetchone()
    if not u:
        conn.close()
        await update.message.reply_text("🐾 هنوز هاپو نزدی! اول بنویس *هاپ* تا ثبت بشی!", parse_mode="Markdown")
        return

    dog    = conn.execute("SELECT * FROM dogs WHERE user_id=?",        (user.id,)).fetchone()
    hook   = conn.execute("SELECT * FROM hooks WHERE user_id=?",       (user.id,)).fetchone()
    strays = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (user.id,)).fetchone()
    jailed, jail_row = is_in_jail(user.id)

    r_pts = conn.execute(
        "SELECT COUNT(*)+1 FROM users WHERE hop_points > ?", (u["hop_points"],)
    ).fetchone()[0]
    r_hops = conn.execute(
        "SELECT COUNT(*)+1 FROM users WHERE total_hops > ?", (u["total_hops"],)
    ).fetchone()[0]
    stray_count = strays["count"] if strays else 0
    r_stray = conn.execute(
        "SELECT COUNT(*)+1 FROM user_strays WHERE count > ?", (stray_count,)
    ).fetchone()[0]
    conn.close()

    lvl       = u["level"]
    hops      = u["total_hops"]
    next_hops = hops_for_next_level(lvl)
    if next_hops > 0 and lvl > 1:
        prev_hops  = hops_for_next_level(lvl - 1)
        progress   = hops - prev_hops
        needed     = next_hops - prev_hops
        filled     = int((progress / needed) * 5) if needed > 0 else 5
    else:
        filled, needed, progress = 5, 0, hops
    bar = "▰" * filled + "▱" * (5 - filled)

    dog_line = ""
    if dog:
        dog_rank_name, _ = DOG_RANKS.get(dog["rank"], ("نامشخص", 1))
        dog_line = (
            f"\n┐─ 🐕 سگ : {dog['name']}\n"
            f"└─ 🎖️ سطح {dog['level']} | مقام {dog_rank_name}"
        )

    hook_line = ""
    if hook:
        hook_line = f"\n└─ 🎣 قلاب : سطح {hook['level']}"

    jail_line = ""
    if jailed:
        release = datetime.fromisoformat(jail_row["release_at"])
        mins    = int((release - datetime.now()).total_seconds() // 60)
        jail_line = f"\n\n⛓️ *در زندان!* — {mins} دقیقه تا آزادی"

    display_name = f"@{user.username}" if user.username else user.first_name

    caption = (
        f"╮──「 🐾 پروفایل هاپو 🐾 」\n\n"
        f"┐─ 👤 کاربر : {display_name}\n"
        f"└─ 🪪 آیدی : `{user.id}`\n\n"
        f"┐─ 💰 هاپ پوینت : {u['hop_points']:,.0f} 🦴\n"
        f"└─ 🎖️ رتبه ({r_pts:,})\n"
        f"┐─ 🐾 هاپ‌های کل : {hops:,}\n"
        f"└─ 🎖️ رتبه ({r_hops:,})\n\n"
        f"┐─ 🐈 پیشی‌های خیابونی : {stray_count}\n"
        f"└─ 🎖️ رتبه ({r_stray:,})\n"
        f"{dog_line}"
        f"{hook_line}\n\n"
        f"╯─ ⭐️ سطح : {lvl} | {progress} / {needed if needed else '∞'} {bar}"
        f"{jail_line}"
    )

    photo_buf = None
    try:
        photos = await context.bot.get_user_profile_photos(user.id, limit=1)
        if photos.total_count > 0:
            file = await photos.photos[0][-1].get_file()
            photo_buf = io.BytesIO()
            await file.download_to_memory(photo_buf)
            photo_buf.seek(0)
    except Exception:
        pass

    if photo_buf:
        await update.message.reply_photo(
            photo=photo_buf,
            caption=caption,
            parse_mode="Markdown"
        )
    else:
        await update.message.reply_text(caption, parse_mode="Markdown")

async def hapoha_profile_other(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not update.message.reply_to_message:
        await update.message.reply_text("❌ باید روی پیام کسی ریپلای کنی!")
        return

    target = update.message.reply_to_message.from_user
    if target.is_bot:
        await update.message.reply_text("🤖 پروفایل ربات؟ نه بابا!")
        return

    conn = get_db()
    u = conn.execute("SELECT * FROM users WHERE user_id=?", (target.id,)).fetchone()
    if not u:
        conn.close()
        await update.message.reply_text(
            f"🐾 *{target.first_name}* هنوز هاپو نزده و ثبت نشده!",
            parse_mode="Markdown"
        )
        return

    dog    = conn.execute("SELECT * FROM dogs WHERE user_id=?",            (target.id,)).fetchone()
    hook   = conn.execute("SELECT * FROM hooks WHERE user_id=?",           (target.id,)).fetchone()
    strays = conn.execute("SELECT count FROM user_strays WHERE user_id=?", (target.id,)).fetchone()
    jailed, jail_row = is_in_jail(target.id)

    r_pts = conn.execute(
        "SELECT COUNT(*)+1 FROM users WHERE hop_points > ?", (u["hop_points"],)
    ).fetchone()[0]
    r_hops = conn.execute(
        "SELECT COUNT(*)+1 FROM users WHERE total_hops > ?", (u["total_hops"],)
    ).fetchone()[0]
    stray_count = strays["count"] if strays else 0
    r_stray = conn.execute(
        "SELECT COUNT(*)+1 FROM user_strays WHERE count > ?", (stray_count,)
    ).fetchone()[0]
    conn.close()

    lvl       = u["level"]
    hops      = u["total_hops"]
    next_hops = hops_for_next_level(lvl)
    if next_hops > 0 and lvl > 1:
        prev_hops = hops_for_next_level(lvl - 1)
        progress  = hops - prev_hops
        needed    = next_hops - prev_hops
        filled    = int((progress / needed) * 5) if needed > 0 else 5
    else:
        filled, needed, progress = 5, 0, hops
    bar = "▰" * filled + "▱" * (5 - filled)

    dog_line = ""
    if dog:
        dog_rank_name, _ = DOG_RANKS.get(dog["rank"], ("نامشخص", 1))
        dog_line = (
            f"\n┐─ 🐕 سگ : {dog['name']}\n"
            f"└─ 🎖️ سطح {dog['level']} | مقام {dog_rank_name}"
        )

    hook_line = ""
    if hook:
        hook_line = f"\n└─ 🎣 قلاب : سطح {hook['level']}"

    jail_line = ""
    if jailed:
        release = datetime.fromisoformat(jail_row["release_at"])
        mins    = int((release - datetime.now()).total_seconds() // 60)
        jail_line = f"\n\n⛓️ *در زندان!* — {mins} دقیقه تا آزادی"

    display_name = f"@{target.username}" if target.username else target.first_name

    caption = (
        f"╮──「 🐾 پروفایل هاپو 🐾 」\n\n"
        f"┐─ 👤 کاربر : {display_name}\n"
        f"└─ 🪪 آیدی : `{target.id}`\n\n"
        f"┐─ 💰 هاپ پوینت : {u['hop_points']:,.0f} 🦴\n"
        f"└─ 🎖️ رتبه ({r_pts:,})\n"
        f"┐─ 🐾 هاپ‌های کل : {hops:,}\n"
        f"└─ 🎖️ رتبه ({r_hops:,})\n\n"
        f"┐─ 🐈 پیشی‌های خیابونی : {stray_count}\n"
        f"└─ 🎖️ رتبه ({r_stray:,})\n"
        f"{dog_line}"
        f"{hook_line}\n\n"
        f"╯─ ⭐️ سطح : {lvl} | {progress} / {needed if needed else '∞'} {bar}"
        f"{jail_line}"
    )

    photo_buf = None
    try:
        photos = await context.bot.get_user_profile_photos(target.id, limit=1)
        if photos.total_count > 0:
            file = await photos.photos[0][-1].get_file()
            photo_buf = io.BytesIO()
            await file.download_to_memory(photo_buf)
            photo_buf.seek(0)
    except Exception:
        pass

    if photo_buf:
        await update.message.reply_photo(photo=photo_buf, caption=caption, parse_mode="Markdown")
    else:
        await update.message.reply_text(caption, parse_mode="Markdown")

MEOW_REPLIES = [
    "اینجا قلمرو هاپوهاست، صدای گربه میاد بار آخرت باشه! 🐕☠️",
    "اشتباهی اومدی داداش، گربه‌ها رو اینجا سگ‌خور می‌کنیم! 🐕",
    "میو؟ مگه تو هاپو نیستی؟ داری جاسوسی گربه‌ها رو می‌کنی؟ 🤨",
    "یک بار دیگه صدا گربه در بیاری بچه‌ها می‌فرستنت انفرادی! 😾"
]

async def handle_meow_logic(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    message = update.effective_message
    if not message or not message.text:
        return False

    text = message.text.strip()
    
    if text != "میو":
        return False

    target_user = message.from_user
    chat_id = update.effective_chat.id
    
    reply_text = random.choice(MEOW_REPLIES)
    
    keyboard = [[
        cbtn("رای به زندانی شدن (0/3) ⚖️", callback_data=f"vmeow_{target_user.id}")
    ]]
    reply_markup = InlineKeyboardMarkup(keyboard)
    
    vote_msg = await message.reply_text(
        f"{reply_text}\n\n🚨 <b>رای‌گیری برای زندانی کردن {target_user.mention_html()} شروع شد!</b>\nاگر تا ۱ دقیقه دیگر ۳ رای جمع بشه، میره زندان!",
        reply_markup=reply_markup,
        parse_mode="HTML"
    )
    
    context.job_queue.run_once(
        delete_meow_vote_msg, 
        60, 
        chat_id=chat_id, 
        message_id=vote_msg.message_id,
        data={"target_id": target_user.id}
    )
    
    return True

async def delete_meow_vote_msg(context: ContextTypes.DEFAULT_TYPE):
    job = context.job
    target_id = job.data["target_id"]
    if "meow_votes" in context.bot_data and target_id in context.bot_data["meow_votes"]:
        del context.bot_data["meow_votes"][target_id]
    try:
        await context.bot.edit_message_reply_markup(
            chat_id=job.chat_id,
            message_id=job.message_id,
            reply_markup=None
        )
    except Exception:
        pass
    try:
        await context.bot.delete_message(chat_id=job.chat_id, message_id=job.message_id)
    except Exception:
        pass


def get_referral_setting(key, default):
    with db_conn() as conn:
        row = conn.execute("SELECT value FROM bot_settings WHERE key=?", (key,)).fetchone()
        if not row: return default
        return row["value"]

def set_referral_setting(key, value):
    with db_conn() as conn:
        conn.execute("INSERT OR REPLACE INTO bot_settings (key,value) VALUES (?,?)", (key, str(value)))
        conn.commit()

def is_referral_enabled() -> bool:
    return get_referral_setting("referral_enabled", "1") == "1"

def get_referral_reward_sender() -> int:
    return int(get_referral_setting("referral_reward_sender", str(REFERRAL_REWARD_SENDER)))

def get_referral_reward_joiner() -> int:
    return int(get_referral_setting("referral_reward_joiner", str(REFERRAL_REWARD_JOINER)))

def get_referral_count(user_id: int) -> int:
    with db_conn() as conn:
        row = conn.execute(
            "SELECT COUNT(*) FROM referrals WHERE inviter_id=? AND rewarded=1", (user_id,)
        ).fetchone()
        return row[0] if row else 0

async def referral_invite_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type != "private":
        await update.message.reply_text("🔗 لینک دعوت رو توی پیوی بهت میدم! ربات رو توی پیوی باز کن.")
        return
    if not is_referral_enabled():
        await update.message.reply_text("❌ سیستم دعوت دوستان فعلاً غیرفعاله.")
        return
    invite_link = f"https://t.me/{BOT_USERNAME}?start=ref_{user.id}"
    count = get_referral_count(user.id)
    reward_sender = get_referral_reward_sender()
    reward_joiner = get_referral_reward_joiner()
    text = (
        f"🎁 *دعوت دوستان*\n\n"
        f"لینک اختصاصی تو:\n`{invite_link}`\n\n"
        f"👥 تعداد دعوت‌های موفق: *{count} نفر*\n\n"
        f"💰 جوایز:\n"
        f"┐─ دعوت‌کننده (تو): *{reward_sender:,} هاپ پوینت*\n"
        f"└─ دعوت‌شده (دوستت): *{reward_joiner:,} هاپ پوینت*\n\n"
        f"⚡️ جایزه بعد از اولین هاپ دوستت واریز میشه!"
    )
    kb = InlineKeyboardMarkup([
        [cbtn("📤 اشتراک‌گذاری لینک", url=f"https://t.me/share/url?url={invite_link}&text=بیا%20هاپ%20داگ%20بازی%20کن!")],
        [cbtn("👥 تعداد دعوت‌هام", callback_data="ref_mystats")],
    ])
    await update.message.reply_text(text, parse_mode="Markdown", reply_markup=kb)

async def referral_admin_panel(message_or_query, edit=False):
    enabled = is_referral_enabled()
    reward_s = get_referral_reward_sender()
    reward_j = get_referral_reward_joiner()
    with db_conn() as conn:
        total = conn.execute("SELECT COUNT(*) FROM referrals WHERE rewarded=1").fetchone()[0]
    status = "🟢 فعال" if enabled else "🔴 غیرفعال"
    toggle_label = "🔴 غیرفعال کردن دعوت" if enabled else "🟢 فعال کردن دعوت"
    text = (
        f"🎁 *پنل مدیریت دعوت دوستان*\n\n"
        f"وضعیت: {status}\n"
        f"💰 جایزه دعوت‌کننده: {reward_s:,}\n"
        f"💰 جایزه دعوت‌شده: {reward_j:,}\n"
        f"👥 کل دعوت‌های موفق: {total}\n\n"
        f"یه گزینه انتخاب کن:"
    )
    kb = InlineKeyboardMarkup([
        [cbtn(toggle_label, callback_data="ref_admin_toggle")],
        [cbtn("✏️ تغییر جایزه دعوت‌کننده", callback_data="ref_admin_set_sender")],
        [cbtn("✏️ تغییر جایزه دعوت‌شده", callback_data="ref_admin_set_joiner")],
    ])
    if edit:
        await message_or_query.edit_message_text(text, parse_mode="Markdown", reply_markup=kb)
    else:
        await message_or_query.reply_text(text, parse_mode="Markdown", reply_markup=kb)

REFERRAL_ADMIN_CONV = {}

async def handle_referral_admin_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user
    if user.id not in REFERRAL_ADMIN_CONV:
        return False
    if update.effective_chat.type != "private":
        return False
    if not is_admin(user.id):
        del REFERRAL_ADMIN_CONV[user.id]
        return False
    text = update.message.text.strip()
    field = REFERRAL_ADMIN_CONV[user.id]
    try:
        amount = int(text.replace(",", "").replace("،", ""))
        if amount < 0: raise ValueError
    except ValueError:
        await update.message.reply_text("❌ عدد معتبر وارد کن!")
        return True
    if field == "sender":
        set_referral_setting("referral_reward_sender", amount)
        await update.message.reply_text(f"✅ جایزه دعوت‌کننده به *{amount:,}* تغییر کرد!", parse_mode="Markdown")
    else:
        set_referral_setting("referral_reward_joiner", amount)
        await update.message.reply_text(f"✅ جایزه دعوت‌شده به *{amount:,}* تغییر کرد!", parse_mode="Markdown")
    del REFERRAL_ADMIN_CONV[user.id]
    return True

async def handle_referral_callbacks(query, data, user, context) -> bool:
    uid = user.id

    if data == "ref_mystats":
        count = get_referral_count(uid)
        reward_s = get_referral_reward_sender()
        await query.answer()
        await query.edit_message_text(
            f"👥 *دعوت‌های موفق تو: {count} نفر*\n\n"
            f"💰 جمع جایزه دریافتی: {count * reward_s:,} هاپ پوینت\n\n"
            f"هر دوستی که با لینک تو بیاد و اولین هاپش رو بزنه = +{reward_s:,} برای تو!",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="ref_back")]])
        )
        return True

    if data == "ref_back":
        await query.answer()
        await referral_invite_cmd_query(query, user, context)
        return True

    if data == "ref_admin_toggle":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        current = is_referral_enabled()
        set_referral_setting("referral_enabled", "0" if current else "1")
        await query.answer("✅ وضعیت تغییر کرد!")
        await referral_admin_panel(query, edit=True)
        return True

    if data == "ref_admin_set_sender":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        REFERRAL_ADMIN_CONV[uid] = "sender"
        await query.edit_message_text(
            "✏️ مقدار جدید جایزه دعوت‌کننده رو بنویس (هاپ پوینت):\n\nبرای لغو بنویس: لغو"
        )
        return True

    if data == "ref_admin_set_joiner":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        REFERRAL_ADMIN_CONV[uid] = "joiner"
        await query.edit_message_text(
            "✏️ مقدار جدید جایزه دعوت‌شده رو بنویس (هاپ پوینت):\n\nبرای لغو بنویس: لغو"
        )
        return True

    return False

async def referral_invite_cmd_query(query, user, context):
    if not is_referral_enabled():
        await query.edit_message_text("❌ سیستم دعوت دوستان فعلاً غیرفعاله.")
        return
    invite_link = f"https://t.me/{BOT_USERNAME}?start=ref_{user.id}"
    count = get_referral_count(user.id)
    reward_sender = get_referral_reward_sender()
    reward_joiner = get_referral_reward_joiner()
    text = (
        f"🎁 *دعوت دوستان*\n\n"
        f"لینک اختصاصی تو:\n`{invite_link}`\n\n"
        f"👥 تعداد دعوت‌های موفق: *{count} نفر*\n\n"
        f"💰 جوایز:\n"
        f"┐─ دعوت‌کننده (تو): *{reward_sender:,} هاپ پوینت*\n"
        f"└─ دعوت‌شده (دوستت): *{reward_joiner:,} هاپ پوینت*\n\n"
        f"⚡️ جایزه بعد از اولین هاپ دوستت واریز میشه!"
    )
    kb = InlineKeyboardMarkup([
        [cbtn("📤 اشتراک‌گذاری لینک", url=f"https://t.me/share/url?url={invite_link}&text=بیا%20هاپ%20داگ%20بازی%20کن!")],
        [cbtn("👥 تعداد دعوت‌هام", callback_data="ref_mystats")],
    ])
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=kb)


import uuid as _uuid

def market_id_gen():
    return _uuid.uuid4().hex[:10]

def is_market_open() -> bool:
    with db_conn() as conn:
        row = conn.execute("SELECT value FROM bot_settings WHERE key='market_open'").fetchone()
        if not row: return True
        return row["value"] == "1"

def set_market_open(val: bool):
    with db_conn() as conn:
        conn.execute(
            "INSERT OR REPLACE INTO bot_settings (key,value) VALUES ('market_open',?)",
            ("1" if val else "0",)
        )
        conn.commit()

def get_listing(listing_id):
    with db_conn() as conn:
        return conn.execute("SELECT * FROM user_market WHERE listing_id=?", (listing_id,)).fetchone()

def get_active_listings():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM user_market WHERE status='active' ORDER BY created_at DESC"
        ).fetchall()

def get_seller_listings(seller_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM user_market WHERE seller_id=? ORDER BY created_at DESC", (seller_id,)
        ).fetchall()

def get_pending_listings():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM user_market WHERE status='pending' ORDER BY created_at ASC"
        ).fetchall()

async def market_admin_panel(update_or_query, edit=False):
    pendings = get_pending_listings()
    actives  = get_active_listings()
    market_status = "🟢 باز" if is_market_open() else "🔴 تعطیل"
    text = (
        f"🛒 *پنل مدیریت مارکت*\n\n"
        f"وضعیت مارکت: {market_status}\n"
        f"⏳ در انتظار تأیید: {len(pendings)} آگهی\n"
        f"✅ فعال: {len(actives)} آگهی\n\n"
        f"یه گزینه انتخاب کن:"
    )
    toggle_label = "🔴 تعطیل کردن مارکت" if is_market_open() else "🟢 باز کردن مارکت"
    kb = InlineKeyboardMarkup([
        [cbtn(f"⏳ بررسی آگهی‌های در انتظار ({len(pendings)})", callback_data="mkt_admin_pending")],
        [cbtn(f"📋 لیست آگهی‌های فعال", callback_data="mkt_admin_active")],
        [cbtn(toggle_label, callback_data="mkt_admin_toggle")],
    ])
    if edit:
        await update_or_query.edit_message_text(text, parse_mode="Markdown", reply_markup=kb)
    else:
        await update_or_query.reply_text(text, parse_mode="Markdown", reply_markup=kb)

async def market_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_type = update.effective_chat.type
    user = update.effective_user

    if chat_type == "private":
        await market_private_panel(update.message, user, context)
    else:
        if not is_market_open():
            await update.message.reply_text("🔴 مارکت در حال حاضر تعطیله!")
            return
        listings = get_active_listings()
        if not listings:
            await update.message.reply_text("🛒 مارکت خالیه! هنوز آگهی فعالی ثبت نشده.")
            return
        text = "🛒 *مارکت هاپ‌داگ*\n\n"
        kb_rows = []
        for l in listings:
            remaining = l["max_buyers"] - l["buyer_count"]
            text += (
                f"🏷 *{l['title']}*\n"
                f"👤 فروشنده: {l['seller_name']}\n"
                f"💰 قیمت: {l['price']:,} هاپ پوینت\n"
                f"📦 ظرفیت باقی‌مانده: {remaining}\n"
                f"───────────────\n"
            )
            kb_rows.append([cbtn(f"🛍 خرید «{l['title']}»", callback_data=f"mkt_buy_{l['listing_id']}")])
        await update.message.reply_text(
            text, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup(kb_rows)
        )

async def market_private_panel(message, user, context):
    my_listings = get_seller_listings(user.id)
    active = [l for l in my_listings if l["status"] == "active"]
    pending = [l for l in my_listings if l["status"] == "pending"]
    cancelled = [l for l in my_listings if l["status"] == "cancelled"]
    text = (
        f"🛒 *پنل مارکت شخصی*\n\n"
        f"✅ فعال: {len(active)} آگهی\n"
        f"⏳ در انتظار تأیید: {len(pending)} آگهی\n"
        f"❌ لغو‌شده: {len(cancelled)} آگهی\n\n"
        f"یه گزینه انتخاب کن:"
    )
    kb = [[cbtn("➕ ثبت آگهی جدید", callback_data="mkt_new")]]
    if active or pending:
        kb.append([cbtn("📋 آگهی‌های من", callback_data="mkt_my_listings")])
    await message.reply_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

MARKET_CONV = {}

async def handle_market_private_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user
    if user.id not in MARKET_CONV:
        return False
    if update.effective_chat.type != "private":
        return False
    if not update.message or not update.message.text:
        return False

    text = update.message.text.strip()
    state = MARKET_CONV[user.id]
    step = state["step"]

    if text == "لغو" or text == "انصراف":
        del MARKET_CONV[user.id]
        await update.message.reply_text("❌ ثبت آگهی لغو شد.")
        return True

    if step == "title":
        if len(text) > 50:
            await update.message.reply_text("❌ اسم محصول حداکثر ۵۰ کاراکتر!")
            return True
        state["title"] = text
        state["step"] = "description"
        await update.message.reply_text(
            "📝 *توضیح محصول:*\nیه توضیح کوتاه بنویس که خریدار قبل از خرید بخونه.\n_(محتوای اصلی بعداً پرسیده میشه)_\n\nبرای لغو بنویس: لغو",
            parse_mode="Markdown"
        )

    elif step == "description":
        if len(text) > 200:
            await update.message.reply_text("❌ توضیح حداکثر ۲۰۰ کاراکتر!")
            return True
        state["description"] = text
        state["step"] = "price"
        await update.message.reply_text(
            "💰 *قیمت (هاپ پوینت):*\nچند هاپ پوینت میخوای بفروشی؟\n\nبرای لغو بنویس: لغو",
            parse_mode="Markdown"
        )

    elif step == "price":
        try:
            price = int(text.replace(",", "").replace("،", ""))
            if price < 1:
                raise ValueError
        except ValueError:
            await update.message.reply_text("❌ قیمت باید یه عدد مثبت باشه!")
            return True
        state["price"] = price
        state["step"] = "max_buyers"
        await update.message.reply_text(
            "👥 *حداکثر تعداد خریدار:*\nچند نفر میتونن این آگهی رو بخرن؟\n_(بعد از رسیدن به این تعداد، آگهی خودکار حذف میشه)_\n\nبرای لغو بنویس: لغو",
            parse_mode="Markdown"
        )

    elif step == "max_buyers":
        try:
            max_b = int(text)
            if max_b < 1 or max_b > 1000:
                raise ValueError
        except ValueError:
            await update.message.reply_text("❌ عدد بین ۱ تا ۱۰۰۰ وارد کن!")
            return True
        state["max_buyers"] = max_b
        state["step"] = "content"
        await update.message.reply_text(
            "📦 *محتوای آگهی:*\nاین چیزیه که بعد از خرید، توی پیوی خریدار فرستاده میشه.\n"
            "میتونه لینک، آیدی، متن یا هر چیزی باشه.\n\n"
            "⚠️ این محتوا فقط برای ادمین و خریدار قابل دیدنه.\n\nبرای لغو بنویس: لغو",
            parse_mode="Markdown"
        )

    elif step == "content":
        state["content"] = text
        state["step"] = "confirm"
        d = state
        await update.message.reply_text(
            f"✅ *بررسی آگهی:*\n\n"
            f"🏷 نام: {d['title']}\n"
            f"📝 توضیح: {d['description']}\n"
            f"💰 قیمت: {d['price']:,} هاپ پوینت\n"
            f"👥 حداکثر خریدار: {d['max_buyers']} نفر\n"
            f"📦 محتوا: {d['content']}\n\n"
            f"آیا تأیید میکنی؟",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("✅ ارسال برای تأیید ادمین", callback_data="mkt_submit_confirm")],
                [cbtn("❌ لغو", callback_data="mkt_submit_cancel")],
            ])
        )

    return True

async def handle_market_callbacks(query, data, user, context) -> bool:
    uid = user.id

    if data == "mkt_admin_toggle":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین اصلی!", show_alert=True); return True
        current = is_market_open()
        set_market_open(not current)
        await query.answer("✅ وضعیت مارکت تغییر کرد!")
        await market_admin_panel(query, edit=True)
        return True

    if data == "mkt_admin_pending":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        pendings = get_pending_listings()
        if not pendings:
            await query.edit_message_text("✅ هیچ آگهی در انتظاری نیست!", reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_admin_back")]]))
            return True
        kb = []
        for l in pendings:
            kb.append([cbtn(f"👁 {l['title']} — {l['seller_name']}", callback_data=f"mkt_admin_review_{l['listing_id']}")])
        kb.append([cbtn("🔙 برگشت", callback_data="mkt_admin_back")])
        await query.edit_message_text(
            f"⏳ *آگهی‌های در انتظار تأیید ({len(pendings)} عدد):*",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb)
        )
        return True

    if data.startswith("mkt_admin_review_"):
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        lid = data.split("_", 3)[3]
        l = get_listing(lid)
        if not l:
            await query.edit_message_text("❌ آگهی پیدا نشد!"); return True
        await query.edit_message_text(
            f"📋 *بررسی آگهی*\n\n"
            f"🏷 نام: {l['title']}\n"
            f"👤 فروشنده: {l['seller_name']} (آیدی: `{l['seller_id']}`)\n"
            f"📝 توضیح: {l['description']}\n"
            f"💰 قیمت: {l['price']:,} هاپ پوینت\n"
            f"👥 حداکثر خریدار: {l['max_buyers']}\n"
            f"📦 محتوا (برای خریدار): {l['content']}\n"
            f"📅 ثبت شده: {l['created_at'][:16]}",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("✅ تأیید", callback_data=f"mkt_admin_approve_{lid}"),
                 cbtn("❌ رد", callback_data=f"mkt_admin_reject_{lid}")],
                [cbtn("🔙 برگشت", callback_data="mkt_admin_pending")],
            ])
        )
        return True

    if data.startswith("mkt_admin_approve_"):
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        lid = data.split("_", 3)[3]
        with db_conn() as conn:
            conn.execute("UPDATE user_market SET status='active' WHERE listing_id=?", (lid,))
            conn.commit()
        l = get_listing(lid)
        await query.edit_message_text(f"✅ آگهی *{l['title']}* تأیید و در مارکت منتشر شد!", parse_mode="Markdown",
                                      reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_admin_pending")]]))
        try:
            await context.bot.send_message(
                chat_id=l["seller_id"],
                text=f"✅ *آگهیت تأیید شد!*\n\n🏷 «{l['title']}» الان توی مارکت فعاله و قابل خریده.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    if data.startswith("mkt_admin_reject_"):
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        lid = data.split("_", 3)[3]
        with db_conn() as conn:
            conn.execute("UPDATE user_market SET status='rejected' WHERE listing_id=?", (lid,))
            conn.commit()
        l = get_listing(lid)
        await query.edit_message_text(f"❌ آگهی *{l['title']}* رد شد.", parse_mode="Markdown",
                                      reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_admin_pending")]]))
        try:
            await context.bot.send_message(
                chat_id=l["seller_id"],
                text=f"❌ *آگهیت رد شد!*\n\n🏷 «{l['title']}» توسط ادمین تأیید نشد.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    if data == "mkt_admin_active":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        actives = get_active_listings()
        if not actives:
            await query.edit_message_text("📋 هیچ آگهی فعالی وجود نداره!", reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_admin_back")]]))
            return True
        kb = []
        for l in actives:
            remaining = l["max_buyers"] - l["buyer_count"]
            kb.append([cbtn(f"🏷 {l['title']} | {remaining} باقی", callback_data=f"mkt_admin_deactivate_{l['listing_id']}")])
        kb.append([cbtn("🔙 برگشت", callback_data="mkt_admin_back")])
        await query.edit_message_text("📋 *آگهی‌های فعال* (برای لغو انتخاب کن):", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mkt_admin_deactivate_"):
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        lid = data.split("_", 3)[3]
        with db_conn() as conn:
            conn.execute("UPDATE user_market SET status='cancelled' WHERE listing_id=?", (lid,))
            conn.commit()
        l = get_listing(lid)
        await query.answer(f"❌ آگهی {l['title']} لغو شد!")
        await query.edit_message_text(f"❌ آگهی *{l['title']}* لغو شد.", parse_mode="Markdown",
                                      reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_admin_active")]]))
        try:
            await context.bot.send_message(chat_id=l["seller_id"],
                text=f"⚠️ آگهیت «{l['title']}» توسط ادمین لغو شد.", parse_mode="Markdown")
        except Exception:
            pass
        return True

    if data == "mkt_admin_back":
        if not is_admin(uid):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        await market_admin_panel(query, edit=True)
        return True

    if data == "mkt_new":
        if not is_market_open():
            await query.answer("🔴 مارکت الان تعطیله!", show_alert=True); return True
        MARKET_CONV[uid] = {"step": "title", "data": {}}
        await query.edit_message_text(
            "🏷 *اسم محصول:*\nیه اسم کوتاه و واضح برای آگهیت بنویس.\n\nبرای لغو بنویس: لغو",
            parse_mode="Markdown"
        )
        return True

    if data == "mkt_submit_confirm":
        if uid not in MARKET_CONV:
            await query.answer("❌ اطلاعات پیدا نشد!", show_alert=True); return True
        state = MARKET_CONV[uid]
        lid = market_id_gen()
        with db_conn() as conn:
            conn.execute(
                "INSERT INTO user_market (listing_id,seller_id,seller_name,title,description,content,price,max_buyers,status) VALUES (?,?,?,?,?,?,?,?,'pending')",
                (lid, uid, user.full_name, state["title"], state["description"], state["content"], state["price"], state["max_buyers"])
            )
            conn.commit()
        del MARKET_CONV[uid]
        await query.edit_message_text(
            f"✅ *آگهیت ثبت شد!*\n\n«{state['title']}» برای تأیید ادمین فرستاده شد.\nبعد از تأیید، توی مارکت منتشر میشه و بهت خبر میدم.",
            parse_mode="Markdown"
        )
        for admin_id in ADMIN_IDS:
            try:
                await context.bot.send_message(
                    chat_id=admin_id,
                    text=f"🔔 *آگهی جدید در انتظار تأیید*\n\n🏷 {state['title']}\n👤 {user.full_name}\n💰 {state['price']:,} هاپ پوینت\n\nبرای بررسی بنویس: مارکت",
                    parse_mode="Markdown"
                )
            except Exception:
                pass
        return True

    if data == "mkt_submit_cancel":
        if uid in MARKET_CONV:
            del MARKET_CONV[uid]
        await query.edit_message_text("❌ ثبت آگهی لغو شد.")
        return True

    if data == "mkt_my_listings":
        listings = get_seller_listings(uid)
        if not listings:
            await query.edit_message_text("📋 هنوز هیچ آگهیی نداری!", reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_my_back")]]))
            return True
        kb = []
        for l in listings:
            emoji = {"active": "✅", "pending": "⏳", "cancelled": "❌", "rejected": "🚫"}.get(l["status"], "❓")
            kb.append([cbtn(f"{emoji} {l['title']} | {l['buyer_count']}/{l['max_buyers']} خریدار", callback_data=f"mkt_my_detail_{l['listing_id']}")])
        kb.append([cbtn("🔙 برگشت", callback_data="mkt_my_back")])
        await query.edit_message_text("📋 *آگهی‌های من:*", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mkt_my_detail_"):
        lid = data.split("_", 3)[3]
        l = get_listing(lid)
        if not l or l["seller_id"] != uid:
            await query.answer("❌ آگهی پیدا نشد!", show_alert=True); return True
        status_text = {"active": "✅ فعال", "pending": "⏳ در انتظار تأیید", "cancelled": "❌ لغو شده", "rejected": "🚫 رد شده"}.get(l["status"], "؟")
        text = (
            f"🏷 *{l['title']}*\n\n"
            f"📊 وضعیت: {status_text}\n"
            f"💰 قیمت: {l['price']:,} هاپ پوینت\n"
            f"👥 خریداران: {l['buyer_count']} از {l['max_buyers']} نفر\n"
            f"📝 توضیح: {l['description']}\n"
            f"📅 ثبت شده: {l['created_at'][:16]}"
        )
        kb = []
        if l["status"] == "active":
            kb.append([cbtn("❌ لغو آگهی", callback_data=f"mkt_my_cancel_{lid}")])
        kb.append([cbtn("🔙 برگشت", callback_data="mkt_my_listings")])
        await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mkt_my_cancel_"):
        lid = data.split("_", 3)[3]
        l = get_listing(lid)
        if not l or l["seller_id"] != uid:
            await query.answer("❌ مجاز نیستی!", show_alert=True); return True
        with db_conn() as conn:
            conn.execute("UPDATE user_market SET status='cancelled' WHERE listing_id=?", (lid,))
            conn.commit()
        await query.answer("❌ آگهی لغو شد!")
        await query.edit_message_text(f"❌ آگهی *{l['title']}* لغو شد.", parse_mode="Markdown",
                                      reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data="mkt_my_listings")]]))
        return True

    if data == "mkt_my_back":
        from telegram import Message
        await query.edit_message_text(
            "🛒 *پنل مارکت شخصی*\n\nیه گزینه انتخاب کن:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("➕ ثبت آگهی جدید", callback_data="mkt_new")],
                [cbtn("📋 آگهی‌های من", callback_data="mkt_my_listings")],
            ])
        )
        return True

    if data.startswith("mkt_buy_") and not data.startswith("mkt_buy_confirm_"):
        if not is_market_open():
            await query.answer("🔴 مارکت الان تعطیله!", show_alert=True); return True
        lid = data.split("_", 2)[2]
        l = get_listing(lid)
        if not l or l["status"] != "active":
            await query.answer("❌ این آگهی دیگه فعال نیست!", show_alert=True); return True
        if l["seller_id"] == uid:
            await query.answer("❌ نمیتونی محصول خودت رو بخری!", show_alert=True); return True
        with db_conn() as conn:
            already = conn.execute("SELECT 1 FROM user_market_buyers WHERE listing_id=? AND buyer_id=?", (lid, uid)).fetchone()
        if already:
            await query.answer("❌ قبلاً این رو خریدی!", show_alert=True); return True
        u = get_user(uid)
        if not u or u["hop_points"] < l["price"]:
            await query.answer(f"❌ پوینت کافی نداری! لازم: {l['price']:,}", show_alert=True); return True
        await query.answer()
        await query.message.reply_text(
            f"🛍 *تأیید خرید*\n\n"
            f"🏷 {l['title']}\n"
            f"💰 قیمت: {l['price']:,} هاپ پوینت\n"
            f"📝 {l['description']}\n\n"
            f"مطمئنی؟",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("✅ بله، خریدم!", callback_data=f"mkt_buy_confirm_{lid}"),
                 cbtn("❌ انصراف", callback_data="cancel")],
            ])
        )
        return True

    if data.startswith("mkt_buy_confirm_"):
        lid = data.split("_", 3)[3]
        l = get_listing(lid)
        if not l or l["status"] != "active":
            await query.answer("❌ آگهی دیگه فعال نیست!", show_alert=True); return True
        if l["seller_id"] == uid:
            await query.answer("❌ نمیتونی محصول خودت رو بخری!", show_alert=True); return True
        with db_conn() as conn:
            already = conn.execute("SELECT 1 FROM user_market_buyers WHERE listing_id=? AND buyer_id=?", (lid, uid)).fetchone()
            if already:
                await query.answer("❌ قبلاً خریدی!", show_alert=True); return True
            u = conn.execute("SELECT hop_points FROM users WHERE user_id=?", (uid,)).fetchone()
            if not u or u["hop_points"] < l["price"]:
                await query.answer(f"❌ پوینت کافی نداری!", show_alert=True); return True
            conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (l["price"], uid))
            conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?", (l["price"], l["seller_id"]))
            conn.execute("INSERT INTO user_market_buyers (listing_id,buyer_id) VALUES (?,?)", (lid, uid))
            new_count = l["buyer_count"] + 1
            if new_count >= l["max_buyers"]:
                conn.execute("UPDATE user_market SET buyer_count=?, status='done' WHERE listing_id=?", (new_count, lid))
            else:
                conn.execute("UPDATE user_market SET buyer_count=? WHERE listing_id=?", (new_count, lid))
            conn.commit()
        await query.edit_message_text(
            f"✅ *خرید موفق!*\n\n💰 {l['price']:,} هاپ پوینت کسر شد.\nمحتوای آگهی توی پیوی برات فرستاده شد 📩",
            parse_mode="Markdown"
        )
        try:
            await context.bot.send_message(
                chat_id=uid,
                text=f"📦 *محتوای خریدت:*\n\n🏷 {l['title']}\n\n{l['content']}",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        try:
            await context.bot.send_message(
                chat_id=l["seller_id"],
                text=f"🛍 *یه نفر آگهیت رو خرید!*\n\n🏷 {l['title']}\n💰 +{l['price']:,} هاپ پوینت دریافت کردی\n👥 خریداران: {new_count}/{l['max_buyers']}",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    return False


def lottery_id_gen():
    import uuid
    return uuid.uuid4().hex[:10]

def get_lottery(lottery_id):
    with db_conn() as conn:
        return conn.execute("SELECT * FROM lotteries WHERE lottery_id=?", (lottery_id,)).fetchone()

def get_all_lotteries():
    with db_conn() as conn:
        return conn.execute("SELECT * FROM lotteries ORDER BY created_at DESC").fetchall()

def get_lottery_entries(lottery_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM lottery_entries WHERE lottery_id=? ORDER BY joined_at ASC",
            (lottery_id,)
        ).fetchall()

def get_lottery_entry(lottery_id, user_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM lottery_entries WHERE lottery_id=? AND user_id=?",
            (lottery_id, user_id)
        ).fetchone()

def lottery_participant_text(entries):
    if not entries:
        return "هنوز کسی شرکت نکرده 😿"
    lines = []
    for i, e in enumerate(entries, 1):
        uname = f"@{e['username']}" if e['username'] else "—"
        lines.append(f"{i}. {e['first_name']} ({uname})")
    return "\n".join(lines)

def lottery_announce_text(lot, entries):
    return (
        f"🎰 *قرعه‌کشی: {lot['title']}*\n\n"
        f"🎁 جایزه: {lot['prize']:,} هاپ پوینت\n"
        f"🏆 تعداد برنده: {lot['winner_count']} نفر\n\n"
        f"👥 شرکت‌کنندگان ({len(entries)} نفر):\n"
        f"{lottery_participant_text(entries)}"
    )

async def lottery_admin_panel(message_obj, edit=False):
    lotteries = get_all_lotteries()
    open_lots = [l for l in lotteries if l['state'] == 'open']
    done_lots = [l for l in lotteries if l['state'] == 'done']
    cancelled = [l for l in lotteries if l['state'] == 'cancelled']

    text = (
        f"🎰 *پنل مدیریت قرعه‌کشی*\n\n"
        f"🟢 باز: {len(open_lots)}\n"
        f"✅ انجام‌شده: {len(done_lots)}\n"
        f"❌ لغو‌شده: {len(cancelled)}\n\n"
        f"یه گزینه انتخاب کن:"
    )
    kb = InlineKeyboardMarkup([
        [cbtn("➕ ساخت قرعه‌کشی جدید", callback_data="lot_new")],
        [cbtn("📋 لیست همه قرعه‌کشی‌ها", callback_data="lot_list_0")],
        [cbtn("🟢 قرعه‌کشی‌های باز", callback_data="lot_open")],
    ])
    if edit:
        await message_obj.edit_message_text(text, parse_mode="Markdown", reply_markup=kb)
    else:
        await message_obj.reply_text(text, parse_mode="Markdown", reply_markup=kb)

async def lottery_panel_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    if user.id not in ADMIN_IDS:
        return
    await lottery_admin_panel(update.message)

async def do_lottery_draw(lottery_id, context):
    lot = get_lottery(lottery_id)
    if not lot or lot['state'] != 'open':
        return
    entries = get_lottery_entries(lottery_id)
    if not entries:
        with db_conn() as conn:
            conn.execute("UPDATE lotteries SET state='cancelled' WHERE lottery_id=?", (lottery_id,))
            conn.commit()
        return

    import random as _random
    winner_count = min(lot['winner_count'], len(entries))
    winners = _random.sample(list(entries), winner_count)
    prize_each = lot['prize'] // winner_count

    with db_conn() as conn:
        for w in winners:
            conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?",
                         (prize_each, w['user_id']))
        conn.execute("UPDATE lotteries SET state='done' WHERE lottery_id=?", (lottery_id,))
        conn.commit()

    winner_lines = []
    for w in winners:
        uname = f"@{w['username']}" if w['username'] else "—"
        winner_lines.append(
            f"🏆 *{w['first_name']}*\n"
            f"   🆔 آیدی: `{w['user_id']}`\n"
            f"   👤 یوزرنیم: {uname}\n"
            f"   💰 جایزه: {prize_each:,} هاپ پوینت"
        )
    announce = (
        f"🎉 *نتیجه قرعه‌کشی: {lot['title']}*\n\n"
        f"از بین {len(entries)} نفر شرکت‌کننده، "
        f"{winner_count} نفر انتخاب شدن:\n\n"
        + "\n\n".join(winner_lines) +
        f"\n\n✨ تبریک به برندگان!"
    )

    with db_conn() as conn:
        groups = conn.execute("SELECT group_id FROM groups").fetchall()
    for g in groups:
        try:
            await context.bot.send_message(
                chat_id=g['group_id'],
                text=announce,
                parse_mode="Markdown"
            )
        except Exception:
            pass

async def lottery_callback_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    query = update.callback_query
    data = query.data
    user = query.from_user

    if data == "lot_panel":
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        await query.answer()
        await lottery_admin_panel(query, edit=True)
        return True

    if data == "lot_new":
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        await query.answer()
        context.user_data["lot_create"] = {"step": "title"}
        await query.edit_message_text(
            "🎰 *ساخت قرعه‌کشی جدید*\n\n"
            "📝 اسم قرعه‌کشی رو بنویس:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[
                cbtn("❌ انصراف", callback_data="lot_panel")
            ]])
        )
        return True

    if data.startswith("lot_list_"):
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        await query.answer()
        page = int(data.split("_")[2])
        lotteries = get_all_lotteries()
        per_page = 5
        total = len(lotteries)
        start = page * per_page
        page_lots = lotteries[start:start+per_page]
        state_emoji = {"open": "🟢", "done": "✅", "cancelled": "❌"}
        lines = []
        kb_rows = []
        for i, l in enumerate(page_lots):
            emoji = state_emoji.get(l['state'], "❓")
            entries = get_lottery_entries(l['lottery_id'])
            lines.append(f"{emoji} *{l['title']}* — {len(entries)} نفر — جایزه: {l['prize']:,}")
            kb_rows.append([cbtn(
                f"{emoji} {l['title']}", callback_data=f"lot_view_{l['lottery_id']}"
            )])
        nav = []
        if page > 0:
            nav.append(cbtn("◀️ قبلی", callback_data=f"lot_list_{page-1}"))
        if start + per_page < total:
            nav.append(cbtn("بعدی ▶️", callback_data=f"lot_list_{page+1}"))
        if nav:
            kb_rows.append(nav)
        kb_rows.append([cbtn("🔙 پنل", callback_data="lot_panel")])
        text = f"📋 *همه قرعه‌کشی‌ها* ({total} تا)\n\n" + ("\n".join(lines) if lines else "هیچ قرعه‌کشی‌ای نیست!")
        await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb_rows))
        return True

    if data == "lot_open":
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        await query.answer()
        lotteries = [l for l in get_all_lotteries() if l['state'] == 'open']
        kb_rows = []
        for l in lotteries:
            entries = get_lottery_entries(l['lottery_id'])
            kb_rows.append([cbtn(
                f"🟢 {l['title']} — {len(entries)} نفر", callback_data=f"lot_view_{l['lottery_id']}"
            )])
        kb_rows.append([cbtn("🔙 پنل", callback_data="lot_panel")])
        text = f"🟢 *قرعه‌کشی‌های باز* ({len(lotteries)} تا)"
        if not lotteries:
            text += "\n\nهیچ قرعه‌کشی بازی نیست!"
        await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb_rows))
        return True

    if data.startswith("lot_view_"):
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        await query.answer()
        lot_id = data[9:]
        lot = get_lottery(lot_id)
        if not lot:
            await query.edit_message_text("❌ قرعه‌کشی پیدا نشد!")
            return True
        entries = get_lottery_entries(lot_id)
        state_map = {"open": "🟢 باز", "done": "✅ انجام‌شده", "cancelled": "❌ لغو"}
        text = (
            f"🎰 *{lot['title']}*\n\n"
            f"📌 وضعیت: {state_map.get(lot['state'], lot['state'])}\n"
            f"🎁 جایزه کل: {lot['prize']:,} هاپ پوینت\n"
            f"🏆 تعداد برنده: {lot['winner_count']} نفر\n"
            f"👥 شرکت‌کنندگان: {len(entries)} نفر\n\n"
            f"{lottery_participant_text(entries)}"
        )
        kb_rows = []
        if lot['state'] == 'open':
            kb_rows.append([
                cbtn("🎲 قرعه‌کشی همین الان!", callback_data=f"lot_draw_{lot_id}"),
                cbtn("❌ لغو", callback_data=f"lot_cancel_{lot_id}"),
            ])
        kb_rows.append([cbtn("🔙 برگشت", callback_data="lot_list_0")])
        await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb_rows))
        return True

    if data.startswith("lot_cancel_"):
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        lot_id = data[11:]
        lot = get_lottery(lot_id)
        if not lot or lot['state'] != 'open':
            await query.answer("❌ نمیشه لغو کرد!", show_alert=True); return True
        await query.answer()
        kb = InlineKeyboardMarkup([[
            cbtn("✅ بله، لغو کن", callback_data=f"lot_cancel_confirm_{lot_id}"),
            cbtn("❌ نه", callback_data=f"lot_view_{lot_id}"),
        ]])
        await query.edit_message_text(
            f"⚠️ مطمئنی میخوای قرعه‌کشی *{lot['title']}* رو لغو کنی?",
            parse_mode="Markdown", reply_markup=kb
        )
        return True

    if data.startswith("lot_cancel_confirm_"):
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        lot_id = data[19:]
        with db_conn() as conn:
            conn.execute("UPDATE lotteries SET state='cancelled' WHERE lottery_id=?", (lot_id,))
            conn.commit()
        await query.answer("❌ قرعه‌کشی لغو شد!")
        await query.edit_message_text("❌ *قرعه‌کشی لغو شد.*", parse_mode="Markdown",
                                       reply_markup=InlineKeyboardMarkup([[
                                           cbtn("🔙 پنل", callback_data="lot_panel")
                                       ]]))
        return True

    if data.startswith("lot_draw_"):
        if user.id not in ADMIN_IDS:
            await query.answer("فقط ادمین!", show_alert=True); return True
        lot_id = data[9:]
        lot = get_lottery(lot_id)
        if not lot or lot['state'] != 'open':
            await query.answer("❌ قرعه‌کشی باز نیست!", show_alert=True); return True
        entries = get_lottery_entries(lot_id)
        if not entries:
            await query.answer("❌ هیچ شرکت‌کننده‌ای نیست!", show_alert=True); return True
        await query.answer("🎲 در حال قرعه‌کشی...")
        await query.edit_message_text("⏳ *در حال قرعه‌کشی...*", parse_mode="Markdown")
        await do_lottery_draw(lot_id, context)
        await query.edit_message_text(
            "✅ *قرعه‌کشی انجام شد! نتایج به همه گروه‌ها ارسال شد.*",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[
                cbtn("🔙 پنل", callback_data="lot_panel")
            ]])
        )
        return True

    if data.startswith("lot_join_"):
        lot_id = data[9:]
        lot = get_lottery(lot_id)
        if not lot or lot['state'] != 'open':
            await query.answer("❌ این قرعه‌کشی دیگه باز نیست!", show_alert=True); return True
        if get_lottery_entry(lot_id, user.id):
            await query.answer("✅ قبلاً ثبت‌نام کردی!", show_alert=True); return True
        ensure_user(user.id, user.username or "", user.first_name)
        with db_conn() as conn:
            conn.execute(
                "INSERT OR IGNORE INTO lottery_entries (lottery_id,user_id,username,first_name) VALUES (?,?,?,?)",
                (lot_id, user.id, user.username or "", user.first_name)
            )
            conn.commit()
        entries = get_lottery_entries(lot_id)
        lot = get_lottery(lot_id)
        await query.answer(f"✅ ثبت‌نام شدی! ({len(entries)} نفر تا الان)")
        new_text = lottery_announce_text(lot, entries)
        kb = InlineKeyboardMarkup([[
            cbtn(f"✋ شرکت در قرعه‌کشی ({len(entries)} نفر)", callback_data=f"lot_join_{lot_id}")
        ]])
        try:
            await query.edit_message_text(new_text, parse_mode="Markdown", reply_markup=kb)
        except Exception:
            pass
        return True

    return False

async def lottery_text_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    lot_state = context.user_data.get("lot_create")
    if not lot_state:
        return False
    user = update.effective_user
    if user.id not in ADMIN_IDS:
        return False
    text = update.message.text.strip()

    if lot_state["step"] == "title":
        context.user_data["lot_create"]["title"] = text
        context.user_data["lot_create"]["step"] = "prize"
        await update.message.reply_text(
            f"✅ اسم: *{text}*\n\n💰 حالا مبلغ جایزه رو بنویس (عدد، هاپ پوینت):",
            parse_mode="Markdown"
        )
        return True

    if lot_state["step"] == "prize":
        try:
            prize = int(text.replace(",", ""))
            if prize <= 0: raise ValueError
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        context.user_data["lot_create"]["prize"] = prize
        context.user_data["lot_create"]["step"] = "winner_count"
        await update.message.reply_text(
            f"✅ جایزه: *{prize:,}* هاپ پوینت\n\n🏆 چند نفر برنده بشن؟ (عدد بنویس):",
            parse_mode="Markdown"
        )
        return True

    if lot_state["step"] == "winner_count":
        try:
            wc = int(text)
            if wc <= 0: raise ValueError
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        lot_state["winner_count"] = wc
        context.user_data.pop("lot_create", None)

        title = lot_state["title"]
        prize = lot_state["prize"]
        lot_id = lottery_id_gen()

        with db_conn() as conn:
            conn.execute(
                "INSERT INTO lotteries (lottery_id,title,prize,winner_count,state,created_by) VALUES (?,?,?,?,?,?)",
                (lot_id, title, prize, wc, 'open', user.id)
            )
            conn.commit()

        lot = get_lottery(lot_id)
        entries = []
        announce_text = lottery_announce_text(lot, entries)
        kb = InlineKeyboardMarkup([[
            cbtn("✋ شرکت در قرعه‌کشی (0 نفر)", callback_data=f"lot_join_{lot_id}")
        ]])
        with db_conn() as conn:
            groups = conn.execute("SELECT group_id FROM groups").fetchall()
        sent_count = 0
        for g in groups:
            try:
                await context.bot.send_message(
                    chat_id=g['group_id'],
                    text=announce_text,
                    parse_mode="Markdown",
                    reply_markup=kb
                )
                sent_count += 1
            except Exception:
                pass

        await update.message.reply_text(
            f"🎉 *قرعه‌کشی ساخته شد!*\n\n"
            f"🎰 اسم: {title}\n"
            f"🎁 جایزه: {prize:,} هاپ پوینت\n"
            f"🏆 تعداد برنده: {wc} نفر\n"
            f"📢 ارسال به {sent_count} گروه\n\n"
            f"کد قرعه‌کشی: `{lot_id}`",
            parse_mode="Markdown"
        )
        return True

    return False

async def callback_handler_full(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data
    user = query.from_user
    if data == "check_join":
        await check_join_callback(update, context)
        return
    if data.startswith("ref_"):
        if await handle_referral_callbacks(query, data, user, context): return
    if data.startswith("mkt_"):
        if await handle_market_callbacks(query, data, user, context): return
    if data.startswith("factory_"):
        await factory_callback_handler(update, context)
        return
    if data.startswith("lot_"):
        await lottery_callback_handler(update, context)
        return
    if data.startswith("mayor_") or data.startswith("melec_"):
        if await mayor_callback(update, context): return
        return
    if data.startswith("mproj_"):
        if await mayor_projects_callback(update, context): return
        return
    if data.startswith("crisis_"):
        query = update.callback_query
        user = query.from_user
        if await handle_crisis_callback(query, data, user, context): return
        return
    if data.startswith("adm_reset_"):
        if not is_admin(user.id):
            await query.answer("فقط ادمین اصلی!", show_alert=True)
            return

        if data == "adm_reset_by_balance":
            await query.answer()
            context.user_data["admin_reset_step"] = "awaiting_threshold"
            await show_reset_balance_input(query)
            return

        if data == "adm_reset_set_exclude":
            await query.answer()
            context.user_data["admin_reset_step"] = "awaiting_exclude_nums"
            rows = context.bot_data.get("reset_rows", [])
            excluded = context.bot_data.get("reset_excluded_ids", set())
            ex_nums = [str(i+1) for i, r in enumerate(rows) if r[0] in excluded]
            current = f"استثناهای فعلی: `{', '.join(ex_nums)}`\n\n" if ex_nums else ""
            await query.edit_message_text(
                f"🚫 *استثنا کردن کاربر*\n\n"
                f"{current}"
                "شماره‌های کاربرانی که نباید ریست بشن رو بفرست.\n"
                "مثال: `3,7,12` یا `5` یا `همه` برای پاک کردن استثناها\n\n"
                "✏️ بنویس:",
                parse_mode="Markdown"
            )
            return

        if data == "adm_reset_confirm":
            await query.answer()
            threshold = context.bot_data.get("pending_reset_threshold", 0)
            excluded  = context.bot_data.get("reset_excluded_ids", set())
            excl_count = len(excluded)
            context.user_data["admin_reset_step"] = "awaiting_init_points"
            await query.edit_message_text(
                f"✅ تأیید شد — آستانه: `{threshold:,}`\n"
                f"🚫 استثناها: `{excl_count}` کاربر\n\n"
                "حالا *موجودی اولیه* که به همه داده بشه رو بفرست (مثلاً `0` یا `1000`):",
                parse_mode="Markdown"
            )
            return

        if data == "adm_reset_cancel":
            await query.answer()
            context.bot_data.pop("pending_reset_threshold", None)
            context.bot_data.pop("reset_excluded_ids", None)
            context.user_data.pop("admin_reset_step", None)
            context.user_data.pop("reset_init_points", None)
            await query.edit_message_text("❌ ریست لغو شد.")
            return

    if await new_callback_handler(update, context): return
    if await casino_callback_handler(update, context): return
    if await handle_leader_callback(query, user, data, context): return
    await callback_handler(update, context)

async def contraband_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    message = update.effective_message
    user_id = update.effective_user.id
    
    user = get_user(user_id)
    if not user or user["level"] < 4:
        await message.reply_text("❌ داداش برای ورود به دنیای سیاه و پرخطر قاچاق، باید حداقل به *سطح ۴* رسیده باشی! 🥷", parse_mode="Markdown")
        return
        
    jailed, jail_row = is_in_jail(user_id)
    if jailed:
        rel = datetime.fromisoformat(jail_row["release_at"])
        left = int((rel - datetime.now()).total_seconds())
        m, s = divmod(left, 60)
        await message.reply_text(f"⛓️ تو خودت الان زندانی هستی! نمی‌تونی باند قاچاق راه بندازی!\n⌛️ {m} دقیقه و {s} ثانیه تا آزادی")
        return
        
    conn = get_db()
    row = conn.execute("SELECT count FROM user_strays WHERE user_id = ?", (user_id,)).fetchone()
    conn.close()
    stray_count = row["count"] if row else 0
    if stray_count < 3:
        await message.reply_text("❌ برای راهی کردن محموله، باید حداقل *۳ تا سگ خیابانی* نجات داده باشی! 🐕", parse_mode="Markdown")
        return
        
    max_smuggle = min(stray_count, 15)
    keyboard = []
    row_buttons = []
    for count in range(3, max_smuggle + 1):
        row_buttons.append(cbtn(f"🐕 {count} سگ", callback_data=f"smuggle_{count}_{user_id}"))
        if len(row_buttons) == 2:
            keyboard.append(row_buttons)
            row_buttons = []
    if row_buttons:
        keyboard.append(row_buttons)
        
    reply_markup = InlineKeyboardMarkup(keyboard)
    await message.reply_text(
        "🥷 *به بخش قاچاق زیرزمینی هاپویی خوش آمدی!*\n\n"
        f"انتخاب کن چند تا از سگ‌های خیابانیت (موجودی شما: {stray_count}) رو می‌خوای قاچاق کنی؟\n"
        "⚠️ _هر چی تعداد بالاتر بره، شانس لو رفتن و رفتن به انفرادی بیشتر میشه!_",
        reply_markup=reply_markup, parse_mode="Markdown"
    )


async def profile_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if await force_join_check(update, context): return
    await hapoha_profile(update, context)

async def top_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if await force_join_check(update, context): return
    conn = get_db()
    rows = conn.execute(
        "SELECT first_name, hop_points, level FROM users ORDER BY hop_points DESC LIMIT 10"
    ).fetchall()
    conn.close()
    if not rows:
        await update.message.reply_text("هنوز کسی هاپو نزده!")
        return
    text = "🏆 *برترین هاپوها* 🐾\n\n"
    medals = ["🥇", "🥈", "🥉"]
    for i, r in enumerate(rows):
        medal = medals[i] if i < 3 else f"{i+1}."
        text += f"{medal} {r['first_name']} — {r['hop_points']:,.0f} 🦴 | سطح {r['level']}\n"
    await update.message.reply_text(text, parse_mode="Markdown")

async def help_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if await force_join_check(update, context): return
    text = (
        "╮──「 🐾 راهنمای هاپو 🐾 」\n\n"
        "┐─ 🐾 *هاپ* — جمع پوینت (کولداون ۵ دقیقه)\n"
        "┐─ 🐕 *سگ* — خرید و مدیریت سگ\n"
        "┐─ 🎣 *قلاب* — خرید قلاب ماهیگیری\n"
        "┐─ 🦴 *استخوان* — صید استخوان\n"
        "┐─ 🏦 *بانک* — مدیریت حساب بانکی\n"
        "┐─ 🏭 *کارخونه* — مدیریت کارخونه\n"
        "┐─ 🛍 *بازار* — قیمت‌های بازار\n"
        "┐─ 🏰 *شهر* — وضعیت شهر گروه\n"
        "┐─ 🎲 *بازی* — منوی بازی‌ها\n"
        "┐─ 💳 *انتقال [عدد] @یوزر* — انتقال پوینت\n"
        "┐─ 🐾 *هاپوهام* — پروفایل خودت\n"
        "└─ 🐾 *هاپ هاش* — پروفایل نفر ریپلای‌شده\n\n"
        "پروفایل — پروفایل\n"
        "برترین — لیدربرد"
    )
    await update.message.reply_text(text, parse_mode="Markdown")

async def handle_group_text_full(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if await force_join_check(update, context): return
    if await handle_meow_logic(update, context): return
    if not update.message or not update.message.text:
        return

    _u = update.effective_user
    _chat = update.effective_chat
    if _u and _chat and _u.id not in ADMIN_IDS:
        _jailed, _ = is_in_jail(_u.id)
        if not _jailed:
            _dur = jail_spam(_u.id, _chat.id)
            if _dur:
                _m, _s = divmod(_dur, 60)
                await update.message.reply_text(
                    f"🚔 *{_u.first_name} به خاطر اسپم {_m} دقیقه زندانی شد!*",
                    parse_mode="Markdown"
                )
                return
    text = update.message.text.strip().replace("\u200c", " ").replace("\u200b", "").strip()
    if await handle_rename(update, context): return
    if await handle_mayor_protest_input(update, context): return
    if await handle_mayor_project_input(update, context): return
    if await handle_bank_input(update, context): return
    if await handle_dice_input(update, context): return
    if await handle_gamble_input(update, context): return
    if await handle_casino_text_input(update, context): return
    if await lottery_text_handler(update, context): return
    if await handle_leader_text_input(update, context): return

    admin_triggers = ["افزایش لول", "کاهش لول", "افزایش پوینت", "کاهش پوینت"]
    if any(text.startswith(t) for t in admin_triggers):
        await admin_cmd(update, context); return

    if text == "افزودن ادمین": await add_admin_cmd(update, context); return
    if text == "حذف ادمین": await remove_admin_cmd(update, context); return
    if text == "حذف کاربر": await delete_user_cmd(update, context); return

    JAIL_BLOCKED_CMDS = ["هاپ", "هاپو", "hop", "سگ", "dog", "Dog",
                         "قلاب", "hook", "Hook", "استخوان", "bone", "Bone",
                         "بانک", "bank", "قاچاق", "کازینو", "casino",
                         "تاس", "گردونه", "قمار"]
    if text in JAIL_BLOCKED_CMDS and update.effective_user.id not in ADMIN_IDS:
        _jailed2, _jail_row2 = is_in_jail(update.effective_user.id)
        if _jailed2:
            _rel2 = datetime.fromisoformat(_jail_row2["release_at"])
            _left2 = max(0, int((_rel2 - datetime.now()).total_seconds()))
            _m2, _s2 = divmod(_left2, 60)
            await update.message.reply_text(
                f"⛓️ تو زندانی هستی! نمی‌تونی این کار رو بکنی.\n"
                f"⌛️ {_m2} دقیقه و {_s2} ثانیه تا آزادی\n"
                f"بنویس *زندان* برای گزینه‌های آزادی.",
                parse_mode="Markdown"
            )
            return

    if text in ["هاپ", "هاپو", "hop"]: await handle_hop(update, context)
    elif text in ["سگ", "dog", "Dog"]: await dog_cmd(update, context)
    elif text in ["قلاب", "hook", "Hook"]: await hook_cmd(update, context)
    elif text in ["استخوان", "bone", "Bone"]: await cast_cmd(update, context)
    elif text in ["بانک", "bank"]: await bank_cmd(update, context)
    elif text in ["زندان", "jail"]: await jail_cmd(update, context)
    elif text in ["بازی", "games"]: await games_menu(update, context)
    elif text == "قاچاق": await contraband_cmd(update, context)
    elif text in ["کازینو", "casino"]:
        u = get_user(update.effective_user.id)
        if u and u["level"] >= CASINO_MIN_LEVEL:
            await casino_menu_show(update, update.effective_user, u)
        else:
            await update.message.reply_text(f"🔒 سطح {CASINO_MIN_LEVEL} لازمه!")
    elif text in ["تاس"]: await dice_cmd(update, context)
    elif text in ["گردونه"]: await wheel_cmd(update, context)
    elif text in ["قمار"]: await gamble_cmd(update, context)
    elif text in ["شهر", "city"]: await city_cmd(update, context)
    elif text.startswith("اهدا"): await donate_cmd(update, context)
    elif text.startswith("انتقال"): await transfer_cmd(update, context)
    elif text in ["هاپوهام", "هاپو هام", "هاپ هام", "هاپ‌هام", "هاپوهام", "هاپوهامم", "هاپو‌هام", "hapoham"]: await hapoha_profile(update, context)
    elif text in ["هاپ هاش", "هاپ‌هاش", "هاپو هاش", "هاپوهاش", "هاپو‌هاش", "hapohash"]: await hapoha_profile_other(update, context)
    elif text in ["کارخونه", "factory"]: await factory_cmd(update, context)
    elif text in ["مارکت", "market"]: await market_cmd(update, context)
    elif text in ["پنل", "قرعه کشی", "قرعه‌کشی"]: await lottery_panel_cmd(update, context)
    elif text in ["شهرداری", "شهردار"]: await mayor_cmd(update, context)
    elif text in ["بحران", "وضعیت بحران", "crisis"]: await crisis_status_cmd(update, context)
    elif text in ["رهبر", "رهبری", "leader"]: await leader_panel_cmd(update, context)
    elif text in ["نامزد رهبر", "نامزد رهبری"]: await leaderjoin_cmd(update, context)
    elif text in ["رای رهبر", "رأی رهبر", "vote رهبر"]: await leadervote_cmd(update, context)
    elif text in ["برترین", "لیدربرد", "top", "Top"]: await top_cmd(update, context)


FORCE_JOIN_FIXED = [
    {"username": FORCE_JOIN_CHANNEL,   "name": FORCE_JOIN_CHANNEL_NAME,   "link": FORCE_JOIN_CHANNEL_LINK},
]

def get_force_join_channels() -> list:
    channels = list(FORCE_JOIN_FIXED)
    try:
        with db_conn() as conn:
            rows = conn.execute(
                "SELECT username, name, link FROM force_join_channels ORDER BY added_at"
            ).fetchall()
            for r in rows:
                channels.append({"username": r["username"], "name": r["name"], "link": r["link"]})
    except Exception:
        pass
    return channels

def init_force_join_table():
    with db_conn() as conn:
        conn.execute("""CREATE TABLE IF NOT EXISTS force_join_channels (
            username  TEXT PRIMARY KEY,
            name      TEXT,
            link      TEXT,
            added_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.commit()

async def is_member_of_all_channels(bot, user_id: int) -> tuple:
    missing = []
    for ch in get_force_join_channels():
        try:
            member = await bot.get_chat_member(f"@{ch['username']}", user_id)
            if member.status not in ("member", "administrator", "creator"):
                missing.append(ch)
        except Exception:
            pass
    return (len(missing) == 0), missing

async def is_member_of_channel(bot, user_id: int) -> bool:
    ok, _ = await is_member_of_all_channels(bot, user_id)
    return ok

def build_join_keyboard(missing_channels: list = None):
    channels = missing_channels or get_force_join_channels()
    rows = [[cbtn(f"عضویت در {ch['name']} 🔔", url=ch["link"])] for ch in channels]
    rows.append([cbtn("✅ عضو شدم، بررسی کن!", callback_data="check_join")])
    return InlineKeyboardMarkup(rows)

async def send_force_join_message(message, user, missing=None):
    if missing is None:
        _, missing = await is_member_of_all_channels(message.bot if hasattr(message, 'bot') else None, user.id)
    ch_list = "\n".join(f"• {ch['name']}" for ch in (missing or get_force_join_channels()))
    text = (
        f"⛔️ {user.mention_html()} عزیز!\n\n"
        f"برای استفاده از ربات هاپ‌داگ، ابتدا باید عضو این کانال‌ها بشی:\n\n"
        f"{ch_list}\n\n"
        f"👇 روی دکمه‌ها کلیک کن، عضو بشو، بعد «عضو شدم» رو بزن:"
    )
    await message.reply_text(text, reply_markup=build_join_keyboard(missing or get_force_join_channels()), parse_mode="HTML")

async def force_join_check(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message:
        return False
    if update.effective_chat.type not in ("group", "supergroup"):
        return False
    user = update.effective_user
    if not user:
        return False
    if user.id in ADMIN_IDS:
        return False
    ok, missing = await is_member_of_all_channels(context.bot, user.id)
    if not ok:
        await send_force_join_message(update.message, user, missing)
        return True
    return False

async def check_join_callback(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    user = query.from_user
    await query.answer()
    ok, missing = await is_member_of_all_channels(context.bot, user.id)
    if ok:
        await query.edit_message_text(
            f"✅ {user.mention_html()} خوش اومدی به هاپ‌داگ! 🐾\nالان میتونی از ربات استفاده کنی 🎉",
            parse_mode="HTML"
        )
    else:
        ch_names = "، ".join(ch["name"] for ch in missing)
        await query.answer(
            f"❌ هنوز عضو {ch_names} نشدی!\nاول عضو بشو بعد دکمه رو بزن.",
            show_alert=True
        )


async def force_join_admin_panel(message):
    channels = get_force_join_channels()
    fixed_count = len(FORCE_JOIN_FIXED)

    lines = ["📋 *کانال‌های جوین اجباری:*\n"]
    for i, ch in enumerate(channels, 1):
        tag = " 🔒" if i <= fixed_count else f" — /deljoin_{ch['username']}"
        lines.append(f"{i}. {ch['name']} (@{ch['username']}){tag}")

    lines.append("\n🔒 = ثابت و قابل حذف نیست")
    lines.append("\n➕ برای اضافه کردن کانال جدید بنویس:\n`addjoin @username نام_کانال`")

    kb = InlineKeyboardMarkup([[cbtn("❌ بستن", callback_data="cancel")]])
    await message.reply_text("\n".join(lines), parse_mode="Markdown", reply_markup=kb)

async def handle_force_join_admin_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user
    if not is_admin(user.id):
        return False
    if not update.message or not update.message.text:
        return False
    text = update.message.text.strip()

    if text.lower().startswith("addjoin "):
        parts = text.split(None, 2)
        if len(parts) < 3:
            await update.message.reply_text("❌ فرمت: `addjoin @username نام کانال`", parse_mode="Markdown")
            return True
        username = parts[1].lstrip("@")
        name     = parts[2].strip()
        link     = f"https://t.me/{username}"
        fixed_usernames = {ch["username"] for ch in FORCE_JOIN_FIXED}
        if username in fixed_usernames:
            await update.message.reply_text("❌ این کانال ثابته و نمیشه دوباره اضافه کرد.")
            return True
        with db_conn() as conn:
            conn.execute(
                "INSERT OR REPLACE INTO force_join_channels (username, name, link) VALUES (?,?,?)",
                (username, name, link)
            )
            conn.commit()
        await update.message.reply_text(f"✅ کانال @{username} با نام *{name}* اضافه شد.", parse_mode="Markdown")
        await force_join_admin_panel(update.message)
        return True

    if text.lower().startswith("deljoin ") or text.lower().startswith("/deljoin_"):
        username = text.split("_", 1)[-1].lstrip("@") if text.startswith("/deljoin_") else text.split(None, 1)[1].lstrip("@")
        fixed_usernames = {ch["username"] for ch in FORCE_JOIN_FIXED}
        if username in fixed_usernames:
            await update.message.reply_text("❌ کانال‌های ثابت قابل حذف نیستن.")
            return True
        with db_conn() as conn:
            deleted = conn.execute("DELETE FROM force_join_channels WHERE username=?", (username,)).rowcount
            conn.commit()
        if deleted:
            await update.message.reply_text(f"🗑 کانال @{username} حذف شد.")
        else:
            await update.message.reply_text(f"❌ کانال @{username} پیدا نشد.")
        await force_join_admin_panel(update.message)
        return True

    return False


async def show_reset_panel(message):
    kb = InlineKeyboardMarkup([
        [cbtn("🔴 ریست کاربران بر اساس موجودی", callback_data="adm_reset_by_balance")],
        [cbtn("❌ بستن پنل", callback_data="cancel")],
    ])
    await message.reply_text(
        "🔴 *پنل ریست ادمین اصلی*\n\n"
        "⚠️ این عملیات برگشت‌پذیر نیست!\n"
        "موجودی، قلاب، سگ، بانک، کارخانه و لول ریست می‌شن.\n"
        "جدول کاربران پاک *نمی‌شه*.",
        parse_mode="Markdown",
        reply_markup=kb
    )

async def show_reset_balance_input(query):
    await query.edit_message_text(
        "🔴 *ریست بر اساس موجودی*\n\n"
        "عدد مرز رو بفرست — هر کاربری که موجودی‌اش *بیشتر یا مساوی* این عدد باشه ریست می‌شه.\n\n"
        "مثال: `5000000000` (۵ میلیارد)\n\n"
        "✏️ عدد رو بنویس:",
        parse_mode="Markdown"
    )


def _build_reset_table_msg(context) -> tuple:
    threshold = context.bot_data.get("pending_reset_threshold", 0)
    excluded  = context.bot_data.get("reset_excluded_ids", set())
    rows = context.bot_data.get("reset_rows", [])

    will_reset = [r for r in rows if r[0] not in excluded]

    lines = [
        f"🔴 *لیست ریست*\n"
        f"آستانه: `{threshold:,}`\n"
        f"✅ ریست می‌شن: *{len(will_reset)}* | 🚫 استثنا: *{len(excluded)}*\n"
    ]
    for i, (uid, fname, uname, pts, lvl) in enumerate(rows, 1):
        status = "🚫" if uid in excluded else "🔴"
        uname_str = f"@{uname}" if uname else f"#{uid}"
        lines.append(f"{status} `{i}.` {fname} ({uname_str}) — {int(pts):,} | لول {lvl}")

    lines.append("\n📌 برای استثنا کردن، شماره‌ها رو بفرست (مثلاً `3,7,12`)")

    kb = InlineKeyboardMarkup([
        [cbtn("🚫 استثنا کردن", callback_data="adm_reset_set_exclude")],
        [cbtn(f"✅ تأیید ریست {len(will_reset)} کاربر", callback_data="adm_reset_confirm")],
        [cbtn("❌ لغو", callback_data="adm_reset_cancel")],
    ])
    return "\n".join(lines), kb


async def show_reset_confirm_table(message, threshold: int, context):
    context.bot_data["pending_reset_threshold"] = threshold
    context.bot_data["reset_excluded_ids"] = set()

    with db_conn() as conn:
        rows = conn.execute(
            "SELECT user_id, first_name, username, hop_points, level FROM users "
            "WHERE hop_points >= ? ORDER BY hop_points DESC",
            (threshold,)
        ).fetchall()

    if not rows:
        await message.reply_text(f"✅ هیچ کاربری بالای {threshold:,} موجودی نداره.")
        return

    context.bot_data["reset_rows"] = [
        (r["user_id"], r["first_name"], r["username"] or "", r["hop_points"], r["level"])
        for r in rows
    ]

    text, kb = _build_reset_table_msg(context)
    await message.reply_text(text, parse_mode="Markdown", reply_markup=kb)


async def do_reset_users(threshold: int, init_points: int, init_level: int, excluded: set) -> int:
    with db_conn() as conn:
        targets = conn.execute(
            "SELECT user_id FROM users WHERE hop_points >= ?", (threshold,)
        ).fetchall()
        ids = [r["user_id"] for r in targets if r["user_id"] not in excluded]
        if not ids:
            return 0

        placeholders = ",".join("?" * len(ids))

        conn.execute(
            f"UPDATE users SET hop_points=?, total_hops=0, level=?, last_hop=NULL WHERE user_id IN ({placeholders})",
            [init_points, init_level] + ids
        )
        conn.execute(f"DELETE FROM hooks WHERE user_id IN ({placeholders})", ids)
        conn.execute(f"DELETE FROM dogs WHERE user_id IN ({placeholders})", ids)
        conn.execute(f"DELETE FROM pending_bones WHERE user_id IN ({placeholders})", ids)
        conn.execute(f"DELETE FROM bank WHERE user_id IN ({placeholders})", ids)
        conn.execute(f"DELETE FROM transfers WHERE user_id IN ({placeholders})", ids)
        conn.execute(
            f"UPDATE factories SET stock=0, exp=0, level=1, producing=0, "
            f"production_end=NULL, machine_level=1, warehouse_level=1, last_produced=NULL "
            f"WHERE user_id IN ({placeholders})", ids
        )
        conn.execute(f"DELETE FROM jail WHERE user_id IN ({placeholders})", ids)
        conn.execute(f"DELETE FROM user_strays WHERE user_id IN ({placeholders})", ids)
        conn.commit()
    return len(ids)

async def handle_reset_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    user = update.effective_user
    if not is_admin(user.id):
        return False

    step = context.user_data.get("admin_reset_step")
    if not step:
        return False

    text = update.message.text.strip().replace(",", "").replace("_", "").replace(" ", "")

    if step == "awaiting_exclude_nums":
        context.user_data.pop("admin_reset_step", None)
        rows = context.bot_data.get("reset_rows", [])
        if text.strip() in ["همه", "all", "0"]:
            context.bot_data["reset_excluded_ids"] = set()
            await update.message.reply_text("✅ همه استثناها پاک شدن.")
        else:
            nums = []
            for part in text.replace("،", ",").split(","):
                part = part.strip()
                if part.isdigit():
                    nums.append(int(part))
            valid = [n for n in nums if 1 <= n <= len(rows)]
            if not valid:
                await update.message.reply_text("❌ شماره معتبری پیدا نشد، دوباره امتحان کن.")
                context.user_data["admin_reset_step"] = "awaiting_exclude_nums"
                return True
            excluded = set()
            for n in valid:
                excluded.add(rows[n - 1][0])
            context.bot_data["reset_excluded_ids"] = excluded
            names = ", ".join(rows[n-1][1] for n in valid)
            await update.message.reply_text(f"🚫 استثنا شدن: {names}")
        text_tbl, kb = _build_reset_table_msg(context)
        await update.message.reply_text(text_tbl, parse_mode="Markdown", reply_markup=kb)
        return True

    if step == "awaiting_threshold":
        try:
            threshold = int(float(text))
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        if threshold <= 0:
            await update.message.reply_text("❌ عدد باید مثبت باشه!")
            return True
        context.user_data.pop("admin_reset_step", None)
        await show_reset_confirm_table(update.message, threshold, context)
        return True

    if step == "awaiting_init_points":
        try:
            init_points = int(float(text))
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        context.user_data["reset_init_points"] = init_points
        context.user_data["admin_reset_step"] = "awaiting_init_level"
        await update.message.reply_text(
            f"✅ موجودی اولیه: `{init_points:,}`\n\n"
            "حالا *لول اولیه* رو بفرست (مثلاً `1`):",
            parse_mode="Markdown"
        )
        return True

    if step == "awaiting_init_level":
        try:
            init_level = int(text)
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        if init_level < 1:
            init_level = 1

        init_points = context.user_data.pop("reset_init_points", 0)
        threshold   = context.bot_data.pop("pending_reset_threshold", None)
        excluded    = context.bot_data.pop("reset_excluded_ids", set())
        context.user_data.pop("admin_reset_step", None)

        if threshold is None:
            await update.message.reply_text("❌ خطا: آستانه‌ای ذخیره نشده، دوباره از اول شروع کن.")
            return True

        await update.message.reply_text("⏳ در حال ریست...")
        count = await do_reset_users(threshold, init_points, init_level, excluded)
        await update.message.reply_text(
            f"✅ *ریست کامل شد!*\n\n"
            f"👥 تعداد ریست‌شده: `{count}` کاربر\n"
            f"🚫 استثناها: `{len(excluded)}` کاربر\n"
            f"💰 موجودی اولیه: `{init_points:,}`\n"
            f"⭐️ لول اولیه: `{init_level}`\n"
            f"📊 آستانه: بالای `{threshold:,}`",
            parse_mode="Markdown"
        )
        return True

    return False


async def private_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if await handle_reset_text_input(update, context): return
    if await handle_force_join_admin_input(update, context): return
    if await handle_bank_input(update, context): return
    if await handle_mayor_project_input(update, context): return
    if await lottery_text_handler(update, context): return
    if await handle_market_private_input(update, context): return
    if await handle_referral_admin_input(update, context): return
    if await handle_leader_text_input(update, context): return
    if not update.message or not update.message.text:
        return
    text = update.message.text.strip().replace("\u200c", " ").replace("\u200b", "").strip()
    user = update.effective_user

    if text in ["ریست", "reset", "🔴 ریست"] and is_admin(user.id):
        await show_reset_panel(update.message); return
    if text in ["جوین ادمین", "مدیریت جوین", "force join"] and is_admin(user.id):
        await force_join_admin_panel(update.message); return

    if text in ["مارکت", "market", "🛒 مارکت"]:
        await market_cmd(update, context); return
    if text == "مارکت ادمین" and is_admin(user.id):
        await market_admin_panel(update.message); return
    if text in ["دعوت دوستان", "🎁 دعوت دوستان", "دعوت", "invite"]:
        await referral_invite_cmd(update, context); return
    if text in ["پنل دعوت", "دعوت ادمین"] and is_admin(user.id):
        await referral_admin_panel(update.message); return
    if text in ["هاپوهام", "هاپو هام", "🐾 هاپوهام"]:
        await hapoha_profile(update, context); return
    if text in ["راهنما", "📖 راهنما"]:
        await help_cmd(update, context); return
    if text in ["لیدربرد", "📊 لیدربرد"]:
        await top_cmd(update, context); return

    await update.message.reply_text(
        "🐾 دستورات اصلی فقط توی گروه کار می‌کنن!\nاز دکمه‌های زیر استفاده کن 👇"
    )


MAYOR_DECREES_DEF = {
    "hop_festival":    {"name": "🎉 جشنواره هاپ",          "desc": "+۳۰٪ پوینت هاپ برای کل گروه",       "effect": "hop_boost",     "value": 0.30},
    "factory_boost":   {"name": "🏭 افزایش تولید کارخانه", "desc": "+۴۰٪ سرعت تولید کارخانه",           "effect": "factory_boost", "value": 0.40},
    "dog_income":      {"name": "🐕 افزایش درآمد سگ‌ها",   "desc": "+۳۵٪ درآمد سگ‌های گروه",            "effect": "dog_boost",     "value": 0.35},
    "tax_cut":         {"name": "💰 کاهش مالیات شهر",      "desc": "اهدای ۵٪ خزانه به همه کاربران",     "effect": "tax_cut",       "value": 0.05},
    "bank_profit":     {"name": "🏦 افزایش سود بانک",      "desc": "+۵۰٪ سود بانکی امروز",               "effect": "bank_boost",    "value": 0.50},
    "daily_prize":     {"name": "🎁 رویداد جایزه روزانه",  "desc": "جایزه تصادفی ۵۰۰-۵۰۰۰ برای هر هاپ", "effect": "daily_prize",   "value": 0},
}

MAYOR_PLEDGES_DEF = {
    "tax_cut":       "کاهش مالیات (اجرای فرمان کاهش مالیات)",
    "build_project": "ساخت پروژه جدید (ساخت حداقل یک پروژه)",
    "bank_boost":    "افزایش سود بانک (اجرای فرمان سود بانک)",
    "hop_festival":  "برگزاری جشنواره (اجرای فرمان جشنواره هاپ)",
    "factory_up":    "افزایش تولید (اجرای فرمان تولید کارخانه)",
}


def init_mayor_full_tables():
    with db_conn() as conn:
        conn.execute("""CREATE TABLE IF NOT EXISTS mayor (
            group_id    INTEGER PRIMARY KEY,
            user_id     INTEGER,
            username    TEXT,
            first_name  TEXT,
            elected_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            term_end    TEXT,
            popularity  INTEGER DEFAULT 100
        )""")
        try:
            conn.execute("ALTER TABLE mayor ADD COLUMN popularity INTEGER DEFAULT 100")
        except Exception:
            pass

        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_decrees (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id    INTEGER,
            user_id     INTEGER,
            decree_type TEXT,
            decree_name TEXT,
            issued_at   TEXT DEFAULT CURRENT_TIMESTAMP,
            expires_at  TEXT
        )""")

        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_pledges (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id    INTEGER,
            user_id     INTEGER,
            pledge_key  TEXT,
            pledge_text TEXT,
            fulfilled   INTEGER DEFAULT 0,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")

        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_elections (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id    INTEGER,
            status      TEXT DEFAULT 'candidacy',
            started_by  INTEGER,
            started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            ended_at    TEXT DEFAULT NULL
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_candidates (
            election_id INTEGER,
            user_id     INTEGER,
            username    TEXT,
            first_name  TEXT,
            pledges     TEXT DEFAULT '',
            joined_at   TEXT DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (election_id, user_id)
        )""")
        try:
            conn.execute("ALTER TABLE mayor_candidates ADD COLUMN pledges TEXT DEFAULT ''")
        except Exception:
            pass
        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_election_votes (
            election_id    INTEGER,
            voter_id       INTEGER,
            candidate_id   INTEGER,
            voted_at       TEXT DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (election_id, voter_id)
        )""")

        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_protests (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id    INTEGER,
            user_id     INTEGER,
            username    TEXT,
            first_name  TEXT,
            reason      TEXT,
            status      TEXT DEFAULT 'pending',
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_protest_votes (
            protest_id  INTEGER,
            user_id     INTEGER,
            vote        TEXT,
            voted_at    TEXT DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (protest_id, user_id)
        )""")

        conn.execute("""CREATE TABLE IF NOT EXISTS mayor_log (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id    INTEGER,
            user_id     INTEGER,
            action_type TEXT,
            action_desc TEXT,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")

        conn.commit()

def get_mayor(group_id: int):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor WHERE group_id=?", (group_id,)
        ).fetchone()

def set_mayor(group_id: int, user_id: int, username: str, first_name: str, term_days: int = 7):
    term_end = (datetime.now() + timedelta(days=term_days)).isoformat()
    with db_conn() as conn:
        conn.execute(
            """INSERT OR REPLACE INTO mayor (group_id, user_id, username, first_name, elected_at, term_end, popularity)
               VALUES (?, ?, ?, ?, ?, ?, 100)""",
            (group_id, user_id, username, first_name, datetime.now().isoformat(), term_end)
        )
        conn.commit()

def get_mayor_popularity(group_id: int) -> int:
    with db_conn() as conn:
        row = conn.execute("SELECT popularity FROM mayor WHERE group_id=?", (group_id,)).fetchone()
        return row["popularity"] if row else 0

def change_mayor_popularity(group_id: int, delta: int):
    with db_conn() as conn:
        conn.execute(
            "UPDATE mayor SET popularity=MAX(0,MIN(100,popularity+?)) WHERE group_id=?",
            (delta, group_id)
        )
        conn.commit()

def log_mayor_action(group_id: int, user_id: int, action_type: str, desc: str):
    with db_conn() as conn:
        conn.execute(
            "INSERT INTO mayor_log (group_id, user_id, action_type, action_desc) VALUES (?,?,?,?)",
            (group_id, user_id, action_type, desc)
        )
        conn.commit()

def get_active_decree(group_id: int):
    with db_conn() as conn:
        return conn.execute(
            """SELECT * FROM mayor_decrees WHERE group_id=? AND expires_at > datetime('now')
               ORDER BY id DESC LIMIT 1""",
            (group_id,)
        ).fetchone()

def issue_decree(group_id: int, user_id: int, decree_type: str):
    d = MAYOR_DECREES_DEF[decree_type]
    expires = (datetime.now() + timedelta(hours=24)).isoformat()
    with db_conn() as conn:
        conn.execute(
            """INSERT INTO mayor_decrees (group_id, user_id, decree_type, decree_name, expires_at)
               VALUES (?,?,?,?,?)""",
            (group_id, user_id, decree_type, d["name"], expires)
        )
        conn.commit()

def get_decree_hop_multiplier(group_id: int) -> float:
    d = get_active_decree(group_id)
    if d and d["decree_type"] == "hop_festival":
        return 1.0 + MAYOR_DECREES_DEF["hop_festival"]["value"]
    return 1.0

def get_decree_dog_multiplier(group_id: int) -> float:
    d = get_active_decree(group_id)
    if d and d["decree_type"] == "dog_income":
        return 1.0 + MAYOR_DECREES_DEF["dog_income"]["value"]
    return 1.0

def get_decree_factory_multiplier(group_id: int) -> float:
    d = get_active_decree(group_id)
    if d and d["decree_type"] == "factory_boost":
        return 1.0 + MAYOR_DECREES_DEF["factory_boost"]["value"]
    return 1.0

def get_decree_bank_multiplier(group_id: int) -> float:
    d = get_active_decree(group_id)
    if d and d["decree_type"] == "bank_profit":
        return 1.0 + MAYOR_DECREES_DEF["bank_profit"]["value"]
    return 1.0

def get_decree_daily_prize(group_id: int) -> int:
    d = get_active_decree(group_id)
    if d and d["decree_type"] == "daily_prize":
        return random.randint(500, 5000)
    return 0

def get_active_election(group_id: int):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor_elections WHERE group_id=? AND status NOT IN ('done','cancelled') ORDER BY id DESC LIMIT 1",
            (group_id,)
        ).fetchone()

def get_active_protest(group_id: int):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor_protests WHERE group_id=? AND status='pending' ORDER BY id DESC LIMIT 1",
            (group_id,)
        ).fetchone()

def get_mayor_pledges(group_id: int, user_id: int):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor_pledges WHERE group_id=? AND user_id=?",
            (group_id, user_id)
        ).fetchall()

def check_pledge_fulfilled(group_id: int, user_id: int, pledge_key: str) -> bool:
    with db_conn() as conn:
        row = conn.execute(
            "SELECT fulfilled FROM mayor_pledges WHERE group_id=? AND user_id=? AND pledge_key=?",
            (group_id, user_id, pledge_key)
        ).fetchone()
        return bool(row and row["fulfilled"])

def fulfill_pledge(group_id: int, user_id: int, pledge_key: str):
    with db_conn() as conn:
        conn.execute(
            "UPDATE mayor_pledges SET fulfilled=1 WHERE group_id=? AND user_id=? AND pledge_key=?",
            (group_id, user_id, pledge_key)
        )
        conn.commit()


async def mayor_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    chat = update.effective_chat
    if chat.type == "private":
        await update.message.reply_text("🏛 شهرداری فقط توی گروه کار می‌کنه!")
        return
    ensure_user(user.id, user.username or "", user.first_name)
    ensure_group(chat.id, chat.title or "گروه")
    await _send_mayor_panel(update.message, chat.id, user)

async def _send_mayor_panel(message_obj, group_id: int, user):
    mayor = get_mayor(group_id)
    election = get_active_election(group_id)
    decree = get_active_decree(group_id)
    is_mayor = mayor and mayor["user_id"] == user.id
    pop = get_mayor_popularity(group_id) if mayor else 0
    pop_bar = "🟢" * (pop // 20) + "⚪️" * (5 - pop // 20)

    if mayor:
        term = datetime.fromisoformat(mayor["term_end"])
        days_left = max(0, (term - datetime.now()).days)
        mayor_line = f"👑 شهردار: {mayor['first_name']} | محبوبیت: {pop}٪ {pop_bar}\n⏳ {days_left} روز تا پایان دوره"
    else:
        mayor_line = "⚠️ این گروه شهردار ندارد!"

    decree_line = f"📜 فرمان فعال: {decree['decree_name']}" if decree else "📜 فرمانی فعال نیست"

    text = (
        f"🏛 *شهرداری هاپو*\n\n"
        f"{mayor_line}\n"
        f"{decree_line}\n\n"
    )

    kb = []
    if is_mayor:
        kb.append([cbtn("📜 صدور فرمان روزانه", callback_data=f"mayor_decree_{group_id}")])
        kb.append([cbtn("📊 پنل مدیریت شهردار", callback_data=f"mayor_panel_{group_id}")])
    kb.append([cbtn("🗳 انتخابات", callback_data=f"mayor_election_{group_id}")])
    kb.append([cbtn("📋 وضعیت شهرداری", callback_data=f"mayor_status_{group_id}")])
    if mayor and not is_mayor:
        kb.append([cbtn("📢 اعتراض به شهردار", callback_data=f"mayor_protest_{group_id}")])
    kb.append([cbtn("📜 تاریخچه تصمیمات", callback_data=f"mayor_log_{group_id}")])

    await message_obj.reply_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))


async def mayor_callback(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    query = update.callback_query
    data  = query.data
    user  = query.from_user
    await query.answer()

    if data.startswith("mayor_status_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        election = get_active_election(group_id)
        decree = get_active_decree(group_id)
        protest = get_active_protest(group_id)
        pop = get_mayor_popularity(group_id) if mayor else 0
        pop_bar = "🟢" * (pop // 20) + "⚪️" * (5 - pop // 20)

        txt = "📊 *وضعیت شهرداری*\n\n"
        if mayor:
            term = datetime.fromisoformat(mayor["term_end"])
            days_left = max(0, (term - datetime.now()).days)
            txt += (f"👑 شهردار: {mayor['first_name']}\n"
                    f"📅 پایان دوره: {term.strftime('%Y-%m-%d')}\n"
                    f"⏳ باقی‌مانده: {days_left} روز\n"
                    f"📊 محبوبیت: {pop}٪ {pop_bar}\n\n")
            pledges = get_mayor_pledges(group_id, mayor["user_id"])
            if pledges:
                txt += "📌 *وعده‌های انتخاباتی:*\n"
                for p in pledges:
                    icon = "✅" if p["fulfilled"] else "⏳"
                    txt += f"{icon} {p['pledge_text']}\n"
                txt += "\n"
        else:
            txt += "⚠️ شهرداری خالیه — انتخابات برگزار نشده!\n\n"

        if decree:
            exp = datetime.fromisoformat(decree["expires_at"])
            h = int((exp - datetime.now()).total_seconds() // 3600)
            txt += f"📜 فرمان فعال: {decree['decree_name']} ({h} ساعت باقی)\n"
        if election:
            txt += f"🗳 انتخابات در جریان: {election['status']}\n"
        if protest:
            with db_conn() as conn:
                yes = conn.execute("SELECT COUNT(*) FROM mayor_protest_votes WHERE protest_id=? AND vote='yes'", (protest["id"],)).fetchone()[0]
                no  = conn.execute("SELECT COUNT(*) FROM mayor_protest_votes WHERE protest_id=? AND vote='no'",  (protest["id"],)).fetchone()[0]
            txt += f"\n📢 اعتراض فعال: {protest['reason'][:40]}\n✅ {yes} | ❌ {no}\n"

        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", callback_data=f"mayor_back_{group_id}")]]))
        return True

    if data.startswith("mayor_log_"):
        group_id = int(data.split("_")[2])
        with db_conn() as conn:
            logs = conn.execute(
                "SELECT * FROM mayor_log WHERE group_id=? ORDER BY id DESC LIMIT 15",
                (group_id,)
            ).fetchall()
        txt = "📜 *تاریخچه تصمیمات شهرداری*\n\n"
        if not logs:
            txt += "هنوز هیچ رویدادی ثبت نشده."
        for lg in logs:
            dt = lg["created_at"][:16]
            txt += f"• [{dt}] {lg['action_desc']}\n"
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", callback_data=f"mayor_back_{group_id}")]]))
        return True

    if data.startswith("mayor_back_"):
        group_id = int(data.split("_")[2])
        await _edit_mayor_panel(query, group_id, user)
        return True

    if data.startswith("mayor_decree_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار می‌تونه فرمان بده!", show_alert=True); return True
        existing = get_active_decree(group_id)
        if existing:
            exp = datetime.fromisoformat(existing["expires_at"])
            h = int((exp - datetime.now()).total_seconds() // 3600)
            await query.answer(f"❌ فرمان فعال داری! {h} ساعت دیگه منقضی میشه.", show_alert=True); return True
        kb = []
        for key, d in MAYOR_DECREES_DEF.items():
            kb.append([cbtn(f"{d['name']}", callback_data=f"mayor_issue_{group_id}_{key}")])
        kb.append([cbtn("❌ انصراف", callback_data=f"mayor_back_{group_id}")])
        await query.edit_message_text(
            "📜 *صدور فرمان روزانه*\n\nهر ۲۴ ساعت فقط یک فرمان می‌تونی صادر کنی:\n",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mayor_issue_"):
        parts = data.split("_")
        group_id = int(parts[2])
        decree_key = "_".join(parts[3:])
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        if get_active_decree(group_id):
            await query.answer("❌ فرمان فعال داری!", show_alert=True); return True
        if decree_key not in MAYOR_DECREES_DEF:
            await query.answer("❌ فرمان نامعتبر!", show_alert=True); return True

        d = MAYOR_DECREES_DEF[decree_key]
        issue_decree(group_id, user.id, decree_key)

        if decree_key == "tax_cut":
            with db_conn() as conn:
                grp = conn.execute("SELECT * FROM groups WHERE group_id=?", (group_id,)).fetchone()
                if grp and grp["treasury"] > 0:
                    pool = int(grp["treasury"] * MAYOR_DECREES_DEF["tax_cut"]["value"])
                    users_in_grp = conn.execute(
                        "SELECT DISTINCT user_id FROM users WHERE user_id IN "
                        "(SELECT user_id FROM users ORDER BY total_hops DESC LIMIT 50)"
                    ).fetchall()
                    if users_in_grp:
                        share = pool // len(users_in_grp)
                        if share > 0:
                            for ur in users_in_grp:
                                conn.execute("UPDATE users SET hop_points=hop_points+? WHERE user_id=?",
                                             (share, ur["user_id"]))
                            conn.execute("UPDATE groups SET treasury=treasury-? WHERE group_id=?",
                                         (share * len(users_in_grp), group_id))
                    conn.commit()

        log_mayor_action(group_id, user.id, "decree", f"فرمان صادر شد: {d['name']}")

        pledge_map = {
            "tax_cut": "tax_cut",
            "bank_profit": "bank_boost",
            "hop_festival": "hop_festival",
            "factory_boost": "factory_up",
        }
        if decree_key in pledge_map:
            fulfill_pledge(group_id, user.id, pledge_map[decree_key])
            change_mayor_popularity(group_id, +5)

        try:
            await query.bot.send_message(
                group_id,
                f"📣 *فرمان شهردار صادر شد!*\n\n{d['name']}\n📋 {d['desc']}\n\n⏳ ۲۴ ساعت فعال است!",
                parse_mode="Markdown"
            )
        except Exception:
            pass

        await query.edit_message_text(
            f"✅ *فرمان صادر شد!*\n\n{d['name']}\n{d['desc']}\n\n⏳ ۲۴ ساعت فعاله!",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 پنل شهرداری", callback_data=f"mayor_back_{group_id}")]]))
        return True

    if data.startswith("mayor_panel_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        pop = get_mayor_popularity(group_id)
        pop_bar = "🟢" * (pop // 20) + "⚪️" * (5 - pop // 20)
        pledges = get_mayor_pledges(group_id, user.id)
        done = sum(1 for p in pledges if p["fulfilled"])
        total = len(pledges)
        txt = (f"📊 *پنل مدیریت شهردار*\n\n"
               f"👑 {mayor['first_name']}\n"
               f"📊 محبوبیت: {pop}٪ {pop_bar}\n"
               f"📌 وعده‌ها: {done}/{total} انجام شده\n\n"
               f"از اینجا می‌تونی شهر رو مدیریت کنی.")
        kb = [
            [cbtn("📜 صدور فرمان", callback_data=f"mayor_decree_{group_id}")],
            [cbtn("📌 وعده‌های من", callback_data=f"mayor_mypledges_{group_id}")],
            [cbtn("📊 گزارش عملکرد", callback_data=f"mayor_report_{group_id}")],
            [cbtn("🔙 بازگشت", callback_data=f"mayor_back_{group_id}")],
        ]
        await query.edit_message_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mayor_mypledges_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        pledges = get_mayor_pledges(group_id, user.id)
        txt = "📌 *وعده‌های انتخاباتی من*\n\n"
        if not pledges:
            txt += "وعده‌ای ثبت نشده."
        for p in pledges:
            icon = "✅" if p["fulfilled"] else "⏳"
            txt += f"{icon} {p['pledge_text']}\n"
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", callback_data=f"mayor_panel_{group_id}")]]))
        return True

    if data.startswith("mayor_report_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        with db_conn() as conn:
            decree_count = conn.execute(
                "SELECT COUNT(*) FROM mayor_decrees WHERE group_id=? AND user_id=?",
                (group_id, user.id)
            ).fetchone()[0]
        pledges = get_mayor_pledges(group_id, user.id)
        done = sum(1 for p in pledges if p["fulfilled"])
        total = len(pledges)
        pop = get_mayor_popularity(group_id)
        txt = (f"📊 *گزارش عملکرد شهردار*\n\n"
               f"📜 فرمان‌های صادرشده: {decree_count}\n"
               f"📌 وعده‌های انجام‌شده: {done}/{total}\n"
               f"📊 محبوبیت فعلی: {pop}٪\n\n"
               f"💡 هر وعده انجام‌نشده در پایان دوره محبوبیت رو کم می‌کنه!")
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", callback_data=f"mayor_panel_{group_id}")]]))
        return True


    if data.startswith("mayor_election_"):
        group_id = int(data.split("_")[2])
        election = get_active_election(group_id)
        mayor = get_mayor(group_id)
        txt = "🗳 *انتخابات شهرداری*\n\n"
        if election:
            if election["status"] == "candidacy":
                with db_conn() as conn:
                    cands = conn.execute(
                        "SELECT * FROM mayor_candidates WHERE election_id=?", (election["id"],)
                    ).fetchall()
                txt += f"📋 مرحله: ثبت‌نام نامزدها\n👥 نامزدها: {len(cands)} نفر\n\n"
                for c in cands:
                    txt += f"• {c['first_name']}\n"
                kb = [[cbtn("✋ ثبت‌نام نامزدی", callback_data=f"melec_join_{group_id}_{election['id']}")]]
                if is_admin(user.id):
                    kb.append([cbtn("🗳 شروع رای‌گیری", callback_data=f"melec_startvote_{group_id}_{election['id']}")])
                    kb.append([cbtn("❌ لغو انتخابات", callback_data=f"melec_cancel_{group_id}_{election['id']}")])
            elif election["status"] == "voting":
                with db_conn() as conn:
                    cands = conn.execute(
                        "SELECT mc.*, COUNT(mev.voter_id) as votes FROM mayor_candidates mc "
                        "LEFT JOIN mayor_election_votes mev ON mev.candidate_id=mc.user_id AND mev.election_id=mc.election_id "
                        "WHERE mc.election_id=? GROUP BY mc.user_id",
                        (election["id"],)
                    ).fetchall()
                txt += "📊 مرحله: رای‌گیری\n\n"
                for c in cands:
                    txt += f"• {c['first_name']} — {c['votes']} رای\n"
                kb = [[cbtn(f"🗳 رای به {c['first_name']}", callback_data=f"melec_vote_{group_id}_{election['id']}_{c['user_id']}")]
                      for c in cands]
                if is_admin(user.id):
                    kb.append([cbtn("🏁 اعلام نتیجه", callback_data=f"melec_finish_{group_id}_{election['id']}")])
                    kb.append([cbtn("❌ لغو انتخابات", callback_data=f"melec_cancel_{group_id}_{election['id']}")])
            else:
                txt += "انتخابات تموم شده!"
                kb = []
        else:
            txt += "انتخاباتی در جریان نیست."
            kb = []
            if is_admin(user.id):
                kb.append([cbtn("🚀 شروع انتخابات جدید", callback_data=f"melec_start_{group_id}")])

        kb.append([cbtn("🔙 بازگشت", callback_data=f"mayor_back_{group_id}")])
        await query.edit_message_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("melec_start_"):
        group_id = int(data.split("_")[2])
        if not is_admin(user.id):
            await query.answer("❌ فقط ادمین می‌تونه انتخابات شروع کنه!", show_alert=True); return True
        if get_active_election(group_id):
            await query.answer("❌ انتخابات در جریانه!", show_alert=True); return True
        with db_conn() as conn:
            conn.execute(
                "INSERT INTO mayor_elections (group_id, status, started_by) VALUES (?,?,?)",
                (group_id, "candidacy", user.id)
            )
            conn.commit()
        log_mayor_action(group_id, user.id, "election", "انتخابات جدید شروع شد")
        try:
            await query.bot.send_message(
                group_id,
                "🗳 *انتخابات شهرداری شروع شد!*\n\n"
                "👤 برای نامزد شدن بنویسید *شهرداری* و دکمه ثبت‌نام رو بزنید.\n"
                "⏳ مرحله نامزدی آغاز شد.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        await query.edit_message_text("✅ انتخابات شروع شد! کاربران می‌تونن ثبت‌نام کنن.",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data=f"mayor_election_{group_id}")]]))
        return True

    if data.startswith("melec_join_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        with db_conn() as conn:
            exists = conn.execute(
                "SELECT user_id FROM mayor_candidates WHERE election_id=? AND user_id=?",
                (election_id, user.id)
            ).fetchone()
            if exists:
                await query.answer("❌ قبلاً ثبت‌نام کردی!", show_alert=True); return True
            conn.execute(
                "INSERT INTO mayor_candidates (election_id, user_id, username, first_name) VALUES (?,?,?,?)",
                (election_id, user.id, user.username or "", user.first_name)
            )
            conn.commit()

        context.user_data["pledge_election_id"] = election_id
        context.user_data["pledge_group_id"] = group_id
        context.user_data["waiting_pledges"] = True

        kb = []
        for key, txt_p in MAYOR_PLEDGES_DEF.items():
            kb.append([cbtn(txt_p, callback_data=f"melec_pledge_{group_id}_{election_id}_{key}")])
        kb.append([cbtn("✅ تمام، ثبت وعده‌ها", callback_data=f"melec_donepledge_{group_id}_{election_id}")])

        await query.edit_message_text(
            f"✅ *{user.first_name}* ثبت‌نام شد!\n\nحالا ۱ تا ۳ وعده انتخاباتی انتخاب کن:",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("melec_pledge_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        pledge_key = "_".join(parts[4:])
        if pledge_key not in MAYOR_PLEDGES_DEF:
            await query.answer("وعده نامعتبر!", show_alert=True); return True
        with db_conn() as conn:
            existing = conn.execute(
                "SELECT COUNT(*) FROM mayor_pledges WHERE group_id=? AND user_id=?",
                (group_id, user.id)
            ).fetchone()[0]
            if existing >= 3:
                await query.answer("❌ حداکثر ۳ وعده!", show_alert=True); return True
            already = conn.execute(
                "SELECT id FROM mayor_pledges WHERE group_id=? AND user_id=? AND pledge_key=?",
                (group_id, user.id, pledge_key)
            ).fetchone()
            if already:
                await query.answer("این وعده قبلاً ثبت شده!", show_alert=True); return True
            conn.execute(
                "INSERT INTO mayor_pledges (group_id, user_id, pledge_key, pledge_text) VALUES (?,?,?,?)",
                (group_id, user.id, pledge_key, MAYOR_PLEDGES_DEF[pledge_key])
            )
            conn.commit()
        await query.answer(f"✅ وعده ثبت شد: {MAYOR_PLEDGES_DEF[pledge_key]}")
        return True

    if data.startswith("melec_donepledge_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        with db_conn() as conn:
            pledges = conn.execute(
                "SELECT pledge_text FROM mayor_pledges WHERE group_id=? AND user_id=?",
                (group_id, user.id)
            ).fetchall()
        pledge_list = "\n".join(f"• {p['pledge_text']}" for p in pledges) or "بدون وعده"
        try:
            await query.bot.send_message(
                group_id,
                f"✋ *{user.first_name}* نامزد شهرداری شد!\n\n"
                f"📌 *وعده‌های انتخاباتی:*\n{pledge_list}\n\n"
                f"🗳 برای رای دادن منتظر شروع رای‌گیری باشید.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        await query.edit_message_text(
            f"✅ ثبت‌نام کامل شد!\n\n📌 وعده‌هات:\n{pledge_list}",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data=f"mayor_election_{group_id}")]]))
        return True

    if data.startswith("melec_startvote_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        if not is_admin(user.id):
            await query.answer("❌ فقط ادمین می‌تونه رای‌گیری رو شروع کنه!", show_alert=True); return True
        with db_conn() as conn:
            cands = conn.execute(
                "SELECT * FROM mayor_candidates WHERE election_id=?", (election_id,)
            ).fetchall()
            if len(cands) < 1:
                await query.answer("❌ حداقل یه نامزد لازمه!", show_alert=True); return True
            conn.execute(
                "UPDATE mayor_elections SET status='voting' WHERE id=?", (election_id,)
            )
            conn.commit()
        log_mayor_action(group_id, user.id, "election", "رای‌گیری شروع شد")
        try:
            cand_list = "\n".join(f"• {c['first_name']}" for c in cands)
            await query.bot.send_message(
                group_id,
                f"🗳 *رای‌گیری شهرداری شروع شد!*\n\n"
                f"👥 نامزدها:\n{cand_list}\n\n"
                f"برای رای دادن بنویسید *شهرداری* و نامزد مورد نظر رو انتخاب کنید!",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        await query.edit_message_text("✅ رای‌گیری شروع شد!",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data=f"mayor_election_{group_id}")]]))
        return True

    if data.startswith("melec_vote_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        candidate_id = int(parts[4])
        with db_conn() as conn:
            elec = conn.execute("SELECT * FROM mayor_elections WHERE id=?", (election_id,)).fetchone()
            if not elec or elec["status"] != "voting":
                await query.answer("❌ رای‌گیری فعال نیست!", show_alert=True); return True
            already = conn.execute(
                "SELECT voter_id FROM mayor_election_votes WHERE election_id=? AND voter_id=?",
                (election_id, user.id)
            ).fetchone()
            if already:
                await query.answer("❌ قبلاً رای دادی!", show_alert=True); return True
            conn.execute(
                "INSERT INTO mayor_election_votes (election_id, voter_id, candidate_id) VALUES (?,?,?)",
                (election_id, user.id, candidate_id)
            )
            conn.commit()
        await query.answer("✅ رای ثبت شد!")
        return True

    if data.startswith("melec_finish_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        if not is_admin(user.id):
            await query.answer("❌ فقط ادمین می‌تونه نتیجه رو اعلام کنه!", show_alert=True); return True
        with db_conn() as conn:
            elec = conn.execute("SELECT * FROM mayor_elections WHERE id=?", (election_id,)).fetchone()
            if not elec or elec["status"] != "voting":
                await query.answer("❌ رای‌گیری فعال نیست!", show_alert=True); return True
            winner = conn.execute(
                """SELECT mc.user_id, mc.username, mc.first_name, COUNT(mev.voter_id) as votes
                   FROM mayor_candidates mc
                   LEFT JOIN mayor_election_votes mev ON mev.candidate_id=mc.user_id AND mev.election_id=?
                   WHERE mc.election_id=?
                   GROUP BY mc.user_id ORDER BY votes DESC LIMIT 1""",
                (election_id, election_id)
            ).fetchone()
            conn.execute(
                "UPDATE mayor_elections SET status='done', ended_at=? WHERE id=?",
                (datetime.now().isoformat(), election_id)
            )
            conn.commit()

        if not winner:
            await query.answer("❌ نامزدی وجود نداره!", show_alert=True); return True

        old_mayor = get_mayor(group_id)
        if old_mayor:
            old_pledges = get_mayor_pledges(group_id, old_mayor["user_id"])
            unfulfilled = sum(1 for p in old_pledges if not p["fulfilled"])
            if unfulfilled > 0:
                change_mayor_popularity(group_id, -unfulfilled * 10)

        set_mayor(group_id, winner["user_id"], winner["username"], winner["first_name"])
        log_mayor_action(group_id, winner["user_id"], "elected", f"{winner['first_name']} شهردار شد با {winner['votes']} رای")

        try:
            await query.bot.send_message(
                group_id,
                f"🎉 *انتخابات تموم شد!*\n\n👑 شهردار جدید: *{winner['first_name']}*\n🗳 با {winner['votes']} رای\n\nمبارک باشه! 🏛",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        await query.edit_message_text(
            f"🎉 *{winner['first_name']}* شهردار جدید شد!",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 پنل شهرداری", callback_data=f"mayor_back_{group_id}")]]))
        return True

    if data.startswith("melec_cancel_"):
        parts = data.split("_")
        group_id = int(parts[2])
        election_id = int(parts[3])
        if not is_admin(user.id):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        with db_conn() as conn:
            conn.execute("UPDATE mayor_elections SET status='cancelled' WHERE id=?", (election_id,))
            conn.commit()
        log_mayor_action(group_id, user.id, "election", "انتخابات توسط ادمین لغو شد")
        try:
            await query.bot.send_message(
                group_id,
                "❌ *انتخابات شهرداری لغو شد.*\n\nادمین انتخابات را لغو کرد.",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        await query.edit_message_text("✅ انتخابات لغو شد.",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 برگشت", callback_data=f"mayor_election_{group_id}")]]))
        return True


    if data.startswith("mayor_protest_"):
        group_id = int(data.split("_")[2])
        mayor = get_mayor(group_id)
        if not mayor:
            await query.answer("❌ شهرداری نداریم!", show_alert=True); return True
        if mayor["user_id"] == user.id:
            await query.answer("❌ نمی‌تونی به خودت اعتراض کنی!", show_alert=True); return True
        existing = get_active_protest(group_id)
        if existing:
            await query.answer("❌ یه اعتراض فعال داریم! صبر کن.", show_alert=True); return True
        context.user_data["protest_group_id"] = group_id
        context.user_data["waiting_protest"] = True
        await query.edit_message_text(
            "📢 *اعتراض به شهردار*\n\nدلیل اعتراضت رو بنویس (حداکثر ۱۰۰ کاراکتر):",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("❌ انصراف", callback_data=f"mayor_back_{group_id}")]])
        )
        return True

    if data.startswith("mayor_pvote_"):
        parts = data.split("_")
        group_id = int(parts[2])
        protest_id = int(parts[3])
        vote = parts[4]
        with db_conn() as conn:
            already = conn.execute(
                "SELECT user_id FROM mayor_protest_votes WHERE protest_id=? AND user_id=?",
                (protest_id, user.id)
            ).fetchone()
            if already:
                await query.answer("قبلاً رای دادی!", show_alert=True); return True
            conn.execute(
                "INSERT INTO mayor_protest_votes (protest_id, user_id, vote) VALUES (?,?,?)",
                (protest_id, user.id, vote)
            )
            yes = conn.execute("SELECT COUNT(*) FROM mayor_protest_votes WHERE protest_id=? AND vote='yes'", (protest_id,)).fetchone()[0]
            no  = conn.execute("SELECT COUNT(*) FROM mayor_protest_votes WHERE protest_id=? AND vote='no'",  (protest_id,)).fetchone()[0]
            conn.commit()
        if yes >= 10:
            fired_mayor = get_mayor(group_id)
            mayor_name = fired_mayor["first_name"] if fired_mayor else "شهردار"
            with db_conn() as conn:
                conn.execute("UPDATE mayor_protests SET status='accepted' WHERE id=?", (protest_id,))
                conn.execute("DELETE FROM mayor WHERE group_id=?", (group_id,))
                conn.commit()
            log_mayor_action(group_id, user.id, "protest", "شهردار با رای اعتراض برکنار شد")
            try:
                await query.bot.send_message(
                    group_id,
                    f"⚠️ *اعتراض پذیرفته شد!*\n\n"
                    f"👤 {mayor_name} با رای مردم از شهرداری برکنار شد.\n"
                    f"🗳 انتخابات جدید برگزار کنید!",
                    parse_mode="Markdown"
                )
            except Exception:
                pass
        await query.answer(f"✅ رای ثبت شد | ✅{yes} ❌{no}")
        return True

    return False

async def _edit_mayor_panel(query, group_id: int, user):
    mayor = get_mayor(group_id)
    election = get_active_election(group_id)
    decree = get_active_decree(group_id)
    is_mayor = mayor and mayor["user_id"] == user.id
    pop = get_mayor_popularity(group_id) if mayor else 0
    pop_bar = "🟢" * (pop // 20) + "⚪️" * (5 - pop // 20)

    if mayor:
        term = datetime.fromisoformat(mayor["term_end"])
        days_left = max(0, (term - datetime.now()).days)
        mayor_line = f"👑 شهردار: {mayor['first_name']} | محبوبیت: {pop}٪ {pop_bar}\n⏳ {days_left} روز تا پایان دوره"
    else:
        mayor_line = "⚠️ این گروه شهردار ندارد!"

    decree_line = f"📜 فرمان فعال: {decree['decree_name']}" if decree else "📜 فرمانی فعال نیست"

    text = (f"🏛 *شهرداری هاپو*\n\n{mayor_line}\n{decree_line}\n\n")

    kb = []
    if is_mayor:
        kb.append([cbtn("📜 صدور فرمان روزانه", callback_data=f"mayor_decree_{group_id}")])
        kb.append([cbtn("📊 پنل مدیریت شهردار", callback_data=f"mayor_panel_{group_id}")])
    kb.append([cbtn("🗳 انتخابات", callback_data=f"mayor_election_{group_id}")])
    kb.append([cbtn("📋 وضعیت شهرداری", callback_data=f"mayor_status_{group_id}")])
    if mayor and not is_mayor:
        kb.append([cbtn("📢 اعتراض به شهردار", callback_data=f"mayor_protest_{group_id}")])
    kb.append([cbtn("📜 تاریخچه تصمیمات", callback_data=f"mayor_log_{group_id}")])
    await query.edit_message_text(text, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))


async def handle_mayor_protest_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message or not update.message.text:
        return False
    if not context.user_data.get("waiting_protest"):
        return False

    group_id = context.user_data.pop("protest_group_id", None)
    context.user_data.pop("waiting_protest", None)

    if not group_id:
        return False

    user = update.effective_user
    reason = update.message.text.strip()[:100]
    mayor = get_mayor(group_id)
    if not mayor:
        await update.message.reply_text("❌ شهرداری نداریم!")
        return True

    with db_conn() as conn:
        conn.execute(
            "INSERT INTO mayor_protests (group_id, user_id, username, first_name, reason) VALUES (?,?,?,?,?)",
            (group_id, user.id, user.username or "", user.first_name, reason)
        )
        conn.commit()
        protest_id = conn.execute("SELECT last_insert_rowid()").fetchone()[0]

    log_mayor_action(group_id, user.id, "protest", f"اعتراض ثبت شد: {reason[:40]}")

    kb = InlineKeyboardMarkup([
        [cbtn("✅ موافقم", callback_data=f"mayor_pvote_{group_id}_{protest_id}_yes"),
         cbtn("❌ مخالفم", callback_data=f"mayor_pvote_{group_id}_{protest_id}_no")],
    ])

    try:
        await context.bot.send_message(
            group_id,
            f"📢 *اعتراض جدید!*\n\n👤 {user.first_name}: {reason}\n\n"
            f"⚠️ اگه ۱۰ نفر موافق باشن، شهردار برکنار میشه!",
            parse_mode="Markdown", reply_markup=kb
        )
    except Exception:
        pass

    await update.message.reply_text("✅ اعتراضت ثبت شد و به گروه اعلام شد!")
    return True


CITY_PROJECTS = {
    "bank":      {"name": "🏦 توسعه بانک",         "desc": "+۱۵٪ سود بانک",              "effect": "bank_boost",    "build_hours": 24, "max_level": 3},
    "factory":   {"name": "🏭 منطقه صنعتی",         "desc": "+۲۰٪ تولید کارخانه",         "effect": "factory_boost", "build_hours": 24, "max_level": 3},
    "dog":       {"name": "🐕 پناهگاه سگ‌ها",       "desc": "+۲۰٪ درآمد سگ",             "effect": "dog_boost",     "build_hours": 12, "max_level": 3},
    "park":      {"name": "🌳 پارک شهر",             "desc": "+۱۰٪ پاداش فعالیت روزانه",  "effect": "daily_boost",   "build_hours": 18, "max_level": 2},
    "power":     {"name": "⚡ نیروگاه",              "desc": "-۲۰٪ زمان انتظار",           "effect": "cooldown_cut",  "build_hours": 36, "max_level": 2},
}

CITY_CONTRACTS = {
    "industrial": {"name": "🏭 قرارداد صنعتی",  "pros": "+۲۰٪ تولید کارخانه", "cons": "-۱۰٪ سود بانک",    "pro_effect": "factory_boost", "con_effect": "bank_cut"},
    "banking":    {"name": "🏦 قرارداد بانکی",   "pros": "+۱۵٪ سود بانک",      "cons": "-۵٪ درآمد هاپ",    "pro_effect": "bank_boost",    "con_effect": "hop_cut"},
    "welfare":    {"name": "🤝 قرارداد رفاهی",   "pros": "+۱۰٪ درآمد سگ",      "cons": "-۵٪ تولید کارخانه","pro_effect": "dog_boost",     "con_effect": "factory_cut"},
}

def get_project_setting(key, default=None):
    with db_conn() as conn:
        r = conn.execute("SELECT value FROM mayor_project_settings WHERE key=?", (key,)).fetchone()
    return r["value"] if r else default

def set_project_setting(key, value):
    with db_conn() as conn:
        conn.execute("INSERT OR REPLACE INTO mayor_project_settings (key,value) VALUES (?,?)", (key, str(value)))
        conn.commit()

def get_contract_setting(key, default=None):
    with db_conn() as conn:
        r = conn.execute("SELECT value FROM mayor_contract_settings WHERE key=?", (key,)).fetchone()
    return r["value"] if r else default

def set_contract_setting(key, value):
    with db_conn() as conn:
        conn.execute("INSERT OR REPLACE INTO mayor_contract_settings (key,value) VALUES (?,?)", (key, str(value)))
        conn.commit()

def get_project_cost(project_key, level=1):
    raw = get_project_setting(f"cost_{project_key}_lv{level}", 50000)
    return int(raw)

def get_contract_min_days():
    raw = get_contract_setting("min_days", 3)
    return int(raw)

def get_active_project(group_id, project_key):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor_projects WHERE group_id=? AND project_key=? ORDER BY id DESC LIMIT 1",
            (group_id, project_key)
        ).fetchone()

def get_active_contract(group_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM mayor_contracts WHERE group_id=? AND status='active' AND expires_at > datetime('now') ORDER BY id DESC LIMIT 1",
            (group_id,)
        ).fetchone()

def project_effect_multiplier(group_id, effect_key) -> float:
    from datetime import datetime
    with db_conn() as conn:
        rows = conn.execute(
            "SELECT * FROM mayor_projects WHERE group_id=? AND status='done'",
            (group_id,)
        ).fetchall()
    mult = 1.0
    for p in rows:
        proj = CITY_PROJECTS.get(p["project_key"])
        if proj and proj["effect"] == effect_key:
            mult += 0.15 * p["level"]
    return mult

def contract_effect(group_id, effect_key) -> float:
    c = get_active_contract(group_id)
    if not c:
        return 1.0
    contract = CITY_CONTRACTS.get(c["contract_key"])
    if not contract:
        return 1.0
    boosts = {"factory_boost": 1.2, "bank_boost": 1.15, "dog_boost": 1.1}
    cuts   = {"bank_cut": 0.9, "hop_cut": 0.95, "factory_cut": 0.95}
    if contract["pro_effect"] == effect_key:
        return boosts.get(effect_key, 1.0)
    if contract["con_effect"] == effect_key:
        return cuts.get(effect_key, 1.0)
    return 1.0

async def projects_panel(query, group_id, user):
    mayor = get_mayor(group_id)
    is_mayor = mayor and mayor["user_id"] == user.id

    txt = "🏗 *پروژه‌های شهری*\n\n"
    kb = []

    for key, p in CITY_PROJECTS.items():
        proj = get_active_project(group_id, key)
        if not proj:
            status = "⬜ ساخته نشده"
            lv = 0
        elif proj["status"] == "building":
            from datetime import datetime
            done = datetime.fromisoformat(proj["done_at"])
            rem = done - datetime.now()
            h = int(rem.total_seconds() // 3600)
            m = int((rem.total_seconds() % 3600) // 60)
            status = f"🔨 در حال ساخت ({h}:{m:02d} مانده)"
            lv = proj["level"] - 1
        else:
            lv = proj["level"]
            status = f"✅ سطح {lv}"

        max_lv = p["max_level"]
        txt += f"{p['name']} — {status}\n📋 {p['desc']}\n\n"

        if is_mayor and lv < max_lv and (not proj or proj["status"] == "done"):
            cost = get_project_cost(key, lv + 1)
            kb.append([cbtn(f"🔨 {p['name']} سطح {lv+1} ({cost:,} پوینت)", f"mproj_build_{key}")])

    contract = get_active_contract(group_id)
    if contract:
        c = CITY_CONTRACTS[contract["contract_key"]]
        from datetime import datetime
        exp = datetime.fromisoformat(contract["expires_at"])
        rem = exp - datetime.now()
        d = int(rem.total_seconds() // 86400)
        txt += f"📜 *قرارداد فعال:* {c['name']}\n✅ {c['pros']} | ❌ {c['cons']}\n⏳ {d} روز مانده\n"
    else:
        txt += "📜 *قرارداد:* ندارد\n"
        if is_mayor:
            kb.append([cbtn("📜 امضای قرارداد", "mproj_contract_menu")])

    kb.append([cbtn("🔙 بازگشت", "mayor_back")])
    await query.edit_message_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

async def mayor_projects_callback(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    query = update.callback_query
    data = query.data
    user = update.effective_user
    group_id = update.effective_chat.id if update.effective_chat else None

    if not data.startswith("mproj_"):
        return False

    await query.answer()

    if data == "mproj_panel":
        await projects_panel(query, group_id, user)
        return True

    if data.startswith("mproj_build_"):
        key = data.replace("mproj_build_", "")
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True

        proj = get_active_project(group_id, key)
        p = CITY_PROJECTS[key]
        cur_level = proj["level"] if proj and proj["status"] == "done" else 0
        if cur_level >= p["max_level"]:
            await query.answer("❌ حداکثر سطح!", show_alert=True); return True
        if proj and proj["status"] == "building":
            await query.answer("❌ در حال ساخته!", show_alert=True); return True

        cost = get_project_cost(key, cur_level + 1)
        half = cost // 2

        u = get_user(user.id)
        if not u or u["hop_points"] < half:
            await query.answer(f"❌ پوینت کافی نداری! نیاز: {half:,}", show_alert=True); return True

        with db_conn() as conn:
            grp = conn.execute("SELECT treasury FROM groups WHERE group_id=?", (group_id,)).fetchone()
        treasury = grp["treasury"] if grp else 0
        if treasury < half:
            await query.answer(f"❌ خزانه کافی نیست! نیاز: {half:,}", show_alert=True); return True

        from datetime import datetime, timedelta
        build_hours = p["build_hours"]
        done_at = (datetime.now() + timedelta(hours=build_hours)).isoformat()

        with db_conn() as conn:
            conn.execute("UPDATE users SET hop_points=hop_points-? WHERE user_id=?", (half, user.id))
            conn.execute("UPDATE groups SET treasury=treasury-? WHERE group_id=?", (half, group_id))
            conn.execute(
                "INSERT INTO mayor_projects (group_id, project_key, level, done_at, status) VALUES (?,?,?,?,'building')",
                (group_id, key, cur_level + 1, done_at)
            )
            conn.commit()

        await query.edit_message_text(
            f"✅ *ساخت {p['name']} شروع شد!*\n\n"
            f"💰 هزینه: {half:,} از شما + {half:,} از خزانه\n"
            f"⏳ زمان ساخت: {build_hours} ساعت\n\n"
            f"بعد از اتمام، مزیت فعال میشه.",
            parse_mode="Markdown"
        )
        try:
            await context.bot.send_message(
                group_id,
                f"🏗 *پروژه جدید شهری!*\n\n{p['name']} در حال ساخت...\n⏳ {build_hours} ساعت دیگه آماده میشه!",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    if data == "mproj_contract_menu":
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        min_days = get_contract_min_days()
        kb = []
        for key, c in CITY_CONTRACTS.items():
            kb.append([cbtn(f"{c['name']}", f"mproj_sign_{key}")])
        kb.append([cbtn("🔙 بازگشت", "mproj_panel")])
        await query.edit_message_text(
            f"📜 *امضای قرارداد*\n\n"
            f"⚠️ در هر زمان فقط یک قرارداد فعاله.\n"
            f"⏳ حداقل مدت: {min_days} روز\n\n"
            + "\n".join([f"{c['name']}\n✅ {c['pros']} | ❌ {c['cons']}" for c in CITY_CONTRACTS.values()]),
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup(kb)
        )
        return True

    if data.startswith("mproj_sign_"):
        key = data.replace("mproj_sign_", "")
        mayor = get_mayor(group_id)
        if not mayor or mayor["user_id"] != user.id:
            await query.answer("❌ فقط شهردار!", show_alert=True); return True
        if get_active_contract(group_id):
            await query.answer("❌ قرارداد فعال دارید!", show_alert=True); return True
        if key not in CITY_CONTRACTS:
            return True

        min_days = get_contract_min_days()
        context.user_data["signing_contract"] = key
        context.user_data["signing_contract_group"] = group_id
        context.user_data["waiting_contract_days"] = True
        c = CITY_CONTRACTS[key]
        await query.edit_message_text(
            f"📜 *{c['name']}*\n\n✅ {c['pros']}\n❌ {c['cons']}\n\n"
            f"چند روز؟ (حداقل {min_days} روز)\nعدد رو بنویس:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 انصراف", "mproj_contract_menu")]])
        )
        return True

    if data == "mproj_admin_panel":
        if not is_admin(user.id):
            await query.answer("❌ فقط ادمین!", show_alert=True); return True
        min_days = get_contract_min_days()
        txt = "⚙️ *تنظیمات پروژه‌ها*\n\n"
        txt += f"📅 حداقل مدت قرارداد: {min_days} روز\n\n"
        txt += "💰 *هزینه پروژه‌ها:*\n"
        for key, p in CITY_PROJECTS.items():
            for lv in range(1, p["max_level"] + 1):
                cost = get_project_cost(key, lv)
                txt += f"• {p['name']} سطح {lv}: {cost:,}\n"
        kb = [
            [cbtn("✏️ تغییر هزینه پروژه", "mproj_admin_setcost")],
            [cbtn("✏️ تغییر حداقل روز قرارداد", "mproj_admin_setdays")],
        ]
        await query.edit_message_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data == "mproj_admin_setdays":
        if not is_admin(user.id): return True
        context.user_data["admin_setting"] = "contract_min_days"
        await query.edit_message_text(
            "عدد حداقل روز قرارداد رو بنویس:",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 انصراف", "mproj_admin_panel")]])
        )
        return True

    if data == "mproj_admin_setcost":
        if not is_admin(user.id): return True
        kb = []
        for key, p in CITY_PROJECTS.items():
            for lv in range(1, p["max_level"] + 1):
                kb.append([cbtn(f"{p['name']} سطح {lv}", f"mproj_admin_cost_{key}_{lv}")])
        kb.append([cbtn("🔙 بازگشت", "mproj_admin_panel")])
        await query.edit_message_text("کدوم پروژه رو میخوای؟", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("mproj_admin_cost_"):
        if not is_admin(user.id): return True
        parts = data.replace("mproj_admin_cost_", "").rsplit("_", 1)
        key, lv = parts[0], parts[1]
        context.user_data["admin_setting"] = f"project_cost_{key}_lv{lv}"
        context.user_data["admin_setting_label"] = f"{CITY_PROJECTS[key]['name']} سطح {lv}"
        await query.edit_message_text(
            f"هزینه {CITY_PROJECTS[key]['name']} سطح {lv} رو بنویس:",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 انصراف", "mproj_admin_panel")]])
        )
        return True

    if data == "mayor_back":
        await query.edit_message_text("🏛 پنل بسته شد.")
        return True

    return False

async def handle_mayor_project_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message or not update.message.text:
        return False

    if context.user_data.get("admin_setting") and is_admin(update.effective_user.id):
        setting = context.user_data.pop("admin_setting")
        label = context.user_data.pop("admin_setting_label", setting)
        text = update.message.text.strip()
        if not text.isdigit():
            await update.message.reply_text("❌ عدد بنویس!")
            return True
        val = int(text)
        if setting == "contract_min_days":
            set_contract_setting("min_days", val)
            await update.message.reply_text(f"✅ حداقل روز قرارداد: {val} روز")
        else:
            pk = setting.replace("project_cost_", "")
            parts = pk.rsplit("_lv", 1)
            set_project_setting(f"cost_{parts[0]}_lv{parts[1]}", val)
            await update.message.reply_text(f"✅ هزینه {label}: {val:,} پوینت")
        return True

    if context.user_data.get("waiting_contract_days"):
        contract_key = context.user_data.get("signing_contract")
        group_id = context.user_data.get("signing_contract_group")
        if not contract_key or not group_id:
            return False
        text = update.message.text.strip()
        if not text.isdigit():
            await update.message.reply_text("❌ عدد بنویس!")
            return True
        days = int(text)
        min_days = get_contract_min_days()
        if days < min_days:
            await update.message.reply_text(f"❌ حداقل {min_days} روز!")
            return True

        context.user_data.pop("waiting_contract_days", None)
        context.user_data.pop("signing_contract", None)
        context.user_data.pop("signing_contract_group", None)

        from datetime import datetime, timedelta
        expires = (datetime.now() + timedelta(days=days)).isoformat()
        with db_conn() as conn:
            conn.execute(
                "INSERT INTO mayor_contracts (group_id, contract_key, expires_at, status) VALUES (?,?,?,'active')",
                (group_id, contract_key, expires)
            )
            conn.commit()

        c = CITY_CONTRACTS[contract_key]
        await update.message.reply_text(
            f"✅ *قرارداد امضا شد!*\n\n{c['name']}\n✅ {c['pros']}\n❌ {c['cons']}\n⏳ {days} روز",
            parse_mode="Markdown"
        )
        try:
            await context.bot.send_message(
                group_id,
                f"📜 *قرارداد جدید!*\n\n{c['name']}\n✅ {c['pros']}\n❌ {c['cons']}\n⏳ {days} روز",
                parse_mode="Markdown"
            )
        except Exception:
            pass
        return True

    return False


LEADER_COMMAND_COOLDOWN   = 86_400
LEADER_COMMAND_DURATION   = 43_200
LEADER_TERM_DAYS          = 30
LEADER_ELECTION_DAYS      = 3
LEADER_MIN_CANDIDATES     = 1

NATIONAL_COMMANDS = {
    "hop_boost":      {"name": "🦴 افزایش درآمد هاپ",           "hop_mult": 1.5,  "desc": "درآمد هاپ همه بازیکنان ۵۰٪ بیشتر میشه"},
    "factory_boost":  {"name": "🏭 افزایش تولید کارخانه‌ها",    "factory_mult": 1.4, "desc": "سرعت تولید کارخانه‌ها ۴۰٪ بیشتر میشه"},
    "dog_boost":      {"name": "🐕 افزایش درآمد سگ‌ها",          "dog_mult": 1.5,  "desc": "درآمد سگ‌ها ۵۰٪ بیشتر میشه"},
    "bank_boost":     {"name": "🏦 افزایش سود بانک",             "bank_mult": 1.3, "desc": "سود بانک ۳۰٪ بیشتر میشه"},
    "tax_cut":        {"name": "💸 کاهش مالیات",                 "tax_mult": 0.5,  "desc": "مالیات کشور ۵۰٪ کمتر میشه"},
    "daily_bonus":    {"name": "🎁 افزایش جایزه مأموریت روزانه", "daily_bonus": 500, "desc": "+۵۰۰ جایزه اضافه به هاپ روزانه"},
    "cd_cut":         {"name": "⚡ کاهش کولداون",                "cd_mult": 0.8,   "desc": "زمان انتظار ۲۰٪ کمتر میشه"},
    "all_boost":      {"name": "✨ افزایش درآمد همه مشاغل",      "all_mult": 1.25, "desc": "کل درآمدها ۲۵٪ بیشتر میشه"},
}

NATIONAL_LAWS = {
    "tax_5":          {"name": "⚖️ مالیات کشور ۵٪",         "tax_rate": 0.05},
    "tax_2":          {"name": "⚖️ مالیات کشور ۲٪",         "tax_rate": 0.02},
    "bank_bonus":     {"name": "🏦 افزایش سود بانک",         "bank_rate_bonus": 0.01},
    "factory_bonus":  {"name": "🏭 درآمد کارخانه بیشتر",    "factory_rate": 1.15},
    "upgrade_disc":   {"name": "🔧 کاهش هزینه ارتقا",        "upgrade_disc": 0.85},
    "item_disc":      {"name": "🛒 کاهش هزینه آیتم",         "item_disc": 0.90},
}

NATIONAL_PROJECTS = {
    "central_bank":   {"name": "🏦 بانک مرکزی",      "cost": 5_000_000,  "build_hours": 24, "effect": "bank_mult:1.2",  "desc": "سود بانک ۲۰٪ بیشتر میشه"},
    "industrial_zone":{"name": "🏭 شهرک صنعتی",      "cost": 8_000_000,  "build_hours": 36, "effect": "factory_mult:1.3","desc": "تولید کارخانه ۳۰٪ بیشتر میشه"},
    "airport":        {"name": "🛫 فرودگاه",           "cost": 12_000_000, "build_hours": 48, "effect": "hop_mult:1.15",  "desc": "درآمد هاپ ۱۵٪ بیشتر میشه"},
    "seaport":        {"name": "🚢 بندر",              "cost": 10_000_000, "build_hours": 42, "effect": "trade_mult:1.25","desc": "درآمد تجارت ۲۵٪ بیشتر میشه"},
    "powerplant":     {"name": "⚡ نیروگاه ملی",      "cost": 6_000_000,  "build_hours": 30, "effect": "cd_mult:0.85",   "desc": "کولداون همه ۱۵٪ کمتر میشه"},
    "tech_center":    {"name": "📡 مرکز فناوری",      "cost": 15_000_000, "build_hours": 72, "effect": "all_mult:1.1",   "desc": "درآمد همه مشاغل ۱۰٪ بیشتر میشه"},
}

MINISTER_ROLES = {
    "economy":    {"name": "وزیر اقتصاد 💹",      "effect": "all_mult:1.05",    "desc": "+۵٪ درآمد کل کشور"},
    "industry":   {"name": "وزیر صنعت 🏭",        "effect": "factory_mult:1.1", "desc": "+۱۰٪ تولید کارخانه"},
    "bank":       {"name": "وزیر بانک 🏦",         "effect": "bank_mult:1.03",   "desc": "+۳٪ سود بانک"},
    "agriculture":{"name": "وزیر کشاورزی 🌾",     "effect": "dog_mult:1.08",    "desc": "+۸٪ درآمد سگ‌ها"},
    "livestock":  {"name": "وزیر دامداری 🐄",      "effect": "dog_mult:1.06",    "desc": "+۶٪ درآمد سگ‌ها"},
    "trade":      {"name": "وزیر تجارت 🤝",        "effect": "hop_mult:1.08",    "desc": "+۸٪ درآمد هاپ"},
}

NATIONAL_EVENTS = {
    "hop_festival":   {"name": "🎉 جشنواره هاپ",    "hours": 6,  "hop_mult": 2.0,  "cost": 500_000},
    "industry_week":  {"name": "🏭 هفته صنعت",       "hours": 24, "factory_mult":1.5,"cost": 800_000},
    "dog_party":      {"name": "🐕 جشن سگ‌ها",       "hours": 6,  "dog_mult": 2.0,  "cost": 400_000},
    "login_bonus":    {"name": "🎁 پاداش ورود",       "hours": 12, "daily_bonus": 1000,"cost": 300_000},
    "golden_friday":  {"name": "🌟 جمعه طلایی",      "hours": 8,  "all_mult": 1.5,  "cost": 1_000_000},
    "double_income":  {"name": "💰 دو برابر درآمد",  "hours": 4,  "all_mult": 2.0,  "cost": 1_500_000},
}

EMERGENCY_TYPES = {
    "war":          {"name": "⚔️ جنگ",            "hours": 12, "tax_mult": 0.3, "all_mult": 0.8},
    "recession":    {"name": "📉 رکود اقتصادی",    "hours": 24, "tax_mult": 0.5, "factory_mult": 0.7},
    "crisis":       {"name": "💥 بحران مالی",       "hours": 12, "bank_mult": 0.5,"all_mult": 0.9},
    "special":      {"name": "🚨 وضعیت ویژه",      "hours": 6,  "cd_mult": 0.7,  "all_mult": 1.1},
}

def init_leader_tables():
    with db_conn() as conn:
        conn.execute("""CREATE TABLE IF NOT EXISTS leader (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER UNIQUE,
            username    TEXT,
            first_name  TEXT,
            elected_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            term_end    TEXT,
            popularity  INTEGER DEFAULT 100
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_elections (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            status      TEXT DEFAULT 'candidacy',
            started_by  INTEGER,
            started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            vote_start  TEXT DEFAULT NULL,
            ended_at    TEXT DEFAULT NULL
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_candidates (
            election_id INTEGER,
            user_id     INTEGER,
            username    TEXT,
            first_name  TEXT,
            pledges     TEXT DEFAULT '',
            joined_at   TEXT DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (election_id, user_id)
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_votes (
            election_id INTEGER,
            voter_id    INTEGER,
            candidate_id INTEGER,
            voted_at    TEXT DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (election_id, voter_id)
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_commands (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            command_key TEXT,
            issued_at   TEXT DEFAULT CURRENT_TIMESTAMP,
            expires_at  TEXT,
            is_active   INTEGER DEFAULT 1
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_laws (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            law_key     TEXT,
            law_name    TEXT,
            enacted_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            expires_at  TEXT,
            is_active   INTEGER DEFAULT 1
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS national_treasury (
            id      INTEGER PRIMARY KEY CHECK(id=1),
            balance REAL DEFAULT 0
        )""")
        conn.execute("INSERT OR IGNORE INTO national_treasury (id,balance) VALUES (1,0)")
        conn.execute("""CREATE TABLE IF NOT EXISTS treasury_log (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            amount      REAL,
            reason      TEXT,
            user_id     INTEGER,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS city_budgets (
            group_id    INTEGER PRIMARY KEY,
            amount      REAL DEFAULT 0,
            assigned_at TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS national_projects (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            project_key TEXT UNIQUE,
            status      TEXT DEFAULT 'building',
            started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            done_at     TEXT
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS ministers (
            role        TEXT PRIMARY KEY,
            user_id     INTEGER,
            username    TEXT,
            first_name  TEXT,
            appointed_at TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS national_events (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            event_key   TEXT,
            started_at  TEXT DEFAULT CURRENT_TIMESTAMP,
            expires_at  TEXT,
            is_active   INTEGER DEFAULT 1
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS emergency_state (
            id          INTEGER PRIMARY KEY CHECK(id=1),
            etype       TEXT DEFAULT NULL,
            started_at  TEXT DEFAULT NULL,
            expires_at  TEXT DEFAULT NULL,
            is_active   INTEGER DEFAULT 0
        )""")
        conn.execute("INSERT OR IGNORE INTO emergency_state (id) VALUES (1)")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_ratings (
            user_id     INTEGER PRIMARY KEY,
            rating      TEXT,
            rated_at    TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_log (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER,
            action_type TEXT,
            action_desc TEXT,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.execute("""CREATE TABLE IF NOT EXISTS leader_pledges (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            election_id INTEGER,
            user_id     INTEGER,
            pledge_text TEXT,
            fulfilled   INTEGER DEFAULT 0,
            created_at  TEXT DEFAULT CURRENT_TIMESTAMP
        )""")
        conn.commit()

def get_leader():
    with db_conn() as conn:
        return conn.execute("SELECT * FROM leader LIMIT 1").fetchone()

def set_leader(user_id, username, first_name, term_days=LEADER_TERM_DAYS):
    term_end = (datetime.now() + timedelta(days=term_days)).isoformat()
    with db_conn() as conn:
        conn.execute("DELETE FROM leader")
        conn.execute(
            "INSERT INTO leader (user_id,username,first_name,elected_at,term_end,popularity) VALUES (?,?,?,?,?,100)",
            (user_id, username, first_name, datetime.now().isoformat(), term_end)
        )
        conn.commit()

def remove_leader():
    with db_conn() as conn:
        conn.execute("DELETE FROM leader")
        conn.commit()

def is_leader(user_id):
    ld = get_leader()
    return ld is not None and ld["user_id"] == user_id

def log_leader(user_id, action_type, desc):
    with db_conn() as conn:
        conn.execute(
            "INSERT INTO leader_log (user_id,action_type,action_desc,created_at) VALUES (?,?,?,?)",
            (user_id, action_type, desc, datetime.now().isoformat())
        )
        conn.commit()

def get_treasury() -> float:
    with db_conn() as conn:
        row = conn.execute("SELECT balance FROM national_treasury WHERE id=1").fetchone()
        return row["balance"] if row else 0.0

def change_treasury(amount: float, reason: str, user_id: int = 0):
    with db_conn() as conn:
        conn.execute("UPDATE national_treasury SET balance=MAX(0,balance+?) WHERE id=1", (amount,))
        conn.execute(
            "INSERT INTO treasury_log (amount,reason,user_id,created_at) VALUES (?,?,?,?)",
            (amount, reason, user_id, datetime.now().isoformat())
        )
        conn.commit()

def get_active_command():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM leader_commands WHERE is_active=1 AND expires_at>datetime('now') ORDER BY id DESC LIMIT 1"
        ).fetchone()

def get_active_laws():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM leader_laws WHERE is_active=1 AND (expires_at IS NULL OR expires_at>datetime('now'))"
        ).fetchall()

def get_active_events():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM national_events WHERE is_active=1 AND expires_at>datetime('now')"
        ).fetchall()

def get_emergency():
    with db_conn() as conn:
        row = conn.execute("SELECT * FROM emergency_state WHERE id=1").fetchone()
        if row and row["is_active"] and row["expires_at"]:
            if datetime.fromisoformat(row["expires_at"]) > datetime.now():
                return row
            conn.execute("UPDATE emergency_state SET is_active=0, etype=NULL WHERE id=1")
            conn.commit()
        return None

def can_issue_command():
    with db_conn() as conn:
        row = conn.execute(
            "SELECT issued_at FROM leader_commands ORDER BY id DESC LIMIT 1"
        ).fetchone()
    if not row:
        return True, 0
    diff = (datetime.now() - datetime.fromisoformat(row["issued_at"])).total_seconds()
    left = max(0, int(LEADER_COMMAND_COOLDOWN - diff))
    return left == 0, left

def get_national_hop_multiplier() -> float:
    mult = 1.0
    cmd = get_active_command()
    if cmd:
        cfg = NATIONAL_COMMANDS.get(cmd["command_key"], {})
        mult *= cfg.get("hop_mult", 1.0)
        mult *= cfg.get("all_mult", 1.0)
    for ev in get_active_events():
        ecfg = NATIONAL_EVENTS.get(ev["event_key"], {})
        mult *= ecfg.get("hop_mult", 1.0)
        mult *= ecfg.get("all_mult", 1.0)
    em = get_emergency()
    if em:
        ecfg = EMERGENCY_TYPES.get(em["etype"], {})
        mult *= ecfg.get("all_mult", 1.0)
        mult *= ecfg.get("hop_mult", 1.0)
    return mult

def get_national_daily_bonus() -> int:
    bonus = 0
    cmd = get_active_command()
    if cmd:
        bonus += NATIONAL_COMMANDS.get(cmd["command_key"], {}).get("daily_bonus", 0)
    for ev in get_active_events():
        bonus += NATIONAL_EVENTS.get(ev["event_key"], {}).get("daily_bonus", 0)
    return bonus

def get_national_cd_multiplier() -> float:
    mult = 1.0
    cmd = get_active_command()
    if cmd:
        mult *= NATIONAL_COMMANDS.get(cmd["command_key"], {}).get("cd_mult", 1.0)
    em = get_emergency()
    if em:
        mult *= EMERGENCY_TYPES.get(em["etype"], {}).get("cd_mult", 1.0)
    return mult

def get_national_dog_multiplier() -> float:
    mult = 1.0
    cmd = get_active_command()
    if cmd:
        cfg = NATIONAL_COMMANDS.get(cmd["command_key"], {})
        mult *= cfg.get("dog_mult", 1.0)
        mult *= cfg.get("all_mult", 1.0)
    for ev in get_active_events():
        ecfg = NATIONAL_EVENTS.get(ev["event_key"], {})
        mult *= ecfg.get("dog_mult", 1.0)
        mult *= ecfg.get("all_mult", 1.0)
    em = get_emergency()
    if em:
        mult *= EMERGENCY_TYPES.get(em["etype"], {}).get("all_mult", 1.0)
    return mult

def get_national_factory_multiplier() -> float:
    mult = 1.0
    cmd = get_active_command()
    if cmd:
        cfg = NATIONAL_COMMANDS.get(cmd["command_key"], {})
        mult *= cfg.get("factory_mult", 1.0)
        mult *= cfg.get("all_mult", 1.0)
    for ev in get_active_events():
        ecfg = NATIONAL_EVENTS.get(ev["event_key"], {})
        mult *= ecfg.get("factory_mult", 1.0)
        mult *= ecfg.get("all_mult", 1.0)
    em = get_emergency()
    if em:
        emcfg = EMERGENCY_TYPES.get(em["etype"], {})
        mult *= emcfg.get("factory_mult", 1.0)
        mult *= emcfg.get("all_mult", 1.0)
    return mult

async def leader_panel_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    ensure_user(user.id, user.username or "", user.first_name)
    ld = get_leader()
    if is_admin(user.id):
        await _send_admin_leader_panel(update, context, ld)
        return
    if ld and ld["user_id"] == user.id:
        await _send_leader_panel(update, context, ld)
        return
    if not ld:
        txt = "👑 *رهبر*\n\nهنوز رهبری انتخاب نشده.\nانتخابات رهبری توسط ادمین اصلی برگزار میشه."
    else:
        term_end = datetime.fromisoformat(ld["term_end"])
        days_left = max(0, (term_end - datetime.now()).days)
        cmd = get_active_command()
        cmd_txt = f"\n👑 *فرمان فعال:* {NATIONAL_COMMANDS.get(cmd['command_key'],{}).get('name','—')}" if cmd else ""
        em = get_emergency()
        em_txt = f"\n🚨 *وضعیت اضطراری:* {EMERGENCY_TYPES.get(em['etype'],{}).get('name','—')}" if em else ""
        txt = (
            f"👑 *رهبر کشور*\n\n"
            f"👤 {ld['first_name']}\n"
            f"📊 محبوبیت: {ld['popularity']}٪\n"
            f"⏳ {days_left} روز تا پایان دوره"
            f"{cmd_txt}{em_txt}"
        )
    kb = []
    if ld:
        kb.append([cbtn("👍 رضایت", "leader_rate_up"), cbtn("👎 نارضایتی", "leader_rate_down")])
    await update.message.reply_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb) if kb else None)

async def _send_admin_leader_panel(update_or_query, context, ld):
    is_query = hasattr(update_or_query, "edit_message_text")
    send = update_or_query.edit_message_text if is_query else update_or_query.message.reply_text
    has_election = _get_active_election()
    treasury = get_treasury()
    ld_txt = f"👤 {ld['first_name']} | محبوبیت: {ld['popularity']}٪" if ld else "❌ رهبری وجود ندارد"
    txt = (
        f"⚙️ *پنل ادمین — مدیریت رهبر*\n\n"
        f"👑 رهبر فعلی: {ld_txt}\n"
        f"🏦 خزانه ملی: {treasury:,.0f} هاپ‌پوینت\n"
        f"🗳️ انتخابات: {'فعال ✅' if has_election else 'غیرفعال ❌'}"
    )
    kb = []
    if not has_election:
        kb.append([cbtn("🗳️ برگزاری انتخابات رهبری", "ladmin_start_election")])
    else:
        if has_election["status"] == "candidacy":
            kb.append([cbtn("🗳️ شروع رأی‌گیری", f"ladmin_start_voting_{has_election['id']}")])
        elif has_election["status"] == "voting":
            kb.append([cbtn("✅ اعلام نتیجه و پایان", f"ladmin_end_election_{has_election['id']}")])
        kb.append([cbtn("❌ لغو انتخابات", "ladmin_cancel_election")])
    if ld:
        kb.append([cbtn("🚫 برکناری رهبر", "ladmin_remove_leader")])
    kb.append([cbtn("💰 واریز به خزانه", "ladmin_treasury_add")])
    kb.append([cbtn("📜 لاگ تصمیمات", "ladmin_view_log")])
    await send(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

async def _send_leader_panel(update_or_query, context, ld):
    is_query = hasattr(update_or_query, "edit_message_text")
    send = update_or_query.edit_message_text if is_query else update_or_query.message.reply_text
    treasury = get_treasury()
    cmd = get_active_command()
    em = get_emergency()
    term_end = datetime.fromisoformat(ld["term_end"])
    days_left = max(0, (term_end - datetime.now()).days)
    can_cmd, cd_left = can_issue_command()
    cmd_txt = f"فعال: {NATIONAL_COMMANDS.get(cmd['command_key'],{}).get('name','—')}" if cmd else ("✅ آماده" if can_cmd else f"⌛️ {cd_left//3600}:{(cd_left%3600)//60:02d}:00")
    em_txt = f"\n🚨 اضطراری: {EMERGENCY_TYPES.get(em['etype'],{}).get('name','—')}" if em else ""
    txt = (
        f"👑 *پنل رهبر*\n\n"
        f"📊 محبوبیت: {ld['popularity']}٪\n"
        f"⏳ {days_left} روز تا پایان دوره\n"
        f"🏦 خزانه: {treasury:,.0f}\n"
        f"🎖️ فرمان: {cmd_txt}"
        f"{em_txt}"
    )
    kb = [
        [cbtn("👑 فرمان ملی", "ldr_cmd_menu"), cbtn("⚖️ قانون‌گذاری", "ldr_law_menu")],
        [cbtn("🏦 خزانه ملی", "ldr_treasury"), cbtn("🏛️ بودجه شهرها", "ldr_budget")],
        [cbtn("🏗️ پروژه‌های ملی", "ldr_projects"), cbtn("🎉 رویدادها", "ldr_events")],
        [cbtn("👥 شورای وزیران", "ldr_ministers"), cbtn("🚨 وضعیت اضطراری", "ldr_emergency")],
        [cbtn("🏛️ مدیریت شهرداران", "ldr_mayors_menu"), cbtn("📊 گزارش عملکرد", "ldr_report")],
    ]
    await send(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))

def _get_active_election():
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM leader_elections WHERE status IN ('candidacy','voting') ORDER BY id DESC LIMIT 1"
        ).fetchone()

def _start_election(started_by):
    with db_conn() as conn:
        conn.execute(
            "INSERT INTO leader_elections (status,started_by,started_at) VALUES ('candidacy',?,?)",
            (started_by, datetime.now().isoformat())
        )
        conn.commit()
        return conn.execute("SELECT last_insert_rowid() as id").fetchone()["id"]

def _cancel_election():
    with db_conn() as conn:
        conn.execute(
            "UPDATE leader_elections SET status='cancelled', ended_at=? WHERE status IN ('candidacy','voting')",
            (datetime.now().isoformat(),)
        )
        conn.commit()

def _move_to_voting(election_id):
    with db_conn() as conn:
        conn.execute(
            "UPDATE leader_elections SET status='voting', vote_start=? WHERE id=?",
            (datetime.now().isoformat(), election_id)
        )
        conn.commit()

def _get_candidates(election_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT * FROM leader_candidates WHERE election_id=?", (election_id,)
        ).fetchall()

def _get_votes(election_id):
    with db_conn() as conn:
        return conn.execute(
            "SELECT candidate_id, COUNT(*) as cnt FROM leader_votes WHERE election_id=? GROUP BY candidate_id ORDER BY cnt DESC",
            (election_id,)
        ).fetchall()

def _finish_election(election_id):
    votes = _get_votes(election_id)
    candidates = {r["user_id"]: r for r in _get_candidates(election_id)}
    winner = None
    if votes:
        top = votes[0]
        winner_id = top["candidate_id"]
        if winner_id in candidates:
            c = candidates[winner_id]
            set_leader(winner_id, c["username"] or "", c["first_name"])
            log_leader(winner_id, "elected", f"با {top['cnt']} رأی به رهبری انتخاب شد")
            winner = c
    with db_conn() as conn:
        conn.execute(
            "UPDATE leader_elections SET status='done', ended_at=? WHERE id=?",
            (datetime.now().isoformat(), election_id)
        )
        conn.commit()
    return winner

async def handle_leader_callback(query, user, data: str, context) -> bool:
    if data.startswith("ldr_vote_"):
        parts = data.replace("ldr_vote_", "").split("_")
        if len(parts) >= 2:
            eid, candidate_id = int(parts[0]), int(parts[1])
            with db_conn() as conn:
                voted = conn.execute(
                    "SELECT 1 FROM leader_votes WHERE election_id=? AND voter_id=?", (eid, user.id)
                ).fetchone()
                if voted:
                    await query.answer("قبلاً رأی دادی!", show_alert=True); return True
                conn.execute(
                    "INSERT INTO leader_votes (election_id,voter_id,candidate_id,voted_at) VALUES (?,?,?,?)",
                    (eid, user.id, candidate_id, datetime.now().isoformat())
                )
                target = conn.execute("SELECT first_name FROM users WHERE user_id=?", (candidate_id,)).fetchone()
                conn.commit()
            name = target["first_name"] if target else str(candidate_id)
            await query.answer(f"✅ رأیت به {name} ثبت شد!", show_alert=True)
            await query.edit_message_text(f"🗳️ رأیت به *{name}* ثبت شد.\nنتیجه را ادمین اعلام می‌کند.", parse_mode="Markdown")
            return True

    if data == "ladmin_start_election":
        if not is_admin(user.id): await query.answer("❌ فقط ادمین!", show_alert=True); return True
        elec = _get_active_election()
        if elec: await query.answer("انتخابات قبلاً فعاله!", show_alert=True); return True
        eid = _start_election(user.id)
        log_leader(user.id, "start_election", f"انتخابات رهبری #{eid} شروع شد")
        await query.edit_message_text(
            f"🗳️ *انتخابات رهبری شروع شد!*\n\nشناسه انتخابات: #{eid}\n\n"
            f"بازیکنان میتونن با دستور /leaderjoin نامزد بشن.\n"
            f"بعد از ثبت‌نام کافی، دکمه رأی‌گیری رو بزن:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("🗳️ شروع رأی‌گیری", f"ladmin_start_voting_{eid}")],
                [cbtn("❌ لغو انتخابات", "ladmin_cancel_election")],
                [cbtn("🔙 بازگشت", "ladmin_panel")],
            ])
        )
        return True

    if data == "ladmin_cancel_election":
        if not is_admin(user.id): await query.answer("❌", show_alert=True); return True
        _cancel_election()
        log_leader(user.id, "cancel_election", "انتخابات لغو شد")
        await query.answer("✅ انتخابات لغو شد", show_alert=True)
        await _send_admin_leader_panel(query, context, get_leader())
        return True

    if data.startswith("ladmin_start_voting_"):
        if not is_admin(user.id): await query.answer("❌", show_alert=True); return True
        eid = int(data.replace("ladmin_start_voting_", ""))
        candidates = _get_candidates(eid)
        if len(candidates) < LEADER_MIN_CANDIDATES:
            await query.answer(f"❌ حداقل {LEADER_MIN_CANDIDATES} نامزد لازمه!", show_alert=True)
            return True
        _move_to_voting(eid)
        log_leader(user.id, "start_voting", f"رأی‌گیری انتخابات #{eid} شروع شد")
        clist = "\n".join(f"• {c['first_name']}" for c in candidates)
        await query.edit_message_text(
            f"🗳️ *رأی‌گیری شروع شد!*\n\nنامزدها:\n{clist}\n\nبازیکنان میتونن با /leadervote رأی بدن.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("✅ اعلام نتیجه و پایان", f"ladmin_end_election_{eid}")],
                [cbtn("❌ لغو", "ladmin_cancel_election")],
            ])
        )
        return True

    if data.startswith("ladmin_end_election_"):
        if not is_admin(user.id): await query.answer("❌", show_alert=True); return True
        eid = int(data.replace("ladmin_end_election_", ""))
        winner = _finish_election(eid)
        txt = f"🎉 *انتخابات پایان یافت!*\n\n👑 رهبر جدید: {winner['first_name']}\n\nتبریک!" if winner else "⚠️ انتخابات پایان یافت اما برنده‌ای مشخص نشد (بدون رأی)."
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 پنل ادمین", "ladmin_panel")]]))
        return True

    if data == "ladmin_remove_leader":
        if not is_admin(user.id): await query.answer("❌", show_alert=True); return True
        ld = get_leader()
        if not ld: await query.answer("رهبری وجود ندارد!", show_alert=True); return True
        await query.edit_message_text(
            f"⚠️ مطمئنی میخوای {ld['first_name']} رو برکنار کنی؟",
            reply_markup=InlineKeyboardMarkup([
                [cbtn("✅ بله، برکنار کن", "ladmin_confirm_remove"), cbtn("❌ انصراف", "ladmin_panel")]
            ])
        )
        return True

    if data == "ladmin_confirm_remove":
        if not is_admin(user.id): await query.answer("❌", show_alert=True); return True
        ld = get_leader()
        name = ld["first_name"] if ld else "نامشخص"
        remove_leader()
        log_leader(user.id, "remove_leader", f"رهبر {name} توسط ادمین برکنار شد")
        await query.edit_message_text(f"✅ {name} برکنار شد.\n\nمی‌تونی انتخابات جدید برگزار کنی.",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ladmin_panel")]]))
        return True

    if data == "ladmin_panel":
        if not is_admin(user.id): return True
        await _send_admin_leader_panel(query, context, get_leader())
        return True

    if data == "ladmin_treasury_add":
        if not is_admin(user.id): return True
        context.user_data["leader_input"] = "admin_treasury_add"
        await query.edit_message_text("💰 مقدار واریز به خزانه ملی رو بنویس:",
            reply_markup=InlineKeyboardMarkup([[cbtn("❌ انصراف", "ladmin_panel")]]))
        return True

    if data == "ladmin_view_log":
        if not is_admin(user.id): return True
        with db_conn() as conn:
            rows = conn.execute("SELECT * FROM leader_log ORDER BY id DESC LIMIT 10").fetchall()
        if not rows:
            await query.answer("لاگی موجود نیست", show_alert=True)
            return True
        txt = "📜 *آخرین تصمیمات:*\n\n"
        for r in rows:
            txt += f"• [{r['created_at'][:16]}] {r['action_type']}: {r['action_desc']}\n"
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ladmin_panel")]]))
        return True

    if not (is_leader(user.id) or is_admin(user.id)):
        if data.startswith(("ldr_", "ldr")):
            await query.answer("❌ فقط رهبر!", show_alert=True); return True

    if data == "ldr_back":
        ld = get_leader()
        if ld: await _send_leader_panel(query, context, ld)
        return True

    if data == "ldr_cmd_menu":
        can, left = can_issue_command()
        cmd = get_active_command()
        if cmd:
            cfg = NATIONAL_COMMANDS.get(cmd["command_key"], {})
            exp = datetime.fromisoformat(cmd["expires_at"])
            rem = int((exp - datetime.now()).total_seconds())
            h, r = divmod(rem, 3600); m, s = divmod(r, 60)
            txt = f"👑 *فرمان فعال:* {cfg.get('name','—')}\n⏳ {h}:{m:02d}:{s:02d} مونده"
            await query.edit_message_text(txt, parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))
            return True
        if not can:
            h, r = divmod(left, 3600); m = r // 60
            await query.answer(f"⌛️ {h} ساعت و {m} دقیقه تا فرمان بعدی", show_alert=True)
            return True
        kb = [[cbtn(cfg["name"], f"ldr_issue_cmd_{key}")] for key, cfg in NATIONAL_COMMANDS.items()]
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("👑 *فرمان ملی انتخاب کن:*\n\nهر فرمان ۱۲ ساعت فعال می‌مونه.",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_issue_cmd_"):
        key = data.replace("ldr_issue_cmd_", "")
        if key not in NATIONAL_COMMANDS:
            await query.answer("❌ نامعتبر", show_alert=True); return True
        cfg = NATIONAL_COMMANDS[key]
        now = datetime.now()
        expires = (now + timedelta(seconds=LEADER_COMMAND_DURATION)).isoformat()
        with db_conn() as conn:
            conn.execute("UPDATE leader_commands SET is_active=0")
            conn.execute(
                "INSERT INTO leader_commands (command_key,issued_at,expires_at,is_active) VALUES (?,?,?,1)",
                (key, now.isoformat(), expires)
            )
            conn.commit()
        log_leader(user.id, "command", f"فرمان ملی: {cfg['name']}")
        await query.edit_message_text(
            f"✅ *فرمان صادر شد!*\n\n{cfg['name']}\n📢 {cfg['desc']}\n⏳ ۱۲ ساعت فعاله.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))
        return True

    if data == "ldr_law_menu":
        active = get_active_laws()
        active_keys = {r["law_key"] for r in active}
        kb = []
        for key, cfg in NATIONAL_LAWS.items():
            if key in active_keys:
                kb.append([cbtn(f"✅ {cfg['name']} (فعال)", f"ldr_revoke_law_{key}")])
            else:
                kb.append([cbtn(cfg["name"], f"ldr_enact_law_{key}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("⚖️ *قانون‌گذاری*\n\nقوانین فعال ✅ رو میتونی لغو کنی:",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_enact_law_"):
        key = data.replace("ldr_enact_law_", "")
        cfg = NATIONAL_LAWS.get(key)
        if not cfg: return True
        with db_conn() as conn:
            conn.execute(
                "INSERT INTO leader_laws (law_key,law_name,enacted_at,is_active) VALUES (?,?,?,1)",
                (key, cfg["name"], datetime.now().isoformat())
            )
            conn.commit()
        log_leader(user.id, "law", f"قانون تصویب شد: {cfg['name']}")
        await query.answer(f"✅ {cfg['name']} تصویب شد!", show_alert=True)
        active = get_active_laws()
        active_keys = {r["law_key"] for r in active}
        kb = []
        for k, c in NATIONAL_LAWS.items():
            kb.append([cbtn(f"✅ {c['name']} (فعال)" if k in active_keys else c["name"],
                           f"ldr_revoke_law_{k}" if k in active_keys else f"ldr_enact_law_{k}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("⚖️ *قانون‌گذاری*", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_revoke_law_"):
        key = data.replace("ldr_revoke_law_", "")
        with db_conn() as conn:
            conn.execute("UPDATE leader_laws SET is_active=0 WHERE law_key=?", (key,))
            conn.commit()
        log_leader(user.id, "law_revoke", f"قانون لغو شد: {key}")
        await query.answer("✅ قانون لغو شد", show_alert=True)
        active = get_active_laws()
        active_keys = {r["law_key"] for r in active}
        kb = []
        for k, c in NATIONAL_LAWS.items():
            kb.append([cbtn(f"✅ {c['name']} (فعال)" if k in active_keys else c["name"],
                           f"ldr_revoke_law_{k}" if k in active_keys else f"ldr_enact_law_{k}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("⚖️ *قانون‌گذاری*", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data == "ldr_treasury":
        bal = get_treasury()
        with db_conn() as conn:
            logs = conn.execute("SELECT * FROM treasury_log ORDER BY id DESC LIMIT 5").fetchall()
        log_txt = ""
        for l in logs:
            sign = "+" if l["amount"] > 0 else ""
            log_txt += f"\n• {sign}{l['amount']:,.0f} — {l['reason']}"
        await query.edit_message_text(
            f"🏦 *خزانه ملی*\n\n💰 موجودی: {bal:,.0f}\n\n📜 آخرین تراکنش‌ها:{log_txt}",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("💸 مصرف از خزانه", "ldr_treasury_spend")],[cbtn("🔙 بازگشت", "ldr_back")]]))
        return True

    if data == "ldr_treasury_spend":
        context.user_data["leader_input"] = "treasury_spend"
        await query.edit_message_text(
            "💸 مقدار و دلیل مصرف از خزانه رو بنویس:\n(فرمت: مقدار|دلیل)\nمثال: 500000|ساخت فرودگاه",
            reply_markup=InlineKeyboardMarkup([[cbtn("❌ انصراف", "ldr_treasury")]]))
        return True

    if data == "ldr_budget":
        with db_conn() as conn:
            groups = conn.execute("SELECT * FROM groups ORDER BY treasury DESC LIMIT 10").fetchall()
        kb = [[cbtn(f"🏙️ {g['title'] or g['group_id']}", f"ldr_budget_city_{g['group_id']}")] for g in groups]
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("🏛️ *بودجه شهرها*\n\nشهر رو انتخاب کن:",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_budget_city_"):
        gid = int(data.replace("ldr_budget_city_", ""))
        context.user_data["leader_input"] = "budget_city"
        context.user_data["leader_budget_gid"] = gid
        with db_conn() as conn:
            g = conn.execute("SELECT * FROM groups WHERE group_id=?", (gid,)).fetchone()
        name = g["title"] if g else str(gid)
        await query.edit_message_text(
            f"🏙️ *{name}*\n\nمقدار بودجه رو (هاپ‌پوینت) بنویس:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("❌ انصراف", "ldr_budget")]]))
        return True

    if data == "ldr_projects":
        with db_conn() as conn:
            done = {r["project_key"]: r for r in conn.execute("SELECT * FROM national_projects").fetchall()}
        kb = []
        for key, cfg in NATIONAL_PROJECTS.items():
            prow = done.get(key)
            if prow:
                if prow["status"] == "building":
                    rem = max(0, int((datetime.fromisoformat(prow["done_at"]) - datetime.now()).total_seconds() // 3600))
                    kb.append([cbtn(f"🔨 {cfg['name']} ({rem}h)", f"ldr_proj_info_{key}")])
                else:
                    kb.append([cbtn(f"✅ {cfg['name']}", f"ldr_proj_info_{key}")])
            else:
                kb.append([cbtn(f"🏗️ {cfg['name']} ({cfg['cost']:,})", f"ldr_build_proj_{key}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("🏗️ *پروژه‌های ملی*", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_build_proj_"):
        key = data.replace("ldr_build_proj_", "")
        cfg = NATIONAL_PROJECTS.get(key)
        if not cfg: return True
        bal = get_treasury()
        if bal < cfg["cost"]:
            await query.answer(f"❌ خزانه کافی نیست! موجودی: {bal:,.0f}", show_alert=True)
            return True
        now = datetime.now()
        done_at = (now + timedelta(hours=cfg["build_hours"])).isoformat()
        change_treasury(-cfg["cost"], f"ساخت {cfg['name']}", user.id)
        with db_conn() as conn:
            conn.execute(
                "INSERT OR REPLACE INTO national_projects (project_key,status,started_at,done_at) VALUES (?,?,?,?)",
                (key, "building", now.isoformat(), done_at)
            )
            conn.commit()
        log_leader(user.id, "project", f"ساخت {cfg['name']} شروع شد")
        await query.edit_message_text(
            f"🏗️ *ساخت شروع شد!*\n\n{cfg['name']}\n💰 {cfg['cost']:,} از خزانه کسر شد\n⏳ {cfg['build_hours']} ساعت تا تکمیل",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_projects")]]))
        return True

    if data.startswith("ldr_proj_info_"):
        key = data.replace("ldr_proj_info_", "")
        cfg = NATIONAL_PROJECTS.get(key, {})
        with db_conn() as conn:
            prow = conn.execute("SELECT * FROM national_projects WHERE project_key=?", (key,)).fetchone()
        if not prow:
            await query.answer("یافت نشد", show_alert=True); return True
        if prow["status"] == "done":
            txt = f"✅ *{cfg.get('name','—')}*\n\n📢 {cfg.get('desc','')}\n\nاین پروژه فعاله و برای کل کشور اثر داره."
        else:
            rem = max(0, int((datetime.fromisoformat(prow["done_at"]) - datetime.now()).total_seconds()))
            h, r = divmod(rem, 3600); m = r // 60
            txt = f"🔨 *{cfg.get('name','—')} در حال ساخت*\n\n⏳ {h}:{m:02d} مونده"
        await query.edit_message_text(txt, parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_projects")]]))
        return True

    if data == "ldr_events":
        active_evs = {ev["event_key"] for ev in get_active_events()}
        kb = []
        for key, cfg in NATIONAL_EVENTS.items():
            if key in active_evs:
                kb.append([cbtn(f"✅ {cfg['name']} (فعال)", f"ldr_event_info_{key}")])
            else:
                kb.append([cbtn(f"🎉 {cfg['name']} ({cfg['cost']:,})", f"ldr_launch_event_{key}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("🎉 *رویدادهای ملی*", parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_launch_event_"):
        key = data.replace("ldr_launch_event_", "")
        cfg = NATIONAL_EVENTS.get(key)
        if not cfg: return True
        bal = get_treasury()
        if bal < cfg["cost"]:
            await query.answer(f"❌ خزانه کافی نیست! موجودی: {bal:,.0f}", show_alert=True)
            return True
        now = datetime.now()
        expires = (now + timedelta(hours=cfg["hours"])).isoformat()
        change_treasury(-cfg["cost"], f"رویداد {cfg['name']}", user.id)
        with db_conn() as conn:
            conn.execute(
                "INSERT INTO national_events (event_key,started_at,expires_at,is_active) VALUES (?,?,?,1)",
                (key, now.isoformat(), expires)
            )
            conn.commit()
        log_leader(user.id, "event", f"رویداد ملی: {cfg['name']}")
        await query.edit_message_text(
            f"🎉 *رویداد شروع شد!*\n\n{cfg['name']}\n⏳ {cfg['hours']} ساعت فعاله.\n💰 {cfg['cost']:,} از خزانه کسر شد.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_events")]]))
        return True

    if data.startswith("ldr_event_info_"):
        key = data.replace("ldr_event_info_", "")
        cfg = NATIONAL_EVENTS.get(key, {})
        for ev in get_active_events():
            if ev["event_key"] == key:
                exp = datetime.fromisoformat(ev["expires_at"])
                rem = max(0, int((exp - datetime.now()).total_seconds()))
                h, r = divmod(rem, 3600); m = r // 60
                await query.edit_message_text(
                    f"🎉 *{cfg.get('name','—')}*\n\n⏳ {h}:{m:02d} مونده",
                    parse_mode="Markdown",
                    reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_events")]]))
                return True
        return True

    if data == "ldr_ministers":
        with db_conn() as conn:
            mrows = {r["role"]: r for r in conn.execute("SELECT * FROM ministers").fetchall()}
        txt = "👥 *شورای وزیران*\n\n"
        kb = []
        for role, cfg in MINISTER_ROLES.items():
            m = mrows.get(role)
            if m:
                txt += f"• {cfg['name']}: {m['first_name']}\n"
                kb.append([cbtn(f"✏️ تغییر {cfg['name']}", f"ldr_set_minister_{role}")])
            else:
                txt += f"• {cfg['name']}: *خالی*\n"
                kb.append([cbtn(f"➕ انتصاب {cfg['name']}", f"ldr_set_minister_{role}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text(txt, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_set_minister_"):
        role = data.replace("ldr_set_minister_", "")
        cfg = MINISTER_ROLES.get(role)
        if not cfg: return True
        context.user_data["leader_input"] = "set_minister"
        context.user_data["leader_minister_role"] = role
        await query.edit_message_text(
            f"👤 *انتصاب {cfg['name']}*\n\nیوزرنیم یا آیدی عددی بازیکن رو بنویس:",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("❌ انصراف", "ldr_ministers")]]))
        return True

    if data == "ldr_emergency":
        em = get_emergency()
        if em:
            cfg = EMERGENCY_TYPES.get(em["etype"], {})
            exp = datetime.fromisoformat(em["expires_at"])
            rem = max(0, int((exp - datetime.now()).total_seconds()))
            h, r = divmod(rem, 3600); m = r // 60
            await query.edit_message_text(
                f"🚨 *وضعیت اضطراری فعال:*\n\n{cfg.get('name','—')}\n⏳ {h}:{m:02d} مونده",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))

            return True
        kb = [[cbtn(cfg["name"], f"ldr_declare_emergency_{etype}")] for etype, cfg in EMERGENCY_TYPES.items()]
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("🚨 *اعلام وضعیت اضطراری*\n\nنوع رو انتخاب کن:",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_declare_emergency_"):
        etype = data.replace("ldr_declare_emergency_", "")
        cfg = EMERGENCY_TYPES.get(etype)
        if not cfg: return True
        now = datetime.now()
        expires = (now + timedelta(hours=cfg["hours"])).isoformat()
        with db_conn() as conn:
            conn.execute(
                "UPDATE emergency_state SET etype=?,started_at=?,expires_at=?,is_active=1 WHERE id=1",
                (etype, now.isoformat(), expires)
            )
            conn.commit()
        log_leader(user.id, "emergency", f"وضعیت اضطراری: {cfg['name']}")
        await query.edit_message_text(
            f"🚨 *وضعیت اضطراری اعلام شد!*\n\n{cfg['name']}\n⏳ {cfg['hours']} ساعت فعاله.",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))
        return True

    if data == "ldr_mayors_menu":
        with db_conn() as conn:
            mayors = conn.execute("SELECT * FROM mayor").fetchall()
            groups = {g["group_id"]: g for g in conn.execute("SELECT * FROM groups").fetchall()}
        if not mayors:
            await query.edit_message_text("هیچ شهرداری ثبت نشده.",
                reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))
            return True
        kb = []
        for m in mayors:
            gname = groups.get(m["group_id"], {})
            title = gname["title"] if gname else str(m["group_id"])
            kb.append([cbtn(f"🏙️ {title} — {m['first_name']}", f"ldr_mayor_action_{m['group_id']}")])
        kb.append([cbtn("🔙 بازگشت", "ldr_back")])
        await query.edit_message_text("🏛️ *شهرداران*\n\nشهر رو انتخاب کن:",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_mayor_action_"):
        gid = int(data.replace("ldr_mayor_action_", ""))
        with db_conn() as conn:
            m = conn.execute("SELECT * FROM mayor WHERE group_id=?", (gid,)).fetchone()
            g = conn.execute("SELECT * FROM groups WHERE group_id=?", (gid,)).fetchone()
        if not m:
            await query.answer("شهردار پیدا نشد", show_alert=True); return True
        gname = g["title"] if g else str(gid)
        pop = m["popularity"] if "popularity" in m.keys() else 100
        kb = [
            [cbtn("⚠️ اخطار به شهردار", f"ldr_warn_mayor_{gid}")],
            [cbtn("💸 کاهش بودجه", f"ldr_cut_budget_{gid}")],
            [cbtn("🗳️ انتخابات زودهنگام", f"ldr_early_election_{gid}")],
            [cbtn("🚫 برکناری شهردار", f"ldr_fire_mayor_{gid}")],
            [cbtn("🔙 بازگشت", "ldr_mayors_menu")],
        ]
        await query.edit_message_text(
            f"🏙️ *{gname}*\n\n👤 شهردار: {m['first_name']}\n📊 محبوبیت: {pop}٪\n",
            parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(kb))
        return True

    if data.startswith("ldr_warn_mayor_"):
        gid = int(data.replace("ldr_warn_mayor_", ""))
        with db_conn() as conn:
            m = conn.execute("SELECT * FROM mayor WHERE group_id=?", (gid,)).fetchone()
        if not m: return True
        log_leader(user.id, "warn_mayor", f"اخطار به شهردار {m['first_name']} در شهر {gid}")
        try:
            await context.bot.send_message(gid,
                f"⚠️ *اخطار رهبری!*\n\nشهردار {m['first_name']}، رهبر کشور به عملکرد شما اخطار داد.\nاگر بهبود نیابد، اقدامات جدی‌تر انجام خواهد شد.",
                parse_mode="Markdown")
        except Exception: pass
        await query.answer("✅ اخطار ارسال شد", show_alert=True)
        return True

    if data.startswith("ldr_fire_mayor_"):
        gid = int(data.replace("ldr_fire_mayor_", ""))
        with db_conn() as conn:
            m = conn.execute("SELECT * FROM mayor WHERE group_id=?", (gid,)).fetchone()
        if not m: return True
        name = m["first_name"]
        with db_conn() as conn:
            conn.execute("DELETE FROM mayor WHERE group_id=?", (gid,))
            conn.commit()
        log_leader(user.id, "fire_mayor", f"شهردار {name} در شهر {gid} برکنار شد")
        try:
            await context.bot.send_message(gid,
                f"📣 *اطلاعیه رهبری!*\n\nشهردار {name} توسط رهبر کشور برکنار شد.\nشهر بدون شهردار است.",
                parse_mode="Markdown")
        except Exception: pass
        await query.answer("✅ شهردار برکنار شد", show_alert=True)
        return True

    if data.startswith("ldr_early_election_"):
        gid = int(data.replace("ldr_early_election_", ""))
        log_leader(user.id, "early_election", f"انتخابات زودهنگام در شهر {gid}")
        try:
            await context.bot.send_message(gid,
                "🗳️ *رهبر کشور دستور برگزاری انتخابات زودهنگام داد!*\n\nبرای ثبت‌نام بنویس: شهردار",
                parse_mode="Markdown")
        except Exception: pass
        await query.answer("✅ اعلام شد", show_alert=True)
        return True

    if data.startswith("ldr_cut_budget_"):
        gid = int(data.replace("ldr_cut_budget_", ""))
        with db_conn() as conn:
            conn.execute("UPDATE groups SET treasury=treasury*0.8 WHERE group_id=?", (gid,))
            conn.commit()
        log_leader(user.id, "cut_budget", f"بودجه شهر {gid} ۲۰٪ کاهش یافت")
        await query.answer("✅ بودجه ۲۰٪ کاهش یافت", show_alert=True)
        return True

    if data == "ldr_report":
        ld = get_leader()
        if not ld: return True
        with db_conn() as conn:
            cmd_count = conn.execute("SELECT COUNT(*) as c FROM leader_commands").fetchone()["c"]
            law_count = conn.execute("SELECT COUNT(*) as c FROM leader_laws WHERE is_active=1").fetchone()["c"]
            proj_count = conn.execute("SELECT COUNT(*) as c FROM national_projects WHERE status='done'").fetchone()["c"]
            ev_count = conn.execute("SELECT COUNT(*) as c FROM national_events").fetchone()["c"]
            spent = conn.execute("SELECT SUM(ABS(amount)) as s FROM treasury_log WHERE amount<0").fetchone()["s"] or 0
        await query.edit_message_text(
            f"📊 *گزارش عملکرد رهبر*\n\n"
            f"👑 فرمان‌های صادرشده: {cmd_count}\n"
            f"⚖️ قوانین فعال: {law_count}\n"
            f"🏗️ پروژه‌های تکمیل‌شده: {proj_count}\n"
            f"🎉 رویدادهای برگزارشده: {ev_count}\n"
            f"💸 کل هزینه‌ها: {spent:,.0f}\n"
            f"📊 محبوبیت: {ld['popularity']}٪",
            parse_mode="Markdown",
            reply_markup=InlineKeyboardMarkup([[cbtn("🔙 بازگشت", "ldr_back")]]))
        return True

    if data in ("leader_rate_up", "leader_rate_down"):
        ld = get_leader()
        if not ld:
            await query.answer("رهبری وجود ندارد", show_alert=True); return True
        with db_conn() as conn:
            existing = conn.execute("SELECT * FROM leader_ratings WHERE user_id=?", (user.id,)).fetchone()
            if existing:
                await query.answer("قبلاً رأی دادی!", show_alert=True); return True
            rating = "up" if data == "leader_rate_up" else "down"
            conn.execute(
                "INSERT INTO leader_ratings (user_id,rating,rated_at) VALUES (?,?,?)",
                (user.id, rating, datetime.now().isoformat())
            )
            conn.commit()
        delta = 2 if data == "leader_rate_up" else -2
        with db_conn() as conn:
            conn.execute("UPDATE leader SET popularity=MAX(0,MIN(100,popularity+?)) WHERE user_id=?",
                        (delta, ld["user_id"]))
            conn.commit()
        await query.answer("👍 رضایت ثبت شد!" if data == "leader_rate_up" else "👎 نارضایتی ثبت شد!", show_alert=True)
        return True

    return False

async def handle_leader_text_input(update: Update, context: ContextTypes.DEFAULT_TYPE) -> bool:
    if not update.message or not update.message.text:
        return False
    user = update.effective_user
    text = update.message.text.strip()
    inp = context.user_data.get("leader_input")
    if not inp:
        return False

    if inp == "admin_treasury_add" and is_admin(user.id):
        context.user_data.pop("leader_input", None)
        try:
            amount = float(text.replace(",", ""))
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        change_treasury(amount, "واریز ادمین", user.id)
        await update.message.reply_text(f"✅ {amount:,.0f} به خزانه ملی اضافه شد!\nموجودی جدید: {get_treasury():,.0f}")
        return True

    if inp == "treasury_spend" and is_leader(user.id):
        context.user_data.pop("leader_input", None)
        parts = text.split("|", 1)
        if len(parts) != 2:
            await update.message.reply_text("❌ فرمت: مقدار|دلیل")
            return True
        try:
            amount = float(parts[0].strip().replace(",", ""))
        except ValueError:
            await update.message.reply_text("❌ مقدار معتبر نیست")
            return True
        reason = parts[1].strip()
        bal = get_treasury()
        if amount > bal:
            await update.message.reply_text(f"❌ موجودی کافی نیست! موجودی: {bal:,.0f}")
            return True
        change_treasury(-amount, reason, user.id)
        log_leader(user.id, "spend", f"{amount:,.0f} برای {reason}")
        await update.message.reply_text(f"✅ {amount:,.0f} از خزانه برداشت شد.\n📝 دلیل: {reason}")
        return True

    if inp == "budget_city" and is_leader(user.id):
        gid = context.user_data.pop("leader_budget_gid", None)
        context.user_data.pop("leader_input", None)
        if not gid: return True
        try:
            amount = float(text.replace(",", ""))
        except ValueError:
            await update.message.reply_text("❌ عدد معتبر وارد کن!")
            return True
        bal = get_treasury()
        if amount > bal:
            await update.message.reply_text(f"❌ موجودی خزانه کافی نیست! موجودی: {bal:,.0f}")
            return True
        change_treasury(-amount, f"بودجه شهر {gid}", user.id)
        with db_conn() as conn:
            conn.execute("UPDATE groups SET treasury=treasury+? WHERE group_id=?", (amount, gid))
            g = conn.execute("SELECT title FROM groups WHERE group_id=?", (gid,)).fetchone()
            conn.commit()
        gname = g["title"] if g else str(gid)
        log_leader(user.id, "budget", f"بودجه {amount:,.0f} به شهر {gname} واریز شد")
        await update.message.reply_text(f"✅ {amount:,.0f} به خزانه شهر {gname} واریز شد!")
        try:
            await context.bot.send_message(gid,
                f"💰 *بودجه دریافت شد!*\n\n+{amount:,.0f} هاپ‌پوینت از طرف رهبر کشور به خزانه شهر واریز شد.",
                parse_mode="Markdown")
        except Exception: pass
        return True

    if inp == "set_minister" and is_leader(user.id):
        role = context.user_data.pop("leader_minister_role", None)
        context.user_data.pop("leader_input", None)
        if not role: return True
        cfg = MINISTER_ROLES.get(role, {})
        with db_conn() as conn:
            if text.isdigit():
                target = conn.execute("SELECT * FROM users WHERE user_id=?", (int(text),)).fetchone()
            else:
                target = conn.execute("SELECT * FROM users WHERE username=?", (text.lstrip("@"),)).fetchone()
        if not target:
            await update.message.reply_text("❌ کاربر پیدا نشد!")
            return True
        with db_conn() as conn:
            conn.execute(
                "INSERT OR REPLACE INTO ministers (role,user_id,username,first_name,appointed_at) VALUES (?,?,?,?,?)",
                (role, target["user_id"], target["username"], target["first_name"], datetime.now().isoformat())
            )
            conn.commit()
        log_leader(user.id, "minister", f"{target['first_name']} به عنوان {cfg.get('name',role)} منصوب شد")
        await update.message.reply_text(
            f"✅ {target['first_name']} به عنوان {cfg.get('name', role)} منصوب شد!\n📢 {cfg.get('desc','')}")
        return True

    return False

async def leaderjoin_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    ensure_user(user.id, user.username or "", user.first_name)
    elec = _get_active_election()
    if not elec or elec["status"] != "candidacy":
        await update.message.reply_text("❌ الان انتخابات رهبری‌ای در جریان نیست.")
        return
    eid = elec["id"]
    with db_conn() as conn:
        exists = conn.execute(
            "SELECT 1 FROM leader_candidates WHERE election_id=? AND user_id=?", (eid, user.id)
        ).fetchone()
        if exists:
            await update.message.reply_text("✅ قبلاً ثبت‌نام کردی!")
            return
        conn.execute(
            "INSERT INTO leader_candidates (election_id,user_id,username,first_name) VALUES (?,?,?,?)",
            (eid, user.id, user.username or "", user.first_name)
        )
        conn.commit()
    await update.message.reply_text(
        f"✅ {user.first_name}، نامزدیت در انتخابات رهبری ثبت شد!\n\n"
        f"📢 میتونی وعده‌هات رو بنویسی تا بازیکنان بدونن اگه رهبر بشی چیکار میکنی."
    )

async def leadervote_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    ensure_user(user.id, user.username or "", user.first_name)
    elec = _get_active_election()
    if not elec or elec["status"] != "voting":
        await update.message.reply_text("❌ الان رأی‌گیری در جریان نیست.")
        return
    eid = elec["id"]
    with db_conn() as conn:
        voted = conn.execute(
            "SELECT 1 FROM leader_votes WHERE election_id=? AND voter_id=?", (eid, user.id)
        ).fetchone()
    if voted:
        await update.message.reply_text("✅ قبلاً رأی دادی!")
        return
    candidates = _get_candidates(eid)
    if not candidates:
        await update.message.reply_text("❌ نامزدی نیست!")
        return
    kb = [[cbtn(f"👤 {c['first_name']}", f"ldr_vote_{eid}_{c['user_id']}")] for c in candidates]
    await update.message.reply_text(
        "🗳️ *رأی‌گیری رهبری*\n\nنامزد مورد نظرت رو انتخاب کن:",
        parse_mode="Markdown",
        reply_markup=InlineKeyboardMarkup(kb)
    )

async def leader_auto_jobs(context):
    now = datetime.now()
    with db_conn() as conn:
        done = conn.execute(
            "SELECT * FROM national_projects WHERE status='building' AND done_at<=?",
            (now.isoformat(),)
        ).fetchall()
        for p in done:
            conn.execute("UPDATE national_projects SET status='done' WHERE id=?", (p["id"],))
            conn.commit()
            cfg = NATIONAL_PROJECTS.get(p["project_key"], {})
            groups = conn.execute("SELECT group_id FROM groups").fetchall()
            for g in groups:
                try:
                    await context.bot.send_message(
                        g["group_id"],
                        f"🎉 *پروژه ملی تکمیل شد!*\n\n{cfg.get('name','—')}\n✅ {cfg.get('desc','')} فعال شد!",
                        parse_mode="Markdown"
                    )
                except Exception:
                    pass
        conn.execute(
            "UPDATE leader_commands SET is_active=0 WHERE is_active=1 AND expires_at<=?",
            (now.isoformat(),)
        )
        conn.execute(
            "UPDATE national_events SET is_active=0 WHERE is_active=1 AND expires_at<=?",
            (now.isoformat(),)
        )
        conn.commit()


def main():
    init_db()
    init_casino_tables()

    app = Application.builder().token(BOT_TOKEN).build()

    async def complete_projects(context):
        from datetime import datetime
        with db_conn() as conn:
            rows = conn.execute(
                "SELECT * FROM mayor_projects WHERE status='building' AND done_at <= datetime('now')"
            ).fetchall()
            for p in rows:
                conn.execute("UPDATE mayor_projects SET status='done' WHERE id=?", (p["id"],))
                conn.commit()
                proj = CITY_PROJECTS.get(p["project_key"])
                if proj:
                    try:
                        await context.bot.send_message(
                            p["group_id"],
                            f"🎉 *پروژه تکمیل شد!*\n\n{proj['name']} سطح {p['level']}\n✅ {proj['desc']} فعال شد!",
                            parse_mode="Markdown"
                        )
                    except Exception:
                        pass

    app.job_queue.run_repeating(complete_projects, interval=300, first=10)
    app.job_queue.run_repeating(leader_auto_jobs, interval=300, first=15)

    async def auto_expire_crises(context):
        expired = expire_old_crises()
        for row in expired:
            c = CRISIS_TYPES.get(row["crisis_type"], {})
            try:
                await context.bot.send_message(
                    row["group_id"],
                    f"⚠️ *بحران بدون مدیریت ماند!*\n\n"
                    f"{c.get('name', row['crisis_type'])}\n\n"
                    f"شهردار تصمیم نگرفت و شهر جریمه شد:\n"
                    f"❌ {c.get('penalty', '—')}",
                    parse_mode="Markdown"
                )
            except Exception:
                pass

    app.job_queue.run_repeating(auto_expire_crises, interval=60, first=30)

    async def daily_economy_jobs(context):
        with db_conn() as conn:
            rich_users = conn.execute(
                "SELECT user_id, hop_points FROM users WHERE hop_points > 600_000_000"
            ).fetchall()
            for u in rich_users:
                tax = int(u["hop_points"] * 0.01)
                conn.execute(
                    "UPDATE users SET hop_points = hop_points - ? WHERE user_id = ?",
                    (tax, u["user_id"])
                )
            conn.commit()

    from datetime import time as dtime
    app.job_queue.run_daily(daily_economy_jobs, time=dtime(3, 0, 0))

    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("profile", profile_cmd))
    app.add_handler(CommandHandler("top", top_cmd))
    app.add_handler(CommandHandler("help", help_cmd))
    app.add_handler(CommandHandler("lottery", lottery_panel_cmd))
    app.add_handler(CommandHandler("panel", lottery_panel_cmd))
    app.add_handler(CommandHandler("invite", referral_invite_cmd))
    app.add_handler(CommandHandler("ref", referral_invite_cmd))
    app.add_handler(CommandHandler("leaderjoin", leaderjoin_cmd))
    app.add_handler(CommandHandler("leadervote", leadervote_cmd))

    app.add_handler(MessageHandler(
        filters.Regex(r"^[هH][اa][پp]$") & filters.ChatType.GROUPS,
        handle_hop
    ))


    app.add_handler(MessageHandler(
        filters.ChatType.GROUPS & filters.TEXT & ~filters.COMMAND,
        handle_group_text_full
    ))
    app.add_handler(MessageHandler(
        filters.ChatType.PRIVATE & ~filters.COMMAND,
        private_message
    ))
    app.add_handler(CallbackQueryHandler(callback_handler_full))

    logger.info("🐕 ربات هاپی شروع به کار کرد!")
    app.run_polling()

if __name__ == "__main__":
    main()
