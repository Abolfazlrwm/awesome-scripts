<?php
/**
 * ============================================================
 *  Likes & Challenges Bot — نصب‌کننده
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

declare(strict_types=1);

$root = __DIR__;
$lockFile = $root . '/.installed';
$errors = [];
$messages = [];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function defaultWebhookUrl(): string
{
    $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || strtolower($proto) === 'https';
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'example.com';
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir = $dir === '/' || $dir === '\\' ? '' : rtrim($dir, '/');
    return $scheme . '://' . $host . $dir . '/bot.php';
}

function splitAdmins(string $value): array
{
    $parts = preg_split('/[\s,،]+/u', trim($value));
    $admins = [];
    foreach ($parts ?: [] as $part) {
        $id = (int)trim($part);
        if ($id > 0) {
            $admins[] = $id;
        }
    }
    return array_values(array_unique($admins));
}

function phpString(string $value): string
{
    return var_export($value, true);
}

function writeConfig(array $data): string
{
    $admins = '[' . implode(', ', array_map('intval', $data['main_admins'])) . ']';
    $debug = !empty($data['debug_mode']) ? 'true' : 'false';
    return <<<PHP
<?php
/**
 * تنظیمات اصلی ربات چالش • لایک • نظرسنجی
 * این فایل توسط install.php ساخته شده است.
 */

declare(strict_types=1);

const BOT_TOKEN = {bot_token};
const BOT_USERNAME = {bot_username};
const WEBHOOK_URL = {webhook_url};
const MAIN_ADMINS = {admins};

const DB_HOST = {db_host};
const DB_NAME = {db_name};
const DB_USER = {db_user};
const DB_PASS = {db_pass};
const DB_CHARSET = 'utf8mb4';

const DEFAULT_LIKE_EMOJI = '❤️';
const DEFAULT_TIMEZONE = 'Asia/Tehran';
const DEBUG_MODE = {debug};
PHP;
}

function telegramApi(string $token, string $method, array $params = []): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'description' => 'PHP cURL extension is disabled'];
    }
    $ch = curl_init('https://api.telegram.org/bot' . $token . '/' . $method);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $err) {
        return ['ok' => false, 'description' => $err ?: 'curl failed', 'http_code' => $code];
    }
    $json = json_decode((string)$raw, true);
    if (!is_array($json)) {
        return ['ok' => false, 'description' => 'invalid telegram response', 'raw' => $raw, 'http_code' => $code];
    }
    $json['http_code'] = $code;
    return $json;
}

$defaults = [
    'bot_token' => '',
    'bot_username' => '',
    'webhook_url' => defaultWebhookUrl(),
    'main_admins' => '',
    'db_host' => 'localhost',
    'db_name' => '',
    'db_user' => '',
    'db_pass' => '',
    'set_webhook' => '1',
    'debug_mode' => '',
];

$values = array_merge($defaults, $_POST);
$installed = file_exists($lockFile) && !isset($_GET['force']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim((string)$values['bot_token']);
    $webhookUrl = trim((string)$values['webhook_url']);
    $botUsername = ltrim(trim((string)$values['bot_username']), '@');
    $admins = splitAdmins((string)$values['main_admins']);
    $dbHost = trim((string)$values['db_host']);
    $dbName = trim((string)$values['db_name']);
    $dbUser = trim((string)$values['db_user']);
    $dbPass = (string)$values['db_pass'];

    if (PHP_VERSION_ID < 80000) {
        $errors[] = 'نسخه PHP باید 8.0 یا بالاتر باشد. نسخه فعلی: ' . PHP_VERSION;
    }
    if (!extension_loaded('pdo_mysql')) {
        $errors[] = 'افزونه pdo_mysql روی هاست فعال نیست.';
    }
    if (!extension_loaded('curl')) {
        $errors[] = 'افزونه curl روی هاست فعال نیست.';
    }
    if ($token === '' || !preg_match('/^\d{5,}:[A-Za-z0-9_\-]{20,}$/', $token)) {
        $errors[] = 'توکن ربات معتبر نیست.';
    }
    if ($webhookUrl === '' || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'آدرس وبهوک معتبر نیست.';
    }
    if (substr($webhookUrl, 0, 8) !== 'https://') {
        $errors[] = 'وبهوک تلگرام باید با https شروع شود.';
    }
    if (!$admins) {
        $errors[] = 'حداقل یک آیدی عددی ادمین اصلی وارد کنید.';
    }
    if ($dbHost === '' || $dbName === '' || $dbUser === '') {
        $errors[] = 'اطلاعات دیتابیس کامل نیست.';
    }

    $me = null;
    if (!$errors) {
        $me = telegramApi($token, 'getMe');
        if (!($me['ok'] ?? false)) {
            $errors[] = 'توکن توسط تلگرام تایید نشد: ' . ($me['description'] ?? 'خطای نامشخص');
        } else {
            $botUsername = $botUsername ?: (string)($me['result']['username'] ?? '');
            $messages[] = 'توکن تایید شد: @' . $botUsername;
        }
    }

    $pdo = null;
    if (!$errors) {
        try {
            $pdo = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4', $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $messages[] = 'اتصال دیتابیس موفق بود.';
        } catch (Throwable $e) {
            $errors[] = 'اتصال دیتابیس ناموفق بود: ' . $e->getMessage();
        }
    }

    if (!$errors && $pdo instanceof PDO) {
        try {
            $sql = file_get_contents($GLOBALS['root'] . '/install.sql');
            if ($sql === false) {
                throw new RuntimeException('install.sql پیدا نشد.');
            }
            $pdo->exec($sql);
            $messages[] = 'جدول‌های دیتابیس نصب/به‌روزرسانی شدند.';
        } catch (Throwable $e) {
            $errors[] = 'خطا در نصب دیتابیس: ' . $e->getMessage();
        }
    }

    if (!$errors) {
        $config = writeConfig([
            'bot_token' => phpString($token),
            'bot_username' => phpString($botUsername),
            'webhook_url' => phpString($webhookUrl),
            'admins' => $admins,
            'main_admins' => $admins,
            'db_host' => phpString($dbHost),
            'db_name' => phpString($dbName),
            'db_user' => phpString($dbUser),
            'db_pass' => phpString($dbPass),
            'debug_mode' => !empty($values['debug_mode']),
        ]);
        $config = strtr($config, [
            '{bot_token}' => phpString($token),
            '{bot_username}' => phpString($botUsername),
            '{webhook_url}' => phpString($webhookUrl),
            '{admins}' => '[' . implode(', ', $admins) . ']',
            '{db_host}' => phpString($dbHost),
            '{db_name}' => phpString($dbName),
            '{db_user}' => phpString($dbUser),
            '{db_pass}' => phpString($dbPass),
            '{debug}' => !empty($values['debug_mode']) ? 'true' : 'false',
        ]);
        if (@file_put_contents($GLOBALS['root'] . '/config.php', $config) === false) {
            $errors[] = 'امکان نوشتن config.php وجود ندارد. سطح دسترسی پوشه را بررسی کنید.';
        } else {
            $messages[] = 'فایل config.php ساخته شد.';
        }
    }

    if (!$errors && !empty($values['set_webhook'])) {
        $res = telegramApi($token, 'setWebhook', [
            'url' => $webhookUrl,
            'drop_pending_updates' => 'true',
            'allowed_updates' => json_encode(['message','callback_query','channel_post','edited_channel_post'], JSON_UNESCAPED_UNICODE),
        ]);
        if (!($res['ok'] ?? false)) {
            $errors[] = 'وبهوک ست نشد: ' . ($res['description'] ?? 'خطای نامشخص');
        } else {
            $messages[] = 'وبهوک با موفقیت ست شد.';
        }
    }

    if (!$errors) {
        @file_put_contents($lockFile, date('Y-m-d H:i:s') . PHP_EOL);
        $installed = true;
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>نصب ربات چالش، لایک و نظرسنجی</title>
<style>
body{font-family:tahoma,Arial,sans-serif;background:#f4f7fb;margin:0;color:#172033}.wrap{max-width:920px;margin:35px auto;padding:0 16px}.card{background:#fff;border-radius:18px;padding:24px;box-shadow:0 8px 28px rgba(20,35,70,.08);margin-bottom:18px}.title{margin:0 0 8px;font-size:24px}.muted{color:#667085;line-height:1.9}.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{margin-bottom:14px}.field label{display:block;font-weight:700;margin-bottom:7px}input[type=text],input[type=password],input[type=url]{width:100%;box-sizing:border-box;border:1px solid #d0d5dd;border-radius:12px;padding:12px;font-size:14px;direction:ltr;text-align:left}.full{grid-column:1/-1}.btn{border:0;border-radius:12px;padding:13px 22px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}.btn2{display:inline-block;border-radius:12px;padding:11px 16px;background:#eef2ff;color:#1d4ed8;text-decoration:none;font-weight:700;margin:4px}.ok{background:#ecfdf3;border:1px solid #abefc6;color:#067647;border-radius:14px;padding:12px;margin:8px 0}.err{background:#fef3f2;border:1px solid #fecdca;color:#b42318;border-radius:14px;padding:12px;margin:8px 0}.warn{background:#fffaeb;border:1px solid #fedf89;color:#93370d;border-radius:14px;padding:12px;margin:8px 0}.check{display:flex;gap:8px;align-items:center}.ltr{direction:ltr;text-align:left;white-space:pre-wrap}.small{font-size:12px}.footer{color:#667085;text-align:center;font-size:12px}@media(max-width:700px){.grid{grid-template-columns:1fr}.wrap{margin:14px auto}.card{padding:18px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1 class="title">نصب ربات چالش • لایک • نظرسنجی</h1>
    <div class="muted">اطلاعات را وارد کنید تا دیتابیس نصب شود، فایل تنظیمات ساخته شود و وبهوک تلگرام ست شود.</div>
  </div>

  <?php foreach ($messages as $m): ?><div class="ok">✅ <?=h($m)?></div><?php endforeach; ?>
  <?php foreach ($errors as $e): ?><div class="err">❌ <?=h($e)?></div><?php endforeach; ?>

  <?php if ($installed && !$errors): ?>
    <div class="card">
      <h2>✅ نصب کامل شد</h2>
      <p class="muted">ربات آماده استفاده است. برای امنیت، بعد از اطمینان از عملکرد، فایل <b>install.php</b> را حذف کنید یا نامش را تغییر دهید.</p>
      <a class="btn2" href="setup_check.php">بررسی وضعیت نصب</a>
      <a class="btn2" href="setwebhook.php">ست مجدد وبهوک</a>
      <a class="btn2" href="deletewebhook.php">حذف وبهوک</a>
      <a class="btn2" href="install.php?force=1">نصب/تنظیم دوباره</a>
    </div>
  <?php else: ?>
    <?php if (file_exists($lockFile)): ?><div class="warn">این ربات قبلاً نصب شده است. چون حالت نصب مجدد را باز کرده‌اید، اطلاعات جدید جایگزین می‌شود.</div><?php endif; ?>
    <form method="post" class="card" autocomplete="off">
      <div class="grid">
        <div class="field full">
          <label>توکن ربات تلگرام</label>
          <input type="password" name="bot_token" value="<?=h((string)$values['bot_token'])?>" placeholder="123456789:AA..." required>
        </div>
        <div class="field">
          <label>یوزرنیم ربات بدون @</label>
          <input type="text" name="bot_username" value="<?=h((string)$values['bot_username'])?>" placeholder="MyLikeBot">
        </div>
        <div class="field">
          <label>آیدی عددی ادمین اصلی</label>
          <input type="text" name="main_admins" value="<?=h((string)$values['main_admins'])?>" placeholder="123456789 یا چند آیدی با کاما" required>
        </div>
        <div class="field full">
          <label>آدرس وبهوک / bot.php</label>
          <input type="url" name="webhook_url" value="<?=h((string)$values['webhook_url'])?>" placeholder="https://domain.com/likebot/bot.php" required>
          <div class="muted small">باید دقیقاً به فایل bot.php همین پوشه اشاره کند و حتماً https باشد.</div>
        </div>
        <div class="field">
          <label>DB Host</label>
          <input type="text" name="db_host" value="<?=h((string)$values['db_host'])?>" required>
        </div>
        <div class="field">
          <label>DB Name</label>
          <input type="text" name="db_name" value="<?=h((string)$values['db_name'])?>" required>
        </div>
        <div class="field">
          <label>DB User</label>
          <input type="text" name="db_user" value="<?=h((string)$values['db_user'])?>" required>
        </div>
        <div class="field">
          <label>DB Password</label>
          <input type="password" name="db_pass" value="<?=h((string)$values['db_pass'])?>">
        </div>
        <div class="field full check">
          <input type="checkbox" id="set_webhook" name="set_webhook" value="1" <?=!empty($values['set_webhook'])?'checked':''?>>
          <label for="set_webhook">بعد از نصب، وبهوک را خودکار ست کن</label>
        </div>
        <div class="field full check">
          <input type="checkbox" id="debug_mode" name="debug_mode" value="1" <?=!empty($values['debug_mode'])?'checked':''?>>
          <label for="debug_mode">حالت خطایابی موقت فعال باشد</label>
        </div>
      </div>
      <button class="btn" type="submit">شروع نصب</button>
    </form>
  <?php endif; ?>

  <div class="footer">Like Challenge Bot Installer</div>
</div>
</body>
</html>
