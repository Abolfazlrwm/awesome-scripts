<?php
error_reporting(0);

// توکن ربات را از متغیر محیطی BOT_TOKEN می‌خواند؛ در صورت نبود، به مقدار پیش‌فرض برمی‌گردد
$bot_token = getenv('BOT_TOKEN') ?: 'YOUR_BOT_TOKEN_HERE';

//  نوشته شده توسط: @cod_arshiya تلگرام
//  آیدی کانال تلگرام: @cod_arshiya0

@$Update = json_decode(file_get_contents('php://input'));
if(isset($Update)){
    // ==============================
    $telegram_ip_ranges = [['lower'=>'149.154.160.0', 'upper'=>'149.154.175.255'], ['lower'=>'91.108.4.0', 'upper'=>'91.108.7.255']];
    $ip_dec = (float) sprintf('%u', ip2long($_SERVER['REMOTE_ADDR'])); $ok=false;
    foreach ($telegram_ip_ranges as $telegram_ip_range) if(!$ok){
        $lower_dec = (float) sprintf('%u', ip2long($telegram_ip_range['lower']));
        $upper_dec = (float) sprintf('%u', ip2long($telegram_ip_range['upper']));
        if($ip_dec >= $lower_dec && $ip_dec <= $upper_dec) $ok=true; 
    } if(!$ok) die;
    // ==============================
    @$pid = pcntl_fork();
    if($pid == -1) posix_kill($pid, SIGKILL);
    elseif($pid) pcntl_wait($status);
    else 
    {
        if(isset($Update->message))
        {
            @$Message= $Update->message;
            @$FromId = $Message->from->id;
            if(isset($Message->forward_from) && $Message->forward_from->is_bot){
                @$botId = $Message->forward_from->id;
                @$firstName = $Message->forward_from->first_name;
                @$userName = $Message->forward_from->username!= null ? "@{$Message->forward_from->username}\n" : '';
                $information = "{$userName}Id: {$botId}\nFirst: {$firstName}";
            } elseif(isset($Message->forward_from_chat) && $Message->forward_from_chat->type== 'channel'){
                @$chatId = $Message->forward_from_chat->id;
                @$chatTitle = $Message->forward_from_chat->title;
                @$chatUser = $Message->forward_from_chat->username ?? 'null';
                @$chatMessageId = $Message->forward_from_message_id;
                $information = "Id: {$chatId}\nTitle: {$chatTitle}\nhttps://t.me/{$chatUser}/{$chatMessageId}";
            } else {
                @$firstName = $Message->from->first_name;
                @$lastName = $Message->from->last_name ? "\nLast: {$Message->from->last_name}" : '';
                @$userName = $Message->from->username!= null ? "@{$Message->from->username}\n" : '';
                @$userLang = $Message->from->language_code;
                $information = "{$userName}Id: {$FromId}\nFirst: {$firstName}{$lastName}\nLang: {$userLang}";
            }
            // ==============================
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$bot_token}/sendMessage");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'chat_id'=> $FromId,
                'text'=> $information,
                'disable_web_page_preview'=> true
            ]);
            curl_exec($ch);
            // ==============================
            if(!file_exists('members.txt')) touch('members.txt');
            @$file_check = file('members.txt', FILE_IGNORE_NEW_LINES);
            if(!$file_check) $file_check = [];
            if(!in_array($FromId, $file_check))
            {
                @$fopen = fopen('members.txt', 'a') or die;
                @fwrite($fopen, "{$FromId}\n");
                @fclose($fopen);
            }
        }
    }
}


//  نوشته شده توسط: @cod_arshiya تلگرام
//  آیدی کانال تلگرام: @cod_arshiya0