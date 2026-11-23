# 🗜 فشرده‌ساز مدیا با ffmpeg (Media Compressor Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات PTB با موتور ffmpeg: ویدیوها را به H.265 با CRF تنظیم‌شده و تصاویر را با کیفیت ۸۵ فشرده می‌کند؛ برای کم‌حجم‌کردن فایل‌ها قبل از ارسال در کانال‌ها ساخته شده است.

## ✨ امکانات

- فشرده‌سازی ویدیو با libx265 (CRF 30، ultrafast)
- فشرده‌سازی تصویر با حفظ کیفیت
- پنل ادمین و لاگ عملیات کاربران
- ذخیره‌سازی SQLite

## 📋 پیش‌نیازها

- Python ۳.۸+، `pip install python-telegram-bot ffmpeg-python` و نصب `ffmpeg` روی سیستم

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
1. توکن ربات و آیدی ادمین را در `bot.py` وارد کنید.

### گام ۲: اجرا
```bash
python bot.py
```

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PTB media compressor powered by ffmpeg — H.265 video, image re-encode, admin panel and SQLite logging. **Attribution to @Abolfazl_rwm required.**
