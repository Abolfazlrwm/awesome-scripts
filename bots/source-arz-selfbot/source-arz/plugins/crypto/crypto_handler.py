# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Crypto handler module for the currency bot.
This module handles displaying cryptocurrency data and registering handlers.
"""
import logging
import time
import re
import aiohttp
import json
from typing import Dict, Any, List, Optional, Tuple
from telethon import events
from telethon.tl.custom import Button
from .crypto_cache import crypto_cache, POPULAR_CRYPTO_SYMBOLS, CRYPTO_INFO
from .currency_converter import format_number
PERSIAN_DIGITS = {
    '۰': '0',
    '۱': '1',
    '۲': '2',
    '۳': '3',
    '۴': '4',
    '۵': '5',
    '۶': '6',
    '۷': '7',
    '۸': '8',
    '۹': '9',
}
USDT_PRICE_CACHE = {
    'price': None,
    'timestamp': 0,
    'ttl': 300
}
logger = logging.getLogger(__name__)
class CryptoHandler:
    """Handler for cryptocurrency commands and queries"""
    def __init__(self, symbol: str, name: str, icon: str, triggers: List[str]):
        """Initialize the crypto handler
        Args:
            symbol: The crypto symbol (e.g., 'BTCIRT')
            name: The Persian name of the cryptocurrency
            icon: The icon/emoji for the cryptocurrency
            triggers: List of trigger words to activate this handler
        """
        self.symbol = symbol
        self.name = name
        self.icon = icon
        self.triggers = triggers
        self.base_symbol = symbol.split('IRT')[0].split('USDT')[0]
        self.quote_symbol = 'IRT' if 'IRT' in symbol else 'USDT'
        self.quote_name = 'تومان' if self.quote_symbol == 'IRT' else 'دلار'
    def _parse_amount(self, amount_str: str) -> Optional[float]:
        """Parse an amount string, handling Persian digits and commas
        Args:
            amount_str: The amount string to parse
        Returns:
            The parsed amount as a float, or None if parsing failed
        """
        if not amount_str:
            return None
        for persian_digit, english_digit in PERSIAN_DIGITS.items():
            amount_str = amount_str.replace(persian_digit, english_digit)
        amount_str = amount_str.replace(',', '').replace(' ', '')
        try:
            amount = float(amount_str)
            MAX_AMOUNT = 1000000000
            if amount > MAX_AMOUNT:
                logger.warning(f"Amount {amount} exceeds maximum limit of {MAX_AMOUNT}")
                return None
            return amount if amount > 0 else None
        except ValueError:
            logger.error(f"Could not parse amount: {amount_str}")
            return None
    async def handle_crypto(self, event, client, amount_str: Optional[str] = None):
        """Handle cryptocurrency requests
        Args:
            event: The Telegram event
            client: The Telegram client
            amount_str: Optional string representing the amount from the message
        """
        parsed_amount = None
        if amount_str:
            parsed_amount = self._parse_amount(amount_str)
            if parsed_amount is None:
                try:
                    test_amount = float(amount_str.replace(',', '').replace(' ', ''))
                    if test_amount > 1000000000:
                        await event.respond(f"❌ مقدار وارد شده بسیار بزرگ است. لطفاً عددی کمتر از 1,000,000,000 وارد کنید.")
                    else:
                        return
                except:
                    print("Invalid amount")
                return
        else:
            parsed_amount = 1.0
        data = crypto_cache.get_data(self.symbol)
        if 'USDT' in self.symbol and not data:
            usdt_price_data = await get_live_usdt_price()
            if usdt_price_data and usdt_price_data.get('price'):
                usdt_rate = usdt_price_data['price']
                is_live = True
                logger.info(f"Using live USDT data with rate {usdt_rate}")
            else:
                usdt_rate = 42000
                is_live = False
                logger.info(f"Using fallback USDT rate of {usdt_rate}")
            data = {
                'lastTradePrice': str(usdt_rate),
                'asks': [[str(usdt_rate), '1']],
                'bids': [[str(usdt_rate), '1']],
                'timestamp': usdt_price_data.get('timestamp', time.time()) if is_live else time.time(),
                'priceChange': usdt_price_data.get('change', '0'),
                'priceChangePercent': usdt_price_data.get('change_percent', '0'),
                'synthetic': not is_live,
                'live': is_live
            }
            logger.info(f"Created {'live' if is_live else 'synthetic'} USDT data for {self.symbol} with rate {usdt_rate}")
        elif not data:
            crypto_cache._update_cache_for_symbol(self.symbol)
            data = crypto_cache.get_data(self.symbol)
        if not data:
            await event.respond(f'اطلاعات {self.name} در حال حاضر در دسترس نیست. ❌')
            return
        price = self._format_price(data.get('lastTradePrice', '0'))
        best_ask = self._get_best_price(data.get('asks', []), 'ask')
        best_bid = self._get_best_price(data.get('bids', []), 'bid')
        update_time = self._format_update_time(data.get('timestamp', time.time()))
        is_synthetic = data.get('synthetic', False)
        usd_price = ""
        if 'IRT' in self.symbol:
            usdt_symbol = f"{self.base_symbol}USDT"
            usdt_data = crypto_cache.get_data(usdt_symbol)
            if usdt_data and 'lastTradePrice' in usdt_data:
                usd_price = self._format_price(usdt_data.get('lastTradePrice', '0'))
        try:
            rial_to_toman_conversion = 10 if 'IRT' in self.symbol else 1
            unit_price_value = float(data.get('lastTradePrice', '0'))
            if 'IRT' in self.symbol and not data.get('already_converted', False):
                unit_price_value = unit_price_value / rial_to_toman_conversion
                logger.info(f"Converted price from {unit_price_value * rial_to_toman_conversion} Rials to {unit_price_value} Tomans for {self.symbol}")
            total_value = unit_price_value * parsed_amount
            if unit_price_value == 0:
                raw_unit_price = "N/A"
            else:
                raw_unit_price = f"{int(unit_price_value):,}" if unit_price_value >= 1 else f"{unit_price_value:.8f}"
            if total_value == 0:
                raw_total_price = "N/A"
            else:
                raw_total_price = f"{int(total_value):,}" if total_value >= 1 else f"{total_value:.8f}"
            ask_price = float(data.get('asks', [[0, 0]])[0][0]) if data.get('asks') else 0
            bid_price = float(data.get('bids', [[0, 0]])[0][0]) if data.get('bids') else 0
            if 'IRT' in self.symbol and not data.get('already_converted', False):
                ask_price = ask_price / rial_to_toman_conversion
                bid_price = bid_price / rial_to_toman_conversion
            raw_ask = f"{int(ask_price):,}" if ask_price >= 1 else ("N/A" if ask_price == 0 else f"{ask_price:.8f}")
            raw_bid = f"{int(bid_price):,}" if bid_price >= 1 else ("N/A" if bid_price == 0 else f"{bid_price:.8f}")
            data['already_converted'] = True
        except (ValueError, IndexError):
            unit_price_value = 0
            total_value = 0
            raw_unit_price = "N/A"
            raw_total_price = "N/A"
            raw_ask = "N/A"
            raw_bid = "N/A"
        price_change = data.get('priceChange')
        change_emoji = ""
        change_text = ""
        if 'USDT' in self.symbol and parsed_amount != 1.0:
            change_emoji = ""
            change_text = ""
        elif data.get('priceChange') is not None and data.get('priceChangePercent') is not None:
            try:
                price_change = float(data.get('priceChange', 0))
                price_change_percent = float(data.get('priceChangePercent', 0))
                if abs(price_change) < 0.000001:
                    change_emoji = ""
                    change_text = ""
                elif price_change > 0:
                    change_emoji = "🟢 ↗️"
                    change_text = f"+{int(price_change):,} ({price_change_percent:.2f}%)"
                elif price_change < 0:
                    change_emoji = "🔴 ↘️"
                    change_text = f"{int(price_change):,} ({price_change_percent:.2f}%)"
                else:
                    change_emoji = "⚪️ ↔️"
                    change_text = "بدون تغییر (0%)"
            except (ValueError, TypeError) as e:
                logger.error(f"Error formatting change: {e}")
                change_emoji = ""
                change_text = ""
        if parsed_amount == 1.0 and amount_str is None:
            caption = f"{self.icon} **نرخ لحظه‌ای {self.name}:**\n\n"
            caption += f"💰 **قیمت:** {raw_unit_price} {self.quote_name}\n"
        else:
            formatted_amount = format_number(parsed_amount)
            if 'USDT' in self.symbol:
                caption = f"{self.icon} **ارزش {formatted_amount} {self.name}:**\n\n"
                caption += f"💰 **{formatted_amount} = {raw_total_price} {self.quote_name}**\n"
            else:
                caption = f"{self.icon} **ارزش {formatted_amount} {self.name}:**\n\n"
                caption += f"💰 **{formatted_amount} {self.name} = {raw_total_price} {self.quote_name}**\n"
        if 'USDT' in self.symbol and parsed_amount == 1.0 and amount_str is None:
            if is_synthetic:
                caption += "\n💡 **(نرخ تقریبی)**\n"
            elif data.get('live', False):
                caption += "\n💹 **(نرخ زنده)**\n"
        if change_text and not ('USDT' in self.symbol and parsed_amount != 1.0):
            caption += f"{change_emoji} **تغییرات:** {change_text}\n"
        caption += f"📈 **قیمت فروش:** {raw_ask} {self.quote_name}\n"
        caption += f"📉 **قیمت خرید:** {raw_bid} {self.quote_name}\n\n"
        caption += f"🕒 **بروزرسانی:** {update_time}\n\n"
        if usd_price and 'IRT' in self.symbol and parsed_amount == 1.0 and amount_str is None:
            try:
                raw_usd_price = f"{int(usdt_data.get('lastTradePrice', '0')):,}"
            except (ValueError, TypeError):
                raw_usd_price = usdt_data.get('lastTradePrice', '0') if usdt_data else "N/A"
            caption += f"💵 **قیمت دلاری:** {raw_usd_price} دلار\n\n"
        caption += "📢 @TelebotCraft"
        user_id = event.sender_id
        if parsed_amount == 1.0 and amount_str is None:
            logger.info(f"User {user_id} requested price for {self.symbol}")
        else:
            logger.info(f"User {user_id} requested price for {parsed_amount} {self.symbol}")
        if parsed_amount == 1.0 and amount_str is None:
            buttons = [
                [Button.inline(f"💰 قیمت معامله", b'noop'), Button.inline(f"{price} {self.quote_name}", b'noop')],
                [Button.inline(f"📈 قیمت فروش", b'noop'), Button.inline(f"{best_ask} {self.quote_name}", b'noop')],
                [Button.inline(f"📉 قیمت خرید", b'noop'), Button.inline(f"{best_bid} {self.quote_name}", b'noop')],
                [Button.url("📢 کانال ما", "https://t.me/TelebotCraft")]
            ]
        else:
            buttons = [
                [Button.url("📢 @TelebotCraft", "https://t.me/TelebotCraft")]
            ]
        if hasattr(event, 'message_id') and hasattr(event, 'edit'):
            await event.edit(caption, buttons=buttons)
        else:
            await event.respond(caption, buttons=buttons)
    def _format_price(self, price_str: str) -> str:
        """Format the price string with commas and convert large numbers to text
        Args:
            price_str: The price string from the API
        Returns:
            Formatted price string with text for large numbers
        """
        try:
            price_float = float(price_str)
            if 'IRT' in self.symbol:
                price_float = price_float / 10
            price = int(price_float) if price_float == int(price_float) else price_float
            if price >= 1_000_000_000_000:
                return f"{price / 1_000_000_000_000:.2f} تریلیون"
            elif price >= 1_000_000_000:
                return f"{price / 1_000_000_000:.2f} میلیارد"
            elif price >= 1_000_000:
                return f"{price / 1_000_000:.2f} میلیون"
            elif price >= 1_000:
                return f"{price / 1_000:.2f} هزار"
            else:
                return f"{price:,}"
        except (ValueError, TypeError):
            return price_str
    def _get_best_price(self, orders: List[List[str]], order_type: str) -> str:
        """Get the best ask or bid price from the orders
        Args:
            orders: List of orders from the API
            order_type: Either 'ask' or 'bid'
        Returns:
            The best price formatted with commas
        """
        if not orders:
            return "N/A"
        best_price = orders[0][0]
        if 'IRT' in self.symbol:
            try:
                best_price_float = float(best_price) / 10
                best_price = str(best_price_float)
            except (ValueError, TypeError):
                pass
        return self._format_price(best_price)
    def _format_update_time(self, timestamp: float) -> str:
        """Format the update time as a human-readable string
        Args:
            timestamp: Unix timestamp of the last update
        Returns:
            Human-readable time string
        """
        now = time.time()
        diff = now - timestamp
        if diff < 60:
            return "چند ثانیه پیش"
        elif diff < 3600:
            minutes = int(diff / 60)
            return f"{minutes} دقیقه پیش"
        else:
            hours = int(diff / 3600)
            return f"{hours} ساعت پیش"
    def _format_symbol(self) -> str:
        """Format the symbol for display
        Returns:
            Formatted symbol string
        """
        if 'IRT' in self.symbol:
            return f"{self.base_symbol}/IRT"
        elif 'USDT' in self.symbol:
            return f"{self.base_symbol}/USDT"
        return self.symbol
async def show_crypto_list(event, client):
    """Show the list of available cryptocurrencies
    Args:
        event: The Telegram event
        client: The Telegram client
    """
    try:
        logger.info("Showing crypto list")
        all_symbols = crypto_cache.get_all_symbols()
        logger.info(f"Got {len(all_symbols)} symbols from cache")
        irt_symbols = [s for s in all_symbols if 'IRT' in s]
        usdt_symbols = [s for s in all_symbols if 'USDT' in s]
        logger.info(f"Split into {len(irt_symbols)} IRT symbols and {len(usdt_symbols)} USDT symbols")
        custom_emojis = {
            'BTC': '₿',
            'ETH': 'Ξ',
            'LTC': 'Ł',
            'USDT': '₮',
            'BNB': '🔶',
            'SOL': '☀️',
            'ADA': '🦊',
            'XRP': '💧',
            'DOGE': 'Ð',
            'DOT': '◉',
            'SHIB': '🐕',
            'MATIC': '🔷',
            'AVAX': '🔺',
            'TRX': '♦️',
        }
        irt_buttons = []
        for symbol in irt_symbols:
            base_symbol = symbol.split('IRT')[0]
            info = crypto_cache.get_crypto_info(base_symbol)
            emoji = custom_emojis.get(base_symbol, info.get('icon', '🔸'))
            name = info.get('name', base_symbol)
            irt_buttons.append(
                Button.inline(f"{emoji} {name}", f"crypto_{symbol}")
            )
        usdt_buttons = []
        for symbol in usdt_symbols:
            base_symbol = symbol.split('USDT')[0]
            info = crypto_cache.get_crypto_info(base_symbol)
            emoji = custom_emojis.get(base_symbol, info.get('icon', '🔹'))
            name = info.get('name', base_symbol)
            usdt_buttons.append(
                Button.inline(f"{emoji} {name}", f"crypto_{symbol}")
            )
        irt_rows = [irt_buttons[i:i+2] for i in range(0, len(irt_buttons), 2)]
        usdt_rows = [usdt_buttons[i:i+2] for i in range(0, len(usdt_buttons), 2)]
        buttons = []
        buttons.extend(irt_rows)
        buttons.extend(usdt_rows)
        buttons.append([Button.inline("🏠 بازگشت به منوی اصلی", b"home")])
        buttons.append([Button.url("📢 کانال ما", "https://t.me/TelebotCraft")])
        message = "🪙 <b>لیست ارزهای دیجیتال:</b>\n\nلطفا یکی از ارزهای دیجیتال را انتخاب کنید:"
        logger.info("Sending crypto list message with buttons")
        await event.respond(message, buttons=buttons, parse_mode='html')
        logger.info("Crypto list message sent successfully")
    except Exception as e:
        logger.error(f"Error in show_crypto_list: {str(e)}", exc_info=True)
        await event.answer(f"خطا در نمایش لیست ارزهای دیجیتال: {str(e)}", alert=True)
async def handle_usdt_price(event, client):
    """Handle USDT price requests
    Args:
        event: The Telegram event
        client: The Telegram client
    """
    handler = CryptoHandler(
        symbol="USDTIRT",
        name="تتر",
        icon="💵",
        triggers=["usdt", "تتر"]
    )
    await handler.handle_crypto(event, client)
    return True
async def handle_crypto_button(event, client):
    """Handle crypto button presses
    Args:
        event: The Telegram event
        client: The Telegram client
    """
    data = event.data.decode('utf-8')
    if not data.startswith('crypto_'):
        return
    symbol = data.replace('crypto_', '')
    current_user_id = event.sender_id
    parts = data.split('crypto_')[1].split('_')
    if len(parts) >= 2:
        symbol = parts[0]
        try:
            original_user_id = int(parts[1])
            if current_user_id != original_user_id:
                await event.answer("این دکمه برای شما نیست! لطفاً خودتان قیمت را درخواست کنید.", alert=True)
                return
        except (ValueError, IndexError):
            symbol = data.replace('crypto_', '')
    else:
        symbol = data.split('crypto_')[1]
    base_symbol = symbol.split('IRT')[0].split('USDT')[0]
    info = crypto_cache.get_crypto_info(base_symbol)
    handler = CryptoHandler(
        symbol=symbol,
        name=info.get('name', base_symbol),
        icon=info.get('icon', ''),
        triggers=[base_symbol]
    )
    await handler.handle_crypto(event, client)
async def get_live_usdt_price() -> Dict[str, Any]:
    """Get the live USDT price from external APIs
{{ ... }}
    Returns:
        Dictionary with price, timestamp, and other data in Tomans
    """
    global USDT_PRICE_CACHE
    current_time = time.time()
    if USDT_PRICE_CACHE['price'] and (current_time - USDT_PRICE_CACHE['timestamp']) < USDT_PRICE_CACHE['ttl']:
        logger.info(f"Using cached USDT price: {USDT_PRICE_CACHE['price']} Tomans")
        return USDT_PRICE_CACHE
    apis = [
        {
            'url': 'https://api.nobitex.ir/v2/trades/USDTIRT',
            'parser': parse_nobitex_usdt
        },
        {
            'url': 'https://api.tetherland.com/currencies',
            'parser': parse_tetherland_usdt
        },
        {
            'url': 'https://api.exir.io/v1/ticker/usdt-irt',
            'parser': parse_exir_usdt
        }
    ]
    result = None
    for api in apis:
        try:
            async with aiohttp.ClientSession() as session:
                async with session.get(api['url'], timeout=5) as response:
                    if response.status == 200:
                        data = await response.text()
                        result = api['parser'](data)
                        if result and result.get('price'):
                            USDT_PRICE_CACHE = result
                            USDT_PRICE_CACHE['timestamp'] = current_time
                            logger.info(f"Updated USDT price cache: {result['price']} from {api['url']}")
                            return result
        except Exception as e:
            logger.error(f"Error fetching USDT price from {api['url']}: {str(e)}")
    return None
def parse_nobitex_usdt(data: str) -> Dict[str, Any]:
    """Parse USDT price data from Nobitex API"""
    try:
        json_data = json.loads(data)
        trades = json_data.get('trades', [])
        if trades:
            latest_trade = trades[0]
            price = float(latest_trade.get('price', 0))
            price = price / 10
            return {
                'price': price,
                'change': '0',
                'change_percent': '0',
                'source': 'nobitex'
            }
    except Exception as e:
        logger.error(f"Error parsing Nobitex data: {str(e)}")
    return None
def parse_tetherland_usdt(data: str) -> Dict[str, Any]:
    """Parse USDT price data from Tetherland API"""
    try:
        json_data = json.loads(data)
        usdt_data = next((c for c in json_data if c.get('symbol') == 'USDT'), None)
        if usdt_data:
            price = float(usdt_data.get('price', 0))
            price = price / 10
            change = float(usdt_data.get('change', '0')) / 10 if usdt_data.get('change') else '0'
            return {
                'price': price,
                'change': str(change),
                'change_percent': usdt_data.get('changePercent', '0'),
                'source': 'tetherland'
            }
    except Exception as e:
        logger.error(f"Error parsing Tetherland data: {str(e)}")
    return None
def parse_exir_usdt(data: str) -> Dict[str, Any]:
    """Parse USDT price data from Exir API"""
    try:
        json_data = json.loads(data)
        price = float(json_data.get('last', 0))
        price = price / 10
        last_price = float(json_data.get('last', 0)) / 10
        open_price = float(json_data.get('open', 0)) / 10
        change = last_price - open_price
        return {
            'price': price,
            'change': str(change),
            'change_percent': json_data.get('percentChange', '0'),
            'source': 'exir'
        }
    except Exception as e:
        logger.error(f"Error parsing Exir data: {str(e)}")
    return None
def register_crypto_handlers(client):
    logger.info("Registering crypto handlers...")
    try:
        for symbol_pair in POPULAR_CRYPTO_SYMBOLS:
            base_symbol = None
            quote_currency = None
            if symbol_pair.endswith("IRT"):
                base_symbol = symbol_pair[:-3]
                quote_currency = "IRT"
            elif symbol_pair.endswith("USDT") and symbol_pair != "USDT":
                base_symbol = symbol_pair[:-4]
                quote_currency = "USDT"
            else:
                continue
            if not base_symbol:
                continue
            info = crypto_cache.get_crypto_info(base_symbol)
            if symbol_pair == "USDTIRT":
                logger.info(f"[USDTIRT_DEBUG] Processing symbol_pair: {symbol_pair}")
                logger.info(f"[USDTIRT_DEBUG]   Base symbol: {base_symbol}, Quote: {quote_currency}")
                logger.info(f"[USDTIRT_DEBUG]   Info for {base_symbol}: {info}")
            triggers = []
            triggers.append(base_symbol)
            if info.get('name'):
                triggers.append(info.get('name'))
                triggers.append(f"قیمت {info.get('name')}")
                triggers.append(f"نرخ {info.get('name')}")
            if quote_currency == "IRT":
                triggers.append(f"{base_symbol}IRT")
                triggers.append(f"{base_symbol}/IRT")
                triggers.append(f"{base_symbol} IRT")
                if info.get('name'):
                    triggers.append(f"{info.get('name')} تومان")
                    triggers.append(f"{info.get('name')} به تومان")
            elif quote_currency == "USDT":
                triggers.append(f"{base_symbol}USDT")
                triggers.append(f"{base_symbol}/USDT")
                triggers.append(f"{base_symbol} USDT")
                if info.get('name'):
                    triggers.append(f"{info.get('name')} دلار")
                    triggers.append(f"{info.get('name')} به دلار")
            handler_instance = CryptoHandler(
                symbol=symbol_pair,
                name=info.get('name', base_symbol),
                icon=info.get('icon', ''),
                triggers=triggers
            )
            if symbol_pair == "USDTIRT":
                logger.info(f"[USDTIRT_DEBUG]   Generated triggers for {symbol_pair}: {triggers}")
            for trigger_word in triggers:
                if not trigger_word:
                    continue
                pattern_regex = rf"^(?:([۰-۹\d\.,\s]+)\s*)?{re.escape(trigger_word)}(?:\s*([۰-۹\d\.,\s]+))?$"
                if symbol_pair == "USDTIRT":
                    logger.info(f"[USDTIRT_DEBUG]     Registering trigger: '{trigger_word}' with pattern: {pattern_regex}")
                async def specific_handler(event, current_handler=handler_instance, current_trigger=trigger_word):
                    match = event.pattern_match
                    amount_str_before = match.group(1)
                    amount_str_after = match.group(2)
                    amount_str = None
                    if amount_str_before and amount_str_before.strip():
                        amount_str = amount_str_before.strip()
                    elif amount_str_after and amount_str_after.strip():
                        amount_str = amount_str_after.strip()
                    await current_handler.handle_crypto(event, client, amount_str=amount_str)
                    raise events.StopPropagation
                client.add_event_handler(specific_handler, events.NewMessage(pattern=re.compile(pattern_regex, re.IGNORECASE)))
        logger.info(f"Successfully registered crypto handlers.")
    except Exception as e:
        logger.error(f"Error during crypto handler registration: {e}", exc_info=True)
        pass
def initialize_crypto_plugin(client):
    """Initializes the crypto plugin by registering handlers and starting cache."""
    crypto_cache.start()
    register_crypto_handlers(client)
    client.add_event_handler(
        lambda e: show_crypto_list(e, client),
        events.NewMessage(pattern=r'^/crypto$')
    )
    client.add_event_handler(
        lambda e: handle_crypto_button(e, client),
        events.CallbackQuery(pattern=r'^crypto_')
    )
    client.add_event_handler(
        lambda e: handle_usdt_price(e, client),
        events.NewMessage(pattern=r'(?i)^(usdt|تتر|تتر به تومان|قیمت تتر|نرخ تتر)$')
    )
    logger.info("Crypto plugin initialized successfully")
