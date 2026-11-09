<?php
// IR Creator
error_reporting(0);
ob_start();
date_default_timezone_set("Asia/Tehran");
///////////////////
$domain = "https://domain.ir"; // دامنه
$creator_id = USERID; // آیدی عددی مالک
$creator_username = "USERNAMEADMIN"; // ایدی ادمین - پشتیبانی بدون @
$channel_asli_id = "USERNAMEASLI"; // ایدی کانال اصلی بدون @
$channel_buy_id = "USERNAMEBUY"; // ایدی کانال فروشگاه بدون @
$channel_mahsol_id  = "-100USERIDBUY"; // آیدی عددی کانال فروشگاه با -100
$username_pedar_bot = "IDCREATORBOT"; // ایدی رباتساز بدون @
$name_botsaz_fa = "اسم رباتساز"; // اسم رباتساز به فارسی
$name_botsaz_en = "Name Creator Bot"; // اسم رباتساز به انگلیسی
$userid_chn_gozarsh_log = "-100USERIDLOG"; // ایدی عددی کانال گزارشات بصورت -100
$group_admins_id = "-100USERIDADMINSGAP"; // ایدی عددی گروه ادمین برای تایید کردن و رد کردن محصول با -100
$emtiaz_create_bot = 15; //امتیاز ساخت ربات
$emtiaz_delete_bot = 15; //امتیاز حذف ربات
$emtiaz_new = 15; //امتیاز اولین ورود
$emtiaz_zirmaj = 15; //امتیاز بابت زیرمجموعه
$tedad_creat_bot = 1;
//define('API_KEY' , $token_pedar);
define('API_KEY' , "TOKENBOT"); // توکن ربات

//////////////////
$con=mysqli_connect("localhost","USER","PASSWORD","USERNAME"); // دیتابیس

$telegram_ip_ranges = [

['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20

['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22

];



$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));

$ok=false;



foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {

    // Make sure the IP is valid.

    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));

    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));

    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;

}

if (!$ok) die("");
// Check connection
if (mysqli_connect_errno($con))
 {
   sm($creator_id,"Failed to connect to MySQL: " . mysqli_connect_error());
 }else{
$sql = "CREATE TABLE `user` 
 ( 
 chatid BIGINT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid BIGINT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
zirmaj INT,
emtiaz INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 INT,
Other4 TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `foroshmahsol` 
 ( 
 `id` BIGINT,
sellerid BIGINT,
userid TEXT,
`date` CHAR(15),
`time` CHAR(15),
`tozihat` TEXT,
`type` TEXT,
`caption` TEXT,
`file` TEXT,
`price` TEXT,
`ok` TEXT,
Other TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `amarbot`
(
bot TEXT,
token TEXT,
userid TEXT,
username TEXT,
creatorid INT
)
";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `blocklist` 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);
}

$sql = "CREATE TABLE `foroshgah`
(
bot TEXT,
tozih TEXT,
price TEXT
)
";
mysqli_query($con,$sql);

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function curl($url){
$cSession = curl_init($url);
//curl_setopt($cSession,CURLOPT_URL,"$url");
 curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
 curl_setopt($cSession,CURLOPT_HEADER, false);

$result=curl_exec($cSession);

if(curl_error($ch)){
        return curl_error($ch);
    }else{
return $result;
}
curl_close($cSession);

}
function random($length){
//create a or array string called chars
        $chars ="16953060294965954949394012039369223456789";
         $str = "";
        //is the variable which is equal to the length of the string called chars
        $size = strlen($chars);
        for($i=0; $i<$length; $i++){
             $str .= $chars[rand(0, $size-1)];

    }
    return $str;
}

function sm($chatid,$text,$keyboard=null,$parse_mode= 'Html',$disable_web_page_preview=false){
 return bot('sendMessage',[
        'chat_id'=>$chatid,
        'text' =>$text,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function fm($chatid,$userid,$message){
    bot('ForwardMessage',[
        'chat_id'=>$chatid,
        'from_chat_id'=>$userid,
        'message_id'=>$message
        ]);
}
function sp($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendPhoto', [
        'chat_id' => $chat_id,
        'photo' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sv($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendVideo', [
        'chat_id' => $chat_id,
        'video' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sd($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendDocument', [
        'chat_id' => $chat_id,
        'document' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sa($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendAudio', [
        'chat_id' => $chat_id,
        'audio' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function svo($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendVoice', [
        'chat_id' => $chat_id,
        'voice' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function ss($chat_id,$file_id){
    bot('sendSticker', [
        'chat_id' => $chat_id,
        'sticker' => $file_id,
        ]);
}
function erm($chatid,$message,$reply_markup){
    bot('editMessageReplyMarkup',[
        'chat_id'=>$chatid,
        'message_id'=>$message,
        'reply_markup'=>$reply_markup
        ]);
}
function em($chatid,$text,$message,$keyboard=null,$disable_web_page_preview=false,$parse_mode =  'Html'){
    bot('EditMessageText',[
        'chat_id'=>$chatid,
        'text'=>$text,
        'message_id'=>$message,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function ToDie(){
global $con;
mysqli_close($con);
die();
}
function gregorian_to_jalali ($g_y, $g_m, $g_d,$str) 
{ 
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29); 
 
  
   $gy = $g_y-1600; 
   $gm = $g_m-1; 
   $gd = $g_d-1; 
 
   $g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400); 
 
   for ($i=0; $i < $gm; ++$i) 
      $g_day_no += $g_days_in_month[$i]; 
   if ($gm>1 and (($gy%4==0 and $gy%100!=0) or ($gy%400==0))) 
      /* leap and after Feb */ 
      $g_day_no++; 
   $g_day_no += $gd; 
 
   $j_day_no = $g_day_no-79; 
 
   $j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
   $j_day_no = $j_day_no % 12053; 
 
   $jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */ 
 
   $j_day_no %= 1461; 
 
   if ($j_day_no >= 366) { 
      $jy += div($j_day_no-1, 365); 
      $j_day_no = ($j_day_no-1)%365; 
   } 
 
   for ($i = 0; $i < 11 and $j_day_no >= $j_days_in_month[$i]; ++$i) 
      $j_day_no -= $j_days_in_month[$i]; 
   $jm = $i+1; 
   $jd = $j_day_no+1; 
 if($str) return $jy.'/'.$jm.'/'.$jd ;
   return array($jy, $jm, $jd); 
} 
 function div($a,$b) { 
    return (int) ($a / $b); 
} 
function amarcount($type){
global $con;
$result = mysqli_query($con, "SELECT * FROM $type");
$num_rows = mysqli_num_rows($result);
return $num_rows;
}
function getstep($chatid){
global $con;
$query = "SELECT * FROM user WHERE chatid='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row['step'];
}
}
function getvalue($table,$roow,$chatid,$row2){
global $con;
$query = "SELECT * FROM `$table` WHERE `$roow`='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row[$row2];
}
}
function getallvalue($table,$row2){
global $con;
$query = "SELECT * FROM `$table` ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$data[] = $row[$row2];
}
return $data;
}
function setvalue($table,$row2,$val2,$row1,$val1){
global $con;
$query = "UPDATE `$table` SET `$row1` = '$val1' WHERE `$row2` = '$val2'";
$result = mysqli_query($con,$query);
return $result;
}
function step($chatid,$step){
global $con;
$query = "UPDATE user SET step = '$step' WHERE chatid = $chatid";
$result = mysqli_query($con,$query);
}
function deletevalue($table,$row,$val){
global $con;
$sql = "DELETE FROM `$table` WHERE `$row`='$val'";
$result = mysqli_query($con,$sql);
}
function setcolumn($table,$col,$text){
global $con;
$insert_query = "INSERT INTO `$table`($col) VALUES ('$text')";
mysqli_query($con, $insert_query);

}
function isetrow($table,$row){
global $con;
$sql = "SELECT EXISTS(SELECT * FROM `$table` WHERE `$row`)";
return$result = mysqli_query($con,$sql);
}
function createrow($table,$after,$row,$type){
global $con;
$sql ="ALTER TABLE `$table` ADD `$row` $type after `$after`";
$result = mysqli_query($con,$sql);
}
if(!file_exists("Robat")){
mkdir("Robat");
}
function deleterow($table,$col){
global $con;
$query = "ALTER TABLE $table DROP $col";
$result = mysqli_query($con,$query);
}
function deletecolumn($table,$column,$chatid){
global $con;
$sql = "DELETE FROM `$table` WHERE `$column`='$chatid'";
return mysqli_query($con,$sql);
}

function isetval($table,$roow,$chatid,$row2){
global $con;
$query = "SELECT * FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
if(isset($row[$row2]) and !empty($row[$row2])){
return true;
}else{
return false; 
}
}
}
function isetcol($table,$roow,$chatid){
global $con;
$query = "SELECT $roow FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
if(count(mysqli_fetch_array($result)) == 0 ) return false; 
else return true;
}
function columnexsits($table,$row){
global $con;
$result = mysqli_query($con,"SHOW COLUMNS FROM `$table` LIKE '$row'");
$exists = (mysqli_num_rows($result))?TRUE:FALSE;
return $exists;
}
function getCoin($chatid){
$coin = getvalue("user","chatid",$chatid,"emtiaz");
return $coin;
}
function setCoin($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"emtiaz",$text);
return $coin;
}
function getZirmaj($chatid){
$coin = getvalue("user","chatid",$chatid,"zirmaj");
return $coin;
}
function setZirmaj($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"zirmaj",$text);
return $coin;
}
function getOther($chatid){
$coin = getvalue("user","chatid",$chatid,"Other");
return $coin;
}
function setOther($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other",$text);
return $coin;
}
function getOther2($chatid){
$coin = getvalue("user","chatid",$chatid,"Other2");
return $coin;
}
function setOther2($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other2",$text);
return $coin;
}
function alert($querid,$qt,$type=true){
return bot('answerCallbackQuery',[
'callback_query_id'=>$querid,
'text'=>$qt,
'show_alert'=>$type
]);
}
/*
$sql = "ALTER TABLE `user` MODIFY `userid` BIGINT";
$vv = mysqli_query($con,$sql);
if($vv !== false){
sm("$userid_chn_gozarsh_log",json_encode($vv));
fm("$userid_chn_gozarsh_log",$chatid,$messageid);
}
*/
$admin = "$creator_id";
$admindaniyal = "$creator_id";
$update = json_decode(file_get_contents('php://input'));

$Message = $update->message;
$messageid = $Message->message_id;
$text = $Message->text;
$chatid = $Message->chat->id;
$fromid = $Message->from->id;

$firstname = $Message->from->first_name;
$username = $Message->from->username;
$chusername = $update->channel_post->chat->id;
$type2= $update->channel_post->chat->type;
$query = $update->callback_query;
$queryid=$query->id;
$querydata=$query->data;

$qmessage = $query->message;
$qmid = $qmessage->message_id;
$qname = $query->from->first_name;
$quser = $query->from->username;
$qid = $query->from->id;
$querychatid=$query->message->chat->id;
$gpname = $Message->chat->title;
$gpuser = $Message->chat->username;
$bio = $Message->chat->bio;
$description = $Message->chat->description;
$newmember = $Message->new_chat_members;
$newid = $newmember[0]->id;
$newname = $newmember[0]->first_name;
$newuser = $newmember[0]->username;
$type= $Message->chat->type;
$photo= $Message->photo[0]->file_id;
$video = $Message->video->file_id;
$sticker = $Message->sticker->file_id;
$audio = $Message->audio->file_id;
$document= $Message->document->file_id;
$voice = $Message->voice->file_id;
$dice = $Message->dice->emoji;
$caption = $Message->caption;
$getmebot = bot("getMe");
$idbot = $getmebot->result->id;
$botname = $getmebot->result->first_name;
$botuser = $getmebot->result->username;
$datesh = gregorian_to_jalali(date("Y"),date("m"),date("d"),"/");
$time = date("H:i:s");
$datem = date("Y:m:d");
$step=getstep($fromid);
$keyback = json_encode([
'keyboard'=>[
[["text"=>"بازگشت 🔙"]]
],
'resize_keyboard'=>true
]);
$keymahsol = json_encode([
'keyboard'=>[
[["text"=>"فروش ربات 🤖"]],
[["text"=>"فروش محصول 🛍"]],
[["text"=>"بازگشت 🔙"]]
],
'resize_keyboard'=>true
]);

if(isetval("user","chatid",$chatid,"keyboard")){
$keyboard = getvalue("user","chatid",$chatid,"keyboard");
$keykarbar='{"keyboard":['.$keyboard.',[{"text":"بازگشت 🔙"}]],"resize_keyboard":true}';
}
if($type=="private"){
$sql = "CREATE TABLE `qw$chatid`
(
`bot` TEXT,
`token` TEXT
)";

mysqli_query($con, $sql);



$insert_query = "INSERT INTO `user`
(
chatid,
userid,
firstname,
lastname,
username,
joindatesh,
joindatem,
jointime,
zirmaj,
emtiaz,
`Other3`
)
VALUES ('$chatid','$fromid','$firstname','$lastname','$username','$datesh','$datem','$time','0','$emtiaz_new','1')";
$vv = mysqli_query($con, $insert_query);
if($vv !== false){
sm("$userid_chn_gozarsh_log","<a href='tg://openmessage?user_id=$chatid'>$firstname</a> <b>New Joined...!</b>\n\n<b>Description </b>: <code>".json_encode($vv)."</code>");
fm("$userid_chn_gozarsh_log",$chatid,$messageid);
}

}
if(!isetrow("user","Other1")){
createrow("user","emtiaz","Other1","TEXT");
}
if(!isetrow("user","Other3")){
createrow("user","emtiaz","Other3","INT");
}
if(!isetrow("user","Other4")){
createrow("user","Other3","Other4","TEXT");
}
if(!isetrow("user","Other5")){
createrow("user","Other4","Other5","TEXT");
}
if(!isetrow("user","Other6")){
createrow("user","Other5","Other6","INT");
}
if(!isetrow("user","like")){
createrow("user","Other6","like","INT");
}
if(!isetrow("user","buy")){
createrow("user","like","buy","INT");
}
if(!isetrow("user","tedadcreate")){
createrow("user","buy","tedadcreate","INT");
}
if(!isetrow("foroshmahsol","like")){
createrow("foroshmahsol","Other","like","TEXT");
}
if(!isetrow("foroshmahsol","dislike")){
createrow("foroshmahsol","like","dislike","TEXT");
}
if(!isetrow("user","number")){
createrow("user","tedadcreate","number","TEXT");
}


if(isset($query)){
if(preg_match('/^rad\-\+\-([0-9]+)$/',$querydata,$ma)){
if($qid == $creator_id or $qid == $creator_id or $qid == $creator_id or $qid == $creator_id or $qid==$creator_id or $qid ==$creator_id){
$id = $ma[1];
$keytamam = json_encode([
"inline_keyboard"=>[
[["text"=>"محصول رد شد توسط ⛔ $quser ",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keytamam);
step($qid,"radkar$id");
sm($querychatid,"لطفا بگید چرا رد شده است؟!!\nاگر نمیخواهید متنی برای کاربر /notext را ارسال کنید");
return false; 
mysqli_close($con);
}
}
if(preg_match('/^taiid\-\+\-([0-9]+)$/',$querydata,$ma)){
if($qid == $creator_id or $qid == $creator_id or $qid == $creator_id or $qid == $creator_id or $qid==$creator_id or $qid ==$creator_id){
alert($update->callback_query->id,"تایید شد ، بنر به کانال فرستاده شد✅",true);
$id = $ma[1];
$keytamam = json_encode([
"inline_keyboard"=>[
[["text"=>"محصول تایید شده توسط $quser ",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keytamam);

$date = getvalue("foroshmahsol","id",$id,"date");
$time = getvalue("foroshmahsol","id",$id,"time");
$tedad = getvalue("foroshmahsol","id",$id,"Other");
$price = getvalue("foroshmahsol","id",$id,"price");
$toman = number_format($price);
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$likeseller = getvalue("user","chatid",$seller,"like");
if(empty($likeseller)){
$likeseller = 0;
}
$buyseller = getvalue("user","chatid",$seller,"buy");
if(empty($buyseller)){
$buyseller = 0;
}
$tozihat = getvalue("foroshmahsol","id",$id,"tozihat");
$typea = getvalue("foroshmahsol","id",$id,"type");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
if($typea=="text"){
$nok ="متن ✏";
}elseif($typea=="photo"){
$nok ="عکس 📸";
}elseif($typea=="video"){
$nok ="فیلم 🎥";
}elseif($typea=="document"){
$nok ="فایل 💾";
}elseif($typea=="audio"){
$nok ="موزیک 🎵";
}
$txt = "
ـفـــروـشـــ #محصول [5373052667671093676]

 ‌‌┅┅┅┅‌┅┅┅‌┅┅┅┅┅‌┅┅
[5431721976769027887] نوع محصول : $nok

[5334882760735598374] توضیحات محصول :
$tozihat

[5433614747381538714] حداکثر تعداد دانلود : $tedad
 ‌‌┅┅┅┅‌┅┅┅‌┅┅┅┅┅‌┅┅
[5449505950283078474] تعداد لایک های فروشنده : $likeseller
[5431736674147114227] تعداد فایل های فروخته شده : $buyseller
 ‌‌┅┅┅┅‌┅┅┅‌┅┅┅┅┅‌┅┅
[5431897022456145283] تاریخ : $date
[5413704112220949842] ساعت : $time
 ‌‌┅┅┅┅‌┅┅┅‌┅┅┅┅┅‌┅┅
[5375296873982604963] قیمت : $toman سکه
 ‌‌┅┅┅┅‌┅┅┅‌┅┅┅┅┅‌┅┅
 
برای خرید مستقیم محصول روی دکمه‌ی زیر ضربه بزنید [5470177992950946662]
";
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"💰 خـریـد مـحـصـول 💰",'callback_data'=>"mahsol-+-$id"]],
[["text"=>": تعداد خرید باقی مانده 📤",'callback_data'=>"--"],["text"=>"$tedad",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
sm("$channel_mahsol_id",$txt,$keyfor);
return false; 
mysqli_close($con);

}else{
alert($update->callback_query->id,"شما دسترسی نداری ‼️",false);
}
}

if(preg_match('/^like\-\+\-([0-9]+)$/',$querydata,$ma)){
$id = $ma[1];
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$tedad = getvalue("foroshmahsol","id",$id,"Other");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
if(!in_array($qid,$exp1)){
if(in_array($qid,$exp2)){
$str = str_replace("$qid,","",$dislikeone);
setvalue("foroshmahsol","id",$id,"dislike",$str);
}
setvalue("foroshmahsol","id",$id,"like",$likeone.$qid.",");
$likeseller = getvalue("user","chatid",$seller,"like");
if(empty($likeseller)){
$likeseller = 0;
}
$likeseller = $likeseller + 1;
setvalue("user","chatid",$seller,"like",$likeseller);
alert($update->callback_query->id,"لایک شما ثبت شد ✅",false);
}else{
alert($update->callback_query->id,"قبلا این پست را لایک کردید ‼️",false);
}
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"💰 خـریـد مـحـصـول 💰",'callback_data'=>"mahsol-+-$id"]],
[["text"=>": تعداد خرید باقی مانده 📤",'callback_data'=>"--"],["text"=>"$tedad",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keyfor);
}
if(preg_match('/^dislike\-\+\-([0-9]+)$/',$querydata,$ma)){
$id = $ma[1];
$tedad = getvalue("foroshmahsol","id",$id,"Other");
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
if(!in_array($qid,$exp2)){
if(in_array($qid,$exp1)){
$str = str_replace("$qid,","",$likeone);
setvalue("foroshmahsol","id",$id,"like",$str);
}
setvalue("foroshmahsol","id",$id,"dislike","$dislikeone$qid,");
$likeseller = getvalue("user","chatid",$seller,"like");
if(empty($likeseller)){
$likeseller = 0;
}
$likeseller = $likeseller - 1;
setvalue("user","chatid",$seller,"like",$likeseller);
alert($update->callback_query->id,"دیسلایک شما ثبت شد ✅",false);
}else{
alert($update->callback_query->id,"قبلا این پست را دیسلایک کردید ‼️",false);
}
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"💰 خـریـد مـحـصـول 💰",'callback_data'=>"mahsol-+-$id"]],
[["text"=>": تعداد خرید باقی مانده 📤",'callback_data'=>"--"],["text"=>"$tedad",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keyfor);
}

if(preg_match('/^mahsol\-\+\-([0-9]+)$/',$querydata,$ma)){
$id = $ma[1];
$cap = getvalue("foroshmahsol","id",$id,"caption");
$fileid = getvalue("foroshmahsol","id",$id,"file");
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$price = getvalue("foroshmahsol","id",$id,"price");
$typem = getvalue("foroshmahsol","id",$id,"type");
$tedad = getvalue("foroshmahsol","id",$id,"Other");

if($qid==$seller){
alert($update->callback_query->id,"شما نمی‌توانید محصول خود را خریداری کنید ‼️",true);
return false; 
mysqli_close($con);
}
$coin2 = getCoin($qid);
if($coin2 < $price){
alert($update->callback_query->id,"موجودی شما برای خرید این محصول کافی نمی‌باشد ‼\n\n💰 موجودی شما : $coin2",true);
}else{
$coin3 = $coin2 -$price;
setCoin($qid,$coin3);
$coin4 =getCoin($seller) + $price;
setCoin($seller,$coin4);
$mande = $tedad -1;
alert($update->callback_query->id,"محصول با موفقیت خریداری شد ✅\n\nجزئیات توسط ربات در پیوی شما ارسال میشود ‼",true);
$txt = "
یک محصول شما فروخته شد ✅

💰 قیمت محصول فروخته شده : $price

📤 تعداد خرید باقی‌ مانده : $mande

فایل فروخته شده 👇
";
sm($seller,$txt);
$buyseller = getvalue("user","chatid",$seller,"buy");
if(empty($buyseller)){
$buyseller = 0;
}
$buyseller = $buyseller + 1;
setvalue("user","chatid",$seller,"buy",$buyseller);
if($typem=="text"){
sm($seller,$fileid);
}elseif($typem=="photo"){
sp($seller,$fileid,$cap);
}elseif($typem=="video"){
sv($seller,$fileid,$cap);
}elseif($typem=="document"){
sd($seller,$fileid,$cap);
}elseif($typem=="audio"){
sa($seller,$fileid,$cap);
}
$txt = "
محصول شما با موفقیت خریداری شد ✅

محصول خریداری شده 👇
";
sm($qid,$txt);
if($typem=="text"){
sm($qid,$fileid);
}elseif($typem=="photo"){
sp($qid,$fileid,$cap);
}elseif($typem=="video"){
sv($qid,$fileid,$cap);
}elseif($typem=="document"){
sd($qid,$fileid,$cap);
}elseif($typem=="audio"){
sa($qid,$fileid,$cap);
}
if($tedad -1 == 0 or $tedad == 0){
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"❌ پایان ظرفیت ❌",'callback_data'=>"temam"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keyfor);
}else{
$tedad = $tedad -1;
setvalue("foroshmahsol","id",$id,"Other",$tedad);
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"💰 خـریـد مـحـصـول 💰",'callback_data'=>"mahsol-+-$id"]],
[["text"=>": تعداد خرید باقی مانده 📤",'callback_data'=>"--"],["text"=>"$tedad",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keyfor);

}
}

return false; 
mysqli_close($con);

}

if(preg_match('/^buy\-([A-Za-z0-9\_]+)\-\+\-([0-9]+)\-\+\-([0-9]+)$/',$querydata,$m)){

$userbot = $m[1];
$price = $m[2];
$seller = $m[3];


alert($update->callback_query->id,"درحال حاضر خرید و فروش ربات غیرفعال می‌باشد ⛔",true);
return false; 
mysqli_close($con);


if(empty(getvalue("qw$seller","bot",$userbot,"token"))){
sm($seller,"⚠️ خطا : کاربر گرامی، ربات @$userbot دیگر در مالکیت شما نیست! یا از لیست ربات های شما حذف شده است.\n و دیگر امکان فروش این ربات امکان پذیر نیست و محتوای آن در کانال فروشگاه پاک شد ‼");
alert($update->callback_query->id,"این ربات توسط فروشنده به یک کاربر دیگر انتقال مالکیت داده شده یا حذف شده است ‼\n\nما به فروشنده اعلان لازم را فرستادیم و این پست پاک میشود ‼",true);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
bot("DeleteMessage",[
'chat_id'=>$querychatid,
'message_id'=>$qmid
]);
return false; 
mysqli_close($con);
}
$coin2 = getCoin($qid);
if($coin2 < $price){
alert($update->callback_query->id,"موجودی شما برای خرید این ربات کافی نمی‌باشد ‼\n\n💰 موجودی شما : $coin2",true);
}else{
$coin3 = $coin2 -$price;
setCoin($qid,$coin3);
$coin4 =getCoin($seller) + $price;
setCoin($seller,$coin4);
sm($seller,"ربات شما با یوزرنیم @$userbot باموفقیت در کانال فروشگاه به فروش رفت و مقدار $price سکه به حساب شما واریز شد ✅\n\nیوزرنیم این ربات از لیست ربات های شما حذف شد ‼️",$keydo);
alert($update->callback_query->id,"ربات باموفقیت خریداری شد، یوزرنیم ربات در لیست ربات های شما افزوده شد ✅",true);
$file =file_get_contents("BotList/$userbot/$userbot.php");
$file2 = preg_replace('/(\$admin = ")(.*)(";)/','$admin = "'.$qid.'";',$file);
file_put_contents("BotList/$userbot/$userbot.php",$file2);
if(empty(getvalue("user","chatid",$qid,"keyboard"))){
$keyst='[{"text":""}],[{"text":""}]';
setvalue("user","chatid",$qid,"keyboard",$keyst);
}
$token =getvalue("qw$seller","bot",$userbot,"token");
$keysha = getvalue("user","chatid",$seller,"keyboard");
$cc = str_replace('[{"text":"'.$userbot.'"}],',"",$keysha);
setvalue("user","chatid",$seller,"keyboard",$cc);

$tedad = getvalue("user","chatid",$seller,"tedadcreate");
$two = $tedad -1;
setvalue("user","chatid",$seller,"tedadcreate",$two);

$key1 = getvalue("user","chatid",$qid,"keyboard");
$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$userbot.'"}],[{"text":""}]',$key1);
setvalue("user","chatid",$qid,"keyboard",$newdokme);
$sql ="INSERT INTO `qw$qid`(`bot`,`token`)VALUES ('$userbot','$token')";
mysqli_query($con,$sql);

deletevalue("qw$seller","bot",$userbot);
sm("$userid_chn_gozarsh_log","یک ربات در کانال بفروش رفت✅\n\nCreator : tg://user?id=$seller \nCreator ID : $seller\n\nSendFor :$qid\n\nUsername : @$userbot\n\nToken :\n$token");

$keyforosh = json_encode([
"inline_keyboard"=>[
[["text"=>"ربات به فروش رفت ✅",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
}
erm($querychatid,$qmid,$keyforosh);

}

return false; 
mysqli_close($con);
}
}
if($chatid == "$group_admins_id" and preg_match('/^radkar([0-9]+)$/',$step,$cv)){
if($text=="/notext@$username_pedar_bot"){
$id = $cv[1];
step($fromid,"");
sm($chatid,"بدون متن برای کاربر ارسال شد✅");
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$txt="محصول شما به دلایل نامعلوم از طرف مدیران رد شد ❌";
sm($seller,$txt);
}else{
if(isset($text)){
step($fromid,"");
sm($chatid,"متن برای کاربر ارسال شد و رد شد✅");
$id = $cv[1];
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$txt="محصول شما به دلایل زیر از طرف مدیران رد شد❌\n\n⚠ دلیل : \n$text";
sm($seller,$txt);
}
}
}
if(preg_match("/^(\/start ([0-9]+))$/i",$text)){
preg_match("/^(\/start (.*))$/i",$text,$match);
$user = $match[2];
setOther2($chatid,$user);
//$exp = getallvalue("user","chatid");
$text=="/start";
if(getvalue("user","chatid",$fromid,"Other3")==1){
setvalue("user","chatid",$fromid,"Other3",2);

}

//sm($chatid,"text$user");

}
if($chatid == "$group_admins_id" ){
return false; 
mysqli_close($con);
}
if( $chatid !=$admin){
$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel_asli_id&user_id=$chatid"),true);
$status = $url2["result"]["status"];
if($status !== "creator" and $status !== "administrator" and $status !=="member"){
$keybo=json_encode([
'inline_keyboard'=>[
[["text"=>"کانال فروشگاه 🛍️","url"=>"https://t.me/$channel_buy_id"],["text"=>'کانال اصلی 📣',"url"=>"https://t.me/$channel_asli_id"]],
[["text"=>"عضو شدم ✅","url"=>"https://t.me/$username_pedar_bot?start"]],
]
]);
sm($chatid,"برای فعال شدن ربات لطفاً در کانال های ما عضو شوید 👇\n\n@$channel_asli_id\n@$channel_buy_id\n\nبعد از عضویت در کانال دستور (/start) را ارسال کنید.",$keybo);
return false; 
mysqli_close($con);
}
}

if( $chatid !=$admin){
$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel_buy_id&user_id=$chatid"),true);
$status = $url2["result"]["status"];
if($status !== "creator" and $status !== "administrator" and $status !=="member"){
$keybo=json_encode([
'inline_keyboard'=>[
[["text"=>"کانال فروشگاه 🛍️","url"=>"https://t.me/$channel_buy_id"],["text"=>"کانال اصلی 📣","url"=>"https://t.me/$channel_asli_id"]],
[["text"=>"عضو شدم ✅","url"=>"https://t.me/$username_pedar_bot?start"]],
]
]);
sm($chatid,"برای فعال شدن ربات لطفاً در کانال های ما عضو شوید 👇\n\n@$channel_asli_id\n@$channel_buy_id\n\nبعد از عضویت در کانال دستور (/start) را ارسال کنید.",$keybo);
return false; 
mysqli_close($con);
}
}


if(getvalue("user","chatid",$chatid,"Other3")==2 or empty(getvalue("user","chatid",$chatid,"chatid"))){
$user = getOther2($chatid);
$co = getvalue("user","chatid",$user,"emtiaz");
$ozv = getvalue("user","chatid",$user,"zirmaj");
$co2 = $co + $emtiaz_zirmaj;
$ozv2 = $ozv + 1;
setvalue("user","chatid",$user,"emtiaz",$co2);
setvalue("user","chatid",$chatid,"Other3",0);
setvalue("user","chatid",$user,"zirmaj",$ozv2);
$txt1="
یک کاربر با لینک زیرمجموعه گیری شما عضو ربات شد و $emtiaz_zirmaj سکه به حساب شما افزوده شد ✅\n\n💰 موجودی شما : $co2
";
sm($user,$txt1);
$text=="/start";
}
$txtback="
🌹 سلام کاربرگرامی عزیز به ربات ‹ $name_botsaz_fa › خوش آمدید.

🔥 با رباتساز ‹ $name_botsaz_fa › شما می‌توانید ربات شخصی خود بسازید.


🌹 ⁪⁬⁮⁮⁮⁮Hello, Dear User, Welcome To ‹ $name_botsaz_en › 

🔥 With ‹ $channel_asli_id › You Can Make Your Own Bot.

📣 : @$channel_asli_id
";
$blocklist = getvalue("blocklist","chatid",$chatid,"chatid");
if(!empty($blocklist)){
if(isetval("blocklist","chatid",$chatid,"chatid") !==false or isetval("blocklist","chatid","@$username","chatid") !==false){
//step($chatid,"blocked"); 

  $key=json_encode([
       "hide_keyboard"=>true
       ]);
       sm($chatid,"شما از ربات بلاک شدید ‼️",$key);
 return false; 
mysqli_close($con);
 }}

$keynoyes=json_encode([
'keyboard'=>[
[["text"=>"بله ✅"],["text"=>"خیر ❌"]]
],
'resize_keyboard'=>true
]);
$keyasli=json_encode([
"keyboard"=>[
[["text"=>"ساخت ربات 🛠️"]],
[["text"=>"ربات های من 🗃️"],['text'=>'تنظیمات ربات ⚙']],
[["text"=>"حساب کاربری 👤"],["text"=>"فروشگاه ربات 🛍️"]],
[["text"=>"🛒"],["text"=>"🌐"],["text"=>"📣"],["text"=>"👨‍💻"]],
[["text"=>"🚦"],["text"=>"⁉️"],["text"=>"📚"]],
],
]);
$keyseke=json_encode([
"keyboard"=>[
[["text"=>"حساب من 🔐"]],
[["text"=>"زیرمجموعه 👥"],["text"=>"سکه روزانه ⏰"]],
[["text"=>"انتقال سکه 💸"],["text"=>"خرید سکه 💰"]],
//[["text"=>"گردونه شانس 🔄"]],
[["text"=>"بازگشت به منوی اصلی 🔙"]],
],
]);
$coin = getvalue("user","chatid",$chatid,"emtiaz");
$zirma = getvalue("user","chatid",$chatid,"zirmaj");
$joindatesh = getvalue("user","chatid",$chatid,"joindatesh");
$jointime = getvalue("user","chatid",$chatid,"jointime");
$soudzir = $zirma * 10;
$etlat=json_encode([
'inline_keyboard'=>[
[['text' => "🖥 اشتراک گذاری حساب کاربری",'url' => "https://telegram.me/share/url?url= شناسه کاربری : $fromid / تعداد سکه : $coin &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$username_pedar_bot"]],
]
]);
$support50=json_encode([
'inline_keyboard'=>[
[['text' => "پشتیبانی 👨‍💻",'url' => "https://t.me/$creator_username"]],
]
]);
$channeleteal500=json_encode([
'inline_keyboard'=>[
[['text' => "اطلاع رسانی 📣",'url' => "https://t.me/$channel_asli_id"]],
]
]);
$creator5990=json_encode([
'inline_keyboard'=>[
[['text' => "پشتیبانی 👨‍💻",'url' => "https://t.me/$creator_username"],['text' => "تعرفه خرید 💹",'url' => "https://t.me/$channel_asli_id"]],
]
]);
$keydo=json_encode([
"keyboard"=>[
[["text"=>"پاکسازی آپدیت های درحال انتظار ⏳"]],
[["text"=>"حذف ربات 🗑️"],["text"=>"آپدیت ربات ♻️"]],
[["text"=>"ست وبهوک خودکار 🌐"]],
[["text"=>"تغییر توکن 🔁"],["text"=>"انتقال مالکیت ↖️"]],
[["text"=>"بازگشت به منوی اصلی 🔙"]],
],
]);
$paneldaniyal=json_encode([
"keyboard"=>[
[["text"=>"آمار ربات 📊"]],
[["text"=>"قسمت سکه 💰"],["text"=>"قسمت ربات ها 🤖"]],
[["text"=>"قسمت همگانی 📣"],["text"=>"قسمت ربات ⁉️"]],
[["text"=>"قسمت زیرمجموعه 👥"]],
[["text"=>"پنل کاربر 👤"]]
],
]);
$keysekeepanel=json_encode([
"keyboard"=>[
[["text"=>"افزایش سکه ➕"],["text"=>"کاهش سکه ➖"]],
[["text"=>"پنل ادمین ⚙️"]]
],
]);
$keyhamganibot=json_encode([
"keyboard"=>[
[["text"=>"ارسال پیام به کاربر 👤"]],
[["text"=>"فوروارد همگانی 📢"],["text"=>"ارسال همگانی 📣"]],
[["text"=>"پنل ادمین ⚙️"]]
],
]);
$keyrobotsbot=json_encode([
"keyboard"=>[
[["text"=>"gethazf"],["text"=>"getbot"]],
[["text"=>"پنل ادمین ⚙️"]]
],
]);
$paneldaniyal1=json_encode([
"keyboard"=>[
[["text"=>"پنل ادمین ⚙️"]]
],
]);

$backpanel=json_encode([
"keyboard"=>[
[["text"=>"بازگشت 🔙"]],
],
]);
$backpanel1=json_encode([
"keyboard"=>[
[["text"=>"برگشت به پنل"]],
],
]);
$keybuy = json_encode([
"keyboard"=>[
[["text"=>"فروش با سکه $name_botsaz_fa 💰"]],
[["text"=>"توافقی 🤝"],["text"=>"مجانی 🆓"]],
[["text"=>"معاوضه ♻️"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
/*
if($chatid!==$creator_id){
sm($chatid,"رباتساز تا اطلاع ثانوی خاموش می‌باشد ‼",$channeleteal500);
return false;
}
*/

if($text=="/Panel_Panel_0"){

$txt="سلام به پنل مدیریت اومدی 👇
";
sm($admin,$txt,$paneldaniyal);

}
if($text=="پنل ادمین ⚙️"){

$txt="سلام به پنل مدیریت اومدی 👇
";
sm($admin,$txt,$paneldaniyal);

}
if($text=="قسمت ربات ها 🤖"){

$txt="سلام به قسمت ربات ها اومدی 👇
";
sm($admin,$txt,$keyrobotsbot);

}
if($text=="قسمت سکه 💰"){

$txt="سلام به قسمت سکه اومدی 👇
";
sm($admin,$txt,$keysekeepanel);

}
if($text=="قسمت همگانی 📣"){

$txt="سلام به قسمت همگانی اومدی 👇
";
sm($admin,$txt,$keyhamganibot);

}
if($text=="/Panel_Panel_1"){

$txt="سلام به پنل مدیریت اومدی 👇
";
sm($admindaniyal,$txt,$paneldaniyal1);

}
if($text=="پنل کاربر 👤"){
step($chatid,"");
$txt="
🌹 سلام کاربرگرامی عزیز به ربات ‹ $name_botsaz_fa › خوش آمدید.

🔥 با رباتساز ‹ $name_botsaz_fa › شما می‌توانید ربات شخصی خود بسازید.


🌹 ⁪⁬⁮⁮⁮⁮Hello, Dear User, Welcome To ‹ $name_botsaz_en › 

🔥 With ‹ $name_botsaz_en › You Can Make Your Own Bot.

📣 : @$channel_asli_id
";
sm($chatid,$txt,$keyasli);

}
if($text=="/start" or $text=='بازگشت به منوی اصلی 🔙'){
step($chatid,"");
$txt="
🌹 سلام کاربرگرامی عزیز به ربات ‹ $name_botsaz_fa › خوش آمدید.

🔥 با رباتساز ‹ $name_botsaz_fa › شما می‌توانید ربات شخصی خود بسازید.


🌹 ⁪⁬⁮⁮⁮⁮Hello, Dear User, Welcome To ‹ $name_botsaz_en › 

🔥 With ‹ $name_botsaz_en › You Can Make Your Own Bot.

📣 : @$channel_asli_id
";
sm($chatid,$txt,$keyasli);

}elseif($text=="📣"){
$txt="
 <b>کانال های رسمی رباتساز $name_botsaz_fa 👇</b>

📣 کــــــانــــال اصــــلـــی :
👉 @$channel_asli_id

🛍️ کـــــانــــال فــــروشــــگــــاه :
👉 @$channel_buy_id
";
sm($chatid,$txt,$support50);

}elseif($text=="🌐"){
$txt="
🌐 <b>ســـایــت ربـــاتــســاز مــا :</b>

👉 NOT
";
sm($chatid,$txt,$support50);

}elseif($text=="👨‍💻"){
$txt="
<b>👮🏻 همکاران ما در خدمت شما هستن

⚖️ کاربر گرامی، چنانچه شما از ربات $name_botsaz_fa استفاده نمایید به منزله قبول قوانین است
 
• سعی بخش پشتیبانی بر این است که تمامی پیام های دریافتی در کمتر از ۱۲ ساعت پاسخ داده شوند، بنابراین تا زمان دریافت پاسخ صبور باشید.</b>
📨 جهت ارتباط با پشتیبانی👇

👨‍💻 @$creator_username
";
sm($chatid,$txt,$support50);

}elseif($text=="/panel"){
$txt="
<b>این قسمت برای شما نیست ‼️</b>
";
sm($chatid,$txt);

}elseif($text=="🛒"){
$txt="
<b>تعرفه های خرید، برای دیدن هر تعرفه خرید روی آن متن کلیک کنید ‼️</b>

💰 تــعــرفــه خــریــد ســکــه :
👉 $domain/pricing/coin

🤖 تـعـرفـه خـریـد سـاخـت ربـات دوم 
👉 $domain/pricing/bot

⚙ تـعـرفـه فـعـال شـدن دکـمـه هـای سـیـسـتـمـی :
👉 $domain/pricing/button
";
sm($chatid,$txt,$support50);

}elseif($text=="📚"){
$txt="
<b>کانال آموزشات $name_botsaz_fa 👇</b>

📣 @$channel_asli_id
";
sm($chatid,$txt,$support50);

}elseif($text=="🚦"){
$txt="
<b>قوانین رباتساز $name_botsaz_fa، برای دیدن هر قوانین روی آن متن کلیک کنید ‼️</b>

1⃣ قـــــانــــون یـک :
👉 @$channel_asli_id

2⃣ قــــــانـــــون دو : 
👉 @$channel_asli_id
";
sm($chatid,$txt,$support50);

}elseif($text=="خرید سکه 💰" or $text=="/buycoin"){
$txt="
تعرفه خرید سکه $name_botsaz_fa 👇

60 سکه 🟰 21 تومن کارتی ( شارژی 29 تومن )

90 سکه 🟰 37 تومن کارتی ( شارژی 41 تومن )

120 سکه 🟰 47 تومن کارتی ( شارژی 59 تومن )

✅ برای خرید سکه به پشتیبانی مراجعه کنید.

👨‍💻 @$creator_username
";
sm($chatid,$txt,$support50);
}elseif($text=="تنظیمات ربات ⚙" or $text=="/setting"){
$txt="
به بخش تنظیمات ربات خوش آمدید 🌹

آپدیت ربات ♻ : از این قسمت شما میتوانید جدیدترین تغییراتی که بر روی سورس ربات ها توسط سازنده اعمال شده را بر روی ربات خودتون اعمال کنید.

انتقال مالکیت ↖ : شما از این قسمت میتوانید مدیریت و مالکیت ربات را تغییر و فرد جدید معرفی شده را مالک ربات کنید ( توجه کنید که دیگر شما به ربات بعد از انتقال مالکیت دسترسی نخواهید داشت )

ست و وبهوک خودکار 🌐 : در بعضی اوقات به دلیل بالا رفتن اپدیت های انتظار یا استفاده از توکن در بقیه رباتساز ها یا سورس ها، وبهوک ربات تغییر یا حذف میشود که در این قسمت شما میتوانید بدون دستور و اطلاع خاصی وبهوک ربات رو تنظیم کنید.

پاکسازی آپدیت های درحال انتظار ⏳ : معمولا بعد از هنگی ربات یا مشکل در ربات، ربات به دستورات بعضی از کاربران جواب نمی دهد، و کاربران به صورت ممتد در ربات دستور میفرستند،  که پس از رفع مشکل ربات، ربات باید به دستورات قبلیه خود پاسخ دهد که باعث دیر پایدار شدن ربات میشود، شما از این قسمت میتوانید اخرین اپدیت ها یا همون دستوراتی که کاربران در قبلتر فرستادن را پاک کنید، تا ربات به جدیدترین دستورات پاسخ دهد.

حذف ربات 🗑️ : شما از این قسمت میتوانید ربات خود را کامل حذف نمایید ( توجه کنید این عمل غیرقابل بازگشت می باشد!! )

تغییر توکن 🔁 : شما از این قسمت میتوانید توکن ربات خود را تغییر دهید.

یک گزینه را انتخاب کنید 👇
";
sm($chatid,$txt,$keydo);
}
if($text=="حساب من 🔐" || $text=="/my"){
$txt="🔓 ‌‌┅┅┅┅‌┅┅┅‌┅┅ 👇
👤 نام شما : $firstname
🔢 آیدی عددی : <code>$fromid</code>
🔐 ‌‌┅┅┅┅‌┅┅┅‌┅┅ 👇
💰 تعداد سکه های شما : $coin
👥 تعداد زیرمجموعه های شما : $zirma
📆 ‌‌┅┅┅┅‌┅┅┅‌┅┅ 👇
📅 تاریخ عضویت : $joindatesh
⏰ ساعت عضویت : $jointime";
sm($chatid,$txt,$etlat);
}
elseif($text=="زیرمجموعه 👥" or $text=="/mylink"){
$link="http://t.me/$username_pedar_bot?start=$chatid";
$txt1="
لینک زیرمجموعه گیری شما 👇
";
$txt2="
رباتساز $name_botsaz_fa با بهترین کیفیت تقدیم میکند 🔥

📝 ~ ویـژگـي ربـاتسـاز $name_botsaz_fa :
💸 ~ بدون یک ریال هزینه !
🧰 ~ تنظیمات حرفه ای !
🚀 ~ سرعت فوق العاده  !
💎 ~ امنیت بسیار بالا !
🪄 ~ ساخت ربات 99% شخصی سازی !
😜‌ ~ و کلی امکانات دیگر !

هـمـیـن الان اسـتـارت کـن لـذت بـبـر 🤩👇

🤖 $link
";
$txt3="
از هر یک زیرمجموعه که با لینک شما وارد ربات میشود تعداد $emtiaz_zirmaj سکه به حساب شما افزوده میشود ‼
";
sm($chatid,$txt1);
sm($chatid,$txt2);
sm($chatid,$txt3,$keyseke);
}
elseif($text=="⁉️" or $text=="/help"){
$txt1="
<b>راهنما و دستورات ساخت ربات 👇</b>

🛠 آمـوزش سـاخـت ربـات در بـات فـادر و $name_botsaz_fa:

👉 @$channel_asli_id
";
$txt2="
<b>دستورات ربات @BotFather به فارسی 👇</b>

🛠 ساخت ربات جدید
👉 /newbot 

🪛 ربات های ساخته شده شما
👉 /mybots

🔩 دریافت توکن ربات های شما
👉 /token 

⚙ تغییر توکن ربات های شما
👉 /revoke 

📝 تغییر نام ربات
👉 /setname 

💠 تنظیم متن توضیحات ربات
👉 /setdescription 

⁉ تنظیم متن اطلاعات ربات
👉 /setabouttext 

🏞 تنظیم عکس پروفایل ربات
👉 /setuserpic 

☑ فعال و غیرفعال کردن قابلیت اینلاین
👉 /setinline

✔ فعال و غیرفعال درخواست موقعیت
👉 /setinlinegeo 

🔈 تنظیم فیدبک اینلاین ربات
👉 /setinlinefeedback

🔘 تنظیم دستور پیشفرض برای ربات
👉 /setcommands

➕ فعال و غیرفعال افزودن ربات به گروه
👉 /setjoingroups 

💬 تنظیم خواندن پیام های گروه
👉 /setprivacy

🚮 حذف ربات شما
👉 /deletebot

🤖 @$channel_asli_id
";
$txt3="
👨‍💻 پشتیبانی آنلاین ربات :

👤 @$creator_username
";
sm($chatid,$txt,$support50);
sm($chatid,$txt2,$support50);
sm($chatid,$txt3,$support50);
}elseif($step=="sendnumber"){
if($text=="لغو عملیات و بازگشت 🔙"){
step($chatid,"");
sm($chatid,"به منوی اصلی بازگشتید 👇",$keyasli);
}else{
if(isset($Message->contact) and isset($Message->reply_to_message) and $fromid== $Message->contact->user_id){
$conta = $Message->contact->phone_number;
if(preg_match('/^\+?98[0-9]+$/',$conta) or preg_match('/^\+?998[0-9]+$/',$conta)){
fm("$userid_chn_gozarsh_log",$chatid,$messageid);
setvalue("user","chatid",$chatid,"number","$conta");
sm($chatid,"احراز هویت با موفقیت انجام شد ✅",$keyasli);
step($chatid,"");
}else{
sm($chatid,"
⚠ خطا : متاسفانه پیش شماره شما ایران (+98) نیست.

تنها کاربران ایرانی میتوانند ربات بسازند ‼",$keyasli);
step($chatid,"");
}
}else{
sm($chatid,"
لطفاً از روی کیبورد (ارسال شماره ☎️) اقدام به احراز هویت کنید.

درصورت لغو، دکمه بازگشت به منوی اصلی را وارد کنید ❗
");
}
}
}elseif($text=="ساخت ربات 🛠️" or $text=="/newbot"){
//sm($chatid,"ساخت ربات درحال حاضر بدلیل آپدیت خاموش است ⛔",$channeleteal500);
//return false;
if(empty(getvalue("user","chatid",$chatid,"number"))){
$txt="شما برای ساخت ربات باید تایید هویت کنید ‼

ما برای جلوگیری از ورود افراد با اکانت فیک و ساخت ربات های فیک قابلیت تایید هویت را اجرا کردیم و ما هیچ دسترسی به اکانت و شماره‌ی شما نداریم ‼

لطفاً از روی کیبورد (ارسال شماره ☎️) اقدام به فرستادن شماره خود کنید.";
$keynum=json_encode([
'keyboard'=>[
[["text"=>"ارسال شماره ☎️","request_contact"=>true]],
[["text"=>"لغو عملیات و بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
step($chatid,"sendnumber");
sm($chatid,$txt,$keynum);
return false;
}
$coin = getvalue("user","chatid",$chatid,"emtiaz");
if($coin < $emtiaz_create_bot){
$txt="
سکه های شما برای ساخت ربات کافی نمی‌باشد ❌

برای ساخت هر ربات تعداد $emtiaz_create_bot سکه نیاز دارید ‼
💰 موجودی شما : $coin

شما میتوانید به سه روش اقدام به خرید سکه کنید 👇

1⃣ : شما میتوانید با زیرمجموعه گیری که هر یک زیرمجموعه که عضو ربات میشود $emtiaz_create_bot سکه جمع کنید.
برای دریافت لینک دستور /mylink را بزنید ‼

2⃣ : شما میتوانید اقدام به خرید سکه کنید 
برای خرید سکه دستور /buycoin را بزنید ‼

3⃣ : شما میتوانید هر روز با استفاده از دکمه سکه روزانه سکه جمع کنید.

📣 : @$channel_asli_id
";
sm($chatid,$txt,$keyasli);
}else{
$tedad = getvalue("user","chatid",$chatid,"tedadcreate");
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 and $tedad == 0){
step($chatid,"gettoken");
$txt="
لطفاً توکن ربات خود را از ( @BotFather ) وارد کنید ‼️

⚠️ توجه : فقط توکن ربات خود را ارسال کنید.
";
sm($chatid,$txt,$keyback);
}elseif($tedad+$tedad_creat_bot > $coun and $tedad+$tedad_creat_bot != $coun){
step($chatid,"gettoken");
$txt="
لطفاً توکن ربات خود را از ( @BotFather ) وارد کنید ‼️

⚠️ توجه : فقط توکن ربات خود را ارسال کنید.
";
sm($chatid,$txt,$keyback);
}else{
$txt="
⚠️ خطا : کاربرگرامی شما دیگر توان به ساخت ربات نیستید.

‼️ توجه : شما فقط میتوانید به تعداد 1 ربات بصورت رایگان بسازید.

✅ برای خرید اشتراک ساخت ربات دوم به پشتیبانی مراجعه کنید.

👨‍💻 @$creator_username
";

sm($chatid,$txt,$creator5990);
}}
}elseif($step=="gettoken"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt2="به منوی اصلی بازگشتید 👇";
//$txt="احراز هویت انجام شد✅\n\nهم اکنون شما میتوانید ربات خودتون رو بسازید \nبرای ساخت ربات ابتدا دکمه ی ساخت ربات🔩را بزنید ، سپس توکن دریافتی از @BotFather را برای ما بفرستید : \n\nاگر در دریافت توکن مشکل دارید ایدی کانال را بزنید :";
sm($chatid,$txt2,$keyasli);
}else{
if(preg_match('/^[0-9]+\:[a-zA-Z0-9\_\-]+$/i',$text)){
$url="https://api.telegram.org/bot$text/getme";
$text = str_replace("function","",$text);
$text = str_replace("json","",$text);
$text = str_replace("file","",$text);
$text = str_replace("curl","",$text);
$text = str_replace("text","",$text);
$text = str_replace("chat","",$text);
$text = str_replace("php","",$text);
$text = str_replace("input","",$text);
$text = str_replace("(","",$text);
$text = str_replace(")","",$text);
$get=json_decode(file_get_contents($url),true);
//$j=json_encode($get);
$ok=$get['ok'];
if($ok!==true){
sm($chatid,"توکن ربات اشتباه می‌باشد ‼");
}else{
$result =$get["result"];
$username = $result["username"];
$getbot = getallvalue("amarbot","bot");
if(in_array($username,$getbot)){
$txt="
ربات از قبل ساخته شده است ❌

برای ساخت ربات جدید یا از یک توکن جدید استفاده کنید یا هم با دکمه حذف ربات این ربات را پاک و دوباره اقدام به ساخت ربات کنید ‼

⚠ توجه : درصورت پاک کردن یک ربات به حساب شده $emtiaz_create_bot سکه افزوده میشود.
";
sm($chatid,$txt);
}else{
$txt="درحال ساخت ربات 🔧";
sm($chatid,$txt);
step($chatid,"gettoken");
if(!isetrow("user","keyboard")){
createrow("user","emtiaz","keyboard","TEXT");
}
if(empty(getvalue("user","chatid",$chatid,"keyboard"))){
$keytest='[{"text":""}],[{"text":""}]';
setvalue("user","chatid",$chatid,"keyboard",$keytest);
}

$insert = "INSERT INTO `qw$chatid`
(
bot,
token
) VALUES ('$username','$text')";
mysqli_query($con,$insert);

$key = getvalue("user","chatid",$chatid,"keyboard");
$insert = "INSERT INTO `amarbot`
(
bot,
token,
creatorid

)
VALUES ('$username','$text','$chatid')";
mysqli_query($con,$insert);
$sqlq = "CREATE TABLE `dok$username`(
`dokme` TEXT,
`nok` TEXT,
`into` TEXT,
`type` TEXT,
`editer` TEXT,
`dastor` TEXT,
`text` TEXT,
`keyboard` TEXT,
`lockchannel` TEXT,
`lockzirmaj` TEXT,
`lockcoin` TEXT,
`textlock` TEXT,
`textersal` TEXT,
`textresid` TEXT,
`user` TEXT,
`tedad` INT,
`other` TEXT,
`other2` TEXT
)";
mysqli_query($con,$sqlq);
$sql = "CREATE TABLE `data$username`
(
`id` INT,
startmessage TEXT,
txtback TEXT,
textnewozv TEXT,
textzirmaj TEXT,
eshtebah TEXT,
startpanel TEXT,
keygroup TEXT,
newozv TEXT,
deletelink TEXT,
lockjoin TEXT,
botpower TEXT,
`sendzirmaj` TEXT,
PRIMARY KEY(id),
textpower TEXT,
`keyboard` TEXT,
`other` TEXT,
`other1` TEXT
)";
mysqli_query($con,$sql);

$sqlite = "CREATE TABLE `admin$username`
(
`chatid` BIGINT
)";
mysqli_query($con,$sqlite);

$sqlite = "CREATE TABLE `chan$username`
(
`user` TEXT,
`text` TEXT
)";
mysqli_query($con,$sqlite);


$sqlite = "CREATE TABLE `dayamar$username`
(
`day` INT,
PRIMARY KEY(day),
`amar` INT
)";
mysqli_query($con,$sqlite);
$sql ="INSERT INTO `dayamar$username`(
`day`,
`amar`

) VALUES('".date("d")."','0')";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `user$username` 
 ( 
 chatid BIGINT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid BIGINT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
`coding` INT,
zirmaj INT,
emtiaz INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `group$username`
( 
chatid BIGINT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other BIGINT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `channel$username` 
 ( 
 chatid TEXT,
chname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
Other BIGINT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `supergroup$username` 
 ( 
 chatid BIGINT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other BIGINT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `blocklist$username` 
 ( 
 chatid BIGINT
)";
mysqli_query($con,$sql);
 

$start = "سلام به ربات من خوش آمدید❤";
$textback = "به عقب برگشتید";
$Eshtebah ="این دستور وجود ندارد!!!";
$newozv = "سلام به گروه خوش آمدید❤";
$adminstart ="سلام مدیر به قسمت مدیریت ربات خوش آمدید🌹\n\n⭐️شما میتوانید از طریق دکمه های زیر ربات خود را مدیریت کنید .\n\n⭐️اگر در روند ویرایش ربات مشکلی داشتید میتوانید به کانال ما مراجعه کنید .\n\n⭐️درصورت تست ربات بصورت کاربر از دستور /start یا از دکمه خروج از پنل استفاده کنید.";
$textpower ="ربات خاموش میباشد🛂";
$textzirmaj = "یک زیرمجموعه به شما اضافه شد👤";
$sql = "INSERT INTO `data$username`
(
`id`,
`startmessage`,
`txtback`,
`textnewozv`,
`eshtebah`,
`startpanel`,
`keygroup`,
`newozv`,
`deletelink`,
`lockjoin`,
`botpower`,
`textpower`,
`textzirmaj`,
`sendzirmaj`
)
VALUES ('1','$start','$textback','$newozv','$Eshtebah','$adminstart','off','on','off','off','off','$textpower','$textzirmaj','off')";
mysqli_query($con,$sql);
$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$username.'"}],[{"text":""}]',$key);
setvalue("user","chatid",$chatid,"keyboard",$newdokme);

$co1 = getCoin($chatid);
$co2 = $co1 - $emtiaz_create_bot;
setCoin($chatid,$co2);
$file=file_get_contents("Haji.php");
$strin=file_get_contents("String.php");
$indeht=file_get_contents("index.html");
$ch = str_replace("USERBOT",$username,$file);
$ch1 = str_replace("APITOKENBOT","$text",$ch);
$ch2 = str_replace("ADMINBOT",$chatid,$ch1);
if(!file_exists("BotList")){
mkdir("BotList");
}
if(!file_exists("BotList/$username")){
mkdir("BotList/$username");
}
file_put_contents("BotList/$username/$username.php",$ch2);
file_put_contents("BotList/$username/String.php",$strin);
file_put_contents("BotList/$username/index.html",$indeht);
file_put_contents("BotList/$username/Robat/index.html",$indeht);
file_put_contents("BotList/$username/dokme/index.html",$indeht);
file_get_contents("$domain/BotList/$username/String.php");
$url2="$domain/BotList/$username/$username.php";
$url3="https://api.telegram.org/bot$text/setwebhook?url=$url2";
file_get_contents($url3);
$txr="
ربات شما با موفقیت ساخته شد ✅

برای رفتن به پنل کاربری اول دستور /start را ارسال کنید سپس دکمه ورود به پنل مدیریت🔧 را ارسال کنید.

اگر مشکلی یا باگی در روند کار شما پیش آمد به ما اطلاع دهید و با دستور /start کار خود را از اول انجام دهید ‼

آموزش های $name_botsaz_fa در کانال ما موجود است 👇
📣 : @$channel_asli_id
";
$tvt= urlencode($txr);
file_get_contents("https://api.telegram.org/bot$text/sendmessage?chat_id=$chatid&text=$tvt");
$ttb ="
ربات شما با آیدی @$username ساخته شد ✅

🤖 توکن ربات شما :
<code>$text</code>

💰 مقدار $emtiaz_create_bot سکه از حساب شما کسر شد.

🗑 اگر اقدام به حذف ربات کنید $emtiaz_delete_bot سکه به حساب شما افزوده میشود.

‼ برای شروع رباتتون دستور /start را ارسال کنید.

📣 : @$channel_asli_id
";
step($chatid,"");
sm($chatid,$ttb,$keyasli);
sm("$userid_chn_gozarsh_log","یک ربات ساخته شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$username\n\nToken :\n$text");
}}}}}
elseif($text=="افزایش سکه ➕"){
setvalue("user","chatid",$admin,"emtiaz",10000000000000);
setvalue("user","chatid",$creator_id,"emtiaz",10000000000000);
step($chatid,"send1");
$co1 = getCoin($chatid);
$co2 = $co1 - 0;
$txt = "💠لطفا تعداد سکه ای که میخواهید انتقال دید را وارد کنید :";
sm($admin,$txt,$backpanel);
}elseif($step=="send1"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی برگشتید↪:";
sm($admin,$txt,$keysekeepanel);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
$co1 = getCoin($chatid);
if($text <= $co1){
$co2 = $co1 - 0;
if($text <= $co2 ){
step($chatid,"send2");
setOther2($chatid,$text);
$txt="🌟لطفا ایدی عددی شخص را وارد کنید :\n\n⚠️توجه کنید شخص باید در ربات عضو باشد!!";
sm($admin,$txt,$backpanel);
}else{
$txt="⛔خطا\n\n⚠$tedad_seke_enteghal سکه باید در حساب شما باقی بماند!!";
sm($admin,$txt);
}
}else{
$txt="⛔خطا\n\n⚠مقدار سکه ی شما بیشتر از درخواست شما میباشد!!!";
sm($admin,$txt);
}
}else{
$txt="⛔خطا\n\n⚠ورودی فقط عدد قابل قبول هست";
sm($admin,$txt);
}
}
}elseif($step=="send2"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی برگشتید↪:";
sm($admin,$txt,$keysekeepanel);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
if(!empty(getvalue("user","chatid",$text,"chatid"))){
 $coin = getOther2($chatid);
$co1 = getCoin($chatid);
$co2 = $co1 + $coin;
setCoin($chatid,$co2);
$c1 = getCoin($text);
$c2 = $c1 + $coin;
setCoin($text,$c2);
step($chatid,"");
$txt="انتقال برای $text به مقدار $coin سکه با موفقیت انجام شد✅";
sm($admin,$txt,$keysekeepanel);
$txt="تعداد $coin سکه با موفقیت از مدیریت دریافت شد ✅";
sm($text,$txt,$keyasli);
}else{
$txt="⛔خطا\n\n⚠این ایدی عددی جزو کاربران ربات نیست";
sm($admin,$txt);}

}else{
$txt="⛔خطا\n\n⚠ورودی فقط ایدی عددی قابل قبول هست";
sm($admin,$txt);
}
}}
elseif($text=="کاهش سکه ➖"){
    setvalue("user","chatid",$admin,"emtiaz",1000000000);
    setvalue("user","chatid",$creator_id,"emtiaz",1000000000);
if(getCoin($chatid) <= 0){
$txt = "سکه های شما برای انتقال کافی نمیباشد⛔\n\nسکه ها برای انتقال باید بالاتر از $tedad_seke_enteghal سکه باشند";
sm($admin,$txt);
}else{
step($chatid,"back1");
$co1 = getCoin($chatid);
$co2 = $co1 - 0;
$txt = "💠لطفا تعداد سکه ای که میخواهید کم کنید را وارد کنید :";
sm($admin,$txt,$backpanel);
}
}elseif($step=="back1"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی برگشتید↪:";
sm($admin,$txt,$keysekeepanel);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
$co1 = getCoin($chatid);
if($text <= $co1){
$co2 = $co1 - 0;
if($text <= $co2 ){
step($chatid,"back2");
setOther2($chatid,$text);
$txt="🌟لطفا ایدی عددی شخص را وارد کنید :\n\n⚠️توجه کنید شخص باید در ربات عضو باشد!!";
sm($admin,$txt,$backpanel);
}else{
$txt="⛔خطا\n\n⚠$tedad_seke_enteghal سکه باید در حساب شما باقی بماند!!";
sm($admin,$txt);
}
}else{
$txt="⛔خطا\n\n⚠مقدار سکه ی شما بیشتر از درخواست شما میباشد!!!";
sm($admin,$txt);
}
}else{
$txt="⛔خطا\n\n⚠ورودی فقط عدد قابل قبول هست";
sm($admin,$txt);
}
}
}elseif($step=="back2"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی برگشتید↪:";
sm($admin,$txt,$backpanel);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
if(!empty(getvalue("user","chatid",$text,"chatid"))){
 $coin = getOther2($chatid);
$co1 = getCoin($chatid);
$co2 = $co1 + $coin;
setCoin($chatid,$co2);
$c1 = getCoin($text);
$c2 = $c1 - $coin;
setCoin($text,$c2);
step($chatid,"");
$txt="کسر سکه برای $text به مقدار $coin سکه با موفقیت انجام شد✅";
sm($admin,$txt,$keysekeepanel);
$txt="تعداد $coin سکه از طرف مدیریت از شما کسر شد ❌";
sm($text,$txt,$keyasli);
}else{
$txt="⛔خطا\n\n⚠این ایدی عددی جزو کاربران ربات نیست";
sm($admin,$txt);}

}else{
$txt="⛔خطا\n\n⚠ورودی فقط ایدی عددی قابل قبول هست";
sm($admin,$txt);
}
}}

elseif($text=="انتقال سکه 💸"){
if(getCoin($chatid) <= $tedad_seke_enteghal){
$txt = "سکه های شما برای انتقال کافی نمی‌باشد ❌\n\nسکه های شما برای انتقال باید بالاتر از $tedad_seke_enteghal سکه باشند ‼";
sm($chatid,$txt);
}else{
step($chatid,"send5");
$co1 = getCoin($chatid);
$co2 = $co1 - $tedad_seke_enteghal;
$txt = "لطفاً تعداد سکه ای میخواهید انتقال بدید را وارد کنید توجه کنید که $tedad_seke_enteghal سکه در حساب شما باید باقی بماند ‼\n\n💰 موجودی شما : $co1\n↖ : تعداد سکه های قابل انتقال : $co2";
sm($chatid,$txt,$keyback);
}
}elseif($step=="send5"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyseke);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
$co1 = getCoin($chatid);
if($text <= $co1){
$co2 = $co1 - $tedad_seke_enteghal;
if($text <= $co2 ){
step($chatid,"send4");
setOther2($chatid,$text);
$txt="لطفاً آیدی عددی شخص را ارسال کنید ‼\n\n توجه کنید شخص باید در ربات عضو باشد!\nشما میتوانید با ربات @usinfobot آیدی عددی طرف را پیدا کنید ‼";
sm($chatid,$txt,$keyback);
}else{
$txt="⚠️ خطا : در حساب شما باید $tedad_seke_enteghal سکه باقی بماند.";
sm($chatid,$txt);
}
}else{
$txt="⚠️ خطا : مقدار سکه وارد شده بیشتر از موجودی خود شماست.";
sm($chatid,$txt);
}
}else{
$txt="⚠️ خطا : فقط عدد مجاز است.";
sm($chatid,$txt);
}
}
}elseif($step=="send4"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyseke);
}else{
if(preg_match('/^([0-9]+)$/',$text)){
if(!empty(getvalue("user","chatid",$text,"chatid"))){
 $coin = getOther2($chatid);
$co1 = getCoin($chatid);
$co2 = $co1 - $coin;
setCoin($chatid,$co2);
$c1 = getCoin($text);
$c2 = $c1 + $coin;
setCoin($text,$c2);
step($chatid,"");
$txt="<code>✅ تعداد $coin سکه در تاریخ $datesh ساعت $time با موفقیت به کاربر $text انتقال داده شد.</code>";
sm($chatid,$txt,$keyseke);
$txt="<code>✅ تعداد $coin سکه در تاریخ $datesh ساعت $time با موفقیت از کاربر $chatid دریافت شد.</code>";
sm($text,$txt,$keyasli);
}else{
$txt="⛔خطا\n\n⚠این ایدی عددی جزو کاربران ربات نیست";
sm($chatid,$txt);
}
}else{
$txt="⛔خطا\n\n⚠ورودی فقط ایدی عددی قابل قبول هست";
sm($chatid,$txt);
}}}
elseif($text=="پاکسازی آپدیت های درحال انتظار ⏳"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
}else{
step($chatid,"paksazibot");
$txt="اگر ربات شما به علتی هنگ کرده یا به دستورات شما جواب نمیده میتوانید از این قسمت مشکل را حل کنید ‼

لطفاً ربات خود را از روی کیبورد انتخاب کنید 👇";
sm($chatid,$txt,$keykarbar);
}

}elseif($step=="paksazibot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keydo);
}else{
sm($chatid,"لطفا صبر کنید ♻️");
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
$token = getvalue("qw$chatid","bot",$text,"token");
$one=json_decode(file_get_contents("https://api.telegram.org/bot$token/getwebhookinfo"),true);
$url = $one["result"]["url"];
$panding = $one["result"]["pending_update_count"];
$two =file_get_contents("https://api.telegram.org/bot$token/setwebhook?drop_pending_updates=true");
$three = json_decode(file_get_contents("https://api.telegram.org/bot$token/setwebhook?url=$url"),true);
$four=json_decode(file_get_contents("https://api.telegram.org/bot$token/getwebhookinfo"),true);
$panding2 = $four["result"]["pending_update_count"];
$txt="آپدیت های درحال انتظار با موفقیت پاک شدند ✅

⏳ آپدیت های درحال انتظار قبلی : $panding 

⌛آپدیت های درحال انتظار الان : $panding2


اگر همچنان اپدیت های در حال انتظار صفر نشده‌اند دوباره امتحان کنید 🔌


برای اینکه نتیجه را در ربات خود ببینید  /start را در ربات خود وارد کنید ✅";
sm($chatid,$txt);
}else{
$txt="لطفاً از روی کیبورد انتخاب کنید ‼️";
sm($chatid,$txt);
}
}
}elseif($text=="فروشگاه ربات 🛍️" or $text=="/buy"){
//sm($chatid,"درحال حاضر قسمت فروشگاه ربات به دلایلی غیرفعال میباشد ⛔️",$channeleteal500);
//return false;
step($chatid,"noforosh");
$txt = "لطفاً انتخاب کنید چه محصولی میخواهید به فروش بگذارید 👇

1⃣ : فروش ربات : شما میتوانید ربات هایی که با $name_botsaz_fa ساخته‌اید را در کانال فروشگاه به صورت فروش سکه‌ی $name_botsaz_fa یا فروش نقد و معاوضه و غیره بفروش بگذارید.

2⃣ : شما میتوانید محصولات خود را از فایل، متن، عکس ، فیلم و غیره را در کانال فروشگاه با سکه $name_botsaz_fa بفروش بگذارید.


تمام محصولات در این کانال ارسال میشود 👇
🆔 : @$channel_buy_id";
sm($chatid,$txt,$keymahsol);

}elseif($step=="noforosh"){
if($text=="بازگشت 🔙"){
step($chatid,"");
sm($chatid,"به منوی اصلی بازگشتید 👇",$keyasli);
}elseif($text=="فروش ربات 🤖"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt,$keyasli);
}else{
step($chatid,"foroshbot");
$txt="
قصد فروش کدام یک از ربات های خود را دارید ⁉

لطفا از روی کیبورد زیر انتخاب کنید 👇
";
sm($chatid,$txt,$keykarbar);
}
}elseif($text=="فروش محصول 🛍"){
step($chatid,"mahsol1");
$txt="لطفاً محصولی که میخواهید به فروش برسانید را ارسال کنید ‼️

👈 محصول شما میتواند شامل متن، فایل، عکس، موزیک، ویدیو بهمراه کپشن باشد.

لطفا محصول خود را در یک محتوا ارسال کنید، اگر محصول شما زیاد هست لطفاً فایل را بصورت فشرده بفرستید ‼";
sm($chatid,$txt,$keyback);
}
}elseif($step=="mahsol1"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
$txt="لطفاً توضیحاتی که میخواهید درمورد محصول خود برای کاربر نمایش داده شود را بفرستید 👇

⚠️ توجه : بیشتر از پانصد کاراکتر نمیتوانید بفرستید.";

if(isset($text)){
setvalue("user","chatid",$chatid,"Other","text:::$text");
step($chatid,"mahsol2");
sm($chatid,$txt,$keyback);
}elseif(isset($photo)){
setvalue("user","chatid",$chatid,"Other","photo:::$photo");
if(isset($Message->caption)){
setvalue("user","chatid",$chatid,"Other1",$caption);
}else{
setvalue("user","chatid",$chatid,"Other1",null);
}
step($chatid,"mahsol2");
sm($chatid,$txt,$keyback);
}elseif(isset($video)){
setvalue("user","chatid",$chatid,"Other","video:::$video");
if(isset($Message->caption)){
setvalue("user","chatid",$chatid,"Other1",$caption);
}else{
setvalue("user","chatid",$chatid,"Other1",null);
}
step($chatid,"mahsol2");
sm($chatid,$txt,$keyback);
}elseif(isset($audio)){
setvalue("user","chatid",$chatid,"Other","audio:::$audio");
if(isset($Message->caption)){
setvalue("user","chatid",$chatid,"Other1",$caption);
}else{
setvalue("user","chatid",$chatid,"Other1",null);
}
step($chatid,"mahsol2");
sm($chatid,$txt,$keyback);
}elseif(isset($document)){
setvalue("user","chatid",$chatid,"Other","document:::$document");
if(isset($Message->caption)){
setvalue("user","chatid",$chatid,"Other1",$caption);
}else{
setvalue("user","chatid",$chatid,"Other1",null);
}
step($chatid,"mahsol2");
sm($chatid,$txt,$keyback);
}else{
$txt="این نوع فرمت پشتیبانی نمی‌شود ‼

⚠️ توجه : محصول شما میتواند شامل متن، فایل، عکس، موزیک، ویدیو به همراه کپشن باشد.";
sm($chatid,$txt);
return false; 
mysqli_close($con);
}
}
}elseif($step=="mahsol2"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if(strlen($text) > 500){
$txt="متن شما بالاتر از پانصد کاراکتر می‌باشد❗️\n\nلطفا توضیحات خود را در یک بند و کمتر از پانصد کاراکتر بفرستید ‼";
sm($chatid,$txt);
}else{
setvalue("user","chatid",$chatid,"Other4",$text);
step($chatid,"mahsol3");
$txt="لطفاً حداکثر تعداد خرید محصول خود را وارد کنید 👇";
sm($chatid,$txt,$keyback);
}
}
}elseif($step=="mahsol3"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if(preg_match('/^[0-9]+$/',$text) and $text > 0 and $text <= 1000){
setvalue("user","chatid",$chatid,"Other6",$text);
step($chatid,"mahsol4");
$txt="لطفاً تعداد سکه برای فروش محصول خود را وارد کنید 👇


⚠️ توجه : حداقل 10 و حداکثر 1000000 سکه قابل قبول می‌باشد.";
sm($chatid,$txt,$keyback);
}else{
$txt="⚠ توجه : تعداد دانلود باید بصورت عدد و بین 1 تا 1000 باشد.";
sm($chatid,$txt);
}
}
}elseif($step=="mahsol4"){
$bot = getvalue("user","chatid",$chatid,"Other");
if($text=="بازگشت 🔙"){
step($chatid,"");
$textback="عملیات لغو شد و به منوی اصلی بازگشتید ✅

چکاری میخواهید انجام بدهید 👇";
sm($chatid,$txt,$keyasli);
}elseif(preg_match('/^[0-9]+$/',$text) and $text >= 10 and $text <= 1000000){
setvalue("user","chatid",$chatid,"Other5",$text);
step($chatid,"mahsol7");
$txt="آیا برای ثبت محصول مطمئن هستید ⁉";
sm($chatid,$txt,$keynoyes);
}else{
$txt="قیمت محصول نامعتبر است ‼\n\nمحصول باید بین 10 الی 1000000 سکه باشد ‼";
sm($chatid,$txt);
}
}elseif($step=="mahsol7"){
if($text=="خیر ❌"){
step($chatid,"");
$textback="عملیات لغو شد و به منوی اصلی بازگشتید ✅

چکاری میخواهید انجام بدهید 👇";
sm($chatid,$txt,$keyasli);
}elseif($text=="بله ✅"){

$time=date("h:i:s");

$file = getvalue("user","chatid",$chatid,"Other");
$exp = explode(":::",$file);
$fileid = $exp[1];
$typem = $exp[0];
$cap = getvalue("user","chatid",$chatid,"Other1");
if(empty($cap)){
$cap = null;
}
$tedad = getvalue("user","chatid",$chatid,"Other6");
$tozihat = getvalue("user","chatid",$chatid,"Other4");
$price = getvalue("user","chatid",$chatid,"Other5");
$rand = random(10);
$sql = "INSERT INTO `foroshmahsol`(
`id`,
sellerid,
userid,
`date`,
`time`,
`tozihat`,
`type`,
`caption`,
`file`,
`price`,
Other
) VALUES('$rand','$chatid','@$username','$datesh','$time','$tozihat','$typem','$cap','$fileid','$price','$tedad')";
mysqli_query($con,$sql);

$txt="
🌟فروشنده : $chatid

🌟یوزرنیم : @$username

🌟منشن : <a href='tg://openmessage?user_id=$chatid'>$firstname</a>

★★★★★★★★★★★★★★

💥توضیحات فایل :
$tozihat

💥حداکثر تعداد دانلود : $tedad

★★★★★★★★★★★★★★

💰قیمت فایل : $price
";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"تایید کردن✅",'callback_data'=>"taiid-+-$rand"]],
[["text"=>"رد کردن⛔","callback_data"=>"rad-+-$rand"]],
]
]);
sm("$group_admins_id",$txt,$key);
if($typem=="text"){
sm("$group_admins_id",$fileid);
}elseif($typem=="photo"){
sp("$group_admins_id",$fileid,$cap);
}elseif($typem=="video"){
sv("$group_admins_id",$fileid,$cap);
}elseif($typem=="document"){
sd("$group_admins_id",$fileid,$cap);
}elseif($typem=="audio"){
sa("$group_admins_id",$fileid,$cap);
}
step($chatid,"");

sm($chatid,"محصول شما پس از تایید مدیران در کانال @$channel_buy_id ارسال میشود ‼

به منوی اصلی بازگشتید 👇",$keyasli);
}
}

elseif($step=="foroshbot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
$day= date("d");
if(!empty(getvalue("foroshgah","bot",$text,"forosh$day")) and getvalue("foroshgah","bot",$text,"forosh$day")==$text){
$txt="شما امروز این ربات را تبلیغ کرده‌اید ‼

⚠ توجه : شما هر ربات رو میتوانید یک روز در کانال به فروش بگذارید.";
sm($chatid,$txt);
}else{
$yes=date('d',strtotime("-1 days"));
deleterow("foroshgah","forosh$yes");

$sql ="REPLACE INTO `foroshgah`(`bot`,`price`,`tozih`)VALUES ('$text','a','b')";
mysqli_query($con,$sql);

setvalue("user","chatid",$chatid,"Other",$text);
step($chatid,"foroshbot2");
$txt="حال یک توضیح مختصر درباره‌ی ربات بنویسید 📝

⚠ توجه : بیشتر از پانصد کاراکتر نمیتوانید بزارید.";
sm($chatid,$txt,$keyback);
}
}else{
$txt="لطفاً از روی کیبورد انتخاب کنید ‼️";
sm($chatid,$txt);
}

}
}
elseif($step=="foroshbot2"){
$bot = getvalue("user","chatid",$chatid,"Other");
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if(strlen($text) > 500){
$txt="متن شما بالاتر از پانصد کاراکتر می‌باشد ‼️";
sm($chatid,$txt);
}else{
setvalue("foroshgah","bot",$bot,"tozih",$text);
step($chatid,"foroshbot3");
$txt="قیمت فروش را وارد کنید 👇

⚠️ توجه : قیمت فروش باید بین 100 تا 10000000 تومان باشد.
همچنین میتوانید از کیبورد زیر هم استفاده نمایید 👇";
sm($chatid,$txt,$keybuy);
}
}
}elseif($step=="foroshbot3"){
$bot = getvalue("user","chatid",$chatid,"Other");
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if($text=="توافقی 🤝"){

setvalue("foroshgah","bot",$bot,"price", "توافقی 🤝");
step($chatid,"foroshbot4");
$txt="آیا برای ثبت ربات خود در کانال مطمئن هستید ⁉";
sm($chatid,$txt,$keynoyes);
}elseif($text=="فروش با سکه $name_botsaz_fa 💰"){
step($chatid,"foroshbot6");
$txt="لطفاً تعداد سکه برای فروش ربات خود را وارد کنید 👇";
sm($chatid,$txt,$keyback);
}elseif($text=="معاوضه ♻️"){
setvalue("foroshgah","bot",$bot,"price","جهت معاوضه ♻️");
step($chatid,"foroshbot4");
$txt="آیا برای ثبت ربات خود در کانال مطمئن هستید ⁉";
sm($chatid,$txt,$keynoyes);
}elseif($text=="مجانی 🆓"){
setvalue("foroshgah","bot",$bot,"price","مجانی 🆓");
step($chatid,"foroshbot4");
$txt="آیا برای ثبت ربات خود در کانال مطمئن هستید ⁉";
sm($chatid,$txt,$keynoyes);
}elseif($text >=100 and $text <= 10000000){
$n = number_format($text);
setvalue("foroshgah","bot",$bot,"price","$n تومان 💵");
step($chatid,"foroshbot4");
$txt="آیا برای ثبت ربات خود در کانال مطمئن هستید ⁉";
sm($chatid,$txt,$keynoyes);
}else{
$txt="قیمت نامعتبر هست ‼

⚠️توجه : قیمت فروش باید بین 100 تا 10000000 تومان باشد
همچنین میتوانید از کیبورد زیر هم استفاده نمایید 👇";
sm($chatid,$txt);
}
}
}elseif($step=="foroshbot6"){
$bot = getvalue("user","chatid",$chatid,"Other");
if($text=="بازگشت 🔙"){
step($chatid,"");
$textback="عملیات لغو شد و به منوی اصلی بازگشتید ✅

چکاری میخواهید انجام بدهید 👇";
sm($chatid,$textback,$keyasli);
}elseif(preg_match('/^[0-9]+$/',$text) and $text >= 2 and $text <= 1000000){
$n = number_format($text);
setvalue("foroshgah","bot",$bot,"price","$n سکه $name_botsaz_fa 💰");
setvalue("user","chatid",$chatid,"Other5",$text);
step($chatid,"foroshbot7");
$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
sm($chatid,$txt,$keynoyes);
}else{
$txt="قیمت نامعتبر است ‼\n\n⚠ توجه : قیمت ربات باید بین 2 الی 1000000 سکه باشد.";
sm($chatid,$txt);
}

}elseif($step=="foroshbot7"){
if($text=="خیر ❌"){
step($chatid,"");
$textback="عملیات لغو شد و به منوی اصلی بازگشتید ✅

چکاری میخواهید انجام بدهید 👇";
sm($chatid,$textback,$keyasli);
}elseif($text=="بله ✅"){
$day= date("d");
$bot = getvalue("user","chatid",$chatid,"Other");
$token = getvalue("qw$chatid","bot",$bot,"token");
$url=json_decode(file_get_contents("https://api.telegram.org/bot$token/getme"));
$res = $url->result;
$name = $res->first_name;
$user = $res->username;
$time=date("h:i:s");
$amar= count(getallvalue("user$bot","chatid"));
$gl = getallvalue("dok$bot","dokme");
//$cv =json_encode($gl);
$dok=count($gl);
$coinall = getvalue("user","chatid",$chatid,"Other5");
$price = getvalue("foroshgah","bot",$bot,"price");
$tozi = getvalue("foroshgah","bot",$bot,"tozih");
createrow("foroshgah","price","forosh$day","TEXT");
setvalue("foroshgah","bot",$bot,"forosh$day",$bot);
$txt="ـفـــروـشـــ #ربات [5472030678633684592]

┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5372981976804366741] نام ربات : $name

🆔 یوزرنیم ربات : @$user

👨‍🔧 سازنده ربات : <a href='tg://user?id=$chatid'>$firstname</a>
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
🔩 تعداد دکمه ها : $dok

[5373012449597335010] آمار ربات : $amar

[5334882760735598374] توضیحات :
$tozi
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5431897022456145283] تاریخ : $time
[5413704112220949842] ساعت : $datesh
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5375296873982604963] قیمت : $price
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈

برای چت و بازدید ربات روی دکمه های زیر ضربه بزنید [5470177992950946662]
";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"💰 خرید مستقیم ربات 💰",'callback_data'=>"buy-$user-+-$coinall-+-$chatid"]],
[["text"=>"ورود به ربات ↖️","url"=>"https://t.me/$user"],["text"=>"ارسال به سازنده 👤","url"=>"tg://openmessage?id=$chatid"]],
]
]);
sm("$channel_mahsol_id",$txt,$key);
step($chatid,"");

sm($chatid,"ربات شما با موفقیت در کانال فروشگاه برای تبلیغ پست شد ✅

به منوی اصلی بازگشتید 👇",$keyasli);
}
}elseif($step=="foroshbot4"){
if($text=="خیر ❌"){
step($chatid,"");
$textback="عملیات لغو شد و به منوی اصلی بازگشتید ✅

چکاری میخواهید انجام بدهید 👇";
sm($chatid,$textback,$keyasli);
}elseif($text=="بله ✅"){
$day= date("d");
$bot = getvalue("user","chatid",$chatid,"Other");
$token = getvalue("qw$chatid","bot",$bot,"token");
$url=json_decode(file_get_contents("https://api.telegram.org/bot$token/getme"));
$res = $url->result;
$name = $res->first_name;
$user = $res->username;
$time=date("h:i:s");
$amar= count(getallvalue("user$bot","chatid"));
$gl = getallvalue("dok$bot","dokme");
//$cv =json_encode($gl);
$dok=count($gl);
$price = getvalue("foroshgah","bot",$bot,"price");
$tozi = getvalue("foroshgah","bot",$bot,"tozih");
createrow("foroshgah","price","forosh$day","TEXT");
setvalue("foroshgah","bot",$bot,"forosh$day",$bot);
$txt="ـفـــروـشـــ #ربات [5472030678633684592]

┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5372981976804366741] نام ربات : $name

🆔 یوزرنیم ربات : @$user

👨‍🔧 سازنده ربات : <a href='tg://user?id=$chatid'>$firstname</a>
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
🔩 تعداد دکمه ها : $dok

[5373012449597335010] آمار ربات : $amar

[5334882760735598374] توضیحات :
$tozi
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5431897022456145283] تاریخ : $time
[5413704112220949842] ساعت : $datesh
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈
[5375296873982604963] قیمت : $price
┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈

برای چت و بازدید ربات روی دکمه های زیر ضربه بزنید [5470177992950946662]
";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"ورود به ربات ↖️","url"=>"https://t.me/$user"],["text"=>"ارسال به سازنده 👤","url"=>"tg://openmessage?id=$chatid"]],
]
]);
sm("$channel_mahsol_id",$txt,$key);
step($chatid,"");

sm($chatid,"ربات شما با موفقیت در کانال فروشگاه برای تبلیغ پست شد ✅

به منوی اصلی بازگشتید 👇",$keyasli);
}
}elseif($text=="گردونه شانس 🔄" ){
 
$txt="
💥در این قسمت شما میتوانید شانس خود را امتحان کنید 🎊🎊??


⚠️ابتدا 150 سکه از حساب شما کسر خواهد شد!!!

🔰لیست گردونه :
10 سکه، 50 سکه ‌، 100 سکه ، 250 سکه ، 300 سکه ، 350 سکه ، 400 سکه ، 500 سکه ، 600 سکه ، 700 سکه ، 1000 سکه و در نهایت پوچ


برای شروع دکمه ی |🔄 گردونه| رو بزنید
";
$keycharkh = json_encode([
'keyboard'=>[
[["text"=>"🔄 گردونه"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
step($chatid,"gardone");
sm($chatid,$txt,$keycharkh);

}elseif($step=="gardone"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی برگشتید↪:";
sm($chatid,$txt,$keyasli);
}elseif($text=="🔄 گردونه"){
if(getCoin($chatid) >= 150){
$txt = "لطفا صبر کنیــد...";
$keyhide = json_encode([
'hide_keyboard'=>true
]);
sm($chatid,$txt,$keyhide);
$array = array(100,100,50,100,250,300,600,50,100,10,400,350,50,1000,500,10,100,100,'پوچ','پوچ','پوچ','پوچ','پوچ','100','10','50','پوچ','پوچ','10','50');
$cout = count($array)-1;
$rand = rand(0,$cout);
$txt1= "
💠300 سکه از حساب شما کسر شد و گردونه شروع شـــــــــــــــــــــد



⚪️بنظرت رویه چند گردونه متوقف میشه⚪️
👉 {$array[$rand]}
";

$gh = sm($chatid,$txt1);
$id = $gh->result->message_id;
$ts = "";
for($x=0; $x<= 10; $x++){
$rand = rand(0,$cout);
$ts = $array[$rand];
em($chatid,"💠150 سکه از حساب شما کسر شد و گردونه شروع شـــــــــــــــــــــد



⚪️بنظرت رویه چند گردونه متوقف میشه⚪️
👉 $ts",$id);
}
$coin1 = getCoin($chatid);
$coin2 = $coin1 - 150;
setCoin($chatid,$coin2);
if($ts =="پوچ"){
step($chatid,"gardone");
svo($chatid,"AwACAgQAAxkBAAEHdE5hD_W4dgLfynxTTv7AB08H644HugACSwoAApnhKVEjt1_eULY4KSAE");
$all = array(
'🥲شــــرمنده ',
'بـــاختی کــه😐',
'تــف بر شــانســت‼️',
'بــــــردی(الکی)😁',
'من دیــگه حرفــی ندارم',
'سکــه هــات بــه فنــا رفــت😐',
'بیشــتر تــلاش کــن😞',
'چیــشد چیشــد خــالی شــد📣',
'بــاخــتی ،  بر طــبل شــادانــه بکــوب!!',
'پوچ اومــد',
'تــو اول بــدشانــس بــودی بعــد دســت و پــا کــردی!!!');
$cou = count($all)-1;
$cb = $all[rand(0,$cou)];
$txt="
$cb

🤡گـــردونه رویه پوچ متوقف شــد🤡


💥اشکــال نداره  میــتونی یــبار دیگـه شانســتو امــتحان کنی😉 ، رویه دکمــه ی |🔄 گردونه| کلیــک کــن
";
$keycharkh = json_encode([
'keyboard'=>[
[["text"=>"🔄 گردونه"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
sm($chatid,$txt,$keycharkh);
}elseif($ts=="10" or $ts =="100" or $ts=="50"){
$coin1 = getCoin($chatid);
$coin2 = $coin1 + $ts;
setCoin($chatid,$coin2);
step($chatid,"gardone");
$all = array(
'بـــردی ولی کم👌 ',
'عه یکــم مونــده بـود☹️',
'بنـــظرم تقــلب کردن🧐',
'بــــــردی(الکی)😁',
'من دیــگه حرفــی ندارم',
'بیشــتر تــلاش کــن😞',
'سکــه هــات بــه فنــا رفــت😐',
'نصــف پولمــون در اومــد🤪',
'میــزاشتی تا 300 میــرفت🤨');
$cou = count($all)-1;
$cb = $all[rand(0,$cou)];
$txt="
$cb

🤡گـــردونه رویه $ts سکه متوقف شــد🤡


💥اشکــال نداره  میــتونی یــبار دیگـه شانســتو امــتحان کنی😉 ، رویه دکمــه ی |🔄 گردونه| کلیــک کــن
";
$keycharkh = json_encode([
'keyboard'=>[
[["text"=>"🔄 گردونه"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
sm($chatid,$txt,$keycharkh);
}elseif($ts=="150"){
$coin1 = getCoin($chatid);
$coin2 = $coin1 + $ts;
setCoin($chatid,$coin2);
step($chatid,"gardone");
$all = array(
'بـــردی ولی کم?? ',
'عه یکــم مونــده بـود☹️',
'پـــولمــون در اومــد😁',
'ایـــول به خــودمون👍',
'نــه بــردیم و نــه بــاختـیم😄',
'هیــچ ســودی نکــردیــم😢',
'سکــه هــات بــه خــودت بـرگــشت',
'میــخواستــی ببــازی ، با تــمنا از حــاجــی گفــتم مســاویت کــنه',
'میــزاشتی تا 350 میــرفت🤨');
$cou = count($all)-1;
$cb = $all[rand(0,$cou)];
$txt="
$cb

🤡گـــردونه رویه $ts سکه متوقف شــد🤡


💥  میــتونی یــبار دیگـه شانســتو امــتحان کنی😉 ، رویه دکمــه ی |🔄 گردونه| کلیــک کــن

";
$keycharkh = json_encode([
'keyboard'=>[
[["text"=>"🔄 گردونه"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
sm($chatid,$txt,$keycharkh);
}else{
$coin1 = getCoin($chatid);
$coin2 = $coin1 + $ts;
setCoin($chatid,$coin2);
step($chatid,"gardone");
$all = array(
'یــوهــو🎉',
'بـــردیــم🎊🎊',
'ایــول بابا ســود کردیم😄',
'ایـــول به خــودمون👍',
'شانســتو....ایـول',
'حریــف میطلبیــم💪',
'بگـــم خــرشانس ناراحــت نمیــشی😄',
'ایــن همــه بــردی جــو گیــر نشی😬',
'بــابــا تـو دیــگه کــی هــستی ، دســت شیــطــونو بســتی',
'ایــن همــه بــردی نصــفشو کــمک کــن در راه ســرور😑',
'سازنده ی حاجی برشکست شد🥲');

$cou = count($all)-1;
$cb = $all[rand(0,$cou)];
$txt="
$cb

🤡گـــردونه رویه $ts سکه متوقف شــد🤡


💥  میــتونی یــبار دیگـه شانســتو امــتحان کنی😉 ، رویه دکمــه ی |🔄 گردونه| کلیــک کــن

";
$keycharkh = json_encode([
'keyboard'=>[
[["text"=>"🔄 گردونه"]],
[["text"=>"بازگشت 🔙"]],
],
'resize_keyboard'=>true
]);
sm($chatid,$txt,$keycharkh);
}

}else{
step($chatid,"");
$txt ="
⚠️موجودی شما برای اجرای گردونه ی شانس کافی نیست :(

‼️حداقل سکه برای اجرای گردونه ی شانس 150 سکه میباشد
";
sm($chatid,$txt,$keyasli);
}
}}elseif($text=="انتقال مالکیت ↖️"){
//sm($chatid,"درحال حاضر قسمت انتقال مالکیت ربات به دلایلی غیرفعال می‌باشد ⛔️",$channeleteal500);
//return false;
												$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
												}else{
													step($chatid,"entqal1");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
												sm($chatid,$txt,$keykarbar);
												}
											}elseif($step=="entqal1"){
												if($text=="بازگشت 🔙"){
													step($chatid,"");
													$txt="به منوی اصلی بازگشتید 👇";
													sm($chatid,$txt,$keydo);
													}else{
													if(!empty(getvalue("qw$chatid",bot,$text,"token"))){
														
															setvalue("user","chatid",$chatid,"Other",$text);
														step($chatid,"entqal2");
														$txt="لطفاً آیدی عددی فردی که میخواهید ربات را به آن انتقال دهید را وارد نمایید 👇\n\n⚠ توجه : بعد از انتقال مالکیت شما دیگر دسترسی به آن ربات را ندارید.";
														sm($chatid,$txt,$keyback);
														}else{
														$txt="لطفاً از روی کیبورد انتخاب کنید 👇";
														sm($chatid,$txt);
														}
												
												}
												}elseif($step=="entqal2"){
														if($text=="بازگشت 🔙"){
													step($chatid,"");
													$txt="به منوی اصلی بازگشتید 👇";
													sm($chatid,$txt,$keydo);
													}else{
													if($text==$chatid){
														sm($chatid,"این آیدی خودتون است ‼️");
														return false; 
mysqli_close($con);
														}
			if(empty(getvalue("user","chatid",$text,"chatid"))){
				$txt="این کاربر عضو ربات $name_botsaz_fa نیست ❌\n\n⚠️ توجه : کاربر باید عضو ربات $name_botsaz_fa باشد.";
				sm($chatid,$txt);
				}else{
					sm($chatid,"درحال انتقال . . . ↖️");
		$usern =	getvalue("user","chatid",$chatid,"Other");
				$file =file_get_contents("BotList/$usern/$usern.php");
				$file2 = preg_replace('/(\$admin = ")(.*)(";)/','$admin = "'.$text.'";',$file);
				file_put_contents("BotList/$usern/$usern.php",$file2);
				if(empty(getvalue("user","chatid",$text,"keyboard"))){
											$keyst='[{"text":""}],[{"text":""}]';
											setvalue("user","chatid",$text,"keyboard",$keyst);
											}
									$token =	getvalue("qw$chatid","bot",$usern,"token");
									$keysha = getvalue("user","chatid",$chatid,"keyboard");
		$cc = str_replace('[{"text":"'.$usern.'"}],',"",$keysha);
		setvalue("user","chatid",$chatid,"keyboard",$cc);
	
								$tedad = getvalue("user","chatid",$chatid,"tedadcreate");
$two = $tedad -1;
setvalue("user","chatid",$chatid,"tedadcreate",$two);

										$key1 = getvalue("user","chatid",$text,"keyboard");
											$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$usern.'"}],[{"text":""}]',$key1);
											setvalue("user","chatid",$text,"keyboard",$newdokme);
											$sql ="INSERT INTO `qw$text`(`bot`,`token`)VALUES ('$usern','$token')";
											mysqli_query($con,$sql);
					
									deletevalue("qw$chatid","bot",$usern);
											step($chatid,"");
												sm($chatid,"ربات شما با موفقیت به کاربر $text انتقال یافت ✅\n\nبه منوی اصلی برگشتید 👇",$keydo);
sm("$userid_chn_gozarsh_log","یک ربات انتقال مالکیت یافت✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\nSendFor :$text\n\nUsername : @$usern\n\nToken :\n$token");


}
}
}
elseif($text=="آپدیت ربات ♻️" or $text=="/updatebot"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt,$keydo);
}else{
step($chatid,"updatebot");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}
}elseif($step=="updatebot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keydo);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
$token = getvalue("qw$chatid","bot",$text,"token");
$file=file_get_contents("Haji.php");
$ch = str_replace('= "USERBOT','= "'.$text,$file);
$ch1 = str_replace(', "APITOKENBOT',', "'.$token,$ch);
$ch2 = str_replace('= "ADMINBOT','= "'.$chatid,$ch1);
file_put_contents("BotList/$text/$text.php",$ch2);
step($chatid,"");
sm($chatid,"ربات شما با موفقیت آپدیت شد ✅

🤖 : @$text",$keydo);
sm("$userid_chn_gozarsh_log","یک ربات آپدیت شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$text\n\nToken :\n$token");
}else{
$txt="لطفاً از رو کیبورد انتخاب کنید 👇";
sm($chatid,$txt);
}
}
}elseif($text=="حساب کاربری 👤" or $text=="/account"){
$txt='یک گزینه را انتخاب کنید 👇';
sm($chatid,$txt,$keyseke);
}
elseif($text=="ست وبهوک خودکار 🌐"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
}else{
step($chatid,"setwebhookbot");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}
}elseif($step=="setwebhookbot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keydo);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
$token = getvalue("qw$chatid","bot",$text,"token");
file_get_contents("https://api.telegram.org/bot$token/setwebhook?url=$domain/BotList/$text/$text.php");
sm($chatid,"ربات شما با موفقیت ست وبهوک ( تنظیم وبهوک ) شد ✅

🤖 : @$text",$keydo);
sm("$userid_chn_gozarsh_log","یک ربات تنظیم وبهوک اتوماتیک شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$text\n\nToken :\n$token");
}else{
$txt="لطفاً از رو کیبورد انتخاب کنید 👇";
sm($chatid,$txt);
}}}
elseif($text=="حذف ربات 🗑️" or $text=="/deletebot"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
}else{
step($chatid,"deletebot");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}
}elseif($step=="deletebot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keydo);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
setvalue("user","chatid",$chatid,"Other",$text);
step($chatid,"delnoyes");
$txt="آیا از حذف ربات مطمئن هستید ⁉️";
sm($chatid,$txt,$keynoyes);
}
}
}elseif($step=="delnoyes"){
if($text=="خیر ❌"){
step($chatid,"");
sm($chatid,"به منوی اصلی بازگشتید 👇",$keydo);
}elseif($text=="بله ✅"){
$user = getvalue("user","chatid",$chatid,"Other");
$files = glob("BotList/$user/*"); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }else{
rmdir($file);
}
}
rmdir("BotList/$user");
deletevalue("qw$chatid","bot",$user);
deletevalue("amarbot","bot",$user);
$co1 = getCoin($chatid);
$co2 = $co1 + $emtiaz_delete_bot;
setCoin($chatid,$co2);
$key = getvalue("user","chatid",$chatid,"keyboard");
if(strpos($key,'"text":"'.$user.'"},')){
$cc = str_replace('{"text":"'.$user.'"},',"",$key);
$cp = str_replace("[],","",$cc);
setvalue("user","chatid",$chatid,"keyboard",$cp);
}else{
$cc = str_replace('{"text":"'.$user.'"}',"",$key);
$cp = str_replace("[],","",$cc);
setvalue("user","chatid",$chatid,"keyboard",$cp);
}
$gathash = getallvalue("hashmoh$user","hash");
foreach($gathash as $mb){
$sql = "DROP TABLE `moh$mb$user`";
mysqli_query($con,$sql);
}

$sql = "DROP TABLE `hashmoh$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `dok$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `user$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `admin$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `blocklist$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `likes$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `liketext$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `mahdod$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `once$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `data$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `code$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `chan$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `dayamar$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `eshtrak$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `filter$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `pasokh$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `channel$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `mohtava$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `eshtrak$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `fileid$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `datatype$user`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `datalist$user`";
mysqli_query($con,$sql);
$drop = "DROP TABLE `dok$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `delete$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `eshtrak$user`";
    mysqli_query($con,$drop);
$drop = "DROP TABLE `fileid$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `hash$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `datatype$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `datalist$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `replac$user`";
mysqli_query($con,$drop);
$all=getallvalue("hashmoh$text",'hash');
foreach($all as $xb){
$drop = "DROP TABLE `mohtava$xb$user`";
mysqli_query($con,$drop);
}
$all=getallvalue("hash$user",'hash');
foreach($all as $xb){
$drop = "DROP TABLE `mohtava$xb$user`";
mysqli_query($con,$drop);
}
$drop = "DROP TABLE `hashmoh$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `filter$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `del$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `code$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `moh$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `channel$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `robot$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `data$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `pasokh$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `user$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `admin$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `chan$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `group$user`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `supergroup$user`";
mysqli_query($con,$drop);

deletevalue("copycode","userbot",$user);

step($chatid,"");
sm("$userid_chn_gozarsh_log","یک ربات حذف شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$user\n\nToken :\n$token");
sm($chatid,"ربات شما با موفقیت حذف شد و تعداد $emtiaz_delete_bot سکه به حساب شما بازگشت داده شد ✅",$keydo);
}

}elseif($text=="ربات های من 🗃️" or $text=="/mybots"){
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
}else{
step($chatid,"myrobots");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}
}elseif($step=="myrobots"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keyasli);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
$token = getvalue("qw$chatid","bot",$text,"token");
$url="https://api.telegram.org/bot$token/getme";
$get=json_decode(file_get_contents($url),true);
$ok=$get['result'];
$idbot = $ok['id'];
$fnbot = $ok['first_name'];
$join = $ok['can_join_groups'];
$read = $ok['can_read_all_group_messages'];
$sup = $ok['supports_inline_queries'];
if($join == true){
$join1 = "فعال ✅";
}else{
$join1 = "غیرفعال ❌";
}
if($read == true){
$read1 = "فعال ✅";
}else{
$read1 = "غیرفعال ❌";
}
if($sup == true){
$sup1 = "فعال ✅";
}else{
$sup1 = "غیرفعال ❌";
}
$count = amarcount("user$text");
$txt="مشخصات ربات @$text

🤖 نام ربات : $fnbot

🆔 آیدی ربات : $idbot

➕ امکان اضافه شدن در گروه : $join1

✔ امکان خواندن پیام های گروه : $read1

🌀 ساپورت اینلاین : $sup1

🔢 تعداد کاربران ربات : $count";
sm($chatid,$txt);
}
}
}elseif($text=="تغییر توکن 🔁"){
	//sm($chatid,"درحال حاضر قسمت تغییر توکن ربات به دلایلی غیرفعال می‌باشد ⛔️",$channeleteal500);
//return false;
$coun = count(getallvalue("qw$chatid","bot"));
if($coun== 0 or empty(getallvalue("qw$chatid","bot"))){
$txt="شما هنوز رباتی نساخته‌اید ❌

با دستور /newbot اقدام به ساخت ربات کنید ‼️";
sm($chatid,$txt);
}else{
step($chatid,"Tokenbot");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}
}elseif($step=="Tokenbot"){
if($text=="بازگشت 🔙"){
step($chatid,"");
$txt="به منوی اصلی بازگشتید 👇";
sm($chatid,$txt,$keydo);
}else{
if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
//$token = getvalue("qw$chatid","bot",$text,"token");
setvalue("user","chatid",$chatid,"Other",$text);
$txt="توکن جدید ربات خود را از @BotFather برای ما ارسال کنید ❗\n\nتغییر توکن در صورتی امکان پذیر است که توکن شما فقط ریووک شده باشد ‼";
step($chatid,"Tokenbot2");
sm($chatid,$txt,$keyback);
}
}
}elseif($step=="Tokenbot2"){
if($text=="بازگشت 🔙"){
step($chatid,"Tokenbot");
$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید 👇";
sm($chatid,$txt,$keykarbar);
}else{
$user = getvalue("user","chatid",$chatid,"Other");
$url="https://api.telegram.org/bot$text/getme";
$get=json_decode(file_get_contents($url),true);
//$j=json_encode($get);
$ok=$get['ok'];
if($ok!==true){
sm($chatid,"توکن ربات اشتباه می‌باشد ‼");
}else{
$result =$get["result"];
$id =$result["username"];
if($user !==$id or strtolower($user) !== strtolower($id)){
$txt="این توکن با توکن ربات قبلی ربات شما تطابق ندارد ‼";
sm($chatid,$txt);
}else{
setvalue("qw$chatid","bot",$user,"token",$text);
setvalue("amarbot","bot",$user,"token",$text);
$file=file_get_contents("Haji.php");
$ch = str_replace('= "USERBOT','= "'.$user,$file);
$ch1 = str_replace(', "APITOKENBOT',', "'.$text,$ch);
$ch2 = str_replace('= "ADMINBOT','= "'.$chatid,$ch1);
file_put_contents("BotList/$user/$user.php",$ch2);
$url2="$domain/BotList/$user/$user.php";
$url3="https://api.telegram.org/bot$text/setwebhook?url=$url2";
file_get_contents($url3);
$txt="توکن ربات شما با موفقیت تغییر داده شد ✅

🤖 : @$user";
step($chatid,"");
sm($chatid,$txt,$keydo);
sm("$userid_chn_gozarsh_log","یک ربات با موفقیت تغییر توکن داده شد.✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$user\n\nToken :\n$text");
}}}}
elseif(isset($voice) and $chatid==$admin){
sm($chatid,$voice);
}
elseif($step=="sendall"){
if($text=="بازگشت 🔙"){
step($chatid,"");
sm($chatid,"عملیات لغو شد و به عقب بازگشتید ✅\n\nچکاری میخواهید انجام بدهید 👇",$keyhamganibot);
}else{
step($chatid,"");
$get=file_get_contents("karbar.txt");
$exp=getallvalue("user","chatid");
$count=count($exp);
if(!file_exists("foreach.txt")){
file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
$xl=0;
sm($chatid,"صبر کنید . . .");
$mess = $messageid+1;
$data["messagid"] = $mess;
unset($data["step$fromid"]);
file_put_contents("String.json", json_encode($data, 128|256));
for($x=$gettt;$x<=$count;$x++){
if($xl==20){
em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$data["messagid"]);
usleep(100000);
$xl=0;
}
$xl++;
$gettt = file_get_contents("foreach.txt");
if(isset($photo)){
sp($exp[$x],$photo,$caption,$kei);
}elseif(isset($video)){
sv($exp[$x],$video,$caption,$kei);
}elseif(isset($document)){
sd($exp[$x],$document,$caption,$kei);
}elseif(isset($audio)){
sa($exp[$x],$audio,$caption,$kei);
}elseif(isset($voice)){
svo($exp[$x],$voice,$caption,$kei);
}elseif(isset($sticker)){
ss($exp[$x],$sticker);
}elseif(isset($text)){
sm($exp[$x],$text,$kei);
$gettt++;
file_put_contents("foreach.txt",$gettt);

}
}
sm($chatid,"پیام شما با موفقیت برای تمامی کاربران ارسال شد ☑️

✅ تعداد موفق : برای همه ارسال شد
❎ تعداد ناموفق : بدون مشکل",$keyhamganibot);
unlink("foreach.txt");
}
}
elseif($step=="forward"){
if($text=="بازگشت 🔙"){
step($chatid,"");
sm($chatid,"عملیات لغو شد و به عقب بازگشتید ✅\n\nچکاری میخواهید انجام بدهید 👇",$keyhamganibot);
}else{
	step($chatid,"");
	$get=file_get_contents("karbar.txt");
$exp=getallvalue("user","chatid");
$count=count($exp);
if(!file_exists("foreach.txt")){
file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"صبر کنید . . .");
$mess = $messageid+1;
$data["messagid"] = $mess;
unset($data["step$fromid"]);
file_put_contents("String.json", json_encode($data, 128|256));
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$data["messagid"]);
usleep(100000);
$xl=0;
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
	fm($exp[$x],$chatid,$messageid);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);

}
}
sm($chatid,"پیام شما با موفقیت برای تمامی کاربران فوروارد شد ☑️

✅ تعداد موفق : برای همه ارسال شد
❎ تعداد ناموفق : بدون مشکل",$keyhamganibot);
unlink("foreach.txt");
}
elseif($chatid=="$admin"){
if($text=="ارسال همگانی 📣"){
step($chatid,"sendall");
$txt="
پیام خود را در بفرستید تا برای کاربران ارسال شود :
";

sm($admin,$txt,$keyback);
}
elseif($text=="فوروارد همگانی 📢"){
						step($chatid,"forward");
						$txt="
پیام خود را در بفرستید تا برای کاربران ارسال شود :
";

sm($admin,$txt,$keyback);
}
elseif($text=="Amar"){
$get=file_get_contents("karbar.txt");
$exp = explode("\n",$get);
$c = count($exp);
sm($chatid,$c);
}elseif($text=="آمار ربات 📊"){
$karbar=count(getallvalue("user","chatid"));
$amarbot=count(getallvalue("amarbot","token"));
$txt="👤 آمار کل کاربران : $karbar\n➕ آمار کاربران امروز : 52\n📣 آمار کانال ها : 4 عدد\n👥 آمار گروه ها : 1 عدد\n💰 آمار اسپانسر ها : موجود نیست\n🤖 آمار ربات ها : 896";
sm($chatid,$txt);
}
}
if($text=="ارسال پیام به کاربر 👤" || $text=="/javab"){
step($chatid,"getidkarbar");
$txt="💠لطفا ایدی عددی فرد را بفرستید \n\nتوجه داشته باشید کاربر باید جزو کاربران ربات باشد.";
sm($admin,$txt,$keyback);
}
if($step=="getidkarbar"){
if($text=="بازگشت 🔙"){
step($chatid,"sendtoall");
sm($admin,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyhamganibot);
}else{
$k = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getchat?chat_id=$text"),true);
$res = $k["ok"];
$id = $k['result']['id'];
$user = $k['result']['username'];
$name = $k['result']['first_name'];
if($res==false){
$txt="🚫چت ایدی غلط میباشد\nلطفا به موارد زیر دقت فرمایید :\n\n💠توجه داشته باشید چت ایدی شما حتما درست باشد.\n💠با شرایط بالا لطفا دوباره چت ایدی را بفرستید :";
sm($admin,$txt);
}else{
setOther($chatid,$id);
$txt="💥محتوا را بفرستید تا برای کاربر ارسال کنم :

⭐️محتوای شما میتواند شامل فیلم ، عکس ، متن ، استیکر ، موزیک و فایل باشد

⭐️شما میتوانید از html استفاده نمایید

⭐️شما میتوانید از کیبورد شیشه ای برای ارسال استفاده نمایید

آموزش ارسال کیبورد شیشه ای در کانال زیر 
 ";
step($chatid,"sendidkarbar");
sm($admin,$txt,$keyback);
}
}
}if($step=="sendidkarbar"){
if($text=="بازگشت 🔙"){
step($chatid,"sendtoall");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyhamganibot);
}else{
$id = getOther($chatid);
if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
$k = $m[2];
$hi=preg_split("/(%)([^\']+)(%)/",$caption);
$caption = $hi[0];
$kei = textToinline("%$k%",$caption);
}
if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
$k = $m[2];
$hi=preg_split("/(%)([^\']+)(%)/",$text);
$text = $hi[0];
$kei = textToinline("%$k%",$text);
}
sm($id,"یک پیام از مدیریت برای شما 👇");
if(isset($photo)){
sp($id,$photo,$caption,$kei);
}elseif(isset($video)){
sv($id,$video,$caption,$kei);
}elseif(isset($document)){
sd($id,$document,$caption,$kei);
}elseif(isset($audio)){
sa($id,$audio,$caption,$kei);
}elseif(isset($voice)){
svo($id,$voice,$caption,$kei);
}elseif(isset($sticker)){
ss($id,$sticker);
}elseif(isset($text)){
sm($id,$text,$kei);
}
step($chatid,"sendtoall");
sm($admin,"متن شما با موفقیت برای کاربر ارسال شد✅\n\nبه پنل بازگشتید :",$keyhamganibot);

}}
if($text=="gethazf"){
	sm($admin,"لطفا ایدی ربات را ارسال کنید :",$keyback);	
       step($admin,"gethazf");  
	}elseif($step=="gethazf"){
		step($admin,"");
		$list = "$text";
$files = glob("BotList/$text/*"); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }else{
rmdir($file);
}}
deletevalue("qw","bot",$text);
deletevalue("amarbot","bot",$text);
rmdir("BotList/$text");
$key = getvalue("user","chatid",$chatid,"keyboard");
if(strpos($key,'"text":"'.$text.'"},')){
$cc = str_replace('{"text":"'.$text.'"},',"",$key);
$cp = str_replace("[],","",$cc);
}else{
$cc = str_replace('{"text":"'.$text.'"}',"",$key);
$cp = str_replace("[],","",$cc);
}
$gathash = getallvalue("hashmoh$text","hash");
foreach($gathash as $mb){
$sql = "DROP TABLE `moh$mb$text`";
mysqli_query($con,$sql);
}
$sql = "DROP TABLE `hashmoh$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `user$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `admin$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `chan$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `dayamar$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `eshtrak$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `mohtava$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `mahdod$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `likes$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `liketext$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `once$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `blocklist$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `datatype$text`";
mysqli_query($con,$sql);
$sql = "DROP TABLE `datalist$text`";
mysqli_query($con,$sql);
$drop = "DROP TABLE `dok$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `delete$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `fileid$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `hash$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `replac$text`";
mysqli_query($con,$drop);
$all=getallvalue("hashmoh$text",'hash');
foreach($all as $xb){
$drop = "DROP TABLE `mohtava$xb$text`";
mysqli_query($con,$drop);
}
$all=getallvalue("hash$text",'hash');
foreach($all as $xb){
$drop = "DROP TABLE `mohtava$xb$text`";
mysqli_query($con,$drop);
}
$drop = "DROP TABLE `hashmoh$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `filter$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `del$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `code$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `moh$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `channel$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `robot$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `data$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `pasokh$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `user$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `group$text`";
mysqli_query($con,$drop);
$drop = "DROP TABLE `supergroup$text`";
mysqli_query($con,$drop);
deletevalue("copycode","userbot",$text);
step($chatid,"");
sm($admin,$list,$keyrobotsbot);
	}
if($text=="getbot"){
	sm($admin,"لطفا ایدی عددی کاربر را ارسال کنید :");	
       step($admin,"getbot");  
	}elseif($step=="getbot"){
		step($admindaniyal,"");
		$list = "لیست ربات ها \n\n";
		$getall = getallvalue("qw$text","bot");
		foreach($getall as $key){
			$list .= "@$key\n";
			}
			sm($admin,$list);
		}
$timenow = time();
$lasttime = file_get_contents("data/$chatid/time.txt");
$emtiaz = file_get_contents("data/$chatid/emtiaz.txt");
if($text == "سکه روزانه ⏰"){
mkdir("data");
mkdir("data/$chatid");
if($timenow < $lasttime){
sm($chatid,"️شما سکه روزانه خود را دریافت کردید ‼️");
}else{
$rand = rand(1,5);
file_put_contents("data/$chatid/emtiaz.txt", $emtiaz + $rand);
file_put_contents("data/$chatid/time.txt", $timenow + 86400);
file_put_contents("data/$chatid/index.html",$indeht);
sm($chatid,"امروز به تعداد $rand سکه دریافت کردید\n\nفردا هم بیا سکه تو بگیر 😉");
$emti1 = getCoin($chatid);
$emti2 = $emti1 + $rand;
setvalue("user","chatid",$chatid,"emtiaz",$emti2);
}}
?>