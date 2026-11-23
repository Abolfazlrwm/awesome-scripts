# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import time
from typing import Optional, Dict, Any
import threading
import requests
import logging
class CurrencyCache:
    def __init__(self, update_interval: int = 60):
        self._cache: Optional[Dict[str, Any]] = None
        self._last_update: float = 0
        self._update_interval = update_interval
        self._lock = threading.Lock()
        self._update_thread: Optional[threading.Thread] = None
        self._running = False
        self._api_url = 'https://apiarz.qprjz.workers.dev/'
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(levelname)s - %(message)s'
        )
        self.logger = logging.getLogger('CurrencyCache')
    def start(self):
        """Start the background update thread"""
        self._running = True
        self._update_thread = threading.Thread(target=self._update_loop, daemon=True)
        self._update_thread.start()
        self.logger.info("Cache update thread started")
    def stop(self):
        """Stop the background update thread"""
        self._running = False
        if self._update_thread:
            self._update_thread.join()
        self.logger.info("Cache update thread stopped")
    def _fetch_data(self) -> Optional[Dict[str, Any]]:
        """Fetch fresh data from the API"""
        try:
            response = requests.get(self._api_url)
            if response.status_code == 200:
                return response.json()
            self.logger.warning(f"API request failed with status code: {response.status_code}")
        except Exception as e:
            self.logger.error(f"Error fetching data: {str(e)}")
        return None
    def _update_loop(self):
        """Background thread that updates the cache periodically"""
        while self._running:
            self._update_cache()
            time.sleep(self._update_interval)
    def _update_cache(self):
        """Update the cache with fresh data"""
        new_data = self._fetch_data()
        if new_data:
            with self._lock:
                self._cache = new_data
                self._last_update = time.time()
                self.logger.info("Cache updated successfully")
    def get_data(self) -> Optional[Dict[str, Any]]:
        """Get the cached data"""
        with self._lock:
            return self._cache
    @property
    def last_update_time(self) -> float:
        """Get the timestamp of the last update"""
        return self._last_update
currency_cache = CurrencyCache()
