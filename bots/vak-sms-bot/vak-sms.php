<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(E_ALL);
//===[کلید وب سرویس vak-sms]===//
define('API_KEY','e82ccf4321f890728');
//===[توکن]===//
define('TOKEN','65875:oNF_mHkX88Ls8C0');
//===[توکن]===//
define('ADMINS',[123478292,1725955696]);
//===[امنیت دامنه]===//
$telegram_ip_ranges = [
['lower'=>'149.154.160.0','upper'=>'149.154.175.255'], // literally 149.154.160.0/20
['lower'=>'91.108.4.0','upper'=>'91.108.7.255'],  // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u",ip2long($_SERVER['REMOTE_ADDR']));
$ok = true;
foreach($telegram_ip_ranges as $telegram_ip_range){
if($ok){
$lower_dec = (float) sprintf("%u",ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u",ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec){
$ok = false;
}
}
}
if($ok){
exit(header("location: https://Google.com"));
}
//===[فانکشن های لازم]===//
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".TOKEN."/".$method;
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
function Save($link,$data){
$outjson = json_encode($data,true);
file_put_contents($link,$outjson);
}
function Request($url){
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function BuyNumber($country,$operator = null){
return Request('https://vak-sms.com/api/getNumber/?apiKey='.API_KEY.'&service=tg&operator='.$operator.'&country='.$country);
}
function Cancel($id){
return Request('https://vak-sms.com/api/setStatus/?apiKey='.API_KEY.'&status=end&idNum='.$id);
}
function ReceiveSMS($id){
return Request('https://vak-sms.com/api/getSmsCode/?apiKey='.API_KEY.'&all=true&idNum='.$id);
}
function GetBalance(){
return Request('https://vak-sms.com/api/getBalance/?apiKey='.API_KEY);
}
function GetCountNumber($country,$operator = null){
return Request('https://vak-sms.com/api/getCountNumber/?apiKey='.API_KEY.'&service=tg&operator='.$operator.'&country='.$country);
}
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
$chat_id = $update->message->chat->id;
$text = $update->message->text ?? null;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name ?? '𝑛𝑜𝑛𝑒';
$user_name = $update->message->from->username ?? '𝑛𝑜𝑛𝑒';
}
elseif(isset($update->callback_query)){
$chat_id = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$from_id = $update->callback_query->from->id;
$first_name = $update->callback_query->from->first_name;
$last_name = $update->callback_query->from->last_name ?? '𝑛𝑜𝑛𝑒';
$user_name = $update->callback_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
//-----------------------------------------------------------------------------------------------
$menu = json_encode(['keyboard'=>[
[['text'=>"💰 موجودی"]],
[['text'=>"🇬🇧 شماره انگلستان"],['text'=>"🇮🇩 شماره اندونزی"]],
[['text'=>"🇬🇧 موجودی انگلستان"],['text'=>"🇮🇩 موجودی اندونزی"]],
[['text'=>"🇦🇷 شماره آرژانتین"],['text'=>"🇲🇽 شماره مکزیک"]],
[['text'=>"🇦🇷 موجودی آرژانتین"],['text'=>"🇲🇽 موجودی مکزیک"]],
[['text'=>"🇵🇰 شماره پاکستان"]],
[['text'=>"🇵🇰 موجودی پاکستان"]],
],'resize_keyboard'=>true]);
//-----------------------------------------------------------------------------------------------
if(isset($data) and in_array($from_id,ADMINS)){
if(strpos($data,"sms-") !== false){
$id = explode("-",$data)[1];
$json = ReceiveSMS($id);
if($json->smsCode){
foreach($json->smsCode as $sms){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>'Code : '.$sms,
'reply_to_message_id'=>$message_id
]);
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ کدی هنوز دریافت نشده است",
'show_alert'=>false
]);
}
}
elseif(strpos($data,"cancel-") !== false){
$id = explode("-",$data)[1];
$json = Cancel($id);
if(isset($json->status) and $json->status == 'update'){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"❌ دریافت کد شماره با موفقیت لغو شد",
'parse_mode'=>'HTML'
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"⚠️ خطایی در لغو کردن شماره به وجود آمده است",
'show_alert'=>false
]);
}
}
}
//-----------------------------------------------------------------------------------------------
if(isset($text) and in_array($from_id,ADMINS)){
if(strpos($text,"/start") !== false){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🌹 سلام به ربات خوش آمدید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
elseif($text == "🇬🇧 شماره انگلستان"){
$json = BuyNumber('gb');
if(isset($json->idNum,$json->tel)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ شماره <code>{$json->tel}</code> با موفقیت ساخته شد",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔢 دریافت کد",'callback_data'=>"sms-{$json->idNum}"]],
[['text'=>"❌ لغو کردن شماره",'callback_data'=>"cancel-{$json->idNum}"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در ساخت شماره انگلستان به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇮🇩 شماره اندونزی"){
$json = BuyNumber('id','telkomsel');
if(isset($json->idNum,$json->tel)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ شماره <code>{$json->tel}</code> با موفقیت ساخته شد",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔢 دریافت کد",'callback_data'=>"sms-{$json->idNum}"]],
[['text'=>"❌ لغو کردن شماره",'callback_data'=>"cancel-{$json->idNum}"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در ساخت شماره اندونزی به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇦🇷 شماره آرژانتین"){
$json = BuyNumber('ar');
if(isset($json->idNum,$json->tel)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ شماره <code>{$json->tel}</code> با موفقیت ساخته شد",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔢 دریافت کد",'callback_data'=>"sms-{$json->idNum}"]],
[['text'=>"❌ لغو کردن شماره",'callback_data'=>"cancel-{$json->idNum}"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در ساخت شماره آرژانتین به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇲🇽 شماره مکزیک"){
$json = BuyNumber('mx');
if(isset($json->idNum,$json->tel)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ شماره <code>{$json->tel}</code> با موفقیت ساخته شد",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔢 دریافت کد",'callback_data'=>"sms-{$json->idNum}"]],
[['text'=>"❌ لغو کردن شماره",'callback_data'=>"cancel-{$json->idNum}"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در ساخت شماره مکزیک به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇵🇰 شماره پاکستان"){
$json = BuyNumber('pk','telenor');
if(isset($json->idNum,$json->tel)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"✅ شماره <code>{$json->tel}</code> با موفقیت ساخته شد",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔢 دریافت کد",'callback_data'=>"sms-{$json->idNum}"]],
[['text'=>"❌ لغو کردن شماره",'callback_data'=>"cancel-{$json->idNum}"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در ساخت شماره پاکستان به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇬🇧 موجودی انگلستان"){
$json = GetCountNumber('gb');
if(isset($json->tg)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🇬🇧 تعداد شماره های انگستان : <code>{$json->tg}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت تعداد شماره انگلستان به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇮🇩 موجودی اندونزی"){
$json = GetCountNumber('id','telkomsel');
if(isset($json->tg)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🇮🇩 تعداد شماره های اندونزی : <code>{$json->tg}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت تعداد شماره اندونزی به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇦🇷 موجودی آرژانتین"){
$json = GetCountNumber('ar');
if(isset($json->tg)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🇦🇷 تعداد شماره های آرژانتین : <code>{$json->tg}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت تعداد شماره آرژانتین به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇲🇽 موجودی مکزیک"){
$json = GetCountNumber('mx');
if(isset($json->tg)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🇲🇽 تعداد شماره های مکزیک : <code>{$json->tg}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت تعداد شماره مکزیک به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "🇵🇰 موجودی پاکستان"){
$json = GetCountNumber('pk','telenor');
if(isset($json->tg)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🇵🇰 تعداد شماره های پاکستان : <code>{$json->tg}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت تعداد شماره پاکستان به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif($text == "💰 موجودی"){
$json = GetBalance();
if(isset($json->balance)){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💰 موجودی شما : <code>{$json->balance}</code>",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"⁉️ خطایی در دریافت اطلاعات پروفایل شما به وجود آمده است",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
}
?>