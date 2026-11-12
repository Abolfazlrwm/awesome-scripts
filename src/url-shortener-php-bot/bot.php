<?php
// Telegram: @Abolfazl_rwm
define('BOT_TOKEN', getenv('BOT_TOKEN') ?: 'توکن ربات'); //توکن ربات 
define('ADMIN_ID', 123456789); //آیدی عددی ادمین 

$db = new SQLite3('bot.db');
$db->exec("CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY, 
    blocked INTEGER DEFAULT 0, 
    last_message_time REAL, 
    message_count INTEGER DEFAULT 0, 
    block_until REAL DEFAULT 0
)");
$db->exec("CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY, 
    value TEXT
)");
$db->exec("CREATE TABLE IF NOT EXISTS messages (
    user_id INTEGER, 
    message TEXT, 
    timestamp REAL
)");

$db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES ('start_message', 'خوش آمدید! لینک خود را ارسال کنید تا کوتاه شود.')");
$db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES ('help_message', 'لطفاً یک لینک معتبر ارسال کنید تا کوتاه شود.')");
$db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES ('force_join', '0')");
$db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES ('channel_id', '')");

function shorten_url($url) {
    try {
        $response = file_get_contents("http://tinyurl.com/api-create.php?url=" . urlencode($url));
        if ($response !== false) {
            return $response;
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}

function check_channel_membership($user_id) {
    global $db;
    $result = $db->querySingle("SELECT value FROM settings WHERE key='force_join'");
    $force_join = ($result == '1');
    if (!$force_join) {
        return true;
    }
    $channel_id = $db->querySingle("SELECT value FROM settings WHERE key='channel_id'");
    if (!$channel_id) {
        return true;
    }
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/getChatMember?chat_id=" . urlencode($channel_id) . "&user_id=" . $user_id;
    $response = file_get_contents($url);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    if ($data['ok'] && isset($data['result']['status'])) {
        $status = $data['result']['status'];
        return in_array($status, ['member', 'administrator', 'creator']);
    }
    return false;
}

function check_rate_limit($user_id) {
    global $db;
    $stmt = $db->prepare("SELECT last_message_time, message_count, block_until FROM users WHERE user_id = ?");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $current_time = time();

    if ($row) {
        $last_time = $row['last_message_time'];
        $msg_count = $row['message_count'];
        $block_until = $row['block_until'];
        
        if ($block_until > $current_time) {
            return ['allowed' => false, 'message' => "شما تا " . intval($block_until - $current_time) . " ثانیه دیگر مسدود هستید."];
        }
        
        if ($last_time && ($current_time - $last_time) < 30) {
            if ($msg_count >= 3) {
                $block_until = $current_time + 300;
                $stmt = $db->prepare("UPDATE users SET block_until = ? WHERE user_id = ?");
                $stmt->bindValue(1, $block_until, SQLITE3_FLOAT);
                $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
                $stmt->execute();
                return ['allowed' => false, 'message' => "شما بیش از حد پیام ارسال کردید. ۵ دقیقه مسدود شدید."];
            }
            $stmt = $db->prepare("UPDATE users SET message_count = ? WHERE user_id = ?");
            $stmt->bindValue(1, $msg_count + 1, SQLITE3_INTEGER);
            $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
            $stmt->execute();
        } else {
            $stmt = $db->prepare("UPDATE users SET message_count = 1, last_message_time = ? WHERE user_id = ?");
            $stmt->bindValue(1, $current_time, SQLITE3_FLOAT);
            $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
            $stmt->execute();
        }
    } else {
        $stmt = $db->prepare("INSERT INTO users (user_id, last_message_time, message_count) VALUES (?, ?, 1)");
        $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(2, $current_time, SQLITE3_FLOAT);
        $stmt->execute();
    }
    return ['allowed' => true, 'message' => ""];
}

function sendMessage($chat_id, $text, $reply_markup = null) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($reply_markup) {
        $data['reply_markup'] = $reply_markup;
    }
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ]
    ];
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

function getWebhookUpdate() {
    $content = file_get_contents('php://input');
    return json_decode($content, true);
}

function handleStart($chat_id, $user_id) {
    global $db;
    if (!check_channel_membership($user_id)) {
        $channel_id = $db->querySingle("SELECT value FROM settings WHERE key='channel_id'");
        sendMessage($chat_id, "لطفاً ابتدا در کانال {$channel_id} عضو شوید.");
        return;
    }
    $start_message = $db->querySingle("SELECT value FROM settings WHERE key='start_message'");
    sendMessage($chat_id, $start_message);
}

function handleHelp($chat_id, $user_id) {
    global $db;
    if (!check_channel_membership($user_id)) {
        $channel_id = $db->querySingle("SELECT value FROM settings WHERE key='channel_id'");
        sendMessage($chat_id, "لطفاً ابتدا در کانال {$channel_id} عضو شوید.");
        return;
    }
    $help_message = $db->querySingle("SELECT value FROM settings WHERE key='help_message'");
    sendMessage($chat_id, $help_message);
}

function handleAdminPanel($chat_id, $user_id) {
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی به پنل مدیریت ندارید.");
        return;
    }
    $commands = "/set_force_join <channel_id> - تنظیم جوین اجباری\n/remove_force_join - حذف جوین اجباری\n/set_start <message> - تغییر متن استارت\n/set_help <message> - تغییر متن راهنما\n/ban <user_id> - مسدود کردن کاربر\n/unban <user_id> - لغو مسدودیت کاربر\n/broadcast <message> - ارسال پیام همگانی\n/send <user_id> <message> - ارسال پیام تکی";
    sendMessage($chat_id, "دستورات پنل مدیریت:\n" . $commands);
}

function handleSetForceJoin($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً آیدی کانال را وارد کنید (مثال: /set_force_join @Abolfazl_rwm)");
        return;
    }
    $channel_id = $args[0];
    $db->exec("UPDATE settings SET value='1' WHERE key='force_join'");
    $stmt = $db->prepare("UPDATE settings SET value=? WHERE key='channel_id'");
    $stmt->bindValue(1, $channel_id, SQLITE3_TEXT);
    $stmt->execute();
    sendMessage($chat_id, "جوین اجباری برای کانال {$channel_id} فعال شد.");
}

function handleRemoveForceJoin($chat_id, $user_id) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    $db->exec("UPDATE settings SET value='0' WHERE key='force_join'");
    $db->exec("UPDATE settings SET value='' WHERE key='channel_id'");
    sendMessage($chat_id, "جوین اجباری غیرفعال شد.");
}

function handleSetStart($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً متن جدید را وارد کنید.");
        return;
    }
    $new_message = implode(" ", $args);
    $stmt = $db->prepare("UPDATE settings SET value=? WHERE key='start_message'");
    $stmt->bindValue(1, $new_message, SQLITE3_TEXT);
    $stmt->execute();
    sendMessage($chat_id, "متن استارت تغییر کرد.");
}

function handleSetHelp($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً متن جدید را وارد کنید.");
        return;
    }
    $new_message = implode(" ", $args);
    $stmt = $db->prepare("UPDATE settings SET value=? WHERE key='help_message'");
    $stmt->bindValue(1, $new_message, SQLITE3_TEXT);
    $stmt->execute();
    sendMessage($chat_id, "متن راهنما تغییر کرد.");
}

function handleBan($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً آیدی کاربر را وارد کنید.");
        return;
    }
    try {
        $target_user = intval($args[0]);
        $stmt = $db->prepare("UPDATE users SET blocked=1 WHERE user_id=?");
        $stmt->bindValue(1, $target_user, SQLITE3_INTEGER);
        $stmt->execute();
        if ($db->changes() == 0) {
            $stmt = $db->prepare("INSERT INTO users (user_id, blocked) VALUES (?, 1)");
            $stmt->bindValue(1, $target_user, SQLITE3_INTEGER);
            $stmt->execute();
        }
        sendMessage($chat_id, "کاربر {$target_user} مسدود شد.");
    } catch (Exception $e) {
        sendMessage($chat_id, "لطفاً یک آیدی معتبر وارد کنید.");
    }
}

function handleUnban($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً آیدی کاربر را وارد کنید.");
        return;
    }
    try {
        $target_user = intval($args[0]);
        $stmt = $db->prepare("UPDATE users SET blocked=0, block_until=0 WHERE user_id=?");
        $stmt->bindValue(1, $target_user, SQLITE3_INTEGER);
        $stmt->execute();
        sendMessage($chat_id, "مسدودیت کاربر {$target_user} لغو شد.");
    } catch (Exception $e) {
        sendMessage($chat_id, "لطفاً یک آیدی معتبر وارد کنید.");
    }
}

function handleBroadcast($chat_id, $user_id, $args) {
    global $db;
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (empty($args)) {
        sendMessage($chat_id, "لطفاً متن پیام را وارد کنید.");
        return;
    }
    $message = implode(" ", $args);
    $result = $db->query("SELECT user_id FROM users");
    $users = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $row['user_id'];
    }
    foreach ($users as $user) {
        try {
            sendMessage($user, $message);
        } catch (Exception $e) {
            continue;
        }
    }
    sendMessage($chat_id, "پیام همگانی ارسال شد.");
}

function handleSend($chat_id, $user_id, $args) {
    if ($user_id != ADMIN_ID) {
        sendMessage($chat_id, "شما دسترسی ندارید.");
        return;
    }
    if (count($args) < 2) {
        sendMessage($chat_id, "لطفاً آیدی کاربر و متن پیام را وارد کنید (مثال: /send 123456789 متن پیام)");
        return;
    }
    try {
        $target_user = intval($args[0]);
        $message = implode(" ", array_slice($args, 1));
        sendMessage($target_user, $message);
        sendMessage($chat_id, "پیام به کاربر {$target_user} ارسال شد.");
    } catch (Exception $e) {
        sendMessage($chat_id, "لطفاً یک آیدی معتبر و متن پیام وارد کنید.");
    }
}

function handleSupport($chat_id, $user_id, $text) {
    global $db;
    $message = substr($text, 8);
    if (empty(trim($message))) {
        sendMessage($chat_id, "لطفاً پیام خود را بعد از /support وارد کنید.");
        return;
    }
    $stmt = $db->prepare("INSERT INTO messages (user_id, message, timestamp) VALUES (?, ?, ?)");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $message, SQLITE3_TEXT);
    $stmt->bindValue(3, time(), SQLITE3_FLOAT);
    $stmt->execute();
    sendMessage(ADMIN_ID, "پیام از {$user_id}:\n{$message}");
    sendMessage($chat_id, "پیام شما به پشتیبانی ارسال شد.");
}

function handleMessage($chat_id, $user_id, $text) {
    $rate_limit = check_rate_limit($user_id);
    if (!$rate_limit['allowed']) {
        sendMessage($chat_id, $rate_limit['message']);
        return;
    }
    
    if (!check_channel_membership($user_id)) {
        global $db;
        $channel_id = $db->querySingle("SELECT value FROM settings WHERE key='channel_id'");
        sendMessage($chat_id, "لطفاً ابتدا در کانال {$channel_id} عضو شوید.");
        return;
    }
    
    if (strpos($text, '/support') === 0) {
        handleSupport($chat_id, $user_id, $text);
        return;
    }
    
    $shortened_url = shorten_url($text);
    if ($shortened_url) {
        sendMessage($chat_id, "لینک کوتاه شده: {$shortened_url}");
    } else {
        sendMessage($chat_id, "لطفاً یک لینک معتبر وارد کنید.");
    }
}

function handleCommand($chat_id, $user_id, $text) {
    $parts = explode(' ', $text);
    $command = $parts[0];
    $args = array_slice($parts, 1);
    
    switch ($command) {
        case '/start':
            handleStart($chat_id, $user_id);
            break;
        case '/help':
            handleHelp($chat_id, $user_id);
            break;
        case '/admin':
            handleAdminPanel($chat_id, $user_id);
            break;
        case '/set_force_join':
            handleSetForceJoin($chat_id, $user_id, $args);
            break;
        case '/remove_force_join':
            handleRemoveForceJoin($chat_id, $user_id);
            break;
        case '/set_start':
            handleSetStart($chat_id, $user_id, $args);
            break;
        case '/set_help':
            handleSetHelp($chat_id, $user_id, $args);
            break;
        case '/ban':
            handleBan($chat_id, $user_id, $args);
            break;
        case '/unban':
            handleUnban($chat_id, $user_id, $args);
            break;
        case '/broadcast':
            handleBroadcast($chat_id, $user_id, $args);
            break;
        case '/send':
            handleSend($chat_id, $user_id, $args);
            break;
        default:
            if (strpos($text, '/') === 0) {
                sendMessage($chat_id, "دستور نامعتبر است.");
            } else {
                handleMessage($chat_id, $user_id, $text);
            }
    }
}

function setWebhook() {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setWebhook?url=" . urlencode('https://your-domain.com/bot.php');
    $response = file_get_contents($url);
    return json_decode($response, true);
}

$update = getWebhookUpdate();
if ($update && isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $user_id = $message['from']['id'];
    $text = isset($message['text']) ? $message['text'] : '';
    
    if (!empty($text)) {
        if (strpos($text, '/') === 0) {
            handleCommand($chat_id, $user_id, $text);
        } else {
            handleMessage($chat_id, $user_id, $text);
        }
    }
}
?>
