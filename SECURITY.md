# Security Policy — سیاست امنیتی

## 🇬🇧 English

### Supported versions

This repository is a rolling collection — only the latest commit on `main` is supported.
Always pull before deploying anything.

| Version | Supported |
| --- | --- |
| `main` branch | ✅ |
| Older tags / commits | ❌ |

### Reporting a vulnerability

Please use **GitHub's private vulnerability reporting** (Security tab → *Report a vulnerability*)
so details stay confidential until a fix ships.

You will get:

- an initial response within **72 hours**
- a fix or a mitigation plan within **14 days** for confirmed issues

Please include: the affected project folder (e.g. `src/php-bot-maker/`), the file and line,
steps to reproduce, and your assessment of the impact.

### What belongs here — and what does not

- ✅ Injection flaws, broken auth, leaked endpoints, XSS/SSRF, path traversal, unsafe deserialization…
- ✅ Anything that would let an attacker abuse *the operator's* infrastructure.
- ❌ "This bot can be used to spam people" — the **spam/attack modules** are published for
  security research and testing against infrastructure **you own**, and this is stated in their
  documentation. Misuse reports of that kind are not security vulnerabilities.

### Secrets

Never hard-code bot tokens, API keys or passwords. The CI pipeline
(`tools/validate_projects.py`) scans every push for leaked credentials — if you find a leaked
secret in the history, report it immediately so it can be revoked.

---

## 🇮🇷 فارسی

### نسخه‌های پشتیبانی‌شده

این مخزن یک مجموعه‌ی در حال توسعه است و فقط آخرین وضعیت شاخه‌ی `main` پشتیبانی می‌شود. قبل از اجرای هر پروژه، مخزن را به‌روز بگیرید.

### گزارش آسیب‌پذیری

از قابلیت **گزارش خصوصی آسیب‌پذیری** در تب Security گیت‌هاب استفاده کنید تا جزئیات تا زمان رفع مشکل محرمانه بماند.

- اولین پاسخ حداکثر تا **۷۲ ساعت**
- راه‌حل یا برنامه‌ی رفع برای مشکلات تأییدشده حداکثر تا **۱۴ روز**

لطفاً این موارد را ذکر کنید: پوشه‌ی پروژه (مثلاً `src/php-bot-maker/`)، فایل و شماره‌ی خط، مراحل بازتولید مشکل و ارزیابی خودتان از میزان اثرگذاری.

### چه چیزی گزارش بخواهد دارد — و چه چیزی نه

- ✅ آسیب‌پذیری‌های تزریق، احراز هویت معیوب، XSS/SSRF، عبور از مسیر و…
- ✅ هر چیزی که به مهاجم اجازه‌ی سوءاستفاده از زیرساختِ **مالک ربات** را بدهد.
- ❌ جمله‌هایی از قبیل «این ربات می‌تواند برای مزاحمت استفاده شود» — ماژول‌های **spam/attack** صرفاً برای پژوهش و تست روی زیرساختِ خودِ شما منتشر شده‌اند و این در مستندات‌شان ذکر شده است. این نوع گزارش‌ها آسیب‌پذیری امنیتی نیستند.

### اطلاعات حساس

هرگز توکن ربات، کلید API یا رمز عبور را در کد قرار ندهید. پایپ‌لاین CI با `tools/validate_projects.py` همه‌ی پش‌ها را از نظر افشای اطلاعات حساس اسکن می‌کند.
