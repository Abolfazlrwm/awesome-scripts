# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import time
import requests
from telethon import events
from .base import Plugin
from .db_utils import execute_query

logger = logging.getLogger(__name__)

NOBITEX_STATS_URL = "https://api.nobitex.ir/market/stats"
CACHE_TTL = 300  # 5 minutes

HELP = """
📊 مشاهده قیمت ارز دیجیتال از نوبیتکس 📊

دستورات:

/nobitex [نماد]
🔹 دریافت قیمت لحظه‌ای ارز دیجیتال به تومان و تتر
مثال:
    /nobitex btc
    /nobitex doge
    /nobitex shiba

نرخ [نماد]
🔹 معادل فارسی برای دریافت قیمت

/top50 یا برتر
🔹 نمایش ۵۰ ارز دیجیتال محبوب بازار

راهنما یا /nobitex_help
🔹 نمایش این راهنما

📌 فقط کاربران مجاز می‌توانند از این دستورات استفاده کنند.
"""

class NobitexPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self._cache = {'timestamp': 0, 'data': None}

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
        @self.client.on(events.NewMessage(pattern=r'^(?:/nobitex|نرخ)(?: (\w+))?$'))
        async def nobitex_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not await self._is_authorized(str(event.sender_id)):
                return await self._unauthorized_reply(event)

            symbol = (event.pattern_match.group(1) or "btc").lower()
            data = await self.get_cached_data()
            stats = data.get("stats", {})

            irr_key = f"{symbol}-rls"
            usdt_key = f"{symbol}-usdt"

            msg = f"📊 قیمت لحظه‌ای {symbol.upper()}:\n"
            found = False

            if irr_key in stats:
                try:
                    i = stats[irr_key]
                    latest = int(i['latest']) // 10
                    buy = int(i['bestBuy']) // 10
                    sell = int(i['bestSell']) // 10
                    change = i['dayChange']
                    msg += f"\n🇮🇷 بازار ریال:\n💰 {latest:,} تومان\n🟢 خرید: {buy:,}\n🔴 فروش: {sell:,}\n📉 تغییر: {change}%"
                    found = True
                except:
                    msg += "\n❌ خطا در دریافت اطلاعات ریالی"

            if usdt_key in stats:
                try:
                    i = stats[usdt_key]
                    latest = float(i['latest'])
                    buy = float(i['bestBuy'])
                    sell = float(i['bestSell'])
                    change = i['dayChange']
                    msg += f"\n\n💵 بازار تتر:\n💰 {latest:,.2f} USDT\n🟢 خرید: {buy:,.2f}\n🔴 فروش: {sell:,.2f}\n📉 تغییر: {change}%"
                    found = True
                except:
                    msg += "\n❌ خطا در دریافت اطلاعات تتری"

            if not found:
                msg = "❌ ارز مورد نظر یافت نشد."

            await event.reply(msg)

        @self.client.on(events.NewMessage(pattern=r'^(?:/top50|برتر)$'))
        async def top_50_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            if not await self._is_authorized(str(event.sender_id)):
                return await self._unauthorized_reply(event)

            data = await self.get_cached_data()
            stats = data.get("stats", {})

            coins = [
                (sym, stat)
                for sym, stat in stats.items()
                if sym.endswith("-rls") and 'volumeDst' in stat
            ]

            top = sorted(coins, key=lambda x: float(x[1]['volumeDst']), reverse=True)[:50]

            lines = []
            for sym, info in top:
                coin = sym.replace("-rls", "").upper()
                try:
                    latest = int(info['latest']) // 10
                    lines.append(f"▫️ {coin}: {latest:,} تومان")
                except:
                    continue

            await event.reply("📈 ۵۰ ارز برتر بر اساس حجم بازار:\n\n" + "\n".join(lines))

        @self.client.on(events.NewMessage(pattern=r'^(?:/nobitex_help|راهنما)$'))
        async def help_handler(event):
            await event.reply(HELP)

    async def _unauthorized_reply(self, event):
        await event.reply(
            "🚫 شما مجاز به استفاده از این دستور نیستید.\n"
            "برای فعال‌سازی نسخه VIP:\n"
            "👉 @VIPSelfsazxBot"
        )

    async def get_cached_data(self):
        now = time.time()
        if self._cache['data'] and now - self._cache['timestamp'] < CACHE_TTL:
            return self._cache['data']

        try:
            response = requests.post(NOBITEX_STATS_URL, timeout=10)
            response.raise_for_status()
            all_data = response.json()
            self._cache['data'] = all_data
            self._cache['timestamp'] = now
            return all_data
        except Exception as e:
            logger.error(f"[Nobitex Fetch Error] {e}")
            return self._cache['data'] or {}
