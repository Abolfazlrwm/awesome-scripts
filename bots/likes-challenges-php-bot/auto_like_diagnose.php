<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — عیب‌یابی خودکار لایک
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
require_once __DIR__ . '/functions.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    ensureSchema();
    initDefaultSettings();

    echo "Auto Like Diagnose\n";
    echo "==================\n\n";

    $wh = tg('getWebhookInfo');
    echo "Webhook:\n";
    if ($wh['ok'] ?? false) {
        $info = $wh['result'] ?? [];
        echo "URL: " . ($info['url'] ?? '') . "\n";
        echo "Pending updates: " . ($info['pending_update_count'] ?? 0) . "\n";
        echo "Allowed updates: " . json_encode($info['allowed_updates'] ?? [], JSON_UNESCAPED_UNICODE) . "\n";
        if (!empty($info['last_error_message'])) echo "Last error: " . $info['last_error_message'] . "\n";
        $allowed = $info['allowed_updates'] ?? [];
        if (is_array($allowed) && $allowed && !in_array('channel_post', $allowed, true)) {
            echo "WARNING: channel_post is not in allowed_updates. Open setwebhook.php once.\n";
        }
    } else {
        echo "ERROR: cannot fetch webhook info: " . json_encode($wh, JSON_UNESCAPED_UNICODE) . "\n";
    }

    echo "\nActive auto-like jobs:\n";
    $jobs = db()->query("SELECT * FROM auto_like_jobs WHERE status = 'active' AND remaining_posts > 0 ORDER BY id DESC LIMIT 20")->fetchAll();
    if (!$jobs) {
        echo "No active jobs.\n";
    }
    foreach ($jobs as $job) {
        echo "#{$job['id']} | owner={$job['owner_id']} | channel_chat_id={$job['channel_chat_id']} | title={$job['channel_title']} | remaining={$job['remaining_posts']} | fake={$job['fake_likes']} | price={$job['price_per_post']}\n";
        $admin = checkBotAdminInChannelForAuto((string)$job['channel_chat_id']);
        echo "  Bot admin/edit status: " . (($admin['ok'] ?? false) ? 'OK' : 'FAILED') . ' - ' . ($admin['msg'] ?? '') . "\n";
    }

    echo "\nRecent auto_like.log:\n";
    $log = __DIR__ . '/auto_like.log';
    if (is_file($log)) {
        $lines = file($log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach (array_slice($lines, -80) as $line) echo $line . "\n";
    } else {
        echo "No auto_like.log file yet. Send a new post in the configured channel after creating an auto-like job.\n";
    }

    echo "\nRecent error.log Telegram API errors:\n";
    $elog = __DIR__ . '/error.log';
    if (is_file($elog)) {
        $lines = file($elog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach (array_slice($lines, -80) as $line) {
            if (str_contains($line, 'Telegram API error') || str_contains($line, 'Webhook fatal')) echo $line . "\n";
        }
    } else {
        echo "No error.log file.\n";
    }

} catch (Throwable $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
