# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Crypto cache module for the currency bot.
This module handles fetching and caching cryptocurrency data from the Nobitex API.
"""
import time
import json
import logging
import threading
import requests
from typing import Dict, Any, Optional, List
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger('CryptoCache')
POPULAR_CRYPTO_SYMBOLS = [
    'BTCIRT', 'ETHIRT', 'LTCIRT', 'USDTIRT', 'XRPIRT', 'BCHIRT', 'BNBIRT', 'EOSIRT', 'XLMIRT', 'ETCIRT',
    'TRXIRT', 'DOGEIRT', 'UNIIRT', 'DAIIRT', 'LINKIRT', 'DOTIRT', 'AAVEIRT', 'ADAIRT', 'SHIBIRT', 'FTMIRT',
    'MATICIRT', 'AXSIRT', 'MANAIRT', 'SANDIRT', 'AVAXIRT', 'MKRIRT', 'GMTIRT', 'USDCIRT', 'CHZIRT', 'GRTIRT',
    'CRVIRT', 'EGLDIRT', 'GALIRT', 'HBARIRT', 'IMXIRT', 'WBTCIRT', 'ONEIRT', 'ENSIRT', '1M_BTTIRT', 'SUSHIIRT',
    'LDOIRT', 'ZROIRT', 'STORJIRT', 'ANTIRT', '100K_FLOKIIRT', 'GLMIRT', 'XMRIRT', 'OMIRT', 'RDNTIRT', 'MAGICIRT',
    'TIRT', 'ATOMIRT', 'NOTIRT', 'CVXIRT', 'XTZIRT', 'FILIRT', 'UMAIRT', '1B_BABYDOGEIRT', 'BANDIRT', 'SSVIRT',
    'DAOIRT', 'BLURIRT', 'GMXIRT', 'WIRT', 'SKLIRT', 'SNTIRT', 'NMRIRT', 'API3IRT', 'CVCIRT', 'WLDIRT', 'SOLIRT',
    'QNTIRT', 'GRTIRT', 'FETIRT', 'AGIXIRT', 'LPTIRT', 'SLPIRT', 'COMPIRT', 'MEMEIRT', 'BATIRT', 'SNXIRT', 'TRBIRT',
    '1INCHIRT', 'RSRIRT', 'RNDRIRT', 'YFIIRT', 'MDTIRT', 'LRCIRT', '1M_PEPEIRT', 'BICOIRT', 'ETHFIIRT', 'APEIRT',
    '1M_NFTIRT', 'ARBIRT', 'DYDXIRT', 'BALIRT', 'TONIRT', 'APTIRT', 'CELRIRT', 'ALGOIRT', 'NEARIRT', 'ZRXIRT',
    'MASKIRT', 'EGALAIRT', 'FLOWIRT', 'OMGIRT', 'WOOIRT', 'ENJIRT', 'JSTIRT',
    'BTCUSDT', 'ETHUSDT', 'LTCUSDT', 'XRPUSDT', 'BCHUSDT', 'BNBUSDT', 'EOSUSDT', 'XLMUSDT', 'ETCUSDT', 'TRXUSDT',
    'PMNUSDT', 'DOGEUSDT', 'UNIUSDT', 'DAIUSDT', 'LINKUSDT', 'DOTUSDT', 'AAVEUSDT', 'ADAUSDT', 'SHIBUSDT', 'FTMUSDT',
    'MATICUSDT', 'AXSUSDT', 'MANAUSDT', 'SANDUSDT', 'AVAXUSDT', 'MKRUSDT', 'GMTUSDT', 'USDCUSDT', 'BANDUSDT', 'COMPUSDT',
    'HBARUSDT', 'WBTCUSDT', 'GLMUSDT', 'ATOMUSDT', 'AEVOUSDT', 'RSRUSDT', 'API3USDT', 'ENSUSDT', 'MAGICUSDT', 'NOTUSDT',
    'ONEUSDT', 'EGALAUSDT', 'XTZUSDT', 'FLOWUSDT', 'GALUSDT', 'CVCUSDT', 'NMRUSDT', 'BATUSDT', 'TRBUSDT', 'RDNTUSDT',
    'YFIUSDT', 'TUSDT', 'QNTUSDT', 'IMXUSDT', 'GMXUSDT', 'ETHFIUSDT', 'WLDUSDT', 'MEMEUSDT', 'SOLUSDT', 'BALUSDT',
    'DAOUSDT', 'TONUSDT', 'OMUSDT', 'SLPUSDT', 'SSVUSDT', 'RNDRUSDT', 'AGLDUSDT', 'NEARUSDT', 'WOOUSDT', 'CRVUSDT',
    'MDTUSDT', 'EGLDUSDT', 'LPTUSDT', 'BICOUSDT', 'ANTUSDT', '1INCHUSDT', 'APEUSDT', 'LRCUSDT', 'WUSDT', 'BLURUSDT',
    'CELRUSDT', 'CVXUSDT', '100K_FLOKIUSDT', 'JSTUSDT', 'ZROUSDT', 'ARBUSDT', 'APTUSDT', '1M_NFTUSDT', 'UMAUSDT',
    'SKLUSDT', 'ZRXUSDT', 'SUSHIUSDT', 'FETUSDT', 'ALGOUSDT', '1M_PEPEUSDT', '1B_BABYDOGEUSDT', 'MASKUSDT', '1M_BTTUSDT',
    'STORJUSDT', 'XMRUSDT', 'SNTUSDT', 'FILUSDT', 'ENJUSDT', 'OMGUSDT', 'CHZUSDT', 'DYDXUSDT', 'AGIXUSDT', 'LDOUSDT'
]
CRYPTO_INFO = {
    'BTC': {'name': 'بیت کوین', 'icon': '₿'},
    'ETH': {'name': 'اتریوم', 'icon': 'Ξ'},
    'LTC': {'name': 'لایت کوین', 'icon': 'Ł'},
    'USDT': {'name': 'تتر', 'icon': '₮'},
    'XRP': {'name': 'ریپل', 'icon': 'XRP'},
    'BCH': {'name': 'بیت کوین کش', 'icon': 'BCH'},
    'BNB': {'name': 'بایننس کوین', 'icon': 'BNB'},
    'DOGE': {'name': 'دوج کوین', 'icon': 'Ð'},
    'ADA': {'name': 'کاردانو', 'icon': 'ADA'},
    'SHIB': {'name': 'شیبا اینو', 'icon': 'SHIB'},
    'SOL': {'name': 'سولانا', 'icon': 'SOL'},
    'DOT': {'name': 'پولکادات', 'icon': 'DOT'},
    'MATIC': {'name': 'پالیگان', 'icon': 'MATIC'},
    'AVAX': {'name': 'آوالانچ', 'icon': 'AVAX'},
    'EOS': {'name': 'ایاس', 'icon': 'EOS'},
    'XLM': {'name': 'استلار', 'icon': 'XLM'},
    'ETC': {'name': 'اتریوم کلاسیک', 'icon': 'ETC'},
    'TRX': {'name': 'ترون', 'icon': 'TRX'},
    'UNI': {'name': 'یونی سواپ', 'icon': 'UNI'},
    'DAI': {'name': 'دای', 'icon': 'DAI'},
    'LINK': {'name': 'چین لینک', 'icon': 'LINK'},
    'AAVE': {'name': 'آوه', 'icon': 'AAVE'},
    'FTM': {'name': 'فانتوم', 'icon': 'FTM'},
    'AXS': {'name': 'اکسی اینفینیتی', 'icon': 'AXS'},
    'MANA': {'name': 'دیسنترالند', 'icon': 'MANA'},
    'SAND': {'name': 'سندباکس', 'icon': 'SAND'},
    'MKR': {'name': 'میکر', 'icon': 'MKR'},
    'GMT': {'name': 'استپن', 'icon': 'GMT'},
    'USDC': {'name': 'یو اس دی کوین', 'icon': 'USDC'},
    'CHZ': {'name': 'چیلیز', 'icon': 'CHZ'},
    'GRT': {'name': 'گراف', 'icon': 'GRT'},
    'CRV': {'name': 'کرو', 'icon': 'CRV'},
    'BAND': {'name': 'بند پروتکل', 'icon': 'BAND'},
    'COMP': {'name': 'کامپاند', 'icon': 'COMP'},
    'EGLD': {'name': 'الروند', 'icon': 'EGLD'},
    'HBAR': {'name': 'هدرا', 'icon': 'HBAR'},
    'GAL': {'name': 'گالا', 'icon': 'GAL'},
    'WBTC': {'name': 'رپد بیت کوین', 'icon': 'WBTC'},
    'IMX': {'name': 'ایموتابل ایکس', 'icon': 'IMX'},
    'ONE': {'name': 'هارمونی', 'icon': 'ONE'},
    'GLM': {'name': 'گولم', 'icon': 'GLM'},
    'ENS': {'name': 'انس', 'icon': 'ENS'},
    '1M_BTT': {'name': 'بیت تورنت', 'icon': 'BTT'},
    'SUSHI': {'name': 'سوشی سواپ', 'icon': 'SUSHI'},
    'LDO': {'name': 'لیدو', 'icon': 'LDO'},
    'ATOM': {'name': 'کازموس', 'icon': 'ATOM'},
    'ZRO': {'name': 'زرو', 'icon': 'ZRO'},
    'STORJ': {'name': 'استورج', 'icon': 'STORJ'},
    'ANT': {'name': 'آراگون', 'icon': 'ANT'},
    'AEVO': {'name': 'آیوو', 'icon': 'AEVO'},
    '100K_FLOKI': {'name': 'فلوکی', 'icon': 'FLOKI'},
    'RSR': {'name': 'ریزرو رایتس', 'icon': 'RSR'},
    'API3': {'name': 'ای پی آی 3', 'icon': 'API3'},
    'XMR': {'name': 'مونرو', 'icon': 'XMR'},
    'OM': {'name': 'مانترا دائو', 'icon': 'OM'},
    'RDNT': {'name': 'رادینت', 'icon': 'RDNT'},
    'MAGIC': {'name': 'مجیک', 'icon': 'MAGIC'},
    'T': {'name': 'تراشولد', 'icon': 'T'},
    'NOT': {'name': 'نوتیون', 'icon': 'NOT'},
    'CVX': {'name': 'کانوکس', 'icon': 'CVX'},
    'XTZ': {'name': 'تزوس', 'icon': 'XTZ'},
    'FIL': {'name': 'فایل کوین', 'icon': 'FIL'},
    'UMA': {'name': 'یو ام ای', 'icon': 'UMA'},
    '1B_BABYDOGE': {'name': 'بیبی دوج', 'icon': 'BABYDOGE'},
    'SSV': {'name': 'اس اس وی', 'icon': 'SSV'},
    'DAO': {'name': 'دائو میکر', 'icon': 'DAO'},
    'BLUR': {'name': 'بلور', 'icon': 'BLUR'},
    'EGALA': {'name': 'ایگالا', 'icon': 'EGALA'},
    'GMX': {'name': 'جی ام ایکس', 'icon': 'GMX'},
    'FLOW': {'name': 'فلو', 'icon': 'FLOW'},
    'W': {'name': 'رپد', 'icon': 'W'},
    'CVC': {'name': 'سیویک', 'icon': 'CVC'},
    'NMR': {'name': 'نیومرایر', 'icon': 'NMR'},
    'SKL': {'name': 'اسکیل', 'icon': 'SKL'},
    'SNT': {'name': 'استاتوس', 'icon': 'SNT'},
    'BAT': {'name': 'بیسیک اتنشن توکن', 'icon': 'BAT'},
    'TRB': {'name': 'تلور', 'icon': 'TRB'},
    'WLD': {'name': 'ورلد کوین', 'icon': 'WLD'},
    'YFI': {'name': 'یرن فایننس', 'icon': 'YFI'},
    'QNT': {'name': 'کوانت', 'icon': 'QNT'},
    'FET': {'name': 'فتچ', 'icon': 'FET'},
    'AGIX': {'name': 'سینگولاریتی نت', 'icon': 'AGIX'},
    'LPT': {'name': 'لیوپیر', 'icon': 'LPT'},
    'SLP': {'name': 'اسموث لاو پوشن', 'icon': 'SLP'},
    'MEME': {'name': 'میم کوین', 'icon': 'MEME'},
    'BAL': {'name': 'بالانسر', 'icon': 'BAL'},
    'TON': {'name': 'تون کوین', 'icon': 'TON'},
    'SNX': {'name': 'سینتتیکس', 'icon': 'SNX'},
    '1INCH': {'name': 'وان اینچ', 'icon': '1INCH'},
    'RNDR': {'name': 'رندر', 'icon': 'RNDR'},
    'AGLD': {'name': 'ادونچر گلد', 'icon': 'AGLD'},
    'NEAR': {'name': 'نیر پروتکل', 'icon': 'NEAR'},
    'WOO': {'name': 'وو نتورک', 'icon': 'WOO'},
    'MDT': {'name': 'میزورابل دیتا توکن', 'icon': 'MDT'},
    'LRC': {'name': 'لوپرینگ', 'icon': 'LRC'},
    'BICO': {'name': 'بیکانومی', 'icon': 'BICO'},
    '1M_PEPE': {'name': 'پپه', 'icon': 'PEPE'},
    'ETHFI': {'name': 'اتریوم فای', 'icon': 'ETHFI'},
    'APE': {'name': 'اپ کوین', 'icon': 'APE'},
    '1M_NFT': {'name': 'ان اف تی', 'icon': 'NFT'},
    'ARB': {'name': 'آربیتروم', 'icon': 'ARB'},
    'CELR': {'name': 'سلر نتورک', 'icon': 'CELR'},
    'DYDX': {'name': 'دیدکس', 'icon': 'DYDX'},
    'JSTUSDT': {'name': 'جاست', 'icon': 'JST'},
    'ZRX': {'name': 'زیرو ایکس', 'icon': 'ZRX'},
    'ALGO': {'name': 'الگوراند', 'icon': 'ALGO'},
    'MASK': {'name': 'ماسک نتورک', 'icon': 'MASK'},
    'OMG': {'name': 'او ام جی', 'icon': 'OMG'},
    'ENJ': {'name': 'انجین کوین', 'icon': 'ENJ'},
}
class CryptoCache:
    """Cache for cryptocurrency data from Nobitex API"""
    def __init__(self, update_interval: int = 60):
        """Initialize the crypto cache
        Args:
            update_interval: Time between updates in seconds (default: 60)
        """
        self._cache: Dict[str, Dict[str, Any]] = {}
        self._last_update: float = 0
        self._update_interval = update_interval
        self._lock = threading.Lock()
        self._update_thread: Optional[threading.Thread] = None
        self._running = False
        self._api_all_url = 'https://api.nobitex.ir/v3/orderbook/all'
        self._api_single_url = 'https://api.nobitex.ir/v3/orderbook/'
    def start(self):
        """Start the background update thread"""
        self._running = True
        self._update_thread = threading.Thread(target=self._update_loop, daemon=True)
        self._update_thread.start()
        logger.info("Crypto cache update thread started")
    def stop(self):
        """Stop the background update thread"""
        self._running = False
        if self._update_thread:
            self._update_thread.join()
        logger.info("Crypto cache update thread stopped")
    def _fetch_all_data(self) -> Optional[Dict[str, Any]]:
        """Fetch data for all crypto symbols from the API in a single request
        Returns:
            The API response data or None if the request failed
        """
        try:
            response = requests.get(self._api_all_url, timeout=30)
            if response.status_code == 200:
                data = response.json()
                if data.get('status') == 'ok':
                    return data
                else:
                    logger.warning(f"API returned non-ok status for all data: {data.get('status')}")
            else:
                logger.warning(f"API request failed for all data with status code: {response.status_code}")
        except Exception as e:
            logger.error(f"Error fetching all data: {str(e)}")
        return None
    def _fetch_single_data(self, symbol: str) -> Optional[Dict[str, Any]]:
        """Fetch data for a specific crypto symbol from the API
        Args:
            symbol: The crypto symbol to fetch (e.g., 'BTCIRT')
        Returns:
            The API response data or None if the request failed
        """
        try:
            url = f"{self._api_single_url}{symbol}"
            response = requests.get(url, timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get('status') == 'ok':
                    return data
                else:
                    logger.warning(f"API returned non-ok status for {symbol}: {data.get('status')}")
            else:
                logger.warning(f"API request failed for {symbol} with status code: {response.status_code}")
        except Exception as e:
            logger.error(f"Error fetching data for {symbol}: {str(e)}")
        return None
    def _update_loop(self):
        """Background thread that updates the cache periodically"""
        while self._running:
            self._update_cache()
            time.sleep(self._update_interval)
    def _update_cache(self):
        """Update the cache with fresh data for all symbols"""
        logger.info("Updating crypto cache...")
        all_data = self._fetch_all_data()
        if all_data:
            current_time = time.time()
            with self._lock:
                all_data.pop('status', None)
                for symbol, data in all_data.items():
                    if symbol in POPULAR_CRYPTO_SYMBOLS or symbol.upper() in POPULAR_CRYPTO_SYMBOLS:
                        prev_price = None
                        if symbol in self._cache and 'lastTradePrice' in self._cache[symbol]:
                            prev_price = self._cache[symbol]['lastTradePrice']
                        price_change = None
                        price_change_percent = None
                        current_price = data.get('lastTradePrice')
                        if prev_price and current_price:
                            try:
                                prev_price_float = float(prev_price)
                                current_price_float = float(current_price)
                                price_change = current_price_float - prev_price_float
                                if prev_price_float > 0:
                                    price_change_percent = (price_change / prev_price_float) * 100
                            except (ValueError, TypeError):
                                price_change = None
                                price_change_percent = None
                        self._cache[symbol] = {
                            'lastUpdate': data.get('lastUpdate'),
                            'lastTradePrice': current_price,
                            'previousPrice': prev_price,
                            'priceChange': price_change,
                            'priceChangePercent': price_change_percent,
                            'asks': data.get('asks', []),
                            'bids': data.get('bids', []),
                            'timestamp': current_time
                        }
                self._last_update = current_time
            logger.info(f"Crypto cache updated successfully with {len(all_data)} symbols")
        else:
            logger.warning("Failed to fetch all data at once, falling back to individual updates")
            for symbol in POPULAR_CRYPTO_SYMBOLS:
                try:
                    self._update_cache_for_symbol(symbol)
                except Exception as e:
                    logger.error(f"Error updating cache for {symbol}: {str(e)}")
            with self._lock:
                self._last_update = time.time()
            logger.info("Crypto cache updated successfully using individual requests")
    def _update_cache_for_symbol(self, symbol):
        """Update the cache for a specific symbol
        Args:
            symbol: The crypto symbol to update (e.g., 'BTCIRT')
        """
        try:
            data = self._fetch_single_data(symbol)
            if data:
                prev_price = None
                with self._lock:
                    if symbol in self._cache and 'lastTradePrice' in self._cache[symbol]:
                        prev_price = self._cache[symbol]['lastTradePrice']
                price_change = None
                price_change_percent = None
                current_price = data.get('lastTradePrice')
                if prev_price and current_price:
                    try:
                        prev_price_float = float(prev_price)
                        current_price_float = float(current_price)
                        price_change = current_price_float - prev_price_float
                        if prev_price_float > 0:
                            price_change_percent = (price_change / prev_price_float) * 100
                    except (ValueError, TypeError):
                        price_change = None
                        price_change_percent = None
                with self._lock:
                    self._cache[symbol] = {
                        'lastUpdate': data.get('lastUpdate'),
                        'lastTradePrice': current_price,
                        'previousPrice': prev_price,
                        'priceChange': price_change,
                        'priceChangePercent': price_change_percent,
                        'asks': data.get('asks', []),
                        'bids': data.get('bids', []),
                        'timestamp': time.time()
                    }
                logger.debug(f"Updated cache for {symbol} with price change tracking")
                return True
        except Exception as e:
            logger.error(f"Error updating cache for {symbol}: {str(e)}")
        return False
    def get_data(self, symbol: Optional[str] = None) -> Any:
        """Get cached data for a specific symbol or all symbols
        Args:
            symbol: The crypto symbol to get data for, or None for all data
        Returns:
            The cached data for the specified symbol, or all cached data
        """
        with self._lock:
            if symbol:
                return self._cache.get(symbol)
            return self._cache
    def get_all_symbols(self) -> List[str]:
        """Get a list of all available symbols in the cache
        Returns:
            List of symbol strings
        """
        with self._lock:
            return list(self._cache.keys())
    def get_crypto_info(self, symbol: str) -> Dict[str, str]:
        """Get information about a cryptocurrency by its symbol
        Args:
            symbol: The crypto symbol (e.g., 'BTC', 'ETH')
        Returns:
            Dictionary with name and icon for the cryptocurrency
        """
        return CRYPTO_INFO.get(symbol, {'name': symbol, 'icon': symbol})
    @property
    def last_update_time(self) -> float:
        """Get the timestamp of the last update"""
        return self._last_update
crypto_cache = CryptoCache()
