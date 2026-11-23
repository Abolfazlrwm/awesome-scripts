<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
$JekCbe = '[*[TOKEN]*]'; // توکن ربات 
define('SDHAKPK',$JekCbe);
include("[*[JDF]*].php");
function IeKsbG($method,$datas=[]){
    $url = "https://api.telegram.org/bot".SDHAKPK."/".$method;
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
function SendMessage($pV_iD,$SkTebC,$mode,$keyboard,$reply,$disable='true'){
return IeKsbG('SendMessage',[
'chat_id'=>$pV_iD,
'text'=>$SkTebC,
'parse_mode'=>$mode,
'reply_to_message_id'=>$reply,
'disable_web_page_preview'=>$disable,
'reply_markup'=>$keyboard
]);
}
function EditMessage($pV_iD,$message_id,$SkTebC,$mode,$keyboard){
return IeKsbG('EditMessageText',[
'chat_id'=>$pV_iD,
'message_id'=>$message_id,
'text'=>$SkTebC,
'parse_mode'=>$mode,
'reply_markup'=>$keyboard
]);    
}
function DeleteMessage($pV_iD,$message_id){
return IeKsbG('deletemessage', [
'chat_id'=>$pV_iD,
'message_id'=>$message_id,
]);
}

function Forward($KojaShe,$AzKoja,$KodomMSG){
return IeKsbG('ForwardMessage',[
'chat_id'=>$KojaShe,
'from_chat_id'=>$AzKoja,
'message_id'=>$KodomMSG
]);
}
function SendPhoto($pV_iD,$photo,$keyboard,$caption,$reply){
return IeKsbG('SendPhoto',[
'chat_id'=>$pV_iD,
'photo'=>$photo,
'caption'=>$caption
]);
}
function SendAudio($chatid,$audio,$keyboard,$caption,$reply,$title,$sazande){
return IeKsbG('SendAudio',[
'chat_id'=>$chatid,
'audio'=>$audio,
'caption'=>$caption,
'performer'=>$sazande,
'title'=>$title,
'reply_to_message_id'=>$reply,
'reply_markup'=>$keyboard
]);
}
function SendDocument($chatid,$document,$keyboard,$caption,$reply){
return IeKsbG('SendDocument',[
'chat_id'=>$chatid,
'document'=>$document,
'caption'=>$caption,
'reply_to_message_id'=>$reply,
'reply_markup'=>$keyboard
]);
}
function SendVideo($chatid,$video,$keyboard,$caption,$reply,$duration){
return IeKsbG('SendVideo',[
'chat_id'=>$chatid,
'video'=>$video,
'caption'=>$caption,
'reply_to_message_id'=>$reply,
'duration'=>$duration,
'reply_markup'=>$keyboard
]);
}

function save($filename,$TXTdata){
$myfile = fopen($filename,"w") or die("Unable to open file!");
fwrite($myfile,"$TXTdata");
fclose($myfile);
}
  function save2($data, $dir){
       $f = fopen("media/$dir","a") or die("Error to open file!");
       fwrite($f, "$data,");
       fclose($f);
  }
function KickChatMember($chatid,$user_id){
	IeKsbG('kickChatMember',[
	'chat_id'=>$chatid,
	'user_id'=>$user_id
	]);
	}
	
function LeaveChat($chatid){
	IeKsbG('LeaveChat',[
	'chat_id'=>$chatid
	]);
	}
	
function getChat($idchat){
	$json=file_get_contents('https://api.telegram.org/bot'.SDHAKPK."/getChat?chat_id=".$idchat);
	$data=json_decode($json,true);
	return $data["result"]["first_name"];
}
	
function GetChatMembersCount($chatid){
	IeKsbG('getChatMembersCount',[
	'chat_id'=>$chatid
	]);
	}
	
function GetChatMember($chatid,$userid){
	$truechannel = json_decode(file_get_contents('https://api.telegram.org/bot'.SDHAKPK."/getChatMember?chat_id=".$chatid."&user_id=".$userid));
	$tch = $truechannel->result->status;
	return $tch;
	}
	function TAK(){
    $numerics   = 'QWERTYUIOPLKJHGFDSAZCXVBNM';
    $container = $numerics;
    $key = '';
    for($i = 0;$i < 10;$i++){
        $_rand = rand(0,strlen($container)-1);
        $key .= substr($container, $_rand, 1);
    }
    return $key;
}
 /////// 
$update = json_decode(file_get_contents('php://input'),true);
if(isset($update['message'])){
    $message = $update['message'];
    $pV_iD = $message['chat']['id'];
    $message_id = $message['message_id'];
    $SkTebC = $message['text'];
    $iD_pV = $message['from']['id'];
    $firstname = $message['from']['first_name'];
    $lastname = isset($message['from']['last_name']) ? null:null;
    $username = isset($message['from']['username']) ?'@'.null:null;
    $video = $message['video'];
    $video_id = $message['video']['file_id'];
    $photo = $message['photo'];
    $photo_id = $message['photo'][0]['file_id'];
    $doc = $message['document'];
    $doc_id = $message['document']['file_id'];
    $forward_id = $update->message->forward_from->id;
$forward_chat = $update->message->forward_from_chat;
$forward_chat_username = $update->message->forward_from_chat->username;
$forward_from = $message->forward_from;
$forward_from_chat = $message->forward_from_chat;
$forward_id = $forward_from->id;
$forward_name = $forward_from->first_name;
$stickerid = $message->sticker->file_id;
$videoid = $message->video->file_id;
$voiceid = $message->voice->file_id;
$fileid = $message->document->file_id;
$photoid = $photo[count($photo)-1]->file_id;
$musicid = $message->audio->file_id;
$music_name = $message->audio->title;
$videonoteid = $message->video_note->file_id;
$caption = $update->message->caption;
}
$get = json_decode(file_get_contents("https://api.telegram.org/bot".SDHAKPK."/getMe"));
$IeKsbGid = $get->result->username;
$firstIeKsbG = $get->result->first_name;
$ChAnNeL = file_get_contents("channel.txt");
$ChAnNeL2 = file_get_contents("channel2.txt");
$ChAnNeL3 = file_get_contents("channel3.txt");
$joincha = json_decode(file_get_contents("https://api.telegram.org/bot$JekCbe/getChatMember?chat_id=@$ChAnNeL&user_id=".$iD_pV));
$joqw = $joincha->result->status;
$joinch = json_decode(file_get_contents("https://api.telegram.org/bot$JekCbe/getChatMember?chat_id=@$ChAnNeL2&user_id=".$iD_pV));
$joqwe = $joinch->result->status;
$joincj = json_decode(file_get_contents("https://api.telegram.org/bot$JekCbe/getChatMember?chat_id=@$ChAnNeL3&user_id=".$iD_pV));
$isok = $joincj->result->status;
$db = file_get_contents(json_decode('data.json',true));
$gif = file_get_contents("media/gif.txt");
$vid = file_get_contents("media/vid.txt");
$db = file_get_contents(json_decode('data.json',true));
$gif = file_get_contents("media/gif.txt");
$vid = file_get_contents("media/vid.txt");
$pics = file_get_contents("media/pic.txt");
$admin1 = file_get_contents("admin1.txt");
$admin2 = file_get_contents("admin2.txt");
$admin = "[*[ADMIN]*]"; // ای دی عددی ادمین اصلی 
$sudo = ["[*[ADMIN]*]","$admin1","$admin2"]; //آیدی عددی ادمین اصلی به جای 000000 بزارید
$IeKsbG_date = date('Ymd');
$step = file_get_contents("$pV_iD.txt");
mkdir("media");
//-------------@tak_php--------------
if(file_exists("textStart.txt")){
$SkTebCStart = file_get_contents("textStart.txt");
}else{
$SkTebCStart = "سلام به ربات فول سکسی خوش آمدید 🤤💦";
}
if(file_exists("media/CCC.txt")){
$uplink = file_get_contents("media/CCC.txt");
$up_id = explode("\n",$uplink);
$count_up = count($up_id);
if($count_up > 15){
IeKsbG('sendmessage',[
'chat_id'=>$admin,
'text'=>"
📝 لیست لینک های ذخیره شده آپلودر به 15 عدد لینک رسیدند و جهت افزایش سرعت ربات این لینک ها پاکسازی شدند ! 
 
🔗 آخرین لیست بدست آمده :

$uplink
",
'parse_mode'=>'HTML',     
]);
unlink("media/CCC.txt");
}
}else{
$uplink = "❌ لینک فایلی موجود نمیباشد ";
}
if(file_exists("textCHANNEL.txt")){
$SkTebCch = file_get_contents("textCHANNEL.txt");
}else{
$SkTebCch = "🌹 لطفاً جهت استفاده از ربات در سه کانال اسپانسر ما عضو شوید ";
}
if(file_exists("caption.txt")){
$caption = file_get_contents("caption.txt");
}else{
$caption = "♻️ متن کپشن تنظیم شود";
}
if(file_exists("onof.txt")){
$onof = file_get_contents("onof.txt");
}else{
$onof = "on";
}
if(file_exists("del.txt")){
$delt = file_get_contents("del.txt");
}else{
$delt = "10";
}
if(file_exists("lockpic.txt")){
$lockpic = file_get_contents("lockpic.txt");
}else{
$lockpic = "on";
}
if(file_exists("lockfilm.txt")){
$lockfilm = file_get_contents("lockfilm.txt");
}else{
$lockfilm = "on";
}
if(file_exists("lockgif.txt")){
$lockgif = file_get_contents("lockgif.txt");
}else{
$lockgif = "on";
}
if(file_exists("timer.txt")){
$timers = file_get_contents("timer.txt");
}else{
$timers = "on";
}
if(file_exists("dokm1.txt")){
$dokm1 = file_get_contents("dokm1.txt");
}else{
$dokm1 = "🤍 عکس 🖤";
}
if(file_exists("dokm2.txt")){
$dokm2 = file_get_contents("dokm2.txt");
}else{
$dokm2 = "🍿 فیلم 🍿";
}
if(file_exists("dokm3.txt")){
$dokm3 = file_get_contents("dokm3.txt");
}else{
$dokm3 = "گیف 🎗";
}
$keyMedia = json_encode([
      'keyboard'=> [
      [['text'=> '➕ عکس'],['text'=> '➕ فیلم']],
      [['text'=> '➕ گیف'], ['text'=> '🔙 بازگشت']]
      ],'resize_keyboard'=> true
]);

$keyHome = json_encode([
      'keyboard'=> [
      [['text'=> "$dokm1"],['text'=> "$dokm2"]],
      [['text'=>"$dokm3"]],
      ],'resize_keyboard'=> true
]);
$keyLock = json_encode([
      'keyboard'=> [
      [['text'=>"🔐 قفل عکس"]],
      [['text'=> '🔐 قفل گیف'],['text'=> '🔐 قفل فیلم']],
      [['text'=> '🔙 بازگشت']],
      ],'resize_keyboard'=> true
]);
$keyUnlock = json_encode([
      'keyboard'=> [
      [['text'=>"🔓 بازکردن عکس"]],
      [['text'=> '🔓 بازکردن گیف'],['text'=> '🔓 بازکردن فیلم']],
      [['text'=> '🔙 بازگشت']],
      ],'resize_keyboard'=> true
]);
$keyNameDok = json_encode([
      'keyboard'=> [
      [['text'=>"🏞 اسم دکمه عکس"]],
      [['text'=>"🎞 اسم دکمه فیلم"]],
      [['text'=>"🎥 اسم دکمه گیف"]],
      [['text'=> '🔙 بازگشت']],
      ],'resize_keyboard'=> true
]);
$keyPanel = json_encode([
      'keyboard'=> [
      [['text'=> 'آمار ربات 📊'],['text'=> '📥 آپلود']],
      [['text'=> '🔐 قفل چنل اول 🔐'],['text'=>"🔐 قفل چنل دوم 🔐"]],
      [['text'=> '🔐 قفل چنل سوم 🔐']],
      [['text'=> '🔰 افزودن ادمین 1'],['text'=>"🔰 افزودن ادمین 2"]],
      [['text'=> '🔰 حذف ادمین 1'],['text'=>"🔰 حذف ادمین 2"]],
      [['text'=> '🔰 تنظیم پاک شدن پیام بعد از چند ثانیه']],
      [['text'=>"پیام همگانی 📨"],['text'=>"فوروارد همگانی 📧"]],
      [['text'=>'📝 متن جوین اجباری کانال']],
      [['text'=>'📑 متن استارت ربات '],['text'=>'📝 متن کپشن']],
      [['text'=> '🎈 اسم دکمه ها']],
      [['text'=>"🔆 روشن کردن ربات"],['text'=>"💤 خاموش کردن ربات"]],
      [['text'=> '➕ افزودن مدیا'],['text'=>'🗑 حذف مدیا']],
      [['text'=> '🔓 باز کردن'],['text'=>'🔐 قفل کردن']],
      [['text'=> '🔆 روشن کردن تایمر'],['text'=>'💤 خاموش کردن تایمر']],
      [['text'=> 'تنظیم کانال فیلم 🍿'],['text'=>'🖇 ارسال به کانال']],
      [['text'=> '/start']],
      ],'resize_keyboard'=> true
]);
$keyNew = json_encode([
      'keyboard'=> [
      [['text'=> '➕ گیف']],
      [['text'=> '➕ عکس'],['text'=>"➕ فیلم"]],
      [['text'=> '🔙 بازگشت']],
      ],'resize_keyboard'=> true
]);
$keyDel = json_encode([
      'keyboard'=> [
      [['text'=> '🗑 حذف همه فیلم ها']],
      [['text'=> '🗑 حذف همه گیف ها']],
      [['text'=> '🗑 حذف همه عکس ها']],
      [['text'=> '🔙 بازگشت']],
      ],'resize_keyboard'=> true
]);
$keyBack = json_encode([
      'keyboard'=> [
      [['text'=> '🔙 بازگشت']]
      ],'resize_keyboard'=> true
]);
$keyRemove = json_encode([
      'ReplyKeyboardRemove'=>[
       []
      ],'remove_keyboard'=> true
]);
//-------------@tak_php--------------
$amar = file_get_contents("members.txt");
$memberst = explode("\n",$amar);
if (!in_array($pV_iD, $memberst)) {
$myfile2 = fopen("members.txt", "a") or die("Unable to open file!");
fwrite($myfile2, "$pV_iD\n");
fclose($myfile2);
}
if($onof == "off" and $pV_iD != $admin){
IeKsbG('sendMessage',[
'chat_id'=>$pV_iD,
'text'=>"
🤖 | ربات موقتا خاموش میباشد
",
]);
exit();
}
if(file_exists("channel.txt") and file_exists("channel2.txt") and file_exists("channel3.txt") and $pV_iD != $admin){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator' or $isok != 'member' && $isok != 'creator' && $isok != 'administrator'){
IeKsbG('sendMessage',[
'chat_id'=>$pV_iD,
'text'=>"
$SkTebCch
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🧡 1 🧡", 'url' => "https://t.me/$ChAnNeL"],['text' => "💛 2 💛", 'url' => "https://t.me/$ChAnNeL2"]],
[['text' => "💙 3 💙", 'url' => "https://t.me/$ChAnNeL3"]],
],
])
]);
exit();
}}
if(file_exists("channel.txt") and file_exists("channel2.txt") and $pV_iD != $admin){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator' or $joqwe != 'member' && $joqwe != 'creator' && $joqwe != 'administrator'){
IeKsbG('sendMessage',[
'chat_id'=>$pV_iD,
'text'=>"
$SkTebCch
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🧡 1 🧡", 'url' => "https://t.me/$ChAnNeL"],['text' => "💛 2 💛", 'url' => "https://t.me/$ChAnNeL2"]],
],
])
]);
exit();
}}
if(file_exists("channel.txt") and $pV_iD != $admin){
if($joqw != 'member' && $joqw != 'creator' && $joqw != 'administrator'){
IeKsbG('sendMessage',[
'chat_id'=>$pV_iD,
'text'=>"
$SkTebCch
",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🧡 1 🧡", 'url' => "https://t.me/$ChAnNeL"]],
],
])
]);
exit();
}}
if(preg_match('/^\/([Ss]tart)(.*)/',$SkTebC,$match)){
$match[2] = str_replace(" ","",$match[2]);
$match[2] = str_replace("\n","",$match[2]);
$number = $match[2];
$ups = file_get_contents("media/$number.txt");
if($number == ""){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"$SkTebCStart",
'reply_markup'=>$keyHome
]);
}
if($number != ""){
if(file_exists("media/$number.txt")){
$jo = $mphoto + 1;
    save($iD_pV ,"photo", "$jo");
   $ex = explode(",",$ups);
	$rand = rand(1, count($ex)-1) - 1;
	$send = $ex[$rand];
     $mi1 = IeKsbG('sendphoto',[
  'chat_id'=>$pV_iD,
'photo'=>"$send",
'caption'=>"$caption
",
  ]);
  $mi2 = IeKsbG('sendvideo',[
'chat_id'=>$pV_iD,
'video'=>"$send",
   'parse_mode'=>'HTML',
   'caption'=>"$caption
",
   ]);
   $mi3 = IeKsbG('SendDocument',[
'chat_id'=>$pV_iD,
'document'=>"$send",
   'parse_mode'=>'HTML',
'caption'=>"$caption
",
   ]);
   if($timers == "on"){
    $nop = $delt;////بعد از چند ثانیه پاک بشه
      $send = IeKsbG('sendmessage',[
  'chat_id'=>$pV_iD,
'text'=>"<pre>فایل بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️} 
",
'parse_mode'=>'HTML',     
]);
while($nop >= 1){
sleep(1);
$nop--;
$id = $send->result->message_id;
   IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
'text'=>"<pre>فایل بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️} 

لطفا صبور باشید این پیام حذف شود و دوباره رو یک دکمه جدید بزنید 😉🌹",
'parse_mode'=>'HTML',     
]);}
sleep(1);
 $no1 = $mi1->result->message_id;
 $no2 = $mi2->result->message_id;
 $no3 = $mi3->result->message_id;
  IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no1,
    ]);
    IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no2,
    ]);
    IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no3,
    ]);
     IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
                'text'=>" <i> این پیام حذف شد </i>",
                'parse_mode'=>'HTML',     
]);
}
}else{
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"❌ فایل با شماره ( $number ) در دیتابیس ما یافت نشد ",
]);
}
}
}
//-------------@tak_php--------------
if($SkTebC == "/creator"){
if(file_exists("../../SlowCreate/UsErBoT/$IeKsbGid/creator.txt")){
$creator = file_get_contents("../../SlowCreate/UsErBoT/$IeKsbGid/creator.txt");
}
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"$creator",
]);
}
//-------------@tak_php--------------
if($SkTebC == "$dokm1"){
if($lockpic == "off"){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🔐 قسمت دیدن عکس در حال حاضر قفل است",
]);
exit();
}
 if(file_exists("media/pic.txt")){
    $jo = $mphoto + 1;
    save($iD_pV ,"photo", "$jo");
   $ex = explode(",",$pics);
	$rand = rand(1, count($ex)-1) - 1;
	$send = $ex[$rand];
     $mi = IeKsbG('sendphoto',[
  'chat_id'=>$pV_iD,
'photo'=>"$send",
'caption'=>"$caption
",
  ]);
    $nop = $delt;////بعد از چند ثانیه پاک بشه
      $send = IeKsbG('sendmessage',[
  'chat_id'=>$pV_iD,
'text'=>"<pre>عکس بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️} 
",
'parse_mode'=>'HTML',     
]);
while($nop >= 1){
sleep(1);
$nop--;
$id = $send->result->message_id;
   IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
'text'=>"<pre>عکس بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️} 

لطفا صبور باشید این پیام حذف شود و دوباره رو یک دکمه جدید بزنید 😉🌹",
'parse_mode'=>'HTML',     
]);}
sleep(1);
 $no = $mi->result->message_id;
  IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no,
    ]);
     IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
                'text'=>" <i> این پیام حذف شد </i>",
                'parse_mode'=>'HTML',     
]);
 }else{
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🌹فعلا عکسی موجود نمی باشد!️",
]);
}}
//-------------@tak_php--------------
if($SkTebC == "$dokm2"){
if($lockfilm == "off"){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🔐 قسمت دیدن فیلم در حال حاضر قفل است",
]);
exit();
}
 if(file_exists("media/vid.txt")){
       $jo = $mvideo + 1;
    save($iD_pV ,"video", "$jo");
   $ex = explode(",",$vid);
	$rand = rand(1, count($ex)-1) - 1;
	$send = $ex[$rand];
	 $mi = IeKsbG('sendvideo',[
'chat_id'=>$pV_iD,
'video'=>"$send",
   'parse_mode'=>'HTML',
   'caption'=>"$caption
",
   ]);
    $nop = $delt;////بعد از چند ثانیه پاک بشه
      $send = IeKsbG('sendmessage',[
  'chat_id'=>$pV_iD,
'text'=>"<pre>فیلم بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️}
",
'parse_mode'=>'HTML',     
]);
while($nop >= 1){
sleep(1);
$nop--;
$id = $send->result->message_id;
   IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
'text'=>"<pre>فیلم بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️}

لطفا صبور باشید این پیام حذف شود و دوباره رو یک دکمه جدید بزنید 😉🌹",
'parse_mode'=>'HTML',     
]);}
sleep(1);
 $no = $mi->result->message_id;
  IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no,
    ]);
     IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
                'text'=>" <i> این پیام حذف شد </i>",
                'parse_mode'=>'HTML',     
]);
}else{
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🌹فعلا فیلمی موجود نمی باشد!️",
]);
}}
//-------------@tak_php--------------
elseif($SkTebC == "$dokm3"){
if($lockgif == "off"){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🔐 قسمت دیدن گیف در حال حاضر قفل است",
]);
exit();
}
    if(file_exists("media/gif.txt")){
       $jo = $mgif + 1;
    save($iD_pV ,"gif", "$jo");
   $ex = explode(",",$gif);
	$rand = rand(1, count($ex)-1) - 1;
	$send = $ex[$rand];
	 $mi = IeKsbG('SendDocument',[
'chat_id'=>$pV_iD,
'document'=>"$send",
   'parse_mode'=>'HTML',
'caption'=>"$caption
",
   ]);
    $nop = $delt;////بعد از چند ثانیه پاک بشه
      $send = IeKsbG('sendmessage',[
  'chat_id'=>$pV_iD,
'text'=>"<pre>گیف بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️}
",
'parse_mode'=>'HTML',     
]);
while($nop >= 1){
sleep(1);
$nop--;
$id = $send->result->message_id;
   IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
'text'=>"<pre>گیف بالا در <i>$nop</i> ثانیه دیگر به صورت خودکار پاک میشود</pre>

لطفا آن را در پیام های ذخیره شده تلگرام بفرستید .... { فضا ابری تلگرام ☁️}

لطفا صبور باشید این پیام حذف شود و دوباره رو یک دکمه جدید بزنید 😉🌹",
'parse_mode'=>'HTML',     
]);}
sleep(1);
 $no = $mi->result->message_id;
  IeKsbG('deletemessage',[
  'chat_id'=>$pV_iD,
    'message_id'=>$no,
    ]);
     IeKsbG('editmessagetext',[
                'chat_id'=>$pV_iD,
                'message_id'=>$id,
                'text'=>" <i> این پیام حذف شد </i>",
                'parse_mode'=>'HTML',     
]);
}else{
        IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🌹فعلا گیفی موجود نمی باشد!️",
]);
}}
//-------------@tak_php--------------
elseif($SkTebC == "/panel" || $SkTebC == "🔙 بازگشت"){
if(in_array($iD_pV , $sudo)){
      save("$pV_iD.txt",'none');
  IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<pre>سلام ادمین جان به پنل مدیریت خوش اومدی</pre>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyPanel
	   ]);
}}
elseif($SkTebC == 'آمار ربات 📊' && in_array($iD_pV , $sudo)){
  $user = file_get_contents("members.txt");
    $member_id = explode("\n",$user);
    $counts = count($member_id) -1;
    $picc = count(explode(",", $pics)) - 1;
    $vidd = count(explode(",", $vid)) - 1;
    $giff = count(explode(",", $gif)) - 1;
    $time = jdate('آخرین بروزرسانی در ساعت H:i:s روز l انجام شد.');   
	 IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"تعداد کاربران : $counts 📊
تعداد فیلم ها : $vidd 💚
تعداد عکس ها : $picc  🤍
تعداد گیف ها : $giff ❤️
".PHP_EOL."$time

فیلم های آپلود شده برای دانلود کاربران با لینک 👇🏻

•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•

$uplink

•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•

",
'parse_mode'=>'HTML',
   ]);
}
elseif($SkTebC == "➕ افزودن مدیا"){
if(in_array($iD_pV , $sudo)){
      save("$pV_iD.txt",'none');
  IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<pre>لطفا نوع مدیا خود را انتخاب کنید :</pre>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyNew
	   ]);
}}
elseif($SkTebC == '➕ گیف' or $SkTebC == '➕ عکس' or $SkTebC == '➕ فیلم'  && in_array($iD_pV , $sudo)){
    $fa = ["➕ گیف","➕ فیلم","➕ عکس"];
    $en = ['gif','film','photo'];
    $str = str_replace($fa,$en,$SkTebC);
    save("$pV_iD.txt","$str");
    IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"لطفا $SkTebC های خود را ارسال کنید و در آخر گزینه 🔙 بازگشت را بزنید",
  'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'film' && isset($video)){
    save2($video_id,"vid.txt");
       IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎞 فیلم شما ذخیره شد فیلم های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .
",
  'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'photo' && isset($photo)){
   save2($photo_id,"pic.txt");
      IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🌁 عکس شما ذخیره شد عکس های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .
",
 'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'gif' && isset($doc)){
    save2($doc_id,"gif.txt");
     IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎥 گیف شما ذخیره شد گیف های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .
",
 'reply_markup'=>$keyBac
  ]);
}
elseif($SkTebC == "📥 آپلود" && in_array($pV_iD,$sudo)){
save("$pV_iD.txt",'uplod');
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"فیلم , عکس , گیف خود را جهت آپلود ارسال کنید . . .",
 'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'uplod' && isset($photo)){
$r = TAK();
save2($photo_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
 IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🌁 عکس شما ذخیره شد عکس های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
 'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'uplod' && isset($doc)){
$r = TAK();
save2($doc_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎥 گیف شما ذخیره شد گیف های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
 'reply_markup'=>$keyBac
  ]);
}
elseif($step == 'uplod' && isset($video)){
$r = TAK();
save2($video_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎞 فیلم شما ذخیره شد فیلم های دیگر هم دارید ارسال کنید در غیر این صورت ( 🔙 بازگشت ) را بزنید . . .

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
  'reply_markup'=>$keyBack
  ]);
}
elseif($SkTebC == "فوروارد همگانی 📧" && in_array($pV_iD,$sudo)){
         save("$pV_iD.txt",'fwd');
    IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"پیام خودتون را فروراد کنید:",
    'reply_markup'=>json_encode([
      'keyboard'=>[
	  [['text'=>'/panel']],
      ],'resize_keyboard'=>true])
  ]);
}
elseif($step == "fwd" && $SkTebC!="/panel"){
   save("$pV_iD.txt","none");
  IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"با موفقیت فروارد شد.",
  ]);
	$all_member = fopen( "members.txt", "r");
		while( !feof( $all_member)) {
 			$user = fgets( $all_member);
	$id = json_decode(file_get_contents("https://api.telegram.org/bot".SDHAKPK."/getChat?chat_id=".$user));
	$user2 = $id->result->id;
if($user2 != null){
			    IeKsbG('ForwardMessage',[
        'chat_id'=>$user,
        'from_chat_id'=>$pV_iD,
        'message_id'=>$message_id
    ]);
}
		}
}
elseif($SkTebC == "🖇 ارسال به کانال" && in_array($pV_iD,$sudo)){
save("$pV_iD.txt",'ullodaer');
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"فیلم , عکس , گیف خود را جهت آپلود ارسال کنید . . .",
 'reply_markup'=>$keyBack
  ]);
}
elseif($step == 'ullodaer' && isset($photo)){
$r = TAK();
save2($photo_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
file_put_contents("New.txt","https://t.me/$IeKsbGid?start=$r");
save("$pV_iD.txt",'SnedCh');
 IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"
🌁 عکس شما ذخیره شد جهت ارسال به کانال روی دکمه زیر بزنید 👇🏻

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔆 ارسال به کانال"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
elseif($step == 'ullodaer' && isset($doc)){
$r = TAK();
save2($doc_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
save("$pV_iD.txt",'SnedCh');
file_put_contents("New.txt","https://t.me/$IeKsbGid?start=$r");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"
🎥 گیف شما ذخیره شد جهت ارسال به کانال روی دکمه زیر بزنید 👇🏻

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔆 ارسال به کانال"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
elseif($step == 'ullodaer' && isset($video)){
$r = TAK();
save2($video_id,"$r.txt");
$myfile2 = fopen("media/CCC.txt", "a") or die("Unable to open file!");
$link = "🔗 لینک :  https://t.me/$IeKsbGid?start=$r";
fwrite($myfile2, "$link\n");
fclose($myfile2);
save("$pV_iD.txt",'SnedCh');
file_put_contents("New.txt","https://t.me/$IeKsbGid?start=$r");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎞 فیلم شما ذخیره شد جهت ارسال به کانال روی دکمه زیر بزنید 👇🏻

🔗 لینک :  https://t.me/$IeKsbGid?start=$r",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🔆 ارسال به کانال"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}

elseif($step == 'SnedCh' && $SkTebC == "🔆 ارسال به کانال"){
save("$pV_iD.txt",'mtnnn');
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"📑 متن پیام ارسال به کانال را بفرستید",
'parse_mode'=>'Markdown', 
'reply_markup'=>$keyBack
]);
}
elseif($step == 'mtnnn'){
save("$pV_iD.txt",'sendinhg');
$linkfilm = file_get_contents("New.txt");
file_put_contents("Net.txt","$SkTebC");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"
🔐 جهت تایید ارسال پیام پایین به کانال روی دکمه ( ✅ تایید ) بزنید

$SkTebC



[📤 مشاهده فیلم]($linkfilm)

                  [📤 مشاهده فیلم ]($linkfilm)
",
'parse_mode'=>'Markdown', 
'reply_markup'=>json_encode([ 
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"✅ تایید"]],
[['text'=>"🔙 بازگشت"]],
]
])
]);
}
elseif($step == 'sendinhg' && $SkTebC == "✅ تایید"){
if(file_exists("ChAnNeLsEnD.txt")){
$linkfilm = file_get_contents("New.txt");
$mtnfilm = file_get_contents("Net.txt");
$CaNnElSeNd = file_get_contents("ChAnNeLsEnD.txt");
save("$pV_iD.txt",'none');
IeKsbG('sendmessage',[
'chat_id'=>$CaNnElSeNd,
'text'=>"

$mtnfilm



[📤 مشاهده فیلم]($linkfilm)

                  [📤 مشاهده فیلم ]($linkfilm)
",
'parse_mode'=>'Markdown', 
]);
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"✅ متن با موفقیت به کانال ارسال شد ",
'reply_markup'=>$keyBack
]);
}else{
save("$pV_iD.txt",'none');
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"❌ ابتدا کانال فیلم را تنظیم کنید . . .",
'reply_markup'=>$keyBack
]);
}
}
elseif($SkTebC == "تنظیم کانال فیلم 🍿" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","setfilm");
SendMessage($pV_iD,"
💯 ای دی کانال فیلم *با را @ ارسال کنید*

👈🏻 توجه : ربات باید مدیر کانال مورد نظر باشد!",'Markdown',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "setfilm"){
file_put_contents("ChAnNeLsEnD.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"
✅ با موفقیت چنل فیلم $SkTebC تنظیم شد
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "پیام همگانی 📨" && in_array($pV_iD,$sudo)){
      save("$pV_iD.txt",'send');
 IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"پیام خود را در قالب متن بفرستید:",
'reply_markup'=>json_encode([
      'keyboard'=>[
	  [['text'=>'/panel']],
      ],'resize_keyboard'=>true])
  ]);
}
elseif($step == "send" && $SkTebC!="/panel" && in_array($pV_iD,$sudo)){
   save("$pV_iD.txt","none");
  IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"✅ پیام همگانی فرستاده شد.",
  ]);
		$all_member = fopen( "members.txt", 'r');
		while( !feof( $all_member)) {
 			$user = fgets( $all_member);
	$id = json_decode(file_get_contents("https://api.telegram.org/bot".SDHAKPK."/getChat?chat_id=".$user));
	$user2 = $id->result->id;
if($user2 != null){
			if($SkTebC != null){
	IeKsbG('sendMessage', [
	'chat_id' =>$user,
	'text' =>$SkTebC,
	'parse_mode' =>"html",
	'disable_web_page_preview' =>"true"
	]);
			}
}
		}
}
elseif($SkTebC == "💤 خاموش کردن تایمر" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("timer.txt","off");
SendMessage($pV_iD,"
💤  تایمر با موفقیت خاموش شد احتمال فیلتر شدن ربات بیشتر میشود ❗️
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}

elseif($SkTebC == "🔆 روشن کردن تایمر" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("timer.txt","on");
SendMessage($pV_iD,"
✅ تایمر با موفقیت روشن شد زمان حذف شدن پیام ها : $delt ثانیه . . .
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($SkTebC == "🔐 قفل چنل اول 🔐" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","set join channel");
SendMessage($pV_iD,"💠 لطفا آیدی کانال مورد نظر را بدون @ ارسال کنید

👈🏻 توجه : ربات باید مدیر کانال مورد نظر باشد!",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "set join channel"){
file_put_contents("channel.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت چنل اول @$SkTebC تنظیم شد ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "🔐 قفل چنل دوم 🔐" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","set join channel2");
SendMessage($pV_iD,"💠 لطفا آیدی کانال مورد نظر را بدون @ ارسال کنید

👈🏻 توجه : ربات باید مدیر کانال مورد نظر باشد!",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "set join channel2"){
file_put_contents("channel2.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت چنل دوم @$SkTebC تنظیم شد ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "🔐 قفل چنل سوم 🔐" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","set join channel3");
SendMessage($pV_iD,"💠 لطفا آیدی کانال مورد نظر را بدون @ ارسال کنید

👈🏻 توجه : ربات باید مدیر کانال مورد نظر باشد!",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "set join channel3"){
file_put_contents("channel3.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت چنل سوم @$SkTebC تنظیم شد ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "📝 متن جوین اجباری کانال" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","textCHANNEL");
SendMessage($pV_iD,"🔐 متن جوین اجباری کانال را ارسال کنید 

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "textCHANNEL"){
file_put_contents("textCHANNEL.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت متن جوین اجباری کانال تنظیم شد . . .

♻️ متن : 
$SkTebC
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "📑 متن استارت ربات" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","textStart");
SendMessage($pV_iD,"🔰 متن استارت ربات را ارسال کنید 

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "textStart"){
file_put_contents("textStart.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت متن استارت ربات تنظیم شد . . .

♻️ متن : 
$SkTebC
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "🔰 تنظیم پاک شدن پیام بعد از چند ثانیه" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","del");
SendMessage($pV_iD,"♨️ لطفاً عدد انگلیسی را ارسال کنید زمان پاک شدن ثانیه ای می‌باشد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "del" and is_numeric($SkTebC)){
if($SkTebC < 30 and $SkTebC > 1){
file_put_contents("del.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت فایل ها بعد از $SkTebC ثانیه پاک میشوند از این به بعد . . .
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ لطفاً عددی بین 1 تا 30 ارسال کنید !
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
}
elseif($SkTebC == "📝 متن کپشن" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","caption");
SendMessage($pV_iD,"🔰 متن کپشن ربات را ارسال کنید

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "caption"){
file_put_contents("caption.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت کپشن ربات تنظیم شد . . .

♻️ متن : 
$SkTebC
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($SkTebC == "🔰 افزودن ادمین 1" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
save("$pV_iD.txt","admin1");
SendMessage($pV_iD,"لطفا ایدی عددی فرد مورد نظر را جهت ادمین شدن ارسال کنید📍

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($step == "admin1" and is_numeric($SkTebC)){
file_put_contents("admin1.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت ادمین جدید با ای دی عددی <pre>$SkTebC</pre> اضافه شد ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($SkTebC == "🔰 افزودن ادمین 2" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
save("$pV_iD.txt","admin2");
SendMessage($pV_iD,"لطفا ایدی عددی فرد مورد نظر را جهت ادمین شدن ارسال کنید📍

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($step == "admin2" and is_numeric($SkTebC)){
file_put_contents("admin2.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت ادمین جدید با ای دی عددی <pre>$SkTebC</pre> اضافه شد ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "🔰 حذف ادمین 1" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
save("$pV_iD.txt","DeleteAdmin1");
SendMessage($pV_iD,"

جهت حذف این ادمین روی ✅ انجام بزنید . . .
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"✅ انجام"]],
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($step == "DeleteAdmin1" and $SkTebC == "✅ انجام" and $SkTebC != "🔙 بازگشت"){
file_put_contents("admin1.txt","$pV_iD");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت ادمین مورد نظر حذف شد",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($SkTebC == "🔰 حذف ادمین 2" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
save("$pV_iD.txt","DeleteAdmin2");
SendMessage($pV_iD,"

جهت حذف این ادمین روی ✅ انجام بزنید . . .
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"✅ انجام"]],
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($step == "DeleteAdmin2" and $SkTebC == "✅ انجام" and $SkTebC != "🔙 بازگشت"){
file_put_contents("admin2.txt","$pV_iD");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"✅ با موفقیت ادمین مورد نظر حذف شد",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}

elseif($SkTebC == "💤 خاموش کردن ربات" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("onof.txt","off");
SendMessage($pV_iD,"ربات با موفقیت 💤 خاموش شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}

elseif($SkTebC == "🔆 روشن کردن ربات" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("onof.txt","on");
SendMessage($pV_iD,"ربات با موفقیت 🔆 روشن شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($SkTebC == "🗑 حذف مدیا"){
if(in_array($iD_pV , $sudo)){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<b>🔐 گزینه مورد نظر را جهت حذف کردن انتخاب کنید</b>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyDel
	   ]);
}}
elseif($SkTebC == "🗑 حذف همه فیلم ها"){
if(in_array($iD_pV , $sudo)){
unlink("media/vid.txt");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<b>✅ با موفقیت تمام فیلم ها حذف شدند</b>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyPanel
	   ]);
}}
elseif($SkTebC == "🗑 حذف همه گیف ها"){
if(in_array($iD_pV , $sudo)){
unlink("media/gif.txt");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<b>✅ با موفقیت تمام گیف ها حذف شدند</b>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyPanel
	   ]);
}}
elseif($SkTebC == "🗑 حذف همه عکس ها"){
if(in_array($iD_pV , $sudo)){
unlink("media/pic.txt");
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>'<b>✅ با موفقیت تمام عکس ها حذف شدند</b>',
	   'parse_mode'=>'HTML',
	   'reply_markup'=>$keyPanel
	   ]);
}}
elseif($SkTebC == "🔓 باز کردن" && in_array($iD_pV , $sudo)){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🔓 گزینه مورد نظر جهت باز کردن آن را انتخاب کنید",
'reply_markup'=>$keyUnlock
]);
}
elseif($SkTebC == "🔐 قفل کردن" && in_array($iD_pV , $sudo)){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🔐 گزینه مورد نظر جهت قفل کردن آن را انتخاب کنید",
'reply_markup'=>$keyLock
]);
}
elseif($SkTebC == "🔐 قفل عکس" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("lockpic.txt","off");
SendMessage($pV_iD,"🔐 قفل عکس با موفقیت فعال شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}

elseif($SkTebC == "🔓 بازکردن عکس" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
unlink("lockpic.txt");
SendMessage($pV_iD,"🔓 قفل عکس با موفقیت باز شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($SkTebC == "🔐 قفل فیلم" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("lockfilm.txt","off");
SendMessage($pV_iD,"🔐 قفل فیلم با موفقیت فعال شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}

elseif($SkTebC == "🔓 بازکردن فیلم" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
unlink("lockfilm.txt");
SendMessage($pV_iD,"🔓 قفل فیلم با موفقیت باز شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($SkTebC == "🔐 قفل گیف" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
file_put_contents("lockgif.txt","off");
SendMessage($pV_iD,"🔐 قفل گیف با موفقیت فعال شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}

elseif($SkTebC == "🔓 بازکردن گیف" && in_array($iD_pV , $sudo)){
if ($pV_iD == $admin) {
unlink("lockgif.txt");
SendMessage($pV_iD,"🔓 قفل گیف با موفقیت باز شد

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}else{
SendMessage($pV_iD,"❌ | شما ادمین اصلی نیستید ",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}}
elseif($SkTebC == "🎈 اسم دکمه ها" && in_array($iD_pV , $sudo)){
IeKsbG('sendmessage',[
'chat_id'=>$pV_iD,
'text'=>"🎈 دکمه مورد نظر جهت تغییر اسم آن را انتخاب کنید",
'reply_markup'=>$keyNameDok
]);
}
elseif($SkTebC == "🏞 اسم دکمه عکس" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","dokm1");
SendMessage($pV_iD,"✅ اسم جدید دکمه را وارد کنید

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "dokm1" and $SkTebC != "🔙 بازگشت"){
file_put_contents("dokm1.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"📌 اسم دکمه مورد نظر با موفقیت به <b>$SkTebC</b> تغییر کرد
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($SkTebC == "🎞 اسم دکمه فیلم" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","dokm2");
SendMessage($pV_iD,"✅ اسم جدید دکمه را وارد کنید

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "dokm2" and $SkTebC != "🔙 بازگشت"){
file_put_contents("dokm2.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"📌 اسم دکمه مورد نظر با موفقیت به <b>$SkTebC</b> تغییر کرد
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($SkTebC == "🎥 اسم دکمه گیف" && in_array($iD_pV , $sudo)){
save("$pV_iD.txt","dokm3");
SendMessage($pV_iD,"✅ اسم جدید دکمه را وارد کنید

",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
elseif($step == "dokm3" and $SkTebC != "🔙 بازگشت"){
file_put_contents("dokm3.txt","$SkTebC");
save("$pV_iD.txt",'none');
SendMessage($pV_iD,"📌 اسم دکمه مورد نظر با موفقیت به <b>$SkTebC</b> تغییر کرد
",'HTML',json_encode(['resize_keyboard'=>true,'keyboard'=>[
[['text'=>"🔙 بازگشت"]],
]
]),$message_id);
}
?>