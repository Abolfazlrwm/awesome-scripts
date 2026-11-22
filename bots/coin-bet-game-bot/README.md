# 🎲 بازی شرط سکه (Coin Bet Game Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

بازی شرط‌بندی سکه‌ای با PHP + MySQL: کاربر روی «سر یا خط» شرط می‌بندد، موجودی و تراکنش‌ها در دیتابیس ذخیره می‌شود و پنل ادمین آمار، خاموش/روشن، تنظیم کانال و مدیریت کاربران را می‌دهد. نصب دیتابیس با `table.php` خودکار است.

## ✨ امکانات

- 🪙 شرط سکه‌ای (مبلغ شرط از موجودی، نتیجه‌ی شانسی، آپدیت coin)
- 💳 افزایش موجودی و «درخواست برداشت» با تایید ادمین
- 🔁 انتقال موجودی بین کاربران (`ubnk`)
- 👑 پنل ادمین: آمار، خاموش/روشن، تنظیم کانال، پیام به کاربر، مشخصات کاربر
- ⚙️ نصب خودکار جداول (`table.php`) + تنظیمات در `setting/setting.json`

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و ext-mysqli + دیتابیس MySQL

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `config.php` را پر کنید: `$token`، `$admin`، `$localhost`، `$name`، `$user`، `$password`.

### گام ۲: نصب جداول
یک‌بار `table.php` را در مرورگر باز کنید تا جداول ساخته شوند.

### گام ۳: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/coin-bet-game-bot/index.php
```

⚠️ شرط‌بندی با پول واقعی در بسیاری از jurisdictions ممنوع است — فقط با ارز درون‌بازی و مسئولیت قانونی خودت استفاده کن.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PHP+MySQL coin-flip betting game — balances, deposit/withdraw requests, transfers and a full admin panel; tables auto-install via `table.php`. Use with in-game currency only. **Attribution to @Abolfazl_rwm required.**
