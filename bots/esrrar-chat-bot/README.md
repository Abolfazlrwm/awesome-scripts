# 💬 چت ناشناس اسرار (Esrrar Anonymous Chat Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات گفتگوی ناشناس دوطرفه: کاربران بدون دیدن هویتِ هم با «کاربر مقابل» یا با فروشنده/مدیر گفتگو می‌کنند؛ پیام‌ها از طریق ربات رد و بدل می‌شوند. به‌همراه پیام همگانی، دروازه‌ی عضویت، تاریخ شمسی (jdf) و یک **رانر Telethon/Flask** برای لاگین سشن کمکی.

## ✨ امکانات

- 🗣 چت دوطرفه‌ی ناشناس («پیام خود را برای کاربر مقابل ارسال کنید»)
- 🛒 پیام به فروشنده — مناسب پشتیبانی فروشگاه‌های داخل تلگرام
- 📣 پیام همگانی (broadcast) از پنل مدیر
- 🚪 دروازه‌ی عضویت اجباری در کانال
- 📅 تاریخ و ساعت شمسی با کتابخانه‌ی `jdf.php`
- 🐍 `run.py`: رانر کمکی Telethon + Flask برای ساخت سشن (مدیریت api_id/api_hash به‌ازای هر شماره در `session/`)
- 🛡 خروج سریع وب‌هوک (litespeed/fastcgi) و پاک‌سازی ورودی

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ برای ربات اصلی (وب‌هوک)
- Python ۳.۸+ فقط اگر خواستید رانر سشن را اجرا کنید
- یک ربات از [@BotFather](https://t.me/BotFather)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی ربات PHP
فایل `index.php` را باز کنید و بخش `API_KEY` را پر کنید:

| متغیر | توضیح |
|---|---|
| `API_KEY` | توکن ربات از BotFather |
| ادمین/کانال | بخش‌های علامت‌گذاری‌شده در ابتدای فایل |

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/esrrar-chat-bot/index.php
```

### گام ۳ (اختیاری): رانر سشن
```bash
pip install -r requirements.txt
python run.py   # سرویس Flask بالا می‌آید؛ طبق متن آموزش داخل فایل شماره را لاگین کنید
```

## 📄 اعتبار و لایسنس

کد اصلی از کانال **سورس کده** (open @ESRRAR) — بازنشر تحت **MIT**.

---
**EN:** Anonymous two-way chat bot: users talk to each other or to the seller/admin without revealing identities, plus admin broadcast, mandatory channel join and Persian (Jalali) dates. Ships a Telethon/Flask session runner (`run.py`). Original by Sourcekade (@ESRRAR), MIT.
