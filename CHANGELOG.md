# 📋 Changelog — تاریخچه تغییرات

All notable releases of this repository. Every entry matches a published [GitHub Release](https://github.com/Abolfazlrwm/awesome-scripts/releases) with a downloadable ZIP archive.
تمام نسخه‌های منتشرشده‌ی این مخزن — هر مورد با یک [GitHub Release](https://github.com/Abolfazlrwm/awesome-scripts/releases) دارای فایل ZIP مطابق است.

> Format: `version — date — projects (categories) · highlights`
> قالب: `نسخه — تاریخ — پروژه‌ها (دسته‌ها) · نکات اصلی`

---

## [v1.22.0] — 2026-11-20 — 📲 PWA + shareable views — install it, share any filter, go offline

- **Progressive Web App** — `sw.js` precaches the app shell (pages, catalog, stats, icons), serves stale-while-revalidate for same-origin GETs and network-first navigations: the gallery and the matrix now **work offline** and are **installable** on desktop and mobile. `manifest.json` refreshed (109 projects, 192px icon, app shortcuts: **Project Matrix** and **Random project**).
- **Shareable URL state** — search, category, language, demo/favorite filters and UI language mirror into the URL (`?q=tetris&demo=1&fa=1`); any view can be bookmarked or shared and boots exactly as saved.
- **🎲 Random project** — header button (plus `#random` deep link and PWA shortcut) opens a random **live-demo** project in the modal.
- **🚀 Release chip + ⬇️ ZIP** — header links to the latest release and a stable one-click download (`releases/latest/download/awesome-scripts.zip`); the release workflow now publishes the alias ZIP alongside the versioned one.

سایت حالا PWA است (نصب + کارکرد آفلاین)، هر فیلتری URL اشتراکی دارد، دکمه‌ی 🎲 پروژه‌ی تصادفی و ⬇️ دانلود ZIP ثابت اضافه شد؛ workflow ریلیز ZIP نام‌دار و alias را هم‌زمان منتشر می‌کند.

## [v1.21.0] — 2026-11-20 — ⚖️ Project Matrix + walkthrough video (159,716 lines of code, measured)

- **⚖️ Project Matrix (`compare.html`)** — a new stats-driven companion page to the gallery: every project with real file counts, lines of code (159,716 across the repo) and size; advanced filters (category, language, has-demo, has-preview, LOC range), sortable columns and **side-by-side comparison of up to 4 projects** with preview thumbnails. Bilingual (EN/FA) with dark/light themes.
- **`tools/generate_stats.py`** — collects per-project file/LOC/size metrics into `data/stats.json` + `stats.js` (marked `linguist-generated`).
- **🎬 Walkthrough video** — a scripted 40-second screencast of the gallery (search → live preview → matrix → compare) embedded in the README as a 4.5 MB GIF; the full-quality MP4 ships with the release assets.
- Gallery header now links to the matrix; sitemap carries 41 URLs.

صفحه‌ی «ماتریس پروژه‌ها» با آمار واقعی هر پروژه (فایل/خط کد/حجم)، فیلتر پیشرفته و مقایسه‌ی ۴تایی اضافه شد؛ ویدیوی چهل‌ثانیه‌ای معرفی سایت در README است و MP4 با کیفیت کامل در Assets ریلیز.

## [v1.20.0] — 2026-11-19 — 💼 Professional polish — product + code, one click apart

- **Detail modal** — every project card now opens a modal with the live preview embed and five actions: **▶ Live demo**, **💻 Code**, **📖 README**, **📋 Copy git clone** (clipboard with feedback) and **🔗 Share** — an employer can run the product and read the source in the same minute.
- **Featured products grid** — six flagship web tools with real clickable screenshots at the top of the README: thumbnail ▶ runs the live product, 💻 opens the code.
- **Engineering practices table** — what each CI gate actually enforces (validate 109/109, catalog drift, link 200s, CodeQL, releases, Dependabot), in plain language.
- **Repo hygiene** — `.gitattributes` marks generated files (`catalog.json/js`, sitemap) as `linguist-generated` and normalizes line endings; first-interaction **Greetings** workflow welcomes new issues/PRs.

مودال هر پروژه حالا پروداکت زنده + کد + کپی git clone را یک‌جا می‌دهد؛ README شش محصول شاخص با اسکرین‌شات واقعی و جدول gateهای مهندسی دارد؛ `.gitattributes` و اکشن خوش‌آمدگویی اضافه شد.

## [v1.19.0] — 2026-11-19 — 🗂 Repository reorganized + gallery front & center (63 bots · 40 web · 6 desktop)

- **Structure** — the mixed `src/` bucket is gone: projects now live in **`bots/` (63)**, **`web/` (40)** and **`desktop/` (6)**. The whole toolchain followed: catalog generator, validator, scaffolder, release packaging, sitemap and every README row (218 folder links + 76 demo links rewritten).
- **Sitemap rebuilt** — new `tools/generate_sitemap.py` emits all **110 URLs** (root + every project page) from the catalog, plus `.nojekyll` for faster Pages builds.
- **Gallery visibility** — the interactive site now sits at the top of the README (hero + screenshot), is set as the **repository homepage link**, and the repo description points to it. Demo coverage: 38/40 web tools (the 2 without — a PHP web app and an API library — genuinely have no browser UI).
- Filter chips (`▶ Live demo only`, `⭐ Favorites`) are now bilingual and follow the language toggle.

`src/` قاطی حذف شد و پروژه‌ها به سه پوشه‌ی `bots/`، `web/` و `desktop/` رفتند؛ تمام ابزارها و لینک‌ها به‌روز شدند؛ سایت تعاملی حالا اول README، لینک homepage مخزن و همراه اسکرین‌شات است.

## [v1.18.0] — 2026-11-19 — 🔬 Pro tools + landing v7 favorites & live preview (63 bots · 40 web · 6 desktop)

- **3 new pro-grade tools** (`106 → 109`): `text-diff-web` (LCS line diff with word-level highlights and a similarity score), `image-compressor-web` (quality/resize with before-after compare — 100% client-side, verified 1.97 MB → 185 KB) and `cron-explainer-web` (plain-Persian explanation + the next five fire times; its weekday parser bug was caught and fixed in QA — `*` no longer collapses to Sunday-only, verified against Python's datetime).
- **Landing v7** — ⭐ **favorites**: star any of the 106 cards, persisted in localStorage, with a `⭐ علاقه‌مندی‌ها` filter chip; and **▶ live preview inside the modal** — demo projects embed their real running page in an iframe (tetris actually plays inside the modal), with image-restore and teardown on close.
- **Governance** — `SECURITY.md` policy (72 h response, private reporting) plus a weekly **CodeQL** security scan for JS & Python with a new badge.

سه ابزار حرفه‌ای (مقایسه‌گر متن، فشرده‌ساز تصویر، توضیح‌گر cron) اضافه شدند؛ لندینگ حالا ستاره‌ی علاقه‌مندی با ذخیره‌سازی و پیش‌نمایش زنده‌ی واقعی داخل مودال دارد؛ سیاست امنیتی و اسکن هفتگی CodeQL هم فعال شد.

## [v1.17.0] — 2026-11-18 — 🧰 Everyday tools + landing v6 (63 bots · 37 web · 6 desktop)

- **3 new everyday tools** (`103 → 106`): `markdown-editor-web` (live split preview with a safe escape-first parser, toolbar, autosave and .md export), `json-formatter-web` (pretty/minify, line-and-column precise errors, collapsible tree viewer) and `unit-converter-web` (length, mass, temperature, data and speed with exact SI factors) — each function-tested in a real browser and shipped with preview + live demo.
- **Landing v6** — curated **collection shelves** (🎮 Games / 🤖 AI / 🛒 Shop / 🎨 Creative / 🧰 Utils) with horizontal scrolling; every project card now opens via **shareable deep link** (`#p=slug`) with a one-click **Share** button in the modal; new **▶ demo-only** filter chip.
- **PWA + branding** — `manifest.json` with standalone display, SVG favicon and a 512 px app icon; the site is now installable from the browser menu.
- **SEO + social** — `sitemap.xml` and `robots.txt` generated from the catalog, plus a custom 1200×630 **OG banner** (`assets/og-banner.png`) wired into Twitter/OG meta for rich link cards.
- **CI** — new weekly **Link Check** workflow (`tools/check_links.py`): probes all demo URLs concurrently and posts a health table to the job summary.

سه ابزار کاربردی روزمره اضافه شدند؛ لندینگ قفسه‌ی مجموعه‌های منتخب، لینک اشتراک‌گذاری (#p=slug) و فیلتر «فقط دمودار» گرفت؛ سایت حالا نصب‌شدنی است (PWA)، برای موتورهای جستجو نقشه‌ی سایت و بنر شبکه‌های اجتماعی دارد و هر هفته CI همه‌ی لینک‌های دمو را بررس می‌کند.

## [v1.16.0] — 2026-11-18 — 🕹 Arcade pack (63 bots · 34 web · 6 desktop)

- **3 new arcade classics** (`100 → 103`): `2048-web` (real sliding-tile animation with line-compression merges, undo and swipe), `minesweeper-web` (three difficulties, always-safe first click, long-press flags, per-difficulty best times) and `breakout-web` (angle-based paddle physics, three level layouts, 2-HP bricks, particle bursts) — each QA'd interactively and shipped with preview + live demo.
- **Landing page v5** — every card now has a **cover thumbnail** (88 real previews, gradient+emoji fallback for the rest) and clicking a card opens a **glass detail modal** with cover, chips, bilingual description and demo/folder/README actions.
- **Catalog v2** — `generate_catalog.py` now emits a `preview` field per project (file-aware, deterministic).
- **Repo professionalization** — deep audit (100/103 READMEs signed, zero ad residue, zero JS syntax errors, all 29 live demo links HTTP 200), added `.github/FUNDING.yml` and a feature-request issue template, and refreshed the GitHub repo description + topics.

سه کلاسیک آرکید جدید (۲۰۴۸، مین‌یاب و آجرشکن) با QA تعاملی اضافه شدند؛ همه‌ی کارت‌های لندینگ کاور تصویری گرفتند و با کلیک، مودال جزئیات شیشه‌ای باز می‌شود؛ کاتالوگ فیلد preview گرفت و فایل‌های حرفه‌ای مخزن (FUNDING، قالب ایده، توضیحات و topics) به‌روز شدند.

## [v1.15.0] — 2026-11-17 — 💯 100 projects (63 bots · 31 web · 6 desktop)

- **The Century milestone** — the repo now ships exactly **100 complete, ready-to-run projects**.
- **5 new showpiece web tools** (`95 → 100`): `tetris-web` (full Tetris with 7-bag, hold, ghost piece and touch controls), `game-of-life-web` (Conway simulation with famous pattern stamps, live painting and fading trails), `pixel-art-studio` (pixel editor with flood fill, mirror symmetry and crisp PNG export), `matrix-rain-web` (the digital rain with a message-reveal mode and Persian glyphs) and `noise-art-web` (Perlin-noise generative art with flow-field particles) — each with preview + live demo.
- **Landing page v4** — `Ctrl+K` bilingual command palette with keyboard navigation, 3D pointer tilt on cards, rotating hero phrases, 🎲 confetti burst on the lucky button and a back-to-top button.

صد پروژه‌ی کامل! پنج ابزار وب نمایشی جدید (تتریس، بازی زندگی، استودیو پیکسل‌آرت، بارش ماتریکس و هنر نویز) با پیش‌نمایش و دموی زنده اضافه شدند و لندینگ‌پیج با پالت فرمان `Ctrl+K` دو‌زبانه، تیلت سه‌بعدی کارت‌ها، عبارت‌های چرخان قهرمان و کانفتی دکمه‌ی شانسی خفن‌تر شد.

## [v1.14.0] — 2026-11-17 — 95 projects (63 bots · 26 web · 6 desktop)

- **3 new showpiece web tools** (`92 → 95`): `snake-game-web` (neon snake with touch-swipe and keyboard controls, accelerating loop, persistent personal best), `fractal-explorer` (Mandelbrot & Julia deep-zoom explorer with smooth escape-time coloring, 5 palettes and PNG export) and `synthwave-keys` (two-octave WebAudio synthesizer with a live oscilloscope and an event recorder) — each with a screenshot preview and live demo.
- **Landing page v3** — animated aurora background, count-up hero counter, pointer-tracking card spotlight, a 🎲 random-project button and a scroll progress bar.

سه ابزار وب نمایشی جدید (مار نئونی، کاوشگر فرکتال و کیبورد سینتی‌سایزر) با پیش‌نمایش و دموی زنده اضافه شد و شمارنده‌ها از ۹۲ به ۹۵ رسید؛ لندینگ‌پیج با پس‌زمینه‌ی شفق متحرک، شمارنده‌ی عددی، نورافکن کارت‌ها و دکمه‌ی «پروژه‌ی شانسی» خفن‌تر شد.

## [v1.13.0] — 2026-11-17 — 92 projects (63 bots · 23 web · 6 desktop)

- **3 new interactive web tools** (`89 → 92`): `password-forge` (crypto-API password/passphrase generator with entropy scoring and crack-time estimates), `pomodoro-focus` (Web Worker timer with SVG dial and daily stats) and `regex-playground` (live tester with match highlighting and capture-group table) — each with a screenshot preview and live demo.
- **Landing page v2** — restyled with the repository design system (Vazirmatn + Outfit webfonts, glassmorphism, gradient orbs) and refreshed all static counters (74 → 92).
- **Security hardening** — TLS certificate verification in `iran-market-rate-bot`, expression-size guard in `calculator`, opt-in CORS in `caller-lookup-app`, 30s HTTP timeouts in `post-manager-bot` and `url-shortener-php-bot`.
- **Changelog backfill** — releases v1.9.0 … v1.12.0 documented below.

سه ابزار وب جدید (تولید پسورد، تایمر پومودورو، زمین بازی Regex) با پیش‌نمایش و دموی زنده اضافه شد و شمارنده‌ها از ۸۹ به ۹۲ رسید؛ لندینگ‌پیج با سیستم طراحی جدید بازطراحی شد، چند اصلاح امنیتی اعمال و ورودی‌های نسخه‌های قبلی تکمیل شد.

## [v1.12.0] — 2026-11-16 — 89 projects (63 bots · 20 web · 6 desktop)

- **UI/UX overhaul of the 4 flagship web tools** — Vazirmatn + Outfit typography, glassmorphism design system with per-tool accent palettes, dark & light themes.
- `typing-speed-race`: keypress audio, mistake heatmap, PB confetti, animated counters.
- `sudoku-game-web`: undo, auto-notes, digit counters, per-difficulty records, win confetti.
- `ascii-art-generator-web`: color FX modes, banner PNG export, procedural samples, clipboard paste.
- `canvas-paint-studio`: rainbow & glow brushes, 4-way symmetry, redo, keyboard shortcuts.

بازطراحی کامل ظاهر چهار ابزار وب شاخص با فونت وزیرمتن/Outfit و پنل‌های شیشه‌ای، همراه با قابلیت‌های جدید برای هر ابزار.

## [v1.11.0] — 2026-11-16 — 89 projects (63 bots · 20 web · 6 desktop)

- **8 new projects** (`81 → 89`): `typing-speed-race`, `sudoku-game-web`, `ascii-art-generator-web`, `canvas-paint-studio`, `dupe-file-finder`, `auction-house-bot`, `fortune-teller-bot` and `prayer-times-bot` — including four interactive web tools with screenshot previews.
- Catalog tooling hardened: stray artifacts ignored.

۸ پروژه‌ی جدید شامل چهار ابزار وب تعاملی (تایپ‌تست، سودوکو، استودیو ASCII و بوم نقاشی)، ابزار یافتن فایل‌های تکراری و سه ربات ثبت شد.

## [v1.10.0] — 2026-11-16 — 81 projects (60 bots · 16 web · 5 desktop)

- **7 new projects** (`74 → 81`): `drum-machine-web` (16-step WebAudio sequencer), `color-palette-studio` (harmony palettes with WCAG lab), `wordle-fa-bot` (Persian word game), `flashcard-srs-bot` (SM-2 spaced repetition), `expense-splitter-bot`, `dice-duel-bot` and `bulk-renamer-cli`.

۷ پروژه‌ی جدید شامل درام‌ماشین وب، استودیو پالت رنگ، وردل فارسی، بات‌های مطالعه/خرج گروهی/نردبان تاس و ابزار تغییر نام گروهی اضافه شد.

## [v1.9.0] — 2026-11-16 — 74 projects (56 bots · 14 web · 4 desktop)

- **Docs & tooling release** — added CHANGELOG, expanded the contributing guide, fixed language-count drift, added the project scaffolder (`tools/new_project.py`) and enforced README/catalog consistency in the validator, plus OG/Twitter meta tags for the site.

نسخه‌ی مستندات و ابزارها: چنج‌لاگ، راهنمای مشارکت توسعه‌یافته، اسکریپت ساخت پروژه‌ی جدید و کنترل سازگاری README/کاتالوگ در اعتبارسنج.

## [v1.8.0] — 2026-11-14 — 74 projects (56 bots · 14 web · 4 desktop)

- **Audit & branding cleanup** — full repo review: syntax-checked 162 Python + all JS files, fixed the only gap (`caller-lookup-app` missing `requirements.txt`, documented as stdlib-only).
- بازبینی کامل مخزن: سینتکس ۱۶۲ فایل پایتون و همه فایل‌های JS بررسی شد؛ تنها کمبود (`requirements.txt` در `caller-lookup-app`) رفع شد.

## [v1.7.0] — 2026-11-12 — 74 projects (56 bots · 14 web · 4 desktop)

- **4 new projects** (`70 → 74`): `ai-chat-webapp-php`, `likes-challenges-php-bot`, `pyrogram-selfbot-manager`, `giftix-premium-shop-bot` and more.
- ۴ پروژه‌ی جدید ثبت شد و شمارنده‌ها از ۷۰ به ۷۴ رسید.

## [v1.6.0] — 2026-11-12 — 70 projects (53 bots · 13 web · 4 desktop)

- **Repo-wide branding pass** — every project carries `Telegram: @Abolfazl_rwm` contact lines in code headers and READMEs; third-party credits replaced.
- برندسازی سراسری: همه پروژه‌ها خط تماس `Telegram: @Abolfazl_rwm` را در هدر کد و README دارند.

## [v1.5.0] — 2026-11-12 — 70 projects (53 bots · 13 web · 4 desktop)

- **14 new projects** (`56 → 70`): including `ton-nft-marketplace` (TON Connect NFT UI), `telegram-selfbot-panel`, economy & shop bots.
- ۱۴ پروژه‌ی جدید از جمله مارکت‌پلیس NFT روی TON و پنل سلف‌بات اضافه شد.

## [v1.4.0] — 2026-11-11 — 56 projects (43 bots · 9 web · 4 desktop)

- **7 new projects** (`49 → 56`): including `channel-likes-bot` (live like button for channel posts).
- ۷ پروژه‌ی جدید از جمله ربات لایک زنده‌ی پست کانال.

## [v1.3.0] — 2026-11-11 — 49 projects

- **Preview cards** for bot pages and window previews for desktop tools in README tables.
- کارت پیش‌نمایش برای ربات‌ها و پیش‌نمایش پنجره برای ابزارهای دسکتاپ در جداول README.

## [v1.2.0] — 2026-11-11 — 49 projects

- **Persian-aware search** in the interactive catalog (folds ی/ک, ZWNJ, diacritics, Persian digits); dynamic page title.
- جستجوی فارسی‌آگاه در کاتالوگ (ادغام ی/ک، نیم‌فاصله، اعراب و ارقام فارسی) و عنوان داینامیک صفحه.

## [v1.1.0] — 2026-11-11 — 46 projects (35 bots · 8 web · 3 desktop)

- **Live catalog promoted** in README; repo tooling hardened (`--check` tolerates timestamp drift); 11 new projects.
- کاتالوگ زنده در README جای گرفت، ابزارهای مخزن مقاوم‌تر شدند و ۱۱ پروژه‌ی جدید اضافه شد.

## [v1.0.0] — 2026-11-09

- **First release** — the collection goes public with the interactive catalog site and CI pipeline.
- اولین انتشار رسمی مجموعه به همراه کاتالوگ تعاملی و خط CI.

---

## 🔮 Versioning — نسخه‌بندی

[SemVer](https://semver.org/)-style: **MAJOR** reserved for breaking catalog format changes, **MINOR** for new projects or features, **PATCH** for fixes. Tags trigger the Release workflow which publishes a full ZIP automatically.
به سبک SemVer: تغییر ناسازگار در قالب کاتالوگ → Major، پروژه یا قابلیت جدید → Minor، رفع اشکال → Patch. هر تگ به‌طور خودکار ریلیز با ZIP کامل می‌سازد.
