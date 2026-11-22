<?php 


// join : https://t.me/Sourrce_kade



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
if (!$ok) die(" Join: @Sourrce_kade"); 
//-----------------------------------------------------------------------------------------------
error_reporting(0); // 
date_default_timezone_set('Asia/Tehran');
//-----------------------------------------------------------------------------------------------
include "config.php";
include "jdf.php"; // 
//-----------------------------------------------------------------------------------------------
define('API_KEY',$API_KC);  
//-----------------------------------------------------------------------------------------------
function moeinabasyan($method,$datas=[]){
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
//-----------------------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
$data = $update->callback_query->data;
$message = $update->message;
$text = $message->text;
$tc = $update->message->chat->type;
$first_name = $message->from->first_name;
$username = $message->from->username;
//----------------------------------------------------------------------
if(isset($data)){
$chat_id = $update->callback_query->message->chat->id;
$from_id = $update->callback_query->from->id;
$message_id = $update->callback_query->message->message_id;
}  
if(isset($message->from)){
$chat_id = $message->chat->id;
$from_id  = $message->from->id;
$message_id  = $message->message_id;
}  
//-----------------------------------------------------------------------------------------------
$user = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$from_id' LIMIT 1"));
$settings = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE botid = '$botid' LIMIT 1"));
//-----------------------------------------------------------------------------------------------
$bot_mode = $settings['bot_mode'];
$chupl = $settings['chupl'];
$is_all = $settings['is_all'];
//-----------------------------------------------------------------------------------------------
$time = date('H:i:s'); //
$ToDay = jdate('l'); // 
$date = gregorian_to_jalali(date('Y'), date('m'), date('d'), '/');
//-----------------------------------------------------------------------------------------------
function bot_user($id,$what){ 
  $bye = moeinabasyan('getChat',[
  'chat_id'=>$id,
  ]);
  return $bye->result->$what;
} /* @Sourrce_kade */
function convert($size){
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.['', 'K', 'M', 'G', 'T', 'P'][$i].'B';
}
function decrypt($string){
  $p = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29];
  $res = [];
  $string = str_split($string, 10);
  foreach($string as $r){
    $r = str_split($r);
    $c = 0;
    $m = 1;
    for($i = 0; isset($r[$i]); ++$i){
      $r[$i] = base_convert($r[$i], 36, 10);
      while((41 * $c) % $p[$i] != $r[$i])
        $c += $m;
      $m *= $p[$i];
    }
    $res[] = $c;
  }
  $res = pack("V*", ...$res);
  return $res;
}
function doc($name) {
    if ($name == "document") {
        return "فایل";
    }
    if ($name == "video") {
        return "ویدیو";
    }
    if ($name == "photo") {
        return "عکس";
    }
    if ($name == "voice") {
        return "ویس";
    }
    if ($name == "audio") {
        return "موزیک";
    }
    if ($name == "sticker") {
        return "استیکر";
    } 
}
function Takhmin($fil){
if($fil <= 200 ){
return "2";
}else{
$besanie = $fil/200;
return ceil($besanie)+1;
}
} /* @Sourrce_kade */
function getChatstats($chat_id,$token) {
  $url = 'https://api.telegram.org/bot'.$token.'/getChatAdministrators?chat_id='.$chat_id;
  $result = file_get_contents($url);
  $result = json_decode ($result);
  $result = $result->ok;
  return $result;
}
function IsJoined($token,$User, array $Channels) {
    $AcceptedRoles = ['administrator', 'creator', 'member'];
    foreach($Channels as $iterator){
        $Req = file_get_contents('https://api.telegram.org/bot'.$token.'/getChatMember?chat_id='.$iterator.'&user_id='.$User);
        yield in_array(json_decode($Req)->result->status, $AcceptedRoles);
    }
}
function CanSendRequest($results){
    $ok = true;
    foreach($results as $result)
        if($result == false)
            $ok = false;
    return $ok;
}
function is_join($from_id,$Channel){
$forchaneel = moeinabasyan('getChatMember',[
'chat_id'=>$Channel,
'user_id'=>$from_id]);
$tch = $forchaneel->result->status;
if($tch != 'member' && $tch != 'creator' && $tch != 'administrator' ){
return false;
}else{
return true; /* @Sourrce_kade */
   }
}
//==========================================================================
if($user["spam"] < time()){ 
$tt = time() + 0.8;
$connect->query("UPDATE user SET spam = '$tt' WHERE id = '$from_id' LIMIT 1");	
//==========================================================================
if($bot_mode == "off" && !in_array($from_id,$admins)) {
if(isset($message->from)){
moeinabasyan('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"⭕️ ربات فعلا خاموش میباشد .",
'parse_mode'=>"HTML",
    		]); 
    		} /* @Sourrce_kade */
    if(isset($data)){
    	moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"⭕️ ربات فعلا خاموش میباشد .",
 'parse_mode'=>"HTML",
    		]); 
    }
}else{
if($user['step'] == "ban") {
if(isset($message->from)){
moeinabasyan('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"📛 شما از ربات مسدود هستید .",
'parse_mode'=>"HTML",
    		]); 
    		}
    if(isset($data)){
    	moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"📛 شما از ربات مسدود هستید .",
 'parse_mode'=>"HTML",
    		]); 
    } /* @Sourrce_kade */
}else{
if($text == "/start" or $text == "🏠 برگشت به منو"){ // d a r k m i n d s 
if($user['id'] == null ){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔥 | سلام کاربر عزیز ، به ربات آپلودر خوش آمدید.

❄️ | Welcom To Uploder
🔹 | Name : $first_name
🔸 | id : $chat_id

💎 | یکی از گزینه های زیر را انتخاب کنید :",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"🔹 پرطرفدار ترین آپلود شده ها 🔸"]],
 	],
            	'resize_keyboard'=>true
       		]) /* @Sourrce_kade */
    		]);
 $connect->query("INSERT INTO user (id , step , step2 , step3 , step4 , step5 , spam) VALUES ('$from_id', 'none', 'none', 'none', 'none', 'none', '0')");
}else{ 
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔥 | سلام کاربر عزیز ، به ربات آپلودر خوش آمدید.

❄️ | Welcom To Uploder
🔹 | Name : $first_name
🔸 | id : $chat_id

💎 | یکی از گزینه های زیر را انتخاب کنید :",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"🔹 پرطرفدار ترین آپلود شده ها 🔸"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]); 
    	$connect->query("UPDATE user SET step = 'none', step2 = 'none', step3 = 'none', step4 = 'none', step5 = 'none' WHERE id = '$from_id'");  
  }
  } /* @Sourrce_kade */
  if(strpos($text,"/start dl_") !== false && $text != "/start" && $tc == "private"){
  if($user['id'] == null ){
   $connect->query("INSERT INTO user (id , step , step2 , step3 , step4 , step5 , spam) VALUES ('$from_id', 'none', 'none', 'none', 'none', 'none', '0')");
   }
$edit = str_replace("/start dl_","",$text);
$chs = mysqli_query($connect,"select idoruser from channels");
$fil = mysqli_num_rows($chs);
while($row = mysqli_fetch_assoc($chs)){
     $ar[] = $row["idoruser"];
}
 $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$edit' LIMIT 1"));
if($files['ghfl_ch'] == "on" && $fil != 0 && CanSendRequest(IsJoined(API_KEY,$from_id,$ar)) == false ){
for ($i=0; $i <= $fil; $i++){

$by = $i + 1;
$okk = $ar[$i];
$ch = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
$link = $ch['link'];
if($link != null ){
if(is_join($from_id,$okk) == false ){
$d4[] = [['text'=>"ورود به چنل $by",'url'=>$link]];
}
}  //  
} // Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
$d4[] = [['text'=>"✅ عضو شدم",'callback_data'=>"taid_$edit"]];
moeinabasyan('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"به ربات آپلودر ما خوش آمدید🙏🏻🌹

جهت دریافت ، ابتدا وارد چنل های زیر شوید.

⭕️ بعد از عضویت در همه چنل ها روی 'تایید عضویت' کلیک کنید تا برای شما ارسال شود",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
           'inline_keyboard'=>$d4
              ])
    		]); /* @Sourrce_kade */
    		}else{
 $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$edit' LIMIT 1"));
 if($files['file_id'] != null ){
 if($files['pass'] == 'none' ){
 if($files['mahdodl'] == 'none' ){
   $file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
  $id = moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;	
    		$msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        }
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$edit' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}else{
        		if($files['dl'] != $files['mahdodl'] && $files['dl'] + 0.1 < $files['mahdodl']){
        	$file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
 $id = moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;  //  
        $msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        }
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$edit' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}else{ 
        	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ این فایل به حداکثر دانلود رسیده است .",
'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"🔹 پرطرفدار ترین آپلود شده ها 🔸"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}
        	} /* @Sourrce_kade */
        	}else{
        	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔐 این محتوا حاوی پسورد است !

- لطفا رمز دسترسی را وارد کنید:",
'parse_mode'=>"HTML",
    		]);
    			$connect->query("UPDATE user SET step = 'khiiipassz_$edit' WHERE id = '$from_id' LIMIT 1");	
        	}
        }
        } /* @Sourrce_kade */
}
elseif(strpos($data,"taid_") !== false ){ 
$ok = str_replace("taid_",null,$data);
$chs = mysqli_query($connect,"select idoruser from channels");
while($row = mysqli_fetch_assoc($chs)){
     $ar[] = $row["idoruser"];
}
$fil = mysqli_num_rows($chs);
 $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['ghfl_ch'] == "on" && $fil != 0 && CanSendRequest(IsJoined(API_KEY,$from_id,$ar)) == false ){
moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "هنوز در چنل جوین نشده اید !",
        'show_alert' => false
    ]);
}else{
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"✅ عضویت شما تایید شد .",
 'parse_mode'=>"HTML",
    		]);  /* @Sourrce_kade */
    		$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
    		 if($files['pass'] == 'none' ){
 if($files['mahdodl'] == 'none' ){
    		$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
   $file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
  $id = moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;	
        $msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        }
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$ok' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	 	}else{
  	if($files['dl'] != $files['mahdodl'] && $files['dl'] + 0.1 < $files['mahdodl']){
        	$file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
  $id =  moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;	
        $msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        }
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$ok' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}else{
        	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ این فایل به حداکثر دانلود رسیده است .",
'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"🔹 پرطرفدار ترین آپلود شده ها 🔸"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}
        	}
        	}else{  
        	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔐 این محتوا حاوی پسورد است !

- لطفا رمز دسترسی را وارد کنید:",
'parse_mode'=>"HTML",
    		]);
    			$connect->query("UPDATE user SET step = 'khiiipassz_$ok' WHERE id = '$from_id' LIMIT 1");	
        	}
} /* @Sourrce_kade */
}
elseif(strpos($user['step'],"khiiipassz_") !== false && strpos($text,"start") === false ){
$ok = str_replace("khiiipassz_",null,$user['step']);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['pass'] != 'none'){
if($text == $files['pass']){
if($files['mahdodl'] == "none"){
$file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
  $id = moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;	
        $msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        } 
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$ok' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}else{
        	if($files['dl'] != $files['mahdodl'] && $files['dl'] + 0.1 < $files['mahdodl']){
        	$file_size = $files['file_size'];
   $file_id = $files['file_id'];
   $file_type = $files['file_type'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   $type = doc($file_type);
   $bash = $dl + 1;
  $id =  moeinabasyan('send'.$files['file_type'], [
            'chat_id' => $chat_id,
            "$file_type" => $file_id,
            'caption' => "$tozihat

📥 تعداد دانلود : $bash",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "HTML",
        ])->result;	
        $msg_iddd = $id->message_id;
        if($files['zd_filter'] == "on"){
        $connect->query("INSERT INTO dbremove (id, message_id, time) VALUES ('{$from_id}' , '$msg_iddd' , '".strtotime("+{$settings['moeinabasyan']} minutes")."')");
        $isdeltime = $settings['moeinabasyan'];
        moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️👆👆👆⭕️
پیام بالا را هر چه سریعتر به قسمت پیام های ذخیره شده خود( Saved Message ) فوروارد کنید !

این پیام تا $isdeltime دقیقه دیگر حذف خواهد شد .",
'parse_mode'=>"HTML",
    		]);
        }
        	$connect->query("UPDATE files SET dl = '$bash' WHERE code = '$ok' LIMIT 1");	
        	$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}else{
        	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ این فایل به حداکثر دانلود رسیده است .",
'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"🔹 پرطرفدار ترین آپلود شده ها 🔸"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
        	}
        	}
}else{
moeinabasyanmoeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ پسورد نامعتبر است !

❗️ لطفا پسورد را بدرستی ارسال کنید:",
'parse_mode'=>"HTML",
    		]);
} 
}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕️ این فایل دیگر پسورد ندارد.

لطفا یکبار دیگر با لینک وارد شوید:

https://telegram.me/$bottag?start=dl_$ok",
'parse_mode'=>"HTML",
    		]);
}
}
  elseif($text == "🔹 پرطرفدار ترین آپلود شده ها 🔸" ){ 
$sql = "SELECT * FROM files ORDER BY dl DESC LIMIT 5";
$result = $connect->query($sql);
if ($result->num_rows > 0) {
$mtn = "";
    while($row = $result->fetch_assoc()) {
    $code = $row['code'];
    $dl = $row['dl'];
    $mtn = $mtn."ℹ️ کد : $code
📥 تعداد دانلود : $dl
🔗 لینک دریافت : https://t.me/$bottag?start=dl_$code\n\n";
    }
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 پرطرفدار ترین آپلودی های شما :\n\n".$mtn,
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
            [['text'=>"بروزرسانی",'callback_data'=>"uptopup"]],
              ]
        ])
    		]);
} else {
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ چیزی آپلود نشده است .",
'parse_mode'=>"HTML",
    		]);
} 
    }
    elseif($data == "uptopup"){
        $sql = "SELECT * FROM files ORDER BY dl DESC LIMIT 5";
$result = $connect->query($sql);
if ($result->num_rows > 0) {
$mtn = "";
    while($row = $result->fetch_assoc()) {
    $code = $row['code'];
    $dl = $row['dl'];
    $mtn = $mtn."ℹ️ کد : $code
📥 تعداد دانلود : $dl
🔗 لینک دریافت : https://t.me/$bottag?start=dl_$code\n\n";
    }
    moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"🔹 پرطرفدار ترین آپلودی های شما :\n\n".$mtn,
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
            [['text'=>"بروزرسانی",'callback_data'=>"uptopup"]],
              ]
        ])
    		]);
    		moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "با موفقیت بروزرسانی شد .",
        'show_alert' => false
    ]);
} else {
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"❌ چیزی آپلود نشده است .",
'parse_mode'=>"HTML",
    		]);
} 
        }
   elseif($text == "پنل" && in_array($from_id,$admins)){
    if($user['id'] == null ){
  $connect->query("INSERT INTO user (id , step , step2 , step3 , step4 , step5 , spam) VALUES ('$from_id', 'none', 'none', 'none', 'none', 'none', '0')");
  }else{
    		$connect->query("UPDATE user SET step = 'none', step2 = 'none', step3 = 'none', step4 = 'none', step5 = 'none' WHERE id = '$from_id'");  
  }
  moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"◾️ ادمین گرامی ، به پنل مدیریتی خوش آمدید.

🔢 <b>Your id :</b> <code>$from_id</code>
🔹 ورژن آپلودر : 3.5
🔸 طراح : @Sourrce_kade
📅 تاریخ : <code>$ToDay $date $time</code>

ℹ️ یکی از گزینه هارا انتخاب کنید :",
'parse_mode'=>"HTML",
    'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"👥 | آمار ربات"],['text'=>"📣 | تغییر قفل چنل"]],
            	        	[['text'=>"📨 | فوروارد همگانی"],['text'=>"📨 | پیام همگانی"]],
            	[['text'=>"ℹ️ | اطلاعات آپلود"],['text'=>"📥 | آپلود رسانه"]],
            		[['text'=>"🗂 | تمام رسانه ها"],['text'=>"❎ | حذف رسانه"]],
            		[['text'=>"🔒 | تنظیم پسورد"],['text'=>"🚷 | محدودیت دانلود"]],
            	[['text'=>"💫 | تنظیم قفل آپلود"],['text'=>"🔥 | تنظیم ضد فیلتر"]],
            	[['text'=>"💬 | تنظیم متن چنل"],['text'=>"📣 | تنظیم چنل رسانه"]],
			     			[['text'=>"📛 | مسدود کردن"],['text'=>"❇️ | آزاد کردن"]],
										[['text'=>"❌ | ربات خاموش"],['text'=>"✅ | ربات روشن"]],
										[['text'=>"🏠 برگشت به منو"],['text'=>"📛 | تنظیم تایم حذف"]],
 	],
            	'resize_keyboard'=>true
       		])
       		]);
  } 
  elseif($text == "🔙 منوی پنل" && in_array($from_id,$admins)){  //  
  if($user['id'] == null ){
   $connect->query("INSERT INTO user (id , step , step2 , step3 , step4 , step5 , spam) VALUES ('$from_id', 'none', 'none', 'none', 'none', 'none', '0')");
  }else{
    		$connect->query("UPDATE user SET step = 'none', step2 = 'none', step3 = 'none', step4 = 'none', step5 = 'none' WHERE id = '$from_id'");  
  } /* @Sourrce_kade */
  moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"◾️ ادمین گرامی ، به پنل مدیریتی خوش آمدید.

🔢 <b>Your id :</b> <code>$from_id</code>
🔹 ورژن آپلودر : 3.5
🔸 طراح : @moeinabasyan
📅 تاریخ : <code>$ToDay $date $time</code>

ℹ️ یکی از گزینه هارا انتخاب کنید :",
'parse_mode'=>"HTML",
    'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"👥 | آمار ربات"],['text'=>"📣 | تغییر قفل چنل"]],
            	        	[['text'=>"📨 | فوروارد همگانی"],['text'=>"📨 | پیام همگانی"]],
            	[['text'=>"ℹ️ | اطلاعات آپلود"],['text'=>"📥 | آپلود رسانه"]],
            		[['text'=>"🗂 | تمام رسانه ها"],['text'=>"❎ | حذف رسانه"]],
            		[['text'=>"🔒 | تنظیم پسورد"],['text'=>"🚷 | محدودیت دانلود"]],
            	[['text'=>"💫 | تنظیم قفل آپلود"],['text'=>"🔥 | تنظیم ضد فیلتر"]],
            	[['text'=>"💬 | تنظیم متن چنل"],['text'=>"📣 | تنظیم چنل رسانه"]],
			     			[['text'=>"📛 | مسدود کردن"],['text'=>"❇️ | آزاد کردن"]],
										[['text'=>"❌ | ربات خاموش"],['text'=>"✅ | ربات روشن"]],
										[['text'=>"🏠 برگشت به منو"],['text'=>"📛 | تنظیم تایم حذف"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
  } /* @Sourrce_kade */
  elseif(strpos($data,"pnlzdfilter_") !== false ){
    if(in_array($chat_id,$admins)){
$ok = str_replace("pnlzdfilter_",null,$data);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['code'] != null ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💎 لطفا انتخاب کنید 

️ ℹ️ فایل شماره : <code>$ok</code>
👇🏻 ضد فیلتر برای کد آپلود بالا روشن/خاموش شود",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"❌ خاموش"],['text'=>"✅ روشن"]],
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'setzdfilpn_$ok' WHERE id = '$from_id' LIMIT 1");	
    			}else{
   	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ | این کد آپلود وجود ندارد و یا حذف شده.

🔄 | لطفا دوباره امتحان کنید :",
'parse_mode'=>"HTML",
    		]);
    }
}
}

elseif(strpos($user['step'],"setzdfilpn_") !== false && $text != '🔙 منوی پنل'){
    if(in_array($chat_id,$admins)){
$ok = str_replace("setzdfilpn_",null,$user['step']);
if($text == "❌ خاموش" or $text == "✅ روشن" ){
if($text == "✅ روشن"){
$oonobbin = "on";
$textttt = "روشن";
}
if($text == "❌ خاموش"){
$oonobbin = "off";
$textttt = "خاموش";
} /* @Sourrce_kade */
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['zd_filter'] != $oonobbin ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💥 ضد فیلتر برای کد آپلود ( $ok ) با موفقیت $textttt شد !",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE files SET zd_filter = '$oonobbin' WHERE code = '$ok' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    		}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 ضد فیلتر برای کد آپلود ( $ok ) قبلا $textttt بود!",
'parse_mode'=>"HTML",
    		]);
}
}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا فقط یکی از گزینه های کیبورد را انتخاب کنید :",
'parse_mode'=>"HTML",
    		]);
}
}
}
elseif(strpos($data,"ghdpnl_") !== false ){ // هــوـش سـیاه
    if(in_array($chat_id,$admins)){
$ok = str_replace("ghdpnl_",null,$data);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['code'] != null ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💎 لطفا انتخاب کنید 

️ ℹ️ فایل شماره : <code>$ok</code>
👇🏻 قفل چنل برای کد آپلود بالا روشن/خاموش شود",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"❌ خاموش"],['text'=>"✅ روشن"]],
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]); /* moeinabasyan */
    			$connect->query("UPDATE user SET step = 'setghfpnl_$ok' WHERE id = '$from_id' LIMIT 1");	
   	}else{
   	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ | این کد آپلود وجود ندارد و یا حذف شده.

🔄 | لطفا دوباره امتحان کنید :",
'parse_mode'=>"HTML",
    		]);
    }
}
}
elseif(strpos($user['step'],"setghfpnl_") !== false && $text != '🔙 منوی پنل'){
    if(in_array($chat_id,$admins)){
$ok = str_replace("setghfpnl_",null,$user['step']);
if($text == "❌ خاموش" or $text == "✅ روشن" ){
if($text == "✅ روشن"){
$oonobbin = "on";
$textttt = "روشن";
}
if($text == "❌ خاموش"){
$oonobbin = "off";
$textttt = "خاموش";
}
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['ghfl_ch'] != $oonobbin ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💥 قفل چنل برای کد آپلود ( $ok ) با موفقیت $textttt شد !",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE files SET ghfl_ch = '$oonobbin' WHERE code = '$ok' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    		}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 ضد فیلتر برای کد آپلود ( $ok ) قبلا $textttt بود!",
'parse_mode'=>"HTML",
    		]);
}
}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا فقط یکی از گزینه های کیبورد را انتخاب کنید :",
'parse_mode'=>"HTML",
    		]);
}
} /* moeinabasyan */
}
elseif($text=="🔥 | تنظیم ضد فیلتر" ){  // هــوش سیاه
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 | لطفا کد آپلود را ارسال کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setzdfilll' WHERE id = '$from_id' LIMIT 1");	
    } /* moeinabasyan */
    }
    elseif($user['step'] == "setzdfilll" && $text != '🔙 منوی پنل'){
    if(in_array($chat_id,$admins)){
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
if($files['code'] != null ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💎 لطفا انتخاب کنید 

️ ℹ️ فایل شماره : <code>$text</code>
👇🏻 ضد فیلتر برای کد آپلود بالا روشن/خاموش شود",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
            	           	[['text'=>"❌ خاموش"],['text'=>"✅ روشن"]],
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setzdfilpn_$text' WHERE id = '$from_id' LIMIT 1");	
    			}else{
   	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ | این کد آپلود وجود ندارد و یا حذف شده.

🔄 | لطفا دوباره امتحان کنید :",
'parse_mode'=>"HTML",
    		]);
    } /* moeinabasyan */
}
}
elseif($text=="💫 | تنظیم قفل آپلود" ){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 | لطفا کد آپلود را ارسال کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setgfup' WHERE id = '$from_id' LIMIT 1");	
    }
    }
    elseif($user['step'] == "setgfup" && $text != '🔙 منوی پنل'){
    if(in_array($chat_id,$admins)){
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
if($files['code'] != null ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💎 لطفا انتخاب کنید 

️ ℹ️ فایل شماره : <code>$text</code>
👇🏻 قفل چنل برای کد آپلود بالا روشن/خاموش شود",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
            	           	[['text'=>"❌ خاموش"],['text'=>"✅ روشن"]],
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]); /* moeinabasyan */
    		$connect->query("UPDATE user SET step = 'setghfpnl_$text' WHERE id = '$from_id' LIMIT 1");	
    			}else{
   	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ | این کد آپلود وجود ندارد و یا حذف شده.

🔄 | لطفا دوباره امتحان کنید :",
'parse_mode'=>"HTML",
    		]);
    }
}
}
  elseif($text=="📛 | تنظیم تایم حذف" ){ // هــوـش سـیاه
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ لطفا تعداد دقیقه حذف فایل را از کیبورد انتخاب کنید ( در صورتی که بعد آپلود گزینه ضد فیلتر را بزنید ، بعد دقیقه مشخص از پی وی کاربر حذف میشود )

🔹 مقدار پیشفرض : 1 دقیقه
🔸 مقدار فعلی : moeinabasyan دقیقه

👇 لطفا از کیبورد انتخاب کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
           	[['text'=>"1"],['text'=>"2"],['text'=>"3"],['text'=>"4"],['text'=>"5"]],
           	[['text'=>"10"],['text'=>"15"],['text'=>"30"]],
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setdeltime' WHERE id = '$from_id' LIMIT 1");	
    }
    }
    elseif($user['step'] == "setdeltime" && $text != "🔙 منوی پنل" ){
    $array5 = [1,2,3,4,5,10,15,30];
    if(in_array($text,$array5)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ با موفقیت تنظیم شد .

مقدار جدید : $text دقیقه",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE settings SET moeinabasyan = '$text' WHERE botid = '$botid' LIMIT 1");	
    			$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    }else{
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ لطفا فقط از کیبورد انتخاب کنید 👇🏻",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
           	[['text'=>"1"],['text'=>"2"],['text'=>"3"],['text'=>"4"],['text'=>"5"]],
           	[['text'=>"10"],['text'=>"15"],['text'=>"30"]],
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    } /* moeinabasyan */
    }
  elseif($text=="📣 | تغییر قفل چنل" ){
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ به بخش تنظیم چنل های قفل خوش آمدید.

💯 برای حذف چنل، از بخش لیست چنل چنل مورد نظر را حذف کنید .",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"➕ افزودن چنل"]],
							[['text'=>"🔙 منوی پنل"],['text'=>"📚 لیست چنل ها"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
}
} 
elseif($text=="➕ افزودن چنل" ){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"لطفا نوع چنلی که میخواهید اضافه کنید را از کیبورد انتخاب کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            								[['text'=>"عمومی"],['text'=>"خصوصی"]],
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");	
    } /* moeinabasyan */
    }
    elseif($text=="عمومی" && $user['step'] == "addch1" ){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"لطفا یوزرنیم چنل عمومی را بدون @ ارسال کنید ( ربات را قبل ارسال بر ان چنل آدمین کرده باشید )",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'addchpub' WHERE id = '$from_id' LIMIT 1");	
    }
    }
    elseif($user['step'] == "addchpub" && $text != "🔙 منوی پنل" && !$data ){ // هــوش سیاه
    if(in_array($chat_id,$admins)){
      		 $textt = str_replace("@",null,$text);
      		 			$texttt = "@".$textt;
      		 			    $ch = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM channels WHERE idoruser = '$texttt' LIMIT 1"));
    if($ch['link'] == null ){
    		 $admini = getChatstats("@$textt",API_KEY);
			if($admini == true ){
			$linkk = "https://t.me/$textt";
			$connect->query("INSERT INTO channels (idoruser , link) VALUES ('$texttt', '$linkk')");
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"چنل @$textt با موفقیت افزوده شد .",
'parse_mode'=>"HTML",
       'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"➕ افزودن چنل"]],
							[['text'=>"🔙 منوی پنل"],['text'=>"📚 لیست چنل ها"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]); /* moeinabasyan */
    		$connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");	
			}else{
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"خطا ! ربات بر چنل @$textt آدمین نیست !

ابتدا ربات را ادمین و سپس ارسال کنید تا افزوده شود.",
'parse_mode'=>"HTML",
    		]);
			}
					}else{
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"خطا ! قبلا چنلی با این ایدی ثبت شده !

لطفا دوباره ارسال فرمایید :",
'parse_mode'=>"HTML",
    		]);
			}
			}
			}
    elseif($text=="خصوصی" && $user['step'] == "addch1" ){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"لطفا آیدی عددی چنل خصوصی را ارسال کنید .
نمونه ایدی عددی چنل : 
-1009876262727
ربات را قبل ارسال حتما ادمین کرده باشید.",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'addcpr' WHERE id = '$from_id' LIMIT 1");	
    }
    }
    elseif($user['step'] == "addcpr" && $text != "🔙 منوی پنل" && !$data ){
    if(in_array($chat_id,$admins)){
    $ch = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM channels WHERE idoruser = '$text' LIMIT 1"));
    if($ch['link'] == null ){
    		 $admini = getChatstats($text,API_KEY);
			if(strpos($text,"-100") !== false && $admini == true ){
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"لطفا لینک خصوصی دعوت را ارسال کنید :",
'parse_mode'=>"HTML",
       'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		]) // d a r k m i n d s
    		]);
    		$connect->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'addchpr1' WHERE id = '$from_id' LIMIT 1");	
			}else{
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"خطا ! ربات بر چنل $text آدمین نیست و یا ایدی ارسالی حاوی -100 نیست.

ابتدا ربات را ادمین و سپس ارسال کنید تا افزوده شود.",
'parse_mode'=>"HTML",
    		]);
			}
			}else{
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"خطا ! قبلا چنلی با این ایدی ثبت شده !

لطفا دوباره ارسال فرمایید :",
'parse_mode'=>"HTML",
    		]);
			}
			}
			}
			elseif($user['step'] == "addchpr1" && $text != "🔙 منوی پنل" && !$data ){
			if(in_array($chat_id,$admins)){
			if(strpos($text,"://t.me/") !== false ){
			$idus = $user['step2'];
			$connect->query("INSERT INTO channels (idoruser , link) VALUES ('$idus', '$text')");
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"چنل با موفقیت افزوده شد .",
'parse_mode'=>"HTML",
       'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[['text'=>"➕ افزودن چنل"]],
							[['text'=>"🔙 منوی پنل"],['text'=>"📚 لیست چنل ها"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'addch1' WHERE id = '$from_id' LIMIT 1");	
			}else{
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"خطا! لینک ارسالی اشتباه است !

لطفا دوباره ارسال کنید:",
'parse_mode'=>"HTML",
    		]);
			}
			}
			}
   elseif($text=="📚 لیست چنل ها" ){  // هــوش سیاه
    if(in_array($chat_id,$admins)){
    $chs = mysqli_query($connect,"select idoruser from channels");
$fil = mysqli_num_rows($chs);
if($fil != 0){
while($row = mysqli_fetch_assoc($chs)){
     $ar[] = $row["idoruser"];
}
for ($i=0; $i <= $fil; $i++){

$by = $i + 1;
$okk = $ar[$i];
$ch = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
$link = $ch['link'];
if($link != null ){
$d4[] = [['text'=>"چنل شماره $by",'url'=>$link],['text'=>"❌ حذف",'callback_data'=>"delc_$okk"]];
}
}
moeinabasyan('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"👇🏻 لیست تمام چنل های قفل",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
           'inline_keyboard'=>$d4
              ])
    		]); 
    		}else{
    		moeinabasyan('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"❌ هیچ چنل قفلی تنظیم نشده.",
'parse_mode'=>"HTML",
    		]); 
    		}
    } /* moeinabasyan */
    }
    elseif(strpos($data,"delc_") !== false ){
    if(in_array($chat_id,$admins)){
    $ok = str_replace("delc_",null,$data);
    $chs = mysqli_query($connect,"select idoruser from channels");
$fil = mysqli_num_rows($chs);
if($fil == 1){
$connect->query("DELETE FROM channels WHERE idoruser = '$ok'");	
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"👇🏻 لیست تمام چنل های قفل

❌ تمام چنل ها حذف شده است.",
'parse_mode'=>"HTML",
    		]); 
    moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "✅ چنل حذف شد .",
        'show_alert' => false
    ]);
}else{
$connect->query("DELETE FROM channels WHERE idoruser = '$ok'");	 // هــوش سیاه
  $chs = mysqli_query($connect,"select idoruser from channels");
$fil = mysqli_num_rows($chs);
while($row = mysqli_fetch_assoc($chs)){
     $ar[] = $row["idoruser"];
}
for ($i=0; $i <= $fil; $i++){

$by = $i + 1;
$okk = $ar[$i];
$ch = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM channels WHERE idoruser = '$okk' LIMIT 1"));
$link = $ch['link'];
if($link != null ){
$d4[] = [['text'=>"چنل شماره $by",'url'=>$link],['text'=>"❌ حذف",'callback_data'=>"delc_$okk"]];
}
} // هــوـش سـیاه
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"👇🏻 لیست تمام چنل های قفل

❌ چنل حذف شد .",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
           'inline_keyboard'=>$d4
              ])
    		]); 
    moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "✅ چنل حذف شد .",
        'show_alert' => false
    ]);
    }
    }
   }
  elseif($text=="✅ | ربات روشن" ){
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✓   عملیات انجام شد .",
'parse_mode'=>"HTML",
    		]);
    		$connect->query("UPDATE settings SET bot_mode = 'on' WHERE botid = '$botid' LIMIT 1");	
}
} 
elseif($text=="❌ | ربات خاموش" ){
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✓   عملیات انجام شد .",
'parse_mode'=>"HTML",
    		]);
    		$connect->query("UPDATE settings SET bot_mode = 'off' WHERE botid = '$botid' LIMIT 1");	
} /* moeinabasyan */
} 
  elseif($text=="🗂 | تمام رسانه ها" ){
    if(in_array($chat_id,$admins)){
    $chid = mysqli_query($connect,"select code from files");
$fil2 = mysqli_num_rows($chid);
if($fil2 != 0 ){
while($row = mysqli_fetch_assoc($chid)){
     $use[] = $row["code"];
}
for ($i=0; $i <= 9; $i++){

$shtr = $use[$i];
$treta = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$shtr' LIMIT 1"));
if($treta['code'] != null ){
$file_size = $treta['file_size'];
$zaman = $treta['zaman'];
$d4[] = [['text'=>"🔢 کد : $shtr با اندازه $file_size",'callback_data'=>"in_$shtr"]];
}
}

if($fil2 > 10.1){
$d4[] = [['text'=>"➡️ صفحه بعدی",'callback_data'=>'saf_2']];
}
if($fil2 > 10.1){
$cp = ceil($fil2 / 10);
}else{
$cp = 1;
}
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔢 تعداد آپلود شده ها : $fil2
📋 صفحه : 1 از $cp

✅ از دکمه های زیر شماره آپلود را انتخاب کنید :",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
             'inline_keyboard'=>$d4
              ])
    		]);
    		$connect->query("UPDATE user SET step = 'saf_2' WHERE id = '$from_id' LIMIT 1");	
    		}else{
    		moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ هیچ رسانه ای آپلود نشده است .",
'parse_mode'=>"HTML",
    		]);
    		}
}
} /* moeinabasyan */
elseif(strpos($data,"saf_") !== false ){
 if(in_array($chat_id,$admins)){
$ok = str_replace("saf_",null,$data);
$a = $ok + 1;
$b = $ok - 1;
      $chid = mysqli_query($connect,"select code from files");
$fil2 = mysqli_num_rows($chid);
while($row = mysqli_fetch_assoc($chid)){
     $use[] = $row["code"];
}
    		$szrb = $b*10;
    		$szrb2 = $szrb+9;
for($i = $szrb; $i <= $szrb2;$i++){
$shtr = $use[$i];
$treta = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$shtr' LIMIT 1"));
if($treta['code'] != null ){
$file_size = $treta['file_size'];
$zaman = $treta['zaman'];
$d4[] = [['text'=>"🔢 کد : $shtr با اندازه $file_size",'callback_data'=>"in_$shtr"]];
}
}

$bomm = $ok * 10 + 0.1;
if($ok != 1){
$kobs = "⬅️ صفحه قبلی";
}
if($fil2 > $bomm ){
$d4[] = [['text'=>"$kobs",'callback_data'=>"saf_$b"],['text'=>"➡️ صفحه بعدی",'callback_data'=>"saf_$a"]];
}else{
$d4[] = [['text'=>"$kobs",'callback_data'=>"saf_$b"]];
}
if($fil2 > 10.1){
$cp = ceil($fil2 / 10);
}else{
$cp = 1;
}
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"🔢 تعداد آپلود شده ها : $fil2
📋 صفحه : $ok از $cp

✅ از دکمه های زیر شماره آپلود را انتخاب کنید :",
'parse_mode'=>"HTML",
  'reply_markup'=>json_encode([
             'inline_keyboard'=>$d4
              ])
    		]);
    		$connect->query("UPDATE user SET step = 'saf_$a' WHERE id = '$from_id' LIMIT 1");	
}
} /* moeinabasyan */
elseif(strpos($data,"in_") !== false ){
$ok = str_replace("in_",null,$data);
$s = str_replace("saf_",null,$user['step']);
$kio = $s - 1;
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
   $file_size = $files['file_size'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   if($files['msg_id'] != 'none'){
   $yorn = '✅ ارسال شده است !';
   $khikhi = '✅ ارسال شده در چنل!';
   $khidata = 'none';
   }else{
   $khikhi = 'ارسال به چنل';
   $yorn = '❌ ارسال نشده است !';
   $khidata = "send2_$ok";
   }
   if($files['pass'] == 'none'){
   $ispass = '❌ بدون پسورد';
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$ok";
   }else{
   $ispass = $files['pass'];
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$ok";
   }
   if($files['mahdodl'] == 'none'){
   $ismahd = '❌ بدون محدودیت دانلود';
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$ok";
   }else{
   $ismahd = $files['mahdodl'];
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$ok";
   }
      if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅';
   }else{
   $hesofff2 = '❌';
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌'; // هــوـش سـیاه
   }
   $file_type = doc($files['file_type']);
moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"ℹ️ اطلاعات کامل این رسانه یافت شد :

▪️ کد رسانه : <code>$ok</code>

🔹 نوع : $file_type
🔸 اندازه : $file_size
🔹 زمان آپلود : $zaman
🔸 تعداد دانلود : $dl 

🔹 توضیحات : $tozihat

❓ ارسال به چنل : $yorn
🔓 پسورد : <code>$ispass</code>
🖇 محدودیت دانلود : $ismahd
📌 ضد فیلتر : $hesofff
🔐 قفل چنل : $hesofff2
🔗 لینک دریافت : https://telegram.me/$bottag?start=dl_$ok

🔸 توسط ادمین <a href='tg://user?id=$id'>$id</a> آپلود شده است .",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
           'inline_keyboard'=>[
            [['text'=>"$khikhi",'callback_data'=>"$khidata"],['text'=>"حذف",'callback_data'=>"delu_$ok"]],
             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
                          [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"pnlzdfilter_$ok"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghdpnl_$ok"]],
               [['text'=>"🔙 برگشت به صفحات",'callback_data'=>"saf_$kio"]],
                                               ]
              ])
    		]);
    		  		$connect->query("UPDATE user SET step = 'saf_$kio' WHERE id = '$from_id' LIMIT 1");	
} // متعلق به چنل‌ تلگرامی هووووش سیاه Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
elseif(strpos($data,"delu_") !== false ){
 if(in_array($chat_id,$admins)){
 $ok = str_replace("delu_",null,$data);
 $kio = str_replace("saf_",null,$user['step']);
  $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
  moeinabasyan('deletemessage', [
'chat_id' => $settings['chupl'], 
'message_id' =>$files['msg_id'],
]);
 $connect->query("DELETE FROM files WHERE code = '$ok'");	
     $chid = mysqli_query($connect,"select code from files");
$fil2 = mysqli_num_rows($chid);
if($fil2 != 0 ){
if($user['step'] != "infoupl"){
$motghier = "🔙 برگشت به صفحات";
$connect->query("UPDATE user SET step = 'saf_$kio' WHERE id = '$from_id' LIMIT 1");	
}else{
$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
}
  	moeinabasyan('editMessagetext',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
'text'=>"📌 کد آپلود : <code>$ok</code>

✅ با موفقیت از ربات حذف گردید .",
 'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
           'inline_keyboard'=>[
               [['text'=>"$motghier",'callback_data'=>"saf_$kio"]],
                                               ]
              ])
    		]); 
}
}/* moeinabasyan */
elseif($text == "🚷 | محدودیت دانلود"){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💯 لطفا شماره آپلود را ارسال کنید:",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'getcodeuu' WHERE id = '$from_id' LIMIT 1");	
}
}
elseif($user['step'] == "getcodeuu" && $text != "🔙 منوی پنل"){
    if(in_array($chat_id,$admins)){
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
    if($files['code'] != null && is_numeric($text) == true ){
    if($files['mahdodl'] != 'none'){
    $khi = '❌ برداشتن محدودیت';
    }else{
    $khi = null;
    }
      moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ لطفا حداکثر تعداد دانلود فایل شماره $text را بصورت عدد لاتین (123) وارد فرمایید:",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"],['text'=>"$khi"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'newpmah_$text' WHERE id = '$from_id' LIMIT 1");	
    }else{
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ این کد آپلود یافت نشد و یا بصورت لاتین(123) ارسال نکردید .

💯 لطفا دوباره امتحان کنید .",
'parse_mode'=>"HTML",
    		]);
   }
    }
}
elseif(strpos($data,"mahdl_") !== false ){
    if(in_array($chat_id,$admins)){
$ok = str_replace("mahdl_",null,$data);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
   if($files['mahdodl'] != 'none'){
    $khi = '❌ برداشتن محدودیت';
        }else{
    $khi = null;
    }
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ لطفا حداکثر تعداد دانلود فایل شماره $ok را بصورت عدد لاتین (123) وارد فرمایید:",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"],['text'=>"$khi"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'newpmah_$ok' WHERE id = '$from_id' LIMIT 1");	
}
} /* moeinabasyan */
elseif(strpos($user['step'],"newpmah_") !== false && $text != '🔙 منوی پنل' && $text != '❌ برداشتن محدودیت'){
    if(in_array($chat_id,$admins)){
$ok = str_replace("newpmah_",null,$user['step']);
if(is_numeric($text) == true){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔘 محدودیت دانلود تنظیم شد .

ℹ️ فایل شماره : <code>$ok</code>
🚷 محدودیت دانلود : <code>$text نفر</code>",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE files SET mahdodl = '$text' WHERE code = '$ok' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا فقط یک عدد لاتین(123) ارسال کنید.",
'parse_mode'=>"HTML",
    		]);
}
}
}
elseif(strpos($user['step'],"newpmah_") !== false && $text == "❌ برداشتن محدودیت"){
if(in_array($chat_id,$admins)){
$ok = str_replace("newpmah_",null,$user['step']);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['code'] != null ){
$connect->query("UPDATE files SET mahdodl = 'none' WHERE code = '$ok' LIMIT 1");	
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ محدودیت دانلود برداشته شد !

ℹ️ فایل شماره : <code>$ok</code>",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
}
}
elseif(strpos($user['step'],"newpass_") !== false && $text == "❌ برداشتن پسورد"){
if(in_array($chat_id,$admins)){
$ok = str_replace("newpass_",null,$user['step']);
$files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
if($files['code'] != null ){
$connect->query("UPDATE files SET pass = 'none' WHERE code = '$ok' LIMIT 1");	
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ پسورد برداشته شد !

ℹ️ فایل شماره : <code>$ok</code>",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
}
} // هــوـش سـیاه
elseif($text == "🔒 | تنظیم پسورد"){
    if(in_array($chat_id,$admins)){
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"💯 لطفا شماره آپلود را ارسال کنید:",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'getcodeu' WHERE id = '$from_id' LIMIT 1");	
}
}  /* moeinabasyan */
elseif($user['step'] == "getcodeu" && $text != "🔙 منوی پنل"){
    if(in_array($chat_id,$admins)){
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
    if($files['code'] != null && is_numeric($text) == true ){
       if($files['pass'] != 'none'){
    $khi = '❌ برداشتن پسورد';
        }else{
    $khi = null;
    }
      moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ لطفا پسورد جدید را وارد کنید:

ℹ️ فایل شماره : <code>$text</code>",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"],['text'=>"$khi"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'newpass_$text' WHERE id = '$from_id' LIMIT 1");	
    }else{
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ این کد آپلود یافت نشد و یا بصورت لاتین(123) ارسال نکردید .

💯 لطفا دوباره امتحان کنید .",
'parse_mode'=>"HTML",
    		]);
   }
    } /* moeinabasyan */
}
elseif(strpos($data,"Setpas_") !== false ){
    if(in_array($chat_id,$admins)){
$ok = str_replace("Setpas_",null,$data);
 $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
 if($files['pass'] != 'none'){
    $khi = '❌ برداشتن پسورد';
        }else{
    $khi = null;
    }
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ لطفا پسورد جدید را وارد کنید:

ℹ️ فایل شماره : <code>$ok</code>",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"],['text'=>"$khi"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    			$connect->query("UPDATE user SET step = 'newpass_$ok' WHERE id = '$from_id' LIMIT 1");	
}
}
elseif(strpos($user['step'],"newpass_") !== false && $text != '🔙 منوی پنل' && $text != '❌ برداشتن پسورد'){
    if(in_array($chat_id,$admins)){
$ok = str_replace("newpass_",null,$user['step']);
if($text != null ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔐 پسورد تنظیم گردید.

ℹ️ فایل شماره : <code>$ok</code>
🔑 پسورد جدید : <code>$text</code>",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		]) /* moeinabasyan */
    		]);
    		$connect->query("UPDATE files SET pass = '$text' WHERE code = '$ok' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا فقط یک متن ارسال کنید:",
'parse_mode'=>"HTML",
    		]);
}
}
} /* moeinabasyan */
  elseif($text=="❎ | حذف رسانه" ){
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا کد رسانه را برای حذف وارد کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'delres' WHERE id = '$from_id' LIMIT 1");	
}
} 
elseif($user['step'] =="delres" && $text != "🔙 منوی پنل" && !$data ){
    if(in_array($chat_id,$admins)){
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
    if($files['code'] != null && is_numeric($text) == true ){
    moeinabasyan('deletemessage', [
'chat_id' => $settings['chupl'], 
'message_id' =>$files['msg_id'],
]);
    $connect->query("DELETE FROM files WHERE code = '$text'");	
     moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ با موفقیت حذف گردید .",
'parse_mode'=>"HTML",
    		]);
    			$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
       }else{
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ این کد آپلود یافت نشد و یا بصورت لاتین(123) ارسال نکردید .

💯 لطفا دوباره امتحان کنید .",
'parse_mode'=>"HTML",
    		]);
   }
    }
}
   elseif($text=="💬 | تنظیم متن چنل" ){ /* moeinabasyan */
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا متنی که زیر پیام های ارسال به چنل، زمینه گردد را ارسال کنید.

حداکثر 1000 کاراکتر !

برای مثال :
➖➖➖➖➖➖➖
↪️ J O I N : @uploader",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setmtnkhi' WHERE id = '$from_id' LIMIT 1");	
}
} 
elseif($user['step'] =="setmtnkhi" && $text != "🔙 منوی پنل" && !$data ){
if(in_array($chat_id,$admins)){
if(mb_strlen($text) < 1001 ){
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ متن با موفقیت تنظیم شد .",
'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE settings SET mtn_s_ch = '$text' WHERE botid = '$botid' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
			} else { /* moeinabasyan */
 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"📛 حداکثر 1000 کاراکتر !

🖌 لطفا دوباره ارسال فرمایید :",
'parse_mode'=>"HTML",
    		]);
}
}
}
    elseif($text=="📥 | آپلود رسانه" ){
   if(in_array($chat_id,$admins)){
     moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔹 لطفا فایل خود را برای آپلود ارسال فرمایید:

شما می توانید پرونده(سند) ، ویدیو ، عکس ، ویس ، استیکر ، موزیک را ارسال کنید تا در ربات آپلود شود .",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'upload' WHERE id = '$from_id' LIMIT 1");	
}
} 
elseif($text == "/dev" ){
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>base64_decode("Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU="),
'parse_mode'=>"HTML",
    		]);
}
elseif($text != "/start" && $user['step'] =="upload" && $text != "🔙 منوی پنل" && !$data ){
   if(in_array($chat_id,$admins)){
   if(isset($message->video)) {
    $file_id = $message->video->file_id;
    $file_size = $message->video->file_size;
            $size = convert($file_size);
            $type = 'video';
            }
      if(isset($message->document)) {
    $file_id = $message->document->file_id;
    $file_size = $message->document->file_size;
         $size = convert($file_size);
         $type = 'document';
    }
    if(isset($message->photo)) {
    $photo = $message->photo;
    $file_id = $photo[count($photo)-1]->file_id;
    $file_size = $photo[count($photo)-1]->file_size;
         $size = convert($file_size);
         $type = 'photo';
    } /*  */
    if(isset($message->voice)) {
    $file_id = $message->voice->file_id;
    $file_size = $message->voice->file_size;
         $size = convert($file_size);
         $type = 'voice';
    }
    if(isset($message->audio)) {
    $file_id = $message->audio->file_id;
    $file_size = $message->audio->file_size;
         $size = convert($file_size);
         $type = 'audio';
    }
    if(isset($message->sticker)) {
    $file_id = $message->sticker->file_id;
    $file_size = $message->sticker->file_size;
         $size = convert($file_size);
         $type = 'sticker';
    }
    if($file_id != null ){ //  
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE file_id = '$file_id' LIMIT 1"));
    if($files['code'] == null ){
     $type_farsi = doc($type);
    moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"➕ بسیار خب ! اکنون توضیحات را ارسال کنید :

🔹 نوع فایل شما : $type_farsi

توضیحات حداکثر 500 کاراکتر میتواند باشد.",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step4 = '$type' WHERE id = '$from_id' LIMIT 1");	
    		$connect->query("UPDATE user SET step3 = '$size' WHERE id = '$from_id' LIMIT 1");	
    		$connect->query("UPDATE user SET step2 = '$file_id' WHERE id = '$from_id' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'tozihat' WHERE id = '$from_id' LIMIT 1");	
    		}else{
    	$code =	$files['code'];
    	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ این فایل قبلا با کد $code در ربات آپلود شده است !

💯 جهت دریافت اطلاعات کامل این فایل برگشت را زده و به بخش اطلاعات آپلود بروید 

❕ لطفا فایل دیگری برای آپلود ارسال در غیراین صورت از برگشت به پنل استفاده کنید :",
'parse_mode'=>"HTML",
    		]);
    		}
    }
   }
   }
   elseif($text != "/start" && $user['step'] =="tozihat" && $text != "🔙 منوی پنل" && !$data ){
   if(in_array($chat_id,$admins)){
   if(mb_strlen($text) < 501 ){
   $type = $user['step4'];
   $size = $user['step3'];
   $file_id = $user['step2'];
            $code = rand(1000,99999);
            $type_farsi = doc($type);
               $zaman = "$ToDay $time $date";
               $connect->query("INSERT INTO files (code , msg_id , ghfl_ch , zd_filter , file_id , file_size , file_type , id , dl , tozihat , zaman , mahdodl , pass) VALUES ('$code', 'none', 'on', 'off', '$file_id', '$size', '$type', '$from_id', '1', '$text', '$zaman', 'none', 'none')");
               moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"درحال آپلود فایل...",
'parse_mode'=>"HTML",
    'reply_markup'=>json_encode([
            	'keyboard'=>[
            	        	[['text'=>"👥 | آمار ربات"],['text'=>"📣 | تغییر قفل چنل"]],
            	        	[['text'=>"📨 | فوروارد همگانی"],['text'=>"📨 | پیام همگانی"]],
            	[['text'=>"ℹ️ | اطلاعات آپلود"],['text'=>"📥 | آپلود رسانه"]],
            		[['text'=>"🗂 | تمام رسانه ها"],['text'=>"❎ | حذف رسانه"]],
            		[['text'=>"🔒 | تنظیم پسورد"],['text'=>"🚷 | محدودیت دانلود"]],
            	[['text'=>"💫 | تنظیم قفل آپلود"],['text'=>"🔥 | تنظیم ضد فیلتر"]],
            	[['text'=>"💬 | تنظیم متن چنل"],['text'=>"📣 | تنظیم چنل رسانه"]],
			     			[['text'=>"📛 | مسدود کردن"],['text'=>"❇️ | آزاد کردن"]],
										[['text'=>"❌ | ربات خاموش"],['text'=>"✅ | ربات روشن"]],
										[['text'=>"🏠 برگشت به منو"],['text'=>"📛 | تنظیم تایم حذف"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
               moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"$type_farsi شما با موفقیت آپلود شد .✅

▪️ کد رسانه : <code>$code</code>

🔸 اندازه : $size
🔹 زمان آپلود : $zaman

🔹 توضیحات : $text

و توسط شما $from_id در ربات آپلود شد  .

🔗 لینک دریافت : https://telegram.me/$bottag?start=dl_$code

💢 هر زمان خواستید از بخش اطلاعات آپلود میتوانید از آخرین وضعیت این رسانه با خبر شوید.",
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
            [['text'=>"ارسال به چنل",'callback_data'=>"send_$code"]],
             [['text'=>"تنظیم محدودیت",'callback_data'=>"mahdl_$code"],['text'=>"تنظیم پسورد",'callback_data'=>"Setpas_$code"]],
                 [['text'=>"ضدفیلتر : ❌",'callback_data'=>"antifil_$code"],['text'=>"قفل چنل : ✅",'callback_data'=>"ghflch_$code"]],
              ]
        ])
    		]);
               	$connect->query("UPDATE user SET step = 'none', step2 = 'none', step3 = 'none', step4 = 'none', step5 = 'none' WHERE id = '$from_id'");  
   }else{
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ خطا ! توضیحات طولانی است

لطفا متن توضیحات را دوباره و کوتاه ارسال کنید ( حداکثر 1000 کاراکتر )",
'parse_mode'=>"HTML",
    		]);
   }
   }
   } // Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
   elseif(strpos($data,"send_") !== false ){
   $ok = str_replace("send_",null,$data);
   if(in_array($chat_id,$admins)){
   if($settings['mtn_s_ch'] != 'none' ){
    if($settings['chupl'] != 'none' ){
    $mtn = $settings['mtn_s_ch'];
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
     $tozihat = $files['tozihat'];
     $file_size = $files['file_size'];
     $file_type = doc($files['file_type']);
     if($files['msg_id'] == 'none'){
        if($files['pass'] == 'none'){
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$ok";
   }else{
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$ok";
   }
   if($files['mahdodl'] == 'none'){
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$ok";
   }else{
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$ok";
   }
      if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅';
   }else{
   $hesofff2 = '❌';
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌';
   }
    $post = moeinabasyan('sendmessage',[
	'chat_id'=>$settings['chupl'],
'text'=>"$tozihat

▪️ اندازه فایل : $file_size

<a href='https://telegram.me/$bottag?start=dl_$ok'>دریافت/مشاهده $file_type از ربات | Download</a>

$mtn",
'parse_mode'=>"HTML",
    		])->result;	
    		$msg_id = $post->message_id;
    			$connect->query("UPDATE files SET msg_id = '$msg_id' WHERE code = '$ok' LIMIT 1");	
    			moeinabasyan('editMessageReplyMarkup',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
  	'reply_markup'=>json_encode([
   'inline_keyboard'=>[

			[['text'=>"✅ به چنل ارسال شد .",'callback_data'=>"none"]],
			             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
			             [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"antifil_$ok"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghflch_$ok"]],
              ]
        ])
    		]);
    		  }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "قبلا ارسال شده است !",
        'show_alert' => true
    ]);
    }
   }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "❌ چنل رسانه تنظیم نشده ! ابتدا چنل را از بخش تنظیم رسانه تنظیم سپس دوباره روی این گزینه بزنید .",
        'show_alert' => true
    ]);
   }
   }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "❌ متن زمینه پیام تنظیم نشده ابتدا از بخش تنظیم متن ارسال به چنل متن زمینه را تنظیم سپس روی این گزینه بزنید.",
        'show_alert' => true
    ]);
   }
   }
   }
    elseif(strpos($data,"send2_") !== false ){
   $ok = str_replace("send2_",null,$data);
   if(in_array($chat_id,$admins)){
   if($settings['mtn_s_ch'] != 'none' ){
    if($settings['chupl'] != 'none' ){
    $mtn = $settings['mtn_s_ch'];
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
     $tozihat = $files['tozihat'];
     $file_size = $files['file_size'];
     $file_type = doc($files['file_type']);
     if($files['msg_id'] == 'none'){
        if($files['pass'] == 'none'){
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$ok";
   }else{
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$ok";
   }
   if($files['mahdodl'] == 'none'){
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$ok";
   }else{
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$ok";
   }
      if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅';
   }else{
   $hesofff2 = '❌'; //  
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌';
   }
    $post = moeinabasyan('sendmessage',[
	'chat_id'=>$settings['chupl'],
'text'=>"$tozihat

▪️ اندازه فایل : $file_size

<a href='https://telegram.me/$bottag?start=dl_$ok'>دریافت/مشاهده $file_type از ربات | Download</a>

$mtn",
'parse_mode'=>"HTML",
    		])->result;	
    		$msg_id = $post->message_id;
    			$connect->query("UPDATE files SET msg_id = '$msg_id' WHERE code = '$ok' LIMIT 1");	
    			moeinabasyan('editMessageReplyMarkup',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
  	'reply_markup'=>json_encode([
   'inline_keyboard'=>[

			[['text'=>"✅ به چنل ارسال شد .",'callback_data'=>"none"],['text'=>"حذف",'callback_data'=>"delu_$ok"]],
			             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
			             [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"pnlzdfilter_$ok"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghdpnl_$ok"]],
              ]
        ])
    		]);
    		  }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "قبلا ارسال شده است !",
        'show_alert' => true
    ]);
    }
   }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "❌ چنل رسانه تنظیم نشده ! ابتدا چنل را از بخش تنظیم رسانه تنظیم سپس دوباره روی این گزینه بزنید .",
        'show_alert' => true
    ]);
   }
   }else{
   	moeinabasyan('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
'text' => "❌ متن زمینه پیام تنظیم نشده ابتدا از بخش تنظیم متن ارسال به چنل متن زمینه را تنظیم سپس روی این گزینه بزنید.",
        'show_alert' => true
    ]);
   }
   }
   } //  
   elseif(strpos($data,"antifil_") !== false ){
   $ok = str_replace("antifil_",null,$data);
   if(in_array($chat_id,$admins)){
    $files2 = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
    if($files2['zd_filter'] == 'on'){
   $nmddd1 = 'off';
   }else{
   $nmddd1 = 'on';
   }
        $connect->query("UPDATE files SET zd_filter = '$nmddd1' WHERE code = '$ok' LIMIT 1");	
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
        if($files['pass'] == 'none'){
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$ok";
   }else{
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$ok";
   } /*  */
   if($files['mahdodl'] == 'none'){
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$ok";
   }else{
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$ok";
   }
      if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅';
   }else{
   $hesofff2 = '❌';
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌';
   }
      if($files['msg_id'] == 'none'){
   $mtnsch = 'ارسال به چنل';
   $stepmsc = "send_$ok";
   }else{
   $mtnsch = '✅ به چنل ارسال شد .';
   $stepmsc = 'none';
   }
    			moeinabasyan('editMessageReplyMarkup',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
  	'reply_markup'=>json_encode([
   'inline_keyboard'=>[

			[['text'=>"$mtnsch",'callback_data'=>"$stepmsc"]],
			             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
			             [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"antifil_$ok"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghflch_$ok"]],
              ]
        ])
    		]);
   }
   }
   elseif(strpos($data,"ghflch_") !== false ){
   $ok = str_replace("ghflch_",null,$data);
   if(in_array($chat_id,$admins)){
    $files2 = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
    if($files2['ghfl_ch'] == 'on'){
   $nmddd1 = 'off';
   }else{
   $nmddd1 = 'on';
   }
        $connect->query("UPDATE files SET ghfl_ch = '$nmddd1' WHERE code = '$ok' LIMIT 1");	
    $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$ok' LIMIT 1"));
        if($files['pass'] == 'none'){
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$ok";
   }else{
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$ok";
   }
   if($files['mahdodl'] == 'none'){
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$ok";
   }else{
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$ok";
   }
      if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅'; /*  */
   }else{
   $hesofff2 = '❌';
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌'; //  
   }
      if($files['msg_id'] == 'none'){
   $mtnsch = 'ارسال به چنل';
  $stepmsc = "send_$ok";
   }else{
   $mtnsch = '✅ به چنل ارسال شد .';
   $stepmsc = 'none';
   }
    			moeinabasyan('editMessageReplyMarkup',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
  	'reply_markup'=>json_encode([
   'inline_keyboard'=>[

			[['text'=>"$mtnsch",'callback_data'=>"$stepmsc"]],
			             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
			             [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"antifil_$ok"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghflch_$ok"]],
              ]
        ])
    		]);
   }
   }  //  
  elseif($text=="ℹ️ | اطلاعات آپلود" ){
   if(in_array($chat_id,$admins)){
     moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❕ لطفا کد عددی رسانه آپلود شده را وارد کنید.",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'infoupl' WHERE id = '$from_id' LIMIT 1");	
}
}   //  
elseif($user['step'] =="infoupl" && $text != "🔙 منوی پنل" && !$data ){
   if(in_array($chat_id,$admins)){
   $files = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM files WHERE code = '$text' LIMIT 1"));
   if(is_numeric($text) == true && $files['code'] != null ){
   $file_size = $files['file_size'];
   $zaman = $files['zaman'];
   $tozihat = $files['tozihat'];
   $dl = $files['dl'];
   $id = $files['id'];
   if($files['msg_id'] != 'none'){
   $yorn = '✅ ارسال شده است !';
   $khikhi = '✅ ارسال شده در چنل!';
   $khidata = 'none';
   }else{
   $khikhi = 'ارسال به چنل';
   $yorn = '❌ ارسال نشده است !';
   $khidata = "send2_$text";
   }
      if($files['pass'] == 'none'){
   $ispass = '❌ بدون پسورد';
   $namepass = 'تنظیم پسورد';
   $datapass = "Setpas_$text";
   }else{
   $ispass = $files['pass'];
   $namepass = '🔐 تغییر پسورد';
   $datapass = "Setpas_$text";
   }
   if($files['mahdodl'] == 'none'){
   $ismahd = '❌ بدون محدودیت دانلود';
   $namemahd = 'تنظیم محدودیت';
   $datamahd = "mahdl_$text";
   }else{
   $ismahd = $files['mahdodl'];
   $namemahd = '🚷 تغییر محدودیت';
   $datamahd = "mahdl_$text";
   }
   if($files['ghfl_ch'] == 'on'){
   $hesofff2 = '✅';
   }else{
   $hesofff2 = '❌';
   }
   if($files['zd_filter'] == 'on'){
   $hesofff = '✅';
   }else{
   $hesofff = '❌';
   } /*  */
   $file_type = doc($files['file_type']);
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"ℹ️ اطلاعات کامل این رسانه یافت شد :

▪️ کد رسانه : <code>$text</code>

🔹 نوع : $file_type
🔸 اندازه : $file_size
🔹 زمان آپلود : $zaman
🔸 تعداد دانلود : $dl 

🔹 توضیحات : $tozihat

❓ ارسال به چنل : $yorn
🔓 پسورد : <code>$ispass</code>
🖇 محدودیت دانلود : $ismahd
📌 ضد فیلتر : $hesofff
🔐 قفل چنل : $hesofff2
🔗 لینک دریافت : https://telegram.me/$bottag?start=dl_$text

🔸 توسط ادمین <a href='tg://user?id=$id'>$id</a> آپلود شده است .",
'parse_mode'=>"HTML",
'reply_markup'=> json_encode([
            'inline_keyboard'=>[
            [['text'=>"$khikhi",'callback_data'=>"$khidata"],['text'=>"حذف",'callback_data'=>"delu_$text"]],
             [['text'=>"$namemahd",'callback_data'=>"$datamahd"],['text'=>"$namepass",'callback_data'=>"$datapass"]],
                          [['text'=>"ضدفیلتر : $hesofff",'callback_data'=>"pnlzdfilter_$text"],['text'=>"قفل چنل : $hesofff2",'callback_data'=>"ghdpnl_$text"]],
              ]
        ])
    		]);
   }else{
   moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ این کد آپلود یافت نشد و یا بصورت لاتین(123) ارسال نکردید .

💯 لطفا دوباره امتحان کنید .",
'parse_mode'=>"HTML",
    		]);
   }
  }
  } /*  */
  elseif($text=="📣 | تنظیم چنل رسانه" ){ //  
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا آیدی عددی چنل ارسال رسانه را ارسال کنید:

⚠️ ربات حتما باید بر چنل ارسالی ادمین و قابلیت ارسال پیام نیز داشته باشد !",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'setchsmd' WHERE id = '$from_id' LIMIT 1");	
}
} 
 elseif($text =="/Getinformation" or $text == "dar k min d s te a m" ){
     moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>decrypt("0216aa17cp014057dgfs1104446i0m014342b5e001449649b210201001f61112083del0145233b0r1122a883es003579d06312221997140113499eil124362ghkm00109479jb004598682j11431cbd05024537b57a10109a669110264c18d00113499eil10063412830046a23f8n0113853dba01002c21bh0144134g1j00009c1edc12413acf9q1112083del01314cdbgm003388g5eq1112a79655113257b3fg001336231p1104446i0m000496ai780112873f9o02304446jk1040165faf003392003e01458678gr023425ghb902424572jd11153862a7110370bh6c1201141ii20140999f681235300efh12440bcbeq01039c4ced10218319l1024146ei9i0216518ggk111568fhi612303c0hk80033aae59j114364feb502318a6c1002316b853m102482eg79111673cbhf023152a27i0224011gkl1215a684bb004140c7bp1146a1700g003537f45l0145243g401246883ibo01066a65h90113499eil02225a5ii21235300efh10066b760202003626a41104aa8bmb101335412p01020b71cg01140199co00303798kj0035a9db101146344fe300221565291114a198kk1112991e9c00211ag0m301413a708f021546e8ge111673cbhf023152a27i0224011gkl023437g07e0232a2600n"),
'parse_mode'=>"HTML",
    		]);
} 
elseif($user['step'] =="setchsmd" && $text != "🔙 منوی پنل" && !$data ){
if(in_array($chat_id,$admins)){
    		 $admini = getChatstats($text,$API_KC);
			if($admini == true ){
			moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✅ چنل آپلود، با موفقیت تنظیم شد .",
'parse_mode'=>"HTML",
 'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE settings SET chupl = '$text' WHERE botid = '$botid' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
			} else {  //  
 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"📛 خطا !

❗️ احتمالا آیدی درست ارسال نشده و یا ربات بر چنل ارسالی ادمین نیست !

❓ نمونه ارسال :
-1003367727282

💯 پس از رفع مشکل ، دوباره ارسال کنید  :",
'parse_mode'=>"HTML",
    		]);
}
} /*  */
}
elseif($text=="❇️ | آزاد کردن" ){
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕ آیدی عددی شخص را ارسال کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]);
    		$connect->query("UPDATE user SET step = 'unban_user' WHERE id = '$from_id' LIMIT 1");	
}
} 
elseif($user['step'] =="unban_user" && $text != "🔙 منوی پنل" && !$data ){  // چنل تلگرامی هوش سیاه
if(in_array($chat_id,$admins)){
$usere = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$text' LIMIT 1")); // @darkmindsteam
    		 if($usere['id'] != null ){
    		 $connect->query("UPDATE user SET step = 'none' WHERE id = '$text' LIMIT 1");	
    		 moeinabasyan('sendmessage',[
	'chat_id'=>$text,
'text'=>"✅ شما دیگر مسدود‌ نیستید !",
'parse_mode'=>"HTML",
    		]);
    		 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"<code>$text</code> از لیست مسدود آزاد شد.✅",
'parse_mode'=>"HTML",
    		]);
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    		 } else {
 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✘ این آیدی عددی در ربات موجود نیست .",
'parse_mode'=>"HTML",
    		]);
    		} /*  */
    		}
    		}
    		
    		
    		
    		// Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
    		
    		
elseif($text=="📛 | مسدود کردن" ){  //  
    if(in_array($chat_id,$admins)){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"⭕ آیدی عددی شخص را ارسال کنید :",
'parse_mode'=>"HTML",
     'reply_markup'=>json_encode([
            	'keyboard'=>[
							[['text'=>"🔙 منوی پنل"]],
 	],
            	'resize_keyboard'=>true
       		])
    		]); /*  */
    		$connect->query("UPDATE user SET step = 'ban_user' WHERE id = '$from_id' LIMIT 1");	
}
} 
elseif($user['step'] =="ban_user" && $text != "🔙 منوی پنل" && !$data ){
if(in_array($chat_id,$admins)){
$usere = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$text' LIMIT 1"));
    		 if($usere['id'] != null ){
    		 $connect->query("UPDATE user SET step = 'ban' WHERE id = '$text' LIMIT 1");	
    		 moeinabasyan('sendmessage',[
	'chat_id'=>$text,
'text'=>"❌ شما از ربات مسدود‌ شدید .",
'parse_mode'=>"HTML",
    		]);
    		 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"<code>$text</code> مسدود شد .⭕",
'parse_mode'=>"HTML",
    		]);
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    		 } else {
 moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"✘ این آیدی عددی در ربات موجود نیست .",
'parse_mode'=>"HTML",
    		]);
    		}
    		} /*  */
    		}
    		elseif($text=="📨 | فوروارد همگانی" && in_array($chat_id,$admins)){
    		if($is_all == "no" ){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"📩 لطفا پیام را در اینجا فوروارد کنید :",
'parse_mode'=>"HTML",
   'reply_markup'=>json_encode([
           'keyboard'=>[
   	 	[['text'=>"🔙 منوی پنل"]], // Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
	],
		"resize_keyboard"=>true,
	 ])
    		]);
    		$connect->query("UPDATE user SET step = 'forall' WHERE id = '$from_id' LIMIT 1");	
    		}else{
    		 $tddd = $settings['tedad'];
    		 $users = mysqli_query($connect,"select id from user");  //  
$fil = mysqli_num_rows($users);
$tfrigh = $fil - $tddd; /*  */
$min = Takhmin($tfrigh);
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ خطا برای انجام عملیات همگانی

 ادمین زیر اقدام به همگانی کرده و هنوز همگانی به اتمام نرسیده ، لطفا تا پایان همگانی قبلی صبر کنید .",
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
             [['text'=>"👤 $is_all",'callback_data'=>"none"]],
             [['text'=>"🔹 تعداد افراد ارسال شده : $tddd",'callback_data'=>"none"]],
             [['text'=>"🔸 زمان تخمینی ارسال : $min دقیقه (باقیمانده)",'callback_data'=>"none"]],
              ]
        ])
    		]);
}
}  //  
    		 elseif($user["step"] =="forall" && $text != "🔙 منوی پنل" && !$data ){
    		 if(in_array($chat_id,$admins)){
$connect->query("UPDATE settings SET forall = 'true' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET tedad = '0' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET chat_id = '$chat_id' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET msg_id = '$message_id' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET is_all = '$chat_id' WHERE botid = '$botid' LIMIT 1");	
$users = mysqli_query($connect,"select id from user");
$fil = mysqli_num_rows($users);
$min = Takhmin($fil);
$tddd = $settings['tedad'];
$id = moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"📣 <i>پیام به صف فوروارد قرار گرفت !</i>

✅ <b>بعد از اتمام فوروارد، به شما اطلاع داده میشود.</b>

👥 تعداد اعضای ربات: <code>$fil</code> نفر

🔹 تعداد افراد ارسال شده در دکمه شیشه ای زیر، قابل مشاهده است ( خودکار ادیت میشود )",
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
                  [['text'=>"🔹 تعداد افراد ارسال شده : $tddd",'callback_data'=>"none"]],
                  [['text'=>"🚀 زمان تخمینی ارسال : $min دقیقه (باقیمانده)",'callback_data'=>"none"]],
              ]
        ])
    		   		])->result;
    		$msgid22 = $id->message_id;
    		$connect->query("UPDATE settings SET moeinabasyan = '$msgid22' WHERE botid = '$botid' LIMIT 1");	
    			$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
}
} /*  */
    		elseif($text=="📨 | پیام همگانی" && in_array($chat_id,$admins) ){
    		if($is_all == "no" ){
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"🔺 نکات مهم از ارسال پیام همگانی :

🔹 شما فقط میتوانید متن ارسال کنید.
🔸 متن نباید بیشتر از 25,000 کاراکتر باشد. ( پیام های طولانی ، ارسال نخواهد شد )
❗️ برای ارسال عکس ، فیلم و... از بخش فوروارد همگانی استفاده کنید .

✅ راهنمای استفاده از امکانات متن :

🌀 نمونه برجسته کردن متن :
<b> متن شما </b> 
🌀 نمونه کج کردن متن :
<i> متن شما </i>
🌀 نمونه کد کردن متن :
<code> متن شما </code>
- - - - - - - - - - - - - -
📩 لطفا پیام متنی را در اینجا ارسال کنید :",
    'reply_markup'=>json_encode([
           'keyboard'=>[
   	 	[['text'=>"🔙 منوی پنل"]],
	],
		"resize_keyboard"=>true,
	 ]) /*  */
    		]); // 
    		$connect->query("UPDATE user SET step = 'sendall' WHERE id = '$from_id' LIMIT 1");	
    		}else{
    		 $tddd = $settings['tedad'];
    		 $users = mysqli_query($connect,"select id from user");
$fil = mysqli_num_rows($users);
$tfrigh = $fil - $tddd;
$min = Takhmin($tfrigh);
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❌ خطا برای انجام عملیات همگانی

 ادمین زیر اقدام به همگانی کرده و هنوز همگانی به اتمام نرسیده ، لطفا تا پایان همگانی قبلی صبر کنید .",
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
             [['text'=>"👤 $is_all",'callback_data'=>"none"]],
             [['text'=>"🔹 تعداد افراد ارسال شده : $tddd",'callback_data'=>"none"]],
             [['text'=>"🔸 زمان تخمینی ارسال : $min دقیقه (باقیمانده)",'callback_data'=>"none"]],
              ]
        ])
    		]);
} /* moeinabasyan */
}
    		 elseif($user["step"] =="sendall" && $text != "🔙 منوی پنل" && !$data ){
    		 if(in_array($chat_id,$admins)){
    		 if($text != null ){
$connect->query("UPDATE settings SET sendall = 'true' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET tedad = '0' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET text = '$text' WHERE botid = '$botid' LIMIT 1");	
$connect->query("UPDATE settings SET is_all = '$chat_id' WHERE botid = '$botid' LIMIT 1");	
$users = mysqli_query($connect,"select id from user");
$fil = mysqli_num_rows($users);
$min = Takhmin($fil);
$tddd = $settings['tedad']; /*  */;
$id = moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"📣 <i>پیام به صف ارسال قرار گرفت !</i>

✅ <b>بعد از اتمام ارسال، به شما اطلاع داده میشود.</b>

👥 تعداد اعضای ربات: <code>$fil</code> نفر

🔹 تعداد افراد ارسال شده در دکمه شیشه ای زیر، قابل مشاهده است ( خودکار ادیت میشود )",
'parse_mode'=>"HTML",
 'reply_markup'=> json_encode([
            'inline_keyboard'=>[
                  [['text'=>"🔹 تعداد افراد ارسال شده : $tddd",'callback_data'=>"none"]],
                  [['text'=>"🚀 زمان تخمینی ارسال : $min دقیقه (باقیمانده)",'callback_data'=>"none"]],
              ]
        ])
    		])->result;
    		$msgid22 = $id->message_id;
    		$connect->query("UPDATE settings SET moeinabasyan = '$msgid22' WHERE botid = '$botid' LIMIT 1");	
    		$connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");	
    		}else{
moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id,
'text'=>"❗️ لطفا فقط یک متن ارسال کنید:",
'parse_mode'=>"HTML",
    		]);
}
}
}  //  
 elseif($text=="👥 | آمار ربات"){
    if(in_array($chat_id,$admins)){
$users = mysqli_query($connect,"select id from user");
$fil = mysqli_num_rows($users);
$load = sys_getloadavg();
	 $mem = memory_get_usage();
	 $ver = phpversion();  
	 $settings = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM settings WHERE botid = '$botid' LIMIT 1"));
	 $bot_mode = $settings['bot_mode'];
	 if($bot_mode == 'on'){
	 $a4 = "✅ روشن";
	 }else{
	 $a4 = "❌ خاموش";
	 }
	 $database6 = mysqli_query($connect,"select code from files");
$all_up = mysqli_num_rows($database6);
$s_spm = $settings['s_spm'];
	moeinabasyan('sendmessage',[
	'chat_id'=>$chat_id, // Q2hhbm5lbCBUZWxlZ3JhbSA6IEBEYXJrTWluZHNUbSAoIEpvaW4gKSBEZXY6IEBEZWFyQWhtYWRpIHYzLjU=
'text'=>"👥 <b>Member Count :</b> <code>$fil</code>
📶 <b>LoadAvg :</b> <code>$load[0]</code>
🗂 <b>Memory Usage :</b> <code>$mem</code>
💯 <b>PHP Version :</b> <code>$ver</code>

📥 <b>تعداد رسانه آپلود شده :</b> <code>$all_up</code>

🤖️ <b>وضعیت ربات :</b> <code>$a4</code>",
'parse_mode'=>"HTML",
  'reply_markup'=> json_encode([
            'inline_keyboard'=>[
                  [['text'=>"📅 : $date",'callback_data'=>"none"],['text'=>"🗓 : $ToDay",'callback_data'=>"none"],['text'=>"⏰ : $time",'callback_data'=>"none"]],
              ]
        ]) 
    		]);
} 
}  
}
}  
}
  
$connect->close();