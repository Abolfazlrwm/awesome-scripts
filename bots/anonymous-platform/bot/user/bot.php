<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])
    exit;

require_once '../../include/settings.php';
$up = json_decode(file_get_contents("php://input"),true);
if(!isset($up['api_key']) || !isset($up['telegram']) || $up['api_key'] != $api_key)exit;
else $up = $up['telegram'];

if(isset($up["message"])){
    $message = $up["message"];
    $chat_id = $up["message"]["chat"]["id"];
    $user_id = $up["message"]["from"]["id"];
    $first_name = $up["message"]["from"]["first_name"];
    $text = $up["message"]["text"];
    $message_id = $up["message"]["message_id"];
    $entities = 0;
    if($up["message"]["text"]){
        $text = $up["message"]["text"];
        $textcap = $text;
        if(isset($up["message"]["entities"]))
            $entities = $up["message"]["entities"];
    }
    else if(isset($up["message"]["caption"])){
        $caption = $up["message"]["caption"];
        $textcap = $caption;
        if(isset($up["message"]["caption_entities"]))
            $entities = $up["message"]["caption_entities"];
    }
    (isset($up['message']['from']['username']))?($username = "@{$up['message']['from']['username']}"):($username = "");
    $ex_text = explode("_",$text);
}
else if(isset($up["edited_message"])){
    $chat_id = $up["edited_message"]["chat"]["id"];
    $user_id = $up["edited_message"]["from"]["id"];
    $username = "@".$up['edited_message']['from']['username'];
    $entities = 0;
    if($up["edited_message"]["text"]){
        $text = $up["edited_message"]["text"];
        $textcap = $text;
        if(isset($up["edited_message"]["entities"]))
            $entities = $up["edited_message"]["entities"];
    }
    else if(isset($up["edited_message"]["caption"])){
        $caption = $up["edited_message"]["caption"];
        $textcap = $caption;
        if(isset($up["edited_message"]["caption_entities"]))
            $entities = $up["edited_message"]["caption_entities"];
    }
    $message_id = $up["edited_message"]["message_id"];
    $ex_text = explode("_",$text);
}
else if(isset($up["callback_query"])){
    $cq_id = $up["callback_query"]["id"];
    $user_id = $up["callback_query"]["from"]["id"];
    $data = $up["callback_query"]["data"];
    $message_id = $up["callback_query"]["message"]["message_id"];
    (isset($up['callback_query']['message']['from']['username']))?($username = "@{$up['callback_query']['message']['from']['username']}"):($username = "");
    if($data == "test")exit;
    $ex_data = explode(";",$data);
}
else if(isset($up['inline_query'])){
    $inline_query = $up['inline_query']['query'];
    $offset = $up['inline_query']['offset'];
    $inline_query_id = $up['inline_query']['id'];
    $user_id = $up['inline_query']['from']['id'];
    $username = $up['inline_query']['from']['username'];
    $user_fn = $up['inline_query']['from']['first_name'];
}
else exit;
$link = str_replace("/user/bot.php","","https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

require_once '../../include/config.php';
require_once '../options/functions.php';
require_once '../options/jdf.php';

require_once 'auth.php';
require_once 'static.php';
require_once 'profile.php';
require_once 'chat.php';
require_once 'start.php';
require_once 'main.php';

send_reply("sendMessage", [
    'chat_id' => $user_id,
    'text' =>
        "متوجه نشدم :/\n\n".
        "<code>چه کاری برات انجام بدم؟ از منوی پایین انتخاب کن 👇</code>",
    'parse_mode' => 'HTML',
    'reply_to_message_id' => $message_id,
    'reply_markup' => json_encode(['resize_keyboard' => true,'keyboard' => mainMenu(true)])
]);

