# ⏱ ربات قفل زمان‌دار (Timed Auto-Lock Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات وب‌هوکی PHP برای **قفل و بازکردن خودکارِ زمان‌دار** گروه‌ها و کانال‌ها: برای هر کانال یک بازه‌ی زمانی تعریف می‌کنی (مثلاً بسته‌بودن شبانه) و ربات در آن بازه قفل را اعمال و بعد باز می‌کند. مدیریت کانال‌ها با اینلاین‌کیبورد و لیست قفل‌های فعال.

## ✨ امکانات

- ⏰ قفل خودکار زمان‌دار per-کانال — از ساعت `$from` تا `$to`
- 📋 افزودن/حذف کانال و حذف قفل خودکار با دکمه‌های callback (`delauto`)
- 👀 مشاهده‌ی لیست کانال‌های تحت مدیریت و قفل‌های فعال
- 🛡 اجازه‌ی درخواست فقط از IPهای رسمی تلگرام + خروج سریع وب‌هوک (litespeed/fastcgi)

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl + Cron/Visitor برای اجرای دوره‌ای منطق قفل

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `anti.php` را باز کنید و `API_KEY` را با توکن ربات از [@BotFather](https://t.me/BotFather) پر کنید.

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/timed-lock-bot/anti.php
```

### گام ۳: تست
ربات را استارت کنید، یک کانال اضافه کنید (ربات ادمین با دسترسی لازم باشد) و یک قفل زمان‌دار تعریف کنید.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PHP webhook bot for time-based auto-locking of groups/channels: define a lock window per channel (e.g. nightly quiet hours), manage the channel list with inline callbacks, Telegram-IP allow-list included. **Attribution to @Abolfazl_rwm required.**
