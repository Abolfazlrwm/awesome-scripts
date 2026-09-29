# 🔎 Forward Info Bot | ربات نمایش اطلاعات پیام فورواردی

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![Telegram Bot API](https://img.shields.io/badge/Telegram%20Bot%20API-Webhook-26A5E4?logo=telegram&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

## 📖 توضیحات

ربات کوچک و کاربردی تلگرامی که وقتی کاربر یک پیام را از یک **کاربر دیگر**، **ربات دیگر** یا یک **کانال** به آن فوروارد می‌کند، اطلاعات فرستنده‌ی اصلی (آیدی عددی، نام، یوزرنیم یا آیدی کانال) را نمایش می‌دهد. همچنین آیدی عددی هر کاربری که برای اولین بار با ربات صحبت می‌کند را در فایل `members.txt` ذخیره می‌کند.

مناسب برای: پیدا کردن آیدی عددی کاربران، شناسایی کانال‌های ناشناس، دیباگ ربات‌های دیگر.

## ✨ ویژگی‌ها

- تشخیص فوروارد از **کاربر**، از **ربات**، و از **کانال**
- نمایش نام، یوزرنیم و آیدی عددی فرستنده‌ی اصلی
- پیام راهنما برای پیام‌های متنی معمولی
- ذخیره‌ی خودکار آیدی اعضای جدید در `members.txt`
- بدون نیاز به دیتابیس — فقط PHP خام و Webhook

## ⚙️ نصب و راه‌اندازی

1. یک هاست با پشتیبانی PHP 7.4+ و HTTPS معتبر تهیه کنید.
2. توکن ربات را از [@BotFather](https://t.me/BotFather) بگیرید.
3. فایل `bot.php` را روی هاست آپلود کنید.
4. توکن را به‌صورت متغیر محیطی `BOT_TOKEN` تنظیم کنید (یا به‌صورت موقت داخل کد جایگزین `YOUR_BOT_TOKEN_HERE` کنید — توصیه نمی‌شود).
5. Webhook را تنظیم کنید:

```bash
curl -F "url=https://YOUR_DOMAIN/bot.php" \
  "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook"
```

6. تمام! حالا کافیست پیامی را برای ربات فوروارد کنید.

## 🧩 نیازمندی‌ها

- PHP 7.4 یا بالاتر
- `allow_url_fopen` فعال (برای ارسال درخواست به API تلگرام)
- دسترسی نوشتن در پوشه‌ی اجرای اسکریپت (برای ساخت `members.txt`)

## ⚠️ نکات امنیتی

- توکن ربات هرگز نباید در کد یا مخزن عمومی قرار بگیرد؛ همیشه از متغیر محیطی استفاده کنید.
- فایل `members.txt` حاوی داده‌ی زمان اجراست و در `.gitignore` قرار دارد؛ آن را در مخزن commit نکنید.

---

## English

A lightweight Telegram bot that reveals the original sender's info (user ID, name, username, or channel ID) when a message is **forwarded** to it from a user, bot, or channel. New chatters' IDs are auto-logged to `members.txt`.

**Setup:** upload `bot.php` to any PHP 7.4+ HTTPS host, set the `BOT_TOKEN` environment variable, then register the webhook with the Telegram Bot API (see the `setWebhook` call above). No database required.

**Security:** never hardcode your bot token in source control — always use an environment variable, and keep `members.txt` (runtime data) out of git via `.gitignore`.
