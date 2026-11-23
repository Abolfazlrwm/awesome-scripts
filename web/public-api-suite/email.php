<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
header('Content-type: application/json');

function GetNewMail($name, $domain){
$cp = curl_init('https://api.internal.temp-mail.io/api/v2/email/new');
curl_setopt_array($cp, [
CURLOPT_POSTFIELDS => json_encode(['name' => $name, 'domain' => $domain]),
CURLOPT_RETURNTRANSFER => true,
CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/5351 (KHTML, like Gecko) Chrome/37.0.857.0 Mobile Safari/5351'
]);
$res = json_decode(curl_exec($cp),true);
unset($res['token']);
return $res;
curl_close($cp);
}
function GetMessages($email){
return json_decode(file_get_contents('https://api.internal.temp-mail.io/api/v2/email/' . $email . '/messages'), true) ?: 'No Messages In This Fake Mail !';
}
function GetDomains(){
$dm = json_decode(file_get_contents('https://api.internal.temp-mail.io/api/v2/domains'), true)['domains'];
return $dm[rand(0, count($dm)-1)];
}

$method = $_GET['method'] ?? $_POST['method'];
$email = $_GET['email'] ?? $_POST['email'];
$name = $_GET['name'] ?? $_POST['name'] ?? 'tiklib';
if(isset($method) and !empty($method)){
	if($method == 'GetNewMail'){
		$results = GetNewMail($name.rand(0,9999), GetDomains());
		echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "$results"], 448);
	}
elseif($method == 'GetMessages'){
		if(isset($email) and !empty($email)){
			$results = GetMessages($email);
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "$results"], 448);
		}else{
			echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The email parameter is empty !"], 448);
		}
	}else{
		echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "Method Must Be GetNewMail Or GetMessages !"], 448);
	}
}else{
	echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The method parameter is empty !"], 448);
}