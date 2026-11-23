# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from telethon.tl.custom import Button
from ..utils import format_number, format_change
GOLD_CATEGORIES = {
    "انس": ["انس طلا", "انس نقره", "انس پلاتین", "انس پالادیوم"],
    "طلا": ["طلای 18 عیار", "طلای 24 عیار", "طلای دست دوم", "مثقال طلا", "آبشده نقدی"],
    "سکه": ["سکه امامی", "سکه بهار آزادی", "نیم سکه", "ربع سکه", "سکه گرمی"],
    "نقره": ["گرم نقره ۹۹۹"]
}
GOLD_SYMBOLS = {
    "انس طلا": "🏆",
    "انس نقره": "🥈",
    "انس پلاتین": "⚪",
    "انس پالادیوم": "⭐",
    "طلای 18 عیار": "💍",
    "طلای 24 عیار": "💎",
    "طلای دست دوم": "🔄",
    "گرم نقره ۹۹۹": "✨",
    "مثقال طلا": "⚖️",
    "آبشده نقدی": "💧",
    "سکه امامی": "🏅",
    "سکه بهار آزادی": "🪙",
    "نیم سکه": "🥇",
    "ربع سکه": "🥉",
    "سکه گرمی": "💰"
}
async def handle_gold_command(event, client):
    """Handle /gold command to display all gold prices"""
    data = event.client.gold_data
    if not data:
        await event.respond('متاسفانه در حال حاضر امکان دریافت اطلاعات طلا وجود ندارد. ❌')
        return
    gold_types = data.get('GoldType', {}).get('data', [])
    if not gold_types:
        await event.respond('اطلاعات طلا در حال حاضر در دسترس نیست. ❌')
        return
    header_row = [
        Button.inline("💱 نوع", b'noop_header'),
        Button.inline("💰 قیمت", b'noop_header'),
        Button.inline("📊 تغییر", b'noop_header')
    ]
    all_buttons = [header_row]
    for category, items in GOLD_CATEGORIES.items():
        all_buttons.append([Button.inline(f"✨ {category}", b'noop_section')])
        for item_name in items:
            gold_info = next((g for g in gold_types if g['currencyName'] == item_name), None)
            if gold_info:
                price = format_number(gold_info['livePrice'])
                change = format_change(gold_info['change'])
                symbol = GOLD_SYMBOLS.get(item_name, '💰')
                item_row = [
                    Button.inline(f"{symbol} {item_name}", b'noop'),
                    Button.inline(f"{price} تومان", b'noop'),
                    Button.inline(f"{change}", b'noop')
                ]
                all_buttons.append(item_row)
    footer_buttons = [
        [Button.url("📢 عضویت در کانال ما", "https://t.me/TelebotCraft")],
        [Button.url("➕ افزودن ربات به گروه", f"https://t.me/{(await client.get_me()).username}?startgroup=true")]
    ]
    all_buttons.extend(footer_buttons)
    if gold_types:
        last_update = gold_types[0]['time']
        await event.respond(f"💎 نرخ لحظه‌ای طلا و سکه (آخرین بروزرسانی: {last_update}):", buttons=all_buttons)
    else:
        await event.respond('اطلاعات طلا در حال حاضر در دسترس نیست. ❌')
