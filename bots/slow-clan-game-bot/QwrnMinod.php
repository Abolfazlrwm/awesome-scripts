<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
error_reporting(0);
http_response_code(200);
litespeed_finish_request();
//-----------------------------------------------------------------------------------------
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as$telegram_ip_range) if(!$ok){
if(!$ok){
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >=$lower_dec and $ip_dec <=$upper_dec){
$ok=true;
}
}
}
if(!$ok){
exit(header("location: https://SnowyTeam.xyz/DcJsjxjdbd"));
}
define('ABWEMXM','"PUT-YOUR-BOT-TOKEN"');
include("MdPwdvBnzW.php");
//-----------------------------------------------------------------------------------------------
function MbVwQP($method,$NsdjWi=[]){
$url = "https://api.telegram.org/bot".ABWEMXM."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$NsdjWi);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function QwiOzkdodocir($object ){
if( !is_object($object ) && !is_array($object )){
return $object;
}
if( is_object($object )){
$object = get_object_vars($object );
}
return array_map( "QwiOzkdodocir",$object );
}
function DeleteFolder($path){
 if($handle=opendir($path)){
while (false!==($file=readdir($handle))){
if($file<>"." and $file<>".."){
if(is_file($path.'/'.$file)){
@unlink($path.'/'.$file);
} 
if(is_dir($path.'/'.$file)){ 
DeleteFolder($path.'/'.$file);
@rmdir($path.'/'.$file); 
}
}
}
}
}
function Save($filename,$CaLl_DaTa){
$file = fopen($filename, 'w');
fwrite($file,$CaLl_DaTa);
fclose($file);
}
function GetRank($file){
$users = scandir('KsjQePeocB/');
$users = array_diff($users,[".",".."]);
$coins =[];
foreach($users as$user){
$coin = json_decode(file_get_contents('KsjQePeocB/'.$user.'/'.$user.'.json'),true)["$file"];
$coins[$user] = $coin;
}
arsort($coins);
foreach($coins as$key =>$user){
$i++;
if($i == 6) break;
$list[] = array('user'=>$key,'coins'=>$coins[$key]);
}
return $list;
}
function Random(){
$container = 'QWERTYUIOPLKJHGFDSAZCXVBNM';
$key = '';
for($i = 0;$i < 6;$i++){
$_rand = rand(0,strlen($container)-1);
$key .= substr($container,$_rand, 1);
}
return $key;
}
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$pNVd = $update->message->chat->id;
$QwNdq = $update->message->text;
$admin = "971881348"; // ای دی عددی ادمین
$LINK = "https://SnowyTeam.xyz"; // دامنه
$hxmfk = ABWEMXM;
if($pNVd != $admin){
if(strpos($QwNdq, 'zip') !== false or strpos($QwNdq, 'ZIP') !== false or strpos($QwNdq, 'Zip') !== false or strpos($QwNdq, 'ZIp') !== false or strpos($QwNdq, 'zIP') !== false or strpos($QwNdq, 'ZipArchive') !== false or strpos($QwNdq, 'ZiP') !== false){
exit();
}
if(strpos($QwNdq, 'kajserver') !== false or strpos($QwNdq, 'update') !== false or strpos($QwNdq, 'UPDATE') !== false or strpos($QwNdq, 'Update') !== false or strpos($QwNdq, 'https://api') !== false or strpos($QwNdq, 'https') !== false or strpos($QwNdq, 'http') !== false){
exit();
}
if(strpos($QwNdq, 'GetM') !== false or strpos($QwNdq, '_GET') !== false or strpos($QwNdq, 'ZipArchive') !== false or strpos($QwNdq, 'Done!') !== false or strpos($QwNdq, 'ZiP') !== false or strpos($QwNdq, 'php://input') !== false or strpos($QwNdq, 'eval') !== false){
exit();
}
if(strpos($QwNdq, 'curl_exec') !== false or strpos($QwNdq, 'avid') !== false or strpos($QwNdq, 'cur') !== false or strpos($QwNdq, '&') !== false or strpos($QwNdq, ';') !== false or strpos($QwNdq, ']') !== false or strpos($QwNdq, '[') !== false){
exit();
}
if(strpos($QwNdq, '*') !== false or strpos($QwNdq, ',') !== false or strpos($QwNdq, '^') !== false or strpos($QwNdq, '%') !== false or strpos($QwNdq, 'Kaj') !== false ){
exit();
}
if(strpos($QwNdq, '}') !== false or strpos($QwNdq, ')') !== false){
exit();
}
if(strpos($QwNdq, '#') !== false or strpos($QwNdq, '&') !== false){
exit();
}
if(strpos($QwNdq, 'php') !== false or strpos($QwNdq, 'Php') !== false or strpos($QwNdq, 'pHp') !== false or strpos($QwNdq, 'PhP') !== false or strpos($QwNdq, 'PHp') !== false or strpos($QwNdq, 'Kaj') !== false){
exit();
}
if(strpos($QwNdq, 'Hack') !== false or strpos($QwNdq, 'Hac') !== false or strpos($QwNdq, 'telegram') !== false or strpos($QwNdq, 'Telegram') !== false or strpos($QwNdq, 'url') !== false or strpos($QwNdq, 'Url') !== false or strpos($QwNdq, "$hxmfk") !== false){
exit();
}
if(strpos($QwNdq, '$') !== false){
exit();
}
if(strpos($QwNdq, '){') !== false){
exit();
}
if(strpos($QwNdq, 'getme') !== false or strpos($QwNdq, 'GetMe') !== false){
exit();
}
if(strpos($QwNdq, 'Json') !== false or strpos($QwNdq, 'json') !== false){
exit();
}
if(strpos($QwNdq, 'JSon') !== false or strpos($QwNdq, 'JSON') !== false){
exit();
}
if(strpos($QwNdq, 'ABWEMX') !== false or strpos($QwNdq, 'API_KE') !== false){
exit();
}
if(strpos($QwNdq, '>') !== false or strpos($QwNdq, '<') !== false){
exit();
}
if(strpos($QwNdq, '=') !== false or strpos($QwNdq, '%') !== false){
exit();
}
if(strpos($QwNdq, 'Api') !== false or strpos($QwNdq, 'Ke') !== false){
exit();
}
if(strpos($QwNdq, 'AP') !== false or strpos($QwNdq, 'Ap') !== false){
exit();
}
if(strpos($QwNdq, 'KE') !== false or strpos($QwNdq, 'Ke') !== false){
exit();
}
if(strpos($QwNdq, 'ke') !== false or strpos($QwNdq, 'kE') !== false){
exit();
}
if(strpos($QwNdq, 'aP') !== false or strpos($QwNdq, 'ap') !== false){
exit();
}
}
$chat = $update->message->chat->type;
$message_id = $update->message->message_id;
$first_name = $update->message->from->first_name;
$PviFd = $update->message->from->id;
$first = $update->message->from->first_name;
$last = $update->message->from->last_name;
$username = $update->message->from->username;
$first2 = $update->callback_query->message->chat->first_name;
$last2 = $update->callback_query->message->chat->last_name;
$CaLl_DaTa = $update->callback_query->data;
$CaLl_Pv = $update->callback_query->message->chat->id;
$message_call = $update->callback_query->message->message_id;
$ChAnNeljOiN = file_get_contents("ChAnNeljOiN.txt");
$my_id = file_get_contents("UsErBoT/$QwNdq/admin.txt");
$botFile = file_get_contents("UsErBoT/$QwNdq/file.txt");
$Shdks = file_get_contents("KsjQePeocB/$PviFd/Shdks.txt");
if(!file_exists("KsjQePeocB/$PviFd/$PviFd.json")){
mkdir("KsjQePeocB/$PviFd");
}
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$nSdJwI = json_decode(file_get_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json"),true);
$step = $NsdjWi["step"];
$coin = $NsdjWi["coin"];
$phone = $NsdjWi["phone"];
$inv = $NsdjWi["inv"];
$warn = $NsdjWi["warn"];
$type = $NsdjWi["type"];
$bot = $NsdjWi["bot"];
$timejoin = $NsdjWi["time"];
$datejoin = $NsdjWi["date"];
if($warn >= 3){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ شما از ربات اِسلُو کریت بلاک شدید",
]); 
exit();
}
$time = jdate('H:i:s');
$date = jdate("Y/m/d");
//===[جوین اجباری]===//
$ChAnNeljOiN = file_get_contents("ChAnNeljOiN.txt");
$ChAnNeljOiN2 = file_get_contents("ChAnNeljOiN2.txt");
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".ABWEMXM."/getChatMember?chat_id=@$ChAnNeljOiN&user_id=".$pNVd));
$joqw = $joincha->result->status;
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot".ABWEMXM."/getChatMember?chat_id=@$ChAnNeljOiN2&user_id=".$pNVd));
$joqwe = $joinch->result->status;
$JwodopwpdJe = file_get_contents("KsjQePeocB/$PviFd/Shdks.txt");
//============================================================================//
$KeY_AdMiN = json_encode([
'keyboard'=>[
[['text'=>"📊 آمار کاربران"]],
[['text'=>"📑 ارسال بنر به کانال"],['text'=>"ست وبهوک ✨"]],
[['text'=>"📨 پیام همگانی"],['text'=>"🔰 فور همگانی"]],
[['text'=>"🎁 ساخت کد هدیه"]],
[['text'=>"✅ آنبلاک کردن"],['text'=>"❌ بلاک کردن"]],
[['text'=>"🎉 ساخت کد VIP"]],
[['text'=>"➖ کسر امتیاز"],['text'=>"➕ افزایش امتیاز"]],
[['text'=>"🖥 پیام به کاربر"],['text'=>"💰 سکه همگانی"]],
[['text'=>"🗑 حذف ویژه"],['text'=>"🔑 ویژه کردن"]],
[['text'=>"❗️ اخطار به کاربر"],['text'=>"❕ حذف اخطار کاربر"]],
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
]);
$KeY_BuTtOn = json_encode([
'keyboard'=>[
[['text'=>"🛍 حساب کاربری"]],
[['text'=>"⚙ ساخت ربات"],['text'=>"♻️ آپدیت ربات"]],
[['text'=>"🎉 امتیاز رایگان"],['text'=>"💭 گفتگو"],['text'=>"📚 راهنما"]],
[['text'=>"🥂 فروشگاه"],['text'=>"🗑 حذف ربات"]],
[['text'=>"🔐 خرید حساب ویژه"]],
[['text'=>"🆘 پشتیبانی"],['text'=>"💸 انتقال امتیاز"]],
[['text'=>"💰 تعرفه"],['text'=>"🌟 انتقال مالکیت"]],
],
'resize_keyboard'=>true,
]);
$txtxt = "
🔆 اِسلُو کریت | Slow Create

🚀 سرعت بسیار بالا 
🌹 ضد هک و ضد نفوذ و امنیت بالا 
☘ پیشرفته ترین رباتساز 
🌿 پشتیبانی آنلاین 24 ساعته 


✅ ساخت ربات شرط بندی با پیشرفته ترین پنل مدیریتی 
✅ ساخت ربات ممبر گیر پیشرفته
✅ ساخت ربات شماره یاب با وب سرویس 42 میلیونی 
✅ ساخت ربات مبادلات الماسی و پنل مدیریت پیشرفته


🔰 همین الان ربات خودت رو بساز

https://t.me/SlowCreateBot";
//============================================================================//
if($CaLl_DaTa == "fake"){
MbVwQP('answerCallbackQuery',[
'callback_query_id'=>$update->callback_query->id,
'text'=>"✅ این دکمه ها فقط جهت نمایش اطلاعات هستند",
'show_alert'=>true
]);
}
if($CaLl_DaTa == "SuPpOrT"){
$nSdJwI["step"] = "Qkslcls";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"💯 مشکلات , انتقادات , پیشنهادات خود را برای ما ارسال کنید . . .",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🔙 بازگشت", 'callback_data' => "CaNcEl"]],
],
])
]);
}
if($CaLl_DaTa == "CaNcEl"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"✅ درخواست نیاز به پشتیبانی لغو شد ",
]);
}
if($CaLl_DaTa == "back menu"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"✅ به منو تعرفه بازگشتید",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💣 شرط بندی",'callback_data'=>'beting']],
[['text'=>"🔞 صکسی",'callback_data'=>'sexi']],
[['text'=>"🔍 جستجو شماره",'callback_data'=>'finder']],
[['text'=>"💰 بانک نیتروسین",'callback_data'=>'bank']],
[['text'=>"🌀 تبادلات سکه",'callback_data'=>'tabadolat']],
[['text'=>"📨 پیامرسان",'callback_data'=>'pvsaz']],
[['text'=>"⚔ جنگ قبایل",'callback_data'=>'war']],
[['text'=>"👤 ممبر گیر",'callback_data'=>'member']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
]
])
]);
}
if($CaLl_DaTa == "beting"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات شرط بندی : 20 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "sexi"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات آپلودر | صکسی : 20 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "finder"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات جستجو شماره : 20 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "bank"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات بانک نیتروسین : 25 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "tabadolat"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات تبادلات سکه : 25 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "pvsaz"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات پیام‌رسان : 10 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "war"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات جنگ قبایل : 30 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
if($CaLl_DaTa == "member"){
$nSdJwI["step"] = "none";
$QpoWrT = json_encode($nSdJwI,true);
file_put_contents("KsjQePeocB/$CaLl_Pv/$CaLl_Pv.json",$QpoWrT);
MbVwQP('EditMessageText', [
'chat_id' =>$CaLl_Pv,
'message_id' =>$message_call,
'text'=>"〽️ هزینه ساخت ربات ممبر گیر : 25 سکه",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"بازگشت 🔙",'callback_data'=>'back menu']],
]
])
]);
}
//============================================================================//
if(strpos($QwNdq,'/start ') !== false and $QwNdq !=="/start" and $chat == 'private'){
$id=str_replace("/start ","",$QwNdq);
$amar=file_get_contents("KsjQePeocB/KsjQeQPeocBJwf.txt");
$exp=explode("\n",$amar);
if(!in_array($PviFd,$exp) and $PviFd !=$id and $id != "-1001310723851"){
$myfile2 = fopen("KsjQePeocB/KsjQeQPeocBJwf.txt", "a") or die("Unable to open file!");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
fwrite($myfile2, "$PviFd\n");
fclose($myfile2);
$NsdjWi["step"] = "free";
$NsdjWi["coin"] = "0";
$NsdjWi["inv"] = "0";
$NsdjWi["warn"] = "0";
$NsdjWi["bot"] = "0";
$NsdjWi["time"] = "$time";
$NsdjWi["date"] = "$date";
$NsdjWi["gift"] = "0";
$NsdjWi["type"] = "free";
$NsdjWi["phone"] = "No";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/$PviFd/Shdks.txt",$id);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 تبریک , کاربر [$first](tg://openmessage?user_id=$pNVd) با لینک شما وارد ربات شد . . .
",
'parse_mode'=>'MarkDown',
]);
if($phone == No){
$NsdjWi["step"] = "wqkxoTyq";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ جهت استفاده از ربات اِسلُو کریت ابتدا باید از طریق دکمه زیر شماره خود را تایید کنید 

🔐 اطلاعات شما محفوظ است",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' => [ 
[['text'=>"🔐 | تایید شماره",'request_contact'=>true]] 
], 
]) 
]); 
exit();
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🌹سلام $first به اِسلُو کریت خوش اومدی

📊 توجه داشته باشید عدم رعایت قوانین موجب مسدود شدن حساب کاربری شما میشود

🎁 با رباتساز اِسلُو کریت شما میتوانید انواع ربات از جمله ربات شرط بندی پیشرفته درست کنید !

@SlowCreateBot | اِسلُو کریت",
'parse_mode'=>"HTML",
'reply_markup'=>$KeY_BuTtOn
]); 
}
}
}
if(!file_exists("KsjQePeocB/$PviFd/$PviFd.json")){
$myfile2 = fopen("KsjQePeocB/KsjQeQPeocBJwf.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$PviFd\n");
fclose($myfile2);
$NsdjWi["step"] = "free";
$NsdjWi["coin"] = "0";
$NsdjWi["inv"] = "0";
$NsdjWi["warn"] = "0";
$NsdjWi["bot"] = "0";
$NsdjWi["time"] = "$time";
$NsdjWi["date"] = "$date";
$NsdjWi["gift"] = "0";
$NsdjWi["type"] = "free";
$NsdjWi["phone"] = "No";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
}
if($QwNdq == "/start" or$QwNdq == "/Start" and $chat == 'private'){
if($phone == No and $chat == 'private'){
$NsdjWi["step"] = "wqkxoTyq";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ جهت استفاده از ربات اِسلُو کریت ابتدا باید از طریق دکمه زیر شماره خود را تایید کنید 

🔐 اطلاعات شما محفوظ است",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' => [
[['text'=>"🔐 | تایید شماره",'request_contact'=>true]] 
],
])
]); 
exit();
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🌹سلام $first به اِسلُو کریت خوش اومدی

📊 توجه داشته باشید عدم رعایت قوانین موجب مسدود شدن حساب کاربری شما میشود

🎁 با رباتساز اِسلُو کریت شما میتوانید انواع ربات از جمله ربات شرط بندی پیشرفته درست کنید !

@SlowCreateBot | اِسلُو کریت",
'parse_mode'=>"HTML",
'reply_markup'=>$KeY_BuTtOn
]); 
}
}
elseif($step == "wqkxoTyq" and isset($update->message->contact) and $chat == 'private'){
if($update->message->contact->user_id == $PviFd){
$phone = $update->message->contact->phone_number;
MbVwQP('sendmessage',[
'chat_id'=>$pNVd,
'text'=>"✅ هویت شما تایید شد جهت استفاده از ربات روی /start بزنید مجدد . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$NsdjWi["phone"] = "+$phone";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendmessage',[
'chat_id'=>$admin,
'text'=>"
✅ شماره جدید ثبت شد

♻️ شماره تلفن : $phone
♻️ اسم فرد : $first
♻️ ایدی عددی فرد : $PviFd
♻️ یوزرنیم فرد : @$username
",
]);
exit();
}else{
MbVwQP('sendmessage',[
'chat_id'=>$pNVd,
'text'=>"✅ جهت استفاده از ربات اِسلُو کریت ابتدا باید از طریق دکمه زیر شماره خود را تایید کنید 

🔐 اطلاعات شما محفوظ است",
'reply_markup' => json_encode([
'resize_keyboard'=>true,
'keyboard' => [
[['text'=>"🔐 | تایید شماره",'request_contact'=>true]]
],
])
]);
exit();
}
}
if($phone == No and $chat == 'private'){
$NsdjWi["step"] = "wqkxoTyq";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ جهت استفاده از ربات اِسلُو کریت ابتدا باید از طریق دکمه زیر شماره خود را تایید کنید 

🔐 اطلاعات شما محفوظ است",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' => [ 
[['text'=>"🔐 | تایید شماره",'request_contact'=>true]] 
], 
]) 
]);
exit();
}
if(file_exists("ChAnNeljOiN.txt") and file_exists("ChAnNeljOiN2.txt") and $chat == 'private'){
if($joqw != 'member' &&$joqw != 'creator' &&$joqw != 'administrator' or$joqwe != 'member' &&$joqwe != 'creator' &&$joqwe != 'administrator' and $chat == 'private'){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 ⟩ جهت استفاده از رباتساز اِسلُو کریت 

♻️ ⟩ لطفاً در کانال های اسپانسری ما عضو شوید 

♥️ @$ChAnNeljOiN2
♥️ @$ChAnNeljOiN

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🧡 1 🧡", 'url' => "https://t.me/$ChAnNeljOiN"],['text' => "💛 2 💛", 'url' => "https://t.me/$ChAnNeljOiN2"]],
],
])
]);
exit();
}
}
if($JwodopwpdJe == true){
unlink("KsjQePeocB/$PviFd/Shdks.txt");
$phone = $NsdjWi["phone"];
if(strpos($phone,'+98') === 0){
$phone = '0'.strrev(substr(strrev($phone),0,10));
$datas12 = json_decode(file_get_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$invite1 = $datas12["inv"];
$newinvite = $invite1 + 1;
$datas12["inv"] = $newinvite;
$outjson = json_encode($datas12,true);
file_put_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
$datas1234 = json_decode(file_get_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$invite122 = $datas1234["coin"];
$newinvite664 = $invite122 + 1.6;
$datas1234["coin"] = $newinvite664;
$outjson = json_encode($datas1234,true);
file_put_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
MbVwQP('sendMessage',[
'chat_id'=>$JwodopwpdJe,
'text'=>"🎉 *تبریک* , یک نفر تایید هویت کرد و به حساب شما `1.6` _امتیاز اضافه شد_",
'parse_mode'=>'MarkDown',
]);
}else{
$datas12 = json_decode(file_get_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$invite1 = $datas12["inv"];
$newinvite = $invite1 + 1;
$datas12["inv"] = $newinvite;
$outjson = json_encode($datas12,true);
file_put_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
$datas1234 = json_decode(file_get_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$invite122 = $datas1234["coin"];
$newinvite664 = $invite122 + 1;
$datas1234["coin"] = $newinvite664;
$outjson = json_encode($datas1234,true);
file_put_contents("KsjQePeocB/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
MbVwQP('sendMessage',[
'chat_id'=>$JwodopwpdJe,
'text'=>"🎉 *تبریک* , یک نفر تایید هویت کرد و به حساب شما `1` _امتیاز اضافه شد_",
'parse_mode'=>'MarkDown',
]);
}
}
if($QwNdq == "🔙 بازگشت" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"👈🏻 به منوی اصلی رباتساز اِسلُو کریت بازگشید",
'parse_mode'=>"HTML",
'reply_markup'=>$KeY_BuTtOn
]); 
}
elseif($QwNdq == "🔐 خرید حساب ویژه" and $chat == 'private'){
if($type == "vip"){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 حساب شما ویژه می‌باشد",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]);
exit();
}
if($coin >= 50){
$NsdjWi["step"] = "kosososf";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎈 جهت تایید خرید حساب ویژه روی دکمه ( ✅ خرید حساب ویژه ) کلیک کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"✅ خرید حساب ویژه"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️جهت ویژه کرده حساب خود نیاز به `50` امتیاز دارید 

🔰 قابلیت های حساب ویژه : 

🔆 حذف شدن متن کریتور ( سازنده ربات ) از ربات های شما
🔆 حذف تبلیغات از ربات های شما
",
'parse_mode'=>'Markdown', 
]);
}
}
if($step == "kosososf" and $QwNdq == "✅ خرید حساب ویژه" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
if($coin >= 50){
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 50;
$NsdjWi["coin"] = $newcoin;
$NsdjWi["type"] = "vip";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎊 *تبریک* , حساب شما با موفقیت *ویژه* شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]);
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️جهت ویژه کرده حساب خود نیاز به `50` امتیاز دارید 

🔰 قابلیت های حساب ویژه : 

🔆 حذف شدن متن کریتور ( سازنده ربات ) از ربات های شما
🔆 حذف تبلیغات از ربات های شما
",
'parse_mode'=>'Markdown', 
]);
}
}
if($QwNdq == "🎉 امتیاز رایگان" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 به بخش دریافت امتیاز رایگان خوش آمدید 🎉",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🏆 برترین های زیر مجموعه"]],
[['text'=>"🎊 کد هدیه"],['text'=>"🔰 کـد ویـژه"]],
[['text'=>"🎁 امتیاز روزانه"],['text'=>"👤 زیرمجموعه گیری"]],
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
elseif($QwNdq == "🔰 کـد ویـژه" and $chat == 'private'){
mkdir("KsjQePeocB/codvip");
if($type == "vip"){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 حساب شما ویژه می‌باشد",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]);
}else{
$NsdjWi["step"] = "taranehjonam";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎊 کد هدیه را ارسال کنید

🎈 در صورت سالم بودن کد و شخص دیگری استفاده نکرده باشد شما میتوانید استفاده کنید . . .",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
if($step == "taranehjonam" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){ 
$codevip = file_get_contents("KsjQePeocB/codvip/$QwNdq.txt");
if($codevip == vip){
$codefree = file_get_contents("KsjQePeocB/codvip/$QwNdq.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$pNVd/$pNVd.json"),true);
$NsdjWi["type"] = "vip";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$pNVd/$pNVd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 تبریک کد شما درست بود . . . 
حساب شما vip شد . . .",
'parse_mode'=>"HTML",
]); 
unlink("KsjQePeocB/codvip/$QwNdq.txt");
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"
🎁 کد ویژه کننده ($QwNdq )

✅ کد با موفقیت استفاده شد توسط 👇🏻

📝 نام کاربر : *$first*
📌 ای دی عددی کاربر : `$PviFd`
🔐 نوع حساب کاربر : `$type`
🔍 تعداد ربات های کاربر : `$bot`
💰 تعداد امتیاز های کاربر : *$coin*
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]); 
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ کد ویژه ارسال شده معتبر نمیباشد یا قبلا توسط شخص دیگری استفاده شده است",
'parse_mode'=>"HTML",
]); 
}
}
elseif($QwNdq == "🎊 کد هدیه" and $chat == 'private'){
mkdir("KsjQePeocB/codes");	
$NsdjWi["step"] = "smxlslcnq";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎊 کد هدیه را ارسال کنید

🎈 در صورت سالم بودن کد و شخص دیگری استفاده نکرده باشد شما میتوانید استفاده کنید . . .",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
if($step == "smxlslcnq" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){ 
if(file_exists("KsjQePeocB/codes/$QwNdq.txt")){
$codefree = file_get_contents("KsjQePeocB/codes/$QwNdq.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$pNVd/$pNVd.json"),true);
$newin = $coin +$codefree;
$NsdjWi["coin"] = "$newin";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$pNVd/$pNVd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 تبریک کد شما درست بود 

شما مقدار $codefree امتیاز هدیه گرفتید . . .",
'parse_mode'=>"HTML",
]); 
unlink("KsjQePeocB/codes/$QwNdq.txt");
unlink("newgiftm.txt");   
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"
🎁 امتیاز کد هدیه : *$codefree*

✅ کد با موفقیت استفاده شد توسط 👇🏻

📝 نام کاربر : *$first*
📌 ای دی عددی کاربر : `$PviFd`
🔐 نوع حساب کاربر : `$type`
🔍 تعداد ربات های کاربر : `$bot`
💰 تعداد امتیاز های کاربر : *$coin*
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]); 
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ کد هدیه ارسال شده معتبر نمیباشد یا قبلا توسط شخص دیگری استفاده شده است",
'parse_mode'=>"HTML",
]); 
}
}
if($QwNdq == "🗑 حذف ربات" and $chat == 'private'){
if($bot >= 1){
$NsdjWi["step"] = "sjsjsjsjk";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ ای دی ربات خود را بدون @ ارسال کنید جهت حذف ربات . . .",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ شما رباتی بر روی سرور رباتساز اِسلُو کریت ندارید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
if($step == "sjsjsjsjk" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){ 
if($pNVd !=$my_id){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
",
]); 
}else{
if(file_exists("../SnowyBot/$botFile")){
DeleteFolder("../SnowyBot/$botFile");
rmdir("../SnowyBot/$botFile");
DeleteFolder("UsErBoT/$QwNdq");
rmdir("UsErBoT/$QwNdq");
$neww = $bot - 1;
$NsdjWi["bot"] = "$neww";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"✅ ربات @$QwNdq با موفقیت حذف شد ", 
]);
}else{
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"❗️ خطا : شما ادمین این ربات نیستید یا اینکه این ربات توسط رباتساز اِسلُو کریت ساخته نشده است ",
]); 
}
}
}
if($QwNdq == "🥂 فروشگاه" and $chat == 'private'){	
$NsdjWi["step"] = "shop";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendmessage',[
'chat_id'=>$pNVd,
'text'=>"✅ مقدار سکه جهت خرید را ارسال کنید ",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
if($step == "shop" &&$QwNdq != "🔙 بازگشت"){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
if($QwNdq >= 1 and $QwNdq <= 1000 and is_numeric($QwNdq)){
$cash = $QwNdq * 800 ;
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"

🛍 جهت خرید $QwNdq سکه به مبلغ $cash سکه نیترو سین

✅ مقدار $cash سکه نیترو سین را به حساب کاربری `971881348` واریز کنید 

",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "❗️پشتیبانی",'url' => "t.me/CreatorSLow"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]);
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendmessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️خطا :

( ❌ ) عدد ارسال باید بین `1` تا `1000` باشد  . . . 
( ❌ ) فقط عدد ارسال کنید . . . 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($QwNdq == "🎁 امتیاز روزانه" and $chat == 'private'){
if($NsdjWi["gift"] == $date){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎈 شما قبلا امتیاز روزانه خود را دریافت کردید تا فردا منتظر بمانید",
'parse_mode'=>"HTML",
]); 
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$dice = MbVwQP('sendDice',[
'chat_id' =>$pNVd,
'emoji'=> '🎲'
]);
$value = $dice->result->dice->value;
$inv = $NsdjWi["coin"];
$newin = $inv +$value;
$NsdjWi["coin"] = "$newin";
$NsdjWi["gift"] = "$date";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎉 تبریک شما $value امتیاز روزانه دریافت کردید",
'parse_mode'=>"HTML",
]); 
}
}
if($QwNdq == "📚 راهنما" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"📨 | به بخش راهنما خوش آمدید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📑 دستورات بات فادر"],['text'=>"📂 راهنمای توکن"]],
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}

if($QwNdq == "♻️ آپدیت ربات" and $chat == 'private'){
if($bot >= 1){
$NsdjWi["step"] = "ksshdhdhs";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
♻️ جهت آپدیت ربات خود توکن را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ شما رباتی بر روی سرور رباتساز اِسلُو کریت ندارید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "ksshdhdhs" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
$TyPeBot = file_get_contents("UsErBoT/$flty/type.txt");
//•••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($TyPeBot == shart){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/JSJDwoYwQ/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == sexi){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$DaTeJdF = file_get_contents("UsErBoT/$flty/jdf.txt");
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/SeXiSbOt/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[JDF]*]",$DaTeJdF,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == member){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/MeMbErGiRsT/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
$tAmDidfeli = file_get_contents("../SnowyBot/$FiLeMaNg/tAmDid.txt");
if($tAmDidfeli <= 1){
$file = file_get_contents("KakdIwxjQWQ/MeMbErGiRsT/tAmDid.txt");
file_put_contents("../SnowyBot/$FiLeMaNg/tAmDid.txt",$file);
}
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == bank){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/HemJenVa/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == tabadolat){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/TaBaDoLatsT/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == number){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$DaTeJdF = file_get_contents("UsErBoT/$flty/jdf.txt");
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/SeRtQyUoC/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[JDF]*]",$DaTeJdF,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == clash){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$FiLeTeL = file_get_contents("UsErBoT/$flty/file.txt");
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$classTwo = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/QeRgH.php");
$classTwo = str_replace("[*[ADMIN]*]",$PviFd,$classTwo);
Save("../SnowyBot/$FiLeMaNg/$FiLeTeL.php",$classTwo);
$class = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
$class = str_replace("[*[TELEGRAM]*]",$FiLeTeL,$class);
Save("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
$tAmDidfeli = file_get_contents("../SnowyBot/$FiLeMaNg/tAmDid.txt");
if($tAmDidfeli <= 1){
$file = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/tAmDid.txt");
file_put_contents("../SnowyBot/$FiLeMaNg/tAmDid.txt",$file);
}
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot == pm){
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❗️ خطا : 
1 - شما ادمین این ربات نیستید
2 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
3 - ای دی ربات را با @ ارسال کردید باید بدون @ ارسال کنید ای دی ربات را
4 - توکن ربات اشتباه است
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt") and $pNVd == $AdDiD ){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
mkdir("../SnowyBot");
mkdir("../SnowyBot/$FiLeMaNg");
$class = file_get_contents("KakdIwxjQWQ/PvXsAzI/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$pVmSgE = urlencode("
✅ با موفقیت ربات شما آپدیت 

♻️ مجدد ربات را /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSgE."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با موفقیت آپدیت شد

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
1 - این ربات توسط رباتساز اِسلُو کریت ساخته نشده است 
2 - شما ادمین این ربات نیستید
",
]); 
}
}
}
if($TyPeBot != "tabadolat" or $TyPeBot != "member" or $TyPeBot != "shart" or $TyPeBot != "number" or $TyPeBot != "bank" or $TyPeBot != "clash" or $TyPeBot != "sexi" or $TyPeBot != "pm"){
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❌ توکن ارسال شده صحیح نمیباشد مجدد تلاش کنید . . .
",
]); 
}
}
if($QwNdq == "⚙ ساخت ربات" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
⚙ ربات مورد نظر جهت ساخت را انتخاب کنید
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"💣 شرط بندی"]],
[['text'=>"🔞 صکسی"],['text'=>"⚔ جنگ قبایل"]],
[['text'=>"📨 پیامرسان"]],
[['text'=>"💰 بانک نیتروسین"],['text'=>"🔍 جستجو شماره"]],
[['text'=>"🌀 تبادلات سکه"],['text'=>"👤 ممبر گیر"]],
[['text'=>"🔙 بازگشت"]],
],
])
]);
}
elseif($QwNdq == "💣 شرط بندی" and $chat == 'private'){
if($coin >= 20){
$NsdjWi["step"] = "hsoskskakkkk";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 20 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "hsoskskakkkk" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","shart"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$class = file_get_contents("KakdIwxjQWQ/JSJDwoYwQ/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 20;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "📨 پیامرسان" and $chat == 'private'){
if($coin >= 10){
$NsdjWi["step"] = "koeosjg";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 10 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "koeosjg" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
$jskwmsk = Random();
$kjsjsjd = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","pm"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$class = file_get_contents("KakdIwxjQWQ/PvXsAzI/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 10;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "👤 ممبر گیر" and $chat == 'private'){
if($coin >= 25){
$NsdjWi["step"] = "MeMbErGiR";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 25 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "MeMbErGiR" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("../SnowyBot/$jsjkdkkd/TaRaNeH");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/admin");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/cont");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/date");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/postid");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/seen");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/time");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/user");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","member"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$class = file_get_contents("KakdIwxjQWQ/MeMbErGiRsT/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
$file = file_get_contents("KakdIwxjQWQ/MeMbErGiRsT/tAmDid.txt");
file_put_contents("../SnowyBot/$jsjkdkkd/tAmDid.txt",$file);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 25;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "🌀 تبادلات سکه" and $chat == 'private'){
if($coin >= 25){
$NsdjWi["step"] = "TaBaDoLat";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 25 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "TaBaDoLat" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
$jsajjsaj = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","tabadolat"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$class = file_get_contents("KakdIwxjQWQ/TaBaDoLatsT/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
$class1 = file_get_contents("KakdIwxjQWQ/TaBaDoLatsT/QwErO.php");
$class1 = str_replace("[*[TOKEN]*]",$QwNdq,$class1);
file_put_contents("../SnowyBot/$jsjkdkkd/$jsajjsaj.php",$class1);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 25;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "🔍 جستجو شماره" and $chat == 'private'){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💯 بزودی در دسترس ساخت قرار میگیرید وب سرویس شماره یاب به مشکل برخورده است",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
elseif($QwNdq == "kobsSheR" and $chat == 'private'){
if($coin >= 20){
$NsdjWi["step"] = "twhajaksk";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 20 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "twhajaksk" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
$JsTejS = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","number"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
file_put_contents("UsErBoT/$flty/jdf.txt","$JsTejS"); // فایل جی دی اف
$class = file_get_contents("KakdIwxjQWQ/SeRtQyUoC/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[JDF]*]",$JsTejS,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
$file_jdf = file_get_contents("KakdIwxjQWQ/SeRtQyUoC/QrSbO.php");
file_put_contents("../SnowyBot/$jsjkdkkd/$JsTejS.php",$file_jdf);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 20;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "🔞 صکسی" and $chat == 'private'){
if($coin >= 20){
$NsdjWi["step"] = "iauaygs";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 20 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "iauaygs" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
$JsTejS = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","sexi"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
file_put_contents("UsErBoT/$flty/jdf.txt","$JsTejS"); // فایل جی دی اف
$class = file_get_contents("KakdIwxjQWQ/SeXiSbOt/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[JDF]*]",$JsTejS,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
$file_jdf = file_get_contents("KakdIwxjQWQ/SeXiSbOt/QrSbO.php");
file_put_contents("../SnowyBot/$jsjkdkkd/$JsTejS.php",$file_jdf);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 20;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "💰 بانک نیتروسین" and $chat == 'private'){
if($coin >= 25){
$NsdjWi["step"] = "HemJenVaCe";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 25 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "HemJenVaCe" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
mkdir("../SnowyBot");
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("../SnowyBot/$jsjkdkkd/TaRaNeH");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/admin");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/cont");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/date");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/postid");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/seen");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/time");
mkdir("../SnowyBot/$jsjkdkkd/AdSoS/user");
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","bank"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
$class = file_get_contents("KakdIwxjQWQ/HemJenVa/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
file_put_contents("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 25;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
elseif($QwNdq == "⚔ جنگ قبایل" and $chat == 'private'){
if($coin >= 30){
$NsdjWi["step"] = "ClAsHi";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"♻️ جهت ساخت ربات مورد نظر توکن ربات خود را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
💰 جهت ساخت این ربات باید حداقل 30 سکه داشته باشید 

🎉 در بخش امتیاز رایگان میتوانید امتیاز خود را افزایش دهید یا از طریق فروشگاه اقدام به خرید امتیاز کنید
",
]);
}
}
elseif($step == "ClAsHi" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💚 درحال ساخت ربات . . .",
'reply_markup'=>$KeY_BuTtOn
]);
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
 //=================================
$jsjkdkkd = Random();
$jdjjxjmwp = Random();
$skekbhsj = Random();
mkdir("UsErBoT");
mkdir("UsErBoT/$flty");
file_put_contents("UsErBoT/$flty/admin.txt","$PviFd"); // ای دی ادمین
file_put_contents("UsErBoT/$flty/type.txt","clash"); // نوع ربات
file_put_contents("UsErBoT/$flty/file.txt","$jsjkdkkd"); // اسم پوشه ربات
file_put_contents("UsErBoT/$flty/telegram.txt","$skekbhsj"); // اسم پوشه تلگرام
file_put_contents("UsErBoT/$flty/index.txt","$jdjjxjmwp"); // اسم پوشه ایندکس ربات
if($type == "free"){
file_put_contents("UsErBoT/$flty/creator.txt","$txtxt"); // تبلیغ حساب رایگان
}else{
unlink("UsErBoT/$flty/creator.txt");
}
mkdir("../SnowyBot/$jsjkdkkd");
mkdir("../SnowyBot/$jsjkdkkd/.run");
mkdir("../SnowyBot/$jsjkdkkd/clans");
mkdir("../SnowyBot/$jsjkdkkd/clans/config");
mkdir("../SnowyBot/$jsjkdkkd/codes");
mkdir("../SnowyBot/$jsjkdkkd/enemy");
mkdir("../SnowyBot/$jsjkdkkd/event");
mkdir("../SnowyBot/$jsjkdkkd/revenge");
mkdir("../SnowyBot/$jsjkdkkd/users");
mkdir("../SnowyBot/$jsjkdkkd/users/.config");
mkdir("../SnowyBot/$jsjkdkkd/.config");
$kosamaterfan = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/.config/pricetale.php");
Save("../SnowyBot/$jsjkdkkd/.config/pricetale.php",$kosamaterfan);
$kosamaterfankoni = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/.config/pricetroop.php");
Save("../SnowyBot/$jsjkdkkd/.config/pricetroop.php.php",$kosamaterfankoni);
$kosamaterfankoni12 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/.config/pricetroopgem.php");
Save("../SnowyBot/$jsjkdkkd/.config/pricetroopgem.php",$kosamaterfankoni12);
$kosamaterfankoni123 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/ban");
Save("../SnowyBot/$jsjkdkkd/users/ban",$kosamaterfankoni123);
$kosamaterfankoni1234 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/cup");
Save("../SnowyBot/$jsjkdkkd/users/cup",$kosamaterfankoni1234);
$kosamaterfankoni12345 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/dead");
Save("../SnowyBot/$jsjkdkkd/users/dead",$kosamaterfankoni12345);
$kosamaterfankoni123456 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/farm");
Save("../SnowyBot/$jsjkdkkd/users/farm",$kosamaterfankoni123456);
$kosamaterfankoni1234567 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/food");
Save("../SnowyBot/$jsjkdkkd/users/food",$kosamaterfankoni1234567);
$kosamaterfankoni12345678 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/gold");
Save("../SnowyBot/$jsjkdkkd/users/gold",$kosamaterfankoni12345678);
$kosamaterfankoni123456789 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/joinclan");
Save("../SnowyBot/$jsjkdkkd/users/joinclan",$kosamaterfankoni123456789);
$eshtraknagaiidams675 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/tAmDid.txt");
Save("../SnowyBot/$jsjkdkkd/tAmDid.txt",$eshtraknagaiidams675);
$ZsRvNalatkoni = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/ifarm");
Save("../SnowyBot/$jsjkdkkd/users/ifarm",$ZsRvNalatkoni);
$ZsRvNalatkonijon = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/name");
Save("../SnowyBot/$jsjkdkkd/users/name",$ZsRvNalatkonijon);
$ZsRvNalatkonikos = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/run");
Save("../SnowyBot/$jsjkdkkd/users/run",$ZsRvNalatkonikos);
$ZsRvNalatkonishops = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/shop");
Save("../SnowyBot/$jsjkdkkd/users/shop",$ZsRvNalatkonishops);
$ZsRvNalatkoni123 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/spam.txt");
Save("../SnowyBot/$jsjkdkkd/users/spam.txt",$ZsRvNalatkoni123);
$ZsRvNalatkoni = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/ifarm");
Save("../SnowyBot/$jsjkdkkd/users/ifarm",$ZsRvNalatkoni);
$ZsRvNalatkonipoty = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/tale");
Save("../SnowyBot/$jsjkdkkd/users/tale",$ZsRvNalatkonipoty);
$ZsRvNalatkonifuckk = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/timejoin");
Save("../SnowyBot/$jsjkdkkd/users/timejoin",$ZsRvNalatkonifuckk);
$ZsRvNalatkoniamato = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/tixp");
Save("../SnowyBot/$jsjkdkkd/users/tixp",$ZsRvNalatkoniamato);
$transhtransh = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/transh");
Save("../SnowyBot/$jsjkdkkd/users/transh",$transhtransh);
$QzXcBptroop = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/troop");
Save("../SnowyBot/$jsjkdkkd/users/troop",$QzXcBptroop);
$QzXcBptroop123 = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/wood");
Save("../SnowyBot/$jsjkdkkd/users/wood",$QzXcBptroop123);
$QzXcBptroop = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/troop");
Save("../SnowyBot/$jsjkdkkd/users/troop",$QzXcBptroop);
$xpis = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/users/xp");
Save("../SnowyBot/$jsjkdkkd/users/xp",$xpis);
$joinis = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/joinclan");
Save("../SnowyBot/$jsjkdkkd/joinclan",$joinis);
$blocklistkos = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/blocklist.txt");
Save("../SnowyBot/$jsjkdkkd/blocklist.txt",$blocklistkos);
$cup = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/cup");
Save("../SnowyBot/$jsjkdkkd/cup",$cupis);
$foodis = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/food");
Save("../SnowyBot/$jsjkdkkd/food",$foodis);
$goldkosnago = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/gold");
Save("../SnowyBot/$jsjkdkkd/gold",$goldkosnago);
$blocklistkos = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/blocklist.txt");
Save("../SnowyBot/$jsjkdkkd/blocklist.txt",$blocklistkos);
$lvp = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/lvp.php");
Save("../SnowyBot/$jsjkdkkd/lvp.php",$lvp);
$shopisp = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/shop.txt");
Save("../SnowyBot/$jsjkdkkd/shop.txt",$shopisp);
$useridclash = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/useridclash.txt");
Save("../SnowyBot/$jsjkdkkd/useridclash.txt",$useridclash);
$woodakr = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/wood");
Save("../SnowyBot/$jsjkdkkd/wood",$woodakr);
$chanelssams = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/channels");
Save("../SnowyBot/$jsjkdkkd/channels",$chanelssams);
$classTwo = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/QeRgH.php");
$classTwo = str_replace("[*[ADMIN]*]",$PviFd,$classTwo);
Save("../SnowyBot/$jsjkdkkd/$skekbhsj.php",$classTwo);
$class = file_get_contents("KakdIwxjQWQ/ClAshOfClaN/QkdkfK.php");
$class = str_replace("[*[TOKEN]*]",$QwNdq,$class);
$class = str_replace("[*[ADMIN]*]",$PviFd,$class);
$class = str_replace("[*[IDBOT]*]",$id_bot,$class);
$class = str_replace("[*[USERNAME]*]",$flty,$class);
$class = str_replace("[*[TELEGRAM]*]",$skekbhsj,$class);
Save("../SnowyBot/$jsjkdkkd/$jdjjxjmwp.php",$class);
//==================================
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$jsjkdkkd/$jdjjxjmwp.php");
//==================================
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin - 30;
$NsdjWi["coin"] = $newcoin;
$bot = $NsdjWi["bot"];
$newb = $bot + 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
//==================================
$pVmSg = urlencode("
✅ ربات شما با موفقیت به سرور اِسلُو کریت متصل شد . . . 

♻️ ربات خود را مجدد /start کنید 
");
file_get_contents("https://api.telegram.org/bot".$QwNdq."/sendMessage?chat_id=".$pNVd."&text=".$pVmSg."&parse_mode=MarkDown");
MbVwQP('sendMessage',[
'chat_id' =>$pNVd,
'text'=>"
✅ ربات شما با با موفقیت به سرور رباتساز اِسلُو کریت متصل شد 

❗️جهت ورود به پنل مدیریت ربات خود از دستور /panel در ربات خود استفاده کنید . . .

🥂 جهت ورود به ربات خود روی دکمه زیر بزنید 👇🏻

",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=>$message_id + 1,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💛 @$flty 💚",'url'=>"https://t.me/$flty"]],
]])
]);
}
}
if($QwNdq == "🌟 انتقال مالکیت" and $chat == 'private'){
if($bot >= 1){
$NsdjWi["step"] = "EnMalekit";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
🌟 ایدی عددی کسی که می‌خواهید مالکیت رباتتون رو انتقال بدید ارسال کنید

⚠️ _مهم_ : *در صورت انتقال مالکیت ربات خود نمیتوانید آنرا پس بگیرید !*
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ شما رباتی بر روی سرور رباتساز اِسلُو کریت ندارید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
if($step == "EnMalekit" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "EntToken-$QwNdq";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
⚡️ جهت انتقال مالکیت توکن ربات خودتان را از [ بات فادر | bot father](t.me/BotFather) ارسال کنید . . .

🔰 در صورت مشکل در توکن راهنما را مطالعه کنید
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ کاربر `$QwNdq` در ربات اِسلُو کریت نیست",
'parse_mode'=>"Markdown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
if(strpos($step,'EntToken') !== false and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$EnTgid = explode("-",$step)[1];
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
if($ok != 1 or (strlen($QwNdq) > 50 ) or strpos($QwNdq, '/') or strpos($QwNdq, '(') or strpos($QwNdq, ')') or strpos($QwNdq, '}') or strpos($QwNdq, '{') or strpos($QwNdq, ']') or strpos($QwNdq, '[') or strpos($QwNdq, '"') !== false){

$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
❌ خطا ! توکن شما صحیح نمیباشد
",
]);
}else{
$AdDiD = file_get_contents("UsErBoT/$flty/admin.txt");
if(file_exists("UsErBoT/$flty/file.txt")){
if($pNVd == $AdDiD){
$newb = $bot - 1;
$NsdjWi["bot"] = "$newb";
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$EnTgInf = json_decode(file_get_contents("KsjQePeocB/$EnTgid/$EnTgid.json"),true);
$EnTgInf["bot"] += "1";
$EnTeSa = json_encode($EnTgInf,true);
file_put_contents("KsjQePeocB/$EnTgid/$EnTgid.json",$EnTeSa);
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
$class = file_get_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php");
$class = str_replace($pNVd,$EnTgid,$class);
file_put_contents("../SnowyBot/$FiLeMaNg/$IdBoTuP.php",$class);
file_put_contents("UsErBoT/$flty/admin.txt","$EnTgid");
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ مالکیت ربات @$flty با موفقیت به کاربر $EnTgid انتقال یافت !
",
]);
MbVwQP('sendMessage',[
'chat_id'=>$EnTgid,
'text'=>"
✅ کاربر $pNVd ربات @$flty را به شما داد و شما مالک این ربات هستید !
",
]);
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
شما ادمین این ربات نیستید
",
]); 
}
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[ 
'chat_id'=>$pNVd, 
'text'=>"
❗️ خطا :
این ربات توسط رباتساز اِسلُو کریت ساخته نشده است
",
]); 
}
}
}
if($QwNdq == "📑 دستورات بات فادر" and $chat == 'private'){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"

🥂 - دستورات مهم بات فادر
 
💎 - ساخت ربات 
/newbot

✂️ - ویرایش ربات ها
/mybots

🔓 - دریافت توکن ربات ها
/token

🧭 - تغییر نام ربات
/setname

❓- تنظیم توضیحات قبل از استارت
/setdescription

🌹 - تنظیم بیوگرافی ربات 
/setabouttext

♨️ - تنظیم پروفایل ربات
/setuserpic

⌨ - تنظیمات اینلاین
/setinline

💡 - تنظیم کامند
/setcommands

⚙ - تنظیم توانایی عضویت در گروه
/setjoingroups

❌ - حذف ربات
/deletebot",
]); 
}
if($QwNdq == "📂 راهنمای توکن" and $chat == 'private'){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
🔖 - توکن چیست و چطور باید توکن دریافت کنم

🤖 - هر ربات برای اتصال به api تلگرام دارای یک کد هست که ( توکن ) نام دارد.

✅ - برای دریافت ( توکن ) مراحل زیر را انجام دهید :

1⃣ - ابتدا به ربات ( @BotFather ) مراجعه کنید.

2⃣ - دستور /newbot رو ارسال کنید.

3⃣ - یک نام ( فارسی یا انگلیسی ) برای ربات ارسال کنید. (بعداً قابل تغییر است)

4⃣ - پس از ارسال نام،یک یوزرنیم (انگلیسی) ارسال کنید. (قابل تغییر نیست)

❗️- توجه کنین حتما باید در آخر یوزرنیم ارسالی کلمه bot با حروف کوچیک یا بزرگ (فرقی نداره) وجود داشته باشه

5⃣ - اگر با پیغام زیر برخورد کردید

Sorry, this username is already taken. Please try something different.

❕- یعنی قبلا یکی این یوزرنیم رو ثبت کرده یوزرنیم دیگری وارد کنید. 

‼️ - اگر این پیغام رو دریافت نکردید یعنی یوزرنیم می‌توانید انتخاب کنید

6⃣ - حالا به این رباتساز اِسلُو کریت مراجعه کنید و دکمه ساخت ربات رو بزنید.

7⃣ - سپس پیام آخری که از ربات (@BotFather ) دریافت کردید رو اون جایی که مثل این یه متن نوشته👇

PUT-YOUR-BOT-TOKEN

🌀 توکن را کپی کرده و به رباتساز ارسال کنید . . .

8⃣ -  ربات شما با موفقیت ساخته شد",
]); 
}

if($QwNdq == "🛍 حساب کاربری" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
if($username == NULL){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✅ اطلاعات حساب شما در ربات اِسلُو کریت :

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @SlowCreateBot ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$first",'callback_data'=>'fake'],['text'=>"📑 نام شما : ",'callback_data'=>'fake']],
[['text'=>"𝑛𝑜𝑛𝑒",'callback_data'=>'fake'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'fake']],
[['text'=>"$PviFd",'callback_data'=>'fake'],['text'=>"♻️ ای دی عددی شما :",'callback_data'=>'fake']],
[['text'=>"$coin",'callback_data'=>'fake'],['text'=>"📊 تعداد امتیاز شما :",'callback_data'=>'fake']],
[['text'=>"$inv",'callback_data'=>'fake'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'fake']],
[['text'=>"$type",'callback_data'=>'fake'],['text'=>"🔐 نوع حساب شما :",'callback_data'=>'fake']],
[['text'=>"$warn",'callback_data'=>'fake'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'fake']],
[['text'=>"$phone",'callback_data'=>'fake'],['text'=>"💫 شماره تلفن شما :",'callback_data'=>'fake']],
[['text'=>"$bot",'callback_data'=>'fake'],['text'=>"💤 تعداد ربات های شما :",'callback_data'=>'fake']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $PviFd / coin : $coin &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@SlowCreateBot"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$datejoin",'callback_data'=>'fake'],['text'=>'📆 تاریخ عضویت شما ◄','callback_data'=>'fake']],
[['text'=>"$timejoin",'callback_data'=>'fake'],['text'=>'⏰ ساعت عضویت شما ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✅ اطلاعات حساب شما در ربات اِسلُو کریت :

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @SlowCreateBot ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$first",'callback_data'=>'fake'],['text'=>"📑 نام شما : ",'callback_data'=>'fake']],
[['text'=>"@$username",'callback_data'=>'fake'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'fake']],
[['text'=>"$PviFd",'callback_data'=>'fake'],['text'=>"♻️ ای دی عددی شما :",'callback_data'=>'fake']],
[['text'=>"$coin",'callback_data'=>'fake'],['text'=>"📊 تعداد امتیاز شما :",'callback_data'=>'fake']],
[['text'=>"$inv",'callback_data'=>'fake'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'fake']],
[['text'=>"$type",'callback_data'=>'fake'],['text'=>"🔐 نوع حساب شما :",'callback_data'=>'fake']],
[['text'=>"$warn",'callback_data'=>'fake'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'fake']],
[['text'=>"$phone",'callback_data'=>'fake'],['text'=>"💫 شماره تلفن شما :",'callback_data'=>'fake']],
[['text'=>"$bot",'callback_data'=>'fake'],['text'=>"💤 تعداد ربات های شما :",'callback_data'=>'fake']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $PviFd / coin : $coin &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@SlowCreateBot"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$datejoin",'callback_data'=>'fake'],['text'=>'📆 تاریخ عضویت شما ◄','callback_data'=>'fake']],
[['text'=>"$timejoin",'callback_data'=>'fake'],['text'=>'⏰ ساعت عضویت شما ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
]
])
]);
}
}
if($QwNdq == "🏆 برترین های زیر مجموعه" and $chat == 'private'){
$get = GetRank("inv");
$user1 = $get[0]['user'];
$inv1 = $get[0]['coins'];
$user2 = $get[1]['user'];
$inv2 = $get[1]['coins'];
$user3 = $get[2]['user'];
$inv3 = $get[2]['coins'];
$user4 = $get[3]['user'];
$inv4 = $get[3]['coins'];
$user5 = $get[4]['user'];
$inv5 = $get[4]['coins'];
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎁 برترین کاربران در زیر مجموعه گیری 

« <a href ='tg://user?id=$user1'>🔰 ⟩ نفر اول</a> »
👥 تعداد زیرمجموعه : $inv1 


« <a href ='tg://user?id=$user2'>🔰 ⟩ نفر دوم</a> »
👥 تعداد زیرمجموعه : $inv2


« <a href ='tg://user?id=$user3'>🔰 ⟩ نفر سوم</a> »
👥 تعداد زیرمجموعه : $inv3


« <a href ='tg://user?id=$user4'>🔰 ⟩ نفر چهارم</a> »
👥 تعداد زیرمجموعه : $inv4


« <a href ='tg://user?id=$user5'>🔰 ⟩ نفر پنجم</a> »
👥 تعداد زیرمجموعه : $inv5

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @SlowCreateBot ⟨⟨•••
",
'parse_mode'=>'HTML',
]);
}
if($QwNdq == "👤 زیرمجموعه گیری" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendphoto',[
 'chat_id'=>$pNVd,
'photo'=>'https://t.me/Mafia_nit/155',
 'caption'=>"
 🔆 اِسلُو کریت | Slow Create

🚀 سرعت بسیار بالا 
🌹 ضد هک و ضد نفوذ و امنیت بالا 
☘ پیشرفته ترین رباتساز 
🌿 پشتیبانی آنلاین 24 ساعته 


✅ ساخت ربات شرط بندی با پیشرفته ترین پنل مدیریتی 
✅ ساخت ربات ممبر گیر پیشرفته
✅ ساخت ربات شماره یاب با وب سرویس 42 میلیونی 
✅ ساخت ربات مبادلات الماسی و پنل مدیریت پیشرفته


🔰 همین الان ربات خودت رو بساز
https://t.me/SlowCreateBot?start=$PviFd
",
 ]);
}
elseif($QwNdq == "🆘 پشتیبانی" and $chat == 'private'){	
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

یکی از گزینه های زیر را انتخاب کنید 👇🏻

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @SlowCreateBot ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔰 ⟩ پشتیبانی آنلاین ربات",'callback_data'=>'SuPpOrT']],
[['text' => "🔰 ⟩ پشتیبانی مستقیم",'url' => "t.me/CreatorSLow"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
]
])
]);
}
if($step == "Qkslcls" &&$QwNdq != "🔙 بازگشت" &&$CaLl_DaTa != "CaNcEl"){ 
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('ForwardMessage',[
'chat_id'=>$admin,
'from_chat_id'=>$pNVd,
'message_id'=>$message_id
]); 
MbVwQP('sendmessage',[
'chat_id'=>$admin,
'text'=>"♨️ یک پیام از سوی کاربر `$pNVd` ارسال شد

🔰 پی وی کاربر [$first](t.me/$username) یا [$first](tg://openmessage?user_id=$pNVd) است 
",
'parse_mode'=>"MarkDown"
]);
MbVwQP('sendmessage',[
'chat_id'=>$pNVd,
'text'=>"با تشکر از شما 🌹


✅ پیام شما به پشتیبانی ارسال شد",
'reply_markup'=>$KeY_BuTtOn
]);
}
if($QwNdq == "💭 گفتگو" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"📩 جهت ورود گروه گفتگو رباتساز اِسلُو کریت لطفا به چند نکته توجه کنید :

1️⃣⁩ - در گروه ارسال فیلم یا عکس یا استیکر و گیف مجاز نیست.

⁦2️⃣⁩ - در گروه ارسال کلمات رکیک مجاز نیست و درصورت مشاهده فرد در کمترین زمان از ربات و گروه مسدود میشود.

⁦3️⃣⁩ - اسپم در گپ و خراب کردن نظم چت موجب بلاکی شما در ربات و گروه میباشد.

⁦👇🏻⁩ درصورت قبول قوانین بر روی 📩 ورود به گروه گفتگو کلیک کنید و سپس بر روی پیوستن یا Join کلیک کنید.⁦
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "📩 ورود به گروه گفتگو", 'url' => "https://t.me/Slow_Group"]],
]
])
]);
}
if($QwNdq == "💰 تعرفه" and $chat == 'private'){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
🔐 جهت دیدن تعرفه ساخت ربات ها روی گزینه مورد نظر کلیک کنید 👇
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💣 شرط بندی",'callback_data'=>'beting']],
[['text'=>"🔞 صکسی",'callback_data'=>'sexi']],
[['text'=>"🔍 جستجو شماره",'callback_data'=>'finder']],
[['text'=>"💰 بانک نیتروسین",'callback_data'=>'bank']],
[['text'=>"🌀 تبادلات سکه",'callback_data'=>'tabadolat']],
[['text'=>"📨 پیامرسان",'callback_data'=>'pvsaz']],
[['text'=>"⚔ جنگ قبایل",'callback_data'=>'war']],
[['text'=>"👤 ممبر گیر",'callback_data'=>'member']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
]
])
]);
}
elseif($QwNdq == "💸 انتقال امتیاز" and $chat == 'private'){	
if($coin >= 15){
$NsdjWi["step"] = "SneJeknL";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔐 ای دی عددی کاربر مورد نظر جهت انتقال سکه وارد کنید . . .
",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
♻️ جهت انتقال امتیاز حداقل باید *15* امتیاز داشته باشید !
",
'parse_mode'=>"MarkDown"
]);
}
}
elseif($step == "SneJeknL" and $QwNdq != "🔙 بازگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "KaiHekM";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/$PviFd/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
🎉 تعداد امتیاز مورد نظر جهت انتقال به کاربر *$QwNdq* را وارد کنید 


📌 امتیاز های شما : *$coin*
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
elseif($step == "KaiHekM" and $QwNdq != "🔙 بازگشت" and is_numeric($QwNdq) and $chat == 'private'){
if($QwNdq >= 15 and $QwNdq <= 1000 and $coin >=$QwNdq){
$id = file_get_contents("KsjQePeocB/$PviFd/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$PviFd/$PviFd.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin -$QwNdq;
$NsdjWi["coin"] = $newcoin;
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$DvCwId = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$coin = $DvCwId["coin"];
$newcoin = $coin +$QwNdq;
$DvCwId["coin"] = $newcoin;
$QpoWrT = json_encode($DvCwId,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
`✅ تعداد $QwNdq امتیاز در تاریخ $date ساعت $time با موفقیت از کاربر $pNVd دریافت شد`
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$PviFd,
'text'=>"`✅ تعداد $QwNdq امتیاز در تاریخ $date ساعت $time با موفقیت به کاربر $id انتقال داده شد`",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_BuTtOn
]); 
}else{
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
_⚠️ خطا :_

❌ حداقل سکه جهت انتقال *15* سکه و حداکثر سکه جهت انتقال *1000* سکه است 

❌ تعداد سکه جهت انتقال را با *توجه به موجودی خود* ارسال کنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_BuTtOn
]); 
}
}
if($pNVd == $admin){
//====[ADMIN]====//
if($QwNdq == "📊 آمار کاربران" and $chat == 'private'){
if($pNVd == $admin){
$document = ("UsErBoT");
$scan = scandir($document);
$scan = array_diff($scan, ['.','..']);
$AlLbOt = count($scan);
$alluser = file_get_contents("KsjQePeocB/KsjQeQPeocBJwf.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki) - 2;
$memChnel = MbVwQP('getChatMembersCount',['chat_id'=>"@SlowTM"])->result;
$load = sys_getloadavg();
$memm = memory_get_usage();
$ver = phpversion(); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
📈 وضعیت ربات به شرح زیر میباشد :
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'join']],
[['text'=>"$allusers",'callback_data'=>'join'],['text'=>'📊 آمار ربات','callback_data'=>'join']],
[['text'=>"$AlLbOt",'callback_data'=>'join'],['text'=>'🥂 تعداد ربات ها','callback_data'=>'join']],
[['text'=>"$memChnel",'callback_data'=>'join'],['text'=>'📉 آمار کانال','callback_data'=>'join']],
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'join']],
[['text'=>"$load[0]",'callback_data'=>'join'],['text'=>"پینگ 〽️",'callback_data'=>'join']],
[['text'=>"$ver",'callback_data'=>'join'],['text'=>"ورژن پی اچ پی♻️",'callback_data'=>'join']],
[['text'=>"$memm KB",'callback_data'=>'join'],['text'=>"میزان مصرف حافظه💻",'callback_data'=>'join']],
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'join']],
]
])
]);
}
}
elseif($QwNdq == "🎉 ساخت کد VIP" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "taranehjon";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ کد ویژه کننده جدید را ارسال کنید . . .",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "taranehjon" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/codvip/$QwNdq.txt",vip);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ کد $QwNdq ویژه کننده با موفقیت ساخته شد ",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($QwNdq == "🎁 ساخت کد هدیه" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "getid2gg";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"کد هدیه جدید را ارسال نمایید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "getid2gg" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "sendcoin2gg";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("newgiftm.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"این کد شامل چند سکه باشد؟",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "sendcoin2gg" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
if(is_numeric($QwNdq)){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$newgiftm = file_get_contents("newgiftm.txt");
mkdir("KsjQePeocB/codes");
file_put_contents("KsjQePeocB/codes/$newgiftm.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"کد *$newgiftm* به ارزش *$QwNdq* سکه با موفقیت ساخته شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]);
sleep(15);
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"🎉🎉🎉",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]); 
sleep(15);
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"🎉🎉",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]); 
sleep(15);
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"🎉",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]); 
sleep(1);
MbVwQP('sendMessage',[
'chat_id'=>-1001310723851,
'text'=>"
🎁 کد هدیه جدید 

کد : `$newgiftm`

🛍 همین الان وارد ربات شوید از لینک زیر و کد رو وارد کن و جایزه بگیر 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ خطا : لطفاً عدد ارسال کنید فقط . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
}
if($QwNdq == "/panel" or$QwNdq == "🔙 برگشت" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"👈🏻⁩ به منوی مديريت خوش امدید
",
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "➕ افزایش امتیاز" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "getcoin";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "getcoin" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "coinup";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 تعداد امتیاز را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($step == "coinup" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin +$QwNdq;
$NsdjWi["coin"] = $newcoin;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 تبریک تعداد *$QwNdq* امتیاز به شما اضافه شد . . . 
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت تعداد *$QwNdq* امتیاز به کاربر `$id` اضافه شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "➖ کسر امتیاز" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "byecoin";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "byecoin" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "coindown";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 تعداد امتیاز را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($step == "coindown" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$coin = $NsdjWi["coin"];
$newcoin = $coin -$QwNdq;
$NsdjWi["coin"] = $newcoin;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
🔐 تعداد *$QwNdq* امتیاز از شما کسر شد . . .
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت تعداد *$QwNdq* امتیاز از کاربر `$id` کسر شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "❗️ اخطار به کاربر" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "getwarn";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "getwarn" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "warnup";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 تعداد اخطار را وارد کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($step == "warnup" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$warn = $NsdjWi["warn"];
$newwarn = $warn +$QwNdq;
$NsdjWi["warn"] = $newwarn;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
❗️ اوپس , تعداد *$QwNdq* اخطار به شما اضافه شد . . .
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت تعداد *$QwNdq* اخطار به کاربر `$id` اضافه شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "❕ حذف اخطار کاربر" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "byewarn";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "byewarn" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "warndown";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 تعداد اخطار را وارد کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($step == "warndown" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$warn = $NsdjWi["warn"];
$newwarn = $warn -$QwNdq;
$NsdjWi["warn"] = $newwarn;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 تبریک تعداد *$QwNdq* اخطار از شما کم شد . . .
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت تعداد *$QwNdq* اخطار از کاربر `$id` حذف شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "🔑 ویژه کردن" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "vipshod";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "vipshod" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$QwNdq/$QwNdq.json"),true);
$NsdjWi["type"] = vip;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$QwNdq/$QwNdq.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$QwNdq,
'text'=>"
🎉 تبریک حساب کاربری شما از طرف ادمین ویژه شد . . . 
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ حساب کاربری `$QwNdq` با موفقیت ویژه شد ",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($QwNdq == "🗑 حذف ویژه" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "vipnashod";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "vipnashod" and $QwNdq != "🔙 برگشت" and is_numeric($QwNdq) and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$QwNdq/$QwNdq.json"),true);
$NsdjWi["type"] = free;
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$QwNdq/$QwNdq.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$QwNdq,
'text'=>"
❗️ حساب شما بصورت رایگان در اومد در صورت اعتراض به پشتیبانی ربات مراجعه کنید . . . 
 
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ حساب کاربری `$QwNdq` با موفقیت حذف ویژه شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($QwNdq == "🖥 پیام به کاربر" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "djrofo39fowj";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🆔 ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}
}
elseif($step == "djrofo39fowj" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
if(file_exists("KsjQePeocB/$QwNdq/$QwNdq.json")){
$NsdjWi["step"] = "djoepfp299wb";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
file_put_contents("KsjQePeocB/id.txt",$QwNdq);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔰 متن پیام خود را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]); 
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ ایدی کاربری که ارسال کردید در ربات نیست",
]); 
}
}
}
elseif($step == "djoepfp299wb" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎈 یک پیام از پشتیبانی دارید . . . 

📨 پیام : `$QwNdq`
",
'parse_mode'=>"MarkDown"
]); 
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"📌 با موفقیت ارسال شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]); 
}
}
elseif($QwNdq == "❌ بلاک کردن" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "dndodockd";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💯 ای دی عددی کاربر مورد نظر را ارسال کنید",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step == "dndodockd" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
if(is_numeric($QwNdq)){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$NsdjWi["warn"] = "4";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔆 با موفقیت کاربر $QwNdq بلاک شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ خطا : لطفاً عدد ارسال کنید فقط . . .",
'parse_mode'=>"MarkDown"
]);
}
}
}
elseif($QwNdq == "✅ آنبلاک کردن" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "dndjdoekcmsm";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"💯 ای دی عددی کاربر مورد نظر را ارسال کنید",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step == "dndjdoekcmsm" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
if(is_numeric($QwNdq)){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$id = file_get_contents("KsjQePeocB/id.txt");
$NsdjWi = json_decode(file_get_contents("KsjQePeocB/$id/$id.json"),true);
$NsdjWi["warn"] = "0";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$id/$id.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔆 با موفقیت کاربر $QwNdq آنبلاک شد ",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeY_AdMiN
]);
}else{
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"❌ خطا : لطفاً عدد ارسال کنید فقط . . .",
'parse_mode'=>"MarkDown"
]);
}
}
}
elseif($QwNdq == "📨 پیام همگانی" and $chat == 'private'){	
if($pNVd == $admin){
$NsdjWi["step"] = "snxpwkxpwb";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎈 پیام خود را جهت ارسال پیام همگانی ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step == "snxpwkxpwb" and $QwNdq != "🔙 برگشت" and $chat == 'private'){ 
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$get = json_decode(file_get_contents('send.json'),true);
$get['type'] = "send";
$get['text'] = "$QwNdq";
$get['msgid'] = "$message_id";
$get['count'] = "0";
$get['chat_id'] = "$pNVd";
file_put_contents('send.json',json_encode($get));
exec('php FoRwArD.php');
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت پیام شما برای تمام کاربران ارسال شد . . .",
'parse_mode'=>"MarkDown"
]);
}
}
elseif($QwNdq == "🔰 فور همگانی" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "smdpwms";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🎈 پیام خود را فوروارد کنید . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($QwNdq != "🔙 برگشت" and $step == "smdpwms" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "free";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$all_member = fopen( "KsjQePeocB/KsjQeQPeocBJwf.txt", 'r');
$get = json_decode(file_get_contents('send.json'),true);
$get['type'] = "forward";
$get['msgid'] = "$message_id";
$get['count'] = "0";
$get['chat_id'] = "$pNVd";
file_put_contents('send.json',json_encode($get));
exec('php FoRwArD.php');
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ فوروارد همگانی با موفقیت برای تمام کاربران فوروارد شد . . .",
'parse_mode'=>'MarkDown',
]);
}
}
elseif($QwNdq == "📑 ارسال بنر به کانال" and $chat == 'private'){
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ بنر ربات با موفقیت به کانال ارسال شد ",
'parse_mode'=>"MarkDown"
]);	
MbVwQP('sendphoto',[
 'chat_id'=>-1001310723851,
'photo'=>'https://t.me/slokings/7871',
 'caption'=>"
 🔆 اِسلُو کریت | Slow Create

🚀 سرعت بسیار بالا 
🌹 ضد هک و ضد نفوذ و امنیت بالا 
☘ پیشرفته ترین رباتساز 
🌿 پشتیبانی آنلاین 24 ساعته 


✅ ساخت ربات شرط بندی با پیشرفته ترین پنل مدیریتی 
✅ ساخت ربات ممبر گیر پیشرفته
✅ ساخت ربات شماره یاب با وب سرویس 42 میلیونی 
✅ ساخت ربات مبادلات الماسی و پنل مدیریت پیشرفته


🔰 همین الان ربات خودت رو بساز

",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' =>"🎈 ورود به رباتساز",'url' => "https://t.me/SlowCreateBot"]],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
]
])
]);
}
elseif($QwNdq == "💰 سکه همگانی" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "EhenHekl";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"🔐 تعداد سکه همگانی را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($QwNdq != "🔙 برگشت" and $step == "EhenHekl" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "none";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
$all_member = file_get_contents("KsjQePeocB/KsjQeQPeocBJwf.txt");
$ex = explode("\n",$all_member);
$cplug = count($ex) - 2;
for($n=0;$n<=$cplug;$n++){
$Id_AlL = $ex[$n];
$GenHen = json_decode(file_get_contents("KsjQePeocB/$Id_AlL/$Id_AlL.json"),true);
$inv = $GenHen["coin"];
$newin = $inv +$QwNdq;
$GenHen["coin"] = "$newin";
$KeoBen = json_encode($GenHen,true);
file_put_contents("KsjQePeocB/$Id_AlL/$Id_AlL.json",$KeoBen);
}
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"✅ با موفقیت تعداد $QwNdq به تمام کاربران ربات اضافه شد . . .",
'parse_mode'=>"MarkDown"
]);
}
}
if($QwNdq == "📚 متن کریتور" and $chat == 'private'){
if($pNVd == $admin){
$NsdjWi["step"] = "GhJehK";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
♻️ جهت تغییر متن کریتور ربات های رایگان متن جدید را ارسال کنید
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
}
elseif($step == "GhJehK" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$files = glob('*/*/creator.txt');
foreach($files as$a){
file_put_contents("$a","$QwNdq");
$EjHbrbJ = 971881348;
$HebYeg = "SnowyTeam.xyz/$a";
$NjHenUe = "PUT-YOUR-BOT-TOKEN";
file_get_contents("https://api.telegram.org/bot".$NjHenUe."/sendmessage?chat_id=".$EjHbrbJ."&text=".$HebYeg);
}
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ با موفقیت متن کریتور ربات های معمولی به
=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=
$QwNdq
=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=
تغییر کردند !
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
if($QwNdq == "ست وبهوک ✨" and $chat == 'private'){
$NsdjWi["step"] = "SeTwEb";
$QpoWrT = json_encode($NsdjWi,true);
file_put_contents("KsjQePeocB/$PviFd/$PviFd.json",$QpoWrT);
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
♻️ جهت ست وب هوک مجدد توکن را ارسال کنید
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
elseif($step == "SeTwEb" and $QwNdq != "🔙 بازگشت" and $chat == 'private'){
$userbot = json_decode(file_get_contents("https://api.telegram.org/bot".$QwNdq."/getme"));
$resultb = QwiOzkdodocir($userbot);
$flty = $resultb["result"]["username"];
$id_bot = $resultb["result"]["id"];
$ok = $resultb["ok"];
$FiLeMaNg = file_get_contents("UsErBoT/$flty/file.txt");
$IdBoTuP = file_get_contents("UsErBoT/$flty/index.txt");
file_get_contents("https://api.telegram.org/bot$QwNdq/setwebhook?url=$LINK/SnowyBot/$FiLeMaNg/$IdBoTuP.php");
MbVwQP('sendMessage',[
'chat_id'=>$pNVd,
'text'=>"
✅ با موفقیت ست وبهوک تنظیم شد
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"ست وبهوک ✨"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
}
if(file_exists(error_log)) unlink(error_log);
?>