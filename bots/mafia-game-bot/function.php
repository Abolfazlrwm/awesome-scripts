<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(E_ALL);
http_response_code(200);
litespeed_finish_request();
define('API_KEY','2029909972:HxESM4RL_r1PFvo');
//============================================================================//
@$server = 'localhost';
@$username = 'snowytea_mf';
@$password = 'PUT-YOUR-KEY-HERE';
@$db = 'snowytea_mf';
$connect = mysqli_connect($server,$username,$password,$db);
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
function StartGame($domain,$f,$datas=[]){
$curl = curl_init("$domain/$f-player-game.php");
curl_setopt_array($curl,[
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POSTFIELDS => $datas,
CURLOPT_CUSTOMREQUEST => 'POST',
]);
return curl_exec($curl);
}
function DeleteFolder($path){
 if($handle=opendir($path)){
  while (false!==($file=readdir($handle))){
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
function TimeFormat($seconds = 600 , $mode = 'long', $extra = ''){
$names = array('long' => array("سال","ماه","روز","ساعت","دقیقه","ثانیه"), 'short' => array("yr","mnth","day","hr","min","sec"));
$seconds = floor($seconds);
$minutes = intval($seconds / 60);
$seconds -= ($minutes * 60);
$hours = intval($minutes / 60);
$minutes -= ($hours * 60);
$days = intval($hours / 24);
$hours -= ($days * 24);
$months = intval($days / 31);
$days -= ($months * 31);
$years = intval($months / 12);
$months -= ($years * 12);
$result = array();
if($years) $result[] = sprintf("%s%s %s%s", number_format($years),$extra, $names[$mode][0], $years == 1 ? "" : "");
if($months) $result[] = sprintf("%s%s %s%s", number_format($months),$extra, $names[$mode][1], $months == 1 ? "" : "");
if($days) $result[] = sprintf("%s%s %s%s", number_format($days),$extra, $names[$mode][2], $days == 1 ? "" : "");
if($hours) $result[] = sprintf("%s%s %s%s", number_format($hours),$extra, $names[$mode][3], $hours == 1 ? "" : "");
if($minutes and count($result) < 2) $result[] = sprintf("%s%s %s%s", number_format($minutes),$extra, $names[$mode][4], $minutes == 1 ? "" : "");
if(($seconds and count($result) < 2) or !count($result)) $result[] = sprintf("%s%s %s%s", number_format($seconds),$extra, $names[$mode][5], $seconds == 1 ? "" : "");
return implode(" و ", $result);
}
function Base($user){
if(is_numeric($user)){
$res = base64_encode($user);
}else{
$res = base64_decode($user);
}
$res = str_replace('=',null,$res);
return $res;
}
function GetRanks($file){
global $connect;
$code = $connect->query("SELECT * FROM `user` ORDER BY `$file` DESC LIMIT 10");
while($res = mysqli_fetch_assoc($code)){
$i++;
$names = $res['name'];
$scores = $res["$file"];
$result = $result."$i ➣ $names | $scores\n";
}
return $result;
}
function GetLike($from_id,$which,$count){
global $connect;
$max = $count + 10;
$code = mysqli_query($connect,"SELECT * FROM `$which` WHERE `who` = '$from_id' LIMIT $max OFFSET $count");
while($res = mysqli_fetch_assoc($code)){
$i++;
$ids = $res['id'];
$hesname = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$ids' LIMIT 1"))["name"];
$hesid = Base($ids);
$result = $result."• $hesname | 𝓹𝓻𝓸𝓯𝓲𝓵𝓮 : /user_$hesid\n";
}
return $result;
}
function Random($count){
$container = 'QWERTYUIOPLKJHGFDSAZCXVBNM';
$key = '';
for($i = 0;$i < $count;$i++){
$_rand = rand(0,mb_strlen($container)-1);
$key .= substr($container, $_rand, 1);
}
return $key;
}
function GetRay($server){
global $connect;
$RAYS = [];
$array = file_get_contents("play/$server/newgame.txt");
$users = explode("\n",$array);
foreach($users as $user){
$RAY = count(json_decode(file_get_contents("play/$server/ray.json"),true)["$user"]["ray"]);
$RAYS[$user] = $RAY;
}
arsort($RAYS);
foreach($RAYS as $key => $user){
$names = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$key' LIMIT 1"))["name"];
$list[] = array('id'=>$key,'name'=>$names,'coins'=>$RAYS[$key]);
}
return $list;
}
function Information($server){
$array = json_decode(file_get_contents("play/$server/info.json"),true);
$count = ['LIVE' => 0,'DEAD' => 0];
foreach($array as $value){
foreach($value as $key){
if($key == 'LIVE')
$count['LIVE'] += 1;
if($key == 'DEAD')
$count['DEAD'] += 1;
}
}
return $count;
}
function ListRay($type,$server,$gets){
global $connect;
if($type == "guilty"){
$guilty_json = json_decode(file_get_contents("play/$server/court.json"),true)["$gets"]["guilty"];
foreach($guilty_json as $key){
$names = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$key' LIMIT 1"))["name"];
$list = $list."$names رای به *گناهکاری* داد !\n";
}
}
if($type == "innocent"){
$innocent_json = json_decode(file_get_contents("play/$server/court.json"),true)["$gets"]["innocent"];
foreach($innocent_json as $key){
$names = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE `id` = '$key' LIMIT 1"))["name"];
$list = $list."$names رای به _بی‌گناهی_ داد !\n";
}
}
return $list;
}
function GetPercent($value,$total){
return round(($value * 100) / $total,0)."%";
}
function PercentEmoji($Percent){
$num = str_replace("%",null,$Percent);
for($i = 1;$i < 11;$i++){
$b = $i * 10;
if($num >= $b){
$res = $res."★";
}else{
$res = $res."☆";
}
}
return $res;
}
function Check($from_id,$text){
$id = explode(":","$text")[0];
$md5 = explode(":","$text")[1];
$ok = md5("KEY_$id");
if($from_id != $id){
if($md5 == $ok){
return 'true';
}else{
return 'false';
}
}else{
return 'userid';
}
}
function MakeID($from_id){
$md5 = md5("KEY_$from_id");
return "$from_id:$md5";
}
function Font($text){
$font = [["۰","۱","۲","۳","۴","۵","۶","۷","۸","۹"]];
$echo = str_replace(range(0,9),$font[array_rand($font)],$text);
return $echo;
}
function PlayerList($server,$game){
for($i = 1;$i < $game + 1;$i++){
$next = Font($i);
$serverid = json_decode(file_get_contents("play/$server/id.json"),true);
$players = $serverid["player$i"];
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$XB = $serverinfo["$players"]["nameP"] ?? null;
if($XB != null){
$list = $list."$next . $XB\n";
}
}
return $list;
}
function PlayerNumber($from_id,$name,$game,$server){
switch ($game) {
case '9':
$TypeGame = "آسان ( ۹ نفره )";
break;
case '10':
$TypeGame = "متوسط ( ۱۰ نفره )";
break;
case '12':
$TypeGame = "سخت ( ۱۲ نفره )";
break;
case '15':
$TypeGame = "سخت ( ۱۵ نفره )";
break;
case '20':
$TypeGame = "بدون مستقل ( ۲۰ نفره )";
break;
case '23':
$TypeGame = "خیلی سخت ( ۲۳ نفره )";
break;
}
for($i = 1;$i < $game + 1;$i++){
$serverid = json_decode(file_get_contents("play/$server/id.json"),true);
if($serverid["player$i"] == false){
$serverid["player$i"] = "$from_id";
$outjson = json_encode($serverid,true);
file_put_contents("play/$server/id.json",$outjson);
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$from_id"]["live"] = "LIVE";
$serverinfo["$from_id"]["nameP"] = "$name";
$serverinfo["$from_id"]["number"] = "player$i";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$nex = Font($i);
$listplayer = PlayerList($server,$game);
if($i == $game) file_put_contents("play/$server/lock.txt","$game");
bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>"
🎲 درحال جستجوی بازیکن برای شروع...
🔸 نوع بازی : $TypeGame

👥 لیست افراد در صف انتظار

$listplayer
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode(['keyboard'=>[
[['text'=>"💭 پیام خصوصی"],['text'=>"🔮 جادو ها"]],
[['text'=>"🚫 گزارش تخلف"],['text'=>"خروج از بازی"]],
],'resize_keyboard'=>true,'input_field_placeholder'=>"🎈 منو بازی"])
]);
break;
}
}
return $nex;
}
function PlayerJobs($server,$playerid,$randsjob){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$playername = $serverinfo["$playerid"]["nameP"] ?? null;
if($randsjob == "پدرخوانده 🚬"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "پدرخوانده 🚬";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["father"] = "$playername";
$serverjobs["fatherid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : پدرخوانده 🚬
گروه : مافیا 🔴
توضیحات : رهبر مافیا! مافیا را مدیریت کنید تا برنده شوید. هر شب کسی را انتخاب کنید تا نوچه شما به او حمله کند. در صورت مردن نوچه مافیا, خود به هدف حمله میکنید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "نوچه مافیا 🕶"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "نوچه مافیا 🕶";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["noche"] = "$playername";
$serverjobs["nocheid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : نوچه مافیا 🕶
گروه : مافیا 🔴
توضیحات : هر شب به یکی از افراد شهر حمله کنید! اگر پدرخوانده هدف تعیین کند، شما به هدف او حمله میکنید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "مفتش 🔎"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "مفتش 🔎";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["mofatesh"] = "$playername";
$serverjobs["mofateshid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : مفتش 🔎
گروه : مافیا 🔴
توضیحات : هر شب نقش دقیق یکی را پیدا کنید و به مافیا کمک کنید تا نقش های مهم را از بازی حذف کند.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "مذاکره کننده 🤝"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "مذاکره کننده 🤝";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["mozakere"] = "$playername";
$serverjobs["mozakereid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : مذاکره کننده 🤝
گروه : مافیا 🔴
توضیحات : درصورت مردن یکی از یارها بجز پدرخوانده و مافیا می تواند بجای شلیک کردن مذاکره کند
کسی که با او مذاکره می شود به نوچه مافیا تبدیل می شود مافیا فقط یکبار می تواند مذاکره کند !
مذاکره با بازپرس و دکتر و بادیگارد و محقق و کاراگاه موفق نخواهد شد
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "هکر 👨🏻‍💻"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "هکر 👨🏻‍💻";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["hacker"] = "$playername";
$serverjobs["hackerid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : هکر 👨🏻‍💻
گروه : مافیا 🔴
توضیحات : هرشب تلاش می کنید تا یک بازیکن را هک کنید شانس موفقیت شما 50 درصد است
درصورت موفق بودن هک و هدف شما در روز بعد قادر به صحبت و رای دادن نخواهد بود
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "شیاد ☠️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "شیاد ☠️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["shayad"] = "$playername";
$serverjobs["shayadid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : شیاد ☠️
گروه : مافیا 🔴
توضیحات : هر شب یکی را انتخاب کنید تا در صورت مردن خودتان نقش او به بقیه نشان داده شود نه نقش مافیای شما
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "خفتگير 🚷"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "خفتگير 🚷";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["kheftgir"] = "$playername";
$serverjobs["kheftgirid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : خفتگير 🚷
گروه : مافیا 🔴
توضیحات : هر شب یکی از اعضای دهات را از ایفای نقشش باز دارید
دکتر , بادیگارد و پلیس قابل خفت کردن نیستن
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "خائن 😈"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "خائن 😈";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["khaen"] = "$playername";
$serverjobs["khaenid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : خائن 😈
گروه : شهروند 🟢
توضیحات : عضو شهر هستی ولی باید در جریان بازی به نفع مافیا عمل کنید شهروند خائن باید در فاز روز اعضاء مافیا را شناسایی کند و به آن‌ها در برد کمک کند !
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "شهروند 🚶‍♂️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "شهروند 🚶‍♂️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["shahr"] = "$playername";
$serverjobs["shahrid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : شهروند 🚶‍♂️
گروه : شهروند 🟢
توضیحات : با همکاری دیگر اعضای گروه شهروند، مافیا ها را پیدا و با رای دادن اعدام کنید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "دکتر 💉"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "دکتر 💉";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["doctor"] = "$playername";
$serverjobs["doctorid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : دکتر 💉
گروه : شهروند 🟢
توضیحات : هر شب جان یکی را از حمله افراد نجات دهید. در طول بازی یک شب میتوانید خود را نجات دهید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "کاراگاه 🕵️‍♂️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "کارآگاه 🕵";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["karagah"] = "$playername";
$serverjobs["karagahid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : کاراگاه 🕵️‍♂️
گروه : شهروند 🟢
توضیحات : هر شب مشکوکیت یکی را بررسی کنید. با یکبار بررسی نمیتوانید پدرخوانده را پیدا کنید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "بازپرس 🔗"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "بازپرس 🔗";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["bazpors"] = "$playername";
$serverjobs["bazporsid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
$keyBazpors = [];
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($live == "LIVE" and $playerid != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM `user` WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyBazpors[] = [['text'=>"$yournamer",'callback_data'=>"bazpors-$timer-$k"]];
}
}
bot('sendMessage',[
'chat_id'=>$playerid,
'text'=>"
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : بازپرس 🔗
گروه : شهروند 🟢
توضیحات : در طول روز یکی را انتخاب کنید تا شب بعد از او بازپرسی کنید. در شب تصمیم بگیرید که او را اعدام یا آزاد کنید. در صورت کشتن اعضای شهر دیگر نمیتوانید کسی را بکشید.
📜 مقررات و ترکیب بازی 👈 /help_terms

یک نفر را انتخاب کنید تا اورا زندانی کرده و شب از او بازجویی کنید 👇

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyBazpors
])
]);
}
if($randsjob == "اسنایپر 🔫"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "اسنایپر 🔫";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["asnayper"] = "$playername";
$serverjobs["asnayperid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : اسنايپر 🔫
گروه : شهروند 🟢
توضیحات : عدالت را به دستان خود بگیرید و شب ها مافیاها را بکشید. در صورت کشتن اشتباه اعضای شهر از احساس گناه دست به خودکشی میزنید!
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "ساحره 🧙‍♀️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "ساحره 🧙‍♀";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["sahere"] = "$playername";
$serverjobs["sahereid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$playerid\n");
fclose($DeadWrite);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : ساحره 🧙‍♀️
گروه : شهروند 🟢
توضیحات : شما می توانید با مرده ها حرف بزنید و از عالم غیب برای شهر خبر بیاورید.
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "زره پوش 🛡"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "زره پوش 🛡";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["zereh"] = "$playername";
$serverjobs["zerehid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : زره پوش 🛡
گروه : شهروند 🟢
توضیحات : شما دوجان دارید با یکبار حمله کشته نمی شوید مگر اینکه اعدام شوید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "بادیگارد 💂‍♂️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "بادیگارد 💂‍♂️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["bodyguard"] = "$playername";
$serverjobs["bodyguardid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : بادیگارد 💂‍♂️
گروه : شهروند 🟢
توضیحات : هر شب از یکی محافظت کنید اگر کسی به هدف شما حمله کرد به او حمله میکنید و جان خود را نیز از دست میدهید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "محقق 🔍"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "محقق 🔍";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["mohageg"] = "$playername";
$serverjobs["mohagegid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : محقق 🔍
گروه : شهروند 🟢
توضیحات : هر شب درباره کسی تحقیق کنید و نقش او را حدس بزنید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "پلیس 👮🏻‍♂️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "پلیس 👮🏻‍♂️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["police"] = "$playername";
$serverjobs["policeid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : پلیس 👮🏻‍♂️
گروه : شهروند 🟢
توضیحات : اگر در شب هوشیار بمانید به هر کسی که به شما نزدیک شد شلیک میکنید ! شما میتوانید 2 شب هوشیار بمانید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "دیدبان 👀"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "دیدبان 👀";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["didban"] = "$playername";
$serverjobs["didbanid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : دیدبان 👀
گروه : شهروند 🟢
توضیحات : هر شب خانه یکی را بپایید تا ببینید چه کسانی به آنجا رفته اند
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "ردگیر 👣️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "ردگیر 👣️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["radgir"] = "$playername";
$serverjobs["radgirid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : ردگیر 👣️
گروه : شهروند 🟢
توضیحات : هرشب یکی را دنبال کنید و ببینید چه کسی را هدف قرار داده
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "کشیش 🧔"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "کشیش 🧔";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["keshish"] = "$playername";
$serverjobs["keshishid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : کشیش 🧔
گروه : شهروند 🟢
توضیحات : کشیش میتونه جلوی جرم و جنایتو برای یک شب بگیره اون میتونه برای همه دعا کنه و اونشب هیچ کس قادر به استفاده از تواناییاش نیست ! حیف که فقط یکباره
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "خبرنگار 📸"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "خبرنگار 📸";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["khabarnegar"] = "$playername";
$serverjobs["khabarnegarid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : خبرنگار 📸
گروه : شهروند 🟢
توضیحات : اگر مافیا مذاکره کرده باشد در استعلام مشخص می شود اگر مذاکره کننده در بازی نباشد نقش ردگیر را خواهد داشت
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "فرشته نجات 🧖🏻‍♀️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "فرشته نجات 🧖🏻‍♀️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["fereshte"] = "$playername";
$serverjobs["fereshteid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : فرشته نجات 🧖🏻‍♀️
گروه : شهروند 🟢
توضیحات : شما می توانید در طول بازی یک نفر را زنده کنید تا بعنوان شهروند ساده به بازی برگردد. فقط می توانید شهروند هارا زنده کنید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "شهردار 🎩"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "شهردار 🎩";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["shahrdar"] = "$playername";
$serverjobs["shahrdarid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : شهردار 🎩
گروه : شهروند 🟢
توضیحات : در مرحله رای گیری اگر حس کند شهروندی بی گناه اعدام می شود حق وتو دارد فقط یکبار می توانید از این توانایی استفاده کنید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "قاتل سریالی 🔪"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "قاتل سریالی 🔪";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["ghatel"] = "$playername";
$serverjobs["ghatelid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : قاتل سریالی 🔪
گروه : مستقل 🟡
توضیحات : هر شب یکی را به قتل برسانید تا شاهد نابودی شهر و مافیا باشید!
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "جوکر 👺"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "جوکر 👺";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["joker"] = "$playername";
$serverjobs["jokerid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : جوکر 👺
گروه : مستقل 🟡
توضیحات : اعدام شوید تا برنده شوید! بعد مرگ انتقام خود را از یکی از کسانی که به شما رای گناهکار دادند بگیرید!
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "گرگینه 🐺"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "گرگینه 🐺";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["gorg"] = "$playername";
$serverjobs["gorgid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : گرگینه 🐺
گروه : مستقل 🟡
توضیحات : در شب هایی که ماه کامل است اگر تصمیم به حمله بگیرید هم به هدف خود حمله می کنید هم  به تمامی افرادی که به خانه هدف شما بیایند
درصورتی که در ماه کامل هدفی انتخاب نکنید به هر کسی که به خانه شما بیاید حمله خواهید کرد
- شما دارای دفاع معمولی هستید و توسط کسانی که حمله معمولی دارند (مثل مافیا) کشته نمی شوید
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "آشپز 👨🏻‍🍳"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "آشپز 👨🏻‍🍳";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["ashpaz"] = "$playername";
$serverjobs["ashpazid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : آشپز 👨🏻‍🍳
گروه : مستقل 🟡
توضیحات : هر شب یکی را مسموم کنید هدف شما شب بعد به دلیل مسمومیت خواهد مرد تنها دکتر می تواند مسمومیت شخصی را درمان کند
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "گورکن ⚰️"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "گورکن ⚰️";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["gorkan"] = "$playername";
$serverjobs["gorkanid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : گورکن ⚰️
گروه : مستقل 🟡
توضیحات : شب هایی که ماه کامل نیست گور یک نفر را بکنید فردای آن روز خواهد مرد هیچ چیز مانع شما نیست
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "آرسونیست 🔥"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "آرسونیست 🔥";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["arsonist"] = "$playername";
$serverjobs["arsonistid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : آرسونیست 🔥
گروه : مستقل 🟡
توضیحات : هر شب یکی را نفتی کنید و با کبریت زدن یکجا تمام افراد نفتی را به آتش بکشید!
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
if($randsjob == "بمب گذار 💣"){
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$serverinfo["$playerid"]["job"] = "بمب گذار 💣";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["boomb"] = "$playername";
$serverjobs["boombid"] = "$playerid";
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
return "
🗣 بازی شروع شد، ۲۵ ثانیه وقت داری به بقیه سلام کنی

نقش شما : بمب گذار 💣
گروه : مستقل 🟡
توضیحات : شب ها توی خونه یه نفر بمب بذارید اگر اون شب کاری انجام بده بمب منفجر میشه اگر توی رای گیری فردا بازیکنی که بمب گذاری شده اعدام بشه بمب منفجر میشه و یه نفر دیگه رو هم میکشه اگر شب کسی بسمت بمب گذار شلیک کنه بمب منفجر میشه و یه نفر دیگه هم کشته میشه
📜 مقررات و ترکیب بازی 👈 /help_terms

💬 چت : فعال
🌙 شب اول 👈 ⏱ 25 ثانیه
";
}
}