# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import random

def generate():
	upper_letters = "ABCDEFGHIJKLMNOPQRSTUVWXVZ"
	lower_letters = "abcdefghijklmnopqrstuvwxyz"
	number = "1234567890"
	link_data_code = upper_letters+lower_letters+number
	code = "".join(random.sample(link_data_code,5))
	return code