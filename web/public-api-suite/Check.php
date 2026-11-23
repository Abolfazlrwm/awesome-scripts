<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

ini_set('display_errors', 0);
ini_set('memory_limit', -1);
ini_set('max_execution_time', 0);
header('Content-type: application/json');
if (!file_exists('Madeline/madeline.php')) copy('https://phar.madelineproto.xyz/madeline.php', 'Madeline/madeline.php');
include 'Madeline/madeline.php';
use \danog\MadelineProto\FastAPI as maker;
$phone = $_GET['phone'] ?? $_POST['phone'];
if(isset($phone) and !empty($phone)){
	$setting = [
	'serialization' => [
		'cleanup_before_serialization' => true,
	],
	'logger' => [
		'max_size' => 1*1024*1024,
	],
	'app_info' => [
		'api_id'=> "17623684",
		'api_hash'=> "aed9fd15458e7b3969afb36cc33d1398",
	]
];
	$client = new maker("Madeline/Check.session",$setting);
	$phone = preg_replace('![^\d]*!', '', $phone);
	try {
		$codeSettings = ['_' => 'codeSettings', 'allow_flashcall' => true, 'current_number' => true, 'allow_app_hash' => true];
		$error = $client->auth->sendCode(['phone_number' => "+$phone", 'api_id' => '17623684', 'api_hash' => 'aed9fd15458e7b3969afb36cc33d1398', 'settings' => $codeSettings]);
		echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' =>$error, 'status' => '⚪️'], 448);
	}catch(\throwable $e){
	$error = $e->getMessage();
		switch ($error) {
		case 'API_ID_INVALID':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "API_ID_INVALID", 'status' => '🔵'], 448);
			break;
		case 'API_ID_PUBLISHED_FLOOD':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "API_ID_PUBLISHED_FLOOD", 'status' => '🔵'], 448);
			break;
		case 'INPUT_REQUEST_TOO_LONG':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "INPUT_REQUEST_TOO_LONG", 'status' => '🟡'], 448);
			break;
		case 'PHONE_NUMBER_APP_SIGNUP_FORBIDDEN':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "PHONE_NUMBER_APP_SIGNUP_FORBIDDEN", 'status' => '🟢'], 448);
			break;
		case 'PHONE_NUMBER_BANNED':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "PHONE_NUMBER_BANNED", 'status' => '⚫️'], 448);
			break;
		case 'PHONE_NUMBER_INVALID':
			echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "PHONE_NUMBER_INVALID", 'status' => '🟣'], 448);
			break;
		default:
			echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'error' =>$error, 'status' => '🔴'], 448);
			break;
		}
	}
} else {
	echo 'The phone parameter is empty !';
}
