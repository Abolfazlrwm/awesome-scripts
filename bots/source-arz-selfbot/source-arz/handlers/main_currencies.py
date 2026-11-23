# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telethon import events, Button
import math
from typing import List, Tuple
from .constants import BASE_CHART_URL
from .utils import create_currency_buttons, COUNTRY_FLAGS_MAP
ITEMS_PER_PAGE = 10
def format_number(number: str) -> str:
    """Format number with commas"""
    try:
        num_str = str(number).replace(',', '')
        if len(num_str) > 3:
            parts = []
            while num_str:
                parts.append(num_str[-3:])
                num_str = num_str[:-3]
            return ','.join(reversed(parts))
        return num_str
    except:
        return str(number)
def format_change(change: str) -> str:
    """Format price change with arrows"""
    try:
        change = change.strip('()')
        if '(' in change:
            change = change.split('(')[1].split(')')[0]
        parts = change.split()
        if len(parts) > 1:
            change_value = parts[-1].replace(',', '')
        else:
            change_value = change.replace(',', '')
        formatted_change = format_number(change_value)
        if change.startswith('-'):
            return f"📉 {formatted_change}-"
        else:
            return f"📈 {formatted_change}+"
    except:
        return change
def get_main_currency_items(data: dict) -> List[Tuple[str, str, str]]:
    """Get main currency items from data"""
    if not data:
        return []
    currencies = data.get('mainCurrencies', {}).get('data', [])
    result = []
    for c in currencies:
        name = c['currencyName']
        flag = COUNTRY_FLAGS_MAP.get(name, '')
        display_name = f"{flag} {name}".strip()
        result.append((display_name, c['livePrice'], c['change']))
    return result
def get_navigation_buttons(current_page: int, total_pages: int) -> List[List[Button]]:
    """Generate navigation buttons for main currencies"""
    buttons = []
    nav_buttons = []
    if current_page > 1:
        nav_buttons.append(Button.inline("⬅️ قبلی", f"main_curr_{current_page-1}"))
    nav_buttons.append(Button.inline(f"📄 {current_page}/{total_pages}", "noop_page"))
    if current_page < total_pages:
        nav_buttons.append(Button.inline("بعدی ➡️", f"main_curr_{current_page+1}"))
    buttons.append(nav_buttons)
    buttons.append([Button.inline("🏠 صفحه اصلی", "home")])
    buttons.extend([
        [Button.url("📢 عضویت در کانال ما", "https://t.me/TelebotCraft")],
        [Button.url("➕ افزودن ربات به گروه", "tg://resolve?domain={bot_username}&startgroup=true")]
    ])
    return buttons
async def show_main_currencies_page(event, page: int, client):
    """Show a specific page of main currencies"""
    data = client.currency_data
    items = get_main_currency_items(data)
    total_pages = max(1, math.ceil(len(items) / ITEMS_PER_PAGE))
    page = min(max(1, page), total_pages)
    start_idx = (page - 1) * ITEMS_PER_PAGE
    end_idx = start_idx + ITEMS_PER_PAGE
    raw_currencies = data.get('mainCurrencies', {}).get('data', [])
    current_page_raw_currencies = raw_currencies[start_idx:end_idx]
    all_buttons = []
    all_buttons.append([
        Button.inline("💱 نوع", b'noop_header'),
        Button.inline("💰 قیمت", b'noop_header'),
        Button.inline("📊 تغییر", b'noop_header')
    ])
    for currency_data in current_page_raw_currencies:
        all_buttons.extend(create_currency_buttons(currency_data, BASE_CHART_URL))
    nav_buttons = get_navigation_buttons(page, total_pages)
    all_buttons.extend(nav_buttons)
    last_update = "نامشخص"
    if items:
        currencies = data.get('mainCurrencies', {}).get('data', [])
        if currencies:
            last_update = currencies[0].get('time', "نامشخص")
    message = f"💱 نرخ ارزهای اصلی (آخرین بروزرسانی: {last_update})"
    try:
        if isinstance(event, events.CallbackQuery.Event):
            await event.edit(message, buttons=all_buttons)
        else:
            await event.respond(message, buttons=all_buttons)
    except Exception as e:
        if isinstance(event, events.CallbackQuery.Event):
            await event.answer("خطا در نمایش صفحه ارزهای اصلی.", alert=True)
def register_handlers(client):
    """Register all handlers related to main currencies"""
    @client.on(events.CallbackQuery(pattern=r"main_curr_(\d+)"))
    async def handle_main_currency_pagination(event):
        """Handle main currency pagination"""
        try:
            page = int(event.pattern_match.group(1))
            await show_main_currencies_page(event, page, event.client)
            await event.answer()
        except Exception as e:
            await event.answer(f"خطا در تغییر صفحه: {str(e)}", alert=True)
        raise events.StopPropagation
