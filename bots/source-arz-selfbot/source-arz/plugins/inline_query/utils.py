# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
Utility functions for the inline query feature.
"""
import re
from typing import Dict, List, Any, Optional
def find_matching_currencies(query: str, currency_mapping: Dict[str, Dict[str, str]],
                           currency_data_map: Dict[str, Dict[str, Any]]) -> List[str]:
    """
    Find currencies that match the query string.
    Args:
        query: The search query string
        currency_mapping: Mapping of search terms to currency info
        currency_data_map: Mapping of currency names to their data
    Returns:
        List of matching currency names
    """
    matched_currencies = set()
    query = query.lower().strip()
    for search_term, currency_info in currency_mapping.items():
        if query in search_term or search_term in query:
            currency_name = currency_info['name']
            if currency_name in currency_data_map and currency_name not in matched_currencies:
                matched_currencies.add(currency_name)
    for currency_name in currency_data_map.keys():
        if currency_name not in matched_currencies and query in currency_name.lower():
            matched_currencies.add(currency_name)
    return list(matched_currencies)
def get_currency_info(currency_name: str, currency_data_map: Dict[str, Dict[str, Any]],
                    currency_mapping: Dict[str, Dict[str, str]]) -> Optional[Dict[str, Any]]:
    """
    Get currency information for the given currency name.
    Args:
        currency_name: The name of the currency
        currency_data_map: Mapping of currency names to their data
        currency_mapping: Mapping of search terms to currency info
    Returns:
        Dictionary with currency information or None if not found
    """
    if currency_name not in currency_data_map:
        return None
    currency_data = currency_data_map[currency_name]
    flag = '🌐'
    for search_term, info in currency_mapping.items():
        if info['name'] == currency_name:
            flag = info['flag']
            break
    return {
        'name': currency_name,
        'flag': flag,
        'price': currency_data['livePrice'],
        'change': currency_data['change'],
        'lowest': currency_data['lowest'],
        'highest': currency_data['highest'],
        'time': currency_data['time']
    }
