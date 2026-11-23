<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$ttti = time();
error_reporting(0);
set_time_limit(0);
include("jdf.php");
define('API_KEY',"PUT-YOUR-BOT-TOKEN");//توکن
$get = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getMe"));
$botid = $get->result->username;
$namebot = $get->result->first_name;
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);$res = curl_exec($ch);

if(curl_error($ch)){var_dump(curl_error($ch));
}else{
return json_decode($res);
}}
function SendMessage($from_id,$text,$keyboard = null){
return bot('sendMessage',[
'chat_id'=>$from_id,
'text'=>$text,
'parse_mode'=>"html",
'disable_web_page_preview'=>"true",
'reply_markup'=>$keyboard]);
}
function DeleteMessage($from_id,$message_id){
bot('deleteMessage',[
'chat_id' => $from_id,
'message_id' => $message_id,
]);
}
function EditMessage($from_id,$meesage_id,$text,$reply_markup){
return bot('editMessageText',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>$text,
'reply_markup'=>$keyboard]);
}
function answerCallbackQuery($callbackid,$text,$true){
	bot('answerCallbackQuery',[
		'callback_query_id' => $callbackid,
		'text' => $text,
		'show_alert' => $true,
		'cache_time'=>5,
]);
}
function rand_string( $length ) {
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
return substr(str_shuffle($chars),0,$length);
}
function deletefolder($path){
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
@$update = json_decode(file_get_contents("php://input"));
if(isset($update)){
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
	}
//••••••••••••••••••••••••••••••••••••••••••••••••••••••••••
if(file_exists("text/moshak.txt")){
$moshak = file_get_contents("text/moshak.txt");
}else{
$moshak = "𝑛𝑜𝑛𝑒";
}
$iddel1 = file_get_contents("id1.txt");
bot('deleteMessage',[
'chat_id' => $moshak,
'message_id' => $iddel1,
]);
unlink("id1.txt");
$iddel2 = file_get_contents("id2.txt");
bot('deleteMessage',[
'chat_id' => $moshak,
'message_id' => $iddel2,
]);
unlink("id2.txt");
$nume = file_get_contents("play/number.txt");
$gift = [2 , 1 , 1 , 3 , 0 , 1 , 1 , 4 , 1 , 5 , 1 , 6 , 1 , 1 , 7 , 1 , 0 , 8 ,1 , 1 , 2 , 2 , 1 , 1 , 3 , 3 , 1 , 1 , 4  , 0 , 4 , 1 , 1 , 5 , 1 , 1 , 6 , 1 , 1 , 7 , 1 , 1 , 8 , 1 , 1 , 9 , 1 , 1 , 0 , 2 , 1 , 3 , 1 , 4 , 1 , 5 , 1 , 6 , 1 , 7 , 1 , 0 , 8 , 1 , 9 , 1 , 10]; 
if(file_exists("play/zaribdast.txt")){
$zaribavaki = file_get_contents("play/zaribdast.txt");
}else{
$zaribavaki = $gift[array_rand($gift)];
}
$zaribsadomsh = rand(00,99);
$m = "$zaribavaki.$zaribsadomsh";
if($zaribavaki == "0"){
$m = "0.00";
}
file_put_contents("play/zarid.txt","$m");
$zarib = file_get_contents("play/zarid.txt");
$random = glob('play/*/game.txt');
foreach($random as $player){
$file = str_replace("play/", "", $player);
$LOL = file_get_contents("play/$file");
$guess = explode("~","$LOL");
$id = $guess[2];
$user = json_decode(file_get_contents("data/$id/$id.json"),true);
if($zarib == $guess[0] || $zarib >= $guess[0]){
$winCoin = $guess[1] * $guess[0];
$CoinWin = $winCoin - $guess[1];
$yourCoin = $user["money"] + $winCoin;
$user["money"] = "$yourCoin";
$mojt = $user['bardasht'] + $winCoin;
$user["bardasht"] = $mojt;
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$id/$id.json",$OutJson);
$a = substr($id, 3, 4);
$idWin = str_replace($a, str_repeat('*', strlen($a)), $id);
$shart = number_format($guess[1]);
$sood = number_format($CoinWin);
$playerWin = "$idWin | $shart COIN | $guess[0] | $sood COIN | 🟩
➖➖➖➖➖";
$NewGame = fopen("play/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Winer = file_get_contents("play/playerWiner.txt");
$hdkwpcl = $Winer + 1;
file_put_contents("play/playerWiner.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("play/CoinWiner.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("play/CoinWiner.txt","$dhhwhxjbd");
unlink("play/$file");
}else{
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$id/$id.json",$OutJson);
$a = substr($id, 3, 4);
$sood = number_format($guess[1]);
$idWin = str_replace($a, str_repeat('*', strlen($a)), $id);
$playerWin = "$idWin | $sood COIN | $guess[0] | 🟥
➖➖➖➖➖";
$NewGame = fopen("play/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Loser = file_get_contents("play/playerLoser.txt");
$hdkwpcl = $Loser + 1;
file_put_contents("play/playerLoser.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("play/CoinLoser.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("play/CoinLoser.txt","$dhhwhxjbd");
unlink("play/$file");
}}
$Loser = file_get_contents("play/playerLoser.txt");
$Loser = $Loser + 0;
$Winer = file_get_contents("play/playerWiner.txt");
$Winer = $Winer + 0;
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$count = $Winer + $Loser;
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$TextLoser = file_get_contents("play/CoinLoser.txt");
$LoserCoin = $TextLoser + 0;
$AllCoinLoser = number_format($LoserCoin);
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$TextWiner = file_get_contents("play/CoinWiner.txt");
$WinerCoin = $TextWiner + 0;
$AllCoinWiner = number_format($WinerCoin);
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$plus = $LoserCoin + $WinerCoin;
$AllCoin = number_format($plus);
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
if(file_exists("play/NewGame.txt")){
$infoGame = file_get_contents("play/NewGame.txt");
}else{
$infoGame = "❌ هیچ بازیکنی در این دست بازی نکرده است";
}
if($count >= 4){
$jsonID = json_decode(file_get_contents("play/id.json"),true);
$randi = $jsonID["id"];
$barande = $randi[array_rand($randi)];
$gorheh = $plus / 100 ;
$user = json_decode(file_get_contents("data/$barande/$barande.json"),true);
$yourCoin = $user["money"] + $gorheh;
$user["money"] = "$yourCoin";
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$barande/$barande.json",$OutJson);
$meqdar = number_format($gorheh);
SendMessage($barande,"🏆 شما برنده $meqdar سکه در قرعه کشی این دست موشک شدید :

• $moshak");
$a = substr($barande, 3, 4);
$barandehe = str_replace($a, str_repeat('*', strlen($a)), $barande);
$infoGorh = "🎁 مقدار سکه قرعه کشی و برنده این دست :  

🏆 $barandehe | $meqdar COIN";
}else{
$infoGorh = "🏆 این دست دارای قرعه کشی نمیباشد";
}
$md‌‌ = md5($zarib);
$hash = hash('ripemd160',$zarib);
$id1 = SendMessage($moshak,"
🔥 ضریب این دست در $zarib بسته شد

➖➖➖➖➖➖➖➖➖➖
💣MD5 : <code>$md‌‌</code>
➖➖➖➖➖➖➖➖➖➖
💣HASH : <code>$hash</code>
➖➖➖➖➖➖➖➖➖➖
👤 آمار بازیکنان این دست :

👥 مجموع بازیکنان این دست : $count بازیکن

💰 مجموع مقدار سکه ها این دست : $AllCoin سکه

📈 مجموع برد این دست ($Winer) نفر : $AllCoinWiner سکه

📉 مجموع باخت این دست ($Loser) نفر : $AllCoinLoser سکه

$infoGorh
➖➖➖➖➖
$infoGame
")->result->message_id;
$id2 = SendMessage($moshak,"
⏳ یک دقیقه تا شروع بازی دست بعد...

 🚀 برای شروع و ثبت بازی جدید وارد ربات اِسلُو بت شوید ( @Slowbet_bot ) بروی ثبت بازی جدید و سپس بر روی بازی موشک کلیک کنید و مراحل ثبت بازی را طی کنید !
",json_encode(['inline_keyboard' => [
[['text' => "👤",'url' => "https://t.me/$botid?start=Slow"],['text' => "🚀",'url' => "https://t.me/$botid?start=Game"]],
[['text' => "🔥 شروع بازی اتوماتیک موشک 🔥",'url' => "https://t.me/$botid?start=Automatic"]],
]]))->result->message_id;
file_put_contents("id1.txt","$id1");
file_put_contents("id2.txt","$id2");
unlink("play/zarid.txt");
unlink("play/id.json");
unlink("play/playerLoser.txt");
unlink("play/playerWiner.txt");
unlink("play/CoinLoser.txt");
unlink("play/CoinWiner.txt");
unlink("play/zaribdast.txt");
unlink("play/NewGame.txt");
unlink("error_log");
echo "OK | TRUE . . . !";
?>