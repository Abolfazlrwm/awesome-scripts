<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if (in_array($from_id,$admin)){

$_menp = json_encode([
'keyboard' => [
[['text'=>"👤آمار"]],
[['text'=>"⚙️خاموش/روشن"],['text'=>"📝تنظیمات کانال"]],
[['text'=>"🗨️پیام به کاربر"],['text'=>"🔍 مشخصات کاربر"]],
[['text'=>"💸دادن موجودی"],['text'=>"➖کسر موجودی"]],
[['text'=>"مسدود"],['text'=>"رفع مسدود"]],
[['text'=>"✏️تنظیم ولت"],['text'=>"⚙️تنظیمات ضریب"]],
[['text'=>"🗨️پیام همگانی"],['text'=>"🗨️ فوروارد همگانی"]],
[['text'=>"🔙برگشت"]],
],'resize_keyboard'=>true]);
$_backq = json_encode([
'keyboard' => [
[['text'=>"برگشت"]],
],'resize_keyboard'=>true]);
$zarib_d = json_encode([
'keyboard' => [
[['text'=>"⭕ضریب فوتبال"],['text'=>"⭕ضریب تاس"]],
[['text'=>"⭕ضریب بولینگ"],['text'=>"⭕ضریب بسکتبال"]],
[['text'=>"⭕ضریب دارت"],['text'=>"⭕ضریب اسلات"]],
[['text'=>"برگشت"]],
],'resize_keyboard'=>true]);
$chan_set = json_encode([
'keyboard' => [
[['text'=>"➕افزودن چنل"],['text'=>"➖حذف چنل"]],
[['text'=>"برگشت"]],
],'resize_keyboard'=>true]);
if($text == "/admin" || $text == "پنل" || $text == "برگشت"){
    $bot->sendMessage($from_id,"👤ادمین عزیز به پنل مدیریت خوش آمدید ",$_menp);
    $conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");		

}

if($text == "➕افزودن چنل"){
$bot->sendMessage($from_id,"⚙️به بخش تنظیمات کانال خوش امدید",$chan_set);
 }

if ( $text == "⚙️خاموش/روشن" ) {
if($dataa['on'] == "on") {
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['on'] = "off";
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ربات با موفقیت خاموش شد</b>");
}else{ 
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['on'] = "on";
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ربات با موفقیت روشن شد</b>");
}
}

 if ($text == '🗨️ فوروارد همگانی' and $tc == 'private' and in_array($from_id,$admin)){
bot('sendmessage',['chat_id'=>$from_id,
'text'=>"📝جهت فوروارد همگانی پیام خود را ارسال کنید.

📌توجه : *پیام شما برای همه ی کاربران این ربات به صورت فوروارد ارسال میشود.*",
 'parse_mode' => "MarkDown",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'froward' WHERE `id` = '$from_id' LIMIT 1");		
}
if ($text == '🗨️پیام همگانی' and $tc == 'private' and in_array($from_id,$admin)) {
bot('sendmessage',['chat_id'=>$from_id,
'text'=>"📝برای ارسال پیام به کاربران . پیام خود را ارسال کنید

*📌 توجه پیام شما برای تموم کاربران ارسال میشود*",
 'parse_mode' => "MarkDown",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'senjl' WHERE `id` = '$from_id' LIMIT 1");
}
if ($user['step'] == 'froward'  and $text != "/panel"and !in_array($text,$arr)) {
bot('sendmessage',['chat_id'=>$from_id,
'text'=>"🔗پیام شما در صف ارسال قرار گرفت.

*📌به زودی نتیجه برای شما ارسال میشود.*",
'reply_to_message_id'=>$message_id,
 'parse_mode' => "MarkDown",
]);
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	
$conn->query("UPDATE `sendpm` SET `step` = 'forward' , `text` = '$message_id' , `chat` = '$from_id' LIMIT 1");		
}
if ($user['step'] == 'senjl'  and $text != "/panel"and !in_array($text,$arr)) {
$photo = $message->photo[count($message->photo)-1]->file_id;
$caption = $update->message->caption;
bot('sendmessage',['chat_id'=>$from_id,
'text'=>"🔗پیام شما در صف ارسال قرار گرفت.

*📌به زودی نتیجه برای شما ارسال میشود.*",
'reply_to_message_id'=>$message_id,
 'parse_mode' => "MarkDown",
]);
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
$conn->query("UPDATE `sendpm` SET step = 'send' , `text` = '$text$caption' , `chat` = '$photo' LIMIT 1");			
}



if($text == "👤آمار"){
if($dataa['on'] == "on"){
$s = "روشن";
}else{ 
$s = "خاموش";
}
$ban  = mysqli_query($conn,"select `id` from `block`")->num_rows;
$mem = mysqli_query($conn,"select `id` from `user`")->num_rows;

$bot->sendMessage($from_id,"<b>تعداد کاربران : $mem \n تعداد مسدود شدگان : $ban\nوضعیت ربات : $s\n\n➖ ➖ ➖ ➖\n⭐ضریب فوتبال : {$dataa['fot']}\n⭐ضریب تاس : {$dataa['tas']}\n⭐ضریب بولینگ : {$dataa['bol']}\n⭐ضریب بسکتبال : {$dataa['bas']}\n⭐ضریب دارت : {$dataa['dar']}\n⭐ضریب اسلات : {$dataa['slt']}\n\nدرصورت وجود مشکل با پشتیبانی در ارتباط باشید</b>");
}
if($text == "🗨️پیام به کاربر"){
$bot->sendMessage($from_id,"<b>🧑‍🔧لطفا آیدی عددی فرد را ارسال کنید.</b>",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'sendmp' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "sendmp" and !in_array($from_id,$arry)){
$bot->sendMessage($from_id,"<b>🧑‍🔧لطفا پیام خود را ارسال کنید..</b>",$_backq);
 $conn->query("UPDATE `user` SET `step2` = '$text' WHERE `id` = '$from_id' LIMIT 1");		
  $conn->query("UPDATE `user` SET `step` = 'sendert' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "sendert" and !in_array($from_id,$arry)){
$bot->sendMessage($from_id,"<b>🧑‍🔧پیام شما ارسال شد</b>",$_backq);
$bot->bot('CopyMessage',[
'from_chat_id' => $chat_id,
'chat_id' => $user['step2'],
'message_id' => $message_id,
]);
 $conn->query("UPDATE `user` SET `step2` = 'none' WHERE `id` = '$from_id' LIMIT 1");		
  $conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	
  }
  if($text == "🔍 مشخصات کاربر"){
  $bot->sendMessage($from_id,"<b>🧑‍🔧لطفا آیدی عددی کاربر ارسال کنید</b>",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'sendin' WHERE `id` = '$from_id' LIMIT 1");		
}
  if($user['step'] == "sendin" and !in_array($from_id,$arry)){
  $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '$text' LIMIT 1"));
  if($user['id'] == true){
$bot->sendMessage($from_id,"<b>✅اطلاعات کاربر دریافت شد : \n تعداد سکه : {$user['coin']} \n آیدی عددی : $text</b>",$_backq);
 $conn->query("UPDATE `user` SET `step2` = 'none' WHERE `id` = '$from_id' LIMIT 1");		
  $conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");		
}else{
$bot->sendMessage($from_id,"<b>❌این کاربر یافت نشد</b>",$_backq);
}}

if($text == "⚙️تنظیمات ضریب"){
    $bot->sendMessage($from_id,"🧑‍🔧به بخش تنظیمات ضریب خوش اومدی⚙️",$zarib_d);
 }
if($text == "⭕ضریب اسلات"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['slt'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی اسلات : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zarslt' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zarslt" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['slt'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}
if($text == "⭕ضریب دارت"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['dar'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی دارت : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zardar' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zardar" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['dar'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}
if($text == "⭕ضریب بسکتبال"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['bas'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی بسکتبال : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zarbas' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zarbas" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['bas'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}
if($text == "⭕ضریب بولینگ"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['bol'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی بولینگ : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zarbol' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zarbol" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['bol'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}
if($text == "⭕ضریب تاس"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['tas'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی تاس : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zartas' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zartas" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['tas'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}
if($text == "⭕ضریب فوتبال"){
$dataa = json_decode(file_get_contents("setting/setting.json"), true);
$er = $dataa['fot'];
$bot->sendMessage($from_id,"⭕جهت تنظیم ضریب ، ضریب را به عدد وارد کنید \n\n 🧑‍🔧ضریب فعلی بازی فوتبال : $er",$_backq);
    $conn->query("UPDATE `user` SET `step` = 'zarfot' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == "zarfot" and !in_array($text,$arry)) {
if (is_numeric($text)){
$res = json_decode(file_get_contents("setting/setting.json"), true);
$res['fot'] = $text;
file_put_contents("setting/setting.json", json_encode($res));
$bot->sendMessage($from_id,"<b>✅ضریب تنظیم شد \n\n ضریب فعلی : $text</b>",$_backq);
}else{
$bot->sendMessage($from_id,"<b>⚠️فقط عدد وارد کنید</b>",$_backq);
}
}

if ($text == "✏️تنظیم ولت") {
$result = mysqli_query($conn,"SELECT * FROM metko");
$row = mysqli_fetch_assoc($result);
$start = $row['start'];
$conn->query("UPDATE `user` SET `step` = 'setwallet' WHERE `id` = '$from_id' LIMIT 1");		
$bot->sendMessage($from_id, "<b>📝 ولت تنظیمی شما : {$start} \n جهت تنظیم ولت جدید، آدرس ولت خود را ارسال کنید.</b>", $_backq);

}
if($user['step'] == 'setwallet' && $text != "برگشت") {
$conn->query("UPDATE `metko` SET `start` = '$text' ");	
$bot->sendMessage($from_id,"<b>✅ولت شما تنظیم شد.</b>",$_backq);
}
if ($text == '💸دادن موجودی'){
$bot->sendMessage($from_id,"<b>💸جهت ارسال موجودی به کاربر آیدی عددی فرد را ارسال کنید</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'sendadmin' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == 'sendadmin' and !in_array($text,$arry)) {
if(ctype_digit($text)){
$conn->query("UPDATE `user` SET `step2` = '$text' WHERE `id` = '$from_id' LIMIT 1");
$bot->sendMessage($from_id,"<b>✏تعداد سکه جهت ارسال را وارد کنید.</b>",$_backq);
	         $conn->query("UPDATE `user` SET `step` = 'sendct' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id,"<b>⚠مقادیر ارسالی صحیح نمیباشد.</b>",$_backq);
	 }
   }
if($user['step'] == 'sendct' and !in_array($text,$arry)) {
if(ctype_digit($text)){
$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '{$user['step2']}' LIMIT 1"));			 
if($users['id'] == true){
$coin = $users['coin'] + $text;
$conn->query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '{$user['step2']}' LIMIT 1");
$userr = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '{$user['step2']}' LIMIT 1"));			 
$bot->sendMessage($from_id,"<b>✅تعداد $text سکه به کاربر {$user['step2']} ارسال شد.\nموجودی جدید کاربر : {$userr['coin']}</b>",$_backq);
$bot->sendMessage($user['step2'],"<b>✅ تعداد $text سکه از طرف مدیریت به شما داده شد.\n موجودی جدید : $coin</b>");
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
$conn->query("UPDATE `user` SET `step2` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id,"<b>⚠این کاربر عضو ربات نمیباشد</b>",$_backq);
	}
	}else{
$bot->sendMessage($from_id,"<b>⚠مقادیر ارسالی صحیح نمیباشد.</b>",$_backq);
  }
    }
if ($text == '➖کسر موجودی' and $tc == 'private' and in_array($from_id,$admin)){
$bot->sendMessage($from_id,"<b>💸جهت کسر موجودی به کاربر آیدی عددی فرد را ارسال کنید</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'chgadmin' WHERE `id` = '$from_id' LIMIT 1");		
}
if($user['step'] == 'chgadmin' and !in_array($text,$arry)) {
if(ctype_digit($text)){
$conn->query("UPDATE `user` SET `step2` = '$text' WHERE `id` = '$from_id' LIMIT 1");
$bot->sendMessage($from_id,"<b>✏تعداد سکه جهت کسر را وارد کنید.</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'chgct' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id,"<b>⚠مقادیر ارسالی صحیح نمیباشد.</b>",$_backq);
	 }
	}
if($user['step'] == 'chgct' and !in_array($text,$arry)) {
$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '{$user['step2']}' LIMIT 1"));			 
if(ctype_digit($text)){
if($users['id'] == true){
if($users['coin'] >= $text){
$coin = $users['coin'] - $text;
$conn->query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$all[0]' LIMIT 1");
$userr = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '{$user['step2']}' LIMIT 1"));			 
$bot->sendMessage($from_id,"<b>✅تعداد $text سکه از کاربر $name کم شد.\nموجودی جدید کاربر : {$userr['coin']}</b>",$_backq);
$bot->sendMessage($user['step2'],"<b>✏️ تعداد $text سکه  از طرف مدیریت ربات از شما کسر شد.\n💰 موجودی جدید شما : $coin سکه");
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
$conn->query("UPDATE `user` SET `step2` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id,"<b>⚠این فرد انقدر موجودی برای کسر ندارد لطفا عددی پایین تر وارد کنید</b>",$_backq);
	         }
}else{
$bot->sendMessage($from_id,"<b>⚠این کاربر عضو ربات نمیباشد</b>",$_backq);
	         }
	         }else{
$bot->sendMessage($from_id,"<b>⚠مقادیر ارسالی صحیح نمیباشد.</b>",$_backq);
	}
}
if($text == "مسدود" ) {
$bot->sendMessage($from_id,"<b>🧑‍🎤جهت مسدود کردن کاربر آیدی عددی او را ارسال کنید</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'blockc' WHERE `id` = '$from_id' LIMIT 1");		
}
if ($text == 'رفع مسدود'){
$bot->sendMessage($from_id,"<b>🧑‍🔧 جهت حذف مسدودیت کاربر آیدی عددی او را ارسال کنید.</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'unblock' WHERE `id` = '$from_id' LIMIT 1");    
}
if($user['step'] == "blockc" and !in_array($text,$arry)) {
$bot->sendMessage($from_id,"<b>👩‍🎤کاربر با موفقیت از ربات مسدود شد.</b>",$_backq);
$conn->query("INSERT INTO `block` (`id`) VALUES ('$text')");
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}
if($user['step'] == "unblock" and !in_array($text,$arry)) {
$bot->sendMessage($from_id,"<b>🧑‍🔧حساب کاربری فرد با موفقیت آزاد شد.</b>",$_backq);
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
$conn->query("DELETE FROM `block` WHERE `id` = '{$text}' LIMIT 1 ");
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$userid' LIMIT 1");
}
if ($text == '➕افزودن چنل'and in_array($from_id,$admin)) {
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"📣جهت تنظیم چنل جوین اجباری آیدی چنل خود را بدون @ وارد کنید",
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'addchannel' WHERE `id` = '$from_id' LIMIT 1");		
}
if ($user['step'] == 'addchannel' and !in_array($text,$arr)) {
if(strpos($text,'@' ) == false or strpos($text,'https://t.me' ) == false){
$status = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$text&user_id=".$idbot.""));
$statuxds2 = $status->result->status;
if($statuxds2 == "administrator"){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅چنل @$text با موفقیت تنظیم شد",
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	  
$conn->query("INSERT INTO `channels` (`id` , `admin` , `tarikh`) VALUES ('$text' , '$from_id' , '1402')");
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"⭕ربات در کانال @$text ادمین نیست",
'reply_markup'=>$_backp
]);
}}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"🚫لطفا آیدی را بدون @ وارد کنید",
'reply_markup'=>$_backp
]);
}}



if ($text == '➖حذف چنل'and in_array($from_id,$admin)) {
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"❌جهت حذف کانال ایدی ان را بدون @ وارد کنید",
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'delch' WHERE `id` = '$from_id' LIMIT 1");	
}
elseif($user['step'] == 'delch' and !in_array($text,$arr)) {
if(strpos($text,'@' ) == false or strpos($text,'https://t.me' ) == false){
$use = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `channels` WHERE `id` = '$text' LIMIT 1"));
if($use['id'] == true){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"✅کانال @$text با موفقیت حذف شد",
'reply_markup'=>$_backp
]);
$conn->query("DELETE FROM `channels` WHERE `id` = '$text' LIMIT 1 ");
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	
}else{
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌این کانال تنظیم نشده بود",
'reply_markup'=>$_backp
]);
}}else{
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌لطفا آیدی کانال را بدون @ وارد کنید",
'reply_markup'=>$_backp
]);
$conn->query("UPDATE `user` SET `step` = 'tasjhaxd2' WHERE `id` = '$from_id' LIMIT 1");	
}}
}