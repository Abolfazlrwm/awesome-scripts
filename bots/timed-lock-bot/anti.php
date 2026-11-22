/////Sourrce_kade/// 
////مشکلی داشتید پیوی مراجعه کنید//// 
////@qqabr//// 
/////کسناموس کسی ک بدون منبع بردارع/////
<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

error_reporting(0);
http_response_code(200);
if(is_callable('litespeed_finish_request')){
litespeed_finish_request();
}elseif(is_callable('fastcgi_finish_request')){
fastcgi_finish_request();
}
date_default_timezone_set("Asia/Tehran");
define('API_KEY',''); // توکن ربات را از @BotFather بگیرید و اینجا بگذارید
//-----------------------------------------------------------------------------------------------
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok = true;
foreach($telegram_ip_ranges as $telegram_ip_range){
if($ok){
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec){
$ok = false;
}
}
}
if($ok){
exit(header("location: https://Google.com"));
}
//-----------------------------------------------------------------------------------------------
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
function Save($link,$data){
$outjson = json_encode($data,true);
file_put_contents($link,$outjson);
}
function StrTime($string){
if(preg_match('/(\d+):(\d+):(\d+)/',$string,$match)){
list($all,$hour,$minute,$second) = $match;
return mktime($hour,$minute,$second);
}else{
return 0;
}
}
function Delete($directory){
if(is_file($directory)) return @unlink($directory);
$contents = glob($directory.'/*');
foreach($contents as $path){
if(is_file($path)){
@unlink($path);
}elseif(is_dir($path)){
Delete($path);
}
}
return @rmdir($directory);
}
function Security($text){
$filters = ['https','api','telegram','key','zip','->','"',';','{','}'];
foreach($filters as $filter){
if(stripos($text,$filter) !== false){
return $filter;
}
}
return false;
}
//-----------------------------------------------------------------------------------------------
$get = bot('getMe');
$botid = $get->result->id;
$botuser = $get->result->username;
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if(isset($update->chat_member)){
$from_id = $update->chat_member->from->id ?? null;
$first_name = $update->chat_member->from->first_name ?? null;
$user_id = $update->chat_member->old_chat_member->user->id ?? null;
$kicked_name = $update->chat_member->old_chat_member->user->first_name ?? '𝑛𝑜𝑛𝑒';
$channel_id = $update->chat_member->chat->id ?? null;
$channel_title = $update->chat_member->chat->title ?? null;
$status = $update->chat_member->new_chat_member->status ?? null;
}
if(isset($update->message)){
$chat_id = $update->message->chat->id ?? null;
$text = $update->message->text ?? null;
$message_id = $update->message->message_id ?? null;
$from_id = $update->message->from->id ?? null;
$tc = $update->message->chat->type ?? null;
$first_name = $update->message->from->first_name ?? null;
}
if(isset($update->callback_query)){
$data = $update->callback_query->data ?? null;
$user_id = $update->callback_query->id ?? null;
$from_id = $update->callback_query->from->id ?? null;
$inline_message_id = $update->callback_query->inline_message_id;
$message_id = $update->callback_query->message->message_id;
}
if(isset($update->inline_query)){
$user_id = $update->inline_query->id ?? null;
$query = $update->inline_query->query ?? null;
$from_id = $update->inline_query->from->id ?? null;
}
if(isset($update->channel_post)){
$channel_user = $update->channel_post->chat->id ?? null;
$channel_message_id = $update->channel_post->message_id ?? null;
}
if(isset($update->edited_channel_post)){
$channel_user = $update->edited_channel_post->chat->id ?? null;
$channel_message_id = $update->edited_channel_post->message_id ?? null;
}
if(isset($text) and Security($text)) exit();
//-----------------------------------------------------------------------------------------------
if(!file_exists("data")) mkdir("data");
if(!file_exists("user")) mkdir("user");
if(!file_exists("channel")) mkdir("channel");
$admin = [5581738774,920995783];
$channel = '@zedkhyanatt';
//-----------------------------------------------------------------------------------------------
/////Sourrce_kade/// 
////مشکلی داشتید پیوی مراجعه کنید//// 
////@qqabr//// 
/////کسناموس کسی ک بدون منبع بردارع/////
$menu = json_encode(['keyboard'=>[
[['text'=>"📢 ثبت کانال جدید"]],
[['text'=>"⚙ مدیریت کانال ها"],['text'=>"🔐 حساب کاربری"]],
[['text'=>"🗣 زیرمجموعه گیری"],['text'=>"📚 راهنما"],['text'=>"🔖 سوالات متداول"]],
[['text'=>"📮 پشتیبانی و قوانین"]],
],'resize_keyboard'=>true]);

$panel = json_encode(['keyboard'=>[
[['text'=>"📊 آمار"]],
[['text'=>"➖  کسر سکه"],['text'=>"➕ افزایش سکه"]],
[['text'=>"📨 پیام همگانی"],['text'=>"💭 فوروارد همگانی"]],
[['text'=>"🔰 آنبلاک کردن"],['text'=>"❌ بلاک کردن"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$back = json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);
//-----------------------------------------------------------------------------------------------
if($status == "kicked"){
if(file_exists("data/$channel_id") and file_exists("channel/$channel_id")){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$datas = json_decode(file_get_contents("data/$channel_id/$from_id.json"),true);
$datas['warn'] += 1;
Save("data/$channel_id/$from_id.json",$datas);
$reply = bot('sendMessage',[
'chat_id'=>$channel_data['admin'],
'text'=>"
⚠️ کاربر <a href ='tg://user?id=$user_id'>$kicked_name</a> توسط ادمین <a href ='tg://user?id=$from_id'>$first_name</a> از کانال $channel_title اخراج شد

❗️تعداد  اخطار ها : {$datas['warn']}
",
'parse_mode'=>'HTML'
])->result->message_id;
if($datas['warn'] >= 5){
$datas['warn'] = 0;
Save("data/$channel_id/$from_id.json",$datas);
$result = bot('promoteChatMember',[
'chat_id'=>$channel_id,
'user_id'=>$from_id,
'can_manage_chat'=>false,
'can_change_info'=>false,
'can_post_messages'=>false,
'can_edit_messages'=>false,
'can_delete_messages'=>false,
'can_invite_users'=>false,
'can_restrict_members'=>false,
'can_manage_voice_chats'=>false,
'is_anonymous'=>false
]);
if($result->ok){
bot('sendMessage',[
'chat_id'=>$channel_data['admin'],
'text'=>"
✅ با موفقیت ادمین شما عزل شد
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$reply
]);
}else{
bot('sendMessage',[
'chat_id'=>$channel_data['admin'],
'text'=>"
❌ متاسفانه خطایی در عزل کردن ادمین شما پیش آمده است
",
'parse_mode'=>'HTML',
'reply_to_message_id'=>$reply
]);
}
}
}
}
if(isset($channel_user)){
if(file_exists("channel/$channel_user/$channel_user.json")){
$channel_data = json_decode(file_get_contents("channel/$channel_user/$channel_user.json"),true);
if($channel_data['adminaccess'] == "on"){
if(isset($channel_data["adminaccessfrom"],$channel_data["adminaccessto"])){
if(StrTime($channel_data["adminaccessfrom"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($channel_data["adminaccessto"])){
if($channel_data['getaccess'] == false){
$channel_data['getaccess'] = true;
Save("channel/$channel_user/$channel_user.json",$channel_data);
foreach(glob("data/$channel_user/*.json") as $file){
$ids = str_replace('.json',null,explode('/',$file)[2]);
$result = bot('promoteChatMember',[
'chat_id'=>$channel_user,
'user_id'=>$ids,
'can_manage_chat'=>true,
'can_change_info'=>false,
'can_post_messages'=>true,
'can_edit_messages'=>false,
'can_delete_messages'=>false,
'can_invite_users'=>true,
'can_restrict_members'=>false,
'can_manage_voice_chats'=>true,
'is_anonymous'=>false
]);
sleep(5);
}
}
}else{
if($channel_data['getaccess'] == true){
$channel_data['getaccess'] = false;
Save("channel/$channel_user/$channel_user.json",$channel_data);
foreach(glob("data/$channel_user/*.json") as $file){
$ids = str_replace('.json',null,explode('/',$file)[2]);
$result = bot('promoteChatMember',[
'chat_id'=>$channel_user,
'user_id'=>$ids,
'can_manage_chat'=>true,
'can_change_info'=>false,
'can_post_messages'=>true,
'can_edit_messages'=>true,
'can_delete_messages'=>true,
'can_invite_users'=>true,
'can_restrict_members'=>true,
'can_manage_voice_chats'=>true,
'is_anonymous'=>true
]);
sleep(5);
}
}
}
}
}else{
foreach($channel_data["adminaccessall"] as $arrays){
if($arrays["mode"] == "on"){
if(StrTime($arrays["from"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($arrays["to"])){
if($channel_data['getaccess'] == false){
$channel_data['getaccess'] = true;
Save("channel/$channel_user/$channel_user.json",$channel_data);
foreach(glob("data/$channel_user/*.json") as $file){
$ids = str_replace('.json',null,explode('/',$file)[2]);
$result = bot('promoteChatMember',[
'chat_id'=>$channel_user,
'user_id'=>$ids,
'can_manage_chat'=>true,
'can_change_info'=>false,
'can_post_messages'=>true,
'can_edit_messages'=>false,
'can_delete_messages'=>false,
'can_invite_users'=>true,
'can_restrict_members'=>false,
'can_manage_voice_chats'=>true,
'is_anonymous'=>false
]);
sleep(5);
}
}
}else{
if($channel_data['getaccess'] == true){
$channel_data['getaccess'] = false;
Save("channel/$channel_user/$channel_user.json",$channel_data);
foreach(glob("data/$channel_user/*.json") as $file){
$ids = str_replace('.json',null,explode('/',$file)[2]);
$result = bot('promoteChatMember',[
'chat_id'=>$channel_user,
'user_id'=>$ids,
'can_manage_chat'=>true,
'can_change_info'=>false,
'can_post_messages'=>true,
'can_edit_messages'=>true,
'can_delete_messages'=>true,
'can_invite_users'=>true,
'can_restrict_members'=>true,
'can_manage_voice_chats'=>true,
'is_anonymous'=>true
]);
sleep(5);
}
}
}
}
}
}
if($channel_data['photo'] == "on" and isset($update->channel_post->photo)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['video'] == "on" and isset($update->channel_post->video)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['audio'] == "on" and isset($update->channel_post->audio)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['document'] == "on" and isset($update->channel_post->document)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['gif'] == "on" and isset($update->channel_post->animation)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['sticker'] == "on" and isset($update->channel_post->sticker)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['location'] == "on" and isset($update->channel_post->location)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['contact'] == "on" and isset($update->channel_post->contact)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['voice'] == "on" and isset($update->channel_post->voice)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['videonote'] == "on" and isset($update->channel_post->video_note)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if(preg_match('~((https\://|http\://)?((t|telegram)\.me/|@)(.+))\s~i',$update->channel_post->text." LINK",$match) or preg_match('~((https\://|http\://)?((t|telegram)\.me/|@)(.+))\s~i',$update->channel_post->caption." LINK",$match)){
if($channel_data['link'] == "on"){
if(isset($channel_data["linkfrom"],$channel_data["linkto"])){
if(StrTime($channel_data["linkfrom"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($channel_data["linkto"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
foreach($channel_data["linkall"] as $arrays){
if($arrays["mode"] == "on"){
if(StrTime($arrays["from"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($arrays["to"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
}
}
if($channel_data['tag'] == "on" and strpos($update->channel_post->text,"@") !== false){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['hashtag'] == "on" and strpos($update->channel_post->text,"#") !== false){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if(isset($update->channel_post->text)){
if($channel_data['text'] == "on"){
if(isset($channel_data["textfrom"],$channel_data["textto"])){
if(StrTime($channel_data["textfrom"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($channel_data["textto"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
foreach($channel_data["textall"] as $arrays){
if($arrays["mode"] == "on"){
if(StrTime($arrays["from"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($arrays["to"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
}
}
if($channel_data['swearword'] == "on"){
foreach($channel_data['swearwords'] as $value){
if(strpos($update->channel_post->text,$value) !== false){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
if($channel_data['stickeranimated'] == "on" and $update->channel_post->sticker->is_animated === true){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if(isset($update->edited_channel_post) and empty($update->edited_channel_post->text)){
if($channel_data['editmedia'] == "on"){
if(isset($channel_data["editmediafrom"],$channel_data["editmediato"])){
if(StrTime($channel_data["editmediafrom"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($channel_data["editmediato"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
foreach($channel_data["editmediaall"] as $arrays){
if($arrays["mode"] == "on"){
if(StrTime($arrays["from"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($arrays["to"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
}
}
if(empty($update->channel_post->text)){
if($channel_data['media'] == "on"){
if(isset($channel_data["mediafrom"],$channel_data["mediato"])){
if(StrTime($channel_data["mediafrom"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($channel_data["mediato"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}else{
foreach($channel_data["mediaall"] as $arrays){
if($arrays["mode"] == "on"){
if(StrTime($arrays["from"]) < StrTime(date("G:i:s")) and StrTime(date("G:i:s")) < StrTime($arrays["to"])){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
}
}
if($channel_data['forward'] == "on" and isset($update->channel_post->forward_date)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
if($channel_data['edittext'] == "on" and isset($update->edited_channel_post) and isset($update->edited_channel_post->text)){
bot('deleteMessage',[
'chat_id'=>$channel_user,
'message_id'=>$channel_message_id,
]);
}
}
}
//-----------------------------------------------------------------------------------------------
if(preg_match('#lock~(.*)~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$type = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$Off_On = $channel_data[$type] ?? "off";
if($Off_On == "on"){
if(isset($status) and $status == 'creator'){
$channel_data[$type] = "off";
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ با موفقیت این قفل خاموش شد",
'show_alert'=>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ این قفل روشن است",
'show_alert'=>true
]);
}
}else{
if(isset($status) and $status == 'creator'){
$channel_data[$type] = "on";
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ با موفقیت این قفل روشن شد",
'show_alert'=>true
]);
if(in_array($type,["media","link","adminaccess","editmedia","text"])){
if(isset($channel_data[$type."from"],$channel_data[$type."to"])){
$from = $channel_data[$type."from"];
$to = $channel_data[$type."to"];
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"از ساعت $from تا ساعت $to",'callback_data'=>"delete~$type~$channel_id"]],
[['text'=>"تغییر ساعت",'callback_data'=>"settime~$type~$channel_id"]],
]])
]);
}else{
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"تنظیم ساعت",'callback_data'=>"settime~$type~$channel_id"]],
]])
]);
}
exit();
}
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این قفل خاموش است",
'show_alert'=>true
]);
}
}
$photo = $channel_data['photo'] == 'on' ? '✅' : '❌';
$video = $channel_data['video'] == 'on' ? '✅' : '❌';
$audio = $channel_data['audio'] == 'on' ? '✅' : '❌';
$document = $channel_data['document'] == 'on' ? '✅' : '❌';
$gif = $channel_data['gif'] == 'on' ? '✅' : '❌';
$sticker = $channel_data['sticker'] == 'on' ? '✅' : '❌';
$location = $channel_data['location'] == 'on' ? '✅' : '❌';
$contact = $channel_data['contact'] == 'on' ? '✅' : '❌';
$voice = $channel_data['voice'] == 'on' ? '✅' : '❌';
$videonote = $channel_data['videonote'] == 'on' ? '✅' : '❌';
$link = $channel_data['link'] == 'on' ? '✅' : '❌';
$tag = $channel_data['tag'] == 'on' ? '✅' : '❌';
$hashtag = $channel_data['hashtag'] == 'on' ? '✅' : '❌';
$text = $channel_data['text'] == 'on' ? '✅' : '❌';
$swearword = $channel_data['swearword'] == 'on' ? '✅' : '❌';
$stickeranimated = $channel_data['stickeranimated'] == 'on' ? '✅' : '❌';
$editmedia = $channel_data['editmedia'] == 'on' ? '✅' : '❌';
$media = $channel_data['media'] == 'on' ? '✅' : '❌';
$forward = $channel_data['forward'] == 'on' ? '✅' : '❌';
$edittext = $channel_data['edittext'] == 'on' ? '✅' : '❌';
$adminaccess = $channel_data['adminaccess'] == 'on' ? '✅' : '❌';
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"عکس $photo",'callback_data'=>"lock~$channel_id~photo"],['text'=>"فیلم $video",'callback_data'=>"lock~$channel_id~video"]],
[['text'=>"آهنگ $audio",'callback_data'=>"lock~$channel_id~audio"],['text'=>"فایل $document",'callback_data'=>"lock~$channel_id~document"]],
[['text'=>"گیف $gif",'callback_data'=>"lock~$channel_id~gif"],['text'=>"استیکر $sticker",'callback_data'=>"lock~$channel_id~sticker"]],
[['text'=>"موقعیت مکانی $location",'callback_data'=>"lock~$channel_id~location"],['text'=>"مخاطب $contact",'callback_data'=>"lock~$channel_id~contact"]],
[['text'=>"ویس $voice",'callback_data'=>"lock~$channel_id~voice"],['text'=>"فیلم سلفی $videonote",'callback_data'=>"lock~$channel_id~videonote"]],
[['text'=>"هشتگ $hashtag",'callback_data'=>"lock~$channel_id~hashtag"],['text'=>"تگ $tag",'callback_data'=>"lock~$channel_id~tag"]],
[['text'=>"فحش $swearword",'callback_data'=>"lock~$channel_id~swearword"],['text'=>"استیکر متحرک $stickeranimated",'callback_data'=>"lock~$channel_id~stickeranimated"]],
[['text'=>"فوروارد $forward",'callback_data'=>"lock~$channel_id~forward"],['text'=>"ویرایش پیام $edittext",'callback_data'=>"lock~$channel_id~edittext"]],
[['text'=>"ویرایش رسانه $editmedia",'callback_data'=>"lock~$channel_id~editmedia"],['text'=>"قفل رسانه $media",'callback_data'=>"lock~$channel_id~media"]],
[['text'=>"لینک $link",'callback_data'=>"lock~$channel_id~link"],['text'=>"متن $text",'callback_data'=>"lock~$channel_id~text"]],
[['text'=>"دسترسی ادمین ها $adminaccess",'callback_data'=>"lock~$channel_id~adminaccess"]],
[['text'=>"قفل خودکار",'callback_data'=>"automatically~$channel_id"]],
]])
]);
}
if(preg_match('#auto~(.*)~(.*)~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$find = $exp[2];
$type = $exp[3];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$Off_On = $channel_data[$type][$find] ?? "off";
if($Off_On == "on"){
if(isset($status) and $status == 'creator'){
$channel_data[$type][$find] = "off";
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ با موفقیت این قفل خاموش شد",
'show_alert'=>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ این قفل روشن است",
'show_alert'=>true
]);
}
}else{
if(isset($status) and $status == 'creator'){
$channel_data[$type][$find] = "on";
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"✅ با موفقیت این قفل روشن شد",
'show_alert'=>true
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"❌ این قفل خاموش است",
'show_alert'=>true
]);
}
}
$media = $channel_data['allmedia'][$find] == 'on' ? '✅' : '❌';
$editmedia = $channel_data['alleditmedia'][$find] == 'on' ? '✅' : '❌';
$text = $channel_data['alltext'][$find] == 'on' ? '✅' : '❌';
$link = $channel_data['alllink'][$find] == 'on' ? '✅' : '❌';
$adminaccess = $channel_data['alladminaccess'][$find] == 'on' ? '✅' : '❌';
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"ویرایش رسانه $editmedia",'callback_data'=>"auto~$channel_id~$find~alleditmedia"],['text'=>"قفل رسانه $media",'callback_data'=>"auto~$channel_id~$find~allmedia"]],
[['text'=>"لینک $link",'callback_data'=>"auto~$channel_id~$find~alllink"],['text'=>"متن $text",'callback_data'=>"auto~$channel_id~$find~alltext"]],
[['text'=>"دسترسی ادمین ها $adminaccess",'callback_data'=>"auto~$channel_id~$find~alladminaccess"]],
[['text'=>"تأیید کردن",'callback_data'=>"okauto~$channel_id~$find"]],
]])
]);
}
if(preg_match('#settime~(.*)~(.*)#',$data,$exp)){
$type = $exp[1];
$channel_id = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$line = 0;
for($row = 0;$row <= 5;$row++){
for($column = 0;$column <= 3;$column++){
$key[$row][$column] = ['text'=>$line,'callback_data'=>"set~...~...~...~...~...~...~$line~$type~$channel_id"];
$line++;
}
}
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• از ساعت ... و ... دقیقه و ... ثانیه تا ساعت ... و ... دقیقه ‌و ... ثانیه فعال شد
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#delete~(.*)~(.*)#',$data,$exp)){
$type = $exp[1];
$channel_id = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
unset($channel_data[$type."from"]);
unset($channel_data[$type."to"]);
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• ساعت این قفل با موفقیت برداشته شد
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"تنظیم ساعت",'callback_data'=>"settime~$type~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#automatically~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$inlines = [];
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
foreach($channel_data["all"] as $key){
$find = key($channel_data["all"]);
next($channel_data["all"]);
$inlines[] = [['text'=>$key["from"]." الی ".$key["to"],'callback_data'=>"settings~$channel_id~$find"]];
}
$inlines[] = [['text'=>"➕ افزودن زمان قفل خودکار",'callback_data'=>"settime~all~$channel_id"]];
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• به بخش تنظیمات قفل خودکار خوش آمدید
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>$inlines])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#settings~(.*)~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$find = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$media = $channel_data['allmedia'][$find] == 'on' ? '✅' : '❌';
$editmedia = $channel_data['alleditmedia'][$find] == 'on' ? '✅' : '❌';
$text = $channel_data['alltext'][$find] == 'on' ? '✅' : '❌';
$link = $channel_data['alllink'][$find] == 'on' ? '✅' : '❌';
$adminaccess = $channel_data['alladminaccess'][$find] == 'on' ? '✅' : '❌';
bot('editMessageReplyMarkup',[
'inline_message_id'=>$inline_message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"ویرایش رسانه $editmedia",'callback_data'=>"auto~$channel_id~$find~alleditmedia"],['text'=>"قفل رسانه $media",'callback_data'=>"auto~$channel_id~$find~allmedia"]],
[['text'=>"لینک $link",'callback_data'=>"auto~$channel_id~$find~alllink"],['text'=>"متن $text",'callback_data'=>"auto~$channel_id~$find~alltext"]],
[['text'=>"دسترسی ادمین ها $adminaccess",'callback_data'=>"auto~$channel_id~$find~alladminaccess"]],
[['text'=>"حذف این قفل خودکار",'callback_data'=>"delauto~$channel_id~$find"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#okauto~(.*)~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$find = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
foreach(["media","link","adminaccess","editmedia","text"] as $type){
if($channel_data["all".$type][$find] == 'on'){
$channel_data[$type."all"][$find] = ["to"=>$channel_data["all"][$find]["to"],"from"=>$channel_data["all"][$find]["from"],"mode"=>"on"];
}else{
unset($channel_data[$type."all"][$find]);
}
}
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• با موفقیت زمان برای قفل های انتخابی تنظیم شدن
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"برگشت",'callback_data'=>"automatically~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#delauto~(.*)~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$find = $exp[2];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$from = $channel_data["all"][$find]["from"];
$to = $channel_data["all"][$find]["to"];
foreach(["media","link","adminaccess","editmedia","text"] as $type){
unset($channel_data[$type."all"][$find]);
unset($channel_data["all".$type][$find]);
unset($channel_data["all"][$find]);
}
unset($channel_data["all"][$find]["from"]);
unset($channel_data["all"][$find]["to"]);
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• با موفقیت زمان $from الی $to حذف شد
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"برگشت",'callback_data'=>"automatically~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
if(preg_match('#set~(.*)~(.*)~(.*)~(.*)~(.*)~(.*)~(.*)~(.*)~(.*)#',$data,$exp)){
$type = $exp[8];
$channel_id = $exp[9];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$from_H = $exp[1] ?? '...';
$from_M = $exp[2] ?? '...';
$from_S = $exp[3] ?? '...';
$to_H = $exp[4] ?? '...';
$to_M = $exp[5] ?? '...';
$to_S = $exp[6] ?? '...';
$number = $exp[7];
if($from_H == '...'){
$from_H = $number;
}elseif($from_M == '...'){
$from_M = $number;
}elseif($from_S == '...'){
$from_S = $number;
}elseif($to_H == '...'){
$to_H = $number;
}elseif($to_M == '...'){
$to_M = $number;
}elseif($to_S == '...'){
$to_S = $number;
}
if($from_H == '...'){
$line = 0;
for($row = 0;$row <= 5;$row++){
for($column = 0;$column <= 3;$column++){
$key[$row][$column] = ['text'=>$line,'callback_data'=>"set~$from_H~$from_M~$from_S~$to_H~$to_M~$to_S~$line~$type~$channel_id"];
$line++;
}
}
}elseif($from_M == '...' or $from_S == '...'){
$line = 0;
for($row = 0;$row <= 14;$row++){
for($column = 0;$column <= 3;$column++){
$key[$row][$column] = ['text'=>$line,'callback_data'=>"set~$from_H~$from_M~$from_S~$to_H~$to_M~$to_S~$line~$type~$channel_id"];
$line++;
}
}
}elseif($to_H == '...'){
$line = 0;
for($row = 0;$row <= 5;$row++){
for($column = 0;$column <= 3;$column++){
$key[$row][$column] = ['text'=>$line,'callback_data'=>"set~$from_H~$from_M~$from_S~$to_H~$to_M~$to_S~$line~$type~$channel_id"];
$line++;
}
}
}elseif($to_M == '...' or $to_S == '...'){
$line = 0;
for($row = 0;$row <= 14;$row++){
for($column = 0;$column <= 3;$column++){
$key[$row][$column] = ['text'=>$line,'callback_data'=>"set~$from_H~$from_M~$from_S~$to_H~$to_M~$to_S~$line~$type~$channel_id"];
$line++;
}
}
}else{
if($type == "all"){
$find = time();
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$channel_data["all"][$find] = ["to"=>$to_H.":".$to_M.":".$to_S,"from"=>$from_H.":".$from_M.":".$from_S];
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• از ساعت $from_H و $from_M دقیقه و $from_S ثانیه تا ساعت $to_H و $to_M دقیقه ‌و $to_S ثانیه فعال شد

• لطفاً دسترسی هایی که میخواهید بگیرید در این تایم را مشخص کنید
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"ویرایش رسانه ❌",'callback_data'=>"auto~$channel_id~$find~alleditmedia"],['text'=>"قفل رسانه ❌",'callback_data'=>"auto~$channel_id~$find~allmedia"]],
[['text'=>"لینک ❌",'callback_data'=>"auto~$channel_id~$find~alllink"],['text'=>"متن ❌",'callback_data'=>"auto~$channel_id~$find~alltext"]],
[['text'=>"دسترسی ادمین ها ❌",'callback_data'=>"auto~$channel_id~$find~alladminaccess"]],
[['text'=>"تأیید کردن",'callback_data'=>"okauto~$channel_id~$find"]],
]])
]);
}else{
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$channel_data[$type."from"] = $from_H.":".$from_M.":".$from_S;
$channel_data[$type."to"] = $to_H.":".$to_M.":".$to_S;
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• از ساعت $from_H و $from_M دقیقه و $from_S ثانیه تا ساعت $to_H و $to_M دقیقه ‌و $to_S ثانیه فعال شد
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔰 خاموش کردن / روشن کردن 🔰",'callback_data'=>"lock~$channel_id~$type"]],
]])
]);
}
exit();
}
bot('editMessageText',[
'inline_message_id'=>$inline_message_id,
'text'=>"
• از ساعت $from_H و $from_M دقیقه و $from_S ثانیه تا ساعت $to_H و $to_M دقیقه ‌و $to_S ثانیه فعال شد
",
'parse_mode'=>'markdown',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دسترسی به این دکمه را ندارید",
'show_alert'=>true
]);
}
}
//-----------------------------------------------------------------------------------------------
if(isset($data)){
if(file_exists("user/$from_id/$from_id.json")){
$datas = json_decode(file_get_contents("user/$from_id/$from_id.json"),true);
}else{
mkdir("user/$from_id");
$datas = ['step'=>'none','coin'=>0,'zir'=>0,'channels'=>[]];
Save("user/$from_id/$from_id.json",$datas);
}
if(preg_match('#info~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
⚙ پنل برای کانال {$chat->result->title} باز شد
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"❄️ پنل مدیریت اصلی",'switch_inline_query'=>"panel $channel_id"]],
[['text'=>"🗑 حذف کانال",'callback_data'=>"delete~$channel_id"]],
[['text'=>"👤 ادمین ها",'callback_data'=>"admins~$channel_id"]],
[['text'=>"📃 فیلتر کلمات",'callback_data'=>"swearword~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
if(preg_match('#delete~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
Delete("data/$channel_id");
Delete("channel/$channel_id");
$search = array_search($channel_id,$datas['channels']);
unset($datas['channels'][$search]);
$datas['channels'] = array_values($datas['channels']);
Save("user/$from_id/$from_id.json",$datas);
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
💯 کانال {$chat->result->title} با موفقیت از لیست حذف شد
"
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#admins~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🥂 به بخش کنترل ادمین ها خوش آمدید

⚙ پنل برای کانال {$chat->result->title} باز شده است
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"➕ افزودن ادمین",'callback_data'=>"addadmin~$channel_id"]],
[['text'=>"➖ حذف ادمین",'callback_data'=>"removeadmin~$channel_id"]],
[['text'=>"🔙 برگشت",'callback_data'=>"info~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#swearword~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"
🥂 به بخش کلمات فیلتر خوش آمدید

⚙ پنل برای کانال {$chat->result->title} باز شده است
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"➕ افزودن کلمه",'callback_data'=>"addword~$channel_id"]],
[['text'=>"➖ حذف کلمه",'callback_data'=>"removeword~$channel_id"]],
[['text'=>"🔙 برگشت",'callback_data'=>"info~$channel_id"]],
]])
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#addword~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$datas['step'] = $data;
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"➕ کلمه‌ی مورد نظر را جهت فیلتر کردن ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#removeword~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$datas['step'] = $data;
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"➖ کلمه‌ی مورد نظر جهت حذف فیلتر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#addadmin~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$datas['step'] = $data;
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"➕ آیدی عددی ادمین جدید را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
elseif(preg_match('#removeadmin~(.*)#',$data,$exp)){
$channel_id = $exp[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$datas['step'] = $data;
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"➖ آیدی عددی ادمین را برای عزل کردن ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}else{
bot('answercallbackquery',[
'callback_query_id'=>$user_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'show_alert'=>true
]);
}
}
}
}
//-----------------------------------------------------------------------------------------------
if(isset($text) and $tc == 'private'){
if(strpos($text,'/start') !== false){
if(!file_exists("user/$from_id/$from_id.json")){
$invite = explode(' ',$text)[1] ?? false;
mkdir("user/$from_id");
$datas = ['step'=>'none','coin'=>0,'zir'=>0,'invite'=>((is_numeric($invite) and $from_id != $invite and file_exists("user/$invite/$invite.json")) ? $invite : 0),'channels'=>[]];
Save("user/$from_id/$from_id.json",$datas);
}
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🌹 <i>سلام به ربات ضد خیانت خوش آمدید</i> 🌹",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
if(file_exists("user/$from_id/$from_id.json")){
$datas = json_decode(file_get_contents("user/$from_id/$from_id.json"),true);
}else{
mkdir("user/$from_id");
$datas = ['step'=>'none','coin'=>0,'zir'=>0,'channels'=>[]];
Save("user/$from_id/$from_id.json",$datas);
}
if(isset($channel)){
if(in_array(bot('getChatMember',['chat_id'=>$channel,'user_id'=>$from_id])->result->status,['left','kicked','restricted'])){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📢 جهت استفاده از ربات در کانال $channel ابتدا عضو شوید",
'parse_mode'=>'HTML'
]);
exit();
}
}
if(isset($datas['invite']) and $datas['invite'] != 0){
$invite = $datas['invite'];
$info = json_decode(file_get_contents("user/$invite/$invite.json"),true);
$info['coin'] += 1;
$info['zir'] += 1;
Save("user/$invite/$invite.json",$info);
unset($datas['invite']);
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$invite,
'text'=>"🎉 یک کاربر با لینک زیرمجموعه گیری شما وارد ربات شد و شما 1 سکه پاداش گرفتید.",
'parse_mode'=>'HTML'
]);
}
if($text == '🔙 برگشت'){
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔙 به منوی اصلی برگشتید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
elseif($text == '📮 پشتیبانی و قوانین'){
  bot('sendmessage',[
    'chat_id' => $chat_id,
    'text' => "🌟 قوانین ضد خیانت اوپرا :

🔸ربات بدون ایراد است و غیر نفوذ اگر کسی بی دلیل بدگویی پشت ربات انجام دهد حساب کاربر مسدود می‌شود و دیگر آزاد نخواهد شد.
🔸آیدی پشتیبانی رسمی ضد خیانت اوپرا @haditaktaz است.
قانون های دیگر بزودی اضافه می‌شود.

❣ربات و کانال اطلاع رسانی ضد خیانت اوپرا :
@ZedKhyanatt
@ZedKhyanatBot",
    'parse_mode'=>'HTML',
'reply_markup'=>$back
    ]);
  
}
elseif($text == '📚 راهنما'){

  bot('sendmessage',[

    'chat_id' => $chat_id,
    'text' => "📚 راهنمای ضد خیانت اوپرا :

⚙ ربات اوپرا مخصوص محافظت کانال و گروه های شما کاربران گرامی است و اجازه نمی دهد که ادمین های شما به شما خیانت بکند.

⚙ برای ثبت هر کانال به 2 سکه نیاز است و تنها روش به دست آوردن سکه زیرمجموعه گیری است.

❣ربات و کانال اطلاع رسانی ضد خیانت اوپرا :
@ZedKhyanatt
@ZedKhyanatBot",
    'parse_mode'=>'HTML',
'reply_markup'=>$menu
    ]);
  
}
elseif($text == '🔖 سوالات متداول'){

  bot('sendmessage',[
    'chat_id' => $chat_id,
    'text' => "✅ در این بخش سوالات مهم کاربران ربات نوشته می‌شود:

📝 آیا ربات ویژگی های خوبی برای محافظت از کانال دارد؟ بله ربات تمام قابلیت های کانال را داراست و به خوبی میتوانید استفاده کنید.
📝 آیا ادمین خیانت کار بعد حذف چند کاربر از کانال عزل می‌شود؟ ادمین شما بعد اینکه ۵ اخطار بگیرد یعنی ۵ نفر را از کانال حذف کند به صورت آنی عزل می‌شود.
📝 آیا امنیت ربات کامل است و قابل هک و نفوذ است یا خیر؟ خیر ضد خیانت اوپرا غیرقابل هک هست و نگرانی ندارد.

❣ربات و کانال اطلاع رسانی ضد خیانت اوپرا :
@Zedkhyanatt
@ZedkhyanatBot",
    'parse_mode'=>'HTML',
'reply_markup'=>$menu
    ]);
  
}
elseif($text == '🔐 حساب کاربری'){
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
$count = count($datas['channels']);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
👤 آیدی عددی شما : $from_id

🎊 تعداد زیر مجموعه های شما : {$datas['zir']}

💰 تعداد سکه شما : {$datas['coin']}

📢 تعداد کانال های ثبت شده : $count
",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
elseif($text == '🗣 زیرمجموعه گیری'){
bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>'https://t.me/sbqkaowhe/15',
'caption'=>"💎 ضد خیانت اوپرا 💎

💠محافظ کانال و گروه های شما در برابر ادمین ها.
💠امنیت بالا ، سرعت بالا در خدمات.
💠کاملا امن و ضد نفوذ.

🔰همین حالا استارت کن و کانال خودتو امن کن :
https://t.me/$botuser?start=$from_id",
'parse_mode'=>'HTML'
]);
}
elseif($text == '📢 ثبت کانال جدید'){
$datas['step'] = 'newchannel';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"📢 یوزرنیم کانال خود یا آیدی عددی کانال خود را با -100 ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == 'newchannel'){
if($datas['coin'] >= 2){
$chat = bot('getChat',['chat_id'=>$text]);
if($chat->ok and $chat->result->type == 'channel'){
$channel_id = $chat->result->id;
$chekjoin = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$botid])->result->status;
if($chekjoin == 'administrator'){
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
if(in_array($channel_id,$datas['channels'])){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ کانال {$chat->result->title} از قبل ثبت شده است",
'reply_markup'=>$back
]);
}else{
$datas['step'] = 'none';
$datas['coin'] = $datas['coin'] - 2;
$datas['channels'][] = $channel_id;
Save("user/$from_id/$from_id.json",$datas);
mkdir("data/$channel_id");
mkdir("channel/$channel_id");
Save("channel/$channel_id/$channel_id.json",['admin'=>$from_id,'swearwords'=>[]]);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"✅ کانال {$chat->result->title} با موفقیت ثبت شد",
'reply_markup'=>$menu
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"❌ متاسفانه شما مالک این کانال نیستید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"❌ لطفاً ابتدا ربات را ادمین کانال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"❌ لطفاً آیدی کانال را به درستی ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🥀 موجودی شما برای ثبت کانال کافی نمیباشد",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
}
elseif($text == '⚙ مدیریت کانال ها'){
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
$key = [];
foreach($datas['channels'] as $channel_user){
$key[] = [['text'=>(bot('getChat',['chat_id'=>$channel_user])->result->title),'callback_data'=>"info~$channel_user"]];
}
if(empty($key)){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ شما کانالی در ربات ما ثبت نکردید",
'parse_mode'=>'HTML'
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"✏️ جهت مدیریت هر کانال روی نام کانال مورد نظر خود کلیک کنید",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$key])
]);
}
}
elseif(preg_match('#addword~(.*)#',$datas['step'],$exp)){
$channel_id = $exp[1];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$channel_data['swearwords'][] = $text;
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"➕ کلمه مورد نظر به لیست کلمات فیلتر شده اضافه شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"swearword~$channel_id"]],
]])
]);
}else{
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif(preg_match('#removeword~(.*)#',$datas['step'],$exp)){
$channel_id = $exp[1];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$search = array_search($text,$channel_data['swearwords']);
unset($channel_data['swearwords'][$search]);
Save("channel/$channel_id/$channel_id.json",$channel_data);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"➖ کلمه مورد نظر از لیست کلمات فیلتر شده حذف شد",
'parse_mode'=>'HTML',
'reply_markup'=>$back,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"swearword~$channel_id"]],
]])
]);
}else{
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif(preg_match('#addadmin~(.*)#',$datas['step'],$exp)){
$channel_id = $exp[1];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$result = bot('promoteChatMember',[
'chat_id'=>$channel_id,
'user_id'=>$text,
'can_manage_chat'=>true,
'can_change_info'=>false,
'can_post_messages'=>true,
'can_edit_messages'=>true,
'can_delete_messages'=>true,
'can_invite_users'=>true,
'can_restrict_members'=>true,
'can_manage_voice_chats'=>true,
'is_anonymous'=>false
]);
if($result->ok){
if(is_numeric($text)) Save("data/$channel_id/$text.json",['warn'=>0]);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت ادمین شما ترفیع داده شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"admins~$channel_id"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متاسفانه خطایی در ترفیع دادن به ادمین شما پیش آمده است
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"admins~$channel_id"]],
]])
]);
}
}else{
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
elseif(preg_match('#removeadmin~(.*)#',$datas['step'],$exp)){
$channel_id = $exp[1];
$status = bot('getChatMember',['chat_id'=>$channel_id,'user_id'=>$from_id])->result->status;
if($status == 'creator'){
$result = bot('promoteChatMember',[
'chat_id'=>$channel_id,
'user_id'=>$text,
'can_manage_chat'=>false,
'can_change_info'=>false,
'can_post_messages'=>false,
'can_edit_messages'=>false,
'can_delete_messages'=>false,
'can_invite_users'=>false,
'can_restrict_members'=>false,
'can_manage_voice_chats'=>false,
'is_anonymous'=>false
]);
if($result->ok){
if(is_numeric($text)) unlink("data/$channel_id/$text.json");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
✅ با موفقیت ادمین شما عزل شد
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"admins~$channel_id"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
❌ متاسفانه خطایی در عزل کردن ادمین شما پیش آمده است
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"🔙 برگشت",'callback_data'=>"admins~$channel_id"]],
]])
]);
}
}else{
$datas['step'] = 'none';
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔑 شما دیگر مالک این کانال نیستید",
'parse_mode'=>'HTML',
'reply_markup'=>$menu
]);
}
}
//-----------------------------------------------------------------------------------------------
elseif(in_array($from_id,$admin)){
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
$member = count(glob("user/*"));
$channels = count(glob("channel/*"));
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
📉 تعداد کل کاربران ربات : $member

📈 تعداد کل کانال های ربات : $channels
",
'parse_mode'=>'HTML',
'reply_markup'=>$panel
]);
}
elseif($text == '📑 لیست چنل ها'){
$channels = [];
foreach(glob("channel/*") as $path){
$channel_id = explode('/',$path)[1];
$chat = bot('getChat',['chat_id'=>$channel_id]);
if($chat->ok){
$channels[] = ($chat->result->title.' : '.($chat->result->active_usernames ? '@'.implode(' , @',$chat->result->active_usernames) : $chat->result->invite_link));
}
if(count($channels) >= 10){
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>implode(PHP_EOL,$channels)
]);
$channels = [];
}
}
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>implode(PHP_EOL,$channels).PHP_EOL.PHP_EOL.'📑 لیست تمام چنل ها برای شما ارسال شدند'
]);
}
elseif($text == "➕ افزایش سکه"){
$datas['step'] = "add";
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "add" and isset($text) and is_numeric($text)){
if(file_exists("user/$text/$text.json")){
$datas['step'] = "addcoin-$text";
Save("user/$from_id/$from_id.json",$datas);
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
elseif(strpos($datas['step'],"addcoin-") !== false and isset($text) and is_numeric($text)){
$id = explode("-",$datas['step'])[1];
if(is_numeric($text)){
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
$info = json_decode(file_get_contents("user/$id/$id.json"),true);
$info['coin'] += $text;
Save("user/$id/$id.json",$info);
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
elseif($text == "➖  کسر سکه"){
$datas['step'] = "low";
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "low" and isset($text) and is_numeric($text)){
if(file_exists("user/$text/$text.json")){
$datas['step'] = "lowcoin-$text";
Save("user/$from_id/$from_id.json",$datas);
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
elseif(strpos($datas['step'],"lowcoin-") !== false and isset($text) and is_numeric($text)){
$id = explode("-",$datas['step'])[1];
if(is_numeric($text)){
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
$info = json_decode(file_get_contents("user/$id/$id.json"),true);
$info['coin'] -= $text;
Save("user/$id/$id.json",$info);
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
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "blockid" and isset($text) and is_numeric($text)){
if(file_exists("user/$text/$text.json")){
$info = json_decode(file_get_contents("user/$text/$text.json"),true);
$info['step'] = "block";
Save("user/$text/$text.json",$info);
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
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
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"🔰 ایدی کاربر مورد نظر را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "unblockid" and isset($text) and is_numeric($text)){
if(file_exists("user/$text/$text.json")){
$info = json_decode(file_get_contents("user/$text/$text.json"),true);
$info['step'] = "none";
Save("user/$text/$text.json",$info);
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
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
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"📨 پیام خود را ارسال کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "sendall" and isset($update->message)){
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
foreach(glob("user/*/*.json") as $key){
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
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"💭 پیام خود را فوروارد کنید",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
}
elseif($datas['step'] == "forall" and isset($update->message)){
$datas['step'] = "none";
Save("user/$from_id/$from_id.json",$datas);
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"♻️ در حال ارسال . . . !",
'parse_mode'=>'HTML',
'reply_markup'=>$back
]);
foreach(glob("user/*/*.json") as $key){
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
}
}
//-----------------------------------------------------------------------------------------------
if(preg_match('~panel (.*)~',$query,$exp)){
$status = bot('getChatMember',['chat_id'=>$exp[1],'user_id'=>$from_id])->result->status;
if(isset($status) and $status == 'creator'){
$chat = bot('getChat',['chat_id'=>$exp[1]]);
$channel_id = $chat->result->id;
if(isset($channel_id) and file_exists("channel/$channel_id")){
$channel_data = json_decode(file_get_contents("channel/$channel_id/$channel_id.json"),true);
$photo = $channel_data['photo'] == 'on' ? '✅' : '❌';
$video = $channel_data['video'] == 'on' ? '✅' : '❌';
$audio = $channel_data['audio'] == 'on' ? '✅' : '❌';
$document = $channel_data['document'] == 'on' ? '✅' : '❌';
$gif = $channel_data['gif'] == 'on' ? '✅' : '❌';
$sticker = $channel_data['sticker'] == 'on' ? '✅' : '❌';
$location = $channel_data['location'] == 'on' ? '✅' : '❌';
$contact = $channel_data['contact'] == 'on' ? '✅' : '❌';
$voice = $channel_data['voice'] == 'on' ? '✅' : '❌';
$videonote = $channel_data['videonote'] == 'on' ? '✅' : '❌';
$link = $channel_data['link'] == 'on' ? '✅' : '❌';
$tag = $channel_data['tag'] == 'on' ? '✅' : '❌';
$hashtag = $channel_data['hashtag'] == 'on' ? '✅' : '❌';
$text = $channel_data['text'] == 'on' ? '✅' : '❌';
$swearword = $channel_data['swearword'] == 'on' ? '✅' : '❌';
$stickeranimated = $channel_data['stickeranimated'] == 'on' ? '✅' : '❌';
$editmedia = $channel_data['editmedia'] == 'on' ? '✅' : '❌';
$media = $channel_data['media'] == 'on' ? '✅' : '❌';
$forward = $channel_data['forward'] == 'on' ? '✅' : '❌';
$edittext = $channel_data['edittext'] == 'on' ? '✅' : '❌';
$adminaccess = $channel_data['adminaccess'] == 'on' ? '✅' : '❌';
bot('answerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>1,
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/156',
'title'=>'❗️اینجا کلیک کنید',
'input_message_content'=>[
'message_text'=>"
• پنل اصلی بخش قفل ها برای کانال {$chat->result->title} باز شد
",
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true
],'reply_markup'=>['inline_keyboard'=>[
[['text'=>"عکس $photo",'callback_data'=>"lock~$channel_id~photo"],['text'=>"فیلم $video",'callback_data'=>"lock~$channel_id~video"]],
[['text'=>"آهنگ $audio",'callback_data'=>"lock~$channel_id~audio"],['text'=>"فایل $document",'callback_data'=>"lock~$channel_id~document"]],
[['text'=>"گیف $gif",'callback_data'=>"lock~$channel_id~gif"],['text'=>"استیکر $sticker",'callback_data'=>"lock~$channel_id~sticker"]],
[['text'=>"موقعیت مکانی $location",'callback_data'=>"lock~$channel_id~location"],['text'=>"مخاطب $contact",'callback_data'=>"lock~$channel_id~contact"]],
[['text'=>"ویس $voice",'callback_data'=>"lock~$channel_id~voice"],['text'=>"فیلم سلفی $videonote",'callback_data'=>"lock~$channel_id~videonote"]],
[['text'=>"هشتگ $hashtag",'callback_data'=>"lock~$channel_id~hashtag"],['text'=>"تگ $tag",'callback_data'=>"lock~$channel_id~tag"]],
[['text'=>"فحش $swearword",'callback_data'=>"lock~$channel_id~swearword"],['text'=>"استیکر متحرک $stickeranimated",'callback_data'=>"lock~$channel_id~stickeranimated"]],
[['text'=>"فوروارد $forward",'callback_data'=>"lock~$channel_id~forward"],['text'=>"ویرایش پیام $edittext",'callback_data'=>"lock~$channel_id~edittext"]],
[['text'=>"ویرایش رسانه $editmedia",'callback_data'=>"lock~$channel_id~editmedia"],['text'=>"قفل رسانه $media",'callback_data'=>"lock~$channel_id~media"]],
[['text'=>"لینک $link",'callback_data'=>"lock~$channel_id~link"],['text'=>"متن $text",'callback_data'=>"lock~$channel_id~text"]],
[['text'=>"دسترسی ادمین ها $adminaccess",'callback_data'=>"lock~$channel_id~adminaccess"]],
[['text'=>"قفل خودکار",'callback_data'=>"automatically~$channel_id"]],
]
]
]
])
]);
}
}
}
?>
/////Sourrce_kade/// 
////مشکلی داشتید پیوی مراجعه کنید//// 
////@qqabr//// 
/////کسناموس کسی ک بدون منبع بردارع/////