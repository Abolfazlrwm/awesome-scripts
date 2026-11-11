# -*- coding: utf-8 -*-
"""
ربات مدیریت پست با دکمه‌های رنگی (Bot API 7.10+)
------------------------------------------------
همه چیز تو همین یه فایله. فقط بخش «تنظیمات» پایین رو پر کن و اجرا کن.

امکانات:
  /start          -> راهنما
  /addbutton      -> اضافه کردن یک دکمه (چنل یا بات) به لیست دائمی
  /listbuttons    -> نمایش لیست دکمه‌های ذخیره‌شده
  /removebutton   -> حذف یک دکمه از لیست (با شماره)
  /newpost        -> ساخت و ارسال پست جدید به چنل هدف (همراه با تمام دکمه‌های ذخیره‌شده)
  /cancel         -> لغو عملیات جاری

نصب پیش‌نیاز:
  pip install requests
"""

import json
import os
import time
import requests

# ============================================================
#                        ⚙️  تنظیمات
# ============================================================

# توکن رباتت رو از BotFather بگیر
# ⚠️ اگه توکن قبلی جایی لو رفته، حتما تو BotFather دستور /revoke بزن و توکن جدید بگیر
BOT_TOKEN = "PUT_YOUR_BOT_TOKEN_HERE"

# آیدی یا یوزرنیم چنلی که پیام‌ها نهایتا اونجا پست میشن (ربات باید ادمین اون چنل باشه)
# چنل خصوصی: عددی که با -100 شروع میشه، مثلا "-1001234567890"
# چنل عمومی: یوزرنیم با @ ، مثلا "@your_channel_username"
TARGET_CHANNEL_ID = "@your_channel_username"

# لیست آیدی عددی ادمین‌هایی که اجازه دارن با ربات کار کنن
# آیدی عددی خودتو از رباتی مثل @userinfobot بگیر
ADMIN_IDS = [123456789]  # آیدی عددی ادمین‌ها (از @userinfobot بگیرید)

# مسیر فایلی که دکمه‌های دائمی توش ذخیره میشه (نیازی به تغییر نیست)
BUTTONS_FILE = "buttons.json"

# ============================================================
#                     پایان بخش تنظیمات
# ============================================================


API_URL = f"https://api.telegram.org/bot{BOT_TOKEN}"

# استایل‌های مجاز دکمه طبق Bot API 7.10+
STYLES = {
    "1": ("primary", "🔵 آبی (primary)"),
    "2": ("success", "🟢 سبز (success)"),
    "3": ("danger", "🔴 قرمز (danger)"),
    "4": (None, "⚪️ پیش‌فرض (بدون رنگ خاص)"),
}


# ---------- ذخیره‌سازی دکمه‌ها روی دیسک ----------
def load_buttons():
    if not os.path.exists(BUTTONS_FILE):
        return []
    with open(BUTTONS_FILE, "r", encoding="utf-8") as f:
        return json.load(f)


def save_buttons(buttons):
    with open(BUTTONS_FILE, "w", encoding="utf-8") as f:
        json.dump(buttons, f, ensure_ascii=False, indent=2)


# ---------- توابع کمکی ارتباط با تلگرام ----------
def send_message(chat_id, text, reply_markup=None, parse_mode="HTML"):
    data = {
        "chat_id": chat_id,
        "text": text,
        "parse_mode": parse_mode,
    }
    if reply_markup is not None:
        data["reply_markup"] = reply_markup
    r = requests.post(f"{API_URL}/sendMessage", json=data)
    return r.json()


def build_keyboard(buttons):
    """buttons: لیستی از دیکشنری {title, url, style} -> هر کدوم یک ردیف جدا"""
    rows = []
    for b in buttons:
        btn = {"text": b["title"], "url": b["url"]}
        if b.get("style"):
            btn["style"] = b["style"]
        rows.append([btn])
    return {"inline_keyboard": rows} if rows else None


# ---------- وضعیت هر کاربر (state machine ساده در حافظه) ----------
sessions = {}  # sessions[chat_id] = {"action": "...", "data": {...}}


def is_admin(chat_id):
    return chat_id in ADMIN_IDS


def reset_session(chat_id):
    sessions.pop(chat_id, None)


HELP_TEXT = (
    "👋 <b>راهنمای ربات مدیریت پست</b>\n\n"
    "🔘 /addbutton — اضافه کردن دکمه‌ی جدید (چنل یا بات) به لیست دائمی\n"
    "📋 /listbuttons — نمایش لیست دکمه‌های ذخیره‌شده\n"
    "🗑 /removebutton — حذف یک دکمه از لیست\n"
    "📝 /newpost — ساخت و ارسال پست جدید به چنل هدف (با همه‌ی دکمه‌ها)\n"
    "❌ /cancel — لغو عملیات جاری"
)


def handle_command(chat_id, text):
    text = text.strip()

    if text == "/start":
        send_message(chat_id, HELP_TEXT)
        return

    if text == "/cancel":
        reset_session(chat_id)
        send_message(chat_id, "عملیات لغو شد.")
        return

    if text == "/addbutton":
        sessions[chat_id] = {"action": "addbutton_title", "data": {}}
        send_message(chat_id, "📝 عنوان دکمه رو بفرست (مثلا: 🌟 چنل ما)")
        return

    if text == "/listbuttons":
        buttons = load_buttons()
        if not buttons:
            send_message(chat_id, "لیست دکمه‌ها خالیه. با /addbutton یکی اضافه کن.")
            return
        lines = ["📋 <b>لیست دکمه‌های ذخیره‌شده:</b>\n"]
        for i, b in enumerate(buttons, start=1):
            style_label = b.get("style") or "پیش‌فرض"
            lines.append(f"{i}. {b['title']}\n   لینک: {b['url']}\n   رنگ: {style_label}")
        send_message(chat_id, "\n\n".join(lines))
        return

    if text == "/removebutton":
        buttons = load_buttons()
        if not buttons:
            send_message(chat_id, "لیست دکمه‌ها خالیه.")
            return
        lines = ["🗑 شماره‌ی دکمه‌ای که می‌خوای حذف بشه رو بفرست:\n"]
        for i, b in enumerate(buttons, start=1):
            lines.append(f"{i}. {b['title']}")
        sessions[chat_id] = {"action": "removebutton_index", "data": {}}
        send_message(chat_id, "\n".join(lines))
        return

    if text == "/newpost":
        buttons = load_buttons()
        sessions[chat_id] = {"action": "newpost_text", "data": {}}
        note = ""
        if not buttons:
            note = "\n\n⚠️ توجه: هنوز هیچ دکمه‌ای اضافه نکردی، پست بدون دکمه ارسال میشه."
        send_message(chat_id, "📝 متن پست رو بفرست:" + note)
        return

    send_message(chat_id, "دستور نامعتبره. برای راهنما /start رو بزن.")


def handle_session_message(chat_id, text):
    session = sessions.get(chat_id)
    if not session:
        return False

    action = session["action"]
    data = session["data"]

    # ---- افزودن دکمه: مرحله ۱ عنوان ----
    if action == "addbutton_title":
        data["title"] = text.strip()
        session["action"] = "addbutton_url"
        send_message(
            chat_id,
            "🔗 حالا لینک چنل یا بات رو بفرست.\n"
            "مثال: https://t.me/mychannel یا https://t.me/mybot",
        )
        return True

    # ---- افزودن دکمه: مرحله ۲ لینک ----
    if action == "addbutton_url":
        url = text.strip()
        if not (url.startswith("http://") or url.startswith("https://") or url.startswith("tg://")):
            send_message(chat_id, "❌ لینک معتبر نیست. باید با http:// یا https:// شروع بشه. دوباره بفرست:")
            return True
        data["url"] = url
        session["action"] = "addbutton_style"
        style_lines = ["🎨 رنگ دکمه رو انتخاب کن، عدد مربوطه رو بفرست:\n"]
        for key, (_, label) in STYLES.items():
            style_lines.append(f"{key}. {label}")
        send_message(chat_id, "\n".join(style_lines))
        return True

    # ---- افزودن دکمه: مرحله ۳ رنگ ----
    if action == "addbutton_style":
        choice = text.strip()
        if choice not in STYLES:
            send_message(chat_id, "❌ عدد نامعتبره. یکی از اعداد ۱ تا ۴ رو بفرست:")
            return True
        style_value, _ = STYLES[choice]
        buttons = load_buttons()
        buttons.append({"title": data["title"], "url": data["url"], "style": style_value})
        save_buttons(buttons)
        reset_session(chat_id)
        send_message(chat_id, f"✅ دکمه «{data['title']}» با موفقیت اضافه شد.")
        return True

    # ---- حذف دکمه ----
    if action == "removebutton_index":
        buttons = load_buttons()
        try:
            idx = int(text.strip()) - 1
            if idx < 0 or idx >= len(buttons):
                raise ValueError
        except ValueError:
            send_message(chat_id, "❌ شماره نامعتبره. دوباره تلاش کن یا /cancel بزن.")
            return True
        removed = buttons.pop(idx)
        save_buttons(buttons)
        reset_session(chat_id)
        send_message(chat_id, f"🗑 دکمه «{removed['title']}» حذف شد.")
        return True

    # ---- ساخت پست جدید: مرحله ۱ متن ----
    if action == "newpost_text":
        data["text"] = text
        session["action"] = "newpost_confirm"
        buttons = load_buttons()
        keyboard = build_keyboard(buttons)
        send_message(chat_id, "👇 پیش‌نمایش پست:")
        send_message(chat_id, data["text"], reply_markup=keyboard)
        send_message(
            chat_id,
            "برای ارسال به چنل هدف بنویس: <b>تایید</b>\nبرای لغو بنویس: /cancel",
        )
        return True

    # ---- ساخت پست جدید: مرحله ۲ تایید ----
    if action == "newpost_confirm":
        if text.strip() == "تایید":
            buttons = load_buttons()
            keyboard = build_keyboard(buttons)
            result = send_message(TARGET_CHANNEL_ID, data["text"], reply_markup=keyboard)
            reset_session(chat_id)
            if result.get("ok"):
                send_message(chat_id, "✅ پست با موفقیت به چنل ارسال شد.")
            else:
                send_message(chat_id, f"❌ خطا در ارسال:\n{result}")
        else:
            send_message(chat_id, "برای تایید ارسال، دقیقا بنویس: تایید\nیا /cancel برای لغو.")
        return True

    return False


def process_update(update):
    message = update.get("message")
    if not message:
        return
    chat_id = message["chat"]["id"]
    text = message.get("text", "")

    if not is_admin(chat_id):
        send_message(chat_id, "⛔️ شما اجازه‌ی استفاده از این ربات رو ندارید.")
        return

    if text.startswith("/"):
        if text.strip() != "/cancel":
            reset_session(chat_id)
        handle_command(chat_id, text)
        return

    if handle_session_message(chat_id, text):
        return

    send_message(chat_id, "برای شروع از /start استفاده کن.")


def main():
    print("ربات روشن شد و در حال گوش دادن به پیام‌هاست ...")
    offset = None
    while True:
        try:
            params = {"timeout": 30}
            if offset is not None:
                params["offset"] = offset
            r = requests.get(f"{API_URL}/getUpdates", params=params, timeout=35)
            resp = r.json()
            if not resp.get("ok"):
                print("خطا در getUpdates:", resp)
                time.sleep(3)
                continue
            for update in resp["result"]:
                offset = update["update_id"] + 1
                process_update(update)
        except requests.exceptions.RequestException as e:
            print("خطای شبکه:", e)
            time.sleep(3)


if __name__ == "__main__":
    main()
