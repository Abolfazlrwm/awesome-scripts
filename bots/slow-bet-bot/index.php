<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$ttti = time();
error_reporting(0);
set_time_limit(0);
define('API_KEY',"PUT-YOUR-BOT-TOKEN");//توکن
include("jdf.php");
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);$res = curl_exec($ch);

if(curl_error($ch)){var_dump(curl_error($ch));
}else{
return json_decode($res);
}}
function SendMessage($fromid,$text,$keyboard = null){
return bot('sendMessage',[
'chat_id'=>$fromid,
'text'=>$text,
'parse_mode'=>"html",
'disable_web_page_preview'=>"true",
'reply_markup'=>$keyboard]);
}
function EditMessage($chat_id,$meesage_id,$text,$reply_markup){
return bot('editMessageText',[
'chat_id'=>$fromid,
'message_id'=>$message_id,
'text'=>$text,
'reply_markup'=>$keyboard]);
}
function answerCallbackQuery($callbackid,$text,$true){
	bot('answerCallbackQuery',[
		'callback_query_id' => $callbackid,
		'text' => $text,
		'show_alert' => $true,
		'cache_time'=>5,
]);
}
function convert($string) {
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
    return $englishNumbersOnly;
}
function rand_string( $length ) {
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
return substr(str_shuffle($chars),0,$length);
}
function deletefolder($path){
 if($handle=opendir($path)){
  while (false!==($file=readdir($handle))){
   if($file<>"." AND $file<>".."){
    if(is_file($path.'/'.$file)){ 
     @unlink($path.'/'.$file);
} 
    if(is_dir($path.'/'.$file)) { 
     deletefolder($path.'/'.$file); 
     @rmdir($path.'/'.$file); 
}
   }
    }
}
}
@$update = json_decode(file_get_contents("php://input"));
if(isset($update)){
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
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
	exit(header("location: Https://SnowyTeam.xyz"));
	}
	}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
@$text = convert($update->message->text);
@$from_id = $update->message->from->id;
@$type = $update->message->chat->type;
@$first_name = $update->message->from->first_name;
@$username2 = $update->message->from->username;
@$message_id = $update->message->message_id;
@$data = $update->callback_query->data;
@$messageid = $update->callback_query->message->message_id;
@$firstname = $update->callback_query->from->first_name;
@$callback_query = $update->callback_query;
@$data = $callback_query->data;
@$callbackid = $callback_query->id;
@$calltext = $callback_query->message->text;
@$chatid = $update->callback_query->message->chat->id;
@$creator = "@CreatorSlow";
@$text = str_replace(["`","*","[","]","-","+","(",")","=","^","~","&"],["","","","",""],$text);
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(!file_exists('text')){
mkdir("text");
}
if(!file_exists('saham')){
mkdir("saham");
}
if(!file_exists('admin')){
mkdir("admin");
}
if(!file_exists('coin')){
mkdir("coin");
}
if(!file_exists('subset')){
mkdir("subset");
}
if(!file_exists('onof')){
mkdir("onof");
}
if(!file_exists('button')){
mkdir("button");
}
if(!file_exists('tarakonesh')){
mkdir("tarakonesh");
}
if(!file_exists('xbox')){
mkdir("xbox");
}
if(!file_exists('zarib')){
mkdir("zarib");
}
if(!file_exists('data')){
mkdir("data");
}
if(!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
function Spam($from_id){
if(!file_exists('spam')){
mkdir("spam");
}
if(file_exists("spam/$from_id.json")){
$spam_status = json_decode(file_get_contents("spam/$from_id.json"),true);
}else{
$spam_statuss = "none";
file_put_contents("spam/$from_id.json",json_encode($spam_statuss,true));
}
if($spam_status != null){
if(mb_strpos($spam_status[0],"time") !== false){
if(str_replace("time ",null,$spam_status[0]) >= time())
exit(false);
else
$spam_status = [1,time()+1];
}
elseif(time() < $spam_status[1]){
if($spam_status[0]+1 > 1){
$time = time() + 0;
$spam_status = ["time $time"];
file_put_contents("spam/$from_id.json",json_encode($spam_status,true));
RfTvKj('SendMessage',[
'chat_id'=>$from_id,
'text'=>"💯 لطفاً آهسته تر با ربات کار کنید",
]);
exit(false);
}else{
$spam_status = [$spam_status[0]+1,$spam_status[1]];
}
}else{
$spam_status = [1,time()+1];
}
}else{
$spam_status = [1,time()+1];
}
file_put_contents("spam/$from_id.json",json_encode($spam_status,true));
}
Spam($from_id);
$date = jdate("Y/m/d");
$time = jdate("H:i:s");
$hxmfk = API_KEY;
if(strpos($text, 'zip') !== false or strpos($text, 'ZIP') !== false or strpos($text, 'Zip') !== false or strpos($text, 'ZIp') !== false or strpos($text, 'zIP') !== false or strpos($text, 'ZipArchive') !== false or strpos($text, 'ZiP') !== false){
exit;
}
if(strpos($text, 'kajserver') !== false or strpos($text, 'update') !== false or strpos($text, 'UPDATE') !== false or strpos($text, 'Update') !== false or strpos($text, 'https://api') !== false or strpos($text, 'https') !== false or strpos($text, 'http') !== false){
exit;
}
if(strpos($text, 'GetM') !== false or strpos($text, '_GET') !== false or strpos($text, 'ZipArchive') !== false or strpos($text, 'Done!') !== false or strpos($text, 'ZiP') !== false or strpos($text, 'php://input') !== false or strpos($text, 'eval') !== false){
exit;exit;
}
if(strpos($text, 'curl_exec') !== false or strpos($text, 'avid') !== false or strpos($text, 'cur') !== false or strpos($text, '&') !== false or strpos($text, ';') !== false or strpos($text, ']') !== false or strpos($text, '[') !== false){
exit;
}
if(strpos($text, '*') !== false or strpos($text, '|') !== false or strpos($text, ',') !== false or strpos($text, '^') !== false or strpos($text, '%') !== false or strpos($text, 'Kaj') !== false ){
exit;
}
if(strpos($text, '}') !== false or strpos($text, ')') !== false){
exit;
}
if(strpos($text, 'php') !== false or strpos($text, 'Php') !== false or strpos($text, 'pHp') !== false or strpos($text, 'PhP') !== false or strpos($text, 'PHp') !== false or strpos($text, 'Kaj') !== false){
exit;
}
if(strpos($text, 'Hack') !== false or strpos($text, 'Hac') !== false or strpos($text, 'telegram') !== false or strpos($text, 'Telegram') !== false or strpos($text, 'url') !== false or strpos($text, 'Url') !== false or strpos($text, "$hxmfk") !== false){
exit;
}
if(strpos($text, '$') !== false){
exit;
}
if(strpos($text, '){') !== false){
exit;
}
if(strpos($text, 'getme') !== false or strpos($text, 'GetMe') !== false){
exit;
}
if(strpos($text, 'Json') !== false or strpos($text, 'json') !== false){
	exit;
}
if(strpos($text, 'JSon') !== false or strpos($text, 'JSON') !== false){
exit;
}
if(strpos($text, 'API_KEY') !== false or strpos($text, 'API_KE') !== false){
exit;
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("text/help.txt")){
$help = file_get_contents("text/help.txt");
}else{
$help = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/saham.txt")){
$saham = file_get_contents("text/saham.txt");
}else{
$saham = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/Law.txt")){
$Law = file_get_contents("text/Law.txt");
}else{
$Law = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/bartar1.txt")){
$bartar1 = file_get_contents("text/bartar1.txt");
}else{
$bartar1 = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/bartar2.txt")){
$bartar2 = file_get_contents("text/bartar2.txt");
}else{
$bartar2 = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/bartar3.txt")){
$bartar3 = file_get_contents("text/bartar3.txt");
}else{
$bartar3 = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/Conversation.txt")){
$Conversation = file_get_contents("text/Conversation.txt");
}else{
$Conversation = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/poshtiban.txt")){
$poshtiban = file_get_contents("text/poshtiban.txt");
}else{
$poshtiban = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/shart.txt")){
$shart = file_get_contents("text/shart.txt");
}else{
$shart = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/moshak.txt")){
$moshak = file_get_contents("text/moshak.txt");
}else{
$moshak = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/DartChannel.txt")){
$DartChannel = file_get_contents("text/DartChannel.txt");
}else{
$DartChannel = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/gap.txt")){
$gap = file_get_contents("text/gap.txt");
}else{
$gap = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/addi.txt")){
$addi = file_get_contents("text/addi.txt");
}else{
$addi = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/accReceiver.txt")){
$accReceiver = file_get_contents("text/accReceiver.txt");
}else{
$accReceiver = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/bonos.txt")){
$bonos = file_get_contents("text/bonos.txt");
}else{
$bonos = "𝑛𝑜𝑛𝑒";
}
if(file_exists("text/bonos1.txt")){
$bonos1 = file_get_contents("text/bonos1.txt");
}else{
$bonos1 = "1000";
}
if(file_exists("text/bonos2.txt")){
$bonos2 = file_get_contents("text/bonos2.txt");
}else{
$bonos2 = "4000";
}
if(file_exists("text/bonos3.txt")){
$bonos3 = file_get_contents("text/bonos3.txt");
}else{
$bonos3 = "1000";
}
if(file_exists("text/bonos4.txt")){
$bonos4 = file_get_contents("text/bonos4.txt");
}else{
$bonos4 = "1000";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("commentX/coment1.txt")){
$coment1 = file_get_contents("commentX/coment1.txt");
}else{
$coment1 = "𝑛𝑜𝑛𝑒";
}
if(file_exists("commentX/coment2.txt")){
$coment2 = file_get_contents("commentX/coment2.txt");
}else{
$coment2 = "𝑛𝑜𝑛𝑒";
}
if(file_exists("commentX/coment3.txt")){
$coment3 = file_get_contents("commentX/coment3.txt"); 
}else{
$coment3 = "𝑛𝑜𝑛𝑒";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("saham/saham.txt")){
$mix = file_get_contents("saham/saham.txt");
}else{
$mix = "0";
}
if(file_exists("saham/sahamdar.txt")){
$sahamdar = file_get_contents("saham/sahamdar.txt");
}else{
$sahamdar = "❌ شخصی سهامی خریداری نکرده است";
}
$baqi = 100 - $mix;
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("admin/admin1.txt")){
$admin1 = file_get_contents("admin/admin1.txt");
}else{
$admin1 = "❌ ندارید";
}
if(file_exists("admin/admin2.txt")){
$admin2 = file_get_contents("admin/admin2.txt");
}else{
$admin2 = "❌ ندارید";
}
if(file_exists("admin/admin3.txt")){
$admin3 = file_get_contents("admin/admin3.txt");
}else{
$admin3 = "❌ ندارید";
}
if(file_exists("admin/owner.txt")){
$owner = file_get_contents("admin/owner.txt");
}else{
$owner = "971881348";
}
@$admin = array("$owner","$admin1","$admin2","$admin3"); // ای دی عددی ادمین ها
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("coin/meqdar.txt")){
$meqdar = file_get_contents("coin/meqdar.txt");
}else{
$meqdar = "100";
}
if(file_exists("coin/bardasht.txt")){
$bardasht = file_get_contents("coin/bardasht.txt");
}else{
$bardasht = "10000";
}
if(file_exists("coin/bardashtX.txt")){
$bardashtX = file_get_contents("coin/bardashtX.txt");
}else{
$bardashtX = "3000000";
}
if(file_exists("coin/poof.txt")){
$poof = file_get_contents("coin/poof.txt");
}else{
$poof = "10000";
}
if(file_exists("coin/zir.txt")){
$zir = file_get_contents("coin/zir.txt");
}else{
$zir = "100";
}
if(file_exists("coin/sharj.txt")){
$sharj = file_get_contents("coin/sharj.txt");
}else{
$sharj = "1000";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("subset/link.txt")){
$LINK = file_get_contents("subset/link.txt");
}else{
$LINK = "https://t.me/Mafia_nit/153";
}
if(file_exists("subset/mtn.txt")){
$mtn = file_get_contents("subset/mtn.txt");
}else{
$mtn = "
✅ قابلیت بازی در کانال 
✅ بازی بصورت خودکار در کانال اجرا میشود 
✅ پشتیبانی 24 ساعته 
✅ آپدیت های روزانه 
✅ قابلیت زیر مجموعه گیری 
✅ قابلیت شارژ حساب به 5 روش
⇱ نیترو سین / ریپید سین / سرعت ممبر / جت ممبر / زیر مجموعه گیری ⇲


⌚️ بازی های متنوع و جالب روز 
💸 تسویه حساب بصورت آنی 
💳 شارژ حساب با سرعت بالا 
🎁 بونوس خوش آمد گویی
🎊 ضریب های بالا 
🧬 چالش های روزانه در ربات 



🎊 همین حالا شانس خودت رو امتحان کن و میلیونر نیترو شوید 🧨

📊 اِسلُو بِت معتبر ترین ربات شرط بندی
";
}
if(file_exists("subset/mtnzir.txt")){
$mtnzir = file_get_contents("subset/mtnzir.txt");
}else{
$mtnzir = "
✅  بابت هر فردی که با لینک شما وارد ربات $zir تومان اهدا میشود ! 
👈 بنر خود را به دوستان خود ارسال کنید

زیرمجموعه شما باید عضو کانال ها بشن و بعد روی چنتا دکمه ربات کلیک کنند تا ثبت بشن و سکه به حسابتون بیاد ... !
";
}
if(file_exists("subset/doksubset.txt")){
$dos = file_get_contents("subset/doksubset.txt");
}else{
$dos = "زیر مجموعه گیری 🎉";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("onof/card21.txt")){
$card21 = file_get_contents("onof/card21.txt");
}else{
$card21 = "on";
}
if(file_exists("onof/basket.txt")){
$basket = file_get_contents("onof/basket.txt");
}else{
$basket = "on";
}
if(file_exists("onof/enfejar.txt")){
$enfejar = file_get_contents("onof/enfejar.txt");
}else{
$enfejar = "on";
}
if(file_exists("onof/moont.txt")){
$moont = file_get_contents("onof/moont.txt");
}else{
$moont = "on";
}
if(file_exists("onof/gooli.txt")){
$gooli = file_get_contents("onof/gooli.txt");
}else{
$gooli = "on";
}
if(file_exists("onof/footi.txt")){
$footi = file_get_contents("onof/footi.txt");
}else{
$footi = "on";
}
if(file_exists("onof/boli.txt")){
$boli = file_get_contents("onof/boli.txt");
}else{
$boli = "on";
}
if(file_exists("onof/sangi.txt")){
$sangi = file_get_contents("onof/sangi.txt");
}else{
$sangi = "on";
}
if(file_exists("onof/rolet.txt")){
$roleton = file_get_contents("onof/rolet.txt");
}else{
$roleton = "on";
}
if(file_exists("onof/popi.txt")){
$popi = file_get_contents("onof/popi.txt");
}else{
$popi = "on";
}
if(file_exists("onof/tasi.txt")){
$tasi = file_get_contents("onof/tasi.txt");
}else{
$tasi = "on";
}
if(file_exists("onof/darti.txt")){
$darti = file_get_contents("onof/darti.txt");
}else{
$darti = "on";
}
if(file_exists("onof/skati.txt")){
$skati = file_get_contents("onof/skati.txt");
}else{
$skati = "on";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("button/dok1.txt")){
$dok1 = file_get_contents("button/dok1.txt");
}else{
$dok1 = "💣 ثبت بازی جدید";
}
if(file_exists("button/dok2.txt")){
$dok2 = file_get_contents("button/dok2.txt");
}else{
$dok2 = "📊 پیش‌بینی ها";
}
if(file_exists("button/dok3.txt")){
$dok3 = file_get_contents("button/dok3.txt");
}else{
$dok3 = "🔐 حساب کاربری";
}
if(file_exists("button/dok4.txt")){
$dok4 = file_get_contents("button/dok4.txt");
}else{
$dok4 = "🆘 پشتیبانی";
}
if(file_exists("button/dok5.txt")){
$dok5 = file_get_contents("button/dok5.txt");
}else{
$dok5 = "💡راهنما";
}
if(file_exists("button/dok6.txt")){
$dok6 = file_get_contents("button/dok6.txt");
}else{
$dok6 = "🎗سهام🎗";
}
if(file_exists("button/dok7.txt")){
$dok7 = file_get_contents("button/dok7.txt");
}else{
$dok7 = "قوانین ربات 📑";
}
if(file_exists("button/dok8.txt")){
$dok8 = file_get_contents("button/dok8.txt");
}else{
$dok8 = "ارسال اکانت 💫";
}
if(file_exists("button/dok9.txt")){
$dok9 = file_get_contents("button/dok9.txt");
}else{
$dok9 = "💸";
}
if(file_exists("button/dok10.txt")){
$dok10 = file_get_contents("button/dok10.txt");
}else{
$dok10 = "💵 شارژ حساب";
}
if(file_exists("button/dok11.txt")){
$dok11 = file_get_contents("button/dok11.txt");
}else{
$dok11 = "💰 برداشت موجودی";
}
if(file_exists("button/dok12.txt")){
$dok12 = file_get_contents("button/dok12.txt");
}else{
$dok12 = "گفت و گو 🔰";
}
if(file_exists("button/dok13.txt")){
$dok13 = file_get_contents("button/dok13.txt");
}else{
$dok13 = "💰 انتقال سکه";
}
if(file_exists("button/dok14.txt")){
$dok14 = file_get_contents("button/dok14.txt");
}else{
$dok14 = "🎁برترین ها";
}
if(file_exists("button/dok15.txt")){
$dok15 = file_get_contents("button/dok15.txt");
}else{
$dok15 = "🥂 وضعیت ربات";
}
if(file_exists("button/dok16.txt")){
$dok16 = file_get_contents("button/dok16.txt");
}else{
$dok16 = "📜 تاریخچه بازی ها";
}
if(file_exists("button/dok17.txt")){
$dok17 = file_get_contents("button/dok17.txt");
}else{
$dok17 = "🔥 تراکنش ها";
}
if(file_exists("button/dok18.txt")){
$dok18 = file_get_contents("button/dok18.txt");
}else{
$dok18 = "☁️ چت با اعضا ربات";
}
if(file_exists("button/dok19.txt")){
$dok19 = file_get_contents("button/dok19.txt");
}else{
$dok19 = "🎉";
}
if(file_exists("button/dok20.txt")){
$dok20 = file_get_contents("button/dok20.txt");
}else{
$dok20 = "♻️ آپدیت";
}
if(file_exists("button/dok21.txt")){
$dok21 = file_get_contents("button/dok21.txt");
}else{
$dok21 = "🌟 بخش ویژه";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
$harvest1 = file_get_contents("tarakonesh/harvest1.txt");
$harvest2 = file_get_contents("tarakonesh/harvest2.txt");
$harvest3 = file_get_contents("tarakonesh/harvest3.txt"); 
$hesab1 = file_get_contents("tarakonesh/hesab1.txt");
$hesab2 = file_get_contents("tarakonesh/hesab2.txt");
$hesab3 = file_get_contents("tarakonesh/hesab3.txt"); 
$crash1 = file_get_contents("xbox/crash1.txt");
$crash2 = file_get_contents("xbox/crash2.txt");
$crash3 = file_get_contents("xbox/crash3.txt");
$monti1 = file_get_contents("xbox/monti1.txt");
$monti2 = file_get_contents("xbox/monti2.txt");
$monti3 = file_get_contents("xbox/monti3.txt");
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("zarib/football.txt")){
$football = file_get_contents("zarib/football.txt");
}else{
$football = "0.5";
}
if(file_exists("zarib/basketball.txt")){
$basketball = file_get_contents("zarib/basketball.txt");
}else{
$basketball = "1";
}
if(file_exists("zarib/boling.txt")){
$boling = file_get_contents("zarib/boling.txt");
}else{
$boling = "1.5";
}
if(file_exists("zarib/mon.txt")){
$mon = file_get_contents("zarib/mon.txt");
}else{
$mon = "0.5";
}
if(file_exists("zarib/gool.txt")){
$gool = file_get_contents("zarib/gool.txt");
}else{
$gool = "0.5";
}
if(file_exists("zarib/poop.txt")){
$poop = file_get_contents("zarib/poop.txt");
}else{
$poop = "0.5";
}
if(file_exists("zarib/rolet.txt")){
$rolet = file_get_contents("zarib/rolet.txt");
}else{
$rolet = "0.7";
}
if(file_exists("zarib/Paper.txt")){
$Paper = file_get_contents("zarib/Paper.txt");
}else{
$Paper = "1";
}
if(file_exists("zarib/skat.txt")){
$skat = file_get_contents("zarib/skat.txt");
}else{
$skat = "2";
}
if(file_exists("zarib/dartV.txt")){
$dartV = file_get_contents("zarib/dartV.txt");
}else{
$dartV = "1";
}
if(file_exists("zarib/dartGH.txt")){
$dartGH = file_get_contents("zarib/dartGH.txt");
}else{
$dartGH = "0.75";
}
if(file_exists("zarib/dartSS.txt")){
$dartSS = file_get_contents("zarib/dartSS.txt");
}else{
$dartSS = "1";
}
if(file_exists("zarib/dartBI.txt")){
$dartBI = file_get_contents("zarib/dartBI.txt");
}else{
$dartBI = "1.2";
}
if(file_exists("zarib/tasupd.txt")){
$tasupd = file_get_contents("zarib/tasupd.txt");
}else{
$tasupd = "0.5";
}
if(file_exists("zarib/tasfard.txt")){
$tasfard = file_get_contents("zarib/tasfard.txt");
}else{
$tasfard = "0.5";
}
if(file_exists("zarib/tashad.txt")){
$tashad = file_get_contents("zarib/tashad.txt");
}else{
$tashad = "1";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("channel.txt")){
$channel = file_get_contents("channel.txt");
}
if(file_exists("channel2.txt")){
$channel2 = file_get_contents("channel2.txt");
}
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel&user_id=".$from_id));
$joqw = $joincha->result->status;
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel2&user_id=".$from_id));
$joqwe = $joinch->result->status;
if(isset($update->message)){
if(file_exists("data/$from_id/$from_id.json")){
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
}
}
if(isset($update->callback_query)){
if(file_exists("data/$chatid/$chatid.json")){
$datas = json_decode(file_get_contents("data/$chatid/$chatid.json"),true);
}
}
$warn = $datas["warn"];
if(file_exists("data/ben.txt")){
$ben = file_get_contents("data/ben.txt");
}else{
$ben = "0";
}
$tch = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$channel&user_id=".$from_id))->result->status;
$tch2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$channel2&user_id=".$from_id))->result->status;
$get = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getMe"));
$botid = $get->result->username;
$namebot = $get->result->first_name;
$contact = $update->message->contact;
$contactid = $contact->user_id;
$contactnum = $contact->phone_number;
$step = $datas['step'];

if(file_exists("menu.txt")){
$button = file_get_contents("menu.txt");
}else{
$button = json_encode(['keyboard'=>[
[['text'=>"$dok1"]],
[['text'=>"$dok16"],['text'=>"$dok3"]],
[['text'=>"$dok4"],['text'=>"$dok12"],['text'=>"$dok5"]],
[['text'=>"$dok18"]],
[['text'=>"$dok10"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"],['text'=>"$dok9"]],
[['text'=>"$dok17"],['text'=>"$dok13"]],
[['text'=>"$dok2"],['text'=>"$dok6"],['text'=>"$dok19"]],
[['text'=>"$dok14"],['text'=>"$dok15"]],
[['text'=>"$dok20"],['text'=>"$dok21"]],
],'resize_keyboard'=>true]);
}

$button1 = json_encode(['keyboard'=>[
[['text'=>"$dok1"]],
[['text'=>"$dok16"],['text'=>"$dok3"]],
[['text'=>"$dok4"],['text'=>"$dok12"],['text'=>"$dok5"]],
[['text'=>"$dok18"]],
[['text'=>"$dok10"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"],['text'=>"$dok9"]],
[['text'=>"$dok17"],['text'=>"$dok13"]],
[['text'=>"$dok2"],['text'=>"$dok6"],['text'=>"$dok19"]],
[['text'=>"$dok14"],['text'=>"$dok15"]],
[['text'=>"$dok20"],['text'=>"$dok21"]],
],'resize_keyboard'=>true]);

$button2 = json_encode(['keyboard'=>[
[['text'=>"$dok1"],['text'=>"$dok3"]],
[['text'=>"$dok16"],['text'=>"$dok5"]],
[['text'=>"$dok4"],['text'=>"$dok12"]],
[['text'=>"$dok18"],['text'=>"$dok9"]],
[['text'=>"$dok10"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"]],
[['text'=>"$dok17"],['text'=>"$dok13"]],
[['text'=>"$dok19"]],
[['text'=>"$dok2"],['text'=>"$dok6"]],
[['text'=>"$dok14"],['text'=>"$dok15"]],
[['text'=>"$dok20"],['text'=>"$dok21"]],
],'resize_keyboard'=>true]);

$button3 = json_encode(['keyboard'=>[
[['text'=>"$dok1"],['text'=>"$dok3"]],
[['text'=>"$dok16"]],
[['text'=>"$dok4"],['text'=>"$dok12"],['text'=>"$dok5"]],
[['text'=>"$dok18"]],
[['text'=>"$dok10"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"],['text'=>"$dok9"]],
[['text'=>"$dok17"],['text'=>"$dok15"],['text'=>"$dok13"]],
[['text'=>"$dok2"],['text'=>"$dok6"],['text'=>"$dok19"]],
[['text'=>"$dok14"]],
],'resize_keyboard'=>true]);


$button4 = json_encode(['keyboard'=>[
[['text'=>"$dok16"],['text'=>"$dok1"],['text'=>"$dok3"]],
[['text'=>"$dok4"],['text'=>"$dok12"],['text'=>"$dok5"]],
[['text'=>"$dok18"],['text'=>"$dok19"]],
[['text'=>"$dok10"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"],['text'=>"$dok9"]],
[['text'=>"$dok17"],['text'=>"$dok13"]],
[['text'=>"$dok21"]],
[['text'=>"$dok2"],['text'=>"$dok6"]],
[['text'=>"$dok20"]],
[['text'=>"$dok14"],['text'=>"$dok15"]],
],'resize_keyboard'=>true]);


$button5 = json_encode(['keyboard'=>[
[['text'=>"$dok1"]],
[['text'=>"$dok16"],['text'=>"$dok3"]],
[['text'=>"$dok4"],['text'=>"$dok12"],['text'=>"$dok5"]],
[['text'=>"$dok10"],['text'=>"$dok9"],['text'=>"$dok11"]],
[['text'=>"$dok7"],['text'=>"$dok8"]],
[['text'=>"$dok17"],['text'=>"$dok18"],['text'=>"$dok13"]],
[['text'=>"$dok2"],['text'=>"$dok6"],['text'=>"$dok19"]],
],'resize_keyboard'=>true]);

@$button_tayeed = json_encode(['keyboard'=>[
[['text'=>"🔐 تایید حساب 🔐"],['text'=>"🔓 ورود به حساب 🔓"]],
],'resize_keyboard'=>true]);

@$button_admin = json_encode(['keyboard'=>[
[['text'=>"آمار 📊"]],
[['text'=>"💡 خاموش و روشن بازی ها"],['text'=>"🌺 بستن آخرین چت"]],
[['text'=>"🎁 تنظیمات بونوس"]],
[['text'=>"بروزرسانی ⚡"],['text'=>"🍭 تنظیم ضریب ها"]],
[['text'=>"انتقال مالکیت ربات 📤"]],
[['text'=>"ارسال همگانی ✨"],['text'=>"فوروارد همگانی ❄️"]],
[['text'=>"بخش ادمین ها 🔋"]],
[['text'=>"پیام به کاربر 📨"],['text'=>"تنظیم متن ها 🎈"]],
[['text'=>"🆔 تنظیم ای دی ها"]],
[['text'=>"آنبلاک کردن ⭐"],['text'=>"بلاک کردن 🍂"]],
[['text'=>"⚙ تنظیمات سکه"]],
[['text'=>"کسر سکه 🥈"],['text'=>"افزایش سکه 🥇"]],
[['text'=>"❕حذف اخطار کاربر"],['text'=>"❗️اخطار به کاربر"]],
[['text'=>"🍁 بخش تنظیم دکمه ها 🍁"]],
[['text'=>"👤 تنظیمات زیر مجموعه"],['text'=>"🔑 چیدمان دکمه ها"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_lol = json_encode(['keyboard'=>[
[['text'=>"❤️ چیدمان نوع اول ❤️"]],
[['text'=>"🧡 چیدمان نوع دوم 🧡"]],
[['text'=>"💚 چیدمان نوع سوم 💚"]],
[['text'=>"💛 چیدمان نوع چهارم 💛"]],
[['text'=>"💜 چیدمان نوع پنجم 💜"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_f = json_encode(['keyboard'=>[
[['text'=>"$dos"]],
[['text'=>"💰 رپید سین"],['text'=>"💰 نیترو سین"]],
[['text'=>"🚀جت ممبر"],['text'=>"سرعت ممبر🚀"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_zir = json_encode(['keyboard'=>[
[['text'=>"🌄 تنظیم عکس زیر مجموعه"]],
[['text'=>"📝 تنظیم متن اصلی بنر زیر مجموعه"]],
[['text'=>"🔖 تنظیم متن زیر بنر اصلی"]],
[['text'=>"🎉 تنظیم اسم دکمه زیر مجموعه"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_f2 = json_encode(['keyboard'=>[
[['text'=>"🔎 وضعیت سهام من"],['text'=>"📊 سهام داران"]],
[['text'=>"📈 خرید سهام"],['text'=>"💰 فروش سهام"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);


$button_b = json_encode(['keyboard'=>[
[['text'=>"🚀 موشک"]],
[['text'=>"💣 بازی دو نفره"],['text'=>"🃏 21"]],
[['text'=>"🏀 بسکتبال"],['text'=>"💣 انفجار"],['text'=>"🔢 مونتی"]],
[['text'=>"✊ گل یا پوچ"]],
[['text'=>"⚽️ فوتبال"],['text'=>"🎳 بولینگ"]],
[['text'=>"👊🏻 سنگ کاغذ قیچی"]],
[['text'=>"🎱 رولت"],['text'=>"💩 پوپ"]],
[['text'=>"🎲 تاس"],['text'=>"🎯 دارت"],['text'=>"🎰 ماشین اسلات"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_dar = json_encode(['keyboard'=>[
[['text'=>"🎯 دارت وسط"]],
[['text'=>"🎯 دارت سفید"],['text'=>"🎯 دارت قرمز"]],
[['text'=>"🎯 دارت بیرون"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_TAK = json_encode(['keyboard'=>[
[['text'=>"بازه ضریب ها پیشرفته 💣"]], 
[['text'=>"تشخیص ضریب💣"],['text'=>"بازه ضریب ها 💣"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_LOVE = json_encode(['keyboard'=>[
[['text'=>"📊 عدد مونتی"]],[['text'=>"📊 ضریب انفجار"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

@$button_date = json_encode(['keyboard'=>[
[['text'=>"💣 تاریخچه انفجار"],['text'=>"🔢 تاریخچه مونتی"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_ASL = json_encode(['keyboard'=>[
[['text'=>"بازه عدد ها پیشرفته 🔢"]],
[['text'=>"تشخیص عدد 🔢"],['text'=>"بازه عدد ها 🔢"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_tas = json_encode(['keyboard'=>[
[['text'=>"🎲 تاس بالا"],['text'=>"🎲 تاس پایین"]],
[['text'=>"🎲 حدس عدد تاس"]],
[['text'=>"🎲 تاس فرد"],['text'=>"🎲 تاس زوج"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_ts = json_encode(['keyboard'=>[
[['text'=>"💰 اِسلُو سین"]],
[['text'=>"💰 نیترو سین"]],[['text'=>"💰 رپید سین"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

@$button_dok = json_encode(['keyboard'=>[
[['text'=>"📊 دکمه های فعلی"]],
[['text'=>"دکمه 📜 تاریخچه بازی ها"],['text'=>"دکمه 💣 ثبت بازی جدید"]],
[['text'=>"دکمه 📊 پیش‌بینی ها"],['text'=>"دکمه 🔐 حساب کاربری"]],
[['text'=>"دکمه 🎗سهام"],['text'=>"دکمه 🆘 پشتیبانی"]],
[['text'=>"دکمه قوانین ربات 📑"],['text'=>"دکمه 💡راهنما"]],
[['text'=>"دکمه ♻️ آپدیت"],['text'=>"دکمه 🌟بخش ویژه"]],
[['text'=>"دکمه ارسال اکانت 💫"],['text'=>"دکمه 💸 بونوس"]],
[['text'=>"دکمه 🎉 هدیه"]],
[['text'=>"دکمه 💰 برداشت موجودی"],['text'=>"دکمه 💵 شارژ حساب"]],
[['text'=>"دکمه ☁️ چت با اعضا ربات"]],
[['text'=>"دکمه 💰 انتقال سکه"],['text'=>"دکمه 🥂 وضعیت ربات"]],
[['text'=>"دکمه 🔥 تراکنش ها"]],
[['text'=>"دکمه 🎁 برترین ها"],['text'=>"دکمه گفت و گو 🔰"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

@$button_add = json_encode(['keyboard'=>[
[['text'=>"📊 آمار ادمین ها"]],
[['text'=>"❌ حذف ادمین 1"],['text'=>"✅ اضافه کردن ادمین 1"]],
[['text'=>"❌ حذف ادمین 2"],['text'=>"✅ اضافه کردن ادمین 2"]],
[['text'=>"❌ حذف ادمین 3"],['text'=>"✅ اضافه کردن ادمین 3"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]); 

@$button_zarib = json_encode(['keyboard'=>[
    [['text'=>"ضریب ⚽️ فوتبال"],['text'=>"ضریب 🏀 بسکتبال"]],
    [['text'=>"ضریب 👊🏻 سنگ کاغذ قیچی"]],
    [['text'=>"ضریب 🎱 رولت"],['text'=>"ضریب 💩 پوپ"]],
    [['text'=>"ضریب 🎰 ماشین اسلات"]],
    [['text'=>"ضریب 🎲 تاس"],['text'=>"ضریب 🎯 دارت"]],
    [['text'=>"ضریب ✊ گل یا پوچ"]],
    [['text'=>"ضریب 🎳 بولینگ"],['text'=>"ضریب 🔢 مونتی"]],
    [['text'=>"🔙 بازگشت"]],
     ],'resize_keyboard'=>true]);
     
@$button_dartset = json_encode(['keyboard'=>[
[['text'=>"ضریب 🎯 دارت قرمز"]],
[['text'=>"ضریب 🎯 دارت سفید"]],
[['text'=>"ضریب 🎯 دارت بیرون"]],
[['text'=>"ضریب 🎯 دارت وسط"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

@$button_taset = json_encode(['keyboard'=>[
[['text'=>"ضریب 🎲 حدس عدد تاس"]],
[['text'=>"ضریب 🎲 تاس فرد و زوج"]],
[['text'=>"ضریب 🎲 تاس بالا و پایین"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

@$button_chat = json_encode(['keyboard'=>[
[['text'=>"📊 آمار گروه چت"]],
[['text'=>"بستن چت ❌"],['text'=>"📨 ارسال پیام"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_tar = json_encode(['keyboard'=>[
[['text'=>"🎈 لیست آخرین برداشتی ها"]],
[['text'=>"💸 لیست آخرین شارژ حساب"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

$button_paly = json_encode(['keyboard'=>[
[['text'=>"📩 گفتگو کردن"],['text'=>"🎮 بریم بازی"]],
[['text'=>"⚖ قوانین"],['text'=>"📚 راهنما"]],
[['text'=>"📛گزارش بازیکن"],['text'=>"❌ قطع اتصال"]],
],'resize_keyboard'=>true]);

$button_miz_tas = json_encode(['keyboard'=>[
[['text'=>"🎈 بازیکنان حاضر"]],
[['text'=>"🔰 لغو شروع"],['text'=>"✅ شروع بازی"]],
[['text'=>"⚖ قوانین بازی"],['text'=>"📚 راهنمای بازی"]],
[['text'=>"❗️گزارش بازیکنان"],['text'=>"❌ قطع اتصال"]],
],'resize_keyboard'=>true]);

$button_sang = json_encode(['keyboard'=>[
[['text'=>"✌️🏻"],['text'=>"👊🏻"],['text'=>"🖐🏻"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);

$button_coin = json_encode(['keyboard'=>[
[['text'=>"♻️ ریست تنظیمات سکه"]],
[['text'=>"💰 تنظیم سکه حداقل شارژ"]],
[['text'=>"💰 تنظیم سکه زیر مجموعه"]],
[['text'=>"💰 تنظیم حداقل سکه برای برداشت"]],
[['text'=>"💰 تنظیم حداکثر سکه برای برداشت"]],
[['text'=>"💰 تنظیم حداقل سکه برای شرط"]],
[['text'=>"💰 تنظیم حداقل سکه جهت انتقال"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_bons = json_encode(['keyboard'=>[
[['text'=>"🎁 تنظیم حداقل بونوس هدیه"]],
[['text'=>"🎁 تنظیم حداکثر بونوس هدیه"]],
[['text'=>"🎁 تنظیم بونوس خوش آمد گویی"]],
[['text'=>"🎁 تنظیم بونوس شارژ بیشتر"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_matn = json_encode(['keyboard'=>[
[['text'=>"تنظیم متن بونوس"]],
[['text'=>"تنظیم متن راهنما"],['text'=>"تنظیم متن سهام"]],
[['text'=>"تنظیم متن قوانین"]],
[['text'=>"تنظیم متن گفت و گو"],['text'=>"تنظیم متن ارسال اکانت"]],
[['text'=>"تنظیم برترین ها"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_id = json_encode(['keyboard'=>[
[['text'=>"🥂 تنظیم ای دی کانال موشک"],['text'=>"🥂 تنظیم ای دی کانال دارت"]],
[['text'=>"🥂 تنظیم ای دی کانال بازی ها"],['text'=>"🥂 تنظیم ای دی پشتیبانی"]],
[['text'=>"🥂 تنظیم ای دی گفت و گو"]],
[['text'=>"🥂 تنظیم کانال دوم"],['text'=>"🥂 تنظیم کانال اول"]],
[['text'=>"🥂 تنظیم ای دی عددی برای شارژ حساب"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_bartar = json_encode(['keyboard'=>[
[['text'=>"تنظیم نفر اول برترین ها"]],
[['text'=>"تنظیم نفر سوم برترین ها"],['text'=>"تنظیم نفر دوم برترین ها"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_Special = json_encode(['keyboard'=>[
[['text'=>"💬 کامنت گذاشتن"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

@$button_off = json_encode(['keyboard'=>[
[['text'=>"خاموش کردن بازی 🃏 21"]],
[['text'=>"خاموش کردن بازی 🏀 بسکتبال"]],
[['text'=>"خاموش کردن بازی 🔢 مونتی"]],
[['text'=>"خاموش کردن بازی 💣 انفجار"]],
[['text'=>"خاموش کردن بازی ✊ گل یا پوچ"]],
[['text'=>"خاموش کردن بازی ⚽️ فوتبال"]],
[['text'=>"خاموش کردن بازی 🎳 بولینگ"]],
[['text'=>"خاموش کردن بازی 💩 پوپ"]],
[['text'=>"خاموش کردن بازی 👊🏻 سنگ کاغذ قیچی"]],
[['text'=>"خاموش کردن بازی 🎱 رولت"]],
[['text'=>"خاموش کردن بازی 🎰 ماشین اسلات"]],
[['text'=>"خاموش کردن بازی 🎲 تاس"]],
[['text'=>"خاموش کردن بازی 🎯 دارت"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

@$button_on = json_encode(['keyboard'=>[
[['text'=>"روشن کردن بازی 🃏 21"]],
[['text'=>"روشن کردن بازی 🏀 بسکتبال"]],
[['text'=>"روشن کردن بازی 🔢 مونتی"]],
[['text'=>"روشن کردن بازی 💣 انفجار"]],
[['text'=>"روشن کردن بازی ✊ گل یا پوچ"]],
[['text'=>"روشن کردن بازی ⚽️ فوتبال"]],
[['text'=>"روشن کردن بازی 🎳 بولینگ"]],
[['text'=>"روشن کردن بازی 💩 پوپ"]],
[['text'=>"روشن کردن بازی 👊🏻 سنگ کاغذ قیچی"]],
[['text'=>"روشن کردن بازی 🎱 رولت"]],
[['text'=>"روشن کردن بازی 🎰 ماشین اسلات"]],
[['text'=>"روشن کردن بازی 🎲 تاس"]],
[['text'=>"روشن کردن بازی 🎯 دارت"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

if($warn >= 3){
SendMessage($from_id,"❌ شما از ربات مسدود شدید");
exit();
}
if($step =="bon" and !in_array($from_id,$admin) and $type == "private"){
exit();}
/////////////////////////////// inline ///////////////////////////////
if(strpos($data, "prooo") !== false){
answerCallbackQuery($callbackid,'این دکمه ها فقط برای دریافت اطلاعات هستند !',false);
}
if(strpos($data,'report') !== false){
$id = str_replace('report',null,$data);
if($chatid != $id){
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✔️ دلیل گزارش این کاربر را انتخاب کنید
", 'parse_mode'=>'MarkDown','disable_web_page_preview'=> true,
'message_id'=> $messageid,
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"❗️بی احترامی",'callback_data'=>"biaht$id"]],
[['text'=>"❗️فحاشی",'callback_data'=>"fosh$id"]],
[['text'=>"❗️تقلب در بازی",'callback_data'=>"taqe$id"]],
[['text'=>"❗️به هم ریختن نظم بازی",'callback_data'=>"binazm$id"]],
[['text'=>"❗️تبلیغات",'callback_data'=>"adser$id"]],
]])
]);
}else{
answerCallbackQuery($callbackid,'نمیتوانید خودتان را گزارش کنید 🔥',false);
}
}
if(strpos($data,'biaht') !== false){
$id = str_replace('biaht',null,$data);
$reps = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$repname = $reps->result->user->first_name;
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✅ کاربر $repname با موفقیت گزارش شد 
",'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=> $messageid,
]);
SendMessage($owner,"
🎲 در بازی گروهی تاس یک نفر را گزارش کردند 


💣 اطلاعات فردی که گزارش شده : 

💸 اسم : $repname
🔋 عددی : $id


💣 فردی که گزارش کرده است : 

💸 اسم : $firstname
🔋 عددی : $chatid



🎈 دلیل گزارش : بی احترامی
");
}
if(strpos($data,'fosh') !== false){
$id = str_replace('fosh',null,$data);
$reps = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$repname = $reps->result->user->first_name;
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✅ کاربر $repname با موفقیت گزارش شد 
",'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=> $messageid,
]);
SendMessage($owner,"
🎲 در بازی گروهی تاس یک نفر را گزارش کردند 


💣 اطلاعات فردی که گزارش شده : 

💸 اسم : $repname
🔋 عددی : $id


💣 فردی که گزارش کرده است : 

💸 اسم : $firstname
🔋 عددی : $chatid



🎈 دلیل گزارش : فحاشی
");
}
if(strpos($data,'taqe') !== false){
$id = str_replace('taqe',null,$data);
$reps = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$repname = $reps->result->user->first_name;
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✅ کاربر $repname با موفقیت گزارش شد 
",'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=> $messageid,
]);
SendMessage($owner,"
🎲 در بازی گروهی تاس یک نفر را گزارش کردند 


💣 اطلاعات فردی که گزارش شده : 

💸 اسم : $repname
🔋 عددی : $id


💣 فردی که گزارش کرده است : 

💸 اسم : $firstname
🔋 عددی : $chatid



🎈 دلیل گزارش : تقلب در بازی
");
}
if(strpos($data,'binazm') !== false){
$id = str_replace('binazm',null,$data);
$reps = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$repname = $reps->result->user->first_name;
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✅ کاربر $repname با موفقیت گزارش شد 
",'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=> $messageid,
]);
SendMessage($owner,"
🎲 در بازی گروهی تاس یک نفر را گزارش کردند 


💣 اطلاعات فردی که گزارش شده : 

💸 اسم : $repname
🔋 عددی : $id


💣 فردی که گزارش کرده است : 

💸 اسم : $firstname
🔋 عددی : $chatid



🎈 دلیل گزارش : به هم زدن نظم بازی
");
}
if(strpos($data,'adser') !== false){
$id = str_replace('adser',null,$data);
$reps = bot('getChatMember',['chat_id'=>"$id",'user_id'=>"$id"]);
$repname = $reps->result->user->first_name;
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
✅ کاربر $repname با موفقیت گزارش شد 
",'parse_mode'=>'MarkDown',
'disable_web_page_preview'=> true,
'message_id'=> $messageid,
]);
SendMessage($owner,"
🎲 در بازی گروهی تاس یک نفر را گزارش کردند 


💣 اطلاعات فردی که گزارش شده : 

💸 اسم : $repname
🔋 عددی : $id


💣 فردی که گزارش کرده است : 

💸 اسم : $firstname
🔋 عددی : $chatid



🎈 دلیل گزارش : تبلیغات 
");
}
if(strpos($data, "comment") !== false){
$datas["step"] = "come3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$chatid/$chatid.json",$outjson);
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
📨 پیام کامنت خود را بفرستید . . .

", 'parse_mode'=>'MarkDown','disable_web_page_preview'=> true,
'message_id'=> $messageid,
'reply_markup'=>json_encode([
      'inline_keyboard'=>[
      [
   ['text'=>"🔙 برگشت",'callback_data'=>'back page']
    ],
  ]])
]);
}
if(strpos($data, "Rules") !== false){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$chatid/$chatid.json",$outjson);
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
⚙ قوانین کامنت گذاشتن گذاشتن : 


1 - فحاشی و توهین به دیگران ممنوع 
2 - تبلیغات ممنوع 
3 - درخواست های نا به جا ممنوع 
4 - احترام واجب 
5 - نظرات خود را به اشتراک بگذارید

", 'parse_mode'=>'MarkDown','disable_web_page_preview'=> true,
'message_id'=> $messageid,
'reply_markup'=>json_encode([
      'inline_keyboard'=>[
      [
   ['text'=>"🔙 برگشت",'callback_data'=>'back page']
    ],
  ]])
]);
}
if(strpos($data, "back page") !== false){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$chatid/$chatid.json",$outjson);
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"
بخش کامنت ها 💬 میتوانید نظرات خود را به اشتراک بگذارید . . . 

┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment1
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment2
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment3
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
", 'parse_mode'=>'MarkDown','disable_web_page_preview'=> true,
'message_id'=> $messageid,
'reply_markup'=>json_encode([
      'inline_keyboard'=>[
      [
      ['text'=>"💬 کامنت گذاشتن",'callback_data'=>'comment']
      ],
      [
      ['text'=>"💣 قوانین کامنت گذاشتن",'callback_data'=>'Rules']
      ],
      [
      ['text'=>"♻️ آپدیت",'callback_data'=>'up date']
      ],
  ]])
]);
}

if(strpos($data, "up date") !== false){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$chatid/$chatid.json",$outjson);
bot('editMessagetext',[
'chat_id' => $chatid,
'text'=>"بخش کامنت ها 💬 میتوانید نظرات خود را به اشتراک بگذارید . . . 

┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment1
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment2
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment3
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄ 
ᴜᴘᴅᴀᴛᴇᴅ ᴀᴛ : $time
─━─━─━─━─━─━─┛
", 'parse_mode'=>'MarkDown','disable_web_page_preview'=> true,
'message_id'=> $messageid,
'reply_markup'=>json_encode([
      'inline_keyboard'=>[
      [
      ['text'=>"💬 کامنت گذاشتن",'callback_data'=>'comment']
      ],
      [
      ['text'=>"💣 قوانین کامنت گذاشتن",'callback_data'=>'Rules']
      ],
      [
      ['text'=>"♻️ آپدیت",'callback_data'=>'up date']
      ],
  ]])
]);
} 
/////////////////////////////// end of inline ///////////////////////////////
if(preg_match('/^\/([Ss]tart)(.*)/',$text,$match) and $type == "private"){
$match[2] = str_replace(" ","",$match[2]);
$match[2] = str_replace("\n","",$match[2]);
if($match[2] == "Slow" or $match[2] == "settings" or $match[2] == "Automatic"or $match[2] == "Game"){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["money"] = "$bonos3";
$datas["step"] = "free";
$datas["subset"] = "0";
$datas["id"] = "$match[2]";
$datas["warn"] = "0";
$datas["tayeed"] = "false";
$datas["saham"] = "0";
$datas["game"] = "0";
$datas["coin"] = "0";
$datas["zarib"] = "0";
$datas["hads"] = "0";
$datas["DartCoin"] = "0";
$datas["bardasht"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
}
if($match[2] == "Slow"){
$random = glob('play/*/game.txt');
if($random == true){
foreach($random as $player){
$file = str_replace("play/", "", $player);
$LOL = file_get_contents("play/$file");
$guess = explode("~","$LOL");
$sgahau = file_get_contents("allcoin.txt");
$hdkwpcl = $sgahau + $guess[1];
file_put_contents("allcoin.txt","$hdkwpcl");
$hehakk = file_get_contents("allplayer.txt");
$hshagut = $hehakk + 1;
file_put_contents("allplayer.txt","$hshagut");
$coin = number_format("$guess[1]");
$a = substr($guess[2], 3, 4);
$guhe = str_replace($a, str_repeat('*', strlen($a)), $guess[2]);
$ids .= "$guhe | $coin COIN | -----\n➖➖➖➖➖\n";
}
$nume = file_get_contents("allplayer.txt");
$coinall = file_get_contents("allcoin.txt");
SendMessage($from_id,"
👤 امار بازیکنان این دست :
 
👥 مجموع بازیکنان این دست : $nume بازیکن

💰 مجموع مقدار سکه ها این دست : $coinall سکه 
➖➖➖➖➖
$ids
");
unlink("allplayer.txt");
unlink("allcoin.txt");
exit();
}else{
SendMessage($from_id,"
👤 امار بازیکنان این دست :
 
👥 مجموع بازیکنان این دست : 0 بازیکن

💰 مجموع مقدار سکه ها این دست : 0 سکه 
➖➖➖➖➖
❌ هیچ بازیکنی در این دست بازی نکرده است.
");
}
exit();
}
if($match[2] == "Game"){
$datas["step"] = "sksksk";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Rocket Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
if($match[2] == "Automatic"){
$atozar = $datas["zarib"];
$myatocoin = $datas["coin"];
if($atozar != "0"){
if($datas['money'] > 100 and $datas['money'] > $myatocoin){
mkdir("play");
$rand = rand(111111111,999999999);
mkdir("play/$rand");
file_put_contents("play/$rand/game.txt","$atozar~$myatocoin~$from_id");
$jsonID = json_decode(file_get_contents("play/id.json"),true);
$jsonID["id"][] = "$from_id";
$jsonID = json_encode($jsonID,true);
file_put_contents("play/id.json",$jsonID);
$datas["step"] = "none";
$newmoney = $datas['money'] - $myatocoin;
$datas["money"] = "$newmoney";
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $myatocoin;
$datas["bardasht"] = $mojt;
}
$newgame = $datas['game'] + 1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
💣 Rocket Panel :
              
💰 مقدار سکه ثبت شده : $myatocoin سکه (اتوماتیک)

💣 ضریب درخواستی : $atozar (اتوماتیک)


➖➖➖➖➖
🚀 برای مشاهده وضعیت بازی در کانال زیر عضو شوید و بازی خود را تماشا کنید :

🚀 $moshak


",json_encode(['inline_keyboard' => [
[['text' => "⚙ تنظیمات موشک",'url' => "https://t.me/$botid?start=settings"]],
[['text' => "🔥 شروع بازی اتوماتیک موشک 🔥",'url' => "https://t.me/$botid?start=Automatic"]],
]]));
exit();
}else{
SendMessage($from_id,"
❌ حداقل باید 100 سکه در حساب خود داشته باشید 


❌ موجودی شما کمتر از سکه ثبت شده اتوماتیک می‌باشد 
",json_encode(['inline_keyboard' => [
[['text' => "⚙ تنظیمات موشک",'url' => "https://t.me/$botid?start=settings"]],
]]));
exit();
}
}else{
$datas["step"] = "akakajkk";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Rocket Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
}
if($match[2] == "settings"){
$datas["step"] = "akakajkk";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Rocket Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
if($match[2] == "SlowDart"){
$random = glob('dart/*/game.txt');
if($random == true){
foreach($random as $darter){
$file = str_replace("dart/", "", $darter);
$LOL = file_get_contents("dart/$file");
$guess = explode("~","$LOL");
$sgahau = file_get_contents("allDartCoin.txt");
$hdkwpcl = $sgahau + $guess[1];
file_put_contents("allDartCoin.txt","$hdkwpcl");
$hehakk = file_get_contents("alldarter.txt");
$hshagut = $hehakk + 1;
file_put_contents("alldarter.txt","$hshagut");
$DartCoin = number_format("$guess[1]");
$a = substr($guess[2], 3, 4);
$guhe = str_replace($a, str_repeat('*', strlen($a)), $guess[2]);
$ids .= "$guhe | $DartCoin Coin | -----\n➖➖➖➖➖\n";
}
$nume = file_get_contents("alldarter.txt");
$DartCoinall = file_get_contents("allDartCoin.txt");
SendMessage($from_id,"
👤 امار بازیکنان این دست :
 
👥 مجموع بازیکنان این دست : $nume بازیکن

💰 مجموع مقدار سکه ها این دست : $DartCoinall سکه 
➖➖➖➖➖
$ids
");
unlink("alldarter.txt");
unlink("allDartCoin.txt");
exit();
}else{
SendMessage($from_id,"
👤 امار بازیکنان این دست :
 
👥 مجموع بازیکنان این دست : 0 بازیکن

💰 مجموع مقدار سکه ها این دست : 0 سکه 
➖➖➖➖➖
❌ هیچ بازیکنی در این دست بازی نکرده است.
");
}
exit();
}
if($match[2] == "Dart"){
$datas["step"] = "DaRtInG";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Dart Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
if($match[2] == "DartAuto"){
$hads = $datas["hads"];
$DartPanel = ['Dartred','Dartwhite'];
$keys = [$DartPanel[0]=>'🎯 دارت قرمز گروهی',$DartPanel[1]=>'🎯 دارت سفید گروهی'];
$select = $keys[$hads];
$myatoDartCoin = $datas["DartCoin"];
if($hads != "0"){
if($datas['money'] > 100 and $datas['money'] > $myatoDartCoin){
mkdir("dart");
$rand = rand(111111111,999999999);
mkdir("dart/$rand");
file_put_contents("dart/$rand/game.txt","$hads~$myatoDartCoin~$from_id");
$jsonID = json_decode(file_get_contents("dart/id.json"),true);
$jsonID["id"][] = "$from_id";
$jsonID = json_encode($jsonID,true);
file_put_contents("dart/id.json",$jsonID);
$datas["step"] = "none";
$newmoney = $datas['money'] - $myatoDartCoin;
$datas["money"] = "$newmoney";
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $myatoDartCoin;
$datas["bardasht"] = $mojt;
}
$newgame = $datas['game'] + 1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
💣 Dart Panel :
              
💰 مقدار سکه ثبت شده : $myatoDartCoin سکه (اتوماتیک)

🎯 نوع دارت : $select (اتوماتیک)


➖➖➖➖➖
🚀 برای مشاهده وضعیت بازی در کانال زیر عضو شوید و بازی خود را تماشا کنید :

🚀 $DartChannel


",json_encode(['inline_keyboard' => [
[['text' => "⚙ تنظیمات دارت گروهی",'url' => "https://t.me/$botid?start=settingsDart"]],
[['text' => "🔥 شروع بازی اتوماتیک دارت گروهی 🔥",'url' => "https://t.me/$botid?start=DartAuto"]],
]]));
exit();
}else{
SendMessage($from_id,"
❌ حداقل باید 100 سکه در حساب خود داشته باشید 


❌ موجودی شما کمتر از سکه ثبت شده اتوماتیک می‌باشد 
",json_encode(['inline_keyboard' => [
[['text' => "⚙ تنظیمات دارت گروهی",'url' => "https://t.me/$botid?start=settingsDart"]],
]]));
exit();
}
}else{
$datas["step"] = "BehHv";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Dart Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
}
if($match[2] == "settingsDart"){
$datas["step"] = "BehHv";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Dart Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
exit();
}
if($match[2] != null and $match[2] != $from_id and $datas['id'] == false){
$amar = file_get_contents("data/datas.txt");
$exp = explode("\n",$amar);
if(!in_array( $from_id, $exp) and $from_id != $match[2]){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator'){
SendMessage($from_id,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel2

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel2"]],]]));
$datas["step"] = "ozv-$match[2]";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
exit();
}else{
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["money"] = "$bonos3";
$datas["step"] = "free";
$datas["subset"] = "0";
$datas["id"] = "$match[2]";
$datas["warn"] = "0";
$datas["tayeed"] = "false";
$datas["saham"] = "0";
$datas["game"] = "0";
$datas["coin"] = "0";
$datas["zarib"] = "0";
$datas["hads"] = "0";
$datas["DartCoin"] = "0";
$datas["bardasht"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$userZir2 = json_decode(file_get_contents("data/$match[2]/$match[2].json"),true);
$coinc = $userZir2['money'] + $zir;
$inv = $userZir2['subset'] + 1;
 SendMessage($match[2],"🎉 تبریک کاربر | <a href ='tg://user?id=$from_id'>$first_name</a> | با لینک شما وارد ربات شد . . .
🎊 تعداد $zir سکه به حساب شما اضافه شد . . .");
$userZir2["subset"] = $inv;
$userZir2["money"] = $coinc;
$userZir2["step"] = "none";
$outjson = json_encode($userZir2,true);
file_put_contents("data/$match[2]/$match[2].json",$outjson);
}
}
}
$tayeed = $datas["tayeed"];
if($tayeed == true){
SendMessage($from_id,"
💣 سلام <a href ='tg://user?id=$from_id'>$first_name</a> به $namebot خوش آمدید 🌹

💰 برای شروع بازی جدید بر روی ثبت شرط کلیک کنید
",$button);
}else{
SendMessage($from_id,"
💣 به $namebot سکه خوش امدید . . .

💰 با $namebot سکه شما میتوانید در کمترین زمان ممکن سکه های خود را چند برابر کنید . . .

⁦🔐⁩ برای شروع لطفا ابتدا حساب خود را تایید و وارد حساب خود شوید . . .
",$button_tayeed);
}}
if($text =="🔐 تایید حساب 🔐" and $type == "private"){
 if(!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
$tayeed = $datas["tayeed"];
if($tayeed != true){
$datas["tayeed"] = "true";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🔥 حساب کاربری شما با موفقیت ثبت و تایید شد ، هم اکنون میتوانید وارد حساب خود شوید.
",$button_tayeed);
}else{
$datas["tayeed"] = "true";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🔥 حساب کاربری شما مورد تایید میباشد!
");
}}
if($text == "🔓 ورود به حساب 🔓"){
 if(!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
$tayeed = $datas["tayeed"];
if($tayeed == true){
SendMessage($from_id,"
💣 سلام <a href ='tg://user?id=$from_id'>$first_name</a> به $namebot خوش آمدید 🌹

💰 برای شروع بازی جدید بر روی ثبت شرط کلیک کنید
",$button);
}else{
SendMessage($from_id,"
🔥 حساب شما فعال نمیباشد از گزینه زیر اقدام به فعالسازی کنید . . .
",$button_tayeed);
}
}
if(strpos($step, "ozv-") !== false and isset($text) and $type == "private"){
$amar = file_get_contents("data/datas.txt");
$exp = explode("\n",$amar);
if(!in_array( $from_id, $exp) and $from_id != $b[1]){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator'){
SendMessage($from_id,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel2

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel2"]],]]));exit();
}else{
$b = explode("-",$step);
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["money"] = "$bonos3";
$datas["step"] = "free";
$datas["subset"] = "0";
$datas["id"] = "$b[1]";
$datas["warn"] = "0";
$datas["tayeed"] = "false";
$datas["saham"] = "0";
$datas["game"] = "0";
$datas["coin"] = "0";
$datas["zarib"] = "0";
$datas["hads"] = "0";
$datas["DartCoin"] = "0";
$datas["bardasht"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$userZir1 = json_decode(file_get_contents("data/$b[1]/$b[1].json"),true);
$coinc = $userZir1['money'] + $zir;
$inv = $userZir1['subset'] + 1;
 SendMessage($b[1],"🎉 تبریک کاربر | <a href ='tg://user?id=$from_id'>$first_name</a> | با لینک شما وارد ربات شد . . .
🎊 تعداد $zir سکه به حساب شما اضافه شد . . .");
$userZir1["subset"] = $inv;
$userZir1["money"] = $coinc;
$userZir1["step"] = "none";
$outjson = json_encode($userZir1,true);
file_put_contents("data/$b[1]/$b[1].json",$outjson);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}}}
$tayeed = $datas["tayeed"];
if($tayeed == true or $text == "/start" or $text == "🔓 ورود به حساب 🔓" or $text == "🔐 تایید حساب 🔐"){
}else{
SendMessage($from_id,"
🔥 حساب شما فعال نمیباشد از گزینه زیر اقدام به فعالسازی کنید . . .
",$button_tayeed);exit();
}
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["money"] = "$bonos3";
$datas["step"] = "free";
$datas["subset"] = "0";
$datas["id"] = "none";
$datas["warn"] = "0";
$datas["tayeed"] = "false";
$datas["saham"] = "0";
$datas["game"] = "0";
$datas["coin"] = "0";
$datas["zarib"] = "0";
$datas["hads"] = "0";
$datas["DartCoin"] = "0";
$datas["bardasht"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if(file_exists("channel.txt") and file_exists("channel2.txt")){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator'){
SendMessage($from_id,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel2

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel2"]],]]));exit();
}
}
if(file_exists("channel.txt")){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator'){
SendMessage($from_id,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel"]],]]));exit();
}
}
$mizer3 = file_get_contents("tas/miz3/users.txt");
$exp3 = explode("\n",$mizer3);
if(in_array($from_id,$exp3)){
$datas["step"] = "ingamerts3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
$mizer2 = file_get_contents("tas/miz2/users.txt");
$exp2 = explode("\n",$mizer2);
if(in_array($from_id,$exp2)){
$datas["step"] = "ingamerts2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
$mizer1 = file_get_contents("tas/miz1/users.txt");
$exp1 = explode("\n",$mizer1);
if(in_array($from_id,$exp1)){
$datas["step"] = "ingamerts1";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($text =="🔙 بازگشت" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"✅ به منوی اصلی بازگشتید",$button);
}
if($text =="$dok2" and $type == "private"){
SendMessage($from_id,"
🎈 در این بخش میتوانید ضریب های بازی انفجار و عدد های پشبینی شده بازی مونتی رورو ببینید و در بازی های مونتی و انفجار شانس بیشتری داشته باشید و میلیونی نیترو برنده بشوید 🎉
",$button_LOVE);
}

if($text =="📊 ضریب انفجار" and $type == "private"){
SendMessage($from_id,"
🎈 در این بخش میتوانید ضریب ها رو ببینید و در بازی انفجار شانس بیشتری داشته باشید 🎉
",$button_TAK);
}
if($text =="📊 عدد مونتی" and $type == "private"){
SendMessage($from_id,"
🎈 در این بخش میتوانید عدد ها رو ببینید و در بازی مونتی شانس بیشتری داشته باشید 🎉
",$button_ASL);
}
if($text =="$dok4" and $type == "private"){
$datas["step"] = "sup";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👈🏻 برای ارتباط مستقیم با ادمین با ایدی زیر ارتباط پیدا کنید .
👉🏻 @$poshtiban
🔍 اگر ریپورت هستید میتوانید متن خود را در همین ربات ارسال نمائید ! ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step =="sup" and $text !="🔙 بازگشت" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"✅ پیام شما به پشتیبانی ارسال شد !

جهت دریافت پاسخ خود فوروارد خود را باز کنید ❗️");
bot('forwardMessage',['chat_id'=>$addi,'from_chat_id'=>$from_id,'message_id'=>$message_id]);
}
if($text =="بازه عدد ها 🔢" and $type == "private"){
$T = rand(1,100);
$A = rand(1,100);
$B = rand(1,100);
$C = rand(1,100);
$D = rand(1,100);
$E = rand(1,100);
$F = rand(1,100);
$G = rand(1,100);
$H = rand(1,100);
SendMessage($from_id,"
🔢  بازه ی عدد های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊

┏━━━━━━━━━━━━┓
<code>𝟏 - $A</code>
┄┅┈┉┅┉┈┅┄
<code>𝟐 - $B</code>
┄┅┈┉┅┉┈┅┄
<code>𝟑 - $C</code>
┄┅┈┉┅┉┈┅┄
<code>𝟒 - $D</code>
┄┅┈┉┅┉┈┅┄
<code>𝟓 - $E</code>
┄┅┈┉┅┉┈┅┄
<code>𝟔 - $F</code>
┄┅┈┉┅┉┈┅┄
<code>𝟕 - $G</code>
┄┅┈┉┅┉┈┅┄
<code>𝟖 - $H</code>
┄┅┈┉┅┉┈┅┄
<code>𝟗 - $T</code>
┗━━━━━━━━━━━━┛
",$button_ASL);
}
if($text =="بازه ضریب ها پیشرفته 💣" and $type == "private"){
$T = rand(99,200)/100;
$A = rand(99,200)/100;
$B = rand(99,200)/100;
$C = rand(99,200)/100;
$D = rand(99,200)/100;
$E = rand(99,200)/100;
$F = rand(99,200)/100;
$G = rand(99,200)/100;
$H = rand(99,200)/100;
SendMessage($from_id,"💣 بازه ی ضریب های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊
",json_encode(['inline_keyboard' => [
[['text'=>"┏━━━━━━━━━━━━┓",'callback_data'=>'ping']],
[['text'=>"𝟏-",'callback_data'=>'ping'],['text'=>"$A",'callback_data'=>'ping']],
[['text'=>"𝟐-",'callback_data'=>'ping'],['text'=>"$B",'callback_data'=>'ping']],
[['text'=>"𝟑-",'callback_data'=>'ping'],['text'=>"$C",'callback_data'=>'ping']],
[['text'=>"𝟒-",'callback_data'=>'ping'],['text'=>"$D",'callback_data'=>'ping']],
[['text'=>"𝟓-",'callback_data'=>'ping'],['text'=>"$E",'callback_data'=>'ping']],
[['text'=>"𝟔-",'callback_data'=>'ping'],['text'=>"$F",'callback_data'=>'ping']],
[['text'=>"𝟕-",'callback_data'=>'ping'],['text'=>"$G",'callback_data'=>'ping']],
[['text'=>"𝟖-",'callback_data'=>'ping'],['text'=>"$H",'callback_data'=>'ping']],
[['text'=>"𝟗-",'callback_data'=>'ping'],['text'=>"$T",'callback_data'=>'ping']],
[['text'=>"┗━━━━━━━━━━━━┛",'callback_data'=>'ping']],
]]));exit();
}
if($text =="بازه عدد ها پیشرفته 🔢" and $type == "private"){
$T = rand(1,100);
$A = rand(1,100);
$B = rand(1,100);
$C = rand(1,100);
$D = rand(1,100);
$E = rand(1,100);
$F = rand(1,100);
$G = rand(1,100);
$H = rand(1,100);
SendMessage($from_id,"🔢  بازه ی عدد های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊
",json_encode(['inline_keyboard' => [
[['text'=>"┏━━━━━━━━━━━━┓",'callback_data'=>'ping']],
[['text'=>"𝟏-",'callback_data'=>'ping'],['text'=>"$A",'callback_data'=>'ping']],
[['text'=>"𝟐-",'callback_data'=>'ping'],['text'=>"$B",'callback_data'=>'ping']],
[['text'=>"𝟑-",'callback_data'=>'ping'],['text'=>"$C",'callback_data'=>'ping']],
[['text'=>"𝟒-",'callback_data'=>'ping'],['text'=>"$D",'callback_data'=>'ping']],
[['text'=>"𝟓-",'callback_data'=>'ping'],['text'=>"$E",'callback_data'=>'ping']],
[['text'=>"𝟔-",'callback_data'=>'ping'],['text'=>"$F",'callback_data'=>'ping']],
[['text'=>"𝟕-",'callback_data'=>'ping'],['text'=>"$G",'callback_data'=>'ping']],
[['text'=>"𝟖-",'callback_data'=>'ping'],['text'=>"$H",'callback_data'=>'ping']],
[['text'=>"𝟗-",'callback_data'=>'ping'],['text'=>"$T",'callback_data'=>'ping']],
[['text'=>"┗━━━━━━━━━━━━┛",'callback_data'=>'ping']],
]]));exit();
}

if($text =="تشخیص عدد 🔢" and $type == "private"){
$S = rand(1,100);
SendMessage($from_id,"
🔢  Forecast panel : 

🚀 عدد این دست در ⇲ <code>$S</code> ⇱ پیشبینی شده است ᱹᱹᱹ
این عدد مونتی فقط برای ربات ما است ❗️

 🥀 : $botid
",$button_ASL);
}
elseif($text == "$dok13"){
if($datas['money'] >= $poof){
$datas["step"] = "sjxjdkcldk";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💸 در این بخش میتوانید سکه های خودتان را به سایر کاربران ربات انتقال دهید ! 
💰 موجودی شما : {$datas['money']} سکه

💣 تعداد سکه موردنظر برای انتقال را وارد کنید :",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"⚠️ این انتقال قابل انجام نیست ; برای انتقال نیاز است که $poof سکه داخل حساب خودتان باقی بماند ....",$button);exit();
}}
elseif($step == 'sjxjdkcldk' and is_numeric($text) and $text != "🔙 بازگشت"){
if($text >= $poof and $text <= 1000000000000000 and $datas['money'] >= $text){
$datas["step"] = "transfer-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"♨️ توجه : عملیات انتقال سکه غیرقابل بازگشت است !

👈 درصورتی که درخواست انتقال $text سکه مورد تاییدتان است , شناسه کاربری مقصد را ارسال کنید ...",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"⚠️ حداقل مقدار قابل انتقال $poof سکه است .",$button);
}}
if(strpos($step, "transfer-") !== false and $text != "🔙 بازگشت" and $text != "$from_id"){
if(file_exists("data/$text/$text.json")){
$ssss = explode("-",$step);
$coin = $datas['money'] - $ssss[1];
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"<code>✅ تعداد $ssss[1] سکه در تاریخ $date ساعت $time با موفقیت به کاربر $text انتقال داده شد !</code>",$button);
 $userdata = json_decode(file_get_contents("data/$text/$text.json"),true);
 $coin = $userdata['money'] + $ssss[1];
 SendMessage($text,"<code>✅ تعداد $ssss[1] سکه در تاریخ $date ساعت $time با موفقیت از کاربر $from_id دریافت شد !</code>",$button);
 $datas["money"] = "$coin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$text/$text.json",$outjson);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌  ایدی عددی کاربری که ارسال کردید در ربات نیست",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}  

if($text =="$dok15" and $type == "private"){
$load = sys_getloadavg();
$memm = memory_get_usage();
$ver = phpversion(); 
$alluser = file_get_contents("data/datas.txt");
$alaki = explode("\n",$alluser);
$a = count($alaki);
SendMessage($from_id,"<code>وضعیت ربات و اطلاعات ربات در تاریخ $date و ساعت $time شامل موارد زیر است 👇</code>

",json_encode(['inline_keyboard' => [
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'ping']],
[['text'=>"$load[0]",'callback_data'=>'ping'],['text'=>"پینگ 〽️ :",'callback_data'=>'ping']],
[['text'=>"$ver",'callback_data'=>'ping'],['text'=>"ورژن پی اچ پی♻️ :",'callback_data'=>'ping']],
[['text'=>"$memm KB",'callback_data'=>'ping'],['text'=>"میزان مصرف حافظه💻 :",'callback_data'=>'ping']],
[['text'=>"$a",'callback_data'=>'ping'],['text'=>"✅ تعداد کاربران ربات :",'callback_data'=>'ping']],
[['text'=>"$ben",'callback_data'=>'ping'],['text'=>"❌ تعداد کاربران بن شده :",'callback_data'=>'ping']],
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'ping']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'time']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'time']],
[['text'=>"┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈┅┈",'callback_data'=>'ping']],
]]));exit();
}
if($text =="$dok19" and $type == "private"){
mkdir("data");
mkdir("data/$from_id");
$dbcke = file_get_contents("data/$from_id/dbcke.txt");
if($dbcke != true){
$sbbckwcj = rand($bonos1,$bonos2);
$dnckerdkc = $datas['money']+$sbbckwcj;
$datas["money"] = "$dnckerdkc";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("data/$from_id/dbcke.txt","1000");
SendMessage($from_id,"
🎁 هدیه شما $sbbckwcj

$date - $time
");
}else{
SendMessage($from_id,"
🎁 شما قبلا هدیه خود را دریافت کرده اید !

$date - $time
");
}}

if($text =="$dok17" and $type == "private"){
SendMessage($from_id,"🌟 به قسمت تراکنش ها خوش آمدید یکی از گزینه های زیر را انتخاب کنید 👇
",$button_tar);
}
if($text =="🎈 لیست آخرین برداشتی ها" and $type == "private"){
SendMessage($from_id,"💵 لیست آخرین تراکنش های برداشت از ربات ما به شرح زیر می‌باشند 👇

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_tar);
}
if($text =="💸 لیست آخرین شارژ حساب" and $type == "private"){
SendMessage($from_id,"💵 لیست آخرین تراکنش های شارژ حساب در ربات ما به شرح زیر است 👇

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_tar);
}

if($text =="$dok16" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🎈 در این بخش میتوانید ضریب های بازی انفجار و عدد های پشبینی شده بازی مونتی رورو ببینید و در بازی های مونتی و انفجار شانس بیشتری داشته باشید و میلیونی نیترو برنده بشوید 🎉
",@$button_date);
}

if($text =="💣 تاریخچه انفجار" and $type == "private"){
SendMessage($from_id,"💣 لیست آخرین بازیکنان انفجار به شرح زیر می‌باشد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_date);
}
if($text =="🔢 تاریخچه مونتی" and $type == "private"){
SendMessage($from_id,"🔢 لیست آخرین بازیکنان مونتی به شرح زیر می‌باشد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_date);
}
if($text =="♻️ نمایندگی ♻️" and $type == "private"){
SendMessage($from_id,"🎈 هر گونه تبر خوردن از ربات های نمایندگی به سازنده ربات مربوط نمیباشد فقط و فقط به ادمین ربات یعنی @$poshtiban مربوط می‌باشد ❗️

♻️ در بازی ها هیچ گونه دستی دخالت ندارد و تمام بازی ها شانسی هستند ❕

🍁 جهت خرید ربات نمایندگی مانند همین ربات روی دکمه شیشه ای زیر کلیک کنید 👇",json_encode(['inline_keyboard' => [[['text' => "خرید ربات نمایندگی 🛍️",'url' => "T.me/tak_php"]],]]));exit();
}
if($text =="بازه ضریب ها 💣" and $type == "private"){
$T = rand(99,200)/100;
$A = rand(99,200)/100;
$B = rand(99,200)/100;
$C = rand(99,200)/100;
$D = rand(99,200)/100;
$E = rand(99,200)/100;
$F = rand(99,200)/100;
$G = rand(99,200)/100;
$H = rand(99,200)/100;
SendMessage($from_id,"
💣 بازه ی ضریب های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊

┏━━━━━━━━━━━━┓
<code>𝟏 - $A</code>
┄┅┈┉┅┉┈┅┄
<code>𝟐 - $B</code>
┄┅┈┉┅┉┈┅┄
<code>𝟑 - $C</code>
┄┅┈┉┅┉┈┅┄
<code>𝟒 - $D</code>
┄┅┈┉┅┉┈┅┄
<code>𝟓 - $E</code>
┄┅┈┉┅┉┈┅┄
<code>𝟔 - $F</code>
┄┅┈┉┅┉┈┅┄
<code>𝟕 - $G</code>
┄┅┈┉┅┉┈┅┄
<code>𝟖 - $H</code>
┄┅┈┉┅┉┈┅┄
<code>𝟗 - $T</code>
┗━━━━━━━━━━━━┛
",$button_TAK);
}
if($text =="تشخیص ضریب💣" and $type == "private"){
$S = rand(99,200)/100;
SendMessage($from_id,"
💣 Forecast panel : 

🚀 ضریب این دست در ⇲ <code>$S</code> ⇱ پیشبینی شده است ᱹᱹᱹ
این ضریب مخصوص ربات ما می‌باشد ❗️

 🥀 : $botid
",$button_TAK);
}

if($text =="$dok18" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dnckdkck=file_get_contents("users.txt");
$exp=explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
$myfile2 = fopen("users.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 کاربر $first_name به گروه چت پیوست ";
sendMessage($user,$pv);
}
}
SendMessage($from_id,"
✅ با موفقیت وارد گروه چت ربات شرط بندی شدید 


جهت ارسال پیام به اعضای گروه از دکمه 📨 ارسال پیام استفاده کنید . . . 
",$button_chat);
}
if($text =="🌺 بستن آخرین چت" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pmclose = "✅ ادمین ( <code>$first_name</code> ) چت فعلی را بست ❌
 جهت چت مجدد روی <code>☁️ چت با اعضا ربات</code> بزنید . . . ";
sendMessage($user,$pmclose);
}
$close = "";
file_put_contents("users.txt",$close);
SendMessage($from_id,"✅ ادمین گرامی :) با موفقیت چت بسته شد . . . ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}

if($text =="بستن چت ❌" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dcoeockencn=file_get_contents("users.txt");
$fncndkckek = str_replace($from_id, "", $dcoeockencn);
file_put_contents("users.txt", $fncndkckek);
SendMessage($from_id,"✅ با موفقیت از چت خارج شدید . . . 


جهت پیوستن مجدد به چت روی دکمه <code>$dok18</code> بزنید . . . 

",json_encode(['keyboard'=>[[['text'=>"$dok18"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$ps = "💣 کاربر $first_name از گروه لفت داد ";
sendMessage($user,$ps);
}
}
if($text =="📊 آمار گروه چت" and $type == "private"){
	SendMessage($from_id,"
📊 لیست آمار کسانی که تا ساعت <code>$time</code> و تا تاریخ <code>$date</code> در گروه چت وجود دارند 👇
");
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$dnckdkck=file_get_contents("users.txt");
SendMessage($from_id,"
<code>$user</code>
",$button_chat);
}}
if($text =="📨 ارسال پیام" and $type == "private"){
$datas["step"] = "caht2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♻️ پیام خود را ارسال کنید تا برای همه اعضای این چت ارسال کنم . . .
",$button_chat);
}
if($step == "caht2" and $text !="🔙 بازگشت" and $text !="📨 ارسال پیام" and $text !="📊 آمار گروه چت" and $text !="بستن چت ❌" and $text !="$dok18" and $type == "private"){
 if(preg_match('/[a-z]/i',$text)){
SendMessage($from_id,"امکان ارسال لینک وجود ندارد");
exit();
  }
  if(preg_match('~bot|t(elegram)?\.me|@|\.ir|\.com~i',$text)){
SendMessage($from_id,"امکان ارسال لینک وجود ندارد");
exit();
  }
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pm = "$first_name : $text";
sendMessage($user,$pm);
}}
if($text =="🎲 میز شماره یک" or $text =="/miz1" and $type == "private"){
$mizer3 = file_get_contents("tas/miz3/users.txt");
$exp3 = explode("\n",$mizer3);
if(in_array($from_id,$exp3)){
exit();
}
$mizer2 = file_get_contents("tas/miz2/users.txt");
$exp2 = explode("\n",$mizer2);
if(in_array($from_id,$exp2)){
exit();
}
$now = file_get_contents("tas/miz1/users.txt");
$lolnow = explode("\n",$now);
$nstsl = count($lolnow);
$nowsfor = $nstsl - 1 ;
if($nowsfor == "6"){
SendMessage($from_id,"🔆 میز شماره یک پر است",$button);
exit();
}
if(1000 > $datas['money']){
SendMessage($from_id,"❗️جهت ورود به این میز نیاز به 1,000 سکه دارید",$button);
exit();
}
$player1 = file_get_contents("tas/miz1/player1.txt");
$player2 = file_get_contents("tas/miz1/player2.txt");
$player3 = file_get_contents("tas/miz1/player3.txt");
$player4 = file_get_contents("tas/miz1/player4.txt");
$player5 = file_get_contents("tas/miz1/player5.txt");
$player6 = file_get_contents("tas/miz1/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
if(!file_exists('tas')){
mkdir("tas");
}
if(!file_exists('tas/miz1')){
mkdir("tas/miz1");
}
$datas["step"] = "ingamerts1";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dnckdkck=file_get_contents("tas/miz1/users.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
$myfile2 = fopen("tas/miz1/users.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
if(!file_exists("tas/miz1/player1.txt")){
file_put_contents("tas/miz1/player1.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player1");
$nex = "۱";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$first_name
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz1/player2.txt")){
file_put_contents("tas/miz1/player2.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player2");
$nex = "۲";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$first_name
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz1/player3.txt")){
file_put_contents("tas/miz1/player3.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player3");
$nex = "۳";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$first_name
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz1/player4.txt")){
file_put_contents("tas/miz1/player4.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player4");
$nex = "۴";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$first_name
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz1/player5.txt")){
file_put_contents("tas/miz1/player5.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player5");
$nex = "۵";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$first_name
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz1/player6.txt")){
file_put_contents("tas/miz1/player6.txt",$from_id);
file_put_contents("tas/miz1/$from_id.txt","player6");
$nex = "۶";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$first_name

",$button_miz_tas);
}
}
}
}
}
}
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 ( $nex ) کاربر $first_name به بازی تاس 6 نفره پیوست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در این میز هستید",$button_miz_tas);
exit();
}
}
if($text =="🎲 میز شماره دو" or $text =="/miz2" and $type == "private"){
$mizer3 = file_get_contents("tas/miz3/users.txt");
$exp3 = explode("\n",$mizer3);
if(in_array($from_id,$exp3)){
exit();
}
$mizer1 = file_get_contents("tas/miz1/users.txt");
$exp1 = explode("\n",$mizer1);
if(in_array($from_id,$exp1)){
exit();
}
$now = file_get_contents("tas/miz2/users.txt");
$lolnow = explode("\n",$now);
$nstsl = count($lolnow);
$nowsfor = $nstsl - 1 ;
if($nowsfor == "6"){
SendMessage($from_id,"🔆 میز شماره دو پر است",$button);
exit();
}
if(5000 > $datas['money']){
SendMessage($from_id,"❗️جهت ورود به این میز نیاز به 5,000 سکه دارید",$button);
exit();
}
$player1 = file_get_contents("tas/miz2/player1.txt");
$player2 = file_get_contents("tas/miz2/player2.txt");
$player3 = file_get_contents("tas/miz2/player3.txt");
$player4 = file_get_contents("tas/miz2/player4.txt");
$player5 = file_get_contents("tas/miz2/player5.txt");
$player6 = file_get_contents("tas/miz2/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
if(!file_exists('tas')){
mkdir("tas");
}
if(!file_exists('tas/miz2')){
mkdir("tas/miz2");
}
$datas["step"] = "ingamerts2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dnckdkck=file_get_contents("tas/miz2/users.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
$myfile2 = fopen("tas/miz2/users.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
if(!file_exists("tas/miz2/player1.txt")){
file_put_contents("tas/miz2/player1.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player1");
$nex = "۱";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$first_name
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz2/player2.txt")){
file_put_contents("tas/miz2/player2.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player2");
$nex = "۲";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$first_name
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz2/player3.txt")){
file_put_contents("tas/miz2/player3.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player3");
$nex = "۳";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$first_name
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz2/player4.txt")){
file_put_contents("tas/miz2/player4.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player4");
$nex = "۴";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$first_name
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz2/player5.txt")){
file_put_contents("tas/miz2/player5.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player5");
$nex = "۵";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$first_name
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz2/player6.txt")){
file_put_contents("tas/miz2/player6.txt",$from_id);
file_put_contents("tas/miz2/$from_id.txt","player6");
$nex = "۶";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$first_name

",$button_miz_tas);
}
}
}
}
}
}
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 ( $nex ) کاربر $first_name به بازی تاس 6 نفره پیوست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در این میز هستید",$button_miz_tas);
exit();
}
}
if($text =="🎲 میز شماره سه" or $text =="/miz3" and $type == "private"){
$mizer2 = file_get_contents("tas/miz2/users.txt");
$exp2 = explode("\n",$mizer2);
if(in_array($from_id,$exp2)){
exit();
}
$mizer1 = file_get_contents("tas/miz1/users.txt");
$exp1 = explode("\n",$mizer1);
if(in_array($from_id,$exp1)){
exit();
}
$now = file_get_contents("tas/miz3/users.txt");
$lolnow = explode("\n",$now);
$nstsl = count($lolnow);
$nowsfor = $nstsl - 1 ;
if($nowsfor == "6"){
SendMessage($from_id,"🔆 میز شماره سه پر است",$button);
exit();
}
if(10000 > $datas['money']){
SendMessage($from_id,"❗️جهت ورود به این میز نیاز به 10,000 سکه دارید",$button);
exit();
}
$player1 = file_get_contents("tas/miz3/player1.txt");
$player2 = file_get_contents("tas/miz3/player2.txt");
$player3 = file_get_contents("tas/miz3/player3.txt");
$player4 = file_get_contents("tas/miz3/player4.txt");
$player5 = file_get_contents("tas/miz3/player5.txt");
$player6 = file_get_contents("tas/miz3/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
if(!file_exists('tas')){
mkdir("tas");
}
if(!file_exists('tas/miz3')){
mkdir("tas/miz3");
}
$datas["step"] = "ingamerts3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dnckdkck=file_get_contents("tas/miz3/users.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
$myfile2 = fopen("tas/miz3/users.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
if(!file_exists("tas/miz3/player1.txt")){
file_put_contents("tas/miz3/player1.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player1");
$nex = "۱";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$first_name
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz3/player2.txt")){
file_put_contents("tas/miz3/player2.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player2");
$nex = "۲";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$first_name
$yournamer3
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz3/player3.txt")){
file_put_contents("tas/miz3/player3.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player3");
$nex = "۳";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$first_name
$yournamer4
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz3/player4.txt")){
file_put_contents("tas/miz3/player4.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player4");
$nex = "۴";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$first_name
$yournamer5
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz3/player5.txt")){
file_put_contents("tas/miz3/player5.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player5");
$nex = "۵";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$first_name
$yournamer6

",$button_miz_tas);
}else{
if(!file_exists("tas/miz3/player6.txt")){
file_put_contents("tas/miz3/player6.txt",$from_id);
file_put_contents("tas/miz3/$from_id.txt","player6");
$nex = "۶";
SendMessage($from_id,"
🎲 درحال جستجوی بازیکنان آنلاین . . .

--------------------------------

👥 لیست افراد در صف انتظار


$yournamer1
$yournamer2
$yournamer3
$yournamer4
$yournamer5
$first_name

",$button_miz_tas);
}
}
}
}
}
}
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 ( $nex ) کاربر $first_name به بازی تاس 6 نفره پیوست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در این میز هستید",$button_miz_tas);
exit();
}
}
if($text =="❌ قطع اتصال" and $step == "ingamerts1" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$delid = file_get_contents("tas/miz1/$from_id.txt");
unlink("tas/miz1/$delid.txt");
unlink("tas/miz1/$from_id.txt");
$taser1 = file_get_contents("tas/miz1/users.txt");
$again1 = str_replace("$from_id\n", "", $taser1);
file_put_contents("tas/miz1/users.txt", $again1);
$mizband = file_get_contents("tas/miz1/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$taser1 = file_get_contents("tas/miz1/ready.txt");
$again1 = str_replace("$from_id\n", "", $taser1);
file_put_contents("tas/miz1/ready.txt", $again1);
}
SendMessage($from_id,"✅ با موفقیت از میز شماره 1 خارج شدید",$button);
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "🚫 کاربر ( $first_name ) از میز بازی خارج شد";
sendMessage($user,$pv);
}
}
if($text =="❌ قطع اتصال" and $step == "ingamerts2" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$delid = file_get_contents("tas/miz2/$from_id.txt");
unlink("tas/miz2/$delid.txt");
unlink("tas/miz2/$from_id.txt");
$taser2 = file_get_contents("tas/miz2/users.txt");
$again2 = str_replace("$from_id\n", "", $taser2);
file_put_contents("tas/miz2/users.txt", $again2);
$mizband = file_get_contents("tas/miz2/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$taser2 = file_get_contents("tas/miz2/ready.txt");
$again2 = str_replace("$from_id\n", "", $taser2);
file_put_contents("tas/miz2/ready.txt", $again2);
}
SendMessage($from_id,"✅ با موفقیت از میز شماره 2 خارج شدید",$button);
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "🚫 کاربر ( $first_name ) از میز بازی خارج شد";
sendMessage($user,$pv);
}
}
if($text =="❌ قطع اتصال" and $step == "ingamerts3" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$delid = file_get_contents("tas/miz3/$from_id.txt");
unlink("tas/miz3/$delid.txt");
unlink("tas/miz3/$from_id.txt");
$taser3 = file_get_contents("tas/miz3/users.txt");
$again3 = str_replace("$from_id\n", "", $taser3);
file_put_contents("tas/miz3/users.txt", $again3);
$mizband = file_get_contents("tas/miz3/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$taser3 = file_get_contents("tas/miz3/ready.txt");
$again3 = str_replace("$from_id\n", "", $taser3);
file_put_contents("tas/miz3/ready.txt", $again3);
}
SendMessage($from_id,"✅ با موفقیت از میز شماره 3 خارج شدید",$button);
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "🚫 کاربر ( $first_name ) از میز بازی خارج شد";
sendMessage($user,$pv);
}
}
if($text =="✅ شروع بازی" and $step == "ingamerts1" and $type == "private"){
$dnckdkck = file_get_contents("tas/miz1/ready.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
if(1000 > $datas['money']){
SendMessage($from_id,"❗️جهت شروع این بازی نیاز به 1,000 سکه دارید",$button_miz_tas);
exit();
}
$moj = $datas["money"] - 1000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$myfile2 = fopen("tas/miz1/ready.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
SendMessage($from_id,"✅ شما به لیست کاربران حاضر اضافه شدید",$button_miz_tas);
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "✅ $first_name آماده بازی است";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در لیست کاربران حاضر هستید",$button_miz_tas);
}
}
if($text =="✅ شروع بازی" and $step == "ingamerts2" and $type == "private"){
$dnckdkck = file_get_contents("tas/miz2/ready.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
if(5000 > $datas['money']){
SendMessage($from_id,"❗️جهت شروع این بازی نیاز به 5,000 سکه دارید",$button_miz_tas);
exit();
}
$moj = $datas["money"] - 5000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$myfile2 = fopen("tas/miz2/ready.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
SendMessage($from_id,"✅ شما به لیست کاربران حاضر اضافه شدید",$button_miz_tas);
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "✅ $first_name آماده بازی است";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در لیست کاربران حاضر هستید",$button_miz_tas);
}
}
if($text =="✅ شروع بازی" and $step == "ingamerts3" and $type == "private"){
$dnckdkck = file_get_contents("tas/miz3/ready.txt");
$exp = explode("\n",$dnckdkck);
if(!in_array($from_id,$exp)){
if(10000 > $datas['money']){
SendMessage($from_id,"❗️جهت شروع این بازی نیاز به 10,000 سکه دارید",$button_miz_tas);
exit();
}
$moj = $datas["money"] - 10000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$myfile2 = fopen("tas/miz3/ready.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
SendMessage($from_id,"✅ شما به لیست کاربران حاضر اضافه شدید",$button_miz_tas);
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "✅ $first_name آماده بازی است";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❌ شما از قبل در لیست کاربران حاضر هستید",$button_miz_tas);
}
}
if($text =="🔰 لغو شروع" and $step == "ingamerts1" and $type == "private"){
$mizband = file_get_contents("tas/miz1/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$moj = $datas["money"] + 1000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$taser1 = file_get_contents("tas/miz1/ready.txt");
$again1 = str_replace("$from_id\n", "", $taser1);
file_put_contents("tas/miz1/ready.txt", $again1);
SendMessage($from_id,"♻️ شروع بازی لغو شد از سمت شما",$button_miz_tas);
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "❗️کاربر $first_name آماده شروع بازی نیست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❓شما اصلا روی شروع بازی نزدید ",$button_miz_tas);
}
}
if($text =="🔰 لغو شروع" and $step == "ingamerts2" and $type == "private"){
$mizband = file_get_contents("tas/miz2/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$moj = $datas["money"] + 5000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$taser2 = file_get_contents("tas/miz2/ready.txt");
$again2 = str_replace("$from_id\n", "", $taser2);
file_put_contents("tas/miz2/ready.txt", $again2);
SendMessage($from_id,"♻️ شروع بازی لغو شد از سمت شما",$button_miz_tas);
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "❗️کاربر $first_name آماده شروع بازی نیست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❓شما اصلا روی شروع بازی نزدید ",$button_miz_tas);
}
}
if($text =="🔰 لغو شروع" and $step == "ingamerts3" and $type == "private"){
$mizband = file_get_contents("tas/miz3/ready.txt");
$exp = explode("\n",$mizband);
if(in_array($from_id,$exp)){
$moj = $datas["money"] + 10000;
$datas["money"] = $moj;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$taser3 = file_get_contents("tas/miz3/ready.txt");
$again3 = str_replace("$from_id\n", "", $taser3);
file_put_contents("tas/miz3/ready.txt", $again3);
SendMessage($from_id,"♻️ شروع بازی لغو شد از سمت شما",$button_miz_tas);
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "❗️کاربر $first_name آماده شروع بازی نیست";
sendMessage($user,$pv);
}
}else{
SendMessage($from_id,"❓شما اصلا روی شروع بازی نزدید ",$button_miz_tas);
}
}
if($text =="🎈 بازیکنان حاضر" and $step == "ingamerts1" and $type == "private"){
$count = file_get_contents("tas/miz1/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
SendMessage($from_id,"💤 تعداد کاربران حاضر برای شروع بازی : $countsfor نفر",$button_miz_tas);
}
if($text =="🎈 بازیکنان حاضر" and $step == "ingamerts2" and $type == "private"){
$count = file_get_contents("tas/miz2/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
SendMessage($from_id,"💤 تعداد کاربران حاضر برای شروع بازی : $countsfor نفر",$button_miz_tas);
}
if($text =="🎈 بازیکنان حاضر" and $step == "ingamerts3" and $type == "private"){
$count = file_get_contents("tas/miz2/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
SendMessage($from_id,"💤 تعداد کاربران حاضر برای شروع بازی : $countsfor نفر",$button_miz_tas);
}
if(file_exists("tas/miz1/ready.txt")){
$count = file_get_contents("tas/miz1/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
if($countsfor == "6"){
SendMessage($shart,"♠️ شرط بندی میز شماره ( 1 ) بازی تاس گروهی ");
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
$msg = $dice->result->message_id;
$playerwiner = file_get_contents("tas/miz1/player$value.txt");
$infowiner = bot('getChatMember',['chat_id'=>"$playerwiner",'user_id'=>"$playerwiner"]);
$namewiner = $infowiner->result->user->first_name;
$winert = json_decode(file_get_contents("data/$playerwiner/$playerwiner.json"),true);
$mowin = $winert["money"] + 5000;
$winert["money"] = $mowin;
$outjson = json_encode($winert,true);
file_put_contents("data/$playerwiner/$playerwiner.json",$outjson);
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('forwardMessage',['chat_id'=>"$user",'from_chat_id'=>"$shart",'message_id'=>"$msg"]);
sendMessage($user,"🎉 کاربر $namewiner برنده این بازی شد . . .");
}
unlink("tas/miz1/ready.txt");
}
}
if(file_exists("tas/miz2/ready.txt")){
$count = file_get_contents("tas/miz2/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
if($countsfor == "6"){
SendMessage($shart,"♠️ شرط بندی میز شماره ( 2 ) بازی تاس گروهی ");
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
$msg = $dice->result->message_id;
$playerwiner = file_get_contents("tas/miz2/player$value.txt");
$infowiner = bot('getChatMember',['chat_id'=>"$playerwiner",'user_id'=>"$playerwiner"]);
$namewiner = $infowiner->result->user->first_name;
$winert = json_decode(file_get_contents("data/$playerwiner/$playerwiner.json"),true);
$mowin = $winert["money"] + 29000;
$winert["money"] = $mowin;
$outjson = json_encode($winert,true);
file_put_contents("data/$playerwiner/$playerwiner.json",$outjson);
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('forwardMessage',['chat_id'=>"$user",'from_chat_id'=>"$shart",'message_id'=>"$msg"]);
sendMessage($user,"🎉 کاربر $namewiner برنده این بازی شد . . .");
}
unlink("tas/miz2/ready.txt");
}
}
if(file_exists("tas/miz3/ready.txt")){
$count = file_get_contents("tas/miz3/ready.txt");
$tokhob = explode("\n",$count);
$consky = count($tokhob);
$countsfor = $consky - 1 ;
if($countsfor == "6"){
SendMessage($shart,"♠️ شرط بندی میز شماره ( 3 ) بازی تاس گروهی ");
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
$msg = $dice->result->message_id;
$playerwiner = file_get_contents("tas/miz3/player$value.txt");
$infowiner = bot('getChatMember',['chat_id'=>"$playerwiner",'user_id'=>"$playerwiner"]);
$namewiner = $infowiner->result->user->first_name;
$winert = json_decode(file_get_contents("data/$playerwiner/$playerwiner.json"),true);
$mowin = $winert["money"] + 58000;
$winert["money"] = $mowin;
$outjson = json_encode($winert,true);
file_put_contents("data/$playerwiner/$playerwiner.json",$outjson);
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('forwardMessage',['chat_id'=>"$user",'from_chat_id'=>"$shart",'message_id'=>"$msg"]);
sendMessage($user,"🎉 کاربر $namewiner برنده این بازی شد . . .");
}
unlink("tas/miz3/ready.txt");
}
}
if($text =="⚖ قوانین بازی"){
if($step == "ingamerts1" or $step == "ingamerts2" or $step == "ingamerts3"){
SendMessage($from_id,"
⚖ قوانین بازی :


1 - هر گونه تقلب ممنوع 

2 - به هم زدن نظم بازی ممنوع 

3 - بی احترامی و فحاشی ممنوع 

4 - تبلیغات ممنوع 

5 - در صورت گزارش شدن شما توسط بازیکنان دیگر شما مسدود میشوید 
",$button_miz_tas);
}
}
if($text =="📚 راهنمای بازی"){
if($step == "ingamerts1" or $step == "ingamerts2" or $step == "ingamerts3"){
SendMessage($from_id,"
🎲 بازی تاس گروهی : 


1 - نحوه انجام این بازی تعدادی از کاربران وارد یک میز از بازی میشوند و باید روی شروع بازی بزنند و اگر 6 نفر از بازیکنان روی شروع بازی بزنند بازی شروع میشوند و تاس انداخته میشوند و یک کاربر برنده میشود 


2 - از همه بازیکنان سکه کم میشود و به بازیکن برنده میرسد تمام سکه ها 

3 - مقدار از سکه بازی به عنوان دست مزد ما کم میشود مثال : هر کاربر سر 1 کا سکه شرط بسته اند 

نفر اول به او باید 6 کا سکه برسد ولی 5 کا میرسد 1 کا به دلیل دست مزد ما و هزینه های دیگر ربات 



4 - هر کسی که نظم بازی را به هم ریخت یا بی احترامی م فحاشی کرد می‌توانید گزارش دهید و با او برخورد شد
",$button_miz_tas);
}
}
if($text =="❗️گزارش بازیکنان" and $step == "ingamerts1" and $type == "private"){
$player1 = file_get_contents("tas/miz1/player1.txt");
$player2 = file_get_contents("tas/miz1/player2.txt");
$player3 = file_get_contents("tas/miz1/player3.txt");
$player4 = file_get_contents("tas/miz1/player4.txt");
$player5 = file_get_contents("tas/miz1/player5.txt");
$player6 = file_get_contents("tas/miz1/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
SendMessage($from_id,"
✔️ کدام کاربر را میخواهید گزارش کنید 
",json_encode(['inline_keyboard' => [
[['text'=>"$yournamer1",'callback_data'=>"report$player1"]],
[['text'=>"$yournamer2",'callback_data'=>"report$player2"]],
[['text'=>"$yournamer3",'callback_data'=>"report$player3"]],
[['text'=>"$yournamer4",'callback_data'=>"report$player4"]],
[['text'=>"$yournamer5",'callback_data'=>"report$player5"]],
[['text'=>"$yournamer6",'callback_data'=>"report$player6"]],
]]));exit();
}
if($text =="❗️گزارش بازیکنان" and $step == "ingamerts2" and $type == "private"){
$player1 = file_get_contents("tas/miz2/player1.txt");
$player2 = file_get_contents("tas/miz2/player2.txt");
$player3 = file_get_contents("tas/miz2/player3.txt");
$player4 = file_get_contents("tas/miz2/player4.txt");
$player5 = file_get_contents("tas/miz2/player5.txt");
$player6 = file_get_contents("tas/miz2/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
SendMessage($from_id,"
✔️ کدام کاربر را میخواهید گزارش کنید 
",json_encode(['inline_keyboard' => [
[['text'=>"$yournamer1",'callback_data'=>"report$player1"]],
[['text'=>"$yournamer2",'callback_data'=>"report$player2"]],
[['text'=>"$yournamer3",'callback_data'=>"report$player3"]],
[['text'=>"$yournamer4",'callback_data'=>"report$player4"]],
[['text'=>"$yournamer5",'callback_data'=>"report$player5"]],
[['text'=>"$yournamer6",'callback_data'=>"report$player6"]],
]]));exit();
}
if($text =="❗️گزارش بازیکنان" and $step == "ingamerts3" and $type == "private"){
$player1 = file_get_contents("tas/miz3/player1.txt");
$player2 = file_get_contents("tas/miz3/player2.txt");
$player3 = file_get_contents("tas/miz3/player3.txt");
$player4 = file_get_contents("tas/miz3/player4.txt");
$player5 = file_get_contents("tas/miz3/player5.txt");
$player6 = file_get_contents("tas/miz3/player6.txt");
$infouser1 = bot('getChatMember',['chat_id'=>"$player1",'user_id'=>"$player1"]);
$yournamer1 = $infouser1->result->user->first_name;
$infouser2 = bot('getChatMember',['chat_id'=>"$player2",'user_id'=>"$player2"]);
$yournamer2 = $infouser2->result->user->first_name;
$infouser3 = bot('getChatMember',['chat_id'=>"$player3",'user_id'=>"$player3"]);
$yournamer3 = $infouser3->result->user->first_name;
$infouser4 = bot('getChatMember',['chat_id'=>"$player4",'user_id'=>"$player4"]);
$yournamer4 = $infouser4->result->user->first_name;
$infouser5 = bot('getChatMember',['chat_id'=>"$player5",'user_id'=>"$player5"]);
$yournamer5 = $infouser5->result->user->first_name;
$infouser6 = bot('getChatMember',['chat_id'=>"$player6",'user_id'=>"$player6"]);
$yournamer6 = $infouser6->result->user->first_name;
SendMessage($from_id,"
✔️ کدام کاربر را میخواهید گزارش کنید 
",json_encode(['inline_keyboard' => [
[['text'=>"$yournamer1",'callback_data'=>"report$player1"]],
[['text'=>"$yournamer2",'callback_data'=>"report$player2"]],
[['text'=>"$yournamer3",'callback_data'=>"report$player3"]],
[['text'=>"$yournamer4",'callback_data'=>"report$player4"]],
[['text'=>"$yournamer5",'callback_data'=>"report$player5"]],
[['text'=>"$yournamer6",'callback_data'=>"report$player6"]],
]]));exit();
}
if($step == "ingamerts1" and $text != "🎈 بازیکنان حاضر" and $text != "✅ شروع بازی" and $text != "🔰 لغو شروع" and $text != "📚 راهنمای بازی" and $text != "❗️گزارش بازیکنان" and $text != "⚖ قوانین بازی" and $text != "❌ قطع اتصال"){
if($text == true){
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
sendMessage($from_id,"⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",$button_miz_tas);
exit();
}
if(strlen($text) > 200){
sendMessage($from_id,"⚠️ خطا : پیام شما نباید از 200 حرف بیشتر شود",$button_miz_tas);
exit();
}
$all_member = fopen( "tas/miz1/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 $first_name : $text";
sendMessage($user,$pv,$button_miz_tas);
}
}else{
sendMessage($from_id,"🔰 فقط ارسال متن مجاز است",$button_miz_tas);
exit();
}
}
if($step == "ingamerts2" and $text != "🎈 بازیکنان حاضر" and $text != "✅ شروع بازی" and $text != "🔰 لغو شروع" and $text != "📚 راهنمای بازی" and $text != "❗️گزارش بازیکنان" and $text != "⚖ قوانین بازی" and $text != "❌ قطع اتصال"){
if($text == true){
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
sendMessage($from_id,"⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",$button_miz_tas);
exit();
}
if(strlen($text) > 200){
sendMessage($from_id,"⚠️ خطا : پیام شما نباید از 200 حرف بیشتر شود",$button_miz_tas);
exit();
}
$all_member = fopen( "tas/miz2/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 $first_name : $text";
sendMessage($user,$pv,$button_miz_tas);
}
}else{
sendMessage($from_id,"🔰 فقط ارسال متن مجاز است",$button_miz_tas);
exit();
}
}
if($step == "ingamerts3" and $text != "🎈 بازیکنان حاضر" and $text != "✅ شروع بازی" and $text != "🔰 لغو شروع" and $text != "📚 راهنمای بازی" and $text != "❗️گزارش بازیکنان" and $text != "⚖ قوانین بازی" and $text != "❌ قطع اتصال"){
if($text == true){
if(preg_match('/^(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)|(.*)(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)|(q|w|e|r|t|y|u|i|o|p|a|s|d|f|g|h|j|k|l|z|x|c|v|b|n|m|Q|W|E|R|T|Y|U|I|O|P|A|S|D|F|G|H|J|K|L|Z|X|C|V|B|N|M)(.*)/',$text) ){
sendMessage($from_id,"⚠️ خطا : لطفا فقط با حروف فارسی چت کنید",$button_miz_tas);
exit();
}
if(strlen($text) > 200){
sendMessage($from_id,"⚠️ خطا : پیام شما نباید از 200 حرف بیشتر شود",$button_miz_tas);
exit();
}
$all_member = fopen( "tas/miz3/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 $first_name : $text";
sendMessage($user,$pv,$button_miz_tas);
}
}else{
sendMessage($from_id,"🔰 فقط ارسال متن مجاز است",$button_miz_tas);
exit();
}
}


if($text =="🔎 وضعیت سهام من" and $type == "private"){
SendMessage($from_id,"
🌹مشخصات سهام داری شما
",json_encode(['inline_keyboard' => [
[['text'=>"$from_id",'callback_data'=>'prooo'],['text'=>"🔐 شناسه شما :",'callback_data'=>'prooo']],
[['text'=>"{$datas['saham']}",'callback_data'=>'prooo'],['text'=>"📊 تعداد سهام شما :",'callback_data'=>'prooo']],
[['text'=>"=•=•=•=•=•=•=•=•=•=•=",'callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>"⏳ ساعت :",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>"⏳ تاریخ :",'callback_data'=>'prooo']],
]]));exit();
}
if($text =="💰 فروش سهام" and $type == "private"){
$datas["step"] = "stL";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 تعداد سهام را جهت فروش ارسال کنید ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step == "stL" and $text !="🔙 بازگشت" and is_numeric($text) and $type == "private"){
if($text <= $datas["saham"]){
$datas["step"] = "stL-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$Q = $text * 2000;
SendMessage($from_id,"
🛍 جهت فروش تعداد <code>$text</code> سهام به قیمت <code>$Q</code> تومان ای دی عددی خود را ارسال کنید ❕
",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💢 شما این مقدار سهام ندارید ❗️",$button);
}}
if(strpos($step, "stL-") !== false and $text =="$from_id" and $text !="🔙 بازگشت" and is_numeric($text) and $type == "private"){
$b = explode("-",$step);
$tok = $b[1] * 2000;
$mo = $datas["money"]+$tok;
$datas["money"] = $mo;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🛍 شما تعداد <code>$b[1]</code> به مبلغ <code>$tok</code> نیتروسین فروختید . . .
",$button);
$ckcbbx = "
👤 نام فرد سهام دار : $first_name
📊 مقدار خرید سهام فرد : {$datas['saham']} عدد


┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
"; 
$sahamdar = file_get_contents("saham/sahamdar.txt");
$djcnfkvkrj = str_replace($ckcbbx, "", $sahamdar);
file_put_contents("saham/sahamdar.txt", $djcnfkvkrj);
$newsaham = $datas["saham"] - $b[1];
$datas["saham"] = "$newsaham";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$again = "
👤 نام فرد سهام دار : $first_name
📊 مقدار خرید سهام فرد : {$datas['saham']} عدد


┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
"; 
$myfile2 = fopen("saham/sahamdar.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$again\n");
fclose($myfile2);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$newsam = $mix - $b[1];
file_put_contents("saham/saham.txt", "$newsam");
} 
if($text =="📈 خرید سهام" and $type == "private"){
if($mix <= 99){
$datas["step"] = "stV";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد سهام جهت خرید را وارد کنید بین 0 تا $baqi وارد کنید 🎈",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"سهامی برای خرید وجود ندارد ❌",$button);exit();
}}
if($step == "stV" and $text !="🔙 بازگشت" and is_numeric($text) and $type == "private"){
if($text >= 1 and $text <= 100 and $datas["money"] >= $text * 2000){
$datas["step"] = "stV-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$Q = $text * 2000;
SendMessage($from_id,"
✅ ای دی عددی خود را جهت خرید این تعداد سهام و با این قیمت ارسال کنید . . .

📈 مقدار سهام جهت خرید : <code>$text</code>
📉 قیمت تعداد سهام انتخاب شده : <code>$Q</code>

",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$R = $text * 2000;
SendMessage($from_id,"⭕️ قیمت این تعداد سهام $R نیترو سین می‌باشد موجودی شما کافی نمیباشد ❗️",$button);
}}
if(strpos($step, "stV-") !== false and $text =="$from_id" and $text !="🔙 بازگشت" and is_numeric($text) and $type == "private"){
$b = explode("-",$step);
if($b[1] <= $baqi){
$tok = $b[1] * 2000;
$mo = $datas["money"]-$tok;
$datas["money"] = $mo;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
شما تعداد <code>$b[1]</code> سهام خریدید 💣
",$button);

$newsaham = $datas["saham"] + $b[1];
$datas["saham"] = "$newsaham";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$newsam = $mix + $b[1];
file_put_contents("saham/saham.txt", "$newsam");
SendMessage($addi,"
👤 نام فرد سهام دار : <code>$first_name</code>
✅ ای دی کاربر : <code>@$username2</code>
📊 مقدار خرید سهام فرد : <code>$b[1]</code> عدد
📆 تاریخ خرید سهام : <code>$time</code> | <code>$date</code>
🔢 تعداد سهام های جدید کاربر : <code>$newsaham</code>
");
$fast = "
👤 نام فرد سهام دار : $first_name
📊 مقدار خرید سهام فرد : $b[1] عدد


┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
"; 
$myfile2 = fopen("saham/sahamdar.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$fast\n");
fclose($myfile2);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"این تعداد سهام برای خرید باقی نمانده است ❌ لطفاً دوباره امتحان کنید",$button);
}}

if($text =="📊 سهام داران" and $type == "private"){
SendMessage($from_id,"

📊 اطلاعات سهام و سهام داران:

📈 نام سهام داران فعلی :
              
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$sahamdar

            
=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=
📈 وضعیت فعلی سهام ها : 100 درصد / نا مشخص

📈 قیمت هر یک سهام : 2000 هزار سکه

📈 تعداد سهام های باقی مانده : $baqi

📈 تعداد سهام های فروخته شده : $mix
");
}
if($text =="🎯 دارت" and $type == "private"){
$X1= $dartV+1;
$X2 = $dartGH+1;
$X3 = $dartSS+1;
$X4 = $dartBI+1;
SendMessage($from_id,"
🌹 سلام به بخش بازی دارت خوش آمدید 🎯

🥳 دارت قرمز
🥳 دارت سفید
🥳 دارت وسط
🥳 دارت بیرون

🎯 راهنما بازی دارت 

❤️ دارت قرمز : یعنی دارت دقیقا رو هدف قرمز بخورد •••| ضریب $X2 |•••

🤍 دارت سفید : یعنی دارت دقیقا رو هدف سفید بخورد •••| ضریب $X3 |•••

💛 دارت وسط : یعنی دارت حتما وسط بخورد •••| ضریب $X1 |•••

🖤 دارت بیرون : یعنی دارت خارج بشود و به هیچ جا نخورد •••| ضریب $X4 |•••

",$button_dar);
}
if($text =="🎲 تاس" and $type == "private"){
$Y1 = $tasupd+1;
$Y2 = $tasfard+1;
$Y3 = $tashad+1;
SendMessage($from_id,"
🌹 سلام به بخش بازی تاس خوش آمدید 🎲

🥳 تاس پایین
🥳 تاس بالا
🥳 حدس عدد تاس
🥳 تاس زوج 
🥳 تاس فرد

🎲 راهنما بازی تاس

🎈 تاس بالا : یعنی عدد تاس بالا تر از 3 باشد یعنی 4 و 5 و 6 باشد •••| ضریب $Y1 |•••

🎈 تاس پایین : یعنی عدد تاس پایین تر از 3 باشد یعنی 3 و 2 و 1 باشد •••| ضریب $Y1 |•••

📈 تاس فرد : یعنی عدد تاس 1 و 3 و 5 بیاد •••| ضریب $Y2 |•••

📉 تاس زوج : یعنی عدد تاس 2 و 4 و 6 بیاد •••| ضریب $Y2 |•••

📊 حدس عدد تاس : یعنی پیش‌بینی عدد تاس ! •••| ضریب $Y3 |•••
",$button_tas);
}

if($text =="$dok1" and $type == "private"){
if($meqdar < $datas['money']){
SendMessage($from_id,"🔢 توضیحات بازی مونتی : در این بازی ابتدا شما مبلغ شرط وارد میکنید سپس پیشبینی میکنید عددی که ربات میسازه بیشتر از پنجاه میباشد یا کمتر در صورت درست حدس زدن شما برنده میشوید

🎲 توضیحات بازی تاس : انواع بازی های تاس بصورت خودکار در کانال انجام میشوند / تاس بالا / تاس پایین / تاس زوج / تاس فرد / حدس عدد تاس اگر حدس شما درست باشد شما برنده میشوید

🎯 توضیحات بازی دارت : در این بازی شما ابتدا نوع بازی دارت را انتخاب میکنید 
⟪ دارت قرمز ⟫⟪ دارت سفید ⟫⟪ دارت وسط ⟫⟪ دارت بیرون ⟫ و سپس مبلغ شرط را تنظیم میکنید و بعد  ✅ انجام را میزنید و شرط شما در کانال انجام میشود 

🎰 توضیحات بازی ماشین اسلات : در این بازی شما ابتدا مبلغ شرط را تنظیم میکنید سپس ✅ انجام را بزنید در صورتی که سه گزینه تولید شده در ماشین اسلات مشابه هم باشند شما برنده میشوید 

🏀 بسکتبال: در این بازی شما ابتدا مبلغ شرط را تنظیم میکنید و بعد ربات استیکر تلگرامی را برایتان در کانال ارسال میکند اگر توپ در سبد بیفتد شما برنده میشوید ...

💣 توضیحات بازی انفجار : در این بازی ابتدا شما مقدار سکه و سپس یک ضریب مانند 2.01 و.. وارد میکنید (دقت کنید اعشاری باشه) سپس ربات یک ضریب میده اگر ضریب شما از ضریبی که ربات میگه کمتر باشه هرچقدر مقدار سکه ای ارسال کرده باشید همون ضریبی که زدید برابر میشه مثلا :

✅ عدد و ضریب | 3000 × 1.50 = 1500 سکه سود میکنید 

⚽️ فوتبال: در این بازی توپ فوتبال پرتاب میشود و درصورتی که گل شود شما برنده میشوید
و مدیران یا ربات هیچگونه دسترسی در بازی ها ندارند این بازی های تلگرامی هستند و ربات فقط تشخیص میدهد
🎈 بازی مورد نظر خود را از لیست پایین انتخاب کنید 🎈",$button_b);
}else{
SendMessage($from_id,"
💤 جهت شروع بازی حداقل $meqdar سکه لازم می‌باشد

💰 ابتدا حساب خود را شارژ کنید ! 
",$button);
}}
$m012 = rand(1,12)/1;
if($m012 == $text || $m012 >= $text){
}
$m0120 = rand(13,36)/1;
if($m0120 == $text || $m0120 >= $text){
}
$m1224 = rand(12,24)/1;
if($m1224 == $text || $m1224 >= $text){
}
$m122490 = rand(25,36)/1;
if($m122490 == $text || $m122490 >= $text){
}
$m122490 = rand(1,11)/1;
if($m122490 == $text || $m122490 >= $text){
}
$m0012 = rand(1,18)/1;
if($m0012 == $text || $m0012 >= $text){
}
$m01020 = rand(19,36)/1;
if($m01020 == $text || $m01020 >= $text){
}
$m192240 = rand(18,36)/1;
if($m192240 == $text || $m192240 >= $text){
}
$m122400 = rand(1,17)/1;
if($m122400 == $text || $m122400 >= $text){
}
$m122410 = rand(24,36)/1;
if($m122410 == $text || $m122410 >= $text){
}
$m1224080 = rand(1,23)/1;
if($m1224080 == $text || $m1224080 >= $text){
}
$gpa = rand(1,36)/1;
if($gpa == $text || $gpa >= $text){
}
$fard = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["1","3","5","7","9","11","13","15","17","19","21","23","25","27","29","31","33","35"
];
$fard = $botss[rand(0, count($botss)-1)];if($fard == $text || $fard >= $text){
}
$fard1 = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["2","4","6","8","10","12","14","16","18","20","22","24","26","28","30","32","34","36"
];
$fard1 = $botss[rand(0, count($botss)-1)];if($fard1 == $text || $fard1 >= $text){
}
$md5r = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["02e74f10e0327ad868d138f2b4fdd6f0","c74d97b01eae257e44aa9d5bade97baf","c9f0f895fb98ab9159f51fd0297e236d","cfcd208495d565ef66e7dff9f98764da","1ff1de774005f8da13f42943881c655f","e4da3b7fbbce2345d7772b0674a318d5","8f14e45fceea167a5a36dedd4bea2543","a87ff679a2f3e71d9181a67b7542122c","c20ad4d76fe97759aa27a0c99bff6710","6f4922f45568161a8cdf4ad2299f6d23","d3d9446802a44259755d38e6d163e820"
];
$md5r = $botss[rand(0, count($botss)-1)];if($md5r == $text || $md5r >= $text){
}

$hashr = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["4579673ebb29d37a31e341b787de7bf968c45a53","09ea4c771fd41d6e857f2a6f64488f5e","6aa3b03f4c2f0ad6438d79570bd240b7","3d1bff1ba654ca5fdb6ac1370533d876","c14ab3cc72fe6a5d5d6ed96218ee315f33d3c93cfd0e1b5e5250bc4a697cd47beae805aeaeaeb25c","243b9a9757506e2e54dfbc10fc0e699f6f3d128a","86b89ce063ebc4974db7148db27ca332648d0f18dcc21d28f7234d27123b993e09119b3493b5b0f1","bfb347b6bfbb2a7cb5a7bab4724d5ee63387b7ff8aeeb38cb6abd3246167f40a5016d63743ceb352","58bd2c615ce3fbfa69b1e0e309b610e40cb4c83f","6f8549a9dca8a2dd002dd6918b88f8607fdcb4fcd8520ec38fca36c6203d72b8d2bfede25f732090","db9b3419d156b8aa90938cc3773ff8aa"
];
$hashr = $botss[rand(0, count($botss)-1)];if($hashr == $text || $hashr >= $text){
}
if($text =="🃏 21" and $type == "private"){
if($card21 == on){
$datas["step"] = "TwentyOne";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🃏 Card 21 :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "TwentyOne" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "LF";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
function randing( $LEN ) {
$charch = "2346";
return substr(str_shuffle($charch),0,$LEN);
}
file_put_contents("data/$from_id/amount.txt",$text);
$r =  randing(1);
file_put_contents("data/$from_id/number.txt",$r);
SendMessage($from_id,"
🃏 کارت که برای شما افتاد : $r
",json_encode(['keyboard'=>[
[['text'=>"🃏 دریافت کارت"]],
],'resize_keyboard'=>true]));
}}}
if($text =="🃏 دریافت کارت" and $step =="LF" and $type == "private"){
$datas["step"] = "VQ";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$opes = file_get_contents("data/$from_id/number.txt");
$r =  rand(6,11);
$xyz = $opes+$r;
file_put_contents("data/$from_id/number.txt",$xyz);
SendMessage($from_id,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♥️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}
if($text =="♥️ دریافت کارت جدید" and $step =="VQ" and $type == "private"){
$opes = file_get_contents("data/$from_id/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("data/$from_id/number.txt",$xyz);
if($xyz < 22){
$datas["step"] = "new";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♠️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("data/$from_id/amount.txt");
$coin = $datas['money'] - $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("data/$from_id/number.txt");
}}
if($text =="♠️ دریافت کارت جدید" and $step =="new" and $type == "private"){
$opes = file_get_contents("data/$from_id/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("data/$from_id/number.txt",$xyz);
if($xyz < 22){
$datas["step"] = "neww";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♦️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("data/$from_id/amount.txt");
$coin = $datas['money'] - $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("data/$from_id/number.txt");
}}
if($text =="♦️ دریافت کارت جدید" and $step =="neww" and $type == "private"){
$opes = file_get_contents("data/$from_id/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("data/$from_id/number.txt",$xyz);
if($xyz < 22){
$datas["step"] = "newww";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♣️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("data/$from_id/amount.txt");
$coin = $datas['money'] - $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("data/$from_id/number.txt");
}}
if($text =="♣️ دریافت کارت جدید" and $step =="newww" and $type == "private"){
$opes = file_get_contents("data/$from_id/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("data/$from_id/number.txt",$xyz);
if($xyz < 22){
$datas["step"] = "VQ";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♥️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("data/$from_id/amount.txt");
$coin = $datas['money'] - $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("data/$from_id/number.txt");
}}

if($text =="🏦 بانکدار بازی" and $type == "private"){
$opes = file_get_contents("data/$from_id/number.txt");
if($opes == true){
unlink("data/$from_id/number.txt");
$amount = file_get_contents("data/$from_id/amount.txt");
$r =  rand(12,27);
if($opes > $r){
$coin = $datas['money'] + $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ تبریک , جمع کارت های شما بیشتر از کارت های بانکدار بود 

♻️ مجموع کارت بانکدار : $r

🔰 مجموع کارت های شما : $opes

✅ مقدار برد : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}else{
$coin = $datas['money'] - $amount;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🚫 متاسفانه جمع کارت های شما کمتر از یا برابر با کارت بانکدار بود.

🅱️ مجموع کارت بانکدار : $r

⭕️ مجموع کارت های شما : $opes

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}else{
SendMessage($from_id,"
❌
",$button);
}}

if($text =="🎱 رولت" and $type == "private"){
if($roleton == on){
$datas["step"] = "ro";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎱 Panel Roulette :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "ro" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "ro-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎱 Panel Roulette :

🔢 گزینه مورد نظر خود را پیشبینی کنید:
",json_encode(['keyboard'=>[[['text'=>"🎱 پیشبینی عدد 12 الی 24"],['text'=>"🎱 پیشبینی عدد 0 الی 12"]],[['text'=>"🎱 پیشبینی عدد 24 الی 36"],['text'=>"🎱 پیشبینی عدد 0 الی 18"]],[['text'=>"🎱 پیشبینی عدد 18 الی 36"]],[['text'=>"🎱 پیشبینی عدد زوج"],['text'=>"🎱 پیشبینی عدد فرد"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "ro-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎱 نوع شرط : رولت
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('Sendsticker',['chat_id'=>$shart,'sticker'=>"https://t.me/slokings/7847",]);
if($text == "🎱 پیشبینی عدد 0 الی 12"){
$a = rand(0,36);
if($a < 12){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 12",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 12

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 12",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 12

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد 0 الی 18"){
$a = rand(0,36);
if($a < 18){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 18",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 18

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 18",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 18

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد 12 الی 24"){
$a = rand(0,36);
if($a == 12 or $a == 13 or $a == 14 or $a == 15 or $a == 16 or $a == 17 or $a == 18 or $a == 19 or $a == 20 or $a == 21 or $a == 22 or $a == 23 or $a == 24){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 12 الی 24",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 12 الی 24

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 12 الی 24",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 12 الی 24

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد 24 الی 36"){
$a = rand(0,36);
if($a == 24 or $a == 25 or $a == 26 or $a == 27 or $a == 28 or $a == 29 or $a == 30 or $a == 31 or $a == 32 or $a == 33 or $a == 34 or $a == 35 or $a ==36){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 24 الی 36",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 24 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 24 الی 36",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 24 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد 18 الی 36"){
$a = rand(0,36);
if($a == 18 or $a == 19 or $a == 20 or $a == 21 or $a == 22 or $a == 23 or $a == 24 or $a == 25 or $a == 26 or $a == 27 or $a == 28 or $a == 29 or $a == 30 or $a == 31 or $a == 32 or $a == 33 or $a == 34 or $a == 35 or $a ==36){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 18 الی 36",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 18 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 18 الی 36",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 18 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد زوج"){
$a = rand(0,36);
if($a == 0 or $a == 2 or $a == 4 or $a == 6 or $a == 8 or $a == 10 or $a == 12 or $a == 14 or $a == 16 or $a == 18 or $a == 20 or $a == 22 or $a == 24 or $a == 26 or $a == 28 or $a == 30 or $a == 32 or $a == 34 or $a == 36){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد زوج",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد زوج

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد زوج",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد زوج

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($text == "🎱 پیشبینی عدد فرد"){
$a = rand(0,36);
if($a == 1 or $a == 3 or $a == 5 or $a == 7 or $a == 9 or $a == 11 or $a == 13 or $a == 15 or $a == 17 or $a == 19 or $a == 21 or $a == 23 or $a == 25 or $a == 27 or $a == 29 or $a == 31 or $a == 33 or $a == 35){
$g = $b[1] * $rolet;
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد فرد",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد فرد

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($from_id,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد فرد",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد فرد

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}}
if($text =="⚽️ فوتبال" and $type == "private"){
if($footi == on){
$datas["step"] = "foot";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"⚽️ football Bet :  

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "foot" and is_numeric($text) and $type == "private"){ 
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson); 
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button); 
}else{
$datas["step"] = "foot-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "foot-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
⚽️ نوع شرط : فوتبال
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '⚽️']);
$value = $dice->result->dice->value;
if($value == 3 or $value == 4 or $value == 5 or $value == 6 ){
$g = $b[1] * $football;
SendMessage($from_id,"⚽️ football Bet :

 📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"
✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "⚽ فوتبال",'url' => "https://telegram.me/share/url?url=⚽️%20فوتبال"]],]]));exit();
}else{
SendMessage($from_id,"⚽️ football Bet : 

 📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "⚽ فوتبال",'url' => "https://telegram.me/share/url?url=⚽️%20فوتبال"]],]]));exit();
}}

if($text =="🏀 بسکتبال" and $type == "private"){
if($basket == on){
$datas["step"] = "bas";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🏀 Basketball Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "bas" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "bask-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "bask-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🏀 نوع شرط : بسکتبال
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🏀']);
$value = $dice->result->dice->value;
if($value == 4 or $value == 5 or $value == 6){
$g = $b[1] * $basketball;
SendMessage($from_id,"🏀 Basketball Bet :
   
📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🏀 بسکتبال",'url' => "https://telegram.me/share/url?url=🏀%20بسکتبال"]],]]));exit();
}else{
SendMessage($from_id,"🏀 Basketball Bet :
   
📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🏀 بسکتبال",'url' => "https://telegram.me/share/url?url=🏀%20بسکتبال"]],]]));exit();
}}
if($text =="🎳 بولینگ" and $type == "private"){
if($boli == on){
$datas["step"] = "boling";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎳 Bolling Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "boling" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "boling-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "boling-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎳 نوع شرط : بولینگ
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎳']);
$value = $dice->result->dice->value;
if($value==6){
$g = $b[1] * $boling;
SendMessage($from_id,"🎳 Bolling Bet :
   
📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎳 بولینگ",'url' => "https://telegram.me/share/url?url=🎳%20بولینگ"]],]]));exit();
}else{
SendMessage($from_id,"🎳 Bolling Bet :
   
📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎳 بولینگ",'url' => "https://telegram.me/share/url?url=🎳%20بولینگ"]],]]));exit();
}}
if($text =="🎲 حدس عدد تاس" and $type == "private"){
if($tasi == on){
$datas["step"] = "do";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "do" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "d-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 بنظر شما عدد تاس بر روی چند می ایستد؟",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "d-") !== false and is_numeric($text) and $text != "🔙 بازگشت" and $type == "private"){
if($text <= 6 and $text >= 1){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : حدس عدد تاس
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
⛱ عدد پیش بینی شده : $text
╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($text == $value){
$g = $b[1] * $tashad;
SendMessage($from_id,"🎲 Dice Bet :
   
✅ حدس شما درست بود !

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($from_id,"🎲 Dice Bet :
   
🎲 عدد پیشبینی شده : $text ⟿
🎲 عدد تاس این دست : $value ⟿

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ عدد ارسالی صحیح نیست حداقل عدد 1 و حداکثر 6 میباشد.",$button);exit();
}}
if($text =="🎲 تاس فرد" and $type == "private"){
if($tasi == on){
$datas["step"] = "taF";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "taF" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "tasF-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasF-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس فرد
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 5 or $value == 3 or $value == 1){
$g = $b[1] * $tasfard;
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($text =="🎲 تاس زوج" and $type == "private"){
if($tasi == on){
$datas["step"] = "taZ";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "taZ" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "tasZ-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasZ-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس زوج
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 6 or $value == 4 or $value == 2){
$g = $b[1] * $tasfard;
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($text =="🎲 تاس پایین" and $type == "private"){
if($tasi == on){
$datas["step"] = "taP";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "taP" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "tasP-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasP-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس پایین
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 1 or $value == 2 or $value == 3){
$g = $b[1] * $tasupd;
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($text =="🎲 تاس بالا" and $type == "private"){
if($tasi == on){
$datas["step"] = "taU";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "taU" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "tasU-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasU-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس بالا
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 4 or $value == 5 or $value == 6){
$g = $b[1] * $tasupd;
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($from_id,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($text =="🎯 دارت وسط" and $type == "private"){
if($darti == on){
$datas["step"] = "da";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "da" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "dar-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "dar-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت معمولی ( وسط )
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value==6){
$g = $b[1] * $dartV;
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($text =="🎯 دارت سفید" and $type == "private"){
if($darti == on){
$datas["step"] = "das";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "das" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "darS-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darS-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت سفید
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 5 or $value == 3){
$g = $b[1] * $dartSS;
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($text =="🎯 دارت قرمز" and $type == "private"){
if($darti == on){
$datas["step"] = "daf";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "daf" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "darF-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darF-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت قرمز
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 6 or $value == 4 or $value == 2){
$g = $b[1] * $dartGH;
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($text =="🎯 دارت بیرون" and $type == "private"){
if($darti == on){
$datas["step"] = "daB";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "daB" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "darB-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darB-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت بیرون
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 1){
$g = $b[1] * $dartBI;
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($from_id,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($text =="🎰 ماشین اسلات" and $type == "private"){
if($skati == on){
$datas["step"] = "scat";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎰 Scat Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "scat" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "act-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👇 جهت شروع روی دکمه ✅ انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅ انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "act-") !== false and $text == "✅ انجام" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎰 نوع شرط : اسلات
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$dice = bot('sendDice',['chat_id' => $shart,'emoji'=> '🎰']);
$value = $dice->result->dice->value;
if($value == 64){
$g = $b[1] * $skat;
SendMessage($from_id,"🎰 Scat Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎰 ماشین اسلات",'url' => "https://telegram.me/share/url?url=🎰%20ماشین اسلات"]],]]));exit();
}else{
SendMessage($from_id,"🎰 Scat Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎰 ماشین اسلات",'url' => "https://telegram.me/share/url?url=🎰%20ماشین اسلات"]],]]));exit();
}}

if($text =="💣 انفجار" and $type == "private"){
if($enfejar == on){
$datas["step"] = "enfejar";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 ExplosionPanel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "enfejar" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "ex2-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 ضریب مورد نظر خود را ارسال کنید . . .",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "ex2-") !== false and is_numeric($text) and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
if($text >= 1.01 and $text <= 999.99){
SendMessage($shart,"╔═══════════════════════════════╗
💣 نوع شرط : انفجار
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$R = rand(80,180)/100;
if($R <= "1.01"){
$m = "0.00";
}else{
$m = "$R";
}
SendMessage($shart,"
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $text
");
if($m == $text || $m >= $text){
$abolfazl = $b[1] * $text;
$g = $abolfazl - $b[1];
$tak = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🧬 ضریب درخواستی : $text
💯 ضریب بسته شده : $m
🔰 وضعیت : ✅ برد
"; 
$datas["step"] = "none";
file_put_contents("xbox/crash3.txt",$crash2);
file_put_contents("xbox/crash2.txt",$crash1);
file_put_contents("xbox/crash1.txt",$tak); 
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
SendMessage($from_id,"💣 Explosion Bet :
   
🔥 ضریب این دست در $m بسته شد.

💰 شما مبلغ $abolfazl سکه برنده شدید.

💣 ضریب درخواستی شما : $text
➖➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md</code>

💣Hash : <code>$hash</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $abolfazl coin
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $text

🎉 channel : $shart");exit();
}else{
$tak = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🧬 ضریب درخواستی : $text
💯 ضریب بسته شده : $m
🔰 وضعیت : ❌ باخت 
"; 
$datas["step"] = "none";
file_put_contents("xbox/crash3.txt",$crash2);
file_put_contents("xbox/crash2.txt",$crash1);
file_put_contents("xbox/crash1.txt",$tak); 
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
SendMessage($from_id,"💣 Explosion Bet :
   
💣 ضریب این دست در $m بسته شد.

❌ شما مبلغ $b[1] سکه باختید.

🎲 ضریب درخواستی شما : $text
➖➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md</code>

💣Hash : <code>$hash</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧨 Loss amount : $b[1] coin
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $text

🎉 channel : $shart");exit();
}}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ ضریب ارسال صحیح نیست حداقل ضریب 1.01 و حداکثر 999.99 میباشد.",$button);exit();
}}
if($text =="🔢 مونتی" and $type == "private"){
if($moont == on){
$datas["step"] = "mo";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 CrashPanel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "mo" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "m-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 Monti Panel :

🔢 گزینه مورد نظر خود را پیشبینی کنید:",json_encode(['keyboard'=>[[['text'=>"عدد شانس بیشتر از 50"],['text'=>"عدد شانس کمتر از 50"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "m-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🔢 نوع شرط : مونتی
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
bot('Sendsticker',['chat_id'=>$shart,'sticker'=>"https://t.me/slokings/7846",]);
if($text == "عدد شانس کمتر از 50"){
$a = rand(1,100);
if($a < 50){
$g = $b[1] * $mon;
$tik = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $text
وضعیت بازی : ✅ برد
"; 
$datas["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tik);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 Panel Bet:
   
🔢  این دست در $a بسته شد.

💰 شما سکه $g برنده شدید.

🎲 گزینه درخواستی شما : کمتر از 50",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : عدد شانس کمتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}else{
$tok = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $text
وضعیت بازی : ❌ باخت
"; 
$datas["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tok);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 Panel Monti:
   
🔢 عدد این دست در $a بسته شد.

❌ شما مبلغ $b[1] سکه باختید.

🎲 گزینه درخواستی شما : کمتر از 50",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : عدد شانس کمتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}}
if($text == "عدد شانس بیشتر از 50"){
$a = rand(1,100);
if($a > 50){
$g = $b[1] * $mon;
$tik = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $text
وضعیت بازی : ✅ برد
"; 
$datas["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tik);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 Panel Bet:
   
🔢  این دست در $a بسته شد.

💰 شما مبلغ $g برنده شدید.

🎲 گزینه درخواستی شما : بیشتر از 50",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : عدد شانس بیشتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}else{
$tok = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $text
وضعیت بازی : ❌ باخت
"; 
$datas["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tok);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤖 Panel Monti:
   
🔢 عدد این دست در $a بسته شد.

❌ شما مبلغ $b[1] تومان باختید.

🎲 گزینه درخواستی شما : بیشتر از 50",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : عدد شانس بیشتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}}}
if($text =="💩 پوپ" and $type == "private"){
if($popi == on){
$datas["step"] = "po";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💩 Pop Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "po" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "po-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🤑 Poop Bet 💩:
  
💩 از جعبه های زیر یکی را انتخاب کنید اگر در جعبه مورد نظر شکلک 🤑 قرار داشته باشد مقدار سکه مورد نظر شما 1.25 برابر میشود و اما شکلک 💩 باشد شما میبازید ❗️",json_encode(['keyboard'=>[[['text'=>"جعبه دوم 📦"],['text'=>"جعبه اول 📦"]],[['text'=>"جعبه چهارم 📦"],['text'=>"جعبه سوم 📦"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "po-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
💩 نوع شرط : پوپ
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
if($text == "جعبه اول 📦"){
$a = rand(100,12);
if($a < 50){
$g = $b[1] * $poop;
SendMessage($from_id,"🤑");
SendMessage($from_id,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه اول 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($from_id,"💩");
SendMessage($from_id,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه اول 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($text == "جعبه دوم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1] * $poop;
SendMessage($from_id,"🤑");
SendMessage($from_id,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه دوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($from_id,"💩");
SendMessage($from_id,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه دوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($text == "جعبه سوم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1] * $poop;
SendMessage($from_id,"🤑");
SendMessage($from_id,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه سوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($from_id,"💩");
SendMessage($from_id,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه سوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($text == "جعبه چهارم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1] * $poop;
SendMessage($from_id,"🤑");
SendMessage($from_id,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه چهارم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($from_id,"💩");
SendMessage($from_id,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه چهارم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
}
if($text =="👊🏻 سنگ کاغذ قیچی" and $type == "private"){
if($sangi == on){
$datas["step"] = "sa";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "sa" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "sa-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
  
به پنل سنگ کاغد قیچی خوش امدید .
  
انتخاب خود را بکنید 💎
در صورت امدن یکی از موارد زیر شما برنده میشوید !
👊🏻 سنگ : قیچی
✌🏻 قیچی : کاغذ 
✋🏻 کاغذ : سنگ",json_encode(['keyboard'=>[[['text'=>"✋🏻 کاغذ"],['text'=>"👊🏻 سنگ"]],[['text'=>"✌🏻 قیچی"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "sa-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
👊🏻 نوع شرط : سنگ / کاغذ / قیچی
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($text == "👊🏻 سنگ"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(1,90);
if($a > 60){
$g = $b[1] * $Paper;
SendMessage($from_id,"✌🏻‍");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
✌🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
✌🏻Md5 : <code>$hadi</code>

✌🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✌🏻 قیچی");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 👊🏻 سنگ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($from_id,"✋🏻");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
✋🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
✋🏻Md5 : <code>$hadi</code>

✋🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✋🏻 کاغذ");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 👊🏻 سنگ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
if($text == "✋🏻 کاغذ"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(90,180);
if($a > 150){
$g = $b[1] * $Paper;
SendMessage($from_id,"👊🏻");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
👊🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
👊🏻Md5 : <code>$hadi</code>

👊🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"👊🏻 سنگ");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : ✋🏻 کاغذ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($from_id,"✌🏻‌");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
✌🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
✌🏻Md5 : <code>$hadi</code>

✌🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✌🏻 قیچی");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : ✋🏻 کاغذ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
if($text == "✌🏻 قیچی"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(1,90);
if($a < 30){
$g = $b[1] * $Paper;
SendMessage($from_id,"✋🏻");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
✋🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
✋🏻Md5 : <code>$hadi</code>

✋🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"✋🏻 کاغذ");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : ✌🏻 قیچی

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($from_id,"👊🏻");
SendMessage($from_id,"👊🏻 Scissors / Paper / Rock :
   
👊🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
👊🏻Md5 : <code>$hadi</code>

👊🏻Hash : <code>$hadi2</code>",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"👊🏻 سنگ");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : ✌🏻 قیچی

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
}
if($text =="✊ گل یا پوچ" and $type == "private"){
if($gooli == on){
$datas["step"] = "gol";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌟 Absurd Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ بازی $text خاموش است . . .",$button);exit();
}}
if($step == "gol" and is_numeric($text) and $type == "private"){
if($text < $meqdar){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "gol-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌟 Absurd Panel :

🎈 گزینه مورد نظر خود را انتخاب کنید ( ( گل ✊ یا 👐 پوچ ) )",json_encode(['keyboard'=>[[['text'=>"گل ✊"],['text'=>"پوچ 👐"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "gol-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
👐 نوع شرط : ✊ گل یا پوچ
📄 نام : $first_name
🎈 آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $datas['game']+1;
$datas["game"] = "$newgame";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if($text == "پوچ 👐"){
$a = rand(1,100);
if($a < 50){
$g = $b[1] * $gool;
SendMessage($from_id,"پوچ 👐");
SendMessage($from_id,"🌟 Absurd :
   
این دست 👐 پوچ بود شما مبلغ $g برنده شدید 🥳 ➛║$shart║

🎲 گزینه درخواستی شما : پوچ 👐",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"پوچ 👐");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : پوچ 👐


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}else{
SendMessage($from_id,"گل ✊");
SendMessage($from_id,"🌟 Absurd :
   
این دست ✊ گل بود شما مبلغ $b[1] باختید ➛║$shart║

🎲 گزینه درخواستی شما : پوچ 👐",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"گل ✊");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : پوچ 👐


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}}
if($text == "گل ✊"){
$a = rand(1,100);
if($a > 50){
$g = $b[1] * $gool;
SendMessage($from_id,"گل ✊");
SendMessage($from_id,"🌟 Absurd :
   
این دست ✊ گل بود شما مبلغ $g برنده شدید 🥳 ➛║$shart║

🎲 گزینه درخواستی شما :  گل ✊",$button);
$mo = $datas['money'] + $g ;
$datas["money"] = $mo;
$mojt = $datas['bardasht'] + $g ;
$datas["bardasht"] = $mojt;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"گل ✊");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : گل ✊


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}else{
SendMessage($from_id,"پوچ 👐");
SendMessage($from_id,"🌟 Absurd :
   
این دست 👐 پوچ بود شما مبلغ $b[1] باختید ➛║$shart║

🎲 گزینه درخواستی شما :  گل ✊",$button);
$mo = $datas['money'] - $b[1];
$datas["money"] = $mo;
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"پوچ 👐");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : گل ✊


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}}} 
elseif($text =="$dok11" and $type == "private"){
if($datas['money'] >= $bardasht){
$datas["step"] = "tasfe";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا نوع برداشت را انتخاب نمایید:",$button_ts);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($text =="💰 نیترو سین" and $step =="tasfe" and $type == "private"){
if($datas['bardasht'] >= $bardasht){
$datas["step"] = "toman";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد سکه جهت برداشت را وارد کنید ❗️

💸 حداقل سکه جهت برداشت : $bardasht
💸 حداکثر سکه جهت برداشت : $bardashtX


💵 موجودی حساب شما : {$datas['bardasht']} ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($step =="toman" and is_numeric($text) and $type == "private"){
if($text >= $bardasht and $text <= $bardashtX and $datas['bardasht'] >= $text){
$datas["step"] = "to-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ایدی عددی را بفرستید",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ حداقل سکه مورد نیاز برای برداشت $bardasht سکه و حداکثر سکه جهت برداشت $bardashtX است ",$button);
}}
elseif(strpos($step, "to-") !== false and is_numeric($text) and $type == "private"){
if(strlen($text) == "11" or "7"){
$b = explode("-",$step);
SendMessage($from_id,"✅درخواست تسویه حساب با موفقیت ارسال و پس از بررسی انجام خواهد شد.

💰 مبلغ : <code>$b[1]</code> سکه  
💸 آدرس ایدی عددی : <code>$text</code>",$button);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$mo = $datas['bardasht'] - $b[1];
$tar = "
📊 درخواست تسویه حساب 
➖➖➖➖➖➖➖
✅ نوع درخواست : نیترو سین
📄 نام : $first_name
🥂 آیدی عددی : <code>$from_id</code>
💰 مقدار درخواست : <code>$b[1]</code> سکه
⏳ تاریخ و زمان : $date | $time
📤 وضعیت : انجام شد
"; 
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("tarakonesh/harvest3.txt",$harvest2);
file_put_contents("tarakonesh/harvest2.txt",$harvest1);
file_put_contents("tarakonesh/harvest1.txt",$tar);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه نیترو سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
SendMessage($addi,"~|••• درخواست تسویه حساب ••|~
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه نیترو سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
$datas['bardasht'] = $mo;
$mojd = $datas['money'] - $b[1];
$datas['money'] = $mojd;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا آدرس ایدی عددی 7 تا 11 رقمی خود را درست ارسال کنید",$button);
}}
elseif($text =="💰 اِسلُو سین" and $step =="tasfe" and $type == "private"){
if($datas['bardasht'] >= $bardasht){
$datas["step"] = "Slowto";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد سکه جهت برداشت را وارد کنید ❗️

💸 حداقل سکه جهت برداشت : $bardasht
💸 حداکثر سکه جهت برداشت : $bardashtX


💵 موجودی حساب شما : {$datas['bardasht']} ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($step =="Slowto" and is_numeric($text) and $type == "private"){
if($text >= $bardasht and $text <= $bardashtX and $datas['bardasht'] >= $text){
$datas["step"] = "sno-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ایدی عددی را بفرستید",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ حداقل سکه مورد نیاز برای برداشت $bardasht سکه و حداکثر سکه جهت برداشت $bardashtX است ",$button);
}}
elseif(strpos($step, "sno-") !== false and is_numeric($text) and $type == "private"){
if(strlen($text) == "11" or "7"){
$b = explode("-",$step);
SendMessage($from_id,"✅درخواست تسویه حساب با موفقیت ارسال و پس از بررسی انجام خواهد شد.

💰 مبلغ : <code>$b[1]</code> سکه  
💸 آدرس ایدی عددی : <code>$text</code>",$button);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$mo = $datas['bardasht'] - $b[1];
$tar = "
📊 درخواست تسویه حساب 
➖➖➖➖➖➖➖
✅ نوع درخواست : اِسلُو سین
📄 نام : $first_name
🥂 آیدی عددی : <code>$from_id</code>
💰 مقدار درخواست : <code>$b[1]</code> سکه
⏳ تاریخ و زمان : $date | $time
📤 وضعیت : انجام شد
"; 
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("tarakonesh/harvest3.txt",$harvest2);
file_put_contents("tarakonesh/harvest2.txt",$harvest1);
file_put_contents("tarakonesh/harvest1.txt",$tar);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه اِسلُو سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
SendMessage($addi,"~|••• درخواست تسویه حساب ••|~
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه اِسلُو سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
$datas['bardasht'] = $mo;
$mojd = $datas['money'] - $b[1];
$datas['money'] = $mojd;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا آدرس ایدی عددی 7 تا 11 رقمی خود را درست ارسال کنید",$button);
}}
elseif($text =="💰 رپید سین" and $step =="tasfe" and $type == "private"){
if($datas['bardasht'] >= $bardasht){
$datas["step"] = "bitcoin";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد سکه جهت برداشت را وارد کنید ❗️

💸 حداقل سکه جهت برداشت : $bardasht
💸 حداکثر سکه جهت برداشت : $bardashtX


💵 موجودی حساب شما : {$datas['bardasht']} ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($step =="bitcoin" and is_numeric($text) and $type == "private"){
if($text >= $bardasht and $text <= $bardashtX and $datas['bardasht'] >= $text){
$datas["step"] = "bioin-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"آدرس ایدی عددی را بفرستید",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ حداقل سکه مورد نیاز برای برداشت $bardasht سکه و حداکثر سکه جهت برداشت $bardashtX است ",$button);
}}
elseif(strpos($step, "bioin-") !== false and $text != "🔙 بازگشت" and $type == "private"){
if(strlen($text) == "11" or "7"){
    $b = explode("-",$step);
SendMessage($from_id,"✅درخواست تسویه حساب با موفقیت ارسال و پس از بررسی انجام خواهد شد.

💰 مبلغ : <code>$b[1]</code> سکه  
💸 آدرس ایدی عددی : <code>$text</code>",$button);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$mo = $datas['bardasht'] - $b[1];
$tar = "
📊 درخواست تسویه حساب 
➖➖➖➖➖➖➖
✅ نوع درخواست : ریپید سین
📄 نام : $first_name
🥂 آیدی عددی : <code>$from_id</code>
💣 مقدار درخواست : <code>$b[1]</code> سکه
⏳ تاریخ و زمان : $date | $time
📤 وضعیت : انجام شد
"; 
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("tarakonesh/harvest3.txt",$harvest2);
file_put_contents("tarakonesh/harvest2.txt",$harvest1);
file_put_contents("tarakonesh/harvest1.txt",$tar);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($shart,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه ریپید سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
SendMessage($addi,"~|••• درخواست تسویه حساب ••|~
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه ریپید سین
💎 مقدار درخواست : <code>$b[1]</code>
💡آیدی عددی : <code>$from_id</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$text</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
$datas['bardasht'] = $mo;
$mojd = $datas['money'] - $b[1];
$datas['money'] = $mojd;
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا آدرس ایدی عددی 7 تا 11 رقمی خود را درست ارسال کنید",$button);
}}
if($text =="$dok10" and $type == "private"){
$datas["step"] = "fe";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"

👈🏻 ربات مورد نظر را برای شارژ حساب کاربری خود انتخاب کنید 👇🏻
",$button_f);
}
if($text =="💰 نیترو سین" and $step =="fe" and $type == "private"){
$datas["step"] = "kfe";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 میزان اعتباری که میخواهید حساب خود را شارژ کنید ارسال کنید :

🍻 حداقل $sharj سکه💰",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
elseif($step =="kfe" and is_numeric($text) and $type == "private"){
if($text >= $sharj and $text <= 3000000){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($addi,"✅ یک درخواست شارژ حساب دیگر ساخته شد ❗️
کاربری : $from_id
📄 نام : $first_name
💰 مقدار شارژ : $text
⏳ تاریخ و زمان : $date | $time
");
$sha = "
📄 نام : $first_name 
🍻 کاربری : <code>$from_id</code>
💰 مقدار درخواست شارژ : <code>$text</code>
✅ نوع درخواست شارژ : نیتروسین
⏳ تاریخ و زمان : $date | $time
"; 
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("tarakonesh/hesab3.txt",$hesab2);
file_put_contents("tarakonesh/hesab2.txt",$hesab1);
file_put_contents("tarakonesh/hesab1.txt",$sha);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان <code>$text</code> سکه را به حساب <code>$addi</code> واریز کنید ✅
پس از واریز حساب شما حداکثر طی 10 دقیقه شارژ میشود ❕❗️",$button);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌سکه ارسالی باید بین $sharj تا 3.000.000 سکه باشد.",$button);
}}
if($text =="💰 رپید سین" and $step =="fe" and $type == "private"){
$datas["step"] = "kbit";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 میزان اعتباری که میخواهید حساب خود را شارژ کنید ارسال کنید :

🍻 حداقل $sharj سکه💰",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
elseif($step =="kbit" and is_numeric($text) and $type == "private"){
if($text >= $sharj and $text <= 3000000){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($addi,"✅ یک درخواست شارژ حساب دیگر ساخته شد ❗️
کاربری : $from_id
📄 نام : $first_name
💰 مقدار شارژ : $text
⏳ تاریخ و زمان : $date | $time
");
$sha = "
📄 نام : $first_name 
🍻 کاربری : <code>$from_id</code>
💰 مقدار درخواست شارژ : <code>$text</code>
✅ نوع درخواست شارژ : ریپید سین
⏳ تاریخ و زمان : $date | $time
"; 
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("tarakonesh/hesab3.txt",$hesab2);
file_put_contents("tarakonesh/hesab2.txt",$hesab1);
file_put_contents("tarakonesh/hesab1.txt",$sha);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان <code>$text</code> سکه را به حساب <code>$addi</code> واریز کنید ✅
پس از واریز حساب شما حداکثر طی 10 دقیقه شارژ میشود ❕❗️",$button);
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌سکه ارسالی باید بین $sharj تا 3.000.000 سکه باشد.",$button);
}}
if($text =="$dos" and $type == "private"){
bot('SendPhoto',['chat_id'=>$from_id,'photo'=>"$LINK",
'caption'=>"
💻 پیشرفته ترین ربات شرط بندی در تلگرام ••|  $namebot |••

$mtn

https://t.me/$botid?start=$from_id",]);
bot('sendMessage',['chat_id'=>$from_id,'text'=>"
$mtnzir
",'parse_mode'=>'html','reply_to_message_id'=>$update->message->message_id + 1]);
}
if($text =="$dok3" and $type == "private"){
if (!file_exists("data/$from_id/$from_id.json")) {
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
$datas["money"] = "$bonos3";
$datas["step"] = "free";
$datas["subset"] = "0";
$datas["id"] = "none";
$datas["warn"] = "0";
$datas["tayeed"] = "false";
$datas["saham"] = "0";
$datas["game"] = "0";
$datas["coin"] = "0";
$datas["zarib"] = "0";
$datas["hads"] = "0";
$datas["DartCoin"] = "0";
$datas["bardasht"] = "0";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($datas['money'] < 0){
$datas["money"] = 0;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 موجودی شما بروزرسانی شد جهت مشاهده دوباره بر روی حساب کاربری کلیک کنید");
}else{
if($datas['bardasht'] > $datas['money']){
$datas["bardasht"] = $datas['money'];
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 موجودی شما بروزرسانی شد جهت مشاهده دوباره بر روی حساب کاربری کلیک کنید");
}else{
if($datas['bardasht'] < 0){
$datas["bardasht"] = 0;
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 موجودی شما بروزرسانی شد جهت مشاهده دوباره بر روی حساب کاربری کلیک کنید");
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if( $username2 == NULL){
SendMessage($from_id,"<code>⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است.</code>


✅ اطلاعات حساب کاربری شما 👇

",json_encode(['inline_keyboard' => [
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"𝑛𝑜𝑛𝑒",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$from_id",'callback_data'=>'prooo'],['text'=>"🔥 شناسه کاربری شما :",'callback_data'=>'prooo']],
[['text'=>"{$datas['money']}",'callback_data'=>'prooo'],['text'=>"💰 موجودی حساب شما :",'callback_data'=>'prooo']],
[['text'=>"{$datas['bardasht']}",'callback_data'=>'prooo'],['text'=>"💸 مقدار سکه قابل برداشت :",'callback_data'=>'prooo']],
[['text'=>"{$datas['subset']}",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"{$datas['game']}",'callback_data'=>'prooo'],['text'=>"💣 تعداد بازی ها :",'callback_data'=>'prooo']],
[['text'=>"{$datas['warn']}",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $from_id / coin : {$datas['money']} &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botid"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]]));
}else{
SendMessage($from_id,"<code>⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است.</code>


✅ اطلاعات حساب کاربری شما 👇

",json_encode(['inline_keyboard' => [
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"@$username2",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$from_id",'callback_data'=>'prooo'],['text'=>"🔥 شناسه کاربری شما :",'callback_data'=>'prooo']],
[['text'=>"{$datas['money']}",'callback_data'=>'prooo'],['text'=>"💰 موجودی حساب شما :",'callback_data'=>'prooo']],
[['text'=>"{$datas['bardasht']}",'callback_data'=>'prooo'],['text'=>"💸 مقدار سکه قابل برداشت :",'callback_data'=>'prooo']],
[['text'=>"{$datas['subset']}",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"{$datas['game']}",'callback_data'=>'prooo'],['text'=>"💣 تعداد بازی ها :",'callback_data'=>'prooo']],
[['text'=>"{$datas['warn']}",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $from_id / coin : {$datas['money']} &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botid"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]]));
}}}}}
if($text =="بروزرسانی ⚡" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"

🚀آخرین نسخه موجود ، در ربات شما نصب شده است🚀

🚀نسخه فعلی : 7.2🚀

");
}
if($text =="سرعت ممبر🚀" and $type == "private"){
SendMessage($from_id,"

⚡جهت شارژ تعداد سکه مورد نظر را به آیدی عددی ( <code>$addi</code> ) ارسال نمایید⚡

⚡پس از واریز حساب شما خودکار شارژ می شود⚡

🎈 هر 1000 الماس معادل 25,000 سکه میباشد 🤯

");
}
if($text =="🚀جت ممبر" and $type == "private"){
SendMessage($from_id,"

⚡جهت شارژ تعداد سکه مورد نظر را به آیدی عددی ( <code>$addi</code> ) ارسال نمایید⚡

⚡پس از واریز حساب شما خودکار شارژ می شود⚡

🎉 هر 1000 الماس معادل 25,000 سکه میباشد 🥳

");
}
if($text =="$dok20" and $type == "private"){
SendMessage($from_id,"ᵂᴬᴵᵀ ᴬ ᴹᴼᴹᴱᴺᵀ • • •");
sleep(3);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▭▭▭▭▭▭▭▭▭▭ | 0%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▭▭▭▭▭▭▭▭▭ | 10%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▭▭▭▭▭▭▭▭ | 20%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▭▭▭▭▭▭▭ | 30%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▭▭▭▭▭▭ | 40%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▭▭▭▭▭ | 50%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▬▭▭▭▭ | 60%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▬▬▭▭▭ | 70%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▬▬▬▭▭ | 80%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▬▬▬▬▭ | 90%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'▬▬▬▬▬▬▬▬▬▬ | 100%','message_id'=>$message_id + 1]);
sleep(0.2);
bot('editMessageText',['chat_id'=>$from_id,'text'=>'┄┅┈┉┅┉┈┅┄┄┅┈┉ 𝑈𝑃𝐷𝐴𝑇𝐸𝐷 ┉┈┅┄┄┅┈┉┅┉┈┅┄','message_id'=>$message_id + 1]);
}
elseif($text =="💬 کامنت گذاشتن" and $type == "private"){
mkdir("commentX");
SendMessage($from_id,"
بخش کامنت ها 💬 میتوانید نظرات خود را به اشتراک بگذارید . . . 

┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment1
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment2
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
$coment3
┄┅┈┉┅┉┈┅┄┄┅┈┉|┉┈┅┄┄┅┈┉┅┉┈┅┄
",json_encode(['inline_keyboard' => [
[['text'=>"💬 کامنت گذاشتن",'callback_data'=>'comment']],
[['text'=>"💣 قوانین کامنت گذاشتن",'callback_data'=>'Rules']],
[['text'=>"♻️ آپدیت",'callback_data'=>'up date']],
]]));exit();
}
elseif($step == "come3" and $data !="back page" and $text !="🔙 بازگشت" and $type == "private"){
SendMessage($from_id,"
🎉 کامنت شما با موفقیت گذاشته شد هم اکنون در بخش کامنت ها قابل دیدن است
");
$com = "$first_name : $text";
$datas["step"] = "none";
file_put_contents("commentX/coment3.txt",$coment2);
file_put_contents("commentX/coment2.txt",$coment1);
file_put_contents("commentX/coment1.txt",$com);
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
if($text =="💣 بازی دو نفره" and $type == "private"){
$datas["step"] = "chat7";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💯 ای دی عددی شخصی که میخواید باهاش بازی کنید را ارسال کنید . . .",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}
elseif($step == "chat7" and $text !="🔙 بازگشت" and $text !="$from_id" and is_numeric($text) and $type == "private"){
@$XR = file_get_contents("chat/$text/RT.txt");
if($XR != true){
mkdir("chat");
mkdir("chat/$from_id");
file_put_contents("chat/$from_id/Request.txt",$text);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
mkdir("chat");
mkdir("chat/$text");
file_put_contents("chat/$text/Request.txt",$from_id);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ درخواست شما با موفقیت به کاربر مورد نظر ارسال شد
");
SendMessage($text,"
✅ یک نفر درخواست بازی داده است 

🔐 آیا بازی دو نفره را قبول میکنید یا رد میکنید ؟ 
",json_encode(['keyboard'=>[
[['text'=>"✅ تایید درخواست"],['text'=>"❌ رد درخواست"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id,"
❌ کاربر مورد نظر در حال بازی با شخص دیگری است
",$button);
}}
if($text =="✅ تایید درخواست" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
@$RQ = file_get_contents("chat/$from_id/Request.txt");
@$SD = file_get_contents("chat/$from_id/RT.txt");
if($SD != true){
mkdir("chat");
mkdir("chat/$from_id");
file_put_contents("chat/$from_id/RT.txt",$RQ);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
mkdir("chat");
mkdir("chat/$RQ");
file_put_contents("chat/$RQ/RT.txt",$from_id);
$datas["step"] = "play";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ درخواست با موفقیت پذیرفته شد
",$button_paly);
$PlayerRQ = json_decode(file_get_contents("data/$RQ/$RQ.json"),true);
$PlayerRQ["step"] = "play";
$outjson = json_encode($PlayerRQ,true);
file_put_contents("data/$RQ/$RQ.json",$outjson);
SendMessage($RQ,"
✅ کاربر مورد نظر درخواست شما رو پذیرفت
",$button_paly);
}else{
SendMessage($from_id,"
شما در حال بازی شخص دیگری هستید ❌
",$button_paly);
}}
if($text =="❌ رد درخواست" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
@$RQ = file_get_contents("chat/$from_id/Request.txt");
SendMessage($from_id,"
❌ در خواست با موفقیت رد شد
",$button);
SendMessage($RQ,"
❌ کاربر مورد نظر درخواست شما را رد کرد 
",$button);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$close = "";
file_put_contents("chat/$from_id/RT.txt",$close);
file_put_contents("chat/$RQ/RT.txt",$close);
file_put_contents("chat/$from_id/Request.txt",$close);
file_put_contents("chat/$RQ/Request.txt",$close);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}
elseif($text =="📩 گفتگو کردن" and $step == "play" and $type == "private"){
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$datas["step"] = "chat8";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
👈🏻پیام خود را ارسال کنید تا به بازیکن وصل شده ارسال کنم✅
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
if($step == "chat8" and $text !="✅ قبول کردن" and $text !="❌ رد کردن" and $text !="📩 گفتگو کردن" and $text !="🔙 بازگشت" and $text !="✅ تایید درخواست" and $text !="❌ رد درخواست" and $text !="🔙 برگشت" and $text !="❌ قطع اتصال" and $text !="⚖ قوانین" and $text !="📚 راهنما" and $text !="🎮 بریم بازی" and $text !="📛گزارش بازیکن" and $text !="✌️🏻" and $text !="👊🏻" and $text !="🖐🏻" and $type == "private"){
@$WT = file_get_contents("chat/$from_id/RT.txt");
 if($WT == true){
 if(preg_match('/[a-z]/i',$text)){
SendMessage($from_id,"امکان ارسال لینک وجود ندارد");
exit();
  }
  if(preg_match('~bot|t(elegram)?\.me|@|\.ir|\.com~i',$text)){
SendMessage($from_id,"امکان ارسال لینک وجود ندارد");
exit();
  }
SendMessage($WT,$text);
SendMessage($from_id,"
❇️ پیام شما با موفقیت به بازیکن ارسال گردید . . . 

🔐 جهت ارسالی پیامی دیگر متن پیام را ارسال کنید

🔙 در غیر این صورت بر روی برگشت کلیک کنید 
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id,"
❌ ارتباط شما با کاربر مورد نظر قطع شده است روی | 🔙 بازگشت | بزنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}
elseif($text =="❌ قطع اتصال" and $step == "play" and $type == "private"){
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
@$WT = file_get_contents("chat/$from_id/RT.txt");
SendMessage($WT,"
ارتباط شما توسط کاربر مقابل قطع شد✅

👈🏻به منوی اصلی بازگشتید.
",$button);
SendMessage($from_id,"
ارتباط با موفقیت قطع شد✅

👈🏻به منوی اصلی بازگشتید.
",$button);
$close = "";
file_put_contents("chat/$from_id/RT.txt",$close);
file_put_contents("chat/$WT/RT.txt",$close);
$PlayerOff = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$PlayerOff["step"] = "none";
$outjson = json_encode($PlayerOff,true);
file_put_contents("data/$WT/$WT.json",$outjson);
}

elseif($text =="⚖ قوانین" and $step == "play" and $type == "private"){
SendMessage($from_id,"
⚖ قوانین بازی دو نفره


1⃣ - استفاده از کلمات رکیک در گفتگو با فرد ممنوع میباشد.

2⃣ - تقلب ممنوع میباشد.

3⃣ - در صورت باخت حق اعتراض به بازیکن مقابل را ندارید

💣 Slow - bet | $date / $time

",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
elseif($text =="📚 راهنما" and $step == "play" and $type == "private"){
SendMessage($from_id,"
📚 راهنما بازی دو نفره

1⃣ - با استفاده از بخش گفتگو کردن با بازیکن مقابل گفتگو کنید . . .

2⃣ - اگر فرد مقابل از کلمات رکیک استفاده کرد از بخش گزارش آن را گزارش کنید . . .

3⃣ - در صورتی قصد خارج شدن از بخش دو نفره را دارید بر روی قطع اتصال بزنید

4⃣ - جهت شروع بازی از بخش بریم بازی اقدام کنید

💣 Slow Bet | $date - $time
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
elseif($text =="📛گزارش بازیکن" and $step == "play" and $type == "private"){
$datas["step"] = "report";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
📛لطفاً دلیل گزارش را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
if($step == "report" and $text !="✅ قبول کردن" and $text !="❌ رد کردن" and $text !="📩 گفتگو کردن" and $text !="🔙 بازگشت" and $text !="✅ تایید درخواست" and $text !="❌ رد درخواست" and $text !="🔙 برگشت" and $text !="❌ قطع اتصال" and $text !="⚖ قوانین" and $text !="📚 راهنما" and $text !="🎮 بریم بازی" and $text !="📛گزارش بازیکن" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🎈 گزارش شما با موفقیت ثبت گردید و در اسرع وقت پیگیری میشود 
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
@$WT = file_get_contents("chat/$from_id/RT.txt");
SendMessage($addi,"🔰 اطلاعات کاربری که گزارش داده : 

✅ نام : <code>$first_name</code>
✅ ای دی عددی : <code>$from_id</code>
✅ ای دی : <code>@$username2</code>

💯 ای دی فردی که گزارش شده : <code>$WT</code>


📑 متن گزارش :  

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
<code>$text</code>
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄");
}
elseif($text =="🎮 بریم بازی" and $step == "play" and $type == "private"){
$coin = $datas["money"];
if($coin >= 1000){
$datas["step"] = "gamenew";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 panel bet :

💰 مقدار سکه مورد نظر خود را ارسال کنید حداقل 1,000 سکه میباشد . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "play";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
⚠️ خطا ❗️

❌ موجودی شما کافی نمیباشد حداقل موجودی برای بازی 1,000 سکه است 
",$button_play);exit();
}}
elseif($step == "gamenew" and is_numeric($text) and $type == "private"){
$coin = $datas["money"];
if($text >= 1000 and $text <= 1000000 and $coin >= $text){
@$WT = file_get_contents("chat/$from_id/RT.txt");
file_put_contents("chat/$WT/coingame.txt",$text);
SendMessage($from_id,"
✅ درخواست بازی ارسال شد . . . 
");
$PlayerOff = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$PlayerOff["step"] = "dar";
$outjson = json_encode($PlayerOff,true);
file_put_contents("data/$WT/$WT.json",$outjson);
SendMessage($WT,"
✅ درخواست بازی برای ( <code>$text</code> ) نیترو آیا درخواست را می‌پذیرید ؟
",json_encode(['keyboard'=>[
[['text'=>"✅ قبول کردن"],['text'=>"❌ رد کردن"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ لطفاً مبلغ بازی را بین 1,000 تا 1,000,000 هزار نیترو سین وارد کنید با توجه به موجودی خود . . . ",$button_play);
}}
if($text =="✅ قبول کردن" and $step == "dar" and $type == "private"){
$coinX = file_get_contents("chat/$from_id/coingame.txt");
$coinplay = $datas["money"];
if($coinplay >= $coinX){
$datas["step"] = "nobat";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
@$WT = file_get_contents("chat/$from_id/RT.txt");
@$wincoin = file_get_contents("chat/$from_id/coingame.txt");
file_put_contents("chat/$from_id/wincoin.txt",$wincoin);
unlink("chat/$from_id/select.txt");
unlink("chat/$WT/select.txt");
SendMessage($WT,"
✅ کاربر مقابل درخواست شما را پذیرفت . . . 

🔰 « نوبت حریف است . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($from_id,"
✅ درخواست بازی با موفقیت پذیرفته شد 

یکی از گزینه های زیر را انتخاب کنید 👇
",$button_sang);
}else{
SendMessage($from_id,"
❌ موجودی شما برای انجام این بازی کافی نیست . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

if($text =="❌ رد کردن" and $step == "dar" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
@$WT = file_get_contents("chat/$from_id/RT.txt");
SendMessage($from_id,"
❌ در خواست با موفقیت رد شد
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
❌ کاربر مورد نظر درخواست شما را رد کرد 
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
elseif($text =="🖐🏻" and $step == "nobat" and $type == "private"){
@$NOR = file_get_contents("chat/$from_id/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$from_id/RT.txt");
file_put_contents("chat/$from_id/select.txt",kaqaz);
$PlayerOff = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$PlayerOff["step"] = "nobat2";
$outjson = json_encode($PlayerOff,true);
file_put_contents("data/$WT/$WT.json",$outjson);
SendMessage($from_id,"
💣 » Scissors Paper Rock :

انتخاب شما : $text

الان نوبت حریف هست
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
🌹 نوبت شما است 

روی یکی از گزینه های زیر کلیک کنید 👇🏻
",json_encode(['keyboard'=>[
[['text'=>"✌️🏻 قیچی"],['text'=>"👊🏻 سنگ"],['text'=>"🖐🏻 کاغذ"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($text =="👊🏻" and $step == "nobat" and $type == "private"){
@$NOR = file_get_contents("chat/$from_id/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$from_id/RT.txt");
file_put_contents("chat/$from_id/select.txt",sang);
$PlayerOff = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$PlayerOff["step"] = "nobat2";
$outjson = json_encode($PlayerOff,true);
file_put_contents("data/$WT/$WT.json",$outjson);
SendMessage($from_id,"
💣 » Scissors Paper Rock :

انتخاب شما : $text

الان نوبت حریف هست
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
🌹 نوبت شما است 

روی یکی از گزینه های زیر کلیک کنید 👇🏻
",json_encode(['keyboard'=>[
[['text'=>"✌️🏻 قیچی"],['text'=>"👊🏻 سنگ"],['text'=>"🖐🏻 کاغذ"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($text =="✌️🏻" and $step == "nobat" and $type == "private"){
@$NOR = file_get_contents("chat/$from_id/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$from_id/RT.txt");
file_put_contents("chat/$from_id/select.txt",gechi);
$PlayerOff = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$PlayerOff["step"] = "nobat2";
$outjson = json_encode($PlayerOff,true);
file_put_contents("data/$WT/$WT.json",$outjson);
SendMessage($from_id,"
💣 » Scissors Paper Rock :

انتخاب شما : $text

الان نوبت حریف هست
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
🌹 نوبت شما است 

روی یکی از گزینه های زیر کلیک کنید 👇🏻
",json_encode(['keyboard'=>[
[['text'=>"✌️🏻 قیچی"],['text'=>"👊🏻 سنگ"],['text'=>"🖐🏻 کاغذ"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}
elseif($text =="✌️🏻 قیچی" and $step == "nobat2" and $type == "private"){
@$WT = file_get_contents("chat/$from_id/RT.txt");
@$NEX = file_get_contents("chat/$from_id/select.txt");
if($NEX != true){
file_put_contents("chat/$from_id/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "gechi"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : ✌️🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : ✌️🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
if($fun == "sang"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 👊🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] - $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : ✌️🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);
}else{
if($fun == "kaqaz"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 🖐🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] + $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : ✌️🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] - $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);

}
}
}
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($text =="👊🏻 سنگ" and $step == "nobat2" and $type == "private"){
@$WT = file_get_contents("chat/$from_id/RT.txt");
@$NEX = file_get_contents("chat/$from_id/select.txt");
if($NEX != true){
file_put_contents("chat/$from_id/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "sang"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 👊🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 👊🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
if($fun == "kaqaz"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 🖐🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] - $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : 👊🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);
}else{
if($fun == "gechi"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : ✌️🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] + $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 👊🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);

}
}
}
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($text =="🖐🏻 کاغذ" and $step == "nobat2" and $type == "private"){
@$WT = file_get_contents("chat/$from_id/RT.txt");
@$NEX = file_get_contents("chat/$from_id/select.txt");
if($NEX != true){
file_put_contents("chat/$from_id/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "kaqaz"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : 🖐🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : 🖐🏻

🌹 » شما مساوی شدید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
if($fun == "gechi"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : ✌️🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] - $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 🖐🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);
}else{
if($fun == "sang"){
SendMessage($from_id,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما :  🖐🏻
🔰 » انتخاب حریف : 👊🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$datas = json_decode(file_get_contents("data/$from_id/$from_id.json"),true);
$coin = $datas['money'] + $newcoin;
$datas["money"] = "$coin";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 🖐🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("data/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$PlayerTwo["money"] = "$coin";
$PlayerTwo["step"] = "none";
$outjson = json_encode($PlayerTwo,true);
file_put_contents("data/$WT/$WT.json",$outjson);

}
}
}
}else{
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

if($text =="🔙 برگشت" and $type == "private"){
$WT = file_get_contents("chat/$from_id/RT.txt");
if($WT == true){
$datas["step"] = "play";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
♻️ به منوی بازی دو نفره بازگشتید
",$button_paly);
}else{
SendMessage($from_id,"
❌ ارتباط شما با کاربر مورد نظر قطع شده است روی | 🔙 بازگشت | بزنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}

if($text =="$dok21" and $type == "private"){
$datas["step"] = "dont";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$rand = rand(11111,99999);
mkdir("Gmail");
mkdir("Gmail/$from_id");
file_put_contents("Gmail/$from_id/eh.txt",$rand);
SendMessage($from_id,"♻️ جیمیل خود را ارسال کنید . . .",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}
elseif($step =="dont" and $text !="🔙 بازگشت" and $type == "private"){
$cod = file_get_contents("Gmail/$from_id/eh.txt");
mail("$text","hello !","@$botid","ʏᴏᴜʀ ᴄᴏᴅ : $cod | ᴛʜᴇ ᴄᴏᴅᴇ ᴡᴀs sᴇɴᴛ ᴀᴛ : $time");
$datas["step"] = "gem";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"✅ کد یکبار مصرف بازیابی را به جیمیل <code>$text</code> ارسال کردیم 

⚙ پوشه هرزنامه را چک کنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
} 
elseif($step =="gem" and is_numeric($text) and $text !="🔙 بازگشت" and $type == "private"){
$Gmailhovit = file_get_contents("Gmail/$from_id/eh.txt");
if($Gmailhovit == $text){
file_put_contents("Gmail/$from_id/step.txt","tayeede");
file_put_contents("Gmail/$from_id/Gmailz.txt","1000");
$datas["step"] = "Special";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"✅ کد صحیح است با موفقیت به بخش ویژه وارد شدید",$button_Special);
}else{
SendMessage($from_id,"
❌ کد اشتباه است مجدد امتحان کنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}

if($text =="🚀 موشک" and $type == "private"){
$datas["step"] = "sksksk";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Rocket Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step == "sksksk" and is_numeric($text) and $type == "private"){
if($text < 100){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط 100 سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "hak-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 ضریب مورد نظر خود را ارسال کنید . . .",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "hak-") !== false and is_numeric($text) and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
if($text >= 1.01 and $text <= 999.99){
mkdir("play");
$rand = rand(111111111,999999999);
mkdir("play/$rand");
file_put_contents("play/$rand/game.txt","$text~$b[1]~$from_id");
$jsonID = json_decode(file_get_contents("play/id.json"),true);
$jsonID["id"][] = "$from_id";
$jsonID = json_encode($jsonID,true);
file_put_contents("play/id.json",$jsonID);
$newmoney = $datas['money'] - $b[1];
$datas["money"] = "$newmoney";
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$newgame = $datas['game'] + 1;
$datas["game"] = "$newgame";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
💣 Rocket Panel :
              
💰 مقدار سکه ثبت شده : $b[1] سکه

💣 ضریب درخواستی : $text


➖➖➖➖➖
🚀 برای مشاهده وضعیت بازی در کانال زیر عضو شوید و بازی خود را تماشا کنید :

🚀 $moshak
");
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ ضریب ارسال صحیح نیست حداقل ضریب 1.01 و حداکثر 999.99 میباشد.",$button);exit();
}}
if($step == "akakajkk" and is_numeric($text) and $type == "private"){
if($text < 100){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط 100 سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "alaccurk-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 ضریب مورد نظر خود را ارسال کنید . . .

",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "alaccurk-") !== false and is_numeric($text) and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
if($text >= 1.01 and $text <= 999.99){
$datas["zarib"] = "$text";
$datas["coin"] = "$b[1]";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
⚙️ اطلاعات بازی اتومات شما به شکل زیر با موفقیت ثبت شد!

💰 مقدار سکه بازی در هر دست : $b[1] سکه

🔥 ضریب بازی در هر دست : $text

👇🏻 هم اکنون میتوانید با لمس گزینه شیشه ای زیر بازی خود را بصورت اتومات ثبت کنید . . .
",json_encode(['inline_keyboard' => [
[['text' => "🔥 شروع بازی اتوماتیک موشک 🔥",'url' => "https://t.me/$botid?start=Automatic"]],
]]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ ضریب ارسال صحیح نیست حداقل ضریب 1.01 و حداکثر 999.99 میباشد.",$button);exit();
}}
if($text =="🎯 دارت گروهی" and $type == "private"){
$datas["step"] = "DaRtInG";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💣 Dart Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل 100 سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step == "DaRtInG" and is_numeric($text) and $type == "private"){
if($text < 100){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط 100 سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "dddars-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 نوع دارت را از منو زیر انتخاب کنید . . . 
",json_encode(['keyboard'=>[
[['text'=>"🎯 دارت سفید گروهی"],['text'=>"🎯 دارت قرمز گروهی"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "dddars-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
$DartPanel = ['🎯 دارت قرمز گروهی','🎯 دارت سفید گروهی'];
$keys = [$DartPanel[0]=>'Dartred',$DartPanel[1]=>'Dartwhite'];
$select = $keys[$text];
if(in_array($text,$DartPanel)){
mkdir("dart");
$rand = rand(111111111,999999999);
mkdir("dart/$rand");
file_put_contents("dart/$rand/game.txt","$select~$b[1]~$from_id");
$jsonID = json_decode(file_get_contents("dart/id.json"),true);
$jsonID["id"][] = "$from_id";
$jsonID = json_encode($jsonID,true);
file_put_contents("dart/id.json",$jsonID);
$newmoney = $datas['money'] - $b[1];
$datas["money"] = "$newmoney";
if($datas['bardasht'] <= 0){
$mojt = $datas['bardasht'] - $b[1];
$datas["bardasht"] = $mojt;
}
$newgame = $datas['game'] + 1;
$datas["game"] = "$newgame";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
💣 Dart Panel :
              
💰 مقدار سکه ثبت شده : $b[1] سکه

🎯 نوع دارت : $text


➖➖➖➖➖
🚀 برای مشاهده وضعیت بازی در کانال زیر عضو شوید و بازی خود را تماشا کنید :

🚀 $DartChannel
");
}else{
SendMessage($from_id,"⚠️ از منوی زیر نوع دارت را انتخاب کنید ! 
",json_encode(['keyboard'=>[
[['text'=>"🎯 دارت سفید گروهی"],['text'=>"🎯 دارت قرمز گروهی"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}
if($step == "BehHv" and $type == "private"){
if($text < 100){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط 100 سکه است.)",$button);exit();
}else{
if($text > $datas['money']){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"موجودی کافی نیست",$button);
}else{
$datas["step"] = "hegust-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎯 نوع دارت را از منو زیر انتخاب کنید . . . 
",json_encode(['keyboard'=>[
[['text'=>"🎯 دارت سفید گروهی"],['text'=>"🎯 دارت قرمز گروهی"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "hegust-") !== false and $text != "🔙 بازگشت" and $type == "private"){
$b = explode("-",$step);
$DartPanel = ['🎯 دارت قرمز گروهی','🎯 دارت سفید گروهی'];
$keys = [$DartPanel[0]=>'Dartred',$DartPanel[1]=>'Dartwhite'];
$select = $keys[$text];
if(in_array($text,$DartPanel)){
$datas["hads"] = "$select";
$datas["DartCoin"] = "$b[1]";
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
⚙️ اطلاعات بازی اتومات شما به شکل زیر با موفقیت ثبت شد!

💰 مقدار سکه بازی در هر دست : $b[1] سکه

🔥 نوع دارت انتخابی : $text

👇🏻 هم اکنون میتوانید با لمس گزینه شیشه ای زیر بازی خود را بصورت اتومات ثبت کنید . . .
",json_encode(['inline_keyboard' => [
[['text' => "🔥 شروع بازی اتوماتیک دارت گروهی 🔥",'url' => "https://t.me/$botid?start=DartAuto"]],
]]));
}else{
SendMessage($from_id,"⚠️ از منوی زیر نوع دارت را انتخاب کنید ! 
",json_encode(['keyboard'=>[
[['text'=>"🎯 دارت سفید گروهی"],['text'=>"🎯 دارت قرمز گروهی"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}
 // [ ADMIN ] //
elseif($text =="/admin" or $text == "/panel" or $text == "✨بازگشت✨" ){
if(in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"✅ادمین عزیز به پنل مدیریت خوش اومدی 🙃",$button_admin);
}}

if($text =="آمار 📊" and in_array($from_id,$admin) and $type == "private"){
$alluser = file_get_contents("data/datas.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
SendMessage($from_id,"

🚀 تعداد کاربران : $allusers 🚀


");
}
if($text =="آمار 📊" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"
🚀 تعداد کاربران بن شده : $ben

");
}
if($text =="ارسال همگانی ✨" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "rall";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا پیام خود را جهت ارسال بفرستید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="rall" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ارسال شد.");
$all_member = fopen( "data/datas.txt", 'r');
while( !feof( $all_member)) {
$user = fgets( $all_member);
SendMessage($user,"$text");
}}
if($text =="فوروارد همگانی ❄️" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "forall";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفا پیام خود را جهت فوروارد ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="forall" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"فوروارد شد.");
$all_member = fopen( "data/datas.txt", 'r');
while( !feof( $all_member)) {
$user = fgets( $all_member);
SendMessage($user,"$text");
bot('ForwardMessage',[
'chat_id'=>$user,
'from_chat_id'=>$from_id,
'message_id'=>$message_id
]);
}    
}

if($text =="پیام به کاربر 📨" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "pm";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="pm" and is_numeric($text) and $type == "private"){
if(file_exists("data/$text/$text.json")){
$datas["step"] = "PPMM-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," ♻️ پیام خود را جهت ارسال پیام به کاربر <code>$text</code> ارسال کنید . . .
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PPMM-") !== false and $text != "✨بازگشت✨" and $type == "private"){
$b = explode("-",$step);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ پیام شما ( <code>$text</code> ) با موفقیت به کاربر ( <code>$b[1]</code> ) ارسال شد
",$button_admin);
SendMessage($b[1],"
🎉 کاربر گرامی شما یک پیام از طرف مدیر دارید ❗️
📥 پیام :
$text");
}
if($text =="کسر سکه 🥈" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "coinoff";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="coinoff" and is_numeric($text) and $type == "private"){
if(file_exists("data/$text/$text.json")){
$datas["step"] = "PcoinoffM-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"💰 تعداد سکه جهت کسر سکه از کاربر <code>$text</code> را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PcoinoffM-") !== false and $text != "✨بازگشت✨" and $type == "private"){
$b = explode("-",$step);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ با موفقیت از حساب کاربری <code>$b[1]</code> مقدار <code>$text</code> سکه کسر شد
",$button_admin);
 SendMessage($b[1],"
💵 از حساب کاربری شما مقدار $text سکه کم شد 
");
$usersharj = json_decode(file_get_contents("data/$b[1]/$b[1].json"),true);
$coin = $usersharj['money'] - $text;
$usersharj["money"] = "$coin";
$usersharj["step"] = "none";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$b[1]/$b[1].json",$outjson);
}
if($text =="افزایش سکه 🥇" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "coinon";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="coinon" and is_numeric($text) and $type == "private"){
if(file_exists("data/$text/$text.json")){
$datas["step"] = "PcoinonM-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," 💰 تعداد سکه جهت افزایش سکه کاربر <code>$text</code> را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PcoinonM-") !== false and $text != "✨بازگشت✨" and $type == "private"){
$b = explode("-",$step);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ با موفقیت , حساب کاربری <code>$b[1]</code> مقدار <code>$text</code> سکه شارژ شد
",$button_admin);
$usersharj = json_decode(file_get_contents("data/$b[1]/$b[1].json"),true);
$coin = $usersharj['money'] + $text;
$coinsharji = $coin + $bonos4;
$usersharj["money"] = "$coinsharji";
$usersharj["step"] = "none";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$b[1]/$b[1].json",$outjson);
$coinsharj = $text + $bonos4;
 SendMessage($b[1],"
💵 حساب کاربری شما مقدار $coinsharj سکه شارژ شد.
");
}
if($text =="❗️اخطار به کاربر" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "warnos";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="warnos" and is_numeric($text) and $type == "private"){
if(file_exists("data/$text/$text.json")){
$datas["step"] = "PwarnosM-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," 💯 تعداد اخطار را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PwarnosM-") !== false and $text != "✨بازگشت✨" and $type == "private"){
$b = explode("-",$step);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
❗️ $text اخطار به کاربر $b[1] اضافه شد . . .
",$button_admin);
$usersharj = json_decode(file_get_contents("data/$b[1]/$b[1].json"),true);
$warnNew = $usersharj['warn'] + $text;
$usersharj["warn"] = "$warnNew";
$usersharj["step"] = "none";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$b[1]/$b[1].json",$outjson);
 SendMessage($b[1],"
❗️اوپس ! 
شما تعداد $text اخطار دریافت کردید . . .
");
SendMessage($from_id,"
❗️ تعداد اخطار های کاربر : $warnNew
",$button_admin);
}
if($text =="❕حذف اخطار کاربر" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "warnoff";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="warnoff" and is_numeric($text) and $type == "private"){
if(file_exists("data/$text/$text.json")){
$datas["step"] = "PwarnoffM-$text";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," 💯 تعداد اخطار را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PwarnoffM-") !== false and $text != "✨بازگشت✨" and $type == "private"){
$b = explode("-",$step);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
❗️ $text تعداد اخطار از کاربر $b[1] کم شد . . .
",$button_admin);
$usersharj = json_decode(file_get_contents("data/$b[1]/$b[1].json"),true);
$warnNew = $usersharj['warn'] - $text;
$usersharj["warn"] = "$warnNew";
$usersharj["step"] = "none";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$b[1]/$b[1].json",$outjson);
 SendMessage($b[1],"
❕اوپس ! 
شما تعداد $text اخطار از شما کم شد . . .
");
SendMessage($from_id,"
❗️ تعداد اخطار های کاربر : $warnNew
",$button_admin);
}

if($text =="بلاک کردن 🍂" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "bl";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"آیدی عددی فرد مورد نظر را ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="bl" and $text !="✨بازگشت✨" and $type == "private"){
$usersharj = json_decode(file_get_contents("data/$text/$text.json"),true);
$usersharj["step"] = "bon";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$text/$text.json",$outjson);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$newben = $ben + 1;
file_put_contents("data/ben.txt", $newben);
SendMessage($from_id,"بلاک شد",$button_admin);
}

if($text =="آنبلاک کردن ⭐" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "obl";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"آیدی عددی فرد مورد نظر را ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="obl" and $text !="✨بازگشت✨" and $type == "private"){
$usersharj = json_decode(file_get_contents("data/$text/$text.json"),true);
$usersharj["step"] = "none";
$outjson = json_encode($usersharj,true);
file_put_contents("data/$text/$text.json",$outjson);
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
$newben = $ben - 1;
file_put_contents("data/ben.txt", $newben);
SendMessage($from_id,"آنبلاک شد",$button_admin);
}
// Set dok ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="🍁 بخش تنظیم دکمه ها 🍁" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم دکمه ها خوش آمدید 🎉 


پس از تغییر اسم دکمه ها ربات رو مجدد /start کنید 🔐
",$button_dok);
}
if($text =="📊 دکمه های فعلی" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "free";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌹 لیست اسم دکمه های فعلی : 

<code>$dok1</code>
┄┅┈┉┅┉┈┅┄
<code>$dok2</code>
┄┅┈┉┅┉┈┅┄
<code>$dok3</code>
┄┅┈┉┅┉┈┅┄
<code>$dok4</code>
┄┅┈┉┅┉┈┅┄
<code>$dok5</code>
┄┅┈┉┅┉┈┅┄
<code>$dok6</code>
┄┅┈┉┅┉┈┅┄
<code>$dok7</code>
┄┅┈┉┅┉┈┅┄
<code>$dok8</code>
┄┅┈┉┅┉┈┅┄
<code>$dok9</code>
┄┅┈┉┅┉┈┅┄
<code>$dok10</code>
┄┅┈┉┅┉┈┅┄
<code>$dok11</code>
┄┅┈┉┅┉┈┅┄
<code>$dok12</code>
┄┅┈┉┅┉┈┅┄
<code>$dok13</code>
┄┅┈┉┅┉┈┅┄
<code>$dok14</code>
┄┅┈┉┅┉┈┅┄
<code>$dok15</code>
┄┅┈┉┅┉┈┅┄
<code>$dok16</code>
┄┅┈┉┅┉┈┅┄
<code>$dok17</code>
┄┅┈┉┅┉┈┅┄
<code>$dok18</code>
┄┅┈┉┅┉┈┅┄
<code>$dok19</code>
┄┅┈┉┅┉┈┅┄
<code>$dok20</code>
┄┅┈┉┅┉┈┅┄
<code>$dok21</code>
┄┅┈┉┅┉┈┅┄

•••{ $creator }•••");
}
if($text =="دکمه 💣 ثبت بازی جدید" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok1";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok1" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok1.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 📊 پیش‌بینی ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok2";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok2" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok2.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🔐 حساب کاربری" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok3";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok3" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok3.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🆘 پشتیبانی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok4";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok4" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok4.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 💡راهنما" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok5";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok5" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok5.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🎗سهام" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok6";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok6" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok6.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه قوانین ربات 📑" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok7";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok7" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok7.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه ارسال اکانت 💫" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok8";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok8" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok8.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 💸 بونوس" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok9";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok9" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok9.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 💵 شارژ حساب" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok10";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok10" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok10.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 💰 برداشت موجودی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok11";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok11" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok11.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه گفت و گو 🔰" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok12";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok12" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok12.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 💰 انتقال سکه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok13";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok13" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok13.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🎁 برترین ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok14";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok14" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok14.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🥂 وضعیت ربات" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok15";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok15" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok15.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
} 
    if($text =="دکمه 📜 تاریخچه بازی ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok16";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok16" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok16.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🔥 تراکنش ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok17";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok17" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok17.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه ☁️ چت با اعضا ربات" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok18";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok18" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok18.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🎉 هدیه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok19";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok19" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok19.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه ♻️ آپدیت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok20";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok20" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok20.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
    if($text =="دکمه 🌟بخش ویژه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dok21";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را وارد کنید 🍀");
} 
	if($step =="dok21" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("button/dok21.txt", $text);
SendMessage($from_id,"🌷 اسم دکمه با موفقیت به <code>$text</code> تغییر یافت ✨");
}
     // Set up admin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
 
if($text =="بخش ادمین ها 🔋" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش حذف و اضافه کردن ادمین ها خوش آمدید 🎉
",$button_add);
}
if($text =="📊 آمار ادمین ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"📊 لیست ادمین ها : 
<code>𝟏 - $admin1</code>
<code>𝟐 - $admin2</code>
<code>𝟑 - $admin3</code>

مالک ربات : ♕ <code>$owner</code> ♕
",$button_add);
}
if($text =="✅ اضافه کردن ادمین 1" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin1";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
	if($step =="admin1" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin1.txt", $text);
SendMessage($from_id,"🎊 ادمین 1 با موفقیت اضافه شد");
}
    if($text =="✅ اضافه کردن ادمین 2" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin2";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
	if($step =="admin2" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin2.txt", $text);
SendMessage($from_id,"🎊 ادمین 2 با موفقیت اضافه شد");
}
    if($text =="✅ اضافه کردن ادمین 3" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin3";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
	if($step =="admin3" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin3.txt", $text);
SendMessage($from_id,"🎊 ادمین 3 با موفقیت اضافه شد");
}
    
    if($text =="❌ حذف ادمین 1" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin-1";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"برای حذف ادمین 1 ای دی عددی آنرا وارد کنید ❌");
} 
	if($step =="admin-1" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin1.txt",❌);
SendMessage($from_id,"🗑 ادمین 1 با موفقیت حذف شد");
}
    if($text =="❌ حذف ادمین 2" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin-2";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"برای حذف ادمین 2 ای دی عددی آنرا وارد کنید ❌");
} 
	if($step =="admin-2" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin2.txt",❌);
SendMessage($from_id,"🗑 ادمین 2 با موفقیت حذف شد");
}
    if($text =="❌ حذف ادمین 3" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "admin-3";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"برای حذف ادمین 3 ای دی عددی آنرا وارد کنید ❌");
} 
	if($step =="admin-3" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
        $datas["step"] = "none";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("admin/admin3.txt",❌);
SendMessage($from_id,"🗑 ادمین 3 با موفقیت حذف شد");
} 
// finished Set up admin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••  
//  Set up Zarib ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="🍭 تنظیم ضریب ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"🍀 به بخش تنظیمات ضریب ها خوش آمدید . . . 
",$button_zarib);
}
if($text =="ضریب 🎯 دارت" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم ضریب های🎯 دارت خوش آمدید 🌷
",$button_dartset);
}
if($text =="ضریب 🎲 تاس" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم ضریب های 🎲 تاس خوش آمدید 🌷
",$button_taset);
}
if($text =="ضریب 🏀 بسکتبال" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "basketball";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="basketball" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/basketball.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب ⚽️ فوتبال" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "football";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="football" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/football.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎳 بولینگ" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bolingl";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="bolingl" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/boling.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب ✊ گل یا پوچ" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "gool";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="gool" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/gool.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🔢 مونتی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "mon";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="mon" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/mon.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 💩 پوپ" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "poop";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="poop" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/poop.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎱 رولت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "rolet";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="rolet" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/rolet.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 👊🏻 سنگ کاغذ قیچی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "Paper";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="Paper" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/Paper.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎲 تاس بالا و پایین" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "tasupd";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="tasupd" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/tasupd.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎲 تاس فرد و زوج" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "tasfard";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="tasfard" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/tasfard.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎲 حدس عدد تاس" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "tashad";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="tashad" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/tashad.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎯 دارت وسط" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dartV";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="dartV" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/dartV.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎯 دارت سفید" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dartSS";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="dartSS" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/dartSS.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎯 دارت قرمز" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dartGH";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="dartGH" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/dartGH.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎯 دارت بیرون" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "dartBI";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="dartBI" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/dartBI.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="ضریب 🎰 ماشین اسلات" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "skat";
        $outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
        file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌱 ضریب جدید را وارد کنید . . .");
} 
	if($step =="skat" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("zarib/skat.txt", $text);
SendMessage($from_id,"📊 ضریب با موفقیت به <code>$text</code> تغییر کرد ♻️");
}
    if($text =="🍂 ضریب دستی" and in_array($from_id,$admin) and $type == "private"){ 
$datas["step"] = "dastie";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"
🌹 ضریب مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="dastie" and is_numeric($text) and $text != "✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("play/zaribdast.txt","$text");
SendMessage($from_id,"
💤 ضریب مورد نظر با موفقیت ذخیره شد 
");
}
    // finished Set up Zarib •••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••• 

if($text =="انتقال مالکیت ربات 📤" and in_array($from_id,$admin) and $type == "private"){
if ($from_id == $owner) {
$datas["step"] = "owner1";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی عددی مالک جدید را وارد کنید 🎈 


📨 جهت انتقال مالکیت پنل مدیریت به کاربر دیگر نمیتوانید آن را عزل کنید و کسی هم نمی‌تواند او را عزل کند ❗️
و تمام ادمین های فعلی عزل میشوند 🎈
",@$button_back);
}else{
 SendMessage($from_id,"❌ شما مالک اصلی ربات نیستید و نمی توانید انتقال مالکیت ربات را به شخص دیگری بدهید ",$button);
}}
	elseif($step == "owner1" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
if(file_exists("data/$text/$text.json")){
file_put_contents("admin/owner.txt", $text);
SendMessage($from_id,"🎉 تبریک , کاربر <code>$text</code> مالک ربات تنظیم شد ❕",$button);
SendMessage($text,"✅ تبریک , شما مالک ربات شدید 🥳",$button_admin);
}else{
SendMessage($from_id," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست",$button);
}}
    
   // Set up coin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="⚙ تنظیمات سکه" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم ای دی های خوش آمدید 🎉
",$button_coin);
}
if($text =="♻️ ریست تنظیمات سکه" and in_array($from_id,$admin) and $type == "private"){
unlink("coin/meqdar.txt");
unlink("coin/bardasht.txt");
unlink("coin/bardashtX.txt");
unlink("coin/zir.txt");
unlink("coin/poof.txt");
unlink("coin/sharj.txt");
SendMessage($from_id,"
✅ با موفقیت تنظیمات سکه ریست شد
و تمام تنظیمات سکه به حالت پیشفرض در آمدند  
",$button_coin);
}
if($text =="💰 تنظیم حداقل سکه برای شرط" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "meqdar";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار حداقل سکه برای شرط را وارد کنید ❗️");
} 
	if($step == "meqdar" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/meqdar.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
    if($text =="💰 تنظیم حداقل سکه برای برداشت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bardasht";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار حداقل سکه برای برداشت موجودی را وارد کنید ❗️");
} 
	if($step == "bardasht" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/bardasht.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
    if($text =="💰 تنظیم حداکثر سکه برای برداشت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bardashtX";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار حداکثر سکه برای برداشت موجودی را وارد کنید ❗️");
} 
	if($step == "bardashtX" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/bardashtX.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
    if($text =="💰 تنظیم حداقل سکه جهت انتقال" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "poof";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار حداقل سکه جهت انتقال موجودی را وارد کنید ❗️");
} 
	if($step == "poof" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/poof.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
    if($text =="💰 تنظیم سکه زیر مجموعه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "zir";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار سکه زیر مجموعه گیری را وارد کنید ❗️");
} 
	if($step == "zir" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/zir.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
       if($text =="💰 تنظیم سکه حداقل شارژ" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "sharj";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"بصورت عدد انگلیسی مقدار حداقل سکه شارژ حساب را وارد کنید ❗️");
} 
	if($step == "sharj" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("coin/sharj.txt", $text);
SendMessage($from_id,"تعداد سکه با موفقیت به <code>$text</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
// finished Set up coin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
// Set up ID ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="🆔 تنظیم ای دی ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم ای دی های خوش آمدید 🎉
",$button_id);
}
if($text =="🥂 تنظیم ای دی پشتیبانی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "poshtiban";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی پشتیبانی را بدون @ ارسال کنید ❗️");
} 
	if($step =="poshtiban" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/poshtiban.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم ای دی کانال بازی ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "shart";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی کانال بازی های شرط بندی را با @ وارد کنید ❗️");
} 
	if($step =="shart" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/shart.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم ای دی کانال موشک" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "moshak";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی کانال بازی های موشک را با @ وارد کنید ❗️");
} 
	if($step =="moshak" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/moshak.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم ای دی کانال دارت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "DartChannel";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی کانال بازی های دارت را با @ وارد کنید ❗️");
} 
	if($step =="DartChannel" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/DartChannel.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم کانال اول" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "channel";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی کانال اول را بدون @ ارسال کنید ❗️");
} 
	if($step =="channel" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("channel.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم کانال دوم" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "channel2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"ای دی کانال دوم را بدون @ ارسال کنید ❗️");
} 
	if($step =="channel2" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("channel2.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم ای دی گفت و گو" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "gap";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اگه گپ شما خصوصی است لینک را بدون https://t.me ارسال کنید ❗️

در غیر این صورت ای دی گپ رو بدون @ ارسال کنید ❕");
} 
	if($step =="gap" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/gap.txt", $text);
SendMessage($from_id,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
    if($text =="🥂 تنظیم ای دی عددی برای شارژ حساب" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "addi";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"لطفاً ای دی عددی خود را بصورت کامل ارسال کنید ❗️");
} 
	if($step =="addi" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/addi.txt", $text);
SendMessage($from_id,"🌱 ای دی عددی برای واریز نیترو با موفقیت به
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$text
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
// finished Set up ID ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••

if($text =="🔑 چیدمان دکمه ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"
♻️ یک نوع از چیدمان های زیر را انتخاب کنید . . .
",$button_lol);
}
if($text =="❤️ چیدمان نوع اول ❤️" and in_array($from_id,$admin) and $type == "private"){
file_put_contents("menu.txt",$button1);
SendMessage($from_id,"
✅ با موفقیت $text تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($text =="🧡 چیدمان نوع دوم 🧡" and in_array($from_id,$admin) and $type == "private"){
file_put_contents("menu.txt",$button2);
SendMessage($from_id,"
✅ با موفقیت $text تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($text =="💚 چیدمان نوع سوم 💚" and in_array($from_id,$admin) and $type == "private"){
file_put_contents("menu.txt",$button3);
SendMessage($from_id,"
✅ با موفقیت $text تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($text =="💛 چیدمان نوع چهارم 💛" and in_array($from_id,$admin) and $type == "private"){
file_put_contents("menu.txt",$button4);
SendMessage($from_id,"
✅ با موفقیت $text تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($text =="💜 چیدمان نوع پنجم 💜" and in_array($from_id,$admin) and $type == "private"){
file_put_contents("menu.txt",$button5);
SendMessage($from_id,"
✅ با موفقیت $text تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
// Set texts ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="تنظیم متن ها 🎈" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"به بخش تنظیم متن ها خوش آمدید 🎉
",$button_matn);
}
if($text =="$dok9" and $type == "private"){
SendMessage($from_id,"$bonos
");
}
if($text =="تنظیم متن بونوس" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bonos";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="bonos" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bonos.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($text =="$dok12" and $type == "private"){
SendMessage($from_id,"$Conversation

",json_encode(['inline_keyboard' => [[['text' => "گفت و گو 📩",'url' => "https://t.me/$gap"]],]]));exit();
}
if($text =="تنظیم متن گفت و گو" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "Conversation";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="Conversation" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/Conversation.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
    if($text =="$dok7" and $type == "private"){
SendMessage($from_id,"$Law

@$botid");
}
if($text =="تنظیم متن قوانین" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "Law";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="Law" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/Law.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($text =="$dok6" and $type == "private"){
SendMessage($from_id,"$saham

📩 پشتیبانی سوالات شما : @$poshtiban


",$button_f2);
}
if($text =="تنظیم متن سهام" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "saham";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="saham" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/saham.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
    if($text =="$dok5" and $type == "private"){
SendMessage($from_id,"$help

@$botid");
}
if($text =="تنظیم متن راهنما" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "help";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="help" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/help.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($text =="$dok8" and $type == "private"){
SendMessage($from_id,"
$accReceiver
",json_encode(['inline_keyboard' => [[['text' => "$dok8",'url' => "https://telegram.me/$poshtiban"]],]]));exit();
}
if($text =="تنظیم متن ارسال اکانت" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "accReceiver";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="accReceiver" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/accReceiver.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($text =="تنظیم برترین ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"یکی از گزینه های زیر را انتخاب کنید 👇
",$button_bartar);
}
if($text =="تنظیم نفر اول برترین ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bartar1";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="bartar1" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bartar1.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
    if($text =="تنظیم نفر دوم برترین ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bartar2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="bartar2" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bartar2.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
    if($text =="تنظیم نفر سوم برترین ها" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bartar3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="bartar3" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bartar3.txt", $text);
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
    if($text =="$dok14" and $type == "private"){
SendMessage($from_id,"

🎁 نفر اول | $bartar1

🎁نفر دوم | $bartar2

🎁 نفر سوم | $bartar3

");
}
// Set  subset ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••

if($text =="👤 تنظیمات زیر مجموعه" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"
سلام به بخش تنظیمات زیر مجموعه خوش آمدید 🎉 
",$button_zir);
}

if($text =="🌄 تنظیم عکس زیر مجموعه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "link";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🌄 لینک عکس زیر مجموعه را ارسال کنید");
} 
	if($step =="link" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("subset/link.txt","$text");
SendMessage($from_id,"✅ لینک عکس بنر زیر مجموعه با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($text =="📝 تنظیم متن اصلی بنر زیر مجموعه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "mtn";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="mtn" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("subset/mtn.txt","$text");
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($text =="🔖 تنظیم متن زیر بنر اصلی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "mtnzir";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"متن جدید را بفرستید");
} 
	if($step =="mtnzir" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("subset/mtnzir.txt","$text");
SendMessage($from_id,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($text =="🎉 تنظیم اسم دکمه زیر مجموعه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "doksubset";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"اسم جدید دکمه را ارسال کنید");
} 
	if($step =="doksubset" and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("subset/doksubset.txt","$text");
SendMessage($from_id,"✅ اسم دکمه زیر مجموعه با موفقیت به
╔═══════════════════════════════╗
 $text
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($text =="🎁 تنظیمات بونوس" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"
به بخش تنظیمات بونوس خوش آمدید 🎉
",$button_bons);
}
if($text =="🎁 تنظیم حداقل بونوس هدیه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bonos1";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد حداقل سکه رندوم بونوس هدیه را وارد کنید ❗️");
} 
	if($step =="bonos1" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bonos1.txt", $text);
SendMessage($from_id,"🎉 حداقل سکه رندوم بونوس هدیه به $text سکه تغییر کرد");
}
    if($text =="🎁 تنظیم حداکثر بونوس هدیه" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bonos2";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد حداکثر سکه رندوم بونوس هدیه را وارد کنید ❗️");
} 
	if($step =="bonos2" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bonos2.txt", $text);
SendMessage($from_id,"🎉 حداکثر سکه رندوم بونوس هدیه به $text سکه تغییر کرد");
}
    if($text =="🎁 تنظیم بونوس خوش آمد گویی" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bonos3";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"🎁 تعداد سکه بونوس خوش آمد گویی را وارد کنید بصورت عدد انگلیسی . . .");
} 
	if($step =="bonos3" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bonos3.txt", $text);
SendMessage($from_id,"🎉 بونوس خوش آمد گویی به $text سکه تغییر کرد");
}
    if($text =="🎁 تنظیم بونوس شارژ بیشتر" and in_array($from_id,$admin) and $type == "private"){
        $datas["step"] = "bonos4";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
SendMessage($from_id,"تعداد سکه بونوس شارژ بیشتر را وارد کنید ❗️");
} 
	if($step =="bonos4" and is_numeric($text) and $text !="✨بازگشت✨" and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("text/bonos4.txt", $text);
SendMessage($from_id,"🎉 بونوس شارژ بیشتر به $text سکه تغییر کرد");
}
// Set  off and on ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($text =="💡 خاموش و روشن بازی ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"✅ روشن کردن بازی ها"],['text'=>"💤 خاموش کردن بازی ها"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
if($text =="✅ روشن کردن بازی ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"✅ به بخش تنظیمات روشن کردن بازی ها خوش آمدید . . .
",$button_on);
}
if($text =="💤 خاموش کردن بازی ها" and in_array($from_id,$admin) and $type == "private"){
SendMessage($from_id,"💤 به بخش تنظیمات خاموش کردن بازی ها خوش آمدید . . .
",$button_off);
}
if($text =="روشن کردن بازی 🃏 21" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/card21.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🏀 بسکتبال" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/basket.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🔢 مونتی" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/moont.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 💣 انفجار" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/enfejar.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی ✊ گل یا پوچ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/gooli.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی ⚽️ فوتبال" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/footi.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🎳 بولینگ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/boli.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 👊🏻 سنگ کاغذ قیچی" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/sangi.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 💩 پوپ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/popi.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🎱 رولت" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/rolet.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🎰 ماشین اسلات" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/skati.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🎲 تاس" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/tasi.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="روشن کردن بازی 🎯 دارت" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
unlink("onof/darti.txt");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($text =="خاموش کردن بازی 🃏 21" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/card21.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🏀 بسکتبال" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/basket.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🔢 مونتی" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/moont.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 💣 انفجار" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/enfejar.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی ✊ گل یا پوچ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/gooli.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی ⚽️ فوتبال" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/footi.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🎳 بولینگ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/boli.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 👊🏻 سنگ کاغذ قیچی" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/sangi.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 💩 پوپ" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/popi.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🎱 رولت" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/rolet.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🎰 ماشین اسلات" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/skati.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🎲 تاس" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/tasi.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="خاموش کردن بازی 🎯 دارت" and in_array($from_id,$admin) and $type == "private"){
$datas["step"] = "none";
$outjson = json_encode($datas,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
file_put_contents("onof/darti.txt","off");
SendMessage($from_id,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($text =="/creator" and $type == "private"){
SendMessage($from_id,"🌱 تیم برنامه نویسی و پشتیبانی ربات اِسلُو بت : 

🥀 $creator");
}

if(is_file("error_log"))
unlink("error_log");
?>