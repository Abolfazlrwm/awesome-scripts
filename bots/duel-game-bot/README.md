# ⚔️ ربات دوئل گروهی (Robot Duel Game Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

بازی دوئل گروهی با Telethon: اعضای گروه به هم دوئل می‌دهند، نتیجه با شانس و جزئیات مبارزه اعلام می‌شود و آمار در SQLite نگه‌داری می‌شود (`BotData.db` به‌صورت خودکار ساخته می‌شود).

## ✨ امکانات

- 🥊 دوئل تک‌به‌تک بین اعضا با نتایج متنوع (۶۲ رشته‌ی دوئل)
- 📊 آمار بازیکنان در دیتابیس SQLite (خودکار ساخته می‌شود)
- 🧰 اسکریپت کمکی `String_session.py` برای ساخت StringSession
- 📖 راهنمای فارسی در `docs/rahnama.txt`

## 📋 پیش‌نیازها

- Python ۳.۸+ و `pip install telethon`

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `info.py` را پر کنید: `api_id`، `api_hash` ([my.telegram.org](https://my.telegram.org))، `token` (توکن ربات از BotFather) و `owner` (آیدی عددی مالک).

### گام ۲: اجرا
```bash
python main.py
```
ربات را به گروه اضافه کنید و با دستورات دوئل بازی را شروع کنید.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Telethon group duel game — members challenge each other, results announced with flavor text, stats persisted in auto-created SQLite. Config via `info.py`; a StringSession helper ships along. **Attribution to @Abolfazl_rwm required.**
