<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
error_reporting(0);
include 'config.php';
$imageUrl = file_get_contents("data/photo.txt");
$caption = file_get_contents("data/cap.txt");
$adid = file_get_contents("data/adid.txt");
$send = file_get_contents("data/send.txt");
file_put_contents("data/send.txt","ok");
if($send == "ok"){
exit();
}
file_put_contents("data/send.txt","ok");
$url = "https://api.telegram.org/bot$token/sendPhoto";
$data = [
    'chat_id' => $channelid,
    'photo' => $imageUrl,
    'caption' => $caption,
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🖼️ تصاویر بیشتر", 'url' => "https://t.me/$botidd?start=$adid"]],
                [['text' => "💰خریداری اکانت", 'url' => "https://t.me/$botidd?start=s$adid"]],
            ],
            'resize_keyboard' => true,
        ]) 
];
$options = [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => $data,
];

$ch = curl_init();
curl_setopt_array($ch, $options);
$response = curl_exec($ch);
curl_close($ch);
$true = json_decode($response,true);
$message_id = $true["result"]["message_id"];
file_put_contents("data/adp/$adid/delm.txt","$message_id");
echo "true, message_id_channel:".$message_id."";
?>
	