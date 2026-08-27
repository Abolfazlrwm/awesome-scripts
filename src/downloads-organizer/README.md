# 🗂 مرتب‌کننده پوشه Downloads | Downloads Folder Organizer

اسکریپت پاورشل برای مرتب‌سازی خودکار فایل‌های شلوغ پوشه Downloads (یا هر پوشه دلخواه دیگر) بر اساس نوع فایل، به همراه گزارش کامل از عملیات انجام‌شده.

![PowerShell](https://img.shields.io/badge/PowerShell-5.1%2B-5391FE.svg)
![Platform](https://img.shields.io/badge/platform-Windows-0078D6.svg)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ امکانات

- 📁 دسته‌بندی خودکار به ۱۲ گروه: Documents, Archives, Images, Videos, Audio, Executables, Development, Design, Fonts, Temporary, Installers, Other
- 🚫 بدون بازنویسی فایل‌ها: در صورت هم‌نام بودن، به‌صورت خودکار شماره‌گذاری می‌شوند
- 📝 لاگ کامل با timestamp برای هر عملیات
- 📊 گزارش خلاصه در پایان (`Organization_Summary.txt`) شامل تعداد فایل هر دسته
- 📈 نوار پیشرفت زنده حین اجرا
- 📂 قابلیت باز کردن خودکار پوشه سازمان‌دهی‌شده در پایان کار
- ⚙️ قابل تنظیم برای هر پوشه‌ای، نه فقط Downloads (با پارامتر `-SourcePath`)

## ⚙️ نصب و راه‌اندازی

نیازی به نصب چیزی نیست، فقط PowerShell (نسخه ۵.۱ به بالا، از قبل روی ویندوز نصب است).

```powershell
# مرتب‌سازی پوشه Downloads پیش‌فرض کاربر فعلی
.\Organize.ps1

# یا اجرای مستقیم بدون تغییر Execution Policy سیستم
powershell -ExecutionPolicy Bypass -File .\Organize.ps1

# مرتب‌سازی یک پوشه دلخواه دیگر و لاگ سفارشی
.\Organize.ps1 -SourcePath "D:\MessyFolder" -LogPath "D:\organize.log"
```

فایل‌ها در یک زیرپوشه‌ی جدید به نام `Organized_YYYYMMDD_HHmmss` داخل همان پوشه مبدأ قرار می‌گیرند تا از داده‌های اصلی جدا بمانند.

## 📄 لایسنس

MIT — آزادانه استفاده و تغییر بده.
