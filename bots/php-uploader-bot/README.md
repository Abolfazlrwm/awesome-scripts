# 📤 ربات آپلودر PHP (PHP Uploader Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات آپلودر مبتنی بر **PHP + MySQL**: کاربر لینک می‌دهد، ربات فایل را دانلود و به تلگرام آپلود می‌کند؛ پنل ادمین، آمار، تاریخ شمسی، حذف پیام از کانال و جدول‌های دیتابیس هم دارد. فایل آموزش ران (docx) داخل `docs/` ضمیمه است.

## ✨ امکانات

- 📥 دانلود از URL و آپلود مستقیم به تلگرام (سند/عکس/ویدیو/صدا)
- 🗄 ذخیره‌سازی در MySQL با اتصال `utf8_persian_ci`
- 👑 پنل ادمین + مدیریت پیام‌ها (`delmsg.php` برای حذف پیام از کانال)
- 📊 جدول آماری دیتابیس (`table.php`)
- 📅 تاریخ و ساعت شمسی با `jdf.php`
- 🔌 API جانبی (`api.php`) برای ستوب‌هوک و getMe
- 🛡 اجازه‌ی درخواست فقط از IPهای رسمی تلگرام

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و ext-mysqli
- یک دیتابیس MySQL
- یک ربات از [@BotFather](https://t.me/BotFather)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `config.php` را باز کنید و مقادیر خالی را پر کنید:

| متغیر | توضیح |
|---|---|
| `$API_KC` | توکن ربات از BotFather |
| `$admins` | آرایه‌ی آیدی عددی مدیرها، مثل `array(123456789)` |
| `$Dev` / `$Channel` | آیدی سازنده / یوزرنیم کانال |
| `$dbname` / `$username` / `$password` | مشخصات دیتابیس MySQL |

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/php-uploader-bot/index.php
```

### گام ۳: تست
به ربات `/start` بدهید، یک لینک مستقیم فایل بفرستید و دریافت فایل در تلگرام را ببینید.

### 📚 آموزش ران
فایل `docs/run-tutorial.docx` آموزش گام‌به‌گام نصب روی هاست است (به فارسی).

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** — بازنشر تحت **MIT**.

---
**EN:** PHP+MySQL uploader bot: send a link, receive the file inside Telegram. Admin panel, per-message delete control, DB stats table, Jalali dates, webhook IP allow-list, and a Persian video-less step-by-step tutorial in `docs/run-tutorial.docx`. Config via `config.php`. Original by Sourcekade, MIT.


## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.