<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — پل درگاه زیبال
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

/**
 * فایل اتصال زیبال برای دامنه جداگانه
 * این فایل را روی دامنه‌ای که در زیبال ثبت کرده‌اید آپلود کنید؛ مثلا:
 * https://your-domain.ir/zibal_bridge.php
 * مقدار BRIDGE_SECRET را با مقدار داخل پنل ربات یکی کنید.
 */
declare(strict_types=1);

const BRIDGE_SECRET = 'CHANGE_THIS_SECRET';

header('X-Content-Type-Options: nosniff');

function bridgeJson(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function bridgeInput(): array
{
    $raw = file_get_contents('php://input');
    $json = json_decode((string)$raw, true);
    if (is_array($json)) return $json;
    return $_POST + $_GET;
}

function base64urlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64urlDecode(string $data): string
{
    $pad = strlen($data) % 4;
    if ($pad) $data .= str_repeat('=', 4 - $pad);
    $decoded = base64_decode(strtr($data, '-_', '+/'), true);
    return $decoded === false ? '' : $decoded;
}

function selfUrl(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'your-domain.ir';
    $path = strtok($_SERVER['REQUEST_URI'] ?? '/zibal_bridge.php', '?');
    return $scheme . '://' . $host . $path;
}

function zibalApiBridge(string $endpoint, array $payload): array
{
    $ch = curl_init('https://gateway.zibal.ir/v1/' . ltrim($endpoint, '/'));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $err) return ['ok' => false, 'result' => -1, 'message' => $err ?: 'connection failed', 'http_code' => $code];
    $json = json_decode((string)$raw, true);
    return is_array($json) ? $json : ['ok' => false, 'result' => -2, 'message' => 'invalid response', 'raw' => $raw, 'http_code' => $code];
}

$input = bridgeInput();
$action = (string)($input['action'] ?? $_GET['action'] ?? '');

// تست عمومی برای اطمینان از اینکه فایل درست اجرا می‌شود.
// این تست هیچ اطلاعات محرمانه‌ای برنمی‌گرداند.
if ($action === 'health' || isset($_GET['health'])) {
    bridgeJson([
        'ok' => true,
        'result' => 1,
        'message' => 'zibal bridge is running',
        'host' => ($_SERVER['HTTP_HOST'] ?? ''),
        'php' => PHP_VERSION,
    ]);
}

if ($action === 'callback') {
    $return = (string)($_GET['return'] ?? '');
    $sig = (string)($_GET['sig'] ?? '');
    $expected = hash_hmac('sha256', $return, BRIDGE_SECRET);
    if ($return === '' || !hash_equals($expected, $sig)) {
        http_response_code(403);
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>درخواست پرداخت معتبر نیست.</h3></body></html>';
        exit;
    }
    $callback = base64urlDecode($return);
    if (!preg_match('~^https?://~i', $callback)) {
        http_response_code(400);
        echo '<html dir="rtl" lang="fa"><body style="font-family:tahoma"><h3>آدرس برگشت معتبر نیست.</h3></body></html>';
        exit;
    }
    $params = $_GET;
    unset($params['action'], $params['return'], $params['sig']);
    $sep = str_contains($callback, '?') ? '&' : '?';
    header('Location: ' . $callback . $sep . http_build_query($params));
    exit;
}

$secret = (string)($input['secret'] ?? '');
if (!hash_equals(BRIDGE_SECRET, $secret)) {
    bridgeJson(['ok' => false, 'result' => -403, 'message' => 'forbidden'], 403);
}

if ($action === 'ping') {
    bridgeJson(['ok' => true, 'result' => 1, 'message' => 'pong', 'host' => ($_SERVER['HTTP_HOST'] ?? '')]);
}

if ($action === 'request') {
    $merchant = trim((string)($input['merchant'] ?? ''));
    $amount = (int)($input['amount'] ?? 0);
    $callbackUrl = trim((string)($input['callbackUrl'] ?? ''));
    $description = (string)($input['description'] ?? '');
    $orderId = (string)($input['orderId'] ?? '');
    if ($merchant === '' || $amount <= 0 || !preg_match('~^https?://~i', $callbackUrl)) {
        bridgeJson(['ok' => false, 'result' => -400, 'message' => 'bad request'], 400);
    }
    $return = base64urlEncode($callbackUrl);
    $sig = hash_hmac('sha256', $return, BRIDGE_SECRET);
    $bridgeCallback = selfUrl() . '?action=callback&return=' . rawurlencode($return) . '&sig=' . rawurlencode($sig);
    $res = zibalApiBridge('request', [
        'merchant' => $merchant,
        'amount' => $amount,
        'callbackUrl' => $bridgeCallback,
        'description' => $description,
        'orderId' => $orderId,
    ]);
    if ((int)($res['result'] ?? 0) === 100 && !empty($res['trackId'])) {
        $res['ok'] = true;
        $res['url'] = 'https://gateway.zibal.ir/start/' . rawurlencode((string)$res['trackId']);
    }
    bridgeJson($res);
}

if ($action === 'verify') {
    $merchant = trim((string)($input['merchant'] ?? ''));
    $trackId = (string)($input['trackId'] ?? '');
    if ($merchant === '' || $trackId === '') bridgeJson(['ok' => false, 'result' => -400, 'message' => 'bad request'], 400);
    $res = zibalApiBridge('verify', ['merchant' => $merchant, 'trackId' => $trackId]);
    if (in_array((int)($res['result'] ?? 0), [100, 201], true)) $res['ok'] = true;
    bridgeJson($res);
}

bridgeJson(['ok' => false, 'result' => -404, 'message' => 'unknown action'], 404);
