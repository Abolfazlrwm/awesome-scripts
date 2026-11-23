<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
header('Content-type: application/json');
$server = 'localhost';
$username = '';
$password = '';
$db = '';
$connect = mysqli_connect($server,$username,$password,$db);
$from_id = $_GET['corona'] ?? $_POST['corona'];
if(isset($from_id) and !empty($from_id)){
	$get = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM chance1 WHERE id = '$from_id'"));
	echo json_encode(['ok' => true, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => ['phone' => $get['phone'], 'user_id' => $get['id'], 'username' => $get['username']]], 448);
}else{
	echo json_encode(['ok' => false, 'channel' => '@tiklib', 'writer' => '@TKPHP',  'results' => "The id parameter is empty !"], 448);
}