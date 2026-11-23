<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */


include('config.php');
$connect = mysqli_connect($servername, $username, $password, $dbname);
if($connect->query("SELECT * FROM `user`") == false){
mysqli_multi_query($connect,"CREATE TABLE user (
    id bigint(20) PRIMARY KEY,
 step varchar(30) NOT NULL,
 stock int(10) NOT NULL,
 member int(10) NOT NULL,
 listby varchar(2000) NOT NULL,
 inviter varchar(20) NOT NULL,
 service varchar(200) NOT NULL,
number bigint(20) DEFAULT '0'
  );

CREATE TABLE `gold` (
`name` text NOT NULL,
`viber` int(200) NOT NULL,
`amazon` int(200) NOT NULL,
`tiktok` int(200) NOT NULL,
`facebook` int(200) NOT NULL,
`microsoft` int(200) NOT NULL,
`instagram` int(200) NOT NULL,
`yahoo` int(200) NOT NULL,
`google` int(200) NOT NULL,
`telegram` int(200) NOT NULL,
`whatsapp` int(200) NOT NULL,
`line` int(200) NOT NULL,
`twitter` int(200) NOT NULL,
`paypal` int(200) NOT NULL,
`viberCount` varchar(200) DEFAULT '0',
`amazonCount` varchar(200) DEFAULT '0',
`tiktokCount` varchar(200) DEFAULT '0',
`facebookCount` varchar(200) DEFAULT '0',
`microsoftCount` varchar(200) DEFAULT '0',
`instagramCount` varchar(200) DEFAULT '0',
`yahooCount` varchar(200) DEFAULT '0',
`googleCount` varchar(200) DEFAULT '0',
`telegramCount` varchar(200) DEFAULT '0',
`whatsappCount` varchar(200) DEFAULT '0',
`lineCount` varchar(200) DEFAULT '0',
`twitterCount` varchar(200) DEFAULT '0',
`paypalCount` varchar(200) DEFAULT '0'
);

CREATE TABLE `silver` (
`name` text NOT NULL,
	`viber` varchar(200) NOT NULL,
	`amazon` varchar(200) NOT NULL,
`tiktok` varchar(200) NOT NULL,
	`facebook` varchar(200) NOT NULL,
`microsoft` varchar(200) NOT NULL,
	`instagram` varchar(200) NOT NULL,
`yahoo` varchar(200) NOT NULL,
	`google` varchar(200) NOT NULL,
`telegram` varchar(200) NOT NULL,
	`whatsapp` varchar(200) NOT NULL,
`line` varchar(200) NOT NULL,
	`twitter` varchar(200) NOT NULL,
`paypal` varchar(200) NOT NULL
    );
CREATE TABLE `sendall` (
   step varchar(20) NOT NULL,
   msgid varchar(10) NOT NULL,
 text varchar(2000) NOT NULL,
 chat varchar(20) NOT NULL,
 user int(10) NOT NULL
  );
CREATE TABLE `daily` (
   time varchar(20) NOT NULL,
 user int(10) NOT NULL
  );
CREATE TABLE `pay` (
   `id` varchar(400) PRIMARY KEY);
INSERT INTO sendall (msgid, step, text,  chat, user) VALUES ('', 'none', '', '', '0');
INSERT INTO daily (time, user) VALUES ('', '0');");
}
if ($connect->connect_error) {
    die("خطا ار اتصال به خاطره : " . $connect->connect_error);
} else echo "دیتابیس متصل است !";
?>