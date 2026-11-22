# 🪪 پنل سلف CLI — MadelineProto (Self-CLI PHP Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

پنل «سلف» مبتنی بر **MadelineProto v8** (SimpleEventHandler) که روی **اکانت شخصی خودتان** اجرا می‌شود: مدیریت نام و بیو، ابزارهای پروفایل، تایمرها، فوروارد و ذخیره، دستور آب‌وهوا و امکانات متنی دیگر — همه از داخل یک ترمینال PHP.

## ✨ امکانات

- 🧩 معماری رویدادمحور MadelineProto v8 (`SimpleEventHandler`) با فیلترهای Outgoing/IsNotEdited
- ✏️ ابزارهای نام و بیو (تغییر، تکرار، زمان‌بندی) روی اکانت خودتان
- ⏱ تایمر/کارهای زمان‌دار سمت اکانت
- 🌤 دستور `/weather <city>` با OpenWeatherMap (کلید API جای‌گذاری‌شده — مال خودتان را بگذارید)
- 🔁 فوروارد و ذخیره‌ی پیام‌ها، ابزارهای متن و پاسخ سریع
- 💾 سشن لوکال (فایل session) — بعد از لاگین اولیه دیگر کد لازم نیست

## 📋 پیش‌نیازها

- PHP ۸.۰+ با ext-gmp و ext-curl
- Composer
- ⚠️ این یک **یوزربات** است: اجرای اتوماسیون روی اکانت شخصی خلاف قوانین تلگرام است و ریسک محدودشدگی اکانت دارد — با مسئولیت خودتان و فقط برای اکانت خودتان استفاده کنید.

## ⚙️ نصب و راه‌اندازی

### گام ۱: نصب MadelineProto
```bash
composer require danog/madelineproto
```

### گام ۲: لاگین
```bash
php Api.php     # شماره تلفن + کد ورود را می‌پرسد و سشن می‌سازد
```

### گام ۳: اجرای پنل
```bash
php Self.php    # هندلر رویدادها روی اکانت شما بالا می‌آید
```

### گام ۴ (اختیاری): آب‌وهوا
در فایل `Self.php` مقدار `YOUR_OPENWEATHER_API_KEY` را با کلید رایگان از [openweathermap.org](https://openweathermap.org/api) جایگزین کنید.

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** (Author: @Mahdi_a_8) — بازنشر تحت **MIT**.

---
**EN:** A MadelineProto v8 self-bot panel for your **own** Telegram account: name/bio tools, timers, forwards, saving, a `/weather` command and text utilities, event-driven via SimpleEventHandler. Login once with `Api.php`, run with `Self.php`. It is a userbot — Telegram may limit such accounts; use at your own risk. Original by @Mahdi_a_8 (Sourcekade), MIT.
