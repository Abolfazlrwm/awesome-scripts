<?php
flush();
ob_start();
ob_implicit_flush(1);
include 'config.php';
include("class.php");
include 'jdf.php';
//========================== // bot // ==============================
function bot($method, $dats = []){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.API_KEY.'/'.$method);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dats);
$curl_exec = curl_exec($ch);
return json_decode($curl_exec,true);
}
//========================== // perti // ==============================
function perti($methode,$datase=[]){
$chh = curl_init();
curl_setopt($chh,CURLOPT_URL,'https://api.telegram.org/bot'.PARTNER_BOT_TOKEN.'/'.$methode );
curl_setopt($chh,CURLOPT_RETURNTRANSFER,true);
curl_setopt($chh,CURLOPT_POSTFIELDS,$datase);
return json_decode(curl_exec($chh));
}
//----------------------------------------//
function openurl($url){
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
return $data;
}
//----------------------------------------//
$chanel_post = file_get_contents("kodam/channel3.txt");
//----------------------------------------//
$channel = file_get_contents("kodam/channel.txt");
$channel2 = file_get_contents("kodam/channel2.txt");
$cr_robot = file_get_contents("sharge_cr_bot.txt");
$sharge_panel_seen = file_get_contents("panelseen.txt");
$list = json_decode(file_get_contents("kodam/list.json"),true);
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("Button/start.txt")){
$mtns = file_get_contents("Button/start.txt");
}else{
$mtns = "برای ادامه یکی از دکمه های زیر را انتخاب نمایید:";
}
//////////------------------------\\\\\\\\\\\\\\
$Button_Home = json_encode(['keyboard'=>[
[['text'=>"$line1_1"],['text'=>"$line1_2"],['text'=>"$line1_3"],['text'=>"$line1_4"]],
[['text'=>"$line2_1"],['text'=>"$line2_2"],['text'=>"$line2_3"],['text'=>"$line2_4"]],
[['text'=>"$line3_1"],['text'=>"$line3_2"],['text'=>"$line3_3"],['text'=>"$line3_4"]],
[['text'=>"$line4_1"],['text'=>"$line4_2"],['text'=>"$line4_3"],['text'=>"$line4_4"]],
[['text'=>"$line5_1"],['text'=>"$line5_2"],['text'=>"$line5_3"],['text'=>"$line5_4"]],
[['text'=>"$line6_1"],['text'=>"$line6_2"],['text'=>"$line6_3"],['text'=>"$line6_4"]],
[['text'=>"$line7_1"],['text'=>"$line7_2"],['text'=>"$line7_3"],['text'=>"$line7_4"]],
[['text'=>"$line8_1"],['text'=>"$line8_2"],['text'=>"$line8_3"],['text'=>"$line8_4"]],

],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\
$Button_Admins_Home = json_encode(['keyboard'=>[
[['text'=>"$line1_1"],['text'=>"$line1_2"],['text'=>"$line1_3"],['text'=>"$line1_4"]],
[['text'=>"$line2_1"],['text'=>"$line2_2"],['text'=>"$line2_3"],['text'=>"$line2_4"]],
[['text'=>"$line3_1"],['text'=>"$line3_2"],['text'=>"$line3_3"],['text'=>"$line3_4"]],
[['text'=>"$line4_1"],['text'=>"$line4_2"],['text'=>"$line4_3"],['text'=>"$line4_4"]],
[['text'=>"$line5_1"],['text'=>"$line5_2"],['text'=>"$line5_3"],['text'=>"$line5_4"]],
[['text'=>"$line6_1"],['text'=>"$line6_2"],['text'=>"$line6_3"],['text'=>"$line6_4"]],
[['text'=>"$line7_1"],['text'=>"$line7_2"],['text'=>"$line7_3"],['text'=>"$line7_4"]],
[['text'=>"$line8_1"],['text'=>"$line8_2"],['text'=>"$line8_3"],['text'=>"$line8_4"]],
[['text'=>"👤پنل مدیریت👤"]],
],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\///
$Button_Back_cr = json_encode(['keyboard'=>[
[['text'=>"🔙 بازگشت"]],
],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\///
$Button_Back = json_encode(['keyboard'=>[
[['text'=>'برگشت 🔙']],
],'resize_keyboard'=>true]);
$Button_Back_admin = json_encode(['keyboard'=>[
[['text'=>'🔙 برگشت']],
],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\///
$Button_Admins_Panel = json_encode(['keyboard'=>[
[['text'=>"📍 مدیریت کاربران"],['text'=>"📍 آمار ربات"]],
[['text'=>"📍 چیدمان کیبورد"],['text'=>"📍 تنظیمات سفارش"],['text'=>"📍 افزودن ادمین"]],
[['text'=>"📍 تنظیم دکمه"],['text'=>"📍 تنظیم امتیاز"],['text'=>"📍 تنظیم متن"]],
[['text'=>"📍 بروزرسانی"],['text'=>"📍 تنظیم کانال"],['text'=>"📍 اشتراک باقی مانده"]],
[['text'=>"🔙 بازگشت به ربات"]],
],'resize_keyboard'=>true]);
//////////------------------------\\\\\\\\\\\\\\
function SM($chatID,$msg,$mode,$reply = null,$keyboard = null){
$data = bot('SendMessage',['chat_id'=>$chatID,'text'=>$msg,'parse_mode'=>$mode,'reply_to_message_id'=>$reply,'reply_markup'=>$keyboard]);
return $data;
}
//////////------------------------\\\\\\\\\\\\\\
function Editmessagetext($chatID, $msg_id, $msg, $keyboard){
 bot('editmessagetext', [
'chat_id' => $chatID,
'message_id' => $msg_id,
  'text' => $msg,
  'reply_markup' => $keyboard
]);
//////////------------------------\\\\\\\\\\\\\\
}
function FM($chatID,$FC,$MI){
$data = bot('ForwardMessage',['chat_id'=>$chatID,'from_chat_id'=>$FC,'message_id'=>$MI]);
return $data;
}
//////////------------------------\\\\\\\\\\\\\\
function Sph($chatID,$pID,$Caption,$Mode,$keyboard = null){
$data = bot('sendphoto',['chat_id'=>$chatID,'photo'=>$pID,'caption'=>$Caption,'parse_mode'=>$Mode,'reply_markup'=>$keyboard]);
return $data;
}
//////////------------------------\\\\\\\\\\\\\\
function saveJson($file,$data){
$new_data = json_encode($data,true);
file_put_contents($file,$new_data);
}
//////////------------------------\\\\\\\\\\\\\\
function Save($filename,$TXTdata){
file_put_contents($filename,$TXTdata);
}
//////////------------------------\\\\\\\\\\\\\\
function DLM($chatID, $msg_id){
bot('deletemessage',['chat_id'=>$chatID,'message_id'=>$msg_id]);
}
//////////------------------------\\\\\\\\\\\\\\
function ACQ($cqi,$msg,$sa){
bot('answerCallbackQuery',['callback_query_id'=>$cqi,'text'=>$msg,'show_alert'=>$sa]);
}
//////////------------------------\\\\\\\\\\\\\\
function GCM($chatID,$userID){
$truechannel = bot('getChatMember',['chat_id'=>$chatID,'user_id'=>$userID]);
$Join = $truechannel['result']['status'];
return $Join;
}
//////////------------------------\\\\\\\\\\\\\\
function GCMB($chatID){
$data = bot('getChat',['chat_id'=>$chatID]);
return $data['ok'];
}
//////////------------------------\\\\\\\\\\\\\\
function GMN(){
$data = bot('getMe');
return $data['result']['first_name'];
}
//////////------------------------\\\\\\\\\\\\\\
function DL_Folder($directory){
if(!is_dir($directory))
return;
$contents = scandir($directory);
unset($contents[0], $contents[1]);
foreach($contents as $object){
$current_object = $directory.'/'.$object;
if(filetype($current_object) === 'dir'){
DL_Folder($current_object);
}else{
unlink($current_object);
}}
rmdir($directory);
}
//////////------------------------\\\\\\\\\\\\\\//
function top($number,$invite){
$usersscan = scandir('melat');
unset($usersscan[0]); 
unset($usersscan[1]); 
unset($usersscan[2]); 
foreach ($usersscan as $userlist){
$users = json_decode(file_get_contents('melat/'.$userlist),true);
$userlistsave[$userlist] = $users[$invite]; 
} 
$chakhesh = $userlistsave; 
arsort($chakhesh,SORT_NUMERIC); 
$charkhesharray = array();
foreach ($chakhesh as $key=>$value){
$charkhesharray[] = $key;  
}
$neshonbde = str_replace('.json','',$charkhesharray[$number]);
return $neshonbde;
}
//////////------------------------\\\\\\\\\\\\\\
$up = json_decode(file_get_contents('php://input'));
$message = $up->message;
$msg = $message->text;
$callback_query = $up->callback_query;
$data = $callback_query->data;
if(isset($message)){
$chatID = $message->chat->id;
$msg_id = $message->message_id;
$userID = $message->from->id;
$first_name = $message->from->first_name;
$username = $message->from->username;
$Tc = $message->chat->type;
}
//////////------------------------\\\\\\\\\\\\\\
if(isset($callback_query)){
$data_id = $callback_query->id;
$chatID = $callback_query->message->chat->id;
$inline_keyboard = $callback_query->message->reply_markup->inline_keyboard;
$userID = $callback_query->from->id;
$first_name = $callback_query->from->first_name;
$username = $callback_query->from->username;
$Tc = $callback_query->message->chat->type;
$msg_id = $callback_query->message->message_id;
}
//////////------------------------\\\\\\\\\\\\\\//
if(isset($userID) and is_file("melat/$userID.json")){
$user = json_decode(file_get_contents("melat/$userID.json"),true);
$step = $user['step'];
$Points = $user['Points'];
$create = $user['create'];
$zirmjmae = $user['zirmjmae'];
}
//=====================================================
if($channel != null or $channel != ''){
$Join = GCM("@$channel",$userID);
}else{
$Join = 'member';
}
if($channel2 != null or $channel2 != ''){
$Join2 = GCM("@$channel2",$userID);
}else{
$Join2 = 'member';
}
//=====================================================
if(in_array($chatID,$list['admins'])){
$Button_Home = $Button_Admins_Home;
}
//=====================================================
if(is_file("kodam/jijinanahekose.json")){
$lists = json_decode(file_get_contents("kodam/jijinanahekose.json"),true);
$ban = $lists['ban'];
}
//=====================================================
if(in_array($chatID,$ban) and $userID != $list['admins'][0]){
exit();
}
//=====================================================
//=====================================================
function Spam($userID){
$spam_status = json_decode(file_get_contents("spam/$userID.json"),true);
if($spam_status != null){
if(mb_strpos($spam_status[0],"time") !== false){
if(str_replace("time ",null,$spam_status[0]) >= time())
exit(false);
else
$spam_status = [1,time()+2];
}
elseif(time() < $spam_status[1]){
if($spam_status[0]+1 > 3){
$time = time() + 15;
$spam_status = ["time $time"];
file_put_contents("spam/$userID.json",json_encode($spam_status,true));
bot('SendMessage',[
'chat_id'=>$userID,
'text'=>"⚠️کمی اهسته تر با ربات کار کنید

⛔️حساب شما به مدت ۱۵ ثانیه محدود شد",
]);
exit(false);
}else{
$spam_status = [$spam_status[0]+1,$spam_status[1]];
}}else{
$spam_status = [1,time()+2];
}}else{
$spam_status = [1,time()+2];
}
file_put_contents("spam/$userID.json",json_encode($spam_status,true));
}
Spam($userID);
//=====================================================
date_default_timezone_set('Asia/Tehran'); 
$sharge = file_get_contents("Lite.txt");
$a = date('Y/m/d');
$b = "$sharge";
$sec = strtotime($b)-strtotime($a);
$days = $sec/86400;
$d0ays = explode('.',$days)[0];
if(!$d0ays >= 1){
if(in_array($chatID,$list['admins'])){
SM($chatID,"⚠️ زمان اشتراک ماهیانه ربات شما به پایان رسید.",'html');
exit();
}else{
$nemebot = GMN();
SM($chatID,"⚠️ مدت زمان اشتراک ماهیانه ربات $nemebot به پایان رسیده است.",'html');
exit();   
}}
//////////------------------------\\\\\\\\\\\\\\//
//////////------------------------\\\\\\\\\\\\\\///
else if($data == 'join' and $Tc = 'private'){
if(($Join == 'member' or $Join == 'creator' or $Join == 'administrator') and ($Join2 == 'member' or $Join2 == 'creator' or $Join2 == 'administrator') and $userID != $list['admins'][0]){
DLM($chatID,$msg_id);
SM($chatID,"❕حساب  کاربری شما تایید شد. همکنون میتوانید از ربات استفاده کنید❕",'MarkDown',null,$Button_Home);
}else{
if($Join2 != 'member' and $Join2 != 'creator' and $Join2 != 'administrator'){
ACQ($data_id,"❌ هنوز داخل کانال های @$channel و @$channel2 عضو نیستید",true);
}else{
ACQ($data_id,"❌ هنوز داخل کانال @$channel عضو نیستید",true);
}}
exit(); 
}
//////////------------------------\\\\\\\\\\\\\\///
//////////------------------------\\\\\\\\\\\\\\//
if(isset($user['start']) and ($Join == 'member' or $Join == 'creator' or $Join == 'administrator') and ($Join2 == 'member' or $Join2 == 'creator' or $Join2 == 'administrator') and $Tc == 'private' and $userID != $list['admins'][0]){
$user = json_decode(file_get_contents("melat/$userID.json"),true);
$start = $user['start'];
unset($user['start']);
$ustart = json_decode(file_get_contents("melat/$start.json"),true);
$isset = $ustart['zirmjmae'] + 1;
$ustart['zirmjmae'] = $isset;
$Pointsplus = $ustart['Points'] + $zirmjmaec;
$ustart['Points'] = $Pointsplus;
saveJson("melat/$start.json",$ustart);
$nemebot = GMN();
SM($start,"❇️تبریک!!!\n یک کاربر با لینک فعالسازی شما عضو $nemebot شد  
$zirmjmaec سکه 
به شما اضافه شد",'html');
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\//
$vvltp = jdate("Y/m/d");
//=====================================================
if($msg == '/start' and $Tc == 'private'){
SM($chatID,"$mtns",'html',$msg_id,$Button_Home);
if(!is_file("melat/$userID.json")){
$user['step'] = 'none';
$user['Points'] = $starts;
$user['zirmjmae'] = 0;
$user['sefareshorg'] = 0;
$user['fakeviewkasr'] = 0;
$user['data-start'] = "$vvltp";
$user['warn'] = 0;
$user['coinadmin'] = 0;
$user['orgviewkasb'] = 0;
$user['masrafi'] = 0;
$user['enteghal_as'] = 0;
$user['enteghal_to'] = 0;
$user['bots'] = 'no';
$user["panel"] = 'normal';
$user['idbot'] = null;
}else{
$user['step'] = 'none';
}
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\//
else if(strpos($msg , '/start ') !== false and $Tc == 'private'){
$start = str_replace("/start ",null,$msg);
if(strpos($start,"-100") !== false or strpos($start,'@') !== false){
exit();
}
if(!is_file("melat/$userID.json")){
SM($chatID,"$mtns",'html',$msg_id,$Button_Home);
$user['step'] = 'none';
$user['Points'] = $starts;
$user['zirmjmae'] = 0;
$user['sefareshorg'] = 0;
$user['fakeviewkasr'] = 0;
$user['data-start'] = "$vvltp";
$user['warn'] = 0;
$user['coinadmin'] = 0;
$user['orgviewkasb'] = 0;
$user['masrafi'] = 0;
$user['enteghal_as'] = 0;
$user['enteghal_to'] = 0;
$user['bots'] = 'no';
$user["panel"] = 'normal';
$user['idbot'] = null;
$user['start'] = $start;
if(is_file("melat/$start.json")){
if(($Join != 'member' and $Join != 'creator' and $Join != 'administrator') or ($Join2 != 'member' and $Join2 != 'creator' and $Join2 != 'administrator') and $userID != $list['admins'][0]){
$user['start'] = $start;
}else{
$ustart = json_decode(file_get_contents("melat/$start.json"),true);
$isset = $ustart['zirmjmae'] + 1;
$ustart['zirmjmae'] = $isset;
$Pointsplus = $ustart['Points'] + $zirmjmaec;
$ustart['Points'] = $Pointsplus;
saveJson("melat/$start.json",$ustart);
$nemebot = GMN();
SM($start,"❇️تبریک!!!\n یک کاربر با لینک فعالسازی شما عضو $nemebot شد  
$zirmjmaec سکه 
به شما اضافه شد",'html');
}}
}else{
$user['step'] = 'none';
SM($chatID,"$mtns",'html',$msg_id,$Button_Home);
}
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\//
else if($Join != 'member' and $Join != 'creator' and $Join != 'administrator' and $Tc == 'private' and $userID != $list['admins'][0]){
if($Join2 != 'member' and $Join2 != 'creator' and $Join2 != 'administrator' and $userID != $list['admins'][0]){
$nemebot = GMN();
SM($chatID,"برای استفاده از ربات « $nemebot » ابتدا عضو کانال های زیر شوید و سپس مجددا استارت کنید ♻️

📣 @$channel
📣 @$channel2

👇 بعد از « عضویت » بروی دکمه « ✅تایید عضویت » کلیک کنید 👇",'html',$msg_id, $Button_Join);

}else{
$nemebot = GMN();
SM($chatID,"برای استفاده از ربات « $nemebot » ابتدا عضو کانال های زیر شوید و سپس مجددا استارت کنید ♻️

📣 @$channel
📣 @$channel

👇 بعد از « عضویت » بروی دکمه « ✅تایید عضویت » کلیک کنید 👇",'html',$msg_id,$Button_Join);
}
exit();
}
//////////------------------------\\\\\\\\\\\\\\//
else if($Join2 != 'member' and $Join2 != 'creator' and $Join2 != 'administrator' and $userID != $list['admins'][0]){
$nemebot = GMN();
SM($chatID,"برای استفاده از ربات « $nemebot » ابتدا عضو کانال های زیر شوید و سپس مجددا استارت کنید ♻️

📣 @$channel2
📣 @$channel2

👇 بعد از « عضویت » بروی دکمه « ✅تایید عضویت » کلیک کنید 👇",'html',$msg_id,$Button_Join);
exit();
}
//////////------------------------\\\\\\\\\\\\\\//
else if($msg == 'برگشت 🔙' and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"به منوی اصلی بازگشتید ↪️",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
exit();
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '🔙 بازگشت به ربات' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'none';
SM($chatID,"🔖به منوی اصلی برگشتیم.
لطفا یک گزینه را انتخاب کنید!",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
exit();
}
//////////------------------------\\\\\\\\\\\\\\///
//----------------------------------------/////
else if($data == 'backcrview' and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"🔖به منوی اصلی برگشتیم.
لطفا یک گزینه را انتخاب کنید!",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == "$dok1" and $Tc == 'private'){
$user['step'] = 'crview';
$Button_oin = json_encode(['inline_keyboard'=>[
[['text' => "💰 بازدید دلخواه",'callback_data' => "bazdid delkhah"]],
[['text'=>"$dokt1",'callback_data'=>"orgs-$dokk1-$dokc1"],['text'=>"$dokt2",'callback_data'=>"orgs-$dokk2-$dokc2"]],
[['text'=>"$dokt3",'callback_data'=>"orgs-$dokk3-$dokc3"],['text'=>"$dokt4",'callback_data'=>"orgs-$dokk4-$dokc4"]],
[['text'=>"$dokt5",'callback_data'=>"orgs-$dokk5-$dokc5"],['text'=>"$dokt6",'callback_data'=>"orgs-$dokk6-$dokc6"]],
[['text'=>"$dokt7",'callback_data'=>"orgs-$dokk7-$dokc7"],['text'=>"$dokt8",'callback_data'=>"orgs-$dokk8-$dokc8"]],
[['text'=>"$dokt9",'callback_data'=>"orgs-$dokk9-$dokc9"],['text'=>"$dokt10",'callback_data'=>"orgs-$dokk10-$dokc10"]],
[['text'=>"🔙 بازگشت",'callback_data'=>'backcrview']],
]]);
SM($chatID,"👁‍🗨تعداد بازدید مورد نظر برای ثبت تبلیغ خود را انتخاب نمایید .",'html',$msg_id,$Button_oin);
}
//////////------------------------\\\\\\\\\\\\\\
elseif($data == 'bazdid delkhah'){
if($Points > 0){
SM($chatID,"📊 تعداد بازدید مورد نظر خود را بین $delmin الی $delmax به صورت لاتین انتخاب نمایید تا در معرض دید کاربران قرار بگیرد.",'html',$msg_id,$Button_Back);
$user['step'] = "delpostorg";
saveJson("melat/$userID.json",$user);
}else{
ACQ($data_id,"❌ جهت سفارش بازدید حداقل باید شما 1 سکه در حساب خود داشته باشید.",true);
}}
//////////------------------------\\\\\\\\\\\\\\
elseif($step == 'delpostorg'){
if(is_numeric($msg) and $msg >= $delmin && $msg <= $delmax){
if($Points >= $msg * 2){
$hvk = $msg * 2;
SM($chatID,"✅ تبلیغ، متن، تصویر و یا پست خود را فوروارد نمایید.
 
⚠️ توجه داشته باشید ثبت تبلیغ دارای محتوا های خلاف قوانین ایران اسلامی، کلاهبرداری، توهین و فحاشی، تبلیغ ربات های مشابه، پخش شماره سایر افراد و... غیر مجاز میباشد. در صورت ثبت چنین پست هایی حساب کاربر بدون اطلاع قبلی بسته خواهد شد. (قبل از ثبت تبلیغات، کلیه قوانین ما را از طریق دکمه قوانین و مقررات بررسی نمایید.)",'MarkDown',null,$Button_Back);
$user['step'] = "setpostorg-$msg-$hvk";
saveJson("melat/$userID.json",$user);
}else{
SM($chatID,"❗️ خطا ، میزان بازدید وارد شده از موجودی شما بیشتر است
هزینه هر بازدید ضریب 2 میباشد",'MarkDown',null,$Button_Back);
}}else{
SM($chatID,"❗️ خطا ، پیام شما دارای عدد ورودی نادرست است

👇🏻 تعداد بازدید دلخواه را به صورت عدد بین $delmin الی $delmax وارد نمایید",'MarkDown',null,$Button_Back);
}}
//////////------------------------\\\\\\\\\\\\\\
elseif(preg_match('/^orgs-(.*)-(.*)/', $data, $match)){
if($Points >= $match[2]){
SM($chatID,"✅ تبلیغ، متن، تصویر و یا پست خود را فوروارد نمایید.
 
⚠️ توجه داشته باشید ثبت تبلیغ دارای محتوا های خلاف قوانین ایران اسلامی، کلاهبرداری، توهین و فحاشی، تبلیغ ربات های مشابه، پخش شماره سایر افراد و... غیر مجاز میباشد. در صورت ثبت چنین پست هایی حساب کاربر بدون اطلاع قبلی بسته خواهد شد. (قبل از ثبت تبلیغات، کلیه قوانین ما را از طریق دکمه قوانین و مقررات بررسی نمایید.)",'html',$msg_id,$Button_Back);
$user['step'] = "setpostorg-$match[1]-$match[2]";
saveJson("melat/$userID.json",$user);
}else{
ACQ($data_id,"❌ جهت سفارش بازدید حداقل باید شما $match[2] سکه در حساب خود داشته باشید.",true);
}}
//////////------------------------\\\\\\\\\\\\\\
elseif(preg_match('/^setpostorg-(.*)-(.*)/', $step, $match)){
if($message->forward_from_chat == true){
$Buton_oin = json_encode(['inline_keyboard'=>[
[['text'=>"✅بله",'callback_data'=>"forpostorg-$match[1]-$match[2]-$msg_id"],['text'=>"❌خیر",'callback_data'=>'backcrview']],
]]);
SM($chatID,"⁉️آیا مایل به ثبت تبلیغ فوق به میزان $match[1] بازدید هستید؟",'html',$msg_id,$Buton_oin);
$user['step'] = "none";
saveJson("melat/$userID.json",$user);
}}
elseif(preg_match('/^forpostorg-(.*)-(.*)-(.*)/', $data, $match)){
$id = FM("@$chanel_post",$chatID,$match[3])['result']['message_id'];
bot('sendMessage',[
'chat_id' => "@$chanel_post",
'text' => "👁‍🗨 بازدید درخواستی : {$match[1]}",
'reply_to_message_id' => $id,
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => 'بازدید👁‍🗨','callback_data' => "seen-$id"],
['text' => "گزارش⛔️",'callback_data' => "Report-$id"],
['text' => 'ربات🔜','url' => 'https://t.me/'.$boter_id]]
]])]);
$Button_channel = json_encode(['inline_keyboard'=>[
[['text'=>"مشاهده تبلیغ در کانال",'url'=>"http://telegram.me/$chanel_post/$id"]],
]]);
SM($chatID,"✅ تبلیغ شما با موفقیت ثبت شد !!

🔍 کد پیگیری سفارش شما $id می باشد
 
👈 تبلیغ شما در قسمت پیگیری سفارشات قابل پیگیری است.",'html',null,$Button_channel);
$user['step'] = 'none';
$user['Points'] = $Points - $match[2];
saveJson("melat/$userID.json",$user);
$order['admin'] = $userID;
$order['coin'] = $match[2];
$order['code'] = $id;
$order['tedad'] = '0';
$order['Report'] = null;
$order['members'] = null;
$order['seen'] = $match[1];
$order['stats'] = 'در حال اجرا ♻️';
file_put_contents("orderseen/$id.json", json_encode($order, 448));
$ustart = json_decode(file_get_contents("melat/$userID.json"),true);
$Pointsplus = $ustart['sefareshorg'] + 1;
$ustart['sefareshorg'] = $Pointsplus;
$Pointsplus = $ustart['masrafi'] + $match[2];
$ustart['masrafi'] = $Pointsplus;
saveJson("melat/$userID.json",$ustart);
}
//##########################################################################
elseif(preg_match('/^seen-(.*)/', $data, $match)){
if(is_file("melat/$userID.json")){
$order = json_decode(file_get_contents("orderseen/$match[1].json"),true);
if($userID !== $order['admin']){
if(!in_array($userID,$order['members'])){
//========
$coin = $order['tedad'] + 1;
$order['tedad']= $coin;
$order['members'][] = $userID;
//========
$seke = "2";
$ustart = json_decode(file_get_contents("melat/$userID.json"),true);
$Posplus = $ustart['orgviewkasb'] + 1;
$ustart['orgviewkasb'] = $Posplus;
$Pointsplus = $ustart['Points'] + $seke;
$ustart['Points'] = $Pointsplus;
saveJson("melat/$userID.json",$ustart);
//========
ACQ($data_id,"🎖 $seke سکه دریافت شد | موجودی: $Pointsplus",false);
//========
if($order['tedad'] >= $order['seen']){
DLM("@$chanel_post",$msg_id);
DLM("@$chanel_post",$order['code']);
$order['stats'] = 'تکمیل شده✅';
SM($order['admin'],"✅ سفارش شما به کد پیگیری {$order['code']} با موفقیت تکمیل شد.

تعداد درخواست: {$order['seen']} بازدید
تکمیل در {$order['tedad']} بازدید
هزینه سفارش {$order['coin']} سکه",'html');
}
//========
file_put_contents("orderseen/$match[1].json", json_encode($order, 448));
}else{
ACQ($data_id,"❌ شما قبلا سکه این تبلیغ را دریافت کرده اید",true);
}}else{
ACQ($data_id,"❌امکان دریافت سکه از پست خودتان ندارید❌",true);
}}else{
ACQ($data_id,"❌ شما عضو ربات نمیباشید",true);
}}
//##########################################################################
elseif(preg_match('/^Report-(.*)/', $data, $match)){
if(is_file("melat/$userID.json")){
$order = json_decode(file_get_contents("orderseen/$match[1].json"),true);
if(!in_array($userID,$order['Report'])){
$order['Report'][] = $userID;
ACQ($data_id,"گزارش شما ثبت شد ✔️",false);
if($datas['Report-text']=='✅فعال'){
SM($admins,"پست زیر توسط $userID گزارش شد:\n\nhttps://t.me/$chanel_post/$match[1]",'html');
}
//========
file_put_contents("orderseen/$match[1].json", json_encode($order, 448));
}else{
ACQ($data_id,"گزارش شما قبلا ثبت شده است ❌",true);
if($userID == $admins){
ACQ($data_id,"✅ این پست حذف گردید",true);
DLM("@$chanel_post",$msg_id);
DLM("@$chanel_post",$order['code']);
SM($order['admin'],"به دلیل ثبت گزارش مکرر برای سفارش {$order['code']} این سفارش از کانال حذف گردید 
و وضعیت سفارش به حالت کنسل شده❌ در آمد !️",'html');
$order['stats'] = 'کنسل شده❌';
//========
file_put_contents("orderseen/$match[1].json", json_encode($order, 448));
}}
}else{
ACQ($data_id,"❌ شما عضو ربات نمیباشید",true);
}}
//##########################################################################
elseif(strpos($msg,"/ord_") !== false){
$id = str_replace("/ord_","",$msg);
if(is_file("orderseen/$id.json")){
$order = json_decode(file_get_contents("orderseen/$id.json"),true);
if($userID == $order['admin']){
if($order['stats'] == 'در حال اجرا ♻️'){
bot('ForwardMessage',[
'chat_id'=>$userID,
'from_chat_id'=>"@$chanel_post",
'message_id'=>$id,
]);
}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 این سفارش فعال نمیباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 این سفارش متعلق به شما نمیباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 کد پیگیری سفارش نامعتبر میباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}
//##########################################################################
elseif(strpos($msg,"/cancell_") !== false){
$id = str_replace("/cancell_","",$msg);
if(is_file("orderseen/$id.json")){
$order = json_decode(file_get_contents("orderseen/$id.json"),true);
if($userID == $order['admin']){
if($order['stats'] == 'در حال اجرا ♻️'){
$msgid = bot('ForwardMessage',[
'chat_id'=>$userID,
'from_chat_id'=>"@$chanel_post",
'message_id'=>$id,
])->result->message_id;
$Buton_poin = json_encode(['inline_keyboard'=>[
[['text'=>"✅بله",'callback_data'=>"cancell-$id"],['text'=>"❌خیر",'callback_data'=>'backchancel']],
]]);
SM($chatID,"⁉️آیا مایل به لغو این تبلیغ هستید؟

⚠️توجه داشته باشید با ضریب 0.5 معادل بازدید های باقی مانده سکه به حساب شما بازگشت داده خواهد شد",'html',$msgid,$Buton_poin);
}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 این سفارش فعال نمیباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 این سفارش متعلق به شما نمیباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 کد پیگیری سفارش نامعتبر میباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}
//##########################################################################
elseif(preg_match('/^cancell-(.*)/', $data, $match)){
$order = json_decode(file_get_contents("orderseen/$match[1].json"),true);
$coins = $order['coin'] - $order['tedad'];
$coin = $coins / 2;
Editmessagetext($chatID, $msg_id,"تبلیغ شما با موفقیت لغو شد و مقدار $coin سکه به حساب  شما بازگردانده شد ✅
",null);
DLM("@$chanel_post",$order['code']);
DLM("@$chanel_post",$order['code'] + 1);
$order['stats'] = 'کنسل شده❌';
//========
file_put_contents("orderseen/$match[1].json", json_encode($order, 448));
$ustart = json_decode(file_get_contents("melat/$userID.json"),true);
$Pointsplus = $ustart['Points'] + $coin;
$ustart['Points'] = $Pointsplus;
saveJson("melat/$userID.json",$ustart);
}
//##########################################################################
//##########################################################################
else if($msg == "$dok11" and $Tc == 'private'){
$user['step'] = 'peyuser';
SM($chatID,"✅ #کد_پیگیری دریافتی از ربات را ارسال کنید :",'html',null,$Button_Back);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($step == 'peyuser' and $Tc = 'private'){
if(is_numeric($msg) and is_file("orderseen/$msg.json")){
$order = json_decode(file_get_contents("orderseen/$msg.json"),true);
if($order["admin"] == $userID){
$mylist = "
📄کد پیگیری سفارش : {$order["code"]}
⏳تعداد درخواستی : {$order["seen"]}
✅تعداد انجام شده : {$order["tedad"]}
📊وضعیت سفارش : {$order["stats"]}
👁‍🗨مشاهده تبلیغ : /ord_{$order["code"]}
❌لغو سفارش : /cancell_{$order["code"]}
💡نوع سفارش : بازدید واقعی

—----------------------------";
SM($chatID,"$mylist",'html',null,$Button_Home); 
$user['step'] = 'none';
saveJson("melat/$userID.json",$user);
}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 این سفارش متعلق به شما نمیباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}else{
$user['step'] = 'none';
SM($chatID,"👈🏻 کد پیگیری سفارش نامعتبر میباشد",'html',$msg_id,$Button_Home);
saveJson("melat/$userID.json",$user);
}}
//##########################################################################
else if($msg == "$dok4" and $Tc == 'private'){
$user['step'] = 'sendcoin';
SM($chatID,"$doktxt4",'html',$msg_id,$Button_Back);
saveJson("melat/$userID.json",$user);
}
///////////////////////////////////////////////////////////
else if($step == 'sendcoin' and $Tc == 'private'){
$all = explode("\n", $msg);
if(preg_match('/^([0-9])/',$all[0]) and is_file("melat/$all[0].json") and $all[0] != $userID){
if(preg_match('/^([0-9])/',$all[1]) and $all[1] > 0 and $all[1] <= $Points){
$user = json_decode(file_get_contents("melat/{$userID}.json"), 1);
$coin = $Points - $all[1];
$user['Points'] = $coin;
$Poiplus = $user['enteghal_to'] + $all[1];
$user['enteghal_to'] = $Poiplus;
saveJson("melat/$userID.json",$user);
$date = date('Y/m/d');
date_default_timezone_set('Asia/Tehran');
$time = date('H:i:s');
bot('sendmessage',[ 
'chat_id'=>$chatID,
'text'=>"✅ تعداد $all[1] سکه در تاریخ $date ساعت $time با موفقیت به کاربر {$all[0]} انتقال داده شد.",
'reply_to_message_id'=>$msg_id,
  'reply_markup'=>$Button_Home
]);
$users = json_decode(file_get_contents("melat/$all[0].json"),true);
$getusercoin = $users['Points'] + $all[1];
$users['Points'] = $getusercoin;
$Poiplus = $users['enteghal_as'] + $all[1];
$users['enteghal_as'] = $Poiplus;
$date = date('Y/m/d');
date_default_timezone_set('Asia/Tehran');
$time = date('H:i:s');
bot('sendmessage',[ 
'chat_id'=>$all[0],
'text'=>"✅ تعداد $all[1] سکه در تاریخ $date ساعت $time با موفقیت از کاربر $userID دریافت شد.",
]);
saveJson("melat/$all[0].json",$users);
$user['step'] = 'none';
saveJson("melat/$userID.json",$user);
}else{
bot('sendmessage',[ 
'chat_id'=>$chatID,
'text'=>"⚠️ تعداد سکه های شما برای انتقال کافی نمیباشد !",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Home
]);
$user['step'] = 'none';
saveJson("melat/$userID.json",$user);
}}else{
bot('sendmessage',[ 
'chat_id'=>$chatID,
'text'=>"⚠️ فردی با شناسه کاربری وارد شده در ربات یافت نشد ! لطفا شناسه فرد را با دقت وارد کنید
🎫 شناسه کاربری هر فرد در قسمت حساب من درج شده است  .",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Home
]);
$user['step'] = 'none';
saveJson("melat/$userID.json",$user);
}}
//##########################################################################
else if($msg == "$dok2" and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"
👤نام شما : $first_name
🆔یوزرنیم : @$username
🔖شماره کاربری: $userID
📆تاریخ عضویت : {$user['data-start']}

🔻––––––––––––––––🔻

⚠️تعداد اخطار : {$user['warn']} از 3
🎁هدیه مدیریت : {$user['coinadmin']}

🔻––––––––––––––––🔻

👁‍🗨بازدیدهای کسب شده : {$user['orgviewkasb']}
✔️ تعداد سفارشات ثبت شده : {$user['sefareshorg']}


🔻––––––––––––––––🔻

🏧سکه های انتقالی به شما:  {$user['enteghal_as']}
🏧سکه های انتقالی از شما:  {$user['enteghal_to']}
⏳سکه های مصرفی : {$user['masrafi']}

🔻––––––––––––––––🔻

👥تعداد زیرمجموعه ها: {$user['zirmjmae']}
💰 سکه شما : {$user['Points']}
💯💯💯💯💯",'html',null);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok3" and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"$doktxt3
\nhttp://t.me/$boter_id?start=$userID",'html',null);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok5" and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"$doktxt5",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok7" and $Tc == 'private'){
$user['step'] = 'none';
$Button_coinfree = json_encode(['inline_keyboard' => [
[['text' => '👤عضویت در کانال','url' => "https://t.me/$chanel_post"]]
]]);
SM($chatID,"$doktxt7",'html',null,$Button_coinfree);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok12" and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"$doktxt12",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok0" and $Tc == 'private'){
$user['step'] = 'none';
SM($chatID,"♻️ در آپدیت بعدی این بخش از ربات دسترس کاربران قرار خواهد گرفت.",'html',null,$Button_Home);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
else if($msg == "$dok8" and $Tc == 'private'){
$user['step'] = 'none';
$Buttjgy_Join = json_encode(['inline_keyboard'=>[
[['text'=>"‍👤زیرمجموعه گیـری",'callback_data'=>'zirmajmueh'],['text'=>"👁‍🗨مشاهده تبلیغ",'callback_data'=>'seenpots']],
[['text'=>"📝ثبت تبلیغ",'callback_data'=>'post']],
]]);
SM($chatID,"$doktxt8",'html',null,$Buttjgy_Join);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($data == 'backbartar' and $Tc == 'private'){
$user['step'] = 'none';
$Buttj_Join = json_encode(['inline_keyboard'=>[
[['text'=>"‍👤زیرمجموعه گیـری",'callback_data'=>'zirmajmueh'],['text'=>"👁‍🗨مشاهده تبلیغ",'callback_data'=>'seenpots']],
[['text'=>"📝ثبت تبلیغ",'callback_data'=>'post']],
]]);
Editmessagetext($chatID, $msg_id,"$doktxt8",$Buttj_Join);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($data == 'zirmajmueh' and $Tc == 'private'){
$user['step'] = 'none';
$Buttonoin = json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>'backbartar']],
]]);
for ($t=0;$t<=9;$t++){
$users = json_decode(file_get_contents('melat/' . top($t,'zirmjmae') . '.json'),true);
$invite = $users['zirmjmae'];
$getid = top($t,'zirmjmae');
if ($invite == ''){
$invite = 0;
} else {
$invite = $invite;
}
if ($getid == ''){
$getid = 0;
} else {
$getid = $getid;
}
$textbartar = $textbartar .= " ♻️ شماره کاربری: $getid
👤تعداد زیرمجموعه:  $invite \n\n";
}
Editmessagetext($chatID, $msg_id,"$textbartar",$Buttonoin);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($data == 'seenpots' and $Tc == 'private'){
$user['step'] = 'none';
$Buttonoin = json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>'backbartar']],
]]);
for ($t=0;$t<=9;$t++){
$users = json_decode(file_get_contents('melat/' . top($t,'orgviewkasb') . '.json'),true);
$invite = $users['orgviewkasb'];
$getid = top($t,'orgviewkasb');
if ($invite == ''){
$invite = 0;
} else {
$invite = $invite;
}
if ($getid == ''){
$getid = 0;
} else {
$getid = $getid;
}
$textbartar = $textbartar .= " ♻️ شماره کاربری: $getid
👁‍🗨تعداد مشاهده تبلیغ:  $invite \n\n";
}
Editmessagetext($chatID, $msg_id,"$textbartar",$Buttonoin);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($data == 'post' and $Tc == 'private'){
$user['step'] = 'none';
$Buttonoin = json_encode(['inline_keyboard'=>[
[['text'=>"🔙 بازگشت",'callback_data'=>'backbartar']],
]]);
for ($t=0;$t<=9;$t++){
$users = json_decode(file_get_contents('melat/' . top($t,'sefareshorg') . '.json'),true);
$invite = $users['sefareshorg'];
$getid = top($t,'sefareshorg');
if ($invite == ''){
$invite = 0;
} else {
$invite = $invite;
}
if ($getid == ''){
$getid = 0;
} else {
$getid = $getid;
}
$textbartar = $textbartar .= " ♻️ شماره کاربری: $getid
📝تعداد ثبت تبلیغ:  $invite \n\n";
}
Editmessagetext($chatID, $msg_id,"$textbartar",$Buttonoin);
saveJson("melat/$userID.json",$user);
}
//##########################################################################
//////////------------------------\\\\\\\\\\\\\\///
if($msg == "$dok6" and $Tc == 'private'){
$user['step'] = 'Support';
SM($chatID,"$doktxt6",'html',null,$Button_Back);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($step == 'Support' and $Tc == 'private'){
if(!isset($up->message->forward_from) and !isset($up->message->forward_from_chat)){
SM($list['admins'][0],"یک پیام از کاربر :$userID
متن پیام : $msg",'html',null,json_encode(['inline_keyboard'=>[[['text'=>"🗣 پاسخ دادن",'callback_data'=>"pasokh-$userID"],['text'=>"❗️بلاک کردن",'callback_data'=>"bluck-$userID"]],
[['text'=>"⛔️ بستن تیکت",'callback_data'=>"rad-$userID"]],]]));
$user['step'] = "none"; 
SM($chatID,"✅ پیام شما با موفقیت دریافت شد!\nلطفا تا زمان دریافت پاسخ صبور باشید، بزودی واحد پشتیبانی به آن پاسخ خواهند داد.",'html');
saveJson("melat/$chatID.json",$user);
}else{
SM($chatID,"📨پیام ارسالی نباید فروارد شده باشد :
❕دقت در ارسال پیام داشته باشید !",'html',$msg_id);
}}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^pasokh-(.*)/', $data, $match)){
$id = $match[1];
$user['step'] = "send-$id"; 
SM($chatID,"در حال پاسخ به کاربر $id هستید :
پیام خود را در یک قالب ارسال کنید !",'html',null);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^send-(.*)/', $step, $match)){
$i2d0 = $match[1];
SM($i2d0,"پاسخ پشتیبانی :
$msg",'html',null,$Button_Back);
$users['step'] = 'Support';
$user['step'] = "none"; 
SM($chatID,"کاربر $i2d0 پیام شمارا دریافت کرد!",'html');
saveJson("melat/$i2d0.json",$users);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^bluck-(.*)/', $data, $match)){
$i2d5= $match[1];
$user['step'] = "none"; 
$lists['ban'][] = $i2d5;
saveJson("kodam/jijinanahekose.json",$lists);
DLM($chatID,$msg_id);
SM($chatID,"کاربر $i2d5 با موفقیت از ربات بلاک شد ✔️",'html');
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^rad-(.*)/', $data, $match)){
$i3d= $match[1];
$user['step'] = "none"; 
DLM($chatID,$msg_id);
SM($chatID,"پیام $i3d با موفقیت رد شد✔️",'html');
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == "📍 تنظیم متن"  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_dok0 = json_encode(['inline_keyboard'=>[
[['text'=>"〽️ استارت",'callback_data'=>'sets-start']],
[['text'=>"$dok7",'callback_data'=>"sets-doktxt7"],
['text'=>"$dok1",'callback_data'=>"sets-doktxt1"]],
[['text'=>"$dok3",'callback_data'=>"sets-doktxt3"],
['text'=>"$dok12",'callback_data'=>"sets-doktxt12"]],
[['text'=>"$dok6",'callback_data'=>"sets-doktxt6"],
['text'=>"$dok5",'callback_data'=>"sets-doktxt5"]],
]]);
SM($chatID,"✅در این بخش می توانید متن دکمه هارا تنظیم کنید",'html',null,$Button_dok0); 
}
//----------------------------------------/////
//----------------------------------------/////
else if($msg == '📍 افزودن ادمین' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$butt = json_encode(['keyboard'=>[
[['text'=>'لیست مدیران📜']],
[['text'=>'افزودن ➕'],['text'=>'حذف کردن ➖']],
[['text'=>'🔙 برگشت']],
],'resize_keyboard'=>true]);
$user['step'] = 'none';
SM($chatID,"🔹 یکی از گزینه های زیر را انتخاب نمایید :️",'html',$msg_id,$butt);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if($msg == '📍 مدیریت کاربران' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Butotanim = json_encode(['keyboard'=>[
[['text'=>"📍 پیگیری کاربر"]],
[['text'=>"📍 کسر موجودی"],['text'=>"📍 افزایش موجودی"]],
[['text'=>"📍 ارسال به کاربران"],['text'=>"📍 فروارد به کاربران"]],
[['text'=>"📍 آنبلاک کاربر"],['text'=>"📍 مسدود کردن"]],
[['text'=>"🔙 برگشت"]],
],'resize_keyboard'=>true]);
SM($chatID,"گزینه مورد نظر را انتخاب کنید",'html',null,$Butotanim);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == "📍 تنظیم امتیاز"  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_dok0 = json_encode(['inline_keyboard'=>[
[['text'=>"شروع ربات",'callback_data'=>'setcs-starts'],
['text'=>"زیرمجموعه گیری",'callback_data'=>'setcs-zirmjmaec']],
]]);
SM($chatID,"✅در این بخش می توانید امتیاز هر بخش از ربات را تنظیم کنید",'html',null,$Button_dok0); 
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == "📍 تنظیم دکمه"  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_dok0 = json_encode(['inline_keyboard'=>[
[['text'=>"$dok7",'callback_data'=>"sets-dok7"]],
[['text'=>"$dok2",'callback_data'=>"sets-dok2"],
['text'=>"$dok1",'callback_data'=>"sets-dok1"]],
[['text'=>"$dok3",'callback_data'=>"sets-dok3"],
['text'=>"$dok12",'callback_data'=>"sets-dok12"],
['text'=>"$dok4",'callback_data'=>"sets-dok4"]],
[['text'=>"$dok0",'callback_data'=>"sets-dok0"],
['text'=>"$dok8",'callback_data'=>"sets-dok8"]],
[['text'=>"$dok6",'callback_data'=>"sets-dok6"],
['text'=>"$dok11",'callback_data'=>"sets-dok11"],
['text'=>"$dok5",'callback_data'=>"sets-dok5"]],
]]);
SM($chatID,"✅در این بخش می توانید نام دکمه هارا تنظیم کنید",'html',null,$Button_dok0); 
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == "📍 تنظیمات سفارش"  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_dok8 = json_encode(['inline_keyboard'=>[
[['text'=>"نام آیتم",'callback_data'=>'0'],
['text'=>"تعداد سکه",'callback_data'=>'0'],
['text'=>"تعداد بازدید",'callback_data'=>'0']],
[['text'=>"$dokt1",'callback_data'=>'setname-dokt1'],
['text'=>"$dokc1",'callback_data'=>'setcoinseen-dokc1'],
['text'=>"$dokk1",'callback_data'=>'setviewseen-dokk1']],
[['text'=>"$dokt2",'callback_data'=>'setname-dokt2'],
['text'=>"$dokc2",'callback_data'=>'setcoinseen-dokc2'],
['text'=>"$dokk2",'callback_data'=>'setviewseen-dokk2']],
[['text'=>"$dokt3",'callback_data'=>'setname-dokt3'],
['text'=>"$dokc3",'callback_data'=>'setcoinseen-dokc3'],
['text'=>"$dokk3",'callback_data'=>'setviewseen-dokk3']],
[['text'=>"$dokt4",'callback_data'=>'setname-dokt4'],
['text'=>"$dokc4",'callback_data'=>'setcoinseen-dokc4'],
['text'=>"$dokk4",'callback_data'=>'setviewseen-dokk4']],
[['text'=>"$dokt5",'callback_data'=>'setname-dokt5'],
['text'=>"$dokc5",'callback_data'=>'setcoinseen-dokc5'],
['text'=>"$dokk5",'callback_data'=>'setviewseen-dokk5']],
[['text'=>"$dokt6",'callback_data'=>'setname-dokt6'],
['text'=>"$dokc6",'callback_data'=>'setcoinseen-dokc6'],
['text'=>"$dokk6",'callback_data'=>'setviewseen-dokk6']],
[['text'=>"$dokt7",'callback_data'=>'setname-dokt7'],
['text'=>"$dokc7",'callback_data'=>'setcoinseen-dokc7'],
['text'=>"$dokk7",'callback_data'=>'setviewseen-dokk7']],
[['text'=>"$dokt8",'callback_data'=>'setname-dokt8'],
['text'=>"$dokc8",'callback_data'=>'setcoinseen-dokc8'],
['text'=>"$dokk8",'callback_data'=>'setviewseen-dokk8']],
[['text'=>"$dokt9",'callback_data'=>'setname-dokt9'],
['text'=>"$dokc9",'callback_data'=>'setcoinseen-dokc9'],
['text'=>"$dokk9",'callback_data'=>'setviewseen-dokk9']],
[['text'=>"$dokt10",'callback_data'=>'setname-dokt10'],
['text'=>"$dokc10",'callback_data'=>'setcoinseen-dokc10'],
['text'=>"$dokk10",'callback_data'=>'setviewseen-dokk10']],
[['text'=>"بازدید دلخواه",'callback_data'=>'0']],
[['text'=>"$delmax",'callback_data'=>'setcoindel-delmax'],
['text'=>"تا",'callback_data'=>'0'],
['text'=>"$delmin",'callback_data'=>'setcoindel-delmin']],
]]);
SM($chatID,"💢 به بخش تنظیمات آیتم های سفارش بازدید خوش آمدید

👈در این بخش میتوانید تنظیمات تعداد سکه ، تعداد بازدید و نام هر آیتم را انجام دهید. 

⚠️توجه نمایید که حتما باید تمامی آیتم هارا کامل نمایید",'html',null,$Button_dok8); 
}
//////////------------------------\\\\\\\\\\\\\\///
else if($data == "back-setview"  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_dok8 = json_encode(['inline_keyboard'=>[
[['text'=>"نام آیتم",'callback_data'=>'0'],
['text'=>"تعداد سکه",'callback_data'=>'0'],
['text'=>"تعداد بازدید",'callback_data'=>'0']],
[['text'=>"$dokt1",'callback_data'=>'setname-dokt1'],
['text'=>"$dokc1",'callback_data'=>'setcoinseen-dokc1'],
['text'=>"$dokk1",'callback_data'=>'setviewseen-dokk1']],
[['text'=>"$dokt2",'callback_data'=>'setname-dokt2'],
['text'=>"$dokc2",'callback_data'=>'setcoinseen-dokc2'],
['text'=>"$dokk2",'callback_data'=>'setviewseen-dokk2']],
[['text'=>"$dokt3",'callback_data'=>'setname-dokt3'],
['text'=>"$dokc3",'callback_data'=>'setcoinseen-dokc3'],
['text'=>"$dokk3",'callback_data'=>'setviewseen-dokk3']],
[['text'=>"$dokt4",'callback_data'=>'setname-dokt4'],
['text'=>"$dokc4",'callback_data'=>'setcoinseen-dokc4'],
['text'=>"$dokk4",'callback_data'=>'setviewseen-dokk4']],
[['text'=>"$dokt5",'callback_data'=>'setname-dokt5'],
['text'=>"$dokc5",'callback_data'=>'setcoinseen-dokc5'],
['text'=>"$dokk5",'callback_data'=>'setviewseen-dokk5']],
[['text'=>"$dokt6",'callback_data'=>'setname-dokt6'],
['text'=>"$dokc6",'callback_data'=>'setcoinseen-dokc6'],
['text'=>"$dokk6",'callback_data'=>'setviewseen-dokk6']],
[['text'=>"$dokt7",'callback_data'=>'setname-dokt7'],
['text'=>"$dokc7",'callback_data'=>'setcoinseen-dokc7'],
['text'=>"$dokk7",'callback_data'=>'setviewseen-dokk7']],
[['text'=>"$dokt8",'callback_data'=>'setname-dokt8'],
['text'=>"$dokc8",'callback_data'=>'setcoinseen-dokc8'],
['text'=>"$dokk8",'callback_data'=>'setviewseen-dokk8']],
[['text'=>"$dokt9",'callback_data'=>'setname-dokt9'],
['text'=>"$dokc9",'callback_data'=>'setcoinseen-dokc9'],
['text'=>"$dokk9",'callback_data'=>'setviewseen-dokk9']],
[['text'=>"$dokt10",'callback_data'=>'setname-dokt10'],
['text'=>"$dokc10",'callback_data'=>'setcoinseen-dokc10'],
['text'=>"$dokk10",'callback_data'=>'setviewseen-dokk10']],
[['text'=>"بازدید دلخواه",'callback_data'=>'0']],
[['text'=>"$delmax",'callback_data'=>'setcoindel-delmax'],
['text'=>"تا",'callback_data'=>'0'],
['text'=>"$delmin",'callback_data'=>'setcoindel-delmin']],
]]);
Editmessagetext($chatID, $msg_id,"💢 به بخش تنظیمات آیتم های سفارش بازدید خوش آمدید

👈در این بخش میتوانید تنظیمات تعداد سکه ، تعداد بازدید و نام هر آیتم را انجام دهید. 

⚠️توجه نمایید که حتما باید تمامی آیتم هارا کامل نمایید",$Button_dok8);
}
//----------------------------------------/////
else if($msg == '📍 تنظیم کانال' and $Tc == 'private' and in_array($chatID,$list['admins'])){
SM($chatID,"گزینه مورد نظر را انتخاب کنید",'html',null,$Button_CH);
}
//----------------------------------------/////
else if($data == 'back' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'none';
SM($chatID,"
 شما می توانید با استفاده از دکمه های زیر ربات را مدیریت کنید👇☺️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '👤پنل مدیریت👤' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'none';
SM($chatID,"سلام به پنل مدیریت ربات خوش اومدید🌸
 شما می توانید با استفاده از دکمه های زیر ربات را مدیریت کنید👇☺️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '🔙 برگشت' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'none';
SM($chatID,"سلام به پنل مدیریت ربات خوش اومدید🌸
 شما می توانید با استفاده از دکمه های زیر ربات را مدیریت کنید👇☺️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 آمار ربات' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Scan = scandir('melat');
$Scan = array_diff($Scan, ['.','..']);
$members = 0;
foreach($Scan as $Value){
if(is_file("melat/$Value")){
$members++;
}}
$Scans = scandir('orderseen');
$Scans = array_diff($Scans, ['.','..']);
$allorderlike = 0;
foreach($Scans as $Values){
if(is_file("orderseen/$Values")){
$allorderlike++;
}}
$blk = count($lists['ban']);
SM($chatID,"🤖 آمار ربات شما
    
📍 تعداد کاربران : $members
📍 تعداد سفارشات بازدید واقعی : $allorderlike
📍 تعداد مسدود ها : $blk",'html',$msg_id);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == 'افزودن ➕' and $Tc == 'private' and in_array($chatID,$list['admins'])){
if($userID == $list['admins'][0]){
$user['step'] = 'add-admin';
SM($chatID,"آیدی عددی فرد مورد نظرتون رو ارسال کنید 🌱",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}}
else if($step == 'add-admin' and $Tc == 'private'){
if(is_numeric($msg)){
if(!in_array($msg,$list['admins'])){
SM($chatID,'کاربر '.$msg.' به عنوان یکی از مدیران منصوب شد❗️','MarkDown',$msg_id);
$user['step'] = 'none';
$list['admins'][] = $msg;
saveJson("kodam/list.json",$list);
}else{
$user['step'] = 'none';
SM($chatID,"فرد مورد نظر شما از قبل مدیر ربات میباشد !",'MarkDown',$msg_id);
}
}else{
$user['step'] = 'none';
SM($chatID,"فقط ارسال ایدی عددی مجاز است ❗",'MarkDown',$msg_id);
}
saveJson("melat/$userID.json",$user);
}
else if($msg == 'حذف کردن ➖' and $Tc == 'private' and in_array($chatID,$list['admins'])){
if($userID == $list['admins'][0]){
$user['step'] = 'ksr-admin';
SM($chatID,"آیدی عددی فرد مورد نظرتون رو ارسال کنید 🌱",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}}
else if($step == 'ksr-admin' and $Tc == 'private'){
if($msg != "$admins"){
if(is_numeric($msg)){
if(in_array($msg,$list['admins'])){
SM($chatID,'کاربر '.$msg.' از لیست مدیران حذف گردید❗️️','MarkDown',$msg_id);
$user['step'] = 'none';
$search = array_search($msg,$list['admins']);
unset($list['admins'][$search]);
$list['admins'] = array_values($list['admins']);
saveJson("kodam/list.json",$list);
}else{
$user['step'] = 'none';
SM($chatID,"فرد مورد نظر شما از قبل مدیر ربات نمیباشد !",'MarkDown',$msg_id);
}
}else{
$user['step'] = 'none';
SM($chatID,"فقط ارسال ایدی عددی مجاز است ❗",'MarkDown',$msg_id);
}
saveJson("melat/$userID.json",$user);
}else{
$user['step'] = 'none';
SM($chatID,"این شناسه برای ادمین اصلی میباشد ❗",'MarkDown',$msg_id);
}}
else if($msg == 'لیست مدیران📜' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$admines=null;
foreach($list['admins'] as $id){
$admines = $admines .= "<a href='tg://user?id=$id'>$id</a>\n";
}
$user['step'] = 'none';
SM($chatID,'📍 لیست مدیران به صورت زیر میباشد :'.PHP_EOL.$admines,'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 پیگیری کاربر' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'sendmin';
SM($chatID,"📍 لطفا شناسه کاربری فرد را ارسال کنید",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
elseif($user['step'] == 'sendmin') {
if(is_file("melat/$msg.json")){
$user1 = json_decode(file_get_contents("melat/$msg.json"), 1);
$user['step']= 'none';
saveJson("melat/$userID.json",$user);
SM($chatID,"📆تاریخ عضویت : {$user1['data-start']}

🔻––––––––––––––––🔻

⚠️تعداد اخطار : {$user1['warn']} از 3
🎁هدیه مدیریت : {$user1['coinadmin']}

🔻––––––––––––––––🔻

👁‍🗨بازدیدهای کسب شده : {$user1['orgviewkasb']}
✔️ تعداد سفارشات ثبت شده : {$user1['sefareshorg']}


🔻––––––––––––––––🔻

🏧سکه های دریافتی:  {$user1['enteghal_as']}
🏧سکه های انتقالی:  {$user1['enteghal_to']}
⏳سکه های مصرفی : {$user1['masrafi']}

🔻––––––––––––––––🔻

👥تعداد زیرمجموعه ها: {$user1['zirmjmae']}
💰 سکه :  {$user1['Points']}
",'html',null,$Button_Admins_Panel); 
}else{
SM($chatID,"❗️این کاربر عضو ربات نمیباشد",'html',null,$Button_Admins_Panel); 
$user['step'] = 'none';
saveJson("melat/$userID.json",$user);
}}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 افزایش موجودی' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'senddaadmin';
SM($chatID,"📍 لطفا در خط اول ایدی فرد و در خط دوم میزان موجودی را وارد کنید
برای افزایش سکه :
267785153
200",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
elseif($user['step'] == 'senddaadmin') {
$user['step']= 'none';
saveJson("melat/$userID.json",$user);
$all = explode("\n", $msg);
SM($all[0],"❗️تعداد $all[1] سکه از طرف مدیریت به حساب شما واریز شد .",'html',null);
SM($chatID,"افزایش موجودی با موفقیت انجام شد ✅",'html',$msg_id,$Button_Admins_Panel);
$user2 = json_decode(file_get_contents("melat/{$all[0]}.json"), 1);
$coin = $user2["Points"] + $all[1];
$user2["Points"]= $coin;
saveJson("melat/{$all[0]}.json",$user2);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 کسر موجودی' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'senddadmin';
SM($chatID,"📍 لطفا در خط اول ایدی فرد و در خط دوم میزان موجودی را وارد کنید
برای کسر سکه :
267785153
20
سکه",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
elseif($user['step'] == 'senddadmin') {
$user['step']= 'none';
saveJson("melat/$userID.json",$user);
$all = explode("\n", $msg);
SM($all[0],"❗️تعداد $all[1] سکه از حساب شما توسط مدیریت کسر شد .",'html',null);
SM($chatID,"کسر موجودی با موفقیت انجام شد ✅",'html',$msg_id,$Button_Admins_Panel);
$user2 = json_decode(file_get_contents("melat/{$all[0]}.json"), 1);
$coin = $user2["Points"] - $all[1];
$user2["Points"]= $coin;
saveJson("melat/{$all[0]}.json",$user2);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 مسدود کردن' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'ban';
SM($chatID,"📍 لطفا شناسه کاربری فرد را ارسال کنید",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
else if($step == 'ban' and $Tc == 'private'){
$ok = GCMB($msg);
if($ok == true){
if(!in_array($msg,$ban)){
SM($chatID,"✅ فرد با موفقیت مسدود شد",'MarkDown',$msg_id);
$user['step'] = 'none';
$lists['ban'][] = $msg;
saveJson("kodam/jijinanahekose.json",$lists);
}else{
$user['step'] = 'none';
SM($chatID,"⛔️ این کاربر از قبل بلاک بود.",'MarkDown',$msg_id);
}
}else{
$user['step'] = 'none';
SM($chatID,"❌ این کاربر عضو ربات نیست",'MarkDown',$msg_id);
}
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 آنبلاک کاربر' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'unban';
SM($chatID,"📍 لطفا شناسه کاربری فرد را ارسال کنید",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
else if($step == 'unban' and $Tc == 'private'){
$ok = GCMB($msg);
if($ok == true){
if(in_array($msg,$ban)){
$user['step'] = 'none';
$key = array_search($msg,$ban);
unset($list['ban'][$key]);
$lists['ban'] = array_values($list['ban']);
saveJson("kodam/jijinanahekose.json",$lists);
SM($chatID,"✅ فرد با موفقیت آنبلاک شد",'MarkDown',$msg_id);
}else{
$user['step'] = 'none';
SM($chatID,"🔓 این کاربر اصلا بلاک نیست.",'MarkDown',$msg_id);
}
}else{
$user['step'] = 'none';
SM($chatID,"❌ این کاربر عضو ربات نیست",'MarkDown',$msg_id);
}
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 فروارد به کاربران' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'fortoall-admin';
SM($chatID,"📍 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
elseif($user['step'] == 'fortoall-admin') {
$user['step']= 'none';
saveJson("melat/$userID.json",$user);
SM($chatID,"پیام مورد نظر با موفقیت تنظیم شد. به زودی به تمامی کاربران ربات فوروارد میگردد❗️",'MarkDown',$msg_id);
foreach(glob('melat/*.json') as $array){
$userID = str_replace(['melat/', '.json'], '', $array);
if(is_numeric($userID)){
bot('forwardMessage', ['chat_id'=> $userID, 'from_chat_id'=> $chatID, 'message_id'=> $msg_id]);
}
} bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"پیام مورد نظر به تمامی کاربران ربات فوروارد گردید✅"
]);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 ارسال به کاربران' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'sendtoall-admin';
SM($chatID,"📍 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس باشد]  همچنین میتوانید رسانه را همراه با کشپن [متن چسپیده به رسانه ارسال کنید]",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
elseif($user['step'] == 'sendtoall-admin') {
$user['step']= 'none';
saveJson("melat/$userID.json",$user);
SM($chatID,"پیام مورد نظر با موفقیت تنظیم شد. به زودی به تمامی کاربران ربات ارسال میگردد❗️",'MarkDown',$msg_id);
foreach(glob('melat/*.json') as $array){
$userID = str_replace(['melat/', '.json'], '', $array);
if(is_numeric($userID)){
bot('copyMessage', ['chat_id'=> $userID, 'from_chat_id'=> $chatID, 'message_id'=> $msg_id]);
}} 
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"پیام مورد نظر به تمامی کاربران ربات ارسال گردید✅",
]);
}
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 بروزرسانی'  and $Tc == 'private' and in_array($chatID,$list['admins'])){
$Button_upd = json_encode(['keyboard'=>[
[['text'=>'♻️انجام بروز رسانی♻️']],
[['text'=>'🔙 برگشت']],
],'resize_keyboard'=>true]);
$user['step'] = 'updeta'; 
  SM($chatID,"⁉️درصورتی که بروز رسانی جدید ربات در دسترس باشد با بروز کردن ربات به نسخه جدید میتوانید ربات خود را بهبود ببخشید

👈بهتر است هر هفته این گزینه را امتحان کنید تا در صورت وجود باگ یا تغییرات ربات شما ارتقا یابد:",'html',null,$Button_upd);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if($msg == '♻️انجام بروز رسانی♻️' and $step == 'updeta' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'none';
SM($chatID,"اپدیت ربات شما در حال انجام می باشد...",'html',null);
sleep(1.5);
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'⬛️⬜️⬜️⬜️⬜️ %20' 
]); 
sleep(2.5);
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'⬛️⬛️⬜️⬜️⬜️ %40' 
]); 
sleep(2.5); 
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'⬛️⬛️⬛️⬜️⬜️ %60' 
]); 
sleep(2.5); 
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'⬛️⬛️⬛️⬛️⬜️ %80' 
]); 
sleep(2.5);
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'⬛️⬛️⬛️⬛️⬛️ %100' 
]); 
sleep(2.5);
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'%100درحال بارگزاری اطلاعات' ]); 
copy('../../../Source/viewgir/bot.php',"bot.php");
copy('../../../Source/viewgir/class.php',"class.php");
sleep(1); 
bot('editMessageText',[ 
'chat_id'=>$chatID, 
'message_id'=>$msg_id + 1, 
'text'=>'✅ربات شما با موفقیت به اخرین نسخه اپدیت شد
جهت شروع مجدد /start را بزنید' 
]);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if($msg == '📍 اشتراک باقی مانده' and $Tc == 'private' and in_array($chatID,$list['admins'])){
date_default_timezone_set('Asia/Tehran'); 
$sharge = file_get_contents("Lite.txt");
$a = date('Y/m/d');
$b = "$sharge";
$sec = strtotime($b)-strtotime($a);
$days = $sec/86400;
$d0ays = explode('.',$days)[0];
SM($chatID,"⏳از شارژ ربات شما <code>$d0ays</code> روز باقی مانده است",'html');
}
//----------------------------------------/////
elseif(preg_match('/^setcs-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back"]],
]]);
$user['step']= "setcs-$dok";
Editmessagetext($chatID, $msg_id,"✅ تعداد امتیاز را ارسال کنید :",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setcs-(.*)/', $step, $match)){
if(preg_match('/^([0-9])/',$msg) and $msg >= 1 && $msg <= 20){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}else{
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"❗️عدد ارسالی باید لاتین باشد :
❗️عددی بین 1 الی 20 ارسال کنید :",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Back_admin
]);
}}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^sets-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back"]],
]]);
$user['step']= "sets-$dok";
Editmessagetext($chatID, $msg_id,"متن مورد نظررا وارد نمایید",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^sets-(.*)/', $step, $match)){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setname-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back-setview"]],
]]);
$user['step']= "setname-$dok";
Editmessagetext($chatID, $msg_id,"✅نام آیتم مورد نظر را ارسال کنید

👈نمونه :سفارش ۵ بازدید | ۱۰ سکه",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setname-(.*)/', $step, $match)){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setcoinseen-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back-setview"]],
]]);
$user['step']= "setcoinseen-$dok";
Editmessagetext($chatID, $msg_id,"✅مقدار سکه که مایلید برای سفارش این آیتم از کاربران کسر شود را ارسال کنید

✅نمونه صحیح : 100
❌نمونه غلط : ۱۰۰",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setcoinseen-(.*)/', $step, $match)){
if(preg_match('/^([0-9])/',$msg)){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setviewseen-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back-setview"]],
]]);
$user['step']= "setviewseen-$dok";
Editmessagetext($chatID, $msg_id,"✅تعداد بازدید درخواستی این آیتم را جهت سفارش کاربران ارسال نمایید

✅نمونه صحیح : 100
❌نمونه غلط : ۱۰۰",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setviewseen-(.*)/', $step, $match)){
if(preg_match('/^([0-9])/',$msg)){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setcoindel-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"🔙بازگشت",'callback_data'=>"back-setview"]],
]]);
$user['step']= "setcoindel-$dok";
Editmessagetext($chatID, $msg_id,"✅تعداد بازدید را ارسال نمایید

✅نمونه صحیح : 100
❌نمونه غلط : ۱۰۰",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^setcoindel-(.*)/', $step, $match)){
if(preg_match('/^([0-9])/',$msg)){
$user['step']= 'none';
$doke = $match[1];
Save("Button/$doke.txt",$msg);
bot('sendmessage',[
'chat_id'=>$chatID,
'text'=>"✅با موفقیت تنظیم شد",
'reply_to_message_id'=>$msg_id,
'reply_markup'=>$Button_Admins_Panel
]);
saveJson("melat/$userID.json",$user);
}}
//----------------------------------------/////
else if($msg == '📟 کانال جوین اجباری اول' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'SetCh1';
SM($chatID,"🛠 لطفا ایدی کانال را بدون @  برام بفرستین.",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if(strpos($step,'SetCh1') !== false and $Tc == 'private' and isset($msg)){
if(strpos($msg,"@") == false or strpos($msg,"$") == false or strpos($msg,"#") == false){
$user['step'] = 'none';
Save("kodam/channel.txt",$msg);
SM($chatID,"■ کانال ".$msg." ثبت گردید.️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}}
//----------------------------------------/////
else if($msg == '📟 کانال جوین اجباری دوم' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'SetCh2';
SM($chatID," لطفا ایدی کانال را بدون @  برام بفرستین.",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if(strpos($step,'SetCh2') !== false and $Tc == 'private' and isset($msg)){
if(strpos($msg,"@") == false or strpos($msg,"$") == false or strpos($msg,"#") == false){ 
$user['step'] = 'none';
Save("kodam/channel2.txt",$msg);
SM($chatID,"■ کانال ".$msg." ثبت گردید.️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}}
//----------------------------------------/////
else if($msg == '📟 کانال تبلیغات' and $Tc == 'private' and in_array($chatID,$list['admins'])){
$user['step'] = 'SetCh3';
SM($chatID," لطفا ایدی کانال تبلیغات را بدون @  برام بفرستین.",'html',$msg_id,$Button_Back_admin);
saveJson("melat/$userID.json",$user);
}
//----------------------------------------/////
else if(strpos($step,'SetCh3') !== false and $Tc == 'private' and isset($msg)){
if(strpos($msg,"@") == false or strpos($msg,"$") == false or strpos($msg,"#") == false){ 
$user['step'] = 'none';
Save("kodam/channel3.txt",$msg);
SM($chatID,"■ کانال ".$msg." ثبت گردید.️",'html',$msg_id,$Button_Admins_Panel);
saveJson("melat/$userID.json",$user);
}}
//----------------------------------------/////
//////////------------------------\\\\\\\\\\\\\\///
else if($msg == '📍 چیدمان کیبورد' and $Tc = 'private'){
if(file_exists("keyboard/home/line11.txt")){
$line1_1 = file_get_contents("keyboard/home/line11.txt");
if($line1_1 != null ){
$line1_1 = str_replace('DOK1',$dok1,$line1_1);
$line1_1 = str_replace('DOK2',$dok2,$line1_1);
$line1_1 = str_replace('DOK3',$dok3,$line1_1);
$line1_1 = str_replace('DOK4',$dok4,$line1_1);
$line1_1 = str_replace('DOK5',$dok5,$line1_1);
$line1_1 = str_replace('DOK6',$dok6,$line1_1);
$line1_1 = str_replace('DOK7',$dok7,$line1_1);
$line1_1 = str_replace('DOK8',$dok8,$line1_1);
$line1_1 = str_replace('DOK9',$dok9,$line1_1);
$line1_1 = str_replace('DOK0',$dok0,$line1_1);
$line1_1 = str_replace('DUQ1',$dok11,$line1_1);
$line1_1 = str_replace('DUQ2',$dok12,$line1_1);
}else{
$line1_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line12.txt")){
$line1_2 = file_get_contents("keyboard/home/line12.txt");
if($line1_2 != null ){
$line1_2 = str_replace('DOK1',$dok1,$line1_2);
$line1_2 = str_replace('DOK2',$dok2,$line1_2);
$line1_2 = str_replace('DOK3',$dok3,$line1_2);
$line1_2 = str_replace('DOK4',$dok4,$line1_2);
$line1_2 = str_replace('DOK5',$dok5,$line1_2);
$line1_2 = str_replace('DOK6',$dok6,$line1_2);
$line1_2 = str_replace('DOK7',$dok7,$line1_2);
$line1_2 = str_replace('DOK8',$dok8,$line1_2);
$line1_2 = str_replace('DOK9',$dok9,$line1_2);
$line1_2 = str_replace('DOK0',$dok0,$line1_2);
$line1_2 = str_replace('DUQ1',$dok11,$line1_2);
$line1_2 = str_replace('DUQ2',$dok12,$line1_2);
}else{
$line1_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line13.txt")){
$line1_3 = file_get_contents("keyboard/home/line13.txt");
if($line1_3 != null ){
$line1_3 = str_replace('DOK1',$dok1,$line1_3);
$line1_3 = str_replace('DOK2',$dok2,$line1_3);
$line1_3 = str_replace('DOK3',$dok3,$line1_3);
$line1_3 = str_replace('DOK4',$dok4,$line1_3);
$line1_3 = str_replace('DOK5',$dok5,$line1_3);
$line1_3 = str_replace('DOK6',$dok6,$line1_3);
$line1_3 = str_replace('DOK7',$dok7,$line1_3);
$line1_3 = str_replace('DOK8',$dok8,$line1_3);
$line1_3 = str_replace('DOK9',$dok9,$line1_3);
$line1_3 = str_replace('DOK0',$dok0,$line1_3);
$line1_3 = str_replace('DUQ1',$dok11,$line1_3);
$line1_3 = str_replace('DUQ2',$dok12,$line1_3);
}else{
$line1_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line14.txt")){
$line1_4 = file_get_contents("keyboard/home/line14.txt");
if($line1_4 != null ){
$line1_4 = str_replace('DOK1',$dok1,$line1_4);
$line1_4 = str_replace('DOK2',$dok2,$line1_4);
$line1_4 = str_replace('DOK3',$dok3,$line1_4);
$line1_4 = str_replace('DOK4',$dok4,$line1_4);
$line1_4 = str_replace('DOK5',$dok5,$line1_4);
$line1_4 = str_replace('DOK6',$dok6,$line1_4);
$line1_4 = str_replace('DOK7',$dok7,$line1_4);
$line1_4 = str_replace('DOK8',$dok8,$line1_4);
$line1_4 = str_replace('DOK9',$dok9,$line1_4);
$line1_4 = str_replace('DOK0',$dok0,$line1_4);
$line1_4 = str_replace('DUQ1',$dok11,$line1_4);
$line1_4 = str_replace('DUQ2',$dok12,$line1_4);
}else{
$line1_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line21.txt")){
$line2_1 = file_get_contents("keyboard/home/line21.txt");
if($line2_1 != null ){
$line2_1 = str_replace('DOK1',$dok1,$line2_1);
$line2_1 = str_replace('DOK2',$dok2,$line2_1);
$line2_1 = str_replace('DOK3',$dok3,$line2_1);
$line2_1 = str_replace('DOK4',$dok4,$line2_1);
$line2_1 = str_replace('DOK5',$dok5,$line2_1);
$line2_1 = str_replace('DOK6',$dok6,$line2_1);
$line2_1 = str_replace('DOK7',$dok7,$line2_1);
$line2_1 = str_replace('DOK8',$dok8,$line2_1);
$line2_1 = str_replace('DOK9',$dok9,$line2_1);
$line2_1 = str_replace('DOK0',$dok0,$line2_1);
$line2_1 = str_replace('DUQ1',$dok11,$line2_1);
$line2_1 = str_replace('DUQ2',$dok12,$line2_1);
}else{
$line2_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line22.txt")){
$line2_2 = file_get_contents("keyboard/home/line22.txt");
if($line2_2 != null ){
$line2_2 = str_replace('DOK1',$dok1,$line2_2);
$line2_2 = str_replace('DOK2',$dok2,$line2_2);
$line2_2 = str_replace('DOK3',$dok3,$line2_2);
$line2_2 = str_replace('DOK4',$dok4,$line2_2);
$line2_2 = str_replace('DOK5',$dok5,$line2_2);
$line2_2 = str_replace('DOK6',$dok6,$line2_2);
$line2_2 = str_replace('DOK7',$dok7,$line2_2);
$line2_2 = str_replace('DOK8',$dok8,$line2_2);
$line2_2 = str_replace('DOK9',$dok9,$line2_2);
$line2_2 = str_replace('DOK0',$dok0,$line2_2);
$line2_2 = str_replace('DUQ1',$dok11,$line2_2);
$line2_2 = str_replace('DUQ2',$dok12,$line2_2);
}else{
$line2_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line23.txt")){
$line2_3 = file_get_contents("keyboard/home/line23.txt");
if($line2_3 != null ){
$line2_3 = str_replace('DOK1',$dok1,$line2_3);
$line2_3 = str_replace('DOK2',$dok2,$line2_3);
$line2_3 = str_replace('DOK3',$dok3,$line2_3);
$line2_3 = str_replace('DOK4',$dok4,$line2_3);
$line2_3 = str_replace('DOK5',$dok5,$line2_3);
$line2_3 = str_replace('DOK6',$dok6,$line2_3);
$line2_3 = str_replace('DOK7',$dok7,$line2_3);
$line2_3 = str_replace('DOK8',$dok8,$line2_3);
$line2_3 = str_replace('DOK9',$dok9,$line2_3);
$line2_3 = str_replace('DOK0',$dok0,$line2_3);
$line2_3 = str_replace('DUQ1',$dok11,$line2_3);
$line2_3 = str_replace('DUQ2',$dok12,$line2_3);
}else{
$line2_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line24.txt")){
$line2_4 = file_get_contents("keyboard/home/line24.txt");
if($line2_4 != null ){
$line2_4 = str_replace('DOK1',$dok1,$line2_4);
$line2_4 = str_replace('DOK2',$dok2,$line2_4);
$line2_4 = str_replace('DOK3',$dok3,$line2_4);
$line2_4 = str_replace('DOK4',$dok4,$line2_4);
$line2_4 = str_replace('DOK5',$dok5,$line2_4);
$line2_4 = str_replace('DOK6',$dok6,$line2_4);
$line2_4 = str_replace('DOK7',$dok7,$line2_4);
$line2_4 = str_replace('DOK8',$dok8,$line2_4);
$line2_4 = str_replace('DOK9',$dok9,$line2_4);
$line2_4 = str_replace('DOK0',$dok0,$line2_4);
$line2_4 = str_replace('DUQ1',$dok11,$line2_4);
$line2_4 = str_replace('DUQ2',$dok12,$line2_4);
}else{
$line2_4 = "➕";
}}

//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line31.txt")){
$line3_1 = file_get_contents("keyboard/home/line31.txt");
if($line3_1 != null ){
$line3_1 = str_replace('DOK1',$dok1,$line3_1);
$line3_1 = str_replace('DOK2',$dok2,$line3_1);
$line3_1 = str_replace('DOK3',$dok3,$line3_1);
$line3_1 = str_replace('DOK4',$dok4,$line3_1);
$line3_1 = str_replace('DOK5',$dok5,$line3_1);
$line3_1 = str_replace('DOK6',$dok6,$line3_1);
$line3_1 = str_replace('DOK7',$dok7,$line3_1);
$line3_1 = str_replace('DOK8',$dok8,$line3_1);
$line3_1 = str_replace('DOK9',$dok9,$line3_1);
$line3_1 = str_replace('DOK0',$dok0,$line3_1);
$line3_1 = str_replace('DUQ1',$dok11,$line3_1);
$line3_1 = str_replace('DUQ2',$dok12,$line3_1);
}else{
$line3_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line32.txt")){
$line3_2 = file_get_contents("keyboard/home/line32.txt");
if($line3_2 != null ){
$line3_2 = str_replace('DOK1',$dok1,$line3_2);
$line3_2 = str_replace('DOK2',$dok2,$line3_2);
$line3_2 = str_replace('DOK3',$dok3,$line3_2);
$line3_2 = str_replace('DOK4',$dok4,$line3_2);
$line3_2 = str_replace('DOK5',$dok5,$line3_2);
$line3_2 = str_replace('DOK6',$dok6,$line3_2);
$line3_2 = str_replace('DOK7',$dok7,$line3_2);
$line3_2 = str_replace('DOK8',$dok8,$line3_2);
$line3_2 = str_replace('DOK9',$dok9,$line3_2);
$line3_2 = str_replace('DOK0',$dok0,$line3_2);
$line3_2 = str_replace('DUQ1',$dok11,$line3_2);
$line3_2 = str_replace('DUQ2',$dok12,$line3_2);
}else{
$line3_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line33.txt")){
$line3_3 = file_get_contents("keyboard/home/line33.txt");
if($line3_3 != null ){
$line3_3 = str_replace('DOK1',$dok1,$line3_3);
$line3_3 = str_replace('DOK2',$dok2,$line3_3);
$line3_3 = str_replace('DOK3',$dok3,$line3_3);
$line3_3 = str_replace('DOK4',$dok4,$line3_3);
$line3_3 = str_replace('DOK5',$dok5,$line3_3);
$line3_3 = str_replace('DOK6',$dok6,$line3_3);
$line3_3 = str_replace('DOK7',$dok7,$line3_3);
$line3_3 = str_replace('DOK8',$dok8,$line3_3);
$line3_3 = str_replace('DOK9',$dok9,$line3_3);
$line3_3 = str_replace('DOK0',$dok0,$line3_3);
$line3_3 = str_replace('DUQ1',$dok11,$line3_3);
$line3_3 = str_replace('DUQ2',$dok12,$line3_3);
}else{
$line3_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line34.txt")){
$line3_4 = file_get_contents("keyboard/home/line34.txt");
if($line3_4 != null ){
$line3_4 = str_replace('DOK1',$dok1,$line3_4);
$line3_4 = str_replace('DOK2',$dok2,$line3_4);
$line3_4 = str_replace('DOK3',$dok3,$line3_4);
$line3_4 = str_replace('DOK4',$dok4,$line3_4);
$line3_4 = str_replace('DOK5',$dok5,$line3_4);
$line3_4 = str_replace('DOK6',$dok6,$line3_4);
$line3_4 = str_replace('DOK7',$dok7,$line3_4);
$line3_4 = str_replace('DOK8',$dok8,$line3_4);
$line3_4 = str_replace('DOK9',$dok9,$line3_4);
$line3_4 = str_replace('DOK0',$dok0,$line3_4);
$line3_4 = str_replace('DUQ1',$dok11,$line3_4);
$line3_4 = str_replace('DUQ2',$dok12,$line3_4);
}else{
$line3_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line41.txt")){
$line4_1 = file_get_contents("keyboard/home/line41.txt");
if($line4_1 != null ){
$line4_1 = str_replace('DOK1',$dok1,$line4_1);
$line4_1 = str_replace('DOK2',$dok2,$line4_1);
$line4_1 = str_replace('DOK3',$dok3,$line4_1);
$line4_1 = str_replace('DOK4',$dok4,$line4_1);
$line4_1 = str_replace('DOK5',$dok5,$line4_1);
$line4_1 = str_replace('DOK6',$dok6,$line4_1);
$line4_1 = str_replace('DOK7',$dok7,$line4_1);
$line4_1 = str_replace('DOK8',$dok8,$line4_1);
$line4_1 = str_replace('DOK9',$dok9,$line4_1);
$line4_1 = str_replace('DOK0',$dok0,$line4_1);
$line4_1 = str_replace('DUQ1',$dok11,$line4_1);
$line4_1 = str_replace('DUQ2',$dok12,$line4_1);
}else{
$line4_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line42.txt")){
$line4_2 = file_get_contents("keyboard/home/line42.txt");
if($line4_2 != null ){
$line4_2 = str_replace('DOK1',$dok1,$line4_2);
$line4_2 = str_replace('DOK2',$dok2,$line4_2);
$line4_2 = str_replace('DOK3',$dok3,$line4_2);
$line4_2 = str_replace('DOK4',$dok4,$line4_2);
$line4_2 = str_replace('DOK5',$dok5,$line4_2);
$line4_2 = str_replace('DOK6',$dok6,$line4_2);
$line4_2 = str_replace('DOK7',$dok7,$line4_2);
$line4_2 = str_replace('DOK8',$dok8,$line4_2);
$line4_2 = str_replace('DOK9',$dok9,$line4_2);
$line4_2 = str_replace('DOK0',$dok0,$line4_2);
$line4_2 = str_replace('DUQ1',$dok11,$line4_2);
$line4_2 = str_replace('DUQ2',$dok12,$line4_2);
}else{
$line4_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line43.txt")){
$line4_3 = file_get_contents("keyboard/home/line43.txt");
if($line4_3 != null ){
$line4_3 = str_replace('DOK1',$dok1,$line4_3);
$line4_3 = str_replace('DOK2',$dok2,$line4_3);
$line4_3 = str_replace('DOK3',$dok3,$line4_3);
$line4_3 = str_replace('DOK4',$dok4,$line4_3);
$line4_3 = str_replace('DOK5',$dok5,$line4_3);
$line4_3 = str_replace('DOK6',$dok6,$line4_3);
$line4_3 = str_replace('DOK7',$dok7,$line4_3);
$line4_3 = str_replace('DOK8',$dok8,$line4_3);
$line4_3 = str_replace('DOK9',$dok9,$line4_3);
$line4_3 = str_replace('DOK0',$dok0,$line4_3);
$line4_3 = str_replace('DUQ1',$dok11,$line4_3);
$line4_3 = str_replace('DUQ2',$dok12,$line4_3);
}else{
$line4_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line44.txt")){
$line4_4 = file_get_contents("keyboard/home/line44.txt");
if($line4_4 != null ){
$line4_4 = str_replace('DOK1',$dok1,$line4_4);
$line4_4 = str_replace('DOK2',$dok2,$line4_4);
$line4_4 = str_replace('DOK3',$dok3,$line4_4);
$line4_4 = str_replace('DOK4',$dok4,$line4_4);
$line4_4 = str_replace('DOK5',$dok5,$line4_4);
$line4_4 = str_replace('DOK6',$dok6,$line4_4);
$line4_4 = str_replace('DOK7',$dok7,$line4_4);
$line4_4 = str_replace('DOK8',$dok8,$line4_4);
$line4_4 = str_replace('DOK9',$dok9,$line4_4);
$line4_4 = str_replace('DOK0',$dok0,$line4_4);
$line4_4 = str_replace('DUQ1',$dok11,$line4_4);
$line4_4 = str_replace('DUQ2',$dok12,$line4_4);
}else{
$line4_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line51.txt")){
$line5_1 = file_get_contents("keyboard/home/line51.txt");
if($line5_1 != null ){
$line5_1 = str_replace('DOK1',$dok1,$line5_1);
$line5_1 = str_replace('DOK2',$dok2,$line5_1);
$line5_1 = str_replace('DOK3',$dok3,$line5_1);
$line5_1 = str_replace('DOK4',$dok4,$line5_1);
$line5_1 = str_replace('DOK5',$dok5,$line5_1);
$line5_1 = str_replace('DOK6',$dok6,$line5_1);
$line5_1 = str_replace('DOK7',$dok7,$line5_1);
$line5_1 = str_replace('DOK8',$dok8,$line5_1);
$line5_1 = str_replace('DOK9',$dok9,$line5_1);
$line5_1 = str_replace('DOK0',$dok0,$line5_1);
$line5_1 = str_replace('DUQ1',$dok11,$line5_1);
$line5_1 = str_replace('DUQ2',$dok12,$line5_1);
}else{
$line5_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line52.txt")){
$line5_2 = file_get_contents("keyboard/home/line52.txt");
if($line5_2 != null ){
$line5_2 = str_replace('DOK1',$dok1,$line5_2);
$line5_2 = str_replace('DOK2',$dok2,$line5_2);
$line5_2 = str_replace('DOK3',$dok3,$line5_2);
$line5_2 = str_replace('DOK4',$dok4,$line5_2);
$line5_2 = str_replace('DOK5',$dok5,$line5_2);
$line5_2 = str_replace('DOK6',$dok6,$line5_2);
$line5_2 = str_replace('DOK7',$dok7,$line5_2);
$line5_2 = str_replace('DOK8',$dok8,$line5_2);
$line5_2 = str_replace('DOK9',$dok9,$line5_2);
$line5_2 = str_replace('DOK0',$dok0,$line5_2);
$line5_2 = str_replace('DUQ1',$dok11,$line5_2);
$line5_2 = str_replace('DUQ2',$dok12,$line5_2);
}else{
$line5_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line53.txt")){
$line5_3 = file_get_contents("keyboard/home/line53.txt");
if($line5_3 != null ){
$line5_3 = str_replace('DOK1',$dok1,$line5_3);
$line5_3 = str_replace('DOK2',$dok2,$line5_3);
$line5_3 = str_replace('DOK3',$dok3,$line5_3);
$line5_3 = str_replace('DOK4',$dok4,$line5_3);
$line5_3 = str_replace('DOK5',$dok5,$line5_3);
$line5_3 = str_replace('DOK6',$dok6,$line5_3);
$line5_3 = str_replace('DOK7',$dok7,$line5_3);
$line5_3 = str_replace('DOK8',$dok8,$line5_3);
$line5_3 = str_replace('DOK9',$dok9,$line5_3);
$line5_3 = str_replace('DOK0',$dok0,$line5_3);
$line5_3 = str_replace('DUQ1',$dok11,$line5_3);
$line5_3 = str_replace('DUQ2',$dok12,$line5_3);
}else{
$line5_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line54.txt")){
$line5_4 = file_get_contents("keyboard/home/line54.txt");
if($line5_4 != null ){
$line5_4 = str_replace('DOK1',$dok1,$line5_4);
$line5_4 = str_replace('DOK2',$dok2,$line5_4);
$line5_4 = str_replace('DOK3',$dok3,$line5_4);
$line5_4 = str_replace('DOK4',$dok4,$line5_4);
$line5_4 = str_replace('DOK5',$dok5,$line5_4);
$line5_4 = str_replace('DOK6',$dok6,$line5_4);
$line5_4 = str_replace('DOK7',$dok7,$line5_4);
$line5_4 = str_replace('DOK8',$dok8,$line5_4);
$line5_4 = str_replace('DOK9',$dok9,$line5_4);
$line5_4 = str_replace('DOK0',$dok0,$line5_4);
$line5_4 = str_replace('DUQ1',$dok11,$line5_4);
$line5_4 = str_replace('DUQ2',$dok12,$line5_4);
}else{
$line5_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line61.txt")){
$line6_1 = file_get_contents("keyboard/home/line61.txt");
if($line6_1 != null ){
$line6_1 = str_replace('DOK1',$dok1,$line6_1);
$line6_1 = str_replace('DOK2',$dok2,$line6_1);
$line6_1 = str_replace('DOK3',$dok3,$line6_1);
$line6_1 = str_replace('DOK4',$dok4,$line6_1);
$line6_1 = str_replace('DOK5',$dok5,$line6_1);
$line6_1 = str_replace('DOK6',$dok6,$line6_1);
$line6_1 = str_replace('DOK7',$dok7,$line6_1);
$line6_1 = str_replace('DOK8',$dok8,$line6_1);
$line6_1 = str_replace('DOK9',$dok9,$line6_1);
$line6_1 = str_replace('DOK0',$dok0,$line6_1);
$line6_1 = str_replace('DUQ1',$dok11,$line6_1);
$line6_1 = str_replace('DUQ2',$dok12,$line6_1);
}else{
$line6_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line62.txt")){
$line6_2 = file_get_contents("keyboard/home/line62.txt");
if($line6_2 != null ){
$line6_2 = str_replace('DOK1',$dok1,$line6_2);
$line6_2 = str_replace('DOK2',$dok2,$line6_2);
$line6_2 = str_replace('DOK3',$dok3,$line6_2);
$line6_2 = str_replace('DOK4',$dok4,$line6_2);
$line6_2 = str_replace('DOK5',$dok5,$line6_2);
$line6_2 = str_replace('DOK6',$dok6,$line6_2);
$line6_2 = str_replace('DOK7',$dok7,$line6_2);
$line6_2 = str_replace('DOK8',$dok8,$line6_2);
$line6_2 = str_replace('DOK9',$dok9,$line6_2);
$line6_2 = str_replace('DOK0',$dok0,$line6_2);
$line6_2 = str_replace('DUQ1',$dok11,$line6_2);
$line6_2 = str_replace('DUQ2',$dok12,$line6_2);
}else{
$line6_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line63.txt")){
$line6_3 = file_get_contents("keyboard/home/line63.txt");
if($line6_3 != null ){
$line6_3 = str_replace('DOK1',$dok1,$line6_3);
$line6_3 = str_replace('DOK2',$dok2,$line6_3);
$line6_3 = str_replace('DOK3',$dok3,$line6_3);
$line6_3 = str_replace('DOK4',$dok4,$line6_3);
$line6_3 = str_replace('DOK5',$dok5,$line6_3);
$line6_3 = str_replace('DOK6',$dok6,$line6_3);
$line6_3 = str_replace('DOK7',$dok7,$line6_3);
$line6_3 = str_replace('DOK8',$dok8,$line6_3);
$line6_3 = str_replace('DOK9',$dok9,$line6_3);
$line6_3 = str_replace('DOK0',$dok0,$line6_3);
$line6_3 = str_replace('DUQ1',$dok11,$line6_3);
$line6_3 = str_replace('DUQ2',$dok12,$line6_3);
}else{
$line6_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line64.txt")){
$line6_4 = file_get_contents("keyboard/home/line64.txt");
if($line6_4 != null ){
$line6_4 = str_replace('DOK1',$dok1,$line6_4);
$line6_4 = str_replace('DOK2',$dok2,$line6_4);
$line6_4 = str_replace('DOK3',$dok3,$line6_4);
$line6_4 = str_replace('DOK4',$dok4,$line6_4);
$line6_4 = str_replace('DOK5',$dok5,$line6_4);
$line6_4 = str_replace('DOK6',$dok6,$line6_4);
$line6_4 = str_replace('DOK7',$dok7,$line6_4);
$line6_4 = str_replace('DOK8',$dok8,$line6_4);
$line6_4 = str_replace('DOK9',$dok9,$line6_4);
$line6_4 = str_replace('DOK0',$dok0,$line6_4);
$line6_4 = str_replace('DUQ1',$dok11,$line6_4);
$line6_4 = str_replace('DUQ2',$dok12,$line6_4);
}else{
$line6_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line71.txt")){
$line7_1 = file_get_contents("keyboard/home/line71.txt");
if($line7_1 != null ){
$line7_1 = str_replace('DOK1',$dok1,$line7_1);
$line7_1 = str_replace('DOK2',$dok2,$line7_1);
$line7_1 = str_replace('DOK3',$dok3,$line7_1);
$line7_1 = str_replace('DOK4',$dok4,$line7_1);
$line7_1 = str_replace('DOK5',$dok5,$line7_1);
$line7_1 = str_replace('DOK6',$dok6,$line7_1);
$line7_1 = str_replace('DOK7',$dok7,$line7_1);
$line7_1 = str_replace('DOK8',$dok8,$line7_1);
$line7_1 = str_replace('DOK9',$dok9,$line7_1);
$line7_1 = str_replace('DOK0',$dok0,$line7_1);
$line7_1 = str_replace('DUQ1',$dok11,$line7_1);
$line7_1 = str_replace('DUQ2',$dok12,$line7_1);
}else{
$line7_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line72.txt")){
$line7_2 = file_get_contents("keyboard/home/line72.txt");
if($line7_2 != null ){
$line7_2 = str_replace('DOK1',$dok1,$line7_2);
$line7_2 = str_replace('DOK2',$dok2,$line7_2);
$line7_2 = str_replace('DOK3',$dok3,$line7_2);
$line7_2 = str_replace('DOK4',$dok4,$line7_2);
$line7_2 = str_replace('DOK5',$dok5,$line7_2);
$line7_2 = str_replace('DOK6',$dok6,$line7_2);
$line7_2 = str_replace('DOK7',$dok7,$line7_2);
$line7_2 = str_replace('DOK8',$dok8,$line7_2);
$line7_2 = str_replace('DOK9',$dok9,$line7_2);
$line7_2 = str_replace('DOK0',$dok0,$line7_2);
$line7_2 = str_replace('DUQ1',$dok11,$line7_2);
$line7_2 = str_replace('DUQ2',$dok12,$line7_2);
}else{
$line7_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line73.txt")){
$line7_3 = file_get_contents("keyboard/home/line73.txt");
if($line7_3 != null ){
$line7_3 = str_replace('DOK1',$dok1,$line7_3);
$line7_3 = str_replace('DOK2',$dok2,$line7_3);
$line7_3 = str_replace('DOK3',$dok3,$line7_3);
$line7_3 = str_replace('DOK4',$dok4,$line7_3);
$line7_3 = str_replace('DOK5',$dok5,$line7_3);
$line7_3 = str_replace('DOK6',$dok6,$line7_3);
$line7_3 = str_replace('DOK7',$dok7,$line7_3);
$line7_3 = str_replace('DOK8',$dok8,$line7_3);
$line7_3 = str_replace('DOK9',$dok9,$line7_3);
$line7_3 = str_replace('DOK0',$dok0,$line7_3);
$line7_3 = str_replace('DUQ1',$dok11,$line7_3);
$line7_3 = str_replace('DUQ2',$dok12,$line7_3);
}else{
$line7_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line74.txt")){
$line7_4 = file_get_contents("keyboard/home/line74.txt");
if($line7_4 != null ){
$line7_4 = str_replace('DOK1',$dok1,$line7_4);
$line7_4 = str_replace('DOK2',$dok2,$line7_4);
$line7_4 = str_replace('DOK3',$dok3,$line7_4);
$line7_4 = str_replace('DOK4',$dok4,$line7_4);
$line7_4 = str_replace('DOK5',$dok5,$line7_4);
$line7_4 = str_replace('DOK6',$dok6,$line7_4);
$line7_4 = str_replace('DOK7',$dok7,$line7_4);
$line7_4 = str_replace('DOK8',$dok8,$line7_4);
$line7_4 = str_replace('DOK9',$dok9,$line7_4);
$line7_4 = str_replace('DOK0',$dok0,$line7_4);
$line7_4 = str_replace('DUQ1',$dok11,$line7_4);
$line7_4 = str_replace('DUQ2',$dok12,$line7_4);
}else{
$line7_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line81.txt")){
$line8_1 = file_get_contents("keyboard/home/line81.txt");
if($line8_1 != null ){
$line8_1 = str_replace('DOK1',$dok1,$line8_1);
$line8_1 = str_replace('DOK2',$dok2,$line8_1);
$line8_1 = str_replace('DOK3',$dok3,$line8_1);
$line8_1 = str_replace('DOK4',$dok4,$line8_1);
$line8_1 = str_replace('DOK5',$dok5,$line8_1);
$line8_1 = str_replace('DOK6',$dok6,$line8_1);
$line8_1 = str_replace('DOK7',$dok7,$line8_1);
$line8_1 = str_replace('DOK8',$dok8,$line8_1);
$line8_1 = str_replace('DOK9',$dok9,$line8_1);
$line8_1 = str_replace('DOK0',$dok0,$line8_1);
$line8_1 = str_replace('DUQ1',$dok11,$line8_1);
$line8_1 = str_replace('DUQ2',$dok12,$line8_1);
}else{
$line8_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line82.txt")){
$line8_2 = file_get_contents("keyboard/home/line82.txt");
if($line8_2 != null ){
$line8_2 = str_replace('DOK1',$dok1,$line8_2);
$line8_2 = str_replace('DOK2',$dok2,$line8_2);
$line8_2 = str_replace('DOK3',$dok3,$line8_2);
$line8_2 = str_replace('DOK4',$dok4,$line8_2);
$line8_2 = str_replace('DOK5',$dok5,$line8_2);
$line8_2 = str_replace('DOK6',$dok6,$line8_2);
$line8_2 = str_replace('DOK7',$dok7,$line8_2);
$line8_2 = str_replace('DOK8',$dok8,$line8_2);
$line8_2 = str_replace('DOK9',$dok9,$line8_2);
$line8_2 = str_replace('DOK0',$dok0,$line8_2);
$line8_2 = str_replace('DUQ1',$dok11,$line8_2);
$line8_2 = str_replace('DUQ2',$dok12,$line8_2);
}else{
$line8_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line83.txt")){
$line8_3 = file_get_contents("keyboard/home/line83.txt");
if($line8_3 != null ){
$line8_3 = str_replace('DOK1',$dok1,$line8_3);
$line8_3 = str_replace('DOK2',$dok2,$line8_3);
$line8_3 = str_replace('DOK3',$dok3,$line8_3);
$line8_3 = str_replace('DOK4',$dok4,$line8_3);
$line8_3 = str_replace('DOK5',$dok5,$line8_3);
$line8_3 = str_replace('DOK6',$dok6,$line8_3);
$line8_3 = str_replace('DOK7',$dok7,$line8_3);
$line8_3 = str_replace('DOK8',$dok8,$line8_3);
$line8_3 = str_replace('DOK9',$dok9,$line8_3);
$line8_3 = str_replace('DOK0',$dok0,$line8_3);
$line8_3 = str_replace('DUQ1',$dok11,$line8_3);
$line8_3 = str_replace('DUQ2',$dok12,$line8_3);
}else{
$line8_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line84.txt")){
$line8_4 = file_get_contents("keyboard/home/line84.txt");
if($line8_4 != null ){
$line8_4 = str_replace('DOK1',$dok1,$line8_4);
$line8_4 = str_replace('DOK2',$dok2,$line8_4);
$line8_4 = str_replace('DOK3',$dok3,$line8_4);
$line8_4 = str_replace('DOK4',$dok4,$line8_4);
$line8_4 = str_replace('DOK5',$dok5,$line8_4);
$line8_4 = str_replace('DOK6',$dok6,$line8_4);
$line8_4 = str_replace('DOK7',$dok7,$line8_4);
$line8_4 = str_replace('DOK8',$dok8,$line8_4);
$line8_4 = str_replace('DOK9',$dok9,$line8_4);
$line8_4 = str_replace('DOK0',$dok0,$line8_4);
$line8_4 = str_replace('DUQ1',$dok11,$line8_4);
$line8_4 = str_replace('DUQ2',$dok12,$line8_4);
}else{
$line8_4 = "➕";
}}
$Button_set0 = json_encode(['inline_keyboard'=>[
[['text'=>"$line1_1",'callback_data'=>'set-line11'],
['text'=>"$line1_2",'callback_data'=>'set-line12'],
['text'=>"$line1_3",'callback_data'=>'set-line13'],
['text'=>"$line1_4",'callback_data'=>'set-line14']],
[['text'=>"$line2_1",'callback_data'=>'set-line21'],
['text'=>"$line2_2",'callback_data'=>'set-line22'],
['text'=>"$line2_3",'callback_data'=>'set-line23'],
['text'=>"$line2_4",'callback_data'=>'set-line24']],
[['text'=>"$line3_1",'callback_data'=>'set-line31'],
['text'=>"$line3_2",'callback_data'=>'set-line32'],
['text'=>"$line3_3",'callback_data'=>'set-line33'],
['text'=>"$line3_4",'callback_data'=>'set-line34']],
[['text'=>"$line4_1",'callback_data'=>'set-line41'],
['text'=>"$line4_2",'callback_data'=>'set-line42'],
['text'=>"$line4_3",'callback_data'=>'set-line43'],
['text'=>"$line4_4",'callback_data'=>'set-line44']],
[['text'=>"$line5_1",'callback_data'=>'set-line51'],
['text'=>"$line5_2",'callback_data'=>'set-line52'],
['text'=>"$line5_3",'callback_data'=>'set-line53'],
['text'=>"$line5_4",'callback_data'=>'set-line54']],
[['text'=>"$line6_1",'callback_data'=>'set-line61'],
['text'=>"$line6_2",'callback_data'=>'set-line62'],
['text'=>"$line6_3",'callback_data'=>'set-line63'],
['text'=>"$line6_4",'callback_data'=>'set-line64']],
[['text'=>"$line7_1",'callback_data'=>'set-line71'],
['text'=>"$line7_2",'callback_data'=>'set-line72'],
['text'=>"$line7_3",'callback_data'=>'set-line73'],
['text'=>"$line7_4",'callback_data'=>'set-line74']],
[['text'=>"$line8_1",'callback_data'=>'set-line81'],
['text'=>"$line8_2",'callback_data'=>'set-line82'],
['text'=>"$line8_3",'callback_data'=>'set-line83'],
['text'=>"$line8_4",'callback_data'=>'set-line84']],
]]);
SM($chatID,"✅با استفاده از تنظیمات این بخش میتوانید چینش کیبورد منوی ساخت ربات را شخصی سازی کنید

پس از اعمال تغییرات جهت بروزرسانی کیبورد /start بزنید",'html',null,$Button_set0);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\///
elseif(preg_match('/^set-(.*)/', $data, $match)){
$dok = $match[1];
$Button_set_dok = json_encode(['inline_keyboard'=>[
[['text'=>"$dok7",'callback_data'=>"sete-DOK7_$dok"]],
[['text'=>"$dok2",'callback_data'=>"sete-DOK2_$dok"],
['text'=>"$dok1",'callback_data'=>"sete-DOK1_$dok"]],
[['text'=>"$dok3",'callback_data'=>"sete-DOK3_$dok"],
['text'=>"$dok12",'callback_data'=>"sete-DUQ2_$dok"],
['text'=>"$dok4",'callback_data'=>"sete-DOK4_$dok"]],
[['text'=>"$dok0",'callback_data'=>"sete-DOK0_$dok"],
['text'=>"$dok9",'callback_data'=>"sete-DOK9_$dok"],
['text'=>"$dok8",'callback_data'=>"sete-DOK8_$dok"]],
[['text'=>"$dok6",'callback_data'=>"sete-DOK6_$dok"],
['text'=>"$dok11",'callback_data'=>"sete-DUQ1_$dok"],
['text'=>"$dok5",'callback_data'=>"sete-DOK5_$dok"]],
[['text'=>"🔰خالی🔰",'callback_data'=>"del-$dok"]],
]]);
Editmessagetext($chatID, $msg_id,"👈️ گزینه مورد نظر را انتخاب نمائید.",$Button_set_dok);
saveJson("melat/$userID.json",$user);
}
//////////------------------------\\\\\\\\\\\\\\//
elseif(preg_match('/^sete-(.*)_(.*)/', $data, $match)){
$name = $match[1];
$doke = $match[2];
Save("keyboard/home/$doke.txt",$name);
//////////------------------------\\\\\\\\\\\\\\///
if(file_exists("keyboard/home/line11.txt")){
$line1_1 = file_get_contents("keyboard/home/line11.txt");
if($line1_1 != null ){
$line1_1 = str_replace('DOK1',$dok1,$line1_1);
$line1_1 = str_replace('DOK2',$dok2,$line1_1);
$line1_1 = str_replace('DOK3',$dok3,$line1_1);
$line1_1 = str_replace('DOK4',$dok4,$line1_1);
$line1_1 = str_replace('DOK5',$dok5,$line1_1);
$line1_1 = str_replace('DOK6',$dok6,$line1_1);
$line1_1 = str_replace('DOK7',$dok7,$line1_1);
$line1_1 = str_replace('DOK8',$dok8,$line1_1);
$line1_1 = str_replace('DOK9',$dok9,$line1_1);
$line1_1 = str_replace('DOK0',$dok0,$line1_1);
$line1_1 = str_replace('DUQ1',$dok11,$line1_1);
$line1_1 = str_replace('DUQ2',$dok12,$line1_1);
}else{
$line1_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line12.txt")){
$line1_2 = file_get_contents("keyboard/home/line12.txt");
if($line1_2 != null ){
$line1_2 = str_replace('DOK1',$dok1,$line1_2);
$line1_2 = str_replace('DOK2',$dok2,$line1_2);
$line1_2 = str_replace('DOK3',$dok3,$line1_2);
$line1_2 = str_replace('DOK4',$dok4,$line1_2);
$line1_2 = str_replace('DOK5',$dok5,$line1_2);
$line1_2 = str_replace('DOK6',$dok6,$line1_2);
$line1_2 = str_replace('DOK7',$dok7,$line1_2);
$line1_2 = str_replace('DOK8',$dok8,$line1_2);
$line1_2 = str_replace('DOK9',$dok9,$line1_2);
$line1_2 = str_replace('DOK0',$dok0,$line1_2);
$line1_2 = str_replace('DUQ1',$dok11,$line1_2);
$line1_2 = str_replace('DUQ2',$dok12,$line1_2);
}else{
$line1_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line13.txt")){
$line1_3 = file_get_contents("keyboard/home/line13.txt");
if($line1_3 != null ){
$line1_3 = str_replace('DOK1',$dok1,$line1_3);
$line1_3 = str_replace('DOK2',$dok2,$line1_3);
$line1_3 = str_replace('DOK3',$dok3,$line1_3);
$line1_3 = str_replace('DOK4',$dok4,$line1_3);
$line1_3 = str_replace('DOK5',$dok5,$line1_3);
$line1_3 = str_replace('DOK6',$dok6,$line1_3);
$line1_3 = str_replace('DOK7',$dok7,$line1_3);
$line1_3 = str_replace('DOK8',$dok8,$line1_3);
$line1_3 = str_replace('DOK9',$dok9,$line1_3);
$line1_3 = str_replace('DOK0',$dok0,$line1_3);
$line1_3 = str_replace('DUQ1',$dok11,$line1_3);
$line1_3 = str_replace('DUQ2',$dok12,$line1_3);
}else{
$line1_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line14.txt")){
$line1_4 = file_get_contents("keyboard/home/line14.txt");
if($line1_4 != null ){
$line1_4 = str_replace('DOK1',$dok1,$line1_4);
$line1_4 = str_replace('DOK2',$dok2,$line1_4);
$line1_4 = str_replace('DOK3',$dok3,$line1_4);
$line1_4 = str_replace('DOK4',$dok4,$line1_4);
$line1_4 = str_replace('DOK5',$dok5,$line1_4);
$line1_4 = str_replace('DOK6',$dok6,$line1_4);
$line1_4 = str_replace('DOK7',$dok7,$line1_4);
$line1_4 = str_replace('DOK8',$dok8,$line1_4);
$line1_4 = str_replace('DOK9',$dok9,$line1_4);
$line1_4 = str_replace('DOK0',$dok0,$line1_4);
$line1_4 = str_replace('DUQ1',$dok11,$line1_4);
$line1_4 = str_replace('DUQ2',$dok12,$line1_4);
}else{
$line1_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line21.txt")){
$line2_1 = file_get_contents("keyboard/home/line21.txt");
if($line2_1 != null ){
$line2_1 = str_replace('DOK1',$dok1,$line2_1);
$line2_1 = str_replace('DOK2',$dok2,$line2_1);
$line2_1 = str_replace('DOK3',$dok3,$line2_1);
$line2_1 = str_replace('DOK4',$dok4,$line2_1);
$line2_1 = str_replace('DOK5',$dok5,$line2_1);
$line2_1 = str_replace('DOK6',$dok6,$line2_1);
$line2_1 = str_replace('DOK7',$dok7,$line2_1);
$line2_1 = str_replace('DOK8',$dok8,$line2_1);
$line2_1 = str_replace('DOK9',$dok9,$line2_1);
$line2_1 = str_replace('DOK0',$dok0,$line2_1);
$line2_1 = str_replace('DUQ1',$dok11,$line2_1);
$line2_1 = str_replace('DUQ2',$dok12,$line2_1);
}else{
$line2_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line22.txt")){
$line2_2 = file_get_contents("keyboard/home/line22.txt");
if($line2_2 != null ){
$line2_2 = str_replace('DOK1',$dok1,$line2_2);
$line2_2 = str_replace('DOK2',$dok2,$line2_2);
$line2_2 = str_replace('DOK3',$dok3,$line2_2);
$line2_2 = str_replace('DOK4',$dok4,$line2_2);
$line2_2 = str_replace('DOK5',$dok5,$line2_2);
$line2_2 = str_replace('DOK6',$dok6,$line2_2);
$line2_2 = str_replace('DOK7',$dok7,$line2_2);
$line2_2 = str_replace('DOK8',$dok8,$line2_2);
$line2_2 = str_replace('DOK9',$dok9,$line2_2);
$line2_2 = str_replace('DOK0',$dok0,$line2_2);
$line2_2 = str_replace('DUQ1',$dok11,$line2_2);
$line2_2 = str_replace('DUQ2',$dok12,$line2_2);
}else{
$line2_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line23.txt")){
$line2_3 = file_get_contents("keyboard/home/line23.txt");
if($line2_3 != null ){
$line2_3 = str_replace('DOK1',$dok1,$line2_3);
$line2_3 = str_replace('DOK2',$dok2,$line2_3);
$line2_3 = str_replace('DOK3',$dok3,$line2_3);
$line2_3 = str_replace('DOK4',$dok4,$line2_3);
$line2_3 = str_replace('DOK5',$dok5,$line2_3);
$line2_3 = str_replace('DOK6',$dok6,$line2_3);
$line2_3 = str_replace('DOK7',$dok7,$line2_3);
$line2_3 = str_replace('DOK8',$dok8,$line2_3);
$line2_3 = str_replace('DOK9',$dok9,$line2_3);
$line2_3 = str_replace('DOK0',$dok0,$line2_3);
$line2_3 = str_replace('DUQ1',$dok11,$line2_3);
$line2_3 = str_replace('DUQ2',$dok12,$line2_3);
}else{
$line2_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line24.txt")){
$line2_4 = file_get_contents("keyboard/home/line24.txt");
if($line2_4 != null ){
$line2_4 = str_replace('DOK1',$dok1,$line2_4);
$line2_4 = str_replace('DOK2',$dok2,$line2_4);
$line2_4 = str_replace('DOK3',$dok3,$line2_4);
$line2_4 = str_replace('DOK4',$dok4,$line2_4);
$line2_4 = str_replace('DOK5',$dok5,$line2_4);
$line2_4 = str_replace('DOK6',$dok6,$line2_4);
$line2_4 = str_replace('DOK7',$dok7,$line2_4);
$line2_4 = str_replace('DOK8',$dok8,$line2_4);
$line2_4 = str_replace('DOK9',$dok9,$line2_4);
$line2_4 = str_replace('DOK0',$dok0,$line2_4);
$line2_4 = str_replace('DUQ1',$dok11,$line2_4);
$line2_4 = str_replace('DUQ2',$dok12,$line2_4);
}else{
$line2_4 = "➕";
}}

//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line31.txt")){
$line3_1 = file_get_contents("keyboard/home/line31.txt");
if($line3_1 != null ){
$line3_1 = str_replace('DOK1',$dok1,$line3_1);
$line3_1 = str_replace('DOK2',$dok2,$line3_1);
$line3_1 = str_replace('DOK3',$dok3,$line3_1);
$line3_1 = str_replace('DOK4',$dok4,$line3_1);
$line3_1 = str_replace('DOK5',$dok5,$line3_1);
$line3_1 = str_replace('DOK6',$dok6,$line3_1);
$line3_1 = str_replace('DOK7',$dok7,$line3_1);
$line3_1 = str_replace('DOK8',$dok8,$line3_1);
$line3_1 = str_replace('DOK9',$dok9,$line3_1);
$line3_1 = str_replace('DOK0',$dok0,$line3_1);
$line3_1 = str_replace('DUQ1',$dok11,$line3_1);
$line3_1 = str_replace('DUQ2',$dok12,$line3_1);
}else{
$line3_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line32.txt")){
$line3_2 = file_get_contents("keyboard/home/line32.txt");
if($line3_2 != null ){
$line3_2 = str_replace('DOK1',$dok1,$line3_2);
$line3_2 = str_replace('DOK2',$dok2,$line3_2);
$line3_2 = str_replace('DOK3',$dok3,$line3_2);
$line3_2 = str_replace('DOK4',$dok4,$line3_2);
$line3_2 = str_replace('DOK5',$dok5,$line3_2);
$line3_2 = str_replace('DOK6',$dok6,$line3_2);
$line3_2 = str_replace('DOK7',$dok7,$line3_2);
$line3_2 = str_replace('DOK8',$dok8,$line3_2);
$line3_2 = str_replace('DOK9',$dok9,$line3_2);
$line3_2 = str_replace('DOK0',$dok0,$line3_2);
$line3_2 = str_replace('DUQ1',$dok11,$line3_2);
$line3_2 = str_replace('DUQ2',$dok12,$line3_2);
}else{
$line3_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line33.txt")){
$line3_3 = file_get_contents("keyboard/home/line33.txt");
if($line3_3 != null ){
$line3_3 = str_replace('DOK1',$dok1,$line3_3);
$line3_3 = str_replace('DOK2',$dok2,$line3_3);
$line3_3 = str_replace('DOK3',$dok3,$line3_3);
$line3_3 = str_replace('DOK4',$dok4,$line3_3);
$line3_3 = str_replace('DOK5',$dok5,$line3_3);
$line3_3 = str_replace('DOK6',$dok6,$line3_3);
$line3_3 = str_replace('DOK7',$dok7,$line3_3);
$line3_3 = str_replace('DOK8',$dok8,$line3_3);
$line3_3 = str_replace('DOK9',$dok9,$line3_3);
$line3_3 = str_replace('DOK0',$dok0,$line3_3);
$line3_3 = str_replace('DUQ1',$dok11,$line3_3);
$line3_3 = str_replace('DUQ2',$dok12,$line3_3);
}else{
$line3_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line34.txt")){
$line3_4 = file_get_contents("keyboard/home/line34.txt");
if($line3_4 != null ){
$line3_4 = str_replace('DOK1',$dok1,$line3_4);
$line3_4 = str_replace('DOK2',$dok2,$line3_4);
$line3_4 = str_replace('DOK3',$dok3,$line3_4);
$line3_4 = str_replace('DOK4',$dok4,$line3_4);
$line3_4 = str_replace('DOK5',$dok5,$line3_4);
$line3_4 = str_replace('DOK6',$dok6,$line3_4);
$line3_4 = str_replace('DOK7',$dok7,$line3_4);
$line3_4 = str_replace('DOK8',$dok8,$line3_4);
$line3_4 = str_replace('DOK9',$dok9,$line3_4);
$line3_4 = str_replace('DOK0',$dok0,$line3_4);
$line3_4 = str_replace('DUQ1',$dok11,$line3_4);
$line3_4 = str_replace('DUQ2',$dok12,$line3_4);
}else{
$line3_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line41.txt")){
$line4_1 = file_get_contents("keyboard/home/line41.txt");
if($line4_1 != null ){
$line4_1 = str_replace('DOK1',$dok1,$line4_1);
$line4_1 = str_replace('DOK2',$dok2,$line4_1);
$line4_1 = str_replace('DOK3',$dok3,$line4_1);
$line4_1 = str_replace('DOK4',$dok4,$line4_1);
$line4_1 = str_replace('DOK5',$dok5,$line4_1);
$line4_1 = str_replace('DOK6',$dok6,$line4_1);
$line4_1 = str_replace('DOK7',$dok7,$line4_1);
$line4_1 = str_replace('DOK8',$dok8,$line4_1);
$line4_1 = str_replace('DOK9',$dok9,$line4_1);
$line4_1 = str_replace('DOK0',$dok0,$line4_1);
$line4_1 = str_replace('DUQ1',$dok11,$line4_1);
$line4_1 = str_replace('DUQ2',$dok12,$line4_1);
}else{
$line4_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line42.txt")){
$line4_2 = file_get_contents("keyboard/home/line42.txt");
if($line4_2 != null ){
$line4_2 = str_replace('DOK1',$dok1,$line4_2);
$line4_2 = str_replace('DOK2',$dok2,$line4_2);
$line4_2 = str_replace('DOK3',$dok3,$line4_2);
$line4_2 = str_replace('DOK4',$dok4,$line4_2);
$line4_2 = str_replace('DOK5',$dok5,$line4_2);
$line4_2 = str_replace('DOK6',$dok6,$line4_2);
$line4_2 = str_replace('DOK7',$dok7,$line4_2);
$line4_2 = str_replace('DOK8',$dok8,$line4_2);
$line4_2 = str_replace('DOK9',$dok9,$line4_2);
$line4_2 = str_replace('DOK0',$dok0,$line4_2);
$line4_2 = str_replace('DUQ1',$dok11,$line4_2);
$line4_2 = str_replace('DUQ2',$dok12,$line4_2);
}else{
$line4_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line43.txt")){
$line4_3 = file_get_contents("keyboard/home/line43.txt");
if($line4_3 != null ){
$line4_3 = str_replace('DOK1',$dok1,$line4_3);
$line4_3 = str_replace('DOK2',$dok2,$line4_3);
$line4_3 = str_replace('DOK3',$dok3,$line4_3);
$line4_3 = str_replace('DOK4',$dok4,$line4_3);
$line4_3 = str_replace('DOK5',$dok5,$line4_3);
$line4_3 = str_replace('DOK6',$dok6,$line4_3);
$line4_3 = str_replace('DOK7',$dok7,$line4_3);
$line4_3 = str_replace('DOK8',$dok8,$line4_3);
$line4_3 = str_replace('DOK9',$dok9,$line4_3);
$line4_3 = str_replace('DOK0',$dok0,$line4_3);
$line4_3 = str_replace('DUQ1',$dok11,$line4_3);
$line4_3 = str_replace('DUQ2',$dok12,$line4_3);
}else{
$line4_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line44.txt")){
$line4_4 = file_get_contents("keyboard/home/line44.txt");
if($line4_4 != null ){
$line4_4 = str_replace('DOK1',$dok1,$line4_4);
$line4_4 = str_replace('DOK2',$dok2,$line4_4);
$line4_4 = str_replace('DOK3',$dok3,$line4_4);
$line4_4 = str_replace('DOK4',$dok4,$line4_4);
$line4_4 = str_replace('DOK5',$dok5,$line4_4);
$line4_4 = str_replace('DOK6',$dok6,$line4_4);
$line4_4 = str_replace('DOK7',$dok7,$line4_4);
$line4_4 = str_replace('DOK8',$dok8,$line4_4);
$line4_4 = str_replace('DOK9',$dok9,$line4_4);
$line4_4 = str_replace('DOK0',$dok0,$line4_4);
$line4_4 = str_replace('DUQ1',$dok11,$line4_4);
$line4_4 = str_replace('DUQ2',$dok12,$line4_4);
}else{
$line4_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line51.txt")){
$line5_1 = file_get_contents("keyboard/home/line51.txt");
if($line5_1 != null ){
$line5_1 = str_replace('DOK1',$dok1,$line5_1);
$line5_1 = str_replace('DOK2',$dok2,$line5_1);
$line5_1 = str_replace('DOK3',$dok3,$line5_1);
$line5_1 = str_replace('DOK4',$dok4,$line5_1);
$line5_1 = str_replace('DOK5',$dok5,$line5_1);
$line5_1 = str_replace('DOK6',$dok6,$line5_1);
$line5_1 = str_replace('DOK7',$dok7,$line5_1);
$line5_1 = str_replace('DOK8',$dok8,$line5_1);
$line5_1 = str_replace('DOK9',$dok9,$line5_1);
$line5_1 = str_replace('DOK0',$dok0,$line5_1);
$line5_1 = str_replace('DUQ1',$dok11,$line5_1);
$line5_1 = str_replace('DUQ2',$dok12,$line5_1);
}else{
$line5_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line52.txt")){
$line5_2 = file_get_contents("keyboard/home/line52.txt");
if($line5_2 != null ){
$line5_2 = str_replace('DOK1',$dok1,$line5_2);
$line5_2 = str_replace('DOK2',$dok2,$line5_2);
$line5_2 = str_replace('DOK3',$dok3,$line5_2);
$line5_2 = str_replace('DOK4',$dok4,$line5_2);
$line5_2 = str_replace('DOK5',$dok5,$line5_2);
$line5_2 = str_replace('DOK6',$dok6,$line5_2);
$line5_2 = str_replace('DOK7',$dok7,$line5_2);
$line5_2 = str_replace('DOK8',$dok8,$line5_2);
$line5_2 = str_replace('DOK9',$dok9,$line5_2);
$line5_2 = str_replace('DOK0',$dok0,$line5_2);
$line5_2 = str_replace('DUQ1',$dok11,$line5_2);
$line5_2 = str_replace('DUQ2',$dok12,$line5_2);
}else{
$line5_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line53.txt")){
$line5_3 = file_get_contents("keyboard/home/line53.txt");
if($line5_3 != null ){
$line5_3 = str_replace('DOK1',$dok1,$line5_3);
$line5_3 = str_replace('DOK2',$dok2,$line5_3);
$line5_3 = str_replace('DOK3',$dok3,$line5_3);
$line5_3 = str_replace('DOK4',$dok4,$line5_3);
$line5_3 = str_replace('DOK5',$dok5,$line5_3);
$line5_3 = str_replace('DOK6',$dok6,$line5_3);
$line5_3 = str_replace('DOK7',$dok7,$line5_3);
$line5_3 = str_replace('DOK8',$dok8,$line5_3);
$line5_3 = str_replace('DOK9',$dok9,$line5_3);
$line5_3 = str_replace('DOK0',$dok0,$line5_3);
$line5_3 = str_replace('DUQ1',$dok11,$line5_3);
$line5_3 = str_replace('DUQ2',$dok12,$line5_3);
}else{
$line5_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line54.txt")){
$line5_4 = file_get_contents("keyboard/home/line54.txt");
if($line5_4 != null ){
$line5_4 = str_replace('DOK1',$dok1,$line5_4);
$line5_4 = str_replace('DOK2',$dok2,$line5_4);
$line5_4 = str_replace('DOK3',$dok3,$line5_4);
$line5_4 = str_replace('DOK4',$dok4,$line5_4);
$line5_4 = str_replace('DOK5',$dok5,$line5_4);
$line5_4 = str_replace('DOK6',$dok6,$line5_4);
$line5_4 = str_replace('DOK7',$dok7,$line5_4);
$line5_4 = str_replace('DOK8',$dok8,$line5_4);
$line5_4 = str_replace('DOK9',$dok9,$line5_4);
$line5_4 = str_replace('DOK0',$dok0,$line5_4);
$line5_4 = str_replace('DUQ1',$dok11,$line5_4);
$line5_4 = str_replace('DUQ2',$dok12,$line5_4);
}else{
$line5_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line61.txt")){
$line6_1 = file_get_contents("keyboard/home/line61.txt");
if($line6_1 != null ){
$line6_1 = str_replace('DOK1',$dok1,$line6_1);
$line6_1 = str_replace('DOK2',$dok2,$line6_1);
$line6_1 = str_replace('DOK3',$dok3,$line6_1);
$line6_1 = str_replace('DOK4',$dok4,$line6_1);
$line6_1 = str_replace('DOK5',$dok5,$line6_1);
$line6_1 = str_replace('DOK6',$dok6,$line6_1);
$line6_1 = str_replace('DOK7',$dok7,$line6_1);
$line6_1 = str_replace('DOK8',$dok8,$line6_1);
$line6_1 = str_replace('DOK9',$dok9,$line6_1);
$line6_1 = str_replace('DOK0',$dok0,$line6_1);
$line6_1 = str_replace('DUQ1',$dok11,$line6_1);
$line6_1 = str_replace('DUQ2',$dok12,$line6_1);
}else{
$line6_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line62.txt")){
$line6_2 = file_get_contents("keyboard/home/line62.txt");
if($line6_2 != null ){
$line6_2 = str_replace('DOK1',$dok1,$line6_2);
$line6_2 = str_replace('DOK2',$dok2,$line6_2);
$line6_2 = str_replace('DOK3',$dok3,$line6_2);
$line6_2 = str_replace('DOK4',$dok4,$line6_2);
$line6_2 = str_replace('DOK5',$dok5,$line6_2);
$line6_2 = str_replace('DOK6',$dok6,$line6_2);
$line6_2 = str_replace('DOK7',$dok7,$line6_2);
$line6_2 = str_replace('DOK8',$dok8,$line6_2);
$line6_2 = str_replace('DOK9',$dok9,$line6_2);
$line6_2 = str_replace('DOK0',$dok0,$line6_2);
$line6_2 = str_replace('DUQ1',$dok11,$line6_2);
$line6_2 = str_replace('DUQ2',$dok12,$line6_2);
}else{
$line6_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line63.txt")){
$line6_3 = file_get_contents("keyboard/home/line63.txt");
if($line6_3 != null ){
$line6_3 = str_replace('DOK1',$dok1,$line6_3);
$line6_3 = str_replace('DOK2',$dok2,$line6_3);
$line6_3 = str_replace('DOK3',$dok3,$line6_3);
$line6_3 = str_replace('DOK4',$dok4,$line6_3);
$line6_3 = str_replace('DOK5',$dok5,$line6_3);
$line6_3 = str_replace('DOK6',$dok6,$line6_3);
$line6_3 = str_replace('DOK7',$dok7,$line6_3);
$line6_3 = str_replace('DOK8',$dok8,$line6_3);
$line6_3 = str_replace('DOK9',$dok9,$line6_3);
$line6_3 = str_replace('DOK0',$dok0,$line6_3);
$line6_3 = str_replace('DUQ1',$dok11,$line6_3);
$line6_3 = str_replace('DUQ2',$dok12,$line6_3);
}else{
$line6_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line64.txt")){
$line6_4 = file_get_contents("keyboard/home/line64.txt");
if($line6_4 != null ){
$line6_4 = str_replace('DOK1',$dok1,$line6_4);
$line6_4 = str_replace('DOK2',$dok2,$line6_4);
$line6_4 = str_replace('DOK3',$dok3,$line6_4);
$line6_4 = str_replace('DOK4',$dok4,$line6_4);
$line6_4 = str_replace('DOK5',$dok5,$line6_4);
$line6_4 = str_replace('DOK6',$dok6,$line6_4);
$line6_4 = str_replace('DOK7',$dok7,$line6_4);
$line6_4 = str_replace('DOK8',$dok8,$line6_4);
$line6_4 = str_replace('DOK9',$dok9,$line6_4);
$line6_4 = str_replace('DOK0',$dok0,$line6_4);
$line6_4 = str_replace('DUQ1',$dok11,$line6_4);
$line6_4 = str_replace('DUQ2',$dok12,$line6_4);
}else{
$line6_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line71.txt")){
$line7_1 = file_get_contents("keyboard/home/line71.txt");
if($line7_1 != null ){
$line7_1 = str_replace('DOK1',$dok1,$line7_1);
$line7_1 = str_replace('DOK2',$dok2,$line7_1);
$line7_1 = str_replace('DOK3',$dok3,$line7_1);
$line7_1 = str_replace('DOK4',$dok4,$line7_1);
$line7_1 = str_replace('DOK5',$dok5,$line7_1);
$line7_1 = str_replace('DOK6',$dok6,$line7_1);
$line7_1 = str_replace('DOK7',$dok7,$line7_1);
$line7_1 = str_replace('DOK8',$dok8,$line7_1);
$line7_1 = str_replace('DOK9',$dok9,$line7_1);
$line7_1 = str_replace('DOK0',$dok0,$line7_1);
$line7_1 = str_replace('DUQ1',$dok11,$line7_1);
$line7_1 = str_replace('DUQ2',$dok12,$line7_1);
}else{
$line7_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line72.txt")){
$line7_2 = file_get_contents("keyboard/home/line72.txt");
if($line7_2 != null ){
$line7_2 = str_replace('DOK1',$dok1,$line7_2);
$line7_2 = str_replace('DOK2',$dok2,$line7_2);
$line7_2 = str_replace('DOK3',$dok3,$line7_2);
$line7_2 = str_replace('DOK4',$dok4,$line7_2);
$line7_2 = str_replace('DOK5',$dok5,$line7_2);
$line7_2 = str_replace('DOK6',$dok6,$line7_2);
$line7_2 = str_replace('DOK7',$dok7,$line7_2);
$line7_2 = str_replace('DOK8',$dok8,$line7_2);
$line7_2 = str_replace('DOK9',$dok9,$line7_2);
$line7_2 = str_replace('DOK0',$dok0,$line7_2);
$line7_2 = str_replace('DUQ1',$dok11,$line7_2);
$line7_2 = str_replace('DUQ2',$dok12,$line7_2);
}else{
$line7_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line73.txt")){
$line7_3 = file_get_contents("keyboard/home/line73.txt");
if($line7_3 != null ){
$line7_3 = str_replace('DOK1',$dok1,$line7_3);
$line7_3 = str_replace('DOK2',$dok2,$line7_3);
$line7_3 = str_replace('DOK3',$dok3,$line7_3);
$line7_3 = str_replace('DOK4',$dok4,$line7_3);
$line7_3 = str_replace('DOK5',$dok5,$line7_3);
$line7_3 = str_replace('DOK6',$dok6,$line7_3);
$line7_3 = str_replace('DOK7',$dok7,$line7_3);
$line7_3 = str_replace('DOK8',$dok8,$line7_3);
$line7_3 = str_replace('DOK9',$dok9,$line7_3);
$line7_3 = str_replace('DOK0',$dok0,$line7_3);
$line7_3 = str_replace('DUQ1',$dok11,$line7_3);
$line7_3 = str_replace('DUQ2',$dok12,$line7_3);
}else{
$line7_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line74.txt")){
$line7_4 = file_get_contents("keyboard/home/line74.txt");
if($line7_4 != null ){
$line7_4 = str_replace('DOK1',$dok1,$line7_4);
$line7_4 = str_replace('DOK2',$dok2,$line7_4);
$line7_4 = str_replace('DOK3',$dok3,$line7_4);
$line7_4 = str_replace('DOK4',$dok4,$line7_4);
$line7_4 = str_replace('DOK5',$dok5,$line7_4);
$line7_4 = str_replace('DOK6',$dok6,$line7_4);
$line7_4 = str_replace('DOK7',$dok7,$line7_4);
$line7_4 = str_replace('DOK8',$dok8,$line7_4);
$line7_4 = str_replace('DOK9',$dok9,$line7_4);
$line7_4 = str_replace('DOK0',$dok0,$line7_4);
$line7_4 = str_replace('DUQ1',$dok11,$line7_4);
$line7_4 = str_replace('DUQ2',$dok12,$line7_4);
}else{
$line7_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line81.txt")){
$line8_1 = file_get_contents("keyboard/home/line81.txt");
if($line8_1 != null ){
$line8_1 = str_replace('DOK1',$dok1,$line8_1);
$line8_1 = str_replace('DOK2',$dok2,$line8_1);
$line8_1 = str_replace('DOK3',$dok3,$line8_1);
$line8_1 = str_replace('DOK4',$dok4,$line8_1);
$line8_1 = str_replace('DOK5',$dok5,$line8_1);
$line8_1 = str_replace('DOK6',$dok6,$line8_1);
$line8_1 = str_replace('DOK7',$dok7,$line8_1);
$line8_1 = str_replace('DOK8',$dok8,$line8_1);
$line8_1 = str_replace('DOK9',$dok9,$line8_1);
$line8_1 = str_replace('DOK0',$dok0,$line8_1);
$line8_1 = str_replace('DUQ1',$dok11,$line8_1);
$line8_1 = str_replace('DUQ2',$dok12,$line8_1);
}else{
$line8_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line82.txt")){
$line8_2 = file_get_contents("keyboard/home/line82.txt");
if($line8_2 != null ){
$line8_2 = str_replace('DOK1',$dok1,$line8_2);
$line8_2 = str_replace('DOK2',$dok2,$line8_2);
$line8_2 = str_replace('DOK3',$dok3,$line8_2);
$line8_2 = str_replace('DOK4',$dok4,$line8_2);
$line8_2 = str_replace('DOK5',$dok5,$line8_2);
$line8_2 = str_replace('DOK6',$dok6,$line8_2);
$line8_2 = str_replace('DOK7',$dok7,$line8_2);
$line8_2 = str_replace('DOK8',$dok8,$line8_2);
$line8_2 = str_replace('DOK9',$dok9,$line8_2);
$line8_2 = str_replace('DOK0',$dok0,$line8_2);
$line8_2 = str_replace('DUQ1',$dok11,$line8_2);
$line8_2 = str_replace('DUQ2',$dok12,$line8_2);
}else{
$line8_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line83.txt")){
$line8_3 = file_get_contents("keyboard/home/line83.txt");
if($line8_3 != null ){
$line8_3 = str_replace('DOK1',$dok1,$line8_3);
$line8_3 = str_replace('DOK2',$dok2,$line8_3);
$line8_3 = str_replace('DOK3',$dok3,$line8_3);
$line8_3 = str_replace('DOK4',$dok4,$line8_3);
$line8_3 = str_replace('DOK5',$dok5,$line8_3);
$line8_3 = str_replace('DOK6',$dok6,$line8_3);
$line8_3 = str_replace('DOK7',$dok7,$line8_3);
$line8_3 = str_replace('DOK8',$dok8,$line8_3);
$line8_3 = str_replace('DOK9',$dok9,$line8_3);
$line8_3 = str_replace('DOK0',$dok0,$line8_3);
$line8_3 = str_replace('DUQ1',$dok11,$line8_3);
$line8_3 = str_replace('DUQ2',$dok12,$line8_3);
}else{
$line8_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line84.txt")){
$line8_4 = file_get_contents("keyboard/home/line84.txt");
if($line8_4 != null ){
$line8_4 = str_replace('DOK1',$dok1,$line8_4);
$line8_4 = str_replace('DOK2',$dok2,$line8_4);
$line8_4 = str_replace('DOK3',$dok3,$line8_4);
$line8_4 = str_replace('DOK4',$dok4,$line8_4);
$line8_4 = str_replace('DOK5',$dok5,$line8_4);
$line8_4 = str_replace('DOK6',$dok6,$line8_4);
$line8_4 = str_replace('DOK7',$dok7,$line8_4);
$line8_4 = str_replace('DOK8',$dok8,$line8_4);
$line8_4 = str_replace('DOK9',$dok9,$line8_4);
$line8_4 = str_replace('DOK0',$dok0,$line8_4);
$line8_4 = str_replace('DUQ1',$dok11,$line8_4);
$line8_4 = str_replace('DUQ2',$dok12,$line8_4);
}else{
$line8_4 = "➕";
}}
$Button_sete = json_encode(['inline_keyboard'=>[
[['text'=>"$line1_1",'callback_data'=>'set-line11'],
['text'=>"$line1_2",'callback_data'=>'set-line12'],
['text'=>"$line1_3",'callback_data'=>'set-line13'],
['text'=>"$line1_4",'callback_data'=>'set-line14']],
[['text'=>"$line2_1",'callback_data'=>'set-line21'],
['text'=>"$line2_2",'callback_data'=>'set-line22'],
['text'=>"$line2_3",'callback_data'=>'set-line23'],
['text'=>"$line2_4",'callback_data'=>'set-line24']],
[['text'=>"$line3_1",'callback_data'=>'set-line31'],
['text'=>"$line3_2",'callback_data'=>'set-line32'],
['text'=>"$line3_3",'callback_data'=>'set-line33'],
['text'=>"$line3_4",'callback_data'=>'set-line34']],
[['text'=>"$line4_1",'callback_data'=>'set-line41'],
['text'=>"$line4_2",'callback_data'=>'set-line42'],
['text'=>"$line4_3",'callback_data'=>'set-line43'],
['text'=>"$line4_4",'callback_data'=>'set-line44']],
[['text'=>"$line5_1",'callback_data'=>'set-line51'],
['text'=>"$line5_2",'callback_data'=>'set-line52'],
['text'=>"$line5_3",'callback_data'=>'set-line53'],
['text'=>"$line5_4",'callback_data'=>'set-line54']],
[['text'=>"$line6_1",'callback_data'=>'set-line61'],
['text'=>"$line6_2",'callback_data'=>'set-line62'],
['text'=>"$line6_3",'callback_data'=>'set-line63'],
['text'=>"$line6_4",'callback_data'=>'set-line64']],
[['text'=>"$line7_1",'callback_data'=>'set-line71'],
['text'=>"$line7_2",'callback_data'=>'set-line72'],
['text'=>"$line7_3",'callback_data'=>'set-line73'],
['text'=>"$line7_4",'callback_data'=>'set-line74']],
[['text'=>"$line8_1",'callback_data'=>'set-line81'],
['text'=>"$line8_2",'callback_data'=>'set-line82'],
['text'=>"$line8_3",'callback_data'=>'set-line83'],
['text'=>"$line8_4",'callback_data'=>'set-line84']],
]]);
Editmessagetext($chatID, $msg_id,"👈️ گزینه مورد نظر را انتخاب نمائید.",$Button_sete);
}
elseif(preg_match('/^del-(.*)/', $data, $match)){
$doke = $match[1];
Save("keyboard/home/$doke.txt",null);
//////////------------------------\\\\\\\\\\\\\\///
if(file_exists("keyboard/home/line11.txt")){
$line1_1 = file_get_contents("keyboard/home/line11.txt");
if($line1_1 != null ){
$line1_1 = str_replace('DOK1',$dok1,$line1_1);
$line1_1 = str_replace('DOK2',$dok2,$line1_1);
$line1_1 = str_replace('DOK3',$dok3,$line1_1);
$line1_1 = str_replace('DOK4',$dok4,$line1_1);
$line1_1 = str_replace('DOK5',$dok5,$line1_1);
$line1_1 = str_replace('DOK6',$dok6,$line1_1);
$line1_1 = str_replace('DOK7',$dok7,$line1_1);
$line1_1 = str_replace('DOK8',$dok8,$line1_1);
$line1_1 = str_replace('DOK9',$dok9,$line1_1);
$line1_1 = str_replace('DOK0',$dok0,$line1_1);
$line1_1 = str_replace('DUQ1',$dok11,$line1_1);
$line1_1 = str_replace('DUQ2',$dok12,$line1_1);
}else{
$line1_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line12.txt")){
$line1_2 = file_get_contents("keyboard/home/line12.txt");
if($line1_2 != null ){
$line1_2 = str_replace('DOK1',$dok1,$line1_2);
$line1_2 = str_replace('DOK2',$dok2,$line1_2);
$line1_2 = str_replace('DOK3',$dok3,$line1_2);
$line1_2 = str_replace('DOK4',$dok4,$line1_2);
$line1_2 = str_replace('DOK5',$dok5,$line1_2);
$line1_2 = str_replace('DOK6',$dok6,$line1_2);
$line1_2 = str_replace('DOK7',$dok7,$line1_2);
$line1_2 = str_replace('DOK8',$dok8,$line1_2);
$line1_2 = str_replace('DOK9',$dok9,$line1_2);
$line1_2 = str_replace('DOK0',$dok0,$line1_2);
$line1_2 = str_replace('DUQ1',$dok11,$line1_2);
$line1_2 = str_replace('DUQ2',$dok12,$line1_2);
}else{
$line1_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line13.txt")){
$line1_3 = file_get_contents("keyboard/home/line13.txt");
if($line1_3 != null ){
$line1_3 = str_replace('DOK1',$dok1,$line1_3);
$line1_3 = str_replace('DOK2',$dok2,$line1_3);
$line1_3 = str_replace('DOK3',$dok3,$line1_3);
$line1_3 = str_replace('DOK4',$dok4,$line1_3);
$line1_3 = str_replace('DOK5',$dok5,$line1_3);
$line1_3 = str_replace('DOK6',$dok6,$line1_3);
$line1_3 = str_replace('DOK7',$dok7,$line1_3);
$line1_3 = str_replace('DOK8',$dok8,$line1_3);
$line1_3 = str_replace('DOK9',$dok9,$line1_3);
$line1_3 = str_replace('DOK0',$dok0,$line1_3);
$line1_3 = str_replace('DUQ1',$dok11,$line1_3);
$line1_3 = str_replace('DUQ2',$dok12,$line1_3);
}else{
$line1_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line14.txt")){
$line1_4 = file_get_contents("keyboard/home/line14.txt");
if($line1_4 != null ){
$line1_4 = str_replace('DOK1',$dok1,$line1_4);
$line1_4 = str_replace('DOK2',$dok2,$line1_4);
$line1_4 = str_replace('DOK3',$dok3,$line1_4);
$line1_4 = str_replace('DOK4',$dok4,$line1_4);
$line1_4 = str_replace('DOK5',$dok5,$line1_4);
$line1_4 = str_replace('DOK6',$dok6,$line1_4);
$line1_4 = str_replace('DOK7',$dok7,$line1_4);
$line1_4 = str_replace('DOK8',$dok8,$line1_4);
$line1_4 = str_replace('DOK9',$dok9,$line1_4);
$line1_4 = str_replace('DOK0',$dok0,$line1_4);
$line1_4 = str_replace('DUQ1',$dok11,$line1_4);
$line1_4 = str_replace('DUQ2',$dok12,$line1_4);
}else{
$line1_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line21.txt")){
$line2_1 = file_get_contents("keyboard/home/line21.txt");
if($line2_1 != null ){
$line2_1 = str_replace('DOK1',$dok1,$line2_1);
$line2_1 = str_replace('DOK2',$dok2,$line2_1);
$line2_1 = str_replace('DOK3',$dok3,$line2_1);
$line2_1 = str_replace('DOK4',$dok4,$line2_1);
$line2_1 = str_replace('DOK5',$dok5,$line2_1);
$line2_1 = str_replace('DOK6',$dok6,$line2_1);
$line2_1 = str_replace('DOK7',$dok7,$line2_1);
$line2_1 = str_replace('DOK8',$dok8,$line2_1);
$line2_1 = str_replace('DOK9',$dok9,$line2_1);
$line2_1 = str_replace('DOK0',$dok0,$line2_1);
$line2_1 = str_replace('DUQ1',$dok11,$line2_1);
$line2_1 = str_replace('DUQ2',$dok12,$line2_1);
}else{
$line2_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line22.txt")){
$line2_2 = file_get_contents("keyboard/home/line22.txt");
if($line2_2 != null ){
$line2_2 = str_replace('DOK1',$dok1,$line2_2);
$line2_2 = str_replace('DOK2',$dok2,$line2_2);
$line2_2 = str_replace('DOK3',$dok3,$line2_2);
$line2_2 = str_replace('DOK4',$dok4,$line2_2);
$line2_2 = str_replace('DOK5',$dok5,$line2_2);
$line2_2 = str_replace('DOK6',$dok6,$line2_2);
$line2_2 = str_replace('DOK7',$dok7,$line2_2);
$line2_2 = str_replace('DOK8',$dok8,$line2_2);
$line2_2 = str_replace('DOK9',$dok9,$line2_2);
$line2_2 = str_replace('DOK0',$dok0,$line2_2);
$line2_2 = str_replace('DUQ1',$dok11,$line2_2);
$line2_2 = str_replace('DUQ2',$dok12,$line2_2);
}else{
$line2_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line23.txt")){
$line2_3 = file_get_contents("keyboard/home/line23.txt");
if($line2_3 != null ){
$line2_3 = str_replace('DOK1',$dok1,$line2_3);
$line2_3 = str_replace('DOK2',$dok2,$line2_3);
$line2_3 = str_replace('DOK3',$dok3,$line2_3);
$line2_3 = str_replace('DOK4',$dok4,$line2_3);
$line2_3 = str_replace('DOK5',$dok5,$line2_3);
$line2_3 = str_replace('DOK6',$dok6,$line2_3);
$line2_3 = str_replace('DOK7',$dok7,$line2_3);
$line2_3 = str_replace('DOK8',$dok8,$line2_3);
$line2_3 = str_replace('DOK9',$dok9,$line2_3);
$line2_3 = str_replace('DOK0',$dok0,$line2_3);
$line2_3 = str_replace('DUQ1',$dok11,$line2_3);
$line2_3 = str_replace('DUQ2',$dok12,$line2_3);
}else{
$line2_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line24.txt")){
$line2_4 = file_get_contents("keyboard/home/line24.txt");
if($line2_4 != null ){
$line2_4 = str_replace('DOK1',$dok1,$line2_4);
$line2_4 = str_replace('DOK2',$dok2,$line2_4);
$line2_4 = str_replace('DOK3',$dok3,$line2_4);
$line2_4 = str_replace('DOK4',$dok4,$line2_4);
$line2_4 = str_replace('DOK5',$dok5,$line2_4);
$line2_4 = str_replace('DOK6',$dok6,$line2_4);
$line2_4 = str_replace('DOK7',$dok7,$line2_4);
$line2_4 = str_replace('DOK8',$dok8,$line2_4);
$line2_4 = str_replace('DOK9',$dok9,$line2_4);
$line2_4 = str_replace('DOK0',$dok0,$line2_4);
$line2_4 = str_replace('DUQ1',$dok11,$line2_4);
$line2_4 = str_replace('DUQ2',$dok12,$line2_4);
}else{
$line2_4 = "➕";
}}

//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line31.txt")){
$line3_1 = file_get_contents("keyboard/home/line31.txt");
if($line3_1 != null ){
$line3_1 = str_replace('DOK1',$dok1,$line3_1);
$line3_1 = str_replace('DOK2',$dok2,$line3_1);
$line3_1 = str_replace('DOK3',$dok3,$line3_1);
$line3_1 = str_replace('DOK4',$dok4,$line3_1);
$line3_1 = str_replace('DOK5',$dok5,$line3_1);
$line3_1 = str_replace('DOK6',$dok6,$line3_1);
$line3_1 = str_replace('DOK7',$dok7,$line3_1);
$line3_1 = str_replace('DOK8',$dok8,$line3_1);
$line3_1 = str_replace('DOK9',$dok9,$line3_1);
$line3_1 = str_replace('DOK0',$dok0,$line3_1);
$line3_1 = str_replace('DUQ1',$dok11,$line3_1);
$line3_1 = str_replace('DUQ2',$dok12,$line3_1);
}else{
$line3_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line32.txt")){
$line3_2 = file_get_contents("keyboard/home/line32.txt");
if($line3_2 != null ){
$line3_2 = str_replace('DOK1',$dok1,$line3_2);
$line3_2 = str_replace('DOK2',$dok2,$line3_2);
$line3_2 = str_replace('DOK3',$dok3,$line3_2);
$line3_2 = str_replace('DOK4',$dok4,$line3_2);
$line3_2 = str_replace('DOK5',$dok5,$line3_2);
$line3_2 = str_replace('DOK6',$dok6,$line3_2);
$line3_2 = str_replace('DOK7',$dok7,$line3_2);
$line3_2 = str_replace('DOK8',$dok8,$line3_2);
$line3_2 = str_replace('DOK9',$dok9,$line3_2);
$line3_2 = str_replace('DOK0',$dok0,$line3_2);
$line3_2 = str_replace('DUQ1',$dok11,$line3_2);
$line3_2 = str_replace('DUQ2',$dok12,$line3_2);
}else{
$line3_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line33.txt")){
$line3_3 = file_get_contents("keyboard/home/line33.txt");
if($line3_3 != null ){
$line3_3 = str_replace('DOK1',$dok1,$line3_3);
$line3_3 = str_replace('DOK2',$dok2,$line3_3);
$line3_3 = str_replace('DOK3',$dok3,$line3_3);
$line3_3 = str_replace('DOK4',$dok4,$line3_3);
$line3_3 = str_replace('DOK5',$dok5,$line3_3);
$line3_3 = str_replace('DOK6',$dok6,$line3_3);
$line3_3 = str_replace('DOK7',$dok7,$line3_3);
$line3_3 = str_replace('DOK8',$dok8,$line3_3);
$line3_3 = str_replace('DOK9',$dok9,$line3_3);
$line3_3 = str_replace('DOK0',$dok0,$line3_3);
$line3_3 = str_replace('DUQ1',$dok11,$line3_3);
$line3_3 = str_replace('DUQ2',$dok12,$line3_3);
}else{
$line3_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line34.txt")){
$line3_4 = file_get_contents("keyboard/home/line34.txt");
if($line3_4 != null ){
$line3_4 = str_replace('DOK1',$dok1,$line3_4);
$line3_4 = str_replace('DOK2',$dok2,$line3_4);
$line3_4 = str_replace('DOK3',$dok3,$line3_4);
$line3_4 = str_replace('DOK4',$dok4,$line3_4);
$line3_4 = str_replace('DOK5',$dok5,$line3_4);
$line3_4 = str_replace('DOK6',$dok6,$line3_4);
$line3_4 = str_replace('DOK7',$dok7,$line3_4);
$line3_4 = str_replace('DOK8',$dok8,$line3_4);
$line3_4 = str_replace('DOK9',$dok9,$line3_4);
$line3_4 = str_replace('DOK0',$dok0,$line3_4);
$line3_4 = str_replace('DUQ1',$dok11,$line3_4);
$line3_4 = str_replace('DUQ2',$dok12,$line3_4);
}else{
$line3_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line41.txt")){
$line4_1 = file_get_contents("keyboard/home/line41.txt");
if($line4_1 != null ){
$line4_1 = str_replace('DOK1',$dok1,$line4_1);
$line4_1 = str_replace('DOK2',$dok2,$line4_1);
$line4_1 = str_replace('DOK3',$dok3,$line4_1);
$line4_1 = str_replace('DOK4',$dok4,$line4_1);
$line4_1 = str_replace('DOK5',$dok5,$line4_1);
$line4_1 = str_replace('DOK6',$dok6,$line4_1);
$line4_1 = str_replace('DOK7',$dok7,$line4_1);
$line4_1 = str_replace('DOK8',$dok8,$line4_1);
$line4_1 = str_replace('DOK9',$dok9,$line4_1);
$line4_1 = str_replace('DOK0',$dok0,$line4_1);
$line4_1 = str_replace('DUQ1',$dok11,$line4_1);
$line4_1 = str_replace('DUQ2',$dok12,$line4_1);
}else{
$line4_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line42.txt")){
$line4_2 = file_get_contents("keyboard/home/line42.txt");
if($line4_2 != null ){
$line4_2 = str_replace('DOK1',$dok1,$line4_2);
$line4_2 = str_replace('DOK2',$dok2,$line4_2);
$line4_2 = str_replace('DOK3',$dok3,$line4_2);
$line4_2 = str_replace('DOK4',$dok4,$line4_2);
$line4_2 = str_replace('DOK5',$dok5,$line4_2);
$line4_2 = str_replace('DOK6',$dok6,$line4_2);
$line4_2 = str_replace('DOK7',$dok7,$line4_2);
$line4_2 = str_replace('DOK8',$dok8,$line4_2);
$line4_2 = str_replace('DOK9',$dok9,$line4_2);
$line4_2 = str_replace('DOK0',$dok0,$line4_2);
$line4_2 = str_replace('DUQ1',$dok11,$line4_2);
$line4_2 = str_replace('DUQ2',$dok12,$line4_2);
}else{
$line4_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line43.txt")){
$line4_3 = file_get_contents("keyboard/home/line43.txt");
if($line4_3 != null ){
$line4_3 = str_replace('DOK1',$dok1,$line4_3);
$line4_3 = str_replace('DOK2',$dok2,$line4_3);
$line4_3 = str_replace('DOK3',$dok3,$line4_3);
$line4_3 = str_replace('DOK4',$dok4,$line4_3);
$line4_3 = str_replace('DOK5',$dok5,$line4_3);
$line4_3 = str_replace('DOK6',$dok6,$line4_3);
$line4_3 = str_replace('DOK7',$dok7,$line4_3);
$line4_3 = str_replace('DOK8',$dok8,$line4_3);
$line4_3 = str_replace('DOK9',$dok9,$line4_3);
$line4_3 = str_replace('DOK0',$dok0,$line4_3);
$line4_3 = str_replace('DUQ1',$dok11,$line4_3);
$line4_3 = str_replace('DUQ2',$dok12,$line4_3);
}else{
$line4_3 = "➕";
}}//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line44.txt")){
$line4_4 = file_get_contents("keyboard/home/line44.txt");
if($line4_4 != null ){
$line4_4 = str_replace('DOK1',$dok1,$line4_4);
$line4_4 = str_replace('DOK2',$dok2,$line4_4);
$line4_4 = str_replace('DOK3',$dok3,$line4_4);
$line4_4 = str_replace('DOK4',$dok4,$line4_4);
$line4_4 = str_replace('DOK5',$dok5,$line4_4);
$line4_4 = str_replace('DOK6',$dok6,$line4_4);
$line4_4 = str_replace('DOK7',$dok7,$line4_4);
$line4_4 = str_replace('DOK8',$dok8,$line4_4);
$line4_4 = str_replace('DOK9',$dok9,$line4_4);
$line4_4 = str_replace('DOK0',$dok0,$line4_4);
$line4_4 = str_replace('DUQ1',$dok11,$line4_4);
$line4_4 = str_replace('DUQ2',$dok12,$line4_4);
}else{
$line4_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line51.txt")){
$line5_1 = file_get_contents("keyboard/home/line51.txt");
if($line5_1 != null ){
$line5_1 = str_replace('DOK1',$dok1,$line5_1);
$line5_1 = str_replace('DOK2',$dok2,$line5_1);
$line5_1 = str_replace('DOK3',$dok3,$line5_1);
$line5_1 = str_replace('DOK4',$dok4,$line5_1);
$line5_1 = str_replace('DOK5',$dok5,$line5_1);
$line5_1 = str_replace('DOK6',$dok6,$line5_1);
$line5_1 = str_replace('DOK7',$dok7,$line5_1);
$line5_1 = str_replace('DOK8',$dok8,$line5_1);
$line5_1 = str_replace('DOK9',$dok9,$line5_1);
$line5_1 = str_replace('DOK0',$dok0,$line5_1);
$line5_1 = str_replace('DUQ1',$dok11,$line5_1);
$line5_1 = str_replace('DUQ2',$dok12,$line5_1);
}else{
$line5_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line52.txt")){
$line5_2 = file_get_contents("keyboard/home/line52.txt");
if($line5_2 != null ){
$line5_2 = str_replace('DOK1',$dok1,$line5_2);
$line5_2 = str_replace('DOK2',$dok2,$line5_2);
$line5_2 = str_replace('DOK3',$dok3,$line5_2);
$line5_2 = str_replace('DOK4',$dok4,$line5_2);
$line5_2 = str_replace('DOK5',$dok5,$line5_2);
$line5_2 = str_replace('DOK6',$dok6,$line5_2);
$line5_2 = str_replace('DOK7',$dok7,$line5_2);
$line5_2 = str_replace('DOK8',$dok8,$line5_2);
$line5_2 = str_replace('DOK9',$dok9,$line5_2);
$line5_2 = str_replace('DOK0',$dok0,$line5_2);
$line5_2 = str_replace('DUQ1',$dok11,$line5_2);
$line5_2 = str_replace('DUQ2',$dok12,$line5_2);
}else{
$line5_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line53.txt")){
$line5_3 = file_get_contents("keyboard/home/line53.txt");
if($line5_3 != null ){
$line5_3 = str_replace('DOK1',$dok1,$line5_3);
$line5_3 = str_replace('DOK2',$dok2,$line5_3);
$line5_3 = str_replace('DOK3',$dok3,$line5_3);
$line5_3 = str_replace('DOK4',$dok4,$line5_3);
$line5_3 = str_replace('DOK5',$dok5,$line5_3);
$line5_3 = str_replace('DOK6',$dok6,$line5_3);
$line5_3 = str_replace('DOK7',$dok7,$line5_3);
$line5_3 = str_replace('DOK8',$dok8,$line5_3);
$line5_3 = str_replace('DOK9',$dok9,$line5_3);
$line5_3 = str_replace('DOK0',$dok0,$line5_3);
$line5_3 = str_replace('DUQ1',$dok11,$line5_3);
$line5_3 = str_replace('DUQ2',$dok12,$line5_3);
}else{
$line5_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line54.txt")){
$line5_4 = file_get_contents("keyboard/home/line54.txt");
if($line5_4 != null ){
$line5_4 = str_replace('DOK1',$dok1,$line5_4);
$line5_4 = str_replace('DOK2',$dok2,$line5_4);
$line5_4 = str_replace('DOK3',$dok3,$line5_4);
$line5_4 = str_replace('DOK4',$dok4,$line5_4);
$line5_4 = str_replace('DOK5',$dok5,$line5_4);
$line5_4 = str_replace('DOK6',$dok6,$line5_4);
$line5_4 = str_replace('DOK7',$dok7,$line5_4);
$line5_4 = str_replace('DOK8',$dok8,$line5_4);
$line5_4 = str_replace('DOK9',$dok9,$line5_4);
$line5_4 = str_replace('DOK0',$dok0,$line5_4);
$line5_4 = str_replace('DUQ1',$dok11,$line5_4);
$line5_4 = str_replace('DUQ2',$dok12,$line5_4);
}else{
$line5_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line61.txt")){
$line6_1 = file_get_contents("keyboard/home/line61.txt");
if($line6_1 != null ){
$line6_1 = str_replace('DOK1',$dok1,$line6_1);
$line6_1 = str_replace('DOK2',$dok2,$line6_1);
$line6_1 = str_replace('DOK3',$dok3,$line6_1);
$line6_1 = str_replace('DOK4',$dok4,$line6_1);
$line6_1 = str_replace('DOK5',$dok5,$line6_1);
$line6_1 = str_replace('DOK6',$dok6,$line6_1);
$line6_1 = str_replace('DOK7',$dok7,$line6_1);
$line6_1 = str_replace('DOK8',$dok8,$line6_1);
$line6_1 = str_replace('DOK9',$dok9,$line6_1);
$line6_1 = str_replace('DOK0',$dok0,$line6_1);
$line6_1 = str_replace('DUQ1',$dok11,$line6_1);
$line6_1 = str_replace('DUQ2',$dok12,$line6_1);
}else{
$line6_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line62.txt")){
$line6_2 = file_get_contents("keyboard/home/line62.txt");
if($line6_2 != null ){
$line6_2 = str_replace('DOK1',$dok1,$line6_2);
$line6_2 = str_replace('DOK2',$dok2,$line6_2);
$line6_2 = str_replace('DOK3',$dok3,$line6_2);
$line6_2 = str_replace('DOK4',$dok4,$line6_2);
$line6_2 = str_replace('DOK5',$dok5,$line6_2);
$line6_2 = str_replace('DOK6',$dok6,$line6_2);
$line6_2 = str_replace('DOK7',$dok7,$line6_2);
$line6_2 = str_replace('DOK8',$dok8,$line6_2);
$line6_2 = str_replace('DOK9',$dok9,$line6_2);
$line6_2 = str_replace('DOK0',$dok0,$line6_2);
$line6_2 = str_replace('DUQ1',$dok11,$line6_2);
$line6_2 = str_replace('DUQ2',$dok12,$line6_2);
}else{
$line6_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line63.txt")){
$line6_3 = file_get_contents("keyboard/home/line63.txt");
if($line6_3 != null ){
$line6_3 = str_replace('DOK1',$dok1,$line6_3);
$line6_3 = str_replace('DOK2',$dok2,$line6_3);
$line6_3 = str_replace('DOK3',$dok3,$line6_3);
$line6_3 = str_replace('DOK4',$dok4,$line6_3);
$line6_3 = str_replace('DOK5',$dok5,$line6_3);
$line6_3 = str_replace('DOK6',$dok6,$line6_3);
$line6_3 = str_replace('DOK7',$dok7,$line6_3);
$line6_3 = str_replace('DOK8',$dok8,$line6_3);
$line6_3 = str_replace('DOK9',$dok9,$line6_3);
$line6_3 = str_replace('DOK0',$dok0,$line6_3);
$line6_3 = str_replace('DUQ1',$dok11,$line6_3);
$line6_3 = str_replace('DUQ2',$dok12,$line6_3);
}else{
$line6_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line64.txt")){
$line6_4 = file_get_contents("keyboard/home/line64.txt");
if($line6_4 != null ){
$line6_4 = str_replace('DOK1',$dok1,$line6_4);
$line6_4 = str_replace('DOK2',$dok2,$line6_4);
$line6_4 = str_replace('DOK3',$dok3,$line6_4);
$line6_4 = str_replace('DOK4',$dok4,$line6_4);
$line6_4 = str_replace('DOK5',$dok5,$line6_4);
$line6_4 = str_replace('DOK6',$dok6,$line6_4);
$line6_4 = str_replace('DOK7',$dok7,$line6_4);
$line6_4 = str_replace('DOK8',$dok8,$line6_4);
$line6_4 = str_replace('DOK9',$dok9,$line6_4);
$line6_4 = str_replace('DOK0',$dok0,$line6_4);
$line6_4 = str_replace('DUQ1',$dok11,$line6_4);
$line6_4 = str_replace('DUQ2',$dok12,$line6_4);
}else{
$line6_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line71.txt")){
$line7_1 = file_get_contents("keyboard/home/line71.txt");
if($line7_1 != null ){
$line7_1 = str_replace('DOK1',$dok1,$line7_1);
$line7_1 = str_replace('DOK2',$dok2,$line7_1);
$line7_1 = str_replace('DOK3',$dok3,$line7_1);
$line7_1 = str_replace('DOK4',$dok4,$line7_1);
$line7_1 = str_replace('DOK5',$dok5,$line7_1);
$line7_1 = str_replace('DOK6',$dok6,$line7_1);
$line7_1 = str_replace('DOK7',$dok7,$line7_1);
$line7_1 = str_replace('DOK8',$dok8,$line7_1);
$line7_1 = str_replace('DOK9',$dok9,$line7_1);
$line7_1 = str_replace('DOK0',$dok0,$line7_1);
$line7_1 = str_replace('DUQ1',$dok11,$line7_1);
$line7_1 = str_replace('DUQ2',$dok12,$line7_1);
}else{
$line7_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line72.txt")){
$line7_2 = file_get_contents("keyboard/home/line72.txt");
if($line7_2 != null ){
$line7_2 = str_replace('DOK1',$dok1,$line7_2);
$line7_2 = str_replace('DOK2',$dok2,$line7_2);
$line7_2 = str_replace('DOK3',$dok3,$line7_2);
$line7_2 = str_replace('DOK4',$dok4,$line7_2);
$line7_2 = str_replace('DOK5',$dok5,$line7_2);
$line7_2 = str_replace('DOK6',$dok6,$line7_2);
$line7_2 = str_replace('DOK7',$dok7,$line7_2);
$line7_2 = str_replace('DOK8',$dok8,$line7_2);
$line7_2 = str_replace('DOK9',$dok9,$line7_2);
$line7_2 = str_replace('DOK0',$dok0,$line7_2);
$line7_2 = str_replace('DUQ1',$dok11,$line7_2);
$line7_2 = str_replace('DUQ2',$dok12,$line7_2);
}else{
$line7_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line73.txt")){
$line7_3 = file_get_contents("keyboard/home/line73.txt");
if($line7_3 != null ){
$line7_3 = str_replace('DOK1',$dok1,$line7_3);
$line7_3 = str_replace('DOK2',$dok2,$line7_3);
$line7_3 = str_replace('DOK3',$dok3,$line7_3);
$line7_3 = str_replace('DOK4',$dok4,$line7_3);
$line7_3 = str_replace('DOK5',$dok5,$line7_3);
$line7_3 = str_replace('DOK6',$dok6,$line7_3);
$line7_3 = str_replace('DOK7',$dok7,$line7_3);
$line7_3 = str_replace('DOK8',$dok8,$line7_3);
$line7_3 = str_replace('DOK9',$dok9,$line7_3);
$line7_3 = str_replace('DOK0',$dok0,$line7_3);
$line7_3 = str_replace('DUQ1',$dok11,$line7_3);
$line7_3 = str_replace('DUQ2',$dok12,$line7_3);
}else{
$line7_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line74.txt")){
$line7_4 = file_get_contents("keyboard/home/line74.txt");
if($line7_4 != null ){
$line7_4 = str_replace('DOK1',$dok1,$line7_4);
$line7_4 = str_replace('DOK2',$dok2,$line7_4);
$line7_4 = str_replace('DOK3',$dok3,$line7_4);
$line7_4 = str_replace('DOK4',$dok4,$line7_4);
$line7_4 = str_replace('DOK5',$dok5,$line7_4);
$line7_4 = str_replace('DOK6',$dok6,$line7_4);
$line7_4 = str_replace('DOK7',$dok7,$line7_4);
$line7_4 = str_replace('DOK8',$dok8,$line7_4);
$line7_4 = str_replace('DOK9',$dok9,$line7_4);
$line7_4 = str_replace('DOK0',$dok0,$line7_4);
$line7_4 = str_replace('DUQ1',$dok11,$line7_4);
$line7_4 = str_replace('DUQ2',$dok12,$line7_4);
}else{
$line7_4 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line81.txt")){
$line8_1 = file_get_contents("keyboard/home/line81.txt");
if($line8_1 != null ){
$line8_1 = str_replace('DOK1',$dok1,$line8_1);
$line8_1 = str_replace('DOK2',$dok2,$line8_1);
$line8_1 = str_replace('DOK3',$dok3,$line8_1);
$line8_1 = str_replace('DOK4',$dok4,$line8_1);
$line8_1 = str_replace('DOK5',$dok5,$line8_1);
$line8_1 = str_replace('DOK6',$dok6,$line8_1);
$line8_1 = str_replace('DOK7',$dok7,$line8_1);
$line8_1 = str_replace('DOK8',$dok8,$line8_1);
$line8_1 = str_replace('DOK9',$dok9,$line8_1);
$line8_1 = str_replace('DOK0',$dok0,$line8_1);
$line8_1 = str_replace('DUQ1',$dok11,$line8_1);
$line8_1 = str_replace('DUQ2',$dok12,$line8_1);
}else{
$line8_1 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line82.txt")){
$line8_2 = file_get_contents("keyboard/home/line82.txt");
if($line8_2 != null ){
$line8_2 = str_replace('DOK1',$dok1,$line8_2);
$line8_2 = str_replace('DOK2',$dok2,$line8_2);
$line8_2 = str_replace('DOK3',$dok3,$line8_2);
$line8_2 = str_replace('DOK4',$dok4,$line8_2);
$line8_2 = str_replace('DOK5',$dok5,$line8_2);
$line8_2 = str_replace('DOK6',$dok6,$line8_2);
$line8_2 = str_replace('DOK7',$dok7,$line8_2);
$line8_2 = str_replace('DOK8',$dok8,$line8_2);
$line8_2 = str_replace('DOK9',$dok9,$line8_2);
$line8_2 = str_replace('DOK0',$dok0,$line8_2);
$line8_2 = str_replace('DUQ1',$dok11,$line8_2);
$line8_2 = str_replace('DUQ2',$dok12,$line8_2);
}else{
$line8_2 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line83.txt")){
$line8_3 = file_get_contents("keyboard/home/line83.txt");
if($line8_3 != null ){
$line8_3 = str_replace('DOK1',$dok1,$line8_3);
$line8_3 = str_replace('DOK2',$dok2,$line8_3);
$line8_3 = str_replace('DOK3',$dok3,$line8_3);
$line8_3 = str_replace('DOK4',$dok4,$line8_3);
$line8_3 = str_replace('DOK5',$dok5,$line8_3);
$line8_3 = str_replace('DOK6',$dok6,$line8_3);
$line8_3 = str_replace('DOK7',$dok7,$line8_3);
$line8_3 = str_replace('DOK8',$dok8,$line8_3);
$line8_3 = str_replace('DOK9',$dok9,$line8_3);
$line8_3 = str_replace('DOK0',$dok0,$line8_3);
$line8_3 = str_replace('DUQ1',$dok11,$line8_3);
$line8_3 = str_replace('DUQ2',$dok12,$line8_3);
}else{
$line8_3 = "➕";
}}
//////////------------------------\\\\\\\\\\\\\\
if(file_exists("keyboard/home/line84.txt")){
$line8_4 = file_get_contents("keyboard/home/line84.txt");
if($line8_4 != null ){
$line8_4 = str_replace('DOK1',$dok1,$line8_4);
$line8_4 = str_replace('DOK2',$dok2,$line8_4);
$line8_4 = str_replace('DOK3',$dok3,$line8_4);
$line8_4 = str_replace('DOK4',$dok4,$line8_4);
$line8_4 = str_replace('DOK5',$dok5,$line8_4);
$line8_4 = str_replace('DOK6',$dok6,$line8_4);
$line8_4 = str_replace('DOK7',$dok7,$line8_4);
$line8_4 = str_replace('DOK8',$dok8,$line8_4);
$line8_4 = str_replace('DOK9',$dok9,$line8_4);
$line8_4 = str_replace('DOK0',$dok0,$line8_4);
$line8_4 = str_replace('DUQ1',$dok11,$line8_4);
$line8_4 = str_replace('DUQ2',$dok12,$line8_4);
}else{
$line8_4 = "➕";
}}
$Button_sete = json_encode(['inline_keyboard'=>[
[['text'=>"$line1_1",'callback_data'=>'set-line11'],
['text'=>"$line1_2",'callback_data'=>'set-line12'],
['text'=>"$line1_3",'callback_data'=>'set-line13'],
['text'=>"$line1_4",'callback_data'=>'set-line14']],
[['text'=>"$line2_1",'callback_data'=>'set-line21'],
['text'=>"$line2_2",'callback_data'=>'set-line22'],
['text'=>"$line2_3",'callback_data'=>'set-line23'],
['text'=>"$line2_4",'callback_data'=>'set-line24']],
[['text'=>"$line3_1",'callback_data'=>'set-line31'],
['text'=>"$line3_2",'callback_data'=>'set-line32'],
['text'=>"$line3_3",'callback_data'=>'set-line33'],
['text'=>"$line3_4",'callback_data'=>'set-line34']],
[['text'=>"$line4_1",'callback_data'=>'set-line41'],
['text'=>"$line4_2",'callback_data'=>'set-line42'],
['text'=>"$line4_3",'callback_data'=>'set-line43'],
['text'=>"$line4_4",'callback_data'=>'set-line44']],
[['text'=>"$line5_1",'callback_data'=>'set-line51'],
['text'=>"$line5_2",'callback_data'=>'set-line52'],
['text'=>"$line5_3",'callback_data'=>'set-line53'],
['text'=>"$line5_4",'callback_data'=>'set-line54']],
[['text'=>"$line6_1",'callback_data'=>'set-line61'],
['text'=>"$line6_2",'callback_data'=>'set-line62'],
['text'=>"$line6_3",'callback_data'=>'set-line63'],
['text'=>"$line6_4",'callback_data'=>'set-line64']],
[['text'=>"$line7_1",'callback_data'=>'set-line71'],
['text'=>"$line7_2",'callback_data'=>'set-line72'],
['text'=>"$line7_3",'callback_data'=>'set-line73'],
['text'=>"$line7_4",'callback_data'=>'set-line74']],
[['text'=>"$line8_1",'callback_data'=>'set-line81'],
['text'=>"$line8_2",'callback_data'=>'set-line82'],
['text'=>"$line8_3",'callback_data'=>'set-line83'],
['text'=>"$line8_4",'callback_data'=>'set-line84']],
]]);
Editmessagetext($chatID, $msg_id,"👈️ گزینه مورد نظر را انتخاب نمائید.",$Button_sete);
}
//////////------------------------\\\\\\\\\\\\\\//
//////////------------------------\\\\\\\\\\\\\\//
if(is_file(error_log))
unlink(error_log);
?>