# 🎧 ربات ابزار صدا (Audio Tools Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات وب‌هوکی PHP برای پردازش فایل‌های صوتی: متادیتا (طول، بیت‌ریت، تگ‌ها) را با کتابخانه‌ی **getID3** استخراج می‌کند و با کلاس **PHPMP3** کارهای mp3 (برش/ادغام/ساخت فایل) انجام می‌دهد؛ آمار کاربران (`/stat`) و چک کاربر (`/chuser`) هم دارد.

## ✨ امکانات

- 🎼 استخراج کامل متادیتای فایل صوتی با getID3 (duration، bitrate، ID3 tags، کاور)
- ✂️ دستکاری mp3 با کلاس PHPMP3 (ترکیب/برش/ساخت قطعه)
- 📊 آمار (`/stat`) و بررسی/ثبت کاربر (`/chuser`)
- 🛡 اجازه‌ی درخواست فقط از IPهای رسمی تلگرام

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl
- کتابخانه‌ی [getID3](https://github.com/JamesHeinrich/getID3) (برای سبک‌ماندن مخزن، وندور نشده)

## ⚙️ نصب و راه‌اندازی

### گام ۱: نصب getID3
پوشه‌ی `getid3` را از مخزن رسمی getID3 دانلود و کنار `index.php` بگذارید (ساختار: `./getid3/getid3.php`).

### گام ۲: پیکربندی و ستوب‌هوک
توکن ربات را در `index.php` تنظیم کنید و:

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/audio-tools-bot/index.php
```

### گام ۳: تست
یک فایل mp3 به ربات بفرستید — متادیتا و خروجی پردازش برمی‌گردد.

`phpmp3.php` کلاس LGPL جداگانه‌ای است که با هدر اصلی خودش ضمیمه شده است.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PHP webhook bot for audio files — full metadata via getID3, mp3 manipulation via the bundled PHPMP3 class (LGPL), user stats and checks. getID3 is not vendored; drop it in per README. **Attribution to @Abolfazl_rwm required.**
