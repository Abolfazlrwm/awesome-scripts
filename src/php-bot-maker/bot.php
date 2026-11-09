<?php
// IR Creator
ob_start();
header("HTTP/1.0 200 OK");
session_start();
ini_set('max_execution_time', 0);
date_default_timezone_set("Asia/Tehran");
unlink("error_log");
///////////////////
define('API_KEY' , "TOKEN"); // توکن ربات
$creator_id_bot = USERID; // ایدی عددی ادمین ربات
//////////////////
$userbott = "USERBOT";
$con=mysqli_connect("localhost","USER","PASSWORD","USERNAME"); // دیتابیس لاین 640 هم ادیت بشه دیتابیس
// Check connection
if (mysqli_connect_errno($con))
 {
   sm($creator_id_bot,"Failed to connect $userbott to MySQL: " . mysqli_connect_error());
   
 }else{
$sql = "CREATE TABLE user 
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
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE group
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
$sql = "CREATE TABLE `update`
(
`noskhe` TEXT,
`update` TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE channel 
 ( 
 chatid INT,
chname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE supergroup 
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

$sql = "CREATE TABLE blocklist 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);
 }
 
 


 
//////////////////
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
curl_setopt($ch, CURLOPT_HEADER, 200);
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
function sm($chatid,$text,$keyboard=null,$parse_mode= 'Html',$disable_web_page_preview=false){
    bot('sendMessage',[
        'chat_id'=>$chatid,
        'text' =>$text,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function fm($chatid,$userid,$message){
    bot('ForwardMessage',[
        'chat_id'=>$chatid,
        'from_chat_id'=>$userid,
        'message_id'=>$message
        ]);
}
function validImage($file) {
   $headers = get_headers($file);
   $head ="";
   foreach($headers as $key=>$val){
    if(strpos($val,"Content-Type")!==false){
     $head .= substr($val, 14);
     }
     }
     $cv = explode("/",$head);
     return $cv[0];
     
}
function sdi($chat_id,$file_id){
    bot('sendDice', [
        'chat_id' => $chat_id,
        'emoji' => $file_id,
        ]);
}
function em($chatid,$text,$message,$keyboard=null,$disable_web_page_preview=false,$parse_mode =  'Html'){
    bot('editMessageText',[
        'chat_id'=>$chatid,
        'text'=>$text,
        'message_id'=>$message,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function sp($chat_id,$file_id,$message= null,$reply = null,$parse_mode= "Html"){
    bot('sendPhoto', [
        'chat_id' => $chat_id,
        'photo' => $file_id,
        'caption' =>$message,
'reply_markup'=>$reply,
        'parse_mode' => $parse_mode
        ]);
}
function gregorian_to_jalali ($g_y, $g_m, $g_d,$str){ 
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29); 
 
  
   $gy = $g_y-1600; 
   $gm = $g_m-1; 
   $gd = $g_d-1; 
 
   $g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400); 
 
   for ($i=0; $i < $gm; ++$i) 
      $g_day_no += $g_days_in_month[$i]; 
   if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) 
      /* leap and after Feb */ 
      $g_day_no++; 
   $g_day_no += $gd; 
 
   $j_day_no = $g_day_no-79; 
 
   $j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
   $j_day_no = $j_day_no % 12053; 
 
   $jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */ 
 
   $j_day_no %= 1461; 
 
   if ($j_day_no >= 366) { 
      $jy += div($j_day_no-1, 365); 
      $j_day_no = ($j_day_no-1)%365; 
   } 
 
   for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) 
      $j_day_no -= $j_days_in_month[$i]; 
   $jm = $i+1; 
   $jd = $j_day_no+1; 
 if($str) return $jy.'/'.$jm.'/'.$jd ;
   return array($jy, $jm, $jd); 
} 
 function div($a,$b) { 
    return (int) ($a / $b); 
} 
function typee($file) {
   $headers = get_headers($file);
   $head ="";
   foreach($headers as $key=>$val){
    if(strpos($val,"Content-Type")!==false){
     $head .= substr($val, 14);
     }
     }
     $cv = explode("/",$head);
     return $cv[1];
     
}
function amarcount($type){
 global $con;
$result = mysqli_query($con, "SELECT * FROM $type");
$num_rows = mysqli_num_rows($result);
return $num_rows;
}
function getstep($chatid){
 global $con;
$query = "SELECT * FROM user WHERE chatid='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row['step'];
}
}

function getvalue($table,$roow,$chatid,$row2){
 global $con;
$query = "SELECT * FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row[$row2];
}
}
function getallvalue($table,$row2){
 global $con;
$query = "SELECT * FROM $table ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$data[] = $row[$row2];
}
return $data;
}
function setvalue($table,$row2,$val2,$row1,$val1){
 global $con;
$query = "UPDATE $table SET $row1 = '$val1' WHERE $row2 = '$val2'";
$result = mysqli_query($con,$query);
}
function step($chatid,$step){
 global $con;
$query = "UPDATE user SET step = '$step' WHERE chatid = $chatid";
$result = mysqli_query($con,$query);
}
function deletevalue($table,$row,$val){
	global $con;
	$sql = "DELETE FROM `$table` WHERE `$row`='$val'";
	$result = mysqli_query($con,$sql);
	}
 function isetrow($table,$row){
 global $con;
 $sql = "SELECT EXISTS(SELECT * FROM $table WHERE $row)";
return $result = mysqli_query($con,$sql);
 }
 function isetval($table,$roow,$chatid,$row2){
 global $con;
$query = "SELECT * FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
if($row[$row2] !==null || $row[$row2] !=="" ){
 return true;
 }else{
 return false;
 }
}
}
function iset($table,$row,$val){
 global $con;
 $sql="SELECT EXISTS(SELECT * FROM $table WHERE $row='$val')";
return mysqli_query($con,$sql);
 }
 function createrow($table,$after,$row,$type){
global $con;
$sql ="ALTER TABLE $table ADD $row $type after $after";
$result = mysqli_query($con,$sql);
}
function my_json_decode($s) {
    $s = str_replace(
        array('"',  "'"),
        array('\"', '"'),
        $s
    );
    $s = preg_replace('/(\w+):/i', '"\1":', $s);
    return json_decode(sprintf('{%s}', $s));
}
function getOther($chatid){
$coin = getvalue("user","chatid",$chatid,"Other");
return $coin;
}
function setOther($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other",$text);
return $coin;}
function getCoin($chatid){
$coin = getvalue("user","chatid",$chatid,"emtiaz");
return $coin;
}
function setCoin($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"emtiaz",$text);
return $coin;
}
function Delete($path)
{
    if (is_dir($path) === true)
    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file)
        {
            Delete(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    }

    else if (is_file($path) === true)
    {
        return unlink($path);
    }

    return false;
}
$admin = "$creator_id_bot";
$update = json_decode(file_get_contents('php://input'));
$Message = $update->message;
$messageid = $Message->message_id;
$text = $Message->text;
$chatid = $Message->chat->id;
$fromid = $Message->from->id;
$firstname = $Message->from->first_name;
$username = $Message->from->username;
$chusername = $update->channel_post->chat->id;
$type2= $update->channel_post->chat->type;
$query = $update->callback_query;
$queryid=$query->id;
$querydata=$query->data;
$qmessage = $query->message;
$qname = $query->from->first_name;
$quser = $query->from->username;
$qid = $query->from->id;
$querychatid=$query->message->chat->id;
$gpname = $Message->chat->title;
$gpuser = $Message->chat->username;
$bio = $Message->chat->bio;
$description = $Message->chat->description;
$newmember = $Message->new_chat_members;
$newid = $newmember[0]->id;
$newname = $newmember[0]->first_name;
$newuser = $newmember[0]->username;
$type= $Message->chat->type;
$photo= $Message->photo[0]->file_id;
$video = $Message->video->file_id;
$sticker = $Message->sticker->file_id;
$audio = $Message->audio->file_id;
$document= $Message->document->file_id;
$voice = $Message->voice->file_id;
$dice = $Message->dice->emoji;
$caption = $Message->caption;
$getmebot = bot("getMe");
$idbot = $getmebot->result->id;
$botname = $getmebot->result->first_name;
$botuser = $getmebot->result->username;
$datesh = gregorian_to_jalali(date("Y"),date("m"),date("d"),"/");
$time = date("H:i:s");
$datem = date("Y:m:d");
$step=getstep($chatid);
///////////////////////////////

if($text=="/start"){
 step($chatid,"");
 sm($chatid,"رباتساز تااطلاع ثانوی خاموش می‌باشد‼");
 }elseif($text=="amar"){
 $c = amarcount("user");
 sm($chatid,"amar is $c");
 }elseif($text=="getstep"){
  $get = getstep($chatid);
  sm($chatid,"g is $get");
  }
  elseif($text=="setstep"){
  $set = setstep($chatid,"hi");
  sm($chatid,"g is $set");
  }elseif($text=="getall"){
   $xc = getallvalue("user","chatid");
   sm($chatid,json_encode($xc));
   }elseif($text=="check"){
    if(iset("user","chatid",$chatid)){
     sm($chatid,"yesiset");
     }else{
      sm($chatid,"notisset");
      }
   
    }elseif($text=="update"){
     sm($chatid,"لطفا نسخه ی اپدیت را بفرستید");
     step($chatid,"update");
     }elseif($step=="update"){
      setvalue("user","chatid",$chatid,"Other",$text);
      step($chatid,"update2");
      sm($chatid,"حالا بگید در این اپدیت چه مواردی قرار گرفته هست؟!!");
      }elseif($step=="update2"){
       if($text=="/back"){
        step($chatid,"update");
      sm($chatid,"حالا بگید در این اپدیت چه مواردی قرار گرفته هست؟!!");
        }else{
       $nos = getvalue("user","chatid",$chatid,"Other");
       $sql="INSERT INTO `update`(
       `noskhe`,
       `update`
       ) VALUES('$nos','$text')";
       mysqli_query($con,$sql);
      step($chatid,"");
      sm($chatid,"ثبت شد🔰");
 
       }
       }elseif($text=="deleteup"){
$sl = "DROP TABLE `update`";
mysqli_query($con,$sl);
	sm($chatid,"finish");
}elseif($text=="deletingbot"){
        step($chatid,"deletingbot");
        sm($chatid,"Please Send Username Bot WithOut @ \nFor Finish Send /finish For Me :)");
        }elseif($step=="deletingbot"){
         if($text=="/finish"){
          step($chatid,"");
          sm($chatid,"FiniShed :)");
          }else{
          	$exp = explode("\n",$text);
          foreach($exp as $text){
          	if(preg_match('/\@(.*bot)/i',$text)){
          	preg_match('/\@(.*bot)/i',$text,$maw);
          $text = $maw[1];
          }
          	if(empty(getvalue("amarbot","bot",$text,"bot"))){
          	sm($chatid,"Bot $text Not Found IN List!!!");
          
          }else{
               $drop = "DROP TABLE dok$text";
                              mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE delete$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE eshtrak$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE fileid$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE hash$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE datatype$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE datalist$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE replac$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
              $all=getallvalue("hashmoh$text",'hash');
         foreach($all as $xb){
          $drop = "DROP TABLE mohtava$xb$text";
               mysqli_query($con,$drop);
             }
          $all=getallvalue("hash$text",'hash');
         foreach($all as $xb){
          $drop = "DROP TABLE mohtava$xb$text";
               mysqli_query($con,$drop);
          }
          $drop = "DROP TABLE hashmoh$text";
               mysqli_query($con,$drop);
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE filter$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE del$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE code$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE moh$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE channel$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE robot$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE data$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE pasokh$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE user$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE admin$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE chan$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $drop = "DROP TABLE group$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
                        $drop = "DROP TABLE supergroup$text";
               mysqli_query($con,$drop);
               
            //--------------++++++++++---------------------//
            $idbot = $text;
            $tables=array("del$idbot","dok$idbot","delete$idbot","eshtrak$idbot","fileid$idbot","hash$idbot","datatype$idbot","datalist$idbot","replace$idbot","filter$idbot","del$idbot","code$idbot","moh$idbot","channel$idbot","robot$idbot","data$idbot","pasokh$idbot","likes$idbot","liketext$idbot","copycode$idbot","notoflike$idbot","admin$idbot","chan$idbot","dayamar$idbot","user$idbot","group$idbot","supergroup$idbot","blocklist$idbot");
foreach($tables as $table){
  $drop = "DROP TABLE $table";
  mysqli_query($con,$drop);
}
$creator = getvalue("amarbot","bot",$idbot,"creatorid");
$key = getvalue("user","chatid",$creator,"keyboard");
											if(strpos($key,'"text":"'.$idbot.'"},')){
		$cc = str_replace('{"text":"'.$idbot.'"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setvalue("user","chatid",$creator,"keyboard",$cp);
		}else{
		$cc = str_replace('{"text":"'.$idbot.'"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setvalue("user","chatid",$creator,"keyboard",$cp);
		}
		deletevalue("qw$creator","bot",$idbot);
deletevalue("amarbot","bot",$idbot);
            $del = Delete("BotList/$text");
              sm($chatid,"Deleted Data For @$text\n\nFor Finish /finish Commemd Send For me ");
           }
           }
           }
         }elseif($text=="addcreate"){
         sm($chatid,"لطفا ایدی عددی کاربر را ارسال کنید :");	
       step($chatid,"addcreate");  
}elseif($step=="addcreate"){
$tedad = getvalue("user","chatid",$text,"tedadcreate");
$two = $tedad +1;
setvalue("user","chatid",$text,"tedadcreate",$two);
sm($chatid,"امکان ساخت  ربات دیگر برای کاربر مهیا شد✅");
step($chatid,"");  
}elseif($text=="remcreate"){
         sm($chatid,"لطفا ایدی عددی کاربر را ارسال کنید :");	
       step($chatid,"remcreate");  
}elseif($step=="remcreate"){
$tedad = getvalue("user","chatid",$text,"tedadcreate");
if($tedad != 0){
$two = $tedad -1;
setvalue("user","chatid",$text,"tedadcreate",$two);
}
sm($chatid,"امکان ساخت یک ربات از کاربر گرفته شد✅");
step($chatid,"");  
}elseif($text=="tedadcreate"){
         sm($chatid,"لطفا ایدی عددی کاربر را ارسال کنید :");	
       step($chatid,"tedadcreate");  
}elseif($step=="tedadcreate"){
$tedad = getvalue("user","chatid",$text,"tedadcreate");
sm($chatid,"این کاربر واجب شرایط ساخت $tedad ربات میباشد");
step($chatid,"");  
}elseif($text=="getbot"){
	sm($chatid,"لطفا ایدی عددی کاربر را ارسال کنید :");	
       step($chatid,"getbot");  
	}elseif($step=="getbot"){
		step($chatid,"");
		$list = "لیست ربات ها \n\n";
		$getall = getallvalue("qw$text","bot");
		foreach($getall as $key){
			$list .= "@$key\n";
			}
			sm($chatid,$list);
		}elseif($text=="getmym"){
			sm($chatid,"Your Mym is $messageid");
			}elseif($text=="getpm"){
				step($chatid,"getmp");
				sm($chatid,"Please Send Id");
				}elseif($step=="getmp"){
					step($chatid,"");
				for($x = 0; $x <= 100; $x++){
					fm($creator_id_bot,$creator_id_bot,$text -$x);
					}
					sm($chatid,"finish");
				}
elseif($text=="entqal"){
  sm($chatid,"Please Wait");
$idbot = "IRCreatorBot";
$newconnect = mysqli_connect("localhost","USERNAME","PASSWORD","USERID"); // دیتابیس
if (mysqli_connect_errno($con))
 {
   sm($creator_id_bot,"Failed to connect $idbot to MySQL: " . mysqli_connect_error());
   mysqli_close($con);
 }	
//$tables = mysqli_fetch_array(mysqli_query($dblink1,"SHOW TABLES  "));
//$tables=array("dok$idbot","delete$idbot","eshtrak$idbot","fileid$idbot","hash$idbot","datatype$idbot","datalist$idbot","replace$idbot","filter$idbot","del$idbot","code$idbot","moh$idbot","channel$idbot","robot$idbot","data$idbot","pasokh$idbot","likes$idbot","liketext$idbot","copycode$idbot","notoflike$idbot","admin$idbot","chan$idbot","dayamar$idbot","user$idbot","group$idbot","supergroup$idbot","blocklist$idbot");
$tables = array("dokFilmRaygan_Uploader_Bot","dok$idbot","dokCartoonUploader_Bot");
foreach($tables as $table){
$tableinfo = mysqli_fetch_array(mysqli_query($con,"SHOW CREATE TABLE $table  ")); // get structure from table on server 1
mysqli_query($newconnect," $tableinfo[1] "); // use found structure to make table on server 2
$result = mysqli_query($con,"SELECT * FROM $table  "); // select all content        
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {     
mysqli_query($newconnect,"INSERT INTO $table (".implode(", ",array_keys($row)).") VALUES ('".implode("', '",array_values($row))."')"); // insert one row into new table
    }
}
sm($chatid,"Finish All Transfer Bot :");
 mysqli_close($con); 
 mysqli_close($newconnect);
         
         }elseif($text=="dice"){
$gh = bot("sendDice",[
"chat_id"=>$chatid,
"emoji"=>"⚽️"
]);
sm($chatid,$gh);
}
elseif($text=="updateallbot"){
 $update = json_decode(file_get_contents("update.json"),true);
$update["ok"]="fa";
$update["chatid"]=$chatid;
$update["messageid"]=$messageid;
$update["tedad"]= null;
file_put_contents("update.json", json_encode($update));
sm($chatid,"Started");
}
elseif($text=="deletetable"){
step($chatid,"delete1");
sm($chatid,"please send Tabel Name?");
}elseif($step=="delete1"){
if($text=="/back"){
        step($chatid,"");
      sm($chatid,"ok👌");
        }else{
         sm($chatid,"Please Wait");
$getallbot =getallvalue("amarbot","bot");
foreach($getallbot as $zd){
 $sql="DROP TABLE $text$zd";
 mysqli_query($con,$sql);
 }
 step($chatid,"");
 sm($chatid,"delete All Table $text🙏");
 
}
}elseif($text=="getalllistbot"){
	sm($chatid,"please wait...");
$getallbot =getallvalue("amarbot","bot");
$list="";

for($x= 650; $x <= 700; $x++){
	$zd = $getallbot[$x];
	$cout = count(getallvalue("user$zd","chatid"));
	$file =file_get_contents("BotList/$zd/$zd.php");
	preg_match('/(\$updating\=\")(.*)(\")/',$file,$nos);
 $list .= " @$zd | $cout | ".$nos[2]."\n";
 }
$cvbb = str_split($list, 4095);
      foreach($cvbb as $vm){
      sm($chatid,$vm);
      }
      sm($chatid,"finish");
}elseif($text=="isset"){
     $step = getstep($chatid);
     if(!empty(getvalue("user","chatid",$chatid,"step"))){
      sm($chatid,"really and $step");
      }else{
      sm($chatid,"No");
      }
     }elseif($text=="reset"){
      $set = setstep($chatid,"");
  sm($chatid,"g is $set");
      }elseif($text=="link"){
       $name=base64_encode("/start");
$txt="https://t.me/$userbott?start=$name";
sm($chatid,$txt);
}elseif($text=="addcoin"){
step($chatid,"addcoi");
sm($chatid,"Please Send Chatid ?!");
}elseif($step=="addcoi"){
step($chatid,"addcoi2");
setOther($chatid,$text);
sm($chatid,"Please Send Coin For Added");
}elseif($text=="getcoin"){
step($chatid,"getcoi");
sm($chatid,"Please Send Chatid ?!");
}elseif($step=="getcoi"){
step($chatid,"");
$get = getCoin($text);
sm($chatid,"Is Coin : $get");
}elseif($step=="addcoi2"){
step($chatid,"");
$id = getOther($chatid);
$get = getCoin($id);
$get = $get + $text;
setCoin($id,$get);
sm($chatid,"Addded :)");
}elseif($text=="amarkoli"){
$allbot = getallvalue("amarbot","bot");
$x = 0;
$count = count($allbot);
 sm($chatid,"amar kol in $count");
}else{
       sm($chatid,typee($text));
       sm($chatid,validImage($text));
       }
       
  
 //////////////////////////////////

?>