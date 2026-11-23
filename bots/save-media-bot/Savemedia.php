<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
دوست عزیز این سورس کد توسط شخص @devbc کد نویسی شده
حمایت کنید اپدیت های جدید در راهه
*/

$admin = 1371365451; // اینم خب مشخصه ایدی ادمین بزار
$token = "vghggghggggggg"; // توکن رباتت بزار
$idbot = ""; // ایدی ربات بدون @
define("BOT_TOKEN", $token); // دست نزن به این
$send = -1002286039673; // ایدی کانالت بزار که میخای پیشنمایش لینک اونجا بره
$image_url = "https://t.me/Isotim/426"; // یه عکس توی کانال عمومی یا هاست اپلود کن لینکش بزار نزاشتی هم همین پیش فرض خودش هست

$update = json_decode(file_get_contents("php://input"));

$message        = $update->message ?? null;
$callback_query = $update->callback_query ?? null;

$from_id    = $message->from->id         ?? $callback_query->from->id         ?? null;
$chat_id    = $message->chat->id         ?? $callback_query->message->chat->id ?? null;
$text       = $message->text             ?? null;
$data       = $callback_query->data      ?? null;
$query_id   = $callback_query->id        ?? null;
$tc         = $message->chat->type       ?? null;
$first_name = $message->from->first_name ?? $callback_query->from->first_name ?? null;
$message_id = $message->message_id       ?? $callback_query->message->message_id ?? null;
$in_text    = $callback_query->message->text ?? null;

if (!is_dir("data")) mkdir("data");
if (!is_dir("data/media")) mkdir("data/media");
if (!is_dir("data/media_album")) mkdir("data/media_album");
if (!is_dir("data/user")) mkdir("data/user");
if (!is_dir("data/user/$from_id")) mkdir("data/user/$from_id");
if (!file_exists("data/users.txt")) file_put_contents("data/users.txt", "");
if (!file_exists("data/user/$from_id/step.txt")) file_put_contents("data/user/$from_id/step.txt", "none");
if (!is_dir("tmp")) mkdir("tmp");

$step = file_get_contents("data/user/$from_id/step.txt");

function bot($method, $datas=[]) {
    $url = "https://api.telegram.org/bot".BOT_TOKEN."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

function adminpanel($chat_id) {
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "✏️ ادمین عزیز سلام ، به پنل مدیریت خوش آمدید:

- جهت ادامه یکی از بخش های زیر را انتخاب کنید .",
        "reply_markup" => json_encode([
            "keyboard" => [
                [["text" => "📥 آپلود تکی"],['text'=> "📤 آپلود آلبومی"]],
                [["text" => "🔐 تنظیمات مربوط به ارسال"]],
                 [["text" => "🔗 تنظیمات کانال"]],
            ],
            "resize_keyboard" => true
        ])
    ]);
}


$oo = "بازگشت";
$bk = json_encode(['keyboard'=>[
[['text'=>"$oo"]],
],'resize_keyboard' =>true]);


$user_id = $from_id;
$file = "data/users.txt";
$users = [];
if (file_exists($file)) {
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
if (!in_array($user_id, $users)) {
    file_put_contents($file, $user_id . PHP_EOL, FILE_APPEND | LOCK_EX);
}


if ($from_id != $admin) {
    $channels = file_exists("data/channels.json") ? json_decode(file_get_contents("data/channels.json"), true) : [];
    $not_joined = [];

    foreach ($channels as $username => $name) {
        $res = bot("getChatMember", [
            "chat_id" => $username,
            "user_id" => $from_id
        ]);
        $status = $res["result"]["status"] ?? "";

        if (!in_array($status, ["creator", "administrator", "member"])) {
            $not_joined[$username] = $name;
        }
    }

    if (!empty($not_joined)) {
        $inline = [];
foreach ($not_joined as $username => $name) {
    $link = str_replace('@', '', $username);
    $inline[] = [["text" => "عضویت در $name", "url" => "https://t.me/$link"]];
}
        $inline[] = [["text" => "عضو شدم ✅", "callback_data" => "check_join"]];

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "برای ادامه، ابتدا در کانال‌های زیر عضو شوید:",
            "reply_markup" => json_encode(["inline_keyboard" => $inline])
        ]);
        return;
    }
}

if ($data == "check_join") {
    $channels = file_exists("data/channels.json") ? json_decode(file_get_contents("data/channels.json"), true) : [];
    $not_joined = [];

    foreach ($channels as $username => $name) {
        $res = bot("getChatMember", [
            "chat_id" => $username,
            "user_id" => $from_id
        ]);
        $status = $res["result"]["status"] ?? "";

        if (!in_array($status, ["creator", "administrator", "member"])) {
            $not_joined[$username] = $name;
        }
    }

    if (!empty($not_joined)) {
        $inline = [];
foreach ($not_joined as $username => $name) {
    $link = str_replace('@', '', $username);
    $inline[] = [["text" => "عضویت در $name", "url" => "https://t.me/$link"]];
}
        $inline[] = [["text" => "عضو شدم ✅", "callback_data" => "check_join"]];

        bot("answerCallbackQuery", ["callback_query_id" => $callback_id]);
        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $message_id,
            "text" => "هنوز در همه کانال‌ها عضو نشدی. لطفا عضو شو و دوباره امتحان کن.",
            "reply_markup" => json_encode(["inline_keyboard" => $inline])
        ]);
        return;
    } else {
        $text = "/start " . (file_get_contents("data/last_code.txt") ?: "");
    }
}


if ($from_id == $admin) {
    if ($text == "/start" or $text == $oo) {
        adminpanel($chat_id);
        file_put_contents("data/user/$from_id/step.txt", "none");
    } 
    
    if ($text == "📥 آپلود تکی"){
    file_put_contents("data/user/$from_id/step.txt", "capt");
    bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "لطفاً کپشن مدیای خود را ارسال کنید.",
            "reply_markup" => $bk,
        ]);
        exit();
    }
    
    if ($text == "📤 آپلود آلبومی"){
    file_put_contents("data/user/$from_id/step.txt", "capg");
    bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "لطفاً کپشن مدیای خود را ارسال کنید.",
            "reply_markup" => $bk,
        ]);
        exit();
    }

    if ($step == "capt" and $text != $oo) {
        file_put_contents("data/user/$from_id/step.txt", "await_media");
        file_put_contents("data/user/$from_id/cap.txt", $text);
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "لطفاً فایل مدیای خود را ارسال کنید.",
            "reply_markup" => $bk,
        ]);
        exit();
    } 
    
    
        if ($step == "capg" and $text != $oo) {
        file_put_contents("data/user/$from_id/step.txt", "await_album");
        file_put_contents("data/user/$from_id/cap.txt", $text);
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "لطفاً فایل مدیای خود را ارسال کنید.",
            "reply_markup" => $bk,
        ]);
        exit();
    } 

    if ($text == "🔐 تنظیمات مربوط به ارسال") {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "یکی از گزینه‌ها را انتخاب کن:",
            "reply_markup" => json_encode([
                "keyboard" => [
                    [["text" => "ارسال همگانی"], ["text" => "فوروارد همگانی"]],
                    [["text" => "$oo"]]
                ],
                "resize_keyboard" => true
            ])
        ]);
    } 

    if ($text == "ارسال همگانی") {      
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "متن مورد نظر را بفرست:",
            "reply_markup" => $bk,
        ]);
        file_put_contents("data/user/$from_id/step.txt", "senduser");
        exit();
    } 

    if ($text == "فوروارد همگانی") {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "پیام را فوروارد کن:",
            "reply_markup" => $bk,
        ]);
         file_put_contents("data/user/$from_id/step.txt", "foruser");
         exit();
    } 

    if ($step == "senduser" && $text != $oo) {
        $users = file("data/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ok = 0;
        $fail = 0;
        foreach ($users as $u) {
            $res = bot("sendMessage", ["chat_id" => $u, "text" => $text]);
            if (isset($res["ok"]) && $res["ok"]) $ok++; else $fail++;
        }
        file_put_contents("data/user/$from_id/step.txt", "");
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "ارسال انجام شد. موفق: $ok | ناموفق: $fail"
        ]);
    }
}


if ($from_id == $admin && file_exists("data/user/$from_id/step.txt") && file_get_contents("data/user/$from_id/step.txt") == "await_album" and $text != "پایان عملیات") {

    if (!is_dir("data/user/$from_id/album")) {
        mkdir("data/user/$from_id/album", 0777, true);
    }

    $file_id = null;
    $type = null;

    if (isset($message->photo)) {
        $file_id = end($message->photo)->file_id;
        $type = "photo";
    } elseif (isset($message->video)) {
        $file_id = $message->video->file_id;
        $type = "video";
    }

    if ($file_id && $type) {
        $rand = uniqid();
        file_put_contents("data/user/$from_id/album/$rand.json", json_encode([
            "file_id" => $file_id,
            "type" => $type
        ]));
        
$key = json_encode(['keyboard'=>[
[['text'=>"پایان عملیات"]],
],'resize_keyboard' =>true]);

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "مدیا ذخیره شد. برای پایان دکمه زیر رو کلیک کن .",
             "reply_markup" => $key,
        ]);
    } else {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "فقط عکس یا ویدیو ارسال کن."
        ]);
    }
}

if ($from_id == $admin && $text == "پایان عملیات" && file_exists("data/user/$from_id/step.txt") && file_get_contents("data/user/$from_id/step.txt") == "await_album") {
    $files = glob("data/user/$from_id/album/*.json");

    if (count($files) < 2) {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "حداقل دو مدیا باید ارسال کنی برای آلبوم."
        ]);
        return;
    }

    $code = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
    $albumData = [];

    foreach ($files as $file) {
        $albumData[] = json_decode(file_get_contents($file), true);
    }

    file_put_contents("data/media_album/$code.json", json_encode($albumData));

    array_map('unlink', $files);
    rmdir("data/user/$from_id/album");

    file_put_contents("data/user/$from_id/step.txt", "");

    $link = "https://t.me/$idbot?start=album_$code";
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "آلبوم با موفقیت ذخیره شد.

🔗 لینک آلبوم:
$link",
    ]);
    
$caption = file_get_contents ("data/user/$from_id/cap.txt");
$key = json_encode(['inline_keyboard' => [
[['text'=>"دریافت فایل",'url'=>$link]],
]]);

bot("sendPhoto", [
    "chat_id" => $send,
    "photo" => $image_url,
    "caption" => $caption,
    "parse_mode" => "HTML",
    "reply_markup" => $key,
]);
    adminpanel($chat_id);
}


if ($from_id == $admin && file_exists("data/user/$from_id/step.txt") && file_get_contents("data/user/$from_id/step.txt") == "await_media") {
$file_id = null;
$type = null;

if (isset($message->photo)) {  
    $file_id = end($message->photo)->file_id;  
    $type = "photo";  
} elseif (isset($message->video)) {  
    $file_id = $message->video->file_id;  
    $type = "video";  
} elseif (isset($message->audio)) {  
    $file_id = $message->audio->file_id;  
    $type = "audio";  
} elseif (isset($message->animation)) {  
    $file_id = $message->animation->file_id;  
    $type = "animation";  
}  

if ($file_id) {  
    $code = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);  
    file_put_contents("data/media/$code.json", json_encode([  
        "file_id" => $file_id,  
        "type" => $type  
    ]));  

    $link = "https://t.me/$idbot?start=$code";  
    bot("sendMessage", [  
        "chat_id" => $chat_id,  
        "text" => "  
        📝 فایل شما با موفقیت ذخیره گردید .

🔐 لینک فایل :
$link
"
]);

$caption = file_get_contents ("data/user/$from_id/cap.txt");
$key = json_encode(['inline_keyboard' => [
[['text'=>"دریافت فایل",'url'=>$link]],
]]);

bot("sendPhoto", [
    "chat_id" => $send,
    "photo" => $image_url,
    "caption" => $caption,
    "parse_mode" => "HTML",
    "reply_markup" => $key,
]);

file_put_contents("data/user/$from_id/step.txt", "");
} else {
bot("sendMessage", [
"chat_id" => $chat_id,
"text" => "لطفاً یک مدیای معتبر ارسال کن."
]);
file_put_contents("data/user/$from_id/step.txt", "none");
}
}

if ($from_id == $admin && file_exists("data/user/$from_id/step.txt") && file_get_contents("data/user/$from_id/step.txt") == "foruser" && isset($message->forward_from) && $text != $oo) {
    $users = file("data/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $ok = 0;
    $fail = 0;
    foreach ($users as $u) {
        $res = bot("forwardMessage", [
            "chat_id" => $u,
            "from_chat_id" => $chat_id,
            "message_id" => $message_id
        ]);
        if (isset($res["ok"]) && $res["ok"]) $ok++; else $fail++;
    }
    file_put_contents("data/user/$from_id/step.txt", "");
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "فوروارد انجام شد. موفق: $ok | ناموفق: $fail"
    ]);
}

if ($text == "🔗 تنظیمات کانال" && $from_id == $admin) {
    $channels = file_exists("data/channels.json") ? json_decode(file_get_contents("data/channels.json"), true) : [];

    $inline = [[["text" => "➕ افزودن کانال", "callback_data" => "add_channel"]]];
    foreach ($channels as $id => $name) {
        $inline[] = [["text" => "❌ حذف $name", "callback_data" => "delch_$id"]];
    }

    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "مدیریت کانال‌ها:",
        "reply_markup" => json_encode(["inline_keyboard" => $inline])
    ]);
}

if (isset($data) && $data == "add_channel" && $from_id == $admin) {
    file_put_contents("data/user/$from_id/step.txt", "wait_channel_name");
    bot("editMessageText", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => "لطفاً *نام کانال* را ارسال کنید:",
        "parse_mode" => "Markdown"
    ]);
}

if (file_exists("data/user/$from_id/step.txt") && $from_id == $admin) {
    $step = file_get_contents("data/user/$from_id/step.txt");

    if ($step == "wait_channel_name" && isset($text)) {
        file_put_contents("data/temp_name.txt", $text);
        file_put_contents("data/user/$from_id/step.txt", "wait_channel_id");
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "حالا *آیدی کانال* را بفرست (مثلاً `@mychannel`):",
            "parse_mode" => "Markdown"
        ]);
    } 
    else if ($step == "wait_channel_id" && isset($text)) {
        $id = $text;
        $name = file_get_contents("data/temp_name.txt");

        $channels = file_exists("data/channels.json") ? json_decode(file_get_contents("data/channels.json"), true) : [];
        $channels[$id] = $name;
        file_put_contents("data/channels.json", json_encode($channels));

        unlink("data/user/$from_id/step.txt");
        unlink("data/temp_name.txt");

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "کانال *$name* با آیدی `$id` ذخیره شد.",
            "parse_mode" => "Markdown"
        ]);
    }
}

if (isset($data) && strpos($data, "delch_") === 0 && $from_id == $admin) {
    $id = str_replace("delch_", "", $data);
    $channels = file_exists("data/channels.json") ? json_decode(file_get_contents("data/channels.json"), true) : [];
    if (isset($channels[$id])) {
        unset($channels[$id]);
        file_put_contents("data/channels.json", json_encode($channels));

        $inline = [[["text" => "➕ افزودن کانال", "callback_data" => "add_channel"]]];
        foreach ($channels as $cid => $cname) {
            $inline[] = [["text" => "❌ حذف $cname", "callback_data" => "delch_$cid"]];
        }

        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $message_id,
            "text" => "کانال حذف شد. لیست فعلی:",
            "reply_markup" => json_encode(["inline_keyboard" => $inline])
        ]);
    }
}

if (isset($text) && strpos($text, "/start") === 0) {
    if (strpos($text, "/start ") !== false) {
        $parts = explode(" ", $text);
        $code = $parts[1];

        if (strpos($code, "album_") === 0) {
            $album_code = str_replace("album_", "", $code);
            $album_file = "data/media_album/$album_code.json";

            if (file_exists($album_file)) {
                $media_items = json_decode(file_get_contents($album_file), true);
                $media = [];

                foreach ($media_items as $item) {
                    if (in_array($item['type'], ['photo', 'video'])) {
                        $media[] = [
                            'type' => $item['type'],
                            'media' => $item['file_id']
                        ];
                    }
                }

                $res = bot('sendMediaGroup', [
                    'chat_id' => $chat_id,
                    'media' => json_encode($media)
                ]);

                if ($res && isset($res['result'])) {
                    $msg_ids = array_map(function ($msg) {
                        return $msg['message_id'];
                    }, $res['result']);

                    $notice = bot("sendMessage", [
                        "chat_id" => $chat_id,
                        "text" => "این آلبوم تا ۱۵ ثانیه دیگر پاک می‌شود."
                    ]);

                    $notice_id = $notice['result']['message_id'] ?? null;

                    $delete_script = "tmp/delete_album_{$chat_id}_" . time() . ".php";
                    $delete_code = '<?php
sleep(15);
$token = "' . BOT_TOKEN . '";
$chat_id = ' . $chat_id . ';
' . implode("\n", array_map(function ($id) use ($chat_id) {
    return 'file_get_contents("https://api.telegram.org/bot" . $token . "/deleteMessage?chat_id=$chat_id&message_id=' . $id . '");';
}, $msg_ids)) . '
' . ($notice_id ? 'file_get_contents("https://api.telegram.org/bot" . $token . "/deleteMessage?chat_id=$chat_id&message_id=' . $notice_id . '");' : '') . '
unlink(__FILE__);
?>';
                    file_put_contents($delete_script, $delete_code);
                    exec("php $delete_script > /dev/null 2>&1 &");
                }
            } else {
                bot("sendMessage", [
                    "chat_id" => $chat_id,
                    "text" => "آلبوم یافت نشد."
                ]);
            }

            return; 
        }

        file_put_contents("data/last_code.txt", $code);
        $data_file = "data/media/$code.json";

        if (file_exists($data_file)) {
            $media = json_decode(file_get_contents($data_file), true);
            $type = $media["type"];
            $file_id = $media["file_id"];

            $msg_id = null;
            $notice_id = null;
            switch ($type) {
                case "photo":
                    $res = bot("sendPhoto", [
                        "chat_id" => $chat_id,
                        "photo" => $file_id
                    ]);
                    break;
                case "video":
                    $res = bot("sendVideo", [
                        "chat_id" => $chat_id,
                        "video" => $file_id
                    ]);
                    break;
                case "audio":
                    $res = bot("sendAudio", [
                        "chat_id" => $chat_id,
                        "audio" => $file_id
                    ]);
                    break;
                case "animation":
                    $res = bot("sendAnimation", [
                        "chat_id" => $chat_id,
                        "animation" => $file_id
                    ]);
                    break;
                default:
                    $res = null;
                    break;
            }

            if ($res && isset($res["result"]["message_id"])) {
                $msg_id = $res["result"]["message_id"];

                $notice = bot("sendMessage", [
                    "chat_id" => $chat_id,
                    "text" => "فایل ارسالی تا ۱۵ ثانیه دیگه پاک میشه"
                ]);
                if (isset($notice["result"]["message_id"])) {
                    $notice_id = $notice["result"]["message_id"];
                }

                $delete_script = "tmp/delete_{$chat_id}_{$msg_id}.php";
                $delete_code = '<?php
sleep(15);
file_get_contents("https://api.telegram.org/bot' . BOT_TOKEN . '/deleteMessage?chat_id=' . $chat_id . '&message_id=' . $msg_id . '");
file_get_contents("https://api.telegram.org/bot' . BOT_TOKEN . '/deleteMessage?chat_id=' . $chat_id . '&message_id=' . $notice_id . '");
unlink(__FILE__);
?>';
                file_put_contents($delete_script, $delete_code);
                exec("php $delete_script > /dev/null 2>&1 &");
            }

        } else {
            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => "فایل یافت نشد."
            ]);
        }
    } else {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "سلام! خوش اومدی."
        ]);
    }
}