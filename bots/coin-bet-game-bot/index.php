<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

ob_start();
error_reporting(E_ALL);

date_default_timezone_set('Asia/Tehran'); 


if (!file_exists('setting/setting.json')){
mkdir("setting");
file_put_contents('setting/setting.json', '{"fot":"2","tas":"2","bol":"2","bas":"2","dar":"2","slt":"2","on":"on"}');
}

include 'config.php';

$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],];
$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$ok=false;
foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
$lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower'])); $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
if($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;} if(!$ok) 
        exit("<h1 style='text-align: center;margin-top:30px'>403 @sourcepluss</a></h1>");
$update = json_decode(file_get_contents("php://input"));

if (isset($update->message)) {
$message = $update->message;
$firest = @str_replace([">", "<"], ["&gt;", "&lt;"], $message->from->first_name);
$message_id = $message->message_id;
$text = $message->text;
$chat_id = $message->chat->id;
$tc = $message->chat->type;
$from_id = $message->from->id;
$username = $message->from->username;
}else{
if (isset($update->callback_query)) {
$callback_query = $update->callback_query;
$data = $callback_query->data;
$tc = $callback_query->message->chat->type;
$chat_id = $callback_query->message->chat->id;
$from_id = $callback_query->from->id;
$username = $callback_query->from->username;
$message_id = $callback_query->message->message_id;
$firest = @str_replace([">", "<"], ["&gt;", "&lt;"], $callback_query->from->first_name);
$callbackid = $callback_query->id;
$callback_query     = $update->callback_query;
$text = $callback_query->message->text;
}else{
exit();
}
}
$conn = mysqli_connect($localhost, $user, $password, $name);

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '$from_id' LIMIT 1"));
$block = mysqli_query($conn,"SELECT * FROM `block` WHERE `id` = '$from_id' LIMIT 1");




require 'setting.php';
  $date = jdate('Y/m/d'); 
  $time = jdate('H:i:s'); 
$dataa = json_decode(file_get_contents("setting/setting.json"), true);



require 'admin.php'; 

if (mysqli_num_rows($block) > 0) exit();




$get_Channels = mysqli_query($conn,"SELECT id FROM `channels` LIMIT 100");
foreach ($get_Channels as $ch_ids){
$ch_username20   = $ch_ids["id"];
$channels[]    = $ch_username20;
}
function is_join($id){
global $channels;
$in_ch = [];
                    foreach ($channels as $ch_id){
                    $type = bot("getChatMember" , ["chat_id" => "@$ch_id" , "user_id" => $id]);
                    $type = (is_object($type)) ? $type->result->status : $type['result']['status'];
                    if($type == 'creator' || $type ==  'administrator' || $type ==  'member'){
                    $in_ch[$ch_id] = $type;
}else{
                    $keyboard = [];
                    foreach ($channels as $ch_id)
{
                    $ch_info = bot("getChat", ["chat_id"=>"@$ch_id"]);//get channel
                    $ch_name = (is_object($ch_info)) ? $ch_info->result->title : $ch_info['result']['title'];//channel name
                    $keyboard[] = [['text' => $ch_name, 'url' => "https://t.me/$ch_id"]];
}
$keyboard[]     = [['text'=>'✅ تایید عضویت','callback_data'=>'join']];
$text           = '✅برای حمایت از ما در کانال های زیر عضو شوید';
bot('sendmessage',[
'chat_id'=>$id,
'text'=>"✅برای حمایت از ما در کانال های زیر عضو شوید" ,'parse_mode' => 'MarkDown', 'reply_markup' => 
json_encode(['inline_keyboard' => $keyboard])]);
break;
}}
return true;
}
function check_join($id){
    global $channels;
    $in_ch = [];
    foreach ($channels as $ch_id){
    
                    $type = bot("getChatMember" , ["chat_id" => "@$ch_id" , "user_id" => $id]);
                    $type = (is_object($type)) ? $type->result->status : $type['result']['status'];
                    if($type == 'creator' || $type ==  'administrator' || $type ==  'member'){
                    $in_ch[$ch_id] = $type;
}else{
return false;
break;
}}
return true;
}

if(check_join("$from_id")!='true' && $tc == 'private'){
if($user['id'] != true)
    
is_join("$from_id");
exit;
}
if($data == 'join'){
if(check_join("$from_id")=='true'){
bot('EditMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"⏳",
]);
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"عضویت شما تایید شد.",
'reply_to_message_id'=>$message_id,
'reply_markup'=>$_home
]);
}
if(check_join("$from_id")!='true'){
bot('answercallbackquery', [
'callback_query_id' =>$callbackid,
'text' => "❌شما عضو کانال های ما نشدید",
'show_alert' =>true
]);
}
}

if($dataa['on'] == "off" and !in_array($from_id,$admin)){
 $bot->sendMessage($from_id,"🧑‍🔧ربات جهت آپدیت تا دقایقی خاموش شد"); 
exit();
}

if(strpos($text,'#' ) !== false or strpos($text,"'" ) !== false or strpos($text,"$" ) !== false or strpos($text,"&" ) !== false){ exit;}


$_back = json_encode($media->keyboards(["back"]));
$_dar = json_encode($media->keyboards(["dar"]));
if($text == "/start" || $text == $key['bac']) {
$date = jdate('Y/m/d'); 
 $time = jdate('H:i:s'); 
$datee = jdate('l'); 
   $home = json_encode($media->keyboards(["home1"]));
   $conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	
     $conn->query("UPDATE `user` SET `step2` = 'none' WHERE `id` = '$from_id' LIMIT 1");	

if($user['id'] == true){
 $bot->sendMessage($from_id,$media->text(["start"]),$home); 

}else{
 $bot->sendMessage($from_id,$media->text(["start"]),$home); 
 $conn->query("INSERT INTO `user` (`id`) VALUES ('$from_id')");
$conn->query("INSERT INTO `user` (`id` , `step`) VALUES ('$from_id' , 'none')");
$conn->query("UPDATE `user` SET `time` = '$date-$time-$datee' WHERE `id` = '$from_id' LIMIT 1");
}
}
if($text == $key['startgame']) {
   $home = json_encode($media->keyboards(["home2"]));
 $bot->sendMessage($from_id,$media->text(["startgames"]),$home); 
}
if($text == $key['myacc']){
$bot->sendMessage($from_id, $media->text(["etl",$firest,$from_id, $username,$user['time'],$user['coin']]), $rowgame);
}
if($text == $key['ubnk']){
$bot->sendMessage($from_id, $media->text(["ent", $user['coin']]), $_back);
$conn->query("UPDATE `user` SET `step` = 'sendusercoin' WHERE `id` = '$from_id' LIMIT 1");	
$conn->query("UPDATE `user` SET `step2` = '$text' WHERE `id` = '$from_id' LIMIT 1");		    
}  
if($text == $key['ghu']){
$bot->sendMessage($from_id, $media->text(["lib"]), $_back);
}
if($user['step'] == 'sendusercoin' && $tc == 'private'  and !in_array($text,$arry)) {
if($user['coin'] >= $text){
if( $text>= '1' && is_numeric($text)){
$bot->sendMessage($from_id, $media->text(["senden",$text]), $_back);
$conn->query("UPDATE `user` SET `step` = 'sendcoinnn' WHERE `id` = '$from_id' LIMIT 1");	
}else{
$bot->sendMessage($from_id, $media->text(["noy"]),$_back);
}
}else{            
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}
}
if($user['step'] == 'sendcoinnn' && $tc == 'private'  and !in_array($text,$arry)) {
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM `user` WHERE `id` = '$text' LIMIT 1"));
if(is_numeric($text)){
if($user['id'] == true && $text!=$from_id){
$bot->sendMessage($from_id,$media->text(["pkpro"]),$_back);
}else{
$bot->sendMessage($from_id, $media->text(["jsjns"]),$_back);
}
}else{
$bot->sendMessage($from_id, "<b>⚠️ شناسه کاربری مقصد نامعتبر است و یا ارسال سکه به کاربر موردنظر ممکن نیست.</b>",$_back);
}
}
if($text == $key['support']){
$bot->sendMessage($from_id, "<b>لطفا پیام، سوال، پیشنهاد و یا انتقاد خود را در قالب یک پیام واحد به طور کامل ارسال کنید 👇🏻</b>",$_back);
$conn->query("UPDATE `user` SET `step` = 'sup_rre' WHERE `id` = '$from_id' LIMIT 1");
}
if($user['step'] == "sup_rre"and !in_array($text,$arry)) {
$bot->sendMessage($from_id, "<b>✅ پیام شما با موفقیت ارسال شد منتظر پاسخ پشتیبانی باشید</b>",$_back);
$bot->bot('CopyMessage',['from_chat_id' => $chat_id,'chat_id' => $admin[0],'message_id' => $message_id,'reply_markup'=>json_encode(['inline_keyboard'=>[[['text' => "👤",'url' => "tg://user?id=$from_id"]],[['text' => "❌مسدود",'callback_data' => "block|$from_id"],['text' => "✏️پاسخ",'callback_data' => "icgohc|$from_id"]]]])]);
$conn->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");	

}
if(strpos($data,"block|") !== false){
$exit = explode("|",$data);
$block = $exit[1];
$conn->query("INSERT INTO `block` (`id`) VALUES ('$block')");
$bot->sendMessage($block, "<b>❌حساب شما از طرف مدیریت مسدود شد");
$bot->sendMessage($admin[0], "<b>کاربر [ $block ] از ربات مسدود شد ✅️");
}

if($text == $key['minc']){
$bot->sendMessage($from_id, "<b>💸جهت افزایش موجودی یک روش را انتخاب کنید</b>",$_dar);
}
if($text == $key['tron']){
if (isset($user['trc20_wallet']) && isset($user['trc20_privatekey']) && isset($user['hex'])) {
$bot->sendMessage($from_id, "<code>درحال دریافت نرخ ترون . . .</code>");
    $get = json_decode(file_get_contents("https://mizban-self.ir/api/price-tron.php"), true);
            $fi = $get['TRX'];
     $bot->bot('EditMessageText',[
 'chat_id'=>$chat_id,
 'message_id'=>$message_id + 1,
	'text'=> "<b>💸 نرخ این لحظه ترون $fi تومان میباشد.</b>",
    'parse_mode'=>"html",
    		]);
            $address = $user['trc20_wallet'];
            $pk = $user['trc20_privatekey'];
            $hex = $user['hex'];
$bot->bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"♾ لطفا فقط و فقط میزان ترون مورد نظر خود را به حساب زیر بزنید : 

Wallet : `$address`

ترون خود را به آدرس بالا واریز و سپس بر روی «✅ پرداخت شد» کلیک کنید .

⚠️ اخطار ! از انتقال کمتر از 5 ترون خودداری کنید زیرا موجودی از بین می رود و غیر قابل برگشت است و در هیچ صورتی به حساب شما بازگشت داده نمی شود !",
                'parse_mode' => "MarkDown",
      'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=>'✅ پرداخت شد','callback_data'=>'i_paid_trx']]
]
])
            ]);
        } else {
$bot->sendMessage($from_id, "<code>درحال دریافت نرخ ترون . . .</code>");
    $get = json_decode(file_get_contents("https://mizban-self.ir/api/price-tron.php"), true);
            $fi = $get['TRX'];
     $bot->bot('EditMessageText',[
 'chat_id'=>$chat_id,
 'message_id'=>$message_id + 1,
	'text'=> "<b>💸 نرخ این لحظه ترون $fi تومان میباشد.</b>",
    'parse_mode'=>"html",
    		]);
            $wallet_info = json_decode(file_get_contents("https://mizban-self.ir/api/Tron.php?action=makewallet"), true);
            $address = $wallet_info['wallet'];
            $pk = $wallet_info['private_key'];
            $hex = $wallet_info['hex'];
            $conn -> query("UPDATE `user` SET `trc20_wallet` = '$address',`hex` = '$hex', `trc20_privatekey` = '$pk' WHERE `id` = '$from_id' LIMIT 1");
$bot->bot("sendmessage", [
                'chat_id' => $chat_id, 
                'text' => "♾ لطفا فقط و فقط میزان ترون مورد نظر خود را به حساب زیر بزنید : 

Wallet : `$address`

ترون خود را به آدرس بالا واریز و سپس بر روی «✅ پرداخت شد» کلیک کنید .

⚠️ اخطار ! از انتقال کمتر از 5 ترون خودداری کنید زیرا موجودی از بین می رود و غیر قابل برگشت است و در هیچ صورتی به حساب شما بازگشت داده نمی شود !",
                'parse_mode' => "MarkDown",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=>'✅ پرداخت شد','callback_data'=>'i_paid_trx']]
]
])
            ]);
        }
    } elseif ($data == "i_paid_trx") {
    $details = json_decode(file_get_contents("https://api.trongrid.io/v1/accounts/" . $user['trc20_wallet'] . "/transactions/?only_confirmed=true&only_to=true"), true)['data'];
    $sent = 0;
    $link = "https://mizban-self.ir/api/price-tron.php"; // Dast Nazanid
$result = mysqli_query($conn,"SELECT * FROM metko");
$row = mysqli_fetch_assoc($result);
$start = $row['start'];
    foreach ($details as $detail) {
        if ($detail['raw_data']['contract'][0]['parameter']['value']['amount'] >= 2 * 1000000) {
            $txid = $detail['txID'];
            $payment_detials_in_db = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `trx_payments` WHERE `txid` = '$txid' LIMIT 1"));
            $amount = $detail['raw_data']['contract'][0]['parameter']['value']['amount'] / 1000000;
            $t_amount = $amount;
            file_get_contents("https://mizban-self.ir/api/Tron.php?action=send&address=".$user['trc20_wallet']."&private_key=".$user['trc20_privatekey']."&tx_id=".$user['hex']."&to=".$start."&amount=$t_amount");
            if (!isset($payment_detials_in_db['txid'])) {
                $sent = 1;
                $conn->query("INSERT INTO `trx_payments` (`txid`) VALUES ('$txid');");
                $get = json_decode(file_get_contents($link), true);
      $fi = $get['TRX']; 
                $amount_rial  = $amount * $fi;
                $new_balance = $user['coin'] + $amount_rial;
                $en = $amount_rial;
                $fn = $en + $user['coin'];
                $conn->query("UPDATE `user` SET `coin` = '$en' WHERE `id` = '$from_id' LIMIT 1");
                $bot->bot("sendmessage", [
                    'chat_id' => $from_id,
                    'text' => "✅ تراکنش باموفقیت انجام شد.\n\n نرخ این لحظه ترون $fi میباشد. \n\n تعداد ترون پاریزی $amount میباشد و ارزی دارایی شما$amount_rial میباشد",
                    'parse_mode' => 'markdown',
                ]);
            }
        }
    }
    if (!$sent) {
        $bot->bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "❌ تراکنشی صورت نگرفته است لطفا بعدا امتحان کنید",
            'show_alert' => true
        ]);
    }
}

if($data == "okpayv"){
if($user['coin'] >= $user['step2']){
$bot->bot('editMessagetext',[
'chat_id'=>$from_id,
'message_id'=>$message_id,
'text'=>"✅در خواست شما با موفقیت ارسال شد.\n\n💰تعداد سکه درخواستی : {$user['step2']}\n💸آدرس ولت شما : {$user['step3']} \n\n 📅تاریخ درخواست : $date ⏰ساعت درخواست : $time\n⚠️لطفا از این پیام اسکرین شات بگیرید",
]);
$bot->bot('sendmessage',[
'chat_id'=>$admin[0],
'text'=>"💲ادمین عزیز درخواست برداشت دارید : 


💰تعداد سکه جهت برداشت : {$user['step2']}
💷تعرفه هر سکه : 1500 تومان

➖ ➖ ➖ ➖
📝آدرس ولت شخص : 
`{$user['step3']}`

📅تاریخ درخواست : $date
⏰ساعت درخواست : $time",
  'parse_mode' => "MarkDown",
]);
    $bot->bot('pinChatMessage',['chat_id'=>$chat_id, 'message_id'=>$message_id
    ]);
     $conn->query("UPDATE user SET step2 = 'none' WHERE id = '$from_id' LIMIT 1");
      $conn->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
 $conn->query("UPDATE user SET step3 = 'none' WHERE id = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}
}

if($text == $key['sendc']){
$bot->sendMessage($from_id, "<b>💸تعداد سکه جهت برداشت را وارد کنید.\n\n👈تعرفه هر یک سکه برابر با 1500 تومان می باشد.\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
 $conn->query("UPDATE user SET step = 'diobhgf' WHERE id = '$from_id' LIMIT 1");
}
if($user['step'] == "diobhgf" and !in_array($text,$arry)) {
if($user['coin'] >= $text && $text > 0){
    $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
    $bot->sendMessage($from_id, "<b>💲جهت برداشت لطفا ادرس ولت ترون خود را ارسال کنید.\n\n💲تعداد سکه شما : $text \n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
    $conn->query("UPDATE user SET step = 'ciuvvuvi' WHERE id = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}
}
if($user['step'] == "ciuvvuvi" and !in_array($text,$arry)) {
 $conn->query("UPDATE user SET step3 = '$text' WHERE id = '$from_id' LIMIT 1");
$bot->bot('sendMessage',['chat_id'=>$from_id, 'text'=>"<b>💲تعداد درخواست موجودی : {$user['step2']}\n💲آدرس ولت شما : $text \n\n👈درصورتی که مورد تایید شما هست روی دکمه زیر بزنید.</b>",
  'parse_mode' => "html",
'reply_markup'=>json_encode([
    'inline_keyboard'=>[
[['text'=>'✅ تایید','callback_data'=>'okpayv']]
]
])
]);
 $conn->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");

}
if(strpos($data,"icgohc|") !== false){
	$exit = explode("|",$data);
	$ureply = $exit[1];
	$conn->query("UPDATE `user` SET `step3` = 'reply|$ureply' WHERE `id` = '$from_id' LIMIT 1");	
$bot->sendMessage($from_id, "<b>⭕لطفا پاسخ خود را ارسال کنید.</b>",$_back);
}
if(strpos($user['step3'],"reply|") !== false and !in_array($text,$arry)) {
	$exit = explode("|",$user['step3']);
	$ureply = $exit[1];
	$bot->bot('CopyMessage',[
	'from_chat_id' => $admin[0],
	'chat_id' => $ureply,
	'message_id' => $message_id,
	]);
$bot->sendMessage($from_id, "<b>پیام شما ارسال شد ✅️</b>");
$conn->query("UPDATE `user` SET `step3` = 'none' WHERE `id` = '$from_id' LIMIT 1");	
}
include 'game.php';