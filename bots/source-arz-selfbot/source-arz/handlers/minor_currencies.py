# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from telethon.tl.custom import Button
from .utils import format_number, format_change, create_currency_buttons
from .constants import ITEMS_PER_PAGE, BASE_CHART_URL
import math
async def show_minor_currencies_page(event, page_number, client):
    """Display a paginated list of minor currencies."""
    data = client.currency_data
    if not data or 'minorCurrencies' not in data or not data['minorCurrencies'].get('data'):
        await event.edit("متاسفانه اطلاعات ارزهای فرعی در حال حاضر در دسترس نیست. ❌")
        return
    minor_currencies = data['minorCurrencies']['data']
    total_items = len(minor_currencies)
    total_pages = max(1, math.ceil(total_items / ITEMS_PER_PAGE))
    page_number = min(max(1, page_number), total_pages)
    start_index = (page_number - 1) * ITEMS_PER_PAGE
    end_index = start_index + ITEMS_PER_PAGE
    current_page_currencies = minor_currencies[start_index:end_index]
    if not current_page_currencies and page_number > 1:
        await event.answer("صفحه دیگری وجود ندارد.", alert=True)
        return
    elif not current_page_currencies and page_number == 1:
        await event.edit("هیچ ارز فرعی برای نمایش وجود ندارد.")
        return
    all_buttons = []
    all_buttons.append([
        Button.inline("💱 نوع", b'noop_header'),
        Button.inline("💰 قیمت", b'noop_header'),
        Button.inline("📊 تغییر", b'noop_header')
    ])
    for currency in current_page_currencies:
        all_buttons.extend(create_currency_buttons(currency, BASE_CHART_URL))
    navigation_buttons_row = []
    if page_number > 1:
        navigation_buttons_row.append(Button.inline("⬅️ قبلی", data=f"minor_curr_page_{page_number - 1}"))
    navigation_buttons_row.append(Button.inline(f"📄 {page_number}/{total_pages}", data="noop_page_count"))
    if end_index < total_items:
        navigation_buttons_row.append(Button.inline("بعدی ➡️", data=f"minor_curr_page_{page_number + 1}"))
    if navigation_buttons_row:
        all_buttons.append(navigation_buttons_row)
    all_buttons.append([Button.inline("🏠 صفحه اصلی", data="home")])
    message = f"📜 لیست ارزهای فرعی (صفحه {page_number}):"
    try:
        if isinstance(event, events.CallbackQuery.Event):
            await event.edit(message, buttons=all_buttons)
            await event.answer()
        else:
            await event.respond(message, buttons=all_buttons)
    except Exception as e:
        if isinstance(event, events.CallbackQuery.Event):
            await event.answer("خطایی در بروزرسانی لیست ارزهای فرعی رخ داد.", alert=True)
        else:
            await event.respond("خطایی در نمایش لیست ارزهای فرعی رخ داد.")
@events.register(events.CallbackQuery(pattern=r"minor_curr_page_(\d+)"))
async def handle_minor_currency_page_navigation(event):
    """Handle minor currency page navigation via inline buttons."""
    page_number = int(event.pattern_match.group(1))
    event.client.currency_data = event.client.currency_cache.get_data()
    await show_minor_currencies_page(event, page_number, event.client)
def register_handlers(client):
    """Register all handlers for minor currencies."""
    client.add_event_handler(handle_minor_currency_page_navigation)
