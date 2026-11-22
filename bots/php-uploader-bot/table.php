<?php
error_reporting(0);
// Dev: @moeinabasyan 
include "config.php";
// Dev: @moeinabasyan 
define('API_KEY',$API_KC); 
// Dev: @moeinabasyan 
$connect->query("CREATE TABLE user (
id bigint(64) PRIMARY KEY,
step varchar(5000) NOT NULL,
step2 varchar(5000) NOT NULL,
step3 varchar(5000) NOT NULL,
step4 varchar(5000) NOT NULL,
step5 varchar(5000) NOT NULL,
spam varchar(20) NOT NULL )");
// Dev: @moeinabasyan 
$connect->query("CREATE TABLE settings (
botid varchar(30) PRIMARY KEY,
bot_mode varchar(20) NOT NULL,
mtn_s_ch TEXT(25000) NOT NULL,
chupl varchar(500) NOT NULL,
forall varchar(20) NOT NULL,
sendall varchar(20) NOT NULL,
tedad varchar(20) NOT NULL,
text TEXT(25000) NOT NULL,
chat_id varchar(20) NOT NULL,
darkmindstm varchar(20) NOT NULL,
is_all varchar(20) NOT NULL,
dearahmadi varchar(20) NOT NULL,
msg_id varchar(20) NOT NULL )");
// Dev: @moeinabasyan 
$connect->query("CREATE TABLE channels (
idoruser varchar(30) PRIMARY KEY,
link varchar(200) NOT NULL )");
// Dev: @moeinabasyan 
$connect->query("CREATE TABLE files (
code varchar(3070) PRIMARY KEY,
msg_id varchar(5000) NOT NULL,
ghfl_ch varchar(5000) NOT NULL,
zd_filter varchar(5000) NOT NULL,
file_id varchar(5000) NOT NULL,
file_size varchar(5000) NOT NULL,
file_type varchar(5000) NOT NULL,
id varchar(5000) NOT NULL,
dl bigint(250) NOT NULL,
pass varchar(5000) NOT NULL,
mahdodl varchar(5000) NOT NULL,
tozihat TEXT(25000) NOT NULL,
zaman varchar(5000) NOT NULL )");
// Dev: @moeinabasyan 
$connect->query("CREATE TABLE dbremove (
id int(50) NOT NULL,
message_id int(250) NOT NULL,
time int(250) NOT NULL )");
// Dev: @moeinabasyan 
if($settings['botid'] == null ){
    	$connect->query("INSERT INTO settings (botid , bot_mode , mtn_s_ch , chupl , forall , sendall , tedad , text , chat_id , darkmindstm , is_all , dearahmadi , msg_id) VALUES ('$botid', 'on', 'none', 'none', 'false', 'false', '0', 'none', 'none', 'none', 'no', '1', 'none')");
} // @moeinabasyan
echo "<b>Table Created! @Sourrce_kade</b>";
echo "</br>"."</br>"."--------- @Sourrce_kade";
echo "</br>"."</br>"."جهت اطمینان خاطر از phpMyAdmin چک کنید.
https://t.me/Sourrce_kade";