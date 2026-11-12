# راهنمای نصب ربات Abolfazl (Emoji Forge) — نسخه ۱۰٫۱٫۰

ربات تلگرامی شخصی‌سازی ایموجی‌های سفارشی (TGS/WEBM): تغییر نام/لوگو/رنگ
دقیقاً در جای برند قبلی، انتشار پک در ست شخصی کاربر، پنل ادمین و صف پردازش.

**تازه‌های نسخه ۱۰٫۱٫۰:** پشتیبانی از **کلید API اختصاصی با Base URL دلخواه** —
می‌توانید هر سرویس سازگار با OpenAI را وصل کنید (OpenAI، OpenRouter،
سرور لوکال و…)؛ اگر کلید ندهید ربات با پیشنهادهای داخلی خودش کار می‌کند
و اگر سرویس خطا بدهد، خودکار به همان حالت برمی‌گردد.

---

## ۱) نصب سریع روی سرور (روش پیشنهادی) — systemd

نیازمندی: سرور لینوکسی (Ubuntu/Debian/Fedora)، Python 3.11+، دسترسی root.

```bash
unzip TZ-Emojis-Bot-v10.1-Full-Source.zip
cd TZ-Emojis-Bot
chmod +x install.sh
sudo ./install.sh
```

نصب‌کننده این کارها را می‌کند: بررسی Python و ffmpeg → ساخت `.venv` و نصب
`requirements.txt` → پرسیدن توکن ربات، آیدی ادمین‌ها و (اختیاری) کلید API
→ نوشتن `.env` با دسترسی 600 → خودآزمایی → ساخت و اجرای سرویس systemd
به نام `tz-emojis`.

بدون پرسش (از روی محیط):

```bash
EMOJI_FORGE_TOKEN='123:AA...' EMOJI_FORGE_ADMIN_IDS=123456789 \
AI_API_KEY='sk-...' AI_BASE_URL='https://api.openai.com/v1' AI_MODEL='gpt-4o-mini' \
sudo -E ./install.sh --non-interactive
```

مدیریت سرویس:

```bash
sudo systemctl status tz-emojis
sudo systemctl restart tz-emojis
journalctl -u tz-emojis -f
```

بدون systemd هم می‌شود: `./start.sh` و `./stop.sh` (لاگ در `logs/bot.log`).

## ۲) نصب روی هاست اشتراکی cPanel (پنل تحت وب)

روی هاست اشتراکی systemd ندارید؛ ربات با **Python App (Passenger)** اجرا
می‌شود و باید با کرون‌جاب زنده نگه داشته شود. روش پایدار اصلی همان سرور
است؛ این روش برای هاست‌های اشتراکی است و ممکن است هاست هر از گاهی پروسه
را بکشد.

1. زیپ را در File Manager هاست آپلود و در یک پوشه (مثلاً `tz-emojis`) باز کنید.
2. مرورگر را روی `https://YOUR-DOMAIN/tz-emojis/cpanel/install.php` باز کنید.
3. ویزارد فارسی: بررسی محیط → وارد کردن توکن (همان‌جا با تلگرام اعتبارسنجی
   می‌شود)، آیدی ادمین و در صورت تمایل کلید API + Base URL + مدل →
   فایل‌های `.env` و `app.py` ساخته می‌شوند و کلید پنل وضعیت نمایش داده می‌شود.
4. در cPanel: **Setup Python App → Create Application** → Python 3.11+ →
   Application root = همان پوشه → startup file = `app.py` → entry point =
   `application` → Create → **Run Pip Install** با `requirements.txt` → Restart.
5. از همان صفحه‌ی موفقیت نصب، دو Cron Job پیشنهادی را در cPanel → Cron Jobs
   بسازید (keep-alive هر ۵ دقیقه + restart.txt).
6. وضعیت را در `cpanel/panel.php` با همان کلید پنل ببینید.
7. **حتماً `cpanel/install.php` را حذف کنید** (در صفحه موفقیت دکمه‌اش هست).

## ۳) کلید API اختصاصی + Base URL

سه متغیر محیطی (در `.env`):

| متغیر | پیش‌فرض | توضیح |
|---|---|---|
| `AI_API_KEY` | خالی | با پر کردنش، این ارائه‌دهنده فعال و بر بقیه اولویت دارد |
| `AI_BASE_URL` | `https://api.openai.com/v1` | آدرس پایه‌ی هر endpoint سازگار با OpenAI |
| `AI_MODEL` | `gpt-4o-mini` | نام مدل روی همان سرویس |

مثال‌ها:

```bash
# OpenAI
AI_BASE_URL=https://api.openai.com/v1
AI_MODEL=gpt-4o-mini

# OpenRouter (مدل‌های متنوع با یک کلید)
AI_BASE_URL=https://openrouter.ai/api/v1
AI_MODEL=openai/gpt-4o-mini

# سرور لوکال (مثلاً LM Studio یا هر گیت‌وی سازگار)
AI_BASE_URL=http://127.0.0.1:1234/v1
AI_MODEL=local-model
```

نکته‌ها: کلید فقط در هدر `Authorization` به همان Base URL فرستاده می‌شود،
هیچ‌جا لاگ یا ذخیره نمی‌شود (به جز `.env` خودتان)، و روی هر خطای شبکه/سرور،
ربات بی‌سروصدا به پیشنهادهای داخلی برمی‌گردد — یعنی خرابی سرویس، ربات را
از کار نمی‌اندازد. بدون کلید هم ربات کامل کار می‌کند.

## ۴) اجرا، به‌روزرسانی، توقف

- سرور: `systemctl restart tz-emojis` | دستی: `./stop.sh && ./start.sh`
- cPanel: دکمه Restart در Setup Python App یا `touch tmp/restart.txt`
- به‌روزرسانی: فایل‌های جدید را روی قبلی بریزید (**`.env` و `data/` را نگه
  دارید**)، بعد `pip install -r requirements.txt` در همان venv و ری‌استارت.

## ۵) عیب‌یابی

| مشکل | راه‌حل |
|---|---|
| ربات اجرا نمی‌شود، خطای توکن | `EMOJI_FORGE_TOKEN` در `.env` خالی/اشتباه است؛ از @BotFather توکن تازه بگیرید |
| خودآزمایی خطای فونت/ffmpeg | ffmpeg برای WEBM لازم است: `sudo apt install ffmpeg`؛ نبودنش فقط WEBM را غیرفعال می‌کند |
| خطای vtracer در خودآزمایی | فقط برداری‌سازی لوگوی تصویری غیرفعال می‌شود؛ نصب: داخل requirements هست |
| روی cPanel اپ خواب می‌رود | کرون keep-alive را فعال کنید؛ یا به سرور+systemd مهاجرت کنید |
| پاسخ API سفارشی نمی‌آید | Base URL باید به `/v1` ختم شود و endpoint `/chat/completions` داشته باشد؛ خطا ربات را متوقف نمی‌کند |
| خطای 401 از تلگرام | توکن باطل شده؛ توکن جدید در `.env` و ری‌استارت |

## ۶) امنیت

- `.env` را با دسترسی 600 نگه دارید و هرگز commit/share نکنید.
- بعد از نصب cPanel، `install.php` را حذف کنید؛ `panel.php` با `PANEL_KEY`
  محافظت می‌شود و هیچ مقداری از رازها را نمایش نمی‌دهد.
- توکن ربات را دوره‌ای از BotFather عوض کنید.

## ۷) ساختار بسته

```
run.py              نقطه ورود ربات
install.sh          نصب‌کننده سرور (systemd/nohup)
start.sh / stop.sh  اجرای دستی
cpanel/             ویزارد نصب تحت وب + پنل وضعیت (PHP)
src/emoji_forge/    سورس کامل ربات
assets/             فونت‌ها + registry.json
scripts/            ابزارهای عملیاتی (صف پردازش، shrink و…)
tests/              تست‌های خودکار (pytest)
data/               داده‌های اجرایی (خالی در بسته؛ خودکار ساخته می‌شود)
systemd/            قالب واحد systemd
README-FA.md        راهنمای کامل ربات | BRAIN.md معماری موتور پیشنهاد
```

> اسکریپت‌های یک‌بارمصرف تعمیر پک‌های شخصی مالک (حاوی آیدی/ست‌های شخصی)
> و فایل‌های پروژه‌های مشتریان در این بسته‌ی توزیعی نیستند؛ سورس خود ربات
> کامل و بدون آن‌ها قابل اجراست. دارایی `data/logo_e30tz.json` هم فقط
> مصرف همان اسکریپت‌های قدیمی بود — موتور ربات نسخه‌ی داخلی خودش را در
> `src/emoji_forge/abolfazl_logo_shapes.json` دارد.
