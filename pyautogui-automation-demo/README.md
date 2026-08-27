# 🖱 ابزار اتوماسیون دسکتاپ | Desktop Automation Demo (PyAutoGUI)

یک ابزار خط‌فرمان سبک و امن برای تست اتوماسیون دسکتاپ با PyAutoGUI: تایپ خودکار متن، گرفتن اسکرین‌شات و کلیک روی مختصات دلخواه — همه از طریق آرگومان‌های قابل تنظیم.

![Python](https://img.shields.io/badge/Python-3.8%2B-3776AB.svg)
![PyAutoGUI](https://img.shields.io/badge/PyAutoGUI-automation-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ امکانات

- ⏳ شمارش معکوس قبل از شروع، تا فرصت کنی پنجره مقصد را باز/فعال کنی
- ⌨️ تایپ خودکار یک پیام دلخواه با فاصله‌ی زمانی قابل تنظیم بین کاراکترها
- 📸 گرفتن اسکرین‌شات و ذخیره در مسیر دلخواه
- 🖱 کلیک روی مختصات مشخص یا وسط صفحه (پیش‌فرض)
- 🛑 قابلیت غیرفعال‌سازی هرکدام از مراحل (تایپ، اسکرین‌شات، کلیک) با فلگ‌ها
- 🚨 فیل‌سیف فعال PyAutoGUI: با بردن ماوس به گوشه‌ی صفحه، اجرای اسکریپت فوراً متوقف می‌شود

## ⚙️ نصب و راه‌اندازی

```bash
pip install -r requirements.txt
```

### اجرا با تنظیمات پیش‌فرض

```bash
python automation_demo.py
```

### مثال‌های بیشتر

```bash
# پیام سفارشی و ۳ ثانیه تاخیر، بدون کلیک
python automation_demo.py --message "سلام دنیا!" --delay 3 --no-click

# کلیک روی مختصات مشخص و ذخیره اسکرین‌شات با نام دلخواه
python automation_demo.py --click-x 500 --click-y 300 --screenshot out.png

# فقط تایپ، بدون اسکرین‌شات و بدون کلیک
python automation_demo.py --no-screenshot --no-click
```

برای دیدن همه گزینه‌ها:

```bash
python automation_demo.py --help
```

## 📄 لایسنس

MIT — آزادانه استفاده و تغییر بده.
