<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
header('Content-type: application/json');
$corona = $_GET['corona'] ?? $_POST['corona'];
if(isset($corona) and !empty($corona)){
$get = file_get_contents("https://www.worldometers.info/coronavirus/country/$corona/");
if(preg_match_all('#<span>(.*?)</span>#',$get,$res) and preg_match_all('#<span style="color:(.*?)">(.*?)</span>#',$get,$ress)){
	echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The name of the country is correct !", 'information'=>['cases' => $ress[2][1], 'death' => $res[1][0], 'recovered' => $res[1][1]]], 448);
}else{
	echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The country name is incorrect !"], 448);
}
}else{
	echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The corona parameter is empty !"], 448);
}