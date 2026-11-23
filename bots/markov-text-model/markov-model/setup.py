# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import string, os

# ساخت فایل متنی داده
if not os.path.exists("DigitsWord.txt"):
    os.chdir("Data")
    words_data = {" ": 0, "!": 53, "?": 54, ",": 55, "_": 56, "'": 57, '"': 58, ".": 59, "\n": 60, "-": 61, ";": 62, ":": 63, "(": 64, ")": 65, "[": 66, "]": 67, "{": 68, "}": 69, "&": 70, "*": 71, "/": 72, "\\": 73, "%": 74, "$": 75, "#": 76, "@": 77, "+": 78, "=": 79, "’": 80, "0": 81, "1": 82, "2": 83, "3": 84, "4": 85, "5": 86, "6": 87, "7": 88, "8": 89, "9": 90, "10": 91}
    digits = 0
    # تبدیل حروف کوچک 
    for key in string.ascii_lowercase:
        digits += 1
        words_data[key] = digits
    # تبدیل حروف بزرگ
    for key in string.ascii_uppercase:
        digits += 1
        words_data[key] = digits
    
    # ذخیره فایل
    with open("DigitsWord.txt", "w") as file:
        file.write(str(words_data))