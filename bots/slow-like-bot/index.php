<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
define('API_KEY','');
//===[ فانکشن ها ]===//
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
function Random($count){
return md5(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTU"),0,$count));
}
function StrSplit($str){
return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}
//============================================================================//
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)){
$chat_id = $update->message->chat->id;
$text = $update->message->text;
$caption = $update->message->caption ?? '𝑛𝑜𝑛𝑒 . . . !';
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$user_name = $update->message->from->username ?? '𝑛𝑜𝑛𝑒';
}
if (isset($update->callback_query)){
$chat_id = $update->callback_query->chat->id;
$data = $update->callback_query->data;
$user_id = $update->callback_query->id;
$message_id = $update->callback_query->message->message_id;
$from_id = $update->callback_query->from->id;
$first_name = $update->callback_query->from->first_name;
$last_name = $update->callback_query->from->last_name;
$user_name = $update->callback_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
if (isset($update->inline_query)){
$inline = $update->inline_query;
$user_id = $update->inline_query->id;
$query = $update->inline_query->query;
$from_id = $update->inline_query->from->id;
$first_name = $update->inline_query->from->first_name;
$last_name = $update->inline_query->from->last_name;
$user_name = $update->inline_query->from->username ?? '𝑛𝑜𝑛𝑒';
}
//============================================================================//
@$server = 'localhost';
@$username = 'likes';
@$password = 'PUT-YOUR-KEY-HERE';
@$db = 'likes';
$connect = mysqli_connect($server,$username,$password,$db);
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$from_id' LIMIT 1"));
$step = $datas["step"];
//============================================================================//
if(strpos($data,"like-") !== false){
$str = str_replace("like-","",$data);
$explode = explode("-",$str);
$like_id = $explode[0];
$cc = $explode[1];
$xz = "likes$cc";
$setlike = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `like` WHERE `hash` = '$like_id' LIMIT 1"));
$channeljoin = $setlike['join'];
if($channeljoin == 'none'){
$sets = $setlike["$xz"] + 1 ;
$connect->query("UPDATE `like` SET $xz = '$sets' WHERE `hash` = '$like_id' LIMIT 1");
$likeset = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `like` WHERE `hash` = '$like_id' LIMIT 1"));
$exp = explode("-",$setlike['button']);
foreach($exp as $emoji){
if($emoji != null){
$i++;
if($i == 7) break;
$nums = $likeset["likes$i"];
$keyLike[] = [['text'=>"$emoji ($nums)",'callback_data'=>"like-$like_id-$i"]];
}
}
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "you {$exp[$cc]} this . . . !",
'show_alert' =>false
]);
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' =>$keyLike
])
]);
}else{
$joqw = bot('getChatMember', [
'chat_id' =>$channeljoin,
'user_id' => $from_id
])->result->status;
if($joqw == 'member' and $joqw == 'creator' and $joqw == 'administrator'){
$sets = $setlike["$xz"] + 1 ;
$connect->query("UPDATE `like` SET $xz = '$sets' WHERE `hash` = '$like_id' LIMIT 1");
$likeset = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `like` WHERE `hash` = '$like_id' LIMIT 1"));
$exp = explode("-",$setlike['button']);
foreach($exp as $emoji){
if($emoji != null){
$i++;
if($i == 7) break;
$nums = $likeset["likes$i"];
$keyLike[] = [['text'=>"$emoji ($nums)",'callback_data'=>"like-$like_id-$i"]];
}
}
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "you {$exp[$cc]} this . . . !",
'show_alert' =>false
]);
bot('editMessageReplyMarkup',[
'chat_id'=>$from_id,
'message_id' => $message_id,
'reply_markup' => json_encode([
'inline_keyboard' =>$keyLike
])
]);
}else{
bot('answercallbackquery', [
'callback_query_id' =>$user_id,
'text' => "bye",
'show_alert' =>false
]);
}
}
}
//============================================================================//
if($datas['id'] == false){
$connect->query("INSERT INTO `user` (`id` , `step`) VALUES ('$from_id','none')");
}
if($text == "/start"){
$connect->query("UPDATE `user` SET `step` = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Let's create a message with emoji like-buttons , First , send me the message , It can be anything — a text , photo , video , even a sticker",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/help"){
$connect->query("UPDATE `user` SET `step` = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"I can help you create posts with emoji-based like buttons, share them with your friends and collect their reactions My masters are currently testing how things like me will work in large communities, so for now you can only send my posts to one-on-one chats, sorry

Try me in inline mode as well to quickly create posts with 👍/👎 buttons To do this, start a message with ‘@like …’ in any chat, then type your post",
'parse_mode'=>'HTML',
]);
exit();
}
if($text == "/cancel"){
if($step != "none"){
$connect->query("UPDATE `user` SET `step` = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"The command has been cancelled. Just send me something if you want to start",
'parse_mode'=>'HTML',
]);
}else{
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"No active command to cancel , I wasn't doing anything anyway 💤",
'parse_mode'=>'HTML',
]);
}
exit();
}
if(strpos($step,'SendEmoji') !== false and $tc == 'private'){
$Step = explode("-","$step");
$type = $Step[1];
$link = $Step[2];
$caption = $Step[3];
$random = Random(6);
$connect->query("INSERT INTO `like` (`hash` , `type` , `file` , `caption` , `join` , `admin` , `likes1` , `likes2` , `likes3` , `likes4` , `likes5` , `likes6`) VALUES ('$random','$type','$link','$caption','none','$from_id','0','0','0','0','0','0')");
foreach(StrSplit($text) as $emoji){
if($emoji != null){
$i++;
if($i == 7) break;
$keyLike[] = [['text'=>"$emoji",'callback_data'=>"like-$random-$i"]];
$setlike = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `like` WHERE `hash` = '$random' LIMIT 1"));
$connect->query("UPDATE `like` SET `button` = '{$setlike['button']}-$emoji' WHERE `hash` = '$random' LIMIT 1");
}
}
$keyLike[] = [['text'=>"Publish",'switch_inline_query'=>"share-$random"]];
if($type == "document"){
$file_id = bot('sendDocument',[
'chat_id'=>$from_id,
'document'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "video"){
$file_id = bot('sendVideo',[
'chat_id'=>$from_id,
'video'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "photo"){
$file_id = bot('sendPhoto',[
'chat_id'=>$from_id,
'photo'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "voice"){
$file_id = bot('sendVoice',[
'chat_id'=>$from_id,
'voice'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "audio"){
$file_id = bot('sendAudio',[
'chat_id'=>$from_id,
'audio'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "sticker"){
$file_id = bot('sendSticker',[
'chat_id'=>$from_id,
'sticker'=>$link,
'caption'=>$caption,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
if($type == "text"){
$file_id = bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>$link,
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyLike
])
])->result->message_id;
}
$connect->query("UPDATE `user` SET `step` = 'none' WHERE id = '$from_id' LIMIT 1");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
👍 Post created , Now use the ‘Publish’ button to send it to your friends
",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$file_id,
'reply_markup'=>json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true])
]);
}
if($step == "none"){
if(isset($update->message->photo)){
$filephoto = $update->message->photo;
$file_id = $filephoto[count($filephoto)-1]->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-photo-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->voice)){
$file_id = $update->message->voice->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-voice-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->audio)){
$file_id = $update->message->audio->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-audio-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->video)){
$file_id = $update->message->video->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-video-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->sticker)){
$file_id = $update->message->sticker->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-sticker-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->document)){
$file_id = $update->message->document->file_id;
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-document-$file_id-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
if(isset($update->message->text)){
$connect->query("UPDATE `user` SET `step` = 'SendEmoji-text-$text-$caption' WHERE id = '$from_id' LIMIT 1");
bot('sendmessage',[
'chat_id'=>$from_id,
'text'=>"Now send me one or more emoticons that we will use for buttons ( up to 6 emoji ) Use /cancel if you changed your mind",
'parse_mode'=>'HTML',
]);
}
}
if(strpos($query, 'share-') !== false){
$like_id = explode('share-',"$query")[1];
$setlike = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `like` WHERE `hash` = '$like_id' LIMIT 1"));
$Link = $setlike['file'];
$type_file = $setlike['type'];
$txt = $setlike['caption'];
$exp = explode("-",$setlike['button']);
foreach($exp as $emoji){
if($emoji != null){
$i++;
if($i == 7) break;
$nums = $setlike["likes$i"];
$keyLike[] = [['text'=>"$emoji ($nums)",'callback_data'=>"like-$like_id-$i"]];
}
}
if($type_file == "document"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'document',
'title'=>"❤️ Click Here . . . !",
'document_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "video"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'video',
'title'=>"❤️ Click Here . . . !",
'video_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "photo"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'photo',
'title'=>"❤️ Click Here . . . !",
'photo_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "voice"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'voice',
'title'=>"❤️ Click Here . . . !",
'voice_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "audio"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'audio',
'title'=>"❤️ Click Here . . . !",
'audio_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "sticker"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=>base64_encode(rand(5,555)),
'type'=>'sticker',
'title'=>"❤️ Click Here . . . !",
'sticker_file_id'=>$Link,
'caption'=>$txt,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true,
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
if($type_file == "text"){
bot('AnswerInlineQuery',[
'inline_query_id'=>$user_id,
'cache_time'=>0,
'is_personal'=>true,
'results'=>json_encode([[
'id'=> base64_encode(1),
'type'=>'article',
'thumb_url'=>'https://t.me/Mafia_nit/158',
'title'=>"❤️ Click Here . . . !",
'input_message_content'=>[
'message_text'=>$Link,
'parse_mode'=>'HTML',
'disable_web_page_preview'=>true
],
'reply_markup'=>[
'inline_keyboard'=>$keyLike
]
],
])
]);
}
exit();
}