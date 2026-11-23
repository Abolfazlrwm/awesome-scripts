# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

import requests
from threading import Thread,local


def usdt():
    return requests.post('https://ramzarz.news//wp-admin/admin-ajax.php',data={'action':'usdtPrice_ajax','price':'usdt'}).content.decode('utf-8')