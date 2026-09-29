# 🎰 Lottery & Giveaway Bot | ربات قرعه‌کشی و گیوای

![Python](https://img.shields.io/badge/Python-3.10%2B-3776AB?logo=python&logoColor=white)
![aiogram](https://img.shields.io/badge/aiogram-3.x-2CA5E0?logo=telegram&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

## 📖 توضیحات

یک ربات تلگرامی کامل برای ساخت و مدیریت **قرعه‌کشی و گیوای** در کانال‌ها و گروه‌ها. هر کاربر می‌تواند گیوای خودش را بسازد، شانس شرکت‌کنندگان را از طریق دعوت دوستان (رفرال) و عضویت اجباری در کانال افزایش دهد، و در پایان برنده(ها) به‌صورت تصادفی و شفاف انتخاب می‌شوند. داده‌ها به‌صورت فایل JSON با بک‌آپ خودکار ذخیره می‌شوند — بدون نیاز به دیتابیس جداگانه.

> ℹ️ این ربات صرفاً امتیاز/شانس شرکت در قرعه‌کشی را مدیریت می‌کند و به هیچ درگاه پرداخت یا تراکنش مالی واقعی متصل نیست.

## ✨ ویژگی‌ها

- ✅ ساخت گیوای اختصاصی توسط هر کاربر
- ✅ عضویت اجباری در یک یا چند کانال به‌عنوان شرط شرکت
- ✅ لینک رفرال اختصاصی برای هر گیوای (افزایش شانس با دعوت دوستان)
- ✅ پنل مدیریت گیوای برای سازنده (ویرایش، حذف، مشاهده‌ی آمار و رفرال‌ها)
- ✅ پنل ادمین کامل: آمار کلی، ارسال همگانی (متنی و فوروارد)، مدیریت گیوای‌ها
- ✅ قرعه‌کشی تصادفی و شفاف با اعلام برنده(ها) در پیام
- ✅ چندزبانه (ساختار `TEXTS` برای افزودن آسان زبان‌های جدید)
- ✅ ذخیره‌سازی امن با نوشتن اتمیک (`.tmp` → `os.replace`) و فایل بک‌آپ خودکار

## ⚙️ نصب و راه‌اندازی

```bash
git clone <این-مخزن>
cd lottery-giveaway-bot
pip install -r requirements.txt
cp .env.example .env
# مقادیر BOT_TOKEN و ADMIN_ID را در .env تنظیم کنید
python bot.py
```

## 🔧 پیکربندی (`.env`)

| متغیر | توضیح |
|---|---|
| `BOT_TOKEN` | توکن ربات از [@BotFather](https://t.me/BotFather) |
| `ADMIN_ID` | آیدی عددی مدیر اصلی ربات |
| `OWNER_USERNAME` | یوزرنیم نمایش‌داده‌شده در بخش پشتیبانی |
| `BOT_DISPLAY_NAME` | نامی که در پنل مدیریت و متن معرفی نمایش داده می‌شود |

## 🧩 نیازمندی‌ها

- Python 3.10+
- `aiogram` نسخه‌ی 3.x
- `python-dotenv`

## ⚠️ نکات امنیتی

- توکن ربات و آیدی ادمین هرگز نباید در کد commit شوند؛ همیشه از `.env` استفاده کنید (فایل `.env` در `.gitignore` قرار دارد).
- فایل‌های `bot_data.json` و `bot_data_backup.json` حاوی داده‌ی زمان اجراست و نباید در مخزن قرار گیرد.

---

## English

A full-featured Telegram giveaway/lottery bot. Any user can create their own giveaway, boost entries via mandatory channel-join and referral links, and a fair random draw picks the winner(s) at the end. Data is stored in a JSON file with atomic writes and automatic backups — no external database needed.

**Features:** per-user giveaway creation, mandatory-join channels, referral-based extra entries, creator management panel, full admin panel (stats, broadcast, giveaway management), transparent random draws, and an easily extensible multi-language text system.

**Setup:** `pip install -r requirements.txt`, copy `.env.example` to `.env`, fill in `BOT_TOKEN` and `ADMIN_ID`, then `python bot.py`.

**Note:** this bot only manages entry chances for a giveaway — it has no payment gateway or real-money transactions.
