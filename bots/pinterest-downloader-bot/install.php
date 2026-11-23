<?php
/* ==============================
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

include 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);

    echo "<h3 style='color: green'>اتصال با موفقیت برقرار شد!</h3>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            step TEXT,
            username VARCHAR(100),
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            language VARCHAR(10) DEFAULT 'fa', -- ستون جدید برای زبان
            last_request_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `groups` (
            id BIGINT PRIMARY KEY,
            title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            type VARCHAR(50),
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `send` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            target VARCHAR(200) DEFAULT NULL,
            type_send VARCHAR(200) DEFAULT NULL,
            chat_id BIGINT DEFAULT '0',
            message TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            media VARCHAR(200) DEFAULT NULL,
            send_count INT DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `channels` (
            link VARCHAR(150) NOT NULL PRIMARY KEY
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admins` (
            id BIGINT PRIMARY KEY
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        INSERT IGNORE INTO `admins` (`id`) VALUES ('$dev')
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `bot_settings` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_name VARCHAR(50) NOT NULL,
            setting_value VARCHAR(255) NOT NULL,
            UNIQUE KEY setting_name (setting_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $pdo->exec("
        INSERT INTO `bot_settings` (`setting_name`, `setting_value`) 
        VALUES ('request_cooldown', '15')
        ON DUPLICATE KEY UPDATE `setting_value` = '15'
    ");

    echo "<h3 style='color: blue'>جداول با موفقیت ساخته شدند!</h3>";

} catch (PDOException $e) {
    echo "<div style='color: red; font-family: monospace;'>
            <h3>خطا در اتصال یا ساخت جداول:</h3>
            <pre>{$e->getMessage()}</pre>
          </div>";
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