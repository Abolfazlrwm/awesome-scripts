<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

//=============================
// Developed by: @ShahghaIab  
//=============================

error_reporting(E_ALL);
ini_set('display_errors', 1);

$telegram_ip_ranges = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
    ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],
];

$ip_dec = (float)sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$is_telegram_ip = false;

foreach ($telegram_ip_ranges as $range) {
    if (!$is_telegram_ip) {
        $lower_dec = (float)sprintf("%u", ip2long($range['lower']));
        $upper_dec = (float)sprintf("%u", ip2long($range['upper']));
        if ($ip_dec >= $lower_dec && $ip_dec <= $upper_dec) {
            $is_telegram_ip = true;
        }
    }
}

if (!$is_telegram_ip) {
    die("Access Denied - Invalid IP Address");
}

define('API_KEY', '"PUT-YOUR-BOT-TOKEN"'); //توکن
define('ADMIN_IDS', [
    7756364888, //ایدی عددی ادمین اول
    987654321, //ایدی عددی ادمین دوم 
    456789123, // ایدی عددی ادمین سوم 
    789123456, // ایدی عددی ادمین چهارم
    321654987 // ایدی عددی ادمین پنجم
]);

$required_files = [
    'bot_status.txt' => 'on',
    'reply_mode.txt' => '',
    'users.txt' => '',
    'blocked_users.txt' => ''
];

foreach ($required_files as $filename => $default_content) {
    if (!file_exists($filename)) {
        file_put_contents($filename, $default_content);
        chmod($filename, 0666);
    }
}

function bot($method, $data = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$update = json_decode(file_get_contents('php://input'), true);
$message = $update['message'] ?? [];
$callback = $update['callback_query'] ?? [];
$chat_id = $message['chat']['id'] ?? ($callback['message']['chat']['id'] ?? null);
$text = $message['text'] ?? '';
$from_id = $message['from']['id'] ?? ($callback['from']['id'] ?? null);
$first_name = $message['from']['first_name'] ?? '';
$username = $message['from']['username'] ?? 'بدون یوزرنیم';
$data = $callback['data'] ?? '';
$message_id = $callback['message']['message_id'] ?? null;
$reply_to_message = $message['reply_to_message'] ?? null;

$bot_status = file_get_contents('bot_status.txt');
$reply_mode = file_get_contents('reply_mode.txt');
$blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

if (!$chat_id) die('Invalid Request');

if (in_array($from_id, $blocked_users) && !in_array($from_id, ADMIN_IDS)) {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "⛔️ شما توسط مدیریت بلاک شده‌اید و قادر به استفاده از ربات نیستید."
    ]);
    exit;
}

if (in_array($from_id, ADMIN_IDS)) {
    if ($text == '/admin') {
        $keyboard = [
            [
                ['text' => '📬 ارسال پیام همگانی'],
                ['text' => '⚡️ وضعیت ربات: ' . ($bot_status == 'on' ? 'روشن ✅' : 'خاموش ❌')]
            ],
            [
                ['text' => 'آمار ربات📊'],
                ['text' => 'مدیریت کاربران🗄️']
            ]
        ];
        
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "🔰 پنل مدیریت ربات\n\n📊 وضعیت فعلی: " . ($bot_status == 'on' ? 'روشن ✅' : 'خاموش ❌'),
            'reply_markup' => json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true])
        ]);
        exit;
    }

if ($text == 'مدیریت کاربران🗄️') {
    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $keyboard = [];

    foreach ($users as $user_id) {
        $status = in_array($user_id, $blocked_users) ? '🚫' : '✅';
        $keyboard[] = [['text' => "$status کاربر: $user_id", 'callback_data' => "user_manage_$user_id"]];
    }

    if (empty($keyboard)) {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "❌ هیچ کاربری یافت نشد!"
        ]);
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "👥 لیست کاربران ربات:\n\n✅ = فعال\n🚫 = بلاک شده\n\nبرای مدیریت هر کاربر روی آن کلیک کنید.",
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }
    exit;
}

if (strpos($data, 'user_manage_') === 0) {
    $user_id = str_replace('user_manage_', '', $data);
    $blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $is_blocked = in_array($user_id, $blocked_users);
    
    $keyboard = [
        [
            ['text' => $is_blocked ? '✅ آنبلاک کاربر' : '🚫 بلاک کاربر', 
             'callback_data' => $is_blocked ? "unblock_$user_id" : "block_$user_id"]
        ],
        [
            ['text' => '🔙 بازگشت به لیست', 'callback_data' => 'back_to_users_list']
        ]
    ];

    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "👤 مدیریت کاربر: $user_id\n\nوضعیت فعلی: " . ($is_blocked ? '🚫 بلاک شده' : '✅ فعال'),
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ]);
    exit;
}

if (strpos($data, 'block_') === 0) {
    $user_id = str_replace('block_', '', $data);
    $blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    if (!in_array($user_id, $blocked_users)) {
        file_put_contents('blocked_users.txt', $user_id . "\n", FILE_APPEND);

        $keyboard = [
            [
                ['text' => '✅ آنبلاک کاربر', 'callback_data' => "unblock_$user_id"]
            ],
            [
                ['text' => '🔙 بازگشت به لیست', 'callback_data' => 'back_to_users_list']
            ]
        ];

        bot('sendMessage', [
            'chat_id' => $user_id,
            'text' => "⛔️ شما توسط مدیریت بلاک شده‌اید و قادر به استفاده از ربات نیستید."
        ]);

        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "✅ کاربر $user_id با موفقیت بلاک شد.\n\nوضعیت فعلی: 🚫 بلاک شده",
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }
    exit;
}
if (strpos($data, 'unblock_') === 0) {
    $user_id = str_replace('unblock_', '', $data);
    $blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    if (in_array($user_id, $blocked_users)) {
        $blocked_users = array_diff($blocked_users, [$user_id]);
        file_put_contents('blocked_users.txt', implode("\n", $blocked_users) . "\n");

        $keyboard = [
            [
                ['text' => '🚫 بلاک کاربر', 'callback_data' => "block_$user_id"]
            ],
            [
                ['text' => '🔙 بازگشت به لیست', 'callback_data' => 'back_to_users_list']
            ]
        ];

        bot('sendMessage', [
            'chat_id' => $user_id,
            'text' => "✅ محدودیت دسترسی شما به ربات برداشته شد."
        ]);

        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "✅ کاربر $user_id با موفقیت آنبلاک شد.\n\nوضعیت فعلی: ✅ فعال",
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }
    exit;
}

if ($data == 'back_to_users_list') {
    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $blocked_users = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $keyboard = [];

    foreach ($users as $user_id) {
        $status = in_array($user_id, $blocked_users) ? '🚫' : '✅';
        $keyboard[] = [['text' => "$status کاربر: $user_id", 'callback_data' => "user_manage_$user_id"]];
    }

    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "👥 لیست کاربران ربات:\n\n✅ = فعال\n🚫 = بلاک شده\n\nبرای مدیریت هر کاربر روی آن کلیک کنید.",
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ]);
    exit;
}
    if (strpos($text, '⚡️ وضعیت ربات:') === 0) {
        $new_status = $bot_status == 'on' ? 'off' : 'on';
        file_put_contents('bot_status.txt', $new_status);
        
        $keyboard = [
            [
                ['text' => '📬 ارسال پیام همگانی'],
                ['text' => '⚡️ وضعیت ربات: ' . ($bot_status == 'on' ? 'روشن ✅' : 'خاموش ❌')]
            ],
            [
                ['text' => 'آمار ربات📊'],
                ['text' => 'مدیریت کاربران🗄️']
            ]
        ];
        
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "وضعیت ربات به " . ($new_status == 'on' ? 'روشن ✅' : 'خاموش ❌') . " تغییر کرد.",
            'reply_markup' => json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true])
        ]);
        exit;
    }

    if ($text == 'آمار ربات📊') {
        $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $blocked = file('blocked_users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "📊 آمار ربات:\n\n👥 تعداد کل کاربران: " . count($users) . "\n🚫 کاربران بلاک شده: " . count($blocked)
        ]);
        exit;
    }
    
    if ($text == '📬 ارسال پیام همگانی') {
        file_put_contents('reply_mode.txt', 'broadcast');
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => '📝 پیام خود را برای ارسال همگانی وارد کنید:'
        ]);
        exit;
    }
    
    if ($reply_mode == 'broadcast' && $text != 'ارسال پیام همگانی') {
        $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $success = $fail = 0;
        
        foreach ($users as $user_id) {
            if (!in_array($user_id, $blocked_users)) {
                $result = bot('sendMessage', [
                    'chat_id' => $user_id,
                    'text' => "📢 پیام از طرف مدیریت:\n\n$text"
                ]);
                
                if ($result['ok']) $success++; else $fail++;
                usleep(50000);
            }
        }
        
        file_put_contents('reply_mode.txt', '');
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "📬 نتیجه ارسال پیام همگانی:\n\n✅ موفق: $success\n❌ ناموفق: $fail"
        ]);
        exit;
    }

    if ($reply_to_message && isset($reply_to_message['forward_from'])) {
        $user_id = $reply_to_message['forward_from']['id'];
        
        if (!in_array($user_id, $blocked_users)) {
            bot('sendMessage', [
                'chat_id' => $user_id,
                'text' => "📬 پاسخ از طرف پشتیبانی:\n\n$text",
                'reply_to_message_id' => $reply_to_message['message_id']
            ]);
            
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "✅ پاسخ شما با موفقیت ارسال شد."
            ]);
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "⚠️ این کاربر بلاک شده است و نمی‌توانید به او پیام ارسال کنید."
            ]);
        }
        exit;
    }
}

if ($text == '/start') {
    if ($bot_status == 'off' && !in_array($from_id, ADMIN_IDS)) {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ ربات در حال حاضر خاموش است. لطفا بعداً مراجعه کنید."
        ]);
        exit;
    }

    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    if (!in_array($chat_id, $users)) {
        file_put_contents('users.txt', $chat_id . "\n", FILE_APPEND);
    }
    
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "👋 سلام $first_name عزیز!\n\nبه ربات پارسی ساپورتر خوش آمدید.\n 💬 پیام خود را ارسال کنید تا به پشتیبانی ارسال شود.\n/help"
    ]);
    exit;
}

if ($text == '/help') {
    if ($bot_status == 'off' && !in_array($from_id, ADMIN_IDS)) {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ ربات در حال حاضر خاموش است. لطفا بعداً مراجعه کنید."
        ]);
        exit;
    }

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "💡 راهنمای ربات:\n\n📝 برای ارتباط با پشتیبانی کافیست پیام خود را ارسال کنید.\n\n✅ پیام شما به صورت مستقیم به مدیران ارسال می‌شود و در اسرع وقت پاسخ داده خواهد شد."
    ]);
    exit;
}

if ($text && !in_array($from_id, ADMIN_IDS) && $bot_status == 'on' && 
    $text != '/start' && $text != '/help' && $text != '/admin') {

    if ($bot_status == 'off') {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ ربات در حال حاضر خاموش است. لطفا بعداً مراجعه کنید."
        ]);
        exit;
    }

    if (in_array($from_id, $blocked_users)) {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "⛔️ شما توسط مدیریت بلاک شده‌اید و قادر به ارسال پیام نیستید."
        ]);
        exit;
    }

    foreach (ADMIN_IDS as $admin_id) {
        $forwarded = bot('forwardMessage', [
            'chat_id' => $admin_id,
            'from_chat_id' => $chat_id,
            'message_id' => $message['message_id']
        ]);

        if ($forwarded['ok']) {
            $forward_id = $forwarded['result']['message_id'];
            
            $inline_keyboard = [[
                ['text' => '📝 پاسخ سریع', 'callback_data' => 'reply_to_' . $from_id],
                ['text' => '🚫 بلاک کاربر', 'callback_data' => 'block_' . $from_id]
            ]];

            bot('sendMessage', [
                'chat_id' => $admin_id,
                'text' => "📨 اطلاعات کاربر:\n\n👤 نام: $first_name\n🆔 یوزرنیم: @$username\n📌 آیدی عددی: $from_id\n\n💡 برای پاسخ می‌توانید:\n1️⃣ روی پیام ریپلای کنید\n2️⃣ از دکمه پاسخ سریع استفاده کنید",
                'reply_to_message_id' => $forward_id,
                'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
            ]);
        }
    }

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "✅ پیام شما با موفقیت به پشتیبانی ارسال شد.\n\n⏳ لطفا منتظر پاسخ بمانید.",
        'reply_to_message_id' => $message['message_id']
    ]);
    exit;
}

if (strpos($data, 'reply_to_') === 0) {
    $user_id = str_replace('reply_to_', '', $data);
    
    if (in_array($user_id, $blocked_users)) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $callback['id'],
            'text' => "⚠️ این کاربر بلاک است و نمی‌توانید به او پیام دهید.",
            'show_alert' => true
        ]);
        exit;
    }

    file_put_contents('reply_mode.txt', $user_id);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "📝 لطفا پیام خود را برای ارسال به کاربر وارد کنید:",
        'reply_markup' => json_encode([
            'keyboard' => [[['text' => 'لغو پاسخ']]],
            'resize_keyboard' => true
        ])
    ]);
    exit;
}

if (is_numeric($reply_mode) && $text && $text != 'لغو پاسخ') {
    $user_id = $reply_mode;
    
    if (in_array($user_id, $blocked_users)) {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ این کاربر بلاک است و نمی‌توانید به او پیام دهید."
        ]);
    } else {
        bot('sendMessage', [
            'chat_id' => $user_id,
            'text' => "📬 پاسخ از طرف پشتیبانی:\n\n$text"
        ]);

        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "✅ پیام شما با موفقیت ارسال شد.",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => '📬 ارسال پیام همگانی'],
                ['text' => '⚡️ وضعیت ربات: ' . ($bot_status == 'on' ? 'روشن ✅' : 'خاموش ❌')]
            ],
            [
                ['text' => 'آمار ربات📊'],
                ['text' => 'مدیریت کاربران🗄️']
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }
    
    file_put_contents('reply_mode.txt', '');
    exit;
}

if ($text == 'لغو پاسخ' && $reply_mode) {
    file_put_contents('reply_mode.txt', '');
    
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "❌ ارسال پاسخ لغو شد.",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => '📬 ارسال پیام همگانی'],
                ['text' => '⚡️ وضعیت ربات: ' . ($bot_status == 'on' ? 'روشن ✅' : 'خاموش ❌')]
            ],
            [
                ['text' => 'آمار ربات📊'],
                ['text' => 'مدیریت کاربران🗄️']
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    exit;
}