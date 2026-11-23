<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
error_reporting(0);
date_default_timezone_set('Asia/Tehran');
//-----------------------------------------------------------------------------------------
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
	exit(header("location: https://SnowyTeam.xyz/DcJsjxjdbd"));
	}
define('MAXTOKEN','[*[TOKEN]*]');
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".MAXTOKEN."/".$method;
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
function SM($iD_cAlLbAcK)
{
	$tab = json_decode(file_get_contents("../../tab.json"),true);
	if($tab['type'] == 'photo')
	{
		bot('sendphoto',['chat_id'=>$iD_cAlLbAcK,'photo'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'file')
	{
		bot('sendDocument',['chat_id'=>$iD_cAlLbAcK,'document'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'video')
	{
		bot('SendVideo',['chat_id'=>$iD_cAlLbAcK,'video'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'music')
	{
		bot('SendAudio',['chat_id'=>$iD_cAlLbAcK,'audio'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'sticker')
	{
		bot('SendSticker',['chat_id'=>$iD_cAlLbAcK,'sticker'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'voice')
	{
		bot('SendVoice',['chat_id'=>$iD_cAlLbAcK,'voice'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else
	{
		if($tab['reply_markup'] != null)
		{
			bot('SendMessage',['chat_id'=>$iD_cAlLbAcK,'text'=>$tab['text'],'reply_markup'=>$tab['reply_markup']]);
		}
		else
		{
			bot('SendMessage',['chat_id'=>$iD_cAlLbAcK,'text'=>$tab['text']]);
		}
	}
}
function SendPhoto($pViD,$link,$MyLoVe) {
bot('SendPhoto',['chat_id' => $pViD, 'photo' => $link, 'caption' => $MyLoVe]);
}
function sendmessage($pViD,$MyLoVe){
bot('sendMessage',['chat_id'=>$pViD,'text'=>$MyLoVe,'parse_mode'=>"html"]);
}
function save($filename, $data)
{
$file = fopen($filename, 'w');
fwrite($file, $data);
fclose($file);
}
function getChatstats($pViD,$JuStLoVe) {
  $url = 'https://api.telegram.org/bot'.$JuStLoVe.'/getChatAdministrators?chat_id=@'.$pViD;
  $result = file_get_contents($url);
  $result = json_decode ($result);
  $result = $result->ok;
  return $result;
}
function json($iDpV,$type,$what){
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
$NsdjWi[$type] = $what;
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
}
function getRanks($file){
   $users = scandir('DaTaBoT/');
   $users = array_diff($users,[".",".."]);
   $coins =[];
   foreach($users as $user){
    $coin = json_decode(file_get_contents('DaTaBoT/'.$user.'/'.$user.'.json'),true)["$file"];
    $coins[$user] = $coin;
}
   arsort($coins);
   foreach($coins as $key => $user){
   $list[] = array('user'=>$key,'coins'=>$coins[$key]);
   } 
   return $list;
}
function deletemessage($pViD,$message_id){
bot('deletemessage', ['chat_id' => $pViD,'message_id' => $message_id,]);
}

function EditMessageText($pViD,$MyLoVe,$key,$message_id){
global $bot;
$bot('EditMessageText',['chat_id'=>$pViD,'text'=>$MyLoVe,'message_id'=>$message_id,'parse_mode'=>'html','reply_markup'=>$key]);
}
//Variables
$update = json_decode(file_get_contents('php://input'));
$update->message = $update->message;
$pViD = $update->message->chat->id;
$MyLoVe = $update->message->text;
$chat = $update->message->chat->type;
$message_id = $update->message->message_id;
$first_name = $update->message->from->first_name;
$iDpV = $update->message->from->id;
$first = $update->message->from->first_name;
$last = $update->message->from->last_name;
$username = $update->message->from->username;
$first2 = $update->callback_query->message->chat->first_name;
$last2 = $update->callback_query->message->chat->last_name;
$iD_cAlLbAcK = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$message_id2 = $update->callback_query->message->message_id;
$time = date('H:i:s');
$date = date('Y-m-d');
$photo = $update->message->photo;
$admin = "[*[ADMIN]*]"; 
$channel = file_get_contents("channel.txt"); 
$JuStLoVe = MAXTOKEN; 
$stepp = file_get_contents("DaTaBoT/$iDpV/step.txt");
mkdir("DaTaBoT");
mkdir("DaTaBoT/codes");
mkdir("DaTaBoT/$iDpV");
if (!file_exists("DaTaBoT/$iDpV/$iDpV.json")){mkdir("DaTaBoT/$iDpV");}
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
$NsdjWi1 = json_decode(file_get_contents("DaTaBoT/$iD_cAlLbAcK/$iD_cAlLbAcK.json"),true);
$coin1 = $NsdjWi1["coin"];
$step = $NsdjWi["step"];
$inv = $NsdjWi["inv"];
$coin = $NsdjWi["coin"];
$type = $NsdjWi["type"];
$hoviat = $NsdjWi["hoviat"];
$mem = bot('getChatMembersCount',['chat_id'=>'@'.$MyLoVe])->result;
$al = $NsdjWi["ted"];
$sefaresh = $NsdjWi["sefaresh"];
$warn = $NsdjWi["warn"];

if(strpos($MyLoVe,"'") !== false or strpos($MyLoVe,'"') !== false or strpos($MyLoVe,"}") !== false or strpos($MyLoVe,"{") !== false or strpos($MyLoVe,")'") !== false or strpos($MyLoVe,"(") !== false or strpos($MyLoVe,",") !== false and $pViD != $admin){ 
file_put_contents("DaTaBoT/$iDpV/state.txt","none");
file_put_contents("DaTaBoT/$iDpV/step.txt","none");
$addus= $iDpV . "\n";
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
$inv = $NsdjWi["warn"];
$newin = $inv + 4;
$NsdjWi["warn"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$addus= $iDpV . "\n";
  bot('sendMessage',[
 'chat_id'=>$pViD,
 'text'=>"
 به دلیل ارسال کلمه ، حروف ممنوعه از ربات بلاک شدید 🌹
 ",
 'parse_mode'=>"HTML",
  'reply_to_message_id'=>$message_id,
  ]); 
  bot('sendMessage',[
 'chat_id'=>$admin,
 'text'=>"
مدیریت گرامی 🌹
سیستم ضد هک هوشمند یک فرد که ظاهرا قسط هک ربات داشته رو دستگیر کرده 🌹
👇🏻 اطلاعات فرد 👇🏻
👤 نام : $first_name
[🗣 نمایش پروفایل](tg://user?id=$iDpV)
🆔 ایدی فرد : $username
🆔 آیدی عددی فرد : $iDpV
🚫 کد استفاده شده : 🚫
[   $MyLoVe   ]
",
 'parse_mode'=>"MarkDown",
  ]); 
 }
elseif(strpos($blocklist, "$iDpV") !== false and $pViD != $admin){
bot('sendmessage',[
'chat_id'=>$pViD,
'text'=>"
شما به خاطر رعایت نکردن قوانین از ربات مسدود شدید. ❌

لطفا پیام ارسال نکنید.⚠️
",
'reply_markup'=>json_encode([
'remove_keyboard'=>true,
])
]);
exit();
}

if($warn >= 3 and $pViD != $admin){
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"❌ شما سه اخطار دریافت کردید و از ربات مسدود شدید",
'parse_mode'=>"HTML",
'reply_to_message_id'=>$message_id
]); 
return false;

}
$ads = $NsdjWi["ads"];
$invcoin = $NsdjWi["invcoin"];
$date = date("Y-F-d");
if(file_exists("DaTaBoT/admin2.txt")){
$admin2 = file_get_contents("DaTaBoT/admin2.txt");
}else{
$admin2 = "[*[ADMIN]*]";
}
/////////////------//////////
if(file_exists("DaTaBoT/admin3.txt")){
$admin3 = file_get_contents("DaTaBoT/admin3.txt");
}else{
$admin3 = "[*[ADMIN]*]";
}
/////////////------//////////
if(file_exists("DaTaBoT/starttext.txt")){
$StArTeXt = file_get_contents("DaTaBoT/starttext.txt");
$StArTeXt = str_replace('NAME',$first,$StArTeXt);
$StArTeXt = str_replace('LAST',$last,$StArTeXt);
$StArTeXt = str_replace('USER',$username,$StArTeXt);
$StArTeXt = str_replace('ID',$iDpV,$StArTeXt);
}else{
$StArTeXt = "
⭐️ به @[*[USERNAME]*] خوش آمدید

با ما میتوانید بهترین ربات برای مبادله سکه ربات های تلگرام استفاده کنید 😉🌹

💎 برای شروع مبادله روی دکمه های زیر کلیک کنید
";
}
/////////////////////////---/////
if(file_exists("DaTaBoT/coinamount.txt")){
$coinamount = file_get_contents("DaTaBoT/coinamount.txt");
$coinamount = str_replace('NAME',$first,$coinamount);
}else{
$coinamount = "1";
}
/////////////------//////////
if(file_exists("DaTaBoT/porsant.txt")){
$porsant = file_get_contents("DaTaBoT/porsant.txt");
$porsant = str_replace('NAME',$first,$porsant);
}else{
$porsant = "0.2";
}
/////////////------//////////
if(file_exists("DaTaBoT/ZiRtExT.txt")){
$idbot = [*[USERNAME]*];
$ZiRtExT = file_get_contents("DaTaBoT/ZiRtExT.txt");
$ZiRtExT = str_replace('NAME',$first,$ZiRtExT);
$ZiRtExT = str_replace('LAST',$last,$ZiRtExT);
$ZiRtExT = str_replace('LINK',"https://t.me/$idbot?start=$pViD",$ZiRtExT);
$ZiRtExT = str_replace('ID',$pViD,$ZiRtExT);
}else{
$ZiRtExT = "
💞 متن پیشفرض :


✅ رباتساز اِسلُو کریت ! 

✅ قابلیت ساخت انواع ربات های پیشرفته ! 

✅ قابلیت زیر مجموعه گیری 

✅ همین الان ربات خودت رو بساز ! 

{ @SlowCreateBot }
";
}

////////////////----///////////////
if(file_exists("DaTaBoT/ghavanin.txt")){
$ghavanin = file_get_contents("DaTaBoT/ghavanin.txt");
$ghavanin = str_replace('NAME',$first2,$ghavanin);
}else{
$ghavanin = "  تنظیم نشده است";
}
////////////////----///////////////
if(file_exists("DaTaBoT/ghavanin2.txt")){
$ghavanin2 = file_get_contents("DaTaBoT/ghavanin2.txt");
}else{
$ghavanin2 = "  تنظیم نشده است";
}
////////////////----///////////////
if(file_exists("DaTaBoT/ghavanin4.txt")){
$ghavanin4 = file_get_contents("DaTaBoT/ghavanin4.txt");
}else{
$ghavanin4 = "  تنظیم نشده است";
}
////////////////----///////////////
////////////////----///////////////
if(file_exists("DaTaBoT/ghavanin1.txt")){
$ghavanin1 = file_get_contents("DaTaBoT/ghavanin1.txt");
}else{
$ghavanin1 = "تنظیم نشده است";
}
////////////////----///////////////
if(file_exists("DaTaBoT/ghavanin3.txt")){
$ghavanin3 = file_get_contents("DaTaBoT/ghavanin3.txt");
}else{
$ghavanin3 = "  تنظیم نشده است";
}
///////////////-------///////
////////////////----///////////////
if(file_exists("DaTaBoT/ghghghghg.txt")){
$ghghghghg = file_get_contents("DaTaBoT/ghghghghg.txt");
$ghghghghg = str_replace('NAME',$first2,$ghghghghg);
}else{
$ghghghghg  = "متن تعرفه تنظیم نشده است";
}
///////////////-------///////
if(file_exists("DaTaBoT/invitecoin.txt")){
$invitecoin = file_get_contents("DaTaBoT/invitecoin.txt");
$invitecoin = str_replace('NAME',$first2,$invitecoin);
}else{
$invitecoin = "10";
}
////////////////////////////
if(file_exists("DaTaBoT/piclink.txt")){
$piclink = file_get_contents("DaTaBoT/piclink.txt");
}else{
$piclink = "https://t.me/slokings/7871";
}
///////////------//////////-----------////////
$chads = file_get_contents("cht.txt");
$chor = file_get_contents("DaTaBoT/ch.txt");
$channels = json_decode(file_get_contents("https://api.telegram.org/bot$JuStLoVe/getChatMember?chat_id=@$chor&user_id=".$iDpV or $iD_cAlLbAcK));
$to = $channels->result->status;
$reply = $update->message->reply_to_message->forward_from->id;
//============================================================================//
if(strpos($MyLoVe == "/start") !== false  and $MyLoVe !=="/start" and $chat == 'private'){
$id=str_replace("/start ","",$MyLoVe);
$amar=file_get_contents("DaTaBoT/ozvs.txt");
$exp=explode("\n",$amar);
if(!in_array($iDpV,$exp) and $iDpV != $id){
if(!is_file("VIP")){
	SM($pViD);
}
if (!file_exists("DaTaBoT/$pViD/$pViD.json")) {
$myfile2 = fopen("DaTaBoT/ozvs.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pViD\n");
fclose($myfile2);
$NsdjWi["step"] = "free";
$NsdjWi["type"] = "empty";
$NsdjWi["inv"] = "0";
$NsdjWi["coin"] = "0";
$NsdjWi["warn"] = "0";
$NsdjWi["ads"] = "0";
$NsdjWi["sub"] = "$id";
$NsdjWi["hoviat"] = "No";
$NsdjWi["invcoin"] = "0";
$NsdjWi["panel"] = "free";
$NsdjWi['dafeee'] = "first";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$pViD/$pViD.json",$OuTjSoN);
}
$NsdjWi12 = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$invite1 = $NsdjWi12["inv"];
settype($invite1,"integer");
$newinvite = $invite1 + 1;
$NsdjWi12["inv"] = $newinvite;
$OuTjSoN = json_encode($NsdjWi12,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
$NsdjWi1234 = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$invite122 = $NsdjWi1234["coin"];
settype($invite122,"integer");
$newinvite664 = $invite122 + $invitecoin;
$NsdjWi1234["coin"] = $newinvite664;
$OuTjSoN = json_encode($NsdjWi1234,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"
🎉 کاربر [$first_name](t.me/$idbot) با لینک شما وارد ربات شد
🎊 و تعداد $invitecoin به حساب شما اضافه شد
",
'parse_mode'=>"MarkDown",
]);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$StArTeXt
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]);
}
}
if($MyLoVe == "/start" and $chat == 'private'){
if (!file_exists("DaTaBoT/$pViD/$pViD.json")) {
$myfile2 = fopen("DaTaBoT/ozvs.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pViD\n");
fclose($myfile2);
$NsdjWi["step"] = "free";
$NsdjWi["type"] = "empty";
$NsdjWi["inv"] = "0";
$NsdjWi["coin"] = "0";
$NsdjWi["warn"] = "0";
$NsdjWi["ads"] = "0";
$NsdjWi["hoviat"] = "No";
$NsdjWi["invcoin"] = "0";
$NsdjWi["panel"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$pViD/$pViD.json",$OuTjSoN);
}
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {

if(!is_file("VIP")){
SM($pViD);
}
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$StArTeXt
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
if(!is_file("VIP")){
	SM($pViD);
}
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$StArTeXt
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
if(isset($iDpV)){
$truechannel = json_decode(file_get_contents("https://api.telegram.org/bot$JuStLoVe/getChatMember?chat_id=$channel&user_id=$iDpV"));
}
else
{
$CaLlBaCk_iD = $update->callback_query->from->id;
$truechannel = json_decode(file_get_contents("https://api.telegram.org/bot$JuStLoVe/getChatMember?chat_id=$channel&user_id=$CaLlBaCk_iD"));
}
$chath25 = $truechannel->result->status;
if($chath25 != 'member' and $chath25 != 'creator' and $chath25 != 'administrator' and is_file("channel.txt") and $pViD != $admin){
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"⚠️ جهت فعالیت در ربات  نیاز است در کانال اطلاع رسانی آن عضو شوید.

🌐 $channel 

♻️ پس از عضویت در کانال مجددا ربات را /start ارسال نمایید.
",
'disable_web_page_preview' => true,
'parse_mode'=>"HTML",
]);
exit();
}
if (!file_exists("DaTaBoT/$iDpV/$iDpV.json")) {
$myfile2 = fopen("DaTaBoT/ozvs.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$iDpV\n");
fclose($myfile2);
$NsdjWi["step"] = "free";
$NsdjWi["type"] = "empty";
$NsdjWi["inv"] = "0";
$NsdjWi["coin"] = "0";
$NsdjWi["warn"] = "0";
$NsdjWi["ads"] = "0";
$NsdjWi["hoviat"] = "No";
$NsdjWi["invcoin"] = "0";
$NsdjWi["panel"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
}
if($hoviat== "No" && $iDpV != $admin ){
json($iDpV,"hoviat","yes");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"شما تایید هویت نکرده اید🔐

لطفا صبر کنید تا تصویر امنیتی رو ارسال کنم👌",
'reply_markup'=>json_encode([
'KeyboardRemove'=>[],
'remove_keyboard'=>true,
])
]);
$r = rand(1111,99999);
json($iDpV,"step","cap$r");
bot('sendPhoto',[
'chat_id'=>$pViD,
'photo'=>"https://dynamic.brandcrowd.com/asset/logo/322d6997-a2bc-4d68-9456-3e8ce1f1506c/logo?v=4&text=$r",
'caption'=>"با سلام و عرض ادب خدمت شما کاربر عزیز برای تایید هویت عدد در تصویر رو ارسال کنید🔆",
]);
return false;
    
}
if($MyLoVe != "sss" && strpos($step,"cap") !== false && $MyLoVe != "برگشت"){
$okoo = str_replace("cap","",$step);
if($MyLoVe == $okoo){
json($iDpV,"step","none");
json($iDpV,"hoviat","yes");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"🔐",
'reply_markup'=>json_encode([
"resize_keyboard"=>true,
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"عدد وارد شده اشتباه هست لطفا دباره سعی کنید♻️❗️",
]);
}
return false;
    
}

if($MyLoVe == "🔙 بازگشت" or $MyLoVe == "🔙 بازگشت" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"👈🏻⁩ به منوی اصلی بازگشتید.

",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$StArTeXt
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

if($MyLoVe == "⚖" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"🇮🇷 تابع قوانین جمهوری اسلامی ایران

‼️خرید از طریق کارت های هک شده و دزدی ممنوع میباشد و موجب مسدود شدن حساب میگردد + پیگرد قانونی

🚫 استفاده از اکانت های فیک ممنوع میباشد و موجب مسدود شدن حساب می گردد 🚫

✅ احراز هویت در ربات فعال شده است جهت جلوگیری از کلاهبرداری و افزایش امنیت پرداخت و برداشت از حساب

❇️ برای هر سفارش 5 درصد برای کارمزد گرفت می‌شود 
🌀 مثال : اگر شما 10000 نیتروسین سفارش دادید 9500 دریافت میکنید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"🇮🇷 تابع قوانین جمهوری اسلامی ایران

‼️خرید از طریق کارت های هک شده و دزدی ممنوع میباشد و موجب مسدود شدن حساب میگردد + پیگرد قانونی

🚫 استفاده از اکانت های فیک ممنوع میباشد و موجب مسدود شدن حساب می گردد 🚫

✅ احراز هویت در ربات فعال شده است جهت جلوگیری از کلاهبرداری و افزایش امنیت پرداخت و برداشت از حساب

❇️ برای هر سفارش 5 درصد برای کارمزد گرفت می‌شود 
🌀 مثال : اگر شما 10000 نیتروسین سفارش دادید 9500 دریافت میکنید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
if($MyLoVe == "💡راهنما" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$ghavanin",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$ghavanin",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
if($MyLoVe == "💰 تعرفه" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$ghghghghg",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"$ghghghghg",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
////////////////////////////
if($MyLoVe == "موجودی ربات🔹" and $chat == 'private'){
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>" 
موجودی فعلی ربات تا زمان
💠 $time 💠
",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[
['text' => "⭐️ نیتروسین", 'callback_data' => "x"],
['text' => "$ghavanin2", 'callback_data' => "x"]
                    ],
                    [
['text' => "〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰", 'callback_data' => "x"]
                    ],[
['text' => "⭐️ رپیدسین", 'callback_data' => "x"],
['text' => "$ghavanin3", 'callback_data' => "x"]
                    ],
                    [
['text' => "〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰", 'callback_data' => "x"]
                    ],[
['text' => "⭐️ جت ممبر", 'callback_data' => "x"],
['text' => "$ghavanin1", 'callback_data' => "x"]
                    ],
                    [
['text' => "〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰〰", 'callback_data' => "x"]
                    ],[
['text' => "⭐️ سرعت ممبر", 'callback_data' => "x"],
['text' => "$ghavanin4", 'callback_data' => "x"]
                    ],
]
])
]);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"
موجودی فعلی ربات تا زمان🤞
💠 $time 💠",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
////////////////////////////
if($MyLoVe == "⭐️ برداشت موجودی" and $chat == 'private'){
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"ربات مورد نظر خود را انتخاب کنید.📍",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ رپیدسین"],['text'=>"⭐️ نیتروسین"]],
[['text'=>"⭐️ جت ممبر"],['text'=>"⭐️ سرعت ممبر"]],
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
//////////////////////////
elseif($MyLoVe == "⭐️ سرعت ممبر" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2196";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید از حساب خود برداشت کنید  ارسال کنید :

#حداقل 100 الماس💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2196" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
         $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $tak = $NsdjWi["coin"];
    if ($tak >= $MyLoVe) {
    if ($MyLoVe >= 100) {
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv - $MyLoVe;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"✅ برداشت شما با موفقیت ثبت شد.
😉 تمام تلاش ما این است که تمام سفارشات در اسرع وقت انجام شوند.
🎈 بزودی برداشت شما تکمیل خواهد شد...
➖➖➖➖➖➖➖
ℹ️ اطلاعات سفارش شما
🚀 دسته بندی: ⭐️ سرعت ممبر 
💎 تعداد سفارش: $MyLoVe
⏱ نشانی دریافت : $pViD
📟 وضعیت سفارش: درحال انجام...!

➖➖➖➖➖➖➖
❤️با تشکر فراوان از شما!",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست  برداشت  موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 {COIN OF SPEEDMEMBER }
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
با تشکر از شما سرور گرامی💛
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز  100 امتیاز می باشد");
	}
	}else{
		SendMessage($pViD,"❌امتیاز شما کافی نمی باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }

//////////////////////////
elseif($MyLoVe == "⭐️ جت ممبر" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2195";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید از حساب خود برداشت کنید  ارسال کنید :

#حداقل 100 الماس💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2195" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
         $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $tak = $NsdjWi["coin"];
    if ($tak >= $MyLoVe) {
    if ($MyLoVe >= 100) {
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv - $MyLoVe;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"✅ برداشت شما با موفقیت ثبت شد.
😉 تمام تلاش ما این است که تمام سفارشات در اسرع وقت انجام شوند.
🎈 بزودی برداشت شما تکمیل خواهد شد...
➖➖➖➖➖➖➖
ℹ️ اطلاعات سفارش شما
🚀 دسته بندی: ⭐️ جت ممبر
💎 تعداد سفارش: $MyLoVe
⏱ نشانی دریافت : $pViD
📟 وضعیت سفارش: درحال انجام...!

➖➖➖➖➖➖➖
❤️با تشکر فراوان از شما!",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست  برداشت  موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 {COIN OF GETMEMBER }
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
با تشکر از شما سرور گرامی💛
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز   100 امتیاز می باشد");
	}
	}else{
		SendMessage($pViD,"❌امتیاز شما کافی نمی باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }

////////////////////////////
elseif($MyLoVe == "⭐️ رپیدسین" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2198";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید از حساب خود برداشت کنید  ارسال کنید :

#حداقل 100 سکه💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2198" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
         $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $tak = $NsdjWi["coin"];
    if ($tak >= $MyLoVe) {
    if ($MyLoVe >= 100) {
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv - $MyLoVe;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"✅ برداشت شما با موفقیت ثبت شد.
😉 تمام تلاش ما این است که تمام سفارشات در اسرع وقت انجام شوند.
🎈 بزودی برداشت شما تکمیل خواهد شد...
➖➖➖➖➖➖➖
ℹ️ اطلاعات سفارش شما
🚀 دسته بندی: ⭐️ رپیدسین
💎 تعداد سفارش: $MyLoVe
⏱ نشانی دریافت : $pViD
📟 وضعیت سفارش: درحال انجام...!

➖➖➖➖➖➖➖
❤️با تشکر فراوان از شما!",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست  برداشت  موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 {COIN OF RAPIDSEEN }
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
با تشکر از شما سرور گرامی💛
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز  100 امتیاز می باشد");
	}
	}else{
		SendMessage($pViD,"❌امتیاز شما کافی نمی باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }

/////////////////////////////
elseif($MyLoVe == "⭐️ نیتروسین" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2199";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید از حساب خود برداشت کنید  ارسال کنید :

#حداقل 100 سکه💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2199" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
         $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $tak = $NsdjWi["coin"];
    if ($tak >= $MyLoVe) {
    if ($MyLoVe >= 100) {
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv - $MyLoVe;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"✅ برداشت شما با موفقیت ثبت شد.
😉 تمام تلاش ما این است که تمام سفارشات در اسرع وقت انجام شوند.
🎈 بزودی برداشت شما تکمیل خواهد شد...
➖➖➖➖➖➖➖
ℹ️ اطلاعات سفارش شما
🚀 دسته بندی: ⭐️ نیتروسین
💎 تعداد سفارش: $MyLoVe
⏱ نشانی دریافت : $pViD
📟 وضعیت سفارش: درحال انجام...!

➖➖➖➖➖➖➖
❤️با تشکر فراوان از شما!",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست  برداشت  موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 { COIN OF NITROSEEN}
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
با تشکر از شما سرور گرامی💛
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),
]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز   100 امتیاز می باشد");
	}
	}else{
		SendMessage($pViD,"❌امتیاز شما کافی نمی باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }
////////////////////////////
if($MyLoVe == "💠 افزایش موجودی" and $chat == 'private'){
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"ربات مورد نظر خود را انتخاب کنید.📍",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"زیرمجموعه گیری⚜"]],
[['text'=>"❗️پرداخت رپیدسین"],['text'=>"❗️پرداخت نیتروسین"]],
[['text'=>"❗️پرداخت جت ممبر"],['text'=>"❗️پرداخت سرعت ممبر"]],
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
//
elseif($MyLoVe == "❗️پرداخت نیتروسین" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT219";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید حساب خود را شارژ کنید ارسال کنید :

#حداقل 100 سکه💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT219" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
    if($MyLoVe >= 100){
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان `$MyLoVe` سکه را به حساب ( `$admin` ) واریز کنید ✅
پس از واریز حساب شما حداکثر طی 1 ساعت شارژ میشود ❗️
",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست تسویه افزایش موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 { coin of Nitroseen}
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
    توجه❗: صبر کنید تا به حساب شما واریز شود بغد اقدام به افزایش موجودی کاربر درخواست کننده نمایید😉
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز   100 امتیاز می باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }
/////////////////////
elseif($MyLoVe == "❗️پرداخت سرعت ممبر" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2191";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان الماس ای که میخواهید حساب خود را شارژ کنید ارسال کنید :

#حداقل 100 الماس💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2191" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
    if($MyLoVe >= 100){
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان `$MyLoVe` الماس را به حساب ( `$admin` ) واریز کنید ✅
پس از واریز حساب شما حداکثر طی 1 ساعت شارژ میشود ❗️
",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست تسویه افزایش موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 { coin of speedmember}
    مقدار الماس درخواستی کاربر
    👉🏻 { $MyLoVe }
    توجه❗: صبر کنید تا به حساب شما واریز شود بغد اقدام به افزایش موجودی کاربر درخواست کننده نمایید😉
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]
);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز   100 امتیاز می باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }
///////////////////
elseif($MyLoVe == "❗️پرداخت جت ممبر" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2193";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان الماس ای  میخواهید حساب خود را شارژ کنید ارسال کنید :

#حداقل 100 الماس💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2193" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
    if($MyLoVe >= 100){
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان `$MyLoVe` الماس را به حساب ( `$admin` ) واریز کنید ✅
پس از واریز حساب شما حداکثر طی 1 ساعت شارژ میشود ❗️
",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست تسویه افزایش موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 { COIN OF GETMEMBER}
    مقدار الماس درخواستی کاربر
    👉🏻 { $MyLoVe }
    توجه❗: صبر کنید تا به حساب شما واریز شود بغد اقدام به افزایش موجودی کاربر درخواست کننده نمایید😉
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز  100 امتیاز می باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }
////////////////////
elseif($MyLoVe == "❗️پرداخت رپیدسین" and $chat == 'private'){
$NsdjWi["step"] = "ZiRtExT2197";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
    bot('sendMessage',[
        'chat_id'=>$pViD,
        'text'=>"💰 میزان سکه ای که میخواهید حساب خود را شارژ کنید ارسال کنید :

#حداقل 100 سکه💰",
         'reply_to_message_id'=>$message_id,
         'parse_mode'=>"HTML",
          'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
        ])
        ]);
}
elseif($step == "ZiRtExT2197" and $MyLoVe != "🔙 بازگشت"and $chat == 'private'){ 
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
if(is_numeric($MyLoVe)){
    if($MyLoVe >= 100){
bot('sendMessage',[
    'chat_id'=>$pViD,
    'text'=>"💰درخواست شارژ حساب شما با موفقیت ساخته شد!

شما باید میزان `$MyLoVe` سکه را به حساب ( `$admin` ) واریز کنید ✅
پس از واریز حساب شما حداکثر طی 1 ساعت شارژ میشود ❗️
",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"در خواست تسویه افزایش موجودی 💠
    کاربر در خواست کننده 
    👉🏻 { $pViD }
    نوع پرداخت✔
    👉🏻 { COIN OF RAPIDSEEN}
    مقدار سکه درخواستی کاربر
    👉🏻 { $MyLoVe }
    توجه❗: صبر کنید تا به حساب شما واریز شود بغد اقدام به افزایش موجودی کاربر درخواست کننده نمایید😉
    ",
    'parse_mode'=>"MarkDown",
            'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⭐️ برداشت موجودی"]],
[['text'=>"♻️ آپدیت"],['text'=>"💡راهنما"]],
[['text'=>"💠 افزایش موجودی"],['text'=>"موجودی ربات🔹"]],
[['text'=>"🔐"],['text'=>"🎁"],['text'=>"🏧"],['text'=>"⚖"],['text'=>"🆘"]],
[['text'=>"💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
]),

]);
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز  100 امتیاز می باشد");
	}
    }else{
		SendMessage($pViD," چی میگی عزیز متوجه نمیشوم😎");
	}
         
     }
elseif($MyLoVe == "🎁" and $chat == 'private'){
$NsdjWi["step"] = "mgiftcode";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"

🧨 کد هدیه را وارد کنید
",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
if($step == "mgiftcode" and $MyLoVe != "🔙 بازگشت" and $chat == 'private'){ 
      if(file_exists("DaTaBoT/codes/$MyLoVe.txt")){
        $pricegift = file_get_contents("DaTaBoT/codes/$MyLoVe.txt");
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$pViD/$pViD.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv + $pricegift;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$pViD/$pViD.json",$OuTjSoN);
		SendMessage($pViD,"کد امتیازی شما درست بود🌟

 $pricegift 💎امتیاز به حسابت الان واریز شد✅");
        unlink("DaTaBoT/codes/$MyLoVe.txt");
        $NsdjWi1["step"] = "free";
bot('sendMessage', [
'chat_id' =>"@$chor",
'text' => "کدامتیازی💎با موفقیت استفاده شد✅

👤 توسط :
➖➖➖➖➖➖➖➖➖➖➖➖
🆔Username:$pViD
➖➖➖➖➖➖➖➖➖➖➖➖

🎉 ➖➖👆🏽نوش جونت👆🏽 ➖➖🎉",
]);
	}else{
		SendMessage($pViD,"😔متاسفانه کد امتیازی دیر زدی یکی دیگه استفاده کرد🔹");
	}
}
elseif($MyLoVe == "🆘"){	
$NsdjWi["step"] = "support";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendmessage',[
'chat_id'=>$pViD,
'text'=>"✉️ متن پیام خود را با رعایت موارد زیر ارسال نمایید:

1⃣ سوال، پیام، انتقاد و پیشنهادهای خود را درون یک پیام واحد نوشته و ارسال نمایید. (از جواب دادن به مواردی که در چند پیام جداگانه ارسال می شود معذوریم)
2⃣ تا زمان دریافت پاسخ صبور باشید و از پرسش مجدد خودداری کنید.",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
            'keyboard'=>[
                [
                ['text'=>"🔙 بازگشت"],
                ]
              ],
])
]);
}
if($step == "support" && $MyLoVe != "🔙 بازگشت"){ 
$NsdjWi["step"] = "support";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('ForwardMessage',[
'chat_id'=>$admin,
'from_chat_id'=>$pViD,
'message_id'=>$message_id
]);
SendMessage($admin,"✍🏻کاربر ارسال کننده : $pViD");
SendMessage($pViD,"💎پیام شما دریافت شد
لطفا تا زمان پاسخ گویی صبور باشید");
}
elseif($s2da != "" && $iDpV == $admin){
bot('sendmessage',[
'chat_id'=>$s2da,
 'text'=>"📮پاسخ پشتیبان برای شما🕔
┅┄┄┅┅┄┄┅┅┄┄┅
$MyLoVe",
'parse_mode'=>'MarkDown',
]);
bot('sendmessage',[
 'chat_id'=>$pViD,
 'text'=>"پاسخ با موفقیت به $s2da ارسال شد",
'parse_mode'=>'MarkDown',
 ]);
}
/////////////////////////
elseif($MyLoVe == "🏧" and $chat == 'private'){
$NsdjWi["step"] = "movegeme";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"〽️ به بخش انتقال موجودی خوش آمدید

💰  با دکمه انتقال موجودی می توانید موجودی های خود را به دیگران انتقال دهید.

⭐️ ایدی عددی کاربری که میخواهید انتقال دهید رو وارد 
",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
if($step == "movegeme" and $MyLoVe != "/start" and $MyLoVe != "🔙 بازگشت" and $chat == 'private'){ 
      if(file_exists("DaTaBoT/$MyLoVe/")){
           file_put_contents("DaTaBoT/$pViD/movemem.txt",$MyLoVe);
$NsdjWi["step"] = "movegeme2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
		SendMessage($pViD,"چه تعداد 💎امتیاز میخواهید انتقال دهید!!؟؟" );
        unlink("DaTaBoT/codes/$MyLoVe.txt");
	}else{
		SendMessage($pViD,"❌این کاربر عضو ربات نمی باشد");
	}
}
if($step == "movegeme2" and $MyLoVe != "/start" and $MyLoVe != "🔙 بازگشت" and $chat == 'private'){ 
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
     if ($inv >= $MyLoVe) {
     if ($MyLoVe >= 1000) {
        $movemem = file_get_contents("DaTaBoT/$iDpV/movemem.txt");
        $NsdjWi = json_decode(file_get_contents("DaTaBoT/$iDpV/$iDpV.json"),true);
        $inv = $NsdjWi["coin"];
        $newin = $inv - $MyLoVe;
        $NsdjWi["coin"] = "$newin";
        $OuTjSoN = json_encode($NsdjWi,true);
        file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
        $NsdjWi212 = json_decode(file_get_contents("DaTaBoT/$movemem/$movemem.json"),true);
        $inv212 = $NsdjWi212["coin"];
        $newin212 = $inv212 + $MyLoVe;
        $NsdjWi212["coin"] = "$newin212";
        $OuTjSoN = json_encode($NsdjWi212,true);
        file_put_contents("DaTaBoT/$movemem/$movemem.json",$OuTjSoN);
		SendMessage($pViD,"🔅با موفقیت $MyLoVe امتیاز به شناسه کاربری $movemem منتقل گردید");
		SendMessage($movemem,"💌🔅کاربر گرامی

کاربر با شناسه $pViD میزان $MyLoVe امتیاز به حساب شما منتقل کرد");
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	}else{
		SendMessage($pViD,"❌حداقل امتیاز قابل انتقال 1000 امتیاز می باشد");
	}
	}else{
		SendMessage($pViD,"❌امتیاز شما کافی نمی باشد");
	}
         
     }
     
elseif($MyLoVe == "/creator" and $chat == 'private'){
if(file_exists("../../SlowCreate/UsErBoT/$idbot/creator.txt")){
$creator = file_get_contents("../../SlowCreate/UsErBoT/$idbot/creator.txt");
}
SendMessage($pViD,"$creator");
}
//////////////////////////////////
if($MyLoVe=="زیرمجموعه گیری⚜"){
    bot('sendphoto',[
    'photo'=>"$piclink",
    'chat_id'=>$pViD,
    'caption'=>"$ZiRtExT
",
'parse_mode'=>'html',

    ]);
}
///////////////////
if($MyLoVe=="♻️ آپدیت"){
   bot('sendMessage',[
 'chat_id'=>$pViD,
 'text'=>'لطفا صبرکنید 🎉'
]);
 sleep(0);
   bot('editmessagetext',[
 'chat_id'=>$pViD,
  'message_id'=>$message_id + 1,
 'text'=>'در حال دریافت اطلاعات📂'
]);
 sleep(0);
   bot('editmessagetext',[
 'chat_id'=>$pViD,
  'message_id'=>$message_id + 1,
 'text'=>'بروز رسانی تا 3 ثانیه دیگه انجام می شود ♻'
]);
  sleep(3);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️10%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️20%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️30%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️▪️40%'
]);
 sleep(0); 
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️▪️▪️50%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️▪️▪️▪️60%'
]);
 sleep(1);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'twxt' => '▪️▪️▪️▪️▪️▪️▪️70%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️▪️▪️▪️▪️▪️80%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text' => '▪️▪️▪️▪️▪️▪️▪️▪️▪️90%'
]);
 sleep(0);
 bot('editmessagetext',[
 'chat_id'=>$pViD,
 'message_id'=>$message_id + 1,
 'text'=>"
📅 تاریخ :  $date
🕰 زمان :  $time
  
♻️ ربات شما به آخرین نسخه آپدیت شد. 
💠 نسخه‌ی ربات : 1.0
☑️ برای اعمال به روز رسانی ربات را مجدداً /start کنید.",

'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[

],
]
])
]); 
}


//===hmdemon===//
if($MyLoVe == "🔐" and $chat == 'private'){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$step = $NsdjWi["step"];
$inv = $NsdjWi["inv"];
$coin = $NsdjWi["coin"];
$type = $NsdjWi["type"];
$sefaresh = $NsdjWi["sefaresh"];
$warn = $NsdjWi["warn"];
$ads = $NsdjWi["ads"];
$invcoin = $NsdjWi["invcoin"];
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>" 
👤 شناسه شما : $pViD
👥🌀تعداد زیرمجموعه ها : $inv 
⚠️ تعداد اخطارها : $warn از 3
🔐 وضعیت کپچا : $hoviat

💰 موجودی شما : $coin

⏰ : $time- $date
",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[

],
]
])
]); 
}
//----------------------------------------------------------------------
elseif($MyLoVe == "مدیریت" or $MyLoVe == "🔙 پنل مدیریت" or $MyLoVe == "/panel" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"سلام مدیر گرامی به پنل خوش آمدید!",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"⚙ مدیریت"],['text'=>"🃏 تنظیمات پیشرفته"]],
[['text'=>"🔙 بازگشت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
if($MyLoVe == "📈 آمار ربات" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki) - 2;
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>" 
💠 $time 💠 مشخصات ربات تا ساعت👇
",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[
['text' => "آمار ربات  🧾", 'callback_data' => "x"],
['text' => "$allusers", 'callback_data' => "x"]
                    ],
[
['text' => "موجودی نیتروسبن ثبت شده⚜", 'callback_data' => "x"],
['text' => "$ghavanin2", 'callback_data' => "x"]
                    ],
                    [
                    ],[
['text' => "موجودی ریپدسین ثبت شده ✨", 'callback_data' => "x"],
['text' => "$ghavanin3", 'callback_data' => "x"]
                    ],
                    [

                    ],[
['text' => "موجودی جت ممبر ثبت شده 🚀", 'callback_data' => "x"],
['text' => "$ghavanin1", 'callback_data' => "x"]
                    ],
                    [

                    ],[
['text' => "موجودی سرعت ثبت شده ⚡", 'callback_data' => "x"],
['text' => "$ghavanin4", 'callback_data' => "x"]
                    ],
]
])
]);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"
💠 $time 💠 مشخصات ربات تا ساعت👆
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
///////////////////////////////////////////
if($MyLoVe == "🃏 تنظیمات پیشرفته" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"به بخش تنظمات پیشرفته خوش امدید ادمین 📍
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}


///////////////////////////////////////////
elseif($MyLoVe == "⚙ مدیریت" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"به بخش مدیریت خوش امدید ادمین 📍
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

///////////////----------------////////////
elseif($MyLoVe == "تنظیم متن و امتیاز ⚙" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"به قسمت تنظیم متن و امتیاز خوش آمدید
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"تنظیم پورسانت زیرمجموعه"]],
[['text'=>"تنظیم امتیاز زیرمجموعه گیری"]],
[['text'=>"تنظیم متن بنر عکسدار"],['text'=>"تنظیم متن استارت"]],
[['text'=>"تنظیم متن 💡راهنما"]],
[['text'=>"تنظیم متن 💰 تعرفه"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
///////////////----------------////////////
elseif($MyLoVe == "🔰 تنظیمات موجودی" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"به بخش تنظیمات موجودی خوش امدید🌹
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔵 تنظیم موجودی جت ممبر"],['text'=>"🟢 تنظیم موجودی سرعت ممبر"]],
[['text'=>"💙 تنظیم موجودی نیترو سین"],['text'=>"💚 تنظیم موجودی ریپدسین"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "مبادلات امتیازی♻️" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"به قسمت مبادلات امتیاز خوش آمدید❤️
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"اهدای امتیاز💎"],['text'=>"کسر امتیاز💎"]],
[['text'=>"💎امتیاز همگانی💎"],['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "⛔️ اخطار دادن و حذف اخطار✅" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"بوسیله این قسمت میتونی کاربر رو بلاک یا ان بلاک کنی❗️
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"اخطار دادن📛"],['text'=>"حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "ارسال پیام💬" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"🔹به قسمت ارسال پیام خوش آمدید🔸
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🕴🏻پیام به کاربر🕴🏻"],['text'=>"پیام همگانی💬"]],
[['text'=>"فروارد همگانی💭"],['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

elseif($MyLoVe == "☑ افزودن ادمین"){		
if ($pViD == $admin) {
$alluser = file_get_contents("DaTaBoT/ozvs.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki);
$done = file_get_contents("DaTaBoT/done.txt");
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"✅ به بخش تنظیمات ادمین ها خوش آمدید",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 ادمین سوم"],['text'=>"🔰 ادمین دوم"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"❌این بخش فقط توسط ادمین اصلی ربات قابل استفاده می باشد",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "🏞 تنظیم عکس 🏞" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "starttext688";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"خب عکس جدید رو بفرستین",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "starttext688" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $MyLoVe != "🔙 بازگشت" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
	$filephoto = $update->message->photo;
	$photo = $filephoto[count($filephoto)-1]->file_id;
	if(isset($photo)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/piclink.txt",$photo);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"با موفقیت تنظیم شد",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
	}else{
		bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عکس بفرس",
'parse_mode'=>"MarkDown"
]); 
	}
}}
elseif($MyLoVe == "🔰 ادمین دوم" and $chat == 'private'){	
if ($pViD == $admin) {
$NsdjWi["step"] = "adminman2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی ادمین جدید را ارسال نمایید
ادمین فعلی : $admin2",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "adminman2" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $chat == 'private'){ 
if ($pViD == $admin) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/admin2.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"با موفقیت تنظیم شد

ادمین جدید : $MyLoVe",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 ادمین سوم"],['text'=>"🔰 ادمین دوم"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "🔰 ادمین سوم" and $chat == 'private'){	
if ($pViD == $admin) {
$NsdjWi["step"] = "adminman3";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی ادمین جدید را ارسال نمایید
ادمین فعلی : $admin3",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "adminman3" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $chat == 'private'){ 
if ($pViD == $admin) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/admin3.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"با موفقیت تنظیم شد

ادمین جدید : $MyLoVe",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 ادمین سوم"],['text'=>"🔰 ادمین دوم"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

elseif($MyLoVe == "تنظیم متن استارت" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "starttext";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"متن استارت را ارسال کنید
به جای نام NAME
به جای یوزرنیم @USER
و به جای نام خانوادگی LAST
و به جای آیدی عددی ID

را در متن قرار دهید تا جایگزین شود!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "starttext" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/starttext.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "🆑 تنظیم کانال" and $chat == 'private'){
	if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
		$NsdjWi["step"] = "getchannel";
		$OuTjSoN = json_encode($NsdjWi,true);
		file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
		bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا ابتدا ربات را ادمین کانال کنید سپس آیدی کانال را همراه با @ ارسال نمایید.\nدرصورتی که میخواهید قفل کانال حذف شود عدد 0 را ارسال کنید.",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
	}
}
elseif($step == "getchannel" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){
	$NsdjWi["step"] = "none";
	$OuTjSoN = json_encode($NsdjWi,true);
	file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
	if($MyLoVe == '0'){
		unlink("channel.txt");
		bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"قفل کانال با موفقیت حذف شد.",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
	}
	else{
		file_put_contents("channel.txt",$MyLoVe);
		bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"قفل کانال روی کانال $MyLoVe تنظیم شد.",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]);
	}
}
elseif($MyLoVe == "اخطار دادن📛" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getid";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی فرد را ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "getid" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(file_exists("DaTaBoT/$MyLoVe/$MyLoVe.json")){
$NsdjWi["step"] = "sendwarn";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/id.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"چند اخطار به کاربر داده شود؟!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"همچین کاربری در ربات وجود ندارد
آیدی عددی درست ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($step == "sendwarn" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(is_numeric($MyLoVe)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$id = file_get_contents("DaTaBoT/id.txt");
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$inv = $NsdjWi["warn"];
$newin = $inv + $MyLoVe;
$NsdjWi["warn"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"💢از طرف مدیریت به شما *$MyLoVe* اخطار داده شد!",
'parse_mode'=>"MarkDown",
]); 
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"*$MyLoVe* اخطار به *$id* داده شد",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عدد ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
//////////----------------/////////////////////////////////////////
elseif($MyLoVe == "اهدای امتیاز💎" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getid2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی فرد را ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "getid2" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private' and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(file_exists("DaTaBoT/$MyLoVe/$MyLoVe.json")){
$NsdjWi["step"] = "sendcoin2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/id.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"چند امتیاز به کاربر داده شود؟!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"همچین کاربری در ربات وجود ندارد
آیدی عددی درست ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($step == "sendcoin2" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(is_numeric($MyLoVe)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$id = file_get_contents("DaTaBoT/id.txt");
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$inv = $NsdjWi["coin"];
$newin = $inv + $MyLoVe;
$NsdjWi["coin"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"از طرف مدیریت به شما *$MyLoVe* امتیاز داده شد!",
'parse_mode'=>"MarkDown",
]); 
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"*$MyLoVe* امتیاز به *$id* ارسال گردید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عدد ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
//----------------------------------------------------------------------
elseif($MyLoVe == "🎁ساخت کد هدیه🎁" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getid2gg";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"کد هدیه جدید را ارسال نمایید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "getid2gg" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "sendcoin2gg";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("newgiftm.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"این کد شامل چند امتیاز باشد؟",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "sendcoin2gg" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(is_numeric($MyLoVe)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$newgiftm = file_get_contents("newgiftm.txt");
file_put_contents("DaTaBoT/codes/$newgiftm.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"کد *$newgiftm* به ارزش *$MyLoVe* امتیاز با موفقیت ساخته شد",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
bot('sendMessage', [
'chat_id' =>"@$chads",
'text' => "💎کد امتیازی جدید ساخته شد👌

➖➖➖➖➖➖➖➖➖➖➖➖
🏷کد⬅️: $newgiftm

💎امتیاز : $MyLoVe
➖➖➖➖➖➖➖➖➖➖➖➖

@[*[USERNAME]*]",
]);
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عدد ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
//----------------------------------------------------------------------
elseif($MyLoVe == "حذف اخطار✅" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getids";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی فرد را ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

elseif($step == "getids" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(file_exists("DaTaBoT/$MyLoVe/$MyLoVe.json")){
$NsdjWi["step"] = "sendwarns";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/id.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"چند اخطار از کاربر کسر شود؟!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"همچین کاربری در ربات وجود ندارد
آیدی عددی درست ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($step == "sendwarns" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(is_numeric($MyLoVe)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$id = file_get_contents("DaTaBoT/id.txt");
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$inv = $NsdjWi["warn"];
$newin = $inv - $MyLoVe;
$NsdjWi["warn"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"☑️از طرف مدیریت از شما *$MyLoVe* اخطار کسر گردید!",
'parse_mode'=>"MarkDown",
]); 
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"*$MyLoVe* اخطار از *$id* کسر گردید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عدد ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
////////////------------//////////////////////////////////////////////////
elseif($MyLoVe == "کسر امتیاز💎" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getids2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی فرد را ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "getids2" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(file_exists("DaTaBoT/$MyLoVe/$MyLoVe.json")){
$NsdjWi["step"] = "sendcoins2";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/id.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"چند امتیاز از کاربر کسر شود؟!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"همچین کاربری در ربات وجود ندارد
آیدی عددی درست ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($step == "sendcoins2" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(is_numeric($MyLoVe)){
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$id = file_get_contents("DaTaBoT/id.txt");
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$inv = $NsdjWi["coin"];
$newin = $inv - $MyLoVe;
$NsdjWi["coin"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"از طرف مدیریت از شما *$MyLoVe* امتیاز کسر گردید!",
'parse_mode'=>"MarkDown",
]); 
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"*$MyLoVe* امتیاز از *$id* کسر گردید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"لطفا عدد ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($MyLoVe == "🕴🏻پیام به کاربر🕴🏻" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "getid2000";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"آیدی عددی دریافت کننده ی پیام را ارسال نمایید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "getid2000" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
if(file_exists("DaTaBoT/$MyLoVe/$MyLoVe.json")){
$NsdjWi["step"] = "sendcoin2000";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/id.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"متن ارسالی به کاربر مورد نظر را ارسال نمایید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}else{
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"همچین کاربری در ربات وجود ندارد
آیدی عددی درست ارسال کنید!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}}
elseif($step == "sendcoin2000" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$id = file_get_contents("DaTaBoT/id.txt");
$NsdjWi = json_decode(file_get_contents("DaTaBoT/$id/$id.json"),true);
$inv = $NsdjWi["coin"];
$newin = $inv + $MyLoVe;
$NsdjWi["coin"] = "$newin";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$id/$id.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$id,
'text'=>"💢یک پیام از طرف مدیریت دریافت کرده اید
----------------------
$MyLoVe",
'parse_mode'=>"MarkDown",
]); 
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"با موفقیت به $id ارسال گردید

متن پیام
------------------------
$MyLoVe",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
////////////----/////////////////////////////////////////////////
//----------------------------------------------------------------------
elseif($MyLoVe == "تنظیم متن 💡راهنما" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT21";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"متن 💡راهنما را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT21" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghavanin.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "🔵 تنظیم موجودی جت ممبر" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT212121";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"مقدار موجودی جدید رو بگویید!
مقدار فعلی👉🏻  $ghavanin1",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT212121" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghavanin1.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "🟢 تنظیم موجودی سرعت ممبر" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT2121212";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"مقدار موجودی جدید رو بگویید!
مقدار فعلی👉🏻  $ghavanin4",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT2121212" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghavanin4.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "💚 تنظیم موجودی ریپدسین" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT2121211";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"مقدار موجودی جدید رو بگویید!
مقدار فعلی👉🏻  $ghavanin3",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT2121211" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghavanin3.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
///////////////////////
elseif($MyLoVe == "💙 تنظیم موجودی نیترو سین" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT21212121";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"مقدار موجودی جدید رو بگویید!
مقدار فعلی👉🏻  $ghavanin2",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT21212121" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghavanin2.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

//////////////////////////
elseif($MyLoVe == "تنظیم متن 💰 تعرفه" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT212222";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"متن تعرفه را ارسال کنید",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT212222" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ghghghghg.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe == "تنظیم امتیاز زیرمجموعه گیری" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT24";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"امتیاز زیرمجموعه گیری را وارد نمایید
مثال : 10",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT24" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/invitecoin.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
///////////////////////////////////////////---//////
elseif($MyLoVe == "تنظیم متن بنر عکسدار" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "ZiRtExT";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"متن زیرمجموعه گیری را ارسال کنید
به جای نام NAME
به جای لینک LINK
و به جای نام خانوادگی LAST
و به جای آیدی عددی ID

را در متن قرار دهید تا جایگزین شود!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "ZiRtExT" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
file_put_contents("DaTaBoT/ZiRtExT.txt",$MyLoVe);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تنظیم شد!",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔰 تنظیمات موجودی"]],
[['text'=>"تنظیم متن و امتیاز ⚙"],['text'=>"☑ افزودن ادمین"]],
[['text'=>"🏞 تنظیم عکس 🏞"],['text'=>"🆑 تنظیم کانال"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}

//----------------------------------------------------------------------
elseif($MyLoVe == "پیام همگانی💬" and $chat == 'private'){	
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "send2all";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"پیام خود رو بفرست",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($step == "send2all" and $MyLoVe != "🔙 پنل مدیریت" and $chat == 'private'){ 
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$All_MembEr = fopen( "DaTaBoT/ozvs.txt", 'r');
while( !feof( $All_MembEr)) {
$user = fgets( $All_MembEr);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>$MyLoVe,
'parse_mode'=>"MarkDown",
]);
}
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"پیام به همه ارسال شد",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
//----------------------------------------------------------------------
elseif($MyLoVe == "فروارد همگانی💭" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "f2all";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"پیام خودت رو فور بده اینجا",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}}
elseif($MyLoVe != "🔙 پنل مدیریت" and $step == "f2all" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "free";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
$All_MembEr = fopen( "DaTaBoT/ozvs.txt", 'r');
while( !feof( $All_MembEr)) {
$user = fgets( $All_MembEr);
bot('ForwardMessage',[
'chat_id'=>$user,
'from_chat_id'=>$pViD,
'message_id'=>$message_id
]);
}    
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"فروارد همگانی به همه اعضای ربات فروارد شد",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]);
}}
elseif($MyLoVe == "💎امتیاز همگانی💎" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "sekhame";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"🧮 خب تعداد امتیاز رو وارد کنید.",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 پنل مدیریت"]],
],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
}
}

elseif($MyLoVe != "🔙 پنل مدیریت" and $step == "sekhame" and $chat == 'private'){
if ($pViD == $admin or $pViD == $admin2 or $pViD == $admin3) {
$NsdjWi["step"] = "none";
$OuTjSoN = json_encode($NsdjWi,true);
file_put_contents("DaTaBoT/$iDpV/$iDpV.json",$OuTjSoN);
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"در حال فرستادن $MyLoVe امتیاز برای همه اعضا",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"📈 آمار ربات"],['text'=>"ارسال پیام💬"]],
[['text'=>"مبادلات امتیازی♻️"],['text'=>"🎁ساخت کد هدیه🎁"]],
[['text'=>"⛔️ اخطار دادن و حذف اخطار✅"]],
[['text'=>"🔙 پنل مدیریت"]],

],
"resize_keyboard"=>true,'one_time_keyboard' => true,
])
]); 
$All_MembEr = file_get_contents("DaTaBoT/ozvs.txt");
$ex = explode("\n",$All_MembEr);
$cplug = count($ex) - 2;
for($n=0; $n<=$cplug; $n++) {
$user = $ex[$n];
$gfgfgfgf = json_decode(file_get_contents("DaTaBoT/$user/$user.json"),true);
$inv = $gfgfgfgf["coin"];
$newin = $inv + $MyLoVe;
$gfgfgfgf["coin"] = "$newin";
$fvdsfvdsfv = json_encode($gfgfgfgf,true);
file_put_contents("DaTaBoT/$user/$user.json",$fvdsfvdsfv);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"همگانی ‼️
به پاس قدر دانی از شما کاربران گرامی از طرف مدیریت مقدار$MyLoVe امتیاز💎 به همه کاربران ربات  اهدا گردید.
",
'parse_mode'=>"html"
]); 
}
bot('sendMessage',[
'chat_id'=>$pViD,
'text'=>"تعداد $MyLoVe امتیاز با موفقیت برای همه اعضا ارسال شد.",
'parse_mode'=>"html"
]);
}
}
}
if(file_exists(error_log))
	unlink(error_log);
?>
