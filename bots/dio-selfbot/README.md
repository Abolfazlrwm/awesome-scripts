# 🌟 سلف‌بات DIO (DIO Selfbot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

سلف‌بات Pyrogram برای **اکانت شخصی خودت** با تمرکز روی پروفایل: بیو و نامِ زمان‌دار (ساعت تهران به‌صورت خودکار در بیو/نام)، مدیریت حالت‌های mute/game و لیست پاسخ‌های آماده — همراه زمان‌بند APScheduler.

## ✨ امکانات

- 🕐 بیو/نامِ زمان‌دار: ساعت تهران هر دقیقه به‌روز می‌شود (قابل روشن/خاموش)
- 🔇 حالت‌های `mute` و `game` با دستورات متنی
- 📋 لیست پاسخ/فحش آماده (`list_fosh`) برای واکنش خودکار — با احتیاط استفاده کن
- 📥 کپی پیام پاسخ‌داده‌شده به Saved Messages
- ⏲ زمان‌بند APScheduler برای کارهای دوره‌ای

## 📋 پیش‌نیازها

- Python ۳.۸+، `pip install pyrogram apscheduler pytz`
- ⚠️ یوزربات است — روی اکانت خودت و با مسئولیت خودت اجرا کن.

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
در `DIO.py` مقدارهای `api_id` و `api_hash` خودت را (از [my.telegram.org](https://my.telegram.org)) جایگزین `0` و `''` کنید.

### گام ۲: اجرا
```bash
python DIO.py
```
اولین بار شماره‌ی اکانت و کد ورود می‌پرسد و سشن می‌سازد.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Pyrogram selfbot for your own account — time-aware bio/name (Tehran clock), mute/game toggles, saved-replies list, APScheduler jobs. A userbot: run at your own risk. Original opened by @Sourrce_kade (dev: @disculpas). **Attribution to @Abolfazl_rwm required.**
