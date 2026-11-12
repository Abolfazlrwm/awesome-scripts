<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — تنظیمات اصلی
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

/**
 * تنظیمات اصلی ربات چالش • لایک • نظرسنجی
 * بعد از آپلود، مقدارهای زیر را با اطلاعات خودتان جایگزین کنید.
 */

declare(strict_types=1);

const BOT_TOKEN = 'PUT_YOUR_BOT_TOKEN_HERE';

// اگر خالی بگذارید، ربات خودش با getMe دریافت می‌کند؛ اما برای ساخت لینک بهتر است وارد شود.
const BOT_USERNAME = 'PUT_YOUR_BOT_USERNAME_HERE'; // بدون @ مثال: MyLikeBot

// آدرس کامل فایل bot.php برای ست وبهوک
const WEBHOOK_URL = 'https://example.com/likebot/bot.php';

// ادمین‌های اصلی؛ آیدی عددی تلگرام را وارد کنید.
const MAIN_ADMINS = [123456789];

// دیتابیس MySQL
const DB_HOST = 'localhost';
const DB_NAME = 'like_challenge_bot';
const DB_USER = 'db_user';
const DB_PASS = 'db_pass';
const DB_CHARSET = 'utf8mb4';

// تنظیمات عمومی
const DEFAULT_LIKE_EMOJI = '❤️';
const DEFAULT_TIMEZONE = 'Asia/Tehran';

// برای خطایابی موقت true کنید؛ روی هاست واقعی بهتر است false باشد.
const DEBUG_MODE = false;
