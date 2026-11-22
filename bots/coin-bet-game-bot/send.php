<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

include 'config.php';
$conn = new mysqli($localhost,$user,$password,$name);
$conn->query("SET NAMES 'utf8'"); $conn->set_charset('utf8mb4');
error_reporting(E_ALL);

date_default_timezone_set('Asia/Tehran');

$send = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `sendpm` LIMIT 1"));




if($send['step'] == 'send')
{
$alluser = mysqli_num_rows(mysqli_query($conn,"select id from `user`"));
$users = mysqli_query($conn,"SELECT id FROM `user` LIMIT 100 OFFSET {$send['user']}");
if($send['chat'] == false){
while($row = mysqli_fetch_assoc($users))
     bot('sendmessage',[
        'chat_id'=>$row['id'],        
		'text'=>$send['text'],
        ]);	
}
else

{
while
(
$row = mysqli_fetch_assoc($users))
  bot('sendphoto',[
	'chat_id'=>$row['id'],
	'photo'=>$send['chat'],
	'caption'=>$send['text'],
 ]);
}

$conn->query("UPDATE `sendpm` SET `user` = `user` + 100 LIMIT 1");
if($send['user'] + 100 >= $alluser)
{
  bot('sendmessage',[
      'chat_id'=>$admin[0],
      'text'=>"پیام برای همه کابران ارسال شد",
 ]);
$conn->query("UPDATE `sendpm` SET `step` = 'none' , `text` = '' , `user` = '0' , `chat` = '' LIMIT 1");	
}
}
elseif($send['step'] == 'forward')
{
$alluser = mysqli_num_rows(mysqli_query($conn,"select id from `user`"));
$users = mysqli_query($conn,"SELECT id FROM `user` LIMIT 100 OFFSET {$send['user']}");
while(
$row = mysqli_fetch_assoc($users))
bot('ForwardMessage',[
'chat_id'=>$row['id'],   
'from_chat_id'=>$send['chat'],
'message_id'=>$send['text'],
]);	
$conn->query("UPDATE `sendpm` SET `user` = `user` + 100 LIMIT 1");
if($send['user'] + 100 >= $alluser){
  bot('sendmessage',[
      'chat_id'=>$admin[0],
      'text'=>"پیام برای همه کابران ارسال شد",
 ]);
$conn->query("UPDATE `sendpm` SET `step` = 'none' , `text` = '' , `user` = '0' , `chat` = '' LIMIT 1");		
}
}