<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/
ob_start();
include 'config.php';
include('lib/jdf.php');
//========================== // bot // ==============================
function bot($method, $datas = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . API_KEY . '/' . $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    return json_decode(curl_exec($ch));
}
//========================== // update // ==============================
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)) {
    $message = $update->message;
    $message_id = $message->message_id;
    $text = safe($message->text);
    $chat_id = $message->chat->id;
    $tc = $message->chat->type;
    $first_name = $message->from->first_name;
    $from_id = $message->from->id;
    // databse
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$from_id' LIMIT 1"));
    $block = mysqli_query($connect, "SELECT * FROM `block` WHERE `id` = '$from_id' LIMIT 1");
}
if (isset($update->callback_query)) {
    $callback_query = $update->callback_query;
    $callback_query_id = $callback_query->id;
    $data = $callback_query->data;
    $from_id = $callback_query->from->id;
    $message_id = $callback_query->message->message_id;
    $chat_id = $callback_query->message->chat->id;
    // databse
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$from_id' LIMIT 1"));
    $block = mysqli_query($connect, "SELECT * FROM `block` WHERE `id` = '$from_id' LIMIT 1");
}
$admin = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `admin` WHERE `admin` = '$from_id' LIMIT 1"));
date_default_timezone_set('Asia/Tehran');
$timestamp = time();
$new_time = $timestamp - 3600;
$time = date('H:i', $new_time);
$date = gregorian_to_jalali(date('Y'), date('m'), date('d'), '/');
$ToDay = jdate('l');
$forchaneel = json_decode(file_get_contents("https://api.telegram.org/bot" . API_KEY . "/getChatMember?chat_id=@$channel&user_id=" . $from_id));
$tch = $forchaneel->result->status;

$send = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `sendall` LIMIT 1"));

$statjson = json_decode(file_get_contents("https://api.telegram.org/bot" . API_KEY . "/getChatMember?chat_id=$chat_id&user_id=" . $from_id), true);
$status = $statjson['result']['status'];

//==============================// function //==============================//
function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return json_decode(curl_exec($ch));
}
function convert($string)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];
    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
    return $englishNumbersOnly;
}
function Takhmin($fil)
{
    if ($fil <= 100) {
        return "1";
    } else {
        $besanie = $fil / 100;
        return ceil($besanie) + 1;
    }
}
function safe($text)
{
    global $connect;
    $text = $connect->real_escape_string($text);
    $array = ['$', ';', '"', "'", '<', '>'];
    return str_replace($array, '', $text);
}
function CanSendRequest($results)
{
    $ok = true;
    foreach ($results as $result)
        if ($result == false)
            $ok = false;
    return $ok;
}
function objectToArrays($object)
{
    if (!is_object($object) && !is_array($object)) {
        return $object;
    }
    if (is_object($object)) {
        $object = get_object_vars($object);
    }
    return array_map("objectToArrays", $object);
}
function generate_key($prefix)
{
    $random_part_1 = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
    $random_part_2 = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
    $key = "{$prefix}-{$random_part_1}-{$random_part_2}";
    return $key;
}
function user_link($prefix)
{
    return generate_key($prefix);
}
function getChatstats($chat_id, $token)
{
    $url = 'https://api.telegram.org/bot' . $token . '/getChatAdministrators?chat_id=' . $chat_id;
    $result = file_get_contents($url);
    $result = json_decode($result);
    $result = $result->ok;
    return $result;
}
//================// Join Function //================//
function IsJoined($token, $User, array $Channels)
{
    $AcceptedRoles = ['administrator', 'creator', 'member'];
    foreach ($Channels as $iterator) {
        $Req = file_get_contents('https://api.telegram.org/bot' . $token . '/getChatMember?chat_id=' . $iterator . '&user_id=' . $User);
        yield in_array(json_decode($Req)->result->status, $AcceptedRoles);
    }
}
function is_join($id)
{
    global $botname;
    global $botnameEN;
    global $userjoin;
    global $connect;

    $chs = mysqli_query($connect, "SELECT idoruser FROM channels");
    $fil = mysqli_num_rows($chs);
    while ($row = mysqli_fetch_assoc($chs)) {
        $ar[] = $row["idoruser"];
    }
    for ($i = 0; $i < $fil; $i++) {
        $by = $i + 1;
        $okk = $ar[$i];
        $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
        $link = $ch['link'];
        $ch_id = $ch['idoruser'];

        $in_ch = array();
        $type = bot("getChatMember", ["chat_id" => "$ch_id", "user_id" => $id]);
        $type = (is_object($type)) ? $type->result->status : $type['result']['status'];
        if ($type == 'creator' || $type == 'administrator' || $type == 'member') {
            $in_ch[$ch_id] = $type;
        } else {
            $keyboard = array();
            for ($j = 0; $j < $fil; $j++) {
                $by = $j + 1;
                $okk = $ar[$j];
                $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
                $link = $ch['link'];
                $ch_id = $ch['idoruser'];

                $ch_info = bot("getChat", ["chat_id" => "$ch_id"]);
                $ch_name = (is_object($ch_info)) ? $ch_info->result->title : $ch_info['result']['title'];
                $keyboard[] = array(array('text' => $ch_name, 'url' => $link));
            }
            $userjoin = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$id' LIMIT 1"));
            $keyboard[] = array(array('text' => '✅ تایید عضویت', 'callback_data' => 'join'));
            $text = "☑️ برای استفاده از ربات « $botname » ابتدا باید وارد کانال های ما شوید

❗️ برای دریافت آموزش ها ، اطلاعیه ها شما باید عضو کانال های ربات شوید

👇 بعد از عضویت در کانال روی دکمه « ✅ تایید عضویت » بزنید 👇";

            bot('sendmessage', [
                'chat_id' => $id,
                'text' => "$text",
                'parse_mode' => 'MarkDown',
                'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))
            ]);
        }
    }
}

function check_join($id)
{
    global $connect;
    $in_ch = [];
    $chs = mysqli_query($connect, "SELECT idoruser FROM channels");
    $fil = mysqli_num_rows($chs);
    if ($fil == 0) {
        return true;
    }
    for ($i = 0; $i < $fil; $i++) {
        $okk = mysqli_fetch_assoc($chs)['idoruser'];
        $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
        $link = $ch['link'];
        $ch_id = $ch['idoruser'];

        $type = bot("getChatMember", ["chat_id" => "$ch_id", "user_id" => $id]);
        $type = (is_object($type)) ? $type->result->status : $type['result']['status'];
        if ($type == 'creator' || $type ==  'administrator' || $type ==  'member') {
            $in_ch[$ch_id] = $type;
        } else {
            return false;
        }
    }
    return true;
}
//==============================// keybord and Text //=======================================
$home = json_encode([
    'keyboard' => [
        [['text' => "💌 به مخاطب خاصم وصلم کن"]],
        [['text' => "📬 لینک ناشناس من"], ['text' => "👤 مدیریت حساب"]],
        [['text' => "📚 راهنما و پشتیبانی ☎️"]],
    ],
    'resize_keyboard' => true,
]);
if ($admin['admin'] == $from_id) {
    $homeData = json_decode($home, true);
    $homeData['keyboard'][] = [['text' => '👤 پنل مدیریت 👤']];
    $home = json_encode($homeData);
}
$manage_account = json_encode([
    'keyboard' => [
        [['text' => "👤 اطلاعات حساب"]],
        [['text' => "💳 شارژ حساب"], ['text' => "💎 لینک شخصی"]],
        [['text' => "🔙 بازگشت"]],
    ],
    'resize_keyboard' => true,
]);
$help_support = json_encode([
    'inline_keyboard' => [
        [['text' => "📚 راهنما استفاده", 'callback_data' => "help"], ['text' => "☎️ پشتیبانی ربات", 'callback_data' => "support"]],
    ]
]);
$back = json_encode([
    'keyboard' => [
        [['text' => "🔙 بازگشت"]],
    ],
    'resize_keyboard' => true,
]);
$admin_panel = json_encode([
    'keyboard' => [
        [['text' => "📊 آمار ربات 📊"]],
        [['text' => "❌ حذف مسدودیت"], ['text' => "⚠️ مسدود کردن"]],
        [['text' => "📣 مدیریت قفل ها"], ['text' => "👤 مدیریت ادمین ها"]],
        [['text' => "💬 پیام همگانی"], ['text' => "↗️ فوروارد همگانی"]],
        [['text' => "🟢 ربات روشن"], ['text' => "🔴 ربات خاموش"]],
        [['text' => "🔙 بازگشت"]],
    ],
    'resize_keyboard' => true,
]);
$manage_admin = json_encode([
    'keyboard' => [
        [['text' => "➕ افزودن ادمین"]],
        [['text' => "👤 پنل مدیریت 👤"], ['text' => "📚 لیست ادمین ها"]],
    ],
    'resize_keyboard' => true,
]);
$manage_channel = json_encode([
    'keyboard' => [
        [['text' => "➕ افزودن چنل"]],
        [['text' => "👤 پنل مدیریت 👤"], ['text' => "📚 لیست چنل ها"]],
    ],
    'resize_keyboard' => true,
]);
$help_button = json_encode([
    'inline_keyboard' => [
        [['text' => "👈 این ربات چیه؟ به چه درد میخوره؟", 'callback_data' => "help_1"]],
        [['text' => "👈 چطوری به مخاطب خاصم وصل بشم؟", 'callback_data' => "help_2"]],
        [['text' => "👈 چطوری پیام ناشناس دریافت کنم؟", 'callback_data' => "help_3"]],
    ]
]);
$backhp_button = json_encode([
    'inline_keyboard' => [
        [['text' => "بازگشت به صفحه راهنما", 'callback_data' => "help"]],
    ]
]);

$start = "😇 به ربات چت ناشناس خوش آمدید!

👇 امروز میخواید چه کاری براتون انجام بدم؟";

//===========================// data //===========================//

if ($data == "help") {
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "🔎 راهنما

من اینجام که کمکت کنم 😃

برای دریافت راهنمایی در مورد هر موضوع، کافیه دکمه شیشه ای موردنظر رو لمس کنی 👇🏻",
        'reply_markup' => $help_button
    ]);
}
if ($data == "help_1") {
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "این روبات چیه؟ به چه درد میخوره؟

👈 « $botname » محبوب‌ترین و کامل‌ترین ربات‌ تلگرام شناخته شده که با استفاده از این برنامه :

🔹 میتونی به دوستات اجازه بدی هر حرف یا انتقادی که تو دلشون مونده رو بصورت ناشناس بهت بگن!

🔹 و جذاب تر از همه میتونی به مخاطب خاصت بصورت ناشناس پیام بفرستی 👌",
        'reply_markup' => $backhp_button
    ]);
}
if ($data == "help_2") {
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "چطوری به مخاطب خاصم وصل بشم؟

برای اینکه بتونی به مخاطب خاصت وصل بشی باید Username@ یا همون آیدی تلگرام اون شخص رو وارد ربات کـنی یا آیدی عددی اون شخص رو بفرستی تا ببینیم ایشون عضو برنامه هست یا نه! و خیلی راحت بدون اینکه بفهمه کی هستی پیامهای ناشناس بفرستی و حرف دلـتو بهش بگی !😎",
        'reply_markup' => $backhp_button
    ]);
}
if ($data == "help_3") {
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "چطوری پیام ناشناس دریافت کنم؟

برای دریافت پیام ناشناس کافیه دستور 👈🏻 /link رو لمس کنی تا لینک اختصاصیت برات ارسال بشه. با فرستادن این لینک به دوستات و گروه‌ها یا با گذاشتن در شبکه‌های اجتماعی مثل فیس‌بوک و توییتر دوستات میتونن حرفی که تو دلشونه رو به صورت ناشناس بهت بزنن. یک متن پیش‌فرض همراه لینک بهت فرستاده میشه که بتونی راحت همون پیام رو فوروارد کنی، البته میتونی متنشو به دلخواه خودت تغییر هم بدی!",
        'reply_markup' => $backhp_button
    ]);
}

if ($data == "support") {
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "👮🏻 همکاران ما در خدمت شما هستن

📨 جهت ارتباط به صورت مستقیم 👈🏻 @$support ",
    ]);
}

if ($data == 'join') {
    if (check_join("$from_id") == 'true') {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "☑️ عضویت شما در کانال تایید شد

            $start",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $home
        ]);
    } else {
        bot('answercallbackquery', [
            'callback_query_id' => $callback_query_id,
            'text' => "⚠️ - لطفا ابتدا عضو کانال های جوین اجباری شوید.",
            'show_alert' => true
        ]);
    }
}

if (strpos($data, "delad_") !== false and $from_id == $creator) {
    $ok = str_replace("delad_", '', $data);
    $chs = mysqli_query($connect, "select admin from admin");
    $fil = mysqli_num_rows($chs);
    if ($fil == 1) {
        $connect->query("DELETE FROM admin WHERE admin = '$ok'");
        bot('editMessagetext', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "👇🏻 لیست تمام ادمین های 

❌ تمام ادمین ها حذف شده است.",
            'parse_mode' => "HTML",
        ]);
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "✅ ادمین حذف شد .",
            'show_alert' => false
        ]);
    } else {
        $connect->query("DELETE FROM admin WHERE admin = '$ok'");
        $chs = mysqli_query($connect, "select admin from admin");
        $fil = mysqli_num_rows($chs);
        while ($row = mysqli_fetch_assoc($chs)) {
            $ar[] = $row["admin"];
        }
        for ($i = 0; $i <= $fil; $i++) {

            $by = $i + 1;
            $okk = $ar[$i];
            $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM admin WHERE admin = '$okk' LIMIT 1"));
            $link = $ch['admin'];
            if ($link != null) {
                $d4[] = [['text' => "$link", 'callback_data' => 'ior'], ['text' => "❌ حذف", 'callback_data' => "delad_" . $okk . ""]];
            }
        }
        bot('editMessagetext', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "👇🏻 لیست تمام ادمین ها

❌ ادمین حذف شد .",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => $d4,
            ])
        ]);
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "✅ ادمین حذف شد .",
            'show_alert' => false
        ]);
    }
}

if (strpos($data, "delc_") !== false) {
    if ($admin['admin'] == $from_id) {
        $ok = str_replace("delc_", '', $data);
        $chs = mysqli_query($connect, "select idoruser from channels");
        $fil = mysqli_num_rows($chs);
        if ($fil == 1) {
            $connect->query("DELETE FROM channels WHERE idoruser = '$ok'");
            bot('editMessagetext', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => "👇🏻 لیست تمام چنل های قفل

❌ تمام چنل ها حذف شده است.",
                'parse_mode' => "HTML",
            ]);
            bot('answercallbackquery', [
                'callback_query_id' => $update->callback_query->id,
                'text' => "✅ چنل حذف شد .",
                'show_alert' => false
            ]);
        } else {
            $connect->query("DELETE FROM channels WHERE idoruser = '$ok'");
            $chs = mysqli_query($connect, "select idoruser from channels");
            $fil = mysqli_num_rows($chs);
            while ($row = mysqli_fetch_assoc($chs)) {
                $ar[] = $row["idoruser"];
            }
            for ($i = 0; $i <= $fil; $i++) {

                $by = $i + 1;
                $okk = $ar[$i];
                $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
                $link = $ch['link'];
                if ($link != null) {
                    $d4[] = [['text' => "چنل شماره $by", 'url' => $link], ['text' => "❌ حذف", 'callback_data' => "delc_$okk"]];
                }
            }
            bot('editMessagetext', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => "👇🏻 لیست تمام چنل های قفل

❌ چنل حذف شد .",
                'parse_mode' => "HTML",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $d4
                ])
            ]);
            bot('answercallbackquery', [
                'callback_query_id' => $update->callback_query->id,
                'text' => "✅ چنل حذف شد .",
                'show_alert' => false
            ]);
        }
    }
}

if (strpos($data, "change_status|") !== false) {
    $get_data = explode("|", $data);
    $need_change = $get_data[1];
    $messg_id = $get_data[2];

    if ($user[$need_change] == 'false') {
        $need_changed = "true";
        $need_emoji = "✅";
    } elseif ($user[$need_change] == 'true') {
        $need_changed = "false";
        $need_emoji = "❌";
    }

    $connect->query("UPDATE `user` SET `$need_change` = '$need_changed' WHERE `id` = '$from_id'");

    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$from_id' LIMIT 1"));

    if ($user['silent'] == 'false') {
        $silent_mode = "❌";
    } elseif ($user['silent'] == 'true') {
        $silent_mode = "✅";
    }
    if ($user['copy'] == 'false') {
        $copy_mode = "❌";
    } elseif ($user['copy'] == 'true') {
        $copy_mode = "✅";
    }

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "وضعیت با موفقیت به [$need_emoji] تغییر یافت!",
        'show_alert' => false
    ]);
    bot('editMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $messg_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "[$silent_mode]", 'callback_data' => "change_status|silent|$messg_id"], ['text' => "😴 حالت سایلنت", 'callback_data' => "change_status|silent|$messg_id"]],
                [['text' => "[$copy_mode]", 'callback_data' => "change_status|copy|$messg_id"], ['text' => "🔰 حالت کپی رایت", 'callback_data' => "change_status|copy|$messg_id"]],
                [['text' => "📅 : $date", 'callback_data' => "none"], ['text' => "🗓 : $ToDay", 'callback_data' => "none"], ['text' => "⏰ : $time", 'callback_data' => "none"]],
            ]
        ])
    ]);
}

if (strpos($data, "answer|") !== false) {
    $get_data = explode("|", $data);
    $msg_id = $get_data[1];
    $messag_id = $get_data[2];

    $msg_info = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `messages` WHERE `id` = '$msg_id' LIMIT 1"));

    $user_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '{$msg_info['from']}' LIMIT 1"));

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "لطفا پیامتان را ارسال کنید.",
        'show_alert' => false
    ]);

    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "☝️ در حال پاسخ دادن به فرستنده این پیام هستی ... ؛ منتظریم بفرستی :)",
        'reply_to_message_id' => $messag_id,
        'reply_markup' => $back
    ]);
    $connect->query("UPDATE `user` SET `step` = 'send_answer|$msg_id' WHERE `id` = '$from_id' LIMIT 1");
}

if (strpos($data, "block|") !== false) {
    $get_data = explode("|", $data);
    $user_id = $get_data[1];
    $messg_id = $get_data[2];
    $messg_id2 = $get_data[3];

    $connect->query("INSERT INTO `block_user` (`from` , `to`) VALUES ('$from_id' , '$user_id')");

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "✅ با موفقیت [⚠️ بلاک] شد!",
        'show_alert' => false
    ]);

    bot('editMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $messg_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🔐 آزاد سازی", 'callback_data' => "unblock|$user_id|$messg_id"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$messg_id2|$messg_id"]],
            ]
        ])
    ]);
}

if (strpos($data, "unblock|") !== false) {
    $get_data = explode("|", $data);
    $user_id = $get_data[1];
    $messg_id = $get_data[2];
    $messg_id2 = $get_data[3];

    $connect->query("DELETE FROM `block_user` WHERE `from` = '$from_id' AND `to` = '$user_id'");

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "✅ با موفقیت [🔐 آزاد] شد!",
        'show_alert' => false
    ]);

    bot('editMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $messg_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "⛔️ بلاک", 'callback_data' => "block|$user_id|$messg_id"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$messg_id2|$messg_id"]],
            ]
        ])
    ]);
}
//===========================// Send Msg //===========================//
if (preg_match('/^(\/start) (.*)/', $text, $match)) {
    if ($user['id'] != true) {
        $connect->query("INSERT INTO `user` (`id` , `create_at`) VALUES ('$from_id' , '$timestamp')");
    }
    $link_send = safe($match[2]);
    $user_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `link` = '$link_send' LIMIT 1"));
    $user_id = $user_check['id'];
    if ($user_id != $from_id) {
        if ($user_check['id'] == true) {
            if ($user_check['silent'] == 'false') {
                $block_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `block_user` WHERE `from` = '$user_id' AND `to` = '$from_id' LIMIT 1"));
                if (!$block_check) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "در حال ارسال پیام ناشناس به {$user_check['name']} هستی!

با خیال راحت هر حرف یا انتقادی که تو دلت هست بنویس ، این پیام بصورت کاملا ناشناس ارسال میشه :)",
                        'reply_markup' => $back
                    ]);
                    $connect->query("UPDATE `user` SET `step` = 'send_pm|$user_id' WHERE `id` = '$from_id' LIMIT 1");
                } else {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "ببخشید! مخاطب پیام بلاکت کرده.
            
            چه کاری برات انجام بدم؟",
                        'reply_markup' => $home
                    ]);
                }
            } else {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "مخاطبت ربات رو خاموش کرده و پیام بهش نرسید!

چه کاری برات انجام بدم؟",
                    'reply_markup' => $home
                ]);
                $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
            }
        }
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "اینکه آدم گاهی با خودش حرف بزنه خوبه ، ولی اینجا نمیتونی به خودت پیام ناشناس بفرستی ! :)

چه کاری برات انجام بدم؟",
            'reply_markup' => $home
        ]);
        $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    }
}
//===========================// block //===========================//
if (mysqli_num_rows($block) > 0) exit();
if (file_exists('bot') and !$admin['admin'] == $from_id and $tc == 'private')
    return bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💎 ربات جهت بروزرسانی خاموش است ! لطفا پیام مجدد ارسال نکنید.",
    ]);
//===========================// join checker //===========================//
if (check_join("$from_id") != 'true') {
    is_join("$from_id");
    exit;
}
//===========================// update //===========================//
if ($message && $tc == "private") {
    $connect->query("UPDATE `user` SET `name` = '$first_name' WHERE `id` = '$from_id' LIMIT 1");
}

if ($message) {
    $connect->query("UPDATE `user` SET `update_at` = '$timestamp' WHERE `id` = '$from_id' LIMIT 1");
}
//===========================// start //===========================//
if ($text == "/start" and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => $start,
        'reply_to_message_id' => $message_id,
        'reply_markup' => $home
    ]);
    if ($user['id'] != true) {
        $connect->query("INSERT INTO `user` (`id` , `create_at`) VALUES ('$from_id' , '$timestamp')");
    }
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    exit;
}

if (strpos($text, 'آیرا تیم') !== false or strpos($text, 'ایرا تیم') !== false or strpos($text, 'IRA Team') !== false) {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "تیم برنامه‌نویسی آیرا تیم یک تیم متخصص و حرفه‌ای در زمینه تولید و توسعه ربات‌های تلگرامی است که بیش از پنج سال تجربه در این زمینه دارد. این تیم تاکنون با افراد زیادی همکاری کرده و به دلیل تخصص و تجربه خود، فعالیت بسیار قوی و گسترده‌ای در این حوزه داشته است.

یکی از ویژگی‌های بارز آیرا تیم، امنیت بالای ربات‌های تلگرامی است که تأمین این امنیت برای کاربران آنها از اهمیت زیادی برخوردار است. این تیم با اعمال استانداردهای امنیتی بین‌المللی و استفاده از بهترین روش‌ها و پروتکل‌های امنیتی، به حفظ اطلاعات و حریم خصوصی کاربران اهمیت زیادی می‌دهد.

همچنین، آیرا تیم به بهبود عملکرد و قابلیت‌های ربات‌های تلگرامی متمرکز است و با ارائه خدمات ویژه و سفارشی به مشتریان، توانسته است جایگاه ویژه‌ای در این صنعت کسب کند. توانایی این تیم در طراحی و پیاده‌سازی ربات‌هایی با عملکرد بی‌نقص، باعث شناخته‌شدن آنها به عنوان یکی از بهترین تیم‌ها در زمینه برنامه‌نویسی ربات‌های تلگرامی شده است.

در نهایت، می‌توان گفت که آیرا تیم با تمرکز بر کیفیت، امنیت و خدمات ویژه، یکی از تیم‌های برتر و معتبر در زمینه برنامه‌نویسی ربات‌های تلگرامی است که توانسته است اعتماد و رضایت بسیاری از مشتریان خود را جلب کند.",
    ]);
    exit;
}
//=======================// back //=======================//
if ($text == '🔙 بازگشت' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => $start,
        'reply_to_message_id' => $message_id,
        'reply_markup' => $home
    ]);
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    exit;
}
//=======================// Main //=======================//
if ($text == '💌 به مخاطب خاصم وصلم کن' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "برای اینکه بتونم به مخاطب خاصت بطور ناشناس وصلت کنم، یکی از این ۲ کار رو انجام بده:

راه اول 👈 : Username@ یا همون آیدی تلگرام اون شخص رو الان وارد ربات کن!

راه دوم 👈 : آیدی عددی اون شخص رو بفرست!",
        'reply_markup' => $back
    ]);
    $connect->query("UPDATE `user` SET `step` = 'connect' WHERE `id` = '$from_id' LIMIT 1");
}

if ($user['step'] == 'connect' && $tc == 'private' && $text != "🔙 بازگشت" && $text != "/start") {
    if (strpos($text, "@") === 0) {
        $response = bot('getChat', ['chat_id' => $text]);
        if (isset($response->result->id)) {
            $user_id = $response->result->id;
            if ($user_id != $from_id) {
                $user_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$user_id' LIMIT 1"));
                if ($user_check['id'] == true) {
                    if ($user_check['silent'] == 'false') {
                        $block_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `block_user` WHERE `from` = '$user_id' AND `to` = '$from_id' LIMIT 1"));
                        if (!$block_check) {
                            bot('sendmessage', [
                                'chat_id' => $chat_id,
                                'text' => "در حال ارسال پیام ناشناس به {$user_check['name']} هستی!

با خیال راحت هر حرف یا انتقادی که تو دلت هست بنویس ، این پیام بصورت کاملا ناشناس ارسال میشه :)",
                                'reply_markup' => $back
                            ]);
                            $connect->query("UPDATE `user` SET `step` = 'send_pm|$text' WHERE `id` = '$user_id' LIMIT 1");
                        } else {
                            bot('sendmessage', [
                                'chat_id' => $chat_id,
                                'text' => "ببخشید! مخاطب پیام بلاکت کرده.

چه کاری برات انجام بدم؟",
                                'reply_markup' => $home
                            ]);
                        }
                    } else {
                        bot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "مخاطبت ربات رو خاموش کرده و پیام بهش نرسید!

چه کاری برات انجام بدم؟",
                            'reply_markup' => $home
                        ]);
                        $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
                    }
                } elseif ($user_check['id'] != true) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "متاسفانه مخاطبت الان عضو ربات نیست!

چطوره یه جوری لینک ربات رو بهش برسونی تا بیاد و عضو بشه؟ مثلا لینک خودت رو بهش بفرستی یا اگه جزء دنبال کننده‌های اینستاگرامته لینکت رو در اینستاگرامت بذاری.

برای دریافت لینک 👈 /link",
                        'reply_markup' => $home
                    ]);
                    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
                }
            } else {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "اینکه آدم گاهی با خودش حرف بزنه خوبه ، ولی اینجا نمیتونی به خودت پیام ناشناس بفرستی ! :)

چه کاری برات انجام بدم؟",
                    'reply_markup' => $home
                ]);
                $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
            }
        }
    } elseif (is_numeric($text)) {
        if ($text != $from_id) {
            $user_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$text' LIMIT 1"));
            if ($user_check['id'] == true) {
                if ($user_check['silent'] == 'false') {
                    $block_check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `block_user` WHERE `from` = '$text' AND `to` = '$from_id' LIMIT 1"));
                    if (!$block_check) {
                        bot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "در حال ارسال پیام ناشناس به {$user_check['name']} هستی!

با خیال راحت هر حرف یا انتقادی که تو دلت هست بنویس ، این پیام بصورت کاملا ناشناس ارسال میشه :)",
                            'reply_markup' => $back
                        ]);
                        $connect->query("UPDATE `user` SET `step` = 'send_pm|$text' WHERE `id` = '$from_id' LIMIT 1");
                    } else {
                        bot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "ببخشید! مخاطب پیام بلاکت کرده.

چه کاری برات انجام بدم؟",
                            'reply_markup' => $home
                        ]);
                    }
                } else {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "مخاطبت ربات رو خاموش کرده و پیام بهش نرسید!

چه کاری برات انجام بدم؟",
                        'reply_markup' => $home
                    ]);
                }
            } elseif ($user_check['id'] != true) {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "متاسفانه مخاطبت الان عضو ربات نیست!

چطوره یه جوری لینک ربات رو بهش برسونی تا بیاد و عضو بشه؟ مثلا لینک خودت رو بهش بفرستی یا اگه جزء دنبال کننده‌های اینستاگرامته لینکت رو در اینستاگرامت بذاری.

برای دریافت لینک 👈 /link",
                    'reply_markup' => $home
                ]);
                $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
            }
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "اینکه آدم گاهی با خودش حرف بزنه خوبه ، ولی اینجا نمیتونی به خودت پیام ناشناس بفرستی ! :)

چه کاری برات انجام بدم؟",
                'reply_markup' => $home
            ]);
            $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
        }
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "متاسفانه مخاطبت الان عضو ربات نیست!

چطوره یه جوری لینک ربات رو بهش برسونی تا بیاد و عضو بشه؟ مثلا لینک خودت رو بهش بفرستی یا اگه جزء دنبال کننده‌های اینستاگرامته لینکت رو در اینستاگرامت بذاری.

برای دریافت لینک 👈 /link",
            'reply_markup' => $back
        ]);
        $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    }
}

if (strpos($user['step'], "send_pm|") !== false && $tc == 'private' && $text != "🔙 بازگشت" && strpos($text, "/start") === false) {
    $get_data = explode("|", $user['step']);
    $user_id = $get_data[1];
    $msg_id = $message_id;

    if (isset($message->video)) {
        $file_id = $message->video->file_id;
        $type = 'video';
    }
    if (isset($message->document)) {
        $file_id = $message->document->file_id;
        $type = 'document';
    }
    if (isset($message->photo)) {
        $photo = $message->photo;
        $file_id = $photo[count($photo) - 1]->file_id;
        $type = 'photo';
    }
    if (isset($message->voice)) {
        $file_id = $message->voice->file_id;
        $type = 'voice';
    }
    if (isset($message->audio)) {
        $file_id = $message->audio->file_id;
        $type = 'audio';
    }
    if (isset($message->sticker)) {
        $file_id = $message->sticker->file_id;
        $type = 'sticker';
    }
    $caption = $message->caption;

    bot('sendmessage', [
        'chat_id' => $user_id,
        'text' => "📬 شما یک پیام ناشناس جدید دارید !

جهت دریافت کلیک کنید 👈 /newmsg",
    ]);
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام شما ارسال شد 😊

چه کاری برات انجام بدم؟",
        'reply_markup' => $home
    ]);
    if ($type != "video" && $type != "document" && $type != "photo" && $type != "voice" && $type != "audio" && $type != "sticker") {
        $connect->query("INSERT INTO `messages` (`from` , `to` , `message` , `status` , `message_id` , `time` , `date`) VALUES ('$from_id' , '$user_id' , '$text' , 'unseen' , '$msg_id' , '$time' , '$date')");
    } else {
        $connect->query("INSERT INTO `messages` (`from` , `to` , `message` , `attachment` , `attachment_type` , `status` , `message_id` , `time` , `date`) VALUES ('$from_id' , '$user_id' , '$caption' , '$file_id' , '$type' , 'unseen' , '$msg_id' , '$time' , '$date')");
    }
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}

if (strpos($user['step'], "send_answer|") !== false && $tc == 'private' && $text != "🔙 بازگشت" && $text != "/start") {
    $get_data = explode("|", $user['step']);
    $msg_id = $get_data[1];
    $msg_info = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `messages` WHERE `id` = '$msg_id' LIMIT 1"));
    $msg_iiiiidddddd = $message_id;

    if (isset($message->video)) {
        $file_id = $message->video->file_id;
        $type = 'video';
    }
    if (isset($message->document)) {
        $file_id = $message->document->file_id;
        $type = 'document';
    }
    if (isset($message->photo)) {
        $photo = $message->photo;
        $file_id = $photo[count($photo) - 1]->file_id;
        $type = 'photo';
    }
    if (isset($message->voice)) {
        $file_id = $message->voice->file_id;
        $type = 'voice';
    }
    if (isset($message->audio)) {
        $file_id = $message->audio->file_id;
        $type = 'audio';
    }
    if (isset($message->sticker)) {
        $file_id = $message->sticker->file_id;
        $type = 'sticker';
    }
    $caption = $message->caption;

    bot('sendmessage', [
        'chat_id' => $msg_info['from'],
        'text' => "📬 شما یک پیام ناشناس جدید دارید !

جهت دریافت کلیک کنید 👈 /newmsg",
    ]);
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام شما ارسال شد 😊

چه کاری برات انجام بدم؟",
        'reply_markup' => $home
    ]);
    if ($type != "video" && $type != "document" && $type != "photo" && $type != "voice" && $type != "audio" && $type != "sticker") {
        $connect->query("INSERT INTO `messages` (`from` , `to` , `message` , `status` , `message_id` , `reply_message`  , `time` , `date`) VALUES ('$from_id' , '{$msg_info['from']}' , '$text' , 'unseen' , '$msg_iiiiidddddd' , '{$msg_info['message_id']}' , '$time' , '$date')");
    } else {
        $connect->query("INSERT INTO `messages` (`from` , `to` , `message` , `attachment` , `attachment_type` , `status` , `message_id` , `reply_message`  , `time` , `date`) VALUES ('$from_id' , '{$msg_info['from']}' , '$caption' , '$file_id' , '$type' , 'unseen' , '$msg_iiiiidddddd' , '{$msg_info['message_id']}' , '$time' , '$date')");
    }
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}
//======// لینک ناشناس //======//
if (($text == '📬 لینک ناشناس من' or $text == "/link") and $tc == 'private') {
    if ($user['link'] != null) {
        $user_link = $user['link'];
    } elseif ($user['link'] == null) {
        $user_link = user_link('it');
        $connect->query("UPDATE `user` SET `link` = '$user_link' WHERE `id` = '$from_id' LIMIT 1");
    }
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "سلام $first_name هستم ✋️

لینک زیر رو لمس کن و هر حرفی که تو دلت هست یا هر انتقادی که نسبت به من داری رو با خیال راحت بنویس و بفرست. بدون اینکه از اسمت باخبر بشم پیامت به من می‌رسه. خودتم می‌تونی امتحان کنی و از بقیه بخوای راحت و ناشناس بهت پیام بفرستن، حرفای خیلی جالبی می‌شنوی! 😉

👇👇
https://telegram.me/$usernamebot?start=$user_link",
        'parse_mode' => 'markdown',
        'disable_web_page_preview' => true
    ]);
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "☝️ پیام بالا رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
        'parse_mode' => 'markdown',
        'reply_markup' => $home
    ]);
}
//======// مدیریت حساب //======//
if ($text == '👤 مدیریت حساب' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👤 برای مدیریت حساب خود از منوی زیر اقدام کنید 👇",
        'parse_mode' => 'markdown',
        'reply_markup' => $manage_account
    ]);
}
//======// اطلاعات حساب //======//
if ($text == '👤 اطلاعات حساب' and $tc == 'private') {
    if ($user['silent'] == 'false') {
        $silent_mode = "❌";
    } elseif ($user['silent'] == 'true') {
        $silent_mode = "✅";
    }
    if ($user['copy'] == 'false') {
        $copy_mode = "❌";
    } elseif ($user['copy'] == 'true') {
        $copy_mode = "✅";
    }
    $id = bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👤 اطلاعات حساب شما (`$from_id`) به شرح زیر میباشد:

✏️ نام شما: $first_name
💎 آیدی عددی شما: $from_id
💴 موجودی شما: " . number_format($user['amount']),
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "[$silent_mode]", 'callback_data' => "change_status|silent"], ['text' => "😴 حالت سایلنت", 'callback_data' => "change_status|silent"]],
                [['text' => "[$copy_mode]", 'callback_data' => "change_status|copy"], ['text' => "🔰 حالت کپی رایت", 'callback_data' => "change_status|copy"]],
                [['text' => "📅 : $date", 'callback_data' => "none"], ['text' => "🗓 : $ToDay", 'callback_data' => "none"], ['text' => "⏰ : $time", 'callback_data' => "none"]],
            ]
        ])
    ])->result->message_id;
    bot('editMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "[$silent_mode]", 'callback_data' => "change_status|silent|$id"], ['text' => "😴 حالت سایلنت", 'callback_data' => "change_status|silent|$id"]],
                [['text' => "[$copy_mode]", 'callback_data' => "change_status|copy|$id"], ['text' => "🔰 حالت کپی رایت", 'callback_data' => "change_status|copy|$id"]],
                [['text' => "📅 : $date", 'callback_data' => "none"], ['text' => "🗓 : $ToDay", 'callback_data' => "none"], ['text' => "⏰ : $time", 'callback_data' => "none"]],
            ]
        ])
    ]);
}
//======// لینک شخصی //======//
if ($text == '💎 لینک شخصی' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💎 آیدی مورد نظر را مانند نمونه ارسال کنید (بیشتر از 4 حرف)

👉 UserName",
        'parse_mode' => 'markdown',
        'reply_markup' => $back
    ]);
    $connect->query("UPDATE `user` SET `step` = 'set_link' WHERE `id` = '$from_id' LIMIT 1");
}

if ($user['step'] == 'set_link' && $tc == 'private' && $text != "🔙 بازگشت" && $text != "/start") {
    if (preg_match('/^[a-zA-Z]+$/', $text)) {
        if (strlen($text) >= 4) {
            if (strlen($text) > $start_token) {
                $check_id = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `link` = '$text' LIMIT 1"));
                if ($check_id['link'] != true) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "✅ شما آیدی $text را با موفقیت دریافت کردید!",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => $back
                    ]);
                    $connect->query("UPDATE `user` SET `link` = '$text' WHERE id = '$from_id' LIMIT 1");
                } elseif ($check_id['link'] == true) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "⚠️ آیدی مورد نظر آزاد نیست!",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => $back
                    ]);
                }
            } elseif (strlen($text) <= $start_token) {
                $check_id = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `link` = '$text' LIMIT 1"));
                if ($check_id['link'] != true) {
                    $amount_buy_id = (($start_token - strlen($text)) + 1) * $token_amount;
                    $amount_buy_id_show = number_format($amount_buy_id);
                    if ($user['amount'] >= $amount_buy_id) {
                        bot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "✅ شما آیدی $text را با موفقیت به مبلغ $amount_buy_id_show تومان خریداری کردید!",
                            'reply_to_message_id' => $message_id,
                            'reply_markup' => $back
                        ]);
                        $connect->query("UPDATE `user` SET `link` = '$text' WHERE id = '$from_id' LIMIT 1");
                    } else {
                        bot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "⚠️ برای دریافت این آیدی نیاز به $amount_buy_id_show تومان موجودی دارید!",
                            'reply_to_message_id' => $message_id,
                            'reply_markup' => $back
                        ]);
                    }
                } elseif ($check_id['link'] == true) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "⚠️ آیدی مورد نظر آزاد نیست!",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => $back
                    ]);
                }
            }
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "❗️ حداقل باید 4 حرف وارد کنید.",
                'reply_to_message_id' => $message_id,
                'reply_markup' => $back
            ]);
        }
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❗️ آیدی نامعتبر.",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
    }
}
//======// شارژ حساب //======//
if ($text == '💳 شارژ حساب' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👇🏻 برای افزایش موجودی حساب مبلغ موردنظر خود را به تومان وارد نمایید

❗️ توجه کنید که مبلغ را به عدد وارد کنید و حداقل میتوانید $hadaddfuns و حداکثر $maxaddfuns تومان حساب خود را شارژ کنید",
        'parse_mode' => 'markdown',
        'reply_markup' => $back
    ]);
    $connect->query("UPDATE `user` SET `step` = 'pay' WHERE `id` = '$from_id' LIMIT 1");
}

if ($user['step'] == 'pay' && $tc == 'private' && $text != "🔙 بازگشت" && $text != "/start") {
    if ($text >= $hadaddfuns and $text <= $maxaddfuns) {
        $ntext = number_format($text);
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => '⏳ در حال ساخت فاکتور پرداخت ...',
            'reply_markup' => $home
        ]);
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✅ فاکتور افزایش موجودی با مبلغ $ntext تومان با موفقیت برای شما ساخته شد

👇🏻 پرای پرداخت کافیست از دکمه زیر استفاده کنید",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "💳 پرداخت $ntext تومان (زرین پال) 💎", 'url' => "$web/pay/zarinpal/?amount=$text&user_id=$from_id"]],
                ]
            ])
        ]);
        $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❗️ خطا ، پیام شما دارای عدد ورودی نادرست است
👇🏻 برای افزایش موجودی حساب مبلغ موردنظر خود را به تومان وارد نمایید

💡 توجه کنید که مبلغ را به عدد وارد کنید و حداقل میتوانید $hadaddfuns و حداکثر $maxaddfuns تومان حساب خود را شارژ کنید",
            'reply_to_message_id' => $message_id,
        ]);
    }
}
//======// راهنما و پشتیبانی //======//
if ($text == '📚 راهنما و پشتیبانی ☎️' and $tc == 'private') {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👇 گزینه مورد نظر را از منوی زیر انتخاب کنید",
        'parse_mode' => 'markdown',
        'reply_markup' => $help_support
    ]);
}
//======// پیام جدید //======//
if ($text == '/newmsg' and $tc == 'private') {
    $query = "SELECT * FROM `messages` WHERE `to` = '$from_id' AND `status` = 'unseen' LIMIT 10";
    $result = mysqli_query($connect, $query);
    $newmsg_count = mysqli_num_rows($result);
    $total_messages = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `messages` WHERE `to` = '$from_id' AND `status` = 'unseen'"));

    if ($newmsg_count > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $message_text = $row['message'];
            $message_id = $row['id'];
            $message_from = $row['from'];
            $message_msgid = $row['message_id'];
            $message_reply_to = $row['reply_message'];
            $message_attachment = $row['attachment'];
            $message_attachment_type = $row['attachment_type'];
            $user_from = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `user` WHERE `id` = '$message_from' LIMIT 1"));

            if ($message_attachment == null && $message_attachment_type == null) {

                $id = bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => $message_text,
                    'protect_content' => $user_from['copy'],
                    'reply_to_message_id' => $message_reply_to,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "⛔️ بلاک", 'callback_data' => "block|$message_from"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$message_id"]],
                        ]
                    ])
                ])->result->message_id;
                bot('editMessageReplyMarkup', [
                    'chat_id' => $chat_id,
                    'message_id' => $id,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "⛔️ بلاک", 'callback_data' => "block|$message_from|$id|$message_id"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$message_id|$id"]],
                        ]
                    ])
                ]);
            } else {
                $id = bot('send' . $message_attachment_type, [
                    'chat_id' => $chat_id,
                    "$message_attachment_type" => $message_attachment,
                    'caption' => $message_text,
                    'protect_content' => $user_from['copy'],
                    'reply_to_message_id' => $message_reply_to,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "⛔️ بلاک", 'callback_data' => "block|$message_from"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$message_id"]],
                        ]
                    ])
                ])->result->message_id;
                bot('editMessageReplyMarkup', [
                    'chat_id' => $chat_id,
                    'message_id' => $id,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "⛔️ بلاک", 'callback_data' => "block|$message_from|$id|$message_id"], ['text' => "✍🏻 پاسخ", 'callback_data' => "answer|$message_id|$id"]],
                        ]
                    ])
                ]);
            }

            bot('sendmessage', [
                'chat_id' => $message_from,
                'text' => "این پیامت ☝️ رو دید!",
                'reply_to_message_id' => $message_msgid,
            ]);

            mysqli_query($connect, "UPDATE `messages` SET `status` = 'seen' WHERE `id` = '$message_id'");
        }
        if ($total_messages >= 10) {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "✅ 10 تا از $total_messages پیام جدید شما ارسال شد.",
                'parse_mode' => 'markdown',
            ]);
        }
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "پیام نخونده‌ای نداری !

چطوره با زدن این دستور 👈 /link لینک خودت رو بگیری و به دوستات یا گروه‌ها بفرستی تا بتونند بهت پیام ناشناس بفرستند؟ 😊",
            'parse_mode' => 'markdown',
        ]);
    }
}

//===========================// panel admin //===========================
if (($text == '/panel' || $text == '👤 پنل مدیریت 👤' || $text == 'برگشت 🔙') && $tc == 'private' && $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👋 ادمین عزیز به پنل مدیریت ربات خوش آمدید.",
        'reply_markup' => $admin_panel
    ]);
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}
//====================// آمار ربات //====================//
if ($text == '📊 آمار ربات 📊' and $tc == 'private' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    $alluser = mysqli_num_rows(mysqli_query($connect, "select `id` from `user`"));
    $allblock = mysqli_num_rows(mysqli_query($connect, "select `id` from `block`"));
    $load = sys_getloadavg();

    $serverIP = gethostbyname(gethostname());
    $starttime = microtime(true);
    $socket = fsockopen($serverIP, 80, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;
    if (!$socket) {
        $status = -1;
    } else {
        fclose($socket);
        $server_ping = ($stoptime - $starttime) * 1000;
        $server_ping = round($server_ping, 2);
    }
    $mem = number_format(memory_get_usage());
    $ver = phpversion();
    $ip_add = $_SERVER['SERVER_ADDR'];
    $domain = $_SERVER['SERVER_NAME'];
    $onlineUsers = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `update_at` > $timestamp - 60")) ?: 0);
    $onlineUsers1 = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `update_at` > $timestamp - 86400")) ?: 0);
    $onlineUsers2 = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `update_at` > $timestamp - 604800")) ?: 0);
    $onlineUsers3 = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `update_at` > $timestamp - 2592000")) ?: 0);
    $hourlyUsers = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `create_at` > $timestamp - 3600")) ?: 0);
    $dailyUsers = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `create_at` > $timestamp - 86400")) ?: 0);
    $weeklyUsers = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `create_at` > $timestamp - 604800")) ?: 0);
    $monthlyUsers = number_format(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `user` WHERE `create_at` > $timestamp - 2592000")) ?: 0);
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📊 آمار ربات شما به شرح زیر است:

━━━━━━━━━━━━━━━━━
👥 <b>تعداد کاربران :</b> <code>$alluser</code>
⛔️ <b>کاربران مسدود :</b> <code>$allblock</code>
        
💎 <b>فعالیت کاربران ربات شما به شرح زیر میباشد</b> 👇
        
🟢 <b>کاربران آنلاین :</b> <code>$onlineUsers</code> 
🕛 <b>24 ساعت گذشته :</b> <code>$onlineUsers1</code> 
📅 <b>7 روز گذشته :</b> <code>$onlineUsers2</code> 
🗓 <b>31 روز گذشته :</b> <code>$onlineUsers3</code>
        
💎 <b>آمار کاربران جدید ربات شما به شرح زیر میباشد</b> 👇
        
🕛 <b>24 ساعت گذشته:</b> <code>$dailyUsers</code> 
📅 <b>7 روز گذشته:</b> <code>$weeklyUsers</code>
🗓 <b>31 روز گذشته:</b> <code>$monthlyUsers</code>
━━━━━━━━━━━━━━━━━
📶 <b>Server Ping :</b> <code>$server_ping</code>
🎛 <b>LoadAvg :</b> <code>$load[0]</code>
        
📍 <b>IP Address :</b> <code>$ip_add</code>
🗂 <b>Memory Usage :</b> <code>$mem</code>
        
💯 <b>PHP Version :</b> <code>$ver</code>
━━━━━━━━━━━━━━━━━
        
🌐 <b>Domain :</b> <code>$domain</code>",
        'parse_mode' => "HTML",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "📅 : $date", 'callback_data' => "none"], ['text' => "🗓 : $ToDay", 'callback_data' => "none"], ['text' => "⏰ : $time", 'callback_data' => "none"]],
            ]
        ])
    ]);
}
//===================// تغییر وضعیت ربات //===================//
if ($text == '🟢 ربات روشن' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🟢 ربات روشن شد",
    ]);
    unlink('bot');
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}
if ($text == '🔴 ربات خاموش' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🔴 ربات خاموش شد",
    ]);
    touch('bot');
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}
//===================// مدیریت قفل ها //===================//
if ($text == "📣 مدیریت قفل ها") {
    if ($admin['admin'] == $from_id) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❗️ به بخش تنظیم چنل های قفل خوش آمدید.

💯 برای حذف چنل، از بخش لیست چنل چنل مورد نظر را حذف کنید .",
            'parse_mode' => "HTML",
            'reply_markup' => $manage_channel
        ]);
    }
}
//=====// افزودن چنل //=====//
if ($text == "➕ افزودن چنل") {
    if ($admin['admin'] == $from_id) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "لطفا نوع چنلی که میخواهید اضافه کنید را از کیبورد انتخاب کنید :",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "عمومی"], ['text' => "خصوصی"]],
                    [['text' => "👤 پنل مدیریت 👤"]],
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");
    }
}
//=====// چنل عمومی //=====//
if ($text == "عمومی" && $user['step'] == "addch1") {
    if ($admin['admin'] == $from_id) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "لطفا یوزرنیم چنل عمومی را بدون @ ارسال کنید ( ربات را قبل ارسال بر ان چنل آدمین کرده باشید )",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "👤 پنل مدیریت 👤"]],
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'addchpub' WHERE id = '$from_id' LIMIT 1");
    }
}
if ($user['step'] == "addchpub" && $text != "👤 پنل مدیریت 👤") {
    if ($admin['admin'] == $from_id) {
        $textt = str_replace("@", '', $text);
        $texttt = "@" . $textt;
        $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$texttt' LIMIT 1"));
        if ($ch['link'] == null) {
            $admini = getChatstats("@$textt", API_KEY);
            if ($admini == true) {
                $linkk = "https://t.me/$textt";
                $connect->query("INSERT INTO channels (idoruser , link) VALUES ('$texttt', '$linkk')");
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "چنل @$textt با موفقیت افزوده شد .",
                    'parse_mode' => "HTML",
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [['text' => "➕ افزودن چنل"]],
                            [['text' => "👤 پنل مدیریت 👤"], ['text' => "📚 لیست چنل ها"]],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");
            } else {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "خطا ! ربات بر چنل @$textt آدمین نیست !

ابتدا ربات را ادمین و سپس ارسال کنید تا افزوده شود.",
                    'parse_mode' => "HTML",
                ]);
            }
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "خطا ! قبلا چنلی با این ایدی ثبت شده !

لطفا دوباره ارسال فرمایید :",
                'parse_mode' => "HTML",
            ]);
        }
    }
}
//=====// چنل خصوصی //=====//
if ($text == "خصوصی" && $user['step'] == "addch1") {
    if ($admin['admin'] == $from_id) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "لطفا آیدی عددی چنل خصوصی را ارسال کنید .
نمونه ایدی عددی چنل : 
-1009876262727
ربات را قبل ارسال حتما ادمین کرده باشید.",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "👤 پنل مدیریت 👤"]],
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'addcpr' WHERE id = '$from_id' LIMIT 1");
    }
}
if ($user['step'] == "addcpr" && $text != "👤 پنل مدیریت 👤" && !$data) {
    if ($admin['admin'] == $from_id) {
        $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$text' LIMIT 1"));
        if ($ch['link'] == null) {
            $admini = getChatstats($text, API_KEY);
            if (strpos($text, "-100") !== false && $admini == true) {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "لطفا لینک خصوصی دعوت را ارسال کنید :",
                    'parse_mode' => "HTML",
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [['text' => "👤 پنل مدیریت 👤"]],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
                $connect->query("UPDATE user SET step = 'addchpr1' WHERE id = '$from_id' LIMIT 1");
            } else {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "خطا ! ربات بر چنل $text آدمین نیست و یا ایدی ارسالی حاوی -100 نیست.

ابتدا ربات را ادمین و سپس ارسال کنید تا افزوده شود.",
                    'parse_mode' => "HTML",
                ]);
            }
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "خطا ! قبلا چنلی با این ایدی ثبت شده !

لطفا دوباره ارسال فرمایید :",
                'parse_mode' => "HTML",
            ]);
        }
    }
}
if ($user['step'] == "addchpr1" && $text != "👤 پنل مدیریت 👤" && !$data) {
    if ($admin['admin'] == $from_id) {
        if (strpos($text, "://t.me/") !== false) {
            $idus = $user['step2'];
            $connect->query("INSERT INTO channels (idoruser , link) VALUES ('$idus', '$text')");
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "چنل با موفقیت افزوده شد .",
                'parse_mode' => "HTML",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => "➕ افزودن چنل"]],
                        [['text' => "👤 پنل مدیریت 👤"], ['text' => "📚 لیست چنل ها"]],
                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "خطا! لینک ارسالی اشتباه است !

لطفا دوباره ارسال کنید:",
                'parse_mode' => "HTML",
            ]);
        }
    }
}
//=====// لیست چنل ها //=====//
if ($text == "📚 لیست چنل ها") {
    if ($admin['admin'] == $from_id) {
        $chs = mysqli_query($connect, "select idoruser from channels");
        $fil = mysqli_num_rows($chs);
        if ($fil != 0) {
            while ($row = mysqli_fetch_assoc($chs)) {
                $ar[] = $row["idoruser"];
            }
            for ($i = 0; $i <= $fil; $i++) {

                $by = $i + 1;
                $okk = $ar[$i];
                $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
                $link = $ch['link'];
                if ($link != null) {
                    $d4[] = [['text' => "چنل شماره $by", 'url' => $link], ['text' => "❌ حذف", 'callback_data' => "delc_$okk"]];
                }
            }
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "👇🏻 لیست تمام چنل های قفل",
                'parse_mode' => "HTML",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $d4
                ])
            ]);
        } else {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "❌ هیچ چنل قفلی تنظیم نشده.",
                'parse_mode' => "HTML",
            ]);
        }
    }
}
//===================// مدیریت ادمین ها //===================//
if ($text == "👤 مدیریت ادمین ها" and $tc == 'private' and ($admin['admin'] == $from_id)) {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "❗️ به بخش تنظیم ادمین خوش آمدید.

💯 برای حذف ادمین، از بخش لیست ادمین . ادمین مورد نظر را حذف کنید .",
        'parse_mode' => "HTML",
        'reply_markup' => $manage_admin
    ]);
}
//=====// افزودن ادمین //=====//
if ($text == "➕ افزودن ادمین"  and $tc == 'private' and ($admin['admin'] == $creator)) {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "لطفا آیدی عددی فرد موردنظر را وارد نمایید ✅",
        'parse_mode' => "HTML",
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => "👤 پنل مدیریت 👤"]],
            ],
            'resize_keyboard' => true,
            'input_field_placeholder' => "$from_id"

        ])
    ]);
    $connect->query("UPDATE user SET step = 'addadmin' WHERE id = '$from_id' LIMIT 1");
}
if ($user['step'] == "addadmin" && $text != "👤 پنل مدیریت 👤"  and $tc == 'private' and ($admin['admin'] == $from_id)) {
    $ad = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM admin WHERE admin = '$text' LIMIT 1"));
    if ($ad['admin'] == null) {
        $connect->query("INSERT INTO admin (admin) VALUES ('$text')");
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "کاربر $text با موفقیت افزوده شد .",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "➕ افزودن ادمین"]],
                    [['text' => "👤 پنل مدیریت 👤"], ['text' => "📚 لیست ادمین ها"]],
                ],
                'resize_keyboard' => true,
                'input_field_placeholder' => "$from_id"

            ])
        ]);
        $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "ایدی عددی کاربر <code>$text</code> در لیست ادمین ها وجود دارد",
            'parse_mode' => "HTML",
        ]);
        $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    }
}
//=====// لیست ادمین ها //=====//
if ($text == '📚 لیست ادمین ها' and $tc == 'private' and ($admin['admin'] == $from_id)) {
    $chs = mysqli_query($connect, "select admin from admin");
    $fil = mysqli_num_rows($chs);
    if ($fil != 0) {
        while ($row = mysqli_fetch_assoc($chs)) {
            $ar[] = $row["admin"];
        }
        for ($i = 0; $i <= $fil; $i++) {

            $by = $i + 1;
            $okk = $ar[$i];
            $ch = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM admin WHERE admin = '$okk' LIMIT 1"));
            $link = $ch['admin'];
            if ($link != null) {
                $d4[] = [['text' => "$link", 'callback_data' => 'ok'], ['text' => "❌ حذف", 'callback_data' => "delad_$okk"]];
            }
        }
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "👇🏻 لیست تمام ادمین ها",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => $d4
            ])
        ]);
    } else {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ هیچ ادمینی  تنظیم نشده.",
            'parse_mode' => "HTML",
        ]);
    }
}
//===================// بخش همگانی //===================//
if ($text == '💬 پیام همگانی' and $tc == 'private' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    if ($send['admin'] == null) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "👨🏻‍💻 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس باشد]  همچنین میتوانید رسانه را همراه با کپشن [متن چسبیده به رسانه ارسال کنید]",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "برگشت 🔙"]]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE `user` SET `step` = 'sendtoall' WHERE `id` = '$from_id' LIMIT 1");
    } else {
        $tddd = $send['sended'];
        $users = mysqli_query($connect, "select id from `user`");
        $fil = mysqli_num_rows($users);
        $tfrigh = $fil - $tddd;
        $min = Takhmin($tfrigh);
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ خطا برای انجام عملیات همگانی

ادمین زیر اقدام به همگانی کرده و هنوز همگانی به اتمام نرسیده ، لطفا تا پایان همگانی قبلی صبر کنید .",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "👤 {$send['sended']}", 'callback_data' => "none"]],
                    [['text' => "🔹 تعداد افراد ارسال شده : $tddd", 'callback_data' => "none"]],
                    [['text' => "🔸 زمان تخمینی ارسال : $min دقیقه (باقیمانده)", 'callback_data' => "none"]],
                ]
            ])
        ]);
    }
}
if ($text == '↗️ فوروارد همگانی' and $tc == 'private' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    if ($send['admin'] == null) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "👨🏻‍💻 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => "برگشت 🔙"]]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE `user` SET `step` = 'fortoall' WHERE `id` = '$from_id' LIMIT 1");
    } else {
        $tddd = $send['sended'];
        $users = mysqli_query($connect, "select id from `user`");
        $fil = mysqli_num_rows($users);
        $tfrigh = $fil - $tddd;
        $min = Takhmin($tfrigh);
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ خطا برای انجام عملیات همگانی

ادمین زیر اقدام به همگانی کرده و هنوز همگانی به اتمام نرسیده ، لطفا تا پایان همگانی قبلی صبر کنید .",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "👤 {$send['sended']}", 'callback_data' => "none"]],
                    [['text' => "🔹 تعداد افراد ارسال شده : $tddd", 'callback_data' => "none"]],
                    [['text' => "🔸 زمان تخمینی ارسال : $min دقیقه (باقیمانده)", 'callback_data' => "none"]],
                ]
            ])
        ]);
    }
}
//===================// بلاک آنبلاک //===================//
if ($text == '⚠️ مسدود کردن' and $tc == 'private' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👨🏻‍💻 لطفا شناسه کاربری فرد را ارسال کنید",
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => "برگشت 🔙"]]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE `user` SET `step` = 'block' WHERE `id` = '$from_id' LIMIT 1");
}
if ($text == '❌ حذف مسدودیت' and $tc == 'private' and $admin['admin'] == $from_id) {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👨🏻‍💻 لطفا شناسه کاربری فرد را ارسال کنید",
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => "برگشت 🔙"]]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE `user` SET `step` = 'unblock' WHERE `id` = '$from_id' LIMIT 1");
}
//===========================// admin step //===========================//
if ($user['step'] == 'block' && $tc == 'private') {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✅ فرد با موفقیت مسدود شد",
    ]);
    $connect->query("INSERT INTO `block` (`id`) VALUES ('$text')");
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}
if ($user['step'] == 'unblock' && $tc == 'private') {
    $defaultusecommend = "true";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✅ فرد با موفقیت لغو مسدود شد",
    ]);
    $connect->query("DELETE FROM `block` WHERE `id` = '$text'");
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}
//===========================// همگانی //===========================//
if ($user['step'] == 'sendtoall') {
    $defaultusecommend = "true";
    $photo = $message->photo[count($message->photo) - 1]->file_id;
    $caption = $update->message->caption;
    $users = mysqli_query($connect, "select id from `user`");
    $fil = mysqli_num_rows($users);
    $min = Takhmin($fil);
    $tddd = $send['sended'];
    $id = bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📣 <i>پیام به صف ارسال قرار گرفت !</i>

✅ <b>بعد از اتمام ارسال، به شما اطلاع داده میشود.</b>

👥 تعداد اعضای ربات: <code>$fil</code> نفر

🔹 تعداد افراد ارسال شده در دکمه شیشه ای زیر، قابل مشاهده است ( خودکار ادیت میشود )",
        'parse_mode' => "HTML",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🔹 تعداد افراد ارسال شده : $tddd", 'callback_data' => "none"]],
                [['text' => "🚀 زمان تخمینی ارسال : $min دقیقه (باقیمانده)", 'callback_data' => "none"]],
            ]
        ])
    ])->result;
    $msgid22 = $id->message_id;
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    $connect->query("UPDATE `sendall` SET `step` = 'send' , `admin` = '$from_id' , `messageid` = '$msgid22' , `text` = '$text$caption' , `chat` = '$photo' LIMIT 1");
}
if ($user['step'] == 'fortoall') {
    $defaultusecommend = "true";
    $users = mysqli_query($connect, "select id from `user`");
    $fil = mysqli_num_rows($users);
    $min = Takhmin($fil);
    $tddd = $send['sended'];
    $id = bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📣 <i>پیام به صف فوروارد قرار گرفت !</i>

✅ <b>بعد از اتمام فوروارد، به شما اطلاع داده میشود.</b>
        
👥 تعداد اعضای ربات: <code>$fil</code> نفر

🔹 تعداد افراد ارسال شده در دکمه شیشه ای زیر، قابل مشاهده است ( خودکار ادیت میشود )",
        'parse_mode' => "HTML",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🔹 تعداد افراد ارسال شده : $tddd", 'callback_data' => "none"]],
                [['text' => "🚀 زمان تخمینی ارسال : $min دقیقه (باقیمانده)", 'callback_data' => "none"]],
            ]
        ])
    ])->result;
    $msgid22 = $id->message_id;
    $connect->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    $connect->query("UPDATE `sendall` SET `step` = 'forward' , `admin` = '$from_id' , `messageid` = '$msgid22' , `text` = '$message_id' , `chat` = '$chat_id' LIMIT 1");
}
/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/