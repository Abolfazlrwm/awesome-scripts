<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

http_response_code(200);
$url = dirname(getenv('SCRIPT_URI')) . '/index.php';
$data = file_get_contents('php://input');
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POSTFIELDS          => $data,
    CURLOPT_TIMEOUT          => 1,
    CURLOPT_RETURNTRANSFER      => true,
    CURLOPT_SSL_VERIFYPEER      => false,
    CURLOPT_SSL_VERIFYHOST      => false,
    CURLOPT_CONNECTTIMEOUT_MS   => 0,
    CURLOPT_HTTPHEADER => [
    'accept: application/json',
    'content-type: application/json'
]
]);
curl_exec($ch);
?>