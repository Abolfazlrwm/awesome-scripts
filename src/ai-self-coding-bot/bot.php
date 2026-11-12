<?php
// Telegram: @Abolfazl_rwm
// این فایل به صورت داینامیک توسط هوش مصنوعی تکمیل می‌شود.
$string = file_get_contents("php://input");
$update = json_decode($string, true);
if (!$update) exit;

define('API_KEY', getenv('BOT_TOKEN') ?: 'توکن ربات');

function bot($method, $datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){ var_dump(curl_error($ch)); } else { return json_decode($res); }
}

$chat_id = $update['message']['chat']['id'] ?? null;
$message = $update['message']['text'] ?? null;

// --- کدهای تولید شده توسط ابوالفضل از اینجا به بعد اضافه می‌شوند ---


