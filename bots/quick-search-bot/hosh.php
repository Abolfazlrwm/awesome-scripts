<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
/*
نویسنده  : @devbc
کانال  : سورس کده
*/
#-----------------------------#
date_default_timezone_set('Asia/Tehran');
error_reporting(0);
#-----------------------------#
$token = "ydZJU1FIXdu_BU"; //توکن
#-----------------------------#
define('API_KEY', $token);
#-----------------------------#
$update = json_decode(file_get_contents("php://input"));
if(isset($update->message)){
    $from_id    = $update->message->from->id;
    $chat_id    = $update->message->chat->id;
    $tc         = $update->message->chat->type;
    $text       = $update->message->text;
    $first_name = $update->message->from->first_name;
    $message_id = $update->message->message_id;
}elseif(isset($update->callback_query)){
    $chat_id    = $update->callback_query->message->chat->id;
    $data       = $update->callback_query->data;
    $query_id   = $update->callback_query->id;
    $message_id = $update->callback_query->message->message_id;
    $in_text    = $update->callback_query->message->text;
    $from_id    = $update->callback_query->from->id;
}
#-----------------------------#
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
#-----------------------------#
function sendmessage($chat_id,$text,$keyboard = null) {
    bot('sendMessage',[
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => "HTML",
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ]);
}
#-----------------------------#
if (!is_dir("data")){
mkdir ("data");
mkdir ("data/user");
}
if (!is_dir("data/user/$from_id")){
mkdir ("data/user/$from_id");
}
$step = file_get_contents ("data/user/$from_id/step.txt");
#-----------------------------#
if($text == "/start"){
sendmessage ($chat_id , "سلام لطفا سوال خود را بپرسید :");
file_put_contents ("data/user/$from_id/step.txt","send");
}
if ($step == "send" and $text != "/start"){
$text = str_replace(' ', '-', $text);
$text = urlencode($text);
$web = file_get_contents ("https://zitactm.n-cpanel.xyz/api/gpt/api.php?text=$text");
$data = json_decode($web, true);
$msg = $data['message'];
$msg2 = urldecode ($msg);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
`$msg2`
",
'parse_mode'=>'MarkDown',
]); 
}
#-----------------------------#
/*
نویسنده  : @devbc
کانال  : سورس کده
*/
?>