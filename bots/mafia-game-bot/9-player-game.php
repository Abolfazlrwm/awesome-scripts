<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

$timer = time();
include("jdf.php");
include("button.php");
include("function.php");
if(isset($_POST["server"])){
$server = $_POST["server"];
$serverid = json_decode(file_get_contents("play/$server/id.json"),true);
$player1 = $serverid["player1"];
$player2 = $serverid["player2"];
$player3 = $serverid["player3"];
$player4 = $serverid["player4"];
$player5 = $serverid["player5"];
$player6 = $serverid["player6"];
$player7 = $serverid["player7"];
$player8 = $serverid["player8"];
$player9 = $serverid["player9"];
$rand_1_9 = '012345678';
$trues = str_shuffle($rand_1_9);
$fales = wordwrap($trues, 1, ' ',true);
$explodes = explode(" ",$fales);
$mafia = array("شهروند 🚶‍♂️","ساحره 🧙‍♀","قاتل سریالی 🔪","کارآگاه 🕵","دکتر 💉","بازپرس 🔗","اسنایپر 🔫","نوچه مافیا 🕶","پدرخوانده 🚬");
$random_keys = array_rand($mafia,9);
$paly1 = $mafia[$random_keys[$explodes[0]]];
$paly2 = $mafia[$random_keys[$explodes[1]]];
$paly3 = $mafia[$random_keys[$explodes[2]]];
$paly4 = $mafia[$random_keys[$explodes[4]]];
$paly5 = $mafia[$random_keys[$explodes[5]]];
$paly6 = $mafia[$random_keys[$explodes[6]]];
$paly7 = $mafia[$random_keys[$explodes[3]]];
$paly8 = $mafia[$random_keys[$explodes[7]]];
$paly9 = $mafia[$random_keys[$explodes[8]]];
////////////////////////////////////////////////////////////////////////////////////////////
$yourjob1 = PlayerJobs("$server","$player1","$paly1");
$yourjob2 = PlayerJobs("$server","$player2","$paly2");
$yourjob3 = PlayerJobs("$server","$player3","$paly3");
$yourjob4 = PlayerJobs("$server","$player4","$paly4");
$yourjob5 = PlayerJobs("$server","$player5","$paly5");
$yourjob6 = PlayerJobs("$server","$player6","$paly6");
$yourjob7 = PlayerJobs("$server","$player7","$paly7");
$yourjob8 = PlayerJobs("$server","$player8","$paly8");
$yourjob9 = PlayerJobs("$server","$player9","$paly9");
////////////////////////////////////////////////////////////////////////////////////////////
$pin = bot('sendMessage',[
'chat_id'=>$player1,
'text'=>"
$yourjob1
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player1,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player2,
'text'=>"
$yourjob2
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player2,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player3,
'text'=>"
$yourjob3
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player3,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player4,
'text'=>"
$yourjob4
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player4,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player5,
'text'=>"
$yourjob5
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player5,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player6,
'text'=>"
$yourjob6
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player6,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player7,
'text'=>"
$yourjob7
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player7,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player8,
'text'=>"
$yourjob8
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player8,
'message_id' => $pin,
]);
$pin = bot('sendMessage',[
'chat_id'=>$player9,
'text'=>"
$yourjob9
",
'parse_mode'=>"HTML",
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$player9,
'message_id' => $pin,
]);
sleep(25);
for($i = 0;$i < 10;$i++){
$timer = time();
$ronight = str_replace(['0','1','2','3','4','5','6','7','8','9'],['🌙 #شب اول','🌙 #شب دوم ( 🌕 امشب ماه کامل است )','🌙 #شب سوم','🌙 #شب چهارم ( 🌕 امشب ماه کامل است )','🌙 #شب پنجم','🌙 #شب ششم ( 🌕 امشب ماه کامل است )','🌙 #شب هفتم','🌙 #شب هشتم ( 🌕 امشب ماه کامل است )','🌙 #شب نهم','🌙 #شب دهم ( 🌕 امشب ماه کامل است )'],$i);
$today = str_replace(['0','1','2','3','4','5','6','7','8','9'],['🌞 #روز اول','🌞 #روز دوم','🌞 #روز سوم','🌞 #روز چهارم','🌞 #روز پنجم','🌞 #روز ششم','🌞 #روز هفتم','🌞 #روز هشتم','🌞 #روز نهم','🌞 #روز دهم'],$i);
//============================================================================//
$serverid = json_decode(file_get_contents("play/$server/id.json"),true);
$player1 = $serverid["player1"];
$player2 = $serverid["player2"];
$player3 = $serverid["player3"];
$player4 = $serverid["player4"];
$player5 = $serverid["player5"];
$player6 = $serverid["player6"];
$player7 = $serverid["player7"];
$player8 = $serverid["player8"];
$player9 = $serverid["player9"];
//============================================================================//
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$TG1 = $serverinfo["$player1"]["nameP"];
$TG2 = $serverinfo["$player2"]["nameP"];
$TG3 = $serverinfo["$player3"]["nameP"];
$TG4 = $serverinfo["$player4"]["nameP"];
$TG5 = $serverinfo["$player5"]["nameP"];
$TG6 = $serverinfo["$player6"]["nameP"];
$TG7 = $serverinfo["$player7"]["nameP"];
$TG8 = $serverinfo["$player8"]["nameP"];
$TG9 = $serverinfo["$player9"]["nameP"];
$jobs1 = $serverinfo["$player1"]["job"];
$jobs2 = $serverinfo["$player2"]["job"];
$jobs3 = $serverinfo["$player3"]["job"];
$jobs4 = $serverinfo["$player4"]["job"];
$jobs5 = $serverinfo["$player5"]["job"];
$jobs6 = $serverinfo["$player6"]["job"];
$jobs7 = $serverinfo["$player7"]["job"];
$jobs8 = $serverinfo["$player8"]["job"];
$jobs9 = $serverinfo["$player9"]["job"];
//============================================================================//
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$NameFather = $serverjobs["father"];
$NameNoche = $serverjobs["noche"];
//============================================================================//
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$god_id = $serverjobs["fatherid"] ?? null;
$noche_id = $serverjobs["nocheid"] ?? null;
$ghatel_id = $serverjobs["ghatelid"] ?? null;
$bazpors_id = $serverjobs["bazporsid"] ?? null;
$doctor_id = $serverjobs["doctorid"] ?? null;
$kar_id = $serverjobs["karagahid"] ?? null;
$sahere_id = $serverjobs["sahereid"] ?? null;
$asnayper_id = $serverjobs["asnayperid"] ?? null;
$shaher_id = $serverjobs["shahrid"] ?? null;
//============================================================================//
$keyAsnayper = [];
$keyKar = [];
$keyGhatel = [];
$keyMafia = [];
$keyDoctor = [];
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($live == "LIVE" and $god_id != $k and $noche_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyMafia[] = [['text'=>"$yournamer",'callback_data'=>"mafia-$timer-$k"]];
}
if($live == "LIVE" and $ghatel_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyGhatel[] = [['text'=>"$yournamer",'callback_data'=>"ghatel-$timer-$k"]];
}
if($live == "LIVE" and $doctor_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyDoctor[] = [['text'=>"$yournamer",'callback_data'=>"doctor-$timer-$k"]];
}
if($live == "LIVE" and $kar_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyKar[] = [['text'=>"$yournamer",'callback_data'=>"kar-$timer-$k"]];
}
if($live == "LIVE" and $asnayper_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyAsnayper[] = [['text'=>"$yournamer",'callback_data'=>"asnayper-$timer-$k"]];
}
}
//============================================================================//
unlink("play/$server/chati.txt");
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$zendan = $serverselect["bazporcc"]["zendan"] ?? 'none';
$zendaname = $serverselect["bazporcc"]["zendaname"] ?? 'none';
if($zendan == "none"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
💬 چت : غیر فعال
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}
if($zendan == "$god_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$god_id,
'text'=>"
$ronight
🎭 نقش شما : پدرخوانده 🚬
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
if($zendan == "$noche_id"){
bot('sendMessage',[
'chat_id'=>$god_id,
'text'=>"
$ronight
🎭 نقش شما : پدرخوانده 🚬
🔖 مأموریت : یه نفرو انتخاب کن تا امشب بهش حمله کنی
پدرخوانده 🚬 : $NameFather 🔫
نوچه مافیا 🕶 : $NameNoche | زندانی
💬 چت : فقط با تیم
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyMafia
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$god_id,
'text'=>"
$ronight
🎭 نقش شما : پدرخوانده 🚬
🔖 مأموریت : یه نفرو انتخاب کن تا امشب بهش حمله کنی
پدرخوانده 🚬 : $NameFather 🔫
نوچه مافیا 🕶 : $NameNoche
💬 چت : فقط با تیم
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyMafia
])
]);
}
}
if($zendan == "$noche_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$ronight
🎭 نقش شما : نوچه مافیا 🕶
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
if($zendan == "$god_id"){
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$ronight
🎭 نقش شما : نوچه مافیا 🕶
🔖 مأموریت : یه نفرو انتخاب کن تا امشب بهش حمله کنی
پدرخوانده 🚬 : $NameFather | زندانی
نوچه مافیا 🕶 : $NameNoche 🔫
💬 چت : فقط با تیم
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyMafia
])
]);
}else{
if($god_id != null){
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$ronight
🎭 نقش شما : نوچه مافیا 🕶
🔖 مأموریت : پدرخوانده باید یک نفر را انتخاب کند برای حمله
پدرخوانده 🚬 : $NameFather 🔫
نوچه مافیا 🕶 : $NameNoche 
💬 چت : فقط با تیم
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$noche_id,
'text'=>"
$ronight
🎭 نقش شما : نوچه مافیا 🕶
🔖 مأموریت : یه نفرو انتخاب کن تا امشب بهش حمله کنی
پدرخوانده 🚬 : $NameFather
نوچه مافیا 🕶 : $NameNoche 🔫
💬 چت : فقط با تیم
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyMafia
])
]);
}
}
}
if($zendan == "$ghatel_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$ghatel_id,
'text'=>"
$ronight
🎭 نقش شما : قاتل سریالی 🔪
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$ghatel_id,
'text'=>"
$ronight
🎭 نقش شما : قاتل سریالی 🔪
🔖 مأموریت : یه نفرو انتخاب کن تا امشب بهش حمله کنی
💬 چت : غیرفعال
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyGhatel
])
]);
}
if($zendan == "$doctor_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$doctor_id,
'text'=>"
$ronight
🎭 نقش شما : دکتر 💉
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$doctor_id,
'text'=>"
$ronight
🎭 نقش شما : دکتر 💉
🔖 مأموریت : امشب میتونی جون یه نفر رو نجات بدی , انتخاب کن
💬 چت : غیرفعال 
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyDoctor
])
]);
}
if($zendan == "$kar_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$kar_id,
'text'=>"
$ronight
🎭 نقش شما : کاراگاه 🕵️‍♂️
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$kar_id,
'text'=>"
$ronight
🎭 نقش شما : کاراگاه 🕵️‍♂️
🔖 مأموریت : مشکوکیت یه نفرو بررسی کن با یکبار استعلام نمیتونی پدرخوانده رو پیدا کنی...
💬 چت : غیرفعال
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyKar
])
]);
}
if($zendan == "$sahere_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$sahere_id,
'text'=>"
$ronight
🎭 نقش شما : ساحره 🧙‍♀️
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$sahere_id,
'text'=>"
$ronight
🎭 نقش شما : ساحره 🧙‍♀️
💬 چت : با مردگان
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}
if($zendan == "$asnayper_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$asnayper_id,
'text'=>"
$ronight
🎭 نقش شما : اسنايپر 🔫
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$asnayper_id,
'text'=>"
$ronight
🎭 نقش شما : اسنايپر 🔫
🔖 مأموریت : امشب تفنگ خود را آماده می کنید
💬 چت : غیرفعال
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyAsnayper
])
]);
}
if($zendan == "$shaher_id"){
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
$ronight
🎭 نقش شما : بازپرس 🔗
زندانی شما : $zendaname
💬 چت : فقط با زندانی
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"$zendaname",'callback_data'=>"bazporcc-$timer-$zendan"]],
]
])
]);
bot('sendMessage',[
'chat_id'=>$shaher_id,
'text'=>"
$ronight
🎭 نقش شما : شهروند 🚶‍♂️
🔖 مأموریت : شما امشب زندانی هستید
💬 چت : فقط با بازپرس
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}else{
bot('sendMessage',[
'chat_id'=>$shaher_id,
'text'=>"
$ronight
🎭 نقش شما : شهروند 🚶‍♂️
💬 چت : غیرفعال
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$okno = json_decode(file_get_contents("play/$server/info.json"),true)["$user"]["live"];
if($okno == "DEAD"){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
‏$ronight
💬 چت : فقط کشته شده ها
🌞 روز 👈 ⏱ 40 ثانیه
",
'parse_mode'=>"HTML",
]);
}
}
sleep(40);
$timer = time();
$serverselect = json_decode(file_get_contents("play/$server/select.json"),true);
$seldokid = $serverselect["getdoki"]["select"] ?? null;
$selasnaid = $serverselect["getasna"]["select"] ?? null;
$chosbaz = $serverselect["bazporcc"]["select"] ?? null;
$selbazid = $serverselect["bazporcc"]["zendan"] ?? null;
$selkarid = $serverselect["getkaragah"]["select"] ?? null;
$selghatelid = $serverselect["independent"]["select"] ?? null;
$selmafid = $serverselect["getmafia"]["select"] ?? null;
$listroy = file_get_contents("play/$server/roytan.txt");
$roytan = explode("\n",$listroy);
$listkhabar = file_get_contents("play/$server/khabar.txt");
$khabar = explode("\n",$listkhabar);
if($selkarid != null){
$karname = json_decode(file_get_contents("play/$server/info.json"),true)["$selkarid"]["nameP"];
$karjob = json_decode(file_get_contents("play/$server/info.json"),true)["$selkarid"]["job"];
if($karjob == "شهروند 🚶‍♂️" or $karjob == "اسنایپر 🔫" or $karjob == "ساحره 🧙‍♀" or $karjob == "دکتر 💉" or $karjob == "بازپرس 🔗"){
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$kar_id"]["roydad2"] = "$karname مشکوک نیست";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if($karjob == "قاتل سریالی 🔪" or $karjob == "نوچه مافیا 🕶"){
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$kar_id"]["roydad2"] = "$karname مشکوک است";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if($karjob == "پدرخوانده 🚬"){
if(file_get_contents("play/$server/assfather.txt") == true){
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$kar_id"]["roydad2"] = "$karname مشکوک است";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$kar_id"]["roydad2"] = "$karname مشکوک نیست";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
file_put_contents("play/$server/assfather.txt","yes");
}
}
}
if($chosbaz != null){
if(in_array($chosbaz,$khabar)){
$who_is = json_decode(file_get_contents("play/$server/jobs.json"),true)["bazpors"];
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$chosbaz"]["roydad3"] = "دیشب $who_is به شما حمله کرد !";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if(!in_array($chosbaz,$roytan)){
if($chosbaz != $seldokid){
$exc = file_get_contents("play/$server/dead.txt");
$exp = explode("\n",$exc);
if(!in_array($chosbaz,$exp)){
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$chosbaz\n");
fclose($DeadWrite);
}
$connect->query("UPDATE user SET step = 'dead' WHERE id = '$chosbaz' LIMIT 1");
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$killjob1 = $serverinfo["$chosbaz"]["job"];
$newname = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$chosbaz' LIMIT 1"))["name"];
$serverinfo["$chosbaz"]["nameP"] = "$newname | $killjob1";
$serverinfo["$chosbaz"]["live"] = "DEAD";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$putting = "‌$newname توسط بازپرس کشته شد";
$write = fopen("play/$server/roydad.txt", "a") or die("Unable to open file!");
fwrite($write, "$putting\n");
fclose($write);
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$chosbaz"]["roydad1"] = "شما کشته شدید";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$chosbaz"]["roydad1"] = "دکتر شما را از یک حمله نجات داد";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$chosbaz"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}
if($selasnaid != null){
if(in_array($selasnaid,$khabar)){
$who_is = json_decode(file_get_contents("play/$server/jobs.json"),true)["asnayper"];
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
if($serverRoydad["$selasnaid"]["roydad3"] == false){
$serverRoydad["$selasnaid"]["roydad3"] = "دیشب $who_is به شما حمله کرد !";
}else{
$serverRoydad["$selasnaid"]["roydad3"] = "{$serverRoydad["$selasnaid"]["roydad3"]}\nدیشب $who_is به شما حمله کرد !";
}
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if($selasnaid != $selbazid){
if(!in_array($selasnaid,$roytan)){
if($selasnaid != $seldokid){
$exc = file_get_contents("play/$server/dead.txt");
$exp = explode("\n",$exc);
if(!in_array($selasnaid,$exp)){
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$selasnaid\n");
fclose($DeadWrite);
}
$connect->query("UPDATE user SET step = 'dead' WHERE id = '$selasnaid' LIMIT 1");
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$killjob2 = $serverinfo["$selasnaid"]["job"];
$newname = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$selasnaid' LIMIT 1"))["name"];
$serverinfo["$selasnaid"]["nameP"] = "$newname | $killjob2";
$serverinfo["$selasnaid"]["live"] = "DEAD";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$putting = "‌$newname توسط اسنایپر کشته شد";
$write = fopen("play/$server/roydad.txt", "a") or die("Unable to open file!");
fwrite($write, "$putting\n");
fclose($write);
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selasnaid"]["roydad1"] = "شما کشته شدید";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selasnaid"]["roydad1"] = "دکتر شما را از یک حمله نجات داد";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selasnaid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selasnaid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}
if($selmafid != null){
if(in_array($selmafid,$khabar)){
if($god_id != null){
$who_is = json_decode(file_get_contents("play/$server/jobs.json"),true)["father"];
}else{
$who_is = json_decode(file_get_contents("play/$server/jobs.json"),true)["noche"];
}
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
if($serverRoydad["$selmafid"]["roydad3"] == false){
$serverRoydad["$selmafid"]["roydad3"] = "دیشب $who_is به شما حمله کرد !";
}else{
$serverRoydad["$selmafid"]["roydad3"] = "{$serverRoydad["$selmafid"]["roydad3"]}\nدیشب $who_is به شما حمله کرد !";
}
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if($selmafid != $selbazid){
if(!in_array($selmafid,$roytan)){
if($selmafid != $seldokid){
$exc = file_get_contents("play/$server/dead.txt");
$exp = explode("\n",$exc);
if(!in_array($selmafid,$exp)){
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$selmafid\n");
fclose($DeadWrite);
}
$connect->query("UPDATE user SET step = 'dead' WHERE id = '$selmafid' LIMIT 1");
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$killjob3 = $serverinfo["$selmafid"]["job"];
$newname = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$selmafid' LIMIT 1"))["name"];
$serverinfo["$selmafid"]["nameP"] = "$newname | $killjob3";
$serverinfo["$selmafid"]["live"] = "DEAD";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$putting = "‌$newname توسط گروه مافیا کشته شد";
$write = fopen("play/$server/roydad.txt", "a") or die("Unable to open file!");
fwrite($write, "$putting\n");
fclose($write);
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selmafid"]["roydad1"] = "شما کشته شدید";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selmafid"]["roydad1"] = "دکتر شما را از یک حمله نجات داد";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selmafid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selmafid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}
if($selghatelid != null){
if(in_array($selghatelid,$khabar)){
$who_is = json_decode(file_get_contents("play/$server/jobs.json"),true)["ghatel"];
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
if($serverRoydad["$selghatelid"]["roydad3"] == false){
$serverRoydad["$selghatelid"]["roydad3"] = "دیشب $who_is به شما حمله کرد !";
}else{
$serverRoydad["$selghatelid"]["roydad3"] = "{$serverRoydad["$selghatelid"]["roydad3"]}\nدیشب $who_is به شما حمله کرد !";
}
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
if($selghatelid != $selbazid){
if(!in_array($selghatelid,$roytan)){
if($selghatelid != $seldokid){
$exc = file_get_contents("play/$server/dead.txt");
$exp = explode("\n",$exc);
if(!in_array($selghatelid,$exp)){
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$selghatelid\n");
fclose($DeadWrite);
}
$connect->query("UPDATE user SET step = 'dead' WHERE id = '$selghatelid' LIMIT 1");
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$killjob4 = $serverinfo["$selghatelid"]["job"];
$newname = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$selghatelid' LIMIT 1"))["name"];
$serverinfo["$selghatelid"]["nameP"] = "$newname | $killjob4";
$serverinfo["$selghatelid"]["live"] = "DEAD";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$putting = "‌$newname توسط قاتل سریالی کشته شد";
$write = fopen("play/$server/roydad.txt", "a") or die("Unable to open file!");
fwrite($write, "$putting\n");
fclose($write);
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selghatelid"]["roydad1"] = "شما کشته شدید";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selghatelid"]["roydad1"] = "دکتر شما را از یک حمله نجات داد";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selghatelid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}else{
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$serverRoydad["$selghatelid"]["roydad1"] = "دیشب یک نفر سعی در کشتن شما داشت";
$outjson = json_encode($serverRoydad,true);
file_put_contents("play/$server/roydad.json",$outjson);
}
}
////////////////////////////////////////////////////////////////////////////////////////////
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$TG1 = $serverinfo["$player1"]["nameP"];
$TG2 = $serverinfo["$player2"]["nameP"];
$TG3 = $serverinfo["$player3"]["nameP"];
$TG4 = $serverinfo["$player4"]["nameP"];
$TG5 = $serverinfo["$player5"]["nameP"];
$TG6 = $serverinfo["$player6"]["nameP"];
$TG7 = $serverinfo["$player7"]["nameP"];
$TG8 = $serverinfo["$player8"]["nameP"];
$TG9 = $serverinfo["$player9"]["nameP"];
$roydadall = file_get_contents("play/$server/roydad.txt");
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverRoydad = json_decode(file_get_contents("play/$server/roydad.json"),true);
$Uroydad1 = $serverRoydad["$user"]["roydad1"];
$Uroydad2 = $serverRoydad["$user"]["roydad2"];
$Uroydad3 = $serverRoydad["$user"]["roydad3"];
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"‏
$today

‏$roydadall

1. ‏$TG1
2. ‏$TG2
3. ‏$TG3
4. ‏$TG4
5. ‏$TG5
6. ‏$TG6
7. ‏$TG7
8. ‏$TG8
9. ‏$TG9

$Uroydad1
$Uroydad2
$Uroydad3
💬 چت کردن  👈 ⏱ 5 ثانیه
",
'parse_mode'=>"HTML",
]);
}
if($killjob1 == "شهروند 🚶‍♂️" or $killjob2 == "شهروند 🚶‍♂️" or $killjob3 == "شهروند 🚶‍♂️" or $killjob4 == "شهروند 🚶‍♂️"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["shahr"]);
unset($serverjobs["shahrid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "اسنایپر 🔫" or $killjob2 == "اسنایپر 🔫" or $killjob3 == "اسنایپر 🔫" or $killjob4 == "اسنایپر 🔫"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["asnayper"]);
unset($serverjobs["asnayperid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "ساحره 🧙‍♀" or $killjob2 == "ساحره 🧙‍♀" or $killjob3 == "ساحره 🧙‍♀" or $killjob4 == "ساحره 🧙‍♀"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["sahere"]);
unset($serverjobs["sahereid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "کارآگاه 🕵" or $killjob2 == "کارآگاه 🕵" or $killjob3 == "کارآگاه 🕵" or $killjob4 == "کارآگاه 🕵"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["karagah"]);
unset($serverjobs["karagahid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "دکتر 💉" or $killjob2 == "دکتر 💉" or $killjob3 == "دکتر 💉" or $killjob4 == "دکتر 💉"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["doctor"]);
unset($serverjobs["doctorid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "بازپرس 🔗" or $killjob2 == "بازپرس 🔗" or $killjob3 == "بازپرس 🔗" or $killjob4 == "بازپرس 🔗"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["bazpors"]);
unset($serverjobs["bazporsid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "قاتل سریالی 🔪" or $killjob2 == "قاتل سریالی 🔪" or $killjob3 == "قاتل سریالی 🔪" or $killjob4 == "قاتل سریالی 🔪"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["ghatel"]);
unset($serverjobs["ghatelid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "نوچه مافیا 🕶" or $killjob2 == "نوچه مافیا 🕶" or $killjob3 == "نوچه مافیا 🕶" or $killjob4 == "نوچه مافیا 🕶"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["noche"] = "$NameNoche | کشته شده";
unset($serverjobs["nocheid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($killjob1 == "پدرخوانده 🚬" or $killjob2 == "پدرخوانده 🚬" or $killjob3 == "پدرخوانده 🚬" or $killjob4 == "پدرخوانده 🚬"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["father"] = "$NameFather | کشته شده";
unset($serverjobs["fatherid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
sleep(5);
$timer = time();
unlink("play/$server/roydad.json");
unlink("play/$server/roydad.txt");
unlink("play/$server/select.json");
unlink("play/$server/roytan.txt");
unlink("play/$server/khabar.txt");
file_put_contents("play/$server/chati.txt","on");
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$god_id = $serverjobs["fatherid"] ?? null;
$noche_id = $serverjobs["nocheid"] ?? null;
$ghatel_id = $serverjobs["ghatelid"] ?? null;
$bazpors_id = $serverjobs["bazporsid"] ?? null;
$doctor_id = $serverjobs["doctorid"] ?? null;
$kar_id = $serverjobs["karagahid"] ?? null;
$sahere_id = $serverjobs["sahereid"] ?? null;
$asnayper_id = $serverjobs["asnayperid"] ?? null;
$shaher_id = $serverjobs["shahrid"] ?? null;
$x = Information($server)['LIVE'];
if($x == "0"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
⚪️ این بازی مساوی شد !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($ghatel_id != null and $x == "1"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["ghatelid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🟡 گروه مستقل برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($god_id != null and $x == "1" or $noche_id != null and $x == "1" or $god_id != null and $noche_id != null and $x == "2"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["nocheid","fatherid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🔴 گروه مافیا برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($god_id == null and $noche_id == null){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["bazporsid","doctorid","karagahid","sahereid","asnayperid","shahrid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🟢 شهروندان برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}
}
}
}
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
💬 چت : فعال برای همه
⚖️ رای گیری  👈 ⏱ 45 ثانیه
",
'parse_mode'=>"HTML",
]);
}
sleep(45);
$timer = time();
file_put_contents("play/$server/chati.txt","on");
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$bazpors_id = $serverjobs["bazporsid"] ?? null;
if($bazpors_id != null){
$timer = time();
$keyBazpors = [];
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($live == "LIVE" and $bazpors_id != $k and $k != null){
$GetName = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $GetName["name"];
$keyBazpors[] = [['text'=>"$yournamer",'callback_data'=>"bazpors-$timer-$k"]];
}
}
bot('sendMessage',[
'chat_id'=>$bazpors_id,
'text'=>"
یک نفر را انتخاب کنید تا اورا زندانی کرده و شب از او بازجویی کنید 👇

💬 چت : فعال
⚖️ پایان رای گیری  👈 ⏱ 45 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyBazpors
])
]);
}
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$okno = json_decode(file_get_contents("play/$server/info.json"),true)["$user"]["live"];
if($okno != "DEAD"){
$keyRay = [];
$newgame = file_get_contents("play/$server/newgame.txt");
$exp = explode("\n",$newgame);
foreach($exp as $k){
$arraylist = file_get_contents("play/$server/imnot.txt");
$notray = explode("\n",$arraylist);
if(!in_array($k,$notray)){
$live = json_decode(file_get_contents("play/$server/info.json"),true)["$k"]["live"];
if($k != null and $live == "LIVE"){
$datas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$k' LIMIT 1"));
$yournamer = $datas["name"];
$keyRay[] = [['text'=>"$yournamer",'callback_data'=>"RayGiri-$timer-$k"]];
}
}
}
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
‏#رای بدهید و افراد مشکوک را اعدام کنید

🔰 وضعیت رای دادن : ✅

⚖️ پایان رای گیری  👈 ⏱ 45 ثانیه
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>$keyRay
])
]);
}else{
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
‏#رای بدهید و افراد مشکوک را اعدام کنید

🔰 وضعیت رای دادن : ❌

⚖️ پایان رای گیری  👈 ⏱ 45 ثانیه
",
'parse_mode'=>"HTML",
]);
}
}
sleep(45);
$timer = time();
$info = GetRay($server);
$namers = $info[0]['name'];
$rays = $info[0]['coins'];
$ids = $info[0]['id'];
$namerss = $info[1]['name'];
$rayss = $info[1]['coins'];
$idss = $info[1]['id'];
if($rayss != $rays){
file_put_contents("play/$server/motaham.txt","$ids");
unlink("play/$server/chati.txt");
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
⚖️ $namers با $rays رای به دادگاه رفت !
💬 چت : فقط متهم

👈 رای گیری مجدد : 20 ثانیه
",
'parse_mode'=>"HTML",
]);
}
}else{
if($rays == "0"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
💯 کسی به دادگاه نرفت ! 
🌙 #شب 20 ثانیه
💬 چت : فعال برای همه
",
'parse_mode'=>"HTML",
]);
}
}else{
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🔴 رای ها مساوی شد و کسی به دادگاه نرفت ! 
$namers و $namerss با $rays رای مساوی شدن !
🌙 #شب 20 ثانیه
💬 چت : فعال برای همه
",
'parse_mode'=>"HTML",
]);
}
}
}
sleep(20);
$timer = time();
if(file_exists("play/$server/motaham.txt")){
file_put_contents("play/$server/chati.txt","on");
$gets = file_get_contents("play/$server/motaham.txt");
$mohatame = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$gets' LIMIT 1"))["name"];
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
if($gets != $user){
$okno = json_decode(file_get_contents("play/$server/info.json"),true)["$user"]["live"];
if($okno != "DEAD"){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
‏✅ درحال رای گیری مجدد برای $mohatame !
",
'parse_mode'=>"HTML",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"گناهکاره !",'callback_data'=>"motaham-$timer-$gets"]],
[['text'=>"بی گناه !",'callback_data'=>"bigonah-$timer-$gets"]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
✔️ بازیکنان زنده در بازی درحال تصمیم گیری درباره‌ی $mohatame هستند !
",
'parse_mode'=>'MarkDown',
]);
}
}else{
bot('sendMessage',[
'chat_id'=>$gets,
'text'=>"
✔️ بازیکنان دیگه درحال تصمیم گیری در باره‌ی شما هستند !
",
'parse_mode'=>'MarkDown',
]);
}
}
sleep(15);
$timer = time();
$gets = file_get_contents("play/$server/motaham.txt");
$guilty = count(json_decode(file_get_contents("play/$server/court.json"),true)["$gets"]["guilty"]);
$innocent = count(json_decode(file_get_contents("play/$server/court.json"),true)["$gets"]["innocent"]);
$guilty_list = ListRay("guilty",$server,$gets) ?? "🌟 خالی . . . !\n";
$innocent_list = ListRay("innocent",$server,$gets) ?? "🌟 خالی . . . !\n";
$mohatame = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$gets' LIMIT 1"))["name"];
$jobs_he = json_decode(file_get_contents("play/$server/info.json"),true)["$gets"]["job"];
if($guilty == "0" or $innocent >= $guilty){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
⚫️ ( $guilty ) نفر رای به گناهکاری دادند !
$guilty_list
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
⚪️ ( $innocent ) نفر رای به بی‌گناهی دادند !
$innocent_list
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

$mohatame تبرئه شد
💬 چت : فعال
",
'parse_mode'=>"MarkDown",
]);
}
}else{
$exc = file_get_contents("play/$server/dead.txt");
$exp = explode("\n",$exc);
if(!in_array($gets,$exp)){
$DeadWrite = fopen("play/$server/dead.txt", "a") or die("Unable to open file!");
fwrite($DeadWrite, "$gets\n");
fclose($DeadWrite);
}
$connect->query("UPDATE user SET step = 'dead' WHERE id = '$gets' LIMIT 1");
$serverinfo = json_decode(file_get_contents("play/$server/info.json"),true);
$newname = $serverinfo["$gets"]["nameP"];
$jobs_he = $serverinfo["$gets"]["job"];
$serverinfo["$gets"]["nameP"] = "$newname | $jobs_he";
$serverinfo["$gets"]["live"] = "DEAD";
$outjson = json_encode($serverinfo,true);
file_put_contents("play/$server/info.json",$outjson);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
⚫️ ( $guilty ) نفر رای به گناهکاری دادند !
$guilty_list
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄
⚪️ ( $innocent ) نفر رای به بی‌گناهی دادند !
$innocent_list
┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄

*$mohatame* اعدام شد !
نقش : *$jobs_he*
💬 چت : فعال برای همه
",
'parse_mode'=>"MarkDown",
]);
}
if($jobs_he == "شهروند 🚶‍♂️"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["shahr"]);
unset($serverjobs["shahrid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "اسنایپر 🔫"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["asnayper"]);
unset($serverjobs["asnayperid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "ساحره 🧙‍♀"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["sahere"]);
unset($serverjobs["sahereid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "کارآگاه 🕵"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["karagah"]);
unset($serverjobs["karagahid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "دکتر 💉"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["doctor"]);
unset($serverjobs["doctorid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "بازپرس 🔗"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["bazpors"]);
unset($serverjobs["bazporsid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "قاتل سریالی 🔪"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
unset($serverjobs["ghatel"]);
unset($serverjobs["ghatelid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "نوچه مافیا 🕶"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["noche"] = "$NameNoche | کشته شده";
unset($serverjobs["nocheid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
if($jobs_he == "پدرخوانده 🚬"){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$serverjobs["father"] = "$NameFather | کشته شده";
unset($serverjobs["fatherid"]);
$outjson = json_encode($serverjobs,true);
file_put_contents("play/$server/jobs.json",$outjson);
}
}
unlink("play/$server/motaham.txt");
sleep(10);
$timer = time();
}
unlink("play/$server/ray.json");
unlink("play/$server/court.json");
unlink("play/$server/imnot.txt");
unlink("play/$server/hack.txt");
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$god_id = $serverjobs["fatherid"] ?? null;
$noche_id = $serverjobs["nocheid"] ?? null;
$ghatel_id = $serverjobs["ghatelid"] ?? null;
$bazpors_id = $serverjobs["bazporsid"] ?? null;
$doctor_id = $serverjobs["doctorid"] ?? null;
$kar_id = $serverjobs["karagahid"] ?? null;
$sahere_id = $serverjobs["sahereid"] ?? null;
$asnayper_id = $serverjobs["asnayperid"] ?? null;
$shaher_id = $serverjobs["shahrid"] ?? null;
$x = Information($server)['LIVE'];
if($x == "0"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
⚪️ این بازی مساوی شد !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($ghatel_id != null and $x == "1"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["ghatelid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🟡 گروه مستقل برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($god_id != null and $x == "1" or $noche_id != null and $x == "1" or $god_id != null and $noche_id != null and $x == "2"){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["nocheid","fatherid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🔴 گروه مافیا برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}else{
if($god_id == null and $noche_id == null){
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$serverjobs = json_decode(file_get_contents("play/$server/jobs.json"),true);
$key = array_search($user,$serverjobs);
$listwin = ["bazporsid","doctorid","karagahid","sahereid","asnayperid","shahrid"];
if(in_array($key,$listwin)){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ez = $userD["score"] + 1;
$ezz = $userD["win"] + 1;
$connect->query("UPDATE user SET win = '$ezz' , score = '$ez' WHERE id = '$user' LIMIT 1");
}
$pin_id = bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
🟢 شهروندان برنده شدند !
",
'parse_mode'=>"HTML",
'reply_markup'=>$KeyEnd
])->result->message_id;
bot('pinChatMessage',[
'chat_id'=>$user,
'message_id' => $pin_id,
]);
}
unlink("play/$server/lock.txt");
sleep(30);
$players = file_get_contents("play/$server/newgame.txt");
$value = explode("\n","$players");
foreach($value as $user){
$userD = mysqli_fetch_assoc(mysqli_query($connect,"SELECT * FROM user WHERE id = '$user' LIMIT 1"));
$ezzz = $userD["game"] + 1;
$connect->query("UPDATE user SET game = '$ezzz' , step = 'none' , server = 'null' WHERE id = '$user' LIMIT 1");
bot('unpinAllChatMessages',[
'chat_id'=>$user,
]);
bot('sendMessage',[
'chat_id'=>$user,
'text'=>"
منو اصلی 👇
",
'parse_mode'=>'MarkDown',
'reply_markup'=>$KeyHome
]);
}
DeleteFolder("play/$server");
break;
}
}
}
}
}
}
if(file_exists("error_log")) unlink("error_log");
?>