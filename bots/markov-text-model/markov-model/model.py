# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import ast
from collections import defaultdict
import random
import os

os.chdir("Data")

# خوندن داده‌ها
with open("Digitsdata.txt", "r") as file:
    digits_data = ast.literal_eval(file.read())

digits = ast.literal_eval(open("DigitsWord.txt", "r").read())

def trade(num):
    rev = {v: k for k, v in digits.items()}
    return rev.get(num, '?') 

# ساخت دیکشنری مارکو
markov_model = defaultdict(list)
for i in range(len(digits_data) - 1):
    current_char = digits_data[i]
    next_char = digits_data[i + 1]
    markov_model[current_char].append(next_char)

# پیش‌بینی چند کاراکتر
def predict_next_chars(start_sequence, num_chars=5):
    sequence = start_sequence.copy()  
    for _ in range(num_chars):
        current_char = sequence[-1] 
        possible_next = markov_model.get(current_char, [0]) 
        next_char = random.choice(possible_next)
        sequence.append(next_char)
    return sequence

# تبدیل رشته به اعداد
def string_to_nums(text):
    return [digits.get(char, 0) for char in text]

# تست
start_text = "w"  # رشته ورودی
start_nums = string_to_nums(start_text)  # تبدیل به اعداد
print(f"ورودی: {start_text} ({start_nums})")

predicted_nums = predict_next_chars(start_nums, num_chars=5)  # پیش‌بینی ۵ کاراکتر
predicted_text = ''.join(trade(num) for num in predicted_nums)
print(f"خروجی کامل: {predicted_text} ({predicted_nums})")