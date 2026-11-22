# 🔃 سوپرتبچی کپی‌کن پست (SuperTabchi Copy Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

تبچی Pyrogram با **زمان‌بند APScheduler**: پست‌های کانال مبدأ را با فاصله‌ی زمانی تعیین‌شده به گفتگوها/کانال‌های مقصد `copy_message` می‌کند — لیست تایمرها و مقصدها در حافظه مدیریت می‌شود و ادمین با دستورات متنی آن را می‌چیند.

## ✨ امکانات

- 📋 کپی پست‌های مبدأ به مقصدها (`copy_message` — بدون برچسب فوروارد)
- ⏱ تایمرهای جداگانه برای گروه‌ها و کانال‌ها (`Timerg`/`Timerp`)
- 🎯 مقصدهای مجزا (`AdCHIDP`/`AdIDP`) و مدیریت با دستورات
- 🔄 دوبار اجرای زمان‌بند (`max_instances: 2`) برای ارسال‌های همپوشان

## 📋 پیش‌نیازها

- Python ۳.۸+، `pip install pyrogram apscheduler`
- ⚠️ یوزربات — فقط برای کانال‌هایی که مالک/ادمین آن‌ها هستی.

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
در `SuperTabchi.py` مقادیر `api_id` و `api_hash` خودت را جایگزین خالی‌ها کنید.

### گام ۲: اجرا
```bash
python SuperTabchi.py
```
اولین اجرا ادمین اول را می‌پرسد؛ بقیه با دستورات ادمین اضافه می‌شوند.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Pyrogram tabchi with APScheduler — copies source-channel posts to destinations without the forward header, separate group/channel timers, admin-driven setup. Userbot: use on channels you own. **Attribution to @Abolfazl_rwm required.**
