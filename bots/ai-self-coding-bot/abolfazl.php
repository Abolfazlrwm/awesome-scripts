<?php
// Telegram: @Abolfazl_rwm
// =======================================================
// تنظیمات اصلی - اختصاصی شده با اطلاعات شما
// =======================================================
define('API_KEY', getenv('BOT_TOKEN') ?: 'توکن ربات'); 
define('OPENROUTER_KEY', ''); 
define('OWNER_ID', (int) (getenv('OWNER_ID') ?: 0)); 
define('BOT_NAME', 'پسرم ابوالفضل');
define('TARGET_FILE', 'bot.php'); 
define('ERROR_LOG_FILE', 'error_log');

// دریافت آپدیت‌ها از تلگرام
$string = file_get_contents("php://input");
$update = json_decode($string, true);
if (!$update) exit;

$message = $update['message'] ?? null;
if (!$message) exit;

$text = $message['text'] ?? '';
$chat_id = $message['chat']['id'] ?? '';

/**
 * تابع کمکی برای ارسال پیام به مالک
 */
function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot".API_KEY."/sendMessage";
    $post_fields = array('chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'Markdown');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_exec($ch);
    curl_close($ch);
}

// بررسی دسترسی: فقط مالک و پیام‌هایی که با اسم ربات شروع شوند پردازش می‌شوند
if ($chat_id == OWNER_ID && strpos($text, BOT_NAME) === 0) {
    
    // جدا کردن دستور اصلی از اسم ربات
    $user_instruction = trim(substr($text, strlen(BOT_NAME)));
    
    // خواندن کدهای فعلی فایل مقصد جهت ارائه به هوش مصنوعی به عنوان Context
    $current_bot_code = file_exists(TARGET_FILE) ? file_get_contents(TARGET_FILE) : '';
    
    // تشخیص نوع درخواست (اصلاح باگ / بررسی ارور لاگ / افزودن قابلیت جدید)
    $check_auto_log = (strpos($user_instruction, 'ارور لاگ') !== false || strpos($user_instruction, 'ارورلاگ') !== false);
    $is_bug_fix = ($check_auto_log || strpos($user_instruction, 'اصلاح') !== false || strpos($user_instruction, 'باگ') !== false || strpos($user_instruction, 'خرابه') !== false);
    
    $error_context = "";
    if ($check_auto_log) {
        if (file_exists(ERROR_LOG_FILE)) {
            // خواندن ۱۰ خط آخر فایل ارور لاگ جهت بهینه‌سازی پرامپت
            $log_lines = file(ERROR_LOG_FILE);
            $last_lines = array_slice($log_lines, -10);
            $error_context = implode("", $last_lines);
            sendMessage($chat_id, "🔍 " . BOT_NAME . " فایل ارور لاگ هاست را پیدا کرد. در حال تحلیل خطاهای زیر:\n\n`" . $error_context . "`");
        } else {
            sendMessage($chat_id, "⚠️ " . BOT_NAME . " : فایل `error_log` در این پوشه یافت نشد. اما کد را بازبینی می‌کنم.");
        }
    } elseif ($is_bug_fix) {
        sendMessage($chat_id, "🔧 " . BOT_NAME . " در حال بررسی کدهای قبلی و رفع باگ است...");
    } else {
        sendMessage($chat_id, "🧠 " . BOT_NAME . " در حال برنامه‌نویسی به زبان PHP و توسعه فایل ربات کاربران است...");
    }
    
    // اتصال به OpenRouter API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    $headers = [
        "Authorization: Bearer " . OPENROUTER_KEY,
        "Content-Type: application/json",
        "HTTP-Referer: http://localhost", // مورد نیاز برای OpenRouter
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // پرامپت مهندسی شده و سخت‌گیرانه برای جلوگیری از باگ تگ‌های تکراری
    $prompt = "تو یک توسعه‌دهنده ارشد ربات تلگرام با زبان PHP خالص (بدون فریم‌ورک) هستی.
    کد فعلی ربات (فایل bot.php) به این شرح است:
    ----
    $current_bot_code
    ----
    
    خطاهای ثبت شده در سرور (Error Log) در صورت وجود:
    ----
    $error_context
    ----
    
    درخواست کاربر این است: '$user_instruction'
    
    قوانین فوق‌العاده حیاتی برای تولید کد که اگر رعایت نکنی ربات خراب می‌شود:
    ۱. اگر درخواست کاربر یک 'قابلیت یا دستور جدید' است، تو نباید کل فایل را از اول بازنویسی کنی. فقط و فقط تکه کد مربوط به شرط جدید (مانند بلاک if یا کدهای داخل آن) را بنویس تا به انتهای فایل چسبانده شود.
    ۲. به هیچ عنوان تگ بازکننده `<?php` یا توابعی مثل `bot` یا تعاریف متغیرهای عمومی مثل `\$update` یا `\$message` را در کد جدید تکرار نکن! این‌ها از قبل در فایل وجود دارند. تکرار آن‌ها خطای Syntax و Fatal ایجاد می‌کند.
    ۳. فقط و فقط در صورتی کل فایل را بازنویسی کن که کاربر کلمه 'اصلاح'، 'باگ' یا 'ارور لاگ' را به کار برده باشد. در این حالت کل فایل bot.php را از تگ اولیه `<?php` تا انتها به صورت کاملاً اصلاح‌شده و یکپارچه خروجی بده.
    ۴. خروجی تو باید فقط و فقط کد PHP خالص داخل تگ ```php ... ``` باشد. هیچ توضیح فارسی، انگلیسی یا مارک‌داون دیگری خارج از تگ کد ننویس.";
    
    $post_data = [
        "model" => "meta-llama/llama-3-70b-instruct", 
        "messages" => [
            ["role" => "system", "content" => $prompt],
            ["role" => "user", "content" => $user_instruction]
        ],
        "temperature" => 0.1
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    $response = curl_exec($ch);
    curl_close($ch);
    
    $res_json = json_decode($response, true);
    $ai_content = $res_json['choices'][0]['message']['content'] ?? '';
    
    // استخراج کد PHP خالص از بین تگ‌های مارک‌داون
    if (preg_match('/```php(.*?)```/s', $ai_content, $matches)) {
        $new_code = trim($matches[1]);
    } else {
        $new_code = trim($ai_content);
    }
    
    if (!empty($new_code)) {
        if ($is_bug_fix) {
            // در حالت رفع باگ، کل فایل با کد یکپارچه و جدید جایگزین می‌شود
            file_put_contents(TARGET_FILE, $new_code);
            
            if (file_exists(ERROR_LOG_FILE)) @unlink(ERROR_LOG_FILE);
            
            sendMessage($chat_id, "✅ باگ‌ها برطرف شد! فایل `bot.php` به صورت کامل بازنویسی و اصلاح شد.");
        } else {
            // در حالت افزودن دستور جدید، تکه کد به انتهای فایل الصاق می‌شود
            $log_text = "\n\n// --- قابلیت جدید اضافه شده ---\n" . $new_code . "\n";
            file_put_contents(TARGET_FILE, $log_text, FILE_APPEND);
            
            sendMessage($chat_id, "✅ قابلیت جدید با موفقیت به فایل `bot.php` اضافه شد.");
        }
    } else {
        sendMessage($chat_id, "❌ " . BOT_NAME . " نتوانست کدی تولید یا اصلاح کند. پاسخ دریافتی از هوش مصنوعی خالی بود.");
    }
}
include 'bot.php';
