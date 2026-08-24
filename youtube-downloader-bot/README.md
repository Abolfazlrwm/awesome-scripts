# 🎬 ربات دانلودر یوتیوب (YouTube Downloader Bot)

لینک ویدیوی یوتیوب رو برای ربات بفرست، کیفیت (ویدیو یا فقط صدا) رو انتخاب کن و فایل رو مستقیم توی تلگرام دریافت کن.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4.svg)
![yt-dlp](https://img.shields.io/badge/powered%20by-yt--dlp-red.svg)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ امکانات

- 🎥 دانلود ویدیو با کیفیت تا 480p
- 🎵 استخراج و دانلود فقط صدا (MP3)
- 🎯 انتخاب کیفیت با دکمه‌ی شیشه‌ای
- ⚡️ استفاده از ابزار متن‌باز و قدرتمند `yt-dlp` برای دانلود واقعی
- 🧹 پاک‌سازی خودکار فایل‌های موقت بعد از ارسال

## 📋 پیش‌نیازها

- سرور با PHP نسخه‌ی ۷.۴ یا بالاتر و افزونه‌ی `curl`
- نصب بودن ابزار [`yt-dlp`](https://github.com/yt-dlp/yt-dlp) روی همان سرور (و در دسترس بودن آن در PATH یا مسیر مشخص‌شده)

### نصب yt-dlp روی سرور (لینوکس)
```bash
sudo apt install python3-pip -y
pip install -U yt-dlp
```

## ⚙️ نصب و راه‌اندازی

### گام ۱: آپلود فایل
فایل `bot.php` را روی هاست/سرور خودت آپلود کن. یک پوشه‌ی قابل‌نوشتن به نام `downloads` هم کنارش لازم است (خودش در صورت نبود ساخته می‌شود).

### گام ۲: تنظیمات اولیه
فایل را باز کن و این مقادیر را جایگزین کن:

| ثابت (define) | توضیح |
|---|---|
| `BOT_TOKEN` | توکن ربات از [@BotFather](https://t.me/BotFather) |
| `ADMIN_ID` | آیدی عددی ادمین از [@userinfobot](https://t.me/userinfobot) |
| `YTDLP_PATH` | مسیر اجرایی yt-dlp (اگر global نصب شده همان `"yt-dlp"` کافیست) |

### گام ۳: تنظیم وبهوک
```
https://api.telegram.org/bot<BOT_TOKEN>/setWebhook?url=https://yourdomain.com/path/to/bot.php
```

## ⚠️ نکات مهم

- تلگرام برای ربات‌های معمولی حداکثر حجم ارسالی حدود ۵۰ مگابایت را قبول می‌کند؛ برای فایل‌های بزرگ‌تر باید از [Local Bot API Server](https://core.telegram.org/bots/api#using-a-local-bot-api-server) استفاده کنی.
- فقط برای دانلود محتوایی استفاده کن که اجازه‌ی دانلودش را داری (محتوای عمومی/متن‌باز/مجاز) — رعایت قوانین کپی‌رایت بر عهده‌ی استفاده‌کننده است.

## 📄 لایسنس

MIT — آزادانه استفاده و تغییر بده.
