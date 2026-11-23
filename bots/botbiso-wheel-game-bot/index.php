<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
Developer: @Cristianoronal_d_o
Channel: @botbiso
*/
/*
Developer: @Cristianoronal_d_
Channel: @botbiso
*/
// تنظیمات اولیه
define('BOT_TOKEN', 'توکن');
define('ADMIN_ID', 0); //ایدی عددی ادمین
define('CHANNEL_LINK', 'ایدی چنل ');
define('DB_FILE', 'users.json');

// دریافت آپدیت
$update = json_decode(file_get_contents('php://input'), true);
if (!$update) exit; // خروج در صورت آپدیت نامعتبر
$chat_id = $update['message']['chat']['id'] ?? $update['callback_query']['from']['id'] ?? 0;
$user_id = $update['message']['from']['id'] ?? $update['callback_query']['from']['id'] ?? 0;
$message = $update['message']['text'] ?? '';
$callback_data = $update['callback_query']['data'] ?? '';
$message_id = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? 0;

// توابع کمکی
function sendMessage($chat_id, $text, $keyboard = null, $reply_to = null) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
    ];
    if ($keyboard) $params['reply_markup'] = json_encode($keyboard);
    if ($reply_to) $params['reply_to_message_id'] = $reply_to;
    $response = @file_get_contents($url . '?' . http_build_query($params));
    return $response ? json_decode($response, true) : false;
}

function deleteMessage($chat_id, $message_id) {
    if ($message_id) {
        $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/deleteMessage";
        @file_get_contents($url . '?' . http_build_query(['chat_id' => $chat_id, 'message_id' => $message_id]));
    }
}

function loadUsers() {
    if (file_exists(DB_FILE) && is_readable(DB_FILE)) {
        $data = @file_get_contents(DB_FILE);
        return $data ? json_decode($data, true) : [];
    }
    return [];
}

function saveUsers($users) {
    if (is_writable(dirname(DB_FILE)) || (file_exists(DB_FILE) && is_writable(DB_FILE))) {
        file_put_contents(DB_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

function checkChannelMembership($chat_id) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getChatMember?chat_id=@" . basename(CHANNEL_LINK) . "&user_id=$chat_id";
    $result = @file_get_contents($url);
    if ($result) {
        $result = json_decode($result, true);
        return in_array($result['result']['status'] ?? '', ['member', 'administrator', 'creator']);
    }
    return false;
}

// منوی اصلی
function showMainMenu($chat_id, $user_id) {
    $keyboard = ['inline_keyboard' => [
        [['text' => '🎡 چرخوندن گردونه', 'callback_data' => 'spin']],
        [['text' => '👥 زیرمجموعه‌گیری', 'callback_data' => 'referral']],
        [['text' => '💰 سکه‌های من', 'callback_data' => 'balance']],
    ]];
    if ($user_id == ADMIN_ID) {
        $keyboard['inline_keyboard'][] = [['text' => '🛠 پنل مدیریت', 'callback_data' => 'admin']];
    }
    sendMessage($chat_id, "🎉 به ربات گردونه خوش اومدی!\nچه کاری برات انجام بدم؟", $keyboard);
}

// لود و مقداردهی اولیه دیتابیس
$users = loadUsers();
if (!isset($users[$user_id])) {
    $users[$user_id] = [
        'coins' => 0,
        'referrals' => [],
        'wheel_names' => [],
        'state' => '',
        'spinning_message_id' => 0,
        'spin_rounds' => 0,
    ];
    saveUsers($users);
}

// دستورات
if ($message == '/start') {
    $users[$user_id]['state'] = ''; // ریست حالت
    saveUsers($users);
    showMainMenu($chat_id, $user_id);
} elseif (preg_match('/\/start ref_(\d+)/', $message, $matches)) {
    $referrer_id = $matches[1];
    if ($referrer_id != $user_id && !in_array($user_id, $users[$referrer_id]['referrals'] ?? [])) {
        $users[$referrer_id]['coins'] = ($users[$referrer_id]['coins'] ?? 0) + 1;
        $users[$referrer_id]['referrals'][] = $user_id;
        saveUsers($users);
        sendMessage($referrer_id, "🎉 یه زیرمجموعه جدید گرفتی و ۱ سکه به حسابت اضافه شد!");
        sendMessage($chat_id, "✅ از طریق زیرمجموعه‌گیری وارد شدی!");
    }
    $users[$user_id]['state'] = '';
    saveUsers($users);
    showMainMenu($chat_id, $user_id);
} elseif ($message && $users[$user_id]['state']) {
    if ($users[$user_id]['state'] == 'awaiting_names') {
        $names = array_filter(array_map('trim', preg_split('/[,،]/u', $message)));
        if (count($names) < 2) {
            $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
            sendMessage($chat_id, "❌ حداقل ۲ اسم وارد کن (با کاما جدا کن، مثال: علی,مریم,حسن یا علی،مریم،حسن)", $keyboard);
            return;
        }
        $users[$user_id]['wheel_names'] = $names;
        $users[$user_id]['state'] = 'awaiting_rounds';
        saveUsers($users);
        $keyboard = ['inline_keyboard' => [
            [['text' => '۱ دور', 'callback_data' => 'rounds_1'], ['text' => '۲ دور', 'callback_data' => 'rounds_2']],
            [['text' => '۳ دور', 'callback_data' => 'rounds_3'], ['text' => '۴ دور', 'callback_data' => 'rounds_4']],
            [['text' => '۵ دور', 'callback_data' => 'rounds_5']],
            [['text' => '🔙 برگشت', 'callback_data' => 'back']],
        ]];
        sendMessage($chat_id, "🎡 چند دور می‌خوای گردونه بچرخه؟", $keyboard);
    } elseif ($user_id == ADMIN_ID) {
        if ($users[$user_id]['state'] == 'awaiting_broadcast') {
            foreach ($users as $uid => $data) {
                if ($uid != $user_id) {
                    sendMessage($uid, "📢 پیام از ادمین:\n$message");
                }
            }
            sendMessage($chat_id, "✅ پیام همگانی به همه کاربرا ارسال شد!");
            $users[$user_id]['state'] = '';
            saveUsers($users);
            showMainMenu($chat_id, $user_id);
        } elseif ($users[$user_id]['state'] == 'awaiting_give_coins') {
            if (preg_match('/(\d+)\s+(\d+)/', $message, $matches)) {
                $target_id = $matches[1];
                $coins = (int)$matches[2];
                if (isset($users[$target_id])) {
                    $users[$target_id]['coins'] = ($users[$target_id]['coins'] ?? 0) + $coins;
                    saveUsers($users);
                    sendMessage($chat_id, "🎁 $coins سکه به کاربر $target_id اهدا شد!");
                    sendMessage($target_id, "🎉 $coins سکه از طرف ادمین به حسابت اضافه شد!");
                } else {
                    sendMessage($chat_id, "🚫 کاربر با آیدی $target_id پیدا نشد!");
                }
            } else {
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "❌ فرمت اشتباه! مثال: 123456 10", $keyboard);
            }
            $users[$user_id]['state'] = '';
            saveUsers($users);
            showMainMenu($chat_id, $user_id);
        } elseif ($users[$user_id]['state'] == 'awaiting_take_coins') {
            if (preg_match('/(\d+)\s+(\d+)/', $message, $matches)) {
                $target_id = $matches[1];
                $coins = (int)$matches[2];
                if (isset($users[$target_id]) && $users[$target_id]['coins'] >= $coins) {
                    $users[$target_id]['coins'] -= $coins;
                    saveUsers($users);
                    sendMessage($chat_id, "➖ $coins سکه از کاربر $target_id کسر شد!");


                    sendMessage($target_id, "⚠️ $coins سکه از حسابت کسر شد!");
                } else {
                    sendMessage($chat_id, "🚫 کاربر $target_id وجود نداره یا سکه کافی نداره!");
                }
            } else {
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "❌ فرمت اشتباه! مثال: 123456 10", $keyboard);
            }
            $users[$user_id]['state'] = '';
            saveUsers($users);
            showMainMenu($chat_id, $user_id);
        }
    }
} elseif ($callback_data) {
    switch ($callback_data) {
        case 'back':
            $users[$user_id]['state'] = '';
            $users[$user_id]['wheel_names'] = [];
            $users[$user_id]['spinning_message_id'] = 0;
            $users[$user_id]['spin_rounds'] = 0;
            saveUsers($users);
            showMainMenu($chat_id, $user_id);
            break;
        case 'check_membership':
            if (checkChannelMembership($chat_id)) {
                showMainMenu($chat_id, $user_id);
            } else {
                $keyboard = ['inline_keyboard' => [
                    [['text' => '➕ عضویت در کانال', 'url' => CHANNEL_LINK]],
                    [['text' => '✅ بررسی عضویت', 'callback_data' => 'check_membership']],
                    [['text' => '🔙 برگشت', 'callback_data' => 'back']],
                ]];
                sendMessage($chat_id, "هنوز تو کانال عضو نشدی! 😕", $keyboard);
            }
            break;
        case 'spin':
            if (($users[$user_id]['coins'] ?? 0) >= 1) {
                $users[$user_id]['state'] = 'awaiting_names';
                saveUsers($users);
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "🎡 اسامی رو با کاما وارد کن (مثال: علی,مریم,حسن یا علی،مریم،حسن):", $keyboard);
            } else {
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "� ❌ سکه کافی نداری! زیرمجموعه بگیر یا سکه بخر.", $keyboard);
            }
            break;
        case 'referral':
            $ref_link = "https://t.me/Bhkwkjejsjdbot?start=ref_$user_id";
            $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
            sendMessage($chat_id, "🔗 لینک زیرمجموعه‌گیری تو:\n$ref_link\nهر زیرمجموعه = ۱ سکه!", $keyboard);
            break;
        case 'balance':
            $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
            sendMessage($chat_id, "💰 موجودی سکه‌های تو: " . ($users[$user_id]['coins'] ?? 0), $keyboard);
            break;
        case 'admin':
            if ($user_id == ADMIN_ID) {
                $keyboard = ['inline_keyboard' => [
                    [['text' => '📢 پیام همگانی', 'callback_data' => 'broadcast']],
                    [['text' => '🎁 اهدای سکه', 'callback_data' => 'give_coins']],
                    [['text' => '➖ کسر سکه', 'callback_data' => 'take_coins']],
                    [['text' => '📊 آمار ربات', 'callback_data' => 'stats']],
                    [['text' => '🔙 برگشت', 'callback_data' => 'back']],
                ]];
                sendMessage($chat_id, "🛠 پنل مدیریت:\nچه کاری می‌خوای انجام بدی؟", $keyboard);
            } else {
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "🚫 تو ادمین نیستی!", $keyboard);
            }
            break;
        case 'broadcast':
            if ($user_id == ADMIN_ID) {
                $users[$user_id]['state'] = 'awaiting_broadcast';
                saveUsers($users);
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "📢 متن پیام همگانی رو بنویس:", $keyboard);
            }
            break;
        case 'give_coins':
            if ($user_id == ADMIN_ID) {
                $users[$user_id]['state'] = 'awaiting_give_coins';
                saveUsers($users);
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "🎁 آیدی عددی کاربر و تعداد سکه رو بنویس (مثال: 123456 10):", $keyboard);
            }
            break;
        case 'take_coins':
            if ($user_id == ADMIN_ID) {
                $users[$user_id]['state'] = 'awaiting_take_coins';
                saveUsers($users);
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "➖ آیدی عددی کاربر و تعداد سکه رو بنویس (مثال: 123456 10):", $keyboard);
            }
            break;
        case 'stats':
            if ($user_id == ADMIN_ID) {
                $total_users = count($users);
                $total_coins = array_sum(array_map(fn($u) => $u['coins'] ?? 0, $users));
                $total_referrals = array_sum(array_map(fn($u) => count($u['referrals'] ?? []), $users));
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "📊 آمار ربات:\nکاربرا: $total_users\nسکه‌های کل: $total_coins\nزیرمجموعه‌ها: $total_referrals", $keyboard);
            }
            break;
        case 'rounds_1':
        case 'rounds_2':
        case 'rounds_3':
        case 'rounds_4':
        case 'rounds_5':
            if (($users[$user_id]['coins'] ?? 0) >= 1 && !empty($users[$user_id]['wheel_names'])) {
                $rounds = (int)substr($callback_data, 7);
                $users[$user_id]['spin_rounds'] = $rounds;
                $users[$user_id]['coins'] -= 1;
                saveUsers($users);
                $spinning_message = sendMessage($chat_id, "🎡 در حال چرخاندن گردونه ($rounds دور)...");
                $users[$user_id]['spinning_message_id'] = $spinning_message['result']['message_id'] ?? 0;
                saveUsers($users);
                sleep(2); // تاخیر برای حس چرخش
                $names = $users[$user_id]['wheel_names'];
                $winner = trim($names[array_rand($names)]);
                if ($users[$user_id]['spinning_message_id']) {
                    deleteMessage($chat_id, $users[$user_id]['spinning_message_id']);
                }
                $keyboard = ['inline_keyboard' => [[['text' => '🔙 برگشت', 'callback_data' => 'back']]]];
                sendMessage($chat_id, "🏆 نتیجه چرخش ($rounds دور):\n🎉 برنده: <b>$winner</b>", $keyboard);
                $users[$user_id]['wheel_names'] = [];
                $users[$user_id]['state'] = '';
                $users[$user_id]['spinning_message_id'] = 0;
                $users[$user_id]['spin_rounds'] = 0;
                saveUsers($users);
            }
            break;
    }
}

// بررسی عضویت در کانال
if ($chat_id && !checkChannelMembership($chat_id)) {
    $keyboard = [
        'inline_keyboard' => [
            [['text' => '➕ عضویت در کانال', 'url' => CHANNEL_LINK]],
            [['text' => '✅ بررسی عضویت', 'callback_data' => 'check_membership']],
            [['text' => '🔙 برگشت', 'callback_data' => 'back']],
        ]
    ];
    sendMessage($chat_id, "لطفاً اول در کانال ما عضو شو!\n" . CHANNEL_LINK, $keyboard);
    exit;
}
/*
Developer: @Cristianoronal_d_o
Channel: @botbiso
*/
/*
Developer: @Cristianoronal_d_o
Channel: @botbiso
*/
?>