<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

date_default_timezone_set('Asia/Tehran');
include("jdf.php");
define('LOLXD','"PUT-YOUR-BOT-TOKEN"');
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
$url = "https://api.telegram.org/bot".LOLXD."/".$method;
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
//=====متغیر ها=====//
$update = json_decode(file_get_contents('php://input'));
$TeXt = $update->message->text;
$TyPe = $update->message->chat->type;
$Pv_Id = $update->message->from->id;
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$time2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("H:i"));
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$date2 = str_replace(range(0,9),$fonts[array_rand($fonts)],date("Y/m/d"));
$fonts = [["𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]];
$dateF = str_replace(range(0,9),$fonts[array_rand($fonts)],jdate("Y/m/d"));
$random =["😂","😐","😁","😘","❤️","😍","😞","🌹","😆","😀","😕","😇","🙃","🙂","‌🥲","😎","🧐","🤪","🤩","🥳","🙁","🤯","😶","😴"];
$emoji = $random[array_rand($random)];
$file = json_decode(file_get_contents('time.json'),true);
$users = $file['users'];
foreach($users as $user){
if($user != null){
if($file[$user]['link'] != null and is_array($file[$user]['link']) and $file[$user]['bio'] != null and is_array($file[$user]['bio'])){
foreach($file[$user]['link'] as $link){
$get = json_decode(file_get_contents("https://api.telegram.org/bot".LOLXD."/getMe"));
$id = $get->result->id;
$info = json_decode(file_get_contents("https://api.telegram.org/bot".LOLXD."/getChatMember?chat_id=$link&user_id=$id"));
$status = $info->result->status;
$test = $info->result->can_change_info;
if($status == "left"){
$warn = $file[$user]['warn'];
$nees = $warn + 1 ;
$file[$user]['warn'] = "$nees";
file_put_contents("time.json",json_encode($file,true));
if($nees >= 3){
$dcoeockencn = file_get_contents("time.txt");
$fncndkckek = str_replace($link, "none . . . !", $dcoeockencn);
file_put_contents("time.txt", $fncndkckek);
$new = array_diff($file[$user]['link'], $link);
$file[$user]['link'] = $new;
$file[$user]['bio'] = $new;
file_put_contents("time.json",json_encode($file,true));
$file[$user]['warn'] = "0";
file_put_contents("time.json",json_encode($file,true));
}
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
📢 | لطفاً ربات را ادمین کانال ( $link ) کنید . . . ! 
"]);
}else{
if($test == "true"){
foreach($file[$user]['bio'] as $bio){
$bio = str_replace('TIME',$time2,$bio);
$bio = str_replace('DATE',$date2,$bio);
$bio = str_replace('FA',$dateF,$bio);
$bio = str_replace('EMOJI',$emoji,$bio);
bot('setChatDescription',[
'chat_id'=>"$link",
'description'=>"$bio"]);
}
}else{
$warn = $file[$user]['warn'];
$nees = $warn + 1 ;
$file[$user]['warn'] = "$nees";
file_put_contents("time.json",json_encode($file,true));
if($nees >= 20){
$dcoeockencn = file_get_contents("time.txt");
$fncndkckek = str_replace($link, "none . . . !", $dcoeockencn);
file_put_contents("time.txt", $fncndkckek);
$new = array_diff($file[$user]['link'], $link);
$file[$user]['link'] = $new;
$file[$user]['bio'] = $new;
file_put_contents("time.json",json_encode($file,true));
$file[$user]['warn'] = "0";
file_put_contents("time.json",json_encode($file,true));
}
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
❗️ | لطفاً دسترسی تغییر اطلاعات کانال را برای ربات در کانال ( $link ) باز کنید . . . !
"]);
}
}
}
}
}
}
if($TyPe == 'private'){
if($TeXt == "/test"){
bot('setChatTitle',[
'chat_id'=>"@Mafia_nit",
'title'=>"$time2"]);
bot('sendMessage',[
'chat_id'=>$Pv_Id,
'text'=>"
it's true ! 🌹 bye :)

Bot : @SlowFinderBot
Channel : @SlowTM
",
]);
exit();
}
if($TeXt == "/start"){
bot('sendMessage',[
'chat_id'=>$Pv_Id,
'text'=>"
it's true ! 🌹

Bot : @SlowFinderBot
Channel : @SlowTM
",
]);
exit();
}else{
bot('sendMessage',[
'chat_id'=>$Pv_Id,
'text'=>"
it's true ! 🌹

Bot : @SlowFinderBot
Channel : @SlowTM
",
]);
exit();
}
}
header('Content-type: application/json');
echo "OK . . . ! it's false";
if(file_exists("error_log")) unlink("error_log");
?>