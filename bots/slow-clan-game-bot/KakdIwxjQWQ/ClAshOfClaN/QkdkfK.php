<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
ob_start('ob_gzhandler');
$telegram_ip_ranges = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
    ['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;

foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    
    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;
}
if(!$ok)
{
	exit(header("location: https://SnowyTeam.xyz/DcJsjxjdbd"));
	}
unlink('error_log');
define("QUYSVB","[*[TOKEN]*]");//توکن ربات خود را وارد کنید
date_default_timezone_set('Asia/Tehran');
flush();
include "[*[TELEGRAM]*].php";
include "lvp.php";
@$viewbot = file_get_contents("viewbot.txt");
$channels = file_get_contents("channels");
if(file_exists("starttxt.txt")){
$starttxt = file_get_contents("starttxt.txt");
}else{
$starttxt = "متن استارت تنظیم نشده است";
}
//////---sinza///
if(file_exists("cadmin1.txt")){
$cadmin1 = file_get_contents("cadmin1.txt");
}else{
$cadmin1 = "[*[ADMIN]*]";
}
////---//////
if(file_exists("cadmin2.txt")){
$cadmin2 = file_get_contents("cadmin2.txt");
}else{
$cadmin2 = "[*[ADMIN]*]";
}
////---//////
if(file_exists("crozamount.txt")){
$crozamount = file_get_contents("crozamount.txt");
}else{
$crozamount = "10";
}
////---//////sinza
if(file_exists("cztime.txt")){
$cztime = file_get_contents("cztime.txt");
}else{
$cztime = "10";
}
////---//////
if(file_exists("cadmin3.txt")){
$cadmin3 = file_get_contents("cadmin3.txt");
}else{
$cadmin3 = "[*[ADMIN]*]";
}
////---//////
if(file_exists("shoptxt.txt")){
$shoptxt = file_get_contents("shoptxt.txt");
}else{
$shoptxt = "متن فروشگاه تنظیم نشده است";
}
////---//////
if(file_exists("goldtxt.txt")){
$goldtxt = file_get_contents("goldtxt.txt");
}else{
$goldtxt = "متن خرید الماس تنظیم نشده است";
}
///////---///
if(file_exists("freegoldtxt.txt")){
$freegoldtxt = file_get_contents("freegoldtxt.txt");
}else{
$freegoldtxt = "متن الماس رایگان تنظیم نشده است";
}
//////////-------/////////
if(file_exists("clashd1.txt")){
$clashd1 = file_get_contents("clashd1.txt");
}else{
$clashd1 = "🏆برترین ها🏆";
}
//////////-------/////////
if(file_exists("clashd2.txt")){
$clashd2 = file_get_contents("clashd2.txt");
}else{
$clashd2 = "🛒فروشگاه🛒";
}
//////////-------/////////sinza
if(file_exists("clashd3.txt")){
$clashd3 = file_get_contents("clashd3.txt");
}else{
$clashd3 = "💂🏻‍♂️سرباز";
}
//////////-------/////////
if(file_exists("clashd4.txt")){
$clashd4 = file_get_contents("clashd4.txt");
}else{
$clashd4 = "🏡منابع";
}
//////////-------/////////
if(file_exists("clashd5.txt")){
$clashd5 = file_get_contents("clashd5.txt");
}else{
$clashd5 = "⚔️حمله";
}
//////////-------/////////
if(file_exists("clashd6.txt")){
$clashd6 = file_get_contents("clashd6.txt");
}else{
$clashd6 = "🔫انتقام";
}
//////////-------/////////
if(file_exists("clashd7.txt")){
$clashd7 = file_get_contents("clashd7.txt");
}else{
$clashd7 = "💣ایجاد تله";
}
//////////-------/////////
if(file_exists("clashd8.txt")){
$clashd8 = file_get_contents("clashd8.txt");
}else{
$clashd8 = "⛺️دهکده من";
}
//////////-------/////////
if(file_exists("clashd9.txt")){
$clashd9 = file_get_contents("clashd9.txt");
}else{
$clashd9 = "🛡اتحاد";
}
//////////-------/////////
if(file_exists("clashd11.txt")){
$clashd11 = file_get_contents("clashd11.txt");
}else{
$clashd11 = "💳انتقال الماس💳";
}
//////////-------/////////
if(file_exists("gemamount.txt")){
$gemamount = file_get_contents("gemamount.txt");
}else{
$gemamount = "10";
}
//////----///
if(file_exists("cgolamount.txt")){
$cgolamount = file_get_contents("cgolamount.txt");
}else{
$cgolamount = "100";
}
//////----///
if(file_exists("cminmove.txt")){
$cminmove = file_get_contents("cminmove.txt");
}else{
$cminmove = "10";
}
//////----///
if(file_exists("cshoplink.txt")){
$cshoplink = file_get_contents("cshoplink.txt");
}else{
$cshoplink = "https://t.me/SlowCreateBot";
}
//////----///
if(file_exists("cplan1.txt")){
$cplan1 = file_get_contents("cplan1.txt");
}else{
$cplan1 = "💎 1000 الماس | 10000 تومان 💎";
}
//////----///
if(file_exists("cplan2.txt")){
$cplan2 = file_get_contents("cplan2.txt");
}else{
$cplan2 = "💎 2000 الماس | 19000 تومان 💎";
}
//////----///
if(file_exists("cplan3.txt")){
$cplan3 = file_get_contents("cplan3.txt");
}else{
$cplan3 = "💎 4000 الماس | 35000 تومان 💎";
}
//////----///
if(file_exists("clashdroz.txt")){
$clashdroz = file_get_contents("clashdroz.txt");
}else{
$clashdroz = "🎊الماس روزانه💎";
}
//////----///
if(file_exists("clashdgift.txt")){
$clashdgift = file_get_contents("clashdgift.txt");
}else{
$clashdgift = "🔅کد هدیه😻";
}
$back = 'بازگشت🏠';

$button_manage = json_encode(['keyboard'=>[
[['text'=>"🔥 آمار ربات"],['text'=>"🔖 پیام همگانی"],['text'=>"🔱 گندم همگانی"]],
[['text'=>"💎 الماس همگانی"],['text'=>"🎁 ساخت کد"],['text'=>"🌀 اهدا الماس"]],
[['text'=>"🚫 بلاک کردن"],['text'=>"➰ دکمه ها"],['text'=>"♻️ انبلاک کردن"]],
[['text'=>"🛍 تنظیم فروشگاه"],['text'=>"🗂 تنظیم متن"],['text'=>"👤 ادمین ها"]],
[['text'=>"تنظیم کانال 🆔"],['text'=>"🕹 محدودیت حمله"],['text'=>"💳 بخش انتقال"]],
[['text'=>"💢خاموش روشن حمله"],['text'=>"🍁 اهدای چوب"],['text'=>"☀️ اهدای گندم"]],
[['text'=>"💣 اهدای تله"],['text'=>"💂‍♀️ اهدای سرباز"],['text'=>"🛑 کسر الماس"]],
[['text'=>"⭕️ کسر سرباز"],['text'=>"⚠️ کسر تله"],['text'=>"♨️ کسر چوب"]],
[['text'=>"🔫 سرباز همگانی"],['text'=>"🧨 تله همگانی"],['text'=>"💥 چوب همگانی"]],
[['text'=>"⬇️ کسر گندم"],['text'=>"$back"],['text'=>"⏳ اشتراک مانده"]],
],'resize_keyboard'=>true]);
//////----///
//////----///
function gift_loot($chat_id){
$cup = get_user($chat_id,"cup");
if($cup >= 55000){
return 400000;
}elseif($cup >= 50000){
return 350000;
}elseif($cup >= 40000){
return 300000;
}elseif($cup >= 35000){
return 250000;
}elseif($cup >= 30000){
return 200000;
}elseif($cup >= 25000){
return 150000;
}elseif($cup >= 24000){
return 140000;
}elseif($cup >= 22000){
return 130000;
}elseif($cup >= 20000){
return 120000;
}elseif($cup >= 19000){
return 110000;
}elseif($cup >= 16000){
return 100000;
}elseif($cup >= 14000){
return 80000;
}elseif($cup >= 12000){
return 70000;
}elseif($cup >= 10000){
return 65000;
}elseif($cup >= 5000){
return 50000;
}elseif($cup >= 2500){
return 25000;
}elseif($cup >= 1000){
return 10000;
}elseif($cup >= 500){
return 5000;
}elseif($cup >= 100){
return 2500;
}elseif($cup >= 50){
return 1000;
}elseif($cup >= 20){
return 500;
}elseif($cup >= 10){
return 100;
}elseif($cup >= 0){
return 50;
}
}
function get_clan($name,$mode){
if(!is_dir("clans/$name/config")){
mkdir("clans/$name/config");
}
return file_get_contents("clans/$name/config/$mode");
}
function ForwardMessage($chat_id,$from_chat,$message_id){
	bot('forwardmessage',[
	'chat_id' => $chat_id,
	'from_chat_id' => $from_chat,
	'message_id' => $message_id
	]);
}
function chenge_clan($name,$mode,$new){
if(!is_dir("clans/$name/config")){
mkdir("clans/$name/config");
}
file_put_contents("clans/$name/config/$mode",$new);
}
function remove_clan($chat_id,$name){
$joinclan = $name;
chenge_user($chat_id,"joinclan",null);
rmdir("users/$chat_id/clan");
rmdir("clans/$joinclan/users/$chat_id");
file_put_contents("users/$chat_id/clan/name.txt",null);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
از اتحاد اخراج شدید💀
",
'parse_mode'=>'MARKDOWN',
]);
 
$adminclans = file_get_contents("clans/$joinclan/admin.txt");
QuBnM('sendmessage',[ 
    'chat_id'=>$adminclans, 
    'text'=>" 
کاربر [$chat_id](tg://user?id=$chat_id) از اتحاد اخراج شد💀
در صورت نیاز میتوانید افراد دیگر را با ارسال ایدی حذف و یا یا برروی دکمه $back کلیک کنید!
",
'parse_mode'=>'MARKDOWN',
]);
}

function left_clan($chat_id){
$joinclan = get_user($chat_id,"joinclan");
chenge_user($chat_id,"joinclan",null);
rmdir("users/$chat_id/clan");
rmdir("clans/$joinclan/users/$chat_id");
file_put_contents("users/$chat_id/clan/name.txt",null);
$adminclans = file_get_contents("clans/$joinclan/admin.txt");
QuBnM('sendmessage',[ 
    'chat_id'=>$adminclans, 
    'text'=>" 
کاربر [$chat_id](tg://user?id=$chat_id) اتحاد را ترک کرد💀
",
'parse_mode'=>'MARKDOWN',
]);
}
function join_clan($chat_id,$name){

}
function equel_cup($cup1,$cup2){
$last = $cup1 - $cup2;
$last2 = $cup2 - $cup1;
if($cup1 == $cup2){
return true;
}elseif($last <= 20){
return true;
}elseif($last2 <= 20){
return true;
}else{
return false;
}
}
 
$cupall = get_user($fadmin,'cup');
if($cupall <= 0){
file_put_contents("users/$fadmin/cup",0);
}
function update_level($fadmin){
$time = time();
$tixp = file_get_contents("users/$fadmin/tixp");
$time2 = $time - $tixp;
$day = $time2 / 86400;
if($day > 1){
$xp = file_get_contents("users/$fadmin/xp");
$xp += 1;
file_put_contents("users/$fadmin/xp",$xp);
file_put_contents("users/$fadmin/tixp",$time);
}
}

function conver_gold($fadmin,$type,$pack){
$gold = round(get_user($fadmin,'gold'));
$getlootme = round(get_user($fadmin,$type));
if($pack == 1){
$getlootme += 100000;
if($gold > 10){
chenge_user($fadmin,$type,$getlootme);
$gold -= 10;
chenge_user($fadmin,'gold',$gold);
return true;
}else{
return false;
}


}
if($pack == 2){
$getlootme += 500000;
if($gold > 50){
chenge_user($fadmin,$type,$getlootme);
$gold -= 50;
chenge_user($fadmin,'gold',$gold);

return true;

}else{
return false;
}
}
if($pack == 3){
$getlootme += 1000000;
if($gold > 100){
chenge_user($fadmin,$type,$getlootme);
$gold -= 100;
chenge_user($fadmin,'gold',$gold);

return true;

}else{
return false;
}
 
}
if($pack == 4){
$getlootme += 2000000;
if($gold > 200){
chenge_user($fadmin,$type,$getlootme);
$gold -= 200;
chenge_user($fadmin,'gold',$gold);
return true;
}else{
return false;
}
}
}

function Get_Loot($ctroop,$cloot){
if($ctroop >= 10000000){
$percent = $cloot / 100;
$percent *= 80;
return $percent;
}elseif($ctroop >= 70000000){
$percent = $cloot / 100;
$percent *= 75;
return $percent;

}elseif($ctroop >= 50000000){
$percent = $cloot / 100;
$percent *= 60;
return $percent;

}elseif($ctroop >= 30000000){
$percent = $cloot / 100;
$percent *= 59;
return $percent;

}elseif($ctroop >= 10000000){
$percent = $cloot / 100;
$percent *= 57;
return $percent;

}elseif($ctroop >= 9000000){
$percent = $cloot / 100;
$percent *= 55;
return $percent;

}elseif($ctroop >= 7000000){
$percent = $cloot / 100;
$percent *= 50;
return $percent;

}elseif($ctroop >= 5000000){
$percent = $cloot / 100;
$percent *= 47;
return $percent;

}elseif($ctroop >= 3000000){
$percent = $cloot / 100;
$percent *= 45;
return $percent;
 
}elseif($ctroop >= 1000000){
$percent = $cloot / 100;
$percent *= 40;
return $percent;

}elseif($ctroop >= 500000){
$percent = $cloot / 100;
$percent *= 48;
return $percent;

}elseif($ctroop >= 100000){
$percent = $cloot / 100;
$percent *= 46;
return $percent;

}elseif($ctroop >= 50000){
$percent = $cloot / 100;
$percent *= 44;
return $percent;

}elseif($ctroop >= 10000){
$percent = $cloot / 100;
$percent *= 43;
return $percent;

}elseif($ctroop >= 5000){
$percent = $cloot / 100;
$percent *= 40;
return $percent;

}elseif($ctroop >= 1000){
$percent = $cloot / 100;
$percent *= 30;
return $percent;

}elseif($ctroop >= 500){
$percent = $cloot / 100;
$percent *= 20;
return $percent;
 
}elseif($ctroop >= 200){
$percent = $cloot / 100;
$percent *= 10;
return $percent;

}elseif($ctroop >= 50){
$percent = $cloot / 100;
$percent *= 5;
return $percent;

}elseif($ctroop >= 10){
$percent = $cloot / 100;
$percent *= 1;
return $percent;

}elseif($ctroop >= 0){
$percent = $cloot / 100;
$percent *= 0.5;
return $percent;

}
}
function get_user($fadmin,$name){
if(!file_exists("users/$fadmin/$name")){
file_put_contents("users/$fadmin/$name",null);
}
return file_get_contents("users/$fadmin/$name");
}
function chenge_user($fadmin,$name,$new){
file_put_contents("users/$fadmin/$name",$new);
}
function run($fadmin,$mode){
file_put_contents("users/$fadmin/run",$mode);
}
 
function checkuser($fadmin){
if(is_dir("users/$fadmin")){
return true;
}else{
return false;
}
}
function signup($fadmin){
mkdir("users/$fadmin");
mkdir("users/$fadmin/enemy");
mkdir("users/$fadmin/clan");
mkdir("users/$fadmin/revenge");
mkdir("clans/$name/config");
mkdir("clans");
mkdir("revenge");
mkdir("event");
mkdir("enemy");
mkdir("codes");
$time = time();
file_put_contents("users/$fadmin/id",$fadmin);
file_put_contents("users/$fadmin/gold",$cgolamount);
file_put_contents("users/$fadmin/wood",2500);
file_put_contents("users/$fadmin/food",2500);
file_put_contents("users/$fadmin/troop",500);
file_put_contents("users/$fadmin/xp",0);
file_put_contents("users/$fadmin/castel",1);
file_put_contents("users/$fadmin/name",$fadmin);
file_put_contents("users/$fadmin/timejoin",$time);
file_put_contents("users/$fadmin/shop",0);
file_put_contents("users/$fadmin/ban",0);
file_put_contents("users/$fadmin/lfarm",1);
file_put_contents("users/$fadmin/farm",0);
file_put_contents("users/$fadmin/cup",0);
file_put_contents("users/$fadmin/transh",0);
file_put_contents("users/$fadmin/run",0);
file_put_contents("users/$fadmin/tale",0);
file_put_contents("users/$fadmin/dead",0);
file_put_contents("users/$fadmin/tixp",0);
}
function ttooppclans($mode,$number){
 $saveusers = array();
  $usersscan = scandir("clans");
  unset($usersscan[0]);
  unset($usersscan[1]);
  foreach($usersscan as $savetojs){
$savedis = file_get_contents("clans/$savetojs/$mode");
$saveusers[$savetojs] = $savedis;
  }
  $rating = $saveusers;
    arsort($rating,SORT_NUMERIC); 
    $rate = array(); 
    foreach($rating as $key=>$value){ 
      $rate[] = $key; 
    } 
    return $rate[$number]; 
}

function ttoopp($mode,$number){
 $saveusers = array();
  $usersscan = scandir("users");
  unset($usersscan[0]);
  unset($usersscan[1]);
  foreach($usersscan as $savetojs){
$savedis = file_get_contents("users/$savetojs/$mode");
$saveusers[$savetojs] = $savedis;
  }
  $rating = $saveusers;
    arsort($rating,SORT_NUMERIC); 
    $rate = array(); 
    foreach($rating as $key=>$value){ 
      $rate[] = $key; 
    } 
    return $rate[$number]; 
}

function ttoopp2($mode,$number){

$save = array();
$scan1 = scandir("users");
unset($scan1[0]);
unset($scan1[1]);
foreach($scan1 as $users){
$save[] = array(file_get_contents("users/$users/$mode"),$users);
}
rsort($save);
return $save[$number][0];
}
 

function getrank($fadmin,$mode){ 
    
  $saveusers = array();
  $usersscan = scandir("users");
  unset($usersscan[0]);
  unset($usersscan[1]);
  foreach($usersscan as $savetojs){
$savedis = file_get_contents("users/$savetojs/$mode");
$saveusers[$savetojs] = $savedis;
  }
  $rating = $saveusers;
  if(isset($rating[$fadmin])){ 
    arsort($rating,SORT_NUMERIC); 
    $rate = array(); 
    foreach($rating as $key=>$value){ 
      $rate[] = $key; 
    } 
    $flipped = array_flip($rate); 
    return $flipped[$fadmin]+1; 
  }else{ 
    return false; 
  } 
}
function getpowerrank($rank,$mode){
  $saveusers = array();
  $usersscan = scandir("users");
  foreach($usersscan as $savetojs){
$savedis = file_get_contents("users/$savetojs/$mode");
$saveusers[$savetojs] = $savedis;
  }
  $rating = $saveusers;
  print_r($rating);
}
if(!is_dir('users')){
mkdir("users");
mkdir("clans/$name/config");
}
if(!is_dir('.run')){
mkdir(".run");
}
 
function getrankclan($fadmin,$mode){ 
    
  $saveusers = array();
  $usersscan = scandir("clans/$fadmin/users");
  unset($usersscan[0]);
  unset($usersscan[1]);
  foreach($usersscan as $savetojs){
$savedis = file_get_contents("clans/$fadmin/users/$savetojs/$mode");
$saveusers[$fadmin] += $savedis;
  }
  $rating = $saveusers;
    arsort($rating,SORT_NUMERIC); 
    $rate = array(); 
    foreach($rating as $key=>$value){ 
      $rate[] = $key; 
 
    $flipped = array_flip($rate); 
    return $flipped[$fadmin]+1; 
}

}
$gold = round(get_user($chat_id,'gold'));
$wood = round(get_user($chat_id,'wood'));
$food = round(get_user($chat_id,'food'));
$troop = round(get_user($chat_id,'troop'));
$xp = get_user($chat_id,'xp');
$name = get_user($chat_id,'name');
$shop = get_user($chat_id,'shop');
$timejoin = get_user($chat_id,'timejoin');
$lfarm = get_user($chat_id,'lfarm');
$lgold = get_user($chat_id,'lgold');
$farm = get_user($chat_id,'farm');
$run = get_user($chat_id,'run');
$transh = get_user($chat_id,'transh');
$ban = get_user($chat_id,'ban');
$cup = get_user($chat_id,'cup');
$tale = round(get_user($chat_id,'tale'));
$ptale = file_get_contents(".config/pricetale.php");
$dead = round(get_user($chat_id,'dead'));
function update_farm($fadmin){
$day = get_user($fadmin,'xp');
$lfarm = get_user($fadmin,'lfarm');
$gold = get_user($fadmin,'lfarm');
}
update_farm($fadmin);
update_level($fadmin);
if($tc=='supergroup' or $tc=='group'){QuBnM('LeaveChat',['chat_id'=>$chat_id]);}
 
if($tc != 'supergroup' or $tc != 'group'){
if((time() - filectime("users/$chat_id/spam.txt")) > 0.5){
file_put_contents("users/$chat_id/spam.txt",0);
$block = file_get_contents('blocklist.txt');
if(strpos($block, "$fadmin") !== false){
QuBnM('SendMessage',[
	'chat_id'=>$chat_id,
	'text'=>"‼ حساب شما مسدود شده است",
  'parse_mode'=>'MarkDown',
 	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
  'keyboard'=>[
	[['text'=>""]],
	]
	])
	]);
}
if((time() - filectime("tAmDid.txt")) >= 216000){
   $eshtrakfeli = file_get_contents("tAmDid.txt");
   $anjameshtralalan = $eshtrakfeli - 1;
   unlink("tAmDid.txt");
    file_put_contents("tAmDid.txt",$anjameshtralalan);
}
$eshtrakfeli = file_get_contents("tAmDid.txt");
if($eshtrakfeli <= 1){
    QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"💯 اشتراک یک ماهه این ربات تموم شده است جهت تمدید مجدد به رباتساز اِسلُو کریت مراجعه کنید 
@SlowCreateBot
",
]);
exit();
}
else{
if($MyCoIn1 == "/start" || $MyCoIn1 == $back){
run($fadmin,"no");
if(checkuser($fadmin) != true){
signup($fadmin);
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
$starttxt
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"$clashd1"],['text'=>"$clashd2"]
                ],
                [
                ['text'=>"$clashd3"],['text'=>"$clashd4"],['text'=>"$clashd5"]
                ],
                [
                ['text'=>"$clashd6"],['text'=>"$clashd7"]
                ],
                [
                ['text'=>"$clashd8"],['text'=>"$clashd9"]
                ],
                [
                ['text'=>"$clashdroz"],['text'=>"$clashdgift"]
                ],                
                [
                ['text'=>"$clashd11"]
                ]
              ],
])
]);
}else{
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
$starttxt
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                     [
                ['text'=>"$clashd1"],['text'=>"$clashd2"]
                ],
                [
                ['text'=>"$clashd3"],['text'=>"$clashd4"],['text'=>"$clashd5"]
                ],
                [
                ['text'=>"$clashd6"],['text'=>"$clashd7"]
                ],
                [
                ['text'=>"$clashd8"],['text'=>"$clashd9"]
                ],
                [
                ['text'=>"$clashdroz"],['text'=>"$clashdgift"]
                ],    
                [
                ['text'=>"$clashd11"]
                ]
              ],
])
]);
 }
 
file_put_contents("spam/$fadmin.txt",0);
}
if($MyCoIn1 == "$clashd1"){
$mohajem1 = ttoopp('dead',0);
$mohajem2 = ttoopp('dead',1);
$mohajem3 = ttoopp('dead',2);

$ratemohajem = getrank($fadmin,'dead');

$goldttoopp = ttoopp('gold',0);
$goldttoopp2 = ttoopp('gold',1);
$goldttoopp3 = ttoopp('gold',2);

$rategold = getrank($fadmin,'gold');

$troopttoopp = ttoopp('troop',0);
$troopttoopp2 = ttoopp('troop',1);
$troopttoopp3 = ttoopp('troop',2);

$ratetroop = getrank($fadmin,'troop');

$cupttoopp = ttoopp('cup',0);
$cupttoopp2 = ttoopp('cup',1);
$cupttoopp3 = ttoopp('cup',2);

$ratecup = getrank($fadmin,'cup');

$topclan = ttooppclans('users',0);
$topclan2 = ttooppclans('users',1);
$topclan3 = ttooppclans('users',2);

$claninfoo = file_get_contents("users/$chat_id/joinclan");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
اتحاد شما : $claninfoo
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
برترین مهاجم⚔
رتبه 1 : [$mohajem1](tg://user?id=$mohajem1)
رتبه 2 : [$mohajem2](tg://user?id=$mohajem2)
رتبه 3 : [$mohajem3](tg://user?id=$mohajem3)

رتبه شما : $ratemohajem
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
فرمانده ی کینگدام🎃 
رتبه 1 : [$troopttoopp](tg://user?id=$troopttoopp)
رتبه 2 : [$troopttoopp2](tg://user?id=$troopttoopp2)
رتبه 3 : [$troopttoopp3](tg://user?id=$troopttoopp3)

رتبه شما : $ratetroop
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
بالاترین کاپ🏆
رتبه 1 : [$cupttoopp](tg://user?id=$cupttoopp)
رتبه 2 : [$cupttoopp2](tg://user?id=$cupttoopp2)
رتبه 3 : [$cupttoopp3](tg://user?id=$cupttoopp3)

رتبه شما : $ratecup

", 
'parse_mode'=>'MARKDOWN',
]);
}
 
    $pack0 = 5 * 10;
    $pack1 = 1 * 1;

if($MyCoIn1 =="$clashd3"){
$truechannel = json_decode(file_get_contents("https://api.telegram.org/bot[*[TOKEN]*]/getChatMember?chat_id=@$channels&user_id=$chat_id"));
$tch25 = $truechannel->result->status;
if($tch25 != 'member' && $tch25 != 'creator' && $tch25 != 'administrator'){
    	QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"💎کاربرگرامی،
برای حمایت از ما و بازشدن قفل ربات لطفا در کانال های ما عضو شوید👇

ID : @$channels 🔑

سپس مجدد ربات را با دستور
/start",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "💎عضویت در کانال🔑",'url'=>"https://t.me/$channels"]
                    ]
                ]
            ])
]);
}else{
    	QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
ایجاد 5 سرباز💂
قیمت💵 : $pack0 گندم

ایجاد 1000 سرباز💂
قیمت : 1 الماس

الماس ها💎 : $gold
گندم ها🌾 : $food
سرباز ها💂‍♀ : $troop
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "خرید با گندم🌾",'callback_data'=>"pack"]
                    ],[
['text' => "وارد کردن دستی👾",'callback_data'=>"entertroop"]
                    ],[
['text' => "خرید 1000 سرباز💎",'callback_data'=>"pack2"]
                    ],[
['text' => "خرید 10000 سرباز💎",'callback_data'=>"pack3"]
                    ],[
['text' => "خرید 100000 سرباز💎",'callback_data'=>"pack4"]
                    ]
                ]
            ])
]);
}}
if($MyCoIn1 == 'pack'){
    file_put_contents(".run/".$chat_id."mi.txt",$message_id2); 
    if($food >= $pack0){
    $troop += 5;
    $food -= $pack0;
    $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
    chenge_user($chat_id,'food',$food);
    chenge_user($chat_id,'troop',$troop);
     QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
ایجاد 5 سرباز💂
قیمت💵 : $pack0 گندم
گندم ها🌾 : $food
سرباز ها💂‍♀ : $troop
",
            'reply_markup' => json_encode([
                  'inline_keyboard' => [
                    [
['text' => "خرید با گندم🌾",'callback_data'=>"pack"]
                    ],[
['text' => "وارد کردن دستی👾",'callback_data'=>"entertroop"]
                    ],[
['text' => "خرید 1000 سرباز💎",'callback_data'=>"pack2"]
                    ],[
['text' => "خرید 10000 سرباز💎",'callback_data'=>"pack3"]
                    ],[
['text' => "خرید 100000 سرباز💎",'callback_data'=>"pack4"]
                    ]
                ]
            ])
]);
 
    }else{
        $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
منابع کافی نیست ?
",
            ]);
    }
}
if($MyCoIn1 == 'pack2'){
    file_put_contents(".run/".$chat_id."mi.txt",$message_id2); 
    if($gold >= $pack1){
    $troop += 1000;
    $gold -= $pack1;
    $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
    chenge_user($chat_id,'gold',$gold);
    chenge_user($chat_id,'troop',$troop);
     QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
ایجاد 1000 سرباز💂
قیمت : 1 الماس
الماس ها💎 : $gold
سرباز ها💂‍♀ : $troop
",
            'reply_markup' => json_encode([
                  'inline_keyboard' => [
                    [
['text' => "خرید با گندم🌾",'callback_data'=>"pack"]
                    ],[
['text' => "وارد کردن دستی👾",'callback_data'=>"entertroop"]
                    ],[
['text' => "خرید 1000 سرباز💎",'callback_data'=>"pack2"]
                    ],[
['text' => "خرید 10000 سرباز💎",'callback_data'=>"pack3"]
                    ],[
['text' => "خرید 100000 سرباز💎",'callback_data'=>"pack4"]
                    ]
                ]
            ])
]);
 
    }else{
        $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
الماس کافی نیست ?
",
            ]);
    }
}
if($MyCoIn1 == 'pack3'){
    file_put_contents(".run/".$chat_id."mi.txt",$message_id2); 
    if($gold >= $pack1){
    $troop += 10000;
    $gold -= 10;
    $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
    chenge_user($chat_id,'gold',$gold);
    chenge_user($chat_id,'troop',$troop);
     QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
ایجاد 10000 سرباز💂
قیمت : 10 الماس
الماس ها💎 : $gold
سرباز ها💂‍♀ : $troop
",
            'reply_markup' => json_encode([
                  'inline_keyboard' => [
                    [
['text' => "خرید با گندم🌾",'callback_data'=>"pack"]
                    ],[
['text' => "وارد کردن دستی👾",'callback_data'=>"entertroop"]
                    ],[
['text' => "خرید 1000 سرباز💎",'callback_data'=>"pack2"]
                    ],[
['text' => "خرید 10000 سرباز💎",'callback_data'=>"pack3"]
                    ],[
['text' => "خرید 100000 سرباز💎",'callback_data'=>"pack4"]
                    ]
                ]
            ])
]);
 
    }else{
        $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
الماس کافی نیست ?
",
            ]);
    }
}
if($MyCoIn1 == 'pack4'){
    file_put_contents(".run/".$chat_id."mi.txt",$message_id2); 
    if($gold >= $pack1){
    $troop += 100000;
    $gold -= 100;
    $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
    chenge_user($chat_id,'gold',$gold);
    chenge_user($chat_id,'troop',$troop);
     QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
ایجاد 100000 سرباز💂
قیمت : 100 الماس
الماس ها💎 : $gold
سرباز ها💂‍♀ : $troop
",
            'reply_markup' => json_encode([
                  'inline_keyboard' => [
                    [
['text' => "خرید با گندم🌾",'callback_data'=>"pack"]
                    ],[
['text' => "وارد کردن دستی👾",'callback_data'=>"entertroop"]
                    ],[
['text' => "خرید 1000 سرباز💎",'callback_data'=>"pack2"]
                    ],[
['text' => "خرید 10000 سرباز💎",'callback_data'=>"pack3"]
                    ],[
['text' => "خرید 100000 سرباز💎",'callback_data'=>"pack4"]
                    ]
                ]
            ])
]);
 
    }else{
        $mi = file_get_contents(".run/".$chat_id."mi.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
الماس کافی نیست ?
",
            ]);
    }
}
if($MyCoIn1 == "$clashd11" and $MyCoIn1 != $back){
run($fadmin,"kodom");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
💳آیدی عددی شخص دریافت کننده الماس را ارسال نمایید
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($run == "kodom" and $MyCoIn1 != $back){
if(is_dir("users/$MyCoIn1")){
file_put_contents("users/$chat_id/kodom.txt","$MyCoIn1");
run($fadmin,"ine");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
میزان الماس را جهت انتقال ارسال نمایید
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
❌ این کاربر عضو ربات نمی باشد
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($run == "ine" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
if($gold >= $MyCoIn1){
if($MyCoIn1 >= $cminmove){
$kodom = file_get_contents("users/$chat_id/kodom.txt");
$gold = round(get_user($chat_id,'gold'));
$gold1 = round(get_user($kodom,'gold'));
$kamas = $gold1 +$MyCoIn1;
file_put_contents("users/$kodom/gold","$kamas");
$kame = $gold -$MyCoIn1;
file_put_contents("users/$chat_id/gold","$kame");
file_put_contents("users/$chat_id/state.txt","none");
file_put_contents("users/$chat_id/kodom.txt","none");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
با موفقیت انتقال یافت
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
QuBnM('sendMessage',[ 
    'chat_id'=>$kodom, 
    'text'=>" 
کاربر گرامی
ار طرف کاربر با آیدی $chat_id میزان $MyCoIn1 الماس به جساب شما انتقال یافت
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
file_put_contents("users/$chat_id/state.txt","none");
file_put_contents("users/$chat_id/kodom.txt","none");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"حداقل امتیاز قابل انتقال $cminmove امتیاز می باشد
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
}else{
file_put_contents("users/$chat_id/state.txt","none");
file_put_contents("users/$chat_id/kodom.txt","none");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"الماس شما کافی نمی باشد
    
الماس فعلی شما : $gold
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
}else{
file_put_contents("users/$chat_id/state.txt","none");
file_put_contents("users/$chat_id/kodom.txt","none");
QuBnM('sendMessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط از عدد لاتین استفاده نمایید", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[[['text'=>$back]]],
])]);}}
/////////////////////////////////
if($MyCoIn1 == "$clashdgift" and $MyCoIn1 != $back){
run($fadmin,"kodomgif");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
💌کد هدیه را ارسال نمایید
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($run == "kodomgif" and $MyCoIn1 != $back){
$giftloc = file_get_contents("codes/$MyCoIn1.txt");
if(file_exists("codes/$MyCoIn1.txt")){
$giftloc = file_get_contents("codes/$MyCoIn1.txt");
$gold = get_user($chat_id,'gold');
$gold += $giftloc;
chenge_user($chat_id,'gold',$gold);
run($fadmin,"none");
unlink("codes/$MyCoIn1.txt");
 QuBnM('sendmessage',[ 
    'chat_id'=>"@$channels", 
    'text'=>" 
💌کد هدیه استفاده شد

☑️ کد : $MyCoIn1

🕴🏻 دریافت کننده : $chat_id
",
]);
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
😻🎈کد شما درست بود

$giftloc الماس به حساب شما افزوده شد
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
❌ کد نامعتبر و یا قبلا استفاده شده
",
'parse_mode'=>'MARKDOWN',
]);
}
}
////////////////////////////
if($MyCoIn1 == "$clashd4"){
$startfarm = get_user($chat_id,'farm');
if($startfarm == 0){
$speedfarm = farm($lfarm,'speed');
$diskfarm = farm($lfarm,'max');

QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
سطح مزرعه 🏡 : $lfarm
سرعت تولید⚡️ : X$speedfarm
فضا 🌕 : $diskfarm

گندم  🌱 : $food
چوب 🌲: $wood

",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "🏭شروع تولید🏭",'callback_data'=>"startfarm"]
                    ],
                    [
['text' => "⚡️ارتقا سطح مزرعه⚡️",'callback_data'=>"upgradefarm"]
                    ]
                ]
            ])
]);
}else{
$time = time();
$timefarm = get_user($chat_id,'farm');
$infarn = $time - $timefarm;
$speedfarm = farm($lfarm,'speed');
$diskfarm = farm($lfarm,'max');
$in_farm = $infarn * $speedfarm;
if($timefarm == 0){
$infarm = 0;
}else
if($in_farm > $diskfarm){
$infarm = $diskfarm;
}else{
$infarm = $in_farm;
}

QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
گندم  🌱 : $food
چوب 🌲: $wood

تولید فعلی گندم و چوب : $infarm 🌱
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text'=>"🔅بروزرسانی اطلاعات🔅",'callback_data'=>"updatefarm"]
                    ],
                    [
['text'=>"☑️برداشت☑️",'callback_data'=>"getfarm"]
                    ]
                ]
            ])
]);
}
}

if($MyCoIn1 == "upgradefarm"){
if($gold >= 10){
file_put_contents(".run/".$chat_id."mif.txt",$message_id2); 
$mi = file_get_contents(".run/".$chat_id."mif.txt");
$time = time();
$timefarm = get_user($chat_id,'farm');
$infarn = $time - $timefarm;
$speedfarm = farm($lfarm,'speed');
$diskfarm = farm($lfarm,'max');
$in_farm = $infarn * $speedfarm;
chenge_user($chat_id,'farm',0);
$gold -= 10;
$lfarm += 1;
chenge_user($chat_id,'gold',$gold);
chenge_user($chat_id,'lfarm',$lfarm);
QuBnM('editmessagetext',[ 
    'chat_id'=>$chat_id, 
   'message_id'=>$mi, 
    'text'=>" 
از حساب شما 10 الماس کسر شد
سطح مزرعه شما : $lfarm
",
]);
}
    if($gold <= 10){
    }else{
        $mit = file_get_contents(".run/".$chat_id."mit.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mi, 
    'text'=>" 
از حساب شما 10 الماس کسر شد
سطح مزرعه شما : $lfarm
",
            ]);
    }
}
if($MyCoIn1 == "startfarm"){
$time = time();
chenge_user($chat_id,'farm',$time);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
   'message_id'=>$mi, 
    'text'=>" 
مزرعه درحال تولید میباشد 🌱
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text'=>"🔅بروزرسانی اطلاعات🔅",'callback_data'=>"updatefarm"]
                    ]
                ]
            ])
]);
}
if($MyCoIn1 == "updatefarm"){
file_put_contents(".run/".$chat_id."mif.txt",$message_id2); 
$mi = file_get_contents(".run/".$chat_id."mif.txt");
$time = time();
$timefarm = get_user($chat_id,'farm');
$infarn = $time - $timefarm;
$speedfarm = farm($lfarm,'speed');
$diskfarm = farm($lfarm,'max');
$in_farm = $infarn * $speedfarm;
if($timefarm == 0){
$infarm = 0;
}else
if($in_farm > $diskfarm){
$infarm = $diskfarm;
}else{
$infarm = $in_farm;
}

QuBnM('editmessagetext',[ 
    'chat_id'=>$chat_id, 
   'message_id'=>$mi, 
    'text'=>" 
گندم  🌱 : $food
چوب 🌲: $wood

تولید فعلی گندم و چوب : $infarm 🌱",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text'=>"🔅بروزرسانی اطلاعات🔅",'callback_data'=>"updatefarm"]
                    ],
                    [
['text'=>"☑️برداشت☑️",'callback_data'=>"getfarm"]
                    ]
                ]
            ])
]);
}
 
if($MyCoIn1 == "getfarm"){
file_put_contents(".run/".$chat_id."mif.txt",$message_id2); 
$mi = file_get_contents(".run/".$chat_id."mif.txt");
$time = time();
$timefarm = get_user($chat_id,'farm');
$infarn = $time - $timefarm;
$speedfarm = farm($lfarm,'speed');
$diskfarm = farm($lfarm,'max');
$in_farm = $infarn * $speedfarm;
if($timefarm == 0){
$infarm = 0;
}else
if($in_farm > $diskfarm){
$infarm = $diskfarm;
}else{
$infarm = $in_farm;
}
chenge_user($chat_id,'farm',0);
$food += $infarm;
$wood += $infarm;
chenge_user($chat_id,'wood',$wood);
chenge_user($chat_id,'food',$food);
QuBnM('editmessagetext',[ 
    'chat_id'=>$chat_id, 
   'message_id'=>$mi, 
    'text'=>" 
مقدار $infarm چوب و گندم دریافت شد 🌱
",
]);
}
if($MyCoIn1 == "$clashd5"){
    if($viewbot == "off"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 🗡 بخش حمله بصورت موقت خاموش میباشد. ",
        'parse_mode'=>'MarkDown',
          'reply_markup'=>$menu1
  ]);
    exit();
}
mkdir("users/$fadmin/enemy");
mkdir("clans/$name/config");
mkdir("clans");
mkdir("revenge");
mkdir("event");
mkdir("enemy");
mkdir("codes");
file_put_contents("spam/$fadmin.txt",file_get_contents("spam/$fadmin.txt") + 1);
if(file_get_contents("spam/$fadmin.txt") <= 1){
if((time() - filectime("users/$chat_id/zam.txt")) > $cztime){
$scandir = scandir("users/$chat_id/enemy");
    unset($scandir[0]);  unset($scandir[1]);
    $count = count($scandir) + 1;
    $rand = rand(2,$count);
    $enemy = $scandir[$rand];
    $troopenemy = round(get_user($enemy,'troop'));
    file_put_contents("users/$chat_id/enemy.txt",$enemy);

$joinclanen = get_user($enemy,"joinclan");
  $enfood = get_user($enemy,'food');
  $enwood = get_user($enemy,'wood');
  $GLfood = round(Get_Loot($troop,$enfood));
  $GLwood = round(Get_Loot($troop,$enwood));
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
دشمن : [$enemy](tg://user?id=$enemy)
💂🏻️ تعداد سرباز ها : 𝕙𝕚𝕕𝕕𝕖𝕟
🌱 گندم : $GLfood
🌲 چوب : $GLwood
🛡 نام اتحاد : $joinclanen
",
'parse_mode'=>'MARKDOWN',
 'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text'=>"⚔️حمله⚔️",'callback_data'=>"attack"]],]])]);
}else{
$x = time() - filectime("users/$chat_id/zam.txt");
$zam = $cztime - $x;
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"❗️شما تا $zam ثانیه دیگر نمیتوانید حمله کنید",
'parse_mode'=>'MARKDOWN',
]);
 }}file_put_contents("spam/$fadmin.txt",0);
}
$enemymy = file_get_contents("users/$chat_id/enemy.txt");
if($MyCoIn1 == "attack" and $enemymy != 'no'){
file_put_contents("users/$chat_id/zam.txt",'');
rmdir("users/$chat_id/revenge/$enemymy");
$troopanemy = get_user($enemymy,'troop');
$taleanemy = get_user($enemymy,'tale');
file_put_contents("users/$chat_id/enemy.txt",'no');
if($taleanemy >= $troop){
$taleanemy -= $troop;
chenge_user($enemymy,'troop',$taleanemy);
chenge_user($chat_id,'troop',0);
$cup -= 5;
chenge_user($chat_id,'cup',$cup);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
🧱 دشمن تله زیادی داشت ما باختیم
🏆 کاپ 5-
",
'parse_mode'=>'MARKDOWN',
]);
$encup = get_user($enemymy,'cup');
$encup += 5;
chenge_user($enemymy,'cup',$encup);
QuBnM('sendmessage',[ 
    'chat_id'=>$enemymy, 
    'text'=>" 
کاربر [$chat_id](tg://user?id=$chat_id) به ما حمله کرد💀
 اما نتوانست به دهکده نفوذ کند و تله ها تمام سربازان دشمن را کشت!💀
تله های باقیمانده  $taleanemy
تلفات تله ها $troop
🏆 کاپ 5+",
'parse_mode'=>'MARKDOWN',
]);
}else{
$troop -= $taleanemy;
 
chenge_user($chat_id,'troop',$troop);
chenge_user($enemymy,'tale',0);

$cup += 5;
chenge_user($chat_id,'cup',$cup);

QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
💣از تله ها گذر کردیم💣
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
تعداد باقیمانده سرباز ها  $troop
تلفات  $taleanemy
🏆 کاپ 5+
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
",
'parse_mode'=>'MARKDOWN',
]);
$encup = get_user($enemymy,'cup');
$encup -= 5;
chenge_user($enemymy,'cup',$encup);
QuBnM('sendmessage',[ 
    'chat_id'=>$enemymy, 
    'text'=>" 
کاربر [$chat_id](tg://user?id=$chat_id) به ما حمله کرد💀
 انها توانستند از تله ها گذر کرده و تمام تله هارو خنسی کنند!💀
🏆 کاپ 5-
",
'parse_mode'=>'MARKDOWN',
]);
}

if($troopanemy >= $troop){
$troopanemy -= $troop;
chenge_user($enemymy,'troop',$troopanemy);
chenge_user($chat_id,'troop',0);

$dead += $troopanemy;
chenge_user($chat_id,"dead",$dead);

$endead = get_user($enemymy,'dead');
$endead += $troop;
chenge_user($enemymy,"dead",$endead);

$cup -= 5;
chenge_user($chat_id,'cup',$cup);

QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
دشمن سرباز زیادی داشت ما باختیم😥
🏆 کاپ 5-
",
'parse_mode'=>'MARKDOWN',
]);
$encup = get_user($enemymy,'cup');
$encup += 5;
chenge_user($enemymy,'cup',$encup);
QuBnM('sendmessage',[ 
    'chat_id'=>$enemymy, 
    'text'=>" 
دشمن نتوانست به دهکده نفوذ کند و سرباز ها تمام سربازان دشمن را کشتن!💀
سرباز های باقیمانده  $troopanemy
تلفات $troop
+ 5 کاپ🏆
",
'parse_mode'=>'MARKDOWN',
]);
}else{
$troop -= $troopanemy;
 
$dead += $troopanemy;
chenge_user($chat_id,"dead",$dead);

$endead = get_user($enemymy,'dead');
$endead += $troop;
chenge_user($enemymy,"dead",$endead);

chenge_user($chat_id,'troop',$troop);
chenge_user($enemymy,'troop',0);

$encup = get_user($enemymy,'cup');

$enfood = get_user($enemymy,'food');
$enwood = get_user($enemymy,'wood');

$GLfood = round(Get_Loot($troop,$enfood));
$GLwood = round(Get_Loot($troop,$enwood));

$forenemeywood = $enwood - $GLwood;
chenge_user($enemymy,'wood',$forenemeywood);
$forenemeyfood = $enfood - $GLfood;
chenge_user($enemymy,'food',$forenemeyfood);

$forwinnerwood = $wood + $GLwood;
chenge_user($chat_id,'wood',$forwinnerwood);
$forwinnerfood = $food + $GLfood;
chenge_user($chat_id,'food',$forwinnerfood);

$encup -= 10;
chenge_user($enemymy,'cup',$encup);
$cup += 10;
chenge_user($chat_id,'cup',$cup);
rmdir("users/$chat_id/enemy/$enemymy");
$giftfood = gift_loot($chat_id);

$wood += $giftfood;
chenge_user($chat_id,'wood',$wood);
$food += $giftfood;
chenge_user($chat_id,'food',$food);

QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
🗡از سرباز ها گذر کردیم🗡
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
تعداد باقیمانده سرباز ها : $troop
☠️تلفات : $troopanemy
💈منابع جمع اوری شده💈
🌾 گندم : $GLfood
🌲 چوب : $GLwood
🎁 جایزه کاپ : $giftfood چوب و گندم
🏆 کاپ 10+
┄┅┄┅┄┄┅┄┅┄┄┅┄┅┄
",
'parse_mode'=>'MARKDOWN',
]);
$joinclanenemy = get_user($enemymy,"joinclan");
$scanclanen = scandir("clans/$joinclanenemy/users");
unset($scanglobal[0]); unset($scanglobal[1]);
foreach($scanclanen as $sendforall){

if(!is_dir("users/$sendforall/revenge")){
mkdir("users/$sendforall/revenge");
}
mkdir("users/$sendforall/revenge/$chat_id");
}
$scanglobalclanen = scandir("clans/$joinclanenemy/global");
foreach($scanglobalclanen as $sendforall){
QuBnM('sendmessage',[ 
    'chat_id'=>$sendforall, 
    'text'=>"
به کاربر [$enemymy](tg://user?id=$enemymy) توسط [$chat_id](tg://user?id=$chat_id) حمله شد👹
وقت انتقام گیریه💀
",
'parse_mode'=>'MARKDOWN',
]);
}
 
QuBnM('sendmessage',[ 
    'chat_id'=>$enemymy, 
    'text'=>"🛑🔊🔊🛑🔊🔊🛑
دشمن توانست به دهکده نفوذ کند و سرباز های ما همه کشته شدند
┄┅┄┄┅┄┅┄┄┅┄
💀 منابع از دست رفته :
🌾 گندم : $GLfood
🌲 چوب : $GLwood
🏆 کاپ 10-
┄┅┄┄┅┄┅┄┄┅┄
",

'parse_mode'=>'MARKDOWN',
]);
}


}
if($MyCoIn1 == "$clashd2"){
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
$shoptxt
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"💎 خرید الماس 💎"],['text'=>'🛄تبدیل الماس🛄']
                ],
                [
                ['text'=>'🔅الماس رایگان🔅'],['text'=>$back]
                ]
              ],
])
]);
}
if($MyCoIn1 =="💎 خرید الماس 💎"){
    	QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" $goldtxt
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "$cplan1",'url'=>"$cshoplink"]
                                        ],
                    [
['text' => "$cplan2",'url'=>"$cshoplink"]
                    ],
                    [
['text' => "$cplan3",'url'=>"$cshoplink"]
                    ],
                    [
['text' => "پشتیبانی",'url'=>"$cshoplink"]
                    ]

                ]
            ])
]);
}
if($MyCoIn1 =="$clashd7"){
$truechannel = json_decode(file_get_contents("https://api.telegram.org/bot[*[TOKEN]*]/getChatMember?chat_id=@$channels&user_id=$chat_id"));
$tch25 = $truechannel->result->status;
if($tch25 != 'member' && $tch25 != 'creator' && $tch25 != 'administrator'){
    	QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"💎کاربرگرامی،
برای حمایت از ما و بازشدن قفل ربات لطفا در کانال های ما عضو شوید👇

ID : @$channels 🔑

سپس مجدد ربات را با دستور
/start",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "💎عضویت در کانال🔑",'url'=>"https://t.me/$channels"]
                    ]
                ]
            ])
]);
}else{
    	QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
ایجاد 10 تله💣
قیمت💵 : $ptale چوب
چوب ها🌲 : $wood
تله ها💣 : $tale
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "💣ایجاد💣",'callback_data'=>"ct"]
                    ],
                    [
['text' => "☑️ایجاد دستی☑️",'callback_data'=>"customtale"]
                    ]

                ]
            ])
]);
}}
if($MyCoIn1 == "/creator"){
if(file_exists("../../SlowCreate/UsErBoT/[*[USERNAME]*]/creator.txt")){
$creator = file_get_contents("../../SlowCreate/UsErBoT/[*[USERNAME]*]/creator.txt");
}
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"$creator",
'parse_mode'=>'html',
]);
}
if($MyCoIn1 == 'ct'){
    file_put_contents(".run/".$chat_id."mit.txt",$message_id2); 
    if($wood >= $ptale){
    $tale += 10;
    $wood -= $ptale;
    $mit = file_get_contents(".run/".$chat_id."mit.txt"); 
    chenge_user($chat_id,'wood',$wood);
    chenge_user($chat_id,'tale',$tale);
     QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mit, 
    'text'=>" 
ایجاد 10 تله💣
قیمت💵 : $ptale چوب
چوب ها🌲 : $wood
تله ها💣 : $tale
",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text' => "💣ایجاد💣",'callback_data'=>"ct"]
                    ],
                    [
['text' => "☑️ایجاد دستی☑️",'callback_data'=>"customtale"]
                    ]
                ]
            ])
]);
    }else{
        $mit = file_get_contents(".run/".$chat_id."mit.txt"); 
      QuBnM('editMessagetext',[ 
    'chat_id'=>$chat_id, 
    'message_id'=>$mit, 
    'text'=>" 
منابع کافی نیست 😭
",
            ]);
    }
}
if($MyCoIn1 == "🛄تبدیل الماس🛄"){

QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد الماس های شما : $gold 💎
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"تبدیل به گندم🌾"],['text'=>'تبدیل به چوب🌲']
                ],
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($MyCoIn1 == "تبدیل به گندم🌾"){
file_put_contents("users/$fadmin/convert.txt","food");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
پک 1 
10 الماس برای 100000 گندم

پک 2
50 الماس برای 500000 گندم

پک 3
100 الماس برای 1000000 گندم

پک 4
200 الماس برای 2000000 گندم
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"پک 1"],['text'=>'پک 2']
                ],
                [
                ['text'=>"پک 3"],['text'=>'پک 4']
                ],
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
 
if($MyCoIn1 == "تبدیل به چوب🌲"){
file_put_contents("users/$fadmin/convert.txt","wood");
QuBnM('sendMessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
پک 1 
10 الماس برای 100000 چوب

پک 2
50 الماس برای 500000 چوب

پک 3
100 الماس برای 1000000 چوب

پک 4
200 الماس برای 140000 چوب
", 
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"پک 1"],['text'=>'پک 2']
                ],
                [
                ['text'=>"پک 3"],['text'=>'پک 4']
                ],
                [
                ['text'=>$back]
                ]
              ],
])
]);
}

$converttype = file_get_contents("users/$fadmin/convert.txt");
if($converttype == 'food'){
if($MyCoIn1 == "پک 1"){
$cg = conver_gold($chat_id,'food',1);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "پک 2"){
$cg = conver_gold($chat_id,'food',2);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "پک 3"){
$cg = conver_gold($chat_id,'food',3);
if($cg == true){
QuBnM('sendmessage',[ 

    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
 
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",

'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "پک 4"){
$cg = conver_gold($chat_id,'food',4);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
}
if($converttype == 'wood'){

if($MyCoIn1 == "پک 1"){
$cg = conver_gold($chat_id,'wood',1);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
 
if($MyCoIn1 == "پک 2"){
$cg = conver_gold($chat_id,'wood',2);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "پک 3"){
$cg = conver_gold($chat_id,'wood',3);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "پک 4"){
$cg = conver_gold($chat_id,'wood',4);
if($cg == true){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
خرید موفقیت امیز بود👼
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
الماس ها کافی نیست👼
",
'parse_mode'=>'MARKDOWN',
]);
}
}
}
if($MyCoIn1 == "🔅الماس رایگان🔅"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" $freegoldtxt

T.me/[*[USERNAME]*]?start=$chat_id",
'parse_mode'=>'html',
]);
}
if(isset($idzormajmoe) and is_dir("users/$idzormajmoe")){
if(!is_dir("users/$fadmin")){
$gold = get_user($idzormajmoe,'gold');
$gold += $gemamount;
chenge_user($idzormajmoe,'gold',$gold);
QuBnM('sendmessage',[ 
    'chat_id'=>$idzormajmoe, 
    'text'=>" 
⚡️ یک نفر با لینک شما وارد ربات شد

💎🔥$gemamount الماس به حساب شما واریز شد
",
'parse_mode'=>'MARKDOWN',
]);
mkdir("users/$fadmin");
mkdir("clans");
signup($fadmin);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
$starttxt
💎 لطفا مجدد ربات  را با دستور 
/start
استارت نمایید
",
'parse_mode'=>'MARKDOWN',
]);
}
}

if($chat_id == $cadmin1 || $chat_id == $cadmin2 || $chat_id == $cadmin3){
if($MyCoIn1 == "/panel" || $MyCoIn1 == "p"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
💎 به منوی مدیریت خوش آمدید
",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 == "مدیریت" || $MyCoIn1 == "💎مدیریت💎"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
💎 به منوی مدیریت خوش آمدید
",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 =="👤 ادمین ها"){
if($chat_id == $cadmin1){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
به پنل مدیریت ادمین ها خوش آمدید در این قسمت میتوانید ادمین دوم و سوم را مشخص نمایید و یا ویرایش کنید
",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
    [['text'=>"1️⃣ ادمین اول", 'callback_data'=> 'admin1s'],['text'=>"2️⃣ ادمین دوم",'callback_data'=> 'admin2s']],
],
])
]);
}}
if($MyCoIn1 == "/start" || $MyCoIn1 == "بازگشت🏠"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ شما ادمین این ربات هستید.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"$clashd1"],['text'=>"$clashd2"]
                ],
                [
                ['text'=>"$clashd3"],['text'=>"$clashd4"],['text'=>"$clashd5"]
                ],
                [
                ['text'=>"$clashd6"],['text'=>"$clashd7"]
                ],
                [
                ['text'=>"$clashd8"],['text'=>"$clashd9"]
                ],
                [
                ['text'=>"$clashdroz"],['text'=>"$clashdgift"]
                ],    
                [
                ['text'=>"$clashd11"]
                ],
                [
                ['text'=>"💎مدیریت💎"]
                ]
              ],
])
]);
}
////////////-------sinza////////
if($MyCoIn1 == "➰ دکمه ها"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
⚒به قسمت تنظیم دکمه خوش آمدید
",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
    [['text'=>"$clashd1", 'callback_data'=> 'ozvch'],['text'=>"$clashd2",'callback_data'=> 'dok2s']],
    [['text'=>"$clashd3", 'callback_data'=> 'dok3s'],['text'=>"$clashd4", 'callback_data'=> 'dok4s'],['text'=>"$clashd5",'callback_data'=> 'dok5s']],
    [['text'=>"$clashd6", 'callback_data'=> 'dok6s'],['text'=>"$clashd7",'callback_data'=> 'dok7s']],
    [['text'=>"$clashd9", 'callback_data'=> 'dok9s'],['text'=>"$clashd8",'callback_data'=> 'dok8s']],
    [['text'=>"$clashdroz", 'callback_data'=> 'almsrozanasba'],['text'=>"$clashdgift",'callback_data'=> 'clashdgift']],
    [['text'=>"$clashd11", 'callback_data'=> 'dok11s']],
],
])
]);
}
if($MyCoIn1 == "💢خاموش روشن حمله"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
⭕️به بخش خاموش و روشن بخش حمله خوش اومدید.
",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
    [['text'=>"خاموش", 'callback_data'=> 'views'],['text'=>"روشن",'callback_data'=> 'viewson']],
],
])
]);
}
////////////////hm-------------/////////-------------////////////demon-------------///
if($MyCoIn1 == "🗂 تنظیم متن"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
⚙ به قسمت تنظیم متن خوش آمدید
",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$message_id,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
    [['text'=>"متن استارت", 'callback_data'=> 'mtstart'],['text'=>"متن الماس رایگان",'callback_data'=> 'mtalms']],
    [['text'=>"متن فروشگاه", 'callback_data'=> 'mtshopss'],['text'=>"متن خرید الماس",'callback_data'=> 'mtkharid']],
    [['text'=>"الماس زیرمجموعه", 'callback_data'=> 'almszir'],['text'=>"الماس ورودی",'callback_data'=> 'almswell']],
    [['text'=>"الماس روزانه", 'callback_data'=> 'almsroz']],
],
])
]);
}
////////////////-------------/////////-------------////////////-------------///
if($MyCoIn1 == "🛍 تنظیم فروشگاه" || $MyCoIn1 == "تنظیم فروشگاه"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
🛒 به قسمت تنظیم فروشگاه خوش آمدید
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"پلان خرید الماس اول"],['text'=>"پلان خرید الماس دوم"]
                ],
                [
                ['text'=>"پلان خرید الماس سوم"]
                ],
                [
                ['text'=>"نصب درگاه پرداخت"]
                ],
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
////////////////-------------/////////-------------////////////-------------///
if($MyCoIn1 == "🚫 بلاک کردن"){
run($fadmin,"ban");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"😡 ایدی عددی کاربر موردنظر رو بفرست :",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}
if($run == "ban" and $MyCoIn1 != $back){
 $f2 = fopen("blocklist.txt", "a") or die("Unable to open file!");
 fwrite($f2,"$MyCoIn1\n");
 fclose($f2);
 run($fadmin,"none");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت مسدود شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}

if($MyCoIn1 == "♻️ انبلاک کردن"){
run($fadmin,"unban");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"✳ ایدی عددی کاربر موردنظر رو بفرست :",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('blocklist.txt',"$talk");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت از لیست مسدودیت حذف شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}

if($MyCoIn1 == "🔥 آمار ربات"){
$count = count(scandir("users"));
$a = $count - 23;
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"👤 تعداد بازیکنان : $a",
]);
}
if($MyCoIn1 == "⏳ اشتراک مانده"){
$eshtrakfeli = file_get_contents("tAmDid.txt");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"⏳ اشتراک باقی مانده ربات شما : $eshtrakfeli",
]);
}
 
if($MyCoIn1 == "🔖 پیام همگانی"){
run($fadmin,"sendtoall");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
متن مورد نظر را برای ارسال به همه وارد کنید📡
در صورت لغو برروی $back کلیک کنید!😌✋
",
]);
}
if($run == "sendtoall" and $MyCoIn1 != $back){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){
QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
$MyCoIn1
",
]);
}
}
//////////----------------/////////////////////////////////////////
if($MyCoIn1 == "almszir"){
    run($chat_id,"unban30000");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"تعداد الماس هدیه *زیرمجموعه گیری* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban30000" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('gemamount.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////////////-----------------------///////////
if($MyCoIn1 == "almsroz"){
    run($chat_id,"unban3000roz");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"تعداد الماس *الماس روزانه* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban3000roz" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('crozamount.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

هم اکنون الماس روزانه $MyCoIn1 الماس می باشد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////////////-----------------------///////////
if($MyCoIn1 == "🕹 محدودیت حمله"){
run($fadmin,"unban300900");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"مدت زمان محدودیت حمله را *بر حسب ثانیه* ارسال نمایید

میزان فعلی : $cztime ثانیه",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban300900" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cztime.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
میزان جدید : $MyCoIn1 ثانیه",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////////////-----------------------///////////
if($MyCoIn1 == "admin1s"){
if($chat_id == $cadmin1){
    run($chat_id,"admin2man");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"*آیدی عددی* ادمین جدید را ارسال نمایید

ادمین فعلی : $cadmin2",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}}

if($run == "admin2man" and $MyCoIn1 != $back){
if($chat_id == $cadmin1){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cadmin2.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

ادمین جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}}
///////////////////-----------------------///////////
if($MyCoIn1 == "admin2s"){
if($chat_id == $cadmin1){
    run($chat_id,"admin3man");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"*آیدی عددی* ادمین جدید را ارسال نمایید

ادمین فعلی : $cadmin3",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}}

if($run == "admin3man" and $MyCoIn1 != $back){
if($chat_id == $cadmin1){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cadmin3.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

ادمین جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}}
///////////////////-----------------------///////////
if($MyCoIn1 == "almswell"){
    run($chat_id,"qq87");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"مقدار الماس ورودی هدیه به کاربران را ارسال نمایید
  
میزان فعلی : $cgolamount",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "qq87" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cgolamount.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
میزان جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////////////-----------------------///////////
if($MyCoIn1 == "💳 بخش انتقال"){
run($fadmin,"qq88");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"مقدار الماس قابل انتقال را وارد نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "qq88" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cminmove.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
میزان جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////////////-----------------------///////////
if($MyCoIn1 == "mtalms"){
    run($chat_id,"unban32");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"متن *الماس رایگان* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban32" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('freegoldtxt.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
////////////////----//////////////////
if($MyCoIn1 == "mtkharid"){
    run($chat_id,"unban31");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"متن *خرید الماس* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban31" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('goldtxt.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////-----/////////////////////////////
if($MyCoIn1 == "mtshopss"){
    run($chat_id,"unban3");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"متن *فروشگاه* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban3" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('shoptxt.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
////////////////-----------///////////////
if($MyCoIn1 == "mtstart"){
    run($chat_id,"unban2");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"متن *استارت* را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban2" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('starttxt.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "پلان خرید الماس اول"){
run($fadmin,"unban8888");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام *پلان اول خرید الماس* را ارسال نمایید
  
نام فعلی : $cplan1",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban8888" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cplan1.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
نام جدید
$MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "پلان خرید الماس دوم"){
run($fadmin,"unban8889");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام *پلان دوم خرید الماس* را ارسال نمایید
  
نام فعلی : $cplan2",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban8889" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cplan2.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
نام جدید
$MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "پلان خرید الماس سوم"){
run($fadmin,"unban8890");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام *پلان سوم خرید الماس* را ارسال نمایید
  
نام فعلی : $cplan3",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban8890" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cplan3.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
نام جدید
$MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////--sinza-////////////
if($MyCoIn1 == "🎁 ساخت کد"){
$cnewgif = file_get_contents("cnewgif.txt");
run($fadmin,"bansec1gift");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"کد هدیه جدید را ارسال نمایید",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}
if($run == "bansec1gift" and $MyCoIn1 != $back){
file_put_contents("cnewgif.txt","$MyCoIn1");
run($fadmin,"bansec2gift");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"کد $MyCoIn1 شامل چند الماس باشد؟",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($run == "bansec2gift" and $MyCoIn1 != $back){
$cnewgif = file_get_contents("cnewgif.txt");
file_put_contents("codes/$cnewgif.txt","$MyCoIn1");
 run($fadmin,"none");
 QuBnM('sendmessage',[ 
    'chat_id'=>"@$channels", 
    'text'=>" 
💌کد هدیه جدید ساخته شد

☑️ کد : $cnewgif

💎هدیه : $MyCoIn1 الماس
",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"💎 کد $cnewgif به ارزش $MyCoIn1 الماس با موفقیت ساخته شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
///////////---------------//////////
if($MyCoIn1 == "نصب درگاه پرداخت"){
run($fadmin,"unban8891");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"آدرس کامل *درگاه پرداخت شخصی* خود را به همراه https:// ارسال نمایید

لینک فعلی : [$cshoplink]",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "unban8891" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('cshoplink.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد
    
لینک جدید
[$MyCoIn1]",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "ozvch"){
run($chat_id,"dok1");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *برترین ها* را ارسال نمایید

نام فعلی : $clashd1",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok1" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd1.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok2s"){
run($chat_id,"dok2");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *فروشگاه* را ارسال نمایید

نام فعلی : $clashd2",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok2" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd2.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok3s"){
run($chat_id,"dok3");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *سرباز* را ارسال نمایید

نام فعلی : $clashd3",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok3" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd3.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok4s"){
run($chat_id,"dok4");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *منابع* را ارسال نمایید

نام فعلی : $clashd4",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok4" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd4.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////hm demon////////---////////////
if($MyCoIn1 == "views"){
    file_put_contents('viewbot.txt',"off");
    QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🛡بخش حمله خاموش شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 == "viewson"){
    file_put_contents('viewbot.txt',"on");
    QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🛡بخش حمله روشن شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 == "dok11s"){
run($chat_id,"dok11");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *انتقال الماس* را ارسال نمایید

نام فعلی : $clashd11",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok11" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd11.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 == "تنظیم کانال 🆔"){
run($chat_id,"channelsco");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"ایدی کانال خود را بدون @ ارسال نمایید.",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}
if($run == "channelsco" and $MyCoIn1 != $back){
file_put_contents('channels',$MyCoIn1);
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok5s"){
run($chat_id,"dok5");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *حمله* را ارسال نمایید

نام فعلی : $clashd5",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok5" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd5.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok6s"){
run($chat_id,"dok6");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *انتقام* را ارسال نمایید

نام فعلی : $clashd6",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok6" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd6.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////h/m/d/e/m/o/n//////---////////////
if($MyCoIn1 == "dok7s"){
run($chat_id,"dok7");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *ایجاد تله* را ارسال نمایید

نام فعلی : $clashd7",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok7" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd7.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "almsrozanasba"){
run($chat_id,"dok8");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *الماس روزانه* را ارسال نمایید

نام فعلی : $clashdroz",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok8" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashdroz.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "clashdgift"){
    run($chat_id,"codehedyas");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *کد هدیه* را ارسال نمایید

نام فعلی : $clashdgift",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "codehedyas" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashdgift.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "dok9s"){
run($chat_id,"dok9");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *اتحاد* را ارسال نمایید

نام فعلی : $clashd9",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dok9" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd9.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
if($MyCoIn1 == "dok8s"){
    run($chat_id,"dokkosna");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"نام جدید دکمه *دهکده من* را ارسال نمایید

نام فعلی : $clashd9",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);
}

if($run == "dokkosna" and $MyCoIn1 != $back){
$talk = str_replace("\n$MyCoIn1","","$block");
file_put_contents('clashd8.txt',"$MyCoIn1");
run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ثبت شد

نام جدید : $MyCoIn1",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}
/////////////////////---////////////
if($MyCoIn1 == "🔱 گندم همگانی"){
run($fadmin,"sendgift");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
چه تعداد گندم و چوب به همه هدیه میدهید📡
در صورت لغو برروی $back کلیک کنید!😌✋
",
]);
}
if($run == "sendgift" and $MyCoIn1 != $back){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){

$addfood = get_user($yses,'food');
$addfood += $MyCoIn1;
chenge_user($yses,'food',$addfood);


$addwood = get_user($yses,'wood');
$addwood += $MyCoIn1;
chenge_user($yses,'wood',$addwood);

QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);
}
}
///////////---------------//////////
if($MyCoIn1 == "☀️ اهدای گندم"){
run($fadmin,"bansec1323");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"🌾 کاربری دریافت کننده گندم را ارسال نمایید.",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec1323" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec2323");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔱 تعداد گندم جهت ارسال را وارد نمایید.
🔍 توجه حتما عدد لاتین انگلیسی باشد وگرنه ربات ارور میدهد.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec2323" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$food = get_user($useridclash,'food');
$food += $MyCoIn1;
chenge_user($useridclash,'food',$food);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"✅ از طرف مدیریت این ربات $MyCoIn1 گندم برای شما ارسال شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "⬇️ کسر گندم"){
run($fadmin,"sinzaersal1");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"🔘 کاربری شخصی که باید ازش گندم کسر بشه رو بفرست.",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "sinzaersal1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"sinzaersal2");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🟡مقدار گندمی که باید کسر بشه رو ارسال کنید.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "sinzaersal2" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$food = get_user($useridclash,'food');
$food -= $MyCoIn1;
chenge_user($useridclash,'food',$food);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"❌ $MyCoIn1 گندم از طرف مدیریت از شما کسر شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت کسر شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "⚠️ کسر تله"){
run($fadmin,"talesinza1");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص کسر شونده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "talesinza1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"talesinza");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان تله کسری رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "talesinza" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$tale = get_user($useridclash,'tale');
$tale -= $MyCoIn1;
chenge_user($useridclash,'tale',$tale);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"‼️ $MyCoIn1 تله از شما کسر شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "💣 اهدای تله"){
run($fadmin,"bansec13234585s");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص دریافت کننده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec13234585s" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec23234585s");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان تله دریافتی رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec23234585s" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$tale = get_user($useridclash,'tale');
$tale += $MyCoIn1;
chenge_user($useridclash,'tale',$tale);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"✅ از طرف مدیریت این ربات $MyCoIn1 تله برای شما ارسال شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "💂‍♀️ اهدای سرباز"){
run($fadmin,"bansec13234585");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص دریافت کننده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec13234585" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec23234585");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان سرباز دریافتی رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec23234585" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$troop = get_user($useridclash,'troop');
$troop += $MyCoIn1;
chenge_user($useridclash,'troop',$troop);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"✅ از طرف مدیریت این ربات $MyCoIn1 سرباز برای شما ارسال شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "⭕️ کسر سرباز"){
run($fadmin,"mrsinzaace1");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص کسر شونده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "mrsinzaace1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"mrsinzaace");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان سرباز کسری رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "mrsinzaace" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$troop = get_user($useridclash,'troop');
$troop -= $MyCoIn1;
chenge_user($useridclash,'troop',$troop);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"🚫 $MyCoIn1سرباز از شما کسر شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت کسر شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "🍁 اهدای چوب"){
run($fadmin,"bansec132345");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص دریافت کننده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec132345" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec232345");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان چوب دریافتی رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec232345" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$wood = get_user($useridclash,'wood');
$wood += $MyCoIn1;
chenge_user($useridclash,'wood',$wood);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"✅ از طرف مدیریت این ربات $MyCoIn1 چوب برای شما ارسال شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "♨️ کسر چوب"){
run($fadmin,"ksrchobs");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"👤 کاربری شخص دریافت کننده رو بفرست",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "ksrchobs" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"ksrchobs1");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🔆 میزان چوب کسری رو ارسال کن.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "ksrchobs1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$wood = get_user($useridclash,'wood');
$wood -= $MyCoIn1;
chenge_user($useridclash,'wood',$wood);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"⛔️ $MyCoIn1 چوب از شما کسر شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "🌀 اهدا الماس"){
run($fadmin,"bansec1");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"💎آیدی عددی شخص دریافت کننده را ارسال نمایید :",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec2");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"میزان الماس را جهت واریز به $MyCoIn1 ارسال نمایید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec2" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$gold = get_user($useridclash,'gold');
$gold += $MyCoIn1;
chenge_user($useridclash,'gold',$gold);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>" 
💎 از طرف مدیریت $MyCoIn1 الماس به حساب شما واریز شد
",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
if($MyCoIn1 == "🛑 کسر الماس"){
run($fadmin,"bansec5");
QuBnM('SendMessage', [
  'chat_id' =>$chat_id,
  'text' =>"💎آیدی عددی شخص کسر شونده را ارسال نمایید :",'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
	'resize_keyboard'=>true,
	'keyboard'=>[
	[['text'=>$back]]
	]
	])
	]);}if($run == "bansec5" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
file_put_contents('useridclash.txt',"$MyCoIn1");
run($fadmin,"bansec4");
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"میزان الماس را جهت کسر به $MyCoIn1 ارسال نمایید",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$yses, 
'text'=>" 
🌾 از طرف مدیریت $MyCoIn1 گندم و چوب به تمامی کاربران ارسال شد
",
]);    
}}
if($run == "bansec4" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){    
$useridclash = file_get_contents("useridclash.txt");
$gold = get_user($useridclash,'gold');
$gold -= $MyCoIn1;
chenge_user($useridclash,'gold',$gold);
 run($fadmin,"none");
QuBnM('sendmessage',[ 
    'chat_id'=>$useridclash, 
    'text'=>"⁉️ $MyCoIn1 الماس از شما کسر شد.",
]);
 QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"✅ باموفقیت ارسال شد",
'parse_mode'=>'MarkDown',
        	'reply_markup'=>$button_manage
]);
}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط عدد لاتین وارد نمایید.",
]);
}}
///////////------------///////////
if($MyCoIn1 == "🧨 تله همگانی"){
run($fadmin,"sendgift1w373wwetale");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد تله همگانی را وارد نمایید 
",
]);
}
if($run == "sendgift1w373wwetale" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){

$tale = get_user($yses,'tale');
$tale += $MyCoIn1;
chenge_user($yses,'tale',$tale);

QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
✅ از طرف مدیریت $MyCoIn1 تله برای همه ارسال شد.
",
]);
}}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط از عدد لاتین استفاده نمایید.",
]);
}}
if($MyCoIn1 == "🔫 سرباز همگانی"){
run($fadmin,"sendgift1w373wwe");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد سرباز همگانی را وارد نمایید 
",
]);
}
if($run == "sendgift1w373wwe" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){

$troop = get_user($yses,'troop');
$troop += $MyCoIn1;
chenge_user($yses,'troop',$troop);

QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
✅ از طرف مدیریت $MyCoIn1 سرباز برای همه ارسال شد.
",
]);
}}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط از عدد لاتین استفاده نمایید.",
]);
}}
if($MyCoIn1 == "💥 چوب همگانی"){
run($fadmin,"sendgift1w3");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد چوب همگانی را وارد نمایید 
",
]);
}
if($run == "sendgift1w3" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){

$wood = get_user($yses,'wood');
$wood += $MyCoIn1;
chenge_user($yses,'wood',$wood);

QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
✅ از طرف مدیریت $MyCoIn1 چوب برای همه ارسال شد.
",
]);
}}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط از عدد لاتین استفاده نمایید.",
]);
}}
if($MyCoIn1 == "💎 الماس همگانی"){
run($fadmin,"sendgift1");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد الماس همگانی را وارد نمایید 
",
]);
}
if($run == "sendgift1" and $MyCoIn1 != $back){
if(preg_match("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/",$MyCoIn1)){
$users = scandir("users");
run($fadmin,"no");
foreach($users as $yses){

$gold = get_user($yses,'gold');
$gold += $MyCoIn1;
chenge_user($yses,'gold',$gold);

QuBnM('sendmessage',[ 
    'chat_id'=>$yses, 
    'text'=>" 
💎 از طرف مدیریت $MyCoIn1 الماس به تمامی کاربران ارسال شد
",
]);
}}else{
QuBnM('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"لطفا فقط از عدد لاتین استفاده نمایید.",
]);
}}}
if($MyCoIn1 == "$clashd8"){
$dead = round(get_user($chat_id,'dead'));
$joinclan = get_user($chat_id,"joinclan");
    QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
🌾 گندم ها : $food
🌲 چوب ها : $wood
💎 الماس ها : $gold
💂‍♀ سرباز ها : $troop
🆔 شناسه شما : $chat_id
💀 تعداد قتل های شما: $dead
👴 سطح شما : $xp
🏆 کاپ : $cup
👑 نام اتحاد : $joinclan
",
]);
}
if($MyCoIn1 == "$clashdroz"){
$date = date("Y-F-d");
$lasttime = file_get_contents("users/$chat_id/time.txt");
if($date == $lasttime){
    QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" ❌شما امتیاز امروز خود را دریافت کرده اید",
]);
}else{
$gold = get_user($chat_id,'gold');
$gold += $crozamount;
chenge_user($chat_id,'gold',$gold);
 run($fadmin,"none");
file_put_contents("users/$chat_id/time.txt",$date);
    QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"🎊تبریک🎊

🔅تعداد $crozamount الماس به عنوان الماس روزانه به شما تعلق گرفت",
]);
}
}

if($MyCoIn1 == "entertroop"){
run($chat_id,"entertroop");
$pricealltroop = round($food / 10);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 

    'text'=>" 
تعداد مورد نظر را ارسال کنید💀
حداکثر سرباز ها : $pricealltroop
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
 
if($run == "entertroop"){
    $MyCoInacg = preg_replace('/[^0-9]/','',$MyCoIn1);
    $packprice = $MyCoInacg * 10;
    if($food >= $packprice and $MyCoIn1 != $back){
$troop += $MyCoInacg;
    $food -= $packprice;
    chenge_user($chat_id,'food',$food);
    chenge_user($chat_id,'troop',$troop);
    $pricealltroop = round($food / 10) - 1;
if($pricealltroop > 0){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
سرباز های مورد نظر ایجاد شد💀
باقیمانده ایجاد💀 : $pricealltroop
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
سرباز های مورد نظر ایجاد شد💀
",
'parse_mode'=>'MARKDOWN',
]);
}
}elseif($MyCoIn1 != $back){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
منابع کافی نیست💀
",
'parse_mode'=>'MARKDOWN',
]);
}
}



if($MyCoIn1 == "customtale"){
run($chat_id,"customtale");
$pricealltale = round($wood / $ptale);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تعداد مورد نظر را ارسال کنید💀
حداکثر تله ها : $pricealltale
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($run == "customtale"){
    $MyCoInacg = preg_replace('/[^0-9]/','',$MyCoIn1);
    $packprice = $MyCoInacg * 100;
    if($wood >= $packprice and $MyCoIn1 != $back){
    $tale += $MyCoInacg;
    $wood -= $packprice;
    chenge_user($chat_id,'wood',$wood);
    chenge_user($chat_id,'tale',$tale);
    $pricealltroop = round($wood / $ptale);

if($pricealltroop > 0){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تله های مورد نظر ایجاد شد💀
باقیمانده ایجاد💀 : $pricealltroop
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
تله های مورد نظر ایجاد شد💀
",
'parse_mode'=>'MARKDOWN',
]);
}
}elseif($MyCoIn1 != $back){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
منابع کافی نیست💀
",
'parse_mode'=>'MARKDOWN',
]);
}
}
 
$joinclan = get_user($fadmin,"joinclan");
if($MyCoIn1 == "$clashd9"){
if($joinclan == null){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
یک اتحاد بسازید و یا وارد یک اتحاد بشید👹
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                 [
                 ['text'=>'ایجاد اتحاد🎭'], ['text'=>'ورود به اتحاد👾']
                ],
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
}

if($joinclan == null){

if($MyCoIn1 == "ایجاد اتحاد🎭"){
run($chat_id,"create_clan1");

QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
نام اتحاد را وارد کنید💎
هزینه 50 الماس💎
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);}
if($run == "create_clan1" and $MyCoIn1 != $back){
if($gold >= 50){
if(!is_dir("clans/$MyCoIn1")){
$gold -= 50;
chenge_user($chat_id,'gold',$gold);
chenge_user($chat_id,"joinclan",$MyCoIn1);
mkdir("users/$chat_id/clan");
mkdir("clans");
file_put_contents("users/$chat_id/clan/name.txt",$MyCoIn1);
mkdir("clans/$MyCoIn1");
mkdir("clans/$MyCoIn1/users");
file_put_contents("clans/$MyCoIn1/admin.txt",$fadmin);
mkdir("clans/$MyCoIn1/users/$fadmin");
run($chat_id,"no");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
اتحاد با موفقیت ایجاد شد!
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
این نام از قبل استفاده شده است
",
'parse_mode'=>'MARKDOWN',
]);
}
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
منابع کافی نیست!
",
'parse_mode'=>'MARKDOWN',
]);
}
}
if($MyCoIn1 == "ورود به اتحاد👾"){
run($chat_id,"join_clan");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
نام اتحاد را وارد کنید💎
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($run == "join_clan" and $MyCoIn1 != $back){
if(is_dir("clans/$MyCoIn1")){
$mintroop = get_clan($joinclan,'mintroop');
if($troop > $mintroop){
run($chat_id,"no");
chenge_user($chat_id,"joinclan",$MyCoIn1);
mkdir("users/$chat_id/clan");
mkdir("clans");
mkdir("clans/$MyCoIn1/users");
mkdir("clans/$MyCoIn1/users/$fadmin");
file_put_contents("users/$chat_id/clan/name.txt",$MyCoIn1);
$adminclans = file_get_contents("clans/$MyCoIn1/admin.txt");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
با موفقیت وارد شدید💎
",
'parse_mode'=>'MARKDOWN',
]);
QuBnM('sendmessage',[ 
    'chat_id'=>$adminclans, 
    'text'=>" 
کاربر [$first_name](tg://user?id=$fadmin) عضو اتحاد شد💎
",
'parse_mode'=>'MARKDOWN',
]);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$adminclans, 
    'text'=>" 
تعداد سرباز های شما برای ورود به این اتحاد کافی نیست💀
حداقل سرباز : $mintroop
",
'parse_mode'=>'html',
]);
}
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
این اتحاد وجود ندارد
",
'parse_mode'=>'MARKDOWN',
]);
}
}
}

if($joinclan != null){
if($MyCoIn1 == "$clashd9"){
$info = get_clan($joinclan,'bio');
$adminclans = file_get_contents("clans/$joinclan/admin.txt");
if($chat_id != $adminclans){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
نام اتحاد : $joinclan
$info
",
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
               [
               ['text'=>"چت اتحاد☁"]
               ],
               [
                ['text'=>"لیست اعضا👹"],['text'=>'قدرت اتحاد⚔']
                ],
                [
                ['text'=>"خروج از اتحاد🎪"],['text'=>$back]
                ]
              ],
])
]);
}else{
     
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
نام اتحاد : $joinclan
$info
",
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
               [
               ['text'=>"چت اتحاد☁"]
               ],
               [
                ['text'=>"لیست اعضا👹"],['text'=>'قدرت اتحاد⚔']
                ],
                [
                ['text'=>'اخراج🤡'],['text'=>'تنظیمات⚙']
                ],
                [
                ['text'=>"خروج از اتحاد🎪"],['text'=>$back]
                ]
              ],
])
]);
}
}
if($MyCoIn1 == "خروج از اتحاد🎪"){
left_clan($chat_id);
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
با موفقیت خارج شدید💀
",
'parse_mode'=>'MARKDOWN',
]);
}
if($MyCoIn1 == "قدرت اتحاد⚔"){
$scanglobal = scandir("clans/$joinclan/users");
unset($scanglobal[0]); unset($scanglobal[1]);
$powertroop = 0;
$powercup = 0;
foreach($scanglobal as $sendforall){
$counttroop = round(get_user($sendforall,'troop'));
$powertroop += $counttroop;
$countcup = round(get_user($sendforall,'cup'));
$powercup += $countcup;
}
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
قدرت سرباز💂 : $powertroop
قدرت کاپ🏆 : $powercup
",
'parse_mode'=>'MARKDOWN',
]);
}
if($MyCoIn1 == "لیست اعضا👹"){
file_put_contents("spam/$fadmin.txt",file_get_contents("spam/$fadmin.txt") + 1);
if(file_get_contents("spam/$fadmin.txt") <= 1){
$scanglobal = scandir("clans/$joinclan/users");
unset($scanglobal[0]); unset($scanglobal[1]);
foreach($scanglobal as $sendforall){
$counttroop = round(get_user($sendforall,'troop'));
$countcup = round(get_user($sendforall,'cup'));
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
نام کاربر 💂 : [$sendforall](tg://user?id=$sendforall)
تعداد سرباز ها :👹 $counttroop
تعداد کاپ ها🏆 : $countcup
",
'parse_mode'=>'MARKDOWN',
]);
}
 
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
لیست اعضا ارسال شد💂
",
'parse_mode'=>'MARKDOWN',
]);
}
file_put_contents("spam/$fadmin.txt",0);
}

$adminclans = file_get_contents("clans/$joinclan/admin.txt");
if($chat_id == $adminclans){
if($MyCoIn1 == "اخراج🤡"){
run($chat_id,"remove_userclan");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
چت ایدی فرد مورد نظر را وارد کنید🏆
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])
]);
}
if($MyCoIn1 == "تنظیمات⚙"){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
خوش امدید!👹
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>'متن ورود به اتحاد']
                ],
                [
                ['text'=>'حداقل سرباز برای ورود به اتحاد']
                ],
                [
                ['text'=>$back]
                ]
              ],
])]);}
if($MyCoIn1 == "متن ورود به اتحاد"){
run($fadmin,"textjoinclan");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
متن مورد نظر را وارد کنید!👹
",
]);}
if($run == "textjoinclan" and $MyCoIn1 != $back){
chenge_clan($joinclan,'bio',$MyCoIn1);
run($fadmin,"no");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
تنظیم شد👹!
",
]);}
if($MyCoIn1 == "حداقل سرباز برای ورود به اتحاد"){
run($fadmin,"mintroopforjoin");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
تعداد مورد نظر را وارد کنید!👹
مثال : 1000
بعد از وارد کردن 1000 افرادی که تعداد سرباز کمتر 1000 دارند نمیتوانند عضو اتحاد شوند
",
]);}
if($run == "mintroopforjoin" and $MyCoIn1 != $back and is_numeric($MyCoIn1)){
chenge_clan($joinclan,'mintroop',$MyCoIn1);
run($fadmin,"no");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
تنظیم شد👹!
",
]);}
if($run == "remove_userclan" and $MyCoIn1 != $back){
if(is_dir("clans/$joinclan/users/$MyCoIn1")){
remove_clan($MyCoIn1,$joinclan);
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
این کاربر وجود ندارد😑
",
'parse_mode'=>'MARKDOWN',
]);}}}}
if($MyCoIn1 == "$clashd6"){
if((time() - filectime("users/$chat_id/zam.txt")) > $cztime){
run($fadmin,"dead_enemy");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
ایدی مورد نظر را برای جنگ وارد کنید💀
",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>$back]
                ]
              ],
])]);
}else{
$x = time() - filectime("users/$chat_id/zam.txt");
$zam = $cztime - $x;
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"❗️شما تا $zam ثانیه دیگر نمیتوانید حمله کنید",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[[['text'=>$back]]],])]);}}
if($run == "dead_enemy" and $MyCoIn1 != $back and $MyCoIn1 != $chat_id){
if(is_dir("users/$MyCoIn1")){
if(!is_dir("users/$chat_id/revenge/$MyCoIn12")){
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
این فرد مرتکب جرمی در اتحاد شما نشده است💀
",
'parse_mode'=>'MARKDOWN',
]);
}else{
run($fadmin,"no");
    $enemy = $MyCoIn1;
    $troopenemy = round(get_user($enemy,'troop'));
    file_put_contents("users/$chat_id/enemy.txt",$enemy);
$joinclanen = get_user($enemy,"joinclan");
  $enfood = get_user($enemy,'food');
  $enwood = get_user($enemy,'wood');
  $GLfood = round(Get_Loot($troop,$enfood));
  $GLwood = round(Get_Loot($troop,$enwood));
 
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>" 
دشمن : [$enemy](tg://user?id=$enemy)
💂🏻️ تعداد سرباز ها : 𝕙𝕚𝕕𝕕𝕖𝕟
🌱 گندم : $GLfood
🌲 چوب : $GLwood
🛡 نام اتحاد : $joinclanen
",
'parse_mode'=>'MARKDOWN',
 'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
['text'=>"⚔️حمله⚔️",'callback_data'=>"attack"]
],]])]);}
}else{
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"
❌ این کاربر عضو ربات نمی باشد
",
'parse_mode'=>'MARKDOWN',
]);}}
if($MyCoIn1 == 'چت اتحاد☁'){
if(!is_dir("clans/$joinclan/global")){
mkdir("clans/$joinclan/global");
}
mkdir("clans/$joinclan/global/$fadmin");
run($chat_id,"chat_clan");
QuBnM('sendmessage',[ 
    'chat_id'=>$chat_id, 
    'text'=>"با موفقیت وارد چت اتحاد شدید 📨
❗️از حالا به بعد هر پیامی ارسال کنید به تمامی کاربران عضو این اتحاد ارسال خواهد شد",
'parse_mode'=>'MARKDOWN',
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[[['text'=>$back]]],])]);}
if($run == "chat_clan" and $MyCoIn1 != $back and $MyCoIn1 != null and $MyCoIn1 != '/start'){
if(strpos($MyCoIn1, "http") !== false){
QuBnM('sendmessage',[ 
    'chat_id'=>$fadmin, 
    'text'=>"تبلیغات کنی اخراج میشی 😒",
'parse_mode'=>'MARKDOWN',
]);}else{
$scanglobalclan = scandir("clans/$joinclan/users");
foreach($scanglobalclan as $sendforall){
QuBnM('sendmessage',[ 
    'chat_id'=>$sendforall, 
    'text'=>
"[$first_name](tg://user?id=$fadmin) | $fadmin :
$MyCoIn1
",
'parse_mode'=>'MARKDOWN',]);}}}
if($run == "chat_clan" and $MyCoIn1 == $back){
rmdir("clans/$joinclan/global/$fadmin");
QuBnM('sendmessage',[ 
    'chat_id'=>$fadmin, 
    'text'=>"شما با موفقیت خارج شدید❌
❕توجه کنید که ازحالا به بعد پیام های شما برای اعضای اتحاد نمایش داده نخواهد شد.",
'parse_mode'=>'MARKDOWN',]);}
 $su = scandir('users');
foreach($su as $users){$joinclanusers = get_user($users,"joinclan");
$cup2 = get_user($users,'cup');
if($cup2 < 0){
chenge_user($users,"cup",0);}
$joinclanus = get_user($users,"joinclan");$joinclan = get_user($chat_id,"joinclan");
if(equel_cup($cup,$cup2) and $users != $fadmin and is_numeric($users) and $joinclanus != $joinclan){
mkdir("users/$fadmin/enemy/$users");
mkdir("users/$users/enemy/$fadmin");}
$scen = scandir("users/$users/enemy");
foreach($scen as $userenemy){
if(!is_numeric($userenemy)){
rmdir("users/$users/enemy/$userenemy");
}}}
if(checkuser($fadmin) != true){
signup($fadmin);
}}}}
if(file_exists(error_log))
unlink(error_log);
?>