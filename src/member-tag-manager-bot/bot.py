# Telegram: @Abolfazl_rwm
import os
import re
import copy
import html
import sqlite3
import random
import requests
from telethon import TelegramClient, events, utils, Button
from telethon.tl.types import MessageEntityCustomEmoji
from telethon.tl import functions, types as tltypes

# ---------------- تنظیمات ----------------
API_ID = int(os.environ.get("API_ID", "0") or 0)       # از my.telegram.org
API_HASH = os.environ.get("API_HASH", "")              # از my.telegram.org
BOT_TOKEN = os.environ.get("BOT_TOKEN", "")            # توکن ربات از @BotFather

ADMIN_ID = int(os.environ.get("ADMIN_ID", "0") or 0)   # آیدی عددی ادمین اصلی
TARGET_GROUP_ID = int(os.environ.get("TARGET_GROUP_ID", "0") or 0)  # آیدی عددی گروه خصوصی

PLACEHOLDER = "🔥"   # ایموجی جایگزین که ایموجی پرمیوم روش نمایش داده میشه
# ------------------------------------------

EMOJI_CODE_PATTERN = re.compile(r"\[(\d{5,25})\]")

client = TelegramClient("Bot", API_ID, API_HASH).start(bot_token=BOT_TOKEN)

# متد setChatMemberTag هنوز توی Telethon (که MTProto خامه) پوشش داده نشده،
# پس مستقیم از طریق HTTP Bot API صداش می‌زنیم.
BOT_API_URL = f"https://api.telegram.org/bot{BOT_TOKEN}"


def utf16_len(s: str) -> int:
    """طول رشته بر حسب واحدهای UTF-16 (همون واحدی که تلگرام برای offset/length استفاده می‌کنه)"""
    return len(s.encode("utf-16-le")) // 2


def build_new_text_and_entities(text, entities, emoji_map=None):
    """
    کدهای [عدد] و همچنین ایموجی‌های ساده‌ای که با دستور em ثبت شدن رو با
    ایموجی پرمیوم واقعی جایگزین می‌کنه و آفست entityهای قبلی (بولد، ایتالیک،
    کوت و ...) رو متناسب با تغییر طول متن اصلاح می‌کنه.

    emoji_map: دیکشنری {ایموجی ساده: document_id ایموجی پرمیوم} که با دستور
    "em CODE = ایموجی" ساخته میشه.
    """
    entities = list(entities or [])
    emoji_map = emoji_map or {}
    if not text:
        return text, entities

    # الگوی ترکیبی: هم کدهای [عدد] و هم ایموجی‌های سادهٔ ثبت‌شده رو پیدا می‌کنه.
    # ایموجی‌های طولانی‌تر (چند-کدپوینتی) اول چک میشن تا با یه بخشی از خودشون اشتباه گرفته نشن.
    alt_parts = [EMOJI_CODE_PATTERN.pattern]
    sorted_emojis = sorted(emoji_map.keys(), key=len, reverse=True)
    alt_parts.extend(re.escape(e) for e in sorted_emojis)
    combined_pattern = re.compile("|".join(alt_parts))

    matches = list(combined_pattern.finditer(text))
    if not matches:
        return text, entities

    def resolve_document_id(matched_text):
        code_match = re.fullmatch(r"\[(\d{5,25})\]", matched_text)
        if code_match:
            return int(code_match.group(1))
        return emoji_map.get(matched_text)

    result_text = ""
    new_entities = []
    match_spans = []  # (orig_start_u16, orig_end_u16, delta)
    last_py_idx = 0

    for m in matches:
        start, end = m.span()
        document_id = resolve_document_id(m.group(0))

        result_text += text[last_py_idx:start]

        if document_id is None:
            # این نباید معمولاً پیش بیاد، اما برای اطمینان متن رو دست‌نخورده نگه می‌داریم
            result_text += text[start:end]
            last_py_idx = end
            continue

        orig_start_u16 = utf16_len(text[:start])
        orig_end_u16 = utf16_len(text[:end])
        old_len_u16 = orig_end_u16 - orig_start_u16

        placeholder_start_u16 = utf16_len(result_text)
        result_text += PLACEHOLDER
        new_len_u16 = utf16_len(PLACEHOLDER)

        new_entities.append(MessageEntityCustomEmoji(
            offset=placeholder_start_u16,
            length=new_len_u16,
            document_id=document_id,
        ))
        match_spans.append((orig_start_u16, orig_end_u16, new_len_u16 - old_len_u16))
        last_py_idx = end

    result_text += text[last_py_idx:]
    final_len_u16 = utf16_len(result_text)

    for e in entities:
        e_start = e.offset
        e_end = e.offset + e.length
        offset_shift = 0
        length_shift = 0
        for s, t, delta in match_spans:
            if t <= e_start:
                # جایگزینی کاملاً قبل از این entity رخ داده -> فقط آفست جابجا میشه
                offset_shift += delta
            elif s >= e_end:
                # جایگزینی کاملاً بعد از این entity -> بی‌تاثیر
                continue
            else:
                # جایگزینی داخل یا هم‌پوشان با این entity -> طولش عوض میشه
                length_shift += delta
        e.offset = max(0, e_start + offset_shift)
        e.length = max(0, e.length + length_shift)
        # entityهایی که کاملا بیرون از محدوده متن نهایی افتادن رو حذف کن
        if e.offset < final_len_u16 and e.length > 0:
            if e.offset + e.length > final_len_u16:
                e.length = final_len_u16 - e.offset
            new_entities.append(e)

    new_entities.sort(key=lambda e: e.offset)
    return result_text, new_entities


def slice_text_and_entities(text, entities, start_py_idx):
    """
    یه زیررشته از متن (از ایندکس start_py_idx به بعد، بر حسب کاراکترهای پایتون)
    رو برمی‌گردونه و entityهای فرمت (بولد، ایتالیک، ایموجی و ...) رو هم متناسب
    با اون بخش برش می‌زنه و آفستشون رو نسبت به شروع متن جدید اصلاح می‌کنه.
    برای جدا کردن متن اصلی از پیشوند یه دستور (مثل "تست ") استفاده میشه.
    """
    entities = list(entities or [])
    if start_py_idx <= 0:
        return text, entities

    prefix_u16 = utf16_len(text[:start_py_idx])
    new_text = text[start_py_idx:]
    new_entities = []

    for e in entities:
        e_start = e.offset
        e_end = e.offset + e.length
        if e_end <= prefix_u16:
            continue  # این entity کاملاً توی پیشوند حذف‌شده بود

        new_start = max(e_start, prefix_u16) - prefix_u16
        new_length = e_end - max(e_start, prefix_u16)
        if new_length <= 0:
            continue

        e_copy = copy.copy(e)
        e_copy.offset = new_start
        e_copy.length = new_length
        new_entities.append(e_copy)

    return new_text, new_entities


@client.on(events.NewMessage(from_users=ADMIN_ID, incoming=True, func=lambda e: e.is_private))
async def forward_admin_message(event):
    message = event.message
    original_text = message.raw_text or ""

    # اگه این پیام یه دستور "Set CODE = نام شهر" باشه، فوروارد نشه (هندلر جدا خودش رسیدگی می‌کنه)
    if SET_CITY_PATTERN.match(original_text.strip()):
        return

    # اگه این پیام یه دستور "em CODE = ایموجی" باشه، فوروارد نشه (هندلر جدا خودش رسیدگی می‌کنه)
    if EM_PATTERN.match(original_text.strip()):
        return

    original_entities = message.entities or []
    emoji_map = get_emoji_map()

    final_text, final_entities = build_new_text_and_entities(original_text, original_entities, emoji_map)
    final_text = final_text or None

    try:
        if message.media:
            await client.send_file(
                TARGET_GROUP_ID,
                file=message.media,
                caption=final_text,
                formatting_entities=final_entities if final_text else None,
            )
        elif final_text:
            await client.send_message(
                TARGET_GROUP_ID,
                final_text,
                formatting_entities=final_entities,
            )
    except Exception as e:
        await event.reply(f"خطا در ارسال به گروه: {e}")


# ============== ویژگی Set Name (تگ اعضا) ==============

DB_PATH = "custom_names.db"
db = sqlite3.connect(DB_PATH)
db.execute("""
CREATE TABLE IF NOT EXISTS custom_names (
    chat_id INTEGER,
    user_id INTEGER,
    name TEXT,
    PRIMARY KEY (chat_id, user_id)
)
""")
db.execute("""
CREATE TABLE IF NOT EXISTS tag_admins (
    chat_id INTEGER,
    user_id INTEGER,
    PRIMARY KEY (chat_id, user_id)
)
""")
db.execute("""
CREATE TABLE IF NOT EXISTS city_map (
    code TEXT PRIMARY KEY,
    name TEXT
)
""")
db.execute("INSERT OR IGNORE INTO city_map (code, name) VALUES ('TH', 'تهران')")
db.execute("""
CREATE TABLE IF NOT EXISTS emoji_map (
    simple_emoji TEXT PRIMARY KEY,
    code INTEGER
)
""")
db.execute("""
CREATE TABLE IF NOT EXISTS invite_links (
    chat_id INTEGER,
    user_id INTEGER,
    link TEXT,
    PRIMARY KEY (chat_id, user_id)
)
""")
db.execute("""
CREATE TABLE IF NOT EXISTS invite_link_joins (
    chat_id INTEGER,
    link TEXT,
    user_id INTEGER,
    PRIMARY KEY (chat_id, link, user_id)
)
""")
db.execute("""
CREATE TABLE IF NOT EXISTS join_requests (
    chat_id INTEGER,
    user_id INTEGER,
    link TEXT,
    PRIMARY KEY (chat_id, user_id)
)
""")
db.commit()

# آیدی ایموجی پرمیومی که قبل از پیام نتیجه نشون داده میشه (بولت)
CUSTOM_EMOJI_BULLET_ID = 5253991947978160480

SET_NAME_PATTERN = re.compile(r"(?i)^set name\s+(.+)$")
SET_TAGER_PATTERN = re.compile(r"(?i)^set tager$")


def add_tager_admin(chat_id: int, user_id: int):
    db.execute(
        "INSERT OR IGNORE INTO tag_admins (chat_id, user_id) VALUES (?, ?)",
        (chat_id, user_id),
    )
    db.commit()


def is_tager_admin(chat_id: int, user_id: int) -> bool:
    cur = db.execute(
        "SELECT 1 FROM tag_admins WHERE chat_id = ? AND user_id = ?",
        (chat_id, user_id),
    )
    return cur.fetchone() is not None


def is_authorized_for_set_name(chat_id: int, user_id: int) -> bool:
    return user_id == ADMIN_ID or is_tager_admin(chat_id, user_id)


def save_custom_name(chat_id: int, user_id: int, name: str):
    db.execute(
        "INSERT INTO custom_names (chat_id, user_id, name) VALUES (?, ?, ?) "
        "ON CONFLICT(chat_id, user_id) DO UPDATE SET name = excluded.name",
        (chat_id, user_id, name),
    )
    db.commit()


async def is_owner_or_admin(chat_id: int, user_id: int) -> bool:
    try:
        perms = await client.get_permissions(chat_id, user_id)
        return bool(perms.is_admin or perms.is_creator)
    except Exception:
        return False


def set_member_tag(chat_id: int, user_id: int, tag: str):
    """صدا زدن مستقیم متد رسمی setChatMemberTag تلگرام (آپدیت مارس ۲۰۲۶)"""
    resp = requests.post(
        f"{BOT_API_URL}/setChatMemberTag",
        json={"chat_id": chat_id, "user_id": user_id, "tag": tag},
        timeout=15,
    )
    return resp.json()


@client.on(events.NewMessage(chats=TARGET_GROUP_ID))
async def set_name_handler(event):
    match = SET_NAME_PATTERN.match(event.raw_text or "")
    if not match:
        return

    if not is_authorized_for_set_name(TARGET_GROUP_ID, event.sender_id):
        return  # کاربر مجاز به استفاده از این دستور نیست

    if not event.message.is_reply:
        await event.reply("برای ثبت نام باید روی پیام کاربر مورد نظر ریپلای کنی.")
        return

    new_name = match.group(1).strip()
    reply_msg = await event.message.get_reply_message()

    if reply_msg is None:
        await event.reply("پیام ریپلای‌شده پیدا نشد (شاید پاک شده یا در دسترس نیست).")
        return

    target_user = await reply_msg.get_sender()

    if target_user is None:
        await event.reply("کاربر ریپلای‌شده پیدا نشد.")
        return

    target_id = target_user.id
    target_first_name = target_user.first_name or "کاربر"

    # ثبت توی دیتابیس - همیشه انجام میشه، چه ادمین/مالک باشه چه عضو عادی
    save_custom_name(TARGET_GROUP_ID, target_id, new_name)

    admin_or_owner = await is_owner_or_admin(TARGET_GROUP_ID, target_id)

    # فقط برای اعضای عادی، تگ واقعی (Member Tag) روی نامشون توی تلگرام ست میشه؛
    # چون تگ owner/admin رنگ خودشو (بنفش/سبز) از قبل داره و ربات نباید دستش بزنه،
    # برای اونا فقط توی دیتابیس ثبت میشه.
    if not admin_or_owner:
        result = set_member_tag(TARGET_GROUP_ID, target_id, new_name)
        if not result.get("ok"):
            err = result.get("description", "خطای نامشخص")
            await event.reply(
                "خطا در ثبت تگ:\n"
                f"{err}\n\n"
                "مطمئن شو ربات توی گروه ادمینه و دسترسی «مدیریت تگ‌ها» (can_manage_tags) بهش داده شده."
            )
            return

    name_link = f'<a href="tg://user?id={target_id}">{html.escape(new_name)}</a>'
    user_link = f'<a href="tg://user?id={target_id}">{html.escape(target_first_name)}</a>'

    response_text = (
        f'<blockquote>'
        f'• | <tg-emoji emoji-id="{CUSTOM_EMOJI_BULLET_ID}">🔥</tg-emoji> | '
        f'<b>The tag {name_link} was successfully registered for {user_link}</b>'
        f'</blockquote>'
    )

    await event.respond(response_text, parse_mode="html")


@client.on(events.NewMessage(chats=TARGET_GROUP_ID, from_users=ADMIN_ID))
async def set_tager_handler(event):
    if not SET_TAGER_PATTERN.match((event.raw_text or "").strip()):
        return

    if not event.message.is_reply:
        await event.reply("برای افزودن ادمین تگ‌ها باید روی پیام کاربر مورد نظر ریپلای کنی.")
        return

    reply_msg = await event.message.get_reply_message()
    if reply_msg is None:
        await event.reply("پیام ریپلای‌شده پیدا نشد (شاید پاک شده یا در دسترس نیست).")
        return

    target_user = await reply_msg.get_sender()
    if target_user is None:
        await event.reply("کاربر ریپلای‌شده پیدا نشد.")
        return

    add_tager_admin(TARGET_GROUP_ID, target_user.id)

    target_first_name = target_user.first_name or "کاربر"
    user_link = f'<a href="tg://user?id={target_user.id}">{html.escape(target_first_name)}</a>'

    response_text = (
        f'• | <tg-emoji emoji-id="{CUSTOM_EMOJI_BULLET_ID}">🔥</tg-emoji> | '
        f'<b>کاربر {user_link} با موفقیت ادمین تگ‌ها شد!</b>'
    )

    await event.respond(response_text, parse_mode="html")


# ============== ویژگی معرفی (اصل / اصل بده / شما / معرفی کن) ==============

INTRO_TRIGGERS = {"اصل", "اصل بده", "شما", "شما؟", "معرفی کن", "معرفی کن خودتو"}

# ایموجی‌های پرمیومی که هر بار یکیشون به‌صورت رندوم انتخاب میشه
INTRO_EMOJI_IDS = [
    5253715150220831973,
    5251268629474847228,
    5251667107950635992,
    5251723591065545015,
]

SET_CITY_PATTERN = re.compile(r"(?i)^set\s+(\S+)\s*=\s*(.+)$")

# دستور "em کد = ایموجی_ساده" برای ثبت جایگزینی ایموجی ساده با ایموجی پرمیوم
EM_PATTERN = re.compile(r"(?i)^em\s+(\d{5,25})\s*=\s*(\S+)\s*$")


def add_emoji_mapping(simple_emoji: str, code: int):
    db.execute(
        "INSERT INTO emoji_map (simple_emoji, code) VALUES (?, ?) "
        "ON CONFLICT(simple_emoji) DO UPDATE SET code = excluded.code",
        (simple_emoji, code),
    )
    db.commit()


def get_emoji_map() -> dict:
    cur = db.execute("SELECT simple_emoji, code FROM emoji_map")
    return {row[0]: row[1] for row in cur.fetchall()}


def add_city_mapping(code: str, name: str):
    db.execute(
        "INSERT INTO city_map (code, name) VALUES (?, ?) "
        "ON CONFLICT(code) DO UPDATE SET name = excluded.name",
        (code.upper(), name),
    )
    db.commit()


def get_city_name(code: str) -> str:
    cur = db.execute("SELECT name FROM city_map WHERE code = ?", (code.upper(),))
    row = cur.fetchone()
    return row[0] if row else code  # اگه کد شناخته‌شده نبود، همون کد نمایش داده میشه


def get_member_tag(chat_id: int, user_id: int):
    """گرفتن تگ فعلی یک عضو از طریق متد رسمی getChatMember"""
    resp = requests.get(
        f"{BOT_API_URL}/getChatMember",
        params={"chat_id": chat_id, "user_id": user_id},
        timeout=15,
    )
    result = resp.json()
    if not result.get("ok"):
        return None
    return result["result"].get("tag")


def get_custom_name(chat_id: int, user_id: int):
    """
    برای ادمین/مالک، چون تگ واقعی تلگرام روشون ست نمیشه، تگ ثبت‌شده
    توی دیتابیس محلی (custom_names) به‌عنوان جایگزین خونده میشه.
    """
    cur = db.execute(
        "SELECT name FROM custom_names WHERE chat_id = ? AND user_id = ?",
        (chat_id, user_id),
    )
    row = cur.fetchone()
    return row[0] if row else None


@client.on(events.NewMessage(chats=TARGET_GROUP_ID))
async def intro_handler(event):
    text = (event.raw_text or "").strip()
    if text not in INTRO_TRIGGERS:
        return

    if not event.message.is_reply:
        return  # این قابلیت فقط با ریپلای روی کاربر معنی داره

    reply_msg = await event.message.get_reply_message()
    if reply_msg is None:
        return

    target_user = await reply_msg.get_sender()
    if target_user is None:
        return

    raw_tag = get_member_tag(TARGET_GROUP_ID, target_user.id)
    if not raw_tag:
        # برای ادمین/مالک تگ واقعی تلگرام ست نشده، پس از دیتابیس محلی می‌خونیم
        raw_tag = get_custom_name(TARGET_GROUP_ID, target_user.id)
    if not raw_tag:
        return  # این کاربر هیچ تگی (نه واقعی، نه محلی) نداره

    parts = [p.strip() for p in raw_tag.split("-")]
    if len(parts) < 3:
        return  # فرمت تگ درست نیست (باید اسم - شهر - سن باشه)

    name, city_code, age = parts[0], parts[1], parts[2]
    city_name = get_city_name(city_code)
    target_id = target_user.id

    name_link = f'<a href="tg://user?id={target_id}">{html.escape(name)}</a>'
    city_link = f'<a href="tg://user?id={target_id}">{html.escape(city_name)}</a>'
    age_link = f'<a href="tg://user?id={target_id}">{html.escape(age)}</a>'

    emoji_id = random.choice(INTRO_EMOJI_IDS)

    response_text = (
        f'<b>• | <tg-emoji emoji-id="{emoji_id}">🔥</tg-emoji> | '
        f'این رفیقمون اِسمش {name_link} عه و داره توی {city_link} زندگی میکنه و اَلانه {age_link} سالشه!</b>'
    )

    await event.reply(response_text, parse_mode="html")


# دستور "Set CODE = نام شهر" توی پیوی ربات، هم از خود مالک هم از هر کسی که با Set Tager اضافه شده
@client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def set_city_handler(event):
    match = SET_CITY_PATTERN.match((event.raw_text or "").strip())
    if not match:
        return

    if not is_authorized_for_set_name(TARGET_GROUP_ID, event.sender_id):
        return  # کاربر مجاز نیست

    code = match.group(1)
    name = match.group(2).strip()
    add_city_mapping(code, name)

    await event.reply(f"کد شهر «{code.upper()}» با نام «{name}» ثبت شد.")


# دستور "em CODE = ایموجی_ساده" توی پیوی ربات، از ادمین یا هر کسی که با Set Tager اضافه شده
# ازون به بعد هر بار همون ایموجی ساده توی پیام ادمین فرستاده بشه، موقع ارسال به گروه
# به‌جای ایموجی ساده، همون ایموجی پرمیوم مربوط به کد نمایش داده میشه.
@client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def em_handler(event):
    match = EM_PATTERN.match((event.raw_text or "").strip())
    if not match:
        return

    if not is_authorized_for_set_name(TARGET_GROUP_ID, event.sender_id):
        return  # کاربر مجاز نیست

    code = int(match.group(1))
    simple_emoji = match.group(2)
    add_emoji_mapping(simple_emoji, code)

    await event.reply(f"از این به بعد ایموجی {simple_emoji} با ایموجی پرمیوم کد {code} جایگزین میشه.")


# دستور "تست پیام" فقط از مالک اصلی و فقط داخل گروه هدف.
# دقیقاً مثل حالت پیویش (فرمت‌ها حفظ میشه و کدهای ایموجی/em تبدیل میشن) پیام رو
# به‌عنوان خود ربات توی گروه ارسال می‌کنه، پیام دستور مالک حذف میشه، و اگه مالک
# روی کاربری ریپلای کرده باشه، ربات هم دقیقاً روی همون کاربر ریپلای می‌زنه.
TEST_COMMAND_PATTERN = re.compile(r"(?is)^تست\s+(.+)$")


@client.on(events.NewMessage(chats=TARGET_GROUP_ID, from_users=ADMIN_ID))
async def test_command_handler(event):
    message = event.message
    raw_text = message.raw_text or ""

    match = TEST_COMMAND_PATTERN.match(raw_text)
    if not match:
        return

    content_start = match.start(1)
    sliced_text, sliced_entities = slice_text_and_entities(
        raw_text, message.entities or [], content_start
    )

    emoji_map = get_emoji_map()
    final_text, final_entities = build_new_text_and_entities(sliced_text, sliced_entities, emoji_map)
    final_text = final_text or None

    reply_to = message.reply_to_msg_id if message.is_reply else None

    try:
        if message.media:
            await client.send_file(
                TARGET_GROUP_ID,
                file=message.media,
                caption=final_text,
                formatting_entities=final_entities if final_text else None,
                reply_to=reply_to,
            )
        elif final_text:
            await client.send_message(
                TARGET_GROUP_ID,
                final_text,
                formatting_entities=final_entities,
                reply_to=reply_to,
            )
        else:
            return  # چیزی برای ارسال نبود
    except Exception as e:
        await event.reply(f"خطا در ارسال پیام تست: {e}")
        return

    try:
        await message.delete()
    except Exception:
        pass  # اگه حذف پیام به هر دلیلی ممکن نشد، حداقل پیام توی گروه ارسال شده


# ============== ویژگی لینک اختصاصی (لینک بده) ==============
# وقتی کاربری توی گروه بنویسه "لینک بده"، ربات یه لینک دعوت اختصاصی
# مخصوص همون کاربر (بر اساس آیدی عددیش) می‌سازه، توی دیتابیس ذخیره می‌کنه،
# و هر بار که دوباره درخواست بده، همون لینک قبلی رو به‌همراه تعداد افرادی
# که از طریق اون لینک عضو گروه شدن نشون میده.

LINK_REQUEST_TRIGGERS = {"لینک بده", "لینک بده لطفا", "لینک بده لطفاً", "لینک میخوام", "لینک می‌خوام"}

# دستور ادمین برای منقضی/حذف کردن همه‌ی لینک‌های اختصاصی ساخته‌شده؛
# بعد از این دستور، هر کسی دوباره "لینک بده" بزنه یه لینک کاملاً جدید می‌گیره.
DELETE_LINKS_TRIGGERS = {"حذف لینکا", "حذف لینک ها", "حذف لینک‌ها", "پاک کردن لینکا", "پاک کردن لینک ها"}


def save_invite_link(chat_id: int, user_id: int, link: str):
    db.execute(
        "INSERT INTO invite_links (chat_id, user_id, link) VALUES (?, ?, ?) "
        "ON CONFLICT(chat_id, user_id) DO UPDATE SET link = excluded.link",
        (chat_id, user_id, link),
    )
    db.commit()


def get_invite_link(chat_id: int, user_id: int):
    cur = db.execute(
        "SELECT link FROM invite_links WHERE chat_id = ? AND user_id = ?",
        (chat_id, user_id),
    )
    row = cur.fetchone()
    return row[0] if row else None


async def create_user_invite_link(chat_id: int, user_id: int) -> str:
    """
    یه لینک دعوت جدید و اختصاصی (با تایتل حاوی آیدی کاربر) توی گروه می‌سازه.
    request_needed=True یعنی هر کسی از این لینک وارد بشه اول به‌صورت
    «درخواست عضویت» ثبت میشه و تا وقتی ادمین تاییدش نکنه، عضو گروه نمیشه.
    """
    peer = await client.get_input_entity(chat_id)
    result = await client(functions.messages.ExportChatInviteRequest(
        peer=peer,
        title=f"user_{user_id}",
        request_needed=True,
    ))
    return result.link


def save_join_request(chat_id: int, user_id: int, link: str):
    """درخواست عضویت در حال انتظار رو ذخیره می‌کنه تا موقع تایید/رد توسط ادمین بدونیم مال کدوم لینکه."""
    db.execute(
        "INSERT INTO join_requests (chat_id, user_id, link) VALUES (?, ?, ?) "
        "ON CONFLICT(chat_id, user_id) DO UPDATE SET link = excluded.link",
        (chat_id, user_id, link),
    )
    db.commit()


def get_join_request_link(chat_id: int, user_id: int):
    cur = db.execute(
        "SELECT link FROM join_requests WHERE chat_id = ? AND user_id = ?",
        (chat_id, user_id),
    )
    row = cur.fetchone()
    return row[0] if row else None


def delete_join_request(chat_id: int, user_id: int):
    db.execute(
        "DELETE FROM join_requests WHERE chat_id = ? AND user_id = ?",
        (chat_id, user_id),
    )
    db.commit()


def record_invite_join(chat_id: int, link: str, joined_user_id: int):
    db.execute(
        "INSERT OR IGNORE INTO invite_link_joins (chat_id, link, user_id) VALUES (?, ?, ?)",
        (chat_id, link, joined_user_id),
    )
    db.commit()


def get_invite_join_count(chat_id: int, link: str) -> int:
    """
    توجه: تلگرام به بات‌ها اجازه نمیده لیست ورودی‌های یه لینک دعوت رو مستقیم
    از سرور بگیرن (GetChatInviteImportersRequest فقط برای اکانت کاربریه، نه بات).
    برای همین به‌جاش، خودمون هر ورود جدید از طریق لینک رو با آپدیت خام تلگرام
    (updateChannelParticipant) شکار می‌کنیم و توی دیتابیس محلی ثبت می‌کنیم؛
    این تابع فقط همون شمارش محلی رو برمی‌گردونه (از لحظه‌ی فعال شدن ربات به بعد).
    """
    cur = db.execute(
        "SELECT COUNT(*) FROM invite_link_joins WHERE chat_id = ? AND link = ?",
        (chat_id, link),
    )
    return cur.fetchone()[0]


@client.on(events.Raw)
async def invite_join_tracker(event):
    """
    هر بار یه عضو جدید (یا تغییر وضعیت عضویت) توی گروه هدف رخ بده، این آپدیت خام
    میاد و اگه عضویت از طریق یه لینک دعوت اختصاصی بوده باشه (فیلد invite پر باشه)،
    اون ورود رو برای اون لینک توی دیتابیس محلی ثبت می‌کنه.
    """
    if not isinstance(event, tltypes.UpdateChannelParticipant):
        return

    try:
        event_chat_id = utils.get_peer_id(tltypes.PeerChannel(event.channel_id))
    except Exception:
        return

    if event_chat_id != TARGET_GROUP_ID:
        return

    invite = getattr(event, "invite", None)
    if invite is None or not getattr(invite, "link", None):
        return  # عضویت از طریق لینک دعوت نبوده (مثلاً اضافه‌شدن دستی یا ریکوئست)

    new_participant = getattr(event, "new_participant", None)
    if new_participant is None:
        return  # این یه خروج یا تغییر دیگه بوده، نه عضویت جدید

    record_invite_join(TARGET_GROUP_ID, invite.link, event.user_id)


@client.on(events.NewMessage(chats=TARGET_GROUP_ID))
async def link_request_handler(event):
    text = (event.raw_text or "").strip()
    if text not in LINK_REQUEST_TRIGGERS:
        return

    user_id = event.sender_id
    link = get_invite_link(TARGET_GROUP_ID, user_id)

    try:
        if not link:
            link = await create_user_invite_link(TARGET_GROUP_ID, user_id)
            save_invite_link(TARGET_GROUP_ID, user_id, link)

        join_count = get_invite_join_count(TARGET_GROUP_ID, link)
    except Exception as e:
        await event.reply(
            "خطا در ساخت/دریافت لینک اختصاصی:\n"
            f"{e}\n\n"
            "مطمئن شو ربات توی گروه ادمینه و دسترسی «دعوت کاربران با لینک» بهش داده شده."
        )
        return

    sender = await event.get_sender()
    first_name = (sender.first_name if sender else None) or "کاربر"
    user_name_link = f'<a href="tg://user?id={user_id}">{html.escape(first_name)}</a>'

    response_text = (
        f'<blockquote>'
        f'• | <tg-emoji emoji-id="{CUSTOM_EMOJI_BULLET_ID}">🔥</tg-emoji> | '
        f'<b>لینک اختصاصی {user_name_link} (آیدی: {user_id})</b>\n'
        f'{html.escape(link)}\n\n'
        f'👥 <b>تعداد اعضای عضو شده از طریق این لینک:</b> {join_count} نفر'
        f'</blockquote>'
    )

    await event.reply(response_text, parse_mode="html")


# دستور ادمین: "حذف لینکا" (یا مشابهش) توی گروه هدف - همه‌ی لینک‌های اختصاصی
# ساخته‌شده رو توی خود تلگرام باطل (revoke) می‌کنه و از دیتابیس پاک می‌کنه.
# بعدش هر کاربری دوباره "لینک بده" بنویسه، چون دیگه رکوردی توی دیتابیس نیست،
# create_user_invite_link یه لینک کاملاً جدید براش می‌سازه.
@client.on(events.NewMessage(chats=TARGET_GROUP_ID, from_users=ADMIN_ID))
async def delete_all_links_handler(event):
    text = (event.raw_text or "").strip()
    if text not in DELETE_LINKS_TRIGGERS:
        return

    peer = await client.get_input_entity(TARGET_GROUP_ID)
    cur = db.execute(
        "SELECT link FROM invite_links WHERE chat_id = ?",
        (TARGET_GROUP_ID,),
    )
    links = [row[0] for row in cur.fetchall()]

    failed = 0
    for link in links:
        try:
            await client(functions.messages.EditExportedChatInviteRequest(
                peer=peer,
                link=link,
                revoked=True,
            ))
        except Exception:
            failed += 1  # شاید قبلاً منقضی/باطل شده بوده، مشکلی نیست

    db.execute("DELETE FROM invite_links WHERE chat_id = ?", (TARGET_GROUP_ID,))
    db.execute("DELETE FROM invite_link_joins WHERE chat_id = ?", (TARGET_GROUP_ID,))
    db.execute("DELETE FROM join_requests WHERE chat_id = ?", (TARGET_GROUP_ID,))
    db.commit()

    reply_text = (
        f"✅ {len(links)} لینک اختصاصی منقضی و از دیتابیس حذف شد.\n"
        f"از الان هر کسی «لینک بده» بنویسه، لینک کاملاً جدید می‌گیره."
    )
    if failed:
        reply_text += f"\n⚠️ {failed} مورد موقع باطل کردن توی تلگرام خطا داد (احتمالاً از قبل باطل بوده)."

    await event.reply(reply_text)


# ============== ویژگی تایید درخواست عضویت توسط ادمین ==============
# چون همه‌ی لینک‌های اختصاصی الان با request_needed=True ساخته میشن، هر کسی
# روی لینک بزنه به‌جای عضویت مستقیم، یه «درخواست عضویت» ثبت میشه. اینجا اون
# درخواست رو می‌گیریم، برای ADMIN_ID توی پیوی با دو دکمه‌ی تایید/رد می‌فرستیم،
# و بر اساس تصمیم ادمین درخواست رو قبول یا رد می‌کنیم.


@client.on(events.Raw)
async def join_request_notifier(event):
    if not isinstance(event, tltypes.UpdateBotChatInviteRequester):
        return

    try:
        event_chat_id = utils.get_peer_id(event.peer)
    except Exception:
        return

    if event_chat_id != TARGET_GROUP_ID:
        return

    user_id = event.user_id
    link = getattr(event.invite, "link", None)
    if link:
        save_join_request(TARGET_GROUP_ID, user_id, link)

    try:
        requester = await client.get_entity(user_id)
        requester_name = utils.get_display_name(requester) or "کاربر"
    except Exception:
        requester_name = "کاربر"

    user_link = f'<a href="tg://user?id={user_id}">{html.escape(requester_name)}</a>'
    text = (
        f'<b>📥 درخواست عضویت جدید</b>\n'
        f'{user_link} (آیدی: {user_id}) با یه لینک اختصاصی درخواست عضویت توی گروه داده.\n'
        f'{html.escape(link) if link else ""}'
    )

    await client.send_message(
        ADMIN_ID,
        text,
        parse_mode="html",
        buttons=[
            [
                Button.inline("✅ تایید عضویت", data=f"jr_ok_{user_id}"),
                Button.inline("❌ رد درخواست", data=f"jr_no_{user_id}"),
            ]
        ],
    )


@client.on(events.CallbackQuery(pattern=r"^jr_(ok|no)_(\d+)$"))
async def join_request_decision_handler(event):
    if event.sender_id != ADMIN_ID:
        await event.answer("فقط مالک ربات می‌تونه تصمیم بگیره.", alert=True)
        return

    decision, user_id_str = event.pattern_match.groups()
    user_id = int(user_id_str)
    approve = decision == "ok"

    peer = await client.get_input_entity(TARGET_GROUP_ID)
    try:
        target_user = await client.get_input_entity(user_id)
        await client(functions.messages.HideChatJoinRequestRequest(
            peer=peer,
            user_id=target_user,
            approve=approve,
        ))
    except Exception as e:
        await event.answer(f"خطا: {e}", alert=True)
        return

    link = get_join_request_link(TARGET_GROUP_ID, user_id)
    if approve and link:
        record_invite_join(TARGET_GROUP_ID, link, user_id)
    delete_join_request(TARGET_GROUP_ID, user_id)

    result_line = "✅ عضویت تایید شد." if approve else "❌ درخواست رد شد."
    try:
        await event.edit(f"{event.message.raw_text}\n\n{result_line}", buttons=None)
    except Exception:
        pass
    await event.answer(result_line)


print("Bot is running...")
client.run_until_disconnected()
