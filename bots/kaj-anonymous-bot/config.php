<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/
error_reporting(0);
//========================== // token // ==============================
define('API_KEY', 'Token'); // توکن ربات
define('API_URL', 'https://api.telegram.org/bot' . API_KEY . '/');
//========================== // config // ==============================
$admin    = "1234"; // آیدی عددی ادمین ها

$channel  = "Sourrce_Kade"; // آیدی کانال بدون @
$usernamebot  = "Sourrce_Kade"; // آیدی ربات

$web = "https://server.telegram.com/folder"; // محل قرارگیری سورس

$botname = "چت ناشناس";
$channellog = "-1001234";

$support = "Sourrce_Kade";

$MerchantID = ""; // مرچند کد زرین پال

$hadaddfuns = "1000"; // حداقل شارژ حساب
$maxaddfuns = "10000"; // حداکثر شارژ حساب

$token_amount = "100000"; // به ازای هر حرف چقدر قیمتش بیشتر شه
$start_token = "7"; // از ۷ حرف به بعد مجانیه
//========================== // database // ==============================
$dbuser = "Db"; // یوزر دیتابیس
$dbpass = "Db"; // رمز دیتابیس
$dbname = "DB"; // نام دیتابیس
$connect = new mysqli('localhost', $dbuser, $dbpass, $dbname);
$connect->query("SET NAMES 'utf8'");
$connect->set_charset('utf8mb4');
/*
به نام خداوند یکتا

سورس چت ناشناس نوشته شده توسط ابوالفضل کاج و منتشر شده در کانال آیرا تیم

لطفا درصورت انتشار آیدی نویسنده ذکر شود.

https://AbolfazlKaj [نویسنده]
https://Sourrce_Kade [منتشر کننده]
*/