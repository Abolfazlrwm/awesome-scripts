# 🛰 ربات اسکراپر پروکسی تلگرام (Proxy Scraper Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات وب‌هوکی PHP که با زدن `/start`، ده پروکسی MTProto تازه را از API عمومی پروکسی می‌گیرد و به‌صورت دکمه‌های لینک تپ‌خور به کاربر نشان می‌دهد؛ هیچ دیتابیسی لازم ندارد و فایل آن فقط یک صفحه است.

## ✨ امکانات

- 🔄 دریافت ۱۰ پروکسی زنده از `haji-api.ir` به‌ازای هر استارت
- 🔘 نمایش پروکسی‌ها به‌شکل اینلاین‌کیبورد دو ستونه («سرور اول» تا «سرور دهم»)
- 🛡 محدودسازی درخواست‌ها به IPهای رسمی تلگرام (۱۴۹.۱۵۴.۱۶۰.۰/۲۰ و ۹۱.۱۰۸.۴.۰/۲۲)
- ⚙️ کانال مرجع اسکرپ به‌صورت یک متغیر ساده (`$channelprox`) قابل تغییر است
- 🪶 تک‌فایل، بدون دیتابیس و بدون وابستگی Composer

## 📋 پیش‌نیازها

- هاست PHP با نسخه‌ی ۷.۴ به بالا (ext-curl فعال)
- یک ربات از [@BotFather](https://t.me/BotFather)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `bot.php` را باز کنید و متغیرهای ابتدای فایل را تنظیم کنید:

| متغیر | توضیح | نمونه |
|---|---|---|
| `$token` | توکن ربات از BotFather | `123456:ABC-DEF...` |
| `$channelprox` | یوزرنیم کانال مقصد اسکرپ پروکسی، بدون @ | `ProxyMTProto` |

### گام ۲: ستوب‌هوک
فایل را روی هاست آپلود کنید و وب‌هوک را تنظیم کنید:

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/proxy-scraper-bot/bot.php
```

### گام ۳: تست
در ربات `/start` بزنید — باید فهرست ۱۰ پروکسی با دکمه‌های لینک نمایش داده شود.

## ⚠️ نکته

سرویس `haji-api.ir` یک API شخص ثالث است؛ اگر قطع شد، خطا نمایش داده نمی‌شود (`error_reporting(0)`) — می‌توانید آدرس API را در همان خط `$api = file_get_contents(...)` به سرویس دلخواه خود تغییر دهید.

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** (@devbc) — بازنشر با ساختار باز تحت **MIT**.

---
**EN:** A single-file PHP webhook bot: `/start` fetches 10 fresh MTProto proxies from a public proxy API and shows them as tappable URL buttons. Telegram-IP allow-list included, no database, config via two variables (`$token`, `$channelprox`). Original source by @devbc (Sourcekade), released under MIT.


## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.