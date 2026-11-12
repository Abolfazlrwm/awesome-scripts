<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: text/plain; charset=utf-8');

echo "در حال بررسی و تنظیم ربات...\n\n";
$commandsResult = apiRequest('setMyCommands', [
    'commands' => [
        ['command' => 'start', 'description' => 'شروع کار با ربات'],
        ['command' => 'panel', 'description' => 'پنل مدیریت (فقط ادمین)'],
    ],
]);
echo 'ثبت دستورات /start و /panel: ' . (!empty($commandsResult->ok) ? 'موفق ✅' : 'ناموفق ❌ (توکن رو چک کنید)') . "\n\n";

// نمایش وضعیت فعلی وبهوک
$webhookInfo = apiRequest('getWebhookInfo', []);
if ($webhookInfo && !empty($webhookInfo->ok)) {
    $info = $webhookInfo->result;
    echo 'آدرس وبهوک فعلی: ' . ($info->url ?: '(هنوز تنظیم نشده)') . "\n";
    echo 'تعداد آپدیت‌های در انتظار: ' . ($info->pending_update_count ?? 0) . "\n";
    if (!empty($info->last_error_message)) {
        echo 'آخرین خطای وبهوک: ' . $info->last_error_message . "\n";
    }
} else {
    echo "نتونستم وضعیت وبهوک رو بگیرم - لطفا مقدار API_KEY رو در config.php چک کنید.\n";
}

$suggestedUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'yourdomain.com') . rtrim(dirname($_SERVER['REQUEST_URI'] ?? ''), '/') . '/index.php';

echo "\nاگه وبهوک هنوز تنظیم نشده، این آدرس رو یک‌بار در مرورگر باز کنید:\n";
echo 'https://api.telegram.org/bot' . API_KEY . '/setWebhook?url=' . urlencode($suggestedUrl) . "\n";

echo "\nبعد از اتمام تنظیمات، این فایل (setup.php) رو از روی سرور حذف کنید.\n";
