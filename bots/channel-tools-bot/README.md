# 🛠 ابزار کانال PHP (Channel Tools PHP Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات کمکیِ سمت کانال به‌سبک وب‌هوکی تک‌فایلی: دروازه‌ی عضویت، دفتر اعضا، کنترل پین/ادیت/حذف پیام، کلید روشن/خاموش ربات و دکمه‌های لینک شفاف با شناسه‌ی `g` — پیکربندی فقط با ویرایش چند متغیر در ابتدای فایل.

## ✨ امکانات

- 🚪 دروازه‌ی عضویت: قبل از نشان‌دادن محتوای `/start`، عضویت کاربر در کانال بررسی می‌شود (`getChatMember`)
- 🧾 دفتر اعضا: هر عضو جدید در `allmember.txt` ثبت می‌شود
- 📌 کنترل پیام: پین، ادیت و حذف پیام با توابع آماده (`pinChatMessage`، `EditMessage`، `delete_message`)
- 🔛 کلید روشن/خاموش ربات — در حالت خاموش هر پیام با کیبورد حذف‌شده پاسخ می‌گیرد
- 🔗 دکمه‌های لینک شفاف با تبدیل شناسه‌ی کانال (`-100...` → `g...`)
- 🗃 فایل‌محور: وضعیت هر چت در `data/<chat_id>/` ذخیره می‌شود — بدون دیتابیس
- 🛡 اجازه‌ی درخواست فقط از IPهای رسمی تلگرام + ذخیره‌ی یوزرنیم کانال در `ch.txt`

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و اجازه‌ی نوشتن فایل
- یک ربات از [@BotFather](https://t.me/BotFather) + عضویت ربات در کانال با دسترسی ادمین

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
فایل `clob.php` را باز کنید و بخش `#Edit` را پر کنید:

| متغیر | توضیح | نمونه |
|---|---|---|
| `$token` | توکن ربات از BotFather | `123456:ABC-DEF...` |
| `$channel` | یوزرنیم کانال در فایل `ch.txt`، بدون @ | `mychannel` |
| `$admin` | آیدی عددی مدیر | `123456789` |
| `$idbot` | یوزرنیم خود ربات، بدون @ | `mytoolsbot` |

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/channel-tools-bot/clob.php
```

### گام ۳: تست
ربات را استارت کنید؛ باید محتوای استارت فقط پس از عضویت در کانال نمایش داده شود و عضو در دفتر اعضا ثبت شود.

## 📄 اعتبار و لایسنس

کد اصلی از مجموعه‌ی **سورس کده** — بازنشر تحت **MIT**.

---
**EN:** Single-file PHP channel-side utility bot: membership gate before `/start` content, member log (`allmember.txt`), pin/edit/delete controls, an on/off switch, transparent `g-id` link buttons and file-based per-chat state. Config via the `#Edit` block at the top. Original from the Sourcekade collection, MIT.
