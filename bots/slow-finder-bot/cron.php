<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
error_reporting(0);
http_response_code(200);
litespeed_finish_request();
define('API_KEY','"PUT-YOUR-BOT-TOKEN"');
//-----------------------------------------------------------------------------------------
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
$file = json_decode(file_get_contents('data.json'),true);
$users = $file['users'];
foreach($users as $user){
if($user != null){
if($file[$user]['tags'] != null and is_array($file[$user]['tags'])){
foreach($file[$user]['tags'] as $id){
$o = str_replace([' ','@'],null,$id);
$data = file_get_contents("https://t.me/$o");
preg_match_all('#<meta property="og:title" content="(.*)">#',$data,$matches2);
if(strpos($matches2[1][0],'Telegram: Contact') !== false){
$data = file_get_contents("https://t.me/$o");
preg_match_all('#<meta property="og:title" content="(.*)">#',$data,$matches2);
if(strpos($matches2[1][0],'Telegram: Contact') !== false){
$search = array_search($id, $file[$user]['tags']);
unset($file[$user]['tags'][$search]);
$file[$user]['free'][] = $id;
file_put_contents("data.json",json_encode($file,true));
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
💯 | تگــ $id هم اکنون در دسترس می‌باشد !
❕| میتوانید سریعا این تگ را قفل کنید 
"]);
}
}
}
}
}
}
header('Content-Type: application/json');
echo "OK . . . ! it's false";
if(file_exists("error_log")) unlink("error_log");
?>