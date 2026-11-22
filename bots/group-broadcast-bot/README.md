# 📣 ربات ارسال همگانی به گروه‌ها (Group Broadcast Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات کوچک وب‌هوکی PHP که پیام مدیر را به **همه‌ی گروه‌هایی که خودش عضو آن‌هاست** می‌فرستد و ابزارهای چک/مشاهده‌ی وضعیت هم دارد — مناسب اعلامِ آپدیت به شبکه‌ی گروه‌های خودت.

## ✨ امکانات

- 📤 ارسال همگانی پیام به تمام گروه‌های عضوِ ربات
- ✅ ابزارهای چک و مشاهده‌ی وضعیت ارسال
- 🛡 اجازه‌ی درخواست فقط از IPهای رسمی تلگرام
- 🪶 دو فایل ساده (`bot.php` + `functions.php`)، بدون دیتابیس

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl
- ربات از [@BotFather](https://t.me/BotFather) + عضویت در گروه‌های مقصد

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
توکن و آیدی ادمین را در `bot.php` تنظیم کنید (بخش توکن).

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/group-broadcast-bot/bot.php
```

### گام ۳: تست
پیام ادمین را با دکمه‌ی «ارسال به گروه‌هایی که عضو است» بفرستید.

⚠️ فقط برای گروه‌هایی که خودت عضو/مدیرشان هستی استفاده کن — ارسال انبوه به چت‌های ناشناس یا گروه‌های دیگران اسپم است و جایز نیست.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Tiny PHP webhook bot that broadcasts the admin's message to every group the bot itself is a member of, plus simple check utilities. Use only on your own groups. **Attribution to @Abolfazl_rwm required.**
