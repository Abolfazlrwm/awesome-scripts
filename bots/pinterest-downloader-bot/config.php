<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
/*
* Title: سورس ربات تلگرامی دانلود از پینترسته
* Developer: Mr Kigga
* Telegram: t.me/Mr_Kigga
* Channel: t.me/pulseXcode
*
* توجه: این سورس حق نشر دارد و بدون ذکر تگ سازنده غیرقانونی است.
*/

// ============ CONFIGURATION SECTION ============
// Replace these values with your own credentials

// Telegram Bot API Token - Get this from @BotFather
// WARNING: Keep this token secret! Anyone with this can control your bot
define('API_KEY', 'YOUR_TELEGRAM_BOT_TOKEN_HERE'); // Replace with your actual bot token

// Developer Telegram User ID (numeric)
// This is used for admin notifications and access control
$dev = YOUR_TELEGRAM_USER_ID_HERE; // Replace with your numeric Telegram ID

// Database Configuration - Replace with your actual database credentials
// SECURITY WARNING: Never use default or weak credentials
$host = 'localhost'; // Database host (usually 'localhost')
$db   = 'YOUR_DATABASE_NAME';      // Database name
$user = 'YOUR_DATABASE_USERNAME';  // Database username
$pass = 'YOUR_STRONG_DB_PASSWORD'; // Use a strong password here

// ============ END OF CONFIGURATION ============

// Database connection setup
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log error securely instead of displaying to users
    error_log("Database connection failed: " . $e->getMessage());
    exit('Database connection error. Please contact administrator.');
}

/*
* پایان سورس ربات تلگرامی دانلود از پینترسته
* توسعه‌دهنده: Mr Kigga
* تلگرام: t.me/Mr_Kigga
* چنل: t.me/pulseXcode
*
* هشدار: هرگونه کپی‌برداری یا انتشار بدون ذکر منبع پیگرد قانونی دارد.
* این سورس متعلق به سازنده اصلی است و هیچ‌کس حق ندارد آن را به نام خود منتشر کند.
*/
?>