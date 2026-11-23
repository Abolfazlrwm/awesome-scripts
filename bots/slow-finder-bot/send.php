<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');
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
$get = json_decode(file_get_contents('send.json'), true);
if($get == true){
$all_member = file_get_contents("data/datahU.txt");
$value = explode("\n","$all_member");
$nums = count($value);
if($get['type'] == 'send') {
if($get['count'] < $nums){
for($i = $get['count'];$i < $get['count'] + 50;$i++){
bot('SendMessage', [
'chat_id' => $value[$i],
'text' => $get['text'],
]);
}
$get = json_decode(file_get_contents('send.json'), true);
$msg = $get['count'] + 50;
$get['count'] = $msg;
file_put_contents('send.json',json_encode($get));
bot('SendMessage',[
'chat_id' => $get['chat_id'],
'text' => "📨 تاکنون برای $msg نفر ارسال شده است !",
'reply_to_message_id' => $get['msgid'],
'reply_markup' => $back_panel
]);
}else{
bot('SendMessage', [
'chat_id' => $get['chat_id'],
'text' => "✅ پیام شما برای تمام کاربران ارسال شد !",
'reply_to_message_id' => $get['msgid'],
'reply_markup' => $back_panel
]);
unlink('send.json');
}
}
if($get['type'] == 'forward'){
if($get['count'] < $nums){
for($i = $get['count'];$i < $get['count'] + 50;$i++){
bot('ForwardMessage', [
'chat_id' => $value[$i],
'from_chat_id' => $get['chat_id'],
'message_id'=>$get['msgid']
]);
}
$get = json_decode(file_get_contents('send.json'), true);
$msg = $get['count'] + 50;
$get['count'] = $msg;
file_put_contents('send.json',json_encode($get));
bot('SendMessage',[
'chat_id' => $get['chat_id'],
'text' => "📨 تاکنون برای $msg نفر ارسال شده است !",
'reply_to_message_id' => $get['msgid'],
'reply_markup' => $back_panel
]);
}else{
bot('SendMessage', [
'chat_id' => $get['chat_id'],
'text' => "✅ پیام شما برای تمام کاربران ارسال شد !",
'reply_to_message_id' => $get['msgid'],
'reply_markup' => $back_panel
]);
unlink('send.json');
}
}
}
echo "OK | true . . . !";
?>