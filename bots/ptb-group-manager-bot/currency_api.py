# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import requests
import json
from datetime import datetime
import jdatetime
import pytz
import time
import logging

class CurrencyAPI:
    """
    A class to fetch and provide currency exchange rates, cryptocurrency prices, and gold prices
    using free public APIs with proper conversion to Iranian Toman.
    """
    
    def __init__(self):
        """Initialize the CurrencyAPI with default values and settings."""
        self.last_update = datetime.now()
        self.cache_duration = 900  
        self.cached_data = None
        self.logger = self._setup_logger()
        self.api_sources = {
            "currencies": "https://open.er-api.com/v6/latest/USD",
            "crypto": "https://api.coingecko.com/api/v3/simple/price",
            "gold": "https://www.goldapi.io/api/XAU/USD"
        }

        self.usd_to_toman_rate = 83535  
        
    def _setup_logger(self):
        """Set up and configure logger for the class."""
        logger = logging.getLogger('CurrencyAPI')
        logger.setLevel(logging.INFO)
        if not logger.handlers:
            handler = logging.StreamHandler()
            formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
            handler.setFormatter(formatter)
            logger.addHandler(handler)
        return logger

    def _get_iran_datetime(self):
        """Get current date and time in Iran timezone."""
        tehran_tz = pytz.timezone('Asia/Tehran')
        now = datetime.now(tehran_tz)
        

        j_date = jdatetime.datetime.fromgregorian(datetime=now)
        persian_date = j_date.strftime("%A %d %B %Y")
        persian_time = now.strftime("%H:%M")
        
        return persian_date, persian_time

    def _fetch_usd_to_toman_rate(self):
        """Fetch the USD to Iranian Toman exchange rate from bonbast.com API-like sources."""
        try:
            response = requests.get("https://api.nobitex.ir/market/stats",
                                    headers={'User-Agent': 'Mozilla/5.0'})
            response.raise_for_status()
            data = response.json()
            

            if 'stats' in data and 'USDT_IRR' in data['stats']:
                usdt_to_irr = float(data['stats']['USDT_IRR']['latest'])

                return int(usdt_to_irr / 10)
            
            return self.usd_to_toman_rate 
        except Exception as e:
            self.logger.error(f"Error fetching USD to Toman rate: {str(e)}")
            return self.usd_to_toman_rate

    def _fetch_currency_rates(self):
        """Fetch currency exchange rates and convert to Iranian Toman."""
        try:

            self.usd_to_toman_rate = self._fetch_usd_to_toman_rate()
            

            response = requests.get(self.api_sources["currencies"])
            response.raise_for_status()
            data = response.json()
            

            currencies = {
                "usd": {"name": "دلار آمریکا", "price": f"{self.usd_to_toman_rate:,}", "flag": "🇺��"},
                "eur": {"name": "یورو اروپا", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['EUR']):,}", 
                       "flag": "🇪🇺"},
                "gbp": {"name": "پوند انگلیس", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['GBP']):,}", 
                       "flag": "🇬🇧"},
                "chf": {"name": "فرانک سوئیس", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['CHF']):,}", 
                       "flag": "🇨🇭"},
                "cad": {"name": "دلار کانادا", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['CAD']):,}", 
                       "flag": "🇨🇦"},
                "try": {"name": "لیر ترکیه", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['TRY']):,}", 
                       "flag": "🇹🇷"},
                "rub": {"name": "روبل روسیه", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['RUB']):,}", 
                       "flag": "🇷🇺"},
                "cny": {"name": "یوان چین", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['CNY']):,}", 
                       "flag": "🇨🇳"},
                "aed": {"name": "درهم امارات", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['AED']):,}", 
                       "flag": "🇦🇪"},
                "iqd": {"name": "دینار عراق", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['IQD']):,}", 
                       "flag": "🇮🇶"},
                "afn": {"name": "افغانی", 
                       "price": f"{int(self.usd_to_toman_rate / data['rates']['AFN']):,}", 
                       "flag": "🇦🇫"}
            }
            
            return currencies
        except Exception as e:
            self.logger.error(f"Error fetching currency rates: {str(e)}")
            return self._get_fallback_currencies()

    def _fetch_crypto_prices(self):
        """Fetch cryptocurrency prices and convert to USD and Toman."""
        try:
            params = {
                'ids': 'bitcoin,ethereum,litecoin,binancecoin,solana,cardano,tether,xrp,tron,dogecoin,shiba-inu,pepe,bonk,floki,ton,cake',
                'vs_currencies': 'usd'
            }
            response = requests.get(self.api_sources["crypto"], params=params)
            response.raise_for_status()
            data = response.json()
            

            usdt_toman_price = f"{self.usd_to_toman_rate:,}"
            

            crypto = {
                "usdt": {"name": "تتر", "price": usdt_toman_price, "symbol": "💵"},
                "btc": {"name": "بیت کوین", "price": f"{data['bitcoin']['usd']:,.0f}", "symbol": "💰"},
                "eth": {"name": "اتریوم", "price": f"{data['ethereum']['usd']:,.0f}", "symbol": "💰"},
                "bnb": {"name": "بایننس کوین", "price": f"{data['binancecoin']['usd']:,.2f}", "symbol": "💰"},
                "sol": {"name": "سولانا", "price": f"{data['solana']['usd']:,.2f}", "symbol": "💰"},
                "ada": {"name": "کاردانو", "price": f"{data['cardano']['usd']:,.4f}", "symbol": "💰"},
                "xrp": {"name": "ریپل", "price": f"{data['xrp']['usd']:,.4f}", "symbol": "💰"},
                "trx": {"name": "ترون", "price": f"{data['tron']['usd']:,.4f}", "symbol": "💰"},
                "doge": {"name": "دوج کوین", "price": f"{data['dogecoin']['usd']:,.4f}", "symbol": "💰"},
                "shib": {"name": "شیبا", "price": f"{data['shiba-inu']['usd']:.7f}", "symbol": "💰"},
                "pepe": {"name": "پپه", "price": f"{data['pepe']['usd']:.7f}", "symbol": "💰"},
                "bonk": {"name": "بونک", "price": f"{data['bonk']['usd']:.7f}", "symbol": "💰"},
                "floki": {"name": "فلوکی اینو", "price": f"{data['floki']['usd']:.7f}", "symbol": "💰"},
                "ton": {"name": "تون کوین", "price": f"{data['ton']['usd']:,.2f}", "symbol": "💰"},
                "cake": {"name": "پنکیک سواپ", "price": f"{data['cake']['usd']:,.2f}", "symbol": "💰"}
            }
            
            return crypto
        except Exception as e:
            self.logger.error(f"Error fetching crypto prices: {str(e)}")
            return self._get_fallback_crypto()

    def _fetch_gold_prices(self):
        """Fetch gold prices and convert to Iranian Toman."""
        try:

            response = requests.get("https://api.metals.live/v1/spot")
            response.raise_for_status()
            data = response.json()
            
            gold_price_usd = next((item for item in data if "gold" in item), {"price": 2000})["price"]
            

            gold_price_toman = gold_price_usd * self.usd_to_toman_rate
            gold_gram_price_toman = gold_price_toman / 31.1034768  
            gold_mithqal_price_toman = gold_gram_price_toman * 4.25  
            

            coin_premium = 1.15  
            
            gold = {
                "coin": {"name": "سکه", "price": f"{int(gold_gram_price_toman * 8.133 * coin_premium):,}"},  # Emami coin weight
                "mesghal": {"name": "هر مثقال طلا", "price": f"{int(gold_mithqal_price_toman):,}"},
                "gram": {"name": "هر گرم طلا", "price": f"{int(gold_gram_price_toman):,}"},
                "ounce": {"name": "انس طلا", "price": f"{int(gold_price_usd):,}"}
            }
            
            return gold, gold_price_usd
        except Exception as e:
            self.logger.error(f"Error fetching gold prices: {str(e)}")
            return self._get_fallback_gold()

    def _format_price(self, price_value):
        """Format price value to string with thousands separator."""
        if isinstance(price_value, (int, float)):
            if price_value > 1:
                return f"{price_value:,.0f}" if price_value > 100 else f"{price_value:,.3f}"
            return f"{price_value:.7f}".rstrip('0').rstrip('.')
        return str(price_value)

    def _get_fallback_currencies(self):
        """Provide fallback currency data when API fails."""
        return {
            "usd": {"name": "دلار آمریکا", "price": "83,535", "flag": "🇺🇸"},
            "eur": {"name": "یورو اروپا", "price": "97,020", "flag": "🇪🇺"},
            "gbp": {"name": "پوند انگلیس", "price": "113,620", "flag": "🇬🇧"},
            "chf": {"name": "فرانک سوئیس", "price": "102,980", "flag": "🇨🇭"},
            "cad": {"name": "دلار کانادا", "price": "61,320", "flag": "🇨🇦"},
            "try": {"name": "لیر ترکیه", "price": "2,120", "flag": "🇹🇷"},
            "rub": {"name": "روبل روسیه", "price": "1,047", "flag": "🇷🇺"},
            "cny": {"name": "یوان چین", "price": "11,650", "flag": "🇨🇳"},
            "iqd": {"name": "دینار عراق", "price": "58", "flag": "🇮🇶"},
            "aed": {"name": "درهم امارات", "price": "22,847", "flag": "🇦🇪"},
            "afn": {"name": "افغانی", "price": "1,207", "flag": "🇦🇫"}
        }

    def _get_fallback_crypto(self):
        """Provide fallback cryptocurrency data when API fails."""
        return {
            "usdt": {"name": "تتر", "price": "83,535", "symbol": "💵"}, 
            "btc": {"name": "بیت کوین", "price": "105,588", "symbol": "💰"},
            "eth": {"name": "اتریوم", "price": "2,553", "symbol": "💰"},
            "bnb": {"name": "بایننس کوین", "price": "653.94", "symbol": "💰"},
            "sol": {"name": "سولانا", "price": "147.32", "symbol": "💰"},
            "ada": {"name": "کاردانو", "price": "0.6382", "symbol": "💰"},
            "xrp": {"name": "ریپل", "price": "2.1487", "symbol": "💰"},
            "trx": {"name": "ترون", "price": "0.2696", "symbol": "💰"},
            "doge": {"name": "دوج کوین", "price": "0.1781", "symbol": "💰"},
            "shib": {"name": "شیبا", "price": "0.0000119", "symbol": "💰"},
            "pepe": {"name": "پپه", "price": "0.0000109", "symbol": "💰"},
            "bonk": {"name": "بونک", "price": "0.0000149", "symbol": "💰"},
            "floki": {"name": "فلوکی اینو", "price": "0.0000789", "symbol": "💰"},
            "ton": {"name": "تون کوین", "price": "2.99", "symbol": "💰"},
            "cake": {"name": "پنکیک سواپ", "price": "2.43", "symbol": "💰"}
        }

    def _get_fallback_gold(self):
        """Provide fallback gold data when API fails."""
        gold = {
            "coin": {"name": "سکه", "price": "75,890,000"},
            "mesghal": {"name": "هر مثقال طلا", "price": "29,346,000"},
            "gram": {"name": "هر گرم طلا", "price": "6,774,100"},
            "ounce": {"name": "انس طلا", "price": "3,427"}
        }
        return gold, 3427

    def get_current_rates(self):
        """
        Get the current exchange rates for currencies, cryptocurrency, and gold.
        Uses cached data if available and not expired.
        """
        current_time = datetime.now()
        

        if self.cached_data and (current_time - self.last_update).total_seconds() < self.cache_duration:
            self.logger.info("Using cached exchange rates")
            return self.cached_data
        
        self.logger.info("Fetching new exchange rates")
        

        persian_date, persian_time = self._get_iran_datetime()
        
        try:

            currencies = self._fetch_currency_rates()
            crypto = self._fetch_crypto_prices()
            gold, gold_price = self._fetch_gold_prices()
            

            rates = {
                "date": persian_date,
                "time": persian_time,
                "currencies": currencies,
                "gold": gold,
                "crypto": crypto
            }
            
            self.cached_data = rates
            self.last_update = current_time
            
            return rates
        except Exception as e:
            self.logger.error(f"Error getting current rates: {str(e)}")
            

            if self.cached_data:
                return self.cached_data
            else:
                return {
                    "date": persian_date,
                    "time": persian_time,
                    "currencies": self._get_fallback_currencies(),
                    "gold": self._get_fallback_gold()[0],
                    "crypto": self._get_fallback_crypto()
                }

    def format_currency_message(self, rates=None):
        """Format the currency data into a readable message."""
        if rates is None:
            rates = self.get_current_rates()
        
        message = f"""◄ نرخ ارز و طلا در بازار آزاد :
        
• تاریخ : {rates['date']}
• ساعت : {rates['time']}

"""

        for currency_code, currency_data in rates['currencies'].items():
            message += f"• {currency_data['name']} {currency_data['flag']} : {currency_data['price']} تومان\n"
        
        message += "\n~ ~ ~ ~ ~ ~\n"
        

        for gold_code, gold_data in rates['gold'].items():
            message += f"• {gold_data['name']} : {gold_data['price']} تومان\n"
        
        message += "\n~ ~ ~ ~ ~ ~\n"
        message += "◄ قیمت ارزهای دیجیتال:\n\n"
        

        for crypto_code, crypto_data in rates['crypto'].items():
            if crypto_code == "usdt":
                message += f"{crypto_data['symbol']} {crypto_data['name']} : {crypto_data['price']} تومان\n"
            else:
                message += f"{crypto_data['symbol']} {crypto_data['name']} : {crypto_data['price']} دلار\n"
        
        return message 