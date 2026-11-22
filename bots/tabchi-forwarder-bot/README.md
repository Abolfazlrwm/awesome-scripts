# 🔁 تبچی فروارد خودکار — MadelineProto (Tabchi Forwarder Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

«تبچی» کلاسیک با **MadelineProto v8**: پست‌های جدید کانال‌های مبدأ را به‌صورت خودکار به کانال‌های مقصد فوروارد می‌کند. برای مدیریت چند تبچه همزمان پوشه‌ی `Data/` را دارد و مدیاها (وویس/استیکر/گیف) به‌صورت جدا ذخیره می‌شوند.

## ✨ امکانات

- 🔁 فوروارد خودکار پیام‌های جدید از کانال‌های مبدأ به مقصدها
- 🗂 پشتیبانی از چند تبچه با داده‌ی جدا در پوشه‌ی `Data/`
- 💽 پوشه‌های آماده‌ی `Data/Voice` ،`Data/Sticker` ،`Data/Gif` برای کش مدیا
- 🧩 معماری رویدادمحور MadelineProto v8 — بدون polling سنگین
- ⚙️ افزودن/حذف مبدأ و مقصد از داخل خود ربات (دستورات ادمین)

## 📋 پیش‌نیازها

- PHP ۸.۰+ با ext-gmp و ext-curl
- Composer
- ⚠️ یوزربات است — فقط برای کانال‌هایی که مالک یا ادمین آن‌ها هستید استفاده کنید؛ مسئولیت رعایت قوانین تلگرام و کپی‌رایت محتوا با شماست.

## ⚙️ نصب و راه‌اندازی

### گام ۱: نصب MadelineProto
```bash
composer require danog/madelineproto
```

### گام ۲: لاگین
```bash
php Api.php     # ساخت سشن با شماره‌ی اکانت شما
```

### گام ۳: اجرا
```bash
php Tabchi.php
```

### گام ۴: تنظیم مسیرها
از دستورات ادمین داخل ربات، کانال‌های مبدأ و مقصد را اضافه کنید؛ اعضای مجاز در `Data/` نگه‌داری می‌شوند.

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** (Author: @Mahdi_a_8) — بازنشر تحت **MIT**.

---
**EN:** The classic Tabchi auto-forwarder on MadelineProto v8: pipes new posts from your source channels to destination channels, multi-tabche support with per-tab data and media cache folders (Voice/Sticker/Gif). Userbot — only forward channels you own or administer. Original by @Mahdi_a_8 (Sourcekade), MIT.
