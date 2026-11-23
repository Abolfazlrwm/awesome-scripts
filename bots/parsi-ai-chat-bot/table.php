<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

//=============================
// Developed by: @ShahghaIab  
//=============================


try {
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    $db->set_charset("utf8mb4");
    mysqli_set_charset($db, "utf8mb4");
    $db->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $db->query("SET CHARACTER SET utf8mb4");
    $db->query("SET SESSION collation_connection = utf8mb4_unicode_ci");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("خطا در اتصال به پایگاه داده");
}

$api = "https://api.telegram.org/bot{$config['token']}/";

function init_db($db) {
    global $config;
    
    $queries = [
        "ALTER DATABASE `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS users (
            id BIGINT PRIMARY KEY,
            username VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            first_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            joined_date DATETIME,
            last_activity DATETIME,
            ai_chat_state TINYINT DEFAULT 0,
            ai_usage_count INT DEFAULT 0,
            is_blocked TINYINT DEFAULT 0,
            user_role ENUM('user', 'vip') DEFAULT 'user'
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS groups (
            id BIGINT PRIMARY KEY,
            title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            joined_date DATETIME,
            last_activity DATETIME,
            member_count INT DEFAULT 0,
            is_active TINYINT DEFAULT 1,
            settings TEXT
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY DEFAULT 1,
            bot_active TINYINT DEFAULT 1,
            broadcast_mode INT DEFAULT 0,
            maintenance_mode TINYINT DEFAULT 0,
            config TEXT
        ) ENGINE=InnoDB",
        
        "CREATE TABLE IF NOT EXISTS chat_history (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT,
            message TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            response TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        
        "INSERT IGNORE INTO settings (id, bot_active, broadcast_mode, maintenance_mode) VALUES (1, 1, 0, 0)"
    ];
    
    foreach ($queries as $query) {
        if (!$db->query($query)) {
            error_log("Database initialization error: " . $db->error);
        }
    }
}

?>