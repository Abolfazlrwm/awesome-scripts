<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — حذف وبهوک
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

$res = tgInstallerApi('deleteWebhook', ['drop_pending_updates' => 'true']);
echo ($res['ok'] ?? false) ? "✅ وبهوک حذف شد.\n\n" : "❌ وبهوک حذف نشد.\n\n";
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
