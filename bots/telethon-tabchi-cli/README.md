# 🔁 تبچی CLI تلثون (Telethon Tabchi CLI)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

تبچی نسخه‌ی ۲ (VIP) با کتابخانه‌ی **Telethon** — فوروارد خودکار پست‌های کانال‌های مبدأ به مقصدها از داخل ترمینال، با مدیریت مبدأ/مقصد، چک اسپم‌بات و مدیریت خطاهای FloodWait.

## ✨ امکانات

- 🔁 فوروارد خودکار پیام‌های جدید از کانال‌های مبدأ به لیست مقصدها
- ➕➖ افزودن/حذف مبدأ و مقصد با دستورات ادمین
- 🛡 چک وضعیت اسپم‌بات (`conversation with spambot`) قبل از عملیات
- ⏱ مدیریت خطاهای `FloodWaitError` و لینک‌های نامعتبر (`InviteHashInvalid/Expired`)
- 🧵 معماری CLI — بدون پنل، فقط ترمینال

## 📋 پیش‌نیازها

- Python ۳.۸+ و `pip install telethon`

## ⚙️ نصب و راه‌اندازی

### گام ۱: نصب وابستگی
```bash
pip install -r requirements.txt
```

### گام ۲: اجرا و لاگین
```bash
python tabchi.py
```
شماره‌ی اکانت و کد ورود را وارد کنید؛ سشن لوکال ساخته می‌شود.

### گام ۳: تنظیم مسیرها
از دستورات ادمین داخل CLI، کانال‌های مبدأ و مقصد را اضافه کنید.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Telethon-based Tabchi CLI v2 — auto-forwards new posts from source channels to destinations, with admin commands, spambot check and FloodWait handling. Run `python tabchi.py` and log in. Original by @MrChronicle (Sourcekade). **Attribution to @Abolfazl_rwm required.**
