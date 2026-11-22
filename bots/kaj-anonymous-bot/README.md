# 💬 چت ناشناس کاج (Kaj Anonymous Paid Chat)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات چت ناشناس نوشته‌شده توسط **ابوالفضل کاج** با مدل درآمدی جالب: پیام‌های بلندتر از ۷ حرف **شارژی** می‌شوند — قیمت هر حرف (`$token_amount`) و سقف مجانی (`$start_token`) در تنظیمات است و پرداخت با درگاه **زرین‌پال** انجام می‌شود.

## ✨ امکانات

- 💬 چت ناشناس کاربران با مدیریت مراحل
- 💰 قیمت‌گذاری per-حرف: ۷ حرف اول مجانی، مابقی شارژی
- 💳 درگاه زرین‌پال (`pay/zarinpal/`) + صفحه‌ی خطای پرداخت
- 📅 تاریخ شمسی (`lib/jdf.php`) + آمار روزانه (`lib/daily.php`) + جدول کاربران (`lib/table.php`)
- 📖 راهنمای متنی (`help.txt`) و بخش‌های help چندمرحله‌ای

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و ext-mysqli + دیتابیس MySQL

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `config.php` را پر کنید:

| متغیر | توضیح |
|---|---|
| `API_KEY` | توکن ربات از [@BotFather](https://t.me/BotFather) |
| `$token_amount` | قیمت هر حرف (ریال) |
| `$start_token` | تعداد حروف مجانی |
| مشخصات DB و مرچنت زرین‌پال | در همان فایل |

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/kaj-anonymous-bot/bot.php
```

## 📄 اعتبار

نویسنده‌ی اصلی: **ابوالفضل کاج (AbolfazlKaj)** — بازنشر از کانال سورس کده.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Anonymous chat bot by AbolfazlKaj with per-character pricing (first 7 chars free), Zarinpal gateway, Jalali dates and daily stats. Original author credited above. **Attribution to @Abolfazl_rwm required.**
