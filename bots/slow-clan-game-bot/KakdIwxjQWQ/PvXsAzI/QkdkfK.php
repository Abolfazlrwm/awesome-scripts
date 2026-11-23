<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

flush();
ob_start();
set_time_limit(0);
error_reporting(0);
ob_implicit_flush(1);
date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//
$DeRtK = [0]; //ایدی ادمین //ایدی ادمین
$FdCvBn = "[*[TOKEN]*]"; //توکن ربات
define('TDKVSN',$FdCvBn);
//------------------------------------------------------------------------------
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".TDKVSN."/".$method;
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
//------------------------------------------------------------------------------
function CreateZip($files = array(),$destination) {
    if(file_exists($destination)){
		return false;
	}
    $valid_files = array();
    if(is_array($files)){
        foreach($files as $file){
            if(file_exists($file)){
                $valid_files[] = $file;
            }
        }
    }
    if(count($valid_files)){
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true){
            return false;
        }
        foreach($valid_files as $file){
            $zip->addFile($file,$file);
        }
        $zip->close();
        return file_exists($destination);
    }else{
        return false;
    }
}
function DeleteFolder($path){
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
//------------------------------------------------------------------------------
function SendMessage($pV_iD,$HbNsMl,$mode,$reply = null,$keyboard = null){
	bot('SendMessage',[
	'chat_id'=>$pV_iD,
	'text'=>$HbNsMl,
	'parse_mode'=>$mode,
	'reply_to_message_id'=>$reply,
	'reply_markup'=>$keyboard
	]);
}
function EditMessageText($pV_iD,$message_id,$HbNsMl){
    bot('EditMessageText',[
    'chat_id'=>$pV_iD,
    'message_id'=>$message_id,
    'text'=>$HbNsMl
    ]);
}
function EditKeyboard($pV_iD,$message_id,$keyboard){
	bot('EditMessageReplyMarkup',[
    'chat_id'=>$pV_iD,
    'message_id'=>$message_id,
	'reply_markup'=>$keyboard
    ]);
}
function AnswerCallbackQuery($callback_query_id,$HbNsMl,$show_alert = false){
	bot('AnswerCallbackQuery',[
    'callback_query_id'=>$callback_query_id,
    'text'=>$HbNsMl,
	'show_alert'=>$show_alert
    ]);
}
function Forward($chatid,$iD_pV,$massege_id){
	bot('ForwardMessage',[
    'chat_id'=>$chatid,
    'from_chat_id'=>$iD_pV,
    'message_id'=>$massege_id
    ]);
}
function SendPhoto($chatid,$photo,$caption = null){
	bot('SendPhoto',[
	'chat_id'=>$chatid,
	'photo'=>$photo,
	'caption'=>$caption
	]);
}
function SendAudio($chatid,$audio,$caption = null,$sazande = null,$title = null){
	bot('SendAudio',[
	'chat_id'=>$chatid,
	'audio'=>$audio,
	'caption'=>$caption,
	'performer'=>$sazande,
	'title'=>$title
	]);
}
function SendDocument($chatid,$document,$caption = null){
	bot('SendDocument',[
	'chat_id'=>$chatid,
	'document'=>$document,
	'caption'=>$caption
	]);
}
function SendSticker($chatid,$sticker){
	bot('SendSticker',[
	'chat_id'=>$chatid,
	'sticker'=>$sticker
	]);
}
function SendVideo($chatid,$video,$caption = null,$duration = null){
	bot('SendVideo',[
	'chat_id'=>$chatid,
	'video'=>$video,
    'caption'=>$caption,
	'duration'=>$duration
	]);
}
function SendVoice($chatid,$voice,$caption = null){
	bot('SendVoice',[
	'chat_id'=>$chatid,
	'voice'=>$voice,
	'caption'=>$caption
	]);
}
function SendContact($chatid,$first_name,$phone_number){
	bot('SendContact',[
	'chat_id'=>$chatid,
	'first_name'=>$first_name,
	'phone_number'=>$phone_number
	]);
}
function GetProfile($iD_pV){
    $get = file_get_contents('https://api.telegram.org/bot'.TDKVSN.'/getUserProfilePhotos?user_id='.$iD_pV);
    $decode = json_decode($get, true);
    $result = $decode['result'];
    $profile = $result['photos'][0][0]['file_id'];
    return $profile;
}
function GetChat($chatid){
	$get =  bot('GetChat',['chat_id'=> $chatid]);
	return $get;
}
function GetMe(){
	$get =  bot('GetMe',[]);
	return $get;
}
function CheckLink($HbNsMl){
	global $data;
	if($data['lock']['link'] == "✅"){
		if(stripos($HbNsMl, "t.me") !== false || stripos($HbNsMl, "http") !== false || stripos($HbNsMl, "www.") !== false){
			return true;
		}
	}
}
function CheckFilter($HbNsMl){
	global $data;
	foreach($data['filters'] as $value){
		if(mb_strstr($HbNsMl, "$value")){
			return true;
		}
	}
}
//------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if(!isset($update->update_id)){ exit(); }
if(isset($update->message)){
    $message = $update->message; 
    $pV_iD = $message->chat->id;
    $HbNsMl = $message->text;
    $message_id = $message->message_id;
    $iD_pV = $message->from->id;
    $tc = $message->chat->type;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $username = $message->from->username;
    $caption = $message->caption;
    $reply = $message->reply_to_message->forward_from->id;
    $reply_id = $message->reply_to_message->from->id;
    $forward = $message->forward_from;
    $forward_id = $message->forward_from->id;
    $sticker_id = $message->sticker->file_id;
    $video_id = $message->video->file_id;
    $voice_id = $message->voice->file_id;
    $file_id = $message->document->file_id;
    $music_id = $message->audio->file_id;
    $photo0_id = $message->photo[0]->file_id;
    $photo1_id = $message->photo[1]->file_id;
    $photo2_id = $message->photo[2]->file_id;
}
if(isset($update->callback_query)){
    $callback_query = $update->callback_query;
    $Data = $callback_query->data;
    $data_id = $callback_query->id;
    $chatid = $callback_query->message->chat->id;
    $iDpV = $callback_query->from->id;
    $tccall = $callback_query->message->chat->type;
    $messageid = $callback_query->message->message_id;
}
$now = date("Y-m-d-h-i-sa");
//--------[Json]--------//
@$list = json_decode(file_get_contents("data/list.json"),true);
@$data = json_decode(file_get_contents("data/data.json"),true);
//------------------------------------------------------------------------------
$get = Bot('GetChatMember',[
'chat_id'=>$data['channel'],
'user_id'=>$iD_pV]);
$rank = $get->result->status;
//------------------------------------------------------------------------------
if($data['button']['profile']['stats'] != "⛔️"){
	if($data['button']['profile']['name'] == null){
		$profile_key = "🎫 پروفایل";
	}else{
		$profile_key = $data['button']['profile']['name'];
	}
}
if($data['button']['contact']['stats'] != "⛔️"){
	if($data['button']['contact']['name'] == null){
		$contact_key = "ارسال شماره شما 📞";
	}else{
		$contact_key = $data['button']['contact']['name'];
	}
}
if($data['button']['location']['stats'] != "⛔️"){
	if($data['button']['location']['name'] == null){
		$location_key = "⚓️ ارسال مکان شما";
	}else{
		$location_key = $data['button']['location']['name'];
	}
}
//------------------------------------------------
if($data['button']['profile']['stats'] == "⛔️"){
	$profile_key = null;
}
if($data['button']['contact']['stats'] == "⛔️"){
	$contact_key = null;
}
if($data['button']['location']['stats'] == "⛔️"){
	$location_key = null;
}
//--------[Buttons]--------//
if($profile_key == null and $contact_key == null and $location_key == null){
	$button_user = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);
}else{
    foreach($data['buttons'] as $key => $name){
        $name = $data['buttons'][$key];
        $button_user[] = [['text'=>"$name"]];
    }
    $button_user[] = [ ['text'=>"$profile_key"] ];
    $button_user[] = [ ['text'=>"$contact_key",'request_contact' => true],['text'=>"$location_key",'request_location' => true] ];
    $button_user = json_encode(['keyboard'=> $button_user ,'resize_keyboard'=>true]);
}

if($data['stats'] == "off"){
$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"✅ روشن کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
],'resize_keyboard'=>true]);
}else{
$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"❎ خاموش کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
],'resize_keyboard'=>true]);
}
//------------------------------------------------------Zir Menu
$tanzhadi = json_encode(['keyboard'=>[
[['text'=>"📥 دانلودر"],['text'=>"📤 آپلودر"]],
[['text'=>"📣 قفل کانال"],['text'=>"💡 جعبه ابزار"]],
[['text'=>"⚜ کانتکت"],['text'=>"📯 فیلتر کلمه"]],
[['text'=>"👨🏻‍💻 ادمین ها"],['text'=>"🔐 قفل ها"]],
[['text'=>"🔘 بهینه سازی"],['text'=>"🗄 پشتیبان گیری"]],
[['text'=>"🀄️ اطلاعات کاربر"],['text'=>"▪️ کاربران اخیر"]],
[['text'=>"📢 آپدیت های اخیر"],['text'=>"💮 ریست ربات"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$peygham = json_encode(['keyboard'=>[
[['text'=>"🔸 متن پیش فرض"],['text'=>"🔹 متن شروع"]],
[['text'=>"🎟 متن پروفایل"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$quick = json_encode(['keyboard'=>[
[['text'=>"➖ حذف کلمه"],['text'=>"➕ افزودن کلمه"]],
[['text'=>"📑 لیست پاسخ ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button = json_encode(['keyboard'=>[
[['text'=>"➖ حذف دکمه"],['text'=>"➕ افزودن دکمه"]],
[['text'=>"⚜ نام دکمه های سیستمی"],['text'=>"📌 وضعیت نمایش دکمه ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button_tools = json_encode(['keyboard'=>[
[['text'=>"🖼 استیکر به عکس"],['text'=>"🎐 عکس به استیکر"]],
[['text'=>"🙏🏻 فال حافظ"],['text'=>"🌐 مترجم"]],
[['text'=>"🖊 طراحی متن"],['text'=>"🎪 عکس تصادفی"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$languages = json_encode(['keyboard'=>[
[['text'=>"🇵🇳 انگلیسی"],['text'=>"🇮🇷 فارسی"]],
[['text'=>"🇷🇺 روسی"],['text'=>"🇸🇦 عربی"]],
[['text'=>"🇹🇷 ترکی"],['text'=>"🇫🇷 فرانسوی"]],
[['text'=>"↩️ برگشت به جعبه ابزار"]]
],'resize_keyboard'=>true]);
$button_name = json_encode(['keyboard'=>[
[['text'=>"دکمه پروفایل"]],
[['text'=>"دکمه ارسال شماره"],['text'=>"دکمه ارسال مکان"]],
[['text'=>"↩️ بازگشت"]]
],'resize_keyboard'=>true]);
$button_filter = json_encode(['keyboard'=>[
[['text'=>"➖ حذف فیلتر"],['text'=>"➕ افزودن فیلتر"]],
[['text'=>"📑 لیست فیلتر"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$button_admins = json_encode(['keyboard'=>[
[['text'=>"➖ حذف ادمین"],['text'=>"➕ افزودن ادمین"]],
[['text'=>"👥 لیست ادمین ها"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$reset = json_encode(['keyboard'=>[
[['text'=>"بله، مطمئن هستم"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$contact = json_encode(['keyboard'=>[
[['text'=>"📞 شماره من"]],
[['text'=>"☎️ تنظیم شماره",'request_contact'=>true]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
//------------------------------------------------------Back
$back = json_encode(['keyboard'=>[
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$backans = json_encode(['keyboard'=>[
[['text'=>"↩️ برگشت "]]
],'resize_keyboard'=>true]);
$backbtn = json_encode(['keyboard'=>[
[['text'=>"↩️ بازگشت"]]
],'resize_keyboard'=>true]);
$backto = json_encode(['keyboard'=>[
[['text'=>"↩️ برگشت به جعبه ابزار"]]
],'resize_keyboard'=>true]);
//------------------------------------------------------Inline
$profile_btn = $data['button']['profile']['stats'];
$contact_btn = $data['button']['contact']['stats'];
$location_btn = $data['button']['location']['stats'];
$btnstats = json_encode(['inline_keyboard'=>[
[['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
[['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
[['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
]]);
//------------------------------------------------------
$remove = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);
//------------------------------------------------------------------------------
if(in_array($iD_pV, $list['ban'])){
	exit();
}
if(($tc == 'group' | $tc == 'supergroup') and $data['feed'] != null and $pV_iD != $data['feed']){
	SendMessage($pV_iD,"■ ربات اجازه ورود به هیچ گروهی را ندارد!", 'Html');
	Bot('LeaveChat',['chat_id'=> $pV_iD]);
	exit();
}
@$flood = json_decode(file_get_contents("data/flood.json"),true);
@$floods = $flood['flood']["$now-$iD_pV"];
@$flood['flood']["$now-$iD_pV"] = $floods+1;
@file_put_contents("data/flood.json",json_encode($flood));
@$flood = json_decode(file_get_contents("data/flood.json"),true);
@$floods = $flood['flood']["$now-$iD_pV"];

if($floods >= 3 and $iD_pV != $DeRtK and $tc == 'private'){
    if($list['ban'] == null){
        $list['ban'] = [];
    }
    unlink("data/flood.json");
	array_push($list['ban'], $iD_pV);
	file_put_contents("data/list.json",json_encode($list));
	SendMessage($iD_pV,"■ شما به علت اسپم، از ربات مسدود شدید.", 'MarkDown', null, $remove);
	SendMessage($DeRtK,"■ کاربر [$iD_pV](tg://user?id=$iD_pV) به علت اسپم از ربات مسدود گردید.", 'MarkDown');
}
elseif($data['stats'] == "off" and $iD_pV != $DeRtK and $tc == 'private'){
	SendMessage($pV_iD,"■ ربات تا اطلاع ثانوی توسط ادمین خاموش می باشد\n■ لطفا پیام خود را در وقتی دیگر ارسال کنید", null, $message_id);
	exit();
}
//--------[User]--------//
if(!file_exists("data/$pV_iD")){
mkdir("data/$pV_iD");
}
elseif(preg_match('/^\/(start)$/i',$HbNsMl) and $iD_pV != $DeRtK and $tc == 'private'){
	$start = $data['text']['start'];
	if($start != null){
		$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
		$date = $get['date'];
		$today = $get['today'];
		
		$start = str_replace('F-NAME', "$first_name", $start);
		$start = str_replace('L-NAME', "$last_name", $start);
		$start = str_replace('U-NAME', "$username", $start);
		$start = str_replace('TIME', date('H:i:s'), $start);
		$start = str_replace('DATE', $date, $start);
		$start = str_replace('TODAY', $today, $start);
		SendMessage($pV_iD,$start, null, $message_id, $button_user);
	}else{
		SendMessage($pV_iD,"■ سلام، به ربات من خوش آمدید.", null, $message_id,$button_user);
	}
}
elseif(preg_match('/^\/(creator)/i',$HbNsMl) and $iD_pV != $DeRtK){
if(file_exists("../../SlowCreate/UsErBoT/$BotID/creator.txt")){
$creator = file_get_contents("../../SlowCreate/UsErBoT/$BotID/creator.txt");
}
SendMessage($pV_iD,"$creator", null, $message_id);
}
elseif(($rank == 'left' and $iD_pV != $DeRtK) && ($data['lock']['channel'] == "✅" and $data['channel'] != null)){
	SendMessage($pV_iD,"■ دوست عزیز برای استفاده از ربات ابتدا وارد کانال :\n\n➔ ".$data['channel']."\n\n■ شوید و سپس به ربات برگشته و /start بزنید.", null, $message_id, $remove);
}
elseif($HbNsMl == $profile_key and isset($HbNsMl) and $tc == 'private'){
	$profile = isset($data['text']['profile']) ? $data['text']['profile'] : "■ پروفایل خالی است!";
	if($iD_pV == $DeRtK){
		SendMessage($pV_iD,$profile, null, $message_id);
	}else{
		SendMessage($pV_iD,$profile, null, $message_id, $button_user);
	}
}
elseif($data['quick'][$HbNsMl] != null and $iD_pV != $DeRtK and $tc == 'private'){
	$answer = $data['quick'][$HbNsMl];
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$answer = str_replace('F-NAME', "$first_name", $answer);
	$answer = str_replace('L-NAME', "$last_name", $answer);
	$answer = str_replace('U-NAME', "$username", $answer);
	$answer = str_replace('TIME', date('H:i:s'), $answer);
	$answer = str_replace('DATE', $date, $answer);
	$answer = str_replace('TODAY', $today, $answer);
	SendMessage($pV_iD,$answer, null, $message_id, $button_user);
}
elseif($data['buttonans'][$HbNsMl] != null and $tc == 'private'){
	if($iD_pV != $DeRtK){
		$ansbtn = $data['buttonans'][$HbNsMl];
		SendMessage($pV_iD,"$ansbtn", null, $message_id, $button_user);
	}else{
		if($data['step'] == "none"){
			$ansbtn = $data['buttonans'][$HbNsMl];
			SendMessage($pV_iD,"$ansbtn", null, $message_id);
		}
	}
}
elseif(isset($update->message) and $iD_pV != $DeRtK and $data['feed'] == null && $tc == 'private'){
	$done = isset($data['text']['done'])?$data['text']['done'] : "■ پیام ارسال شد.";
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$done = str_replace('F-NAME', "$first_name", $done);
	$done = str_replace('L-NAME', "$last_name", $done);
	$done = str_replace('U-NAME', "$username", $done);
	$done = str_replace('TIME', date('H:i:s'), $done);
	$done = str_replace('DATE', $date, $done);
	$done = str_replace('TODAY', $today, $done);
	if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
		if($data['lock']['forward'] == "✅"){
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			exit();
		}
	}
	if(isset($message->text)){
		if($data['lock']['text'] != "✅"){
			$checklink = CheckLink($HbNsMl);
			$checkfilter = CheckFilter($HbNsMl);
			if($checklink != true){
				if($checkfilter != true){
					Forward($DeRtK, $pV_iD, $message_id);
					SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
					if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
						SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
					}
				}
			}
			if($checklink == true){
				SendMessage($pV_iD,"🔐 ارسال لینک توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			}
			if($checkfilter == true){
				SendMessage($pV_iD,"■ پیام شما به دلیل داشتن کلمه غیر مجاز ارسال نشد!", 'Html' ,$message_id, $button_user);
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->photo)){
		if($data['lock']['photo'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->video)){
		if($data['lock']['video'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->voice)){
		if($data['lock']['voice'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->audio)){
		if($data['lock']['audio'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->sticker)){
		if($data['lock']['sticker'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->document)){
		if($data['lock']['document'] != "✅"){
			Forward($DeRtK, $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($DeRtK,"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
}
//--------[Feed]--------//
elseif(preg_match('/^\/(setfeed)$/i',$HbNsMl) and ($tc == 'group' | $tc == 'supergroup') and $iD_pV == $DeRtK){
    $data['feed'] = "$pV_iD";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ این گروه برای پشتیبانی تنظیم گردید.", 'Html' ,$message_id, $remove);
}
elseif(preg_match('/^\/(delfeed)$/i',$HbNsMl) and $tc == 'private' and $iD_pV == $DeRtK){
    unset($data['feed']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ گروه پشتیبانی حذف شد و پیام ها به ادمین ارسال خواهد شد.", 'Html' ,$message_id);
}
elseif(isset($update->message) and $iD_pV != $DeRtK and $data['feed'] != null && $tc == 'private'){
	$done = isset($data['text']['done'])?$data['text']['done'] : "■ پیام ارسال شد.";
	
	$get = json_decode(file_get_contents("https://api.raminsudo.ir/time.php"), true);
	$date = $get['date'];
	$today = $get['today'];
		
	$done = str_replace('F-NAME', "$first_name", $done);
	$done = str_replace('L-NAME', "$last_name", $done);
	$done = str_replace('U-NAME', "$username", $done);
	$done = str_replace('TIME', date('H:i:s'), $done);
	$done = str_replace('DATE', $date, $done);
	$done = str_replace('TODAY', $today, $done);
	if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
		if($data['lock']['forward'] == "✅"){
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			exit();
		}
	}
	if(isset($message->text)){
		if($data['lock']['text'] != "✅"){
			$checklink = CheckLink($HbNsMl);
			$checkfilter = CheckFilter($HbNsMl);
			if($checklink != true){
				if($checkfilter != true){
					Forward($data['feed'], $pV_iD, $message_id);
					SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
					if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
						SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
					}
				}
			}
			if($checklink == true){
				SendMessage($pV_iD,"🔐 ارسال لینک توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
			}
			if($checkfilter == true){
				SendMessage($pV_iD,"■ پیام شما به دلیل داشتن کلمه غیر مجاز ارسال نشد!", 'Html' ,$message_id, $button_user);
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->photo)){
		if($data['lock']['photo'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->video)){
		if($data['lock']['video'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->voice)){
		if($data['lock']['voice'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->audio)){
		if($data['lock']['audio'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->sticker)){
		if($data['lock']['sticker'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
	if(isset($message->document)){
		if($data['lock']['document'] != "✅"){
			Forward($data['feed'], $pV_iD, $message_id);
			SendMessage($pV_iD,"$done", 'Html' ,$message_id, $button_user);
			if(isset($update->message->forward_from) or isset($update->message->forward_from_chat)){
				SendMessage($data['feed'],"■ ارسال کننده پیام فوق : [$iD_pV](tg://user?id=$iD_pV)", 'MarkDown');
			}
		}else{
			SendMessage($pV_iD,"🔐 ارسال این نوع رسانه توسط ادمین محدود شده!", 'Html' ,$message_id, $button_user);
		}
	}
}
elseif(isset($reply) and (in_array($iD_pV, $list['admin']) | $iD_pV == $DeRtK) and $pV_iD == $data['feed']){
    if($reply_id == GetMe()->result->id)
	if(preg_match('/^\/(ban)$/i',$HbNsMl)){
		if(!in_array($reply, $list['ban'])){
		    if($list['ban'] == null){
                $list['ban'] = [];
            }
			array_push($list['ban'], $reply);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($pV_iD,"■ کاربر مورد نظر از ربات مسدود شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
		}else{
			SendMessage($pV_iD,"■ کاربر از قبل مسدود شده بود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(unban)$/i',$HbNsMl)){
		if(in_array($reply, $list['ban'])){
			$search = array_search($reply, $list['ban']);
			unset($list['ban'][$search]);
			$list['ban'] = array_values($list['ban']);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($pV_iD,"■ کاربر مورد نظر از ربات از مسدودیت خارج شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
		}else{
			SendMessage($pV_iD,"■ کاربر از قبل مسدود نبود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(share)$/i',$HbNsMl)){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
		if($phone != null and $name != null){
			SendContact($reply, $name, $phone);
			SendMessage($pV_iD,"■ شماره شما با موفقیت برای کاربر به اشتراک گذاشته شد.", 'MarkDown', $message_id);
		}else{
			SendMessage($pV_iD,'■ شماره شما تنظیم نشده!'.PHP_EOL.'■ برای این کار ابتدا شماره خود را از بخش "کانتکت" تنظیم کنید.', 'MarkDown', $message_id);
		}
	}
	elseif(isset($message)){
		if($HbNsMl != null){
			SendMessage($reply,$HbNsMl,null);
		}
		elseif($voice_id != null){
			SendVoice($reply,$voice_id,$caption);
		}
		elseif($file_id != null){
			SendDocument($reply,$file_id,$caption);
		}
		elseif($music_id != null){
			SendAudio($reply,$music_id,$caption);
		}
		elseif($photo2_id != null){
			SendPhoto($reply,$photo2_id,$caption);
		}
		elseif($photo1_id != null){
			SendPhoto($reply,$photo1_id,$caption);
		}
		elseif($photo0_id != null){
			SendPhoto($reply,$photo0_id,$caption);
		}
		elseif($video_id != null){
			SendVideo($reply,$video_id,$caption);
		}
		elseif($sticker_id != null){
			SendSticker($reply,$sticker_id);
		}
		SendMessage($pV_iD,"■ _پیام مورد نظر ارسال شد_", 'MarkDown', $message_id);
	}
}
//--------[Dev]--------//
if(($iD_pV == $DeRtK | $iDpV == $DeRtK) and ($tc == 'private' | $tccall == 'private')){
if($HbNsMl == '▫️ برگشت ▫️' | $HbNsMl == '❄️ مدیریت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ چه کمکی میتونم بهتون کنم قربان؟", 'MarkDown' ,$message_id, $panel);
}
elseif($HbNsMl == '⏹ غیرفعال کردن حالت مدیریت' | preg_match('/^\/(start)$/i',$HbNsMl)){
	foreach($data['buttons'] as $key => $name){
        $name = $data['buttons'][$key];
        $manage_off[] = [['text'=>"$name"]];
    }
    $manage_off[] = [ ['text'=>"$profile_key"] ];
    $manage_off[] = [ ['text'=>"$contact_key",'request_contact' => true],['text'=>"$location_key",'request_location' => true]];
    $manage_off[] = [ ['text'=>"❄️ مدیریت"]];
    $manage_off = json_encode(['keyboard'=> $manage_off ,'resize_keyboard'=>true]);
	SendMessage($pV_iD,"■ حالت مدیریت غیرفعال گردید.", 'MarkDown' ,$message_id, $manage_off);
}
elseif(isset($reply)){
	if(preg_match('/^\/(ban)$/i',$HbNsMl)){
		if(!in_array($reply, $list['ban'])){
		    if($list['ban'] == null){
                $list['ban'] = [];
            }
			array_push($list['ban'], $reply);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($pV_iD,"■ کاربر مورد نظر از ربات مسدود شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
		}else{
			SendMessage($pV_iD,"■ کاربر از قبل مسدود شده بود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(unban)$/i',$HbNsMl)){
		if(in_array($reply, $list['ban'])){
			$search = array_search($reply, $list['ban']);
			unset($list['ban'][$search]);
			$list['ban'] = array_values($list['ban']);
			file_put_contents("data/list.json",json_encode($list));
			SendMessage($pV_iD,"■ کاربر مورد نظر از ربات از مسدودیت خارج شد.", 'MarkDown', $message_id);
			SendMessage($reply,"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
		}else{
			SendMessage($pV_iD,"■ کاربر از قبل مسدود نبود.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^\/(share)$/i',$HbNsMl)){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
		if($phone != null and $name != null){
			SendContact($reply, $name, $phone);
			SendMessage($pV_iD,"■ شماره شما با موفقیت برای کاربر به اشتراک گذاشته شد.", 'MarkDown', $message_id);
		}else{
			SendMessage($pV_iD,'■ شماره شما تنظیم نشده!'.PHP_EOL.'■ برای این کار ابتدا شماره خود را از بخش "کانتکت" تنظیم کنید.', 'MarkDown', $message_id);
		}
	}
	elseif(isset($message)){
		if($HbNsMl != null){
			SendMessage($reply,$HbNsMl,null);
		}
		elseif($voice_id != null){
			SendVoice($reply,$voice_id,$caption);
		}
		elseif($file_id != null){
			SendDocument($reply,$file_id,$caption);
		}
		elseif($music_id != null){
			SendAudio($reply,$music_id,$caption);
		}
		elseif($photo2_id != null){
			SendPhoto($reply,$photo2_id,$caption);
		}
		elseif($photo1_id != null){
			SendPhoto($reply,$photo1_id,$caption);
		}
		elseif($photo0_id != null){
			SendPhoto($reply,$photo0_id,$caption);
		}
		elseif($video_id != null){
			SendVideo($reply,$video_id,$caption);
		}
		elseif($sticker_id != null){
			SendSticker($reply,$sticker_id);
		}
		SendMessage($pV_iD,"■ _پیام مورد نظر ارسال شد_", 'MarkDown', $message_id);
	}
}
elseif($HbNsMl == '📊 آمار ربات'){
	$countban = count(array_unique($list['ban']));
	$countuser = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $countuser++;
	    }
	}
	$countupdate = $data['count']['update'];
	SendMessage($pV_iD,"■ تعداد کل اعضای ربات : *$countuser*\n■ تعداد افراد مسدود شده : *$countban*\n■ کل آپدیت های ارسالی از سمت تلگرام : *$countupdate*", 'MarkDown', $message_id);
}
elseif($HbNsMl == '📑 لیست پاسخ ها'){
	$quick = $data['quick'];
	if($quick != null){
		$str = null;
		foreach($quick as $word => $answer){
			$str .= "کلمه ($word) | پاسخ ($answer)\n";
		}
		SendMessage($pV_iD,"■ لیست پاسخ های سریع :\n$str", 'Html', $message_id);
	}else{
		SendMessage($pV_iD,"■ لیست پاسخ های سریع خالی می باشد!", 'Html', $message_id);
	}
}
elseif($HbNsMl == '📑 لیست فیلتر'){
	$filters = $data['filters'];
	if($filters != null){
		$im = implode(PHP_EOL, $filters);
		SendMessage($pV_iD,"■ لیست کلمات فیلتر شده :\n$im", 'Html', $message_id);
	}else{
		SendMessage($pV_iD,"■ لیست فیلتر خالی می باشد!", 'Html', $message_id);
	}
}
elseif($HbNsMl == '🔐 قفل ها'){
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$HbNsMl = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	
	if($video == null){
		$data['lock']['video'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($audio == null){
		$data['lock']['audio'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($voice == null){
		$data['lock']['voice'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($HbNsMl == null){
		$data['lock']['text'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($sticker == null){
		$data['lock']['sticker'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($link == null){
		$data['lock']['link'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($photo == null){
		$data['lock']['photo'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($document == null){
		$data['lock']['document'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($forward == null){
		$data['lock']['forward'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($channel == null){
		$data['lock']['channel'] = "❌";
		file_put_contents("data/data.json",json_encode($data));
	}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$HbNsMl = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"$video",'callback_data'=>"video"],['text'=>"🎥 قفل ویدیو",'callback_data'=>"view1"]],
    [['text'=>"$audio",'callback_data'=>"audio"],['text'=>"🎵 قفل موسیقی",'callback_data'=>"view2"]],
    [['text'=>"$voice",'callback_data'=>"voice"],['text'=>"🔊 قفل ویس",'callback_data'=>"view3"]],
    [['text'=>"$HbNsMl",'callback_data'=>"text"],['text'=>"🏷 قفل متن",'callback_data'=>"view4"]],
    [['text'=>"$sticker",'callback_data'=>"sticker"],['text'=>"😺 قفل استیکر",'callback_data'=>"view5"]],
    [['text'=>"$link",'callback_data'=>"link"],['text'=>"🔗 قفل لینک",'callback_data'=>"view6"]],
    [['text'=>"$photo",'callback_data'=>"photo"],['text'=>"🖼 قفل عکس",'callback_data'=>"view7"]],
    [['text'=>"$document",'callback_data'=>"document"],['text'=>"🗂 قفل فایل",'callback_data'=>"view8"]],
    [['text'=>"$forward",'callback_data'=>"forward"],['text'=>"⤴️ قفل فروارد",'callback_data'=>"view9"]],
    [['text'=>"$channel",'callback_data'=>"channel"],['text'=>"📢 قفل کانال",'callback_data'=>"view10"]],
    ]]);
	SendMessage($pV_iD,"■ برای قفل و یا بازکردن روی دکمه های سمت چپ کلیک کنید.همچنین میتونید مفهوم اموجی هارو در زیر مشاهده کنید.\n👈 قفل : ✅ | 👈 آزاد : ❌", 'MarkDown', $message_id, $btnstats);
}
elseif($HbNsMl == '📌 وضعیت نمایش دکمه ها'){
	$profile_btn = $data['button']['profile']['stats'];
	$contact_btn = $data['button']['contact']['stats'];
	$location_btn = $data['button']['location']['stats'];
	
	if($profile_btn == null){
		$data['button']['profile']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($contact_btn == null){
		$data['button']['contact']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	if($location_btn == null){
		$data['button']['location']['stats'] = "✅";
		file_put_contents("data/data.json",json_encode($data));
	}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$profile_btn = $data['button']['profile']['stats'];
	$contact_btn = $data['button']['contact']['stats'];
	$location_btn = $data['button']['location']['stats'];
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
    [['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
    [['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
    ]]);
	SendMessage($pV_iD,"■ برای تغییر وضعیت هر کدام، روی آن بزنید", 'MarkDown', $message_id, $btnstats);
}
elseif(isset($Data)){
	$locks = ['video','audio','voice','text','sticker','link','photo','document','forward','channel'];
	if(in_array($Data, $locks)){
		$media = $data['lock'][$Data];
		if($media == "❌"){
			$data['lock'][$Data] = "✅";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			if($media == "✅"){
				$data['lock'][$Data] = "❌";
				file_put_contents("data/data.json",json_encode($data));
			}
		}
	$data = json_decode(file_get_contents("data/data.json"),true);
	$video = $data['lock']['video'];
	$audio = $data['lock']['audio'];
	$voice = $data['lock']['voice'];
	$HbNsMl = $data['lock']['text'];
	$sticker = $data['lock']['sticker'];
	$link = $data['lock']['link'];
	$photo = $data['lock']['photo'];
	$document = $data['lock']['document'];
	$forward = $data['lock']['forward'];
	$channel = $data['lock']['channel'];
	$btnstats = json_encode(['inline_keyboard'=>[
    [['text'=>"$video",'callback_data'=>"video"],['text'=>"🎥 قفل ویدیو",'callback_data'=>"view1"]],
    [['text'=>"$audio",'callback_data'=>"audio"],['text'=>"🎵 قفل موسیقی",'callback_data'=>"view2"]],
    [['text'=>"$voice",'callback_data'=>"voice"],['text'=>"🔊 قفل ویس",'callback_data'=>"view3"]],
    [['text'=>"$HbNsMl",'callback_data'=>"text"],['text'=>"🏷 قفل متن",'callback_data'=>"view4"]],
    [['text'=>"$sticker",'callback_data'=>"sticker"],['text'=>"😺 قفل استیکر",'callback_data'=>"view5"]],
    [['text'=>"$link",'callback_data'=>"link"],['text'=>"🔗 قفل لینک",'callback_data'=>"view6"]],
    [['text'=>"$photo",'callback_data'=>"photo"],['text'=>"🖼 قفل عکس",'callback_data'=>"view7"]],
    [['text'=>"$document",'callback_data'=>"document"],['text'=>"🗂 قفل فایل",'callback_data'=>"view8"]],
    [['text'=>"$forward",'callback_data'=>"forward"],['text'=>"⤴️ قفل فروارد",'callback_data'=>"view9"]],
    [['text'=>"$channel",'callback_data'=>"channel"],['text'=>"📢 قفل کانال",'callback_data'=>"view10"]],
    ]]);
	AnswerCallbackQuery($data_id,"■ انجام شد.");
	EditKeyboard($chatid, $messageid, $btnstats);
	}
	elseif($Data == "profile" | $Data == "contact" | $Data == "location"){
		$btn = $data['button'][$Data]['stats'];
		
		if($btn == "⛔️"){
			$data['button'][$Data]['stats'] = "✅";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			if($btn == "✅"){
				$data['button'][$Data]['stats'] = "⛔️";
				file_put_contents("data/data.json",json_encode($data));
			}
		}
		
		$profile_btn = $data['button']['profile']['stats'];
		$contact_btn = $data['button']['contact']['stats'];
		$location_btn = $data['button']['location']['stats'];
		$btnstats = json_encode(['inline_keyboard'=>[
		[['text'=>"دکمه پروفایل $profile_btn",'callback_data'=>"profile"]],
		[['text'=>"دکمه ارسال شماره $contact_btn",'callback_data'=>"contact"]],
		[['text'=>"دکمه ارسال مکان $location_btn",'callback_data'=>"location"]],
		]]);
		AnswerCallbackQuery($data_id,"■ انجام شد.");
		EditKeyboard($chatid, $messageid, $btnstats);
	}
}
elseif($HbNsMl == '📕 راهنما'){
	SendMessage($pV_iD,"▪️*/Ban* مسدود کاربر (ریپلی)\n ▪️*/UnBan* (ریپلی) رفع مسدود کاربر\n\n▪️ */Ban* (آیدی) مسدود کاربر\n▪️ */UnBan* (آیدی) رفع مسدود کاربر\n\n▪️ */Share* (ریپلی) اشتراک گذاری شماره\n*----------------------------------*\n👈 همواره شما می توانید در پیغام های 'شروع' و 'پیشفرض' و 'پاسخ های سریع' خود از متغیر های زیر استفاده نمایید تا مقدار های اصلی آن ها در مطلب نمایش داده شود :\n▫️ `F-NAME` | نام شخص\n▫️ `L-NAME` | نام خانوادگی شخص\n▫️ `U-NAME` | نام کاربری شخص بدون @\n▫️ `TIME` | ساعت ایران\n▫️ `DATE` | تاریخ ایران\n▫️ `TODAY` | روز هفته\n*----------------------------------*\n● */SetFeed* تنظیم گروه پشتیبانی\n•ربات رو به گروه مورد نظر اد کرده و این دستور در گروه مورد نظر ارسال گردد.\n\n● */DelFeed* حذف گروه پشتیبانی و ارسال پیام ها به پیوی\n• این دستور در پیوی ربات ارسال گردد.", 'MarkDown', $message_id);
}
elseif($HbNsMl == 'سایر تنظیمات🛠'){
	SendMessage($pV_iD,"به بخش تنظیمات خوش آمدید.", 'MarkDown',$message_id,$tanzhadi);
}
elseif($HbNsMl == '📢 آپدیت های اخیر'){
	$file = new CURLFile("data/updates.txt");
	SendDocument($pV_iD, $file, "■ آپدیت های اخیر ارسالی از سمت تلگرام");
}
elseif($HbNsMl == '▪️ کاربران اخیر'){
	$count = count($list['user'])-12;
	$lastmem = null;
	foreach($list['user'] as $key => $value){
		if($count <= $key){
			$lastmem .= "[$value](tg://user?id=$value) | ";
			$key++;
		}
	}
	SendMessage($pV_iD,"■ لیست *12* کاربر اخیر ربات به شرح ذیل می باشد :\n$lastmem", 'MarkDown', $message_id);
}
elseif($HbNsMl == '👥 لیست ادمین ها'){
    if(isset($list['admin'])){
	    $count = count($list['admin']);
	    $lastmem = null;
	    foreach($list['admin'] as $key => $value){
	    		$lastmem .= "[$value](tg://user?id=$value)\n";
	    }
	    SendMessage($pV_iD,"■ لیست کامل ادمین های ربات به شرح ذیل می باشد :\n$lastmem", 'MarkDown', $message_id);
    }else{
        SendMessage($pV_iD,"■ لیست ادمین ها خالی می باشد!", 'MarkDown', $message_id);
    }
}
elseif($HbNsMl == '🔘 بهینه سازی'){
	unset($data['id']);
	unset($data['word']);
	unset($data['step']);
	unlink("data/updates.txt");
	unlink("data/flood.json");
	array_map('unlink', glob('backup*'));
	file_put_contents("data/data.json",json_encode($data));
	
	SendMessage($pV_iD,"■ بهینه سازی با موفقیت انجام شد.", 'MarkDown', $message_id);
}
elseif($HbNsMl == '🗄 پشتیبان گیری'){
	SendMessage($pV_iD,"■ نسخه پشتیبان درحال آماده سازی است.\n■ منتظر بمانید ...", 'MarkDown', $message_id);
	
	copy('data/list.json','list.json');
	copy('data/data.json','data.json');
	$file_to_zip = array('list.json','data.json');
	$create = CreateZip($file_to_zip, "backup.zip");
	$zipfile = new CURLFile("backup.zip");
	$time = date('H:i:s');
	SendDocument($pV_iD, $zipfile, "■ آخرین نسخه پشتیبان\n> ساعت : $time");
	unlink('list.json');
	unlink('data.json');
	unlink('backup.zip');
	unlink('updates.txt');
}
elseif($HbNsMl == '✉️ پیغام ها' | $HbNsMl == '↩️ برگشت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به بخش تنظیم و مشاهده پیغام ها خوش آمدید", 'MarkDown', $message_id, $peygham);
}
elseif($HbNsMl == '📯 فیلتر کلمه' | $HbNsMl == '↩️  برگشـت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به بخش فیلتر کلمات خوش آمدید", 'MarkDown', $message_id, $button_filter);
}
elseif($HbNsMl == '📍 پاسخ سریع' | $HbNsMl == '↩️ برگشت '){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به بخش پاسخ سریع خوش آمدید", 'MarkDown',$message_id, $quick);
}
elseif($HbNsMl == '⚗ دکمه ها' | $HbNsMl == '↩️ بازگشت'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به بخش تنظیم و مشاهده وضعیت دکمه ها آمدید", 'MarkDown', $message_id, $button);
}
elseif($HbNsMl == '💡 جعبه ابزار' | $HbNsMl == '↩️ برگشت به جعبه ابزار'){
	$data['step'] = "none";
	unset($data['translate']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به جعبه ابزار خوش آمدید\n■ این بخش تنها جهت سرگرمی شما طراحی شده !", 'MarkDown', $message_id, $button_tools);
}
elseif($HbNsMl == '👨🏻‍💻 ادمین ها' | $HbNsMl == '↩️ برگشت به منوی ادمین ها'){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به منوی تنظیم ادمین ها خوش آمدید\n■ این بخش برای افراد مجاز پاسخگویی در گروه پشتیبانی طراحی شده !", 'MarkDown', $message_id, $button_admins);
}
elseif($HbNsMl == '⚜ نام دکمه های سیستمی'){
	SendMessage($pV_iD,"■ دکمه ای را برای تغییر نام انتخاب کنید", 'MarkDown', $message_id, $button_name);
}
elseif($HbNsMl == 'دکمه پروفایل' || $HbNsMl == 'دکمه ارسال شماره' || $HbNsMl == 'دکمه ارسال مکان'){
	$fa = array ('دکمه پروفایل','دکمه ارسال شماره','دکمه ارسال مکان');
	$en = array ('profile','contact','location');
	$str = str_replace($fa, $en, $HbNsMl);
	if($str == 'profile'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "🎫 پروفایل";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	if($str == 'contact'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "ارسال شماره شما 📞";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	if($str == 'location'){
	    if($data['button'][$str]['name'] == null){
		    $btnname = "⚓️ ارسال مکان شما";
	    }else{
		    $btnname = $data['button'][$str]['name'];
	    }
	}
	$data['step'] = "btn$str";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ شما هم اکنون درحال تغییر نام ( $HbNsMl ) هستید!\n■ نام فعلی دکمه : ( $btnname )", null, $message_id, $backbtn);
}
elseif($HbNsMl == '⚜ کانتکت'){
	SendMessage($pV_iD,"■ به بخش تنظیم و مشاهده کانتکت (شماره) خوش آمدید", 'MarkDown', $message_id, $contact);
}
elseif($HbNsMl == '📞 شماره من'){
	$name = $data['contact']['name'];
	$phone = $data['contact']['phone'];
	if($phone != null and $name != null){
		SendMessage($pV_iD,"■ شماره تنظیم شده فعلی :", 'MarkDown', $message_id, $contact);
		SendContact($pV_iD, $name, $phone);
	}else{
		SendMessage($pV_iD,'■ تا کنون شماره ای تنظیم نشده!'.PHP_EOL.'■ شما هم اکنون می توانید توسط دکمه "تنظیم شماره" شماره خود را تنظیم نمائید و به کاربر توسط دستور *share/* با ریپلی ارسال کنید', 'MarkDown', $message_id, $contact);
	}
}
elseif(isset($update->message->contact) and $data['step'] == "none" | $data['step'] == null){
	$name_contact = $update->message->contact->first_name;
	$number_contact = $update->message->contact->phone_number;
	
	$data['contact']['name'] = "$name_contact";
	$data['contact']['phone'] = "+$number_contact";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ _شماره ارسالی_ :\n*+$number_contact*\n■ با موفقیت تنظیم گردید.", 'MarkDown', $message_id, $contact);
}
elseif($HbNsMl == '💮 ریست ربات'){
	$data['step'] = "reset";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ با انجام این عمل کلیه سیستم ربات از جمله : اعضای ربات و تمامی داده های تنظیمی توسط شما از بین خواهند رفت!\n■ اگر اطمینان دارید دکمه زیر را لمس کنید", 'MarkDown', $message_id, $reset);
}
elseif($HbNsMl == 'بله، مطمئن هستم' and $data['step'] == "reset"){
	DeleteFolder("data");
	mkdir("data");
	SendMessage($pV_iD,"■ با موفقیت ریست شد.", 'MarkDown', $message_id, $panel);
}
elseif($HbNsMl == '✅ روشن کردن ربات'){
	$data['stats'] = "on";
	file_put_contents("data/data.json",json_encode($data));
	$panel = json_encode(['keyboard'=>[
[['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"❎ خاموش کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
    ],'resize_keyboard'=>true]);
	SendMessage($pV_iD,"■ ربات با موفقیت روشن شد و پیام کاربران دریافت خواهد شد.", 'MarkDown', $message_id, $panel);
}
elseif($HbNsMl == '❎ خاموش کردن ربات'){
	$data['stats'] = "off";
	file_put_contents("data/data.json",json_encode($data));
	$panel = json_encode(['keyboard'=>[
    [['text'=>"📕 راهنما"],['text'=>"📊 آمار ربات"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"📍 پاسخ سریع"],['text'=>"✉️ پیغام ها"]],
[['text'=>"✏️ پیام به کاربر"],['text'=>"⚗ دکمه ها"]],
[['text'=>"✅ روشن کردن ربات"]],
[['text'=>"سایر تنظیمات🛠"]],
[['text'=>"⏹ غیرفعال کردن حالت مدیریت"]]
    ],'resize_keyboard'=>true]);
	SendMessage($pV_iD,"■ ربات با موفقیت خاموش شد و پیام کاربران دریافت نخواهد شد.", 'MarkDown', $message_id, $panel);
}
//------------------------------------------------------------------------------
elseif($HbNsMl == '🎐 عکس به استیکر'){
	$data['step'] = "tosticker";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ عکس مورد نظر را جهت تبدیل به استیکر ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($HbNsMl == '🖼 استیکر به عکس'){
	$data['step'] = "tophoto";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ استیکر مورد نظر را جهت تبدیل شدن به عکس ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($HbNsMl == '🙏🏻 فال حافظ'){
	$pic = "http://www.beytoote.com/images/Hafez/".rand(1,149).".gif";
	SendPhoto($pV_iD,$pic,"■ با ذکر صلوات و فاحته ای جهت شادی روح 'حافظ شیرازی' فال تان را بخوانید.");
}
elseif($HbNsMl == '🌐 مترجم'){
	$data['step'] = "translate";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ متن مورد نظر را برای ترجمه ارسال کنید", 'MarkDown', $message_id, $backto);
}
elseif($HbNsMl == '🎪 عکس تصادفی'){
	$emojies = ['🎑', '🏞', '🌅', '🌄', '🌠', '🎇', '🎆', '🌇', '🏙', '🌌', '🌉'];
	SendPhoto($pV_iD, 'https://picsum.photos/500?random=' . rand(1, 2000), $emojies[mt_rand(0, count($emojies)-1)]);
}
elseif($HbNsMl == '🖊 طراحی متن'){
	$data['step'] = "write";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ متن مورد نظر را برای فونت دهی در قالب انگلیسی ارسال کنید", 'MarkDown', $message_id, $backto);
}
//------------------------------------------------------------------------------
elseif($HbNsMl == '🔹 متن شروع'){
	$data['step'] = "setstart";
	file_put_contents("data/data.json",json_encode($data));
	$start = $data['text']['start'];
	if($data['text']['start'] != null){
		$start = $data['text']['start'];
    }else{
    	$start = "■ سلام، به پیام رسان من خوش آمدید\nپیام خود را ارسال کنید :";
    }
	SendMessage($pV_iD,"■ پیغام فعلی شروع :\n<code>$start</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '🔸 متن پیش فرض'){
	$data['step'] = "setdone";
	file_put_contents("data/data.json",json_encode($data));
	if($data['text']['done'] != null){
		$done = $data['text']['done'];
    }else{
    	$done = "■ پیام ارسال شد.";
    }
	SendMessage($pV_iD,"■ پیغام فعلی پیش فرض :\n<code>$done</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '🎟 متن پروفایل'){
	$data['step'] = "setprofile";
	file_put_contents("data/data.json",json_encode($data));
	if($data['text']['profile'] != null){
		$profile = $data['text']['profile'];
    }else{
    	$profile = "■ پروفایل خالی است!";
    }
	SendMessage($pV_iD,"■ پیغام فعلی پروفایل :\n<code>$profile</code>\n■ جهت تغییر ، متن جدید را ارسال کنید.", 'Html', $message_id, json_encode(['keyboard'=>[[['text'=>"حذف پروفایل"]],[['text'=>"↩️ برگشت"]]],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '✏️ پیام به کاربر'){
	$data['step'] = "user";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ آیدی عددی فرد مربوطه را ارسال کرده یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, $back);
}
elseif($HbNsMl == '➕ افزودن کلمه'){
	$data['step'] = "addword";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ کلمه ای که می خواهید به آن پاسخ داده شود را ارسال کنید", 'MarkDown', $message_id, $backans);
}
elseif($HbNsMl == '➖ حذف کلمه'){
	$data['step'] = "delword";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ کلمه ای که می خواهید حذف شود را ارسال کنید", 'MarkDown', $message_id, $backans);
}
elseif($HbNsMl == '➕ افزودن فیلتر'){
	$data['step'] = "addfilter";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ کلمه ای که می خواهید فیلتر شود را ارسال کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️  برگشـت"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '➖ حذف فیلتر'){
	$data['step'] = "delfilter";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ کلمه ای که می خواهید از لیست فیلتر حذف شود را ارسال کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️  برگشـت"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '➕ افزودن ادمین'){
	$data['step'] = "addadmin";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت به منوی ادمین ها"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '➖ حذف ادمین'){
	$data['step'] = "deladmin";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ آیدی عددی فرد را ارسال کنید یا پیامی از شخص فروارد کنید", 'MarkDown', $message_id, json_encode(['keyboard'=>[ [['text'=>"↩️ برگشت به منوی ادمین ها"]] ],'resize_keyboard'=>true]));
}
elseif($HbNsMl == '➕ افزودن دکمه'){
	$data['step'] = "addbutton";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ نام دکمه ای که میخواهید اضافه شود را ارسال کنید", 'MarkDown', $message_id, $backbtn);
}
elseif($HbNsMl == '➖ حذف دکمه'){
	$data['step'] = "delbutton";
	file_put_contents("data/data.json",json_encode($data));
	if($data['buttons'] != null){
	    foreach($data['buttons'] as $key => $name){
            $name = $data['buttons'][$key];
            $delbuttons[] = [['text'=>"$name"]];
        }
        $delbuttons[] = [ ['text'=>"↩️ بازگشت"] ];
        $delbuttons = json_encode(['keyboard'=> $delbuttons ,'resize_keyboard'=>true]);
	    SendMessage($pV_iD,"■ قصد حذف کدام دکمه را دارید ؟", 'MarkDown', $message_id, $delbuttons);
	}else{
	    SendMessage($pV_iD,"■ شما هنوز هیچ دکمه ای اضافه نکرده اید!", 'MarkDown', $message_id, $button);
	}
}
elseif($HbNsMl == '📤 آپلودر'){
	$data['step'] = "upload";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ رسانه مورد نظر را برای آپلود ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($HbNsMl == '📥 دانلودر'){
	$data['step'] = "download";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ لینک مستقیم یا لینکی از اینستاگرام ارسال کنید تا برای شما ارسال شود", 'MarkDown', $message_id, $back);
}
elseif($HbNsMl == '📣 قفل کانال'){
	$data['step'] = "setchannel";
	file_put_contents("data/data.json",json_encode($data));
	$ch = $data['channel']? $data['channel'] : "تنظیم نشده !";
	SendMessage($pV_iD,"■ پیامی از کانال مورد نظر فروارد کنید.\n■ می توانید آیدی کانال را هم به طور دستی وارد کنید\n📢 کانال فعلی : $ch", 'Html', $message_id, $back);
}
elseif($HbNsMl == '🀄️ اطلاعات کاربر'){
	$data['step'] = "userinfo";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ آیدی عددی کاربر مورد نظر را ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($HbNsMl == '📬 ارسال همگانی'){
    $data['step'] = "s2all";
	file_put_contents("data/data.json",json_encode($data));
    SendMessage($pV_iD,"■ پیام مورد نظر را ارسال کنید", 'MarkDown', $message_id, $back);
}
elseif($HbNsMl == '📮 فروارد همگانی'){
    $data['step'] = "f2all";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ پیام مورد نظر را فروارد کنید", 'MarkDown', $message_id, $back);
}
//------------------------------------------------------------------------------
elseif($data['step'] == "tosticker" and isset($message->photo)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$photo = $message->photo;
	$file = $photo[count($photo)-1]->file_id;
    $get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
    file_put_contents("data/sticker.webp",fopen('https://api.telegram.org/file/bot'.TDKVSN.'/'.$patch, 'r'));
	SendSticker($pV_iD,new CURLFile("data/sticker.webp"));
	unlink("data/sticker.webp");
	SendMessage($pV_iD,"■ منوی جعبه ابزار :", 'MarkDown', $message_id, $button_tools);
}
elseif($data['step'] == "tophoto" and isset($message->sticker)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	$file = $message->sticker->file_id;
    $get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
    file_put_contents("data/photo.png",fopen('https://api.telegram.org/file/bot'.TDKVSN.'/'.$patch, 'r'));
	SendPhoto($pV_iD,new CURLFile("data/photo.png"));
	unlink("data/photo.png");
	SendMessage($pV_iD,"■ منوی جعبه ابزار :", 'MarkDown', $message_id, $button_tools);
}
elseif($data['step'] == "translate" and isset($HbNsMl)){
	$data['step'] = "translate0";
	$data['translate'] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ به چه زبانی ترجمه شود ؟", 'MarkDown', $message_id, $languages);
}
elseif($data['step'] == "translate0"){
	$langs = ["🇮🇷 فارسی","🇵🇳 انگلیسی","🇸🇦 عربی","🇷🇺 روسی","🇫🇷 فرانسوی","🇹🇷 ترکی"];
	if(in_array($HbNsMl, $langs)){
		$langs = ["🇮🇷 فارسی","🇵🇳 انگلیسی","🇸🇦 عربی","🇷🇺 روسی","🇫🇷 فرانسوی","🇹🇷 ترکی"];
		$langs_a = ["fa","en","ar","ru","fr","tr"];
		$lan = str_replace($langs, $langs_a, $HbNsMl);
		
		$fields = array('sl' => urlencode('auto'), 'tl' => urlencode($lan), 'q' => urlencode($data['translate']));
		
		$fields_string = '';
		
		foreach ($fields as $key => $value) {
			$fields_string .= '&' . $key . '=' . $value;
		}
		
		$ch = curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => 'https://translate.googleapis.com/translate_a/single?client=gtx&dt=t',
			CURLOPT_POSTFIELDS => $fields_string,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => 'UTF-8',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36(KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
		]);
		
		$res = json_decode(curl_exec($ch), true);
		
		foreach ($res[0] as $X => $Z) {
			if (!is_array($Z[0])) $result .= $Z[0];
		}
		
		
		if (!empty($result)) {
			sendMessage($pV_iD, $result, null, $message_id);
		} else {
			sendMessage($pV_iD, "❌ متاسفانه ترجمه انجام نشد.", null, $message_id);
		}
	}
	else {
		$data['step'] = "translate0";
		$data['translate'] = $HbNsMl;
		file_put_contents("data/data.json",json_encode($data));
		sendMessage($pV_iD, "🏳️‍🌈 به چه زبانی ترجمه شود ؟", 'markdown', $message_id, $languages);
	}
}
elseif($data['step'] == "write" and isset($HbNsMl)){
	$matn = strtoupper($HbNsMl);
		$Eng = ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M'];
		
		//Fonts
		$Font_1 = ['ⓠ', 'ⓦ', 'ⓔ', 'ⓡ', 'ⓣ', 'ⓨ', 'ⓤ', 'ⓘ', 'ⓞ', 'ⓟ', 'ⓐ', 'ⓢ', 'ⓓ', 'ⓕ', 'ⓖ', 'ⓗ', 'ⓙ', 'ⓚ', 'ⓛ', 'ⓩ', 'ⓧ', 'ⓒ', 'ⓥ', 'ⓑ', 'ⓝ', 'ⓜ'];
		$Font_2 = ['⒬', '⒲', '⒠', '⒭', '⒯', '⒴', '⒰', '⒤', '⒪', '⒫', '⒜', '⒮', '⒟', '⒡', '⒢', '⒣', '⒥', '⒦', '⒧', '⒵', '⒳', '⒞', '⒱', '⒝', '⒩', '⒨'];
		$Font_3 = ['🇶 ', '🇼 ', '🇪 ', '🇷 ', '🇹 ', '🇾 ', '🇺 ', '🇮 ', '🇴 ', '🇵 ', '🇦 ', '🇸 ', '🇩 ', '🇫 ', '🇬 ', '🇭 ', '🇯 ', '🇰 ', '🇱 ', '🇿 ', '🇽 ', '🇨 ', '🇻 ', '🇧 ', '🇳 ', '🇲 '];
		$Font_4 = ['զ', 'ա', 'ɛ', 'ʀ', 't', 'ʏ', 'ʊ', 'ɨ', 'օ', 'ք', 'a', 's', 'ɖ', 'ʄ', 'ɢ', 'ɦ', 'ʝ', 'ҡ', 'ʟ', 'ʐ', 'x', 'ᴄ', 'ʋ', 'ɮ', 'ռ', 'ʍ'];
		$Font_5 = ['ǫ', 'ᴡ', 'ᴇ', 'ʀ', 'ᴛ', 'ʏ', 'ᴜ', 'ɪ', 'ᴏ', 'ᴘ', 'ᴀ', 's', 'ᴅ', 'ғ', 'ɢ', 'ʜ', 'ᴊ', 'ᴋ', 'ʟ', 'ᴢ', 'x', 'ᴄ', 'ᴠ', 'ʙ', 'ɴ', 'ᴍ'];
		$Font_6 = ['ᑫ', 'ʷ', 'ᵉ', 'ʳ', 'ᵗ', 'ʸ', 'ᵘ', 'ᶦ', 'ᵒ', 'ᵖ', 'ᵃ', 'ˢ', 'ᵈ', 'ᶠ', 'ᵍ', 'ʰ', 'ʲ', 'ᵏ', 'ˡ', 'ᶻ', 'ˣ', 'ᶜ', 'ᵛ', 'ᵇ', 'ⁿ', 'ᵐ'];
		$Font_7 = ['ǫ', 'ш', 'ε', 'я', 'т', 'ч', 'υ', 'ı', 'σ', 'ρ', 'α', 'ƨ', 'ɔ', 'ғ', 'ɢ', 'н', 'נ', 'κ', 'ʟ', 'z', 'х', 'c', 'ν', 'в', 'п', 'м'];
		$Font_8 = ['φ', 'ω', 'ε', 'Ʀ', '†', 'ψ', 'u', 'ι', 'ø', 'ρ', 'α', 'Տ', 'ძ', 'δ', 'ĝ', 'h', 'j', 'κ', 'l', 'z', 'χ', 'c', 'ν', 'β', 'π', 'ʍ'];
		
		//Replace
		$font1 = str_replace($Eng, $Font_1, $matn);
		$font2 = str_replace($Eng, $Font_2, $matn);
		$font3 = trim(str_replace($Eng, $Font_3, $matn));
		$font4 = str_replace($Eng, $Font_4, $matn);
		$font5 = str_replace($Eng, $Font_5, $matn);
		$font6 = str_replace($Eng, $Font_6, $matn);
		$font7 = str_replace($Eng, $Font_7, $matn);
		$font8 = str_replace($Eng, $Font_8, $matn);

		if ($font1 != $HbNsMl) {
			$data['step'] = "none";
			file_put_contents("data/data.json",json_encode($data));
			sendMessage($pV_iD, "● `$font1`\n● `$font2`\n● `$font3`\n● `$font4`\n● `$font5`\n● `$font6`\n● `$font7`\n● `$font8`", 'markdown', $message_id, $button_tools);
		} else {
			sendMessage($pV_iD, "🇺🇸 تنها متن انگلیسی قابل قبول است.", 'markdown', $message_id);
		}
}
//------------------------------------------------------------------------------
elseif($data['step'] == "setstart" and isset($HbNsMl)){
	$data['step'] = "none";
	$data['text']['start'] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
}
elseif($data['step'] == "setdone" and isset($HbNsMl)){
	$data['step'] = "none";
	$data['text']['done'] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
}
elseif($data['step'] == "setprofile" and isset($HbNsMl)){
	$data['step'] = "none";
	if($HbNsMl != 'حذف پروفایل'){
		$data['text']['profile'] = "$HbNsMl";
		SendMessage($pV_iD,"■ ثبت شد", 'MarkDown', $message_id, $peygham);
	}else{
		unset($data['text']['profile']);
		SendMessage($pV_iD,"■ دکمه پروفایل خالی را نمایش خواهد داد.", 'MarkDown', $message_id, $peygham);
	}
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "user"){
	if(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			$data['step'] = "msg";
			$data['id'] = "$forward_id";
			file_put_contents("data/data.json",json_encode($data));
			SendMessage($pV_iD,"■ پیام مورد نظر را در هر قالبی ارسال کنید", 'MarkDown', $message_id, $back);
		}else{
			SendMessage($pV_iD,"■ خطا ، کاربر عضو ربات نیست و نمی تواند ربات به او پیام دهد!", 'MarkDown', $message_id, $panel);
		}
	}else{
		$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$HbNsMl);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		
		if($ok == true){
			$data['id'] = "$HbNsMl";
			$data['step'] = "msg";
			file_put_contents("data/data.json",json_encode($data));
			SendMessage($pV_iD,"■ پیام مورد نظر را در هر قالبی ارسال کنید", 'MarkDown', $message_id, $back);
		}else{
			SendMessage($pV_iD,"■ خطا ، کاربر یافت نشد!", 'MarkDown', $message_id, $panel);
		}
	}
}
elseif($data['step'] == "msg"){
	$id = $data['id'];
	
	if($forward_from != null){
		Forward($id,$pV_iD,$message_id);
	}
	elseif($video_id != null){
		SendVideo($id,$video_id,$caption);
	}
	elseif($voice_id != null){
		SendVoice($id,$voice_id,$caption);
	}
	elseif($file_id != null){
		SendDocument($id,$file_id,$caption);
	}
	elseif($music_id != null){
		SendAudio($id,$music_id,$caption);
	}
	elseif($photo2_id != null){
		SendPhoto($id,$photo2_id,$caption);
	}
	elseif($photo1_id != null){
		SendPhoto($id,$photo1_id,$caption);
	}
	elseif($photo0_id != null){
		SendPhoto($id,$photo0_id,$caption);
	}
	elseif($HbNsMl != null){
		SendMessage($id, $HbNsMl, null);
	}
	elseif($sticker_id != null){
		SendSticker($id,$sticker_id);
	}
	
	$data['step'] = "none";
	unset($data['id']);
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"■ پیام شما با موفقیت به کاربر ارسال شد.", null, $message_id, $panel);
}
elseif($data['step'] == "addword" and isset($HbNsMl)){
	$data['step'] = "ans";
	SendMessage($pV_iD,"■ پاسخی که باید برای ( $HbNsMl ) ارسال کنم را ارسال کنید", null, $message_id, $backans);
	$data['word'] = "$HbNsMl";
	$data['quick'][$HbNsMl] = null;
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "ans" and isset($HbNsMl)){
	$word = $data['word'];
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	SendMessage($pV_iD,"> کلمه : ( $word )\n> پاسخ : ( $HbNsMl )\n■ ثبت شد.", null, $message_id, $quick);
	$data['quick'][$word] = "$HbNsMl";
	unset($data['word']);
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "delword" and isset($HbNsMl)){
	if($data['quick'][$HbNsMl] != null){
		SendMessage($pV_iD,"■ کلمه ( $HbNsMl ) از لیست پاسخ سریع حذف گردید.", null, $message_id, $quick);
		$data['step'] = "none";
		unset($data['quick'][$HbNsMl]);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($pV_iD,"■ خطا، کلمه ارسالی شما یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "addfilter" and isset($HbNsMl)){
	if(!in_array($HbNsMl, $data['filters'])){
		$data['step'] = "none";
		SendMessage($pV_iD,"■ کلمه : ( $HbNsMl ) با موفقیت فیلتر شد.", null, $message_id, $button_filter);
		$data['filters'][] = "$HbNsMl";
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($pV_iD,"■ کلمه : ( $HbNsMl ) از قبل فیلتر بود!", null, $message_id);
	}
}
elseif($data['step'] == "delfilter" and isset($HbNsMl)){
	if(in_array($HbNsMl, $data['filters'])){
		SendMessage($pV_iD,"■ کلمه ( $HbNsMl ) از لیست پاسخ سریع حذف گردید.", null, $message_id, $button_filter);
		$data['step'] = "none";
		$search = array_search($HbNsMl, $data['filters']);
		unset($data['filters'][$search]);
		$data['filters'] = array_values($data['filters']);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($pV_iD,"■ خطا، کلمه ارسالی شما در لیست فیلتر یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "addadmin"){
    if(is_numeric($HbNsMl) == true){
	    $get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$HbNsMl);
    	$result = json_decode($get, true);
	    $ok = $result['ok'];
	    if($ok == true){
	        if(!in_array($HbNsMl, $list['admin'])){
	            if($list['admin'] == null){
                    $list['admin'] = [];
                }
	    	    array_push($list['admin'], $HbNsMl);
	    	    file_put_contents("data/list.json",json_encode($list));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$HbNsMl'>".GetChat($HbNsMl)->result->first_name."</a>";
	    	    SendMessage($pV_iD,"■ کاربر ($mention) در ربات ادمین شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($HbNsMl,"■ شما در ربات ادمین شدید و از الان مجاز به پاسخگویی در گروه پشتیبانی هستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$HbNsMl'>".GetChat($HbNsMl)->result->first_name."</a>";
	            SendMessage($pV_iD,"■ کاربر ($mention) از قبل ادمین بود!", 'Html', $message_id, $button_admins);
	        }
	    }else{
	    	SendMessage($pV_iD,"■ کاربری با آیدی *".$HbNsMl."* در ربات یافت نشد!", 'MarkDown', $message_id);
	    }
	    file_put_contents("data/data.json",json_encode($data));
    }
    elseif(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			if(!in_array($forward_id, $list['admin'])){
	            if($list['admin'] == null){
                    $list['admin'] = [];
                }
	    	    array_push($list['admin'], $forward_id);
	    	    file_put_contents("data/list.json",json_encode($list));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	    	    SendMessage($pV_iD,"■ کاربر ($mention) در ربات ادمین شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($forward_id,"■ شما در ربات ادمین شدید و از الان مجاز به پاسخگویی در گروه پشتیبانی هستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	            SendMessage($pV_iD,"■ کاربر ($mention) از قبل ادمین بود!", 'Html', $message_id, $button_admins);
	        }
		}else{
			SendMessage($pV_iD,"■ کاربری با آیدی *".$forward_id."* در ربات یافت نشد!", 'MarkDown', $message_id);
		}
		file_put_contents("data/data.json",json_encode($data));
	}
}
elseif($data['step'] == "deladmin"){
    if(is_numeric($HbNsMl) == true){
	    $get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$HbNsMl);
    	$result = json_decode($get, true);
	    $ok = $result['ok'];
	    if($ok == true){
	        if(in_array($HbNsMl, $list['admin'])){
	            $search = array_search($HbNsMl, $list['admin']);
		        unset($list['admin'][$search]);
		        $list['admin'] = array_values($list['admin']);
		        file_put_contents("data/list.json",json_encode($data));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$HbNsMl'>".GetChat($HbNsMl)->result->first_name."</a>";
	    	    SendMessage($pV_iD,"■ کاربر ($mention) از ادمینی برکنار شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($HbNsMl,"■ شما از ادمین بودن در ربات برکنار شدید و دیگر مجاز به پاسخگویی در گروه پشتیبانی نیستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$HbNsMl'>".GetChat($HbNsMl)->result->first_name."</a>";
	            SendMessage($pV_iD,"■ کاربر ($mention) از قبل ادمین نبود!", 'Html', $message_id, $button_admins);
	        }
	    }else{
	    	SendMessage($pV_iD,"■ کاربری با آیدی *".$HbNsMl."* در ربات یافت نشد!", 'MarkDown', $message_id);
	    }
	    file_put_contents("data/data.json",json_encode($data));
    }
    elseif(isset($forward)){
		$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$forward_id);
		$result = json_decode($get, true);
		$ok = $result['ok'];
		if($ok == true){
			if(in_array($forward_id, $list['admin'])){
	            $search = array_search($forward_id, $list['admin']);
		        unset($list['admin'][$search]);
		        $list['admin'] = array_values($list['admin']);
		        file_put_contents("data/list.json",json_encode($data));
	    	    $data['step'] = "none";
	    	    $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	    	    SendMessage($pV_iD,"■ کاربر ($mention) از ادمینی برکنار شد.", 'Html', $message_id, $button_admins);
	    	    SendMessage($forward_id,"■ شما از ادمین بودن در ربات برکنار شدید و دیگر مجاز به پاسخگویی در گروه پشتیبانی نیستید.", 'MarkDown', null);
	        }else{
	            $data['step'] = "none";
	            $mention = "<a href='tg://user?id=$forward_id'>".GetChat($forward_id)->result->first_name."</a>";
	            SendMessage($pV_iD,"■ کاربر ($mention) از قبل ادمین نبود!", 'Html', $message_id, $button_admins);
	        }
		}else{
			SendMessage($pV_iD,"■ کاربری با آیدی *".$forward_id."* در ربات یافت نشد!", 'MarkDown', $message_id);
		}
		file_put_contents("data/data.json",json_encode($data));
	}
}
elseif($data['step'] == "addbutton" and isset($HbNsMl)){
	$data['step'] = "ansbtn|$HbNsMl";
	SendMessage($pV_iD,"■ مطلب دکمه ( $HbNsMl ) را ارسال کنید", null, $message_id, $backbtn);
	$data['buttons'][] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
}
elseif(strpos($data['step'], "ansbtn") !== false and isset($HbNsMl)){
	$nambtn = str_replace("ansbtn|",null,$data['step']);
	$data['step'] = "none";
	SendMessage($pV_iD,"■ مطلب ( $HbNsMl ) برای دکمه ( $nambtn ) ثبت شد.", null, $message_id, $button);
	$data['buttonans'][$nambtn] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "delbutton" and isset($HbNsMl)){
	if(in_array($HbNsMl, $data['buttons'])){
		SendMessage($pV_iD,"■ دکمه ( $HbNsMl ) از لیست دکمه ها حذف گردید.", null, $message_id, $button);
		$data['step'] = "none";
		$search = array_search($HbNsMl, $data['buttons']);
		unset($data['buttons'][$search]);
		unset($data['buttonans'][$HbNsMl]);
		$data['buttons'] = array_values($data['buttons']);
		file_put_contents("data/data.json",json_encode($data));
	}else{
		SendMessage($pV_iD,"■ خطا، دکمه ارسالی شما در لیست دکمه ها یافت نشد!\n■ لطفا مجدد با دقت بیشتری ارسال کنید", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "upload" and isset($message)){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	
	if($sticker_id != null){
		$file = $sticker_id;
	}
	elseif($video_id != null){
		$file = $video_id;
	}
	elseif($voice_id != null){
		$file = $voice_id;
	}
	elseif($file_id != null){
		$file = $file_id;
	}
	elseif($music_id != null){
		$file = $music_id;
	}
	elseif($photo2_id != null){
		$file = $photo2_id;
	}
	elseif($photo1_id != null){
		$file = $photo1_id;
	}
	elseif($photo0_id != null){
		$file = $photo0_id;
	}
	
	$get = Bot('getFile',['file_id'=> $file]);
    $patch = $get->result->file_path;
	$file_link = 'https://api.telegram.org/file/bot'.TDKVSN.'/'.$patch;
	$short = json_decode(file_get_contents("http://api.beyond-dev.ir/shortLink?url=$file_link"), true);
	$google = $short['results']['google'];
	SendMessage($pV_iD,"■ با موفقیت آپلود شد.\n■ لینک کوتاه شده : $google", null, $message_id, $panel);
}
elseif ($data['step'] == "download" && isset($HbNsMl)) {
	if (preg_match('#https?\:\/\/www\.instagram\.com\/(p|tv)\/([a-zA-Z0-9\-\_]+)#isu', $HbNsMl, $matches)) {
		$page = curl_file_get_contents('https://www.instagram.com/p/' . $matches[2] . '/');
		if (!preg_match_all('@sharedData\s=\s(\{.+)\;<\/script>@ui', $page, $json)) {
			$get = ['ok' => false, 'description' => "URL invalid."];
			//sendMessage($pV_iD, $page . "\n" . json_encode($json, 128|256), null, $message_id);
			goto edame;
		}
		$result = [];
		$data = json_decode($json[1][0], true)['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
		if ($data['__typename'] == 'GraphSidecar') {
			$result['type'] = 'album';
			$result['caption'] = $data['edge_media_to_caption']['edges'][0]['node']['text'];
			$result['like'] = $data['edge_media_preview_like']['count'];
			$result['thumb'] = $data['display_url'];
			$i = 0;
			foreach ($data['edge_sidecar_to_children']['edges'] as $media) {
				$result['media'][$i]['type'] = ($media['node']['__typename'] == 'GraphVideo' ? 'video' : 'photo');
				$result['media'][$i]['url'] = (isset($media['node']['video_url']) ? $media['node']['video_url'] :$media['node']['display_url']);
				$i++;
			}
			$result['sender']['profile'] = $data['owner']['profile_pic_url'];
			$result['sender']['username'] = $data['owner']['username'];
			$result['sender']['full_name'] = $data['owner']['full_name'];
			$result['sender']['is_verified'] = $data['owner']['is_verified'];
		}
		elseif ($data['__typename'] == 'GraphVideo') {
			$result['type'] = 'video';
			$result['caption'] = $data['edge_media_to_caption']['edges'][0]['node']['text'];
			$result['like'] = $data['edge_media_preview_like']['count'];
			$result['thumb'] = $data['display_url'];
			$result['thumb_x'] = $data['thumbnail_src'];
			$result['url'] = $data['video_url'];
			$result['sender']['profile'] = $data['owner']['profile_pic_url'];
			$result['sender']['username'] = $data['owner']['username'];
			$result['sender']['full_name'] = $data['owner']['full_name'];
			$result['sender']['is_verified'] = $data['owner']['is_verified'];
		}
		elseif ($data['__typename'] == 'GraphImage') {
			$result['type'] = 'photo';
			$result['caption'] = $data['edge_media_to_caption']['edges'][0]['node']['text'];
			$result['like'] = $data['edge_media_preview_like']['count'];
			$result['url'] = $data['display_url'];
			$result['sender']['profile'] = $data['owner']['profile_pic_url'];
			$result['sender']['username'] = $data['owner']['username'];
			$result['sender']['full_name'] = $data['owner']['full_name'];
			$result['sender']['is_verified'] = $data['owner']['is_verified'];
		}
		$get = ['ok' => true, 'result' => $result];

		$caption = @$get['result']['caption'];
		$caption = str_replace(['<', '>'], null, $caption);
		$caption = preg_replace('|@([0-9A-Za-z\.\-\_]+)|', '<a href="https://instagram.com/\1/">@\1</a>', $caption);
		$caption = preg_replace('|#([^\s^@^#^\(^\).]+)|', '<a href="https://www.instagram.com/explore/tags/\1/">#\1</a>', $caption);
		$like = @number_format($get['result']['like']);
		$verfy = ($get['result']['sender']['is_verified'] == true ? '✅' : null);
		edame:
		if ($get['ok'] == false) {
			sendMessage($pV_iD, "❌ لینک پست اینستاگرام ارسالی نادرست است.", null, $message_id);
		}
		else {
			$data['step'] = "none";
			file_put_contents("data/data.json", json_encode($data));
			if ($get['result']['type'] == 'photo') {
				
				bot('sendPhoto',
				[
					'chat_id' => $pV_iD,
					'photo' => $get['result']['url'],
					'caption' => $caption,
					'parse_mode' => 'html',
					'reply_to_message_id' => $message_id,
					'reply_markup' => 
					json_encode(
						[
							'inline_keyboard' =>
							[
								[
									['text' => "❤️ $like", 'url' => 'https://www.instagram.com/p/' . $matches[2] . '/']
								],
								[
									['text' => $verfy . '👤 ' . (!empty($get['result']['sender']['full_name'])?$get['result']['sender']['full_name']:$get['result']['sender']['username']), 'url' => 'https://www.instagram.com/' . $get['result']['sender']['username'] . '/']
								]
							]
						]
						)

				]);
			}
			elseif ($get['result']['type'] == 'video') {
				$size = get_file_size($get['result']['url']);
				$human_size = humanFileSize($size);
				if ($size >= 50*1024*1024) {
					
					$url = $get['result']['url'];
					bot('sendPhoto',
					[
						'chat_id' => $pV_iD,
						'photo' => $get['result']['thumb'],
						'caption' => $caption,
						'parse_mode' => 'html',
						'reply_to_message_id' => $message_id,
						'reply_markup' => 
						json_encode(
							[
								'inline_keyboard' =>
								[
									[
										['text' => "❤️ $like", 'url' => 'https://www.instagram.com/p/' . $matches[2] . '/']
									],
									[
										['text' => $verfy . '👤 ' . (!empty($get['result']['sender']['full_name'])?$get['result']['sender']['full_name']:$get['result']['sender']['username']), 'url' => 'https://www.instagram.com/' . $get['result']['sender']['username'] . '/']
									]
								]
							]
						)
					]);
					sendMessage($pV_iD, "🚨 حجم ویدیو بیش از ۵۰ مگابایت است و توان ارسال آنرا ندارم.\n<a href='$url'>🌐 لینک مستقیم</a>\n💾 حجم ویدیو : $human_size", 'html', $message_id);
				} elseif ($size >= 20*1024*1024) {
					
					$file_name = time() . '.' . pathinfo($get['result']['url'])['extension'];
					copy($get['result']['url'], $file_name);
					
					bot('sendVideo',
					[
						'chat_id' => $pV_iD,
						'video' => new CURLFile($file_name),
						'thumb' => $get['result']['thumb'],
						'caption' => $caption,
						'parse_mode' => 'html',
						'reply_to_message_id' => $message_id,
						'reply_markup' => json_encode(['inline_keyboard' => [
							[['text' => "❤️ $like", 'url' => 'https://www.instagram.com/p/' . $matches[2] . '/']],
							[['text' => $verfy . '👤 ' . $get['result']['sender']['full_name'], 'url' => 'https://www.instagram.com/' . $get['result']['sender']['username'] . '/']],
						]])
					]);
					unlink($file_name);
				} else {
					
					bot('sendVideo',
					[
						'chat_id' => $pV_iD,
						'video' => $get['result']['url'],
						'thumb' => $get['result']['thumb_x'],
						'caption' => $caption,
						'parse_mode' => 'html',
						'reply_to_message_id' => $message_id,
						'reply_markup' => json_encode(['inline_keyboard' => [
							[['text' => "❤️ $like", 'url' => 'https://www.instagram.com/p/' . $matches[2] . '/']],
							[['text' => $verfy . '👤 ' . $get['result']['sender']['full_name'], 'url' => 'https://www.instagram.com/' . $get['result']['sender']['username'] . '/']],
						]])
					]);
				}
			}
			elseif ($get['result']['type'] == 'album') {
				$media = [];
				$i = 1;
				foreach ($get['result']['media'] as $mediaa) {
					if ($i >= 10) break;
					if ($mediaa['type'] == 'video') {
						$media[] = [
							'type' => 'video',
							'media' => $mediaa['url']
						];
						

					} else {
						$media[] = [
							'type' => 'photo',
							'media' => $mediaa['url']
						];
						
					}
					$i++;
				}
				bot('sendMediaGroup',[
					'chat_id' => $pV_iD,
					'media' => json_encode($media),
					'reply_to_message_id' => $message_id
				]);
				bot('sendMessage', [
					'chat_id' => $pV_iD,
					'text' => $caption,
					'parse_mode'=>'html',
					'disable_web_page_preview' => true,
					'reply_markup' => json_encode(['inline_keyboard' => [
						[['text' => "❤️ $like", 'url' => 'https://www.instagram.com/p/' . $matches[2] . '/']],
						[['text' => $verfy . '👤 ' . $get['result']['sender']['full_name'], 'url' => 'https://www.instagram.com/' . $get['result']['sender']['username'] . '/']],
					]])
				]);
			}
			sendMessage($pV_iD, "👇🏻 یکی از دکمه های زیر را انتخاب کنید :", null, $message_id, $button_tools);
		}
		
	}
	if (filter_var($HbNsMl, FILTER_VALIDATE_URL)) {
		$header = get_headers($HbNsMl, 1);
		$regex = $HbNsMl . '' . implode(' ', $header['Content-Type']);
		if ($header['Content-Length'] > 1 && !preg_match('#htm#i', $regex)) {
			if ($header['Content-Length'] < 20*1024*1024) {
				$type = $header['Content-Type'];
				if (preg_match('#api\.telegram\.org/file/#i', $HbNsMl)) {
					$file_name = time() . '.' . pathinfo($HbNsMl)['extension'];

					file_put_contents($file_name, '');
					chmod($file_name, 0666);
					file_put_contents($file_name, file_get_contents($HbNsMl));
					
					//copy($HbNsMl, $file_name);
					$HbNsMl = new CURLFile($file_name);
				}
				if (preg_match('#mp4#i', $regex)) {
					
					sendVideo($pV_iD, $HbNsMl);
				}
				elseif (preg_match('#(webp|tgs)#i', $regex)) {
					sendSticker($pV_iD, $HbNsMl);
				}
				elseif (preg_match('#oga#i', $regex)) {
					
					sendVoice($pV_iD, $HbNsMl);
				}
				elseif (preg_match('#(mp3png)#i', $regex)) {
					
					sendAudio($pV_iD, $HbNsMl);
				}
				elseif (preg_match('#(jpg|jpeg|png)#i', $regex)) {
					
					sendPhoto($pV_iD, $HbNsMl);
				}
				else {
					
					sendDocument($pV_iD, $HbNsMl);
				}
				sendMessage($pV_iD, "👇🏻 یکی از دکمه های زیر را انتخاب کنید :", null, $message_id, $button_tools);
				@unlink($file_name);
			} else {
				$size = humanFileSize($header['Content-Length']);
				sendMessage($pV_iD, "❌ حجم فایل بیش از ۲۰ مگابایت است و نمی توانم آنرا دانلود کنم.\n\n💠 حجم فایل : $size", null, $message_id);
				
			}
		} else {
			sendMessage($pV_iD, "❌ لطفا یک لینک معتبر ارسال کنید.", null, $message_id);
			
		}
		$data['step'] = "none";
		file_put_contents("data/data.json", json_encode($data));
		
} else {
	sendMessage($pV_iD, "❌ لطفا یک لینک معتبر ارسال کنید.", null, $message_id);
}
}
elseif(strpos($data['step'], "btn") !== false){
	$nambtn = str_replace("btn",'',$data['step']);
	$data['step'] = "none";
	
	$en = array ('profile','contact','location');
	$fa = array ('دکمه پروفایل','دکمه ارسال شماره','دکمه ارسال مکان');
	$str = str_replace($en, $fa, $nambtn);
	SendMessage($pV_iD,"■ نام ( $str ) با نام ( $HbNsMl ) ثبت گردید.", null, $message_id, $button_name);
	$data['button'][$nambtn]['name'] = "$HbNsMl";
	file_put_contents("data/data.json",json_encode($data));
}
elseif($data['step'] == "setchannel"){
	$GetMe = GetMe();
	$id = $GetMe->result->id;
	if(isset($message->forward_from_chat->username)){
		$get = Bot('GetChatMember',[
		'chat_id'=>"@".$message->forward_from_chat->username,
		'user_id'=>$id]);
		if($get->result->status == 'administrator'){
			SendMessage($pV_iD,"■ کانال @".$message->forward_from_chat->username." ثبت گردید.", 'Html', $message_id, $panel);
			$data['step'] = "none";
			$data['channel'] = "@".$message->forward_from_chat->username;
			file_put_contents("data/data.json",json_encode($data));
		}else{
			SendMessage($pV_iD,"■ ابتدا ربات را در کانال مربوطه ادمین کنید سپس اقدام به تنظیم کانال کنید.", 'MarkDown', $message_id);
		}
	}
	elseif(preg_match('/^(@)(\S{5,32})/i', $HbNsMl)){
		$get = Bot('GetChatMember',[
		'chat_id'=>$HbNsMl,
		'user_id'=>$id]);
		if($get->result->status == 'administrator'){
			SendMessage($pV_iD,"■ کانال ".$HbNsMl." ثبت گردید.", 'Html', $message_id, $panel);
			$data['step'] = "none";
			$data['channel'] = "$HbNsMl";
			file_put_contents("data/data.json",json_encode($data));
		}else{
			SendMessage($pV_iD,"■ ابتدا ربات را در کانال مربوطه ادمین کنید سپس اقدام به تنظیم کانال کنید.", 'MarkDown', $message_id);
		}
	}
	if(isset($message->forward_from_chat) and $message->forward_from_chat->username == null){
		SendMessage($pV_iD,"■ کانال حتما باید عمومی باشد!", 'MarkDown', $message_id);
	}
}
elseif($data['step'] == "userinfo" and is_numeric($HbNsMl) == true){
	$data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	
	$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$HbNsMl);
	$result = json_decode($get, true);
	$ok = $result['ok'];
	if($ok == true){
		$mention = "<a href='tg://user?id=$HbNsMl'>$HbNsMl</a>";
		$f_name = $result['result']['first_name'];
		if($result['result']['last_name'] != null){
			$l_name = $result['result']['last_name'];
		}else{
			$l_name = "ندارد!";
		}
		if($result['result']['username'] != null){
			$username = "@".$result['result']['username'];
		}else{
			$username = "ندارد!";
		}
		$profile = GetProfile($HbNsMl);
		if($profile != null){
			SendPhoto($pV_iD,$profile,"■ عکس پروفایل کاربر خاطی");
		}
		SendMessage($pV_iD,"■ اطلاعات کاربری <b>$HbNsMl</b> :\n\n• منشن کاربر : [ $mention ]\n• نام کاربر : [ $f_name ]\n• نام خانوادگی کاربر : [ $l_name ]\n• آیدی کاربر : [ $username ]", 'Html', $message_id, $panel);
	}else{
		SendMessage($pV_iD,"■ کاربری با آیدی *$HbNsMl* در ربات یافت نشد!", 'MarkDown', $message_id, $panel);
	}
}
elseif($data['step'] == "s2all" and isset($message)){
    $count = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $count++;
	    }
	}
	$sec = $count*2;
	$min = round($sec/60);
	SendMessage($pV_iD,"■ پیام در صف قرار گرفت، نتیجه ارسال اطلاع داده خواهد شد.\n■ زمان تقریبی ارسال : $sec ثانیه یا تقریبا $min دقیقه", 'MarkDown', $message_id, $panel);
    $data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	if($HbNsMl != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendMessage($id, $HbNsMl, null, null, $button_user);
	            sleep(2);
	        }
	    }
	}
	elseif($video_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendVideo($id,$video_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($voice_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendVoice($id,$voice_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($file_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendDocument($id,$file_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($music_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendAudio($id,$music_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo2_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo2_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo1_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo1_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($photo0_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendPhoto($id,$photo0_id,$caption);
	            sleep(2);
	        }
	    }
	}
	elseif($sticker_id != null){
	    foreach(glob('data/*') as $dir){
	        if(is_dir($dir)){
	            $id = pathinfo($dir)['filename'];
	            SendSticker($id,$sticker_id);
	            sleep(2);
	        }
	    }
	}
	SendMessage($pV_iD,"■ پیام به تمامی اعضا ارسال شد", 'MarkDown', null, $panel);
}
elseif($data['step'] == "f2all" and isset($message)){
    $count = 0;
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
	        $count++;
	    }
	}
	$sec = $count*2;
	$min = round($sec/60);
	SendMessage($pV_iD,"■ پیام در صف قرار گرفت، نتیجه ارسال اطلاع داده خواهد شد.\n■ زمان تقریبی فروارد : $sec ثانیه یا تقریبا $min دقیقه", 'MarkDown', $message_id, $panel);
    $data['step'] = "none";
	file_put_contents("data/data.json",json_encode($data));
	foreach(glob('data/*') as $dir){
	    if(is_dir($dir)){
            $id = pathinfo($dir)['filename'];
            Forward($id, $pV_iD, $message_id);
            sleep(2);
        }
    }
	SendMessage($pV_iD,"■ پیام به تمامی اعضا فروارد شد", 'MarkDown', null, $panel);
}
//------------------------------------------------------------------------------
elseif(preg_match('/^\/(ban) ([0-9]+)/i',$HbNsMl)){
	preg_match('/^\/(ban) ([0-9]+)/i',$HbNsMl,$match);
	$get = file_get_contents("https://api.telegram.org/bot".TDKVSN."/getChat?chat_id=".$match[2]);
	$result = json_decode($get, true);
	$ok = $result['ok'];
	if($ok == true){
	    if(in_array($match[2], $list['ban'])){
	        if($list['ban'] == null){
                $list['ban'] = [];
            }
		    array_push($list['ban'], $match[2]);
		    file_put_contents("data/list.json",json_encode($list));
		    SendMessage($pV_iD,"■ کاربر *".$match[2]."* از ربات مسدود گردید.", 'MarkDown', $message_id);
		    SendMessage($match[2],"■ شما از طرف مدیر مسدود شدید و پیام شما دیگر دریافت نخواهد شد.", 'MarkDown', null, $remove);
	    }else{
	        SendMessage($pV_iD,"■ کاربری با آیدی *".$match[2]."* از قبل در لیست مسدودیت بود!", 'MarkDown', $message_id);
	    }
	}else{
		SendMessage($pV_iD,"■ کاربری با آیدی *".$match[2]."* در ربات یافت نشد!", 'MarkDown', $message_id);
	}
}
elseif(preg_match('/^\/(unban) ([0-9]+)/i',$HbNsMl)){
	preg_match('/^\/(unban) ([0-9]+)/i',$HbNsMl,$match);
	if(in_array($match[2], $list['ban'])){
		$search = array_search($match[2], $list['ban']);
		unset($list['ban'][$search]);
		$list['ban'] = array_values($list['ban']);
		file_put_contents("data/list.json",json_encode($list));
		SendMessage($pV_iD,"■ کاربر *".$match[2]."* از مسدودیت آزاد گردید.", 'MarkDown', $message_id);
		SendMessage($match[2],"■ شما از طرف مدیر آزاد شدید و پیام شما دریافت خواهد شد.", 'MarkDown', null, $button_user);
	}else{
		SendMessage($pV_iD,"■ کاربری با آیدی *".$match[2]."* در لیست مسدودیت وجود نداشت!", 'MarkDown', $message_id);
	}
}
//------------------------------------------------------------------------------
} //--------[End of Dev]--------// 
//------------------------------------------------------------------------------
elseif(isset($update->inline_query->query)){
$GetChat = GetChat($DeRtK);
$DeRtKName = $GetChat->result->first_name;
$GetMe = GetMe();
$BotID = $GetMe->result->username;
   bot('AnswerInlineQuery',[
        'inline_query_id'=> $update->inline_query->id,
        'results'=> json_encode([[
        
        
            'type'=>'article',
						'thumb_url'=>'https://t.me/slokings/7871',
						'id'=> base64_encode(1),
						'title'=>'🔐 معرفی رباتساز',
						'input_message_content'=>
						[
							'parse_mode'=>'markdown',
							'message_text'=>"👈🏻 جهت ورود به *رباتساز اِسلُو کریت* روی دکمه شیشه ای زیر کلیک کنید",
							'disable_web_page_preview'=>true
						],
						'reply_markup'=>
						[
							'inline_keyboard'=>
							[
								[['text'=>'〽️ اِسلُو کریت', 'url'=>"https://telegram.me/SlowCreateBot"]]
							]
						]
					],
					[
						'type'=>'article',
						'thumb_url'=>'https://t.me/slokings/7871',
						'id'=> base64_encode(2),
						'title'=>'💠 معرفی پیامرسان',
						'input_message_content'=>
						[
							'parse_mode'=>'markdown',
							'message_text'=>"
■ ربات پیام رسان [$DeRtKName](tg://user?id=$DeRtK)\nشما از طریق این ربات می توانید با من در ارتباط باشید و ریپورت بودن شما در این حالت بلا مانع است.\nبرای هدایت شدن به این ربات پیام رسان از دکمه شیشه ای زیر استفاده نمایید!
",
							'disable_web_page_preview'=>true
						],
						'reply_markup'=>
						[
							'inline_keyboard'=>
							[
								[['text'=>'🤖 ربات پیامرسان', 'url'=>"https://telegram.me/$BotID"]]
							]
						]
					]
				]
			)
		]);
	exit();
}
else {
	exit();
}
//------------------------------------------------------------------------------
//--------[Auto Configer via Get]--------//
if(!is_dir('data')){
	mkdir('data');
	$txt = "*Bot is Running ... | Click* /start";
	file_get_contents("https://api.telegram.org/bot".TDKVSN."/SendMessage?chat_id=".$DeRtK."&text=".$txt."&parse_mode=MarkDown");
	$WebHook = file_get_contents("https://api.telegram.org/bot".TDKVSN."/SetWebHook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']);
	$result = json_decode($WebHook, true);
	$ok = $result['ok'];
	if($ok == true){
		header('Content-type: application/json');
		SendMessage($DeRtK,"■ ربات شما با موفقیت اجرا شد.\n■ هم اکنون روی /start بزنید :)", 'Html');
		$array = ['ok'=>'true','Description'=>'Bot is Running ...'];
		$json = json_encode($array, JSON_PRETTY_PRINT);
		Echo $json;
	}else{
		header('Content-type: application/json');
		$array = ['ok'=>'false','Description'=>'Failed To Run :('];
		$json = json_encode($array, JSON_PRETTY_PRINT);
		Echo $json;
	}
	exit();
}
//------------------------------------------------------------------------------
if(!in_array($iD_pV, $list['user']) and !is_null($iD_pV)){
    mkdir("data/$iD_pV");
    if($list['user'] == null){
    $list['user'] = [];
    }
    array_push($list['user'], $iD_pV);
    file_put_contents("data/list.json",json_encode($list));
}
//------------------------------------------------------------------------------
if(isset($update)){
	$up = $data['count']['update'];
	$data['count']['update'] = $up + 1;
	file_put_contents("data/data.json",json_encode($data));
	#Save Update to History
	$up_date = file_get_contents('php://input');
	$dir = 'data/updates.txt';
	file_put_contents($dir,$up_date."\n------------------------\n",FILE_APPEND);
}
//------------------------------------------------------------------------------
unlink('error_log');
?>