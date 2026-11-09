<?php
// IR Creator
ob_start();
error_reporting(0);
header("HTTP/1.0 200 OK");
date_default_timezone_set("Asia/Tehran");
if(file_exists("error_log")){
unlink("error_log");
}
//include '../../Function.php';
///////////////////
$userbott = "USERBOT"; // دست نزنيید
$updating="6.4";
define('API_KEY' , "APITOKENBOT"); // دست نزنید
////////////////// اطلاعات 
$token_pedar = "TOKENBOT"; // توکن رباتساز
$domain_url = "https://domain.ir"; // دامین
$channel_name_fa = "نام ربات ساز"; // اسم رباتساز به فارسی
$channel_name_en = "Name Creator"; // اسم رباتساز به انگلیسی
$userid_malek_botsaz = "USERID"; // آیدی عددی مالک اصلی رباتساز
$channel_token_id = "-100"; // آیدی عددی کانال گزارشات
$channel_sazande_id = "USER_NAME_ch"; // آیدی کانال اصلی بدون @
$bot_creator_id = "USER_NAME_BOT"; // آیدی رباتساز اصلی بدون @
include '../../Function.php';
include '../../connect.php';
$telegram_ip_ranges = [

['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20

['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22

];



$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));

$ok=false;



foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {

    // Make sure the IP is valid.

    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));

    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));

    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;

}

if (!$ok) die("");

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function curl($url){
$cSession = curl_init($url);
//curl_setopt($cSession,CURLOPT_URL,"$url");
 curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
 curl_setopt($cSession,CURLOPT_HEADER, false);

$result=curl_exec($cSession);

if(curl_error($ch)){
        return curl_error($ch);
    }else{
return $result;
}
curl_close($cSession);

}
//----------تاریخ jdf--------//

function sm($chat_id,$text,$keyboard=null,$parse_mode= 'Html'){
	$hi = getvalue("data","id",1,"webview");
	if($hi=="on"){
		$disable_web_page_preview=False;
		}else{
			$disable_web_page_preview=True;
			}
			global $messageid;
			global $chatid;
			global $globaltype;
			$mi = getvalue("data","id",1,"replymessage");
			if($mi=="on"){
				$reply=$messageid;
				}else{
					$reply=null;
					}
					$mo = getvalue("data","id",1,"matnforward");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
					
 $gh = bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text' =>$text,
        'reply_to_message_id'=>$reply,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'protect_content'=>$preg,
        'reply_markup'=>$keyboard
        ]);
       
        $id= $gh->result->message_id;
        setvalue("user","chatid",$chatid,"Other6",$id);
        return $gh;
} 
function fm($chatid,$userid,$message){
	$mo = getvalue("data","id",2,"matnforward");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
  return  bot('ForwardMessage',[
        'chat_id'=>$chatid,
        'from_chat_id'=>$userid,
        'protect_content'=>$preg,
        'message_id'=>$message
        ]);
}
function sp($chat_id,$file_id,$message= null,$reply = null,$parse_mode= "Html"){
	$mo = getvalue("data","id",1,"aksfilm");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
    return bot('sendPhoto', [
        'chat_id' => $chat_id,
        'photo' => $file_id,
        'caption' =>$message,
        'protect_content'=>$preg,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function sv($chat_id,$file_id,$message= null,$reply = null,$parse_mode= "Html"){
	$mo = getvalue("data","id",2,"aksfilm");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
   return bot('sendVideo', [
        'chat_id' => $chat_id,
        'video' => $file_id,
        'caption' =>$message,
        'protect_content'=>$preg,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function sd($chat_id,$file_id,$message= null,$reply= null,$parse_mode= "Html"){
	$mo = getvalue("data","id",1,"filevideonote");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
  return  bot('sendDocument', [
        'chat_id' => $chat_id,
        'document' => $file_id,
        'caption' =>$message,
        'protect_content'=>$preg,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function sa($chat_id,$file_id,$message= null,$reply = null,$parse_mode= "Html"){
	$mo = getvalue("data","id",2,"voiceaudio");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
   return bot('sendAudio', [
        'chat_id' => $chat_id,
        'audio' => $file_id,
        'caption' =>$message,
        'protect_content'=>$preg,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function svo($chat_id,$file_id,$message= null,$reply=null,$parse_mode= "Html"){
	$mo = getvalue("data","id",1,"voiceaudio");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
   return bot('sendVoice', [
        'chat_id' => $chat_id,
        'voice' => $file_id,
        'caption' =>$message,
        'protect_content'=>$preg,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function ss($chat_id,$file_id){
	$mo = getvalue("data","id",1,"stickerdice");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
    return bot('sendSticker', [
        'chat_id' => $chat_id,
        'protect_content'=>$preg,
        'sticker' => $file_id,
        ]);
}
function sco($chat_id,$file_id,$name){
   return bot('sendContact', [
        'chat_id' => $chat_id,
        'phone_number' => $file_id,
        'first_name'=>$name
        ]);
}
function slo($chat_id,$file_id,$name){
  return bot('sendLocation', [
        'chat_id' => $chat_id,
        'latitude' => $file_id,
        'longitude'=>$name
        ]);
}
function svin($chat_id,$file_id){
	$mo = getvalue("data","id",1,"filevideonote");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
   return bot('sendVideoNote', [
        'chat_id' => $chat_id,
        'protect_content'=>$preg,
        'video_note' => $file_id
        ]);
}
function sdi($chat_id,$file_id){
	$mo = getvalue("data","id",2,"stickerdice");
			if($mo=="on"){
				$preg=true;
				}else{
					$preg=false;
					}
$vb =    bot('sendDice', [
        'chat_id' => $chat_id,
        'protect_content'=>$preg,
        'emoji' => $file_id,
        ]);
        return $vb;
}
function dm($chat_id,$message_id){
return   bot('deleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        ]);
}
function erm($chatid,$message,$reply_markup){
   return bot('editMessageReplyMarkup',[
        'chat_id'=>$chatid,
        'message_id'=>$message,
        'reply_markup'=>$reply_markup
        ]);
}
function em($chatid,$text,$message,$keyboard=null,$disable_web_page_preview=false,$parse_mode =  'Html'){
 return bot('editMessageText',[
        'chat_id'=>$chatid,
        'text'=>$text,
        'message_id'=>$message,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function alert($querid,$qt,$type=true){
return bot('answerCallbackQuery',[
		'callback_query_id'=>$querid,
		'text'=>$qt,
		'show_alert'=>$type
		]);
}
function ToDie(){
	global $con;
	$vars = array_keys(get_defined_vars());
for ($i = 0; $i < sizeOf($vars); $i++) {
    unset($$vars[$i]);
}
unset($vars,$i);
mysqli_close($con);
die();
}

function textToinline($text,$text2 = null){
	global $messageid;
	global $fromid;
	global $userbott;
	global $con;
preg_match_all("/(%)(.*)(%)/",$text,$k);
$ck = $k[2];
$x=0;
$y=0;
foreach($ck as $m){
	$x=0;
		//	$v=$k[2];
		$exp = explode(",",$m);
		$liketext = "";
		foreach($exp as $key){
			$expp = explode("|",$key);
			if(filter_var($expp[1], FILTER_VALIDATE_URL)){
		$array[$y][$x]=["text"=>$expp[0],"url"=>$expp[1]];
		}elseif(strpos($expp[1],"LIKE")!==false){
			$cod = str_replace("LIKE","",$expp[1]);
			if(empty(getvalue("likes","time",$cod,"time"))){
		$array[$y][$x]=["text"=>$expp[0]." (0)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
		
		 insert("liketext","`code`,`text`,`key`","'$cod','$text2','$text'");
		
			
		}else{
			$query = "SELECT * FROM `likes$userbott` WHERE `time`='$cod' AND `name`='$expp[0]'";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$name = $row["name"];
$userid = $row["userid"];
}
$expl = explode(",",$userid);
$cot = count($expl)-1;

$array[$y][$x]=["text"=>"$name ($cot)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
			}
		}elseif(strpos($expp[1],"SHARE")!==false){
			$one = $fromid.$messageid;
			$two = $text2.$text;
			insert("eshtrak","`id`,`text`","'$one','$two'");
			$array[$y][$x]=["text"=>$expp[0],"switch_inline_query"=>"Share_$fromid$messageid"];
		}else{
			$array[$y][$x]=["text"=>$expp[0],"callback_data"=>$expp[1]];
			}
	
	//	$arr[]=$array[$x];
		$x++;
		}
					$y++;
		}
		
		$js = json_encode([
		'inline_keyboard'=>$array
		]);
	return $js;
}
function textToinline2($text,$text2 = null){
	global $messageid;
	global $fromid;
	global $userbott;
	global $con;
preg_match_all("/(%)(.*)(%)/",$text,$k);
$ck = $k[2];
$x=0;
$y=0;
foreach($ck as $m){
	$x=0;
		//	$v=$k[2];
		$exp = explode(",",$m);
		foreach($exp as $key){
			$expp = explode("|",$key);
			if(filter_var($expp[1], FILTER_VALIDATE_URL)){
		$array[$y][$x]=["text"=>$expp[0],"url"=>$expp[1]];
		}elseif(strpos($expp[1],"LIKE")!==false){
			$cod = str_replace("LIKE","",$expp[1]);
			if(empty(getvalue("likes","time",$cod,"time"))){
		$array[$y][$x]=["text"=>$expp[0]." (0)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
		
		 insert("liketext","`code`,`text`,`key`","'$cod','$text2','$text'");
		
			
		}else{
			$query = "SELECT * FROM `likes$userbott` WHERE `time`='$cod' AND `name`='$expp[0]'";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$name = $row["name"];
$userid = $row["userid"];
}
$expl = explode(",",$userid);
$cot = count($expl)-1;

$array[$y][$x]=["text"=>"$name ($cot)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
			}
		}elseif(strpos($expp[1],"SHARE")!==false){
			$one = $fromid.$messageid;
			$two = $text2.$text;
			insert("eshtrak","`id`,`text`","'$one','$two'");
			$array[$y][$x]=["text"=>$expp[0],"switch_inline_query"=>"Share_$fromid$messageid"];
		}else{
			$array[$y][$x]=["text"=>$expp[0],"callback_data"=>$expp[1]];
			}
	
	//	$arr[]=$array[$x];
		$x++;
		}
					$y++;
		}
		$js = [
		'inline_keyboard'=>$array
		];
	return $js;
}

function textToinline3($text,$text2 = null){
	global $messageid;
	global $fromid;
	
	global $userbott;
	global $con;
preg_match_all("/(%)(.*)(%)/",$text,$k);
$ck = $k[2];
$x=0;
$y=0;
foreach($ck as $m){
	$x=0;
		//	$v=$k[2];
		$exp = explode(",",$m);
		foreach($exp as $key){
			$expp = explode("|",$key);
			if(filter_var($expp[1], FILTER_VALIDATE_URL)){
		$array[$y][$x]=["text"=>$expp[0],"url"=>$expp[1]];
		}elseif(strpos($expp[1],"LIKE")!==false){
		$cod = str_replace("LIKE","",$expp[1]);
			$query = "SELECT * FROM `likes$userbott` WHERE `time`='$cod' && `name`='$expp[0]'";
			$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
	if(empty($row['time'])){
		$time2 = null;
		}else{
			$time2 = $row['time'];
		}
	if(empty($row['name'])){
		$name = null;
		}else{
			$name = $row['name'];
		}
if(empty($row['userid'])){
		$userid = null;
		}else{
			$userid = $row['userid'];
		}
if(empty($time2) && empty($name)){
	$array[$y][$x]=["text"=>$expp[0]." (0)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
		}else{
$expl = explode(",",$userid);
$cot = count($expl)-1;

$array[$y][$x]=["text"=>"$name ($cot)","callback_data"=>$expp[1]."+-+".$expp[0]."--+--"];
			}
		}elseif(strpos($expp[1],"SHARE")!==false){
			$one = $fromid.$messageid;
			$two = $text2.$text;
			if(empty(getvalue("eshtrak","id",$one,"id"))){
			$cgh = insert("eshtrak","`id`,`text`","'$one','$two'");
			
			}
			$array[$y][$x]=["text"=>$expp[0],"switch_inline_query"=>"Share_$fromid$messageid"];
		}else{
			$array[$y][$x]=["text"=>$expp[0],"callback_data"=>$expp[1]];
			}
	
	//	$arr[]=$array[$x];
		$x++;
		}
					$y++;
		}
		$js = json_encode([
		'inline_keyboard'=>$array
		]);
	return $js;
}

 function ping($host, $timeout = 1) {
   
}


function amarcount($type){
	global $con;
	global $userbott;
$result = mysqli_query($con, "SELECT * FROM $type$userbott");
$num_rows = mysqli_num_rows($result);
return $num_rows;
}
function getstep($chatid){
	global $con;
	global $userbott;
$query = "SELECT * FROM user$userbott WHERE chatid='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row['step'];
}
}
function getvalue($table,$roow,$chatid,$row2){
	global $con;
	global $userbott;
$query = "SELECT * FROM `$table$userbott` WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row[$row2];
}
}
function getvaluee($table,$roow,$chatid,$row2){
	global $con;
$query = "SELECT * FROM `$table` WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row[$row2];
}
}

function getallvalue($table,$row2){
	global $con;
	global $userbott;
$query = "SELECT * FROM $table$userbott ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$data[] = $row[$row2];
}
return $data;
}

function getallvaluee($table,$row2){
	global $con;
	global $userbott;
$query = "SELECT * FROM $table ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$data[] = $row[$row2];
}
return $data;
}

function setvalue($table,$row2,$val2,$row1,$val1){
	global $con;
	global $userbott;
$query = "UPDATE $table$userbott SET `$row1` = '$val1' WHERE `$row2` = '$val2'";
$result = mysqli_query($con,$query);
}
function setvaluee($table,$row2,$val2,$row1,$val1){
	global $con;
	global $userbott;
$query = "UPDATE $table SET `$row1` = '$val1' WHERE `$row2` = '$val2'";
$result = mysqli_query($con,$query);
}
function step($chatid,$step){
	global $con;
	global $userbott;
$query = "UPDATE user$userbott SET step = '$step' WHERE chatid = $chatid";
$result = mysqli_query($con,$query);
}
function deletevalue($table,$row,$val){
	global $con;
	global $userbott;
	$sql = "DELETE FROM `$table$userbott` WHERE `$row`='$val'";
	$result = mysqli_query($con,$sql);
	}
function deletevaluee($table,$row,$val){
	global $con;
	global $userbott;
	$sql = "DELETE FROM `$table` WHERE `$row`='$val'";
	$result = mysqli_query($con,$sql);
	}
function isetrow($table,$row){
	global $con;
	global $userbott;
	$sql = "SELECT EXISTS(SELECT * FROM `$table$userbott` WHERE `$row`)";
return	$result = mysqli_query($con,$sql);
	}
	
	function isetroww($table,$row){
	global $con;
	global $userbott;
	$sql = "SELECT EXISTS(SELECT * FROM `$table` WHERE `$row`)";
return	$result = mysqli_query($con,$sql);
	}
	
function createrow($table,$after,$row,$type){
global $con;
global $userbott;
$sql ="ALTER TABLE `$table$userbott` ADD `$row` $type after `$after`";
$result = mysqli_query($con,$sql);
}
function createroww($table,$after,$row,$type){
global $con;
$sql ="ALTER TABLE `$table` ADD `$row` $type after `$after`";
$result = mysqli_query($con,$sql);
}
if(!file_exists("Robat")){
	mkdir("Robat");
	}
function isetval($table,$roow,$chatid,$row2){
	global $con;
	global $userbott;
$query = "SELECT * FROM $table$userbott WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
if(isset($row[$row2]) && !empty($row[$row2])){
	return true;
	}else{
	ToDie();
	}
}
}
function getCoin($chatid){
$coin = getvalue("user","chatid",$chatid,"emtiaz");
return $coin;
}
function setCoin($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"emtiaz",$text);
return $coin;
}
function getZirmaj($chatid){
$coin = getvalue("user","chatid",$chatid,"zirmaj");
return $coin;
}
function setZirmaj($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"zirmaj",$text);
return $coin;
}
function getOther($chatid){
$coin = getvalue("user","chatid",$chatid,"Other");
return $coin;
}
function setOther($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other",$text);
return $coin;
}
function getOther2($chatid){
$coin = getvalue("user","chatid",$chatid,"Other2");
return $coin;
}
function setOther2($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other2",$text);
return $coin;
}
function getOther3($chatid){
$coin = getvalue("user","chatid",$chatid,"Other3");
return $coin;
}
function setOther3($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other3",$text);
return $coin;
}
function getCode($chatid){
$coin = getvalue("user","chatid",$chatid,"code");
return $coin;
}
function setCode($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"code",$text);
return $coin;
}
function getStartMessage(){
$coin = getvalue("data","id",1,"startmessage");
return $coin;
}
function setStartMessage($text){
$coin = setvalue("data","id",1,"startmessage",$text);
return $coin;
}
function getTxtback(){
$coin = getvalue("data","id",1,"txtback");
return $coin;
}
function setTxtback($text){
$coin = setvalue("data","id",1,"txtback",$text);
return $coin;
}
function getTxtnewozv(){
$coin = getvalue("data","id",1,"textnewozv");
return $coin;
}
function setTxtnewozv($text){
$coin = setvalue("data","id",1,"textnewozv",$text);
return $coin;
}
function getTxteshtebah(){
$coin = getvalue("data","id",1,"eshtebah");
return $coin;
}
function setTxteshtebah($text){
$coin = setvalue("data","id",1,"eshtebah",$text);
return $coin;
}
function getTxtstartpanel(){
$coin = getvalue("data","id",1,"startpanel");
return $coin;
}
function setTxtstartpanel($text){
$coin = setvalue("data","id",1,"startpanel",$text);
return $coin;
}
function getTxtpower(){
$coin = getvalue("data","id",1,"textpower");
return $coin;
}
function setTxtpower($text){
$coin = setvalue("data","id",1,"textpower",$text);
return $coin;
}
function getTxtzirmaj(){
$coin = getvalue("data","id",1,"textzirmaj");
return $coin;
}
function setTxtzirmaj($text){
$coin = setvalue("data","id",1,"textzirmaj",$text);
return $coin;
}
function getKeyboard(){
$coin = getvalue("data","id",1,"keyboard");
return $coin;
}
function setBotpower($text){
$coin = setvalue("data","id",1,"botpower",$text);
return $coin;
}
function getBotpower(){
$coin = getvalue("data","id",1,"botpower");
return $coin;
}

function setKeyboard($text){
$coin = setvalue("data","id",1,"keyboard",$text);
return $coin;
}
function getadmin($chatid){
	$coin = getvalue("admin","chatid",$chatid,"chatid");
return $coin;
	}
	function setadmin($chatid){
	global $con;
	global $userbott;
$query = "UPDATE `admin$userbott` SET chatid = '$chatid'";
$result = mysqli_query($con,$query);
	}
	function setKeygroup($text){
$coin = setvalue("data","id",1,"keygroup",$text);
return $coin;
}
function getKeygroup(){
$coin = getvalue("data","id",1,"keygroup");
return $coin;
}
function setNewozv($text){
$coin = setvalue("data","id",1,"newozv",$text);
return $coin;
}
function getNewozv(){
$coin = getvalue("data","id",1,"newozv");
return $coin;
}
function setDeletelink($text){
$coin = setvalue("data","id",1,"deletelink",$text);
return $coin;
}
function getDeletelink(){
$coin = getvalue("data","id",1,"deletelink");
return $coin;
}
function setLockjoin($text){
$coin = setvalue("data","id",1,"lockjoin",$text);
return $coin;
}
function getLockjoin(){
$coin = getvalue("data","id",1,"lockjoin");
return $coin;
}
function getJoindatesh($chatid){
$coin = getvalue("user","chatid",$chatid,"joindatesh");
return $coin;
}
function getJoindatem($chatid){
$coin = getvalue("user","chatid",$chatid,"joindatem");
return $coin;
}
function getJointime($chatid){
$coin = getvalue("user","chatid",$chatid,"jointime");
return $coin;
}
function getDice($chatid){
$coin = getvalue("user","chatid",$chatid,"dice");
return $coin;
}
function setDice($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"dice",$text);
return $coin;
}
function setInto($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"into",$text);
return $coin;
}
function getInto($dokme){
$coin = getvalue("dok","dokme",$dokme,"into");
return $coin;
}
function setDokmeType($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"type",$text);
return $coin;
}
function getDokmeType($dokme){
$coin = getvalue("dok","dokme",$dokme,"type");
return $coin;
}
function setEditer($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"editer",$text);
return $coin;
}
function getEditer($dokme){
$coin = getvalue("dok","dokme",$dokme,"editer");
return $coin;
}

function setDastor($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"dastor",$text);
return $coin;
}
function getDastor($dokme){
$coin = getvalue("dok","dokme",$dokme,"dastor");
return $coin;
}
function getDastorText($dokme){
$coin = getvalue("dok","dastor",$dokme,"dokme");
return $coin;
}
function getDokme($dokme){
$coin = getvalue("dok","dokme",$dokme,"dokme");
return $coin;
}
function setDokme($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"dokme",$text);
return $coin;
}
function getDokmenok($dokme){
$coin = getvalue("dok","dokme",$dokme,"nok");
return $coin;
}
function setDokmenok($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"nok",$text);
return $coin;
}
function setDokmetext($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"text",$text);
return $coin;
}
function getDokmetext($dokme){
$coin = getvalue("dok","dokme",$dokme,"text");
return $coin;
}
function setLockchannel($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"lockchannel",$text);
return $coin;
}
function getLockchannel($dokme){
$coin = getvalue("dok","dokme",$dokme,"lockchannel");
return $coin;
}
function setLockzirmaj($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"lockzirmaj",$text);
return $coin;
}
function getLockzirmaj($dokme){
$coin = getvalue("dok","dokme",$dokme,"lockzirmaj");
return $coin;
}
function setLockcoin($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"lockcoin",$text);
return $coin;
}
function getLockcoin($dokme){
$coin = getvalue("dok","dokme",$dokme,"lockcoin");
return $coin;
}
function setTextlock($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"textlock",$text);
return $coin;
}
function getTextlock($dokme){
$coin = getvalue("dok","dokme",$dokme,"textlock");
return $coin;
}
function setTextersal($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"textersal",$text);
return $coin;
}
function getTextersal($dokme){
$coin = getvalue("dok","dokme",$dokme,"textersal");
return $coin;
}
function setTextresid($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"textresid",$text);
return $coin;
}
function getTextresid($dokme){
$coin = getvalue("dok","dokme",$dokme,"textresid");
return $coin;
}
function setDokother($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"other",$text);
return $coin;
}
function getDokother($dokme){
$coin = getvalue("dok","dokme",$dokme,"other");
return $coin;
}
function setDokuser($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"user",$text);
return $coin;
}
function getDokuser($dokme){
$coin = getvalue("dok","dokme",$dokme,"user");
return $coin;
}
function setDoktedad($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"tedad",$text);
return $coin;
}
function getDoktedad($dokme){
$coin = getvalue("dok","dokme",$dokme,"tedad");
return $coin;
}
function setDokother2($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"other2",$text);
return $coin;
}
function getDokother2($dokme){
$coin = getvalue("dok","dokme",$dokme,"other2");
return $coin;
}
function setChannel($dokme,$text){
$coin = setvalue("chan","user",$dokme,"text",$text);
return $coin;
}
function getChannel($dokme){
$coin =getvalue("chan","user",$dokme,"user");
return $coin;
}
function getChanneltext($dokme){
$coin = getvalue("chan","user",$dokme,"text");
return $coin;
}
function deleteChannel($dokme){
$coin = deletevalue("chan","user",$dokme);
return $coin;
}
function setBot($dokme,$text){
$coin = setvalue("robot","user",$dokme,"text",$text);
return $coin;
}
function getBot($dokme){
$coin =getvalue("robot","user",$dokme,"user");
return $coin;
}
function getBottext($dokme){
$coin = getvalue("robot","user",$dokme,"text");
return $coin;
}
function deleteBot($dokme){
$coin = deletevalue("robot","user",$dokme);
return $coin;
}
function deleteAdmin($dokme){
$coin = deletevalue("admin","chatid",$dokme);
return $coin;
}
function deleteBlock($dokme){
$coin = deletevalue("blocklist","chatid",$dokme);
return $coin;
}
function getBlock($dokme){
$coin = getvalue("blocklist","chatid",$dokme,"chatid");
return $coin;
}
function setBlock($dokme,$text){
$coin = setvalue("blocklist","chatid",$dokme,"chatid",$text);
return $coin;
}
function getSendzirmaj(){
$coin = getvalue("data","id",1,"sendzirmaj");
return $coin;
}
function setSendzirmaj($text){
$coin = setvalue("data","id",1,"sendzirmaj",$text);
return $coin;
}
function getDokkeyboard($dokme){
$coin = getvalue("dok","dokme",$dokme,"keyboard");
return $coin;
}
function setDokkeyboard($dokme,$text){
$coin = setvalue("dok","dokme",$dokme,"keyboard",$text);
return $coin;
}
function insert($tble, $cols, $values){
global $con;
global $userbott;
$insertquery = "INSERT INTO `" . $tble .$userbott. "` (" . $cols . ") VALUES (" . $values . ")";
$insertresult = mysqli_query($con, $insertquery) or die(mysqli_error($con));
return $insertresult;
}
function insertt($tble, $cols, $values){
global $con;
global $userbott;
$insertquery = "INSERT INTO `" . $tble. "` (" . $cols . ") VALUES (" . $values . ")";
$insertresult = mysqli_query($con, $insertquery) or die(mysqli_error($dbcon));
return $insertresult;
}
function getAllchannel(){
$res = getallvalue("chan","user");
return $res;
}
function getAllBot(){
$res = getallvalue("robot","user");
return $res;
}
function Amardokme($text){
	return getvalue("dok","dokme",$text,"amardok");
}
function photo_file($chatid){
	return getvalue("fileid","chatid",$chatid,"photo");
}
function video_file($chatid){
	return getvalue("fileid","chatid",$chatid,"video");
}
function video_note_file($chatid){
	return getvalue("fileid","chatid",$chatid,"videonote");
}
function document_file($chatid){
	return getvalue("fileid","chatid",$chatid,"document");
}
function sticker_file($chatid){
	return getvalue("fileid","chatid",$chatid,"sticker");
}
function audio_file($chatid){
	return getvalue("fileid","chatid",$chatid,"audio");
}
function voice_file($chatid){
	return getvalue("fileid","chatid",$chatid,"voice");
}
function str_var($tt){
	$array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","TIMESTAMP","MAXWARN","WARN","PHONE","BIO","PING","IDBOT","BOTUSER","BOTNAME","GPNAME","PROFILE_PHOTO","BOTLIST","GPUSER","CHATID","DESCRIOPTION","FATYPEBALL","ENTYPEBALL","FATYPEGETBALL","ENTYPEGETBALL","FATYPEBASKET","ENTYPEBASKET","FATYPEGETBASKET","ENTYPEGETBASKET","FATYPEBOWLING","ENTYPEBOWLING","FATYPEGETBOWLING","ENTYPEGETBOWLING","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT","ADD","TRANS");
    return str_replace($array1,"",$tt);
	}
function str_text2($teext,$int = 1,$user=null){
	global $text;
	global $chatid;
	global $fromid;
	global $messageid;
	global $firstname;
	global $lastname;
	global $username;
	global $gpname;
	global $gpuser;
	global $bio;
	global $description;
	global $newmember;
    global $newid;
    global $newname;
    global $newuser;
    global $photo;
    global $video;
    global $video_note;
    global $sticker;
    global $document;
    global $audio;
    global $voice;
	global $contact_name;
    global $contact_id;
    global $contact_number;
    global $long_location;
    global $lat_location;
    global $dice;
    global $val;
    global $caption;
    global $idbot;
    global $botname;
    global $botuser;
    global $datesh;
    global $datem;
    global $phone_number;
    global $queryid;
    global $querydata;
    global $qmessage;
    global $qname;
    global $quser;
    global $qid;
    global $querychatid;
    global $time;
    global $fatypebasket;
    global $entypebasket;
    global $fatypeball;
    global $entypeball;
    global $fatypebowling;
    global $entypebowling;
    $tml = getallvaluee("qw$chatid","bot");
    $botlist="";
    foreach($tml as $key){
    	$botlist .="@$key\n";
    }
    $ping = ping("$domain_url");
    if(getvalue("user","chatid",$chatid,"emdice")=="🏀"){
    	if(getOther($chatid)==4 or getOther($chatid)==5){
    	$fatypegetbasket = "برد";
    $entypegetbasket = "Win";
    }else{
    	$fatypegetbasket = "باخت";
    $entypegetbasket = "Lost";
    }
    }elseif(getvalue("user","chatid",$chatid,"emdice")=="⚽"){
    	if(getOther($chatid)==4 or getOther($chatid)==5 or getOther($chatid)==3){
    	$fatypegetball = "برد";
    $entypegetball = "Win";
    }else{
    	$fatypegetball = "باخت";
    $entypegetball = "Lost";
    }
    }elseif(getvalue("user","chatid",$chatid,"emdice")=="🎳"){
    	if(getOther($chatid)==6){
    	$fatypegetbowling = "برد";
    $entypegetbowling = "Win";
    }else{
    	$fatypegetbowling = "باخت";
    $entypegetbowling = "Lost";
    }
    }
    $man = bot('getUserProfilePhotos',[
'user_id'=>$chatid,
'limit'=>1
]);
    @$prof = $man->result->photos[0][0]->file_id;
  // sm($chatid,json_encode($prof));
  if(preg_match_all('/STRLOWER\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRLOWER\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text($val,1);
  $xc = strtolower($xc);
  $teext = str_replace("STRLOWER($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRUPPER\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRUPPER\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text($val,1);
  $xc = strtoupper($xc);
  $teext = str_replace("STRUPPER($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRLEN\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRLEN\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text($val,1);
  $xc = strlen($xc);
  $teext = str_replace("STRLEN($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRREV\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRREV\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text($val,1);
  $xc = strrev($xc);
  $teext = str_replace("STRREV($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/PASS\(([^\']+)\)/U',$teext)){
  	preg_match_all('/PASS\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xx = str_text($val,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^[0-9]$/',$xx[1])){
  	$xx[1]=8;
  }
  $xc = random2($xx[0],$xx[1]);
  $teext = str_replace("PASS($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/MD5\(([^\']+)\)/U',$teext)){
  	preg_match_all('/MD5\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $val = str_text($val,1);
  $md = md5($val);
  $teext = str_replace("MD5($val)",$md,$teext);
  }
  }
  if(preg_match_all('/HASH\(([^\']+)\)/U',$teext)){
  	preg_match_all('/HASH\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $val = str_text($val,1);
  $md = hash('sha256', $val);
  $teext = str_replace("HASH($val)",$md,$teext);
  }
  }
  if(preg_match_all('/SENDCHAT\(([^\']+)\)/',$teext)){
  	preg_match_all('/SENDCHAT\(([^\']+)\)/',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$shh){
  $xx = str_text($shh,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^\-?[0-9]+$/',$xx[0])){
  	$xx[0]=$chatid;
  }
  $txt = $xx[1];
if(preg_match("/(%)([^\']+)(%)/s",$txt,$mc)){
$k = $mc[2];
$hi=	preg_split("/(%)([^\']+)(%)/s",$txt);
	$txt = $hi[0];
		$tii = textToinline("%$k%",$txt);
		
			}
  sm($xx[0],$txt,$tii);
  $teext = str_replace("SENDCHAT($shh)","",$teext);
  }
  }
  if(preg_match_all('/TRANS\(([^\']+)\)/U',$teext)){
  	preg_match_all('/TRANS\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$shh){
  $xx = str_text($shh,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^\-?[0-9]+$/',$xx[0])){
  	$xx[0]=$chatid;
  }
  $txt = $xx[1];
if(strpos($txt,"-")){
					$coin = getCoin($xx[0]);
						$co = $coin - $txt;
						setCoin($xx[0],$co);
						}else{
							$coin = getCoin($xx[0]);
							$co = $coin + $txt;
						setCoin($xx[0],$co);
							}
  $teext = str_replace("TRANS($shh)","$txt",$teext);
  }
  }
    if(preg_match_all('/MESSAGE\(([^\']+)\)/U',$teext)){
		preg_match_all('/MESSAGE\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$rando = str_text2($random,1);
			if(preg_match("/(%)([^\']+)(%)/s",$rando,$mc)){
		$k = $mc[2];
	$hi=	preg_split("/(%)([^\']+)(%)/s",$rando);
	$rando = $hi[0];
		$tii = textToinline("%$k%",$rando);
		
			}
			sm($chatid,$rando,$tii);
	$teext=str_replace("MESSAGE($val)","",$teext);
}
		}
		if(preg_match_all('/GETDB\_([A-Z0-9]+)/',$teext)){
  	preg_match_all('/GETDB\_([A-Z0-9]+)/',$teext,$ma);
 $m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			if(!empty(getvalue("datatype","name",$val,"name"))){
				$type= getvalue("datatype","name",$val,"type");
				if($type=="kol"){
					if(!empty(getvalue("datalist","name",$val,"name"))){
						$value = getvalue("datalist","name",$val,"value");
						$teext=str_replace("GETDB_$val",$value,$teext);
						}else{
							insert("datalist","`name`,`value`","'$val','0'");
							$teext=str_replace("GETDB_$val",0,$teext);
							}
					}else{
						if(!empty(getvalue("datalist","name",$chatid.$val,"name"))){
						$value = getvalue("datalist","name",$chatid.$val,"value");
						$teext=str_replace("GETDB_$val",$value,$teext);
						}else{
							insert("datalist","`name`,`value`","'$chatid.$val','0'");
							$teext=str_replace("GETDB_$val",0,$teext);
							}
						}
				}else{
					$teext=str_replace("GETDB_$val","",$teext);
					}
			}
  }
  if(preg_match_all('/SETDB\_([A-Z0-9]+)\(([^\']+)\)/U',$teext)){
	  preg_match_all('/SETDB\_([A-Z0-9]+)\(([^\']+)\)/U',$teext,$ma,PREG_SET_ORDER);
		
		
		foreach($ma as $key=>$val){
			$random=$val;
			$db = $val[1];
			$str=str_text($val[2],1);
			if(!empty(getvalue("datatype","name",$db,"name"))){
				$type= getvalue("datatype","name",$db,"type");
				if($type=="kol"){
					if(!empty(getvalue("datalist","name",$db,"name"))){
						setvalue("datalist","name",$db,"value",$str);
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						}else{
							insert("datalist","`name`,`value`","'$db','$str'");
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							}
					}elseif($type=="kar"){
						if(!empty(getvalue("datalist","name",$chatid.$db,"name"))){
						setvalue("datalist","name",$chatid.$db,"value",$str);
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						
						}else{
							insert("datalist","`name`,`value`","'$chatid$db','$str'");
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							
							}
						}elseif($type=="score"){
						if(!empty(getvalue("datalist","name",$chatid.$db,"name"))){
							if(preg_match("/^\-?[0-9]+$/",$str)){
								if(strpos($str,"-")){
								$coin = getvalue("datalist","name",$chatid.$db,"value");
						$co = $coin - $str;
						setvalue("datalist","name",$chatid.$db,"value",$co);
						}else{
							$coin = getvalue("datalist","name",$chatid.$db,"value");
						$co = $coin + $str;
						setvalue("datalist","name",$chatid.$db,"value",$co);
						}
						}
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						
						}else{
							if(preg_match("/^\-?[0-9]+$/",$str)){
							insert("datalist","`name`,`value`","'$chatid$db','$str'");
							}
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							
							}
						}
				}else{
					$teext=str_replace("SETDB_$db($val[2])","",$teext);
					}
			
		}
		}
if(preg_match('/RANDOM\(([^\']+)\)/U',$teext)){
		preg_match('/RANDOM\(([^\']+)\)/U',$teext,$ma);
		$random=$ma[1];
		$exp=explode(",",$random);
		$count=count($exp)-1;
		$rand=rand(0,$count);
		$array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","PHONE","BIO","PING","IDBOT","BOTUSER","BOTNAME","GPNAME","PROFILE_PHOTO","BOTLIST","GPUSER","CHATID","DESCRIOPTION","FATYPEBALL","ENTYPEBALL","FATYPEGETBALL","ENTYPEGETBALL","FATYPEBASKET","ENTYPEBASKET","FATYPEGETBASKET","ENTYPEGETBASKET","FATYPEBOWLING","ENTYPEBOWLING","FATYPEGETBOWLING","ENTYPEGETBOWLING","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT");
    if($int==1){
	$array2 = array($firstname,$lastname,$username,$fromid,$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$chatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($chatid),getDice($chatid),$messageid,getCoin($chatid),getZirmaj($chatid),"https://t.me/$botuser?start=$fromid",getDokother2($text),date("H"),date("i"),date("s"),getJoindatem($fromid),getJoindatesh($fromid),getJointime($fromid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($chatid),video_file($chatid),video_note_file($chatid),sticker_file($chatid),document_file($chatid),audio_file($chatid),voice_file($chatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$text);
	}else{
		$array2 = array($qname,$lastname,$quser,$qid,$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$querychatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($qid),getDice($qid),$messageid,getCoin($qid),getZirmaj($qid),"https://t.me/$botuser?start=$qid",getDokother2($querydata),date("H"),date("i"),date("s"),getJoindatem($qid),getJoindatesh($qid),getJointime($qid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($querychatid),video_file($querychatid),video_note_file($querychatid),sticker_file($querychatid),document_file($querychatid),audio_file($querychatid),voice_file($querychatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$querydata);
		}
$ch13 = str_replace($array1,$array2,$exp[$rand]);
if($int==1){
	if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($fromid);
						$co = $coin - $ma;
						setCoin($chatid,$co);
						}else{
							$coin = getCoin($fromid);
							$co = $coin + $ma;
						setCoin($chatid,$co);
							}
					}
					}else{
						if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($qid);
						$co = $coin - $ma;
						setCoin($qid,$co);
						}else{
							$coin = getCoin($qid);
							$co = $coin + $ma;
						setCoin($qid,$co);
							}
					}
						}
					$ch13=str_replace("ADD","",$ch13);
					
		$teext=preg_replace('/RANDOM\(([^\']+)\)/',$ch13,$teext);
		
		}
		
    $array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","PHONE","BIO","PING","IDBOT","BOTUSER","BOTNAME","GPNAME","PROFILE_PHOTO","BOTLIST","GPUSER","CHATID","DESCRIOPTION","FATYPEBALL","ENTYPEBALL","FATYPEGETBALL","ENTYPEGETBALL","FATYPEBASKET","ENTYPEBASKET","FATYPEGETBASKET","ENTYPEGETBASKET","FATYPEBOWLING","ENTYPEBOWLING","FATYPEGETBOWLING","ENTYPEGETBOWLING","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT");
    if($int==1){
	$array2 = array($firstname,$lastname,$username,$fromid,$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$chatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($chatid),getDice($chatid),$messageid,getCoin($chatid),getZirmaj($chatid),"https://t.me/$botuser?start=$fromid",getDokother2($text),date("H"),date("i"),date("s"),getJoindatem($fromid),getJoindatesh($fromid),getJointime($fromid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($chatid),video_file($chatid),video_note_file($chatid),sticker_file($chatid),document_file($chatid),audio_file($chatid),voice_file($chatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$text);
	}else{
		$array2 = array($qname,$lastname,$quser,$qid,$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$querychatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($qid),getDice($qid),$messageid,getCoin($qid),getZirmaj($qid),"https://t.me/$botuser?start=$qid",getDokother2($querydata),date("H"),date("i"),date("s"),getJoindatem($qid),getJoindatesh($qid),getJointime($qid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($querychatid),video_file($querychatid),video_note_file($querychatid),sticker_file($querychatid),document_file($querychatid),audio_file($querychatid),voice_file($querychatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$querydata);
		}
	$ch13 = str_replace($array1,$array2,$teext);
	if($int==1){
	if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($fromid);
						$co = $coin - $ma;
						setCoin($chatid,$co);
						}else{
							$coin = getCoin($fromid);
							$co = $coin + $ma;
						setCoin($chatid,$co);
							}
					}
					}else{
						if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($qid);
						$co = $coin - $ma;
						setCoin($qid,$co);
						}else{
							$coin = getCoin($qid);
							$co = $coin + $ma;
						setCoin($qid,$co);
							}
					}
						}
					$ch13=str_replace("ADD","",$ch13);
					$teext=$ch13;
					if(preg_match_all('/GETDATA\(([^\']+)\)/U',$teext)){
		preg_match_all('/GETDATA\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		$url = "";
		foreach($m as $key=>$val){
		$val2 = str_text2($val,1);
		if(strpos($val2,",[")==false){
			$xc = file_get_contents($val2);
		}else{
			preg_match('/(.*)\,\[(.*)\]/',$val2,$xx);
			$xx2 = $xx[1];
			$exp= explode(",",$xx[2]);
			if($url != $xx2){
			$url = $xx2;
//Initiate cURL.
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
}
	$xx3 =json_decode($result);
			$gget =$xx3;
			foreach($exp as $key){
				$gget = $gget->$key;
				if($gget ===null){
					$gget = "Error To Parse Json!!!";
					}
				}
				$xc = $gget;
			}
	$teext=str_replace("GETDATA($val)",$xc,$teext);
	}
		}
					if(preg_match_all('/SENDPHOTO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDPHOTO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!=null){
			$chatid=$user;
			}
			sp($chatid,$file,$cap);
	$teext=preg_replace("/SENDPHOTO\($random\)/","",$teext);
}
		}
		    if(preg_match_all('/SENDVIDEO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVIDEO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!=null){
			$chatid=$user;
			}
			sv($chatid,$file,$cap);
	$teext=preg_replace("/SENDVIDEO\($random\)/","",$teext);
}
		}
		    if(preg_match_all('/SENDAUDIO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDAUDIO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sa($chatid,$file,$cap);
	$teext=preg_replace("/SENDAUDIO\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDVOICE\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVOICE\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			svo($chatid,$file,$cap);
	$teext=preg_replace("/SENDVOICE\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDSTICKER\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDSTICKER\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			if($user!==null){
			$chatid=$user;
			}
			ss($chatid,$val);
	$teext=preg_replace("/SENDSTICKER\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDDOCUMENT\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDDOCUMENT\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sd($chatid,$file,$cap);
	$teext=preg_replace("/SENDDOCUMENT\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDVINOTE\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVINOTE\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			svin($chatid,$file,$cap);
	$teext=preg_replace("/SENDVINOTE\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDEMOJI\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDEMOJI\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			if($user!==null){
			$chatid=$user;
			}
			sdi($chatid,$val);
	$teext=preg_replace("/SENDEMOJI\($random\)/","",$teext);
}
		}
		
		if(preg_match_all('/SENDCONTACT\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDCONTACT\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sco($chatid,$file,$cap);
	$teext=preg_replace("/SENDCONTACT\($file\,$cap\)/","",$teext);
}
		}

if(preg_match_all('/DELETE([0-9]+)/',$teext)){
		preg_match_all('/DELETE([0-9]+)/',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
	$now = time();
	$id = getvalue("user","chatid",$chatid,"Other6");
	if($id > $messageid){
     $id = $id+1;	
     }else{
     $id = $messageid + 1;	
     }
     insert("delete","chm,id,time","'$chatid-$id','POSTDEL$val','$now'");
	$teext=preg_replace("/DELETE$val/","",$teext);
}
		}
					return $teext;
	}
function str_text($teext,$int = 1,$user = null){
	global $text;
	global $chatid;
	global $fromid;
	global $messageid;
	global $firstname;
	global $lastname;
	global $username;
	global $gpname;
	global $gpuser;
	global $bio;
	global $description;
	global $newmember;
    global $newid;
    global $newname;
    global $newuser;
    global $photo;
    global $video;
    global $video_note;
    global $sticker;
    global $document;
    global $audio;
    global $voice;
	global $contact_name;
    global $contact_id;
    global $contact_number;
    global $long_location;
    global $lat_location;
    global $dice;
    global $val;
    global $caption;
    global $idbot;
    global $botname;
    global $botuser;
    global $datesh;
    global $datem;
    global $phone_number;
    global $queryid;
    global $querydata;
    global $qmessage;
    global $qname;
    global $quser;
    global $qid;
    global $querychatid;
    global $time;
    global $fatypebasket;
    global $entypebasket;
    global $fatypeball;
    global $entypeball;
    global $fatypebowling;
    global $entypebowling;
    $tml = getallvaluee("qw$chatid","bot");
    $botlist="";
    foreach($tml as $key){
    	$botlist .="@$key\n";
    }
    $ping = ping("$domain_url");
    if(getvalue("user","chatid",$chatid,"emdice")=="🏀"){
    	if(getOther($chatid)==4 or getOther($chatid)==5 ){
    	$fatypegetbasket = "برد";
    $entypegetbasket = "Win";
    }else{
    	$fatypegetbasket = "باخت";
    $entypegetbasket = "Lost";
    }
    }elseif(getvalue("user","chatid",$chatid,"emdice")=="⚽"){
    	if(getOther($chatid)==4 or getOther($chatid)==5 or getOther($chatid)==3){
    	$fatypegetball = "برد";
    $entypegetball = "Win";
    }else{
    	$fatypegetball = "باخت";
    $entypegetball = "Lost";
    }
    }elseif(getvalue("user","chatid",$chatid,"emdice")=="🎳"){
    	if(getOther($chatid)==6){
    	$fatypegetbowling = "برد";
    $entypegetbowling = "Win";
    }else{
    	$fatypegetbowling = "باخت";
    $entypegetbowling = "Lost";
    }
    }
    $man = bot('getUserProfilePhotos',[
'user_id'=>$chatid,
'limit'=>1
]);
    @$prof = $man->result->photos[0][0]->file_id;
  // sm($chatid,json_encode($prof));
  if(preg_match_all('/STRLOWER\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRLOWER\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text2($val,1);
  $xc = strtolower($xc);
  $teext = str_replace("STRLOWER($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRUPPER\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRUPPER\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text2($val,1);
  $xc = strtoupper($xc);
  $teext = str_replace("STRUPPER($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRLEN\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRLEN\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text2($val,1);
  $xc = strlen($xc);
  $teext = str_replace("STRLEN($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/STRREV\(([^\']+)\)/U',$teext)){
  	preg_match_all('/STRREV\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xc = str_text2($val,1);
  $xc = strrev($xc);
  $teext = str_replace("STRREV($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/REPLACE\(([^\']+)\)/U',$teext)){
  	preg_match_all('/REPLACE\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$mbn){
  $xc = str_text2($mbn,1);
  $exp= explode(",",$xc);
  $xc = str_replace($exp[0],$exp[1],$exp[2]);
  $teext = str_replace("REPLACE($mbn)",$xc,$teext);
  }
  }
  
  if(preg_match_all('/PASS\(([^\']+)\)/U',$teext)){
  	preg_match_all('/PASS\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $xx = str_text2($val,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^[0-9]$/',$xx[1])){
  	$xx[1]=8;
  }
  $xc = random2($xx[0],$xx[1]);
  $teext = str_replace("PASS($val)",$xc,$teext);
  }
  }
  if(preg_match_all('/MD5\(([^\']+)\)/U',$teext)){
  	preg_match_all('/MD5\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $val = str_text2($val,1);
  $md = md5($val);
  $teext = str_replace("MD5($val)",$md,$teext);
  }
  }
  if(preg_match_all('/HASH\(([^\']+)\)/U',$teext)){
  	preg_match_all('/HASH\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$val){
  $val = str_text2($val,1);
  $md = hash('sha256', $val);
  $teext = str_replace("HASH($val)",$md,$teext);
  }
  }
  if(preg_match_all('/SENDCHAT\(([^\']+)\)/U',$teext)){
  	preg_match_all('/SENDCHAT\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$chh){
  $xx = str_text2($chh,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^\-?[0-9]+$/',$xx[0])){
  	$xx[0]=$chatid;
  }
  $txt = $xx[1];
if(preg_match("/(%)([^\']+)(%)/s",$txt,$mc)){
$k = $mc[2];
$hi=	preg_split("/(%)([^\']+)(%)/s",$txt);
	$txt = $hi[0];
		$tii = textToinline("%$k%",$txt);
		
			}
  sm($xx[0],$txt,$tii);
  $teext = str_replace("SENDCHAT($chh)","",$teext);
  }
  }
  if(preg_match_all('/TRANS\(([^\']+)\)/U',$teext)){
  	preg_match_all('/TRANS\(([^\']+)\)/U',$teext,$ma);
  $m = $ma[1];
  foreach($m as $key=>$shh){
  $xx = str_text2($shh,1);
  $xx = explode(",",$xx);
  if(!preg_match('/^\-?[0-9]+$/',$xx[0])){
  	$xx[0]=$chatid;
  }
  $txt = $xx[1];
if(strpos($txt,"-")){
					$coin = getCoin($xx[0]);
						$co = $coin - $txt;
						setCoin($xx[0],$co);
						}else{
							$coin = getCoin($xx[0]);
							$co = $coin + $txt;
						setCoin($xx[0],$co);
							}
  $teext = str_replace("TRANS($shh)","$txt",$teext);
  }
  }
  if(preg_match_all("/IF\(([^\']+)(\=|\<|\>|\>\=|\<\=|\!\=)([^\']+)\,([^\']+)\,([^\']+)\)/U",$teext)){
  	preg_match_all("/IF\(([^\']+)(\=|\<|\>|\>\=|\<\=|\!\=)([^\']+)\,([^\']+)\,([^\']+)\)/U",$teext,$ma,PREG_SET_ORDER);
		foreach($ma as $key=>$xgg){
			$one = str_text2($xgg[1],1);
			$two = $xgg[2];
			$three = str_text2($xgg[3],1);
			if($two == "=" && $one == $three){
				$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}elseif($two == ">" && $one > $three){
					$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}elseif($two == "<" && $one < $three){
					$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}elseif($two == "<=" && $one <= $three){
					$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}elseif($two == ">=" && $one >= $three){
					$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}elseif($two == "!=" && $one != $three){
					$four = str_text2($xgg[4],1);
			$teext = str_replace($xgg[0],$four,$teext);
				}else{
					$five = str_text2($xgg[5],1);
					$teext = str_replace($xgg[0],$five,$teext);
					}
					
			}
			}
  if(preg_match_all('/GETDB\_([A-Z0-9]+)/',$teext)){
  	preg_match_all('/GETDB\_([A-Z0-9]+)/',$teext,$ma);
 $m = $ma[1];
		foreach($m as $val){
			$random=$val;
			if(!empty(getvalue("datatype","name",$val,"name"))){
				$type= getvalue("datatype","name",$val,"type");
				if($type=="kol"){
					if(!empty(getvalue("datalist","name",$val,"name"))){
						$value = getvalue("datalist","name",$val,"value");
						$teext=str_replace("GETDB_$val",$value,$teext);
						}else{
							insert("datalist","`name`,`value`","'$val','0'");
							$teext=str_replace("GETDB_$val",0,$teext);
							}
					}else{
						if(!empty(getvalue("datalist","name",$chatid.$val,"name"))){
						$value = getvalue("datalist","name",$chatid.$val,"value");
						$teext=str_replace("GETDB_$val",$value,$teext);
						}else{
							insert("datalist","`name`,`value`","'$chatid$val','0'");
							$teext=str_replace("GETDB_$val",0,$teext);
							}
						}
				}else{
					$teext=str_replace("GETDB_$val","",$teext);
					}
			}
  }
  if(preg_match_all('/SETDB\_([A-Z0-9]+)\(([^\']+)\)/U',$teext)){
	  preg_match_all('/SETDB\_([A-Z0-9]+)\(([^\']+)\)/U',$teext,$ma,PREG_SET_ORDER);
		
		
		foreach($ma as $key=>$val){
			$random=$val;
			$db = $val[1];
			$str=str_text2($val[2],1);
			if(!empty(getvalue("datatype","name",$db,"name"))){
				$type= getvalue("datatype","name",$db,"type");
				if($type=="kol"){
					if(!empty(getvalue("datalist","name",$db,"name"))){
						setvalue("datalist","name",$db,"value",$str);
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						}else{
							insert("datalist","`name`,`value`","'$db','$str'");
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							}
					}elseif($type=="kar"){
						if(!empty(getvalue("datalist","name",$chatid.$db,"name"))){
						setvalue("datalist","name",$chatid.$db,"value",$str);
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						
						}else{
							insert("datalist","`name`,`value`","'$chatid$db','$str'");
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							
							}
						}elseif($type=="score"){
						if(!empty(getvalue("datalist","name",$chatid.$db,"name"))){
							if(preg_match("/^\-?[0-9]+$/",$str)){
								if(strpos($str,"-")){
								$coin = getvalue("datalist","name",$chatid.$db,"value");
						$co = $coin - $str;
						setvalue("datalist","name",$chatid.$db,"value",$co);
						}else{
							$coin = getvalue("datalist","name",$chatid.$db,"value");
						$co = $coin + $str;
						setvalue("datalist","name",$chatid.$db,"value",$co);
						}
						}
						$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
						
						}else{
							if(preg_match("/^\-?[0-9]+$/",$str)){
							insert("datalist","`name`,`value`","'$chatid$db','$str'");
							}
							$teext=str_replace("SETDB_$db($val[2])",$str,$teext);
							
							}
						}
				}else{
					$teext=str_replace("SETDB_$db($val[2])","",$teext);
					}
			
		}
		}
    if(preg_match_all("/MESSAGE\(([^\']+)\)/U",$teext)){
		preg_match_all("/MESSAGE\(([^\']+)\)/U",$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$rando = str_text2($random,1);
			if(preg_match("/(%)([^\']+)(%)/s",$rando,$mc)){
		$k = $mc[2];
	$hi=	preg_split("/(%)([^\']+)(%)/s",$rando);
	$rando = $hi[0];
		$tii = textToinline("%$k%",$rando);
		
			}
			sm($chatid,$rando,$tii);
	$teext=str_replace("MESSAGE($val)","",$teext);
}
		}
if(preg_match('/RANDOM\(([^\']+)\)/U',$teext)){
		preg_match('/RANDOM\(([^\']+)\)/U',$teext,$ma);
		$random=$ma[1];
		$exp=explode(",",$random);
		$count=count($exp)-1;
		$rand=rand(0,$count);
		$array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","TIMESTAMP","MAXWARN","WARN","PHONE","BIO","PING","IDBOT","BOTUSER","BOTNAME","GPNAME","PROFILE_PHOTO","BOTLIST","GPUSER","CHATID","DESCRIOPTION","FATYPEBALL","ENTYPEBALL","FATYPEGETBALL","ENTYPEGETBALL","FATYPEBASKET","ENTYPEBASKET","FATYPEGETBASKET","ENTYPEGETBASKET","FATYPEBOWLING","ENTYPEBOWLING","FATYPEGETBOWLING","ENTYPEGETBOWLING","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT");
	if($int==1){
	$array2 = array($firstname,$lastname,$username,$fromid,time(),getvalue("data","id",1,"maxwarn"),getvalue("user","chatid",$chatid,"tedadwarn"),$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$chatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($chatid),getDice($chatid),$messageid,getCoin($chatid),getZirmaj($chatid),"https://t.me/$botuser?start=$fromid",getDokother2($text),date("H"),date("i"),date("s"),getJoindatem($fromid),getJoindatesh($fromid),getJointime($fromid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($chatid),video_file($chatid),video_note_file($chatid),sticker_file($chatid),document_file($chatid),audio_file($chatid),voice_file($chatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$text);
	}else{
		$array2 = array($qname,$lastname,$quser,$qid,time(),getvalue("data","id",1,"maxwarn"),getvalue("user","chatid",$qid,"tedadwarn"),$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$querychatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($qid),getDice($qid),$messageid,getCoin($qid),getZirmaj($qid),"https://t.me/$botuser?start=$qid",getDokother2($querydata),date("H"),date("i"),date("s"),getJoindatem($qid),getJoindatesh($qid),getJointime($qid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($querychatid),video_file($querychatid),video_note_file($querychatid),sticker_file($querychatid),document_file($querychatid),audio_file($querychatid),voice_file($querychatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$querydata);
		}
$ch13 = str_replace($array1,$array2,$exp[$rand]);
	if($int==1){
	if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text2($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($fromid);
						$co = $coin - $ma;
						setCoin($chatid,$co);
						}else{
							$coin = getCoin($fromid);
							$co = $coin + $ma;
						setCoin($chatid,$co);
							}
					}
					}else{
						if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text2($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($qid);
						$co = $coin - $ma;
						setCoin($qid,$co);
						}else{
							$coin = getCoin($qid);
							$co = $coin + $ma;
						setCoin($qid,$co);
							}
					}
						}
					$ch13=str_replace("ADD","",$ch13);
					
		$teext=preg_replace('/RANDOM\(([^\']+)\)/',$ch13,$teext);
		
		}
		
    $array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","TIMESTAMP","MAXWARN","WARN","PHONE","BIO","PING","IDBOT","BOTUSER","BOTNAME","GPNAME","PROFILE_PHOTO","BOTLIST","GPUSER","CHATID","DESCRIOPTION","FATYPEBALL","ENTYPEBALL","FATYPEGETBALL","ENTYPEGETBALL","FATYPEBASKET","ENTYPEBASKET","FATYPEGETBASKET","ENTYPEGETBASKET","FATYPEBOWLING","ENTYPEBOWLING","FATYPEGETBOWLING","ENTYPEGETBOWLING","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT");
    if($int==1){
	$array2 = array($firstname,$lastname,$username,$fromid,time(),getvalue("data","id",1,"maxwarn"),getvalue("user","chatid",$chatid,"tedadwarn"),$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$chatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($chatid),getDice($chatid),$messageid,getCoin($chatid),getZirmaj($chatid),"https://t.me/$botuser?start=$fromid",getDokother2($text),date("H"),date("i"),date("s"),getJoindatem($fromid),getJoindatesh($fromid),getJointime($fromid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($chatid),video_file($chatid),video_note_file($chatid),sticker_file($chatid),document_file($chatid),audio_file($chatid),voice_file($chatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$text);
	}else{
		$array2 = array($qname,$lastname,$quser,$qid,time(),getvalue("data","id",1,"maxwarn"),getvalue("user","chatid",$qid,"tedadwarn"),$phone_number,$bio,$ping,$idbot,$botuser,$botname,$gpname,$prof,$botlist,$gpuser,$querychatid,$description,$fatypeball,$entypeball,$fatypegetball,$entypegetball,$fatypebasket,$entypebasket,$fatypegetbasket,$entypegetbasket,$fatypebowling,$entypebowling,$fatypegetbowling,$entypegetbowling,getOther($qid),getDice($qid),$messageid,getCoin($qid),getZirmaj($qid),"https://t.me/$botuser?start=$qid",getDokother2($querydata),date("H"),date("i"),date("s"),getJoindatem($qid),getJoindatesh($qid),getJointime($qid),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($querychatid),video_file($querychatid),video_note_file($querychatid),sticker_file($querychatid),document_file($querychatid),audio_file($querychatid),voice_file($querychatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$querydata);
		}
	$ch13 = str_replace($array1,$array2,$teext);
	if($int==1){
	if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text2($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($fromid);
						$co = $coin - $ma;
						setCoin($chatid,$co);
						}else{
							$coin = getCoin($fromid);
							$co = $coin + $ma;
						setCoin($chatid,$co);
							}
					}
					}else{
						if(preg_match('/ADD(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13)){
					preg_match('/(ADD)(\-?[0-9A-Za-z\_\(\)\s\:\?\&\@\/\.\=\-\*\%\+]+)/',$ch13,$mag);
					$ma=$mag[2];
					$ma = str_text2($ma,1);
					if(strpos($ma,"-")){
					$coin = getCoin($qid);
						$co = $coin - $ma;
						setCoin($qid,$co);
						}else{
							$coin = getCoin($qid);
							$co = $coin + $ma;
						setCoin($qid,$co);
							}
					}
						}
					$ch13=str_replace("ADD","",$ch13);
					$teext=$ch13;
					if(preg_match_all('/GETDATA\(([^\']+)\)/U',$teext)){
		preg_match_all('/GETDATA\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		$url = "";
		foreach($m as $key=>$val){
		$val2 = str_text2($val,1);
		if(strpos($val2,",[")==false){
			$xc = file_get_contents($val2);
		}else{
			preg_match('/(.*)\,\[(.*)\]/',$val2,$xx);
			$xx2 = $xx[1];
			$exp= explode(",",$xx[2]);
			if($url !== $xx2){
			$url = $xx2;
//Initiate cURL.
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
}
	$xx3 =json_decode($result);
			$gget =$xx3;
			foreach($exp as $key){
				
				if(preg_match("/(.*)\+([0-9]+)/",$key)){
					preg_match("/(.*)\+([0-9]+)/",$key,$cc);
					//sm(2097019227,json_encode($cc));
					$one = $cc[1];
					$two = $cc[2];
					$gget = $gget->$one[$two];
					}else{
				$gget = $gget->$key;
				}
				if($gget ===null){
					$gget = "Error To Parse Json!!!";
					}
				}
				$xc = $gget;
			}
	$teext=str_replace("GETDATA($val)",$xc,$teext);
	}
		}
					if(preg_match_all('/SENDPHOTO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDPHOTO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sp($chatid,$file,$cap);
	$teext=preg_replace("/SENDPHOTO\($random\)/","",$teext);
}
		}
		    if(preg_match_all('/SENDVIDEO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVIDEO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sv($chatid,$file,$cap);
	$teext=preg_replace("/SENDVIDEO\($random\)/","",$teext);
}
		}
		    if(preg_match_all('/SENDAUDIO\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDAUDIO\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sa($chatid,$file,$cap);
	$teext=preg_replace("/SENDAUDIO\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDVOICE\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVOICE\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			svo($chatid,$file,$cap);
	$teext=preg_replace("/SENDVOICE\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDSTICKER\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDSTICKER\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			if($user!==null){
			$chatid=$user;
			}
			ss($chatid,$val);
	$teext=preg_replace("/SENDSTICKER\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDDOCUMENT\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDDOCUMENT\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sd($chatid,$file,$cap);
	$teext=preg_replace("/SENDDOCUMENT\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDVINOTE\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDVINOTE\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			svin($chatid,$file,$cap);
	$teext=preg_replace("/SENDVINOTE\($random\)/","",$teext);
}
		}
		if(preg_match_all('/SENDEMOJI\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDEMOJI\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			if($user!==null){
			$chatid=$user;
			}
			sdi($chatid,$val);
	$teext=preg_replace("/SENDEMOJI\($random\)/","",$teext);
}
		}
		
		if(preg_match_all('/SENDCONTACT\(([^\']+)\)/U',$teext)){
		preg_match_all('/SENDCONTACT\(([^\']+)\)/U',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
			$random=$val;
			$exp=explode(",",$random);
		$file = $exp[0];
		$cap = $exp[1];
		if($user!==null){
			$chatid=$user;
			}
			sco($chatid,$file,$cap);
	$teext=preg_replace("/SENDCONTACT\($file\,$cap\)/","",$teext);
}
		}
if(preg_match_all('/DELETE([0-9]+)/',$teext)){
		preg_match_all('/DELETE([0-9]+)/',$teext,$ma);
		$m = $ma[1];
		foreach($m as $key=>$val){
	$now = time();
     $id = getvalue("user","chatid",$chatid,"Other6");
     if($id > $messageid){
     $id = $id+1;	
     }else{
     $id = $messageid + 1;	
     }
     insert("delete","chm,id,time","'$chatid-$id','POSTDEL$val','$now'");
	$teext=preg_replace("/DELETE$val/","",$teext);
}
		}
					return $teext;
	}
	if(!file_exists("dokme")){
	mkdir("dokme");
	}
	/*
if(!file_exists('dokme.json')){
//	file_put_contents("Admin.txt","\n");
$fh = fopen("dokme.json", 'wp') or die("Could not create the file.");
$keytest='[{"text":""}],[{"text":""}]';
$data=(["keyboard"=>$keytest]);
    fwrite($fh,json_encode($data)) or die("Could not write to the file.");
    fclose($fh);
}
*/
if(empty(getKeyboard())){
	$keytest='[{"text":""}],[{"text":""}]';
	setKeyboard($keytest);
	}
$admin = "ADMINBOT";
$update = json_decode(file_get_contents('php://input'));
//sm($userid_malek_botsaz,json_encode($update));
$Message = $update->message;
$messageid = $Message->message_id;
$text = $Message->text;
$chatid = $Message->chat->id;
if($chatid==$userid_malek_botsaz){
$admin=$userid_malek_botsaz;
	}
	if($chatid==$userid_malek_botsaz){
$admin=$userid_malek_botsaz;
	}
$fromid = $Message->from->id;
$firstname = str_var($Message->from->first_name);
$lastname =str_var($Message->from->last_name);
$username = str_var($Message->from->username);
if(isset($Message->forward_from)){
$forward = $Message->forward_from;
	}elseif(isset($Message->forward_from_chat)){
$forward = $Message->forward_from_chat;
	}elseif(isset($Message->forward_sender_name)){
$forward = $Message->forward_sender_name;
	}
	if(isset($update->channel_post)){
$chusername = $update->channel_post->chat->username;
$chid = $update->channel_post->chat->id;
$type2= $update->channel_post->chat->type;
$chmessage= $update->channel_post->message_id;
}
$type= $Message->chat->type;
$globaltype= $Message->chat->type;
if(isset($update->callback_query)){
$query = $update->callback_query;
$messageid = $query->message->message_id;
if(empty($query->message->message_id)){
$inline_messageid = $query->inline_message_id;
	}
$chatid = $query->message->chat->id;
$type= $query->message->chat->type;
$globaltype= $query->message->chat->type;
$fromid = $query->from->id;
$firstname =str_var($query->from->first_name);
$lastname =str_var($query->from->last_name);
$username =str_var($query->from->usernam);
$queryid=$query->id;
$querydata=$query->data;
$qmessage = $query->message;
$qname = str_var($query->from->first_name);
$quser = str_var($query->from->username);
$qid = $query->from->id;
$querychatid=$query->message->chat->id;
if(empty($querychatid)){
$querychatid= $query->from->id;
	}
}
if(isset($Message->chat->invite_link)){
$invite_link = $Message->chat->invite_link;
}
if(isset($Message->chat->title)){
$gpname = str_var($Message->chat->title);
}
if(isset($Message->chat->username)){
$gpuser = str_var($Message->chat->username);
}
if(isset($Message->chat->bio)){
$bio = str_var($Message->chat->bio);    
}
if(isset($Message->chat->description)){
$description = str_var($Message->chat->description);
}
if(isset($Message->new_chat_members)){
$newmember = $Message->new_chat_members;
$newid = $newmember[0]->id;
$newname = str_var($newmember[0]->first_name);
$newuser = str_var($newmember[0]->username);
}
if(isset($Message->photo[0]->file_id)){
$photo= $Message->photo[0]->file_id;

	setvalue("fileid","chatid",$chatid,"photo",$photo);
	}
	if(isset($Message->video->file_id)){
$video = $Message->video->file_id;
	setvalue("fileid","chatid",$chatid,"video",$video);
	}
	if(isset($Message->sticker->file_id)){
$sticker = $Message->sticker->file_id;
	setvalue("fileid","chatid",$chatid,"sticker",$sticker);
	}
	if(isset($Message->audio->file_id)){
$audio = $Message->audio->file_id;
	setvalue("fileid","chatid",$chatid,"audio",$audio);
	}
	if(isset($Message->document->file_id)){
$document= $Message->document->file_id;
	setvalue("fileid","chatid",$chatid,"document",$document);
	}
	if(isset($Message->voice->file_id)){
$voice = $Message->voice->file_id;
	setvalue("fileid","chatid",$chatid,"voice",$voice);
	}
	if(isset($Message->video_note->file_id)){
@$video_note = $Message->video_note->file_id;
	setvalue("fileid","chatid",$chatid,"videonote",$video_note);
	}
	if(isset($Message->contact)){
@$contact_name= str_var($Message->contact->first_name);
@$contact_id= $Message->contact->user_id;
@$contact_number=$Message->contact->phone_number;
}
if(isset($Message->location)){
@$long_location = $Message->location->longitude;
@$lat_location = $Message->location->latitude;
}
@$dice = $Message->dice->emoji;
@$val= $Message->dice->value;
if(isset($dice)){
	setDice($chatid,$val);
	if($dice=="⚽"){
		if($val==5 or $val==4 or $val ==3){
			@$fatypeball = "برد";
			@$entypeball = "Win";
			}else{
				@$fatypeball = "باخت";
			@$entypeball = "Lost";
				}
		}
		elseif($dice=="🏀"){
		if($val==5 or $val==4){
			@$fatypebasket = "برد";
			@$entypebasket = "Win";
			}else{
				@$fatypebasket = "باخت";
			@$entypebasket = "Lost";
				}
		}elseif($dice=="🎳"){
		if($val==6){
			@$fatypebowling = "برد";
			@$entypebowling = "Win";
			}else{
				@$fatypebowling = "باخت";
			@$entypebowling = "Lost";
				}
		}
	}
@$caption = $Message->caption;
@$getmebot = bot("getMe");
@$idbot = $getmebot->result->id;
@$botname = $getmebot->result->first_name;
@$botuser = $getmebot->result->username;
@$datesh = gregorian_to_jalali(date("Y"),date("m"),date("d"),"/");
@$phone_number=getvalue("user","chatid",$chatid,"phone");
$time = date("H:i:s");
$datem = date("Y/m/d");
@$step=getstep($chatid);
if(isset($query)){
@$step=getstep($qid);
}
if(empty(getvaluee("user","chatid",$chatid,"keyboard"))){
											$keytest='[{"text":""}],[{"text":""}]';
											setvaluee("user","chatid",$chatid,"keyboard",$keytest);
												}
if(empty(getadmin($chatid)) && $chatid != $admin && isset($text)){
	$text = str_var($text);
	$querydata = str_var($querydata);
	}
	if(empty(getadmin($querychatid)) && $querychatid != $admin && isset($querydata) ){
	$text = str_var($text);
	$querydata = str_var($querydata);
	}
if(isset($text)){
if(strpos($text,"/start BUTTON")!==false){
	$text=str_replace("/start BUTTON","",$text);
	@$text=getvalue("hash","hash",$text,"text");
	}
	
	if(strpos($text,"|LIKE%")!==false){
		$time=time();
	$text=str_replace("|LIKE%","|LIKE$time%",$text);
	}
	if(strpos($text,"|LIKE,")!==false){
		$time=time();
	$text=str_replace("|LIKE,","|LIKE$time,",$text);
	}
	}
	if(isset($caption)){
	if(strpos($caption,"|LIKE%")!==false){
		$time=time();
	$caption=str_replace("|LIKE%","|LIKE$time%",$caption);
	}
	if(strpos($caption,"|LIKE,")!==false){
		$time=time();
	$caption=str_replace("|LIKE,","|LIKE$time,",$caption);
	}
	}
	if(!isetrow("delete","chm")){
	$sql = "CREATE TABLE `delete$userbott`
(
`chm` TEXT,
`id` TEXT,
`time` TEXT
)";
mysqli_query($con,$sql);
}
if(!isetrow("likes","like")){
	$sql = "CREATE TABLE `likes$userbott`
(
`userid` TEXT,
`message_id` TEXT,
`inline_message_id` TEXT,
`text` TEXT,
`name` TEXT,
`dokme` TEXT,
`matn` TEXT,
`like` TEXT,
`time` TEXT,
`like2` TEXT
)";
mysqli_query($con,$sql);
}
if(!isetrow("once","id")){
$sql = "CREATE TABLE `once$userbott` 
 ( 
 `id` BIGINT
)";

mysqli_query($con,$sql);
}


if(!isetrow("liketext","code")){
	$sql = "CREATE TABLE `liketext$userbott`
(
`code` TEXT,
`text` TEXT,
`key` TEXT
)";
mysqli_query($con,$sql);
}
if(!isetroww("copycode","code")){
	$sql = "CREATE TABLE `copycode`
(
`code` TEXT,
`userbot` TEXT
)";
mysqli_query($con,$sql);
}
	if(!isetrow("eshtrak","text")){
		$sql = "CREATE TABLE `eshtrak$userbott` 
 ( 
`id` BIGINT,
PRIMARY KEY(id),
`text` TEXT
)";
mysqli_query($con,$sql);

		}
		
	if(!isetrow("fileid","photo")){
		$sql = "CREATE TABLE `fileid$userbott` 
 ( 
 `chatid` BIGINT,
 PRIMARY KEY(chatid),
 `photo` TEXT,
 `audio` TEXT,
 `voice` TEXT,
 `document` TEXT,
 `video` TEXT,
 `videonote` TEXT,
 `sticker` TEXT
)";
mysqli_query($con,$sql);
		}
		
		if(empty(getvalue("fileid","chatid",$chatid,"chatid"))){
	$sql = "INSERT INTO `fileid$userbott`
(
`chatid`
) VALUES('$chatid')";
mysqli_query($con,$sql);
}
//------------For Create Bot----------//
/*
if($type=="private"){
									$sql = "CREATE TABLE `qw$chatid`
									(
									`bot` TEXT,
									`token` TEXT
									)";
									
									mysqli_query($con, $sql);
									
								
								
$insert_query = "INSERT INTO `user`
(
chatid,
userid,
firstname,
lastname,
username,
joindatesh,
joindatem,
jointime,
zirmaj,
emtiaz,
`Other3`
)
VALUES ('$chatid','$fromid','$firstname','$lastname','$username','$datesh','$datem','$time','0','20','1')";
mysqli_query($con, $insert_query);

	}
	if(!isetroww("user","Other3")){
	createroww("user","emtiaz","Other3","INT");
	}
	if(!isetroww("user","Other4")){
	createroww("user","Other3","Other4","TEXT");
	}


*/

//-------------------Finish For Create Bot
//--------++Create Table++-----------//
if(!isetrow("user","zirmajuserid"))
{

	$sql = "CREATE TABLE `hash$userbott` 
 ( 
 `hash` TEXT,
 `text` TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `datatype$userbott` 
 ( 
 `name` TEXT,
 `type` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `datalist$userbott` 
 ( 
 `name` TEXT,
 `value` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `mahdod$userbott` 
 ( 
 `chatid` BIGINT,
 PRIMARY KEY(chatid)
)";

mysqli_query($con,$sql);


$sql = "CREATE TABLE `replac$userbott` 
 ( 
 `replac` TEXT,
 `totext` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `hashmoh$userbott` 
 ( 
 `hash` TEXT,
 `text` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `filter$userbott` 
 ( 
 `filter` TEXT,
 `dokme` TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `del$userbott` 
 ( 
 `del` TEXT,
 `dokme` TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `code$userbott` 
 ( 
 `code` TEXT,
 `dokme` TEXT,
 `tedad` INT,
 `nafar` INT,
 `date` TEXT,
 `vaz` TEXT,
 `text` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `moh$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `channel$userbott` 
 ( 
 chatid TEXT,
chname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `eshtrak$userbott` 
 ( 
`id` BIGINT,
PRIMARY KEY(id),
`text` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `robot$userbott` 
 ( 
`user` TEXT,
`text` TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `pasokh$userbott` 
 ( 
 `pasokh` TEXT,
 `javab` TEXT
)";
mysqli_query($con,$sql);


if(!isetrow("data","zirmajcoin")){
	createrow("data","sendzirmaj","zirmajcoin","INT");
	}
	if(!isetrow("data","txtblock")){
	createrow("data","txtback","txtblock","TEXT");
	}
	if(!isetrow("data","nameback")){
	createrow("data","zirmajcoin","nameback","TEXT");
	}
	if(!isetrow("dok","lockday")){
	createrow("dok","lockcoin","lockday","TEXT");
	}
	if(!isetrow("dok","locksade")){
	createrow("dok","lockcoin","locksade","TEXT");
	}
	if(!isetrow("dok","lockemtiaz2")){
	createrow("dok","lockcoin","lockemtiaz2","TEXT");
	}
	if(!isetrow("dok","keyback")){
	createrow("dok","lockday","keyback","TEXT");
	}
	if(!isetrow("dok","textemtiaz1")){
	createrow("dok","keyback","textemtiaz1","TEXT");
	}
	if(!isetrow("dok","textemtiaz2")){
	createrow("dok","textemtiaz1","textemtiaz2","TEXT");
	}
	if(!isetrow("dok","textemtiaz3")){
	createrow("dok","textemtiaz2","textemtiaz3","TEXT");
	}
	if(!isetrow("dok","textemtiaz4")){
	createrow("dok","textemtiaz3","textemtiaz4","TEXT");
	}
	if(!isetrow("dok","channel")){
	createrow("dok","textemtiaz4","channel","TEXT");
	}
	if(!isetrow("dok","textchannel")){
	createrow("dok","textemtiaz4","textchannel","TEXT");
	}
	if(!isetrow("dok","hidden")){
	createrow("dok","textchannel","hidden","TEXT");
	}
	if(!isetrow("dok","lockcode")){
	createrow("dok","hidden","lockcode","TEXT");
	}
	if(!isetrow("dok","lockshomare")){
	createrow("dok","lockcode","lockshomare","TEXT");
	}
	if(!isetrow("dok","lockshomare2")){
	createrow("dok","lockshomare","lockshomare2","TEXT");
	}
	if(!isetrow("dok","textemtiaz5")){
	createrow("dok","textemtiaz4","textemtiaz5","TEXT");
	}
	if(!isetrow("dok","textemtiaz6")){
	createrow("dok","textemtiaz5","textemtiaz6","TEXT");
	}
	if(!isetrow("dok","mohtava")){
	createrow("dok","lockcode","mohtava","TEXT");
	}
	if(!isetrow("dok","emza")){
	createrow("dok","mohtava","emza","TEXT");
	}
	if(!isetrow("dok","emzatext")){
	createrow("dok","emza","emzatext","TEXT");
	}
	if(!isetrow("dok","field")){
	createrow("dok","emzatext","field","TEXT");
	}
	if(!isetrow("user","coding")){
	createrow("user","code","coding","INT");
	}
	if(!isetrow("user","ref")){
	createrow("user","code","ref","TEXT");
	}
	if(!isetrow("user","dayl")){
	createrow("user","coding","dayl","TEXT");
	}
	if(!isetrow("user","phone")){
	createrow("user","dayl","phone","TEXT");
	}
	if(!isetrow("data","forwardstart")){
	createrow("data","nameback","forwardstart","TEXT");
	}
	if(!isetrow("user","Other")){
	createrow("user","dayl","Other","TEXT");
	}
	if(!isetrow("user","Other4")){
	createrow("user","dayl","Other4","TEXT");
	}
	if(!isetrow("user","captha")){
	createrow("user","dayl","captha","TEXT");
	}
	if(!isetrow("user","Other5")){
	createrow("user","Other4","Other5","TEXT");
	}
	if(!isetrow("user","Other6")){
	createrow("user","Other5","Other6","TEXT");
	}
	if(!isetrow("user","dice")){
	createrow("user","Other6","dice","TEXT");
	}
	if(!isetrow("user","emdice")){
	createrow("user","Other6","emdice","TEXT");
	}
	if(!isetrow("user","tedadwarn")){
	createrow("user","emdice","tedadwarn","TEXT");
	}
	if(!isetrow("user","zirmajuserid")){
	createrow("user","tedadwarn","zirmajuserid","TEXT");
	}
	if(!isetrow("dok","textlock2")){
	createrow("dok","lockcode","textlock2","TEXT");
	}
	if(!isetrow("dok","textlock3")){
	createrow("dok","textlock2","textlock3","TEXT");
	}
	if(!isetrow("dok","locknafar")){
	createrow("dok","textlock2","locknafar","TEXT");
	}
	if(!isetrow("dok","numnafar")){
	createrow("dok","locknafar","numnafar","TEXT");
	}
	if(!isetrow("dok","finishnafar")){
	createrow("dok","locknafar","finishnafar","TEXT");
	}
	if(!isetrow("dok","autodel")){
	createrow("dok","finishnafar","autodel","TEXT");
	}
	if(!isetrow("dok","timeautodel")){
	createrow("dok","finishnafar","timeautodel","TEXT");
	}
	if(!isetrow("dok","qoflforward")){
	createrow("dok","timeautodel","qoflforward","TEXT");
	}
	if(!isetrow("dok","qoflforwardtext")){
	createrow("dok","qoflforward","qoflforwardtext","TEXT");
	}
	if(!isetrow("dok","themebartarin")){
	createrow("dok","qoflforwardtext","themebartarin","TEXT");
	}
	if(!isetrow("dok","textresid2")){
	createrow("dok","themebartarin","textresid2","TEXT");
	}
	if(!isetrow("dok","lowshart")){
	createrow("dok","textresid2","lowshart","TEXT");
	}
	if(!isetrow("dok","highshart")){
	createrow("dok","lowshart","highshart","TEXT");
	}
	
	if(!isetrow("data","forwardid")){
	createrow("data","forwardstart","forwardid","TEXT");
	}
	if(!isetrow("data","typing")){
	createrow("data","forwardstart","typing","TEXT");
	}
	if(!isetrow("data","webview")){
	createrow("data","typing","webview","TEXT");
	}
	if(!isetrow("data","replymessage")){
	createrow("data","webview","replymessage","TEXT");
	}
	if(!isetrow("data","lockphone")){
	createrow("data","replymessage","lockphone","TEXT");
	}
	if(!isetrow("data","phoneiran")){
	createrow("data","lockphone","phoneiran","TEXT");
	}
	if(!isetrow("data","textphiran")){
	createrow("data","phoneiran","textphiran","TEXT");
	}
	if(!isetrow("data","textphone1")){
	createrow("data","lockphone","textphone1","TEXT");
	}
	if(!isetrow("data","textphone2")){
	createrow("data","textphone1","textphone2","TEXT");
	}
	if(!isetrow("data","dokphone")){
	createrow("data","textphone1","dokphone","TEXT");
	}
	if(!isetrow("data","filter")){
	createrow("data","textphone2","filter","TEXT");
	}
	if(!isetrow("data","autodel")){
	createrow("data","filter","autodel","TEXT");
	}
	if(!isetrow("data","leavegroup")){
	createrow("data","forwardid","leavegroup","TEXT");
	}
	if(!isetrow("data","textleft")){
	createrow("data","leavegroup","textleft","TEXT");
	}
	if(!isetrow("data","autoanswer")){
	createrow("data","textleft","autoanswer","TEXT");
	}
	
	if(!isetrow("data","coinstart")){
	createrow("data","zirmajcoin","coinstart","TEXT");
	}
	if(!isetrow("data","tedadcoin")){
	createrow("data","coinstart","tedadcoin","INT");
	}
	if(!isetrow("data","pmnewcoin")){
	createrow("data","coinstart","pmnewcoin","TEXT");
	}
	if(!isetrow("data","newcoin")){
	createrow("data","coinstart","newcoin","TEXT");
	}
	if(!isetrow("data","resize")){
	createrow("data","tedadcoin","resize","TEXT");
	}
	if(!isetrow("data","captha")){
    createrow("data","resize","captha","TEXT");
}
if(!isetrow("data","textcaptha1")){
    createrow("data","captha","textcaptha1","TEXT");
}
if(!isetrow("data","textcaptha2")){
    createrow("data","textcaptha1","textcaptha2","TEXT");
}
if(!isetrow("data","textcaptha3")){
    createrow("data","textcaptha2","textcaptha3","TEXT");
}
if(!isetrow("data","capstyle")){
    createrow("data","textcaptha3","capstyle","TEXT");
}
if(!isetrow("data","lockrobot")){
    createrow("data","textcaptha3","lockrobot","TEXT");
}
	if(!isetrow("data","spam")){
	createrow("data","lockrobot","spam","TEXT");
	}
	if(!isetrow("data","spamsecond")){
	createrow("data","spam","spamsecond","INT");
	}
	if(!isetrow("data","spamtedad")){
	createrow("data","spamsecond","spamtedad","INT");
	}
	if(!isetrow("data","spamban")){
	createrow("data","spamtedad","spamban","INT");
	}
	if(!isetrow("data","spamtext")){
	createrow("data","spamban","spamtext","TEXT");
	}
	if(!isetrow("data","pmresanall")){
	createrow("data","spamtext","pmresanall","TEXT");
	}
	if(!isetrow("data","pmresantext")){
	createrow("data","pmresanall","pmresantext","TEXT");
	}
	if(!isetrow("data","replacetext")){
	createrow("data","pmresantext","replacetext","TEXT");
	}
	if(!isetrow("data","opencodetext")){
	createrow("data","replacetext","opencodetext","TEXT");
	}
	if(!isetrow("data","barmoh")){
	createrow("data","opencodetext","barmoh","TEXT");
	}
    if(!isetrow("data","textbarmoh")){
	createrow("data","barmoh","textbarmoh","TEXT");
	}
	if(!isetrow("data","sendlog")){
	createrow("data","barmoh","sendlog","TEXT");
	}
	if(!isetrow("data","logchannel")){
	createrow("data","sendlog","logchannel","TEXT");
	}
if(!isetrow("data","notoflike")){
	createrow("data","logchannel","notoflike","TEXT");
	}
if(!isetrow("data","notofliketext")){
	createrow("data","notoflike","notofliketext","TEXT");
	}
	if(!isetrow("data","notofdisliketext")){
	createrow("data","notofliketext","notofdisliketext","TEXT");
	}
	if(!isetrow("data","sendtomail")){
	createrow("data","notofliketext","sendtomail","TEXT");
	}
	if(!isetrow("data","maxwarn")){
	createrow("data","sendtomail","maxwarn","TEXT");
	}
	if(!isetrow("data","textwarn")){
	createrow("data","maxwarn","textwarn","TEXT");
	}
	if(!isetrow("data","matnforward")){
	createrow("data","textwarn","matnforward","TEXT");
	}
	if(!isetrow("data","aksfilm")){
	createrow("data","matnforward","aksfilm","TEXT");
	}
	if(!isetrow("data","voiceaudio")){
	createrow("data","aksfilm","voiceaudio","TEXT");
	}
	if(!isetrow("data","stickerdice")){
	createrow("data","voiceaudio","stickerdice","TEXT");
	}
	if(!isetrow("data","filevideonote")){
	createrow("data","stickerdice","filevideonote","TEXT");
	}
	if(!isetrow("data","mahdod")){
	createrow("data","filevideonote","mahdod","TEXT");
	}
	if(!isetrow("data","oncetype")){
	createrow("data","mahdod","oncetype","TEXT");
	}
	if(!isetrow("admin","adddokme")){
	createrow("admin","chatid","adddokme","TEXT");
	}
	if(!isetrow("admin","editdokme")){
	createrow("admin","adddokme","editdokme","TEXT");
	}
	if(!isetrow("admin","amarbot")){
	createrow("admin","editdokme","amarbot","TEXT");
	}
	if(!isetrow("admin","resetbot")){
	createrow("admin","amarbot","resetbot","TEXT");
	}
	if(!isetrow("admin","addadmin")){
	createrow("admin","resetbot","addadmin","TEXT");
	}
	if(!isetrow("admin","pasokh")){
	createrow("admin","addadmin","pasokh","TEXT");
	}
	if(!isetrow("admin","editmatn")){
	createrow("admin","pasokh","editmatn","TEXT");
	}
	if(!isetrow("admin","ersal")){
	createrow("admin","editmatn","ersal","TEXT");
	}
	if(!isetrow("admin","group")){
	createrow("admin","ersal","group","TEXT");
	}
	if(!isetrow("admin","sayersetting")){
	createrow("admin","group","sayersetting","TEXT");
	}
	if(!isetrow("moh","textget")){
	createrow("moh","link","textget","TEXT");
	}
	if(!isetrow("moh","textesh")){
	createrow("moh","textget","textesh","TEXT");
	}
	if(!isetrow("moh","audio")){
	createrow("moh","textesh","textget","TEXT");
	}
	if(!isetrow("moh","english")){
	createrow("moh","textesh","english","TEXT");
	}
	if(!isetrow("channel","other3")){
	createrow("channel","Other","other3","TEXT");
	}
	if(!isetrow("channel","other3")){
	createrow("channel","other3","other4","TEXT");
	}
	if(!isetrow("user","spam1")){
	createrow("user","dice","spam1","TEXT");
	}
	if(!isetrow("user","spam2")){
	createrow("user","spam1","spam2","TEXT");
	}
	if(!isetrow("user","stoptime")){
	createrow("user","spam2","stoptime","TEXT");
	}
	if(!isetrow("user","under")){
	createrow("user","stoptime","under","TEXT");
	}
	if(!isetrow("user","vindor")){
	createrow("user","under","vindor","TEXT");
	}
	if(!isetrow("user","phone2")){
	createrow("user","vindor","phone2","TEXT");
	}
	if(!isetrow("user","zarib")){
	createrow("user","phone2","zarib","TEXT");
	}
	}
	//--------بخــــــــــش دوم دیتابیــــــــــس---------\\
	$sql = "ALTER TABLE `admin$userbott` MODIFY `chatid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `user$userbott` MODIFY `chatid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `group$userbott` MODIFY `chatid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `user$userbott` MODIFY `userid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `user$userbott` MODIFY `emtiaz` TEXT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `group$userbott` MODIFY `Other` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `channel$userbott` MODIFY `Other` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `supergroup$userbott` MODIFY `chatid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `supergroup$userbott` MODIFY `Other` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `blocklist$userbott` MODIFY `chatid` BIGINT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `data$userbott` MODIFY `tedadcoin` TEXT";
	mysqli_query($con,$sql);
	$sql = "ALTER TABLE `data$userbott` MODIFY `zirmajcoin` TEXT";
	mysqli_query($con,$sql);
	if(empty(getvalue("data","id",2,"id"))){
	$sql = "INSERT INTO `data$userbott`
(
`id`
) VALUES('2')";
mysqli_query($con,$sql);
}
	if(!isetrow("dok","amardok")){
	createrow("dok","emzatext","amardok","TEXT");
	}
	$backname = getvalue("data","id",1,"nameback");
if($type=="private"){
	if(!isetrow("user","coding")){
		createrow("user","emtiaz","coding","INT");
		}
	if(empty(getvalue("user","chatid",$chatid,"chatid"))){
		if(empty(getvalue("dayamar","day",date("d"),"day"))){
			$sql ="INSERT INTO `dayamar$userbott`(`day`,`amar`) VALUES ('".date("d")."','1')";
		mysqli_query($con,$sql);
			}else{
				$amar = getvalue("dayamar","day",date("d"),"amar");
		$newamar=$amar+1;
		setvalue("dayamar","day",date("d"),"amar",$newamar);
				}
		}
		if(empty(getvalue("data","id",1,"tedadcoin"))){
			$em=0;
			}else{
				$em = getvalue("data","id",1,"tedadcoin");
				}
			$firstname = str_replace("<","",$firstname);
			$firstname = str_replace(">","",$firstname);
			$firstname = str_replace("'","",$firstname);
			$firstname = str_replace('"',"",$firstname);
$insert_query = "INSERT INTO `user$userbott`
(
chatid,
userid,
firstname,
lastname,
username,
joindatesh,
joindatem,
jointime,
zirmaj,
emtiaz,
`coding`
)
VALUES ('$chatid','$fromid','$firstname','$lastname','$username','$datesh','$datem','$time','0','$em','1')";
mysqli_query($con, $insert_query);
if(getvalue("data","id",1,"newcoin")=="on" && empty(getvalue("user","chatid",$chatid,"under"))){
	if(empty(getvalue("data","id",1,"pmnewcoin"))){
		sm($chatid,"کاربر جدید شما $em امتیاز برای اولین بار دریافت کردید✅");
		setvalue("user","chatid",$chatid,"under","1");
		}else{
			$tet = getvalue("data","id",1,"pmnewcoin");
			$ch13 = str_text($tet,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
	$kei="";
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline2("%$k%",$ch13);
			}
			sm($chatid,$ch13,$kei);
			setvalue("user","chatid",$chatid,"under","1");
			}
	}
	}
	//////////////
	
	if($type=="group"){
$insert_query = "INSERT INTO `group$userbott`
(
chatid,
gpname,
username,
joindatesh,
joindatem,
jointime
)
VALUES ('$chatid','$gpname','$gpuser','$datesh','$datem','$time')";
mysqli_query($con, $insert_query);

	}
	
	if($type=="supergroup"){
$insert_query = "INSERT INTO `supergroup$userbott`
(
chatid,
gpname,
username,
joindatesh,
joindatem,
jointime
)
VALUES ('$chatid','$gpname','$gpuser','$datesh','$datem','$time')";
mysqli_query($con, $insert_query);

	}
if($type2=="channel"){
	if(empty(getvalue("channel","chatid",$chid,"chatid"))){
		
$insert_query = "INSERT INTO `channel$userbott`
(
`chatid`,
`username`,
`joindatesh`,
`joindatem`,
`jointime`
)
VALUES ('$chid','$chusername','$datesh','$datem','$time')";
mysqli_query($con,$insert_query);
}
	}
//$keypanel ='{"keyboard":[[{"text":"افزودن دکمه🔼"}],[{"text":"افزودن دکمه◀"},{"text":"افزودن دکمه▶"}],[{"text":"افزودن دکمه🔽"}],[{"text":"ویرایش متن شروع✏"},{"text":"ویرایش متن اشتباه🚫"}],[{"text":"بلاک کردن⛔"},{"text":"انبلاک کردن✅"}],[{"text":"آمار ربات💯"}],[{"text":"فوروارد کاربران↩"},{"text":"ارسال کاربران📢"}]],"resize_keyboard":true}';

if(getvalue("data","id",1,"typing")=="on"){
	if(isset($Message)){
		bot('sendchatAction',[
		'chat_id'=>$chatid,
		'action'=>"typing"
		]);
		}
	}
	
	$resize_keyboard=getvalue("data","id",1,"resize");
	if($resize_keyboard=="off"){
		$sizekol="false";
		}else{
			$sizekol="true";
			}
			if(!empty(getadmin($chatid))){
				$keypanel ='{"keyboard":[[{"text":"افزودن دکمه جدید🔧"}],[{"text":"ویرایش دکمه ها✂"}],[{"text":"ارسال همگانی📣"},{"text":"ویرایش متن ها✏"}],[{"text":"پاسخ خودکار🔉"}],[{"text":"آمار ربات💯"},{"text":"بخش ادمین ها👤"}],[{"text":"بخش دیتابیس🗃"}],[{"text":"ریست کامل ربات♻"},{"text":"بررسی بروزرسانی💠"}],[{"text":"⚠تنظیم ضداسپم"}],[{"text":"🔩تنظیمات گروه"},{"text":"سایر تنظیمات🔆"}],[{"text":"کانال سازنده💰"},{"text":"سازنده ربات🛠"}],[{"text":"کد رونوشت🔏"},{"text":"وارد کردن کد رونوشت📥"}],[{"text":"📨پیامرسان سراسری"}],[{"text":"خروج از پنل🏠"}]],"resize_keyboard":true}';
if(getvalue("admin","chatid",$chatid,"adddokme")=="off"){
				$keypanel=str_replace("افزودن دکمه جدید🔧","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"editdokme")=="off"){
				$keypanel=str_replace("ویرایش دکمه ها✂","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"amarbot")=="off"){
				$keypanel=str_replace("آمار ربات💯","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"resetbot")=="off"){
				$keypanel=str_replace("ریست کامل ربات♻","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"addadmin")=="off"){
				$keypanel=str_replace("بخش ادمین ها👤","",$keypanel);
				}
			    if(getvalue("admin","chatid",$chatid,"pasokh")=="off"){
				$keypanel=str_replace("پاسخ خودکار🔉","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"ersal")=="off"){
				$keypanel=str_replace("ارسال همگانی📣","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"editmatn")=="off"){
				$keypanel=str_replace("ویرایش متن ها✏","",$keypanel);
				}
				
				
				if(getvalue("admin","chatid",$chatid,"group")=="off"){
				$keypanel=str_replace("🔩تنظیمات گروه","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"zedspam")=="off"){
				$keypanel=str_replace("⚠تنظیم ضداسپم","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"pmresanall")=="off"){
				$keypanel=str_replace("📨پیامرسان سراسری","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"sayersetting")=="off"){
				$keypanel=str_replace("سایر تنظیمات🔆","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"addcodero")=="off"){
				$keypanel=str_replace("وارد کردن کد رونوشت📥","",$keypanel);
				}
				if(getvalue("admin","chatid",$chatid,"getcodero")=="off"){
				$keypanel=str_replace("کد رونوشت🔏","",$keypanel);
				}
				}else{
$keypanel ='{"keyboard":[[{"text":"افزودن دکمه جدید🔧"}],[{"text":"ویرایش دکمه ها✂"}],[{"text":"ارسال همگانی📣"},{"text":"ویرایش متن ها✏"}],[{"text":"پاسخ خودکار🔉"}],[{"text":"آمار ربات💯"},{"text":"بخش ادمین ها👤"}],[{"text":"بخش دیتابیس🗃"}],[{"text":"ریست کامل ربات♻"},{"text":"بررسی بروزرسانی💠"}],[{"text":"⚠تنظیم ضداسپم"}],[{"text":"🔩تنظیمات گروه"},{"text":"سایر تنظیمات🔆"}],[{"text":"کانال سازنده💰"},{"text":"سازنده ربات🛠"}],[{"text":"کد رونوشت🔏"},{"text":"وارد کردن کد رونوشت📥"}],[{"text":"📨پیامرسان سراسری"}],[{"text":"خروج از پنل🏠"}]],"resize_keyboard":true}';
}
@$data = json_decode(file_get_contents("string.json"),true);
@$dokme = json_decode(file_get_contents("dokme.json"),true);

$keyboard=getKeyboard();
$keycreatorb= '{"keyboard":[[{"text":"افزودن دکمه🔼"}],'.$keyboard.',[{"text":"افزودن دکمه🔽"}],[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
if(!empty(getadmin($chatid)) || $chatid==$admin || $qid == $admin ){
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"ورود به پنل🔧"}]],"resize_keyboard":'.$sizekol.'}';
}elseif($type=="supergroup" && getKeygroup()=="off"){
	$keykarbar=json_encode([
       "hide_keyboard"=>true
       ]);
	}else{
		$getall = getallvalue("dok","dokme");
		foreach($getall as $ke){
			if(getvalue("dok","dokme",$ke,"hidden")=="on"){
				$keyboard=str_replace('"'.$ke.'"','""',$keyboard);
				}
			}
$keykarbar='{"keyboard":['.$keyboard.'],"resize_keyboard":'.$sizekol.'}';
}
if($chatid != $admin && getvalue("data","id",1,"sendlog")=="on" && $type =="private"){
			if(!empty(getvalue("data","id",1,"logchannel"))){
				if(isset($Message)){
				$cjnml = getvalue("data","id",1,"logchannel");
				
			$xcvtxt="⭐️نام کاربر : $firstname
⭐️نام فامیلی : $username
⭐️ایدی کاربر : $fromid

⭐️زمان : $datesh-$time";
sm($cjnml,$xcvtxt);
fm($cjnml,$chatid,$messageid);
}

}
			}

if(isset($update->inline_query)){
    $chat_id = $update->inline_query->from->id;
    $firstname = $update->inline_query->from->firstname;
    $lastname = $update->inline_query->from->lastname;
     $username = $update->inline_query->from->username;
     $qid = $update->inline_query->from->id;
     $querychatid = $update->inline_query->chat->id;
     $inlineQueryID = $update->inline_query->id;
   $cmn =  $update->inline_query->query;
   if(preg_match('/^Share_(.*)/',$cmn)){
   $cmn = str_replace("Share_","",$cmn);
    $tecv = getvalue("eshtrak","id",$cmn,"text");
    $tecv = preg_replace("/\,?\%?(.*)\|(.*)?SHARE(.*)?/","",$tecv);
   $tecv = str_replace(",%","",$tecv);
      $tecv = str_replace("%%","",$tecv);
      $kei=null;
        if(preg_match("/(%)([^\']+)(%)/",$tecv,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$tecv);
 $tecv = $hi[0];
  $kei = textToinline2("%$k%",$tecv);
  bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'article',
            'id' => base64_encode(1),
            'title' => $userbott,
            'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => $tecv],
            'reply_markup' =>$kei
        ]])
    ]);
}else{
	bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'article',
            'id' => base64_encode(1),
            'title' => $userbott,
            'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => $tecv]
        ]])
    ]);
	}
  }
  }
  
  if(isset($update->inline_query)){
    $chat_id = $update->inline_query->from->id;
    $chatid = $update->inline_query->from->id; 
    $firstname = $update->inline_query->from->firstname;
    $lastname = $update->inline_query->from->lastname;
     $username = $update->inline_query->from->username;
     $qid = $update->inline_query->from->id;
     $querychatid = $update->inline_query->chat->id;
     $inlineQueryID = $update->inline_query->id;
   $cmn =  $update->inline_query->query;
   if(!empty(getvalue("pasokh","pasokh",$cmn,"pasokh")) && getvalue("data","id",1,"autoanswer")=="on"){
	$get = json_decode(getvalue("pasokh","pasokh",$cmn,"javab"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];      
	if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	$kei="";
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline2("%$k%",$ch13);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'article',
            'id' => base64_encode(rand(0,99999999999999999)),
            'title' => $userbott,
            'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => $ch13],
            'reply_markup'=>$kei
        ]])
    ]);
		}else{
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'article',
            'id' => base64_encode(1),
            'title' => $userbott,
            'input_message_content' => ['parse_mode' => 'HTML', 'message_text' => $ch13],
        ]])
    ]);
    }
    ToDie();
	}elseif($type=="photo"){
		$capp = str_text($capp,1);
$capp=str_replace("/r/n/r","\n",$capp);
		if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'photo',
            'id' => base64_encode(1),
            'title' => $userbott,
            'photo_url'=>$tet,
            'thumb_url'=>$tet,
            'caption'=>$capp,
  'reply_markup'=>$kei
        ]])
    ]);
		}else{
		$fh = bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'photo',
            'id' => base64_encode(1),
            'title' => $userbott,
            'photo_url'=>$tet,
            'thumb_url'=>$tet,
            'caption'=>$capp
            ]])
    ]);
    
    }
    ToDie();
		}elseif($type=="video"){
		$capp = str_text($capp,1);
$capp=str_replace("/r/n/r","\n",$capp);
		if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'video',
            'id' => base64_encode(1),
            'title' => $userbott,
            'video_url'=>$tet,
            'mime_type'=>"video/mp4",
            'thumb_url'=>$tet,
            'caption'=>$capp,
  'reply_markup'=>$kei
        ]])
    ]);
		}else{
		$fh = bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'video',
            'id' => base64_encode(1),
            'title' => $userbott,
            'video_url'=>$tet,
            'thumb_url'=>$tet,
            'mime_type'=>"video/mp4",
            'caption'=>$capp
            ]])
    ]);
    
    }
    ToDie();
		}elseif($type=="audio"){
		$capp = str_text($capp,1);
$capp=str_replace("/r/n/r","\n",$capp);
		if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'audio',
            'id' => base64_encode(1),
            'title' => $userbott,
            'audio_url'=>$tet,
            
            'caption'=>$capp,
  'reply_markup'=>$kei
        ]])
    ]);
		}else{
		$fh = bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'audio',
            'id' => base64_encode(1),
            'title' => $userbott,
            'audio_url'=>$tet,
            'caption'=>$capp
            ]])
    ]);
    
    }
    ToDie();
		}elseif($type=="voice"){
		$capp = str_text($capp,1);
$capp=str_replace("/r/n/r","\n",$capp);
		if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'voice',
            'id' => base64_encode(1),
            'title' => $userbott,
            'voice_url'=>$tet,
            'caption'=>$capp,
  'reply_markup'=>$kei
        ]])
    ]);
		}else{
		$fh = bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'voice',
            'id' => base64_encode(1),
            'title' => $userbott,
            'voice_url'=>$tet,
            'caption'=>$capp
            ]])
    ]);
    
    }
    ToDie();
		}elseif($type=="document"){
		$capp = str_text($capp,1);
$capp=str_replace("/r/n/r","\n",$capp);
		if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'document',
            'id' => base64_encode(1),
            'title' => $userbott,
            'document_url'=>$tet,
            'thumb_url'=>$tet,
            'caption'=>$capp,
            'mime_type'=>"application/zip",
  'reply_markup'=>$kei
        ]])
    ]);
		}else{
		$fh = bot('answerInlineQuery',[
        'inline_query_id'=>$inlineQueryID,
        'results' => json_encode([[
            'type' => 'document',
            'id' => base64_encode(1),
            'title' => $userbott,
            'document_url'=>$tet,
            'thumb_url'=>$tet,
            'mime_type'=>"application/zip",
            'caption'=>$capp
            ]])
    ]);
    
    }
			ToDie();
			}
	}
  }
  
if(isset($Message) &&  strpos($type,"group")!==false && getvalue("data","id",1,"leavegroup")=="on"){
	
		if(!empty(getvalue("data","id",1,"textleft"))){
				$txtt=getvalue("data","id",1,"textleft");
				$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($chatid,$ch13,$kei);
			}else{
			$txt = "لفت خودکار فعال هست ، بای بای😘";
			sm($chatid,$txt);
			}
			bot('leaveChat',[
			'chat_id'=>$chatid
			]);
			ToDie();
		
		
		}
		if(!empty(getvalue("user","chatid",$chatid,"stoptime")) && time() < getvalue("user","chatid",$chatid,"stoptime")){
			ToDie();
			}
		if(empty(getadmin($chatid)) && $chatid != $admin && getvalue("data","id",1,"spam")=="on" ){
			$second = getvalue("data","id",1,"spamsecond");
			$tedad = getvalue("data","id",1,"spamtedad");
			$ban = getvalue("data","id",1,"spamban");
			$spamtext = getvalue("data","id",1,"spamtext");
		if(!empty(getvalue("user","chatid",$chatid,"stoptime")) && time() < getvalue("user","chatid",$chatid,"stoptime")){
			ToDie();
			}else{
				setvalue("user","chatid",$chatid,"stoptime","");
				}
		if(empty(getvalue("user","chatid",$chatid,"spam1"))){
			setvalue("user","chatid",$chatid,"spam1",time());
			}
		$spam = getvalue("user","chatid",$chatid,"spam1");
		$spam2 = $spam+$second;
		if(time() < $spam2){
			$flood = getvalue("user","chatid",$chatid,"spam2");
			if($flood < $tedad){
				$flood2 = $flood+1;
				setvalue("user","chatid",$chatid,"spam2",$flood2);
				}else{
					if(!empty(getvalue("data","id",1,"spamtext"))){
				$txtt=getvalue("data","id",1,"spamtext");
				$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($chatid,$ch13,$kei);
			}else{
			$txt = "شما به دلیل اسپم از ربات تا $ban ثانیه محروم شدید⛔";
			sm($chatid,$txt);
			}
					setvalue("user","chatid",$chatid,"spam2",0);
					setvalue("user","chatid",$chatid,"stoptime",time()+$ban);
					ToDie();
					}
			}else{
				setvalue("user","chatid",$chatid,"spam2",0);
				setvalue("user","chatid",$chatid,"spam1",time());
				}
				
				}
if(getvalue("data","id",2,"botpower")=="on"){
	$time_off = getvalue("data","id",2,"autodel");
	if(empty($time_off)){
		$time_off = "23:59";
		}
	$time_on = getvalue("data","id",2,"timeautodel");
	if(empty($time_on)){
		$time_on = "07:59";
		}
		if(date('H:i') >= date('H:i', strtotime($time_off)) ){
			setBotpower("on");
			}elseif(date('H:i') >= date('H:i', strtotime($time_on))){
				setBotpower("off");
				}
}
if(empty(getadmin($chatid)) && $chatid != $admin && getBotpower()=="on" && $type=="private"){
	
		if(!empty(getTxtpower())){
				$txtt=getTxtpower();
				$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($chatid,$ch13,$kei);
			}else{
			$txt = "ربات خاموش میباشد⛔";
			sm($chatid,$txt);
			}
			ToDie();
		}
		if(!empty(getallvalue("delete","chm"))){
	$all = getallvalue("delete","chm");
	foreach($all as $key){
	$time= getvalue("delete","chm",$key,"time");
	$vb = getvalue("delete","chm",$key,"id");
	if(!preg_match('/^POSTDEL([0-9]+)$/',$vb)){
	if(!empty(getvalue("dok","dokme",$vb,"timeautodel"))){
		$timefin = getvalue("dok","dokme",$vb,"timeautodel");
		}else{
			$timefin = 120;
			}
			}else{
				$timefin = str_replace("POSTDEL","",$vb);
				}
	$time = $time + $timefin;
	$now = time();
	if($time <= $now){
		$exp = explode("-",$key);
		$ch = bot('DeleteMessage',[
		'chat_id'=>$exp[0],
		'message_id'=>$exp[1]
		]);
		deletevalue("delete","chm",$key);
		//sm($chatid,"time $time and now is $now and key is $key and ".json_encode($chq));
			}
			}
	}
		if(!empty(getDokme($text)) and empty(getvalue("user","chatid",$fromid,"step"))){
			$get = getvalue("dok","dokme",$text,"amardok");
			$two = $get + 1;
			setvalue("dok","dokme",$text,"amardok",$two);
		}
		if(!empty(getDokme($querydata)) and empty(getvalue("user","chatid",$querychatid,"step"))){
			$get = getvalue("dok","dokme",$querydata,"amardok");
			$two = $get + 1;
			setvalue("dok","dokme",$querydata,"amardok",$two);
		}
		
		if(preg_match('/^\/start [0-9]+$/',$text)){
		$user = str_replace("/start ","",$text);
								if(empty(getvalue("user","chatid",$chatid,"chatid")) || getvalue("user","chatid",$chatid,"coding")==1){
									if($chatid != $user){
									setOther2($chatid,$user);
									setvalue("user","chatid",$chatid,"coding",2);
									}
									}
									}
									if ((empty(getadmin($chatid)) && $chatid != $admin) && $step != "getcaptha1" && empty(getvalue("user", "chatid", $chatid, "captha")) && $type == "private") {
    if (getvalue("data", "id", 1, "captha") == "on") {
        $matn = random(5);

        setOther($chatid, $matn);

        // متن درخواست کپچا
        $ch13 = "لطفا رقم داخل عکس را بفرستید :";
        if (!empty(getvalue("data", "id", 1, "textcaptha1"))) {
            $txtt = getvalue("data", "id", 1, "textcaptha1");
            $ch13 = str_text($txtt, 1);
            $ch13 = str_replace("/r/n/r", "\n", $ch13);
        }

        // JSON برای مخفی کردن کیبورد
        $ke = json_encode([
            "hide_keyboard" => true
        ]);

        // تنظیم استایل کپچا
        step($chatid, "getcaptha1");
        $style = getvalue("data", "id", 1, "capstyle");

        // ارسال کپچا
        sp($chatid, "$domain_url/captha.php?cap=$matn&style=$style", $ch13, $ke);
        ToDie();
    }
}

if ($step == "getcaptha1" && getvalue("data", "id", 1, "captha") == "on" && $type == "private") {
    if (getOther($chatid) == $text) {
        // در صورت صحیح بودن کپچا
        step($chatid, "");
        setvalue("user", "chatid", $chatid, "captha", "true");
        $text = "/start";
    } else {
        // در صورت اشتباه بودن کپچا
        $matn = random(5);
        setOther($chatid, $matn);

        // متن خطای کپچا
        $ch13 = "لطفا کپچا را به درستی وارد کنید!!!";
        if (!empty(getvalue("data", "id", 1, "textcaptha2"))) {
            $txtt = getvalue("data", "id", 1, "textcaptha2");
            $ch13 = str_text($txtt, 1);
            $ch13 = str_replace("/r/n/r", "\n", $ch13);

            // بررسی جایگزینی متن
            if (preg_match("/(%)([^\']+)(%)/", $ch13, $m)) {
                $k = $m[2];
                $hi = preg_split("/(%)([^\']+)(%)/", $ch13);
                $ch13 = $hi[0];
                $kei = textToinline("%$k%", $ch13);
            }
        }

        // بررسی و استفاده از textcaptha3 (در صورت وجود)
        if (!empty(getvalue("data", "id", 1, "textcaptha3"))) {
            $txtt3 = getvalue("data", "id", 1, "textcaptha3");
            $ch13 .= "\n" . str_text($txtt3, 1);
            $ch13 = str_replace("/r/n/r", "\n", $ch13);
        }

        // تنظیم استایل کپچا
        $style = getvalue("data", "id", 1, "capstyle");

        // ارسال کپچا جدید
        sp($chatid, "$domain_url/captha.php?cap=$matn&style=$style", $ch13, $ke);
        ToDie();
    }
}
									if((empty(getadmin($chatid)) && $chatid !=$admin) && $step != "getphone1" && empty(getvalue("user","chatid",$chatid,"phone"))){
			if(getvalue("data","id",1,"lockphone")=="on" ){
				$ch13="لطفا شماره تلفن خود را بفرستید";
				if(!empty(getvalue("data","id",1,"textphone1"))){
						$txtt=getvalue("data","id",1,"textphone1");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]]
				],
				'resize_keyboard'=>true
				]);
				step($chatid,"getphone1");
				sm($chatid,$ch13,$ke);
				ToDie();
			}
			}
			if($step=="getphone1" && getvalue("data","id",1,"lockphone")=="on" ){
				if(isset($Message->contact) && isset($Message->reply_to_message) && $fromid== $Message->contact->user_id){
					if(getvalue("data","id",1,"phoneiran")=="on"){
						$conta = $Message->contact->phone_number;
						if(preg_match('/^\+?98[0-9]+$/',$conta)){
							step($chatid,"");
					setvalue("user","chatid",$chatid,"phone",$Message->contact->phone_number);
					$text="/start";
							}else{
								$ch13= "فقط شماره ایران قابل قبول هست⛔";
						if(!empty(getvalue("data","id",1,"textphiran"))){
						$txtt=getvalue("data","id",1,"textphiran");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
sm($chatid,$ch13);
								ToDie();
								}
						}else{
					step($chatid,"");
					setvalue("user","chatid",$chatid,"phone",$Message->contact->phone_number);
					$text="/start";
					}
}else{
						$ch13= "لطفا از روی کیبورد انتخاب کنید!!!!";
						if(!empty(getvalue("data","id",1,"textphone2"))){
						$txtt=getvalue("data","id",1,"textphone2");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);

}
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]]
				],
				'resize_keyboard'=>true
				]);
			sm($chatid,$ch13,$ke);
			ToDie();
						}
				}
				if((empty(getadmin($chatid)) && $chatid !=$admin) && $step != "getphonepm1" && $step != "getphonepm2" && empty(getvalue("user","chatid",$chatid,"phone2"))){
			if(getvalue("data","id",2,"lockphone")=="on" ){
				$ch13="لطفا شماره تلفن خود را بفرستید";
				if(!empty(getvalue("data","id",1,"textphone1"))){
						$txtt=getvalue("data","id",1,"textphone1");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]]
				],
				'resize_keyboard'=>true
				]);
				step($chatid,"getphonepm1");
				sm($chatid,$ch13,$ke);
				ToDie();
			}
			}
			if(($step=="getphonepm2" && !isset($text)) || $step=="getphonepm1" && getvalue("data","id",2,"lockphone")=="on" ){
				if(isset($Message->contact) && isset($Message->reply_to_message) && $fromid== $Message->contact->user_id){
						$conta = $Message->contact->phone_number;
						if(preg_match('/^\+?98[0-9]+$/',$conta)){
							$api = getvalue("data","id",2,"phoneiran");
						$rand = random2("1234567890",5);
				setOther($chatid,$rand);
							$link = "https://web.sms-bot.ir/api/v1/sendsms?type=send&apikey=$api&phone=$conta&pattern_code=afddw21xrv0v5sn&value=$rand";
							$res = json_decode(file_get_contents($link),true);
							if($res['status_code']==200){
							step($chatid,"getphonepm2");
							$ch13="کد برای شما با موفقیت ارسال شد✅\n\nلطفا کد دریافتی را وارد کنید :";
							if(!empty(getvalue("data","id",2,"textphone1"))){
						$txtt=getvalue("data","id",2,"textphone1");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
sm($chatid,$ch13);
ToDie();
							}else{
								sm($chatid,"⚠️خطایی در موقع ارسال پیامک پیش آمد\n\nلطفا ساعات دیگر دوباره امتحان کنید :");
								ToDie();
								}
							}else{
								$ch13= "فقط شماره ایران قابل قبول هست⛔";
						if(!empty(getvalue("data","id",1,"textphiran"))){
						$txtt=getvalue("data","id",1,"textphiran");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
sm($chatid,$ch13);
								ToDie();
						}
}else{
						$ch13= "لطفا از روی کیبورد انتخاب کنید!!!!";
						if(!empty(getvalue("data","id",1,"textphone2"))){
						$txtt=getvalue("data","id",1,"textphone2");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);

}
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]]
				],
				'resize_keyboard'=>true
				]);
			sm($chatid,$ch13,$ke);
			ToDie();
						}
				}elseif($step=="getphonepm2" && $step!="getphonepm1" && isset($text) && getvalue("data","id",2,"lockphone")=="on" ){
					if(getOther($chatid)==$text){
					step($chatid,"");
					setvalue("user","chatid",$chatid,"phone2","true");
					$text="/start";
					}else{
						$ch13= "لطفا کد را به درستی وارد نمایید!!!!";
						if(!empty(getvalue("data","id",2,"textphone2"))){
						$txtt=getvalue("data","id",2,"textphone2");
		$ch13 = str_text($txtt,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
}
sm($chatid,$ch13);
								ToDie();
						}
					}
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			$cccc =count(getAllchannel());
		if(getLockjoin()=="on" &&  $cccc !=0 && $type=="private"){
			
			$xc = getAllchannel();
			foreach($xc as $key){
				if(preg_match("/^\-[0-9]+$/",$key)){
			$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$key&user_id=$chatid"),true);
			}else{
			$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$key&user_id=$chatid"),true);
			}
			$status = $url2["result"]["status"];
			if($status != "creator" && $status != "administrator" && $status !="member"){
	$txtt=getChanneltext($key);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			break;
			}
	}	}
			}
			if((empty(getadmin($chatid)) && $chatid !=$admin)){
			$cccc =count(getAllBot());
		if(getvalue("data","id",1,"lockrobot")=="on" &&  $cccc !=0 && $type=="private"){
			
			$xc = getAllBot();
			foreach($xc as $key){
				if(empty(getvaluee("user$key","chatid",$chatid,"chatid"))){
				$txtt=getBottext($key);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			break;
			}
			}
			}
			}
		if(getvalue("user","chatid",$chatid,"coding")==2 ){
		$user = getOther2($chatid);
		setvalue("user","chatid",$chatid,"coding",1);
								if(empty(getvalue("user","chatid",$chatid,"chatid")) || getvalue("user","chatid",$chatid,"coding")==1){
									if(empty(getvalue("data","id",1,"zirmajcoin"))){
										$em = 1;
										}else{
											$em = getvalue("data","id",1,"zirmajcoin");
											}
									setvalue("user","chatid",$chatid,"coding",0);
									setOther2($chatid,$user);
									$referal = getvalue("user","chatid",$user,"zirmajuserid");
									$referal = "$referal,$chatid";
									setvalue("user","chatid",$user,"zirmajuserid",$referal);
				$g =	getZirmaj($user);
				$gg = $g+1;
			setZirmaj($user,$gg);
			$xx = getCoin($user);
			$cc = $xx + $em;
			setCoin($user,$cc);
				if(getvalue("data","id",1,"sendzirmaj")=="on"){
					if(empty(getvalue("data","id",1,"textzirmaj"))){
						$txt="یک کاربر به زیرمجموعه های شما افزوده شد✅";
						bot('sendmessage',[
						'chat_id'=>$user,
						"text"=>$txt
						]);
						
						}else{
							$cccb = getvalue("data","id",1,"textzirmaj");
							$ch13 = str_text($cccb,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
bot('sendmessage',[
						'chat_id'=>$user,
						"text"=>$ch13,
						'parse_mode'=>'HTML'
						]);
							}
					}
					if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($chatid,$userid,$messid);
				}
			}
			step($chatid,"");
			$txtt=getStartMessage();
			$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
sm($chatid,$ch13,$keykarbar);
ToDie();
				}else{
		if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($chatid,$userid,$messid);
				}
			}
						step($chatid,"");
						$txtt=getStartMessage();
			$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
sm($chatid,$ch13,$keykarbar);
ToDie();
				}
		}
		
	if(strpos($text,"/start GETFILE")!==false){
	$text=str_replace("/start GETFILE","",$text);
	if(empty(getvalue("upload","hash",$text,"hash"))){
		sm($chatid,"این فایل در سرور وجود ندارد ⛔");
		ToDie();
		}else{
	$tet=getvalue("upload","hash",$text,"text");
	$type=getvalue("upload","hash",$text,"type");
	$cap=getvalue("upload","hash",$text,"caption");
	if($type=="photo"){
		sp($chatid,$tet,$cap);
		}elseif($type=="video"){
		sv($chatid,$tet,$cap);
		}elseif($type=="audio"){
		sa($chatid,$tet,$cap);
		}elseif($type=="voice"){
		svo($chatid,$tet,$cap);
		}elseif($type=="document"){
			sd($chatid,$tet,$cap);
			}
	ToDie();
	}
	}
	
			if(preg_match('/^\/start [0-9]+$/',$text)){
		if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($chatid,$userid,$messid);
				}
			}
						step($chatid,"");
						$txtt=getStartMessage();
			$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
sm($chatid,$ch13,$keykarbar);
ToDie();
								
									}
if(!empty(getDokme($text)) && empty(getstep($chatid))){
if(getLockchannel($text)=="on"){
	$user = getDokuser($text);
	$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$chatid"),true);
			$status = $url2["result"]["status"];
			if($status !== "creator" && $status != "administrator" && $status !="member"){
	$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			}
	}elseif(getLockzirmaj($text)=="on"){
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(getDoktedad($text) > getZirmaj($fromid)){
					$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}
			}
		}elseif(getLockcoin($text)=="on"){
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(getDoktedad($text) > getCoin($fromid)){
					$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}else{
					$man =getDoktedad($text);
					$coin=getCoin($fromid);
					$cv = $coin - $man;
					setCoin($fromid,$cv);
					}
			}
		}elseif(getvalue("dok","dokme",$text,"lockemtiaz2")=="on"){
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(getDoktedad($text) > getCoin($fromid)){
					$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}
			}
		}elseif(getvalue("dok","dokme",$text,"lockday")=="on"){
			if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(date("d")==getvalue("user","chatid",$chatid,"dayl")){
				$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}else{
					setvalue("user","chatid",$chatid,"dayl",date("d"));
					}
			}
	}elseif(getvalue("dok","dokme",$text,"lockcode")=="on" && $step != "getcode"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$text);
	step($chatid,"getcode");
	$txtt=getvalue("dok","dokme",$text,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$keyback=json_encode([
	'keyboard'=>[
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$text,"lockshomare")=="on" && $step != "getshomare"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$text);
	step($chatid,"getshomare");
	$txtt=getvalue("dok","dokme",$text,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	
	]);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$text,"lockshomare2")=="on" && $step != "getshomare2"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$text);
	step($chatid,"getshomare2");
	$txtt=getvalue("dok","dokme",$text,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	
	]);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$text,"locknafar")=="on"){
	if((empty(getadmin($chatid)) && $chatid !=$admin)){
	$num = getvalue("dok","dokme",$text,"numnafar");
	$fin = getvalue("dok","dokme",$text,"finishnafar");
if($num >= $fin){
	$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
	}else{
		$num = $num + 1;
		$num = setvalue("dok","dokme",$text,"numnafar",$num);
	
		}
}
}elseif(getvalue("dok","dokme",$text,"locksade")=="on"){
			if((empty(getadmin($chatid)) && $chatid !=$admin)){
				$txtt=getTextlock($text);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			}
			}
	}
	if(!empty(getDastortext($text)) && empty(getstep($chatid))){
		$bmm = getDastortext($text);
if(getLockchannel($bmm)=="on"){
	$user = getDokuser($bmm);
	$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$chatid"),true);
			$status = $url2["result"]["status"];
			if($status != "creator" && $status != "administrator" && $status !="member"){

	$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			}
	}elseif(getLockzirmaj($bmm)=="on"){
		if((empty(getstep($chatid)) && $chatid !=$admin)){
			if(getDoktedad($bmm) > getZirmaj($fromid)){
					$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}
			}
		}elseif(getLockcoin($bmm)=="on"){
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(getDoktedad($bmm) > getCoin($fromid)){
					$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}else{
					
							$man = getDoktedad($bmm);
							
					$coin=getCoin($fromid);
					$cv = $coin - $man;
					setCoin($fromid,$cv);
					}
			}
		}elseif(getvalue("dok","dokme",$bmm,"lockemtiaz2")=="on"){
		if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(getDoktedad($bmm) > getCoin($fromid)){
					$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}
			}
		}elseif(getvalue("dok","dokme",$bmm,"lockday")=="on"){
			if((empty(getadmin($chatid)) && $chatid !=$admin)){
			if(date("d")==getvalue("user","chatid",$chatid,"dayl")){
				$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
				}else{
					setvalue("user","chatid",$chatid,"dayl",date("d"));
					}
			}
	}elseif(getvalue("dok","dokme",$bmm,"lockcode")=="on" && $step != "getcode"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$bmm);
	step($chatid,"getcode");
	$txtt=getvalue("dok","dokme",$bmm,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$bmm,"lockshomare")=="on" && $step != "getshomare"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$bmm);
	step($chatid,"getshomare");
	$txtt=getvalue("dok","dokme",$bmm,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$bmm,"lockshomare2")=="on" && $step != "getshomare2"){
if((empty(getadmin($chatid)) && $chatid !=$admin)){
	setOther2($fromid,$bmm);
	step($chatid,"getshomare2");
	$txtt=getvalue("dok","dokme",$bmm,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($chatid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$bmm,"locknafar")=="on"){
	if((empty(getadmin($chatid)) && $chatid !=$admin)){
	$num = getvalue("dok","dokme",$bmm,"numnafar");
	$fin = getvalue("dok","dokme",$bmm,"finishnafar");
if($num >= $fin){
	$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
	}else{
		$num = $num + 1;
		$num = setvalue("dok","dokme",$bmm,"numnafar",$num);
	
		}
}
}elseif(getvalue("dok","dokme",$bmm,"locksade")=="on"){
			if((empty(getadmin($chatid)) && $chatid !=$admin)){
			$txtt=getTextlock($bmm);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
			ToDie();
			}
			}
	}
	if((empty(getadmin($chatid)) && $chatid !=$admin) && getvalue("data","id",1,"autodel")=="on"){
		$df = getallvalue("del","del");
		foreach($df as $xcv){
			if($xcv==$text){
				bot('DeleteMessage',[
				'chat_id'=>$chatid,
				'message_id'=>$messageid
				]);
				}
			}
		}
if((empty(getadmin($chatid)) && $chatid !=$admin) && getvalue("data","id",1,"filter")=="on" && !empty(getvalue("user","chatid",$chatid,"step"))){
		$df = getallvalue("filter","filter");
		foreach($df as $xcv){
			$text=str_replace($xcv,"",$text);
			}
		}
		if((empty(getadmin($chatid)) && $chatid !=$admin) && getvalue("data","id",1,"replacetext")=="on" && !empty(getvalue("user","chatid",$chatid,"step"))){
		$df = getallvalue("replac","replac");
		foreach($df as $xcv){
			$tt = getvalue("replac","replac",$xcv,"totext");
			$text=str_replace($xcv,$tt,$text);
			}
		}
if(isset($query)){
	if(strpos($querydata,"/start GETFILE")!==false){
	$querydata=str_replace("/start GETFILE","",$querydata);
	if(empty(getvalue("upload","hash",$querydata,"hash"))){
		sm($chatid,"این فایل در سرور وجود ندارد ⛔");
		ToDie();
		}else{
	$tet=getvalue("upload","hash",$querydata,"text");
	$type=getvalue("upload","hash",$querydata,"type");
	$cap = getvalue("upload","hash",$querydata,"caption");
	if($type=="photo"){
		sp($querychatid,$tet,$cap);
		}elseif($type=="video"){
		sv($querychatid,$tet,$cap);
		}elseif($type=="audio"){
		sa($querychatid,$tet,$cap);
		}elseif($type=="voice"){
		svo($querychatid,$tet,$cap);
		}elseif($type=="document"){
			sd($querychatid,$tet,$cap);
			}
	ToDie();
	}
	}
	if($querydata=="/start"){
		if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($qid,$userid,$messid);
				}
			}
				$key=json_encode([
							"hide_keyboard"=>true
							]);
							
			
			
			step($querychatid,"");
			$txtt=getStartMessage();
			$ch13 = str_text($txtt,2);
sm($querychatid,$ch13,$keykarbar);
ToDie();
}elseif(preg_match('/^(Javab)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(Javab)(.*)$/',$querydata,$m);
		$id = $m[2];
		setOther2($qid,$id);
		step($qid,"sendforchat");
		sm($querychatid,"لطفا متن خود را بفرستید تا برای کاربر $id ارسال شود \n\n⭐از Html میتوانید در متن استفاده کنید .\n\n⭐از دکمه های شیشه ای میتوانید در متن استفاده کنید\n ",$keyback);
		ToDie();
}elseif(preg_match('/^(reading)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(reading)(.*)$/',$querydata,$m);
		$id = $m[2];
		$exp=explode("-.-",$id);
		$keym=json_encode([
			'inline_keyboard'=>[
			[["text"=>"خوانده شده✅","callback_data"=>"NOTIFخوانده شده✅"]],
			]
			]);
		erm($querychatid,$messageid,$keym);
		ToDie();
			}
elseif(preg_match('/^(block)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(block)(.*)$/',$querydata,$m);
		$id = $m[2];
		if($id==$admin){
			sm($chatid,"خودتون رو میخواهید بلاک کنید ؟!!");
			ToDie();
			}else{
		insert("blocklist","`chatid`","'$id'");
		sm($admin,"کاربر $id به لیست بلاک اضافه شد✅");
		ToDie();
		}
}elseif(preg_match('/^(manfiwarn)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(manfiwarn)(.*)$/',$querydata,$m);
		$id = $m[2];
		if($id==$admin){
			sm($chatid,"خودتون رو میخواهید اخطار بدید!!");
			ToDie();
			}else{
		$tedad = getvalue("user","chatid",$id,"tedadwarn") -1;
		if($tedad < 1){
			setvalue("user","chatid",$id,"tedadwarn",0);
			}else{
				setvalue("user","chatid",$id,"tedadwarn",$tedad);
				}
				if(empty(getvalue("data","id",1,"maxwarn"))){
				$maxwarn = 4;
				}else{
					$maxwarn = getvalue("data","id",1,"maxwarn");
					}
					if(empty(getvalue("user","chatid",$id,"tedadwarn"))){
				$tedadwarn = 0;
				}else{
					$tedadwarn = getvalue("user","chatid",$id,"tedadwarn");
					}
					
			$keym=json_encode([
			'inline_keyboard'=>[
			[["text"=>"مشخصات فرد👦",'callback_data'=>"etlaat$id"]],
			[["text"=>"جواب دادن↪","callback_data"=>"Javab$id"],["text"=>"بلاک کردن⛔",'callback_data'=>"block$id"]],
			[["text"=>"➖","callback_data"=>"manfiwarn$id"],["text"=>"$tedadwarn/$maxwarn","callback_data"=>"تعداد اخطار ها"],["text"=>"➕","callback_data"=>"addwarn$id"]],
			[["text"=>"خوانده شدن و بستن کیبورد👁","callback_data"=>"reading$id-.-$messageid"]],
			]
			]);
			erm($querychatid,$messageid,$keym);
		ToDie();
		}
}elseif(preg_match('/^(addwarn)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(addwarn)(.*)$/',$querydata,$m);
		$id = $m[2];
		if($id==$admin){
			sm($chatid,"خودتون رو میخواهید اخطار بدید!!");
			ToDie();
			}else{
		if(empty(getvalue("data","id",1,"maxwarn"))){
				$maxwarn = 4;
				}else{
					$maxwarn = getvalue("data","id",1,"maxwarn");
					}
					if(empty(getvalue("user","chatid",$id,"tedadwarn"))){
				$tedadwarn = 0;
				}else{
					$tedadwarn = getvalue("user","chatid",$id,"tedadwarn");
					}
					$tedadwarn = $tedadwarn +1;
					setvalue("user","chatid",$id,"tedadwarn",$tedadwarn);
					if(!empty(getvalue("data","id",1,"textwarn"))){
					$textwarn2 = str_text(getvalue("data","id",1,"textwarn"),2);
					}else{
						$textwarn2 = "شما یک خطا دریافت کردید!!";
						}
			$keym=json_encode([
			'inline_keyboard'=>[
			[["text"=>"مشخصات فرد👦",'callback_data'=>"etlaat$id"]],
			[["text"=>"جواب دادن↪","callback_data"=>"Javab$id"],["text"=>"بلاک کردن⛔",'callback_data'=>"block$id"]],
			[["text"=>"➖","callback_data"=>"manfiwarn$id"],["text"=>"$tedadwarn/$maxwarn","callback_data"=>"تعداد اخطار ها"],["text"=>"➕","callback_data"=>"addwarn$id"]],
			[["text"=>"خوانده شدن و بستن کیبورد👁","callback_data"=>"reading$id-.-$messageid"]],
			]
			]);
		
			erm($querychatid,$messageid,$keym);
			sm($id,$textwarn2);
			if($tedadwarn >= $maxwarn){
				setvalue("user","chatid",$id,"tedadwarn",0);
				if(!empty(getvalue("data","id",2,"maxwarn"))){
					$textwarn = str_text(getvalue("data","id",2,"maxwarn"),2);
					}else{
						$textwarn = "شما به علت دریافت خطا از ربات محدود شدید!!";
						}
						if(!empty(getvalue("data","id",2,"textwarn"))){
							if(getvalue("data","id",2,"textwarn")=="block"){
								insert("blocklist","`chatid`","'$id'");
								}elseif(getvalue("data","id",2,"textwarn")=="spam"){
								$second = getvalue("data","id",1,"spamsecond");
			$tedad = getvalue("data","id",1,"spamtedad");
			$ban = getvalue("data","id",1,"spamban");
			setvalue("user","chatid",$id,"spam2",0);
					setvalue("user","chatid",$id,"stoptime",time()+$ban);
								}
							}else{
								insert("blocklist","`chatid`","'$id'");
								}
								sm($id,$textwarn);
								sm($querychatid,"کاربر به علت دریافت حداکثر خطا ، محدود شد✅ ");
				}
		ToDie();
		}
}elseif(preg_match('/^(etlaat)(.*)$/',$querydata) && (!empty(getadmin($qid)) or $qid ==$admin)){
		preg_match('/^(etlaat)(.*)$/',$querydata,$m);
		$id = $m[2];
						if(!empty(getvalue("user","chatid",$id,"chatid"))){
							$name = getvalue("user","chatid",$id,"firstname");
							$user = getvalue("user","chatid",$id,"username");
						$joinsh = getvalue("user","chatid",$id,"joindatesh");
						$joinm = getvalue("user","chatid",$id,"joindatem");
						$jointime = getvalue("user","chatid",$id,"jointime");
						$number = getvalue("user","chatid",$id,"phone");
						if(empty($number)){
							$number = "وارد نشده⛔";
							}
						$zirmaj = getvalue("user","chatid",$id,"zirmaj");
						$coin = getvalue("user","chatid",$id,"emtiaz");
						$captha = getvalue("user","chatid",$id,"captha");
						if($captha =="true"){
							$captha ="انجام شده✅";
						}else{
							$captha ="انجام نشده⛔";
							}
							 $tem1 = "💠جزو کاربران ربات : ✅هست
💠زیرمجموعه ها : $zirmaj
💠امتیازات کاربر : $coin
💠شماره تلفن : $number
💠تاریخ عضویت میلادی : $joinm
💠تاریخ عضویت شمسی : $joinsh
💠زمان عضویت : $jointime
💠وضعیت کپچا : $captha";
							}else{
								$tem1 = "💠جزو کاربران ربات : ⛔نیست";
								}
					$txt = "💠ایدی عددی کاربر : $id\n\n💠اسم کاربر : $name\n\n💠یوزرنیم کاربر : @$user\n\n💠منشن 1 : <a href='tg://user?id=$id'>$name</a>\n💠منشن 2 : <a href='tg://openmessage?user_id=$id'>$name</a>\n$tem1";
			sm($querychatid,$txt);
ToDie();
}elseif(!empty(getvalue("pasokh","pasokh",$querydata,"pasokh")) && getvalue("data","id",1,"autoanswer")=="on"){
	$get = json_decode(getvalue("pasokh","pasokh",$querydata,"javab"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	$ch13 = str_text($capp,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($querychatid,$ch13,$kei);
	}elseif($type=="photo"){
		sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			ss($querychatid,$tet);
			}elseif($type=="contact"){
		sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		svin($querychatid,$tet);
		}elseif($type=="location"){
			slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$gett=sdi($querychatid,$tet);
			$get = $gett->result->dice->value;
			setOther($qid,$get);
			$em= $gett->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			sd($querychatid,$tet,$cap,$kei);
			}
			}elseif(strpos($querydata,"DELETE")!==false){
				
				if(!empty($query->message->message_id)){
bot("DeleteMessage",[
				'chat_id'=>$querychatid,
				'message_id'=>$messageid
				]);
}else{
bot("DeleteMessage",[
				'inline_message_id'=>$messageid
				]);
	}
				ToDie();
			}elseif(strpos($querydata,"LIKE")!==false){
				$messageidd = $update->callback_query->message->message_id;
			$exp = explode("+-+",$querydata);
			$tim = str_replace("LIKE","",$exp[0]);
			$nam = str_replace("--+--","",$exp[1]);
			$query = "SELECT * FROM `likes$userbott` WHERE `time`='$tim' AND `name`='$nam'";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
	$tim2 = $row['time'];
$name = $row["name"];
$userid = $row["userid"];
$messageiid = $row["message_id"];

if(empty($tim2) && empty($name)){
	$xcv = insert("likes","`userid`,`time`,`name`,`message_id`","'$fromid,','$tim','$nam','$messageidd'");
	$tx = getvalue("liketext","code",$tim,"text");
	$keym = getvalue("liketext","code",$tim,"key");
	$keyi = textToinline3("$keym",$tx);
	if(getvalue("data","id",1,"notoflike")=="on"){
		if(empty(getvalue("data","id",1,"notofliketext"))){
			$notof="لایک👍 شما ثبت شد";
			}else{
				$notof = getvalue("data","id",1,"notofliketext");
				}
				alert($update->callback_query->id,$notof,false);
		}
	if(isset($update->callback_query->inline_message_id)){
		$ghh = bot('editMessageReplyMarkup',[
	
	'inline_message_id'=>$update->callback_query->inline_message_id,
	'reply_markup'=>$keyi
	]);
		}else{
	$ghh = bot('editMessageReplyMarkup',[
	'chat_id'=>$querychatid,
	'message_id'=>$messageidd,
	'reply_markup'=>$keyi
	]);
	}
	}elseif(!empty($tim2) && !empty($name)){
		$exxp=explode(",",$userid);
		if(!in_array($fromid,$exxp)){
			
		$query = "UPDATE `likes$userbott` SET `userid` = '$userid$fromid,' WHERE `time` = '$tim2' AND `name` = '$name'";
$result = mysqli_query($con,$query);
if(getvalue("data","id",1,"notoflike")=="on"){
		if(empty(getvalue("data","id",1,"notofliketext"))){
			$notof="لایک👍 شما ثبت شد";
			}else{
				$notof = getvalue("data","id",1,"notofliketext");
				}
				alert($update->callback_query->id,$notof,false);
		}
		}else{
			
			$ttt = str_replace("$fromid,","",$userid);
			$query = "UPDATE `likes$userbott` SET `userid` = '$ttt' WHERE `time` = '$tim2' AND `name` = '$name'";
$result = mysqli_query($con,$query);
if(getvalue("data","id",1,"notoflike")=="on"){
		if(empty(getvalue("data","id",1,"notofdisliketext"))){
			$notof="لایک 👎 شما پس گرفته شد";
			}else{
				$notof = getvalue("data","id",1,"notofdisliketext");
				}
				alert($update->callback_query->id,$notof,false);
		}
			}
		$tx = getvalue("liketext","code",$tim2,"text");
	$keym = getvalue("liketext","code",$tim2,"key");
	$keyi = textToinline3("$keym",$tx);
	
	if(isset($update->callback_query->inline_message_id)){
		$ghh = bot('editMessageReplyMarkup',[
	
	'inline_message_id'=>$update->callback_query->inline_message_id,
	'reply_markup'=>$keyi
	]);
		}else{
	$ghh = bot('editMessageReplyMarkup',[
	'chat_id'=>$querychatid,
	'message_id'=>$messageidd,
	'reply_markup'=>$keyi
	]);
	}
		}
ToDie();
			}elseif(strpos($querydata,"ONCE") !==false){
$querydata=str_replace("ONCE","",$querydata);
$exp = explode("*",$querydata);
$messageid = $qmessage->message_id;
if(empty(getvalue("once","id",$messageid,"id"))){
	$text = $exp[0];
	insert("once","`id`","'$messageid'");
	if(getvalue("data","id",1,"oncetype")=="notif"){
	alert($queryid,$text,false);
	}else{
		alert($queryid,$text);
		}
}else{
	$text = $exp[1];
	if(getvalue("data","id",1,"oncetype")=="notif"){
	alert($queryid,$text,false);
	}else{
		alert($queryid,$text);
		}
	}
ToDie();
}elseif(strpos($querydata,"EDIT")!==false){
			$querydata=str_replace("EDIT","",$querydata);
			$messageid = $qmessage->message_id;
			if(!empty(getvalue("pasokh","pasokh",$querydata,"pasokh")) && getvalue("data","id",1,"autoanswer")=="on"){
	$get = json_decode(getvalue("pasokh","pasokh",$querydata,"javab"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	if(strpos($tet,"EDITMSG")){
		$ex=explode("EDITMSG",$tet);
		$tet=$ex[0];
		$edit=$ex[1];
		}
		
	$ch13 = str_text($capp,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$ch13=str_replace("/r/n/r","\n",$ch13);
if(!empty($query->message->message_id)){
em($querychatid,$ch13,$messageid,$kei);
}else{
	$xh = bot("editMessageText",[
	'inline_message_id'=>$inline_messageid,
	'text'=>$ch13,
	'reply_markup'=>$kei,
	'parse_mode'=>'Html'
	]);
	}
	if(isset($edit)){
		$sd = $edit;
		$vb=str_replace("(","",$sd);
		$vb=str_replace(")","",$vb);
		$exp=explode(",",$vb);
		$x=1;
		foreach($exp as $key){
			$ch13 = str_text($key,1);
	if(!empty($query->message->message_id)){
em($querychatid,$ch13,$messageid,$kei);
}else{
	bot("editMessageText",[
	'inline_message_id'=>$messageid,
	'text'=>$ch13,
	'reply_markup'=>$kei,
	'parse_mode'=>'Html'
	]);
	}
			}
		}
	}elseif($type=="photo"){
		sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			ss($querychatid,$tet);
			}elseif($type=="contact"){
		sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		svin($querychatid,$tet);
		}elseif($type=="location"){
			slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$gett=sdi($querychatid,$tet);
			$get = $gett->result->dice->value;
			setOther($qid,$get);
			$em= $gett->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			sd($querychatid,$tet,$cap,$kei);
			}
			}
			if(getLockchannel($querydata)=="on"){
	$user = getDokuser($querydata);
	$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$querychatid"),true);
			$status = $url2["result"]["status"];
			if($status != "creator" && $status != "administrator" && $status !="member"){
	$txtt=getTextlock($querydata);
	$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
			
		if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		em($querychatid,$ch13,$messageid,$kei);
		
			ToDie();
			}
	}	elseif(getLockcoin($querydata)=="on"){
		if((empty(getadmin($qid)) && $qid !=$admin)){
			if(getDoktedad($querydata) > getCoin($qid)){
					$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
em($querychatid,$ch13,$messageid,$kei);
			
			ToDie();
				}else{
					$man = getDoktedad($querydata);
					$coin=getCoin($qid);
					$cv = $coin - $man;
					setCoin($qid,$cv);
					}
			}
			
			}elseif(getvalue("dok","dokme",$querydata,"lockemtiaz2")=="on"){
		if((empty(getadmin($qid)) && $qid !=$admin)){
			if(getDoktedad($querydata) > getCoin($qid)){
					$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
em($querychatid,$ch13,$messageid,$kei);
			
			ToDie();
				}
			}
			
			}elseif(getvalue("dok","dokme",$querydata,"lockday")=="on"){
			if((empty(getadmin($qid)) && $qid !=$admin)){
			if(date("d")==getvalue("user","chatid",$qid,"dayl")){
				$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
em($querychatid,$ch13,$messageid,$kei);
			ToDie();
				}else{
					setvalue("user","chatid",$qid,"dayl",date("d"));
					}
			}
		}elseif(getvalue("dok","dokme",$querydata,"lockcode")=="on" && $step != "getcode"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getcode");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
			em($querychatid,$ch13,$messageid,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"lockshomare")=="on" && $step != "getshomare"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getshomare");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($qid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"lockshomare2")=="on" && $step != "getshomare2"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getshomare2");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($qid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"locknafar")=="on"){
	if((empty(getadmin($chatid)) && $chatid !=$admin)){
	$num = getvalue("dok","dokme",$querydata,"numnafar");
	$fin = getvalue("dok","dokme",$querydata,"finishnafar");
if($num >= $fin){
	$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			em($querychatid,$ch13,$messageid,$kei);
			ToDie();
	}else{
		$num = $num + 1;
		$num = setvalue("dok","dokme",$querydata,"numnafar",$num);
	
		}
}
}elseif(getvalue("dok","dokme",$querydata,"locksade")=="on"){
			if((empty(getadmin($qid)) && $qid !=$admin)){
			$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
em($querychatid,$ch13,$messageid,$kei);
			ToDie();
			}
		}
			if(getDokmenok($querydata)=="getApi"){
				step($querychatid,"getApi$querydata");
				setCode($querychatid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
				em($querychatid,$teext,messageid,$keyback);
				}}else{
				if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
em($querychatid,$ch13,$messageid,$keyback);
		//sm($querychatid,$ch13,$keyback);
		}
		if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		ToDie();
				}elseif(getDokmenok($querydata)=="jostojo"){
					step($qid,"jostojo$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	em($querychatid,$ch13,$messageid,$keyback);
		//sm($qid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="search"){
					step($qid,"search$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	em($querychatid,$ch13,$messageid,$keyback);
		//sm($qid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="change"){
					step($qid,"change$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($qid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ایدی عددی فردی که میخواهید برای ان امتیاز ارسال کنید را وارد کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	em($querychatid,$ch13,$messageid,$keyback);
		//sm($qid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="enfjar"){
					step($qid,"enfjar$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($qid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="👈 لطفا تعداد سکه ای که میخواهید شرط ببندید را وارد کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	em($querychatid,$ch13,$messageid,$keyback);
		//sm($qid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="createbot"){
					step($chatid,"createbot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا توکن ربات را برای ساخت ربات ارسال نمایید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="updatebot"){
					step($chatid,"updatebot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="deletebot"){
					step($chatid,"deletebot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}
					
elseif(getDokmenok($querydata)=="sendadmin"){
					step($querychatid,"sendadmin$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
				em($querychatid,$teext,messageid,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
em($querychatid,$ch13,$messageid,$keyback);
		
		//sm($querychatid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="schannel"){
					step($querychatid,"schannel$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
				em($querychatid,$teext,messageid,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
em($querychatid,$ch13,$messageid,$keyback);
		
		//sm($querychatid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="fchannel"){
					step($querychatid,"fchannel$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
				em($querychatid,$teext,messageid,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
em($querychatid,$ch13,$messageid,$keyback);
		
		//sm($querychatid,$ch13,$keyback);
					}
					ToDie();
					}
				elseif(getDokmenok($querydata)=="getmatntaki"){
				step($querychatid,"getmatntaki$querydata");
				setCode($qid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
				em($querychatid,$teext,messageid,$keyback);
				}}else{
			if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
				$ch13 = str_text($tx,2);
				}
em($querychatid,$ch13,$messageid,$keyback);
		
		//sm($querychatid,$ch13,$keyback);
				}
				if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
				ToDie();
				}elseif(getDokmenok($querydata)=="getphp"){
				step($querychatid,"getphp$querydata");
				setCode($qid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
				em($querychatid,$teext,messageid,$keyback);
				}}else{
			if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
				$ch13 = str_text($tx,2);
				}
em($querychatid,$ch13,$messageid,$keyback);
		
		//sm($querychatid,$ch13,$keyback);
				}
				
				ToDie();
				}elseif(getDokmenok($querydata)=="coin"){
				$tet=getDokmetext($querydata);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `emtiaz` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `emtiaz` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getCoin($fg);
					if(!empty(getvalue("dok","dokme",$querydata,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$querydata,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$ch13=str_replace("/r/n/r","\n",$ch13);
em($querychatid,$ch13,$messageid,$kei);
	//sm($qid,$texxt,$kei);
	ToDie();
				}elseif(getDokmenok($querydata)=="bartarin"){
				$tet=getDokmetext($querydata);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `zirmaj` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `zirmaj` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getZirmaj($fg);
					if(!empty(getvalue("dok","dokme",$querydata,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$querydata,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$ch13=str_replace("/r/n/r","\n",$ch13);
em($querychatid,$ch13,$messageid,$kei);
	//sm($qid,$texxt,$kei);
	ToDie();
				}elseif(getDokmenok($querydata)=="php"){
     $get=getDokmetext($querydata);
     $array1 = array('$FIRSTNAME','$LASTNAME','$USERNAME','$USERID','$PHONE','$BIO','$IDBOT','$BOTUSER','$BOTNAME','$GPNAME','$GPUSER','$CHATID','$DESCRIOPTION','$GETDICE','$DICE','$MESSAGEID','$COIN','$MEMBER','$LINK','$ALLMEM','$HOUR','$MINUTE','$SECOND','$JOINDATEM','$JOINDATESH','$JOINTIME','$TIME','$YEAR','$MONTH','$DAY','$DATESH','$DATEM','$BOTMEM','$TEXT','$ADD');
 $array2 = array('"'.$qname.'"','"'.$lastname.'"','"'.$quser.'"','"'.$qid.'"','"'.$phone_number.'"','"'.$bio.'"','"'.$idbot.'"','"'.$botuser.'"','"'.$botname.'"','"'.$gpname.'"','"'.$gpuser.'"','"'.$querychatid.'"','"'.$description.'"','"'.getOther($qid).'"','"'.$Message->dice->value.'"','"'.$messageid.'"','"'.getCoin($qid).'"','"'.getZirmaj($qid).'"','"https://t.me/'.$botuser.'?start='.$qid.'"','"'.getDokother2($querydata).'"','"'.date('H').'"','"'.date('i').'"','"'.date('s').'"','"'.getJoindatem($qid).'"','"'.getJoindatesh($qid).'"','"'.getJointime($qid).'"','"'.date("H:i:s").'"','"'.date("Y").'"','"'.date("m").'"','"'.date("d").'"','"'.$datesh.'"','"'.$datem.'"','"'.amarcount("user").'"','"'.$querychatid.'"',"");
 $code=str_replace($array1,$array2,$get);
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://rextester.com/rundotnet/Run");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"LanguageChoice=8&Program=$code&CompilerArgs=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
$ge=json_decode($server_output,true);
if(!empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }elseif(empty($ge["Errors"]) && !empty($ge["Result"])){
 $sg = sm($chatid,$ge["Result"]);
 }else{
$sg =   sm($chatid,$ge["Errors"]);
  }
  
$messageid= $sg->result->message_id;
if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
  ToDie();   
     }
	elseif(getDokmenok($querydata)=="matntaki"){
		
	$get = json_decode(getDokmetext($querydata),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	$ch13 = str_text($capp,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
	if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		$sg = em($querychatid,$ch13,$messageid,$kei);
		//sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			$sg = ss($querychatid,$tet);
			}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg=sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			setOther($qid,$get);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
			ToDie();
	}elseif(getDokmenok($querydata)=="matntartib"){
		$get = json_decode(getDokmetext($querydata),true);
		step($querychatid,"matntartib$querydata");
				setCode($querychatid,$querydata);
				if(empty(getvalue("dok","dokme",$querydata,"textemtiaz1"))){
					$dokmee="پست بعدی⏩";
					}else{
						$dokmee=getvalue("dok","dokme",$querydata,"textemtiaz1");
						}
						if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
									$keybo=json_encode([
									'keyboard'=>[
									[["text"=>$dokmee]],
									[["text"=>$back]]
									],
									'resize_keyboard'=>true,
									]);
									setvalue("dok","dokme",$querydata,"textemtiaz2",0);
						$nok=$get[0];
						$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,2);
	$capp=str_replace("/r/n/r","\n",$capp);
	
				$capp=str_replace("ADD","",$capp);
		
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	em($querychatid,$ch13,$messageid,$keybo);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$capp,$keybo);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$capp,$keybo);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$capp,$keybo);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$capp,$keybo);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet,$keybo);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$capp);
		em($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$messageid,$keybo);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		em($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$messageid,$keybo);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$capp);
			em($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$messageid,$keybo);
			}elseif($type=="dice"){
			
			$sg = sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			em($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$messageid,$keybo);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$capp,$keybo);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}elseif(getDokmenok($querydata)=="matnchand"){
		$get = json_decode(getDokmetext($querydata),true);
		$x = 0;
foreach ($get as $nok){
	if($x==10){
	sleep(1);
$x=0;	
	}
	$x++;
$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	$cap = str_text($capp,2);
$cap=str_replace("/r/n/r","\n",$cap);
	
if(preg_match("/(%)([^\']+)(%)/",$cap,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$cap);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		$sg = em($querychatid,$ch13,$messageid,$kei);
//sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg=sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
}
		}elseif(getDokmenok($querydata)=="matnrand"){
		$get = json_decode(getDokmetext($querydata),true);
$cou = count($get)-1;
$rand=rand(0,$cou);
$type = $get[$rand]['type'];
	$tet = $get[$rand]['text'];
	$capp = $get[$rand]['caption'];
	$ch13 = str_text($capp,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
$sg = em($querychatid,$ch13,$messageid,$kei);
	//sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}elseif(getDokmenok($querydata)=="Api"){
							$gget = json_decode(getDokmetext($querydata),true);
				$get=$gget['text'];
				if(strpos($get,"||")){
					$exp=explode("||",$get);
				$get =$exp[0];
					$cap=$exp[1];
    $cap13 = str_text($cap,2);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
  } 
}
$txtt = $get;
			$ch13 = str_text($txtt,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
if(validImage($ch13)=="image"){
							if(strpos(typee($ch13),"gif")!==false){
							$sg = sd($qid,$ch13,$cap13,$kei);
							}else{
							$sg = sp($qid,$ch13,$cap13,$kei);
							}
							}elseif(validImage($ch13)=="audio"){
$sg = sa($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="video"){
$sg = sv($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="audio"){
$sg = sa($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="application"){
	if(strpos(typee($ch13),"json")!==false){
		$xc = file_get_contents($ch13);
		$sg = em($querychatid,$xc.$cap13,$messageid,$kei);
						//sm($qid,$xc.$cap13,$kei);
		}else{
$sg = sd($qid,$ch13,$cap13,$kei);
}
}else{
	if(getimagesize("$ch13")==true){
		$sg = sp($qid,$ch13,$cap13,$kei);
		}else{
								$xc = file_get_contents($ch13);
								$sg = em($querychatid,$xc.$cap13,$messageid,$kei);
						//sm($qid,$xc.$cap13,$kei);
						}
						}
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}
elseif(getDokmenok($querydata)=="rss"){
							$get = json_decode(getDokmetext($querydata),true);
							$tbbt=$get['text'];
							
		if(preg_match("/(%)([^\']+)(%)/",$tbbt,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$tbbt);
	$tbbt = $hi[0];
		$kei = textToinline("%$k%",$tbbt);
		}
						$xc = getrss($tbbt);
						$sg = em($querychatid,$xc,$messageid,$kei);
						//sm($querychatid,$xc,$kei);
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
						}elseif(getDokmenok($querydata)=="back"){
							if(!empty(getTextersal($querydata))){
								$tet = getTextersal($querydata);
								$ch13 = str_text($tet,2);
$txt=$ch13;
								}else{
								$txt="به عقب برگشتید :";
								}
$key = getvalue("dok","dokme",$querydata,"keyback");
if($key=="منوی اصلی" || empty($key)){
	em($querychatid,$txt,$messageid,$keykarbar);
	//sm($qid,$txt,$keykarbar);
	}else{
		if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($key);
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								em($querychatid,$txt,$messageid,$keykar);
								sm($qid,$txt,$keykar);
		}
}elseif(getDokmenok($querydata)=="newdokme"){
								$get = json_decode(getDokmetext($querydata),true);
							if(!empty(getTextersal($querydata))){
								$tet=getTextersal($querydata);
								$ch13 = str_text($tet,2);
$txt=$ch13;
								}else{
								$txt="یک دکمه را انتخاب کنید";
								}
								if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($querydata);
							$getall = getallvalue("dok","dokme");
		foreach($getall as $ke){
			if(getvalue("dok","dokme",$ke,"hidden")=="on"){
				$key=str_replace('"'.$ke.'"','""',$key);
				}
			}
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								em($querychatid,$txt,$messageid,$keykar);
								//sm($querychatid,$txt,$keykar);
							}
							ToDie();
			}
elseif(!empty(getDokme($querydata))){
		if(getLockchannel($querydata)=="on"){
	$user = getDokuser($querydata);
	$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$querychatid"),true);
			$status = $url2["result"]["status"];
			if($status != "creator" && $status != "administrator" && $status !="member"){
	$txtt=getTextlock($querydata);
	$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
			
		if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($querychatid,$ch13,$kei);
			ToDie();
			}
	}	elseif(getLockcoin($querydata)=="on"){
		if((empty(getadmin($qid)) && $qid !=$admin)){
			if(getDoktedad($querydata) > getCoin($qid)){
					$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($qid,$ch13,$kei);
			ToDie();
				}else{
					$man = getDoktedad($querydata);
					$coin=getCoin($qid);
					$cv = $coin - $man;
					setCoin($qid,$cv);
					}
			}
			
			}elseif(getvalue("dok","dokme",$querydata,"lockemtiaz2")=="on"){
		if((empty(getadmin($qid)) && $qid !=$admin)){
			if(getDoktedad($querydata) > getCoin($qid)){
					$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($qid,$ch13,$kei);
			ToDie();
				}
			}
			
			}elseif(getvalue("dok","dokme",$querydata,"lockday")=="on"){
			if((empty(getadmin($qid)) && $qid !=$admin)){
			if(date("d")==getvalue("user","chatid",$qid,"dayl")){
				$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($qid,$ch13,$kei);
			ToDie();
				}else{
					setvalue("user","chatid",$qid,"dayl",date("d"));
					}
			}
		}elseif(getvalue("dok","dokme",$querydata,"lockcode")=="on" && $step != "getcode"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getcode");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	sm($qid,$ch13,$kei);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"lockshomare")=="on" && $step != "getshomare"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getshomare");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($qid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"lockshomare2")=="on" && $step != "getshomare2"){
if((empty(getadmin($qid)) && $qid !=$admin)){
	setOther2($qid,$querydata);
	step($qid,"getshomare2");
	$txtt=getvalue("dok","dokme",$querydata,"textlock2");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
	$keyback=json_encode([
	'keyboard'=>[
	[["text"=>$dokm,"request_contact"=>true]],
	[['text'=>"برگشت↪"]]
	],
	'resize_keyboard'=>true,
	'one_time_keyboard'=>true,
	'selective'=>true
	]);
			sm($qid,$ch13,$keyback);
			ToDie();
	}
}elseif(getvalue("dok","dokme",$querydata,"locknafar")=="on"){
	if((empty(getadmin($chatid)) && $chatid !=$admin)){
	$num = getvalue("dok","dokme",$querydata,"numnafar");
	$fin = getvalue("dok","dokme",$querydata,"finishnafar");
if($num >= $fin){
	$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($qid,$ch13,$kei);
			ToDie();
	}else{
		$num = $num + 1;
		$num = setvalue("dok","dokme",$querydata,"numnafar",$num);
	
		}
}
}elseif(getvalue("dok","dokme",$querydata,"locksade")=="on"){
			if((empty(getadmin($qid)) && $qid !=$admin)){
			$txtt=getTextlock($querydata);
		$ch13 = str_text($txtt,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($qid,$ch13,$kei);
			ToDie();
			}
		}
			$text = $querydata;
			$chatid = $querychatid;
			$firstname= $qname;
			$username = $quser;
			$fromid = $qid;
			if(getDokmenok($querydata)=="getApi"){
				step($querychatid,"getApi$querydata");
				setCode($querychatid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
				if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
		}
				}elseif(getDokmenok($querydata)=="jostojo"){
					step($qid,"jostojo$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($qid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="search"){
					step($qid,"search$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($qid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="change"){
					step($qid,"change$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($qid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ایدی عددی فردی که میخواهید برای ان امتیاز ارسال کنید را وارد کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($qid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="enfjar"){
					step($qid,"enfjar$querydata");
					setCode($qid,$querydata);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($qid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="👈 لطفا تعداد سکه ای که میخواهید شرط ببندید را وارد کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($qid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="createbot"){
					step($chatid,"createbot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا توکن ربات را برای ساخت ربات ارسال نمایید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="updatebot"){
					step($chatid,"updatebot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}elseif(getDokmenok($querydata)=="deletebot"){
					step($chatid,"deletebot$querydata");
					setCode($chatid,$querydata);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}
					
elseif(getDokmenok($querydata)=="sendadmin"){
					step($querychatid,"sendadmin$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="schannel"){
					step($querychatid,"schannel$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
					}
					}elseif(getDokmenok($querydata)=="fchannel"){
					step($querychatid,"fchannel$querydata");
					setCode($qid,$querydata);
					if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
					$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
					if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
	$ch13 = str_text($tx,2);
}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
					}
					}
				elseif(getDokmenok($querydata)=="getmatntaki"){
				step($querychatid,"getmatntaki$querydata");
				setCode($qid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
			if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
				$ch13 = str_text($tx,2);
				}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
				}
				}elseif(getDokmenok($querydata)=="getphp"){
				step($querychatid,"getphp$querydata");
				setCode($qid,$querydata);
				if(!empty(getvalue("hashmoh","text",$querydata,"hash"))){
				$drop = getvalue("hashmoh","text",$querydata,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($querychatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,2);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($querychatid,$teext,$keyback);
				}}else{
			if(getTextersal($querydata)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($querydata);
				$ch13 = str_text($tx,2);
				}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($querychatid,$ch13,$keyback);
				}
				}elseif(getDokmenok($querydata)=="coin"){
				$tet=getDokmetext($querydata);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `emtiaz` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `emtiaz` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getCoin($fg);
					if(!empty(getvalue("dok","dokme",$querydata,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$querydata,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($qid,$texxt,$kei);
	
				}elseif(getDokmenok($querydata)=="bartarin"){
				$tet=getDokmetext($querydata);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `zirmaj` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `zirmaj` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getZirmaj($fg);
					if(!empty(getvalue("dok","dokme",$querydata,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$querydata,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($qid,$texxt,$kei);
	
				}elseif(getDokmenok($querydata)=="php"){
     $get=getDokmetext($querydata);
     $array1 = array('$FIRSTNAME','$LASTNAME','$USERNAME','$USERID','$PHONE','$BIO','$IDBOT','$BOTUSER','$BOTNAME','$GPNAME','$GPUSER','$CHATID','$DESCRIOPTION','$GETDICE','$DICE','$MESSAGEID','$COIN','$MEMBER','$LINK','$ALLMEM','$HOUR','$MINUTE','$SECOND','$JOINDATEM','$JOINDATESH','$JOINTIME','$TIME','$YEAR','$MONTH','$DAY','$DATESH','$DATEM','$BOTMEM','$TEXT','$ADD');
 $array2 = array('"'.$qname.'"','"'.$lastname.'"','"'.$quser.'"','"'.$qid.'"','"'.$phone_number.'"','"'.$bio.'"','"'.$idbot.'"','"'.$botuser.'"','"'.$botname.'"','"'.$gpname.'"','"'.$gpuser.'"','"'.$querychatid.'"','"'.$description.'"','"'.getOther($qid).'"','"'.$Message->dice->value.'"','"'.$messageid.'"','"'.getCoin($qid).'"','"'.getZirmaj($qid).'"','"https://t.me/'.$botuser.'?start='.$qid.'"','"'.getDokother2($querydata).'"','"'.date('H').'"','"'.date('i').'"','"'.date('s').'"','"'.getJoindatem($qid).'"','"'.getJoindatesh($qid).'"','"'.getJointime($qid).'"','"'.date("H:i:s").'"','"'.date("Y").'"','"'.date("m").'"','"'.date("d").'"','"'.$datesh.'"','"'.$datem.'"','"'.amarcount("user").'"','"'.$querychatid.'"',"");
 $code=str_replace($array1,$array2,$get);
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://rextester.com/rundotnet/Run");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"LanguageChoice=8&Program=$code&CompilerArgs=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
$ge=json_decode($server_output,true);
if(!empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }elseif(empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }else{
$sg =   sm($chatid,$ge["Errors"]);
  }
  

$messageid= $sg->result->message_id;
     if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
     }
	elseif(getDokmenok($querydata)=="matntaki"){
		
	$get = json_decode(getDokmetext($querydata),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	$ch13 = str_text($capp,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$texxt = $hi[0];
		$kei = textToinline("%$k%",$texxt);
		}
		$sg = sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			$sg = ss($querychatid,$tet);
			}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg=sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
	}elseif(getDokmenok($querydata)=="matntartib"){
		$get = json_decode(getDokmetext($querydata),true);
		step($querychatid,"matntartib$querydata");
				setCode($querychatid,$querydata);
				if(empty(getvalue("dok","dokme",$querydata,"textemtiaz1"))){
					$dokmee="پست بعدی⏩";
					}else{
						$dokmee=getvalue("dok","dokme",$querydata,"textemtiaz1");
						}
						if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
									$keybo=json_encode([
									'keyboard'=>[
									[["text"=>$dokmee]],
									[["text"=>$back]]
									],
									'resize_keyboard'=>true,
									]);
									setvalue("dok","dokme",$querydata,"textemtiaz2",0);
						$nok=$get[0];
						$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,2);
		$capp=str_replace("/r/n/r","\n",$capp);
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,2);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($querychatid,$ch13,$keybo);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$capp,$keybo);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$capp,$keybo);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$capp,$keybo);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$capp,$keybo);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet,$keybo);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$capp);
		sm($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		sm($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$capp);
			sm($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="dice"){
			
			$sg = sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			sm($querychatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$capp,$keybo);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}elseif(getDokmenok($querydata)=="matnchand"){
		$get = json_decode(getDokmetext($querydata),true);
		$x = 0;
foreach ($get as $nok){
	if($x==10){
	sleep(1);
$x=0;	
	}
	$x++;
$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	$capp = str_text($capp,2);
$capp=str_replace("/r/n/r","\n",$capp);
	
if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$capp);
	$capp = $hi[0];
		$kei = textToinline("%$k%",$capp);
		}
if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$texxt = $hi[0];
		$kei = textToinline("%$k%",$texxt);
		}
$sg = sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$capp,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$capp,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$capp,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$capp,$kei);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$capp);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$capp);
			}elseif($type=="dice"){
			$sg=sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$capp,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
}
		}elseif(getDokmenok($querydata)=="matnrand"){
		$get = json_decode(getDokmetext($querydata),true);
$cou = count($get)-1;
$rand=rand(0,$cou);
$type = $get[$rand]['type'];
	$tet = $get[$rand]['text'];
	$capp = $get[$rand]['caption'];
	$ch13 = str_text($capp,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$cap = $hi[0];
		$kei = textToinline("%$k%",$cap);
		}
if($type=="text"){
			$ch13 = str_text($tet,2);
$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$texxt = $hi[0];
		$kei = textToinline("%$k%",$texxt);
		}

	$sg = sm($querychatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($querychatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($querychatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($querychatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($querychatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($querychatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($querychatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($querychatid,$tet);
		}elseif($type=="location"){
			$sg = slo($querychatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($querychatid,$tet);
			$get = $sg->result->dice->value;
			setOther($qid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($querychatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}elseif(getDokmenok($querydata)=="Api"){
							$gget = json_decode(getDokmetext($querydata),true);
				$get=$gget['text'];
				if(strpos($get,"||")){
					$exp=explode("||",$get);
				$get =$exp[0];
					$cap=$exp[1];
    $cap13 = str_text($cap,2);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
  } 
}
$txtt = $get;
			$ch13 = str_text($txtt,2);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
if(validImage($ch13)=="image"){
							if(strpos(typee($ch13),"gif")!==false){
							$sg = sd($qid,$ch13,$cap13,$kei);
							}else{
							$sg = sp($qid,$ch13,$cap13,$kei);
							}
							
							}elseif(validImage($ch13)=="audio"){
$sg = sa($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="video"){
$sg = sv($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="audio"){
$sg = sa($qid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="application"){
	if(strpos(typee($ch13),"json")!==false){
		$xc = file_get_contents($ch13);
						$sg = sm($querychatid,$xc.$cap13,$kei);
		}else{
$sg = sd($qid,$ch13,$cap13,$kei);
}
}else{
	if(getimagesize("$ch13")==true){
		$sg = sp($qid,$ch13,$cap13,$kei);
		}else{
								$xc = file_get_contents($ch13);
						$sg = sm($querychatid,$xc.$cap13,$kei);
						}
						}
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
		}
elseif(getDokmenok($querydata)=="rss"){
							$get = json_decode(getDokmetext($querydata),true);
							$tbbt=$get['text'];
							
		if(preg_match("/(%)([^\']+)(%)/",$tbbt,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$tbbt);
	$tbbt = $hi[0];
		$kei = textToinline("%$k%",$tbbt);
		}
						$xc = getrss($tbbt);
						$sg = sm($querychatid,$xc,$kei);
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$querydata,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$querydata','$now'");
					}
						}elseif(getDokmenok($querydata)=="back"){
							if(!empty(getTextersal($querydata))){
								$tet = getTextersal($querydata);
								$ch13 = str_text($tet,2);
$txt=$ch13;
								}else{
								$txt="به عقب برگشتید :";
								}
$key = getvalue("dok","dokme",$querydata,"keyback");
if($key=="منوی اصلی" || empty($key)){
	sm($qid,$txt,$keykarbar);
	}else{
		if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($key);
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								sm($qid,$txt,$keykar);
		}
}elseif(getDokmenok($querydata)=="newdokme"){
								$get = json_decode(getDokmetext($querydata),true);
							if(!empty(getTextersal($querydata))){
								$tet=getTextersal($querydata);
								$ch13 = str_text($tet,2);
$txt=$ch13;
								}else{
								$txt="یک دکمه را انتخاب کنید";
								}
								if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($querydata);
							$getall = getallvalue("dok","dokme");
		foreach($getall as $ke){
			if(getvalue("dok","dokme",$ke,"hidden")=="on"){
				$key=str_replace('"'.$ke.'"','""',$key);
				}
			}
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								sm($querychatid,$txt,$keykar);
							}
				ToDie();			
			}else{
				if(strpos($querydata,"NOTIF")!==false){
					$querydata=str_replace("NOTIF","",$querydata);
		alert($queryid,$querydata,false);	
		ToDie();
		}else{
			alert($queryid,$querydata);	
		ToDie();
			}
			}
		}
		
		
		if($type == "supergroup" or $type=="group"){
			if(getvalue("data","id",2,"leavegroup")=="on"){
			$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res == "creator" or $res =="administrator" or $fromid==$admin){
if(preg_match('/^[\/\#\!\^]?(antilink off)$/i',$text)){
	setDeletelink("off");
	sm($chatid,"ضدلینک با موفقیت خاموش شد✅");
	ToDie();
	}elseif(preg_match('/^[\/\#\!\^]?(antilink on)$/i',$text)){
	setDeletelink("on");
	sm($chatid,"ضدلینک با موفقیت روشن شد✅");
	ToDie();
	}elseif(preg_match('/^[\/\#\!\^]?(antiusername off)$/i',$text)){
	setvalue("data","id",2,"deletelink","off");
	sm($chatid,"ضدیوزرنیم با موفقیت خاموش شد✅");
	ToDie();
	}elseif(preg_match('/^[\/\#\!\^]?(antiusername on)$/i',$text)){
	setvalue("data","id",2,"deletelink","on");
	sm($chatid,"ضدیوزرنیم با موفقیت روشن شد✅");
	ToDie();
	}elseif(preg_match('/^[\/\#\!\^]?(antiforward off)$/i',$text)){
	setvalue("data","id",2,"newozv","off");
	sm($chatid,"ضدفوروارد با موفقیت خاموش شد✅");
	ToDie();
	}elseif(preg_match('/^[\/\#\!\^]?(antiforward on)$/i',$text)){
	setvalue("data","id",2,"newozv","on");
	sm($chatid,"ضدفوروارد با موفقیت روشن شد✅");
	ToDie();
	}
				}
}
}
$blocklist = getvalue("blocklist","chatid",$chatid,"chatid");
if(!empty($blocklist)){
if(isetval("blocklist","chatid",$chatid,"chatid") !==false || isetval("blocklist","chatid","@$username","chatid") !==false){
//step($chatid,"blocked"); 

  $key=json_encode([
       "hide_keyboard"=>true
       ]);
       if(empty(getvalue("data","id",1,"txtblock"))){
       sm($chatid,"شما از ربات بلاک شدید⛔",$key);
       }else{
       	$txt = getvalue("data","id",1,"txtblock");
       $txt = str_text($txt,1);
       	sm($chatid,$txt,$key);
       }
 ToDie();
 }}
 if(empty(getadmin($chatid)) && $chatid != $admin){
if(getvalue("data","id",1,"mahdod")=="on"){ 
if(empty(getvalue("mahdod","chatid",$chatid,"chatid"))){
//step($chatid,"blocked"); 
  $key=json_encode([
       "hide_keyboard"=>true
       ]);      
       sm($chatid,"شما اجازه دسترسی به این ربات را ندارید !!!⛔",$key);
 ToDie();
 }
 }
}
if(preg_match("/^(.*)([Hh]ttp|[Hh]ttps|t.me)(.*)|([Hh]ttp|[Hh]ttps|t.me)(.*)|(.*)([Hh]ttp|[Hh]ttps|t.me)|(.*)[Tt]elegram.me(.*)|[Tt]elegram.me(.*)|(.*)[Tt]elegram.me|(.*)[Tt].me(.*)|[Tt].me(.*)|(.*)[Tt].me/",$text) && getDeletelink()=="on" && $type !="private"){
preg_match("/^(.*)([Hh]ttp|[Hh]ttps|t.me)(.*)|([Hh]ttp|[Hh]ttps|t.me)(.*)|(.*)([Hh]ttp|[Hh]ttps|t.me)|(.*)[Tt]elegram.me(.*)|[Tt]elegram.me(.*)|(.*)[Tt]elegram.me|(.*)[Tt].me(.*)|[Tt].me(.*)|(.*)[Tt].me/",$text,$match);
$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res != "creator" && $res !="administrator"){
dm($chatid,$messageid);
ToDie();
}
}
if(preg_match("/^(.*)([Hh]ttp|[Hh]ttps|t.me)(.*)|([Hh]ttp|[Hh]ttps|t.me)(.*)|(.*)([Hh]ttp|[Hh]ttps|t.me)|(.*)[Tt]elegram.me(.*)|[Tt]elegram.me(.*)|(.*)[Tt]elegram.me|(.*)[Tt].me(.*)|[Tt].me(.*)|(.*)[Tt].me/",$caption) && getDeletelink()=="on" && $type !="private"){
preg_match("/^(.*)([Hh]ttp|[Hh]ttps|t.me)(.*)|([Hh]ttp|[Hh]ttps|t.me)(.*)|(.*)([Hh]ttp|[Hh]ttps|t.me)|(.*)[Tt]elegram.me(.*)|[Tt]elegram.me(.*)|(.*)[Tt]elegram.me|(.*)[Tt].me(.*)|[Tt].me(.*)|(.*)[Tt].me/",$caption,$match);
$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res != "creator" && $res !="administrator"){
dm($chatid,$messageid);
ToDie();
}
}
if(preg_match('/\@(.*)/',$text) && getvalue("data","id",2,"deletelink")=="on" && $type !="private"){
$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res != "creator" && $res !="administrator"){
dm($chatid,$messageid);
ToDie();
}
}
if(preg_match('/\@(.*)/',$caption) && getvalue("data","id",2,"deletelink")=="on" && $type !="private"){
$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res != "creator" && $res !="administrator"){
dm($chatid,$messageid);
ToDie();
}
}
if(isset($forward) && getvalue("data","id",2,"newozv")=="on" && $type !="private"){
$get = bot('getChatMember',['chat_id'=>$chatid,'user_id'=>$fromid]);
$res = $get->result->status;
if($res != "creator" && $res !="administrator"){
dm($chatid,$messageid);
ToDie();
}
}
if(isset($newmember) && getNewozv() == "on"){
	$txt = getTxtnewozv();
	$cap13 = str_text($txt,1);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
}
sm($chatid,$cap13);
ToDie();
	}
	if($step=="getcode"){
		$vb=getOther2($fromid);
		if($text=="برگشت↪"){
			
			sm($chatid,"به عقب برگشتید",$keykarbar);
			step($chatid,"");
			ToDie();
			}else{
				if(!empty(getvalue("code","code",$text,"code")) && getvalue("code","code",$text,"dokme")==$vb){
					$tedad = getvalue("code","code",$text,"tedad");
					$vaz = getvalue("code","code",$text,"vaz");
					$nafar = getvalue("code","code",$text,"nafar");
					$tex = getvalue("code","code",$text,"text");
					if($nafar < $tedad && $vaz == "on"){
						$anjam = $nafar + 1;
						setvalue("code","code",$text,"nafar",$anjam);
						if(!empty(getvalue("data","id",1,"opencodetext"))){
							$txt=getvalue("data","id",1,"opencodetext");
							$cap13 = str_text($txt,1);
$txtopen=$cap13;
							}else{
								$txtopen="دکمه باز شد✅";
								}
						sm($chatid,$txtopen,$keykarbar);
						step($chatid,"");
						$keybb=json_encode([
							"hide_keyboard"=>true
							]);
						$text=$vb;
						}else{
							
							$txtt=$tex;
							setvalue("code","code",$text,"vaz","off");
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($chatid,$ch13,$keykarbar);
		step($chatid,"");
		ToDie();
							}
					}else{
						$txtt=getTextlock($vb);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}
		sm($chatid,$ch13,$kei);
		ToDie();
		
						}
				}
		}
if($step=="getshomare"){
		$vb=getOther2($fromid);
		if($text=="برگشت↪"){
			
			sm($chatid,"به عقب برگشتید",$keykarbar);
			step($chatid,"");
			ToDie();
			}else{
				if(isset($Message->contact) && isset($Message->reply_to_message) && $fromid== $Message->contact->user_id){
					
					step($chatid,"");
					setvalue("user","chatid",$chatid,"phone",$Message->contact->phone_number);
					if(!empty(getvalue("data","id",2,"opencodetext"))){
							$txt=getvalue("data","id",2,"opencodetext");
							$cap13 = str_text($txt,1);
$txtopen=$cap13;
							}else{
								$txtopen="شماره تلفن تایید و دکمه باز شد✅";
								}
					sm($chatid,$txtopen,$keykarbar);
						step($chatid,"");
						$keybb=json_encode([
							"hide_keyboard"=>true
							]);
						$text=$vb;
					
}else{
						$txtt=getTextlock($vb);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]],
				[["text"=>"برگشت↪"]]
				],
				'resize_keyboard'=>true
				]);
			sm($chatid,$ch13,$ke);
			ToDie();
						}
				}
				}
				
				if($step=="getshomare2"){
		$vb=getOther2($fromid);
		if($text=="برگشت↪"){
			
			sm($chatid,"به عقب برگشتید",$keykarbar);
			step($chatid,"");
			ToDie();
			}else{
				if(isset($Message->contact) && isset($Message->reply_to_message) && $fromid== $Message->contact->user_id){
					$conta = $Message->contact->phone_number;
						if(preg_match('/^\+?98[0-9]+$/',$conta)){
							step($chatid,"");
					setvalue("user","chatid",$chatid,"phone",$Message->contact->phone_number);
					if(!empty(getvalue("data","id",2,"opencodetext"))){
							$txt=getvalue("data","id",2,"opencodetext");
							$cap13 = str_text($txt,1);
$txtopen=$cap13;
							}else{
								$txtopen="شماره تلفن تایید و دکمه باز شد✅";
								}
					sm($chatid,$txtopen,$keykarbar);
						step($chatid,"");
						$keybb=json_encode([
							"hide_keyboard"=>true
							]);
						$text=$vb;
							}else{
								$txtt=getTextlock($vb);
		$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
$dokm = "📞درخواست شماره";
if(!empty(getvalue("data","id",1,"dokphone"))){
	$dokm=getvalue("data","id",1,"dokphone");
	}
				$ke=json_encode([
				'keyboard'=>[
				[["text"=>$dokm,"request_contact"=>true]],
				[["text"=>"برگشت↪"]]
				],
				'resize_keyboard'=>true
				]);
			sm($chatid,$ch13,$ke);
			ToDie();
								}
					}
				}
				}
	$ch = getCode($fromid);	
	if($text=="/start"){
		if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($chatid,$userid,$messid);
				}
			}
				$key=json_encode([
							"hide_keyboard"=>true
							]);
							
			
			
			
			$txtt = getStartmessage();
			$ch13 = str_text($txtt,1);
sm($chatid,$ch13,$keykarbar);
step($chatid,"");
}elseif(!empty(getvalue("pasokh","pasokh",$text,"pasokh")) && getvalue("data","id",1,"autoanswer")=="on" && empty($step)){
					$get = json_decode(getvalue("pasokh","pasokh",$text,"javab"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	if(strpos($tet,"EDITMSG")){
		$ex=explode("EDITMSG",$tet);
		$tet=$ex[0];
		$edit=$ex[1];
		}
		
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	if(isset($edit)){
		$sd = $edit;
		$vb=str_replace("(","",$sd);
		$vb=str_replace(")","",$vb);
		$exp=explode(",",$vb);
		$x=1;
		foreach($exp as $key){
			$ch13 = str_text($key,1);
			em($chatid,$ch13,$messageid+1);
			}
		}
	}elseif($type=="photo"){
		sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			ss($chatid,$tet);
			}elseif($type=="contact"){
		sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		svin($chatid,$tet);
		}elseif($type=="location"){
			slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$gett = sdi($chatid,$tet);
			$get = $gett->result->dice->value;
			setOther($fromid,$get);
			$em= $gett->result->dice->emoji;
			setvalue("user","chatid",$qid,"emdice",$em);
			}elseif($type=="document"){
			sd($chatid,$tet,$cap,$kei);
			}
			}
	elseif($step=="sendadmin$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,2);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$drop = getvalue("hashmoh","text",$ch,"hash");
			$ted = getOther($chatid);
			$sql = "CREATE TABLE `get$chatid$userbott`(`dokme` TEXT,`text` TEXT)";
			mysqli_query($con,$sql);
			if(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="addad" && !preg_match('/^[0-9]+$/',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="link" && !preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="number" && !preg_match('/^(?:09|\+?63|\+?98|\+?1)(?:\d(?:-)?){9,10}$/m',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="english" && !preg_match('/^[a-z0-9 .\-]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="farsimatn" && !preg_match('/^[پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="hamematn" && !preg_match('/^[a-zA-Z0-9ضصقفغعهخحجشسیبلاتنمکظطدزروچپگژآأإء\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="englishandnumber" && !preg_match('/^[a-zA-Z0-9\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="forward" && !isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="noforward" && isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="username" && !preg_match('/^\@[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dastor" && !preg_match('/^\/[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="email" && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="ball" && $dice !="⚽"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="basket" && $dice !="🏀"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="boling" && $dice !="🎳"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="eslat" && $dice !="🎰"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dart" && $dice !="🎯"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dice" && $dice !="🎲"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="photo" && !isset($photo)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="film" && !isset($video)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="audio" && !isset($audio)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="sticker" && !isset($sticker)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="video_note" && !isset($video_note)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="contact" && !isset($contact_number)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="location" && !isset($long_location)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}
			
			$ted2 = $ted+1;
			insert("get$chatid","`dokme`,`text`","'TEXT_$ted','$text'");
			if(!empty(getvalue("mohtava$drop","dokme","TEXT_$ted2","dokme"))){
				$teext=getvalue("mohtava$drop","dokme","TEXT_$ted2","textget");
				setOther($chatid,$ted2);
				sm($chatid,$teext);
}else{
			
				if(getTextresid($ch)==null){
					
					
		$ch13="پیام شما ارسال شد✅";
		}else{
	$txtt = getTextresid($ch);
			$ch13 = str_text($txtt,1);
		}
			
			sm($chatid,$ch13,$keykarbar);
			step($chatid,"");
			$tttm = "";
			if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$tttm .= "$bh => $vn\n";
			}
			}
			if(empty(getvalue("data","id",1,"maxwarn"))){
				$maxwarn = 4;
				}else{
					$maxwarn = getvalue("data","id",1,"maxwarn");
					}
					if(empty(getvalue("user","chatid",$chatid,"tedadwarn"))){
				$tedadwarn = 0;
				}else{
					$tedadwarn = getvalue("user","chatid",$chatid,"tedadwarn");
					}
					
			$keym=json_encode([
			'inline_keyboard'=>[
			[["text"=>"مشخصات فرد👦",'callback_data'=>"etlaat$chatid"]],
			[["text"=>"جواب دادن↪","callback_data"=>"Javab$chatid"],["text"=>"بلاک کردن⛔",'callback_data'=>"block$chatid"]],
			[["text"=>"➖","callback_data"=>"manfiwarn$chatid"],["text"=>"$tedadwarn/$maxwarn","callback_data"=>"تعداد اخطار ها"],["text"=>"➕","callback_data"=>"addwarn$chatid"]],
			[["text"=>"خوانده شدن و بستن کیبورد👁","callback_data"=>"reading$chatid-.-$messageid"]],
			]
			]);
			$txt="
			📨یک پیام دریافت شد

⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️اسم دکمه : $ch
⭐️متن پیام : 👇👇👇\n$tttm";
bot('sendMessage',[
        'chat_id'=>$admin,
        'text' =>$txt,
        'parse_mode'=>"HTML",
      'reply_markup'=>$keym
        ]);
			$arr = getallvalue("admin","chatid");
			fm($admin,$fromid,$messageid);
			foreach($arr As $key){
   bot('sendMessage',[
        'chat_id'=>$key,
        'text' =>$txt,
        'parse_mode'=>"HTML",
      'reply_markup'=>$keym
        ]);
   fm($key,$chatid,$messageid);
   }
      if(getvalue("data","id",1,"sendtomail")=="on"){
   	if(!empty(getvalue("data","id",2,"sendtomail"))){
   	$to = getvalue("data","id",2,"sendtomail");
    $subject = "یک پیام از ربات @$userbott";
    $message = "$txt\n$text";
    $message = wordwrap($message, 70, "\r\n");
    $header = "From:sender@creat.ir \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";
    
   mail ($to,$subject,$message,$header);
  }
  }
   $sql = "DROP TABLE `get$chatid$userbott`";
			mysqli_query($con,$sql);
				}
				}
					}elseif($step=="fchannel$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if(!empty(getvalue("dok","dokme",$ch,"qoflforward")) && getvalue("dok","dokme",$ch,"qoflforward")=="on" && isset($forward)){
				if(empty(getvalue("dok","dokme",$ch,"qoflforwardtext"))){
				$txt = "متن فورواردی ممنوع میباشد⛔";
				}else{
					$txt = getvalue("dok","dokme",$ch,"qoflforwardtext");
					}
					sm($chatid,$txt);
					ToDie();
				}
				if(getTextresid($ch)==null){
					
					
		$ch13="محتوای شما به کانال فوروارد شد✅";
		}else{
	$txtt = getTextresid($ch);
			$ch13 = str_text($txtt,1);
		}
			
			sm($chatid,$ch13,$keykarbar);
			step($chatid,"");
			if(empty(getvalue("dok","dokme",$ch,"channel"))){
			$txt="⛔بخاطر ست نکردن کانال پیام کاربر برای شما ارسال شد\n\n
⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️اسم دکمه : $ch
⭐️متن پیام : 👇👇👇";

        bot('sendmessage',[
        "chat_id"=>$admin,
        "text"=>$txt,
        "parse_mode"=>"HTML"
        ]);
   fm($admin,$chatid,$messageid);
   }else{
   $id=getvalue("dok","dokme",$ch,"channel");
	$gg = bot('forwardMessage',[
	'chat_id'=>$id,
	'from_chat_id'=>$chatid,
	'message_id'=>$messageid
	]);
   $chmessage = $gg->result->message_id;
									if(getvalue("dok","dokme",$ch,"emza")=="on"){
										$getchannel=getvalue("dok","dokme",$ch,"emzatext");
										$getchannel = str_text($getchannel,1);
$getchannel=str_replace("/r/n/r","\n",$getchannel);
	
	if(preg_match("/(%)([^\']+)(%)/",$getchannel,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$getchannel);
 $getchannel = $hi[0];
  $kei = textToinline("%$k%",$getchannel);
  }
										bot('sendmessage',[
									'chat_id'=>$id,
									'text'=>$getchannel,
									'reply_to_message_id'=>$chmessage,
									'reply_markup'=>$kei,
									'parse_mode'=>"HTML"
									]);
										}
   }
				}
}elseif($step=="schannel$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$drop = getvalue("hashmoh","text",$ch,"hash");
			$ted = getOther($chatid);
			$sql = "CREATE TABLE `get$chatid$userbott`(`dokme` TEXT,`text` TEXT)";
			mysqli_query($con,$sql);
			if(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="addad" && !preg_match('/^[0-9]+$/',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="link" && !preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="number" && !preg_match('/^(?:09|\+?63|\+?98|\+?1)(?:\d(?:-)?){9,10}$/m',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="english" && !preg_match('/^[a-z0-9 .\-]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="farsimatn" && !preg_match('/^[پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="hamematn" && !preg_match('/^[a-zA-Z0-9ضصقفغعهخحجشسیبلاتنمکظطدزروچپگژآأإء\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="englishandnumber" && !preg_match('/^[a-zA-Z0-9\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="forward" && !isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="noforward" && isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="username" && !preg_match('/^\@[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dastor" && !preg_match('/^\/[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="email" && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="ball" && $dice !="⚽"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="basket" && $dice !="🏀"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="boling" && $dice !="🎳"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="eslat" && $dice !="🎰"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dart" && $dice !="🎯"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dice" && $dice !="🎲"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="photo" && !isset($photo)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="film" && !isset($video)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="audio" && !isset($audio)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="sticker" && !isset($sticker)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="video_note" && !isset($video_note)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="contact" && !isset($contact_number)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="location" && !isset($long_location)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}
			
			$ted2 = $ted+1;
			insert("get$chatid","`dokme`,`text`","'TEXT_$ted','$text'");
			if(!empty(getvalue("mohtava$drop","dokme","TEXT_$ted2","dokme"))){
				$teext=getvalue("mohtava$drop","dokme","TEXT_$ted2","textget");
				setOther($chatid,$ted2);
				$teext= str_text($teext,1);
				sm($chatid,$teext);
}else{
			
				if(getTextresid($ch)==null){
					
					
		$ch13="محتوای شما به کانال ارسال شد✅";
		}else{
	$txtt = getTextresid($ch);
			$ch13 = str_text($txtt,1);
		}
			
			sm($chatid,$ch13,$keykarbar);
			step($chatid,"");
			if(empty(getvalue("dok","dokme",$ch,"channel"))){
				if(empty(getvalue("dok","dokme",$ch,"textchannel"))){
			$txt="⛔بخاطر ست نکردن کانال پیام کاربر برای شما ارسال شد
		
⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️اسم دکمه : $ch
⭐️متن پیام : 👇👇
";
        bot('sendmessage',[
        "chat_id"=>$admin,
        "text"=>$txt,
        "parse_mode"=>"HTML"
        ]);
   fm($admin,$chatid,$messageid);
   }else{
   $txt="⛔بخاطر ست نکردن کانال پیام کاربر برای شما ارسال شد
		
⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️اسم دکمه : $ch
⭐️متن پیام : 👇👇
";
        bot('sendmessage',[
        "chat_id"=>$admin,
        "text"=>$txt,
        "parse_mode"=>"HTML"
        ]);
        $txtt=getvalue("dok","dokme",$ch,"textchannel");
   $ch13 = str_text($txtt,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  }
  if(isset($photo)){
			sp($admin,$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($admin,$video,$caption,$kei);
				}elseif(isset($document)){
					sd($admin,$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($admin,$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($admin,$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($admin,$sticker);
								}elseif(isset($video_note)){
								svin($admin,$video_note);
								}elseif(isset($contact_number)){
								sco($admin,$contact_number,$contact_name);
								}elseif(isset($long_location)){
								slo($admin,$long_location,$lat_location);
								}elseif(isset($dice)){
								$cl = sdi($admin,$dice);
								setDice($chatid,$cl->result->dice->value);
								}elseif(isset($text)){
									bot('sendmessage',[
									'chat_id'=>$admin,
									'text'=>$ch13,
									'reply_markup'=>$kei,
									'parse_mode'=>"HTML"
									]);
	}
	
		}
   }else{
   	$chnel=getvalue("dok","dokme",$ch,"channel");
   	 if(empty(getvalue("dok","dokme",$ch,"textchannel"))){
			if(isset($photo)){
			sp($chnel,$photo,$caption);
			}elseif(isset($video)){
				sv($chnel,$video,$caption);
				}elseif(isset($document)){
					sd($chnel,$document,$caption);
					}elseif(isset($audio)){
						sa($chnel,$audio,$caption);
						}elseif(isset($voice)){
							svo($chnel,$voice,$caption);
							}elseif(isset($sticker)){
								ss($chnel,$sticker);
								}elseif(isset($video_note)){
								svin($chnel,$video_note);
								}elseif(isset($contact_number)){
								sco($chnel,$contact_number,$contact_name);
								}elseif(isset($long_location)){
								slo($chnel,$long_location,$lat_location);
								}elseif(isset($dice)){
								$cl = sdi($chnel,$dice);
								setDice($chatid,$cl->result->dice->value);
								}elseif(isset($text)){
									
									$gg = bot('sendmessage',[
									'chat_id'=>$chnel,
									'text'=>$text,
									'parse_mode'=>"HTML"
									]);
									}
									$chmessage = $gg->result->message_id;
									if(getvalue("dok","dokme",$ch,"emza")=="on"){
										$getchannel=getvalue("dok","dokme",$ch,"emzatext");
										$getchannel = str_text($getchannel,1,$chnel);
$getchannel=str_replace("/r/n/r","\n",$getchannel);
	
	if(preg_match("/(%)([^\']+)(%)/",$getchannel,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$getchannel);
 $getchannel = $hi[0];
  $kei = textToinline("%$k%",$getchannel);
  }
										bot('sendmessage',[
									'chat_id'=>$chnel,
									'text'=>$getchannel,
									'reply_to_message_id'=>$chmessage,
									'reply_markup'=>$kei,
									'parse_mode'=>"HTML"
									]);
										}
									}else{
										$chnel=getvalue("dok","dokme",$ch,"channel");

										$txtt=getvalue("dok","dokme",$ch,"textchannel");
										
					if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$txtt = str_replace($bh,$vn,$txtt);
			}
			}
				$ch13 = str_text($txtt,1,$chnel);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  }
  $chnel=getvalue("dok","dokme",$ch,"channel");
										$gg = bot('sendmessage',[
									'chat_id'=>$chnel,
									'text'=>$ch13,
									'reply_markup'=>$kei,
									'parse_mode'=>"HTML"
									]);
									
									$chmessage = $gg->result->message_id;
									if(getvalue("dok","dokme",$ch,"emza")=="on"){
										
										$getchannel=getvalue("dok","dokme",$ch,"emzatext");
										if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$getchannel = str_replace($bh,$vn,$getchannel);
			}
			}
										$getchannel = str_text($getchannel,1);
$getchannel=str_replace("/r/n/r","\n",$getchannel);
	
	if(preg_match("/(%)([^\']+)(%)/",$getchannel,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$getchannel);
 $getchannel = $hi[0];
  $kei = textToinline("%$k%",$getchannel);
  }
										bot('sendmessage',[
									'chat_id'=>$chnel,
									'text'=>$getchannel,
									'reply_to_message_id'=>$chmessage,
									'reply_markup'=>$kei,
									'parse_mode'=>"HTML"
									]);
										}
									$sql = "DROP TABLE `get$chatid$userbott`";
			mysqli_query($con,$sql);
										}  
  } 
				}
				}
}elseif($step=="createbot$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$coin = getvaluee("user","chatid",$admin,"emtiaz");
if($coin > 19){
	if(preg_match('/^[0-9]+\:[a-zA-Z0-9\_\-]+$/i',$text)){
										$url="https://api.telegram.org/bot$text/getme";	
										$text = str_replace("function","",$text);
										$text = str_replace("json","",$text);
										$text = str_replace("file","",$text);
										$text = str_replace("curl","",$text);
										$text = str_replace("text","",$text);
										$text = str_replace("chat","",$text);
										$text = str_replace("php","",$text);
										$text = str_replace("input","",$text);
										$text = str_replace("(","",$text);
										$text = str_replace(")","",$text);
										$get=json_decode(file_get_contents($url),true);
									//	$j=json_encode($get);
									$ok=$get['ok'];
									if($ok!=true){
										$txt=str_text(getvalue("dok","dokme",$ch,"textemtiaz1"),1);
		if(empty($txt)){
			$txt="توکن اشتباه میباشد⛔\n\nلطفا از صحت توکن اطمینان حاصل و سپس دوباره امتحان کنید :";
			}
			if(preg_match("/(%)([^\']+)(%)/",$txt,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$txt);
	$txt = $hi[0];
		$kei = textToinline("%$k%",$txt);
		}
			sm($chatid,$txt,$kei);
										}else{
										$result =$get["result"];
										$username = $result["username"];
										$first_nam = $result["first_name"];
										$getbot = getallvaluee("amarbot","bot");
										if(in_array($username,$getbot)){
											$txt=str_text(getvalue("dok","dokme",$ch,"textemtiaz4"),1);
		if(empty($txt)){
			$txt="این ربات از قبل ساخته شده است⛔";
			}
			if(preg_match("/(%)([^\']+)(%)/",$txt,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$txt);
	$txt = $hi[0];
		$kei = textToinline("%$k%",$txt);
		}
											sm($chatid,$txt,$kei);
											}else{
												
												if(!isetroww("user","keyboard")){
													createroww("user","emtiaz","keyboard","TEXT");
													}
											if(empty(getvaluee("user","chatid",$chatid,"keyboard"))){
											$keytest='[{"text":""}],[{"text":""}]';
											setvaluee("user","chatid",$chatid,"keyboard",$keytest);
												}
										
										$insert = "INSERT INTO `qw$chatid`
										(
										bot,
										token
										) VALUES ('$username','$text')";
										mysqli_query($con,$insert);
										
										$key = getvaluee("user","chatid",$chatid,"keyboard");
										$insert = "INSERT INTO `amarbot`
										(
										bot,
										token,
										creatorid
										
										)
										VALUES ('$username','$text','$chatid')";
										mysqli_query($con,$insert);
										$sqlq = "CREATE TABLE `dok$username`(
										`dokme` TEXT,
										`nok` TEXT,
										`into` TEXT,
										`type` TEXT,
										`editer` TEXT,
										`dastor` TEXT,
										`text` TEXT,
										`keyboard` TEXT,
										`lockchannel` TEXT,
										`lockzirmaj` TEXT,
										`lockcoin` TEXT,
										`textlock` TEXT,
										`textersal` TEXT,
										`textresid` TEXT,
										`user` TEXT,
										`tedad` INT,
										`other` TEXT,
										`other2` TEXT
										)";
										mysqli_query($con,$sqlq);
$sql = "CREATE TABLE `data$username`
(
`id` INT,
startmessage TEXT,
txtback TEXT,
textnewozv TEXT,
textzirmaj TEXT,
eshtebah TEXT,
startpanel TEXT,
keygroup TEXT,
newozv TEXT,
deletelink TEXT,
lockjoin TEXT,
botpower TEXT,
`sendzirmaj` TEXT,
PRIMARY KEY(id),
textpower TEXT,
`keyboard` TEXT,
`other` TEXT,
`other1` TEXT
)";
mysqli_query($con,$sql);

$sqlite = "CREATE TABLE `admin$username`
(
`chatid` INT
)";
mysqli_query($con,$sqlite);

$sqlite = "CREATE TABLE `chan$username`
(
`user` TEXT,
`text` TEXT
)";
mysqli_query($con,$sqlite);


$sqlite = "CREATE TABLE `dayamar$username`
(
`day` INT,
PRIMARY KEY(day),
`amar` INT
)";
mysqli_query($con,$sqlite);
$sql ="INSERT INTO `dayamar$username`(
`day`,
`amar`

) VALUES('".date("d")."','0')";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `user$username` 
 ( 
 chatid INT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid INT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
`coding` INT,
zirmaj INT,
emtiaz INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `group$username`
( 
chatid INT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `channel$username` 
 ( 
 chatid TEXT,
chname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `supergroup$username` 
 ( 
 chatid INT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `blocklist$username` 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);
 

$start = "سلام به ربات من خوش آمدید❤";
$textback = "به عقب برگشتید";
$Eshtebah ="این دستور وجود ندارد!!!";
$newozv = "سلام به گروه خوش آمدید❤";
$adminstart ="سلام مدیر به قسمت مدیریت ربات خوش آمدید🌹\n\n⭐️شما میتوانید از طریق دکمه های زیر ربات خود را مدیریت کنید .\n\n⭐️اگر در روند ویرایش ربات مشکلی داشتید میتوانید به کانال ما مراجعه کنید .\n\n⭐️درصورت تست ربات بصورت کاربر از دستور /start یا از دکمه خروج از پنل استفاده کنید";
$textpower ="ربات خاموش میباشد🛂";
$textzirmaj = "یک زیرمجموعه به شما اضافه شد💥";
$sql = "INSERT INTO `data$username`
(
`id`,
`startmessage`,
`txtback`,
`textnewozv`,
`eshtebah`,
`startpanel`,
`keygroup`,
`newozv`,
`deletelink`,
`lockjoin`,
`botpower`,
`textpower`,
`textzirmaj`,
`sendzirmaj`
)
VALUES ('1','$start','$textback','$newozv','$Eshtebah','$adminstart','off','on','off','off','off','$textpower','$textzirmaj','off')";
mysqli_query($con,$sql);
$key = getvaluee("user","chatid",$chatid,"keyboard");
											$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$username.'"}],[{"text":""}]',$key);
											setvaluee("user","chatid",$chatid,"keyboard",$newdokme);
											
											$co1 = getvaluee("user","chatid",$admin,"emtiaz");
											$co2 = $co1 - 5;
											setvaluee("user","chatid",$admin,"emtiaz",$co2);
											$file=file_get_contents("../../Haji.php");
											$indeht=file_get_contents("../../index.html");
											$mn = preg_replace('/\"(U)SERBOT\"/','"'.$username.'"',$file);
											$ch1 = preg_replace('/\"(A)PITOKENBOT\"/','"'.$text.'"',$mn);
											$ch2 = preg_replace('/\"(A)DMINBOT\"/','"'.$chatid.'"',$ch1);
											
											if(!file_exists("../../BotList")){
											mkdir("../../BotList");		
													}
													if(!file_exists("../../BotList/$username")){
											mkdir("../../BotList/$username");		
													}
													file_put_contents("../../BotList/$username/$username.php",$ch2);
								file_put_contents("../../BotList/$username/index.html",$indeht);
file_put_contents("../../BotList/Robat/index.html",$indeht);
file_put_contents("../../BotList/dokme/index.html",$indeht);
$url2="$domain_url/BotList/$username/$username.php";
													$url3="https://api.telegram.org/bot$text/setwebhook?url=$url2";
													file_get_contents($url3);
													$txr="
												ربات شما با موفقیت ساخته شد ✅

برای رفتن به پنل کاربری اول دستور /start را ارسال کنید سپس دکمه ورود به پنل مدیریت🔧 را ارسال کنید.

اگر مشکلی یا باگی در روند کار شما پیش آمد به ما اطلاع دهید و با دستور /start کار خود را از اول انجام دهید ‼

آموزش های رباتساز در کانال ما موجود است 👇
📣 : @$channel_sazande_id
 	
													";
													$tvt= urlencode($txr);
													file_get_contents("https://api.telegram.org/bot$text/sendmessage?chat_id=$chatid&text=$tvt");
													$ttb=str_text(getvalue("dok","dokme",$ch,"textemtiaz2"),1);
													
		if(empty($ttb)){
			$ttb="ربات شما با آیدی @$username ساخته شد ✅

🤖 توکن ربات شما :
<code>$text</code>

‼ برای شروع رباتتون دستور /start را ارسال کنید.

📣 : @$channel_sazande_id";
			}
			$ttb = str_replace("TOKENUSER",$username,$ttb);
													$ttb = str_replace("TOKENNAME",$first_nam,$ttb);
													step($chatid,"");
													sm($chatid,$ttb,$keykarbar);
													$send = "یک ربات ساخته شد✅\n\nCreated With @$userbott\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$username\n\nToken :\n$text";
													file_get_contents("https://api.telegram.org/bot$token_pedar/sendMessage?parse_mode=html&chat_id=$channel_token_id&text=".urlencode($send));
											
											}
										}
										
	}
			}else{
		$txt=str_text(getvalue("dok","dokme",$ch,"textemtiaz3"),1);
		if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
			$txt="در حال حاضر امکان ساخت ربات غیرفعال میباشد⛔";
			}
			if(preg_match("/(%)([^\']+)(%)/",$txt,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$txt);
	$txt = $hi[0];
		$kei = textToinline("%$k%",$txt);
		}
		sm($chatid,$txt,$kei);
			}
}
}
elseif($step=="deletebot2$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if($text=="بله✅" or (!empty(getvalue("dok","dokme",$ch,"textemtiaz4")) && $text==getvalue("dok","dokme",$ch,"textemtiaz4"))){
				$user = getvaluee("user","chatid",$chatid,"Other");
																	$files = glob("../../BotList/$user/*"); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }else{
	rmdir($file);
}
}
rmdir("../../BotList/$user");
deletevaluee("qw$chatid","bot",$user);
deletevaluee("amarbot","bot",$user);
	
											$key = getvaluee("user","chatid",$chatid,"keyboard");
											if(strpos($key,'"text":"'.$user.'"},')){
		$cc = str_replace('{"text":"'.$user.'"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setvaluee("user","chatid",$chatid,"keyboard",$cp);
		}else{
		$cc = str_replace('{"text":"'.$user.'"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setvaluee("user","chatid",$chatid,"keyboard",$cp);
		}
		$gathash = getallvaluee("hashmoh$user","hash");
		foreach($gathash as $mb){
			$sql = "DROP TABLE `moh$mb$user`";
							mysqli_query($con,$sql);
			}
			$sql = "DROP TABLE `hashmoh$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `dok$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `user$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `admin$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `data$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `code$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `chan$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `dayamar$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `eshtrak$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `filter$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `pasokh$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `channel$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `mohtava$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `eshtrak$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `fileid$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `datatype$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `datalist$user`";
							mysqli_query($con,$sql);
							$drop = "DROP TABLE `dok$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `delete$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `eshtrak$user`";
						    mysqli_query($con,$drop);
							$drop = "DROP TABLE `fileid$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `hash$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `datatype$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `datalist$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `replac$user`";
							mysqli_query($con,$drop);
							$all=getallvaluee("hashmoh$text",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$user`";
															mysqli_query($con,$drop);
													}
										$all=getallvaluee("hash$user",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$user`";
															mysqli_query($con,$drop);
										}
							$drop = "DROP TABLE `hashmoh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `filter$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `del$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `code$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `moh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `channel$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `robot$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `data$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `pasokh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `user$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `admin$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `chan$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `group$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `supergroup$user`";
							mysqli_query($con,$drop);
															
							deletevaluee("copycode","userbot",$user);
							$co1 = getvaluee("user","chatid",$admin,"emtiaz");
											$co2 = $co1 + 5;
											setvaluee("user","chatid",$admin,"emtiaz",$co2);
											
		step($chatid,"");
		if(empty(getvalue("dok","dokme",$ch,"textemtiaz2"))){
		$txt="ربات با موفقیت حذف شد✅";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz2");
			$txt = str_text($txt,1);
			}
		sm($chatid,$txt,$keykarbar);
		$send="یک ربات حذف شد✅

توسط ربات  @$userbott

ربات پاک شده : @$user";
		file_get_contents("https://api.telegram.org/bot$token_pedar/sendMessage?chat_id=$channel_token_id&text=".urlencode($send));
																	
				}elseif($text=="خیر⛔" or (!empty(getvalue("dok","dokme",$ch,"textemtiaz5")) && $text==getvalue("dok","dokme",$ch,"textemtiaz5"))){
				if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
				step($chatid,"");
				sm($chatid,$txt,$keykarbar);
				}
			}
			}
elseif($step=="deletebot$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if(!empty(getvaluee("qw$chatid","bot",$text,"token"))){
														if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
															$txt = "آیا برای حذف ربات مطمعن هستید ؟!!";
															
															}else{
																$txt = str_text(getvalue("dok","dokme",$ch,"textemtiaz3"),1);
																}
														if(empty(getvalue("dok","dokme",$ch,"textemtiaz4"))){
															$keyyes = "بله✅";
															}else{
																$keyyes = getvalue("dok","dokme",$ch,"textemtiaz4");
																}
																if(empty(getvalue("dok","dokme",$ch,"textemtiaz5"))){
															$keyno = "خیر⛔";
															}else{
																$keyno = getvalue("dok","dokme",$ch,"textemtiaz5");
																}
																$keypad = json_encode([
																'keyboard'=>[
																[["text"=>$keyyes],["text"=>$keyno]]
																],
																'resize_keyboard'=>true
																]);
																setvaluee("user","chatid",$chatid,"Other",$text);
																step($chatid,"deletebot2$ch");
																sm($chatid,$txt,$keypad);
														}else{
															if(empty(getvalue("dok","dokme",$ch,"textemtiaz1"))){
		$txt="لطفا از روی کیبورد انتخاب کنید!!!";
		}else{
			$txt=str_text(getvalue("dok","dokme",$ch,"textemtiaz1"),1);
			}
														sm($chatid,$txt);
														}
			
			}
			}

elseif($step=="updatebot$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if(!empty(getvaluee("qw$chatid","bot",$text,"token"))){
														$token = getvaluee("qw$chatid","bot",$text,"token");
														$file=file_get_contents("../../Haji.php");
														$mn = preg_replace('/\"(U)SERBOT\"/','"'.$text.'"',$file);
											$ch1 = preg_replace('/\"(A)PITOKENBOT\"/','"'.$token.'"',$mn);
											$ch2 = preg_replace('/\"(A)DMINBOT\"/','"'.$chatid.'"',$ch1);
												file_put_contents("../../BotList/$text/$text.php",$ch2);
													step($chatid,"");
													if(empty(getvalue("dok","dokme",$ch,"textemtiaz2"))){
		$txt="ربات با موفقیت اپدیت شد✅";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz2");
			$txt = str_text($txt,1);
			}
												sm($chatid,$txt,$keykarbar);
												$send="یک ربات اپدیت شد✅

توسط ربات  @$userbott

ربات اپدیت شده : @$text";
		file_get_contents("https://api.telegram.org/bot$token_pedar/sendMessage?chat_id=$channel_token_id&text=".urlencode($send));
																	
				
}else{
															if(empty(getvalue("dok","dokme",$ch,"textemtiaz1"))){
		$txt="لطفا از روی کیبورد انتخاب کنید!!!";
		}else{
			$txt=str_text(getvalue("dok","dokme",$ch,"textemtiaz1"),1);
			}
														sm($chatid,$txt);
														}
			
			}
			}
elseif($step=="change$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
if(empty(getvalue("user","chatid",$text,"chatid")) || !preg_match('/^[0-9]+$/',$text)){
	if(empty(getvalue("dok","dokme",$ch,"textemtiaz1"))){
		$txt="ایدی عددی غلط میباشد یا جزو مخاطبین ربات ما نیست⛔";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz1");
			$txt = str_text($txt,1);
			
			}
			sm($chatid,$txt);
	}else{
		
		setOther($chatid,$text);
		if(empty(getvalue("dok","dokme",$ch,"textemtiaz2"))){
		$txt="لطفا تعداد امتیازی که میخواهید انتقال بدید را وارد کنید :";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz2");
			$txt = str_text($txt,1);
			
			}
		sm($chatid,$txt);
		step($chatid,"change2$ch");
		}
}
}elseif($step=="enfjar$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if(empty(getvalue("dok","dokme",$ch,"lowshart"))){
		$lowshart= 100;
		}else{
			$lowshart =getvalue("dok","dokme",$ch,"lowshart");
			
			}
			if(empty(getvalue("dok","dokme",$ch,"highshart"))){
		$highshart= 100;
		}else{
			$highshart =getvalue("dok","dokme",$ch,"highshart");
			
			}
if(preg_match('/^[0-9?\.]+$/',$text) && $text >= $lowshart && $text <= highshart){
				if(getCoin($chatid) >= $text){
					setOther($chatid,$text);
					
if(empty(getvalue("dok","dokme",$ch,"textemtiaz1"))){
		$txt="
				💣 ضریب مورد نظر خود را ارسال کنید.
";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz1");
			$txt = str_text($txt,1);
}
$zaribb = getvalue("user","chatid",$chatid,"zarib");
if(empty(getvalue("user","chatid",$chatid,"zarib"))){
	$zaribb = "";
	}
if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>$zaribb]],
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>$zaribb]],
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
}
step($chatid,"enfjar2$ch");
sm($chatid,$txt,$keyback);

}else{
	if(empty(getvalue("dok","dokme",$ch,"textemtiaz2"))){
		$txt="❌ موجودی حساب شما جهت شرط کافی نیست !";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz2");
			$txt = str_text($txt,1);
			}
			sm($chatid,$txt);
	}
		
		}else{
			if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
		$txt="
⛔مقدار عدد ارسالی نا معتبر می باشد!!!

⚠حداقل امتیاز برای شرط $lowshart و حداکثر $highshart می باشد.
";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz3");
			$txt = str_text($txt,1);
			}
			sm($chatid,$txt);
			}
}
}elseif($step=="enfjar2$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			if(preg_match('/^([1-9]|[1-9][0-9])\.([0-9]|[0-9][0-9])$/',$text,$xv) and $text !="1.00"){
				
				if($xv[2]=="0"){
					$xv[2]="00";
					}
					/*
					if($xv[2]=="1"){
					$xv[2]="10";
					}
					if($xv[2]=="2"){
					$xv[2]="20";
					}
					if($xv[2]=="3"){
					$xv[2]="30";
					}
					if($xv[2]=="4"){
					$xv[2]="40";
					}
					if($xv[2]=="5"){
					$xv[2]="50";
					}
					if($xv[2]=="6"){
					$xv[2]="60";
					}
					if($xv[2]=="7"){
					$xv[2]="70";
					}
					if($xv[2]=="8"){
					$xv[2]="80";
					}
					if($xv[2]=="9"){
					$xv[2]="90";
					}
					*/
					$sett = getCoin($chatid) - getOther($chatid);
							setCoin($chatid,$sett);
					setvalue("user","chatid",$chatid,"zarib",$text);
					if($xv[1]==1){
					$rand1=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,3,3,3,3,4,4,5,5,5,6,6,7,7,8,8,9,9,10,11,12,13,25,34,45,65,56,67,98,99,23,13,19);
					}elseif($xv[1]==2){
						$rand1=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,5,5,5,5,5,6,7,8,9,1,11,12,15,23,45,34,54,58,69,71,23,99,98);
						}elseif($xv[1]==3){
						$rand1=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,11,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,5,5,5,5,5,6,7,8,9,1);
						}else{
						$rand1=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,11,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,5,5,5,5,5,6,7,8,9,1,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,11,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,11,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,5,5,5,5,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99);
						}
$c1 = count($rand1)-1;
					$random1 = $rand1[rand(0,$c1)];
					if($xv[2] <= 10){
						$rand2=array("00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","00","01","01","01","01","01","01","01","01","01","01","01","01","02","02","02","02","02","02","02","02","02","02","02","02","03","03","03","03","03","03","03","03","03","03","00","01","01","01","01","01","01","01","01","01","01","01","01","02","02","02","02","02","02","02","02","02","02","02","02","03","03","03","03","03","03","03","03","03","03","00","00","00","00","00","00","00","01","01","01","01","01","01","01","01","01","01","01","01","02","02","02","02","02","02","02","02","02","02","02","02","03","03","03","03","03","03","03","03","03","03","03","03","03","03","03","03","03","03","03","04","04","04","04","04","04","04","04","04","04","04","04","04","04","04","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","06","06","06","06","06","06","06","06","06","06","06","06","06","06","06","07","07","07","07","07","07","07","07","07","07","07","07","07","08","08","08","08","08","08","08","08","08","08","08","08","08","08","08","09","09","09","09","09","09","09","09","09","09","09","09","09","09","09",10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,80,81,82,83,84);
						}else{
$rand2=array("00","00","00","00","00","00","01","01","01","01","01","01","01","01","02","02","02","02","02","03","03","03","03","03","03","03","03","03","03","03","03","04","04","04","04","04","04","04","04","04","04","04","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","05","06","06","06","06","06","06","06","06","06","06","07","07","07","07","07","07","07","07","07","07","07","08","08","08","08","08","08","08","08","08","09","09","09","09","09","09","09",10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,53,53,53,53,54,54,54,54,54,55,55,55,55,55,56,56,56,56,56,56,57,57,57,57,57,58,58,58,58,58,59,59,59,59,59,60,60,60,60,60,61,61,61,61,61,61,61,61,62,62,62,62,62,63,63,63,63,63,63,64,64,64,65,65,65,66,66,66,66,67,67,67,67,68,68,68,68,69,69,69,69,70,70,70,70,71,71,71,71,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99);
}
$c2 = count($rand2)-1;
					$random2 = $rand2[rand(0,$c2)];
					if($random1==0 or empty($random2)){
	$random2="00";
	}
						$botzarib = $random1.".".$random2;
						
						$zarib = $xv[1].".".$xv[2];
						$getOther = getOther($chatid);
						$fini = getOther($chatid) * $zarib;
						$md5 = md5($botzarib);
						$hash = hash('sha256', $botzarib);
						if($botzarib == 0){
					$botzarib ="0.00";
					}
					if($botzarib==1){
					$botzarib="1.00";
					}
						
step($chatid,"");
						if($botzarib == $zarib or $botzarib >= $zarib){
							$coi = getvalue("user","chatid",$chatid,"emtiaz");
							$set = $coi + $fini;
							setCoin($chatid,$set);
							$bord = "برد✅";
							if(empty(getvalue("dok","dokme",$ch,"textresid"))){
		
							$txt = "
							🔥 ضریب این دست در $botzarib بسته شد.

✅ شما مبلغ $fini سکه برنده شدید.

💣 ضریب درخواستی شما : $zarib
➖➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md5</code>

💣Hash : <code>$hash </code>
							";
							}else{
								$txt = getvalue("dok","dokme",$ch,"textresid");
								$r1 = array("GETEMTIAZ","EMTIAZ","GETZARIB","ZARIB");
						$r2 = array($fini,$getOther,$botzarib,$zarib);
						$txt = str_replace($r1,$r2,$txt);
						$txt = str_text($txt,1);
								}
							sm($chatid,$txt,$keykarbar);
							}else{
								$bord = "باخت⛔";
								if(empty(getvalue("dok","dokme",$ch,"textresid2"))){
		
								$txt="
								🔥 ضریب این دست در $botzarib بسته شد.

❌ شما مبلغ $getOther  سکه باختید.

💣 ضریب درخواستی شما : $zarib
➖➖➖➖➖➖➖➖➖➖➖➖
💣Md5 : <code>$md5</code>

💣Hash : <code>$hash</code>
								";
								}else{
								$txt = getvalue("dok","dokme",$ch,"textresid2");
								$r1 = array("GETEMTIAZ","EMTIAZ","GETZARIB","ZARIB");
						$r2 = array($getOther,$getOther,$botzarib,$zarib);
						$txt = str_replace($r1,$r2,$txt);
						$txt = str_text($txt,1);
								}
								sm($chatid,$txt,$keykarbar);
							
								}
				}else{
					if(empty(getvalue("dok","dokme",$ch,"textemtiaz4"))){
		$txt="
		❌ ضریب مورد نظر نامعتبر است !
👈 ضریب مورد نظر را از  1.01 الی 99.99 ارسال کنید.
		";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz4");
			$txt = str_text($txt,1);
			}
			sm($chatid,$txt);
					}
			}
	}elseif($step=="change2$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$coin = getCoin($chatid);
			if(preg_match('/^[0-9]+$/',$text)){
			if($text <= $coin ){
				$id = getOther($chatid);
				$remcoin = $coin - $text;
				setCoin($chatid,$remcoin);
				$coin2 = getCoin($id);
				$addcoin = $coin2 + $text;
				setCoin($id,$addcoin);
				if(empty(getvalue("dok","dokme",$ch,"textresid"))){
		$txt="انتقال با موفقیت انجام شد✅";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textresid");
			$txt=str_replace("TEXT_1",$id,$txt);
			$txt = str_text($txt,1);
			}
			
			sm($chatid,$txt,$keykarbar);
			step($chatid,"");
			if(empty(getvalue("dok","dokme",$ch,"textemtiaz4"))){
		$txt2="کاربر $chatid برای شما در تاریخ $datesh-$time مقدار $text امتیاز انتقال داد";
		}else{
			$txt2=getvalue("dok","dokme",$ch,"textemtiaz4");
			$txt2=str_replace("TEXT_1",$chatid,$txt2);
			$txt2 = str_text($txt2,1);
			
			}
			
			bot("sendmessage",[
			"chat_id"=>$id,
			"text"=>$txt2,
			'parse_mode'=>'HTML'
			]);
			step($chatid,"");
				}else{
					if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
		$txt="مقدار امتیاز نامعتبر یا بیشتر از مقدار امتیاز شماست🚫";
		}else{
			$txt=getvalue("dok","dokme",$ch,"textemtiaz3");
			$txt = str_text($txt,1);
			
			}
			sm($chatid,$txt);
					}
					}else{
     if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
  $txt="مقدار امتیاز نامعتبر یا بیشتر از مقدار امتیاز شماست🚫";
  }else{
   $txt=getvalue("dok","dokme",$ch,"textemtiaz3");
   $txt = str_text($txt,1);
			
   }
   sm($chatid,$txt);
   }
			}
	}elseif($step=="search$ch"){
if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$chnnl = getvalue("dok","dokme",$ch,"channel");
			$get=json_decode(file_get_contents("$domain_url/search.php?search=".urlencode($text)."=&type=json&username=$chnnl"),true);
			$ok = $get["ok"];
			if($ok==false){
				if(empty(getvalue("dok","dokme",$ch,"textemtiaz2"))){
					$txtt="چیزی یافت نشد⛔";
				}else{
					$txtt=getvalue("dok","dokme",$ch,"textemtiaz2");
					}
				$ch13 = str_text($txtt,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  }
  sm($chatid,$txtt,$kei);
}else{
	if(empty(getvalue("dok","dokme",$ch,"textemtiaz3"))){
		$tedad=10;
		}else{
					$tedad = getvalue("dok","dokme",$ch,"textemtiaz3");
					}
					$non = getvalue("dok","dokme",$ch,"textemtiaz1");
					if($non=="forward"){
						for($x=0; $x <= $tedad; $x++){
							$result= $get["result"][$x]["message_id"];
							if(!empty($result)){
								fm($chatid,$chnnl,$result);
								}else{
									break;
									}
							}
						}elseif($non=="ersal"){
							for($x=0; $x <= $tedad; $x++){
							$result= $get["result"][$x]["message_id"];
							if(!empty($result)){
								bot("copyMessage",[
								'chat_id'=>$chatid,
								'from_chat_id'=>$chnnl,
								'message_id'=>$result
								]);
								}else{
									break;
									}
							}
							}elseif($non=="link"){
								$chnnl=str_replace("@","",$chnnl);
								for($x=0; $x <= $tedad; $x++){
							$result= $get["result"][$x]["message_id"];
							if(!empty($result)){
								
								sm($chatid,"https://t.me/$chnnl/$result");
								}else{
									break;
									}
							}
								}else{
									for($x=0; $x <= $tedad; $x++){
							$result= $get["result"][$x]["message_id"];
							if(!empty($result)){
								fm($chatid,$chnnl,$result);
								
								}else{
									break;
									}
							}
									}
					}
			}
}elseif($step=="jostojo$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$arra = getallvalue("dok","dokme");
			$keytest='[{"text":""}],[{"text":""}]';
			foreach($arra as $sd){
				if(strpos($sd,$text) || $sd ==$text){
					if(getvalue("dok","dokme",$sd,"hidden")!="on"){
					$keytest = str_replace(',[{"text":""}]',',[{"text":"'.$sd.'"}],[{"text":""}]',$keytest);
					}
}
				}
				if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
									
				$keykarbar='{"keyboard":['.$keytest.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
			step($chatid,"");
				if(getTextresid($ch)==null){
					
					
		$ch13="دکمه های زیر جست و جو شد :";
		}else{
	$txtt = getTextresid($ch);
			$ch13 = str_text($txtt,1);
		}
			sm($chatid,$ch13,$keykarbar);
				}
					}elseif($step=="getApi$ch"){
						if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		$sql = "DROP TABLE `get$chatid$userbott`";
   mysqli_query($con,$sql);
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$drop = getvalue("hashmoh","text",$ch,"hash");
			$ted = getOther($chatid);
			$sql = "CREATE TABLE `get$chatid$userbott`(`dokme` TEXT,`text` TEXT)";
			mysqli_query($con,$sql);
			if(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="addad" && !preg_match('/^[0-9]+$/',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="link" && !preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="number" && !preg_match('/^(?:09|\+?63|\+?98|\+?1)(?:\d(?:-)?){9,10}$/m',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="english" && !preg_match('/^[a-z0-9 .\-]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="farsimatn" && !preg_match('/^[پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="hamematn" && !preg_match('/^[a-zA-Z0-9ضصقفغعهخحجشسیبلاتنمکظطدزروچپگژآأإء\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="englishandnumber" && !preg_match('/^[a-zA-Z0-9\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="forward" && !isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="noforward" && isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="username" && !preg_match('/^\@[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dastor" && !preg_match('/^\/[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="email" && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="ball" && $dice !="⚽"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="basket" && $dice !="🏀"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="boling" && $dice !="🎳"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="eslat" && $dice !="🎰"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dart" && $dice !="🎯"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dice" && $dice !="🎲"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="photo" && !isset($photo)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="film" && !isset($video)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="audio" && !isset($audio)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="sticker" && !isset($sticker)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="video_note" && !isset($video_note)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="contact" && !isset($contact_number)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="location" && !isset($long_location)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}
			
			$ted2 = $ted+1;
			insert("get$chatid","`dokme`,`text`","'TEXT_$ted','$text'");
			if(!empty(getvalue("mohtava$drop","dokme","TEXT_$ted2","dokme"))){
				$teext=getvalue("mohtava$drop","dokme","TEXT_$ted2","textget");
				setOther($chatid,$ted2);
				$teext= str_text($teext,1);
				sm($chatid,$teext);
				
				ToDie();
}else{
		$gget = json_decode(getDokmetext($ch),true);
				$get=$gget['text'];
				if(strpos($get,"||")){
					$exp=explode("||",$get);
				$get =$exp[0];
					$cap=$exp[1];
  if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
		$cap = str_replace($bh,$vn,$cap);
			}
			}
    $cap13 = str_text($cap,1);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
  } 
}
  if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
		$get = str_replace($bh,urlencode(trim($vn)),$get);
			}
			}
			$sql = "DROP TABLE `get$chatid$userbott`";
   mysqli_query($con,$sql);
							$txtt = $get;
							if(preg_match("/(%)([^\']+)(%)/",$txtt,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$txtt);
 $txtt = $hi[0];
  $kei = textToinline("%$k%",$txtt);
  } 
			$ch13 = str_text($txtt,1);
	$ch13 = str_replace("TEXT",urlencode(trim($text)),$ch13);
//$get = json_decode(file_get_contents("dokme/$bmm.json"),true);
						if(validImage($ch13)=="image"){
							if(strpos(typee($ch13),"gif")!==false){
							$sg = sd($chatid,$ch13,$cap13,$kei);
							}else{
							$sg = sp($chatid,$ch13,$cap13,$kei);
							}
							}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="video"){
$sg = sv($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="application"){
	if(strpos(typee($ch13),"json")!==false){
		
		$xc = file_get_contents($ch13);
		$xc= str_text($xc,1);
		if(preg_match("/(%)([^\']+)(%)/",$xc,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$xc);
 $xc = $hi[0];
  $kei = textToinline("%$k%",$xc);
  } 
		$sg = sm($chatid,$xc.$cap13,$kei);
						
		}else{
$sg = sd($chatid,$ch13,$cap13,$kei);
}
}else{
	if(getimagesize("$ch13")==true){
		$sg = sp($chatid,$ch13,$cap13,$kei);
		}else{
		$xc = file_get_contents($ch13);
		$xc= str_text($xc,1);
		if(preg_match("/(%)([^\']+)(%)/",$xc,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$xc);
 $xc = $hi[0];
  $kei = textToinline("%$k%",$xc);
  } 
						$sg = sm($chatid,$xc.$cap13,$kei);
						}
						
						}
						
						}
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$ch,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$ch','$now'");
					}
						$sql = "DROP TABLE `get$chatid$userbott`";
   mysqli_query($con,$sql);
						
}
	}elseif($step=="sendforchat"){
					if($text=="برگشت↪"){
						step($chatid,"panel");
						sm($chatid,"به پنل برگشتید :",$keypanel);
						}else{
						$id = getOther2($chatid);
						step($chatid,"panel");
						if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}	
		if(isset($photo)){
			sp($id,$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($id,$video,$caption,$kei);
				}elseif(isset($document)){
					sd($id,$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($id,$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($id,$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($id,$sticker);
								}elseif(isset($text)){
									bot('sendmessage',[
									'chat_id'=>$id,
									'text'=>$text,
									'parse_mode'=>'HTML',
									'reply_markup'=>$kei
									]);
	}
		sm($chatid,"پیام شما ارسال شد✅\n\nبه پنل برگشتید :",$keypanel);	

						}
					}elseif($step=="matntartib$ch"){
						if($text=="برگشت به خانه" || (!empty($backname) && $text==$backname)){
		if($text==$backname){
			$txtt=getvalue("data","id",1,"txtback");
			}else{
				$txtt=getvalue("data","id",1,"txtback");
				
				}
		//$tet="به عقب برگشتید : ";
		//$txt = $data['textback'];
		step($chatid,"");
		$ch13 = str_text($txtt,1);
sm($chatid,$ch13,$keykarbar);
		}elseif($text=="پست بعدی⏩"  || (!empty(getvalue("dok","dokme",$ch,"textemtiaz1")) && $text==getvalue("dok","dokme",$ch,"textemtiaz1"))){
			$x = getvalue("dok","dokme",$ch,"textemtiaz2");
			$x = $x +1;
			$get = json_decode(getDokmetext($ch),true);
			$nok=$get[$x];
			if(empty($nok)){
				$x = $x - 1;
				setvalue("dok","dokme",$ch,"textemtiaz2",$x);
				$nok=$get[$x];
				}else{
			setvalue("dok","dokme",$ch,"textemtiaz2",$x);
			}
						$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,1);
	$capp=str_replace("/r/n/r","\n",$capp);
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$capp,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$capp,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$capp,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$capp,$kei);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet,$kei);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$capp);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$capp);
			}elseif($type=="dice"){
			
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$ch,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$ch','$now'");
					}
			}
						}
		elseif($step=="getmatntaki$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		$sql = "DROP TABLE `get$chatid$userbott`";
   mysqli_query($con,$sql);
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$drop = getvalue("hashmoh","text",$ch,"hash");
			$ted = getOther($chatid);
			$sql = "CREATE TABLE `get$chatid$userbott`(`dokme` TEXT,`text` TEXT)";
			mysqli_query($con,$sql);
			if(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="addad" && !preg_match('/^[0-9]+$/',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="link" && !preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="number" && !preg_match('/^(?:09|\+?63|\+?98|\+?1)(?:\d(?:-)?){9,10}$/m',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="english" && !preg_match('/^[a-z0-9 .\-]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="farsimatn" && !preg_match('/^[پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="hamematn" && !preg_match('/^[a-zA-Z0-9ضصقفغعهخحجشسیبلاتنمکظطدزروچپگژآأإء\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="englishandnumber" && !preg_match('/^[a-zA-Z0-9\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="forward" && !isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="noforward" && isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="username" && !preg_match('/^\@[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dastor" && !preg_match('/^\/[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="email" && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="ball" && $dice !="⚽"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="basket" && $dice !="🏀"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="boling" && $dice !="🎳"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="eslat" && $dice !="🎰"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dart" && $dice !="🎯"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dice" && $dice !="🎲"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="photo" && !isset($photo)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="film" && !isset($video)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="audio" && !isset($audio)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="sticker" && !isset($sticker)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="video_note" && !isset($video_note)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="contact" && !isset($contact_number)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="location" && !isset($long_location)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}
			
			$ted2 = $ted+1;
			insert("get$chatid","`dokme`,`text`","'TEXT_$ted','$text'");
			if(!empty(getvalue("mohtava$drop","dokme","TEXT_$ted2","dokme"))){
				$teext=getvalue("mohtava$drop","dokme","TEXT_$ted2","textget");
				setOther($chatid,$ted2);
				$teext= str_text($teext,1);
				sm($chatid,$teext);
}else{
	
		$get = json_decode(getDokmetext($ch),true);
//	$type = $get['type'];
	//$text = $get['text'];
	
	$type = $get['type'];
	$texx = $get['text'];
	$capp = $get['caption'];
	if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$capp = str_replace($bh,$vn,$capp);
			}
			}
		$ch13 = str_text($capp,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $cap = $hi[0];
  $kei = textToinline("%$k%",$cap);
  } 
if($type=="text"){
		if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$texx = str_replace($bh,$vn,$texx);
			}
			}
			$ch13 = str_text($texx,1);
$ch13=str_replace("/r/n/r","\n",$ch13);
	
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$texx,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$texx,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$texx,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$texx,$cap,$kei);
		}elseif($type=="sticker"){
			$sg = ss($chatid,$texx);
			}elseif($type=="contact"){
		$sg = sco($chatid,$texx,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$texx);
		}elseif($type=="location"){
			$sg = slo($chatid,$texx,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$texx);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$texx,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$ch,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$ch','$now'");
					}
			$sql = "DROP TABLE `get$chatid$userbott`";
			mysqli_query($con,$sql);
			
	}
	}
}elseif($step=="getphp$ch"){
	if($text=="برگشت↪" || (!empty(getvalue("data","id",1,"barmoh")) && $text==getvalue("data","id",1,"barmoh"))){
		step($chatid,"");
		$sql = "DROP TABLE `get$chatid$userbott`";
   mysqli_query($con,$sql);
		if(empty(getvalue("data","id",1,"textbarmoh"))){
			$txt="به منو برگشتید";
			}else{
				$txt=getvalue("data","id",1,"textbarmoh");
				$txt= str_text($txt,1);
				}
		sm($chatid,$txt,$keykarbar);
		}else{
			$drop = getvalue("hashmoh","text",$ch,"hash");
			$ted = getOther($chatid);
			$sql = "CREATE TABLE `get$chatid$userbott`(`dokme` TEXT,`text` TEXT)";
			mysqli_query($con,$sql);
			if(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="addad" && !preg_match('/^[0-9]+$/',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="link" && !preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="number" && !preg_match('/^(?:09|\+?63|\+?98|\+?1)(?:\d(?:-)?){9,10}$/m',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="english" && !preg_match('/^[a-z0-9 .\-]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="farsimatn" && !preg_match('/^[پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="hamematn" && !preg_match('/^[a-zA-Z0-9ضصقفغعهخحجشسیبلاتنمکظطدزروچپگژآأإء\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="englishandnumber" && !preg_match('/^[a-zA-Z0-9\s]+$/u',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="forward" && !isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="noforward" && isset($forward)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="username" && !preg_match('/^\@[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dastor" && !preg_match('/^\/[a-z0-9\_]+$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="email" && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="ball" && $dice !="⚽"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="basket" && $dice !="🏀"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="boling" && $dice !="🎳"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="eslat" && $dice !="🎰"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dart" && $dice !="🎯"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="dice" && $dice !="🎲"){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="photo" && !isset($photo)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="film" && !isset($video)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="audio" && !isset($audio)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="sticker" && !isset($sticker)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="video_note" && !isset($video_note)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="contact" && !isset($contact_number)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}elseif(getvalue("mohtava$drop","dokme","TEXT_$ted","matn")=="location" && !isset($long_location)){
				sm($chatid,str_text(getvalue("mohtava$drop","dokme","TEXT_$ted","textesh"),1));
				ToDie();
				}
			
			$ted2 = $ted+1;
			insert("get$chatid","`dokme`,`text`","'TEXT_$ted','$text'");
			if(!empty(getvalue("mohtava$drop","dokme","TEXT_$ted2","dokme"))){
				$teext=getvalue("mohtava$drop","dokme","TEXT_$ted2","textget");
				setOther($chatid,$ted2);
				$teext= str_text($teext,1);
				sm($chatid,$teext);
}else{
		$get = getDokmetext($ch);
	if(!empty(getallvalue("mohtava$drop","dokme"))){
		$ck = getallvalue("mohtava$drop","dokme");
	foreach($ck as $bh){
		$vn = getvalue("get$chatid","dokme",$bh,"text");
			$get = str_replace('$'.$bh,'"'.$vn.'"',$get);
			}
			
			}
     $array1 = array('$FIRSTNAME','$LASTNAME','$USERNAME','$USERID','$PHONE','$BIO','$IDBOT','$BOTUSER','$BOTNAME','$GPNAME','$GPUSER','$CHATID','$DESCRIOPTION','$GETDICE','$DICE','$MESSAGEID','$COIN','$MEMBER','$LINK','$ALLMEM','$HOUR','$MINUTE','$SECOND','$JOINDATEM','$JOINDATESH','$JOINTIME','$TIME','$YEAR','$MONTH','$DAY','$DATESH','$DATEM','$BOTMEM','$TEXT','$ADD');
 $array2 = array('"'.$firstname.'"','"'.$lastname.'"','"'.$username.'"','"'.$fromid.'"','"'.$phone_number.'"','"'.$bio.'"','"'.$idbot.'"','"'.$botuser.'"','"'.$botname.'"','"'.$gpname.'"','"'.$gpuser.'"','"'.$chatid.'"','"'.$description.'"','"'.getOther($chatid).'"','"'.$Message->dice->value.'"','"'.$messageid.'"','"'.getCoin($chatid).'"','"'.getZirmaj($chatid).'"','"https://t.me/'.$botuser.'?start='.$fromid.'"','"'.getDokother2($text).'"','"'.date('H').'"','"'.date('i').'"','"'.date('s').'"','"'.getJoindatem($fromid).'"','"'.getJoindatesh($fromid).'"','"'.getJointime($fromid).'"','"'.date("H:i:s").'"','"'.date("Y").'"','"'.date("m").'"','"'.date("d").'"','"'.$datesh.'"','"'.$datem.'"','"'.amarcount("user").'"','"'.$text.'"',"");
 $code=str_replace($array1,$array2,$get);
 $cha = curl_init();
curl_setopt($cha, CURLOPT_URL,"https://rextester.com/rundotnet/Run");
curl_setopt($cha, CURLOPT_POST, 1);
curl_setopt($cha, CURLOPT_POSTFIELDS,"LanguageChoice=8&Program=$code&CompilerArgs=1");
curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($cha);
curl_close ($cha);
$ge=json_decode($server_output,true);
if(!empty($ge["Errors"]) && !empty($ge["Result"])){
$sg = sm($chatid,$ge["Result"]);
 }elseif(empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }else{
$sg =   sm($chatid,$ge["Errors"]);
  }
  $messageid = $sg->result->message_id;
  if(getvalue("dok","dokme",$ch,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$ch','$now'");
					}
  $sql = "DROP TABLE `get$chatid$userbott`";
			mysqli_query($con,$sql);
	}
	}
}elseif(!empty(getvalue("dok","dastor",$text,"dastor")) && empty(getstep($chatid)) ){
$bmm = getvalue("dok","dastor",$text,"dokme");	
	if(getDokmenok($bmm)=="getApi"){
				step($chatid,"getApi$bmm");
				setCode($chatid,$bmm);
				if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
				if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
		}
				}elseif(getDokmenok($bmm)=="jostojo"){
					step($chatid,"jostojo$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="search"){
					step($chatid,"search$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="change"){
					step($chatid,"change$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا ایدی عددی فردی که میخواهید برای ان امتیاز ارسال کنید را وارد کنید :";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="enfjar"){
					step($chatid,"enfjar$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="👈 لطفا تعداد سکه ای که میخواهید شرط ببندید را وارد کنید :";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="createbot"){
					step($chatid,"createbot$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا توکن ربات را برای ساخت ربات ارسال نمایید :";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
					}
					ToDie();
					}elseif(getDokmenok($bmm)=="updatebot"){
					step($chatid,"updatebot$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}elseif(getDokmenok($bmm)=="deletebot"){
					step($chatid,"deletebot$bmm");
					setCode($chatid,$bmm);
				if(isset($dokme["getmoh$bmm"]) && isset($dokme["getmoh$bmm"][$cg])){
				$teext = $dokme["getmoh$bmm"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}elseif(getDokmenok($bmm)=="schannel"){
					step($chatid,"schannel$bmm");
					setCode($chatid,$bmm);
					if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="fchannel"){
					step($chatid,"fchannel$bmm");
					setCode($chatid,$bmm);
					if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					}elseif(getDokmenok($bmm)=="fchannel"){
					step($chatid,"fchannel$bmm");
					setCode($chatid,$bmm);
					if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					
					}
				elseif(getDokmenok($bmm)=="getmatntaki"){
				step($chatid,"getmatntaki$bmm");
				setCode($chatid,$bmm);
				if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
			if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
				}
				ToDie();
				}elseif(getDokmenok($bmm)=="getphp"){
				step($chatid,"getphp$bmm");
				setCode($chatid,$bmm);
				if(!empty(getvalue("hashmoh","text",$bmm,"hash"))){
				$drop = getvalue("hashmoh","text",$bmm,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
			if(getTextersal($bmm)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($bmm);
	$ch13 = str_text($tx,1);
	 }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
				}
				}elseif(getDokmenok($bmm)=="coin"){
				$tet=getDokmetext($bmm);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `emtiaz` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `emtiaz` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getCoin($fg);
					if(!empty(getvalue("dok","dokme",$bmm,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$bmm,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	
				}elseif(getDokmenok($bmm)=="bartarin"){
				$tet=getDokmetext($bmm);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `zirmaj` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `zirmaj` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getZirmaj($fg);
					if(!empty(getvalue("dok","dokme",$bmm,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$bmm,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	
				}elseif(getDokmenok($bmm)=="php"){
     $get=getDokmetext($bmm);
     $array1 = array('$FIRSTNAME','$LASTNAME','$USERNAME','$USERID','$PHONE','$BIO','$IDBOT','$BOTUSER','$BOTNAME','$GPNAME','$GPUSER','$CHATID','$DESCRIOPTION','$GETDICE','$DICE','$MESSAGEID','$COIN','$MEMBER','$LINK','$ALLMEM','$HOUR','$MINUTE','$SECOND','$JOINDATEM','$JOINDATESH','$JOINTIME','$TIME','$YEAR','$MONTH','$DAY','$DATESH','$DATEM','$BOTMEM','$TEXT','$ADD');
 $array2 = array('"'.$firstname.'"','"'.$lastname.'"','"'.$username.'"','"'.$fromid.'"','"'.$phone_number.'"','"'.$bio.'"','"'.$idbot.'"','"'.$botuser.'"','"'.$botname.'"','"'.$gpname.'"','"'.$gpuser.'"','"'.$chatid.'"','"'.$description.'"','"'.getOther($chatid).'"','"'.$Message->dice->value.'"','"'.$messageid.'"','"'.getCoin($chatid).'"','"'.getZirmaj($chatid).'"','"https://t.me/'.$botuser.'?start='.$fromid.'"','"'.getDokother2($text).'"','"'.date('H').'"','"'.date('i').'"','"'.date('s').'"','"'.getJoindatem($fromid).'"','"'.getJoindatesh($fromid).'"','"'.getJointime($fromid).'"','"'.date("H:i:s").'"','"'.date("Y").'"','"'.date("m").'"','"'.date("d").'"','"'.$datesh.'"','"'.$datem.'"','"'.amarcount("user").'"','"'.$text.'"',"");
 $code=str_replace($array1,$array2,$get);
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://rextester.com/rundotnet/Run");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"LanguageChoice=8&Program=$code&CompilerArgs=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
$ge=json_decode($server_output,true);
if(!empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }elseif(empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }else{
$sg=   sm($chatid,$ge["Errors"]);
  }
  $messageid= $sg->result->message_id;
if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}

     
     }
	elseif(getDokmenok($bmm)=="matntaki"){
		
	$get = json_decode(getDokmetext($bmm),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$txtt = $get['text'];
	$capp = $get['caption'];
		
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($txtt,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$txttt=str_replace("/r/n/r","\n",$ch13);
	$sg = sm($chatid,$txttt,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$txtt,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$txtt,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$txtt,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$txtt,$cap,$kei);
		}elseif($type=="sticker"){
			$sg = ss($chatid,$txtt);
			}elseif($type=="contact"){
		$sg = sco($chatid,$txtt,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$txtt);
		}elseif($type=="location"){
			$sg = slo($chatid,$txtt,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$txtt);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$txtt,$cap,$kei);
			}
			$messageid = $sg->result->message_id;
			if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
	}elseif(getDokmenok($bmm)=="matntartib"){
		$get = json_decode(getDokmetext($bmm),true);
		step($chatid,"matntartib$bmm");
				setCode($chatid,$bmm);
				if(empty(getvalue("dok","dokme",$bmm,"textemtiaz1"))){
					$dokmee="پست بعدی⏩";
					}else{
						$dokmee=getvalue("dok","dokme",$bmm,"textemtiaz1");
						}
						if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
									$keybo=json_encode([
									'keyboard'=>[
									[["text"=>$dokmee]],
									[["text"=>$back]]
									],
									'resize_keyboard'=>true,
									]);
									setvalue("dok","dokme",$bmm,"textemtiaz2",0);
						$nok=$get[0];
						$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,1);
		$capp=str_replace("/r/n/r","\n",$capp);
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$keybo);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$capp,$keybo);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$capp,$keybo);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$capp,$keybo);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$capp,$keybo);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet,$keybo);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$capp);
		sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$capp);
			sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="dice"){
			
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$capp,$keybo);
			}
			$messageid = $sg->result->message_id;
			if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
		}elseif(getDokmenok($bmm)=="matnchand"){
		$get = json_decode(getDokmetext($bmm),true);
$x = 0;
foreach ($get as $nok){
	if($x==10){
	sleep(1);
$x=0;	
	}
	$x++;
$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$ch13 = str_text($capp,1);
				$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$cap,$kei);
			}
			$messageid = $sg->result->message_id;
			if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
}
		}elseif(getDokmenok($bmm)=="matnrand"){
		$get = json_decode(getDokmetext($bmm),true);
$cou = count($get)-1;
$rand=rand(0,$cou);
$type = $get[$rand]['type'];
	$tet = $get[$rand]['text'];
	$capp = $get[$rand]['caption'];
	
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	$sg = sm($chatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$cap,$kei);
			}
			$messageid = $sg->result->message_id;
			if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
		}elseif(getDokmenok($bmm)=="Api"){
							$gget = json_decode(getDokmetext($bmm),true);
				$get=$gget['text'];
				if(strpos($get,"||")){
					$exp=explode("||",$get);
				$get =$exp[0];
					$cap=$exp[1];
    $cap13 = str_text($cap,1);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
  } 
}
$txtt = $get;
			$ch13 = str_text($txtt,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
if(validImage($ch13)=="image"){
							if(strpos(typee($ch13),"gif")!==false){
							$sg = sd($chatid,$ch13,$cap13,$kei);
							}else{
							$sg = sp($chatid,$ch13,$cap13,$kei);
							}
							}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="video"){
$sg = sv($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="application"){
	if(strpos(typee($ch13),"json")!==false){
		$xc = file_get_contents($ch13);
						$sg = sm($chatid,$xc.$cap13,$kei);
		}else{
$sg = sd($chatid,$ch13,$cap13,$kei);
}
}else{
	if(getimagesize("$ch13")==true){
		$sg = sp($chatid,$ch13,$cap13,$kei);
		}else{
								$xc = file_get_contents($ch13);
						$sg = sm($chatid,$xc.$cap13,$kei);
						}
						}
						$messageid = $sg->result->message_id;
						if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
		}
		elseif(getDokmenok($bmm)=="rss"){
							$get = json_decode(getDokmetext($bmm),true);
							$text=$get['text'];
							
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
						$xc = getrss($text);
						$sg = sm($chatid,$xc,$kei);
						$messageid = $sg->result->message_id;
						if(getvalue("dok","dokme",$bmm,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$bmm','$now'");
					}
						}elseif(getDokmenok($bmm)=="back"){
							if(!empty(getTextersal($bmm))){
								$txtt = getTextersal($bmm);
								$ch13 = str_text($txtt,1);
$txt=$ch13;
								}else{
								$txt="به عقب برگشتید :";
								}
$key = getvalue("dok","dokme",$text,"keyback");
if($key=="منوی اصلی" || empty($key)){
	sm($chatid,$txt,$keykarbar);
	}else{
		if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($key);
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								sm($chatid,$txt,$keykar);
		}
}elseif(getDokmenok($bmm)=="newdokme"){
								$get = json_decode(getDokmetext($bmm),true);
							if(!empty(getTextersal($bmm))){
								$txtt = getTextersal($bmm);
								$ch13 = str_text($txtt,1);
$txt=$ch13;
								}else{
								$txt="یک دکمه را انتخاب کنید";
								}
								if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($bmm);
							$getall = getallvalue("dok","dokme");
		foreach($getall as $ke){
			if(getvalue("dok","dokme",$ke,"hidden")=="on"){
				$key=str_replace('"'.$ke.'"','""',$key);
				}
			}
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								sm($chatid,$txt,$keykar);
							}
	}
	
		elseif(!empty(getDokme($text)) and empty(getvalue("user","chatid",$fromid,"step"))){
			
			if(getDokmenok($text)=="getApi"){
				step($chatid,"getApi$text");
				setCode($chatid,$text);
				if(!empty(getvalue("hashmoh","text",$text,"hash"))){
				$drop = getvalue("hashmoh","text",$text,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
				if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	  }
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
		}
		ToDie();
				}elseif(getDokmenok($text)=="jostojo"){
					step($chatid,"jostojo$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="search"){
					step($chatid,"search$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="چه چیزی را میخواهید سرچ کنید?!";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="change"){
					step($chatid,"change$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا ایدی عددی فردی که میخواهید برای ان امتیاز ارسال کنید را وارد کنید :";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="enfjar"){
					step($chatid,"enfjar$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="👈 لطفا تعداد سکه ای که میخواهید شرط ببندید را وارد کنید :";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="createbot"){
					step($chatid,"createbot$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا توکن ربات را برای ساخت ربات ارسال نمایید :";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="updatebot"){
					step($chatid,"updatebot$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
					}
					ToDie();
					}elseif(getDokmenok($text)=="deletebot"){
					step($chatid,"deletebot$text");
					setCode($chatid,$text);
				if(isset($dokme["getmoh$text"]) && isset($dokme["getmoh$text"][$cg])){
				$teext = $dokme["getmoh$text"]["$cg"];
				sm($chatid,$teext,$keyback);
				}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا ربات خود را از روی کیبورد انتخاب کنید :";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = "برگشت↪";
			}else{
				$keyback = getvalue("data","id",1,"barmoh");
				}
				$keyboard = getvaluee("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"'.$keyback.'"}]],"resize_keyboard":true}';
		sm($chatid,$ch13,$keykarbar);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="sendadmin"){
					step($chatid,"sendadmin$text");
					setCode($chatid,$text);
					if(!empty(getvalue("hashmoh","text",$text,"hash"))){
					$drop = getvalue("hashmoh","text",$text,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="schannel"){
					step($chatid,"schannel$text");
					setCode($chatid,$text);
					if(!empty(getvalue("hashmoh","text",$text,"hash"))){
					$drop = getvalue("hashmoh","text",$text,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
	if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}elseif(getDokmenok($text)=="fchannel"){
					step($chatid,"fchannel$text");
					setCode($chatid,$text);
					if(!empty(getvalue("hashmoh","text",$text,"hash"))){
					$drop = getvalue("hashmoh","text",$text,"hash");
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}}else{
					if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
					}
					ToDie();
					}
				elseif(getDokmenok($text)=="getmatntaki"){
					
				step($chatid,"getmatntaki$text");
				setCode($chatid,$text);
				if(!empty(getvalue("hashmoh","text",$text,"hash"))){
				$drop = getvalue("hashmoh","text",$text,"hash");
			
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}
				}else{
if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
		if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
				}
				ToDie();
				}elseif(getDokmenok($text)=="getphp"){
					
				step($chatid,"getphp$text");
				setCode($chatid,$text);
				if(!empty(getvalue("hashmoh","text",$text,"hash"))){
				$drop = getvalue("hashmoh","text",$text,"hash");
			
				if(!empty(getallvalue("mohtava$drop","dokme"))){
					setOther($chatid,1);
					$teext = getvalue("mohtava$drop","dokme","TEXT_1","textget");
					$teext= str_text($teext,1);
					if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
				sm($chatid,$teext,$keyback);
				}
				}else{
if(getTextersal($text)==null){
					
					
		$ch13="لطفا متن مورد نظر خود را بفرستید";
		}else{
	$tx=	getTextersal($text);
	$ch13 = str_text($tx,1);
	}
		if(empty(getvalue("data","id",1,"barmoh"))){
			$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
			}else{
				$keyback = json_encode([
'keyboard'=>[
[["text"=>getvalue("data","id",1,"barmoh")]]
],
'resize_keyboard'=>true
]);
				}
		sm($chatid,$ch13,$keyback);
		ToDie();
				}
				ToDie();
				}elseif(getDokmenok($text)=="coin"){
				$tet=getDokmetext($text);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `emtiaz` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `emtiaz` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getCoin($fg);
					if(!empty(getvalue("dok","dokme",$text,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$text,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="
";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	ToDie();
				}elseif(getDokmenok($text)=="bartarin"){
				$tet=getDokmetext($text);
				$list="";
				//$sql ="SELECT * FROM `user$userbott` ORDER BY `zirmaj` LIMIT 10";
				$sql = "SELECT * from `user$userbott` ORDER BY `zirmaj` desc LIMIT 10";
				$result = mysqli_query($con,$sql);
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$x=1;
				foreach($rows as $ks){
					$fg = $ks["chatid"];
					$fname = getvalue("user","chatid",$fg,"firstname");
					$lname = getvalue("user","chatid",$fg,"lastname");
					$username = getvalue("user","chatid",$fg,"username");
					$member = getvalue("user","chatid",$fg,"zirmaj");
					$tcoin = getvalue("user","chatid",$fg,"emtiaz");
					$coin = getZirmaj($fg);
					if(!empty(getvalue("dok","dokme",$text,"themebartarin"))){
						$ttx = getvalue("dok","dokme",$text,"themebartarin");
						$r1 = array("T-FIRSTNAME","T-LASTNAME","T-USERNAME","T-USERID","T-RANGE","T-MEMBER","T-COIN");
						$r2 = array($fname,$lname,$username,$fg,$x,$member,$tcoin);
						$list .= str_replace($r1,$r2,$ttx);
$list .="\n";
						}else{
					$list .="<b>$x _</b> <a href='tg://user?id=$fg'>$fname</a> => <b>$coin</b>\n\n";
					}
					$x++;
					}
					$tet = str_replace("BC",$list,$tet);
				$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	ToDie();
				}elseif(getDokmenok($text)=="php"){
     $get=getDokmetext($text);
     $array1 = array('$FIRSTNAME','$LASTNAME','$USERNAME','$USERID','$PHONE','$BIO','$IDBOT','$BOTUSER','$BOTNAME','$GPNAME','$GPUSER','$CHATID','$DESCRIOPTION','$GETDICE','$DICE','$MESSAGEID','$COIN','$MEMBER','$LINK','$ALLMEM','$HOUR','$MINUTE','$SECOND','$JOINDATEM','$JOINDATESH','$JOINTIME','$TIME','$YEAR','$MONTH','$DAY','$DATESH','$DATEM','$BOTMEM','$TEXT','$ADD');
 $array2 = array('"'.$firstname.'"','"'.$lastname.'"','"'.$username.'"',$fromid,'"'.$phone_number.'"','"'.$bio.'"',$idbot,'"'.$botuser.'"','"'.$botname.'"','"'.$gpname.'"','"'.$gpuser.'"','"'.$chatid.'"','"'.$description.'"',getOther($chatid),getDice($chatid),$messageid,getCoin($chatid),getZirmaj($chatid),'"https://t.me/'.$botuser.'?start='.$fromid.'"','"'.getDokother2($text).'"','"'.date('H').'"','"'.date('i').'"','"'.date('s').'"','"'.getJoindatem($fromid).'"','"'.getJoindatesh($fromid).'"','"'.getJointime($fromid).'"','"'.date("H:i:s").'"','"'.date("Y").'"','"'.date("m").'"','"'.date("d").'"','"'.$datesh.'"','"'.$datem.'"',amarcount("user"),'"'.$text.'"',"");
 $code=str_replace($array1,$array2,$get);
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://rextester.com/rundotnet/Run");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"LanguageChoice=8&Program=$code&CompilerArgs=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
$ge=json_decode($server_output,true);
if(!empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }elseif(empty($ge["Errors"]) && !empty($ge["Result"])){
$sg =  sm($chatid,$ge["Result"]);
 }else{
  $sg = sm($chatid,$ge["Errors"]);
  }
  $messageid= $sg->result->message_id;
  if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
  ToDie();
     }
	elseif(getDokmenok($text)=="matntaki"){
		
	$get = json_decode(getDokmetext($text),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	if(strpos($tet,"EDITMSG")){
		$ex=explode("EDITMSG",$tet);
		$tet=$ex[0];
		$edit=$ex[1];
		}
		
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	$sg = sm($chatid,$texxt,$kei);
	if(isset($edit)){
		$sd = $edit;
		$vb=str_replace("(","",$sd);
		$vb=str_replace(")","",$vb);
		$exp=explode(",",$vb);
		$x=1;
		foreach($exp as $key){
			$ch13 = str_text($key,1);
			em($chatid,$ch13,$messageid+1);
			}
		}
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			$sg = ss($chatid,$tet);
			}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
			ToDie();
	}elseif(getDokmenok($text)=="matntartib"){
		$get = json_decode(getDokmetext($text),true);
		step($chatid,"matntartib$text");
				setCode($chatid,$text);
				if(empty(getvalue("dok","dokme",$text,"textemtiaz1"))){
					$dokmee="پست بعدی⏩";
					}else{
						$dokmee=getvalue("dok","dokme",$text,"textemtiaz1");
						}
						if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
									$keybo=json_encode([
									'keyboard'=>[
									[["text"=>$dokmee]],
									[["text"=>$back]]
									],
									'resize_keyboard'=>true,
									]);
									setvalue("dok","dokme",$text,"textemtiaz2",0);
						$nok=$get[0];
						$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,1);
		$capp=str_replace("/r/n/r","\n",$capp);
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$keybo);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$capp,$keybo);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$capp,$keybo);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$capp,$keybo);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$capp,$keybo);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet,$keybo);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$capp);
		sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$capp);
			sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="dice"){
			
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			sm($chatid,"برای ادامه دکمه ی زیر را بزنید :",$keybo);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$capp,$keybo);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
			ToDie();
		}elseif(getDokmenok($text)=="matnchand"){
		$get = json_decode(getDokmetext($text),true);
$x = 0;
foreach ($get as $nok){
	if($x==10){
	sleep(1);
$x=0;	
	}
	$x++;
$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
	
				$capp = str_text($capp,1);
		$capp=str_replace("/r/n/r","\n",$capp);
	if(preg_match("/(%)([^\']+)(%)/",$capp,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$capp);
 $capp = $hi[0];
  $kei = textToinline("%$k%",$capp);
  } 
	if($type=="text"){
			
				$ch13 = str_text($tet,1);
				$ch13=str_replace("/r/n/r","\n",$ch13);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
	$sg = sm($chatid,$ch13,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$capp,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$capp,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$capp,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$capp,$kei);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$capp);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$capp);
			}elseif($type=="dice"){
			
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$capp,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
}ToDie();
		}elseif(getDokmenok($text)=="matnrand"){
		$get = json_decode(getDokmetext($text),true);
$cou = count($get)-1;
$rand=rand(1,$cou);
$type = $get[$rand]['type'];
	$tet = $get[$rand]['text'];
	$capp = $get[$rand]['caption'];
	
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	$sg = sm($chatid,$texxt,$kei);
	}elseif($type=="photo"){
		$sg = sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		$sg = sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		$sg = sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		$sg = svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
		$sg = ss($chatid,$tet);
		}elseif($type=="contact"){
		$sg = sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		$sg = svin($chatid,$tet);
		}elseif($type=="location"){
			$sg = slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$sg = sdi($chatid,$tet);
			$get = $sg->result->dice->value;
			setOther($fromid,$get);
			$em= $sg->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			$sg = sd($chatid,$tet,$cap,$kei);
			}
			$messageid= $sg->result->message_id;
			if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
			ToDie();
		}elseif(getDokmenok($text)=="Api"){
							$gget = json_decode(getDokmetext($text),true);
				$get=$gget['text'];
				if(strpos($get,"||")){
					$exp=explode("||",$get);
				$get =$exp[0];
					$cap=$exp[1];
    $cap13 = str_text($cap,1);
	if(preg_match("/(%)([^\']+)(%)/",$cap13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$cap13);
 $cap13 = $hi[0];
  $kei = textToinline("%$k%",$cap13);
  } 
}
$txtt = $get;
			$ch13 = str_text($txtt,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
if(validImage($ch13)=="image"){
							if(strpos(typee($ch13),"gif")!==false){
							$sg = sd($chatid,$ch13,$cap13,$kei);
							}else{
							$sg = sp($chatid,$ch13,$cap13,$kei);
							}
							}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="video"){
$sg = sv($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="audio"){
$sg = sa($chatid,$ch13,$cap13,$kei);
}elseif(validImage($ch13)=="application"){
	if(strpos(typee($ch13),"json")!==false){
		$xc = file_get_contents($ch13);
						$sg = sm($chatid,$xc.$cap13,$kei);
		}else{
$sg = sd($chatid,$ch13,$cap13,$kei);
}
}else{
	if(getimagesize("$ch13")==true){
		$sg = sp($chatid,$ch13,$cap13,$kei);
		}else{
								$xc = file_get_contents($ch13);
						$sg = sm($chatid,$xc.$cap13,$kei);
						}
						}
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
						ToDie();
		}elseif(getDokmenok($text)=="rss"){
							$get = json_decode(getDokmetext($text),true);
							$text=$get['text'];
							
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
						$xc = getrss($text);
						$sg = sm($chatid,$xc,$kei);
						$messageid= $sg->result->message_id;
						if(getvalue("dok","dokme",$text,"autodel")=="on"){
		$now = time();
					insert("delete","`chm`,`id`,`time`","'$chatid-$messageid','$text','$now'");
					}
					ToDie();
						}elseif(getDokmenok($text)=="back"){
							if(!empty(getTextersal($text))){
								$txtt = getTextersal($text);
								$ch13 = str_text($txtt,1);
$txt=$ch13;
								}else{
								$txt="به عقب برگشتید :";
								}
$key = getvalue("dok","dokme",$text,"keyback");
if($key=="منوی اصلی" || empty($key)){
	sm($chatid,$txt,$keykarbar);
	}else{
		if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($key);
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"resize_keyboard":'.$sizekol.'}';
								sm($chatid,$txt,$keykar);
								ToDie();
		}
	ToDie();
}elseif(getDokmenok($text)=="newdokme"){
								$get = json_decode(getDokmetext($text),true);
							if(!empty(getTextersal($text))){
								$txtt = getTextersal($text);
								$ch13 = str_text($txtt,1);
$txt=$ch13;
								}else{
								$txt="یک دکمه را انتخاب کنید";
								}
								if(!empty($backname)){
									$back = $backname;
									}else{
										$back= "برگشت به خانه";
									}
							$key=getDokmetext($text);
							$getall = getallvalue("dok","dokme");
		foreach($getall as $ke){
			if(getvalue("dok","dokme",$ke,"hidden")=="on"){
				$key=str_replace('"'.$ke.'"','""',$key);
				}
			}
			if(!empty(getvalue("dok","dokme",$text,"field"))){
				$field = getvalue("dok","dokme",$text,"field");
				}else{
					$field = null;
					}
								$keykar='{"keyboard":['.$key.',[{"text":"'.$back.'"}]],"input_field_placeholder":"'.$field.'","resize_keyboard":'.$sizekol.'}';
								sm($chatid,$txt,$keykar);
								
								ToDie();
							}
							ToDie();
	}elseif($text=="برگشت به خانه" || (!empty($backname) && $text==$backname)){
		if($text==$backname){
			$txtt=getvalue("data","id",1,"txtback");
			}else{
				$txtt=getvalue("data","id",1,"txtback");
				}
		//$tet="به خانه برگشتید :";
		//$txt = $data['textback'];
		
		$ch13 = str_text($txtt,1);
sm($chatid,$ch13,$keykarbar);
ToDie();
		}elseif($chatid==$admin && $text=="ورود به پنل🔧"){
			
			/*$vb = getEditer();
						if(!empty(getDokother($vb))){
						setDokother($vb,null);
						}
						*/
	sm($chatid,getTxtstartpanel(),$keypanel);
	step($chatid,"panel");
			
			
		}
		elseif(!empty(getadmin($fromid)) && $text=="ورود به پنل🔧"){
		
			/*$vb = getEditer();
						if(!empty(getDokother($vb))){
						setDokother($vb,null);
						}
						*/
	sm($chatid,getTxtstartpanel(),$keypanel);
	step($chatid,"panel");
			
			
		}
		elseif(!empty(getadmin($fromid)) ||$chatid==$admin){
if ($text == "/panel"){
	$vb = getEditer();
						if(!empty(getDokother($vb))){
						setDokother($vb,null);
						}
	sm($chatid,getTxtstartpanel(),$keypanel);
	step($chatid,"panel");
	}elseif($step=="panel"){
				if($text=="ویرایش متن ها✏"){
					if(getvalue("admin","chatid",$chatid,"editmatn")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							}else{
					step($chatid,"editmatnha");
					$txt="
					یک گزینه را از لیست انتخاب کنید :
					
					";
					sm($chatid,$txt,$keyedit);
					}}elseif($text=="ارسال همگانی📣"){
						if(getvalue("admin","chatid",$chatid,"ersal")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							}else{
					step($chatid,"sendtoall");
					$txt="
					لطفا یک گزینه را انتخاب کنید :
					";
					sm($chatid,$txt,$keyersal);
					}}elseif($text=="پاسخ خودکار🔉"){
						if(getvalue("admin","chatid",$chatid,"pasokh")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
					step($chatid,"pasokh");
					$txt="
					لطفا یک گزینه را انتخاب کنید :
					";
					sm($chatid,$txt,$keypasokh);
					
					}
}elseif($text=="بخش ادمین ها👤"){
						if(getvalue("admin","chatid",$chatid,"addadmin")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
						step($chatid,"adminlist");
						sm($chatid,"لطفا یه گزینه را انتخاب کنید",$keyadmin);
						
						}
							}elseif($text=="وارد کردن کد رونوشت📥"){
								if(getvalue("admin","chatid",$chatid,"addcodero")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
	step($chatid,"addcodecopy");
$txt="🔰لطفا کد رو نوشت را کپی و کد را کامل برای ما بفرستید

⚠️توجه کنید تمام اطلاعات ربات بجز آمار ربات به ربات جدید ارتقا پیدا میکند!!";
sm($chatid,$txt,$keyback);
}
}elseif($text=="کد رونوشت🔏"){
	if(getvalue("admin","chatid",$chatid,"getcodero")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
	step($chatid,"getcopycode");
if(empty(getvaluee("copycode","userbot",$userbott,"code"))){
	$code = md5(random(20));
	insertt("copycode","`code`,`userbot`","'$code','$userbott'");
	$txt="کد رونوشت ساخته شد✅

<code>$code</code>

⚠️توجه کنید کد را در اختیار افراد بیجا قرار ندهید
درصورت وارد کردن کد ‌، اخرین اطلاعات ربات شما کپی خواهد شد‼️";
sm($chatid,$txt,$keycopy);
	}else{
		$code = getvaluee("copycode","userbot",$userbott,"code");
	$txt="کد رونوشت شما از قبل ایجاد شده هست✅

<code>$code</code>

⚠️توجه کنید کد را در اختیار افراد بیجا قرار ندهید
درصورت وارد کردن کد ‌، اخرین اطلاعات ربات شما کپی خواهد شد‼️";
sm($chatid,$txt,$keycopy);
		}
}
}
elseif($text=="بخش دیتابیس🗃"){
						if(getvalue("admin","chatid",$chatid,"database")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
						step($chatid,"database");
						sm($chatid,"لطفا یه گزینه را انتخاب کنید",$keydata);
						
						}
							}elseif($text=="📨پیامرسان سراسری"){
						if(getvalue("admin","chatid",$chatid,"pmresanall")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
						step($chatid,"pmresanall");
						$pm="خاموش⛔";
						if(getvalue("data","id",1,"pmresanall")=="on"){
							$pm = "روشن✅";
							}
						sm($chatid,"حالت پیامرسان سراسری $pm میباشد :\n\nلطفا یک گزینه را انتخاب کنید :",$keypmresanall);
						
						}
							}elseif($text=="⚠تنظیم ضداسپم"){
						if(getvalue("admin","chatid",$chatid,"zedspam")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
						step($chatid,"zedspam");
						if(empty(getvalue("data","id",1,"spam"))){
							setvalue("data","id",1,"spam","off");
							}
							if(empty(getvalue("data","id",1,"spamsecond"))){
							setvalue("data","id",1,"spamsecond",5);
							}
							if(empty(getvalue("data","id",1,"spamtedad"))){
							setvalue("data","id",1,"spamtedad",5);
							}
							if(empty(getvalue("data","id",1,"spamban"))){
							setvalue("data","id",1,"spamban",60);
							}
							if(getvalue("data","id",1,"spam")=="off"){
							$spam="خاموش🚫";
							}else{
								$spam="روشن✅";
								}
								$second = getvalue("data","id",1,"spamsecond");
								$tedad = getvalue("data","id",1,"spamtedad");
								$ban = getvalue("data","id",1,"spamban");
								$txt="⚠ضد اسپم ربات  <b>$spam</b>  میباشد

🚨کاربر میتواند در هر <b>$second </b> ثانیه در ربات حداکثر <b>$tedad</b>  دستور ارسال کند .

🚧در غیر این صورت کاربر به مدت زمان  <b> $ban </b>  ثانیه  از رباتومحدود میشود .

شما میتوانید از دکمه های زیر ، این قسمت ها را تغییر دهید👇👇👇";
sm($chatid,$txt,$keyspam);

						}
							}elseif($text=="ریست کامل ربات♻"){
								if(getvalue("admin","chatid",$chatid,"resetbot")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
					step($chatid,"Resetbot");
					$txt="با این کار تمام دکمه های شما به همراه محتوا پاک میشوند و به حالت عادی برمیگردند\n\nآیا از انجام این کار مطمعن هستید ؟!";
					
					sm($chatid,$txt,$keynoyes);
					
					}
						}elseif($text=="کانال سازنده💰"){
sm($chatid,"@$channel_sazande_id");
}elseif($text=="سازنده ربات🛠"){
sm($chatid,"این ربات توسط رباتساز $channel_name_fa ساخته شده است 🛠

🆔 : @$bot_creator_id");
}elseif($text=="آمار ربات💯"){
							if(getvalue("admin","chatid",$chatid,"amarbot")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
							step($chatid,"amarbot");
					$cou=amarcount("user");
					$supercou=amarcount("supergroup");
					$gpcou=amarcount("group");
					$sccou=amarcount("channel");
					$couu=amarcount("blocklist");
					$count=amarcount("dok");
					$list = "💠ده کاربر اخیر ربات\n\n-------------------\n";
					$sql = "SELECT * from `user$userbott` ORDER BY `chatid` DESC LIMIT 10";
$result = mysqli_query($con,$sql);
if(mysqli_query($con,$sql)){
	$result = mysqli_query($con,$sql);
	}else{
		sm($admin,"Error :".mysqli_error($con));
		}
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
foreach($rows as $row){
   if($row['username']){
   	$name ="@".$row['username'];
   }else{
   $name = "<a href='tg://openmessage?user_id=".$row['userid']."'>".$row['firstname']."</a>";
   }
   $list .= "$name\n";
}
}
					$zi = "SELECT SUM(`zirmaj`) FROM `user$userbott`";
					if(mysqli_query($con,$zi)){
						$result = mysqli_query($con,$sql);
						$row = mysqli_fetch_array($result);
$zir = $row['zirmaj'];
	}else{
		sm($admin,"Error :".mysqli_error($con));
		}
					
					$txt="$list\n------------------\n💠آمار کاربران : $cou نفر\n💠آمار گروه : $gpcou\n💠آمار سوپرگروه : $supercou\n💠آمار کانال : $sccou\n💠آمار زیرمجموعه : $zir\n💠آمار بلاک : $couu نفر\n💠دکمه های ساخته شده : $count دکمه";
					sm($chatid,$txt,$keyamar);
					
					}
}elseif($text=="سایر تنظیمات🔆"){
	if(getvalue("admin","chatid",$chatid,"sayersetting")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
						step($chatid,"sayersetting");
						if(getLockjoin()=="on"){
							$join =  "روشن✅";
							}else{
							$join = "خاموش🚫";
							}
							if(getBotpower()=="off"){
							$joirn =  "روشن✅";
							}else{
							$joirn = "خاموش🚫";
							}
							if(getvalue("data","id",1,"replacetext")=="on"){
							$replace =  "روشن✅";
							}else{
							$replace = "خاموش🚫";
							}
							if(getvalue("data","id",1,"filter")=="on"){
							$filter =  "روشن✅";
							}else{
							$filter = "خاموش🚫";
							}
							if(getvalue("data","id",1,"lockrobot")=="on"){
							$robot =  "روشن✅";
							}else{
							$robot = "خاموش🚫";
							}
							if(getvalue("data","id",1,"captha")=="on"){
							$captha =  "روشن✅";
							}else{
							$captha = "خاموش🚫";
							}
							if(getvalue("data","id",1,"lockphone")=="on"){
							$phon =  "روشن✅";
							}else{
							$phon = "خاموش🚫";
							}
							if(getvalue("data","id",1,"phoneiran")=="on"){
							$phon2 =  "روشن✅";
							}else{
							$phon2 = "خاموش🚫";
							}
							if(getvalue("data","id",1,"autodel")=="on"){
							$autodel =  "روشن✅";
							}else{
							$autodel = "خاموش🚫";
							}
							if(getvalue("data","id",2,"lockphone")=="on"){
							$qoflersal =  "روشن✅";
							}else{
							$qoflersal = "خاموش🚫";
							}
							if(getvalue("data","id",1,"sendlog")=="on"){
							$logchn =  "روشن✅";
							}else{
							$logchn = "خاموش🚫";
							}
							if(getvalue("data","id",2,"botpower")=="on"){
							$autoonoff =  "روشن✅";
							}else{
							$autoonoff = "خاموش🚫";
							}
							if(getvalue("data","id",1,"webview")=="on"){
							$webview =  "فعال✅";
							}else{
							$webview = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"typing")=="on"){
							$istype =  "فعال✅";
							}else{
							$istype = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"resize")=="on"){
							$dokide =  "فعال✅";
							}else{
							$dokide = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"replymessage")=="on"){
							$replyto =  "فعال✅";
							}else{
							$replyto = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"sendtomail")=="on"){
							$sendmail =  "فعال✅";
							}else{
							$sendmail = "غیرفعال🚫";
							}
							


$txt="
💠وضعیت اکنون : 

💠ربات $joirn هست
💠روشن خاموش خودکار $autoonoff هست
➖➖➖➖➖➖➖➖➖➖
💠جوین اجباری ربات $robot هست
💠جوین اجباری کانال $join هست
💠قفل با کپچا $captha هست
💠قفل با شماره اجباری $phon هست
💠قفل با شماره اجباری ایران $phon2 هست
💠قفل با ارسال پیامک $qoflersal هست
➖➖➖➖➖➖➖➖➖➖
💠کانال Log $logchn هست
💠جایگزین متن خودکار $replace هست
💠حذف خودکار کلمات $autodel هست
💠فیلتر کلمات $filter هست
💠ارسال ایمیل $sendmail هست
➖➖➖➖➖➖➖➖➖➖
💠وضعیت وب ویو متن $webview هست
💠وضعیت در حال نوشتن $istype هست
💠سایز ایده ال دکمه ها $dokide هست
💠ریپلی پیام های ربات $replyto هست
➖➖➖➖➖➖➖➖➖➖
لطفا یک گزینه را انتخاب کنید :
";
sm($chatid,$txt,$keysayer);
}
							}
elseif($text=="بررسی بروزرسانی💠"){
$sql = "SELECT * FROM `update` ORDER BY `noskhe` DESC LIMIT 1";
if(mysqli_query($con,$sql)){
$quer=mysqli_query($con,$sql);
while ($row = mysqli_fetch_array($quer)) {
$daa[] = $row["noskhe"];
}
foreach($daa as $key){
	$cv = $key;
	}
if($updating==$cv){
	sm($chatid,"شما جدیدترین نسخه را نصب دارید✅\n\n💠نسخه شما : $cv");
	}else{
		$query = "SELECT * FROM `update` WHERE `noskhe`='$cv' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$text=  $row["update"];
}
step($chatid,"barrasiberoz");
$keypad = json_encode([
'keyboard'=>[
[["text"=>"♻آپدیت ربات"]],
[["text"=>"برگشت↪"]],
],
'resize_keyboard'=>true
]);
sm($chatid,"نسخه شما اپدیت نیست🚫\n\n💠نسخه فعلی : $updating\n💠نسخه جدید : $cv\nـــــــــــــــــــــــــــ\n♻تغییرات جدید : \n$text",$keypad);
}
}else{
	sm($chatid,"Error for ".mysqli_error($con));
	}
}
elseif($text=="خروج از پنل🏠"){
							$key=json_encode([
							"hide_keyboard"=>true
							]);
							sm($chatid,"از پنل خارج شدید",$key);
							/*
								if(!empty(getadmin($fromid)) || $fromid==$admin ){
				step($chatid,"");
				}else{
			step($chatid,"");
			}
			*/
			step($chatid,"");
			if(getvalue("data","id",1,"forwardstart")=="on"){
			if(!empty(getvalue("data","id",1,"forwardid"))){
				$fo = getvalue("data","id",1,"forwardid");
				$exp= explode("&",$fo);
				$userid=$exp[0];
				$messid=$exp[1];
				fm($chatid,$userid,$messid);
				}
			}
			$txtt=getStartMessage();
			$ch13 = str_text($txtt,1);
sm($chatid,$ch13,$keykarbar);

								}elseif($text=="افزودن دکمه جدید🔧"){
									if(getvalue("admin","chatid",$chatid,"adddokme")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
							step($chatid,"create");
						//	$txt="برای ساخت دکمه انتخاب کنید :";
						$txt="
						🔩برای ساخت دکمه یکی از گزینه های زیر را انتخاب کنید :

★اگر روی اسم دکمه های از قبل ساخته شده کلیک کنید ، دکمه در سمت راست آن ساخته خواهد شد★

★از اسم تکراری نمیتوانید استفاده کنید★
						";
							sm($chatid,$txt,$keycreatorb);
								
									}}elseif($text=="ویرایش دکمه ها✂"){
										if(getvalue("admin","chatid",$chatid,"editdokme")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
								step($chatid,"edit");
								$txt="برای ویرایش دکمه ها از دکمه های زیر انتخاب کنید :";
								$keyboard=getKeyboard();
			$keo='{"keyboard":['.$keyboard.',[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
	
								sm($chatid,$txt,$keo);
										
										}
}elseif($text=="🔩تنظیمات گروه"){
	if(getvalue("admin","chatid",$chatid,"group")=="off"){
							sm($chatid,"شما دسترسی به این بخش را ندارید🚫");
							
							}else{
											step($chatid,"groupsetting");
											
												if(getNewozv() == "on"){
													$ozv = "فعال✅";
													}else{
													$ozv = "🚫غیرفعال";
													}
												if(getDeletelink() == "on"){
													$link = "فعال✅";
													}else{
													$link = "🚫غیرفعال";
													}
												if(getKeygroup == "on"){
													$pm = "فعال✅";
													}else{
													$pm = "🚫غیرفعال";
													}
													if(getvalue("data","id",1,"leavegroup") == "on"){
													$leave = "فعال✅";
													}else{
													$leave = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"leavegroup") == "on"){
													$dast = "فعال✅";
													}else{
													$dast = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"deletelink") == "on"){
													$usernam = "فعال✅";
													}else{
													$usernam = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"newozv") == "on"){
													$forw = "فعال✅";
													}else{
													$forw = "🚫غیرفعال";
													}
												
												
											$txt="💠نمایش کیبورد در گروه : $pm\n💠پیام خوش آمدگویی : $ozv\n💠لینک پاک کن : $link\n💠ضد یوزرنیم : $usernam\n💠ضد فوروارد : $forw\n💠لفت گروه خودکار : $leave\n💠تعیین دسترسی ادمین و مدیران گروه : $dast\n\nبرای فعال و غیرفعال کردن از گزینه های زیر انتخاب کنید :
											";
											sm($chatid,$txt,$keygroup);
											}
											}
			else{
			sm($chatid,"این دستور موجود نیست⛔\n\nاز دستورات پنل استفاده کنید :");
			}
		
		}elseif($step=="barrasiberoz"){
			if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						
				}else{
					if($text=="♻آپدیت ربات"){
						if(!empty(getvaluee("qw$chatid","bot",$userbott,"token"))){
														$token = getvaluee("qw$chatid","bot",$userbott,"token");
														$file=file_get_contents("../../Haji.php");
														$mn = preg_replace('/\"(U)SERBOT\"/','"'.$userbott.'"',$file);
											$ch1 = preg_replace('/\"(A)PITOKENBOT\"/','"'.$token.'"',$mn);
											$ch2 = preg_replace('/\"(A)DMINBOT\"/','"'.$admin.'"',$ch1);
												file_put_contents("../../BotList/$userbott/$userbott.php",$ch2);
													step($chatid,"panel");
													$txt="ربات شما با موفقیت آپدیت شد✅";
												sm($chatid,$txt,$keypanel);
												$send="یک ربات اپدیت شد✅\n\nاپدیت شده توسط خودش\nایدی ربات : @$userbott";
		file_get_contents("https://api.telegram.org/bot$token_pedar/sendMessage?chat_id=$channel_token_id&text=".urlencode($send));
																}
			}
			}
			}
elseif($step=="pmresanall"){
if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						
				}else{
					if($text=="تغییر متن رسید📩"){
							step($chatid,"pmresantext");
							$txt="⭐️متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

 ";
sm($chatid,$txt,$keyback);

							}elseif($text=="فعال کردن✅"){
								setvalue("data","id",1,"pmresanall","on");
								$txt="سیستم پیامرسان سراسری فعال شد✅";
								sm($chatid,$txt);
								
								}elseif($text=="غیرفعال کردن⛔"){
								setvalue("data","id",1,"pmresanall","off");
								$txt="سیستم پیامرسان سراسری غیرفعال شد✅";
								sm($chatid,$txt);
								
								}
					}
}elseif($step=="zedspam"){
if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}else{
					if($text=="تنظیم ثانیه اسپم⏳"){
						step($chatid,"spamsecond");
						$tedad = getvalue("data","id",1,"spamtedad");
						$txt="⚠️لطفا بگویید کاربر هر چند ثانیه میتواند تعداد حداکثر $tedad دستور ارسال کند :

حداقل <b> 5</b> و حداکثر <b> 20</b>  ثانیه مجاز میباشد";
sm($chatid,$txt,$keyback);
						}elseif($text=="تنظیم حداکثر دستور🛂"){
						step($chatid,"spamtedad");
						$second = getvalue("data","id",1,"spamsecond");
						$txt="⚠️لطفا بگویید کاربر در هر <b> $second </b> ثانیه ، حداکثر چند دستور میتواند در ربات ارسال کند ؟!

حداقل <b> 5</b> و حداکثر <b> 20</b>  دستور مجاز میباشد";
sm($chatid,$txt,$keyback);
						}elseif($text=="تنظیم مدت سکوت🔇"){
						step($chatid,"spamban");
						$ban = getvalue("data","id",1,"spamban");
						$txt=" ⚠️لطفا بگویید کاربر در زمان اسپم ، چند ثانیه از ربات محروم شود ؟!!

زمان فعلی : <b> $ban </b>";
sm($chatid,$txt,$keyback);
						}elseif($text=="تنظیم متن اسپم✏"){
							step($chatid,"spamtext");
							$txt="⭐️متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

 ";
sm($chatid,$txt,$keyback);
							}elseif($text=="فعال کردن✅"){
								setvalue("data","id",1,"spam","on");
								$txt="ضداسپم با موفقیت روشن شد✅";
								sm($chatid,$txt);
								}elseif($text=="غیرفعال کردن⛔"){
								setvalue("data","id",1,"spam","off");
								$txt="ضداسپم با موفقیت خاموش شد✅";
								sm($chatid,$txt);
								}
					}
}elseif($step=="spamsecond"){
if($text=="برگشت↪"){
				step($chatid,"zedspam");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyspam);
				}else{
					if($text >= 5 && $text <= 20){
						step($chatid,"zedspam");
						setvalue("data","id",1,"spamsecond",$text);
						sm($chatid,"تنظمیات جدید ثبت شد✅",$keyspam);
						}else{
							sm($chatid,"⚠لطفا یک عدد بین 5 تا 20 وارد نمایید !!!");
							}
					}
}elseif($step=="spamtedad"){
if($text=="برگشت↪"){
				step($chatid,"zedspam");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyspam);
				}else{
					if($text >= 5 && $text <= 20){
						setvalue("data","id",1,"spamtedad",$text);
						step($chatid,"zedspam");
						sm($chatid,"تنظمیات جدید ثبت شد✅",$keyspam);
						}else{
							sm($chatid,"⚠لطفا یک عدد بین 5 تا 20 وارد نمایید !!!");
							}
					}
}elseif($step=="spamban"){
if($text=="برگشت↪"){
				step($chatid,"zedspam");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyspam);
				}else{
					if(preg_match('/^[0-9]+$/',$text)){
						step($chatid,"zedspam");
						setvalue("data","id",1,"spamban",$text);
						sm($chatid,"تنظمیات جدید ثبت شد✅",$keyspam);
						}else{
							sm($chatid,"⚠ورودی فقط عدد مجاز هست!!!");
							}
					}
}elseif($step=="spamtext"){
if($text=="برگشت↪"){
				step($chatid,"zedspam");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyspam);
				}else{
					if(isset($text)){
						step($chatid,"zedspam");
						setvalue("data","id",1,"spamtext",$text);
						sm($chatid,"تنظمیات جدید ثبت شد✅",$keyspam);
						}else{
							sm($chatid,"⚠ورودی فقط متن مجاز هست!!!");
							}
					}
}elseif($step=="pmresantext"){
if($text=="برگشت↪"){
				step($chatid,"pmresanall");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypmresanall);
				}else{
					if(isset($text)){
						step($chatid,"pmresanall");
						setvalue("data","id",1,"pmresantext",$text);
						sm($chatid,"متن جدید ثبت شد✅",$keypmresanall);
						}else{
							sm($chatid,"⚠ورودی فقط متن مجاز هست!!!");
							}
					}
}elseif($step=="addpasokh1"){
if($text=="برگشت↪"){
				step($chatid,"pasokh");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypasokh);
				}else{
					$gt = getvalue("pasokh","pasokh",$text,"pasokh");
						if(empty($gt)){
							step($chatid,"addpasokh2");
							setOther2($chatid,$text);
							sm($chatid,"🌟خوب حالا برای پاسخ $text ، ربات چه جوابی بدهد؟!!\n\n★میتوانید از عکس،فیلم،موزیک،استیکر،گیف،ویس،فایل و متن استفاده کنید\n★همچنین میتوانید از کد های html هم استفاده کنید\n★شما میتوانید از دکمه های شیشه ای هم استفاده کنید\n★شما میتوانید از پیشفرض های ربات استفاده نمایید\n\n ");
							}else{
								step($chatid,"addpasokh3");
							setOther2($chatid,$text);
							sm($chatid,"💢شما درحال تغییر جواب برای پاسخ $text هستید , جواب جدید را بفرستید :\n\n★میتوانید از عکس،فیلم،موزیک،استیکر،گیف،ویس،فایل و متن استفاده کنید\n★همچنین میتوانید از کد های html هم استفاده کنید\n★شما میتوانید از دکمه های شیشه ای هم استفاده کنید\n★شما میتوانید از پیشفرض های ربات استفاده نمایید\n\n ");
							
								}
					}
}elseif($step=="addpasokh3"){
	$vb = getOther2($chatid);
	if($text=="برگشت↪"){
								step($chatid,"pasokh");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypasokh);
			}else{
				if(isset($text)){
				$xc=([
					"type"=>"text",
					"text"=>$text
					]);
					
				setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($photo)){
						if(isset($caption)){
							$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"photo","text"=>"$photo","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($video)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"video","text"=>"$video","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($document)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"document","text"=>"$document","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($sticker)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"sticker","text"=>"$sticker","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($voice)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"voice","text"=>"$voice","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($audio)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"audio","text"=>"$audio","caption"=>$cap]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($dice)){
						
						
					$xc=(["type"=>"dice","text"=>$dice]);
					setvalue("pasokh","pasokh",$vb,"javab",addslashes(json_encode($xc)));
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}else{
					$txt="
					این فرمت پشتیبانی نمیشود !!!\nلطفا از فرمت های دیگر استفاده کنید :
					";
					sm($chatid,$txt);
					
					}
					}
	
}elseif($step=="addpasokh2"){
	$vb = getOther2($chatid);
	if($text=="برگشت↪"){
								step($chatid,"pasokh");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypasokh);
			}else{
				if(isset($text)){
				$xc=([
					"type"=>"text",
					"text"=>$text
					]);
					
				insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($photo)){
						if(isset($caption)){
							$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"photo","text"=>"$photo","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($video)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"video","text"=>"$video","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($document)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"document","text"=>"$document","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($sticker)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"sticker","text"=>"$sticker","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($voice)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"voice","text"=>"$voice","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($audio)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"audio","text"=>"$audio","caption"=>$cap]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}elseif(isset($dice)){
						
						
					$xc=(["type"=>"dice","text"=>$dice]);
					insert("pasokh","`pasokh`,`javab`","'$vb','".addslashes(json_encode($xc))."'");
					step($chatid,"pasokh");
					$txt="پاسخ و جواب شما ثبت شد✅\n\nبه منوی قبل برگشتید :";
					sm($chatid,$txt,$keypasokh);
					}else{
					$txt="
					این فرمت پشتیبانی نمیشود !!!\nلطفا از فرمت های دیگر استفاده کنید :
					";
					sm($chatid,$txt);
					
					}
					}
	
}elseif($step=="rempasokh1"){
	if($text=="برگشت↪"){
								step($chatid,"pasokh");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypasokh);
			}else{
				
				if(!empty(getvalue("pasokh","pasokh",$text,"pasokh"))){
					step($chatid,"pasokh");
					deletevalue("pasokh","pasokh",$text);
					sm($chatid,"پاسخ $text به همراه جواب حذف شد✅",$keypasokh);
					}else{
						sm($chatid,"🚫پاسخ $text یافت نشد\n\nلطفا با دقت امتحان کنید :");
						}
				}
	}
	elseif($step=="listjavab"){
	if($text=="برگشت↪"){
								step($chatid,"pasokh");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypasokh);
			}else{
				
				if(!empty(getvalue("pasokh","pasokh",$text,"pasokh"))){
					$get = json_decode(getvalue("pasokh","pasokh",$text,"javab"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	
				$ch13 = str_text($capp,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch13 = str_text($tet,1);
	if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
  $k = $m[2];
 $hi= preg_split("/(%)([^\']+)(%)/",$ch13);
 $ch13 = $hi[0];
  $kei = textToinline("%$k%",$ch13);
  } 
$texxt=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texxt,$kei);
	}elseif($type=="photo"){
		sp($chatid,$tet,$cap,$kei);
		}elseif($type=="video"){
		sv($chatid,$tet,$cap,$kei);
		}elseif($type=="audio"){
		sa($chatid,$tet,$cap,$kei);
		}elseif($type=="voice"){
		svo($chatid,$tet,$cap,$kei);
		}elseif($type=="sticker"){
			ss($chatid,$tet);
			}elseif($type=="contact"){
		sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		svin($chatid,$tet);
		}elseif($type=="location"){
			slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			$gett = sdi($chatid,$tet);
			$get = $gett->result->dice->value;
			setOther($fromid,$get);
			$em= $gett->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			sd($chatid,$tet,$cap,$kei);
			}
}else{
						sm($chatid,"🚫پاسخ $text یافت نشد\n\nلطفا با دقت امتحان کنید :");
						}
				}
	}
elseif($step=="pasokh"){
if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="افزودن پاسخ➕"){
					step($chatid,"addpasokh1");
					sm($chatid,"✳لطفا پاسخ خود را ارسال کنید\n\n🚫⚠پاسخ باید حتما متن باشد.",$keyback);
					}elseif($text=="حذف پاسخ➖"){
						$gt = getallvalue("pasokh","pasokh");
						if(empty($gt)){
							sm($chatid,"⚠هنوز پاسخی ثبت نشده است");
							}else{
					step($chatid,"rempasokh1");
					sm($chatid,"✳لطفا پاسخ خود را ارسال کنید\n\n🚫⚠پاسخ شما باید از قبل ثبت شده باشد.",$keyback);
					}
					}
elseif($text=="لیست پاسخ ها👁‍🗨"){
						$gt = getallvalue("pasokh","pasokh");
						if(empty($gt)){
							sm($chatid,"⚠هنوز پاسخی ثبت نشده است");
							}else{
								$list = "🔆پاسخ های ثبت شده ی شما :\n\n";
								foreach($gt as $k){
								$list .= "$k || ";
									}
									$list .= "\n\n⚠برای دیدن جواب های ثبت شده از قسمت دریافت جواب استفاده نمایید.";
									sm($chatid,$list);
								}
						}elseif($text=="دریافت جواب➿"){
							$gt = getallvalue("pasokh","pasokh");
							if(empty($gt)){
							sm($chatid,"⚠هنوز پاسخی ثبت نشده است");
							}else{
								step($chatid,"listjavab");
					sm($chatid,"✳لطفا پاسخ خود را ارسال کنید\n\n🚫⚠پاسخ شما باید از قبل ثبت شده باشد.",$keyback);
					
								}
							}elseif($text=="روشن✅"){
								setvalue("data","id",1,"autoanswer","on");
								sm($chatid,"حالت پاسخ خودکار روشن شد✅");
								}elseif($text=="خاموش🚫"){
								setvalue("data","id",1,"autoanswer","off");
								sm($chatid,"حالت پاسخ خودکار خاموش شد✅");
								}

}elseif($step=="joinrobot"){
			if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
				}elseif($text=="🔒فعال کردن"){
					$txt="جوین اجباری ربات با موفقیت فعال شد🔒";
					setvalue("data","id",1,"lockrobot","on");
					sm($chatid,$txt);
					}elseif($text=="🔓غیرفعال کردن"){
					$txt="جوین اجباری ربات با موفقیت غیرفعال شد🔓";
					setvalue("data","id",1,"lockrobot","off");
					sm($chatid,$txt);
					}elseif($text=="➕افزودن ربات"){
						step($chatid,"joinrobot2");
			$txt="💠لطفا یوزرنیم ربات را بدون @  وارد کنید :

⚠️لطفا توجه داشته باشید ربات باید حتما توسط $channel_name_fa ساخته شده باشد.";
sm($chatid,$txt,$keyback);
						}elseif($text=="➖حذف ربات"){
							$co = count(getAllBot());
							if($co == 0){
								$txt="هیچ رباتی هنوز شما ثبت نکرده اید🚫";
								sm($chatid,$txt);
								}else{
									step($chatid,"deleterobot");
							$txt="لطفا یوزرنیم ربات را بدون @ وارد کنید : ";
sm($chatid,$txt,$keyback);
								}
							}elseif($text=="🔘لیست ربات ها"){
								$co = count(getAllBot());
							if($co == 0){
								$txt="هیچ رباتی هنوز شما ثبت نکرده اید🚫";
								sm($chatid,$txt);
								}else{
									$list = "لیست ربات های شما : \n";
									$dc = getAllBot();
	foreach($dc as $key){
	
		$list .= "$key | ";
	
	}
	sm($chatid,$list);
									}
								
								}
				
					}elseif($step=="deleterobot"){
						if($text=="برگشت↪"){
			step($chatid,"joinrobot");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyrobot);
			}else{
if(!empty(getBot($text))){
	deleteBot($text);
	step($chatid,"joinrobot");
sm($chatid,"ربات شما با موفقیت از لیست حذف شد✅",$keyrobot);
	}else{
		$txt="📛این ربات در لیست وجود ندارد

⚠️ابتدا لیست ربات های خود را با دکمه ی لیست ربات ها دریافت نمایید و پس از مطمعن شدن دوباره امتحان کنید";
	sm($chatid,$txt);
	}
				}
							
								}elseif($step=="joinrobot2"){
									if($text=="برگشت↪"){
				step($chatid,"joinrobot");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyrobot);
				}else{
					if(!empty(getvaluee("amarbot","bot",$text,"bot"))){
						setOther($chatid,$text);
				step($chatid,"joinrobot3");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

 ";
				sm($chatid,$txt,$keyback);
						}else{
							sm($chatid,"📛این ربات جزو رباتهای ساخته شده ی $channel_name_fa نیست!!!

⚠️لطفا از صحت یوزرنیم ربات دقت حاصل فرمایید :

همچنین یوزرنیم ربات باید بدون @ فرستاده شود :");
							}
					
					}
									}elseif($step=="joinrobot3"){
										if($text=="برگشت↪"){
				step($chatid,"joinrobot");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyrobot);
				}else{
					if(isset($text)){
						$user = getOther($chatid);
	insert("robot","`user`,`text`","'$user','$text'");
	step($chatid,"joinrobot");
	$txt="ربات شما با موفقیت ثبت شد✅\n\nبا دکمه های فعال کردن و غیرفعال کردن ، اقدام به تنظیم جوین اجباری کنبد :";
	sm($chatid,$txt,$keyrobot);
						
										}
										}
										}
elseif($step=="joinchannel"){
			if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
				}elseif($text=="🔒فعال کردن"){
					$txt="جوین اجباری کانال با موفقیت فعال شد🔒";
					setLockjoin("on");
					sm($chatid,$txt);
					}elseif($text=="🔓غیرفعال کردن"){
					$txt="جوین اجباری کانال با موفقیت غیرفعال شد🔓";
					setLockjoin("off");
					sm($chatid,$txt);
					}elseif($text=="➕افزودن کانال"){
						step($chatid,"joinchannel2");
			$txt="🔰لطفا ابتدا ربات را در کانال خود ادمین نمایید.

سپس یوزرنیم کانال را را برای ما بفرستید

مثال 🆔 : @$channel_name_en

اگر کانال شما خصوصی میباشد ، ایدی عددی کانال را برای ما بفرستید .
 
مثال 🆔 : -1002135620077

🔴توجه داشته باشید اگر ربات را از ادمینی کانال بردارید ، دکمه ی شما پس از جوین کاربر در کانال شما همچنان قفل خواهد ماند.";
sm($chatid,$txt,$keyback);
						}elseif($text=="➖حذف کانال"){
							$co = count(getAllchannel());
							if($co == 0){
								$txt="هیچ کانالی هنوز شما ثبت نکرده اید 🚫";
								sm($chatid,$txt);
								}else{
									step($chatid,"deletechannel");
							$txt="لطفا یوزرنیم کانال خود را وارد کنید


اگر کانال شما خصوصی میباشد ، ایدی عددی آن را وارد کنید.";	
								sm($chatid,$txt,$keyback);
								}
							}elseif($text=="🔘لیست کانال ها"){
								$co = count(getAllchannel());
							if($co == 0){
								$txt="هیچ کانالی هنوز شما ثبت نکرده اید 🚫";
								sm($chatid,$txt);
								}else{
									$list = "لیست کانال های شما : \n";
									$dc = getAllchannel();
	foreach($dc as $key){
	
		$list .= "$key | ";
	
	}
	sm($chatid,$list);
									}
								
								}
				
					}elseif($step=="deletechannel"){
						if($text=="برگشت↪"){
			step($chatid,"joinchannel");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keychannel);
			}else{
if(!empty(getChannel($text))){
	deleteChannel($text);
	step($chatid,"joinchannel");
sm($chatid,"کانال شما با موفقیت از لیست حذف شد✅",$keychannel);
	}else{
	$txt="این کانال در لیست وجود ندارد🚫\n\n💠جهت مطمعن شدن میتوانید ایدی یا یوزرنیم خود را از لیست کانال ها دریافت نمایید";
	sm($chatid,$txt);
	}
				}
							
								}elseif($step=="joinchannel2"){
									if($text=="برگشت↪"){
			step($chatid,"joinchannel");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keychannel);
			}else{
			if(preg_match("/^(\@)(.*)$/",$text)){
				preg_match("/^(\@)(.*)$/",$text,$mat);
				$user = $mat[2];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setOther($chatid,$user);
				step($chatid,"joinchannel3");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

 ";
				sm($chatid,$txt,$keyback);
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
				//$stat = $url2->result->status;
							}elseif(preg_match('/^\-[0-9]+$/',$text)){
								preg_match('/^\-[0-9]+$/',$text,$mat);
				$user = $mat[0];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setOther($chatid,$user);
					step($chatid,"joinchannel3");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

 ";
				sm($chatid,$txt,$keyback);
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
								
								}else{
							$txt="⛔️فرمت یوزرنیم یا ایدی عددی ارسال شده اشتباه میباشد!

❗️لطفا یوزرنیم را همراه با @ برای ما بفرستید 
مثال 🆔 : @$channel_name_en
مثال 🆔 : -1002135620077"
;
				sm($chatid,$txt);		
								}
			}
							}elseif($step=="joinchannel3"){
								if($text=="برگشت↪"){
			step($chatid,"joinchannel");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keychannel);
			}else{
if(isset($text)){
	$user = getOther($chatid);
	insert("chan","`user`,`text`","'$user','$text'");
	step($chatid,"joinchannel");
	$txt="کانال شما با موفقیت ثبت شد✅\n\nبا دکمه های فعال کردن و غیرفعال کردن ، اقدام به تنظیم جوین اجباری کنبد :";
	sm($chatid,$txt,$keychannel);
	}
				}
									}elseif($step=="adddatabasekol"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(empty(getvalue("datatype","name",$text,"name"))){
						insert("datatype","`name`,`type`","'$text','kol'");
						step($chatid,"database");
						$txt="دیتابیس کلی شما با موفقیت ساخته شد✅


➕شما از این پس میتوانید با دستور 
GETDB_$text
مقدار را برای کاربران نمایش دهید

➕همچنین با دستور زیر 
SETDB_$text(مقدار)
مقدار جدیدی را در دیتابیس ثبت نمایید


⚠️درحالت دیتابیس کلی ، اگر مقدار تغییر کند برای تمام کاربران ربات تغییر میکند";
						sm($chatid,$txt,$keydata);
						}else{
							$txt="⚠️این دیتابیس از قبل وجود دارد!!!

لطفا از نام دیگری استفاده کنید :";
sm($chatid,$txt);
							}
						}else{
							$txt="📛مشکل در ساخت دیتابیس!!

⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
					}
}elseif($step=="adddatabasekar"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(empty(getvalue("datatype","name",$text,"name"))){
						insert("datatype","`name`,`type`","'$text','kar'");
						step($chatid,"database");
						$txt="دیتابیس کاربر شما با موفقیت ساخته شد✅


➕شما از این پس میتوانید با دستور 
GETDB_$text
مقدار را برای کاربران نمایش دهید

➕همچنین با دستور زیر 
SETDB_$text(مقدار)
مقدار جدیدی را در دیتابیس ثبت نمایید


⚠️درحالت دیتابیس کاربر ، اگر مقدار تغییر کند فقط برای همان کاربر تغییر پیدا میکند";
						sm($chatid,$txt,$keydata);
						}else{
							$txt="⚠️این دیتابیس از قبل وجود دارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="📛مشکل در ساخت دیتابیس!!

⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
					}
}elseif($step=="adddatabasescore"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(empty(getvalue("datatype","name",$text,"name"))){
						insert("datatype","`name`,`type`","'$text','score'");
						step($chatid,"database");
						$txt="دیتابیس کاربر شما با موفقیت ساخته شد✅


➕شما از این پس میتوانید با دستور 
GETDB_$text
مقدار را برای کاربران نمایش دهید

➕همچنین با دستور زیر 
SETDB_$text(مقدار)
مقدار جدیدی را در دیتابیس ثبت نمایید


⚠️درحالت دیتابیس امتیاز ، اگر مقدار تغییر کند ، فقط برای همان کاربر تغییر خواهد کرد\n\n⚠در  دیتابیس امتیاز فقط عدد پشتیبانی میشود  و درصورت فرستادن حروف ، هیچ اثری رو مقدار دیتابیس نخواهد داشت.";
						sm($chatid,$txt,$keydata);
						}else{
							$txt="⚠️این دیتابیس از قبل وجود دارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="📛مشکل در ساخت دیتابیس!!

⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
					}
}elseif($step=="dastdatabasekar"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							$type= getvalue("datatype","name",$text,"type");
if($type=="kar"){
							setOther($chatid,$text);
							step($chatid,"dastdatabasekar2");
							sm($chatid,"💠لطفا ایدی عددی شخص را وارد کنید :

⚠️توجه کنید کاربر باید از قبل دیتابیس برای آن ساخته شده باشد",$keyback);
							}else{
								sm($chatid,"این یک دیتابیس کاربر نیست⛔\nلطفا دوباره تلاش فرمایید");
								}
}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}elseif($step=="dastdatabasekar2"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[0-9]+$/',$text)){
						$val = getOther($chatid);
						if(!empty(getvalue("datalist","name",$text.$val,"name"))){
							setOther2($chatid,$text);
							step($chatid,"dastdatabasekar32");
							sm($chatid,"💠لطفا متنی که میخواهید برای این دیتابیس کاربر به ثبت برسد را بفرستید:


⚠️توجه کنید این متن فقط برای دیتابیس کاربر مورد تغییر خواهد یافت.",$keyback);
							}else{
							$txt="این کاربر وجود ندارد یا اینکه هنوز برای آن دیتابیسی مقدار دهی نشده است⛔";
sm($chatid,$txt);
							}
						}
						}
}elseif($step=="dastdatabasescore"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							$type= getvalue("datatype","name",$text,"type");
if($type=="score"){
							setOther($chatid,$text);
							step($chatid,"dastdatabasekar2");
							sm($chatid,"💠لطفا ایدی عددی شخص را وارد کنید :

⚠️توجه کنید کاربر باید از قبل دیتابیس برای آن ساخته شده باشد",$keyback);
							}else{
								sm($chatid,"این یک دیتابیس کاربر نیست⛔\nلطفا دوباره تلاش فرمایید");
								}
}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}elseif($step=="dastdatabasescore2"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[0-9]+$/',$text)){
						$val = getOther($chatid);
						if(!empty(getvalue("datalist","name",$text.$val,"name"))){
							setOther2($chatid,$text);
							step($chatid,"dastdatabasescore32");
							sm($chatid,"💠لطفا مقداری که میخواهید برای دیتابیس امتیاز این کاربر به ثبت برسد را وارد کنید :


⚠️توجه کنید فقط عدد قابل قبول هست!!",$keyback);
							}else{
							$txt="این کاربر وجود ندارد یا اینکه هنوز برای آن دیتابیسی مقدار دهی نشده است⛔";
sm($chatid,$txt);
							}
						}
						}
}elseif($step=="dastdatabasescore32"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match("/^[0-9]+$/",$text)){
$db = getOther($chatid);
$user = getOther2($chatid);
setvalue("datalist","name",$user.$db,"value",$text);
step($chatid,"database");
sm($chatid,"مقدار دیتابیس با موفقیت تغییر یافت✅",$keydata);
ToDie();
}else{
	sm($chatid,"فقط عدد قابل قبول هست⛔");
	}
}

		}elseif($step=="dastdatabase"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							setOther2($chatid,$text);
							step($chatid,"dastdatabase2");
							sm($chatid,"💠حالا متنی که میخواهید ثبت شود را وارد کنید :

⚠️توجه کنید متن برای تمام کاربران اعمال میشود :");
							}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}elseif($step=="dastdatabasekar32"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(isset($text)){
$db = getOther($chatid);
$user = getOther2($chatid);
setvalue("datalist","name",$user.$db,"value",$text);
step($chatid,"database");
sm($chatid,"مقدار دیتابیس با موفقیت تغییر یافت✅",$keydata);
ToDie();
}
}

		}elseif($step=="dastdatabase2"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(isset($text)){
$db = getOther2($chatid);
$type= getvalue("datatype","name",$db,"type");
if($type=="kol"){
	if(empty(getvalue("datalist","name",$db,"name"))){
						insert("datalist","`name`,`value`","'$db','$text'");
						}else{
	setvalue("datalist","name",$db,"value",$text);
	}
}else{
		$all = getallvalue("datalist","name");
		foreach($all as $key){
			if(preg_match('/^([0-9]+'.$db.')$/',$key)){
				
				setvalue("datalist","name",$key,"value",$text);
				}
			}
		}
		step($chatid,"database");
		$txt="مقدار دیتابیس با موفقیت تغییر یافت✅

⚠️توجه کنید اگر دیتابیس از نوع کاربر باشد ، تا زمانی که کاربر از دیتابیس استفاده ای نکند ، تغییرات برای آن اعمال نخواهد شد!!
⚠️مقدار دیفالت در صورت استفاده نکردن کاربر از دیتابیس 0 میباشد";
		sm($chatid,$txt,$keydata);
}
}
}elseif($step=="remdatabase"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							$type= getvalue("datatype","name",$text,"type");
				if($type=="kol"){
					deletevalue("datalist","name",$text);
					deletevalue("datatype","name",$text);
					}else{
						$all = getallvalue("datalist","name");
						foreach($all as $key){
							if(preg_match('/^[0-9]+('.$text.')$/',$key)){
								deletevalue("datalist","name",$key);
								}
							}
							deletevalue("datatype","name",$text);
						}
						step($chatid,"database");
						$txt="دیتابیس شما با موفقیت به همراه اطلاعات ذخیره شده حذف شد✅";
						sm($chatid,$txt,$keydata);
							}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}elseif($step=="getDatakol"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							$type= getvalue("datatype","name",$text,"type");
				if($type=="kol"){
					$matm= getvalue("datalist","name",$text,"value");
					if(empty($matm)){
						$matm="0";
						}
					$txt="مقدار ثبت شده برای دیتابیس کلی $text :\n\n<code>$matm</code>";
					sm($chatid,$txt,$keydata);
					step($chatid,"database");
					}else{
						$txt="⛔این یک دیتابیس کلی نیست!!!\n⚠اگر دیتابیس امتیاز یا کاربر میباشد ، از قسمت دریافت مقدار دیتابیس کاربر و امتیاز اقدام به دریافت کنید .";
						sm($chatid,$txt);
						}
							}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}elseif($step=="getDatakar"){
if($text=="برگشت↪"){
				step($chatid,"database");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydata);
				}else{
					if(preg_match('/^[A-Z0-9]+$/',$text)){
						if(!empty(getvalue("datatype","name",$text,"name"))){
							$type= getvalue("datatype","name",$text,"type");
				if($type!="kol"){
					$all = getallvalue("datalist","name");
					$tt="";
						foreach($all as $key){
							if(preg_match('/^[0-9]+('.$text.')$/',$key)){
								$user = str_replace($text,"",$key);
								$firstname = getvalue("user","chatid",$user,"firstname");
								$matm= getvalue("datalist","name",$key,"value");
					if(!empty($matm)){
								$tt .= "<b><a href='tg://user?id=$user'>$firstname</a></b> : <code><b>$matm</b></code>";
								}
								}
							
								}
								$txt="مقدار ثبت شده برای دیتابیس کاربر یا امتیاز $text :\n\n$tt";
					$cvbb = str_split($txt, 4095);
						foreach($cvbb as $vm){
						sm($chatid,$vm);
						}
					sm($chatid,"لیست پایان یافت✅\n\n⚠اگر فردی در لیست نباشد ، یعنی اینکه هیچ مقداری برای ان کاربر ثبت نشده است!!",$keydata);
					step($chatid,"database");
							
					}else{
						$txt="⛔این یک دیتابیس کاربر یا امتیاز نیست!!!\n\n⚠اگر دیتابیس کلی میباشد ، از قسمت دریافت مقدار دیتابیس کلی اقدام به دریافت مقدار کنید  .";
						sm($chatid,$txt);
						}
							}else{
							$txt="⚠️این دیتابیس در لیست وجود ندارد!!!

لطفا از نام دیگری استفاده کنید :";
							sm($chatid,$txt);
							}
						}else{
							$txt="
⚠️نام دیتابیس فقط میتواند شامل حروف بزرگ انگلیسی و عدد باشد";
sm($chatid,$txt);
							}
						}
}
elseif($step=="database"){
if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="افزودن دیتابیس کلی➕"){
					step($chatid,"adddatabasekol");
					sm($chatid,"💠لطفا نام دیتابیس را وارد کنید :


⚠️توجه کنید نام دیتابیس فقط میتواند شامل حروف انگلیسی بزرگ و عدد باشد!",$keyback);
					}elseif($text=="افزودن دیتابیس کاربر➕"){
					step($chatid,"adddatabasekar");
					sm($chatid,"💠لطفا نام دیتابیس را وارد کنید :


⚠️توجه کنید نام دیتابیس فقط میتواند شامل حروف انگلیسی بزرگ و عدد باشد!",$keyback);
					}elseif($text=="افزودن دیتابیس امتیازی➕"){
					step($chatid,"adddatabasescore");
					sm($chatid,"💠لطفا نام دیتابیس را وارد کنید :


⚠️توجه کنید نام دیتابیس فقط میتواند شامل حروف انگلیسی بزرگ و عدد باشد!",$keyback);
					}elseif($text=="مقدار دهی دیتابیس📝"){
					step($chatid,"dastdatabase");
					sm($chatid,"💠لطفا در ابتدا نام دیتابیس را وارد کنید :

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید :",$keyback);
					}elseif($text=="دریافت مقدار دیتابیس کلی💠"){
					step($chatid,"getDatakol");
					sm($chatid,"💠لطفا در ابتدا نام دیتابیس را وارد کنید :

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید :",$keyback);
					}elseif($text=="دریافت مقدار دیتابیس کاربر و امتیاز💠"){
					step($chatid,"getDatakar");
					sm($chatid,"💠لطفا در ابتدا نام دیتابیس را وارد کنید :

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید :",$keyback);
					}elseif($text=="اپدیت مقدار دیتابیس کاربر♻"){
					step($chatid,"dastdatabasekar");
					sm($chatid,"💠لطفا در ابتدا نام دیتابیس را وارد کنید :

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید :",$keyback);
					}elseif($text=="اپدیت مقدار دیتابیس امتیازی🔢"){
					step($chatid,"dastdatabasescore");
					sm($chatid,"💠لطفا در ابتدا نام دیتابیس را وارد کنید :

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید :",$keyback);
					}elseif($text=="حذف دیتابیس➖"){
						step($chatid,"remdatabase");
					sm($chatid,"💠لطفا نام دیتابیس را برای حذف وارد کنید : 

⚠️توجه کنید درصورت حذف ، برای کاربران مقدار خالی نمایش داد میشود!

⚠️نام دیتابیس را بصورت حروف بزرگ ارسال کنید",$keyback);		
						}elseif($text=="لیست دیتابیس ها🔘"){
							$list = getallvalue("datatype","name");
							$co = count($list);
							if($co == 0){
								sm($chatid,"🛑شما هنوز هیچ دیتابیسی نساخته اید!!

➕شما میتوانید با دکمه های افزودن دیتابیس به دو صورت کلی و کاربر اقدام به ساخت دیتابیس کنید.");
								}else{
							$tex ="➕لیست دیتابیس های ساخته شده توسط شما :
name= نام دیتابیس
type = نوع دیتابیس


name       |     type\n\n";
							$x=1;
							foreach($list as $key){
								$val = getvalue("datatype","name",$key,"type");
								if($val == "kar"){
									$nok = "User";
}elseif($val =="kol"){
	$nok = "All";
	}elseif($val=="score"){
		$nok = "Score";
		}
									
							$tex .= "$x.<code>$key</code> |   <b> $nok </b>\n";
							$x++;
							}
							sm($chatid,$tex);
							}
							}
}elseif($step=="adminlist"){
		if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="افزودن ادمین➕"){
					step($chatid,"addadmin");
					sm($chatid,"لطفا ایدی عددی کاربر را وارد کنید",$keyback);
					}
elseif($text=="تعیین دسترسی ادمین🛂"){
					step($chatid,"dastadmin");
					sm($chatid,"لطفا ایدی عددی کاربر را وارد کنید",$keyback);
					}elseif($text=="حذف ادمین➖"){
						step($chatid,"remadmin");
					sm($chatid,"لطفا ایدی عددی کاربر را وارد کنید",$keyback);		
						}elseif($text=="لیست ادمین ها🔘"){
							$list = getallvalue("admin","chatid");
							$co = count($list);
							if($co == 0){
								sm($chatid,"لیست ادمین ها خالی میباشد🚫\n\nبا دستور افزودن ادمین ، برای ربات ادمین تعیین کنید .");
								}else{
							$tex ="💠لیست ادمین های ربات :\nتعداد کاربران : $co\n\n";
							$x=1;
							foreach($list as $key){
								$val = getvalue("user","chatid",$key,"firstname");
							$tex .= "$x. $key | <a href='tg://user?id=$key'>$val</a>\n";
							}
							sm($chatid,$tex);
							}
							}
			}elseif($step=="addadmin"){
				if($text=="برگشت↪"){
				step($chatid,"adminlist");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyadmin);
				}else{
				$k = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getchat?chat_id=$text"),true);
				$res = $k["ok"];
				$id = $k['result']['id'];
				$user = $k['result']['username'];
				$name = $k['result']['first_name'];
				if($res==false){
					$txt="🚫چت ایدی غلط میباشد\nلطفا به موارد زیر دقت فرمایید :\n\n💠توجه داشته باشید چت ایدی شما حتما درست باشد.";
					sm($chatid,$txt);
					}else{
						if(!isset($user)){
						$username = $name;	
							}else{
							$username = $user;	
							}
							step($chatid,"adminlist");
							if(!isetrow("admin","pmresanall")){
	createrow("admin","group","pmresanall","TEXT");
	}
	if(!isetrow("admin","zedspam")){
	createrow("admin","group","zedspam","TEXT");
	}
	if(!isetrow("admin","addcodero")){
	createrow("admin","sayersetting","addcodero","TEXT");
	}
	if(!isetrow("admin","getcodero")){
	createrow("admin","addcodero","getcodero","TEXT");
	}
							insert("admin","`chatid`,`adddokme`,`editdokme`,`amarbot`,`resetbot`,`addadmin`,`pasokh`,`ersal`,`editmatn`,`group`,`pmresanall`,`zedspam`,`sayersetting`,`addcodero`,`getcodero`","'$id','on','on','on','off','off','on','off','on','on','off','off','off','off','off'");
						sm($chatid,"کاربر با موفقیت به لیست ادمین های ربات اضافه شد✅",$keyadmin);
						}
				}
				}elseif($step=="dastadmin"){
				if($text=="برگشت↪"){
				step($chatid,"adminlist");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyadmin);
				}else{
				if(empty(getadmin($text))){
					$txt="این ایدی عددی جزو ادمین های ربات نمیباشد🚫";
					sm($chatid,$txt);
					}else{
						setOther2($chatid,$text);
						step($chatid,"dastadmin2");
						if(getvalue("admin","chatid",$text,"adddokme")=="off"){
							$adddokme="ندارد🚫";
							}else{
								$adddokme="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"editdokme")=="off"){
							$editdokme="ندارد🚫";
							}else{
								$editdokme="دارد✅";
								}
						if(getvalue("admin","chatid",$text,"amarbot")=="off"){
							$amarbot="ندارد🚫";
							}else{
								$amarbot="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"resetbot")=="off"){
							$resetbot="ندارد🚫";
							}else{
								$resetbot="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"addadmin")=="off"){
							$addadmin="ندارد🚫";
							}else{
								$addadmin="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"pasokh")=="off"){
							$pasokh="ندارد🚫";
							}else{
								$pasokh="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"editmatn")=="off"){
							$editmatn="ندارد🚫";
							}else{
								$editmatn="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"ersal")=="off"){
							$ersal="ندارد🚫";
							}else{
								$ersal="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"group")=="off"){
							$group="ندارد🚫";
							}else{
								$group="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"pmresanall")=="off"){
							$pmresanall="ندارد🚫";
							}else{
								$pmresanall="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"zedspam")=="off"){
							$zedspam="ندارد🚫";
							}else{
								$zedspam="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"sayersetting")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"addcodero")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"getcodero")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								$txt="مشخصات ادمین به شکل زیر میباشد : \n\n💠دسترسی به بخش ساخت دکمه $adddokme\n💠دسترسی به بخش ویرایش دکمه $editdokme\n💠دسترسی به بخش آمار ربات $amarbot \n💠دسترسی به بخش ریست ربات $resetbot\n💠دسترسی به بخش افزودن ادمین $addadmin\n💠دسترسی به بخش پاسخ سریع $pasokh\n💠دسترسی به بخش ارسال همگانی $ersal\n💠دسترسی به بخش ادیت متن ها $editmatn\n💠دسترسی به بخش تنظیمات گروه $group\n💠دستری به بخش ضداسپم $zedspam\n💠دسترسی به بخش پیامرسان سراسری $pmresanall\n💠دسترسی به بخش سایر تنظیمات $sayersetting";
								sm($chatid,$txt,$keydast);
						}
				}
				}elseif($step=="dastadmin2"){
					$id=getOther2($chatid);
if($text=="برگشت↪"){
				step($chatid,"adminlist");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyadmin);
				}else{
					if($text=="بروزرسانی دسترسی ها♻"){
						if(getvalue("admin","chatid",$id,"adddokme")=="off"){
							$adddokme="ندارد🚫";
							}else{
								$adddokme="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"editdokme")=="off"){
									
							$editdokme="ندارد🚫";
							}else{
								$editdokme="دارد✅";
								}
						if(getvalue("admin","chatid",$id,"amarbot")=="off"){
							$amarbot="ندارد🚫";
							}else{
								$amarbot="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"resetbot")=="off"){
							$resetbot="ندارد🚫";
							}else{
								$resetbot="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"addadmin")=="off"){
							$addadmin="ندارد🚫";
							}else{
								$addadmin="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"pasokh")=="off"){
							$pasokh="ندارد🚫";
							}else{
								$pasokh="دارد✅";
								}
								
								if(getvalue("admin","chatid",$id,"editmatn")=="off"){
							$editmatn="ندارد🚫";
							}else{
								$editmatn="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"ersal")=="off"){
							$ersal="ندارد🚫";
							}else{
								$ersal="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"group")=="off"){
							$group="ندارد🚫";
							}else{
								$group="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"pmresanall")=="off"){
							$pmresanall="ندارد🚫";
							}else{
								$pmresanall="دارد✅";
								}
								if(getvalue("admin","chatid",$text,"zedspam")=="off"){
							$zedspam="ندارد🚫";
							}else{
								$zedspam="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"sayersetting")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"addcodero")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								if(getvalue("admin","chatid",$id,"getcodero")=="off"){
							$sayersetting="ندارد🚫";
							}else{
								$sayersetting="دارد✅";
								}
								$txt="مشخصات ادمین به شکل زیر میباشد : \n\n💠دسترسی به بخش ساخت دکمه $adddokme\n💠دسترسی به بخش ویرایش دکمه $editdokme\n💠دسترسی به بخش آمار ربات $amarbot \n💠دسترسی به بخش ریست ربات $resetbot\n💠دسترسی به بخش افزودن ادمین $addadmin\n💠دسترسی به بخش پاسخ سریع $pasokh\n💠دسترسی به بخش ارسال همگانی $ersal\n💠دسترسی به بخش ادیت متن ها $editmatn\n💠دسترسی به بخش تنظیمات گروه $group\n💠دستری به بخش ضداسپم $zedspam\n💠دسترسی به بخش پیامرسان سراسری $pmresanall\n💠دسترسی به بخش سایر تنظیمات $sayersetting";
								sm($chatid,$txt,$keydast);
								
						}elseif($text=="افزودن دکمه"){
							step($chatid,"dastadddokme");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="ویرایش دکمه"){
							step($chatid,"dasteditdokme");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="آمار ربات"){
							step($chatid,"dastamarbot");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="ریست ربات"){
							step($chatid,"dastresetbot");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="افزودن ادمین"){
							step($chatid,"dastaddadmin");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="پاسخ خودکار"){
							step($chatid,"dastpasokh");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="ارسال همگانی"){
							step($chatid,"dastersal");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="ویرایش متن ها"){
							step($chatid,"dasteditmatn");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="تنظیمات گروه"){
							step($chatid,"dastgroup");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="ضداسپم"){
							step($chatid,"dastzedspam");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="پیامرسان سراسری"){
							step($chatid,"dastpmresanall");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="سایر تنظیمات"){
							step($chatid,"dastsayersetting");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="کدرونوشت"){
							step($chatid,"dastgetcodero");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}elseif($text=="وارد کردن کدرونوشت"){
							step($chatid,"dastaddcodero");
							sm($chatid,"آیا میخواهید ادمین به این گزینه دسترسی داشته باشد یا خیر?!",$keynoyes);
							}
					}
}elseif($step=="dastadddokme"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"adddokme","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"adddokme","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dasteditdokme"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"editdokme","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"editdokme","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastamarbot"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"amarbot","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"amarbot","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastresetbot"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"resetbot","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"resetbot","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastaddadmin"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"addadmin","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"addadmin","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastpasokh"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"pasokh","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"pasokh","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastersal"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"ersal","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"ersal","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dasteditmatn"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"editmatn","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"editmatn","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastgroup"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"group","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"group","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastzedspam"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"zedspam","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"zedspam","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastpmresanall"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"pmresanall","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"pmresanall","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}elseif($step=="dastsayersetting"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"sayersetting","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"sayersetting","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}
	elseif($step=="dastgetcodero"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"getcodero","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"getcodero","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}
	elseif($step=="dastaddcodero"){
	$id=getOther2($chatid);
	if($text=="خیر🚫"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"addcodero","off");
		sm($chatid,"دسترسی ادمین به این بخش محدود شد✅",$keydast);
		}elseif($text=="بله✅"){
		step($chatid,"dastadmin2");
		setvalue("admin","chatid",$id,"addcodero","on");
		sm($chatid,"دسترسی ادمین به این بخش آزاد شد✅",$keydast);
		}
	}
elseif($step=="remadmin"){
						if($text=="برگشت↪"){
				step($chatid,"adminlist");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyadmin);
				}else{
if(!empty(getadmin($text))){
	deleteAdmin($text);
	step($chatid,"adminlist");
	sm($chatid,"کاربر با موفقیت از لیست ادمین ها حذف شد✅",$keyadmin);
	}else{
		sm($chatid,"این کاربر در لیست ادمین های ربات وجود ندارد🚫");
	}
					}
					}
			elseif($step=="editmatnha"){
			if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="👥ویرایش متن دریافت زیرمجموعه"){
					step($chatid,"txxtzirmaj");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع دریافت زیرمجموعه نمایش داده می شود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback);
					
					}elseif($text=="🔐تغییر متن قفل کد"){
					step($chatid,"txxtopencode");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع باز شدن قفل دکمه با کد نمایش داده می شود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback);
					
					}elseif($text=="🔐تغییر متن بازشدن قفل شماره"){
					step($chatid,"txxtopenshomare");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع باز شدن قفل دکمه با شماره یا شماره ایران نمایش داده می شود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
";
		sm($chatid,$txt,$keyback);
					
					}
			elseif($text=="ویرایش متن شروع✏"){
			step($chatid,"matnshoro");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع زدن دستور /start نمایش داده می شود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback)	;
			}elseif($text=="ویرایش متن بلاک🛂"){
			step($chatid,"txtblock");
			$txt="
		در این قسمت شما میتوانید متنی که برای کاربر زمان بلاک شدن،نمایش داده میشود،را تغییر دهید 
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback)	;
			}elseif($text=="ویرایش متن امتیاز بدو ورود🆕"){
			step($chatid,"txtnewcoin");
			$txt="
		در این قسمت شما میتوانید متنی که برای کاربر برای دریافت اولین امتیاز خود در زمان بد ورود دریافت میکند را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback)	;
			}elseif($text=="متن لفت از گروه👋"){
			step($chatid,"textleft");
			$txt="
		در این قسمت شما میتوانید متنی که موقع لفت ربات نمایش داده میشود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback)	;
			}elseif($text=="ویرایش متن اشتباه🚫"){
				step($chatid,"eshtebah");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع زدن دستور اشتباه نمایش داده می شود را تغییر دهید
از کلمات جایگزین زیر هم میتوانید استفاده نمایید
همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="ویرایش متن برگشت🔙"){
				step($chatid,"txtbargasht");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع زدن دکمه برگشت نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
			";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="تغییر نام دکمه برگشت↪"){
				step($chatid,"namebargasht");
			$txt="Ⓜلطفا اسم دکمه ی جدید وارد کنید :";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="ویرایش پیام عضو جدید گروه🆕"){
				step($chatid,"txtnewozv");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع عضو شدن در گروه نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
GPNAME نام گروه
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
			";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="ویرایش متن برگشت محتواها🔙"){
				step($chatid,"txtbargashtmoh");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع زدن دکمه برگشت نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
			";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="تغییر نام دکمه برگشت محتواها↪"){
				step($chatid,"namebargashtmoh");
			$txt="Ⓜلطفا اسم دکمه ی جدید وارد کنید :";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="ویرایش پیام عضو جدید گروه🆕"){
				step($chatid,"txtnewozv");
			$txt="
			در این قسمت شما میتوانید متنی که برای کاربر موقع عضو شدن در گروه نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
GPNAME نام گروه
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
			";
		sm($chatid,$txt,$keyback)	;
				}elseif($text=="ویرایش متن خاموشی ربات⛔"){
					step($chatid,"txtpower");
			$txt="
			در این قسمت شما میتوانید متنی که موقع خاموش بودن ربات نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
			";
		sm($chatid,$txt,$keyback)	;
					
					}
				
					}elseif($step=="afzayeshzir2"){
						
							if($text=="برگشت↪"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
				}else{
if(preg_match('/^[0-9]+$/',$text)){
	$id = getOther($chatid);
	$old = getZirmaj($id);
	$new = $text;
		$w = $old + $new;
		setZirmaj($id,$w);
		$old = getZirmaj($id);
		step($chatid,"zirmajsetting");
		$txt="مقدار $new زیرمجموعه با موفقیت افزایش یافت✅\n\n💠تعداد فعلی زیر مجموعه ها : <b>$old</b>";
		sm($chatid,$txt,$keyzirmaj);
	}else{
	$txt="🚫ورودی نامعتبر\n\nفقط اعداد 0 تا 9999999 مجاز هستند";
	sm($chatid,$txt);
	}
					}
							}elseif($step=="afzayeshzir"){
							if($text=="برگشت↪"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
				}else{
				if(empty(getvalue("user","chatid",$text,"chatid"))){
					$txt="این کاربر در لیست کاربران ربات وجود ندارد🚫\n\nلطفا دوباره امتحان کنید :";
					sm($chatid,$txt);
					}else{
						setOther($chatid,$text);
						step($chatid,"afzayeshzir2");
					$coin = getZirmaj($text);
					$txt="💠تعداد امتیاز های این کاربر : <b>$coin</b>\n\nچه تعداد امتیاز میخواهید به این کاربر افزوده شود؟!";
	sm($chatid,$txt,$keyback);
									}
				}
							}elseif($step=="kasrzir2"){
						
							if($text=="برگشت↪"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
				}else{
if(preg_match('/^[0-9]+$/',$text)){
	$id = getOther($chatid);
	$old = getZirmaj($id);
	$new = $text;
	if(($old - $new) < 0){
		setZirmaj(0);
		}else{
		$w = $old - $new;
		setZirmaj($id,$w);
		}
		$old = getZirmaj($id);
		step($chatid,"zirmajsetting");
		$txt="مقدار $new زیرمجموعه با موفقیت کسر شد✅\n\n💠تعداد فعلی زیر مجموعه ها : <b>$old</b>";
		sm($chatid,$txt,$keyzirmaj);
	}	else{
	$txt="🚫ورودی نامعتبر\n\nفقط اعداد 0 تا 9999999 مجاز هستند";
	sm($chatid,$txt);
}
					}
							}elseif($step=="kasrzir"){
							if($text=="برگشت↪"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
				}else{
				
				if(empty(getvalue("user","chatid",$text,"chatid"))){
					$txt="این کاربر در لیست کاربران ربات وجود ندارد🚫\n\nلطفا دوباره امتحان کنید :";
					sm($chatid,$txt);
					}else{
						setOther($chatid,$text);
						step($chatid,"kasrzir2");
					$coin =getZirmaj($text);
					$txt="💠تعداد امتیاز های این کاربر : <b>$coin</b>\n\nچه تعداد امتیاز میخواهید از این کاربر کسر شود؟\n\n🅾 اگر کاربر تعداد زیر مجموعه هایش صفر باشد با کسر کردن همچنان صفر باقی خواهد ماند";
	sm($chatid,$txt,$keyback);
									}
				}
							}elseif($step=="seenzir"){
							if($text=="برگشت↪"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
				}else{
				
				if(empty(getvalue("user","chatid",$text,"chatid"))){
					$txt="این کاربر در لیست کاربران ربات وجود ندارد🚫\n\nلطفا دوباره امتحان کنید :";
					sm($chatid,$txt);
					}else{
						$listall = getvalue("user","chatid",$chatid,"zirmajuserid");
						$exp = explode(",",$listall);
						$list = "🌟آمار کاربران 👇👇\n\n";
						foreach($exp as $key){
						$list .= "<code>$key | </code>";
						}
						step($chatid,"zirmajsetting");
						$cvbb = str_split($list, 4095);
						foreach($cvbb as $vm){
						sm($chatid,$vm,$keyzirmaj);
						}
									}
				}
							}elseif($step=="resetzir"){
								if($text=="خیر🚫"){
				step($chatid,"zirmajsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyzirmaj);
						}elseif($text=="بله✅"){
							$tevv = getallvalue("user","chatid");
					foreach($tevv as $key){
							setZirmaj($key,0);
						
						}
						step($chatid,"zirmajsetting");
						$txt="♻ریست زیرمجموعه های کاربران با موفقیت انجام شد✅";
						sm($chatid,$txt,$keyzirmaj);
							}
									}elseif($step=="zirmajsetting"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="ریست زیر مجموعه ها🚮"){
							step($chatid,"resetzir");
							sm($chatid,"🚫با این دکمه تمام زیرمجموعه های کاربران صفر میشود!!\n\nآیا از انجام کار مطمعن هستید؟!",$keynoyes);
							}elseif($text=="افزایش زیرمجموعه⬆️"){
								step($chatid,"afzayeshzir");
								sm($chatid,"لطفا ایدی فردی که میخواهید برای آن سکه واریز کنید را وارد نمایید",$keyback);
								}elseif($text=="کم کردن زیرمجموعه⬇️"){
										step($chatid,"kasrzir");
								sm($chatid,"لطفا ایدی فردی که میخواهید برای آن سکه کسر کنید را وارد نمایید",$keyback);
					
									}elseif($text=="ℹنمایش زیرمجموعه های فرد"){
										step($chatid,"seenzir");
								sm($chatid,"🌟لطفا ایدی عددی فرد را وارد کنید :",$keyback);
					
									}elseif($text=="نمایش لیست زیرمجموعه ها🔤"){
										
				$tevv = getallvalue("user","chatid");
				$list = "لیست زیرمجموعه ها\n\n";
					foreach($tevv as $key){
						$zir = getZirmaj($key);
						if(!empty($zir)){
							$list .= "$key => $zir\n";
							}
						}
						step($chatid,"zirmajsetting");
						$cvbb = str_split($list, 4095);
						foreach($cvbb as $vm){
						sm($chatid,$vm);
						}
										}elseif($text=="ارسال پیام روشن✅"){
								$txt="قابلیت ارسال پیام برای کاربر  بعد از دریافت زیرمجموعه روشن شد✅";
								setSendzirmaj("on");
								sm($chatid,$txt);
								}elseif($text=="ارسال پیام خاموش🚫"){
								$txt="قابلیت ارسال پیام برای کاربر  بعد از دریافت زیرمجموعه خاموش شد🚫";
								setSendzirmaj("off");
								sm($chatid,$txt);
								}
							
								}elseif($step=="afzayeshcoin2"){
						
							if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
if(preg_match('/^[0-9?\.]+$/',$text)){
	$id = getOther($chatid);
	$old = getCoin($id);
	$new = $text;
		$w = $old + $new;
		setCoin($id,$w);
		$old = getCoin($id);
		step($chatid,"coinsetting");
		$txt="مقدار $new امتیاز با موفقیت افزایش یافت✅\n\n💠تعداد فعلی امتیاز ها : <b>$old</b>";
		sm($chatid,$txt,$keycoin);
	}else{
	$txt="🚫ورودی نامعتبر\n\nفقط اعداد 0 تا 9999999 مجاز هستند";
	sm($chatid,$txt);
	}
					}
							}elseif($step=="afzayeshcoin"){
							if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
				if(empty(getvalue("user","chatid",$text,"chatid"))){
					$txt="این کاربر در لیست کاربران ربات وجود ندارد🚫\n\nلطفا دوباره امتحان کنید :";
					sm($chatid,$txt);
					}else{
						setOther($chatid,$text);
						step($chatid,"afzayeshcoin2");
					$coin = getCoin($text);
					$txt="💠تعداد امتیاز های این کاربر : <b>$coin</b>\n\nچه تعداد امتیاز میخواهید به این کاربر افزوده شود؟!";
	sm($chatid,$txt,$keyback);
									}
				}
							}elseif($step=="kasrcoin2"){
						
							if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
if(preg_match('/^[0-9?\.]+$/',$text)){
	$id = getOther($chatid);
	$old = getCoin($id);
	$new = $text;
	if(($old - $new) < 0){
		setCoin(0);
		}else{
		$w = $old - $new;
		setCoin($id,$w);
		}
		$old = getCoin($id);
		step($chatid,"coinsetting");
		$txt="مقدار $new امتیاز با موفقیت کسر شد✅\n\n💠تعداد فعلی امتیاز ها : <b>$old</b>";
		sm($chatid,$txt,$keycoin);
	}	else{
	$txt="🚫ورودی نامعتبر\n\nفقط اعداد 0 تا 9999999 مجاز هستند";
	sm($chatid,$txt);
}
					}
							}elseif($step=="kasrcoin"){
							if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
				
				if(empty(getvalue("user","chatid",$text,"chatid"))){
					$txt="این کاربر در لیست کاربران ربات وجود ندارد🚫\n\nلطفا دوباره امتحان کنید :";
					sm($chatid,$txt);
					}else{
						setOther($chatid,$text);
						step($chatid,"kasrcoin2");
					$coin =getCoin($text);
					$txt="👤تعداد امتیاز های این کاربر : <b>$coin</b>\n\nچه تعداد امتیاز میخواهید از این کاربر کسر شود؟\n\n🅾 اگر کاربر تعداد زیر مجموعه هایش صفر باشد با کسر کردن همچنان صفر باقی خواهد ماند";
	sm($chatid,$txt,$keyback);
									}
				}
							}elseif($step=="resetcoin"){
								if($text=="خیر🚫"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
						}elseif($text=="بله✅"){
							$tevv = getallvalue("user","chatid");
					foreach($tevv as $key){
							setCoin($key,0);
						
						}
						step($chatid,"coinsetting");
						$txt="♻ریست امتیاز های کاربران با موفقیت انجام شد✅";
						sm($chatid,$txt,$keycoin);
							}
									}
elseif($step=="coinsetting"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="♻️ ریست امتیاز ها ♻️"){
							step($chatid,"resetcoin");
							$txt="آیا مطمعن هستید که میخواهید تمام امتیاز های کاربران را ریست کنید ؟
زیرا این عمل غیرقابل بازگشت است ❗️";
							sm($chatid,$txt,$keynoyes);
}elseif($text=="افزودن امتیاز ➕"){
								step($chatid,"afzayeshcoin");
								sm($chatid,"لطفا آیدی عددی فرد را جهت افزایش امتیاز وارد کنید ☑️",$keyback);
								}elseif($text=="کسر امتیاز ➖"){
										step($chatid,"kasrcoin");
								sm($chatid,"لطفا آیدی عددی فرد را جهت کاهش امتیاز وارد کنید ☑️",$keyback);
					
									}elseif($text=="نمایش امتیازات 👁‍🗨"){
										
				$tevv = getallvalue("user","chatid");
				$list = "♻لیست امتیاز های کاربران\n\n";
					foreach($tevv as $key){
						$zir = getCoin($key);
						if(!empty($zir)){
							$list .= "$key => $zir\n";
							}
						}
						$cvbb = str_split($list, 4095);
						foreach($cvbb as $vm){
						sm($chatid,$vm);
						}
						}elseif($text=="امتیاز همگانی📬"){
sm($chatid,"چه تعداد امتیاز میخواهید برای هرکاربر افزوده شود؟!!!\n\n🚫فقط عدد مجاز هست",$keyback);
step($chatid,"forallcoin");
}elseif($text=="پیام امتیاز بدو ورود✅"){
sm($chatid,"پیام امتیاز بدو ورود برای کاربران جدید فعال شد✅");
setvalue("data","id",1,"newcoin","on");
}elseif($text=="پیام امتیاز بدو ورود⛔"){
sm($chatid,"پیام امتیاز بدو ورود برای کاربران جدید غیرفعال شد✅");
setvalue("data","id",1,"newcoin","off");
}elseif($text=="امتیاز بدو ورود🆕"){
sm($chatid,"🔰چه تعداد امتیاز میخواهید برای کاربران جدید بدو ورود به ربات واریز شود؟!\n\n🚫فقط عدد مجاز هست",$keyback);
step($chatid,"badvorod");
}elseif($text=="تعیین امتیاز زیرمجموعه🖊"){
							$taain = "🔰لطفا مشخص کنید که برای هر زیرمجموعه چقد سکه واریز بشه‌؟!


💢فقط عدد مجاز هست
💢اگر 0 بزارید هیچ سکه ای واریز نمیشود";
sm($chatid,$taain,$keyback);
step($chatid,"taincoin");
							
							}
						}elseif($step=="forallcoin"){
if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
							if(preg_match('/^[0-9?\.]+$/',$text)){
								$get = getallvalue("user","chatid");
								foreach($get as $key){
									$old = getCoin($key);
$new = $text;
		$w = $old + $new;
		setCoin($key,$w);
									}
									step($chatid,"coinsetting");
									sm($chatid,"مقدار $text امتیاز به همه ی کاربران ربات اضافه شد✅\n\nبه عقب برگشتید :",$keycoin);
								
								}
								}
}elseif($step=="badvorod"){
if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
							if(preg_match('/^[0-9?\.]+$/',$text)){
								setvalue("data","id",1,"tedadcoin",$text);
								step($chatid,"coinsetting");
								sm($chatid,"از این به بعد برای هر کاربری که تازه به ربات اضافه شود ، مقدار $text امتیاز به آن تعلق میگیرد",$keycoin);
								}
}
}elseif($step=="taincoin"){
							if($text=="برگشت↪"){
				step($chatid,"coinsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycoin);
				}else{
							if(preg_match('/^[0-9?\.]+$/',$text)){
								step($chatid,"coinsetting");
								setvalue("data","id",1,"zirmajcoin",$text);
								$txt="🔆از این به بعد برای هر زیرمجموعه $text امتیاز به حساب کاربری فرد افزوده میشود✅";
								sm($chatid,$txt,$keycoin);
								}
							}
				}elseif($step=="addforward"){
					if($text=="برگشت↪"){
				step($chatid,"tanzimforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyforward);
						}else{
$matn = "$chatid&$messageid";
setvalue("data","id",1,"forwardid",$matn);
step($chatid,"tanzimforward");
sm($chatid,"متن فورواردی شما با موفقیت ثبت شد✅",$keyforward);
}

}elseif($step=="tanzimforward"){
					if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="🔘افزودن فوروارد🔘"){
								step($chatid,"addforward");
								$txt="لطفا متن فوروارد خود را بفرستید تا در ربات ثبت شود :\n\nمتن شما میتواند فورواردی باشد :";
								sm($chatid,$txt,$keyback);
								}elseif($text=="روشن🟢"){
									sm($chatid,"فوروارد شروع با موفقیت روشن شد✅");
									setvalue("data","id",1,"forwardstart","on");
									}elseif($text=="خاموش🔴"){
										sm($chatid,"فوروارد شروع با موفقیت خاموش شد✅");
									setvalue("data","id",1,"forwardstart","off");
										}
							}
					}elseif($step=="sendToMail"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعال✅"){
								setvalue("data","id",1,"sendtomail","on");
								sm($chatid,"گزینه ی ارسال به ایمیل برای شما فعال شد✅");
								}elseif($text=="غیرفعال⛔"){
									setvalue("data","id",1,"sendtomail","off");
								sm($chatid,"گزینه ی ارسال به ایمیل برای شما غیرفعال شد✅");
									}elseif($text=="تنظیم ایمیل دریافت📧"){
										step($chatid,"sendToMail2");
										sm($chatid,"💠لطفا ایمیل خود را با فرمت صحیح وارد کنید :",$keyback);
										}
							}
						}elseif($step=="sendToMail2"){
						if($text=="برگشت↪"){
				step($chatid,"sendToMail");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keymail);
						}else{
if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',$text)){
	setvalue("data","id",2,"sendtomail",$text);
	step($chatid,"sendToMail");
sm($chatid,"ایمیل شما با موفقیت ثبت شد✅",$keymail);
	}else{
		sm($chatid,"
⛔فرمت ارسالی اشتباه می باشد !!!

🆔مثال : 
test@gmail.com
");
		}
}
}elseif($step=="istyping"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعال کن✅"){
								setvalue("data","id",1,"typing","on");
								sm($chatid,"وضعیت درحال نوشتن ربات فعال شد✅");
								}elseif($text=="غیرفعال کن🚫"){
									setvalue("data","id",1,"typing","off");
								sm($chatid,"وضعیت درحال نوشتن ربات غیرفعال شد✅");
								
									}
							}
						}elseif($step=="webview"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعال کن✅"){
								setvalue("data","id",1,"webview","on");
								sm($chatid,"وضعیت وب ویو متن ها فعال شد✅");
								}elseif($text=="غیرفعال کن🚫"){
									setvalue("data","id",1,"webview","off");
								sm($chatid,"وضعیت وب ویو متن ها غیرفعال شد✅");
								
									}
							}
						}elseif($step=="replymessage"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعال کن✅"){
								setvalue("data","id",1,"replymessage","on");
								sm($chatid,"وضعیت ریپلی پیام ها فعال شد✅");
								}elseif($text=="غیرفعال کن🚫"){
									setvalue("data","id",1,"replymessage","off");
								sm($chatid,"وضعیت ریپلی پیام ها غیرفعال شد✅");
								
									}
							}
						}elseif($step=="once"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="پاپ آپ🎫"){
								setvalue("data","id",1,"oncetype","popup");
								sm($chatid,"پاپ آپ با موفقیت فعال شد✅");
								}elseif($text=="نوتوفیکشن🎫"){
									setvalue("data","id",1,"oncetype","notif");
								sm($chatid,"نوتوفیکشن با موفقیت فعال شد✅");
								
									}
							}
						}elseif($step=="resize"){
						if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعال کن✅"){
								setvalue("data","id",1,"resize","on");
								sm($chatid,"دکمه ها با سایز ایده ال نمایش داده میشوند✅");
								}elseif($text=="غیرفعال کن🚫"){
									setvalue("data","id",1,"resize","off");
								sm($chatid,"دکمه ها از حالت ایده ال خارج شدند✅");
								
									}
							}
						}elseif($step=="codesetting"){
							if($text=="برگشت🔙"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="➕ساخت کد"){
								step($chatid,"code1");
								sm($chatid,"💠لطفا اسم دکمه ای که میخواهید کد برای آن قابل اجرا باشد را وارد نمایید :\n\n🚫توجه کنید که دکمه از قبل ساخته شده باشد.",$keyback);
								}elseif($text=="🔘لیست کد ها"){
									$get = getallvalue("code","code");
									$list = "<b>💠لیست کد های ساخته شده تا الان</b>\n\n";
									foreach($get as $key){
										$list .= "<code>$key</code> | ";
										}
										sm($chatid,$list);
									}elseif($text=="👁‍🗨وضعیت کد"){
										step($chatid,"vazcode");
										sm($chatid,"💠لطفا کد از قبل ساخته شده ی خود را بفرستید :",$keyback);
										}elseif($text=="➖حذف کد"){
step($chatid,"hazfcode");
										sm($chatid,"💠لطفا کد از قبل ساخته شده ی خود را بفرستید :",$keyback);
}
							}
							}elseif($step=="hazfcode"){
if($text=="برگشت↪"){
				step($chatid,"codesetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(!empty(getvalue("code","code",$text,"code"))){
								deletevalue("code","code",$text);
										step($chatid,"codesetting");
										sm($chatid,"کد مورد نظر شما از لیست حذف شد✅",$keycodedok);
								}else{
									sm($chatid,"🚫این کد وجود ندارد\n\nلطفا ابتدا کد را از قسمت لیست کد ها دریافت نمایید و سپس چک کنید :");
									}
							}
}elseif($step=="vazcode"){
if($text=="برگشت↪"){
				step($chatid,"codesetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(!empty(getvalue("code","code",$text,"code"))){
								$dokme=getvalue("code","code",$text,"dokme");
								$tedad=getvalue("code","code",$text,"tedad");
								$nafar=getvalue("code","code",$text,"nafar");
								$vaz=getvalue("code","code",$text,"vaz");
								$date=getvalue("code","code",$text,"date");
								if($vaz="on"){
									$vaz="#_فعال";
									}else{
										$vaz="#_غیرفعال";
										}
										step($chatid,"codesetting");
										sm($chatid,"🎁وضعیت کد تا الان : \n\n🎫اسم کد : $text\n🎫دکمه کد : $dokme\n🎫قابل استفاده برای : $tedad نفر\n🎫تعداد استفاده : $nafar نفر\n🎫وضعیت کد : $vaz\n🎫تاریخ ایجاد کد : $date",$keycodedok);
								}else{
									sm($chatid,"🚫این کد وجود ندارد\n\nلطفا ابتدا کد را از قسمت لیست کد ها دریافت نمایید و سپس چک کنید :");
									}
							}
}elseif($step=="code1"){
								if($text=="برگشت↪"){
				step($chatid,"codesetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(isset($text)){
								if(!empty(getvalue("dok","dokme",$text,"dokme"))){
									setvalue("user","chatid",$chatid,"Other",$text);
									sm($chatid,"لطفا کد خود را بفرستید :\n💠کد شما میتواند شامل عدد ، حروف ، کاراکتر باشد\n\n🚫توجه کنید که کد باید بصورت متن باشد");
									step($chatid,"code2");
									}else{
										sm($chatid,"این دکمه وجود ندارد⛔\n\nلطفا دوباره با صحت کامل تست کنید :");
										
										}
								}
							}
								}elseif($step=="code2"){
								if($text=="برگشت↪"){
				step($chatid,"codesetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(isset($text)){
								setvalue("user","chatid",$chatid,"Other2",$text);
								step($chatid,"code3");
								sm($chatid,"💠چند نفر میتواند از این کد استفاده نمایید ؟!\n\n⛔فقط میتواند شامل عدد باشد .");
								}else{
									sm($chatid,"⛔کد شما فقط میتواند متن باشد");
									}
							}
							}
							elseif($step=="code3"){
								if($text=="برگشت↪"){
				step($chatid,"codesetting");
				sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(preg_match('/^[0-9]+$/',$text)){
								step($chatid,"code4");
								setvalue("user","chatid",$chatid,"Other3",$text);
								sm($chatid,"💠مشخص کنید که درصورت به اتمام رسیدن استفاده از کد چه متنی برای کاربر نشان دهد :");
								}else{
									sm($chatid,"⛔فقط عدد مجاز هست\n\nلطفا دوباره امتحان کنید :");
									}
							}
							}elseif($step=="code4"){
								if($text=="برگشت↪"){
				step($chatid,"codesetting");
				sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycodedok);
						}else{
							if(isset($text)){
								$dokme=getvalue("user","chatid",$chatid,"Other");
								$code=getvalue("user","chatid",$chatid,"Other2");
								$tedad=getvalue("user","chatid",$chatid,"Other3");
								$in = insert("code","`dokme`,`tedad`,`code`,`text`,`vaz`,`date`","'$dokme','$tedad','$code','$text','on','$datesh-$time'");
								step($chatid,"codesetting");
								sm($chatid,"کد شما ساخته شد✅\n\n💠کد : $code\n💠دکمه : $dokme\n💠تعداد استفاده : $tedad نفر\n💠وضعیت : #_فعال\n💠تاریخ ایجاد : $datesh-$time",$keycodedok);
								}
							}
							}elseif($step=="replace"){
								if($text=="برگشت🔙"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="افزودن متن➕"){
							step($chatid,"addreplace1");
							sm($chatid,"لطفا متن خود را بفرستید\n\n🚫سعی کنید از کلمه ی مناسب استفاده کنید تا مشکلی در روند ربات پیش نیاید\n\n⚠توجه کنید جایگزینی در تمام قسمت های گرفتن محتوای ربات اعمال میشود !!",$keyback);
							}elseif($text=="حذف متن➖"){
							step($chatid,"remreplace");
							sm($chatid,"لطفا متن خود را با توجه به لیست متن ها بفرستید:",$keyback);
							}elseif($text=="لیست متن ها🔘"){
								$get = getallvalue("replac","replac");
								$list="<b>لیست کلمات جایگزین شده : </b>\n\n";
								foreach($get as $hj){
									$te = getvalue("replac","replac",$hj,"totext");
									$list.="<code>$hj => $te</code> | ";
									}
									sm($chatid,$list);
								}elseif($text=="فعال کردن🟢"){
							setvalue("data","id",1,"replacetext","on");
							sm($chatid,"جایگزین کلمات با موفقیت فعال شد✅");
							}elseif($text=="غیرفعال کردن🔴"){
							setvalue("data","id",1,"replacetext","off");
							sm($chatid,"جایگزین کلمات با موفقیت غیرفعال شد✅");
							}elseif($text=="پاکسازی کامل لیست"){
								$sql = "DROP TABLE `replace$userbott`";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `replac$userbott` 
 ( 
 `replac` TEXT,
 `totext` TEXT
)";
mysqli_query($con,$sql);
								}
								}elseif($step=="mahdod"){
								if($text=="برگشت🔙"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="➕افزودن فرد"){
							step($chatid,"addfard");
							sm($chatid,"🌟لطفا ایدی عددی فرد را وارد کنید :",$keyback);
							}elseif($text=="➖حذف فرد"){
							step($chatid,"remfard");
							sm($chatid,"🌟لطفا ایدی عددی فرد را وارد کنید :",$keyback);
							}elseif($text=="🔘لیست افراد مجاز"){
								$get = getallvalue("mahdod","chatid");
								$list="<b>لیست افراد مجاز :</b>\n\n";
								foreach($get as $hj){
									$list.="<code>$hj</code> | ";
									}
									sm($chatid,$list);
								}elseif($text=="فعال کردن🟢"){
							setvalue("data","id",1,"mahdod","on");
							sm($chatid,"محدود کردن ربات برای افراد مجاز فعال شد✅");
							}elseif($text=="غیرفعال کردن🔴"){
							setvalue("data","id",1,"mahdod","off");
							sm($chatid,"محدود کردن ربات برای افراد مجاز غیرفعال شد✅");
							}elseif($text=="پاکسازی کامل لیست"){
								$sql = "DROP TABLE `mahdod$userbott`";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `mahdod$userbott` 
 ( 
 `chatid` TEXT
)";
mysqli_query($con,$sql);
								}
								}elseif($step=="addfard"){
									if($text=="برگشت↪"){
				step($chatid,"mahdod");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keymahdod);
						}else{
							if(isset($text)){
								step($chatid,"mahdod");
								insert("mahdod","`chatid`","'$text'");
								sm($chatid,"ایدی مورد نظر به لیست افراد مجاز اضافه شد✅",$keymahdod);
								}
							}
									}elseif($step=="remfard"){
									if($text=="برگشت↪"){
				step($chatid,"mahdod");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keymahdod);
						}else{
							if(!empty(getvalue("mahdod","chatid",$text,"chatid"))){
								step($chatid,"mahdod");
								deletevalue("mahdod","chatid",$text);
								sm($chatid,"ایدی شما با موفقیت از لیست افراد مجاز حذف شد✅",$keymahdod);
								}else{
									sm($chatid,"ایدی شما در لیست افراد مجاز قرار ندارد‌‌!!!!⛔");
									}
							}
									}elseif($step=="addreplace1"){
									if($text=="برگشت↪"){
				step($chatid,"replace");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyreplace);
						}else{
							if(isset($text)){
								step($chatid,"addreplace2");
								setOther2($chatid,$text);
								sm($chatid,"خوب میخوایی بجای کلمه $text چه متنی نمایش داده شود ؟!",$keyback);
								}
							}
									}elseif($step=="addreplace2"){
									if($text=="برگشت↪"){
				step($chatid,"replace");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyreplace);
						}else{
							if(isset($text)){
								step($chatid,"replace");
								$tt = getOther2($chatid);
								insert("replac","`replac`,`totext`","'$tt','$text'");
								sm($chatid,"کلمه ی شما با موفقیت به لیست جایگزین اضافه شد✅",$keyreplace);
								}
							}
									}elseif($step=="remreplace"){
									if($text=="برگشت↪"){
				step($chatid,"replace");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyreplace);
						}else{
							if(!empty(getvalue("replac","replac",$text,"replac"))){
								step($chatid,"replace");
								deletevalue("replac","replac",$text);
								sm($chatid,"متن شما با موفقیت از لیست جایگزین حذف شد✅",$keyreplace);
								}else{
									sm($chatid,"کلمه ی شما در لیست جایگزین وجود ندارد⛔");
									}
							}
									}elseif($step=="filter"){
								if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="افزودن کلمه➕"){
							step($chatid,"addfilter");
							sm($chatid,"لطفا متن خود را بفرستید\n\n🚫سعی کنید از کلمه ی مناسب استفاده کنید تا مشکلی در روند ربات پیش نیاید\n\n⚠توجه کنید فیلتر در تمام قسمت های گرفتن محتوای ربات اعمال میشود !!",$keyback);
							}elseif($text=="حذف کلمه➖"){
							step($chatid,"remfilter");
							sm($chatid,"لطفا متن خود را با توجه به لیست فیلتر بفرستید :",$keyback);
							}elseif($text=="لیست کلمات🔘"){
								$get = getallvalue("filter","filter");
								$list="<b>لیست کلمات فیلتر شده : </b>\n\n";
								foreach($get as $hj){
									$list.="<code>$hj</code> | ";
									}
									sm($chatid,$list);
								}elseif($text=="فعال کن✅"){
							setvalue("data","id",1,"filter","on");
							sm($chatid,"فیلتر کلمات با موفقیت فعال شد✅");
							}elseif($text=="غیرفعال کن🚫"){
							setvalue("data","id",1,"filter","off");
							sm($chatid,"فیلتر کلمات با موفقیت فعال شد✅");
							}
								}elseif($step=="addfilter"){
									if($text=="برگشت↪"){
				step($chatid,"filter");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfilter);
						}else{
							if(isset($text)){
								step($chatid,"filter");
								insert("filter","`filter`","'$text'");
								sm($chatid,"کلمه ی شما با موفقیت به لیست فیلتر اضافه شد✅",$keyfilter);
								}
							}
									}elseif($step=="remfilter"){
									if($text=="برگشت↪"){
				step($chatid,"filter");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfilter);
						}else{
							if(!empty(getvalue("filter","filter",$text,"filter"))){
								step($chatid,"filter");
								deletevalue("filter","filter",$text);
								sm($chatid,"کلمه ی شما با موفقیت از لیست فیلتر حذف شد✅",$keyfilter);
								}else{
									sm($chatid,"کلمه ی شما در لیست فیلتر وجود ندارد⛔");
									}
							}
									}
									
									elseif($step=="del"){
								if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="افزودن کلمه➕"){
							step($chatid,"adddel");
							sm($chatid,"لطفا کلمه ی خود را بفرستید تا در لیست اعمال شود : \n\n⚠توجه کنید کلمه فقط از قسمت ظاهری چت پاک میشود ، در نتیجه زر روند ربات مشکلی پیش نمیاید",$keyback);
							}elseif($text=="حذف کلمه➖"){
							step($chatid,"remdel");
							sm($chatid,"لطفا متن خود را با توجه به لیست کلمات بفرستید :",$keyback);
							}elseif($text=="لیست کلمات🔘"){
								$get = getallvalue("del","del");
								$list="<b>لیست کلمات وارد شده :</b>\n\n";
								foreach($get as $hj){
									$list.="<code>$hj</code> | ";
									}
									sm($chatid,$list);
								}elseif($text=="فعال کن✅"){
							setvalue("data","id",1,"autodel","on");
							sm($chatid,"حذف کلمات با موفقیت فعال شد✅");
							}elseif($text=="غیرفعال کن🚫"){
							setvalue("data","id",1,"autodel","off");
							sm($chatid,"حذف کلمات با موفقیت غیرفعال شد✅");
							}
								}elseif($step=="adddel"){
									if($text=="برگشت↪"){
				step($chatid,"del");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydel);
						}else{
							if(isset($text)){
								step($chatid,"del");
								insert("del","`del`","'$text'");
								sm($chatid,"کلمه ی شما با موفقیت به لیست حذف کلمات اضافه شد✅",$keydel);
								}
							}
									}elseif($step=="remdel"){
									if($text=="برگشت↪"){
				step($chatid,"del");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydel);
						}else{
							if(!empty(getvalue("del","del",$text,"del"))){
								step($chatid,"del");
								deletevalue("del","del",$text);
								sm($chatid,"کلمه ی شما با موفقیت از لیست حذف کلمات حذف شد✅",$keydel);
								}else{
									sm($chatid,"کلمه ی شما در لیست حذف وجود ندارد⛔");
									}
							}
									}
elseif($step=="lockphone"){
								if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="فعال کن✅"){
							setvalue("data","id",1,"lockphone","on");
							sm($chatid,"قفل شماره با موفقیت فعال شد✅");
							}elseif($text=="غیرفعال کن🚫"){
							setvalue("data","id",1,"lockphone","off");
							sm($chatid,"قفل شماره با موفقیت غیرفعال شد✅");
							}elseif($text=="فقط شماره ایران فعال✅"){
							setvalue("data","id",1,"phoneiran","on");
							sm($chatid,"قفل شماره تنها برای کد ایران +98 فعال شد✅");
							}elseif($text=="فقط شماره ایران غیرفعال🚫"){
							setvalue("data","id",1,"phoneiran","off");
							sm($chatid,"قفل شماره ایران غیرفعال شد✅");
							}elseif($text=="تغییر متن درخواست شماره📞"){
								step($chatid,"textphone1");
								sm($chatid,"لطفا متنی که میخواهید موقع درخواست شماره از کاربر نمایش داده شود را بفرستید :",$keyback);
								}elseif($text=="تغییر متن شماره اشتباه⭕️"){
								step($chatid,"textphone2");
								sm($chatid,"لطفا متنی که میخواهید موقع فرستادن شماره اشتباه برای کاربر نمایش داده شود را بفرستید :",$keyback);
								}elseif($text=="تنظیم متن کد اشتباه🇮🇷"){
								step($chatid,"textphiran");
								sm($chatid,"لطفا متنی که میخواهید برای کاربر موقع ارسال شماره ی غیر از کد ایران نمایش داده شود را بفرستید :",$keyback);
								}elseif($text=="تغییر اسم دکمه🔆"){
								step($chatid,"dokphone");
								sm($chatid,"لطفا اسم دکمه جدید رو بفرستید :",$keyback);
								}
								}elseif($step=="lockcaptha"){
								if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="فعال کن✅"){
							setvalue("data","id",1,"captha","on");
							sm($chatid,"قفل با کپچا با موفقیت فعال شد✅");
							}elseif($text=="غیرفعال کن🚫"){
							setvalue("data","id",1,"captha","off");
							sm($chatid,"قفل با کپچا با موفقیت غیرفعال شد✅");
							}elseif($text=="🔆تغییر متن درخواست کپچا"){
								step($chatid,"textcaptha1");
								sm($chatid,"لطفا متنی که میخواهید موقع درخواست کپچا از کاربر نمایش داده میشود را بفرستید :",$keyback);
								}elseif($text=="تغییر متن کپچا اشتباه⭕️"){
								step($chatid,"textcaptha2");
								sm($chatid,"لطفا متنی که میخواهید موقع فرستادن شماره کپچا اشتباه برای کاربر فرستاده شود را بفرستید :",$keyback);
								}elseif($text=="تغییر استایل🗽"){
								step($chatid,"stylecap");
								sm($chatid,"لطفا یک استایل رو انتخاب کنید :",$keycapstyle);
								}
								}elseif($step=="stylecap"){
    if($text=="برگشت↪"){
        step($chatid,"lockcaptha");
        sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycaptha);
    }else{
        if($text=="استایل اول🌃"){
            setvalue("data","id",1,"capstyle","1");
            sm($chatid,"استایل اول فعال شد✅");
        }elseif($text=="استایل دوم🌃"){
            setvalue("data","id",1,"capstyle","2");
            sm($chatid,"استایل دوم فعال شد✅");
        }elseif($text=="استایل سوم🌃"){ // استایل سوم
            setvalue("data","id",1,"capstyle","3");
            sm($chatid,"استایل سوم فعال شد✅");
        }
    }
}elseif($step=="textcaptha1"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockcaptha");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycaptha);
						}else{
									step($chatid,"lockcaptha");
									setvalue("data","id",1,"textcaptha1",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keycaptha);
									}
									}
									}elseif($step=="textcaptha2"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockcaptha");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycaptha);
						}else{
									step($chatid,"lockcaptha");
									setvalue("data","id",1,"textcaptha2",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keycaptha);
									}
									}
									}elseif($step=="textcaptha2"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockcaptha");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycaptha);
						}else{
									step($chatid,"lockcaptha");
									setvalue("data","id",1,"textcaptha3",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keycaptha);
									}
									}
									}elseif($step=="textphone1"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockphone");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyphone);
						}else{
									step($chatid,"lockphone");
									setvalue("data","id",1,"textphone1",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keyphone);
									}
									}
									}elseif($step=="dokphone"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockphone");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyphone);
						}else{
									step($chatid,"lockphone");
									setvalue("data","id",1,"dokphone",$text);
									sm($chatid,"اسم دکمه تغییر یافت✅",$keyphone);
									}
									}
									}elseif($step=="textphone2"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockphone");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyphone);
						}else{
									step($chatid,"lockphone");
									setvalue("data","id",1,"textphone2",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keyphone);
									}
									}
									}elseif($step=="textphiran"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"lockphone");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyphone);
						}else{
									step($chatid,"lockphone");
									setvalue("data","id",1,"textphiran",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keyphone);
									}
									}
									}elseif($step=="UploadFile"){
										if($text=="برگشت↪"){
											step($chatid,"sayersetting");
											sm($chatid,"یه عقب برگشتید :",$keysayer);
											ToDie();
											}
										if(!isetrow("upload","hash"))
{

	$sql = "CREATE TABLE `upload$userbott` 
 ( 
 `hash` TEXT,
 `type` TEXT,
 `text` TEXT,
 `caption` TEXT
)";
mysqli_query($con,$sql);
}
if(!isetrow("upload","caption")){
	createrow("upload","text","caption","TEXT");
	}
if(isset($Message->audio)){
	$typee="audio";
	$fid = $Message->$typee->file_id;
	$size = $Message->$typee->file_size;
	$md= md5($fid);
	if(!isset($caption)){
		$caption = null;
		}
	insert("upload","`hash`,`type`,`text`,`caption`","'$md','$typee','$fid','$caption'");
	$link="https://t.me/$userbott?start=GETFILE$md";
	}elseif(isset($Message->document)){
	$typee="document";
	$fid = $Message->$typee->file_id;
	$size = $Message->$typee->file_size;
	$md= md5($fid);
	if(!isset($caption)){
		$caption = null;
		}
	insert("upload","`hash`,`type`,`text`,`caption`","'$md','$typee','$fid','$caption'");
$link="https://t.me/$userbott?start=GETFILE$md";
	}elseif(isset($Message->video)){
	$typee="video";
	$fid = $Message->$typee->file_id;
	$size = $Message->$typee->file_size;
	$md= md5($fid);
	insert("upload","`hash`,`type`,`text`","'$md','$typee','$fid'");
	$link="https://t.me/$userbott?start=GETFILE$md";
	}elseif(isset($Message->video_note)){
	$typee="video_note";
	$fid = $Message->$typee->file_id;
	$size = $Message->$typee->file_size;
	$md= md5($fid);
	if(!isset($caption)){
		$caption = null;
		}
	insert("upload","`hash`,`type`,`text`,`caption`","'$md','$typee','$fid','$caption'");
$link="https://t.me/$userbott?start=GETFILE$md";
	}elseif(isset($Message->voice)){
	$typee="voice";
	$fid = $Message->$typee->file_id;
	$size = $Message->$typee->file_size;
	$md= md5($fid);
	if(!isset($caption)){
		$caption = null;
		}
	insert("upload","`hash`,`type`,`text`,`caption`","'$md','$typee','$fid','$caption'");
$link="https://t.me/$userbott?start=GETFILE$md";
	}elseif(isset($Message->photo[0])){
	$typee="photo";
	$fid = $Message->photo[0]->file_id;
	$size = $Message->photo[0]->file_size;
	$md= md5($fid);
	if(!isset($caption)){
		$caption = null;
		}
	insert("upload","`hash`,`type`,`text`,`caption`","'$md','$typee','$fid','$caption'");
$link="https://t.me/$userbott?start=GETFILE$md";
	}else{
		sm($chatid,"این فرمت قابل پشتیبانی نیست🚫");
		ToDie();
		}
	$txt="فایل شما با موفقیت در سرور اپلود شد✅
🕹نوع فایل : $typee
⚓️حجم فایل : $size


➕$link

👆👆👆با لینک بالا میتوانید اقدام به دریافت فایل کنید";
sm($chatid,$txt);
										}elseif($step=="deleteuploadfile"){
							if($text=="خیر🚫"){
								step($chatid,"sayersetting");
								sm($chatid,"عملیات لغو شد و به عقب برگشتید :",$keysayer);
								}elseif($text=="بله✅"){
								step($chatid,"sayersetting");
								$sql = "DROP TABLE `upload$userbott`";
								mysqli_query($con,$sql);
								sm($chatid,"فایل های ذخیره شده با موفقیت حذف شدند✅",$keysayer);
							}
						}elseif($step=="deletesharefile"){
							if($text=="خیر🚫"){
								step($chatid,"sayersetting");
								sm($chatid,"عملیات لغو شد و به عقب برگشتید :",$keysayer);
								}elseif($text=="بله✅"){
								step($chatid,"sayersetting");
								$sql = "DROP TABLE `eshtrak$userbott`";
								mysqli_query($con,$sql);
								sm($chatid,"فایل های ذخیره شده با موفقیت حذف شدند✅",$keysayer);
							}
						}elseif($step=="deleteonce"){
							if($text=="خیر🚫"){
								step($chatid,"sayersetting");
								sm($chatid,"عملیات لغو شد و به عقب برگشتید :",$keysayer);
								}elseif($text=="بله✅"){
								step($chatid,"sayersetting");
								$sql = "DROP TABLE `deleteonce$userbott`";
								mysqli_query($con,$sql);
								sm($chatid,"فایل های ذخیره شده با موفقیت حذف شدند✅",$keysayer);
							}
						}elseif($step=="deletedokmedasti"){
							if($text=="برگشت↪"){
								step($chatid,"sayersetting");
								sm($chatid,"عملیات لغو شد و به عقب برگشتید :",$keysayer);
								}else{
								
									if(!empty(getvalue("dok","dokme",$text,"dokme"))){
										$vb = $text;
										$md = getvalue("hashmoh","text",$vb,"hash");
							$sql = "DROP TABLE `mohtava$md$userbott`";
							mysqli_query($con,$sql);
							$md = getvalue("hash","text",$vb,"hash");
							$sql = "DROP TABLE `mohtava$md$userbott`";
							mysqli_query($con,$sql);
	if(getDokmenok($vb)=="newdokme"){
	sm($chatid,"لطفا صبر کنید...");
			$var=$vb;
			$array=getallvalue("dok","dokme");
			foreach($array as $key){
					$end=0;
					$sum = $key;
					
					$count = count($array);
					while(true){
					$end=1;
					if(getInto($sum)==$vb){
						$arr[]=$key;
						break;
						}else{
							if(empty(getInto($sum))){
								$two = $array[$x];
								 break;
 }else{
 	$sum = getInto($sum);
							
							}
							}
				}
				}
				foreach($arr as $m){
					deletevalue("dok","dokme",$m);
					}
			
		}
	
	$key = getKeyboard();
	if(!empty(getInto($vb))){
		$into=getInto($vb);
		$ddokme=getDokmetext($vb);
		$kky = getDokmetext($into);
		if(strpos($kky,'"text":"'.$vb.'"},')){
		$cc = str_replace('{"text":"'.$vb.'"},',"",$kky);
		$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}elseif(strpos($kky,',{"text":"'.$vb.'"}]')){
		$cc = str_replace(',{"text":"'.$vb.'"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}else{
		$cc = str_replace('{"text":"'.$vb.'"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}
		deletevalue("dok","dokme",$vb);
		step($chatid,"sayersetting");
		$txt="دکمه ی شما با موفقیت حذف شد✅";
		sm($chatid,$txt,$keysayer);
		}else{
	if(strpos($key,'"text":"'.$vb.'"},')){
		$cc = str_replace('{"text":"'.$vb.'"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}elseif(strpos($key,',{"text":"'.$vb.'"}]')){
		$cc = str_replace(',{"text":"'.$vb.'"}',"",$key);
	$cp = str_replace("[],","",$cc);
		setKeyboard($cp);
	}else{
		$cc = str_replace('{"text":"'.$vb.'"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}
		deletevalue("dok","dokme",$vb);
		step($chatid,"sayersetting");
		$txt="دکمه ی شما با موفقیت حذف شد✅";
		sm($chatid,$txt,$keysayer);
		}
	
										}else{
											$txt="📛این دکمه در ربات شما وجود ندارد!!

⚠️لطفا پس از اطمینان کامل اسم دکمه را بفرستید :";
											sm($chatid,$txt);
											}
									}
							}elseif($step=="command"){
								if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="لیست دستورات ربات👁‍🗨"){
							$vb = bot('getMyCommands');
							$txt="<b> لیست دستورات به همراه توضیحات ثبت شده در ربات : </b>\n\n";
							foreach($vb->result as $key){
								$one = $key->command;
								$two = $key->description;
								$txt.="<b>/$one</b> : <code> $two</code>\n••••••••••••••••••••••••\n";
								}
								sm($chatid,$txt);
							}elseif($text=="حذف همه دستورات♾"){
								$vb = bot('setMyCommands');
								sm($chatid,"همه ی دستورات ربات با موفقیت حذف شدند✅

⚠️برای اینکه تغییرات اعمال شود ، باید یکبار از ربات خارج شوید");
								}elseif($text=="افزودن دستور➕"){
									step($chatid,"addcommand");
									$txt="لطفا اسم دستور رو وارد کنید:

⚠️توجه کنید دستور رو بدون / وارد کنید
⚠️تنها حروف انگلیسی و عدد مجاز هست!!
⚠دستور شما باید حتما با حروف کوچک باشد!
⚠️دستور شما باید بین 1 تا 32 حروف باشد";
sm($chatid,$txt,$keyback);
									}
								
								}elseif($step=="addcommand"){
									if($text=="برگشت↪"){
				step($chatid,"command");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycommand);
						}else{
							if(preg_match('/^[a-z0-9]+$/',$text)){
								$ted = strlen($text);
								if($ted >= 1 && $ted <= 32){
									$text= strtolower($text);
									$txt="حالا توضیحاتی برای این دستور بفرستید :

⚠️توضیحات حتما باید بصورت متن باشد

⚠️توضیحات شما باید بین 3 تا حداکثر 256 حروف داشته باشد";
step($chatid,"addcommand1");
sm($chatid,$txt);
setOther2($chatid,$text);

									}else{
										$txt="⚠حروف شما باید بین 1 تا 32 کاراکتر باشد!!\n\nلطفا دوباره امتحان کنید :";
										sm($chatid,$txt);
										}
								}else{
									$txt="⚠حروف شما باید فقط انگلیسی و عدد باشد!!!\n\nلطفا دویاره امتحان کنید";
									sm($chatid,$txt);
									}
							}
									}elseif($step=="addcommand1"){
									if($text=="برگشت↪"){
				step($chatid,"command");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycommand);
						}else{
							if(isset($text)){
								$ted = strlen($text);
								if($ted >= 3 && $ted <= 256){
							$vb = bot('getMyCommands');
							$x=0;
							foreach($vb->result as $key){
								if(empty($key)){
									break;
									}else{
								$one = $key->command;
								$two = $key->description;
								$array[] = ['command'=>$one,'description'=>$two];
								$x++;
								}
								}
							$array[] = ['command'=>getOther2($chatid),'description'=>$text];
								$comm= json_encode($array);
								$vb2 = bot('setMyCommands',[
'commands'=>$comm,
]);

								$txt="دستور جدید با موفقیت اضافه شد✅

⚠️برای ثبت شدن یکبار از ربات خارج شوید.";
step($chatid,"command");
sm($chatid,$txt,$keycommand);


									}else{
										$txt="⚠حروف شما باید بین 1 تا 32 کاراکتر باشد!!\n\nلطفا دوباره امتحان کنید :";
										sm($chatid,$txt);
										}
								}else{
									$txt="⚠حروف شما باید فقط انگلیسی و عدد باشد!!!\n\nلطفا دویاره امتحان کنید";
									sm($chatid,$txt);
									}
							}
									}elseif($step=="setlog"){
if($text=="برگشت↪"){
			step($chatid,"log");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keylog);
			}else{
			if(preg_match("/^(\@)(.*)$/",$text)){
				preg_match("/^(\@)(.*)$/",$text,$mat);
				$user = $mat[2];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setvalue("data","id",1,"logchannel",$text);
				step($chatid,"log");
				sm($chatid,"کانال با موفقیت تنظیم شد✅",$keylog);
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
				//$stat = $url2->result->status;
							}elseif(preg_match('/^\-[0-9]+$/',$text)){
								preg_match('/^\-[0-9]+$/',$text,$mat);
				$user = $mat[0];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setvalue("data","id",1,"logchannel",$text);
				step($chatid,"log");
				sm($chatid,"کانال با موفقیت تنظیم شد✅",$keylog);
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
								
								}else{
							$txt="⛔️فرمت یوزرنیم یا ایدی عددی ارسال شده اشتباه میباشد!

❗️لطفا یوزرنیم را همراه با @ برای ما بفرستید 
مثال 🆔 : @$channel_name_en
مثال 🆔 : -1002135620077"
;
				sm($chatid,$txt);		
								}
			}
							}
elseif($step=="log"){
										if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="روشن✅"){
								setvalue("data","id",1,"sendlog","on");
							sm($chatid,"ارسال لاگ در کانال با موفقیت روشن شد✅");
							
								}elseif($text=="خاموش❌"){
									setvalue("data","id",1,"sendlog","off");
							sm($chatid,"ارسال لاگ در کانال با موفقیت خاموش شد✅");
							
									}elseif($text=="تنظیم ایدی کانال🆔"){
										$txt="🔰لطفا ابتدا ربات را در کانال خود ادمین نمایید.

سپس یوزرنیم کانال را را برای ما بفرستید

مثال 🆔 : @$channel_name_en

اگر کانال شما خصوصی میباشد ، ایدی عددی کانال را برای ما بفرستید .
 
مثال 🆔 : -1002135620077";
step($chatid,"setlog");
sm($chatid,$txt,$keyback);
										}
							}
										}elseif($step=="varlike"){
										if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="نوتوفیکشن✅"){
								setvalue("data","id",1,"notoflike","on");
							sm($chatid,"ارسال نوتوفیکشن بعد لایک یا دیس لایک روشن شد✅");
							
								}elseif($text=="نوتوفیکشن⛔"){
									setvalue("data","id",1,"notoflike","off");
							sm($chatid,"ارسال نوتوفیکشن بعد لایک یا دیس لایک خاموش شد✅");
							
									}elseif($text=="متن نوتوفیکشن لایک👍"){
										$txt="لطفا متن نوتوفیکشنی که بعد از لایک برای کاربر نمایش داده میشود را بفرستید :";
step($chatid,"setliketext");
sm($chatid,$txt,$keyback);
										}elseif($text=="متن نوتوفیکشن دیس لایک👎"){
										$txt="لطفا متن نوتوفیکشنی که بعد از دیس لایک برای کاربر نمایش داده میشود را بفرستید :";
step($chatid,"setdisliketext");
sm($chatid,$txt,$keyback);
										}elseif($text=="صفر کردن تمام لایک ها⤵"){
										$txt="با زدن بله✅ تمام لایک های شما صفر خواهند شد!!\n\nآیا مطمعن هستید؟!!!";
step($chatid,"deletealllike");
sm($chatid,$txt,$keynoyes);
										}
							}
										}elseif($step=="setliketext"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"varlike");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keylike);
						}else{
									step($chatid,"varlike");
									setvalue("data","id",1,"notofliketext",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keylike);
									}
									}
									}elseif($step=="setdisliketext"){
									if(isset($text)){
										if($text=="برگشت↪"){
				step($chatid,"varlike");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keylike);
						}else{
									step($chatid,"varlike");
									setvalue("data","id",1,"notofdisliketext",$text);
									sm($chatid,"متن شما ذخیره شد✅",$keylike);
									}
									}
									}elseif($step=="deletealllike"){
								if($text=="خیر🚫"){
				step($chatid,"varlike");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keylike);
						}elseif($text=="بله✅"){
							$wer = "DROP TABLE `likes$userbott`";
							mysqli_query($con,$wer);
							$wer = "DROP TABLE `liketext$userbott`";
							mysqli_query($con,$wer);
						step($chatid,"varlike");
						$txt="ریست لایک ها با موفقیت انجام شد✅";
						sm($chatid,$txt,$keylike);
							}
									}elseif($step=="getcopycode"){
											if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						}elseif($text=="تغییر کد رونوشت♻"){
							$code = md5(random(20));
							setvaluee("copycode","userbot",$userbott,"code",$code);
							$txt="";
							sm($chatid,"کد رونوشت تغییر یافت✅

<code>$code</code>

⚠️توجه کنید کد را در اختیار افراد بیجا قرار ندهید
درصورت وارد کردن کد ‌، اخرین اطلاعات ربات شما کپی خواهد شد‼️");

							}
											}elseif($step=="addcodecopy"){
												if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						}elseif(!empty(getvaluee("copycode","code",$text,"userbot"))){
							if(getvaluee("copycode","code",$text,"userbot")==$userbott){
								$txt="این کد رونوشت مخصوص برای خود ربات شماست‼️‼️


⚠️لطفا از کد رونوشت دیگران برای کپی اطلاعات در ربات خود استفاده نمایید";
sm($chatid,$txt);
								}else{
							
							step($chatid,"addcodecopy2");
							$userr = getvaluee("copycode","code",$text,"userbot");
							setOther2($chatid,$userr);
							$txt="💠آیا از کپی اطلاعات ربات @$userr به ربات خود مطمعن هستید ؟!!

⚠️تمام اطلاعات بجز آمار کاربران در ربات شما به ربات @$userr تغییر میابد";
sm($chatid,$txt,$keynoyes);
									}
							}else{
								$txt="این کد رو نوشت وجود ندارد یا تغییر یافته است❌

⚠️لطفا از کد رونوشت جدید استفاده کنید";
sm($chatid,$txt);
								}
												}elseif($step=="addcodecopy2"){
													if($text=="خیر🚫"){
														step($chatid,"panel");
														sm($chatid,"کپی کردن ربات با موفقیت لغو شد✅\n\nبه عقب برگشتید :",$keypanel);
														}elseif($text=="بله✅"){
															sm($chatid,"لطفا صبر کنید ...♻");
															$user1 = getOther2($chatid);
															$drop = "DROP TABLE `dok$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `dok$userbott` LIKE `dok$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `dok$userbott` SELECT * FROM `dok$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `delete$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `delete$userbott` LIKE `delete$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `delete$userbott` SELECT * FROM `delete$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `eshtrak$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `eshtrak$userbott` LIKE `eshtrak$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `eshtrak$userbott` SELECT * FROM `eshtrak$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `fileid$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `fileid$userbott` LIKE `fileid$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `fileid$userbott` SELECT * FROM `fileid$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `hash$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `hash$userbott` LIKE `hash$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `hash$userbott` SELECT * FROM `hash$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `datatype$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `datatype$userbott` LIKE `datatype$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `datatype$userbott` SELECT * FROM `datatype$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `datalist$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `datalist$userbott` LIKE `datalist$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `datalist$userbott` SELECT * FROM `datalist$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `replac$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `replac$userbott` LIKE `replac$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `replac$userbott` SELECT * FROM `replac$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `hashmoh$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `hashmoh$userbott` LIKE `hashmoh$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `hashmoh$userbott` SELECT * FROM `hashmoh$user1`";
														mysqli_query($con,$sql2);
														$all=getallvaluee("hashmoh$user1",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `mohtava$xb$userbott` LIKE `mohtava$xb$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `mohtava$xb$userbott` SELECT * FROM `mohtava$xb$user1`";
														mysqli_query($con,$sql2);
										}
										$all=getallvaluee("hash$user1",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `mohtava$xb$userbott` LIKE `mohtava$xb$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `mohtava$xb$userbott` SELECT * FROM `mohtava$xb$user1`";
														mysqli_query($con,$sql2);
										}
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `filter$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `filter$userbott` LIKE `filter$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `filter$userbott` SELECT * FROM `filter$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `del$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `del$userbott` LIKE `del$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `del$userbott` SELECT * FROM `del$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `code$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `code$userbott` LIKE `code$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `code$userbott` SELECT * FROM `code$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `moh$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `moh$userbott` LIKE `moh$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `moh$userbott` SELECT * FROM `moh$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `channel$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `channel$userbott` LIKE `channel$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `channel$userbott` SELECT * FROM `channel$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `robot$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `robot$userbott` LIKE `robot$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `robot$userbott` SELECT * FROM `robot$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `data$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `data$userbott` LIKE `data$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `data$userbott` SELECT * FROM `data$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												$drop = "DROP TABLE `pasokh$userbott`";
															mysqli_query($con,$drop);
															$sql = "CREATE TABLE `pasokh$userbott` LIKE `pasokh$user1`";
															mysqli_query($con,$sql);
														 $sql2 = "INSERT INTO `pasokh$userbott` SELECT * FROM `pasokh$user1`";
														mysqli_query($con,$sql2);
												//--------------++++++++++---------------------//
												step($chatid,"panel");
												$txt="اطلاعات با موفقیت رونوشت شدند✅

تمام اطلاعات ربات @$user1 در ربات شما کپی و اطلاعات ربات شما حذف شدند";
												sm($chatid,$txt,$keypanel);
															}
													}elseif($step=="qoflpm"){
														if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعالسازی✅"){
								setvalue("data","id",2,"lockphone","on");
							sm($chatid,"امکان قفل ربات با پیامک با موفقیت فعال شد✅");
							
								}elseif($text=="غیرفعالسازی⛔"){
									setvalue("data","id",2,"lockphone","off");
							sm($chatid,"امکان قفل ربات با پیامک با موفقیت غیرفعال شد✅");
							
									}elseif($text=="تغییر متن ارسال کد📩"){
										$txt="
🌟لطفا متنی که برای کاربر موقع ارسال پیامک به شماره ی آن نمایش داده میشود را بفرستید :
";
step($chatid,"setqolfpmersal");
sm($chatid,$txt,$keyback);
										}elseif($text=="تغییر متن کد اشتباه📛"){
										$txt="
🌟لطفا متنی که برای کاربر موقع فرستادن کد اشتباه نمایش داده میشود را بفرستید :
";
step($chatid,"setqoflpmesh");
sm($chatid,$txt,$keyback);
										}elseif($text=="نمایش موجودی💸"){
										if(!empty(getvalue("data","id",2,"phoneiran"))){
											$api = getvalue("data","id",2,"phoneiran");
											$link = "https://web.sms-bot.ir/balance.php?apikey=$api&type=amount";
											$res = json_decode(file_get_contents($link),true);
											if($res['status_code']=="ok"){
												$amount = $res["amount"];
												$txt="🌟مقدار موجودی باقی مانده شما در ربات $amount تومان میباشد
												
💥قیمت ارسال هر اس ام اس : 300 تومان";
												}else{
													$txt="⚠️مشکل در اتصال به کلید وبسرویس Api key

💠لطفا از صحت کلید وبسرویس اطمینان حاصل و دوباره تست نمایید !!!";
													}
													sm($chatid,$txt);
											}else{
												sm($chatid,"⚠️شما هنوز کلید وبسرویس Api key ربات خود را تنظیم نکرده اید!!!");
												}
										}elseif($text=="تنظیم Api Key🔑"){
										$txt="💠لطفا کلید وبسرویس دریافتی از ربات @SmSMizbanBot را بفرستید :";
step($chatid,"setqoflpmapi");
sm($chatid,$txt,$keyback);
										}
							}
														}elseif($step=="setqoflpmapi"){
														if($text=="برگشت↪"){
				step($chatid,"qoflpm");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflpm);
						}else{
							if(isset($text)){
								$link = "https://web.sms-bot.ir/balance.php?apikey=$text&type=amount";
											$res = json_decode(file_get_contents($link),true);
											if($res['status_code']=="ok"){
												setvalue("data","id",2,"phoneiran",$text);
												step($chatid,"qoflpm");
												$txt="کلید وبسرویس Api Key شما با موفقیت تنظیم شد✅";
												sm($chatid,$txt,$keyqoflpm);
												}else{
													$txt="⚠️مشکل در اتصال به کلید وبسرویس Api key

💠لطفا از صحت کلید وبسرویس اطمینان حاصل و دوباره تست نمایید !!!";
													sm($chatid,$txt);
													}
								}
							}
							}elseif($step=="setqolfpmersal"){
														if($text=="برگشت↪"){
				step($chatid,"qoflpm");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflpm);
						}else{
							if(isset($text)){
								setvalue("data","id",2,"textphone1",$text);
								step($chatid,"qoflpm");
								$txt="متن شما با موفقیت تغییر یافت ✅";
								sm($chatid,$txt,$keyqoflpm);
								}
								}
								}elseif($step=="setqoflpmesh"){
														if($text=="برگشت↪"){
				step($chatid,"qoflpm");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflpm);
						}else{
							if(isset($text)){
								setvalue("data","id",2,"textphone2",$text);
								step($chatid,"qoflpm");
								$txt="متن شما با موفقیت تغییر یافت ✅";
								sm($chatid,$txt,$keyqoflpm);
								}
								}
								}elseif($step=="autooff"){
														if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="فعالسازی✅"){
								setvalue("data","id",2,"botpower","on");
							sm($chatid,"امکان روشن خاموش کردن ربات بصورت خودکار فعال شد✅");
							
								}elseif($text=="غیرفعالسازی⛔"){
									setvalue("data","id",2,"botpower","off");
							sm($chatid,"امکان روشن خاموش کردن ربات بصورت خودکار غیرفعال شد✅");
							
									}elseif($text=="تنظیم زمان خاموش🔇"){
										$txt="
💠لطفا ساعت و دقیقه ی خاموش شدن ربات رو بصورت  فرمت زیر بفرستید 

مثال :
24:00
";
step($chatid,"autooffoff");
sm($chatid,$txt,$keyback);
										}elseif($text=="تنظیم زمان روشن🔊"){
										$txt="
💠لطفا ساعت و دقیقه ی روشن شدن ربات رو بصورت  فرمت زیر بفرستید 

مثال :
07:00
";
step($chatid,"autooffon");
sm($chatid,$txt,$keyback);
										}
										}
										}elseif($step=="autooffoff"){
														if($text=="برگشت↪"){
				step($chatid,"autooff");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflpm);
						}else{
							if(isset($text) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $text)){
								setvalue("data","id",2,"autodel",$text);
								step($chatid,"autooff");
								$txt="زمان خاموشی خودکار ربات با موفقیت تغییر یافت✅";
								sm($chatid,$txt,$keyautooff);
								}
								}
								}elseif($step=="autooffon"){
														if($text=="برگشت↪"){
				step($chatid,"autooff");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflpm);
						}else{
							if(isset($text) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $text)){
								setvalue("data","id",2,"timeautodel",$text);
								step($chatid,"autooff");
								$txt="زمان روشنی ربات با موفقیت تغییر یافت✅";
								sm($chatid,$txt,$keyautooff);
								}
								}
								}elseif($step=="warnsetting"){
									if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}elseif($text=="تنظیم حداکثر اخطار💢"){
							step($chatid,"maxwarn");
							sm($chatid,"🔰لطفا تعیین کنید کاربر حداکثر تا چند میتواند اخطار دریافت کند ؟!!\n\n⚠لطفا عدد بین 3 تا 10 وارد نمایید",$keyback);
							}elseif($text=="تنظیم متن دریافت حداکثر اخطار📛"){
							step($chatid,"textmaxwarn");
							sm($chatid,"🔰لطفا متنی که بعد از حداکثر اخطار برای کاربر ارسال میشود را وارد کنید :",$keyback);
							}elseif($text=="تنظیم متن دریافت اخطار🛂"){
							step($chatid,"textwarn");
							sm($chatid,"🔰لطفا متنی که بعد از اخطار برای کاربر فرستاده میشود را وارد کنید",$keyback);
							}elseif($text=="نوع واکنش ربات در حداکثر اخطار⛔"){
							step($chatid,"typewarn");
							sm($chatid,"🔰🌟لطفا نوع واکنش ربات بعد از حداکثر اخطار برای کاربر را انتخاب کنید

➖بلاک کردن : کاربر را در لیست بلاک قرار می دهد.
➖محدودیت زمانی : کاربر را در لیست ضداسپم قرار می دهد.",$keywarn2);
							}elseif($text=="صفر کردن تمام اخطار ها♻"){
							step($chatid,"resetwarn");
							sm($chatid,"
با زدن گزینه ی بله ، تمام اخطار های کاربران صفر میشود

➖آیا از انجام کار مطمعن هستید ؟!!
",$keynoyes);
							}
									}elseif($step=="maxwarn"){
										if($text=="برگشت↪"){
				step($chatid,"warnsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keywarn);
						}else{
							if($text >= 3 && $text <= 10){
								setvalue("data","id",1,"maxwarn",$text);
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت ثبت شد✅",$keywarn);
								}else{
									sm($chatid,"لطفا یک عدد بین 3 تا 10 وارد کنید!!!!");
									}
							}
										}elseif($step=="textmaxwarn"){
										if($text=="برگشت↪"){
				step($chatid,"warnsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keywarn);
						}else{
							if(isset($text)){
								setvalue("data","id",2,"maxwarn",$text);
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت ثبت شد✅",$keywarn);
								}else{
									sm($chatid,"⚠لطفا فقط متن وارد کنید !!!");
									}
							}
										}elseif($step=="textwarn"){
										if($text=="برگشت↪"){
				step($chatid,"warnsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keywarn);
						}else{
							if(isset($text)){
								setvalue("data","id",1,"textwarn",$text);
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت ثبت شد✅",$keywarn);
								}else{
									sm($chatid,"⚠لطفا فقط متن وارد کنید !!!");
									}
							}
										}elseif($step=="typewarn"){
										if($text=="برگشت↪"){
				step($chatid,"warnsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keywarn);
						}else{
							if($text=="بلاک کردن کاربر"){
								setvalue("data","id",2,"textwarn","block");
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت ثبت شد✅",$keywarn);
								}elseif($text=="محدودیت زمانی کاربر"){
								setvalue("data","id",2,"textwarn","spam");
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت ثبت شد✅",$keywarn);
								}else{
									sm($chatid,"⚠لطفا از روی کیبورد انتخاب کنید !!!");
									}
							}
										}elseif($step=="resetwarn"){
										if($text=="برگشت↪"){
				step($chatid,"warnsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keywarn);
						}else{
							if($text=="بله✅"){
								$tevv = getallvalue("user","chatid");
					foreach($tevv as $key){
							setvalue("user","chatid",$key,"tedadwarn",0);
						}
								step($chatid,"warnsetting");
								sm($chatid,"با موفقیت پاکسازی شدند✅",$keywarn);
								}elseif($text=="خیر🚫"){
									step($chatid,"warnsetting");
								sm($chatid,"لغو شد ✅\n\nیک گزینه را انتخاب کنید :",$keywarn);
								}else{
									sm($chatid,"⚠لطفا از روی کیبورد انتخاب کنید !!!");
									}
							}
										}elseif($step=="taiinforward"){
										if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if($text=="متن"){
								step($chatid,"forwardmatn");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="فوروارد"){
								step($chatid,"forwardforward");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="عکس"){
								step($chatid,"forwardaks");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="فیلم"){
								step($chatid,"forwardfilm");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="استیکر"){
								step($chatid,"forwardsticker");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="دایس"){
								step($chatid,"forwarddice");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="ویس"){
								step($chatid,"forwardvoice");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="آهنگ"){
								step($chatid,"forwardaudio");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="فایل"){
								step($chatid,"forwardfile");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}elseif($text=="ویدیو نوت"){
								step($chatid,"forwardvideonote");
								sm($chatid,"آیا میخواهید امکان ارسال $text  مهیا باشد یا خیر؟!!",$keyfaal);
								}else{
									sm($chatid,"⚠لطفا از روی کیبورد انتخاب کنید !!!");
									}
							}
										}elseif($step=="forwardforward"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",2,"matnforward","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",2,"matnforward","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardmatn"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",1,"matnforward","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",1,"matnforward","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardaks"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",1,"aksfilm","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",1,"aksfilm","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardfilm"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",2,"aksfilm","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",2,"aksfilm","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardsticker"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",1,"stickerdice","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",1,"stickerdice","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardice"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",2,"stickerdice","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",2,"stickerdice","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardvoice"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",1,"voiceaudio","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",1,"voiceaudio","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardaudio"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",2,"voiceaudio","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",2,"voiceaudio","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardfile"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",1,"filevideonote","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",1,"filevideonote","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="forwardvideonote"){
											if($text=="برگشت↪"){
				step($chatid,"taiinforward");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytaiinforward);
						}else{
							if($text=="فعال کن✅"){
								step($chatid,"taiinforward");
								setvalue("data","id",2,"filevideonote","on");
								sm($chatid,"با موفقیت فعال شد✅",$keytaiinforward);
								}elseif($text=="غیرفعال کن🚫"){
									step($chatid,"taiinforward");
									setvalue("data","id",2,"filevideonote","off");
								sm($chatid,"با موفقیت غیرفعال شد✅",$keytaiinforward);
									}
							}
											}elseif($step=="getfileids"){
											if($text=="برگشت↪"){
				step($chatid,"sayersetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysayer);
						}else{
							if(isset($photo)){
								sm($chatid,"Type : Photo\n\nFile ID : <code> $photo </code>");
								}elseif(isset($video)){
								sm($chatid,"Type : video\n\nFile ID : <code> $video </code>");
								
							}elseif(isset($audio)){
								sm($chatid,"Type : Audio\n\nFile ID : <code> $audio </code>");
								}elseif(isset($voice)){
								sm($chatid,"Type : Voice\n\nFile ID : <code> $voice </code>");
								}elseif(isset($document)){
								sm($chatid,"Type : Document\n\nFile ID : <code> $document </code>");
								}elseif(isset($sticker)){
								sm($chatid,"Type : Sticker\n\nFile ID : <code> $sticker </code>");
								}elseif(isset($video_note)){
								sm($chatid,"Type : Video Note\n\nFile ID : <code> $video_note </code>");
								}
								}
											}
elseif($step=="sayersetting"){
							if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						}elseif($text=="بروزرسانی وضعیت تنظیمات♻"){
if(getLockjoin()=="on"){
							$join =  "روشن✅";
							}else{
							$join = "خاموش🚫";
							}
							if(getBotpower()=="off"){
							$joirn =  "روشن✅";
							}else{
							$joirn = "خاموش🚫";
							}
							if(getvalue("data","id",1,"replacetext")=="on"){
							$replace =  "روشن✅";
							}else{
							$replace = "خاموش🚫";
							}
							if(getvalue("data","id",1,"filter")=="on"){
							$filter =  "روشن✅";
							}else{
							$filter = "خاموش🚫";
							}
							if(getvalue("data","id",1,"lockrobot")=="on"){
							$robot =  "روشن✅";
							}else{
							$robot = "خاموش🚫";
							}
							if(getvalue("data","id",1,"captha")=="on"){
							$captha =  "روشن✅";
							}else{
							$captha = "خاموش🚫";
							}
							if(getvalue("data","id",1,"lockphone")=="on"){
							$phon =  "روشن✅";
							}else{
							$phon = "خاموش🚫";
							}
							if(getvalue("data","id",1,"phoneiran")=="on"){
							$phon2 =  "روشن✅";
							}else{
							$phon2 = "خاموش🚫";
							}
							if(getvalue("data","id",1,"autodel")=="on"){
							$autodel =  "روشن✅";
							}else{
							$autodel = "خاموش🚫";
							}
							if(getvalue("data","id",2,"lockphone")=="on"){
							$qoflersal =  "روشن✅";
							}else{
							$qoflersal = "خاموش🚫";
							}
							if(getvalue("data","id",1,"sendlog")=="on"){
							$logchn =  "روشن✅";
							}else{
							$logchn = "خاموش🚫";
							}
							if(getvalue("data","id",2,"botpower")=="on"){
							$autoonoff =  "روشن✅";
							}else{
							$autoonoff = "خاموش🚫";
							}
							if(getvalue("data","id",1,"webview")=="on"){
							$webview =  "فعال✅";
							}else{
							$webview = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"typing")=="on"){
							$istype =  "فعال✅";
							}else{
							$istype = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"resize")=="on"){
							$dokide =  "فعال✅";
							}else{
							$dokide = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"replymessage")=="on"){
							$replyto =  "فعال✅";
							}else{
							$replyto = "غیرفعال🚫";
							}
							if(getvalue("data","id",1,"sendtomail")=="on"){
							$sendmail =  "فعال✅";
							}else{
							$sendmail = "غیرفعال🚫";
							}
							


$txt="
💠وضعیت اکنون : 

💠ربات $joirn هست
💠روشن خاموش خودکار $autoonoff هست
➖➖➖➖➖➖➖➖➖➖
💠جوین اجباری ربات $robot هست
💠جوین اجباری کانال $join هست
💠قفل با کپچا $captha هست
💠قفل با شماره اجباری $phon هست
💠قفل با شماره اجباری ایران $phon2 هست
💠قفل با ارسال پیامک $qoflersal هست
➖➖➖➖➖➖➖➖➖➖
💠کانال Log $logchn هست
💠جایگزین متن خودکار $replace هست
💠حذف خودکار کلمات $autodel هست
💠فیلتر کلمات $filter هست
💠ارسال ایمیل $sendmail هست
➖➖➖➖➖➖➖➖➖➖
💠وضعیت وب ویو متن $webview هست
💠وضعیت در حال نوشتن $istype هست
💠سایز ایده ال دکمه ها $dokide هست
💠ریپلی پیام های ربات $replyto هست
➖➖➖➖➖➖➖➖➖➖
";
sm($chatid,$txt,$keysayer);
}elseif($text=="دستورات ربات💈"){
							step($chatid,"command");
							sm($chatid,"شما در این قسمت میتوانید برای ربات خود دستور بسازید

🔰بدون نیاز به ربات باتفادر

⚠️توجه کنید این دستورات بعد از ارسال / در چت ربات ظاهر میشوند ، و به تنهایی عملکرد خاصی ندارند و باید برای هر دستور دکمه ای تعیین و از قسمت تعیین دستور اقدام به تعیین دستور کنید.

از گزینه های زیر استفاده کنید :",$keycommand);
							}
elseif($text=="وضعیت درحال نوشتن📝"){
							step($chatid,"istyping");
$txt="لطفا انتخاب کنید که آیا میخواهید وضعیت درحال نوشتن برای ربات فعال شود یا خیر?!";
sm($chatid,$txt,$keyfaal);
}elseif($text=="ارسال پیام ها به ایمیل📧"){
							step($chatid,"sendToMail");
$txt="
🌟در این قسمت شما میتوانید برای ربات خود یک ایمیل تنظیم کنید
تا در مواقعی که شما در تلگرام حضور ندارید ، ربات پیام های دریافتی شما را در قسمت های (ارسال به ادمین ، پیامرسان سراسری)‌ که توسط کاربرا برای ارتباط با شما فرستاده میشوند را به ایمیل شما ارسال کند .


➖لطفا یک گزینه را انتخاب کنید :
";
sm($chatid,$txt,$keymail);
}elseif($text=="تنظیم محدودیت ارسال🔀"){
							step($chatid,"taiinforward");
$txt="
🌟شما در این قسمت میتوانید که چه مواردی در ربات شما غیرقابل ارسال برای دیگران باشند .
در صورت فعال کردن هر مورد 
دیگر مواردی مثل forward , share برای کاربر نمایش داده نمی شوند


➖لطفا یک گزینه را انتخاب کنید
";
sm($chatid,$txt,$keytaiinforward);
}elseif($text=="تنظیمات اخطار Warn🚫"){
							step($chatid,"warnsetting");
$txt="
🌟شما در اینجا میتوانید تعیین کنید تا حداکثر چه تعداد اخطار می تواند کاربر دریافت کند.
همچنین میتوانید تعیین کنید بعد از دریافت حداکثر اخطار ، ربات کاربر را به چه صورت محدود کند 


➖لطفا یک گزینه را انتخاب کنید
";
sm($chatid,$txt,$keywarn);
}elseif($text=="وضعیت وب ویو متن📑"){
	step($chatid,"webview");
	$txt="لطفا انتخاب کنید که آیا میخواهید وب ویو در متن ها فعال شود یا غیرفعال؟!!";
sm($chatid,$txt,$keyfaal);
}elseif($text=="پاکسازی فایل های آپلود شده📤"){
	step($chatid,"deleteuploadfile");
	$txt="با این کار تمام فایل هایی که قبلا اپلود کرده اید پاک خواهند شد.

⚠️لینک هایی که از قبل واسه اپلود بدست آمده اند، از کار می افتند!!

آیا از انجام کار خود مطمعن هستید :؟!";
sm($chatid,$txt,$keynoyes);
}elseif($text=="پاکسازی متغییر SHARE📤"){
	step($chatid,"deletesharefile");
	$txt="با این کار تمام متغییر های SHARE که قبلا در متن ها استفاده کرده اید پاک خواهند شد.

⚠️پست هایی که قبلا دارای دکمه SHARE بودند از کار می افتند و کاربر باید ، پست جدید را برای SHARE کردن انتخاب نماید

آیا از انجام کار خود مطمعن هستید :؟!";
sm($chatid,$txt,$keynoyes);
}elseif($text=="پاکسازی متغییر ONCE🔂"){
	step($chatid,"deleteonce");
	$txt = "
با این کار تمام متغییر های ONCE که قبلا در متن ها استفاده کرده اید پاک خواهند شد.

⚠️پست هایی که قبلا دارای دکمه ONCE بودند صفر میشوند و کاربر می تواند دوباره از مرحله ی اول از این دکمه استفاده نماید

آیا از انجام کار خود مطمعن هستید :؟!

";
	sm($chatid,$txt,$keynoyes);
}elseif($text=="سایز ایده ال دکمه🎚"){
	step($chatid,"resize");
	$txt="لطفا انتخاب کنید که آیا میخواهید سایز ایده ال دکمه ها فعال شود یا غیرفعال؟!!";
sm($chatid,$txt,$keyfaal);
}elseif($text=="تغییر نوع نمایش متغییر ONCE🎫"){
	step($chatid,"once");
	$txt="لطفا نوع نمایش متن برای متغییر once را انتخاب کنید :";
sm($chatid,$txt,$keyonce);
}elseif($text=="ریپلی پیام های ربات⤵️"){
	step($chatid,"replymessage");
	$txt="لطفا انتخاب کنید که آیا میخواهید ربات به پیام های کاربران ریپلی کنید یا خیر ?!!";
sm($chatid,$txt,$keyfaal);
}elseif($text=="ساخت کد دکمه🔏"){
	step($chatid,"codesetting");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keycodedok);
}elseif($text=="قفل شماره اجباری📞"){
	step($chatid,"lockphone");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keyphone);
}elseif($text=="تنظیمات متغییر لایک👍"){
	step($chatid,"varlike");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keylike);
}elseif($text=="قفل با کپچا🔐"){
	step($chatid,"lockcaptha");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keycaptha);
}elseif($text=="فیلتر کلمات🚨"){
	step($chatid,"filter");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keyfilter);
}elseif($text=="پاکسازی دکمه های 0⃣"){
	$keykarr=json_encode([
       "hide_keyboard"=>true
       ]);
sm($chatid,"در حال پاکسازی ، لطفا صبر کنید :",$keykarr);
$getall = getallvalue("dok","dokme");
foreach($getall as $vb){
	if(!empty(getInto($vb))){
		$into=getInto($vb);
		$ddokme=getDokmetext($vb);
		$kky = getDokmetext($into);
		if(strpos($kky,'"text":"0"},')){
		$cc = str_replace('{"text":"0"},',"",$kky);
		$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}elseif(strpos($kky,',{"text":"0"}]')){
		$cc = str_replace(',{"text":"0"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}else{
		$cc = str_replace('{"text":"0"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}
		
		}
	}
	$key = getKeyboard();
	if(strpos($key,'"text":"0"},')){
		$cc = str_replace('{"text":"0"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}elseif(strpos($key,',{"text":"0"}]')){
		$cc = str_replace(',{"text":"0"}',"",$key);
	$cp = str_replace("[],","",$cc);
		setKeyboard($cp);
	}else{
		$cc = str_replace('{"text":"0"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}
		$txt="🌟تمام دکمه هایی که با 0  ساخته شده بودند ، با موفقیت پاک شدند✅";
		sm($chatid,$txt,$keysayer);
}elseif($text=="کانال Log🖇"){
step($chatid,"log");
sm($chatid,"لطفا یک گزینه را انتخاب کنید :",$keylog);
}elseif($text=="حذف خودکار کلمات⭕️"){
	step($chatid,"del");
	$txt="یک گزینه را انتخاب کنید";
sm($chatid,$txt,$keydel);
}elseif($text=="تنظیم روشن خاموش خودکار ربات⏰"){
	step($chatid,"autooff");
	$txt="
🌟شما در این قسمت میتوانید ، بازه ی زمانی برای اجرای ربات به دستورات کاربران تعریف کنید .

⚠️توجه کنید که این قسمت نیازمند اپدیت ربات هست ، پس برای جوابگویی بهتر نیاز هست آمار کاربران ربات شما بالا باشد.

⚠️زمان ها را دقیق و بر اساس 24 ساعت تنظیم کنید
";
sm($chatid,$txt,$keyautooff);
}elseif($text=="دریافت فایل ایدی📎"){
	step($chatid,"getfileids");
	$txt="
🌟شما در این قسمت میتوانید ، ایدی فایل هایی که میفرستید را دریافت کنید 
و در قسمت های دستوراتی مانند 
SENDPHOTO()
SENDVIDEO()
SENDAUDIO()
SENDDOCUMENT()
و ...

استفاده نمایید


➖لطفا فایل های خود را بفرستید
";
sm($chatid,$txt,$keyback);
}elseif($text=="قفل ربات با ارسال پیامک📩"){
	step($chatid,"qoflpm");
	$txt="
🌟شما در این بخش میتوانید برای ربات خود قفل با قابلیت ارسال کد با پیامک ایجاد کنید

⚠️برای تنظیم Api key 
ابتدا به ربات @SmSMizbanBot مراجعه کنید و دریافت کلید وبسرویس را بزنید
و توکنی که در اختیار شما قرار میدهد رو در تنظیم Api key جایگزین کنید

⚠️توجه کنید ، حتما حساب خود را همیشه شارژ نگه دارید!!! ، در غیر این صورت امکان ارسال پیامک برای کاربر مهیا نخواهد بود!!!

⚠️متن های این قسمت ، از جمله تغییر اسم دکمه دریافت شماره ، شماره ی اشتباه و غیره را از قسمت قفل شماره اجباری📞 در سایر تنظیمات ، میتوانید تغییر دهید!

یک گزینه را انتخاب کنید :
";
sm($chatid,$txt,$keyqoflpm);
}elseif($text=="حذف دکمه دستی🗑️"){
step($chatid,"deletedokmedasti");
$txt="💠شما در این قسمت میتوانید یک دکمه را بصورت دستی پاک نمایید.


⚠️لطفا اسم دکمه را وارد نمایید تا پاک شود :";
sm($chatid,$txt,$keyback);
}elseif($text=="📥آپلود فایل"){
	step($chatid,"UploadFile");
$txt="⭐️لطفا محتوای خود را ارسال کنید تا در سرور ذخیره شود :

⚠️شما میتوانید ، فیلم ، موزیک ، فایل ، عکس ، گیف و ... را در ربات اپلود نمایید

⚠️حداکثر حجم مجاز برای اپلود 2 گیگ میباشد.";
sm($chatid,$txt,$keyback);
}elseif($text=="🔁جایگزین متن"){
	step($chatid,"replace");
	$txt="💎شما در این قسمت میتوانید ، یک متن ورودی کاربر را به یک متن دیگر تغییر دهید

⚠️به عنوان مثال شما متن سلام رو برای Hi در ربات ثبت میکنید
سپس کاربر در ربات عبارت سلام را میفرستد ، اما آنچه در خروجی نمایش داده میشود عبارت Hi هست

تنظمیات را میتوانید با کیبورد زیر انجام دهید👇👇👇👇";
sm($chatid,$txt,$keyreplace);
}elseif($text=="محدود کردن ربات برای افراد🛂"){
	step($chatid,"mahdod");
	$txt="
💥شما در این قسمت میتوانید ربات را فقط برای افراد خاص فعال نگه دارید

💠با وارد کردن ایدی عددی فرد و فعال کردن این قسمت ، ربات فقط برای همان فرد پاسخگو خواهد بود


لطفا یک گزینه را انتخاب کنید :
";
sm($chatid,$txt,$keymahdod);
}elseif($text=="تنظیم فوروارد شروع↪"){
step($chatid,"tanzimforward");
sm($chatid,"یک گزینه را انتخاب کنید",$keyforward);
}elseif($text=="وضعیت قفل زیرمجموعه گیری🫂"){
							step($chatid,"zirmajsetting");
							$txt="یک گزینه را  انتخاب کنید :";
							sm($chatid,$txt,$keyzirmaj);
								}elseif($text=="تنظیمات امتیازگیری⚜"){
							step($chatid,"coinsetting");
							$txt="یک گزینه را  انتخاب کنید :";
							sm($chatid,$txt,$keycoin);
								}elseif($text=="تنظیم جوین اجباری💥"){
						step($chatid,"joinchannel");
						$txt="یک گزینه را انتخاب کنید :";
						sm($chatid,$txt,$keychannel);
							}elseif($text=="🤖قفل اجباری ربات"){
						step($chatid,"joinrobot");
						$txt="یک گزینه را انتخاب کنید :";
						sm($chatid,$txt,$keyrobot);
							}elseif($text=="بهینه سازی ربات🔋"){
						sm($chatid,"درحال بهینه سازی....\n\nلطفا صبر کنید");
						em($chatid,"بهینه سازی انجام شد✅",$messageid+1);
						}elseif($text=="خاموش کردن ربات⛔"){
							setBotpower("on");
							sm($chatid,"ربات با موفقیت خاموش شد✅");
							}elseif($text=="روشن کردن ربات✅"){
								setBotpower("off");
							sm($chatid,"ربات با موفقیت روشن شد✅");
							}
							}elseif($step=="groupsetting"){
						if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
						}elseif($text=="♻وضعیت تنظیمات♻"){
							if(getNewozv()=="off"){
												$ozv = "غیرفعال🚫";
												}elseif(getNewozv() == "on"){
													$ozv = "فعال✅";
													}
												if(getDeletelink()=="off"){
												$link = "غیرفعال🚫";
												}elseif(getDeletelink() == "on"){
													$link = "فعال✅";
													}
												if(getKeygroup()=="off"){
												$pm = "غیرفعال🚫";
												}elseif(getKeygroup() == "on"){
													$pm = "فعال✅";
													}
													if(getvalue("data","id",1,"leavegroup") == "on"){
													$leave = "فعال✅";
													}else{
													$leave = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"leavegroup") == "on"){
													$dast = "فعال✅";
													}else{
													$dast = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"deletelink") == "on"){
													$usernam = "فعال✅";
													}else{
													$usernam = "🚫غیرفعال";
													}
													if(getvalue("data","id",2,"newozv") == "on"){
													$forw = "فعال✅";
													}else{
													$forw = "🚫غیرفعال";
													}
													
												
											$txt="💠نمایش کیبورد در گروه : $pm\n\n💠پیام خوش آمدگویی : $ozv\n\n💠لینک پاک کن : $link\n\n💠ضد یوزرنیم : $usernam\n\n💠ضد فوروارد : $forw\n\n💠لفت خودکار گروه : $leave\n\n💠تعیین دسترسی ادمین و مدیران گروه : $dast";
											sm($chatid,$txt);
							}elseif($text=="فعال و غیرفعال کردن صفحه کلید در گروه"){
								step($chatid,"keyoffon");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="فعال و غیرفعال کردن پیام عضو جدید"){
								step($chatid,"ozvoffon");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="فعال و غیرفعال کردن لینک پاک کن"){
								step($chatid,"linkoffon");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="تعیین دسترسی ادمین و مدیران گروه"){
								step($chatid,"taiindast");
								$txt="شما میتوانید در این قسمت تنظیم کنید که آیا مدیر و ادمین های گروه تنظیمات مخصوص گروه را تغییر دهند یا خیر؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="لفت خودکار از گروه"){
								step($chatid,"leaveoffon");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="فعال و غیرفعال کردن ضدیوزرنیم"){
								step($chatid,"offonantiusername");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}elseif($text=="فعال و غیرفعال کردن ضد فوروارد"){
								step($chatid,"offonantiforward");
								$txt="آیا میخواهید این گزینه فعال شود یا غیرفعال؟!!";
								sm($chatid,$txt,$keyfaal);
								}
							
								}elseif($step=="keyoffon"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
				setKeygroup("off");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
					setKeygroup("on");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="leaveoffon"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
				setvalue("data","id",1,"leavegroup","off");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
					setvalue("data","id",1,"leavegroup","on");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="taiindast"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
				setvalue("data","id",2,"leavegroup","off");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
					setvalue("data","id",2,"leavegroup","on");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="offonantiusername"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
				setvalue("data","id",2,"deletelink","off");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
					setvalue("data","id",2,"deletelink","on");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="offonantiforward"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
				setvalue("data","id",2,"newozv","off");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
					setvalue("data","id",2,"newozv","on");
						step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}

elseif($step=="linkoffon"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
					setDeletelink("off");
					step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
						setDeletelink("on");
					step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="ozvoffon"){
									if($text=="برگشت↪"){
				step($chatid,"groupsetting");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keygroup);
				}elseif($text=="غیرفعال کن🚫"){
					setNewozv("off");
					step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت غیرفعال شد✅";
					sm($chatid,$txt,$keygroup);
					}elseif($text=="فعال کن✅"){
						setNewozv("on");
					step($chatid,"groupsetting");
					$txt="گزینه ی مورد نظر شما با موفقیت فعال شد✅";
					sm($chatid,$txt,$keygroup);
					}
									}elseif($step=="amarkarbarbot"){
										if(isset($update->message->forward_from)){
					$id = $update->message->forward_from->id;
					$user = $update->message->forward_from->username;
					$name = $update->message->forward_from->first_name;
					if(!empty(getvalue("user","chatid",$id,"chatid"))){
						$joinsh = getvalue("user","chatid",$id,"joindatesh");
						$joinm = getvalue("user","chatid",$id,"joindatem");
						$jointime = getvalue("user","chatid",$id,"jointime");
						$number = getvalue("user","chatid",$id,"phone");
						$zirmaj = getvalue("user","chatid",$id,"zirmaj");
						$coin = getvalue("user","chatid",$id,"emtiaz");
						$captha = getvalue("user","chatid",$id,"captha");
						if($captha =="true"){
							$captha =="انجام شده✅";
						}else{
							$captha =="انجام نشده⛔";
							}
							$tem1 = "💠جزو کاربران ربات : ✅هست
💠زیرمجموعه ها : $zirmaj
💠امتیازات کاربر : $coin
💠شماره تلفن : $number
💠تاریخ عضویت میلادی : $joinm
💠تاریخ عضویت شمسی : $joinsh
💠زمان عضویت : $jointime
💠وضعیت کپچا : $captha";
							}else{
								$tem1 = "💠جزو کاربران ربات : ⛔نیست";
								}
							$txt = "💠ایدی عددی کاربر : $id\n\n💠اسم کاربر : $name\n\n💠یوزرنیم کاربر : @$user\n\n💠منشن 1 : <a href='tg://user?id=$id'>$name</a>\n💠منشن 2 : <a href='tg://openmessage?user_id=$id'>$name</a>\n$tem1";
			step($chatid,"amarbot");
			sm($chatid,$txt,$keyamar);
							}else{
								if($text=="برگشت↪"){
				step($chatid,"amarbot");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyamar);
				}else{
				$k = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getchat?chat_id=$text"),true);
				$res = $k["ok"];
				$id = $k['result']['id'];
				$user = $k['result']['username'];
				$name = $k['result']['first_name'];
				if($res==false){
					$txt="🚫چت ایدی غلط میباشد\nلطفا به موارد زیر دقت فرمایید :\n\n💠توجه داشته باشید چت ایدی شما حتما درست باشد.\n💠پیام فوروارد شده از طرف کاربر باشد\n💠پیام فوروارد شده قفل نباشد!!\n\nبا شرایط بالا لطفا دوباره چت ایدی را بفرستید :";
					sm($chatid,$txt);
					}else{
						if(!empty(getvalue("user","chatid",$id,"chatid"))){
						$joinsh = getvalue("user","chatid",$id,"joindatesh");
						$joinm = getvalue("user","chatid",$id,"joindatem");
						$jointime = getvalue("user","chatid",$id,"jointime");
						$number = getvalue("user","chatid",$id,"phone");
						if(empty($number)){
							$number = "وارد نشده⛔";
							}
						$zirmaj = getvalue("user","chatid",$id,"zirmaj");
						$coin = getvalue("user","chatid",$id,"emtiaz");
						$captha = getvalue("user","chatid",$id,"captha");
						if($captha =="true"){
							$captha ="انجام شده✅";
						}else{
							$captha ="انجام نشده⛔";
							}
							 $tem1 = "💠جزو کاربران ربات : ✅هست
💠زیرمجموعه ها : $zirmaj
💠امتیازات کاربر : $coin
💠شماره تلفن : $number
💠تاریخ عضویت میلادی : $joinm
💠تاریخ عضویت شمسی : $joinsh
💠زمان عضویت : $jointime
💠وضعیت کپچا : $captha";
							}else{
								$tem1 = "💠جزو کاربران ربات : ⛔نیست";
								}
					$txt = "💠ایدی عددی کاربر : $id\n\n💠اسم کاربر : $name\n\n💠یوزرنیم کاربر : @$user\n\n💠منشن 1 : <a href='tg://user?id=$id'>$name</a>\n💠منشن 2 : <a href='tg://openmessage?user_id=$id'>$name</a>\n$tem1";
			step($chatid,"amarbot");
			sm($chatid,$txt,$keyamar);
					}
				}
				}
										}elseif($step=="hazfamar"){
											if($text=="خیر🚫"){
												step($chatid,"amarbot");
												sm($chatid,"به عقب برگشتید :",$keyamar);
												}elseif($text=="بله✅"){
													$ch = json_decode(file_get_contents("http://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$idbot&user_id=1393100958"),true);
													sm($chatid,json_encode($ch));
													}
											}
									elseif($step=="amarbot"){
			if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}
elseif($text=="کاربران اخیر با نمودار📊"){
	$sql = "SELECT * from `dayamar$userbott` ORDER BY `day` DESC LIMIT 5";
$result = mysqli_query($con,$sql);
if(mysqli_query($con,$sql)){
	$result = mysqli_query($con,$sql);
	}else{
		sm($admin,"Error :".mysqli_error($con));
		}
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
}
$d = 1;
//sm($chatid,json_encode($rows));
for($x=0; $x <=6; $x++){
	if(empty($rows[$x]['day'])){
		$da["roz$d"]=0;
	$da["mem$d"]=0;
		}else{
	$da["roz$d"]=$rows[$x]['day'];
	$da["mem$d"]=$rows[$x]['amar'];
	}
	$d++;
	}
	
		//sm(2097019227,"one ".$key[0]." and two ".$key[1]);
		
		
	$url = "http://mini.site-shot.com/x/codebazan.ir-Web-screenshot/500x500/png/?$domain_url/chart.php?".http_build_query($da);
			sp($chatid,$url,"تعداد کاربران پنج روز اخیر طبق نمودار📊");
						}elseif($text=="حذف بلاک کنندگان ربات از آمار🚫"){
							step($chatid,"hazfamar");
							$txt="⚠️با این دکمه کاربرانی که ربات شما را بلاک کرده اند از لیست آمار حذف خواهد شد
و اطلاعات آن ها هم پاک خواهد شد

آیا مطمعن به انجام این کار هستید ؟!!";
sm($chatid,$txt,$keynoyes);
							}
elseif($text=="دریافت مشخصات کاربر🚹"){
					step($chatid,"amarkarbarbot");
					$txt="🆔لطفا ایدی عددی فرد را بنویسید :\nمثال : $chatid \n\nیا یک پیام از کاربر را برای ما فوروارد کنید :";
					sm($chatid,$txt,$keyback);
						}elseif($text=="به روزرسانی♻"){
					step($chatid,"amarbot");
					$cou=amarcount("user");
					$supercou=amarcount("supergroup");
					$gpcou=amarcount("group");
					$sccou=amarcount("channel");
					$couu=amarcount("blocklist");
					$count=amarcount("dok");
					$list = "💠ده کاربر اخیر ربات\n\n-------------------\n";
					$sql = "SELECT * from `user$userbott` ORDER BY `chatid` DESC LIMIT 10";
$result = mysqli_query($con,$sql);
if(mysqli_query($con,$sql)){
	$result = mysqli_query($con,$sql);
	}else{
		sm($admin,"Error :".mysqli_error($con));
		}
 if(!empty($result))  {
 	while($row =mysqli_fetch_array($result))
{
$rows[] = $row;
}
foreach($rows as $row){
   if($row['username']){
   	$name ="@".$row['username'];
   }else{
   $name = "<a href='tg://openmessage?user_id=".$row['userid']."'>".$row['firstname']."</a>";
   }
   $list .= "$name\n";
}
}
					$zi = "SELECT SUM(`zirmaj`) FROM `user$userbott`";
					if(mysqli_query($con,$zi)){
						$result = mysqli_query($con,$sql);
						$row = mysqli_fetch_array($result);
$zir = $row['zirmaj'];
	}else{
		sm($admin,"Error :".mysqli_error($con));
		}
					
					$txt="$list\n------------------\n💠آمار کاربران : $cou نفر\n💠آمار گروه : $gpcou\n💠آمار سوپرگروه : $supercou\n💠آمار کانال : $sccou\n💠آمار زیرمجموعه : $zir\n💠آمار بلاک : $couu نفر\n💠دکمه های ساخته شده : $count دکمه";
					sm($chatid,$txt,$keyamar);
					}elseif($text=="انبلاک کردن✅"){
									$txt="
									لطفا ایدی یا یوزرنیم کاربری که میخوایید انبلاک شود را وارد نمایید\n\nبرای لغو عملیات برگشت را بزنید :
									";
									step($chatid,"unblock");
									sm($chatid,$txt,$keyback);
										}elseif($text=="بلاک کردن⛔"){
									$txt="
									لطفا ایدی یا یوزرنیم کاربری که میخوایید بلاک شود را وارد نمایید\n\nبرای لغو عملیات برگشت را بزنید :
									";
									step($chatid,"block");
									sm($chatid,$txt,$keyback);
										}elseif($text=="دریافت لیست بلاک⛔"){
											$get = getallvalue("blocklist","chatid");
											
											foreach ($get as $key){
												$g = file_get_contents("blocklist.txt");
												file_put_contents("blocklist.txt","$g\n------------\n$key");
												}
				$url= "https://api.telegram.org/bot".API_KEY."/sendDocument?chat_id=$chatid";
        $post = array(
         "document"  => new CURLFile(realpath('blocklist.txt'))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
        unlink("blocklist.txt");
			}elseif($text=="دریافت لیست کاربران👤"){
				$get = getallvalue("user","chatid");
											
											foreach ($get as $key){
												$joindash = getJoindatesh($key);
												$jointime = getJoindatem($key);
												$zirmaj = getZirmaj($key);
												$g = file_get_contents("karbar.txt");
												file_put_contents("karbar.txt","$g\n------------\n$key\nJoin time : $joindash-$jointime\nMember : $zirmaj");
												}
				$url= "https://api.telegram.org/bot".API_KEY."/sendDocument?chat_id=$chatid";
        $post = array(
         "document"  => new CURLFile(realpath('karbar.txt'))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
        unlink("karbar.txt");
			}elseif($text=="دریافت لیست گروه ها👥"){
				$get = getallvalue("group","chatid");
											
											foreach ($get as $key){
												$g = file_get_contents("group.txt");
												$name=getvalue("group","chatid",$key,"gpname");
												$joindatem=getvalue("group","chatid",$key,"joindatem");
												$joindatesh=getvalue("group","chatid",$key,"joindatesh");
												$jointime=getvalue("group","chatid",$key,"jointime");
												file_put_contents("group.txt","$g\n------------\n$key\n$name\n$joindatem\n$joindatesh\n$jointime");
												}
				$url= "https://api.telegram.org/bot".API_KEY."/sendDocument?chat_id=$chatid";
        $post = array(
         "document"  => new CURLFile(realpath('group.txt'))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
        unlink("group.txt");
			}elseif($text=="دریافت لیست سوپرگروه ها"){
				$get = getallvalue("supergroup","chatid");
											
											foreach ($get as $key){
												$g = file_get_contents("supergroup.txt");
												$name=getvalue("supergroup","chatid",$key,"gpname");
												$joindatem=getvalue("supergroup","chatid",$key,"joindatem");
												$joindatesh=getvalue("supergroup","chatid",$key,"joindatesh");
												$jointime=getvalue("supergroup","chatid",$key,"jointime");
												file_put_contents("supergroup.txt","$g\n------------\n$key\n$name\n$joindatem\n$joindatesh\n$jointime");
												}
				$url= "https://api.telegram.org/bot".API_KEY."/sendDocument?chat_id=$chatid";
        $post = array(
         "document"  => new CURLFile(realpath('supergroup.txt'))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
        unlink("supergroup.txt");
			}elseif($text=="دریافت لیست کانال ها📣"){
				$get = getallvalue("channel","chatid");
											
											foreach ($get as $key){
												$g = file_get_contents("channel.txt");
												$name=getvalue("channel","chatid",$key,"chname");
												$joindatem=getvalue("channel","chatid",$key,"joindatem");
												$joindatesh=getvalue("channel","chatid",$key,"joindatesh");
												$jointime=getvalue("channel","chatid",$key,"jointime");
												file_put_contents("channel.txt","$g\n------------\n$key\n$name\n$joindatem\n$joindatesh\n$jointime");
												}
				$url= "https://api.telegram.org/bot".API_KEY."/sendDocument?chat_id=$chatid";
        $post = array(
         "document"  => new CURLFile(realpath('channel.txt'))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
			}
			unlink("channel.txt");
			}elseif($step=="getidkarbar"){
					if($text=="برگشت↪"){
				step($chatid,"sendtoall");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
		}else{
		$k = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getchat?chat_id=$text"),true);
				$res = $k["ok"];
				$id = $k['result']['id'];
				$user = $k['result']['username'];
				$name = $k['result']['first_name'];
				if($res==false){
					$txt="🚫چت ایدی غلط میباشد\nلطفا به موارد زیر دقت فرمایید :\n\n💠توجه داشته باشید چت ایدی شما حتما درست باشد.\n💠با شرایط بالا لطفا دوباره چت ایدی را بفرستید :";
					sm($chatid,$txt);
					}else{
						setOther($chatid,$id);
						$txt="💥محتوا را بفرستید تا برای کاربر ارسال کنم :

⭐️محتوای شما میتواند شامل فیلم ، عکس ، متن ، استیکر ، موزیک و فایل باشد

⭐️شما میتوانید از html استفاده نمایید

⭐️شما میتوانید از کیبورد شیشه ای برای ارسال استفاده نمایید

آموزش ارسال کیبورد شیشه ای در کانال زیر 
 ";
										step($chatid,"sendidkarbar");
			sm($chatid,$txt,$keyback);
					}
		}
				}elseif($step=="sendidkarbar"){
						if($text=="برگشت↪"){
				step($chatid,"sendtoall");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
		}else{
			$id = getOther($chatid);
			if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$caption);
	$caption = $hi[0];
		$kei = textToinline("%$k%",$caption);
		}
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
		
		if(isset($photo)){
			sp($id,$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($id,$video,$caption,$kei);
				}elseif(isset($document)){
					sd($id,$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($id,$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($id,$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($id,$sticker);
								}elseif(isset($text)){
	sm($id,$text,$kei);
}
step($chatid,"sendtoall");
sm($chatid,"متن شما با موفقیت برای کاربر ارسال شد✅\n\nبه پنل بازگشتید :",$keyersal);

	}
						}
					
				elseif($step=="sendtoall"){
				if($text=="برگشت↪"){
				step($chatid,"panel");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}else{
				if($text=="ارسال کاربران📢"){
						step($chatid,"sendall");
					$txt="
					پیام خود را در بفرستید تا برای کاربران ارسال شود :
			";
					
												sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال پیام به یک کاربر🚹"){
							step($chatid,"getidkarbar");
							$txt="💠لطفا ایدی عددی فرد را بفرستید \n\nتوجه داشته باشید کاربر باید جزو کاربران ربات باشد.";
							sm($chatid,$txt,$keyback);
										}elseif($text=="فوروارد کاربران↩"){
						step($chatid,"forward");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای کاربران ربات فوروارد شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به گروه ها👥"){
						step($chatid,"sendgp");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای گروه های ربات ارسال شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به سوپرگروه ها👥"){
						step($chatid,"sendsupergp");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای سوپرگروه ها ارسال شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به کانال📣"){
						step($chatid,"sendchannel");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای کانال ربات ارسال شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}elseif($text=="فوروارد به سوپرگروه↪"){
						step($chatid,"forwardsupergp");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای سوپرگروه ها فوروارد شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}elseif($text=="فوروارد به گروه↪"){
						step($chatid,"forwardgp");
						$txt="
						متن خود را بفرستید یا از کانال یا گپ های دیگر فوروارد کنید\nتا برای گروه های ربات فوروارد شود\n\nبرای لغو عملیات برگشت را بزنید.
						";
						sm($chatid,$txt,$keyback);
						}
					}
				}elseif($step=="isendadmin$ch"){
	if($text=="برگشت↪"){
		step($chatid,"edit2");
		sm($chatid,"به منو برگشتید",$keysendadmin);
		}else{
			step($chatid,"edit2");
				if(!isset($dokme["resid$ch"]) || $dokme["resid$ch"]==null){
					
					
		$ch13="پیام شما ارسال شد✅";
		}else{
	$tx=	$dokme["resid$ch"];
	$ch1=str_replace("FIRSTNAME",$firstname,$tx);
			$ch2 = str_replace("USERNAME",$username,$ch1);
			$ch3 = str_replace("USERID",$fromid,$ch2);
			$ch4 = str_replace("TEXT",$text,$ch3);
			$ch5 = str_replace("HOUR",date('h'),$ch4);
			$ch6 = str_replace("MINUTE",date('i'),$ch5);
			$ch7 = str_replace("SECOND",date("s"),$ch6);
			$ch8 = str_replace("MEMBER",$data["zirmajj$fromid"],$ch7);
$ch9 = str_replace("LINK","https://t.me/$botuser?start=$fromid",$ch8);
$ch10 = str_replace("ALLMEM",$dokme["tedadzirmaj$text"],$ch9);
$ch11 = str_replace("BOTNAME",$botname,$ch10);
$ch12 = str_replace("BOTUSER",$botuser,$ch11);
$ch13 = str_replace("IDBOT",$idbot,$ch12);
		}
			
			sm($chatid,$ch13,$keysendadmin);
			$txt="
			📨یک پیام دریافت شد

⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️اسم دکمه : $ch
⭐️متن پیام : 👇👇👇";
			sm($chatid,$txt);
			fm($chatid,$chatid,$messageid);
				}
					}elseif($step=="igetApi$ch"){
	if($text=="برگشت↪"){
		step($chatid,"edit2");
		sm($chatid,"به منو برگشتید",$keydokme);
		}else{
		$gget = json_decode(file_get_contents("dokme/$ch.json"),true);
				$get=$gget['text'];
	$ch1=str_replace("FIRSTNAME",$firstname,$get);
			$ch2 = str_replace("USERNAME",$username,$ch1);
			$ch3 = str_replace("USERID",$fromid,$ch2);
			$ch4 = str_replace("TEXT",$text,$ch3);
			$ch5 = str_replace("HOUR",date('h'),$ch4);
			$ch6 = str_replace("MINUTE",date('i'),$ch5);
			$ch7 = str_replace("SECOND",date("s"),$ch6);
			$ch8 = str_replace("MEMBER",$data["zirmajj$fromid"],$ch7);
$ch9 = str_replace("LINK","https://t.me/$botuser?start=$fromid",$ch8);
$ch10 = str_replace("ALLMEM",$dokme["tedadzirmaj$text"],$ch9);
$ch11 = str_replace("BOTNAME",$botname,$ch10);
$ch12 = str_replace("BOTUSER",$botuser,$ch11);
$ch13 = str_replace("IDBOT",$idbot,$ch12);
			//$get = json_decode(file_get_contents("dokme/$bmm.json"),true);
					
						$xc = file_get_contents($ch13);
						sm($chatid,$xc);
}
	}elseif($step=="igetmatntaki$ch"){
	if($text=="برگشت↪"){
		step($chatid,"edit2");
		sm($chatid,"به منو برگشتید",$keydokme);
		}else{
		$get = json_decode(file_get_contents("dokme/$ch.json"),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$texx = $get['text'];
	$capp = $get['caption'];
		$ch1=str_replace("FIRSTNAME",$firstname,$capp);
			$ch2 = str_replace("USERNAME",$username,$ch1);
			$ch3 = str_replace("USERID",$fromid,$ch2);
			$ch4 = str_replace("TEXT",$text,$ch3);
			$ch5 = str_replace("HOUR",date('h'),$ch4);
			$ch6 = str_replace("MINUTE",date('i'),$ch5);
			$ch7 = str_replace("SECOND",date("s"),$ch6);
			$ch8 = str_replace("MEMBER",$data["zirmajj$fromid"],$ch7);
$ch9 = str_replace("LINK","https://t.me/$botuser?start=$fromid",$ch8);
$ch10 = str_replace("ALLMEM",$dokme["tedadzirmaj$text"],$ch9);
$ch11 = str_replace("BOTNAME",$botname,$ch10);
$ch12 = str_replace("BOTUSER",$botuser,$ch11);
$ch13 = str_replace("IDBOT",$idbot,$ch12);
			$cap=str_replace("/r/n/r","\n",$ch13);
	if($type=="text"){
			$ch1=str_replace("FIRSTNAME",$firstname,$texx);
			$ch2 = str_replace("USERNAME",$username,$ch1);
			$ch3 = str_replace("USERID",$fromid,$ch2);
			$ch4 = str_replace("TEXT",$text,$ch3);
			$ch5 = str_replace("HOUR",date('h'),$ch4);
			$ch6 = str_replace("MINUTE",date('i'),$ch5);
			$ch7 = str_replace("SECOND",date("s"),$ch6);
			$ch8 = str_replace("MEMBER",$data["zirmajj$fromid"],$ch7);
$ch9 = str_replace("LINK","https://t.me/$botuser?start=$fromid",$ch8);
$ch10 = str_replace("ALLMEM",$dokme["tedadzirmaj$text"],$ch9);
$ch11 = str_replace("BOTNAME",$botname,$ch10);
$ch12 = str_replace("BOTUSER",$botuser,$ch11);
$ch13 = str_replace("IDBOT",$idbot,$ch12);
			$texx=str_replace("/r/n/r","\n",$ch13);
	sm($chatid,$texx);
	}elseif($type=="photo"){
		sp($chatid,$texx,$cap);
		}elseif($type=="video"){
		sv($chatid,$texx,$cap);
		}elseif($type=="audio"){
		sa($chatid,$texx,$cap);
		}elseif($type=="voice"){
		svo($chatid,$texx,$cap);
		}elseif($type=="sticker"){
			ss($chatid,$texx);
			}elseif($type=="dice"){
			$gett = sdi($chatid,$texx);
			$get = $gett->result->dice->value;
			setOther($fromid,$get);
			$em= $gett->result->dice->emoji;
			setvalue("user","chatid",$chatid,"emdice",$em);
			}elseif($type=="document"){
			sd($chatid,$texx,$cap);
			}
	}
}
				elseif($step=="block"){
			if($text=="برگشت↪"){
				step($chatid,"amarbot");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyamar);
				}else{
				if(isset($text)){
					if(empty(getBlock($text))){
						insert("blocklist","`chatid`","'$text'");
step($chatid,"amarbot");
sm($chatid,"کاربر $text به لیست بلاک افزوده شد✅\n\nبه پنل برگشتید :",$keyamar);
						}else{
						$txt="
						این کاربر از قبل در لیست بلاک وجود داشت!!!🚫\n\nلطفا دوباره سعی نمایید :
						";
						sm($chatid,$txt);
					}}}
				}elseif($step=="unblock"){
			if($text=="برگشت↪"){
				step($chatid,"amarbot");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyamar);
				}else{
				if(isset($text)){
					if(!empty(getBlock($text))){
						step($chatid,"amarbot");
						deleteBlock($text);
sm($chatid,"کاربر $text از لیست حذف شد✅\n\nبه پنل برگشتید :",$keyamar);
				step($text,"");
							}else{
						$txt="
						این کاربر از قبل در لیست بلاک وجود ندارد!!!‼️\n\nلطفا دوباره سعی نمایید :
						";
						sm($chatid,$txt);
						}
					}
				}
				}elseif($step=="Resetbot"){
			if($text=="خیر🚫"){
				step($chatid,"panel");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="بله✅"){
					$sql = "DROP TABLE `dok$userbott`";
					mysqli_query($con,$sql);
					$sq2= "DROP TABLE `chan$userbott`";
					mysqli_query($con,$sql2);
					$sql3 = "DROP TABLE `data$userbott`";
					mysqli_query($con,$sql3);
					$sql4 = "DROP TABLE `blocklist$userbott`";
					mysqli_query($con,$sql4);
					$sql4 = "DROP TABLE `moh$userbott`";
					mysqli_query($con,$sql4);
					$sql4 = "DROP TABLE `channel$userbott`";
					mysqli_query($con,$sql4);
					$sql4 = "DROP TABLE `user$userbott`";
					mysqli_query($con,$sql4);
					$sql4 = "DROP TABLE `eshtrak$userbott`";
					mysqli_query($con,$sql4);
					$sql = "DROP TABLE `fileid$user`";
					mysqli_query($con,$sql);
					$sql = "DROP TABLE `datatype$user`";
					mysqli_query($con,$sql);
					$sql = "DROP TABLE `datalist$user`";
					mysqli_query($con,$sql);
					$sqlite = "CREATE TABLE `chan$userbott`
(
`user` TEXT,
`text` TEXT
)";
mysqli_query($con,$sqlite);

$sql = "CREATE TABLE `blocklist$userbott` 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `user$userbott` 
 ( 
 chatid INT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid INT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
zirmaj INT,
emtiaz INT,
`coding` INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 TEXT
)";
mysqli_query($con,$sql);
					$sqlq = "CREATE TABLE `dok$userbott`(
										`dokme` TEXT,
										`nok` TEXT,
										`into` TEXT,
										`type` TEXT,
										`editer` TEXT,
										`dastor` TEXT,
										`text` TEXT,
										`keyboard` TEXT,
										`lockchannel` TEXT,
										`lockzirmaj` TEXT,
										`lockcoin` TEXT,
										`textlock` TEXT,
										`textersal` TEXT,
										`textresid` TEXT,
										`user` TEXT,
										`tedad` INT,
										`other` TEXT,
										`other2` TEXT
										)";
										mysqli_query($con,$sqlq);
$sql = "CREATE TABLE `data$userbott`
(
`id` INT,
startmessage TEXT,
txtback TEXT,
textnewozv TEXT,
textzirmaj TEXT,
eshtebah TEXT,
startpanel TEXT,
keygroup TEXT,
newozv TEXT,
deletelink TEXT,
lockjoin TEXT,
botpower TEXT,
`sendzirmaj` TEXT,
PRIMARY KEY(id),
textpower TEXT,
`keyboard` TEXT,
`other` TEXT,
`other1` TEXT
)";
mysqli_query($con,$sql);

$start = "سلام به ربات من خوش آمدید❤";
$textback = "به عقب برگشتید";
$Eshtebah ="این دستور وجود ندارد!!!";
$newozv = "سلام به گروه خوش آمدید❤";
$adminstart ="سلام مدیر به قسمت مدیریت ربات خوش آمدید🌹\n\n⭐️شما میتوانید از طریق دکمه های زیر ربات خود را مدیریت کنید .\n\n⭐️اگر در روند ویرایش ربات مشکلی داشتید میتوانید به کانال ما مراجعه کنید .\n\n⭐️درصورت تست ربات بصورت کاربر از دستور /start یا از دکمه خروج از پنل استفاده کنید.\n\n⭐️کلیپ های آموزشی در کانال موجود میباشد\n\n ";
$textpower ="ربات خاموش میباشد🛂";
$textzirmaj = "یک زیرمجموعه به شما اضافه شد💥";
$sql = "INSERT INTO `data$userbott`
(
`id`,
`startmessage`,
`txtback`,
`textnewozv`,
`eshtebah`,
`startpanel`,
`keygroup`,
`newozv`,
`deletelink`,
`lockjoin`,
`botpower`,
`textpower`,
`textzirmaj`,
`sendzirmaj`
)
VALUES ('1','$start','$textback','$newozv','$Eshtebah','$adminstart','off','on','off','off','off','$textpower','$textzirmaj','off')";
mysqli_query($con,$sql);
if(empty(getKeyboard())){
	$keytest='[{"text":""}],[{"text":""}]';
	setKeyboard($keytest);
	}
$keyboard=getKeyboard();
$keykarbar='{"keyboard":['.$keyboard.',[{"text":"ورود به پنل🔧"}]],"resize_keyboard":'.$sizekol.'}';
$txt="ربات با موفقیت به حالت اولیه برگشت و تمام اطلاعات پاک شدند✅\n\nبه پنل برگشتید :";
					step($chatid,"");
					sm($chatid,$txt,$keykarbar);
					}
			
				}elseif($step=="edit"){
			if($text=="برگشت به عقب↪"){
				step($chatid,"panel");
					sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}else{
				if(!empty(getDokme($text))){
					if(getDokmenok($text)=="newdokme"){
						step($chatid,"edit3pp");
						//$dokme['editer'] = $text;
						setOther2($chatid,$text);
						$txt="
						آیا میخواهید ویرایش بر روی همین دکمه اعمال شود یا بر دکمه های دیگر؟!\n\nبرای لغو عملیات برگشت را بزنید :
						";
						sm($chatid,$txt,$keyjadid);
						}else{
					step($chatid,"edit2");
					setOther2($chatid,$text);
					$nol = getDokmenok($text);
					if($nol=="newdokme"){
						$no = "دکمه ی دیگر";
						}elseif($nol=="rss"){
							$no="Rss خوان";
							}elseif($nol=="getApi"){
							$no ="گرفتن پیام و نمایش Api";	
								}elseif($nol=="Api"){
							$no ="استفاده Api";	
								}elseif($nol=="matntaki"){
									$no ="متن تکی";
									}elseif($nol=="getmatntaki"){
									$no ="گرفتن پیام و نمایش متن تکی";
									}elseif($nol=="matnchand"){
										$no="متن چندتایی";
										}elseif($nol=="matnrand"){
											$no="متن رندوم";
											}elseif($nol=="sendadmin"){
											$no="گرفتن پیام و ارسال به ادمین";
											}elseif($nol=="jostojo"){
											$no="دکمه ی جست و جو";
											}elseif($nol=="bartarin"){
											$no="برترین های زیرمجموعه گیری";
											}elseif($nol=="coin"){
											$no="برترین های امتیازگیری";
											}elseif($nol=="back"){
											$no="دکمه ی برگشت";
											}elseif($nol=="schannel"){
											$no="گرفتن پیام و ارسال به کانال";
											}elseif($nol=="fchannel"){
											$no="گرفتن پیام و فوروارد به کانال";
											}elseif($nol=="change"){
											$no="انتقال امتیاز";
											}elseif($nol=="enfjar"){
											$no="انفجار";
											}elseif($nol=="botcreator"){
											$no="دکمه ی رباتساز";
											}elseif($nol=="php"){
											$no="استفاده از php";
											}elseif($nol=="search"){
											$no="جست و جو کانال";
											}elseif($nol=="matntartib"){
												$no="متن به ترتیب";
												}elseif($nol=="createbot"){
											$no="دکمه ی ساخت ربات";
											}elseif($nol=="updatebot"){
											$no="دکمه ی اپدیت ربات";
											}elseif($nol=="deletebot"){
												$no="دکمه ی حذف ربات";
												}
											$dastor = getDastor($text);
											if(!isset($dastor)){
												$dastor = "ندارد";
												}
											$intoo = getInto($text);
											if(!isset($intoo)){
												$intoo="ندارد";
												}
												if(getvalue("dok","dokme",$text,"hidden")=="on"){
													$nam = "مخفی";
													}else{
														$nam="نمایان";
														}
														if(empty(getvalue("dok","dokme",$text,"amardok"))){
															$amar = "هیچکس🅾";
															}else{
																$amar ="<b>".getvalue("dok","dokme",$text,"amardok")."</b> کلیک";
																}
																
											if($nol=="sendadmin" || $nol=="jostojo"){
							sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keysendadmin);
		
							}elseif($nol=="back"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keysback);
}elseif($nol=="change" or $nol== "enfjar" or $nol=="createbot" or $nol=="deletebot" or $nol=="updatebot"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keychange);
}elseif($nol=="schannel"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keyschannel);
}elseif($nol=="fchannel"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keyfchannel);
}elseif($nol=="search"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keysearch);
}elseif($nol=="matntartib"){
sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keytartib);
}else{
					sm($chatid,"💠اسم دکمه : $text\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keydokme);
					
					}
if($nol=="createbot"){
$txt="⚠️توجه کنید در این قسمت نیاز هست تا شما در ربات $channel_name_fa سکه برای ساخت ربات داشته باشید!!!

ساخت ربات توسط کاربر به همان اندازه ، از سکه های شما در $channel_name_fa کسر میشود

اگر موجودی شما به اتمام برسد
برای کاربر متن ساخت ربات غیرفعال شد نمایش داده میشود که درصورت نیاز میتوانید متن را از ویرایش دکمه تغییر دهید";
sm($chatid,$txt);
}

}
						}else{
					sm($chatid,"این دستور موجود نیست⛔\n\nاز دستورات پنل استفاده کنید :");

					}
				}
			//		$keo = '{"keyboard":"[]"}';
			}elseif($step=="edit3pp"){
					$vb = getOther2($chatid);
				if($text=="برگشت↪"){
					step($chatid,"edit");
								$txt="برای ویرایش دکمه ها از دکمه های زیر انتخاب کنید :";
								$keyboard=getKeyboard();
			$keo='{"keyboard":['.$keyboard.',[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
	
								sm($chatid,$txt,$keo);
					}elseif($text=="بله ،روی همین✅"){
						step($chatid,"edit3");
					//	$dokme['editer'] = $text;
					
					$nol = getDokmenok($chatid);
					if($nol=="newdokme"){
						$no = "دکمه ی دیگر";
						}elseif($nol=="rss"){
							$no="Rss خوان";
							}elseif($nol=="Api"){
							$no ="استفاده Api";	
								}elseif($nol=="getApi"){
							$no ="گرفتن پیام و نمایش Api";	
								}elseif($nol=="matntaki"){
									$no ="متن تکی";
									}elseif($nol=="getmatntaki"){
									$no ="گرفتن پیام و نمایش متن تکی";
									}elseif($nol=="matnchand"){
										$no="متن چندتایی";
										}elseif($nol=="matnrand"){
											$no="متن رندوم";
											}elseif($nol=="php"){
											$no="استفاده از php";
											}elseif($nol=="matntartib"){
												$no="متن به ترتیب";
												}
											$dastor = getDastor($text);
											if(!isset($dastor)){
												$dastor = "ندارد";
												}
											$intoo = getInto($text);
											if(!isset($intoo)){
												$intoo="ندارد";
												}
												if(getvalue("dok","dokme",$text,"hidden")=="on"){
													$nam = "مخفی";
													}else{
														$nam="نمایان";
														}
											if(empty(getvalue("dok","dokme",$text,"amardok"))){
															$amar = "هیچکس🅾";
															}else{
																$amar ="<b>".getvalue("dok","dokme",$text,"amardok")."</b> کلیک";
																}
					//	file_put_contents("dokme.json", json_encode($dokme, 128|256));
					sm($chatid,"💠اسم دکمه : $vb\n\n💠نوع دکمه : $no\n\n💠دستور دکمه : $dastor\n\n💠دکمه ی قبلی : $intoo\n\n💠وضعیت نمایش : $nam\n\n💠تعداد کلیک : $amar\n\nچه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keydokme2);
					
					//	sm($chatid,"چه تغییراتی میخواهید بر روی دکمه انجام دهید ؟!",$keydokme2);
						}elseif($text=="خیر،روی بقیه❎"){
							step($chatid,"edit");
						$keyb=getDokmetext($vb);
			$keo='{"keyboard":['.$keyb.',[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
				$txt="برای ویرایش دکمه ها از دکمه های زیر انتخاب کنید :";
	sm($chatid,"$txt",$keo);
							}
				}elseif($step=="edit3"){
					$vb = getOther2($chatid);
				if($text=="برگشت↪"){
					step($chatid,"edit");
								$txt="برای ویرایش دکمه ها از دکمه های زیر انتخاب کنید :";
								$keyboard=getKeyboard();
			$keo='{"keyboard":['.$keyboard.',[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
	
								sm($chatid,$txt,$keo);
					}elseif($text=="تغییر نام🔁"){
						step($chatid,"changename");
						$txt ="لطفا اسم جدید را وارد کنید تا اسم دکمه تغییر یابد : \n\nبرای لغو عملیات برگشت را بزنید:";
						sm($chatid,$txt,$keyback);
						}elseif($text=="لینک دکمه📥"){
							$vb = getOther2($chatid);
$md =md5($vb);
							insert("hash","`hash`,`text`","'$md','$vb'");
//$name=base64_encode($vb);
$txt="https://t.me/$userbott?start=BUTTON$md";
sm($chatid,$txt);
}elseif($text=="حذف دستور🚮"){
							$vb = getOther2($chatid);
							$dastor = getDastor($vb);
											if(isset($dastor)){
												step($chatid,"noyesdastor");
												sm($chatid,"دستور شما : $dastor\n\nآیا از حذف این دستور اطمینان دارید ؟!!",$keynoyes);
												}	else{
								sm($chatid,"شما هنوز دستوری برای این دکمه نساخته اید🚫\n\n⭐با استفاده از دکمه ی تعیین دستور اقدام به ساخت دستور کنید :");				
											}
						
						}elseif($text=="تعیین دستور⚡"){
						step($chatid,"TaiinDastor");
					$txt="لطفا دستور خود را با رعایت نکات زیر بفرستید

⭐️دستور شما باید با / (slash)  شروع شود .

⭐️دستور شما نباید قبلا به کار رفته باشد.

⭐️دستور شما نباید در اسم دکمه ها به کار رفته باشد.

برای لغو عملیات برگشت را بزنید↪️";
										sm($chatid,$txt,$keyback);
						}elseif($text=="حذف دکمه🚮"){
							$txt="⛔آیا برای حذف کامل دکمه مطمعن هستید!!\n\nبا این کار تمام محتوای دکمه هم پاک خواهد شد!؟";
							step($chatid,"hazfdokme");
							sm($chatid,$txt,$keynoyes);
							}elseif($text=="مخفی کردن دکمه🔕"){
							setvalue("dok","dokme",$vb,"hidden","on");
							sm($chatid,"دکمه از این به بعد برای کاربران غیرقابل نمایش هست🔕");
							}elseif($text=="نمایش دکمه🔔"){
							setvalue("dok","dokme",$vb,"hidden","off");
							sm($chatid,"دکمه برای کاربران نمایان شد🔔");
							}elseif($text=="انتقال دکمه♻"){
step($chatid,"entqaldokme");
$keyboard=getKeyboard();
$keo='{"keyboard":['.$keyboard.',[{"text":"لغو عملیات❎"}]],"resize_keyboard":true}';
sm($chatid,"لطفا مقصد دکمه ی مورد نظر را انتخاب نمایید,تا دکمه انتقال پیدا کند :",$keo);
}

elseif($text=="قفل دکمه🔒"){
								step($chatid,"qofldokme");
								$txt="لطفا نوع قفل رو از کیبورد زیر انتخاب کنید : \n\nبرای لغو عملیات برگشت را بزنید : ";
								sm($chatid,$txt,$keyqofl);
								}elseif($text=="حذف قفل دکمه🔓"){
								step($chatid,"hazfqofl");	
								$txt = "آیا از حذف قفل دکمه مطمعن هستید ؟!!";
								sm($chatid,$txt,$keynoyes);
									}elseif($text=="تغییر متن فیلد🔤"){
										step($chatid,"changefieldtext");
$txt="شما در این قسمت میتوانید متن فیلد که عنوان <b> Message </b> تایپ شده را به متن دلخواه خود تغییر دهید .\n\n✏لطفا متن جدید خود را بفرستید :\n\n⚠متن شما باید بین 1 تا 64 حرف باشد!!!";
sm($chatid,$txt,$keyback);
ToDie();
}elseif($text=="تعیین دکمه جدید🆕"){
								step($chatid,"create");
								setEditer($vb,"newdokme");
								$keyb = getDokmetext($vb);
			$keo= '{"keyboard":[[{"text":"افزودن دکمه🔼"}],'.$keyb.',[{"text":"افزودن دکمه🔽"}],[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';

					$txt="
						🔩برای ساخت دکمه یکی از گزینه های زیر را انتخاب کنید :

★اگر روی اسم دکمه های از قبل ساخته شده کلیک کنید ، دکمه در سمت راست آن ساخته خواهد شد★

★از اسم تکراری نمیتوانید استفاده کنید★
						";
	sm($chatid,"$txt",$keo);
								}elseif($text=="تغییر متن ارسال✏️"){
									step($chatid,"textersal");
									$txt="
									در این قسمت شما میتوانید متنی که برای کاربر موقع زدن دکمه ی ساخته شده برای دیدن دکمه های دیگر نمایش داده می شود را تغییر دهید
★از کلمات جایگزین زیر هم میتوانید استفاده نمایید
★همچنین از کدهای Html هم میتوانید در متن استفاده کنید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
									";
									sm($chatid,$txt,$keyback);
									}
					}
				elseif($step=="edit2"){
				$vb = getOther2($chatid);
				if($text=="برگشت↪"){
					step($chatid,"edit");
								$txt="برای ویرایش دکمه ها از دکمه های زیر انتخاب کنید :";
								$keyboard=getKeyboard();
			$keo='{"keyboard":['.$keyboard.',[{"text":"برگشت به عقب↪"}]],"resize_keyboard":true}';
	
								sm($chatid,$txt,$keo);
					}
				if($text=="تغییر نام🔁"){
						step($chatid,"changename");
						$txt ="لطفا اسم جدید را وارد کنید تا اسم دکمه تغییر یابد : \n\nبرای لغو عملیات برگشت را بزنید:";
						sm($chatid,$txt,$keyback);
						}elseif($text=="تغییر متن ها✏"){
						$txt ="یک گزینه را انتخاب کنید";
						$vb = getOther2($chatid);
						$cm = getDokmenok($vb);
							if($cm=="createbot"){
								step($chatid,"changepmhacreate");
						sm($chatid,$txt,$keycreatebot2);
						}elseif($cm=="deletebot"){
								step($chatid,"changepmhadelete");
						sm($chatid,$txt,$keydeletebot2);
						}elseif($cm=="updatebot"){
								step($chatid,"changepmhaupdate");
						sm($chatid,$txt,$keyupdatebot2);
						}elseif($cm=="enfjar"){
								step($chatid,"changepmhaenfjar");
						sm($chatid,$txt,$keyenfjar2);
						}else{
								step($chatid,"changepmha");
						sm($chatid,$txt,$keychange2);
						}
						}elseif($text=="تغییر نام دکمه پست بعدی⏩"){
step($chatid,"changedokmetartib");
sm($chatid,"لطفا اسم دکمه رو وارد کنید ، تا تغییرات ایجاد شود : ",$keyback);
}elseif($text=="مخفی کردن دکمه🔕"){
							setvalue("dok","dokme",$vb,"hidden","on");
							sm($chatid,"دکمه از این به بعد برای کاربران غیرقابل نمایش هست🔕");
							}elseif($text=="🔖تعیین امضا"){
								step($chatid,"addemza");
								sm($chatid,"یک گزینه را انتخاب کنید :",$keyemza);
							}elseif($text=="قفل فوروارد↩"){
								step($chatid,"qoflforward");
								sm($chatid,"یک گزینه را انتخاب کنید :",$keyqoflforward);
							}elseif($text=="نمایش دکمه🔔"){
							setvalue("dok","dokme",$vb,"hidden","off");
							sm($chatid,"دکمه برای کاربران نمایان شد🔔");
							}elseif($text=="تعیین ایدی کانال🆔"){
						step($chatid,"taiinidchannel");
						$txt="💢لطفا ابتدا ربات را در کانال خود ادمین نمایید💢

🔰سپس ایدی یا یوزرنیم کانال خود را بفرستید ، تا محتوای کاربر در آن کانال فوروارد یا ارسال شود 

❗️اگر کانال خصوصی میباشد ایدی عددی آن را بفرستید 
🆔 مثال :-1002135620077 

❗️اگر کانال عمومی میباشد یوزرنیم آن را برای ما ارسال کنید 
🆔مثال : @$channel_name_en";
						sm($chatid,$txt,$keyback);
						}elseif($text=="تنظیم نوع نمایش 👀"){
step($chatid,"nonamayesh");
$txt="💠لطفا انتخاب کنید درصورت سرچ موفق در کانال ، محتوا ها به چه صورت برای کاربر نمایش داده شوند ؟!!

نمایش بصورت فوروارد🔁
➕محتوا را از کانال فوروارد و برای کاربر نمایش می دهد
⚠️نیاز هست تا ربات در کانال مورد نظر ادمین باشد!!

نمایش بصورت پیام📄
➕محتوا را از کانال بصورت ارسالی برای کاربر نمایش داده میشود(‌این گزینه دارای محدودیت هایی از سوی تلگرام هست‌‌)
⚠️نیاز هست تا ربات در کانال مورد نظر ادمین باشد!!

نمایش بصورت لینک پست🌐
➕لینک پست را برای کاربر ارسال میکند";
sm($chatid,$txt,$keynamayesh);
}elseif($text=="تنظیم تعداد سرچ🔢"){
step($chatid,"tanzimtedadsearch");
$txt="💎لطفا تعیین کنید که حداکثر چه تعداد عبارت سرچ شده برای کاربر نمایش داده شود؟!!!


⚠️شما میتوانید حداقل <b>1</b> و حداکثر <b>20</b> عدد را وارد کنید";
sm($chatid,$txt,$keyback);
}elseif($text=="تغییر متن سرچ ناموفق🔎"){
step($chatid,"taiinsearchnamo");
$txt="⭐️لطفا متنی که برای کاربر برای سرچ ناموفق نمایش داده میشود را بفرستید:


⭐️متن شما میتواند شامل تگ های HTML باشد

⭐️متن شما میتواند شامل دکمه ی شیشه ای باشد

⭐️شما میتوانید از پیشفرض های ربات هم استفاده نمایید

 ";
sm($chatid,$txt,$keyback);
}elseif($text=="تنظیم ایدی کانال🆔"){
						step($chatid,"taiinidchannel2");
						$txt="💎لطفا یوزرنیم کانال را به همراه @ ارسال کنید :

⚠️توجه کنید کانال باید حتما پابلیک public باشد

⚠️برای نمایش بصورت فوروارد و یا ارسال پیام ، ربات باید در کانال ادمین باشد.";
						sm($chatid,$txt,$keyback);
						}elseif($text=="انتقال دکمه♻"){
step($chatid,"entqaldokme");
$keyboard=getKeyboard();
$keo='{"keyboard":['.$keyboard.',[{"text":"لغو عملیات❎"}]],"resize_keyboard":true}';
sm($chatid,"لطفا مقصد دکمه ی مورد نظر را انتخاب نمایید,تا دکمه انتقال پیدا کند :",$keo);
}elseif($text=="تعیین متن کانال📩"){
						step($chatid,"pmschannel");
						$txt="⭐️لطفا متنی که در کانال تعیین شده ی شما نمایش داده میشود را بفرستید تا ثبت شود

★شما میتوانید از دستور /empty برای پیشفرض کردن متن کانال استفاده کنید

پیشفرض بودن متن میتواند شامل فیلم و عکس و فایل و غیره باشد ، اما برای متن شما تنها فقط قابلیت ارسال متن میباشد

★شما میتوانید از Html  هم استفاده نمایید

★شما میتوانیید از شیشه ای استفاده نمایید

★شما میتوانید از پیشفرض های ربات استفاده نمایید :

TEXT - اخرین متن کاربر";
						sm($chatid,$txt,$keyback);
						}elseif($text=="لینک دکمه📥"){
							$vb = getOther2($chatid);
$md =md5($vb);
							insert("hash","`hash`,`text`","'$md','$vb'");
//$name=base64_encode($vb);
$txt="https://t.me/$userbott?start=BUTTON$md";
sm($chatid,$txt);
}elseif($text=="تعیین دستور⚡"){
						step($chatid,"TaiinDastor");
					$txt="لطفا دستور خود را با رعایت نکات زیر بفرستید

⭐️دستور شما باید با / (slash)  شروع شود .

⭐️دستور شما نباید قبلا به کار رفته باشد.

⭐️دستور شما نباید در اسم دکمه ها به کار رفته باشد.

برای لغو عملیات برگشت را بزنید↪️";
										sm($chatid,$txt,$keyback);
						}elseif($text=="حذف دستور🚮"){
							$vb = getOther2($chatid);
							$dastor = getDastor($vb);
											if(!empty($dastor)){
												step($chatid,"noyesdastor");
												sm($chatid,"دستور شما : $dastor\n\nآیا از حذف این دستور اطمینان دارید ؟!!",$keynoyes);
												}	else{
								sm($chatid,"شما هنوز دستوری برای این دکمه نساخته اید‼️\n\n⭐با استفاده از دکمه ی تعیین دستور اقدام به ساخت دستور کنید :");				
											}
						
						}elseif($text=="تعیین متن رسید📨"){
							$vb = getOther2($chatid);
							$ccm = getDokmenok($vb);
							if($ccm=="sendadmin"){
							step($chatid,"matnresidadmin");
										$txt="
										شما میتوانید متنی که موقع ارسال پیام به ادمین برای کاربر نمایش داده میشود را تغییر دهید.

★تنها متن مجاز هست!!
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
								}elseif($ccm=="schannel" || $ccm=="fchannel"){
							step($chatid,"matnresidadmin");
										$txt="
										شما میتوانید متنی که موقع ارسال پیام به کانال برای کاربر نمایش داده میشود را تغییر دهید.

★تنها متن مجاز هست!!
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
								}elseif($ccm=="jostojo"){
							step($chatid,"matnresidadmin");
										$txt="شما میتوانید متنی که موقع دریافت متن موقع جست و جو دکمه دریافت میکنید را تغییر دهید
										
										
★تنها متن مجاز هست!!
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
								}
									}elseif($text=="تغییر متن ارسال✏️"){
										$vb = getOther2($chatid);
							$ccm = getDokmenok($vb);
							if($ccm=="getphp" || $ccm=="getApi" || $ccm =="getmatntaki" || $ccm =="sendadmin" || $ccm=="jostojo" || $ccm=="schannel" || $ccm=="fchannel" || $ccm=="search" || $ccm == "createbot" || $ccm == "deletebot" || $ccm =="updatebot" || $ccm=="enfjar" || $ccm=="change"){
							step($chatid,"matnersalget");
										$txt="
شما میتوانید متنی که برای کاربر موقع زدن دکمه ای که برای دریافت پیام کاربر یا نمایش دکمه جدید هست را تغییر دهید


★تنها متن مجاز هست!!
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
								}else{
								sm($chatid,"این دکمه تنها برای قسمت دریافت پیام از کاربر و دکمه ی جدید مجاز هست🚫");
								}
									}elseif($text=="تغییر متن بازگشت✏️"){
										$vb = getOther2($chatid);
							$ccm = getDokmenok($vb);
							if($ccm=="back"){
							step($chatid,"matnersalget");
										$txt="
شما میتوانید متنی که برای کاربر موقع زدن رویه دکمه ی بازگشت نمایش داده میشود را تغییر دهید


★تنها متن مجاز هست!!
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
								}else{
								sm($chatid,"⛔این دکمه فقط برای دکمه سیستمی بازگشت میباشد⛔");
								}
									}elseif($text=="تعیین موقعیت برگشت🔙"){
$arra = getallvalue("dok","dokme");
			$keytest='[{"text":""}],[{"text":""}]';
			foreach($arra as $sd){
				if(getDokmenok($sd)=="newdokme"){
					$keytest = str_replace(',[{"text":""}]',',[{"text":"'.$sd.'"}],[{"text":""}]',$keytest);
					}
				}
				$back="برگشت↪";
									
				$keykarbar='{"keyboard":['.$keytest.',[{"text":"منوی اصلی"}],[{"text":"'.$back.'"}]],"resize_keyboard":true}';
			step($chatid,"nokback");
			$txt="لطفا تعیین کنید که کاربر بعد از زدن رویه دکمه ، کدام یک از دکمه های زیر نمایش داده شود?!!";
			sm($chatid,$txt,$keykarbar);
}elseif($text=="حذف دکمه🚮"){
							$txt="⛔آیا برای حذف کامل دکمه مطمعن هستید!!\n\nبا این کار تمام محتوای دکمه هم پاک خواهد شد!؟";
							step($chatid,"hazfdokme");
							sm($chatid,$txt,$keynoyes);
							}elseif($text=="قفل دکمه🔒"){
								step($chatid,"qofldokme");
								$txt="لطفا نوع قفل رو از کیبورد زیر انتخاب کنید : \n\nبرای لغو عملیات برگشت را بزنید : ";
								sm($chatid,$txt,$keyqofl);
								}elseif($text=="حذف قفل دکمه🔓"){
								step($chatid,"hazfqofl");	
								$txt = "آیا از حذف قفل دکمه مطمعن هستید ؟!!";
								sm($chatid,$txt,$keynoyes);
									}elseif($text=="حذف خودکار محتوا🗑"){
										sm($chatid,"لطفا یک گزینه را انتخاب کنید :",$keyautodel);
										step($chatid,"autodel");
										}elseif($text=="تغییر قالب متن🀄"){
											$get = getDokmenok($vb);
											if($get=="coin" or $get=="bartarin"){
												step($chatid,"themebartarin");
												$txt="
لطفا قالب نمایش برترین ها رو طبق مثال زیر بفرستید :
T-RANGE => T-FIRSTNAME or T-USERID => T-COIN or T-MEMBER
خروجی :
1 => Ali =>10 coin
2 => 137724724724 => 2 member
از متغیر های زیر می توانید در قالب استفاده نمایید
T-RANGE -> رتبه
T-USERID -> ایدی عددی فرد
T-FIRSTNAME -> نام فرد
T-LASTNAME -> فامیلی فرد
T-USERNAME -> یوزرنیم فرد
T-COIN -> تعداد سکه های فرد
T-MEMBER -> تعداد زیرمجموعه های فرد
";
												sm($chatid,$txt,$keyback);
													}else{
														sm($chatid,"این قسمت در حال حاضر فقط برای دکمه های سیستمی نمایش برترین های زیرمجموعه گیری و امتیاز گیری کاربرد دارد!!!");

}
}elseif($text=="دریافت مطالب🛃"){
											//hellomy
											$get = getDokmenok($vb);
											if($get=="coin" or $get=="bartarin"){
												if(getDokmetext($vb)==null or getDokmetext($vb)=="[]"){
													sm($chatid,"هنوز هیچ مطلبی برای این دکمه ذخیره نشده است⛔");
													}else{
												sm($chatid,getDokmetext($vb));
												}
												}elseif($get=="php" or $get=="getphp"){
												if(getDokmetext($vb)==null or getDokmetext($vb)=="[]"){
													sm($chatid,"هنوز هیچ مطلبی برای این دکمه ذخیره نشده است⛔");
													}else{
												sm($chatid,getDokmetext($vb));
												}
												}elseif($get=="matntaki" or $get=="getmatntaki"){
												if(getDokmetext($vb)==null or getDokmetext($vb)=="[]"){
													sm($chatid,"هنوز هیچ مطلبی برای این دکمه ذخیره نشده است⛔");
													}else{
												$get = json_decode(getDokmetext($vb),true);
//	$type = $get['type'];
	//$text = $get['text'];
	$type = $get['type'];
	$tet = $get['text'];
	$capp = $get['caption'];
	
$cap=str_replace("/r/n/r","\n",$capp);
	if($type=="text"){
$ch13=str_replace("/r/n/r","\n",$tet);
	sm($chatid,$ch13);
	}elseif($type=="photo"){
	sp($chatid,$tet,$cap);
		}elseif($type=="video"){
		 sv($chatid,$tet,$cap);
		}elseif($type=="audio"){
		 sa($chatid,$tet,$cap);
		}elseif($type=="voice"){
		 svo($chatid,$tet,$cap);
		}elseif($type=="sticker"){
			 ss($chatid,$tet);
			}elseif($type=="contact"){
		 sco($chatid,$tet,$cap);
		}elseif($type=="video_note"){
		svin($chatid,$tet);
		}elseif($type=="location"){
		slo($chatid,$tet,$cap);
			}elseif($type=="dice"){
			sdi($chatid,$tet);
			}elseif($type=="document"){
			sd($chatid,$tet,$cap);
			}
												}
												}elseif($get=="matnchand" || $get=="matntartib" ||$get=="matnrand"){
												if(getDokmetext($vb)==null or getDokmetext($vb)=="[]"){
													sm($chatid,"هنوز هیچ مطلبی برای این دکمه ذخیره نشده است⛔");
													}else{
												$get = json_decode(getDokmetext($vb),true);
$x = 0;
foreach ($get as $nok){
	if($x==10){
	sleep(1);
$x=0;	
	}
	$x++;
$type = $nok['type'];
	$tet = $nok['text'];
	$capp = $nok['caption'];
		$capp=str_replace("/r/n/r","\n",$capp);
	if($type=="text"){
				$ch13=str_replace("/r/n/r","\n",$tet);
	sm($chatid,$ch13);
	}elseif($type=="photo"){
		sp($chatid,$tet,$capp);
		}elseif($type=="video"){
		sv($chatid,$tet,$capp);
		}elseif($type=="audio"){
		sa($chatid,$tet,$capp);
		}elseif($type=="voice"){
	svo($chatid,$tet,$capp);
		}elseif($type=="sticker"){
		 ss($chatid,$tet);
		}elseif($type=="contact"){
		sco($chatid,$tet,$capp);
		}elseif($type=="video_note"){
		svin($chatid,$tet);
		}elseif($type=="location"){
			slo($chatid,$tet,$capp);
			}elseif($type=="dice"){
			sdi($chatid,$tet);
			}elseif($type=="document"){
			sd($chatid,$tet,$capp);
			}
			}
			}
												}elseif($get=="Api" or $get=="rss" or $get=="getApi"){
												if(getDokmetext($vb)==null or getDokmetext($vb)=="[]"){
													sm($chatid,"هنوز هیچ مطلبی برای این دکمه ذخیره نشده است⛔");
													}else{
														$gget = json_decode(getDokmetext($vb),true);
				$get=$gget['text'];
												sm($chatid,$get);
												}
												}
												}
						elseif($text=="تعیین مطلب🆕"){
							
						if(empty(getDokmetext($vb))){
					setDokmetext($vb,"[]");
}
							$geet = getDokmetext($vb);
						$get =getDokmenok($vb);
						if($get=="coin"){
							step($chatid,"taiincoin");
						$txt="لطفا متن خود را بفرستید :\n\n<b>توجه!</b> : حتما از BC در متن خود استفاده نمایید :\nBC- برترین کاربران امتیازگیری";
						sm($chatid,$txt,$keyback);
							
							}elseif($get=="bartarin"){
							step($chatid,"taiincoin");
						$txt="لطفا متن خود را بفرستید :\n\n<b>توجه!</b> : حتما از BC در متن خود استفاده نمایید :\nBC- برترین کاربران زیرمجموعه گیری";
						sm($chatid,$txt,$keyback);
							
							}elseif($get=="php"){
							step($chatid,"taiinphp");
							$txt='⭐️لطفا کد php خود را بفرستید

⚠️توجه داشته باشید ، کد شما نباید بیشتر از 4096 خط باشد ، یعنی کدشما باید در یک صفحه پیام تلگرام باشد


⚠️برای استفاده از متغیر ها شما میتوانید در ابتدای متغییر های قبلی از علامت $ استفاده کنید
به عنوان مثال اگر متغییر اسم کاربر مساوی با FIRSTNAME باشد ، شما میتوانید در کد های php به شکل $FIRSTNAME استفاده کنید


⚠️همچنین خروجی دکمه مساوی با رشته ای هست که در کد php شما خروجی داده باشد ، به عنوان مثال


echo "Hi";


در اینجا خروجی عبارت Hi هست و برای کاربر Hi نمایش داده میشود.
';
						sm($chatid,$txt,$keyback);
							
							}elseif($get=="getphp"){
							step($chatid,"taiinphp");
							$txt='⭐️لطفا کد php خود را بفرستید

⚠️توجه داشته باشید ، کد شما نباید بیشتر از 4096 خط باشد ، یعنی کدشما باید در یک صفحه پیام تلگرام باشد


⚠️برای استفاده از متغیر ها شما میتوانید در ابتدای متغییر های قبلی از علامت $ استفاده کنید
به عنوان مثال اگر متغییر اسم کاربر مساوی با FIRSTNAME باشد ، شما میتوانید در کد های php به شکل $FIRSTNAME استفاده کنید


⚠️همچنین خروجی دکمه مساوی با رشته ای هست که در کد php شما خروجی داده باشد ، به عنوان مثال


echo "Hi";


در اینجا خروجی عبارت Hi هست و برای کاربر Hi نمایش داده میشود.
';
						sm($chatid,$txt,$keyback);
							
							}
						elseif($get=="getmatntaki"){
							step($chatid,"taiintaki");
						$txt="
						لطفا محتوای خود را بفرستید تا در دکمه ثبت شود :

★محتوای شما میتواند شامل متن ، عکس ، ویدیو ‌، موزیک ، ویس ، استیکر ، فایل ، گیف باشد
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
							
							}
					elseif($get=="matntaki"){
						step($chatid,"taiintaki");
						$txt="
						لطفا محتوای خود را بفرستید تا در دکمه ثبت شود :

★محتوای شما میتواند شامل متن ، عکس ، ویدیو ‌، موزیک ، ویس ، استیکر ، فایل ، گیف باشد
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
						";
						sm($chatid,$txt,$keyback);
						}elseif($get=="matnchand" || $get=="matntartib"){
							if(empty($geet) or $geet == '[]' or $geet ==" " or $geet ==null){
								step($chatid,"taiinchand1");
							$txt="
							لطفا محتوای خود را بفرستید تا در دکمه ثبت شود :
							
							★حداکثر 50 محتوا میتوانید ارسال کنید

★محتوای شما میتواند شامل متن ، عکس ، ویدیو ‌، موزیک ، ویس ، استیکر ، فایل ، گیف باشد
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
							";
							sm($chatid,$txt,$keyback);
							}else{
							$get =json_decode(getDokmetext($vb));
							$count = count($get);
							if($count >= 50){
								step($chatid,"noyes");
								$txt="این دکمه به حداکثر تعداد ثبت خود رسیده !!\n\nآیا میخواهید این پست ها پاک شود و محتویات جدید افزوده شود؟!!";
								sm($chatid,$txt,$keynoyes);
								}else{
									step($chatid,"noyes2");
							$men = 50 - $count;
							//$get =json_encode(file_get_contents("dokme/$vb.json"));
						
							$txt="شما میتوانید $men تا پست دیگر ارسال کنید\nآیا محتویات جدید روی پست های قبل اعمال شود؟!\n";
							sm($chatid,$txt,$keynoyes2);
							}
							}
							}elseif($get=="matnrand"){
							if(empty($geet) or $geet == '[]' or $geet ==" " or $geet ==null){
								step($chatid,"taiinrand1");
							$txt="
							لطفا محتوای خود را بفرستید تا در دکمه ثبت شود :
							
							★حداکثر 50 محتوا میتوانید ارسال کنید

★محتوای شما میتواند شامل متن ، عکس ، ویدیو ‌، موزیک ، ویس ، استیکر ، فایل ، گیف باشد
★همچنین میتوانید از کلمات جایگزین زیر هم استفاده نمایید 
★همچنین میتوانید از کد های Html هم در متن استفاده نمایید
FIRSTNAME نام کاربر
USERNAME یوزرنیم کاربر
USERID ایدی عددی کاربر
TEXT اخرین کلمه کاربر
HOUR ساعت
MINUTE دقیقه
SECOND ثانیه
							";
							sm($chatid,$txt,$keyback);
							}else{
							$get = getDokmetext($vb);
							$count = count($get);
							if($count >= 50){
								step($chatid,"noyesrand");
								$txt="این دکمه به حداکثر تعداد ثبت خود رسیده !!\n\nآیا میخواهید این پست ها پاک شود و محتویات جدید افزوده شود؟!!";
								sm($chatid,$txt,$keynoyes);
								}else{
									step($chatid,"noyes2rand");
							$men = 50 - $count;
							//$get =json_encode(file_get_contents("dokme/$vb.json"));
						
							$txt="شما میتوانید $men تا پست دیگر ارسال کنید\nآیا محتویات جدید روی پست های قبل اعمال شود؟!\n";
							sm($chatid,$txt,$keynoyes2);
							}
							}
							}elseif($get=="Api"){
						step($chatid,"taiinApi");
						$txt="
						لطفا آدرس Api خود را وارد کنید :\n\n★دقت داشته باشید خروجی URL شما نباید بصورت Json یا Array باشد \n\n★خروجی باید بصورت متن باشد و نباید بصورت فایل یا عکس و ... باشد
						";
						sm($chatid,$txt,$keyback);
								}elseif($get=="getApi"){
						step($chatid,"taiinApi");
						$txt="
						لطفا آدرس Api خود را وارد کنید :\n\n★دقت داشته باشید خروجی URL شما نباید بصورت Json یا Array باشد \n\n★خروجی باید بصورت متن باشد و نباید بصورت فایل یا عکس و ... باشد
						";
						sm($chatid,$txt,$keyback);
								}
						
				elseif($get=="rss"){
						step($chatid,"taiinrss");
						$txt="
						لطفا آدرس Rss خود را وارد کنید :\n\n★دقت داشته باشید خروجی URL شما نباید بصورت Json یا Array باشد \n\n★خروجی باید بصورت Xml باشد و نباید بصورت فایل یا عکس و ... باشد
						";
						sm($chatid,$txt,$keyback);
								}
						}
				}elseif($step=="themebartarin"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
}else{
if(isset($text)){
	step($chatid,"edit2");
	setvalue("dok","dokme",$vb,"themebartarin",$text);
	sm($chatid,"متن شما با موفقیت ثبت شد✅",$keydokme);
	}
}
}elseif($step=="changedokmetartib"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
}else{
	if(isset($text)){
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		$txt="اسم دکمه به $text با موفقیت تغییر یافت✅";
		step($chatid,"edit2");
		sm($chatid,$txt,$keytartib);
		}
	}
}elseif($step=="entqaldokme"){
$vb = getOther2($chatid);
if($text=="لغو عملیات❎"){
	if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" ||  getDokmenok($vb)=="jostojo"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
	}
}else{
	if(!empty(getDokme($text))){
		if(getDokmenok($text)=="newdokme"){
			step($chatid,"entqalnewdokme");
			setvalue("user","chatid",$chatid,"Other4",$text);
			$txt="آیا میخواهید دکمه ی قبلی بر روی همین دکمه انتقال پیدا کند یا خیر ،بر رویه دکمه های زیرشاخه؟!!";
			sm($chatid,$txt,$keyjadid);
			}else{
				if(empty(getInto($vb)) && empty(getInto($text))){
					//دکمه ی صفحه اصلی  به اصلی
				$gett= getKeyboard();
						$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$gett);
						$str=str_replace('"'.$text.'"','"'.$vb.'"',$str);
						$str = str_replace("changexxxtoxxxentqal1234",'"'.$text.'"',$str);
						setKeyboard($str);
						$onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$text,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$text,"type",$onetype);
						}elseif(empty(getInto($text)) && !empty(getInto($vb))){
								$keg = getInto($vb);
								//دکمه  غیراصلی به اصلی
							$key1 = getDokmetext($keg);
							$key2 = getKeyboard();
							$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$text.'"',$str);
                           setDokmetext($keg,$str);
                    $str2= str_replace('"'.$text.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str2);
                           setKeyboard($str2);
                           $onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$text,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$text,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$vb,"into");
						$twointo = getvalue("dok","dokme",$text,"into");
						setvalue("dok","dokme",$vb,"into",$twointo);
						setvalue("dok","dokme",$text,"into",$oneinto);
						}elseif(empty(getInto($vb)) && !empty(getInto($text))){
							//دکمه اصلی به غیر اصلی
							$keg = getInto($text);
							$key1 = getDokmetext($keg);
							$key2 = getKeyboard();
							$str = str_replace('"'.$text.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str);
                           setDokmetext($keg,$str);
                    $str2= str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$text.'"',$str2);
                           setKeyboard($str2);
                           $onetype = getvalue("dok","dokme",$text,"type");
						$twotype = getvalue("dok","dokme",$vb,"type");
						setvalue("dok","dokme",$text,"type",$twotype);
						setvalue("dok","dokme",$vb,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$text,"into");
						$twointo = getvalue("dok","dokme",$vb,"into");
						setvalue("dok","dokme",$text,"into",$twointo);
						setvalue("dok","dokme",$vb,"into",$oneinto);
							}else{
							// دکمه غیراصلی به غیر اصلی
							$key1 = getDokmetext(getInto($vb));
							$key2 = getDokmetext(getInto($text));
							$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$text.'"',$str);
                           setDokmetext(getInto($vb),$str);
                    $str2= str_replace('"'.$text.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str2);
                           setDokmetext(getInto($text),$str2);
                           $onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$text,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$text,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$vb,"into");
						$twointo = getvalue("dok","dokme",$text,"into");
						setvalue("dok","dokme",$vb,"into",$twointo);
						setvalue("dok","dokme",$text,"into",$oneinto);
							}
						step($chatid,"panel");
						sm($chatid,"انتقال با موفقیت انجام شد✅\n\nبه پنل برگشتید :",$keypanel);
				}
		}
	}
}elseif($step=="autodel"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" ||  getDokmenok($vb)=="jostojo"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
	}
	}elseif($text=="روشن✅"){
			sm($chatid,"قابلیت حذف خودکار برای این دکمه فعال شد✅");
			setvalue("dok","dokme",$vb,"autodel","on");
			}elseif($text=="خاموش⛔"){
			sm($chatid,"قابلیت حذف خودکار برای این دکمه غیرفعال شد✅");
			setvalue("dok","dokme",$vb,"autodel","off");
			}elseif($text=="تنظیم زمان⏳"){
				sm($chatid,"💎لطفا زمان حذف خودکار را برحسب <b>ثانیه</b> وارد نمایید :

⚠️توجه داشته باشید پس از پایان یافتن زمان پست مورد نظر بعد از اپدیت ربات پاک خواهد شد.

⚠️فقط از 10 تا 99999 ثانیه مجاز میباشد",$keyback);
step($chatid,"autodel2");
				}
}elseif($step=="autodel2"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
	sm($chatid,"به عقب برگشتید :",$keyautodel);
	step($chatid,"autodel");
	}elseif($text >= 10 && $text <= 99999){
		setvalue("dok","dokme",$vb,"timeautodel",$text);
		sm($chatid,"زمان حذف خودکار به $text تغییر یافت✅",$keyautodel);
		step($chatid,"autodel");
		}else{
			sm($chatid,"⚠️لطفا یک عدد بین <b>10</b> تا <b> 99999</b> وارد نمایید ‼️");
			}
}elseif($step=="entqalnewdokme"){
	$vb = getOther2($chatid);
	$vb2 = getvalue("user","chatid",$chatid,"Other4");
if($text=="لغو عملیات❎"){
	if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" ||  getDokmenok($vb)=="jostojo"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
	}
	}else{
		if($text=="خیر،روی بقیه❎"){
			step($chatid,"entqaldokme");
						$keyb=getDokmetext($vb2);
			$keo='{"keyboard":['.$keyb.',[{"text":"لغو عملیات❎"}]],"resize_keyboard":true}';
				$txt="لطفا یک دکمه را برای جابه جایی انتخاب کنید :";
	sm($chatid,"$txt",$keo);
			}elseif($text=="بله ،روی همین✅"){
				if(empty(getInto($vb)) && empty(getInto($vb2))){
					//دکمه ی صفحه اصلی  به اصلی
				$gett= getKeyboard();
						$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$gett);
						$str=str_replace('"'.$vb2.'"','"'.$vb.'"',$str);
						$str = str_replace("changexxxtoxxxentqal1234",'"'.$vb2.'"',$str);
						setKeyboard($str);
						$onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$vb2,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$vb2,"type",$onetype);
						}elseif(empty(getInto($vb2)) && !empty(getInto($vb))){
								$keg = getInto($vb);
								//دکمه  غیراصلی به اصلی
							$key1 = getDokmetext($keg);
							$key2 = getKeyboard();
							$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$vb2.'"',$str);
                           setDokmetext($keg,$str);
                    $str2= str_replace('"'.$vb2.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str2);
                           setKeyboard($str2);
                           $onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$vb2,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$vb2,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$vb,"into");
						$twointo = getvalue("dok","dokme",$vb2,"into");
						setvalue("dok","dokme",$vb,"into",$twointo);
						setvalue("dok","dokme",$vb2,"into",$oneinto);
						}elseif(empty(getInto($vb)) && !empty(getInto($vb2))){
							//دکمه اصلی به غیر اصلی
							$keg = getInto($vb2);
								//دکمه  غیراصلی به اصلی
							$key1 = getDokmetext($keg);
							$key2 = getKeyboard();
							$str = str_replace('"'.$vb2.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str);
                           setDokmetext($keg,$str);
                    $str2= str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$vb2.'"',$str2);
                           setKeyboard($str2);
                           $onetype = getvalue("dok","dokme",$vb2,"type");
						$twotype = getvalue("dok","dokme",$vb,"type");
						setvalue("dok","dokme",$vb2,"type",$twotype);
						setvalue("dok","dokme",$vb,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$vb2,"into");
						$twointo = getvalue("dok","dokme",$vb,"into");
						setvalue("dok","dokme",$vb2,"into",$twointo);
						setvalue("dok","dokme",$vb,"into",$oneinto);
							}else{
							// دکمه غیراصلی به غیر اصلی
							$key1 = getDokmetext(getInto($vb));
							$key2 = getDokmetext(getInto($vb2));
							$str = str_replace('"'.$vb.'"',"changexxxtoxxxentqal1234",$key1);
                           $str = str_replace("changexxxtoxxxentqal1234",'"'.$vb2.'"',$str);
                           setDokmetext(getInto($vb),$str);
                    $str2= str_replace('"'.$vb2.'"',"changexxxtoxxxentqal1234",$key2);
                           $str2 = str_replace("changexxxtoxxxentqal1234",'"'.$vb.'"',$str2);
                           setDokmetext(getInto($vb2),$str2);
                           $onetype = getvalue("dok","dokme",$vb,"type");
						$twotype = getvalue("dok","dokme",$vb2,"type");
						setvalue("dok","dokme",$vb,"type",$twotype);
						setvalue("dok","dokme",$vb2,"type",$onetype);
						$oneinto = getvalue("dok","dokme",$vb,"into");
						$twointo = getvalue("dok","dokme",$vb2,"into");
						setvalue("dok","dokme",$vb,"into",$twointo);
						setvalue("dok","dokme",$vb2,"into",$oneinto);
							}
						step($chatid,"panel");
						sm($chatid,"انتقال با موفقیت انجام شد✅\n\nبه پنل برگشتید :",$keypanel);
						
				}
		}

}elseif($step=="nokback"){
$vb = getOther2($chatid);
											if($text=="برگشت↪"){
												step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}else{
		if($text=="منوی اصلی"){
			setvalue("dok","dokme",$vb,"keyback","منوی اصلی");
			step($chatid,"edit2");
				sm($chatid,"ثبت شد✅\n\nبه منو برگشتید :",$keysback);
			}else{
				if(!empty(getvalue("dok","dokme",$text,"dokme"))){
					setvalue("dok","dokme",$vb,"keyback",$text);
					step($chatid,"edit2");
				sm($chatid,"ثبت شد✅\n\nبه منو برگشتید :",$keysback);
			
					}else{
						sm($chatid,"⛔لطفا از روی کیبورد انتخاب کنید:");
						}
				
		
		}
}
}elseif($step=="qoflforward"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
	if(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}
}else{
	if($text=="فعال✅"){
		setvalue("dok","dokme",$vb,"qoflforward","on");
		sm($chatid,"قفل فوروارد در گرفتن محتوا ممنوع شد✅");
		}elseif($text=="غیرفعال⛔"){
		setvalue("dok","dokme",$vb,"qoflforward","off");
		sm($chatid,"ارسال فوروارد در گرفتن محتوا ازاد شد✅");
		}elseif($text=="تغییر متن فوروارد ممنوع🚫"){
			step($chatid,"qoflforwardtext");
			sm($chatid,"⭐️لطفا متن خود را بفرستید تا زمان فرستادن محتوای فورواردی برای کاربر ارسال شود

★شما میتوانید از پیشفرض های ربات در این قسمت استفاده نمایید :

★شما میتوانید از تگ های Html استفاده کنید

★شما میتوانید از دکمه های شیشه ای استفاده نمایید",$keyback);
}
	}
}elseif($step=="addemza"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
	if(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}
}else{
	if($text=="روشن✅"){
		setvalue("dok","dokme",$vb,"emza","on");
		sm($chatid,"افزودن امضا در پست های کانال با موفقیت روشن شد✅");
		}elseif($text=="خاموش⛔"){
		setvalue("dok","dokme",$vb,"emza","off");
		sm($chatid,"افزودن امضا در پست های کانال با موفقیت خاموش شد✅");
		}
elseif($text=="🔖افزودن امضا"){
			step($chatid,"addemzatext");
			sm($chatid,"⭐️لطفا متن خود را بفرستید تا برای ثبت امضا در زمان فرستادن پست به کانال ثبت شود.

★شما میتوانید از پیشفرض های ربات در این قسمت استفاده نمایید :

★شما میتوانید از تگ های Html استفاده کنید

★شما میتوانید از دکمه های شیشه ای استفاده نمایید",$keyback);
}
	}
}elseif($step=="addemzatext"){
	$vb=getOther2($chatid);
	if($text=="برگشت↪"){
	step($chatid,"addemza");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyemza);
}else{
	step($chatid,"addemza");
	setvalue("dok","dokme",$vb,"emzatext",$text);
	sm($chatid,"امضای شما با موفقیت ثبت شد✅",$keyemza);
	}
	}
	elseif($step=="qoflforwardtext"){
	$vb=getOther2($chatid);
	if($text=="برگشت↪"){
	step($chatid,"qoflforward");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyqoflforward);
}else{
	step($chatid,"qoflforward");
	setvalue("dok","dokme",$vb,"qoflforwardtext",$text);
	sm($chatid,"متن قفل فوروارد با موفقیت تغییر یافت✅",$keyqoflforward);
	}
	}
elseif($step=="pmschannel"){
$vb = getOther2($chatid);
if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
}else{
	if($text=="/empty"){
		setvalue("dok","dokme",$vb,"textchannel","");
		$txt="پیشفرض برای متن کانال ثبت شد✅";
		step($chatid,"edit2");
		sm($chatid,$txt,$keyschannel);
		}else{
			setvalue("dok","dokme",$vb,"textchannel",$text);
		$txt="محتوای شما برای متن کانال ثبت شد✅";
		step($chatid,"edit2");
		sm($chatid,$txt,$keyschannel);
			}
}
}elseif($step=="taiinidchannel"){
	$vb = getOther2($chatid);
if($text=="برگشت↪"){
	if(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
}
}else{
	if(preg_match("/^(\@)(.*)$/",$text)){
				preg_match("/^(\@)(.*)$/",$text,$mat);
				$user = $mat[2];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setvalue("dok","dokme",$vb,"channel",$text);
				if(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keyfchannel);
}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keyschannel);
}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keysearch);
}
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
				//$stat = $url2->result->status;
							}elseif(preg_match('/^\-[0-9]+$/',$text)){
								preg_match('/^\-[0-9]+$/',$text,$mat);
				$user = $mat[0];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setvalue("dok","dokme",$vb,"channel",$text);
				if(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keyfchannel);
}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keyschannel);
}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keysearch);
}
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم یا ایدی عددی کانال را بفرستید";
sm($chatid,$txt);
				}
								
								}else{
							$txt="⛔️فرمت یوزرنیم یا ایدی عددی ارسال شده اشتباه میباشد!

❗️لطفا یوزرنیم را همراه با @ برای ما بفرستید 
مثال 🆔 : @$channel_name_en
مثال 🆔 : -1002135620077"
;
				sm($chatid,$txt);		
								}
	}
}elseif($step=="nonamayesh"){
	$vb = getOther2($chatid);
if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif($text=="نمایش بصورت فوروارد🔁"){
		setvalue("dok","dokme",$vb,"textemtiaz1","forward");
		step($chatid,"edit2");
sm($chatid,"عملیات ثبت شد✅",$keysearch);
		}elseif($text=="نمایش بصورت پیام📄"){
			setvalue("dok","dokme",$vb,"textemtiaz1","ersal");
		step($chatid,"edit2");
sm($chatid,"عملیات ثبت شد✅",$keysearch);
			}elseif($text=="نمایش بصورت لینک پست🌐"){
				setvalue("dok","dokme",$vb,"textemtiaz1","link");
		step($chatid,"edit2");
sm($chatid,"عملیات ثبت شد✅",$keysearch);
				}
}elseif($step=="taiinsearchnamo"){
	$vb = getOther2($chatid);
if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}else{
		if(isset($text)){
				setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		step($chatid,"edit2");
sm($chatid,"متن شما با موفقیت ثبت شد✅",$keysearch);
				}
				}
}elseif($step=="tanzimtedadsearch"){
	$vb = getOther2($chatid);
if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}else{
		if($text >= 1 && $text <= 20){
				setvalue("dok","dokme",$vb,"textemtiaz3",$text);
		step($chatid,"edit2");
sm($chatid,"تعداد سرچ به $text تغییر یافت✅",$keysearch);
				}else{
					sm($chatid,"⛔فقط از 1 تا 20 مجاز هست!!!");
					}
				}
}elseif($step=="taiinidchannel2"){
	$vb = getOther2($chatid);
if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
}else{
	if(preg_match("/^(\@)(.*)$/",$text)){
				preg_match("/^(\@)(.*)$/",$text,$mat);
				$user = $mat[2];
				setvalue("dok","dokme",$vb,"channel",$text);
				if(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
sm($chatid,"کانال با موفقیت ثبت شد✅",$keysearch);
}
		}else{
							$txt="⛔️فرمت یوزرنیم ارسال شده اشتباه میباشد!

❗️لطفا یوزرنیم را همراه با @ برای ما بفرستید 
مثال 🆔 : @$channel_name_en";
				sm($chatid,$txt);		
								}
	}
}elseif($step=="changepmha"){
	if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
}else{
	if($text=="تغییر متن نبودن و اشتباه ایدی عددی🆔"){
		step($chatid,"emtiaz1");
		sm($chatid,"لطفا متن جدید را برای تغییر متنی که برای کاربر موقع اشتباه بودن یا وجود نداشتن ایدی عددی کاربر نشان داده میشود را بفرستید",$keyback);
		}elseif($text=="تغییر متن ارسال ایدی عددی✳"){
			step($chatid,"emtiaz2");
			sm($chatid,"لطفا متن جدید را برای تغییر متنی که برای کاربر موقع ارسال جهت دریافت ایدی عددی نشان داده میشود را بفرستید",$keyback);
			}elseif($text=="تغییر متن تعداد امتیاز💰"){
			step($chatid,"emtiaz3");
			sm($chatid,"لطفا متن جدید را برای تغییر متنی که کاربر موقع درخواست تعداد امتیاز برای انتقال نشان داده میشود را بفرستید",$keyback);
			}elseif($text=="تغییر متن کم بودن امتیاز〽️"){
			step($chatid,"emtiaz4");
			sm($chatid,"لطفا متن جدید را برای تغییر متنی که برای کاربر موقع کم بودن امتیاز آن نشان داده میشود را بفرستید",$keyback);
			}elseif($text=="تغییر متن رسیدⓂ"){
			step($chatid,"emtiaz5");
			sm($chatid,"لطفا متن جدید را برای تغییر متنی که برای کاربر موقع عملیات موفق نشان داده  میشود را بفرستید",$keyback);
			}elseif($text=="تغییر متن رسید کاربر دوم🔆"){
			step($chatid,"emtiaz6");
			sm($chatid,"لطفا متن جدید را برای تغییر متنی که برای کاربر دوم موقع عملیات موفق نشان داده  میشود را بفرستید",$keyback);
			}
	
	}
	}elseif($step=="changepmhaenfjar"){
	if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
}else{
	if($text=="💣 تغییر متن مقدار ضریب شرط"){
		step($chatid,"emtiazz1");
		sm($chatid,"👇 متن جدید خود را ارسال کنید تا جایگزین شود :",$keyback);
		}elseif($text=="❌ تغییر متن موجودی ناکافی"){
			step($chatid,"emtiazz2");
			sm($chatid,"👇 متن جدید خود را ارسال کنید تا جایگزین شود :",$keyback);
			}elseif($text=="❌ تغییر متن اشتباه"){
			step($chatid,"emtiazz3");
			sm($chatid,"👇 متن جدید خود را ارسال کنید تا جایگزین شود :",$keyback);
			}elseif($text=="💣 تغییر متن ضریب نامعتبر"){
			step($chatid,"emtiazz4");
			sm($chatid,"👇 متن جدید خود را ارسال کنید تا جایگزین شود :",$keyback);
			}elseif($text=="☝️تغییر متن بیشتر حد شرط"){
			step($chatid,"emtiazz5");
			sm($chatid,"✅ متن جدید بیشترین حد شرط خود را به عدد ارسال کنید",$keyback);
			}elseif($text=="👇 تغییر متن کمترین حد شرط"){
			step($chatid,"emtiazz6");
			sm($chatid,"✅ متن جدید کمترین حد شرط خود را به عدد ارسال کنید",$keyback);
			}elseif($text=="🎉تغییر متن برد"){
			step($chatid,"emtiazz7");
			$txt = "
			🌟شما در این قسمت میتوانید متن برد را به دلخواه خود تغییر دهید.

⚠️لطفا از متغییر های زیر در این بخش استفاده نمایید
GETZARIB - ضریب ربات
EMTIAZ - امتیاز شرط بسته کاربر
ZARIB - ضریب کاربر
GETEMTIAZ - امتیاز برنده شده کاربر

⚠️همچنین علاوه بر این متغییر ها ، میتوانید از متغییر ها و دستورات قبلی ربات در این بخش استفاده نمایید.
مثال :
FIRSTNAME - اسم کاربر
CHATID - ایدی عددی کاربر
MD5(GETZARIB) - ضریب رمز نگاری md5
HASH(GETZARIB) - ضریب رمزنگاری hash
			";
			sm($chatid,$txt,$keyback);
			}elseif($text=="⭕️تغییر متن باخت"){
			step($chatid,"emtiazz8");
			$txt = "
			🌟شما در این قسمت میتوانید متن باخت را به دلخواه خود تغییر دهید.

⚠️لطفا از متغییر های زیر در این بخش استفاده نمایید
GETZARIB - ضریب ربات
EMTIAZ - امتیاز شرط بسته کاربر
ZARIB - ضریب کاربر
GETEMTIAZ - امتیاز باخت کاربر

⚠️همچنین علاوه بر این متغییر ها ، میتوانید از متغییر ها و دستورات قبلی ربات در این بخش استفاده نمایید.
مثال :
FIRSTNAME - اسم کاربر
CHATID - ایدی عددی کاربر
MD5(GETZARIB) - ضریب رمز نگاری md5
HASH(GETZARIB) - ضریب رمزنگاری hash
			";
			sm($chatid,$txt,$keyback);
			}
	
	}
	}elseif($step=="changepmhacreate"){
	if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
}else{
	if($text=="تغییر متن توکن اشتباه⛔"){
		step($chatid,"createbot1");
		sm($chatid,"لطفا متنی که در موقع فرستادن توکن اشتباه فرستاده میشود را ارسال نمایید :

🌟شما میتوانید از تگ های Html هم استفاده نمایید

🌟شما میتوانید از دکمه های شیشه ای استفاده نمایید

🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
		}elseif($text=="تغییر متن ساخت ربات با موفقیت✅"){
			step($chatid,"createbot2");
			sm($chatid,"لطفا متنی که در موقع ساخت ربات موفقیت آمیز فرستاده میشود را ارسال نمایید :

🌟شما میتوانید از تگ های Html هم استفاده نمایید

🌟شما میتوانید از دکمه های شیشه ای استفاده نمایید

🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
TOKENUSER - یوزرنیم ربات توکن
TOKENNAME - نام ربات توکن
TEXT- آخرین متن کاربر",$keyback);
			}elseif($text=="اتمام موجودی شما در $channel_name_fa💰"){
			step($chatid,"createbot3");
			sm($chatid,"لطفا متنی که در موقع اتمام موجودی شما در ربات $channel_name_fa برای کاربر فرستاده میشود را ارسال نمایید :

🌟شما میتوانید از تگ های Html هم استفاده نمایید

🌟شما میتوانید از دکمه های شیشه ای استفاده نمایید

🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
TEXT- آخرین متن کاربر",$keyback);
			}elseif($text=="تغییر متن توکن تکراری🔄"){
			step($chatid,"createbot4");
			sm($chatid,"لطفا متنی که موقع فرستادن توکن ربات از قبل ساخته شده برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
			}
	
	}
	}elseif($step=="changepmhadelete"){
	if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
}else{
	if($text=="تغییر متن انتخاب اشتباه⛔"){
		step($chatid,"deletebot1");
		sm($chatid,"لطفا متنی که در موقع انتخاب ربات اشتباه برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
		}elseif($text=="تغییر متن حذف با موفقیت✅"){
			step($chatid,"deletebot2");
			sm($chatid,"لطفا متنی که در موقع حذف ربات  برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
			}elseif($text=="تغییر متن تایید حذف🛃"){
			step($chatid,"deletebot3");
			sm($chatid,"لطفا متنی که در موقع تایید کردن حذف ربات برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
			}elseif($text=="تغییر نام دکمه بله✅"){
			step($chatid,"deletebot4");
			sm($chatid,"لطفا اسم دکمه ی بله✅ را وارد کنید :",$keyback);
			}elseif($text=="تغییر نام دکمه خیر⛔"){
			step($chatid,"deletebot5");
			sm($chatid,"لطفا اسم دکمه ی خیر⛔️ را وارد کنید :",$keyback);
			}
	}
	}elseif($step=="changepmhaupdate"){
	if($text=="برگشت↪"){
	step($chatid,"edit2");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
}else{
	if($text=="تغییر متن انتخاب اشتباه⛔"){
		step($chatid,"updatebot1");
		sm($chatid,"لطفا متنی که در موقع انتخاب ربات اشتباه برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
		}elseif($text=="تغییر متن اپدیت با موفقیت✅"){
			step($chatid,"updatebot2");
			sm($chatid,"لطفا متنی که در موقع آپدیت ربات برای کاربر فرستاده میشود را ارسال کنید

🌟شما میتوانید از تگ های Html هم استفاده نمایید


🌟شما میتوانید از پیشفرض های ربات هم استفاده نمایید
FIRSTNAME - نام کاربر
USERNAME - یوزرنیم کاربر
TEXT - آخرین متن کاربر",$keyback);
			}
	}
	}elseif($step=="deletebot1"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhadelete");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydeletebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhadelete");
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keydeletebot2);
		}
	}
		}elseif($step=="deletebot2"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhadelete");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydeletebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhadelete");
		setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keydeletebot2);
		}
	}
		}elseif($step=="deletebot3"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhadelete");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydeletebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhadelete");
		setvalue("dok","dokme",$vb,"textemtiaz3",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keydeletebot2);
		}
	}
		}elseif($step=="deletebot4"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhadelete");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydeletebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhadelete");
		setvalue("dok","dokme",$vb,"textemtiaz4",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keydeletebot2);
		}
	}
		}elseif($step=="deletebot5"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhadelete");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydeletebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhadelete");
		setvalue("dok","dokme",$vb,"textemtiaz5",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keydeletebot2);
		}
	}
		}
elseif($step=="updatebot1"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaupdate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyupdatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaupdate");
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyupdatebot2);
		}
	}
		}elseif($step=="updatebot2"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaupdate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyupdatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaupdate");
		setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyupdatebot2);
		}
	}
		}

elseif($step=="createbot1"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhacreate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhacreate");
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keycreatebot2);
		}
	}
		}elseif($step=="createbot2"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhacreate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhacreate");
		setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keycreatebot2);
		}
	}
		}elseif($step=="createbot3"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhacreate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhacreate");
		setvalue("dok","dokme",$vb,"textemtiaz3",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keycreatebot2);
		}
	}
		}elseif($step=="createbot4"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhacreate");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreatebot2);
}else{
	if(isset($text)){
		step($chatid,"changepmhacreate");
		setvalue("dok","dokme",$vb,"textemtiaz4",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keycreatebot2);
		}
	}
		}elseif($step=="emtiaz1"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange);
		}
	}
		}elseif($step=="emtiaz2"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textersal",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange2);
		}
	}
		}elseif($step=="emtiaz3"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange2);
		}
	}
		}elseif($step=="emtiaz4"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textemtiaz3",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange2);
		}
	}
		}elseif($step=="emtiaz5"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textresid",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange2);
		}
	}
		}elseif($step=="emtiaz6"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmha");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange2);
}else{
	if(isset($text)){
		step($chatid,"changepmha");
		setvalue("dok","dokme",$vb,"textemtiaz4",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keychange2);
		}
	}
		}elseif($step=="emtiazz1"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textemtiaz1",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz2"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textemtiaz2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz3"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textemtiaz3",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz4"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textemtiaz4",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz5"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(preg_match('/^[0-9?\.]+$/',$text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"highshart",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz6"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(preg_match('/^[0-9?\.]+$/',$text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"lowshart",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz7"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textresid",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}elseif($step=="emtiazz8"){
		$vb=getOther2($chatid);
		if($text=="برگشت↪"){
	step($chatid,"changepmhaenfjar");
sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyenfjar2);
}else{
	if(isset($text)){
		step($chatid,"changepmhaenfjar");
		setvalue("dok","dokme",$vb,"textresid2",$text);
		sm($chatid,"متن شما تغییر و ثبت شد✅\n\nبه عقب برگشتید :",$keyenfjar2);
		}
	}
		}
elseif($step=="qofldokme"){
				$vb = getOther2($chatid);
											if($text=="برگشت↪"){
												if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" ||  getDokmenok($vb)=="jostojo"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

	}
	}else{
	if($text=="قفل اجباری کانال"){
		if(getLockzirmaj($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on"|| getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
		step($chatid,"qoflejbar");
		
		$txt="🔰لطفا ابتدا ربات را در کانال خود ادمین نمایید.

سپس یوزرنیم کانال را را برای ما بفرستید

مثال 🆔 : @$channel_name_en

🔴توجه داشته باشید اگر ربات را از ادمینی کانال بردارید ، دکمه ی شما پس از جوین کاربر در کانال شما همچنان قفل خواهد ماند.";
sm($chatid,$txt,$keyback);
		}
			}elseif($text=="قفل با زیرمجموعه گیری"){
			if(getLockchannel($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on"  || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"zirmaj1");
				$txt="🔢لطفا تعداد زیرمجموعه هایی که کاربر باید دعوت کند را وارد نمایید :

🔴فقط ورودی عدد قابل قبول هست!!";
sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل با کسر امتیاز"){
			if(getLockchannel($vb)=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
				$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"coin1");
				$txt="لطفا تعداد امتیاز لازم برای باز شدن دکمه رو بدید 🎁

توجه ! : فقط عدد ✅";
				sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل امتیاز بدون کسر"){
			if(getLockchannel($vb)=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on"  || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
				$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"lockemtiaz21");
				$txt="لطفا تعداد امتیاز لازم برای باز شدن دکمه رو بدید 🎁

توجه ! : فقط عدد ✅";
				sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل تعداد استفاده"){
			if(getLockchannel($vb)=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on"  || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
				$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"locknafar");
				$txt="💠لطفا حداکثر تعدادی که کاربر میتواند از این دکمه استفاده نماید را وارد کنید :\n\n⚠توجه کنید با هر دستور کاربر یک تعداد اضافه میگردد ، حتی اگر کاربر تکراری باشد!!";
				sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل معمولی"){
			if(getLockchannel($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"sade");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

MEMBER - تعداد افراد افزوده شده
LINK- لینک افزودن ممبر
ALLMEM - مجموع زیرمجموعه لازم

 ";
sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل با کد"){
			if(getLockchannel($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"qoflcode");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید


 ";
sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل با شماره"){
			if(getLockchannel($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"qoflshomare");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید


 ";
sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل با شماره ایران"){
			if(getLockchannel($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"lockday")=="on" || getLockzirmaj($vb)=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockshomare")=="on"){
			$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"qoflshomareir");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید


 ";
sm($chatid,$txt,$keyback);
								}
			}elseif($text=="قفل روزانه"){
			if(getLockchannel($vb)=="on" || getLockzirmaj($vb)=="on" || getLockcoin($vb)=="on" || getvalue("dok","dokme",$vb,"locksade")=="on" || getvalue("dok","dokme",$vb,"lockcode")=="on" || getvalue("dok","dokme",$vb,"lockemtiaz2")=="on" || getvalue("dok","dokme",$vb,"locknafar")=="on"  || getvalue("dok","dokme",$vb,"lockshomare")=="on" || getvalue("dok","dokme",$vb,"lockshomare2")=="on"){
				$txt="یک قفل دیگر فعال هست🚫\n\nلطفا ابتدا با استفاده از حذف قفل دکمه ، قفل را حذف سپس اقدام به قفل کردن کنید";
sm($chatid,$txt);
			}else{
				step($chatid,"lockday");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

MEMBER - تعداد افراد افزوده شده
LINK- لینک افزودن ممبر
ALLMEM - مجموع زیرمجموعه لازم

 ";
sm($chatid,$txt,$keyback);
								}
			}
	}}elseif($step=="lockday"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"lockday","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change"  || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
			}
				}
		}elseif($step=="sade"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"locksade","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
			}
				}
		}elseif($step=="qoflcode"){
$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					step($chatid,"qoflcode2");
					setvalue("dok","dokme",$vb,"textlock2",$text);
					

$txt="⭐️خوب حالا متنی که میخوایید برای کاربر موقع فرستان کد اشتباه نمایش داده شود را بفرستید


⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید



 ";
sm($chatid,$txt,$keyback);
}
}
}elseif($step=="qoflcode2"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"lockcode","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
			}
				}
		}elseif($step=="qoflshomare"){
$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					step($chatid,"qoflshomare2");
					setvalue("dok","dokme",$vb,"textlock2",$text);
					

$txt="⭐️خوب حالا متنی که میخوایید برای کاربر موقع فرستان شماره اشتباه نمایش داده شود را بفرستید


⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید



 ";
sm($chatid,$txt,$keyback);
}
}
}elseif($step=="qoflshomareir"){
$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					step($chatid,"qoflshomareir2");
					setvalue("dok","dokme",$vb,"textlock2",$text);
					

$txt="⭐️خوب حالا متنی که میخوایید برای کاربر موقع فرستان شماره اشتباه نمایش داده شود را بفرستید


⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید



 ";
sm($chatid,$txt,$keyback);
}
}
}elseif($step=="qoflshomareir2"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"lockshomare2","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
			}
				}
		}elseif($step=="qoflshomare2"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"lockshomare","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel" ){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد??",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
			}
				}
		}
elseif($step=="zirmaj1"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
		if(preg_match('/^[0-9]+$/',$text)){
			
			setDoktedad($vb,$text);
			step($chatid,"zirmaj2");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

MEMBER - تعداد افراد افزوده شده
LINK- لینک افزودن ممبر
ALLMEM - مجموع زیرمجموعه لازم

 ";
				sm($chatid,$txt,$keyback);
			}else{
			$txt="⚠️ورودی فقط عدد قابل قبول هست!!

⚠️اعداد باید به زبان انگلیسی باشند!!

⚠️اعداد باید بین 0 تا 99 باشند!!";
			sm($chatid,$txt);
			}
		}
		}elseif($step=="zirmaj2"){
			$vb = getOther2($chatid);
				if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setLockzirmaj($vb,"on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
					}
				}
			}elseif($step=="coin1"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
		if(preg_match('/^[0-9]+$/',$text)){
			
			setDoktedad($vb,$text);
			step($chatid,"coin2");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

COIN - تعداد سکه های کاربر
LINK- لینک افزودن ممبر

 ";
				sm($chatid,$txt,$keyback);
			}else{
			$txt="⚠️ورودی فقط عدد قابل قبول هست!!

⚠️اعداد باید به زبان انگلیسی باشند!!";
			sm($chatid,$txt);
			}
		}
		}elseif($step=="coin2"){
			$vb = getOther2($chatid);
				if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setLockcoin($vb,"on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
					}
				}
			}elseif($step=="lockemtiaz21"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
		if(preg_match('/^[0-9]+$/',$text)){
			
			setDoktedad($vb,$text);
			step($chatid,"lockemtiaz22");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

COIN - تعداد سکه های کاربر
LINK- لینک افزودن ممبر

 ";
				sm($chatid,$txt,$keyback);
			}else{
			$txt="⚠️ورودی فقط عدد قابل قبول هست!!

⚠️اعداد باید به زبان انگلیسی باشند!!";
			sm($chatid,$txt);
			}
		}
		}elseif($step=="lockemtiaz22"){
			$vb = getOther2($chatid);
				if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
					setvalue("dok","dokme",$vb,"lockemtiaz2","on");
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
					}
				}
			}
			elseif($step=="locknafar"){
		$vb = getOther2($chatid);
			if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
		if(preg_match('/^[0-9]+$/',$text)){
			
			setvalue("dok","dokme",$vb,"finishnafar",$text);
			step($chatid,"locknafar2");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

COIN - تعداد سکه های کاربر
LINK- لینک افزودن ممبر

 ";
				sm($chatid,$txt,$keyback);
			}else{
			$txt="⚠️ورودی فقط عدد قابل قبول هست!!

⚠️اعداد باید به زبان انگلیسی باشند!!";
			sm($chatid,$txt);
			}
		}
		}elseif($step=="locknafar2"){
			$vb = getOther2($chatid);
				if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
				if(isset($text)){
					setTextlock($vb,$text);
			setvalue("dok","dokme",$vb,"locknafar","on");
			
					if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
					}
				}
			}
		elseif($step=="qoflejbar"){
			$vb = getOther2($chatid);
		if($text=="برگشت↪"){
			step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
			}else{
			if(preg_match("/^(\@)(.*)$/",$text)){
				preg_match("/^(\@)(.*)$/",$text,$mat);
				$user = $mat[2];
				$url1 = bot("getMe");
					$idme = $url1->result->id;
				$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@$user&user_id=$idme"),true);
			$status = $url2["result"]["status"];
			if($status=="administrator"){
				setDokuser($vb,$user);
				step($chatid,"textqofldokme");
				$txt="⭐️خوب حالا متنی که میخوایی برای کاربر نمایش داده بشه رو بفرست

⭐️میتوانید از Html هم در متن استفاده کنید

⭐️میتوانید از پیشفرض های ربات استفاده کنید

⭐️میتوانید از دکمه های شیشه ای استفاده کنید

 ";
				sm($chatid,$txt,$keyback);
				}else{
				$txt="⛔️ربات در کانال مورد نظر ادمین نیست!

❗️لطفا ابتدا ربات را در کانال ادمین و سپس یوزرنیم ربات را بفرستید";
sm($chatid,$txt);
				}
				//$stat = $url2->result->status;
							}else{
							$txt="⛔️فرمت یوزرنیم ارسال شده اشتباه میباشد!

❗️لطفا یوزرنیم را همراه با @ برای ما بفرستید 
مثال 🆔 : @$channel_name_en";
				sm($chatid,$txt);		
								}
			}
		}elseif($step=="textqofldokme"){
			$vb = getOther2($chatid);
			if($text=="برگشت↪"){
				step($chatid,"qofldokme");
			sm($chatid,"به عقب برگشتید↪\n\nیک گزینه را انتخاب کنید :",$keyqofl);
	
				}else{
				if(isset($text)){
					setLockchannel($vb,"on");
					setTextlock($vb,$text);
						if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل شد🔒",$keydokme);
	
			}
					}
				}
			}elseif($step=="hazfqofl"){
				$vb = getOther2($chatid);
				if($text=="خیر🚫"){
						if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

	}
					}elseif($text=="بله✅"){
						setDokuser($vb,null);
						setLockchannel($vb,"off");
						setLockzirmaj($vb,"off");
						setLockcoin($vb,"off");
						setvalue("dok","dokme",$vb,"lockday","off");
						setvalue("dok","dokme",$vb,"locksade","off");
						setvalue("dok","dokme",$vb,"lockcode","off");
						setvalue("dok","dokme",$vb,"lockshomare","off");
						setvalue("dok","dokme",$vb,"lockshomare2","off");
						setvalue("dok","dokme",$vb,"lockemtiaz2","off");
					setvalue("dok","dokme",$vb,"locknafar","off");
			
						setTextlock($vb,null);
						if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keysback);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دکمه ی شما با موفقیت قفل آن حذف شد🔓",$keydokme);
	
			}
						}
				}
						elseif($step=="TaiinDastor"){
						$vb = getOther2($chatid);
											if($text=="برگشت↪"){
												if(getDokmenok($vb)=="newdokme"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="sendadmin"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

	}
	}else{
		//$slash = preg_quote('/', '/');
	if(preg_match("/^(\/)(.*)+$/",$text)){
		if($text=="/start"){
			sm($chatid,"از این دستور نمیتوانید استفاده کنید🚫\n\n⭐این دستور صرفا جهت شروع ربات هست و استفاده ی آن در دیگر دستورات باعث تداخل میشود.");
			}else{
			if(!empty(getvalue("dok","dastor",$text,"dastor"))){
				$txt="این دستور از قبل در ربات ثبت شده است🚫\n\nدستور جدیدی را انتخاب کنید :";
				sm($chatid,$txt);
				}else{
				if(!empty(getDokme($text))){
					$txt="این دستور جز اسم دکمه ها هست🚫\n\n⭐لطفا یک دستور جدید انتخاب کنید";
					sm($chatid,$txt);
					}else{
						setDastor($vb,$text);
				if(getDokmenok($vb)=="newdokme"){
				step($chatid,"edit3");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keydokme2);
			}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keysendadmin);
	
			}elseif(getDokmenok($vb)=="back"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keysback);
	
			}elseif(getDokmenok($vb)=="schannel"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keyschannel);
	
			}elseif(getDokmenok($vb)=="search"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keysearch);
	
			}elseif(getDokmenok($vb)=="matntartib"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keytartib);
	
			}elseif(getDokmenok($vb)=="fchannel"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keyfchannel);
	
			}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
			step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keychange);
	
			}else{
				step($chatid,"edit2");
				sm($chatid,"دستور شما با موفقیت ثبت شد✅",$keydokme);
	
			}
				}
		}
		}
		}else{
		
		sm($chatid,"دستور شما باید با / (slash) آغاز شود🚫\n\n⭐به عنوان مثال : /test");
		}
	}
					}elseif($step=="noyesdastor"){
										$vb = getOther2($chatid);
											if($text=="خیر🚫"){
												if(getDokmenok($vb)=="newdokme" && empty(getInto($vb))){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
							step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
		step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}
	}elseif($text=="بله✅"){
		setDastor($vb,null);
		$txt="دستور شما با موفقیت حذف شد✅\n\nبه عقب برگشتید :";
		if(getDokmenok($vb)=="newdokme" && empty(getInto($vb))){
						step($chatid,"edit3");
				sm($chatid,$txt,$keydokme2);
}elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
							step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
		step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}
		}
							}
					elseif($step=="changename"){
					$vb = getOther2($chatid);
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل وجود دارد⛔");
						}else{
							if(isset($text)){
								if(getDokmenok($vb)=="newdokme" && empty(getInto($vb))){
									if($text=="برگشت↪"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}else{
	step($chatid,"panel");
	$vb = getOther2($chatid);
						$tevv=getallvalue("dok","dokme");
					foreach($tevv as $key){
					if(getInto($key)==$vb){
					setInto($key,$text);
					}
				}
					$gett= getKeyboard();
						$str = str_replace('"'.$vb.'"','"'.$text.'"',$gett);
						setKeyboard($str);
			//		sm($chatid,"دکمه تو در تو ولی در صفحه اول اینتو $tevv and $tev1");
				setDokme($vb,$text);
				setOther2($chatid,$text);
						$txt="
						اسم دکمه با موفقیت تغییر یافت✅\n\nشما به پنل برگشتید ، چکاری میخواید انجام بدید ؟
						";
		sm($chatid,$txt,$keypanel);

}
									}elseif(getDokmenok($vb)=="newdokme" && !empty(getInto($vb))){
										if($text=="برگشت↪"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
}else{
	step($chatid,"panel");
						$vb = getOther2($chatid);
						$into=getInto($vb);
						$gett = getDokmetext($into);
						$str = str_replace('"'.$vb.'"','"'.$text.'"',$gett);
						setDokmetext($into,$str);
						$tevv=getallvalue("dok","dokme");
					foreach($tevv as $key){
					if(getInto($key)==$vb){
					setInto($key,$text);
					}
				}
						//unset($dokme["into$vb"]);
						setDokme($vb,$text);
						setOther2($chatid,$text);
						
						$txt="
						اسم دکمه با موفقیت تغییر یافت✅\n\nشما به پنل برگشتید ، چکاری میخواید انجام بدید ؟
						";
		sm($chatid,$txt,$keypanel);

}
										}elseif(getDokmenok($vb) !=="newdokme" && !empty(getInto($vb))){
										if($text=="برگشت↪"){
											if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
												step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
		step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}
						}else{
step($chatid,"panel");
						$vb =getOther2($chatid);
						$into=getInto($vb);
						$gett = getDokmetext($into);
						$str = str_replace('"'.$vb.'"','"'.$text.'"',$gett);
						setDokmetext($into,$str);
						//$dokme['editer'] = $text;
						setDokme($vb,$text);
						
						setOther2($chatid,$text);
						$txt="
						اسم دکمه با موفقیت تغییر یافت✅\n\nشما به پنل برگشتید ، چکاری میخواید انجام بدید ؟
						";
		sm($chatid,$txt,$keypanel);

}
										}else{
										if($text=="برگشت↪"){
											if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
												step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
		step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}
						}else{
			step($chatid,"panel");
						$vb = getOther2($chatid);
						
						$gett = getKeyboard();
				$str = str_replace('"'.$vb.'"','"'.$text.'"',$gett);
						setKeyboard($str);
						setOther2($chatid,$text);
						setDokme($vb,$text);
						$txt="
						اسم دکمه با موفقیت تغییر یافت✅\n\nشما به پنل برگشتید ، چکاری میخواید انجام بدید ؟
						";
		sm($chatid,$txt,$keypanel);							
										
										}
						}
		}
			}			
						
					}elseif($step=="hazfdokme"){
						$vb = getOther2($chatid);
					if($text=="خیر🚫"){
						if(getDokmenok($vb)=="newdokme"){
							step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
							}
						elseif(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
							step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
		step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}
						}elseif($text=="بله✅"){
							$md = getvalue("hashmoh","text",$vb,"hash");
							$sql = "DROP TABLE `mohtava$md$userbott`";
							mysqli_query($con,$sql);
							$md = getvalue("hash","text",$vb,"hash");
							$sql = "DROP TABLE `mohtava$md$userbott`";
							mysqli_query($con,$sql);
	if(getDokmenok($vb)=="newdokme"){
	sm($chatid,"لطفا صبر کنید...");
			$var=$vb;
			$array=getallvalue("dok","dokme");
			foreach($array as $key){
					$end=0;
					$sum = $key;
					
					$count = count($array);
					while(true){
					$end=1;
					if(getInto($sum)==$vb){
						$arr[]=$key;
						break;
						}else{
							if(empty(getInto($sum))){
								$two = $array[$x];
								 break;
 }else{
 	$sum = getInto($sum);
							
							}
							}
				}
				}
				foreach($arr as $m){
					deletevalue("dok","dokme",$m);
					}
			
		}
	
	$key = getKeyboard();
	if(!empty(getInto($vb))){
		$into=getInto($vb);
		$ddokme=getDokmetext($vb);
		$kky = getDokmetext($into);
		if(strpos($kky,'"text":"'.$vb.'"},')){
		$cc = str_replace('{"text":"'.$vb.'"},',"",$kky);
		$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}elseif(strpos($kky,',{"text":"'.$vb.'"}]')){
		$cc = str_replace(',{"text":"'.$vb.'"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}else{
		$cc = str_replace('{"text":"'.$vb.'"}',"",$kky);
	$cp = str_replace("[],","",$cc);
		setDokmetext($into,$cp);
		}
		deletevalue("dok","dokme",$vb);
		step($chatid,"panel");
		$txt="دکمه ی شما با موفقیت حذف شد✅\n\nبه پنل برگشتید  چه کاری میخواید انجام بدید ؟!";
		sm($chatid,$txt,$keypanel);
		}else{
	if(strpos($key,'"text":"'.$vb.'"},')){
		$cc = str_replace('{"text":"'.$vb.'"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}elseif(strpos($key,',{"text":"'.$vb.'"}]')){
		$cc = str_replace(',{"text":"'.$vb.'"}',"",$key);
	$cp = str_replace("[],","",$cc);
		setKeyboard($cp);
	}else{
		$cc = str_replace('{"text":"'.$vb.'"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setKeyboard($cp);	
		}
		deletevalue("dok","dokme",$vb);
		step($chatid,"panel");
		$txt="دکمه ی شما با موفقیت حذف شد✅\n\nبه پنل برگشتید ، چه کاری میخواید انجام بدید ؟!";
		sm($chatid,$txt,$keypanel);
		}
	
	}
	
						}
					elseif($step=="noyes"){
					$vb = getOther2($chatid);
					if($text=="خیر🚫"){
						if(getDokmenok($vb)=="matntartib"){
							step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
							}else{
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
}
						}elseif($text=="بله✅"){
								setDokmetext($vb,"[]");
								step($chatid,"taiinchand1");
							$txt="محتویات قبلی حذف شدند\nمحتویات جدید را بفرستید تا ثبت شوند";
							sm($chatid,$txt,$keyback);
								}
					}
					elseif($step=="noyes2"){
					$vb = getOther2($chatid);
					if($text=="خیر🚫"){
						if(getDokmenok($vb)=="matntartib"){
							step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
							}else{
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
}
						}elseif($text=="بله✅"){
							step($chatid,"taiinchand1");
							$txt="بفرستید تا ثبت شوند :";
							sm($chatid,$txt,$keyback);
							}elseif($text=="پاک شوند🚮"){
								setDokmetext($vb,"[]");
								step($chatid,"taiinchand1");
							$txt="محتویات قبلی حذف شدند\nمحتویات جدید را بفرستید تا ثبت شوند";
							sm($chatid,$txt,$keyback);
								}
					}elseif($step=="noyesrand"){
					$vb = getOther2($chatid);
					if($text=="خیر🚫"){
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

						}elseif($text=="بله✅"){
							setDokmetext($vb,"[]");
								step($chatid,"taiinrand1");
							$txt="محتویات قبلی حذف شدند\nمحتویات جدید را بفرستید تا ثبت شوند";
							sm($chatid,$txt,$keyback);
								}
					}
					elseif($step=="noyes2rand"){
					$vb = getOther2($chatid);
					if($text=="خیر🚫"){
						step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

						}elseif($text=="بله✅"){
							step($chatid,"taiinrand1");
							$txt="بفرستید تا ثبت شوند :";
							sm($chatid,$txt,$keyback);
							}elseif($text=="پاک شوند🚮"){
								setDokmetext($vb,$text);
								step($chatid,"taiinrand1");
							$txt="محتویات قبلی حذف شدند\nمحتویات جدید را بفرستید تا ثبت شوند";
							sm($chatid,$txt,$keyback);
								}
					}
					elseif($step=="taiinchand1"){
					$vb = getOther2($chatid);
						
								
					if($text=="برگشت↪"){
						if(getDokmenok($vb)=="matntartib"){
							step($chatid,"edit2");
				sm($chatid,"محتوا ثبت شد✅\n\nچکاری میخواید انجام بدید؟",$keytartib);
							}else{
						step($chatid,"edit2");
				sm($chatid,"محتوا ثبت شد✅\n\nچکاری میخواید انجام بدید؟",$keydokme);
}
						}else{
						//$file=file_get_contents("dokme/$vb.json");
						$get = json_decode(getDokmetext($vb));
						$count = count($get);
						if($count >= 50){
							$txt="
							شما به حداکثر ثبت رسیده اید!!!🚫
							";
							sm($chatid,$txt);
							}else{
								if(isset($text)){
									$text=str_replace("\n","/r/n/r",$text);
										$text = str_replace('"','\"',$text);
							
										$eget = getDokmetext($vb);
					$xcml=',{"type":"text","text":"'.$text.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($photo)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"photo","text":"'.$photo.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
					setDokmetext($vb,$k);
					//	$cbn = json_encode($Message);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($video)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
						$eget = getDokmetext($vb);
					$xcml=',{"type":"video","text":"'.$video.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($document)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
							$cap = str_replace('"','\"',$cap);
						
							}else{
							$cap = null;
							}
				$eget = getDokmetext($vb);
					$xcml=',{"type":"document","text":"'.$document.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($sticker)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"sticker","text":"'.$sticker.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($contact_number)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"contact","text":"'.$contact_number.'","caption":"'.$contact_name.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($video_note)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"video_note","text":"'.$video_note.'","caption":"null"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($long_location)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"location","text":"'.$long_location.'","caption":"'.$lat_location.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($dice)){
						
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"dice","text":"'.$dice.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($voice)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"voice","text":"'.$voice.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($audio)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
				$eget = getDokmetext($vb);
					$xcml=',{"type":"audio","text":"'.$audio.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}else{
					$txt="
					این فرمت پشتیبانی نمیشود !!!\nلطفا از فرمت های دیگر استفاده کنید :
					";
					sm($chatid,$txt);
					
					}
							}
					}}elseif($step=="taiinrand1"){
					$vb = getOther2($chatid);
						
								
					if($text=="برگشت↪"){
						step($chatid,"edit2");
				sm($chatid,"عملیات تمام شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);

						}else{
						//$file=file_get_contents("dokme/$vb.json");
						$get = json_decode(getDokmetext($vb));
						$count = count($get);
						if($count >= 50){
							$txt="
							شما به حداکثر ثبت رسیده اید!!!‼️
							";
							sm($chatid,$txt);
							}else{
								if(isset($text)){
									$text=str_replace("\n","/r/n/r",$text);
										$text = str_replace('"','\"',$text);
							
										$eget = getDokmetext($vb);
					$xcml=',{"type":"text","text":"'.$text.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					//		step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($photo)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"photo","text":"'.$photo.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
					setDokmetext($vb,$k);
					//	$cbn = json_encode($Message);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($video)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
						$eget = getDokmetext($vb);
					$xcml=',{"type":"video","text":"'.$video.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($document)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
							$cap = str_replace('"','\"',$cap);
						
							}else{
							$cap = null;
							}
				$eget = getDokmetext($vb);
					$xcml=',{"type":"document","text":"'.$document.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($sticker)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"sticker","text":"'.$sticker.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($contact_number)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"contact","text":"'.$contact_number.'","caption":"'.$contact_name.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($long_location)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"location","text":"'.$long_location.'","caption":"'.$lat_location.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($video_note)){
					$eget = getDokmetext($vb);
					$xcml=',{"type":"video_note","text":"'.$video_note.'","caption":"null"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($dice)){
							
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"dice","text":"'.$dice.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($voice)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
					$eget = getDokmetext($vb);
					$xcml=',{"type":"voice","text":"'.$voice.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}elseif(isset($audio)){
						if(isset($caption)){
							$cap=str_replace("\n","/r/n/r",$caption);
										$cap = str_replace('"','\"',$cap);
							}else{
							$cap = null;
							}
				$eget = getDokmetext($vb);
					$xcml=',{"type":"audio","text":"'.$audio.'","caption":"'.$cap.'"}';
				//	$get2=json_encode($xc,128|256);
			//	$k=str_replace("},]","}]",$eget);
					$c =str_replace("]","$xcml]",$eget);
						$k=str_replace("[,{","[{",$c);
						setDokmetext($vb,$k);
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt);
					}else{
					$txt="
					این فرمت پشتیبانی نمیشود !!!\nلطفا از فرمت های دیگر استفاده کنید :
					";
					sm($chatid,$txt);
					
					}
							}
					}}elseif($step=="taiinApi"){
							$vb = getOther2($chatid);
							if($text=="برگشت↪"){
								step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
	}else{
		if(strpos($text,"||")){
			$exp=explode("||",$text);
			$tex=$exp[0];
			}else{
			$tex=$text;
			}
			if(preg_match('/^https?\:\/\/[^\s.*]+/i',$tex)){
	$xc=([
					"type"=>"url",
					"text"=>$text
					]);
					
				setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);	
		}else{
		$txt="این یک آدرس نیست🚫\n\nلطفا با دقت بیشتر آدرس URL خود را بفرستید :";
		sm($chatid,$txt);
		}
	}
						}elseif($step=="taiinrss"){
							$vb = getOther2($chatid);
							if($text=="برگشت↪"){
								step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
	}else{
	if(filter_var($text, FILTER_VALIDATE_URL)){
	$xc=([
					"type"=>"url",
					"text"=>$text
					]);
					
				setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);	
		}else{
		$txt="این یک آدرس نیست🚫\n\nلطفا با دقت بیشتر آدرس URL خود را بفرستید :";
		sm($chatid,$txt);
		}
	}
						}elseif($step=="taiincoin"){
					$vb = getOther2($chatid);
					if($text=="برگشت↪"){
								step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
			}else{
				if(isset($text)){
				setDokmetext($vb,$text);
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}
					}
					}elseif($step=="taiinphp"){
					$vb = getOther2($chatid);
					if($text=="برگشت↪"){
								step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
			}else{
				if(isset($text)){
					$text = mysqli_real_escape_string($con,$text);
				setDokmetext($vb,$text);
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}
					}
					}
					elseif($step=="taiintaki"){
					$vb = getOther2($chatid);
					if($text=="برگشت↪"){
								step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
			}else{
				if(isset($text)){
				$xc=([
					"type"=>"text",
					"text"=>$text
					]);
					
				setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($photo)){
						if(isset($caption)){
							$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"photo","text"=>"$photo","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($video)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"video","text"=>"$video","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($document)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"document","text"=>"$document","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($sticker)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"sticker","text"=>"$sticker","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($sticker)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"sticker","text"=>"$sticker","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($video_note)){
					$xc=(["type"=>"video_note","text"=>"$video_note","caption"=>"null"]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($contact_number)){
					$xc=(["type"=>"contact","text"=>"$contact_number","caption"=>"$contact_name"]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($long_location)){
					$xc=(["type"=>"location","text"=>"$long_location","caption"=>"$lat_location"]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($voice)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"voice","text"=>"$voice","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($audio)){
						if(isset($caption)){
								$cap=$caption;
							}else{
							$cap = null;
							}
					$xc=(["type"=>"audio","text"=>"$audio","caption"=>$cap]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}elseif(isset($dice)){
						
						
					$xc=(["type"=>"dice","text"=>$dice]);
					setDokmetext($vb,addslashes(json_encode($xc)));
					step($chatid,"edit2");
					$txt="
					محتوای شما ثبت شد✅
					";
					sm($chatid,$txt,$keydokme);
					}else{
					$txt="
					این فرمت پشتیبانی نمیشود !!!\nلطفا از فرمت های دیگر استفاده کنید :
					";
					sm($chatid,$txt);
					
					}
			
						}
			
						}
			
						elseif($step=="changefieldtext"){
							$vb = getOther2($chatid);
							if($text=="برگشت به عقب↪"){
						step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
ToDie();
				}
			elseif(strlen($text) <= 64 && strlen($text) >= 1 && isset($text)){
				step($chatid,"edit3");
				$txt="متن شما با موفقیت جایگزین شد✅";
				sm($chatid,$txt,$keydokme2);
				setvalue("dok","dokme",$vb,"field",$text);
				ToDie();
				}else{
					$txt="⚠خطا : \n\nمتن شما حتما باید بین 1 تا 64 حرف باشد ، فقط متن قابل قبول هست";
					sm($chatid,$txt);
					ToDie();
					}
							}
			elseif($step=="create"){
			if($text=="برگشت به عقب↪"){
				step($chatid,"panel");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}
			elseif($text=="افزودن دکمه🔼"){
							step($chatid,"keybala");
							$txt="
							برای تعیین نوع کاربرد دکمه یکی از گزینه های زیر را انتخاب کنید :\n\nبرای لغو عملیات برگشت را انتخاب کنید :
							";
							sm($chatid,$txt,$keycreate);
							}elseif($text=="افزودن دکمه🔽"){
									step($chatid,"keypain");
							$txt="
							برای تعیین نوع کاربرد دکمه یکی از گزینه های زیر را انتخاب کنید :\n\nبرای لغو عملیات برگشت را انتخاب کنید :
							";
							sm($chatid,$txt,$keycreate);
								}else{
								if(!empty(getDokme($text))){
									step($chatid,"keyhar");
									setOther($chatid,$text);
									$txt="
							برای تعیین نوع کاربرد دکمه یکی از گزینه های زیر را انتخاب کنید :\n\nبرای لغو عملیات برگشت را انتخاب کنید :
							";
							sm($chatid,$txt,$keycreate);
									}else{
									$txt="این دستور موجود نیست🚫\n\nاز دستورات روی دکمه ها استفاده کنید :";
									sm($chatid,$txt);
									}
								}
			
			
			}
			elseif($step=="matnshoro"){
			if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setStartmessage($text);
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				}
			
			}elseif($step=="textleft"){
			if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"textleft",$text);
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				}
			
			}elseif($step=="txtblock"){
			if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"txtblock",$text);
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				}
			
			}elseif($step=="txtnewcoin"){
			if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"pmnewcoin",$text);
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				}
			
			}elseif($step=="txxtzirmaj"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"textzirmaj",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}}elseif($step=="txxtopencode"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"opencodetext",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}}elseif($step=="eshtebah"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setTxteshtebah($text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}}elseif($step=="txxtopenshomare"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",2,"opencodetext",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}}elseif($step=="eshtebah"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setTxteshtebah($text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}}elseif($step=="txtbargasht"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setTxtback($text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}
					}elseif($step=="txtbargashtmoh"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"textbarmoh",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}
					}elseif($step=="txtblock"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"txtblock",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}
					}elseif($step=="namebargashtmoh"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"barmoh",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}
					}elseif($step=="namebargasht"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setvalue("data","id",1,"nameback",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				
					}
					}elseif($step=="txtnewozv"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setTxtnewozv($text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
			}
						}elseif($step=="txtpower"){
					if($text=="برگشت↪"){
				step($chatid,"editmatnha");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyedit);
				}else{
					step($chatid,"editmatnha");
					setTxtpower($text);
					$data["step$fromid"]= "editmatnha";
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyedit);
				}
					
					
					}elseif($step=="textersal"){
						$vb=getOther2($chatid);
					if($text=="برگشت↪"){
				step($chatid,"edit3");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme2);
				}else{
					step($chatid,"edit3");
					setvalue("dok","dokme",$vb,"textersal",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keydokme2);
				}
					
					
					}elseif($step=="matnersalget"){
						$vb=getOther2($chatid);
					if($text=="برگشت↪"){
				step($chatid,"edit2");
				if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="search"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysearch);
	}elseif(getDokmenok($vb)=="matntartib"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keytartib);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keychange);
	}else{
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keydokme);
		}		}else{
					if(isset($text)){
						step($chatid,"edit2");
						setvalue("dok","dokme",$vb,"textersal",$text);
					if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keysendadmin);
						}elseif(getDokmenok($vb)=="back"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keysback);
						}elseif(getDokmenok($vb)=="schannel"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyschannel);
						}elseif(getDokmenok($vb)=="search"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keysearch);
						}elseif(getDokmenok($vb)=="matntartib"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keytartib);
						}elseif(getDokmenok($vb)=="fchannel"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyfchannel);
						}elseif(getDokmenok($vb)=="change" || getDokmenok($vb)=="enfjar" || getDokmenok($vb)=="createbot" || getDokmenok($vb)=="deletebot" || getDokmenok($vb)=="updatebot"){
							sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keychange);
						}else{
			sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keydokme);
			}	}
					}
					
					}elseif($step=="matnresidadmin"){
						$vb=getOther2($chatid);
					if($text=="برگشت↪"){
				if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
					step($chatid,"edit2");
						sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyschannel);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyfchannel);
	}
}else{
					if(isset($text)){
						if(getDokmenok($vb)=="sendadmin" || getDokmenok($vb)=="jostojo"){
						step($chatid,"edit2");
						setvalue("dok","dokme",$vb,"textresid",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keysendadmin);
					}elseif(getDokmenok($vb)=="back"){
	step($chatid,"edit2");
				step($chatid,"edit2");
						setvalue("dok","dokme",$vb,"textresid",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keysback);
	}elseif(getDokmenok($vb)=="schannel"){
	step($chatid,"edit2");
						setvalue("dok","dokme",$vb,"textresid",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyschannel);
	}elseif(getDokmenok($vb)=="fchannel"){
	step($chatid,"edit2");
						setvalue("dok","dokme",$vb,"textresid",$text);
					sm($chatid,"متن شما با موفقیت ذخیره شد✅\n به پنل برگشتید:",$keyfchannel);
	}
						
				}
					}
					
					}elseif($step=="forward"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("user");
	$exp=getallvalue("user","chatid");
	if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other2",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",getvalue("data","id",1,"other2"));
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
	fm($exp[$x],$chatid,$messageid);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	}
				sm($chatid,"متن شما با موفقیت برای تمام کاربران فوروارد شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="forwardgp"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("group");
	$exp=getallvalue("group","chatid");
	if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other2",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",getvalue("data","id",1,"other2"));
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
	fm($exp[$x],$chatid,$messageid);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	}
				sm($chatid,"متن شما با موفقیت برای تمام گروه ها فوروارد شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="forwardsupergp"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("supergroup");
	$exp=getallvalue("supergroup","chatid");
	if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other2",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",getvalue("data","id",1,"other2"));
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
	fm($exp[$x],$chatid,$messageid);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	}
				sm($chatid,"متن شما با موفقیت برای تمام سوپرگروه ها فوروارد شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="sendall"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	$count=amarcount("user");
	$exp=getallvalue("user","chatid");
	if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$caption);
	$caption = $hi[0];
		$kei = textToinline("%$k%",$caption);
		}
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
		
		if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other1",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		$name=getvalue("user","chatid",$exp[$x],"firstname");
		$username=getvalue("user","chatid",$exp[$x],"username");
		$userid=getvalue("user","chatid",$exp[$x],"chatid");
		$array1 = array("FIRSTNAME","LASTNAME","USERNAME","USERID","BIO","IDBOT","BOTUSER","BOTNAME","GPNAME","GPUSER","CHATID","DESCRIOPTION","GETDICE","DICE","MESSAGEID","COIN","MEMBER","LINK","ALLMEM","HOUR","MINUTE","SECOND","JOINDATEM","JOINDATESH","JOINTIME","TIME","YEAR","MONTH","DAY","DATESH","FASL","HAFTEH","BASTANIBORG","HEYVANSAAL","MAHFA","SALFA","ROOZFA","PHOTO_ID","VIDEO_ID","VIDEO_NOTE_ID","STICKER_ID","DOCUMENT_ID","AUDIO_ID","VOICE_ID","NEW_MEMBER_NAME","NEW_MEMBER_USERNAME","NEW_MEMBER_ID","DATEM","CONTACT_NUMBER","CONTACT_NAME","CONTACT_ID","LONG_LOCATION","LAT_LOCATION","BOTMEM","CHANCE","TEXT","ADD");
	$array2 = array($name,"",$username,$exp[$x],$bio,$idbot,$botuser,$botname,$gpname,$gpuser,$chatid,$description,getOther($exp[$x]),getDice($exp[$x]),$messageid,getCoin($exp[$x]),getZirmaj($exp[$x]),"https://t.me/$botuser?start=$exp[$x]",getDokother2($text),date("H"),date("i"),date("s"),getJoindatem($exp[$x]),getJoindatesh($exp[$x]),getJointime($exp[$x]),date("H:i:s"),date("Y"),date("m"),date("d"),$datesh,jdate('f'),jdate('l'),jdate('p'),jdate('q'),jdate('F'),jdate('V'),jdate('J'),photo_file($chatid),video_file($chatid),video_note_file($chatid),sticker_file($chatid),document_file($chatid),audio_file($chatid),voice_file($chatid),$newname,$newuser,$newid,$datem,$contact_number,$contact_name,$contact_id,$long_location,$lat_location,amarcount("user"),rand(0,9),$text,"");
	$text=str_replace($array1,$array2,$text);
		$caption=str_replace($array1,$array2,$caption);
		
		if($xl==20){
			$t = getvalue("data","id",1,"other1");
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$t);
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
		if(isset($photo)){
			sp($exp[$x],$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($exp[$x],$video,$caption,$kei);
				}elseif(isset($document)){
					sd($exp[$x],$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($exp[$x],$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($exp[$x],$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($exp[$x],$sticker);
								}elseif(isset($text)){
									bot('sendmessage',[
									'chat_id'=>$exp[$x],
									'text'=>$text,
									'parse_mode'=>'HTML',
									'reply_markup'=>$kei
									]);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	
}
	}
				sm($chatid,"متن شما با موفقیت برای تمام کاربران ارسال شد✅\n\nبه پنل بازگشتید :",$keyersal);
				step($chatid,"sendtoall");
unlink("foreach.txt");
							}
						}elseif($step=="sendgp"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("group");
	$exp=getallvalue("group","chatid");
	if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$caption);
	$caption = $hi[0];
		$kei = textToinline("%$k%",$caption);
		}
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
		
		if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other2",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$data["messagid"]);
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
		if(isset($photo)){
			sp($exp[$x],$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($exp[$x],$video,$caption,$kei);
				}elseif(isset($document)){
					sd($exp[$x],$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($exp[$x],$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($exp[$x],$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($exp[$x],$sticker);
								}elseif(isset($text)){
	bot('sendmessage',[
									'chat_id'=>$exp[$x],
									'text'=>$text,
									'parse_mode'=>'HTML',
									'reply_markup'=>$kei
									]);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	
}
	}
				sm($chatid,"متن شما با موفقیت برای تمام گروه ها ارسال شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="sendsupergp"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("supergroup");
	$exp=getallvalue("supergroup","chatid");
	if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$caption);
	$caption = $hi[0];
		$kei = textToinline("%$k%",$caption);
		}
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
		
		if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other2",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$data["messagid"]);
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
		if(isset($photo)){
			sp($exp[$x],$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($exp[$x],$video,$caption,$kei);
				}elseif(isset($document)){
					sd($exp[$x],$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($exp[$x],$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($exp[$x],$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($exp[$x],$sticker);
								}elseif(isset($text)){
	bot('sendmessage',[
									'chat_id'=>$exp[$x],
									'text'=>$text,
									'parse_mode'=>'HTML',
									'reply_markup'=>$kei
									]);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	
}
	}
				sm($chatid,"متن شما با موفقیت برای تمام سوپرگروه ها ارسال شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="sendchannel"){
							if($text=="برگشت↪"){
				step($chatid,"sendtoall");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyersal);
				}else{
	step($chatid,"sendtoall");
	$count=amarcount("channel");
	$exp=getallvalue("channel","chatid");
	if(preg_match("/(%)([^\']+)(%)/",$caption,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$caption);
	$caption = $hi[0];
		$kei = textToinline("%$k%",$caption);
		}
		if(preg_match("/(%)([^\']+)(%)/",$text,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$text);
	$text = $hi[0];
		$kei = textToinline("%$k%",$text);
		}
		
		if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	setvalue("data","id",1,"other1",$mess);
	step($chatid,"sendtoall");
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",getvalue("data","id",1,"other1"));
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
		if(isset($photo)){
			sp($exp[$x],$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($exp[$x],$video,$caption,$kei);
				}elseif(isset($document)){
					sd($exp[$x],$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($exp[$x],$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($exp[$x],$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($exp[$x],$sticker);
								}elseif(isset($text)){
	bot('sendmessage',[
									'chat_id'=>$exp[$x],
									'text'=>$text,
									'parse_mode'=>'HTML',
									'reply_markup'=>$kei
									]);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	
}
	}
				sm($chatid,"متن شما با موفقیت برای تمام کانال ها ارسال شد✅\n\nبه پنل بازگشتید :",$keyersal);
unlink("foreach.txt");
							}
						}elseif($step=="keybala"){
							if($text=="برگشت↪"){
								$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,null);
						}
				step($chatid,"panel");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="متن تکی🔰"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtakibala");
						}
					step($chatid,"matntakibala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻استفاده از php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createphpbala");
						}
					step($chatid,"phpbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="متن چندتایی🔠"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchandbala");
						}
						step($chatid,"matnchandbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن به ترتیب⏬"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtartibbala");
						}
						step($chatid,"matntartibbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن رندوم💈"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createrandbala");
						}
						step($chatid,"matnrandbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Api❇"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createapibala");
						}
						step($chatid,"Apibala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Rss📃"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createrssbala");
						}
						step($chatid,"rssbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ساخت دکمه ی دیگر🆕"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createnewdokmebala");
						}
							step($chatid,"newdokmebala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
							}elseif($text=="گرفتن محتوا از کاربر📩"){
								$gvb = getallvalue("moh","dokme");
								foreach($gvb as $xc){
									deletevalue("moh","dokme",$xc);
									}
								step($chatid,"getpmbala");
								$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft);
								}elseif($text=="استفاده از دکمه های سیستمی⚙"){
								step($chatid,"systembala");
								$txt="یک گزینه را انتخاب کنید";
								sm($chatid,$txt,$keysistemi);
								}
							}elseif($step=="systembala"){
										if($text=="برگشت↪"){
													step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="دکمه ی جست و جو🔎"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createjostojobala");
						}
					step($chatid,"jostojobala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="دکمه جست و جو در کانال📚"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsearchbala");
						}
					step($chatid,"searchbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="دکمه ساخت ربات🤖"){
						/*
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcreatebotbala");
						}
					step($chatid,"createbotbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه اپدیت ربات♻"){
						/*
$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createupdatebotbala");
						}
					step($chatid,"updatebotbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه حذف ربات🚯"){
						/*
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createdeletebotbala");
						}
					step($chatid,"deletebotbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه ی بازگشت به خانه🏠"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbackbala");
						}
						step($chatid,"backbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="انتقال امتیاز♻"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchangebala");
						}
						step($chatid,"changebala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="دکمه سیستمی بازی انفجار💣"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createenfjarbala");
						}
						step($chatid,"enfjarbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های زیرمجموعه👥"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbartarinbala");
						}
						step($chatid,"bartarinbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های امتیاز⚜"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcoinbala");
						}
						step($chatid,"coinbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}
					}
									}elseif($step=="getpmbala"){
										if($text=="برگشت↪"){
											
													step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="نمایش متن✏"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategettakibala");
						}
					step($chatid,"getmatntakibala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻نمایش خروجی php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetphpbala");
						}
					step($chatid,"getphpbala");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="نمایش Api✳"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetapibala");
						}
						step($chatid,"getApibala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به ادمین👤"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsendadminbala");
						}
						step($chatid,"sendadminbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به کانال📨"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createschannelbala");
						}
						step($chatid,"schannelbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="فوروارد به کانال🔖"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createfchannelbala");
						}
						step($chatid,"fchannelbala");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="گرفتن محتوای دیگر📩"){
						step($chatid,"getpmmohtava1");
						$txt="لطفا انتخاب کنید ربات چه چیزی از کاربر دریافت کند؟!";
						sm($chatid,$txt,$keychandmoh);
						}
					}
									}elseif($step=="getpmmohtava1"){
										if($text=="برگشت↪️"){
											step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
											}elseif($text=="🔢عدد"){
												setvalue("user","chatid",$chatid,"Other4","addad");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="☎شماره تلفن"){
												setvalue("user","chatid",$chatid,"Other4","number");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📧ایمیل"){
												setvalue("user","chatid",$chatid,"Other4","email");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌐لینک"){
												setvalue("user","chatid",$chatid,"Other4","link");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📽ویدیو نوت"){
												setvalue("user","chatid",$chatid,"Other4","video_note");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📞مخاطب"){
												setvalue("user","chatid",$chatid,"Other4","contact");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌏نقشه"){
												setvalue("user","chatid",$chatid,"Other4","location");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔤متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","matn");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔡متن انگلیسی"){
												setvalue("user","chatid",$chatid,"Other4","english");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🇮🇷متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","farsimatn");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔣هرمتنی بجز کاراکتر"){
												setvalue("user","chatid",$chatid,"Other4","hamematn");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط متن انگلیسی و عدد🔢🔡"){
												setvalue("user","chatid",$chatid,"Other4","englishandnumber");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط فوروارد↩"){
												setvalue("user","chatid",$chatid,"Other4","forward");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="غیر فوروارد🔂"){
												setvalue("user","chatid",$chatid,"Other4","noforward");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="(@)یوزرنیم"){
												setvalue("user","chatid",$chatid,"Other4","username");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚡فقط دستور"){
												setvalue("user","chatid",$chatid,"Other4","dastor");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="✴️هرچیزی"){
												setvalue("user","chatid",$chatid,"Other4","hame");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚽توپ"){
												setvalue("user","chatid",$chatid,"Other4","ball");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🏀بسکتبال"){
												setvalue("user","chatid",$chatid,"Other4","basket");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎳بولینگ"){
												setvalue("user","chatid",$chatid,"Other4","boling");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎲تاس"){
												setvalue("user","chatid",$chatid,"Other4","dice");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎯دارت"){
												setvalue("user","chatid",$chatid,"Other4","dart");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎰اسلات"){
												setvalue("user","chatid",$chatid,"Other4","eslat");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎥فیلم"){
												setvalue("user","chatid",$chatid,"Other4","film");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎇عکس"){
												setvalue("user","chatid",$chatid,"Other4","photo");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}
elseif($text=="🔊آهنگ"){
												setvalue("user","chatid",$chatid,"Other4","audio");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔆استیکر"){
												setvalue("user","chatid",$chatid,"Other4","sticker");
												step($chatid,"getpmmohtava2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}
										}elseif($step=="getpmmohtava2"){
											if($text=="برگشت↪"){
														step($chatid,"keybala");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}elseif(isset($text)){
					step($chatid,"getpmmohtava");
					setvalue("user","chatid",$chatid,"Other5",$text);
					$txt="درصورتی که کاربر محتوای اشتباه برای ربات فرستاد ، ربات در جواب چه بگوید؟!";
					sm($chatid,$txt);
					}
											}
elseif($step=="getpmmohtava"){
											if($text=="برگشت↪"){
														step($chatid,"keybala");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(!empty(getallvalue("moh","dokme"))){
					$count = count(getallvalue("moh","dokme"))+1;
					$coon = "TEXT_$count";
					
					}else{
						$coon="TEXT_1";
						}
						$typemoh = getvalue("user","chatid",$chatid,"Other4");
						$textget = getvalue("user","chatid",$chatid,"Other5");
						$sql = "INSERT INTO `moh$userbott`(
						`dokme`,
						`textesh`,
						`textget`,
						`matn`
						) VALUES('$coon','$text','$textget','$typemoh')";
						mysqli_query($con,$sql);
					step($chatid,"getpmbala");
												$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft2);
										}
										}
										elseif($step=="sendadminbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createsendadminbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','sendadmin','bala','$vb',''");
									step($chatid,'panel');
				
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','text'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','text');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','sendadmin','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
					}
				}
					}elseif($step=="schannelbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createschannelbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','schannel','bala','$vb',''");
									step($chatid,'panel');
				
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','schannel','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="fchannelbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createfchannelbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','fchannel','bala','$vb',''");
									step($chatid,'panel');
				
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','fchannel','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="getmatntakibala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="creategettakibala"){
								
								setEditer($vb,null);
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getmatntaki','bala','$vb',''");
									
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!empty(getallvalue('moh','dokme'))){
					$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getmatntaki','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="getphpbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetphpbala"){
								
								setEditer($vb,null);
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getphp','bala','$vb',''");
									
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!empty(getallvalue('moh','dokme'))){
					$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");

									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getphp','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}
										elseif($step=="matntakibala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createtakibala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntaki','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntaki','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="phpbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createphpbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','php','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','php','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
					}
				}
					}elseif($step=="bartarinbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createbartarinbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','bartarin','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','bartarin','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="coinbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createcoinbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','coin','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','coin','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="jostojobala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createjostojobala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','jostojo','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','jostojo','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="searchbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createsearchbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','search','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','search','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="createbotbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createcreatebotbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','createbot','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','createbot','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="deletebotbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createdeletebotbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','deletebot','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','deletebot','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="updatebotbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createupdatebotbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','updatebot','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','updatebot','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="backbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createbackbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','back','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','back','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
				}
					}elseif($step=="changebala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createchangebala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','change','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','change','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
					}
				}
					}elseif($step=="enfjarbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createenfjarbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','enfjar','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','enfjar','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
					}
				}
					}elseif($step=="matnchandbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createchandbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnchand','bala','$vb','[]'");
							step($chatid,'panel');
				$olddokme =getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnchand','bala','[]'");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}
					}}elseif($step=="matntartibbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createtartibbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntartib','bala','$vb','[]'");
							step($chatid,'panel');
				$olddokme =getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntartib','bala','[]'");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}}
					}elseif($step=="matnrandbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createrandbala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnrand','bala','$vb','[]'");
							step($chatid,'panel');
							setDokmetext($text,"");
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnrand','bala','[]'");
									step($chatid,"panel");
									setDokmetext($text,"");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}}
					}elseif($step=="Apibala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createapibala"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','Api','bala','$vb',''");
						step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','Api','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}}
					}elseif($step=="getApibala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetapibala"){
								setEditer($vb,null);
									
         if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getApi','bala','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getApi','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}}
					}elseif($step=="rssbala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createrssbala"){
								setEditer($vb,null);
								
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','rss','bala','$vb',''");
									step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
				if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','rss','bala',''");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
					}
				}}
					}elseif($step=="newdokmebala"){
									if($text=="برگشت↪"){
				step($chatid,"keybala");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createnewdokmebala"){
								setEditer($vb,null);
								$keytest='[{"text":""}],[{"text":""}]';
$dataa=$keytest;
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','newdokme','bala','$vb','$dataa'");
									step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									sm($chatid,$txt,$keypanel);
				$keytest='[{"text":""}],[{"text":""}]';
$dataa=$keytest;
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','newdokme','bala','$dataa'");
									step($chatid,"panel");
				$olddokme = getKeyboard();
				$newdokme = str_replace('[{"text":""}],','[{"text":""}],[{"text":"'.$text.'"}],',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی ویرایش دکمه ها کار دکمه را به پابان برسانید .";
				sm($chatid,$txt,$keypanel);
			}
				}}
				}
					}
						
							elseif($step=="keyhar"){
							if($text=="برگشت↪"){
				step($chatid,"panel");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
			}elseif($text=="متن تکی🔰"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtakihar");
						}
					step($chatid,"matntakihar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻استفاده از php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createphphar");
						}
					step($chatid,"phphar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}

elseif($text=="متن چندتایی🔠"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchandhar");
						}
						step($chatid,"matnchandhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن به ترتیب⏬"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtartibhar");
						}
						step($chatid,"matntartibhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن رندوم💈"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createrandhar");
						}
						step($chatid,"matnrandhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Api❇"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createapihar");
						}
						step($chatid,"Apihar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Rss📃"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creatersshar");
						}
						step($chatid,"rsshar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ساخت دکمه ی دیگر🆕"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createnewdokmehar");
						}
							step($chatid,"newdokmehar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
							}elseif($text=="گرفتن محتوا از کاربر📩"){
								$gvb = getallvalue("moh","dokme");
								foreach($gvb as $xc){
									deletevalue("moh","dokme",$xc);
									}
								step($chatid,"getpmhar");
								$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft);
								}elseif($text=="استفاده از دکمه های سیستمی⚙"){
								step($chatid,"systemhar");
								$txt="یک گزینه را انتخاب کنید";
								sm($chatid,$txt,$keysistemi);
								}
							}elseif($step=="systemhar"){
										if($text=="برگشت↪"){
													step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="دکمه ی جست و جو🔎"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createjostojohar");
						}
					step($chatid,"jostojohar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="دکمه جست و جو در کانال📚"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsearchhar");
						}
					step($chatid,"searchhar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="دکمه ساخت ربات🤖"){
						/*
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcreatebothar");
						}
					step($chatid,"createbothar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه اپدیت ربات♻"){
					/*
	$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createupdatebothar");
						}
					step($chatid,"updatebothar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه حذف ربات🚯"){
						/*
$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createdeletebothar");
						}
					step($chatid,"deletebothar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه ی بازگشت به خانه🏠"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbackhar");
						}
						step($chatid,"backhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="انتقال امتیاز♻"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchangehar");
						}
						step($chatid,"changehar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="دکمه سیستمی بازی انفجار💣"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createenfjarhar");
						}
						step($chatid,"enfjarhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های زیرمجموعه👥"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbartarinhar");
						}
						step($chatid,"bartarinhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های امتیاز⚜"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcoinhar");
						}
						step($chatid,"coinhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}
					}
									}
							elseif($step=="getpmhar"){
										if($text=="برگشت↪"){
													step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="نمایش متن✏"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategettakihar");
						}
					step($chatid,"getmatntakihar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻نمایش خروجی php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetphphar");
						}
					step($chatid,"getphphar");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="نمایش Api✳"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetapihar");
						}
						step($chatid,"getApihar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به کانال📨"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createschannelhar");
						}
						step($chatid,"schannelhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="فوروارد به کانال🔖"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createfchannelhar");
						}
						step($chatid,"fchannelhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به ادمین👤"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsendadminhar");
						}
						step($chatid,"sendadminhar");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="گرفتن محتوای دیگر📩"){
						step($chatid,"getpmmohtavahar1");
						$txt="لطفا انتخاب کنید ربات چه چیزی از کاربر دریافت کند؟!";
						sm($chatid,$txt,$keychandmoh);
						}
					}
									}elseif($step=="getpmmohtavahar1"){
										if($text=="برگشت↪️"){
											step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
											}elseif($text=="🔢عدد"){
												setvalue("user","chatid",$chatid,"Other4","addad");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="☎شماره تلفن"){
												setvalue("user","chatid",$chatid,"Other4","number");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📧ایمیل"){
												setvalue("user","chatid",$chatid,"Other4","email");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌐لینک"){
												setvalue("user","chatid",$chatid,"Other4","link");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📞مخاطب"){
												setvalue("user","chatid",$chatid,"Other4","contact");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌏نقشه"){
												setvalue("user","chatid",$chatid,"Other4","location");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📽ویدیو نوت"){
												setvalue("user","chatid",$chatid,"Other4","video_note");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔤متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","matn");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔡متن انگلیسی"){
												setvalue("user","chatid",$chatid,"Other4","english");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🇮🇷متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","farsimatn");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔣هرمتنی بجز کاراکتر"){
												setvalue("user","chatid",$chatid,"Other4","hamematn");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط متن انگلیسی و عدد🔢🔡"){
												setvalue("user","chatid",$chatid,"Other4","englishandnumber");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط فوروارد↩"){
												setvalue("user","chatid",$chatid,"Other4","forward");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="غیر فوروارد🔂"){
												setvalue("user","chatid",$chatid,"Other4","noforward");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="(@)یوزرنیم"){
												setvalue("user","chatid",$chatid,"Other4","username");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚡فقط دستور"){
												setvalue("user","chatid",$chatid,"Other4","dastor");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="✴️هرچیزی"){
												setvalue("user","chatid",$chatid,"Other4","hame");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚽توپ"){
												setvalue("user","chatid",$chatid,"Other4","ball");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}

elseif($text=="🏀بسکتبال"){
												setvalue("user","chatid",$chatid,"Other4","basket");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎳بولینگ"){
												setvalue("user","chatid",$chatid,"Other4","boling");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎲تاس"){
												setvalue("user","chatid",$chatid,"Other4","dice");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎯دارت"){
												setvalue("user","chatid",$chatid,"Other4","dart");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎰اسلات"){
												setvalue("user","chatid",$chatid,"Other4","eslat");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎥فیلم"){
												setvalue("user","chatid",$chatid,"Other4","film");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎇عکس"){
												setvalue("user","chatid",$chatid,"Other4","photo");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔊آهنگ"){
												setvalue("user","chatid",$chatid,"Other4","audio");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔆استیکر"){
												setvalue("user","chatid",$chatid,"Other4","sticker");
												step($chatid,"getpmmohtavahar2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}
										}elseif($step=="getpmmohtavahar2"){
											if($text=="برگشت↪"){
														step($chatid,"keyhar");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}elseif(isset($text)){
					step($chatid,"getpmmohtavahar");
					setvalue("user","chatid",$chatid,"Other5",$text);
					$txt="درصورتی که کاربر محتوای اشتباه برای ربات فرستاد ، ربات در جواب چه بگوید؟!";
					sm($chatid,$txt);
					}
											}
elseif($step=="getpmmohtavahar"){
											if($text=="برگشت↪"){
														step($chatid,"keyhar");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(!empty(getallvalue("moh","dokme"))){
					$count = count(getallvalue("moh","dokme"))+1;
					$coon = "TEXT_$count";
					
					}else{
						$coon="TEXT_1";
						}
						$typemoh = getvalue("user","chatid",$chatid,"Other4");
						$textget = getvalue("user","chatid",$chatid,"Other5");
						$sql = "INSERT INTO `moh$userbott`(
						`dokme`,
						`textesh`,
						`textget`,
						`matn`
						) VALUES('$coon','$text','$textget','$typemoh')";
						mysqli_query($con,$sql);
					step($chatid,"getpmhar");
												$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft2);
										}
										}
					elseif($step=="sendadminhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createsendadminhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `moh$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','sendadmin','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','sendadmin','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
				}
						}elseif($step=="schannelhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createschannelhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','schannel','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','schannel','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="fchannelhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createfchannelhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','fchannel','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','fchannel','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="jostojohar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createjostojohar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','jostojo','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','jostojo','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
				}
						}elseif($step=="searchhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createsearchhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','search','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','search','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="createbothar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createcreatebothar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','createbot','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','createbot','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="deletebothar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createdeletebothar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','deletebot','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','deletebot','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="updatebothar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createupdatebothar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','updatebot','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','updatebot','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="backhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createbackhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','back','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','back','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="changehar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createchangehar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','change','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','change','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="enfjarhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createenfjarhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','enfjar','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','enfjar','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="bartarinhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createbartarinhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','bartarin','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','bartarin','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="coinhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createcoinhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','coin','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','coin','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="matntakihar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createtakihar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntaki','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntaki','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="phphar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createphphar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','php','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','php','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="getmatntakihar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creategettakihar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getmatntaki','har','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
									$edithar =	getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getmatntaki','har',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
			$edithar =	getOther($chatid);
			step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="getphphar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetphphar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getphp','har','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
									$edithar =	getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getphp','har',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
			$edithar =	getOther($chatid);
			step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="matnchandhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createchandhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnchand','har','$vb','[]'");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnchand','har','[]'");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="matntartibhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createtartibhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntartib','har','$vb','[]'");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntartib','har','[]'");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="matnrandhar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createrandhar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnrand','har','$vb','[]'");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				setDokmetext($text,"");
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnrand','har','[]'");
								step($chatid,'panel');
								setDokmetext($text,"");
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="Apihar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createapihar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','Api','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','Api','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
				}
						}elseif($step=="getApihar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetapihar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getApi','har','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
									$edithar =	getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getApi','har',''");
									$ggb =  $dokme["getmohtava"];
       if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
			$edithar =	getOther($chatid);
			step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="rsshar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creatersshar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','rss','har','$vb',''");
								$edithar =getOther($chatid);
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','rss','har',''");
								step($chatid,'panel');
								$edithar =getOther($chatid);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="newdokmehar"){
									if($text=="برگشت↪"){
				step($chatid,"keyhar");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createnewdokmehar"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','newdokme','har','$vb',''");
									$edithar =	getOther($chatid);
										step($chatid,'panel');
				$keytest='[{"text":""}],[{"text":""}]';
$dataa=$keytest;
setDokmetext($text,$keytest);
$olddokme=getDokmetext($vb);
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','newdokme','har',''");
									$edithar =	getOther($chatid);
			step($chatid,"panel");
				$keytest='[{"text":""}],[{"text":""}]';
$dataa=$keytest;
setDokmetext($text,$keytest);
				$olddokme = getKeyboard();
				$newdokme = str_replace('{"text":"'.$edithar.'"}','{"text":"'.$edithar.'"},{"text":"'.$text.'"}',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
						}elseif($step=="keypain"){
							if($text=="برگشت↪"){
				step($chatid,"panel");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keypanel);
				}elseif($text=="متن تکی🔰"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtakipain");
						}
					step($chatid,"matntakipain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻استفاده از php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createphppain");
						}
					step($chatid,"phppain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="متن چندتایی🔠"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchandpain");
						}
						step($chatid,"matnchandpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن به ترتیب⏬"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createtartibpain");
						}
						step($chatid,"matntartibpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="متن رندوم💈"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createrandpain");
						}
						step($chatid,"matnrandpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Api❇"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createapipain");
						}
						step($chatid,"Apipain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="استفاده از Rss📃"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creatersspain");
						}
						step($chatid,"rsspain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}

elseif($text=="ساخت دکمه ی دیگر🆕"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createnewdokmepain");
						}
							step($chatid,"newdokmepain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
							}elseif($text=="گرفتن محتوا از کاربر📩"){
								$gvb = getallvalue("moh","dokme");
								foreach($gvb as $xc){
									deletevalue("moh","dokme",$xc);
									}
								step($chatid,"getpmpain");
								$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft);
								}elseif($text=="استفاده از دکمه های سیستمی⚙"){
								step($chatid,"systempain");
								$txt="یک گزینه را انتخاب کنید";
								sm($chatid,$txt,$keysistemi);
								}
							}elseif($step=="systempain"){
										if($text=="برگشت↪"){
													step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="دکمه ی جست و جو🔎"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createjostojopain");
						}
					step($chatid,"jostojopain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}if($text=="دکمه جست و جو در کانال📚"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsearchpain");
						}
					step($chatid,"searchpain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="دکمه ساخت ربات🤖"){
						/*
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcreatebotpain");
						}
					step($chatid,"createbotpain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه اپدیت ربات♻"){
					/*
	$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createupdatebotpain");
						}
					step($chatid,"updatebotpain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه حذف ربات🚯"){
					/*
	$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createdeletebotpain");
						}
					step($chatid,"deletebotpain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					*/
					sm($chatid,"این قابلیت در حال حاضر غیرفعال میباشد⛔");
					}elseif($text=="دکمه ی بازگشت به خانه🏠"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbackpain");
						}
						step($chatid,"backpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="انتقال امتیاز♻"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createchangepain");
						}
						step($chatid,"changepain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="دکمه سیستمی بازی انفجار💣"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createenfjarpain");
						}
						step($chatid,"enfjarpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های زیرمجموعه👥"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createbartarinpain");
						}
						step($chatid,"bartarinpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="نمایش برترین های امتیاز⚜"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createcoinpain");
						}
						step($chatid,"coinpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}
					}
							}elseif($step=="getpmpain"){
										if($text=="برگشت↪"){
													step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if($text=="نمایش متن✏"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategettakipain");
						}
					step($chatid,"getmatntakipain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="💻نمایش خروجی php"){
						$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetphppain");
						}
					step($chatid,"getphppain");
					$txt="لطفا اسم دکمه را وارد کنید :";
					sm($chatid,$txt,$keyback);
					}elseif($text=="نمایش Api✳"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"creategetapipain");
						}
						step($chatid,"getApipain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به کانال📨"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createschannelpain");
						}
						step($chatid,"schannelpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="فوروارد به کانال🔖"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createfchannelpain");
						}
						step($chatid,"fchannelpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="ارسال به ادمین👤"){
							$vb = getOther2($chatid);
						if(getEditer($vb)=="newdokme"){
						setEditer($vb,"createsendadminpain");
						}
						step($chatid,"sendadminpain");
						$txt="لطفا اسم دکمه را وارد کنید :";
							sm($chatid,$txt,$keyback);
						}elseif($text=="گرفتن محتوای دیگر📩"){
						step($chatid,"getpmmohtavapain1");
						$txt="لطفا انتخاب کنید ربات چه چیزی از کاربر دریافت کند؟!";
						sm($chatid,$txt,$keychandmoh);
						}
					}
									}elseif($step=="getpmmohtavapain1"){
										if($text=="برگشت↪️"){
											step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
											}elseif($text=="🔢عدد"){
												setvalue("user","chatid",$chatid,"Other4","addad");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="☎شماره تلفن"){
												setvalue("user","chatid",$chatid,"Other4","number");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📧ایمیل"){
												setvalue("user","chatid",$chatid,"Other4","email");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌐لینک"){
												setvalue("user","chatid",$chatid,"Other4","link");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📽ویدیو نوت"){
												setvalue("user","chatid",$chatid,"Other4","video_note");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="📞مخاطب"){
												setvalue("user","chatid",$chatid,"Other4","contact");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🌏نقشه"){
												setvalue("user","chatid",$chatid,"Other4","location");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔤متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","matn");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔡متن انگلیسی"){
												setvalue("user","chatid",$chatid,"Other4","english");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🇮🇷متن فارسی"){
												setvalue("user","chatid",$chatid,"Other4","farsimatn");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔣هرمتنی بجز کاراکتر"){
												setvalue("user","chatid",$chatid,"Other4","hamematn");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط متن انگلیسی و عدد🔢🔡"){
												setvalue("user","chatid",$chatid,"Other4","englishandnumber");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="فقط فوروارد↩"){
												setvalue("user","chatid",$chatid,"Other4","forward");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="غیر فوروارد🔂"){
												setvalue("user","chatid",$chatid,"Other4","noforward");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="(@)یوزرنیم"){
												setvalue("user","chatid",$chatid,"Other4","username");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚡فقط دستور"){
												setvalue("user","chatid",$chatid,"Other4","dastor");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="✴️هرچیزی"){
												setvalue("user","chatid",$chatid,"Other4","hame");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="⚽توپ"){
												setvalue("user","chatid",$chatid,"Other4","ball");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🏀بسکتبال"){
												setvalue("user","chatid",$chatid,"Other4","basket");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎳بولینگ"){
												setvalue("user","chatid",$chatid,"Other4","boling");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎲تاس"){
												setvalue("user","chatid",$chatid,"Other4","dice");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎯دارت"){
												setvalue("user","chatid",$chatid,"Other4","dart");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎰اسلات"){
												setvalue("user","chatid",$chatid,"Other4","eslat");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎥فیلم"){
												setvalue("user","chatid",$chatid,"Other4","film");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🎇عکس"){
												setvalue("user","chatid",$chatid,"Other4","photo");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔊آهنگ"){
												setvalue("user","chatid",$chatid,"Other4","audio");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}elseif($text=="🔆استیکر"){
												setvalue("user","chatid",$chatid,"Other4","sticker");
												step($chatid,"getpmmohtavapain2");
												$txt="لطفا متنی که میخواهید برای کاربر ارسال شود را بفرستید :";
							sm($chatid,$txt,$keyback);
												}
										}elseif($step=="getpmmohtavapain2"){
											if($text=="برگشت↪"){
														step($chatid,"keypain");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}elseif(isset($text)){
					step($chatid,"getpmmohtavapain");
					setvalue("user","chatid",$chatid,"Other5",$text);
					$txt="درصورتی که کاربر محتوای اشتباه برای ربات فرستاد ، ربات در جواب چه بگوید؟!";
					sm($chatid,$txt);
					}
											}
elseif($step=="getpmmohtavapain"){
											if($text=="برگشت↪"){
														step($chatid,"keypain");
														
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(!empty(getallvalue("moh","dokme"))){
					$count = count(getallvalue("moh","dokme"))+1;
					$coon = "TEXT_$count";
					
					}else{
						$coon="TEXT_1";
						}
						$typemoh = getvalue("user","chatid",$chatid,"Other4");
						$textget = getvalue("user","chatid",$chatid,"Other5");
						$sql = "INSERT INTO `moh$userbott`(
						`dokme`,
						`textesh`,
						`textget`,
						`matn`
						) VALUES('$coon','$text','$textget','$typemoh')";
						mysqli_query($con,$sql);
					step($chatid,"getpmpain");
												$txt="لطفا تعیین کنید بعد از گرفتن محتوا از کاربر،ربات چه محتوایی نمایش دهد؟!";
								sm($chatid,$txt,$keydaryaft2);
										}
										}
				elseif($step=="sendadminpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createsendadminpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','sendadmin','pain','$vb',''");
									step($chatid,'panel');
			$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','sendadmin','pain',''");
									step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard( $newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="schannelpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createschannelpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','schannel','pain','$vb',''");
									step($chatid,'panel');
			$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','schannel','pain',''");
									step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard( $newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}	}	
						}elseif($step=="fchannelpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createfchannelpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','fchannel','pain','$vb',''");
									step($chatid,'panel');
			$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','fchannel','pain',''");
									step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard( $newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="getmatntakipain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creategettakipain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getmatntaki','pain','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getmatntaki','pain',''");
									
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
						}		
						}elseif($step=="getphppain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetphppain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getphp','pain','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getphp','pain',''");
									
								if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="jostojopain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createjostojopain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','jostojo','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','jostojo','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
						}		
						}elseif($step=="searchpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createsearchpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','search','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','search','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="createbotpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createcreatebotpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','createbot','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','createbot','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="deletebotpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createdeletebotpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','deletebot','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','deletebot','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="updatebotpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createupdatebotpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','updatebot','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','updatebot','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="backpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createbackpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','back','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','back','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="changepain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createchangepain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','change','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','change','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="enfjarpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createenfjarpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','enfjar','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','enfjar','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="bartarinpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createbartarinpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','bartarin','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','bartarin','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}elseif($step=="coinpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createcoinpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','coin','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','coin','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
						}		
						}	elseif($step=="matntakipain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createtakipain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntaki','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntaki','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
						}		
						}elseif($step=="phppain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createtakipain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','php','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','php','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
						}}		
						}		elseif($step=="matnchandpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createchandpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnchand','pain','$vb','[]'");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnchand','pain','[]'");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
							}	
						}elseif($step=="matntartibpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createtartibpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matntartib','pain','$vb','[]'");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matntartib','pain','[]'");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
							}	
						}	elseif($step=="matnrandpain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createrandpain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','matnrand','pain','$vb','[]'");
							step($chatid,'panel');
							setDokmetext($text,"");
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','matnrand','pain','[]'");
				step($chatid,'panel');
				setDokmetext($text,"");
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
							}	
						}	elseif($step=="Apipain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="createapipain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','Api','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','Api','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
				}
							}elseif($step=="getApipain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creategetapipain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','getApi','pain','$vb',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','getApi','pain',''");
									if(!empty(getallvalue('moh','dokme'))){
									$md = substr(md5($text), 0, 10);
							insert("hashmoh","`hash`,`text`","'$md','$text'");
									$sql = "CREATE TABLE `mohtava$md$userbott` 
 ( 
 `dokme` TEXT,
 `text` TEXT,
 `tedad` TEXT,
 `phone` TEXT,
 `email` TEXT,
 `hame` TEXT,
 `photo` TEXT,
 `matn` TEXT,
 `link` TEXT,
 `textget` TEXT,
 `textesh` TEXT,
 `Other` TEXT
)";
mysqli_query($con,$sql);
									$all=getallvalue('moh','dokme');
									foreach($all as $xb){
										$gettext = getvalue('moh','dokme',$xb,'textget');
										$textesh = getvalue('moh','dokme',$xb,'textesh');
										$matn = getvalue('moh','dokme',$xb,'matn');
										insert("mohtava$md",'`dokme`,`textget`,`textesh`,`matn`',"'$xb','$gettext','$textesh','$matn'");
										}
										}
				step($chatid,'panel');
				$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}}
				}
							}elseif($step=="rsspain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
								$vb = getOther2($chatid);
							if(getEditer($vb)=="creatersspain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','rss','pain','$vb',''");
							step($chatid,'panel');
				$olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								
								}else{
									insert("dok","`dokme`,`nok`,`type`,`text`","'$text','rss','pain',''");
				step($chatid,'panel');
				$olddokme =getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}}
							}
						elseif($step=="newdokmepain"){
									if($text=="برگشت↪"){
				step($chatid,"keypain");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keycreate);
				}else{
					if(isset($text)){
					if(!empty(getDokme($text))){
						sm($chatid,"این دکمه از قبل در ربات ثبت شده است⛔\n\nلطفا یک اسم جدید انتخاب کنید :");
						}else{
							$vb = getOther2($chatid);
							if(getEditer($vb)=="createnewdokmepain"){
								setEditer($vb,null);
								if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`into`,`text`","'$text','newdokme','pain','$vb',''");
									step($chatid,'panel');
				$keytest='[{"text":""}],[{"text":""}]';
setDokmetext($text,$keytest);
    $olddokme = getDokmetext($vb);
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setDokmetext($vb,$newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
								}else{
									if(!isetrow("dok","nok")){
								createrow("dok","dokme","nok","TEXT");
								}
								insert("dok","`dokme`,`nok`,`type`,`text`","'$text','newdokme','pain',''");
				step($chatid,"panel");
				$keytest='[{"text":""}],[{"text":""}]';
				setDokmetext($text,$keytest);
$olddokme = getKeyboard();
				$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$text.'"}],[{"text":""}]',$olddokme);
				setKeyboard($newdokme);
				$txt="دکمه ی شما با موفقیت ساخته شد✅\n\nهم اکنون میتوانید با کلیک روی دکمه به تنظمیات آن وارد شوید و با دکمه ی تعیین مطلب کار دکمه را به پابان برسانید .";
			sm($chatid,$txt,$keypanel);
					}
				}
				}
}	
				}	
						}else{
							if($type=="private"){
								if(getvalue("data","id",1,"pmresanall")=="on"){
									if(empty(getvalue("data","id",1,"pmresantext"))){
					
					
		$ch13="پیام شما ارسال شد✅";
		}else{
	$txtt = getvalue("data","id",1,"pmresantext");
			$ch13 = str_text($txtt,1);
		}
			sm($chatid,$ch13,$keykarbar);
			if(empty(getvalue("data","id",1,"maxwarn"))){
				$maxwarn = 4;
				}else{
					$maxwarn = getvalue("data","id",1,"maxwarn");
					}
					if(empty(getvalue("user","chatid",$chatid,"tedadwarn"))){
				$tedadwarn = 0;
				}else{
					$tedadwarn = getvalue("user","chatid",$chatid,"tedadwarn");
					}
					
			$keym=json_encode([
			'inline_keyboard'=>[
			[["text"=>"مشخصات فرد👦",'callback_data'=>"etlaat$chatid"]],
			[["text"=>"جواب دادن↪","callback_data"=>"Javab$chatid"],["text"=>"بلاک کردن⛔",'callback_data'=>"block$chatid"]],
			[["text"=>"➖","callback_data"=>"manfiwarn$chatid"],["text"=>"$tedadwarn/$maxwarn","callback_data"=>"تعداد اخطار ها"],["text"=>"➕","callback_data"=>"addwarn$chatid"]],
			[["text"=>"خوانده شدن و بستن کیبورد👁","callback_data"=>"reading$chatid-.-$messageid"]],
			]
			]);
			$txt="
			📨یک پیام دریافت شد

⭐️کاربر : <a href='tg://user?id=$chatid'>$firstname</a>
⭐️کد کاربری : $chatid
⭐️متن پیام : 👇👇👇\n$tttm";
bot('sendMessage',[
        'chat_id'=>$admin,
        'text' =>$txt,
        'parse_mode'=>"HTML",
      'reply_markup'=>$keym
        ]);
			$arr = getallvalue("admin","chatid");
			fm($admin,$fromid,$messageid);
			foreach($arr As $key){
   bot('sendMessage',[
        'chat_id'=>$key,
        'text' =>$txt,
        'parse_mode'=>"HTML",
      'reply_markup'=>$keym
        ]);
   fm($key,$chatid,$messageid);
   }
   if(getvalue("data","id",1,"sendtomail")=="on"){
   	if(!empty(getvalue("data","id",2,"sendtomail"))){
   	$to = getvalue("data","id",2,"sendtomail");
    $subject = "یک پیام از ربات @$userbott";
    $message = "$txt\n$text";
    $message = wordwrap($message, 70, "\r\n");
    $header = "From:sender@creat.ir \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";
    
   mail ($to,$subject,$message,$header);
   
  }
  }
									
									}else{
								$txtt=getTxteshtebah();
								$ch13 = str_text($txtt,1);
	$ch13=str_replace("/r/n/r","\n",$ch13);
if(preg_match("/(%)([^\']+)(%)/",$ch13,$m)){
		$k = $m[2];
	$hi=	preg_split("/(%)([^\']+)(%)/",$ch13);
	$ch13 = $hi[0];
		$kei = textToinline("%$k%",$ch13);
		}

			sm($chatid,$ch13,$kei);
								}
							}
							}
						
ToDie();
?>