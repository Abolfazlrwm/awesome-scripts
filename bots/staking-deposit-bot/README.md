# 🪙 ربات ثبت واریز استیکینگ (Staking Deposit Bot)

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

ربات وب‌هوکی PHP برای کانال‌های استیکینگ/سرمایه‌گذاری: به کاربر فرآیند واریز را با آدرس کیف‌پول نشان می‌دهد، تایمر «چالش این هفته» را نمایش می‌دهد و وضعیت‌های هر کاربر را در پوشه‌ی `user_states/` نگه می‌دارد.

## ✨ امکانات

- ⏳ شمارش معکوس «چالش هفتگی» (روز/ساعت باقی‌مانده)
- 🌟 جریان استیک: معرفی مبلغ، آدرس کیف‌پول، هشدار بررسی تراکنش
- 📈 معرفی مزایا (سود لحظه‌ای، برداشت آسان) به‌شکل پیام‌های Markdown
- 💾 ذخیره‌ی وضعیت گام‌به‌گام هر کاربر در `user_states/`
- 🔔 اعلان به کانال/آدمین با `$channel_id`

## 📋 پیش‌نیازها

- هاست PHP ۷.۴+ با ext-curl و اجازه‌ی نوشتن پوشه‌ی `user_states/`

## ⚙️ نصب و راه‌اندازی

### گام ۱: پیکربندی
ابتدای `index.php`: متغیر `$token` (توکن ربات) و `$channel_id` (کانال مقصد اعلان‌ها).

### گام ۲: ستوب‌هوک

```
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://YOUR-DOMAIN/staking-deposit-bot/index.php
```

⚠️ این کد فقط «ثبت و نمایش» است؛ فعالیت سرمایه‌گذاری واقعی هر مسئولیت قانونی با اپراتور آن است.

## 🔒 ذکر منبع

> 📬 **Telegram:** [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)

استفاده، ویرایش و بازنشر این سورس **فقط با ذکر منبع** مجاز است — حذف یا تغییر این اعتبار مجاز نیست.

---
**EN:** PHP webhook bot for staking channels — weekly-challenge countdown, deposit flow with wallet address, per-user step states in `user_states/`, admin notifications. **Attribution to @Abolfazl_rwm required.**
