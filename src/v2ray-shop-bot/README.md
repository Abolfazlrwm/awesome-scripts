# ویتوری رایگان | free v2ray — کپی ربات @ShopVipIr_Bot

ربات فروشگاهی تلگرام (فروش کانفیگ V2Ray / OpenVPN، کیف پول، سرویس تست رایگان) — بازنویسی تمیز و از صفر بر اساس رفتار ظاهری ربات اصلی، با aiogram 3.x و SQLite در یک فایل `bot.py`.

## نصب و اجرا

```bash
pip install -r requirements.txt
cp .env.example .env   # توکن و آیدی ادمین‌ها را پر کنید
python bot.py
```

متغیرهای `.env`: ‏`BOT_TOKEN`، ‏`BOT_USERNAME`، ‏`ADMIN_IDS` (جدا با کاما)، ‏`DB_PATH` (اختیاری).

## تنظیمات BotFather

- **نام ربات:** ویتوری رایگان | free v2ray
- **دستورات (Commands):**
  ```
  start - منوی اصلی
  help - راهنمای اتصال
  ```

## رنگ دکمه‌ها — چرخهٔ رنگی (مهم)

در ربات اصلی دکمه‌های اینلاین در تلگرام‌وب به ترتیب قرارگیری، با یک چرخهٔ تکراری رنگ می‌گیرند: دکمهٔ ۱ آبی (`primary`)، دکمهٔ ۲ سبز (`success`)، دکمهٔ ۳ قهوه‌ای/نارنجی (`danger`)، دکمهٔ ۴ دوباره آبی و… در این نسخه **همهٔ** کیبوردها از تابع `styled_kb()` ساخته می‌شوند که دقیقاً همین چرخه را با `style = STYLE_CYCLE[index % 3]` بر اساس ترتیب تخت دکمه‌ها اعمال می‌کند؛ پس توالی رنگ هر صفحه با اصل ربات یکی است.

## ایموجی‌های پرمیوم

در ربات اصلی **هیچ ایموجی پرمیوم/کاستومی دیده نشد** — همه ایموجی‌ها یونیکد استانداردند. برای همین `CUSTOM_EMOJI_IDS` در بالای `bot.py` خالی است و متن دکمه‌ها همان ایموجی‌های استاندارد را دارد.

## رفتارهای کلیدی

- **گیت عضویت اجباری:** فقط گروه `@SATO_GP` (قابل تنظیم از پنل ادمین: روشن/خاموش + نام کانال).
- **منوی اصلی، فروشگاه** (V2ray ‏۲٬۰۰۰ تومان، Open vpn ‏۴٬۰۰۰، V2ray - حجمی ‏۱۵٬۰۰۰، V2ray 20gb ‏۵۰٬۰۰۰ تومان — همه ۳۰ روزه و قابل ویرایش از پنل ادمین).
- **پرداخت کارت‌به‌کارت** با ارسال رسید عکس و تأیید/رد ادمین؛ شماره کارت پیش‌فرض `6219861430412378` به نام «عربزاده» (قابل ویرایش).
- **پرداخت از کیف پول، کد تخفیف، کیف پول** (شارژ کارت‌به‌کارت با حداقل ۱۰٬۰۰۰ تومان، شارژ با استارز تلگرام XTR با نرخ ۵٬۰۰۰ تومان برای هر استار، کد هدیه، ادامهٔ پرداخت فاکتور پرداخت‌نشده).
- **سرویس تست رایگان:** یک‌بار برای هر کاربر، ۰٫۱ گیگ / ۱ روز، همراه فایل `.ovpn`.
- **سرویس‌های من:** نمایش انقضا به تاریخ جلالی، نوار مصرف و دانلود فایل کانفیگ `.ovpn` با کپشن «فایل کانفیگ 🔓».
- **دعوت دوستان:** بعد از اولین خرید فرد دعوت‌شده، ۲٬۵۰۰ تومان به معرف (قابل تنظیم).
- **پشتیبانی:** ارسال پیام کاربر برای همهٔ ادمین‌ها + پاسخ ادمین از داخل ربات.
- **قاعدهٔ وضعیت (FSM):** زدن هر دکمهٔ دیگری هنگام انتظار برای ورود متن، وضعیت قبلی را لغو می‌کند؛ `/start` و `/cancel` همیشه از حالت انتظار خارج می‌شوند.
- ناوبری منوها با ویرایش همان پیام (edit-in-place) انجام می‌شود؛ پرامپت‌های ورود متن پیام جدیدند.

## پنل ادمین

با دستور `/admin` (فقط برای `ADMIN_IDS`): آمار (کاربر، سرویس، فاکتور، درآمد، مجموع موجودی‌ها)، رسیدهای در انتظار با تأیید/رد، ویرایش قیمت و حجم محصولات و فعال/غیرفعال‌کردن آن‌ها، ویرایش تنظیمات (شماره کارت، صاحب کارت، گیت، حداقل شارژ، نرخ استار، پاداش دعوت، لینک پشتیبانی)، ساخت/حذف کد تخفیف، ساخت کد هدیه، فهرست صفحه‌بندی‌شدهٔ کاربران با افزودن/کسر موجودی و بن/آن‌بن، و ارسال همگانی (متن یا عکس) با پیش‌نمایش تأیید و گزارش نتیجه.

## بخش‌های استاب (TODO)

ساخت کانفیگ واقعی از پنل Xray/OpenVPN پیاده‌سازی نشده است: `generate_config_text()` یک فایل `.ovpn` نمونه (placeholder) تولید می‌کند که باید به پنل واقعی وصل شود. بقیهٔ رفتارهای قابل مشاهده (متن‌ها، دکمه‌ها، فلوها) کامل پیاده شده‌اند. جزئیات و موارد استنباطی در `REPORT.md` آمده است.

---

## 🇬🇧 English summary

# Free V2Ray Shop — replica of @ShopVipIr_Bot

A Telegram shop bot (selling **V2Ray / OpenVPN configs**, wallet, free trial service) — rewritten cleanly from scratch based on the original bot's visible behaviour, with **aiogram 3.x + SQLite** in a single `bot.py` file.

## Setup

```bash
cd v2ray-shop-bot
python3 -m venv .venv && . .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env   # BOT_TOKEN, BOT_USERNAME, ADMIN_IDS (comma separated), optional DB_PATH
python bot.py
```

## Key behaviours

- **Colour-cycling inline buttons** — the original colours buttons primary/success/danger in a repeating cycle by flat order; every keyboard here is built through `styled_kb()` reproducing exactly that sequence
- **Force-join gate** — one configurable group/channel (on/off + name editable from the admin panel)
- **Shop** — V2ray 2,000 T · OpenVPN 4,000 T · V2ray volume-based 15,000 T · V2ray 20GB 50,000 T (all 30 days, editable from the admin panel)
- **Card-to-card payment** — user sends a receipt photo, admin approves/rejects; wallet balance grows automatically
- **Free trial service** + help/guide screens
- **Admin panel** — prices, gate, orders, users, broadcasts
- Full Persian documentation above; bot texts are Persian by design

## Honest notes

- `CUSTOM_EMOJI_IDS` ships empty (the original used standard unicode emojis only)
- Delivery of real configs is handled manually by the admin after payment approval (as in the original flow)

---

<div align="center">

**Maintained by [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)** · MIT License

</div>
