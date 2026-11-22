<?php
error_reporting(0);
ini_set( "log_errors","Off" );
ignore_user_abort(true);
date_default_timezone_set('Asia/Tehran');
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'], // literally 149.154.160.0/20
['lower' => '91.108.4.0',    'upper' => '91.108.7.255'],    // literally 91.108.4.0/22
];

$ip_dec = (float) sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));


foreach ($telegram_ip_ranges as $telegram_ip_range) if (!$ok) {
    $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_range['lower']));
    $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_range['upper']));
    if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) $ok=true;
}
#Edit

#__________________________________________________________________#

$token      = "[*[TOKEN]*]"; #bot token
$channel = file_get_contents("ch.txt"); #username Channel bedon @
$admin    = [*[ADMIN]*]; #Admin
$idbot      = "[*[User]*]"; #IDbot bedon @
#__________________________________________________________________#
if(!file_exists("ch.txt")){
file_put_contents("ch.txt", '');
}
define('API_KEY',"$token");
//##//##//##//##//##//##//##//
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
function EditMessage($chat_id,$message_id,$text,$mode,$keyboard){
return bot('EditMessageText',[
'chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>$text,
'parse_mode'=>$mode,
'reply_markup'=>$keyboard
]);    
}
function pinChatMessage($chat_id){
bot('pinChatMessage',[
'chat_id'=>$chat_id,
]);
}
function delete_message($chat_id , $message_id) {
    return bot('deleteMessage' , [
        'chat_id'    => $chat_id,
        'message_id' => $message_id
    ]);
}
function sendmessage($chat_id,$text){
bot('sendMessage',['chat_id'=>$chat_id,'text'=>$text,'parse_mode'=>"html"]);
}
mkdir('data');
//Variables
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$chat_id1 = $message->forward_from->id;
$chat_id2 = $message->forward_from_chat->id;
$text = $message->text;
$tc = $message->chat->type;
$message_id = $message->message_id;
$first_name = $message->from->first_name;
$from_id = $message->from->id;
$first = $message->from->first_name;
$first1 = $message->forward_from->first_name;
$last = $message->from->last_name;
$last1 = $message->forward_from->last_name;
$username = $message->from->username;
$username1 = $message->forward_from->username;
$username2 = $message->forward_from_chat->username;
$first2 = $update->callback_query->message->chat->first_name;
$last2 = $update->callback_query->message->chat->last_name;
$chatid = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$message_id2 = $update->callback_query->message->message_id;
$bot = file_get_contents("data/bot.txt");
$setup = file_get_contents("data/$chatid/setup.txt");
//
$message_id2 = $update->callback_query->message->message_id;
$chatid = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
//
if(!file_exists("data/$chat_id/setup.txt")){
file_put_contents("data/$chat_id/setup.txt", 'none');
}
if(!file_exists("data/$chat_id/dr.txt")){
file_put_contents("data/$chat_id/dr.txt", '0');
}
if(!file_exists("data/dr.txt")){
file_put_contents("data/dr.txt", '0');
}
if(!file_exists("data/$chat_id/txt.txt")){
file_put_contents("data/$chat_id/txt.txt", '');
}
if(!file_exists("data/bot.txt")){
file_put_contents("data/bot.txt", 'on');
}
$trxt = file_get_contents("data/$chatid/txt.txt");
$photo = $message->photo;
$tedadmsg = $update->message->message_id;
$text2 = str_replace(' ','+',$text);
$link = json_decode(file_get_contents("http://api.codebazan.ir/font/?text=$trxt"),true);
$link2 = $link["result"];
$and1 = $link2['1'];
$and2 = $link2['2'];
$and3 = $link2['3'];
$and4 = $link2['4'];
$and5 = $link2['5'];
$and6 = $link2['6'];
$and7 = $link2['7'];
$and8 = $link2['8'];
$and9 = $link2['9'];
$and10 = $link2['10'];
$and11 = $link2['11'];
$and12 = $link2['12'];
$and13 = $link2['13'];
$and14 = $link2['14'];
$and15 = $link2['15'];
$and16 = $link2['16'];
$and17 = $link2['17'];
$and18 = $link2['18'];
$and19 = $link2['19'];
$and20 = $link2['20'];
$and21 = $link2['21'];
$and22 = $link2['22'];
$and23 = $link2['23'];
$and24 = $link2['24'];
$and25 = $link2['25'];
$and26 = $link2['26'];
$and27 = $link2['27'];
$and28 = $link2['28'];
$and29 = $link2['29'];
$and30 = $link2['30'];
$and31 = $link2['31'];
$and32 = $link2['32'];
$and33 = $link2['33'];
$and34 = $link2['34'];
$and35 = $link2['35'];
$and36 = $link2['36'];
$and37 = $link2['37'];
$and38 = $link2['38'];
$and39 = $link2['39'];
$and40 = $link2['40'];
$and41 = $link2['41'];
$and42 = $link2['42'];
$and43 = $link2['43'];
$and44 = $link2['44'];
$and45 = $link2['45'];
$and46 = $link2['46'];
$and47 = $link2['47'];
$and48 = $link2['48'];
$and49 = $link2['49'];
$and50 = $link2['50'];
$and51 = $link2['51'];
$and52 = $link2['52'];
$and53 = $link2['53'];
$and54 = $link2['54'];
$and55 = $link2['55'];
$and56 = $link2['56'];
$and57 = $link2['57'];
$and58 = $link2['58'];
$and59 = $link2['59'];
$and60 = $link2['60'];
$and61 = $link2['61'];
$and62 = $link2['62'];
$and63 = $link2['63'];
$and64 = $link2['64'];
$and65 = $link2['65'];
$and66 = $link2['66'];
$and67 = $link2['67'];
$and68 = $link2['68'];
$and69 = $link2['69'];
$and70 = $link2['70'];
$and71 = $link2['71'];
$and72 = $link2['72'];
$and73 = $link2['73'];
$and74 = $link2['74'];
$and75 = $link2['75'];
$and76 = $link2['76'];
$and77 = $link2['77'];
$and78 = $link2['78'];
$and79 = $link2['79'];
$and80 = $link2['80'];
$and81 = $link2['81'];
$and82 = $link2['82'];
$and83 = $link2['83'];
$and84 = $link2['84'];
$and85 = $link2['85'];
$and86 = $link2['86'];
$and87 = $link2['87'];
$and88 = $link2['88'];
$and89 = $link2['89'];
$and90 = $link2['90'];
$and91 = $link2['91'];
$and92 = $link2['92'];
$and93 = $link2['93'];
$and94 = $link2['94'];
$and95 = $link2['95'];
$and96 = $link2['96'];
$and97 = $link2['97'];
$and98 = $link2['98'];
$and99 = $link2['99'];
$and100 = $link2['100'];
$and101 = $link2['101'];
$and102 = $link2['102'];
$and103 = $link2['103'];
$and104 = $link2['104'];
$and105 = $link2['105'];
$and106 = $link2['106'];
$and107 = $link2['107'];
$and108 = $link2['108'];
$and109 = $link2['109'];
$and110 = $link2['110'];
$and111 = $link2['111'];
$and112 = $link2['112'];
$and113 = $link2['113'];
$and114 = $link2['114'];
$and115 = $link2['115'];
$and116 = $link2['116'];
$and117 = $link2['117'];
$and118 = $link2['118'];
$and119 = $link2['119'];
$and120 = $link2['120'];
$and121 = $link2['121'];
$and122 = $link2['122'];
$and123 = $link2['123'];
$and124 = $link2['124'];
$and125 = $link2['125'];
$and126 = $link2['126'];
$and127 = $link2['127'];
$and128 = $link2['128'];
$and129 = $link2['129'];
$and130 = $link2['130'];
$and131 = $link2['131'];
$and132 = $link2['132'];
$and133 = $link2['133'];
$and134 = $link2['134'];
$and135 = $link2['135'];
$and136 = $link2['136'];
$and137 = $link2['137'];
$and138 = $link2['138'];
//
//
$start = "Hi 🧛 [$chat_id](tg://openmessage?user_id=$from_id) * Welcom To Font CR BoT *";
$ky = json_encode(['inline_keyboard'=>[
[['text' => "A",'callback_data'=> "A"],['text' => "B",'callback_data'=> "B"],['text' => "C",'callback_data'=> "C"],['text' => "D",'callback_data'=> "D"]],
[['text' => "E",'callback_data'=> "E"],['text' => "F",'callback_data'=> "F"],['text' => "G",'callback_data'=> "G"],['text' => "H",'callback_data'=> "H"]],
[['text' => "i",'callback_data'=> "i"],['text' => "j",'callback_data'=> "j"],['text' => "k",'callback_data'=> "k"],['text' => "L",'callback_data'=> "L"]],
[['text' => "M",'callback_data'=> "M"],['text' => "N",'callback_data'=> "N"],['text' => "O",'callback_data'=> "O"],['text' => "P",'callback_data'=> "P"]],
[['text' => "Q",'callback_data'=> "Q"],['text' => "R",'callback_data'=> "R"],['text' => "S",'callback_data'=> "S"],['text' => "T",'callback_data'=> "T"]],
[['text' => "W",'callback_data'=> "W"],['text' => "X",'callback_data'=> "X"],['text' => "Y",'callback_data'=> "Y"],['text' => "Z",'callback_data'=> "Z"]],
[['text' => "❌ | Del",'callback_data'=> "del"],['text' => "✅ | Ok",'callback_data'=> "Ok"]],
[['text' => "🔙 | Back menu",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
]);
$kyy = json_encode(['inline_keyboard'=>[
[['text' => "♻️ | ساخت فونت",'callback_data'=> "cr"]],
[['text' => "👤 | حساب کاربری",'callback_data'=> "user"],['text' => "💻 | پشتیبانی",'callback_data'=> "vip"]],
], 'resize_keyboard' => true,
]);
//
$message_id2 = $update->callback_query->message->message_id;
$chatid = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;
$forchannel = json_decode(file_get_contents("https://api.telegram.org/bot$token/getChatMember?chat_id=@$channel&user_id=".$from_id));
$tch = $forchannel->result->status;

if($tch != "creator" and $tch != "administrator" and $tch != "member"){
if($channel != ""){
$start = "";
}}
if($text == "$text" and $bot == "off"){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"
ربات خاموش میباشد ❌
",
'reply_markup'=> json_encode(['KeyboardRemove'=>[
],'remove_keyboard'=>true])
]);
}
if($text == '/start' and $bot == "on"){
$chat_id2 = str_replace("-100","g","$chat_id");
file_put_contents("data/$chat_id/setup2.txt","");
file_put_contents("data/$chat_id/setup.txt","none");
file_put_contents("data/$chatid/txt.txt","");
mkdir("data/$chat_id");
bot('sendMessage',[
'chat_id'=>$chat_id2,
'text'=>"
$start
",
'parse_mode'=>"markdown",
'reply_markup'=> $kyy
]);
}
if(!file_exists("data/$from_id/step.txt")) {
file_put_contents("data/$from_id/step.txt", "none");
$myfile2 = fopen("allmember.txt", "a") or die("Unable to open file!");
fwrite($myfile2,"\n$from_id");
fclose($myfile2);
bot('pinChatMessage',[
'chat_id'=>$chat_id,
'message_id'=> $message_id + 1,
]);
}
if($data == "del"){
file_put_contents("data/$chatid/txt.txt","");
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : 

ok
",
'reply_markup'=> $ky
]);
}
if($data == "cr"){
file_put_contents("data/$chatid/txt.txt","");
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt
",
'reply_markup'=> $ky
]);
}
if($data == "A"){
file_put_contents("data/$chatid/txt.txt","$trxt".'a');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt a
",
'reply_markup'=> $ky
]);
}

if($data == "B"){
file_put_contents("data/$chatid/txt.txt","$trxt".'b');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt b
",
'reply_markup'=> $ky
]);
}
if($data == "C"){
file_put_contents("data/$chatid/txt.txt","$trxt".'c');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt c
",
'reply_markup'=> $ky
]);
}
if($data == "D"){
file_put_contents("data/$chatid/txt.txt","$trxt".'d');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt d
",
'reply_markup'=> $ky
]);
}
//
if($data == "E"){
file_put_contents("data/$chatid/txt.txt","$trxt".'e');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt e
",
'reply_markup'=> $ky
]);
}

if($data == "F"){
file_put_contents("data/$chatid/txt.txt","$trxt".'f');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt f
",
'reply_markup'=> $ky
]);
}
if($data == "G"){
file_put_contents("data/$chatid/txt.txt","$trxt".'g');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt g
",
'reply_markup'=> $ky
]);
}
if($data == "H"){
file_put_contents("data/$chatid/txt.txt","$trxt".'h');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt h
",
'reply_markup'=> $ky
]);
}
if($data == "i"){
file_put_contents("data/$chatid/txt.txt","$trxt".'i');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt i
",
'reply_markup'=> $ky
]);
}

if($data == "j"){
file_put_contents("data/$chatid/txt.txt","$trxt".'j');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt j
",
'reply_markup'=> $ky
]);
}
if($data == "k"){
file_put_contents("data/$chatid/txt.txt","$trxt".'k');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt k
",
'reply_markup'=> $ky
]);
}
if($data == "L"){
file_put_contents("data/$chatid/txt.txt","$trxt".'l');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt l
",
'reply_markup'=> $ky
]);
}
//
if($data == "M"){
file_put_contents("data/$chatid/txt.txt","$trxt".'m');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt m
",
'reply_markup'=> $ky
]);
}

if($data == "N"){
file_put_contents("data/$chatid/txt.txt","$trxt".'n');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt n
",
'reply_markup'=> $ky
]);
}
if($data == "O"){
file_put_contents("data/$chatid/txt.txt","$trxt".'o');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt o
",
'reply_markup'=> $ky
]);
}
if($data == "P"){
file_put_contents("data/$chatid/txt.txt","$trxt".'p');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt p
",
'reply_markup'=> $ky
]);
}

//
if($data == "Q"){
file_put_contents("data/$chatid/txt.txt","$trxt".'q');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt q
",
'reply_markup'=> $ky
]);
}

if($data == "R"){
file_put_contents("data/$chatid/txt.txt","$trxt".'r');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt r
",
'reply_markup'=> $ky
]);
}
if($data == "S"){
file_put_contents("data/$chatid/txt.txt","$trxt".'s');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt s
",
'reply_markup'=> $ky
]);
}
if($data == "T"){
file_put_contents("data/$chatid/txt.txt","$trxt".'t');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt t
",
'reply_markup'=> $ky
]);
}
if($data == "W"){
file_put_contents("data/$chatid/txt.txt","$trxt".'w');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt w
",
'reply_markup'=> $ky
]);
}

if($data == "X"){
file_put_contents("data/$chatid/txt.txt","$trxt".'x');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt x
",
'reply_markup'=> $ky
]);
}
if($data == "Y"){
file_put_contents("data/$chatid/txt.txt","$trxt".'y');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt y
",
'reply_markup'=> $ky
]);
}
if($data == "Z"){
file_put_contents("data/$chatid/txt.txt","$trxt".'z');
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "از طریق کیبورد زیر اسم خود را تایپ کنید ♨️
your text : $trxt z
",
'reply_markup'=> $ky
]);
}
//
if($data == "back"){
file_put_contents("data/$chatid/setup.txt","none");
file_put_contents("data/$chatid/setup2.txt","none");
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "Hi 🧛 [$chatid](tg://openmessage?user_id=$from_id) * Welcom To Font CR BoT *
",
'parse_mode'=>"markdown",
'reply_markup'=> $kyy
]);
	}
if($data == "Ok" and $trxt != ""){
$ted = file_get_contents("data/$chatid/dr.txt");
$ted2 = file_get_contents("data/dr.txt");
$frt = $ted + 1;
$frt2 = $ted2 + 1;
file_put_contents("data/$chatid/dr.txt","$frt");
file_put_contents("data/dr.txt","$frt2");
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست اول ♻️
$and1 
$and2
$and3
$and4
$and5
$and6
$and7
$and8
$and9
$and10
$and11
$and12
$and13
$and14
$and15
$and16
$and17
$and18
$and19
$and20
$and21
$and22
$and23
$and24
$and25
$and26
$and27
$and28
$and29
$and30
$and31
$and32
$and33
",
'parse_mode'=> 'markdown',
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "⏩ | بعدی",'callback_data'=> "next1"]],
[['text' => "❗ | لیست سمبل",'callback_data'=> "sambol"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "next1"){
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست دوم ♻️
$and100
$and101
 $and102 
$and103 
$and104
$and105
$and106
$and107
$and108
$and109
$and110
$and111
$and112
$and113
$and114
$and115
$and116
$and117
$and118
$and119
$and120
$and121
$and122
$and123
$and124
$and125
$and126
$and127
$and128
$and129
$and130
$and131
$and132
$and133
$and134
$and135
$and136
$and137
$and138
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "⏩ | بعدی",'callback_data'=> "next2"],['text' => "🔙 | قبلی",'callback_data'=> "Ok"]],
[['text' => "❗ | لیست سمبل",'callback_data'=> "sambol"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "next2"){
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست سوم ♻️
$and55
$and56
$and57
$and58
$and59
$and60
$and61
$and62
$and63
$and64
$and65
$and66
$and67
$and68
$and69
$and70
$and71
$and72
$and73
$and74
$and75
$and76
$and77
$and78
$and79
$and80
$and81
$and82
$and83
$and84
$and85
$and86
$and87
$and88
$and89
$and90
$and91
$and92
$and93
$and94
$and95
$and96
$and97
$and98
$and99
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "⏩ | بعدی",'callback_data'=> "next3"],['text' => "🔙 | قبلی",'callback_data'=> "next1"]],
[['text' => "❗ | لیست سمبل",'callback_data'=> "sambol"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "next3"){
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست چهارم ♻️
$and34
$and35
$and36
$and37
$and38
$and39
$and40
$and41
$and42
$and43
$and44
$and45
$and46
$and47
$and48
$and49
$and50
$and51
$and52
$and53
$and54 
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "🔙 | قبلی",'callback_data'=> "next2"]],
[['text' => "❗ | لیست سمبل",'callback_data'=> "sambol"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "Ok" and $trxt == ""){
bot('sendMessage',[
'chat_id'=>$chatid,
'text'=>"
فیلد خالی است ❌
",
]);
	}
if($data == "sambol"){
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست سمبل به شرح زیر میباشد ⚡

◯  ◿  𖠳  𖠴  𖠵  𖠶  𖠷  𖠸  𖠹  𖠺  𖠻  𖠼  𖠽  𖠾  𖠿  𖡀  𖡁  𖡂  𖡃  𖡄  𖡅  

𖡆  𖡇  𖡈  𖡉  𖡊  𖡋  𖡌  𖡍  𖡎  𖡏  𖡐  𖡑  𖡒  𖡓  𖡔  𖡕  𖡖  𖡗  𖡘  𖡙  𖡚  𖡛

  𖡜  𖡝  𖡞  𖡟  𖡠  𖡡  𖡢  𖡣  𖡤  𖡥  𖡦  𖡧  𖡨  𖡩  𖡪  𖡫  𖡬  𖡭  𖡮  𖡯  
  
𖡰  𖡱  𖡲  𖡳  𖡴  𖡵  𖡶  𖡷  𖡸  𖡹  𖡺  𖡻  𖡼  𖡽  𖡾  𖡿  𖢀  𖢁  𖢂  𖢃  𖢄 

 𖢅  𖢆  𖢇  𖢈  𖢉  𖢊  𖢋  𖢌  𖢍  𖢎  𖢏  𖢐  𖢑  𖢒  𖢓  𖢔  𖢕  𖢖  𖢗  
 
𖢘  𖢙  𖢚  𖢛  𖢜  𖢝  𖢞  𖢟  𖢠  𖢡  𖢢  𖢣  𖢤  𖢥  𖢦  𖢧  𖢨  𖢩  𖢪  𖢫 

 𖢬  𖢭  𖢮  𖢯  𖢰  𖢍  𖡦  𖣴  𖢩  𖢤  𖣝  𖢔  𖡪  𖨯  𖦆  𖧝  𖥷  𖨥   ᬉ  ❆  
 
❈  ❉  ❊  ❋  ⏆  ▿  ▾  ⬚  ⁂  ❲  ❳  ⋱  ≣  ∕  ✃  ☆  ✩  ★  ✰  ✯  ᔢ  

ഇ  ൺ  ၄  ၃  ⁂  ≎  𖡹  𖣁  𖣘  𖣐  𖡵  ᪣  𖢅  𖡟  ༆  𖧟  𖤛  ༎    

𖣇  ⸙  𖠄  𖦜  𖡨  ۩  𖡷  𖢏  ༜  𖣐  𖡞  𖤌  𖦼  𖤛  𖤹  𖥞  ✠  ☩  ☨  ✙ 

✚  ✛  ✜  ✞  ✠  †  ☨  ✞  ☥  ☓  ♁  ☩  ❂  ⋆  ✢  ✣  ✤  ✥  ✦  ✧  ✩

  ✪  ✫  ✬  ✭  ✮  ✯  ✰  ★  ✱  ✲  ✵  ✶  ✷  ✸  ✹  ✺  ✻  ✼  ❅ 
  
 ❆  ❈  ❉  ❊  ❋  ╰  ☆  ╮  ❂  ✆  ☏  ❖  ❥  ❦  ❧  ♡  ✗  ✘  ⊗  Ω  
 
♢  ♤  ♡  ♧  ✦  ✧  ♔  ♕  ♚  ♛  ★  ☆  ✮  ✯  ☾  ☽  ☼  웃  유  ✖️ 

 ☤  ❖  ❥  ❦  ❧  ♡  ✗  ✘  ⊗  Ω  ♢  ♤  ♡  ♧  ✦  ✧  ♔  ♕  ♚  ♛  ★
 
  ☆  ✮  ✯  ☾  ☽  ☼  ☻  ۩  ♪  ♫  ♬  ✄  ✆  ∞  ♂  ♀  ☿  ▲  ▼  △  ▽ 
  
 ◆  ◇  ◕  ◔  ʊ  ϟ  ღ  回  ₪  ✓  ✔️  ✕  ☥  ☧  ☨  ☩  ☫  ☬  ☭  ™  ©
 
  ®  ¿  ¡  №  ⇨  ❝  ❞  ∃  ⊥  ∀  Ξ  ∞  Σ  Π⌥  ⌘  文  ⑂  ஜ  ๏  ⚓️  
  
⎈  ☻  ヅ  ツ  ッ  シ  Ü  ϡ  $  ₣  ¢  £  ¤  ¥  ฿  ₠  ₡  ₢  ₣  ₤  ₥  ₦ 

 ₧  ₨  ₩  ₫  ₪  €  ＄  ￠  ﹩  ￡  ￥  ￦  ▲  ◣  ◢  ◥  ▼  △  ▽  
 
⊿  ◤  ◥  △  ▴  ▵  ▷  ▸  ▹  ►  ▻  ▼  ▽  ▾  ▿  ◁  ◂  ◃  ◄  ◅
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "⏩ | Next",'callback_data'=> "next12"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "next12"){
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=> $message_id2,
'text'=>"
لیست دوم سمبل ♻️
➀➁➂➃➄➅➆➇➈ ➉

➊➋➌➍➎➏➐➑➒➓

❂ ⋆ ✢ ✣ ✤ ✥ ✦ ✧ ✩ ✪ ✫ ✬ ✭ ✮ ✯ ✰ ★ ✱ ✲

✳ ✴ ✵ ✶ ✷ ✸ ✹ ✺ ✻ ✼ ❄ ❅ ❆ ❇ ❈ ❉ ❊ ❋ ╰☆╮ ✡ ❂

☩ ☨ ☦ ✙ ✚ ✛ ✜ ✝ ✞ ✠

☪ ✡ † ☨ ✞ ✝ ☥ ☦ ☓ ♁ ☩

 ⚜ ☸♨ ❖ ❤ ❥ ❦ ❧ ♡ ✗ ✘ ⊗ ♒ Ω ♦ ♠ ♥ ♣ ♢ ♤ ♡ ♧ 

㌹ – ㍭ – 頹 – 衙 – 浳 – 浤 – 搰 – 煤 – 洳 – 橱 – 橱 – 煪 –

 ㍱ – 煱 – 둻 – 睤 – 楤 – ぱ – – 椹 – ぱ – ㍵ – 畱 – 煵 – 田 
 
– つ – 煵 – 엌 – 嫠 – 쯦 – 案 – 迎 – 是 – 從 – 事 – 網 – 頁 

– 設 – 計 – 簡 – 大✦ ✧ ♔ ♕ ♚ ♛ ★ ☆ ✮ ✯ ☄ ☾ ☽ ☼ ☀ ☁ ☂
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "🔙 | قبلی ",'callback_data'=> "sambol"]],
[['text' => "منو اصلی",'callback_data'=> "back"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "vip"){
file_put_contents("data/$chatid/setup.txt","send");
bot('sendmessage',[
'chat_id'=> $chatid,
'text'=> "
متن خود را ارسال کنید 📤
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "❌ | Cancel",'callback_data'=> "Cancel"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "Cancel"){
file_put_contents("data/$chatid/setup.txt","none");
bot('deleteMessage' , [
'chat_id'    => $chatid,
'message_id' => $message_id2
]);
}
elseif(file_get_contents("data/$chat_id/setup.txt") == "send"){
bot('sendmessage',[
'chat_id'=> $chat_id,
'text'=> "
ارسال شد ✅
",
'reply_markup'=>json_encode(['KeyboardRemove'=>[
],'remove_keyboard'=>true
])
]);
bot('sendmessage',[
'chat_id'=> $admin,
'text'=> "
پیام جدید از کاربر : $chat_id 👤

$text
",
'parse_mode'=>"markdown",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "♻️ | پاسخ سریع",'callback_data'=> "fastsend"],['text' => "✅",'url'=> "https://t.me/$idbot?start=$chat_id"]],
], 'resize_keyboard' => true,
])
]);
file_put_contents("data/$chat_id/setup.txt","none");
}
if($data == "fastsend"){
file_put_contents("data/$chatid/setup.txt","se1");
bot('sendmessage',[
'chat_id'=> $admin,
'text'=> "
*جهت ادامه روی دکمه ✅ کلیک کنید*
",
'parse_mode'=>"markdown",
]);
}
elseif(file_get_contents("data/$chat_id/setup.txt") == "se1"){
$send = str_replace("/start ","","$text");
file_put_contents("data/$chat_id/setup.txt","se2");
file_put_contents("data/$chat_id/setup2.txt","$send");
bot('sendmessage',[
'chat_id'=> $admin,
'text'=> "
*حالا متن مورد نظر را ارسال کنید ❗*
",
'parse_mode'=>"markdown",
'reply_markup'=>json_encode(['KeyboardRemove'=>[
],'remove_keyboard'=>true
])
]);
}
elseif(file_get_contents("data/$chat_id/setup.txt") == "se2"){
file_put_contents("data/$chat_id/setup.txt","none");
bot('sendmessage',[
'chat_id'=> $admin,
'text'=> "
ارسال شد ✅
",
'reply_markup'=>json_encode(['KeyboardRemove'=>[
],'remove_keyboard'=>true
])
]);
bot('sendmessage',[
'chat_id'=> file_get_contents("data/$chat_id/setup2.txt"),
'text'=> "
پیام ارسال شده از مدیریت برای شما 👤 :

$text
",
]);
file_put_contents("data/$chat_id/setup2.txt","none");
}
if($data == "user"){
$ted = file_get_contents("data/$chatid/dr.txt");
bot('EditMessageText',[
'chat_id'=>$chatid,
'message_id'=>$message_id2,
'text'=> "
📤 | تعداد فونت درخواستی شما : $ted
",
'reply_markup'=> $kyy
]);
}
if($text == "/admin" and $chat_id == "$admin"){
file_put_contents("data/$chat_id/setup.txt","none");
file_put_contents("data/$chat_id/setup2.txt","none");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=> "
welcom Admin ⚡
",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "👤 | آمار",'callback_data'=> "am"]],
[['text' => "🤖 | بات خاموش",'callback_data'=> "off"],['text' => "🤖 | بات روشن",'callback_data'=> "on"]],
[['text' => "♨️ | تنظیم چنل",'callback_data'=> "ch"]],
], 'resize_keyboard' => true,
])
]);
}
if($data == "ch"){
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=> "
جهت تنظیم چنل از دستور زیر استفاده کنید
/channel ID
مثال
/channel IDsaji
بدون @ ‼️
",
]);
}
if(strpos($text,"/channel ") !== false and $chat_id == "$admin"){
   $id=str_replace("/channel ","",$text);
     file_put_contents("ch.txt","$id");
bot('sendmessage',[
'chat_id'=>$chat_id,
'text'=> "
seted . . . ♻️
",
]);
}
if($data == "am"){
$allm = file_get_contents("allmember.txt");
$member_id = explode("\n",$allm);
$mmemcount = count($member_id) -1;
$ted = file_get_contents("data/dr.txt");
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=> "
👤 | کاربران : $mmemcount

♻️ | کل فونت های درخواست شده : $ted
",
]);
}
if($data == "on"){
file_put_contents("data/bot.txt","on");
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=> "
روشن شد ⚡
",
]);
}
if($data == "off"){
file_put_contents("data/bot.txt","off");
bot('sendmessage',[
'chat_id'=>$chatid,
'text'=> "
خاموش شد ❌
",
]);
}

if($tch != "creator" and $tch != "administrator" and $tch != "member"){
if($channel != ""){
bot('sendMessage',[
'chat_id' => $chat_id,
'text'=>"
ابتدا در چنل های زیر عضو شوید ⚡
@$channel
⚠️ بعد از عضو شدن بزن رو /start",
'reply_markup'=> json_encode(['inline_keyboard'=>[
[['text' => "عضویت ♻️",'url'=> "t.me/$channel"]],
], 'resize_keyboard' => true,
])
]);
exit;
}
}
?>