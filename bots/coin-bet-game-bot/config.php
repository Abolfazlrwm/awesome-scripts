<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(E_ALL);
$token = ""; // توکن ربات را از @BotFather بگذارید
$admin = ['0']; // آیدی عددی ادمین را بگذارید
$linc = "test";
$localhost = "localhost";
$name = "";
$user = "";
$password = "";
$idbot = "6994053720";//id addi bot


class botm
{
    public $token = NULL;
    public function __construct($token){
        $this->token = $token;
    }
    
    public function bot($method, $datas = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->token.'/'.$method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        return json_decode(curl_exec($ch));
    }
    
    public function sendMessage($from_id, $text, $key = NULL){
        return $this->bot("sendMessage", ["chat_id" => $from_id, "text" => $text, "parse_mode" => "Html", "disable_web_page_preview" => true, "reply_markup" => $key]);
    }
    public function senddice($chat_id,$emoji){
    return $this->bot('sendDice',['chat_id' => $chat_id,'emoji'=> $emoji]);
    }
}

$bot = new botm($token);






?>