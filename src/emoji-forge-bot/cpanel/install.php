<?php
/**
 * Abolfazl Bot (Emoji Forge) — نصب‌کننده‌ی تحت وب برای هاست cPanel
 *
 * Web installer wizard for shared/cPanel hosting (Passenger Python App).
 * Self-contained, PHP >= 7.4, no framework.
 *
 * SECURITY:
 *  - If ../.env already contains a bot token, this installer LOCKS itself.
 *  - Secrets are never echoed back. Password fields are never repopulated.
 *  - DELETE this file right after setup (the success page has a button).
 */
declare(strict_types=1);
session_start();

$ROOT = dirname(__DIR__);               // package root (parent of cpanel/)
$ENV_PATH = $ROOT . '/.env';
$APP_PY = $ROOT . '/app.py';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function env_value(string $path, string $key): string {
    if (!is_file($path)) return '';
    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        if (trim($k) === $key) return trim($v);
    }
    return '';
}

function tg_get_me(string $token): array {
    $ch = curl_init('https://api.telegram.org/bot' . $token . '/getMe');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($raw === false) return ['ok' => false, 'error' => 'خطای اتصال curl: ' . $err];
    $data = json_decode((string)$raw, true);
    if (!is_array($data) || empty($data['ok'])) {
        return ['ok' => false, 'error' => 'تلگرام توکن را قبول نکرد (توکن را از BotFather دوباره بگیرید).'];
    }
    return ['ok' => true, 'username' => $data['result']['username'] ?? '',
            'id' => $data['result']['id'] ?? ''];
}

function ai_ping(string $base, string $key): array {
    $url = rtrim($base, '/') . '/models';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 12,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key],
    ]);
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if ($raw === false || $code >= 400) {
        return ['ok' => false, 'error' => "پاسخ نگرفتیم (HTTP $code) — تنظیم ذخیره می‌شود و ربات هنگام خطا خودکار به پیشنهادهای داخلی برمی‌گردد."];
    }
    return ['ok' => true];
}

function shell_out(string $cmd): string {
    if (!function_exists('shell_exec')) return '';
    $out = @shell_exec($cmd . ' 2>/dev/null');
    return is_string($out) ? trim($out) : '';
}

/* ------------------------------------------------------------- lock */
$alreadyInstalled = env_value($ENV_PATH, 'EMOJI_FORGE_TOKEN') !== '';

/* ---------------------------------------------------- self-delete */
if (($_POST['action'] ?? '') === 'delete_installer') {
    if (!isset($_SESSION['tz_done'])) { http_response_code(403); exit('Forbidden'); }
    @unlink(__FILE__);
    echo '<!DOCTYPE html><html lang="fa" dir="rtl"><meta charset="utf-8">'
       . '<body style="font-family:Tahoma;padding:2rem"><h2>✅ فایل نصب‌کننده حذف شد.</h2>'
       . '<p>حالا از پنل وضعیت (panel.php) با کلیدی که ساختید استفاده کنید.</p></body></html>';
    exit;
}

/* --------------------------------------------------------- checks */
$checks = [];
$checks[] = ['نسخه PHP (حداقل 7.4)', version_compare(PHP_VERSION, '7.4', '>='),
             'نسخه فعلی: ' . PHP_VERSION];
$checks[] = ['افزونه curl', extension_loaded('curl'),
             extension_loaded('curl') ? 'فعال' : 'غیرفعال — با پشتیبانی هاست تماس بگیرید'];
$checks[] = ['قابلیت shell_exec', function_exists('shell_exec'),
             function_exists('shell_exec') ? 'فعال' : 'غیرفعال (تشخیص پایتون ممکن نیست، ولی نصب ادامه پیدا می‌کند)'];
$pyver = shell_out('python3 --version') ?: shell_out('/usr/bin/python3 --version')
       ?: shell_out('python3.11 --version') ?: shell_out('python3.12 --version');
$checks[] = ['Python 3 روی هاست', $pyver !== '', $pyver !== '' ? $pyver : 'پیدا نشد — در cPanel از «Setup Python App» نسخه 3.11+ بسازید'];
$checks[] = ['دایرکتوری اصلی قابل نوشتن', is_writable($ROOT),
             is_writable($ROOT) ? $ROOT : 'غیرقابل نوشتن: ' . $ROOT];

/* -------------------------------------------------------- process */
$errors = [];
$done = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'install') {
    if ($alreadyInstalled) {
        $errors[] = 'قبلاً نصب شده است (.env دارای توکن است). برای نصب مجدد اول .env را حذف کنید.';
    }
    $token   = trim((string)($_POST['token'] ?? ''));
    $admins  = trim((string)($_POST['admins'] ?? ''));
    $aiKey   = trim((string)($_POST['ai_key'] ?? ''));
    $aiBase  = trim((string)($_POST['ai_base'] ?? '')) ?: 'https://api.openai.com/v1';
    $aiModel = trim((string)($_POST['ai_model'] ?? '')) ?: 'gpt-4o-mini';
    $panelKey = trim((string)($_POST['panel_key'] ?? ''));
    if ($panelKey === '') $panelKey = bin2hex(random_bytes(12));

    if ($token === '' || strpos($token, ':') === false) {
        $errors[] = 'توکن ربات نامعتبر است (باید شامل «:» باشد).';
    }
    if ($admins === '' || !preg_match('/^\d+(\s*,\s*\d+)*$/', $admins)) {
        $errors[] = 'آیدی عددی ادمین را درست وارد کنید (مثلاً 123456789 یا چند آیدی با کاما).';
    }
    $me = null; $ai = null;
    if (!$errors) {
        $me = tg_get_me($token);
        if (!$me['ok']) $errors[] = 'اعتبارسنجی توکن: ' . $me['error'];
    }
    if (!$errors && $aiKey !== '') {
        $ai = ai_ping($aiBase, $aiKey);   // non-fatal by design
    }
    if (!$errors) {
        $lines = [
            'EMOJI_FORGE_TOKEN=' . $token,
            'EMOJI_FORGE_ADMIN_IDS=' . $admins,
            'EMOJI_FORGE_DATA_DIR=',
            'EMOJI_FORGE_MAX_CONCURRENT=2',
            'EMOJI_FORGE_MAX_FILE_BYTES=20971520',
            'EMOJI_FORGE_BRAIN_PROVIDER=rule',
            'EMOJI_FORGE_EXTERNAL_BRAIN_URL=',
            'EMOJI_FORGE_EXTERNAL_BRAIN_KEY=',
            'AI_API_KEY=' . $aiKey,
            'AI_BASE_URL=' . $aiBase,
            'AI_MODEL=' . $aiModel,
            'PANEL_KEY=' . $panelKey,
        ];
        if (file_put_contents($ENV_PATH, implode("\n", $lines) . "\n") === false) {
            $errors[] = 'نوشتن .env ناموفق بود؛ دسترسی نوشتن دایرکتوری اصلی را بررسی کنید.';
        } else {
            @chmod($ENV_PATH, 0600);
        }
    }
    if (!$errors) {
        $appPy = <<<'PYEOF'
# Passenger entry point for cPanel "Setup Python App" (generated by cpanel/install.php).
# Starts the bot's Telegram polling loop in a background thread and
# exposes a trivial WSGI status page so Passenger stays happy.
import os
import sys
import threading

BASE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.join(BASE, "src"))

# Load .env sitting next to this file (environment wins over file).
try:
    with open(os.path.join(BASE, ".env"), encoding="utf-8") as _fh:
        for _line in _fh:
            _line = _line.strip()
            if _line and not _line.startswith("#") and "=" in _line:
                _k, _v = _line.split("=", 1)
                os.environ.setdefault(_k.strip(), _v.strip())
except OSError:
    pass


def _run_bot():
    from emoji_forge.bot import main
    main()


_started = False
if not _started:
    _started = True
    threading.Thread(target=_run_bot, daemon=True, name="tz-emojis-bot").start()


def application(environ, start_response):
    start_response("200 OK", [("Content-Type", "text/plain; charset=utf-8")])
    return ["Abolfazl bot is running (Telegram polling).".encode("utf-8")]
PYEOF;
        if (file_put_contents($APP_PY, $appPy) === false) {
            $errors[] = 'نوشتن app.py ناموفق بود؛ دسترسی نوشتن دایرکتوری اصلی را بررسی کنید.';
        }
    }
    if (!$errors) {
        $_SESSION['tz_done'] = true;
        $done = ['username' => $me['username'] ?? '', 'ai' => $ai,
                 'panel_key' => $panelKey, 'root' => $ROOT];
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>نصب ربات Abolfazl — cPanel</title>
<style>
body{font-family:Tahoma,Vazirmatn,sans-serif;background:#0f2a1d;color:#e9f5ee;margin:0;padding:1rem}
.card{max-width:760px;margin:1rem auto;background:#173b29;border-radius:14px;padding:1.4rem;box-shadow:0 8px 30px rgba(0,0,0,.35)}
h1{font-size:1.25rem;margin:.2rem 0 1rem} h2{font-size:1.05rem;margin-top:1.4rem}
label{display:block;margin:.7rem 0 .3rem;font-size:.9rem}
input{width:100%;box-sizing:border-box;padding:.55rem;border-radius:8px;border:1px solid #2d6a4f;background:#0f2a1d;color:#fff}
button{background:#40916c;color:#fff;border:0;padding:.7rem 1.4rem;border-radius:10px;font-size:1rem;cursor:pointer}
button.danger{background:#b02a30}
.ok{color:#74c69d}.bad{color:#ff9a9e}
table{width:100%;border-collapse:collapse} td{padding:.4rem;border-bottom:1px solid #2d6a4f;font-size:.9rem}
.err{background:#5c1f22;border-radius:8px;padding:.6rem .9rem;margin:.5rem 0}
.warn{background:#5c4a1f;border-radius:8px;padding:.6rem .9rem;margin:.5rem 0}
code{background:#0f2a1d;padding:.1rem .35rem;border-radius:5px;direction:ltr;display:inline-block}
pre{background:#0f2a1d;padding:.7rem;border-radius:8px;overflow:auto;direction:ltr;text-align:left;font-size:.8rem}
small{opacity:.85}
</style>
</head>
<body>
<div class="card">
<h1>🤖 نصب ربات Abolfazl روی هاست cPanel</h1>

<?php if ($done): ?>
  <h2 class="ok">✅ نصب کامل شد</h2>
  <p>ربات <b>@<?= h($done['username']) ?></b> شناسایی شد و فایل‌های <code>.env</code> و <code>app.py</code> ساخته شدند.</p>
  <?php if ($done['ai'] && !$done['ai']['ok']): ?>
    <div class="warn">⚠️ تست اتصال API سفارشی: <?= h($done['ai']['error']) ?></div>
  <?php elseif ($done['ai']): ?>
    <p class="ok">✓ اتصال API سفارشی هم پاسخ داد.</p>
  <?php endif; ?>
  <p>🔑 کلید ورود پنل وضعیت (panel.php) — همین حالا ذخیره‌اش کنید:<br>
     <code><?= h($done['panel_key']) ?></code></p>

  <h2>قدم‌های بعدی در cPanel (دقیقاً به همین ترتیب)</h2>
  <ol>
    <li>وارد cPanel شوید → بخش <b>Software</b> → گزینه‌ی <b>Setup Python App</b>.</li>
    <li><b>Create Application</b>: نسخه Python را <b>3.11 یا بالاتر</b> انتخاب کنید.</li>
    <li><b>Application root</b> را مسیر همین پوشه بگذارید: <code><?= h($done['root']) ?></code></li>
    <li><b>Application URL</b> را یک زیردامنه/مسیر خالی انتخاب کنید (مثلاً <code>bot</code>).</li>
    <li><b>Application startup file</b> = <code>app.py</code> و <b>Entry point</b> = <code>application</code>.</li>
    <li>روی <b>Create</b> بزنید؛ بعد در همان صفحه دستور <b>Run Pip Install</b> را با فایل <code>requirements.txt</code> اجرا کنید.</li>
    <li>دکمه‌ی <b>Restart</b> را بزنید و آدرس اپ را باز کنید؛ باید متن «Abolfazl bot is running» را ببینید.</li>
    <li>در تلگرام به ربات <b>/start</b> بفرستید.</li>
  </ol>

  <h2>کرون‌جاب‌های پیشنهادی (cPanel → Cron Jobs)</h2>
  <p>حالت Polling روی هاست اشتراکی باید زنده نگه داشته شود؛ این دو کرون کمک می‌کنند:</p>
  <pre>*/5 * * * * /usr/bin/curl -fsS https://YOUR-DOMAIN/bot/ &gt;/dev/null 2&gt;&amp;1
* * * * * touch <?= h($done['root']) ?>/tmp/restart.txt 2&gt;/dev/null; true</pre>
  <p><small>آدرس YOUR-DOMAIN/bot را با آدرس واقعی اپ خودتان عوض کنید. کرون دوم فقط وقتی لازم است
  که اپ خواب می‌رود؛ Passenger با فایل restart.txt اپ را تازه می‌کند. روش پیشنهادی اصلی همچنان
  سرور + systemd است (install.sh)؛ هاست اشتراکی ممکن است پروسه را هر از گاهی بکشد.</small></p>

  <div class="err">
    <b>⚠️ مهم امنیتی:</b> همین حالا فایل نصب‌کننده را حذف کنید تا کسی نتواند دوباره نصب کند:
    <form method="post" style="margin-top:.6rem">
      <input type="hidden" name="action" value="delete_installer">
      <button class="danger" type="submit">🗑 حذف install.php</button>
    </form>
  </div>

<?php elseif ($alreadyInstalled): ?>
  <div class="warn">🔒 این ربات قبلاً نصب شده است (.env دارای توکن است) و نصب‌کننده قفل است.
  برای مشاهده وضعیت از <a style="color:#ffd166" href="panel.php">پنل وضعیت</a> استفاده کنید.
  برای نصب مجدد، اول فایل <code>.env</code> را حذف کنید.</div>

<?php else: ?>
  <h2>۱) بررسی محیط هاست</h2>
  <table>
  <?php foreach ($checks as [$name, $pass, $note]): ?>
    <tr><td><?= h($name) ?></td>
        <td class="<?= $pass ? 'ok' : 'bad' ?>"><?= $pass ? '✓' : '✗' ?></td>
        <td><?= h($note) ?></td></tr>
  <?php endforeach; ?>
  </table>

  <?php foreach ($errors as $e): ?>
    <div class="err">✗ <?= h($e) ?></div>
  <?php endforeach; ?>

  <h2>۲) اطلاعات ربات</h2>
  <form method="post" autocomplete="off">
    <input type="hidden" name="action" value="install">
    <label>توکن ربات (از @BotFather) *</label>
    <input type="password" name="token" required dir="ltr" placeholder="123456789:AA...">
    <label>آیدی عددی ادمین‌ها (با کاما) *</label>
    <input type="text" name="admins" required dir="ltr" value="<?= h($_POST['admins'] ?? '') ?>" placeholder="123456789">
    <label>کلید API اختصاصی (اختیاری — برای اتصال سرویس دلخواه)</label>
    <input type="password" name="ai_key" dir="ltr" placeholder="sk-...">
    <label>آدرس پایه API (Base URL)</label>
    <input type="text" name="ai_base" dir="ltr" value="<?= h($_POST['ai_base'] ?? 'https://api.openai.com/v1') ?>">
    <label>نام مدل</label>
    <input type="text" name="ai_model" dir="ltr" value="<?= h($_POST['ai_model'] ?? 'gpt-4o-mini') ?>">
    <label>کلید ورود پنل وضعیت (خالی بگذارید = ساخت خودکار)</label>
    <input type="password" name="panel_key" dir="ltr">
    <p><small>توکن با تلگرام اعتبارسنجی می‌شود. اگر کلید API بدهید، یک تست سبک اتصال انجام می‌شود
    (ناموفق بودنش نصب را متوقف نمی‌کند). هیچ مقداری بعد از ارسال دوباره نمایش داده نمی‌شود.</small></p>
    <button type="submit">نصب کن 🚀</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>
