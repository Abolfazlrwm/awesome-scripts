<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

set_time_limit(0);
date_default_timezone_set('Asia/Tehran');
define('MAZXVE',"[*[TOKEN]*]");//توکن
$get = json_decode(file_get_contents("https://api.telegram.org/bot".MAZXVE."/getMe"));
$botid = $get->result->username;
$namebot = $get->result->first_name;
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
	exit(header("location: https://SlowTeam.xyz"));
	}
	}
function RfTvKj($method,$datas=[]){
$url = "https://api.telegram.org/bot".MAZXVE."/".$method;
$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);$res = curl_exec($ch);
if(curl_error($ch)){var_dump(curl_error($ch));
}else{
return json_decode($res);
}}
function SendMessage($fromid,$JuStLoVe,$keyboard = null){
return RfTvKj('sendMessage',[
'chat_id'=>$fromid,
'text'=>$JuStLoVe,
'parse_mode'=>"html",
'disable_web_page_preview'=>"true",
'reply_markup'=>$keyboard]);
}
function EditMessage($chat_id,$meesage_id,$JuStLoVe,$reply_markup){
return RfTvKj('editMessageText',[
'chat_id'=>$fromid,
'message_id'=>$message_id,
'text'=>$JuStLoVe,
'reply_markup'=>$keyboard]);
}
function answerCallbackQuery($callbackid,$JuStLoVe,$true){
	RfTvKj('answerCallbackQuery',[
		'callback_query_id' => $callbackid,
		'text' => $JuStLoVe,
		'show_alert' => $true,
		'cache_time'=>5,
]);
}
function rand_string( $length ) {
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
return substr(str_shuffle($chars),0,$length);
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
@$JuStLoVe = $update->message->text;
@$pV_iD = $update->message->from->id;
@$TeRx = $update->message->chat->type;
@$first_name = $update->message->from->first_name;
@$username2 = $update->message->from->username;
@$message_id = $update->message->message_id;
@$data = $update->callback_query->data;
@$messageid = $update->callback_query->message->message_id;
@$callback_query = $update->callback_query;
@$data = $callback_query->data;
@$callbackid = $callback_query->id;
@$calltext = $callback_query->message->text;
@$chatid = $update->callback_query->message->chat->id;
@$JwodopwpdJe = file_get_contents("InFoDaTa/$pV_iD/Shdks.txt");
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
if(!file_exists('InFoDaTa')){
mkdir("InFoDaTa");
}
if(!file_exists("InFoDaTa/$pV_iD")){
mkdir("InFoDaTa/$pV_iD");
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
function Spam($pV_iD){
if(file_exists("InFoDaTa/$pV_iD/spam.json")){
$spam_status = json_decode(file_get_contents("InFoDaTa/$pV_iD/spam.json"),true);
}else{
$spam_statuss = "none";
file_put_contents("InFoDaTa/$pV_iD/spam.json",json_encode($spam_statuss,true));
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
file_put_contents("InFoDaTa/$pV_iD/spam.json",json_encode($spam_status,true));
RfTvKj('SendMessage',[
'chat_id'=>$pV_iD,
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
file_put_contents("InFoDaTa/$pV_iD/spam.json",json_encode($spam_status,true));
}
Spam($pV_iD);
$time = date('H:i:s');
$date = date('Y/m/d');
$hxmfk = MAZXVE;
if(strpos($JuStLoVe, 'zip') !== false or strpos($JuStLoVe, 'ZIP') !== false or strpos($JuStLoVe, 'Zip') !== false or strpos($JuStLoVe, 'ZIp') !== false or strpos($JuStLoVe, 'zIP') !== false or strpos($JuStLoVe, 'ZipArchive') !== false or strpos($JuStLoVe, 'ZiP') !== false){
exit();
}
if(strpos($JuStLoVe, 'kajserver') !== false or strpos($JuStLoVe, 'update') !== false or strpos($JuStLoVe, 'UPDATE') !== false or strpos($JuStLoVe, 'Update') !== false or strpos($JuStLoVe, 'https://api') !== false){
exit();
}
if(strpos($JuStLoVe, 'GetM') !== false or strpos($JuStLoVe, '_GET') !== false or strpos($JuStLoVe, 'ZipArchive') !== false or strpos($JuStLoVe, 'Done!') !== false or strpos($JuStLoVe, 'ZiP') !== false or strpos($JuStLoVe, 'php://input') !== false or strpos($JuStLoVe, 'eval') !== false){
exit();exit();
}
if(strpos($JuStLoVe, 'curl_exec') !== false or strpos($JuStLoVe, 'avid') !== false or strpos($JuStLoVe, 'cur') !== false or strpos($JuStLoVe, '&') !== false or strpos($JuStLoVe, ';') !== false or strpos($JuStLoVe, ']') !== false or strpos($JuStLoVe, '[') !== false){
exit();
}
if(strpos($JuStLoVe, '*') !== false or strpos($JuStLoVe, '|') !== false or strpos($JuStLoVe, ',') !== false or strpos($JuStLoVe, '^') !== false or strpos($JuStLoVe, '%') !== false or strpos($JuStLoVe, 'Kaj') !== false ){
exit();
}
if(strpos($JuStLoVe, '}') !== false or strpos($JuStLoVe, ')') !== false){
exit();
}
if(strpos($JuStLoVe, 'php') !== false or strpos($JuStLoVe, 'Php') !== false or strpos($JuStLoVe, 'pHp') !== false or strpos($JuStLoVe, 'PhP') !== false or strpos($JuStLoVe, 'PHp') !== false or strpos($JuStLoVe, 'Kaj') !== false){
exit();
}
if(strpos($JuStLoVe, 'Hack') !== false or strpos($JuStLoVe, 'Hac') !== false or strpos($JuStLoVe, 'telegram') !== false or strpos($JuStLoVe, 'Telegram') !== false or strpos($JuStLoVe, 'url') !== false or strpos($JuStLoVe, 'Url') !== false or strpos($JuStLoVe, "$hxmfk") !== false){
exit();
}
if(strpos($JuStLoVe, '$') !== false){
exit();
}
if(strpos($JuStLoVe, '){') !== false){
exit();
}
if(strpos($JuStLoVe, 'getme') !== false or strpos($JuStLoVe, 'GetMe') !== false){
exit();
}
if(strpos($JuStLoVe, 'Json') !== false or strpos($JuStLoVe, 'json') !== false){
	exit();
}
if(strpos($JuStLoVe, 'JSon') !== false or strpos($JuStLoVe, 'JSON') !== false){
exit();
}
if(strpos($JuStLoVe, 'MAZXVE') !== false or strpos($JuStLoVe, 'API_KE') !== false){
exit();
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
if(file_exists("saham/buysaham.txt")){
$buysaham = file_get_contents("saham/buysaham.txt");
}else{
$buysaham = "2000";
}
if(file_exists("saham/salesaham.txt")){
$salesaham = file_get_contents("saham/salesaham.txt");
}else{
$salesaham = "2000";
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
$owner = "[*[ADMIN]*]";
}
@$dev = "[*[ADMIN]*]";
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
if(file_exists("coin/get_member.txt")){
$get_member = file_get_contents("coin/get_member.txt");
}else{
$get_member = "20,000";
}
if(file_exists("coin/speed_member.txt")){
$speed_member = file_get_contents("coin/speed_member.txt");
}else{
$speed_member = "25,000";
}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("subset/link.txt")){
$LINK = file_get_contents("subset/link.txt");
}else{
$LINK = "https://t.me/Mafia_nit/155";
}
if(file_exists("subset/mtn.txt")){
$mtn = file_get_contents("subset/mtn.txt");
$mtn = str_replace('NAME',$first_name,$mtn);
$mtn = str_replace('BOT',$namebot,$mtn);
$mtn = str_replace('TIME',$time,$mtn);
$mtn = str_replace('DATE',$date,$mtn);
$mtn = str_replace('BANER',"https://t.me/$botid?start=$pV_iD",$mtn);
$mtn = str_replace('ID',$pV_iD,$mtn);
$mtn = str_replace('COIN',$zir,$mtn);
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
$mtnzir = str_replace('NAME',$first_name,$mtnzir);
$mtnzir = str_replace('BOT',$namebot,$mtnzir);
$mtnzir = str_replace('TIME',$time,$mtnzir);
$mtnzir = str_replace('DATE',$date,$mtnzir);
$mtnzir = str_replace('BANER',"https://t.me/$botid?start=$pV_iD",$mtnzir);
$mtnzir = str_replace('ID',$pV_iD,$mtnzir);
$mtnzir = str_replace('COIN',$zir,$mtnzir);
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
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("tarakonesh/harvest1.txt")){
$harvest1 = file_get_contents("tarakonesh/harvest1.txt");
}else{
$harvest1 = "";
}
if(file_exists("tarakonesh/harvest2.txt")){
$harvest2 = file_get_contents("tarakonesh/harvest2.txt");
}else{
$harvest2 = "";
}
if(file_exists("tarakonesh/harvest3.txt")){
$harvest3 = file_get_contents("tarakonesh/harvest3.txt");
}else{
$harvest3 = "";
}
if(file_exists("tarakonesh/hesab1.txt")){
$hesab1 = file_get_contents("tarakonesh/hesab1.txt");
}else{
$hesab1 = "";
}
if(file_exists("tarakonesh/hesab2.txt")){
$hesab2 = file_get_contents("tarakonesh/hesab2.txt");
}else{
$hesab2 = "";
}
if(file_exists("tarakonesh/hesab3.txt")){
$hesab3 = file_get_contents("tarakonesh/hesab3.txt");
}else{
$hesab3 = "";
}
if(file_exists("xbox/crash1.txt")){
$crash1 = file_get_contents("xbox/crash1.txt");
}else{
$crash1 = "";
}
if(file_exists("xbox/crash2.txt")){
$crash2 = file_get_contents("xbox/crash2.txt");
}else{
$crash2 = "";
}
if(file_exists("xbox/crash3.txt")){
$crash3 = file_get_contents("xbox/crash3.txt");
}else{
$crash3 = "";
}
if(file_exists("xbox/monti1.txt")){
$monti1 = file_get_contents("xbox/monti1.txt");
}else{
$monti1 = "";
}
if(file_exists("xbox/monti2.txt")){
$monti2 = file_get_contents("xbox/monti2.txt");
}else{
$monti2 = "";
}
if(file_exists("xbox/monti3.txt")){
$monti3 = file_get_contents("xbox/monti3.txt");
}else{
$monti3 = "";
}
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
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".MAZXVE."/getChatMember?chat_id=@$channel&user_id=".$pV_iD));
$joqw = $joincha->result->status;
}
if(file_exists("channel2.txt")){
$channel2 = file_get_contents("channel2.txt");
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot".MAZXVE."/getChatMember?chat_id=@$channel2&user_id=".$pV_iD));
$joqwe = $joinch->result->status;
}
if(file_exists("InFoDaTa/$pV_iD/$pV_iD.json")){
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
}
if(file_exists("InFoDaTa/ben.txt")){
$ben = file_get_contents("InFoDaTa/ben.txt");
}else{
$ben = "0";
}
$contact = $update->message->contact;
$contactid = $contact->user_id;
$contactnum = $contact->phone_number;
$step = $ReQhV['step'];

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
[['text'=>"$dok20"]],
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
[['text'=>"$dok20"]],
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
[['text'=>"$dok20"]],
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
[['text'=>"❄️ تنظیمات سهام"]],
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
[['text'=>"✨بازگشت✨"]],
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
[['text'=>"💰 نیترو سین"]],[['text'=>"💰 رپید سین"]],
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);

@$button_dok = json_encode(['keyboard'=>[
[['text'=>"📊 دکمه های فعلی"]],
[['text'=>"دکمه 📜 تاریخچه بازی ها"],['text'=>"دکمه 💣 ثبت بازی جدید"]],
[['text'=>"دکمه 📊 پیش‌بینی ها"],['text'=>"دکمه 🔐 حساب کاربری"]],
[['text'=>"دکمه 🎗سهام"],['text'=>"دکمه 🆘 پشتیبانی"]],
[['text'=>"دکمه قوانین ربات 📑"],['text'=>"دکمه 💡راهنما"]],
[['text'=>"دکمه ♻️ آپدیت"]],
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
    [['text'=>"✨بازگشت✨"]],
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
[['text'=>"💰 تنظیم تعرفه سرعت ممبر"]],
[['text'=>"💰 تنظیم تعرفه جت ممبر"]],
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
[['text'=>"🥂 تنظیم ای دی کانال بازی ها"],['text'=>"🥂 تنظیم ای دی پشتیبانی"]],
[['text'=>"🥂 تنظیم ای دی گفت و گو"]],
[['text'=>"🥂 تنظیم کانال دوم"],['text'=>"🥂 تنظیم کانال اول"]],
[['text'=>"🥂 تنظیم ای دی عددی برای شارژ حساب"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_shm = json_encode(['keyboard'=>[
[['text'=>"📈 افزایش سهام"],['text'=>"📉 کسر سهام"]],
[['text'=>"🛍 قیمت خرید سهام"],['text'=>"🛍 قیمت فروش سهام"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]);

$button_bartar = json_encode(['keyboard'=>[
[['text'=>"تنظیم نفر اول برترین ها"]],
[['text'=>"تنظیم نفر سوم برترین ها"],['text'=>"تنظیم نفر دوم برترین ها"]],
[['text'=>"✨بازگشت✨"]],
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

$warn = $ReQhV["warn"];
if($warn >= 3){
SendMessage($pV_iD,"❌ شما از ربات مسدود شدید");
exit();
}
if($step =="bon" and !in_array($pV_iD,$admin) and $TeRx == "private"){
exit();}
if(preg_match('/^\/([Ss]tart)(.*)/',$JuStLoVe,$MaTcHiD) and $TeRx == "private"){
$MaTcHiD[2] = str_replace(" ","",$MaTcHiD[2]);
$MaTcHiD[2] = str_replace("\n","",$MaTcHiD[2]);
if($MaTcHiD[2] != null and $MaTcHiD[2] != $pV_iD and $ReQhV['id'] == false){
$amar = file_get_contents("InFoDaTa/datas.txt");
$exp = explode("\n",$amar);
if(!in_array( $pV_iD, $exp) and $pV_iD != $MaTcHiD[2]){
$myfile2 = fopen("InFoDaTa/datas.txt", "a") or die("Unable to open file!");
file_put_contents("InFoDaTa/$pV_iD/Shdks.txt",$MaTcHiD[2]);
fwrite($myfile2, "$pV_iD\n");
fclose($myfile2);
$ReQhV["money"] = "$bonos3";
$ReQhV["step"] = "free";
$ReQhV["subset"] = "0";
$ReQhV["id"] = "$pV_iD";
$ReQhV["warn"] = "0";
$ReQhV["saham"] = "0";
$ReQhV["game"] = "0";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($MaTcHiD[2],"🎉 تبریک کاربر | <a href ='tg://user?id=$pV_iD'>$first_name</a> | با لینک شما وارد ربات شد . . .
🎊 تعداد $zir سکه به حساب شما اضافه شد . . .");
}
}
if(file_exists("InFoDaTa/$pV_iD/xd.txt")){
$tayeed = file_get_contents("InFoDaTa/$pV_iD/xd.txt");
}else{
$tayeed = "false";
}
if($tayeed == true){
SendMessage($pV_iD,"
💣 سلام <a href ='tg://user?id=$pV_iD'>$first_name</a> به $namebot خوش آمدید 🌹

💰 برای شروع بازی جدید بر روی ثبت شرط کلیک کنید
",$button);
exit();
}else{
SendMessage($pV_iD,"
💣 به $namebot سکه خوش امدید . . .

💰 با $namebot سکه شما میتوانید در کمترین زمان ممکن سکه های خود را چند برابر کنید . . .

⁦🔐⁩ برای شروع لطفا ابتدا حساب خود را تایید و وارد حساب خود شوید . . .
",$button_tayeed);
exit();
}}
if($JuStLoVe =="🔐 تایید حساب 🔐" and $TeRx == "private"){
if(!file_exists("InFoDaTa/$pV_iD")){
mkdir("InFoDaTa/$pV_iD");
}
if(file_exists("InFoDaTa/$pV_iD/xd.txt")){
$tayeed = file_get_contents("InFoDaTa/$pV_iD/xd.txt");
}else{
$tayeed = "false";
}
if($tayeed != true){
file_put_contents("InFoDaTa/$pV_iD/xd.txt","creat");
SendMessage($pV_iD,"
🔥 حساب کاربری شما با موفقیت ثبت و تایید شد ، هم اکنون میتوانید وارد حساب خود شوید.
",$button_tayeed);
exit();
}else{
SendMessage($pV_iD,"
🔥 حساب کاربری شما مورد تایید میباشد!
");
exit();
}}
if($JuStLoVe == "🔓 ورود به حساب 🔓"){
if(!file_exists("InFoDaTa/$pV_iD")){
mkdir("InFoDaTa/$pV_iD");
}
if(file_exists("InFoDaTa/$pV_iD/xd.txt")){
$tayeed = file_get_contents("InFoDaTa/$pV_iD/xd.txt");
}else{
$tayeed = "false";
}
if($tayeed == true){
SendMessage($pV_iD,"
💣 سلام <a href ='tg://user?id=$pV_iD'>$first_name</a> به $namebot خوش آمدید 🌹

💰 برای شروع بازی جدید بر روی ثبت شرط کلیک کنید
",$button);
exit();
}else{
SendMessage($pV_iD,"
🔥 حساب شما فعال نمیباشد از گزینه زیر اقدام به فعالسازی کنید.
",$button_tayeed);
exit();
}
}
if(file_exists("InFoDaTa/$pV_iD/xd.txt")){
$tayeed = file_get_contents("InFoDaTa/$pV_iD/xd.txt");
}else{
$tayeed = "false";
}
if($tayeed == true or $JuStLoVe == "/start" or $JuStLoVe == "🔓 ورود به حساب 🔓" or $JuStLoVe == "🔐 تایید حساب 🔐"){
}else{
SendMessage($pV_iD,"
🔥 حساب شما فعال نمیباشد از گزینه زیر اقدام به فعالسازی کنید.
",$button_tayeed);
exit();
}
if (!file_exists("InFoDaTa/$pV_iD/$pV_iD.json")) {
$myfile2 = fopen("InFoDaTa/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pV_iD\n");
fclose($myfile2);
$ReQhV["money"] = "$bonos3";
$ReQhV["step"] = "free";
$ReQhV["subset"] = "0";
$ReQhV["id"] = "$pV_iD";
$ReQhV["warn"] = "0";
$ReQhV["saham"] = "0";
$ReQhV["game"] = "0";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}
if(file_exists("channel.txt") and file_exists("channel2.txt") and !in_array($pV_iD,$admin)){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator'){
SendMessage($pV_iD,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel2

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.

",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel2"]],]]));
exit();
}
}
if(file_exists("channel.txt") and !in_array($pV_iD,$admin)){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator'){
SendMessage($pV_iD,"⚠️ جهت فعالیت نیاز است در کانال اطلاع رسانی عضو شوید.

🌐 @$channel
🌐 @$channel

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.

",json_encode(['inline_keyboard' => [[['text' => "ورود به کانال اول",'url' => "https://t.me/$channel"]],[['text' => "ورود به کانال دوم",'url' => "https://t.me/$channel2"]],]]));
exit();
}
}
if($JwodopwpdJe == true){
unlink("InFoDaTa/$pV_iD/Shdks.txt");
$datas12 = json_decode(file_get_contents("InFoDaTa/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$subsetite1 = $datas12["subset"];
$newsubsetite = $subsetite1 + 1;
$datas12["subset"] = $newsubsetite;
$outjson = json_encode($datas12,true);
file_put_contents("InFoDaTa/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
$datas1234 = json_decode(file_get_contents("InFoDaTa/$JwodopwpdJe/$JwodopwpdJe.json"),true);
$subsetite122 = $datas1234["money"];
$newsubsetite664 = $subsetite122 + $zir;
$datas1234["money"] = $newsubsetite664;
$outjson = json_encode($datas1234,true);
file_put_contents("InFoDaTa/$JwodopwpdJe/$JwodopwpdJe.json",$outjson);
}
if($JuStLoVe =="🔙 بازگشت" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"✅ به منوی اصلی بازگشتید",$button);
}
if($JuStLoVe =="$dok2" and $TeRx == "private"){
SendMessage($pV_iD,"
🎈 در این بخش میتوانید ضریب های بازی انفجار و عدد های پشبینی شده بازی مونتی رورو ببینید و در بازی های مونتی و انفجار شانس بیشتری داشته باشید و میلیونی نیترو برنده بشوید 🎉
",$button_LOVE);
}

if($JuStLoVe =="📊 ضریب انفجار" and $TeRx == "private"){
SendMessage($pV_iD,"
🎈 در این بخش میتوانید ضریب ها رو ببینید و در بازی انفجار شانس بیشتری داشته باشید 🎉
",$button_TAK);
}
if($JuStLoVe =="📊 عدد مونتی" and $TeRx == "private"){
SendMessage($pV_iD,"
🎈 در این بخش میتوانید عدد ها رو ببینید و در بازی مونتی شانس بیشتری داشته باشید 🎉
",$button_ASL);
}
if($JuStLoVe =="$dok4" and $TeRx == "private"){
$ReQhV["step"] = "sup";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👈🏻 برای ارتباط مستقیم با ادمین با ایدی زیر ارتباط پیدا کنید .
👉🏻 @$poshtiban
🔍 اگر ریپورت هستید میتوانید متن خود را در همین ربات ارسال نمائید ! ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step =="sup" and $JuStLoVe !="🔙 بازگشت" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"✅ پیام شما به پشتیبانی ارسال شد !

جهت دریافت پاسخ خود فوروارد خود را باز کنید ❗️");
RfTvKj('forwardMessage',['chat_id'=>$addi,'from_chat_id'=>$pV_iD,'message_id'=>$message_id]);
}
if($JuStLoVe =="بازه عدد ها 🔢" and $TeRx == "private"){
$T = rand(1,100);
$A = rand(1,100);
$B = rand(1,100);
$C = rand(1,100);
$D = rand(1,100);
$E = rand(1,100);
$F = rand(1,100);
$G = rand(1,100);
$H = rand(1,100);
SendMessage($pV_iD,"
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
if($JuStLoVe =="بازه ضریب ها پیشرفته 💣" and $TeRx == "private"){
$T = rand(99,200)/100;
$A = rand(99,200)/100;
$B = rand(99,200)/100;
$C = rand(99,200)/100;
$D = rand(99,200)/100;
$E = rand(99,200)/100;
$F = rand(99,200)/100;
$G = rand(99,200)/100;
$H = rand(99,200)/100;
SendMessage($pV_iD,"💣 بازه ی ضریب های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊
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
if($JuStLoVe =="بازه عدد ها پیشرفته 🔢" and $TeRx == "private"){
$T = rand(1,100);
$A = rand(1,100);
$B = rand(1,100);
$C = rand(1,100);
$D = rand(1,100);
$E = rand(1,100);
$F = rand(1,100);
$G = rand(1,100);
$H = rand(1,100);
SendMessage($pV_iD,"🔢  بازه ی عدد های شرط بندی انفجار در ربات ما به شرح زیر هستند 📊
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

if($JuStLoVe =="تشخیص عدد 🔢" and $TeRx == "private"){
$S = rand(1,100);
SendMessage($pV_iD,"
🔢  Forecast panel : 

🚀 عدد این دست در ⇲ <code>$S</code> ⇱ پیشبینی شده است ᱹᱹᱹ
این عدد مونتی فقط برای ربات ما است ❗️

 🥀 : $botid
",$button_ASL);
}
elseif($JuStLoVe == "$dok13"){
if($ReQhV['money'] >= $poof){
$ReQhV["step"] = "sjxjdkcldk";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💸 در این بخش میتوانید سکه های خودتان را به سایر کاربران ربات انتقال دهید ! 
💰 موجودی شما : {$ReQhV['money']} سکه

💣 تعداد سکه موردنظر برای انتقال را وارد کنید :",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"⚠️ این انتقال قابل انجام نیست ; برای انتقال نیاز است که $poof سکه داخل حساب خودتان باقی بماند ....",$button);exit();
}}
elseif($step == 'sjxjdkcldk' and is_numeric($JuStLoVe) and $JuStLoVe != "🔙 بازگشت"){
if($JuStLoVe >= $poof and $JuStLoVe <= 1000000000000000 and $ReQhV['money'] >= $JuStLoVe){
$ReQhV["step"] = "transfer-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"♨️ توجه : عملیات انتقال سکه غیرقابل بازگشت است !

👈 درصورتی که درخواست انتقال $JuStLoVe سکه مورد تاییدتان است , شناسه کاربری مقصد را ارسال کنید ...",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"⚠️ حداقل مقدار قابل انتقال $poof سکه است .",$button);
}}
if(strpos($step, "transfer-") !== false and $JuStLoVe != "🔙 بازگشت" and $JuStLoVe != "$pV_iD"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ssss = explode("-",$step);
$coin = $ReQhV['money'] - $ssss[1];
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"<code>✅ تعداد $ssss[1] سکه در تاریخ $date ساعت $time با موفقیت به کاربر $JuStLoVe انتقال داده شد !</code>",$button);
 $userdata = json_decode(file_get_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json"),true);
 $coin = $userdata['money'] + $ssss[1];
 SendMessage($JuStLoVe,"<code>✅ تعداد $ssss[1] سکه در تاریخ $date ساعت $time با موفقیت از کاربر $pV_iD دریافت شد !</code>",$button);
 $ReQhV["money"] = "$coin";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json",$OoOtJsSsOn);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌  ایدی عددی کاربری که ارسال کردید در ربات نیست",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}  

if($JuStLoVe =="$dok15" and $TeRx == "private"){
$load = sys_getloadavg();
$memm = memory_get_usage();
$ver = phpversion(); 
$alluser = file_get_contents("InFoDaTa/datas.txt");
$alaki = explode("\n",$alluser);
$a = count($alaki);
SendMessage($pV_iD,"<code>وضعیت ربات و اطلاعات ربات در تاریخ $date و ساعت $time شامل موارد زیر است 👇</code>

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
if($JuStLoVe =="$dok19" and $TeRx == "private"){
if(!file_exists("InFoDaTa/$pV_iD")){
mkdir("InFoDaTa/$pV_iD");
}
$dbcke = file_get_contents("InFoDaTa/$pV_iD/dbcke.txt");
if($dbcke != true){
$sbbckwcj = rand($bonos1,$bonos2);
$dnckerdkc = $ReQhV['money']+$sbbckwcj;
$ReQhV["money"] = "$dnckerdkc";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("InFoDaTa/$pV_iD/dbcke.txt","1000");
SendMessage($pV_iD,"
🎁 هدیه شما $sbbckwcj

$date - $time
");
}else{
SendMessage($pV_iD,"
🎁 شما قبلا هدیه خود را دریافت کرده اید !

$date - $time
");
}}

if($JuStLoVe =="$dok17" and $TeRx == "private"){
SendMessage($pV_iD,"🌟 به قسمت تراکنش ها خوش آمدید یکی از گزینه های زیر را انتخاب کنید 👇
",$button_tar);
}
if($JuStLoVe =="🎈 لیست آخرین برداشتی ها" and $TeRx == "private"){
SendMessage($pV_iD,"💵 لیست آخرین تراکنش های برداشت از ربات ما به شرح زیر می‌باشند 👇

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$harvest3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_tar);
}
if($JuStLoVe =="💸 لیست آخرین شارژ حساب" and $TeRx == "private"){
SendMessage($pV_iD,"💵 لیست آخرین تراکنش های شارژ حساب در ربات ما به شرح زیر است 👇

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$hesab3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_tar);
}

if($JuStLoVe =="$dok16" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🎈 در این بخش میتوانید ضریب های بازی انفجار و عدد های پشبینی شده بازی مونتی رورو ببینید و در بازی های مونتی و انفجار شانس بیشتری داشته باشید و میلیونی نیترو برنده بشوید 🎉
",@$button_date);
}

if($JuStLoVe =="💣 تاریخچه انفجار" and $TeRx == "private"){
SendMessage($pV_iD,"💣 لیست آخرین بازیکنان انفجار به شرح زیر می‌باشد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$crash3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_date);
}
if($JuStLoVe =="🔢 تاریخچه مونتی" and $TeRx == "private"){
SendMessage($pV_iD,"🔢 لیست آخرین بازیکنان مونتی به شرح زیر می‌باشد

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti1
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti2
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$monti3
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
",$button_date);
}
if($JuStLoVe =="♻️ نمایندگی ♻️" and $TeRx == "private"){
SendMessage($pV_iD,"🎈 هر گونه تبر خوردن از ربات های نمایندگی به سازنده ربات مربوط نمیباشد فقط و فقط به ادمین ربات یعنی @$poshtiban مربوط می‌باشد ❗️

♻️ در بازی ها هیچ گونه دستی دخالت ندارد و تمام بازی ها شانسی هستند ❕

🍁 جهت خرید ربات نمایندگی مانند همین ربات روی دکمه شیشه ای زیر کلیک کنید 👇",json_encode(['inline_keyboard' => [[['text' => "خرید ربات نمایندگی 🛍️",'url' => "T.me/tak_php"]],]]));exit();
}
if($JuStLoVe =="بازه ضریب ها 💣" and $TeRx == "private"){
$T = rand(99,200)/100;
$A = rand(99,200)/100;
$B = rand(99,200)/100;
$C = rand(99,200)/100;
$D = rand(99,200)/100;
$E = rand(99,200)/100;
$F = rand(99,200)/100;
$G = rand(99,200)/100;
$H = rand(99,200)/100;
SendMessage($pV_iD,"
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
if($JuStLoVe =="تشخیص ضریب💣" and $TeRx == "private"){
$S = rand(99,200)/100;
SendMessage($pV_iD,"
💣 Forecast panel : 

🚀 ضریب این دست در ⇲ <code>$S</code> ⇱ پیشبینی شده است ᱹᱹᱹ
این ضریب مخصوص ربات ما می‌باشد ❗️

 🥀 : $botid
",$button_TAK);
}

if($JuStLoVe =="$dok18" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dnckdkck=file_get_contents("users.txt");
$exp=explode("\n",$dnckdkck);
if(!in_array($pV_iD,$exp)){
$myfile2 = fopen("users.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pV_iD\n");
fclose($myfile2);
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pv = "💣 کاربر $first_name به گروه چت پیوست ";
sendMessage($user,$pv);
}
}
SendMessage($pV_iD,"
✅ با موفقیت وارد گروه چت ربات شرط بندی شدید 


جهت ارسال پیام به اعضای گروه از دکمه 📨 ارسال پیام استفاده کنید . . . 
",$button_chat);
}
if($JuStLoVe =="🌺 بستن آخرین چت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if(file_exists("users.txt")){
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pmclose = "✅ ادمین ( <code>$first_name</code> ) چت فعلی را بست ❌
 جهت چت مجدد روی <code>☁️ چت با اعضا ربات</code> بزنید . . . ";
sendMessage($user,$pmclose);
}
$close = "";
file_put_contents("users.txt",$close);
}
SendMessage($pV_iD,"✅ ادمین گرامی :) با موفقیت چت بسته شد . . . ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}

if($JuStLoVe =="بستن چت ❌" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dcoeockencn=file_get_contents("users.txt");
$fncndkckek = str_replace($pV_iD, "", $dcoeockencn);
file_put_contents("users.txt", $fncndkckek);
SendMessage($pV_iD,"✅ با موفقیت از چت خارج شدید . . . 


جهت پیوستن مجدد به چت روی دکمه <code>$dok18</code> بزنید . . . 

",json_encode(['keyboard'=>[[['text'=>"$dok18"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$ps = "💣 کاربر $first_name از گروه لفت داد ";
sendMessage($user,$ps);
}
}
if($JuStLoVe =="📊 آمار گروه چت" and $TeRx == "private"){
	SendMessage($pV_iD,"
📊 لیست آمار کسانی که تا ساعت <code>$time</code> و تا تاریخ <code>$date</code> در گروه چت وجود دارند 👇
");
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$dnckdkck=file_get_contents("users.txt");
SendMessage($pV_iD,"
<code>$user</code>
",$button_chat);
}}
if($JuStLoVe =="📨 ارسال پیام" and $TeRx == "private"){
$ReQhV["step"] = "caht2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♻️ پیام خود را ارسال کنید تا برای همه اعضای این چت ارسال کنم . . .
",$button_chat);
}
if($step == "caht2" and $JuStLoVe !="🔙 بازگشت" and $JuStLoVe !="📨 ارسال پیام" and $JuStLoVe !="📊 آمار گروه چت" and $JuStLoVe !="بستن چت ❌" and $JuStLoVe !="☁️ چت با اعضا ربات" and $TeRx == "private"){
 if(preg_match('/[a-z]/i',$JuStLoVe)){
   SendMessage($pV_iD,"امکان ارسال لینک وجود ندارد");
  exit();
  }
  if(preg_match('~bot|t(elegram)?\.me|@|\.ir|\.com~i',$JuStLoVe)){
   SendMessage($pV_iD,"امکان ارسال لینک وجود ندارد");
   exit();
  }
$all_member = fopen( "users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
$pm = "$first_name : $JuStLoVe";
sendMessage($user,$pm);
}}
if($JuStLoVe =="🔎 وضعیت سهام من" and $TeRx == "private"){

SendMessage($pV_iD,"
🌹مشخصات سهام داری شما
",json_encode(['inline_keyboard' => [
[['text'=>"$pV_iD",'callback_data'=>'prooo'],['text'=>"🔐 شناسه شما :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['saham']}",'callback_data'=>'prooo'],['text'=>"📊 تعداد سهام شما :",'callback_data'=>'prooo']],
[['text'=>"=•=•=•=•=•=•=•=•=•=•=",'callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>"⏳ ساعت :",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>"⏳ تاریخ :",'callback_data'=>'prooo']],
]]));exit();
}
if($JuStLoVe =="💰 فروش سهام" and $TeRx == "private"){
$ReQhV["step"] = "stL";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💣 تعداد سهام را جهت فروش ارسال کنید ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
if($step == "stL" and $JuStLoVe !="🔙 بازگشت" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe <= $ReQhV["saham"]){
$ReQhV["step"] = "stL-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$Q = $JuStLoVe * $buysaham;
SendMessage($pV_iD,"
🛍 جهت فروش تعداد <code>$JuStLoVe</code> سهام به قیمت <code>$Q</code> تومان ای دی عددی خود را ارسال کنید ❕
",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💢 شما این مقدار سهام ندارید ❗️",$button);
}}
if(strpos($step, "stL-") !== false and $JuStLoVe =="$pV_iD" and $JuStLoVe !="🔙 بازگشت" and is_numeric($JuStLoVe) and $TeRx == "private"){
$b = explode("-",$step);
$tok = $b[1]*$buysaham;
$mo = $ReQhV["money"]+$tok;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🛍 شما تعداد <code>$b[1]</code> به مبلغ <code>$tok</code> نیتروسین فروختید . . .
",$button);
$ckcbbx = "
👤 نام فرد سهام دار : $first_name
📊 مقدار خرید سهام فرد : {$ReQhV['saham']} عدد


┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
"; 
$sahamdar = file_get_contents("saham/sahamdar.txt");
$djcnfkvkrj = str_replace($ckcbbx, "", $sahamdar);
file_put_contents("saham/sahamdar.txt", $djcnfkvkrj);
$newsaham = $ReQhV["saham"] - $b[1];
$ReQhV["saham"] = "$newsaham";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$again = "
👤 نام فرد سهام دار : $first_name
📊 مقدار خرید سهام فرد : {$ReQhV['saham']} عدد


┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
"; 
$myfile2 = fopen("saham/sahamdar.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$again\n");
fclose($myfile2);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$newsam = $mix - $b[1];
file_put_contents("saham/saham.txt", "$newsam");
} 
if($JuStLoVe =="📈 خرید سهام" and $TeRx == "private"){
if($mix <= 99){
$ReQhV["step"] = "stV";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد سهام جهت خرید را وارد کنید بین 0 تا $baqi وارد کنید 🎈",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"سهامی برای خرید وجود ندارد ❌",$button);exit();
}}
if($step == "stV" and $JuStLoVe !="🔙 بازگشت" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe >= 1 and $JuStLoVe <= 100 and $ReQhV["money"] >= $JuStLoVe * $salesaham){
$ReQhV["step"] = "stV-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$Q = $JuStLoVe * $salesaham;
SendMessage($pV_iD,"
✅ ای دی عددی خود را جهت خرید این تعداد سهام و با این قیمت ارسال کنید . . .

📈 مقدار سهام جهت خرید : <code>$JuStLoVe</code>
📉 قیمت تعداد سهام انتخاب شده : <code>$Q</code>

",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$R = $JuStLoVe * $salesaham;
SendMessage($pV_iD,"⭕️ قیمت این تعداد سهام $R نیترو سین می‌باشد موجودی شما کافی نمیباشد ❗️",$button);
}}
if(strpos($step, "stV-") !== false and $JuStLoVe =="$pV_iD" and $JuStLoVe !="🔙 بازگشت" and is_numeric($JuStLoVe) and $TeRx == "private"){
$b = explode("-",$step);
if($b[1] <= $baqi){
$tok = $b[1]*$salesaham;
$mo = $ReQhV["money"]-$tok;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
شما تعداد <code>$b[1]</code> سهام خریدید 💣
",$button);

$newsaham = $ReQhV["saham"] + $b[1];
$ReQhV["saham"] = "$newsaham";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
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
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"این تعداد سهام برای خرید باقی نمانده است ❌ لطفاً دوباره امتحان کنید",$button);
}}

if($JuStLoVe =="📊 سهام داران" and $TeRx == "private"){
SendMessage($pV_iD,"

📊 اطلاعات سهام و سهام داران:

📈 نام سهام داران فعلی :
      
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
$sahamdar

    
=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=
📈 وضعیت فعلی سهام ها : 100 درصد / نا مشخص

📈 قیمت هر یک سهام جهت فروش : $buysaham هزار سکه

📈 قیمت هر یک سهام جهت خرید : $salesaham هزار سکه

📈 تعداد سهام های باقی مانده : $baqi

📈 تعداد سهام های فروخته شده : $mix
");
}
if($JuStLoVe =="🎯 دارت" and $TeRx == "private"){
$X1= $dartV+1;
$X2 = $dartGH+1;
$X3 = $dartSS+1;
$X4 = $dartBI+1;
SendMessage($pV_iD,"
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
if($JuStLoVe =="🎲 تاس" and $TeRx == "private"){
$Y1 = $tasupd+1;
$Y2 = $tasfard+1;
$Y3 = $tashad+1;
SendMessage($pV_iD,"
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

if($JuStLoVe =="$dok1" and $TeRx == "private"){
if($meqdar < $ReQhV['money']){
SendMessage($pV_iD,"🔢 توضیحات بازی مونتی : در این بازی ابتدا شما مبلغ شرط وارد میکنید سپس پیشبینی میکنید عددی که ربات میسازه بیشتر از پنجاه میباشد یا کمتر در صورت درست حدس زدن شما برنده میشوید

🎲 توضیحات بازی تاس : انواع بازی های تاس بصورت خودکار در کانال انجام میشوند / تاس بالا / تاس پایین / تاس زوج / تاس فرد / حدس عدد تاس اگر حدس شما درست باشد شما برنده میشوید

🎯 توضیحات بازی دارت : در این بازی شما ابتدا نوع بازی دارت را انتخاب میکنید 
⟪ دارت قرمز ⟫⟪ دارت سفید ⟫⟪ دارت وسط ⟫⟪ دارت بیرون ⟫ و سپس مبلغ شرط را تنظیم میکنید و بعد  ✅ انجام را میزنید و شرط شما در کانال انجام میشود 

🎰 توضیحات بازی ماشین اسلات : در این بازی شما ابتدا مبلغ شرط را تنظیم میکنید سپس ✅انجام را بزنید در صورتی که سه گزینه تولید شده در ماشین اسلات مشابه هم باشند شما برنده میشوید 

🏀 بسکتبال: در این بازی شما ابتدا مبلغ شرط را تنظیم میکنید و بعد ربات استیکر تلگرامی را برایتان در کانال ارسال میکند اگر توپ در سبد بیفتد شما برنده میشوید ...

💣 توضیحات بازی انفجار : در این بازی ابتدا شما مقدار سکه و سپس یک ضریب مانند 2.01 و.. وارد میکنید (دقت کنید اعشاری باشه) سپس ربات یک ضریب میده اگر ضریب شما از ضریبی که ربات میگه کمتر باشه هرچقدر مقدار سکه ای ارسال کرده باشید همون ضریبی که زدید برابر میشه مثلا :

✅ عدد و ضریب | 3000 × 1.50 = 1500 سکه سود میکنید 

⚽️ فوتبال: در این بازی توپ فوتبال پرتاب میشود و درصورتی که گل شود شما برنده میشوید
و مدیران یا ربات هیچگونه دسترسی در بازی ها ندارند این بازی های تلگرامی هستند و ربات فقط تشخیص میدهد
🎈 بازی مورد نظر خود را از لیست پایین انتخاب کنید 🎈",$button_b);
}else{
SendMessage($pV_iD,"
💤 جهت شروع بازی حداقل $meqdar سکه لازم می‌باشد

💰 ابتدا حساب خود را شارژ کنید ! 
",$button);
}}
$m012 = rand(1,12)/1;
if($m012 == $JuStLoVe || $m012 >= $JuStLoVe){
}
$m0120 = rand(13,36)/1;
if($m0120 == $JuStLoVe || $m0120 >= $JuStLoVe){
}
$m1224 = rand(12,24)/1;
if($m1224 == $JuStLoVe || $m1224 >= $JuStLoVe){
}
$m122490 = rand(25,36)/1;
if($m122490 == $JuStLoVe || $m122490 >= $JuStLoVe){
}
$m122490 = rand(1,11)/1;
if($m122490 == $JuStLoVe || $m122490 >= $JuStLoVe){
}
$m0012 = rand(1,18)/1;
if($m0012 == $JuStLoVe || $m0012 >= $JuStLoVe){
}
$m01020 = rand(19,36)/1;
if($m01020 == $JuStLoVe || $m01020 >= $JuStLoVe){
}
$m192240 = rand(18,36)/1;
if($m192240 == $JuStLoVe || $m192240 >= $JuStLoVe){
}
$m122400 = rand(1,17)/1;
if($m122400 == $JuStLoVe || $m122400 >= $JuStLoVe){
}
$m122410 = rand(24,36)/1;
if($m122410 == $JuStLoVe || $m122410 >= $JuStLoVe){
}
$m1224080 = rand(1,23)/1;
if($m1224080 == $JuStLoVe || $m1224080 >= $JuStLoVe){
}
$gpa = rand(1,36)/1;
if($gpa == $JuStLoVe || $gpa >= $JuStLoVe){
}
$fard = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["1","3","5","7","9","11","13","15","17","19","21","23","25","27","29","31","33","35"
];
$fard = $botss[rand(0, count($botss)-1)];if($fard == $JuStLoVe || $fard >= $JuStLoVe){
}
$fard1 = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["2","4","6","8","10","12","14","16","18","20","22","24","26","28","30","32","34","36"
];
$fard1 = $botss[rand(0, count($botss)-1)];if($fard1 == $JuStLoVe || $fard1 >= $JuStLoVe){
}
$md5r = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["02e74f10e0327ad868d138f2b4fdd6f0","c74d97b01eae257e44aa9d5bade97baf","c9f0f895fb98ab9159f51fd0297e236d","cfcd208495d565ef66e7dff9f98764da","1ff1de774005f8da13f42943881c655f","e4da3b7fbbce2345d7772b0674a318d5","8f14e45fceea167a5a36dedd4bea2543","a87ff679a2f3e71d9181a67b7542122c","c20ad4d76fe97759aa27a0c99bff6710","6f4922f45568161a8cdf4ad2299f6d23","d3d9446802a44259755d38e6d163e820"
];
$md5r = $botss[rand(0, count($botss)-1)];if($md5r == $JuStLoVe || $md5r >= $JuStLoVe){
}

$hashr = rand(1,1)/1;
$one = "1"; 
$ziro = "3";
$botss = ["4579673ebb29d37a31e341b787de7bf968c45a53","09ea4c771fd41d6e857f2a6f64488f5e","6aa3b03f4c2f0ad6438d79570bd240b7","3d1bff1ba654ca5fdb6ac1370533d876","c14ab3cc72fe6a5d5d6ed96218ee315f33d3c93cfd0e1b5e5250bc4a697cd47beae805aeaeaeb25c","243b9a9757506e2e54dfbc10fc0e699f6f3d128a","86b89ce063ebc4974db7148db27ca332648d0f18dcc21d28f7234d27123b993e09119b3493b5b0f1","bfb347b6bfbb2a7cb5a7bab4724d5ee63387b7ff8aeeb38cb6abd3246167f40a5016d63743ceb352","58bd2c615ce3fbfa69b1e0e309b610e40cb4c83f","6f8549a9dca8a2dd002dd6918b88f8607fdcb4fcd8520ec38fca36c6203d72b8d2bfede25f732090","db9b3419d156b8aa90938cc3773ff8aa"
];
$hashr = $botss[rand(0, count($botss)-1)];if($hashr == $JuStLoVe || $hashr >= $JuStLoVe){
}
if($JuStLoVe =="🃏 21" and $TeRx == "private"){
if($card21 == on){
$ReQhV["step"] = "TwentyOne";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🃏 Card 21 :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "TwentyOne" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "LF";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
function randing( $LEN ) {
$charch = "2346";
return substr(str_shuffle($charch),0,$LEN);
}
file_put_contents("InFoDaTa/$pV_iD/amount.txt",$JuStLoVe);
$r =  randing(1);
file_put_contents("InFoDaTa/$pV_iD/number.txt",$r);
SendMessage($pV_iD,"
🃏 کارت که برای شما افتاد : $r
",json_encode(['keyboard'=>[
[['text'=>"🃏 دریافت کارت"]],
],'resize_keyboard'=>true]));
}}}
if($JuStLoVe =="🃏 دریافت کارت" and $step =="LF" and $TeRx == "private"){
$ReQhV["step"] = "VQ";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
$r =  rand(6,11);
$xyz = $opes+$r;
file_put_contents("InFoDaTa/$pV_iD/number.txt",$xyz);
SendMessage($pV_iD,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♥️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}
if($JuStLoVe =="♥️ دریافت کارت جدید" and $step =="VQ" and $TeRx == "private"){
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("InFoDaTa/$pV_iD/number.txt",$xyz);
if($xyz < 22){
$ReQhV["step"] = "new";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♠️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("InFoDaTa/$pV_iD/amount.txt");
$coin = $ReQhV['money'] - $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("InFoDaTa/$pV_iD/number.txt");
}}
if($JuStLoVe =="♠️ دریافت کارت جدید" and $step =="new" and $TeRx == "private"){
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("InFoDaTa/$pV_iD/number.txt",$xyz);
if($xyz < 22){
$ReQhV["step"] = "neww";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♦️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("InFoDaTa/$pV_iD/amount.txt");
$coin = $ReQhV['money'] - $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("InFoDaTa/$pV_iD/number.txt");
}}
if($JuStLoVe =="♦️ دریافت کارت جدید" and $step =="neww" and $TeRx == "private"){
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("InFoDaTa/$pV_iD/number.txt",$xyz);
if($xyz < 22){
$ReQhV["step"] = "newww";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♣️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("InFoDaTa/$pV_iD/amount.txt");
$coin = $ReQhV['money'] - $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("InFoDaTa/$pV_iD/number.txt");
}}
if($JuStLoVe =="♣️ دریافت کارت جدید" and $step =="newww" and $TeRx == "private"){
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
function randing( $LEN ) {
$charch = "2346789";
return substr(str_shuffle($charch),0,$LEN);
}
$r =  randing(1);
$xyz = $opes+$r;
file_put_contents("InFoDaTa/$pV_iD/number.txt",$xyz);
if($xyz < 22){
$ReQhV["step"] = "VQ";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♠️ مجموع کارت بازی که برای شما از دسته ورق ها بیرون آمد : $xyz
",json_encode(['keyboard'=>[
[['text'=>"♥️ دریافت کارت جدید"],['text'=>"🏦 بانکدار بازی"]],
],'resize_keyboard'=>true]));
}else{
$amount = file_get_contents("InFoDaTa/$pV_iD/amount.txt");
$coin = $ReQhV['money'] - $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🚫 متاسفانه جمع کارت های شما بالای 21 شد و شما باختید.

⭕️ مجموع کارت های شما : $xyz

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
unlink("InFoDaTa/$pV_iD/number.txt");
}}

if($JuStLoVe =="🏦 بانکدار بازی" and $TeRx == "private"){
$opes = file_get_contents("InFoDaTa/$pV_iD/number.txt");
if($opes == true){
unlink("InFoDaTa/$pV_iD/number.txt");
$amount = file_get_contents("InFoDaTa/$pV_iD/amount.txt");
$r =  rand(12,27);
if($opes > $r){
$coin = $ReQhV['money'] + $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ تبریک , جمع کارت های شما بیشتر از کارت های بانکدار بود 

♻️ مجموع کارت بانکدار : $r

🔰 مجموع کارت های شما : $opes

✅ مقدار برد : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}else{
$coin = $ReQhV['money'] - $amount;
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🚫 متاسفانه جمع کارت های شما کمتر از یا برابر با کارت بانکدار بود.

🅱️ مجموع کارت بانکدار : $r

⭕️ مجموع کارت های شما : $opes

❌ مقدار موجودی باخته شده : $amount
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}else{
SendMessage($pV_iD,"
❌
",$button);
}}

if($JuStLoVe =="🎱 رولت" and $TeRx == "private"){
if($roleton == on){
$ReQhV["step"] = "ro";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎱 Panel Roulette :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "ro" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "ro-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎱 Panel Roulette :

🔢 گزینه مورد نظر خود را پیشبینی کنید:
",json_encode(['keyboard'=>[[['text'=>"🎱 پیشبینی عدد 12 الی 24"],['text'=>"🎱 پیشبینی عدد 0 الی 12"]],[['text'=>"🎱 پیشبینی عدد 24 الی 36"],['text'=>"🎱 پیشبینی عدد 0 الی 18"]],[['text'=>"🎱 پیشبینی عدد 18 الی 36"]],[['text'=>"🎱 پیشبینی عدد زوج"],['text'=>"🎱 پیشبینی عدد فرد"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "ro-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎱 نوع شرط : رولت
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
RfTvKj('Sendsticker',['chat_id'=>$shart,'sticker'=>"https://t.me/slokings/7847",]);
if($JuStLoVe == "🎱 پیشبینی عدد 0 الی 12"){
$a = rand(0,36);
if($a < 12){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 12",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 12

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 12",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 12

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد 0 الی 18"){
$a = rand(0,36);
if($a < 18){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 18",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 18

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 0 الی 18",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 0 الی 18

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد 12 الی 24"){
$a = rand(0,36);
if($a == 12 or $a == 13 or $a == 14 or $a == 15 or $a == 16 or $a == 17 or $a == 18 or $a == 19 or $a == 20 or $a == 21 or $a == 22 or $a == 23 or $a == 24){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 12 الی 24",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 12 الی 24

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 12 الی 24",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 12 الی 24

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد 24 الی 36"){
$a = rand(0,36);
if($a == 24 or $a == 25 or $a == 26 or $a == 27 or $a == 28 or $a == 29 or $a == 30 or $a == 31 or $a == 32 or $a == 33 or $a == 34 or $a == 35 or $a ==36){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 24 الی 36",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 24 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 24 الی 36",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 24 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد 18 الی 36"){
$a = rand(0,36);
if($a == 18 or $a == 19 or $a == 20 or $a == 21 or $a == 22 or $a == 23 or $a == 24 or $a == 25 or $a == 26 or $a == 27 or $a == 28 or $a == 29 or $a == 30 or $a == 31 or $a == 32 or $a == 33 or $a == 34 or $a == 35 or $a ==36){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 18 الی 36",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد 18 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد 18 الی 36",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد 18 الی 36

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد زوج"){
$a = rand(0,36);
if($a == 0 or $a == 2 or $a == 4 or $a == 6 or $a == 8 or $a == 10 or $a == 12 or $a == 14 or $a == 16 or $a == 18 or $a == 20 or $a == 22 or $a == 24 or $a == 26 or $a == 28 or $a == 30 or $a == 32 or $a == 34 or $a == 36){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد زوج",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد زوج

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد زوج",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد زوج

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}
if($JuStLoVe == "🎱 پیشبینی عدد فرد"){
$a = rand(0,36);
if($a == 1 or $a == 3 or $a == 5 or $a == 7 or $a == 9 or $a == 11 or $a == 13 or $a == 15 or $a == 17 or $a == 19 or $a == 21 or $a == 23 or $a == 25 or $a == 27 or $a == 29 or $a == 31 or $a == 33 or $a == 35){
$g = $b[1]*$rolet;
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $g برنده شدید 🎉

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد فرد",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 🎱 پیشبینی عدد فرد

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎱 Panel Roulette :
   
🎈 این دست در $a بسته شد 

💰 شما سکه $b[1] باختید ❌

📊 گزینه درخواستی شما : 🎱 پیشبینی عدد فرد",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 🎱 پیشبینی عدد فرد

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎱 رولت",'url' => "https://telegram.me/share/url?url=🎱%20رولت"]],]]));exit();
}}}
if($JuStLoVe =="⚽️ فوتبال" and $TeRx == "private"){
if($footi == on){
$ReQhV["step"] = "foot";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"⚽️ football Bet :  

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "foot" and is_numeric($JuStLoVe) and $TeRx == "private"){ 
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn); 
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button); 
}else{
$ReQhV["step"] = "foot-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "foot-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
⚽️ نوع شرط : فوتبال
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '⚽️']);
$value = $dice->result->dice->value;
if($value == 3 or $value == 4 or $value == 5 or $value == 6 ){
$g = $b[1]*$football;
SendMessage($pV_iD,"⚽️ football Bet :

 📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"
✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "⚽ فوتبال",'url' => "https://telegram.me/share/url?url=⚽️%20فوتبال"]],]]));exit();
}else{
SendMessage($pV_iD,"⚽️ football Bet : 

 📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "⚽ فوتبال",'url' => "https://telegram.me/share/url?url=⚽️%20فوتبال"]],]]));exit();
}}

if($JuStLoVe =="🏀 بسکتبال" and $TeRx == "private"){
if($basket == on){
$ReQhV["step"] = "bas";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🏀 Basketball Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "bas" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "bask-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "bask-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🏀 نوع شرط : بسکتبال
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🏀']);
$value = $dice->result->dice->value;
if($value == 4 or $value == 5 or $value == 6){
$g = $b[1]*$basketball;
SendMessage($pV_iD,"🏀 Basketball Bet :
   
📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🏀 بسکتبال",'url' => "https://telegram.me/share/url?url=🏀%20بسکتبال"]],]]));exit();
}else{
SendMessage($pV_iD,"🏀 Basketball Bet :
   
📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🏀 بسکتبال",'url' => "https://telegram.me/share/url?url=🏀%20بسکتبال"]],]]));exit();
}}
if($JuStLoVe =="🎳 بولینگ" and $TeRx == "private"){
if($boli == on){
$ReQhV["step"] = "boling";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎳 Bolling Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "boling" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "boling-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "boling-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎳 نوع شرط : بولینگ
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎳']);
$value = $dice->result->dice->value;
if($value==6){
$g = $b[1]*$boling;
SendMessage($pV_iD,"🎳 Bolling Bet :
   
📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎳 بولینگ",'url' => "https://telegram.me/share/url?url=🎳%20بولینگ"]],]]));exit();
}else{
SendMessage($pV_iD,"🎳 Bolling Bet :
   
📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎳 بولینگ",'url' => "https://telegram.me/share/url?url=🎳%20بولینگ"]],]]));exit();
}}
if($JuStLoVe =="🎲 حدس عدد تاس" and $TeRx == "private"){
if($tasi == on){
$ReQhV["step"] = "do";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "do" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "d-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 بنظر شما عدد تاس بر روی چند می ایستد؟",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "d-") !== false and is_numeric($JuStLoVe) and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
if($JuStLoVe <= 6 and $JuStLoVe >= 1){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : حدس عدد تاس
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
⛱ عدد پیش بینی شده : $JuStLoVe
╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($JuStLoVe == $value){
$g = $b[1]*$tashad;
SendMessage($pV_iD,"🎲 Dice Bet :
   
✅ حدس شما درست بود !

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($pV_iD,"🎲 Dice Bet :
   
🎲 عدد پیشبینی شده : $JuStLoVe ⟿
🎲 عدد تاس این دست : $value ⟿

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ عدد ارسالی صحیح نیست حداقل عدد 1 و حداکثر 6 میباشد.",$button);exit();
}}
if($JuStLoVe =="🎲 تاس فرد" and $TeRx == "private"){
if($tasi == on){
$ReQhV["step"] = "taF";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "taF" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "tasF-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasF-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس فرد
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 5 or $value == 3 or $value == 1){
$g = $b[1]*$tasfard;
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($JuStLoVe =="🎲 تاس زوج" and $TeRx == "private"){
if($tasi == on){
$ReQhV["step"] = "taZ";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "taZ" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "tasZ-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasZ-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس زوج
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 6 or $value == 4 or $value == 2){
$g = $b[1]*$tasfard;
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($JuStLoVe =="🎲 تاس پایین" and $TeRx == "private"){
if($tasi == on){
$ReQhV["step"] = "taP";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "taP" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "tasP-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasP-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس پایین
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 1 or $value == 2 or $value == 3){
$g = $b[1]*$tasupd;
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($JuStLoVe =="🎲 تاس بالا" and $TeRx == "private"){
if($tasi == on){
$ReQhV["step"] = "taU";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎲 Dice Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "taU" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "tasU-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "tasU-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎲 نوع شرط : تاس بالا
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎲']);
$value = $dice->result->dice->value;
if($value == 4 or $value == 5 or $value == 6){
$g = $b[1]*$tasupd;
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}else{
SendMessage($pV_iD,"🎲 Dice Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' =>[[['text' => "🎲 تاس",'url' => "https://telegram.me/share/url?url=🎲%20تاس"]],]]));exit();
}}
if($JuStLoVe =="🎯 دارت وسط" and $TeRx == "private"){
if($darti == on){
$ReQhV["step"] = "da";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "da" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "dar-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "dar-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت معمولی ( وسط )
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value==6){
$g = $b[1]*$dartV;
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($JuStLoVe =="🎯 دارت سفید" and $TeRx == "private"){
if($darti == on){
$ReQhV["step"] = "das";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "das" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "darS-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darS-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت سفید
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 5 or $value == 3){
$g = $b[1]*$dartSS;
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($JuStLoVe =="🎯 دارت قرمز" and $TeRx == "private"){
if($darti == on){
$ReQhV["step"] = "daf";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "daf" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "darF-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darF-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت قرمز
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 6 or $value == 4 or $value == 2){
$g = $b[1]*$dartGH;
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($JuStLoVe =="🎯 دارت بیرون" and $TeRx == "private"){
if($darti == on){
$ReQhV["step"] = "daB";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎯 Dart Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "daB" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "darB-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "darB-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎯 نوع شرط : دارت بیرون
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎯']);
$value = $dice->result->dice->value;
if($value == 1){
$g = $b[1]*$dartBI;
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}else{
SendMessage($pV_iD,"🎯 Dart Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎯 دارت",'url' => "https://telegram.me/share/url?url=🎯%20دارت"]],]]));exit();
}}
if($JuStLoVe =="🎰 ماشین اسلات" and $TeRx == "private"){
if($skati == on){
$ReQhV["step"] = "scat";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎰 Scat Bet :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "scat" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "act-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👇 جهت شروع روی دکمه ✅انجام کلیک کنید:",json_encode(['keyboard'=>[[['text'=>"✅انجام"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "act-") !== false and $JuStLoVe == "✅انجام" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🎰 نوع شرط : اسلات
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$dice = RfTvKj('sendDice',['chat_id' => $shart,'emoji'=> '🎰']);
$value = $dice->result->dice->value;
if($value == 64){
$g = $b[1]*$skat;
SendMessage($pV_iD,"🎰 Scat Bet :

📊 شما مبلغ $g سکه برنده شدید ➛║ $shart ║",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎰 ماشین اسلات",'url' => "https://telegram.me/share/url?url=🎰%20ماشین اسلات"]],]]));exit();
}else{
SendMessage($pV_iD,"🎰 Scat Bet :

📊 شما مبلغ $b[1] سکه باختید ➛║ $shart ║",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🎰 ماشین اسلات",'url' => "https://telegram.me/share/url?url=🎰%20ماشین اسلات"]],]]));exit();
}}

if($JuStLoVe =="💣 انفجار" and $TeRx == "private"){
if($enfejar == on){
$ReQhV["step"] = "enfejar";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 ExplosionPanel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "enfejar" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "ex2-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💣 ضریب مورد نظر خود را ارسال کنید.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "ex2-") !== false and is_numeric($JuStLoVe) and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
if($JuStLoVe >= 1.01 and $JuStLoVe <= 999.99){
SendMessage($shart,"╔═══════════════════════════════╗
💣 نوع شرط : انفجار
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$m = rand(99,160)/100;
SendMessage($shart,"
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $JuStLoVe
");
if($m == $JuStLoVe || $m >= $JuStLoVe){
$abolfazl = $b[1] * $JuStLoVe;
$g = $abolfazl - $b[1];
$tak = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🧬 ضریب درخواستی : $JuStLoVe
💯 ضریب بسته شده : $m
🔰 وضعیت : ✅ برد
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/crash3.txt",$crash2);
file_put_contents("xbox/crash2.txt",$crash1);
file_put_contents("xbox/crash1.txt",$tak); 
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
SendMessage($pV_iD,"💣 Explosion Bet :
   
🔥 ضریب این دست در $m بسته شد.

💰 شما مبلغ $abolfazl سکه برنده شدید.

💣 ضریب درخواستی شما : $JuStLoVe
➖➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md</code>

💣Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $abolfazl coin
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $JuStLoVe

🎉 channel : $shart");exit();
}else{
$tak = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🧬 ضریب درخواستی : $JuStLoVe
💯 ضریب بسته شده : $m
🔰 وضعیت : ❌ باخت 
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/crash3.txt",$crash2);
file_put_contents("xbox/crash2.txt",$crash1);
file_put_contents("xbox/crash1.txt",$tak); 
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
SendMessage($pV_iD,"💣 Explosion Bet :
   
💣 ضریب این دست در $m بسته شد.

❌ شما مبلغ $b[1] سکه باختید.

🎲 ضریب درخواستی شما : $JuStLoVe
➖➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md</code>

💣Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧨 Loss amount : $b[1] coin
⭕️ Closed coefficient : $m
🧬 Requested coefficient : $JuStLoVe

🎉 channel : $shart");exit();
}}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ ضریب ارسال صحیح نیست حداقل ضریب 1.01 و حداکثر 999.99 میباشد.",$button);exit();
}}
if($JuStLoVe =="🔢 مونتی" and $TeRx == "private"){
if($moont == on){
$ReQhV["step"] = "mo";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 CrashPanel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "mo" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "m-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 Monti Panel :

🔢 گزینه مورد نظر خود را پیشبینی کنید:",json_encode(['keyboard'=>[[['text'=>"عدد شانس بیشتر از 50"],['text'=>"عدد شانس کمتر از 50"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "m-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
🔢 نوع شرط : مونتی
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
RfTvKj('Sendsticker',['chat_id'=>$shart,'sticker'=>"https://t.me/slokings/7846",]);
if($JuStLoVe == "عدد شانس کمتر از 50"){
$a = rand(1,100);
if($a < 50){
$g = $b[1]*$mon;
$tik = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $JuStLoVe
وضعیت بازی : ✅ برد
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tik);
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 Panel Bet:
   
🔢  این دست در $a بسته شد.

💰 شما سکه $g برنده شدید.

🎲 گزینه درخواستی شما : کمتر از 50",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
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
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $JuStLoVe
وضعیت بازی : ❌ باخت
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tok);
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 Panel Monti:
   
🔢 عدد این دست در $a بسته شد.

❌ شما مبلغ $b[1] سکه باختید.

🎲 گزینه درخواستی شما : کمتر از 50",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : عدد شانس کمتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}}
if($JuStLoVe == "عدد شانس بیشتر از 50"){
$a = rand(1,100);
if($a > 50){
$g = $b[1]*$mon;
$tik = "
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $JuStLoVe
وضعیت بازی : ✅ برد
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tik);
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 Panel Bet:
   
🔢  این دست در $a بسته شد.

💰 شما مبلغ $g برنده شدید.

🎲 گزینه درخواستی شما : بیشتر از 50",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
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
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]
🔢 پیش‌بینی : $JuStLoVe
وضعیت بازی : ❌ باخت
"; 
$ReQhV["step"] = "none";
file_put_contents("xbox/monti3.txt",$monti2);
file_put_contents("xbox/monti2.txt",$monti1);
file_put_contents("xbox/monti1.txt",$tok);
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤖 Panel Monti:
   
🔢 عدد این دست در $a بسته شد.

❌ شما مبلغ $b[1] تومان باختید.

🎲 گزینه درخواستی شما : بیشتر از 50",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : عدد شانس بیشتر از 50
❌ Closed coefficient : $a

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "🔢 مونتی",'url' => "https://telegram.me/share/url?url=🔢%20مونتی"]],]]));exit();
}}}
if($JuStLoVe =="💩 پوپ" and $TeRx == "private"){
if($popi == on){
$ReQhV["step"] = "po";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💩 Pop Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "po" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "po-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🤑 Poop Bet 💩:
  
💩 از جعبه های زیر یکی را انتخاب کنید اگر در جعبه مورد نظر شکلک 🤑 قرار داشته باشد مقدار سکه مورد نظر شما 1.25 برابر میشود و اما شکلک 💩 باشد شما میبازید ❗️",json_encode(['keyboard'=>[[['text'=>"جعبه دوم 📦"],['text'=>"جعبه اول 📦"]],[['text'=>"جعبه چهارم 📦"],['text'=>"جعبه سوم 📦"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "po-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
💩 نوع شرط : پوپ
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$md=md5(rand(1000,99999));
$hash = hash('ripemd160',rand(1000,99999));
if($JuStLoVe == "جعبه اول 📦"){
$a = rand(100,12);
if($a < 50){
$g = $b[1]*$poop;
SendMessage($pV_iD,"🤑");
SendMessage($pV_iD,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه اول 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($pV_iD,"💩");
SendMessage($pV_iD,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه اول 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($JuStLoVe == "جعبه دوم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1]*$poop;
SendMessage($pV_iD,"🤑");
SendMessage($pV_iD,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه دوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($pV_iD,"💩");
SendMessage($pV_iD,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه دوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($JuStLoVe == "جعبه سوم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1]*$poop;
SendMessage($pV_iD,"🤑");
SendMessage($pV_iD,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه سوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($pV_iD,"💩");
SendMessage($pV_iD,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه سوم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
if($JuStLoVe == "جعبه چهارم 📦"){
$a = rand(88,0);
if($a > 50){
$g = $b[1]*$poop;
SendMessage($pV_iD,"🤑");
SendMessage($pV_iD,"🤑 Poop :
   
🤑 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
🤑Md5 : <code>$md</code>

🤑Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🤑");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : جعبه چهارم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}else{
SendMessage($pV_iD,"💩");
SendMessage($pV_iD,"💩  Poop Bet :
   
💩  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
💩Md5 : <code>$md</code>

💩Hash : <code>$hash</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"💩");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : جعبه چهارم 📦

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "💩 پوپ",'url' => "https://telegram.me/share/url?url=💩%20پوپ"]],]]));exit();
}}
}
if($JuStLoVe =="👊🏻 سنگ کاغذ قیچی" and $TeRx == "private"){
if($sangi == on){
$ReQhV["step"] = "sa";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "sa" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "sa-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
  
به پنل سنگ کاغد قیچی خوش امدید .
  
انتخاب خود را بکنید 💎
در صورت امدن یکی از موارد زیر شما برنده میشوید !
👊🏻 سنگ : قیچی
✌🏻 قیچی : کاغذ 
✋🏻 کاغذ : سنگ",json_encode(['keyboard'=>[[['text'=>"✋🏻 کاغذ"],['text'=>"👊🏻 سنگ"]],[['text'=>"✌🏻 قیچی"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "sa-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
👊🏻 نوع شرط : سنگ / کاغذ / قیچی
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if($JuStLoVe == "👊🏻 سنگ"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(1,90);
if($a > 60){
$g = $b[1]*$Paper;
SendMessage($pV_iD,"✌🏻‍");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
✌🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
✌🏻Md5 : <code>$hadi</code>

✌🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✌🏻 قیچی");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : 👊🏻 سنگ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($pV_iD,"✋🏻");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
✋🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
✋🏻Md5 : <code>$hadi</code>

✋🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✋🏻 کاغذ");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : 👊🏻 سنگ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
if($JuStLoVe == "✋🏻 کاغذ"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(90,180);
if($a > 150){
$g = $b[1]*$Paper;
SendMessage($pV_iD,"👊🏻");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
👊🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
👊🏻Md5 : <code>$hadi</code>

👊🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"👊🏻 سنگ");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : ✋🏻 کاغذ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($pV_iD,"✌🏻‌");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
✌🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
✌🏻Md5 : <code>$hadi</code>

✌🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✌🏻 قیچی");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : ✋🏻 کاغذ

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
if($JuStLoVe == "✌🏻 قیچی"){
$hadi =  rand_string(32);
$hadi2 =  rand_string(40);
$a = rand(1,90);
if($a < 30){
$g = $b[1]*$Paper;
SendMessage($pV_iD,"✋🏻");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
✋🏻 شما مقدار $g سکه برنده شدید!
➖➖➖➖➖➖➖➖➖➖➖➖➖
✋🏻Md5 : <code>$hadi</code>

✋🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"✋🏻 کاغذ");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : ✌🏻 قیچی

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}else{
SendMessage($pV_iD,"👊🏻");
SendMessage($pV_iD,"👊🏻 Scissors / Paper / Rock :
   
👊🏻  شما مقدار $b[1] سکه باختید
➖➖➖➖➖➖➖➖➖➖➖➖➖
👊🏻Md5 : <code>$hadi</code>

👊🏻Hash : <code>$hadi2</code>",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"👊🏻 سنگ");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : ✌🏻 قیچی

🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "👊🏻 سنگ کاغذ قیچی",'url' => "https://telegram.me/share/url?url=👊🏻%20سنگ%20کاغذ%20قیچی"]],]]));exit();
}}
}
if($JuStLoVe =="✊ گل یا پوچ" and $TeRx == "private"){
if($gooli == on){
$ReQhV["step"] = "gol";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌟 Absurd Panel :

💰 مقدار سکه که میخواهید شرط کنید را ارسال کنید حداقل $meqdar سکه میباشد.",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ بازی $JuStLoVe خاموش است . . .",$button);exit();
}}
if($step == "gol" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe < $meqdar){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ مقدار شرط نامعتبر است.(لطفا بصورت صحیح و لاتین و بسته به موجودی خود وارد کنید حداقل شرط $meqdar سکه است.)",$button);exit();
}else{
if($JuStLoVe > $ReQhV['money']){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"موجودی کافی نیست",$button);
}else{
$ReQhV["step"] = "gol-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌟 Absurd Panel :

🎈 گزینه مورد نظر خود را انتخاب کنید ( ( گل ✊ یا 👐 پوچ ) )",json_encode(['keyboard'=>[[['text'=>"گل ✊"],['text'=>"پوچ 👐"]],[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}}}
elseif(strpos($step, "gol-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($shart,"╔═══════════════════════════════╗
👐 نوع شرط : ✊ گل یا پوچ
📄 نام : $first_name
🎈 آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time
💣 مقدار شرط : $b[1]

╔═══════════════╣⫸
║ ID channel : $shart
║ ID bot : @$botid
╚═══════════════╣⫸");
$newgame = $ReQhV['game']+1;
$ReQhV["game"] = "$newgame";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if($JuStLoVe == "پوچ 👐"){
$a = rand(1,100);
if($a < 50){
$g = $b[1]*$gool;
SendMessage($pV_iD,"پوچ 👐");
SendMessage($pV_iD,"🌟 Absurd :
   
این دست 👐 پوچ بود شما مبلغ $g برنده شدید 🥳 ➛║$shart║

🎲 گزینه درخواستی شما : پوچ 👐",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"پوچ 👐");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : پوچ 👐


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}else{
SendMessage($pV_iD,"گل ✊");
SendMessage($pV_iD,"🌟 Absurd :
   
این دست ✊ گل بود شما مبلغ $b[1] باختید ➛║$shart║

🎲 گزینه درخواستی شما : پوچ 👐",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"گل ✊");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : پوچ 👐


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}}
if($JuStLoVe == "گل ✊"){
$a = rand(1,100);
if($a > 50){
$g = $b[1]*$gool;
SendMessage($pV_iD,"گل ✊");
SendMessage($pV_iD,"🌟 Absurd :
   
این دست ✊ گل بود شما مبلغ $g برنده شدید 🥳 ➛║$shart║

🎲 گزینه درخواستی شما :  گل ✊",$button);
$mo = $ReQhV['money']+$g;
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"گل ✊");
SendMessage($shart,"✅ he / she won
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🎊 Win amount : $g coin
🧬 Forecast : گل ✊


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}else{
SendMessage($pV_iD,"پوچ 👐");
SendMessage($pV_iD,"🌟 Absurd :
   
این دست 👐 پوچ بود شما مبلغ $b[1] باختید ➛║$shart║

🎲 گزینه درخواستی شما :  گل ✊",$button);
$mo = $ReQhV['money']-$b[1];
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"پوچ 👐");
SendMessage($shart,"❌ he / she lose
🎈 Bet amount :  $b[1] coin
📊 New Stock : $mo coin
🧬 Forecast : گل ✊


🎉 channel : $shart",json_encode(['inline_keyboard' => [[['text' => "✊ گل یا پوچ",'url' => "https://telegram.me/share/url?url=✊%20گل%20یا%20پوچ"]],]]));exit();
}}} 
elseif($JuStLoVe =="$dok11" and $TeRx == "private"){
if($ReQhV['money'] >= $bardasht){
$ReQhV["step"] = "tasfe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفا نوع برداشت را انتخاب نمایید:",$button_ts);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($JuStLoVe =="💰 نیترو سین" and $step =="tasfe" and $TeRx == "private"){
if($ReQhV['money'] >= $bardasht){
$ReQhV["step"] = "toman";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد سکه جهت برداشت را وارد کنید ❗️

💸 حداقل سکه جهت برداشت : $bardasht
💸 حداکثر سکه جهت برداشت : $bardashtX


💵 موجودی حساب شما : {$ReQhV['money']} ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($step =="toman" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe >= $bardasht and $JuStLoVe <= $bardashtX and $ReQhV['money'] >= $JuStLoVe){
$ReQhV["step"] = "to-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ایدی عددی را بفرستید",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ حداقل سکه مورد نیاز برای برداشت $bardasht سکه و حداکثر سکه جهت برداشت $bardashtX است ",$button);
}}
elseif(strpos($step, "to-") !== false and is_numeric($JuStLoVe) and $TeRx == "private"){
if(strlen($JuStLoVe) == "11" or "7"){
$b = explode("-",$step);
SendMessage($pV_iD,"✅درخواست تسویه حساب با موفقیت ارسال و پس از بررسی انجام خواهد شد.

💰 مبلغ : <code>$b[1]</code> سکه  
💸 آدرس ایدی عددی : <code>$JuStLoVe</code>",$button);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$mo = $ReQhV['money']-$b[1];
$tar = "
📊 درخواست تسویه حساب 
➖➖➖➖➖➖➖
✅ نوع درخواست : نیترو سین
📄 نام : $first_name
🥂 آیدی عددی : <code>$pV_iD</code>
💰 مقدار درخواست : <code>$b[1]</code> سکه
⏳ تاریخ و زمان : $date | $time
📤 وضعیت : انجام شد
"; 
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("tarakonesh/harvest3.txt",$harvest2);
file_put_contents("tarakonesh/harvest2.txt",$harvest1);
file_put_contents("tarakonesh/harvest1.txt",$tar);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه نیترو سین
💎 مقدار درخواست : <code>$b[1]</code> سکه
💡آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$JuStLoVe</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
SendMessage($addi,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه نیترو سین
💎 مقدار درخواست : <code>$b[1]</code> سکه
💡آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$JuStLoVe</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفا آدرس ایدی عددی 7 تا 11 رقمی خود را درست ارسال کنید",$button);
}}
elseif($JuStLoVe =="💰 رپید سین" and $step =="tasfe" and $TeRx == "private"){
if($ReQhV['money'] >= $bardasht){
$ReQhV["step"] = "bitcoin";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد سکه جهت برداشت را وارد کنید ❗️

💸 حداقل سکه جهت برداشت : $bardasht
💸 حداکثر سکه جهت برداشت : $bardashtX


💵 موجودی حساب شما : {$ReQhV['money']} ",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"جهت درخواست برداشت حداقل باید $bardasht سکه قابل برداشت داشته باشید ‼️",$button);exit();
}}
elseif($step =="bitcoin" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe >= $bardasht and $JuStLoVe <= $bardashtX and $ReQhV['money'] >= $JuStLoVe){
$ReQhV["step"] = "bioin-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"آدرس ایدی عددی را بفرستید",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ حداقل سکه مورد نیاز برای برداشت $bardasht سکه و حداکثر سکه جهت برداشت $bardashtX است ",$button);
}}
elseif(strpos($step, "bioin-") !== false and $JuStLoVe != "🔙 بازگشت" and $TeRx == "private"){
if(strlen($JuStLoVe) == "11" or "7"){
    $b = explode("-",$step);
SendMessage($pV_iD,"✅درخواست تسویه حساب با موفقیت ارسال و پس از بررسی انجام خواهد شد.

💰 مبلغ : <code>$b[1]</code> سکه  
💸 آدرس ایدی عددی : <code>$JuStLoVe</code>",$button);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$mo = $ReQhV['money']-$b[1];
$tar = "
📊 درخواست تسویه حساب 
➖➖➖➖➖➖➖
✅ نوع درخواست : ریپید سین
📄 نام : $first_name
🥂 آیدی عددی : <code>$pV_iD</code>
💰 مقدار درخواست : <code>$b[1]</code> سکه
⏳ تاریخ و زمان : $date | $time
📤 وضعیت : انجام شد
"; 
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("tarakonesh/harvest3.txt",$harvest2);
file_put_contents("tarakonesh/harvest2.txt",$harvest1);
file_put_contents("tarakonesh/harvest1.txt",$tar);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($shart,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه ریپید سین
💎 مقدار درخواست : <code>$b[1]</code> سکه
💡آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$JuStLoVe</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
SendMessage($addi,"🚀درخواست تسویه حساب
➖➖➖➖➖➖➖
✅ نوع درخواست : سکه ریپید سین
💎 مقدار درخواست : <code>$b[1]</code> سکه
💡آیدی عددی : <code>$pV_iD</code>
⏳ تاریخ و زمان : $date | $time

❗️مقدار سکه <code>$b[1]</code> را به حساب کاربری <code>$JuStLoVe</code> انتقال بدهید ❗️
📊موجودی جدید: $mo
",json_encode(['inline_keyboard' => [
[['text'=>"✅ تسویه شد",'callback_data'=>'tak']],
]]));
$ReQhV["money"] = "$mo";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفا آدرس ایدی عددی 7 تا 11 رقمی خود را درست ارسال کنید",$button);
}}
if($JuStLoVe =="$dok10" and $TeRx == "private"){
$ReQhV["step"] = "fe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"

👈🏻 ربات مورد نظر را برای شارژ حساب کاربری خود انتخاب کنید 👇🏻
",$button_f);
}
if($JuStLoVe =="💰 نیترو سین" and $step =="fe" and $TeRx == "private"){
$ReQhV["step"] = "kfe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰 میزان اعتباری که میخواهید حساب خود را شارژ کنید ارسال کنید :

🍻 حداقل $sharj سکه💰",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
elseif($step =="kfe" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe >= $sharj and $JuStLoVe <= 3000000){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($addi,"✅ یک درخواست شارژ حساب دیگر ساخته شد ❗️
کاربری : $pV_iD
📄 نام : $first_name
💰 مقدار شارژ : $JuStLoVe
⏳ تاریخ و زمان : $date | $time
");
$sha = "
📄 نام : $first_name 
🍻 کاربری : <code>$pV_iD</code>
💰 مقدار درخواست شارژ : <code>$JuStLoVe</code>
✅ نوع درخواست شارژ : نیتروسین
⏳ تاریخ و زمان : $date | $time
"; 
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("tarakonesh/hesab3.txt",$hesab2);
file_put_contents("tarakonesh/hesab2.txt",$hesab1);
file_put_contents("tarakonesh/hesab1.txt",$sha);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان <code>$JuStLoVe</code> سکه را به حساب <code>$addi</code> واریز کنید ✅
پس از واریز حساب شما حداکثر طی 10 دقیقه شارژ میشود ❕❗️",$button);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌سکه ارسالی باید بین $sharj تا 3.000.000 سکه باشد.",$button);
}}
if($JuStLoVe =="💰 رپید سین" and $step =="fe" and $TeRx == "private"){
$ReQhV["step"] = "kbit";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰 میزان اعتباری که میخواهید حساب خود را شارژ کنید ارسال کنید :

🍻 حداقل $sharj سکه💰",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}
elseif($step =="kbit" and is_numeric($JuStLoVe) and $TeRx == "private"){
if($JuStLoVe >= $sharj and $JuStLoVe <= 3000000){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($addi,"✅ یک درخواست شارژ حساب دیگر ساخته شد ❗️
کاربری : $pV_iD
📄 نام : $first_name
💰 مقدار شارژ : $JuStLoVe
⏳ تاریخ و زمان : $date | $time
");
$sha = "
📄 نام : $first_name 
🍻 کاربری : <code>$pV_iD</code>
💰 مقدار درخواست شارژ : <code>$JuStLoVe</code>
✅ نوع درخواست شارژ : ریپید سین
⏳ تاریخ و زمان : $date | $time
"; 
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("tarakonesh/hesab3.txt",$hesab2);
file_put_contents("tarakonesh/hesab2.txt",$hesab1);
file_put_contents("tarakonesh/hesab1.txt",$sha);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان <code>$JuStLoVe</code> سکه را به حساب <code>$addi</code> واریز کنید ✅
پس از واریز حساب شما حداکثر طی 10 دقیقه شارژ میشود ❕❗️",$button);
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌سکه ارسالی باید بین $sharj تا 3.000.000 سکه باشد.",$button);
}}
if($JuStLoVe =="$dos" and $TeRx == "private"){
RfTvKj('SendPhoto',[
'chat_id'=>$pV_iD,
'photo'=>"$LINK",
'caption'=>"
$mtn
",]);
RfTvKj('sendMessage',['chat_id'=>$pV_iD,'text'=>"
$mtnzir
",'parse_mode'=>'html','reply_to_message_id'=>$update->message->message_id + 1]);
}
if($JuStLoVe =="$dok3" and $TeRx == "private"){
if (!file_exists("InFoDaTa/$pV_iD/$pV_iD.json")) {
$myfile2 = fopen("InFoDaTa/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pV_iD\n");
fclose($myfile2);
$ReQhV["money"] = "$bonos3";
$ReQhV["step"] = "free";
$ReQhV["subset"] = "0";
$ReQhV["id"] = "$pV_iD";
$ReQhV["warn"] = "0";
$ReQhV["saham"] = "0";
$ReQhV["game"] = "0";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if( $username2 == NULL){
SendMessage($pV_iD,"<code>⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است.</code>


✅ اطلاعات حساب کاربری شما 👇

",json_encode(['inline_keyboard' => [
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"𝑛𝑜𝑛𝑒",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$pV_iD",'callback_data'=>'prooo'],['text'=>"🔥 شناسه کاربری شما :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['money']}",'callback_data'=>'prooo'],['text'=>"💰 موجودی حساب شما :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['money']}",'callback_data'=>'prooo'],['text'=>"💸 مقدار سکه قابل برداشت :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['subset']}",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['game']}",'callback_data'=>'prooo'],['text'=>"💣 تعداد بازی ها :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['warn']}",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $pV_iD / coin : {$ReQhV['money']} &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botid"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]]));
}else{
SendMessage($pV_iD,"<code>⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است.</code>


✅ اطلاعات حساب کاربری شما 👇

",json_encode(['inline_keyboard' => [
[['text'=>"$first_name",'callback_data'=>'prooo'],['text'=>"📑 نام شما : ",'callback_data'=>'prooo']],
[['text'=>"@$username2",'callback_data'=>'prooo'],['text'=>"🎈 یوزرنیم شما :",'callback_data'=>'prooo']],
[['text'=>"$pV_iD",'callback_data'=>'prooo'],['text'=>"🔥 شناسه کاربری شما :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['money']}",'callback_data'=>'prooo'],['text'=>"💰 موجودی حساب شما :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['money']}",'callback_data'=>'prooo'],['text'=>"💸 مقدار سکه قابل برداشت :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['subset']}",'callback_data'=>'prooo'],['text'=>"🎉 تعداد زیر مجموعه :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['game']}",'callback_data'=>'prooo'],['text'=>"💣 تعداد بازی ها :",'callback_data'=>'prooo']],
[['text'=>"{$ReQhV['warn']}",'callback_data'=>'prooo'],['text'=>"❗️تعداد اخطار های شما :",'callback_data'=>'prooo']],
[['text' => "🎈 اشتراک گذاری حساب کاربری🎈",'url' => "https://telegram.me/share/url?url= id : $pV_iD / coin : {$ReQhV['money']} &&text=موجودی%20من%20و%20ایدی%20عددی%20من%20در%20ربات%20@$botid"]],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
[['text'=>"$date",'callback_data'=>'prooo'],['text'=>'📆 تاریخ ◄','callback_data'=>'prooo']],
[['text'=>"$time",'callback_data'=>'prooo'],['text'=>'⏰ ساعت ◄','callback_data'=>'prooo']],
[['text'=>"•=•=•=•=•=•=•=•=•=•=•=•=•=•=•=•",'callback_data'=>'prooo']],
]]));
}}
if(strpos($data, "prooo") !== false){
answerCallbackQuery($callbackid,'این دکمه ها فقط برای دریافت اطلاعات هستند !',false);
}
if($JuStLoVe =="بروزرسانی ⚡" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"

🚀آخرین نسخه موجود ، در ربات شما نصب شده است🚀

🚀نسخه فعلی : 7.2🚀

");
}
if($JuStLoVe =="سرعت ممبر🚀" and $TeRx == "private"){
SendMessage($pV_iD,"

⚡جهت شارژ تعداد سکه مورد نظر را به آیدی عددی ( <code>$addi</code> ) ارسال نمایید⚡

⚡پس از واریز حساب شما خودکار شارژ می شود⚡

🎈 هر 1000 الماس معادل $speed_member سکه می باشد 🤯

");
}
if($JuStLoVe =="🚀جت ممبر" and $TeRx == "private"){
SendMessage($pV_iD,"

⚡جهت شارژ تعداد سکه مورد نظر را به آیدی عددی ( <code>$addi</code> ) ارسال نمایید⚡

⚡پس از واریز حساب شما خودکار شارژ می شود⚡

🎉 هر 1000 الماس معادل $get_member سکه می باشد 🥳

");
}
if($JuStLoVe =="$dok20" and $TeRx == "private"){
SendMessage($pV_iD,"ᵂᴬᴵᵀ ᴬ ᴹᴼᴹᴱᴺᵀ • • •");
sleep(3);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▭▭▭▭▭▭▭▭▭▭ | 0%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▭▭▭▭▭▭▭▭▭ | 10%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▭▭▭▭▭▭▭▭ | 20%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▭▭▭▭▭▭▭ | 30%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▭▭▭▭▭▭ | 40%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▭▭▭▭▭ | 50%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▬▭▭▭▭ | 60%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▬▬▭▭▭ | 70%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▬▬▬▭▭ | 80%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▬▬▬▬▭ | 90%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'▬▬▬▬▬▬▬▬▬▬ | 100%','message_id'=>$message_id + 1]);
sleep(0.2);
RfTvKj('editMessageText',['chat_id'=>$pV_iD,'text'=>'┄┅┈┉┅┉┈┅┄┄┅┈┉ 𝑈𝑃𝐷𝐴𝑇𝐸𝐷 ┉┈┅┄┄┅┈┉┅┉┈┅┄','message_id'=>$message_id + 1]);
}
if($JuStLoVe =="💣 بازی دو نفره" and $TeRx == "private"){
$ReQhV["step"] = "chat7";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ای دی عددی شخصی که میخواید باهاش بازی کنید را ارسال کنید . . .",$button_bak);
}
elseif($step == "chat7" and $JuStLoVe !="🔙 بازگشت" and $JuStLoVe !="$pV_iD" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
@$XR = file_get_contents("chat/$JuStLoVe/RT.txt");
if($XR != true){
if(!file_exists("chat")){
mkdir("chat");
}
if(!file_exists("chat/$pV_iD")){
mkdir("chat/$pV_iD");
}
file_put_contents("chat/$pV_iD/Request.txt",$JuStLoVe);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if(!file_exists("chat")){
mkdir("chat");
}
if(!file_exists("chat/$JuStLoVe")){
mkdir("chat/$JuStLoVe");
}
file_put_contents("chat/$JuStLoVe/Request.txt",$pV_iD);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ درخواست شما با موفقیت به کاربر مورد نظر ارسال شد
");
SendMessage($JuStLoVe,"
✅ یک نفر درخواست بازی داده است 

🔐 آیا بازی دو نفره را قبول میکنید یا رد میکنید ؟ 
",json_encode(['keyboard'=>[
[['text'=>"✅ تایید درخواست"],['text'=>"❌ رد درخواست"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($pV_iD,"
❌ کاربر مورد نظر در حال بازی با شخص دیگری است
",$button);
}
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌  ایدی عددی کاربری که ارسال کردید در ربات نیست",json_encode(['keyboard'=>[[['text'=>"🔙 بازگشت"]],],'resize_keyboard'=>true]));
}} 
if($JuStLoVe =="✅ تایید درخواست" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
@$RQ = file_get_contents("chat/$pV_iD/Request.txt");
@$SD = file_get_contents("chat/$pV_iD/RT.txt");
if($SD != true){
if(!file_exists("chat")){
mkdir("chat");
}
if(!file_exists("chat/$pV_iD")){
mkdir("chat/$pV_iD");
}
file_put_contents("chat/$pV_iD/RT.txt",$RQ);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if(!file_exists("chat")){
mkdir("chat");
}
if(!file_exists("chat/$RQ")){
mkdir("chat/$RQ");
}
file_put_contents("chat/$RQ/RT.txt",$pV_iD);
$ReQhV["step"] = "play";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ درخواست با موفقیت پذیرفته شد
",$button_paly);
$PlayerRQ = json_decode(file_get_contents("InFoDaTa/$RQ/$RQ.json"),true);
$ReQhV["warn"] = $PlayerRQ['warn'];
$ReQhV["saham"] = $PlayerRQ['saham'];
$ReQhV["game"] = $PlayerRQ['game'];
$ReQhV["subset"] = $PlayerRQ['subset'];
$ReQhV["id"] = "$RQ";
$ReQhV["money"] = $PlayerRQ['money'];
$ReQhV["step"] = "play";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$RQ/$RQ.json",$OoOtJsSsOn);
SendMessage($RQ,"
✅ کاربر مورد نظر درخواست شما رو پذیرفت
",$button_paly);
}else{
SendMessage($pV_iD,"
شما در حال بازی شخص دیگری هستید ❌
",$button_paly);
}}
if($JuStLoVe =="❌ رد درخواست" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
@$RQ = file_get_contents("chat/$pV_iD/Request.txt");
SendMessage($pV_iD,"
❌ در خواست با موفقیت رد شد
",$button);
SendMessage($RQ,"
❌ کاربر مورد نظر درخواست شما را رد کرد 
",$button);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
$close = "";
file_put_contents("chat/$pV_iD/RT.txt",$close);
file_put_contents("chat/$RQ/RT.txt",$close);
file_put_contents("chat/$pV_iD/Request.txt",$close);
file_put_contents("chat/$RQ/Request.txt",$close);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}
elseif($JuStLoVe =="📩 گفتگو کردن" and $step == "play" and $TeRx == "private"){
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = $ReQhV['money'];
$ReQhV["step"] = "chat8";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
👈🏻پیام خود را ارسال کنید تا به بازیکن وصل شده ارسال کنم✅
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
if($step == "chat8" and $JuStLoVe !="✅ قبول کردن" and $JuStLoVe !="❌ رد کردن" and $JuStLoVe !="📩 گفتگو کردن" and $JuStLoVe !="🔙 بازگشت" and $JuStLoVe !="✅ تایید درخواست" and $JuStLoVe !="❌ رد درخواست" and $JuStLoVe !="🔙 برگشت" and $JuStLoVe !="❌ قطع اتصال" and $JuStLoVe !="⚖ قوانین" and $JuStLoVe !="📚 راهنما" and $JuStLoVe !="🎮 بریم بازی" and $JuStLoVe !="📛گزارش بازیکن" and $JuStLoVe !="✌️🏻" and $JuStLoVe !="👊🏻" and $JuStLoVe !="🖐🏻" and $TeRx == "private"){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
 if($WT == true){
 if(preg_match('/[a-z]/i',$JuStLoVe)){
   SendMessage($pV_iD,"امکان ارسال لینک وجود ندارد");
  exit();
  }
  if(preg_match('~bot|t(elegram)?\.me|@|\.ir|\.com~i',$JuStLoVe)){
   SendMessage($pV_iD,"امکان ارسال لینک وجود ندارد");
   exit();
  }
SendMessage($WT,$JuStLoVe);
SendMessage($pV_iD,"
❇️ پیام شما با موفقیت به بازیکن ارسال گردید . . . 

🔐 جهت ارسالی پیامی دیگر متن پیام را ارسال کنید

🔙 در غیر این صورت بر روی برگشت کلیک کنید 
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($pV_iD,"
❌ ارتباط شما با کاربر مورد نظر قطع شده است روی | 🔙 بازگشت | بزنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}
elseif($JuStLoVe =="❌ قطع اتصال" and $step == "play" and $TeRx == "private"){
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = $ReQhV['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
SendMessage($WT,"
ارتباط شما توسط کاربر مقابل قطع شد✅

👈🏻به منوی اصلی بازگشتید.
",$button);
SendMessage($pV_iD,"
ارتباط با موفقیت قطع شد✅

👈🏻به منوی اصلی بازگشتید.
",$button);
$close = "";
file_put_contents("chat/$pV_iD/RT.txt",$close);
file_put_contents("chat/$WT/RT.txt",$close);
$PlayerOff = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$ReQhV["warn"] = $PlayerOff['warn'];
$ReQhV["saham"] = $PlayerOff['saham'];
$ReQhV["game"] = $PlayerOff['game'];
$ReQhV["subset"] = $PlayerOff['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = $PlayerOff['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
}

elseif($JuStLoVe =="⚖ قوانین" and $step == "play" and $TeRx == "private"){
SendMessage($pV_iD,"
⚖ قوانین بازی دو نفره


1⃣ - استفاده از کلمات رکیک در گفتگو با فرد ممنوع میباشد.

2⃣ - تقلب ممنوع میباشد.

3⃣ - در صورت باخت حق اعتراض به بازیکن مقابل را ندارید

💣 $namebot | $date / $time

",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
elseif($JuStLoVe =="📚 راهنما" and $step == "play" and $TeRx == "private"){
SendMessage($pV_iD,"
📚 راهنما بازی دو نفره

1⃣ - با استفاده از بخش گفتگو کردن با بازیکن مقابل گفتگو کنید.

2⃣ - اگر فرد مقابل از کلمات رکیک استفاده کرد از بخش گزارش آن را گزارش کنید.

3⃣ - در صورتی قصد خارج شدن از بخش دو نفره را دارید بر روی قطع اتصال بزنید

4⃣ - جهت شروع بازی از بخش بریم بازی اقدام کنید

💣 $namebot | $date - $time
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
elseif($JuStLoVe =="📛گزارش بازیکن" and $step == "play" and $TeRx == "private"){
$ReQhV["step"] = "report";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
📛لطفاً دلیل گزارش را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}
if($step == "report" and $JuStLoVe !="✅ قبول کردن" and $JuStLoVe !="❌ رد کردن" and $JuStLoVe !="📩 گفتگو کردن" and $JuStLoVe !="🔙 بازگشت" and $JuStLoVe !="✅ تایید درخواست" and $JuStLoVe !="❌ رد درخواست" and $JuStLoVe !="🔙 برگشت" and $JuStLoVe !="❌ قطع اتصال" and $JuStLoVe !="⚖ قوانین" and $JuStLoVe !="📚 راهنما" and $JuStLoVe !="🎮 بریم بازی" and $JuStLoVe !="📛گزارش بازیکن" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🎈 گزارش شما با موفقیت ثبت گردید و در اسرع وقت پیگیری میشود 
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
SendMessage($addi,"🔰 اطلاعات کاربری که گزارش داده : 

✅ نام : <code>$first_name</code>
✅ ای دی عددی : <code>$pV_iD</code>
✅ ای دی : <code>@$username2</code>

💯 ای دی فردی که گزارش شده : <code>$WT</code>


📑 متن گزارش :  

┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
<code>$JuStLoVe</code>
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄");
}
elseif($JuStLoVe =="🎮 بریم بازی" and $step == "play" and $TeRx == "private"){
$coin = $ReQhV["money"];
if($coin >= 1000){
$ReQhV["step"] = "gamenew";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💣 panel bet :

💰 مقدار سکه مورد نظر خود را ارسال کنید حداقل 1,000 سکه میباشد . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "play";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
⚠️ خطا ❗️

❌ موجودی شما کافی نمیباشد حداقل موجودی برای بازی 1,000 سکه است 
",$button_play);exit();
}}
elseif($step == "gamenew" and is_numeric($JuStLoVe) and $TeRx == "private"){
$coin = $ReQhV["money"];
if($JuStLoVe >= 1000 and $JuStLoVe <= 1000000 and $coin >= $JuStLoVe){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
file_put_contents("chat/$WT/coingame.txt",$JuStLoVe);
SendMessage($pV_iD,"
✅ درخواست بازی ارسال شد . . . 
");
$PlayerOff = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$ReQhV["warn"] = $PlayerOff['warn'];
$ReQhV["saham"] = $PlayerOff['saham'];
$ReQhV["game"] = $PlayerOff['game'];
$ReQhV["subset"] = $PlayerOff['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = $PlayerOff['money'];
$ReQhV["step"] = "dar";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
SendMessage($WT,"
✅ درخواست بازی برای ( <code>$JuStLoVe</code> ) نیترو آیا درخواست را می‌پذیرید ؟
",json_encode(['keyboard'=>[
[['text'=>"✅ قبول کردن"],['text'=>"❌ رد کردن"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"❌ لطفاً مبلغ بازی را بین 1,000 تا 1,000,000 هزار نیترو سین وارد کنید با توجه به موجودی خود . . . ",$button_play);
}}
if($JuStLoVe =="✅ قبول کردن" and $step == "dar" and $TeRx == "private"){
$coinX = file_get_contents("chat/$pV_iD/coingame.txt");
$coinplay = $ReQhV["money"];
if($coinplay >= $coinX){
$ReQhV["step"] = "nobat";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
@$wincoin = file_get_contents("chat/$pV_iD/coingame.txt");
file_put_contents("chat/$pV_iD/wincoin.txt",$wincoin);
unlink("chat/$pV_iD/select.txt");
unlink("chat/$WT/select.txt");
SendMessage($WT,"
✅ کاربر مقابل درخواست شما را پذیرفت . . . 

🔰 « نوبت حریف است . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
SendMessage($pV_iD,"
✅ درخواست بازی با موفقیت پذیرفته شد 

یکی از گزینه های زیر را انتخاب کنید 👇
",$button_sang);
}else{
SendMessage($pV_iD,"
❌ موجودی شما برای انجام این بازی کافی نیست . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

if($JuStLoVe =="❌ رد کردن" and $step == "dar" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
SendMessage($pV_iD,"
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
elseif($JuStLoVe =="🖐🏻" and $step == "nobat" and $TeRx == "private"){
@$NOR = file_get_contents("chat/$pV_iD/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
file_put_contents("chat/$pV_iD/select.txt",kaqaz);
$PlayerOff = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$ReQhV["warn"] = $PlayerOff['warn'];
$ReQhV["saham"] = $PlayerOff['saham'];
$ReQhV["game"] = $PlayerOff['game'];
$ReQhV["subset"] = $PlayerOff['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = $PlayerOff['money'];
$ReQhV["step"] = "nobat2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

انتخاب شما : $JuStLoVe

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
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($JuStLoVe =="👊🏻" and $step == "nobat" and $TeRx == "private"){
@$NOR = file_get_contents("chat/$pV_iD/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
file_put_contents("chat/$pV_iD/select.txt",sang);
$PlayerOff = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$ReQhV["warn"] = $PlayerOff['warn'];
$ReQhV["saham"] = $PlayerOff['saham'];
$ReQhV["game"] = $PlayerOff['game'];
$ReQhV["subset"] = $PlayerOff['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = $PlayerOff['money'];
$ReQhV["step"] = "nobat2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

انتخاب شما : $JuStLoVe

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
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($JuStLoVe =="✌️🏻" and $step == "nobat" and $TeRx == "private"){
@$NOR = file_get_contents("chat/$pV_iD/select.txt");
if($NOR != true){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
file_put_contents("chat/$pV_iD/select.txt",gechi);
$PlayerOff = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$ReQhV["warn"] = $PlayerOff['warn'];
$ReQhV["saham"] = $PlayerOff['saham'];
$ReQhV["game"] = $PlayerOff['game'];
$ReQhV["subset"] = $PlayerOff['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = $PlayerOff['money'];
$ReQhV["step"] = "nobat2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

انتخاب شما : $JuStLoVe

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
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}
elseif($JuStLoVe =="✌️🏻 قیچی" and $step == "nobat2" and $TeRx == "private"){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
@$NEX = file_get_contents("chat/$pV_iD/select.txt");
if($NEX != true){
file_put_contents("chat/$pV_iD/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "gechi"){
SendMessage($pV_iD,"
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
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 👊🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] - $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : ✌️🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
}else{
if($fun == "kaqaz"){
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 🖐🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] + $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : ✌️🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] - $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);

}
}
}
}else{
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($JuStLoVe =="👊🏻 سنگ" and $step == "nobat2" and $TeRx == "private"){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
@$NEX = file_get_contents("chat/$pV_iD/select.txt");
if($NEX != true){
file_put_contents("chat/$pV_iD/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "sang"){
SendMessage($pV_iD,"
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
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 🖐🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] - $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : 👊🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
}else{
if($fun == "gechi"){
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : ✌️🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] + $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 👊🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] - $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);

}
}
}
}else{
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

elseif($JuStLoVe =="🖐🏻 کاغذ" and $step == "nobat2" and $TeRx == "private"){
@$WT = file_get_contents("chat/$pV_iD/RT.txt");
@$NEX = file_get_contents("chat/$pV_iD/select.txt");
if($NEX != true){
file_put_contents("chat/$pV_iD/select.txt",none);
$fun = file_get_contents("chat/$WT/select.txt");
$newcoin = file_get_contents("chat/$WT/wincoin.txt");
if($fun == "kaqaz"){
SendMessage($pV_iD,"
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
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : ✌️🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] - $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : ✌️🏻
🔰 » انتخاب حریف : 🖐🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] + $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);
}else{
if($fun == "sang"){
SendMessage($pV_iD,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 🖐🏻
🔰 » انتخاب حریف : 👊🏻

✅ » شما مقدار ( <code>$newcoin</code> ) سکه بردید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$ReQhV = json_decode(file_get_contents("InFoDaTa/$pV_iD/$pV_iD.json"),true);
$coin = $ReQhV['money'] + $newcoin;
$ReQhV["warn"] = $ReQhV['warn'];
$ReQhV["saham"] = $ReQhV['saham'];
$ReQhV["game"] = $ReQhV['game'];
$ReQhV["subset"] = $ReQhV['subset'];
$ReQhV["id"] = "$pV_iD";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($WT,"
💣 » Scissors Paper Rock :

🔰 » انتخاب شما : 👊🏻
🔰 » انتخاب حریف : 🖐🏻

❌ » شما مقدار ( <code>$newcoin</code> ) سکه باختید
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
$PlayerTwo = json_decode(file_get_contents("InFoDaTa/$WT/$WT.json"),true);
$coin = $PlayerTwo['money'] - $newcoin;
$ReQhV["warn"] = $PlayerTwo['warn'];
$ReQhV["saham"] = $PlayerTwo['saham'];
$ReQhV["game"] = $PlayerTwo['game'];
$ReQhV["subset"] = $PlayerTwo['subset'];
$ReQhV["id"] = "$WT";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$WT/$WT.json",$OoOtJsSsOn);

}
}
}
}else{
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]));
}}

if($JuStLoVe =="🔙 برگشت" and $TeRx == "private"){
$WT = file_get_contents("chat/$pV_iD/RT.txt");
if($WT == true){
$ReQhV["step"] = "play";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
♻️ به منوی بازی دو نفره بازگشتید
",$button_paly);
}else{
SendMessage($pV_iD,"
❌ ارتباط شما با کاربر مورد نظر قطع شده است روی | 🔙 بازگشت | بزنید . . .
",json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]));
}}


 // [ ADMIN ] //
if($JuStLoVe =="/admin" or $JuStLoVe == "/panel" or $JuStLoVe == "✨بازگشت✨" ){
if(in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"✅ادمین عزیز به پنل مدیریت خوش اومدی 🙃",$button_admin);
}}

if($JuStLoVe =="آمار 📊" and in_array($pV_iD,$admin) and $TeRx == "private"){
$alluser = file_get_contents("InFoDaTa/datas.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
SendMessage($pV_iD,"

🚀 تعداد کاربران : $allusers 🚀


");
}
if($JuStLoVe =="آمار 📊" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
🚀 تعداد کاربران بن شده : $ben

");
}
if($JuStLoVe =="ارسال همگانی ✨" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "rall";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفا پیام خود را جهت ارسال بفرستید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="rall" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ارسال شد.");
$all_member = fopen( "InFoDaTa/datas.txt", 'r');
while( !feof( $all_member)) {
$user = fgets( $all_member);
SendMessage($user,"$JuStLoVe");
}}
if($JuStLoVe =="فوروارد همگانی ❄️" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "forall";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفا پیام خود را جهت فوروارد ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="forall" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"فوروارد شد.");
$all_member = fopen( "InFoDaTa/datas.txt", 'r');
while( !feof( $all_member)) {
$user = fgets( $all_member);
SendMessage($user,"$JuStLoVe");
RfTvKj('ForwardMessage',[
'chat_id'=>$user,
'from_chat_id'=>$pV_iD,
'message_id'=>$message_id
]);
}    
}

if($JuStLoVe =="پیام به کاربر 📨" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "pm";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="pm" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PPMM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ♻️ پیام خود را جهت ارسال پیام به کاربر <code>$JuStLoVe</code> ارسال کنید . . .
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PPMM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($b[1],"
🎉 کاربر گرامی شما یک پیام از طرف مدیر دارید ❗️
📥 پیام :
$JuStLoVe");
SendMessage($pV_iD,"
✅ پیام شما ( <code>$JuStLoVe</code> ) با موفقیت به کاربر ( <code>$b[1]</code> ) ارسال شد
",$button_admin);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
}
if($JuStLoVe =="کسر سکه 🥈" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "coinoff";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="coinoff" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PcoinoffM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰 تعداد سکه جهت کسر سکه از کاربر <code>$JuStLoVe</code> را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PcoinoffM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
SendMessage($pV_iD,"
✅ با موفقیت از حساب کاربری <code>$b[1]</code> مقدار <code>$JuStLoVe</code> سکه کسر شد
",$button_admin);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$usersharj = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$coin = $usersharj['money'] - $JuStLoVe;
 SendMessage($b[1],"
💵 از حساب کاربری شما مقدار $JuStLoVe سکه کم شد 
");
$ReQhV["warn"] = $usersharj['warn'];
$ReQhV["saham"] = $usersharj['saham'];
$ReQhV["game"] = $usersharj['game'];
$ReQhV["subset"] = $usersharj['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = "$coin";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
}
if($JuStLoVe =="افزایش سکه 🥇" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "coinon";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="coinon" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PcoinonM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," 💰 تعداد سکه جهت افزایش سکه کاربر <code>$JuStLoVe</code> را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PcoinonM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ با موفقیت , حساب کاربری <code>$b[1]</code> مقدار <code>$JuStLoVe</code> سکه شارژ شد
",$button_admin);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$usersharj = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$coin = $usersharj['money'] + $JuStLoVe;
$coinsharj = $coin + $bonos4;
$coinazafe = $JuStLoVe + $bonos4;
 SendMessage($b[1],"
💵 حساب کاربری شما مقدار $coinazafe سکه شارژ شد.
");
$ReQhV["warn"] = $usersharj['warn'];
$ReQhV["saham"] = $usersharj['saham'];
$ReQhV["game"] = $usersharj['game'];
$ReQhV["subset"] = $usersharj['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = "$coinsharj";
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
}
if($JuStLoVe =="❗️اخطار به کاربر" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "warnos";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="warnos" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PwarnosM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," 💯 تعداد اخطار را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PwarnosM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
❗️ $JuStLoVe اخطار به کاربر $b[1] اضافه شد . . .
",$button_admin);
$usersharj = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$warnNew = $usersharj['warn'] + $JuStLoVe;
 SendMessage($b[1],"
❗️اوپس ! 
شما تعداد $JuStLoVe اخطار دریافت کردید . . .
");
SendMessage($pV_iD,"
❗️ تعداد اخطار های کاربر : $warnNew
",$button_admin);
$ReQhV["warn"] = "$warnNew";
$ReQhV["saham"] = $usersharj['saham'];
$ReQhV["game"] = $usersharj['game'];
$ReQhV["subset"] = $usersharj['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = $usersharj['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
}
if($JuStLoVe =="❕حذف اخطار کاربر" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "warnoff";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="warnoff" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PwarnoffM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," 💯 تعداد اخطار را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PwarnoffM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
❗️ $JuStLoVe تعداد اخطار از کاربر $b[1] کم شد . . .
",$button_admin);
$usersharj = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$warnNew = $usersharj['warn'] - $JuStLoVe;
 SendMessage($b[1],"
❕اوپس ! 
شما تعداد $JuStLoVe اخطار از شما کم شد . . .
");
SendMessage($pV_iD,"
❗️ تعداد اخطار های کاربر : $warnNew
",$button_admin);
$ReQhV["warn"] = "$warnNew";
$ReQhV["saham"] = $usersharj['saham'];
$ReQhV["game"] = $usersharj['game'];
$ReQhV["subset"] = $usersharj['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = $usersharj['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
}

if($JuStLoVe =="بلاک کردن 🍂" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bl";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"آیدی عددی فرد مورد نظر را ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="bl" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$userWarn = json_decode(file_get_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json"),true);
$userWarn["warn"] = "4";
$userWarn["step"] = "bon";
$OoOtJsSsOn = json_encode($userWarn,true);
file_put_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json",$OoOtJsSsOn);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$newben = $ben + 1;
file_put_contents("InFoDaTa/ben.txt", $newben);
SendMessage($pV_iD,"بلاک شد",$button_admin);
}

if($JuStLoVe =="آنبلاک کردن ⭐" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "obl";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"آیدی عددی فرد مورد نظر را ارسال نمایید:",json_encode(['keyboard'=>[[['text'=>"✨بازگشت✨"]],],'resize_keyboard'=>true]));
}
if($step =="obl" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$userWarn = json_decode(file_get_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json"),true);
$userWarn["warn"] = "0";
$userWarn["step"] = "none";
$OoOtJsSsOn = json_encode($userWarn,true);
file_put_contents("InFoDaTa/$JuStLoVe/$JuStLoVe.json",$OoOtJsSsOn);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$newben = $ben - 1;
file_put_contents("InFoDaTa/ben.txt", $newben);
SendMessage($pV_iD,"آنبلاک شد",$button_admin);
}
// Set dok ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="🍁 بخش تنظیم دکمه ها 🍁" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم دکمه ها خوش آمدید 🎉 


پس از تغییر اسم دکمه ها ربات رو مجدد /start کنید 🔐
",$button_dok);
}
if($JuStLoVe =="📊 دکمه های فعلی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "free";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌹 لیست اسم دکمه های فعلی : 

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

•••{ $creator }•••");
}
if($JuStLoVe =="دکمه 💣 ثبت بازی جدید" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok1" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok1.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 📊 پیش‌بینی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok2" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok2.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🔐 حساب کاربری" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok3";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok3" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok3.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🆘 پشتیبانی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok4";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok4" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok4.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 💡راهنما" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok5";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok5" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok5.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🎗سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok6";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok6" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok6.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه قوانین ربات 📑" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok7";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok7" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok7.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه ارسال اکانت 💫" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok8";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok8" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok8.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 💸 بونوس" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok9";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok9" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok9.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 💵 شارژ حساب" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok10";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok10" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok10.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 💰 برداشت موجودی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok11";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok11" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok11.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه گفت و گو 🔰" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok12";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok12" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok12.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 💰 انتقال سکه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok13";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok13" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok13.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🎁 برترین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok14";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok14" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok14.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🥂 وضعیت ربات" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok15";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok15" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok15.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
} 
if($JuStLoVe =="دکمه 📜 تاریخچه بازی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok16";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok16" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok16.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🔥 تراکنش ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok17";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok17" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok17.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه ☁️ چت با اعضا ربات" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok18";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok18" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok18.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه 🎉 هدیه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok19";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok19" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok19.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
if($JuStLoVe =="دکمه ♻️ آپدیت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dok20";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را وارد کنید 🍀");
} 
if($step =="dok20" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("button/dok20.txt","$JuStLoVe");
SendMessage($pV_iD,"🌷 اسم دکمه با موفقیت به <code>$JuStLoVe</code> تغییر یافت ✨");
}
    
     // Set up admin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
 
if($JuStLoVe =="بخش ادمین ها 🔋" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش حذف و اضافه کردن ادمین ها خوش آمدید 🎉
",$button_add);
}
if($JuStLoVe =="📊 آمار ادمین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"📊 لیست ادمین ها : 
<code>𝟏 - $admin1</code>
<code>𝟐 - $admin2</code>
<code>𝟑 - $admin3</code>

مالک ربات : ♕ <code>$owner</code> ♕
",$button_add);
}
if($JuStLoVe =="✅ اضافه کردن ادمین 1" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
if($step =="admin1" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin1.txt","$JuStLoVe");
SendMessage($pV_iD,"🎊 ادمین 1 با موفقیت اضافه شد");
}
if($JuStLoVe =="✅ اضافه کردن ادمین 2" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
if($step =="admin2" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin2.txt","$JuStLoVe");
SendMessage($pV_iD,"🎊 ادمین 2 با موفقیت اضافه شد");
}
if($JuStLoVe =="✅ اضافه کردن ادمین 3" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin3";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🥂 ای دی عددی ادمین جدید را وارد کنید");
} 
if($step =="admin3" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin3.txt","$JuStLoVe");
SendMessage($pV_iD,"🎊 ادمین 3 با موفقیت اضافه شد");
}
    
if($JuStLoVe =="❌ حذف ادمین 1" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin-1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"برای حذف ادمین 1 ای دی عددی آنرا وارد کنید ❌");
} 
if($step =="admin-1" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin1.txt",❌);
SendMessage($pV_iD,"🗑 ادمین 1 با موفقیت حذف شد");
}
if($JuStLoVe =="❌ حذف ادمین 2" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin-2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"برای حذف ادمین 2 ای دی عددی آنرا وارد کنید ❌");
} 
if($step =="admin-2" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin2.txt",❌);
SendMessage($pV_iD,"🗑 ادمین 2 با موفقیت حذف شد");
}
if($JuStLoVe =="❌ حذف ادمین 3" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "admin-3";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"برای حذف ادمین 3 ای دی عددی آنرا وارد کنید ❌");
} 
if($step =="admin-3" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("admin/admin3.txt",❌);
SendMessage($pV_iD,"🗑 ادمین 3 با موفقیت حذف شد");
} 
// finished Set up admin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••  
//  Set up Zarib ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="🍭 تنظیم ضریب ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"🍀 به بخش تنظیمات ضریب ها خوش آمدید . . . 
",$button_zarib);
}
if($JuStLoVe =="ضریب 🎯 دارت" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم ضریب های🎯 دارت خوش آمدید 🌷
",$button_dartset);
}
if($JuStLoVe =="ضریب 🎲 تاس" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم ضریب های 🎲 تاس خوش آمدید 🌷
",$button_taset);
}
if($JuStLoVe =="ضریب 🏀 بسکتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "basketball";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="basketball" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/basketball.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب ⚽️ فوتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "football";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="football" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/football.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎳 بولینگ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bolingl";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="bolingl" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/boling.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب ✊ گل یا پوچ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "gool";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="gool" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/gool.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🔢 مونتی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "mon";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="mon" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/mon.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 💩 پوپ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "poop";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="poop" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/poop.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎱 رولت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "rolet";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="rolet" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/rolet.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 👊🏻 سنگ کاغذ قیچی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "Paper";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="Paper" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/Paper.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎲 تاس بالا و پایین" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "tasupd";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="tasupd" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/tasupd.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎲 تاس فرد و زوج" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "tasfard";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="tasfard" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/tasfard.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎲 حدس عدد تاس" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "tashad";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="tashad" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/tashad.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎯 دارت وسط" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dartV";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="dartV" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/dartV.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎯 دارت سفید" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dartSS";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="dartSS" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/dartSS.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎯 دارت قرمز" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dartGH";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="dartGH" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/dartGH.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎯 دارت بیرون" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "dartBI";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="dartBI" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/dartBI.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
if($JuStLoVe =="ضریب 🎰 ماشین اسلات" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "skat";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌱 ضریب جدید را وارد کنید . . .");
} 
if($step =="skat" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("zarib/skat.txt","$JuStLoVe");
SendMessage($pV_iD,"📊 ضریب با موفقیت به <code>$JuStLoVe</code> تغییر کرد ♻️");
}
    // finished Set up Zarib •••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••• 

if($JuStLoVe =="انتقال مالکیت ربات 📤" and in_array($pV_iD,$admin) and $TeRx == "private"){
if ($pV_iD == $owner) {
$ReQhV["step"] = "owner1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ای دی عددی مالک جدید را وارد کنید 🎈 


📨 جهت انتقال مالکیت پنل مدیریت به کاربر دیگر نمیتوانید آن را عزل کنید و کسی هم نمی‌تواند او را عزل کند ❗️
و تمام ادمین های فعلی عزل میشوند 🎈
",@$button_back);
}else{
    SendMessage($pV_iD,"❌ شما مالک اصلی ربات نیستید و نمی توانید انتقال مالکیت ربات را به شخص دیگری بدهید ",$button);
}}
	elseif($step == "owner1" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
file_put_contents("admin/owner.txt","$JuStLoVe");
SendMessage($pV_iD,"🎉 تبریک , کاربر <code>$JuStLoVe</code> مالک ربات تنظیم شد ❕",$button);
SendMessage($JuStLoVe,"✅ تبریک , شما مالک ربات شدید 🥳",$button_admin);
}else{
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست",$button);
}}
    
   // Set up coin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="⚙ تنظیمات سکه" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم ای دی های خوش آمدید 🎉
",$button_coin);
}
if($JuStLoVe =="♻️ ریست تنظیمات سکه" and in_array($pV_iD,$admin) and $TeRx == "private"){
unlink("coin/meqdar.txt");
unlink("coin/bardasht.txt");
unlink("coin/bardashtX.txt");
unlink("coin/zir.txt");
unlink("coin/poof.txt");
unlink("coin/sharj.txt");
unlink("coin/get_member.txt");
unlink("coin/speed_member.txt");
SendMessage($pV_iD,"
✅ با موفقیت تنظیمات سکه ریست شد
و تمام تنظیمات سکه به حالت پیشفرض در آمدند  
",$button_coin);
}
if($JuStLoVe =="💰 تنظیم تعرفه سرعت ممبر" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "speed_member";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی تعرفه هر 1,000 سکه سرعت ممبر را وارد کنید ❗️");
} 
if($step == "speed_member" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/speed_member.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم تعرفه جت ممبر" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "get_member";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی تعرفه هر 1,000 سکه جت ممبر را وارد کنید ❗️");
} 
if($step == "get_member" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/get_member.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم حداقل سکه برای شرط" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "meqdar";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار حداقل سکه برای شرط را وارد کنید ❗️");
} 
if($step == "meqdar" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/meqdar.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم حداقل سکه برای برداشت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bardasht";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار حداقل سکه برای برداشت موجودی را وارد کنید ❗️");
} 
if($step == "bardasht" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/bardasht.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم حداکثر سکه برای برداشت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bardashtX";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار حداکثر سکه برای برداشت موجودی را وارد کنید ❗️");
} 
if($step == "bardashtX" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/bardashtX.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم حداقل سکه جهت انتقال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "poof";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار حداقل سکه جهت انتقال موجودی را وارد کنید ❗️");
} 
if($step == "poof" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/poof.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
if($JuStLoVe =="💰 تنظیم سکه زیر مجموعه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "zir";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار سکه زیر مجموعه گیری را وارد کنید ❗️");
} 
if($step == "zir" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/zir.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
   if($JuStLoVe =="💰 تنظیم سکه حداقل شارژ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "sharj";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"بصورت عدد انگلیسی مقدار حداقل سکه شارژ حساب را وارد کنید ❗️");
} 
if($step == "sharj" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("coin/sharj.txt","$JuStLoVe");
SendMessage($pV_iD,"تعداد سکه با موفقیت به <code>$JuStLoVe</code> سکه تغییر کرد 🥀
▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬▭▬");
}
// finished Set up coin ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
// Set up ID ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="🆔 تنظیم ای دی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم ای دی های خوش آمدید 🎉
",$button_id);
}
if($JuStLoVe =="🥂 تنظیم ای دی پشتیبانی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "poshtiban";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ای دی پشتیبانی را بدون @ ارسال کنید ❗️");
} 
if($step =="poshtiban" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/poshtiban.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
if($JuStLoVe =="🥂 تنظیم ای دی کانال بازی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "shart";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ای دی کانال بازی های شرط بندی را با @ وارد کنید ❗️");
} 
if($step =="shart" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/shart.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
if($JuStLoVe =="🥂 تنظیم کانال اول" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "channel";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ای دی کانال اول را بدون @ ارسال کنید ❗️");
} 
if($step =="channel" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("channel.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
if($JuStLoVe =="🥂 تنظیم کانال دوم" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "channel2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"ای دی کانال دوم را بدون @ ارسال کنید ❗️");
} 
if($step =="channel2" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("channel2.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
if($JuStLoVe =="🥂 تنظیم ای دی گفت و گو" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "gap";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اگه گپ شما خصوصی است لینک را بدون https://t.me ارسال کنید ❗️

در غیر این صورت ای دی گپ رو بدون @ ارسال کنید ❕");
} 
if($step =="gap" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/gap.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی با موفقیت به 
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
@$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
if($JuStLoVe =="🥂 تنظیم ای دی عددی برای شارژ حساب" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "addi";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"لطفاً ای دی عددی خود را بصورت کامل ارسال کنید ❗️");
} 
if($step =="addi" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/addi.txt","$JuStLoVe");
SendMessage($pV_iD,"🌱 ای دی عددی برای واریز نیترو با موفقیت به
╭╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╴╮
$JuStLoVe
╰╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╼╾
تغییر کرد 😜");
}
// finished Set up ID ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••

if($JuStLoVe =="🔑 چیدمان دکمه ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
♻️ یک نوع از چیدمان های زیر را انتخاب کنید . . .
",$button_lol);
}
if($JuStLoVe =="❤️ چیدمان نوع اول ❤️" and in_array($pV_iD,$admin) and $TeRx == "private"){
file_put_contents("menu.txt",$button1);
SendMessage($pV_iD,"
✅ با موفقیت $JuStLoVe تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($JuStLoVe =="🧡 چیدمان نوع دوم 🧡" and in_array($pV_iD,$admin) and $TeRx == "private"){
file_put_contents("menu.txt",$button2);
SendMessage($pV_iD,"
✅ با موفقیت $JuStLoVe تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($JuStLoVe =="💚 چیدمان نوع سوم 💚" and in_array($pV_iD,$admin) and $TeRx == "private"){
file_put_contents("menu.txt",$button3);
SendMessage($pV_iD,"
✅ با موفقیت $JuStLoVe تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($JuStLoVe =="💛 چیدمان نوع چهارم 💛" and in_array($pV_iD,$admin) and $TeRx == "private"){
file_put_contents("menu.txt",$button4);
SendMessage($pV_iD,"
✅ با موفقیت $JuStLoVe تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
if($JuStLoVe =="💜 چیدمان نوع پنجم 💜" and in_array($pV_iD,$admin) and $TeRx == "private"){
file_put_contents("menu.txt",$button5);
SendMessage($pV_iD,"
✅ با موفقیت $JuStLoVe تنظیم شد 🌹 برای اعمال این تغییر برای تمام کاربران باید همه کاربران یک بار ربات رو مجدد /start کنند . . .
",$button);
}
// Set texts ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="تنظیم متن ها 🎈" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"به بخش تنظیم متن ها خوش آمدید 🎉
",$button_matn);
}
if($JuStLoVe =="$dok9" and $TeRx == "private"){
SendMessage($pV_iD,"$bonos
");
}
if($JuStLoVe =="تنظیم متن بونوس" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bonos";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="bonos" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bonos.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="$dok12" and $TeRx == "private"){
SendMessage($pV_iD,"$Conversation

",json_encode(['inline_keyboard' => [[['text' => "گفت و گو 📩",'url' => "https://t.me/$gap"]],]]));exit();
}
if($JuStLoVe =="تنظیم متن گفت و گو" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "Conversation";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="Conversation" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/Conversation.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="$dok7" and $TeRx == "private"){
SendMessage($pV_iD,"$Law

@$botid");
}
if($JuStLoVe =="تنظیم متن قوانین" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "Law";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="Law" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/Law.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="$dok6" and $TeRx == "private"){
SendMessage($pV_iD,"$saham

📩 پشتیبانی سوالات شما : @$poshtiban


",$button_f2);
}
if($JuStLoVe =="تنظیم متن سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "saham";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="saham" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/saham.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="$dok5" and $TeRx == "private"){
SendMessage($pV_iD,"$help

@$botid");
}
if($JuStLoVe =="تنظیم متن راهنما" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "help";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="help" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/help.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($JuStLoVe =="$dok8" and $TeRx == "private"){
SendMessage($pV_iD,"
$accReceiver
",json_encode(['inline_keyboard' => [[['text' => "$dok8",'url' => "https://telegram.me/$poshtiban"]],]]));exit();
}
if($JuStLoVe =="تنظیم متن ارسال اکانت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "accReceiver";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="accReceiver" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/accReceiver.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="تنظیم برترین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"یکی از گزینه های زیر را انتخاب کنید 👇
",$button_bartar);
}
if($JuStLoVe =="تنظیم نفر اول برترین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bartar1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="bartar1" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bartar1.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="تنظیم نفر دوم برترین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bartar2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="bartar2" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bartar2.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="تنظیم نفر سوم برترین ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bartar3";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"متن جدید را بفرستید");
} 
if($step =="bartar3" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bartar3.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="$dok14" and $TeRx == "private"){
SendMessage($pV_iD,"

🎁 نفر اول | $bartar1

🎁نفر دوم | $bartar2

🎁 نفر سوم | $bartar3

");
}
// Set  subset ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••

if($JuStLoVe =="👤 تنظیمات زیر مجموعه" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
سلام به بخش تنظیمات زیر مجموعه خوش آمدید 🎉 
",$button_zir);
}

if($JuStLoVe =="🌄 تنظیم عکس زیر مجموعه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "link";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🌄 لینک عکس زیر مجموعه را ارسال کنید");
} 
if($step =="link" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$filephoto = $update->message->photo;
$pHoTo = $filephoto[count($filephoto)-1]->file_id;
if(isset($pHoTo)){
file_put_contents("subset/link.txt","$pHoTo");
RfTvKj('SendPhoto',[
'chat_id'=>$pV_iD,
'photo'=>"$pHoTo",
'caption'=>"
✅ عکس بنر زیر مجموعه با موفقیت تغییر پیدا کرد 🥳
",]);
}else{
file_put_contents("subset/link.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ لینک عکس بنر زیر مجموعه با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
}
if($JuStLoVe =="📝 تنظیم متن اصلی بنر زیر مجموعه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "mtn";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🔰 متن جدید را بفرستید

💯 میتوانید از 

🔗 لینک : BANER
📌 اسم : NAME
🔐 اسم ربات : BOT
📆 تاریخ : DATE
⏳ زمان : TIME
🔰 ای دی عددی : ID
💰 سکه زیر مجموعه : COIN

هم استفاده کنید در متن خود !
");
}
if($step =="mtn" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("subset/mtn.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}
if($JuStLoVe =="🔖 تنظیم متن زیر بنر اصلی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "mtnzir";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
🔰 متن جدید را بفرستید

💯 میتوانید از 

🔗 لینک : BANER
📌 اسم : NAME
🔐 اسم ربات : BOT
📆 تاریخ : DATE
⏳ زمان : TIME
🔰 ای دی عددی : ID
💰 سکه زیر مجموعه : COIN

هم استفاده کنید در متن خود !
");
} 
if($step =="mtnzir" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("subset/mtnzir.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ متن با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($JuStLoVe =="🎉 تنظیم اسم دکمه زیر مجموعه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "doksubset";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"اسم جدید دکمه را ارسال کنید");
} 
if($step =="doksubset" and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("subset/doksubset.txt","$JuStLoVe");
SendMessage($pV_iD,"✅ اسم دکمه زیر مجموعه با موفقیت به
╔═══════════════════════════════╗
 $JuStLoVe
╚═══════════════╣⫸
 تغییر پیدا کرد 🥳");
}

if($JuStLoVe =="🎁 تنظیمات بونوس" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
به بخش تنظیمات بونوس خوش آمدید 🎉
",$button_bons);
}
if($JuStLoVe =="🎁 تنظیم حداقل بونوس هدیه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bonos1";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد حداقل سکه رندوم بونوس هدیه را وارد کنید ❗️");
} 
if($step =="bonos1" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bonos1.txt","$JuStLoVe");
SendMessage($pV_iD,"🎉 حداقل سکه رندوم بونوس هدیه به $JuStLoVe سکه تغییر کرد");
}
if($JuStLoVe =="🎁 تنظیم حداکثر بونوس هدیه" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bonos2";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد حداکثر سکه رندوم بونوس هدیه را وارد کنید ❗️");
} 
if($step =="bonos2" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bonos2.txt","$JuStLoVe");
SendMessage($pV_iD,"🎉 حداکثر سکه رندوم بونوس هدیه به $JuStLoVe سکه تغییر کرد");
}
if($JuStLoVe =="🎁 تنظیم بونوس خوش آمد گویی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bonos3";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🎁 تعداد سکه بونوس خوش آمد گویی را وارد کنید بصورت عدد انگلیسی . . .");
} 
if($step =="bonos3" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bonos3.txt","$JuStLoVe");
SendMessage($pV_iD,"🎉 بونوس خوش آمد گویی به $JuStLoVe سکه تغییر کرد");
}
if($JuStLoVe =="🎁 تنظیم بونوس شارژ بیشتر" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "bonos4";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"تعداد سکه بونوس شارژ بیشتر را وارد کنید ❗️");
} 
if($step =="bonos4" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("text/bonos4.txt","$JuStLoVe");
SendMessage($pV_iD,"🎉 بونوس شارژ بیشتر به $JuStLoVe سکه تغییر کرد");
}
// Set  off and on ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="💡 خاموش و روشن بازی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
❌ شما قبلا انتخاب خود را کردید . . .
",json_encode(['keyboard'=>[
[['text'=>"✅ روشن کردن بازی ها"],['text'=>"💤 خاموش کردن بازی ها"]],
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
if($JuStLoVe =="✅ روشن کردن بازی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"✅ به بخش تنظیمات روشن کردن بازی ها خوش آمدید . . .
",$button_on);
}
if($JuStLoVe =="💤 خاموش کردن بازی ها" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"💤 به بخش تنظیمات خاموش کردن بازی ها خوش آمدید . . .
",$button_off);
}
if($JuStLoVe =="روشن کردن بازی 🃏 21" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/card21.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🏀 بسکتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/basket.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🔢 مونتی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/moont.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 💣 انفجار" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/enfejar.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی ✊ گل یا پوچ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/gooli.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی ⚽️ فوتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/footi.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🎳 بولینگ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/boli.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 👊🏻 سنگ کاغذ قیچی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/sangi.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 💩 پوپ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/popi.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🎱 رولت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/rolet.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🎰 ماشین اسلات" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/skati.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🎲 تاس" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/tasi.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="روشن کردن بازی 🎯 دارت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
unlink("onof/darti.txt");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت روشن شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🃏 21" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/card21.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🏀 بسکتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/basket.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🔢 مونتی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/moont.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 💣 انفجار" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/enfejar.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی ✊ گل یا پوچ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/gooli.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی ⚽️ فوتبال" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/footi.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🎳 بولینگ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/boli.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 👊🏻 سنگ کاغذ قیچی" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/sangi.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 💩 پوپ" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/popi.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🎱 رولت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/rolet.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🎰 ماشین اسلات" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/skati.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🎲 تاس" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/tasi.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
if($JuStLoVe =="خاموش کردن بازی 🎯 دارت" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("onof/darti.txt","off");
SendMessage($pV_iD,"✅ بازی مورد نظر با موفقیت خاموش شد");
} 
// Set saham ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if($JuStLoVe =="❄️ تنظیمات سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){
SendMessage($pV_iD,"
به بخش تنظیمات سهام خوش آمدید 🎉
",$button_shm);
}
if($JuStLoVe =="🛍 قیمت خرید سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "buyshm";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🛍 قیمت خرید سهام را وارد کنید بصورت عدد انگلیسی");
} 
if($step =="buyshm" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("saham/salesaham.txt","$JuStLoVe");
SendMessage($pV_iD,"🛍 قیمت خرید سهام به $JuStLoVe سکه تغییر کرد");
}
if($JuStLoVe =="🛍 قیمت فروش سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){
$ReQhV["step"] = "seleshm";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"🛍 قیمت فروش سهام را وارد کنید بصورت عدد انگلیسی");
} 
if($step =="seleshm" and is_numeric($JuStLoVe) and $JuStLoVe !="✨بازگشت✨" and $TeRx == "private"){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
file_put_contents("saham/buysaham.txt","$JuStLoVe");
SendMessage($pV_iD,"🛍 قیمت فروش سهام به $JuStLoVe سکه تغییر کرد");
}
if($JuStLoVe =="📉 کسر سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "sahamofff";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="sahamofff" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PsahamofffM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"💰 تعداد سهام جهت کسر از کاربر <code>$JuStLoVe</code> را وارد کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PsahamofffM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
$b = explode("-",$step);
$usernone = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$arsaham = $usernone['saham'];
if($JuStLoVe <= $arsaham){
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ با موفقیت , از کاربر <code>$b[1]</code> تعداد <code>$JuStLoVe</code> سهام کسر شد
",$button_admin);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$userAddSaham = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$addsaham = $userAddSaham['saham'] - $JuStLoVe;
 SendMessage($b[1],"
💯 تعداد $JuStLoVe سهام از شما کسر شد

📊 تعداد سهام های شما : $addsaham
");
$ReQhV["warn"] = $userAddSaham['warn'];
$ReQhV["saham"] = "$addsaham";
$ReQhV["game"] = $userAddSaham['game'];
$ReQhV["subset"] = $userAddSaham['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = $userAddSaham['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
$newsam = $mix - $JuStLoVe;
file_put_contents("saham/saham.txt", "$newsam");
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
❌ کاربر $b[1] تنها $arsaham سهام دارد این تعداد سهام جهت کسر از کاربر $b[1] امکان پذیر نمیباشد
",$button_admin);
}
}
if($JuStLoVe =="📈 افزایش سهام" and in_array($pV_iD,$admin) and $TeRx == "private"){ 
$ReQhV["step"] = "sahamonn";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ ایدی عددی فرد مورد نظر را ارسال کنید
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}
elseif($step =="sahamonn" and is_numeric($JuStLoVe) and $TeRx == "private"){
if(file_exists("InFoDaTa/$JuStLoVe/$JuStLoVe.json")){
$ReQhV["step"] = "PsahamonnM-$JuStLoVe";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," 
💰 تعداد سهام جهت افزایش سهام کاربر <code>$JuStLoVe</code> را وارد کنید

🎈 تعداد سهام های باقی مانده : <code>$baqi</code>
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD," ❌  ایدی عددی کاربری که ارسال کردید در ربات نیست
",json_encode(['keyboard'=>[
[['text'=>"✨بازگشت✨"]],
],'resize_keyboard'=>true]));
}}
elseif(strpos($step, "PsahamonnM-") !== false and $JuStLoVe != "✨بازگشت✨" and $TeRx == "private"){
if($JuStLoVe <= $baqi){
$b = explode("-",$step);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
✅ با موفقیت , به کاربر <code>$b[1]</code> تعداد <code>$JuStLoVe</code> سهام اضافه شد 
",$button_admin);
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
$userAddSaham = json_decode(file_get_contents("InFoDaTa/$b[1]/$b[1].json"),true);
$addsaham = $userAddSaham['saham'] + $JuStLoVe;
 SendMessage($b[1],"
🎉 تعداد $JuStLoVe سهام به شما اضافه شد

📊 تعداد سهام های شما : $addsaham
");
$ReQhV["warn"] = $userAddSaham['warn'];
$ReQhV["saham"] = "$addsaham";
$ReQhV["game"] = $userAddSaham['game'];
$ReQhV["subset"] = $userAddSaham['subset'];
$ReQhV["id"] = "$b[1]";
$ReQhV["money"] = $userAddSaham['money'];
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$b[1]/$b[1].json",$OoOtJsSsOn);
$newsam = $mix + $JuStLoVe;
file_put_contents("saham/saham.txt", "$newsam");
}else{
$ReQhV["step"] = "none";
$OoOtJsSsOn = json_encode($ReQhV,true);
file_put_contents("InFoDaTa/$pV_iD/$pV_iD.json",$OoOtJsSsOn);
SendMessage($pV_iD,"
❌ این تعداد سهام جهت افزایش به کاربر $b[1] باقی نمانده است
",$button_admin);
}
}
// finished ••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("../../SlowCreate/UsErBoT/$botid/creator.txt")){
$creator = file_get_contents("../../SlowCreate/UsErBoT/$botid/creator.txt");
}
if($JuStLoVe =="/creator"){
SendMessage($pV_iD,"
$creator
",$button);
}

if(is_file("error_log")){
unlink("error_log");
}
?>