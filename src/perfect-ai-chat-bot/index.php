<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/handlers.php';

ensureDataDirs();

$raw = file_get_contents('php://input');
$update = json_decode($raw);

if (DEBUG) {
    @file_put_contents(DATA_DIR . '/debug.log', date('Y-m-d H:i:s') . ' ' . $raw . "\n", FILE_APPEND);
}

if (!$update) {
    exit;
}

if (isset($update->callback_query)) {
    $cq = $update->callback_query;
    $cqFromId = $cq->from->id ?? null;
    $cqChatId = $cq->message->chat->id ?? $cqFromId;
    $cqData = $cq->data ?? '';
    if ($cqData !== 'joincheck' && $cqFromId !== null && !isAdmin($cqFromId) && !isMemberOfRequiredChannel($cqFromId)) {
        answerCallback($cq->id, 'ابتدا باید عضو کانال بشید.', true);
        showJoinRequiredMessage($cqChatId);
        exit;
    }

    handleCallbackQuery($cq);
    exit;
}

if (isset($update->my_chat_member)) {
    handleMyChatMember($update->my_chat_member);
    exit;
}

if (!isset($update->message) || ($update->message->chat->type ?? '') !== 'private') {
    exit;
}

$message   = $update->message;
$chatId    = $message->chat->id;
$messageId = $message->message_id;
$fromId    = $message->from->id ?? null;
$firstName = $message->from->first_name ?? '';
$username  = $message->from->username ?? '';
$text      = isset($message->text) ? $message->text : '';

if ($fromId === null) {
    exit;
}

if (!isAdmin($fromId) && !isMemberOfRequiredChannel($fromId)) {
    showJoinRequiredMessage($chatId);
    exit;
}

$user = getOrCreateUser($fromId, $firstName, $username);
$parts = preg_split('/\s+/', trim($text), 2);
$command = preg_replace('/@\w+$/', '', $parts[0] ?? '');
$commandPayload = $parts[1] ?? '';
if ($command === '/panel') {
    if (!isAdmin($fromId)) {
        sendMessage($chatId, 'شما به این بخش دسترسی ندارید.');
        exit;
    }
    showAdminPanel($chatId);
    exit;
}

if ($command === '/start') {
    handleStart($user, $commandPayload, $chatId);
    exit;
}

if (in_array($text, ['🔙 بازگشت', 'بازگشت', '✅ پایان چت', 'پایان چت'], true)) {
    $wasChatting = ($user['state'] ?? '') === 'chat';
    $user['state'] = 'none';
    saveUser($user);
    if ($wasChatting) {
        sendMessage($chatId, 'چت شما با موفقیت پایان یافت ✅');
    }
    showMainMenu($chatId, $user);
    exit;
}

if ($text === '➕ فعال‌سازی ربات در کانال من') {
    $user['state'] = 'awaiting_forward_for_activation';
    saveUser($user);
    sendMessage(
        $chatId,
        "برای فعال‌سازی ربات در کانال خودتون:\n\n" .
        "1) ربات رو به‌عنوان *ادمین* به کانالتون اضافه کنید (با دسترسی ارسال پیام).\n" .
        "2) یکی از پست‌های همون کانال رو از کانال به اینجا فوروارد کنید.\n\n" .
        "نکته: در خیلی از موارد، همین که دسترسی ارسال پیام رو به ربات بدید، فعال‌سازی به‌صورت خودکار انجام میشه و نیازی به فوروارد کردن نیست.",
        backKeyboard()
    );
    exit;
}

if ($text === '📋 کانال‌های من') {
    showOwnerChannels($chatId, $fromId);
    exit;
}

if (($user['state'] ?? '') === 'awaiting_forward_for_activation') {
    handleActivationForward($user, $message, $chatId);
    exit;
}

if (isAdmin($fromId) && ($user['state'] ?? '') === 'awaiting_broadcast') {
    handleBroadcastMessage($user, $chatId, $messageId);
    exit;
}

if ($text === '🗨 شروع چت کردن' || $text === 'شروع چت کردن') {
    handleStartChatButton($user, $chatId);
    exit;
}

if (($user['state'] ?? '') === 'chat') {
    handleChatForward($user, $chatId, $messageId);
    exit;
}

showMainMenu($chatId, $user);
