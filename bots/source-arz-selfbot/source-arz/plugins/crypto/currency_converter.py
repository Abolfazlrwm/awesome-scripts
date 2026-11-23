# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Currency converter module for the crypto handler.
This module handles converting between real currencies.
"""
import logging
import re
import time
from typing import Dict, Any, Tuple, Optional
from telethon.tl.custom import Button
from ..currency_converter import CURRENCY_CODES, convert_currency, get_currency_name, get_currency_price_in_toman
from .crypto_cache import crypto_cache, CRYPTO_INFO
def format_number(number):
    """Format a number with commas as thousands separator"""
    if isinstance(number, float):
        if number >= 1000:
            return f"{number:,.2f}"
        elif number >= 100:
            return f"{number:,.3f}"
        elif number >= 10:
            return f"{number:,.4f}"
        elif number >= 1:
            return f"{number:,.5f}"
        else:
            return f"{number:,.8f}"
    else:
        return f"{number:,}"
