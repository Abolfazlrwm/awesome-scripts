<?php
/*
نویسنده  : @devbc
کانال  : سورس کده 
*/
#-----------------------------#
date_default_timezone_set('Asia/Tehran');
error_reporting(0);
#-----------------------------#
$token = ""; // توکن ربات را از @BotFather بگیرید و اینجا بگذارید
$channelprox = "ProxyMTProto"; // ایدی کانالی که قصد اسکی پروکسی دارید بدون @
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
/*
if (!is_dir("data")){
    mkdir ("data");
}
if (!is_dir("data/user")){
    mkdir ("data/user");
}
if (!is_dir("data/user/$from_id")){
    mkdir ("data/user/$from_id");
}
#-----------------------------#
if (!file_exists("data/user/$from_id/step.txt")){
    file_put_contents ("data/user/$from_id/step.txt","none");
}
#-----------------------------#
$step = file_get_contents ("data/user/$from_id/step.txt");
$o = "بازگشت";
*/
#-----------------------------#
$back = json_encode(['keyboard'=>[
[['text'=>"$o"]],
],'resize_keyboard' =>true]);
#-----------------------------#
if($text == "/start"){
$api = file_get_contents ("https://haji-api.ir/proxy?channel=$channelprox");
$data = json_decode($api, true);
$server1 = $data['proxies'][0]['link'];
$server2 = $data['proxies'][1]['link'];
$server3 = $data['proxies'][2]['link'];
$server4 = $data['proxies'][3]['link'];
$server5 = $data['proxies'][4]['link'];
$server6 = $data['proxies'][5]['link'];
$server7 = $data['proxies'][6]['link'];
$server8 = $data['proxies'][7]['link'];
$server9 = $data['proxies'][8]['link'];
$server10 = $data['proxies'][9]['link'];
$key = json_encode (['inline_keyboard'=>[
[['text'=>"سرور اول" , 'url' =>"$server1"],['text'=>"سرور دوم", 'url' => "$server2"]],
[['text'=>"سرور سوم" , 'url' =>"$server3"],['text'=>"سرور چهارم", 'url' => "$server4"]],
[['text'=>"سرور پنجم" , 'url' =>"$server5"],['text'=>"سرورس ششم", 'url' => "$server6"]],
[['text'=>"سرور هفتم" , 'url' =>"$server7"],['text'=>"سرور هشتم", 'url' => "$server8"]],
[['text'=>"سرور نهم" , 'url' =>"$server9"],['text'=>"سرور دهم", 'url' => "$server10"]],
]]);
sendmessage ($chat_id , "
لیست پروکسی های دریافت شده :
" , $key);
//file_put_contents ("data/user/$from_id/step.txt","none");
}
#-----------------------------#
#-----------------------------#
/*
نویسنده  : @devbc
کانال  : سورس کده
*/
?>