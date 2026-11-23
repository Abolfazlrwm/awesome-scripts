# 🧩 سلف پلاگین‌محور SelfSaz (SelfSaz Plugin Selfbot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

سلف‌بات Telethon با معماری پلاگین‌محور: هسته‌ی `bot.py` پلاگین‌های پوشه‌ی `plugins/` را لود می‌کند — ۸۷ پلاگین آماده از anti و ban تا aichat و autoaccept، به‌همراه سیستم سکه، گزارش باگ و دیتابیس‌های خودکار.

## ✨ امکانات

- لودر پلاگین داینامیک — افزودن پلاگین با یک فایل
- ۸۷ پلاگین آماده (afk، aichat، auth، autoaccept، ban، …)
- سیستم سکه و مدیریت دشمنان/کامنت‌ها
- گزارش باگ داخلی (bug_reports.db خودکار)
- دیتابیس‌ها در اولین اجرا ساخته می‌شوند

## 📋 پیش‌نیازها

- Python ۳.۸+ و `pip install telethon` (طبق importهای پلاگین‌ها)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
1. `config.py` را با api_id/api_hash و تنظیمات اکانت پر کنید؛ فایل‌های session و db نسخه‌ی شما از اول ساخته می‌شوند.

### گام ۲: اجرا
```bash
python bot.py
```

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Telethon selfbot with a dynamic plugin loader and 87 ready-made plugins — coins, bug reports and auto-created SQLite state. **Attribution to @Abolfazl_rwm required.**
