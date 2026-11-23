<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
http_response_code(200);
litespeed_finish_request();
include("jdf.php");
//===[توکن]===//
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');
//===[امنیت دامنه]===//
$telegram_ip_ranges = [
['lower'=>'149.154.160.0', 'upper'=>'149.154.175.255'], // literally 149.154.160.0/20
['lower'=>'91.108.4.0',    'upper'=>'91.108.7.255'],    // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
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
if(strpos($text,'api') !== false or strpos($text,'key') !== false){
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
if(strpos($text,'bot') !== false or strpos($text,'function') !== false){
return 'ok';
}
if(strpos($text,'telegram') !== false or strpos($text,'http') !== false){
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
function Number($string) {
if(isset($string)){
$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
$arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
$ChangePersian = str_replace($persian, range(0, 9) , $string);
$ChangeArabic = str_replace($arabic, range(0, 9) , $ChangePersian);
if(is_numeric($ChangeArabic) and $ChangeArabic != 0) $ChangeArabic = abs($ChangeArabic);
return $ChangeArabic;
}
}
function JoinChek($ch,$user){
foreach($ch as $links){
$chekjoin = bot('getChatMember',[
'chat_id'=>$links,
'user_id'=>$user
])->result->status;
if($chekjoin != 'member' and $chekjoin != 'creator' and $chekjoin != 'administrator'){
return $links;
}
}
return "OK";
}
function Save($link,$data){
$outjson = json_encode($data,true);
file_put_contents($link,$outjson);
}
function Font($text){
$font = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$echo = str_replace(range(0,9),$font[array_rand($font)],$text);
return $echo;
}
function Lottery($code){
global $lottery;
$inline_id = $lottery[$code]['msg_id'];
$count = sizeof($lottery[$code]['list']);
$rand = rand(1,$count);
for($i = 1;$i <= $count;$i++){
$f = Font($i);
if($i == $rand){
$ids = $lottery[$code]['list'][$rand - 1];
$user = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result;
$info_name = $user->user->first_name;
$info_user = $user->user->username ?? '𝑛𝑜𝑛𝑒';
$info_language = $user->user->language_code ?? '𝑛𝑜𝑛𝑒';
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎉 کاربر شماره $rand برنده شد",'callback_data'=>"fake"]],
[['text'=>"$info_name",'callback_data'=>"fake"],['text'=>"📑 نام کاربر",'callback_data'=>"fake"]],
[['text'=>"@$info_user",'callback_data'=>"fake"],['text'=>"🆔 یوزرنیم",'callback_data'=>"fake"]],
[['text'=>"$ids",'callback_data'=>"fake"],['text'=>"🔢 ایدی عددی",'callback_data'=>"fake"]],
[['text'=>"$info_language",'callback_data'=>"fake"],['text'=>"🌹 زبان کاربر",'callback_data'=>"fake"]],
]])
]);
$info = json_decode(file_get_contents("data/$ids/$ids.json"),true);
$info['win'] += 1;
Save("data/$ids/$ids.json",$info);
break;
}else{
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"♻️ درحال قرعه : $f",'callback_data'=>"fake"]],
]])
]);
usleep(500000);
}
}
return $rand;
}
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)){
$chat_id = $update->message->chat->id;
$text = Number($update->message->text);
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$user_name = $update->message->from->username ?? '𝑛𝑜𝑛𝑒';
}
if (isset($update->callback_query)){
$chat_id = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$inline_message_id = $update->callback_query->inline_message_id;
$from_id = $update->callback_query->from->id;
$first_name = $update->callback_query->from->first_name;
$last_name = $update->callback_query->from->last_name;
$user_name = $update->callback_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
if (isset($update->inline_query)){
$chat_id = $update->inline_query->message->chat->id;
$inline = $update->inline_query;
$user_id = $update->inline_query->id;
$query = $update->inline_query->query;
$from_id = $update->inline_query->from->id;
$tc = $update->inline_query->chat_type;
$first_name = $update->inline_query->from->first_name;
$last_name = $update->inline_query->from->last_name;
$user_name = $update->inline_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
if(!file_exists("data")) mkdir("data");
if(!file_exists("data/$from_id")) mkdir("data/$from_id");
//===[ربات]===//
$get = bot('getMe');
$botid = $get->result->id;
$botuser = $get->result->username;
$botname = $get->result->first_name;
//===[امنیت]===//
if(Security($text) == 'ok') exit();
//===[دیتابیس]===//
$settings = json_decode(file_get_contents("settings.json"),true);
$ZirCoin = $settings['zir'] ?: 300;
$ZirLink = $settings['link'] ?: "https://t.me/Mafia_nit/152";
$ZirText = $settings['text'] ?: "https://t.me/$botuser?start=$from_id";
$Size = $settings['size'] ?: true;
$Support = $settings['size'] ?: "Telegram";
$Channels = isset($settings['channel']) ? $settings['channel'] : [];
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$step = $datas['step'];
$coin = $datas['coin'];
$shop = $datas['shop'];
$inv = $datas['inv'];
$harvest = $datas['harvest'];
$win = $datas['win'];
$ticket = $datas['ticket'];
$timej = $datas['time'];
$datej = $datas['date'];
$transfer = json_decode(file_get_contents("transfer.json"),true);
$orders = json_decode(file_get_contents("orders.json"),true);
$lottery = json_decode(file_get_contents("lottery.json"),true);
//-----------------------------------------------------------------------------------------------
$admin = [1725955696];
$date = jdate("Y/n/j");
$time = jdate("H:i:s");
$timer = time();
//-----------------------------------------------------------------------------------------------
if($step == "block" and in_array($from_id,$admin) === false) exit();
//-----------------------------------------------------------------------------------------------
$menu = json_encode(['keyboard'=>[
[['text'=>"🔐 حساب کاربری"]],
[['text'=>"💸 انتقال سکه"],['text'=>"🎲 ثبت قرعه"]],
[['text'=>"💰 برداشت"],['text'=>"🛍 شارژ حساب"]],
[['text'=>"🆘 پشتیبانی"],['text'=>"📚 راهنما"]],
],'resize_keyboard'=>$Size]);

$back = json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>$Size]);

$panel = json_encode(['keyboard'=>[
[['text'=>"📊 آمار"]],
[['text'=>"➖ کسر سکه"],['text'=>"➕ افزایش سکه"]],
[['text'=>"📢 کانال جوین اجباری"]],
[['text'=>"📨 پیام همگانی"],['text'=>"💭 فوروارد همگانی"]],
[['text'=>"📩 پیام به کاربر"]],
[['text'=>"🔰 آنبلاک کردن"],['text'=>"❌ بلاک کردن"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>$Size]);

$account = json_encode(['inline_keyboard'=>[
[['text'=>"♻️ آپدیت",'callback_data'=>"update"]],
[['text'=>"🔗 اشتراک گذاری",'switch_inline_query'=>"share"],['text'=>"🎊 زیر مجموعه گیری",'callback_data'=>"invite"]],
[['text'=>"❌ بستن پنل",'callback_data'=>"close"]],
]
]);

$Acc = json_encode(['inline_keyboard'=>[
[['text'=>"🔐 حساب کاربری",'callback_data'=>"update"]],
]
]);
//-----------------------------------------------------------------------------------------------
if($data == "close"){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
❌ پنل با موفقیت بسته شد
",
'parse_mode'=>'HTML',
'reply_markup'=>$Acc
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
🎫 تعداد بلیط های خریداری شده : $ticket
🎟 تعداد برنده شدن : $win
💰 موجودی : $coin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML',
'reply_markup'=>$account
]);
}
if($data == "invite"){
$post_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$ZirLink,
'caption'=>$ZirText,
'parse_mode'=>'HTML',
])->result->message_id;
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎊 بابت هر زیر مجموعه گیری با لینک خود $ZirCoin سکه دریافت کنید !
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$post_id,
'reply_markup'=>$menu
]);
}
if($data == "NO"){
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"❌ درخواست برداشت لغو شد",'callback_data'=>"fake"]]
]
])
]);
}
if(strpos($data,"YES-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
$ids = $exp[2];
$coins = $exp[3];
if($coin >= $coins){
$TextCoin = number_format($coins);
$datas['coin'] -= $coins;
$datas['harvest'] += $coins;
Save("data/$from_id/$from_id.json",$datas);
bot('editmessagetext',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
✅ درخواست برداشت شما تأیید شد و به ادمین ها ارسال شد

🔖 کد برداشت : $code
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➖ مقدار $TextCoin سکه از شما کم شد",'callback_data'=>"fake"]]
]
])
]);
foreach($admin as $me){
$post_id = bot('sendMessage',[
'chat_id'=>$me,
'text'=>"✅ یک نفر درخواست برداشت سکه کرده است 

✔️ نام کاربر : $first_name
🆔 ایدی عددی کاربر : $from_id
💰 مقدار سکه درخواستی : $coins
🔋 آیدی عددی ارسال شده : $ids
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
$orders[$code][$me] = "$post_id";
Save("orders.json",$orders);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ سکه های شما برای تأیید این برداشت کافی نیست",
'show_alert'=>true
]);
}
exit();
}
if(strpos($data,"OK-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
$ids = $exp[2];
$coins = $exp[3];
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
✅ درخواست برداشت با کد $code تأیید شد

💰 مقدار سکه درخواستی نیتروسین : $coins
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
'parse_mode'=>'HTML',
]);
}
unset($orders[$code]);
Save("orders.json",$orders);
}
if(strpos($data,"CANCEL-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
$ids = $exp[2];
$coins = $exp[3];
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name;
bot('sendMessage',[
'chat_id'=>$ids,
'text'=>"
❌ درخواست برداشت با کد $code رد شد

💰 مقدار سکه درخواستی نیتروسین : $coins
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
'parse_mode'=>'HTML',
]);
}
unset($orders[$code]);
Save("orders.json",$orders);
}
if($data == "support"){
$datas['step'] = "Support";
Save("data/$from_id/$from_id.json",$datas);
bot('answercallbackquery', [
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
if($data == "nosupport"){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('answercallbackquery', [
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
'parse_mode'=>'HTML',
]);
exit();
}
if(strpos($data,"ListTicket-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
if(!file_exists("data/$from_id/$from_id.json")){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔰 لطفاً ابتدا وارد ربات شوید : @$botuser",
'show_alert'=>true
]);
exit();
}
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🎈 لیست کسانی که بلیط خریدن داخل پیوی شما ارسال شد",
'show_alert'=>true
]);
if(isset($lottery[$code]['list'])){
foreach($lottery[$code]['list'] as $ids){
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name ?? '𝑛𝑜𝑛𝑒';
$i++;
$lists = "$lists".Font($i)." - $name\n";
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
💸 لیست کسانی که بلیط خریدن

$lists

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••",
'parse_mode'=>'HTML',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 متاسفانه کسی هنوز بلیط نخریده است",
'parse_mode'=>'HTML',
]);
}
}
if(strpos($data,"BuyTicket-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
if(!file_exists("data/$from_id/$from_id.json")){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔰 لطفاً ابتدا وارد ربات شوید : @$botuser",
'show_alert'=>true
]);
exit();
}
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ لطفاً پیوی ربات را چک کنید پیامی برای شما ارسال شده است",
'show_alert'=>true
]);
$lotteryTickets = $lottery[$code]['tickets'];
$lotteryCoin = $lottery[$code]['coin'];
if($lotteryTickets <= 0){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متاسفانه بلیطی جهت خرید باقی نمانده است",
'parse_mode'=>'HTML',
]);
}else{
if($coin >= $lotteryCoin){
$datas['step'] = "BuyTicket-$code";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🤔 بلیط شماره چند را میخواهید بخرید ؟",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ موجودی شما جهت خرید بلیط کافی نیست قیمت هر بلیط ".number_format($lotteryCoin)." سکه است

💰 موجودی شما : ".number_format($coin)." سکه
",
'parse_mode'=>'HTML',
]);
}
}
}
if(strpos($data,"starts-") !== false){
$exp = explode("-",$data);
$ids = $exp[1];
$code = $exp[2];
if($from_id == $ids){
if(isset($lottery[$code]['msg_id'])){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این قرعه کشی در جای دیگری شروع شده است",
'show_alert'=>true
]);
}else{
$lottery[$code]['msg_id'] = $inline_message_id;
Save("lottery.json",$lottery);
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎉 جهت خرید بلیط کلیک کنید",'callback_data'=>"BuyTicket-$code"]],
[['text'=>"📁 لیست کسانی که بلیط خریدن",'callback_data'=>"ListTicket-$code"]],
[['text'=>"🗑 حذف قرعه کشی",'callback_data'=>"DeleteTicket-$code"]],
]])
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما نمیتوانید قرعه کشی را شروع کنید",
'show_alert'=>true
]);
}
}
if(strpos($data,"Set-") !== false){
$exp = explode("-",$data);
$type = $exp[1];
$code = $exp[2];
$TextSend = str_replace(["text","tickets","coin"],["🎈 توضیحات جدید را جهت تغییر ارسال کنید","🎈 تعداد بلیط ها را جهت تغییر ارسال کنید","🎈 تعداد سکه های هر بلیط را جهت تغییر ارسال کنید"],$type);
if(isset($lottery[$code])){
if(isset($lottery[$code]['msg_id'])){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ متاسفانه کد قرعه کشی $code در حال برگزاری هست و شما نمیتوانید تغییری ایجاد کنید",
'show_alert'=>true
]);
}else{
if($type == "delete"){
unset($lottery[$code]);
Save("lottery.json",$lottery);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🗑 کد قرعه کشی $code با موفقیت حذف شد",
'show_alert'=>true
]);
exit();
}
$datas['step'] = "$data-$message_id";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>$TextSend,
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ قرعه کشی کد $code وجود ندارد",
'show_alert'=>true
]);
}
}
if(strpos($data,"DeleteTicket-") !== false){
$exp = explode("-",$data);
$code = $exp[1];
if(isset($lottery[$code])){
$lotteryId = $lottery[$code]['id'];
$lotteryCoin = $lottery[$code]['coin'];
if($from_id == $lotteryId){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ درحال حذف قرعه کشی . . .",
'show_alert'=>true
]);
if(isset($lottery[$code]['list'])){
foreach($lottery[$code]['list'] as $id){
$info = json_decode(file_get_contents("data/$id/$id.json"),true);
$info['coin'] += $lotteryCoin;
Save("data/$id/$id.json",$info);
}
}
bot('editMessageText',[
'inline_message_id'=>$lottery[$code]['msg_id'],
'text'=>"
🔰 کد قرعه کشی $code با موفقیت حذف شد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'disable_web_page_preview'=>true,
'parse_mode'=>'HTML',
]);
unset($lottery[$code]);
Save("lottery.json",$lottery);
exit();
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ شما نمیتوانید این قرعه کشی را حذف کنید",
'show_alert'=>true
]);
}
}
}
if($data == "fake"){
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"💤 این دکمه فقط جهت نمایش اطلاعات است",
'show_alert'=>false
]);
}
//-----------------------------------------------------------------------------------------------
if(in_array($from_id,$admin)){
if($data == "addjoin"){
$datas['step'] = "SetChannel";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📢 آیدی کانال جدید را با @ ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif(strpos($data,"deljoin-") !== false){
$exp = explode("-",$data);
$links = $exp[1];
$search = array_search($links,$Channels);
unset($settings['channel'][$search]);
$settings['channel'] = array_values($settings['channel']);
Save("settings.json",$settings);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🗑 با موفقیت کانال $links حذف شد
",
'parse_mode'=>'HTML'
]);
}
}
//-----------------------------------------------------------------------------------------------
if(preg_match('/^\/([Ss][Tt][Aa][Rr][Tt])(.*)/',$text,$INV)){
if(file_exists("data/$from_id/$from_id.json")){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 سلام به ربات $botname خوش آمدید !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}else{
$invite = preg_replace('![^\d]*!','',$INV[2]);
if(file_exists("data/$invite/$invite.json") and $invite != $from_id){
$datas['my'] = $invite;
}
$datas['step'] = "none";
$datas['coin'] = "0";
$datas['shop'] = "0";
$datas['inv'] = "0";
$datas['harvest'] = "0";
$datas['win'] = "0";
$datas['ticket'] = "0";
$datas['time'] = "$time";
$datas['date'] = "$date";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 سلام به ربات $botname خوش آمدید !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
exit();
}
if(!file_exists("data/$from_id/$from_id.json")){
$datas['step'] = "none";
$datas['coin'] = "0";
$datas['shop'] = "0";
$datas['inv'] = "0";
$datas['harvest'] = "0";
$datas['win'] = "0";
$datas['ticket'] = "0";
$datas['time'] = "$time";
$datas['date'] = "$date";
Save("data/$from_id/$from_id.json",$datas);
}
if(isset($settings['channel'])){
$JoinMe = JoinChek($settings['channel'],$from_id);
if($JoinMe != "OK"){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🔰 لطفاً جهت حمایت از ما و به دلیل رایگان بودن ربات در کانال های اسپانسری ما عضو شوید !

🔗 Channel : $JoinMe
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
exit();
}
}
if(isset($datas['my'])){
$id = $datas['my'];
$info = json_decode(file_get_contents("data/$id/$id.json"),true);
$info['coin'] += $ZirCoin;
$info['inv'] += 1;
Save("data/$id/$id.json",$info);
unset($datas['my']);
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 کاربر <a href ='tg://user?id=$from_id'>$first_name</a> با لینک شما وارد ربات شد و به شما $ZirCoin سکه اضافه شد !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
if(strpos($text,'انتقال داده شد') !== false){
$NitroSeenBot = $update->message->forward_from->id;
if($NitroSeenBot == "977348093"){
$explode = explode(' ',$text);
$CoinSend = $explode[2];
$IdSend = $explode[13];
$TimeSend = $explode[8];
$DateSend = $explode[6];
if($DateSend == $date){
if(in_array($IdSend,$admin)){
if(!in_array($TimeSend,$transfer["Today"])){
$transfer["Today"][] = "$TimeSend";
$transfer["id"][] = "$from_id";
Save("transfer.json",$transfer);
$datas['step'] = "none";
$datas['coin'] += $CoinSend;
$datas['shop'] += $CoinSend;
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت حساب کاربری شما مقدار <code>$CoinSend</code> سکه شارژ شد !",
'parse_mode'=>'HTML',
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ شما قبلا بابت این انتقال سکه موجودی دریافت کردید",
'parse_mode'=>'HTML',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این انتقال سکه برای ادمین های ربات ما نیست",
'parse_mode'=>'HTML',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این انتقال سکه برای امروز نمی‌باشد",
'parse_mode'=>'HTML',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ این پیام از نیتروسین فوروارد نشده است",
'parse_mode'=>'HTML',
]);
}
}
if($text == "🔙 برگشت"){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔙 به منوی اصلی برگشتید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
exit();
}
if($text == "🔐 حساب کاربری"){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
👤 شناسه کاربری : $from_id
📆 تاریخ عضویت : $datej
⏰ ساعت عضویت : $timej
🎉 تعداد زیرمجموعه : $inv نفر

💳 مجموع شارژ حساب : $shop
💸 مجموع برداشت : $harvest
🎫 تعداد بلیط های خریداری شده : $ticket
🎟 تعداد برنده شدن : $win
💰 موجودی : $coin

⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است
",
'parse_mode'=>'HTML',
'reply_markup'=>$account
]);
}
if($text == "📚 راهنما"){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📚 راهنما ربات :

💤 ربات قرعه کشی بصورتی است که خودکار ظرفیت رو تکمیل میکنه و بابت برگزاری هر قرعه کشی فقط مبلغ 1,000 سکه برای خود ربات برداشته میشود


🔰 شما یک قرعه کشی می‌سازید و می‌توانید آنرا تغییر دهید و اگر تمام بلیط ها فروش بروند ربات خودکار قرعه کشی را آغاز میکند و بعد از اتمام قرعه کشی تمام سکه ها به حساب شخصی که قرعه کشی را ایجاد کرده است اضافه میشود !


🔖 لطفاً قرعه کشی دیگران را انجام ندهید با لینک خودشان اگر میخواهید خودتان لینکی ایجاد کنید به دلیل اینکه تمام سکه های قرعه کشی به حساب آن شخص واریز میشوند !


🎈 قرعه کشی فقط در کانال ها انجام می‌شوند نه در گروه ها و پیوی ها !


⚠️ ما هیچ کانالی که از ربات ها استفاده کنند را تأیید نمیکنیم پس لطفاً در کانال های نامعتبر قرعه کشی انجام ندهید و شرکت نکنید !

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
if($text == "🆘 پشتیبانی"){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🆘 به بخش پشتیبانی خوشـ آمدید",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🆘 پشتیبانی آنلاین",'callback_data'=>"support"]],
[['text'=>"🆘 پشتیبانی مستقیم",'url'=>"https://t.me/$Support"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
[['text'=>"$date",'callback_data'=>'fake'],['text'=>'📆 تاریخ ◄','callback_data'=>'fake']],
[['text'=>"$time",'callback_data'=>'fake'],['text'=>'⏰ ساعت ◄','callback_data'=>'fake']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'fake']],
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
'parse_mode'=>'HTML',
]);
}
}
if($text == "🛍 شارژ حساب"){
foreach($admin as $devs){
$i++;
$list = "$list".Font($i)." - <code>$devs</code>\n";
}
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🌹 جهت شارژ حساب فقط کافیست مقدار سکه مورد نیاز خود را به یکی از حساب های کاربری زیر انتقال بزنید ( نیتروسین فقط )

$list

⚠️ نکته : بعد از انتقال سکه در ربات نیتروسین انتقال سکه خود را برای ربات فوروارد کنید تا در ربات حساب شما شارژ شود !
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
if($text == "💸 انتقال سکه"){
if($coin >= 1000){
$datas['step'] = "Transfer";
Save("data/$from_id/$from_id.json",$datas);
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
$datas['step'] = "SendId-$text";
Save("data/$from_id/$from_id.json",$datas);
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
'text'=>"❌ لطفاً مقدار سکه جهت برداشت را با توجه به موجودی خود ارسال کنید

✅ موجودی شما : $coin
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ حداقل سکه جهت برداشت 1,000 و حداکثر سکه جهت برداشت 1,000,000 سکه می‌باشد",
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
if(strpos($step,"SendId-") !== false and isset($text)){
$send = explode("-",$step)[1];
$TextCoin = number_format($send);
if(is_numeric($text)){
if($from_id != $text){
if(file_exists("data/$text/$text.json")){
$info = json_decode(file_get_contents("data/$text/$text.json"),true);
$info['coin'] += $send;
Save("data/$text/$text.json",$info);
$datas['step'] = "none";
$datas['coin'] -= $send;
Save("data/$from_id/$from_id.json",$datas);
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
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
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
if($coin >= 1000){
$datas['step'] = "harvest";
Save("data/$from_id/$from_id.json",$datas);
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
'text'=>"❌ حداقل سکه جهت برداشت 1,000 سکه است",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if($step == "harvest" and isset($text)){
if(is_numeric($text)){
if($text >= 1000 and $text <= 100000){
if($coin >= $text){
$datas['step'] = "Bardasht-$text";
Save("data/$from_id/$from_id.json",$datas);
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
if(strpos($step,"Bardasht-") !== false and isset($text)){
$send = explode("-",$step)[1];
$TextCoin = number_format($send);
if(is_numeric($text)){
if(mb_strlen($text) < 11){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
$rand = rand(11111,99999);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ درخواست برداشت $TextCoin سکه نیتروسین به کاربری $text و با کد برداشت $rand مورد تایید شما است ؟
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"✅ تأیید",'callback_data'=>"YES-$rand-$text-$send"],['text'=>"❌ لغو",'callback_data'=>"NO"]],
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
if($text == "🎲 ثبت قرعه"){
$datas['step'] = "SetLot";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📑 لطفاً متن مربوط به قرعه کشی را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
if($step == "SetLot" and isset($text)){
if(mb_strlen($text) <= 300){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
$rand = rand(111111,999999);
$lottery[$rand]['id'] = "$from_id";
$lottery[$rand]['coin'] = "1000";
$lottery[$rand]['tickets'] = "10";
$lottery[$rand]['text'] = "$text";
$lottery[$rand]['time'] = "$time";
$lottery[$rand]['date'] = "$date";
Save("lottery.json",$lottery);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ متن با موفقیت ثبت شد

✔️ کد قرعه کشی : ( <code>$rand</code> )


🎫 تعداد بلیط ها : 10

💸 قیمت هر بلیط : 1000


🎈 توضیحات مربوط به قرعه کشی :

$text


⏱ این قرعه کشی در تاریخ $date و ساعت $time ساخته شده است

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"📝 تغییر توضیحات",'callback_data'=>"Set-text-$rand"],['text'=>"🔗 شروع قرعه کشی",'switch_inline_query'=>"NewLottery_$rand"]],
[['text'=>"💰 تغییر قیمت هر بلیط",'callback_data'=>"Set-coin-$rand"],['text'=>"🔖 تغییر تعداد بلیط ها",'callback_data'=>"Set-tickets-$rand"]],
[['text'=>"🗑 حذف کردن",'callback_data'=>"Set-delete-$rand"]],
]
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متن شما حداکثر باید 300 کاراکتر باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
if(strpos($step,"BuyTicket-") !== false and isset($text) and is_numeric($text)){
$exp = explode("-",$step);
$code = $exp[1];
$set = $text - 1;
$lotteryTickets = $lottery[$code]['tickets'];
$lotteryCoin = $lottery[$code]['coin'];
if(!isset($lottery[$code]['list'])){
$lottery[$code]['list'] = array_fill(0,$lotteryTickets,"0");
Save("lottery.json",$lottery);
}
$sizeof = sizeof($lottery[$code]['list']);
if($text > $sizeof){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ تعداد کل بلیط ها $sizeof تا است",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
if($lotteryTickets <= 0){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متاسفانه بلیطی جهت خرید باقی نمانده است",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
if($lottery[$code]['list'][$set] == "0"){
$datas['coin'] -= "$lotteryCoin";
$datas['ticket'] += "1";
Save("data/$from_id/$from_id.json",$datas);
$lottery[$code]['list'][$set] = "$from_id";
$lottery[$code]['tickets'] -= "1";
Save("lottery.json",$lottery);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت بلیط شماره‌ی $text خریداری شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
$find = array_search("0",$lottery[$code]['list']);
$find = ($find == '0') ? str_replace("0","YES",$find) : $find;
if(empty($find)){
foreach($lottery[$code]['list'] as $ids){
$name = bot('getChatMember',['chat_id'=>$ids,'user_id'=>$ids])->result->user->first_name ?? '𝑛𝑜𝑛𝑒';
$i++;
$lists = "$lists".Font($i)." - $name\n";
}
bot('editMessageText',[
'inline_message_id'=>$lottery[$code]['msg_id'],
'text'=>"
🎉 درحال قرعه کشی

$lists
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

🔰 کاربر $first_name بلیط آخر شماره $text را خرید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'disable_web_page_preview'=>true,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎉 جهت خرید بلیط کلیک کنید",'callback_data'=>"BuyTicket-$code"]],
[['text'=>"📁 لیست کسانی که بلیط خریدن",'callback_data'=>"ListTicket-$code"]],
[['text'=>"🗑 حذف قرعه کشی",'callback_data'=>"DeleteTicket-$code"]],
]])
]);
$lotteryId = $lottery[$code]['id'];
$who = Lottery($code);
$adds = (sizeof($lottery[$code]['list']) * $lotteryCoin) - 1000;
$info = json_decode(file_get_contents("data/$lotteryId/$lotteryId.json"),true);
$info['coin'] += $adds;
Save("data/$lotteryId/$lotteryId.json",$info);
bot('sendMessage',[
'chat_id'=>$lotteryId,
'text'=>"🌹 کاربر شماره‌ی $who در قرعه کشی برنده شد و به حساب شما ".number_format($adds)." سکه اضافه شد !",
'parse_mode'=>'HTML',
]);
unset($lottery[$code]);
Save("lottery.json",$lottery);
}else{
bot('editMessageText',[
'inline_message_id'=>$lottery[$code]['msg_id'],
'text'=>"
🎫 تعداد بلیط باقی مانده : {$lottery[$code]['tickets']}

💸 قیمت هر بلیط : {$lottery[$code]['coin']}


🎈 توضیحات مربوط به قرعه کشی :

{$lottery[$code]['text']}


⏱ این قرعه کشی در تاریخ {$lottery[$code]['date']} و ساعت {$lottery[$code]['time']} ساخته شده است

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

🔰 کاربر $first_name بلیط شماره $text را خرید

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'disable_web_page_preview'=>true,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🎉 جهت خرید بلیط کلیک کنید",'callback_data'=>"BuyTicket-$code"]],
[['text'=>"📁 لیست کسانی که بلیط خریدن",'callback_data'=>"ListTicket-$code"]],
[['text'=>"🗑 حذف قرعه کشی",'callback_data'=>"DeleteTicket-$code"]],
]])
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 متاسفانه بلیط شماره $text از قبل خریداری شده است",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
}
}
if(strpos($step,"Set-") !== false and isset($text)){
$exp = explode("-",$step);
$type = $exp[1];
$code = $exp[2];
$messageId = $exp[3];
if(isset($lottery[$code])){
if(isset($lottery[$code]['msg_id'])){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متاسفانه کد قرعه کشی $code در حال برگزاری هست و شما نمیتوانید تغییری ایجاد کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
if($type == "text" and mb_strlen($text) > 300){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ متن شما حداکثر باید 300 کاراکتر باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
exit();
}
if($type == "tickets"){
if(is_numeric($text)){
if($text >= 50 and $text <= 2){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ تعداد بلیط ها باید حداقل 2 تا باشد و حداکثر 50 بلیط باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
exit();
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
exit();
}
}
if($type == "coin"){
if(is_numeric($text)){
if($text >= 10000000 and $text <= 1000){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ تعداد سکه های هر بلیط باید حداقل 1,000 سکه باشد و حداکثر 10,000,000 سکه باشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
exit();
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ لطفاً فقط عدد ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
exit();
}
}
$lottery[$code][$type] = "$text";
Save("lottery.json",$lottery);
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ با موفقیت تغییر پیدا کرد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
$lotteryTickets = $lottery[$code]['tickets'];
$lotteryCoin = $lottery[$code]['coin'];
$lotteryText = $lottery[$code]['text'];
$lotteryTime = $lottery[$code]['time'];
$lotteryDate = $lottery[$code]['date'];
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$messageId,
'text'=>"
✅ متن با موفقیت ثبت شد

✔️ کد قرعه کشی : ( <code>$code</code> )


🎫 تعداد بلیط ها : $lotteryTickets

💸 قیمت هر بلیط : $lotteryCoin


🎈 توضیحات مربوط به قرعه کشی :

$lotteryText


⏱ این قرعه کشی در تاریخ $lotteryDate و ساعت $lotteryTime ساخته شده است

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"📝 تغییر توضیحات",'callback_data'=>"Set-text-$code"],['text'=>"🔗 شروع قرعه کشی",'switch_inline_query'=>"NewLottery_$code"]],
[['text'=>"💰 تغییر قیمت هر بلیط",'callback_data'=>"Set-coin-$code"],['text'=>"🔖 تغییر تعداد بلیط ها",'callback_data'=>"Set-tickets-$code"]],
[['text'=>"🗑 حذف کردن",'callback_data'=>"Set-delete-$code"]],
]
])
]);
}
}else{
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"❌ قرعه کشی کد $code وجود ندارد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
//-----------------------------------------------------------------------------------------------
if(in_array($from_id,$admin)){
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
elseif($text == "📊 آمار"){
$member = count(glob("data/*/*.json"));
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📉 تعداد کل کاربران ربات : $member
",
'parse_mode'=>'HTML',
'reply_markup'=>$panel
]);
}
elseif($text == "📢 کانال جوین اجباری"){
if(empty($Channels) === false){
foreach($Channels as $links){
$key[] = [['text'=>$links,'callback_data'=>"fake"],['text'=>"🗑 حذف",'callback_data'=>"deljoin-$links"]];
}
}
$key[] = [['text'=>"✅ افزودن جوین اجباری",'callback_data'=>"addjoin"]];
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ لیست کانال های تنظیم شده :
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}
elseif($step == "SetChannel"){
$getChat = bot('getChat',['chat_id'=>$text])->result->type ?? null;
if($getChat == 'channel'){
$chekjoin = bot('getChatMember',[
'chat_id'=>$text,
'user_id'=>$botid
])->result->status ?? null;
if($chekjoin == 'administrator'){
if(in_array(mb_strtolower($text),$Channels)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متاسفانه کانال $text از قبل تنظیم شده است
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
$settings['channel'] = [...$Channels,mb_strtolower($text)];
Save("settings.json",$settings);
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت کانال $text تنظیم شد
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً ابتدا ربات را ادمین کانال $text کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ لطفاً آیدی کانال معتبر ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
elseif($text == "➕ افزایش سکه"){
$datas['step'] = "add";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "add" and is_numeric($text)){
if(file_exists("data/$text/$text.json")){
$datas['step'] = "addcoin-$text";
Save("data/$from_id/$from_id.json",$datas);
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
elseif(strpos($step,"addcoin-") !== false and is_numeric($text)){
$id = explode("-",$step)[1];
if(is_numeric($text)){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
$info = json_decode(file_get_contents("data/$id/$id.json"),true);
$info['coin'] += $text;
Save("data/$id/$id.json",$info);
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
elseif($text == "➖ کسر سکه"){
$datas['step'] = "low";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "low" and is_numeric($text)){
if(file_exists("data/$text/$text.json")){
$datas['step'] = "lowcoin-$text";
Save("data/$from_id/$from_id.json",$datas);
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
elseif(strpos($step,"lowcoin-") !== false and is_numeric($text)){
$id = explode("-",$step)[1];
if(is_numeric($text)){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
$info = json_decode(file_get_contents("data/$id/$id.json"),true);
$info['coin'] -= $text;
Save("data/$id/$id.json",$info);
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
elseif($text == "❌ بلاک کردن"){
$datas['step'] = "blockid";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "blockid" and is_numeric($text)){
if(file_exists("data/$text/$text.json")){
$info = json_decode(file_get_contents("data/$text/$text.json"),true);
$info['step'] = "block";
Save("data/$text/$text.json",$info);
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
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
elseif($text == "🔰 آنبلاک کردن"){
$datas['step'] = "unblockid";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "unblockid" and is_numeric($text)){
if(file_exists("data/$text/$text.json")){
$info = json_decode(file_get_contents("data/$text/$text.json"),true);
$info['step'] = "none";
Save("data/$text/$text.json",$info);
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
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
elseif($text == "📨 پیام همگانی"){
$datas['step'] = "sendall";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📨 پیام خود را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "sendall" and isset($update->message)){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
foreach(glob("data/*/*.json") as $key){
bot('copyMessage',[
'chat_id'=>explode("/",$key)[1],
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
elseif($text == "💭 فوروارد همگانی"){
$datas['step'] = "forall";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💭 پیام خود را فوروارد کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "forall" and isset($update->message)){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
foreach(glob("data/*/*.json") as $key){
bot('forwardMessage',[
'chat_id'=>explode("/",$key)[1],
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
elseif($text == "📩 پیام به کاربر"){
$datas['step'] = "PvId";
Save("data/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✔️ ای دی عددی کاربر مورد نظر را ارسال کنید
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($step == "PvId" and isset($text)){
if(is_numeric($text)){
if(file_exists("data/$text/$text.json")){
$datas['step'] = "PvPm-$text";
Save("data/$from_id/$from_id.json",$datas);
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
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
⚠️ لطفاً فقط ایدی عددی ارسال کنید بصورت عدد
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
elseif(strpos($step,"PvPm-") !== false and isset($text)){
$ids = explode("-",$step)[1];
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ پیام شما با موفقیت به کاربر ( <code>$ids</code> ) ارسال شد
",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
bot('sendMessage',[
'chat_id'=>$ids,
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
elseif(isset($update->message->reply_to_message)){
$forep_id = $update->message->reply_to_message->forward_from->id;
if(file_exists("data/$forep_id/$forep_id.json")){
$datas['step'] = "none";
Save("data/$from_id/$from_id.json",$datas);
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
'parse_mode'=>'HTML',
]);
}
}
}
//-----------------------------------------------------------------------------------------------
if(strpos($query,"NewLottery_") !== false and file_exists("data/$from_id/$from_id.json") and $tc == "channel"){
$Find = explode("_",$query)[1];
if(isset($lottery[$Find])){
$lotteryId = $lottery[$Find]['id'];
$lotteryTickets = $lottery[$Find]['tickets'];
$lotteryCoin = $lottery[$Find]['coin'];
$lotteryText = $lottery[$Find]['text'];
$lotteryTime = $lottery[$Find]['time'];
$lotteryDate = $lottery[$Find]['date'];
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>'1',
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/186',
'title'=>'❗️اینجا کلیک کنید',
'input_message_content'=>[
'message_text'=>"
🎫 تعداد بلیط باقی مانده : $lotteryTickets

💸 قیمت هر بلیط : $lotteryCoin


🎈 توضیحات مربوط به قرعه کشی :

$lotteryText


⏱ این قرعه کشی در تاریخ $lotteryDate و ساعت $lotteryTime ساخته شده است

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
•••⟩⟩ ʙᴏᴛ ɪᴅ : @$botuser ⟨⟨•••
",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true
],'reply_markup'=>['inline_keyboard'=>[
[['text'=>"✅ شروع",'callback_data'=>"starts-$lotteryId-$Find"]],
]
]
]
])
]);
}
}
if($query == "share" and file_exists("data/$from_id/$from_id.json")){
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
🎫 تعداد بلیط های خریداری شده : $ticket
🎟 تعداد برنده شدن : $win
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