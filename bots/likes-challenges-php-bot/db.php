<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — لایه دیتابیس MySQL
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function dbIdentifier(string $name): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        throw new InvalidArgumentException('Invalid database identifier: ' . $name);
    }
    return '`' . $name . '`';
}

function dbHasTable(string $table): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function dbHasColumn(string $table, string $column): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function dbAddColumnIfMissing(string $table, string $column, string $definition): void
{
    if (!dbHasTable($table)) return;
    if (!dbHasColumn($table, $column)) {
        db()->exec('ALTER TABLE ' . dbIdentifier($table) . ' ADD COLUMN ' . dbIdentifier($column) . ' ' . $definition);
    }
}

function dbHasIndex(string $table, string $index): bool
{
    if (!dbHasTable($table)) return false;
    $stmt = db()->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function dbAddIndexIfMissing(string $table, string $index, string $definition): void
{
    if (!dbHasTable($table)) return;
    if (!dbHasIndex($table, $index)) {
        db()->exec('ALTER TABLE ' . dbIdentifier($table) . ' ADD ' . $definition);
    }
}

function ensureSchema(): void
{
    static $done = false;
    if ($done) return;
    $done = true;

    $sql = @file_get_contents(__DIR__ . '/install.sql');
    if (is_string($sql) && trim($sql) !== '') {
        try { db()->exec($sql); } catch (Throwable $e) { /* installer may already have handled it */ }
    }

    try {
        try { db()->exec("ALTER TABLE `items` MODIFY COLUMN `type` enum('like','poll','challenge_like','auto_like') NOT NULL"); } catch (Throwable $e) {}

        dbAddColumnIfMissing('users', 'coins', 'int NOT NULL DEFAULT 0 AFTER `last_name`');
        dbAddColumnIfMissing('users', 'like_emoji', "varchar(32) NOT NULL DEFAULT '❤️' AFTER `coins`");
        dbAddColumnIfMissing('users', 'default_channel', 'varchar(255) DEFAULT NULL AFTER `like_emoji`');
        dbAddColumnIfMissing('users', 'default_channel_id', 'varchar(64) DEFAULT NULL AFTER `default_channel`');
        dbAddColumnIfMissing('users', 'default_channel_title', 'varchar(255) DEFAULT NULL AFTER `default_channel_id`');
        dbAddColumnIfMissing('users', 'force_channels', 'longtext DEFAULT NULL AFTER `default_channel_title`');
        dbAddColumnIfMissing('users', 'ref_by', 'bigint DEFAULT NULL AFTER `force_channels`');
        dbAddColumnIfMissing('users', 'step', 'varchar(100) DEFAULT NULL AFTER `ref_by`');
        dbAddColumnIfMissing('users', 'temp_json', 'longtext DEFAULT NULL AFTER `step`');
        dbAddColumnIfMissing('users', 'created_at', 'datetime DEFAULT NULL AFTER `temp_json`');
        dbAddColumnIfMissing('users', 'last_seen', 'datetime DEFAULT NULL AFTER `created_at`');
        dbAddColumnIfMissing('users', 'last_daily_reward_date', 'date DEFAULT NULL AFTER `last_seen`');
        dbAddIndexIfMissing('users', 'idx_ref_by', 'KEY `idx_ref_by` (`ref_by`)');
        dbAddIndexIfMissing('users', 'idx_last_seen', 'KEY `idx_last_seen` (`last_seen`)');
        dbAddIndexIfMissing('users', 'idx_default_channel_id', 'KEY `idx_default_channel_id` (`default_channel_id`)');

        // فعال‌سازی خودکار جوین اجباری شخصی برای کاربرانی که قبلاً کانال را تنظیم کرده‌اند،
        // ولی force_channels آن‌ها خالی مانده است.
        try {
            if (dbHasTable('users') && dbHasColumn('users', 'default_channel') && dbHasColumn('users', 'force_channels')) {
                db()->exec("UPDATE `users` SET `force_channels` = JSON_ARRAY(`default_channel_id`) WHERE `default_channel_id` IS NOT NULL AND `default_channel_id` <> '' AND `default_channel_id` LIKE '-100%'");
                db()->exec("UPDATE `users` SET `force_channels` = JSON_ARRAY(`default_channel`) WHERE (`default_channel_id` IS NULL OR `default_channel_id` = '') AND `default_channel` IS NOT NULL AND `default_channel` <> '' AND (`force_channels` IS NULL OR `force_channels` = '' OR `force_channels` = '[]')");
            }
        } catch (Throwable $e) {
            if (function_exists('logError')) logError('Force join backfill failed', ['error' => $e->getMessage()]);
        }

        dbAddColumnIfMissing('items', 'fake_likes', 'int NOT NULL DEFAULT 0 AFTER `options_json`');
        dbAddColumnIfMissing('items', 'target_channel', 'varchar(255) DEFAULT NULL AFTER `fake_likes`');
        dbAddColumnIfMissing('items', 'channel_chat_id', 'varchar(64) DEFAULT NULL AFTER `target_channel`');
        dbAddColumnIfMissing('items', 'channel_message_id', 'bigint DEFAULT NULL AFTER `channel_chat_id`');
        dbAddColumnIfMissing('items', 'source_chat_id', 'varchar(64) DEFAULT NULL AFTER `channel_message_id`');
        dbAddColumnIfMissing('items', 'source_message_id', 'bigint DEFAULT NULL AFTER `source_chat_id`');
        dbAddColumnIfMissing('items', 'updated_at', 'datetime DEFAULT NULL AFTER `created_at`');
        dbAddIndexIfMissing('items', 'idx_channel_msg', 'KEY `idx_channel_msg` (`channel_chat_id`, `channel_message_id`)');
        dbAddIndexIfMissing('items', 'idx_created', 'KEY `idx_created` (`created_at`)');

        db()->exec("CREATE TABLE IF NOT EXISTS `coin_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `coins` int NOT NULL DEFAULT 0,
  `amount` int NOT NULL DEFAULT 0,
  `method` enum('card','zibal') NOT NULL DEFAULT 'card',
  `status` enum('created','pending','paid','rejected','failed') NOT NULL DEFAULT 'created',
  `receipt_file_id` text DEFAULT NULL,
  `receipt_type` varchar(30) DEFAULT NULL,
  `receipt_text` longtext DEFAULT NULL,
  `admin_message_id` bigint DEFAULT NULL,
  `zibal_track_id` varchar(128) DEFAULT NULL,
  `zibal_ref_id` varchar(128) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`, `status`),
  KEY `idx_track` (`zibal_track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        dbAddColumnIfMissing('coin_orders', 'receipt_type', 'varchar(30) DEFAULT NULL AFTER `receipt_file_id`');
        dbAddColumnIfMissing('coin_orders', 'receipt_text', 'longtext DEFAULT NULL AFTER `receipt_type`');
        dbAddColumnIfMissing('coin_orders', 'admin_message_id', 'bigint DEFAULT NULL AFTER `receipt_text`');
        dbAddColumnIfMissing('coin_orders', 'zibal_track_id', 'varchar(128) DEFAULT NULL AFTER `admin_message_id`');
        dbAddColumnIfMissing('coin_orders', 'zibal_ref_id', 'varchar(128) DEFAULT NULL AFTER `zibal_track_id`');
        dbAddIndexIfMissing('coin_orders', 'idx_user_status', 'KEY `idx_user_status` (`user_id`, `status`)');
        dbAddIndexIfMissing('coin_orders', 'idx_track', 'KEY `idx_track` (`zibal_track_id`)');
    } catch (Throwable $e) {
        if (function_exists('logError')) {
            logError('Schema migration failed', ['error' => $e->getMessage()]);
        }
    }
}

function setting(string $key, mixed $default = null): mixed
{
    $stmt = db()->prepare('SELECT svalue FROM settings WHERE skey = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row) return $default;
    $value = $row['svalue'];
    $decoded = json_decode((string)$value, true);
    return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
}

function set_setting(string $key, mixed $value): void
{
    $encoded = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    $stmt = db()->prepare('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)');
    $stmt->execute([$key, $encoded]);
}

function initDefaultSettings(): void
{
    $defaults = [
        'price_like' => '2',
        'price_poll' => '2',
        'price_challenge_like' => '3',
        'price_speed' => '0',
        'price_auto_like' => '2',
        'referral_reward' => '10',
        'new_user_coins' => '5',
        'global_join_channels' => [],
        'sponsor_text' => '☕ برای حمایت یا تبلیغات، با مدیریت ربات در ارتباط باشید.',
        'coin_packages' => [['coins' => 50, 'amount' => 50000], ['coins' => 100, 'amount' => 90000], ['coins' => 250, 'amount' => 200000]],
        'payment_card_enabled' => '1',
        'payment_zibal_enabled' => '0',
        'card_number' => '',
        'card_owner' => '',
        'card_text' => 'پس از واریز، رسید پرداخت را همینجا ارسال کنید.',
        'zibal_merchant' => '',
        'zibal_bridge_enabled' => '1',
        'zibal_bridge_url' => 'https://your-domain.ir/zibal_bridge.php',
        'zibal_bridge_secret' => 'CHANGE_THIS_SECRET',
        'zibal_bridge_ip' => '',
        'daily_reward_enabled' => '1',
        'daily_rewards' => ['1' => 0, '2' => 3, '3' => 1, '4' => 2, '5' => 3, '6' => 0],
        'schema_version' => '2.2',
    ];
    foreach ($defaults as $k => $v) {
        $stmt = db()->prepare('INSERT IGNORE INTO settings (skey, svalue) VALUES (?, ?)');
        $stmt->execute([$k, is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE)]);
    }
}

function getUser(int $userId): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function ensureUser(array $from, ?int $refBy = null): array
{
    $id = (int)($from['id'] ?? 0);
    if ($id <= 0) throw new RuntimeException('Invalid user id');

    $username = $from['username'] ?? null;
    $firstName = $from['first_name'] ?? '';
    $lastName = $from['last_name'] ?? '';
    $now = date('Y-m-d H:i:s');
    $existing = getUser($id);

    if (!$existing) {
        $coins = (int)setting('new_user_coins', 0);
        $validRef = ($refBy && $refBy !== $id && getUser($refBy)) ? $refBy : null;
        $stmt = db()->prepare('INSERT INTO users (id, username, first_name, last_name, coins, like_emoji, ref_by, created_at, last_seen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$id, $username, $firstName, $lastName, $coins, DEFAULT_LIKE_EMOJI, $validRef, $now, $now]);
        if ($validRef) {
            $reward = (int)setting('referral_reward', 10);
            addCoins($validRef, $reward);
            tg('sendMessage', [
                'chat_id' => $validRef,
                'text' => "🎉 یک کاربر جدید با لینک دعوت شما عضو شد.\n💰 {$reward} سکه به حساب شما اضافه شد.",
            ]);
        }
    } else {
        $stmt = db()->prepare('UPDATE users SET username = ?, first_name = ?, last_name = ?, last_seen = ? WHERE id = ?');
        $stmt->execute([$username, $firstName, $lastName, $now, $id]);
    }
    return getUser($id) ?: [];
}

function setStep(int $userId, ?string $step, array $temp = []): void
{
    $stmt = db()->prepare('UPDATE users SET step = ?, temp_json = ? WHERE id = ?');
    $stmt->execute([$step, json_encode($temp, JSON_UNESCAPED_UNICODE), $userId]);
}

function getTemp(array $user): array
{
    $temp = json_decode((string)($user['temp_json'] ?? ''), true);
    return is_array($temp) ? $temp : [];
}

function addCoins(int $userId, int $amount): void
{
    if ($amount === 0) return;
    $stmt = db()->prepare('UPDATE users SET coins = coins + ? WHERE id = ?');
    $stmt->execute([$amount, $userId]);
}

function takeCoins(int $userId, int $amount): bool
{
    if ($amount <= 0) return true;
    $stmt = db()->prepare('UPDATE users SET coins = coins - ? WHERE id = ? AND coins >= ?');
    $stmt->execute([$amount, $userId, $amount]);
    return $stmt->rowCount() > 0;
}

function isAdmin(int $userId): bool
{
    if (in_array($userId, MAIN_ADMINS, true)) return true;
    $stmt = db()->prepare('SELECT id FROM admins WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    return (bool)$stmt->fetch();
}
