<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */


include "bot.php";
// table creator

$connect->query("CREATE TABLE user (
    id int(10) PRIMARY KEY,
	step varchar(20) NOT NULL,
	step2 varchar(20) NOT NULL,
	coin int(10) NOT NULL,
	member int(10) NOT NULL,
	name varchar(50) NOT NULL,
	photo varchar(100) NOT NULL,
	sex varchar(15) NOT NULL,
	username varchar(100) NOT NULL,
	city varchar(50) NOT NULL,
	old int(10) NOT NULL,
	bio varchar(200) NOT NULL,
	stats varchar(50) NOT NULL,
	`like` int(10) NOT NULL,
	dislike int(10) NOT NULL,
	blocklist varchar(1000) NOT NULL,
	frindlist varchar(1000) NOT NULL,
	liker varchar(2000) NOT NULL,
	gps varchar(20) NOT NULL,
	baner varchar(100) NOT NULL,
	daily varchar(10) NOT NULL,
	side int(10) NOT NULL,
	time varchar(20) NOT NULL
  )");
    $connect->query("CREATE TABLE chat (
    user_id int(10) PRIMARY KEY
  )");
  $connect->query("CREATE TABLE chatvip (
    user_id int(10) PRIMARY KEY,
	type varchar(50) NOT NULL,
	stats varchar(100) NOT NULL
  )");
        $connect->query("CREATE TABLE daily (
    run varchar(20) PRIMARY KEY,
  	time varchar(20) NOT NULL,
	user int(8) NOT NULL
  )");
    $connect->query("CREATE TABLE sendall (
    run varchar(20) PRIMARY KEY,
  	step varchar(20) NOT NULL,
  	msgid varchar(20) NOT NULL,
	text varchar(2000) NOT NULL,
	chat varchar(30) NOT NULL,
	user int(8) NOT NULL
  )");
if ($conn->connect_error) {
    die("خطا ار اتصال به خاطره : " . $conn->connect_error);
} 
echo "دیتابیس متصل است !";
//===============================================
?>