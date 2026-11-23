<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
http_response_code(200);
litespeed_finish_request();
define('SLOW_KEY','"PUT-YOUR-BOT-TOKEN"');
include("jdf.php");
$get = json_decode(file_get_contents("https://api.telegram.org/bot".SLOW_KEY."/getMe"));
$usernamebot = $get->result->username;
$namebot = $get->result->first_name;
$botid = $get->result->id;
//===[فانکشن های لازم]===//
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".SLOW_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function Save($files_id,$from_id,$typer,$link,$caption,$names,$file_size,$update,$uptime){
$files = json_decode(file_get_contents("files.json"),true);
$files["$files_id"]['admin'] = "$from_id";
$files["$files_id"]['link'] = "$link";
$files["$files_id"]['caption'] = "$caption";
$files["$files_id"]['type'] = "$typer";
$files["$files_id"]['name'] = "$names";
$files["$files_id"]['size'] = "$file_size";
$files["$files_id"]['time'] = "$uptime";
$files["$files_id"]['date'] = "$update";
$outjson = json_encode($files,true);
file_put_contents("files.json",$outjson);
}
function DeleteFile($files_id,$from_id){
$files = json_decode(file_get_contents("files.json"),true);
if($files["$files_id"]['link'] != null){
if($files["$files_id"]['admin'] == $from_id){
unset($files["$files_id"]);
$outjson = json_encode($files,true);
file_put_contents("files.json",$outjson);
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$search = array_search($files_id, $datas['ListUpload']);
unset($datas['ListUpload'][$search]);
$datas['ListUpload'] = array_values($datas['ListUpload']);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$search = array_search($files_id, $datas['ListDownload']);
unset($datas['ListDownload'][$search]);
$datas['ListDownload'] = array_values($datas['ListDownload']);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
return 'ok';
}else{
return 'noadmin';
}
}else{
return 'nofind';
}
}
function Random($count){
$container = 'QWERTYUIOPLKJHGFDSAZCXVBNMqwertyuioplkjhgfdsazcxvbnm0123456789';
$key = '';
for($i = 0;$i < $count;$i++){
$rand = rand(0,mb_strlen($container)-1);
$key .= substr($container, $rand, 1);
}
return $key;
}
function GetSize($size, $pre = 2){
$units = array('بایت', 'کیلوبایت', 'مگابایت', 'گیگابایت', 'ترابایت');
$base = log($size, 1024);
return round(pow(1024, $base - floor($base)), $pre) . ' ' . $units[$base];
}
function TypeFile($type){
if($type == "document"){
return '📂 فایل';
}
if($type == "video"){
return '🌟 ویدیو';
}
if($type == "photo"){
return '💫 عکس';
}
if($type == "voice"){
return '🍁 ویس';
}
if($type == "audio"){
return '🎧 موسیقی';
}
if($type == "sticker"){
return '❄️ استیکر';
}
if($type == "video_note"){
return '⚡️ ویدیو مسیج';
}
}
function History($from_id,$C,$file,$usernamebot){
for($i = $C;$i < $C +10;$i++){
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$files = json_decode(file_get_contents("files.json"),true);
$ids = $datas["List$file"][$i] ?? 'false';
if($ids == 'false') break;
if($files["$ids"]['link'] != null){
$type_file = $files["$ids"]['type'];
$name_file = TypeFile($type_file);
$password = $files["$ids"]['password'] ?? '🔓 بدون پسورد . . . !';
$view = count($files["$ids"]['view']);
$size = $files["$ids"]['size'];
$time_upload = $files["$ids"]['time'];
$date_upload = $files["$ids"]['date'];
$result = $result."
✅ نوع فایل : $name_file
🎈 حجم فایل : $size
👁‍🗨 تعداد بازدید این فایل : $view
🔐 پسورد فایل : $password
✔️ دیدن فایل : <a href ='https://t.me/$usernamebot?start=download_$ids'>دانلود</a>
⏰ ساعت آپلود : $time_upload
📆 تاریخ آپلود : $date_upload
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
";
}
}
return $result;
}
//===[تاریخ]===//
$date = jdate("Y/m/d");
$time = jdate("H:i:s");
//============================================//
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)){
$chat_id = $update->message->chat->id;
$text = $update->message->text;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$user_name = $update->message->from->username;
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
}
if (isset($update->callback_query)){
$chat_id = $update->callback_query->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$from_id = $update->callback_query->from->id;
$first_name = $update->callback_query->from->first_name;
$last_name = $update->callback_query->from->last_name;
$user_name = $update->callback_query->from->username;
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
}
if (isset($update->inline_query)){
$from_id = $update->inline_query->from->id;
$query = $update->inline_query->query;
$query_id = $update->inline_query->id;
$first_name = $update->inline_query->from->first_name;
$last_name = $update->inline_query->from->last_name;
$user_name = $update->inline_query->from->username;
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
}
$step = $datas["step"];
$name_you = $datas["name"] ?? '🔎 ناشناس !';
$upload = count($datas["ListUpload"]) ?? '0';
$download = count($datas["ListDownload"]) ?? '0';
$ListUpload = $datas["ListUpload"] ?? '🎈 فایلی آپلود نکرده اید !';
$ListDownload = $datas["ListDownload"] ?? '🎈 فایلی دانلود نکرده اید !';
$seen = $datas["seen"];
$time_join = $datas["time"];
$date_join = $datas["date"];
//===[ ذخیره فایل ها ]===//
$files = json_decode(file_get_contents("files.json"),true);
//===[ جوین اجباری ]===//
$settings = json_decode(file_get_contents("settings.json"),true);
$channel = $settings['channel'];
$channel2 = $settings['channel2'];
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".SLOW_KEY."/getChatMember?chat_id=@$channel&user_id=".$from_id));
$joqw = $joincha->result->status;
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot".SLOW_KEY."/getChatMember?chat_id=@$channel2&user_id=".$from_id));
$joqwe = $joinch->result->status;
//=================================================================================================
if (!file_exists("data")){
mkdir("data");
}
if (!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
//===[ ادمین ها ]===//
$dev = "971881348";
if(!in_array($dev,$settings['admin'])){
$settings['admin'][] = "$from_id";
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔰 شما مالک این ربات شناخته شدید !

⚡️ برای ورود به پنل مدیریت از دستور /panel استفاده کنید !
"
]);
}
$admin = $settings['admin'];
//===[ تکست ها و دکمه ها ]===//
$StartText = $settings['start'] ?? '😉🌹 سلام به ربات آپلودر خوشــ آمدید !';
$BackText = $settings['back'] ?? '😉🌹 به منوی اصلی ربات آپلودر برگشتیــد';
$ListText = $settings['listbot'] ?? "🌹 لیست ربات های اِسلُو :\n\n@SlowUploaderBot | اِسلُو آپلودر\n\n@SlowSeenBot | اِسلُو سین\n\n@SlowCreateBot | اِسلُو کریت\n\n@SlowGameBot | اِسلُو گیم\n\n@SlowBet_Bot | اِسلُو بت\n\n@SlowFinderBot | اِسلُو فایندر\n\n┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄\n🔗 CHANNEL1 : @$channel\n🔗 CHANNEL2 : @$channel2";
$JoinText = $settings['join'] ?? "🎈 لطفاً جهت حمایت از ما در کانال های ما عضو شوید\n\n🔐 پس از عضویت روی دکمه ( ✅ تایید عضویت ) کلیک کنید";
$ShareText = $settings['share'] ?? '🔗 اشتراک گذاری فایل';
$UpHelpText = $settings['uphelp'] ?? "📥 *راهنما آپلود :*\n\n`1` - ابتدا شما به بخش آپلود فایل می‌روید\n\n`2` - فایل خود را ارسال میکنید برای ربات\n\n`3` - فایل شما در ربات ذخیره میشود\n\n`4` - جهت حذف کردن فایل خود از ربات میتوانید به بخش حذف فایل بروید ! \n\n`5` - برای اینکه دیگران به فایل شما دسترسی نداشته باشند میتوانید رمز بگذارید برای فایل خود !\n\n`6` - شما دسترسی برای اینکه روی فایل دیگران رمز بگذارید را ندارید و هر فایل مخصوص به یک فرد است ! \n\n`7` - فایل شما میتواند *( گیف , فیلم , عکس , پرونده ( سند ) , موسیقی , ویس و . . . )* باشد !";
$DonHelpText = $settings['donhelp'] ?? "📥 *راهنما دانلود :*\n\n`1` - شما فقط با داشتن لینک میتوانید فایل مورد نظر را دانلود کنید !\n\n`2` - اگر فایلی رمز داشته باشد شما برای دانلود آن باید رمز فایل را داشته باشید !\n\n`3` - شما هم میتوانید فایل خود را آپلود کنید در ربات و لینک دانلود آنرا با دوستان خود به اشتراک بگذارید ! \n\n`4` - دانلود فایل هایی از جمله *( گیف , فیلم , عکس , پرونده ( سند ) , موسیقی , ویس و . . . )* امکان پذیر است";
$updok = $settings['updok'] ?? '📥 آپلود فایل';
$chdok = $settings['chdok'] ?? '📢 کانال ما';
$acdok = $settings['acdok'] ?? '🎫 حساب کاربری';
$helpdok = $settings['helpdok'] ?? '📚 راهنما';
$tkhdondok = $settings['tkhdondok'] ?? '📂 تاریخچه دانلود';
$tkhupdok = $settings['tkhupdok'] ?? '📂 تاریخچه اپلود';
$deldok = $settings['deldok'] ?? '🗑 حذف فایل';
$supdok = $settings['supdok'] ?? '🆘 پشتیبانی';
$passdok = $settings['passdok'] ?? '🔐 تنظیم پسورد';
$listdok = $settings['listdok'] ?? '💻 لیست ربات ها';
$backdok = $settings['backdok'] ?? '🔙 بازگشت';
//==================================================================
$back = json_encode(['keyboard'=>[
[['text'=>"$backdok"]],
],'resize_keyboard'=>true]);

$menu = json_encode(['inline_keyboard'=>[
[['text'=>"$updok",'callback_data'=>"uploaded"]],
[['text'=>"$chdok",'url'=>"https://t.me/$channel"],['text'=>"$helpdok",'switch_inline_query_current_chat'=>"help"]],
[['text'=>"$acdok",'callback_data'=>"account"]],
[['text'=>"$tkhupdok",'callback_data'=>"uplink"],['text'=>"$tkhdondok",'callback_data'=>"donlink"]],
[['text'=>"$deldok",'callback_data'=>"delfile"],['text'=>"$passdok",'callback_data'=>"password"]],
[['text'=>"$supdok",'callback_data'=>"support"],['text'=>"$listdok",'callback_data'=>"listbot"]],
]
]);

$panel_acc = json_encode(['inline_keyboard'=>[
[['text'=>"💫 تغییر نام",'callback_data'=>"editname"]],
[['text'=>"$chdok",'url'=>"https://t.me/$channel"],['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);

$panel_admin = json_encode(['inline_keyboard'=>[
[['text'=>"📊 آمار",'callback_data'=>"amar"]],
[['text'=>"📢 تنظیم کانال اول",'callback_data'=>"Setch"],['text'=>"📢 تنظیم کانال دوم",'callback_data'=>"Setch2"]],
[['text'=>"🍭 تنظیم دکمه ها",'callback_data'=>"SetDook"],['text'=>"🥂 تنظیم متن ها",'callback_data'=>"SetText"]],
[['text'=>"💫 ریست ربات",'callback_data'=>"Reset"],['text'=>"🗑 حذف تمام فایل ها",'callback_data'=>"DelAllFile"]],
[['text'=>"📨 پیام همگانی",'callback_data'=>"PmAll"],['text'=>"📩 فوروارد همگانی",'callback_data'=>"ForAll"]],
[['text'=>"❌ بلاک کردن",'callback_data'=>"Ban"],['text'=>"🔰 آنبلاک کردن",'callback_data'=>"onBan"]],
[['text'=>"➖ حذف ادمین",'callback_data'=>"DelAdmin"],['text'=>"✔️ افزودن ادمین",'callback_data'=>"AddAdmin"]],
]
]);

$back_admin = json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>"BackAdmin"]],
]
]);
//==================================================================
if($step == 'ban' and $tc == 'private'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ شما از ربات مسدود شدید",
'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
 ]);
exit();
}
if($data == "join" and $settings['channel'] != null and $settings['channel2'] != null){
if($joqw != 'member' and $joqw != 'creator' and $joqw != 'administrator' or $joqwe != 'member' and $joqwe != 'creator' and $joqwe != 'administrator'){
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "❌ شما هنوز عضو کانال @$channel و @$channel2 نشدید !",
'show_alert' =>true
]);
}else{
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
$StartText
",
'reply_markup'=>$menu
]);
}
}
if($data == "back"){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"🎈",
'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
 ]);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
$BackText
",
'reply_markup'=>$menu
]);
}
if($data == "uploaded"){
$datas["step"] = "uploading";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
📤 *( فایل , عکس , فیلم , گیف , پرونده ( سند ) , استیکر )* خود را جهت آپلود کردن ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
}
if($data == "account"){
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🎫 _حساب کاربری شما_ :

📑 نام شما : *$name_you*

📥 تعداد دانلود ها : `$download`

📤 تعداد آپلود ها : `$upload`

⏰ ساعت عضویت : `$time_join`

📆 تاریخ عضویت : `$date_join`

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$panel_acc
]);
}
if($data == "editname"){
$datas["step"] = "editnames";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 نام جدید حساب کاربری خود را ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
}
if($data == "uplink"){
if($datas["ListUpload"] == true){
$info = History($from_id,0,'Upload',$usernamebot);
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpageup-0"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpageup-0"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه آپلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}else{
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "$ListUpload",
'show_alert' =>true
]);
}
}
if(strpos($data,'nextpageup-') !== false){
$count = explode('nextpageup-', "$data")[1] + 10;
$info = History($from_id,$count,'Upload',$usernamebot);
if($info == null){
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "❌ صفحه بعد وجود ندارد",
'show_alert' =>false
]);
exit();
}
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpageup-$count"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpageup-$count"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه آپلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}
if(strpos($data,'lastpageup-') !== false){
$count = explode('lastpageup-', "$data")[1] - 10;
$info = History($from_id,$count,'Upload',$usernamebot);
if($info == null){
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "❌ صفحه قبل وجود ندارد",
'show_alert' =>false
]);
exit();
}
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpageup-$count"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpageup-$count"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه آپلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}
if($data == "donlink"){
if($datas["ListDownload"] == true){
$info = History($from_id,0,'Download',$usernamebot);
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpagedon-0"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpagedon-0"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه دانلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}else{
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "$ListDownload",
'show_alert' =>true
]);
}
}
if(strpos($data,'nextpagedon-') !== false){
$count = explode('nextpagedon-', "$data")[1] + 10;
$info = History($from_id,$count,'Upload',$usernamebot);
if($info == null){
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "❌ صفحه بعد وجود ندارد",
'show_alert' =>false
]);
exit();
}
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpagedon-$count"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpagedon-$count"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه دانلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}
if(strpos($data,'lastpagedon-') !== false){
$count = explode('lastpagedon-', "$data")[1] - 10;
$info = History($from_id,$count,'Upload',$usernamebot);
if($info == null){
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "❌ صفحه قبل وجود ندارد",
'show_alert' =>false
]);
exit();
}
$HistoryKey = json_encode(['inline_keyboard'=>[
[['text'=>"صفحه قبل ⬅️",'callback_data'=>"lastpagedon-$count"],['text'=>"➡️ صفحه بعد",'callback_data'=>"nextpagedon-$count"]],
[['text'=>"$backdok",'callback_data'=>"back"]],
]
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📁 تاریخچه دانلود فایل شما :
$info
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$HistoryKey
]);
}
if($data == "delfile"){
$datas["step"] = "deletfile";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🗑 کد فایل مورد نظر جهت حذف را ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$backdok",'callback_data'=>"back"]],
]
])
]);
}
if($data == "password"){
$datas["step"] = "password";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🔐 کد فایل مورد نظر جهت تنظیم پسورد را ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$backdok",'callback_data'=>"back"]],
]
])
]);
}
if($data == "support"){
$datas["step"] = "support";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ شما با موفقیت به پشتیبانی متصل شدید پیام خود را ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$backdok",'callback_data'=>"back"]],
]
])
]);
}
if($data == "listbot"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"$ListText",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$backdok",'callback_data'=>"back"]],
]
])
]);
}
if(in_array($from_id,$admin)){
if($data == "BackAdmin"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"🌹 به پنل اصلی ادمین برگشتید",
'reply_markup'=>$panel_admin
]);
}
if($data == "amar"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$counts = count(glob("data/*/*.json"));
$countfild = count($files);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📈 آمار ربات شما به شرح زیر است

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$counts",'callback_data'=>"none"],['text'=>"📉 تعداد کاربران ربات شما",'callback_data'=>"none"]],
[['text'=>"$countfild",'callback_data'=>"none"],['text'=>"🔰 تعداد فایل های آپلود شده",'callback_data'=>"none"]],
[['text'=>"🔙 بازگشت",'callback_data'=>"BackAdmin"]],
]
])
]);
}
if($data == "AddAdmin"){
$datas["step"] = "AddAdmin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ آیدی عددی ادمین جدید را ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
if($data == "DelAdmin"){
$datas["step"] = "DelAdmin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ آیدی عددی ادمینی که میخواهید حذف کنید را ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
if($data == "Setch"){
$datas["step"] = "Setch";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ آیدی کانال اول را بدون @ ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
if($data == "Setch2"){
$datas["step"] = "Setch2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ آیدی کانال دوم را بدون @ ارسال کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
if($data == "Reset"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("settings.json");
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💫 کل تنظیمات ربات شما ریست شد !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
if($data == "DelAllFile"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با زدن روی دکمه زیر تمام فایل های آپلود شده در آپلودر شما حذف میشوند !

🌷 آیا مطمئن هستید که میخواهید کل فایل های آپلود شده را حذف کنید ؟

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ مطمئن هستم",'callback_data'=>"OkDel"]],
[['text'=>"🔙 بازگشت",'callback_data'=>"BackAdmin"]],
]
])
]);
}
if($data == "OkDel"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($files == true){
unlink("files.json");
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🌹 تمام فایل های آپلود شده در ربات شما حذف شدند !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}else{
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📂 فایلی برای حذف کردن موجود نیست !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>$back_admin
]);
}
}
if($data == "SetText"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$SubStrText1 = (mb_strlen($StartText) <= 30) ? $StartText:mb_substr($StartText,0,30).'...';
$SubStrText2 = (mb_strlen($BackText) <= 30) ? $BackText:mb_substr($BackText,0,30).'...';
$SubStrText3 = (mb_strlen($ListText) <= 30) ? $ListText:mb_substr($ListText,0,30).'...';
$SubStrText4 = (mb_strlen($JoinText) <= 30) ? $JoinText:mb_substr($JoinText,0,30).'...';
$SubStrText5 = (mb_strlen($ShareText) <= 30) ? $ShareText:mb_substr($ShareText,0,30).'...';
$SubStrText6 = (mb_strlen($UpHelpText) <= 30) ? $UpHelpText:mb_substr($UpHelpText,0,30).'...';
$SubStrText7 = (mb_strlen($DonHelpText) <= 30) ? $DonHelpText:mb_substr($DonHelpText,0,30).'...';
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🌟 جهت تغییر متن ها روی متن مورد نظر کلیک کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$SubStrText1",'callback_data'=>"Text-start"]],
[['text'=>"$SubStrText2",'callback_data'=>"Text-back"]],
[['text'=>"$SubStrText3",'callback_data'=>"Text-listbot"]],
[['text'=>"$SubStrText4",'callback_data'=>"Text-join"]],
[['text'=>"$SubStrText5",'callback_data'=>"Text-share"]],
[['text'=>"$SubStrText6",'callback_data'=>"Text-uphelp"]],
[['text'=>"$SubStrText7",'callback_data'=>"Text-donhelp"]],
[['text'=>"🔙 بازگشت",'callback_data'=>"BackAdmin"]],
]
])
]);
}
if(strpos($data,'Text-') !== false){
$codedok = explode('-', "$data")[1];
$datas["step"] = "SetText-$codedok";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🌟 متن جدید را ارسال کنید . . . ! 
",
'reply_markup'=>$back_admin
]);
}
if($data == "SetDook"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🌟 جهت تغییر اسم دکمه روی گزینه های زیر کلیک کنید . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$updok",'callback_data'=>"updok-$updok"]],
[['text'=>"$chdok",'callback_data'=>"chdok-$chdok"],['text'=>"$helpdok",'callback_data'=>"helpdok-$helpdok"]],
[['text'=>"$acdok",'callback_data'=>"acdok-$acdok"]],
[['text'=>"$tkhupdok",'callback_data'=>"tkhupdok-$tkhupdok"],['text'=>"$tkhdondok",'callback_data'=>"tkhdondok-$tkhdondok"]],
[['text'=>"$deldok",'callback_data'=>"deldok-$deldok"],['text'=>"$passdok",'callback_data'=>"passdok-$passdok"]],
[['text'=>"$supdok",'callback_data'=>"supdok-$supdok"],['text'=>"$listdok",'callback_data'=>"listdok-$listdok"]],
[['text'=>"🔙 بازگشت",'callback_data'=>"BackAdmin"]],
]
])
]);
}
if(strpos($data,'dok') !== false){
$namedok = explode('-', "$data")[1];
$datas["step"] = "$data-$namedok";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🌟 اسم جدید دکمه $namedok را ارسال کنید . . . ! 
",
'reply_markup'=>$back_admin
]);
}
}
//==================================================================
if(preg_match('/^(\/start) download_(.*)/', $text , $match) and $tc == 'private'){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/Users.txt", "a") or die("Unable to open file!");
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["step"] = "none";
$datas["seen"] = "ok";
$datas["time"] = "$time";
$datas["date"] = "$date";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
$ids = $match[2];
$Link = $files["$ids"]['link'];
$caption = $files["$ids"]['caption'];
$type_file = $files["$ids"]['type'];
$name_file = TypeFile($type_file);
$password = $files["$ids"]['password'] ?? 'null';
$view = count($files["$ids"]['view']);
$name = $files["$ids"]['name'];
$size = $files["$ids"]['size'];
$time_upload = $files["$ids"]['time'];
$date_upload = $files["$ids"]['date'];
if($Link == null){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
♻️ فایل مورد نظر با کد `( $ids )` یافت نشد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
exit();
}
if($password != 'null'){
$datas["step"] = "SendPassword-$ids";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔐 پسورد فایل ( `$ids` ) را ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
exit();
}
if(!in_array($from_id,$files["$ids"]['view'])){
$files["$ids"]['view'][] = "$from_id";
$outjson = json_encode($files,true);
file_put_contents("files.json",$outjson);
$datas["ListDownload"][] = "$ids";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($type_file == "document"){
$file_id = bot('sendDocument',[
'chat_id'=>$from_id,
'document'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "video"){
$file_id = bot('sendVideo',[
'chat_id'=>$from_id,
'video'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "photo"){
$file_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "voice"){
$file_id = bot('sendVoice',[
'chat_id'=>$from_id,
'voice'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "audio"){
$file_id = bot('sendAudio',[
'chat_id'=>$from_id,
'audio'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "sticker"){
$file_id = bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "video_note"){
$file_id = bot('sendVideoNote',[
'chat_id'=>$from_id,
'video_note'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "location"){
$file_id = bot('sendLocation',[
'chat_id'=>$from_id,
'longitude'=>$Link,
'latitude'=>$caption,
'parse_mode'=>'HTML',
])->result->message_id;
}
$id = bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
✅ فایل بالا در `10` ثانیه دیگر به صورت خودکار پاک میشود !

لطفاً آن را در پیام های ذخیره شده تلگرام بفرستید .... { *فضا ابری تلگرام ☁️* } 

➣ ⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️
",
'parse_mode'=>'MarkDown',
])->result->message_id;
for($i = 10;$i > 0;$i--){
$emoji = str_replace(['10','9','8','7','6','5','4','3','2','1'],['⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️'],$i);
sleep(1);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$id,
'text'=>"
✅ فایل بالا در `$i` ثانیه دیگر به صورت خودکار پاک میشود !

لطفاً آن را در پیام های ذخیره شده تلگرام بفرستید .... { *فضا ابری تلگرام ☁️* } 

➣ $emoji
",
'parse_mode'=>'MarkDown',
]);
}
bot('deletemessage',[
'chat_id'=>$from_id,
'message_id'=>$file_id
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$id,
'text'=>"🗑 _این پیام حذف شد !_",
'parse_mode'=>'MarkDown',
]);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
$StartText
",
'reply_markup'=>$menu
]);
exit();
}
if(preg_match('/^(\/start) password_(.*)/', $text , $match) and $tc == 'private'){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/Users.txt", "a") or die("Unable to open file!");
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["step"] = "none";
$datas["seen"] = "ok";
$datas["time"] = "$time";
$datas["date"] = "$date";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
$ids = $match[2];
$file_admin = $files["$ids"]['admin'];
$Link = $files["$ids"]['link'];
if($Link == null){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
♻️ فایل مورد نظر با کد `( $ids )` یافت نشد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
exit();
}
if($from_id == $file_admin){
$datas["step"] = "SetPassword-$ids";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🔐 پسورد مورد نظر برای فایل زیر را ارسال کنید ! 

https://t.me/$usernamebot?start=download_$ids
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ شما این فایل را آپلود نکرده اید !
",
'reply_markup'=>$menu
]);
}
exit();
}
if(preg_match('/^\/([Ss][Tt][Aa][Rr][Tt])(.*)/',$text,$INV) and $tc == 'private'){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/Users.txt", "a") or die("Unable to open file!");
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["step"] = "none";
$datas["seen"] = "ok";
$datas["time"] = "$time";
$datas["date"] = "$date";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
$StartText
",
'reply_markup'=>$menu
]);
exit();
}
if($text == "$backdok" and $tc == 'private'){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/Users.txt", "a") or die("Unable to open file!");
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["step"] = "none";
$datas["seen"] = "ok";
$datas["time"] = "$time";
$datas["date"] = "$date";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🎈",
'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
 ]);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
$BackText
",
'reply_markup'=>$menu
]);
}
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/Users.txt", "a") or die("Unable to open file!");
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["step"] = "none";
$datas["seen"] = "ok";
$datas["time"] = "$time";
$datas["date"] = "$date";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if(!in_array($from_id,$admin) and $settings['channel'] != null and $settings['channel2'] != null){
if($joqw != 'member' and $joqw != 'creator' and $joqw != 'administrator' or $joqwe != 'member' and $joqwe != 'creator' and $joqwe != 'administrator' and $tc == 'private'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"$JoinText",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔰 عضویت در کانال اول",'url'=>"https://t.me/$channel"]],
[['text'=>"🔰 عضویت در کانال دوم",'url'=>"https://t.me/$channel2"]],
[['text'=>"✅ تایید عضویت",'callback_data'=>"join"]],
]
])
]);
exit();
}
}
if($step == "uploading" and $text != "$backdok" and $tc == 'private'){
$strlen = mb_strlen($update->message->caption);
if($strlen > 550){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
💤 کپشن فایل شما از `550` کارکتر بیشتر است !

✅ تعداد کارکتر های متن کپشن شما : *$strlen*
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
exit();
}
if(isset($update->message->audio)){
$file_id = $update->message->audio->file_id;
$file_size = GetSize($update->message->audio->file_size);
$caption = $update->message->caption;
$typeing = "audio";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendAudio',[
'chat_id'=>$from_id,
'audio'=>$file_id,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
])->result->message_id;
}
if(isset($update->message->voice)){
$file_id = $update->message->voice->file_id;
$file_size = GetSize($update->message->voice->file_size);
$caption = $update->message->caption;
$typeing = "voice";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendVoice',[
'chat_id'=>$from_id,
'voice'=>$file_id,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
])->result->message_id;
}
if(isset($update->message->photo)){
$filephoto = $update->message->photo;
$file_id = $filephoto[count($filephoto)-1]->file_id;
$file_size = GetSize($filephoto[count($filephoto)-1]->file_size);
$caption = $update->message->caption;
$typeing = "photo";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$file_id,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
])->result->message_id;
}
if(isset($update->message->video)){
$file_id = $update->message->video->file_id;
$file_size = GetSize($update->message->video->file_size);
$caption = $update->message->caption;
$typeing = "video";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendVideo',[
'chat_id'=>$from_id,
'video'=>$file_id,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
])->result->message_id;
}
if(isset($update->message->sticker)){
$file_id = $update->message->sticker->file_id;
$file_size = GetSize($update->message->sticker->file_size);
$caption = $update->message->caption;
$typeing = "sticker";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>$file_id,
'caption'=>"$caption",
'parse_mode'=>'HTML',
])->result->message_id;
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$send_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
]);
}
if(isset($update->message->document)){
$file_id = $update->message->document->file_id;
$file_size = GetSize($update->message->document->file_size);
$caption = $update->message->caption;
$typeing = "document";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendDocument',[
'chat_id'=>$from_id,
'document'=>$file_id,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
])->result->message_id;
}
if(isset($update->message->video_note)){
$file_id = $update->message->video_note->file_id;
$file_size = GetSize($update->message->video_note->file_size);
$caption = $update->message->caption;
$typeing = "video_note";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendVideoNote',[
'chat_id'=>$from_id,
'video_note'=>$file_id,
'caption'=>"$caption",
'parse_mode'=>'HTML',
])->result->message_id;
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$send_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
]);
}
if(isset($update->message->location)){
$file_id = $update->message->location->longitude;
$file_size = GetSize($update->message->location->file_size);
$caption = $update->message->location->latitude;
$typeing = "location";
$random = Random(6);
Save($random,$from_id,$typeing,$file_id,$caption,$name_you,$file_size,$date,$time);
$datas["ListUpload"][] = $random;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$send_id = bot('sendLocation',[
'chat_id'=>$from_id,
'longitude'=>$file_id,
'latitude'=>$caption,
'parse_mode'=>'HTML',
])->result->message_id;
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 لینک دانلود فایل :
 https://t.me/$usernamebot?start=download_$random
 
 🔐 تنظیم پسورد برای فایل : 

https://t.me/$usernamebot?start=password_$random

🎈 حجم فایل : $file_size

 ⏰ ساعت آپلود : $time

📆 تاریخ آپلود : $date

Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$send_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$random"]],
]
])
]);
}
}
if(strpos($step,'SetPassword-') !== false and $text != "$backdok" and $tc == 'private'){
$set_id = explode('SetPassword-',$step)[1];
if(mb_strlen($text) < 10){
$files["$set_id"]['password'] = $text;
$outjson = json_encode($files,true);
file_put_contents("files.json",$outjson);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🔐 پسورد فایل ( `$set_id` ) با موفقیت به ( `$text` ) تغییر کرد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ رمز انتخابی حتماً باید کمتر از `10` کارکتر باشد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
}
}
if(strpos($step,'SendPassword-') !== false and $text != "$backdok" and $tc == 'private'){
$ids = explode('SendPassword-',$step)[1];
$Link = $files["$ids"]['link'];
$caption = $files["$ids"]['caption'];
$type_file = $files["$ids"]['type'];
$name_file = TypeFile($type_file);
$password = $files["$ids"]['password'];
$view = count($files["$ids"]['view']);
$name = $files["$ids"]['name'];
$size = $files["$ids"]['size'];
$time_upload = $files["$ids"]['time'];
$date_upload = $files["$ids"]['date'];
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($password != $text){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ پسورد فایل ( `$ids` ) صحیح نیست ! 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
exit();
}
if(!in_array($from_id,$files["$ids"]['view'])){
$files["$ids"]['view'][] = "$from_id";
$outjson = json_encode($files,true);
file_put_contents("files.json",$outjson);
$datas["ListDownload"][] = "$ids";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($type_file == "document"){
$file_id = bot('sendDocument',[
'chat_id'=>$from_id,
'document'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "video"){
$file_id = bot('sendVideo',[
'chat_id'=>$from_id,
'video'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "photo"){
$file_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "voice"){
$file_id = bot('sendVoice',[
'chat_id'=>$from_id,
'voice'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "audio"){
$file_id = bot('sendAudio',[
'chat_id'=>$from_id,
'audio'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "sticker"){
$file_id = bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "video_note"){
$file_id = bot('sendVideoNote',[
'chat_id'=>$from_id,
'video_note'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
])->result->message_id;
}
if($type_file == "location"){
$file_id = bot('sendLocation',[
'chat_id'=>$from_id,
'longitude'=>$Link,
'latitude'=>$caption,
'parse_mode'=>'HTML',
])->result->message_id;
}
$id = bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
✅ فایل بالا در `10` ثانیه دیگر به صورت خودکار پاک میشود !

لطفاً آن را در پیام های ذخیره شده تلگرام بفرستید .... { *فضا ابری تلگرام ☁️* } 

➣ ⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️
",
'parse_mode'=>'MarkDown',
])->result->message_id;
for($i = 10;$i > 0;$i--){
$emoji = str_replace(['10','9','8','7','6','5','4','3','2','1'],['⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚪️','⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️'],$i);
sleep(1);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$id,
'text'=>"
✅ فایل بالا در `$i` ثانیه دیگر به صورت خودکار پاک میشود !

لطفاً آن را در پیام های ذخیره شده تلگرام بفرستید .... { *فضا ابری تلگرام ☁️* } 

➣ $emoji
",
'parse_mode'=>'MarkDown',
]);
}
bot('deletemessage',[
'chat_id'=>$from_id,
'message_id'=>$file_id
]);
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$id,
'text'=>"🗑 _این پیام حذف شد !_",
'parse_mode'=>'MarkDown',
]);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
$StartText
",
'reply_markup'=>$menu
]);
}
if($step == "editnames" and $text != "$backdok" and $tc == 'private'){
if(mb_strlen($text) < 15){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"🎈",
'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
 ]);
$datas["name"] = $text;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
✅ نام حساب کاربری شما با موفقیت به ( *$text* ) تغییر کرد !

$BackText
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ نامی که انتخاب میکنید حتماً باید کمتر از `15` کارکتر باشد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back
]);
}
}
if($step == "deletfile" and $tc == 'private'){
if(mb_strlen($text) == 6){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$echo = DeleteFile($text,$from_id);
if($echo == 'ok'){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🗑 فایل ( `$text` ) با موفقیت حذف شد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}
if($echo == 'noadmin'){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ شما این فایل را آپلود نکرده اید و نمی‌توانید آنرا حذف کنید !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}
if($echo == 'nofind'){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
💤 فایل ( `$text` ) وجود ندارد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 شماره فایل `6` حرف انگلیسی است فقط !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($step == "password" and $tc == 'private'){
if(mb_strlen($text) == 6){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🔐 هم اکنون میتوانید از دکمه زیر برای فایل ( $text ) پسورد انتخاب کنید !
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔐 تنظیم پسورد",'url'=>"https://t.me/$usernamebot?start=password_$text"]],
]
])
]);
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 شماره فایل `6` حرف انگلیسی است فقط !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($step == "support" and $tc == 'private'){
foreach($admin as $admins){
bot('ForwardMessage',[
'chat_id'=>$admins,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
]);
bot('sendmessage',[
'chat_id'=>$admins,
'text'=>"
ID : `$from_id`

🔰 پی وی کاربر [$first_name](t.me/$user_name) یا [$first_name](tg://openmessage?user_id=$from_id) است 
",
'parse_mode'=>'MarkDown',
]);
}
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🆘 پیام شما با موفقیت برای پشتیبانی ارسال شد . . . !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
Bot : @$usernamebot
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"$backdok",'callback_data'=>"back"]],
]
])
]);
}
if(in_array($from_id,$admin)){
if($update->message->reply_to_message){
$userid = $update->message->reply_to_message->forward_from->id;
if(file_exists("data/$userid/$userid.json")){
bot('sendmessage',[
'chat_id'=>$userid,
'text'=>"
🌹 شما یک پیام از پشتیبانی دارید 

✔️ پیام : `$text`
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🆘 پیام به پشتیبانی",'callback_data'=>"support"]],
],
])
]);
$infouser = bot('getChatMember',['chat_id'=>"$userid",'user_id'=>"$userid"]);
$nameuser = $infouser->result->user->first_name;
$nameuser = str_replace(['`'],'',"$nameuser");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت پیام شما به کاربر ( `$nameuser` ) ارسال شد",
'parse_mode'=>'MarkDown',
]);
}
}
}
if(strpos($query, 'share_') !== false){
$share_id = explode('share_', "$query")[1];
$Link = $files["$share_id"]['link'];
$caption = $files["$share_id"]['caption'];
$type_file = $files["$share_id"]['type'];
$name_file = TypeFile($type_file);
$password = $files["$share_id"]['password'] ?? 'null';
$view = count($files["$share_id"]['view']);
$name = $files["$share_id"]['name'];
$size = $files["$share_id"]['size'];
$time_upload = $files["$share_id"]['time'];
$date_upload = $files["$share_id"]['date'];
if($password != 'null'){
bot('AnswerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([
[
'id'=> base64_encode(1),
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/158',
'title'=>'🔐 این فایل پسورد دارد !',
'input_message_content'=>[
'message_text'=>"
❗️این فایل پسورد دارد و نمی‌توانید اشتراک گذاری کنید ! 

*⚠️ با استفاده از دکمه زیر فایل را دانلود کنید !*
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true
],
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📥 دانلود فایل",'url'=>"https://t.me/$usernamebot?start=download_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
],
])
]);
exit();
}
if($type_file == "document"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'document',
'title'=>"$ShareText",
'document_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "video"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'video',
'title'=>"$ShareText",
'video_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "photo"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'photo',
'title'=>"$ShareText",
'photo_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "voice"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'voice',
'title'=>"$ShareText",
'voice_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "audio"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'audio',
'title'=>"$ShareText",
'audio_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "sticker"){
bot('answerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'sticker',
'title'=>"$ShareText",
'sticker_file_id'=>$Link,
'caption'=>"$caption
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
📤 آپلود کننده این فایل : $name

✅ نوع فایل : $name_file

🎈 حجم فایل : $size

👁‍🗨 تعداد بازدید این فایل : $view

⏰ ساعت آپلود : $time_upload

📆 تاریخ آپلود : $date_upload

Bot : @$usernamebot",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📚 راهنما",'switch_inline_query_current_chat'=>"help"],['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
]
])
]);
}
if($type_file == "video_note" or $type_file == "location"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([
[
'id'=> base64_encode(1),
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/158',
'title'=>'⚠️ خطا دریافت فایل !',
'input_message_content'=>[
'message_text'=>"
❗️این نوع فایل را نمیتوان به این صورت به اشتراک بگذارید

*⚠️ با استفاده از دکمه زیر فایل را دانلود کنید !*
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true
],
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"📥 دانلود فایل",'url'=>"https://t.me/$usernamebot?start=download_$share_id"]],
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
]
]
],
])
]);
}
}
if($query == "help"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$query_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([
[
'id'=> base64_encode(1),
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/156',
'title'=>'📥 راهنما آپلود',
'input_message_content'=>[
'message_text'=>"$UpHelpText",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true
],
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
[['text'=>"⚙ ورود به ربات",'url'=>"https://t.me/$usernamebot"]],
]
]
],
[
'id'=> base64_encode(2),
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/156',
'title'=>'📥 راهنما دانلود',
'input_message_content'=>[
'message_text'=>"$DonHelpText",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true
],
'reply_markup'=>[
'inline_keyboard'=>[
[['text'=>"🥤کانال ما",'url'=>"https://t.me/$channel"]],
[['text'=>"⚙ ورود به ربات",'url'=>"https://t.me/$usernamebot"]],
]
]
],
])
]);
exit();
}
if(in_array($from_id,$admin)){
if(preg_match('/^\/([Pp][Aa][Nn][Ee][Ll])(.*)/',$text,$INV) and $tc == 'private'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"😁 سلام به پنل مدیریت خوش اومدی",
'reply_markup'=>$panel_admin
]);
}
if($step == "AddAdmin" and $data != "BackAdmin" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(in_array($text,$settings['admin'])){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
☑️ این کاربر از قبل ادمین بوده است
",
'reply_markup'=>$back_admin
]);
}else{
$settings['admin'][] = $text;
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
☑️ کاربر $text با موفقیت به لیست ادمین ها اضافه شد !
",
'reply_markup'=>$back_admin
]);
}
}
if($step == "DelAdmin" and $data != "BackAdmin" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(in_array($text,$settings['admin'])){
$search = array_search($text, $settings['admin']);
unset($settings['admin'][$search]);
$settings['admin'] = array_values($settings['admin']);
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
☑️ کاربر $text با موفقیت از لیست ادمین ها حذف شد !
",
'reply_markup'=>$back_admin
]);
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
☑️ این کاربر در لیست ادمین ها نیست
",
'reply_markup'=>$back_admin
]);
}
}
if($step == "Setch" and $data != "BackAdmin" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(strpos($text,'@') !== false){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً آیدی کانال خود را بدون @ ارسال کنید
",
'reply_markup'=>$back_admin
]);
}else{
if(mb_strlen($text) > 32){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ آیدی کانال از 32 کاراکتر نباید بیشتر باشد !
",
'reply_markup'=>$back_admin
]);
}else{
$settings['channel'] = $text;
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
✅ ایدی کانال اول با موفقیت به @$text تغییر کرد !
",
'reply_markup'=>$back_admin
]);
}
}
}
if($step == "Setch2" and $data != "BackAdmin" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(strpos($text,'@') !== false){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً آیدی کانال خود را بدون @ ارسال کنید
",
'reply_markup'=>$back_admin
]);
}else{
if(mb_strlen($text) > 32){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ آیدی کانال از 32 کاراکتر نباید بیشتر باشد !
",
'reply_markup'=>$back_admin
]);
}else{
$settings['channel2'] = $text;
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
✅ ایدی کانال دوم با موفقیت به @$text تغییر کرد !
",
'reply_markup'=>$back_admin
]);
}
}
}
if(strpos($step,'dok') !== false and $data != "BackAdmin" and $tc == 'private' and $text == true){
$codedok = explode('-',$step)[0];
$namedok = explode('-',$step)[1];
$settings["$codedok"] = $text;
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
💫 اسم دکمه $namedok با موفقیت به $text تغییر کرد
",
'reply_markup'=>$back_admin
]);
}
if(strpos($step,'SetText-') !== false and $data != "BackAdmin" and $tc == 'private' and $text == true){
$codedok = explode('-',$step)[1];
if(mb_strlen($text) > 600){
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متن شما نباید از 600 کاراکتر بیشتر باشد 
",
'reply_markup'=>$back_admin
]);
exit();
}
$settings["$codedok"] = $text;
$outjson = json_encode($settings,true);
file_put_contents("settings.json",$outjson);
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"
🥂 این متن با موفقیت تغییر کرد !
",
'reply_markup'=>$back_admin
]);
}
}
if(file_exists("error_log")) unlink("error_log");
?>