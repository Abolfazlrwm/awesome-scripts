<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — وضعیت وبهوک
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

$ch = curl_init('https://api.telegram.org/bot' . BOT_TOKEN . '/getWebhookInfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 25,
]);
$raw = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($raw === false || $err) {
    echo "❌ خطا: " . ($err ?: 'curl failed') . "\n";
    exit;
}
$json = json_decode((string)$raw, true);
echo json_encode($json ?: ['raw' => $raw], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
