<?php
// Telegram: @Abolfazl_rwm
require_once __DIR__ . '/functions.php';

function showJoinRequiredMessage($chatId)
{
    $channelUsername = ltrim(REQUIRED_CHANNEL, '@');
    $joinUrl = 'https://t.me/' . $channelUsername;
    $text = "برای استفاده از این ربات، ابتدا باید عضو کانال زیر بشید:\n@{$channelUsername}\n\nبعد از عضویت، روی دکمه «✅ عضو شدم» بزنید.";
    $keyboard = json_encode(['inline_keyboard' => [
        [['text' => '📢 عضویت در کانال', 'url' => $joinUrl]],
        [['text' => '✅ عضو شدم', 'callback_data' => 'joincheck']],
    ]]);
    sendMessage($chatId, $text, $keyboard);
}

function handleStart($user, $payload, $chatId)
{
    if ($payload !== '') {
        $channel = getChannel($payload);
        if ($channel !== null) {
            $user['active_channel'] = $payload;
        } else {
            sendMessage($chatId, 'این لینک نامعتبره یا کانال موردنظر دیگه فعال نیست.');
        }
    }

    $user['state'] = 'none';
    saveUser($user);
    showMainMenu($chatId, $user);
}

function showMainMenu($chatId, $user)
{
    sendMessage(
        $chatId,
        "به ربات چت در کانال خوش آمدید 👋\nاز دکمه‌های زیر استفاده کنید:",
        mainMenuKeyboard($user)
    );
}

function handleStartChatButton($user, $chatId)
{
    if (empty($user['active_channel'])) {
        sendMessage(
            $chatId,
            "برای استفاده از این قابلیت، باید از طریق لینک اختصاصیِ یکی از کانال‌ها وارد ربات بشید.\nبدون این لینک، امکان ارسال پیام به هیچ کانالی وجود نداره.",
            mainMenuKeyboard($user)
        );
        return;
    }

    $channel = getChannel($user['active_channel']);
    if ($channel === null) {
        $user['active_channel'] = null;
        saveUser($user);
        sendMessage($chatId, 'کانال موردنظر دیگه در دسترس نیست.', mainMenuKeyboard($user));
        return;
    }

    if ($channel['status'] !== 'open') {
        sendMessage($chatId, 'در حال حاضر امکان ارسال پیام به این کانال وجود نداره (چت این کانال بسته شده است).', mainMenuKeyboard($user));
        return;
    }

    $channelLabel = !empty($channel['username']) ? '@' . mdEscape($channel['username']) : mdEscape($channel['title']);

    $user['state'] = 'chat';
    saveUser($user);
    sendMessage(
        $chatId,
        "لطفا متن خودرا در کانال ({$channelLabel}) ارسال کنید.\n❗مطالب غیراخلاقی توسط ادمین پاک میشوند!\n\nهر وقت کارتون تموم شد، روی «✅ پایان چت» بزنید.",
        endChatKeyboard()
    );
}

function handleChatForward($user, $chatId, $messageId)
{
    $channel = getChannel($user['active_channel'] ?? '');
    if ($channel === null || $channel['status'] !== 'open') {
        $user['state'] = 'none';
        saveUser($user);
        sendMessage($chatId, 'در حال حاضر امکان ارسال پیام وجود نداره.', mainMenuKeyboard($user));
        return;
    }

    $result = forwardMsg($channel['chat_id'], $chatId, $messageId);
    if (!$result || empty($result->ok)) {
        sendMessage($chatId, 'ارسال پیام با خطا مواجه شد. لطفا کمی بعد دوباره امتحان کنید.');
        return;
    }

    $channel['message_count'] = ($channel['message_count'] ?? 0) + 1;
    saveChannel($channel);

    sendMessage($chatId, "این پیام با موفقیت در کانال ارسال شد\nهم اکنون 2 ثانیه صبرکنید و سپس مطلب جدیدی ارسال کنید!");
    sleep(2);
    sendMessage($chatId, "هم اکنون میتوانید مطلب جدیدی ارسال کنید، یا با «✅ پایان چت» تمومش کنید!");
}

function handleActivationForward($user, $message, $chatId)
{
    $fwdChat = $message->forward_from_chat ?? null;

    if (!$fwdChat || ($fwdChat->type ?? '') !== 'channel') {
        sendMessage($chatId, 'لطفا یک پست از کانال موردنظر رو مستقیم از همون کانال به اینجا فوروارد کنید. برای انصراف «بازگشت» رو بزنید.', backKeyboard());
        return;
    }

    $channelChatId = $fwdChat->id;
    $existingKey = findChannelByChatId($channelChatId);
    if ($existingKey !== null) {
        $existing = getChannel($existingKey);
        $user['state'] = 'none';
        saveUser($user);
        if ((int) $existing['owner_id'] === (int) $user['id']) {
            sendMessage($chatId, 'این کانال قبلا برای شما فعال شده. از «📋 کانال‌های من» می‌تونید مدیریتش کنید.', mainMenuKeyboard($user));
        } else {
            sendMessage($chatId, 'این کانال قبلا توسط کاربر دیگری فعال شده است.', mainMenuKeyboard($user));
        }
        return;
    }

    $memberCheck = apiRequest('getChatMember', ['chat_id' => $channelChatId, 'user_id' => $user['id']]);
    if (!$memberCheck || empty($memberCheck->ok)) {
        sendMessage($chatId, 'نتونستم اطلاعات این کانال رو بگیرم. مطمئن شوید من (ربات) از قبل به کانال اضافه شدم، بعد دوباره پست رو فوروارد کنید.', backKeyboard());
        return;
    }

    $userStatus = $memberCheck->result->status ?? null;
    if (!in_array($userStatus, ['creator', 'administrator'], true)) {
        sendMessage($chatId, 'شما باید ادمین یا سازنده‌ی این کانال باشید تا بتونید فعالش کنید.', backKeyboard());
        return;
    }

    $botCheck = apiRequest('getChatMember', ['chat_id' => $channelChatId, 'user_id' => BOT_ID]);
    $botStatus = $botCheck->result->status ?? null;
    $botCanPost = $botCheck->result->can_post_messages ?? false;
    if ($botStatus !== 'administrator' || !$botCanPost) {
        sendMessage($chatId, 'من هنوز ادمین این کانال نیستم (یا دسترسی «ارسال پیام» رو ندارم). لطفا اول من رو با دسترسی ارسال پیام، ادمین کانال کنید و بعد دوباره پست رو فوروارد کنید.', backKeyboard());
        return;
    }

    $key = generateChannelKey();
    saveChannel([
        'key' => $key,
        'chat_id' => $channelChatId,
        'title' => $fwdChat->title ?? '',
        'username' => $fwdChat->username ?? null,
        'owner_id' => $user['id'],
        'status' => 'open',
        'created_at' => time(),
        'message_count' => 0,
    ]);
    addOwnerChannelKey($user['id'], $key);

    $user['state'] = 'none';
    saveUser($user);

    $link = 'https://t.me/' . BOT_USERNAME . '?start=' . $key;
    sendMessage(
        $chatId,
        "✅ کانال «" . mdEscape($fwdChat->title ?? '') . "» با موفقیت فعال شد!\n\nلینک اختصاصی چت این کانال:\n{$link}\n\nاین لینک رو در کانالتون به اشتراک بذارید.",
        mainMenuKeyboard($user)
    );
}

function handleMyChatMember($mcm)
{
    $chat = $mcm->chat ?? null;
    if (!$chat || ($chat->type ?? '') !== 'channel') {
        return;
    }

    $actorId = $mcm->from->id ?? null;
    $newStatus = $mcm->new_chat_member->status ?? '';
    if (!$actorId || $newStatus !== 'administrator') {
        return;
    }

    if (!isAdmin($actorId) && !isMemberOfRequiredChannel($actorId)) {
        showJoinRequiredMessage($actorId);
        return;
    }

    $canPost = $mcm->new_chat_member->can_post_messages ?? false;
    if (!$canPost) {
        sendMessage($actorId, "متوجه شدم که من رو در کانال «" . mdEscape($chat->title ?? '') . "» ادمین کردید، ولی دسترسی «ارسال پیام» فعال نیست. لطفا این دسترسی رو هم بدید تا کانال رو براتون فعال کنم.");
        return;
    }

    if (findChannelByChatId($chat->id) !== null) {
        return;
    }

    $key = generateChannelKey();
    saveChannel([
        'key' => $key,
        'chat_id' => $chat->id,
        'title' => $chat->title ?? '',
        'username' => $chat->username ?? null,
        'owner_id' => $actorId,
        'status' => 'open',
        'created_at' => time(),
        'message_count' => 0,
    ]);
    addOwnerChannelKey($actorId, $key);

    $link = 'https://t.me/' . BOT_USERNAME . '?start=' . $key;
    sendMessage(
        $actorId,
        "✅ ربات با موفقیت در کانال «" . mdEscape($chat->title ?? '') . "» فعال شد!\n\nلینک اختصاصی چت این کانال:\n{$link}\n\nاین لینک رو در کانالتون به اشتراک بذارید تا مخاطبان بتونن براتون پیام ارسال کنند."
    );
}

function showOwnerChannels($chatId, $ownerId)
{
    $keys = getOwnerChannelKeys($ownerId);
    if (empty($keys)) {
        sendMessage($chatId, 'شما هنوز هیچ کانالی فعال نکردید.');
        return;
    }

    $lines = [];
    $buttons = [];
    foreach ($keys as $key) {
        $channel = getChannel($key);
        if (!$channel) {
            continue;
        }
        $statusText = $channel['status'] === 'open' ? '🔓 باز' : '🔒 بسته';
        $link = 'https://t.me/' . BOT_USERNAME . '?start=' . $key;
        $lines[] = "📌 *" . mdEscape($channel['title']) . "*\nوضعیت: {$statusText} | پیام‌های ارسالی: " . ($channel['message_count'] ?? 0) . "\nلینک: {$link}";

        $toggleLabel = $channel['status'] === 'open' ? '🔒 بستن' : '🔓 باز کردن';
        $buttons[] = [
            ['text' => $toggleLabel, 'callback_data' => 'tgl:' . $key],
            ['text' => '🗑 حذف', 'callback_data' => 'delc:' . $key],
        ];
    }

    sendMessage($chatId, implode("\n\n", $lines), json_encode(['inline_keyboard' => $buttons]));
}

function showAllChannelsForAdmin($chatId)
{
    $keys = getAllChannelKeys();
    if (empty($keys)) {
        sendMessage($chatId, 'هنوز هیچ کانالی ثبت نشده.');
        return;
    }

    $lines = [];
    $buttons = [];
    foreach ($keys as $key) {
        $channel = getChannel($key);
        if (!$channel) {
            continue;
        }
        $statusText = $channel['status'] === 'open' ? '🔓 باز' : '🔒 بسته';
        $lines[] = "📌 *" . mdEscape($channel['title']) . "*\nمالک: `" . $channel['owner_id'] . "` | وضعیت: {$statusText} | پیام‌ها: " . ($channel['message_count'] ?? 0);

        $toggleLabel = $channel['status'] === 'open' ? '🔒 بستن' : '🔓 باز کردن';
        $buttons[] = [
            ['text' => $toggleLabel, 'callback_data' => 'tgl:' . $key],
            ['text' => '🗑 حذف', 'callback_data' => 'delc:' . $key],
        ];
    }

    sendMessage($chatId, implode("\n\n", $lines), json_encode(['inline_keyboard' => $buttons]));
}

function showAdminPanel($chatId)
{
    $userCount = count(getAllUserIds());
    $channelKeys = getAllChannelKeys();
    $channelCount = count($channelKeys);
    $openCount = 0;
    $totalMessages = 0;

    foreach ($channelKeys as $key) {
        $channel = getChannel($key);
        if (!$channel) {
            continue;
        }
        if ($channel['status'] === 'open') {
            $openCount++;
        }
        $totalMessages += $channel['message_count'] ?? 0;
    }
    $closedCount = $channelCount - $openCount;

    $text = "📊 *آمار ربات*\n\n" .
        "👤 تعداد کاربران: {$userCount}\n" .
        "📢 تعداد کانال‌های فعال‌شده: {$channelCount}\n" .
        "🔓 باز: {$openCount}  |  🔒 بسته: {$closedCount}\n" .
        "✉️ مجموع پیام‌های ارسال‌شده: {$totalMessages}";

    $keyboard = json_encode(['inline_keyboard' => [
        [['text' => '📢 ارسال پیام همگانی', 'callback_data' => 'admin_broadcast']],
        [['text' => '📋 مدیریت کانال‌ها', 'callback_data' => 'admin_channels']],
    ]]);

    sendMessage($chatId, $text, $keyboard);
}

function handleBroadcastMessage($user, $chatId, $messageId)
{
    $user['state'] = 'none';
    saveUser($user);

    $userIds = getAllUserIds();
    $success = 0;
    $failed = 0;

    foreach ($userIds as $uid) {
        if ((int) $uid === (int) $chatId) {
            continue;
        }
        $res = copyMsg($uid, $chatId, $messageId);
        if ($res && !empty($res->ok)) {
            $success++;
        } else {
            $failed++;
        }
        usleep(50000);
    }

    sendMessage($chatId, "📢 پیام همگانی ارسال شد.\n✅ موفق: {$success}\n❌ ناموفق: {$failed}");
}

function handleCallbackQuery($cq)
{
    $data = $cq->data ?? '';
    $fromId = $cq->from->id ?? null;
    $chatId = $cq->message->chat->id ?? $fromId;

    if ($fromId === null) {
        return;
    }

    if ($data === 'joincheck') {
        if (!isAdmin($fromId) && !isMemberOfRequiredChannel($fromId)) {
            answerCallback($cq->id, 'هنوز عضو کانال نشدید.', true);
            return;
        }
        answerCallback($cq->id, 'عضویت شما تایید شد ✅');
        $user = getOrCreateUser($fromId);
        showMainMenu($chatId, $user);
        return;
    }

    if (strpos($data, 'tgl:') === 0) {
        $key = substr($data, 4);
        $channel = getChannel($key);
        if (!$channel) {
            answerCallback($cq->id, 'این کانال دیگه وجود نداره.', true);
            return;
        }
        if ((int) $channel['owner_id'] !== (int) $fromId && !isAdmin($fromId)) {
            answerCallback($cq->id, 'شما اجازه این کار رو ندارید.', true);
            return;
        }

        $channel['status'] = $channel['status'] === 'open' ? 'closed' : 'open';
        saveChannel($channel);
        answerCallback($cq->id, $channel['status'] === 'open' ? 'چت این کانال باز شد.' : 'چت این کانال بسته شد.');

        if (isAdmin($fromId) && (int) $fromId !== (int) $channel['owner_id']) {
            showAllChannelsForAdmin($chatId);
        } else {
            showOwnerChannels($chatId, $channel['owner_id']);
        }
        return;
    }

    if (strpos($data, 'delc_yes:') === 0) {
        $key = substr($data, 9);
        $channel = getChannel($key);
        if (!$channel) {
            answerCallback($cq->id, 'این کانال قبلا حذف شده.', true);
            return;
        }
        if ((int) $channel['owner_id'] !== (int) $fromId && !isAdmin($fromId)) {
            answerCallback($cq->id, 'شما اجازه این کار رو ندارید.', true);
            return;
        }

        $ownerId = $channel['owner_id'];
        $title = $channel['title'];
        deleteChannel($key);
        answerCallback($cq->id, 'کانال حذف شد.');
        sendMessage($chatId, "کانال «" . mdEscape($title) . "» حذف شد.");

        if ((int) $fromId !== (int) $ownerId) {
            sendMessage($ownerId, "کانال «" . mdEscape($title) . "» شما توسط مدیریت حذف شد.");
        }

        if (isAdmin($fromId) && (int) $fromId !== (int) $ownerId) {
            showAllChannelsForAdmin($chatId);
        } else {
            showOwnerChannels($chatId, $ownerId);
        }
        return;
    }

    if (strpos($data, 'delc_no:') === 0) {
        $key = substr($data, 8);
        answerCallback($cq->id, 'حذف نشد.');
        $channel = getChannel($key);
        if ($channel) {
            if (isAdmin($fromId) && (int) $fromId !== (int) $channel['owner_id']) {
                showAllChannelsForAdmin($chatId);
            } else {
                showOwnerChannels($chatId, $channel['owner_id']);
            }
        }
        return;
    }

    if (strpos($data, 'delc:') === 0) {
        $key = substr($data, 5);
        $channel = getChannel($key);
        if (!$channel) {
            answerCallback($cq->id, 'این کانال دیگه وجود نداره.', true);
            return;
        }
        if ((int) $channel['owner_id'] !== (int) $fromId && !isAdmin($fromId)) {
            answerCallback($cq->id, 'شما اجازه این کار رو ندارید.', true);
            return;
        }

        answerCallback($cq->id);
        $confirmKeyboard = json_encode(['inline_keyboard' => [
            [['text' => '⚠️ بله، حذف کن', 'callback_data' => 'delc_yes:' . $key]],
            [['text' => 'انصراف', 'callback_data' => 'delc_no:' . $key]],
        ]]);
        sendMessage($chatId, "آیا مطمئنید میخواید کانال «" . mdEscape($channel['title']) . "» رو حذف کنید؟\nاین کار قابل بازگشت نیست و لینک اختصاصی این کانال از کار میفته.", $confirmKeyboard);
        return;
    }

    if ($data === 'admin_broadcast') {
        if (!isAdmin($fromId)) {
            answerCallback($cq->id, 'دسترسی ندارید.', true);
            return;
        }
        answerCallback($cq->id);
        $user = getOrCreateUser($fromId);
        $user['state'] = 'awaiting_broadcast';
        saveUser($user);
        sendMessage($chatId, 'پیام یا محتوایی که می‌خواید همگانی ارسال بشه رو بفرستید (متن، عکس، ویدیو و ...). برای انصراف «بازگشت» رو بزنید.', backKeyboard());
        return;
    }

    if ($data === 'admin_channels') {
        if (!isAdmin($fromId)) {
            answerCallback($cq->id, 'دسترسی ندارید.', true);
            return;
        }
        answerCallback($cq->id);
        showAllChannelsForAdmin($chatId);
        return;
    }

    answerCallback($cq->id);
}
