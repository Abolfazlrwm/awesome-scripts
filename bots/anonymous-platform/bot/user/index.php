<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);

$telegram_ip_ranges = [['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],['lower' => '91.108.4.0', 'upper' => '91.108.7.255']];
$ip_dec = (float) sprintf("%u", ip2long(htmlspecialchars($_SERVER['REMOTE_ADDR'])));
$ok = false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
    if($ip_dec >= $lower_dec && $ip_dec <= $upper_dec) $ok = true;
}
if(!$ok)die("Not allowed");

require_once '../../include/settings.php';
$ch = curl_init(str_replace("/index.php","/bot.php","https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));
curl_setopt_array($ch, [
        CURLOPT_POSTFIELDS => json_encode(['api_key' => $api_key,'telegram' => json_decode(file_get_contents("php://input"),true)]),
        CURLOPT_TIMEOUT => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT_MS => 0,
        CURLOPT_HTTPHEADER => ['accept: application/json','content-type: application/json']
]);
curl_exec($ch);
curl_close($ch);

$up = json_decode(file_get_contents("php://input"),true);
if(isset($up["message"])){
    $user_id = $up["message"]["from"]["id"];
    $text = $up["message"]["text"];
    $message_id = $up["message"]["message_id"];
}
else if(isset($up["callback_query"])){
    $cq_id = $up["callback_query"]["id"];
    $user_id = $up["callback_query"]["from"]["id"];
    $data = $up["callback_query"]["data"];
    $message_id = $up["callback_query"]["message"]["message_id"];
}
else exit;

require_once '../../include/settings.php';
require_once '../../include/config.php';
require_once '../options/functions.php';

$row_users = $conn->query("SELECT * FROM ".users." WHERE user_id='$user_id'")->fetch();
if(!$row_users)
    exit;
$conn->query("UPDATE ".users." SET last_activity='$time' WHERE user_id='$user_id'");

$result = $conn->query("SELECT * FROM ".notif." WHERE user_id_2='$user_id' AND reason='onlinenotif'")->fetchAll();
if($result){
    foreach($result as $row){
        send_reply("sendMessage",['chat_id' => $row['user_id'],'text' => "🔔 هم اکنون کاربر /user_{$row_users['uniq_id']} آنلاین است."]);
        $conn->query("DELETE FROM ".notif." WHERE id='{$row['id']}'");
    }
}
?>