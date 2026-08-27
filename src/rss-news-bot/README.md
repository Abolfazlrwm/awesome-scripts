# 📰 ربات اعلان خودکار فید RSS (RSS News Bot)

فید RSS هر سایت خبری یا وبلاگی رو بهش معرفی کن، خودش هر چند دقیقه چک می‌کنه و پست‌های جدید رو خودکار توی کانال یا گروهت پست می‌کنه.

![C++](https://img.shields.io/badge/C%2B%2B-17-00599C.svg)
![libcurl](https://img.shields.io/badge/uses-libcurl-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ امکانات

- 📡 بررسی دوره‌ای چند فید RSS هم‌زمان (پیش‌فرض هر ۵ دقیقه)
- 🆕 تشخیص هوشمند پست‌های جدید (بدون ارسال تکراری)
- 📢 ارسال خودکار به کانال یا گروه مشخص برای هر فید
- ➕ افزودن/حذف/لیست فید مستقیم از داخل تلگرام (`/addfeed`, `/removefeed`, `/listfeeds`)
- 💾 ذخیره‌سازی با فایل JSON — بدون نیاز به دیتابیس خارجی
- ⚡️ نوشته‌شده با C++ خالص برای کمترین مصرف منابع، مناسب اجرای دائمی روی سرورهای کم‌امکانات

## 📋 پیش‌نیازها

- کامپایلر C++17 یا بالاتر (`g++`)
- کتابخانه‌ی `libcurl` و `nlohmann-json`

### نصب پیش‌نیازها (اوبونتو/دبیان)
```bash
sudo apt update
sudo apt install -y build-essential libcurl4-openssl-dev nlohmann-json3-dev
```

## ⚙️ نصب و راه‌اندازی

### گام ۱: تنظیمات اولیه
فایل `main.cpp` را باز کن و این مقادیر را جایگزین کن:

| متغیر | توضیح |
|---|---|
| `BOT_TOKEN` | توکن ربات از [@BotFather](https://t.me/BotFather) |
| `ADMIN_ID` | آیدی عددی ادمین از [@userinfobot](https://t.me/userinfobot) |
| `CHECK_INTERVAL_SECONDS` | فاصله‌ی بررسی فیدها (ثانیه، پیش‌فرض ۳۰۰ = ۵ دقیقه) |

### گام ۲: کامپایل
```bash
make
```
یا مستقیم بدون Makefile:
```bash
g++ -std=c++17 -O2 main.cpp -o rss-news-bot -lcurl
```

### گام ۳: اجرا
```bash
./rss-news-bot
```

### گام ۴: افزودن فید
داخل کانال یا گروه موردنظر (که ربات باید عضوش باشد، برای کانال هم ادمین باشد):
```
/addfeed https://example.com/rss.xml
```

## 💡 نکته

برای اجرای دائمی روی سرور، پیشنهاد می‌شود از `systemd` یا `screen`/`tmux` استفاده کنی تا بعد از بستن ترمینال هم ربات روشن بماند.

## 📄 لایسنس

MIT — آزادانه استفاده و تغییر بده.
