<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

ob_start();
include'config.php';
include'jdf.php';
//===[امنیت دامنه]===//
$telegram_ip_ranges =[
['lower' =>'149.154.160.0','upper' =>'149.154.175.255'], // literally 149.154.160.0/20
['lower' =>'91.108.4.0','upper' =>'91.108.7.255'],    // literally 91.108.4.0/22
];

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if(!$ok){
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
exit(header("location: https://SnowyTeam.xyz"));
}
//========================== //bot // ==============================
function bot($method,$datas=[]){
$ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,'https://api.telegram.org/bot'.API_KEY.'/'.$method );
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    return json_decode(curl_exec($ch));
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
//========================== // update // ==============================
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
$message_id = $update->message->message_id;
$text = String($update->message->text);
$chat_id = $update->message->chat->id;
$tc = $update->message->chat->type;
$first_name = $update->message->from->first_name;
$from_id = $update->message->from->id;
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
}
if(isset($update->callback_query)){
$callback_query_id = $update->callback_query->id;
$data = $update->callback_query->data;
$fromid = $update->callback_query->from->id;
$update->messageid = $update->callback_query->message->message_id;
$chatid = $update->callback_query->message->chat->id;
$user = json_decode(file_get_contents("data/$fromid/$fromid.json"),true);
}
if(isset($update->channel_post)){
$channel_post_message_id = $update->channel_post->message_id;
$channel_post_chat_id = $update->channel_post->chat->id;
$channel_post_chat_username = $update->channel_post->chat->username;
$channel_post_chat_type = $update->channel_post->chat->type;
$user = json_decode(file_get_contents("channel/$channel_post_chat_id/$channel_post_chat_id.json"),true);
}
//===[امنیت]===//
if(strpos($text,'API_KEY') !== false or strpos($text,'API_KE') !== false){
exit();
}
if(strpos($text,'$apikey') !== false or strpos($text,'apikey') !== false){
exit();
}
$COIN = $user['coin'];
$STEP = $user['step'];
$INVITE = $user['member'];
$DATEJOIN = $user['datejoin'];
$userphone = $user['phone'];
if(!file_exists("data")){
mkdir("data");	
}
if(!file_exists("channel")){
mkdir("channel");	
}
if(!file_exists("data/$from_id")){
mkdir("data/$from_id");
}
function Spam($from_id){
if(file_exists("data/$from_id/spam.json")){
$spam_status = json_decode(file_get_contents("data/$from_id/spam.json"),true);
}else{
$spam_statuss = "none";
file_put_contents("data/$from_id/spam.json",json_encode($spam_statuss,true));
}
if($spam_status != null){
if(mb_strpos($spam_status[0],"time") !== false){
if(str_replace("time ",null,$spam_status[0]) >= time())
exit(false);
else
$spam_status =[1,time()+1];
}
elseif(time() < $spam_status[1]){
if($spam_status[0]+1 > 1){
$time = time() + 0;
$spam_status =["time $time"];
file_put_contents("data/$from_id/spam.json",json_encode($spam_status,true));
exit();
}else{
$spam_status =[$spam_status[0]+1,$spam_status[1]];
}
}else{
$spam_status =[1,time()+1];
}
}else{
$spam_status =[1,time()+1];
}
file_put_contents("data/$from_id/spam.json",json_encode($spam_status,true));
}
Spam($from_id);
$date = jdate("Y/m/d");
$time = jdate("H:i:s");
//==============================// function //=======================================
function curl($url){
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
return curl_exec($ch);
}
function String($string){
$string = str_replace(["`","*","_","[","]","-","+","(",")","=","^","~","&",";"],null,$string);
$persian =['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
$arabic =['٩','٨','٧','٦','٥','٤','٣','٢','١','٠'];
$num = range(0, 9);
$StringedPersianNums = str_replace($persian, $num, $string);
$englishNumbersOnly = str_replace($arabic, $num, $StringedPersianNums);
return $englishNumbersOnly;
}
function GetViews($channel, $post){
$embed = file_get_contents("https://t.me/$channel/$post?embed=true");
preg_match_all('/<span class="tgme_widget_message_views">(.*?)<\/span>/si',$embed,$match);
$views = $match[1][0];
return $views;
}
//==============================// keybord and Text //=======================================
$home = json_encode([
'keyboard'=>[
[['text'=>'❤️ سفارش لایک'],['text'=>'👁‍🗨 سفارش بازدید']],
[['text'=>'🤖 بازدید کانال'],['text'=>'🔎 پیگیری'],['text'=>'👤 حساب من']],
[['text'=>'💰 انتقال سکه'],['text'=>'➕ افزایش موجودی']],
[['text'=>'🆘 پشتیبانی و قوانین']],
],
'resize_keyboard'=>true,
]);
$back = json_encode([
'keyboard'=>[
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
]);
$bye = json_encode([
'keyboard'=>[
[['text'=>'💵 خرید سکه'],['text'=>'🛒 پیگیری خرید']],
[['text'=>'👥 زیرمجموعه'],['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
]);
$speed = json_encode([
'keyboard'=>[
[['text'=>'حداکثر سرعت']],
[['text'=>'تنظیم سرعت'],['text'=>'متوسط']],
[['text'=>'آهسته'],['text'=>'سریع']],
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
]);
$speedlike = json_encode([
'keyboard'=>[
[['text'=>'حداکثر سرعت']],
[['text'=>'هر 2 دقیقه 1 لایک'],['text'=>'هر 5 دقیقه 1 لایک']],
[['text'=>'هر 15 دقیقه 1 لایک'],['text'=>'هر 30 دقیقه 1 لایک']],
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
]);	
$start = " سلام به $botname خوش آمدید ✋

با $botname همراه شماییم تا به راحتی بازدید لایک و رای پستهای تلگرامی شما را افزایش دهیم

برای ادامه کار یک بخش را انتخاب کنید :";
$gift = "🎊 با تشکر از عضویت شما مقدار $GiftCoin سکه برای تست $botname به حسابتان افزوده شد";
//===========================// block //===========================
$block = file_get_contents("data/ban.txt");
$ban = explode("\n",$block);
if(in_array($from_id,$ban) and !in_array($from_id,$admin)){
exit();
}
//==============================// like //=======================================
if(preg_match('/^bu_(.*)/', $data) and $user['step'] =='likebutton' and $text != "🔙 بازگشت"){	
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$fromid/$fromid.json",$outjson);
bot('deletemessage',['chat_id'=> $chatid,'message_id'=> $update->messageid]);
$explode = explode('^',$user['data']);
$match = explode('_',$data,4);
$apikey = "xxxxxxxxxxx";
$result = json_decode(curl("https://AvidSeen.com/web/web.php?apikey=$apikey&type=like&count=$explode[0]&row=$match[1]&column=$match[2]&channel=$channelorder&id=$explode[2]")); 
$ok = $result->result;
if($ok =='ok'){
$amount = $explode[0] * $LikeCoin;
$nowcoin = $user['coin'] - $amount ;
$user["coin"] = "$nowcoin";
$outjson = json_encode($user,true);
file_put_contents("data/$chatid/$chatid.json",$outjson);
$speed = ($explode[1] == 1)?'حداکثر سرعت':"هر $explode[1] دقیقه 1 لایک";
$orderser = "🔎 سفارش شماره : {$result->order}
⏰ زمان : در تاریخ $date و در ساعت $time
✔️ تعداد ( رای / لایک ) درخواستی : $explode[0] ( رای / لایک )
-------------------------------------";
$myfile2 = fopen("data/$chatid/orderlike.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$orderser\n");
fclose($myfile2);
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=>"☑️ سفارش لایک (رأی) با شماره پیگیری {$result->order} ثبت شد

🚀 سرعت تکمیل : $speed
❤️ تعداد درخواست لایک : $explode[0]
👍🏻 گزینه انتخابی برای دریافت لایک رای : $match[3]",
'reply_markup'=>$home
]);
bot('sendMessage',[
'chat_id'=>$chatid,
'message_id'=>$message_id + 1,
'text'=>"✅ سفارش لایک (رأی) با شماره پیگیری {$result->order} تکمیل شد

🚀 سرعت تکمیل : $speed
❤️ تعداد درخواست لایک : $explode[0]
👍🏻 گزینه انتخابی برای دریافت لایک رای : $match[3]",

'reply_markup'=>$home
]);
$user = json_decode(file_get_contents("data/$chatid/$chatid.json"),true);
$coin20 = $user['coin'] + 0;
bot('sendmessage',[
'chat_id'=>"@$channelorder",   
'text' => "❤️ سفارش لایک جدید
                             
• کد پیگیری سفارش : {$result->order} 
• سرعت تکمیل : $speed
ایدی عددی فرد :$chatid
• هزینه سفارش :$amount سکه
• تعداد درخواست لایک  : $explode[0]
• گزینه انتخابی : $match[3]
• تعداد سکه کاربر : $coin20
",
]);
}else{
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=>"⚠️ مشکل در ثبت سفارش لایک / رای به وجود آماده است",
'reply_markup'=>$back
]);	
$user["step"] = "likebutton";
$outjson = json_encode($user,true);
file_put_contents("data/$fromid/$fromid.json",$outjson);
} 
}
//===========================// start //===========================
elseif(preg_match('/^(\/start) (.*)/',$text , $prameter) and !file_exists("data/$chat_id/$chat_id.json")){
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$chat_id\n");
fclose($myfile2);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$user["coin"] = "$GiftCoin";
$user["member"] = "0";
$user["buy"] = "0";
$user["star"] = "⭐️";
$user["inviter"] = "$prameter[2]";
$user["phone"] = "0";
$user["data"] = "0";
$user["datejoin"] = "$date";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
file_put_contents("data/$from_id.txt","$prameter[2]");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"$start",  
'reply_markup'=>$home
]);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>$gift,
]);
exit();
}
if(preg_match('/^\/([Ss]tart)(.*)/',$text,$match)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"$start",  
'reply_markup'=>$home
]);
if(!file_exists("data/$chat_id/$chat_id.json")){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>$gift,
]);
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$chat_id\n");
fclose($myfile2);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$user["coin"] = "$GiftCoin";
$user["member"] = "0";
$user["buy"] = "0";
$user["star"] = "⭐️";
$user["inviter"] = "none";
$user["phone"] = "0";
$user["data"] = "0";
$user["datejoin"] = "$date";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
exit();
}
if(preg_match('/^\/([Dd][Ee][Ll])(.*)/',$text,$match)){
$title =bot('getchat',['chat_id'=>-$match[2]])->result->title;
if($title == null){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ این کانال وجود ندارد",
'reply_markup'=>$home,
]);
exit();
}
if(file_exists("channel/-$match[2]/-$match[2].json")){
$nice = json_decode(file_get_contents("channel/-$match[2]/-$match[2].json"),true);
$id = $nice["id"];
if($id == $chat_id){
$result = file_get_contents("data/$from_id/orderchannel.txt");
$result = str_replace("❌ برای حذف دستور رو به رو را ارسال کنید : /del$match[2]","❌ این کانال حذف شده است از لیست بازدید های خودکار",$result);
file_put_contents("data/$from_id/orderchannel.txt","$result");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ با موفقیت کانال $title از لیست کانال های بازدید خودکار حذف شد",
]);
}else{
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ با موفقیت کانال $title از لیست کانال های بازدید خودکار حذف شد",
]);
bot('sendmessage',[
'chat_id'=>$id,
'text'=>"⚠️ شخص دیگری کانال $title را از لیست کانال های بازدید خودکار حذف کرد ! ",
]);
}
DeleteFolder("channel/-$match[2]");
unlink("channel/-$match[2]");
rmdir("channel/-$match[2]");
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ کانال $title در لیست بازدید های خودکار وجود ندارد",
'reply_markup'=>$home,
]);	
}
//===========================// join //===========================
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel&user_id=".$from_id));
$joqw = $joincha->result->status;
if($joqw !='member' and $joqw !='creator' and $joqw !='administrator' and $tc =='private'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ کاربر گرامی برای اطلاع از آخرین بروزرسانی ها اطلاعیه ها و قوانین نیاز است در کانال رسمی $botname به نشانی زیر عضو شوید :

@$channel",

'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"❄️ $botname",'url'=>"https://t.me/$channel"]],
]
])
]);
exit();
if(!file_exists("data/$chat_id/$chat_id.json")){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>$gift,
]);
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$chat_id\n");
fclose($myfile2);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$user["coin"] = "$GiftCoin";
$user["member"] = "0";
$user["buy"] = "0";
$user["star"] = "⭐️";
$user["inviter"] = "none";
$user["phone"] = "0";
$user["data"] = "0";
$user["datejoin"] = "$date";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
//===========================// invite //===========================
if(!file_exists("data/$chat_id/$chat_id.json")){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>$gift,
]);
$myfile2 = fopen("data/datas.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$chat_id\n");
fclose($myfile2);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$user["coin"] = "$GiftCoin";
$user["member"] = "0";
$user["buy"] = "0";
$user["star"] = "⭐️";
$user["inviter"] = "none";
$user["phone"] = "0";
$user["data"] = "0";
$user["datejoin"] = "$date";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
$invid = file_get_contents("data/$from_id.txt");
if($invid == true){
unlink("data/$from_id.txt");
$zir = json_decode(file_get_contents("data/$invid/$invid.json"),true);
$member = $zir['member'] + 1;
$coin = $zir['coin'] + $ZirCoin;
$zir["coin"] = "$coin";
$zir["member"] = "$member";
$outjson = json_encode($zir,true);
file_put_contents("data/$invid/$invid.json",$outjson);
bot('sendmessage',[
'chat_id'=>$invid,
'text'=>"
🎉 تبریک $ZirCoin سکه بابت زیرمجموعه جدید به حسابتان افزوده شد

🎉 Congratulations , $ZirCoin coins have been added to your account for the new subscription
",
'parse_mode'=>'Markdown',
]);
}
//===========================// back //===========================
elseif($text =='🔙 بازگشت'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔙 به منوی اصلی برگشتید",

'reply_markup'=>$home
]);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
exit();
}
//===========================// coin step //===========================
elseif($user['step'] =='seenpost' and $text != "🔙 بازگشت"){
if($update->message->audio != true){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
$apikey = "xxxxxxxxxxx";
if($update->message->forward_from_chat == true){
$id = bot('ForwardMessage',[
'chat_id'=>"@$channelorder",   
'from_chat_id'=>$from_id,
'message_id'=>$message_id,
])->result->message_id;
}else{
$exlink = explode('/', $text);
$channelorder = $exlink[count($exlink) - 2];
$id = end($exlink);
} 
$explode = explode('^',$user['data']);
$seen = GetViews($channelorder, $id);
$result = json_decode(curl("https://AvidSeen.com/web/web.php?apikey=$apikey&type=view&count=$explode[0]&speed=$explode[2]&period=$explode[3]&channel=$channelorder&id=$id")); 
$seenpaean = $result->seenend;
$seenstart = $result->seenstart;
$ok = $result->result;
if($ok =='ok'){
$amount = $explode[0] * $SeenCoin;
$nowcoin = $user['coin'] - $amount ;
$user["coin"] = "$nowcoin";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
$startorder = ($explode[1] == 0)?'فوری':"بعد از $explode[1] دقیقه";
$speed = ($explode[2] == 0)?'حداکثر سرعت':"هر $explode[3] دقیقه $explode[2] بازدید";
$orderser = "🔎 سفارش شماره : {$result->order}
⏰ زمان : در تاریخ $date و در ساعت $time
✔️ تعداد بازدید درخواستی : $explode[0] بازدید
-------------------------------------";
$myfile2 = fopen("data/$from_id/orderseen.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$orderser\n");
fclose($myfile2);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"☑️ سفارش بازدید به شماره {$result->order} با موفقیت ثبت شد
• تعداد درخواست : $explode[0] بازدید
• سرعت انجام سفارش : $speed
• سرعت استارت : $startorder
• شروع از : $seenstart بازدید
",
'reply_markup'=>$home
]);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ سفارش بازدید به شماره {$result->order} با موفقیت تکمیل شد

• تعداد درخواست : $explode[0] بازدید
• سرعت انجام سفارش : $speed
• استارت سفارش : $startorder
• شروع از :$seenstart بازدید
• تکمیل در : $seenpaean بازدید
",
]);
$user = json_decode(file_get_contents("data/$chat_id/$chat_id.json"),true);
$coin1 = $user['coin'] + $explode[0];
$coin2 = $user['coin'] + 0;
bot('sendmessage',[
'chat_id'=>"@$channelorder",   
'text' => "📣 سفارش بازدید جدید       
• کد پیگیری سفارش : {$result->order} 
شناسه عددی کاربر : $from_id
• سرعت تکمیل : $speed
• هزینه سفارش : $amount سکه
• تعداد درخواست : $explode[0] بازدید
• تعداد سکه کاربر : $coin2
• تعداد سکه قبلی کاربر : $coin1
",
]);
}else	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ خطا مشکلی در تکمیل سین به وجود آماده است",
'reply_markup'=>$home
]);
}else	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ جهت افزایش سین موزیک باید لینک موزیک رو بصورت صحیح ارسال کنید",
'reply_markup'=>$home
]);
}
//========================== // order // ==============================
elseif($text =='👁‍🗨 سفارش بازدید'){
if($user['coin'] >= 50){
$max = floor($user['coin'] / $SeenCoin);
$bazdid = $SeenCoin * 1000;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
👁‍🗨 تعداد بازدید دلخواه خود را بین 1 الی 150,000 (150K) ارسال کنید
موجودی شما : `$COIN` سکه

💡تمامی سفارشها تا سقف 100000 بازدید با توجه به سرعت انتخابی شما تکمیل خواهند شد و پس از آن بدون در نظر گرفتن سرعت تکمیل میشوند
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$back,
]);          
$user["step"] = "seenamount";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ جهت سفارش بازدید حداقل باید شما 50 سکه در حساب خود داشته باشید
",
    
]);	
}
elseif($text =='❤️ سفارش لایک'){
if($user['coin'] >= 100){
$max = floor($user['coin'] / $LikeCoin);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❤️ تعداد لایک / رای دلخواه خود را بین 1 الی 100000 ارسال کنید
موجودی شما : $COIN سکه

* هزینه هر لایک معادل $LikeCoin سکه است
* پست لایک و رایی که نیاز به جوین شدن در کانال داشته باشد توسط ربات قابل انجام نیست و در صورت سفارش برای این موارد سکه مصرفی هدر خواهد رفت",

'reply_markup'=>$back,
]);	
$user["step"] = "likeamount";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ جهت سفارش لایک حداقل باید در حساب شما 100 سکه بماند
",
    
]);	
}
//========================== // auto // ==============================
elseif($text =='🤖 بازدید کانال'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🔰 در اینجا میتوانید بازدید خودکار برای کانالتان ثبت کنید.
👈 چطور کار میکند؟ با ثبت بازدید خودکار برای کانال، به محض انتشار پست جدید، فرایند افزایش بازدید آن توسط $botname شروع خواهد شد و بدون نیازی به هیچ کار خاصی بازدید آن پست افزایش پیدا خواهد کرد!

برای شروع یکی از موارد زیر را انتخاب کنید:",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>'🎯 ثبت سفارش بازدید خودکار'],['text'=>'📊 پیگیری بازدید خودکار']],
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
]),
]);	
}
elseif($text =='📊 پیگیری بازدید خودکار'){
$result = file_get_contents("data/$from_id/orderchannel.txt");
if($result != null){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🚀 کانال های ثبت شده :

$result	", 
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❗️شما تاکنون کانالی جهت بازدید خودکار ثبت نکرده اید.

♻️جهت ثبت بازدید خودکار برای کانال های خود ابتدا وارد قسمت`🎯 بازدید کانال` شوید.

♻️بر روی گزینه `🎯 ثبت سفارش بازدید خودکار` کلیک کنید.

♻️و در اخر مراحل ثبت `🚀بازدید خودکار` را طی کنید.',
'parse_mode'=>'Markdown', 
]);	
}
elseif($text =='🎯 ثبت سفارش بازدید خودکار'){
if($user['coin'] >= 1000){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🔰 برای شروع ثبت تبلیغ لطفا ربات @$usernamebot را در کانالتان ادمین کنید
چرا باید ادمین کنید ؟ طبق قوانین تلگرام ربات ها تنها میتوانند در صورتی پست های کانال را مشاهده کنند که در کانال ادمین باشند
 
🔰 چه دسترسی هایی لازم است ؟
تقریبا دسترسی خاصی لازم نیست شما میتوانید به دلخواه فقط یک دسترسی (برای مثال افزودن عضو به کانال شما) را باز بگذراید
     
🔰 چگونه ربات را ادمین کنم ؟
1 - وارد کانالتان شوید
2 - روی نام کانال (نوار بالا) ضربه بزنید
3 - روی گزینه Administrators (مدیران) کلیک کنید
4 - روی گزینه Add Administrator (افزودن مدیر) کلیک کنید
5 - روی آیکون ذره بین (Search) ضربه بزنید و در قسمت جستجو آی دی ربات را جستجو کنید: @$usernamebot
6 - یک دسترسی را به دلخواه باز بگذارید (تنها یک دسترسی کافیست)
7 - در پایان یک پیام از کانال به ربات فوروارد کنید

👌 حالا ربات @$usernamebot را در کانالتان ادمین کنید پس از آن یک پیام از کانال برای من فروارد کنید
",
'parse_mode'=>'HTML',
   
'reply_markup'=>$back,
]);	
$user["step"] = "setchannel";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ جهت ثبت کانال جهت بازدید خودکار به 1000 سکه نیاز دارید",
]);	
}
//========================== // order list // ==============================
elseif($text =='➕ افزایش موجودی'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
💰 یک بخش را جهت افزایش موجودی انتخاب کنید :
",
'reply_markup'=>$bye
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
//========================== // charge amount // ==============================
elseif($text =='💵 خرید سکه'){
if($userphone !== "0"){
$allbuy = $user["buy"];
bot('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"
❓تعداد #سکه موردنظر خود را وارد نمایید ( هر 1000 سکه 800 تومان )

مجموع خرید شما : $allbuy تومان
",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' =>[ 
[['text'=>'🔙 بازگشت']],
]])
]); 
$user["step"] = "payAmon";
$outjson = json_encode($user,true);
file_put_contents("data/$from_id/$from_id.json",$outjson);
}else{
bot('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"🔐 لطفاً ابتدا تایید هویت کنید اطلاعات شما توسط ما محفوظ هستند ",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' =>[ 
[['text'=>"✔️ ارسال شماره",'request_contact'=>true]],
[['text'=>'🔙 بازگشت']],
]])
]); 
$user["step"] = "verphone";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
}
//==============================================
elseif($user['step'] == "verphone" and isset($update->message->contact)){
if($update->message->contact->user_id == $chat_id){
$phone =$update->message->contact->phone_number;
if(strpos($phone,'98') === 0 || strpos($phone,'+98') === 0){
$phone ='0'.strrev(substr(strrev($phone),0,10));
$user["phone"] = "$phone";
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ شماره تلفن شما با موفقیت در $botname ثبت و تایید شد",
'reply_markup'=>$home
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$admin[0], 
'text'=>"♻️ یک شماره جدید در سیستم ثبت شد
+ ثبت نام کننده : $chat_id
+ شماره تلفن شما : $phone
+ زمان ثبت : $date در ساعت $time",
'reply_markup'=>$home
]);
}else{
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ خرید سکه فقط برای کاربران ایرانی قابل انجام است",
'reply_markup'=>$home
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
}else{
bot('sendmessage',[ 
'chat_id'=>$chat_id, 
'text'=>"🔐 لطفاً ابتدا تایید هویت کنید اطلاعات شما توسط ما محفوظ هستند ",
'reply_markup' => json_encode([ 
'resize_keyboard'=>true, 
'keyboard' =>[ 
[['text'=>"✔️ ارسال شماره",'request_contact'=>true]],
[['text'=>'🔙 بازگشت']],
]])
]); 
}
}
elseif($user['step'] =='payAmon' and $text != "🔙 بازگشت"){
if(is_numeric($text) and $text >= 1000 and $text <= 500000){
$payed = $text/1.25;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
`فاکتور خرید $text سکه به مبلغ $payed تومان برای شما صادر گردید

فاکتور فوق برای شماره همراه $userphone صادر شده است , در صورت تایید دکمه پرداخت را لمس نمایید

$date - $time`
",
'parse_mode'=>'Markdown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"پرداخت",'url'=>"t.me/pay?amount=$payed&id=$from_id"]],
]
])             
]);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"🔙 به منوی اصلی برگشتید",
'reply_markup'=>$home
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'
⚠️ حداقل خرید سکه 1000 و حداکثر خرید سکه 500000 می‌باشد
',
]);	
}
elseif($text =='👥 زیرمجموعه'){
$id =bot('sendphoto',[
'chat_id'=>$chat_id,
'photo'=>$baner,
'caption'=>"✅ ربات $botname :

👁‍🗨 افزایش تخصصی بازدید پستهای تلگرامی
❤️ افزایش لایک پستهای تلگرامی
📈 افزایش رای در نظرسنجی ها
📟 زمانبندی تکمیل سفارشها به دلخواه
👥 زیرمجموعه گیری و دریافت پورسانت
🔐 پرداخت مطمئن و امن

🔗 https://t.me/$usernamebot?start=$from_id"])->result->message_id;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🎉 با ارسال بنر بالا به مخاطبین و دوستان خود به ازای هر نفری که برای اولین بار با لینک شما عضو $botname شود $ZirCoin سکه دریافت خواهید کرد",
'reply_to_message_id'=>$id,
]);
}
//========================== // key // ==============================
elseif($text =='👤 حساب من'){
$edit =bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
_درحال بارگزاری . . ._
",
'parse_mode'=>'Markdown', 
])->result->message_id;
bot('editmessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$edit,
'text'=>"
👤 شناسه کاربری : `$from_id`
👈 تاریخ عضویت : $DATEJOIN
👈 تعداد زیرمجموعه : $INVITE نفر

💳 مجموع خرید شما : `{$user["buy"]}` تومان
🌟 سطح کاربری : {$user["star"]}
💰 موجودی : `$COIN`

`⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است`
",
'parse_mode'=>'Markdown', 
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
}	
elseif($text =='🆘 پشتیبانی و قوانین'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🔰 تیم پشتیبانی $botname با افتخار آماده پاسخگویی به شما عزیزان است :
$usernamesup

⚖️ کاربر گرامی چنانچه شما از ربات $botname استفاده نمایید به منزله قبول قوانین زیر است :

👈 تنها مرجع رسمی افزایش موجودی ربات بخش'➕ افزایش موجودی' است
👈 خرید و فروش موجودی ربات توسط کاربران ممنوعیتی ندارد اما $botname هیچ گونه تعهدی در این رابطه ندارد
👈 درصورتی که تراکنش مشکوکی مشاهده شود $botname این اختیار را دارد که از کاربر مربوطه مدارک موردنیاز را درخواست کند
👈 در صورت استفاده نادرست از بخش های زیرمجموعه گیری و ارسال اکانت حساب شخص مسدود خواهد شد",
]);	
}	
elseif($text =='🛒 پیگیری خرید'){
$result = file_get_contents("data/$from_id/buy.txt");
if($result == true){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🛍 لیست ده خرید اخیر شما , این لیست تنها جهت اطلاع شماست و کاردبرد دیگری ندارد

$result",
]);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❌ شما تا کنون خریدی از ربات نداشته اید', 
]);
}
//===========================// data //===========================
elseif($data =='badbutton'){
bot('answercallbackquery',[
'callback_query_id' =>$callback_query_id,
'text' =>'❌ این دکمه جهت افزایش لایک و رای قابل انتخاب نمی‌باشد',
'show_alert' =>true
]);
}
elseif($text =='🔎 پیگیری'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"👈 با استفاده از دکمه های نمایش داده شده بخش موردنظر را جهت پیگیری انتخاب کنید",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>'❤️ پیگیری لایک'],['text'=>'👁‍🗨 پیگیری بازدید']],
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
])
]);	
}
elseif($text =='👁‍🗨 پیگیری بازدید'){
$result = file_get_contents("data/$from_id/orderseen.txt");
if($result == true){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔎 لیست اخرین سفارشهای بازدید شما :

$result",
    
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ سفارشی جهت نمایش وجود ندارد',
    
]);	
}
elseif($text =='❤️ پیگیری لایک'){
$result = file_get_contents("data/$from_id/orderlike.txt");
if($result == true){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔎 لیست اخرین سفارشهای لایک شما :

$result",
    
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ سفارشی جهت نمایش وجود ندارد', 
]);
}
//===========================// step //===========================
elseif($user['step'] =='seenamount' and $text != "🔙 بازگشت"){
$max = floor($user['coin'] / $SeenCoin);
if(is_numeric($text) and $text >= 1 and $text <= 150000){
if($user['coin'] >= $text * $SeenCoin){	
$limit = ($text > 100000)?'💡تمامی سفارشها تا سقف 100000 بازدید با توجه به سرعت انتخابی شما تکمیل خواهند شد و پس از آن بدون در نظر گرفتن سرعت تکمیل میشوند':null;  
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⏱ سرعت انجام سفارش را با استفاده از دکمه های نمایش داده شده انتخاب نمایید

$limit",
'reply_markup'=>$speed
]);	
$user["step"] = "seenspeed";
$user["data"] = "$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ تعداد بازدید درخواست شده بیشتر از موجودی شما است ",

'reply_markup'=>$back
]);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ لطفاً تعداد بازدید را بین 1 تا 150,000 وارد کنید",

'reply_markup'=>$back
]);
}
elseif($user['step'] =='seenspeed' and $text != "🔙 بازگشت"){
$speedpanel =['حداکثر سرعت','تنظیم سرعت','سریع','متوسط','آهسته'];
if(in_array($text , $speedpanel)){
if($text =='تنظیم سرعت'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⏱ افزایش بازدید بعد از چند دقیقه شروع شود
❗️ مقدار مجاز را به صورت عدد بین  1 الی 100 دقیقه وارد کنید 👇🏻',
'reply_markup'=>$back
]);	
$user["step"] = "configspeed";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else{
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✔️ پست مورد نظر خود را فوروارد کنید به ربات ( میتوانید لینک پست مورد نظر هم ارسال کنید در صورت نیاز شدن )

بازدید درخواستی : {$user['data']}
شروع سفارش : فوری
سرعت تکمیل : $text
",
'reply_markup'=>$back
]);
$keys =[$speedpanel[0]=>'0^10^0',$speedpanel[2]=>'0^10^1',$speedpanel[3]=>'0^10^2',$speedpanel[4]=>'0^10^4'];
$user["step"] = "seenpost";
$user["data"] = "{$user['data']}^{$keys[$text]}";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
}
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ سرعت انجام سفارش را از گزینه های زیر استفاده کنید',
'reply_markup'=>$speed
]);
}
elseif($user['step'] =='configspeed' and $text != "🔙 بازگشت"){
if(is_numeric($text) and $text >= 1 and $text <= 100){
$user["step"] = "configspeedtime";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
⏱ سرعت بازدید در هر دقیقه را وارد کنید
❗️مقدار مجاز با به صورت عدد بین 1 الی 10 بازدید در دقیقه وارد کنید 👇🏻
",
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ خطا , درخواست شما اشتباه است 


⏱ ارسال بازدید هر چند دقیقه یک بار انجام میشود

👈 مقدار مجاز را بصورت عدد بین 1 الی 100 دقیقه را وارد کنید
',
'reply_markup'=>$back
]);
}
elseif($user['step'] =='configspeedtime' and $text != "🔙 بازگشت"){
if(is_numeric($text) and $text >= 1 and $text <= 10){
$user["step"] = "configspeedseen";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
⏱ ارسال بازدید هر چند دقیقه یک بار انجام شود
❗️ مقدار مجاز را به صورت عدد بین 1 الی 100 دقیقه وارد کنید 👇🏻
",
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'
⚠️ خطا , درخواست شما اشتباه است 


⏱ ارسال بازدید هر چند دقیقه یک بار انجام میشود

👈 مقدار مجاز را بصورت عدد بین 1 الی 10 سرعت بازدید را در دقیقه ارسال کنید
',
'reply_markup'=>$back
]);
}
elseif($user['step'] =='configspeedseen' and $text != "🔙 بازگشت"){
$explode = explode('^',$user['data']);
if(is_numeric($text) and $text >= 1 and $text <= 100){
$user["step"] = "seenpost";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✔️ پست مورد نظر خود را فوروارد کنید به ربات ( میتوانید لینک پست مورد نظر هم ارسال کنید در صورت نیاز شدن )

بازدید درخواستی : $explode[0]
شروع سفارش : بعد از $explode[1] دقیقه
سرعت تکمیل : هر $text دقیقه $explode[2] بازدید
",
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❗️ خطا ، درخواست شما دارای ورودی نادرست است

❗️در هر چند دقیقه $explode[2] بازدید انجام شود
",
'reply_markup'=>$back
]);
}
elseif($user['step'] =='likeamount' and $text != "🔙 بازگشت"){
$max = floor($user['coin'] / $LikeCoin);
if(is_numeric($text) and $text >= 1 and $text <= 100000){
if($user['coin'] >= $text * $LikeCoin){	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⏱ سرعت انجام سفارش را با استفاده از دکمه های نمایش داده شده انتخاب نمایید..',
'reply_markup'=>$speedlike
]);	
$user["step"] = "likespeed";
$user["data"] = "$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ تعداد لایک / رای درخواست شده بیشتر از موجودی شما است ",

'reply_markup'=>$back
]);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ لطفاً تعداد لایک را بین 1 تا 100,000 وارد کنید",

'reply_markup'=>$back
]);
}
elseif($user['step'] =='likespeed' and $text != "🔙 بازگشت"){
$speedpanel =['حداکثر سرعت','هر 2 دقیقه 1 لایک','هر 5 دقیقه 1 لایک','هر 15 دقیقه 1 لایک','هر 30 دقیقه 1 لایک'];
if(in_array($text , $speedpanel)){
$amount = $user['data'] * $LikeCoin;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"👈🏻 پست مورد نظر خود را جهت افزایش لایک از یک کانال عمومی فروارد کنید.

❤️ تعداد لایک درخواستی : {$user['data']}

👈🏻 سرعت تکمیل سفارش : $text

👈🏻 مقدار سکه کسر شده : $amount",
'reply_markup'=>$back
]);
$keys =[$speedpanel[0]=>'1',$speedpanel[1]=>'2',$speedpanel[2]=>'5',$speedpanel[3]=>'15',$speedpanel[4]=>'30'];
$user["step"] = "likebutton";
$user["data"] = "{$user['data']}^{$keys[$text]}";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ سرعت انجام سفارش را از گزینه های زیر استفاده کنید',
'reply_markup'=>$speedlike
]);
}
elseif($user['step'] =='likebutton' and $text != "🔙 بازگشت"){
if($update->message->poll == true){ 
if($update->message->poll->is_closed == false){
$i = 0;
foreach ($update->message->poll->options as $k => $v){
$substrtext = (mb_strlen($v->text) <= 27) ? $v->text:mb_substr($v->text,0,27).'...'; 
$key[] =[['text'=>$v->text,'callback_data'=>"bu_{$i}_0_{$substrtext}"]];
$i ++;
}
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'👇🏻گزینه مورد جهت افزایش لایک / رای را انتخاب نمایید',
'reply_markup'=>json_encode([
'inline_keyboard'=>$key
])
]);
$id =bot('ForwardMessage',[
'chat_id'=>"@$channelorder",   
'from_chat_id'=>$from_id,
'message_id'=>$message_id,
])->result->message_id;
$user["data"] = "{$user['data']}^$id";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
} else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❗️لایک / رای پست ارسال شده به پایان رسیده است',
    
'reply_markup'=>$back
]);
}
}
elseif($user['step'] =='setchannel' and $text != "🔙 بازگشت"){
$forward_from_chat_id = $update->message->forward_from_chat->id;
if($forward_from_chat_id == true){
$getChatMember =bot('getChatMember',['chat_id'=>$forward_from_chat_id,'user_id'=>$botid])->result;
$getchat =bot('getchat',['chat_id'=>$forward_from_chat_id])->result;
if($getchat->type =='channel' and $getChatMember->status !='left'){
if(file_exists("channel/$forward_from_chat_id/$forward_from_chat_id.json")){
$nice = json_decode(file_get_contents("channel/$forward_from_chat_id/$forward_from_chat_id.json"),true);
$id = $nice['id'];
}else{
$id = "0";
}
if($id != $chat_id){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
👁‍🗨 تعداد بازدید دلخواه خود را بین 1 الی 150,000 (150K) ارسال کنید
موجودی شما : `$COIN` سکه

💡تمامی سفارشها تا سقف 100000 بازدید با توجه به سرعت انتخابی شما تکمیل خواهند شد و پس از آن بدون در نظر گرفتن سرعت تکمیل میشوند
",
'parse_mode'=>'Markdown',
'reply_markup'=>$back
]);	
$user["step"] = "amountchannel";
$user["data"] = "$forward_from_chat_id";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
❌قبلا برای این کانال بازدید خودکار ثبت شده است

✅ برای ثبت مجدد بازدید خود برای این کانال از گزینه `📊 پیگیری بازدید خودکار` استفاده نمایید.",
'parse_mode'=>'Markdown',
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>'📊 پیگیری بازدید خودکار']],
[['text'=>'🔙 بازگشت']],
],
'resize_keyboard'=>true,
])
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ ابتدا ربات @$usernamebot را ادمین کانال خود کنید",
'parse_mode'=>'HTML',

'reply_markup'=>$back
]);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ لطفا یک پست از کانال فوروارد کنید',

'reply_markup'=>$back
]);
}
elseif($user['step'] =='amountchannel' and $text != "🔙 بازگشت"){
$max = floor($user['coin'] / $SeenCoin);
if(is_numeric($text) and $text >= 1 and $text <= 150000){
if($user['coin'] >= $text * $SeenCoin){	
$limit = ($text > 100000)?'💡 تمامی سفارشها تا سقف 100,000 بازدید با توجه به سرعت انتخابی شما تکمیل خواهند شد و پس از آن بدون در نظر گرفتن سرعت تکمیل میشوند':null;  
$user["step"] = "seenspeedch";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
⏱ سرعت انجام سفارش را با استفاده از دکمه های نمایش داده شده انتخاب نمایید

$limit",
'reply_markup'=>$speed
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ تعداد بازدید درخواست شده بیشتر از موجودی شما است ",

'reply_markup'=>$back
]);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ لطفاً تعداد بازدید را بین 1 تا 150,000 وارد کنید",

'reply_markup'=>$back
]);
}
elseif($user['step'] =='seenspeedch' and $text != "🔙 بازگشت"){
$speedpanel =['حداکثر سرعت','تنظیم سرعت','سریع','متوسط','آهسته'];
if(in_array($text , $speedpanel)){
if($text =='تنظیم سرعت'){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⏱ افزایش بازدید بعد از چند دقیقه شروع شود
❗️ مقدار مجاز را به صورت عدد بین  1 الی 100 دقیقه وارد کنید 👇🏻',
'reply_markup'=>$back
]);	
$user["step"] = "configspeedch";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else{
$explode = explode('^',$user['data']);
$title =bot('getchat',['chat_id'=>"$explode[0]"])->result->title;
$amount = $explode[1] * $SeenCoin;
$orderser = "📢 نام کانال : $title
👈🏻 تعداد بازدید درخواستی : $explode[1]
👈🏻 سرعت تکمیل : $text
❌ برای حذف دستور رو به رو را ارسال کنید : /del".abs($explode[0])."
-------------------------------------";
$myfile2 = fopen("data/$from_id/orderchannel.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$orderser\n");
fclose($myfile2);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🚀 سفارش بازدید خودکار با موفقیت ثبت شد

📢 نام کانال : $title
👈🏻 تعداد بازدید درخواستی : $explode[1]
👈🏻 سرعت تکمیل : $text
👈🏻 برای حذف دستور رو به رو را ارسال کنید : /del".abs($explode[0])."",
'reply_markup'=>$home
]);
bot('sendmessage',[
'chat_id'=>"@$channelorder",
'text' =>"📡 سفارش بازدید کانال

• نام کانال : $title
• آیدی عددی کانال : ".abs($explode[0])."
• کاربر سفارش دهنده : $from_id
• هزینه سفارش هر پست : $amount سکه
• تعداد بازدید درخواستی برای هر پست :$explode[1]",
]);	
$keys =[$speedpanel[0]=>'0^10^0',$speedpanel[2]=>'0^10^2',$speedpanel[3]=>'0^10^3',$speedpanel[4]=>'0^10^5'];
if(!file_exists("channel/$explode[0]")){
mkdir("channel/$explode[0]");
}
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
$user = json_decode(file_get_contents("channel/$explode[0]/$explode[0].json"),true);
$user["id"] = "$chat_id";
$user["speed"] = "$keys[$text]";
$user["view"] = "$explode[1]";
$outjson = json_encode($user,true);
file_put_contents("channel/$explode[0]/$explode[0].json",$outjson);
}
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ سرعت انجام سفارش را از گزینه های زیر استفاده کنید',
'reply_markup'=>$speed
]);
}
elseif($user['step'] =='configspeedch' and $text != "🔙 بازگشت"){
if(is_numeric($text) and $text >= 1 and $text <= 100){
$user["step"] = "configspeedtimech";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
⏱ سرعت بازدید در هر دقیقه را وارد کنید
❗️مقدار مجاز با به صورت عدد بین 1 الی 10 بازدید در دقیقه وارد کنید 👇🏻
",
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ خطا , درخواست شما اشتباه است 


⏱ ارسال بازدید هر چند دقیقه یک بار انجام میشود

👈 مقدار مجاز را بصورت عدد بین 1 الی 100 دقیقه را وارد کنید
',
'reply_markup'=>$back
]);
}
elseif($user['step'] =='configspeedtimech' and $text != "🔙 بازگشت"){
if(is_numeric($text) and $text >= 1 and $text <= 10){
$user["step"] = "configspeedseench";
$user["data"] = "{$user['data']}^$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
⏱ ارسال بازدید هر چند دقیقه یک بار انجام شود
❗️ مقدار مجاز را به صورت عدد بین 1 الی 100 دقیقه وارد کنید 👇🏻
",
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'
⚠️ خطا , درخواست شما اشتباه است 


⏱ ارسال بازدید هر چند دقیقه یک بار انجام میشود

👈 مقدار مجاز را بصورت عدد بین 1 الی 10 سرعت بازدید را در دقیقه ارسال کنید
',
'reply_markup'=>$back
]);
}
elseif($user['step'] =='configspeedseench' and $text != "🔙 بازگشت"){
$explode = explode('^',$user['data']);
if(is_numeric($text) and $text >= 1 and $text <= 100){
$start = ($explode[2] == 0)?'فوری':"بعد از $explode[2] دقیقه";
$speed = ($explode[3] == 0)?'حداکثر سرعت':"هر $text دقیقه $explode[3] بازدید";
$title =bot('getchat',['chat_id'=>$explode[0]])->result->title;
$amount = $explode[1] * $SeenCoin;
$orderser = "📢 نام کانال : $title
👈🏻 تعداد بازدید درخواستی : $explode[1]
👈🏻 سرعت تکمیل : $speed
❌ برای حذف دستور رو به رو را ارسال کنید : /del".abs($explode[0])."
-------------------------------------";
$myfile2 = fopen("data/$from_id/orderchannel.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$orderser\n");
fclose($myfile2);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🚀 سفارش بازدید خودکار با موفقیت ثبت شد

📢 نام کانال : $title
👈🏻 تعداد بازدید درخواستی : $explode[1]
👈🏻 سرعت تکمیل : $speed
👈🏻 برای حذف دستور رو به رو را ارسال کنید : /del".abs($explode[0])."",
'reply_markup'=>$home
]);	
bot('sendmessage',[
'chat_id'=>"@$channelorder",
'text' =>"📡 سفارش بازدید کانال
• نام کانال : $title
• آیدی عددی کانال : ".abs($explode[0])."
• کاربر سفارش دهنده : $from_id
• هزینه سفارش هر پست : $amount سکه
• تعداد بازدید درخواستی برای هر پست :$explode[1]
",
]);	
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
$user = json_decode(file_get_contents("channel/$explode[0]/$explode[0].json"),true);
$user["id"] = "$chat_id";
$user["speed"] = "{$explode[2]}^{$explode[3]}^$text";
$user["view"] = "$explode[1]";
$outjson = json_encode($user,true);
file_put_contents("channel/$explode[0]/$explode[0].json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❗️ خطا ، درخواست شما دارای ورودی نادرست است

❗️در هر چند دقیقه $explode[2] بازدید انجام شود
",
'reply_markup'=>$back
]);
}
elseif($text =='💰 انتقال سکه'){
if($user['coin'] > 300){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"💸 در این بخش میتوانید سکه های خودتان را به سایر کابران $botname انتقال دهید
موجودی شما : $COIN سکه

تعداد سکه موردنظر برای انتقال را وارد کنید :
",
'reply_markup'=>$back,
]);
$user["step"] = "sendid";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❌ جهت انتقال باید حداقل 300 سکه در حساب خودتان بماند',
'reply_markup'=>$home
]);
}
elseif($user['step'] =='sendid' and $text != "🔙 بازگشت"){
$text = str_replace(["`","*","_","[","]","-","+"],["","","","",""],$text);
if(is_numeric($text)){
if($text < 50){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ حداقل مقدار قابل انتقال 50 سکه است",
'reply_markup'=>$back,
]);
exit();	
}
if($user['coin'] >= $text){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🛑 توجه : عملیات انتقال سکه غیرقابل بازگشت است !

👈 درصورتی که درخواست انتقال $text سکه مورد تاییدتان است شناسه کاربری مقصد را ارسال کنید",
'reply_markup'=>$back
]);	
$user["step"] = "sendcoin";
$user["data"] = "$text";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ مقدار سکه درخواستی شما جهت انتقال از موجودی شما بیشتر است',
'reply_markup'=>$back
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'⚠️ لطفاً فقط عدد ارسال کنید',
'reply_markup'=>$back
]);
}
elseif($user['step'] =='sendcoin' and $text != "🔙 بازگشت"){
 if(file_exists("data/$text/$text.json") and $text != $from_id){
 $text = str_replace(["`","*","_","[","]","-","+"],["","","","",""],$text);
 $coin = $user['coin'] - $user['data'];
 $user["step"] = "none";
$user["coin"] = "$coin";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[       
'chat_id'=>$chat_id,
'text'=>"`✅ تعداد {$user['data']} سکه در تاریخ $date ساعت $time با موفقیت به کاربر $text انتقال داده شد`
",
'parse_mode'=>'Markdown',
          
'reply_markup'=>$home
]);
 $userdata = json_decode(file_get_contents("data/$text/$text.json"),true);
 $coin = $userdata['coin'] + $user['data'];
bot('sendmessage',[       
'chat_id'=>$text,
'text'=>"`✅ تعداد {$user['data']} سکه در تاریخ $date ساعت $time با موفقیت از کاربر $from_id دریافت شد`
",
'parse_mode'=>'Markdown',
]);
 $user["coin"] = "$coin";
$outjson = json_encode($user,true);
file_put_contents("data/$text/$text.json",$outjson);
}else{
bot('sendmessage',[       
'chat_id'=>$chat_id,
'text'=>"⚠️ این کاربر در ربات $botname نیست",
'reply_markup'=>$back
]); 
}
}
//===========================// panel admin //===========================
elseif($text =='/panel' and $tc =='private' and in_array($from_id,$admin)){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ ادمین عزیز به پنل مدیریت خوش امدید.",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"❄️ آمار ربات"],['text'=>"❄️ افزایش موجودی"]],
[['text'=>"❄️ ارسال به کاربران"],['text'=>"❄️ فروارد به کاربران"]],
[['text'=>"❄️ کسر موجودی"],['text'=>"❄️ مسدود کردن"]],
[['text'=>"❤️ اطلاعات سفارش لایک"],['text'=>"👁‍🗨 اطلاعات سفارش سین"]],
[['text'=>"🔍 اطلاعات کاربر"],['text'=>"❄️ شارژ پنل"]],
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true
])
]);
}
elseif($text =='🔙 برگشت' and $tc =='private' and in_array($from_id,$admin)){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️به‌ صفحه اصلی پنل مدیریت برگشتید‌",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"❄️ آمار ربات"],['text'=>"❄️ افزایش موجودی"]],
[['text'=>"❄️ ارسال به کاربران"],['text'=>"❄️ فروارد به کاربران"]],
[['text'=>"❄️ کسر موجودی"],['text'=>"❄️ مسدود کردن"]],
[['text'=>"❤️ اطلاعات سفارش لایک"],['text'=>"👁‍🗨 اطلاعات سفارش سین"]],
[['text'=>"🔍 اطلاعات کاربر"],['text'=>"❄️ شارژ پنل"]],
[['text'=>"🔙 بازگشت"]],
],
'resize_keyboard'=>true
])
]);
}
elseif($text =='❄️ آمار ربات' and $tc =='private' and in_array($from_id,$admin)){
$alluser = file_get_contents("data/datas.txt");
$alaki = explode("\n",$alluser);
$allusers = count($alaki) - 2;
$allben = file_get_contents("data/ban.txt");
$bes = explode("\n",$allben);
$allbens = count($bes) - 2;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🤖 آمار ربات شما

❄️ تعداد کاربران : $allusers
❄️ تعداد کاربران بن شده : $allbens
",
]);
}
elseif($text =='❄️ ارسال به کاربران' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ لطفا متن یا رسانه خود را ارسال کنید[میتواند شامل عکس باشد]  همچنین میتوانید رسانه را همراه با کشپن[متن چسپیده به رسانه ارسال کنید]",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "sendtoall";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❄️ فروارد به کاربران' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ لطفا پیام خود را فوروارد کنید[پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "fortoall";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❄️ شارژ پنل' and $tc =='private' and in_array($from_id,$admin)){
$apikey1 = "xxxxxxxxxxx";
$apikey2 = "xxxxxxxxxxx";
$result = json_decode(curl("https://AvidSeen.com/web/web.php?apikey=$apikey1&type=amount")); 
$amountt = $result->amount;
$result = json_decode(curl("https://AvidSeen.com/web/web.php?apikey=$apikey2&type=amount")); 
$amounttt = $result->amount;
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🔰 شارژ پنل اول در حال حاضر `$amountt` تومان است

🔰 شارژ پنل دوم در حال حاضر `$amounttt` تومان است",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❄️ افزایش موجودی' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ لطفا در خط اول ایدی فرد و در خط دوم میزان موجودی را وارد کنید.
        
267785153
20
",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "sendadmin";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❄️ کسر موجودی' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ لطفا در خط اول ایدی فرد و در خط دوم میزان موجودی را وارد کنید.
        
267785153
20
",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "sendadminF";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❄️ مسدود کردن' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❄️ لطفا شناسه کاربری فرد را ارسال کنید",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "block";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($text =='❤️ اطلاعات سفارش لایک' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"آیدی عددی رو بفرستید :",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "fihiewf";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($user['step'] =='fihiewf'){  
$result = file_get_contents("data/$text/orderlike.txt");
if($result == true){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔎 لیست اخرین سفارشهای لایک شما :

$result", 
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❗️سفارشی برای نمایش وجود ندارد!',   
]);	
}
elseif($text =='🔍 اطلاعات کاربر' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"آیدی عددی رو بفرستید :",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "grufdf";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($user['step'] =='grufdf'){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
$search = json_decode(file_get_contents("data/$text/$text.json"),true);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"👤 شناسه کاربری : `$text`
👈 تاریخ عضویت : {$search['datejoin']}
👈 تعداد زیرمجموعه : {$search['member']} نفر

💳 مجموع خرید شما : `{$search["buy"]}` تومان
🌟 سطح کاربری : {$search["star"]}
💰 موجودی : `{$search['coin']}`

`⏱ این گزارش وضعیت در تاریخ $date ساعت $time گرفته شده است`
",
'parse_mode'=>'Markdown', 
]);
}
elseif($text =='👁‍🗨 اطلاعات سفارش سین' and $tc =='private' and in_array($from_id,$admin)){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"آیدی عددی رو بفرستید :",
'reply_markup'=>json_encode([
'keyboard'=>[
[['text'=>"🔙 برگشت"]]
],
'resize_keyboard'=>true
])
]);
$user["step"] = "gsbvcvw";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($user['step'] =='gsbvcvw'){
$result = file_get_contents("data/$text/orderseen.txt");
if($result == true){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
🔎 لیست اخرین سفارشهای بازدید شما :

$result",   
]);	
}else
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>'❗️سفارشی برای نمایش وجود ندارد!',
    
]);	
}
//===========================// admin step //===========================
elseif($user['step'] =='charge' and $text != "🔙 برگشت"){	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ فاکتور افزایش موجودی شما : $webapi/pay?user=$userapi&amount=$text",
]);
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($user['step'] =='block' && $tc =='private' and $text != "🔙 برگشت"){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ فرد با موفقیت مسدود شد",
]);	
$block = file_get_contents("data/ban.txt");
$ban = explode("\n",$block);
if(!in_array($text,$ban)){
$myfile2 = fopen("data/ban.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$text\n");
fclose($myfile2);
}
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
}
elseif($user['step'] =='sendadmin' and $text != "🔙 برگشت"){
$text = str_replace(["`","*","_","[","]","-","+"],["","","","",""],$text);
$all = explode("\n", $text);	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"انتقال موجودی با موفقیت انجام شد ✅",
]);
$user = json_decode(file_get_contents("data/$all[0]/$all[0].json"),true);
$coin = $user['coin'] + $all[1] ;
bot('sendmessage',[
'chat_id'=>$all[0],
'text'=>"
`✅ تعداد $all[1] سکه در تاریخ $date ساعت $time با موفقیت از مدیریت دریافت شد`
",
'parse_mode'=>'Markdown', 
]);	
$user["coin"] = "$coin";
$outjson = json_encode($user,true);
file_put_contents("data/$all[0]/$all[0].json",$outjson);
}
elseif($user['step'] =='sendadminF' and $text != "🔙 برگشت"){
$text = str_replace(["`","*","_","[","]","-","+"],["","","","",""],$text);
$all = explode("\n", $text);	
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"انتقال موجودی با موفقیت انجام شد ✅",
]);
$user = json_decode(file_get_contents("data/$all[0]/$all[0].json"),true);
$coin = $user['coin'] - $all[1] ;
bot('sendmessage',[
'chat_id'=>$all[0],
'text'=>"
`✅ تعداد $all[1] سکه در تاریخ $date ساعت $time با موفقیت از مدیریت کسر شد`
",
'parse_mode'=>'Markdown', 
]);	
$user["coin"] = "$coin";
$outjson = json_encode($user,true);
file_put_contents("data/$all[0]/$all[0].json",$outjson);
}
elseif($user['step'] =='sendtoall' and $text != "🔙 برگشت"){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);
$photo = $update->message->photo[count($update->message->photo)-1]->file_id;
$caption = $update->message->caption;
if(isset($photo)){
$all_member = fopen("data/datas.txt",'r');
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('sendphoto',[
'chat_id'=>$user,
'photo'=>$photo,
'caption'=>$caption,
'parse_mode'=>'MarkDown',
]);
}
}else{
$all_member = fopen("data/datas.txt",'r');
while( !feof( $all_member)){
$user = fgets( $all_member);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>$text,
'parse_mode'=>"MarkDown",
]);
}
}
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✔️ پیام شما با موفقیت برای ارسال همگانی تنظیم شد",
]);
}
elseif($user['step'] =='fortoall' and $text != "🔙 برگشت"){
$user["step"] = "none";
$outjson = json_encode($user,true);
file_put_contents("data/$chat_id/$chat_id.json",$outjson);	
$all_member = fopen("data/datas.txt",'r');
while(!feof($all_member)){
$user = fgets($all_member);
bot('ForwardMessage',[
'chat_id'=>$user,
'from_chat_id'=>$chat_id,
'message_id'=>$message_id
]);
}
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✔️ پیام شما با موفقیت به عنوان فوروارد همگانی تنظیم شد",
]); 
}
//===========================// channel //===========================
if(isset($update->channel_post) and file_exists("channel/$channel_post_chat_id/$channel_post_chat_id.json")){
$apikey = "xxxxxxxxxxx";
$explode = explode('^',$user['speed']);
$viewcount = $user['view'];
$amount = $viewcount * $SeenCoin;
$xid = $user['id'];
$info = json_decode(file_get_contents("data/$xid/$xid.json"),true);
if($info['coin'] >= $amount){	
if($update->channel_post->audio != true){
$id = bot('ForwardMessage',[
'chat_id'=>"@$channelorder",   
'from_chat_id'=>$channel_post_chat_id,
'message_id'=>$channel_post_message_id,
])->result->message_id;
}else{
$id = $channel_post_message_id;
$channelorder = $channel_post_chat_username;
}  
$link = "https://t.me/$channelorder/$id";
$result = json_decode(curl("https://AvidSeen.com/web/web.php?apikey=$apikey&type=view&count=$viewcount&speed=$explode[1]&period=$explode[2]&channel=$channelorder&id=$id")); 
$seenpaean = $result->seenend;
$seenstart = $result->seenstart;
$ok = $result->result;
if($ok =='ok'){
$xid = $user['id'];
$info = json_decode(file_get_contents("data/$xid/$xid.json"),true);
$nowcoin = $info['coin'] - $amount ;
$info["coin"] = "$nowcoin";
$outjson = json_encode($info,true);
file_put_contents("data/$xid/$xid.json",$outjson);
$startorder = ($explode[0] == 0)?'فوری':"بعد از $explode[0] دقیقه";
$speed = ($explode[1] == 0)?'حداکثر سرعت':"هر $explode[2] دقیقه $explode[1] بازدید";
$title =bot('getchat',['chat_id'=>"$channel_post_chat_id"])->result->title; 
bot('sendmessage',[ 
'chat_id'=>$user['id'], 
'text'=>"✅ سفارش بازدید خودکار شما برای کانال $title با کد پیگیری {$result->order} ثبت شد

• تعداد بازدید درخواستی : $viewcount
• سرعت تکمیل سفارش : $speed
• استارت سفارش : $startorder
• شروع از : $seenstart بازدید",
]);
bot('sendMessage',[
'chat_id'=>$user['id'], 
'message_id'=>$message_id + 1,
'text'=>"✅ سفارش بازدید خودکار شما برای کانال $title با کد پیگیری {$result->order} تکمیل شد

• تعداد بازدید درخواستی : $viewcount
• سرعت تکمیل سفارش : $speed
• استارت سفارش : $startorder
• شروع از : $seenstart بازدید
• تکمیل در : $seenpaean بازدید
",
]);     
$orderser = "🔎 سفارش شماره : {$result->order}
⏰ زمان : در تاریخ $date و در ساعت $time
✔️ تعداد بازدید درخواستی : $explode[0] بازدید
-------------------------------------";
$myfile2 = fopen("data/$xid/orderseen.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$orderser\n");
fclose($myfile2);
$off = json_decode(file_get_contents("data/$xid/$xid.json"),true);
$coin40 = $off['coin'];
bot('sendMessage',[
'chat_id' =>"@$channelolder",
'text' =>"📣 سفارش بازدید جدید برای بازدید خودکار
             
• کد پیگیری سفارش : {$result->order} 
• سرعت تکمیل : $speed
• هزینه سفارش : $amount سکه
شناسه عددی کاربر : {$user['id']}
تعداد درخواست بازدید : $amount
تعداد سکه کاربر : $coin40
",
]);
}else
bot('sendmessage',[
'chat_id'=>$user['id'],
'text'=>"⚠️ خطا مشکلی در تکمیل سین به وجود آماده است",
'reply_markup'=>$home
]);
exit();
}else{
$title =bot('getchat',['chat_id'=>$channel_post_chat_id])->result->title;
bot('sendmessage',[
'chat_id'=>$user['id'],
'text'=>"❗️سفارش بازدید خودکار انجام نشد

⚠️ به دلیل نداشتن موجودی کافی سفارش سین خودکار کانال $title انجام نشد
",
]);	
}
}
?>