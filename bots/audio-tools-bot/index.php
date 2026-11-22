<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */


# توسعه دهنده و طراح این سورس کد @mrkral میباشد
# @Sourrce_kade
# هر گونه نشر این سورس بدون منبع نویسنده و کانال توهین به وقتی که برای نوشتن این سورس داده شده است

ob_start(); 
error_reporting(0);
define('API_KEY','توکن'); // جایگزین با توکن خود
$admin = "5445718915"; // جایگزین با آی‌دی ادمین

function fsize($size, $round=2) {
    $sizes = array(' Bytes', ' Kb', ' Mb', ' Gb', ' Tb');
    $total = count($sizes) - 1;
    for ($i = 0; $size > 1024 && $i < $total; $i++) {
        $size /= 1024;
    }
    return round($size, $round) . $sizes[$i];
}

function del($namee) {
    array_map('unlink', glob("$namee"));
}

function bot($method, $datas=[]) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$reply = $message->reply_to_message->text;

$mid = $message->message_id;
$chat_id = $message->chat->id;
$text1 = $message->text;
$fadmin = $message->from->id;

$sreply = $message->reply_to_message->text;  
$sreplyd = $message->audio;
$ent = $message->entities[0]->type;
$reply_menu = json_encode([
    'resize_keyboard' => false,
    'force_reply' => true,
    'selective' => true
]);

if ($text1 == "/start") {
    $kral = file_get_contents("pechat.dat");
    if (mb_stripos($kral, $chat_id) !== false) {
    } else {
        file_put_contents("pechat.dat", "$kral\n$chat_id");
    }
    $text = "🎵 ربات کرال میکس همیار هوشمند شما

🔊 با این ربات میتونی کانال موزیکی خودتو کنترل کنی 

با این ربات میتوانید از آهنگ های خود کاملا ساده بدون هیچ کار خاصی دمو بسازید

🤷‍♂️ نحوه استفاده از ربات :

1 - ربات را در کانال خود ادمین کنید و فول دسترسی بدین بهش
2 - آهنگ خود را در کانال آپلود کنید

😁 بعد از انجام این کار موقع آپلود موسیقی خود ربات آن رو حذف میکنه و اول از آهنگ #دمو ساخته و بعد با #موزیک در کانال آپلود میکند

کانال اطلاع رسانی : @Sourrce_kade";
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "کانال اطلاع رسانی 📣", 'url' => 't.me/Sourrce_kade']]
            ]
        ])
    ]);
}

$chm = $update->channel_post;
$chuser = $chm->chat->username;
$chtext = $chm->text;
$title = $chm->chat->title;
$chma = $chm->audio;
$doc = $chm->audio;
$doc = $message->audio;
$doc_id = $chma->file_id;
$message_ch = $update->channel_post;
$message_ch_mid = $message_ch->message_id;
$muser = $message_ch->chat->username;
$message_ch_chid = $message_ch->chat->id;

if (mb_stripos($chtext, "#cap") !== false) {
    $ex = explode("#cap", $chtext);
    $tx = $ex[1];
    bot('deleteMessage', [
        'chat_id' => $message_ch_chid,
        'message_id' => $message_ch_mid
    ]);
    file_put_contents("$message_ch_chid.caption", "$tx");
}

if (mb_stripos($chtext, "#btn") !== false) {
    $ex = explode("#btn", $chtext);
    $tx = $ex[1];
    bot('deleteMessage', [
        'chat_id' => $message_ch_chid,
        'message_id' => $message_ch_mid
    ]);
    file_put_contents("$message_ch_chid.btn", "$tx");
}

if ($chma) {
    $url = json_decode(file_get_contents('https://api.telegram.org/bot' . API_KEY . '/getFile?file_id=' . $doc_id), true);
    $path = $url['result']['file_path'];
    $file = 'https://api.telegram.org/file/bot' . API_KEY . '/' . $path;
    $ftitle = $chma->title;
    $fname = $chma->performer;
    $type = strtolower(strrchr($file, '.')); 
    $typeee = str_replace('dodasi.com', '@' . $muser, $ftitle);
    file_put_contents($doc_id . ".mp3", file_get_contents($file));
    $xajm = fsize(filesize($doc_id . ".mp3"));
    bot('deletemessage', ['chat_id' => $message_ch_chid, 'message_id' => $message_ch_mid]);
    $chid = $message_ch->chat->id;
    $size = $chma->file_size;
    $dur12 = $chma->duration;
    $dur = $dur12 / 60;
    $size = $size / 1048576;
    $coor = explode('.', $size);
    $coor2 = substr($coor[1], 0, 2);
    $coor3 = explode('.', $dur);
    $coor4 = substr($coor3[1], 0, 2);
    $dur1 = $coor3[0];
    $dur2 = $dur1 * 60;
    $duri = $dur12 - $dur2;
    $size = $coor[0] . '.' . $coor2;
    $dur = $coor3[0] . '.' . $coor4;
    include("getid3/getid3.php");
    $getID3 = new getID3;
    $filer = $getID3->analyze($doc_id . ".mp3");
    $durm = $filer['playtime_string'];
    require_once 'phpmp3.php';

    $mp3 = new PHPMP3($doc_id . ".mp3");
    $mp3_demo = $mp3->extract(0, 60);
    $mp3_demo->save('demo.ogg');

    $chcap = file_get_contents("$message_ch_chid.caption");
    if (strlen($chcap) > 2) {
        $cap = $chcap;
    } else {
        $cap = "💊 @$muser";
    }

    $inline_keyboard = [
        [[
            'text' => "Size: $xajm",
            'callback_data' => "size"
        ],
        [
            'text' => "Duration: $durm",
            'callback_data' => "duration"
        ]]
    ];

    bot('sendAudio', [
        'chat_id' => $message_ch_chid,
        'audio' => new CURLFile($doc_id . ".mp3"),
        'title' => $fname . "-" . $typeee,
        'performer' => "@" . $muser,
        'thumb' => $fileid,
        'caption' => $fname . "-" . $typeee . "\n\n" . $cap,
        'reply_markup' => json_encode([
            'inline_keyboard' => $inline_keyboard
        ])
    ]);

    bot('sendVoice', [
        'chat_id' => $message_ch_chid,
        'voice' => new CURLFile("demo.ogg"),
        'duration' => 60,
        'caption' => $fname . "-" . $typeee . "\n#DEMO ıllıllı 𖦤⤋ \n\n💊 @" . $muser,
    ]);

    del($doc_id . ".mp3");
    del("demo.ogg");
}

$type = $message->chat->type;
if ($type == "private") {
    $kral = file_get_contents("userK.txt");
    if (mb_stripos($kral, $chat_id) !== false) {
    } else {
        file_put_contents("userK.txt", "$kral\n$chat_id");
    }
}

if ($chuser) {
    $dat = file_get_contents("chuser.dat");
    if (mb_stripos($dat, $chuser) !== false) {
    } else {
        file_put_contents("chuser.dat", "$dat\n@$chuser");
        file_put_contents("$message_ch_chid.caption", "");
        file_put_contents("$message_ch_chid.btn", ""); 
    }
}

if ($text1 == "/stat" && $fadmin == $admin) {
    $stat = file_get_contents("userK.txt");
    $count = substr_count($stat, "\n");
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👤 آمار ربات : $count\n\n Developer : @mrkral",
    ]);
}

if ($text1 == "/chuser" && $fadmin == $admin) {
    $chus = file_get_contents("chuser.dat");
    $user = file_get_contents("chuser.dat");
    $son = substr_count($user, "\n");
    $son1 = $son - 1;
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "از همه کانال ها استفاده کنید : $chus \n\n همه کانال ها :$son تا\n\nChannel : @Sourrce_kade",
    ]);
}

$guruh = file_get_contents("userK.txt");

if ($text1 == "/senddd" && $fadmin == $admin) {
    bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "Send Massage ...:",
        'reply_markup' => json_encode([
            'force_reply' => true 
        ])
    ]);
}

if ($reply == "Send Massage ...:" && $fadmin == $admin) {
    $ex = explode("\n", $guruh);
    foreach ($ex as $for) {
        bot('sendmessage', [
            'chat_id' => $for,
            'text' => $text1,
            'parse_mode' => "html",
        ]);
    }
}

# توسعه دهنده و طراح این سورس کد @mrkral میباشد
# @Sourrce_kade
# هر گونه نشر این سورس بدون منبع نویسنده و کانال توهین به وقتی که برای نوشتن این سورس داده شده است

?>
