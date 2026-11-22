<?php
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
error_reporting(0);
http_response_code(200);
if(is_callable('litespeed_finish_request')){
litespeed_finish_request();
}elseif(is_callable('fastcgi_finish_request')){
fastcgi_finish_request();
}
include("jdf.php");
//===[توکن]===//
define('API_KEY',''); // توکن ربات را از @BotFather بگیرید و اینجا بگذارید
//===[امنیت دامنه]===//
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if(!$ok) {
    if(!$ok)
	{
		$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
		$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
		if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec)
		{
			$ok=true;
			}
		}
	}
if(!$ok)
{
	exit(header("location: https://Google.com"));
	}
	//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
//===[فانکشن های لازم]===//
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
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
function Security($text){
$text = mb_strtolower($text);
if(str_contains($text,'api') or str_contains($text,'key')){
return true;
}elseif(str_contains($text,'update') or str_contains($text,'getme')){
return true;
}elseif(str_contains($text,'include') or str_contains($text,'foreach')){
return true;
}elseif(str_contains($text,'input') or str_contains($text,'exec')){
return true;
}elseif(str_contains($text,'json') or str_contains($text,'curl')){
return true;
}elseif(str_contains($text,'php') or str_contains($text,'zip')){
return true;
}elseif(str_contains($text,'bot') or str_contains($text,'function')){
return true;
}elseif(str_contains($text,'telegram') or str_contains($text,'http')){
return true;
}elseif(str_contains($text,'#') or str_contains($text,"'")){
return true;
}elseif(str_contains($text,'$') or str_contains($text,'"')){
return true;
}elseif(str_contains($text,'(') or str_contains($text,')')){
return true;
}elseif(str_contains($text,'%') or str_contains($text,'*')){
return true;
}elseif(str_contains($text,'[') or str_contains($text,']')){
return true;
}else{
return false;
}
}
function Number($string) {
if(isset($string)){
$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
$arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
$ChangePersian = str_replace($persian,range(0, 9),$string);
$ChangeArabic = str_replace($arabic,range(0, 9),$ChangePersian);
if(is_numeric($ChangeArabic) and $ChangeArabic != 0) $ChangeArabic = abs($ChangeArabic);
return $ChangeArabic;
}
}
function Json($data){
if(is_string($data)){
return json_decode($data,true);
}else{
return json_encode($data,true);
}
}
function Save($link,$data){
file_put_contents($link,Json($data));
}
function Font($text){
$font = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$echo = str_replace(range(0,9),$font[array_rand($font)],$text);
return $echo;
}
function SubString($text){
$len = floor(mb_strlen($text) / -2) - 1;
return substr_replace($text,'***',$len,$len + 3);
}
function DeleteAds($adsCode,$ads){
if(isset($ads[$adsCode])){
$search = array_search($adsCode,$ads['Random']);
unset($ads['Random'][$search]);
unset($ads[$adsCode]);
$ads['Random'] = array_values($ads['Random']);
Save("ads.json",$ads);
return true;
}else{
return false;
}
}
function Client($datas=[]){
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,"https://cli.api-bot.site/py/account");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
return json_decode(curl_exec($ch),true);
}
function GetInfo($table,$arrays){
global $pdo;
$rows = [];
foreach($arrays as $key => $value){
$rows[] = $key." = '".$value."'";
}
$row = implode(' AND ',$rows);
$database = $pdo->prepare("SELECT * FROM `$table` WHERE $row LIMIT 1");
$database->execute();
$database->setFetchMode(PDO::FETCH_ASSOC);
return $database->fetch();
}
function GetRanks($file,$count){
global $pdo;
$pdo->exec('SET NAMES utf8');
$code = $pdo->prepare("SELECT * FROM `user` ORDER BY `$file` DESC LIMIT $count");
$code->execute();
foreach($code->fetchAll(PDO::FETCH_ASSOC) as $res){
$i++;
$id = $res['id'];
$user = $res[$file];
$name = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
$list .= Font($i)." ➣ $name | $id | $user\n\n";
}
return $list;
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
$chat_id = $update->message->chat->id;
$text = Number($update->message->text);
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$user_name = $update->message->from->username ?? '𝑛𝑜𝑛𝑒';
$reply_id = $update->message->reply_to_message->from->id;
$reply_name = $update->message->reply_to_message->from->first_name;
$reply_message = $update->message->reply_to_message->message_id;
$newmem = $update->message->new_chat_member->id;
$title_gap = $update->message->chat->title;
}
if(isset($update->callback_query)){
$chat_id = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$from_id = $update->callback_query->from->id;
$first_name = $update->callback_query->from->first_name;
$last_name = $update->callback_query->from->last_name;
$user_name = $update->callback_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
if(isset($update->inline_query)){
$inline = $update->inline_query;
$user_id = $update->inline_query->id;
$query = $update->inline_query->query;
$from_id = $update->inline_query->from->id;
$first_name = $update->inline_query->from->first_name;
$last_name = $update->inline_query->from->last_name;
$user_name = $update->inline_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
//===[ربات]===//
$get = bot('getMe');
$botid = $get->result->id;
$botuser = $get->result->username;
$botname = $get->result->first_name;
//===[امنیت]===//
if(isset($text) and Security($text)) exit();
//===[تیبل ها]===//
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
/*
CREATE TABLE `user` (
	`id` BIGINT PRIMARY KEY,
	`step` TEXT DEFAULT NULL,
	`coin` BIGINT DEFAULT '0',
	`inv` INT DEFAULT '0',
	`harvest` BIGINT DEFAULT '0',
	`shop` BIGINT DEFAULT '0',
	`currency` LONGTEXT DEFAULT NULL,
	`star` TEXT DEFAULT NULL,
	`my` BIGINT DEFAULT '0',
	`vaseteid` BIGINT DEFAULT '0',
	`gapid` BIGINT DEFAULT '0',
	`nitvasete` BIGINT DEFAULT '0',
	`gmail` VARCHAR(320) DEFAULT NULL,
	`phone` VARCHAR(15) NOT NULL,
	`code` VARCHAR(5) NOT NULL,
	`spam` BIGINT DEFAULT '0',
	`time` TIME DEFAULT NULL,
	`date` DATE DEFAULT NULL
	) default charset = utf8mb4;

CREATE TABLE `gaps` (
	`id` BIGINT PRIMARY KEY,
	`count` INT DEFAULT '0',
	`star` TEXT DEFAULT NULL,
	`time` TIME DEFAULT NULL,
	`date` DATE DEFAULT NULL
	) default charset = utf8mb4;

CREATE TABLE `transfer` (
	`hash` LONGTEXT,
	`fromid` BIGINT DEFAULT '0',
	`coin` BIGINT DEFAULT '0',
	`to` LONGTEXT DEFAULT NULL,
	`time` TIME DEFAULT NULL,
	`date` DATE DEFAULT NULL,
	PRIMARY KEY (hash(255))
	) default charset = utf8mb4;
*/
//===[دیتابیس]===//
$server = 'localhost';
$username = 'cli_bot';
$password = 'wxFa4V)a]Oa;';
$db = 'cli_bot';
$pdo = new PDO("mysql:host=$server; dbname=$db; charset=utf8",$username,$password);
$pdo->exec('SET NAMES utf8');
$datas = GetInfo('user',['id'=>$from_id]);
$step = $datas['step'];
$coin = $datas['coin'];
$inv = $datas['inv'];
$harvest = $datas['harvest'];
$shop = $datas['shop'];
$currency = Json($datas['currency']);
$star = $datas['star'];
$timej = $datas['time'];
$datej = str_replace('-','/',$datas['date']);
$ids = $datas['vaseteid'];
$gapid = $datas['gapid'];
$gaps = GetInfo('gaps',['id'=>$chat_id]);
$counts = $gaps['count'];
$starg = $gaps['star'];
$timeg = $gaps['time'];
$dateg = str_replace('-','/',$datas['date']);
$orders = json_decode(file_get_contents("orders.json"),true);
$ads = json_decode(file_get_contents("ads.json"),true) ?: ["Random"=>[]];
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
$type_currency = ['TRX','DOGE','BTC','LTC','BCH','ZEC','DASH','GRS','BNB','ETH','SOL'];
$name_currency = ['ترون','دوج کوین','بیت کوین','لایت کوین','بیت کوین کش','زد کش','دش','گروستل کوین','بایننس کوین','اتریوم','سولانا'];
$wallet_currency = ['TDvUQYU178zG9tmFsNcawW2uYa7ziY64fK','DDwuqvLhSZyY3NFfC97zVPxXYzuXk2Puo8','bc1q7df90ywyg8gelzfpyuw0hv0ue0wnzcmxd8nzf2','ltc1qv39l7nt5xshkvznl06qph2g68krh0p39kq6gzu','qr3l7e57cplpqf93x88t3jf0vyvgevkhrvrtstdfrq','t1ae6EE3jgAygij85MQs6uSeHBhVVGJUxMq','XxoooDFvkDcrLePWNRY8PtcKxoutf1rHJ6','grs1q0fkhakccp3kypac964ahq75dknrvkh0v6yv42c','bnb1tauydqu492np8j0724q0nqanj0mgntgna7rqgd','0xD2dbEDFF6aCE52b3E477f5f4F912eb9cd3aBe330','H5hbDKUGGy8L1Cd42NPmzKusdCqcm8YAVRcPeR6LpJwj'];
$currencys = Json(array_fill_keys($type_currency,0));
$admin = [1139819509,111111117,00000];
$sharj = [5612838263]; /// ایدی شارژ
$reports = [-100]; // ایدی کانال گزارشات
$channel = '@Sourrce_Kade';
$support = 'ESRRAR';
$zircoin = 200;
$zirlink = "https://t.me/VasetChannel/10";
$zirtext = "✅ $botname\n\n🚷 بدون دخالت اشخاص و تماماً خودکار در انجام معاملات\n\n🌐 پوشش دهی از بیشترین نوع معاملات\n\n🔐 امنیت در معاملات با پشتیبانی آنلاین\n\n💯 اولین و معتبر ترین ربات واسطه\n\n💎 معامله بصورت ناشناس با دیگران\n\n👥 معامله در گروه توسط ربات بصورت پیشرفته\n\n\nhttps://t.me/$botuser?start=$from_id";
$date = jdate("Y/n/j");
$time = jdate("H:i:s");
$timer = time();
if($step == "block" and !in_array($from_id,$admin)) exit();
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if($datas['id'] == true){
if(isset($datas['spam']) and $datas['spam'] + 2 > $timer){
if(isset($update->callback_query)){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🍂 لطفاً آهسته تر با ربات کار کنید",
'show_alert'=>true
]);
}elseif(isset($update->message) and $tc == 'private'){
$delete_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🍂 لطفاً آهسته تر با ربات کار کنید
",
'parse_mode'=>'HTML'
])->result->message_id;
sleep(5);
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$message_id
]);
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$delete_id
]);
}
exit();
}else{
$pdo->prepare("UPDATE `user` SET spam = '$timer' WHERE id = '$from_id' LIMIT 1")->execute();
}
}
if($timer - ($update->callback_query->message->date ?? $timer) >= 7200 and !in_array($from_id,$admin)){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✨ متاسفانه این دکمه منقضی شده است",
'show_alert'=>true
]);
exit();
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
$menu = json_encode(['keyboard'=>[
[['text'=>"✅ انجام معامله"]],
[['text'=>"💸 انتقال سکه"],['text'=>"🔐 حساب کاربری"]],
[['text'=>"🌟 تبلیغات"]],
[['text'=>"💰 برداشت"],['text'=>"🛍 شارژ حساب"]],
[['text'=>"🆘 پشتیبانی"],['text'=>"📚 راهنما"]],
],'resize_keyboard'=>true]);

$panel = json_encode(['keyboard'=>[
[['text'=>"📊 آمار"]],
[['text'=>"➖ کسر سکه"],['text'=>"➕ افزایش سکه"]],
[['text'=>"🏆 برترین ها"]],
[['text'=>"📨 پیام همگانی"],['text'=>"💭 فوروارد همگانی"]],
[['text'=>"💷 کسر ارز"],['text'=>"💶 افزایش ارز"]],
[['text'=>"📩 پیام به کاربر"],['text'=>"🔍 اطلاعات کاربر"]],
[['text'=>"🔰 آنبلاک کردن"],['text'=>"❌ بلاک کردن"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$advertising = json_encode(['keyboard'=>[
[['text'=>"🔍 پیدا کردن تبلیغ"]],
[['text'=>"🎈 مشاهده تبلیغات"],['text'=>"✔️ ثبت تبلیغات"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$vasete = json_encode(['keyboard'=>[
[['text'=>"⚡️ تنظیم نوع معامله"]],
[['text'=>"🚫 گزارش کاربر"],['text'=>"🔰 اطلاعات کاربر"]],
[['text'=>"❌ خروج از معامله"]],
],'resize_keyboard'=>true]);

$back = json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$confirmation = json_encode(['keyboard'=>[
[['text'=>"📧 تاییده با جیمیل"],['text'=>"❄️ تاییده با شماره تلفن"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$account = json_encode(['inline_keyboard'=>[
[['text'=>"💷 ارز های من",'callback_data'=>"mycurrency"]],
[['text'=>"♻️ آپدیت",'callback_data'=>"update"],['text'=>"❌ بستن پنل",'callback_data'=>"close"]],
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share"],['text'=>"🎊 زیر مجموعه گیری",'callback_data'=>"invite"]],
[['text'=>"✔️ تاییده گرفتن",'callback_data'=>"gettayd"]],
]]);

$help = json_encode(['inline_keyboard'=>[
[['text'=>"❓ واسطه داخل گروه چطوریه",'callback_data'=>'help-vasetegap']],
[['text'=>"❓ واسطه داخل پیوی چطوریه",'callback_data'=>'help-vasetepv']],
[['text'=>"❓ حسابمون رو چطوری شارژ کنیم",'callback_data'=>'help-howcharj']],
[['text'=>"❓ چطوری ربات رو اد کنیم داخل گروه",'callback_data'=>'help-addbot']],
[['text'=>"❓ چطوری معتبر بشیم",'callback_data'=>'help-howreliable']],
]]);

$bartarin = json_encode(['inline_keyboard'=>[
[['text'=>"💰 برترین سکه داران",'callback_data'=>"bartar-coin"]],
[['text'=>"🎊 برترین های زیر مجموعه گیری",'callback_data'=>"bartar-inv"]],
[['text'=>"🛍 برترین های شارژ حساب",'callback_data'=>"bartar-shop"]],
[['text'=>"📤 برترین های برداشت",'callback_data'=>"bartar-harvest"]],
]
]);
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if($data == "close"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ پنل با موفقیت بسته شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔐 حساب کاربری",'callback_data'=>"update"]],
]
])
]);
}
if($data == "update"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
👤 شناسه کاربری : $from_id
📆 تاریخ عضویت : $datej
⏰ ساعت عضویت : $timej
🎉 تعداد زیرمجموعه : $inv نفر

💳 مجموع شارژ حساب : $shop
💸 مجموع برداشت : $harvest
🔋 سطح کاربری : $star
💰 موجودی : $coin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML',
'reply_markup'=>$account
]);
}
if($data == "mycurrency"){
$list = "💶 ارز های شما :\n\n\n";
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$coins = $currency[$type] ?? 0;
$list .= "💸 ارز $type | $name : $coins تا\n┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄\n";
}
$list .= "\n⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است";
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>$list,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>"update"]],
]])
]);
}
if($data == "gettayd"){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
/*
$pdo->prepare("UPDATE `user` SET step = 'GetTayd' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📂 به منوی تایید هویت و تاییده گرفتن خوش آمدید !


✅ با هر تأیید هویت کردن یک ستاره به حساب شما اضافه میشود و شما ادم معتبر تری شناخته میشود و اگر تبر بزنید اطلاعات شما پخش میشود !
",
'parse_mode'=>'HTML',
'reply_markup'=>$confirmation
]);
*/
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"💤 این بخش متاسفانه خاموش است",
'show_alert'=>false
]);
}
}
if($data == "invite"){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$post_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$zirlink,
'caption'=>$zirtext,
'parse_mode'=>'HTML'
])->result->message_id;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎊 بابت هر زیر مجموعه گیری با لینک خود $zircoin سکه دریافت کنید !
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id,
'reply_markup'=>$menu
]);
}
}
if($data == "NO"){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text'=>"❌ درخواست برداشت لغو شد",'callback_data'=>"fake"]]
]
])
]);
}
}
if(str_contains($data,"GetWallet-")){
$type = explode("-",$data)[1];
$mywallet = $wallet_currency[array_search($type,$type_currency)];
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"Wallet $type : $mywallet",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['force_reply'=>true,'selective'=>true])
]);
}
if(str_contains($data,"YES-")){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$exp = explode("-",$data);
$code = $exp[1];
$id = $exp[2];
$coins = $exp[3];
$types = $exp[4];
if($types == "cart"){
// edit something . . . !
}elseif($types == "nitro"){
if($coin >= $coins){
$TextCoin = number_format($coins);
$pdo->prepare("UPDATE `user` SET coin = coin - '$coins' , harvest = harvest + '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
✅ درخواست برداشت شما تأیید شد و به ادمین ها ارسال شد

🔖 کد برداشت : $code
",
'parse_mode'=>'HTML',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text'=>"➖ مقدار $TextCoin سکه از شما کم شد",'callback_data'=>"fake"]]
]
])
]);
foreach($admin as $me){
$post_id = bot('sendMessage',[
'chat_id'=>$me,
'text'=>"✅ یک نفر درخواست برداشت نیتروسین کرده است

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id
💰 مقدار سکه درخواستی : $coins
🔋 آیدی عددی ارسال شده : $id
🌟 سطح حساب کاربر : $star
💸 موجودی کاربر : $coin

🔖 کد برداشت : $code

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید",'callback_data'=>"OK-$code-$from_id-$coins"],['text'=>"❌ لغو",'callback_data'=>"CANCEL-$code-$from_id-$coins"]],
]])
])->result->message_id;
$orders[$code][$me] = $post_id;
Save("orders.json",$orders);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ سکه های شما برای تأیید این برداشت کافی نیست",
'show_alert'=>true
]);
}
}
}
}
if(str_contains($data,"OK-")){
$exp = explode("-",$data);
$code = $exp[1];
$id = $exp[2];
$coins = $exp[3];
$name = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
✅ درخواست برداشت با کد $code تأیید شد

💰 مقدار : $coins
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
foreach($admin as $me){
bot('editMessageText',[
'chat_id'=>$me,
'message_id'=>$orders[$code][$me],
'text'=>"
✅ درخواست برداشت سکه با کد $code برای کاربر $name توسط ادمین $first_name تأیید شد
",
'parse_mode'=>'HTML'
]);
}
unset($orders[$code]);
Save("orders.json",$orders);
}
if(str_contains($data,"CANCEL-")){
$exp = explode("-",$data);
$code = $exp[1];
$id = $exp[2];
$coins = $exp[3];
$name = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
❌ درخواست برداشت با کد $code رد شد

💰 مقدار سکه درخواستی : $coins
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
foreach($admin as $me){
bot('editMessageText',[
'chat_id'=>$me,
'message_id'=>$orders[$code][$me],
'text'=>"
❌ درخواست برداشت سکه با کد $code برای کاربر $name توسط ادمین $first_name لغو شد
",
'parse_mode'=>'HTML'
]);
}
unset($orders[$code]);
Save("orders.json",$orders);
}
if($data == "Random"){
$Random = $ads["Random"];
if(count($Random) != 0){
$Find = $Random[array_rand($Random)];
$adsId = $ads[$Find]['id'];
$adsText = $ads[$Find]['text'];
$adsTime = $ads[$Find]['time'];
$adsDate = $ads[$Find]['date'];
$name = bot('getChatMember',['chat_id'=>$adsId,'user_id'=>$adsId])->result->user->first_name;
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $Find

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"ads_$Find"]],
[['text'=>"📨 پیام به فروشنده",'callback_data'=>"PM-$adsId"],['text'=>"🚫 گزارش",'callback_data'=>"REPORT-$Find"]],
[['text'=>"🎲 بعدی ( رندوم )",'callback_data'=>"Random"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🌹 متاسفانه تبلیغی پیدا نشد مجدداً امتحان کنید",
'show_alert'=>false
]);
}
}
if($data == "RandomQuery"){
$Random = $ads["Random"];
if(count($Random) != 0){
$Find = $Random[array_rand($Random)];
$adsId = $ads[$Find]['id'];
$adsText = $ads[$Find]['text'];
$adsTime = $ads[$Find]['time'];
$adsDate = $ads[$Find]['date'];
$name = bot('getChatMember',['chat_id'=>$adsId,'user_id'=>$adsId])->result->user->first_name;
bot('editMessageText',[
'inline_message_id'=>$update->callback_query->inline_message_id,
'text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $Find

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'disable_web_page_preview'=>true,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎲 بعدی ( رندوم )",'callback_data'=>"RandomQuery"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🌹 متاسفانه تبلیغی پیدا نشد مجدداً امتحان کنید",
'show_alert'=>false
]);
}
}
if(str_contains($data,"PM-")){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$id = explode("-",$data)[1];
if($from_id != $id){
$pdo->prepare("UPDATE `user` SET step = 'PM-$id' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📄 پیام خود را ارسال کنید ( بصورت متن فقط )
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما نمیتوانید به خودتان پیام ارسال کنید",
'show_alert'=>true
]);
}
}
}
if(str_contains($data,"Delete-")){
$adsCode = explode("-",$data)[1];
if(DeleteAds($adsCode,$ads)){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ تبلیغ شما با کد $adsCode با موفقیت حذف شد
",
'parse_mode'=>'HTML'
]);
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ قبلاً این تبلیغ حذف شده است
",
'parse_mode'=>'HTML'
]);
}
}
if(str_contains($data,"REPORT-")){
$adsCode = explode("-",$data)[1];
if(isset($ads[$adsCode])){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❓دلیل گزارش خود را انتخاب کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"تبلیغ نامناسب",'callback_data'=>"report-$adsCode-unsuitable"]],
[['text'=>"تبلیغ غیر مرتبط با ربات",'callback_data'=>"report-$adsCode-unrelated"]],
[['text'=>"تبلیغ قدیمی و تکراری",'callback_data'=>"report-$adsCode-old"]],
[['text'=>"تبلیغ یک آدم تبر است",'callback_data'=>"report-$adsCode-tabar"]],
]])
]);
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ این تبلیغ حذف شده است
",
'parse_mode'=>'HTML'
]);
}
}
if(str_contains($data,"report-")){
$exp = explode("-",$data);
$adsCode = $exp[1];
$the = $exp[2];
if(isset($ads[$adsCode])){
$reason = str_replace(['unsuitable','unrelated','old','tabar'],['تبلیغ نامناسب','تبلیغ غیر مرتبط با ربات','تبلیغ قدیمی و تکراری','تبلیغ یک آدم تبر است'],$the);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ گزارش شما برای تبلیغ شماره $adsCode ارسال شد !

❗️دلیل گزارش : $reason
",
'parse_mode'=>'HTML'
]);
foreach($admin as $me){
bot('sendMessage',[
'chat_id'=>$me,
'text'=>"
⚠️ تبلیغ با شماره $adsCode گزارش شده است

💤 دلیل گزارش : $reason

🔰 اطلاعات گزارش دهنده :

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🗑 حذف تبلیغ",'callback_data'=>"Delete-$adsCode"]],
]])
]);
}
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ این تبلیغ حذف شده است
",
'parse_mode'=>'HTML'
]);
}
}
if(str_contains($data,"novasete-")){
$id = explode("-",$data)[1];
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ این درخواست معامله با موفقیت رد شد
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
❌ درخواست معامله با کاربر $first_name تأیید نشد
",
'parse_mode'=>'HTML'
]);
}
if(str_contains($data,"okvasete-")){
$id = explode("-",$data)[1];
$infoStep = GetInfo('user',['id'=>$id]);
if($infoStep['vaseteid'] != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این کاربر در حال معامله با شخص دیگری است",
'show_alert'=>true
]);
}else{
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در حال معامله با شخص دیگری هستید",
'show_alert'=>true
]);
}else{
$pdo->prepare("UPDATE `user` SET vaseteid = '$id' , step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET vaseteid = '$from_id' , step = CONCAT('vasete-',vaseteid) WHERE id = '$id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت وارد یک معامله با این کاربر شدید !
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 اکنون میتوانید پیام خود را برای کاربر مقابل ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
✅ درخواست معامله با کاربر $first_name تأیید شد !

🔰 اکنون میتوانید پیام خود را برای کاربر مقابل ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
}
}
}
if(str_contains($data,"Reval-")){
$exp = explode("-",$data);
$me = $exp[1];
$id = $exp[2];
$msg_id = $exp[3];
$infoStep = GetInfo('user',['id'=>$id]);
if($infoStep['vaseteid'] != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این کاربر در حال معامله با شخص دیگری است",
'show_alert'=>true
]);
}else{
if($from_id == $me){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در حال معامله با شخص دیگری هستید",
'show_alert'=>true
]);
}else{
$pdo->prepare("UPDATE `user` SET vaseteid = '$id' , gapid = '$chat_id' , step = 'Senits' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET vaseteid = '$from_id' , gapid = '$chat_id' WHERE id = '$id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت وارد یک معامله با $id شدید !
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
💰 خب مقدار نیتروسین معامله شما چقدر است ؟
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$msg_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎈 معامله موارد دیگر",'callback_data'=>"TagOther-$from_id"]],
]])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ فقط کاربر $me می‌تواند تأیید کند",
'show_alert'=>true
]);
}
}
}
if(str_contains($data,"TagOther-")){
$exp = explode("-",$data);
$me = $exp[1];
if($from_id == $me){
$pdo->prepare("UPDATE `user` SET step = 'Gero' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
💰 خب مقدار ارزش طاق ( ارز و نیتروسین و جت ممبر و سرعت ممبر و .... ) چقدر است به همان مقدار یک نفر باید نیتروسین گرو بگذارد !
",
'parse_mode'=>'HTML'
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ فقط کاربر $me می‌تواند تأیید کند",
'show_alert'=>true
]);
}
}
if(str_contains($data,"MeSet-")){
$exp = explode("-",$data);
$coins = $exp[1];
$me = $exp[2];
$id = $exp[3];
$oks = $datas['vaseteid'];
if($from_id == $me or $from_id == $id){
if($coin >= $coins){
$pdo->prepare("UPDATE `user` SET coin = coin - '$coins' , nitvasete = '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت از شما مقدار $coins سکه کسر شد و اکنون میتوانید از طرف مقابل ( اکانت و ارز و ... ) خود را دریافت کنید

🌹 لطفاً بعد از تحویل گرفتن از طریق دکمه زیر تأیید کنید تا نیتروسین رو به طرف مقابل بدم
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Dade-$coins-$from_id-$oks"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ موجودی شما کافی نیست برای این معامله حساب خود را شارژ کنید",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این معامله برای شما نیست",
'show_alert'=>true
]);
}
}
if(str_contains($data,"Dade-")){
$exp = explode("-",$data);
$coins = $exp[1];
$me = $exp[2];
$id = $exp[3];
if($from_id == $me){
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' , coin = coin + '$coins' WHERE id = '$id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `gaps` SET count = count + '1' WHERE id = '$chat_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
✅ معامله $id و $from_id با موفقیت به اتمام رسید !


💰 تعداد $coins سکه به کاربر $id اضافه شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=> Font($time) ,'callback_data'=>"fake"],['text'=>"⏰ ساعت اتمام معامله",'callback_data'=>"fake"]],
[['text'=> Font($date) ,'callback_data'=>"fake"],['text'=>"📅 تاریخ اتمام معامله",'callback_data'=>"fake"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ متاسفانه شما نمی‌توانید تایید کنید",
'show_alert'=>true
]);
}
}
if(str_contains($data,"GroSet-")){
$exp = explode("-",$data);
$coins = $exp[1];
$me = $exp[2];
$id = $exp[3];
$oks = $datas['vaseteid'];
if($from_id == $me or $from_id == $id){
if($coin >= $coins){
$pdo->prepare("UPDATE `user` SET coin = coin - '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🔰 خب $first_name مقدار $coins سکه گرو گذاشت , ( ارز و نیتروسین و جت ممبر و سرعت ممبر و .... ) را اول میتوانید دریافت کنید و برای پس گرفتن نیتروسین گرو باید طرف مقابل تایید کند

⚠️ کاربر $oks لطفاً بعد از دادن ( ارز و نیتروسین و جت ممبر و سرعت ممبر و .... ) اول و بعد از گرفتن ( ارز و نیتروسین و جت ممبر و سرعت ممبر و .... ) روی دکمه زیر کلیک کنید تا نیتروسین گرو رو پس بدم
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎈 نیتروسین گرو را پس بده",'callback_data'=>"Pasbd-$coins-$from_id-$oks"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ موجودی شما کافی نیست برای گرو گذاشتن حساب خود را شارژ کنید",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این معامله برای شما نیست",
'show_alert'=>true
]);
}
}
if(str_contains($data,"Pasbd-")){
$exp = explode("-",$data);
$coins = $exp[1];
$id = $exp[2];
$me = $exp[3];
if($from_id == $me){
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' , coin = coin + '$coins' WHERE id = '$id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `gaps` SET count = count + '1' WHERE id = '$chat_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
✅ پس گرفتن گرو نیتروسین توسط $first_name تایید شد و معامله شما با موفقیت به اتمام رسید !


🔰 مقدار $coins سکه به کاربر $id برگشت داده شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=> Font($time) ,'callback_data'=>"fake"],['text'=>"⏰ ساعت اتمام معامله",'callback_data'=>"fake"]],
[['text'=> Font($date) ,'callback_data'=>"fake"],['text'=>"📅 تاریخ اتمام معامله",'callback_data'=>"fake"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ متاسفانه شما نمی‌توانید تایید کنید",
'show_alert'=>true
]);
}
}
if($ids != 0 and $gapid == 0){
if(str_contains($data,"setup-")){
$exp = explode("-",$data);
$one = $exp[1];
$typeone = str_replace(['nitro','acc','arz','none'],['نیتروسین','اکانت','ارز','انتخاب نشده'],$one);
$two = $exp[2];
$typetwo = str_replace(['nitro','acc','arz','none'],['نیتروسین','اکانت','ارز','انتخاب نشده'],$two);
$edit_id = $exp[3];
$who = $exp[4];
if($ids == $who){
if($two == "none"){
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"اکانت ✔️",'callback_data'=>"setup-$one-acc-$edit_id-$ids"]],
[['text'=>"نیتروسین ✔️",'callback_data'=>"setup-$one-nitro-$edit_id-$ids"]],
[['text'=>"ارز ✔️",'callback_data'=>"setup-$one-arz-$edit_id-$ids"]],
]])
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$edit_id,
'text'=>"
❓ چه چیزی را شما دریافت می‌کنید
",
'parse_mode'=>'HTML'
]);
}elseif($one == "nitro" and $two == "nitro"){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ متاسفانه شما نمیتوانید نیتروسین با نیتروسین معامله کنید !",
'show_alert'=>true
]);
}elseif($one == "nitro" or $two == "nitro"){
$pdo->prepare("UPDATE `user` SET step = 'setnit-$one-$two' WHERE id = '$from_id' LIMIT 1")->execute();
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$edit_id
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"👆",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💰 مقدار سکه نیتروسین معامله چقدر است ؟

🔰 نوع معامله : $typeone با $typetwo
",
'parse_mode'=>'HTML'
]);
}elseif($one == "arz" and $two == "arz"){
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$edit_id
]);
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"💸 $name",'callback_data'=>"TypeArz-$type-none-$ids"]];
}
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❓ نوع ارز که می‌دهید را مشخص کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}elseif($one == "arz" or $two == "arz"){
$pdo->prepare("UPDATE `user` SET step = 'setarz-$one-$two' WHERE id = '$from_id' LIMIT 1")->execute();
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$edit_id
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"👆",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💰 مقدار ارز معامله چقدر است ؟

🔰 نوع معامله : $typeone با $typetwo
",
'parse_mode'=>'HTML'
]);
}else{
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$edit_id
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🌹 نوع معامله برای طرف مقابل هم ارسال شد تا تأیید کند

🔰 نوع معامله : $typeone با $typetwo

⚠️ <b>شما $typetwo را دریافت می‌کنید و $typeone را تحویل میدهید</b>
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا معامله شما به شرح زیر است ؟

🌟 طاق $typeone با $typetwo

⚠️ <b>شما $typeone را دریافت می‌کنید و $typetwo را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-$one-$two-$from_id-$one"],['text'=>"❌ خیر",'callback_data'=>"Who-No-$one-$two-$from_id-$one"]],
]])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"Who-")){
$exp = explode("-",$data);
$ok = $exp[1];
$one = $exp[2];
$typeone = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$one);
$two = $exp[3];
$typetwo = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$two);
$who = $exp[4];
$coins = $exp[5];
if($ok == "Yes"){
if($ids == $who){
if(is_numeric($coins)){
$pdo->prepare("UPDATE `user` SET nitvasete = '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET nitvasete = '$coins' WHERE id = '$ids' LIMIT 1")->execute();
}
if($one == "nitro"){
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا شما باید مقدار $coins نیتروسین را بدهید ؟
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-$two-$one-$from_id-$coins"],['text'=>"❌ خیر",'callback_data'=>"Who-No-$two-$one-$from_id-$coins"]],
]])
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ صبر کنید تا طرف مقابل تایید کند که نیتروسین را ربات از حسابش کم کند
",
'parse_mode'=>'HTML'
]);
}elseif($two == "nitro"){
if($coin >= $coins and is_numeric($coins)){
$pdo->prepare("UPDATE `user` SET coin = coin - '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
$edit_id = bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
<b>✅ لطفاً $typeone را به کاربر مقابل تحویل دهید سپس صبر کنید تا کاربر مقابل تایید کند شما به آن تحویل دادید</b>

<i>🔰 درصورتی که کاربر مقابل تایید کرد شما میتوانید با استفاده از دکمه‌ی زیر نیتروسین را دریافت کنید</i>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"❌ دریافت",'callback_data'=>"GetNitro-No-$coins-$from_id"]],
]])
])->result->message_id;
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
➖ مقدار $coins نیتروسین از حساب شما کسر شد

<u>⚠️ درصورتی که کاربر مقابل معامله $typeone را به شما تحویل داد روی دکمه تایید کلیک کنید</u>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تایید",'callback_data'=>"TakeOk-$edit_id-$coins-Nitro-$ids"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ موجودی شما $coin سکه است و مقدار نیتروسین معامله $coins سکه است و موجودی شما کافی نمی‌باشد",
'show_alert'=>true
]);
}
}elseif($one == "arz" and $two == "arz"){
$arz = explode("/",$coins);
$give = $arz[0];
$typegive = str_replace($type_currency,$name_currency,$give);
$take = $arz[1];
$typetake = str_replace($type_currency,$name_currency,$take);
$pdo->prepare("UPDATE `user` SET step = 'setarzs-$give-0-$take-0' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"👆",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💰 مقدار ارز $typegive که می‌گیرید چقدر است ؟
",
'parse_mode'=>'HTML'
]);
}elseif($one == "arz"){
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا شما باید مقدار $coins ارز را بدهید ؟
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-$two-$one-$from_id-$coins"],['text'=>"❌ خیر",'callback_data'=>"Who-No-$two-$one-$from_id-$coins"]],
]])
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ صبر کنید تا طرف مقابل تایید کند که ارز را ربات از حسابش کم کند
",
'parse_mode'=>'HTML'
]);
}elseif($two == "arz"){
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"💵 $name 💵",'callback_data'=>"TypesArz-$type-$one-$coins-$ids"]];
}
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❓ نوع ارز که می‌دهید را مشخص کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}elseif($one == "acc"){
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
🔰 لطفاً از طریق دکمه زیر اکانت را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"📤 ارسال اکانت",'callback_data'=>"SendAccount-$two-$from_id"]],
]])
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ صبر کنید تا طرف مقابل اکانت را ارسال کند
",
'parse_mode'=>'HTML'
]);
}elseif($two == "acc"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🔰 لطفاً از طریق دکمه زیر اکانت را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"📤 ارسال اکانت",'callback_data'=>"SendAccount-$one-$ids"]],
]])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ معامله با موفقیت لغو شد
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❌ معامله توسط کاربر مقابل لغو شد
",
'parse_mode'=>'HTML'
]);
}
}
if(str_contains($data,"TypesArz-")){
$exp = explode("-",$data);
$type = $exp[1];
$typearz = str_replace($type_currency,$name_currency,$type);
$give = $exp[2];
$typegive = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$give);
$coins = $exp[3];
$who = $exp[4];
if($ids == $who){
if($currency[$type] >= $coins){
$currency[$type] -= $coins;
$currency = Json($currency);
$pdo->prepare("UPDATE `user` SET currency = '$currency' WHERE id = '$from_id' LIMIT 1")->execute();
$edit_id = bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
<b>✅ لطفاً $typegive را به کاربر مقابل تحویل دهید سپس صبر کنید تا کاربر مقابل تایید کند شما به آن تحویل دادید</b>

<i>🔰 درصورتی که کاربر مقابل تایید کرد شما میتوانید با استفاده از دکمه‌ی زیر ارز را دریافت کنید</i>

<u>❗️ نوع ارز معامله $typearz تنظیم شده است</u>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"❌ دریافت",'callback_data'=>"GetArz-No-$type/$coins-$from_id"]],
]])
])->result->message_id;
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
➖ مقدار $coins تا ارز $typearz از حساب شما کسر شد

<u>⚠️ درصورتی که کاربر مقابل معامله $typegive را به شما تحویل داد روی دکمه تایید کلیک کنید</u>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تایید",'callback_data'=>"TakeOk-$edit_id-$type/$coins-Arz-$ids"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ موجودی شما {$currency[$type]} تا ارز $typearz است و مقدار ارز معامله $coins تا است و موجودی شما کافی نمی‌باشد",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"TakeOk-")){
$exp = explode("-",$data);
$edit_id = $exp[1];
$coins = $exp[2];
$types = $exp[3];
$typetext = str_replace(['Nitro','Acc','Arz'],['نیتروسین','اکانت','ارز'],$types);
$who = $exp[4];
if($ids == $who){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❓ آیا مطمئن هستید که میخواهید کاربر مقابل را تایید کنید تا $typetext به طرف مقابل داده شود
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"OkTake-$edit_id-$coins-$types-$ids"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"OkTake-")){
$exp = explode("-",$data);
$edit_id = $exp[1];
$coins = $exp[2];
$types = $exp[3];
$typetext = str_replace(['Nitro','Acc','Arz'],['نیتروسین','اکانت','ارز'],$types);
$who = $exp[4];
if($ids == $who){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🎈 با موفقیت تایید شد و اکنون کاربر مقابل میتواند $typetext را دریافت کند
",
'parse_mode'=>'HTML'
]);
bot('editMessageReplyMarkup',[
'chat_id'=>$ids,
'message_id'=>$edit_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ دریافت",'callback_data'=>"Get$types-Yes-$coins-$from_id"]],
]])
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
🎉 کاربر مقابل تایید کرد اکنون میتوانید روی دکمه دریافت کلیک کنید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$edit_id
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"GetNitro-")){
$exp = explode("-",$data);
$ok = $exp[1];
$coins = $exp[2];
$who = $exp[3];
if($ids == $who){
if($ok == "Yes"){
$pdo->prepare("UPDATE `user` SET coin = coin + '$coins' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
➕ مقدار $coins نیتروسین به حساب شما افزوده شد
",
'parse_mode'=>'HTML'
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ کاربر مقابل هنوز شما را تایید نکرده است",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"GetArz-")){
$exp = explode("-",$data);
$ok = $exp[1];
$arz = explode("/",$exp[2]);
$type = $arz[0];
$typearz = str_replace($type_currency,$name_currency,$type);
$coins = $arz[1];
$who = $exp[3];
if($ids == $who){
if($ok == "Yes"){
$currency[$type] += $coins;
$currency = Json($currency);
$pdo->prepare("UPDATE `user` SET currency = '$currency' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
➕ مقدار $coins تا ارز $typearz به حساب شما افزوده شد
",
'parse_mode'=>'HTML'
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ کاربر مقابل هنوز شما را تایید نکرده است",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"TypeArz-")){
$exp = explode("-",$data);
$one = $exp[1];
$typeone = str_replace($type_currency,$name_currency,$one);
$two = $exp[2];
$typetwo = str_replace($type_currency,$name_currency,$two);
$who = $exp[3];
if($ids == $who){
if($one == $two){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ متاسفانه شما نمیتوانید $typeone با $typetwo معامله کنید !",
'show_alert'=>true
]);
}elseif($two == "none"){
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"$name 💸",'callback_data'=>"TypeArz-$one-$type-$ids"]];
}
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❓ نوع ارز که می‌گیرید را مشخص کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🌹 نوع معامله برای طرف مقابل هم ارسال شد تا تأیید کند

🔰 نوع معامله : $typeone با $typetwo

⚠️ <b>شما $typetwo را دریافت می‌کنید و $typeone را تحویل میدهید</b>
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا معامله شما به شرح زیر است ؟

🌟 طاق $typeone با $typetwo

⚠️ <b>شما $typeone را دریافت می‌کنید و $typetwo را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-arz-arz-$from_id-$one/$two"],['text'=>"❌ خیر",'callback_data'=>"Who-No-arz-arz-$from_id-$one/$two"]],
]])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"TransferArzs-")){
$exp = explode("-",$data);
$give = $exp[1];
$coingive = $exp[2];
$typegive = str_replace($type_currency,$name_currency,$give);
$take = $exp[3];
$cointake = $exp[4];
$typetake = str_replace($type_currency,$name_currency,$take);
$who = $exp[5];
if($ids == $who){
if($currency[$give] >= $coingive){
$currency_ids = Json(GetInfo('user',['id'=>$ids])['currency']);
if($currency_ids[$take] >= $cointake){
$currency[$give] -= $coingive;
$currency[$take] += $cointake;
$currency = Json($currency);
$pdo->prepare("UPDATE `user` SET currency = '$currency' WHERE id = '$from_id' LIMIT 1")->execute();
$currency_ids[$take] -= $cointake;
$currency_ids[$give] += $coingive;
$currency_ids = Json($currency_ids);
$pdo->prepare("UPDATE `user` SET currency = '$currency_ids' WHERE id = '$ids' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
➕ مقدار $cointake تا ارز $typetake به حساب شما افزوده شد

➖ مقدار $coingive تا ارز $typegive از حساب شما کسر شد
",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
➕ مقدار $coingive تا ارز $typegive به حساب شما افزوده شد

➖ مقدار $cointake تا ارز $typetake از حساب شما کسر شد
",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
🔰 کاربر مقابل از شما درخواست کرد که موجودی ارز $typetake خود را به مقدار $cointake تا افزایش دهید


🌹 با دستور زیر موجودی ارز $typetake خود را افزایش دهید :

/currency
",
'parse_mode'=>'HTML'
]);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ موجودی کاربر مقابل کافی نمیباشد اطلاع رسانی کردم",
'show_alert'=>true
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 با دستور زیر موجودی ارز $typegive خود را افزایش دهید :

/currency
",
'parse_mode'=>'HTML'
]);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ موجودی ارز $typegive شما {$currency[$give]} تا است و مقدار ارز معامله $coingive تا است و موجودی شما کافی نمی‌باشد",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"SendAccount-")){
$exp = explode("-",$data);
$give = $exp[1];
$who = $exp[2];
if($ids == $who){
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ شماره اکانت مورد نظر را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"Tayeed-")){
$exp = explode("-",$data);
$phone = $exp[1];
$give = $exp[2];
$typegive = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$give);
$times = $exp[3];
$who = $exp[4];
if($ids == $who){
if($timer - $times > 180){
if($timer - $times < 12000){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✔️ صبر کنید درحال پردازش . . .",
'show_alert'=>false
]);
$results = Client([
'phone'=>$phone,
'type'=>"session"
]);
if($results['results'] == "Ban"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ اکانت ارسال شده بن شده است
",
'parse_mode'=>'HTML'
]);
}elseif($results['results'] == "Expired"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ اکانت ارسال شده منقضی شده است
",
'parse_mode'=>'HTML'
]);
}elseif($results['results'] == "Revoked"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ اکانت ارسال شده لغو شده است
",
'parse_mode'=>'HTML'
]);
}elseif($results['results'] == "Password Hash Invalid Error"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ پسورد اکانت ارسال شده تغییر کرده است و ربات نمی‌تواند آنرا تایید کند
",
'parse_mode'=>'HTML'
]);
}elseif(isset($results['sessions']) and is_numeric($results['sessions'])){
if($results['sessions'] == 1){
$edit_id = bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
<b>✅ لطفاً $typegive را به کاربر مقابل تحویل دهید سپس صبر کنید تا کاربر مقابل تایید کند شما به آن تحویل دادید</b>

<i>🔰 درصورتی که کاربر مقابل تایید کرد شما میتوانید با استفاده از دکمه‌ی زیر اکانت را دریافت کنید</i>

<u>🔢 شماره اکانت :</u> <code>+$phone</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"❌ دریافت",'callback_data'=>"GetAcc-No-$phone-$from_id"]],
]])
])->result->message_id;
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🎉 تبریک اکانت با موفقیت دریافت و تأیید شد

<u>⚠️ درصورتی که کاربر مقابل معامله $typegive را به شما تحویل داد روی دکمه تایید کلیک کنید</u>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تایید",'callback_data'=>"TakeOk-$edit_id-$phone-Acc-$ids"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ متاسفانه اکانت شما نشست فعال دارد و ربات نمی‌تواند آنرا تایید کند

🔰 تعداد نشست های فعال : {$results['sessions']}
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطایی در تایید اکانت به وجود آمده است
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ شما فقط میتوانید در 20 دقیقه اول اکانت را تأیید کنید",
'show_alert'=>true
]);
}
}else{
$ehat = ( 180 + $times ) - $timer;
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⏳ لطفاً $ehat ثانیه دیگر مجدد تلاش کنید !",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"GetAcc-")){
$exp = explode("-",$data);
$ok = $exp[1];
$phone = $exp[2];
$who = $exp[3];
if($ids == $who){
if($ok == "Yes"){
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ دریافت مجدد کد",'callback_data'=>"GetAcc-$ok-$phone-$ids"]],
[['text'=>"⚠️ خارج شدن ربات از اکانت",'callback_data'=>"Logout-$phone-$ids"]],
]])
]);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✔️ صبر کنید درحال پردازش . . .",
'show_alert'=>false
]);
$results = Client([
'phone'=>$phone,
'type'=>"tel"
]);
if(isset($results['code'])){
$post_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>$results['code'][0],
'parse_mode'=>'HTML'
])->result->message_id;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔑 رمز اکانت بصورت پیشفرض : <code>{$results['password']}</code>",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id
]);
}elseif(empty($results['code'])){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ متاسفانه هنوز کدی به اکانت ارسال نشده است
",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ خطایی در دریافت کد ورود به اکانت به وجود آمده است
{$results['results']}
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ کاربر مقابل هنوز شما را تایید نکرده است",
'show_alert'=>true
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
if(str_contains($data,"Logout-")){
$exp = explode("-",$data);
$phone = $exp[1];
$who = $exp[2];
if($ids == $who){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✔️ صبر کنید درحال پردازش . . .
",
'parse_mode'=>'HTML'
]);
$results = Client([
'phone'=>$phone,
'type'=>"logout"
])['results'];
if($results == "ok"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ ربات با موفقیت از اکانت خارج شد
",
'parse_mode'=>'HTML'
]);
}elseif($results == "no"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
⚠️ متاسفانه ربات نمی‌تواند از اکانت خارج شود
",
'parse_mode'=>'HTML'
]);
}else{
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
⚠️ خطایی در خارج شدن از اکانت به وجود آمده است
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این پیام برای معامله قبل شما است",
'show_alert'=>true
]);
}
}
}
if(str_contains($data,"reports-")){
$exp = explode("-",$data);
$id = $exp[1];
$why = $exp[2];
$reason = str_replace(['aders','fosh','tabar','bi'],['تبلیغات','فحاشی','تبر زدن','بی احترامی'],$why);
$name = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر $name گزارش شد

❗️دلیل گزارش : $reason
",
'parse_mode'=>'HTML'
]);
foreach($admin as $me){
bot('sendMessage',[
'chat_id'=>$me,
'text'=>"
⚠️ کاربر $name با ایدی عددی $id گزارش شد

💤 دلیل گزارش : $reason

🔰 اطلاعات گزارش دهنده :

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML'
]);
}
}
if($data == "support"){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'Support' WHERE id = '$from_id' LIMIT 1")->execute();
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🎈 صبر کنید",
'show_alert'=>false
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🆘 پیام , انتقاد , پیشنهادات خود را برای ما ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>"nosupport"]],
]])
]);
}
}
if($data == "nosupport"){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🎈 صبر کنید",
'show_alert'=>false
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💤 درخواست نیاز به پشتیبانی آنلاین لغو شد
",
'parse_mode'=>'HTML'
]);
exit();
}
}
if(str_contains($data,"help-")){
$exp = explode("-",$data);
switch($exp[1]){
case 'vasetegap':
$editText = "
👤 راهنما گروه :

🌹 جهت معامله ابتدا باید روی پیام طرف مقابل خود ریپلی کنید و کلمه ( <code>طرفم</code> ) را بفرستید و سپس وارد معامله میشوید !

🎈 اگر معامله شما نیتروسین با ارز و اکانت و ... است کسی که قرار است نیتروسین را بدهد روی دکمه شیشه ای که ظاهر میشود کلیک کند و مقدار تعیین شده سکه از او کسر میشود و سپس طرف مقابل با خیال راحت میتوانید ارز و اکانت و ... رو تحویل دهد و بعد از تاییده گرفتن نیتروسین تعیین شده به حساب طرف مقابل اضافه میشود

⚠️ اگر معامله شما طاق اکانت با اکانت است یک نفر باید نیتروسین گرو بگذارید و بعد از معامله طاق اکانت با اکانت مجدد تحویل بگیرد لطفاً ابتدا اطمینان حاصل کنید از سالم بود و ... اکانت سپس تأیید کنید
";
break;
case 'vasetepv':
$editText = "
📚 راهنما ربات :

🔗 شما با لینک کاربری که با او معامله دارید وارد ربات میشوید و بعد از تأیید کردن معامله شما میتوانید نوع معامله را تعیین کنید

💫 اگر در معامله نیتروسین باشد از یک نفر نیتروسین کم می‌شود و بعد از تایید توسط کسی که نیتروسین ازش کم شده نیتروسین به حساب کاربر مقابل ارسال میشود و معامله به اتمام می‌رسد

🍻 اگر در معامله ارز با ارز باشد از هر دو طرف ارز ها کم می‌شوند به نفر دیگری انتقال پیدا میکنند و اگر در معامله فقط ارز با چیز دیگری باشد از یک نفر مقدار مشخص شده ارز تعیین شده کم می‌شود و بعد از دریافت نوع دیگر معامله باید تایید کند تا به طرف مقابل اضافه شود ارز تعیین شده

🔢 اگر طاق اکانت باشد معامله ربات یک اکانت را دریافت می‌کند و بعد از تایید کردن طرفی که اکانت را به ربات داده است طرف دیگر میتواند اکانت را از ربات دریافت کند
";
break;
case 'howcharj':
$editText = "
💶 شارژ حساب کاربری :

💳 شارژ با درگاه : فقط لازم است مقدار سکه ای که در ربات لازم دارید را وارد کنید و از درگاه ما بصورت کارتی پرداخت کنید هر 250 تومان معادل 1,000 سکه در ربات است

💰 شارژ با نیتروسین : فقط کافیست مقداری که میخواهید حساب خود را شارژ کنید با ادمین های ما انتقال بزنید و برای ربات ما فوروارد کنید پیام انتقال سکه را

💴 شارژ با ارز : ارزی که میخواهید حساب خود را شارژ کنید انتخاب می‌کنید و سپس یک پیام حاوی ولت ما برای شما ارسال می‌شود و هش انتقال ارز را روی پیام ولت ما ریپلی می‌کنید و ارسال میکنید سپس حساب شما شارژ می‌شود
";
break;
case 'addbot':
$editText = "
🌟 افزودن ربات به گروه :

https://t.me/$botuser?startgroup=start

⚠️ جهت درست کار کردن ربات لطفاً ربات را در گروه ادمین کنید
";
break;
case 'howreliable':
$editText = "
🥂 کاربران معتبر :

🔋 هر کسی سطح کاربری بالا تری داشته باشد یعنی آدم معتبری شناخته میشود ولی هرگز به آنها اول ندهید چون ما کسی رو تأیید نمیکنم !

💣 جهت افزایش سطح کاربری خود میتوانید به بخش تاییده گرفتن بروید و اطلاعات خود را تکمیل کنید
";
break;
}
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>$editText,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>"helpmenu"]],
]])
]);
}
if($data == "helpmenu"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
⁉️ راهنمای قسمت مورد نظر خود را انتخاب کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>$help
]);
}
if($data == "fake"){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"💤 این دکمه فقط جهت نمایش اطلاعات است",
'show_alert'=>false
]);
}
if(str_contains($data,"harvest-")){
if($ids != 0){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما در معامله هستید نمی‌توانید از این دکمه استفاده کنید",
'show_alert'=>false
]);
}else{
$exp = explode("-",$data);
if($exp[1] == "cart"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💤 متاسفانه این بخش غیرفعال است فعلآ",
'parse_mode'=>'HTML'
]);
}elseif($exp[1] == "nitroseen"){
if($coin >= 1000){
$pdo->prepare("UPDATE `user` SET step = 'harvestnitro' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💸 مقدار سکه جهت برداشت را ارسال کنید

✅ موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ جهت برداشت باید حداقل 1,000 سکه داشته باشید",
'parse_mode'=>'HTML'
]);
}
}elseif($exp[1] == "arz"){
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"💵 $name 💵",'callback_data'=>"harvest-$type"]];
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🥂 نوع ارز جهت برداشت را انتخاب کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}elseif(in_array($exp[1],$type_currency)){
$typearz = str_replace($type_currency,$name_currency,$exp[1]);
if($currency[$exp[1]] > 0){
$pdo->prepare("UPDATE `user` SET step = 'harvestarz-{$exp[1]}' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💸 مقدار ارز $typearz جهت برداشت را ارسال کنید

✅ موجودی شما : {$currency[$exp[1]]}
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ موجودی ارز $typearz شما جهت برداشت کافی نمیباشد",
'show_alert'=>true
]);
}
}
}
}
if(in_array($from_id,$admin)){
if(str_contains($data,"bartar-")){
$Ranks = GetRanks(explode("-",$data)[1],10);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
$Ranks

⏳ ساعت و تاریخ : $time - $date

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>$bartarin
]);
}
if(str_contains($data,"addcurrency-")){
$exp = explode("-",$data);
$id = $exp[1];
$type = $exp[2];
$count = $exp[3];
$typetext = str_replace($type_currency,$name_currency,$type);
$currency_id = Json(GetInfo('user',['id'=>$id])['currency']);
$currency_id[$type] += $count;
$currency_id = Json($currency_id);
$pdo->prepare("UPDATE `user` SET currency = '$currency_id' WHERE id = '$id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"✅ با موفقیت $count تا ارز $typetext به کاربر $id اضافه شد",
'parse_mode'=>'HTML'
]);
}
if(str_contains($data,"lowcurrency-")){
$exp = explode("-",$data);
$id = $exp[1];
$type = $exp[2];
$count = $exp[3];
$currency_id = Json(GetInfo('user',['id'=>$id])['currency']);
$currency_id[$type] -= $count;
$currency_id = Json($currency_id);
$pdo->prepare("UPDATE `user` SET currency = '$currency_id' WHERE id = '$id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"✅ با موفقیت $count تا ارز $typetext از کاربر $id کم شد",
'parse_mode'=>'HTML'
]);
}
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if($tc == 'private'){
if(preg_match('/^\/([Ss][Tt][Aa][Rr][Tt])(?:\s(\d+))?$/i',$text,$match)){
if($ids != 0 and $gapid == 0){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 شما در حال معامله هستید",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
exit();
}
if($datas['id'] == true){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 سلام به ربات $botname خوش آمدید !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}else{
$pdo->prepare("INSERT INTO `user` (`id` , `step` , `coin` , `inv` , `harvest` , `shop` , `currency` , `star` , `my` , `vaseteid` , `gapid` , `nitvasete` , `gmail` , `phone` , `code` , `spam` , `time` , `date`) VALUES ('$from_id','none','0','0','0','0','$currencys','⭐️','0','0','0','0','none','0','0','$timer','$time','$date')")->execute();
$invite = $match[2];
if($invite != $from_id and GetInfo('user',['id'=>$invite])['id'] == true){
$pdo->prepare("UPDATE `user` SET my = '$invite' WHERE id = '$from_id' LIMIT 1")->execute();
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 سلام به ربات $botname خوش آمدید !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
foreach($reports as $chats){
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
✔️ کاربر <a href ='tg://user?id=$from_id'>$first_name</a> ربات را در ساعت <code>$time</code> و تاریخ <code>$date</code> استارت کرد
",
'parse_mode'=>'HTML'
]);
}
}
exit();
}
if($datas['id'] == false){
$pdo->prepare("INSERT INTO `user` (`id` , `step` , `coin` , `inv` , `harvest` , `shop` , `currency` , `star` , `my` , `vaseteid` , `gapid` , `nitvasete` , `gmail` , `phone` , `code` , `spam` , `time` , `date`) VALUES ('$from_id','none','0','0','0','0','$currencys','⭐️','0','0','0','0','none','0','0','$timer','$time','$date')")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🫧 مجدداً ربات را /start کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
foreach($reports as $chats){
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
✔️ کاربر <a href ='tg://user?id=$from_id'>$first_name</a> ربات را در ساعت <code>$time</code> و تاریخ <code>$date</code> استارت کرد
",
'parse_mode'=>'HTML'
]);
}
exit();
}
if(isset($channel)){
$chekjoin = bot('getChatMember',['chat_id'=>$channel,'user_id'=>$from_id])->result->status;
if($chekjoin == 'left'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 لطفاً ابتدا در کانال جوین اجباری زیر عضو شوید سپس از ربات استفاده کنید !

📢 کانال ما : $channel
",
'parse_mode'=>'HTML'
]);
exit();
}
}
if($datas['my'] != 0){
$id = $datas['my'];
$pdo->prepare("UPDATE `user` SET my = '0' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET coin = coin + '$zircoin' , inv = inv + '1' WHERE id = '$id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 کاربر <a href ='tg://user?id=$from_id'>$first_name</a> با لینک شما وارد ربات شد و به شما $zircoin سکه اضافه شد !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
foreach($reports as $chats){
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
🎉 کاربر <a href ='tg://user?id=$from_id'>$first_name</a> در ساعت <code>$time</code> و تاریخ <code>$date</code> زیر مجموعه کاربر <a href ='tg://user?id=$id'>$id</a> شده است
",
'parse_mode'=>'HTML'
]);
}
}
if(preg_match('/^\/([Ss][Tt][Aa][Rr][Tt]) vasete_(\d+)/',$text,$match)){
$id = $match[2];
if($from_id != $id){
$infoStep = GetInfo('user',['id'=>$id]);
if($infoStep['id']){
if($infoStep['vaseteid'] != 0){
if($from_id == $infoStep['vaseteid']){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 کاربر $id در حال معامله با شما است !",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 کاربر $id در حال معامله با شخص دیگری است !",
'parse_mode'=>'HTML'
]);
}
}else{
if($ids != 0){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 شما در حال معامله هستید",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ درخواست شما برای کاربر $id ارسال شد",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
✅ درخواست انجام معامله توسط کاربر $first_name ارسال شده است !

❓ آیا میخواهید معامله ای با این کاربر داشته باشید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"okvasete-$from_id"],['text'=>"❌ خیر",'callback_data'=>"novasete-$from_id"]],
]])
]);
}
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متاسفانه کاربر $id در ربات عضو نمی‌باشد",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ شما با خودتان نمیتوانید معامله کنید",
'parse_mode'=>'HTML'
]);
}
exit();
}
if(str_starts_with($text,"/nitroseen")){
foreach($sharj as $devs){
$i++;
$list .= Font($i)." - <code>$devs</code>\n";
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 جهت شارژ حساب فقط کافیست مقدار سکه مورد نیاز خود را به یکی از حساب های کاربری زیر انتقال بزنید ( نیتروسین فقط )

$list

⚠️ نکته : بعد از انتقال سکه در ربات نیتروسین انتقال سکه خود را برای ربات فوروارد کنید تا در ربات حساب شما شارژ شود !
",
'parse_mode'=>'HTML'
]);
exit();
}
if(preg_match('/✅ تعداد (.*) سکه در تاریخ (.*) ساعت (.*) با موفقیت به کاربر (.*) انتقال داده شد./',$text,$exp)){
if($update->message->forward_from->id == 5188739042){
$CoinSend = $exp[1];
$DateSend = $exp[2];
$TimeSend = $exp[3];
$IdSend = $exp[4];
if($DateSend == $date){
if(in_array($IdSend,$sharj)){
$hash = md5($CoinSend.':'.$DateSend.':'.$TimeSend.':'.$IdSend);
if(GetInfo('transfer',['hash'=>$hash])['hash'] == false){
$pdo->prepare("INSERT INTO `transfer` (`hash` , `fromid` , `coin` , `to` , `time` , `date`) VALUES ('$hash','$from_id','$CoinSend','$IdSend','$time','$date')")->execute();
$pdo->prepare("UPDATE `user` SET coin = coin + '$CoinSend' , shop = shop + '$CoinSend' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت حساب کاربری شما مقدار <code>$CoinSend</code> سکه شارژ شد",
'parse_mode'=>'HTML'
]);
foreach($reports as $chats){
$post_id = bot('forwardMessage',[
'chat_id'=>$chats,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
])->result->message_id;
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
🌹 کاربر زیر حساب خود را با نیتروسین شارژ کرد

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id
🌟 سطح حساب کاربر : $star
💸 موجودی کاربر : ".($coin + $CoinSend)."

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ شما قبلا بابت این انتقال سکه موجودی دریافت کردید",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این انتقال سکه برای ادمین های ربات ما نیست",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این انتقال سکه برای امروز نمی‌باشد",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این پیام از نیتروسین فوروارد نشده است",
'parse_mode'=>'HTML'
]);
}
exit();
}
if(str_starts_with($text,"/currency")){
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"💵 $name 💵",'callback_data'=>"GetWallet-$type"]];
}
bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>'CAACAgIAAxkBAAIaaGL7YZ5XYf3JKnj5ovX09EvS1sc1AAJ9DAACyg9ASk_lHJFjepVqKQQ',
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
exit();
}
if(isset($update->message->reply_to_message)){
$reply_text = $update->message->reply_to_message->text;
if(preg_match('/^Wallet (.*) : (.*)/',$reply_text,$exp)){
$type = $exp[1];
$wallet = $exp[2];
$typetext = str_replace($type_currency,$name_currency,$type);
if(in_array($wallet,$wallet_currency)){
if(mb_strlen($text) <= 100){
if(GetInfo('transfer',['hash'=>$text])['hash'] == false){
$json = json_decode(file_get_contents("https://api-bot.site/API/getTransaction.php?type=$type&hash=$text"),true);
if($json['ok']){
$amount = $json['results']['amount'];
$to = $json['results']['to'];
if($wallet == $to){
$pdo->prepare("INSERT INTO `transfer` (`hash` , `fromid` , `coin` , `to` , `time` , `date`) VALUES ('$text','$from_id','$amount','$to','$time','$date')")->execute();
$currency[$type] += $amount;
$currency = Json($currency);
$pdo->prepare("UPDATE `user` SET currency = '$currency' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت حساب کاربری شما مقدار <code>$amount</code> تا ارز <code>$typetext</code> شارژ شد",
'parse_mode'=>'HTML'
]);
foreach($reports as $chats){
$post_id = bot('forwardMessage',[
'chat_id'=>$chats,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
])->result->message_id;
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
🌹 کاربر زیر حساب خود را با ارز شارژ کرد

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id
🌟 سطح حساب کاربر : $star
💷 نوع ارز : $typetext
💸 موجودی کاربر : ".(Json($currency)[$type])."

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ شما برای ولت $typetext ما انتقال ارز نزدید ! باید به ولت زیر انتقال ارز بزنید

<tg-spoiler>$wallet</tg-spoiler>
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ هش انتقال ارز $typetext درست نمیباشد 
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ شما قبلا بابت این انتقال ارز موجودی دریافت کردید",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ هش شما باید حداکثر 100 کاراکتر باشد
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متاسفانه این ولت دیگر در لیست ولت های ما وجود ندارد
",
'parse_mode'=>'HTML'
]);
}
exit();
}
}
if(str_starts_with($text,"/cart")){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💤 متاسفانه این بخش غیرفعال است فعلآ
",
'parse_mode'=>'HTML'
]);
exit();
}
if($ids != 0 and $gapid == 0){
if($text == "🔙 برگشت"){
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔙 به منوی واسطه برگشتید",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
}elseif(str_contains($step,"setnit-") and isset($text)){
if(is_numeric($text)){
if($text >= 1000 and $text <= 2000000){
$exp = explode("-",$step);
$one = $exp[1];
$typeone = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$one);
$two = $exp[2];
$typetwo = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$two);
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 نوع معامله برای طرف مقابل هم ارسال شد تا تأیید کند

🔰 نوع معامله : $typeone با $typetwo

💰 تعداد سکه نیتروسین معامله : $text

⚠️ <b>شما $typetwo را دریافت می‌کنید و $typeone را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا معامله شما به شرح زیر است ؟

🌟 طاق $typeone با $typetwo

💰 تعداد سکه نیتروسین معامله : $text

⚠️ <b>شما $typeone را دریافت می‌کنید و $typetwo را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-$one-$two-$from_id-$text"],['text'=>"❌ خیر",'callback_data'=>"Who-No-$one-$two-$from_id-$text"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ مقدار معامله نیتروسین شما باید بین 1,000 تا 2,000,000 نیتروسین باشد !
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"setarz-") and isset($text)){
if(is_numeric($text)){
if($text > 0 and $text < 500){
$exp = explode("-",$step);
$one = $exp[1];
$typeone = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$one);
$two = $exp[2];
$typetwo = str_replace(['nitro','acc','arz'],['نیتروسین','اکانت','ارز'],$two);
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 نوع معامله برای طرف مقابل هم ارسال شد تا تأیید کند

🔰 نوع معامله : $typeone با $typetwo

💰 تعداد ارز معامله : $text

⚠️ <b>شما $typetwo را دریافت می‌کنید و $typeone را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❄️ آیا معامله شما به شرح زیر است ؟

🌟 طاق $typeone با $typetwo

💰 تعداد ارز معامله : $text

⚠️ <b>شما $typeone را دریافت می‌کنید و $typetwo را تحویل میدهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله",'callback_data'=>"Who-Yes-$one-$two-$from_id-$text"],['text'=>"❌ خیر",'callback_data'=>"Who-No-$one-$two-$from_id-$text"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ مقدار معامله ارز شما باید از 0 بیشتر و از 500 تا کمتر باشد !
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"setarzs-") and isset($text)){
if(is_numeric($text)){
if($text > 0 and $text < 500){
$exp = explode("-",$step);
$give = $exp[1];
$coingive = $exp[2];
$typegive = str_replace($type_currency,$name_currency,$give);
$take = $exp[3];
$cointake = $exp[4];
$typetake = str_replace($type_currency,$name_currency,$take);
if($coingive == 0){
$pdo->prepare("UPDATE `user` SET step = 'setarzs-$give-$text-$take-0' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💰 مقدار ارز $typetake که می‌دهید چقدر است ؟
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️ اطلاعات برای کاربر مقابل ارسال شدند تا تایید کند

🫧 <b>شما مقدار $coingive تا ارز $typegive می‌گیرید و مقدار $text تا ارز $typetake می‌دهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
🫧 <b>شما مقدار $text تا ارز $typetake می‌گیرید و مقدار $coingive تا ارز $typegive می‌دهید</b>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"💳 انتقال ارز",'callback_data'=>"TransferArzs-$give-$coingive-$take-$text-$from_id"],['text'=>"❌ لغو",'callback_data'=>"Who-No-arz-arz-$from_id-$give/$take"]],
]])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ مقدار معامله ارز شما باید از 0 بیشتر و از 500 تا کمتر باشد !
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"SendAuth-") and isset($text)){
$exp = explode("-",$step);
$give = $exp[1];
if(is_numeric($text)){
if(mb_strlen($text) <= 20){
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
$msg_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ صبر کنید در حال ارسال کد به اکانت شما . . . 
",
'parse_mode'=>'HTML'
])->result->message_id;
$phone = preg_replace('![^\d]*!','',$text);
$json = json_decode(file_get_contents("https://api-bot.site/API/api_id_hash.php?auth=true&phone=+$phone"),true);
if($json['ok']){
$hash = $json['hash'];
$pdo->prepare("UPDATE `user` SET step = 'SendAcc-$give-$phone-$hash' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
🔢 کد یازده رقمی ارسال شده به اکانت +$phone مورد نظر رو ارسال کنید
",
'parse_mode'=>'HTML'
]);
}else{
if(str_contains($json['error'],"too many tries")){
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
⚠️ شماره +$phone از سمت تلگرام محدود شده است و لطفاً بعدا مجدد تلاش کنید
",
'parse_mode'=>'HTML'
]);
}else{
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ شماره +$phone اشتباه است
",
'parse_mode'=>'HTML'
]);
}
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شماره ارسالی حداکثر باید 20 رقم باشد
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"SendAcc-") and isset($text)){
$exp = explode("-",$step);
$give = $exp[1];
$phone = $exp[2];
$hash = $exp[3];
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
$msg_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ صبر کنید در حال ساخت اپلیکیشن برای اکانت شما . . . 
",
'parse_mode'=>'HTML'
])->result->message_id;
$json = json_decode(file_get_contents("https://api-bot.site/API/api_id_hash.php?login=true&phone=+$phone&code=$text&hash=$hash"),true);
if(isset($json['api_hash']) and isset($json['api_id'])){
Client([
'api_id'=>$json['api_id'],
'api_hash'=>$json['api_hash'],
'phone'=>$phone,
'type'=>"save"
]);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
♻️ صبر کنید در حال ارسال کد به اکانت شما . . . 
",
'parse_mode'=>'HTML'
]);
$results = Client([
'phone'=>$phone,
'type'=>"login"
])['results'];
if($results == "ok"){
$pdo->prepare("UPDATE `user` SET step = 'SendCode-$give-$phone' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
🔢 کد پنج رقمی ارسال شده به اکانت +$phone مورد نظر رو ارسال کنید
",
'parse_mode'=>'HTML'
]);
}elseif($results == "Invalid"){
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ شماره +$phone اشتباه است
",
'parse_mode'=>'HTML'
]);
}elseif($results == "Banned"){
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ شماره +$phone مسدود است
",
'parse_mode'=>'HTML'
]);
}elseif(str_contains($results,'Flood-')){
$seconds = str_replace('Flood-',null,$results);
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
⚠️ شماره +$phone از سمت تلگرام محدود شده است و تا $seconds ثانیه دیگر قابل ثبت نیست
",
'parse_mode'=>'HTML'
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❗️ مشکلی در دریافت شماره به وجود آمده است
",
'parse_mode'=>'HTML'
]);
}
}else{
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ کد ارسالی اشتباه است
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"SendCode-") and isset($text)){
if(is_numeric($text)){
if(mb_strlen($text) == 5){
$exp = explode("-",$step);
$give = $exp[1];
$phone = $exp[2];
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
$msg_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ صبر کنید در حال وارد شدن به اکانت شما . . . 
",
'parse_mode'=>'HTML'
])->result->message_id;
$code = preg_replace('![^\d]*!','',$text);
$results = Client([
'phone'=>$phone,
'code'=>$code,
'type'=>"code"
])['results'];
if($results == "PhoneCodeInvalidError"){
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ کد ارسالی اشتباه است
",
'parse_mode'=>'HTML'
]);
}elseif($results == "SessionPasswordNeededError"){
$pdo->prepare("UPDATE `user` SET step = 'SendFa-$phone-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
🔐 اکانت شما دارای تایید دو مرحله ای است لطفاً رمز دو مرحله ای را وارد کنید
",
'parse_mode'=>'HTML'
]);
}elseif($results == "PhoneCodeExpiredError"){
$pdo->prepare("UPDATE `user` SET step = 'SendAuth-$give' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
⚠️ کد ارسال شده منقضی شده است مجدد تلاش کنید
",
'parse_mode'=>'HTML'
]);
}elseif($results == "ok"){
$show = SubString($phone);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
✅ اکانت <code>+$phone</code> با موفقیت دریافت شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید اکانت",'callback_data'=>"Tayeed-$phone-$give-$timer-$ids"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
✅ اکانت زیر با موفقیت از کاربر مقابل دریافت شد


🔢 شماره اکانت : <code>+$show</code>
",
'parse_mode'=>'HTML'
]);
}else{
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❗️ مشکلی در دریافت شماره به وجود آمده است
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ کد ارسالی باید 5 رقم باشد
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML'
]);
}
}elseif(str_contains($step,"SendFa-") and isset($text)){
if(mb_strlen($text) <= 100){
$exp = explode("-",$step);
$phone = $exp[1];
$give = $exp[2];
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
$msg_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ صبر کنید در حال وارد شدن به اکانت شما . . . 
",
'parse_mode'=>'HTML'
])->result->message_id;
$results = Client([
'phone'=>$phone,
'password'=>$text,
'type'=>"2fa"
])['results'];
if($results == "PasswordInvalid"){
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❌ پسورد ارسال شده صحیح نمیباشد
",
'parse_mode'=>'HTML'
]);
}elseif($results == "ok"){
$show = SubString($phone);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
✅ اکانت <code>+$phone</code> با موفقیت دریافت شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید اکانت",'callback_data'=>"Tayeed-$phone-$give-$timer-$ids"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
✅ اکانت زیر با موفقیت از کاربر مقابل دریافت شد


🔢 شماره اکانت : <code>+$show</code>
",
'parse_mode'=>'HTML'
]);
}else{
$pdo->prepare("UPDATE `user` SET step = '$step' WHERE id = '$from_id' LIMIT 1")->execute();
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$msg_id,
'text'=>"
❗️ مشکلی در دریافت شماره به وجود آمده است
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ رمز شما باید حداکثر 100 کاراکتر باشد
",
'parse_mode'=>'HTML'
]);
}
}elseif($text == "⚡️ تنظیم نوع معامله"){
$post_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💱",
'parse_mode'=>'HTML'
])->result->message_id;
$edit_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❓ چه چیزی را شما می‌دهید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id
])->result->message_id;
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id'=>$post_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✔️ اکانت",'callback_data'=>"setup-acc-none-$edit_id-$ids"]],
[['text'=>"✔️ نیتروسین",'callback_data'=>"setup-nitro-none-$edit_id-$ids"]],
[['text'=>"✔️ ارز",'callback_data'=>"setup-arz-none-$edit_id-$ids"]],
]])
]);
}elseif($text == "🚫 گزارش کاربر"){
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❓دلیل گزارش کاربر $name برای چیست",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"تبلیغات",'callback_data'=>"reports-$ids-aders"]],
[['text'=>"فحاشی",'callback_data'=>"reports-$ids-fosh"]],
[['text'=>"تبر زدن",'callback_data'=>"reports-$ids-tabar"]],
[['text'=>"بی احترامی",'callback_data'=>"report-$ids-bi"]],
]])
]);
}elseif($text == "🔰 اطلاعات کاربر"){
$infoStep = GetInfo('user',['id'=>$ids]);
$Lcoin = $infoStep['coin'];
$Lstar = $infoStep['star'];
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ نام کاربر : $name
👤 شناسه کاربر : $ids
🔋 سطح کاربر : $Lstar
💰 موجودی کاربر : $Lcoin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML'
]);
}elseif($text == "❌ خروج از معامله"){
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , nitvasete = '0' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 معامله توسط شما به پایان رسید و از معامله خارج شدید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , nitvasete = '0' WHERE id = '$ids' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"⚠️ معامله توسط کاربر $first_name به اتمام رسید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}elseif(str_contains($step,"vasete-") and isset($text)){
$id = explode("-",$step)[1];
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"$first_name : $text",
'parse_mode'=>'HTML'
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"$first_name : $text",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
}else{
$pdo->prepare("UPDATE `user` SET step = CONCAT('vasete-',vaseteid) WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 لطفاً از معامله خارج نشوید",
'parse_mode'=>'HTML',
'reply_markup'=>$vasete
]);
}
exit();
}
if($text == "🔙 برگشت"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔙 به منوی اصلی برگشتید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
exit();
}
if($text == "🔐 حساب کاربری"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
👤 شناسه کاربری : $from_id
📆 تاریخ عضویت : $datej
⏰ ساعت عضویت : $timej
🎉 تعداد زیرمجموعه : $inv نفر

💳 مجموع شارژ حساب : $shop
💸 مجموع برداشت : $harvest
🔋 سطح کاربری : $star
💰 موجودی : $coin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML',
'reply_markup'=>$account
]);
}
if($text == "📚 راهنما"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⁉️ راهنمای قسمت مورد نظر خود را انتخاب کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>$help
]);
}
if($text == "🆘 پشتیبانی"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📚 قبل از اینکه پیامی به پشتیبانی ارسال کنید لطفاً راهنما را مطالعه کنید در راهنما هر بخش بصورت جداگانه دسته بندی شدند

💯 از اسپم زدن به پشتیبانی و ارسال پیام های تکراری خودداری کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🆘 پشتیبانی آنلاین",'callback_data'=>"support"]],
[['text'=>"📚 راهنما",'callback_data'=>"helpmenu"]],
[['text'=>"🆘 پشتیبانی مستقیم",'url'=>"https://t.me/$support"]],
]])
]);
}
if($step == "Support"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت برای پشتیبانی ارسال شد

🔐 اگر پیام دیگری دارید ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
foreach($admin as $me){
bot('forwardMessage',[
'chat_id'=>$me,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
]);
bot('sendMessage',[
'chat_id'=>$me,
'text'=>"
ID : <code>$from_id</code>

🔰 پی وی کاربر <a href ='tg://user?id=$from_id'>$first_name</a> است
",
'parse_mode'=>'HTML'
]);
}
}
if($text == "🛍 شارژ حساب"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💳 شارژ با درگاه : /cart

💰 شارژ با نیتروسین : /nitroseen

💴 شارژ با ارز : /currency
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
if($text == "💸 انتقال سکه"){
if($coin >= 1000){
$pdo->prepare("UPDATE `user` SET step = 'Transfer' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💸 تعداد سکه مورد نظر جهت انتقال را وارد کنید

💰 موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ جهت انتقال سکه باید حداقل 1,000 سکه داشته باشید

💰 موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
if($step == "Transfer" and isset($text)){
if(is_numeric($text)){
if($text >= 1000 and $text <= 1000000){
if($coin >= $text){
$TextCoin = number_format($text);
$pdo->prepare("UPDATE `user` SET step = 'SendId-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💯 توجه : در صورتی که عملیات انتقال $TextCoin سکه مورد تأیید شما است

🔰 شناسه‌ی کاربری حساب مقصد را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً مقدار سکه جهت انتقال را با توجه به موجودی خود ارسال کنید

✅ موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ حداقل سکه جهت انتقال 1,000 و حداکثر سکه جهت انتقال 1,000,000 سکه می‌باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"SendId-") and isset($text)){
$send = explode("-",$step)[1];
$TextCoin = number_format($send);
if(is_numeric($text)){
if($from_id != $text){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'none' , coin = coin - '$send' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET coin = coin + '$send' WHERE id = '$text' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
<code>✅ تعداد $TextCoin سکه در تاریخ $date ساعت $time با موفقیت به کاربر $text انتقال داده شد</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"
<code>✅ تعداد $TextCoin سکه در تاریخ $date ساعت $time با موفقیت از کاربر $from_id دریافت شد</code>
",
'parse_mode'=>'HTML'
]);
foreach($reports as $chats){
bot('sendMessage',[
'chat_id'=>$chats,
'text'=>"
<code>✅ تعداد $TextCoin سکه در تاریخ $date ساعت $time با موفقیت از کاربر $from_id به کاربر $text انتقال داده شد</code>
",
'parse_mode'=>'HTML'
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این کاربر در ربات $botname عضو نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ شما نمیتوانید به خودتان سکه انتقال بزنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "💰 برداشت"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⚡️ نوع برداشت خود را مشخص کنید",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"💳 برداشت پول",'callback_data'=>'harvest-cart']],
[['text'=>"💰 برداشت نیتروسین",'callback_data'=>'harvest-nitroseen']],
[['text'=>"💴 برداشت ارز",'callback_data'=>'harvest-arz']],
]])
]);
}
if($step == "harvestnitro" and isset($text)){
if(is_numeric($text)){
if($text >= 1000 and $text <= 100000){
if($coin >= $text){
$pdo->prepare("UPDATE `user` SET step = 'Bardashtnitro-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی عددی اکانت خود را جهت برداشت ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً مقدار سکه جهت برداشت را با توجه به موجودی خود ارسال کنید !

✅ موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ حداقل سکه جهت برداشت 1,000 و حداکثر سکه جهت برداشت 100,000 سکه می‌باشد !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"Bardashtnitro-") and isset($text)){
$send = explode("-",$step)[1];
$TextCoin = number_format($send);
if(is_numeric($text)){
if(mb_strlen($text) < 11){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$rand = rand(11111,99999);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ درخواست برداشت $TextCoin نیتروسین به کاربری $text و با کد برداشت $rand مورد تایید شما است ؟
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید",'callback_data'=>"YES-$rand-$text-$send-nitro"],['text'=>"❌ لغو",'callback_data'=>"NO"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ آیدی عددی شما نباید بیشتر از 10 رقم باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"harvestarz-") and isset($text)){
$type = explode("-",$step)[1];
if(is_numeric($text)){
if($text > 0 and $text < 1000){
if($currency[$type] >= $text){
$pdo->prepare("UPDATE `user` SET step = 'Bardashtarz-$text-$type' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ولت خود را جهت برداشت ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً مقدار ارز جهت برداشت را با توجه به موجودی خود ارسال کنید !

✅ موجودی شما : {$currency[$type]}
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ حداقل ارز جهت برداشت باید بیشتر از 0 تا باشد و حداکثر ارز جهت برداشت باید کمتر از 1,000 تا باشد !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"Bardashtarz-") and isset($text)){
$exp = explode("-",$step);
$send = $exp[1];
$type = $exp[2];
$TextCoin = number_format($send);
$TextArz = str_replace($type_currency,$name_currency,$type);
if(mb_strlen($text) <= 100){
$currency[$type] -= $send;
$currency = Json($currency);
$pdo->prepare("UPDATE `user` SET step = 'none' , currency = '$currency' WHERE id = '$from_id' LIMIT 1")->execute();
$rand = rand(11111,99999);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ درخواست برداشت شما تأیید شد و به ادمین ها ارسال شد

🔖 کد برداشت : $rand
",
'parse_mode'=>'HTML',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text'=>"➖ مقدار $TextCoin تا ارز $TextArz از شما کم شد",'callback_data'=>"fake"]]
]
])
]);
foreach($admin as $me){
$post_id = bot('sendMessage',[
'chat_id'=>$me,
'text'=>"✅ یک نفر درخواست برداشت ارز کرده است

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id
💰 مقدار ارز درخواستی : $send
🔋 ولت ارسال شده : $text
⚡️ نوع ارز انتخاب شده : $TextArz
🌟 سطح حساب کاربر : $star
💸 موجودی کاربر : $coin

🔖 کد برداشت : $rand

⏳ ساعت و تاریخ : $time - $date
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید",'callback_data'=>"OK-$rand-$from_id-$send"],['text'=>"❌ لغو",'callback_data'=>"CANCEL-$rand-$from_id-$send"]],
]])
])->result->message_id;
$orders[$rand][$me] = $post_id;
Save("orders.json",$orders);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ ولت شما نباید بیشتر از 100 کاراکتر باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "✅ انجام معامله"){
$pdo->prepare("UPDATE `user` SET step = 'govasete' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 ایدی عدد طرف مقابل را ارسال کنید یا یک پیام از آن فوروارد کنید به ربات
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "govasete" and isset($text)){
if(isset($update->message->forward_date)){
if(isset($update->message->forward_sender_name)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ فوروارد کاربر مخفی است متاسفانه
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$forward_id = $update->message->forward_from->id;
if(GetInfo('user',['id'=>$forward_id])['id']){
if($from_id == $forward_id){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ شما با خودتان نمیتوانید معامله کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ از طریق لینک زیر وارد معامله شوید و درخواست واسطه کنید !

<a href ='https://t.me/$botuser?start=vasete_$forward_id'>کلیک کنید</a>
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 متاسفانه این کاربر در ربات نیست
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
}else{
if(is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ از طریق لینک زیر وارد معامله شوید و درخواست واسطه کنید !

<a href ='https://t.me/$botuser?start=vasete_$text'>کلیک کنید</a>
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 متاسفانه این کاربر در ربات نیست
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط عدد ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
}
if($text == "🌟 تبلیغات"){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌟 تبلیغات به بخش تبلیغاتی ربات $botname خوش آمدید
",
'parse_mode'=>'HTML',
'reply_markup'=>$advertising
]);
}
if($text == "✔️ ثبت تبلیغات"){
$pdo->prepare("UPDATE `user` SET step = 'SetAds' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 متن تبلیغاتی خود را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "SetAds" and isset($text)){
if(mb_strlen($text) <= 500){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$rand = rand(111111,999999);
$ads['Random'][] = $rand;
$ads[$rand]['id'] = $from_id;
$ads[$rand]['text'] = $text;
$ads[$rand]['time'] = $time;
$ads[$rand]['date'] = $date;
Save("ads.json",$ads);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تبلیغات شما با موفقیت ثبت شد !

🔖 کد تبلیغ : $rand
",
'parse_mode'=>'HTML',
'reply_markup'=>$advertising
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متن تبلیغاتی شما نباید بیشتر از 500 کاراکتر باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "🎈 مشاهده تبلیغات"){
$Random = $ads["Random"];
if(count($Random) != 0){
$Find = $Random[array_rand($Random)];
$adsId = $ads[$Find]['id'];
$adsText = $ads[$Find]['text'];
$adsTime = $ads[$Find]['time'];
$adsDate = $ads[$Find]['date'];
$name = bot('getChatMember',['chat_id'=>$adsId,'user_id'=>$adsId])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $Find

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"ads_$Find"]],
[['text'=>"📨 پیام به فروشنده",'callback_data'=>"PM-$adsId"],['text'=>"🚫 گزارش",'callback_data'=>"REPORT-$Find"]],
[['text'=>"🎲 بعدی ( رندوم )",'callback_data'=>"Random"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 متاسفانه تبلیغی پیدا نشد مجدداً امتحان کنید
",
'parse_mode'=>'HTML'
]);
}
}
if($text == "🔍 پیدا کردن تبلیغ"){
$pdo->prepare("UPDATE `user` SET step = 'SearchAds' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔖 کد تبلیغاتی مورد نظر خود را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "SearchAds" and isset($text)){
if(is_numeric($text)){
if(isset($ads[$text])){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$adsId = $ads[$text]['id'];
$adsText = $ads[$text]['text'];
$adsTime = $ads[$text]['time'];
$adsDate = $ads[$text]['date'];
$name = bot('getChatMember',['chat_id'=>$adsId,'user_id'=>$adsId])->result->user->first_name;
if($from_id != $adsId){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $text

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"ads_$text"]],
[['text'=>"📨 پیام به فروشنده",'callback_data'=>"PM-$adsId"],['text'=>"🚫 گزارش",'callback_data'=>"REPORT-$text"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $text

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"ads_$text"]],
[['text'=>"📨 پیام به فروشنده",'callback_data'=>"PM-$adsId"],['text'=>"🚫 گزارش",'callback_data'=>"REPORT-$text"]],
[['text'=>"🗑 حذف تبلیغ",'callback_data'=>"Delete-$text"]],
]])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کد تبلیغاتی ارسال شده پیدا نشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"PM-") and isset($text)){
$id = explode("-",$step)[1];
$name = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت به $name ارسال شد

⚠️ اگر پیام دیگری دارید ارسال کنید در غیر این صورت روی دکمه ( 🔙 برگشت ) بزنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🌹 شما یک پیام دارید از طرف $first_name دارید !

🔰 پیام : $text
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"📨 پیام به خریدار",'callback_data'=>"PM-$from_id"]],
]])
]);
}
if($text == "📧 تاییده با جیمیل" and $step == "GetTayd"){
if($datas['gmail'] != 'none'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🥂 شما قبلا تایید هویت با جی‌میل را انجام دادید 

✔️ جی‌میل ثبت شده : <code>{$datas['gmail']}</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>$confirmation
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'SetGmail' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 جهت تایید هویت با جی‌میل لطفاً جی‌میل خود را ارسال کنید

⚠️ مثال : Example@gmail.com
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($step == "SetGmail" and isset($text)){
if(preg_match("#^([\w\-\.]+)@gmail\.com$#i",$text,$m)){
$rand = rand(11111,99999);
$pdo->prepare("UPDATE `user` SET step = 'CodGmail-{$m[0]}-$rand' WHERE id = '$from_id' LIMIT 1")->execute();
$from = "code@vasete.api-bot.site";
$message = "
✅ کد تایید شما : $rand

❌ لطفاً کد را در اختیار هیچ کسی قرار ندهید

⏰ ساعت : ".Font($time)."

📆 تاریخ : ".Font($date)."
";
$headers = "From:" . $from;
mail($m[0],$botname,$message,$headers);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت یک کد به جی‌میل {$m[0]} ارسال شد

⚠️ لطفاً پوشه هرزنامه را هم چک کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً فقط جی‌میل ارسال کنید و بصورت صحیح ارسال کنید

⚠️ مثال : Example@gmail.com
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"CodGmail-") and isset($text) and is_numeric($text)){
$exp = explode("-",$step);
$email = $exp[1];
$code = $exp[2];
if($text == $code){
$pdo->prepare("UPDATE `user` SET step = 'GetTayd' , star = CONCAT('⭐️ ',star) , gmail = '$email' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تأیید هویت جی‌میل شما با موفقیت انجام شد !

✔️ جی‌میل ثبت شده : <code>$email</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>$confirmation
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ کد ارسال شده صحیح نمیباشد مجدد تلاش کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "❄️ تاییده با شماره تلفن" and $step == "GetTayd"){
if($datas['code'] == 'ok'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🥂 شما قبلا تایید هویت با شماره را انجام دادید 

✔️ شماره ثبت شده : <code>{$datas['phone']}</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>$confirmation
]);
}else{
$pdo->prepare("UPDATE `user` SET step = 'Phones' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 جهت تایید هویت با شماره روی دکمه ( 🔑 تایید شماره ) کلیک کنید و شماره خود را ارسال کنید

⚠️ ربات فقط یک بار برای شما کد ارسال میکند
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"🔑 تایید شماره",'request_contact'=>true]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true])
]);
}
}
if($step == "Phones" and isset($update->message->contact)){
if($update->message->contact->user_id == $from_id){
$phone = "+".($update->message->contact->phone_number);
if(str_contains($phone,"+98")){
if($datas['phone'] != 0){
$pdo->prepare("UPDATE `user` SET step = 'tayeedcod' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔖 قبلا کد تأیید به شماره <code>{$datas['phone']}</code> ارسال شده است لطفاً کد را وارد کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$send = '0'.strrev(substr(strrev($phone),0,10));
$rand = rand(11111,99999);
$pdo->prepare("UPDATE `user` SET step = 'tayeedcod' , phone = '$send' , code = '$rand' WHERE id = '$from_id' LIMIT 1")->execute();
file_get_contents("https://api.codebazan.ir/sms/api.php?type=sms&apikey=None&code=$rand&phone=$send");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت کد احراز هویت به شماره <code>$send</code> ارسال شد

🔢 لطفاً کد را به ربات ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ شماره شما مجازی است لطفاً با اکانت اصلی خود تأیید هویت کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط شماره تلفن خود را ارسال کنید نه شماره دیگران را
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($step == "tayeedcod" and isset($text) and is_numeric($text)){
if($text == $datas['code']){
$pdo->prepare("UPDATE `user` SET step = 'GetTayd' , star = CONCAT('⭐️ ',star) , code = 'ok' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تأیید هویت شماره شما با موفقیت انجام شد !

✔️ شماره ثبت شده : <code>{$datas['phone']}</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>$confirmation
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ کد ارسال شده صحیح نمیباشد مجدد تلاش کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if($tc == 'group' or $tc == 'supergroup'){
if($gaps['id'] == false){
$pdo->prepare("INSERT INTO `gaps` (`id` , `count` , `star` , `time` , `date`) VALUES ('$chat_id','0','⭐️','$time','$date')")->execute();
}
if(isset($newmem) and $newmem == $botid){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
🌹 تشکر بابت عضو کردن ربات در گروه $title_gap

💻 لیست کامند ها : <code>/commands</code>

🔗 تگ کردن : <code>/tag</code>

📚 راهنما : <code>/help</code>

⚖ قوانین : <code>/rules</code>

🔰 اطلاعات گپ : <code>/info</code>

❌ لغو معامله : <code>/cancel</code>

⏰ ساعت : ".Font($time)."

📆 تاریخ : ".Font($date)."

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML'
]);
}
if(str_contains($text,"/commands")){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
🔗 تگ کردن : <code>/tag</code>

📚 راهنما : <code>/help</code>

⚖ قوانین : <code>/rules</code>

🔰 اطلاعات گپ : <code>/info</code>

❌ لغو معامله : <code>/cancel</code>

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
if(str_contains($text,"/info")){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ تعداد معامله های موفق : $counts

🔋 سطح گروه : $starg

⏰ ساعت عضویت ربات : $timeg

📆 تاریخ عضویت ربات : $dateg

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
if(str_contains($text,"/tag")){
$admins = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatAdministrators?chat_id=$chat_id"),true);
foreach($admins['result'] as $a){
if($a['user']['is_bot'] == false){
$list .= "<a href ='tg://user?id={$a['user']['id']}'>{$a['user']['first_name']}</a> | <code>{$a['user']['id']}</code>\n";
}
}
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
📑 لیست ادمین های گروه :

$list
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
if(str_contains($text,"/help")){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
🌹 جهت معامله ابتدا باید روی پیام طرف مقابل خود ریپلی کنید و کلمه ( <code>طرفم</code> ) را بفرستید و سپس وارد معامله میشوید !


🎈 اگر معامله شما نیتروسین با ارز و اکانت و ... است کسی که قرار است نیتروسین را بدهد روی دکمه شیشه ای که ظاهر میشود کلیک کند و مقدار تعیین شده سکه از او کسر میشود و سپس طرف مقابل با خیال راحت میتوانید ارز و اکانت و ... رو تحویل دهد و بعد از تاییده گرفتن نیتروسین تعیین شده به حساب طرف مقابل اضافه میشود

⚠️ اگر معامله شما طاق اکانت با اکانت است یک نفر باید نیتروسین گرو بگذارید و بعد از معامله طاق اکانت با اکانت مجدد تحویل بگیرد لطفاً ابتدا اطمینان حاصل کنید از سالم بود و ... اکانت سپس تأیید کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
if(str_contains($text,"واسط")){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
🥂 لطفاً با مطالعه راهنما معامله خود را انجام دهید

📚 راهنما : <code>/help</code>
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
if(str_contains($text,"/cancel")){
if($gapid != 0){
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET vaseteid = '0' , step = 'none' , gapid = '0' WHERE id = '$ids' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
⚠️ معامله شما با کاربر $ids به اتمام رسید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ شما درحال معامله با کسی در گپ نیستید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
}
if(str_contains($text,"طرفم")){
if(isset($reply_id)){
if($reply_id == $from_id){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ شما نمیتوانید با خودتان معامله کنید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
if(GetInfo('user',['id'=>$from_id])['id'] == false){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ لطفاً ابتدا وارد ربات شوید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
if(GetInfo('user',['id'=>$reply_id])['id'] == false){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ کاربر $reply_name هنوز ربات رو استارت نکرده
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
if($ids != 0){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ متاسفانه شما هم اکنون در ربات هم در حال معامله هستید
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
$infoStep = json_decode(file_get_contents("data/$reply_id/$reply_id.json"),true);
if($infoStep['vaseteid'] != 0){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ متاسفانه کاربر $reply_name در ربات هم اکنون در حال معامله هست
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ اوکی طرف مقابل شما $reply_name ایشون است صبر کن تا تأیید کنه
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ بله تأیید میکنم",'callback_data'=>"Reval-$reply_id-$from_id-$reply_message"]],
]])
]);
}
}
}
}
}
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ لطفاً روی یکی از پیام های طرفت ریپلی کن
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
}
if($step == "Senits" and isset($text) and is_numeric($text)){
if($text >= 1000){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$TextCoin = number_format($text);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
💰 خب مقدار معامله شما $TextCoin سکه تعیین شد

💡 چه کسی قرار است نیتروسین را بدهد ؟
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎈 من باید نیتروسین رو بدم",'callback_data'=>"MeSet-$text-$ids-$from_id"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ حداقل سکه جهت معامله 1,000 سکه است
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
}
if($step == "Gero" and isset($text) and is_numeric($text)){
if($text >= 1000){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$TextCoin = number_format($text);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
🤔 خب چه کسی مقدار $TextCoin سکه برای طاق ( ارز و نیتروسین و جت ممبر و سرعت ممبر و .... ) گرو می‌گذارد ؟
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎈 من نیتروسین گرو میزارم",'callback_data'=>"GroSet-$text-$ids-$from_id"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ حداقل سکه جهت گرو گذاشتن 1,000 سکه است
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$message_id
]);
}
}
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if(in_array($from_id,$admin) and $tc == 'private'){
if($text == "/panel" or $text == "/admin"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 به پنل مدیریت خوش آمدید
",
'parse_mode'=>'HTML',
'reply_markup'=>$panel
]);
}
if($text == "📊 آمار"){
$count = $pdo->prepare("SELECT id FROM `user`");
$count->execute();
$countmember = $count->rowCount();
$count = $pdo->prepare("SELECT * FROM `user` WHERE step = 'block'");
$count->execute();
$countblock = $count->rowCount();
$code = $pdo->prepare("SELECT * FROM `user` WHERE date = '$date' ORDER BY `time` DESC LIMIT 3");
$code->execute();
foreach($code->fetchAll(PDO::FETCH_ASSOC) as $res){
$i++;
$id = $res['id'];
$Jointime = $res['time'];
$Joindate = str_replace('-','/',$res['date']);
$yournamer = bot('getChatMember',['chat_id'=>$id,'user_id'=>$id])->result->user->first_name;
$result .= "
🎈 $i « <a href ='tg://user?id=$id'>$yournamer</a> » 
📆 تاریخ عضویت : $Joindate
⏰ ساعت عضویت : $Jointime
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
";
}
for($list = [] , $i = 6;$i >= 0;$i--){
$today = jdate("Y/n/j",strtotime("-$i day"));
$count = $pdo->prepare("SELECT * FROM `user` WHERE date = '$today'");
$count->execute();
$list[$today] = $count->rowCount();
}
copy('https://api-bot.site/API/chartdate.php?json='.urlencode(Json($list)),'chart.png');
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>new CURLFile('chart.png'),
'caption'=>"
📉 تعداد کل کاربران ربات : $countmember

❌ تعداد کاربران بلاک شده : $countblock

📈 آخرین کاربران ربات :

$result
",
'parse_mode'=>'HTML',
'reply_markup'=>$panel
]);
unlink('chart.png');
}
if($text == "🏆 برترین ها"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🎊 برترین های بخش مورد نظر خود را انتخاب کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$bartarin
]);
}
if($text == "➕ افزایش سکه"){
$pdo->prepare("UPDATE `user` SET step = 'add' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "add" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'addcoin-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💰 مقدار سکه مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"addcoin-") and isset($text) and is_numeric($text)){
$id = explode("-",$step)[1];
if(is_numeric($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET coin = coin + '$text' WHERE id = '$id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت $text سکه به کاربر $id اضافه شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "➖ کسر سکه"){
$pdo->prepare("UPDATE `user` SET step = 'low' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "low" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'lowcoin-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💰 مقدار سکه مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"lowcoin-") and isset($text) and is_numeric($text)){
$id = explode("-",$step)[1];
if(is_numeric($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET coin = coin - '$text' WHERE id = '$id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت $text سکه از کاربر $id کم شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "💶 افزایش ارز"){
$pdo->prepare("UPDATE `user` SET step = 'addarz' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "addarz" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'addcurrency-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💰 مقدار ارز مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"addcurrency-") and isset($text) and is_numeric($text)){
if(is_numeric($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"$name 💶",'callback_data'=>"$step-$type-$text"]];
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❓ نوع ارز را مشخص کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "💷 کسر ارز"){
$pdo->prepare("UPDATE `user` SET step = 'lowarz' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "lowarz" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'lowcurrency-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💰 مقدار ارز مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"lowcurrency-") and isset($text) and is_numeric($text)){
if(is_numeric($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$key[] = [['text'=>"$name 💷",'callback_data'=>"$step-$type-$text"]];
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❓ نوع ارز را مشخص کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "❌ بلاک کردن"){
$pdo->prepare("UPDATE `user` SET step = 'blockid' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "blockid" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'block' WHERE id = '$text' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت کاربر $text بلاک شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"❌ متاسفانه شما از ربات بلاک شدید",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "🔰 آنبلاک کردن"){
$pdo->prepare("UPDATE `user` SET step = 'unblockid' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "unblockid" and isset($text) and is_numeric($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$text' LIMIT 1")->execute();
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت کاربر $text آنبلاک شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"♻️ شما از ربات آنبلاک شدید",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ کاربر $text در ربات نیست",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "📨 پیام همگانی"){
$pdo->prepare("UPDATE `user` SET step = 'sendall' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📨 پیام خود را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "sendall" and isset($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
$all = $pdo->query("SELECT * FROM `user`")->fetchAll(PDO::FETCH_UNIQUE);
foreach(array_keys($all) as $id){
bot('sendMessage',[
'chat_id'=>$id,
'text'=>$text,
'parse_mode'=>'HTML'
]);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت ارسال شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($text == "💭 فوروارد همگانی"){
$pdo->prepare("UPDATE `user` SET step = 'forall' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💭 پیام خود را فوروارد کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "forall" and isset($text)){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
$all = $pdo->query("SELECT * FROM `user`")->fetchAll(PDO::FETCH_UNIQUE);
foreach(array_keys($all) as $id){
bot('ForwardMessage',[
'chat_id'=>$id,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
]);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت ارسال شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($text == "🔍 اطلاعات کاربر"){
$pdo->prepare("UPDATE `user` SET step = 'PvInfo' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ ای دی عددی کاربر مورد نظر را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "PvInfo" and isset($text)){
$info = GetInfo('user',['id'=>$text]);
if($info['id']){
$name = bot('getChatMember',['chat_id'=>$text,'user_id'=>$text])->result->user->first_name;
$coin = $info['coin'];
$inv = $info['inv'];
$harvest = $info['harvest'];
$shop = $info['shop'];
$currency = Json($info['currency']);
$star = $info['star'];
$timej = $info['time'];
$datej = str_replace('-','/',$info['date']);
$gmail = $info['gmail'];
$phone = $info['phone'];
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📑 نام کاربر : $name

📆 تاریخ عضویت : $datej

⏰ ساعت عضویت : $timej

🎉 تعداد زیر مجموعه کاربر : $inv

💰 تعداد سکه کاربر : $coin

💸 مجموع برداشت : $harvest

💳 مجموع شارژ حساب : $shop

🔋 سطح کاربری : $star

✉️ جی‌میل کاربر : $gmail

📱 شماره تلفن : $phone
",
'parse_mode'=>'HTML'
]);
$list = "💶 ارز های کاربر :\n\n\n";
foreach(array_combine($type_currency,$name_currency) as $type => $name){
$coins = $currency[$type] ?? 0;
$list .= "💸 ارز $type | $name : $coins تا\n┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄\n";
}
$list .= "\n⏱ این گزارش وضعیت از کاربر مورد نظر در تاریخ $date ساعت $time گرفته شده است";
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>$list,
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️این کاربر در ربات نیست
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($text == "📩 پیام به کاربر"){
$pdo->prepare("UPDATE `user` SET step = 'PvSId' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ ای دی عددی کاربر مورد نظر را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "PvSId" and isset($text)){
if(GetInfo('user',['id'=>$text])['id']){
$pdo->prepare("UPDATE `user` SET step = 'PvPm-$text' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💬 پیام خود را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️این کاربر در ربات نیست
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(str_contains($step,"PvPm-") and isset($text)){
$id = explode("-",$step)[1];
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت به کاربر ( <code>$id</code> ) ارسال شد
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🌹 شما یک پیام از پشتیبانی دارید 

✔️ پیام : <code>$text</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🆘 پیام به پشتیبانی",'callback_data'=>"support"]],
]])
]);
}
if(isset($update->message->reply_to_message) and isset($text)){
$forep_id = $update->message->reply_to_message->forward_from->id;
if(GetInfo('user',['id'=>$forep_id])['id']){
$pdo->prepare("UPDATE `user` SET step = 'none' WHERE id = '$from_id' LIMIT 1")->execute();
bot('sendMessage',[
'chat_id'=>$forep_id,
'text'=>"
🌹 شما یک پیام از پشتیبانی دارید 

✔️ پیام : <code>$text</code>
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🆘 پیام به پشتیبانی",'callback_data'=>"support"]],
]])
]);
$nameuser = bot('getChatMember',['chat_id'=>$forep_id,'user_id'=>$forep_id])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت پیام شما به کاربر ( <code>$nameuser</code> ) ارسال شد",
'parse_mode'=>'HTML'
]);
}
}
}
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
//// source kade 
//// open @ESRRAR
/*
👑 اوپن شده در سورس کده 😎

! [ @Sourrce_Kade ] !
! [ @Sourrce_Kade ] !

جریان اوپن شده این سورس:
https://t.me/jaryanopen/2
آموزش ران قسمت php :
https://t.me/sourcekaderun/2
*/
if($query == "share" and GetInfo('user',['id'=>$from_id])['id']){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>'1',
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/181',
'title'=>'❗️اینجا کلیک کنید',
'input_message_content'=>[
'message_text'=>"
👤 شناسه کاربری : $from_id
📆 تاریخ عضویت : $datej
⏰ ساعت عضویت : $timej
🎉 تعداد زیرمجموعه : $inv نفر

💳 مجموع شارژ حساب : $shop
💸 مجموع برداشت : $harvest
🔋 سطح کاربری : $star
💰 موجودی : $coin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true
],'reply_markup'=>['inline_keyboard'=>[
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share"]],
]
]
]
])
]);
}
if(str_contains($query,"ads_")){
$Find = explode("_",$query)[1];
if(isset($ads[$Find])){
$adsId = $ads[$Find]['id'];
$adsText = $ads[$Find]['text'];
$adsTime = $ads[$Find]['time'];
$adsDate = $ads[$Find]['date'];
$name = bot('getChatMember',['chat_id'=>$adsId,'user_id'=>$adsId])->result->user->first_name;
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>'1',
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/182',
'title'=>'❗️اینجا کلیک کنید',
'input_message_content'=>[
'message_text'=>"
📑 نام فروشنده : $name

🔖 کد تبلیغاتی : $Find

🎈 متن تبلیغاتی :

$adsText


📆 تاریخ ثبت تبلیغ : $adsDate
⏰ ساعت ثبت تبلیغ : $adsTime

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
🔗 Link : https://t.me/$botuser?start=vasete_$adsId
",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true
],'reply_markup'=>['inline_keyboard'=>[
[['text'=>"🎲 بعدی ( رندوم )",'callback_data'=>"RandomQuery"]],
]
]
]
])
]);
}else{
bot("AnswerInlineQuery",[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'switch_pm_text'=>"⚠️ متاسفانه این تبلیغ پیدا نشد",
'switch_pm_parameter'=>"ads"
]);
}
}