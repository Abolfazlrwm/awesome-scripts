<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — بررسی وضعیت نصب
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function row(string $name, bool $ok, string $detail = ''): array { return ['name' => $name, 'ok' => $ok, 'detail' => $detail]; }
function tgCheckApi(string $method): array
{
    if (!defined('BOT_TOKEN') || BOT_TOKEN === 'PUT_YOUR_BOT_TOKEN_HERE' || !function_exists('curl_init')) {
        return ['ok' => false, 'description' => 'token/curl not ready'];
    }
    $ch = curl_init('https://api.telegram.org/bot' . BOT_TOKEN . '/' . $method);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 20]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($raw === false || $err) return ['ok' => false, 'description' => $err ?: 'curl failed'];
    $json = json_decode((string)$raw, true);
    return is_array($json) ? $json : ['ok' => false, 'description' => 'invalid json'];
}

$checks = [];
$hasConfig = file_exists(__DIR__ . '/config.php');
$checks[] = row('وجود config.php', $hasConfig, $hasConfig ? 'فایل تنظیمات موجود است.' : 'ابتدا install.php را اجرا کنید.');

if ($hasConfig) {
    require_once __DIR__ . '/config.php';
}

$checks[] = row('نسخه PHP', PHP_VERSION_ID >= 80000, PHP_VERSION);
$checks[] = row('افزونه curl', extension_loaded('curl'), extension_loaded('curl') ? 'فعال است.' : 'غیرفعال است.');
$checks[] = row('افزونه PDO MySQL', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'فعال است.' : 'غیرفعال است.');
$checks[] = row('قابل نوشتن بودن پوشه', is_writable(__DIR__), is_writable(__DIR__) ? 'برای error.log و نصب مناسب است.' : 'پوشه قابل نوشتن نیست.');

if ($hasConfig && defined('BOT_TOKEN')) {
    $tokenOk = BOT_TOKEN !== '' && BOT_TOKEN !== 'PUT_YOUR_BOT_TOKEN_HERE';
    $checks[] = row('توکن ربات', $tokenOk, $tokenOk ? 'تنظیم شده است.' : 'توکن هنوز پیش‌فرض است.');
    $urlOk = defined('WEBHOOK_URL') && filter_var(WEBHOOK_URL, FILTER_VALIDATE_URL) && substr(WEBHOOK_URL, 0, 8) === 'https://';
    $checks[] = row('آدرس WEBHOOK_URL', (bool)$urlOk, defined('WEBHOOK_URL') ? WEBHOOK_URL : 'تعریف نشده');
    $adminOk = defined('MAIN_ADMINS') && is_array(MAIN_ADMINS) && count(MAIN_ADMINS) > 0 && (int)MAIN_ADMINS[0] > 0;
    $checks[] = row('ادمین اصلی', $adminOk, $adminOk ? implode(', ', MAIN_ADMINS) : 'تنظیم نشده');

    if (extension_loaded('pdo_mysql') && defined('DB_HOST')) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'), DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $checks[] = row('اتصال دیتابیس', true, 'موفق');
            $tables = ['settings','users','admins','items','votes','speed_challenges','auto_like_jobs','coin_orders'];
            foreach ($tables as $table) {
                try {
                    $pdo->query('SELECT 1 FROM `' . $table . '` LIMIT 1');
                    $checks[] = row('جدول ' . $table, true, 'موجود است.');
                } catch (Throwable $e) {
                    $checks[] = row('جدول ' . $table, false, 'پیدا نشد یا دسترسی ندارد.');
                }
            }
        } catch (Throwable $e) {
            $checks[] = row('اتصال دیتابیس', false, $e->getMessage());
        }
    }

    if ($tokenOk && extension_loaded('curl')) {
        $me = tgCheckApi('getMe');
        $checks[] = row('ارتباط با تلگرام getMe', (bool)($me['ok'] ?? false), ($me['ok'] ?? false) ? '@' . ($me['result']['username'] ?? '') : ($me['description'] ?? 'ناموفق'));
        $wh = tgCheckApi('getWebhookInfo');
        $whOk = (bool)($wh['ok'] ?? false);
        $detail = $whOk ? (string)($wh['result']['url'] ?? 'وبهوک خالی است') : (string)($wh['description'] ?? 'ناموفق');
        $checks[] = row('وضعیت وبهوک', $whOk && !empty($wh['result']['url']), $detail);
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>بررسی نصب ربات</title>
<style>
body{font-family:tahoma,Arial,sans-serif;background:#f4f7fb;margin:0;color:#172033}.wrap{max-width:900px;margin:35px auto;padding:0 16px}.card{background:#fff;border-radius:18px;padding:22px;box-shadow:0 8px 28px rgba(20,35,70,.08)}table{width:100%;border-collapse:collapse}td,th{padding:12px;border-bottom:1px solid #eef2f7;text-align:right}.ok{color:#067647;font-weight:700}.bad{color:#b42318;font-weight:700}.btn{display:inline-block;border-radius:12px;padding:11px 16px;background:#eef2ff;color:#1d4ed8;text-decoration:none;font-weight:700;margin:10px 0 0}</style>
</head>
<body><div class="wrap"><div class="card">
<h1>بررسی وضعیت نصب</h1>
<table><thead><tr><th>مورد</th><th>وضعیت</th><th>جزئیات</th></tr></thead><tbody>
<?php foreach ($checks as $c): ?>
<tr><td><?=h($c['name'])?></td><td class="<?=$c['ok']?'ok':'bad'?>"><?=$c['ok']?'✅ صحیح':'❌ مشکل'?></td><td><?=h($c['detail'])?></td></tr>
<?php endforeach; ?>
</tbody></table>
<a class="btn" href="install.php?force=1">نصب/تنظیم دوباره</a>
<a class="btn" href="webhook_info.php">جزئیات وبهوک</a>
<a class="btn" href="repair_schema.php">تعمیر دیتابیس</a>
</div></div></body></html>
