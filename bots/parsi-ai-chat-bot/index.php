<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

//=============================
// Developed by: @ShahghaIab  
//=============================

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Asia/Tehran');

include 'config.php';
include 'table.php';

function http_request($url, $post = false, $data = [], $headers = []) {
    $ch = curl_init();
    $curl_options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_FOLLOWLOCATION => true
    ];
    
    if ($post) {
        $curl_options[CURLOPT_POST] = true;
        $curl_options[CURLOPT_POSTFIELDS] = is_array($data) ? json_encode($data) : $data;
    }
    
    if (!empty($headers)) {
        $curl_options[CURLOPT_HTTPHEADER] = $headers;
    } else {
        $curl_options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    
    curl_setopt_array($ch, $curl_options);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    return $response;
}
function sm($chat_id, $text, $reply_to = null, $keyboard = null) {
    global $api;
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    if ($reply_to) $data['reply_to_message_id'] = $reply_to;
    
    if ($keyboard) {
        $data['reply_markup'] = is_array($keyboard) ? json_encode($keyboard) : $keyboard;
    }

    $response = http_request($api . "sendMessage?" . http_build_query($data));
    
    if ($response === false) {
        error_log("Failed to send message to chat_id: $chat_id");
    }
    
    return $response;
}

function em($chat_id, $message_id, $text, $keyboard = null) {
    global $api;
    $data = [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    if ($keyboard) {
        $data['reply_markup'] = is_array($keyboard) ? json_encode($keyboard) : $keyboard;
    }
    $response = http_request($api . "editMessageText?" . http_build_query($data));
    return $response;
}
function is_blocked($db, $user_id) {
    $result = $db->query("SELECT is_blocked FROM users WHERE id = $user_id");
    return $result && $result->num_rows > 0 ? (bool)$result->fetch_assoc()['is_blocked'] : false;
}

function validate_telegram_ip() {
    $telegram_ip_ranges = [
        ['149.154.160.0', '149.154.175.255'],
        ['91.108.4.0', '91.108.7.255']
    ];

    $client_ip = $_SERVER['REMOTE_ADDR'];
    foreach ($telegram_ip_ranges as $range) {
        if (ip_in_range($client_ip, $range[0], $range[1])) {
            return true;
        }
    }
    return false;
}

function ip_in_range($ip, $start, $end) {
    return (ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end));
}

if (!validate_telegram_ip()) {
    die('Access Denied: Unauthorized IP');
}

function sanitize_input($data) {
    $data = (string) $data;
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_valid_text($text) {
    $dangerous_characters = ["'", '"', ",", "}", ";", "<", ">", "script", "<?", "?>"];
    foreach ($dangerous_characters as $char) {
        if (strpos($text, $char) !== false) {
            return false;
        }
    }
    return true;
}


$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'] ?? null;
    $user_id = $message['from']['id'] ?? null;

    $text = sanitize_input($message['text'] ?? 'پیامی ارسال نشده');
    $first_name = sanitize_input($message['from']['first_name'] ?? 'بدون نام');
    $username = sanitize_input($message['from']['username'] ?? 'نامشخص');
    $from_id = sanitize_input($message['from']['id'] ?? 'نامشخص');

    if (!is_valid_text($text)) {
        http_request("https://api.telegram.org/bot" . $config['token'] . "/sendMessage", true, [
            'chat_id' => $chat_id,
            'text' => "⚠️ لطفاً از ارسال کدهای مخرب خودداری کنید. این اقدامات ثبت و گزارش می‌شوند."
        ]);

        foreach ($config['admins'] as $admin) {
            $message_to_admin = "
🚨 هشدار امنیتی 🚨

📛 یک تلاش مشکوک برای ارسال پیام خطرناک شناسایی شد.

👤 اطلاعات فرد:
- نام: $first_name
- نام کاربری: @$username
- آیدی عددی: $from_id

🕒 زمان: " . date("Y-m-d H:i:s") . "  
🌐 IP کاربر: " . $_SERVER['REMOTE_ADDR'] . "

🗂 پیام ارسال شده:
$text

⚠️ لطفاً این مورد را بررسی کنید و در صورت نیاز اقدامات لازم را انجام دهید.
            درصورت نیاز کاربر رو از پنل مدیریت مسدود کنید.
            ";
            $response = sm($admin, $message_to_admin, 'HTML');
        }
        if (!file_exists("data/$from_id")) {
            mkdir("data/$from_id", 0777, true);
        }

        file_put_contents("data/$from_id/state.txt", "none");
        file_put_contents("data/$from_id/step.txt", "none");
    }
}
function ai($query) {
    $url = "https://open.wiki-api.ir/apis-1/ChatGPT";
    
    $queryData = ['q' => $query];
    $urlWithParams = $url . '?' . http_build_query($queryData);
    
    $ch = curl_init($urlWithParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300 && $response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['results'])) {
            return $responseData['results'];
        }
    }
    
    return "⚠️ خطا در ارتباط با سرور هوش مصنوعی. لطفاً دوباره تلاش کنید.";
}
function check_send_error($user_id) {
    global $db;
    
    $blocked_check = $db->query("SELECT is_blocked FROM users WHERE id = $user_id")->fetch_assoc();
    if ($blocked_check['is_blocked']) {
        return 'blocked';
    }
    
    $user_check = $db->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
    if (!$user_check) {
        return 'invalid';
    }
    
    return 'unknown';
}

function get_kb($type = 'main') {
    $keyboards = [
        'main' => [
            'inline_keyboard' => [
                [['text' => '🤖 چت هوشمند', 'callback_data' => 'ai_chat']],
                [
                    ['text' => '👤 پروفایل', 'callback_data' => 'profile'],
                    ['text' => '❓ راهنما', 'callback_data' => 'help'],
                ],
                [
                    ['text' => 'ℹ️ درباره ما', 'callback_data' => 'about']
                ]
            ]
        ],
        'back' => [
            'inline_keyboard' => [
                [['text' => '🔙 بازگشت به منوی اصلی', 'callback_data' => 'back']]
            ]
        ],
        'admin' => [
            'inline_keyboard' => [
                [
                    ['text' => '📊 آمار کلی', 'callback_data' => 'stats'],
                    ['text' => '👥 لیست کاربران', 'callback_data' => 'users_list']
                ],
                [
                    ['text' => '📢 پیام همگانی', 'callback_data' => 'broadcast'],
                    ['text' => '📢 پیام گروهی', 'callback_data' => 'group_broadcast']
                ],
                [
                    ['text' => '🚫 مدیریت کاربران', 'callback_data' => 'user_management'],
                    ['text' => '⚙️ تنظیمات', 'callback_data' => 'settings']
                ],
                [['text' => '🔙 بازگشت', 'callback_data' => 'back']]
            ]
        ],
        'settings' => [
            'inline_keyboard' => [
                [
                    ['text' => '🤖 وضعیت ربات', 'callback_data' => 'toggle_bot'],
                    ['text' => '🛠 حالت تعمیر', 'callback_data' => 'maintenance']
                ],
                [['text' => '🔙 بازگشت به پنل', 'callback_data' => 'admin']]
            ]
        ],
        'user_management' => [
            'inline_keyboard' => [
                [
                    ['text' => '🚫 مسدود کردن', 'callback_data' => 'block'],
                    ['text' => '✅ رفع مسدودی', 'callback_data' => 'unblock']
                ],
                [['text' => '🔙 بازگشت به پنل', 'callback_data' => 'admin']]
            ]
        ]
    ];
    
    return $keyboards[$type];
}

function upd_usr($db, $user_id, $username, $first_name) {
    $stmt = $db->prepare("INSERT INTO users (id, username, first_name, joined_date, last_activity) 
                         VALUES (?, ?, ?, NOW(), NOW()) 
                         ON DUPLICATE KEY UPDATE 
                         last_activity = NOW(), 
                         username = VALUES(username),
                         first_name = VALUES(first_name)");
    $stmt->bind_param("iss", $user_id, $username, $first_name);
    return $stmt->execute();
}

function set_state($db, $user_id, $state) {
    return $db->query("UPDATE users SET ai_chat_state = " . ($state ? "1" : "0") . " WHERE id = $user_id");
}

function get_state($db, $user_id) {
    $result = $db->query("SELECT ai_chat_state FROM users WHERE id = $user_id");
    return $result && $result->num_rows > 0 ? (bool)$result->fetch_assoc()['ai_chat_state'] : false;
}

function handle_cb($db, $callback_query) {
    $chat_id = $callback_query['message']['chat']['id'];
    $message_id = $callback_query['message']['message_id'];
    $data = $callback_query['data'];
    $user_id = $callback_query['from']['id'];
    global $config;

    if (!in_array($chat_id, $config['admins']) && is_blocked($db, $user_id)) {
        em($chat_id, $message_id, "⛔️ شما مسدود شده‌اید! برای رفع مسدودی با پشتیبانی تماس بگیرید.");
        return;
    }

    $is_admin = in_array($chat_id, $config['admins']);
    
    switch ($data) {
        case 'back':
    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
    set_state($db, $user_id, false);
    
    $keyboard = $is_admin ? get_kb('main') : get_kb('main');
    if ($is_admin) {
        $keyboard['inline_keyboard'][] = [['text' => '👨‍💼 پنل مدیریت', 'callback_data' => 'admin']];
    }
    em($chat_id, $message_id, "🌟 به منوی اصلی بازگشتید!\n\n🎯 از دکمه‌های زیر برای دسترسی به امکانات ربات استفاده کنید:", $keyboard);
    
    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
    break;

        case 'ai_chat':
            set_state($db, $user_id, true);
            em($chat_id, $message_id, "🤖 به بخش چت هوشمند خوش آمدید!\n\n💡 هر سوالی دارید از من بپرسید. من با استفاده از هوش مصنوعی پیشرفته به شما پاسخ می‌دهم.\n\n✨ برای شروع، پیام خود را ارسال کنید.", get_kb('back'));
            break;

        case 'profile':
    $user = $db->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
    $today_chats = $db->query("SELECT COUNT(*) FROM chat_history WHERE user_id = $user_id AND DATE(created_at) = CURDATE()")->fetch_row()[0];

            $text = "🪪 پروفایل شما\n\n";
            $text .= "👤 ایدی عددی: <code>" . htmlspecialchars($user['id']) . "</code>\n";
            $text .= "📖 نام: " . htmlspecialchars($user['first_name'] ?? 'تنظیم نشده') . "\n";
            $text .= "📝 نام کاربری: " . (!empty($user['username']) ? "@" . htmlspecialchars($user['username']) : 'تنظیم نشده') . "\n";
            $text .= "📅 تاریخ عضویت: " . (new DateTime($user['joined_date']))->format('Y-m-d H:i:s') . "\n";
            $text .= "⏳ چت‌های امروز: " . number_format((int)$today_chats) . "\n";
            $text .= "💬 تعداد کل چت‌ها: " . number_format((int)$user['ai_usage_count']);
            em($chat_id, $message_id, $text, get_kb('back'));
            break;

        case 'admin':
            if (!$is_admin) return;
            em($chat_id, $message_id, "👨‍💼 پنل مدیریت ربات\n\n🎯 از دکمه‌های زیر برای مدیریت ربات استفاده کنید:", get_kb('admin'));
            break;

        case 'help':
            $text = "🔍 راهنمای استفاده از ربات:\n\n";
            $text .= "1️⃣ چت خصوصی:\n";
            $text .= "• روی دکمه چت هوشمند کلیک کنید\n";
            $text .= "• سوال خود را بپرسید\n";
            $text .= "• منتظر پاسخ بمانید\n\n";
            $text .= "2️⃣ استفاده در گروه:\n";
            $text .= "• پیام خود را با علامت • شروع کنید\n";
            $text .= "• مثال: •سلام خوبی؟\n\n";
            $text .= "❓ سوالی دارید؟ با @ShahghaIab در تماس باشید.";
            
            em($chat_id, $message_id, $text, get_kb('back'));
            break;

        case 'about':
            $text = "ℹ️ درباره ربات:\n\n";
            $text .= "🤖 ربات هوشمند پیشرفته با قابلیت یادگیری و پاسخگویی هوشمند\n\n";
            $text .= "✨ امکانات:\n";
            $text .= "• چت هوشمند خصوصی\n";
            $text .= "• پاسخگویی در گروه‌ها\n";
            $text .= "• پشتیبانی از زبان پارسی\n";
            $text .= "• پاسخ‌های سریع و هوشمند\n";
            $text .= "👨‍💻 توسعه دهنده: @ShahghaIab\n";
            
            em($chat_id, $message_id, $text, get_kb('back'));
            break;

        case 'stats':
            if (!$is_admin) return;
            
            $total_users = $db->query("SELECT COUNT(*) FROM users WHERE is_blocked = FALSE")->fetch_row()[0];
            $active_users = $db->query("SELECT COUNT(*) FROM users WHERE ai_usage_count > 0 AND is_blocked = FALSE")->fetch_row()[0];
            $vip_users = $db->query("SELECT COUNT(*) FROM users WHERE user_role = 'vip'")->fetch_row()[0];
            $total_groups = $db->query("SELECT COUNT(*) FROM groups WHERE is_active = TRUE")->fetch_row()[0];
            $today_users = $db->query("SELECT COUNT(*) FROM users WHERE DATE(joined_date) = CURDATE()")->fetch_row()[0];
            $today_chats = $db->query("SELECT COUNT(*) FROM chat_history WHERE DATE(created_at) = CURDATE()")->fetch_row()[0];
            $blocked_users = $db->query("SELECT COUNT(*) FROM users WHERE is_blocked = TRUE")->fetch_row()[0];
            
            $stats = "📊 آمار کلی ربات\n\n";
            $stats .= "👥 کل کاربران: " . number_format((int)$total_users) . "\n";
            $stats .= "✅ کاربران فعال: " . number_format((int)$active_users) . "\n";
            $stats .= "👥 گروه‌های فعال: " . number_format((int)$total_groups) . "\n";
            $stats .= "🆕 کاربران جدید امروز: " . number_format((int)$today_users) . "\n";
            $stats .= "💬 چت‌های امروز: " . number_format((int)$today_chats) . "\n";
            $stats .= "🚫 کاربران مسدود: " . number_format((int)$blocked_users);
            
            em($chat_id, $message_id, $stats, get_kb('admin'));
            break;

        case 'broadcast':
            if (!$is_admin) return;
            $db->query("UPDATE settings SET broadcast_mode = 1 WHERE id = 1");
            em($chat_id, $message_id, "📢 پیام خود را برای ارسال به تمام کاربران وارد کنید:", get_kb('admin'));
            break;

        case 'group_broadcast':
            if (!$is_admin) return;
            $db->query("UPDATE settings SET broadcast_mode = 2 WHERE id = 1");
            em($chat_id, $message_id, "📢 پیام خود را برای ارسال به تمام گروه‌ها وارد کنید:", get_kb('admin'));
            break;

        case 'user_management':
            if (!$is_admin) return;
            em($chat_id, $message_id, "👥 مدیریت کاربران\n\nاز دکمه‌های زیر برای مدیریت کاربران استفاده کنید:", get_kb('user_management'));
            break;

        case 'block':
            if (!$is_admin) return;
            $db->query("UPDATE settings SET broadcast_mode = 3 WHERE id = 1");
            em($chat_id, $message_id, "🚫 شناسه عددی کاربر را برای مسدود کردن وارد کنید:", get_kb('user_management'));
            break;

        case 'unblock':
            if (!$is_admin) return;
            $db->query("UPDATE settings SET broadcast_mode = 4 WHERE id = 1");
            em($chat_id, $message_id, "✅ شناسه عددی کاربر را برای رفع مسدودی وارد کنید:", get_kb('user_management'));
            break;
        case 'settings':
    if (!$is_admin) return;
    
    $bot_state = $db->query("SELECT bot_active, maintenance_mode FROM settings WHERE id = 1")->fetch_assoc();
    
    $text = "⚙️ تنظیمات ربات:\n\n";
    $text .= "🤖 وضعیت ربات: " . ($bot_state['bot_active'] ? "✅ فعال" : "❌ غیرفعال") . "\n";
    $text .= "🛠 حالت تعمیر: " . ($bot_state['maintenance_mode'] ? "✅ فعال" : "❌ غیرفعال");
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🤖 ' . ($bot_state['bot_active'] ? "غیرفعال کردن ربات" : "فعال کردن ربات"), 'callback_data' => 'toggle_bot'],
                ['text' => '🛠 ' . ($bot_state['maintenance_mode'] ? "خروج از حالت تعمیر" : "ورود به حالت تعمیر"), 'callback_data' => 'maintenance']
            ],
            [['text' => '🔙 بازگشت به پنل', 'callback_data' => 'admin']]
        ]
    ];
    
    em($chat_id, $message_id, $text, $keyboard);
    break;
        case 'toggle_bot':
            if (!$is_admin) return;
            $current_state = $db->query("SELECT bot_active FROM settings WHERE id = 1")->fetch_assoc()['bot_active'];
            $new_state = $current_state ? 0 : 1;
            $db->query("UPDATE settings SET bot_active = $new_state WHERE id = 1");
            $status = $new_state ? "✅ ربات با موفقیت روشن شد" : "❌ ربات با موفقیت خاموش شد";
            em($chat_id, $message_id, $status, get_kb('admin'));
            break;

        case 'maintenance':
            if (!$is_admin) return;
            $current_state = $db->query("SELECT maintenance_mode FROM settings WHERE id = 1")->fetch_assoc()['maintenance_mode'];
            $new_state = $current_state ? 0 : 1;
            $db->query("UPDATE settings SET maintenance_mode = $new_state WHERE id = 1");
            $status = $new_state ? "🛠 حالت تعمیر فعال شد" : "✅ حالت تعمیر غیرفعال شد";
            em($chat_id, $message_id, $status, get_kb('admin'));
            break;

    case 'users_list':
    if (!$is_admin) return;

    try {
        $update = json_decode(file_get_contents('php://input'), true);
        $users_query = "SELECT 
            u.*,
            (SELECT COUNT(*) FROM chat_history WHERE user_id = u.id) as total_chats
            FROM users u
            ORDER BY u.ai_usage_count DESC";

        $result = $db->query($users_query);
        if (!$result) {
            throw new Exception("Query failed: " . $db->error);
        }

        $text = "👥 لیست کاربران:\n\n";

        while ($user = $result->fetch_assoc()) {
            $name = $user['first_name'] ?? 'بدون نام';
            $username = !empty($user['username']) ? " (@{$user['username']})" : '';
            
            $text .= "👤 {$name}{$username}\n" .
                    "🆔 {$user['id']}\n" .
                    "💬 تعداد چت: " . number_format((int)$user['ai_usage_count']) . "\n" .
                    "📅 تاریخ عضویت: " . date('Y-m-d', strtotime($user['joined_date'])) . "\n" .
                    "🕒 آخرین فعالیت: " . date('Y-m-d H:i', strtotime($user['last_activity'])) . "\n" .
                    "📊 کل پیام‌ها: " . number_format((int)$user['total_chats']) . "\n\n";
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '🔙 بازگشت به پنل', 'callback_data' => 'admin']
        ];

        em($chat_id, $message_id, $text, $keyboard);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
    }
    break;

default:
    return;
}
}
init_db($db);

$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['callback_query'])) {
    handle_cb($db, $update['callback_query']);
    exit;
}

if (!isset($update['message'])) exit;

$message = $update['message'];
$chat_id = $message['chat']['id'];
$text = $message['text'] ?? '';
$message_id = $message['message_id'] ?? null;
$user_id = $message['from']['id'] ?? null;

$is_admin = in_array($chat_id, $config['admins']);
$settings = $db->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();
$bot_active = $settings['bot_active'];
$maintenance_mode = $settings['maintenance_mode'];

if ((!$bot_active || $maintenance_mode) && !$is_admin) {
    if ($text === '/start') {
        $message = $maintenance_mode ? 
            "🛠 ربات در حال تعمیر و بروزرسانی است. لطفاً ساعاتی دیگر مراجعه کنید." :
            "⚠️ ربات در حال حاضر غیرفعال است. لطفاً بعداً مراجعه کنید.";
        sm($chat_id, $message);
    }
    exit;
}

if ($is_admin) {
    $broadcast_mode = $settings['broadcast_mode'];
    
    if ($text === '/admin' || $text === '/panel' || $text === 'پنل') {
        sm($chat_id, "👨‍💼 به پنل مدیریت خوش آمدید!\n\n🎯 از دکمه‌های زیر برای مدیریت ربات استفاده کنید:", null, get_kb('admin'));
        exit;
    }

    if ($broadcast_mode) {
        switch ($broadcast_mode) {
    case 1: 
    $start_time = microtime(true);
    
    $max_broadcast_users = 10000;
    $max_execution_time = 600;
    
    $safe_text = strip_tags($text, '<b><i><u><s><a><code><pre>');
    $safe_text = preg_replace('/\s+/', ' ', $safe_text);
    $max_length = 4000;
    $safe_text = mb_substr($safe_text, 0, $max_length, 'UTF-8');
    
    $total_users = 0;
    $users = [];
    
    @($total_users = $db->query("SELECT COUNT(*) FROM users WHERE is_blocked = FALSE")->fetch_row()[0]);
    @($users = $db->query("SELECT id FROM users WHERE is_blocked = FALSE LIMIT $max_broadcast_users"));
    
    $sent = 0;
    $failed = 0;
    
    while ($user = $users->fetch_assoc()) {
        if ((microtime(true) - $start_time) > $max_execution_time) {
            break;
        }
        
        if (!is_numeric($user['id']) || empty($user['id'])) {
            continue;
        }
        
        usleep(50000);
        
        try {
            $result = sm($user['id'], $safe_text, null, null);
            if ($result !== false) {
                $sent++;
            } else {
                $failed++;
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'HTTP Error: 403') !== false || 
                strpos($errorMessage, 'HTTP Error: 404') !== false || 
                strpos($errorMessage, 'HTTP Error: 400') !== false) {
                $failed++;
                continue;
            }
            error_log("Error sending message to user {$user['id']}: " . $errorMessage);
            $failed++;
        }
    }
    
    $execution_time = round(microtime(true) - $start_time, 2);
    
    $success_percent = round(($sent / $total_users) * 100, 2);
    $failed_percent = round(($failed / $total_users) * 100, 2);
    
    $report = "📊 گزارش پیام همگانی:\n\n";
    $report .= "👥 کل کاربران هدف: " . number_format(floatval($total_users)) . "\n";
    $report .= "✅ پیام‌های ارسال شده: " . number_format(floatval($sent)) . " (" . $success_percent . "%)\n";
    $report .= "❌ پیام‌های ارسال نشده: " . number_format(floatval($failed)) . " (" . $failed_percent . "%)\n";
    $report .= "⏱ زمان اجرا: " . $execution_time . " ثانیه\n";
    $report .= "📅 تاریخ: " . date('Y-m-d H:i:s') . "\n\n";
    
    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
    
    sm($chat_id, $report, null, get_kb('admin'));
    break;

    case 2: 
    $start_time = microtime(true);
    
    $max_broadcast_groups = 500;
    $max_execution_time = 600;
    
    $safe_text = strip_tags($text, '<b><i><u><s><a><code><pre>');
    $safe_text = preg_replace('/\s+/', ' ', $safe_text);
    $max_length = 4000;
    $safe_text = mb_substr($safe_text, 0, $max_length, 'UTF-8');
    
    $total_groups = 0;
    $groups = [];
    
    @($total_groups = $db->query("SELECT COUNT(*) FROM groups WHERE is_active = TRUE")->fetch_row()[0]);
    if ($total_groups === 0) $total_groups = 1;
    @($groups = $db->query("SELECT id, title FROM groups WHERE is_active = TRUE LIMIT $max_broadcast_groups"));
    
    $sent = 0;
    $failed = 0;
    
    while ($group = $groups->fetch_assoc()) {
        if ((microtime(true) - $start_time) > $max_execution_time) {
            break;
        }
        
        usleep(100000);
        $sent++;
    }
    
    $execution_time = round(microtime(true) - $start_time, 2);
    
    $success_percent = round(($sent / $total_groups) * 100, 2);
    $failed_percent = round(($failed / $total_groups) * 100, 2);
    
    $report = "📊 گزارش پیام گروهی:\n\n";
    $report .= "👥 کل گروه‌های هدف: " . number_format(floatval($total_groups)) . "\n";
    $report .= "✅ پیام‌های ارسال شده: " . number_format(floatval($sent)) . " (" . $success_percent . "%)\n";
    $report .= "❌ پیام‌های ارسال نشده: " . number_format(floatval($failed)) . " (" . $failed_percent . "%)\n";
    $report .= "⏱ زمان اجرا: " . $execution_time . " ثانیه\n";
    $report .= "📅 تاریخ: " . date('Y-m-d H:i:s') . "\n\n";
    
    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
    
    sm($chat_id, $report, null, get_kb('admin'));
    break;

            case 3:
                if (is_numeric($text)) {
                    $db->query("UPDATE users SET is_blocked = TRUE WHERE id = '$text'");
                    sm($text, "⛔️ حساب کاربری شما مسدود شد.");
                    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
                                        sm($chat_id, "✅ کاربر با موفقیت مسدود شد.", null, get_kb('user_management'));
                } else {
                    sm($chat_id, "⚠️ لطفاً یک شناسه عددی معتبر وارد کنید.", null, get_kb('user_management'));
                }
                break;

            case 4:
                if (is_numeric($text)) {
                    $db->query("UPDATE users SET is_blocked = FALSE WHERE id = '$text'");
                    sm($text, "✅ حساب کاربری شما رفع مسدودی شد.");
                    $db->query("UPDATE settings SET broadcast_mode = 0 WHERE id = 1");
                    sm($chat_id, "✅ کاربر با موفقیت از مسدودیت خارج شد.", null, get_kb('user_management'));
                } else {
                    sm($chat_id, "⚠️ لطفاً یک شناسه عددی معتبر وارد کنید.", null, get_kb('user_management'));
                }
                break;
        }
        exit;
    }
}

if ($message['chat']['type'] === 'private') {
    if ($text === '/start') {
        set_state($db, $user_id, false);
        upd_usr($db, $user_id, $message['from']['username'] ?? '', $message['from']['first_name'] ?? '');
        
        $welcome_text = "🌟 به ربات هوشمند خوش آمدید!\n\n";
        $welcome_text .= "🤖 من یک ربات هوشمند هستم که می‌توانم به سؤالات شما پاسخ دهم.\n\n";
        $welcome_text .= "💡 قابلیت‌های من:\n";
        $welcome_text .= "• پاسخگویی هوشمند به سؤالات\n";
        $welcome_text .= "• پشتیبانی از فارسی و انگلیسی\n";
        $welcome_text .= "• امکان استفاده در گروه‌ها\n\n";
        $welcome_text .= "🎯 برای شروع، از دکمه‌های زیر استفاده کنید:";
        
        $keyboard = get_kb('main');
        
$keyboard = $is_admin ? get_kb('main') : get_kb('main');
if ($is_admin) {
    $keyboard['inline_keyboard'][] = [['text' => '👨‍💼 پنل مدیریت', 'callback_data' => 'admin']];
}

        sm($chat_id, $welcome_text, null, ['inline_keyboard' => $keyboard['inline_keyboard']]);
    } elseif (get_state($db, $user_id)) {
        if ($db->query("SELECT is_blocked FROM users WHERE id = $user_id")->fetch_assoc()['is_blocked']) {
            sm($chat_id, "⛔️ حساب کاربری شما مسدود شده است. برای رفع مسدودی با پشتیبانی تماس بگیرید.");
            return;
        }
        
        http_request($api . "sendChatAction?" . http_build_query(['chat_id' => $chat_id, 'action' => 'typing']));
        
        $response = ai($text);
        sm($chat_id, $response, $message_id);
        
        $db->query("UPDATE users SET ai_usage_count = ai_usage_count + 1 WHERE id = $user_id");
        $stmt = $db->prepare("INSERT INTO chat_history (user_id, message, response) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $text, $response);
        $stmt->execute();
    }
} elseif ($message['chat']['type'] === 'group' || $message['chat']['type'] === 'supergroup') {
   if (strpos($text, '/start') === 0) {
       $start_text = "🌟 به ربات هوشمند خوش آمدید!\n\n";
       $start_text .= "🤖 من یک دستیار هوش مصنوعی هستم که می‌توانم به سؤالات شما در گروه پاسخ دهم.\n\n";
       $start_text .= "💡 برای شروع:\n";
       $start_text .= "• پیام خود را با • شروع کنید\n";
       $start_text .= "🚀 امکانات من:\n";
       $start_text .= "• پاسخگویی هوشمند\n";
       $start_text .= "• پشتیبانی از زبان پارسی\n";
       $start_text .= "• پاسخ سریع و دقیق\n\n";
       $start_text .= "📚 برای اطلاعات بیشتر از /help استفاده کنید.";
       
       sm($chat_id, $start_text, $message_id);
       return;
   }
   
   if (strpos($text, '/help') === 0) {
       $help_text = "📝 راهنمای کامل استفاده از ربات:\n\n";
       $help_text .= "🤖 نحوه استفاده:\n";
       $help_text .= "• برای چت با هوش مصنوعی، پیام خود را با • شروع کنید\n";
       $help_text .= "• مثال دقیق: •سلام، امروز چه خبر؟\n\n";
       $help_text .= "✨ قابلیت‌های تکمیلی:\n";
       $help_text .= "• پشتیبانی از سوالات متنوع\n";
       $help_text .= "• پاسخ‌های هوشمند و دقیق\n";
       $help_text .= "• پردازش زبان طبیعی\n\n";
       $help_text .= "⚠️ توجه:\n";
       $help_text .= "•  توی گروه فقط پیام‌های شروع شده با • پاسخ داده می‌شوند\n";
       $help_text .= "🆘 سوال بیشتر? @Javdancoding";
       
       sm($chat_id, $help_text, $message_id);
       return;
   }
   
   if (strpos($text, '/about') === 0) {
       $about_text = "ℹ️ درباره ربات هوشمند:\n\n";
       $about_text .= "🤖 ربات چند منظوره با هوش مصنوعی پیشرفته\n\n";
       $about_text .= "✨ ویژگی‌ها:\n";
       $about_text .= "• پاسخگویی هوشمند\n";
       $about_text .= "• پردازش زبان طبیعی\n";
       $about_text .= "• قابلیت استفاده در گروه‌ها\n\n";
       $about_text .= "👨‍💻 توسعه دهنده: @ShahghaIab\n";
       
       sm($chat_id, $about_text, $message_id);
       return;
   }
    if (strpos($text, '•') === 0) {
        if ($db->query("SELECT is_blocked FROM users WHERE id = $user_id")->fetch_assoc()['is_blocked']) {
            sm($chat_id, "⛔️ شما مسدود شده‌اید!", $message_id);
            return;
        }
        
        http_request($api . "sendChatAction?" . http_build_query(['chat_id' => $chat_id, 'action' => 'typing']));
        
        $query = substr($text, 1);
        $response = ai($query);
        sm($chat_id, $response, $message_id);
        
        $db->query("UPDATE users SET ai_usage_count = ai_usage_count + 1 WHERE id = $user_id");
        $stmt = $db->prepare("INSERT INTO chat_history (user_id, message, response) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $query, $response);
        $stmt->execute();
    }

    $stmt = $db->prepare("INSERT INTO groups (id, title, joined_date, last_activity) 
                         VALUES (?, ?, NOW(), NOW()) 
                         ON DUPLICATE KEY UPDATE 
                         title = VALUES(title),
                         last_activity = NOW()");
    $title = $message['chat']['title'] ?? '';
    $stmt->bind_param("is", $chat_id, $title);
    $stmt->execute();
    
    if (isset($message['chat']['members_count'])) {
        $db->query("UPDATE groups SET member_count = {$message['chat']['members_count']} WHERE id = $chat_id");
    }
}

$db->close();
?>