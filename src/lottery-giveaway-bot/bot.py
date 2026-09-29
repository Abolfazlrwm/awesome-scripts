import asyncio
import json
import os
import random
import re
import string
import sys
import time
import atexit
import html
from typing import List, Dict, Tuple, Optional, Union

from aiogram.exceptions import (
    TelegramBadRequest, TelegramConflictError, TelegramAPIError,
    TelegramForbiddenError, TelegramRetryAfter, TelegramNetworkError,
    TelegramUnauthorizedError,
)

from aiogram import Bot, Dispatcher, Router, F
from aiogram.client.default import DefaultBotProperties
from aiogram.enums import ParseMode
from aiogram.filters import CommandStart
from aiogram.fsm.storage.memory import MemoryStorage
from aiogram.types import CallbackQuery, InlineKeyboardButton, InlineKeyboardMarkup, Message

from dotenv import load_dotenv
load_dotenv()

TOKEN = os.getenv("BOT_TOKEN")
ADMIN_ID = int(os.getenv("ADMIN_ID", "0"))
if not TOKEN or not ADMIN_ID:
    sys.exit("خطا: لطفاً BOT_TOKEN و ADMIN_ID را در فایل .env تنظیم کنید.")
ADMIN_IDS = {ADMIN_ID}
OWNER_USERNAME = os.getenv("OWNER_USERNAME", "YourUsername")
BOT_DISPLAY_NAME = os.getenv("BOT_DISPLAY_NAME", "ربات قرعه‌کشی")
DATA_FILE = "bot_data.json"
BACKUP_FILE = "bot_data_backup.json"
LOCK_FILE = "botdd.lock"
SEP = "\u2501" * 15

MATH_FAIL_LIMIT = 3
BLOCK_MINUTES = 10
MATH_TIMEOUT_SECONDS = 90

bot = Bot(token=TOKEN, default=DefaultBotProperties(parse_mode=ParseMode.HTML))
dp = Dispatcher(storage=MemoryStorage())
router = Router()
router.message.filter(F.chat.type == "private")
dp.include_router(router)

BOT_USERNAME = None


def acquire_single_instance_lock():
    if os.path.exists(LOCK_FILE):
        try:
            with open(LOCK_FILE, "r") as f:
                old_pid = int(f.read().strip() or 0)
            if old_pid and old_pid != os.getpid():
                os.kill(old_pid, 0)
                sys.exit("نمونه دیگری از ربات در حال اجراست (PID {}).".format(old_pid))
        except ValueError:
            pass
        except ProcessLookupError:
            pass
        except PermissionError:
            sys.exit("نمونه دیگری از ربات در حال اجراست.")
        except OSError:
            pass
    try:
        with open(LOCK_FILE, "w") as f:
            f.write(str(os.getpid()))
    except OSError as e:
        print(f"هشدار: ایجاد فایل قفل ممکن نشد ({e})؛ بررسی نمونه تکراری غیرفعال است.")
        return
    atexit.register(lambda: os.path.exists(LOCK_FILE) and os.remove(LOCK_FILE))


PREMIUM_EMOJIS = [
    ('5798587088077066898', '👋'),
    ('5859232223865081255', '📱'),
    ('5767325541547904537', '☎️'),
    ('5767288471685171967', '🛍'),
    ('5951891646445523477', '📰'),
    ('5771556513831194820', '👻'),
    ('5767156873887223565', '🤑'),
    ('5787467546596743616', '🤭'),
    ('5767099626268135605', '😉'),
    ('5764829255015861596', '🗣'),
    ('5929192117220937925', '⭐️'),
    ('5967780993921193106', '🎂'),
    ('5787166284705699232', '🎁'),
    ('6044085967017482138', '🎁'),
    ('5852749687991309385', '🎂'),
    ('5879939498149679716', '🔎'),
    ('5895288332581082241', '🆒'),
    ('5942948218865194866', '😎'),
    ('5836901503482205767', '🔍'),
    ('5859480923946356143', '📱'),
]

EMOJI_TO_CUSTOM_ID = {'\u2728': PREMIUM_EMOJIS[0][0]}

VALID_EMOJI_IDS = set()

PRIZE_OPTIONS = [
    ("prize_stars", "استارز تلگرام", "5929192117220937925"),
    ("prize_premium", "پرمیوم تلگرام", "5767288471685171967"),
    ("prize_gift", "گیفت تلگرام", "5787166284705699232"),
]
PRIZE_CALLBACKS = {cb for cb, _, _ in PRIZE_OPTIONS}
PRIZE_LABELS = {cb: label for cb, label, _ in PRIZE_OPTIONS}
PRIZE_EMOJI_IDS = {cb: emoji_id for cb, _, emoji_id in PRIZE_OPTIONS}
PRIZE_MANUAL_CALLBACK = "prize_manual"

_EMOJI_RE = re.compile(
    "(?:"
    "[\U0001F1E6-\U0001F1FF]{2}"
    "|[\U0001F000-\U0001FFFF](?:\uFE0F|\u200D[\U0001F000-\U0001FFFF])*"
    "|[\u2600-\u27BF\u2B00-\u2BFF\u2190-\u21FF\u2300-\u23FF\u25A0-\u25FF](?:\uFE0F)?"
    ")"
)
_TG_EMOJI_RE = re.compile(r"<tg-emoji\s+emoji-id=[\"'][^>]+?[\"']>.*?</tg-emoji>", re.DOTALL)

BUTTON_CALLBACK_KEYS = [
    'new_lottery', 'active_lotteries', 'manage_lottery', 'profile', 'about_bot', 'support', 'language', 'guide', 'top_winners',
    'admin_users_stats', 'admin_lottery_stats', 'broadcast', 'back_to_menu', 'cancel_lottery',
    'dur_5min', 'dur_10min', 'dur_30min', 'dur_1hour', 'dur_2hour', 'dur_1day', 'dur_manual',
    'step_back', 'cap_10', 'cap_50', 'cap_100', 'cap_500', 'cap_1000', 'cap_unlimited', 'cap_manual',
    'win_1', 'win_2', 'win_3', 'win_5', 'win_10', 'win_manual',
    'min_0', 'min_5', 'min_10', 'min_20', 'min_50', 'min_manual',
    'acc_bot', 'acc_premium', 'acc_all',
    'ch_0', 'ch_1', 'ch_2', 'ch_3', 'ch_4', 'ch_5', 'ch_6', 'ch_manual',
    'ref_remove', 'ref_add', 'ref_no_limit',
    'admin_panel', 'set_lang_fa', 'set_lang_en', 'set_lang_ru',
    'check_forced_channels', 'edit_text', 'send_default',
    'dur_type_days', 'dur_type_hours', 'dur_type_minutes', 'forced_channels_manage',
]
BUTTON_EMOJI_BY_CALLBACK = {
    key: PREMIUM_EMOJIS[i % len(PREMIUM_EMOJIS)][0]
    for i, key in enumerate(BUTTON_CALLBACK_KEYS)
}
BUTTON_EMOJI_BY_CALLBACK.update(PRIZE_EMOJI_IDS)
# Requested fixed premium icons for the main menu buttons.
BUTTON_EMOJI_BY_CALLBACK.update({
    'profile': '5879939498149679716',
    'about_bot': '5836901503482205767',
    'support': '5767325541547904537',
    'new_lottery': '5767288471685171967',
    'active_lotteries': '5951891646445523477',
    'language': '5967304840961856259',  # #️⃣
})


def _next_button_emoji_id(callback_data=None, text=None):
    key = str(callback_data or "")
    candidate = BUTTON_EMOJI_BY_CALLBACK.get(key)
    if candidate is None:
        import hashlib
        idx = int(hashlib.sha256(key.encode("utf-8")).hexdigest(), 16) % len(PREMIUM_EMOJIS)
        candidate = PREMIUM_EMOJIS[idx][0]
    if VALID_EMOJI_IDS and candidate not in VALID_EMOJI_IDS:
        candidate = next(iter(VALID_EMOJI_IDS), None)
    elif not VALID_EMOJI_IDS:
        candidate = None
    return candidate


def premiumize_emojis(text):
    if not isinstance(text, str):
        return text
    valid_pool = [e for e in PREMIUM_EMOJIS if not VALID_EMOJI_IDS or e[0] in VALID_EMOJI_IDS]
    if not valid_pool:
        return _EMOJI_RE.sub("", text)

    counter = {"i": 0}

    def next_tag():
        emoji_id, fallback = valid_pool[counter["i"] % len(valid_pool)]
        counter["i"] += 1
        return f'<tg-emoji emoji-id="{emoji_id}">{fallback}</tg-emoji>'

    protected = []

    def protect(match):
        protected.append(next_tag())
        return f"__TG_CUSTOM_EMOJI_{len(protected) - 1}__"

    text = _TG_EMOJI_RE.sub(protect, text)
    text = _EMOJI_RE.sub(lambda _m: next_tag(), text)
    for index, tag in enumerate(protected):
        text = text.replace(f"__TG_CUSTOM_EMOJI_{index}__", tag)
    return text


def make_all_bold(text):
    if not isinstance(text, str) or not text:
        return text
    if text.startswith('<b>') and text.endswith('</b>'):
        return text
    return f'<b>{text}</b>'


async def refresh_premium_emoji_catalog():
    global PREMIUM_EMOJIS, EMOJI_TO_CUSTOM_ID, VALID_EMOJI_IDS
    all_ids = [e for e, _ in PREMIUM_EMOJIS] + list(PRIZE_EMOJI_IDS.values())
    try:
        stickers = await bot.get_custom_emoji_stickers(custom_emoji_ids=list(dict.fromkeys(all_ids)))
        valid = []
        for sticker in stickers or []:
            emoji_id = str(getattr(sticker, "custom_emoji_id", "") or "")
            alt = getattr(sticker, "emoji", None)
            if emoji_id and alt:
                valid.append((emoji_id, alt))
        if valid:
            VALID_EMOJI_IDS = {e for e, _ in valid}
            button_valid = list(valid)
            if button_valid:
                PREMIUM_EMOJIS = button_valid
            EMOJI_TO_CUSTOM_ID = {alt: emoji_id for emoji_id, alt in valid}
        else:
            VALID_EMOJI_IDS = set()
            print("هشدار: هیچ‌کدام از Custom Emoji IDها معتبر تایید نشدند؛ آیکون‌ها نمایش داده نمی‌شوند.")
    except Exception as e:
        VALID_EMOJI_IDS = set()
        print(f"اعتبارسنجی Custom Emoji ناموفق بود: {e}")


async def tg_send(chat_id, text, reply_markup=None, disable_web_page_preview=None, parse_mode=None):
    if text is None:
        text = ""
    if not isinstance(text, str):
        text = str(text)
    if parse_mode is None:
        parse_mode = ParseMode.HTML
    text = premiumize_emojis(text)
    # متن‌هایی که خودشان HTML دارند، مثل Welcome، نباید دوباره کامل بولد شوند.
    if parse_mode == ParseMode.HTML and text and not (
        text.startswith("<b>") or text.startswith("<blockquote>") or "<blockquote>" in text
    ):
        text = f"<b>{text}</b>"
    last_error = None
    for attempt in range(3):
        try:
            return await bot.send_message(
                chat_id, text, reply_markup=reply_markup,
                disable_web_page_preview=disable_web_page_preview, parse_mode=parse_mode,
            )
        except (TelegramNetworkError, asyncio.TimeoutError, TimeoutError) as e:
            last_error = e
            if attempt < 2:
                await asyncio.sleep(0.6 * (attempt + 1))
    raise last_error


async def tg_send_photo(chat_id, photo, caption=None, reply_markup=None, parse_mode=None):
    if caption is None:
        caption = ""
    if not isinstance(caption, str):
        caption = str(caption)
    if caption:
        caption = premiumize_emojis(caption)
    if parse_mode is None:
        parse_mode = ParseMode.HTML
    if caption and parse_mode == ParseMode.HTML:
        caption = f"<b>{caption}</b>"
    return await bot.send_photo(
        chat_id, photo=photo, caption=caption, reply_markup=reply_markup, parse_mode=parse_mode,
    )


async def tg_edit_text(chat_id, message_id, text, reply_markup=None, parse_mode=None):
    if text is None:
        text = ""
    if not isinstance(text, str):
        text = str(text)
    if parse_mode is None:
        parse_mode = ParseMode.HTML
    text = premiumize_emojis(text)
    try:
        if parse_mode == ParseMode.HTML and text and not (text.startswith("<b>") and text.endswith("</b>")):
            text = f"<b>{text}</b>"
        return await bot.edit_message_text(
            text, chat_id=chat_id, message_id=message_id, reply_markup=reply_markup, parse_mode=parse_mode,
        )
    except TelegramBadRequest:
        return None


async def tg_edit_markup(chat_id, message_id, reply_markup):
    try:
        return await bot.edit_message_reply_markup(chat_id=chat_id, message_id=message_id, reply_markup=reply_markup)
    except TelegramBadRequest:
        return None


async def tg_delete(chat_id, message_id):
    try:
        await bot.delete_message(chat_id, message_id)
    except (TelegramBadRequest, TelegramNetworkError, asyncio.TimeoutError, TimeoutError):
        # حذف پیام فقط برای تمیزی منو است؛ قطع شدن شبکه نباید Callback را خراب کند.
        pass
    except Exception as e:
        print(f"delete message warning: {e}")


async def tg_answer(callback, text=None):
    try:
        if text:
            text = _EMOJI_RE.sub("", _TG_EMOJI_RE.sub("", text))[:200]
        await callback.answer(text)
    except TelegramBadRequest:
        pass


async def notify_admin(text):
    try:
        await tg_send(ADMIN_ID, text)
    except Exception as e:
        print(f"اطلاع‌رسانی به ادمین ناموفق بود: {e}")


START_CUSTOM_EMOJI = '<tg-emoji emoji-id="5771556513831194820">👻</tg-emoji>'

async def send_start_emoji(user_id):
    """Send the start custom emoji as a standalone Telegram message."""
    try:
        await bot.send_message(
            user_id,
            START_CUSTOM_EMOJI,
            parse_mode=ParseMode.HTML,
        )
    except Exception as e:
        print(f"خطا در ارسال ایموجی شروع: {e}")


class KB:
    def __init__(self, row_width=1):
        self.row_width = row_width
        self.rows = []

    def add(self, *buttons):
        buttons = [b for b in buttons if b is not None]
        for i in range(0, len(buttons), self.row_width):
            self.rows.append(list(buttons[i:i + self.row_width]))
        return self

    def markup(self):
        return InlineKeyboardMarkup(inline_keyboard=self.rows) if self.rows else None


def colored_button(text, callback_data, style=None, url=None):
    if isinstance(text, str):
        text = _TG_EMOJI_RE.sub("", text)
        text = _EMOJI_RE.sub("", text).strip()

    kwargs = {"text": text or "-"}
    if url:
        kwargs["url"] = url
    else:
        safe_cb = str(callback_data) if callback_data is not None else "noop"
        kwargs["callback_data"] = safe_cb[:64]

    emoji_id = _next_button_emoji_id(callback_data, text)
    if emoji_id:
        kwargs["icon_custom_emoji_id"] = str(emoji_id)
    if style:
        if style not in ("primary", "success", "danger"):
            raise ValueError(f"Invalid button style {style!r}")
        kwargs["style"] = style

    return InlineKeyboardButton(**kwargs)


user_data = {}
lotteries = {}
user_math = {}
user_languages = {}
forced_channels = []
giveaway_channels = []
all_users = set()
user_join_dates = {}
nav_message = {}
math_fail = {}
blocked_until = {}

TEXTS = {
    "fa": {
        "welcome": "<b>🤍خوش اومدی به  𝓢𝓱𝓪𝓱 𝓦𝓲𝓷𝔃</b>\n\n<blockquote><b>🎁 دنیای قرعه کشی های جذاب و واقعی اینجاست! 🏆\nشرکت کن، دوستات رو دعوت کن و شانس بردت رو بیشتر کن.\n\n✨ با 𝓢𝓱𝓪𝓱 𝓦𝓲𝓷𝔃 ساده بساز • هوشمندانه شرکت کن • برنده شو\n\n🤍 برای شروع از دکمه های زیر استفاده کن</b></blockquote>",
        "must_join": "✨ برای استفاده از ربات باید عضو کانال زیر باشید:\n\n{channels}\n\nپس از عضویت، روی دکمه «عضو شدم» کلیک کنید.",
        "check_again": "✨ عضو شدم",
        "new_lottery": "✨ ساخت قرعه کشی",
        "active_lotteries": "✨ قرعه‌کشی‌های فعال",
        "manage_lottery": "مدیریت قرعه‌کشی",
        "profile": "حساب کاربری",
        "about_bot": "درباره ربات",
        "support": "پشتیبانی ربات",
        "language": "Language",
        "guide": "🚀 راهنما",
        "top_winners": "🏆 برترین وینر ها",
        "guide_text": """🚀 <b>راهنمای ساخت قرعه‌کشی</b>

برای ساخت یک قرعه‌کشی وارد بخش ساخت قرعه‌کشی شو و مراحل زیر رو به ترتیب انجام بده:

<b><tg-emoji emoji-id="5798587088077066898">👋</tg-emoji> نام قرعه‌کشی</b>
یک اسم برای قرعه‌کشی انتخاب کن؛ مثل گیفت، استارز یا NFT.

<b><tg-emoji emoji-id="5859232223865081255">📱</tg-emoji> مدت زمان</b>
مدت فعال بودن قرعه‌کشی رو مشخص کن.

<b><tg-emoji emoji-id="5767325541547904537">☎️</tg-emoji> ظرفیت</b>
حداکثر تعداد افرادی که می‌تونن شرکت کنن رو تعیین کن.

<b><tg-emoji emoji-id="5767288471685171967">🛍</tg-emoji> تعداد برنده‌ها</b>
مشخص کن چند نفر برنده قرعه‌کشی بشن.

<b><tg-emoji emoji-id="5951891646445523477">📰</tg-emoji> حداقل شرکت‌کننده</b>
حداقل تعداد شرکت‌کننده موردنیاز رو تعیین کن؛ در صورت نیاز می‌تونی بدون محدودیت قرارش بدی.

<b><tg-emoji emoji-id="5771556513831194820">👻</tg-emoji> محدودیت دسترسی</b>
مشخص کن چه کسانی امکان شرکت در قرعه‌کشی رو داشته باشن.

<b><tg-emoji emoji-id="5767156873887223565">🤑</tg-emoji> کانال اسپانسر</b>
کانالی که قرعه‌کشی در اون منتشر می‌شه رو مشخص کن.

<b><tg-emoji emoji-id="5787467546596743616">🤭</tg-emoji> رفرال‌گیری</b>
مشخص کن سیستم رفرال برای این قرعه‌کشی فعال باشه یا نه.

<b><tg-emoji emoji-id="5767099626268135605">😉</tg-emoji> جایزه</b>
جایزه قرعه‌کشی رو انتخاب کن یا در صورت وجود گزینه دستی، جایزه دلخواهت رو وارد کن.

✨ بعد از تکمیل مراحل، اطلاعات قرعه‌کشی بررسی و برای انتشار آماده می‌شه.""",
        "select_language": "✨ انتخاب زبان\n<blockquote>✨ زبان مورد نظر خود را انتخاب کنید:</blockquote>",
        "lang_changed": "✨ زبان به {lang} تغییر یافت!",
        "admin_panel": "✨ پنل ادمین",
        "admin_users": "✨ آمار کاربران",
        "admin_lottery": "✨ آمار گیوای‌ها",
        "back": "✨ بازگشت",
        "invalid_number": "✨ عدد وارد شده معتبر نیست!",
        "step1": "<b>✨ گام ۱/۱۰ — نام قرعه‌کشی</b>\n<blockquote>✨ یک اسم دلخواه برای قرعه‌کشیت انتخاب کن\nمثال: گیفت NFT، استارز تلگرام، ...</blockquote>",
        "step2": "<b>✨ گام ۲/۱۰ — مدت زمان</b>\n<blockquote>✨ قرعه‌کشی چقدر طول بکشد؟</blockquote>",
        "step3": "<b>✨ گام ۳/۱۰ — ظرفیت</b>\n<blockquote>✨ حداکثر چند نفر می‌توانند شرکت کنند؟</blockquote>",
        "step4": "<b>✨ گام ۴/۱۰ — تعداد برنده‌ها</b>\n<blockquote>✨ چند نفر برنده می‌شوند؟</blockquote>",
        "step5": "<b>✨ گام ۵/۱۰ — حداقل شرکت‌کننده</b>\n<blockquote>✨ اگر به این تعداد نرسد، قرعه‌کشی لغو می‌شود\n(۰ = بدون محدودیت)</blockquote>",
        "step6": "<b>✨ گام ۶/۱۰ — محدودیت دسترسی</b>\n<blockquote>✨ چه کسانی می‌توانند شرکت کنند؟</blockquote>",
        "step7": "<b>✨ گام ۷/۱۰ — کانال اسپانسر</b>\n<blockquote>✨ کانالی که می‌خواهید گیواوی در آن منتشر شود را وارد کنید.\nشما و ربات باید در آن کانال ادمین باشید.</blockquote>",
        "step8": "<b>✨ گام ۸/۱۰ — رفرال‌گیری</b>\n<blockquote>✨ می‌خواهی این قرعه‌کشی سیستم رفرال داشته باشد؟</blockquote>",
        "step9": "<b>✨ گام ۹/۱۰ — جایزه</b>\n<blockquote>✨ یکی از جایزه‌های زیر را انتخاب کن، یا با «✨ دستی» جایزه دلخواه خودت را وارد کن:</blockquote>",
        "step10": "<b>✨ گام ۱۰/۱۰ — تأیید امنیتی</b>\n<blockquote>✨ برای ورود به قرعه‌کشی، سوال ریاضی فعال باشد یا بدون سوال وارد شوند؟</blockquote>",
        "cancel": "✨ لغو",
        "back_step": "✨ مرحله قبل",
        "unlimited": "✨ بی‌نهایت",
        "manual": "✨ دستی",
        "days": "✨ روز",
        "hours": "✨ ساعت",
        "minutes": "✨ دقیقه",
        "remove_ref": "✨ حذف رفرال",
        "add_ref": "✨ افزودن رفرال",
        "no_limit": "✨ بدون محدودیت",
        "bot_users": "✨ کاربران بات",
        "premium_users": "✨ پرمیوم‌دار",
        "all_users": "✨ همه کاربران",
        "enter_number": "✨ لطفاً یک عدد وارد کنید:",
        "enter_channel": "✨ لطفاً لینک کانال شماره {num} را ارسال کنید:\n(ربات را در کانال ادمین کنید)",
        "enter_channel_manual": "<b>✨ لینک کانال‌های اسپانسر را ارسال کنید:</b>\n\n✨ شما باید ادمین کانال باشید و ربات هم باید ادمین کانال باشد.\nهر لینک را در یک خط جداگانه ارسال کنید.",
        "lottery_created": "✨ قرعه‌کشی با موفقیت ساخته شد!",
        "participate": "✨ شرکت در قرعه‌کشی",
        "joined_success": "✨✨ تبریک! شما با موفقیت در قرعه‌کشی {name} شرکت داده شدید. ✨✨\n\n✨ تعداد تیکت‌های شما: {tickets}\n✨ لینک رفرال اختصاصی شما:\n{ref_link}\n\n✨ برای افزایش شانس خود، دوستانتان را دعوت کنید!",
        "already_joined": "✨ شما قبلاً در این قرعه‌کشی شرکت کرده‌اید!",
        "lottery_ended": "✨ این قرعه‌کشی به پایان رسیده یا غیرفعال است!",
        "full_capacity": "✨ ظرفیت قرعه‌کشی پر شده است!",
        "not_found": "✨ قرعه‌کشی مورد نظر یافت نشد!",
        "math_question": "✨ لطفاً برای تایید شرکت، پاسخ دهید:\n<blockquote>{question} = ?</blockquote>",
        "math_wrong": "✨ پاسخ اشتباه بود!",
        "math_timeout": "✨ زمان پاسخ شما به پایان رسید!",
        "blocked_message": "✨ شما به دلیل اسپم به مدت {minutes} دقیقه مسدود شدید.",
        "still_blocked": "✨ شما مسدود هستید. {seconds} ثانیه دیگر تلاش کنید.",
        "profile_text": "👤 <b>حساب کاربری</b> 💪\n\nخوش اومدی به حساب کاربریت! 🎉\nاینجا می‌تونی اطلاعات، شانس‌ها و فعالیت‌های خودت در گیوای‌ها رو مشاهده و مدیریت کنی. 🎯\n\n🪪 <b>نام:</b> {display_name}\n🥷 <b>یوزرنیم:</b> {username}\n📰 <b>آیدی:</b> {id}\n👤 <b>گیوای‌های شرکت‌کرده:</b> {participations}\n👥 <b>دعوت‌های موفق:</b> {referrals}\n🗓 <b>عضویت در ربات:</b> {joined_at}",
        "active_list": "✨ قرعه‌کشی‌های فعال:\n\n{list}",
        "no_active": "✨ هیچ قرعه‌کشی فعالی وجود ندارد!",
        "manage_title": "✨ مدیریت قرعه‌کشی\n\nلطفاً یکی از قرعه‌کشی‌های خود را انتخاب کنید:",
        "no_manage": "✨ شما هیچ قرعه‌کشی نساخته‌اید!\nبرای ساخت قرعه‌کشی جدید از دکمه مربوطه استفاده کنید.",
        "manage_lottery_title": "✨ مدیریت: {name}\n\n{card}",
        "list_participants": "✨ لیست شرکت‌کنندگان",
        "delete_lottery": "✨ حذف قرعه‌کشی",
        "early_end": "✨ پایان زودهنگام",
        "message_participants": "✨ پیام به شرکت‌کنندگان",
        "referral_list": "✨ لیست رفرال‌ها",
        "change_time": "✨ تغییر زمان",
        "confirm_delete": "✨ آیا مطمئن هستید که می‌خواهید این قرعه‌کشی را حذف کنید؟\nاین عمل غیرقابل بازگشت است!",
        "yes_delete": "✨ بله، حذف کن",
        "no_delete": "✨ نه، برگرد",
        "deleted": "✨ قرعه‌کشی با موفقیت حذف شد!",
        "ended": "✨ قرعه‌کشی پایان یافت!",
        "participants_list": "✨ لیست شرکت‌کنندگان ({count} نفر):\n\n{list}",
        "no_participants": "✨ هنوز هیچ شرکت‌کننده‌ای وجود ندارد!",
        "admin_users_stats": "✨ آمار کاربران\n\n✨ تعداد کل کاربران: {users}\n✨ تعداد قرعه‌کشی‌ها: {lotteries}\n✨ تعداد قرعه‌کشی‌های فعال: {active}",
        "support_text": "<blockquote>برای ارتباط با مدیریت و پشتیبانی ربات:\n\nمالک: @YourUsername\n\nدر صورت داشتن مشکل، پیشنهاد یا سوال پیام دهید.\nپاسخگویی: ۲۴ ساعته</blockquote>",
        "about_bot_text": "🤖 <b>درباره ربات</b> 🤖\n\nبه __BOT_NAME__ خوش اومدی! 🎉🎁\n\nاین ربات یک پلتفرم حرفه‌ای برای ساخت و شرکت در قرعه‌کشی و گیوایه؛ جایی که می‌تونی خیلی راحت گیوای خودت رو بسازی، شرکت‌کننده جذب کنی و شانس برنده شدنت رو با فعالیت‌های مختلف افزایش بدی. 🚀✨\n\nاز دعوت دوستان و بوست گرفته تا روش‌های مختلف کسب شانس، همه‌چیز اینجاست تا تجربه‌ای ساده، جذاب و عادلانه از گیوای داشته باشی.💎\n\nساده بساز، هوشمندانه شرکت کن و با شانس بیشتر برنده شو! 🏆",
"user_not_joined": "✨ شما هنوز عضو کانال زیر نشده‌اید!\n\n{channels}\n\nبرای استفاده از ربات ابتدا عضو شوید.",
    },
}
TEXTS["en"] = TEXTS["fa"]
TEXTS["ru"] = TEXTS["fa"]


def get_text(user_id, key, **kwargs):
    lang = user_languages.get(user_id, "fa")
    text = TEXTS.get(lang, TEXTS["fa"]).get(key, key)
    if text is None:
        text = key
    text = text.replace("@YourUsername", "@" + OWNER_USERNAME).replace("__BOT_NAME__", BOT_DISPLAY_NAME)
    if kwargs:
        text = text.format(**kwargs)
    return text


def get_button_text(user_id, key):
    lang = user_languages.get(user_id, "fa")
    return TEXTS.get(lang, TEXTS["fa"]).get(key, key)


def save_data():
    try:
        data = {
            "forced_channels": forced_channels,
            "giveaway_channels": giveaway_channels,
            "user_languages": user_languages,
            "lotteries": lotteries,
            "all_users": list(all_users),
            "user_join_dates": user_join_dates,
        }
        tmp_path = DATA_FILE + ".tmp"
        with open(tmp_path, "w", encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        os.replace(tmp_path, DATA_FILE)
        try:
            import shutil
            shutil.copy2(DATA_FILE, BACKUP_FILE)
        except Exception:
            pass
    except Exception as e:
        print(f"خطا در ذخیره داده‌ها: {e}")


def load_data():
    global forced_channels, giveaway_channels, user_languages, lotteries, all_users, user_join_dates
    try:
        if os.path.exists(DATA_FILE):
            with open(DATA_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
            forced_channels = data.get("forced_channels", [])
            giveaway_channels = data.get("giveaway_channels", [])
            user_languages = data.get("user_languages", {})
            lotteries = data.get("lotteries", {})
            all_users = set(data.get("all_users", []))
            user_join_dates = data.get("user_join_dates", {})
        elif os.path.exists(BACKUP_FILE):
            with open(BACKUP_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
            forced_channels = data.get("forced_channels", [])
            giveaway_channels = data.get("giveaway_channels", [])
            user_languages = data.get("user_languages", {})
            lotteries = data.get("lotteries", {})
            all_users = set(data.get("all_users", []))
            user_join_dates = data.get("user_join_dates", {})
    except Exception as e:
        print(f"خطا در بارگذاری داده‌ها: {e}")
        forced_channels = []
        save_data()


def track_user(user_id):
    key = str(user_id)
    changed = False
    if user_id not in all_users:
        all_users.add(user_id)
        changed = True
    if key not in user_join_dates:
        user_join_dates[key] = time.strftime('%Y/%m/%d - %H:%M:%S', time.localtime())
        changed = True
    if changed:
        save_data()


def generate_lottery_id():
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=8))


def extract_channel_username(link):
    if link is None:
        return None
    patterns = [r't\.me\/([a-zA-Z0-9_]+)', r'telegram\.me\/([a-zA-Z0-9_]+)', r'@([a-zA-Z0-9_]+)']
    for pattern in patterns:
        match = re.search(pattern, str(link))
        if match:
            return match.group(1)
    return None


def to_chat_ref(channel):
    if channel is None:
        return None
    raw = str(channel).strip()
    if not raw:
        return None
    username = extract_channel_username(raw)
    if username:
        return f"@{username}"
    stripped = raw.lstrip("-")
    if stripped.isdigit():
        try:
            return int(raw)
        except ValueError:
            pass
    return None


def normalize_channel_ref(channel_link):
    if channel_link is None:
        return ""
    username = extract_channel_username(str(channel_link or ""))
    if username:
        return "@" + username.lower()
    return str(channel_link or "").strip().lower()


def register_giveaway_channel(channel_link):
    global giveaway_channels
    link = str(channel_link or "").strip()
    key = normalize_channel_ref(link)
    for item in giveaway_channels:
        if normalize_channel_ref(item) == key:
            return
    giveaway_channels.append(link)


def get_registered_giveaway_channels():
    result, seen = [], set()
    for source in (giveaway_channels, [c for l in lotteries.values() for c in l.get("channels", [])]):
        for channel in source:
            key = normalize_channel_ref(channel)
            if key and key not in seen:
                seen.add(key)
                result.append(channel)
    return result


async def get_bot_channel_status(channel_link):
    username = extract_channel_username(str(channel_link or ""))
    chat_ref = "@" + username if username else str(channel_link).strip()
    try:
        chat = await bot.get_chat(chat_ref)
        member = await bot.get_chat_member(chat.id, bot.id)
        is_admin = member.status in ("administrator", "creator")
        return {"ok": True, "title": getattr(chat, "title", None) or getattr(chat, "username", None) or chat_ref, "status": member.status, "is_admin": is_admin}
    except Exception:
        return {"ok": False, "title": chat_ref, "status": "unknown", "is_admin": False}


def channel_lottery_stats(channel_link):
    key = normalize_channel_ref(channel_link)
    total = active = 0
    for lottery in lotteries.values():
        channels = lottery.get("channels", []) or []
        if any(normalize_channel_ref(c) == key for c in channels):
            total += 1
            if lottery.get("active", False) and lottery.get("end_time", 0) > time.time():
                active += 1
    return total, active


async def check_channel_membership(user_id, channel_username):
    try:
        if channel_username.startswith('@'):
            channel_username = channel_username[1:]
        member = await bot.get_chat_member(f"@{channel_username}", user_id)
        if member.status in ("member", "administrator", "creator"):
            return True, None
        return False, "شما عضو این کانال نیستید!"
    except Exception as e:
        return False, f"خطا: {str(e)}"


async def check_forced_channels(user_id):
    if not forced_channels:
        return True, None
    not_joined = []
    for channel in forced_channels:
        username = extract_channel_username(channel)
        if username:
            is_member, _ = await check_channel_membership(user_id, username)
            if not is_member:
                not_joined.append(channel)
        else:
            not_joined.append(channel)
    if not_joined:
        return False, not_joined
    return True, None


def is_blocked(user_id):
    until = blocked_until.get(user_id)
    if until and time.time() < until:
        return int(until - time.time())
    if until:
        del blocked_until[user_id]
    return None


async def register_fail(user_id, reason_key):
    count = math_fail.get(user_id, 0) + 1
    if count >= MATH_FAIL_LIMIT:
        math_fail[user_id] = 0
        blocked_until[user_id] = time.time() + BLOCK_MINUTES * 60
        try:
            await tg_send(user_id, get_text(user_id, "blocked_message", minutes=BLOCK_MINUTES))
        except Exception:
            pass
    else:
        math_fail[user_id] = count
        try:
            await tg_send(user_id, f"{get_text(user_id, reason_key)}\n✨ تلاش {count}/{MATH_FAIL_LIMIT}")
        except Exception:
            pass


def schedule_math_timeout(user_id, token):
    asyncio.create_task(_math_timeout(user_id, token))


async def _math_timeout(user_id, token):
    await asyncio.sleep(MATH_TIMEOUT_SECONDS)
    data = user_math.get(user_id)
    if data and data.get("token") == token:
        del user_math[user_id]
        await register_fail(user_id, "math_timeout")


async def send_menu(user_id, text, keyboard=None, chat_id=None):
    chat_id = chat_id or user_id
    prev = nav_message.get(user_id)
    if prev:
        await tg_delete(chat_id, prev)
    sent = await tg_send(chat_id, text, reply_markup=keyboard)
    nav_message[user_id] = sent.message_id
    return sent


def is_admin(user_id):
    return int(user_id) in ADMIN_IDS


def get_main_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(colored_button(get_button_text(user_id, "new_lottery"), "new_lottery", "danger"))
    kb.add(
        colored_button(get_button_text(user_id, "active_lotteries"), "active_lotteries", "primary"),
        colored_button(get_button_text(user_id, "manage_lottery"), "manage_lottery", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "profile"), "profile", "success"),
        colored_button(get_button_text(user_id, "about_bot"), "about_bot", "success"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "support"), "support", "primary"),
        colored_button(get_button_text(user_id, "language"), "language", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "guide"), "guide", "success"),
        colored_button(get_button_text(user_id, "top_winners"), "top_winners", "success"),
    )
    if is_admin(user_id):
        kb.add(colored_button(get_button_text(user_id, "admin_panel"), "admin_panel", "danger"))
    return kb.markup()


def get_admin_keyboard(user_id):
    kb = KB(row_width=1)
    kb.add(
        colored_button("🎁 مدیریت گیواوی‌های کاربران", "admin_manage_giveaways", "primary"),
        colored_button("📢 فوروارد همگانی", "broadcast", "primary"),
        colored_button(get_button_text(user_id, "admin_users"), "admin_users_stats", "primary"),
        colored_button(get_button_text(user_id, "admin_lottery"), "admin_lottery_stats", "primary"),
        colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"),
    )
    return kb.markup()


def get_cancel_back_keyboard(user_id, show_back=True):
    kb = KB(row_width=2)
    buttons = []
    if show_back:
        buttons.append(colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"))
    buttons.append(colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"))
    kb.add(*buttons)
    return kb.markup()


def get_step2_keyboard(user_id):
    kb = KB(row_width=3)
    kb.add(
        colored_button("۵ دقیقه", "dur_5min", "primary"),
        colored_button("۱۰ دقیقه", "dur_10min", "primary"),
        colored_button("۳۰ دقیقه", "dur_30min", "primary"),
        colored_button("۱ ساعت", "dur_1hour", "primary"),
        colored_button("۲ ساعت", "dur_2hour", "primary"),
        colored_button("۲۴ ساعت", "dur_1day", "primary"),
    )
    kb.add(colored_button(get_button_text(user_id, "manual"), "dur_manual", "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "danger"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step3_keyboard(user_id):
    kb = KB(row_width=3)
    kb.add(
        colored_button("۱۰ نفر", "cap_10", "primary"),
        colored_button("۵۰ نفر", "cap_50", "primary"),
        colored_button("۱۰۰ نفر", "cap_100", "primary"),
        colored_button("۵۰۰ نفر", "cap_500", "primary"),
        colored_button("۱۰۰۰ نفر", "cap_1000", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "unlimited"), "cap_unlimited", "primary"),
        colored_button(get_button_text(user_id, "manual"), "cap_manual", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "danger"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step4_keyboard(user_id):
    kb = KB(row_width=4)
    kb.add(
        colored_button("۱ برنده", "win_1", "primary"),
        colored_button("۲ برنده", "win_2", "primary"),
        colored_button("۳ برنده", "win_3", "primary"),
        colored_button("۵ برنده", "win_5", "primary"),
    )
    kb.add(colored_button("۱۰ برنده", "win_10", "primary"))
    kb.add(colored_button(get_button_text(user_id, "manual"), "win_manual", "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "danger"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step5_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(colored_button("بدون حداقل", "min_0", "primary"))
    kb.add(
        colored_button("۵", "min_5", "primary"),
        colored_button("۱۰", "min_10", "primary"),
        colored_button("۲۰", "min_20", "primary"),
        colored_button("۵۰", "min_50", "primary"),
    )
    kb.add(colored_button(get_button_text(user_id, "manual"), "min_manual", "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step6_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(
        colored_button(get_button_text(user_id, "bot_users"), "acc_bot", "primary"),
        colored_button(get_button_text(user_id, "premium_users"), "acc_premium", "primary"),
        colored_button(get_button_text(user_id, "all_users"), "acc_all", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step7_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(colored_button("بدون نیاز به کانال", "ch_0", "primary"))
    kb.add(
        colored_button("۱ کانال", "ch_1", "primary"),
        colored_button("۲ کانال", "ch_2", "primary"),
        colored_button("۳ کانال", "ch_3", "primary"),
        colored_button("۴ کانال", "ch_4", "primary"),
        colored_button("۵ کانال", "ch_5", "primary"),
        colored_button("۶ کانال", "ch_6", "primary"),
    )
    kb.add(colored_button(get_button_text(user_id, "manual"), "ch_manual", "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step8_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(
        colored_button(get_button_text(user_id, "remove_ref"), "ref_remove", "danger"),
        colored_button(get_button_text(user_id, "add_ref"), "ref_add", "primary"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step8_5_keyboard(user_id):
    kb = KB(row_width=1)
    kb.add(colored_button(get_button_text(user_id, "no_limit"), "ref_no_limit", "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step9_keyboard(user_id):
    kb = KB(row_width=1)
    for callback_data, label, _ in PRIZE_OPTIONS:
        kb.add(colored_button(label, callback_data, "primary"))
    kb.add(colored_button(get_button_text(user_id, "manual"), PRIZE_MANUAL_CALLBACK, "primary"))
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_step10_keyboard(user_id):
    kb = KB(row_width=2)
    kb.add(
        colored_button("✅ فعال", "captcha_on", "success"),
        colored_button("❌ غیرفعال", "captcha_off", "danger"),
    )
    kb.add(
        colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
        colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
    )
    return kb.markup()


def get_lottery_link(lottery_id):
    username = BOT_USERNAME or "telegram"
    return f"https://t.me/{username}?start=lottery_{lottery_id}"


def get_referral_link(lottery_id, user_id):
    username = BOT_USERNAME or "telegram"
    return f"https://t.me/{username}?start=ref_{lottery_id}_{user_id}"


def link_anchor(url, label):
    return f'<a href="{url}">{label}</a>'


def get_user_tickets(user_id, lottery_id):
    if lottery_id in lotteries:
        refs = lotteries[lottery_id].get("referrals", {})
        count = refs.get(user_id, refs.get(str(user_id), 0))
        try:
            count = int(count)
        except (TypeError, ValueError):
            count = 0
        return count + 1
    return 1


def get_referral_count(user_id, lottery_id):
    lottery = lotteries.get(lottery_id)
    if not lottery:
        return 0
    refs = lottery.get("referrals", {}) or {}
    total = refs.get(user_id, refs.get(str(user_id), 0))
    try:
        return max(0, int(total))
    except (TypeError, ValueError):
        return 0


def get_participant_count(lottery_id):
    lottery = lotteries.get(lottery_id)
    if not lottery:
        return 0
    return len(lottery.get("participants", []) or [])


def build_channel_post_keyboard(lottery_id, lottery):
    lottery_link = get_lottery_link(lottery_id)
    participants = get_participant_count(lottery_id)
    kb = KB(row_width=1)
    kb.add(colored_button("✨ شرکت در قرعه‌کشی", None, "primary", url=lottery_link))
    kb.add(colored_button(f"✨ {participants} نفر شرکت کردن", f"participants_{lottery_id}", "danger"))
    return kb.markup()


def schedule_lottery_end(lottery_id):
    lottery = lotteries.get(lottery_id)
    if not lottery or not lottery.get("active"):
        return
    version = int(lottery.get("timer_version", 0))
    delay = max(0, lottery.get("end_time", time.time()) - time.time())
    asyncio.create_task(_delayed_end(lottery_id, version, delay))


async def _delayed_end(lottery_id, version, delay):
    await asyncio.sleep(delay)
    await end_lottery(lottery_id, version)


async def update_lottery_channel_posts(lottery_id):
    lottery = lotteries.get(lottery_id)
    if not lottery:
        return

    channels = lottery.get("channels", []) or []
    if not channels:
        return

    posts = lottery.setdefault("channel_posts", {})
    keyboard = build_channel_post_keyboard(lottery_id, lottery)
    formatted_text = build_default_channel_text(lottery)

    # Synchronize every configured channel.
    # A channel without a saved post gets a new giveaway post.
    for channel in channels:
        chat_ref = to_chat_ref(channel)
        if chat_ref is None:
            continue

        key = str(channel)
        message_id = posts.get(key)

        try:
            if message_id:
                await tg_edit_markup(chat_ref, message_id, keyboard)
            else:
                sent = await tg_send(
                    chat_ref,
                    formatted_text,
                    reply_markup=keyboard,
                    parse_mode=ParseMode.HTML,
                )
                posts[key] = sent.message_id
        except Exception as e:
            print(f"خطا در همگام‌سازی کانال {channel}: {e}")

    save_data()


def format_duration(lottery):
    duration = lottery.get("duration", 0)
    duration_type = lottery.get("duration_type", "minutes")
    unit = {"days": "روز", "hours": "ساعت", "minutes": "دقیقه"}.get(duration_type, "دقیقه")
    return f"{duration} {unit}"


async def check_lottery_mandatory_channels(user_id, lottery):
    channels = []
    for ch in (lottery.get("mandatory_channels", []) or []) + (lottery.get("channels", []) or []):
        if normalize_channel_ref(ch) and not any(normalize_channel_ref(x) == normalize_channel_ref(ch) for x in channels):
            channels.append(ch)
    if not channels:
        return True, []
    not_joined = []
    for channel in channels:
        username = extract_channel_username(channel)
        if username:
            is_member, _ = await check_channel_membership(user_id, username)
        else:
            is_member = True
        if not is_member:
            not_joined.append(channel)
    return len(not_joined) == 0, not_joined


async def show_mandatory_channels(user_id, lottery_id):
    lottery = lotteries.get(lottery_id)
    if not lottery:
        await tg_send(user_id, "<b>✨ قرعه‌کشی پیدا نشد.</b>")
        return
    if lottery.get("creator_id") != user_id and not is_admin(user_id):
        await tg_send(user_id, "<b>✨ فقط سازنده یا ادمین مجاز می‌تواند کانال اجباری را مدیریت کند.</b>")
        return
    channels = []
    for ch in (lottery.get("channels", []) or []) + (lottery.get("mandatory_channels", []) or []):
        if normalize_channel_ref(ch) and not any(normalize_channel_ref(x) == normalize_channel_ref(ch) for x in channels):
            channels.append(ch)
    listing = "\n".join(f"<b>{i}.</b> {html.escape(str(ch))}" for i, ch in enumerate(channels, 1))
    if not listing:
        listing = "<b>✨ هنوز کانال اجباری ثبت نشده است.</b>"
    text = f"<b>✨ کانال اجباری\n\nتعداد کانال‌ها: {len(channels)}\n\n{listing}</b>"
    kb = KB(row_width=2)
    kb.add(
        colored_button("➕ افزودن کانال", f"mandatory_add_{lottery_id}", "primary"),
        colored_button("➖ حذف کانال", f"mandatory_remove_{lottery_id}", "danger"),
    )
    kb.add(colored_button("↩️ بازگشت", f"manage_{lottery_id}", "danger"))
    await send_menu(user_id, text, kb.markup())


def show_lottery_card(user_id, lottery, show_join_button=True, show_back=True):
    access_text = {
        "bot_users": "همه کاربران بات",
        "premium": "کاربران پرمیوم",
        "all": "همه کاربران",
    }.get(lottery.get("access", "all"), "همه کاربران")

    capacity = lottery.get("capacity", "unlimited")
    capacity_text = "∞ نامحدود" if capacity == "unlimited" else str(capacity)

    referral_enabled = bool(lottery.get("referral", False))
    max_ref = lottery.get("max_ref", "unlimited")
    referral_text = "∞ نامحدود" if max_ref == "unlimited" else str(max_ref)

    min_participants = lottery.get("min_participants", 0)
    min_text = "بدون محدودیت" if not min_participants else f"{min_participants} نفر"

    channels = []
    for ch in (lottery.get("channels", []) or []) + (lottery.get("mandatory_channels", []) or []):
        if normalize_channel_ref(ch) and not any(
            normalize_channel_ref(x) == normalize_channel_ref(ch) for x in channels
        ):
            channels.append(ch)

    remaining = lottery.get("end_time", time.time() + 300) - time.time()
    if remaining > 0 and lottery.get("active", False):
        total_seconds = int(remaining)
        days, rem = divmod(total_seconds, 86400)
        hours, rem = divmod(rem, 3600)
        minutes, seconds = divmod(rem, 60)
        if days:
            time_text = f"{days}d {hours}h"
        elif hours:
            time_text = f"{hours}h {minutes}m"
        else:
            time_text = f"{minutes}m {seconds}s"
        status_text = "فعال 🟢"
    else:
        time_text = "تمام شده"
        status_text = "پایان یافته 🔴"

    user_tickets = get_user_tickets(user_id, lottery.get("id"))
    user_referrals = get_referral_count(user_id, lottery.get("id"))
    participants_count = get_participant_count(lottery.get("id"))

    name = html.escape(str(lottery.get("name", "بدون نام")))
    reward = html.escape(str(lottery.get("reward", "بدون جایزه")))
    creator = html.escape(str(lottery.get("creator", "نامشخص")))
    if creator and not creator.startswith("@"):
        creator = "@" + creator

    lines = [
        f"╭━━━━━━ 🎁 {name} ━━━━━━╮",
        "",
        f"🎁 جایزه  ‹ {reward}",
        f"🏆 برنده‌ها  ‹ {lottery.get('winners', 1)}",
        f"🚀 ظرفیت  ‹ {capacity_text}",
        f"🌍 دسترسی  ‹ {access_text}",
        f"👤 برگزارکننده  ‹ {creator}",
    ]

    if channels:
        lines.append(f"📣 کانال‌ها  ‹ {len(channels)} کانال")

    if min_participants:
        lines.append(f"👥 حداقل شرکت‌کننده  ‹ {min_text}")

    if referral_enabled:
        lines.append(f"🎟️ رفرال  ‹ {referral_text}")
        lines.append(f"👥 دعوت موفق شما  ‹ {user_referrals} نفر")

    lines += [
        "",
        f"🔎 {participants_count} نفر شرکت کرده‌اند",
        f"⏳ زمان باقی‌مانده  ‹ {time_text}",
        f"🎟️ تیکت‌های شما  ‹ {user_tickets}",
        "",
        f"╰━━━━━━ {status_text} ━━━━━━╯",
    ]

    kb = KB(row_width=1)

    if show_join_button and lottery.get("active", False):
        kb.add(colored_button("🎁 شرکت در قرعه‌کشی", f"join_{lottery['id']}", "primary"))

    if show_back:
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))

    return "\n".join(lines), kb.markup()


async def show_referral_center(user_id):
    active = [l for l in lotteries.values() if l.get("active", False) and l.get("end_time", 0) > time.time() and l.get("referral", False)]
    if not active:
        text = "👥 <b>رفرال‌گیری</b>\n\nبا رفرال‌گیری شانس برنده شدن شما در قرعه‌کشی‌هایی که شرکت می‌کنید بیشتر میشه.\n\n❌ در حال حاضر قرعه‌کشی فعالی با سیستم رفرال وجود ندارد."
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, text, kb.markup())
        return
    lines = ["👥 <b>رفرال‌گیری</b>", "", "با رفرال‌گیری شانس برنده شدن شما در قرعه‌کشی‌هایی که شرکت می‌کنید بیشتر میشه.", "", "🎯 قرعه‌کشی موردنظر را انتخاب کن:"]
    kb = KB(row_width=1)
    for l in active:
        lid = l.get("id")
        name = html.escape(str(l.get("name", "بدون نام")))
        ref_link = get_referral_link(lid, user_id)
        lines.append(f"🎁 <b>{name}</b>\n🔗 {ref_link}")
        kb.add(colored_button(f"🔗 رفرال {l.get('name', 'قرعه‌کشی')}", f"my_ref_{lid}", "success"))
    kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
    await send_menu(user_id, "\n\n".join(lines), kb.markup())


async def show_top_winners(user_id):
    win_counts = {}
    for lottery in lotteries.values():
        for winner_id in lottery.get("winners_list", []) or []:
            try:
                uid = int(winner_id)
            except (TypeError, ValueError):
                continue
            win_counts[uid] = win_counts.get(uid, 0) + 1
    top = sorted(win_counts.items(), key=lambda x: (-x[1], x[0]))[:3]
    lines = ["🏆 <b>برترین وینر ها</b>", "", "۳ نفری که بیشترین قرعه‌کشی را برنده شده‌اند:", ""]
    if not top:
        lines.append("هنوز برنده‌ای ثبت نشده است.")
    else:
        medals = ["🥇", "🥈", "🥉"]
        for i, (uid, count) in enumerate(top):
            try:
                u = await bot.get_chat(uid)
                name = f"@{u.username}" if u.username else (u.first_name or str(uid))
            except Exception:
                name = f"کاربر {uid}"
            lines.append(f"{medals[i]} <b>{html.escape(name)}</b> — {count} برد")
    kb = KB(row_width=1)
    kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
    await send_menu(user_id, "\n".join(lines), kb.markup())


async def show_profile(user_id):
    user = await bot.get_chat(user_id)
    display_name = user.first_name or "کاربر"
    username = f"@{user.username}" if user.username else "ندارد"

    total_participations = total_wins = total_tickets = total_referrals = 0
    for lottery in lotteries.values():
        if user_id in lottery.get("participants", []):
            total_participations += 1
            total_tickets += get_user_tickets(user_id, lottery.get("id"))
            total_referrals += get_referral_count(user_id, lottery.get("id"))
        if user_id in lottery.get("winners_list", []):
            total_wins += 1

    win_rate = (total_wins / total_participations) * 100 if total_participations > 0 else 0

    if total_participations >= 50:
        status = "✨ حرفه‌ای"
    elif total_participations >= 20:
        status = "✨ فعال"
    elif total_participations >= 5:
        status = "✨ معمولی"
    else:
        status = "✨ تازه‌کار"

    joined_at = user_join_dates.get(str(user_id), "نامشخص")
    text = get_text(
        user_id, "profile_text", display_name=html.escape(display_name), username=html.escape(username),
        id=str(user_id), participations=total_participations, referrals=total_referrals,
        joined_at=html.escape(joined_at), status=status, wins=total_wins, rate=win_rate, tickets=total_tickets,
    )
    kb = KB(row_width=1)
    kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
    return text, kb.markup()


def build_default_channel_text(lottery):
    if lottery is None:
        return ""
    name = html.escape(str(lottery.get("name", "بدون نام")))
    reward = html.escape(str(lottery.get("reward", "بدون جایزه")))
    duration_text = format_duration(lottery)

    channels = []
    for ch in (lottery.get("channels", []) or []) + (lottery.get("mandatory_channels", []) or []):
        if normalize_channel_ref(ch) and not any(normalize_channel_ref(x) == normalize_channel_ref(ch) for x in channels):
            channels.append(ch)
    channel_text = "\n".join(f"@{extract_channel_username(ch)}" for ch in channels if extract_channel_username(ch))
    lines = [
        f"🎰 <b>قرعه‌کشی {name}</b>",
        "<blockquote>"
        f"🎁 <b>جایزه:</b> {reward}\n"
        f"⌛️ <b>تایم:</b> {duration_text}"
        "</blockquote>",
        "",
        "برای شرکت روی دکمه زیر بزن 👇",
    ]
    return "\n".join(lines)


def detect_and_format_entities(text, is_custom_text=False):
    if text is None:
        return ""
    if not isinstance(text, str):
        return str(text)
    return text


async def send_lottery_to_channels(lottery_id, custom_text=None, photo_file_id=None, custom_is_preformatted_html=False):
    if lottery_id not in lotteries:
        print(f"خطا: قرعه‌کشی {lottery_id} یافت نشد!")
        return 0, 0, []

    lottery = lotteries[lottery_id]
    channels = list(lottery.get("channels", []) or [])
    if not channels:
        print(f"خطا: هیچ کانالی برای قرعه‌کشی {lottery_id} ثبت نشده است!")
        return 0, 0, []

    if custom_text and custom_text.strip():
        formatted_text = custom_text.strip() if custom_is_preformatted_html else html.escape(custom_text.strip())
    else:
        formatted_text = build_default_channel_text(lottery)
    if not formatted_text:
        return 0, 0, []

    lottery["channel_posts"] = {}
    keyboard = build_channel_post_keyboard(lottery_id, lottery)

    async def send_one(channel):
        chat_ref = to_chat_ref(channel)
        if chat_ref is None:
            return channel, None, "شناسه/لینک کانال نامعتبر است"
        for attempt in (1, 2):
            try:
                if photo_file_id:
                    caption = formatted_text if len(formatted_text) <= 1024 else formatted_text[:1020] + "…"
                    sent = await tg_send_photo(chat_ref, photo_file_id, caption=caption, reply_markup=keyboard)
                else:
                    sent = await tg_send(chat_ref, formatted_text, reply_markup=keyboard, parse_mode=ParseMode.HTML)
                return channel, sent.message_id, None
            except TelegramRetryAfter as e:
                if attempt == 1:
                    await asyncio.sleep(e.retry_after + 1)
                else:
                    return channel, None, f"محدودیت نرخ تلگرام (retry after {e.retry_after}s)"
            except TelegramForbiddenError:
                return channel, None, "ربات ادمین کانال نیست یا از کانال حذف/بلاک شده"
            except TelegramUnauthorizedError:
                return channel, None, "توکن ربات نامعتبر است"
            except TelegramBadRequest as e:
                return channel, None, f"درخواست نامعتبر: {e}"
            except TelegramNetworkError as e:
                return channel, None, f"خطای شبکه: {e}"
            except TelegramAPIError as e:
                return channel, None, f"خطای API تلگرام: {e}"
            except Exception as e:
                return channel, None, f"خطای ناشناخته: {e}"

    # Publish to all channels concurrently instead of waiting channel-by-channel.
    results = await asyncio.gather(*(send_one(ch) for ch in channels), return_exceptions=False)
    success_count = failed_count = 0
    failed_details = []
    for channel, message_id, error in results:
        if message_id is not None:
            lottery["channel_posts"][str(channel)] = message_id
            success_count += 1
        else:
            failed_count += 1
            failed_details.append((channel, error or "خطای نامشخص"))

    save_data()
    return success_count, failed_count, failed_details


async def get_user_display_name(user_id):
    """Return @username when available, otherwise the user's first name/ID."""
    try:
        u = await bot.get_chat(int(user_id))
        if getattr(u, "username", None):
            return "@" + u.username
        return getattr(u, "first_name", None) or str(user_id)
    except Exception:
        return str(user_id)


def build_winners_text(lottery, winners, winner_names):
    """Build the final giveaway result message for channels/admin/creator."""
    name = html.escape(str(lottery.get("name", "قرعه‌کشی")))
    reward = html.escape(str(lottery.get("reward", "بدون جایزه")))
    participants = len(lottery.get("participants", []) or [])
    lines = [
        "🏆 <b>قرعه‌کشی به پایان رسید!</b>",
        "",
        f"🎁 <b>قرعه‌کشی:</b> {name}",
        f"🎁 <b>جایزه:</b> {reward}",
        f"👥 <b>شرکت‌کنندگان:</b> {participants} نفر",
        "",
        "🏆 <b>برنده‌ها:</b>",
    ]
    if winners:
        for i, (uid, display_name) in enumerate(zip(winners, winner_names), 1):
            refs = get_referral_count(int(uid), lottery.get("id")) if lottery.get("id") else 0
            lines.append(f"{i}. <b>{html.escape(str(display_name))}</b> — 🎟️ {refs} رفرال")
    else:
        lines.append("برنده‌ای تعیین نشد.")
    lines += ["", "✨ ممنون که در قرعه‌کشی شرکت کردید!"]
    return "\n".join(lines)


async def send_winners_to_channels(lottery_id, winners_text):
    lottery = lotteries.get(lottery_id)
    if not lottery:
        return 0, 0

    channels = list(lottery.get("channels", []) or [])
    if not channels:
        print(f"نتیجه گیواوی {lottery_id}: هیچ کانال انتشاری ثبت نشده است.")
        return 0, 0

    kb = KB(row_width=1)
    kb.add(
        colored_button(
            "✨ مشاهده قرعه‌کشی",
            None,
            "primary",
            url=get_lottery_link(lottery_id),
        )
    )
    markup = kb.markup()

    async def send_one(channel):
        chat_ref = to_chat_ref(channel)
        if chat_ref is None:
            return False, channel, "شناسه/لینک کانال نامعتبر است"

        for attempt in (1, 2, 3):
            try:
                await tg_send(
                    chat_ref,
                    winners_text,
                    reply_markup=markup,
                    parse_mode=ParseMode.HTML,
                )
                return True, channel, None
            except TelegramRetryAfter as e:
                if attempt < 3:
                    await asyncio.sleep(e.retry_after + 1)
                    continue
                return False, channel, f"محدودیت نرخ تلگرام: {e.retry_after}s"
            except TelegramForbiddenError:
                return False, channel, "ربات ادمین کانال نیست یا اجازه ارسال ندارد"
            except TelegramUnauthorizedError:
                return False, channel, "توکن ربات نامعتبر است"
            except TelegramBadRequest as e:
                return False, channel, f"درخواست نامعتبر: {e}"
            except TelegramNetworkError as e:
                if attempt < 3:
                    await asyncio.sleep(2)
                    continue
                return False, channel, f"خطای شبکه: {e}"
            except TelegramAPIError as e:
                if attempt < 3:
                    await asyncio.sleep(2)
                    continue
                return False, channel, f"خطای API تلگرام: {e}"
            except Exception as e:
                if attempt < 3:
                    await asyncio.sleep(2)
                    continue
                return False, channel, f"خطای ناشناخته: {e}"

    # Send to every configured channel at the same time.
    results = await asyncio.gather(
        *(send_one(channel) for channel in channels),
        return_exceptions=False,
    )

    success = sum(1 for ok, _, _ in results if ok)
    failed = len(results) - success

    for ok, channel, error in results:
        if not ok:
            print(f"خطا در ارسال نتیجه به کانال {channel}: {error}")

    return success, failed



@router.message(CommandStart())
async def start_handler(message: Message):
    user_id = message.from_user.id
    track_user(user_id)
    user_first_name = message.from_user.first_name

    blocked_seconds = is_blocked(user_id)
    if blocked_seconds:
        await tg_send(user_id, get_text(user_id, "still_blocked", seconds=blocked_seconds))
        return

    await send_start_emoji(user_id)
    parts = message.text.split()
    if len(parts) > 1:
        args = parts[1]
        if args.startswith("lottery_"):
            lottery_id = args.replace("lottery_", "")
            if lottery_id in lotteries:
                lottery = lotteries[lottery_id]
                active = lottery.get("active", False)
                # The channel link opens the full giveaway card first.
                # Mandatory-channel checks happen only after the user presses Join.
                text, keyboard = show_lottery_card(user_id, lottery, show_join_button=active, show_back=True)
                await tg_send(user_id, text, reply_markup=keyboard)
                return
            await tg_send(user_id, get_text(user_id, "not_found"))
            return
        elif args.startswith("ref_"):
            ref_parts = args.split("_")
            if len(ref_parts) >= 3:
                lottery_id, referrer_id = ref_parts[1], int(ref_parts[2])
                if lottery_id in lotteries:
                    lottery = lotteries[lottery_id]
                    user_data.setdefault(user_id, {})["pending_ref"] = {"lottery_id": lottery_id, "referrer_id": referrer_id}
                    active = lottery.get("active", False)
                    text, keyboard = show_lottery_card(user_id, lottery, show_join_button=active, show_back=True)
                    await tg_send(user_id, text, reply_markup=keyboard)
                    return
                await tg_send(user_id, get_text(user_id, "not_found"))
                return

    text = get_text(user_id, "welcome", name=user_first_name)
    keyboard = get_main_keyboard(user_id)
    await send_menu(user_id, text, keyboard, chat_id=message.chat.id)


async def show_admin_giveaway_channels(user_id, back_lottery_id=None):
    if not is_admin(user_id):
        return
    channels = get_registered_giveaway_channels()
    if not channels:
        text = "✨ <b>مدیریت کانال‌های گیوای</b>\n\n✨ هنوز هیچ کانالی ثبت نشده است."
    else:
        lines = ["✨ <b>مدیریت کانال‌های گیوای</b>", ""]
        for i, ch in enumerate(channels, 1):
            info = await get_bot_channel_status(ch)
            total, active = channel_lottery_stats(ch)
            status_line = "✨ ربات ادمین است" if info.get("is_admin") else "✨ ربات ادمین نیست/داخل کانال نیست"
            title = html.escape(str(info.get('title', ch)))
            lines.append(f"{i}. <b>{title}</b> — {ch}")
            lines.append(f"   {status_line} | کل: {total} | فعال: {active}")
        text = "\n".join(lines)

    kb = KB(row_width=1)
    add_callback = f"admin_giveaway_channel_add_{back_lottery_id}" if back_lottery_id else "admin_giveaway_channel_add"
    kb.add(colored_button("✨ افزودن کانال جدید", add_callback, "primary"))
    for idx, ch in enumerate(channels, 1):
        delete_callback = f"admin_giveaway_channel_delete_{back_lottery_id}_{idx}" if back_lottery_id else f"admin_giveaway_channel_delete_{idx}"
        kb.add(colored_button(f"✨ حذف {idx}. {ch}", delete_callback, "danger"))
    if back_lottery_id and back_lottery_id in lotteries:
        kb.add(colored_button(get_button_text(user_id, "back"), f"manage_{back_lottery_id}", "danger"))
    else:
        kb.add(colored_button(get_button_text(user_id, "back"), "admin_panel", "danger"))
    await send_menu(user_id, text, kb.markup())


async def check_giveaway_channel_permissions(channel_link, creator_id):
    chat_ref = to_chat_ref(channel_link)
    if chat_ref is None:
        return False, "✨ لینک کانال باید به شکل @channel، https://t.me/channel یا آیدی عددی کانال باشد."
    try:
        creator_member = await bot.get_chat_member(chat_ref, creator_id)
        if creator_member.status not in ("administrator", "creator"):
            return False, f"✨ شما در {chat_ref} ادمین نیستید."
    except Exception as e:
        return False, f"✨ بررسی ادمین بودن شما در {chat_ref} انجام نشد.\n{e}"
    try:
        bot_member = await bot.get_chat_member(chat_ref, bot.id)
        if bot_member.status not in ("administrator", "creator"):
            return False, f"✨ ربات در {chat_ref} ادمین نیست."
        can_post = getattr(bot_member, "can_post_messages", None)
        if can_post is False:
            return False, f"✨ ربات در {chat_ref} اجازه ارسال پست ندارد."
    except Exception as e:
        return False, f"✨ ربات به {chat_ref} دسترسی ندارد.\n{e}"
    return True, None


async def validate_giveaway_channels(user_id, channels):
    if not channels:
        return False, "✨ برای ساخت گیواوی حداقل یک کانال لازم است."
    for channel in channels:
        ok, error = await check_giveaway_channel_permissions(channel, user_id)
        if not ok:
            return False, error
    return True, None


async def show_step(user_id, step):
    texts = {
        1: (get_text(user_id, "step1"), get_cancel_back_keyboard(user_id, show_back=False)),
        2: (get_text(user_id, "step2"), get_step2_keyboard(user_id)),
        3: (get_text(user_id, "step3"), get_step3_keyboard(user_id)),
        4: (get_text(user_id, "step4"), get_step4_keyboard(user_id)),
        5: (get_text(user_id, "step5"), get_step5_keyboard(user_id)),
        6: (get_text(user_id, "step6"), get_step6_keyboard(user_id)),
        7: (get_text(user_id, "step7"), get_step7_keyboard(user_id)),
        8: (get_text(user_id, "step8"), get_step8_keyboard(user_id)),
        8.5: ("✨ حداکثر تعداد رفرال\n\n✨ حداکثر چند نفر دعوت‌شده به‌شان تیکت رایگان بدهد؟", get_step8_5_keyboard(user_id)),
        9: (get_text(user_id, "step9"), get_step9_keyboard(user_id)),
        10: (get_text(user_id, "step10"), get_step10_keyboard(user_id)),
    }
    text, keyboard = texts.get(step, ("", get_cancel_back_keyboard(user_id)))
    if text:
        await send_menu(user_id, text, keyboard)


async def create_lottery(user_id, username, first_name):
    udata = user_data[user_id]["data"]
    channels = udata.get("channels", [])
    if not channels:
        await tg_send(user_id, "✨ حداقل یک کانال اسپانسر باید انتخاب شود.")
        user_data[user_id]["step"] = 7
        await show_step(user_id, 7)
        return

    ok, error = await validate_giveaway_channels(user_id, channels)
    if not ok:
        await tg_send(user_id, f"✨ ساخت گیواوی متوقف شد.\n{error}")
        user_data[user_id]["step"] = 7
        await show_step(user_id, 7)
        return

    lottery_id = generate_lottery_id()
    duration = udata.get("duration", 10)
    duration_type = udata.get("duration_type", "minutes")
    if duration_type == "days":
        end_time = time.time() + duration * 86400
    elif duration_type == "hours":
        end_time = time.time() + duration * 3600
    else:
        end_time = time.time() + duration * 60

    lottery = {
        "id": lottery_id,
        "name": udata.get("name", "بدون نام"),
        "duration": duration,
        "duration_type": duration_type,
        "capacity": udata.get("capacity", "unlimited"),
        "winners": udata.get("winners", 1),
        "min_participants": udata.get("min_participants", 0),
        "access": udata.get("access", "all"),
        "channels": channels,
        "mandatory_channels": [],
        "referral": udata.get("referral", False),
        "max_ref": udata.get("max_ref", "unlimited"),
        "reward": udata.get("reward", "بدون جایزه"),
        "captcha_enabled": bool(udata.get("captcha_enabled", False)),
        "creator": username or first_name,
        "creator_id": user_id,
        "participants": [],
        "referrals": {},
        "referred_by": {},
        "referred_users": {},
        "start_time": time.time(),
        "end_time": end_time,
        "active": True,
        "winners_list": [],
        "timer_version": 0,
    }
    lotteries[lottery_id] = lottery
    for channel in channels:
        register_giveaway_channel(channel)
    del user_data[user_id]

    await notify_admin(f"قرعه‌کشی جدید ساخته شد!\n\n{lottery['name']}\nتوسط: @{lottery['creator']}")

    text, keyboard = show_lottery_card(user_id, lottery, show_join_button=True, show_back=True)
    ref_link = get_referral_link(lottery_id, user_id)
    lottery_link = get_lottery_link(lottery_id)
    kb = KB(row_width=1)
    kb.add(colored_button("✨ ارسال در کانال", f"send_channel_{lottery_id}", "primary"))
    for row in keyboard.inline_keyboard:
        for btn in row:
            kb.add(btn)
    await tg_send(
        user_id,
        f"{get_text(user_id, 'lottery_created')}\n\n"
        f"{link_anchor(lottery_link, '🔗 لینک قرعه‌کشی')}\n"
        f"{ref_link}\n\n"
        f"{text}",
        reply_markup=kb.markup(),
    )
    save_data()
    schedule_lottery_end(lottery_id)


async def end_lottery(lottery_id, timer_version=None, forced=False):
    if lottery_id not in lotteries:
        return
    lottery = lotteries[lottery_id]
    lottery.setdefault("id", lottery_id)
    if not forced:
        if not lottery.get("active", False):
            return
        if timer_version is not None and int(lottery.get("timer_version", 0)) != int(timer_version):
            return
        if lottery.get("end_time", 0) > time.time() + 1:
            return

    if not lottery.get("active", False) and forced:
        return

    lottery["active"] = False
    participants = list(lottery.get("participants", []) or [])
    min_participants = lottery.get("min_participants", 0)

    if len(participants) < min_participants:
        for p in participants:
            try:
                await tg_send(p, f"✨ قرعه‌کشی {lottery['name']} به دلیل عدم رسیدن به حداقل شرکت‌کننده لغو شد!")
            except Exception:
                pass
        creator_id = lottery.get("creator_id")
        if creator_id and creator_id != ADMIN_ID:
            try:
                await tg_send(creator_id, f"✨ قرعه‌کشی {lottery['name']} به دلیل عدم رسیدن به حداقل شرکت‌کننده لغو شد!")
            except Exception:
                pass
        await notify_admin(f"قرعه‌کشی {lottery['name']} به دلیل کمبود شرکت‌کننده لغو شد.")
        save_data()
        return

    winners_count = min(int(lottery.get("winners", 1) or 1), len(participants))
    if winners_count > 0:
        # Weighted draw without replacement: a participant cannot win twice.
        remaining = list(participants)
        winners = []
        while remaining and len(winners) < winners_count:
            weights = [max(1, int(get_user_tickets(p, lottery_id))) for p in remaining]
            chosen = random.choices(remaining, weights=weights, k=1)[0]
            winners.append(chosen)
            remaining.remove(chosen)
        lottery["winners_list"] = winners

        winner_names = []
        for w in winners:
            winner_names.append(await get_user_display_name(w))

        creator_id = lottery.get("creator_id")
        creator_username = lottery.get("creator", "نامشخص")
        try:
            if creator_id:
                cu = await bot.get_chat(creator_id)
                creator_username = f"@{cu.username}" if cu.username else cu.first_name
        except Exception:
            pass

        for p in participants:
            try:
                tickets = get_user_tickets(p, lottery_id)
                if p in winners:
                    await tg_send(p, f"✨ تبریک! شما برنده قرعه‌کشی {lottery['name']} شدید!\n\n✨ جایزه: {lottery['reward']}\n✨ تیکت‌های شما: {tickets}\n\nجهت دریافت جایزه با {creator_username} تماس بگیرید.")
                else:
                    await tg_send(p, f"✨ متاسفانه در قرعه‌کشی {lottery['name']} برنده نشدید.\n✨ تیکت‌های شما: {tickets}\n\nدفعه بعد حتماً خوش‌شانس خواهید بود!")
            except Exception:
                pass

        winners_text = build_winners_text(lottery, winners, winner_names)
        winner_success, winner_failed = await send_winners_to_channels(lottery_id, winners_text)
        print(
            f"نتیجه گیواوی {lottery_id}: "
            f"{winner_success} کانال موفق، {winner_failed} کانال ناموفق"
        )
        await notify_admin(winners_text)
        if creator_id and creator_id != ADMIN_ID:
            try:
                await tg_send(creator_id, winners_text)
            except Exception:
                pass
    else:
        for p in participants:
            try:
                await tg_send(p, f"✨ قرعه‌کشی {lottery['name']} بدون برنده به پایان رسید!")
            except Exception:
                pass
        creator_id = lottery.get("creator_id")
        if creator_id and creator_id != ADMIN_ID:
            try:
                await tg_send(creator_id, f"✨ قرعه‌کشی {lottery['name']} بدون برنده به پایان رسید!")
            except Exception:
                pass
        await notify_admin(f"قرعه‌کشی {lottery['name']} بدون برنده به پایان رسید.")

    save_data()



def can_manage_lottery(user_id, lottery_id):
    lottery = lotteries.get(lottery_id)
    return bool(lottery) and (is_admin(user_id) or lottery.get("creator_id") == user_id)


async def show_admin_giveaway_list(user_id, page=0):
    """Admin giveaway list, paginated so one callback never builds an oversized request."""
    if not is_admin(user_id):
        return
    items = list(lotteries.items())
    if not items:
        text = "🎁 <b>مدیریت گیواوی‌های کاربران</b>\n\nهیچ گیواوی‌ای ثبت نشده است."
        kb = KB(row_width=1)
        kb.add(colored_button("↩️ بازگشت", "admin_panel", "danger"))
        await send_menu(user_id, text, kb.markup())
        return

    page_size = 8
    total_pages = max(1, (len(items) + page_size - 1) // page_size)
    page = max(0, min(int(page), total_pages - 1))
    start = page * page_size
    chunk = items[start:start + page_size]

    lines = [
        "🎁 <b>مدیریت گیواوی‌های کاربران</b>",
        "",
        f"صفحه {page + 1} از {total_pages}",
        "یکی از گیواوی‌ها را انتخاب کنید:",
        "",
    ]
    kb = KB(row_width=1)
    for index, (lid, l) in enumerate(chunk, start=start + 1):
        creator = l.get("creator", "نامشخص")
        if creator and not str(creator).startswith("@"):
            creator = "@" + str(creator)
        channels = l.get("channels", []) or []
        channel = extract_channel_username(channels[0]) if channels else None
        channel_text = "@" + channel if channel else (str(channels[0]) if channels else "ندارد")
        active = bool(l.get("active", False)) and l.get("end_time", 0) > time.time()
        status = "فعال" if active else ("متوقف" if l.get("paused", False) else "پایان‌یافته")
        count = get_participant_count(lid)
        lines.append(
            f"🎁 Giveaway #{index}\n"
            f"👤 سازنده: {html.escape(str(creator))}\n"
            f"📣 کانال: {html.escape(channel_text)}\n"
            f"👥 شرکت‌کننده: {count} نفر\n"
            f"⏰ وضعیت: {status}\n"
        )
        kb.add(colored_button(f"🎁 {l.get('name','بدون نام')} | {count} نفر", f"admin_gw_open_{lid}", "primary"))

    nav = []
    if page > 0:
        nav.append(colored_button("⬅️ قبلی", f"admin_gw_page_{page - 1}", "primary"))
    if page < total_pages - 1:
        nav.append(colored_button("➡️ بعدی", f"admin_gw_page_{page + 1}", "primary"))
    if nav:
        kb.add(*nav)
    kb.add(colored_button("↩️ بازگشت", "admin_panel", "danger"))
    await send_menu(user_id, "\n".join(lines), kb.markup())


async def show_admin_giveaway(user_id, lottery_id):
    if not is_admin(user_id):
        return
    l = lotteries.get(lottery_id)
    if not l:
        await tg_send(user_id, "❌ گیواوی پیدا نشد.")
        return
    active = bool(l.get("active", False)) and l.get("end_time", 0) > time.time()
    status = "فعال" if active else ("متوقف" if l.get("paused", False) else "پایان‌یافته")
    creator = l.get("creator", "نامشخص")
    if creator and not str(creator).startswith("@"):
        creator = "@" + str(creator)
    channels = l.get("channels", []) or []
    mandatory = l.get("mandatory_channels", []) or []
    winners = l.get("winners_list", []) or []
    winner_text = []
    for uid in winners:
        try:
            u = await bot.get_chat(int(uid))
            winner_text.append(f"@{u.username}" if u.username else (u.first_name or str(uid)))
        except Exception:
            winner_text.append(str(uid))
    text = (f"🎁 <b>مدیریت گیواوی</b>\n\n"
            f"🎁 نام: {html.escape(str(l.get('name','بدون نام')))}\n"
            f"👤 سازنده: {html.escape(str(creator))}\n"
            f"🎁 جایزه: {html.escape(str(l.get('reward','بدون جایزه')))}\n"
            f"📣 کانال انتشار: {len(channels)}\n"
            f"🔒 کانال اجباری: {len(mandatory)}\n"
            f"👥 شرکت‌کننده: {get_participant_count(lottery_id)} نفر\n"
            f"🎟️ رفرال موفق: {sum(int(v or 0) for v in (l.get('referrals',{}) or {}).values())}\n"
            f"🏆 برنده فعلی: {', '.join(winner_text) if winner_text else 'تعیین نشده'}\n"
            f"⏰ وضعیت: {status}")
    kb = KB(row_width=2)
    kb.add(colored_button("✏️ ویرایش گیواوی", f"admin_edit_menu_{lottery_id}", "primary"),
           colored_button("🗑️ حذف گیواوی", f"admin_delete_{lottery_id}", "danger"))
    if active:
        kb.add(colored_button("⏸️ توقف گیواوی", f"admin_pause_{lottery_id}", "danger"))
    else:
        kb.add(colored_button("▶️ فعال‌سازی مجدد", f"admin_resume_{lottery_id}", "success"))
    kb.add(colored_button("📣 مدیریت کانال‌ها", f"admin_channels_{lottery_id}", "primary"),
           colored_button("👥 مشاهده شرکت‌کنندگان", f"admin_participants_{lottery_id}", "primary"))
    kb.add(colored_button("🏆 مشاهده/تغییر برنده", f"admin_winner_{lottery_id}", "primary"),
           colored_button("🎟️ مدیریت رفرال", f"admin_referrals_{lottery_id}", "primary"))
    kb.add(colored_button("📊 مشاهده آمار", f"admin_stats_{lottery_id}", "primary"),
           colored_button("🔗 دریافت لینک گیواوی", None, "primary", url=get_lottery_link(lottery_id)))
    kb.add(colored_button("↩️ بازگشت", "admin_manage_giveaways", "danger"))
    await send_menu(user_id, text, kb.markup())


async def show_admin_edit_menu(user_id, lottery_id):
    if not is_admin(user_id) or lottery_id not in lotteries:
        return
    kb = KB(row_width=2)
    fields = [("✏️ نام", "name"), ("🎁 جایزه", "reward"), ("⏱️ مدت", "duration"),
              ("👥 ظرفیت", "capacity"), ("🏆 تعداد برنده", "winners"), ("📊 حداقل شرکت‌کننده", "min_participants"),
              ("🔐 دسترسی", "access"), ("🎟️ رفرال", "referral")]
    for label, field in fields:
        kb.add(colored_button(label, f"admin_edit_{lottery_id}_{field}", "primary"))
    kb.add(colored_button("↩️ بازگشت", f"admin_gw_open_{lottery_id}", "danger"))
    await send_menu(user_id, "✏️ <b>ویرایش گیواوی</b>\n\nبخش موردنظر را انتخاب کنید:", kb.markup())


async def show_admin_channels(user_id, lottery_id):
    if not is_admin(user_id) or lottery_id not in lotteries:
        return
    l = lotteries[lottery_id]
    pub = l.get("channels", []) or []
    mand = l.get("mandatory_channels", []) or []
    lines = ["📣 <b>مدیریت کانال‌ها</b>", "", "📣 کانال‌های انتشار:"]
    lines += [f"{i}. {html.escape(str(c))}" for i, c in enumerate(pub, 1)] or ["— ندارد"]
    lines += ["", "🔒 کانال‌های اجباری:"]
    lines += [f"{i}. {html.escape(str(c))}" for i, c in enumerate(mand, 1)] or ["— ندارد"]
    kb = KB(row_width=2)
    kb.add(colored_button("➕ افزودن انتشار", f"admin_ch_add_{lottery_id}_pub", "primary"),
           colored_button("➕ افزودن اجباری", f"admin_ch_add_{lottery_id}_mand", "primary"))
    kb.add(colored_button("🗑️ حذف انتشار", f"admin_ch_remove_{lottery_id}_pub", "danger"),
           colored_button("🗑️ حذف اجباری", f"admin_ch_remove_{lottery_id}_mand", "danger"))
    kb.add(colored_button("📋 لیست کانال‌ها", f"admin_channels_{lottery_id}", "primary"),
           colored_button("↩️ بازگشت", f"admin_gw_open_{lottery_id}", "danger"))
    await send_menu(user_id, "\n".join(lines), kb.markup())


async def show_user_referrals(user_id, lottery_id, details=False):
    l = lotteries.get(lottery_id)
    if not l or str(user_id) not in {str(x) for x in (l.get("participants", []) or [])}:
        await tg_send(user_id, "✨ ابتدا باید در این قرعه‌کشی شرکت کنید.")
        return
    referred_by = l.get("referred_by", {}) or {}
    # رفرال‌ها فقط از همین Giveaway خوانده می‌شوند.
    referred = [int(uid) for uid, ref in referred_by.items() if str(ref) == str(user_id)]
    # هر ورود موفق با لینک رفرال = یک رفرال و یک تیکت.
    tickets = len(referred)
    total = len(referred)
    lines = ["🔎 <b>شرکت‌کنندگان دعوت‌شده توسط شما</b>",
             f"🎁 جایزه: {html.escape(str(l.get('reward','بدون جایزه')))}",
             f"👥 تعداد دعوت‌شده: {total} نفر",
             f"🎟️ تعداد تیکت‌ها: {tickets}", ""]
    for uid in referred:
        try:
            u = await bot.get_chat(uid)
            name = f"@{u.username}" if u.username else (u.first_name or str(uid))
        except Exception:
            name = "کاربر"
        lines.append(f"{html.escape(name)} 🎟️ 1")
    if details:
        lines = ["🔎 <b>کاربران دعوت‌شده توسط شما</b>", ""]
        detail_total = 0
        for uid in referred:
            try:
                u = await bot.get_chat(uid)
                name = f"@{u.username}" if u.username else (u.first_name or str(uid))
            except Exception:
                name = "کاربر"
            lines += [f"👤 {html.escape(name)}", "🎟️ تیکت‌های دریافت‌شده: 1", ""]
            detail_total += 1
        lines.append(f"🎟️ مجموع تیکت‌های رفرال: {detail_total}")
    kb = KB(row_width=1)
    if not details:
        kb.add(colored_button("🔵 مشاهده جزئیات", f"my_ref_details_{lottery_id}", "primary"))
    kb.add(colored_button("↩️ بازگشت", f"lottery_back_{lottery_id}", "danger"))
    await send_menu(user_id, "\n".join(lines), kb.markup())


async def complete_lottery_participation(user_id, lottery_id, referrer_id=None):
    """Register a successful participant and synchronize every channel post."""
    lottery = lotteries.get(lottery_id)
    if not lottery or not lottery.get("active", False):
        await tg_send(user_id, get_text(user_id, "lottery_ended"))
        return False
    if user_id in lottery.get("participants", []):
        await tg_send(user_id, get_text(user_id, "already_joined"))
        return False
    if lottery.get("capacity") != "unlimited":
        try:
            capacity = int(lottery.get("capacity", 0) or 0)
        except (TypeError, ValueError):
            capacity = 0
        if capacity > 0 and len(lottery.get("participants", [])) >= capacity:
            await tg_send(user_id, get_text(user_id, "full_capacity"))
            return False

    lottery["participants"].append(user_id)
    capacity_reached = (
        lottery.get("capacity") != "unlimited"
        and len(lottery.get("participants", []) or []) >= int(lottery.get("capacity", 0) or 0)
    )
    if referrer_id and referrer_id != user_id:
        refs = lottery.setdefault("referrals", {})
        referred_by = lottery.setdefault("referred_by", {})
        lottery.setdefault("referred_users", referred_by)
        ref_key = str(user_id)
        if ref_key not in referred_by:
            max_ref = lottery.get("max_ref", "unlimited")
            current_ref_count = int(refs.get(str(referrer_id), refs.get(referrer_id, 0)) or 0)
            if max_ref == "unlimited" or current_ref_count < int(max_ref):
                refs[str(referrer_id)] = current_ref_count + 1
                referred_by[ref_key] = str(referrer_id)
                try:
                    new_user = await bot.get_chat(user_id)
                    new_user_name = f"@{new_user.username}" if new_user.username else new_user.first_name
                    await tg_send(referrer_id, f"✨ کاربر {new_user_name} با لینک رفرال شما در {lottery['name']} شرکت کرد!\n✨ تعداد رفرال موفق: {refs[str(referrer_id)]}")
                except Exception:
                    pass

    tickets = get_user_tickets(user_id, lottery_id)
    ref_link = get_referral_link(lottery_id, user_id)
    await tg_send(user_id, get_text(user_id, "joined_success", name=lottery["name"], ref_link=ref_link, tickets=tickets))
    card_text, card_keyboard = show_lottery_card(user_id, lottery, show_join_button=False)
    await tg_send(user_id, card_text, reply_markup=card_keyboard)
    await update_lottery_channel_posts(lottery_id)
    save_data()

    # وقتی ظرفیت دقیقاً پر شد، قرعه‌کشی بدون صبر کردن تا پایان زمان خاتمه پیدا می‌کند
    # و همان‌جا فرآیند انتخاب و ارسال نتایج برای شرکت‌کنندگان/کانال‌ها انجام می‌شود.
    if capacity_reached and lottery.get("active", False):
        await end_lottery(
            lottery_id,
            timer_version=lottery.get("timer_version", 0),
            forced=True,
        )
    return True


async def _dispatch_callback(call: CallbackQuery):
    user_id = call.from_user.id
    data = call.data
    track_user(user_id)

    blocked_seconds = is_blocked(user_id)
    if blocked_seconds:
        await tg_answer(call, get_text(user_id, "still_blocked", seconds=blocked_seconds))
        return True

    if data == "admin_panel":
        if not is_admin(user_id):
            return True
        await send_menu(
            user_id,
            f"👑 <b>پنل مدیریت {BOT_DISPLAY_NAME}</b>\n\nبخش موردنظر را انتخاب کنید:",
            get_admin_keyboard(user_id),
        )
        return True

    if data == "admin_manage_giveaways":
        if not is_admin(user_id):
            return True
        await show_admin_giveaway_list(user_id, 0)
        return True

    if data.startswith("admin_gw_page_"):
        if not is_admin(user_id):
            return True
        try:
            page = int(data.replace("admin_gw_page_", "", 1))
        except ValueError:
            page = 0
        await show_admin_giveaway_list(user_id, page)
        return True

    if data.startswith("admin_gw_open_"):
        if not is_admin(user_id):
            return True
        await show_admin_giveaway(user_id, data.replace("admin_gw_open_", "", 1))
        return True

    if data.startswith("admin_edit_menu_"):
        if not is_admin(user_id):
            return True
        await show_admin_edit_menu(user_id, data.replace("admin_edit_menu_", "", 1))
        return True

    if data.startswith("admin_edit_"):
        if not is_admin(user_id):
            return True
        parts = data.split("_", 3)
        if len(parts) == 4:
            lottery_id, field = parts[2], parts[3]
            if lottery_id in lotteries:
                if field == "access":
                    kb = KB(row_width=1)
                    for cb, label in (("bot_users", "🤖 کاربران بات"), ("premium", "💎 پرمیوم‌دار"), ("all", "👥 همه کاربران")):
                        kb.add(colored_button(label, f"admin_access_{lottery_id}_{cb}", "primary"))
                    kb.add(colored_button("↩️ بازگشت", f"admin_edit_menu_{lottery_id}", "danger"))
                    await send_menu(user_id, "🔐 نوع دسترسی را انتخاب کنید:", kb.markup())
                elif field == "referral":
                    kb = KB(row_width=2)
                    kb.add(colored_button("✅ فعال", f"admin_ref_{lottery_id}_1", "success"), colored_button("❌ غیرفعال", f"admin_ref_{lottery_id}_0", "danger"))
                    kb.add(colored_button("↩️ بازگشت", f"admin_edit_menu_{lottery_id}", "danger"))
                    await send_menu(user_id, "🎟️ وضعیت رفرال را انتخاب کنید:", kb.markup())
                else:
                    user_data[user_id] = {"step": "admin_edit_value", "lottery_id": lottery_id, "field": field}
                    prompts = {"name":"نام جدید را ارسال کنید:","reward":"جایزه جدید را ارسال کنید:","duration":"مدت جدید را به عدد وارد کنید (واحد فعلی حفظ می‌شود):","capacity":"ظرفیت جدید را وارد کنید (یا unlimited):","winners":"تعداد برنده جدید را وارد کنید:","min_participants":"حداقل شرکت‌کننده جدید را وارد کنید:"}
                    await tg_send(user_id, "✏️ " + prompts.get(field, "مقدار جدید را ارسال کنید:") + "\nبرای لغو /cancel")
        return True

    if data.startswith("admin_access_"):
        if not is_admin(user_id): return True
        _, _, lottery_id, access = data.split("_", 3)
        if lottery_id in lotteries:
            lotteries[lottery_id]["access"] = {"bot_users":"bot_users","premium":"premium","all":"all"}.get(access, "all")
            save_data(); await show_admin_edit_menu(user_id, lottery_id)
        return True

    if data.startswith("admin_ref_") and not data.startswith("admin_ref_set_"):
        if not is_admin(user_id): return True
        _, _, lottery_id, val = data.split("_", 3)
        if lottery_id in lotteries:
            lotteries[lottery_id]["referral"] = val == "1"
            save_data(); await show_admin_edit_menu(user_id, lottery_id)
        return True

    if data.startswith("admin_pause_"):
        if not is_admin(user_id): return True
        lid = data.replace("admin_pause_", "", 1)
        l = lotteries.get(lid)
        if l and l.get("active"):
            l["paused_remaining"] = max(0, l.get("end_time", time.time()) - time.time())
            l["active"] = False; l["paused"] = True; l["timer_version"] = int(l.get("timer_version",0))+1
            save_data(); await update_lottery_channel_posts(lid); await show_admin_giveaway(user_id, lid)
        return True

    if data.startswith("admin_resume_"):
        if not is_admin(user_id): return True
        lid = data.replace("admin_resume_", "", 1); l = lotteries.get(lid)
        if l:
            remaining = float(l.get("paused_remaining", 0) or 0)
            if remaining <= 0: remaining = 300
            l["end_time"] = time.time() + remaining; l["active"] = True; l["paused"] = False; l["timer_version"] = int(l.get("timer_version",0))+1
            save_data(); schedule_lottery_end(lid); await update_lottery_channel_posts(lid); await show_admin_giveaway(user_id, lid)
        return True

    if data.startswith("admin_delete_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_delete_", "", 1)
        if lid in lotteries:
            kb=KB(row_width=2); kb.add(colored_button("❌ لغو", f"admin_gw_open_{lid}", "primary"), colored_button("✅ حذف", f"admin_confirm_delete_{lid}", "danger"))
            await tg_send(user_id, "⚠️ آیا مطمئن هستید که می‌خواهید این Giveaway حذف شود؟", reply_markup=kb.markup())
        return True

    if data.startswith("admin_confirm_delete_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_confirm_delete_", "", 1)
        if lid in lotteries:
            del lotteries[lid]; save_data(); await tg_send(user_id, "✅ Giveaway با موفقیت حذف شد."); await show_admin_giveaway_list(user_id)
        return True

    if data.startswith("admin_channels_"):
        if not is_admin(user_id): return True
        await show_admin_channels(user_id, data.replace("admin_channels_", "", 1)); return True

    if data.startswith("admin_ch_add_") or data.startswith("admin_ch_remove_"):
        if not is_admin(user_id): return True
        parts=data.split("_"); lid=parts[3]; typ=parts[4]
        action="add" if data.startswith("admin_ch_add_") else "remove"
        user_data[user_id]={"step":f"admin_channel_{action}","lottery_id":lid,"channel_type":typ}
        await tg_send(user_id, ("➕ کانال را ارسال کنید:" if action=="add" else "🗑️ کانالی که باید حذف شود را ارسال کنید:")+"\nبرای لغو /cancel")
        return True

    if data.startswith("admin_participants_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_participants_", "", 1); l=lotteries.get(lid)
        if l:
            lines=[]
            for i,pid in enumerate(l.get("participants",[]) or [],1):
                try:
                    u=await bot.get_chat(int(pid)); name=f"@{u.username}" if u.username else (u.first_name or str(pid))
                except Exception: name=str(pid)
                lines.append(f"{i}. {html.escape(name)} — 🆔 {pid} — 🎟️ {get_user_tickets(pid,lid)}")
            text="👥 <b>شرکت‌کنندگان</b>\n\n"+("\n".join(lines) if lines else "هنوز شرکت‌کننده‌ای نیست.")
            kb=KB(row_width=1); kb.add(colored_button("↩️ بازگشت", f"admin_gw_open_{lid}", "danger")); await send_menu(user_id,text,kb.markup())
        return True

    if data.startswith("admin_winner_") and not data.startswith("admin_winner_set_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_winner_", "", 1); l=lotteries.get(lid)
        if l:
            winners=l.get("winners_list",[]) or []
            names=[]
            for pid in winners:
                try:
                    u=await bot.get_chat(int(pid)); names.append(f"@{u.username}" if u.username else (u.first_name or str(pid)))
                except: names.append(str(pid))
            kb=KB(row_width=1); kb.add(colored_button("🏆 تغییر برنده", f"admin_winner_set_{lid}", "primary"), colored_button("↩️ بازگشت", f"admin_gw_open_{lid}", "danger"))
            await send_menu(user_id, "🏆 <b>برنده فعلی:</b> "+(", ".join(names) if names else "تعیین نشده")+"\n\nبرای تغییر، شناسه عددی/یوزرنیم برنده را بفرستید. برای چند برنده با کاما جدا کنید.", kb.markup())
        return True

    if data.startswith("admin_winner_set_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_winner_set_", "", 1); user_data[user_id]={"step":"admin_winner_set","lottery_id":lid}; await tg_send(user_id,"🏆 شناسه عددی یا @username برنده(ها) را ارسال کنید:\nبرای لغو /cancel"); return True

    if data.startswith("admin_referrals_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_referrals_", "", 1); l=lotteries.get(lid)
        if l:
            refs=l.get("referrals",{}) or {}; lines=["🎟️ <b>مدیریت رفرال</b>",""]
            for uid,c in sorted(refs.items(), key=lambda x:int(x[1] or 0), reverse=True):
                try:
                    u=await bot.get_chat(int(uid))
                    name=f"@{u.username}" if u.username else (u.first_name or "کاربر")
                except Exception:
                    name="کاربر"
                lines.append(f"{html.escape(name)} — {int(c or 0)} رفرال — 🎟️ {get_user_tickets(int(uid),lid)}")
            kb=KB(row_width=1); kb.add(colored_button("✏️ تغییر تعداد رفرال", f"admin_ref_set_{lid}", "primary"), colored_button("↩️ بازگشت", f"admin_gw_open_{lid}", "danger")); await send_menu(user_id,"\n".join(lines),kb.markup())
        return True

    if data.startswith("admin_ref_set_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_ref_set_", "", 1)
        if lid in lotteries:
            user_data[user_id]={"step":"admin_ref_set","lottery_id":lid}
            await tg_send(user_id,"✏️ به شکل زیر ارسال کن: user_id,count\nمثال: 123456789,10\nبرای لغو /cancel")
        return True

    if data.startswith("admin_stats_"):
        if not is_admin(user_id): return True
        lid=data.replace("admin_stats_", "", 1); l=lotteries.get(lid)
        if l:
            participants=len(l.get("participants",[]) or []); refs=sum(int(v or 0) for v in (l.get("referrals",{}) or {}).values()); tickets=sum(get_user_tickets(int(p),lid) for p in l.get("participants",[]) or [])
            text=f"📊 <b>آمار گیواوی</b>\n\n👥 شرکت‌کنندگان: {participants}\n🎟️ کل تیکت‌ها: {tickets}\n🎟️ رفرال موفق: {refs}\n🏆 تعداد برنده: {len(l.get('winners_list',[]) or [])}\n📣 کانال انتشار: {len(l.get('channels',[]) or [])}\n🔒 کانال اجباری: {len(l.get('mandatory_channels',[]) or [])}"
            kb=KB(row_width=1); kb.add(colored_button("↩️ بازگشت", f"admin_gw_open_{lid}", "danger")); await send_menu(user_id,text,kb.markup())
        return True

    if data == "admin_users_stats":
        if not is_admin(user_id):
            return True
        text = get_text(
            user_id, "admin_users_stats", users=len(all_users), lotteries=len(lotteries),
            active=len([l for l in lotteries.values() if l.get("active", False)]),
        )
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "admin_panel", "danger"))
        await send_menu(user_id, text, kb.markup())
        return True

    if data == "admin_lottery_stats":
        if not is_admin(user_id):
            return True
        if not lotteries:
            text = "✨ هیچ گیوایی ساخته نشده است!"
        else:
            list_text = ""
            for i, (lid, lottery) in enumerate(lotteries.items(), 1):
                status = "✨ فعال" if lottery.get("active") and lottery.get("end_time", 0) > time.time() else "✨ غیرفعال"
                list_text += f"{i}. {lottery.get('name', 'بدون نام')}\n"
                list_text += f"   {get_participant_count(lid)} شرکت‌کننده | {status}\n"
                list_text += f"   {link_anchor(get_lottery_link(lid), 'مشاهده')}\n\n"
            text = list_text
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "admin_panel", "danger"))
        await send_menu(user_id, text, kb.markup())
        return True

    if data.startswith("mandatory_channels_"):
        lottery_id = data.replace("mandatory_channels_", "")
        lottery = lotteries.get(lottery_id)
        if not lottery:
            await tg_answer(call, "✨ قرعه‌کشی پیدا نشد.")
            return True
        if lottery.get("creator_id") != user_id and not is_admin(user_id):
            await tg_answer(call, "✨ فقط سازنده یا ادمین مجاز اجازه مدیریت این بخش را دارد.")
            return True
        await show_mandatory_channels(user_id, lottery_id)
        return True

    if data.startswith("mandatory_add_"):
        lottery_id = data.replace("mandatory_add_", "")
        lottery = lotteries.get(lottery_id)
        if not lottery or (lottery.get("creator_id") != user_id and not is_admin(user_id)):
            await tg_answer(call, "✨ دسترسی ندارید.")
            return True
        user_data[user_id] = {"step": "mandatory_add", "lottery_id": lottery_id}
        await tg_send(user_id, "<b>✨ آیدی کانال را ارسال کنید.\n\nمثال: @channel یا -1001234567890</b>\n\nبرای لغو /cancel")
        return True

    if data.startswith("mandatory_remove_"):
        lottery_id = data.replace("mandatory_remove_", "")
        lottery = lotteries.get(lottery_id)
        if not lottery or (lottery.get("creator_id") != user_id and not is_admin(user_id)):
            await tg_answer(call, "✨ دسترسی ندارید.")
            return True
        user_data[user_id] = {"step": "mandatory_remove", "lottery_id": lottery_id}
        await tg_send(user_id, "<b>✨ آیدی کانالی که می‌خواهید حذف شود را ارسال کنید.\n\nمثال: @channel یا -1001234567890</b>\n\nبرای لغو /cancel")
        return True

    if data == "broadcast":
        if not is_admin(user_id):
            return True
        await tg_send(user_id, "📢 <b>فوروارد همگانی</b>\n\nپیام موردنظر را از کانال فوروارد کنید.\nبرای لغو /cancel")
        user_data[user_id] = {"step": "broadcast_forward"}
        return True

    if data == "broadcast_cancel":
        if not is_admin(user_id):
            return True
        user_data.pop(user_id, None)
        await send_menu(user_id, "❌ فوروارد همگانی لغو شد.", get_admin_keyboard(user_id))
        return True

    if data == "broadcast_confirm":
        if not is_admin(user_id):
            return True
        state = user_data.get(user_id, {})
        if state.get("step") != "broadcast_confirm":
            return True
        source_chat_id = state.get("source_chat_id")
        source_message_id = state.get("source_message_id")
        success = fail = 0
        for uid in list(all_users):
            try:
                await bot.copy_message(chat_id=uid, from_chat_id=source_chat_id, message_id=source_message_id)
                success += 1
                await asyncio.sleep(0.03)
            except Exception:
                fail += 1
        user_data.pop(user_id, None)
        await tg_send(user_id, f"✅ <b>ارسال با موفقیت انجام شد</b>\n👥 کل کاربران: {len(all_users):,}\n✅ موفق: {success:,}\n❌ ناموفق: {fail:,}")
        await send_menu(user_id, "✨ پنل ادمین", get_admin_keyboard(user_id))
        return True

    if data == "guide":
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, get_text(user_id, "guide_text"), kb.markup())
        return True

    if data == "top_winners":
        await show_top_winners(user_id)
        return True

    if data == "profile":
        text, keyboard = await show_profile(user_id)
        await send_menu(user_id, text, keyboard)
        return True

    if data == "about_bot":
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, get_text(user_id, "about_bot_text"), kb.markup())
        return True

    if data == "manage_lottery":
        user_lotteries = [l for l in lotteries.values() if l.get("creator_id") == user_id]
        if not user_lotteries:
            await tg_send(user_id, get_text(user_id, "no_manage"))
            return True
        kb = KB(row_width=1)
        for l in user_lotteries:
            kb.add(colored_button(f"✨ {l['name']} - {get_participant_count(l['id'])} شرکت‌کننده", f"manage_{l['id']}", "primary"))
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, get_text(user_id, "manage_title"), kb.markup())
        return True

    if data.startswith("manage_"):
        lottery_id = data.replace("manage_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            lottery = lotteries[lottery_id]
            kb = KB(row_width=1)
            if lottery.get("active"):
                kb.add(colored_button("✨ ارسال در کانال", f"send_channel_{lottery_id}", "primary"))
                kb.add(colored_button(get_button_text(user_id, "early_end"), f"end_{lottery_id}", "danger"))
                kb.add(colored_button(get_button_text(user_id, "message_participants"), f"msg_participants_{lottery_id}", "primary"))
                kb.add(colored_button(get_button_text(user_id, "change_time"), f"change_time_{lottery_id}", "primary"))
            kb.add(colored_button(get_button_text(user_id, "list_participants"), f"list_{lottery_id}", "primary"))
            if lottery.get("referral", False):
                kb.add(colored_button("🎟️ لیست رفرال", f"creator_referrals_{lottery_id}", "success"))
            kb.add(colored_button("✨ کانال اجباری", f"mandatory_channels_{lottery_id}", "primary"))
            kb.add(colored_button(get_button_text(user_id, "delete_lottery"), f"delete_{lottery_id}", "danger"))
            kb.add(colored_button(get_button_text(user_id, "back"), "manage_lottery", "danger"))
            card_text, _ = show_lottery_card(user_id, lottery, show_join_button=False, show_back=False)
            text = get_text(user_id, "manage_lottery_title", name=lottery["name"], card=card_text)
            await send_menu(user_id, text, kb.markup())
        return True

    if data.startswith("creator_referrals_"):
        lottery_id = data.replace("creator_referrals_", "", 1)
        if lottery_id not in lotteries or not can_manage_lottery(user_id, lottery_id):
            return True
        lottery = lotteries[lottery_id]
        refs = lottery.get("referrals", {}) or {}
        if not refs:
            text = "🎟️ <b>لیست رفرال</b>\n\nهنوز هیچ رفرالی برای این قرعه‌کشی ثبت نشده است."
        else:
            items = []
            for uid, count in refs.items():
                try:
                    items.append((int(count or 0), int(uid)))
                except (ValueError, TypeError):
                    continue
            items.sort(key=lambda x: x[0], reverse=True)
            lines = [
                "🎟️ <b>لیست رفرال</b>",
                f"🎁 قرعه‌کشی: {html.escape(str(lottery.get('name', 'بدون نام')))}",
                "",
            ]
            for count, uid in items:
                try:
                    u = await bot.get_chat(uid)
                    display_name = f"@{u.username}" if getattr(u, "username", None) else (getattr(u, "first_name", None) or str(uid))
                except Exception:
                    display_name = str(uid)
                lines.append(f"👤 <b>{html.escape(display_name)}</b> — 🎟️ {count} رفرال")
            text = "\n".join(lines)
        kb = KB(row_width=1)
        kb.add(colored_button("↩️ بازگشت", f"manage_{lottery_id}", "danger"))
        await send_menu(user_id, text, kb.markup())
        return True

    if data.startswith("end_"):
        lottery_id = data.replace("end_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            if not lotteries[lottery_id].get("active", False):
                await tg_answer(call, "✨ این قرعه‌کشی قبلاً پایان یافته است")
                return True
            lotteries[lottery_id]["active"] = False
            save_data()
            await end_lottery(lottery_id, lotteries[lottery_id].get("timer_version", 0), forced=True)
            await tg_answer(call, "✨ قرعه‌کشی پایان یافت")
        return True

    if data.startswith("list_"):
        lottery_id = data.replace("list_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            lottery = lotteries[lottery_id]
            participants = lottery.get("participants", [])
            if participants:
                list_text = ""
                for i, p in enumerate(participants, 1):
                    try:
                        u = await bot.get_chat(p)
                        name = f"@{u.username}" if u.username else u.first_name
                        tickets = get_user_tickets(p, lottery_id)
                        list_text += f"{i}. {name} (✨ {tickets} تیکت)\n"
                    except Exception:
                        list_text += f"{i}. کاربر {p}\n"
                text = get_text(user_id, "participants_list", count=len(participants), list=list_text)
            else:
                text = get_text(user_id, "no_participants")
            kb = KB(row_width=1)
            kb.add(colored_button(get_button_text(user_id, "back"), f"manage_{lottery_id}", "danger"))
            await send_menu(user_id, text, kb.markup())
        return True

    if data.startswith("delete_"):
        lottery_id = data.replace("delete_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            kb = KB(row_width=2)
            kb.add(
                colored_button(get_button_text(user_id, "yes_delete"), f"confirm_delete_{lottery_id}", "primary"),
                colored_button(get_button_text(user_id, "no_delete"), f"manage_{lottery_id}", "danger"),
            )
            await tg_send(user_id, get_text(user_id, "confirm_delete"), reply_markup=kb.markup())
        return True

    if data.startswith("confirm_delete_"):
        lottery_id = data.replace("confirm_delete_", "")
        if data.startswith("confirm_delete_") and lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            del lotteries[lottery_id]
            save_data()
            await tg_send(user_id, get_text(user_id, "deleted"))
        return True

    if data == "new_lottery":
        user_data[user_id] = {"step": 1, "data": {}}
        await send_menu(user_id, get_text(user_id, "step1"), get_cancel_back_keyboard(user_id, show_back=False))
        return True

    if data.startswith("participants_"):
        lottery_id = data.replace("participants_", "")
        if lottery_id in lotteries:
            await tg_answer(call, f"✨ {get_participant_count(lottery_id)} نفر شرکت کرده‌اند")
        return True

    if data.startswith("msg_participants_"):
        lottery_id = data.replace("msg_participants_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            user_data[user_id] = {"step": "participant_broadcast", "lottery_id": lottery_id}
            await tg_send(user_id, "✨ پیام موردنظرت را برای شرکت‌کنندگان بفرست.\nبرای لغو /cancel")
        return True

    if data.startswith("change_time_"):
        lottery_id = data.replace("change_time_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            kb = KB(row_width=4)
            for delta in (5, 10, 30, 60):
                kb.add(colored_button(f"✨ +{delta}", f"time_add_{lottery_id}_{delta}", "primary"))
            for delta in (5, 10, 30, 60):
                kb.add(colored_button(f"✨ -{delta}", f"time_sub_{lottery_id}_{delta}", "danger"))
            kb.add(colored_button(get_button_text(user_id, "back"), f"manage_{lottery_id}", "danger"))
            await tg_send(user_id, "✨ مقدار تغییر زمان را انتخاب کن (دقیقه):", reply_markup=kb.markup())
        return True

    if data.startswith("time_add_") or data.startswith("time_sub_"):
        parts = data.split("_")
        if len(parts) == 4:
            lottery_id, delta = parts[2], int(parts[3])
            if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
                lottery = lotteries[lottery_id]
                seconds = delta * 60 if data.startswith("time_add_") else -delta * 60
                new_end = max(time.time() + 10, lottery.get("end_time", time.time()) + seconds)
                lottery["end_time"] = new_end
                lottery["timer_version"] = int(lottery.get("timer_version", 0)) + 1
                save_data()
                schedule_lottery_end(lottery_id)
                await tg_send(user_id, f"✨ زمان قرعه‌کشی تغییر کرد.\n✨ پایان: {time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(new_end))}")
        return True

    if data.startswith("my_ref_list_"):
        lid=data.replace("my_ref_list_", "", 1)
        lottery = lotteries.get(lid)
        if not lottery or str(user_id) not in {str(x) for x in (lottery.get("participants", []) or [])}:
            await tg_answer(call, "❌ ابتدا در همین گیواوی شرکت کنید.")
            return True
        await show_user_referrals(user_id,lid,details=False); return True

    if data.startswith("my_ref_details_"):
        lid=data.replace("my_ref_details_", "", 1)
        lottery = lotteries.get(lid)
        if not lottery or str(user_id) not in {str(x) for x in (lottery.get("participants", []) or [])}:
            await tg_answer(call, "❌ ابتدا در همین گیواوی شرکت کنید.")
            return True
        await show_user_referrals(user_id,lid,details=True); return True

    if data.startswith("lottery_back_"):
        lid=data.replace("lottery_back_", "", 1)
        if lid in lotteries:
            l=lotteries[lid]; text,keyboard=show_lottery_card(user_id,l,show_join_button=bool(l.get("active",False)),show_back=True); await send_menu(user_id,text,keyboard)
        return True

    if data.startswith("my_ref_"):
        lottery_id = data.replace("my_ref_", "")
        if lottery_id in lotteries:
            ref_link = get_referral_link(lottery_id, user_id)
            ref_count = get_referral_count(user_id, lottery_id)
            tickets = get_user_tickets(user_id, lottery_id)
            text = (
                f"<b>✨ لینک رفرال اختصاصی شما</b>\n\n{ref_link}\n\n"
                f"✨ دعوت موفق: {ref_count} نفر\n✨ تیکت‌های شما: {tickets}"
            )
            await tg_send(user_id, text)
        return True

    if data.startswith("send_channel_"):
        lottery_id = data.replace("send_channel_", "")
        if lottery_id in lotteries and can_manage_lottery(user_id, lottery_id):
            user_data.setdefault(user_id, {})["send_lottery_id"] = lottery_id
            user_data[user_id]["step"] = "send_menu"
            kb = KB(row_width=1)
            kb.add(
                colored_button("✨ تنظیم متن", "edit_text", "primary"),
                colored_button("✨ ارسال با متن پیشفرض", "send_default", "primary"),
                colored_button("✨ بازگشت", "back_to_menu", "danger"),
            )
            await tg_send(user_id, "✨ انتشار گیواوی", reply_markup=kb.markup())
        return True

    if data == "edit_text":
        await tg_send(user_id, "✨ متن دلخواه یا عکس + کپشن را بفرست.")
        user_data.setdefault(user_id, {})["step"] = "waiting_custom_text"
        return True

    if data == "send_default":
        lottery_id = user_data.get(user_id, {}).get("send_lottery_id")
        if lottery_id and lottery_id in lotteries:
            success_count, failed_count, failed_details = await send_lottery_to_channels(lottery_id)
            report = f"✅ با موفقیت ارسال شد!\n\n📣 موفق: {success_count} کانال\n❌ ناموفق: {failed_count} کانال"
            if failed_details:
                lines = "\n".join(f"• {ch}: {reason}" for ch, reason in failed_details[:10])
                report += f"\n\n{lines}"
            await tg_send(user_id, report)
        else:
            await tg_send(user_id, "✨ خطا: قرعه‌کشی یافت نشد!")
        if user_id in user_data:
            del user_data[user_id]
        await send_menu(user_id, get_text(user_id, "welcome", name=call.from_user.first_name), get_main_keyboard(user_id))
        return True

    if data == "support":
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, get_text(user_id, "support_text"), kb.markup())
        return True

    if data == "language":
        kb = KB(row_width=1)
        kb.add(
            colored_button("✨ فارسی", "set_lang_fa", "primary"),
            colored_button("✨ English", "set_lang_en", "primary"),
            colored_button("✨ Русский", "set_lang_ru", "primary"),
            colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"),
        )
        await send_menu(user_id, get_text(user_id, "select_language"), kb.markup())
        return True

    if data.startswith("set_lang_"):
        new_lang = data.split("_")[2]
        user_languages[user_id] = new_lang
        save_data()
        await send_menu(user_id, get_text(user_id, "welcome", name=call.from_user.first_name), get_main_keyboard(user_id))
        return True

    if data == "active_lotteries":
        active = [l for l in lotteries.values() if l.get("active", False) and l.get("end_time", time.time() + 300) - time.time() > 0]
        if active:
            list_text = ""
            for l in active:
                remaining = l.get("end_time", time.time() + 300) - time.time()
                minutes = int((remaining % 3600) // 60)
                hours = int((remaining % 86400) // 3600)
                days = int(remaining // 86400)
                if days > 0:
                    time_text = f"{days} روز و {hours} ساعت"
                elif hours > 0:
                    time_text = f"{hours} ساعت و {minutes} دقیقه"
                else:
                    time_text = f"{minutes} دقیقه"
                capacity_text = "✨ نامحدود" if l.get("capacity") == "unlimited" else str(l.get("capacity", "✨"))
                list_text += f"{link_anchor(get_lottery_link(l['id']), l['name'])}\n\n"
                list_text += f"<blockquote>✨ جایزه: {l.get('reward', 'بدون جایزه')}\n✨ شرکت‌کنندگان: {get_participant_count(l['id'])}\n✨ ظرفیت: {capacity_text}\n✨ زمان باقی‌مانده: {time_text}</blockquote>\n\n"
                list_text += "─" * 20 + "\n\n"
            text = get_text(user_id, "active_list", list=list_text)
        else:
            text = get_text(user_id, "no_active")
        kb = KB(row_width=1)
        kb.add(colored_button(get_button_text(user_id, "back"), "back_to_menu", "danger"))
        await send_menu(user_id, text, kb.markup())
        return True

    if data == "back_to_menu":
        text = get_text(user_id, "welcome", name=call.from_user.first_name)
        if user_id in user_data:
            del user_data[user_id]
        await send_menu(user_id, text, get_main_keyboard(user_id))
        return True

    if data.startswith("check_join_") or data.startswith("join_"):
        lottery_id = data.replace("check_join_", "", 1) if data.startswith("check_join_") else data.replace("join_", "", 1)
        if lottery_id not in lotteries:
            await tg_answer(call, get_text(user_id, "not_found"))
            return True
        lottery = lotteries[lottery_id]
        if not lottery.get("active", False):
            await tg_answer(call, get_text(user_id, "lottery_ended"))
            return True
        if user_id in lottery.get("participants", []):
            await tg_answer(call, get_text(user_id, "already_joined"))
            return True
        if lottery.get("capacity") != "unlimited" and len(lottery.get("participants", [])) >= lottery["capacity"]:
            await tg_answer(call, get_text(user_id, "full_capacity"))
            return True

        mandatory_ok, mandatory_not_joined = await check_lottery_mandatory_channels(user_id, lottery)
        if not mandatory_ok:
            kb = KB(row_width=1)
            for ch in mandatory_not_joined:
                display = extract_channel_username(ch) or ch
                url = ch if str(ch).startswith("http") else (f"https://t.me/{display}" if not str(display).startswith("@") else f"https://t.me/{display[1:]}")
                kb.add(colored_button(f"✨ {display}", None, "primary", url=url))
            kb.add(colored_button("✨ عضو شدم، دوباره امتحان کن", f"check_join_{lottery_id}", "primary"))
            kb.add(colored_button("✨ بازگشت", "back_to_menu", "danger"))
            await tg_send(user_id, "<b>✨ برای شرکت باید عضو کانال‌های اجباری این قرعه‌کشی باشید.</b>", reply_markup=kb.markup(), disable_web_page_preview=True)
            return True

        channels = lottery.get("channels", [])
        if channels:
            not_joined, channel_links = [], []
            for channel in channels:
                username = extract_channel_username(channel)
                is_member = True
                if username:
                    is_member, _ = await check_channel_membership(user_id, username)
                if not is_member:
                    not_joined.append(channel)
                    channel_links.append(channel)
            if not_joined:
                kb = KB(row_width=2)
                for ch_link in channel_links:
                    display = extract_channel_username(ch_link) or ch_link
                    url = ch_link if ch_link.startswith("http") else f"https://t.me/{display}"
                    kb.add(colored_button(f"✨ {display}", None, "primary", url=url))
                kb.add(colored_button("✨ جوین شدم، دوباره امتحان کن", f"check_join_{lottery_id}", "primary"))
                kb.add(colored_button("✨ بازگشت", "back_to_menu", "danger"))
                text_message = "✨ شرط عضویت در کانال‌ها\n\nبرای شرکت باید عضو کانال‌های زیر باشید."
                await tg_send(user_id, text_message, reply_markup=kb.markup(), disable_web_page_preview=True)
                return True

        referrer_id = None
        pending = user_data.get(user_id, {}).get("pending_ref")
        if pending and pending.get("lottery_id") == lottery_id:
            referrer_id = pending.get("referrer_id")
            del user_data[user_id]["pending_ref"]
            if not user_data[user_id]:
                del user_data[user_id]

        # Optional security question. Disabled means channel membership is enough.
        if lottery.get("captcha_enabled", False):
            num1, num2 = random.randint(1, 50), random.randint(1, 50)
            operator = random.choice(["+", "-"])
            if operator == "-" and num1 < num2:
                num1, num2 = num2, num1
            answer = num1 + num2 if operator == "+" else num1 - num2
            token = f"{user_id}-{time.time()}"
            user_math[user_id] = {"lottery_id": lottery_id, "answer": answer, "num1": num1, "num2": num2, "operator": operator, "referrer_id": referrer_id, "token": token}
            schedule_math_timeout(user_id, token)
            await tg_send(user_id, get_text(user_id, "math_question", question=f"{num1} {operator} {num2}"))
            return True

        await complete_lottery_participation(user_id, lottery_id, referrer_id)
        return True

    if data in PRIZE_CALLBACKS:
        if user_id in user_data and isinstance(user_data[user_id].get("step"), (int, float)):
            udata = user_data[user_id]["data"]
            udata["reward"] = PRIZE_LABELS[data]
            user_data[user_id]["step"] = 10
            await show_step(user_id, 10)
        return True

    if data == PRIZE_MANUAL_CALLBACK:
        if user_id in user_data and isinstance(user_data[user_id].get("step"), (int, float)):
            user_data[user_id]["step"] = "waiting_reward_manual"
            await tg_send(user_id, "✨ <b>نام جایزه دلخواه</b> خودت رو بفرست:\n(برای لغو /cancel)")
        return True

    if user_id not in user_data:
        return False

    step = user_data[user_id]["step"]
    udata = user_data[user_id]["data"]

    if data == "cancel_lottery":
        if user_id in user_data:
            del user_data[user_id]
        await send_menu(user_id, "✨ ساخت قرعه‌کشی لغو شد!", get_main_keyboard(user_id))
        return True

    if data == "step_back":
        if isinstance(step, (int, float)) and step > 1:
            user_data[user_id]["step"] = step - 1
            await show_step(user_id, step - 1)
        return True

    if data.startswith("dur_type_"):
        udata["duration_type"] = data.replace("dur_type_", "")
        await send_menu(user_id, get_text(user_id, "enter_number"))
        return True

    if data.startswith("dur_"):
        duration_map = {"dur_5min": 5, "dur_10min": 10, "dur_30min": 30, "dur_1hour": 60, "dur_2hour": 120, "dur_1day": 1440}
        if data in duration_map:
            udata["duration"] = duration_map[data]
            udata["duration_type"] = "minutes"
            user_data[user_id]["step"] = 3
            await show_step(user_id, 3)
            return True
        if data == "dur_manual":
            kb = KB(row_width=2)
            kb.add(
                colored_button(get_button_text(user_id, "days"), "dur_type_days", "primary"),
                colored_button(get_button_text(user_id, "hours"), "dur_type_hours", "primary"),
                colored_button(get_button_text(user_id, "minutes"), "dur_type_minutes", "primary"),
            )
            kb.add(
                colored_button(get_button_text(user_id, "back_step"), "step_back", "primary"),
                colored_button(get_button_text(user_id, "cancel"), "cancel_lottery", "danger"),
            )
            await send_menu(user_id, "✨ نوع زمان را انتخاب کنید:", kb.markup())
            return True
        return True

    if data.startswith("cap_"):
        capacity_map = {"cap_10": 10, "cap_50": 50, "cap_100": 100, "cap_500": 500, "cap_1000": 1000, "cap_unlimited": "unlimited"}
        if data in capacity_map:
            udata["capacity"] = capacity_map[data]
            user_data[user_id]["step"] = 4
            await show_step(user_id, 4)
        elif data == "cap_manual":
            await send_menu(user_id, get_text(user_id, "enter_number"))
        return True

    if data.startswith("win_"):
        win_map = {"win_1": 1, "win_2": 2, "win_3": 3, "win_5": 5, "win_10": 10}
        if data in win_map:
            udata["winners"] = win_map[data]
            user_data[user_id]["step"] = 5
            await show_step(user_id, 5)
        elif data == "win_manual":
            await send_menu(user_id, get_text(user_id, "enter_number"))
        return True

    if data.startswith("min_"):
        min_map = {"min_0": 0, "min_5": 5, "min_10": 10, "min_20": 20, "min_50": 50}
        if data in min_map:
            udata["min_participants"] = min_map[data]
            user_data[user_id]["step"] = 6
            await show_step(user_id, 6)
        elif data == "min_manual":
            await send_menu(user_id, get_text(user_id, "enter_number"))
        return True

    if data in ("acc_bot", "acc_premium", "acc_all"):
        udata["access"] = {"acc_bot": "bot_users", "acc_premium": "premium", "acc_all": "all"}[data]
        user_data[user_id]["step"] = 7
        await show_step(user_id, 7)
        return True

    if data.startswith("ch_"):
        count = data.split("_")[1]
        if count == "0":
            udata["channels"] = []
            user_data[user_id]["step"] = 8
            await show_step(user_id, 8)
        elif count == "manual":
            udata["channel_manual"] = True
            await send_menu(user_id, "✨ تعداد کانال‌های اسپانسر را وارد کن (۱ تا ۲۰):")
            user_data[user_id]["step"] = "waiting_channel_count_manual"
        else:
            udata["channel_count"] = int(count)
            udata["channels"] = []
            udata["channel_manual"] = False
            await send_menu(user_id, get_text(user_id, "enter_channel", num=1))
        return True

    if data == "ref_remove":
        udata["referral"] = False
        user_data[user_id]["step"] = 9
        await show_step(user_id, 9)
        return True

    if data == "ref_add":
        udata["referral"] = True
        user_data[user_id]["step"] = 8.5
        await show_step(user_id, 8.5)
        return True

    if data == "ref_no_limit":
        udata["max_ref"] = "unlimited"
        user_data[user_id]["step"] = 9
        await show_step(user_id, 9)
        return True

    if data in ("captcha_on", "captcha_off"):
        udata["captcha_enabled"] = data == "captcha_on"
        username = call.from_user.username
        first_name = call.from_user.first_name
        await create_lottery(user_id, username, first_name)
        return True

    return False


@router.callback_query()
async def callback_handler(call: CallbackQuery):
    handled = False
    # پاسخ Callback را فوراً بده تا Spinner تلگرام منتظر عملیات منو نماند.
    await tg_answer(call)
    try:
        handled = await _dispatch_callback(call)
    except Exception as e:
        print(f"callback error [{call.data}]: {e}")
        try:
            await notify_admin(f"خطای Callback: {call.data}\n{e}")
        except Exception:
            pass
    finally:
        await tg_answer(call)
    if not handled:
        print(f"callback_data بدون handler: {call.data}")


@router.message(F.content_type.in_({"text", "photo", "video", "document", "audio", "voice", "animation"}))
async def handle_text_messages(message: Message):
    user_id = message.from_user.id
    text = message.text
    track_user(user_id)

    blocked_seconds = is_blocked(user_id)
    if blocked_seconds:
        await tg_send(user_id, get_text(user_id, "still_blocked", seconds=blocked_seconds))
        return

    if user_id in user_data and user_data[user_id].get("step") == "broadcast":
        if text == "/cancel":
            del user_data[user_id]
            await tg_send(user_id, "✨ ارسال همگانی لغو شد!", reply_markup=get_main_keyboard(user_id))
            return
        await tg_send(user_id, "✨ در حال ارسال پیام...")
        success_count = fail_count = 0
        for uid in list(all_users):
            try:
                await tg_send(uid, text)
                success_count += 1
                await asyncio.sleep(0.05)
            except Exception:
                fail_count += 1
        del user_data[user_id]
        await tg_send(user_id, f"✨ ارسال شد به {success_count} کاربر، ناموفق: {fail_count}", reply_markup=get_main_keyboard(user_id))
        return

    if user_id in user_data and user_data[user_id].get("step") == "admin_edit_value":
        state=user_data[user_id]; lid=state.get("lottery_id"); field=state.get("field"); l=lotteries.get(lid)
        if not is_admin(user_id) or not l: user_data.pop(user_id,None); return
        if text == "/cancel": user_data.pop(user_id,None); await show_admin_edit_menu(user_id,lid); return
        raw=(text or "").strip()
        try:
            if field in ("winners","min_participants","duration"):
                val=int(raw)
                if (field=="winners" and val<1) or (field!="winners" and val<0): raise ValueError
                l[field]=val
            elif field=="capacity":
                l[field] = "unlimited" if raw.lower() in ("unlimited","نامحدود") else max(1,int(raw))
            else:
                l[field]=raw
            save_data(); user_data.pop(user_id,None); await tg_send(user_id,"✅ تغییرات با موفقیت ذخیره شد."); await show_admin_edit_menu(user_id,lid)
        except Exception:
            await tg_send(user_id,"❌ مقدار واردشده معتبر نیست. دوباره تلاش کنید یا /cancel بزنید.")
        return

    if user_id in user_data and user_data[user_id].get("step") in ("admin_channel_add","admin_channel_remove"):
        state=user_data[user_id]; lid=state.get("lottery_id"); typ=state.get("channel_type"); l=lotteries.get(lid)
        if not is_admin(user_id) or not l: user_data.pop(user_id,None); return
        if text=="/cancel": user_data.pop(user_id,None); await show_admin_channels(user_id,lid); return
        ch=(text or "").strip(); target="channels" if typ=="pub" else "mandatory_channels"; arr=l.setdefault(target,[]); key=normalize_channel_ref(ch)
        if state["step"]=="admin_channel_add":
            if not key: await tg_send(user_id,"❌ کانال معتبر نیست."); return
            status=await get_bot_channel_status(ch)
            if not status.get("ok") or not status.get("is_admin"):
                await tg_send(user_id,"❌ ربات باید در این کانال ادمین باشد."); return
            if any(normalize_channel_ref(x)==key for x in arr): await tg_send(user_id,"⚠️ این کانال قبلاً ثبت شده است."); return
            arr.append(ch); register_giveaway_channel(ch); save_data(); await update_lottery_channel_posts(lid); await tg_send(user_id,"✅ کانال اضافه شد.")
        else:
            pos=next((i for i,x in enumerate(arr) if normalize_channel_ref(x)==key),None)
            if pos is None: await tg_send(user_id,"⚠️ این کانال در این بخش ثبت نشده است."); return
            arr.pop(pos); save_data(); await update_lottery_channel_posts(lid); await tg_send(user_id,"✅ کانال حذف شد.")
        user_data.pop(user_id,None); await show_admin_channels(user_id,lid); return

    if user_id in user_data and user_data[user_id].get("step") == "admin_ref_set":
        state=user_data[user_id]; lid=state.get("lottery_id"); l=lotteries.get(lid)
        if not is_admin(user_id) or not l: user_data.pop(user_id,None); return
        if text=="/cancel": user_data.pop(user_id,None); await show_admin_giveaway(user_id,lid); return
        try:
            a,b=(text or "").replace("،",",").split(",",1); pid=int(a.strip()); count=max(0,int(b.strip()))
            if pid not in (l.get("participants",[]) or []): raise ValueError
            l.setdefault("referrals",{})[str(pid)]=count; save_data(); user_data.pop(user_id,None); await tg_send(user_id,"✅ تعداد رفرال تغییر کرد."); await show_admin_giveaway(user_id,lid)
        except Exception: await tg_send(user_id,"❌ فرمت یا کاربر نامعتبر است. مثال: 123456789,10")
        return

    if user_id in user_data and user_data[user_id].get("step") == "admin_winner_set":
        state=user_data[user_id]; lid=state.get("lottery_id"); l=lotteries.get(lid)
        if not is_admin(user_id) or not l: user_data.pop(user_id,None); return
        if text=="/cancel": user_data.pop(user_id,None); await show_admin_giveaway(user_id,lid); return
        ids=[]
        for part in (text or "").replace("،",",").split(","):
            part=part.strip()
            if not part: continue
            try:
                if part.startswith("@"): u=await bot.get_chat(part); pid=int(u.id)
                else: pid=int(part)
                if pid not in (l.get("participants",[]) or []): raise ValueError
                ids.append(pid)
            except Exception: pass
        if not ids: await tg_send(user_id,"❌ فقط شرکت‌کنندگان همین گیواوی می‌توانند برنده شوند."); return
        l["winners_list"]=ids[:max(1,int(l.get("winners",1)))]; save_data(); user_data.pop(user_id,None); await tg_send(user_id,"🏆 برنده(ها) با موفقیت تغییر کرد."); await show_admin_giveaway(user_id,lid); return

    if user_id in user_data and user_data[user_id].get("step") == "broadcast_forward":
        # Forwarded channel message: copy it to every bot user only after confirmation.
        if text == "/cancel": user_data.pop(user_id,None); await send_menu(user_id,"❌ فوروارد همگانی لغو شد.",get_admin_keyboard(user_id)); return
        origin=getattr(message,"forward_origin",None)
        source_chat_id=None; source_message_id=None
        if origin is not None:
            source_message_id=getattr(origin,"message_id",None)
            chat=getattr(origin,"chat",None)
            source_chat_id=getattr(chat,"id",None)
        if source_chat_id is None:
            source_chat_id=getattr(getattr(message,"forward_from_chat",None),"id",None)
            source_message_id=getattr(message,"forward_from_message_id",None)
        if source_chat_id is None or source_message_id is None:
            await tg_send(user_id,"❌ لطفاً پیام را مستقیماً از یک کانال برای ربات Forward کنید."); return
        user_data[user_id]={"step":"broadcast_confirm","source_chat_id":source_chat_id,"source_message_id":source_message_id}
        kb=KB(row_width=2); kb.add(colored_button("❌ لغو", "broadcast_cancel", "danger"), colored_button("✅ تأیید ارسال", "broadcast_confirm", "success"))
        await tg_send(user_id,"❓ آیا این پیام برای همه کاربران ارسال شود؟",reply_markup=kb.markup()); return

    if user_id in user_data and user_data[user_id].get("step") in ("mandatory_add", "mandatory_remove"):
        step_name = user_data[user_id]["step"]
        lottery_id = user_data[user_id].get("lottery_id")
        lottery = lotteries.get(lottery_id)
        if text == "/cancel":
            del user_data[user_id]
            await show_mandatory_channels(user_id, lottery_id)
            return
        if not lottery or (lottery.get("creator_id") != user_id and not is_admin(user_id)):
            del user_data[user_id]
            await tg_send(user_id, "<b>✨ دسترسی ندارید.</b>")
            return
        channel = text.strip()
        if step_name == "mandatory_add":
            ok, error = await check_giveaway_channel_permissions(channel, user_id)
            if not ok:
                await tg_send(user_id, f"<b>✨ کانال قابل ثبت نیست.\n{html.escape(str(error))}</b>")
                return
            channels = lottery.setdefault("mandatory_channels", [])
            key = normalize_channel_ref(channel)
            existing = next((c for c in channels if normalize_channel_ref(c) == key), None)
            if existing:
                await tg_send(user_id, "<b>✨ این کانال قبلاً برای همین قرعه‌کشی ثبت شده است.</b>")
            else:
                channels.append(channel)
                register_giveaway_channel(channel)
                save_data()
                await tg_send(user_id, "<b>✨ کانال با موفقیت به همین قرعه‌کشی اضافه شد.</b>")
        else:
            channels = lottery.setdefault("mandatory_channels", [])
            key = normalize_channel_ref(channel)
            existing_index = next((i for i, c in enumerate(channels) if normalize_channel_ref(c) == key), None)
            if existing_index is not None:
                channels.pop(existing_index)
                save_data()
                await tg_send(user_id, "<b>✨ کانال از همین قرعه‌کشی حذف شد.</b>")
            else:
                sponsor_exists = any(normalize_channel_ref(c) == key for c in (lottery.get("channels", []) or []))
                if sponsor_exists:
                    await tg_send(user_id, "<b>✨ این کانال در بخش اسپانسر ثبت شده، اما به‌عنوان کانال اجباری ثبت نشده است.</b>")
                else:
                    await tg_send(user_id, "<b>✨ این کانال در این قرعه‌کشی ثبت نشده است.</b>")
        if user_id in user_data:
            del user_data[user_id]
        await show_mandatory_channels(user_id, lottery_id)
        return

    if user_id in user_data and user_data[user_id].get("step") == "waiting_channel_count_manual":
        if text == "/cancel":
            del user_data[user_id]
            await send_menu(user_id, get_text(user_id, "welcome", name=message.from_user.first_name), get_main_keyboard(user_id))
            return
        try:
            count = int((text or "").strip())
            if count < 1 or count > 20:
                await tg_send(user_id, "✨ تعداد کانال باید بین ۱ تا ۲۰ باشد.")
                return
            user_data[user_id]["data"]["channel_count"] = count
            user_data[user_id]["data"]["channels"] = []
            user_data[user_id]["data"]["channel_manual"] = False
            user_data[user_id]["step"] = 7
            await tg_send(user_id, get_text(user_id, "enter_channel", num=1))
        except (TypeError, ValueError):
            await tg_send(user_id, "✨ فقط یک عدد بین ۱ تا ۲۰ وارد کن.")
        return

    if user_id in user_data and user_data[user_id].get("step") == "waiting_channels_manual":
        channels = [line.strip() for line in text.split("\n") if line.strip()]
        if channels:
            ok, error = await validate_giveaway_channels(user_id, channels)
            if not ok:
                await tg_send(user_id, f"✨ کانال قابل قبول نیست.\n{error}")
                return
            user_data[user_id]["data"]["channels"] = channels
            user_data[user_id]["data"]["channel_manual"] = False
            user_data[user_id]["step"] = 8
            await tg_send(user_id, f"✨ {len(channels)} کانال اسپانسر تأیید شد!")
            await show_step(user_id, 8)
        else:
            await tg_send(user_id, "✨ لطفاً حداقل یک لینک کانال معتبر ارسال کنید!")
        return

    if user_id in user_data and user_data[user_id].get("step") == "participant_broadcast":
        if text == "/cancel":
            del user_data[user_id]
            await tg_send(user_id, "✨ ارسال پیام لغو شد!", reply_markup=get_main_keyboard(user_id))
            return
        lottery_id = user_data[user_id].get("lottery_id")
        participants = list(lotteries.get(lottery_id, {}).get("participants", [])) if lottery_id else []
        success_count = fail_count = 0
        for participant_id in participants:
            try:
                await bot.copy_message(chat_id=participant_id, from_chat_id=message.chat.id, message_id=message.message_id)
                success_count += 1
                await asyncio.sleep(0.05)
            except Exception:
                fail_count += 1
        del user_data[user_id]
        await tg_send(user_id, f"✨ پیام ارسال شد! موفق: {success_count} ناموفق: {fail_count}", reply_markup=get_main_keyboard(user_id))
        return

    if user_id in user_data and user_data[user_id].get("step") == "waiting_reward_manual":
        if text == "/cancel":
            del user_data[user_id]
            await tg_send(user_id, "✨ ساخت قرعه‌کشی لغو شد!", reply_markup=get_main_keyboard(user_id))
            return
        if not text or not text.strip():
            await tg_send(user_id, "✨ لطفاً یک متن معتبر برای جایزه ارسال کنید.")
            return
        user_data[user_id]["data"]["reward"] = text.strip()
        user_data[user_id]["step"] = 10
        await show_step(user_id, 10)
        return

    if user_id in user_data and user_data[user_id].get("step") == "waiting_custom_text":
        lottery_id = user_data[user_id].get("send_lottery_id")
        photo_file_id = None
        if getattr(message, "photo", None):
            photo_file_id = message.photo[-1].file_id
            custom_text = message.html_text or ""
        else:
            custom_text = message.html_text or ""

        if not custom_text.strip() and not photo_file_id:
            await tg_send(user_id, "✨ متن یا عکسی دریافت نشد، دوباره ارسال کنید.")
            return

        if lottery_id and lottery_id in lotteries:
            success_count, failed_count, failed_details = await send_lottery_to_channels(
                lottery_id, custom_text=custom_text, photo_file_id=photo_file_id,
                custom_is_preformatted_html=True,
            )
            report = f"✅ با موفقیت ارسال شد!\n\n📣 موفق: {success_count} کانال\n❌ ناموفق: {failed_count} کانال"
            if failed_details:
                lines = "\n".join(f"• {ch}: {reason}" for ch, reason in failed_details[:10])
                report += f"\n\n{lines}"
            await tg_send(user_id, report)
            if user_id in user_data:
                del user_data[user_id]
            await send_menu(user_id, get_text(user_id, "welcome", name=message.from_user.first_name), get_main_keyboard(user_id))
        else:
            await tg_send(user_id, "✨ خطا: قرعه‌کشی یافت نشد!")
            if user_id in user_data:
                del user_data[user_id]
        return

    if user_id in user_math:
        try:
            user_answer = int(text.strip())
            math_data = user_math[user_id]
            lottery_id = math_data["lottery_id"]
            referrer_id = math_data.get("referrer_id")
            if user_answer == math_data["answer"]:
                del user_math[user_id]
                math_fail[user_id] = 0
                await complete_lottery_participation(user_id, lottery_id, referrer_id)
            else:
                # Wrong answer: immediately generate a fresh question.
                await register_fail(user_id, "math_wrong")
                if user_id in blocked_until:
                    user_math.pop(user_id, None)
                else:
                    num1, num2 = random.randint(1, 50), random.randint(1, 50)
                    operator = random.choice(["+", "-"])
                    if operator == "-" and num1 < num2:
                        num1, num2 = num2, num1
                    answer = num1 + num2 if operator == "+" else num1 - num2
                    token = f"{user_id}-{time.time()}"
                    user_math[user_id] = {"lottery_id": lottery_id, "answer": answer, "num1": num1, "num2": num2, "operator": operator, "referrer_id": referrer_id, "token": token}
                    schedule_math_timeout(user_id, token)
                    await tg_send(user_id, get_text(user_id, "math_question", question=f"{num1} {operator} {num2}"))
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if user_id not in user_data:
        await send_menu(user_id, get_text(user_id, "welcome", name=message.from_user.first_name), get_main_keyboard(user_id), chat_id=message.chat.id)
        return

    step = user_data[user_id]["step"]
    udata = user_data[user_id]["data"]

    if step == 1:
        udata["name"] = text
        user_data[user_id]["step"] = 2
        await tg_send(user_id, f"✨ اسم قرعه‌کشی: {text}")
        await show_step(user_id, 2)
        return

    if step == 2:
        try:
            value = int(text)
            if "duration_type" not in udata:
                await tg_send(user_id, "✨ ابتدا نوع زمان (روز/ساعت/دقیقه) را انتخاب کنید!")
                return
            if value <= 0:
                await tg_send(user_id, "✨ عدد باید بزرگتر از صفر باشد!")
                return
            udata["duration"] = value
            user_data[user_id]["step"] = 3
            await show_step(user_id, 3)
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if step == 3:
        try:
            value = int(text)
            if value <= 0:
                await tg_send(user_id, "✨ عدد باید بزرگتر از صفر باشد!")
                return
            udata["capacity"] = value
            user_data[user_id]["step"] = 4
            await show_step(user_id, 4)
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if step == 4:
        try:
            value = int(text)
            if value <= 0:
                await tg_send(user_id, "✨ عدد باید بزرگتر از صفر باشد!")
                return
            udata["winners"] = value
            user_data[user_id]["step"] = 5
            await show_step(user_id, 5)
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if step == 5:
        try:
            value = int(text)
            if value < 0:
                await tg_send(user_id, "✨ عدد باید بزرگتر یا مساوی صفر باشد!")
                return
            udata["min_participants"] = value
            user_data[user_id]["step"] = 6
            await show_step(user_id, 6)
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if step == 7:
        if not udata.get("channel_manual", False):
            ok, error = await check_giveaway_channel_permissions(text, user_id)
            if not ok:
                await tg_send(user_id, f"✨ این کانال قابل استفاده نیست.\n{error}")
                return
            udata.setdefault("channels", []).append(text)
            channel_count = udata.get("channel_count", 0)
            if len(udata["channels"]) < channel_count:
                await tg_send(user_id, get_text(user_id, "enter_channel", num=len(udata["channels"]) + 1))
            else:
                user_data[user_id]["step"] = 8
                await show_step(user_id, 8)
        return

    if step in (8.5, 8_5):
        try:
            value = int(text)
            if value < 0:
                await tg_send(user_id, "✨ عدد باید بزرگتر یا مساوی صفر باشد!")
                return
            udata["max_ref"] = value
            user_data[user_id]["step"] = 9
            await show_step(user_id, 9)
        except ValueError:
            await tg_send(user_id, get_text(user_id, "enter_number"))
        return

    if step == 9:
        await show_step(user_id, 9)
        return

    if step == 10:
        await show_step(user_id, 10)
        return

    await tg_send(user_id, "✨ لطفاً از دکمه‌ها استفاده کنید!")


async def main():
    global BOT_USERNAME
    acquire_single_instance_lock()
    load_data()
    if not forced_channels:
        save_data()
    await bot.delete_webhook(drop_pending_updates=True)
    me = await bot.get_me()
    BOT_USERNAME = me.username
    await refresh_premium_emoji_catalog()
    # Migrate old giveaway records to the current channel/referral schema.
    for lottery in lotteries.values():
        lottery.setdefault("mandatory_channels", [])
        lottery.setdefault("referrals", {})
        lottery.setdefault("referred_users", {})
        lottery.setdefault("referred_by", {})
        lottery.setdefault("captcha_enabled", False)
        # Normalize old referral keys to strings so every referral is counted consistently.
        lottery["referrals"] = {str(k): int(v) for k, v in (lottery.get("referrals", {}) or {}).items() if str(v).lstrip('-').isdigit()}
    save_data()
    for lid, lottery in list(lotteries.items()):
        try:
            if lottery.get("active") and lottery.get("end_time", 0) > time.time():
                schedule_lottery_end(lid)
            elif lottery.get("active") and lottery.get("end_time", 0) <= time.time():
                await end_lottery(lid, lottery.get("timer_version", 0))
        except Exception:
            pass

    print("✅ شاه وین ز با موفقیت اجرا شد! ربات در حال اجراست...", flush=True)
    while True:
        try:
            await dp.start_polling(bot)
            break
        except TelegramConflictError:
            print("خطای 409: یک نمونه دیگر در حال Poll کردن همین توکن است.")
            await asyncio.sleep(5)
        except TelegramAPIError as e:
            print(f"خطای API تلگرام: {e}")
            await asyncio.sleep(5)
        except Exception as e:
            print(f"error: {e}")
            await asyncio.sleep(3)


if __name__ == "__main__":
    asyncio.run(main())