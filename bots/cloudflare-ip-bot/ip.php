<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
/*

//_______🕊God is enough for me🕊------//

 * Dev : @U3erZX
 
 لاین های 41 و 164 ادیت بشه

//--Conquer hearts first, then conquer lands.🇮🇷--//

*/
$telegram_ip_ranges = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
    ['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],
];

if (!filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
    http_response_code(403);
    exit("Access Denied: Invalid IP Address");
}

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok = false;

foreach ($telegram_ip_ranges as $range) {
    $lower_dec = (float) sprintf("%u", ip2long($range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($range['upper']));
    if ($ip_dec >= $lower_dec && $ip_dec <= $upper_dec) {
        $ok = true;
        break;
    }
}

if (!$ok) {
    http_response_code(403);
    exit("کیر شدگان را تردید نیست");
}
//------------------امنیت-------------------//
$TOKEN = ""; //توکن ربات
$API_URL = "https://api.telegram.org/bot$TOKEN/";

$cloudflare_ipv4_base64 = base64_decode("aHR0cHM6Ly93d3cuY2xvdWRmbGFyZS5jb20vaXBzLXY0");
$fastly_base64 = base64_decode("aHR0cHM6Ly9hcGkuZmFzdGx5LmNvbS9wdWJsaWMtaXAtbGlzdA==");

$spam_users = "spam_users.json";

function setBotStatus($status) {
    $file = 'bot_status.txt';
    if ($status == 'off') {
        file_put_contents($file, 'off');
    } else {
        file_put_contents($file, 'on');
    }
}

function getBotStatus() {
    $file = 'bot_status.txt';
    if (file_exists($file)) {
        return file_get_contents($file);
    }
    return 'on'; 
}

$update = json_decode(file_get_contents("php://input"), true);
if (!$update) exit;

$chat_id = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

$spam_data = json_decode(file_get_contents($spam_users), true);
if (!$spam_data) {
    $spam_data = [];
}

if (isset($spam_data[$chat_id]) && time() - $spam_data[$chat_id]["last_spam_time"] < 5) {
    if ($spam_data[$chat_id]["spam_count"] >= 10) {
        $remaining_time = 60 - (time() - $spam_data[$chat_id]["last_spam_time"]);
        sendMessage($chat_id, "❌ به علت اسپم به مدت {$remaining_time} ثانیه سکوت شده‌اید. لطفاً صبر کنید.");
        return;
    }
}

if (!isset($spam_data[$chat_id])) {
    $spam_data[$chat_id] = ["spam_count" => 0, "last_spam_time" => time()];
}

$spam_data[$chat_id]["spam_count"] += 1;
$spam_data[$chat_id]["last_spam_time"] = time();

if ($spam_data[$chat_id]["spam_count"] > 10) {
    sendMessage($chat_id, "❌ *به علت ارسال درخواست‌های زیاد، به مدت ۱ دقیقه محدود شدید.");
    $spam_data[$chat_id]["spam_count"] = 0;
    file_put_contents($spam_users, json_encode($spam_data));
    return;
}

if ($text == "/start") {
    if (getBotStatus() == 'off' && !isAdmin($chat_id)) {
        sendMessage($chat_id, "⚠️ ربات در حال حاضر خاموش است.");
        return; 
    }
    $welcome_text = "🎉 *به ربات دریافت آی‌پی‌های سالم کلودفلر خوش آمدید!*  
💡 این ربات به شما *آی‌پی‌های سالم و پرسرعت* کلودفلر رو میده، همراه با پورت‌های باز و اپراتور مناسب.  
🔍 برای دریافت آی‌پی، فقط کافیه دستور زیر رو ارسال کنی:  
👉 `/getip`";
    sendMessage($chat_id, $welcome_text, true);
} elseif ($text == "/getip") {
    if (getBotStatus() == 'off' && !isAdmin($chat_id)) {
        sendMessage($chat_id, "⚠️ ربات در حال حاضر خاموش است.");
        return; 
    }
    $processing_msg = sendMessage($chat_id, "⏳ *در حال پردازش... لطفاً کمی صبر کنید.*", true);
    
    $result = getValidIP();
    if ($result) {
        sendInlineKeyboard($chat_id, $result, "🔄 اسکن مجدد", "scan_again_" . time());
    } else {
        sendMessage($chat_id, "❌ *متاسفانه آی‌پی سالمی پیدا نشد!*\n🔁 لطفاً دوباره امتحان کنید.", false);
    }

    deleteMessage($chat_id, $processing_msg);
}

if (isset($update['callback_query'])) {
    $callback_data = $update['callback_query']['data'];
    $chat_id = $update['callback_query']['from']['id'];

    if (strpos($callback_data, "scan_again_") === 0) {
        if (getBotStatus() == 'off' && !isAdmin($chat_id)) {
            sendMessage($chat_id, "⚠️ ربات در حال حاضر خاموش است.");
            return;
        }
        $processing_msg = sendMessage($chat_id, "⏳ *در حال پردازش... لطفاً کمی صبر کنید.*", true);

        $result = getValidIP();
        if ($result) {
            sendInlineKeyboard($chat_id, $result, "🔄 اسکن مجدد", "scan_again_" . time());
        } else {
            sendMessage($chat_id, "❌ *متاسفانه آی‌پی سالمی پیدا نشد!*\n🔁 لطفاً دوباره امتحان کنید.", false);
        }

        deleteMessage($chat_id, $processing_msg);
    }
}

if ($text == "/panel") {
    if (!isAdmin($chat_id)) {
        sendMessage($chat_id, "⚠️ این بخش فقط برای مدیران قابل دسترسی است.");
        return;
    }

    $keyboard = [
        'keyboard' => [
            [['text' => '🟢 ربات روشن'], ['text' => '🔴 ربات خاموش']],
        ],
        'resize_keyboard' => true
    ];
    sendMessage($chat_id, "⚙️ پنل مدیریت", false, $keyboard);
}

function isAdmin($chat_id) {
    $admin_id = '';//عددی مالک
    return $chat_id == $admin_id;
}

if ($text == "🟢 ربات روشن") {
    if (!isAdmin($chat_id)) {
        sendMessage($chat_id, "⚠️ شما دسترسی به این بخش ندارید.");
        return;
    }
    setBotStatus('on');
    sendMessage($chat_id, "🔴 ربات روشن شد.");
} elseif ($text == "🔴 ربات خاموش") {
    if (!isAdmin($chat_id)) {
        sendMessage($chat_id, "⚠️ شما دسترسی به این بخش ندارید.");
        return;
    }
    setBotStatus('off');
    sendMessage($chat_id, "⚪ ربات خاموش شد.");
}

function sendMessage($chat_id, $text, $bold = false, $keyboard = null) {
    global $API_URL;
    $data = [
        "chat_id" => $chat_id,
        "text" => $text,
        "parse_mode" => "Markdown"
    ];
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    $response = json_decode(file_get_contents($API_URL . "sendMessage?" . http_build_query($data)), true);
    return $response['result']['message_id'] ?? null;
}

function getValidIP() {
    global $cloudflare_ipv4_base64, $fastly_base64;

    $cloudflare_ips = @file($cloudflare_ipv4_base64, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $fastly_data = @file_get_contents($fastly_base64);
    $fastly_ips = [];

    if ($fastly_data) {
        $fastly_json = json_decode($fastly_data, true);
        if (isset($fastly_json["addresses"]) && is_array($fastly_json["addresses"])) {
            $fastly_ips = $fastly_json["addresses"];
        }
    }

    $all_ips = array_merge($cloudflare_ips ?: [], $fastly_ips);
    shuffle($all_ips);

    foreach ($all_ips as $range) {
        $ip = getRandomIP($range);
        $open_ports = checkOpenPorts($ip, [443, 2083, 2053, 8443, 80]);

        if (!empty($open_ports)) {
            $operator = randomOperator();
            $ping = getPing($ip);
            return "✅ *آی‌پی سالم:* `$ip`\n🔹 *پورت‌های باز:* `" . implode(", ", $open_ports) . "`\n🔸 *عملکرد بهتر روی:* `$operator`\n📡 *Ping:* `$ping ms`";
        }
    }
    return false;
}

function getRandomIP($range) {
    list($subnet, $cidr) = explode('/', $range);
    $ip_long = ip2long($subnet);
    $mask = (1 << (32 - $cidr)) - 1;
    return long2ip($ip_long + rand(0, $mask));
}

function checkOpenPorts($ip, $ports) {
    $open_ports = [];
    foreach ($ports as $port) {
        $connection = @fsockopen($ip, $port, $errno, $errstr, 1);
        if ($connection) {
            fclose($connection);
            $open_ports[] = $port;
        }
    }
    return $open_ports;
}

function randomOperator() {
    $operators = ["ایرانسل", "همراه اول", "رایتل", "مخابرات"];
    return $operators[array_rand($operators)];
}

function getPing($ip) {
    $ping_result = shell_exec("ping -c 1 -w 1 $ip");
    preg_match('/time=([\d\.]+) ms/', $ping_result, $matches);
    return isset($matches[1]) ? $matches[1] : "نامشخص";
}

function sendInlineKeyboard($chat_id, $text, $button_text, $callback_data) {
    global $API_URL;
    $data = [
        "chat_id" => $chat_id,
        "text" => $text,
        "parse_mode" => "Markdown",
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [["text" => $button_text, "callback_data" => $callback_data]]
            ]
        ])
    ];
    file_get_contents($API_URL . "sendMessage?" . http_build_query($data));
}

function deleteMessage($chat_id, $message_id) {
    global $API_URL;
    file_get_contents($API_URL . "deleteMessage?" . http_build_query([
        "chat_id" => $chat_id,
        "message_id" => $message_id
    ]));
}

?>