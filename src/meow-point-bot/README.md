# 🐱 ربات میو پوینت (Meow Point Bot)

ربات بازی و اقتصاد گروهی تلگرام با محوریت گربه‌ها — جمع‌آوری امتیاز، سطح‌بندی، بانک شخصی، کارخانه، بازار خرید و جنگ گربه‌ها بین اعضای گروه.

![Python](https://img.shields.io/badge/python-3.8+-blue.svg)
![python-telegram-bot](https://img.shields.io/badge/python--telegram--bot-latest-2CA5E0.svg)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ امکانات

- `/meow` — جمع‌آوری امتیاز با کول‌داون (هر ۵ دقیقه)
- `/profile` — نمایش پروفایل اختصاصی (امتیاز، سطح، تجربه، حیوان خانگی)
- `/top` — لیدربورد ۱۰ نفر برتر گروه
- `/transfer [مقدار] @username` — انتقال امتیاز به دیگران
- `/bank`, `/deposit`, `/withdraw` — بانک شخصی برای واریز و برداشت
- `/factory`, `/upgrade_factory`, `/collect_factory` — کارخانه‌ی تولید قابل ارتقا
- `/market`, `/buy` — بازار خرید کالا (استخوان، قلاب ماهیگیری، گربه‌ها، سگ نگهبان)
- `/city` — آمار و وضعیت کلی گروه
- `/games` — منوی بازی‌ها
- `/fight` — جنگ گربه‌ها (با ریپلای به پیام حریف)
- ⭐ سیستم سطح‌بندی خودکار بر اساس تجربه
- 💾 پایگاه‌داده‌ی SQLite داخلی — نیازی به تنظیم جداگانه ندارد

### 🛠 دستورات ادمین/مالک

- `/addpoints @username [مقدار]` — افزایش امتیاز کاربر
- `/removepoints @username [مقدار]` — کاهش امتیاز کاربر
- `/addlevel @username [تعداد]` — افزایش سطح کاربر
- `/removelevel @username [تعداد]` — کاهش سطح کاربر
- `/ban @username [دقیقه]` — بن موقت کاربر
- `/unban @username` — رفع بن کاربر

## 📋 پیش‌نیازها

- Python نسخه‌ی ۳.۸ یا بالاتر (یا Termux روی اندروید)

## ⚙️ نصب و راه‌اندازی

### روی کامپیوتر (ویندوز/لینوکس/مک)

```bash
pip install -r requirements.txt
```

### روی گوشی اندروید (با Termux)

```bash
pkg update && pkg upgrade -y
pkg install python -y
pip install -r requirements.txt
```

### تنظیمات اولیه

فایل `bot.py` را باز کن و مقادیر زیر را (نزدیک ابتدای فایل) جایگزین کن:

| متغیر | توضیح |
|---|---|
| `BOT_TOKEN` | توکن ربات از [@BotFather](https://t.me/BotFather) |
| `OWNER_ID` | آیدی عددی خودت از [@userinfobot](https://t.me/userinfobot) |
| `GROUP_LINK` | لینک گروه یا کانالت (برای دکمه‌ی «افزودن به گروه») |

### اجرا

```bash
python bot.py
```

پایگاه‌داده (`bot.db`) به‌صورت خودکار در همان پوشه ساخته می‌شود.

## ⚠️ نکته‌ی امنیتی

بعد از وارد کردن `BOT_TOKEN` واقعی خودت، **هرگز** فایل `bot.py` را با توکن پرشده در جایی عمومی (مثل گیت‌هاب) آپلود نکن. اگر می‌خواهی نسخه‌ی خودت را با گیت مدیریت کنی، توکن را در یک فایل جدا (مثل `.env`) نگه‌دار و آن فایل را داخل `.gitignore` قرار بده.

## 📄 لایسنس

MIT — آزادانه استفاده و تغییر بده.
