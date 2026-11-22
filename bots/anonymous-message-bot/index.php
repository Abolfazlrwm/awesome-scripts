<?php
//===امنیت===//
// Dev = Dr Amir | t.me/IFIED
// برای پیدا کردن مکان هایی که باید ادیت شوند کافیست کلمه Editing را سرچ کنید
// # source kdae  :) 
# @Sourrce_kade
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
if (!$ok){
die("Robot Is Online."); 
exit();
}
flush();
ob_start();
ob_implicit_flush(1);
ini_set( "expose_php","Off" );
ini_set( "Allow_url_fopen","Off" );
ini_set( "disable_functions","exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,eval,file,file_get_contents,file_put_contents,fclose,fopen,fwrite,mkdir,rmdir,unlink,glob,echo,die,exit,print,scandir" );
ini_set( "max_execution_time","25" );
ini_set( "max_input_time","25" );
ini_set( "memory_limit","15M" );
ini_set( "file_uploads","Off" );
ini_set( "post_max_size","10k" );
error_reporting(0);
ini_set( "log_errors","Off" );
ignore_user_abort(true);
//===امنیت===//
//===تایم زون===//
date_default_timezone_set('Asia/Tehran');
//===تایم زون===//
//===توکن===//
$token = ""; #توکن ادیت کنید Editing
define('API_KEY',"$token");
//===توکن===//
//-----------------------------------------------------------------------------------\\
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
#-
function SendMessage($chat_id,$text,$keyboard = null) {
    bot('sendmessage',[
        'chat_id'     =>     $chat_id,
        'text'     =>     $text,
        'parse_mode'     =>     "HTML",
        'disable_web_page_preview'     =>     true,
        'reply_markup'     =>     $keyboard
    ]);
}
#-
function SendPhoto($chat_id,$photo,$caption){
	bot('SendPhoto',[
	'chat_id'=>$chat_id,
	'photo'=>$photo,
	'caption'=>$caption,
	]);
	}
#-
function SendAudio($chat_id,$audio,$caption){
	bot('SendAudio',[
	'chat_id'=>$chat_id,
	'audio'=>$audio,
	'caption'=>$caption,
	]);
	}
#-
function SendDocument($chat_id,$document,$caption){
	bot('SendDocument',[
	'chat_id'=>$chat_id,
	'document'=>$document,
	'caption'=>$caption,
	]);
	}
#-
function SendSticker($chat_id,$sticker){
	bot('SendSticker',[
	'chat_id'=>$chat_id,
	'sticker'=>$sticker,
	]);
	}
#-
function SendVideo($chat_id,$video,$caption){
	bot('SendVideo',[
	'chat_id'=>$chat_id,
	'video'=>$video,
	'caption'=>$caption,
	]);
	}
#-
function SendVoice($chat_id,$voice,$caption){
	bot('SendVoice',[
	'chat_id'=>$chat_id,
	'voice'=>$voice,
	'caption'=>$caption,
	]);
	}
#-
function Forward($KojaShe, $AzKoja, $KodomMSG)
{
    bot('ForwardMessage', [
        'chat_id' => $KojaShe,
        'from_chat_id' => $AzKoja,
        'message_id' => $KodomMSG
    ]);
}
#-
function getlink($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars), 0, $length);
}
#-
function deleteFolder($path){
if(is_dir($path) === true){
$files = array_diff(scandir($path), array('.', '..'));
foreach ($files as $file)
deleteFolder(realpath($path) . '/' . $file);
return rmdir($path);
}else if (is_file($path) === true)
return unlink($path);
return false;
}
//-----------------------------------------------------------------------------------\\
$update = json_decode(file_get_contents("php://input"));
$Dev = ""; #آیدی عددی ادمین Editing
$botid = ""; #آیدی ربات بدون @ Editing
$tab = ""; #تبلیغ Editing
$namebot = ""; #اسم ربات Editing
$channel1 = ""; #آیدی کانال قفلی بدون @ Editing
$channel2 = ""; #آیدی کانال قفلی بدون @ Editing
//================================\\
//==============
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
//================
 $caption = $update->message->caption;
 $photo = $update->message->photo;
 $photo_id = $photo[count($photo)-1]->file_id;
 $sticker_id = $update->message->sticker->file_id;
 $video_id = $update->message->video->file_id;
 $voice_id = $update->message->voice->file_id;
 $file_id = $update->message->document->file_id;
 $music_id = $update->message->audio->file_id;
 $bot = json_decode(file_get_contents(".original_data/$chat_id/$chat_id.json"),true);
 $blockuser = file_get_contents(".original_data/$chat_id/block.txt");
 $vbot = file_get_contents(".original_data/vbot.txt");
 $user_flood = $bot["flood"];
 $step = $bot["step"];
 $idd = $bot["idd"];
 $vs = $bot["vs"];
 $dr = $bot["dr"];
 $chn = json_decode(file_get_contents(".original_data/$idd.json"),true);
 $chn["name"] = "$first_name";
 $outjson = json_encode($chn,true);
 file_put_contents(".original_data/$idd.json",$outjson);
 $truechannel1 = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=@$channel1&user_id=$chat_id"));
$tch1 = $truechannel1->result->status;
$truechannel2 = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=@$channel2&user_id=$chat_id"));
$tch2 = $truechannel2->result->status;
//================================\\
if($tch1 != 'member' && $tch1 != 'creator' && $tch1 != 'administrator'){
if($tch2 != 'member' && $tch2 != 'creator' && $tch2 != 'administrator'){
bot("sendmessage",[
"chat_id"=>$chat_id,
"text" => "کاربر عزیز ، برای استفاده از « $namebot » حتما باید در کانال زیر عضو بشی 👇

🔹 @$channel1  🔹 @$channel1
🔹 @$channel1   🔹 @$channel2

بعد از عضـویت در کانال روی « ✅ تایید عضویت » بزنید تا ربات برای شما فعال شود. 👇",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[['text'=>"✅ تایید عضویت",'callback_data'=>"go"]],
],
])
]);
exit();
}
}
//=================
if(!is_dir(".original_data")){
mkdir('.original_data');
file_put_contents(".original_data/vbot.txt",on);
bot('sendmessage',[
'chat_id'=>$Dev, 
'text' => "ربات با موفقیت فعال شد!
Dr Amir | @IFIED",
'parse_mode' => "MarkDown"
]);
}
if(!is_dir(".original_data/$chat_id")){
	mkdir(".original_data/$chat_id");
$idlink = getlink(20);
$bot["idd"] = "$idlink";
$bot["step"] = "free";
$bot["vs"] = "unban";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
$s = json_decode(file_get_contents(".original_data/$idlink.json"),true);
$fn = $update->message->from->first_name;
$s["id"] = "$from_id";
$s["name"] = "$fn";
$outjson = json_encode($s,true);
file_put_contents(".original_data/$idlink.json",$outjson);
$add_user = file_get_contents('.original_data/allmembers.txt');
$add_user .= $from_id . "\n";
file_put_contents('.original_data/allmembers.txt', $add_user);
}
//=================
if($vs == "ban"){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ از طرف مدیریت مسدود شدی!

[$tab]
",
'parse_mode' => "html",
]);    
exit();
}

if($vbot == "off" and $from_id != $Dev){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"🤖 ربات از طرف مدیریت خاموش شده است!

[$tab]
",
'parse_mode' => "html",
]);    
exit();
}
if($user_flood < time()){ 

$tf = time() + 1;
$bot["flood"] = "$tf";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);

if($text == "/start" or $data == "go"){
 $bot["step"] = "free";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"حله!

چجوری می تونم کمکت کنم؟

[$tab]
",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
exit();
}
if($data == "back"){
 $bot["step"] = "free";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
'text'=>"حله!

چجوری می تونم کمکت کنم؟

[$tab]
",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
exit();
}
if($data == "i"){
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"[📚] راهنما ربات :
        
برای دریافت پیام ناشناس کافیه دستور 👈🏻 /link رو لمس کنی تا لینک اختصاصیت برات ارسال بشه. با فرستادن این لینک به دوستات و گروه‌ها یا با گذاشتن در شبکه‌های اجتماعی مثل فیس‌بوک و توییتر دوستات میتونن حرفی که تو دلشونه رو به صورت ناشناس بهت بزنن. یک متن پیش‌فرض همراه لینک بهت فرستاده میشه که بتونی راحت همون پیام رو فوروارد کنی.

--------------------------------------------------

[📚] راهنما دکمه ها :

✍️ پاسخ : برای پاسخ به پیام کاربر استفاده کنید.
⛔ بلاک : برای مسدود کردن کاربر استفاده کنید.
🔓 آزاد سازی : برای رفع مسدودیت کاربر استفاده کنید.
 
--------------------------------------------------

👨‍💻 Developer : @IFIED
",
         'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
exit();
}
if($data == "mylink"){ 
        bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"سلام $first_name هستم ✋️

لینک زیر رو لمس کن و هر حرفی که تو دلت هست یا هر انتقادی که نسبت به من داری رو با خیال راحت بنویس و بفرست. بدون اینکه از اسمت باخبر بشم پیامت به من می‌رسه. خودتم می‌تونی امتحان کنی و از بقیه بخوای راحت و ناشناس بهت پیام بفرستن، حرفای خیلی جالبی می‌شنوی! 😉

👇👇
https://t.me/$botid?start=$idd",
         'parse_mode' => "markdown",
]);    
if($dr == "0"){
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[✅] فعال کردن لینک",'callback_data'=>"onlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}else{
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[❌] خاموش کردن لینک",'callback_data'=>"offlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}
exit();
}
if($text == "/link"){ 
        bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"سلام $first_name هستم ✋️

لینک زیر رو لمس کن و هر حرفی که تو دلت هست یا هر انتقادی که نسبت به من داری رو با خیال راحت بنویس و بفرست. بدون اینکه از اسمت باخبر بشم پیامت به من می‌رسه. خودتم می‌تونی امتحان کنی و از بقیه بخوای راحت و ناشناس بهت پیام بفرستن، حرفای خیلی جالبی می‌شنوی! 😉

👇👇
https://t.me/$botid?start=$idd",
         'parse_mode' => "markdown",
]);    
if($dr == "0"){
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[✅] فعال کردن لینک",'callback_data'=>"onlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}else{
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[❌] خاموش کردن لینک",'callback_data'=>"offlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}
exit();
}
if($data == "onlink"){
$bot["dr"] = "1";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[❌] خاموش کردن لینک",'callback_data'=>"offlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}
if($data == "offlink"){
$bot["dr"] = "0";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"☝️ این پیام رو به دوستات و گروه‌هایی که می‌شناسی فـوروارد کن یا لـینک داخلش رو تو شبکه‌های اجتماعی بذار و توئیت کن، تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"[✅] فعال کردن لینک",'callback_data'=>"onlink"]
        ],
        [
        ['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
        ],
]
])
]);    
}
if($data == "SU"){
 bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
'text'=>"👮‍♂️ پیام خود را در قالب یک متن ارسال کنید :",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
],
],
])
]);    

$bot["step"] = "sup0";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);

}

if($step == 'sup0' and $data != 'menu'){ 
if($text == null){
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"‌⚠️ تنها پیام متنی مجاز است!",
'parse_mode' => "html",
]);    
exit();
}
//====Send Admin
bot('sendmessage',[
'chat_id'=>$Dev,
'text'=>"‌👤 یک پیام از طرف کاربر `$chat_id` به مدیریت :

$text",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"📨 پاسخ",'callback_data'=>"/c_$from_id"]
],
]
]) 
]);    
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);

bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"✅ با موفقیت ارسال شد!",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔙] بازگشت",'callback_data'=>"back"]
],
],
])
]);    
}
if(strpos($text,"/start ") !== false or strpos($data,"/start ") !== false){
if(strpos($text,"/start ") !== false){
$ID = str_replace("/start ","",$text);
}elseif(strpos($data,"/start ") !== false){
$ID = str_replace("/start ","",$data);
}
if($idd == $ID){
        bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text'=>"اینکه آدم گاهی با خودش حرف بزنه خوبه ، ولی اینجا نمیتونی به خودت پیام ناشناس بفرستی ! :)

چجوری می تونم کمکت کنم؟

[$tab]
",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
}else{
$sr = json_decode(file_get_contents(".original_data/$ID.json"),true);
 $fm = $sr["name"];
 $id = $sr["id"];
 $block = file_get_contents(".original_data/$id/block.txt");
if($id == null){
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"❌ لینک اشتباه است!",
        'parse_mode' => "markdown",
]);    
}else{
if(strpos($block, "$chat_id") !== false){
bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"ببخشید! مخاطب پیام بلاکت کرده.

چجوری می تونم کمکت کنم؟",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
}else{
$vr = json_decode(file_get_contents(".original_data/$id/$id.json"),true);
$dr = $vr["dr"]; 
if($dr == "0"){
bot('sendmessage',[ 
'chat_id'=>$chat_id,
'text'=>"‌🤷 این کاربر فعلا نمیخواد پیامی دریافت کنه",
'parse_mode' => "html",
]);    
exit();
}
 $bot["step"] = "sendch_$ID";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
	    bot('sendmessage',[
        'chat_id'=>$chat_id, 
        'text'=>"در حال ارسال پیام ناشناس به $fm هستی.

می‌تونی هر حرف یا انتقادی که تو دلت هست رو بگی چون پیامت به صورت کاملا ناشناس ارسال می‌شه!",
        'parse_mode' => "markdown",
        'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"انصراف",'callback_data'=>"back"]
        ],
        ]
])
]);    
exit();
}
}
}
}
if(strpos($step,"sendch_") !== false and $data != "back" and $text != "/start"){
if (isset($message->text)) {
        $file_type = "text";
        $file_typeFa = "متن";
        $file_function = "sendmessage";
    } elseif (isset($message->document)) {
        $file_type = "document";
        $file_typeFa = "فایل";
        $file_function = "senddocument";
    } elseif (isset($message->video)) {
        $file_type = "video";
        $file_typeFa = "ویدیو";
        $file_function = "sendvideo";
    } elseif (isset($message->photo)) {
        $file_type = "photo";
        $file_typeFa = "عکس";
        $file_function = "sendphoto";
    } elseif (isset($message->sticker)) {
        $file_type = "sticker";
        $file_typeFa = "استیکر";
        $file_function = "sendsticker";
    } elseif (isset($message->audio)) {
        $file_type = "audio";
        $file_typeFa = "آهنگ";
        $file_function = "sendaudio";
    } elseif (isset($message->voice)) {
        $file_type = "voice";
        $file_typeFa = "ویس";
        $file_function = "sendvoice";
    }
if ($file_type == "text")  $file_id = $text; elseif($file_type != "photo") $file_id = $message->$file_type->file_id;
    elseif ($file_type == "photo")  $file_id = $message->photo[0]->file_id; 
    $random = getlink(16);
 $ID = str_replace("sendch_","",$step);
 $sr = json_decode(file_get_contents(".original_data/$ID.json"),true);
 $send = $sr["id"];
 $bot["step"] = "free";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
 if(!is_dir(".original_data/$send/new")){
mkdir(".original_data/$send/new");
}
   $base = "$from_id,$message_id,$file_type,$file_function,$file_id,$caption";
file_put_contents(".original_data/$send/new/$random",$base);
 bot('sendmessage',[
'chat_id'=>$send,
'text'=>"‌📬 شما یک پیام ناشناس جدید دارید !

جهت دریافت کلیک کنید 👈 /newmsg",
'parse_mode' => "html",
]);    
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما ارسال شد 😊

چجوری می تونم کمکت کنم؟

[$tab]
",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
}
if(strpos($data,"s_") !== false){
 $re = str_replace("s_","",$data);
 $ID = explode(",",$re)[0];
 $ms = explode(",",$re)[1];
 $block = file_get_contents(".original_data/$ID/block.txt");
if(strpos($block, "$chat_id") !== false){
bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "ببخشید! مخاطب پیام بلاکت کرده.",
            'show_alert' => true
        ]);
}else{
$vr = json_decode(file_get_contents(".original_data/$ID/$ID.json"),true);
$dr = $vr["dr"]; 
if($dr == "0"){
bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "🤷 این کاربر فعلا نمیخواد پیامی دریافت کنه",
            'show_alert' => true
        ]);
exit(); 
}
 $bot["step"] = "sn_$re";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
 bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"☝️ در حال پاسخ دادن به فرستنده این پیام هستی ... ؛ منتظریم بفرستی :)",
'reply_to_message_id'=>$message_id,
'parse_mode' => "html",
        'reply_markup'=> json_encode(['inline_keyboard' => [
        [
        ['text'=>"انصراف",'callback_data'=>"back"]
        ],
        ]
])
]);    
exit();
}
}
if(strpos($step,"sn_") !== false and $data != "back"){
	if (isset($message->text)) {
        $file_type = "text";
        $file_typeFa = "متن";
        $file_function = "sendmessage";
    } elseif (isset($message->document)) {
        $file_type = "document";
        $file_typeFa = "فایل";
        $file_function = "senddocument";
    } elseif (isset($message->video)) {
        $file_type = "video";
        $file_typeFa = "ویدیو";
        $file_function = "sendvideo";
    } elseif (isset($message->photo)) {
        $file_type = "photo";
        $file_typeFa = "عکس";
        $file_function = "sendphoto";
    } elseif (isset($message->sticker)) {
        $file_type = "sticker";
        $file_typeFa = "استیکر";
        $file_function = "sendsticker";
    } elseif (isset($message->audio)) {
        $file_type = "audio";
        $file_typeFa = "آهنگ";
        $file_function = "sendaudio";
    } elseif (isset($message->voice)) {
        $file_type = "voice";
        $file_typeFa = "ویس";
        $file_function = "sendvoice";
    }
if ($file_type == "text")  $file_id = $text; elseif($file_type != "photo") $file_id = $message->$file_type->file_id;
    elseif ($file_type == "photo")  $file_id = $message->photo[0]->file_id; 
    $random = getlink(16);
 $re = str_replace("sn_","",$step);
 $ID = explode(",",$re)[0];
 $ms = explode(",",$re)[1];
 $bot["step"] = "free";
 $outjson = json_encode($bot,true);
 file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
 if(!is_dir(".original_data/$ID/new")){
mkdir(".original_data/$ID/new");
} 
    $base = "$from_id,$message_id,$file_type,$file_function,$file_id,$caption,$ms";
file_put_contents(".original_data/$ID/new/$random",$base);
bot('sendmessage',[
'chat_id'=>$ID,
'text'=>"‌📬 شما یک پیام ناشناس جدید دارید !

جهت دریافت کلیک کنید 👈 /newmsg",
'parse_mode' => "html",
]);    
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"پیام شما ارسال شد 😊

چجوری می تونم کمکت کنم؟

[$tab]
",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[🔗] لینک من",'callback_data'=>"mylink"]
],
[
['text'=>"[👮‍♂️] پشتیبانی",'callback_data'=>"SU"],['text'=>"[📚] راهنما",'callback_data'=>"i"]
],
]
])
]);    
}
$hh = json_encode(['inline_keyboard' => [
[
['text'=>"🔓 آزاد سازی",'callback_data'=>"u_$ID,$ms"],
['text'=>"✍️ پاسخ",'callback_data'=>"s_$ID"]
],
]
]);
if(strpos($data,"b_") !== false){
$re = str_replace("b_","",$data);
$bbb = file_get_contents(".original_data/$chat_id/block.txt");
if(strpos($bbb, "$re") !== false){
$un = str_replace("$re","",$blockuser);
file_put_contents(".original_data/$chat_id/block.txt",$un);
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "با موفقیت آزاد شد",
            'show_alert' => true
]);    
} elseif(strpos($bbb, "$re") == false){
$ban = fopen(".original_data/$chat_id/block.txt", "a") or die("Unable to open file!");
 fwrite($ban,"$re \n");
 fclose($ban);
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "با موفقیت بلاک شد",
            'show_alert' => true,
]); 
}
}
if(!is_dir(".original_data/$chat_id/new") and $text == "/newmsg"){
bot('sendmessage', [
'chat_id' => $chat_id,
'text' => "پیام نخونده‌ای نداری !

چطوره با زدن این دستور 👈 /link لینک خودت رو بگیری و به دوستات یا گروه‌ها بفرستی تا بتونند بهت پیام ناشناس بفرستند؟ 😊",
'parse_mode' => "MarkDown",
]);    
} 
if($text == "/newmsg"){
$scenssir = scandir(".original_data/$chat_id/new");
foreach($scenssir as $ree){
$re = file_get_contents(".original_data/$chat_id/new/$ree");
 $WID = explode(",",$re)[0];
 $meid = explode(",",$re)[1];
 $file_type = explode(",",$re)[2];
 $file_function = explode(",",$re)[3];
 $file_id = explode(",",$re)[4];
 $cap = explode(",",$re)[5];
 $rp = explode(",",$re)[6];
 bot('sendmessage',[
'chat_id'=>$WID,
'text'=>"‌این پیامت ☝️ رو دید!",
'reply_to_message_id'=>$meid,
]);   
if($rp == null){
if($file_type != "text"){
bot("$file_function",[
	'chat_id'=>$chat_id,
	"$file_type"=>$file_id,
	'caption'=>$cap,
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"⛔ بلاک / 🔓 آزاد سازی",'callback_data'=>"b_$WID"],
['text'=>"✍️ پاسخ",'callback_data'=>"s_$WID,$meid"]
],
]
])
]);    
}elseif($file_type == "text"){
bot('sendmessage', [
'chat_id' => $chat_id,
'text' => "‌                                                 ‌ ‌‌‌    ‌
$file_id",
'parse_mode' => "MarkDown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"⛔ بلاک / 🔓 آزاد سازی",'callback_data'=>"b_$WID"],
['text'=>"✍️ پاسخ",'callback_data'=>"s_$WID,$meid"]
],
]
])
]);    
}
}else{
if($file_type != "text"){
bot("$file_function",[
	'chat_id'=>$chat_id,
	"$file_type"=>$file_id,
	'caption'=>$cap,
	'reply_to_message_id'=>$rp,
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"⛔ بلاک / 🔓 آزاد سازی",'callback_data'=>"b_$WID"],
['text'=>"✍️ پاسخ",'callback_data'=>"s_$WID,$meid"]
],
]
])
]);    
}elseif($file_type == "text"){
bot('sendmessage', [
'chat_id' => $chat_id,
'text' => "                                                 ‌ ‌‌‌    ‌
$file_id",
'parse_mode' => "MarkDown",
'reply_to_message_id'=>$rp,
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"⛔ بلاک / 🔓 آزاد سازی",'callback_data'=>"b_$WID"],
['text'=>"✍️ پاسخ",'callback_data'=>"s_$WID,$meid"]
],
]
])
]);    
}
}
}
}
if($text == "/newmsg"){
deletefolder(".original_data/$chat_id/new");
}
//===================
if($from_id == $Dev){
if($text == "/panel") {
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage', [
'chat_id' => $chat_id,
'text' => "👮‍♂️ Welcome to the admin PANEL",
'parse_mode' => "MarkDown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[📦] آمار",'callback_data'=>"amar"]
],
[
['text'=>"[📡] فوروارد همگانی",'callback_data'=>"fs"],
['text'=>"[📰] پیام همگانی",'callback_data'=>"sends"]
],
[
['text'=>"[👤] تغییر وضعیت کاربر",'callback_data'=>"tvr"]
],
]
])
]);    
} 
if($data == "amar"){
if($vbot == "off"){
$e = "Off"; 
}elseif($vbot == "on"){
$e = "On";
}
$datab = file_get_contents(".original_data/allmembers.txt");
$member_id = explode("\n", $datab);
$member_c = count($member_id) - 1;
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text' => "🔥 | About the robot :

🤖 Robot Status : $e
🔧 Robot Version : 3-End
👨‍💻 Developer : @IFIED
👥 Users Statistic : $member_c
Date Of Release : 2023/9/1",
        'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🧑‍🔧 تغییر وضعیت ربات",'callback_data'=>"tagh"]
],
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($data == "tagh"){
if($vbot == "off"){
$e = "On";
file_put_contents(".original_data/vbot.txt",on);
}elseif($vbot == "on"){
$e = "Off";
file_put_contents(".original_data/vbot.txt",off);
}elseif($vbot == null){
$e = "On";
file_put_contents(".original_data/vbot.txt",on);
}
$datab = file_get_contents(".original_data/allmembers.txt");
$member_id = explode("\n", $datab);
$member_c = count($member_id) - 1;
bot('editmessagetext',[
        'chat_id'=>$chat_id, 
        'message_id' => $message_id,
        'text' => "🔥 | About the robot :

🤖 Robot Status : $e
🔧 Robot Version : 3-End
👨‍💻 Developer : @IFIED
👥 Users Statistic : $member_c
Date Of Release : 2023/9/1",
        'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🧑‍🔧 تغییر وضعیت ربات",'callback_data'=>"tagh"]
],
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($data == "sends") {
$bot["step"] = "sn";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$chat_id, 
'message_id' => $message_id,
'text' => "📝 پیام مورد نظر برام بفرست",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($step == "sn" and $data != "backp") {
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
$ui = fopen(".original_data/allmembers.txt", 'r');
 while (!feof($ui)) {
    $uid = fgets($ui);
            
   if($sticker_id != null){
   bot('SendSticker',[
	'chat_id'=>$uid,
	'sticker'=>$sticker_id, 
	]);
	}
	
    elseif($photo_id != null){
    bot('SendPhoto',[
	'chat_id'=>$uid,
	'photo'=>$photo_id,
	'caption'=>$caption,
	]);
	}
	
	elseif($video_id != null){
    bot('SendVideo',[
	'chat_id'=>$uid,
	'video'=>$video_id,
	'caption'=>$caption,
	]);
	}
	
    elseif($voice_id != null){
    bot('SendVoice',[
	'chat_id'=>$uid,
	'voice'=>$voice_id,
	'caption'=>$caption,
	]);
	}
	
	elseif($file_id != null){
    bot('SendDocument',[
	'chat_id'=>$uid,
	'document'=>$file_id,
	'caption'=>$caption,
	]);
	}
	
	elseif($music_id != null){
    bot('SendAudio',[
	'chat_id'=>$uid,
	'audio'=>$music_id,
	'caption'=>$caption,
	]);
	}
	
	elseif($text != null){
    bot('sendmessage',[
	'chat_id'=>$uid,
	'text'=>$text,
	]);
}
}
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"✅ | با موفقیت انجام شد!",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($data == "fs") {
$bot["step"] = "fs";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$chat_id, 
'message_id' => $message_id,
'text' => "☄️ پیام مورد نظر برام فوروارد کن",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($step == 'fs' and $data != "backp"){
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
$foru = fopen(".original_data/allmembers.txt", 'r');
while(!feof($foru)){
$fu = fgets($foru);
Forward($fu, $chat_id, $message_id);
}
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"✅ | با موفقیت انجام شد!",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($data == "tvr") {
$bot["step"] = "tvr";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$chat_id, 
'message_id' => $message_id,
'text' => "👤 آیدی کاربر مورد نظر را وارد کن",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if($step == "tvr" and $data != "backp"){
 $tvv = json_decode(file_get_contents(".original_data/$text/$text.json"),true);
 $blockvv = file_get_contents(".original_data/$text/block.txt");
 $linku = $tvv["idd"];
 $vu = $tvv["vs"];
if($linku == null){
 bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"❌ همچین کاربری ربات استارت نکرده ",
'parse_mode' => "markdown",
]);    
exit();
}
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
if($vu != "ban"){
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $text
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"❌ مسدود کردن",'callback_data'=>"ban_$text"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$text"]
],
]
])
]); 
}elseif($vu == "ban"){
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $text
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"✅ آزادسازی",'callback_data'=>"uban_$text"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$text"]
],
]
])
]); 
}
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"✅ | اطلاعات با موفقیت ارسال شد!",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙",'callback_data'=>"backp"]
],
]
])
]);    
}
if(strpos($data,"backu_") !== false){
$re = str_replace("backu_","",$data);
 $tvv = json_decode(file_get_contents(".original_data/$re/$re.json"),true);
 $blockvgv = file_get_contents(".original_data/$re/block.txt");
 $linku = $tvv["idd"];
 $vu = $tvv["vs"];
if($linku == null){
 bot('sendmessage',[
'chat_id'=>$chat_id, 
'text'=>"❌ همچین کاربری ربات استارت نکرده ",
'parse_mode' => "markdown", 
]);    
exit();
}
if($vu != "ban"){
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $re
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvgv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"❌ مسدود کردن",'callback_data'=>"ban_$re"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$re"]
],
]
])
]); 
}elseif($vu == "ban"){
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $re
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvgv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"✅ آزادسازی",'callback_data'=>"uban_$re"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$re"]
],
]
])
]); 
}
}
if(strpos($data,"ban_") !== false){
$re = str_replace("ban_","",$data);
$tvv = json_decode(file_get_contents(".original_data/$re/$re.json"),true);
$tvv["vs"] = "ban";
$outjson = json_encode($tvv,true);
file_put_contents(".original_data/$re/$re.json",$outjson);
$blockvv = file_get_contents(".original_data/$re/block.txt");
 $linku = $tvv["idd"];
 $vu = $tvv["vs"];
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $re
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"✅ آزادسازی",'callback_data'=>"uban_$re"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$re"]
],
]
])
]); 
}
if(strpos($data,"uban_") !== false){
$re = str_replace("uban_","",$data);
$tvv["vs"] = "unban";
$outjson = json_encode($tvv,true);
file_put_contents(".original_data/$re/$re.json",$outjson);
$blockvv = file_get_contents(".original_data/$re/block.txt");
 $linku = $tvv["idd"];
 $vu = $tvv["vs"];
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text'=>"👤 اطلاعات کاربر به شرح زیر است :

🆔 آیدی عددی : $re
🔗 لینک ناشناس کاربر : $linku
🏷️ وضعیت کاربر : $vu
👥 کاربرانی که این شخص بلاک کرده :
$blockvv
",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"❌ مسدود کردن",'callback_data'=>"ban_$re"]
],
[
['text'=>"🔧 تغییر لیست بلاکی",'callback_data'=>"tn_$re"]
],
]
])
]); 
}
if(strpos($data,"tn_") !== false){
$rec = str_replace("tn_","",$data);
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text'=>"🔧 چه تغییری میخوای بدی؟",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"📤 حذف کاربر",'callback_data'=>"huu_$rec"],
['text'=>"📥 افزودن کاربر",'callback_data'=>"auu_$rec"]
],
[
['text'=>"🔙 منو کاربر",'callback_data'=>"backu_$rec"]
],
]
])
]); 
}
if(strpos($data,"auu_") !== false){
$re = str_replace("auu_","",$data);
$bot["step"] = "au_$re";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$chat_id, 
'message_id' => $message_id,
'text' => "👤 آیدی کاربری که میخوای اضافه کنی بفرست",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙 منو کاربر",'callback_data'=>"backu_$re"]
],
]
])
]);    
}
if(strpos($data,"huu_") !== false){
$re = str_replace("huu_","",$data);
$bot["step"] = "hu_$re";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext',[
'chat_id'=>$chat_id, 
'message_id' => $message_id,
'text' => "👤 آیدی کاربری که میخوای حذف کنی بفرست",
'parse_mode' => "html",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"🔙 منو کاربر",'callback_data'=>"backu_$re"]
],
]
])
]);    
} 
if(strpos($step,"au_") !== false){
$re = str_replace("au_","",$step);
$add = fopen(".original_data/$re/block.txt", "a") or die("Unable to open file!");
 fwrite($add,"$text \n");
 fclose($add);
 $blockvv = file_get_contents(".original_data/$re/block.txt");
 $bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage', [
'chat_id' => $chat_id,
'text'=>"📥 انجام شد!

👥 کاربرانی که این شخص بلاک کرده :
$blockvv",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"📤 حذف کاربر",'callback_data'=>"huu_$re"],
['text'=>"📥 افزودن کاربر",'callback_data'=>"auu_$re"]
],
[
['text'=>"🔙 منو کاربر",'callback_data'=>"backu_$re"]
],
]
])
]); 
}
if(strpos($step,"hu_") !== false){
$re = str_replace("hu_","",$step);
$blockvv = file_get_contents(".original_data/$re/block.txt");
$hu = str_replace("$text","",$blockvv);
file_put_contents(".original_data/$re/block.txt",$hu);
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage', [
'chat_id' => $chat_id,
'text'=>"📤 انجام شد!

👥 کاربرانی که این شخص بلاک کرده :
$blockvv",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"📤 حذف کاربر",'callback_data'=>"huu_$re"],
['text'=>"📥 افزودن کاربر",'callback_data'=>"auu_$re"]
],
[
['text'=>"🔙 منو کاربر",'callback_data'=>"backu_$re"]
],
]
])
]); 
}
if(strpos($data,"/c_") !== false){
$re = str_replace("/c_","",$data);
$bot["step"] = "c_$re";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text' => "📨 پیامی که میخواهید برای `$re` ارسال کنی بفرست :",
'parse_mode' => "markdown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"❌ لغو",'callback_data'=>"backp"]
],
]
])
]);    
}
if(strpos($step,"c_") !== false and $data != "backp"){
$re = str_replace("c_","",$step);
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text' => "✅ انجام شد!",
'parse_mode' => "html",
]);     
bot('sendmessage',[
'chat_id'=>$re, 
'text' => "👮‍♂️ یک پیام از طرف مدیریت :

$text",
'parse_mode' => "html",
]);    
}
if($data == "backp"){
$bot["step"] = "free";
$outjson = json_encode($bot,true);
file_put_contents(".original_data/$chat_id/$chat_id.json",$outjson);
bot('editmessagetext', [
'chat_id' => $chat_id,
'message_id' => $message_id,
'text' => "👮‍♂️ Welcome to the admin PANEL",
'parse_mode' => "MarkDown",
'reply_markup'=> json_encode(['inline_keyboard' => [
[
['text'=>"[📦] آمار",'callback_data'=>"amar"]
],
[
['text'=>"[📡] فوروارد همگانی",'callback_data'=>"fs"],
['text'=>"[📰] پیام همگانی",'callback_data'=>"sends"]
],
[
['text'=>"[👤] تغییر وضعیت کاربر",'callback_data'=>"tvr"]
],
]
])
]);    
}
}
//===================
if(preg_match('/^\/([Cc][Rr][Ee][Aa][Tt][Oo][Rr])/',$text)){
bot('sendmessage',[
'chat_id'=>$chat_id, 
'text' => "👨‍💻 Developer : Dr Amir | @IFIED",
'parse_mode' => "MarkDown",
'reply_markup' => json_encode([
'inline_keyboard' => [
[
['text' => "🍾 ارسال پیام", 'url' => "https://t.me/IFIED"]
],
]
])
]);
}
}
?>