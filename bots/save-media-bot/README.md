# 💾 ذخیره‌کننده‌ی مدیا کانال (Save Media Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات PHP نوشته‌شده توسط @devbc که مدیای کانال‌ها را به کانال ذخیره‌ی شما فوروارد می‌کند و پیش‌نمایش لینک را کنارش می‌فرستد؛ امنیت وب‌هوک با رنج IP تلگرام رعایت شده است.

## ✨ امکانات

- فوروارد خودکار مدیا به کانال ذخیره
- پیش‌نمایش لینک پست در پیام
- امنیت وب‌هوک با محدوده‌ی IP تلگرام
- تنظیمات ساده در چهار ثابت

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با SSL

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
1. در `Savemedia.php` مقادیر `$token`، `$idbot`، `$admin` و `$send` (آیدی کانال ذخیره) را بگذارید؛ سپس وب‌هوک را ست کنید.

### گام ۲: اجرا
```bash
# روی هاست: php -S localhost:8000 برای تست
# و setWebhook به آدرس فایل
```

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PHP webhook bot by @devbc that mirrors channel media into a storage channel with link previews. **Attribution to @Abolfazl_rwm required.**
