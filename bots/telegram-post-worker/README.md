# 📡 مدیریت پست روی Cloudflare Worker (Telegram Post Worker)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

پیاده‌سازی کامل یک ربات مدیریت پست روی Cloudflare Worker (بدون سرور): ساخت پست مرحله‌به‌مرحله، حذف پست، پنل ادمین و ارسال به چند کانال هدف — همه در یک فایل `worker.js` با مسیریابی webhook تلگرام.

## ✨ امکانات

- بدون سرور: اجرای کامل روی Cloudflare Workers
- `/newpost` ساخت پست مرحله‌به‌مرحله
- `/deletepost` حذف با شناسه
- مدیریت چند کانال هدف
- پنل ادمین با `/panel`

## 📋 پیش‌نیازها

- حساب Cloudflare با Workers (پلن رایگان کافی است)

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
1. با `wrangler` دیپلوی کنید؛ توکن ربات و آیدی ادمین را در متغیرهای محیطی Worker ست کنید.

### گام ۲: اجرا
```bash
npx wrangler deploy
```

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Serverless Telegram post manager on Cloudflare Workers — staged post creation, deletion, multi-channel targeting and an admin panel in one JS file. **Attribution to @Abolfazl_rwm required.**
