# 📮 API میل موقت (Temp-Mail API)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

wrapper سبک PHP روی API عمومی **temp-mail.io (v2)**: ساخت ایمیل موقت، دریافت لیست پیام‌ها و خواندن محتوا — همه‌چیز با خروجی JSON تمیز، مناسب ساخت سرویس‌های ثبت‌نام موقت یا تست ایمیل.

## ✨ امکانات

- 📧 ساخت صندوق ایمیل موقت (`POST` /auth-by-new-address)
- 📬 دریافت پیام‌ها و محتوا از همان صندوق
- 🧾 خروجی JSON با `Content-Type` درست و مدیریت خطای cURL
- 🪶 تک‌فایل، بدون دیتابیس و بدون کلید

## 🚀 اجرا

فایل `index.php` را روی هر هاست PHP با ext-curl بگذارید و با پارامتر `action` صدا بزنید؛ پاسخ JSON است.

⚠️ میل موقت برای حریم خصوصی و تست است — از آن برای دورزدن قوانین سرویس‌ها یا تخریب استفاده نکن.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** Single-file PHP wrapper around the public temp-mail.io v2 API — create disposable inboxes and read messages, clean JSON output, zero config. Privacy/testing use only. **Attribution to @Abolfazl_rwm required.**
