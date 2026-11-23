# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events
from telethon.tl.custom import Button
from .utils import create_currency_buttons
from .constants import ITEMS_PER_PAGE, BASE_CHART_URL
import math
import logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)
async def show_gold_page(event, page_number, client):
    """Display a paginated list of gold items."""
    data = client.currency_data
    logger.info("Full data keys: %s", list(data.keys()) if data else None)
    if not data:
        logger.error("No data available from client.currency_data")
        await event.edit("متاسفانه اطلاعات طلا در حال حاضر در دسترس نیست. ❌")
        return
    gold_type = data.get('GoldType', {})
    logger.info("GoldType structure: %s", gold_type.keys() if isinstance(gold_type, dict) else "Not a dict")
    if not gold_type or not isinstance(gold_type, dict):
        logger.error("Invalid GoldType data structure")
        await event.edit("متاسفانه اطلاعات طلا در حال حاضر در دسترس نیست. ❌")
        return
    gold_items = gold_type.get('data', [])
    logger.info("Number of gold items found: %d", len(gold_items))
    if not gold_items:
        await event.edit("هیچ آیتم طلایی برای نمایش وجود ندارد.")
        return
    total_items = len(gold_items)
    total_pages = max(1, math.ceil(total_items / ITEMS_PER_PAGE))
    page_number = min(max(1, page_number), total_pages)
    start_index = (page_number - 1) * ITEMS_PER_PAGE
    end_index = start_index + ITEMS_PER_PAGE
    current_page_items = gold_items[start_index:end_index]
    if not current_page_items:
        await event.answer("صفحه دیگری وجود ندارد.", alert=True)
        return
    all_buttons = []
    all_buttons.append([
        Button.inline("⚜️ نوع", b'noop_gold_header_name'),
        Button.inline("💰 قیمت", b'noop_gold_header_price'),
        Button.inline("📊 تغییر", b'noop_gold_header_change')
    ])
    for item in current_page_items:
        buttons = create_currency_buttons({
            'currencyName': item.get('currencyName', 'N/A'),
            'livePrice': item.get('livePrice', 'N/A'),
            'change': item.get('change', 'N/A'),
            'slug': ''
        }, '')
        all_buttons.extend(buttons)
    navigation_buttons_row = []
    if page_number > 1:
        navigation_buttons_row.append(Button.inline("⬅️ قبلی", data=f"gold_page_{page_number - 1}"))
    navigation_buttons_row.append(Button.inline(f"📄 {page_number}/{total_pages}", data="noop_gold_page_count"))
    if end_index < total_items:
        navigation_buttons_row.append(Button.inline("بعدی ➡️", data=f"gold_page_{page_number + 1}"))
    if navigation_buttons_row:
        all_buttons.append(navigation_buttons_row)
    all_buttons.append([Button.inline("🏠 صفحه اصلی", data="home")])
    message = f"🥇 لیست قیمت طلا (صفحه {page_number}/{total_pages}):"
    try:
        if isinstance(event, events.CallbackQuery.Event):
            await event.edit(message, buttons=all_buttons)
            await event.answer()
        else:
            await event.respond(message, buttons=all_buttons)
    except Exception as e:
        logger.error(f"Error displaying gold list: {str(e)}")
        if isinstance(event, events.CallbackQuery.Event):
            await event.answer("خطایی در بروزرسانی لیست طلا رخ داد.", alert=True)
        else:
            await event.respond("خطایی در نمایش لیست طلا رخ داد.")
@events.register(events.CallbackQuery(pattern=r"gold_page_(\d+)"))
async def handle_gold_page_navigation(event):
    """Handle gold page navigation via inline buttons."""
    page_number = int(event.pattern_match.group(1))
    event.client.currency_data = event.client.currency_cache.get_data()
    await show_gold_page(event, page_number, event.client)
def register_handlers(client):
    """Register all handlers for gold display."""
    client.add_event_handler(handle_gold_page_navigation)
