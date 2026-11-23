<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$timer = time();
include("jdf.php");
include("button.php");
include("function.php");
//-----------------------------------------------------------------------------------------------
$get = bot('getMe');
$bot_username = $get->result->username;
$bot_first_name = $get->result->first_name;
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)){
$first = $update->message->from->first_name;
$last = $update->message->from->last_name;
$username = $update->message->from->username;
$text = $update->message->text;
$tc = $update->message->chat->type;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
}
if (isset($update->callback_query)){
$first = $update->callback_query->from->first_name;
$last = $update->callback_query->from->last_name;
$username = $update->callback_query->from->username;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$from_id = $update->callback_query->from->id;
}
//============================================================================//
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$from_id' LIMIT 1"));
$admin = array("971881348","971881348","971881348"); // ای دی عددی ادمین ها
//============================================================================//
$step = $datas["step"];
$name = $datas["name"];
$coin = $datas["coin"];
$tas = $datas["tas"];
$score = $datas["score"];
$game = $datas["game"];
$win = $datas["win"];
$warn = $datas["warn"];
$info = $datas["data"];
$LinkPhoto = $datas["photo"];
$vip = $datas["vip"];
$like = $datas["like"];
$follower = $datas["follower"];
$following = $datas["following"];
$server = $datas["server"];
if($warn > $timer){
$vaght = $warn - $timer;
$echo = TimeFormat($vaght);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ شما به مدت $echo از ربات مسدود شدید !
✔️ به دلیل گزارش فراوان کاربران !
",
]); 
exit();
}
$time = jdate('H:i:s');
$date = jdate("Y/m/d");
//============================================================================//
if($score >= "0"){
$lig = "🤍";
}
if($score >= "115"){
$lig = "🎗";
}
if($score >= "245"){
$lig = "🏵";
}
if($score >= "390"){
$lig = "💥";
}
if($score >= "550"){
$lig = "⚔️";
}
if($score >= "725"){
$lig = "👻";
}
if($score >= "915"){
$lig = "☀";
}
if($score >= "1120"){
$lig = "✨";
}
if($score >= "1340"){
$lig = "💫";
}
if($score >= "1575"){
$lig = "🦀";
}
if($score >= "1825"){
$lig = "🦂";
}
if($score >= "2090"){
$lig = "🦅";
}
if($score >= "2370"){
$lig = "🌈";
}
if($score >= "2665"){
$lig = "☄️";
}
if($score >= "2975"){
$lig = "🌪";
}
if($score >= "3300"){
$lig = "❄️";
}
if($score >= "3640"){
$lig = "🐉";
}
//============================================================================//
if($data == "fake"){
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"🥀 این دکمه فقط برای نمایش اطلاعات است",
'show_alert' =>false
]);
}
if($data == "game"){
$info = GetRanks("game");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📈 لیست برترین های بیشترین بازی

📉 بازی های شما : $game

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "💫 بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "🏆 بیشترین برد", 'callback_data' => "win"],['text' => "❤️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "✔️ بیشترین بازی", 'callback_data' => "game"],['text' => "🌟 بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($data == "follower"){
$info = GetRanks("follower");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📈 لیست بیشترین های دارای فالوور

📉 تعداد فالوور های شما : $follower

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "✔️ بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "🏆 بیشترین برد", 'callback_data' => "win"],['text' => "❤️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "❄️ بیشترین بازی", 'callback_data' => "game"],['text' => "🌟 بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($data == "like"){
$info = GetRanks("like");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📈 لیست برترین های لایک

📉 لایک های شما : $like

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "💫 بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "🏆 بیشترین برد", 'callback_data' => "win"],['text' => "✔️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "❄️ بیشترین بازی", 'callback_data' => "game"],['text' => "🌟 بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($data == "score"){
$info = GetRanks("score");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📈 لیست برترین های امتیاز

📉 امتیاز های شما : $score

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "💫 بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "🏆 بیشترین برد", 'callback_data' => "win"],['text' => "❤️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "❄️ بیشترین بازی", 'callback_data' => "game"],['text' => "✔️ بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($data == "win"){
$info = GetRanks("win");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📈 لیست برترین های بیشترین برد

📉 برد های شما : $win

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "💫 بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "✔️ بیشترین برد", 'callback_data' => "win"],['text' => "❤️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "❄️ بیشترین بازی", 'callback_data' => "game"],['text' => "🌟 بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($data == "editname"){
$connect->query("UPDATE user SET step = 'editnames' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
یه نام مستعار برای خودت وارد کن
حتما باید فارسی باشه

مثال : علی , محمد و . . .
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر نام", 'callback_data' => "cancel"]],
],
])
]);
}
if($data == "editphoto"){
$connect->query("UPDATE user SET step = 'editphotos' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💫 عکس جدید پروفایل خود را ارسال کنید !

⚠️ *نکته* : فقط بصورت عکس ارسال کنید نه فایل ! 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر عکس", 'callback_data' => "cancel"]],
],
])
]);
}
if($data == "newac"){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ آیا می‌خواهید اکانت خود را *بازیابی* کنید یا *توکن اکانت* خود را جهت بازیابی بدانید ؟
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "⚙ توکن اکانت من", 'callback_data' => "mytoken"]],
[['text' => "🔋 بازیابی اکانت حذف شده", 'callback_data' => "Recovery"]],
],
])
]);
}
if($data == "statistics"){
$darsad = GetPercent($win,$game);
$emoji = PercentEmoji($darsad);
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$darsad", 'callback_data'=>"fake"]],
[['text' => "$emoji", 'callback_data'=>"fake"]],
]
])
]);
}
if($data == "mytoken"){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$key = MakeID($from_id);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
♻️ شما فقط جهت بازیابی این اکانت خود میتوانید با استفاده از توکن زیر بازیابی را انجام دهید 

🔐 TOKEN : `$key`

⚠️ لطفاً آنرا کپی کنید برای بازیابی اکانت خود !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}
if($data == "Recovery"){
$connect->query("UPDATE user SET step = 'SendRecovery' WHERE id = '$from_id' LIMIT 1");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
♻️ لطفاً جهت بازیابی اکانت خود توکن حساب خود را ارسال کنید 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}
if(strpos($step,'LogIn-') !== false and strpos($data,'login-') !== false){
$id = str_replace('LogIn-',null,$step);
$timers = str_replace('login-',null,$data);
$tayed = $timer - $timers;
if($tayed < 600){
$idss = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
if($idss['step'] != "reset"){
$set1 = $idss['score'] + $score;
$set2 = $idss['coin'] + $coin;
$connect->query("UPDATE user SET step = 'none' , coin = '$set2' , score = '$set1' WHERE id = '$from_id' LIMIT 1");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
🎉 تبریک اکانت شما بازیابی شد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
$connect->query("DELETE FROM `user` WHERE id = '$id'");
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"⚠️ *هشدار امنیتی* : یک نفر وارد اکانت شما شده است !",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
❌ متاسفانه این اکانت قابل بازیابی نمی‌باشد توسط شخص دیگری بازیابی شده است !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⏳ زمان تایید بازیابی اکانت به پایان رسیده است ! این مراحل را مجدد طی کنید",
'show_alert' =>true
]);
}
}
if($data == "cancel"){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id' => $message_id,
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
if($data == "ligaal"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🛒 لیگ های شخصی خودتون رو بسازید

از لیست پایین میتونید لیگتون رو عوض کنید.
یا لیگ جدید اضافه کنید!

انتخاب کنید 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ خرید لیگ اختصاصی",'callback_data'=>"fake"]],
]
])
]); 
}
if(strpos($data,'pm-') !== false){
$id = str_replace('pm-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
$connect->query("UPDATE user SET data = 'pm-$id' WHERE id = '$from_id' LIMIT 1");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
💭 شما درحال ارسال پیام خصوصی به ( $reporter ) هستید 

💬 پیام خود را بصورت متن ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف", 'callback_data' => "nopm"]],
],
])
]);
}
if($data == "nopm"){
$connect->query("UPDATE user SET data = 'none' WHERE id = '$from_id' LIMIT 1");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✔️ درخواست شما لغو شد
",
'parse_mode'=>'MarkDown',
]);
exit();
}
if(strpos($data,'report-') !== false){
$id = str_replace('report-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
⚠️ دلیل گزارش کاربر ( $reporter ) را انتخاب کنید ! 

🎈 درصورت لغو گزارش روی گزینه *انصراف* بزنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "فحش دادن", 'callback_data' => "fosh-$id"]],
[['text' => "لو دادن نقش", 'callback_data' => "lodadan-$id"]],
[['text' => "بی احترامی", 'callback_data' => "biahtrami-$id"]],
[['text' => "تقلب در بازی", 'callback_data' => "taghalob-$id"]],
[['text' => "اسم نامناسب", 'callback_data' => "asmbad-$id"]],
[['text' => "تبلیغات", 'callback_data' => "tabliq-$id"]],
[['text' => "میگه بیا پیوی", 'callback_data' => "biapv-$id"]],
[['text' => "به هم زدن نظم بازی", 'callback_data' => "nazm-$id"]],
[['text' => "شماره دادن", 'callback_data' => "shomare-$id"]],
[['text' => "سایر موارد", 'callback_data' => "sayer-$id"]],
[['text' => "انصراف", 'callback_data' => "norep"]],
],
])
]);
}
if($data == "norep"){
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✔️ درخواست شما لغو شد
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'fosh-') !== false){
$id = str_replace('fosh-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : فحش دادن

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'lodadan-') !== false){
$id = str_replace('lodadan-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : لو دادن نقش

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'biahtrami-') !== false){
$id = str_replace('biahtrami-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : بی احترامی

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'taghalob-') !== false){
$id = str_replace('taghalob-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : تقلب در بازی

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'asmbad-') !== false){
$id = str_replace('asmbad-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : نام نامناسب

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'sayer-') !== false){
$id = str_replace('sayer-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : سایر موارد

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'shomare-') !== false){
$id = str_replace('shomare-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : دادن شماره

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'biapv-') !== false){
$id = str_replace('biapv-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : میگه بیا پیوی

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'nazm-') !== false){
$id = str_replace('nazm-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : به هم زدن نظم بازی

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
if(strpos($data,'tabliq-') !== false){
$id = str_replace('tabliq-',null,$data);
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت کاربر ( $reporter ) گزارش شد و بزودی پیگیری میشود ! 

🎈 دلیل گزارش : تبلیغات

🌹 *تشکر بابت همکاری شما !*
",
'parse_mode'=>'MarkDown',
]);
}
//============================================================================//
if(strpos($data,'likers-') !== false){
$id = str_replace('likers-',null,$data);
if($id != $from_id){
$Get = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$namer = $Get["name"];
if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM `like` WHERE `who` = '$id' AND `id` = '$from_id' LIMIT 1")) <= 0){
$set = $Get["like"] + 1 ;
$connect->query("UPDATE user SET `like` = '$set' WHERE id = '$id' LIMIT 1");
$connect->query("INSERT INTO `like` (`id` , `who`) VALUES ('$from_id' , '$id')");
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❤️ شما $namer را لایک کردید",
'show_alert' =>true
]);
}else{
$set = $Get["like"] - 1;
$connect->query("UPDATE user SET `like` = '$set' WHERE id = '$id' LIMIT 1");
$connect->query("DELETE FROM `like` WHERE `who` = '$id' AND `id` = '$from_id'");
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"🖤 شما لایک خود را از $namer پس گرفتید",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما خودتان را نمیتوانید لایک کنید !",
'show_alert' =>true
]);
}
}
if(strpos($data,'follow-') !== false){
$id = str_replace('follow-',null,$data);
if($id != $from_id){
$Get = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$namer = $Get["name"];
if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM `follower` WHERE `who` = '$id' AND `id` = '$from_id' LIMIT 1")) <= 0){
$set = $Get["follower"] + 1;
$connect->query("UPDATE user SET `follower` = '$set' WHERE id = '$id' LIMIT 1");
$connect->query("INSERT INTO `follower` (`id` , `who`) VALUES ('$from_id' , '$id')");
$connect->query("INSERT INTO `following` (`id` , `who`) VALUES ('$id' , '$from_id')");
$add = $datas["following"] + 1;
$connect->query("UPDATE user SET `following` = '$add' WHERE id = '$from_id' LIMIT 1");
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"💛 شما $namer را هم اکنون دنبال میکنید",
'show_alert' =>true
]);
}else{
$set = $Get["follower"] - 1;
$connect->query("UPDATE user SET `follower` = '$set' WHERE id = '$id' LIMIT 1");
$connect->query("DELETE FROM `follower` WHERE `who` = '$id' AND `id` = '$from_id'");
$connect->query("DELETE FROM `following` WHERE `who` = '$from_id' AND `id` = '$id'");
$add = $datas["following"] - 1;
$connect->query("UPDATE user SET `following` = '$add' WHERE id = '$from_id' LIMIT 1");
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"🤍 شما دیگر $namer را دنبال نمیکنید",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما خودتان را نمیتوانید دنبال کنید !",
'show_alert' =>true
]);
}
}
if(strpos($data,"mey-") !== false){
$str = str_replace("mey-","",$data);
$explode = explode("-",$str);
$types = $explode[0];
$msgid = $explode[1];
$list = GetLike($from_id,$types,$msgid);
$negative = $msgid - 10 ;
$plus = $msgid + 10 ;
if($list != null){
switch ($types) {
case 'like':
$txt = "لایک کنندگان";
break;
case 'follower':
$txt = "دنبال کنندگان";
break;
case 'following':
$txt = "دنبال شوندگان";
break;
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔖 لیست $txt :

$list
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "لیست قبلی ⬅️", 'callback_data' => "mey-$types-$negative"],['text' => "➡️ لیست بعدی", 'callback_data' => "mey-$types-$plus"]],
],
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ لیست بعدی یا قبلی وجود ندارد",
'show_alert' =>true
]);
}
}
//============================================================================//
if($data == "myjob" and $step == "ingame"){
$zendan = json_decode(file_get_contents("play/$server/select.json"),true)["bazporcc"]["zendan"] ?? null;
$myjob = json_decode(file_get_contents("play/$server/info.json"),true)["$from_id"]["job"];
if($from_id == $zendan){
if($coin >= "1"){
$set = $datas["coin"] - 1;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 1 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
$bazpors_id = json_decode(file_get_contents("play/$server/jobs.json"),true)["bazporsid"] ?? null;
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"‏📯 $name از جادوی اعلام نقش استفاده کرد

🔖 نقش : $myjob",
'parse_mode'=>"MarkDown",
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}else{
if($coin >= "3"){
$set = $datas["coin"] - 3;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 3 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏📯 $name از جادوی اعلام نقش استفاده کرد

🔖 نقش : $myjob",
'parse_mode'=>"MarkDown",
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}
}
if($data == "tizbeen" and $step == "ingame"){
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($live == "LIVE" and $from_id != $k and $k != null){
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$k' LIMIT 1"));
$yournamer = $datas["name"];
$key[] = [['text'=>"$yournamer",'callback_data'=>"tizbeen-$k-$server"]];
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
برای دیدن نقش بازیکن مورد نظر روی اسمش توی لیست پایین بزن 👇
",
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
}
if(strpos($data,'tizbeen-') !== false and $step == "ingame"){
$str = str_replace("tizbeen-","",$data);
$explode = explode("-",$str);
$id = $explode[0];
$svr = $explode[1];
if($server == $svr){
if($coin >= "2"){
$set = $datas["coin"] - 2;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
$givejob = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["job"];
$namegive = json_decode(file_get_contents("data/$id/$id.json"),true)["name"];
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
📯 جادوی تیزبین

🔖 نقش $namegive : $givejob
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 2 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}
}
if($data == "antihack" and $step == "ingame"){
$hacker = file_get_contents("play/$server/hack.txt");
if($hacker == $from_id){
if($coin >= "3"){
$set = $datas["coin"] - 3;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
unlink("play/$server/hack.txt");
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ هم اکنون میتوانید صحبت کنید و هک شما رفع شده است
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 3 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما هک نشدید و میتوانید صحبت کنید",
'show_alert' =>true
]);
}
}
if($data == "imnot" and $step == "ingame"){
$arraylist = file_get_contents("play/$server/imnot.txt");
$notray = explode("\n",$arraylist);
if(!in_array($from_id,$notray)){
if($coin >= "4"){
$set = $datas["coin"] - 4;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
$NotWrite = fopen("play/$server/imnot.txt", "a") or die("Unable to open file!");
fwrite($NotWrite, "$from_id\n");
fclose($NotWrite);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ شما دیگر امروز در لیست رای گیری قرار نمیگیرد
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 4 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما قبلا این جادو را فعال کردید",
'show_alert' =>true
]);
}
}
if($data == "roytan" and $step == "ingame"){
$arraylist = file_get_contents("play/$server/roytan.txt");
$Royray = explode("\n",$arraylist);
if(!in_array($from_id,$Royray)){
if($coin >= "5"){
$set = $datas["coin"] - 5;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
$RoyWrite = fopen("play/$server/roytan.txt", "a") or die("Unable to open file!");
fwrite($RoyWrite, "$from_id\n");
fclose($RoyWrite);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت جادوی رویین تن فعال شد برای شما
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 5 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما قبلا این جادو را فعال کردید",
'show_alert' =>true
]);
}
}
if($data == "khabar" and $step == "ingame"){
$arraylist = file_get_contents("play/$server/khabar.txt");
$Khabarray = explode("\n",$arraylist);
if(!in_array($from_id,$Khabarray)){
if($coin >= "3"){
$set = $datas["coin"] - 3;
$connect->query("UPDATE user SET coin = '$set' WHERE id = '$from_id' LIMIT 1");
$KhabarWrite = fopen("play/$server/khabar.txt", "a") or die("Unable to open file!");
fwrite($KhabarWrite, "$from_id\n");
fclose($KhabarWrite);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ با موفقیت جادوی خبر چین فعال شد برای شما
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "➖ تعداد 3 سکه از شما کم شد !", 'callback_data'=>"fake"]]
]
])
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ سکه های شما برای این جادو کافی نیست !",
'show_alert' =>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"❌ شما قبلا این جادو را فعال کردید",
'show_alert' =>true
]);
}
}
//============================================================================//
if(strpos($data,"bazpors") !== false){
$str = str_replace("bazpors-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 25){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["bazporcc"]["zendan"] = "$id";
$serverselect["bazporcc"]["zendaname"] = "$namer";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"bazporcc") !== false){
$str = str_replace("bazporcc-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$click = $explode[2];
$tayed = $timer - $timers;
if($tayed < 40){
if($click != "click"){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["bazporcc"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$namer ✔️", 'callback_data' => "bazporcc-$timer-$id-click"]]
]
])
]);
}else{
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
unset($serverselect["bazporcc"]["select"]);
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$namer", 'callback_data' => "bazporcc-$timer-$id"]]
]
])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"asnayper") !== false){
$str = str_replace("asnayper-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 40){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["getasna"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"kar") !== false){
$str = str_replace("kar-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 40){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["getkaragah"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"ghatel") !== false){
$str = str_replace("ghatel-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 40){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["independent"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"doctor") !== false){
$str = str_replace("doctor-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 40){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["getdoki"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"mafia") !== false){
$str = str_replace("mafia-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 40){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$serverselect["getmafia"]["select"] = "$id";
$outjson = json_encode($serverselect,true);
file_put_contents("play/$server/select.json",$outjson);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ شما کاربر ( $namer ) را انتخاب کردید !",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت انتخاب تموم شده است !",
'show_alert' =>true
]);
}
}
if(strpos($data,"RayGiri") !== false){
$str = str_replace("RayGiri-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 45){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$RAY = json_decode(file_get_contents("play/$server/ray.json"),true);
if(!in_array($from_id,$RAY["$id"]["ray"])){
$RAY["$id"]["ray"][] = "$from_id";
$outjson = json_encode($RAY,true);
file_put_contents("play/$server/ray.json",$outjson);
}
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✅ شما رای به $namer دادید !", 'callback_data'=>"fake"]]
]
])
]);
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ با موفقیت شما به ( $namer  ) رای دادید !",
'show_alert' =>true
]);
exit();
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت رای گیری به اتمام رسیده است",
'show_alert' =>true
]);
}
}
if(strpos($data,"bigonah") !== false){
$str = str_replace("bigonah-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 15){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$court = json_decode(file_get_contents("play/$server/court.json"),true);
if(!in_array($from_id,$court["$id"]["innocent"])){
$search = array_search($from_id, $court["$id"]['guilty']);
unset($court["$id"]['guilty'][$search]);
$court["$id"]["innocent"][] = "$from_id";
$outjson = json_encode($court,true);
file_put_contents("play/$server/court.json",$outjson);
}
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ با موفقیت شما به ( $namer  ) رای بی گناهی دادید",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت رای گیری به اتمام رسیده است",
'show_alert' =>true
]);
}
}
if(strpos($data,"motaham") !== false){
$str = str_replace("motaham-","",$data);
$explode = explode("-",$str);
$timers = $explode[0];
$id = $explode[1];
$tayed = $timer - $timers;
if($tayed < 15){
$namer = json_decode(file_get_contents("play/$server/info.json"),true)["$id"]["nameP"];
$court = json_decode(file_get_contents("play/$server/court.json"),true);
if(!in_array($from_id,$court["$id"]["guilty"])){
$search = array_search($from_id, $court["$id"]['innocent']);
unset($court["$id"]['innocent'][$search]);
$court["$id"]["guilty"][] = "$from_id";
$outjson = json_encode($court,true);
file_put_contents("play/$server/court.json",$outjson);
}
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' =>"✅ با موفقیت شما به ( $namer  ) رای گناهکاری دادید",
'show_alert' =>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id' =>$user_id,
'text' => "⚠️ وقت رای گیری به اتمام رسیده است",
'show_alert' =>true
]);
}
}
//============================================================================//
if(preg_match('/^\/([Ss][Tt][Aa][Rr][Tt][ ])(.*)/',$text,$INV) and $tc == 'private'){
$ids = str_replace([" ","\n"],"",$INV[2]);
if($datas['id'] == false){
$connect->query("INSERT INTO `user` (`id` , `name` , `coin` , `warn` , `tas` , `score` , `server` , `follower` , `following` , `like` , `photo` , `vip` , `data` , `game` , `win` , `step`) VALUES ('$from_id','null','0','0','0','0','null','0','0','0','https://t.me/Mafia_nit/152','no','null','0','0','none')");
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"سلام به ربات بازی مافیا خوش آمدید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
exit();
}
if($text == "/start" and $tc == 'private'){
if($step != "ingame"){
if($datas['id'] == false){
$connect->query("INSERT INTO `user` (`id` , `name` , `coin` , `warn` , `tas` , `score` , `server` , `follower` , `following` , `like` , `photo` , `vip` , `data` , `game` , `win` , `step`) VALUES ('$from_id','null','0','0','0','0','null','0','0','0','https://t.me/Mafia_nit/152','no','null','0','0','none')");
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"سلام به ربات بازی مافیا خوش آمدید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
exit();
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"الان توی یه بازی هستی، باید صبر کنی تموم بشه...",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyPlay
]);
exit();
}
}
if($datas['id'] == false){
$connect->query("INSERT INTO `user` (`id` , `name` , `coin` , `warn` , `tas` , `score` , `server` , `follower` , `following` , `like` , `photo` , `vip` , `data` , `game` , `win` , `step`) VALUES ('$from_id','null','0','0','0','0','null','0','0','0','https://t.me/Mafia_nit/152','no','null','0','0','none')");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ حساب شما در ربات $bot_first_name فعال شد !",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
exit();
}
if(strpos($text,'/user_') !== false and $tc == 'private'){
$id = Base(str_replace('/user_',null,$text));
$Get = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$like_user = $Get["like"];
$follower_user = $Get["follower"];
$following_user = $Get["following"];
$win_user = $Get["win"];
$game_user = $Get["game"];
$score_user = $Get["score"];
$name_user = $Get["name"];
$photo_user = $Get["photo"];
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$photo_user,
'caption'=>"
••● نام کاربر : $name_user
••● امتیاز کاربر : $score_user
••● تعداد بازی های کاربر : $game_user
••● تعداد برد های کاربر : $win_user
••● تعداد دنبال شونده ها : $following_user
••● تعداد دنبال کننده ها : $follower_user
••● تعداد لایک کاربر : $like_user
••● یوزر ای دی کاربر : $text
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"❤️ لایک کردن",'callback_data'=>"likers-$id"],['text'=>"🚫 گزارش",'callback_data'=>"ghozaresh-$id"]],
[['text'=>"📨 ارسال پیام خصوصی",'callback_data'=>"sendmsg-$id"],['text'=>"🔐 دنبال کردن",'callback_data'=>"follow-$id"]],
]
])
]);
}
if($text == "🔙 بازگشت" and $tc == 'private' or $text == "/back" and $tc == 'private'){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"سلام به ربات بازی مافیا خوش آمدید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
if($text == "💰 سکه" and $tc == 'private' or $text == "/coins" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💰 سکه فعلی شما : $coin

❓ با سکه چیکار میشه کرد؟

1️⃣ میتونید لیگ اختصاصی 💎 با آیکون دلخواه خودتون بسازید

2️⃣ میتونید از جادو 🔮 های مختلف توی بازی استفاده کنید

3️⃣ ارسال پیام خصوصی 💭 بدون محدودیت کلمات انگلیسی

✅ گروه پشتیبانی اختصاصی

برای خرید سکه روی بسته موردنظر بزنید 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'100 سکه 9,000 تومان','callback_data'=>'plan1']],
[['text'=>'250 سکه 21,000 تومان','callback_data'=>'plan2']],
[['text'=>'500 سکه 40,000 تومان','callback_data'=>'plan3']],
[['text'=>'1000 سکه 75,000 تومان','callback_data'=>'plan4']],
[['text'=>'5000 سکه 300,000 تومان','callback_data'=>'plan5']],
]
])
]); 
}
if($text == "📚 راهنما" and $tc == 'private' or $text == "/help" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📚 آموزش بازی لیگ مافیا

برای مشاهده توضیحات هر بخش روی لینک آن بزنید 👇

🔸 تابحال مافیا بازی نکردم ! چطوریه ؟ 🌹 /help_howtoplay

🔹 لیست همه نقش های بازی 🌹 /help_roles

🔸 مقررات بازی آنلاین 🌹 /help_terms

🔸 چطوری با دوستم بازی کنیم ؟ 🌹 /help_friendly

🔸 امتیاز چیه ؟ چطوری میشه وارد لیگ شد ؟ 🌹 /help_scores

🔸 سکه چیه و چیکار میکنه ؟ 🌹 /help_coins

🔸 جادو چیه ؟ | لیست جادوها | توضیحات 🌹 /help_magics

🔸 میخوام اکانت تلگرامم حذف کنم ! چطوری امتیازمو منتقل کنم ؟ 🌹 /help_account

🔹 میانبر ها کدهای مخفی 🌹 /help_shortcut

🚫 قوانین ربات 🌹 /help_rules

پشتیبانی ربات :
t.me/AupportRoBot

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_howtoplay" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔸 بازی مافیا چطوریه ؟

1️⃣ در اول بازی به هرکس یک نقش داده می شود و هر نقش قابلیت مخصوص خودش را دارد
درمورد نقش ها از اینجا بیشتر بخوانید 👈 /help_roles

2️⃣ دو تیم اصلی یعنی شهروندان و مافیاباهم بازی می کنند
هرتیم نقش های مخصوص خودش رو داره و برخلاف تیم شهرونداعضای مافیا همدیگه رو میشناسن
چند شخصیت مستقل هم وجود داره که هرکدوم شرایط برد خودش رو داره

3️⃣ درفاز روز همه بازیکن ها باهم صحبت می کنن و سعی میکنن با سرنخ هایی که پیدا کردن اعضای مافیا رو شناسایی و اعدام کنن

4️⃣ درفاز شب اعضای مافیا یکی از اعضای شهر رو به قتل می رسانند و باقی افراد نیز اهدافشون رو مشخص می کنن

5️⃣ بازی تا برد یک تیم که به معنی کشتن تمامی اعضای تیم حریف هست ادامه پیدا میکنه

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_roles" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 لیست همه نقش های بازی

گروه مافیا 🔴

پدرخوانده 🚬
رهبر مافیا! مافیا را مدیریت کنید تا برنده شوید هر شب کسی را انتخاب کنید تا نوچه شما به او حمله کند در صورت مردن نوچه مافیا, خود به هدف حمله میکنید

نوچه مافیا 🕶
هر شب به یکی از افراد شهروند حمله کنید! اگر پدرخوانده هدف تعیین کندشما به هدف او حمله میکنید

مفتش 🔎
هر شب نقش دقیق یکی را پیدا کنید و به مافیا کمک کنید تا نقش های مهم را از بازی حذف کند

مذاکره کننده 🤝
درصورت مردن یکی از یارها بجز پدرخواندهمافیا می تواند بجای شلیک کردن مذاکره کند
کسی که با او مذاکره می شود به نوچه مافیا تبدیل می شود مافیا فقط یکبار می تواند مذاکره کند!
مذاکره با بازپرسدکتربادیگاردمحقق و کاراگاه موفق نخواهد شد

هکر 👨🏻‍💻
هرشب تلاش می کنید تا یک بازیکن را هک کنید شانس موفقیت شما 50 درصد است
درصورت موفق بودن هکهدف شما در روز بعد قادر به صحبت و رای دادن نخواهد بود

شیاد ☠️
هر شب یکی را انتخاب کنید تا در صورت مردن خودتاننقش او به بقیه نشان داده شود نه نقش مافیای شما

خفتگير 🚷
هر شب یکی از اعضای شهر را از ایفای نقشش باز دارید
دکتربادیگارد و پلیس قابل خفت کردن نیستن


گروه شهروند 🟢

خائن 😈
عضو شهر هست ولی در جریان بازی به نفع مافیا عمل می‌کند شهروند خائن باید در فاز روزاعضاء مافیا را شناسایی کند و به آن‌ها در برد کمک کند

شهروند ساده
با همکاری دیگر اعضای گروه شهروندمافیا ها را پیدا و با رای دادن اعدام کنید

دکتر 💉
هر شب جان یکی را از حمله افراد نجات دهید در طول بازی یک شب میتوانید خود را نجات دهید

کاراگاه 🕵️‍♂️
هر شب مشکوکیت یکی را بررسی کنید با یکبار بررسی نمیتوانید پدرخوانده را پیدا کنید

بازپرس 🔗
در طول روز یکی را انتخاب کنید تا شب بعد از او بازپرسی کنید در شب تصمیم بگیرید که او را اعدام یا آزاد کنید در صورت کشتن اعضای شهروند دیگر نمیتوانید کسی را بکشید

اسنايپر 🔫
عدالت را به دستان خود بگیرید و شب ها مافیاها را بکشید در صورت کشتن اشتباه اعضای شهروند از احساس گناه دست به خودکشی میزنید!

ساحره 🧙‍♀️
شما می توانید با مرده ها حرف بزنید و از عالم غیب برای شهر خبر بیاورید

زره پوش 🛡
شما دوجان دارید با یکبار حمله کشته نمی شوید مگر اینکه اعدام شوید

بادیگارد 💂‍♂️
هر شب از یکی محافظت کنید, اگر کسی به هدف شما حمله کرد به او حمله میکنید و جان خود را نیز از دست میدهید در طول بازی میتوانید یکبار از خود محافظت کنید

محقق 🔍
هر شب درباره کسی تحقیق کنید و نقش او را حدس بزنید

پلیس 👮🏻‍♂️
اگر در شب هوشیار بمانید به هر کسی که به شما نزدیک شد شلیک میکنید! شما میتوانید 2 شب هوشیار بمانید

دیدبان 👀
هر شب خانه یکی را بپایید تا ببینید چه کسانی به آنجا رفته اند

ردگیر 👣️
هرشب یکی را دنبال کنید و ببینید چه کسی را هدف قرار داده

کشیش 🧔
کشیش میتونه جلوی جرم و جنایتو برای یک شب بگیره اون میتونه برای همه دعا کنه و اونشب هیچ کس قادر به استفاده از تواناییاش نیست ! حیف که فقط یکباره

خبرنگار 📸
اگر مافیا مذاکره کرده باشد در استعلام مشخص می شوداگر مذاکره کننده در بازی نباشد نقش ردگیر را خواهد داشت

فرشته نجات 🧖🏻‍♀️
شما می توانید در طول بازی یک نفر را زنده کنید تا بعنوان شهروند ساده به بازی برگردد فقط می توانید شهروند هارا زنده کنید

شهردار 🎩
در مرحله رای گیری اگر حس کند شهروندی بی گناه اعدام می شود حق وتو داردفقط یکبار می توانید از این توانایی استفاده کنید


گروه مستقل 🟡

قاتل سریالی 🔪
هر شب یکی را به قتل برسانید تا شاهد نابودی شهر و مافیا باشید!

جوکر 👺
اعدام شوید تا برنده شوید! بعد مرگ انتقام خود را از یکی از کسانی که به شما رای گناهکار دادند بگیرید!

گرگینه 🐺
در شب هایی که ماه کامل استاگر تصمیم به حمله بگیرید هم به هدف خود حمله می کنید هم  به تمامی افرادی که به خانه هدف شما بیایند
درصورتی که در ماه کامل هدفی انتخاب نکنید به هر کسی که به خانه شما بیاید حمله خواهید کرد
- شما دارای دفاع معمولی هستید و توسط کسانی که حمله معمولی دارند (مثل مافیا) کشته نمی شوید

آشپز 👨🏻‍🍳
هر شب یکی را مسموم کنیدهدف شما شب بعد به دلیل مسمومیت خواهد مرد تنها دکتر می تواند مسمومیت شخصی را درمان کند

گورکن ⚰️
شب هایی که ماه کامل نیست گور یک نفر را بکنیدفردای آن روز خواهد مردهیچ چیز مانع شما نیست

آرسونیست 🔥
هر شب یکی را نفتی کنید و با کبریت زدنیکجا تمام افراد نفتی را به آتش بکشید!

بمب گذار 💣
شب ها توی خونه یه نفر بمب بذارید اگر اون شب کاری انجام بدهبمب منفجر میشه اگر توی رای گیری فردا بازیکنی که بمب گذاری شده اعدام بشه بمب منفجر میشه و یه نفر دیگه رو هم میکشه اگر شب کسی بسمت بمب گذار شلیک کنه بمب منفجر میشه و یه نفر دیگه هم کشته میشه


🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_terms" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 مقررات بازی آنلاین

🔸 شروع بازی
- بازی ها به دو دسته 9 نفر ساده و 12 نفره سخت تقسیم می شوند
- کاربران تازه وارد که امتیاز کمتر 250 دارند فقط مجاز به بازی 9 نفره هستند
- کاربران بر اساس لیگ به دو دسته حرفه ای و تازه کار تقسیم می شوند

🔹 انواع بازی

1️⃣ بازی ساده
تعداد بازیکن : 9 نفر
امتیاز برنده : 5 امتیاز
حداکثر بازیکن دوستانه : 3 نفر

🔹 ترکیب بازی 9 نفره (آسان)
🔴 2 مافیا : پدرخوانده و نوچه مافیا
🟢 6 شهروند : بازپرس اسنایپر کاراگاه دکتر ساحره شهروند ساده
🟡 1 مستقل : جوکر یا قاتل سریالی

2️⃣ بازی سخت
تعداد بازیکن : 12 نفر
امتیاز برنده : 7 امتیاز
حداکثر بازیکن دوستانه : 5 نفر

🔹 ترکیب بازی 12 نفره (سخت)
🔴 4 نفر گروه مافیا
پدرخوانده نوچه مافیا مفتش
مذاکره کننده یا شیاد یا هکر یا خفتگیر یا خائن 
🟢 7 نفر گروه شهروند
بازپرس اسنایپر
پلیس یا شهردار
کاراگاه یا محقق
ردگیر یا دیدبان یا خبرنگار
دکتر یا بادیگارد یا فرشته نجات
ساحره یا زره پوش یا کشیش
🟡 1 نفر مستقل
قاتل سریالی یا جوکر یا گرگینه یا آشپز یا گورکن یا آرسونیست

3️⃣ بازی شبکه سلامت
تعداد بازیکن : 10 نفر
سکه برای شروع : 7 سکه
امتیاز برنده 7 امتیاز
حداکثر بازیکن دوستانه : 4 نفر

🔹 ترکیب بازی شبکه سلامت 10 نفره
🔴 پدرخوانده مذاکره کننده مافیا ساده
🟢 کاراگاه دکتر اسنایپر زره پوش خبرنگار شهروند شهروند

4️⃣ بازی کامل
تعداد بازیکن : 23 نفر
سکه برای شروع : 20 سکه
امتیاز برنده : 20 امتیاز
حداکثر بازیکن دوستانه : 13 نفر

🔹 ترکیب بازی کامل 23 نفره
شامل همه نقش های بازی بجز مذاکره کننده خبرنگار شهروند ساده و مافیا ساده

5️⃣ بازی کامل بدون مستقل
تعداد بازیکن : 19 نفر
سکه برای شروع : 10 سکه
امتیاز برنده : 15 امتیاز
حداکثر بازیکن دوستانه : 9 نفر

🔹 ترکیب بازی کامل بدون مستقل
همه نقش های بازی بجز گروه مستقل و نقش هایی که تازه اضافه شده اند

6️⃣ بازی 15 نفره تصادفی
امتیاز برنده : 15
حداکثر دوستانه : 9

🔹 ترکیب : از گروه مافیا 5 نفرشهروند 8 نفر و مستقل 2 نفر بصورت کاملا رندوم ( تصادفی) می باشد

7️⃣ بازی مدرن
تعداد بازیکن : 15 نفر
امتیاز برنده : 12 امتیاز
حداکثر بازیکن دوستانه : 7 نفر

🔹 ترکیب بازی 15 نفره (مدرن)
🔴 5 نفر گروه مافیا
پدرخوانده نوچه مافیا مفتش دکتر لکتر
مذاکره کننده یا شیاد یا هکر یا خفتگیر یا خائن 
🟢 8 نفر گروه شهروند
بازپرس اسنایپر پلیس
شهردار یا فرشته نجات
کاراگاه یا محقق
ردگیر یا دیدبان یا خبرنگار
دکتر یا بادیگارد 
ساحره یا زره پوش یا کشیش
🟡 2 نفر مستقل
بصورت تصادفی

8️⃣ سناریو مافیا کلاسیک ( مدرن بدون مستقل )
تعداد بازیکن : 13 نفر
امتیاز برنده : 7 امتیاز
حداکثر بازیکن دوستانه : 6
دستور : /classic

🔹 ترکیب مشابه بازی مدرن ولی بدون نقش های مستقل

9️⃣ تیم مستقل ها
تعداد بازیکن ها : 11 نفر
امتیاز برنده : 7 امتیاز
حداکثر دوستانه : 5 نفر
دستور /mostaghel

🔹 ترکیب
🟡 تیم مستقل :
قاتل سریالی
جوکر یا بمب گزار
آرسونیست یا گرگینه
آشپز یا گورکن
🟢 تیم شهروند مشابه بازی مدرن یا مدرن بدون مستقل میباشد

🔸 حداکثر بازیکن دوستانه یعنی بیشتر از این تعداد باشه امتیاز نمیگیرن!

🔸 خروج از بازی
- برای خروج از بازی حداقل باید 5 ثانیه از ورودتان گذشته باشد
- وقتی بازی شروع می شوند کسی حق خروج از بازی را ندارد

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_friendly" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 بازی دوستانه یا گروهی

👥 میتونی به دوستات یا توی گروه درخواست بدی و اونا رو به بازی دعوت کنی
- توی منوی اصلی ربات روی دکمه (👥 دوستانه) بزنید
- توی پیوی دوست یا توی گروهی که میخواین ایدی ربات رو تایپ کنید
یه باکس باز میشه نوع بازی رو انتخاب کنید و بازی جدید برای شما آغاز میشه!

🔹 دو نوع بازی در این ربات وجود دارد
- بازی سخت با ظرفیت 12 نفره
- بازی آسان با ظرفیت 9 نفره

🔹 اگر کمتر از این تعداد هستیدمیتونید روی دکمه ( شروع بازی با همین تعداد ) بزنید و از بازیکن های آنلاین کمک بگیرید!

🗣 چطوری بصورت صوتی با دوستامون چت کنیم؟!
- میتونید به دوستتون تماس صوتی بگیرید
- توی آپدیت جدید تلگرام امکان ویس چت به گروه ها اضافه شده است

❗️ درصورتی که از دکمه  ( شروع بازی با همین تعداد ) استفاده کردیدبازی شما دیگر دوستانه نیست و باید طبق قوانین بازی آنلاین رفتار کنید 👈 /help_rules
❗️ امتیاز در بازی دوستانه منوط به شرط زیر هست :
- در بازی 12 نفره کمتر از 6 نفر دوستان شما باشند
- در بازی 9 نفره کمتر از 3 نفر دوستان شما باشند

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_scores" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 امتیاز چیست ؟

امتیاز یک واحد رتبه بندی بازیکن ها در جدول رتبه بندی هاست هرچه امتیاز بیشتری بگیرید وارد لیگ جدید تری می شوید که در ادامه توضیح میدیم

🔹 هر بازی چند امتیاز داره و چطوری میشه امتیاز گرفت؟

برای گرفتن امتیاز باید در بازی برنده شوید که در بازی مافیا یعنی نابودی کامل تیم حریف  ولی استثنا هایی هم وجود دارد

در بازی 9 نفره ( آسان ) اگر تیم شما برنده شد و شما زنده باشید 5 امتیاز میگیریدو کسانی که در بازی کشته شده اند امتیازی نمیگیرند

اما در بازی 12 نفره ( سخت ) در تیم برنده هرکس زنده مانده باشد 7 امتیاز و کسانی که مرده اند 2 امتیاز میگیرند

دو نقش هستند که حتی اگر مرده باشند ممکن است امتیاز بگیرند :
- جوکر در صورت اعدام شدن امتیاز کامل میگیرد ( به استثنا مساوی شدن بازی )
- بادیگارد درصورتی که از اعضای شهر دفاع کند و کشته شود و تیمش (شهروند) برنده شود امتیاز کامل را میگیرد

همه امتیاز ها درصورتی برای بازیکن منظور می شود که تا آخر بازی ( اعلام نتیجه ) از بازی خارج نشده باشد

🔹 چطوری میتونم وارد لیگ بشم و لیگ چیه ؟

لیگ شما به ازای امتیازی که دارید بالا می رود و هرلیگ یک علامت انحصاری دارد که در کنار اسم شما در بازی نمایش داده می شود
برای دیدن همه لیگ ها و امتیاز مورد نیاز برای صعود به هر لیگ روی لینک روبرو بزنید https ://tme/mafiabot_official/11

🔹 آیا بازی دوستانه امتیاز دارد؟
بله امتیاز دارد اما محدودیت وجود دارد برای مثال بازی 9 نفره تا 3 نفر و بازی 12 نفره تا 6 نفر از بازیکن ها میتوانند از دوستان شما باشند درغیراینصورت امتیاز حساب نمی شود

🔹 سایر روش های گرفتن امتیاز :

1️⃣  گزارش افراد متخلف در بازی : اگر گزارش شما تایید شود 3 امتیاز دریافت می کنید
2️⃣  تاس انداختن : این فرصت به کسانی داده می شود که کشته شده باشند و تا پایان بازی بمانند در فرصت کل کل آخر بازی تاس بیندازید و جایزه دریافت کنید !

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_coins" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 سکه چیه و چیکار میکنه ؟

بعضی از امکانات ارائه شده در بازی رایگان نیستیدازین رو سکه یک واحد خرید درون بازی محسوب می شود

🔹 با سکه چیکار میشه کرد ؟

1️⃣ می تونید نقشتون 🎭 رو خودتون انتخاب کنید
2️⃣ میتونید از جادو 🔮 های مختلف توی بازی استفاده کنید
❔ جادو چیه ؟ 👈 /help_magics
3️⃣ ارسال پیام خصوصی 💭 بدون محدودیت کلمات انگلیسی
4️⃣ میتونید لیگ اختصاصی 💎 با آیکون دلخواه خودتون بسازید

🔹 روش های مختلف دریافت سکه

1️⃣  پرداخت آنلاین با کارت بانکی (شتاب)
2️⃣  تاس انداختن در بازی

❓ چرا باید برای خرید سکه پول پرداخت کرد؟
درآمد های حاصل از خرید سکه صرف هزینه های سرور ربات و تحقیق و توسعه برای پیشرفت و سایر هزینه های جانبی ربات می شود

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_magics" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 جادو چیه ؟

جادو ها به شما کمک می کنند در بازی بهتر باشید هر جادو توانایی خاصی به شما میدهند

🔻 لیست جادو های بازی

جادوی اعلام نقش 👈 پیش بازپرس 1 سکه موقع اعدام 3 سکه
نقش خود را به بازپرس قطعی اعلام کنید تا او مطمئن شود

جادوی تیزبین 👈 2 سکه
نقش قطعی یک نفر را پیدا کنید

جادوی خبرچین 👈 3 سکه
اگر کسی قصد حمله به شما را دارد به شما خبر می دهد

جادوی من نیستم 👈 4 سکه
برای یک روز از لیست اعدام خارج شوید

جادوی رویین تن 👈 5 سکه
یک شب خود را در مقابل حملات مقاوم کنید

جادوی رفع هک 👈 3 سکه
اگرهک شدید از این جادو استفاده کنید

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_account" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔹 چطوری میتونم امتیازمو به اکانت جدید منتقل کنم؟

<b>🔹 مرحله اول :</b>
• به بخش ( 👤 پروفایل ) بروید و روی دکمه انتقال به اکانت جدید بزنید !
• سپس روی ( توکن اکانت من ) بزنید و توکن خود را کپی کنید !

<b>🔹 مرحله دوم :</b>
• با اکانت جدید خود یا اکانت دیگر وارد ربات شوید و به بخش ( 👤 پروفایل ) بروید
• سپس روی ( بازیابی اکانت حذف شده ) بزنید

<b>🔹 مرحله سوم :</b>
• توکن اکانت قبلی خود که در مرحله اول از ربات گرفتید وارد کنید !
• و در اخر تمام امتیازات و سکه های اکانت شما بازیابی میشوند !

🔸 اخطار :

<b>توکن اکانت خود را به هیچ وجه دست هیچ کسی ندهید !</b>


🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_shortcut" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔸 میانبر ها و کد های مخفی چی هستن ؟

کدهای دستوری هستن که درحالت عادی کسی از اون ها خبر نداره ولی می تونه سرعت دسترسی شما رو به بخش های مختلف بازی بیشتر کنه!
خیلی مهم هستن خیلی!

1️⃣ شروع بر اساس نوع بازی

بازی ساده 👈 /simple
بازی سخت 👈 /hard
بازی شبکه سلامت 👈 /salamat
بازی کامل 👈 /full
بازی کامل بدون مستقل 👈 /fullnm
بازی تصادفی 👈 /tesadofi
بازی مدرن 👈 /modern
بازی کلاسیک 👈 /classic
تیم مستقل 👈 /mostaghel

2️⃣ ارسال پیام خصوصی گزارش تخلف و هدیه دادن سکه به دوستان

پیام خصوصی و گزارش تخلف 👈 https ://tme/mafiabot_official/340
پست هدیه دادن سکه به دوست 👈 https ://tme/mafiabot_official/374
لینک پست آموزش ویدئویی 👈 https ://tme/mafiabot_official/432

3️⃣ پیام دادن ساحره (نقش) به مرده ها حین بازی

وقتی توی بازی ساحره هستید میتونید با گذاشتن یک نقطه  قبل از پیامتون اونو فقط برای مرده ها بفرستید و بقیه نمی بینن

🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help_rules" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚖️ قوانین چت در بازی مافیا

🚫 1 ) تبلیغ و ارسال آیدی یا شماره تلفن و دعوت به سایر گروه ها اکیداً ممنوع می باشد

🚫 2 ) به هیچ وجه بازی رو با ربات های دوست یابی اشتباه نگیرید و چت خارج از بازی نکنید

🚫 3 ) ارسال فحش و بدو بیرا توهین به سایر بازیکنان و توهین به مقدسات توی این ربات ممنوع

🚫 4 ) ارسال پیام متوالی و نامتعارف و هرگونه کاری که باعث برهم زدن نظم بازی بشود (لو دادن نقش ادعا خواستن) ممنوع

🚫 5 ) هرگونه بی ادبی تهدید توهین و ترغیب افراد توسط کانال های شخص ثالث و غیررسمی پیگیری می شود و مسببان مسدود خواهند شد

⚠️ <b>اخطار</b> : درصورت مشاهده هرگونه تخلف و تقلب در کسب امتیاز مانند لو دادن نقش و در بازی گروهی این ربات مختار است امتیاز شما را بطور کامل حذف کند !

🔸 لطفا کاربران خاطی را گزارش کنید


🔸 - ‏ راهنما : /help

𝑇𝐼𝑀𝐸 : ( $time - $date )
",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "🕹 شروع بازی آنلاین" and $tc == 'private'){
if($name == "null"){
$connect->query("UPDATE user SET step = 'editnames' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
یه نام مستعار برای خودت وارد کن
حتما باید فارسی باشه

مثال : علی , محمد و . . .
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر نام", 'callback_data' => "cancel"]],
],
])
]);
exit();
}
if($step == "ingame"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"الان توی یه بازی هستی، باید صبر کنی تموم بشه...",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyPlay
]);
}else{
$server = Random("6");
$randoms = glob('play/*');
foreach($randoms as $servername){
if(!file_exists("$servername/lock.txt")){
$server = explode("/","$servername")[1];
break;
}
}
mkdir("play");
mkdir("play/$server");
$px = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$px);
if(!in_array($from_id,$exp)){
file_put_contents("play/$server/chati.txt","ON");
//============================================================================//
$gametype = file_get_contents("play/$server/typegame.txt") ?: '9';
$nex = PlayerNumber($from_id,$name,$gametype,$server);
//============================================================================//
$connect->query("UPDATE user SET step = 'ingame' , server = '$server' WHERE id = '$from_id' LIMIT 1");
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏$nex . ‏*$name* به این بازی پیوست",
'parse_mode'=>"MarkDown",
]);
}
$write = fopen("play/$server/newgame.txt", "a") or die("Unable to open file!");
fwrite($write, "$from_id\n");
fclose($write);
if(file_get_contents("play/$server/lock.txt") == $gametype){
StartGame("https://SnowyTeam.xyz/mafia","$gametype",['server'=>"$server"]);
}
}
}
}
if(strpos($info,'pm-') !== false){
if($step == "dead" or $step == "ingame"){
$id = str_replace('pm-',null,$info);
if($text == true){
$json = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$id' LIMIT 1"));
$reporter = $json["name"];
$set = $datas["coin"] - 2;
$connect->query("UPDATE user SET data = 'none' , coin = '$set' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
💭 یک پیام خصوصی از طرف ( $name )

💬 پیام این شخص : *$text*
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت به کاربر ( $reporter ) ارسال شد ! 

❕مقدار 2 سکه از حساب شما کسر شد
",
'parse_mode'=>'MarkDown',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً پیام خود را بصورت متن ارسال کنید
",
'parse_mode'=>'MarkDown',
]);
}
exit();
}else{
$connect->query("UPDATE user SET data = 'none' WHERE id = '$from_id' LIMIT 1");
}
}
if(isset($update->message->dice->value) and !file_exists("play/$server/lock.txt") and $tc == 'private'){
if($step == "dead"){
$listgift = file_get_contents("play/$server/gift.txt");
$arraygift = explode("\n",$listgift);
if(!in_array($from_id,$arraygift)){
$value = $update->message->dice->value;
$GiftWrite = fopen("play/$server/gift.txt", "a") or die("Unable to open file!");
fwrite($GiftWrite, "$from_id\n");
fclose($GiftWrite);
if($value == 1){
$set = $datas["score"] + 3;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 3 امتیاز گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
if($value == 2){
$set = $datas["score"] + 5;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 5 امتیاز گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
if($value == 3){
$set = $datas["score"] + 7;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 7 امتیاز گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
if($value == 4){
$set = $datas["score"] + 3;
$sett = $datas["coin"] + 1;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' , coin = '$sett' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 3 امتیاز + 💰 1 سکه گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
if($value == 5){
$set = $datas["score"] + 6;
$sett = $datas["coin"] + 1;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' , coin = '$sett' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 6 امتیاز + 💰 1 سکه گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
if($value == 6){
$set = $datas["score"] + 7;
$sett = $datas["coin"] + 2;
$settt = $datas["tas"] + 1;
$connect->query("UPDATE user SET score = '$set' , tas = '$settt' , coin = '$sett' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 شما 7 امتیاز + 💰 2 سکه گرفتید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شما قبلا جایزه خود را دریافت کرده‌اید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
}else{
if($step == "ingame"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شما نمیتوانید جایزه دریافت کنید !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
]);
}
}
exit();
}
if($step == "dead" and $text != "دیدن نقش ها" and $text != "خروج از بازی" and $text != "💭 پیام خصوصی" and $text != "🚫 گزارش تخلف" and $text != "🔮 جادو ها" and $text != "/coins" and $tc == 'private'){
if($text == false){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام خود را بصورت متن ارسال کنید",
'parse_mode'=>"HTML",
]);
exit();
}
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",
'parse_mode'=>"HTML",
]);
exit();
}
if(mb_strlen($text) > 100){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام شما نباید بیشتر از صد حرف باشد",
'parse_mode'=>"HTML",
]);
exit();
}

if(file_exists("play/$server/lock.txt")){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$from_id"]["live"];
if($live == DEAD){
$playersdead = fopen("play/$server/dead.txt", 'r');
while( !feof( $playersdead)){
$usdead = fgets( $playersdead);
bot('sendMessage',[
'chat_id'=>$usdead,
'text'=>"‏$lig *$name* _(مرده)_ : $text",
'parse_mode'=>"MarkDown",
]);
}
}
}else{
$playersdead = fopen("play/$server/newgame.txt", 'r');
while( !feof( $playersdead)){
$usdead = fgets( $playersdead);
bot('sendMessage',[
'chat_id'=>$usdead,
'text'=>"‏$lig *$name* _(مرده)_ : $text",
'parse_mode'=>"MarkDown",
]);
}
}
}
if($text == "🚫 گزارش تخلف" and $tc == 'private'){
if($step == "ingame" or $step == "dead"){
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
if($from_id != $k and $k != null){
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$k' LIMIT 1"));
$yournamer = $datas["name"];
$key[] = [['text'=>"$yournamer",'callback_data'=>"report-$k"]];
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
برای گزارش بازیکن متخلف روی اسمش توی لیست پایین بزن 👇

اگه درست گزارش کنی 🌟 3 امتیاز میگیری

⚠️ اگه الکی گزارش کنی خودت مسدود میشی ! 
",
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
}
}
if($text == "💭 پیام خصوصی" and $tc == 'private'){
if($step == "ingame" or $step == "dead"){
if($coin >= "2"){
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($from_id != $k and $k != null){
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$k' LIMIT 1"));
$yournamer = $datas["name"];
$key[] = [['text'=>"$yournamer",'callback_data'=>"pm-$k"]];
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❓به چه کسی میخواهید پیام خصوصی ارسال کنید 

⚠️ *هزینه هر پیام خصوصی 2 سکه است !*

🔰 *در پیام خصوصی ارسال آیدی و . . . مجاز است !*
",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💭 *پیام خصوصی ( درگوشی )*

این نوع ارسال پیام بدون فیلتر بوده و ارسال آیدی مجاز است
هربار استفاده `2` سکه کسر می شود

⚠️ _شما سکه کافی برای ارسال پیام دایرکت ندارید_

( *دریافت سکه* : /coins )
",
'parse_mode'=>"MarkDown",
]);
}
}
}
if($text == "🔮 جادو ها" and $tc == 'private'){
if($step == "ingame"){
if(file_exists("play/$server/lock.txt")){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📯 جادوی مورد نظر را از لیست زیر انتخاب کنید
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📯 جادوی اعلام نقش",'callback_data'=>"myjob"]],
[['text'=>"📯 جادوی تیزبین",'callback_data'=>"tizbeen"]],
[['text'=>"📯 جادوی رفع هک",'callback_data'=>"antihack"]],
[['text'=>"📯 جادوی من نیستم",'callback_data'=>"imnot"]],
[['text'=>"📯 جادوی رویین تن",'callback_data'=>"roytan"]],
[['text'=>"📯 جادوی خبر چین",'callback_data'=>"khabar"]],
]
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شما فقط در حین بازی میتوانید از جادو استفاده کنید !
",
'parse_mode'=>"HTML",
]);
}
}else{
if($step == "dead"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شما نمیتوانید از جادو استفاده کنید !
",
'parse_mode'=>"HTML",
]);
}
}
}
if($text == "دیدن نقش ها" and !file_exists("play/$server/lock.txt") and $tc == 'private'){
if($step == "ingame" or $step == "dead"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
if($user != null){
$i++;
$listjob = json_decode(file_get_contents("play/$server/info.json"),true)["$user"]["job"];
$yournamer = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$user' LIMIT 1"))["name"];
$listView = $listView."$i. $yournamer | $listjob\n";
}
}
//============================================================================//
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏
📝 نمایش لیست نقش ها :

$listView
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>"HTML",
]);
}
}
if($step == "ingame" and $text != "دیدن نقش ها" and $text != "خروج از بازی" and $text != "💭 پیام خصوصی" and $text != "🚫 گزارش تخلف" and $text != "🔮 جادو ها" and $text != "/coins" and $tc == 'private'){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$god_id = $serverjobs["fatherid"] ?? null;
$noche_id = $serverjobs["nocheid"] ?? null;
$bazpors_id = $serverjobs["bazporsid"] ?? null;
$chatsahere = $serverjobs["sahereid"] ?? null;
if($text == false){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام خود را بصورت متن ارسال کنید",
'parse_mode'=>"HTML",
]);
exit();
}
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",
'parse_mode'=>"HTML",
]);
exit();
}
if(mb_strlen($text) > 100){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام شما نباید بیشتر از صد حرف باشد",
'parse_mode'=>"HTML",
]);
exit();
}
$hacker = file_get_contents("play/$server/hack.txt") ?: null;
if($hacker == $from_id){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ متاسفانه شما هک شدید و امروز نمیتوانید صحبت کنید",
'parse_mode'=>"MarkDown",
]);
}else{
$motaham = file_get_contents("play/$server/motaham.txt");
if($from_id == $motaham){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏$lig *$name* _( متهم )_ : $text",
'parse_mode'=>"MarkDown",
]);
}
}else{
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$from_id"]["live"];
$zendan = json_decode(file_get_contents("play/$server/select.json"),true)["bazporcc"]["zendan"] ?? null;
if($live == "LIVE"){
$chati = file_get_contents("play/$server/chati.txt");
if($chati == true){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$send = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏$lig *$name* : $text",
'parse_mode'=>"MarkDown",
])->description ?? null;
if(strpos($send,"bot was blocked by the user") !== false){
$seting = time() + 3600;
$connect->query("UPDATE user SET warn = '$seting' WHERE id = '$user' LIMIT 1");
}
}
}else{
if($from_id == $chatsahere and $from_id != $zendan){
$playersdead = fopen("play/$server/dead.txt", 'r');
while( !feof( $playersdead)){
$usdead = fgets( $playersdead);
bot('sendMessage',[
'chat_id'=>$usdead,
'text'=>"‏$lig *$name* _(ساحره)_ : $text",
'parse_mode'=>"MarkDown",
]);
}
}else{
if($from_id == $zendan){
bot('sendMessage',[
'chat_id'=>$zendan,
'text'=>"‏$lig *$name* : $text",
'parse_mode'=>"MarkDown",
]);
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"‏$lig *$name* : $text",
'parse_mode'=>"MarkDown",
]);
exit();
}
if($from_id == $bazpors_id){
if($zendan == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ کسی نیست که پیامتو ببینه 😉
",
'parse_mode'=>"MarkDown",
]);
}else{
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
بازپرس : $text
",
'parse_mode'=>"HTML",
]);
bot('sendMessage',[
'chat_id'=>$zendan,
'text'=>"
بازپرس : $text
",
'parse_mode'=>"HTML",
]);
}
exit();
}
if($from_id == $noche_id){
if($god_id == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ کسی نیست که پیامتو ببینه 😉
",
'parse_mode'=>"MarkDown",
]);
}else{
bot('sendMessage',[
'chat_id'=>$god_id,
'text'=>"
$lig *$name* ( نوچه ) : $text
",
'parse_mode'=>"MarkDown",
]);
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$lig *$name* ( نوچه ) : $text
",
'parse_mode'=>"MarkDown",
]);
}
exit();
}
if($from_id == $god_id){
if($noche_id == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ کسی نیست که پیامتو ببینه 😉
",
'parse_mode'=>"MarkDown",
]);
}else{
bot('sendMessage',[
'chat_id'=>$god_id,
'text'=>"
$lig *$name* ( پدرخوانده ) : $text
",
'parse_mode'=>"MarkDown",
]);
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$lig *$name* ( پدرخوانده ) : $text
",
'parse_mode'=>"MarkDown",
]);
}
exit();
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطا : الان نمی تونی چت کنی
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyPlay
]);
exit();
}
}
}
}
}
}
if($text == "خروج از بازی" and $tc == 'private'){
if($step == "ingame" or $step == "dead"){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$from_id"]["live"];
if(!file_exists("play/$server/lock.txt") or $live == "DEAD" or $step == "dead"){
if($live == "DEAD" or $step == "dead"){
$set = $datas["game"] + 1;
$connect->query("UPDATE user SET step = 'none' , game = '$set' , server = 'null' WHERE id = '$from_id' LIMIT 1");
}else{
$connect->query("UPDATE user SET step = 'none' , server = 'null' WHERE id = '$from_id' LIMIT 1");
}
bot('unpinAllChatMessages',[
'chat_id'=>$from_id,
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
شما از بازی خارج شدید

منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
$dcoeockencn = file_get_contents("play/$server/newgame.txt");
$fncndkckek = str_replace("$from_id\n", "", $dcoeockencn);
file_put_contents("play/$server/newgame.txt", $fncndkckek);
$outdead = file_get_contents("play/$server/dead.txt");
$newdeadlist = str_replace("$from_id\n", "", $outdead);
file_put_contents("play/$server/dead.txt", $newdeadlist);
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$num = $serverinfo["$from_id"]["number"];
$serverid = json_decode(file_get_contents("play/$server/id.json"),true);
unset($serverid["$num"]);
$outjson = json_encode($serverid,true);
file_put_contents("play/$server/id.json",$outjson);
unset($serverinfo["$from_id"]);
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏*$name* از بازی خارج شد",
'parse_mode'=>"MarkDown",
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شما در یک بازی هستید و نمی‌توانید خارج شوید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyPlay
]);
}
}
}
if($text == "🌟 امتیازات" and $tc == 'private'){
$info = GetRanks("game");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📈 لیست برترین های بیشترین بازی

📉 بازی های شما : $game

$info
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$bot_username
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "💫 بیشترین فالوور", 'callback_data' => "follower"]],
[['text' => "🏆 بیشترین برد", 'callback_data' => "win"],['text' => "❤️ محبوب ترین ها", 'callback_data' => "like"]],
[['text' => "❄️ بیشترین بازی", 'callback_data' => "game"],['text' => "🌟 بیشترین امتیاز", 'callback_data' => "score"]],
],
])
]);
}
if($text == "👤 پروفایل" and $tc == 'private'){
$id_Account = Base($from_id);
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$LinkPhoto,
'caption'=>"
• نام شما : $name
• سکه : $coin
• امتیاز : $score
• لیگ : $lig
• تعداد بازی ها : $game
• تعداد برد : $win
• تاس برنده شده : $tas
• تعداد لایک ها : $like
• تعداد دنبال کننده ها : $follower
• تعداد دنبال شونده ها : $following
• مهلت حساب ویژه : $vip
• ایدی اکانت شما : /user_$id_Account
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📝 تغییر نام",'callback_data'=>'editname']],
[['text'=>"🌄 تغییر عکس",'callback_data'=>'editphoto']],
[['text'=>" $lig تغییر لیگ ",'callback_data'=>'ligaal']],
[['text'=>"♻️ انتقال به اکانت جدید",'callback_data'=>'newac']],
[['text'=>"📊 آمار برد",'callback_data'=>'statistics']],
[['text'=>"┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄ ",'callback_data'=>"fake"]],
[['text'=>"❤️ لایک کنندگان",'callback_data'=>'mey-like-0']],
[['text'=>"🌟 دنبال شوندگان",'callback_data'=>'mey-following-0'],['text'=>"💫 دنبال کنندگان",'callback_data'=>'mey-follower-0']],
]
])
]);
}
elseif($step == "editphotos" and $data != "cancel" and $tc == 'private'){
$filephoto = $update->message->photo;
$photo = $filephoto[count($filephoto)-1]->file_id;
if(isset($photo)){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE user SET photo = '$photo' WHERE id = '$from_id' LIMIT 1");
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$photo,
'caption'=>"
✅ عکس پروفایل شما با موفقیت تغییر کرد !

منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطا : لطفاً *عکسی معتبر* ارسال کنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر عکس", 'callback_data' => "cancel"]],
],
])
]);
}
}
elseif($step == "editnames" and $data != "cancel" and $tc == 'private'){
if(preg_match('/^[\x{0600}-\x{06FF}\s]+$/u',$text)){
if(mb_strlen($text) < 15 and mb_strlen($text) > 3){
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
$connect->query("UPDATE user SET name = '$text' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
نام مستعار شما به ($text) تغییر یافت ✅

منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطا : نام وارد شده باید حتما بین 3 تا 15 حرف باشه
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر نام", 'callback_data' => "cancel"]],
],
])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطا : نام وارد شده باید حتما حروف فارسی باشد
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "انصراف از تغییر نام", 'callback_data' => "cancel"]],
],
])
]);
}
}
elseif($step == "SendRecovery" and $data != "cancel" and $tc == 'private'){
$check = Check($from_id,$text);
if($check == 'true'){
$tims = $timer + 600;
$log = explode(":","$text")[0];
$connect->query("UPDATE user SET step = 'LogIn-$log' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 تبریک ! توکن ارسال شد صحیح است


✅ جهت وارد شدن به اکانت خود روی دکمه زیر کلیک کنید

⚠️ *توجه* : _تمام اطلاعات این اکانت شما ریست میشود و حساب کاربری توکن ارسال شده جایگزین اطلاعات حساب شما میشوند !_

⏳ تا 10 دقیقه فرصت دارید تصمیم خود را بگیرید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "✅ موافقم", 'callback_data' => "login-$tims"]],
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}else{
if($check == 'false'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ توکن ارسال شده صحیح نمیباشد
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}else{
if($check == 'userid'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️شما هم اکنون داخل توکن این حساب هستید

⚠️ لطفاً با اکانت دیگر خود اینکار را انجام بدهید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "برگشت", 'callback_data' => "cancel"]],
],
])
]);
}
}
}
}
if(in_array($from_id,$admin)){
if($text == "/panel" or $text == "/admin"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🎊 سلام به پنل مدیریت خوش آمدید !",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyAdmin
]);
}
}
if(file_exists("error_log")) unlink("error_log");
?>