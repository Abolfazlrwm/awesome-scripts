# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import time
import requests
import functools
import re
import asyncio
from .base import Plugin
from telethon import events
from .db_utils import execute_query

logger = logging.getLogger(__name__)

CACHE_TTL = 300
MAX_MESSAGE_LENGTH = 3500
MAX_CONVERSION_AMOUNT = 1e9

class FinancePlugin(Plugin):
    PERSIAN_NUMERALS = '۰۱۲۳۴۵۶۷۸۹'
    ENGLISH_NUMERALS = '0123456789'

    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.api_urls = [
            "https://no-limit.qprjz.workers.dev/"
        ]
        self._cache = {'data': None, 'timestamp': 0}

    @staticmethod
    def convert_persian_numbers(text):
        return text.translate(str.maketrans(
            FinancePlugin.PERSIAN_NUMERALS,
            FinancePlugin.ENGLISH_NUMERALS
        ))

    def parse_live_price(self, price_str):
        try:
            cleaned = price_str.replace(',', '').strip()
            return float(cleaned) / 10  # Rial ➜ Toman
        except:
            return None

    def auto_cache(func):
        @functools.wraps(func)
        async def wrapper(self, *args, **kwargs):
            if not self._cache['data'] or time.time() - self._cache['timestamp'] > CACHE_TTL:
                await self.refresh_cache()
            return await func(self, *args, **kwargs)
        return wrapper

    @auto_cache
    async def get_cached_data(self):
        return self._cache['data']

    async def refresh_cache(self):
        for api_url in self.api_urls:
            try:
                res = requests.get(api_url, timeout=10)
                res.raise_for_status()
                self._cache['data'] = res.json()
                self._cache['timestamp'] = time.time()
                return
            except Exception as e:
                logger.error(f"API error: {e}")
        self._cache['data'] = None

    async def _is_authorized(self, user_id: str) -> bool:
        try:
            result = await execute_query(
                "SELECT 1 FROM authorized_users WHERE user_id = %s",
                (user_id,),
                fetch=True
            )
            return bool(result)
        except Exception as e:
            logger.error(f"[AUTH] DB check failed: {e}")
            return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=re.compile(r'^(?:/finance|نرخ\s+ارز)$', re.IGNORECASE)))
        async def finance_handler(event):
            if str(event.sender_id) != self.owner_id:
                return  # non-owner = always blocked

            if not await self._is_authorized(str(event.sender_id)):
                return await self._unauthorized_reply(event)

            await self.send_currency_list(event)

        @self.client.on(events.NewMessage(pattern=re.compile(r'^([\d,\.۰-۹]+)\s+([^\s]+)\s+به\s+([^\s]+)$', re.IGNORECASE)))
        async def convert_handler(event):
            if str(event.sender_id) != self.owner_id:
                return  # non-owner = always blocked

            if not await self._is_authorized(str(event.sender_id)):
                return await self._unauthorized_reply(event)

            try:
                raw = event.pattern_match.group(1)
                amount = float(self.convert_persian_numbers(raw).replace(',', ''))
                if amount > MAX_CONVERSION_AMOUNT:
                    return await event.reply(f"🚫 مقدار عددی بیش از حد بزرگ است. حداکثر: {MAX_CONVERSION_AMOUNT:,.0f}")
                from_cur = event.pattern_match.group(2).lower()
                to_cur = event.pattern_match.group(3).lower()
                await self.convert_currency(event, amount, from_cur, to_cur)
            except Exception as e:
                logger.error(f"[Conversion] Error: {e}")
                await event.reply("❌ خطا در پردازش تبدیل")

    async def _unauthorized_reply(self, event):
        await event.reply(
                    "🚫 سلف شما هنوز ویژه نیست!\n\n"
                    "✨ با دعوت از دوستان خود، سکه جمع کنید و سلف‌بات خود را ویژه کنید.\n"
                    "با فعال‌سازی نسخه VIP، به تمامی پلاگین‌ها و قابلیت‌های پیشرفته دسترسی خواهید داشت.\n\n"
                    "🎁 برای فعال‌سازی، از ربات زیر استفاده کنید:\n"
                    "👉 @VIPSelfsazxBot"
                )
            

    async def send_currency_list(self, event):
        try:
            data = await self.get_cached_data()
            if not data:
                return await event.reply("❌ دریافت داده‌ها ممکن نشد.")

            sections = [
                ('ارز اصلی', 'mainCurrencies', 'main'),
                ('ارز فرعی', 'minorCurrencies', 'minor'),
                ('طلا', 'GoldType', 'gold'),
            ]

            for title, key, section in sections:
                group = data.get(key, {}).get('data', [])
                if not group:
                    continue
                full_text = f"📊 {title}:\n" + "\n".join(self.format_item(i, section) for i in group)
                for chunk in self.chunk_message(full_text):
                    await event.reply(chunk)
                    await asyncio.sleep(0.3)

            await event.reply("✅ تمام داده‌ها ارسال شد.")
        except Exception as e:
            logger.error(f"[FinanceList] Error: {e}")
            await event.reply("❌ خطا در ارسال لیست")

    def format_item(self, item, section):
        try:
            rate = self.parse_live_price(item['livePrice'])
            if rate is None:
                return f"❌ {item.get('currencyName', 'نامشخص')}: خطا"
            display = f"{rate:,.4f}" if rate < 1 else f"{rate:,.0f}"
            prefix = "🪙" if section == 'gold' else "▫️"
            return f"{prefix} {item['currencyName']}: {display} تومن"
        except:
            return "❌ خطا در نمایش"

    def chunk_message(self, msg, max_len=MAX_MESSAGE_LENGTH):
        if len(msg) <= max_len:
            return [msg]
        lines = msg.split('\n')
        chunks, current = [], ""
        for line in lines:
            if len(current) + len(line) + 1 > max_len:
                chunks.append(current)
                current = line + "\n"
            else:
                current += line + "\n"
        if current:
            chunks.append(current)
        return chunks

    def get_all_currency_rates(self):
        rates = {'toman': 1, 'تومن': 1}
        for key in ['mainCurrencies', 'minorCurrencies', 'GoldType']:
            for item in self._cache['data'].get(key, {}).get('data', []):
                name = item.get('currencyName', '').lower()
                rate = self.parse_live_price(item.get('livePrice', ''))
                if rate:
                    rates[name] = rate
                    if name in ['دلار', 'usd', 'دلار آمریکا']:
                        rates['$'] = rates['dollar'] = rate
        return rates

    async def convert_currency(self, event, amount, from_cur, to_cur):
        rates = self.get_all_currency_rates()
        if from_cur not in rates or to_cur not in rates:
            return await event.reply("❌ ارز پشتیبانی نمی‌شود")

        from_rate, to_rate = rates[from_cur], rates[to_cur]
        result = (amount * from_rate) / to_rate
        reverse = to_rate / from_rate

        await event.reply(
            f"💱 تبدیل:\n"
            f"{amount:,.4f} {from_cur} ➜ {result:,.4f} {to_cur}\n"
            f"🔁 1 {to_cur} = {reverse:,.4f} {from_cur}"
        )
