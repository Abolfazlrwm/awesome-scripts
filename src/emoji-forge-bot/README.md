# 🎨 Emoji Forge AI

A Telegram bot for **custom emoji pack personalization** (TGS/WEBM): rebrand any pack you legally own — change names, logos, colors — preview it, rebuild it and export a **ZIP + official release guide** so you can publish the new pack as **your own sticker set** (Bot API only allows sets owned by the bot itself; the bot never steals someone else's set — see the official-limits section in the Persian docs).

This is a **full engineering project** (not a single script): pipeline package, animation engine, TGS validation, importer guard, admin panel, processing queue, test-suite, systemd deployment and an installer.

## ✨ Highlights

- 🧠 **Brain providers** — built-in rule engine (free, offline), or plug **any OpenAI-compatible endpoint** (OpenAI / OpenRouter / local server) via `AI_API_KEY`; auto-falls-back to rules on errors
- 🧪 **Reproducible pipeline** — cleaning, visual-identity injection, preview, full rebuild, ZIP export; versioned modules (`v4`…`v10`)
- 📦 **TGS support** — standard-JSON TGS reading, validation, size shrinking, no lottie server needed
- 🛡 **Importer guard & access checks** — refuses packs the bot can't legally access
- 🧰 **Ops tooling** — `install.sh` (systemd service `tz-emojis`), start/stop scripts, self-test on boot, QA/diagnostic scripts in `scripts/`
- 🧬 **Trace/restore safety** — data lives in `data/`, runtime artifacts isolated

## 🚀 Quick start

```bash
cd emoji-forge-bot
python3 -m venv .venv && . .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env      # EMOJI_FORGE_TOKEN + EMOJI_FORGE_ADMIN_IDS
python run.py
```

Or on a server:

```bash
chmod +x install.sh && sudo ./install.sh   # creates systemd service "tz-emojis"
```

## 📚 Documentation (Persian, extensive)

| File | Content |
| --- | --- |
| [README-FA.md](README-FA.md) | Full product doc — architecture decisions, official limits, pipeline stages |
| [README-INSTALL-FA.md](README-INSTALL-FA.md) | Step-by-step install (systemd / manual / cPanel) |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Module map & data flow |
| [BRAIN.md](BRAIN.md) | AI "brain" providers & muse-bridge queue |
| [CHANGELOG-V10.md](CHANGELOG-V10.md) | v10 changes |
| [TEST-REPORT.md](TEST-REPORT.md) | Test-suite results |

## 🗂 Layout

```text
src/emoji_forge/   # bot + pipeline + engines (bot.py, pipeline.py, cleaning.py, ...)
tests/             # pytest suite (forge, singles, premium UI, v10)
scripts/           # QA, audits, fixes (25+ tools)
assets/            # shape registry
systemd/           # service unit
data/              # runtime data (git-kept, empty)
```

<details>
<summary><b>🇮🇷 نسخه فارسی</b></summary>

# 🎨 اموجی فورج AI

ربات تلگرامی **شخصی‌سازی پک‌های ایموجی سفارشی** (TGS/WEBM): پکی که مالکیتش رو داری ری‌برند کن — اسم، لوگو، رنگ — پیش‌نمایش بگیر، دوباره بساز و خروجی **ZIP + راهنمای انتشار رسمی** بگیر تا ست جدید رو به‌عنوان **ست خودت** منتشر کنی (Bot API فقط اجازه ساخت ستِ متعلق به خود ربات رو می‌ده؛ ربات هیچ‌وقت پک کس دیگه رو نمی‌دزده — بخش «محدودیت‌های رسمی» در مستندات فارسی).

این یه **پروژه مهندسی کامل** هست: پکیج pipeline، موتور انیمیشن، اعتبارسنجی TGS، گارد ایمپورت، پنل ادمین، صف پردازش، تست‌سوئیت، دیپلوی systemd و نصب‌کننده.

## 🚀 اجرا

```bash
cd emoji-forge-bot
python3 -m venv .venv && . .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env      # EMOJI_FORGE_TOKEN + EMOJI_FORGE_ADMIN_IDS
python run.py
```

یا روی سرور: `sudo ./install.sh` (سرویس systemd به اسم `tz-emojis` می‌سازد)

## 📚 مستندات فارسی کامل

همه‌ی مستندات اصلی فارسی و در همین پوشه هستند: `README-FA.md` (مستند کامل)، `README-INSTALL-FA.md` (نصب گام‌به‌گام)، `ARCHITECTURE.md`، `BRAIN.md`، `CHANGELOG-V10.md` و `TEST-REPORT.md`.

## ⚙️ متغیرهای محیطی مهم

- `EMOJI_FORGE_TOKEN` — توکن ربات از @BotFather
- `EMOJI_FORGE_ADMIN_IDS` — آیدی ادمین‌ها (کاما جدا)
- `EMOJI_FORGE_BRAIN_PROVIDER` — `rule` (پیش‌فرض، رایگان) یا اتصال سرویس خارجی
- `AI_API_KEY` / `AI_BASE_URL` / `AI_MODEL` — اختیاری؛ هر اندپوینت سازگار با OpenAI

</details>

<div align="center">

**Maintained by [@Abolfazl_rwm](https://t.me/Abolfazl_rwm)** · MIT License

</div>
