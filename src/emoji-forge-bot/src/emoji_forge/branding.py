"""Bot-level branding (v9): the BOT itself carries the owner's brand.

Two different brands exist and must never be confused:
- BOT brand ("TZ Emojis" / owner credit "TZ Emojis"): the bot's
  own identity — welcome text, menus, captions, publish messages,
  and the Telegram-side profile set via /apply_branding.
- USER brand: each user's own name/logo/colours, applied INSIDE
  their emojis by the pipeline. Bot branding never overwrites it
  (the only exception is the admin credit toggle, default OFF — see
  settings.py).

Publish title rule (documented): if the user set their own set title
it is kept exactly, with NO suffix. Only a bot-generated default
title gets the " • TZ Emojis" suffix.
"""

BOT_BRAND = "TZ Emojis"
OWNER_BRAND = "TZ Emojis"
OWNER_CREDIT_FA = "ساخته‌شده توسط TZ Emojis — @tzemoji_ai"

BOT_NAME = "TZ Emojis | ایموجی‌ساز"
BOT_SHORT_DESCRIPTION = (
    "ایموجی‌ساز TZ Emojis — لوگوی قبلی پک حذف و با نام و لوگوی شما "
    "بازسازی می‌شود. استفاده نیازمند تأیید مالک است."
)
BOT_DESCRIPTION = (
    "🎨 TZ Emojis — ایموجی‌ساز و بازسازی پک ایموجی\n\n"
    "یک ایموجی یا پک بفرستید؛ لوگوی قبلی پیدا و حذف می‌شود، کاراکتر و "
    "رنگ‌های اصلی حفظ می‌شود و نام/لوگوی شما با جای‌گذاری دقیق "
    "(contain-fit) در جای لوگوی قبلی می‌نشیند. هر سه فرمت TGS و WEBM "
    "و تصویر ثابت پشتیبانی می‌شود.\n\n"
    "🔐 استفاده از ربات نیازمند تأیید مالک است: بعد از /start درخواست "
    "شما برای مالک فرستاده می‌شود و سهمیه استفاده‌تان را او مشخص می‌کند.\n\n"
    "⚖️ فقط پک‌هایی را بفرستید که مالک آن‌ها هستید یا اجازه تغییرشان را دارید.\n\n"
    f"{OWNER_CREDIT_FA}"
)

# Public command list shown by Telegram (set via setMyCommands).
BOT_COMMANDS = [
    {"command": "start", "description": "شروع و منوی اصلی"},
    {"command": "help", "description": "راهنما"},
    {"command": "preview", "description": "پیش‌نمایش نمونه‌ها"},
    {"command": "process", "description": "پردازش کامل پک"},
    {"command": "export", "description": "خروجی ZIP"},
    {"command": "publish_new", "description": "انتشار پک جدید"},
    {"command": "mypacks", "description": "پک‌های من"},
    {"command": "status", "description": "وضعیت پروژه و سهمیه"},
    {"command": "cancel", "description": "لغو پروژه جاری"},
]

# v10 "what's new" block shown on /start and /help. Kept free of
# emojis on purpose: the ONE premium emoji for this block is added
# by the *_html() builders, placed BEFORE «تازه‌های نسخه ۱۰» in the
# string (the visual right in RTL) — never inside a bullet, where
# it would break the wrap (owner screenshot review).
V10_WHATS_NEW = (
    "تازه‌های نسخه ۱۰:\n"
    "• گارد ایمپورتر: فایل‌هایی که تلگرام رد می‌کند پیش از تحویل پیدا و اصلاح می‌شوند.\n"
    "• انتشار قابل ادامه: اگر انتشار وسط کار قطع شود، از همان‌جا ادامه پیدا می‌کند.\n"
    "• نوار پیشرفت زنده با درصد و زمان تقریبی در پردازش و انتشار.\n"
    "• «پک‌های من» در منوی اصلی + پیش‌نمایش کنارهمِ قبل/بعد.\n"
    "• رنگ‌های بیشتر (۱۱۲ رنگ) و ۱۴ گرادیان آماده برای برند شما."
)


def welcome_text() -> str:
    return (
        f"سلام! به {BOT_BRAND} خوش آمدید.\n\n"
        "یک ایموجی یا پک بفرستید؛ بقیه‌اش با ربات است:\n"
        "۱) لوگوی قبلی را پیدا و حذف می‌کنم.\n"
        "۲) کاراکتر را دست نمی‌زنم — رنگ مو و رنگ‌های اصلی‌اش حفظ می‌شود.\n"
        "۳) نام و لوگوی شما را با جای‌گذاری دقیق، جای لوگوی قبلی می‌گذارم.\n\n"
        "پردازش نهایی با دقت کامل توسط خود ربات انجام می‌شود.\n"
        "⚖️ فقط پک‌هایی را بفرستید که مالک آن‌ها هستید یا اجازه تغییرشان را دارید.\n\n"
        "همه‌چیز با دکمه است؛ لازم نیست چیزی تایپ کنید.\n\n"
        f"نسخه ۱۰\n{V10_WHATS_NEW}"
    )


def _with_whats_new_pe(escaped_body: str) -> str:
    """Insert the single premium emoji of the /start//help texts:
    pe('sparkles') immediately BEFORE «تازه‌های نسخه ۱۰» in the
    string — in RTL that is the visual right of the heading, where
    a leading decoration belongs. No premium emoji anywhere else in
    the body (bullets stay clean)."""
    from emoji_forge.premium import pe
    heading = "تازه‌های نسخه ۱۰"
    if heading in escaped_body:
        return escaped_body.replace(
            heading, f"{pe('sparkles')} {heading}", 1)
    return escaped_body


def welcome_html() -> str:
    """HTML variant of the /start welcome (Bot API 9.4): exactly one
    premium emoji, before the «تازه‌های نسخه ۱۰» heading; the body
    is the plain welcome text, HTML-escaped. Sent only with
    parse_mode="HTML"."""
    import html as _html

    return _with_whats_new_pe(_html.escape(welcome_text()))


def help_html() -> str:
    """HTML variant of /help: same single premium emoji before the
    «تازه‌های نسخه ۱۰» heading; the plain help body HTML-escaped.
    Sent only with parse_mode="HTML"."""
    import html as _html

    return _with_whats_new_pe(_html.escape(help_text()))


def pending_welcome_text() -> str:
    return (
        f"سلام! به {BOT_BRAND} خوش آمدید.\n\n"
        "🔐 استفاده از این ربات نیازمند تأیید مالک است. درخواست شما "
        "ثبت شد و برای مالک فرستاده شد؛ به‌محض تأیید، همین‌جا می‌توانید "
        "کار را شروع کنید."
    )


def help_text() -> str:
    return (
        "همه‌چیز با دکمه است:\n"
        "۱) یک ایموجی، استیکر، لینک پک یا فایل بفرستید (برای پک کامل، مالکیت را با دکمه تأیید کنید).\n"
        "۲) «پیش‌نمایش» را بزنید و فایل‌های اصلی و نهایی نمونه‌ها را مقایسه کنید.\n"
        "۳) «پردازش کامل» را بزنید؛ ربات با دقت پک را بررسی می‌کند (لوگو، رنگ مو و کاراکتر) و نتیجه را همین‌جا می‌فرستد.\n"
        "۴) رنگ و برند شما پشت دکمه «رنگ و برند من» است؛ تنظیمات بیشتر پشت «تنظیمات پیشرفته».\n"
        "۵) «انتشار پک جدید» یک ست جدید می‌سازد؛ پک اصلی دست نمی‌خورد.\n"
        "محدودیت صادقانه: تشخیص خودکار لوگو برای نوارهای لبه و لایه‌های TGS است؛ "
        "لوگوی وسط بافت پیچیده را خود ربات با پردازش پیشرفته مشخص می‌کند، و پیش‌نمایش را حتماً ببینید.\n\n"
        f"نسخه ۱۰\n{V10_WHATS_NEW}\n\n"
        f"ℹ️ درباره: {OWNER_CREDIT_FA} — برند ربات: {BOT_BRAND}"
    )


def zip_caption(file_count: int) -> str:
    return f"خروجی ZIP — {file_count} فایل • ساخته‌شده با {BOT_BRAND}"


def report_caption() -> str:
    return f"گزارش پردازش • {BOT_BRAND}"


def publish_title(user_title: str, project_id: str) -> str:
    """Title for createNewStickerSet (see module docstring rule)."""
    user_title = (user_title or "").strip()
    if user_title:
        return user_title[:64]
    return f"{BOT_BRAND} {project_id}"[:64]


def apply_branding(api) -> dict:
    """Push the bot identity to Telegram (setMyName / descriptions /
    commands). Called ONLY from the owner-only /apply_branding
    command — never automatically, never at import/startup."""
    results = {}
    results["name"] = api.set_my_name(BOT_NAME)
    results["short_description"] = api.set_my_short_description(BOT_SHORT_DESCRIPTION)
    results["description"] = api.set_my_description(BOT_DESCRIPTION)
    results["commands"] = api.set_my_commands(BOT_COMMANDS)
    return results
