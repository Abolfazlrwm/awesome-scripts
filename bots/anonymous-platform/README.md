# 🕵️ پلتفرم چت ناشناس (Anonymous Chat Platform)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

پلتفرم کامل چت ناشناس با **پنل کاربری تحت وب + نصاب**: ثبت‌نام/ورود کاربر (`auth.php`)، چت ناشناس (`chat.php`)، پروفایل (`profile.php`)، سیستم پرداخت زرین‌پال (`pay/request.php` و `verify.php`) و نصاب/آپدیتر تحت وب (`run.php`) که HTTPS را اجباری می‌کند.

## ✨ امکانات

- 💬 چت ناشناس دوطرفه با مدیریت گام‌ها (start/main/chat)
- 🧑 پروفایل کاربری و راهنماهای تصویری (`bot/files/help_*.jpg`)
- 💳 درگاه پرداخت زرین‌پال — درخواست و وریفای تراکنش
- 🧰 نصاب وب (`run.php`) با اجبار HTTPS و صفحه‌ی نصب/آپدیت
- 🗂 ساختار تمیز: `bot/user/` منطق ربات، `include/` تنظیمات، `pay/` درگاه

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl، ext-mysqli و SSL معتبر (برای نصاب و پرداخت)
- دیتابیس MySQL

## ⚙️ نصب و راه‌اندازی

### گام ۱: نصب
فایل‌ها را روی هاست بگذارید و `run.php` را در مرورگر باز کنید — نصاب جداول و تنظیمات را انجام می‌دهد.

### گام ۲: پیکربندی
`include/config.php` و `include/settings.php`: توکن ربات، مشخصات دیتابیس و مرچنت‌کد زرین‌پال.

### گام ۳: ستوب‌هوک
وب‌هوک ربات را به `bot/user/bot.php` تنظیم کنید و از پنل، کانال‌های اجباری و قیمت‌گذاری را ست کنید.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Full anonymous-chat platform — web user panel (auth/chat/profile), Zarinpal payment flow, and a web installer that enforces HTTPS. Config in `include/`, bot logic in `bot/user/`. **Attribution to @Abolfazl_rwm required.**
