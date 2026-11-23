<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
/*
نوشته شده توسط @Dev_Amiri و اوپن شده در کانال @CristalTeam 
 ذکر منبع اجباری!
 */
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');// توکن
$admin = 824123981; // آیدی عددی ادمین
$Wallet_Tron = "TJZjRJ99ZPRFsc3DxqqZt4wstp7S1gFkF1"; //ادرس ولت ترون
$Channels = ['CristalTeam'];
$botuser = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getme"))->result->username; 
$servername = "localhost";// دست نزنید
$username   = "USER";// یوزر دیتابیس	
$password   = "PSS"; // پس دیتابیس
$dbname     = "DBname"; // نام دیتابیس

$connect = new mysqli($servername, $username, $password, $dbname);
$connect->query("SET NAMES 'utf8'"); $connect->set_charset('utf8mb4');
/*
نوشته شده توسط @Dev_Amiri و اوپن شده در کانال @CristalTeam 
 ذکر منبع اجباری!
 */
?>