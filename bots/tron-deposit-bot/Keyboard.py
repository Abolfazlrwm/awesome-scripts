# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from pyrogram.types import (ReplyKeyboardMarkup, InlineKeyboardMarkup, InlineKeyboardButton) 



login = ReplyKeyboardMarkup(
[
    ["⭕️ ثبت نام ⭕️"]
],
resize_keyboard=True
)



back_admin = ReplyKeyboardMarkup(
[
    ['پنل اصلی 🏡']
],
resize_keyboard=True
)

yesorno = ReplyKeyboardMarkup(
[
    ["بله","خیر"]
],
resize_keyboard=True
)


back = ReplyKeyboardMarkup(
[
    ['🔙 بازگشت']
],
resize_keyboard=True
)



panel_admin = ReplyKeyboardMarkup(
[
    ["📍ثبت تبلیغ","🏆نفرات برتر"],
    ["📉آمار کل","📜حسابرسی","📯همگانی"],
    ["💰خدمات مالی","👥شرکت کنندگان"],
    ["📋حسابرسی تکی","📒اطلاعات کاربر"],
    ["💵 موجودی کل کاربران"],
    ["📬 لیست ثبت نام","🔸لیست برداشت"],
    ["◽️حالت کاربر"]
],
resize_keyboard=True
)



user = ReplyKeyboardMarkup(
[
    ["آمار 📊","تبلیغات فعال 🟢"],
    ["خدمات مالی 💰","نفرات برتر 🎖"],
    ["پشتیبانی 👤"],
],
resize_keyboard=True
)

kmali_panel = ReplyKeyboardMarkup(
[
    ["کاهش موجودی 📉","افزایش موجودی 📈"],
    ['پنل اصلی 🏡']
],
resize_keyboard=True
)

withraw_panel = ReplyKeyboardMarkup(
[
    ["برداشت ووچر 💸","برداشت ترون 🔴","برداشت تومان 💳"],
    ['🔙 بازگشت']
],
resize_keyboard=True
)

balance = ReplyKeyboardMarkup(
[
    ["💰 موجودی","💵 برداشت وجه","💸 انتقال وجه"],
    ['🔙 بازگشت']
],
resize_keyboard=True
)

check = ReplyKeyboardMarkup(
[
    ["رد","تایید"]
],
resize_keyboard=True
)