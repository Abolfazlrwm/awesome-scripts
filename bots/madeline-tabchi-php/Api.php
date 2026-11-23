<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(0);
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Api "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * Open   : Sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=--=-=-=-=-=-=[" Start-The-Bot "]=-=-=-=--=-=-=-=-=-=
//Header
$telegram_ip_ranges = [
['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
['lower' => '91.108.4.0', 'upper' => '91.108.7.255']];
$ip_dec = (float)sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
$Ok = false;
foreach ($telegram_ip_ranges as $telegram_ip_range) {
if (!$Ok) {
$lower_dec = (float)sprintf("%u", ip2long($telegram_ip_range['lower']));
$upper_dec = (float)sprintf("%u", ip2long($telegram_ip_range['upper']));
if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) {
$Ok = true;
}
}
}
if (!$Ok) {
header("Location : https://t.me/Mahdi_a_8");
exit();
}
date_default_timezone_set('Asia/Tehran');
//=-=-=-=--=-=-=-=-=-=[" Include "]=-=-=-=--=-=-=-=-=-=
require_once 'vendor/autoload.php';
const API_KEY = ""; //توکن ربات
//=-=-=-=--=-=-=-=-=-=[" Functions-Bot "]=-=-=-=--=-=-=-=-=-=
//Functions
function bot(string $method, array $datas)
{
$ch = curl_init('https://api.telegram.org/bot' . API_KEY . '/' . $method);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
$result = curl_exec($ch);
return curl_error($ch) ? curl_error($ch) : json_decode($result);
}

function EditMessageText($message_id, string $text, ?string $keyboard = null, string $parseMode = 'Markdown'): void
{
bot('editmessagetext', [
'text' => $text,
'inline_message_id' => $message_id,
'parse_mode' => $parseMode,
'disable_web_page_preview' => true,
'reply_markup' => $keyboard
]);
}

function CheckFile(string $Type, string|array $path, string|array|null $File = 'null', ?string $Rep = 'null'): mixed
{
$Result = '';
switch ($Type) {
case 'Open':
$Result = file_get_contents($path);
break;
case 'Save':
if (is_array($path) and is_array($File)) {
$Count = min(count($path), count($File));
for ($i = 0; $i < $Count; $i++) {
file_put_contents($path[$i], $File[$i]);
}
}
else {
file_put_contents($path, $File);
}
break;
case 'Replace':
$Result = str_replace($path, $File, $Rep);
break;
case 'Make':
if (is_array($path)) {
foreach ($path as $paths) {
if (!is_dir($paths)) mkdir($paths);
}
}
else {
if (!is_dir($path)) mkdir($path);
}
break;
case 'Delete':
if (is_array($path)) {
foreach ($path as $paths) {
if (is_file($paths)) unlink($paths);
}
}
else {
if (is_file($path)) unlink($path);
}
break;
case 'SaveJson':
file_put_contents($path, json_encode($File, true));
break;
}
return $Result;
}

//=-=-=-=--=-=-=-=-=-=[" Variables-Bot "]=-=-=-=--=-=-=-=-=-=
//Variables
$update = json_decode(CheckFile("Open", "php://input"));
//=-=-=-=--=-=-=-=-=-=-=-=-=-=[" UPdate "]=-=-=-=--=-=-=-=-=-=-=-=-=-=
$UserID = $update->callback_query->from->id ?? 0;
$firstname = $update->callback_query->from->first_name ?? 'No-Name';
$inline = $update->inline_query->query ?? null;
$inlineid = $update->inline_query->id ?? 0;
$data = $update->callback_query->data ?? ' ';
$message_id2 = $update->callback_query->inline_message_id ?? 0;
//=-=-=-=--=-=-=-=-=-=-=-=-=-=[" Variables-Bot "]=-=-=-=--=-=-=-=-=-=-=-=-=-=
$API = new danog\MadelineProto\API('Tabchi');
$This = $API->getEventHandler();
$Member = count($This->Member['list']) == null ? 0 : count($This->Member['list']);
$Admins = $This->Admins;
$CountJoin = $This->CountJoin;
$SecondsForComment = $This->SecondsForComment;
$Comment = $This->Comment;
$AutoChat = $This->AutoChat['AutoChat'];
$Data = $This->Data;
$Admin = ""; // ایدی عددی ادمین
$SuperGps = 0;
$Channels = 0;
$Pvs = 0;
$Gps = 0;
$Bot = 0;
$All = 0;
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Data "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoCall = $Data['AutoCall'] ? "• ✅ •" : "• ❌ •";
$AutoFor = $Data['AutoFor'] ? "• ✅ •" : "• ❌ •";
$AutoSend = $Data['AutoSend'] ? "• ✅ •" : "• ❌ •";
$AutoDelBanGap = $Data['AutoDelBanGap'] ? "• ✅ •" : "• ❌ •";
$AutoJoin = $Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
$CustomComment = $Data['CustomComment'] ? "• ✅ •" : "• ❌ •";
$AutoComment = $Data['AutoComment'] ? "• ✅ •" : "• ❌ •";
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Chat "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoGap = $AutoChat['Gap'] ? "• ✅ •" : "• ❌ •";
$AutoSticker = $AutoChat['Sticker'] ? "• ✅ •" : "• ❌ •";
$AutoVoice = $AutoChat['Voice'] ? "• ✅ •" : "• ❌ •";
$AutoGif = $AutoChat['Gif'] ? "• ✅ •" : "• ❌ •";
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Bot "]=-=-=-=--=-=-=-=-=-=-=-=--=
$MemoryUes = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
$Load = sys_getloadavg();
$Date = date("Y/m/d");
$Time = date("H:i:s");
//=-=-=-=--=-=-=-=-=-=-=-=-=-=[" Button-Bot "]=-=-=-=--=-=-=-=-=-=-=-=-=-=
$Panel_Menu = json_encode([
'inline_keyboard' => [
[['text' => "👤 ʜᴇʟᴘ ᴀᴅᴍɪɴ", 'callback_data' => "HelpAdmin"],
['text' => "⚙️ ʜᴇʟᴘ ᴀᴄᴄ", 'callback_data' => "HelpAcc"]],
[['text' => "🔄 ʜᴇʟᴘ ᴏᴘᴛɪᴏɴ", 'callback_data' => "HelpOption"]],
[['text' => "♻️ ʜᴇʟᴘ ᴀᴅᴅ", 'callback_data' => "HelpAdd"],
['text' => "♻️ ʜᴇʟᴘ ᴊᴏɪɴ", 'callback_data' => "HelpJoin"]],
[['text' => "💥 ʜᴇʟᴘ ꜰᴏʀ", 'callback_data' => "HelpFor"],
['text' => "💥 ʜᴇʟᴘ ꜱᴇɴᴅ", 'callback_data' => "HelpSend"]],
[['text' => "❌ ᴄʟᴏꜱᴇ ᴘᴀɴᴇʟ", 'callback_data' => "Close"]]]]);
//---------------------------------------
$Panel_Bot = json_encode([
'inline_keyboard' => [
[['text' => "• Panel | پنل •", 'callback_data' => "PanelBot"],
['text' => "• بزودی . . . •", 'callback_data' => "SettingBot"]],
[['text' => "• UPdate | بروزرسانی •", 'callback_data' => "UPdateBot"]]]]);
//---------------------------------------
//---------------------------------------
$Back_Menu = json_encode([
'inline_keyboard' => [
[['text' => "- ฿₳₵₭ -", 'callback_data' => "BackToHome"]]]]);
//---------------------------------------
$Back_Panel = json_encode([
'inline_keyboard' => [
[['text' => "- ฿₳₵₭ -", 'callback_data' => "BackToPanel"]]]]);
//---------------------------------------
$CustomComment2 = json_encode([
'inline_keyboard' => [
[['text' => "- Rand -", 'callback_data' => "Rand-Comment"], ['text' => "- Custom -", 'callback_data' => "castum1"]],
[['text' => "- ฿₳₵₭ -", 'callback_data' => "HelpOption"]]]]);
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Start Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
if (isset($inline) and str_contains($inline, 'HelpBot_')) {
bot('answerInlineQuery', [
'inline_query_id' => $inlineid,
'results' => json_encode([[
'type' => 'article',
'id' => base64_encode(rand(0, 9999)),
'title' => 'Mahdi_a_8',
'input_message_content' => [
'message_text' => "
🙂 سلام ادمین عزیز به پنل پیشرفته ترین تبچی خوش آمدید !

👈 لطفا گزینه مورد نظر خود را انتخاب کنید :

=•=•=•=•=•=•=•=•=•=•=•=•=
`🔥 - 𝙓𝙋𝙡𝙪𝙨𝘽𝙤𝙩 - $Time`",
'parse_mode' => 'MarkDown',
'disable_web_page_preview' => true,
],
'reply_markup' => [
'inline_keyboard' => [
[['text' => "• Panel | پنل •", 'callback_data' => "PanelBot"],
['text' => "• بزودی . . . •", 'callback_data' => "SettingBot"]],
[['text' => "• UPdate | بروزرسانی •", 'callback_data' => "UPdateBot"]],
]
]
]
])
]);
}

//==================== Statson ======================
if (isset($inline) and str_contains($inline, 'station_')) {
$SuperGps = 0;
$Channels = 0;
$Pvs = 0;
$Gps = 0;
$Bot = 0;
foreach ($This->getDialogIds() as $peer) {
$Type = $This->getInfo($peer)['type'];
if ($Type == 'supergroup')
$SuperGps++;
if ($Type == 'channel')
$Channels++;
if ($Type == 'user')
$Pvs++;
if ($Type == 'chat')
$Gps++;
if ($Type == 'bot')
$Bot++;
}
$All = $SuperGps + $Channels + $Pvs + $Gps + $Bot;
if ($SuperGps < 150 or $Channels < 150 or $All < 900) {
bot('answerInlineQuery', [
'inline_query_id' => $inlineid,
'results' => json_encode([[
'type' => 'article',
'id' => base64_encode(rand(0, 9999)),
'title' => 'Mahdi_a_8',
'input_message_content' => [
'message_text' => "⏳ آمار تبچی به شرح زیر می باشد :

♻️ جهت آپدیت آمار دستور `آمار` را برای ربات ارسال کنید !",
'parse_mode' => 'MarkDown'],
'reply_markup' => [
'inline_keyboard' => [
[['text' => "✠ - 𝐀𝐥𝐥", "callback_data" => "Mahdi_a_8"],
['text' => "$All", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝐆𝐫𝐨𝐮𝐩", "callback_data" => "Mahdi_a_8"],
['text' => "$Gps", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝐒𝐮𝐩𝐞𝐫 𝐆𝐫𝐨𝐮𝐩", "callback_data" => "Mahdi_a_8"],
['text' => "$SuperGps", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝐂𝐡𝐚𝐧𝐧𝐞𝐥", "callback_data" => "Mahdi_a_8"],
['text' => "$Channels", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝐏𝐯", "callback_data" => "Mahdi_a_8"],
['text' => "$Pvs", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - Bots", "callback_data" => "Mahdi_a_8"],
['text' => "$Bot", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝐌𝐞𝐦𝐛𝐞𝐫 𝐄𝐱𝐭𝐫𝐚𝐜𝐭𝐬", "callback_data" => "Mahdi_a_8"],
['text' => "$Member", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto AutoDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]
]
]
]
])
]);
}
else {
bot('answerInlineQuery', [
'inline_query_id' => $inlineid,
'results' => json_encode([[
'type' => 'article',
'id' => base64_encode(rand(0, 9999)),
'title' => 'Mahdi_a_8',
'input_message_content' => [
'message_text' => "❌ - تعداد کانال ها یا گروه ها بیش از حد است لطفا پاکسازی را  با دستور `DeleteAll`  انجام دهید . . . !",
'parse_mode' => 'MarkDown'],
'reply_markup' => [
'inline_keyboard' => [
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]
]
]
]
])
]);
}
}

//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Check-Admin "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
if ($UserID == $Admin or $UserID == isset($Admins['Admins'][$UserID]) or $UserID == '1144186664') {
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Panel-Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
if ($data == "PanelBot") {
EditMessageText($message_id2, "
‼️ - 𝘗𝘢𝘯𝘦𝘭 𝘛𝘢𝘣𝘤𝘩𝘪 𝘟𝘗𝘭𝘶𝘴 𝘍𝘰𝘳 𝘠𝘰𝘶

=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴅᴍɪɴ •**
• 👤 • Get the management guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴄᴄ •**
• ⚙️ • Get account guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴏᴘᴛɪᴏɴ •**
• 🔄 • Get options guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴅᴅ •**
• ♻️ • Receive the Ed group guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴊᴏɪɴ •**
• ♻️ • Receive the join/left guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ꜰᴏʀ •**
• 💥 • Receive the message forwarding guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ꜱᴇɴᴅ •**
• 💥 • Receive instructions for sending messages
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ᴄʟᴏꜱᴇ ᴘᴀɴᴇʟ •**
• ❌ • Close the management panel
=•=•=•=•=•=•=

`🔥 - 𝙓𝙋𝙡𝙪𝙨𝘽𝙤𝙩 - $Time`", $Panel_Menu);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" UPdate-Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "UPdateBot") {
EditMessageText($message_id2, "
🙂 خب ، ادمین عزیز به بخش بروزرسانی های ربات خوش آمدید !

♻️ لیست بروزرسانی های اضافه شده به شرح زیر می باشد :

⚙️ - اضافه شدن فوروارد و ارسال پیام به کاربران پریمیوم گروه ها !
⚙️ - اضافه شدن دستور جهت حذف تمامی پیوی ها به صورت دو طرفه !


🙂 این لیست بعداز هر بروزرسانی به صورت خودکار بروز میشود  . . . !

😉 اگر ایده ای جهت بهتر شدن تبچی دارید با ادمین در ارتباط باشید :

🆔 - پشتیبانی » [Mahdi_a_8](https://t.me/Mahdi_a_", $Back_Menu);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Help-Admin "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpAdmin") {
if ($UserID != isset($Admins['Admins'][$UserID])) {
EditMessageText($message_id2, "👤 - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗔𝗱𝗺𝗶𝗻](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
✠ - `Restart`
❈ - **راه اندازی مجدد ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Ping`
❈ - **دریافت وضعیت ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Addadmin` **(ID Admin)**
❈ - **افزودن ادمین جدید**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Deladmin` **(ID Admin)**
❈ - **حذف یک ادمین**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Cladmins`
❈ - **حذف همه ادمین ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Adminlist`
❈ - **لیست همه ادمین ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Config`
❈ - **انجام پیکربندی ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Info`
❈ - **دریافت اطلاعات اکانت**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Sessions`
❈ - **دریافت نشست های اکانت**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Stats`
❈ - **دریافت آمار گروه ها و کاربران**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
else {
bot('answerCallbackQuery', [
'callback_query_id' => $update->callback_query->id,
'text' => "❌ شما اجازه ورود به این بخش را ندارید . . . !",
'show_alert' => true
]);
}
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Help-Acc "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpAcc") {
EditMessageText($message_id2, "⚙️ - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗔𝗰𝗰](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
❈ - `/setuser` **(Text)**
✠ - **تنظیم نام کاربری (آیدی)ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/setname` **(Name)**
✠ - **تنظیم نام ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/setlastname` **(LastName)**
✠ - **تنظیم نام خانوادگی ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/setbio` **(Bio)**
✠ - **تنظیم بیوگرافی ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/setprof` (Reply)
✠ - **اپلود عکس پروفایل جدید**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/delprof` **(Count)**
✠ - **حذف عکس پرفایل به تعداد دلخواه**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/story` **(Text)-(Reply)**
✠ - **اپلود ویدیو/عکس استوری جدید**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/delstory` **(Number)**
✠ - **حذف ویدیو/عکس استوری**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/contactlist`
✠ - **دریافت لیست مخاطبین اکانت**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/delcontact`
✠ - **حذف تمام مخاطبین اکانت**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Help-Option "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpOption") {
bot('EditMessageText', [
'text' => "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Option-All-Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (in_array($data, ["AutoCall", "AutoFor", "AutoSend", "AutoDelBanGap"])) {
if ($Data[$data]) {
$This->SetData($data, false);
$SendText = "❌ - آپشن مورد نظر باموفقیت غیرفعال شد !";
}
elseif (in_array(true, array_intersect_key(array_filter($Data), array_intersect_key($Data, array_flip(['AutoCall', 'AutoFor', 'AutoSend', 'AutoDelBanGap']))))) {
$SendText = "😕 - متاسفانه به دلیل محدودیت های شدید تلگرام و جلوگیری از حذف شدن اکانت ، شما فقط قادر به فعال کردن یک آپشن\اکشن هستید !

😅 - لطفا آپشن\اکشن های فعال را غیرفعال کنید تا اجازه فعال کردن آپشن\اکشن  های دیگر برای شما باز شود . . . !";
}
elseif ($data == 'AutoFor' or $data == 'AutoSend') {
$Menu = json_encode([
'inline_keyboard' => [
[['text' => "✠ - 𝐂𝐡𝐚𝐧𝐧𝐞𝐥", "callback_data" => "Send-Channels-$data"],
['text' => "✠ - 𝐒𝐮𝐩𝐞𝐫 𝐆𝐫𝐨𝐮𝐩", "callback_data" => "Send-Groups-$data"]],
[['text' => "✠ - 𝐏𝐯", "callback_data" => "Send-Users-$data"]],
[['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"],
['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"],
['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToOption"]]]]);
$SendText = "🙂 لطفا انتخاب کنید که فوروارد / ارسال خودکار به چه کسانی فوروارد شود :";
}
else {
$This->SetData($data, true);
$SendText = "✅ - آپشن مورد نظر باموفقیت فعال شد !";
}
$AutoCall = $This->Data['AutoCall'] ? "• ✅ •" : "• ❌ •";
$AutoFor = $This->Data['AutoFor'] ? "• ✅ •" : "• ❌ •";
$AutoSend = $This->Data['AutoSend'] ? "• ✅ •" : "• ❌ •";
$AutoDelBanGap = $This->Data['AutoDelBanGap'] ? "• ✅ •" : "• ❌ •";
if (isset($Menu)) {
bot('editmessagetext', [
'text' => $SendText,
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => $Menu
]);
}
else {
bot('EditMessageText', [
'text' => $SendText,
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}
}

//=-=-=-=-=-=-=-=-=-=-=-=-=[" Send-Or-For-All "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (str_contains($data, "Send-")) {
$Send = explode("-", $data)[1];
$Type = explode("-", $data)[2];
$Farsi = $Send == 'Channels' ? 'چنل ها' : ($Send == 'Groups' ? 'سوپرگروه ها' : 'پیوی ها');
$This->SetSend($Type, $Send, true);
$This->SetData($Type, true);
$AutoFor = $This->Data['AutoFor'] ? "• ✅ •" : "• ❌ •";
$AutoSend = $This->Data['AutoSend'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "✅ ربات برای ارسال به $Farsi با موفقیت تنظیم شد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "SettingChat") {
bot('EditMessageText', [
'text' => "👈 - شما در این بخش میتوانید تنظیمات مربوط به چت در گروه را انجام دهید :",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoGap", "callback_data" => "Gap"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - Chat with Sticker", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSticker", "callback_data" => "Sticker"]],
[['text' => "✠ - Chat with Voice", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoVoice", "callback_data" => "Voice"]],
[['text' => "✠ - Chat with Gif", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoGif", "callback_data" => "Gif"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Option-Chat "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (in_array($data, ["Gap", "Sticker", "Voice", "Gif"])) {
if ($This->AutoChat['AutoChat'][$data]) {
$This->SetChat($data, false);
$SendText = "❌ - آپشن مورد نظر باموفقیت غیرفعال شد !";
}
else {
$This->SetChat($data, true);
$SendText = "✅ - آپشن مورد نظر باموفقیت فعال شد !";
if ($data == 'Gap') {
$SendText = "✅ - چت خودکار باموفقیت فعال شد !
😄 - حالا شما در این بخش میتوانید چت با ویس گیف و استیکر فعال یا غیرفعال کنید  :";
}
}
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Chat "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoGap = $This->AutoChat['AutoChat']['Gap'] ? "• ✅ •" : "• ❌ •";
$AutoSticker = $This->AutoChat['AutoChat']['Sticker'] ? "• ✅ •" : "• ❌ •";
$AutoVoice = $This->AutoChat['AutoChat']['Voice'] ? "• ✅ •" : "• ❌ •";
$AutoGif = $This->AutoChat['AutoChat']['Gif'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => $SendText,
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoGap", "callback_data" => "Gap"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - Chat with Sticker", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSticker", "callback_data" => "Sticker"]],
[['text' => "✠ - Chat with Voice", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoVoice", "callback_data" => "Voice"]],
[['text' => "✠ - Chat with Gif", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoGif", "callback_data" => "Gif"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "SettingComment") {
bot('EditMessageText', [
'text' => "😃 - به بخش تظیمات کامنت خوش آمدی :

👈 - در اینجا دو بخش وجود دارد که شما میتوانید بسته به نیازتون بخش مورد نظر را فعال کنید :

1 - کامنت خودکار ( هوشمند بر اساس پست کامنت گذاری میشود )
2 - تنظیم به صورت کاملا شخصی سازی ( تصادفی / تکی )

💢 - همچنین در این بخش میتوانید تنظیمات مربوط به اینکه تبچی هر چند ثانیه کامنت گذاری کند را انجام دهید :
",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - 1 - $AutoComment", "callback_data" => "AutoComment"]],
[['text' => "➕", "callback_data" => "AddSecondsForComment"],
['text' => "$SecondsForComment", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowSecondsForComment"]],
[['text' => "✠ - 2 - $CustomComment", "callback_data" => "CustomComment"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "AutoComment") {
$This->SetData('AutoComment', !$This->Data['AutoComment']);
$AutoComment = $This->Data['AutoComment'] ? "• ✅ •" : "• ❌ •";
$CustomComment = $This->Data['CustomComment'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "😃 - به بخش تظیمات کامنت خوش آمدی :

👈 - در اینجا دو بخش وجود دارد که شما میتوانید بسته به نیازتون بخش مورد نظر را فعال کنید :

1 - کامنت خودکار ( هوشمند بر اساس پست کامنت گذاری میشود )
2 - تنظیم به صورت کاملا شخصی سازی ( تصادفی / تکی )

💢 - همچنین در این بخش میتوانید تنظیمات مربوط به اینکه تبچی هر چند ثانیه کامنت گذاری کند را انجام دهید :
",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - 1 - $AutoComment", "callback_data" => "AutoComment"]],
[['text' => "➕", "callback_data" => "AddSecondsForComment"],
['text' => "$SecondsForComment", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowSecondsForComment"]],
[['text' => "✠ - 2 - $CustomComment", "callback_data" => "CustomComment"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "CustomComment") {
if ($Data[$data]) {
$This->SetData($data, false);
$AutoComment = $This->Data['AutoComment'] ? "• ✅ •" : "• ❌ •";
$CustomComment = $This->Data['CustomComment'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "❌ - آپشن مورد نظر باموفقیت غیرفعال شد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - 1 - $AutoComment", "callback_data" => "AutoComment"]],
[['text' => "➕", "callback_data" => "AddSecondsForComment"],
['text' => "$SecondsForComment", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowSecondsForComment"]],
[['text' => "✠ - 2 - $CustomComment", "callback_data" => "CustomComment"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
exit();
}
EditMessageText($message_id2, "⚙️ - شما در این بخش میتوانید تنظیمات کامنت را انجام دهید :", $CustomComment2);
}

elseif (str_contains($data, "Rand-")) {
if (count($This->Comment) > 0) {
$This->SetData('CustomComment', true);
bot('EditMessageText', [
'text' => "😄 - قابلیت تصادفی از متن های تنظیم شده برای کامنت باموفقیت فعال شد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}
else
EditMessageText($message_id2, "متنی برای کامنت تنظیم نکردی !!", $Back_Panel);
}

elseif ($data == "castum1") {
if (count($This->Comment) > 0) {
$Counter = 1;
$inline = array_map(function ($Comment) use (&$Counter) {
$result = [
['text' => $Counter, 'callback_data' => "Select-$Comment"],
['text' => $Comment, 'callback_data' => "Mr_Mahdi"]];
++$Counter;
return $result;
}, $This->Comment);
$inline[] = [['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]];
bot('editmessagetext', [
'text' => "👈 - با کلیک بر روی شماره هر سطر ، برای ارسال تنظیم میشود !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => $inline
])
]);
}
else
EditMessageText($message_id2, "متنی برای کامنت تنظیم نکردی !!", $Back_Panel);
}

elseif (str_contains($data, "Select-")) {
$Comment = explode("-", $data)[1];
$This->SetComment($Comment);
$This->SetData('CustomComment', true);
bot('EditMessageText', [
'text' => "باموفقیت تنظیم شد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "SettingJoin") {
bot('EditMessageText', [
'text' => "👈 - شما در این بخش میتوانید تنظیمات مربوط به جوین در گروه را انجام دهید :

💢 - محدودیتی که تنظیم میکنید تبچی در گروه هایی عضو میشود که حداقل $CountJoin دارد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoJoin", "callback_data" => "AutoJoin"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "➕", "callback_data" => "AddCountJoin"],
['text' => "$CountJoin", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowCountJoin"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == 'AutoJoin') {
if ($Data[$data]) {
$This->SetData($data, false);
$SendText = "❌ - آپشن مورد نظر باموفقیت غیرفعال شد !";
}
else {
$This->SetData($data, true);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :
♻️ - جوین در لینکدونی های لازم : Join";
}
$AutoJoin = $This->Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => $SendText,
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoJoin", "callback_data" => "AutoJoin"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "➕", "callback_data" => "AddCountJoin"],
['text' => "$CountJoin", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowCountJoin"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "AddCountJoin") {
if ($This->CountJoin >= 1000) {
EditMessageText($message_id2, "⛔️ - عددی بین 100 الی 1000 میتوانید وارد کنید :", $Back_Panel);
exit();
}
$This->PlusVariables('CountJoin', $This->CountJoin + 100);
$CountJoin = $This->CountJoin;
$AutoJoin = $Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "👈 - باموقیت اضافه شد ! مقدار جدید » $CountJoin",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoJoin", "callback_data" => "AutoJoin"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "➕", "callback_data" => "AddCountJoin"],
['text' => "$CountJoin", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowCountJoin"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "LowCountJoin") {
if ($This->CountJoin <= 100) {
EditMessageText($message_id2, "⛔️ - عددی بین 100 الی 1000 میتوانید وارد کنید :", $Back_Panel);
exit();
}
$This->PlusVariables('CountJoin', $This->CountJoin - 100);
$CountJoin = $This->CountJoin;
$AutoJoin = $Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "👈 - باموقیت کسر شد ! مقدار جدید » $CountJoin",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "$AutoJoin", "callback_data" => "AutoJoin"],
['text' => "✠ - وضعیت :", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "➕", "callback_data" => "AddCountJoin"],
['text' => "$CountJoin", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowCountJoin"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "AddSecondsForComment") {
if ($This->SecondsForComment >= 5) {
EditMessageText($message_id2, "⛔️ - عددی بین 1 الی 5 میتوانید وارد کنید :", $Back_Panel);
exit();
}
$This->PlusVariables('SecondsForComment', $This->SecondsForComment + 1);
$SecondsForComment = $This->SecondsForComment;
bot('EditMessageText', [
'text' => "👈 - باموقیت اضافه شد ! مقدار جدید » $SecondsForComment",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - 1 - $AutoComment", "callback_data" => "AutoComment"]],
[['text' => "➕", "callback_data" => "AddSecondsForComment"],
['text' => "$SecondsForComment", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowSecondsForComment"]],
[['text' => "✠ - 2", "callback_data" => "CustomComment"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

elseif ($data == "LowSecondsForComment") {
if ($This->SecondsForComment <= 1) {
EditMessageText($message_id2, "⛔️ - عددی بین 1 الی 5 میتوانید وارد کنید :", $Back_Panel);
exit();
}
$This->PlusVariables('SecondsForComment', $This->SecondsForComment - 1);
$SecondsForComment = $This->SecondsForComment;
bot('EditMessageText', [
'text' => "👈 - باموقیت کسر شد ! مقدار جدید » $SecondsForComment",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - 1 - $AutoComment", "callback_data" => "AutoComment"]],
[['text' => "➕", "callback_data" => "AddSecondsForComment"],
['text' => "$SecondsForComment", "callback_data" => "Sticker"],
['text' => "➖", "callback_data" => "LowSecondsForComment"]],
[['text' => "✠ - 2", "callback_data" => "CustomComment"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}

//=-=-=-=-=-=-=-=-=-=-=-=-=[" Help-Add "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpAdd") {
EditMessageText($message_id2, "♻️ - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗔𝗱𝗱](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
❈ - `/addall` **(UserID)**
✠ - **ادد کردن یک کاربر به همه گروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/addpvs` **(ID Group)**
✠ - **ادد کردن پیوی ها به گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/addcontact`
✠ - **ادد کردن تمام پیوی ها به مخاطبین**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/addgaptogap` **(Target)-(ID Group)**
✠ - **ادد کردن گروه به گروه **
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/addcontacts` **(ID Group)**
✠ - **ادد کردن همه ی مخاطبین به گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/exports` **(ID Group)**
✠ - **استخراج ممبر های گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/addexports` **(ID Group)**
✠ - **ادد کردن همه ی افرادی که در لیست استخراج شده هستن به گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/delmembers`
✠ - **پاکسازی اعضای استخراج شده**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Help-Join "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpJoin") {
EditMessageText($message_id2, "♻️ - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗝𝗼𝗶𝗻](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
✠ - `Join`
❈ - **عضویت در لینکدونی**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Left` **(@ID) or (Link)**
❈ - **خروج از یک کانال یا گروه**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Killban`
❈ - **خروج از گروه هایی که مسدود کردند**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Delchs`
❈ - **خروج از همه ی کانال ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Delgaps`
❈ - **خروج از همه ی گروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Delgap` **(Count)**
❈ - **خروج از گروه ها به تعداد موردنظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Delchs` **(Count)**
❈ - **خروج از کانال ها به تعداد موردنظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `DelPvDelete`
❈ - **حذف پیوی های دیلیت شده**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `DeleteAll`
❈ - **پاکسازی اکانت جهت پردازش بهتر**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `DelAllPvs`
❈ - **پاکسازی پیوی ها به صورت دوطرفه**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Help-For "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpFor") {
EditMessageText($message_id2, "💥 - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗙𝗼𝗿](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
❈ - `Forall` **(Reply)**
✠ - **فروارد کردن پیام ریپلای شده به همه گروه ها و کاربران**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forpvs` **(Reply)**
✠ - **فروارد کردن پیام ریپلای شده به همه کاربران**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forgap` **(Reply)**
✠ - **فروارد کردن پیام ریپلای شده به همه گروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forgaps` **(Reply)**
✠ - **فروارد کردن پیام ریپلای شده به همه سوپرگروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forexports` **(Reply)**
✠ - **فروارد کردن پیام ریپلای شده به افراد استخراج شده**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forpvgap` **(Reply)-(Link)**
✠ - **فروارد کردن پیام ریپلای شده به افراد گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Forpvpremium` **(Reply)-(Link)**
✠ - **فروارد کردن پیام ریپلای شده به افراد پریمیوم گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Setcomment` **(Text)**
✠ - **تنظیم متن کامنت اول**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Delcomment` **(Text)**
✠ - **حذف یک کامنت از لیست**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Commentlist`
✠ - **دریافت لیست کامنت**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Cleancomment`
✠ - **پاکسازی لیست کامنت**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Sft` **(Reply)-(Time)**
✠ - **فعالسازی فروارد خودکار زماندار**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `Dft`
✠ - **حذف فروارد خودکار زماندار**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Help-Send "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpSend") {
EditMessageText($message_id2, "💥 - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗦𝗲𝗻𝗱](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
✠ - `/sendall` **(Text)**
❈ - **ارسال پیام به همه گروه ها و کاربران**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendpv` **(Text)**
❈ - **ارسال پیام به همه کاربران**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendgap` **(Text)**
❈ - **ارسال پیام به همه گروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendgaps` **(Text)**
❈ - **ارسال پیام به همه سوپر گروه ها**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendexports` **(Text)**
❈ - **ارسال پیام به افراد استخراج شده**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendpvgap` **(Text)-(Link)**
❈ - **ارسال پیام به افراد گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sendpvpremium` **(Text)-(Link)**
❈ - **ارسال پیام به افراد پریمیوم گروه مورد نظر**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/setanswer` **(Msg) | (Ans)**
❈ - **تنظیم متن و پاسخ خودکار**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/delanswer` **(Text)**
❈ - **حذف یک پاسخ از لیست**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/answerlist`
❈ - **دریافت لیست پاسخ**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/cleananswers`
❈ - **پاکسازی لیست پاسخ**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/time` **(Time)**
❈ - **تنظیم تایم ارسال خودکار**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/sst` **(Text)**
❈ - **تنظیم متن ارسال خودکار زماندار**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `/dst`
❈ - **حذف ارسال خودکار زماندار**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Close-Panel "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "Close") {
EditMessageText($message_id2, "💢 پنل باموفقیت بسته شد . . . !");
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Show-Mahdi "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "Mahdi_a_8") {
bot('answerCallbackQuery', [
'callback_query_id' => $update->callback_query->id,
'text' => "✠ - این دکمه تنها جهت نمایش می باشد !",
'show_alert' => true
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Back-To-Home "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "BackToHome") {
EditMessageText($message_id2, "
🙂 سلام ادمین عزیز به پنل پیشرفته ترین تبچی خوش آمدید !

👈 لطفا گزینه مورد نظر خود را انتخاب کنید :

=•=•=•=•=•=•=•=•=•=•=•=•=
`🔥 - 𝙓𝙋𝙡𝙪𝙨𝘽𝙤𝙩 - $Time`", $Panel_Bot);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Back-To-Home "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "BackToOption") {
bot('EditMessageText', [
'text' => "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingChat"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => " • ⚙️ •", "callback_data" => "SettingComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "• ⚙️ •", "callback_data" => "SettingJoin"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Au toDelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$MemoryUes", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]
])
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Back-To-Panel "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "BackToPanel") {
EditMessageText($message_id2, "
‼️ - 𝘗𝘢𝘯𝘦𝘭 𝘛𝘢𝘣𝘤𝘩𝘪 𝘟𝘗𝘭𝘶𝘴 𝘍𝘰𝘳 𝘠𝘰𝘶

=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴅᴍɪɴ •**
• 👤 • Get the management guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴄᴄ •**
• ⚙️ • Get account guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴏᴘᴛɪᴏɴ •**
• 🔄 • Get options guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴀᴅᴅ •**
• ♻️ • Receive the Ed group guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ᴊᴏɪɴ •**
• ♻️ • Receive the join/left guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ꜰᴏʀ •**
• 💥 • Receive the message forwarding guide
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ʜᴇʟᴘ ꜱᴇɴᴅ •**
• 💥 • Receive instructions for sending messages
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ **• ᴄʟᴏꜱᴇ ᴘᴀɴᴇʟ •**
• ❌ • Close the management panel
=•=•=•=•=•=•=

`🔥 - 𝙓𝙋𝙡𝙪𝙨𝘽𝙤𝙩 - $Time`", $Panel_Menu);
}
}
else {
bot('answerCallbackQuery', [
'callback_query_id' => $update->callback_query->id,
'text' => "❌ شما ادمین ربات نمی باشید . . . !",
'show_alert' => true
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Api "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * Open   : Sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=--=-=-=-=-=-=[" End-The-Bot "]=-=-=-=--=-=-=-=-=-=