## ربات تلگرام جستجو و استخراج مقاله دیجیاتو (python-telegram-bot 20.7)

![Python](https://img.shields.io/badge/python-3.10+-blue.svg)
![python-telegram-bot](https://img.shields.io/badge/python--telegram--bot-20.7-2CA5E0.svg)
![License](https://img.shields.io/badge/license-MIT-green)

این پروژه یک ربات تلگرامی با کتابخانه `python-telegram-bot==20.7` است که قابلیت‌های زیر را ارائه می‌دهد:
- جستجوی مقالات دیجیاتو با صفحه‌بندی
- مشاهده لینک نتایج درون بات
- استخراج متادیتا و محتوای یک مقاله از آدرس (URL)

### پیش‌نیازها
- Python 3.10 یا جدیدتر
- ساخت Bot Token از `@BotFather` در تلگرام

### نصب وابستگی‌ها
در PowerShell:
```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

### تنظیم متغیرها
در PowerShell:
```powershell
$env:BOT_TOKEN = "توکن_ربات_شما"
# تعداد صفحات جستجو (اختیاری، پیش‌فرض 2)
$env:MAX_PAGES = "2"
```

### اجرای ربات
```powershell
python bot.py
```

### دستورات داخل تلگرام
- `/start` و `/help`: نمایش راهنما
- `/search <عبارت>`: جستجو در دیجیاتو و نمایش نتایج با دکمه‌های قابل کلیک
- `/article <url>`: استخراج عنوان، توضیحات، نویسنده، زمان انتشار و بخش‌هایی از متن مقاله

### نکات فنی
- ماژول `digiato_scraper.py` با استفاده از `requests` و `BeautifulSoup` ساختار HTML دیجیاتو را مشابه کد PHP شما پردازش می‌کند.
- مدیریت خطا، timeout و retry برای پایداری بیشتر پیاده‌سازی شده است.
- هندلرها async هستند و عملیات شبکه‌ای بلاک‌کننده در `asyncio.to_thread` اجرا می‌شود.

### توسعه و دیباگ
- برای لاگ بیشتر:
```powershell
$env:PYTHONWARNINGS = "default"
$env:LOGLEVEL = "INFO"
```
- در صورت تغییر ساختار HTML سایت، سلکتورها را در `digiato_scraper.py` به‌روزرسانی کنید. 