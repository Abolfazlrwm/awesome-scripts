<?php declare(strict_types=1);

//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Cli "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v188
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 * open : sourcekade
 * https://t.me/Sourrce_kade
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" Start "]=-=-=-=-=-=-=-=-=-=-=-=

namespace Madeline\Mahdi;

use danog\MadelineProto\{Exception, RemoteUrl, Logger, ParseMode, Settings, SimpleEventHandler, VoIP};
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\EventHandler\Message\{PrivateMessage, GroupMessage, ChannelMessage};
use danog\MadelineProto\EventHandler\Attributes\{Handler};
use danog\MadelineProto\Broadcast\Filter;
use danog\Loop\GenericLoop;
use danog\MadelineProto\RPCError\FloodWaitError as FloodError;
use Throwable;

if (!is_file('madeline.php')) {
copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

require_once 'madeline.php';

class MyEventHandler extends SimpleEventHandler
{

const ADMIN = "@Mahdi_a_8"; // !!! Change this to your UserName !!

public array $Member = array("list" => []);
public array $Answering = [];
public array $Admins = array("Admins" => []);
public mixed $Comment = null;
public array $AutoFor = array("Msg-ID" => 0, "Chat-ID" => 0, "Time" => 0, "Type" => array("Users" => false, "Groups" => true, "Channels" => false));
public array $AutoSend = array("Text" => '', "Time" => 0, "Type" => array("Users" => false, "Groups" => true, "Channels" => false));
public array $AutoChat = array("AutoChat" => array('Gap' => false, 'Sticker' => false, 'Voice' => false, 'Gif' => false));
public array $Data = array("AutoCall" => false, "AutoFor" => false, "AutoSend" => false, "AutoSendNumber" => false, "AutoJoin" => false, "AutoDelBanGap" => false, "AutoComment" => false);

public function __sleep(): array
{
return ['Member', 'Answering', 'Admins', 'Comment', 'AutoFor', 'AutoSend', 'AutoChat', 'Data'];
}

public function Seter(string $Index, bool $Bet): void
{
$this->Data[$Index] = $Bet;
}

public function SetSend(string $Type, string $Index, bool $Bet): void
{
$this->$Type['Type'][$Index] = $Bet;
}

public function SetChat(mixed $Index, bool $Bet): void
{
$this->AutoChat['AutoChat'][$Index] = $Bet;
}

public function genLoop()
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-For "]=-=-=-=-=-=-=-=-=-=-=-=
if ($this->Data['AutoFor'] and !empty($this->AutoFor['Msg-ID'])) {
$this->broadcastForwardMessages(
from_peer: $this->AutoFor['Chat-ID'], message_ids: [$this->AutoFor['Msg-ID']],
filter: new Filter(allowUsers: $this->AutoFor['Type']['Users'], allowBots: false, allowGroups: $this->AutoFor['Type']['Groups'], allowChannels: $this->AutoFor['Type']['Channels']));
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-Send "]=-=-=-=-=-=-=-=-=-=-=-=
return $this->AutoFor['Time'];
}

public function onStart(): void
{
$genLoop = new GenericLoop([$this, 'genLoop'], 'update Status');
$genLoop->start();
}

public function getReportPeers(): array
{
return [self::ADMIN];
}

#[Handler]
public function HandleMessage(Incoming&PrivateMessage $Message): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text      = $Message->message;
$MsgID     = $Message->id ?? 0;
$UserID    = $Message->senderId;
$replyTo   = $Message->replyToMsgId;
$ChatID    = $Message->chatId;
$Answering = $this->Answering;
$Admin     = "1144186664"; // !!! Change this to your ID !!
$Admins    = $this->Admins;
$Helper    = '@'; // !!! Change this to your UserName-Helper With @ !!
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN) use ($Message) {
$this->Message($Message, 'reply', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Start-Orders "]=-=-=-=-=-=-=-=-=-=-=-=
try {
if ($UserID == $Admin or $Admin == isset($Admins['Admins'][$UserID])) {
//=-=-=-=-=-=-=-=-=-=-=-=[" Switch-All "]=-=-=-=-=-=-=-=-=-=-=-=
switch (true) {
//=-=-=-=-=-=-=-=-=-=-=-=[" Ping "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Ping' or $text == 'ربات' or $text == '.' or $text == '+':
$ReplyMessage("جونم قربان درخدمتم ؟!");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Restart "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Res' or $text == 'Restart' or $text == 'ریس' or $text == 'ریستارت':
$ReplyMessage("✠ - ربات باموفقیت مجدد راه اندازی شد !");
$this->restart();
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Memoryues "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'مصرف':
$ReplyMessage('Memory Usage : ' . round(memory_get_peak_usage(true) / 1021 / 1024, 2) . ' MB');
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Panel "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match('/^(Panel|Help|راهنما|پنل)$/i', $text):
$Res = $this->GetInline($ChatID, $Helper, "helpbot_");
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][0]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(addadmin) (.*)$/i", $text, $addadmin):
if (!isset($Admins['Admins'][$addadmin[2]])) {
$this->Admins['Admins'][$addadmin[2]] = $addadmin[2];
$ReplyMessage("✅ - کاربر `$addadmin[2]` باموفقیت به لیست ادمین ها اضافه شد !");
} else
$ReplyMessage("❌ - کاربر `$addadmin[2]` از قبل در لیست ادمین ها وجود داشت !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(deladmin) (.*)$/i", $text, $Del):
if (isset($Admins['Admins'][$Del[2]])) {
unset($this->Admins['Admins'][$Del[2]]);
$ReplyMessage("✅ - کاربر `$Del[2]` باموفقیت از لیست ادمین ها حذف شد !");
} else
$ReplyMessage("💢 - کاربر `$Del[2]` در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'CleanAdmin':
$this->Admins['Admins'] = [];
$ReplyMessage("👈 - تمامی ادمین ها باموفقیت عزل شدند !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" List-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Adminlist":
if (count($Admins['Admins']) > 0) {
$Text = "✅ - لیست ادمین های ربات به شرح زیر می باشد  : \n";
$counter = 1;
foreach ($Admins['Admins'] as $k) {
$Text .= "$counter : `$k` \n";
$counter++;
}
$ReplyMessage("$Text");
} else
$ReplyMessage("🙂 - ادمینی در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Satas "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match('/^(stats|آمار|امار)$/i', $text):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Res = $this->GetInline($ChatID, $Helper, "statson_");
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][0]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Info-Accaunt "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'info' or $text == '/info':
$Me = $this->getSelf();
$MeId = $Me['id'];
$Name = $Me['first_name'];
$Phone = '+' . $Me['phone'];
$ReplyMessage("
🔄 مشخصات ربات ساخته شده به شرح زیر میباشد :

✠ - سازنده : [$Admin](tg://user?id=$Admin)
✠ - نام اکانت : `$Name`
✠ - آیدی عددی : `$MeId`
✠ - شماره اکانت : `$Phone`", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" sessions "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'sessions':
$res = '';
foreach ($this->account->getAuthorizations()['authorizations'] as $authorization) {
$res .= "✅ - نشست های فعلی اکانت :

✠ - مدل دستگاه : `{$authorization['device_model']}`
✠ - سیستم عامل : `{$authorization['platform']}`
✠ - ورژن سیستم : `{$authorization['system_version']}`
✠ - آیپی آیدی : `{$authorization['api_id']}`
✠ - نام برنامه : `{$authorization['app_name']}`
✠ - ورژن برنامه  :`{$authorization['app_version']}`
✠ - تاریخ ورود : " . date("Y-m-d H:i:s", $authorization['date_active']) . "
✠ - آیپی : `{$authorization['ip']}`
✠ - کشور : `{$authorization['country']}`
\n=•=•=•=•=•=•=•=\n";
}
$ReplyMessage($res);
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Acc "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-User "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(setuser) (.*)$/i", $text, $username):
try {
$this->account->updateUsername(username: $username[2]);
} catch (Exception $exception) {
$ReplyMessage("❗ : " . $exception->getMessage());
}
$ReplyMessage("♻️ - آیدی <code> $username[2] </code> باموفقیت تنظیم شد !", ParseMode::HTML);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Name . . . "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/prof'):
$ip = trim(str_replace("/prof ", "", $text));
$ip = explode("|", $ip . "|||||");
$Name = !empty(trim($ip[0])) ? trim($ip[0]) : "تنظیم نشد !";
$LastName = !empty(trim($ip[1])) ? trim($ip[1]) : "تنظیم نشد !";
$About = !empty(trim($ip[2])) ? trim($ip[2]) : "تنظیم نشد !";
$this->account->updateProfile(first_name: $ip[0], last_name: $ip[1], about: $ip[2]);
$ReplyMessage("✠ - نام جدید : `$Name`
✠ - نام خانوادگی جدید : `$LastName`
✠ - بیوگرافی جدید : `$About`
✅ - باموفقیت تنظیم شد !", ParseMode::MARKDOWN);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Prof "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/delprof'):
$DelProf = substr($text, 9);
$this->photos->deletePhotos(id: $this->photos->getUserPhotos(user_id: 'me', limit: (int)$DelProf)['photos']);
$ReplyMessage("✅ - تعداد `$DelProf` عکس از پروفایل باموفقیت حذف شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Story "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(story) (.*)$/i", $text, $Caption):
$Media = $Message->getReply(PrivateMessage::class)?->media;
if (!$Media) {
$ReplyMessage("📌 - لطفا روی ویدیو/عکس مورد نظر ریپلای کنید :");
return;
}
$this->stories->sendStory(
peer: 'me',
media: $Media,
caption: "$Caption[2]",
parse_mode: ParseMode::MARKDOWN,
privacy_rules: [['_' => 'inputPrivacyValueAllowAll']]
);
$ReplyMessage("✅ - استوری مورد نظر باموفقیت تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Story "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(delstory) (.*)$/i", $text, $Count):
$this->stories->deleteStories(id: [$Count[2]]);
$ReplyMessage("❌ - استوری مورد نظر باموفقیت حذف شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Contact-List "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/contactlist' or $text == 'لیست مخاطبین':
$Text = ' ';
$contacts = $this->contacts->getContacts()['users'];
if ($contacts == null) {
$ReplyMessage("🖤 - لیست مخاطبین خالی میباشد . . . !");
return;
}
foreach ($contacts as $contact) {
$last_name = $contact['last_name'] ?? 'خالی';
$phone = $contact['phone'] ?? 'خالی';
$Text .= "✠ - اسم : `{$contact['first_name']}`
✠ - فامیل : `$last_name`
✠ - شماره : `$phone`
✠ - ایدی عددی : `{$contact['id']}`

\n=•=•=•=•=•\n";
}
$ReplyMessage($Text);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Delete-Contact "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/delcontact':
$array = [];
foreach ($this->contacts->getContacts()['contacts'] as $contact) {
$array[] = $contact['user_id'];
}
$this->contacts->deleteContacts(id: $array);
$ReplyMessage("✅ - تمامی مخاطبین باموفقیت حذف شدند !");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Add "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-All "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/addall'):
$user = substr($text, 8);
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'supergroup') {
try {
$this->channels->inviteToChannel(channel: $peer, users: ["$user"]);
} catch (Throwable $exception) { }
}
}
$ReplyMessage("✅ - کاربر `$user` باموفقیت در تمام گروه ها ادد شد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Pvs "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/addpvs'):
$ID = substr($text, 8);
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
foreach ($this->getDialogIds() as $peer) {
$Type = $this->getInfo($peer);
if ($Type['type'] == 'user') {
$Pv = $Type['user_id'];
try {
$this->channels->inviteToChannel(channel: $ID, users: [$Pv]);
} catch (Throwable $exception) { }
}
}
$ReplyMessage("✅ - تمامی کاربران باموفقیت در گروه `$ID` ادد شدند . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Contacts "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(addcontacts) (.*)$/i", $text, $ID):
if ($this->contacts->getContactIDs()['users'] == null) {
$ReplyMessage("🖤 - لیست مخاطبین خالی میباشد . . . !");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
try {
$this->channels->inviteToChannel(channel: $ID[2], users: $this->contacts->getContactIDs());
} catch (Throwable $exception) {
$ReplyMessage("❗ : " . $exception->getMessage());
}
$ReplyMessage("👥 - تمامی مخاطبین باموفقیت در گروه `$ID[2]` ادد شدند . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(exports) (.*)$/i", $text, $ID):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $ID[2], offset: $offset, limit: 100);
if (empty($Chat['users'])) break;
foreach ($Chat['users'] as $MemberGap) {
if (!$MemberGap['bot'] and isset($MemberGap['username']) and isset($MemberGap['first_name']) and !$MemberGap['deleted'] and !in_array($MemberGap['id'], $this->Member['list'])) {
$this->Member['list'][] = $MemberGap['id'];
$i++;
}
}
if ($i > 1000) break;
$offset += 100;
}
$ReplyMessage("⚙️ تعداد $i ممبر باموفقیت استخراج شد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(addexports) (.*)$/i", $text, $ID):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
try {
$this->channels->inviteToChannel(channel: $ID[2], users: $this->Member['list']);
} catch (Throwable $exception) {
$ReplyMessage("❗ : " . $exception->getMessage());
}
$ReplyMessage("⚙️ - تمامی اعضای استخراج شده باموفقیت در گروه `$ID[2]` ادد شدند . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/delmembers':
$this->Member['list'] = [];
$ReplyMessage("💢 - اعضای استخراج شده باموفقیت حذف شدند !");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Join "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" Join "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(join) (.*)$/i", $text, $ID) :
try {
$this->channels->joinChannel(channel: $ID[2]);
$ReplyMessage("✠ - جوین شدم !");
} catch (Exception $exception) {
$ReplyMessage("❗ : " . $exception->getMessage());
}
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Left "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Left'):
$Left = substr($text, 5);
$ReplyMessage("✠ - لف دادم !");
$this->sendMessage($Left, "✠ - لف بای  . . . !");
$this->channels->leaveChannel(channel: $Left);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Kill-Ban "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Killban':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'supergroup') {
try {
$Ban = $this->channels->getParticipant(channel: $peer, participant: 'me')['participant'];
if ($Ban['_'] == 'channelParticipantBanned') {
$this->channels->leaveChannel(channel: $peer);
$i++;
}
} catch (Throwable $exception) { }
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد گروه های مسدودی : `$i`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Channels "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Delchs':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'channel')
$this->channels->leaveChannel(channel: $peer);
$i++;
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد چنل های لفتی : `$i`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Gaps "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Delgaps':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'supergroup')
$this->channels->leaveChannel(channel: $peer);
$i++;
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد گروه های لفتی : `$i`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del2-Gaps "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Delgap'):
$Count = substr($text, 7);
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'supergroup')
$this->channels->leaveChannel(channel: $peer);
$i++;
if ($i == $Count)
break;
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد گروه های لفتی : `$i`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del2-Channels "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Delch'):
$Count = substr($text, 6);
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'channel') {
$this->channels->leaveChannel(channel: $peer);
$i++;
}
if ($i == $Count)
break;
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد چنل های لفتی : `$i`");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-For "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Bot "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Forall" or $text == "Forpv" or $text == "Forgaps":
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Value1 = '';
$Value2 = '';
$Value3 = '';
switch ($text) {
case 'Forall':
$Value1 = true;
$Value2 = false;
$Value3 = true;
break;
case 'Forpv':
$Value1 = true;
$Value2 = false;
$Value3 = false;
break;
case 'Forgaps':
$Value1 = false;
$Value2 = false;
$Value3 = true;
break;
}
$this->broadcastForwardMessages(
from_peer: $ChatID, message_ids: [$replyTo],
filter: new Filter(allowUsers: $Value1, allowBots: $Value2, allowGroups: $Value3, allowChannels: false));
$ReplyMessage("✠ - عملیات فوروارد پیام با موفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Forgap":
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'chat') {
try {
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $peer);
$this->sleep(rand(2, 5));
} catch (Throwable $exception) { }
}
}
$ReplyMessage("✠ - عملیات فوروارد پیام با موفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Forexports' or $text == 'فوروارداستخراج':
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 1;
while ($i < count($this->Member['list'])) {
foreach ($this->Member['list'] as $id) {
try {
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $id);
$this->sleep(rand(2, 5));
} catch (Throwable $exception) { }
++$i;
}
}
$ReplyMessage("✠ - فوروارد پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Pv-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(Forpvgap) (.*)$/i", $text, $IDGap):
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $IDGap[2], offset: $offset, limit: 100)['users'];
if (empty($Chat)) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['deleted'] and !$MemberGap['bot'] and isset($MemberGap['username']) and isset($MemberGap['first_name']) and $MemberGap['id'] != $this->getSelf()['id']) {
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $MemberGap['id']);
$this->sleep(rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - فوروارد پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Aut-For "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Sft'):
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$SetTime = substr($text, 4);
$Text = '';
$this->AutoFor['Msg-ID'] = $replyTo;
$this->AutoFor['Chat-ID'] = $ChatID;
$this->AutoFor['Time'] = $SetTime * 60;
if ($this->AutoFor['Type']['Users']) $Text = "پیوی ها";
elseif ($this->AutoFor['Type']['Groups']) $Text = "گروه ها";
elseif ($this->AutoFor['Type']['Channels']) $Text = "چنل ها";
$ReplyMessage("✅ - فوروارد زماندار باموفقیت در هر `$SetTime` دقیقه برای ارسال به `$Text` انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Aut-For "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Dft' or $text == 'حذف فوروارد':
unset($this->AutoFor['Msg-ID']);
$ReplyMessage("❌ - فوروارد زماندار باموفقیت حذف شد !");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Send "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" Send-Bot "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(sendall|sendpv|sendgaps) (.*)$/i", $text, $Send):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Value1 = '';
$Value2 = '';
$Value3 = '';
switch ($Send[1]) {
case 'sendall':
$Value1 = true;
$Value2 = false;
$Value3 = true;
break;
case 'sendpv':
$Value1 = true;
$Value2 = false;
$Value3 = false;
break;
case 'sendgaps':
$Value1 = false;
$Value2 = false;
$Value3 = true;
break;
}
$this->broadcastMessages(messages: [['message' => "$Send[2]"]],
filter: new Filter(allowUsers: $Value1, allowBots: $Value2, allowGroups: $Value3, allowChannels: false));
$ReplyMessage("✠ - عملیات ارسال پیام با موفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(sendgap) (.*)$/i", $text, $Send):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'chat') {
try {
$this->SendMessage($peer, $Send[2]);
$this->sleep(rand(1, 3));
} catch (Throwable $exception) { }
}
}
$ReplyMessage("✠ - عملیات ارسال پیام با موفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Send-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/sendexports'):
$Send = substr($text, 13);
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 1;
while ($i < count($this->Member['list'])) {
foreach ($this->Member['list'] as $id) {
try {
$this->SendMessage($id, $Send);
$this->sleep(rand(1, 3));
} catch (Throwable $exception) { }
++$i;
}
}
$ReplyMessage("✠ - ارسال پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Send-Pv-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(sendpvgap) (.*)$/i", $text, $Send):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $Send[2], offset: $offset, limit: 100)['users'];
if (empty($Chat)) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['deleted'] and !$MemberGap['bot'] and isset($MemberGap['username']) and isset($MemberGap['first_name']) and $MemberGap['id'] != $this->getSelf()['id']) {
$this->SendMessage($MemberGap['id'], $Send[3]);
$this->sleep(rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - ارسال پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'setanswer'):
$ip4 = trim(str_replace("/setanswer ", "", $text));
$ip3 = trim(str_replace("!setanswer ", "", $ip4));
$ip2 = trim(str_replace("#setanswer ", "", $ip3));
$ip1 = trim(str_replace("\setanswer ", "", $ip2));
$ip = trim(str_replace("setanswer ", "", $ip1));
$ip = explode("|", $ip . "|||||");
$Text = !empty(trim($ip[0])) ? trim($ip[0]) : "تنظیم نشد !";
$answer = !empty(trim($ip[1])) ? trim($ip[1]) : "تنظیم نشد !";
if (!isset($Answering[$Text])) {
$this->Answering[$Text] = $answer;
$ReplyMessage("
✠ - پیام جدید : `$Text`
✠ - پاسخ جدید : `$answer`
✅ - باموفقیت تنظیم شد !
");
}
else
$ReplyMessage("🖤 - این کلمه از قبل وجود داشت . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(delanswer) (.*?) (.*)$/i", $text, $Del):
if (isset($Answering[$Del[2]])) {
unset($this->Answering[$Del[2]]);
$ReplyMessage("❌ - پیام `$Del[2]` باموفقیت حذف شد !");
} else
$ReplyMessage("💢 -  پیام `$Del[2]` در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" List-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/answerlist":
if (count($Answering) > 0) {
$Text = "✅  :";
$counter = 1;
foreach ($Answering as $k => $ans) {
$Text .= "$counter : $k => $ans \n";
$counter++;
}
$ReplyMessage($Text);
} else
$ReplyMessage("🙂 - پاسخی وجود ندارد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/cleananswers':
unset($this->Answering);
$ReplyMessage("💢 - لیست پاسخ پاکسازی شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Comment "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/setcomment'):
$this->Comment = substr($text, 12);
$ReplyMessage("✅ - کامنت اول باموفقیت تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Time-Send "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/time'):
$TimeSend = substr($text, 6);
$this->AutoSend['Time'] = $TimeSend * 60;
$ReplyMessage("✅ - ارسال زماندار باموفقیت در هر `$TimeSend` دقیقه  تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Text-Send "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/sst'):
$Text = substr($text, 5);
$this->AutoSend['Text'] = $Text;
if ($this->AutoSend['Type']['Users']) $Text = "پیوی ها";
elseif ($this->AutoSend['Type']['Groups']) $Text = "گروه ها";
elseif ($this->AutoSend['Type']['Channels']) $Text = "چنل ها";
$ReplyMessage("✅ - متن ارسال زماندار باموفقیت  برای ارسال به `$Text` تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Text-Send "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/dst' or $text == 'حذف ارسال':
unset($this->AutoSend['Text']);
$ReplyMessage("❌ - ارسال زماندار باموفقیت حذف شد !");
break;
}
//=-=-=-=-=-=-=-=-=-=-=-=[" End-Ring-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Catch-Flood "]=-=-=-=-=-=-=-=-=-=-=-=
} catch (FloodError $exception) {
$i = ceil($exception->getWaitTime() / 60);
$this->SendMessage($Admin, "
💢 محدودیت ... ! ادمین عزیز ربات شما به مدت $i دقیقه خاموش شد !
👈 لطفا تا پایان محدودیت دستوری ارسال نکنید امکان دیلیت شدن اکانت شما هست !");
$exception->wait();
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Answering "]=-=-=-=-=-=-=-=-=-=-=-=
if (isset($Answering[$text]) and str_contains($text, $Answering[$text])) {
$this->sleep(2);
$this->messages->readHistory(peer: $UserID, max_id: $MsgID);
$this->sleep(2);
$this->messages->setTyping(action: ['_' => 'sendMessageTypingAction'], peer: $ChatID);
$ReplyMessage($Answering[$text]);
}
//=-=-=//=-=-=//=-=-=//[" End-Handler-Private "]=-=-=//=-=-=//=-=-=//
}
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Call "]=-=-=//=-=-=//=-=-=//
#[Handler]
public function HandleCall(VoIP&Incoming $Call): void
{
if ($this->Data['AutoCall']) {
$Call->accept()->play(new RemoteUrl('https://icestreaming.rai.it/1.mp3'));
}
}
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Channel "]=-=-=//=-=-=//=-=-=//
#[Handler]
public function HandleChannel(Incoming&ChannelMessage $Message): void
{
if ($this->Data['AutoJoin'] and preg_match_all('/\S+(t.me)\S+/i', $Message->message, $Link)) {
foreach ($Link[0] as $LinkGap) {
try {
$ChatInvite = $this->messages->checkChatInvite(hash: $LinkGap);
if ($ChatInvite['_'] == 'chatInvite' and $ChatInvite['broadcast'] != '1' and $ChatInvite['participants_count'] >= 500) {
$this->channels->joinChannel(channel: $LinkGap);
}
} catch (Throwable $exception) { }
}
}
if ($this->Data['AutoComment'] and !empty($this->Comment)) {
try {
$Message->getDiscussion()?->reply(message: $this->Comment);
} catch (\danog\MadelineProto\RPCErrorException $exception) {
if ($exception->getMessage() == "CHAT_WRITE_FORBIDDEN") {
$this->channels->leaveChannel(channel: $Message->chatId);
}
}
}
}
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Group "]=-=-=//=-=-=//=-=-=//
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Group "]=-=-=//=-=-=//=-=-=//
#[Handler]
public function HandleGroup(Incoming&GroupMessage $Message): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text     = $Message->message;
$MsgID    = $Message->id ?? 0;
$ChatID   = $Message->chatId;
$Data     = $this->Data;
$AutoChat = $this->AutoChat;
$Dir      = 'Data';
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::TEXT) use ($Message) {
$this->Message($Message, 'reply', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-Del-Ban-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
if ($Data['AutoDelBanGap']) {
foreach ($this->getDialogIds() as $peer) {
$Ban = $this->channels->getParticipant(channel: $peer, participant: 'me')['participant'];
if ($Ban['_'] == 'channelParticipantBanned') {
$this->channels->leaveChannel(channel: $peer);
}
}
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-Send-Number "]=-=-=-=-=-=-=-=-=-=-=-=
if ($Data['AutoSendNumber'] and rand(0, 40000) == 1) {
$Me = $this->getSelf();
$Media = ['_' => 'inputMediaContact', 'phone_number' => $Me['phone'], 'first_name' => $Me['first_name']];
$this->SendMedia($ChatID, $Media, $MsgID, "شماره ام :");
$ReplyMessage("شمارمو سیو کن پیوی پیام بده 🥹");
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-Chat-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
if ($AutoChat['AutoChat']['Gap'] and rand(0, 500) == 1) {
$this->messages->setTyping(action: ['_' => 'sendMessageTypingAction'], peer: $ChatID);
$TextForSend = array('سلام کسی فیلم قشنگ داره پیوی معرفی کنه نگا کنم❤️🚶‍♀️', 'تلگرام خوب کسی داره بفرسته پیوی ممنون میشم🚶‍♀️', 'سلام خوبین 👩‍🦯', 'سلام چخبرا👩‍🦯', 'اووه چقد شلوغه اینجا😐', 'دلم گرفته کسی نیست بیاد پی یکم درد و دل کنیم 🙂💔', 'لطفا یه ابجی بیاد بحرفیم پسر نیاد دلم گرفته🙂💔', 'یه ابجی بیاد پی سریع کارش دارم . مرسی🚶‍♀️', 'کی اینجا گیمره؟!🙄🚶‍♀️', 'کسی بلده چجوری برای اکانتم تایید بزارم بیاد پی🥺🚶‍♀️', 'کسی هست از ربات سازی سر در بیاره ؟!🚶‍♀️', 'رفقا ریپ شدم هرکی دوست داشت پیوی یه نقطه بندازه در بیام🚶‍♀️❤️', 'باو پسر نیاد پیوی دیگه☹️🚶‍♀️', 'خوبین ؟!🚶‍♀️', 'یه ابجی پی مخام دردو دل کنم باهاش🙁💋', 'هعی خیلی دلم گرفته 🚶‍♀️', 'اگر پیوی جواب نمیدم گروه ریپ بزنید روم یا پیویم اسپم کنید چون تلگرامم پره چنله🚶‍♀️🖤', 'یه ابجی مهربون بیاد پی لطفا🚶‍♀️🙂', 'کسی پک استیکر داره بفرسته پی ؟!☹️', 'کسی اهنگ دپ داره بده پی حالم گرفتس🚶‍♀️🖤', 'کسی هست از تهران باشه ؟!', 'کسی هست از اصفهان باشه؟؟', 'کسی هست از مشهد باشه ؟!', 'کسی هست از نیشابور باشه؟؟', 'هعییییی');
$SendGap = $TextForSend[array_rand($TextForSend)];
try {
$this->SendMessage($ChatID, $SendGap);
} catch (\danog\MadelineProto\RPCErrorException $exception) {
if ($exception->getMessage() == "CHAT_WRITE_FORBIDDEN") {
$this->channels->leaveChannel(channel: $ChatID);
}
}
}
if ($AutoChat['AutoChat']['Gap'] and $Message->mentioned) {
$this->sleep(2);
$this->messages->setTyping(action: ['_' => 'sendMessageTypingAction'], peer: $ChatID);
$this->sleep(2);
$Mahdi_a_8 = array();
$Name      = $this->getSelf()['first_name'];
if ($text == 'س' or str_contains($text, "سلام") or $text == 'صلام' or $text == 'صلم' or $text == 'سل' or $text == 'سلم' or $text == 'salam' or $text == 'سلاپ' or $text == 'صل' or $text == 'های' or $text == 'سیلام') {
$Mahdi_a_8 = array("Text" => array('سلام خوبی؟', 'سلام', 'علیک', 'صلام', 'خوبی؟', 'سلامم', 'سلام خوبی', 'سلام عزیزم خوبی', 'سلام اصل', 'چطوری ؟', 'سلام عزیزم'), "Voice" => array('Hi', 'Hi2', 'Hi3', 'What', 'How2'));
}
elseif (str_contains($text, 'بای') or str_contains($text, 'خدافظ') or str_contains($text, 'فعلا')) {
$Mahdi_a_8 = array("Text" => array('خدافظی', 'بسلامت', 'بای بده', 'حل بای', 'خدافظ', 'فعلا', 'میتونی بری', 'متنی بری', 'فلن', 'خب خدافظ'), "Voice" => array('Bay', 'Bay2', 'Bay3'));
}
elseif (str_contains($text, 'خوبی') or str_contains($text, 'چطوری') or str_contains($text, 'خبی') or $text == 'khobi' or $text == 'خوبی؟' or str_contains($text, "خبی؟") or $text == 'khobi?' or $text == 'خوبم') {
$Mahdi_a_8 = array("Text" => array('تشکر', 'شما خوبی؟', 'ممنون', 'بخوبیت', 'مرسی تو خوبی؟', 'مرسی', 'شکر', 'میگذره', 'ممنون ت خوبی', 'خوبم ت خوبی', 'اوم بد نیستم ت چطوری', 'نه حالم بده یکم ت خوبی', 'آره بدک نیستم'), "Reaction" => array('❤️', '❤️‍🔥', '👍', '🙏', '😁'), "Voice" => array('How', 'What', 'How2', 'How3'));
}
elseif (str_contains($text, "مرسی") or $text == 'ممنون' or $text == 'تشکر' or $text == 'ممنان' or $text == 'سپاس' or $text == 'مر30' or $text == 'متشکر') {
$Mahdi_a_8 = array("Text" => array('کاری نکردم', 'فدات', 'قربونت', 'خواهش', 'خواهش میکنم'), "Reaction" => array('❤️', '❤️‍🔥', '🔥', '🙏', '🎉', '💋'), "Gif" => rand(1, 10));
}
elseif (str_contains($text, "چخبر") or str_contains($text, "چه خبر") or $text == 'چ خبر' or $text == 'چخبره' or $text == 'چخبر؟' or $text == 'چخبره؟' or $text == 'چ خبر؟') {
$Mahdi_a_8 = array("Text" => array('شماچخبر', 'سلامتیت', 'بی خبری', 'خبری نیس والا', 'ازتوچخبر', 'دسته تبر😐😂', 'هیچ سلامتی', 'سلامتی رهبر', 'برف اومده تا کمر', 'هیچ بدبختی', 'خبرای خوب', 'بی‌خبر ت چه خبر'), "Voice" => array('What', 'Bot2'));
}
elseif (str_contains($text, "و تو؟") or str_contains($text, "اصل") or $text == 'اصل؟' or $text == 'بیو' or $text == 'اصل بده' or $text == 'اصل میدی؟' or $text == 'اصل میدی') {
$Mahdi_a_8 = array("Text" => array(" 18 و شما ؟", "$Name هستم از ایران🥲", "اول شما ؟", "اول تو ؟", "پرنسس از آلمان  و شما ؟😂🥲", "اول شما اصل بده ؟", "$Name هستمممم🥲🥲🥲"), "Voice" => array('Asl', 'Bot2', 'Bot3', 'Bot4'));
}
elseif (str_contains($text, "ربات") or $text == 'ربات؟' or str_contains($text, "رباتی") or $text == 'رباتی؟' or $text == 'رباته' or $text == 'باتی' or $text == 'باتی؟') {
$Mahdi_a_8 = array("Text" => array("نه😐", "ربات نیستم 😐😐", "نه باو ربات چیه😐", "خیر ربات چیه ؟", "ربات چی ؟ ", "ن"), "Reaction" => array('😐', '👎', '🤯', '💔'), "Sticker" => rand(1, 24), "Voice" => array('Bot', 'Bot2', 'Bot3', 'Bot4'), "Gif" => rand(1, 10));
}
elseif (str_contains($text, "باشه") or $text == 'باش' or $text == 'باشه؟' or $text == 'باش؟' or $text == 'حله' or $text == 'اوکیه' or $text == 'حل' or $text == 'اوکی') {
$Mahdi_a_8 = array("Text" => array("جاشه - ", "حله - ", "خوبه -", "اوکیه -"), "Reaction" => array('👌', '👍', '❤️', '🤣'), "Sticker" => rand(1, 24), "Voice" => array('Yes', 'Yes2'), "Gif" => rand(1, 10));
}
elseif ($text == 'خش' or $text == 'خوش' or $text == 'خشبختم' or $text == 'خوش بختم' or $text == 'خش بختم' or $text == 'خوشبختم') {
$Mahdi_a_8 = array("Text" => array('قربونت', 'مرسی', 'ممنون', 'تشکر', 'فدات'), "Reaction" => array('🙏', '❤️', '❤️‍🔥', '😍', '💋', '😘'), "Sticker" => rand(1, 24), "Gif" => rand(1, 10));
}
elseif (str_contains($text, "چیه") or str_contains($text, "چیشد") or str_contains($text, "چشد")) {
$Mahdi_a_8 = array("Text" => array('هیچی', 'چیزی نیست', 'هیچ', 'هچ'), "Sticker" => rand(1, 24));
}
elseif (str_contains($text, "فدات") or $text == 'قربونت' or $text == 'فداتم') {
$Mahdi_a_8 = array("Text" => array('نشی', 'بوص', 'نشو', 'بوس', 'قربانت'), "Reaction" => array('🙏', '❤️', '❤️‍🔥', '😍', '💋', '😘'), "Sticker" => rand(1, 24), "Gif" => rand(1, 10));
}
elseif (str_contains($text, "ویس")) {
$Mahdi_a_8 = array("Text" => array('الان نمیتونم', 'باشه', 'نمیتونم', 'صدام خوب نیست', 'چی بگم ؟'), "Reaction" => array('😐', '👎', '🤯', '💔'), "Sticker" => rand(1, 24), "Voice" => array('Voice', 'Voice2', 'Voice3', 'Voice4'), "Gif" => rand(1, 10));
}
elseif (str_contains($text, '😐')) {
$Mahdi_a_8 = array("Text" => array('چرا پوکر ؟', 'چرا انقدر پوکری ؟', 'هوووف'), "Reaction" => array('😐', '👍', '🤨'), "Sticker" => rand(1, 24), "Gif" => rand(1, 10));
}
elseif (str_contains($text, '😂')) {
$Mahdi_a_8 = array("Text" => array('هعیی بخند', '😐😕.', 'بله - 😂'), "Reaction" => array('🤣', '👍', '🤨'), "Sticker" => rand(1, 24), "Gif" => rand(1, 10));
}
$Keys = array_keys($Mahdi_a_8);
if (!empty($Keys)) {
$Key_Rand = $Keys[array_rand($Keys)];
if ($Key_Rand == "Text") {
try {
$ReplyMessage($Mahdi_a_8['Text'][array_rand($Mahdi_a_8['Text'])]);
} catch (\danog\MadelineProto\RPCErrorException $exception) {
if ($exception->getMessage() == "CHAT_WRITE_FORBIDDEN") {
$this->channels->leaveChannel(channel: $ChatID);
}
}
}
elseif ($Key_Rand == "Reaction") {
$Message->addReaction($Mahdi_a_8['Reaction'][array_rand($Mahdi_a_8['Reaction'])]);
}
elseif ($AutoChat['AutoChat']['Sticker'] and $Key_Rand == "Sticker") {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/Sticker/{$Mahdi_a_8['Sticker']}.tgs"];
$this->SendMedia($ChatID, $Media, $MsgID, "");
}
elseif ($AutoChat['AutoChat']['Voice'] and isset($Keys['Voice'])) {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/{$Mahdi_a_8['Voice'][array_rand($Mahdi_a_8['Voice'])]}.ogg"];
$this->SendMedia($ChatID, $Media, $MsgID, "");
}
elseif ($AutoChat['AutoChat']['Gif'] and $Key_Rand = "Gif") {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/Gif/{$Mahdi_a_8['Gif']}.gif"];
$this->SendMedia($ChatID, $Media, $MsgID, "");
}
}
}
//=-=-=//=-=-=//=-=-=//[" End-Handler-Group "]=-=-=//=-=-=//=-=-=//
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" Functions "]=-=-=//=-=-=//=-=-=//=-=-=//
protected function Message(object $Message, string $type, string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): void
{
$Message->$type(message: $text, parseMode: $parseMode);
}
//=-=-=-=-=-=-=-=-=-=-=-=[" SendMedia "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SendMedia($ChatID, array $Media, int $Message_id, ?string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): array
{
return $this->messages->sendMedia(
peer: $ChatID,
reply_to_msg_id: $Message_id,
media: $Media,
message: $text,
parse_mode: $parseMode
);
}
//=-=-=-=-=-=-=-=-=-=-=-=[" GetInline "]=-=-=-=-=-=-=-=-=-=-=-=
protected function GetInline($ChatID, string $Bot, ?string $Query = null, ?string $Offset = '0'): array
{
return $this->messages->getInlineBotResults(
bot: $Bot,
peer: $ChatID,
query: $Query,
offset: $Offset
);
}
//=-=-=-=-=-=-=-=-=-=-=-=[" SendInline "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SendInline($ChatID, int $Message_id, int $Query_id, string $Query_res_id, ?bool $Silent = true, ?bool $Background = false, ?bool $Clear_draft = true): void
{
$this->messages->sendInlineBotResult(
silent: $Silent,
background: $Background,
clear_draft: $Clear_draft,
peer: $ChatID,
reply_to_msg_id: $Message_id,
query_id: $Query_id,
id: $Query_res_id
);
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" End-Class "]=-=-=//=-=-=//=-=-=//=-=-=//
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" Settings "]=-=-=//=-=-=//=-=-=//=-=-=//
$Settings = new Settings;
$Settings->getPeer()
->setFullFetch(FALSE)
->setCacheAllPeersOnStartup(FALSE);
$Settings->getAppInfo()
->setApiId() // !!! Change this to your Api_id Account !!
->setApiHash("") // !!! Change this to your Api_hash Account !!
->setDeviceModel("Samsung Galaxy S23")
->setSystemVersion("plus messenger")
->setAppVersion("10.2.9.0");
$Settings->getLogger()->setLevel(Logger::LEVEL_ULTRA_VERBOSE);
MyEventHandler::startAndLoop('Tabchi', $Settings);
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Latest Beta-v188
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * open : sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" End "]=-=-=-=-=-=-=-=-=-=-=-=