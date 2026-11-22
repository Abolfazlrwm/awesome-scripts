# 🃏 سلف‌بات جک‌مور (Jackmor Selfbot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

مگا-سلف‌بات Telethon (بر پایه‌ی فریم‌ورک متن‌باز Teletips) با هزاران خط امکانات: ضدحذف چت، اتو-پیک، متن‌های خوش‌آمد، ابزارهای یوتیوب و مدیا، ساخت لوگو و GIF، فونت‌های متنوع و مدیریت کامل اکانت شخصی.

## ✨ امکانات

- 🛡 ضدحذف: پیام‌های حذف‌شده‌ی چت‌ها را لاگ و بازنمایی می‌کند
- 🖼 اتو-پیک با فونت‌های سفارشی (فایل‌های فونت و قالب در مخزن اصلی پروژه)
- 🎬 ابزارهای یوتیوب/مدیا: اطلاعات، دانلود، آهنگ، tor، GIF، لوگو
- 🧯 تولیدکننده‌ی فحش (`fosh_saz`) — تنها با اعضای خودت و به‌شوخی
- 📊 دستورات مدیریتی کامل اکانت + ماژول‌های helper/functions/aiohttp
- ⚠️ دستور «اسپم N» تکرار پیام در چت است — فقط برای تست در چت خودت، نه مزاحمت دیگران

## 📋 پیش‌نیازها

- Python ۳.۹+ و وابستگی‌های `requirements.txt`
- ⚠️ یوزربات — مسئولیت کامل اجرا با خودت است؛ قوانین تلگرام را رعایت کن.

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
در `self.py` بخش تنظیمات API، مقدار `api_id`/`api_hash` خودت را بگذارید (فیلدها به‌صورت خالی ضمیمه شده‌اند) و StringSession بساز.

### گام ۲: اجرا
```bash
pip install -r requirements.txt
python self.py
```

فایل‌های مدیا/فونت/دیتابیس نسخه‌ی اصلی (تصاویر، فونت‌ها، pickle و ...) در مخزن قرار نگرفته‌اند تا مخزن سبک بماند؛ ربات بدون آن‌ها هم بالا می‌آید و در صورت نیاز می‌توانی از منبع اصلی اضافه‌شان کنی.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Telethon mega-selfbot based on the open Teletips framework — anti-delete logging, autopic, welcome texts, YouTube/media tools, logo/GIF makers and full account management. Media/font assets are not committed to keep the repo light. Userbot: at your own risk. **Attribution to @Abolfazl_rwm required.**
