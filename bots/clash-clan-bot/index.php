<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
ob_start();
error_reporting(0);

$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], 
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    
];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;
}
if(!$ok) die("NO :)");
include 'config.php';
define('API_KEY',$token);//توکن را قرار دهید//
$admin2 = "7374685056";
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
//=======//
function save($filename, $data){
$file = fopen($filename, 'w');
fwrite($file, $data);
fclose($file);
}
function SendDocument($chat_id, $document, $caption = null){
bot('SendDocument',[
'chat_id'=>$chat_id,
'document'=>$document,
'caption'=>$caption
]);
}
function SendMessage($chat_id, $text){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>$text,
'parse_mode'=>'MarkDown'
]);
}
function sendmsg($text,$chat_id,$time){
$send = bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>$text,
]);
sleep($time);
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$send->result->message_id,
]);
}
function EditMessageText($chat_id,$message_id,$text,$parse_mode,$disable_web_page_preview,$keyboard){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>$text,
'parse_mode'=>$parse_mode,
'disable_web_page_preview'=>$disable_web_page_preview,
'reply_markup'=>$keyboard
]);
}
 function SendVideo($chat_id,$video,$caption,$keyboard,$duration){
bot('SendVideo',[
'chat_id'=>$chat_id,
'video'=>$video,
'caption'=>$caption,
'duration'=>$duration,
'reply_markup'=>$keyboard
]);
}
function SendPhoto($chat_id, $photo, $caption = null){
bot('SendPhoto',[
'chat_id'=>$chat_id,
'photo'=>$photo,
'caption'=>$caption
]);
}
function sendAction($chat_id, $action){
bot('sendChataction',[
'chat_id'=>$chat_id,
'action'=>$action]);
}
function Forward($kojashe, $azkoja, $kodommsg){
bot('forwardmessage',[
'chat_id'=>$kojashe,
'from_chat_id'=>$azkoja,
'message_id'=>$kodommsg
]);
}
function LeaveChat($chat_id){
bot('LeaveChat',[
'chat_id'=>$chat_id
]);
}
function AnswerCallbackQuery($callback_query_id,$text,$show_alert){
bot('answerCallbackQuery',[
'callback_query_id'=>$callback_query_id,
'text'=>$text,
'show_alert'=>$show_alert,
]);
}
function GetChat($chat_id){
$get =  bot('GetChat',['chat_id'=> $chat_id]);
return $get;
}
function GetMe(){
$get =  bot('GetMe',[]);
return $get;
}
 function EditMessageCaption($chat_id,$message_id,$caption){
bot('editMessageCaption',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
 'caption'=>$caption,
]);
}
//============//
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $from_id       = $update->message->from->id; #
    $chat_id        = $update->message->chat->id; #
    $tc                 = $update->message->chat->type; #
    $text              = $update->message->text; #
    $first_name  = $update->message->from->first_name; #
    $message = $update->message; #
    $message_id = $update->message->message_id; #
}elseif(isset($update->callback_query)){
	$tc                 = $update->callback_query->chat->type; #
    $chat_id       = $update->callback_query->message->chat->id; #
    $data            = $update->callback_query->data; #
    $query_id     = $update->callback_query->id; #
    $message_id = $update->callback_query->message->message_id; #
    $in_text        = $update->callback_query->message->text; #
    $from_id      = $update->callback_query->from->id; #
    $first_name = $update->callback_query->from->first_name; #
    $callback_query = $update->callback_query; #
    $callback_query_id = $update->callback_query->id; #
} 
mkdir("data/$from_id");
$photo = $update->message->photo;
$first_name = $update->message->from->first_name;
$last_name = $update->message->from->last_name;
$username = $update->message->from->username;
$memlist = explode("\n", $members);
$step = file_get_contents("data/$chat_id/step.txt");
$send = file_get_contents("data/send.txt");
$state = file_get_contents("data/$chat_id/state.txt");
$forward = $message->forward_from;
 $forward_id = $message->forward_from->id;
 $firstname = $update->callback_query->message->chat->first_name;
 $user = $update->callback_query->message->chat->username;
//==============//
$date = file_get_contents("data/$chat_id/date.txt");
$buyzula = file_get_contents("data/$from_id/buyzula.txt");
$numid = file_get_contents("data/$from_id/numid.txt");
$timelimit = file_get_contents("data/timelimit.txt");
$inch = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$channel&user_id=".$from_id);
$blocklist = file_get_contents("data/blocklist.txt");
$members = file_get_contents('Member.txt');
$memlist = explode("\n", $members);
$member = file_get_contents("data/$from_id/member.txt");
@$list = json_decode(file_get_contents("data/list.json"),true);
$adid = file_get_contents("data/$chat_id/adid.txt");
$cash = file_get_contents("data/$chat_id/cash.txt");
$dhkh = file_get_contents("data/$chat_id/dhkh.txt");
$tch1 = $truechannel1->result->status;
$truechannel2 = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=@$channel2&user_id=$chat_id"));
$tch2 = $truechannel2->result->status;
//================================\\
if($chat_id == $message->from->id){
if($tch1 != 'member' && $tch1 != 'creator' && $tch1 != 'administrator'){
if($tch2 != 'member' && $tch2 != 'creator' && $tch2 != 'administrator'){
bot("sendmessage",[
"chat_id"=>$chat_id,
"text" => "کاربر عزیز ، برای استفاده از ربات حتما باید در کانال های زیر عضو بشی 👇

🔴 @$channel1
🔴 @$channel2

✅ بعد از عضویت در کانال ها 🔽 روی /start بزنید.",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[['text'=>"🔽 کانال 1",'url'=>"https://t.me/$channel1"]],
[['text'=>"🔽 کانال 2",'url'=>"https://t.me/$channel2"]],
],
])
]);
exit();
}
}
}
if(strpos($text,"/start s") !== false){
$text = str_replace("/start s","",$text);
$st = file_get_contents("data/adp/$text/st.txt");
if($st == "yes"){
file_put_contents("data/$from_id/dhkh.txt",$text);
$p = file_get_contents("data/adp/$text/p.txt");
bot('SendMessage',[
'chat_id'=>$chat_id,
'text'=>"یکی از روش های زیر را برای خرید اکانت #acc$text انتخاب کنید.

💰 قیمت اکانت : $p
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💳 خرید با موجودی ربات", 'callback_data'=>"khba"]],
[['text'=>"🗣️ خرید مستقیم از کاربر", 'callback_data'=>"khaz"]],
[['text'=>"کنسل", 'callback_data'=>"back"]],
]
])
]);
}else{
bot('SendMessage',[
'chat_id'=>$chat_id,
'text'=>"به نظر می رسد اکانت مورد نظر فروخته شده یا آگهی توسط مالک حذف شده است.
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"باشه", 'callback_data'=>"back"]],
]
])
]);
}
}
if($data=="khba"){
$p = file_get_contents("data/adp/$dhkh/p.txt");
$userid = file_get_contents("data/adp/$dhkh/iduser.txt");
if($cash >= $p){
$cash -= $p;
file_put_contents("data/$chat_id/cash.txt",$cash);
file_put_contents("data/adp/$dhkh/st.txt","no");
$rand = rand(99, 9999);
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"با تشکر از خرید شما🌹

کد اکانت خریداری شده : #acc$dhkh
موجودی جدید شما : $cash

این پیام را برای پشتیبانی ارسال کنید و اکانت را تحویل بگیرید.

@qqabr

🛡️ scode: $rand
",
'parse_mode'=>"HTML"
]);
bot('sendMessage',[
'chat_id'=>$admin,
'text'=>"اکانت جدید توسط کاربر خریداری شد!

کد اکانت خریداری شده : #acc$dhkh
موجودی جدید کاربر : $cash
 
کد زیر را با پیام فرواردی کاربر مقایسه کنید.

🛡️ scode: $rand
",
'parse_mode'=>"HTML"
]);
bot('sendMessage',[
'chat_id'=>$userid,
'text'=>"اکانت شما توسط موجودی ربات خریداری شد🤝

کد اکانت خریداری شده : #acc$dhkh

جهت تسویه حساب این پیام را برای پشتیبانی فروارد کنید!

@qqabr

🛡️ scode: $rand
",
'parse_mode'=>"HTML"
]);
file_put_contents("data/adp/$dhkh/st.txt","no");
$mgid = file_get_contents("data/adp/$dhkh/delm.txt");
bot('sendMessage',[
        'chat_id' => $channelid,
        'text' => "🎮 اکانت شماره #acc$dhkh فروخته شد.",
        'reply_to_message_id'=>$mgid
    ]);
if(unlink("data/adp/$dhkh/p13.txt") && unlink("data/adp/$dhkh/p23.txt") && unlink("data/adp/$dhkh/p33.txt") && unlink("data/adp/$dhkh/u.txt") && unlink("data/adp/$dhkh/p.txt") && unlink("data/adp/$dhkh/iduser.txt")){
bot('sendMessage',[
        'chat_id' => $admin,
        'text' => "فایل های آگهی 
#acc$dhkh
حذف شد.",
    ]);
}
}else{
$cashp = $cash - $p;
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
موجودی شما کافی نیست!
موجودی شما : $cash
قیمت اکانت منهای موجودی کنونی : $cashp
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💲 شارژ حساب", 'callback_data'=>"3cash"]],
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
}
}
if($data=="khaz"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"آیا مطمعن هستی میخواهی دستی خرید کنی؟ربات هیچگونه مسولیتی در قبال تبر خوردن شما به در صورت دستی نداره!",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅", 'callback_data'=>"go"]],
[['text'=>"برگشت به منوی اصلی", 'callback_data'=>"back"]],
]
])
]);
}
if($data=="go"){
$user = file_get_contents("data/adp/$dhkh/u.txt");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
اکانت : #acc$dhkh
یوزنیم اگهی دهنده : $user
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
$userid = file_get_contents("data/adp/$dhkh/iduser.txt");
bot('sendMessage',[
'chat_id'=>$userid,
'text'=>"👁️ یک مشتری جدید یوزرنیم شما را درخواست نمود!

در صورتی که اکانت خود را فروختید روی دکمه زیر کلیک کنید👇
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🤝 فروختم", 'callback_data'=>"yyy|$dhkh"]],
]
])
]);
}
if(strpos($data,"yyy|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
file_put_contents("data/adp/$key/st.txt","no");
$mgid = file_get_contents("data/adp/$key/delm.txt");

bot('sendMessage',[
        'chat_id' => $channelid,
        'text' => "🎮 اکانت شماره #acc$key فروخته شد.",
        'reply_to_message_id'=>$mgid
    ]);
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
👍 اطلاعات جدید ثبت شد.

با تشکر از همکاری شما
",
'parse_mode'=>"HTML"
]);
if(unlink("data/adp/$key/p13.txt") && unlink("data/adp/$key/p23.txt") && unlink("data/adp/$key/p33.txt") && unlink("data/adp/$key/u.txt") && unlink("data/adp/$key/p.txt") && unlink("data/adp/$key/iduser.txt")){
bot('sendMessage',[
        'chat_id' => $admin,
        'text' => "فایل های آگهی 
#acc$key
حذف شد.",
        'reply_to_message_id'=>$mgid
    ]);
}
}
if(strpos($text,"/start ") !== false){
$text = str_replace("/start ","",$text);
$st = file_get_contents("data/adp/$text/st.txt");
if($st == "yes"){
$p1 = file_get_contents("data/adp/$text/p13.txt");
$p2 = file_get_contents("data/adp/$text/p23.txt");
$p3 = file_get_contents("data/adp/$text/p33.txt");
bot('Sendphoto',[
'chat_id'=>$chat_id,
'photo'=>$p1,
'caption'=>"1/3",
'parse_mode'=>"HTML",
]);
bot('Sendphoto',[
'chat_id'=>$chat_id,
'photo'=>$p2,
'caption'=>"2/3",
'parse_mode'=>"HTML",
]);
bot('Sendphoto',[
'chat_id'=>$chat_id,
'photo'=>$p3,
'caption'=>"3/3",
'parse_mode'=>"HTML",
]);
bot('SendMessage',[
'chat_id'=>$chat_id,
'text'=>"⚠️ تصاویر پس از 10 ثانیه حذف می‌شوند ربات بعد از حذف شدن عکس ها جواب میدهد لطفا اسپم نکنید⚠️",
'parse_mode'=>"HTML"
]);
sleep(10);
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id+1
]);
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id+2
]);
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$message_id+3
]);
}else{
bot('SendMessage',[
'chat_id'=>$chat_id,
'text'=>"اگهی مورد نظر یافت نشد!",
'parse_mode'=>"HTML"
]);
}
}


//=============//
if($text == "/start"){
$user = file_get_contents('data/users.txt');
$members = explode("\n", $user);
if(!in_array($from_id, $members)){
$add_user = file_get_contents('data/users.txt');
$add_user .= $from_id . "\n";
file_put_contents("data/$chat_id/membrs.txt", "0");
file_put_contents('data/users.txt', $add_user);
file_put_contents("data/$from_id/cash.txt","0");
}
file_put_contents("data/$from_id/step.txt","no");
bot('SendMessage',[
'chat_id'=>$chat_id,
'text'=>"
سلام $first_name عزیز 🌹

🌿 به ربات کلشران خوش آمدید!
➖➖➖➖➖
🧧در این ربات میتونید اکانت کلش اف کلنز یا کلش رویال خودتونو به فروش برسونید.
➖➖➖➖➖
💯 آگهی شما توی کانال اصلی آگهی ربات گذاشته میشه و در صورت به فروش رفتن اکانتتون بهتون اطلاع داده میشه.

💎@$channel
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅ ثبت اکانت (رایگان)", 'callback_data'=>"aghahi"]],
[['text'=>"لیست کانال ها🌐 ", 'callback_data'=>"2"],['text'=>"💰 حساب کاربری", 'callback_data'=>"3"]],
[['text'=>"ارتباط با پشتیبانی 👤", 'callback_data'=>"4"],['text'=>"🔻 قوانین 🔻", 'callback_data'=>"5"]],
[['text'=>"درخواست واسطه ▪️️", 'callback_data'=>"6"]],
],
'resize_keyboard'=>true,
])
]);
}

//==============//
elseif(strpos($inch1,'"status":"left"')== true){ 
 bot('answercallbackquery',[
'callback_query_id'=>$callback_query_id,
'text'=>"
⭕️شما عضو کانال ما نیستید .
/start
ارسال کنید.
",
'parse_mode'=>"html",
'show_alert'=>true,
]);
}   
//=============//
elseif($data=="back"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
به منوی اصلی برگشتیم😉

➖➖➖➖➖
💎@$channel
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅ ثبت اکانت (رایگان)", 'callback_data'=>"aghahi"]],
[['text'=>"لیست کانال ها🌐 ", 'callback_data'=>"2"],['text'=>"💰 حساب کاربری", 'callback_data'=>"3"]],
[['text'=>"ارتباط با پشتیبانی 👤", 'callback_data'=>"4"],['text'=>"🔻 قوانین 🔻", 'callback_data'=>"5"]],
[['text'=>"درخواست واسطه ▪️️", 'callback_data'=>"6"]],
],
'resize_keyboard'=>true,
])
]);
}

//===============//
elseif(strpos($blocklist, "$chat_id") !== false){
bot('answercallbackquery',[
'callback_query_id'=>$callback_query_id,
'text'=>"شما از بات بلاک شده اید 😐",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'KeyboardRemove'=>[],'remove_keyboard'=>true
])
]);
}
//==============//
elseif($data=="5"){
	$tuser -= 1;
$user['tuser'] = $tuser;
file_put_contents("data/$from_id.json",json_encode($user));
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
☯🔻 قوانین ثبت آگهی به شرح زیر میباشد.

1️⃣تمام آگهی ها برای تایید باید در چارچوب قوانین جمهوری اسلامی باشد.
2️⃣ اکانت های غیراخلاقی و غیرمجاز تایید نخواهد شد.
3️⃣ کانال و ربات ما هیچگونه مسئولیتی در قبال خرید و فروش بین مشتری و فروشنده ندارد.
4️⃣ در صورت عدم تکمیل کامل فرم آگهی ، تبلیغ شما تایید نخواهد شد.
5️⃣ برای ثبت اگهی محدودیتی وجود نداره.

🤍 با رعایت قوانین آگهی خود را بفرستید.
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
}
//===============//
elseif($data=="2"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
لیست کانال های ربات به شرح زیر میباشد :

@$channel
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
}
//===============//
elseif($data=="3"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
👤 نام شما : $firstname
🪪 آیدی عددی شما : `$chat_id`
💰 موجودی شما : $cash تومان
🤖 نام ربات : کلشران | ClashRan
🤖 آیدی ربات : @$botidd
",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"💲 شارژ حساب", 'callback_data'=>"3cash"]],
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
}
elseif($data=="3cash"){
file_put_contents("data/$chat_id/step.txt", "cashs");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
$cashstxt
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"3"]],
]
])
]);
}
//==============//
elseif($data=="6"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🦚 برای درخواست واسطه از دکمه های زیر استفاده کنید.
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"▫️وارد شدن به پیوی ◽️",'url'=>'https://t.me/qqabr']],
[['text'=>"▫️کانال واسطه گری ها ◽️",'url'=>'https://t.me/ClashRanch']],
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
]
])
]);
}
//=============//
elseif($data=="aghahi"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
لطفا از منوی زیر نوع آگهی خود را انتخاب کنید !
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🎲 اکانت کلش رویال", 'callback_data'=>"rulesgame1"],['text'=>"🎲 اکانت کلش آف کلنز", 'callback_data'=>"rulesgame2"]],
[['text'=>"برگشت 🔙", 'callback_data'=>"back"]],
],
'resize_keyboard'=>true
])
]);
}
//----------------------------------------------
elseif($data =="4"){
file_put_contents("data/$chat_id/state.txt","mok");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"لطفا نظر،پیشنهاد و مشکل خود را اِرسال کنید 👇🏻",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($state == "mok"){
file_put_contents("data/$chat_id/state.txt","none");
bot('sendMessage',[
'chat_id'=>$admin,
'text'=>"
💡مدیر  یک پیام با اطلاعات زیر داری:
نام کاربر:$first_name
ایدی کاربر: @$username
ایدی عددی کاربر : <pre>$from_id</pre>
🚦 متن پیام :
- - - - - - - - - - - -
$text
",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"پاسخ به کاربر 💡",'callback_data'=>"pm|$from_id"]],
],
'resize_keyboard'=>true,
])
]);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"ارسال شد ✅",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif(strpos($data,"pm|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
file_put_contents("data/$chat_id/info.txt","$key");
file_put_contents("data/$chat_id/state.txt","pm1");
bot('sendMessage',[
'chat_id'=>$admin,
'text'=>"
شما در حال پاسخ به ایدی $key هستید 🛡
پیام خود را ارسال کنید 
",
'parse_mode'=>'html',
]);
}
elseif($state == "pm1"){
file_put_contents("data/$chat_id/state.txt","none");
file_put_contents("data/$from_id/sendpm.txt","$text");
file_put_contents("data/$chat_id/state.txt","none");
$info = file_get_contents("data/$from_id/info.txt");
$sendpm = file_get_contents("data/$from_id/sendpm.txt");
bot('sendMessage',[
'chat_id'=>$info,
 'text'=>"
 پاسخ پیام شما از پشتیبانی 🎙
- - - - - - - - - - - -
$sendpm
",
'parse_mode'=>'MarkDown',
]);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"ارسال شد ✅",
]);
}
//------------
//---------------------------فروش اکانت بازی-----------------------
elseif($data=="rulesgame1"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"✅ به بخش فروش اکانت بازی کلش رویال خوش آمدید.

🔸 قبل از درج آگهی قوانین زیر را به دقت مطالعه کرده و در صورت موافقت اگهی خود را درج کنید.

قوانین درج آگهی
1️⃣اکانتی که ثبت میکنید حتما باید مال خودتون باشه موقع ثبت شمارتون ذخیره میشه و اگر مال خودتون نباشه از ربات مسدود دائم خواهید شد.

2️⃣در صورتی که امکان معامله به صورت مستقیم با مشتری را ندارید میتوانید با موجودی ربات اکانت را خریداری کنید و یا با دکمه درخواست واسطه واسطه برقرار کنید

3️⃣لطفا توضیحات اکانت خود را به صورت واقعی درج کرده و عکس اکانت اصلی برای فروش را ارسال کنید.

با انتخاب دکمه 'با قوانین بالا موافقم' تمام قوانین مورد پذیرش شما قرار گرفته و امکان درج آگهی را خواهید داشت.",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅ با قوانین بالا موافقم",'callback_data'=>"clashr"]],
],
])
]);
}
elseif($data=="rulesgame2"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"✅ به بخش فروش اکانت بازی کلش اف کلنز خوش آمدید.

🔸 قبل از درج آگهی قوانین زیر را به دقت مطالعه کرده و در صورت موافقت اگهی خود را درج کنید.

قوانین درج آگهی
1️⃣اکانتی که ثبت میکنید حتما باید مال خودتون باشه موقع ثبت شمارتون ذخیره میشه و اگر مال خودتون نباشه از ربات مسدود دائم خواهید شد.

2️⃣در صورتی که امکان معامله به صورت مستقیم با مشتری را ندارید میتوانید با موجودی ربات اکانت را خریداری کنید و یا با دکمه درخواست واسطه واسطه برقرار کنید

3️⃣لطفا توضیحات اکانت خود را به صورت واقعی درج کرده و عکس اکانت اصلی برای فروش را ارسال کنید.

با انتخاب دکمه 'با قوانین بالا موافقم' تمام قوانین مورد پذیرش شما قرار گرفته و امکان درج آگهی را خواهید داشت.",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"✅ با قوانین بالا موافقم",'callback_data'=>"clashofc"]],
],
])
]);
}


//================کلش رویال==================//
elseif($data=="clashr"){
if($send != "ok"){
bot('answercallbackquery',[
'callback_query_id'=>$callback_query_id,
'text'=>"لحظات دیگری اقدام به ثبت آگهی جدید کنید",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'KeyboardRemove'=>[],'remove_keyboard'=>true
])
]);
exit();
}
if(time() > $date){
file_put_contents("data/$chat_id/step.txt","photoclashr");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🖼 لطفا از منوی اصلی منوی نبرد اکانتت یه عکس بفرست

🆔@$channel
",
'reply_to_message_id'=>$message_id,
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
else{
$sec = $date - time();
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
⏳ هر $timelimit ثانیه میتوان یک پست ارسال کرد.
⏱ زمان ارسال آگهی بعدی $sec ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step== "photoclashr"){
$rand = rand(9999, 999999999999);
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/$from_id/pics.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photoclashr13");
file_put_contents("data/$chat_id/adid.txt","$rand");
mkdir("data/adp");
mkdir("data/adp/$rand");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ عکس قسمت نبرد ثبت شد.

تو این قسمت ۳ عکس از جاهای مختلف اکانت برای دیدن مشتریان باید بفرستی.

حالا یه عکس دیگه از لول های نیرو هات بفرست.

عکس : 1/3 بفرست تا تنطیم بشه😉
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step== "photoclashr13"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p13.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photoclashr23");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ عکس باموفقیت ثبت شد.

حالا یه عکس دیگه از نیروهات بفرست از نیروهای دیگت که ثبت نشده

عکس 2/3
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step== "photoclashr23"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p23.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photoclashrend");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ این عکس هم ثبت شد.

دوباره یه عکس دیگه از لول نیروهات بده از نیروهایی که ثبت نشده

عکس : 3/3
",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step== "photoclashrend"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p33.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","description");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ 3/3 عکس تکمیل شد!

〽️ حال مشخصات زیر را با لمس روی کپی کردن کد کپی کنید و بنویسید و بفرستید جلوی مشخصات خواسته شده رو پر کنید.
➖➖➖➖
```
🙋‍♂️ اسم اکانت:
🎖لول بازی:
⚔️تعداد لجندری:
👥امکان تغییر نام چند جم:
💎تعداد جم:
💰تعداد سکه:
?? سوپرسل آیدی (شده|نشده):
⚙️لينک ديوايس(شده یا نشده): درصورت اینکه سوپرسل ایدی شده تغییر ندید.
🏆 کاپ :
📝 توضیحات دیگر:```
➖➖➖➖
🔻 نکته : جلوی توضیخات رو خالی نزارید.

⚠️ در صورتی که هر یک از موارد بالا را در فرم بالا تکمیل نکرده باشید ، آگهی شما تایید نخواهد  شد.",
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step == "description" and $text != null){
    file_put_contents("data/$from_id/cap.txt", $text);
    file_put_contents("data/$from_id/step.txt", "price");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💳 قیمت اکانتت را به صورت تومان بفرست :",
        'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
    ]);
}
elseif($step == "price" and preg_match('/\d/', $text)){
    file_put_contents("data/adp/$adid/p.txt", $text);
    file_put_contents("data/$from_id/step.txt", "finishr");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🔎 ایدی یوزرنیم فروشنده اصلی اکانت را بفرست:",
        'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
    ]);
}
elseif($step == "finishr" and $text != null){
    file_put_contents("data/adp/$adid/u.txt",$text);
    $cap = file_get_contents("data/$from_id/cap.txt");
    file_put_contents("data/$from_id/step.txt","no");
    $photoo = file_get_contents("data/$from_id/pics.txt");
    bot('Sendphoto', [
        'chat_id' => $admin,
        'photo' => $photoo,
        'caption' => "$cap\n\n🔖 کد آگهی: #acc$adid\nعکس های بیشتر :\nhttps://t.me/$botidd?start=$adid",
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "تایید ✅", 'callback_data' => "yes7s|$from_id"]],
                [['text' => "رد ❌", 'callback_data' => "no7s|$from_id"]],
            ],
            'resize_keyboard' => true,
        ])
    ]);
    file_put_contents("data/adp/$adid/iduser.txt",$from_id);
    file_put_contents("data/adp/$adid/st.txt","yes");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✅ تمامی مشخصات اکانت شما ثبت شد. آگهی شما به ادمین ارسال شد. منتظر تایید باشید:\n\n$cap",
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
     ]);
    file_put_contents("data/$from_id/step.txt", "no");
}
elseif(strpos($data,"yes7s|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
$cap1 = file_get_contents("data/$key/cap.txt");
$adidd = file_get_contents("data/$key/adid.txt");
$photoo = file_get_contents("data/$key/pics.txt");
file_put_contents("data/photo.txt",$photoo);
file_put_contents("data/cap.txt","$cap1\n\n🔖 کد آگهی: #acc$adidd\n🆔@$channel");
file_put_contents("data/adid.txt",$adidd);
file_put_contents("data/$from_id/step.txt","sendadchannel");
$send = bot('editMessageCaption',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'caption'=>"
$cap1
➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖
✅ آگهی فوق توسط ادمین $first_name تایید شد!
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
file_put_contents("data/send.txt","off");
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$send->result->message_id
]);
bot('Sendmessage',[
'chat_id'=>$key,
'text'=>"
اگهی شما با موفقیت تایید و تا دقایقی دیگه به کانال فرشتاده خواهد شد ✅
",
'parse_mode'=>"HTML",
'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "حذف آگهی", 'callback_data' => "yyy|$adidd"]],
            ],
            'resize_keyboard' => true,
        ]) 
]);
}
elseif(strpos($data, "no7s|") !== false ){
    $exit = explode("|", $data);
    $key = $exit[1];

    $cap1 = file_get_contents("data/$key/cap.txt");
    $numid1 = file_get_contents("data/$key/numid.txt");
    $id1 = file_get_contents("data/$key/id.txt");

    file_put_contents("data/$from_id/step.txt", "no");

    $send1 = bot('editMessageCaption', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "
        $cap1
        ➖➖➖➖➖➖➖➖➖➖➖➖
        آگهی مورد نظر با اطلاعات زیر رد شد ❌
        ",
        'parse_mode' => "HTML",
    ]);
    sleep(3);

    bot('deletemessage', [
        'chat_id' => $chat_id,
        'message_id' => $send1->result->message_id
    ]);

    bot('Sendmessage', [
        'chat_id' => $key,
        'text' => "
        متاسفانه آگهی شما رد شد ❌
        ",
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
    ]);

    file_put_contents("data/$key/cap.txt", '');
    file_put_contents("data/$key/pics.txt", '');
}
//=============کلش اف کلنز===============//
elseif($data=="clashofc"){
if($send != "ok"){
bot('answercallbackquery',[
'callback_query_id'=>$callback_query_id,
'text'=>"لحظات دیگری اقدام به ثبت آگهی جدید کنید",
'parse_mode'=>"html",
'reply_markup'=>json_encode([
'KeyboardRemove'=>[],'remove_keyboard'=>true
])
]);
exit();
}
if(time() > $date){
file_put_contents("data/$chat_id/step.txt","photofclans");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🖼 لطفا از دهکده خانه کلشت برای عکس اصلی یه عکس بفرست.

🆔@$channel
",
'reply_to_message_id'=>$message_id,
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
else{
$sec = $date - time();
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
⏳ هر $timelimit ثانیه میتوان یک پست ارسال کرد.
⏱ زمان ارسال آگهی بعدی $sec ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($step== "photofclans"){
$rand = rand(9999, 999999999999);
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/$from_id/pics.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photofclans13");
file_put_contents("data/$chat_id/adid.txt","$rand");
mkdir("data/adp");
mkdir("data/adp/$rand");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ عکس دهکده اصلی ثبت شد.

تو این قسمت ۳ عکس از جاهای مختلف اکانت برای دیدن مشتریان باید بفرستی.

حالا یه عکس دیگه از لول های نیرو هات توی دهکده خانه بفرست.

عکس : 1/3 بفرست تا تنطیم بشه😉",
'parse_mode'=>'MarkDown',
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
]);
}
}
elseif($step== "photofclans13"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p13.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photofclans23");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ عکس باموفقیت ثبت شد.

حالا برو توی بیلدرهال و از دهکدهش عکس بفرست

عکس 2/3",
'parse_mode'=>'MarkDown',
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
]);
}
}
elseif($step== "photofclans23"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p23.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","photofclansend");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
✅ این عکس هم ثبت شد.

بزن رو پروفایلت و از لول نیروهای بیلدرهال عکس بفرست

عکس : 3/3",
'parse_mode'=>'MarkDown',
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
]);
}
}
elseif($step == "photofclansend"){
if(is_array($photo)){
$count = count ($photo) - 1;
$photo_id = $photo[$count]->file_id;
file_put_contents("data/adp/$adid/p33.txt","$photo_id");
file_put_contents("data/$chat_id/step.txt","description");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"✅ 3/3 عکس تکمیل شد!

〽️ حال مشخصات زیر را با لمس روی کپی کردن کد کپی کنید و بنویسید و بفرستید جلوی مشخصات خواسته شده رو پر کنید.
➖➖➖➖
```
    🙋‍♂️ اسم اکانت:
    🎖 لول بازی:
    ♟ تاون هال:
    👥 امکان تغییر نام:
    💎 تعداد جم:
    🏷 سوپرسل آیدی:
    🎨 تعداد اسکین دهکده:
    ⚙️ لینک دیوایس : در صورتی سوپرسل آیدی فعال کردید ادیت نکنید.
    🏆 کاپ:
    📝 توضیحات دیگر:
```
➖➖➖➖
🔻 نکته : جلوی توضیخات رو خالی نزارید.

⚠️ در صورتی که هر یک از موارد بالا را در فرم بالا تکمیل نکرده باشید ، آگهی شما تایید نخواهد  شد.",
'parse_mode'=>'MarkDown',
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
]);
}
}
elseif($step == "description" and $text != null){
    file_put_contents("data/$from_id/cap.txt", $text);
    file_put_contents("data/$from_id/step.txt", "price");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💳 قیمت اکانتت را به صورت تومان بفرست :",
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
    ]);
}
elseif($step == "price" and preg_match('/\d/', $text)){
    file_put_contents("data/adp/$adid/p.txt", $text);
    file_put_contents("data/$from_id/step.txt", "finishr");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🔎 ایدی یوزرنیم فروشنده اصلی اکانت را بفرست:",
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
    ]);
}
elseif($step == "finish" and $text != null){
    file_put_contents("data/adp/$adid/u.txt",$text);
    $cap = file_get_contents("data/$from_id/cap.txt");
    file_put_contents("data/$from_id/step.txt","no");
    $photoo = file_get_contents("data/$from_id/pics.txt");
    bot('Sendphoto', [
        'chat_id' => $admin,
        'photo' => $photoo,
        'caption' => "$cap🔖 کد آگهی: #acc$adid\nعکس های بیشتر :\nhttps://t.me/$botidd?start=$adid",
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "تایید ✅", 'callback_data' => "yes7s|$from_id"]],
                [['text' => "رد ❌", 'callback_data' => "no7s|$from_id"]],
            ],
            'resize_keyboard' => true,
        ])
    ]);
    file_put_contents("data/adp/$adid/iduser.txt",$from_id);
    file_put_contents("data/adp/$adid/st.txt","yes");
    bot('Sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✅ تمامی مشخصات اکانت شما ثبت شد. آگهی شما به ادمین ارسال شد. منتظر تایید باشید:\n\n$cap",
    'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>"برگشت 🔙",'callback_data'=>"back"]],
            ],
            'resize_keyboard' => true,
        ])
     ]);
    file_put_contents("data/$from_id/step.txt", "no");
}
elseif(strpos($data,"yes7s|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
$cap1 = file_get_contents("data/$key/cap.txt");
$adidd = file_get_contents("data/$key/adid.txt");
$photoo = file_get_contents("data/$key/pics.txt");
file_put_contents("data/photo.txt",$photoo);
file_put_contents("data/cap.txt","$cap1\n\n🔖 کد آگهی: #acc$adidd\n🆔@$channel");
file_put_contents("data/adid.txt",$adidd);
file_put_contents("data/$from_id/step.txt","sendadchannel");
$send = bot('editMessageCaption',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'caption'=>"
$cap1
➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖
✅ آگهی فوق توسط ادمین $first_name تایید شد!
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
file_put_contents("data/send.txt","off");
bot('deletemessage',[
'chat_id'=>$chat_id,
'message_id'=>$send->result->message_id
]);
bot('Sendmessage',[
'chat_id'=>$key,
'text'=>"
اگهی شما با موفقیت تایید شد و تا یک دقیقه دیگر به کانال فرستاده خواهد شد ✅
",
'parse_mode'=>"HTML",
'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "حذف آگهی", 'callback_data' => "yyy|$adidd"]],
            ],
            'resize_keyboard' => true,
        ]) 
]);
}
elseif(strpos($data, "no7s|") !== false ){
    $exit = explode("|", $data);
    $key = $exit[1];

    $cap1 = file_get_contents("data/$key/cap.txt");
    $numid1 = file_get_contents("data/$key/numid.txt");
    $id1 = file_get_contents("data/$key/id.txt");

    file_put_contents("data/$from_id/step.txt", "no");

    $send1 = bot('editMessageCaption', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "
        $cap1
        ➖➖➖➖➖➖➖➖➖➖➖➖
        آگهی مورد نظر با اطلاعات زیر رد شد ❌
        ",
        'parse_mode' => "HTML",
    ]);
    sleep(3);

    bot('deletemessage', [
        'chat_id' => $chat_id,
        'message_id' => $send1->result->message_id
    ]);

    bot('Sendmessage', [
        'chat_id' => $key,
        'text' => "
        متاسفانه آگهی شما رد شد ❌
        ",
        'parse_mode' => 'HTML',
    ]);

    file_put_contents("data/$key/cap.txt", '');
    file_put_contents("data/$key/pics.txt", '');
}
$steep = file_get_contents("data/$admin/step.txt");
if($update && $steep == "sendadchannel"){
$messageid = $update->channel_post->message_id; #
$code = file_get_contents("data/$admin/delm.txt");
$m = file_get_contents("data/adp/$code/delm.txt");
if($code != "0" and $m == null){
file_put_contents("data/$admin/step.txt", "no");
file_put_contents("data/$admin/delm.txt","0");
file_put_contents("data/adp/$code/delm.txt",$message_id);
}
}
//===========//
elseif($from_id == $admin2 and $text =="پنل")
{
  bot('sendmessage', [
'chat_id' =>$chat_id,
'text' =>"به پنل مدیریتی ربات خوش آمدید ⛳️",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'آمار 🔖' , 'callback_data'=>"amar"],['text'=>'محدودیت آگهی 〽️' , 'callback_data'=>"limit"]],
[['text'=>'پیام همگانی 📬' , 'callback_data'=>"pmall"],['text'=>'فورارد همگانی 🗃' , 'callback_data'=>"forall"]],
[['text'=>'بلاک کردن 🔫' , 'callback_data'=>"block"],['text'=>'آنبلاک کردن 🔪' , 'callback_data'=>"unblock"]],
[['text'=>'مدیر ها 💎' , 'callback_data'=>"adlist"],['text'=>'ارسال آگهی 💾' , 'callback_data'=>"post"]],
[['text'=>'👤 تغییر موجودی کاربر' , 'callback_data'=>"+cash"]],
[['text'=>'لیست کانال ها 🆑' , 'callback_data'=>"chhh11"],['text'=>'منوی اصلی 🔰' , 'callback_data'=>"back"]],
],
'resize_keyboard'=>true
])
]);
}
elseif($chat_id==$admin and $data == "2d"){
file_put_contents("data/$chat_id/step.txt", "none");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text' =>"به پنل مدیریتی ربات خوش آمدید ⛳️",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'آمار 🔖' , 'callback_data'=>"amar"],['text'=>'محدودیت آگهی 〽️' , 'callback_data'=>"limit"]],
[['text'=>'پیام همگانی 📬' , 'callback_data'=>"pmall"],['text'=>'فورارد همگانی 🗃' , 'callback_data'=>"forall"]],
[['text'=>'بلاک کردن 🔫' , 'callback_data'=>"block"],['text'=>'آنبلاک کردن 🔪' , 'callback_data'=>"unblock"]],
[['text'=>'مدیر ها 💎' , 'callback_data'=>"adlist"],['text'=>'ارسال آگهی 💾' , 'callback_data'=>"post"]],
[['text'=>'👤 تغییر موجودی کاربر' , 'callback_data'=>"+cash"]],
[['text'=>'لیست کانال ها 🆑' , 'callback_data'=>"chhh11"],['text'=>'منوی اصلی 🔰' , 'callback_data'=>"back"]],
],
'resize_keyboard'=>true
])
]);
}
//===========//
elseif($data=="post"){
file_put_contents("data/$chat_id/step.txt","nozula1");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🅿️آگهی خود را در قالب #متن بفرستید!
",
'reply_to_message_id'=>$message_id,
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "nozula1"){
file_put_contents("data/$from_id/nozula1.txt","$text");
file_put_contents("data/$from_id/step.txt","no");
$nozula1 = file_get_contents("data/$from_id/nozula1.txt");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
$nozula1
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"تایید ✅",'callback_data'=>"yes78|$from_id"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif(strpos($data,"yes78|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
$nozula12 = file_get_contents("data/$key/nozula1.txt");
file_put_contents("data/$from_id/step.txt","no");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
$nozula12
➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖
پیام مورد نظر به تمامی کانال ها ارسال شد ✅
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
bot('Sendmessage',[
'chat_id'=>$channelid,
'text'=>"
$nozula12
➖➖➖➖➖➖
🆔@account_sell_fwbot
",
'parse_mode'=>"HTML",
]);
}

elseif($data=="chhh11"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
لیست کانال های ربات آگهی به شرح زیر میباشد!

$channel
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"2d"]],
]
])
]);
}
elseif($chat_id==$admin and $data == "unblock"){
file_put_contents("data/$chat_id/step.txt","sharr");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"لطفا ایدی عددی کاربر مورد نظر رو ارسال کنید",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "sharr"){
file_put_contents("data/$chat_id/step.txt", "none");
$newlist = str_replace($text, "", $blocklist);
file_put_contents("data/blocklist.txt", $newlist);
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
خب ایدی $text از بلاکی درآمد 😎
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
bot('Sendmessage',[
'chat_id'=>$text,
'text'=>"
ارتباط شما با سرور برقرار شد و میتوانید از بات استفاده کنید 😻
",
]);
}
//===
elseif($chat_id==$admin and  $data == "block"){
file_put_contents("data/$chat_id/step.txt","shar");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"لطفا ایدی فرد مورد نظر را ارسال کنید",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "shar"){
file_put_contents("data/$chat_id/step.txt", "none");
file_put_contents("data/$from_id/shar.txt","$text");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
ایدی $text از ربات بلاک شد
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$adduser = file_get_contents("data/blocklist.txt");
$adduser .= $text . "\n";
file_put_contents("data/blocklist.txt", $adduser);
$id11 = file_get_contents("data/$from_id/shar.txt");
bot('Sendmessage',[
'chat_id'=>$id11,
'text'=>"ارتباط شما با سرور ما قطع شد و نمیتوانید از بات استفاده کنید 😹",
]);
}
//===========//
elseif($chat_id==$admin and $data == 'adlist' ){
if(isset($list['admin'])){
$count = count($list['admin']);
$lastmem = null;
foreach($list['admin'] as $key => $value){
$lastmem .= "[$value](tg://user?id=$value)\n";}
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
لیست کامل ادمین های ربات به شرح ذیل می باشد :\n$lastmem
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}else{
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
لیست ادمین ها خالی میباشد !!!
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($data == 'addad' and $chat_id==$admin){
file_put_contents("data/$chat_id/step.txt", "addadmin");
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید  👨🏻‍🏫
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($data == 'delad' and $chat_id==$admin){
file_put_contents("data/$chat_id/step.txt", "deladmin");
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید  👨🏻‍🏫
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}

//============//
elseif($chat_id==$admin and $data == 'forall' ){
file_put_contents("data/$chat_id/step.txt","fortoall");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"پیام خود را فوروارد کنید  🌿",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == 'fortoall' ){
file_put_contents("data/$chat_id/step.txt","no");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما به تمامی کاربران ربات فرستاده شد 🌳",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$mem = fopen( "data/users.txt", 'r');
while(!feof($mem)){
$memuser = fgets($mem);
Forward($memuser, $chat_id,$message_id);
}
}
//============//
elseif($chat_id==$admin and $data == "pmall"){
file_put_contents("data/$chat_id/step.txt","pmh");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"پیام خود را ارسال کنید",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "pmh" ){
file_put_contents("data/$chat_id/step.txt","none");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما به تمامی کاربران ربات فرستاده شد 🌳",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$all_member = fopen( "data/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
SendMessage($user,$text,"html");
}
}
//============//
elseif($data == "limit"){
file_put_contents("data/$chat_id/step.txt","timelimit");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
محدودیت ارسال آگهی را به ثانیه وارد کنید !
مثلا 300
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "timelimit"){
file_put_contents("data/timelimit.txt",$text);
file_put_contents("data/$from_id/step.txt","no");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"محدودیت ارسال آگهی به $text ثانیه تنظیم شد ✅",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($data == "+cash"){
file_put_contents("data/$chat_id/step.txt","+cash1");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
👤 آیدی عددی کاربر را وارد کنید.
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "+cash1"){
file_put_contents("data/id.txt",$text);
file_put_contents("data/$from_id/step.txt","+cash2");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"💰 میزان شارژ را وارد کنید

توجه:
با افزودن منفی - پشت عدد موجودی کاهش میابد
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "+cash2"){
file_put_contents("data/$from_id/step.txt","none");
$idd = file_get_contents("data/id.txt");
$tbot1 = file_get_contents("data/$idd/cash.txt");
$tbot1 += $text;
file_put_contents("data/$idd/cash.txt", $tbot1);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"شارژ شد.
موجود جدید کاربر : $tbot1 تومن
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
bot('sendmessage',[
'chat_id'=>$idd,
'text'=>"تراکنش پذیرفته شد !
موجود جدید شما : $tbot1 تومن
",
]);
}
//===========//
elseif($chat_id==$admin and $data == "amar"){
$user = file_get_contents("data/users.txt");
$member_id = explode("\n",$user);
$member_count = count($member_id) -1;
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🎩 Amar ::: $member_count
",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($from_id == $admin and $text == "پنل"){
bot('sendmessage', [
'chat_id' =>$chat_id,
'text' =>"به پنل مدیریتی ربات خوش آمدید ⛳️",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'آمار 🔖' , 'callback_data'=>"amar"],['text'=>'محدودیت آگهی 〽️' , 'callback_data'=>"limit"]],
[['text'=>'پیام همگانی 📬' , 'callback_data'=>"pmall"],['text'=>'فورارد همگانی 🗃' , 'callback_data'=>"forall"]],
[['text'=>'بلاک کردن 🔫' , 'callback_data'=>"block"],['text'=>'آنبلاک کردن 🔪' , 'callback_data'=>"unblock"]],
[['text'=>'مدیر ها 💎' , 'callback_data'=>"adlist"],['text'=>'ارسال آگهی 💾' , 'callback_data'=>"post"]],
[['text'=>'👤 تغییر موجودی کاربر' , 'callback_data'=>"+cash"]],
[['text'=>'لیست کانال ها 🆑' , 'callback_data'=>"chhh11"],['text'=>'منوی اصلی 🔰' , 'callback_data'=>"back"]],
],
'resize_keyboard'=>true
])
]);
}
elseif($chat_id==$admin and $data == "2d"){
file_put_contents("data/$chat_id/step.txt", "none");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text' =>"به پنل مدیریتی ربات خوش آمدید ⛳️",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'آمار 🔖' , 'callback_data'=>"amar"],['text'=>'محدودیت آگهی 〽️' , 'callback_data'=>"limit"]],
[['text'=>'پیام همگانی 📬' , 'callback_data'=>"pmall"],['text'=>'فورارد همگانی 🗃' , 'callback_data'=>"forall"]],
[['text'=>'بلاک کردن 🔫' , 'callback_data'=>"block"],['text'=>'آنبلاک کردن 🔪' , 'callback_data'=>"unblock"]],
[['text'=>'مدیر ها 💎' , 'callback_data'=>"adlist"],['text'=>'ارسال آگهی 💾' , 'callback_data'=>"post"]],
[['text'=>'👤 تغییر موجودی کاربر' , 'callback_data'=>"+cash"]],
[['text'=>'لیست کانال ها 🆑' , 'callback_data'=>"chhh11"],['text'=>'منوی اصلی 🔰' , 'callback_data'=>"back"]],
],
'resize_keyboard'=>true
])
]);
}
//===========//
elseif($data=="post"){
file_put_contents("data/$chat_id/step.txt","nozula1");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🅿️آگهی خود را در قالب #متن بفرستید!
",
'reply_to_message_id'=>$message_id,
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "nozula1"){
file_put_contents("data/$from_id/nozula1.txt","$text");
file_put_contents("data/$from_id/step.txt","no");
$nozula1 = file_get_contents("data/$from_id/nozula1.txt");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
$nozula1
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"تایید ✅",'callback_data'=>"yes78|$from_id"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif(strpos($data,"yes78|" ) !== false ){
$exit = explode("|",$data);
$key = $exit[1];
$nozula12 = file_get_contents("data/$key/nozula1.txt");
file_put_contents("data/$from_id/step.txt","no");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
$nozula12
➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖
پیام مورد نظر به تمامی کانال ها ارسال شد ✅
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙",'callback_data'=>"back"]],
],
'resize_keyboard'=>true,
])
]);
bot('Sendmessage',[
'chat_id'=>$channelid,
'text'=>"
$nozula12
➖➖➖➖➖➖
🆔@account_sell_fwbot
",
'parse_mode'=>"HTML",
]);
}

elseif($data=="chhh11"){
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
لیست کانال های ربات آگهی به شرح زیر میباشد!

$channel
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"برگشت 🔙", 'callback_data'=>"2d"]],
]
])
]);
}
elseif($chat_id==$admin and $data == "unblock"){
file_put_contents("data/$chat_id/step.txt","sharr");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"لطفا ایدی عددی کاربر مورد نظر رو ارسال کنید",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "sharr"){
file_put_contents("data/$chat_id/step.txt", "none");
$newlist = str_replace($text, "", $blocklist);
file_put_contents("data/blocklist.txt", $newlist);
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
خب ایدی $text از بلاکی درآمد 😎
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
bot('Sendmessage',[
'chat_id'=>$text,
'text'=>"
ارتباط شما با سرور برقرار شد و میتوانید از بات استفاده کنید 😻
",
]);
}
//===
elseif($chat_id==$admin and  $data == "block"){
file_put_contents("data/$chat_id/step.txt","shar");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"لطفا ایدی فرد مورد نظر را ارسال کنید",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "shar"){
file_put_contents("data/$chat_id/step.txt", "none");
file_put_contents("data/$from_id/shar.txt","$text");
bot('Sendmessage',[
'chat_id'=>$chat_id,
'text'=>"
ایدی $text از ربات بلاک شد
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$adduser = file_get_contents("data/blocklist.txt");
$adduser .= $text . "\n";
file_put_contents("data/blocklist.txt", $adduser);
$id11 = file_get_contents("data/$from_id/shar.txt");
bot('Sendmessage',[
'chat_id'=>$id11,
'text'=>"ارتباط شما با سرور ما قطع شد و نمیتوانید از بات استفاده کنید 😹",
]);
}
//===========//
elseif($chat_id==$admin and $data == 'adlist' ){
if(isset($list['admin'])){
$count = count($list['admin']);
$lastmem = null;
foreach($list['admin'] as $key => $value){
$lastmem .= "[$value](tg://user?id=$value)\n";}
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
لیست کامل ادمین های ربات به شرح ذیل می باشد :\n$lastmem
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}else{
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
لیست ادمین ها خالی میباشد !!!
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
}
elseif($data == 'addad' and $chat_id==$admin){
file_put_contents("data/$chat_id/step.txt", "addadmin");
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید  👨🏻‍🏫
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($data == 'delad' and $chat_id==$admin){
file_put_contents("data/$chat_id/step.txt", "deladmin");
bot('editMessagetext',[   
'chat_id'=>$chat_id,    
'message_id'=>$message_id,
'text'=>"
آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید  👨🏻‍🏫
",   
'parse_mode'=>'MarkDown',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}

//============//
elseif($chat_id==$admin and $data == 'forall' ){
file_put_contents("data/$chat_id/step.txt","fortoall");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"پیام خود را فوروارد کنید  🌿",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == 'fortoall' ){
file_put_contents("data/$chat_id/step.txt","no");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما به تمامی کاربران ربات فرستاده شد 🌳",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$mem = fopen( "data/users.txt", 'r');
while(!feof($mem)){
$memuser = fgets($mem);
Forward($memuser, $chat_id,$message_id);
}
}
//============//
elseif($chat_id==$admin and $data == "pmall"){
file_put_contents("data/$chat_id/step.txt","pmh");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"پیام خود را ارسال کنید",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "pmh" ){
file_put_contents("data/$chat_id/step.txt","none");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما به تمامی کاربران ربات فرستاده شد 🌳",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
$all_member = fopen( "data/users.txt", "r");
while( !feof( $all_member)){
$user = fgets( $all_member);
SendMessage($user,$text,"html");
}
}
//============//
elseif($data == "limit"){
file_put_contents("data/$chat_id/step.txt","timelimit");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
محدودیت ارسال آگهی را به ثانیه وارد کنید !
مثلا 300
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "timelimit"){
file_put_contents("data/timelimit.txt",$text);
file_put_contents("data/$from_id/step.txt","no");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"محدودیت ارسال آگهی به $text ثانیه تنظیم شد ✅",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($data == "+cash"){
file_put_contents("data/$chat_id/step.txt","+cash1");
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
👤 آیدی عددی کاربر را وارد کنید.
",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "+cash1"){
file_put_contents("data/id.txt",$text);
file_put_contents("data/$from_id/step.txt","+cash2");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"💰 میزان شارژ را وارد کنید

توجه:
با افزودن منفی - پشت عدد موجودی کاهش میابد
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}
elseif($step == "+cash2"){
file_put_contents("data/$from_id/step.txt","none");
$idd = file_get_contents("data/id.txt");
$tbot1 = file_get_contents("data/$idd/cash.txt");
$tbot1 += $text;
file_put_contents("data/$idd/cash.txt", $tbot1);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"شارژ شد.
موجود جدید کاربر : $tbot1 تومن
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
bot('sendmessage',[
'chat_id'=>$idd,
'text'=>"تراکنش پذیرفته شد !
موجود جدید شما : $tbot1 تومن
",
]);
}
//===========//
elseif($chat_id==$admin and $data == "amar"){
$user = file_get_contents("data/users.txt");
$member_id = explode("\n",$user);
$member_count = count($member_id) -1;
bot('editMessagetext',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
🎩 Amar ::: $member_count
",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'برگشت ↩️', 'callback_data'=>"2d"]],
],
'resize_keyboard'=>true,
])
]);
}

?> 