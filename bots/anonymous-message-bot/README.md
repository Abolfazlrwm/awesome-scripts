# 🕵️ ربات پیام ناشناس (Anonymous Message Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات پیام ناشناس کلاسیک تلگرام: هر کاربر با `/link` یک لینک اختصاصی می‌گیرد؛ هر کسی از آن لینک وارد شود می‌تواند بدون فاش‌شدن هویت برای صاحب لینک پیام بفرستد و پیام‌ها به‌صورت ناشناس به او فوروارد می‌شوند. پنل مدیریت، عضویت اجباری کانال و آمار دریافت پیام هم دارد.

## ✨ امکانات

- 🔗 ساخت لینک اختصاصی برای هر کاربر با دستور `/link` همراه متن پیش‌فرض قابل ویرایش
- 📨 فوروارد ناشناس پیام‌ها (متن، عکس، ویدیو، صدا) به صاحب لینک — هویت فرستنده هرگز لو نمی‌رود
- 👑 پنل مدیر: آمار، پیام به کاربران، تنظیم متن پیش‌فرض لینک
- 🚪 دروازه‌ی عضویت اجباری در کانال قبل از استفاده
- 💾 ذخیره‌سازی فایل‌محور (پوشه‌ی `data/`) — بدون نیاز به دیتابیس
- 🛡 بررسی IP تلگرام + خروج سریع پاسخ برای جلوگیری از timeout وب‌هوک

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و اجازه‌ی نوشتن فایل
- یک ربات از [@BotFather](https://t.me/BotFather)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `index.php` را باز کنید؛ در بخش توکن (جست‌وجوی کلمه‌ی `Editing`) این مقدارها را پر کنید:

| متغیر | توضیح |
|---|---|
| `$token` | توکن ربات از BotFather |
| `$admin` | آیدی عددی مدیر (از [@userinfobot](https://t.me/userinfobot)) |
| کانال عضویت اجباری | یوزرنیم کانال اجباری در بخش تنظیمات |

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/anonymous-message-bot/index.php
```

### گام ۳: تست
ربات را استارت کنید، `/link` بزنید، لینک را از یک اکانت دیگر باز کنید و یک پیام تستی بفرستید — باید بدون نام فرستنده به شما برسد.

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** (Dev: Dr Amir — t.me/IFIED) — بازنشر تحت **MIT**.

---
**EN:** Classic Telegram anonymous-messaging bot: every user gets a personal `/link`; anyone opening it can send texts or media that arrive without sender identity. Admin panel, mandatory channel join, file-based storage, webhook hardening. Original source by Dr Amir (Sourcekade), MIT.


## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.