<?php
// ========== ربات دانلودر یوتیوب (YouTube Downloader Bot) ==========
// نیازمند نصب ابزار متن‌باز yt-dlp روی سرور (نصب و استفاده کاملاً رایگان و قانونی برای محتوای مجاز)
// این ربات به‌صورت Webhook کار می‌کند.

// ================== تنظیمات پایه ===================
define('BOT_TOKEN', 'توکن ربات');              // توکن ربات از @BotFather
define('ADMIN_ID', 123456789);                  // آیدی عددی ادمین از @userinfobot
define('YTDLP_PATH', 'yt-dlp');                  // مسیر اجرایی yt-dlp روی سرور (اگر global نصب شده همین کافیست)
define('MAX_FILE_SIZE_MB', 50);                  // حداکثر حجم مجاز ارسال توسط ربات معمولی تلگرام (مگابایت)
define('TEMP_DIR', __DIR__ . '/downloads');      // پوشه‌ی موقت دانلود فایل‌ها

if (!is_dir(TEMP_DIR)) {
    mkdir(TEMP_DIR, 0755, true);
}

// ================== توابع کمکی ارتباط با تلگرام ==================
function apiRequest($method, $params = []) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function sendMessage($chatId, $text, $replyMarkup = null) {
    $params = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($replyMarkup) $params['reply_markup'] = json_encode($replyMarkup);
    return apiRequest('sendMessage', $params);
}

function editMessageText($chatId, $messageId, $text) {
    return apiRequest('editMessageText', [
        'chat_id' => $chatId, 'message_id' => $messageId, 'text' => $text, 'parse_mode' => 'HTML'
    ]);
}

function sendVideoFile($chatId, $filePath, $caption = '') {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendVideo";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chatId,
        'caption' => $caption,
        'video' => new CURLFile($filePath),
        'supports_streaming' => true,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function sendAudioFile($chatId, $filePath, $caption = '') {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendAudio";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chatId,
        'caption' => $caption,
        'audio' => new CURLFile($filePath),
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// ================== استخراج لینک یوتیوب از متن ==================
function extractYoutubeUrl($text) {
    if (preg_match('/(https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/\S+)/i', $text, $m)) {
        return $m[1];
    }
    return null;
}

// ================== اجرای yt-dlp ==================
function runYtDlp($url, $format, $outputTemplate) {
    $cmd = escapeshellcmd(YTDLP_PATH) . ' -f ' . escapeshellarg($format) .
           ' -o ' . escapeshellarg($outputTemplate) .
           ' --no-playlist --max-filesize ' . MAX_FILE_SIZE_MB . 'M ' .
           escapeshellarg($url) . ' 2>&1';
    exec($cmd, $output, $returnCode);
    return ['success' => $returnCode === 0, 'log' => implode("\n", $output)];
}

function findDownloadedFile($jobId) {
    $files = glob(TEMP_DIR . "/{$jobId}.*");
    return $files ? $files[0] : null;
}

// ================== پردازش آپدیت دریافتی از تلگرام ==================
$input = file_get_contents('php://input');
$update = json_decode($input, true);
if (!$update) { exit; }

// ---------- دستورات متنی ----------
if (isset($update['message'])) {
    $msg = $update['message'];
    $chatId = $msg['chat']['id'];
    $text = $msg['text'] ?? '';

    if ($text === '/start') {
        sendMessage($chatId,
            "🎬 <b>ربات دانلودر یوتیوب</b>\n\n" .
            "فقط لینک ویدیوی یوتیوب رو برام بفرست، کیفیت رو انتخاب کن و منتظر دانلود بمون.\n\n" .
            "⚠️ به دلیل محدودیت تلگرام، حداکثر حجم قابل ارسال " . MAX_FILE_SIZE_MB . " مگابایته.");
        exit;
    }

    $youtubeUrl = extractYoutubeUrl($text);
    if ($youtubeUrl) {
        $jobId = substr(md5($youtubeUrl . time()), 0, 12);

        // ذخیره‌ی موقت لینک برای این jobId (فایل ساده به‌جای دیتابیس)
        file_put_contents(TEMP_DIR . "/{$jobId}.url", $youtubeUrl);

        sendMessage($chatId, "🎯 کیفیت مورد نظرت رو انتخاب کن:", [
            'inline_keyboard' => [
                [
                    ['text' => '🎥 ویدیو (تا 480p)', 'callback_data' => "dl_video_{$jobId}"],
                    ['text' => '🎵 فقط صدا (MP3)', 'callback_data' => "dl_audio_{$jobId}"],
                ]
            ]
        ]);
        exit;
    }

    if ($text !== '') {
        sendMessage($chatId, "❗ لطفاً یک لینک معتبر یوتیوب بفرست.");
    }
    exit;
}

// ---------- دکمه‌های شیشه‌ای (انتخاب کیفیت) ----------
if (isset($update['callback_query'])) {
    $query = $update['callback_query'];
    $chatId = $query['message']['chat']['id'];
    $messageId = $query['message']['message_id'];
    $data = $query['data'];

    apiRequest('answerCallbackQuery', ['callback_query_id' => $query['id']]);

    if (preg_match('/^dl_(video|audio)_([a-f0-9]+)$/', $data, $m)) {
        $mode = $m[1];
        $jobId = $m[2];
        $urlFile = TEMP_DIR . "/{$jobId}.url";

        if (!file_exists($urlFile)) {
            editMessageText($chatId, $messageId, "❌ این درخواست منقضی شده. لینک رو دوباره بفرست.");
            exit;
        }

        $youtubeUrl = trim(file_get_contents($urlFile));
        editMessageText($chatId, $messageId, "⏳ در حال دانلود... این ممکنه چند دقیقه طول بکشه.");

        if ($mode === 'video') {
            $format = 'best[height<=480][filesize<' . MAX_FILE_SIZE_MB . 'M]/best[height<=480]';
            $outputTemplate = TEMP_DIR . "/{$jobId}.%(ext)s";
        } else {
            $format = 'bestaudio';
            $outputTemplate = TEMP_DIR . "/{$jobId}.%(ext)s";
        }

        $result = runYtDlp($youtubeUrl, $format, $outputTemplate);

        if (!$result['success']) {
            editMessageText($chatId, $messageId, "❌ دانلود ناموفق بود. ممکنه ویدیو خصوصی باشه یا حجمش زیاد باشه.");
            @unlink($urlFile);
            exit;
        }

        $downloadedFile = findDownloadedFile($jobId);
        if (!$downloadedFile) {
            editMessageText($chatId, $messageId, "❌ فایل دانلودشده پیدا نشد.");
            exit;
        }

        editMessageText($chatId, $messageId, "📤 در حال ارسال فایل...");

        if ($mode === 'video') {
            sendVideoFile($chatId, $downloadedFile, "✅ دانلود شد!");
        } else {
            sendAudioFile($chatId, $downloadedFile, "✅ دانلود شد!");
        }

        // پاک‌سازی فایل‌های موقت
        @unlink($downloadedFile);
        @unlink($urlFile);
    }
    exit;
}
