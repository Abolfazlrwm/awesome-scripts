<?php
/**
 * TZ Emojis Bot (Emoji Forge) — پنل وضعیت سبک برای cPanel
 *
 * Tiny status panel. Login with PANEL_KEY (stored in ../.env by the
 * installer). NEVER prints secret values — only which keys are set.
 */
declare(strict_types=1);
session_start();

$ROOT = dirname(__DIR__);
$ENV_PATH = $ROOT . '/.env';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function env_map(string $path): array {
    $out = [];
    if (!is_file($path)) return $out;
    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $out[trim($k)] = trim($v);
    }
    return $out;
}

$env = env_map($ENV_PATH);
$panelKey = $env['PANEL_KEY'] ?? '';

if (($_GET['logout'] ?? '') === '1') { unset($_SESSION['tz_panel']); header('Location: panel.php'); exit; }

$loginError = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['key'])) {
    if ($panelKey !== '' && hash_equals($panelKey, (string)$_POST['key'])) {
        $_SESSION['tz_panel'] = true;
    } else {
        $loginError = 'کلید اشتباه است.';
    }
}
$authed = !empty($_SESSION['tz_panel']) && $panelKey !== '';

$me = null; $meErr = '';
if ($authed && !empty($env['EMOJI_FORGE_TOKEN'])) {
    $ch = curl_init('https://api.telegram.org/bot' . $env['EMOJI_FORGE_TOKEN'] . '/getMe');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 12]);
    $raw = curl_exec($ch);
    if ($raw === false) { $meErr = 'اتصال به تلگرام ناموفق بود.'; }
    else {
        $data = json_decode((string)$raw, true);
        if (!empty($data['ok'])) $me = $data['result']; else $meErr = 'تلگرام توکن را قبول نکرد.';
    }
    curl_close($ch);
}

$keysToShow = ['EMOJI_FORGE_TOKEN', 'EMOJI_FORGE_ADMIN_IDS', 'AI_API_KEY',
               'AI_BASE_URL', 'AI_MODEL', 'PANEL_KEY'];
$installerPresent = is_file(__DIR__ . '/install.php');
$appPyPresent = is_file($ROOT . '/app.py');
$venvPresent = is_dir($ROOT . '/.venv');
$dataPresent = is_dir($ROOT . '/data');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>پنل وضعیت TZ Emojis</title>
<style>
body{font-family:Tahoma,Vazirmatn,sans-serif;background:#0f2a1d;color:#e9f5ee;margin:0;padding:1rem}
.card{max-width:720px;margin:1rem auto;background:#173b29;border-radius:14px;padding:1.4rem}
h1{font-size:1.2rem} h2{font-size:1.02rem;margin-top:1.3rem}
input{width:100%;box-sizing:border-box;padding:.55rem;border-radius:8px;border:1px solid #2d6a4f;background:#0f2a1d;color:#fff}
button{background:#40916c;color:#fff;border:0;padding:.6rem 1.3rem;border-radius:10px;cursor:pointer}
.ok{color:#74c69d}.bad{color:#ff9a9e}
table{width:100%;border-collapse:collapse} td{padding:.4rem;border-bottom:1px solid #2d6a4f;font-size:.92rem}
code{background:#0f2a1d;padding:.1rem .35rem;border-radius:5px;direction:ltr;display:inline-block}
pre{background:#0f2a1d;padding:.7rem;border-radius:8px;overflow:auto;direction:ltr;text-align:left;font-size:.8rem}
.warn{background:#5c4a1f;border-radius:8px;padding:.6rem .9rem}
a{color:#ffd166}
</style>
</head>
<body>
<div class="card">
<h1>📊 پنل وضعیت ربات TZ Emojis</h1>

<?php if ($panelKey === ''): ?>
  <div class="warn">⚠️ در فایل .env هیچ PANEL_KEY تنظیم نشده است؛ پنل قفل است.
  اول با install.php نصب کنید یا PANEL_KEY را دستی به .env اضافه کنید.</div>

<?php elseif (!$authed): ?>
  <?php if ($loginError): ?><p class="bad">✗ <?= h($loginError) ?></p><?php endif; ?>
  <form method="post">
    <label>کلید ورود پنل</label>
    <input type="password" name="key" dir="ltr" autofocus>
    <p><button type="submit">ورود</button></p>
  </form>

<?php else: ?>
  <p style="text-align:left"><a href="?logout=1">خروج</a></p>

  <?php if ($installerPresent): ?>
    <div class="warn">⚠️ فایل <code>install.php</code> هنوز روی هاست است! از نظر امنیتی همین حالا حذفش کنید
    (از File Manager یا دکمه‌ی حذف در صفحه‌ی موفقیت نصب).</div>
  <?php endif; ?>

  <h2>وضعیت اتصال تلگرام</h2>
  <?php if ($me): ?>
    <p class="ok">✓ ربات پاسخ می‌دهد: <b>@<?= h($me['username'] ?? '') ?></b>
       (id: <?= h($me['id'] ?? '') ?>)</p>
  <?php else: ?>
    <p class="bad">✗ <?= h($meErr ?: 'توکن در .env نیست.') ?></p>
  <?php endif; ?>

  <h2>پیکربندی (.env) — فقط وضعیت، بدون نمایش مقادیر</h2>
  <table>
  <?php foreach ($keysToShow as $k): $set = isset($env[$k]) && $env[$k] !== ''; ?>
    <tr><td><code><?= h($k) ?></code></td>
        <td class="<?= $set ? 'ok' : 'bad' ?>"><?= $set ? 'تنظیم شده ✓' : 'خالی ✗' ?></td></tr>
  <?php endforeach; ?>
  </table>
  <?php if (!empty($env['AI_BASE_URL'])): ?>
    <p><small>Base URL فعلی: <code><?= h($env['AI_BASE_URL']) ?></code>
    — مدل: <code><?= h($env['AI_MODEL'] ?? '') ?></code> (این دو مقدار محرمانه نیستند)</small></p>
  <?php endif; ?>

  <h2>فایل‌ها و اجزا</h2>
  <table>
    <tr><td>app.py (ورودی Passenger)</td><td class="<?= $appPyPresent ? 'ok' : 'bad' ?>"><?= $appPyPresent ? 'موجود ✓' : 'نیست ✗' ?></td></tr>
    <tr><td>.venv (محیط مجازی سرور)</td><td class="<?= $venvPresent ? 'ok' : 'bad' ?>"><?= $venvPresent ? 'موجود ✓' : 'نیست (در cPanel ساخته می‌شود)' ?></td></tr>
    <tr><td>پوشه data</td><td class="<?= $dataPresent ? 'ok' : 'bad' ?>"><?= $dataPresent ? 'موجود ✓' : 'نیست ✗' ?></td></tr>
  </table>

  <h2>دستورهای ری‌استارت</h2>
  <p>در cPanel — Setup Python App — دکمه‌ی <b>Restart</b>، یا از ترمینال:</p>
  <pre>touch <?= h($ROOT) ?>/tmp/restart.txt
# حالت سرور/systemd:
sudo systemctl restart tz-emojis
# حالت دستی:
cd <?= h($ROOT) ?> && ./stop.sh && ./start.sh</pre>
  <p><small>یادآوری: روی هاست اشتراکی، Passenger ممکن است پروسه را در بیکاری بکشد؛
  کرون keep-alive در راهنمای نصب را فعال کنید. حالت پایدار پیشنهادی: سرور + systemd.</small></p>
<?php endif; ?>
</div>
</body>
</html>
