# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from config import PERSIAN_NUMERALS, ENGLISH_NUMERALS

def convert_persian_numbers(text):
    return text.translate(str.maketrans(PERSIAN_NUMERALS, ENGLISH_NUMERALS))

__all__ = ['convert_persian_numbers']