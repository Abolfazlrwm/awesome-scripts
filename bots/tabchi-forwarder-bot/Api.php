<?php
error_reporting(0);
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Api "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v188
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=--=-=-=-=-=-=[" Start-The-Bot "]=-=-=-=--=-=-=-=-=-=
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
require_once 'madeline.php';

const API_KEY = ""; // !!! Change this to your Token !!
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
} else {
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
} else {
if (!is_dir($path)) mkdir($path);
}
break;
case 'Delete':
if (is_array($path)) {
foreach ($path as $paths) {
if (is_file($paths)) unlink($paths);
}
} else {
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
$UserID      = $update->callback_query->from->id ?? 0;
$firstname   = $update->callback_query->from->first_name ?? 'No-Name';
$inline      = $update->inline_query->query ?? null;
$inlineid    = $update->inline_query->id ?? 0;
$data        = $update->callback_query->data ?? ' ';
$message_id2 = $update->callback_query->inline_message_id ?? 0;
//=-=-=-=--=-=-=-=-=-=-=-=-=-=[" Variables-Bot "]=-=-=-=--=-=-=-=-=-=-=-=-=-=
$API    = new danog\MadelineProto\API('Tabchi');
$This   = $API->getEventHandler();
$Member = $This->Member['list'];
$Member = count($Member) == null ? 0 : count($Member);
$Admin  = "1144186664"; // !!! Change this to your ID !!
$Admins = $This->Admins;
$SuperGps = 0;
$Channels = 0;
$Pvs = 0;
$Gps = 0;
$Bot = 0;
$All = 0;
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Chat "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoChat    = $This->AutoChat['AutoChat']['Gap'] ? "• ✅ •" : "• ❌ •";
$AutoSticker = $This->AutoChat['AutoChat']['Sticker'] ? "• ✅ •" : "• ❌ •";
$AutoVoice   = $This->AutoChat['AutoChat']['Voice'] ? "• ✅ •" : "• ❌ •";
$AutoGif     = $This->AutoChat['AutoChat']['Gif'] ? "• ✅ •" : "• ❌ •";
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Datas "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoCall       = $This->Data['AutoCall'] ? "• ✅ •" : "• ❌ •";
$AutoFor        = $This->Data['AutoFor'] ? "• ✅ •" : "• ❌ •";
$AutoSend       = $This->Data['AutoSend'] ? "• ✅ •" : "• ❌ •";
$AutoSendNumber = $This->Data['AutoSendNumber'] ? "• ✅ •" : "• ❌ •";
$AutoJoin       = $This->Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
$AutoDelBanGap  = $This->Data['AutoDelBanGap'] ? "• ✅ •" : "• ❌ •";
$AutoComment    = $This->Data['AutoComment'] ? "• ✅ •" : "• ❌ •";
$Memoryues      = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
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
[['text' => "🔄 ꜱᴛᴀᴛꜱ", 'callback_data' => "Stats"]],
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
$Option_Menu = json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoChat", "callback_data" => "AutoChat"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoComment", "callback_data" => "AutoComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoJoin", "callback_data" => "AutoJoin"]],
[['text' => "✠ - Auto DelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "✠ - Auto SendNumber", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSendNumber", "callback_data" => "AutoSendNumber"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]]);
//---------------------------------------
$ShowOption_Menu = json_encode([
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
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoChat", "callback_data" => "AutoChat"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoComment", "callback_data" => "AutoComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoJoin", "callback_data" => "AutoJoin"]],
[['text' => "✠ - Auto DelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "✠ - Auto SendNumber", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSendNumber", "callback_data" => "AutoSendNumber"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]]);
//---------------------------------------
$SendOrFor_Menu = json_encode([
'inline_keyboard' => [
[['text' => "✠ - 𝐂𝐡𝐚𝐧𝐧𝐞𝐥", "callback_data" => "SendChannel"],
['text' => "✠ - 𝐒𝐮𝐩𝐞𝐫 𝐆𝐫𝐨𝐮𝐩", "callback_data" => "SendSuperGroup"]],
[['text' => "✠ - 𝐏𝐯", "callback_data" => "SendPv"]],
[['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"],
['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"],
['text' => "=•=•=•=•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToOption"]]]]);
//---------------------------------------
$AutoChat_Menu = json_encode([
'inline_keyboard' => [
[['text' => "✠ - Chat with Sticker", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSticker", "callback_data" => "AutoSticker"]],
[['text' => "✠ - Chat with Voice", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoVoice", "callback_data" => "AutoVoice"]],
[['text' => "✠ - Chat with Gif", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoGif", "callback_data" => "AutoGif"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]]]);
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Start Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
if (isset($inline) and str_contains($inline, 'helpbot_')) {
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
if (isset($inline) and str_contains($inline, 'statson_')) {
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
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoChat", "callback_data" => "AutoChat"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoComment", "callback_data" => "AutoComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoJoin", "callback_data" => "AutoJoin"]],
[['text' => "✠ - Auto DelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "✠ - Auto SendNumber", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSendNumber", "callback_data" => "AutoSendNumber"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]
]
]
]
])
]);
if ($SuperGps > 350 or $Channels > 200)
EditMessageText($message_id2, "❌ تعداد کانال ها یا گروه ها بیش از حد زیاد است لطفا پاکسازی را  با دستور `پاکسازی`  انجام دهید . . . !");
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Check-Admin "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
if ($UserID == $Admin or $Admin == isset($Admins['Admins'][$UserID])) {
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
✠ **• ꜱᴛᴀᴛꜱ •**
• 🔄 • Get complete account statistics
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

⚙️ افزایش سرعت و رفع باگ های گزارش شده !

🙂 این لیست بعداز هر بروزرسانی به صورت خودکار بروز میشود  . . . !

😉 اگر ایده ای جهت بهتر شدن تبچی دارید با ادمین در ارتباط باشید :

🆔 - پشتیبانی » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Menu);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Help-Admin "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpAdmin") {
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
❈ - `info`
✠ - **دریافت اطلاعات اکانت**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `sessions`
✠ - **دریافت نشست های اکانت**
=•=•=•=•=•=•=•=•=•=•=•=•=
✠ - `Stats`
❈ - **دریافت آمار گروه ها و کاربران**
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=-[" Help-Acc "]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "HelpAcc") {
EditMessageText($message_id2, "⚙️ - [𝑻𝒂𝒃𝒄𝒉𝒊 𝑿𝑷𝒍𝒖𝒔](https://t.me/source_s) 𝑯𝒆𝒍𝒑 » [𝗔𝗰𝗰](tg://user?id=$Admin) . . . !

=•=•=•=•=•=•=
❈ - `/setuser` **(Text)**
✠ - **تنظیم نام کاربری (آیدی)ربات**
=•=•=•=•=•=•=•=•=•=•=•=•=
❈ - `/prof` **(Name) | (LastName) | (Bio)**
✠ - **تنظیم نام اسم ,فامیلو بیوگرافی ربات**
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
EditMessageText($message_id2, "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :", $Option_Menu);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Option-All-Bot "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (in_array($data, ["AutoChat", "AutoCall", "AutoFor", "AutoSend", "AutoComment", "AutoJoin", "AutoDelBanGap", "AutoSendNumber"])) {
$Menu = false;
$Menu2 = false;
$SendText = '';
switch ($data) {
case 'AutoChat':
if (!$This->AutoChat['AutoChat']['Gap']) {
$This->SetChat('Gap', true);
$Menu2 = true;
$SendText = "✅ - چت خودکار باموفقیت فعال شد !
😄 - حالا شما در این بخش میتوانید چت با ویس گیف و استیکر فعال یا غیرفعال کنید  :";
}
else {
$This->SetChat('Gap', false);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
$Menu2 = false;
}
break;
case 'AutoCall':
$This->Seter('AutoCall', !$This->Data['AutoCall']);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
break;
case 'AutoFor':
if ($This->Data['AutoSend']) {
$SendText = "❌ ادمین گرامی در زمانی که سندخودکارفعال باشد امکان فعال کردن فوروارد خودکار نمیباشد . . . !";
}
else {
$This->Seter('AutoFor', !$This->Data['AutoFor']);
if ($This->Data['AutoFor']) {
$Menu = true;
CheckFile("Save", 'Type.txt', 'For');
$SendText = "🙂 لطفا انتخاب کنید که فوروارد خودکار به چه کسانی فوروارد شود:";
}
else {
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
}
}
break;
case 'AutoSend':
if ($This->Data['AutoFor']) {
$SendText = "❌ ادمین گرامی در زمانی که فوروارد خودکار فعال باشد امکان فعال کردن سند خودکار نمیباشد . . . !";
}
else {
$This->Seter('AutoSend', !$This->Data['AutoSend']);
if ($This->Data['AutoSend']) {
$Menu = true;
CheckFile("Save", 'Type.txt', 'Send');
$SendText = "🙂 لطفا انتخاب کنید که ارسال خودکار به چه کسانی ارسال شود :";
}
else {
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
}
}
break;
case 'AutoComment':
$This->Seter('AutoComment', !$This->Data['AutoComment']);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
break;
case 'AutoJoin':
$This->Seter('AutoJoin', !$This->Data['AutoJoin']);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
break;
case 'AutoSendNumber':
$This->Seter('AutoSendNumber', !$This->Data['AutoSendNumber']);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
break;
case 'AutoDelBanGap':
$This->Seter('AutoDelBanGap', !$This->Data['AutoDelBanGap']);
$SendText = "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :";
break;
}
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Datas "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoCall = $This->Data['AutoCall'] ? "• ✅ •" : "• ❌ •";
$AutoFor = $This->Data['AutoFor'] ? "• ✅ •" : "• ❌ •";
$AutoSend = $This->Data['AutoSend'] ? "• ✅ •" : "• ❌ •";
$AutoSendNumber = $This->Data['AutoSendNumber'] ? "• ✅ •" : "• ❌ •";
$AutoJoin = $This->Data['AutoJoin'] ? "• ✅ •" : "• ❌ •";
$AutoDelBanGap = $This->Data['AutoDelBanGap'] ? "• ✅ •" : "• ❌ •";
$AutoComment = $This->Data['AutoComment'] ? "• ✅ •" : "• ❌ •";
if ($Menu) {
EditMessageText($message_id2, $SendText, $SendOrFor_Menu);
}
elseif ($Menu2) {
EditMessageText($message_id2, $SendText, $AutoChat_Menu);
} else {
bot('EditMessageText', [
'text' => $SendText,
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Auto Chat", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoChat", "callback_data" => "AutoChat"]],
[['text' => "✠ - Auto Call", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoCall", "callback_data" => "AutoCall"]],
[['text' => "✠ - Auto For", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoFor", "callback_data" => "AutoFor"]],
[['text' => "✠ - Auto Send", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSend", "callback_data" => "AutoSend"]],
[['text' => "✠ - Auto Comment", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoComment", "callback_data" => "AutoComment"]],
[['text' => "✠ - Auto Join", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoJoin", "callback_data" => "AutoJoin"]],
[['text' => "✠ - Auto DelBanGap", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoDelBanGap", "callback_data" => "AutoDelBanGap"]],
[['text' => "✠ - Auto SendNumber", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSendNumber", "callback_data" => "AutoSendNumber"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]
]
])
]);
}
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Option-Chat "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (in_array($data, ["AutoSticker", "AutoVoice", "AutoGif"])) {
$SendText = '';
switch ($data) {
case 'AutoSticker':
$This->SetChat('Sticker', !$This->AutoChat['AutoChat']['Sticker']);
break;
case 'AutoVoice':
$This->SetChat('Voice', !$This->AutoChat['AutoChat']['Voice']);
break;
case 'AutoGif':
$This->SetChat('Gif', !$This->AutoChat['AutoChat']['Gif']);
break;
}
//=-=-=-=--=-=-=-=-=-=-=-=--=[" Stats-Chat "]=-=-=-=--=-=-=-=-=-=-=-=--=
$AutoChat    = $This->AutoChat['AutoChat']['Gap'] ? "• ✅ •" : "• ❌ •";
$AutoSticker = $This->AutoChat['AutoChat']['Sticker'] ? "• ✅ •" : "• ❌ •";
$AutoVoice   = $This->AutoChat['AutoChat']['Voice'] ? "• ✅ •" : "• ❌ •";
$AutoGif     = $This->AutoChat['AutoChat']['Gif'] ? "• ✅ •" : "• ❌ •";
bot('EditMessageText', [
'text' => "✅ - آپشن مورد نظر باموفقیت فعال شد !",
'inline_message_id' => $message_id2,
'parse_mode' => 'MarkDown',
'reply_markup' => json_encode([
'inline_keyboard' => [
[['text' => "✠ - Chat with Sticker", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoSticker", "callback_data" => "AutoSticker"]],
[['text' => "✠ - Chat with Voice", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoVoice", "callback_data" => "AutoVoice"]],
[['text' => "✠ - Chat with Gif", "callback_data" => "Mahdi_a_8"],
['text' => "$AutoGif", "callback_data" => "AutoGif"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗧𝗶𝗺𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Time", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗗𝗮𝘁𝗲", "callback_data" => "Mahdi_a_8"],
['text' => "$Date", "callback_data" => "Mahdi_a_8"]],
[['text' => "=•=•=•=•=•=•=•==•=•=", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗠𝗲𝗺𝗼𝗿𝘆 𝗨𝗦𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Memoryues", "callback_data" => "Mahdi_a_8"]],
[['text' => "✠ - 𝗣𝗶𝗻𝗴", "callback_data" => "Mahdi_a_8"],
['text' => "$Load[0]", "callback_data" => "Mahdi_a_8"]],
[['text' => "- ฿₳₵₭ -", "callback_data" => "BackToPanel"]]
]
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
✠ - `Join` **(@ID) or (Link)**
❈ - **عضویت در یک کانال یا گروه**
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
=•=•=•=•=•=•=

🆔 - 𝑺𝒖𝒑𝒑𝒐𝒓𝒕 » [Mahdi_a_8](https://t.me/Mahdi_a_8)", $Back_Panel);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Stats "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "Stats") {
EditMessageText($message_id2, "⏳ آمار تبچی به شرح زیر می باشد :

♻️ جهت آپدیت آمار دستور `آمار` را برای ربات ارسال کنید !", $ShowOption_Menu);
if ($SuperGps > 350 or $Channels > 200)
EditMessageText($message_id2, "❌ تعداد کانال ها یا گروه ها بیش از حد زیاد است لطفا پاکسازی را  با دستور `پاکسازی`  انجام دهید . . . !");
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
❈ - `/setcomment` **(Text)**
✠ - **تنظیم متن کامنت اول**
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
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Send-Or-For-All "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif (in_array($data, ["SendChannel", "SendSuperGroup", "SendPv"])) {
$Farsi = '';
$Open = CheckFile("Open", 'Type.txt');
$Type = $Open == 'For' ? "AutoFor" : "AutoChat";
switch ($data) {
case 'SendChannel':
$This->SetSend($Type, 'Channels', true);
$Farsi = 'چنل ها';
break;
case 'SendSuperGroup':
$This->SetSend($Type, 'Groups', true);
$Farsi = 'سوپرگروه ها';
break;
case 'SendPv':
$This->SetSend($Type, 'Users', true);
$Farsi = 'پیوی ها';
break;
}
EditMessageText($message_id2, "✅ ربات برای ارسال به $Farsi با موفقیت تنظیم شد !", $Option_Menu);
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Close-Panel "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "Close") {
EditMessageText($message_id2, "💢 پنل باموفقیت بسته شد . . . !");
}
//=-=-=-=-=-=-=-=-=-=-=-=-=[" Show-Mahdi "]=-=-=-=-=-=-=-=-=-=-=-=-=
elseif ($data == "Mahdi_a_8") {
bot('answerCallbackQuery', [
'callback_query_id' => $inlineid,
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
EditMessageText($message_id2, "👈 شما در این بخش میتوانید آپشن های ربات خود را فعال یا غیرفعال کنید :", $Option_Menu);
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
✠ **• ꜱᴛᴀᴛꜱ •**
• 🔄 • Get complete account statistics
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
} else {
bot('answerCallbackQuery', [
'callback_query_id' => $inlineid,
'text' => "❌ شما ادمین ربات نمی باشید . . . !",
'show_alert' => true
]);
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Api "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v188
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=--=-=-=-=-=-=[" End-The-Bot "]=-=-=-=--=-=-=-=-=-=