<?php
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
if(file_exists("text/DartChannel.txt")){
$DartChannel = file_get_contents("text/DartChannel.txt");
}else{
$DartChannel = "𝑛𝑜𝑛𝑒";
}
if(!file_exists('dart/delete')){
mkdir("dart/delete");
}
$X = glob('dart/delete/*');
foreach($X as $delete){
$file = str_replace("dart/", "", $delete);
$file = file_get_contents("dart/$file");
bot('deleteMessage',[
'chat_id' => $DartChannel,
'message_id' => $file,
]);
unlink("dart/$file");
}
$dice = bot('sendDice',[
'chat_id' => $DartChannel,
'emoji'=> '🎯',
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "♻️ در حال بازی ...", 'callback_data' => "none"]]]
])
]);
$value = $dice->result->dice->value;
$messageid = $dice->result->message_id;
file_put_contents("dart/delete/$messageid.txt","$messageid");
if($value == 6){
bot('editMessageReplyMarkup',[
'chat_id'=>$DartChannel,
'message_id'=>$messageid,
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "♻️ تکرار دارت . . . !", 'callback_data' => "none"]]]
])
]);
$dice = bot('sendDice',[
'chat_id' => $DartChannel,
'emoji'=> '🎯',
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "♻️ در حال بازی ...", 'callback_data' => "none"]]]
])
]);
$value = $dice->result->dice->value;
$messageid = $dice->result->message_id;
file_put_contents("dart/delete/$messageid.txt","$messageid");
}
if($value == 4 or $value == 2){
bot('editMessageReplyMarkup',[
'chat_id'=>$DartChannel,
'message_id'=>$messageid,
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "🎯 دارت قرمز . . . !", 'callback_data' => "none"]]]
])
]);
}
if($value == 5 or $value == 3){
bot('editMessageReplyMarkup',[
'chat_id'=>$DartChannel,
'message_id'=>$messageid,
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "🎯 دارت سفید . . . !", 'callback_data' => "none"]]]
])
]);
}
if($value == 1){
bot('editMessageReplyMarkup',[
'chat_id'=>$DartChannel,
'message_id'=>$messageid,
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => "🎯 دارت بیرون . . . !", 'callback_data' => "none"]]]
])
]);
}
file_put_contents("dart/value.txt",$value);
$random = glob('dart/*/game.txt');
foreach($random as $player){
$file = str_replace("dart/", "", $player);
$LOL = file_get_contents("dart/$file");
$guess = explode("~","$LOL");
$id = $guess[2];
$user = json_decode(file_get_contents("data/$id/$id.json"),true);
if($guess[0] == "Dartred"){
if($value == 4 or $value == 2){
$winCoin = $guess[1] * 1.5;
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
$shart = number_format("$guess[1]");
$sood = number_format("$CoinWin");
$playerWin = "$idWin | $shart COIN | Red darts | $sood COIN | 🟩
➖➖➖➖➖";
$NewGame = fopen("dart/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Winer = file_get_contents("dart/playerWiner.txt");
$hdkwpcl = $Winer + 1;
file_put_contents("dart/playerWiner.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("dart/CoinWiner.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("dart/CoinWiner.txt","$dhhwhxjbd");
unlink("dart/$file");
}else{
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$id/$id.json",$OutJson);
$a = substr($id, 3, 4);
$sood = number_format("$guess[1]");
$idWin = str_replace($a, str_repeat('*', strlen($a)), $id);
$playerWin = "$idWin | $sood COIN | Red darts | 🟥
➖➖➖➖➖";
$NewGame = fopen("dart/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Loser = file_get_contents("dart/playerLoser.txt");
$hdkwpcl = $Loser + 1;
file_put_contents("dart/playerLoser.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("dart/CoinLoser.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("dart/CoinLoser.txt","$dhhwhxjbd");
unlink("dart/$file");
}
}
if($guess[0] == "Dartwhite"){
if($value == 5 or $value == 3){
$winCoin = $guess[1] * 1.5;
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
$shart = number_format("$guess[1]");
$sood = number_format("$CoinWin");
$playerWin = "$idWin | $shart COIN | White darts | $sood COIN | 🟩
➖➖➖➖➖";
$NewGame = fopen("dart/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Winer = file_get_contents("dart/playerWiner.txt");
$hdkwpcl = $Winer + 1;
file_put_contents("dart/playerWiner.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("dart/CoinWiner.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("dart/CoinWiner.txt","$dhhwhxjbd");
unlink("dart/$file");
}else{
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$id/$id.json",$OutJson);
$a = substr($id, 3, 4);
$sood = number_format("$guess[1]");
$idWin = str_replace($a, str_repeat('*', strlen($a)), $id);
$playerWin = "$idWin | $sood COIN | White darts | 🟥
➖➖➖➖➖";
$NewGame = fopen("dart/NewGame.txt", "a") or die("Unable to open file!");
fwrite($NewGame, "$playerWin\n");
fclose($NewGame);
$Loser = file_get_contents("dart/playerLoser.txt");
$hdkwpcl = $Loser + 1;
file_put_contents("dart/playerLoser.txt","$hdkwpcl");
$xhshjdbx = file_get_contents("dart/CoinLoser.txt");
$dhhwhxjbd = $xhshjdbx + $guess[1];
file_put_contents("dart/CoinLoser.txt","$dhhwhxjbd");
unlink("dart/$file");
}
}
}
$Loser = file_get_contents("dart/playerLoser.txt");
$Loser = $Loser + 0;
$Winer = file_get_contents("dart/playerWiner.txt");
$Winer = $Winer + 0;
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$count = $Winer + $Loser;
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$TextLoser = file_get_contents("dart/CoinLoser.txt");
$LoserCoin = $TextLoser + 0;
$AllCoinLoser = number_format("$LoserCoin");
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$TextWiner = file_get_contents("dart/CoinWiner.txt");
$WinerCoin = $TextWiner + 0;
$AllCoinWiner = number_format("$WinerCoin");
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
$plus = $LoserCoin + $WinerCoin;
$AllCoin = number_format("$plus");
// •°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°•°• \\
if(file_exists("dart/NewGame.txt")){
$infoGame = file_get_contents("dart/NewGame.txt");
}else{
$infoGame = "❌ هیچ بازیکنی در این دست بازی نکرده است";
}
if($count >= 4){
$jsonID = json_decode(file_get_contents("dart/id.json"),true);
$randi = $jsonID["id"];
$barande = $randi[array_rand($randi)];
$gorheh = $plus / 100 ;
$user = json_decode(file_get_contents("data/$barande/$barande.json"),true);
$yourCoin = $user["money"] + $gorheh;
$user["money"] = "$yourCoin";
$user["step"] = "none";
$OutJson = json_encode($user,true);
file_put_contents("data/$barande/$barande.json",$OutJson);
$meqdar = number_format("$gorheh");
SendMessage($barande,"🏆 شما برنده $meqdar سکه در قرعه کشی این دست موشک شدید :

• $DartChannel");
$a = substr($barande, 3, 4);
$barandehe = str_replace($a, str_repeat('*', strlen($a)), $barande);
$infoGorh = "🎁 مقدار سکه قرعه کشی و برنده این دست :  

🏆 $barandehe | $meqdar COIN";
}else{
$infoGorh = "🏆 این دست دارای قرعه کشی نمیباشد";
}
$msgid1 = SendMessage($DartChannel,"
➖➖➖➖➖➖➖➖➖➖
👤 آمار بازیکنان این دست دارت گروهی :

👥 مجموع بازیکنان این دست : $count بازیکن

💰 مجموع مقدار سکه ها این دست : $AllCoin سکه

📈 مجموع برد این دست ($Winer) نفر : $AllCoinWiner سکه

📉 مجموع باخت این دست ($Loser) نفر : $AllCoinLoser سکه

$infoGorh
➖➖➖➖➖
$infoGame
")->result->message_id;
$msgid2 = SendMessage($DartChannel,"
⏳ یک دقیقه تا شروع بازی دست بعدی دارت گروهی . . .

 🎯 برای شروع و ثبت بازی جدید وارد ربات اِسلُو بت شوید ( @Slowbet_bot ) بروی ثبت بازی جدید و سپس بر روی بازی گروهی دارت کلیک کنید و مراحل ثبت بازی را طی کنید !
",json_encode(['inline_keyboard' => [
[['text' => "👤",'url' => "https://t.me/$botid?start=SlowDart"],['text' => "🎯",'url' => "https://t.me/$botid?start=Dart"]],
[['text' => "🔥 شروع بازی اتوماتیک دارت 🔥",'url' => "https://t.me/$botid?start=DartAuto"]],
]]))->result->message_id;
file_put_contents("dart/delete/$msgid1.txt","$msgid1");
file_put_contents("dart/delete/$msgid2.txt","$msgid2");
unlink("dart/value.txt");
unlink("dart/id.json");
unlink("dart/playerLoser.txt");
unlink("dart/playerWiner.txt");
unlink("dart/CoinLoser.txt");
unlink("dart/CoinWiner.txt");
unlink("dart/NewGame.txt");
unlink("error_log");
echo "OK | TRUE . . . !";
?>