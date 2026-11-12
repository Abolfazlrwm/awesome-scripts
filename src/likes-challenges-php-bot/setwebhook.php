<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — تنظیم وبهوک
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

function tgInstallerApi(string $method, array $params = []): array
{
    $ch = curl_init('https://api.telegram.org/bot' . BOT_TOKEN . '/' . $method);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($raw === false || $err) {
        return ['ok' => false, 'description' => $err ?: 'curl failed'];
    }
    $json = json_decode((string)$raw, true);
    return is_array($json) ? $json : ['ok' => false, 'description' => 'invalid json', 'raw' => $raw];
}

if (BOT_TOKEN === 'PUT_YOUR_BOT_TOKEN_HERE' || WEBHOOK_URL === 'https://example.com/likebot/bot.php') {
    http_response_code(400);
    echo "❌ ابتدا install.php را اجرا کنید یا config.php را تنظیم کنید.\n";
    exit;
}

$res = tgInstallerApi('setWebhook', [
    'url' => WEBHOOK_URL,
    'drop_pending_updates' => 'true',
    'allowed_updates' => json_encode(['message','callback_query','channel_post','edited_channel_post'], JSON_UNESCAPED_UNICODE),
]);

echo ($res['ok'] ?? false) ? "✅ وبهوک ست شد.\n\n" : "❌ وبهوک ست نشد.\n\n";
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
