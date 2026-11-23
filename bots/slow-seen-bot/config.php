<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
error_reporting(0);
//===[امنیت دامنه]===//
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    if(!$ok)
	{
		$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
		$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
		if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec)
		{
			$ok=true;
			}
		}
	}
if(!$ok)
{
	exit(header("location: https://SnowyTeam.xyz"));
	}
//========================== // token // ==============================
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');
//========================== // config // ==============================
$get = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getMe")); 
$usernamebot = $get->result->username;
$botid = $get->result->id;
$botname = $get->result->first_name;
$admin = ['971881348','971881348'];
$channel = 'SlowTM'; // ایدی کانال بدون @
$channelorder = 'SlowSeenOrders'; // ایدی کانال سفارشات بدون @ جهت انجام سفارشات نیاز هست
$usernamesup = '@CreatorSLow'; // ایدی فرد پشتیبان ربات
$baner = 'https://t.me/Mafia_nit/154'; // بنر زیر مجموعه گیری
//========================== // variable // ==============================
$GiftCoin = '300'; // مقدار موجودی هدیه برای شروع هر کاربر
$ZirCoin = '300'; // پاداش افزودن هر زیر مجموعه به ربات
$DarsadCoin = '500'; // پورسانت خرید زیر مجموعه از ربات
$SeenCoin = '1'; // قیمت هر عدد بازدید در ربات
$LikeCoin = '50'; // قیمت هر عدد لایک در ربات
?>