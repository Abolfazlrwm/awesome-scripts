# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from telethon.tl.custom import Button
from .utils import format_number, format_change
TRIGGERS = ['PEN', 'Peruvian Sol', 'Pen', 'peruvian sol', 'pen', 'sol', 'سول پرو']
async def handle_currency(event, client):
    """Handle سول پرو currency requests"""
    data = event.client.currency_data
    if not data:
        await event.respond('متاسفانه در حال حاضر امکان دریافت اطلاعات نرخ ارز وجود ندارد. ❌')
        return
    currencies = data.get('mainCurrencies', {}).get('data', [])
    currency_info = next((c for c in currencies if c['currencyName'] == 'سول پرو'), None)
    if not currency_info:
        currencies = data.get('minorCurrencies', {}).get('data', [])
        currency_info = next((c for c in currencies if c['currencyName'] == 'سول پرو'), None)
    if not currency_info:
        await event.respond('اطلاعات سول پرو در حال حاضر در دسترس نیست. ❌')
        return
    price = format_number(currency_info['livePrice'])
    change = format_change(currency_info['change'])
    lowest = format_number(currency_info['lowest'])
    highest = format_number(currency_info['highest'])
    time = currency_info['time']
    buttons = [
        [Button.inline("💰 قیمت فعلی", b'noop'), Button.inline(f"{price} تومان", b'noop')],
        [Button.inline("📊 تغییرات", b'noop'), Button.inline(f"{change}", b'noop')],
        [Button.inline("⬇️ کمترین", b'noop'), Button.inline(f"{lowest}", b'noop')],
        [Button.inline("⬆️ بیشترین", b'noop'), Button.inline(f"{highest}", b'noop')],
        [Button.inline("🕒 بروزرسانی", b'noop'), Button.inline(f"{time}", b'noop')],
        [Button.url("📢 کانال ما", "https://t.me/TelebotCraft")],
        [Button.url("➕ افزودن ربات به گروه", f"https://t.me/{(await client.get_me()).username}?startgroup=true")]
    ]
    message = f"🇵🇪 نرخ لحظه‌ای سول پرو:"
    await event.respond(message, buttons=buttons)
